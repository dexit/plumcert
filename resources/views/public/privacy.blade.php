@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-bold mb-8">Privacy Policy</h1>

    <div class="bg-white shadow rounded-lg p-8 space-y-8">
        <div>
            <h2 class="text-2xl font-bold mb-4">1. Information We Collect</h2>
            <p class="text-gray-700">
                We collect information you provide when booking services, such as name, email, phone, postcode, and service preferences. We also collect usage data to improve our website.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-bold mb-4">2. How We Use Your Data</h2>
            <p class="text-gray-700">
                Your data is used to process bookings, send confirmation emails, and provide services. We do not share your information with third parties without consent.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-bold mb-4">3. Data Security</h2>
            <p class="text-gray-700">
                We implement industry-standard security measures to protect your data from unauthorized access.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-bold mb-4">4. Your Rights</h2>
            <p class="text-gray-700">
                You have the right to request access, correction, or deletion of your personal data. Contact us to exercise these rights.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-bold mb-4">5. Contact</h2>
            <p class="text-gray-700">
                For privacy concerns, please <a href="{{ route('contact') }}" class="text-blue-600 hover:text-blue-800">contact us</a>.
            </p>
        </div>
    </div>
</div>
@endsection
