<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use DateTime;
use App\Http\Controllers\ClassUploadImageClass;

class KaryawanController extends Controller
{
    // memasukkan semua data karyawan dari lokahr ke lokaryawan (master cuti & master komplemen)
    public function insertKaryawan() {        
        try {
            // get service API
            $typeService = 'get_all_karyawan';
            $json_data = new API_Guzzle();
            $data_jsonDecode = $json_data->getServiceLokaHR($typeService);
        
            // cek status API
            if($data_jsonDecode->status=='success')
            {
                // karyawan Acvie
                $lstEmpActive = $data_jsonDecode->karyawanActive;
                foreach($lstEmpActive as $v)
                {
                    $idDepartemen = $v->id_departemen;
                    $departemen = $v->departemen;
                    $idSubDepartemen = $v->id_departemen_sub;
                    $subDepartemen = $v->sub_departemen;
                    $idGrade = $v->id_grade;
                    $grade = $v->grade;
                    $username = $v->username;
                    $name = $v->name;
                    $noTelephone = $v->no_hp;
                    $password = $v->password;
                    $idAbsen = $v->id_absen;
                    $isDell = $v->status;
                    $doj = $v->doj;
                    $dob = $v->dob;

                    // cek tanggal join sudah memenuhi atau belum
                    $toDate = Carbon::now();
                    $fromDate = Carbon::parse($doj);
                    $months = $toDate->diffInMonths($fromDate);
                    
                    // if($months < 11)
                    // {
                    //     continue;
                    // }

                    // insert table user
                    $result_['karyawn_active'][0] = $this->insertUser($idDepartemen,$departemen, $idSubDepartemen,$subDepartemen,$idGrade, $grade, $name,$noTelephone, $idAbsen, $username, $password, $isDell,$doj,$dob);
              
                    $tahun = Carbon::now()->format('Y');
                    // get tipe Cuti
                    $c_cuti = new CutiController();
                    $lstMstCuti = $c_cuti->getTypeMasterCuti();
                    foreach($lstMstCuti as $v)
                    {
                        $idCuti = $v->id_cuti;
                        $tipeCuti = $v->tipe_cuti;
                        $cuti = $v->cuti;
                        $jmlHari = $v->jml_hari;
                        $masaBerlaku = $v->masa_berlaku;
                        $tipeMasaBerlaku = $v->tipe_masa_berlaku;
                     
                        // insert Master Cuti
                       $result_['karyawn_active'][1]= $this->insertCuti($idCuti, $tahun,$idAbsen,$tipeCuti,$cuti,$jmlHari,$tipeMasaBerlaku,$masaBerlaku,$doj);
                    }
                    
                    // get master komplement
                    $c_komplement = new KomplementController();
                    $lstMstKomplemen = $c_komplement->getTypeMasterKomplemen();
             
                    foreach($lstMstKomplemen as $v)
                    {
                    
                        $idKomplement = $v->id_komplement;
                        $tipeKomplement = $v->komplement;
                        $qty = $v->qty;

                        $c_generateID = new GenerateIDController();
                        $idKomplemenMst = $c_generateID->getIDKomplemenMst($idKomplement);

                        // insert Master Komplemen
                        $result_['karyawn_active'][2] = $this->insertKomplementMst($idKomplemenMst,$idKomplement, $tahun,$idAbsen,$tipeKomplement,$qty,$qty);
                    }     
                }

                // karyawan Non Acvie
                $lstEmpNonActive = $data_jsonDecode->karyawanNonActive;

                foreach($lstEmpNonActive as $v)
                {
                    $username = $v->username;
                    $idAbsen = $v->id_absen;
                    $name = $v->name;
                    $password = $v->password;

                    // disable Master Cuti
                    $result_['karyawn_nonActive'] = $this->disableDataKaryawan($idAbsen);
                }
                
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Insert Data Karyawan Successfuly',
                    'callback' => $result_
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'API Error'
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // karyawan melakukan request cuti
    public function requestCuti(Request $request) {  
        $idKaryawan = $request->id_karyawan;
        $idCuti = $request->id_cuti;
        $tipeCuti = $request->tipe_cuti;
        $cuti = $request->cuti;
        $tanggal= $request->tanggal;
        $totalCuti = $request->total_cuti;
        $keterangan = $request->keterangan;
    
        try {
            // insert cuti
            $tahun = Carbon::now()->format('Y');
            $idPeriode = '-';
            $tglPengajuan = Carbon::now()->format('Y-m-d H:i:s');
            // status 0 = pending HOD, 1= approve, 2=reject
            $status = '0';
          
            // $tanggal = json_encode($tanggal_);
            $note = '-';
           
            $sisaCuti =0;
            // cek sisa cuti in Master
            $c_cuti = new CutiController();
            $dt = $c_cuti->getCutiKaryawan($idKaryawan, $tahun,$idCuti);

            foreach($dt as $x)
            {
        
                $idMst = $x->id_cuti_mst;
                $sisaCutiDB = $x->sisa_cuti;
                break;
            }
            // $c_toolsDateClass = new ToolsDateCLass();
            // $isRangeDate = $c_toolsDateClass->checkDateRange($tanggal,$dateStart,$dateEnd);
         
            if($sisaCutiDB >= $totalCuti)
            {  
                // insert cuti
                $c_generateID = new GenerateIDController();
                $idTrn = $c_generateID->getIDCutiTrn($idCuti);

                $req = $this->insertCutiTRN($idMst,$idTrn,$idCuti, $idPeriode,$tahun,$idKaryawan,$tipeCuti,$cuti,$tanggal,$totalCuti,$keterangan,$tglPengajuan,$status);
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Request Cuti Karyawan Successfuly',
                    'callback' => $req
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'Sisa Cuti Anda sudah Tidak Mencukupi, Cuti Tersisa : '.$sisaCutiDB,
                ]);
            }

            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }
    
    // get sisa cuti karyawan
    public function getCutiKaryawan(Request $request) {  
        $idKaryawan = $request->id_karyawan;
        $tahun = $request->tahun;
        $idCuti = $request->id_cuti;
        try {
            $c_cuti = new CutiController();
            $req = $c_cuti->getCutiKaryawan($idKaryawan,$tahun,$idCuti);
            $result=response()->json([
                'status' => 'success',
                'message' => 'Get Data Cuti Karyawan Successfuly',
                'data' => $req
            ]);
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // update akses approve karyawan 1=can aprove, 0=can't
    // type approve 0=departemen, 1=sub departemen, 9=custom
    public function updateAksesApprove(Request $request)
    {
        $idKaryawan = $request->id_karyawan;
        $approve = $request->approve;
        $typeApprove = $request->type_approve;
        try {
            $req = $this->updateAksesRoleApprove($idKaryawan,$approve,$typeApprove);
            $result=response()->json([
                'status' => 'success',
                'message' => 'Update Akses Aprove Karyawan Successfuly',
                'callback' => $req
            ]);
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // if type aprove = 9
    // type approve (0=by departemen, 1=by sub departemen,2=by id karyawan)
    // id approve (id departemen, id sub departemen, id karyawan)
    public function insertCustomApprove(Request $request)
    {
        $idKaryawan = $request->id_karyawan;
        $typeApprove = $request->type_approve;
        $idApprove = $request->id_approve;
        try {
            $req = $this->insertCustomRoleApprove($idKaryawan,$typeApprove,$idApprove);
            $result=response()->json([
                'status' => 'success',
                'message' => 'Menambahkan Custom Akses Aprove Karyawan Successfuly',
                'data' => $req
            ]);
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // -----------------------------------------------------------------------------
    private function insertUser($idDepartemen,$departemen, $idSubDepartemen,$subDepartemen,$idGrade, $grade, $name,$noTelephone, $idAbsen, $username, $password, $isDell,$doj,$dob)
    {
        $c_user = new UsersController();
        $result_['insert_userDB'] = $c_user->insertUser($idDepartemen,$departemen, $idSubDepartemen,$subDepartemen,$idGrade, $grade, $name,$noTelephone, $idAbsen, $username, $password,$isDell,$doj,$dob);
        return $result_;
    }

    public function insertCuti($idCuti, $tahun,$idAbsen,$tipeCuti,$cuti,$sisaCuti,$tipeMasaBerlaku,$masaBerlaku,$tglBerlaku)
    {
        // generate ID
        $c_generateID = new GenerateIDController();
        $idMst = $c_generateID->getIdCutiMst($tipeCuti);

        $c_cuti = new CutiController();
        $result_['insert_masterCutiDB'] = $c_cuti->insertCutiMst($idMst,$idCuti, $tahun,$idAbsen,$tipeCuti,$cuti,$sisaCuti,$tipeMasaBerlaku,$masaBerlaku,$tglBerlaku);
        return $result_;
    }

    private function insertCutiTRN($idMst,$idTrn,$idCuti, $idPeriode,$tahun,$idKaryawan,$tipeCuti,$cuti,$tanggal,$totalCuti,$keterangan,$tglPengajuan,$status)
    {  
        // insert data
        $c_cuti = new CutiController();
        $result_['insert_cutiDB'] = $c_cuti->insetCutiTrn($idMst,$idTrn,$idCuti, $idPeriode,$tahun,$idKaryawan,$tipeCuti,$cuti,$tanggal,$totalCuti,$keterangan,$tglPengajuan,$status);

        // update data master cuti
        $c_cuti = new CutiController();
        $result_['update_sisaCut'] = $c_cuti->updateMasterCutiKaryawan($idKaryawan,$idMst,$idTrn,$idCuti);
     
        // get list approve up Level
        $c_grade = new GradeController();
        $lstApproveGradeUp = $c_grade->getGradeLvUp($idKaryawan);
     
        if($lstApproveGradeUp!=null)
        {
            $firstLoad=true;
            foreach($lstApproveGradeUp as $v)
            {
                // 0=pending, 1=approve, 2=reject
                $status = '0';
                $telephone = $v->no_telephone;
                $idKaryawanApprove = $v->id_karyawan;
                $tglApprove = '0000-00-00';
                $note = '-';
                if($firstLoad==true)
                {
                    // sent whatsapp message
                    $c_sentWaController = new SentWhatsappController();
                    $result_['sent_whatsapp'] = $c_sentWaController->sentWhatsappApproveCuti($idTrn,$telephone,$idKaryawan,$cuti,$tanggal,$note);
                }
                $firstLoad=false;
                //insert list Approve up Level
                $c_cuti = new CutiController();
                $result_['insert_approveHistory'] = $c_cuti->insertCutiApproveHistory($idTrn, $status, $telephone, $idKaryawanApprove, $tglApprove, $note);
            }
        }
        else
        {
            $result_['insert_approveHistory'] = 'ID Karyawan : '. $idKaryawan.' Tidak memiliki Atasan untuk Approve';
        }
        
        return $result_;
    }

    private function insertCutiLampiran($idCutiMst,$idCutiTrn,$tahun,$url)
    {
        $c_cuti = new CutiController();
        $result_['insert_lampiran'] = $c_cuti->insertCutiLampiran($idCutiMst,$idCutiTrn,$tahun,$url);
        return $result_;
    }

    private function updateRequestCuti($id,$status,$note,$reff,$tglApprove)
    {
        $c_cuti = new CutiController();
        $result_ = $c_cuti->updateActionCutiTrn($id,$status,$note,$reff,$tglApprove);
        return $result_;
    }

    private function disableDataKaryawan($idAbsen)
    {
        $c_users = new Users();
        $result_['disable_MasterUsers'] = $c_users->disableUsers($idAbsen);

        // $c_cuti = new CutiController();
        // $result_['disable_MasterCutiDB'] = $c_cuti->disableCutiMst($idAbsen);
        return $result_;
    }

    private function updateAksesRoleApprove($idKaryawan,$approve,$typeApprove)
    {
        $c_users = new UsersController();
        $result_['update_aksesApprove'] = $c_users->updateAksesApprove($idKaryawan,$approve,$typeApprove);
        return $result_;
    }
    private function insertCustomRoleApprove($idKaryawan,$typeApprove,$idApprove)
    {
        $c_roleApprove = new RoleApproveController();
        $result_['insert_customRoleApprove'] = $c_roleApprove->insertCustomRoleApprove($idKaryawan,$typeApprove,$idApprove);
        return $result_;
    }
    // --------------------------------------------------------------------------------------------

    // karyawan mengajukan Cuti Khusus
    public function requestCutiKhusus(Request $request)
    {
        $idKaryawan = $request->id_karyawan;
        $idCuti = $request->id_cuti;
        $tipeCuti = $request->tipe_cuti;
        $cuti = $request->cuti;
        $tanggal = $request->tanggal;
        $totalCuti = $request->total_cuti;
        $keterangan = $request->keterangan;
        $lampiranFile = $request->lampiran_file;

        try
        {
            // insert cuti
            $tahun = Carbon::now()->format('Y');
            $idPeriode = '-';
            $tglPengajuan = Carbon::now()->format('Y-m-d H:i:s');
            // status 0 = pending HOD, 1= approve, 2=reject
            $status = '0';

            // get tipe Cuti
            $c_cuti = new CutiController();
            $dataCuti = $c_cuti->getTypeMasterCutiByID($idCuti);

            $masaBerlaku = $dataCuti->masa_berlaku;
            $jmlHari = 0;
            $tipeMasaBerlaku = $dataCuti->tipe_masa_berlaku;

            $tanggalDecode = json_decode($tanggal);
            // get tanggal first json Decode
            $tanggalAwal = $tanggalDecode[0];
         
            // insert cuti mst
            $result_['insert_cutiMST'] =  $this->insertCuti($idCuti,$tahun,$idKaryawan,$tipeCuti,$cuti,$jmlHari,$tipeMasaBerlaku,$masaBerlaku,$tanggalAwal);
          
            $sisaCuti =0;
            // cek sisa cuti
            $c_cuti = new CutiController();
            $dt = $c_cuti->getCutiKaryawan($idKaryawan,$tahun,$idCuti);
            foreach($dt as $x)
            {
                $idMst = $x->id_cuti_mst;
                $sisaCuti = $x->sisa_cuti;
                break;
            }
            
            $keterangan = 'Cuti Khusus';
          
            if($sisaCuti > 0)
            {  
                // insert cuti
                // generate ID
                $c_generateID = new GenerateIDController();
                $idTrn = $c_generateID->getIDCutiTrn($idCuti);
                $result_['insert_cutiTRN'] = $this->insertCutiTRN($idMst,$idTrn,$idCuti, $idPeriode,$tahun,$idKaryawan,$tipeCuti,$cuti,$tanggal,$sisaCuti,$keterangan,$tglPengajuan,$status);
                
                // insert Image
                if($lampiranFile!='') {
                    // call class upload Image
                    $c_uploadImage = new ClassUploadImageClass();
                    $urlPathImgLampiran = $c_uploadImage->processImageLampiran($request->file('lampiran_file'),$idKaryawan,$idMst,$idTrn);
                   
                    // insert 
                    $result_['insert_lampiran'] = $this->insertCutiLampiran($idMst,$idTrn,$tahun,$urlPathImgLampiran);
                } 
            }
            else
            {
                $result_['insert_cutiTRN'] ='Sisa Cuti Anda sudah Habis';
            }
            
            $result=response()->json([
                'status' => 'success',
                'message' => 'Request Cuti Karyawan Successfuly',
                'callback' => $result_
            ]);

            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    } 

    // karyawan mengajukan komplemen
    public function requestKomplemen(Request $request)
    {
        $idKaryawan = $request->id_karyawan;
        $tiket = $request->tiket;
        $tanggalKedatangan = $request->tanggal_kedatangan;
        $totalKomplement = $request->total_komplemen;
        $paymentMethods = $request->payment_methods;
        try {
            // insert cuti
            $tahun = Carbon::now()->format('Y');
            $tglPengajuan = Carbon::now()->format('Y-m-d H:i:s');

            // convert to json decode input tiket
            $listTiket = json_decode($tiket);
     
            $validasiSisaKomplemen = $this->validasiSisaKomplemen($idKaryawan,$tahun,$listTiket);

                if($validasiSisaKomplemen=='success')
                {
                    // insert Master TRN Komplement
                    $c_generateID = new GenerateIDController();
                    $idKomplemenTrn = $c_generateID->getIDKomplemenTrn($idKaryawan);

                    // get detail user request
                    $c_userController = new UsersController();
                    $dtUser = $c_userController->getData($idKaryawan);

                    $name = $dtUser->name;
                    $email = $idKaryawan.'@salokapark.com';
                    $noHp = $dtUser->no_telephone;
                    $kodeBooking = '-';
                    $status = '0';
                    $orderId = '-';
                    $result_['insert_KomplementTrn'] = $this->insertKomplementTrn($idKomplemenTrn,$idKaryawan,$name,$email,$noHp,$tglPengajuan,$tanggalKedatangan,$kodeBooking,$orderId,$tiket,$totalKomplement,$paymentMethods,$status);
                    $i=0;
                    foreach($listTiket as $v)
                    {
                        $ticketID = $v->ticket_id;
                        $ticketPriceID = $v->ticket_price_id;
                        $productName = $v->product_name;
                        $qtyPengajuan = $v->quantity;
                        $qtyBonus = $v->qty_bonus;
                        $priceUnit = $v->price_unit;
                        $subTotal = $v->sub_total;

                        // get komplemen in Master
                        $c_komplement = new KomplementController();
                        $dt = $c_komplement->getKomplemenKaryawan($idKaryawan,$tahun,$ticketID);
                        // get ID komplement Active
                        $idKomplementMst = $dt[0]->id_komplement_mst;

                        $result_['insert_KomplementTicketOrder'] = $this->insertKomplementTicketOrder($idKomplementMst,$idKomplemenTrn,$ticketID,$productName,$ticketPriceID,$qtyPengajuan,$qtyBonus,$priceUnit,$subTotal);
                        
                        // update qty master komplement
                        $c_komplement = new KomplementController();
                        $result_[$i]['update_Stock Master '.$productName] =  $c_komplement->updateMasterKomplementKaryawan($idKaryawan,$idKomplementMst,$idKomplemenTrn,$ticketID);
                        $i++;
                    }  

                    // cek payment method
                    if($paymentMethods=='1')
                    {
                        // lunas
                        $apiServiceName = 'create-reservation-compliment-employee';
                        $resultAPIReservation = $this->API_Guzzle_CreateReservation($apiServiceName,$name,$email,$idKaryawan,$tanggalKedatangan,$listTiket);
                       
                        // breakdown data resultAPiReservation
                        $orderID = $resultAPIReservation['insert_reservationTicket']->reservation_employee->order_id;
                        $arrival_date = $resultAPIReservation['insert_reservationTicket']->reservation_employee->arrival_date;
                        $bill = $resultAPIReservation['insert_reservationTicket']->reservation_employee->bill;
                        $kodeBooking = $resultAPIReservation['insert_reservationTicket']->reservation_employee->booking_code;
                        $status = '1';
                        $paymentLink ='-';
                        // update orderIDBooking Ticket
                        $result_['update_orderID'] = $this->updateOrderIDBooking($idKomplemenTrn,$idKaryawan,$orderID,$kodeBooking,$paymentLink,$status);
                        // sent wa komplement success
                        $c_sentWaController = new SentWhatsappController();
                        $result_['sent_whatsapp'] = $c_sentWaController->sentWhatsappKodeBookingTicket($idKomplemenTrn,$idKaryawan);
                    }
                    elseif($paymentMethods=='2')
                    {
                        // transfer / belum lunas
                        $apiServiceName = 'create-reservation-employee';
                        $orderID = $this->API_Guzzle_CreateReservation($apiServiceName,$name,$email,$idKaryawan,$tanggalKedatangan,$listTiket);
                        
                        // get Order ID ticket
                        $orderID = $orderID['insert_reservationTicket']->order_id;

                        // get payment Link
                        $c_apiGuzzle = new API_Guzzle();
                        $apiServiceName='get-xendit-token-employee';
                        $result_['id_komplement_trn'] =$idKomplemenTrn;
                        $result_['payment_link'] = $this->API_Guzzle_GetPaymentLink($apiServiceName,$orderID);
                       
                        $kodeBooking='-';
                        $status = '2';
                    
                        $paymentLink = $result_['payment_link']['get_paymentLink']->payment_link;
                        // update orderIDBooking Ticket
                        $result_['update_orderID'] = $this->updateOrderIDBooking($idKomplemenTrn,$idKaryawan,$orderID,$kodeBooking,$paymentLink,$status);
                    }
                }
                else
                {
                    // sisa komplement tidak mencukupi
                    $result=response()->json([
                        'status' => 'failed',
                        'message' => 'Request Komplemen Karyawan failed',
                        'callback' => $validasiSisaKomplemen
                    ]);
                    return $result;
                }

             $result=response()->json([
                'status' => 'success',
                'message' => 'Request Komplemen Karyawan Successfuly',
                'callback' => $result_
            ]);

            return $result;


        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // get sisa komplemen karyawan
    public function getKomplemenKaryawan(Request $request) {  
    $idKaryawan = $request->id_karyawan;
    $tahun = $request->tahun;
        try {
            $c_komplement = new KomplementController();
            // sample getKomplemenKaryawan($idKaryawan,$tahun,$type_komplemen)
            $req = $c_komplement->getKomplemenKaryawan($idKaryawan,$tahun,'');
            $result=response()->json([
                'status' => 'success',
                'message' => 'Get Data Komplemen Karyawan Successfuly',
                'data' => $req
            ]);
            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // ---------------------------------------------------------------------------------------
    private function validasiSisaKomplemen($idKaryawan,$tahun,$listTiket)
    {
        foreach($listTiket as $v)
        {
            $idKomplemen = $v->ticket_id;
            $productName = $v->product_name;
            $qtyPengajuan = $v->quantity;

            // cek sisa komplemen in Master
            $c_komplement = new KomplementController();
            // sample getKomplemenKaryawan($idKaryawan,$tahun,$type_komplemen)
            $dt = $c_komplement->getKomplemenKaryawan($idKaryawan,$tahun,$idKomplemen);
            $sisaKomplemenDB =$dt[0]->sisa_komplement;

            if($sisaKomplemenDB >= $qtyPengajuan)
            {  
              
            }
            else
            {
                return 'Sisa Komplemen "'.$productName.'" Anda sudah Tidak Mencukupi, Komplemen Tersisa : '.$sisaKomplemenDB;
            }
        }
        return 'success';
    }

    private function insertKomplementMst($idKomplementMst,$idKomplement, $tahun,$idKaryawan,$tipeKomplement,$jmlKomplement,$sisaKomplement)
    {
        $c_komplement = new KomplementController();
        $result_['insert_KomplementDB'] = $c_komplement->insertKomplemenMst($idKomplementMst,$idKomplement, $tahun,$idKaryawan,$tipeKomplement,$jmlKomplement,$sisaKomplement);
        return $result_;
    }

    private function insertKomplementTrn($idKomplemenTrn,$idKaryawan,$name,$email,$noHp,$tglPengajuan,$tanggalKedatangan,$kodeBooking,$orderId,$ticketOrder,$qtyTotal,$paymentMethods,$status)
    {
        $c_komplemenController = new KomplementController();
        $result_['insert_KomplementTrnDB'] = $c_komplemenController->insertKomplemenTrn($idKomplemenTrn,$idKaryawan,$name,$email,$noHp,$tglPengajuan,$tanggalKedatangan,$kodeBooking,$orderId,$ticketOrder,$qtyTotal,$paymentMethods,$status);
        return $result_;
    }

    private function insertKomplementTicketOrder($idKomplementMst,$idKomplementTrn,$ticketId,$productName,$ticketPriceId,$qty,$qtyBonus,$priceUnit,$subTotal)
    {
        $c_komplemenController = new KomplementController();
        $result_['insert_KomplementTicketOrder'] = $c_komplemenController->insertKomplemenTicketOrder($idKomplementMst,$idKomplementTrn,$ticketId,$productName,$ticketPriceId,$qty,$qtyBonus,$priceUnit,$subTotal);
        return $result_;
    }

    private function updateOrderIDBooking($idKomplementTrn,$idKaryawan,$orderID,$kodeBooking,$paymentLink,$status)
    {
        $c_komplemenController = new KomplementController();
        $result_['update_orderID'] = $c_komplemenController->updateOrderIDBooking($idKomplementTrn,$idKaryawan,$orderID,$kodeBooking,$paymentLink,$status);
        return $result_;
    }

    private function API_Guzzle_CreateReservation($apiServiceName,$name,$email,$idKaryawan,$tanggalKedatangan,$ticketOrder)
    {
        $c_apiGuzzle = new API_Guzzle();
        $result_['insert_reservationTicket'] = $c_apiGuzzle->postServiceTiketing($apiServiceName,$name,$email,$idKaryawan,$tanggalKedatangan,$ticketOrder);
        return $result_;
    }

    private function API_Guzzle_GetPaymentLink($apiServiceName,$orderId)
    {
        $c_apiGuzzle = new API_Guzzle();
        $result_['get_paymentLink'] = $c_apiGuzzle->postGetPaymentLink($apiServiceName,$orderId);
        return $result_;
    }
    // 

    // -------------------------------------------------------------------------------------------------
    // OVERTIME
    // karyawan request overtime
    public function requestOvertime(Request $request)
    {
        $idKaryawan = $request->id_karyawan;
        $tglLembur = $request->tgl_lembur;
        $jamLembur = $request->jam_lembur;
        $keterangan = $request->keterangan;

        try {
            // insert cuti
            $tahun = Carbon::now()->format('Y');
            $tglPengajuan = Carbon::now()->format('Y-m-d H:i:s');
            // 0=pending; 1=approve; 2=reject
            $status = '0';

            $c_generateID = new GenerateIDController();
            $idOvertime = $c_generateID->getIDOvertimeMst();
        
            // insert ke table master overtime
            $result_['insert_OvertimeMst'] = $this->insertOvertimeMst($idOvertime,$idKaryawan,$tglPengajuan,$tglLembur,$jamLembur,$keterangan);
            
            $result=response()->json([
                'status' => 'success',
                'message' => 'Request Lembur Karyawan Successfuly',
                'callback' => $result_
            ]);

            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    // function Overtime ------------------------------------------
    private function insertOvertimeMst($idOvertime,$idKaryawan,$tglPengajuan,$tglLembur,$jamLembur,$keterangan)
    {
        $nip='-';
        $totalJam = 0;
        $c_overtimeController = new OvertimeController();
        $result_['insert_OvertimeMst'] = $c_overtimeController->insertOvertimeMst($idOvertime,$idKaryawan,$nip,$tglPengajuan,$tglLembur,$jamLembur,$totalJam,$keterangan);
        
        // get list approve up Level
        $c_grade = new GradeController();
        $lstApproveGradeUp = $c_grade->getGradeLvUp($idKaryawan);
      
        if($lstApproveGradeUp!=null)
        {
            $firstLoad = true;
            foreach($lstApproveGradeUp as $v)
            {
                // 0=pending, 1=approve, 2=reject
                $status = '0';
                $telephone = $v->no_telephone;
                $idKaryawanApprove = $v->id_karyawan;
                $tglApprove = '0000-00-00';
                $note = '-';
                if($firstLoad==true)
                {
                    // sent whatsapp message
                    $c_sentWaController = new SentWhatsappController();
                    $result_['sent_whatsapp'] = $c_sentWaController->sentWhatsappApproveOvertime($idOvertime,$telephone,$idKaryawan,$tglLembur,$jamLembur,$keterangan);
                }
                $firstLoad=false;

                //insert list Approve up Level
                $result_['insert_approveHistory'] = $this->insertOvertimeHistory($idOvertime,$status,$telephone,$idKaryawanApprove,$tglApprove,$note);
            }

        }
        else
        {
            $result_['insert_approveHistory'] = 'ID Karyawan : '. $idKaryawan.' Tidak memiliki Atasan untuk Approve';
        }
        return $result_;
    }

    private function insertOvertimeHistory($idOvertime,$status,$telephone,$idKaryawanApprove,$tglApprove,$note)
    {
        $c_overtimeController = new OvertimeController();
        $result_['insert_OvertimeHistory'] = $c_overtimeController->insertOvertimeHistory($idOvertime,$status,$telephone,$idKaryawanApprove,$tglApprove,$note);
        return $result_;
    }
    // ------------------------------------------------------------------
}
