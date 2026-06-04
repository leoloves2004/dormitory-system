<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiRoomApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'status' => ['required', 'in:pending,approved,rejected,cancelled'],
            'application_date' => ['nullable', 'date'],
            'reason' => ['nullable', 'string', 'max:1000'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
