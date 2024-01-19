<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

class SentWhatsappController extends Controller
{

    private function getNamaApps()
    {
        return '[HRIS-LOKARYAWAN]';
    }

    private function getWatermarkFooter()
    {
        $message = "https://lokaryawan.salokapark.app/notification". " \n\n".
        "[sent by Bot Loka]";

        return $message;
    }

    private function getBodyCuti($namaPenerima,$idCuti,$nama,$departemen,$subDepartemen,$typeCuti,$tanggalCuti,$note)
    {
        $message = "Notification From : ".$this->getNamaApps()." \n\n".
        "Dear Bapak/Ibu *".$namaPenerima."* \n".
        "Pengajuan cuti nomor : ".$idCuti." telah dibuat dengan detail sbb:"." \n\n".
        "Nama : \n*".$nama."* \n\n".
        "Dept/Sub Dept : \n".$departemen." / ".$subDepartemen." \n\n".
        "Tipe Cuti : \n".$typeCuti." \n\n".
        "Tanggal Cuti : \n".$tanggalCuti." \n\n".
        "Note : \n".$note." \n\n\n";
        return $message;
    }

    private function getBodyOvertime($namaPenerima,$idOvertime,$nama,$departemen,$subDepartemen,$tanggalLembur,$jamLembur,$keterangan)
    {
        $message = "Notification From : ".$this->getNamaApps()." \n\n".
        "Dear Bapak/Ibu *".$namaPenerima."* \n".
        "Pengajuan Lembur nomor : ".$idOvertime." telah dibuat dengan detail sbb:"." \n\n".
        "Nama : \n*".$nama."* \n\n".
        "Dept/Sub Dept : \n".$departemen." / ".$subDepartemen." \n\n".
        "Tanggal Lembur : \n".$tanggalLembur." \n\n".
        "Jam Lembur : \n".$jamLembur ." jam"." \n\n".
        "Note : \n".$keterangan." \n\n\n";
        return $message;
    }

    private function getBodyTicket($namaPenerima,$idKomplement,$tanggalPengajuan,$tanggalKedatangan,$totalTiket,$kodeBooking)
    {
        $message = "Notification From : ".$this->getNamaApps()." \n\n".
        "Dear Bapak/Ibu *".$namaPenerima."* \n".
        "Pengajuan Tiket Komplement nomor : ".$idKomplement." telah dibuat dengan detail sbb:"." \n\n".
        "Tanggal Pengajuan : \n*".$tanggalPengajuan."* \n\n".
        "Tanggal Kedatangan : \n*".$tanggalKedatangan."* \n\n".
        "Total Tiket : \n*".$totalTiket."* \n\n".
        "Kode Booking : \n*".$kodeBooking ."* \n\n".
        "Silahkan Cetak Tiket Melalui KIOSK, Matur Nuwum."." \n\n".$this->getWatermarkFooter();
        return $message;
    }

    // notif kepada HOD ada request cuti masuk
    public function sentWhatsappApproveCuti($idCuti_,$telephone_,$idKaryawan_,$typeCuti_,$tanggalCuti_,$note_)
    {
        // get Data User Request
        $c_users = new UsersController();
        $dtUser = $c_users->getData($idKaryawan_);

        $idCuti = $idCuti_;
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
        $message = $this->getBodyCuti($namaPenerima,$idCuti,$nama,$departemen,$subDepartemen,$typeCuti_,$tanggalCuti,$note).
        "Mohon untuk dapat melakukan pengecekan dan *Approval/Reject* permintaan tersebut."." \n"."Matur Nuwum."." \n\n".$this->getWatermarkFooter();
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
        $message = $this->getBodyCuti($namaPenerima,$idCuti,$nama,$departemen,$subDepartemen,$typeCuti_,$tanggalCuti,$note).
        "*DiReject*"." \n"."Matur Nuwum."." \n\n".$this->getWatermarkFooter();

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
         $message = $this->getBodyCuti($namaPenerima,$idCuti,$nama,$departemen,$subDepartemen,$typeCuti_,$tanggalCuti,$note).
         "Diterima"." \n"."Matur Nuwum."." \n\n".$this->getWatermarkFooter();
         
         $result_ = $this->sentWA($telephone,$message);
         return $result_;
     }
    
