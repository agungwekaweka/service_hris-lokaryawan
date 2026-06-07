<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KomplementGuest extends Controller
{
      /**Create Guest Users Komplelment
     * Insert to Table Guest Users
     * Insert to Table Geust Komplement Mst
     */
    public function insertGuestUsers($request)
    {
        try
        {
            DB::beginTransaction();
            $result=[];
            // fill decclare variable reqeust
            $idUsers=''; $nik=''; $name=''; $grade=''; $email=''; $telephone='';

            if (isset($request['id_users'])) {$idUsers = $request['id_users'];}
            if (isset($request['nik'])) {$nik = $request['nik'];}
            if (isset($request['name'])) {$name = $request['name'];}
            if (isset($request['grade'])) {$grade = $request['grade'];}
            if (isset($request['email'])) {$email = $request['email'];}
            if (isset($request['telephone'])) {$telephone = $request['telephone'];}

            $requestClassGuestUsers =[];
            $requestClassGuestUsers['id_users'] = $idUsers;
            $requestClassGuestUsers['nik'] = $nik;
            $requestClassGuestUsers['name'] = $name;
            $requestClassGuestUsers['grade'] = $grade;
            $requestClassGuestUsers['email'] = $email;
            $requestClassGuestUsers['telephone'] = $telephone;
         
            $classGuestUsers = new Class_GuestUsers();
            $result['insert_guestUsers'] = $classGuestUsers->insert($requestClassGuestUsers);

            // generate ID Komplememnt
            $c_generateID = new GenerateIDController();
            $idKomplement = $c_generateID->getIDKomplemenGuestMst();
        
            $requestClassKomplementMst =[];
            $requestClassKomplementMst['id_users'] = $idUsers;
            $requestClassKomplementMst['id_komplement_mst'] = $idKomplement;
            $requestClassKomplementMst['qty'] = '8';
            $requestClassKomplementMst['sisa'] = '8';
            $requestClassKomplementMst['years'] = Carbon::now()->format('Y');

            $classGuestKommplementMst = new Class_GuestKomplementMst();
            $result['insert_guestsKomplementMst'] = $classGuestKommplementMst->insert($requestClassKomplementMst);
            
            DB::commit();
            return $result;
        } catch (\Exception $ex) {
            DB::rollBack();
            return $ex;
        }
    }
    // #Endregion

    public function requestKomplementUsersGuest($request)
    {
        try
        {
            DB::beginTransaction();
            $result=[];
            // fill declare variable reqeust
            $idUsers=''; $qty=''; $tanggalKedatangan=''; $pic='';

            if (isset($request['id_users'])) {$idUsers = $request['id_users'];}
            if (isset($request['qty'])) {$qty = $request['qty'];}
            if (isset($request['tanggal_kedatangan'])) {$tanggalKedatangan = $request['tanggal_kedatangan'];}
            if (isset($request['pic'])) {$pic = $request['pic'];}

        
            // cek stock
            $requestGuestKomplementMst =[];
            $requestGuestKomplementMst['id_users'] =  $idUsers;
            $requestGuestKomplementMst['years'] = Carbon::now()->format('Y');
           
            $classGuestKommplementMst = new Class_GuestKomplementMst();
            $result['result_guestKomplementMst_show'] = $classGuestKommplementMst->show($requestGuestKomplementMst);

            $idKomplementMst =''; $sisaKomplement =0;
            if($result['result_guestKomplementMst_show'] !=null)
            {
                $idKomplementMst = $result['result_guestKomplementMst_show'][0]->id_komplement_mst;
                $sisaKomplement = $result['result_guestKomplementMst_show'][0]->sisa;
                if($sisaKomplement>0 && $sisaKomplement>= $qty)
                {
                    // get data users 
                    $requestClassGuestUsers=[];
                    $requestClassGuestUsers['id_users'] = $idUsers;

                    $classGuestUsers = new Class_GuestUsers();
                    $result['get_guestUsers'] = $classGuestUsers->show($requestClassGuestUsers);
               
                    $c_generateID = new GenerateIDController();
                    $idKomplementTrn = $c_generateID->getIDKomplemenTrnGuest($idUsers);
                 
                    $name = $result['get_guestUsers'][0]->name;
                    $email = $result['get_guestUsers'][0]->email;
                    $telephone =$result['get_guestUsers'][0]->telephone;
                 
                    // insert komplement TRN
                    $requestKomplementTrn =[];
                    $requestKomplementTrn['id_komplement_trn'] = $idKomplementTrn;
                    $requestKomplementTrn['id_karyawan'] = $idUsers;
                    $requestKomplementTrn['name'] = $name .' (PIS)';
                    $requestKomplementTrn['email'] = $email;
                    $requestKomplementTrn['no_hp'] = $telephone;
                    $requestKomplementTrn['tanggal_pengajuan'] = Carbon::now()->format('Y-m-d H:i:s');
                    $requestKomplementTrn['tanggal_kedatangan'] = $tanggalKedatangan;
                    $requestKomplementTrn['kode_booking'] = '-';
                    $requestKomplementTrn['order_id'] = '-';
                    $requestKomplementTrn['ticket_order'] = '[{ "ticket_id": "40","ticket_price_id": "25","quantity" : "'.$qty.'","qty_bonus": "0" , "price_unit": "100" , "sub_total": "100" ,"product_name": "Komplemen Karyawan"}]'; // default template
                    $requestKomplementTrn['qty_total'] = $qty;
                    $requestKomplementTrn['payment_methods'] = '1';
                    $requestKomplementTrn['payment_link'] = '-';
                    $requestKomplementTrn['status'] = '0';
            
                    $classKomplementTrn = new Class_KomplementTrn();
                    $result['insert_KomplementTrn'] = $classKomplementTrn->insert($requestKomplementTrn);

                    // update
                    $requestGuestKomplementMst=[];
                    $requestGuestKomplementMst['id_users']= $idUsers;
                    $requestGuestKomplementMst['id_komplement_mst'] = $idKomplementMst;
                    $requestGuestKomplementMst['sisa'] = $sisaKomplement-$qty;
                 
                    $classGuestKomplementMst = new Class_GuestKomplementMst();
                    $result['update_guestKomplementMst'] = $classGuestKomplementMst->update($requestGuestKomplementMst);
                   
                 
                    $ticketOrder = json_decode($requestKomplementTrn['ticket_order']);
                    // create reservation oddo
                    $apiServiceName = 'create-reservation-compliment-employee';
                    $resultAPIReservation = $this->API_Guzzle_CreateReservation($apiServiceName,$requestKomplementTrn['name'],$email,'9998',$tanggalKedatangan,$ticketOrder);
             
                    // breakdown data resultAPiReservation
                    $orderID = $resultAPIReservation['insert_reservationTicket']->reservation_employee->order_id;
                    $arrival_date = $resultAPIReservation['insert_reservationTicket']->reservation_employee->arrival_date;
                    $bill = $resultAPIReservation['insert_reservationTicket']->reservation_employee->bill;
                    $kodeBooking = $resultAPIReservation['insert_reservationTicket']->reservation_employee->booking_code;
                    $status = '1';
                    $paymentLink ='-';
                   
                    // update orderIDBooking Ticket
                    $result_['update_orderID'] = $this->updateOrderIDBooking($idKomplementTrn,$idUsers,$orderID,$kodeBooking,$paymentLink,$status);
                   
                    if($result_['update_orderID']!=false)
                    {
                        // sent wa komplement success
                        $requestSentWaController=[];
                        $requestSentWaController['nama_penerima'] = $name;
                        $requestSentWaController['id_komplement_trn'] =$idKomplementTrn;
                        $requestSentWaController['tanggal_pengajuan'] =  Carbon::now()->format('Y-m-d');
                        $requestSentWaController['tanggal_kedatangan'] = $tanggalKedatangan;
                        $requestSentWaController['qty'] = $qty;
                        $requestSentWaController['kode_booking'] = $kodeBooking;
                        $requestSentWaController['telephone'] = $telephone;

                        $c_sentWaController = new SentWhatsappController();
                        $result_['sent_whatsapp'] = $c_sentWaController->sentWhatsappKodeBookingTicketGuest($requestSentWaController);
                    }
                }
                else
                {
                    $result='Sisa Komplement Sudah Habis';
                }
            }
            else
            {
                $result='Get Guest Komplement MST is Null';
            }
            
            DB::commit();
            return $result;
        } catch (\Exception $ex) {
            DB::rollBack();
            return $ex;
        }
    }
    
    private function API_Guzzle_CreateReservation($apiServiceName,$name,$email,$idKaryawan,$tanggalKedatangan,$ticketOrder)
    {
        $c_apiGuzzle = new API_Guzzle();
        $result_['insert_reservationTicket'] = $c_apiGuzzle->postServiceTiketing($apiServiceName,$name,$email,$idKaryawan,$tanggalKedatangan,$ticketOrder);
        return $result_;
    }
    
    private function updateOrderIDBooking($idKomplementTrn,$idKaryawan,$orderID,$kodeBooking,$paymentLink,$status)
    {
        try {
            $data = DB::table('komplement_trn')
            ->select('kode_booking')
            ->where('id_komplemen_trn',$idKomplementTrn)
            ->where('id_karyawan',$idKaryawan)
            ->first();
            if($data->kode_booking=='-')
            {
                DB::table('komplement_trn')
                ->where('id_komplemen_trn',$idKomplementTrn)
                ->where('id_karyawan',$idKaryawan)
                ->update([
                    'order_id'=> $orderID,
                    'kode_booking'=>$kodeBooking,
                    'payment_link'=>$paymentLink,
                    'status'=>$status
                ]);
                return 'Update Order ID Karyawan Successfuly';
            }
            else
            {
                return false;
            }
           
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    public function getKomplementUsersGuest($request) // custotm
    {
        try
        {
            $result=[];
            // fill decclare variable reqeust
            $idUsers=''; $name=''; $year='';

            if (isset($request['id_users'])) {$idUsers = $request['id_users'];}
            if (isset($request['name'])) {$name = $request['name'];}
            if (isset($request['year'])) {$year = $request['year'];}

            $data_ = DB::table('guest_komplement_mst')
            ->select('guest_komplement_mst.id_users','guest_users.name','guest_users.telephone','guest_komplement_mst.qty','guest_komplement_mst.sisa','guest_komplement_mst.years')
            ->join('guest_users','guest_users.id_users','guest_komplement_mst.id_users');
            if($idUsers!='')
            {
                $data_->where('guest_komplement_mst.id_users',$idUsers);
            }
            if($name!='')
            {
                $data_->where('guest_users.name','like','%'.$name.'%');
            }
            if($year!='')
            {
                $data_->where('guest_komplement_mst.years',$year);
            }

            if($data_->exists())
            {
                $data = $data_->get();
            }
            else
            {
                $data = null;
            }

            return $data;
        } catch (\Exception $ex) {
            DB::rollBack();
            return $ex;
        }
    }

    public function getUsersGuest($request)
    {
        try
        {
            $result=[];
            // fill decclare variable reqeust
            $idUsers=''; $name=''; $email=''; $telephone;

            if (isset($request['id_users'])) {$idUsers = $request['id_users'];}
            if (isset($request['name'])) {$name = $request['name'];}
            if (isset($request['email'])) {$email = $request['email'];}
            if (isset($request['telephone'])) {$telephone = $request['telephone'];}

            $classGuestUsers = new Class_GuestUsers();
            $data = $classGuestUsers->show($request);

            return $data;
        } catch (\Exception $ex) {
            DB::rollBack();
            return $ex;
        }
    }

    public function updateUsersGuest($request)
    {
        try
        {
            $result=[];
            // fill decclare variable reqeust
            $idUsers=''; $nik=''; $name=''; $grade=''; $email=''; $telephone=''; $isDell='';

            if (isset($request['id_users']) && $request['id_users']!='') {$idUsers = $request['id_users'];}
            if (isset($request['nik'])  && $request['nik']!='') {$nik = $request['nik'];}
            if (isset($request['name'])  && $request['name']!='') {$name = $request['name'];}
            if (isset($request['grade'])  && $request['grade']!='') {$grade = $request['grade'];}
            if (isset($request['email'])  && $request['email']!='') {$email = $request['email'];}
            if (isset($request['telephone'])  && $request['telephone']!='') {$telephone = $request['telephone'];}
            if (isset($request['is_dell'])  && $request['is_dell']!='') {$isDell = $request['is_dell'];}

            $classGuestUsers = new Class_GuestUsers();
            $data = $classGuestUsers->update($request);

            return $data;
        } catch (\Exception $ex) {
            DB::rollBack();
            return $ex;
        }
    }

}
