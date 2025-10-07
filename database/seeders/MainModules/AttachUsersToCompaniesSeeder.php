<?php

namespace Database\Seeders\MainModules;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;

class AttachUsersToCompaniesSeeder extends Seeder
{
    public function run(): void
    {
        $acme = Company::where('code', 'ACME')->first();
        $glob = Company::where('code', 'GLOB')->first();

        // Regular admin/user → belongs to a specific company
        if ($u = User::where('email', 'admin@acme.test')->first()) {
            $u->company_id = $acme?->id;
            $u->save();
        }
        if ($u = User::where('email', 'ops@glob.test')->first()) {
            $u->company_id = $glob?->id;
            $u->save();
        }

        // Super Admin stays null (access all)
        // if ($root = User::where('email','super@root.test')->first()) {
        //     $root->company_id = null;
        //     $root->save();
        // }
    }
}
