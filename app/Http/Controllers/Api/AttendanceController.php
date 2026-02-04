<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\StoreAttendanceRequest;
use App\Http\Requests\Attendance\UpdateAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Jobs\SendAttendanceNotification;
use App\Models\Attendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendances
     */
    public function index(Request $request): JsonResponse
    {
        $query = Attendance::query()->with('employee');

        // Filter by employee
        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('attendance_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('attendance_date', '<=', $request->to_date);
        }

        $perPage = $request->get('per_page', 15);
        $attendances = $query->orderBy('attendance_date', 'desc')
            ->orderBy('check_in', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => AttendanceResource::collection($attendances->items()),
            'meta' => [
                'current_page' => $attendances->currentPage(),
                'last_page' => $attendances->lastPage(),
                'per_page' => $attendances->perPage(),
                'total' => $attendances->total(),
            ]
        ]);
    }

    /**
     * Store a new attendance record (check-in)
     */
    public function store(StoreAttendanceRequest $request): JsonResponse
    {
        $attendance = Attendance::create([
            'employee_id' => $request->employee_id,
            'check_in' => $request->check_in ?? now(),
            'attendance_date' => $request->attendance_date ?? now()->toDateString(),
        ]);

        $attendance->load('employee');

        // Dispatch job to send email notification
        SendAttendanceNotification::dispatch($attendance, 'check_in');

        return response()->json([
            'message' => 'Check-in recorded successfully',
            'data' => new AttendanceResource($attendance)
        ], 201);
    }

    /**
     * Display a specific attendance record
     */
    public function show(Attendance $attendance): JsonResponse
    {
        $attendance->load('employee');

        return response()->json([
            'data' => new AttendanceResource($attendance)
        ]);
    }

    /**
     * Update an attendance record
     */
    public function update(UpdateAttendanceRequest $request, Attendance $attendance): JsonResponse
    {
        $attendance->update($request->validated());

        return response()->json([
            'message' => 'Attendance updated successfully',
            'data' => new AttendanceResource($attendance)
        ]);
    }

    /**
     * Record check-out time
     */
    public function checkOut(Request $request, Attendance $attendance): JsonResponse
    {
        if ($attendance->check_out) {
            return response()->json([
                'message' => 'Check-out already recorded'
            ], 400);
        }

        $attendance->update([
            'check_out' => $request->check_out ?? now()
        ]);

        $attendance->load('employee');

        // Dispatch job to send email notification
        SendAttendanceNotification::dispatch($attendance, 'check_out');

        return response()->json([
            'message' => 'Check-out recorded successfully',
            'data' => new AttendanceResource($attendance)
        ]);
    }

    /**
     * Remove an attendance record
     */
    public function destroy(Attendance $attendance): JsonResponse
    {
        $attendance->delete();

        return response()->json([
            'message' => 'Attendance deleted successfully'
        ]);
    }
}
