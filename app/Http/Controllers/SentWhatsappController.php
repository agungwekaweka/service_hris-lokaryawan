<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class SentWhatsappController extends Controller
{
    public function sentWhatsappApproveCuti($telephone_,$idKaryawan_,$typeCuti_,$tanggalCuti_,$note_)
    {
        // get Data User Request
        $c_users = new UsersController();
        $dtUser = $c_users->getData($idKaryawan_);

        $departemen = $dtUser->departemen;
        $subDepartemen = $dtUser->sub_departemen;
        $grade = $dtUser->grade;
        $nama = $dtUser->name;
        $tanggalCuti = $tanggalCuti_;
        $note = $note_;

        // declare variable
        $telephone = $telephone_;
        $message = "*Ada Request Cuti Masuk*, \n".
        "Type Cuti : ".$typeCuti_." \n\n".
        $departemen."-".$subDepartemen."\n *".
        $nama."*  \n\n".
        "*Request Tanggal Cuti* :  \n".$tanggalCuti ." \nNote : ".$note." \n\n"."https://lokaryawan.salokapark.app/notification";

        $c_apiGuzzle = new API_Guzzle();
        $result_ = $c_apiGuzzle->getServiceWhatsapp($telephone,$message);
        return $result_;
    }
}
