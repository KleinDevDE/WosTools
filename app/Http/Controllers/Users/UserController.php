<?php

namespace App\Http\Controllers\Users;

use App\Models\User;
use Illuminate\View\View;

class UserController
{
    public function show(User $user): View
    {
        //TODO Do we need a check if user exists?
        return view('users.show', ['user' => $user]);
    }

    public function list(): View
    {
        return view('users.list');
    }
}
