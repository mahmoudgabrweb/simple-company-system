<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->date('spent_at')->nullable()->after('description')->index();
        });

        // Backfill existing rows: use created_at date if present, else today
        DB::statement("UPDATE expenses SET spent_at = DATE(COALESCE(created_at, NOW())) WHERE spent_at IS NULL");
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('spent_at');
        });
    }
};
