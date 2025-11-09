<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Homepage content singletons (per company): Hero & About.
 */
return new class extends Migration {
    public function up(): void
    {
        // HERO (singleton per company)
        Schema::create('home_heroes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->unique('company_id');

            // Content
            $table->string('kicker', 120)->nullable();         // e.g., "inspired interiors"
            $table->string('headline', 190)->nullable();       // main H1
            $table->string('subheadline', 255)->nullable();    // short supporting line

            // CTAs
            $table->string('primary_cta_text', 80)->nullable();
            $table->string('primary_cta_url', 255)->nullable();
            $table->string('secondary_cta_text', 80)->nullable();
            $table->string('secondary_cta_url', 255)->nullable();

            // Background
            $table->enum('background_type', ['image', 'color', 'video'])->default('image');
            $table->string('background_value', 255)->nullable(); // path, hex color, or video URL

            $table->boolean('is_active')->default(true)->index();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });

        // ABOUT (singleton per company)
        Schema::create('about_blocks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->unique('company_id');

            // Content
            $table->string('title', 190)->nullable();          // e.g., "About AIA and Its Story?!"
            $table->longText('body')->nullable();              // rich text
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_blocks');
        Schema::dropIfExists('home_heroes');
    }
};
