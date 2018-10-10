<?php

namespace App\Http\Controllers;

use App\User;
use Tymon\JWTAuth\JWTAuth;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Retrieve the user for the given ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return User::where('id',$id)->first();
    }

    public function listAllUsers(){
        return User::all();
    }
}
