<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('site_projects', function (Blueprint $table) {
            $table->id();

            $table->string('name'); // Project name

            // Image path (uploaded file)
            $table->string('image');

            $table->text('short_description')->nullable();
            $table->longText('full_description')->nullable();

            $table->boolean('is_active')->default(true);

            // Optional meta fields
            $table->string('client')->nullable();    // e.g. Client name
            $table->string('duration')->nullable();  // e.g. "6 months"
            $table->string('category')->nullable();  // e.g. "Interior", "Fitout"

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_projects');
    }
};
