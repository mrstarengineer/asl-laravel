<?php

namespace App\Services\Notification;


use App\Enums\FCMNotificationType;
use App\Models\User;
use App\Services\BaseService;
use Kutia\Larafirebase\Facades\Larafirebase;

class FirebaseService extends BaseService
{
    public function sendAnnouncement ()
    {
        return Larafirebase::withTitle( 'Test Title' )
            ->withBody( 'Test body' )
            ->withIcon( env( 'APP_URL' ) . '/images/logo.jpg' )
            ->withClickAction( 'notifications' )
            ->withPriority( 'high' )
            ->withAdditionalData( [
                'id' => 42,
            ] )
            ->sendNotification( $this->deviceTokens );
    }

    public function sendVehicleCreatedNotification ()
    {
        return Larafirebase::withTitle( 'New Vehicle Created' )
            ->withBody( 'Testing message body' )
            ->withIcon( env( 'APP_URL' ) . '/images/logo.jpg' )
            ->withClickAction( FCMNotificationType::VEHICLE )
            ->withPriority( 'high' )
            ->withAdditionalData( [
                'id' => 64144,
            ] )
            ->sendNotification( \App\Models\User::where( 'status', \App\Enums\VisibilityStatus::ACTIVE )->whereNotNull( 'device_id_token' )->pluck( 'device_id_token' )->toArray() );
    }
}