    //  OVERTIME-------------------------------------
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
        $message = $this->getBodyOvertime($namaPenerima,$idOvertime,$nama,$departemen,$subDepartemen,$tanggalLembur,$jamLembur,$keterangan).
        "Mohon untuk dapat melakukan pengecekan dan *Approval/Reject* permintaan tersebut."." \n"."Matur Nuwum."." \n\n".$this->getWatermarkFooter();

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
        $telephone = $telephone_;
        $message = $this->getBodyOvertime($namaPenerima,$idOvertime,$nama,$departemen,$subDepartemen,$tanggalLembur,$jamLembur,$keterangan).
        "Diterima"." \n"."Matur Nuwum."." \n\n".$this->getWatermarkFooter();

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
        $message = $this->getBodyOvertime($namaPenerima,$idOvertime,$nama,$departemen,$subDepartemen,$tanggalLembur,$jamLembur,$keterangan).
        "*DiReject*"." \n"."Matur Nuwum."." \n\n".$this->getWatermarkFooter();

        $result_ = $this->sentWA($telephone,$message);
        return $result_;
    }

    public function sentWhatsappKodeBookingTicket($idKomplement,$idKaryawan_)
    {
        // get Data User Recipient
        $c_users = new UsersController();
        $dtUserRecipient = $c_users->getData($idKaryawan_);
        $namaPenerima = $dtUserRecipient->name;
        $telephone = $dtUserRecipient->no_telephone;

        // get Data Komplement
        $c_komplementController = new KomplementController();
        $dtKomplement = $c_komplementController->getRequestKomplemenKaryawan($idKaryawan_,$idKomplement);
  
        $tanggalPengajuan = $dtKomplement[0]->tanggal_pengajuan;
        $c_toolsDateClass = new ToolsDateClass();
        $tanggalKedatangan = $c_toolsDateClass->convertTanggalDefault($dtKomplement[0]->tanggal_kedatangan);
        $totalTiket = $dtKomplement[0]->qty_total;
        $kodeBooking = $dtKomplement[0]->kode_booking;

        // declare variable
        $message = $this->getBodyTicket($namaPenerima,$idKomplement,$tanggalPengajuan,$tanggalKedatangan,$totalTiket,$kodeBooking).
        "Silahkan Cetak Tiket Melalui KIOSK, Matur Nuwum."." \n\n".$this->getWatermarkFooter();

        $result_ = $this->sentWA($telephone,$message);
        return $result_;
    }

    public function sentWhatsappErrorTicket($idKomplement,$idKaryawan_,$status)
    {
        // get Data User Recipient
        $c_users = new UsersController();
        $dtUserRecipient = $c_users->getData($idKaryawan_);
        $namaPenerima = $dtUserRecipient->name;
        $telephone = $dtUserRecipient->no_telephone;

        // get Data Komplement
        $c_komplementController = new KomplementController();
        $dtKomplement = $c_komplementController->getRequestKomplemenKaryawan($idKaryawan_,$idKomplement);
  
        $tanggalPengajuan = $dtKomplement[0]->tanggal_pengajuan;
        $c_toolsDateClass = new ToolsDateClass();
        $tanggalKedatangan = $c_toolsDateClass->convertTanggalDefault($dtKomplement[0]->tanggal_kedatangan);
        $totalTiket = $dtKomplement[0]->qty_total;
        $kodeBooking = $dtKomplement[0]->kode_booking;

        // declare variable
        $message = $this->getBodyTicket($namaPenerima,$idKomplement,$tanggalPengajuan,$tanggalKedatangan,$totalTiket,$kodeBooking).
        "Silahkan Cek Kembali Pembayaran Anda, Matur Nuwum."." \n\n".$this->getWatermarkFooter();

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

    public function sentWAtoDeveloper($message)
    {
        // sent to developer
        $telephone ='6285941304991';
        $c_apiGuzzle = new API_Guzzle();
        $result_ = $c_apiGuzzle->getServiceWhatsapp($telephone,$message);
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
