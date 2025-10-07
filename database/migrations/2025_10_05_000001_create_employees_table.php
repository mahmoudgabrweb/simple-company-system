<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('job_id')->constrained('jobs')->cascadeOnDelete();
            $table->string('name');
            $table->string('phone', 50);
            $table->string('email')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('image_path')->nullable();
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();
            $table->decimal('salary', 12, 2)->default(0);
            $table->timestamps();

            $table->index(['company_id', 'job_id']);
            $table->index(['company_id', 'end_at']); // for status filters
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
