<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('units')) {
            Schema::create('units', function (Blueprint $table) {
                $table->id();
                $table->string('code', 20)->unique(); // pcs, m, m2...
                $table->string('name_ar')->nullable();
                $table->string('name_en')->nullable();
                $table->enum('kind', ['length', 'area', 'volume', 'count', 'weight', 'other'])->default('other');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
