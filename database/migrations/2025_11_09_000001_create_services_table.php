<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            // Multi-company scope
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            // Core fields
            $table->string('title', 190);
            $table->string('slug', 190)->unique();
            $table->string('excerpt', 500)->nullable();
            $table->longText('body')->nullable();

            // Presentation / ordering
            $table->string('icon', 190)->nullable(); // icon class or image path
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->enum('status', ['draft', 'published', 'archived'])->default('published')->index();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            // Helpful composite index for listing
            $table->index(['company_id', 'status', 'display_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
