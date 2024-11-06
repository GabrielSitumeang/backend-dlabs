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
            $table->integer('age')->nullable();
            $table->enum('membership_status', ['basic', 'premium', 'vip'])->default('basic');
        });
    }

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('age');
        $table->dropColumn('membership_status');
    });
}

};
