#!/usr/bin/env bash
# Repo: https://github.com/mrstarengineer/asl-laravel
# Deploy the Laravel API that manage.aslshippingline.com calls.
# nginx api.conf serves it at https://connect.amayausedcars.com
# from /var/www/api/asl-laravel/public.
# Nothing is built locally: the server pulls main from GitHub.
#   ./deploy-live-aws.sh            Pull origin/main, composer install (asks), migrations (asks), clear caches, reload PHP-FPM
#   ./deploy-live-aws.sh rollback   Go back to the commit that was live before the last deploy
#   ./deploy-live-aws.sh status     Show the live commit and pending commits
#
# No secrets live here: the SSH key stays on your machine. Override the
# defaults with env vars, e.g. DEPLOY_SSH_KEY=~/.ssh/other.pem ./deploy-live-aws.sh
#
# Does NOT run `php artisan optimize`, `config:cache`, or `route:cache`.
# App code calls env() directly (AWS_S3_BASE_URL, APP_URL, APP_ENV_TYPE), and
# env() returns null once config is cached. routes/web.php also uses closures,
# so route:cache would fail.
#
# Does not change /var/www/manage or /var/www/system.
# The frontend is deployed with frontend/deploy-live-aws.sh.
#
# The old manual steps on this box were: git pull origin main, composer
# dump-autoload, then restart php8.1-fpm. composer install covers the autoload
# dump and also installs packages added in composer.lock. PHP-FPM is reloaded
# instead of restarted. Nginx is left running; it already points at public/.

set -euo pipefail

SSH_KEY="${DEPLOY_SSH_KEY:-$HOME/.ssh/gca_mumbai.pem}"
SERVER="${DEPLOY_SERVER:-ubuntu@ec2-35-154-180-7.ap-south-1.compute.amazonaws.com}"
APP_DIR="${DEPLOY_APP_DIR:-/var/www/api/asl-laravel}"
BRANCH="${DEPLOY_BRANCH:-main}"
HEALTH_URL="${DEPLOY_HEALTH_URL:-https://connect.amayausedcars.com}"
PHP_FPM="${DEPLOY_PHP_FPM:-php8.1-fpm}"

log() { printf '==> %s\n' "$*"; }
ok() { printf 'ok  %s\n' "$*"; }
err() { printf 'err %s\n' "$*" >&2; }

if [[ "$APP_DIR" != "/var/www/api/asl-laravel" ]]; then
	err "Refusing '$APP_DIR'."
	exit 1
fi

if [[ "$HEALTH_URL" != "https://connect.amayausedcars.com" ]]; then
	err "Refusing health URL '$HEALTH_URL'. api.conf serves https://connect.amayausedcars.com."
	exit 1
fi

remote() {
	ssh -i "$SSH_KEY" -o BatchMode=yes -o ConnectTimeout=15 "$SERVER" "$@"
}

# Run a bash script (read from stdin) inside APP_DIR on the server; extra args become $1, $2, ...
remote_script() {
	local args=""
	local a
	for a in "$@"; do args+=" '$a'"; done
	remote "cd '$APP_DIR' && bash -s --$args"
}

confirm() {
	local answer
	printf '%s [y/N] ' "$1"
	read -r answer
	[[ "$answer" == "y" || "$answer" == "Y" ]]
}

preflight() {
	[[ -f "$SSH_KEY" ]] || { err "SSH key not found: $SSH_KEY"; exit 1; }
	remote true || { err "Cannot SSH to $SERVER"; exit 1; }
}

