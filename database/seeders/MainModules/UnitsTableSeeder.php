<?php

namespace Database\Seeders\MainModules;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitsTableSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['code' => 'pcs', 'name_en' => 'Piece', 'name_ar' => 'قطعة', 'kind' => 'count'],
            ['code' => 'm', 'name_en' => 'Meter', 'name_ar' => 'متر', 'kind' => 'length'],
            ['code' => 'm2', 'name_en' => 'Square Meter', 'name_ar' => 'متر مربع', 'kind' => 'area'],
            ['code' => 'm3', 'name_en' => 'Cubic Meter', 'name_ar' => 'متر مكعب', 'kind' => 'volume'],
            ['code' => 'kg', 'name_en' => 'Kilogram', 'name_ar' => 'كجم', 'kind' => 'weight'],
        ];
        foreach ($rows as $r) {
            Unit::firstOrCreate(['code' => $r['code']], $r + ['is_active' => 1]);
        }
    }
}
