<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        $data = array(
            'route' => '/dashboard',
        );
        return view('dashboard', $data);
    }
}
