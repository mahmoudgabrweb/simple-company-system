<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            // Basic transaction info
            $table->date('txn_date');
            $table->enum('type', ['deposit', 'withdrawal', 'transfer_in', 'transfer_out', 'fee', 'interest']);
            $table->decimal('amount', 15, 2); // always positive; direction via 'type'
            $table->string('currency', 3)->default('AED');
            $table->decimal('exchange_rate', 10, 4)->default(1); // to company base

            // Simple account fields (no separate module required)
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_label')->nullable(); // e.g., "Main AED"

            // Optional linking fields
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('counterparty')->nullable(); // Client/Supplier name
            $table->string('reference')->nullable();    // Cheque #, TT #, etc.

            $table->text('description')->nullable();
            $table->boolean('reconciled')->default(false);

            // Attachment (voucher, slip)
            $table->string('attachment_path')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['company_id', 'txn_date']);
            $table->index(['company_id', 'type']);
            $table->index(['company_id', 'bank_name', 'account_label']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};
