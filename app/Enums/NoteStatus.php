<?php
namespace App\Enums;

abstract class NoteStatus
{
    const PENDING = 0;
    const CLOSED = 1;
    const OPEN = 2;

    const UNREAD = 1;
    const READ = 0;
}
