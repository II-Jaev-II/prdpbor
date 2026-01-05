<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('back_to_office_reports', function (Blueprint $table) {
            $table->text('superior_remarks')->nullable()->after('status');
            $table->timestamp('returned_at')->nullable()->after('superior_remarks');
        });

        // Update the status enum to include 'For Revision'
        DB::statement("ALTER TABLE back_to_office_reports MODIFY COLUMN status ENUM('Pending', 'Approved', 'Rejected', 'For Revision') DEFAULT 'Pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('back_to_office_reports', function (Blueprint $table) {
            $table->dropColumn(['superior_remarks', 'returned_at']);
        });

        // Restore original enum values
        DB::statement("ALTER TABLE back_to_office_reports MODIFY COLUMN status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending'");
    }
};
