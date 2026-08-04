@extends('layouts.app')

@section('title', 'For Landlords')

@section('content')
<div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-5xl font-bold mb-4">Gas Safety Compliance Made Easy</h1>
        <p class="text-xl text-blue-100">Annual certifications required by UK law. We handle it.</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white shadow rounded-lg p-8 mb-8">
        <h2 class="text-3xl font-bold mb-6">Landlord Legal Requirements</h2>
        <p class="text-gray-700 mb-6">
            All rental properties in the UK must have a valid Gas Safety Certificate issued within the last 12 months. Failure to comply can result in fines up to £6,000 per property.
        </p>
        <div class="grid grid-cols-2 gap-6">
            <div class="bg-blue-50 p-4 rounded border-l-4 border-blue-600">
                <h3 class="font-bold mb-2">What We Check</h3>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>✓ Gas appliances</li>
                    <li>✓ Flues & ventilation</li>
                    <li>✓ Safety controls</li>
                    <li>✓ Pipe integrity</li>
                </ul>
            </div>
            <div class="bg-green-50 p-4 rounded border-l-4 border-green-600">
                <h3 class="font-bold mb-2">Your Obligations</h3>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>✓ Annual inspection</li>
                    <li>✓ Issue tenant copies</li>
                    <li>✓ Keep records 5 years</li>
                    <li>✓ Report to HSE if unsafe</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-8 mb-8">
        <h2 class="text-2xl font-bold mb-4">Why Choose Plumcert for Your Portfolio?</h2>
        <ul class="space-y-3 text-gray-700">
            <li>✓ Gas Safe registered engineers</li>
            <li>✓ Multi-property bookings welcome</li>
            <li>✓ Flexible scheduling</li>
            <li>✓ Digital reports & compliance tracking</li>
            <li>✓ Competitive portfolio rates</li>
        </ul>
    </div>

    <div class="bg-yellow-50 border border-yellow-200 p-8 rounded-lg mb-8">
        <h3 class="text-xl font-bold mb-4">Book Your Annual Certificates</h3>
        <p class="text-gray-700 mb-6">Don't risk penalties. Schedule your properties today — we'll handle the rest.</p>
        <a href="{{ route('book') }}" class="inline-block px-6 py-3 bg-blue-600 text-white font-bold rounded hover:bg-blue-700">Book Now</a>
    </div>
</div>
@endsection
