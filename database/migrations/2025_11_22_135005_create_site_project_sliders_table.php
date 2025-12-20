<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('site_project_sliders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('site_project_id')
                ->constrained('site_projects')
                ->cascadeOnDelete();

            // Slider image path
            $table->string('image');

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_project_sliders');
    }
};
