<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();

            $table->string('code')->nullable();
            $table->unsignedInteger('version')->default(1);

            // Lifecycle
            $table->enum('status', [
                'draft', 'sending_to_client', 'waiting_client_response',
                'client_approved', 'in_progress', 'completed'
            ])->default('draft');

            // Only one per project with is_active = 1
            $table->boolean('is_active')->default(false);

            $table->date('issue_date')->nullable();
            $table->date('valid_until')->nullable();

            $table->timestamp('sent_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Taxes (keep VAT at quotation level; discount is on project)
            $table->decimal('vat_rate', 5, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_total', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);

            $table->string('pdf_path')->nullable();

            $table->timestamps();

            $table->unique(['project_id', 'is_active'], 'uq_project_active_only_one');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
