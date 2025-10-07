<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('role_assignables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id');              // who is assigning
            $table->unsignedBigInteger('assignable_role_id');   // which role they can assign
            $table->unique(['role_id', 'assignable_role_id'], 'role_assignables_unique');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('assignable_role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_assignables');
    }
};
