<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();

            $table->string('name');
            $table->text('address')->nullable();
            $table->string('location')->nullable(); // e.g., GPS or short descriptor
            $table->text('map')->nullable();        // Google map (embed/link)

            $table->timestamps();

            $table->index(['company_id', 'client_id', 'city_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
