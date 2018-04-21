<?php

namespace SecTheater\Jarvis\Routes;

use Illuminate\Contracts\Routing\Registrar as Router;

class RouteRegistrar
{
    /**
     * The router implementation.
     *
     * @var \Illuminate\Contracts\Routing\Registrar
     */
    protected $router;

    /**
     * Create a new route registrar instance.
     *
     * @param \Illuminate\Contracts\Routing\Registrar $router
     *
     * @return void
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Register routes for transient tokens, clients, and personal access tokens.
     *
     * @return void
     */
    public function all()
    {
        $this->forAuthorization();
        $this->forGuests();
    }

    /**
     * Register the routes needed for authorization.
     *
     * @return void
     */
    public function forAuthorization()
    {
        $this->router->group(['middleware' => 'web'], function ($router) {
            $router->post('/logout', [
                'uses' => "Auth\LoginController@logout",
                'as'   => 'logout',
            ]);
            $router->post('/change-password', [
                'uses' => 'Auth\ChangePasswordController@postChangePassword',
                'as'   => 'change-password',
            ]);
            $router->get('/change-password', [
                'uses' => 'Auth\ChangePasswordController@getChangePassword',
                'as'   => 'change-password',
            ]);
        });
    }

    public function forGuests()
    {
        $this->router->group(['middleware' => 'guest', 'namespace' => 'Auth'], function ($router) {
            $router->get('/login', [
                'uses' => 'LoginController@getLogin',
                 'as'  => 'login',
            ]);
            $router->post('/login', [
                'uses' => 'LoginController@postLogin',
                'as'   => 'login',
            ]);
            $router->get('/register', [
                'uses' => 'RegisterController@getRegister',
                 'as'  => 'register',
            ]);
            $router->post('/register', [
                'uses' => 'RegisterController@postRegister',
                'as'   => 'register',
            ]);
            $router->get('/forgot-password', [
                'uses' => 'ForgotPasswordController@getForgotPassword',
                'as'   => 'forgot-password',
            ]);
            $router->post('/forgot-password', [
                'uses' => 'ForgotPasswordController@postForgotPassword',
                'as'   => 'forgot-password',
            ]);
            $router->get('/reset-password/{email}/{token}', [
                'uses' => 'ResetPasswordController@getPasswordResetThroughEmail',
                 'as'  => 'reset-password',
            ]);
            $router->post('/reset-password', [
                'uses' => 'ResetPasswordController@postPasswordResetThroughEmail',
                'as'   => 'reset-password',
            ]);
            $router->get('/resetBySecurityQuestion', [
                'uses' => 'ResetPasswordController@getPasswordResetThroughQuestion',
                'as'   => 'reset-security',
            ]);
            $router->post('/resetBySecurityQuestion/stage1', [
                'uses' => 'ResetPasswordController@postPasswordResetThroughQuestion1',
                'as'   => 'reset-security-1',
            ]);
            $router->post('/resetBySecurityQuestion/stage2', [
                'uses' => 'ResetPasswordController@postPasswordResetThroughQuestion2',
                'as'   => 'reset-security-2',
            ]);
            $router->post('/resetBySecurityQuestion/stage3', [
                'uses' => 'ResetPasswordController@postPasswordResetThroughQuestion3',
                'as'   => 'reset-security-3',
            ]);
        });
    }
}
