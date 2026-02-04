<?php

namespace App\Http\Controllers\Api;

use App\Exports\AttendanceExport;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    /**
     * Generate PDF attendance report
     */
    public function generatePdfReport(Request $request): BinaryFileResponse
    {
        $query = Attendance::query()->with('employee');

        // Apply filters
        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('from_date')) {
            $query->whereDate('attendance_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('attendance_date', '<=', $request->to_date);
        }

        $attendances = $query->orderBy('attendance_date', 'desc')
            ->orderBy('check_in', 'desc')
            ->get();

        $html = view('reports.attendance-pdf', [
            'attendances' => $attendances,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'generated_at' => now(),
        ])->render();

        $pdf = SnappyPdf::loadHTML($html)
            ->setPaper('a4')
            ->setOrientation('landscape')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10);

        $filename = 'attendance-report-' . now()->format('Y-m-d-His') . '.pdf';

        return response()->download(
            $pdf->output(),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Generate Excel attendance report
     */
    public function generateExcelReport(Request $request): BinaryFileResponse
    {
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $employeeId = $request->employee_id;

        $filename = 'attendance-report-' . now()->format('Y-m-d-His') . '.xlsx';

        return Excel::download(
            new AttendanceExport($fromDate, $toDate, $employeeId),
            $filename
        );
    }
}
