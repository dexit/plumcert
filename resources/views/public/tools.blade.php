@extends('layouts.app')

@section('title', 'Free Calculators & Tools')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-bold mb-4 text-center">Free Calculators & Tools</h1>
    <p class="text-center text-gray-600 mb-12">Professional calculation tools for gas engineers and installers.</p>

    <div class="grid grid-cols-2 gap-8 mb-12">

        <!-- Gas Rate Calculator -->
        <div class="bg-white shadow rounded-lg p-6" x-data="gasRateCalc()" @input="calculate()">
            <h2 class="text-2xl font-bold mb-4">Gas Rate Calculator</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Start Meter Reading (m³)</label>
                    <input type="number" x-model.number="start" step="0.001" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">End Meter Reading (m³)</label>
                    <input type="number" x-model.number="end" step="0.001" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Time (seconds)</label>
                    <input type="number" x-model.number="time" step="1" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Gas Type</label>
                    <select x-model="gasType" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="ng">Natural Gas</option>
                        <option value="lpg">LPG</option>
                    </select>
                </div>
                <div class="bg-blue-50 p-4 rounded mt-6 space-y-2">
                    <div><span class="font-medium">Gas Rate:</span> <span x-text="gasRate.toFixed(3)"></span> m³/hr</div>
                    <div><span class="font-medium">Gross kW:</span> <span x-text="grossKw.toFixed(2)"></span> kW</div>
                    <div><span class="font-medium">Net kW:</span> <span x-text="netKw.toFixed(2)"></span> kW</div>
                </div>
            </div>
        </div>

        <!-- Pipe Sizing Calculator -->
        <div class="bg-white shadow rounded-lg p-6" x-data="pipeSizingCalc()" @input="calculate()">
            <h2 class="text-2xl font-bold mb-4">Pipe Sizing Calculator</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Gas Type</label>
                    <select x-model="gasType" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="ng">Natural Gas</option>
                        <option value="lpg">LPG</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Total kW Load</label>
                    <input type="number" x-model.number="kwLoad" step="0.1" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Pipe Length (m)</label>
                    <input type="number" x-model.number="length" step="0.1" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Pipe Material</label>
                    <select x-model="material" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="copper">Copper</option>
                        <option value="steel">Steel</option>
                    </select>
                </div>
                <div class="bg-blue-50 p-4 rounded mt-6">
                    <div><span class="font-medium">Recommended Diameter:</span> <span x-text="diameter"></span> mm</div>
                    <p class="text-sm text-gray-600 mt-2" x-text="recommendation"></p>
                </div>
            </div>
        </div>

        <!-- Heat Loss Calculator -->
        <div class="bg-white shadow rounded-lg p-6" x-data="heatLossCalc()" @input="calculate()">
            <h2 class="text-2xl font-bold mb-4">Heat Loss Calculator</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Room Area (m²)</label>
                    <input type="number" x-model.number="roomArea" step="0.1" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Wall Area (m²)</label>
                    <input type="number" x-model.number="wallArea" step="0.1" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Window Area (m²)</label>
                    <input type="number" x-model.number="windowArea" step="0.1" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Insulation Level</label>
                    <select x-model="insulation" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="poor">Poor</option>
                        <option value="avg">Average</option>
                        <option value="good">Good</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Temperature Difference (°C)</label>
                    <input type="number" x-model.number="deltaT" step="0.1" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="bg-blue-50 p-4 rounded mt-6">
                    <div><span class="font-medium">Heat Loss:</span> <span x-text="heatLoss.toFixed(2)"></span> kW</div>
                </div>
            </div>
        </div>

        <!-- Gas Installation Volume Calculator -->
        <div class="bg-white shadow rounded-lg p-6" x-data="volumeCalc()">
            <h2 class="text-2xl font-bold mb-4">Gas Installation Volume Calculator</h2>
            <div class="space-y-4">
                <p class="text-sm text-gray-600 mb-4">Add pipe segments to calculate total system volume.</p>

                <div class="space-y-3 max-h-64 overflow-y-auto mb-4">
                    <template x-for="(segment, idx) in segments" :key="idx">
                        <div class="flex gap-2 items-end border-b pb-3">
                            <div class="flex-1">
                                <label class="block text-xs font-medium mb-1">Diameter (mm)</label>
                                <input type="number" x-model.number="segment.diameter" step="0.1" @input="calculate()" class="w-full px-2 py-1 border rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-medium mb-1">Length (m)</label>
                                <input type="number" x-model.number="segment.length" step="0.1" @input="calculate()" class="w-full px-2 py-1 border rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <button @click.prevent="removeSegment(idx)" type="button" class="px-3 py-1 bg-red-500 text-white text-sm rounded hover:bg-red-600">Remove</button>
                        </div>
                    </template>
                </div>

                <button @click.prevent="addSegment()" type="button" class="w-full px-4 py-2 bg-gray-300 text-gray-900 font-medium rounded hover:bg-gray-400">+ Add Segment</button>

                <div class="bg-blue-50 p-4 rounded mt-6">
                    <div><span class="font-medium">Total Volume:</span> <span x-text="totalVolume.toFixed(2)"></span> litres</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function gasRateCalc() {
    return {
        start: 0,
        end: 0,
        time: 1,
        gasType: 'ng',
        gasRate: 0,
        grossKw: 0,
        netKw: 0,
        calculate() {
            const volume = this.end - this.start;
            this.gasRate = (volume * 3600) / this.time;
            const calorific = this.gasType === 'ng' ? 38.7 : 95;
            this.grossKw = this.gasRate * calorific;
            this.netKw = this.grossKw * 0.9;
        }
    }
}

