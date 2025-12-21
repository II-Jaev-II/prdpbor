<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('back_to_office_reports', function (Blueprint $table) {
            $table->string('approval_id', 4)->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('back_to_office_reports', function (Blueprint $table) {
            $table->dropColumn('approval_id');
        });
    }
};
