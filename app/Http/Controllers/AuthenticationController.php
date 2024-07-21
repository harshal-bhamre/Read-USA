<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use App\Helpers\Helper;

class AuthenticationController extends Controller
{
  // Login v1
  public function login_v1()
  {
    $pageConfigs = ['blankPage' => true];
    return view('/content/authentication/auth-login-v1', ['pageConfigs' => $pageConfigs]);
  }


  // public function login(Request $request){
  //   $user = $request->only('email','password');
  //   if(Auth::attempt($user)){
  //     return  redirect('/');
  //   }else{
  //   return back();
  //   }  
  // }

  public function login(Request $request)
  {
    // dd($request->all());
    if (Auth::attempt(["email" => $request["email"], "password" => $request["password"]])) {
     
    }
  }


  // Register v1
  public function register_v1()
  {
    $pageConfigs = ['blankPage' => true];

    return view('/content/authentication/auth-register-v1', ['pageConfigs' => $pageConfigs]);
  }


  // Forgot Password v1
  public function forgot_password_v1()
  {
    $pageConfigs = ['blankPage' => true];

    return view('/content/authentication/auth-forgot-password-v1', ['pageConfigs' => $pageConfigs]);
  }

  public function forgotPassword(Request $request)
  {
    // dd($request);
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
      $request->only('email')
    );
    return $status === Password::RESET_LINK_SENT
      ? back()->with(['status' => __($status)])
      : back()->withErrors(['email' => __($status)]);
  }


  // Reset Password
  public function reset_password_v1()
  {
    $pageConfigs = ['blankPage' => true];

    return view('/content/authentication/auth-reset-password-v1', ['pageConfigs' => $pageConfigs]);
  }

  public function resetPassword(Request $request)
  {
  }
}
