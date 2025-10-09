<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitsTableSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['code' => 'pcs', 'name_ar' => 'قطعة', 'name_en' => 'Piece', 'kind' => 'count'],
            ['code' => 'm', 'name_ar' => 'متر', 'name_en' => 'Meter', 'kind' => 'length'],
            ['code' => 'm2', 'name_ar' => 'متر مربع', 'name_en' => 'Square Meter', 'kind' => 'area'],
            ['code' => 'm3', 'name_ar' => 'متر مكعب', 'name_en' => 'Cubic Meter', 'kind' => 'volume'],
            ['code' => 'kg', 'name_ar' => 'كجم', 'name_en' => 'Kilogram', 'kind' => 'weight'],
            ['code' => 'l', 'name_ar' => 'لتر', 'name_en' => 'Liter', 'kind' => 'volume'],
        ];
        foreach ($rows as $r) {
            Unit::firstOrCreate(['code' => $r['code']], $r + ['is_active' => 1]);
        }
    }
}
