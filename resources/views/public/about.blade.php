@extends('layouts.app')

@section('title', 'About')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-bold mb-8">About Plumcert</h1>

    <div class="bg-white shadow rounded-lg p-8 mb-8">
        <h2 class="text-2xl font-bold mb-4">Who We Are</h2>
        <p class="text-gray-700 mb-4">
            Plumcert is a UK-based gas safety certification company specializing in fast, compliant inspections for homeowners and rental properties. Our Gas Safe registered engineers deliver professional, certified reports.
        </p>
        <p class="text-gray-700 mb-4">
            We offer gas safety certificates, boiler services, electrical inspections (EICR), and energy ratings (EPC) — all backed by industry expertise and regulatory compliance.
        </p>
    </div>

    <div class="bg-white shadow rounded-lg p-8 mb-8">
        <h2 class="text-2xl font-bold mb-4">Our Mission</h2>
        <p class="text-gray-700">
            To make gas safety simple, accessible, and affordable for every UK property owner.
        </p>
    </div>

    <div class="bg-white shadow rounded-lg p-8">
        <h2 class="text-2xl font-bold mb-4">Why Choose Us?</h2>
        <ul class="space-y-2 text-gray-700">
            <li>✓ Gas Safe registered engineers</li>
            <li>✓ Fast turnaround (48-72 hours)</li>
            <li>✓ Digital reporting & compliance</li>
            <li>✓ Nationwide coverage</li>
            <li>✓ Transparent pricing</li>
        </ul>
    </div>
</div>
@endsection
