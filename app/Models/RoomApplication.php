<?php

namespace App\Models;

use Database\Factories\RoomApplicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['student_id', 'preferred_room_id', 'approved_by', 'status', 'preferred_move_in_date', 'reason', 'admin_notes', 'reviewed_at'])]
class RoomApplication extends Model
{
    /** @use HasFactory<RoomApplicationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['preferred_move_in_date' => 'date', 'reviewed_at' => 'datetime'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function preferredRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'preferred_room_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
