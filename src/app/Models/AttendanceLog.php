<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTimeLog;


class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'attendance_id', 'work_date', 'clock_in', 'clock_out', 'reason', 'status'];

    protected $casts = ['work_date' => 'date', 'clock_in' => 'datetime:H:i', 'clock_out' => 'datetime:H:i'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function breakLogs()
    {
        return $this->hasMany(BreakTimeLog::class);
    }
}
