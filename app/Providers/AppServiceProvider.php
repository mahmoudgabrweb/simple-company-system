<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\QuotationSection;
use App\Observers\QuotationObserver;
use App\Observers\QuotationItemObserver;
use App\Observers\QuotationSectionObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Quotation::observe(QuotationObserver::class);
        QuotationItem::observe(QuotationItemObserver::class);
        QuotationSection::observe(QuotationSectionObserver::class);
    }
}
