<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Message;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        $user = $this->getOrCreateUser($request->get('email'));
        $this->saveLoginDate($user);

        auth()->loginUsingId($user->id);
        $this->cacheInitMessageOfUser();
        return $this->sendLoginResponse($request);
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return $this->loggedOut($request) ?: redirect('/');
    }

    private function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
        ]);
    }

    private function getOrCreateUser(string $email): User
    {
        return User::firstOrCreate(['email' => $email], ['last_login_date' => Date::now()]);
    }

    private function saveLoginDate(User $user): void
    {
        $user->update(['last_login_date' => Date::now()]);
    }

    private function cacheInitMessageOfUser(): void
    {
        $initMessage = Message::orderBy('id', 'DESC')->first();
        $id = $initMessage ? $initMessage->id : 0;
        session()->put('init_message_id', $id);
    }
}
