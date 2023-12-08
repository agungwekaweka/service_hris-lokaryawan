<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

class SentWhatsappController extends Controller
{
    // notif kepada HOD ada request cuti masuk
    public function sentWhatsappApproveCuti($idCuti,$telephone_,$idKaryawan_,$typeCuti_,$tanggalCuti_,$note_)
    {
        // get Data User Request
        $c_users = new UsersController();
        $dtUser = $c_users->getData($idKaryawan_);

        $departemen = $dtUser->departemen;
        $subDepartemen = $dtUser->sub_departemen;
        $grade = $dtUser->grade;
        $nama = $dtUser->name;
        $tanggalCuti = $this->convertTanggal($tanggalCuti_);
        $note = $note_;

        // get Data User Recipient
        $c_users = new UsersController();
        $dtUserRecipient = $c_users->getDataByNoHp($telephone_);

        $namaPenerima = $dtUserRecipient->name;
    

        // declare variable
        $telephone = $telephone_;
        $message = "Dear Bapak/Ibu *".$namaPenerima."* \n".
        "Pengajuan cuti nomor : ".$idCuti." telah dibuat dengan detail sbb:"." \n\n".
        "Nama : \n*".$nama."* \n\n".
        "Dept/Sub Dept : \n".$departemen." / ".$subDepartemen." \n\n".
        "Tipe Cuti : \n".$typeCuti_." \n\n".
        "Tanggal Cuti : \n".$tanggalCuti." \n\n".
        "Note : \n".$note." \n\n\n".
        "Mohon untuk dapat melakukan pengecekan dan *Approval/Reject* permintaan tersebut."." \n"."Matur Nuwum."." \n\n"."[sent by Bot Loka]";

        $result_ = $this->sentWA($telephone,$message);
        return $result_;
    }

    // notif kepada karyawan ketika cutinya di tolak
    public function sentWhatsappApproveCutiCancel($idCuti,$telephone_,$idKaryawan_,$typeCuti_,$tanggalCuti_,$note_)
    {
        // get Data User Request
        $c_users = new UsersController();
        $dtUser = $c_users->getData($idKaryawan_);

        $departemen = $dtUser->departemen;
        $subDepartemen = $dtUser->sub_departemen;
        $grade = $dtUser->grade;
        $nama = $dtUser->name;
        $tanggalCuti = $this->convertTanggal($tanggalCuti_);
        $note = $note_;

        // declare variable
        $telephone = $telephone_;
        $message = "Dear Bapak/Ibu *".$nama."* \n".
        "Pengajuan cuti nomor : ".$idCuti." telah dibuat dengan detail sbb:"." \n\n".
        "Nama : \n".$nama." \n\n".
        "Dept/Sub Dept : \n".$departemen." / ".$subDepartemen." \n\n".
        "Tipe Cuti : \n".$typeCuti_." \n\n".
        "Tanggal Cuti : \n".$tanggalCuti." \n\n".
        "Note : \n".$note." \n\n\n".
        "*DiReject*"." \n"."Matur Nuwum."." \n\n"."[sent by Bot Loka]";

        $result_ = $this->sentWA($telephone,$message);
        return $result_;
    }

     // notif kepada karyawan ketika cutinya di terima
     public function sentWhatsappApproveCutiDiterima($idCuti,$telephone_,$idKaryawan_,$typeCuti_,$tanggalCuti_,$note_)
     {
         // get Data User Request
         $c_users = new UsersController();
         $dtUser = $c_users->getData($idKaryawan_);
 
         $departemen = $dtUser->departemen;
         $subDepartemen = $dtUser->sub_departemen;
         $grade = $dtUser->grade;
         $nama = $dtUser->name;
         $tanggalCuti = $this->convertTanggal($tanggalCuti_);
         $note = $note_;
 
         // get Data User Recipient
         $c_users = new UsersController();
         $dtUserRecipient = $c_users->getDataByNoHp($telephone_);
         $namaPenerima = $dtUserRecipient->name;

         // declare variable
         $telephone = $telephone_;
         $message = "Dear Bapak/Ibu *".$namaPenerima."* \n".
            
         "Pengajuan cuti nomor : ".$idCuti." telah dibuat dengan detail sbb:"." \n\n".
         "Nama : \n".$nama." \n\n".
         "Dept/Sub Dept : \n".$departemen." / ".$subDepartemen." \n\n".
         "Tipe Cuti : \n".$typeCuti_." \n\n".
         "Tanggal Cuti : \n".$tanggalCuti." \n\n".
         "Note : \n".$note." \n\n\n".
         "Diterima"." \n"."Matur Nuwum."." \n\n"."[sent by Bot Loka]";
         
         $result_ = $this->sentWA($telephone,$message);
         return $result_;
     }
    
