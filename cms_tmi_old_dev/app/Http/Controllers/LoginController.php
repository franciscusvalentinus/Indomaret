<?php

namespace App\Http\Controllers;

use App\Models\TDBBranch;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.login');
    }


    public function getLoginWeb(Request $Request)
    {
        $email = $Request->get('email');
        $pass = $Request->get('password');

        $password = User::Where('email', $email)->pluck('password');
        $uid = User::Where('email', $email)->pluck('id');

        if (\Hash::check($pass, $password)) {
            //LOGIN OK
            //MAKE AUTH
            \Auth::loginUsingId($uid);

            if(\Auth::user('users')->role == 1){
                return redirect('/inputmember');
            }else if(\Auth::user('users')->role == 4){
                return redirect('/manageversion');
            }else{
                return redirect('/chartsales');
            }

        } else {
            return redirect('login')->with('err', 'Terjadi kesalahan login, Periksa Email dan Password Anda');
        }
    }

}
