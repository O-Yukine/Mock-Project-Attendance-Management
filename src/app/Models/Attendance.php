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

    public function getTotalBreakAttribute()
    {

        $totalMinutes = $this->breaks
            ->filter(fn($b) => $b->break_start && $b->break_end)
            ->sum(fn($b) => $b->break_start->diffInMinutes($b->break_end));

        if ($totalMinutes === 0) {
            return '';
        }

        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        return sprintf('%02d:%02d', $hours, $minutes);
    }
}
