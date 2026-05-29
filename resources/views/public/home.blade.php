@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- Hero -->
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-5xl font-bold mb-4">UK Gas Safety Certificates</h1>
        <p class="text-xl mb-8 text-blue-100">Fast, certified gas safety inspections & legal compliance for homeowners & landlords.</p>
        <a href="{{ route('book') }}" class="inline-block px-8 py-3 bg-yellow-400 text-gray-900 font-bold rounded hover:bg-yellow-300">Get a Quote Now</a>
    </div>
</section>

<!-- Services Grid -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h2 class="text-3xl font-bold mb-8 text-center">Our Services</h2>
    <div class="grid grid-cols-3 gap-6">
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="font-bold text-lg mb-2">Gas Safety Certificate</h3>
            <p class="text-gray-600 text-sm">Annual inspection & certification by Gas Safe engineers.</p>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="font-bold text-lg mb-2">Boiler Service</h3>
            <p class="text-gray-600 text-sm">Maintenance & safety checks to extend boiler life.</p>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="font-bold text-lg mb-2">Boiler Installation</h3>
            <p class="text-gray-600 text-sm">New boiler fitting & commissioning by certified installers.</p>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="font-bold text-lg mb-2">EICR</h3>
            <p class="text-gray-600 text-sm">Electrical Installation Condition Report for rental properties.</p>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="font-bold text-lg mb-2">EPC</h3>
            <p class="text-gray-600 text-sm">Energy Performance Certificate for property sales/lettings.</p>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="font-bold text-lg mb-2">Free Tools</h3>
            <p class="text-gray-600 text-sm">Gas rate, pipe sizing, heat loss, and volume calculators.</p>
        </div>
    </div>
</section>

<!-- Featured Findings -->
@if ($featuredFindings->count() > 0)
<section class="bg-gray-100 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold mb-8 text-center">Featured Findings</h2>
        <div class="grid grid-cols-3 gap-6">
            @foreach ($featuredFindings as $finding)
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="bg-gray-300 h-40"></div>
                    <div class="p-6">
                        <h3 class="font-bold mb-2">{{ $finding->title }}</h3>
                        <p class="text-gray-600 text-sm">{{ Str::limit($finding->description, 100) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA Banner -->
<section class="bg-blue-700 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold mb-4">Schedule Your Inspection Today</h2>
        <p class="mb-6 text-blue-100">Compliant, certified, quick turnaround.</p>
        <a href="{{ route('book') }}" class="inline-block px-8 py-3 bg-yellow-400 text-gray-900 font-bold rounded hover:bg-yellow-300">Book Now</a>
    </div>
</section>
@endsection
