<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Users implements FromView,WithHeadings,WithColumnWidths,WithColumnFormatting
{
    protected $param = array();

    function __construct() {
            // $this->param = $param;
    }

    public function view(): View
    {
            $data['karyawan']= DB::table('users')
            ->select('users.id as id',
            'users.id_departemen as idDepartemen',
            'departemen.departemen as departemen',
            'users.id_departemen_sub as idDepartemenSub',
            'departemen_sub.sub_departemen as subDepartemen',
            'users.pos as pos',
            'users.id_grade as id_grade',
            'users.grade as grade',
            'users.id_absen as idAbsen',
            'users.username as nik',
            'users.name as name',
            'users.email as email',
            'users.no_hp as no_hp',
            'users.id_skema_hari_kerja as idSkemaHariKerja',
            'skema_hari_kerja.skema as skemaHariKerja',
            'skema_hari_kerja.jml_hari as jmlHari',
            'skema_hari_kerja.jam_kerja as jamkerja',
            'users.doj as doj',
            'users.dob as dob',
            'users.id_absen as username')
            ->where('users.status','1')
            ->orderBy('users.username','asc')
            ->get();

        return view('dashboard.master-data.user-management.export', $data);
    }
  
    public function headings(): array{
        return [
            [
                'ID',
                'ID DEPARTEMEN',
                'DEPARTEMEN',
                'ID DEPARTEMEN SUB',
                'DEPARTEMEN SUB',
                'POS',
                'ID GRADE',
                'GRADE',
                'ID ABSEN',
                'NIP',
                'NAME',
                'EMAIL',
                'NO HP',
                'ID SKEMA HARI KERJA',
                'SKEMA HARI KERJA',
                'JML HARI KERJA',
                'JAM KERJA',
                'TANGGAL BERGABUNG',
                'TANGGAL LAHIR',
                'USERNAME',
                'PASSWORD'
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 4,
            'B' => 15,
            'C' => 25,
            'D' => 20,   
            'E' => 25, 
            'F' => 20,
            'G' => 15, 
            'H' => 15, 
            'I' => 10,    
            'J' => 15,
            'K' => 40,
            'L' => 40,
            'M' => 20,
            'N' => 20,
            'O' => 20,
            'P' => 20,
            'Q' => 20,
            'R' => 20,
            'S' => 20,
            'T' => 20,
            'U' => 20,
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
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
            'K' => NumberFormat::FORMAT_TEXT,
            'L' => NumberFormat::FORMAT_TEXT,
            'M' => NumberFormat::FORMAT_TEXT,
            'N' => NumberFormat::FORMAT_TEXT,
            'O' => NumberFormat::FORMAT_TEXT,
            'P' => NumberFormat::FORMAT_TEXT,
            'Q' => NumberFormat::FORMAT_TEXT,
            'R' => NumberFormat::FORMAT_TEXT,
            'S' => NumberFormat::FORMAT_TEXT,
            'T' => NumberFormat::FORMAT_TEXT,
            'U' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
