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
        Schema::table('enrolled_activities', function (Blueprint $table) {
            $table->text('unit_component')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrolled_activities', function (Blueprint $table) {
            $table->enum('unit_component', ['IBUILD', 'IREAP', 'IPLAN', 'ISUPPORT'])->change();
        });
    }
};
