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
            'breaks.*.break_start' => ['nullable', 'after:clock_in', 'before:clock_out'],
            'breaks.*.break_end' => ['nullable', 'before:clock_out'],

        ];
    }

    public function messages()
    {
        return [
            'clock_in.before' => '出勤時間もしくは退勤時間が不適切な値です',
            'reason.required' => '備考を記入してください',

            'breaks.*.break_start.after' => '休憩時間が不適切な値です',
            'breaks.*.break_start.before' => '休憩時間が不適切な値です',
            'breaks.*.break_end.before' => '休憩時間もしくは退勤時間が不適切な値です',

        ];
    }
}
