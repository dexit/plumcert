<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    private $services = [
        'gas-safety-cert' => ['name' => 'Gas Safety Certificate', 'description' => 'UK Gas Safe registered inspection and certification.'],
        'boiler-service' => ['name' => 'Boiler Service', 'description' => 'Annual boiler maintenance and safety check.'],
        'boiler-install' => ['name' => 'Boiler Installation', 'description' => 'New boiler installation by certified installers.'],
        'eicr' => ['name' => 'Electrical Installation Condition Report (EICR)', 'description' => 'Electrical safety inspection and testing.'],
        'epc' => ['name' => 'Energy Performance Certificate (EPC)', 'description' => 'Energy efficiency assessment for properties.'],
    ];

    public function about(): View
    {
        return view('public.about');
    }

    public function services(): View
    {
        return view('public.services', ['services' => $this->services]);
    }

    public function service($slug): View
    {
        $service = $this->services[$slug] ?? null;
        abort_if(!$service, 404);

        return view('public.service-detail', [
            'slug' => $slug,
            'service' => $service
        ]);
    }

    public function estateAgents(): View
    {
        return view('public.estate-agents');
    }

    public function landlords(): View
    {
        return view('public.landlords');
    }

    public function privacy(): View
    {
        return view('public.privacy');
    }

    public function terms(): View
    {
        return view('public.terms');
    }
}
