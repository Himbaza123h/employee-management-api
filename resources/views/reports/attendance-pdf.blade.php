<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info {
            margin-bottom: 20px;
        }
        .info p {
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .status-complete {
            color: green;
            font-weight: bold;
        }
        .status-pending {
            color: orange;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Employee Attendance Report</h1>
        <p>{{ config('app.name') }}</p>
    </div>

    <div class="info">
        @if($from_date || $to_date)
            <p><strong>Report Period:</strong>
                @if($from_date && $to_date)
                    {{ \Carbon\Carbon::parse($from_date)->format('F d, Y') }} - {{ \Carbon\Carbon::parse($to_date)->format('F d, Y') }}
                @elseif($from_date)
                    From {{ \Carbon\Carbon::parse($from_date)->format('F d, Y') }}
                @else
                    Until {{ \Carbon\Carbon::parse($to_date)->format('F d, Y') }}
                @endif
            </p>
        @endif
        <p><strong>Generated On:</strong> {{ $generated_at->format('F d, Y h:i A') }}</p>
        <p><strong>Total Records:</strong> {{ $attendances->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Employee</th>
                <th>Date</th>
                <th>Check-In</th>
                <th>Check-Out</th>
                <th>Hours</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->id }}</td>
                    <td>
                        {{ $attendance->employee->full_name }}<br>
                        <small>{{ $attendance->employee->employee_identifier }}</small>
                    </td>
                    <td>{{ $attendance->attendance_date->format('M d, Y') }}</td>
                    <td>{{ $attendance->check_in->format('h:i A') }}</td>
                    <td>
                        @if($attendance->check_out)
                            {{ $attendance->check_out->format('h:i A') }}
                        @else
                            <span class="status-pending">Pending</span>
                        @endif
                    </td>
                    <td>
                        @if($attendance->check_out)
                            {{ round($attendance->check_in->diffInHours($attendance->check_out), 2) }} hrs
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($attendance->check_out)
                            <span class="status-complete">Complete</span>
                        @else
                            <span class="status-pending">In Progress</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">
                        No attendance records found for the specified period.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
        <p>{{ config('app.name') }} - Employee Management System</p>
    </div>
</body>
</html>
