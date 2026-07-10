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
        Schema::table('meals', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('user_id');
            $table->boolean('synced')->default(false)->after('eaten_at');
        });

        Schema::table('bowel_movements', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('user_id');
            $table->boolean('synced')->default(false)->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'synced']);
        });

        Schema::table('bowel_movements', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'synced']);
        });
    }
};
