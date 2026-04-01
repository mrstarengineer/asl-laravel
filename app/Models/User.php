<?php

namespace App\Models;

use App\Jobs\SyncSingleUserPermissionsJob;
use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable  implements JWTSubject
{
    use HasRoles, HasFactory, Notifiable;

    protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'status',
        'inactive_at',
        'password',
        'role',
        'photo_url',
        'locations',
        'customers',
        'authentication_required',
        'device_id_token',
    ];

    protected $appends = [
        'photo',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
//        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at'        => 'date: Y-m-d H:i:s',
        'updated_at'        => 'date: Y-m-d H:i:s',
        'locations'         => 'array',
        'customers'         => 'array',
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * @return int
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims ()
    {
        return [];
    }

    public function getPhotoAttribute ()
    {
        return ! empty( $this->photo_url ) ? Storage::url($this->photo_url) : asset( 'images/default_pro_pic.png' );
    }

    public function customer ()
    {
        return $this->hasOne( Customer::class, 'user_id' );
    }

    protected static function boot ()
    {
        parent::boot();
        User::updating( function ( $model ) {
            if( $model->getOriginal( 'role' ) != $model->role ) {
                $model->authentication_required = 1;
            }
        } );
    }
}
