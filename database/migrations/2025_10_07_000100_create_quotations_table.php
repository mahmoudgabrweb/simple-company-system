<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();

            $table->string('quotation_number')->nullable();
            $table->unsignedInteger('version')->default(1);

            // keep lifecycle lean; you can expand later
            $table->enum('status', ['draft', 'sent', 'waiting_client_response', 'approved', 'rejected', 'in_progress', 'completed'])
                ->default('draft');

            $table->boolean('is_active')->default(false); // one per project
            $table->decimal('total_amount', 14, 2)->default(0); // cached grand (sum of included items; add VAT/discount outside if needed)

            $table->longText('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->date('valid_until')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('pdf_path')->nullable();

            $table->timestamps();

            $table->unique(['project_id', 'is_active'], 'uq_project_one_active'); // enforces one active per project
            $table->index(['project_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
