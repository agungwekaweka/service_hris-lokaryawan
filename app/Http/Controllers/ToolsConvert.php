<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ToolsConvert extends Controller
{
    public function convertInputTypeDecimalOrInterger($inputNumber)
    {
        if (is_float($inputNumber) || is_numeric($inputNumber) && floor($inputNumber) != $inputNumber) {
            // Jika angka desimal, gunakan number_format
            $value = number_format($inputNumber, 1, '.', ',');
        } else {
            // Jika angka bukan desimal, gunakan format integer
            $value = number_format($inputNumber, 0, '.', ',');
        }
        return $value;
    }
}
