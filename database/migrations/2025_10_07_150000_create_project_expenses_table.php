<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_expenses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            // who paid (employee)
            $table->foreignId('paid_by_employee_id')->nullable()
                ->constrained('employees')->nullOnDelete();

            // payment type
            $table->enum('payment_type', ['cash', 'transfer', 'cheque', 'card', 'other'])->default('cash');

            $table->decimal('amount', 14, 2);
            $table->dateTime('paid_at')->nullable();

            $table->string('attachment_path')->nullable();

            // audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            $table->index(
                ['company_id','project_id','payment_type','paid_at'],
                'pe_comp_proj_type_paidat_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_expenses');
    }
};
