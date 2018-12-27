<?php

namespace App\Http\Controllers;

use App\UserPrivacy;
use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\UserCounter;
use App\UserSocial;
use Mail;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct() {

    }

    public function redirectToProvider($provider)
    {
        return \Socialite::driver($provider)->redirect();
    }

    public function callBackToProvider($provider)
    {
        $user = \Socialite::driver($provider)->user();
        $add_user_counter = false;
        if($provider=='twitter') {
            $user_social_record = UserSocial::where('social_id',$user->id)->where('social_name',$provider)->first();
            if(isset($user_social_record)) {
                $user_record = User::where('id',$user_social_record->user_id)->first();
                $username = $user_record->username;
                $email = $user_record->email;
                $activated = $user_record->activated;
                $profile_image = $user_record->profile_image;
            } else {
                $user_social_record = new UserSocial();
                $user_record = new User();
                $add_user_counter = true;

                $zedinga_user = User::where('username',$user->nickname)->first();

                if(isset($zedinga_user)) {
                    $username = 'tw'.$user->id;
                } else {
                    $username = $user->nickname;
                }

                $zedinga_email_user = User::where('email',$user->email)->first();
                if(isset($zedinga_email_user)) {
                    $email = 'tw'.$user->id.'@zedinga.com';
                    $activated = 0;
                } else {
                    $email = $user->email;
                    $activated = 1;
                }

                $user_social_record->profile_image_url = $user->avatar_original;
                $user_social_record->token = $user->token;
                $user_social_record->token_secret = $user->tokenSecret;
            }

            $location = explode(',',$user->user['location']);

            $user_record->username = $username;
            $user_record->email = $email;
            $user_record->name = $user->name;
            $user_record->activated = $activated;
            $user_record->description = $user->user['description'];
            if(count($location)==2) {
                $user_record->latitude = $location[0];
                $user_record->longitude = $location[1];
            }

            if(!isset($profile_image)) {
                $user_record->profile_image = $user->avatar_original;
            }

            $user_record->save();

            if($add_user_counter) {
                $user_counter = new UserCounter();
                $user_counter->user_id = $user_record->id;
                $user_counter->save();

                $user_privacy = new UserPrivacy();
                $user_privacy->user_id = $user_record->id;
                $user_privacy->save();
            }

            $user_social_record->user_id = $user_record->id;
            $user_social_record->social_name = 'twitter';
            $user_social_record->nick_name = $user->nickname;
            $user_social_record->social_id = $user->id;
            $user_social_record->followers_count = $user->user['followers_count'];
            $user_social_record->friends_count = $user->user['friends_count'];
            $user_social_record->save();

            Auth::login($user_record, true);
            return redirect('profile');
        } elseif(($provider=="facebook") || ($provider=="instagram")) {
            $user_social_record = UserSocial::where('social_id',$user->id)->where('social_name',$provider)->first();
            if(isset($user_social_record)) {
                $user_record = User::where('id',$user_social_record->user_id)->first();
                $username = $user_record->username;
                $email = $user_record->email;
                $activated = $user_record->activated;
            } else {
                $user_social_record = new UserSocial();
                $user_record = new User();
                $add_user_counter = true;

                if($user->nickname<>null) {
                    $zedinga_user = User::where('username',$user->nickname)->first();

                    if(isset($zedinga_user)) {
                        $username = 'tw'.$user->id;
                    } else {
                        $username = $user->nickname;
                    }
                } else {
                    $username = 'fb'.$user->id;
                }

                $zedinga_email_user = User::where('email',$user->email)->first();
                if(isset($zedinga_email_user)) {
                    $email = 'fb'.$user->id.'@zedinga.com';
                    $activated = 0;
                } else {
                    $email = $user->email;
                    $activated = 1;
                }

                if($provider=='facebook') {
                    $user_social_record->profile_image_url = $user->avatar_original;
                } else {
                    $user_social_record->profile_image_url = $user->avatar;
                }
                $user_social_record->token = $user->token;
                $user_social_record->token_secret = $user->refreshToken;
            }

            $user_record->username = $username;
            $user_record->email = $email;
            $user_record->name = $user->name;
            $user_record->activated = $activated;
            if($provider=='facebook') {
                $user_record->profile_image = $user->avatar_original;
            } else {
                $user_record->profile_image = $user->avatar;
            }
            $user_record->save();

            if($add_user_counter) {
                $user_counter = new UserCounter();
                $user_counter->user_id = $user_record->id;
                $user_counter->save();

                $user_privacy = new UserPrivacy();
                $user_privacy->user_id = $user_record->id;
                $user_privacy->save();
            }

            $user_social_record->user_id = $user_record->id;
            $user_social_record->social_name = $provider;
            $user_social_record->nick_name = $user->nickname;
            $user_social_record->social_id = $user->id;
            $user_social_record->save();

            Auth::login($user_record, true);
            return redirect('profile');
        }
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
            return Response::json(['success'=>false,'message'=>['error'=>['Lütfen bilgilerinizi kontrol ediniz!']]]);
        }
    }

    public function forgotPassword(Request $request) {
        $validate = 1;
        $validatorusername = Validator::make($request->all(), [
            'username' => 'required|string|alpha_dash|max:30'
        ]);
        if ($validatorusername->fails()) {
            $validatoremail = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255'
            ]);
            if ($validatoremail->fails()) {
                $validate = 0;
            }
        }

        if ($validate==0) {
            return Response::json(['success'=>false,'message'=>['error'=>['Email veya Kullanıcı adını giriniz']]]);
        }
        if (!$validatorusername->fails()) {
            $user = User::where('username',$request->username)->first();
        } else {
            if (!$validatoremail->fails()) {
                $user = User::where('email',$request->email)->first();
            }
        }

        if ($user==null) {
            return Response::json(['success'=>false,'message'=>['error'=>['Bu Kullanıcı adı veya email adresi ile kayıtlı kullanıcı bulunamadı.']]]);
        }

        $user->setRememberToken($token = rand(100000,999999));
        $user->save();

        $data = ['token'=>$token, 'user'=>$user];
        Mail::send('emails.forgot', $data, function($message) use ($user) {
            $message->to($user->email, 'Zedinga')
                ->subject('Zedinga Şifre Sıfırlama');
            $message->from(env('MAIL_FROM'),'Zedinga');
        });

        return Response::json(['success'=>true,'userid'=>$user->id]);
    }

    public function forgotPasswordUpdate(Request $request) {
        $validator = Validator::make($request->all(), [
            'userid' => 'required|numeric',
            'code' => 'required|numeric|digits:6',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return Response::json(['success'=>false,'message'=>$validator->errors()]);
        }

        $user = User::where('id', $request->userid)->where('remember_token',$request->code)->first();
        if (isset($user)) {
            $user->password = bcrypt($request->password);
            $user->save();
            Auth::login($user, true);
            return Response::json(['success'=>true]);
        } else {
            return Response::json(['success'=>false,'message'=>['error'=>['Lütfen doğrulama kodunuzu kontrol ediniz.']]]);
        }
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
           'email' => 'required|string|email|max:255|unique:users',
           'username' => 'required|string|alpha_dash|unique:users|max:30|min:6',
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

        $user_privacy = new UserPrivacy();
        $user_privacy->user_id = $user->id;
        $user_privacy->save();

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
