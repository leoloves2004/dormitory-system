<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomApplication;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use App\Models\VisitorLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Dormitory Admin',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]
        );

        if (Room::query()->exists()) {
            return;
        }

        Room::factory(18)->create();

        User::factory(25)->create()->each(function (User $user): void {
            Student::factory()->create(['user_id' => $user->id]);
        });

        Student::query()->whereNotNull('room_id')->get()->each(function (Student $student): void {
            Tenant::factory()->create([
                'student_id' => $student->id,
                'room_id' => $student->room_id,
            ]);
        });

        Payment::factory(60)->create();
        RoomApplication::factory(30)->create();
        VisitorLog::factory(20)->create();

        Room::query()->withCount('students')->get()->each(function (Room $room): void {
            $room->update([
                'occupied_slots' => $room->students_count,
                'status' => $room->students_count >= $room->capacity ? 'occupied' : 'available',
            ]);
        });
    }
}
