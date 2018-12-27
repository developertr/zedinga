@extends('layouts.master')

@section('content')
    <div class="profilePage">
        <div class="wallpaper"></div>
        <div class="myDashboardLeft">
            <div class="profileBox">
                <user-info :user="{{ Auth::user() }}" :counters="{{ Auth::user()->counters()->first() }}" class="userInfo"></user-info>
                <div class="profileNavs">
                    <a href="/">
                        <img src="{{ asset('svg/homepage.svg') }}" alt="">
                        Anasayfa
                    </a>
                    <router-link to="/">
                        <img src="{{ asset('svg/profile.svg') }}" alt="">
                        Duvarım
                    </router-link>
                    <a href="">
                        <img src="{{ asset('svg/messages.svg') }}" alt="">
                        <span class="badge badge-pill badge-light">3</span>
                        Mesajlarım
                    </a>
                    <a href="">
                        <img src="{{ asset('svg/notifications.svg') }}" alt="">
                        <span class="badge badge-pill badge-light">8</span>
                        Bildirimler
                    </a>
                    <router-link to="/settings">
                        <img src="{{ asset('svg/settings.svg') }}" alt="">
                        Ayarlar
                    </router-link>
                    <a href="/profile/logout">
                        <img src="{{ asset('svg/logout.svg') }}" alt="">
                        Çıkış Yap
                    </a>
                </div>
            </div>
        </div>
        <div class="myDashboardRight">
            <keep-alive>
                {{--<div name="sidebar" class="sideBar">asd</div>--}}
                <router-view></router-view>
            </keep-alive>
        </div>
        <div class="clearfix"></div>
    </div>
@endsection
