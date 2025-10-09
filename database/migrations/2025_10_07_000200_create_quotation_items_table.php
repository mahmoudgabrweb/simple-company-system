<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotations')->cascadeOnDelete();

            $table->foreignId('parent_id')->nullable()->constrained('quotation_items')->cascadeOnDelete();

            $table->string('code')->nullable();
            $table->string('title');

            $table->enum('node_type', ['section', 'line'])->default('line');
            $table->boolean('is_static')->default(false); // Mobilization/Demobilization

            $table->longText('notes')->nullable();

            $table->enum('pricing_kind', ['none', 'unit', 'lump'])->default('none');
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->decimal('quantity', 12, 3)->nullable();
            $table->decimal('unit_price', 12, 2)->nullable();
            $table->decimal('lump_sum', 12, 2)->nullable();

            $table->enum('item_status', ['included', 'optional', 'tbd', 'excluded'])->default('included');
            $table->boolean('include_in_total')->default(true);

            $table->decimal('line_total', 12, 2)->default(0);

            $table->unsignedInteger('order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};
