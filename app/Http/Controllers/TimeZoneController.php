<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class TimeZoneController extends Controller
{
    public function index(Request $request)
    {
        try {
            if(!$request->country_code) {
                return $this->sendApiResponse(Response::HTTP_UNPROCESSABLE_ENTITY, __('Country code is required'));
            }

            if(Str::length($request->country_code) > 2) {
                return $this->sendApiResponse(Response::HTTP_UNPROCESSABLE_ENTITY, __('Country code should be two letters'));
            }

            $data = Zone::getTimezoneByCountry($request->country_code);
            return $this->sendApiResponse(Response::HTTP_OK, __('Zones fetched successfully'), $data);
        } catch (Exception $e) {
            return $this->sendApiResponse(Response::HTTP_SERVICE_UNAVAILABLE, __('Timezone zone is currently down, we are working on it'));
        }
    }
}
