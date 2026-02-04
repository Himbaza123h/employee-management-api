<?php

namespace App\Notifications;

use App\Models\Attendance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AttendanceRecordedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Attendance $attendance,
        public string $type = 'check_in'
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $employee = $this->attendance->employee;

        if ($this->type === 'check_in') {
            return (new MailMessage)
                ->subject('Check-In Recorded')
                ->greeting("Hello {$employee->full_name}!")
                ->line('Your check-in has been successfully recorded.')
                ->line("Date: {$this->attendance->attendance_date->format('F d, Y')}")
                ->line("Check-in Time: {$this->attendance->check_in->format('h:i A')}")
                ->line('Have a productive day!')
                ->salutation('Best regards, ' . config('app.name'));
        } else {
            $hoursWorked = $this->attendance->check_in->diffInHours($this->attendance->check_out);

            return (new MailMessage)
                ->subject('Check-Out Recorded')
                ->greeting("Hello {$employee->full_name}!")
                ->line('Your check-out has been successfully recorded.')
                ->line("Date: {$this->attendance->attendance_date->format('F d, Y')}")
                ->line("Check-in Time: {$this->attendance->check_in->format('h:i A')}")
                ->line("Check-out Time: {$this->attendance->check_out->format('h:i A')}")
                ->line("Total Hours: " . round($hoursWorked, 2) . " hours")
                ->line('Thank you for your hard work today!')
                ->salutation('Best regards, ' . config('app.name'));
        }
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'attendance_id' => $this->attendance->id,
            'type' => $this->type,
            'date' => $this->attendance->attendance_date,
        ];
    }
}
