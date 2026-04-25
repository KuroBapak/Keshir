<?php

namespace App\Console\Commands;

use App\Models\AttendanceLog;
use Illuminate\Console\Command;

class AutoCheckoutAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:auto-checkout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically checkout employees who missed tapping out after their shift ends.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        $this->info("Running auto-checkout at {$now->toDateTimeString()}");

        // Get open attendance logs that have a shift assigned
        $openLogs = AttendanceLog::with('shift')
            ->whereNull('check_out')
            ->whereNotNull('shift_id')
            ->get();

        $count = 0;

        foreach ($openLogs as $log) {
            $shiftEndTime = \Carbon\Carbon::parse($log->date . ' ' . $log->shift->end_time);
            
            // If the shift crosses midnight, add a day to the end time
            if ($log->shift->end_time < $log->shift->start_time) {
                $shiftEndTime->addDay();
            }

            // We auto-checkout if current time is past the shift end time
            // To be safe, maybe give a 1-hour buffer? But user said "jika melewati batas". Let's do it exactly if it's past.
            if ($now->greaterThanOrEqualTo($shiftEndTime)) {
                $log->update([
                    'check_out' => $shiftEndTime,
                    'status_out' => 'auto_checkout'
                ]);
                $count++;
            }
        }

        $this->info("Successfully auto-checked out {$count} attendance records.");
    }
}
