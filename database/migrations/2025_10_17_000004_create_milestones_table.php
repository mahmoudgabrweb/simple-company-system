<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('milestones', function (Blueprint $table) {
            $table->id();

            // Company scope
            $table->foreignId('company_id')->nullable()
                ->constrained('companies')->nullOnDelete()->cascadeOnUpdate();

            // Polymorphic parent: Quotation or Variation
            $table->morphs('milestonable'); // milestonable_type, milestonable_id (indexed)

            // Core
            $table->string('title', 190);
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2); // store effective amount
            $table->date('due_date')->nullable();
            $table->enum('status', ['planned', 'approved', 'invoiced', 'paid', 'cancelled'])->default('planned');

            // Ordering within a parent (unique per parent)
            $table->unsignedInteger('order_index')->default(1);

            // Optional file
            $table->string('attachment_path')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // Keep ordering clean per parent
            $table->unique(['milestonable_type', 'milestonable_id', 'order_index'], 'milestones_parent_order_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestones');
    }
};
