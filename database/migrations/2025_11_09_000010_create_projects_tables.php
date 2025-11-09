<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Main projects table
        Schema::create('site_projects', function (Blueprint $table) {
            $table->id();

            // Multi-company scope
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            // Core
            $table->string('title', 190);
            $table->string('slug', 190)->unique();
            $table->string('excerpt', 500)->nullable();
            $table->longText('body')->nullable();

            // Meta shown in the modal/card on homepage
            $table->string('client', 190)->nullable();
            $table->string('duration', 190)->nullable();
            $table->string('category', 190)->nullable();

            // Media / presentation
            $table->string('cover_image', 255)->nullable();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->enum('status', ['draft', 'published', 'archived'])->default('published')->index();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['company_id', 'status', 'display_order']);
        });

        // Project gallery images
        Schema::create('site_project_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_project_id')->constrained('site_projects')->cascadeOnDelete();
            $table->string('image_path', 255);
            $table->string('caption', 255)->nullable();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->boolean('is_cover')->default(false)->index(); // if you want to override cover_image
            $table->timestamps();

            $table->index(['site_project_id', 'display_order']);
        });

        // Project key features (bullet list shown in the modal)
        Schema::create('site_project_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_project_id')->constrained('site_projects')->cascadeOnDelete();
            $table->string('label', 255);
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->timestamps();

            $table->index(['site_project_id', 'display_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_project_features');
        Schema::dropIfExists('site_project_images');
        Schema::dropIfExists('site_projects');
    }
};