function pipeSizingCalc() {
    return {
        gasType: 'ng',
        kwLoad: 0,
        length: 0,
        material: 'copper',
        diameter: '15',
        recommendation: 'Copper 15mm suitable for <30kW loads',
        calculate() {
            if (this.kwLoad < 30) {
                this.diameter = '15';
                this.recommendation = 'Copper 15mm recommended for <30kW loads';
            } else if (this.kwLoad < 60) {
                this.diameter = '22';
                this.recommendation = 'Copper 22mm recommended for 30-60kW loads';
            } else if (this.kwLoad < 100) {
                this.diameter = '28';
                this.recommendation = 'Copper 28mm recommended for 60-100kW loads';
            } else {
                this.diameter = '>28';
                this.recommendation = 'Larger than 28mm recommended for >100kW loads. Consult regulations.';
            }
        }
    }
}

function heatLossCalc() {
    return {
        roomArea: 0,
        wallArea: 0,
        windowArea: 0,
        insulation: 'avg',
        deltaT: 15,
        heatLoss: 0,
        calculate() {
            const uValues = {
                poor: { wall: 1.5, window: 5.0 },
                avg: { wall: 0.5, window: 2.0 },
                good: { wall: 0.3, window: 1.4 }
            };
            const u = uValues[this.insulation];
            const wallLoss = u.wall * this.wallArea * this.deltaT;
            const windowLoss = u.window * this.windowArea * this.deltaT;
            this.heatLoss = (wallLoss + windowLoss) / 1000;
        }
    }
}

function volumeCalc() {
    return {
        segments: [{ diameter: 22, length: 10 }],
        totalVolume: 0,
        addSegment() {
            this.segments.push({ diameter: 22, length: 10 });
            this.calculate();
        },
        removeSegment(idx) {
            this.segments.splice(idx, 1);
            this.calculate();
        },
        calculate() {
            this.totalVolume = this.segments.reduce((sum, seg) => {
                const radius = seg.diameter / 2000;
                const v = Math.PI * radius * radius * seg.length * 1000;
                return sum + v;
            }, 0);
        }
    }
}
</script>
@endsection
