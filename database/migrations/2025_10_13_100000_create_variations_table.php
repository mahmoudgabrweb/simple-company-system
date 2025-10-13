<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('variations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            // Keep a reference to the active quotation at creation time (for auditing)
            $table->foreignId('quotation_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();

            $table->string('code')->nullable()->index();       // optional human code (e.g., VAR-2025-001)
            $table->string('title')->nullable();
            $table->text('notes')->nullable();

            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'expired', 'completed'])
                ->default('draft')->index();

            $table->char('currency', 3)->default('AED');
            $table->date('valid_until')->nullable();

            // roll-up financials (computed from sections/items)
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('tax', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variations');
    }
};
