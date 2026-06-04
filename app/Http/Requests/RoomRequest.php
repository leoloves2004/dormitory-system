<?php

namespace App\Http\Requests;

use App\Models\Room;
use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $room = $this->route('room');
        $roomId = $room instanceof Room ? $room->id : null;

        return [
            'room_number' => ['required', 'string', 'max:50', 'unique:rooms,room_number,'.($roomId ?? 'NULL')],
            'room_type' => ['required', 'string', 'max:50'],
            'building' => ['nullable', 'string', 'max:100'],
            'floor' => ['required', 'integer', 'min:1'],
            'capacity' => ['required', 'integer', 'min:1'],
            'occupied_slots' => ['nullable', 'integer', 'min:0'],
            'monthly_fee' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:available,occupied,maintenance'],
            'amenities' => ['nullable', 'string'],
        ];
    }
}
