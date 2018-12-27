@extends('layouts.mail')

@section('content')
    <div style="font-size:14px;line-height:20px;color:#737373;text-align:center;">
        <p>Merhaba;</p>
        <p>Websitesinden şifrenizi sıfırlama talebinde bulundunuz. <strong>Zedinga</strong> dünyasına geri dönmek için lütfen doğrulama kodunu websitesindeki alana yazınız.</p>
        <p>
            Doğrulama Kodunuz<br/>
            <b style="display:inline-block;color:#000;margin-top:40px;text-decoration:none;font-weight:bold;display:block;font-size:60px;line-height:60px;">
            {{ $user->remember_token }}
            </a>
        </p>
    </div>
@endsection
