@extends('layouts.app')

@section('title', $service['name'])

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <a href="{{ route('services') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">&larr; Back to Services</a>

    <h1 class="text-4xl font-bold mb-6">{{ $service['name'] }}</h1>

    <div class="bg-white shadow rounded-lg p-8 mb-8">
        <h2 class="text-2xl font-bold mb-4">Overview</h2>
        <p class="text-gray-700 mb-6">{{ $service['description'] }}</p>

        <h3 class="text-xl font-bold mb-3">What's Included</h3>
        <ul class="space-y-2 text-gray-700 mb-6">
            <li>✓ Professional inspection</li>
            <li>✓ Compliance certification</li>
            <li>✓ Digital report delivery</li>
            <li>✓ 12-month validity</li>
        </ul>

        <h3 class="text-xl font-bold mb-3">Price & Availability</h3>
        <p class="text-gray-700 mb-6">Contact us for a personalized quote. Nationwide coverage, 48-72 hour turnaround.</p>

        <a href="{{ route('book') }}" class="inline-block px-6 py-3 bg-blue-600 text-white rounded font-bold hover:bg-blue-700">Book {{ $service['name'] }}</a>
    </div>
</div>
@endsection
