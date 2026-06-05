<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visitor_logs', function (Blueprint $table): void {
            if (! Schema::hasColumn('visitor_logs', 'visitor_count')) {
                $table->unsignedTinyInteger('visitor_count')->default(1)->after('visitor_phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('visitor_logs', function (Blueprint $table): void {
            if (Schema::hasColumn('visitor_logs', 'visitor_count')) {
                $table->dropColumn('visitor_count');
            }
        });
    }
};
