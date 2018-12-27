<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Response;

class UserController extends Controller
{
    public function userInformation($userId='') {
        if (is_numeric($userId)) {
            $user = User::find($userId);
            return Response::json($user);
        }
    }
}
