<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\BreakTime;
use App\Models\AttendanceLog;


class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'work_date', 'clock_in', 'clock_out', 'status'];

    protected $casts = [
        'work_date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breaks()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function getBreakMinutesAttribute()
    {
        return $this->breaks
            ->filter(fn($b) => $b->break_start && $b->break_end)
            ->sum(fn($b) => $b->break_start->diffInMinutes($b->break_end));
    }
    public function getTotalBreakAttribute()
    {

        if ($this->break_minutes === 0) {
            return '';
        }

        return sprintf(
            '%02d:%02d',
            floor($this->break_minutes / 60),
            $this->break_minutes % 60
        );
    }

    public function getTotalWorkTimeAttribute()
    {
        if (!$this->clock_in || !$this->clock_out) {
            return '';
        }

        $totalMinutes = max(
            0,
            $this->clock_in->diffInMinutes($this->clock_out)
                - $this->break_minutes
        );

        return sprintf(
            '%02d:%02d',
            floor($totalMinutes / 60),
            $totalMinutes % 60
        );
    }
}
