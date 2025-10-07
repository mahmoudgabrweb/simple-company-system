<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Setting;

class MainSiteSettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $counter = 10;

        Setting::updateOrCreate(
            ["key" => "site.phone_number"],
            ["display_name" => "", "value" => "", "type" => "text", "order" => ++$counter, "group" => "Site"]
        );

        Setting::updateOrCreate(
            ["key" => "site.address"],
            ["display_name" => "", "value" => "", "type" => "text", "order" => ++$counter, "group" => "Site"]
        );

        Setting::updateOrCreate(
            ["key" => "site.email"],
            ["display_name" => "", "value" => "", "type" => "text", "order" => ++$counter, "group" => "Site"]
        );

        // Social media
        Setting::updateOrCreate(
            ["key" => "site.facebook"],
            ["display_name" => "", "value" => "", "type" => "text", "order" => ++$counter, "group" => "Site"]
        );

        Setting::updateOrCreate(
            ["key" => "site.instagram"],
            ["display_name" => "", "value" => "", "type" => "text", "order" => ++$counter, "group" => "Site"]
        );

        Setting::updateOrCreate(
            ["key" => "site.x"],
            ["display_name" => "", "value" => "", "type" => "text", "order" => ++$counter, "group" => "Site"]
        );

        Setting::updateOrCreate(
            ["key" => "site.tiktok"],
            ["display_name" => "", "value" => "", "type" => "text", "order" => ++$counter, "group" => "Site"]
        );

        Setting::updateOrCreate(
            ["key" => "site.snapchat"],
            ["display_name" => "", "value" => "", "type" => "text", "order" => ++$counter, "group" => "Site"]
        );

        Setting::updateOrCreate(
            ["key" => "site.youtube"],
            ["display_name" => "", "value" => "", "type" => "text", "order" => ++$counter, "group" => "Site"]
        );
    }
}
