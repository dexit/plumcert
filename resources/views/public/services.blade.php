@extends('layouts.app')

@section('title', 'Services')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-bold mb-12 text-center">Our Services</h1>

    <div class="grid grid-cols-2 gap-8">
        @foreach ($services as $slug => $service)
            <div class="bg-white shadow rounded-lg p-8 hover:shadow-lg transition">
                <h2 class="text-2xl font-bold mb-4">{{ $service['name'] }}</h2>
                <p class="text-gray-700 mb-6">{{ $service['description'] }}</p>
                <a href="{{ route('services.show', $slug) }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">Learn More</a>
            </div>
        @endforeach
    </div>
</div>
@endsection
