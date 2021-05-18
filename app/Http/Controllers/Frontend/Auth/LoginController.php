<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Laravel\Socialite\Facades\Socialite;

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
    protected $redirectTo = '/dashbord';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    public function showLoginForm()
    {
        return view('frontend.auth.login');
    }

    public function username()
    {
        return 'username';
    }
    protected function authenticated(Request $request, $user)
    {
        if($user->status == 1){
            if ($request->wantsJson()){
                $token = $user->createToken('access_token')->accessToken;
                return  response()->Json([
                    'errose'  => false,
                    'message'  => 'Your loging Successfully . ',
                    'token'    => $token,

                ]);
            }
            return redirect()->route('frontend.dashboard')->with([
                'message'  => 'Your loging Successfully . ',
                'alert-type' => 'success'
               ]);
        }
        if ($request->wantsJson()){
            return  response()->Json([
                'errose'  => false,
                'message'  => 'plasase contact bloggi admin   . ',

            ]);
        }
        return redirect()->route('frontend.index')->with([
            'message'  => 'plasase contact bloggi admin   . ',
            'alert-type' => 'warning'
           ]);

    }


    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }


    public function handleProviderCallback($provider)
    {
        $scoialuser = Socialite::driver($provider)->user();

       // dd ($provider,$scoialuser);

        $token = $scoialuser->token;


        $id=$scoialuser->getId();
        $name=$scoialuser->getName();
       $email= $scoialuser->getEmail();
        $avater= $scoialuser->getAvatar();


        $user = User::firstOrCreate([
           'email'  => $email
        ],[
            'name'               => $name,
            'username'           => trim(str::lower(str::replaceArray(' ',['_'],$name))),
            'email'              => $email,
            'email_verified_at'  => Carbon::now(),
            'mobile'             => $id,
            'status'             => 1,
            'receive_email'     => 1,
            'remember_token'     => $token,
            'password'           => Hash::make($email),
        ]);

       if($user->user_image == ''){
           $filename = '' . $user->username . '.jpg';
           $path = public_path('/assets/users/'.$filename);
           Image::make($avater)->save($path,100);
           $user->update(['user_image' => $filename]);
            }
        $user->attachRole(Role::whereName('user')->first()->id);

        Auth::login($user,true);
        return redirect()->route('frontend.dashboard')->with([
            'message'  => 'loggedin  Successfully. ',
            'alert-type' => 'success'
        ]);
    }














}



