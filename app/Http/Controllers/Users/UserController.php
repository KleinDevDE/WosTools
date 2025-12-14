<?php

namespace App\Http\Controllers\Users;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

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

    public function table(Request $request): JsonResponse {
        $users = User::query();

        return DataTables::of($users)
            ->minSearchLength(2)
            ->startsWithSearch(false)
            ->makeHidden(['password', 'remember_token', 'email'])
            ->toJson();
    }
}
