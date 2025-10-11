<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_section_id')->constrained('quotation_sections')->cascadeOnDelete();
            $table->foreignId('parent_item_id')->nullable()->constrained('quotation_items')->cascadeOnDelete();

            $table->unsignedInteger('item_number')->nullable(); // display numbering per section
            $table->string('title');
            $table->longText('description')->nullable();

            $table->string('item_type', 40)->default('simple'); // simple|nested|table|checklist|equipment_list

            // pricing (lump or unit — we stay simple & flexible)
            $table->decimal('quantity', 14, 3)->nullable();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->decimal('unit_price', 14, 2)->nullable();
            $table->decimal('total_price', 14, 2)->default(0); // cached

            $table->json('metadata')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_excluded')->default(false);

            $table->timestamps();

            $table->index(['quotation_section_id', 'parent_item_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};
