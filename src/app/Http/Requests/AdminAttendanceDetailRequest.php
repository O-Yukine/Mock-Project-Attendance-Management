<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminAttendanceDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'clock_in' => ['required', 'date_format:H:i', 'before:clock_out'],
            'clock_out' => ['required', 'date_format:H:i'],
            'reason' => ['required', 'string'],
            'breaks.*.id'          => ['nullable', 'integer', 'exists:break_times,id'],
            'breaks.*.break_start' => ['nullable', 'date_format:H:i', 'after:clock_in', 'before:clock_out'],
            'breaks.*.break_end' => ['nullable', 'date_format:H:i', 'before:clock_out', 'after_or_equal:breaks.*.break_start',],
        ];
    }

    public function messages()
    {
        return [
            'clock_in.before' => '出勤時間もしくは退勤時間が不適切な値です',
            'clock_in.date_format' => '出勤時間もしくは退勤時間の形式が不正です',
            'clock_out.date_format' => '出勤時間もしくは退勤時間の形式が不正です',

            'reason.required' => '備考を記入してください',

            'breaks.*.break_start.after' => '休憩時間が不適切な値です',
            'breaks.*.break_start.before' => '休憩時間が不適切な値です',
            'breaks.*.break_start.date_format' => '休憩開始時間の形式が不正です',

            'breaks.*.break_end.before' => '休憩時間もしくは退勤時間が不適切な値です',
            'breaks.*.break_end.date_format' => '休憩終了時間の形式が不正です',
            'breaks.*.break_end.after_or_equal' => '休憩終了時間は休憩開始時間以降である必要があります',

        ];
    }
}
