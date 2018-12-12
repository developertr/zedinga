<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\UserCounter;
use Mail;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct() {

    }

    public function index() {
        Auth::logout();
        return view('login');
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|alpha_dash|max:30',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return Response::json(['success'=>false,'message'=>$validator->errors()]);
        }

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password, 'activated' => 1])) {
            return Response::json(['success'=>true]);
        } else {
            return Response::json(['success'=>false,'errors'=>['Lütfen bilgilerinizi kontrol ediniz!']]);
        }
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
           'email' => 'required|string|email|max:255|unique:users',
           'username' => 'required|string|alpha_dash|unique:users|max:30',
           'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return Response::json(['success'=>false,'message'=>$validator->errors()]);
        }

        $user = new User();
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->setRememberToken($token = rand(1000,9999));
        $user->save();

        $user_counter = new UserCounter();
        $user_counter->user_id = $user->id;
        $user_counter->save();

        $data = ['token'=>$token, 'user'=>$user];
        Mail::send('emails.register', $data, function($message) use ($user) {
            $message->to($user->email, 'Yeni Üye')
                ->subject('Zedinga Üyelik');
            $message->from(env('MAIL_FROM'),'Zedinga');
        });

        return Response::json(['success'=>true]);
    }

    public function activationCodeSendAgain(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255'
        ]);
        if ($validator->fails()) {
            return Response::json(['success'=>false,'message'=>$validator->errors()]);
        } else {
            $user = User::where('email',$request->email)->first();
            if (isset($user)) {
                if($user->activated==1) {
                    return Response::json(['success'=>false,'message'=>['error'=>['Bu üyenin aktivasyonu daha önce gerçekleştirilmiş']]]);
                }
                $user->setRememberToken($token = rand(1000,9999));
                $user->save();
                $data = ['token'=>$token, 'user'=>$user];
                Mail::send('emails.register', $data, function($message) use ($user) {
                    $message->to($user->email, 'Yeni Üye')
                        ->subject('Zedinga Üyelik');
                    $message->from(env('MAIL_FROM'),'Zedinga');
                });
                return Response::json(['success'=>true]);
            } else {
                return Response::json(['success'=>false,'message'=>['error'=>['Böyle bir üye bulunamadı']]]);
            }
        }
    }

    public function activation(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'code' => 'required|numeric|digits:4'
        ]);
        if ($validator->fails()) {
            return Response::json(['success'=>false,'message'=>$validator->errors()]);
        }

        $user = User::where('email', $request->email)->where('remember_token',$request->code)->first();
        if (isset($user)) {
            $user->activated=1;
            $user->save();
            Auth::login($user, true);
            return Response::json(['success'=>true]);
        } else {
            return Response::json(['success'=>false,'message'=>['error'=>['Lütfen bilgilerinizi kontrol ediniz']]]);
        }
    }
}
