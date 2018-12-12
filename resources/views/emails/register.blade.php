@extends('layouts.mail')

@section('content')
    <div style="font-size:14px;line-height:20px;color:#737373;text-align:center;">
        <p>Hoşgeldin;</p>
        <p>Üyeliğini aktifleştirmek ve <strong>Zedinga</strong> dünyasını keşfetmek için aşağıdaki aktivasyon kodunuzu doğrulayınız.</p>
        <p>
            Aktivasyon Kodunuz<br/>
            <b style="display:inline-block;color:#000;margin-top:40px;text-decoration:none;font-weight:bold;display:block;font-size:60px;line-height:60px;">
            {{ $user->remember_token }}
            </a>
        </p>
    </div>
@endsection
