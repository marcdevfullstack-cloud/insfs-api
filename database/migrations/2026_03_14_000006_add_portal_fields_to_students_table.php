<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignUuid('portal_user_id')
                  ->nullable()
                  ->after('block_reason')
                  ->constrained('portal_users')
                  ->nullOnDelete();

            $table->foreignUuid('application_id')
                  ->nullable()
                  ->after('portal_user_id')
                  ->constrained('enrollment_applications')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['portal_user_id']);
            $table->dropForeign(['application_id']);
            $table->dropColumn(['portal_user_id', 'application_id']);
        });
    }
};