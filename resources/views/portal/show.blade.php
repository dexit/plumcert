<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Certificates — Plumcert</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen font-sans">
<header class="bg-blue-700 text-white py-4 px-6 shadow">
    <div class="max-w-3xl mx-auto flex items-center gap-3">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        <div>
            <h1 class="text-lg font-bold">Plumcert Customer Portal</h1>
            <p class="text-blue-200 text-sm">{{ $customer->first_name }} {{ $customer->last_name }}</p>
        </div>
    </div>
</header>

<main class="max-w-3xl mx-auto px-4 py-8">

    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-300 text-green-800 rounded-lg p-4">
            {{ session('success') }}
        </div>
    @endif

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Your Gas Safety Certificates</h2>

        @forelse ($certificates as $cert)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="font-semibold text-gray-900">{{ $cert->certificate_number }}</div>
                        <div class="text-sm text-gray-500 mt-1">
                            {{ str_replace('_', ' ', ucwords($cert->type, '_')) }} &middot;
                            {{ optional($cert->job->property)->address ?? '—' }}
                        </div>
                        @if ($cert->issued_at)
                            <div class="text-sm text-gray-500 mt-1">
                                Issued: {{ $cert->issued_at->format('d M Y') }}
                                @if ($cert->expires_at)
                                    &middot; Expires: <span class="{{ $cert->expires_at->isPast() ? 'text-red-600 font-medium' : ($cert->expires_at->diffInDays() < 60 ? 'text-amber-600 font-medium' : '') }}">{{ $cert->expires_at->format('d M Y') }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    <a href="{{ URL::signedRoute('portal.cert', ['customer' => $customer->id, 'certificate' => $cert->id]) }}"
                       class="shrink-0 inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>
        @empty
            <div class="text-gray-500 text-sm">No certificates on file yet.</div>
        @endforelse
    </section>

    <section class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-1">Request a Service Visit</h2>
        <p class="text-sm text-gray-500 mb-4">Let us know your preferred date and we'll get back to you to confirm.</p>

        <form method="POST" action="{{ URL::signedRoute('portal.book', ['customer' => $customer->id]) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Date</label>
                <input type="date" name="preferred_date" required min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('preferred_date') border-red-500 @enderror">
                @error('preferred_date') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                <textarea name="notes" rows="3" placeholder="Any specific concerns or access notes..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-lg transition">
                Request Booking
            </button>
        </form>
    </section>
</main>

<footer class="text-center text-xs text-gray-400 py-8">
    This secure link was sent to you by your gas engineer. &copy; {{ date('Y') }} Plumcert.
</footer>
</body>
</html>
