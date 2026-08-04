@extends('layouts.app')

@section('title', 'Finding')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <a href="{{ route('findings') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">&larr; Back to Findings</a>

    <div class="bg-white shadow rounded-lg p-8">
        <div class="bg-gray-300 h-64 rounded mb-8"></div>

        <h1 class="text-4xl font-bold mb-4">{{ $finding->title ?? 'Finding' }}</h1>

        <div class="flex gap-4 mb-6">
            <span class="inline-block px-4 py-2 bg-yellow-100 text-yellow-800 font-bold rounded">{{ $finding->classification ?? 'Gas Safety' }}</span>
            <span class="inline-block px-4 py-2 bg-green-100 text-green-800 font-bold rounded">{{ $finding->status ?? 'Approved' }}</span>
        </div>

        <div class="prose prose-sm max-w-none mb-8">
            <p class="text-gray-700 leading-relaxed">
                {{ $finding->description ?? 'Detailed finding information will appear here.' }}
            </p>
        </div>

        <div class="border-t pt-6">
            <p class="text-gray-600 text-sm">
                <strong>Date:</strong> {{ $finding->created_at->format('d M Y') ?? 'N/A' }}
            </p>
        </div>
    </div>
</div>
@endsection
