<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;

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
    
}
