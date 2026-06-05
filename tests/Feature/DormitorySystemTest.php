<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DormitorySystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_admin_dashboard_renders(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Admin Dashboard');
    }

    public function test_admin_rooms_page_renders(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Room::factory()->create(['room_number' => 'A-101']);

        $this->actingAs($admin)
            ->get(route('admin.rooms.index'))
            ->assertOk()
            ->assertSee('A-101');
    }

    public function test_student_dashboard_renders(): void
    {
        $studentUser = User::factory()->create(['role' => 'student']);
        Student::factory()->create(['user_id' => $studentUser->id, 'room_id' => Room::factory()->create()->id]);

        $this->actingAs($studentUser)
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('Student Portal');
    }

    public function test_rooms_api_returns_json(): void
    {
        Room::factory()->create();

        $this->getJson('/api/rooms')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_payment_report_exports_csv(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.reports.export', ['payments', 'csv']))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_student_can_register_and_reach_portal(): void
    {
        $this->post(route('register'), [
            'name' => 'New Student',
            'email' => 'new.student@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'student_number' => 'STU-TEST-001',
            'course' => 'BSIT',
            'year_level' => '1st Year',
        ])->assertRedirect(route('student.dashboard'));

        $this->assertDatabaseHas('students', ['student_number' => 'STU-TEST-001']);
    }

    public function test_visitor_log_cannot_exceed_eight_people(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $studentUser = User::factory()->create(['role' => 'student']);
        $room = Room::factory()->create();
        $student = Student::factory()->create(['user_id' => $studentUser->id, 'room_id' => $room->id]);
        $tenant = Tenant::factory()->create(['student_id' => $student->id, 'room_id' => $room->id]);

        $this->actingAs($admin)
            ->post(route('admin.visitor-logs.store'), [
                'tenant_id' => $tenant->id,
                'visitor_name' => 'Group Visit',
                'visitor_phone' => '09123456789',
                'visitor_count' => 9,
                'visit_date' => now()->toDateString(),
                'purpose' => 'Family visit',
                'time_in' => now()->toDateTimeString(),
            ])
            ->assertSessionHasErrors('visitor_count');

        $this->assertDatabaseMissing('visitor_logs', ['visitor_name' => 'Group Visit']);
    }
}
