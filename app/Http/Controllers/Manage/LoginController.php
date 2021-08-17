<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index(Request $request) {


        return view('manage/login', []);



    }

    public function auth(Request $request) {


        $ret = Auth::attempt(
            [
                'userId' => $request->post('userId'),
                'password' => $request->post('password'),
                'type' => 1
            ],
            false
        );

        if ($ret) {
            return redirect()->route('manageDisp');
        } else {
            return redirect()->route('manageLogin');
        }


    }


    public function disp() {
        dump('zz');
        dump(Auth::user());
    }



}