    //  Overtime
    public function sentWhatsappApproveOvertime($idOvertime_,$telephone_,$idKaryawan_,$tanggalLembur_,$jamLembur_,$keterangan_)
    {
        // get Data User Request
        $c_users = new UsersController();
        $dtUser = $c_users->getData($idKaryawan_);

        $idOvertime = $idOvertime_;
        $tanggalLembur = $tanggalLembur_;
        $jamLembur = $jamLembur_;
        $keterangan = $keterangan_;
        
        $departemen = $dtUser->departemen;
        $subDepartemen = $dtUser->sub_departemen;
        $grade = $dtUser->grade;
        $nama = $dtUser->name;

        // get Data User Recipient
        $c_users = new UsersController();
        $dtUserRecipient = $c_users->getDataByNoHp($telephone_);
        $namaPenerima = $dtUserRecipient->name;

        // declare variable
        $telephone = $telephone_;
        $message = "Dear Bapak/Ibu *".$namaPenerima."* \n".
        "Pengajuan Lembur nomor : ".$idOvertime." telah dibuat dengan detail sbb:"." \n\n".
        "Nama : \n*".$nama."* \n\n".
        "Dept/Sub Dept : \n".$departemen." / ".$subDepartemen." \n\n".
        "Tanggal Lembur : \n".$tanggalLembur." \n\n".
        "Jam Lembur : \n".$jamLembur ." jam"." \n\n".
        "Note : \n".$keterangan." \n\n\n".
        "Mohon untuk dapat melakukan pengecekan dan *Approval/Reject* permintaan tersebut."." \n"."Matur Nuwum."." \n\n"."[sent by Bot Loka]";

        $result_ = $this->sentWA($telephone,$message);
        return $result_;
    }

    public function sentWhatsappOvertimeDiterima($idOvertime_,$telephone_,$idKaryawan_,$tanggalLembur_,$jamLembur_,$keterangan_)
    {
        // get Data User Request
        $c_users = new UsersController();
        $dtUser = $c_users->getData($idKaryawan_);

        $idOvertime = $idOvertime_;
        $tanggalLembur = $tanggalLembur_;
        $jamLembur = $jamLembur_;
        $keterangan = $keterangan_;
        
        $departemen = $dtUser->departemen;
        $subDepartemen = $dtUser->sub_departemen;
        $grade = $dtUser->grade;
        $nama = $dtUser->name;

        // get Data User Recipient
        $c_users = new UsersController();
        $dtUserRecipient = $c_users->getDataByNoHp($telephone_);
        $namaPenerima = $dtUserRecipient->name;

        // declare variable
        $telephone = $telephone_;
        $message = "Dear Bapak/Ibu *".$namaPenerima."* \n".
        "Pengajuan Lembur nomor : ".$idOvertime." telah dibuat dengan detail sbb:"." \n\n".
        "Nama : \n*".$nama."* \n\n".
        "Dept/Sub Dept : \n".$departemen." / ".$subDepartemen." \n\n".
        "Tanggal Lembur : \n".$tanggalLembur." \n".
        "Jam Lembur : \n".$jamLembur ." jam"." \n\n".
        "Note : \n".$keterangan." \n\n\n".
        "Mohon untuk dapat melakukan pengecekan dan *Approval/Reject* permintaan tersebut."." \n"."Matur Nuwum."." \n\n"."[sent by Bot Loka]";

        $result_ = $this->sentWA($telephone,$message);
        return $result_;
    }

    public function sentWhatsappOvertimeDiTolak($idOvertime_,$telephone_,$idKaryawan_,$tanggalLembur_,$jamLembur_,$keterangan_)
    {
        // get Data User Request
        $c_users = new UsersController();
        $dtUser = $c_users->getData($idKaryawan_);

        $idOvertime = $idOvertime_;
        $tanggalLembur = $tanggalLembur_;
        $jamLembur = $jamLembur_;
        $keterangan = $keterangan_;
        
        $departemen = $dtUser->departemen;
        $subDepartemen = $dtUser->sub_departemen;
        $grade = $dtUser->grade;
        $nama = $dtUser->name;

        // get Data User Recipient
        $c_users = new UsersController();
        $dtUserRecipient = $c_users->getDataByNoHp($telephone_);
        $namaPenerima = $dtUserRecipient->name;

        // declare variable
        $telephone = $telephone_;
        $message = "Dear Bapak/Ibu *".$namaPenerima."* \n".
        "Pengajuan Lembur nomor : ".$idOvertime." telah dibuat dengan detail sbb:"." \n\n".
        "Nama : \n*".$nama."* \n\n".
        "Dept/Sub Dept : \n".$departemen." / ".$subDepartemen." \n\n".
        "Tanggal Lembur : \n".$tanggalLembur." \n\n".
        "Jam Lembur : \n".$jamLembur ." jam"." \n\n".
        "Note : \n".$keterangan." \n\n\n".
        "*DiReject*"." \n"."Matur Nuwum."." \n\n"."[sent by Bot Loka]";

        $result_ = $this->sentWA($telephone,$message);
        return $result_;
    }

    // ---------------------------------------------------------
    private function sentWA($telephone,$message)
    {
        $c_apiGuzzle = new API_Guzzle();
        $result_ = $c_apiGuzzle->getServiceWhatsapp($telephone,$message);
        return $result_;
    }

    private function convertTanggal($jsonTanggal)
    {
        $jsonDecode = json_decode($jsonTanggal);
        $tanggal='';
        
        foreach($jsonDecode as $v)
        {
            $tanggalDariDatabase = $v;

            // Konversi format tanggal
            App::setLocale('id');
            $tanggalBaru = Carbon::parse($tanggalDariDatabase)->isoFormat('dddd, D MMMM YYYY');
            // Jumat, 29 November 2024
            $tanggal = $tanggal.$tanggalBaru." \n"; 
        }
        return $tanggal;
    }
    
}
