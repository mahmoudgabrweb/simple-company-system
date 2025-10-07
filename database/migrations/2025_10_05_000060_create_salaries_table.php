<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('expense_type_id')->nullable()->constrained('expense_types')->nullOnDelete();

            $table->string('title');
            $table->date('month');
            $table->enum('type', ['salary', 'overtime', 'bonus'])->default('salary');
            $table->decimal('amount', 12, 2)->default(0);
            $table->unsignedInteger('days_count')->default(0);

            $table->text('location')->nullable(); // (optional) if you later need; safe to remove
            $table->timestamps();

            $table->index(['employee_id', 'month']);
            $table->index(['expense_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
