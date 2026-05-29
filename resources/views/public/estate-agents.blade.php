@extends('layouts.app')

@section('title', 'For Estate Agents')

@section('content')
<div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-5xl font-bold mb-4">Partner with Plumcert</h1>
        <p class="text-xl text-blue-100">Fast gas safety certificates for your listings.</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white shadow rounded-lg p-8 mb-8">
        <h2 class="text-3xl font-bold mb-6">Why Estate Agents Choose Plumcert</h2>
        <ul class="space-y-4 text-gray-700">
            <li class="flex items-start gap-3">
                <span class="text-xl font-bold text-blue-600">✓</span>
                <div>
                    <h3 class="font-bold">48-72 Hour Turnaround</h3>
                    <p class="text-sm text-gray-600">Fast reporting to help close sales faster.</p>
                </div>
            </li>
            <li class="flex items-start gap-3">
                <span class="text-xl font-bold text-blue-600">✓</span>
                <div>
                    <h3 class="font-bold">Nationwide Coverage</h3>
                    <p class="text-sm text-gray-600">Available across the UK for all your listings.</p>
                </div>
            </li>
            <li class="flex items-start gap-3">
                <span class="text-xl font-bold text-blue-600">✓</span>
                <div>
                    <h3 class="font-bold">Professional Reports</h3>
                    <p class="text-sm text-gray-600">Digital delivery, fully compliant with UK standards.</p>
                </div>
            </li>
            <li class="flex items-start gap-3">
                <span class="text-xl font-bold text-blue-600">✓</span>
                <div>
                    <h3 class="font-bold">Bulk Discounts Available</h3>
                    <p class="text-sm text-gray-600">Special rates for high-volume partners.</p>
                </div>
            </li>
        </ul>
    </div>

    <div class="bg-blue-50 border-l-4 border-blue-600 p-8">
        <h3 class="text-xl font-bold mb-4">Get Started</h3>
        <p class="text-gray-700 mb-6">Contact us today to discuss partnership rates and streamlined ordering.</p>
        <a href="{{ route('contact') }}" class="inline-block px-6 py-3 bg-blue-600 text-white font-bold rounded hover:bg-blue-700">Contact Sales</a>
    </div>
</div>
@endsection
