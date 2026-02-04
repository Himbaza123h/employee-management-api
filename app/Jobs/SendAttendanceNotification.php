<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Notifications\AttendanceRecordedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAttendanceNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Attendance $attendance,
        public string $type = 'check_in'
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $employee = $this->attendance->employee;

        if ($employee && $employee->email) {
            // Send notification to employee email
            $employee->notify(new AttendanceRecordedNotification($this->attendance, $this->type));
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        // Log the failure or send alert
        Log::error('Failed to send attendance notification', [
            'attendance_id' => $this->attendance->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
