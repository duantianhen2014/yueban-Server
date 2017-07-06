<?php

namespace App\Api;


use Auth;
use App\Http\Controllers\Controller;
use App\User;
use Dingo\Api\Routing\Helpers;

class UsersController extends Controller
{

    use Helpers;

    public function __construct()
    {
        $this->middleware('jwt.auth', ['only' => ['me']]);
    }

    public function show($user){

        return User::findOrFail($user);
    }


    public function me(){

        $user = Auth::user();

        return $user;

    }


}