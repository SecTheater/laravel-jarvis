<?php
Route::group(['middleware' => 'guest', 'namespace' => 'Auth'], function () {
		Route::get('/login', 'LoginController@getLogin')->name('login');
		Route::post('/login', 'LoginController@postLogin')->name('login');
		Route::get('/register', 'RegisterController@getRegister')->name('register');
		Route::post('/register', 'RegisterController@postRegister')->name('register');
		Route::get('/forgot-password', 'ForgotPasswordController@getForgotPassword')->name('forgot-password');
		Route::post('/forgot-password', 'ForgotPasswordController@postForgotPassword')->name('forgot-password');
		Route::get('/reset/{email}/{token}', 'ResetPasswordController@getPasswordResetThroughEmail')->name('reset-password');
		Route::post('/reset-password', 'ResetPasswordController@postPasswordResetThroughEmail')->name('reset-password');
		Route::get('/resetBySecurityQuestion', ['uses'         => 'ResetPasswordController@getPasswordResetThroughQuestion', 'as'         => 'reset.security']);
		Route::post('/resetBySecurityQuestion/stage1', ['uses' => 'ResetPasswordController@postPasswordResetThroughQuestion1', 'as' => 'reset.security1']);
		Route::post('/resetBySecurityQuestion/stage2', ['uses' => 'ResetPasswordController@postPasswordResetThroughQuestion2', 'as' => 'reset.security2']);
		Route::post('/resetBySecurityQuestion/stage3', ['uses' => 'ResetPasswordController@postPasswordResetThroughQuestion3', 'as' => 'reset.security3']);

	});
Route::group(['middleware'       => 'Jarvis', 'roles'       => '*.*.*'], function () {
		Route::post('/logout', ['uses' => "Auth\LoginController@logout", 'as' => 'logout']);
		Route::post('/change-password', [
				'uses' => 'Auth\ChangePasswordController@postChangePassword',
				'as'   => 'change-password'
			]);
		Route::get('/change-password', [
				'uses' => 'Auth\ChangePasswordController@getChangePassword',
				'as'   => 'change-password'
			]);

	});
