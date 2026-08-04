@extends('layouts.app')

@section('title', 'Book a Service')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-bold mb-8 text-center">Book a Service</h1>

    <div class="bg-white shadow rounded-lg p-8">
        <form action="{{ route('book.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium mb-2">Full Name *</label>
                <input type="text" id="name" name="name" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium mb-2">Email *</label>
                <input type="email" id="email" name="email" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium mb-2">Phone *</label>
                <input type="tel" id="phone" name="phone" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('phone') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div x-data="postcodeSearch()">
                <div>
                    <label for="postcode" class="block text-sm font-medium mb-2">Postcode *</label>
                    <div class="flex gap-2">
                        <input type="text" id="postcode" x-model="postcode" name="postcode" required class="flex-1 px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="button" @click="lookupPostcode()" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 font-medium">Find Address</button>
                    </div>
                    @error('postcode') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div x-show="results.length" class="mt-4 border rounded bg-white shadow-lg max-h-48 overflow-y-auto">
                    <template x-for="r in results" :key="r.address">
                        <button type="button" @click="selectAddress(r)" class="w-full text-left px-4 py-2 hover:bg-blue-100 border-b last:border-b-0">
                            <span x-text="r.address"></span>
                        </button>
                    </template>
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium mb-2 mt-4">Address *</label>
                    <input type="text" id="address" x-model="address" name="address" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('address') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="city" class="block text-sm font-medium mb-2">City *</label>
                    <input type="text" id="city" x-model="city" name="city" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('city') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <script>
                    function postcodeSearch() {
                        return {
                            postcode: '',
                            address: '',
                            city: '',
                            results: [],

                            async lookupPostcode() {
                                if (!this.postcode || this.postcode.length < 2) {
                                    this.results = [];
                                    return;
                                }

                                try {
                                    const response = await fetch(`/api/v1/addresses/search?q=${encodeURIComponent(this.postcode)}`);
                                    const data = await response.json();
                                    this.results = data;
                                } catch (e) {
                                    console.error('Address lookup failed:', e);
                                    this.results = [];
                                }
                            },

                            selectAddress(result) {
                                this.postcode = result.postcode || this.postcode;
                                this.address = result.address || '';
                                this.city = '';
                                this.results = [];
                            }
                        };
                    }
                </script>
            </div>

            <div>
                <label for="service_type" class="block text-sm font-medium mb-2">Service Type *</label>
                <select id="service_type" name="service_type" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select a service</option>
                    <option value="gas-safety-cert">Gas Safety Certificate</option>
                    <option value="boiler-service">Boiler Service</option>
                    <option value="boiler-install">Boiler Installation</option>
                    <option value="eicr">EICR</option>
                    <option value="epc">EPC</option>
                </select>
                @error('service_type') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="message" class="block text-sm font-medium mb-2">Message</label>
                <textarea id="message" name="message" rows="5" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                @error('message') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white font-bold rounded hover:bg-blue-700">Submit Booking Request</button>
        </form>
    </div>
</div>
@endsection
