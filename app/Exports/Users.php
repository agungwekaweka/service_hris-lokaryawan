<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Users implements FromCollection,WithHeadings,WithColumnWidths,WithColumnFormatting
{
    public $param = array();

    function __construct($param) {
        $this->param = $param;
    }

    public function collection()
    {
        $idDepartemen = $this->param['id_departemen'];
        $idSubDepartemen = $this->param['id_sub_departemen'];

        $data_= DB::table('users')
            ->select(
            'users.departemen as departemen',
            'users.sub_departemen as subDepartemen',
            'users.grade as grade',
            'users.id_karyawan as id_karyawan',
            'users.nik as nik',
            'users.name as name',
            'users.approve as approve',
            'users.type_approve as type_approve')
            ->where('users.is_dell','1')
            ->orderBy('users.nik','asc');
            if($data_->exists())
            {
                if($idDepartemen != '')
                {
                     $data_->where('id_departemen',$idDepartemen);
                }
                if($idSubDepartemen!='')
                {
                    $data_->where('id_sub_departemen',$idSubDepartemen);
                }
                $data = $data_->get();
            }
        
            return $data;
    }
  
    public function headings(): array{
        return [
            [
                'DEPARTEMEN',
                'DEPARTEMEN SUB',
                'GRADE',
                'ID KARYAWAN',
                'NIK',
                'NAMA',
                'APPROVE',
                'TYPE APPROVE'
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 25,
            'C' => 15,   
            'D' => 15, 
            'E' => 15,
            'F' => 25, 
            'G' => 10, 
            'H' => 15
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
