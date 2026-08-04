<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CalculatorController extends Controller
{
    public function gasRate(Request $request)
    {
        $validated = $request->validate([
            'meter_reading_start' => 'required|numeric',
            'meter_reading_end' => 'required|numeric',
            'time_seconds' => 'required|numeric',
            'gas_type' => 'required|in:natural,lpg',
        ]);

        $volumeM3 = $validated['meter_reading_end'] - $validated['meter_reading_start'];
        $rateM3PerHr = ($volumeM3 / $validated['time_seconds']) * 3600;

        $calorificValue = $validated['gas_type'] === 'natural' ? 38.7 : 95;
        $energyMj = $volumeM3 * $calorificValue;
        $energyKw = $energyMj / 3.6;
        $netKw = $energyKw * 0.9;

        return response()->json([
            'gas_rate_m3_per_hr' => round($rateM3PerHr, 2),
            'gross_kw' => round($energyKw, 2),
            'net_kw' => round($netKw, 2),
        ]);
    }

    public function pipeSizing(Request $request)
    {
        $validated = $request->validate([
            'flow_rate_m3h' => 'required|numeric',
            'length_m' => 'required|numeric',
            'material' => 'required|string',
        ]);

        $lookup = [
            'copper' => [10 => 12, 20 => 15, 30 => 18, 50 => 22],
            'plastic' => [10 => 12, 20 => 15, 30 => 18, 50 => 22],
            'steel' => [10 => 10, 20 => 12, 30 => 15, 50 => 18],
        ];

        $diameters = $lookup[$validated['material']] ?? $lookup['copper'];
        $flowRate = $validated['flow_rate_m3h'];

        $diameter = 12;
        foreach ($diameters as $flow => $dia) {
            if ($flowRate <= $flow) {
                $diameter = $dia;
                break;
            }
            $diameter = $dia;
        }

        return response()->json([
            'recommended_diameter_mm' => $diameter,
        ]);
    }

    public function heatLoss(Request $request)
    {
        $validated = $request->validate([
            'rooms' => 'required|array',
            'rooms.*.length' => 'required|numeric',
            'rooms.*.width' => 'required|numeric',
            'rooms.*.height' => 'required|numeric',
            'rooms.*.insulation' => 'nullable|in:poor,average,good,excellent',
        ]);

        $totalKw = 0;
        $uValues = ['poor' => 0.6, 'average' => 0.4, 'good' => 0.25, 'excellent' => 0.15];

        foreach ($validated['rooms'] as $room) {
            $surfaceArea = 2 * (($room['length'] * $room['width']) + ($room['length'] * $room['height']) + ($room['width'] * $room['height']));
            $uValue = $uValues[$room['insulation'] ?? 'average'] ?? 0.4;
            $tempDiff = 21;
            $roomKw = ($surfaceArea * $uValue * $tempDiff) / 1000;
            $totalKw += $roomKw;
        }

        return response()->json([
            'total_kw' => round($totalKw, 2),
        ]);
    }

    public function volume(Request $request)
    {
        $validated = $request->validate([
            'length' => 'required|numeric',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
        ]);

        return response()->json([
            'm3' => round($validated['length'] * $validated['width'] * $validated['height'], 2),
        ]);
    }
}
