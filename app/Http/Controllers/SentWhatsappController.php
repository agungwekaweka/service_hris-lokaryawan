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
        return "[sent by Bot Loka]";
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
        "Note : \n".$note." \n\n";
        return $message;
    }

    private function getBodyIzin($request)
    {
        $namaPenerima=$request['nama_penerima'];
        $idIzin = $request['id_izin'];
        $namaRequest = $request['nama_request'];
        $departemen = $request['departemen'];
        $subDepartemen = $request['sub_departemen'];
        $typeIzin = $request['type_izin'];
        // Set the locale to Indonesian
        Carbon::setLocale('id');
        // Convert the date
        $tanggalIzin = Carbon::parse($request['tanggal_izin'])->translatedFormat('d F Y');
        $jadwalMasuk = $request['jadwal_masuk'];
        $jadwalPulang = $request['jadwal_pulang'];
        $perbaikanAbsenMasuk = $request['perbaikan_absen_masuk'];
        $perbaikanAbsenPulang = $request['perbaikan_absen_pulang'];
        $note = $request['note'];

        $message = "Notification From : ".$this->getNamaApps()." \n\n".
        "Dear Bapak/Ibu *".$namaPenerima."* \n".
        "Pengajuan Perbaikan Absen Nomor ".$idIzin.", dengan detail di bawah ini:"." \n\n".
        "Nama : *".$namaRequest."* \n".
        "Dept : ".$departemen." ( ".$subDepartemen." )"." \n\n".

        "Tanggal : ".$tanggalIzin." \n".
        "Jadwal Masuk : ".$jadwalMasuk." \n".
        "Jadwal Pulang : ".$jadwalPulang." \n".
        "Perbaikan Absen Masuk : ".$perbaikanAbsenMasuk." \n".
        "Perbaikan Absen Pulang : ".$perbaikanAbsenPulang." \n\n".
        "Tipe Perbaikan Absen : *".$typeIzin."* \n".
        "Keterangan : ".$note." \n\n";
        return $message;
    }

    private function getBodySppd($request)
    {
        $namaPenerima=$request['nama_penerima'];
        $idSppd = $request['id_sppd'];
        $namaRequest = $request['nama_request'];
        $departemen = $request['departemen'];
        $subDepartemen = $request['sub_departemen'];
        $city = $request['city'];
        $dateStart = $request['date_start'];
        $dateFinish = $request['date_finish'];
        $depatureTime = $request['depature_time'];
        $longDay = $request['long_day'];
        $night = $request['night'];
        $afterWorkTime = $request['after_work_time'];
        $note = $request['note'];

        $message = "Notification From : ".$this->getNamaApps()." \n\n".
        "Dear Bapak/Ibu *".$namaPenerima."* \n".
        "Pengajuan SPPD nomor : ".$idSppd." telah dibuat dengan detail sbb:"." \n\n".
        "Nama : \n*".$namaRequest."* \n\n".
        "Dept/Sub Dept : \n".$departemen." / ".$subDepartemen." \n\n\n".
        "--Perjalanan Dinas--"." \n\n".
        "Kota Tujuan : \n".$city." \n\n".
        "Tanggal Keberangakatan : \n".$dateStart." \n\n".
        "Jam Keberangkatan : \n".$depatureTime." \n\n".
        "Tanggal Pulang : \n".$dateFinish." \n\n".
        "Jam Pulang : \n".$afterWorkTime." \n\n".
        "Lama Hari : ".$longDay." (hari) ". $night." (malam)"." \n\n".
        "Note : \n".$note." \n\n";
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
        "Kode Booking : \n*".$kodeBooking ."* \n\n";
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
        "Mohon untuk dapat melakukan pengecekan dan *Approval/Reject* permintaan tersebut."." \n"."Matur Nuwun."." \n\n".
        "Link Aplikasi : https://lokaryawan.salokapark.app/notification". " \n".
        $this->getWatermarkFooter();
        $result_ = $this->sentWA($telephone,$message);
        return $result_;
    }

    // notif kepada HOD ada cuti khusus
    public function sentWhatsappApproveCutiKhusus($idCuti_,$telephone_,$idKaryawan_,$typeCuti_,$tanggalCuti_,$note_,$lampiran_)
    {
        // get Data User Request
        $c_users = new UsersController();
        $dtUser = $c_users->getData($idKaryawan_);

        $lampiran = 'https://servicelokaryawan.salokapark.app/storage/'.$lampiran_;

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
        "Mengajukan Cuti Khusus."." \n".
        "Lampiran : ".$lampiran." \n".
        "Matur Nuwun."." \n\n".
        "Link Aplikasi : https://lokaryawan.salokapark.app/notification". " \n".
        $this->getWatermarkFooter();
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

        // get Data User Recipient
        $c_users = new UsersController();
        $dtUserRecipient = $c_users->getDataByNoHp($telephone_);
        $namaPenerima = $dtUserRecipient->name;

        // declare variable
        $telephone = $telephone_;
        $message = $this->getBodyCuti($namaPenerima,$idCuti,$nama,$departemen,$subDepartemen,$typeCuti_,$tanggalCuti,$note).
        "Status Cuti : *DiReject*"." \n"."Matur Nuwun."." \n\n".
        "Link Aplikasi : https://lokaryawan.salokapark.app". " \n".
        $this->getWatermarkFooter();

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
         "Status Cuti : *Diterima*"." \n"."Matur Nuwun."." \n\n".
         "Link Aplikasi : https://lokaryawan.salokapark.app". " \n".
         $this->getWatermarkFooter();
         
         $result_ = $this->sentWA($telephone,$message);
         return $result_;
    }

