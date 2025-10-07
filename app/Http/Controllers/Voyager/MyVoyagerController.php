<?php

namespace App\Http\Controllers\Voyager;

use Illuminate\Support\Facades\DB;
use TCG\Voyager\Http\Controllers\VoyagerController as BaseVoyagerController;

class MyVoyagerController extends BaseVoyagerController
{
    public function index()
    {
        $countries = collect();
        // Your custom logic for the dashboard
        return view('vendor.voyager.dashboard', compact("countries")); // Ensure this points to your custom view
    }
}
