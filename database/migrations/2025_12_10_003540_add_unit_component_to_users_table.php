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
        Schema::table('users', function (Blueprint $table) {
              $table->enum('unit_component', ['IBUILD', 'IREAP', 'IPLAN', 'GGU', 'SES', 'MEL','INFOACE', 'PROCUREMENT', 'FINANCE', 'IDU', 'ADMIN'])->after('superior_role')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('unit_component');
        });
    }
};