# IZIN
    public function sentWhatsappApproveIzinReject($request)
    {
        $idKaryawan = $request['id_karyawan'];
        $idIzin = $request['id_izin'];
        $type= $request['type_izin'];
        # type Izin
        $typeIzin = $this->convertTypeIzin($type);
        # end type izin
        $tanggalIzin = $request['tanggal_izin'];
        $jadwalMasuk = $request['jadwal_masuk'];
        $jadwalPulang = $request['jadwal_pulang'];
        $perbaikanAbsenMasuk = $request['perbaikan_absen_masuk'];
        $perbaikanAbsenPulang = $request['perbaikan_absen_pulang'];
        $note = $request['note'];
        
        // get Data User Request
        $c_users = new UsersController();
        $dtUser = $c_users->getData($idKaryawan);

        $telephone = $dtUser->no_telephone;
        $departemen = $dtUser->departemen;
        $subDepartemen = $dtUser->sub_departemen;
        $grade = $dtUser->grade;
        $nama = $dtUser->name;

        // declare variable
        $request['nama_penerima'] = $nama;
        $request['id_izin'] = $idIzin;
        $request['nama_request'] = $nama;
        $request['departemen'] = $departemen;
        $request['sub_departemen'] = $subDepartemen;
        $request['type_izin'] = $typeIzin;
        $request['tanggal_izin'] = $tanggalIzin;
        $request['jadwal_masuk'] = $jadwalMasuk;
        $request['jadwal_pulang'] = $jadwalPulang;
        $request['perbaikan_absen_masuk'] = $perbaikanAbsenMasuk;
        $request['perbaikan_absen_pulang'] = $perbaikanAbsenPulang;
        $request['note'] = $note;
        
        // declare variable
        $message = $this->getBodyIzin($request).
        "Status Pengajuan : *Ditolak*"." \n"."Maturnuwun."." \n\n".
        "Kunjungi https://salokapark.app/salokaparkapp". " \n".
        $this->getWatermarkFooter();
       
        $result_ = $this->sentWA($telephone,$message);
        return $result_;
    }

    // notif kepada karyawan ketika Izin di terima
    public function sentWhatsappApproveIzinDone($request)
    {
            $idKaryawan = $request['id_karyawan'];
            $idIzin = $request['id_izin'];
            $type= $request['type_izin'];
            # type Izin
            $typeIzin = $this->convertTypeIzin($type);
            # end type izin
            $tanggalIzin = $request['tanggal_izin'];
            $jadwalMasuk = $request['jadwal_masuk'];
            $jadwalPulang = $request['jadwal_pulang'];
            $perbaikanAbsenMasuk = $request['perbaikan_absen_masuk'];
            $perbaikanAbsenPulang = $request['perbaikan_absen_pulang'];
            $note = $request['note'];
            
            // get Data User Request
            $c_users = new UsersController();
            $dtUser = $c_users->getData($idKaryawan);

            $telephone = $dtUser->no_telephone;
            $departemen = $dtUser->departemen;
            $subDepartemen = $dtUser->sub_departemen;
            $grade = $dtUser->grade;
            $nama = $dtUser->name;
    
            // declare variable
            $request['nama_penerima'] = $nama;
            $request['id_izin'] = $idIzin;
            $request['nama_request'] = $nama;
            $request['departemen'] = $departemen;
            $request['sub_departemen'] = $subDepartemen;
            $request['type_izin'] = $typeIzin;
            $request['tanggal_izin'] = $tanggalIzin;
            $request['jadwal_masuk'] = $jadwalMasuk;
            $request['jadwal_pulang'] = $jadwalPulang;
            $request['perbaikan_absen_masuk'] = $perbaikanAbsenMasuk;
            $request['perbaikan_absen_pulang'] = $perbaikanAbsenPulang;
            $request['note'] = $note;
            
            // declare variable
            $message = $this->getBodyIzin($request).
            "Status Pengajuan : *Diterima*"." \n"."Maturnuwun."." \n\n".
            "Kunjungi https://salokapark.app/salokaparkapp". " \n".
            $this->getWatermarkFooter();
             
            $result_ = $this->sentWA($telephone,$message);
            return $result_;
    }

    // notif kepada HOD ada request Izin
    public function sentWhatsappApproveIzin($request)
    {
        $idKaryawan = $request['id_karyawan'];
        $telephone = $request['telephone'];
        $idIzin = $request['id_izin'];      
        $type= $request['type_izin'];
     
        # type Izin
        $typeIzin = $this->convertTypeIzin($type);
        # end type izin
        $tanggalIzin = $request['tanggal_izin'];
        $jadwalMasuk = $request['jadwal_masuk'];
        $jadwalPulang = $request['jadwal_pulang'];
        $perbaikanAbsenMasuk = $request['perbaikan_absen_masuk'];
        $perbaikanAbsenPulang = $request['perbaikan_absen_pulang'];
        $note = $request['note'];

        // get Data User Request
        $c_users = new UsersController();
        $dtUser = $c_users->getData($idKaryawan);
     
        $idIzin = $idIzin;
        $departemen = $dtUser->departemen;
        $subDepartemen = $dtUser->sub_departemen;
        $grade = $dtUser->grade;
        $namarequest = $dtUser->name;
    
        // get Data User Recipient
        $c_users = new UsersController();
        $dtUserRecipient = $c_users->getDataByNoHp($telephone);
        $namaPenerima = $dtUserRecipient->name;
        
        // declare variable
        $request['nama_penerima'] = $namaPenerima;
        $request['id_izin'] = $idIzin;
        $request['nama_request'] = $namarequest;
        $request['departemen'] = $departemen;
        $request['sub_departemen'] = $subDepartemen;
        $request['type_izin'] = $typeIzin;
        $request['tanggal_izin'] = $tanggalIzin;
        $request['jadwal_masuk'] = $jadwalMasuk;
        $request['jadwal_pulang'] = $jadwalPulang;
        $request['perbaikan_absen_masuk'] = $perbaikanAbsenMasuk;
        $request['perbaikan_absen_pulang'] = $perbaikanAbsenPulang;
        $request['note'] = $note;
        $message = $this->getBodyIzin($request).
        "Mohon untuk dapat melakukan pengecekan dan *Approval/Reject* permintaan tersebut."." \n"."Maturnuwun."." \n\n".
        "Kunjungi https://salokapark.app/salokaparkapp". " \n".
        $this->getWatermarkFooter();
        $result_ = $this->sentWA($telephone,$message);
        return $result_;
    }

    private function convertTypeIzin($type)
    {
        $typeIzin='-';
        if($type=='1')
        {
            $typeIzin = 'Terlambat';
        }
        if($type=='2')
        {
            $typeIzin = 'Perbaikan Absen Masuk';
        }
        if($type=='3')
        {
            $typeIzin = 'Perbaikan Absen Pulang';
        }
        if($type=='4')
        {
            $typeIzin = 'Ganti Shift';
        }
        if($type=='5')
        {
            $typeIzin = 'Sakit';
        }
        if($type=='6')
        {
            $typeIzin = 'Keluar Urusan Kantor';
        }
        if($type=='7')
        {
            $typeIzin = 'Keluar Urusan Pribadi';
        }
        if($type=='8')
        {
            $typeIzin = 'Izin';
        }
        return $typeIzin;
    }
