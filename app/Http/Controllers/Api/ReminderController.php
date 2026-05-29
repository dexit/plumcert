<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            Reminder::where('sent_at', null)->paginate(20)
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'remind_at' => 'required|date',
        ]);

        $reminder = Reminder::create($validated);
        return response()->json($reminder, 201);
    }

    public function show(Reminder $reminder)
    {
        return response()->json($reminder);
    }

    public function update(Request $request, Reminder $reminder)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string',
            'description' => 'sometimes|string',
            'remind_at' => 'sometimes|date',
        ]);

        $reminder->update($validated);
        return response()->json($reminder);
    }

    public function destroy(Reminder $reminder)
    {
        $reminder->delete();
        return response()->json(null, 204);
    }

    public function sendNow(Reminder $reminder)
    {
        $reminder->update(['sent_at' => now()]);
        return response()->json(['message' => 'Reminder sent']);
    }
}
