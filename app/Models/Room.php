<?php

namespace App\Models;

use Database\Factories\RoomFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['room_number', 'room_type', 'building', 'floor', 'capacity', 'occupied_slots', 'monthly_fee', 'status', 'amenities', 'qr_code'])]
class Room extends Model
{
    /** @use HasFactory<RoomFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['monthly_fee' => 'decimal:2'];
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    public function tenantStudents(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'tenants')->withPivot(['check_in_date', 'check_out_date', 'status'])->withTimestamps();
    }

    public function applications(): HasMany
    {
        return $this->hasMany(RoomApplication::class, 'room_id');
    }

    public function availableBeds(): int
    {
        return max(0, $this->capacity - $this->students()->count());
    }
}
