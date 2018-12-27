<?php
route::get('login','AuthController@index')->name('login');
route::post('login','AuthController@login');
route::get('login/{provider}','AuthController@redirectToProvider');
route::get('login/{provider}/callback','AuthController@callBackToProvider');
route::post('register','AuthController@register');
route::post('activation-send-again','AuthController@activationCodeSendAgain');
route::post('activation','AuthController@activation');
route::post('forgot','AuthController@forgotPassword');
route::post('forgot-password-update','AuthController@forgotPasswordUpdate');

route::group(['prefix'=>'profile'],function(){
   route::get('/','ProfileController@index')->middleware('auth');
   route::post('new-inga/upload-image','ProfileController@newIngaUploadImage')->middleware('auth');
   route::get('new-inga/detect-video','ProfileController@ingaVideoDetect')->middleware('auth');
   route::post('new-inga','ProfileController@newIngaSave')->middleware('auth');
   route::get('my-profile-ingas','ProfileController@getIngas')->middleware('auth');
   route::get('my-profile-new-inga-count/{lastId}','ProfileController@getNewIngaCount')->middleware('auth');
   route::get('my-profile-last-inga','ProfileController@myLastInga')->middleware('auth');

   // settings
   route::get('get-general','ProfileController@general')->middleware('auth');
   route::get('get-locations','ProfileController@locations')->middleware('auth');
   route::post('location-delete','ProfileController@locationDelete')->middleware('auth');
   route::post('general-save','ProfileController@generalSave')->middleware('auth');
   route::post('privacy-save','ProfileController@privacySave')->middleware('auth');
   route::post('location-add','ProfileController@locationSave')->middleware('auth');
   route::get('logout','ProfileController@logout');
});

route::group(['prefix'=>'inga'],function(){
    route::get('get-my-action/{id}','IngaController@getMyActionThisInga')->middleware('auth');
    route::get('detail/{id}','IngaController@ingaDetail')->middleware('auth');
    route::post('like','IngaController@like')->middleware('auth');
    route::post('rate','IngaController@rateIt')->middleware('auth');
    route::get('user-information/{userId}','UserController@userInformation')->middleware();
});

//route::get('profile','AuthController@register')->middleware('auth');
