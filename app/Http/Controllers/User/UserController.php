<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\IUser;

class UserController extends Controller
{
    protected $users;

    public function __construct(IUser $users)  // import user contract
    {
        $this->users = $users;
    }

    public function index()
    {
        $users = $this->users->all(); // implemnet user repository
        return UserResource::collection($users);
    }
}
