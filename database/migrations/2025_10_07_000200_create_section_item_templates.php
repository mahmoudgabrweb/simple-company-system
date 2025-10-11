<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('section_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false); // if you want cross-company sharing
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('usage_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['company_id', 'is_public', 'is_active']);
        });

        Schema::create('item_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('section_template_id')->nullable()->constrained('section_templates')->nullOnDelete();

            $table->string('name');
            $table->text('description')->nullable();
            $table->string('item_type', 40)->default('simple'); // simple | nested | table | checklist | equipment_list

            // defaults
            $table->decimal('default_quantity', 14, 3)->nullable();
            $table->foreignId('default_unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->decimal('default_unit_price', 14, 2)->nullable();
            $table->json('metadata')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('usage_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['company_id', 'section_template_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_templates');
        Schema::dropIfExists('section_templates');
    }
};
