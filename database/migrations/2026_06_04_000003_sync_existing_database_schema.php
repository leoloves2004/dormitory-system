<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->syncRooms();
        $this->syncStudents();
        $this->syncTenants();
        $this->syncPayments();
        $this->syncRoomApplications();
        $this->syncVisitorLogs();
    }

    public function down(): void
    {
        //
    }

    private function syncRooms(): void
    {
        if (! Schema::hasTable('rooms')) {
            return;
        }

        Schema::table('rooms', function (Blueprint $table): void {
            if (! Schema::hasColumn('rooms', 'room_type')) {
                $table->string('room_type')->default('standard');
            }
            if (! Schema::hasColumn('rooms', 'occupied_slots')) {
                $table->unsignedInteger('occupied_slots')->default(0);
            }
            if (! Schema::hasColumn('rooms', 'monthly_fee')) {
                $table->decimal('monthly_fee', 10, 2)->default(0);
            }
        });

        if (Schema::hasColumn('rooms', 'monthly_rate')) {
            DB::table('rooms')->where('monthly_fee', 0)->update([
                'monthly_fee' => DB::raw('monthly_rate'),
            ]);
        }
    }

    private function syncStudents(): void
    {
        if (! Schema::hasTable('students')) {
            return;
        }

        Schema::table('students', function (Blueprint $table): void {
            if (! Schema::hasColumn('students', 'contact_number')) {
                $table->string('contact_number')->nullable();
            }
            if (! Schema::hasColumn('students', 'status')) {
                $table->string('status')->default('active');
            }
        });

        if (Schema::hasColumn('students', 'phone')) {
            DB::table('students')->whereNull('contact_number')->update([
                'contact_number' => DB::raw('phone'),
            ]);
        }
    }

    private function syncTenants(): void
    {
        if (! Schema::hasTable('tenants')) {
            return;
        }

        Schema::table('tenants', function (Blueprint $table): void {
            if (! Schema::hasColumn('tenants', 'check_in_date')) {
                $table->date('check_in_date')->nullable();
            }
            if (! Schema::hasColumn('tenants', 'check_out_date')) {
                $table->date('check_out_date')->nullable();
            }
        });

        if (Schema::hasColumn('tenants', 'move_in_date')) {
            DB::table('tenants')->whereNull('check_in_date')->update([
                'check_in_date' => DB::raw('move_in_date'),
            ]);
        }
        if (Schema::hasColumn('tenants', 'move_out_date')) {
            DB::table('tenants')->whereNull('check_out_date')->update([
                'check_out_date' => DB::raw('move_out_date'),
            ]);
        }
    }

    private function syncPayments(): void
    {
        if (! Schema::hasTable('payments')) {
            return;
        }

        Schema::table('payments', function (Blueprint $table): void {
            if (! Schema::hasColumn('payments', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable();
            }
            if (! Schema::hasColumn('payments', 'payment_method')) {
                $table->string('payment_method')->default('cash');
            }
        });

        if (Schema::hasColumn('payments', 'method')) {
            DB::table('payments')->where('payment_method', 'cash')->update([
                'payment_method' => DB::raw('method'),
            ]);
        }

        if (Schema::hasColumn('payments', 'student_id')) {
            $payments = DB::table('payments')->whereNull('tenant_id')->whereNotNull('student_id')->get(['id', 'student_id']);

            foreach ($payments as $payment) {
                $tenantId = DB::table('tenants')
                    ->where('student_id', $payment->student_id)
                    ->where('status', 'active')
                    ->value('id');

                if ($tenantId) {
                    DB::table('payments')->where('id', $payment->id)->update(['tenant_id' => $tenantId]);
                }
            }
        }
    }

    private function syncRoomApplications(): void
    {
        if (! Schema::hasTable('room_applications')) {
            return;
        }

        Schema::table('room_applications', function (Blueprint $table): void {
            if (! Schema::hasColumn('room_applications', 'room_id')) {
                $table->foreignId('room_id')->nullable();
            }
            if (! Schema::hasColumn('room_applications', 'application_date')) {
                $table->date('application_date')->nullable();
            }
            if (! Schema::hasColumn('room_applications', 'remarks')) {
                $table->text('remarks')->nullable();
            }
        });

        if (Schema::hasColumn('room_applications', 'preferred_room_id')) {
            DB::table('room_applications')->whereNull('room_id')->update([
                'room_id' => DB::raw('preferred_room_id'),
            ]);
        }
        if (Schema::hasColumn('room_applications', 'admin_notes')) {
            DB::table('room_applications')->whereNull('remarks')->update([
                'remarks' => DB::raw('admin_notes'),
            ]);
        }

        DB::table('room_applications')->whereNull('application_date')->update([
            'application_date' => DB::raw('DATE(created_at)'),
        ]);
    }

    private function syncVisitorLogs(): void
    {
        if (! Schema::hasTable('visitor_logs')) {
            return;
        }

        Schema::table('visitor_logs', function (Blueprint $table): void {
            if (! Schema::hasColumn('visitor_logs', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable();
            }
            if (! Schema::hasColumn('visitor_logs', 'visit_date')) {
                $table->date('visit_date')->nullable();
            }
        });

        if (Schema::hasColumn('visitor_logs', 'student_id')) {
            $logs = DB::table('visitor_logs')->whereNull('tenant_id')->whereNotNull('student_id')->get(['id', 'student_id']);

            foreach ($logs as $log) {
                $tenantId = DB::table('tenants')
                    ->where('student_id', $log->student_id)
                    ->where('status', 'active')
                    ->value('id');

                if ($tenantId) {
                    DB::table('visitor_logs')->where('id', $log->id)->update(['tenant_id' => $tenantId]);
                }
            }
        }

        DB::table('visitor_logs')->whereNull('visit_date')->update([
            'visit_date' => DB::raw('DATE(time_in)'),
        ]);
    }
};
