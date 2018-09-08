<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\User;
use Auth;
use Session;

class AksesController extends Controller
{
  public function login()
  {
    return view('logincustom');
  }


public function loginAction(Request $request)
  {
      $cek = User::where('username', $request->input('username'))->
                    where('password', $request->input('password'))->first();

      if(!empty($cek)){
        Auth::loginUsingId($cek->id_user);
        return Redirect::to('/dashboard');
      }
      else{
        return Redirect::to('/');
      }
  }

    public function logout()
    {
        Auth::logout();
        return Redirect::to('/');
        // return view('login');
    }

}
