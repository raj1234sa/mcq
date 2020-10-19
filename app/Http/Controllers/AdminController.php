<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    public function loginView(Request $request)
    {
        $backurl = DIR_HTTP_HOME.'dashboard';
        if($request->input('backurl') != null) {
            $backurl = $request->input('backurl');
        }
        $data = array(
            'backurl' => $backurl,
        );
        return view('login', $data);
    }

    public function loginAuth(Request $request)
    {
        Session::remove('admin_login');
        $username = $request->input('username');
        $password = $request->input('password');
        $admin = Admin::where('username', '=', $username)
            ->where('password', '=', $password)->get();
        if (isset($admin[0])) {
            Session::put('admin_login', $admin[0]);
            return redirect($request->input('backurl'));
        } else {
            return redirect('/')->with('fail', 'Username or Password is incorrect');
        }
    }

    public function logout()
    {
        Session::remove('admin_login');
        return redirect('/');
    }
}
