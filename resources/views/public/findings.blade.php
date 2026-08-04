@extends('layouts.app')

@section('title', 'Findings')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-bold mb-12 text-center">Case Findings</h1>

    @if (count($findings) > 0)
        <div class="grid grid-cols-3 gap-6">
            @foreach ($findings as $finding)
                <a href="{{ route('findings.show', $finding->id) }}" class="bg-white shadow rounded-lg overflow-hidden hover:shadow-lg transition">
                    <div class="bg-gray-300 h-40"></div>
                    <div class="p-6">
                        <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded mb-2">{{ $finding->classification ?? 'Gas Safety' }}</span>
                        <h3 class="font-bold text-lg mb-2">{{ $finding->title }}</h3>
                        <p class="text-gray-600 text-sm">{{ Str::limit($finding->description, 80) }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="bg-gray-100 rounded-lg p-12 text-center">
            <p class="text-gray-700">No findings published yet.</p>
        </div>
    @endif
</div>
@endsection
