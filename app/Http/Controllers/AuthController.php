<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;

class AuthController extends Controller
{
    public function index() {
        return view('login');
    }

    public function register(Request $request) {
        $validator = $request->validate([
           'email' => 'required|string|email|max:255|unique:users',
           'username' => 'required|string|alpha_dash|unique:users|max:30',
           'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return Response::json(['success'=>false,'message'=>$validator->errors]);
        } else {

        }
    }
}
