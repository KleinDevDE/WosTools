<?php

namespace App\Http\Controllers\Users;

use App\Models\User;
use Illuminate\View\View;

class UserController
{
    public function list(): View
    {
        return view('users.list');
    }
}
