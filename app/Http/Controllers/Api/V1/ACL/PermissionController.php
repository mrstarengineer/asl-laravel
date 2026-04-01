<?php

namespace App\Http\Controllers\Api\V1\ACL;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        return Permission::all();
    }

    public function names()
    {
        return Permission::all()->pluck('name')->toArray();
    }
}
