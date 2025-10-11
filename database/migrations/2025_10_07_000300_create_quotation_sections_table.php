<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quotation_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotations')->cascadeOnDelete();

            $table->string('letter_code', 5)->nullable(); // A, B, C...
            $table->string('title');
            $table->text('description')->nullable();   // intro text
            $table->boolean('is_static')->default(false);

            $table->unsignedInteger('order')->default(0);

            $table->decimal('total_amount', 14, 2)->default(0); // cached sum of child items
            $table->timestamps();

            $table->index(['quotation_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_sections');
    }
};
