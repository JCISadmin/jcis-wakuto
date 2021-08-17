<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index(Request $request) {


        $ret = Auth::attempt([
            'userId' => 'admin',
            'password' => '0000',
            'type' => 1
        ]);

        dump($ret);

        return 'aaa';
    }

}
