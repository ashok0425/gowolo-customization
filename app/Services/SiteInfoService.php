<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Reads branding and pricing data from the dashboardv2 DB (dashboard_db connection).
 */
class SiteInfoService
{
    public function getSiteInfo(int $userId): ?object
    {
        return DB::connection('dashboard_db')
            ->table('site_information')
            ->where('user_id', $userId)
            ->where('active', 1)
            ->first();
    }

    public function getCustomizationPrice(): float
    {
        $row = DB::connection('dashboard_db')
            ->table('site_info_setting')
            ->select('amount')
            ->first();

        return $row ? (float) $row->amount : 0.0;
    }

    public function getUserById(int $userId): ?object
    {
        return DB::connection('dashboard_db')
            ->table('users')
            ->where('id', $userId)
            ->first();
    }

    public function getTechnicians(): array
    {
        return DB::connection('dashboard_db')
            ->table('emp_personal_profiles')
            ->where('status', 1)
            ->get()
            ->toArray();
    }
}
