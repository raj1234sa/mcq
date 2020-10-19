<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $data = array(
            'route' => '/subject-list',
        );
        return view('subjects.index', $data);
    }
}
