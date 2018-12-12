<?php
route::get('login','AuthController@index')->name('login');
route::post('login','AuthController@login');
route::post('register','AuthController@register');
route::post('activation-send-again','AuthController@activationCodeSendAgain');
route::post('activation','AuthController@activation');

//route::get('profile','AuthController@register')->middleware('auth');
