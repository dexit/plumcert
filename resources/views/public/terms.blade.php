@extends('layouts.app')

@section('title', 'Terms of Service')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-bold mb-8">Terms of Service</h1>

    <div class="bg-white shadow rounded-lg p-8 space-y-8">
        <div>
            <h2 class="text-2xl font-bold mb-4">1. Acceptance of Terms</h2>
            <p class="text-gray-700">
                By using Plumcert's website and services, you agree to these terms and our privacy policy.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-bold mb-4">2. Services</h2>
            <p class="text-gray-700">
                Plumcert provides gas safety inspections, certifications, and related services. All work is performed by Gas Safe registered engineers.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-bold mb-4">3. Booking & Payment</h2>
            <p class="text-gray-700">
                Booking requests are subject to confirmation. Prices are provided upon quote. Payment terms will be specified at booking.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-bold mb-4">4. Liability</h2>
            <p class="text-gray-700">
                Plumcert is not liable for indirect damages or loss of business. Our liability is limited to the cost of services provided.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-bold mb-4">5. Cancellation</h2>
            <p class="text-gray-700">
                Cancellations must be made at least 48 hours before the scheduled appointment. Late cancellations may incur charges.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-bold mb-4">6. Contact</h2>
            <p class="text-gray-700">
                For questions about these terms, please <a href="{{ route('contact') }}" class="text-blue-600 hover:text-blue-800">contact us</a>.
            </p>
        </div>
    </div>
</div>
@endsection
