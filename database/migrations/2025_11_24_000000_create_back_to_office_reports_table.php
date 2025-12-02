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
        Schema::create('back_to_office_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('report_num', 50);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('purpose', 255);
            $table->string('place', 255);
            $table->text('accomplishment');
            $table->json('photos'); // Store array of photo paths
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();

            // Indexes for performance
            $table->index('report_num');
            $table->index('status');
            $table->index('start_date');
            $table->index('purpose');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('back_to_office_reports');
    }
};
