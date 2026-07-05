<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * List the authenticated user's appointments (as customer or provider).
     */
    public function index(Request $request)
    {
        $userId = (int) $request->header('X-User-Id');

        $appointments = Appointment::where('user_id', $userId)
            ->orWhere('provider_id', $userId)
            ->orderBy('start_at')
            ->get();

        return response()->json(['data' => $appointments]);
    }

    /**
     * Book a new appointment for the authenticated user.
     */
    public function store(StoreAppointmentRequest $request)
    {
        $appointment = Appointment::create([
            ...$request->validated(),
            'user_id' => (int) $request->header('X-User-Id'),
            'status' => 'scheduled',
        ]);

        return response()->json([
            'message' => 'Appointment created successfully',
            'data' => $appointment,
        ], 201);
    }

    /**
     * Update an appointment owned by the authenticated user.
     */
    public function update(UpdateAppointmentRequest $request, int $id)
    {
        $appointment = $this->findOwned($request, $id);

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found.'], 404);
        }

        $appointment->update($request->validated());

        return response()->json([
            'message' => 'Appointment updated successfully',
            'data' => $appointment,
        ]);
    }

    /**
     * Cancel (soft delete) an appointment owned by the authenticated user.
     */
    public function destroy(Request $request, int $id)
    {
        $appointment = $this->findOwned($request, $id);

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found.'], 404);
        }

        $appointment->delete();

        return response()->json(['message' => 'Appointment deleted successfully']);
    }

    private function findOwned(Request $request, int $id): ?Appointment
    {
        $userId = (int) $request->header('X-User-Id');

        return Appointment::where('id', $id)
            ->where(fn ($q) => $q->where('user_id', $userId)->orWhere('provider_id', $userId))
            ->first();
    }
}
