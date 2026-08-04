<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Plumcert') - UK Gas Safety Certificates</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root {
            --brand-blue: #1a3a6b;
            --brand-yellow: #FFD700;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <a href="{{ route('home') }}" class="text-2xl font-bold" style="color: var(--brand-blue);">Plumcert</a>
                <div class="hidden md:flex space-x-6">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">Home</a>
                    <a href="{{ route('services') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">Services</a>
                    <a href="{{ route('findings') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">Findings</a>
                    <a href="{{ route('tools') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">Tools</a>
                    <a href="{{ route('book') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">Book</a>
                    <a href="{{ route('contact') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">Contact</a>
                </div>
            </div>
            <a href="{{ route('book') }}" class="px-4 py-2 rounded font-medium text-white" style="background-color: var(--brand-blue);">Get Quote</a>
        </div>
    </nav>

    <!-- Messages -->
    @if ($message = Session::get('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mx-auto max-w-7xl mt-4 rounded">
            {{ $message }}
        </div>
    @endif

    <!-- Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-4 gap-8">
            <div>
                <h3 class="font-bold text-white mb-4">Plumcert</h3>
                <p class="text-sm">UK Gas Safe certified inspection & certification.</p>
            </div>
            <div>
                <h4 class="font-bold text-white mb-4">Links</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('about') }}">About</a></li>
                    <li><a href="{{ route('services') }}">Services</a></li>
                    <li><a href="{{ route('findings') }}">Findings</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-white mb-4">Info</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('privacy') }}">Privacy</a></li>
                    <li><a href="{{ route('terms') }}">Terms</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-white mb-4">For</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('estate-agents') }}">Estate Agents</a></li>
                    <li><a href="{{ route('landlords') }}">Landlords</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 px-4 sm:px-6 lg:px-8 py-6 text-center text-sm">
            &copy; {{ date('Y') }} Plumcert. All rights reserved.
        </div>
    </footer>
</body>
</html>
