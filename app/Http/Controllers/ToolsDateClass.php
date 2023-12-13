<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\App;

class ToolsDateClass extends Controller
{
    // Untuk memeriksa apakah tanggal berada dalam range tertentu
    public function checkDateRange($tanggal,$dateStart,$dateEnd)
    {
        $tanggalObj = Carbon::parse($tanggal);
        $start_date = Carbon::parse($dateStart);
        $end_date = Carbon::parse($dateEnd);

        if ($tanggalObj->between($start_date, $end_date)) {
            return true;
        } else {
            return false;
        }
    }

    // sample 2023-01-10 to Jumat, 01 Januari 2023
    public function convertTanggalDefault($value)
    {
        // Konversi format tanggal
        App::setLocale('id');
        $tanggalBaru = Carbon::parse($value)->isoFormat('dddd, D MMMM YYYY');
        return $tanggalBaru;
    }
    
}
