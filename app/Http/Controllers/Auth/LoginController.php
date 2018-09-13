<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;
use Socialite;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $provider_user = Socialite::driver('facebook')->user();
        $user = User::where('email', $provider_user->email)
                    ->where('facebook_id', $provider_user->id)
                    ->first();

        if (!$user) {
            // search for existing user with provider email
            $user = User::where('email', $provider_user->email)->first();

            if (!$user) {
                $user = new User;
                $user->name = $provider_user->name;
                $user->username = $provider_user->id;
                $user->email = $provider_user->email;
                // set default password to fb_id
                $user->password = bcrypt($provider_user->id);
                $user->is_admin = false;
            }
            
            $user->facebook_id = $provider_user->id;
            $user->save();
        }

        auth()->login($user);
        return redirect('/');
    }
}
