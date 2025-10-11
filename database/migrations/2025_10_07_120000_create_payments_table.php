<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();

            // who paid (free text: client, person, entity)
            $table->string('paid_by')->nullable();

            // who received (employee)
            $table->foreignId('received_by_employee_id')->nullable()
                ->constrained('employees')->nullOnDelete();

            // cash / transfer (keep enum extendable)
            $table->enum('payment_method', ['cash', 'transfer', 'cheque', 'card'])->default('cash');

            $table->decimal('amount', 14, 2);

            // when payment actually happened
            $table->dateTime('paid_at')->nullable();

            // attachment (receipt)
            $table->string('attachment_path')->nullable();

            $table->string('reference')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['company_id', 'project_id', 'payment_method', 'paid_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
