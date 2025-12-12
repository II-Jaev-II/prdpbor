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
        Schema::create('enrolled_activities', function (Blueprint $table) {
            $table->id();
            $table->string('activity_name');
            $table->enum('unit_component', ['IBUILD', 'IREAP', 'IPLAN', 'ISUPPORT']);
            $table->enum('purpose', ['Site Specific', 'Non Site Specific']);
            $table->string('purpose_type');
            $table->foreignId('subproject_id')->nullable()->constrained('subproject_lists');
            $table->string('subproject_name')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrolled_activities');
    }
};
