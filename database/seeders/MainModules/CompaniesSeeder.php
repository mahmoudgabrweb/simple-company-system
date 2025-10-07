<?php
namespace Database\Seeders\MainModules;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompaniesSeeder extends Seeder
{
    public function run(): void
    {
        Company::firstOrCreate(['code' => 'ACME'], ['name' => 'Acme Corp']);
        Company::firstOrCreate(['code' => 'GLOB'], ['name' => 'Global Motors']);
    }
}