# END IZIN

    // notif kepada karyawan ketika SPPD di terima
    public function sentWhatsappApproveSppdDone($request)
    {
        $idKaryawan = $request['id_karyawan'];

        $idSppd = $request['id_sppd'];
        $city = $request['city'];
        $dateStart = $request['date_start'];
        $dateFinish = $request['date_finish'];
        $depatureTime = $request['depature_time'];
        $afterWorkTime = $request['after_work_time'];
        $longDay = $request['long_day'];
        $night = $request['night'];
        $note = $request['note'];
        
        // get Data User Request
        $c_users = new UsersController();
        $dtUser = $c_users->getData($idKaryawan);
    
        $telephone = $dtUser->no_telephone;
        $departemen = $dtUser->departemen;
        $subDepartemen = $dtUser->sub_departemen;
        $grade = $dtUser->grade;
        $namarequest = $dtUser->name;
    
        // get Data User Recipient
        $c_users = new UsersController();
        $dtUserRecipient = $c_users->getDataByNoHp($telephone);
        $namaPenerima = $dtUserRecipient->name;
        
        // declare variable
        $request['nama_penerima'] = $namaPenerima;
    
        $request['nama_request'] = $namarequest;
        $request['departemen'] = $departemen;
        $request['sub_departemen'] = $subDepartemen;
        $request['id_sppd'] = $idSppd;
        $request['city'] = $city;
        $request['date_start'] = $dateStart;
        $request['date_finish'] = $dateFinish;
        $request['depature_time'] = $depatureTime;
        $request['after_work_time'] = $afterWorkTime;
        $request['long_day'] = $longDay;
        $request['night'] = $night;
        $request['note'] = $note;
  
        $message = $this->getBodySppd($request).
        "Status SPPD : *Diterima*"." \n"."Matur Nuwun."." \n\n".
        "Link Aplikasi : https://salokapark.app". " \n".
        $this->getWatermarkFooter();
        $result_ = $this->sentWA($telephone,$message);

        return $result_;
    }
    
    // notif kepada HOD ada request SPPD
    public function sentWhatsappApproveSppd($request)
    {
        $idKaryawan = $request['id_karyawan'];
        $telephone = $request['telephone'];

        $idSppd = $request['id_sppd'];
        $city = $request['city'];
        $dateStart = $request['date_start'];
        $dateFinish = $request['date_finish'];
        $depatureTime = $request['depature_time'];
        $afterWorkTime = $request['after_work_time'];
        $longDay = $request['long_day'];
        $night = $request['night'];
        $note = $request['note'];

        // get Data User Request
        $c_users = new UsersController();
        $dtUser = $c_users->getData($idKaryawan);
    
        $departemen = $dtUser->departemen;
        $subDepartemen = $dtUser->sub_departemen;
        $grade = $dtUser->grade;
        $namarequest = $dtUser->name;
    
        // get Data User Recipient
        $c_users = new UsersController();
        $dtUserRecipient = $c_users->getDataByNoHp($telephone);
        $namaPenerima = $dtUserRecipient->name;
        
        // declare variable
        $request['nama_penerima'] = $namaPenerima;
    
        $request['nama_request'] = $namarequest;
        $request['departemen'] = $departemen;
        $request['sub_departemen'] = $subDepartemen;
        $request['id_sppd'] = $idSppd;
        $request['city'] = $city;
        $request['date_start'] = $dateStart;
        $request['date_finish'] = $dateFinish;
        $request['depature_time'] = $depatureTime;
        $request['after_work_time'] = $afterWorkTime;
        $request['long_day'] = $longDay;
        $request['night'] = $night;
        $request['note'] = $note;
        $message = $this->getBodySppd($request).
        "Mohon untuk dapat melakukan pengecekan dan *Approval/Reject* permintaan tersebut."." \n"."Matur Nuwun."." \n\n".
        "Link Aplikasi : https://salokapark.app/salokaparkapp". " \n".
        $this->getWatermarkFooter();
        $result_ = $this->sentWA($telephone,$message);
        return $result_;
    }
     
    // notif kepada karyawan ketika cuti khusus belum melampirakan file
    public function sentWhatsappNotifyLampiranCK($idCuti,$telephone_,$idKaryawan_,$typeCuti_,$tanggalCuti_,$note_)
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
          "*Silahkan Lengkapi Cuti Khusus Anda dengan melampirkan Dokumen pendukung Cuti Khusus.*"." \n"."Matur Nuwun."." \n\n".
          "Link Aplikasi : https://lokaryawan.salokapark.app". " \n".
          $this->getWatermarkFooter();
          
          $result_ = $this->sentWA($telephone,$message);
          return $result_;
    }
    
    //  OVERTIME-------------------------------------
    // karyawan Request
    public function sentWhatsappApproveOvertime($request)
    {
        $idOvertime = $request['id_overtime'];
        $telephone = $request['telephone'];
        $idKaryawan = $request['id_karyawan'];
        $tanggalLembur = $request['tanggal_lembur'];
        $jamLembur = $request['jam_lembur'];
        $keterangan = $request['keterangan'];

        // get Data User Request
        $c_users = new UsersController();
        $dtUser = $c_users->getData($idKaryawan);
        
        $departemen = $dtUser->departemen;
        $subDepartemen = $dtUser->sub_departemen;
        $grade = $dtUser->grade;
        $nama = $dtUser->name;
  
        // get Data User Recipient
        $c_users = new UsersController();
        $dtUserRecipient = $c_users->getDataByNoHp($telephone);
        $namaPenerima = $dtUserRecipient->name;

        // declare variable
        $message = $this->getBodyOvertime($namaPenerima,$idOvertime,$nama,$departemen,$subDepartemen,$tanggalLembur,$jamLembur,$keterangan).
        "Mohon untuk dapat melakukan pengecekan dan *Approval/Reject* permintaan tersebut."." \n"."Matur Nuwun."." \n\n".
        "Link Aplikasi : https://lokaryawan.salokapark.app/notification". " \n".
        $this->getWatermarkFooter();

        $result_ = $this->sentWA($telephone,$message);
        return $result_;
    }

    // HOD Request
    public function sentWhatsappApproveOvertimeKaryawan($request)
    {
        $idOvertime = $request['id_overtime'];
        $telephone = $request['telephone'];
        $idKaryawan = $request['id_karyawan'];
        $tanggalLembur = $request['tgl_lembur'];
        $jamLembur = $request['jam_lembur'];
        $keterangan = $request['keterangan'];

        // get Data User Request
        $c_users = new UsersController();
        $dtUser = $c_users->getData($idKaryawan);
        
        $departemen = $dtUser->departemen;
        $subDepartemen = $dtUser->sub_departemen;
        $grade = $dtUser->grade;
        $nama = $dtUser->name;
  
        // get Data User Recipient
        $c_users = new UsersController();
        $dtUserRecipient = $c_users->getDataByNoHp($telephone);
        $namaPenerima = $dtUserRecipient->name;

        // declare variable
        $message = $this->getBodyOvertime($namaPenerima,$idOvertime,$nama,$departemen,$subDepartemen,$tanggalLembur,$jamLembur,$keterangan).
        "Mohon untuk dapat melakukan pengecekan dan *Approval/Reject* permintaan tersebut."." \n"."Matur Nuwun."." \n\n".
        "Link Aplikasi : https://lokaryawan.salokapark.app/notification". " \n".
        $this->getWatermarkFooter();

        $result_ = $this->sentWA($telephone,$message);
        return $result_;
    }

    public function sentWhatsappOvertimeDiterima($request)
    {
        $idOvertime = $request['id_overtime'];
        $telephone = $request['telephone'];
        $idKaryawan = $request['id_karyawan'];
        $tanggalLembur = $request['tanggal_lembur'];
        $jamLembur = $request['jam_lembur'];
        $keterangan = $request['keterangan'];

        // get Data User Request
        $c_users = new UsersController();
        $dtUser = $c_users->getData($idKaryawan);
        
        $departemen = $dtUser->departemen;
        $subDepartemen = $dtUser->sub_departemen;
        $grade = $dtUser->grade;
        $nama = $dtUser->name;

        // get Data User Recipient
        $c_users = new UsersController();
        $dtUserRecipient = $c_users->getDataByNoHp($telephone);
        $namaPenerima = $dtUserRecipient->name;

        $message = $this->getBodyOvertime($namaPenerima,$idOvertime,$nama,$departemen,$subDepartemen,$tanggalLembur,$jamLembur,$keterangan).
        "Status Overtime : Diterima"." \n"."Matur Nuwun."." \n\n".
        "Link Aplikasi : https://lokaryawan.salokapark.app". " \n".
        $this->getWatermarkFooter();

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
        "Status Overtime : *DiReject*"." \n"."Matur Nuwun."." \n\n".
        "Link Aplikasi : https://lokaryawan.salokapark.app". " \n".
        $this->getWatermarkFooter();

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
        $dtKomplement = $c_komplementController->getRequestKomplemenKaryawan($idKaryawan_,$idKomplement,'','','','','');
  
        $tanggalPengajuan = $dtKomplement[0]->tanggal_pengajuan;
        $c_toolsDateClass = new ToolsDateClass();
        $tanggalKedatangan = $c_toolsDateClass->convertTanggalDefault($dtKomplement[0]->tanggal_kedatangan);
        $totalTiket = $dtKomplement[0]->qty_total;
        $kodeBooking = $dtKomplement[0]->kode_booking;

        // declare variable
        $message = $this->getBodyTicket($namaPenerima,$idKomplement,$tanggalPengajuan,$tanggalKedatangan,$totalTiket,$kodeBooking).
        "Silahkan Cetak Tiket Melalui KIOSK, Matur Nuwun."." \n\n".
        "Link Aplikasi : https://lokaryawan.salokapark.app". " \n".
        $this->getWatermarkFooter();

        $result_ = $this->sentWA($telephone,$message);
        return $result_;
    }
    
    public function sentWhatsappKodeBookingTicketGuest($request)
    {
        $namaPenerima = $request['nama_penerima'];
        $idKomplement = $request['id_komplement_trn'];
        $tanggalPengajuan = $request['tanggal_pengajuan'];
        $tanggalKedatangan = $request['tanggal_kedatangan'];
        $qty = $request['qty'];
        $kodeBooking = $request['kode_booking'];
        $telephone = $request['telephone'];
       
        // declare variable
        $message = $this->getBodyTicket($namaPenerima,$idKomplement,$tanggalPengajuan,$tanggalKedatangan,$qty,$kodeBooking).
        "Silahkan Cetak Tiket Melalui KIOSK, Matur Nuwun."." \n\n".
        "Link Aplikasi : https://lokaryawan.salokapark.app". " \n".
        $this->getWatermarkFooter();

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
        "Silahkan Cek Kembali Pembayaran Anda, Matur Nuwun."." \n\n".
        "Link Aplikasi : https://lokaryawan.salokapark.app". " \n".
        $this->getWatermarkFooter();

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

    public function sentBlastWAAllKaryawan(Request $request)
    {
        try
        {
            $message = 'Tes';
            // get All Data Karywan
            $dtKaryawanActive = DB::table('users')
            ->select('no_telephone')
            ->where('is_dell','1')
            ->orderBy('no_telephone','desc')
            ->get();
         
            foreach($dtKaryawanActive as $v)
            {
                $telephone = $v->no_telephone;
                $c_apiGuzzle = new API_Guzzle();
                $result_ = $c_apiGuzzle->getServiceWhatsapp($telephone,$message);
            }
            return 'success';
        } catch (\Exception $ex) {
            return $ex;
        }
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
