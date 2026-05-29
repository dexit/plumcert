<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ToolsController extends Controller
{
    public function index(): View
    {
        return view('public.tools');
    }
}
