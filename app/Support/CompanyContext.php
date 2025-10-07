<?php
namespace App\Support;

use App\Models\Company;

class CompanyContext
{
    public static function id(): ?int
    {
        return session('company_id');
    }

    public static function company(): ?Company
    {
        $id = self::id();
        return $id ? Company::find($id) : null;
    }

    public static function set(?int $companyId): void
    {
        session(['company_id' => $companyId]);
    }

    public static function clear(): void
    {
        session()->forget('company_id');
    }
}
