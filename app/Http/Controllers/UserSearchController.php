<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Requests\UserSearchRequest;

class UserSearchController extends Controller
{
    public function search(UserSearchRequest $userSearchRequest)
    {
        $user = User::search($userSearchRequest->search)->paginate(10);
        return UserResource::collection($user);
    }
}
