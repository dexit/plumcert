<?php

namespace App\Http\Controllers;

use App\Models\Finding;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('public.home', [
            'featuredFindings' => Finding::whereIn('status', ['featured', 'approved'])->latest()->limit(3)->get(),
        ]);
    }
}
