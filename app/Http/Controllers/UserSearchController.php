<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Requests\UserSearchRequest;

class UserSearchController extends Controller
{
    public function search(UserSearchRequest $userSearchRequest)
    {
        $users = User::search($userSearchRequest->search)
            ->query(function ($query) {
                $query->whereNull('deleted_at');
            })
            ->paginate(10);
        return UserResource::collection($users);
    }
}
