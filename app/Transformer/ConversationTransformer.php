<?php

namespace App\Transformer;

use App\Enums\Roles;

class ConversationTransformer extends \Nahid\Presento\Transformer
{
    public function getClassProperty ( $value )
    {
        $class = "you";

        if ( ( optional( auth()->user() )->role == Roles::CUSTOMER && $value == Roles::CUSTOMER ) || ( optional( auth()->user() )->role != Roles::CUSTOMER && $value != Roles::CUSTOMER ) ) {
            $class = "me";
        }

        return $class;
    }

}

