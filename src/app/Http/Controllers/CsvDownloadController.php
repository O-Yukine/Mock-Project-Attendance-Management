<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;


class CsvDownloadController extends Controller
{
    public function export(Request $request, $id)
    {

        $month = \Carbon\Carbon::createFromFormat(
            'Y/m',
            $request->query('month')
        );

        $staff = User::findOrFail($id);

        $staff_attendances = Attendance::with('breaks')
            ->where('user_id', $id)
            ->whereYear('work_date', $month->year)
            ->whereMonth('work_date', $month->month)
            ->get();

        $csvHeader = ['work_date', 'clock_in', 'clock_out', 'break', 'total_work_time'];

        $fileName = 'attendance_' . $staff->name . '_' . $month->format('Ym');

        $response = response()->stream(function () use ($csvHeader, $staff_attendances) {
            $handle = fopen('php://output', 'w');

            mb_convert_variables('SJIS-win', 'UTF-8', $csvHeader);

            fputcsv($handle, $csvHeader);

            foreach ($staff_attendances as $attendance) {
                fputcsv($handle, [
                    $attendance->work_date->format('m/d'),
                    $attendance->clock_in?->format('H:i') ?? '',
                    $attendance->clock_out?->format('H:i') ?? '',
                    $attendance->total_break ?? '',
                    $attendance->total_work_time ?? '',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
        return $response;
    }
}
