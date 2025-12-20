<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('site_services', function (Blueprint $table) {
            $table->id();

            $table->string('title');                     // e.g. "Plans and Projects"
            $table->string('icon')->nullable();          // SVG/PNG path
            $table->text('short_description')->nullable(); // card short text
            $table->longText('description')->nullable();   // full modal text (HTML)

            $table->unsignedInteger('display_order')->default(0); // 01,02,03..
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('site_services');
    }
};
