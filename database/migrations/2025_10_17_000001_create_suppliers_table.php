<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()
                ->constrained('companies')->nullOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->foreignId('city_id')->nullable()
                ->constrained('cities')->nullOnDelete()->cascadeOnUpdate();
            $table->string('address')->nullable();
            $table->string('contact_person_name');
            $table->string('contact_person_phone');
            $table->string('bank_name')->nullable();
            $table->string('iban')->nullable();
            $table->string('swift')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
