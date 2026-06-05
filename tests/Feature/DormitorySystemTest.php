<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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
        config(['services.api_bearer_token' => 'test-token']);
        Room::factory()->create();

        $this->withHeader('Authorization', 'Bearer test-token')
            ->getJson('/api/rooms')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_rooms_api_requires_bearer_token(): void
    {
        config(['services.api_bearer_token' => 'test-token']);

        $this->getJson('/api/rooms')
            ->assertUnauthorized()
            ->assertJson(['message' => 'Invalid or missing bearer token.']);
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

    public function test_admin_can_import_students_from_csv(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $file = UploadedFile::fake()->createWithContent(
            'students.csv',
            "name,email,student_number,course,year_level,phone\nImport Student,import.student@example.com,STU-IMPORT-001,BSIT,2nd Year,09123456789\n"
        );

        $this->actingAs($admin)
            ->post(route('admin.imports.students'), ['file' => $file])
            ->assertSessionHas('status');

        $this->assertDatabaseHas('users', ['email' => 'import.student@example.com', 'role' => 'student']);
        $this->assertDatabaseHas('students', ['student_number' => 'STU-IMPORT-001', 'course' => 'BSIT']);
    }

    public function test_admin_can_import_payments_from_csv(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $studentUser = User::factory()->create(['role' => 'student']);
        $room = Room::factory()->create();
        $student = Student::factory()->create([
            'user_id' => $studentUser->id,
            'room_id' => $room->id,
            'student_number' => 'STU-PAY-001',
        ]);
        Tenant::factory()->create(['student_id' => $student->id, 'room_id' => $room->id, 'status' => 'active']);
        $file = UploadedFile::fake()->createWithContent(
            'payments.csv',
            "student_number,amount,payment_date,due_date,method,reference_number,status,notes\nSTU-PAY-001,1500,2026-06-05,2026-06-30,gcash,PAY-IMPORT-001,paid,June payment\n"
        );

        $this->actingAs($admin)
            ->post(route('admin.imports.payments'), ['file' => $file])
            ->assertSessionHas('status');

        $this->assertDatabaseHas('payments', [
            'amount' => 1500,
            'reference_number' => 'PAY-IMPORT-001',
            'payment_method' => 'gcash',
        ]);
    }
}