# optional composer install, optional migrations, cache clear, PHP-FPM reload, health check.
finish_deploy() {
	if confirm "Run composer install on the server?"; then
		log "composer install..."
		remote "cd '$APP_DIR' && composer install --no-interaction --prefer-dist --optimize-autoloader"
	else
		log "Skipped composer install. vendor/ on the server is left as it is."
	fi

	local pending
	pending="$(remote "cd '$APP_DIR' && php artisan migrate:status --no-ansi | grep -c Pending || true")"
	if [[ "$pending" != "0" ]]; then
		remote "cd '$APP_DIR' && php artisan migrate:status --no-ansi | grep Pending"
		if confirm "Run $pending pending migration(s) on the LIVE database?"; then
			remote "cd '$APP_DIR' && php artisan migrate --force"
		else
			log "Skipped migrations. Run them later with: php artisan migrate --force"
		fi
	else
		log "No pending migrations."
	fi

	log "Clearing caches and reloading PHP..."
	remote_script "$PHP_FPM" <<'EOF'
set -euo pipefail
php artisan optimize:clear
php artisan view:cache
sudo systemctl reload "$1"
EOF

	local code
	code="$(curl -s -o /dev/null -w '%{http_code}' --max-time 20 "$HEALTH_URL" || true)"
	if [[ "$code" =~ ^[23] ]]; then
		ok "$HEALTH_URL responded $code"
	else
		err "$HEALTH_URL responded '$code'. Check storage/logs/laravel.log, or run: ./deploy-live-aws.sh rollback"
		exit 1
	fi
}

deploy() {
	preflight
	remote_script "$BRANCH" <<'EOF'
set -euo pipefail
# Line changes block the deploy. A mode-only change (bootstrap/cache/.gitignore
# is 755 on this server) prints 0 0 and does not.
if git diff --numstat | awk '$1 != 0 || $2 != 0 { found = 1 } END { exit !found }'; then
	echo "err The server has uncommitted changes to tracked files:" >&2
	git diff --numstat | awk '$1 != 0 || $2 != 0' >&2
	exit 1
fi
git fetch --quiet origin "$1"
echo "Live:     $(git log -1 --format='%h %s (%cr)')"
echo "Incoming: $(git log -1 --format='%h %s (%cr)' "origin/$1")"
echo "New commits:"
git log --oneline "HEAD..origin/$1" | sed 's/^/  /'
EOF

	if [[ "$(remote "cd '$APP_DIR' && git rev-list --count HEAD..origin/$BRANCH")" == "0" ]]; then
		ok "Already up to date with origin/$BRANCH. Nothing to deploy."
		exit 0
	fi
	confirm "Deploy these commits to the LIVE API?" || exit 1

	log "Pulling origin/$BRANCH..."
	remote_script "$BRANCH" <<'EOF'
set -euo pipefail
git rev-parse HEAD > .git/DEPLOY_PREVIOUS
git merge --ff-only "origin/$1"
EOF
	finish_deploy
	printf '\nDeployment is done.\n'
	printf '%s is live.\n' "$HEALTH_URL"
}

rollback() {
	preflight
	local target
	target="$(remote "cd '$APP_DIR' && cat .git/DEPLOY_PREVIOUS 2>/dev/null" || true)"
	[[ -n "$target" ]] || { err "No previous deploy recorded on the server"; exit 1; }
	remote "cd '$APP_DIR' && echo \"Live:        \$(git log -1 --format='%h %s')\" && echo \"Rollback to: \$(git log -1 --format='%h %s' $target)\""
	echo "Migrations are NOT rolled back. If the deploy ran any, check them before continuing."
	confirm "Roll the LIVE API back to this commit?" || exit 1
	remote_script "$target" <<'EOF'
set -euo pipefail
if git diff --numstat | awk '$1 != 0 || $2 != 0 { found = 1 } END { exit !found }'; then
	echo "err The server has uncommitted changes to tracked files; refusing git reset" >&2
	git diff --numstat | awk '$1 != 0 || $2 != 0' >&2
	exit 1
fi
git rev-parse HEAD > .git/DEPLOY_PREVIOUS
git reset --hard "$1"
EOF
	finish_deploy
	ok "Rolled back. Running rollback again returns to the commit you just left."
}

status() {
	preflight
	remote_script "$BRANCH" <<'EOF'
git fetch --quiet origin "$1"
echo "Live:    $(git log -1 --format='%h %s (%cr)')"
echo "Pending: $(git rev-list --count "HEAD..origin/$1") commit(s) on origin/$1"
df -h / | tail -1
EOF
}

case "${1:-deploy}" in
	deploy) deploy ;;
	rollback) rollback ;;
	status) status ;;
	-h|--help|help) sed -n '2,25p' "$0" ;;
	*) err "Unknown command: $1"; exit 1 ;;
esac
