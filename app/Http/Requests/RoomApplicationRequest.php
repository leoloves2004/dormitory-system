<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_id' => ['nullable', 'exists:rooms,id'],
            'application_date' => ['nullable', 'date'],
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }
}
