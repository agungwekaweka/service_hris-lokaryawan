<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

use App\Http\Controllers\OvertimeController;

class RequestOvertime implements FromView,WithColumnFormatting,WithColumnWidths
{
    public $param = array();

    function __construct($param) {
        $this->param = $param;
    }

    public function view(): View
    {
        try
        {
            $overtimeController = new OvertimeController();
            $data['data'] = $overtimeController->getOvertimeRequest($this->param);
            return view('Export.export-requestOvertime', $data);
        } catch (\Exception $ex) {
            dd($ex);
        }
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 20,
            'C' => 25,
            'D' => 25,   
            'E' => 15, 
            'F' => 25,
            'G' => 30, 
            'H' => 20, 
            'I' => 20,    
            'J' => 15,
            'K' => 15,
            'L' => 15,
            'M' => 15,
            'N' => 15,
            'O' => 15,
            'P' => 15,
            'Q' => 15,
            'R' => 30,
            'S' => 10,
            
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT
        ];
    }
}
