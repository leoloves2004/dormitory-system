<?php

namespace App\Models;

use Database\Factories\RoomFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['room_number', 'building', 'floor', 'capacity', 'monthly_rate', 'status', 'amenities', 'qr_code'])]
class Room extends Model
{
    /** @use HasFactory<RoomFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['monthly_rate' => 'decimal:2'];
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(RoomApplication::class, 'preferred_room_id');
    }

    public function availableBeds(): int
    {
        return max(0, $this->capacity - $this->students()->count());
    }
}
