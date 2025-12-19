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
            $table->foreignId('enrolled_activity_id')->nullable()->after('travel_order_id')->constrained('enrolled_activities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('back_to_office_reports', function (Blueprint $table) {
            $table->dropForeign(['enrolled_activity_id']);
            $table->dropColumn('enrolled_activity_id');
        });
    }
};
