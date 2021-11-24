<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $table = 'zone';

    public static function getTimezoneByCountry($countryCode) {
        return static::join('timezone', 'zone.zone_id', '=', 'timezone.zone_id')
        ->where('zone.country_code', $countryCode)
        ->get();
    }
}
