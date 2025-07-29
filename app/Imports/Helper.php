<?php

namespace App\Imports;

use Illuminate\Support\Carbon;

class Helper
{
    public static function excelSerialToCarbon(float|int $serial, string $system = '1900', ?string $tz = 'UTC'): Carbon
    {
        if ($system === '1904') {
            // Excel 1904 system: day 0 = 1904-01-01
            $base = Carbon::create(1904, 1, 1, 0, 0, 0, 'UTC');
            $days  = (int) floor($serial);
            $secs  = (int) round(($serial - $days) * 86400);

            return $base->copy()->addDays($days)->addSeconds($secs)->tz($tz);
        }

        // Excel 1900 system (phổ biến): dùng hằng số 25569 (1970-01-01 - 1899-12-30)
        $timestamp = ($serial - 25569) * 86400;

        return Carbon::createFromTimestampUTC((int) round($timestamp))->tz($tz);
    }
}
