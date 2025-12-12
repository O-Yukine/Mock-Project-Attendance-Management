<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;

class CreateDailyAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:auto-create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::all();
        $today = now()->toDateString();

        foreach ($users as $user) {
            $attendance = Attendance::firstOrCreate(
                ['user_id' => $user->id, 'work_date' => $today],
                ['status' => 'off']
            );
            $attendance->breaks()->create(['attendance_id' => $attendance->id]);
        }
    }
}
