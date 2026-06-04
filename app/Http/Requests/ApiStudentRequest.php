<?php

namespace App\Http\Requests;

use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;

class ApiStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $student = $this->route('student');
        $studentId = $student instanceof Student ? $student->id : null;

        return [
            'user_id' => ['required', 'exists:users,id'],
            'student_number' => ['required', 'string', 'max:50', 'unique:students,student_number,'.($studentId ?? 'NULL')],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'course' => ['nullable', 'string', 'max:100'],
            'year_level' => ['nullable', 'string', 'max:50'],
            'contact_number' => ['nullable', 'string', 'max:30'],
            'status' => ['nullable', 'in:active,inactive'],
        ];
    }
}
