<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('location')->nullable();
            $table->text('map')->nullable();

            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();

            // Project-level discount (applies to all quotations)
            $table->decimal('discount_percent', 5, 2)->default(0);

            $table->timestamps();

            $table->index(['company_id', 'client_id', 'city_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
