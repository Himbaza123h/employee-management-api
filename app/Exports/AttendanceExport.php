<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $fromDate;
    protected $toDate;
    protected $employeeId;

    public function __construct($fromDate = null, $toDate = null, $employeeId = null)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->employeeId = $employeeId;
    }

    /**
     * Query for export
     */
    public function query()
    {
        $query = Attendance::query()->with('employee');

        if ($this->employeeId) {
            $query->where('employee_id', $this->employeeId);
        }

        if ($this->fromDate) {
            $query->whereDate('attendance_date', '>=', $this->fromDate);
        }

        if ($this->toDate) {
            $query->whereDate('attendance_date', '<=', $this->toDate);
        }

        return $query->orderBy('attendance_date', 'desc')
            ->orderBy('check_in', 'desc');
    }

    /**
     * Column headings
     */
    public function headings(): array
    {
        return [
            'ID',
            'Employee ID',
            'Employee Name',
            'Email',
            'Date',
            'Check-In',
            'Check-Out',
            'Hours Worked',
            'Status',
        ];
    }

    /**
     * Map each row
     */
    public function map($attendance): array
    {
        $hoursWorked = null;
        $status = 'Checked In';

        if ($attendance->check_out) {
            $hoursWorked = round($attendance->check_in->diffInHours($attendance->check_out), 2);
            $status = 'Completed';
        }

        return [
            $attendance->id,
            $attendance->employee->employee_identifier,
            $attendance->employee->full_name,
            $attendance->employee->email,
            $attendance->attendance_date->format('Y-m-d'),
            $attendance->check_in->format('Y-m-d H:i:s'),
            $attendance->check_out ? $attendance->check_out->format('Y-m-d H:i:s') : 'N/A',
            $hoursWorked ?? 'N/A',
            $status,
        ];
    }

    /**
     * Apply styles to the sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (headings)
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E0E0E0']
                ],
            ],
        ];
    }

    /**
     * Sheet title
     */
    public function title(): string
    {
        return 'Attendance Report';
    }
}
