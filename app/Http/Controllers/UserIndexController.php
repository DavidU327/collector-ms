<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class UserIndexController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::orderBy('id', 'DESC')->paginate(10));
    }
}
