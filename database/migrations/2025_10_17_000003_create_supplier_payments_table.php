<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()
                ->constrained('companies')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('supplier_material_id')->nullable()
                ->constrained('supplier_materials')->nullOnDelete()->cascadeOnUpdate();
            $table->string('title', 190);
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2);
            $table->enum('payment_type', ['cash', 'transfer', 'cheque', 'card', 'other'])->default('cash');
            $table->foreignId('paid_by_employee_id')->nullable()
                ->constrained('employees')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamp('paid_at')->nullable();
            $table->string('attachment_path')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
    }
};
