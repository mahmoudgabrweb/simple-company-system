<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('variation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variation_section_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->string('name');
            $table->text('description')->nullable();

            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete(); // reuse Units
            $table->decimal('qty', 14, 3)->default(1);
            $table->decimal('unit_price', 14, 2)->default(0);

            $table->enum('discount_type', ['none', 'percent', 'fixed'])->default('none');
            $table->decimal('discount_value', 14, 2)->default(0); // if percent -> 0..100

            $table->decimal('subtotal', 14, 2)->default(0); // qty*price minus discount

            $table->unsignedInteger('order')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variation_items');
    }
};
