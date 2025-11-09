<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Global singleton-style settings per company + footer links
 */
return new class extends Migration {
    public function up(): void
    {
        // Singleton per company
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();

            // Multi-company scope (1 row per company)
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->unique('company_id');

            // Top bar / contact
            $table->json('emails')->nullable(); // ["info@...","sales@..."]
            $table->json('phones')->nullable(); // ["+971 ...", "+20 ..."]
            $table->string('address_line', 255)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('country', 120)->nullable();

            // Socials
            $table->string('whatsapp_url', 255)->nullable();
            $table->string('facebook_url', 255)->nullable();
            $table->string('instagram_url', 255)->nullable();
            $table->string('linkedin_url', 255)->nullable();
            $table->string('youtube_url', 255)->nullable();

            // CTA for “Get Our Portfolio”
            $table->string('portfolio_cta_text', 120)->nullable(); // e.g., "Get Our Portfolio"
            $table->string('portfolio_cta_url', 255)->nullable();  // file or route

            // Footer content (optional short about text)
            $table->string('footer_about_title', 190)->nullable();
            $table->text('footer_about_text')->nullable();

            // Branding
            $table->string('logo_path', 255)->nullable();
            $table->string('favicon_path', 255)->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });

        // Footer links (multiple items, grouped)
        Schema::create('footer_links', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->string('label', 190);
            $table->string('url', 255);
            $table->string('group_key', 50)->default('useful'); // e.g., useful/services/projects/custom
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();

            $table->index(['company_id', 'group_key', 'display_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_links');
        Schema::dropIfExists('site_settings');
    }
};
