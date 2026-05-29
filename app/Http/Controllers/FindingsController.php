<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class FindingsController extends Controller
{
    public function index(): View
    {
        return view('public.findings', ['findings' => []]);
    }

    public function show($finding): View
    {
        return view('public.finding-show', ['finding' => $finding]);
    }
}
