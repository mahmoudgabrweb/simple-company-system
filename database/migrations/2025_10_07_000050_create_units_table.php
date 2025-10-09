<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();       // m, m2, pcs, kg, l
            $table->string('name_ar')->nullable();      // متر، متر مربع، قطعة
            $table->string('name_en')->nullable();      // meter, square meter, piece
            $table->enum('kind', ['length', 'area', 'volume', 'count', 'weight', 'other'])->default('other');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
