<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quotation_item_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_item_id')->constrained('quotation_items')->cascadeOnDelete();

            $table->string('detail_type', 40); // specification|equipment|checklist_item|table_row
            $table->string('label')->nullable();
            $table->text('value')->nullable();
            $table->json('extra_data')->nullable();
            $table->unsignedInteger('order')->default(0);

            $table->timestamps();

            $table->index(['quotation_item_id', 'detail_type', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_item_details');
    }
};
