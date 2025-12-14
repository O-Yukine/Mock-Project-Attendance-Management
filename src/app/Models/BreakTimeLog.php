<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AttendanceLog;
use App\Models\BreakTime;

class BreakTimeLog extends Model
{
    use HasFactory;

    protected $fillable = ['attendance_log_id', 'break_time_id', 'break_start', 'break_end', 'action'];

    protected $casts = ['break_start' => 'datetime:H:i', 'break_end' => 'datetime:H:i'];

    public function attendanceLog()
    {
        return $this->belongsTo(AttendanceLog::class);
    }

    public function breakTime()
    {

        return $this->belongsTo(BreakTime::class);
    }
}
