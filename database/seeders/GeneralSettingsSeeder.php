<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\GeneralSettings;

class GeneralSettingsSeeder extends Seeder
{
    /**
     * Seed baseline (global) settings. All country-specific reads will
     * fall back to these until you override per-country from the UI.
     */
    public function run(): void
    {
        /** @var GeneralSettings $gs */
        $gs = app(GeneralSettings::class);
        $cid = null; // null => global defaults
        $uid = 1;    // audit as system/admin (adjust if you prefer)

        // === GENERAL ===
        $gs->putGroup([
            'store_name_ar' => 'سي بارت',
            'store_name_en' => 'Cipart',
            'default_currency' => 'SAR',
            'admin_auto_logout_minutes' => 30,
            'site_desc_ar' => '',
            'site_desc_en' => '',
            'notes_text' => '',
        ], 'general', $cid, $uid);

        // === PRODUCTS ===
        $gs->putGroup([
            'return_period_days' => 7,
            'stock_threshold_show_number' => true,
            'stock_threshold_value' => 5,
            'show_min_purchase_counter' => false,
            'show_out_of_stock_last_page' => true,
            // Watermark
            'watermark_enabled' => false,
            'watermark_image_path' => '',
            'watermark_position' => 'bottom-right', // top-left|top-right|center|bottom-left|bottom-right
            'watermark_opacity' => 60,
        ], 'products', $cid, $uid);

        // === SHIPPING ===
        $gs->putGroup([
            'split_by_city' => false,
        ], 'shipping', $cid, $uid);

        // === ADS (Vehicles) ===
        $gs->putGroup([
            'vehicles_enabled' => true,
            'user_posting_enabled' => true,
            'phone_visibility' => 'mask', // show-all|mask|hide
            'security_ratio' => 10,       // percent
            'disabled_message_ar' => '',
            'disabled_message_en' => '',
            // Watermark for ads
            'watermark_enabled' => false,
            'watermark_image_path' => '',
            'watermark_position' => 'bottom-right',
            'watermark_opacity' => 60,
        ], 'ads', $cid, $uid);

        // === SMS ===
        $gs->putGroup([
            'provider' => '',
            'username' => '',
            'password' => '',
            'sender_id' => '',
            'notify_merchants_on_order_status' => true,
            'notify_customers_on_order_status' => true,
            'notify_app_users_on_status' => false,
            'otp_enabled' => true,
        ], 'sms', $cid, $uid);

        // === AUTH ===
        $gs->putGroup([
            'login_by_phone_enabled' => true,
            'require_otp_on_login' => false,
        ], 'auth', $cid, $uid);

        // === APPS ===
        $gs->putGroup([
            'ios_latest_version' => '',
            'ios_force_update' => false,
            'android_latest_version' => '',
            'android_force_update' => false,
        ], 'apps', $cid, $uid);

        // === PAYMENTS ===
        $gs->putGroup([
            'online_enabled' => true,
            'mada_enabled' => true,
            'visa_enabled' => true,
            'applepay_enabled' => true,
            'active_environment' => 'test', // test|live
            // Test
            'test_merchant_name' => '',
            'test_merchant_id' => '',
            'test_api_key' => '',
            'test_api_version' => '',
            'test_link' => '',
            'test_channel' => '',
            'test_save_cards' => '',
            // Live
            'live_merchant_name' => '',
            'live_merchant_id' => '',
            'live_api_key' => '',
            'live_api_version' => '',
            'live_link' => '',
            'live_channel' => '',
            'live_save_cards' => '',
        ], 'payments', $cid, $uid);

        // === GOOGLE ===
        $gs->putGroup([
            'maps_api_key' => '',
        ], 'google', $cid, $uid);

        // === MAINTENANCE ===
        $gs->putGroup([
            'web_enabled' => false,
            'ios_enabled' => false,
            'android_enabled' => false,
            'message_ar' => '',
            'message_en' => '',
        ], 'maintenance', $cid, $uid);

        // === UI ===
        $gs->putGroup([
            'breadcrumbs_enabled' => true,
        ], 'ui', $cid, $uid);
    }
}
