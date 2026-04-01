<template>
  <div>
      <div class="card">
          <div class="card-header">
              Welcome to ASL
          </div>
          <div class="card-body">
              <div>
                  <p>ABC Company</p>
                  <small>Xyzzy</small>
              </div>
          </div>
      </div>
  </div>
</template>

<script>
export default {
  middleware: 'auth',

  metaInfo () {
    return { title: this.$t('home') }
  }
}
</script>
