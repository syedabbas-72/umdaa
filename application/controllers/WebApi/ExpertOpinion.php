<?php


defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
ini_set('memory_limit', '-1');

require APPPATH . '/libraries/REST_Controller1.php';

class ExpertOpinion extends REST_Controller1
{
    public function __construct() {
        parent::__construct();
        $this->load->helper('file');
        $this->load->library('PHPMailer');
        $this->load->library('mail_send', array('mailtype' => 'html'));
        $this->load->library('SMTP');
        $this->load->library('phpqrcode/qrlib');
        $this->load->library('zip');
        $this->load->model('Generic_model');
    }

    //Check Expeert Opinion on login
    public function CheckStatus_get($docID = ''){
        if($docID != ""){
            $docInfo = $this->db->query("select * from doctors where doctor_id='".$docID."'")->row();
            if(count($docInfo) > 0){
                $walletInfo = $this->db->select("*")->from("doctor_wallet_prices")->where("doctor_id",$docID)->get()->row();
                if(count($walletInfo) > 0){
                    $param['expertopinion']['openpopup'] = 0;
                }
                else{
                    $param['expertopinion']['openpopup'] = 1;
                }
                $this->response(array('code'=>'200','message'=>'Success','result'=>$param));
            }
            else{
                $param['expertopinion'] = "Doctor  Not Exists. Relogin";
                $this->response(array('code'=>'201','message'=>'Error','result'=>$param));
            }
        }
        else{
            $param['expertopinion'] = "Invalid Inputs";
            $this->response(array('code'=>'201','message'=>'Error','result'=>$param));
        }
    }

    // Save Wallet Money from Online Payment
    public function SavePayment_post(){
        extract($_POST);
        $check = $this->db->query("select * from doctor_wallet_prices where doctor_id='".$doctor_id."'")->row();
        $walletHistory = $this->db->query("SELECT max(eo_wallet_history_id) as max FROM `eo_wallet_history`")->row();
        if(count($check)>0)
        {
            // $data['amount'] = $amount+$check->amount;
            // $this->Generic_model->updateData("doctor_wallet_prices", $data, array('doctor_wallet_id'=>$check->doctor_wallet_id));
            
            // Transaction History
            $str = $walletHistory->max+1;
            // Transaction History
            $data1['transaction_id'] = "UMDEXP".$doctor_id.$str;
            $data1['transaction_id'] = "UMDEXP".$doctor_id.$max_id;
            $data1['transaction_amount'] = $amount;
            $data1['transaction_type'] = "Credit";
            $data1['doctor_id'] = $doctor_id;
            $data1['created_by'] = $doctor_id;
            $data1['created_date_time'] = date("Y-m-d H:i:s");
            $this->Generic_model->insertData("eo_wallet_history",$data1);
            $param['url'] = base_url("OnlinePayment/eo_payment_webview/".$data1['transaction_id']);
            $this->response(array('code'=>'200','result'=>$param));
        }
        else
        {
            // $data['amount'] = $amount;
            // $data['doctor_id'] = $doctor_id;
            // $data['created_by'] = $doctor_id;
            // $data['created_date_time'] = date("Y-m-d H:i:s");
            // $this->Generic_model->insertData("doctor_wallet_prices", $data);
            $str = $walletHistory->max+1;
            // Transaction History
            $data1['transaction_id'] = "UMDEXP".$doctor_id.$str;
            $data1['transaction_amount'] = $amount;
            $data1['transaction_type'] = "Credit";
            $data1['doctor_id'] = $doctor_id;
            $data1['created_by'] = $doctor_id;
            $data1['created_date_time'] = date("Y-m-d H:i:s");
            $this->Generic_model->insertData("eo_wallet_history",$data1);
            $this->Generic_model->angularNotifications('', '', $doctor_id, '', 'EO_Wallet_Money_Added', '');
            $param['url'] = base_url("OnlinePayment/eo_payment_webview/".$data1['transaction_id']);
            $this->response(array('code'=>'200','result'=>$param));
        }
    }

    //get Specialities from wallet specialization
    public function Specialities_get(){
        $check = $this->db->query("select * from `wallet_specilization_prices` group by speciality")->result();
        if(count($check) > 0){
            $i = 0;
            foreach($check as $value){
                $param['specialities'][$i]['speciality'] = $value->speciality;
                $i++;
            }
            $this->response(array('code'=>'200','message'=>'Success','result'=>$param));
        }
        else{
            $param['specialities'] = [];
            $this->response(array('code'=>'201','message'=>'Error','result'=>$param));
        }
    }

    //Add Money into Wallet REquest
    public function walletReq_post()
    {
        extract($_POST);
        if($doctor_id=="" || $amount=="")
        {
            $data = "Parameters are empty.";
            $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$data));
        }
        else
        {
            $data['doctor_id'] = $doctor_id;
            $data['amount'] = $amount;
            $data['created_by'] = $doctor_id;
            $data['created_date_time'] = date("Y-m-d H:i:s");
            $data['modified_by'] = $doctor_id;
            $data['modified_date_time'] = date("Y-m-d H:i:s");
            $this->Generic_model->insertData("wallet_amount_requests", $data);
            $para['result'] = "Successfully Request Sent.";
            $this->response(array('code'=>'200','message'=>'success','result'=>$para));
            $this->Generic_model->angularNotifications('',  '', $doctor_id, '', 'Wallet Request', '');
        }
    }

    // delete prescription item
    public function prescriptionDel_get($ppd_id){
        if(isset($ppd_id))
        {
            $res = $this->Generic_model->deleteRecord('patient_prescription_drug',array('patient_prescription_drug_id'=>$ppd_id));
            if($res)
            {
                
                $this->response(array('code'=>'200','message'=>'success'));
            }
            else
            {
                $this->response(array('code'=>'201','message'=>'Error Occurred'));
            }
        }
    }

    // edit Prescription 
    public function prescriptionEdit_post(){
        if(isset($_POST))
        {
            extract($_POST);
            $prescription_drug = json_decode($prescriptions);
            $patient_prescription_edit['drug_id'] = $prescription_drug[0]->drug_id;
            $patient_prescription_edit['medicine_name'] = $prescription_drug[0]->medicine_name;
            $patient_prescription_edit['day_schedule'] = $prescription_drug[0]->day_schedule;
            $patient_prescription_edit['day_dosage'] = $prescription_drug[0]->day_dosage;
            $patient_prescription_edit['dosage_frequency'] = $prescription_drug[0]->dosage_frequency;
            $patient_prescription_edit['preffered_intake'] = $prescription_drug[0]->preffered_intake;
            $patient_prescription_edit['preffered_time_gap'] = $prescription_drug[0]->preffered_time_gap;
            $patient_prescription_edit['dose_course'] = $prescription_drug[0]->dose_course;
            $patient_prescription_edit['quantity'] = $prescription_drug[0]->quantity;
            $patient_prescription_edit['mode'] = $prescription_drug[0]->mode;
            $patient_prescription_edit['drug_status'] = $prescription_drug[0]->drug_status;
            $patient_prescription_edit['modified_by'] = $user_id;
            $patient_prescription_edit['modified_date_time'] = date('Y-m-d H:i:s');
            $patient_prescription_edit['remarks'] = $prescription_drug[0]->remarks;
            $patient_prescription_edit['drug_dose'] = $prescription_drug[0]->drug_dose;
            $patient_prescription_edit['dosage_unit'] = $prescription_drug[0]->dosage_unit;
            $res = $this->Generic_model->updateData('patient_prescription_drug',$patient_prescription_edit,array('patient_prescription_drug_id'=>$prescription_drug[0]->patient_prescription_drug_id));
            if($res)
            {
                $this->response(array('code'=>'200','message'=>'success','result'=>$patient_prescription_edit));
            }
            else
            {
                $param = "Error";
                $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));
            }
        }
    }

    // Get Doctors List based on department and type of emergency
    public function doctorsList_get($department_id,$user_id){

        $today = date("Y-m-d"); //Today's Date

        $this->db->select("d.*,de.department_name");
        $this->db->from("doctors d");
        $this->db->join("department de","d.department_id=de.department_id");
        $this->db->where("d.department_id='".$department_id."'");
        $this->db->where("d.doctor_id!='".$user_id."'");
        $docInfo = $this->db->get()->result();
        // echo $this->db->last_query();

        $myWalletInfo = $this->db->select("*")->from("doctor_wallet_prices")->where("doctor_id",$user_id)->get()->row();
        $mywallet = $myWalletInfo->amount;

        if(count($docInfo)>0)
        {
            $i = 0;
            foreach ($docInfo as $value) 
            {
                $docAvailbilityInfo = $this->db->query("select * from calendar_blocking where doctor_id='".$value->doctor_id."'")->row();
                if(count($docAvailbilityInfo)>0)
                {
                    $datetime = explode("-", $docAvailbilityInfo->dates);
                    $start = explode(" ", $datetime[0]);
                    $end = explode(" ",$datetime[1]);
                    if(($today >= $start) && ($today <= $end))
                    {
                        // echo "s";
                        continue;
                    }
                    else
                    {
                        // $doctorWalletInfo = $this->db->query("select amount from doctor_wallet_prices  where doctor_id='".$value->doctor_id."'")->row();
                        // // echo $this->db->last_query();
                        // if($doctorWalletInfo->amount == ""){
                        //     continue;
                        // }
                        // $docInfo = 

                        $param['docInfo'][$i]['doctor_id'] = $value->doctor_id;
                        $param['docInfo'][$i]['doctor_name'] = "Dr. ".$value->first_name." ".$value->last_name;
                        $param['docInfo'][$i]['department_name'] = $value->department_name;
                        $param['docInfo'][$i]['price'] = "800";
                        $param['docInfo'][$i]['speciality'] = $value->department_name;
                        // $param['docInfo'][$i]['payment'] = ($mywallet > $doctorWalletInfo->amount)?1:0;
                    }
                }
                else
                {
                    // $doctorWalletInfo = $this->db->query("select amount from doctor_wallet_prices  where doctor_id='".$value->doctor_id."'")->row();
                    // // $doctorWalletInfo = $this->db->query("select ws.speciality,ws.amount from doctor_wallet_prices dw,wallet_specilization_prices ws where dw.speciality=ws.speciality and dw.doctor_id='".$value->doctor_id."'")->row();

                    // if($doctorWalletInfo->amount == ""){
                    //     continue;
                    // }
                    // echo $this->db->last_query();
                    $param['docInfo'][$i]['doctor_id'] = $value->doctor_id;
                    $param['docInfo'][$i]['doctor_name'] = "Dr. ".$value->first_name." ".$value->last_name;
                    $param['docInfo'][$i]['department_name'] = $value->department_name;
                    $param['docInfo'][$i]['price'] = "800";
                    $param['docInfo'][$i]['speciality'] = $value->department_name;
                    // $param['docInfo'][$i]['payment'] = ($mywallet > $doctorWalletInfo->amount)?1:0;
                }
                $i++;
            }
            $this->response(array('code'=>'200','message'=>'DoctorsList','result'=>$param));
        }
        else
        {
            $this->response(array('code'=>'201','message'=>'Doctors Not Found.'));
        }
    }

    

    // Post ExpertOpinion Request to Doctor
    public function ExpertOpinionReq_post()
    {
        if(isset($_POST))
        {
            extract($_POST);
            
            $appInfo = $this->Generic_model->getSingleRecord('appointments', array('appointment_id'=>$appointment_id));

            $data['case_type'] = $case_type;
            $data['parent_doctor_id'] = $user_id;
            $data['referred_doctor_id'] = $referred_doctor_id;
            $data['appointment_id'] = $appointment_id;
            $data['patient_id'] = $appInfo->patient_id;
            $data['department_id'] = $department_id;
            $data['created_by'] = $user_id;
            $data['created_date_time'] = date("Y-m-d H:i:s");
            $expert_opinion_id = $this->Generic_model->insertDataReturnId("expert_opinion",$data);
            if($expert_opinion_id)
            {
                $data2['expert_opinion_id'] = $expert_opinion_id;
                $data2['sent_by'] = $user_id;
                $data2['message'] = $comments;
                $data2['created_by'] = $user_id;
                $data2['created_date_time'] = date("Y-m-d H:i:s");
                $this->Generic_model->insertData("expert_opinion_conversations",$data2);

                $param = "Request Sent Successfully";    
                $this->Generic_model->angularNotifications('', $appointment_id, $referred_doctor_id, '', 'EO_Sent_Notification', '');
                $this->Generic_model->angularNotifications('', $appointment_id, $user_id, '', 'EO_Received_Notification', '');
                $this->response(array('code'=>'200','message'=>'Success','result'=>$param));
            }
            else
            {
                $param = "UnAuthorized Access";    
                $this->response(array('code'=>'201','message'=>'Error Occurred','result'=>$param));                
            }
        }
        else
        {            
            $data = "UnAuthorized Access";    
            $this->response(array('code'=>'201','message'=>'Error Occurred','result'=>$data));
        }
    }

    // Post ExpertOpinion Referred Doctor Comments
    public function ExpertOpinionComments_post()
    {
        if(isset($_POST))
        {
            extract($_POST);
            $data['referred_doctor_comments'] = $comments;
            $data['status'] = 2;
            $res = $this->Generic_model->updateData("expert_opinion",$data,array("expert_opinion_id"=>$expert_opinion_id));
            if($res)
            {
                $param = "Comments Added Successfully";    
                $this->response(array('code'=>'200','message'=>'Success','result'=>$param));
            }
            else
            {
                $param = "UnAuthorized Access";    
                $this->response(array('code'=>'201','message'=>'Error Occurred','result'=>$param));                
            }
        }
        else
        {            
            $data = "UnAuthorized Access";    
            $this->response(array('code'=>'201','message'=>'Error Occurred','result'=>$data));
        }
    }
    // List for doctor who requests another doctor for expert opinion 
    public function requestsPatientsList_get($docId)
    {
        if(!empty(isset($_GET)))
        {
            $docInfo = $this->db->query("select * from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$docId."'")->row();
            if(count($docInfo)>0)
            {
                $refInfo = $this->db->query("select * from expert_opinion where parent_doctor_id='".$docId."' order by expert_opinion_id DESC")->result();
                $i = 0;
                if(count($refInfo) > 0){
                    foreach ($refInfo as $value) 
                    {
                        if($value->status == 0)
                        {
                            $status = "Not Accepted";
                        }
                        elseif($value->status == 1)
                        {
                            $status = "Accepted";
                        }
                        elseif($value->status == 2)
                        {
                            $status = "Commented";
                        }
                        elseif($value->status == 3)
                        {
                            $status = "FI Written";
                        }
                        elseif($value->status == 4)
                        {
                            $status = "Rejected";
                        }
                        elseif($value->status == 5)
                        {
                            $status = "Cancelled";
                        }
                        elseif($value->status == 6)
                        {
                            $status = "Closed";
                        }
                        else
                        {
                            $status = "";
                        }

                        
                        // Check whether expert opinion has any child requests
                        $checkChild = $this->Generic_model->getAllRecords('expert_opinion', array('parent_expert_opinion_id'=>$value->expert_opinion_id));
                        if(count($checkChild) > 0){
                            $param['requested_patient_list'][$i]['closeStatus'] = 1;
                            $param['requested_patient_list'][$i]['childStatus'] = 0;
                        }
                        else{
                            $param['requested_patient_list'][$i]['closeStatus'] = 0;
                            $param['requested_patient_list'][$i]['childStatus'] = 1;
                        }

                        if($value->parent_expert_opinion_id == 0){
                            $param['requested_patient_list'][$i]['opinion_type'] = "New Request";
                        }
                        else{
                            $param['requested_patient_list'][$i]['opinion_type'] = "Repeated Request";
                        }
                        $patientInfo = $this->db->query("select * from appointments a,patients p where p.patient_id=a.patient_id and a.appointment_id='".$value->appointment_id."'")->row();
                        $param['requested_patient_list'][$i]['patientName'] = $patientInfo->first_name." ".$patientInfo->last_name;
                        $param['requested_patient_list'][$i]['doctor_name'] = getDoctorName($value->referred_doctor_id);
                        $param['requested_patient_list'][$i]['umr_no'] = $patientInfo->umr_no;
                        $param['requested_patient_list'][$i]['clinic_id'] = $patientInfo->clinic_id;
                        $param['requested_patient_list'][$i]['patient_id'] = $patientInfo->patient_id;
                        $param['requested_patient_list'][$i]['appointment_id'] = $patientInfo->appointment_id;
                        $param['requested_patient_list'][$i]['doctor_id'] = $docInfo->doctor_id;
                        $param['requested_patient_list'][$i]['appointment_time'] = date("M d Y h:i A", strtotime($value->created_date_time));
                        $param['requested_patient_list'][$i]['requested_doctor_comments'] = $value->parent_doctor_comments;
                        $param['requested_patient_list'][$i]['referred_doctor_comments'] = $value->referred_doctor_comments;
                        $param['requested_patient_list'][$i]['status'] = $status;
                        $param['requested_patient_list'][$i]['expert_opinion_id'] = $value->expert_opinion_id;    
                        $i++;
                    }
                } 
                else{
                    $param['requested_patient_list'] = [];
                }
                
                $this->response(array("code"=>"200","message"=>"Successs","result"=>$param));
            }
            else
            {
                $data = "UnAuthorized Access";    
                $this->response(array('code'=>'201','message'=>'Error Occurred','result'=>$data));            
            }    
        }
        elseif(empty(isset($_GET)))
        {
            $param['result'] = "Please Fill The Data";
            $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));
        }
    }

    //Accept Request
    public function AcceptRequest_get($expID)
    {
        if(isset($_GET))
        {
            $expCheck = $this->db->query("select * from expert_opinion where expert_opinion_id='".$expID."'")->num_rows();
            if($expCheck>0)
            {
                $expInfo = $this->db->query("select * from expert_opinion where expert_opinion_id='".$expID."'")->row();
                if($expInfo->status == 1)
                {
                    $param['result'] = "Already Accepted";
                    $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));
                }
                else
                {
                    $data['status'] = 1;
                    $this->Generic_model->updateData("expert_opinion",$data,array('expert_opinion_id'=>$expID));

                    if($expInfo->parent_expert_opinion_id == 0){
                        $doctorWalletInfo = $this->db->query("select dw.speciality,ws.amount from doctor_wallet_prices dw,wallet_specilization_prices ws where dw.speciality=ws.speciality and dw.doctor_id='".$expInfo->referred_doctor_id."'")->row();
                        // echo $this->db->last_query();
                        $transaction_amount = 800;
    
                        $walletInfo = $this->db->query("select * from doctor_wallet_prices where doctor_id='".$expInfo->parent_doctor_id."'")->row();
                        // echo $this->db->last_query();
                        $wallet['amount'] = $walletInfo->amount-$transaction_amount;
                        $this->Generic_model->updateData("doctor_wallet_prices",$wallet,array('doctor_id'=>$expInfo->parent_doctor_id));
    
                        $data1['transaction_amount'] = $transaction_amount;
                        $data1['transaction_type'] = "Debit";
                        $data1['expert_opinion_id'] = $expID;
                        $data1['payment_status'] = 1;
                        $data1['doctor_id'] = $expInfo->created_by;
                        $data1['created_by'] = $expInfo->created_by;
                        $data1['created_date_time'] = date("Y-m-d H:i:s");
                        $this->Generic_model->insertData("eo_wallet_history", $data1);
    
                        
                        $data3['transaction_amount'] = $transaction_amount-100;
                        $data3['transaction_type'] = "Credit";
                        $data3['expert_opinion_id'] = $expID;
                        $data3['payment_status'] = 0;
                        $data3['doctor_id'] = $expInfo->referred_doctor_id;
                        $data3['created_by'] = $expInfo->referred_doctor_id;
                        $data3['created_date_time'] = date("Y-m-d H:i:s");
                        $this->Generic_model->insertData("eo_wallet_history", $data3);
    
                        $data2['expert_opinion_id'] = $expID;
                        $data2['amount'] = $transaction_amount;
                        $data2['status'] = 0;
                        $data2['created_by'] = $expInfo->created_by;
                        $data2['created_date_time'] = date("Y-m-d H:i:s");
                        $data2['modified_by'] = $expInfo->created_by;
                        $data2['modified_date_time'] = date("Y-m-d H:i:s");
                        $this->Generic_model->insertData('umdaa_eo_wallet', $data2);  
                    }

                    $param['result'] = "Request Accepted";
                    $this->Generic_model->angularNotifications('', $expInfo->appointment_id, $expInfo->parent_doctor_id, '', 'EO_Accept', '');
                    $this->response(array('code'=>'200','message'=>'Success','result'=>$param));
                }    
            }
            else
            {
                $param['result'] = "Invalid Inputs";
                $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));       
            }
        }
        else
        {
            $param['result'] = "Invalid Inputs";
            $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));
        }
    }

    // Close Expert Opinion 
    public function CloseExpertOpinion_get($expID){
        if(isset($_GET))
        {
            $expCheck = $this->db->query("select * from expert_opinion where expert_opinion_id='".$expID."'")->num_rows();
            if($expCheck>0)
            {
                $expInfo = $this->db->query("select * from expert_opinion where expert_opinion_id='".$expID."'")->row();
                if($expInfo->status == 6)
                {
                    $param['result'] = "Expert Opinion Closed";
                    $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));
                }
                else
                {
                    $data['status'] = 6;
                    $this->Generic_model->updateData("expert_opinion",$data,array('expert_opinion_id'=>$expID));
                    $param['result'] = "Expert Opinion Closed";
                    $this->Generic_model->angularNotifications('', $expInfo->appointment_id, $expInfo->parent_doctor_id, '', 'EO_Close', '');
                    $this->response(array('code'=>'200','message'=>'Success','result'=>$param));
                }    
            }
            else
            {
                $param['result'] = "Invalid Inputs";
                $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));       
            }
        }
        else
        {
            $param['result'] = "Invalid Inputs";
            $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));
        }        
    }

    // Request Again For Expert Opinion if not Satisfied
    public function RequestAgainEO_post(){
        if(isset($_POST)){
            extract($_POST);
            $expCheck = $this->db->query("select * from expert_opinion where expert_opinion_id='".$expert_opinion_id."'")->num_rows();
            if($expCheck > 0){
                $today = date("Y-m-d");
                $expInfo = $this->db->query("select * from expert_opinion where expert_opinion_id='".$expert_opinion_id."'")->row();
                
                $expCreatedDate = explode(" ",$expInfo->created_date_time);
                $date1 = date_create($expCreatedDate[0]);
                $date2 = date_create($today);
                $diff = date_diff($date1,$date2);
                $diffDays = $diff->format("%a");

                $parentCheck = $this->Generic_model->getAllRecords("expert_opinion", array('parent_expert_opinion_id'=>$expert_opinion_id,'payment_status'=>2));
                if(count($parentCheck) < 1){

                    if($diffDays >= 7){
                        $param['result'] = "You Request is ".$diffDays." Days Old. Please raise new request";
                        $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));    
                    }
                    else{
                        $data['case_type'] = $expInfo->case_type;
                        $data['parent_expert_opinion_id'] = $expert_opinion_id;
                        $data['parent_doctor_id'] = $expInfo->parent_doctor_id;
                        $data['referred_doctor_id'] = $expInfo->referred_doctor_id;
                        $data['appointment_id'] = $expInfo->appointment_id;
                        $data['department_id'] = $expInfo->department_id;
                        $data['created_by'] = $expInfo->created_by;
                        $data['created_date_time'] = date("Y-m-d H:i:s");
                        $eo_id = $this->Generic_model->insertDataReturnId("expert_opinion",$data);
                        
                        $data2['expert_opinion_id'] = $eo_id;
                        $data2['sent_by'] = $expInfo->created_by;
                        $data2['message'] = $comments;
                        $data2['created_by'] = $expInfo->created_by;
                        $data2['created_date_time'] = date("Y-m-d H:i:s");
                        $this->Generic_model->insertData("expert_opinion_conversations",$data2);

                        $param = "Request Sent Successfully";    
                        $this->Generic_model->angularNotifications('', $expInfo->appointment_id, $expInfo->referred_doctor_id, '', 'EO_Sent_Notification', '');
                        $this->Generic_model->angularNotifications('', $expInfo->appointment_id, $expInfo->parent_doctor_id, '', 'EO_Received_Notification', '');
                        $this->response(array('code'=>'200','message'=>'Success','result'=>$param));
                    }
                    
                }
                else{
                    $param['result'] = "Maximum Requests Exceeded For This Request.";
                    $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));       
                }
            }
            else{
                $param['result'] = "Invalid Inputs";
                $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));       
            }
        }
        else
        {
            $param['result'] = "Invalid Inputs";
            $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));
        } 
    }

    //Check Whether expert opinion is in below 7 days or not
    public function CheckEO_get($expert_opinion_id){
        if(isset($_GET)){
            $expCheck = $this->db->query("select * from expert_opinion where expert_opinion='".$expert_opinion_id."'")->num_rows();
            if($expCheck > 0){

            }
            else{
                $param['result'] = "Invalid Inputs";
                $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));       
            }
        }
        else
        {
            $param['result'] = "Invalid Inputs";
            $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));
        }  
    }

    //Complete and Forward to referred doctor
    public function ForwardRequest_get($expID)
    {
        if(isset($_GET))
        {
            $expCheck = $this->db->query("select * from expert_opinion where expert_opinion_id='".$expID."'")->num_rows();
            if($expCheck>0)
            {
                $expInfo = $this->db->query("select * from expert_opinion where expert_opinion_id='".$expID."'")->row();
                // echo "zz<pre>";print_r($expInfo);echo "</pre>";

                if($expInfo->status == 3)
                {
                    $param['result'] = "Already Forwarded";
                    $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));
                }
                else
                {
                    
                    $cdInfo = $this->db->query("select * from patient_clinical_diagnosis pcd,patient_cd_line_items pcdl where pcd.patient_clinical_diagnosis_id=pcdl.patient_clinical_diagnosis_id and pcd.expert_opinion_id='".$expID."' and pcdl.created_by='".$expInfo->referred_doctor_id."'")->result();
                    $invInfo = $this->db->query("select * from patient_investigation pin,patient_investigation_line_items pil where pin.patient_investigation_id=pil.patient_investigation_id and pin.expert_opinion_id='".$expID."' and pil.created_by='".$expInfo->referred_doctor_id."'")->result();
                    $presInfo = $this->db->query("select * from patient_prescription pp,patient_prescription_drug ppd where pp.patient_prescription_id=ppd.patient_prescription_id and pp.expert_opinion_id='".$expID."' and ppd.created_by='".$expInfo->referred_doctor_id."'")->result();

                    // if(count())
                    
                    $data['status'] = 3;
                    $this->Generic_model->updateData("expert_opinion",$data,array('expert_opinion_id'=>$expID));
                    $param['result'] = "Request Forwarded";
                    $this->Generic_model->angularNotifications('', $expInfo->appointment_id, $expInfo->parent_doctor_id, '', 'EO_FI', '');
                    $this->response(array('code'=>'200','message'=>'Success','result'=>$param));
                }    
            }
            else
            {
                $param['result'] = "Invalid Inputs";
                $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));       
            }
        }
        else
        {
            $param['result'] = "Invalid Inputs";
            $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));
        }
    }

    //Cancel Request
    public function CancelRequest_get($expID)
    {
        if(isset($_GET))
        {
            $expCheck = $this->db->query("select * from expert_opinion where expert_opinion_id='".$expID."'")->num_rows();
            if($expCheck>0)
            {
                $expInfo = $this->db->query("select * from expert_opinion where expert_opinion_id='".$expID."'")->row();
                // echo "zz<pre>";print_r($expInfo);echo "</pre>";
                if($expInfo->status == 5)
                {
                    $param['result'] = "Already Cancelled";
                    $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));
                }
                else
                {
                    $data['status'] = 5;
                    $this->Generic_model->updateData("expert_opinion",$data,array('expert_opinion_id'=>$expID));

                    // change status as Return Money to Wallet 
                    // $umdaa_eo_wallet = $this->Generic_model->getSingleRecord('umdaa_eo_wallet', array('expert_opinion_id'=>$expID));
                    // $data3['status'] = "2";
                    // $this->Generic_model->updateData('umdaa_eo_wallet', $data3, array('expert_opinion_id'=>$expID));
                    // // $this->Generic_model->angularNotifications();

                    // $wallet = $this->Generic_model->getSingleRecord('doctor_wallet_prices', array('doctor_id'=>$expInfo->parent_doctor_id));                    

                    // $data2['amount'] = $wallet->amount+$umdaa_eo_wallet->amount;
                    // $this->Generic_model->updateData('doctor_wallet_prices', $data2, array('doctor_wallet_id'=>$wallet->doctor_wallet_id));

                    // $data1['transaction_amount'] = $umdaa_eo_wallet->amount;
                    // $data1['transaction_type'] = "Credit";
                    // $data1['expert_opinion_id'] = $expID;
                    // $data1['doctor_id'] = $expInfo->created_by;
                    // $data1['created_by'] = $expInfo->created_by;
                    // $data1['created_date_time'] = date("Y-m-d H:i:s");
                    // $this->Generic_model->insertData("eo_wallet_history", $data1);

                    $param['result'] = "Request Cancelled";
                    $this->Generic_model->angularNotifications('', $expInfo->appointment_id, $expInfo->parent_doctor_id, '', 'EO_Cancel', '');
                    $this->response(array('code'=>'200','message'=>'Success','result'=>$param));
                }    
            }
            else
            {
                $param['result'] = "Invalid Inputs";
                $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));       
            }
        }
        else
        {
            $param['result'] = "Invalid Inputs";
            $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));
        }
    }

    //Reject Request
    public function RejectRequest_get($expID)
    {
        if(isset($_GET))
        {
            $expCheck = $this->db->query("select * from expert_opinion where expert_opinion_id='".$expID."'")->num_rows();
            if($expCheck>0)
            {
                $expInfo = $this->db->query("select * from expert_opinion where expert_opinion_id='".$expID."'")->row();
                if($expInfo->status == 4)
                {
                    $param['result'] = "Already Rejected";
                    $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));
                }
                else
                {
                    $data['status'] = 4;
                    $this->Generic_model->updateData("expert_opinion",$data,array('expert_opinion_id'=>$expID));

                    // change status as Return Money to Wallet due to rejection
                    // $umdaa_eo_wallet = $this->Generic_model->getSingleRecord('umdaa_eo_wallet', array('expert_opinion_id'=>$expID));
                    // $data3['status'] = "3";
                    // $this->Generic_model->updateData('umdaa_eo_wallet', $data3, array('expert_opinion_id'=>$expID));

                    // $wallet = $this->Generic_model->getSingleRecord('doctor_wallet_prices', array('doctor_id'=>$expInfo->parent_doctor_id));                    

                    // $data2['amount'] = $wallet->amount+$umdaa_eo_wallet->amount;
                    // $this->Generic_model->updateData('doctor_wallet_prices', $data2, array('doctor_wallet_id'=>$wallet->doctor_wallet_id));

                    // $data1['transaction_amount'] = $umdaa_eo_wallet->amount;
                    // $data1['transaction_type'] = "Credit";
                    // $data1['expert_opinion_id'] = $expID;
                    // $data1['doctor_id'] = $expInfo->created_by;
                    // $data1['created_by'] = $expInfo->created_by;
                    // $data1['created_date_time'] = date("Y-m-d H:i:s");
                    // $this->Generic_model->insertData("eo_wallet_history", $data1);

                    $param['result'] = "Request Rejected";
                    $this->response(array('code'=>'200','message'=>'Success','result'=>$param));
                    $this->Generic_model->angularNotifications('', $expInfo->appointment_id, $expInfo->parent_doctor_id, '', 'EO_Reject', '');
                }    
            }
            else
            {
                $param['result'] = "Invalid Inputs";
                $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));       
            }
        }
        else
        {
            $param['result'] = "Invalid Inputs";
            $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));
        }
    }

    

    // List of Requests that came for expert opinion to the doctor
    public function referredPatientsList_get($docId)
    {
        $docInfo = $this->db->query("select * from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$docId."'")->row();
        if(count($docInfo)>0)
        {
            $refInfo = $this->db->query("select * from expert_opinion where referred_doctor_id='".$docId."' order by expert_opinion_id DESC")->result();
            $i = 0;
            if(count($refInfo) > 0){
                foreach ($refInfo as $value) 
                {
                    if($value->status == 0)
                    {
                        $status = "Not Accepted";
                    }
                    elseif($value->status == 1)
                    {
                        $status = "Accepted";
                    }
                    elseif($value->status == 2)
                    {
                        $status = "Commented";
                    }
                    elseif($value->status == 3)
                    {
                        $status = "FI Written";
                    }
                    elseif($value->status == 4)
                    {
                        $status = "Rejected";
                    }
                    elseif($value->status == 5)
                    {
                        $status = "Cancelled";
                    }
                    elseif($value->status == 6)
                    {
                        $status = "Closed";
                    }
                    else
                    {
                        $status = "";
                    }

                    // Check whether expert opinion has any child requests
                    $checkChild = $this->Generic_model->getAllRecords('expert_opinion', array('parent_expert_opinion_id'=>$value->expert_opinion_id));
                    if(count($checkChild) > 0){
                        $param['referred_patient_list'][$i]['closeStatus'] = 1;
                    }
                    else{
                        $param['referred_patient_list'][$i]['closeStatus'] = 0;
                    }

                    if($value->parent_expert_opinion_id == 0){
                        $param['referred_patient_list'][$i]['opinion_type'] = "New Request";
                    }
                    else{
                        $param['referred_patient_list'][$i]['opinion_type'] = "Repeated Request";
                    }
                    $appInfo = $this->db->query("select * from appointments where appointment_id='".$value->appointment_id."'")->row();
                    $patientInfo = $this->db->query("select * from patients where patient_id='".$appInfo->patient_id."'")->row();
                    
                    // Get Conversation Start Message
                    $conv = $this->db->query("select * from expert_opinion_conversations where expert_opinion_id='".$value->expert_opinion_id."' and sent_by='".$value->parent_doctor_id."' order by eo_conversation_id ASC")->row();

                    $param['referred_patient_list'][$i]['patientName'] = $patientInfo->first_name." ".$patientInfo->last_name;
                    $param['referred_patient_list'][$i]['doctor_name'] = getDoctorName($value->parent_doctor_id);
                    $param['referred_patient_list'][$i]['umr_no'] = $patientInfo->umr_no;
                    $param['referred_patient_list'][$i]['clinic_id'] = $appInfo->clinic_id;
                    $param['referred_patient_list'][$i]['patient_id'] = $appInfo->patient_id;
                    $param['referred_patient_list'][$i]['appointment_id'] = $appInfo->appointment_id;
                    $param['referred_patient_list'][$i]['doctor_id'] = $docInfo->doctor_id;
                    $param['referred_patient_list'][$i]['appointment_time'] = date("M d Y h:i A", strtotime($value->created_date_time));
                    $param['referred_patient_list'][$i]['requested_doctor_comments'] = $value->parent_doctor_comments;
                    $param['referred_patient_list'][$i]['referred_doctor_comments'] = $value->referred_doctor_comments;
                    $param['referred_patient_list'][$i]['status'] = $status;
                    $param['referred_patient_list'][$i]['expert_opinion_id'] = $value->expert_opinion_id;    
                    $param['referred_patient_list'][$i]['comment'] = $conv->message;
                    $i++;
                }
            }
            else{
                $param['referred_patient_list'] = [];
            }
            
            $this->response(array("code"=>"200","message"=>"Successs","result"=>$param));
        }
        else
        {
            $data = "UnAuthorized Access";    
            $this->response(array('code'=>'201','message'=>'Error Occurred','result'=>$data));            
        }
    }


    //saving clinical diagnosys list of patients with respect to appointmet
    public function clinical_diagnosis_submit_post() 
    {
        if(!empty(isset($_POST)))
        {
            extract($_POST);
            if(!empty($appointment_id))
            {
                $expInfo = $this->db->select("*")->from("expert_opinion")->where("expert_opinion_id='".$expert_opinion_id."'")->get()->row();
                $cdInfo = $this->db->select("*")->from("patient_clinical_diagnosis")->where("appointment_id='".$appointment_id."'")->get()->row();
                if(count($cdInfo)>0)
                {   
                    $pcd['expert_opinion_id'] = $expert_opinion_id;
                    $pcd['modified_by'] = $expInfo->referred_doctor_id;
                    $pcd['modified_date_time'] = date("Y-m-d H:i:s");
                    $this->Generic_model->updateData("patient_clinical_diagnosis",$pcd,array('patient_clinical_diagnosis_id'=>$cdInfo->patient_clinical_diagnosis_id));
                    if(count($clinicaldiagnosis)>0)
                    {
                        $cnt = 0;
                        $clinicaldiagnosis = json_decode($clinicaldiagnosis);
                        for($i = 0;$i < count($clinicaldiagnosis);$i++)
                        {
                            $this->db->select("*");
                            $this->db->from("patient_cd_line_items");
                            $this->db->where("patient_clinical_diagnosis_id='".$cdInfo->patient_clinical_diagnosis_id."'");
                            $this->db->where("clinical_diagnosis_id='".$clinicaldiagnosis[$i]->clinical_diagnosis_id."'");
                            $check = $this->db->get()->num_rows();

                            // if($check<=0)
                            // {
                                if($clinicaldiagnosis[$i]->clinical_diagnosis_id == ""){
                                    $cd_Info = masterCDInfo($clinicaldiagnosis[$i]->clinical_diagnosis_id);
                                    $code = $cd_Info->code;
                                }
                                else{
                                    $code = "";
                                }
                                
                                $pcdl['patient_clinical_diagnosis_id'] = $cdInfo->patient_clinical_diagnosis_id;
                                $pcdl['expert_opinion_id'] = $expert_opinion_id;
                                $pcdl['clinical_diagnosis_id'] = ($clinicaldiagnosis[$i]->clinical_diagnosis_id=="")?0:$clinicaldiagnosis[$i]->clinical_diagnosis_id;
                                $pcdl['disease_name'] = $clinicaldiagnosis[$i]->disease_name;
                                $pcdl['code'] = $code;
                                $pcdl['created_by'] = $user_id; 
                                $pcdl['created_date_time'] = date('Y-m-d H:i:s');
                                // print_r($pcdl);
                                $res = $this->Generic_model->insertData('patient_cd_line_items',$pcdl);
                                // echo $this->db->last_query();
                                if($res)
                                    $cnt++;
                            // }
                            // else
                            // {
                            //     $param = "Already in the List";
                            //     $this->response(array('code'=>'201','message'=>'Already In the List','result'=>$param));   
                            // }
                        }
                        if($cnt > 0)
                        {
                            $param = $this->getLatestClinicalDiagnosis($expert_opinion_id);
                            $this->response(array('code'=>'200','message'=>'Patient Clinical Diagnosis Created Successfully','result'=>$param));   
                        }
                    }                    
                }
                else
                {
                    $appInfo = $this->db->query("select * from appointments where appointment_id='".$appointment_id."'")->row();
                    $pcd['clinic_id'] = $appInfo->clinic_id;
                    $pcd['doctor_id'] = $appInfo->doctor_id;
                    $pcd['patient_id'] = $appInfo->patient_id;
                    $pcd['umr_no'] = $appInfo->umr_no;
                    $pcd['appointment_id'] = $appointment_id;
                    $pcd['created_by'] = $expInfo->referred_doctor_id;
                    $pcd['created_date_time'] = date('Y-m-d H:i:s');
                    $pcd['modified_date_time'] = date('Y-m-d H:i:s');
                    $pcd_id = $this->Generic_model->insertDataReturnId('patient_clinical_diagnosis', $pcd);
                    if(count($clinicaldiagnosis)>0)
                    {
                        $cnt = 0;
                        $clinicaldiagnosis = json_decode($clinicaldiagnosis);
                        for($i = 0;$i < count($clinicaldiagnosis);$i++)
                        {
                            $this->db->select("*");
                            $this->db->from("patient_cd_line_items");
                            $this->db->where("patient_clinical_diagnosis_id='".$pcd_id."'");
                            $this->db->where("clinical_diagnosis_id='".$clinicaldiagnosis[$i]->clinical_diagnosis_id."'");
                            $check = $this->db->get()->num_rows();

                            // if($check<=0)
                            // {
                                if($clinicaldiagnosis[$i]->clinical_diagnosis_id == ""){
                                    $cd_Info = masterCDInfo($clinicaldiagnosis[$i]->clinical_diagnosis_id);
                                    $code = $cd_Info->code;
                                }
                                else{
                                    $code = "";
                                }
                                
                                $pcdl['patient_clinical_diagnosis_id'] = $pcd_id;
                                $pcdl['expert_opinion_id'] = $expert_opinion_id;
                                $pcdl['clinical_diagnosis_id'] = $clinicaldiagnosis[$i]->clinical_diagnosis_id;
                                $pcdl['disease_name'] = $clinicaldiagnosis[$i]->disease_name;
                                $pcdl['code'] = $code;
                                $pcdl['created_by'] = $expInfo->referred_doctor_id; 
                                $pcdl['created_date_time'] = date('Y-m-d H:i:s');
                                $res = $this->Generic_model->insertData('patient_cd_line_items',$pcdl);
                                // echo $this->db->last_query();
                                if($res)
                                    $cnt++;
                            // }
                            // else
                            // {
                            //     $param = "Already in the List";
                            //     $this->response(array('code'=>'201','message'=>'Already In the List','result'=>$param));   
                            // }
                        }
                        if($cnt > 0)
                        {
                            $param = $this->getLatestClinicalDiagnosis($expert_opinion_id);
                            $this->response(array('code'=>'200','message'=>'Patient Clinical Diagnosis Created Successfully','result'=>$param)); 
                        }
                    }

                }
            }
            else
            {
                $param['result'] = "Invalid Inputs";
                $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));
            }
            
        }
        else
        {
            $param['result'] = "Invalid Inputs";
            $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));
        }
    }

    public function clinicalDiagnosis_get($expert_opinion_id){
        if($expert_opinion_id != "")
        {
            $expInfo = $this->db->select("*")->from("expert_opinion")->where("expert_opinion_id",$expert_opinion_id)->get()->row();
            $cdInfo = $this->db->query("select * from patient_clinical_diagnosis pcd, patient_cd_line_items pcl where pcd.patient_clinical_diagnosis_id=pcl.patient_clinical_diagnosis_id and pcd.expert_opinion_id='".$expert_opinion_id."' and pcl.created_by='".$expInfo->doctor_id."'")->result();
            // echo count($cdInfo);
            if(count($cdInfo)>0)
            {
                $i = 0;
                foreach($cdInfo as $value)
                {
                    $param['clinicaldiagnosis'][$i]['patient_cd_line_item_id'] = $value->patient_cd_line_item_id;
                    $param['clinicaldiagnosis'][$i]['clinical_diagnosis_id'] = $value->clinical_diagnosis_id;
                    $param['clinicaldiagnosis'][$i]['cd_id'] = $value->patient_cd_line_item_id;
                    $param['clinicaldiagnosis'][$i]['disease_name'] = $value->disease_name;
                    $i++;
                }
            }
            else
            {
                $param = [];
            }
            $this->response(array('code'=>'201','message'=>'Invalid','result'=>$param));

        }
        else
        {
            $this->response(array('code'=>'201','message'=>'Invalid','result'=>'Invalid Inputs'));
        }
    }

    function getLatestClinicalDiagnosis($expert_opinion_id){
        $cdInfo = $this->db->query("select * from patient_clinical_diagnosis where expert_opinion_id='".$expert_opinion_id."'")->row();
        $param['appointment_id'] = $cdInfo->appointment_id;
        $param['clinic_id'] = $cdInfo->clinic_id;
        $i = 0;
        $clinicaldiagnosisInfo = $this->db->query("select * from patient_cd_line_items where patient_clinical_diagnosis_id='".$cdInfo->patient_clinical_diagnosis_id."'  order by patient_cd_line_item_id DESC LIMIT 1")->row();
        if(count($clinicaldiagnosisInfo)>0)
        {       
            $param['clinicaldiagnosis'][0]['clinical_diagnosis_id'] = $clinicaldiagnosisInfo->clinical_diagnosis_id;
            $param['clinicaldiagnosis'][0]['code'] = $clinicaldiagnosisInfo->code;
            $param['clinicaldiagnosis'][0]['disease_name'] = $clinicaldiagnosisInfo->disease_name;
            $param['clinicaldiagnosis'][0]['patient_cd_line_item_id'] = $clinicaldiagnosisInfo->patient_cd_line_item_id;
        }
        else
        {
            $param['clinicaldiagnosis'] = [];
        }
        $param['doctor_id'] = $cdInfo->doctor_id;
        $param['patient_id'] = $cdInfo->patient_id;
        $param['umr_no'] = $cdInfo->umr_no;
        
        return $param;
    }
    
    function getLatestInvestigations($expert_opinion_id){
        $invInfo = $this->db->query("select * from patient_investigation where expert_opinion_id='".$expert_opinion_id."'")->row();
        $param['appointment_id'] = $invInfo->appointment_id;
        $param['clinic_id'] = $invInfo->clinic_id;
        $i = 0;
        $investigationsInfo = $this->db->query("select * from patient_investigation_line_items where patient_investigation_id='".$invInfo->patient_investigation_id."' order by patient_investigation_line_item_id DESC LIMIT 1")->row();
        if(count($investigationsInfo)>0)
        {
            $param['investigations_list'][0]['category'] = $investigationsInfo->clinical_diagnosis_id;
            $param['investigations_list'][0]['investigation_id'] = $investigationsInfo->investigation_id;
            $param['investigations_list'][0]['investigation_name'] = $investigationsInfo->investigation_name;
            $param['investigations_list'][0]['patient_investigation_line_item_id'] = $investigationsInfo->patient_investigation_line_item_id;
        }
        else
        {
            $param['investigations_list'] = [];
        }
        $param['patient_id'] = $invInfo->patient_id;
        $param['umr_no'] = $invInfo->umr_no;
        return $param;
    }

    // Saving Investigations
    public function investigations_submit_post() 
    {
        if(!empty(isset($_POST)))
        {
            extract($_POST);
            if(!empty($appointment_id))
            {
                $expInfo = $this->db->select("*")->from("expert_opinion")->where("expert_opinion_id='".$expert_opinion_id."'")->get()->row();
                $invInfo = $this->db->select("*")->from("patient_investigation")->where("appointment_id='".$appointment_id."'")->get()->row();
                if(count($invInfo)>0)
                {   
                    $pcd['expert_opinion_id'] = $expert_opinion_id;
                    $pcd['modified_by'] = $expInfo->referred_doctor_id;
                    $pcd['modified_date_time'] = date("Y-m-d H:i:s");
                    $this->Generic_model->updateData("patient_investigation",$pcd,array('patient_investigation_id'=>$invInfo->patient_investigation_id));
                    if(count($investigations)>0)
                    {
                        $cnt = 0;
                        $investigations = json_decode($investigations);
                        for($i = 0;$i < count($investigations);$i++)
                        {
                            $this->db->select("*");
                            $this->db->from("patient_investigation_line_items");
                            $this->db->where("patient_investigation_id='".$invInfo->patient_investigation_id."'");
                            $this->db->where("investigation_id='".$investigations[$i]->investigation_id."'");
                            $check = $this->db->get()->num_rows();

                            // if($check<=0)
                            // {
                                $pcdl['patient_investigation_id'] = $invInfo->patient_investigation_id;
                                $pcdl['expert_opinion_id'] = $expert_opinion_id;
                                $pcdl['investigation_id'] = ($investigations[$i]->investigation_id=="")?0:$investigations[$i]->investigation_id;
                                // $pcdl['investigation_code'] = $investigations[$i]->investigation_code;
                                $pcdl['investigation_name'] = $investigations[$i]->investigation_name;
                                $pcdl['created_by'] = $user_id; 
                                $pcdl['created_date_time'] = date('Y-m-d H:i:s');
                                // print_r($pcdl);
                                $res = $this->Generic_model->insertData('patient_investigation_line_items',$pcdl);
                                if($res)
                                    $cnt++;
                            // }
                            // else
                            // {
                            //     $param = "Already in the list";
                            //     $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));   
                            // }
                        }
                        if($cnt > 0)
                        {
                            $param = $this->getLatestInvestigations($expert_opinion_id);
                            $this->response(array('code'=>'200','message'=>'Success','result'=>$param));   
                        }
                    }                    
                }
                else
                {
                    $appInfo = $this->db->query("select * from appointments where appointment_id='".$appointment_id."'")->row();
                    $pcd['clinic_id'] = $appInfo->clinic_id;
                    $pcd['doctor_id'] = $appInfo->doctor_id;
                    $pcd['patient_id'] = $appInfo->patient_id;
                    $pcd['umr_no'] = $appInfo->umr_no;
                    $pcd['expert_opinion_id'] = $expert_opinion_id;
                    $pcd['appointment_id'] = $appointment_id;
                    $pcd['created_by'] = $expInfo->referred_doctor_id;
                    $pcd['created_date_time'] = date('Y-m-d H:i:s');
                    $pcd['modified_date_time'] = date('Y-m-d H:i:s');
                    $pcd_id = $this->Generic_model->insertDataReturnId('patient_investigation', $pcd);
                    if(count($investigations)>0)
                    {
                        $cnt = 0;
                        $investigations = json_decode($investigations);
                        for($i = 0;$i < count($investigations);$i++)
                        {
                            $this->db->select("*");
                            $this->db->from("patient_investigation_line_items");
                            $this->db->where("patient_investigation_id='".$pcd_id."'");
                            $this->db->where("investigation_id='".$investigations[$i]->investigation_id."'");
                            $check = $this->db->get()->num_rows();

                            // if($check<=0)
                            // {
                                $pcdl['patient_investigation_id'] = $pcd_id;
                                $pcdl['expert_opinion_id'] = $expert_opinion_id;
                                $pcdl['investigation_id'] = ($investigations[$i]->investigation_id=="")?0:$investigations[$i]->investigation_id;
                                // $pcdl['investigation_code'] = $investigations[$i]->investigation_code;
                                $pcdl['investigation_name'] = $investigations[$i]->investigation_name;
                                $pcdl['created_by'] = $expInfo->referred_doctor_id; 
                                $pcdl['created_date_time'] = date('Y-m-d H:i:s');
                                // print_r($pcdl);
                                $res = $this->Generic_model->insertData('patient_investigation_line_items',$pcdl);
                                if($res)
                                    $cnt++;
                            // }
                            // else
                            // {
                            //     $param = "Already in the list";
                            //     $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));   
                            // }
                        }
                        if($cnt > 0)
                        {
                            $param = $this->getLatestInvestigations($expert_opinion_id);
                            $this->response(array('code'=>'200','message'=>'Success','result'=>$param));   
                        }
                    }

                }
            }
            else
            {
                $param = "Invalid Inputs";
                $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));
            }
            
        }
        else
        {
            $param = "Invalid Inputs";
            $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));
        }
    }
    
    // Saving Investigations
public function prescriptions_post() 
{
    if(!empty(isset($_POST)))
    {
        extract($_POST);
        // echo json_encode($prescriptions);
        // $prescriptions = json_decode($prescriptions);
        if(!empty($appointment_id))
        {
            $expInfo = $this->db->select("*")->from("expert_opinion")->where("expert_opinion_id='".$expert_opinion_id."'")->get()->row();
            $presInfo = $this->db->select("*")->from("patient_prescription")->where("appointment_id='".$appointment_id."'")->get()->row();
            if(count($presInfo)>0)
            {   
                $pcd['expert_opinion_id'] = $expert_opinion_id;
                $pcd['modified_by'] = $expInfo->referred_doctor_id;
                $pcd['modified_date_time'] = date("Y-m-d H:i:s");
                $this->Generic_model->updateData("patient_prescription",$pcd,array('patient_prescription_id'=>$presInfo->patient_prescription_id));
                if(count($prescriptions)>0)
                {
                    $cnt = 0;
                    $prescription_drug = json_decode($prescriptions);
                    for($i = 0;$i < count($prescriptions);$i++)
                    {
                        $this->db->select("*");
                        $this->db->from("patient_prescription_drug");
                        $this->db->where("patient_prescription_id='".$pcd_id."'");
                        $this->db->where("drug_id='".$prescription_drug[$i]->drug_id."'");
                        $check = $this->db->get()->num_rows();

                        // if($check<=0)
                        // {
                        // $prescription_drug = $prescription;
                
                        $i = 0;
                        $patient_prescription_edit['patient_prescription_id'] = $presInfo->patient_prescription_id;
                        $patient_prescription_edit['expert_opinion_id'] = $expert_opinion_id;
                        $patient_prescription_edit['drug_id'] = $prescription_drug[$i]->drug_id;
                        $patient_prescription_edit['medicine_name'] = $prescription_drug[$i]->medicine_name;
                        $patient_prescription_edit['day_schedule'] = $prescription_drug[$i]->day_schedule;
                        $patient_prescription_edit['day_dosage'] = $prescription_drug[$i]->day_dosage;
                        $patient_prescription_edit['dosage_frequency'] = $prescription_drug[$i]->dosage_frequency;
                        $patient_prescription_edit['preffered_intake'] = $prescription_drug[$i]->preffered_intake;
                        $patient_prescription_edit['preffered_time_gap'] = $prescription_drug[$i]->preffered_time_gap;
                        $patient_prescription_edit['dose_course'] = $prescription_drug[$i]->dose_course;
                        $patient_prescription_edit['quantity'] = $prescription_drug[$i]->quantity;
                        $patient_prescription_edit['mode'] = $prescription_drug[$i]->mode;
                        $patient_prescription_edit['status'] = 1;
                        $patient_prescription_edit['drug_status'] = $prescription_drug[$i]->drug_status;
                        $patient_prescription_edit['created_by'] = $user_id;
                        $patient_prescription_edit['modified_by'] = $user_id;
                        $patient_prescription_edit['created_date_time'] = date('Y-m-d H:i:s');
                        $patient_prescription_edit['modified_date_time'] = date('Y-m-d H:i:s');
                        $patient_prescription_edit['remarks'] = $prescription_drug[$i]->remarks;
                        $patient_prescription_edit['drug_dose'] = $prescription_drug[$i]->drug_dose;
                        $patient_prescription_edit['dosage_unit'] = $prescription_drug[$i]->dosage_unit;
                
                        $updateRes = $this->Generic_model->insertData('patient_prescription_drug', $patient_prescription_edit);
                        if($updateRes)
                            $cnt++;
                        // }
                        // else
                        // {
                        //     $param = "Already in the list";
                        //     $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));   
                        // }
                    }
                    if($cnt > 0)
                    {
                        $param = $this->getLatestPrescriptions($expert_opinion_id);
                        $this->response(array('code'=>'200','message'=>'Success','result'=>$param));   
                    }
                }                    
            }
            else
            {
                $appInfo = $this->db->query("select * from appointments where appointment_id='".$appointment_id."'")->row();
                $pcd['clinic_id'] = $appInfo->clinic_id;
                $pcd['doctor_id'] = $appInfo->doctor_id;
                $pcd['patient_id'] = $appInfo->patient_id;
                // $pcd['umr_no'] = $appInfo->umr_no;
                $pcd['expert_opinion_id'] = $expert_opinion_id;
                $pcd['appointment_id'] = $appointment_id;
                $pcd['created_by'] = $expInfo->referred_doctor_id;
                $pcd['created_date_time'] = date('Y-m-d H:i:s');
                $pcd['modified_date_time'] = date('Y-m-d H:i:s');
                $patient_prescription_id = $this->Generic_model->insertDataReturnId('patient_prescription', $pcd);
                if(count($prescriptions)>0)
                {
                    $cnt = 0;
                    $prescription_drug = json_decode($prescriptions);
                    for($i = 0;$i < count($prescription_drug);$i++)
                    {
                        $this->db->select("*");
                        $this->db->from("patient_prescription_drug");
                        $this->db->where("patient_prescription_id='".$pcd_id."'");
                        $this->db->where("drug_id='".$prescription_drug[$i]->drug_id."'");
                        $check = $this->db->get()->num_rows();

                        // if($check<=0)
                        // {             
                        $prescription_drug_insert[$i]['patient_prescription_id'] = $patient_prescription_id;
                        $prescription_drug_insert[$i]['expert_opinion_id'] = $expert_opinion_id;
                        $prescription_drug_insert[$i]['drug_id'] = $prescription_drug[$i]->drug_id;
                        $prescription_drug_insert[$i]['medicine_name'] = $prescription_drug[$i]->medicine_name;
                        $prescription_drug_insert[$i]['day_schedule'] = $prescription_drug[$i]->day_schedule;
                        $prescription_drug_insert[$i]['day_dosage'] = $prescription_drug[$i]->day_dosage;
                        $prescription_drug_insert[$i]['dosage_frequency'] = $prescription_drug[$i]->dosage_frequency;
                        $prescription_drug_insert[$i]['preffered_intake'] = $prescription_drug[$i]->preffered_intake;
                        $prescription_drug_insert[$i]['preffered_time_gap'] = $prescription_drug[$i]->preffered_time_gap;
                        $prescription_drug_insert[$i]['dose_course'] = $prescription_drug[$i]->dose_course;
                        $prescription_drug_insert[$i]['dose_course'] = $prescription_drug[$i]->dose_course;
                        $prescription_drug_insert[$i]['quantity'] = $prescription_drug[$i]->quantity;
                        $prescription_drug_insert[$i]['mode'] = $prescription_drug[$i]->mode;
                        $prescription_drug_insert[$i]['status'] = 1;
                        $prescription_drug_insert[$i]['drug_status'] = $prescription_drug[$i]->drug_status;
                        $prescription_drug_insert[$i]['created_by'] = $user_id;
                        $prescription_drug_insert[$i]['modified_by'] = $user_id;
                        $prescription_drug_insert[$i]['created_date_time'] = date('Y-m-d H:i:s');
                        $prescription_drug_insert[$i]['modified_date_time'] = date('Y-m-d H:i:s');
                        $prescription_drug_insert[$i]['remarks'] = $prescription_drug[$i]->remarks;
                        $prescription_drug_insert[$i]['drug_dose'] = $prescription_drug[$i]->drug_dose;
                        $prescription_drug_insert[$i]['dosage_unit'] = $prescription_drug[$i]->dosage_unit;

                        $parameters['prescription'][$i]['patient_prescription_drug_id'] = $this->Generic_model->insertDataReturnId('patient_prescription_drug', $prescription_drug_insert[$i]);
                        $parameters['prescription'][$i]['patient_prescription_id'] = $patient_prescription_id;
                        $res = $this->Generic_model->insertData('patient_investigation_line_items',$pcdl);
                        if($res)
                            $cnt++;
                        // }
                        // else
                        // {
                        //     $param = "Already in the list";
                        //     $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));   
                        // }
                    }
                    if($cnt > 0)
                    {
                        $param = $this->getLatestPrescriptions($expert_opinion_id);
                        $this->response(array('code'=>'200','message'=>'Success','result'=>$param));   
                    }
                }

            }
        }
        else
        {
            $param = "Invalid Inputs";
            $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));
        }
        
    }
    else
    {
        $param = "Invalid Inputs";
        $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));
    }
}

    // Get FIs List`
    public function ViewFI_get($appointment_id){
        if(isset($_GET)){
            $check = $this->db->query("select * from expert_opinion where appointment_id='".$appointment_id."'")->result();
            if(count($check) > 0){
                $i = 0;
                foreach($check as $value){
                    $data['opinion_list'][$i]['doctor_name'] = getDoctorName($value->referred_doctor_id);
                    $data['opinion_list'][$i]['doctor_id'] = $value->referred_doctor_id;
                    $data['opinion_list'][$i]['expert_opinion_id'] = $value->expert_opinion_id;
                    $data['opinion_list'][$i]['date'] = date('d-m-Y h:i a', strtotime($value->created_date_time));
                    $i++;
                }
                $this->response(array('code'=>'200','message'=>'Success','result'=>$data));
            }
            else{
                $param = "No Expert Opinion Found";
                $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));   
            }
        }
        else{
            $param = "Invalid Inputs";
            $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));   
        }
    }

    // patient summary list
    public function summaryList_post(){
        if(isset($_POST)){
            extract($_POST);
            $appointments = $this->Generic_model->getAllRecords('appointments', array('patient_id'=>$patient_id));
            if(count($appointments) > 0){
                $a = 0;
                foreach($appointments as $app){
                    $docInfo = doctorDetails($app->doctor_id);            
                    $para['pa_appointment'][$a]['expert_opinion_status'] = 0;
                    $para['pa_appointment'][$a]['appointment_id'] = $app->appointment_id;
                    if($app->course_in_hospital!="" || $app->course_in_hospital!=NULL){
                        $para['pa_appointment'][$a]['discharge_status'] = 1;
                    }
                    else{
                        $para['pa_appointment'][$a]['discharge_status'] = 0;   
                    }
                    $para['pa_appointment'][$a]['appointment_date'] = $app->appointment_date;
                    $para['pa_appointment'][$a]['doctor'] = getDoctorName($app->doctor_id);
                    $para['pa_appointment'][$a]['department'] = $docInfo->department_name;
                    $para['pa_appointment'][$a]['pdf_status'] = $app->pdf_status;
                    $a++;
                }

                foreach($appointments as $app){

                    $check = $this->db->query("select * from expert_opinion where appointment_id='".$app->appointment_id."' and referred_doctor_id='".$doctor_id."'")->result();
                    if(count($check) > 0){
                        foreach($check as $value){
                            $cdInfo = $this->Generic_model->getJoinRecords('patient_clinical_diagnosis pcd','patient_cd_line_items pcdl','pcd.patient_clinical_diagnosis_id=pcdl.patient_clinical_diagnosis_id',array('appointment_id'=>$app->appointment_id,'pcdl.expert_opinion_id'=>$value->expert_opinion_id),'','*');
                            $invInfo = $this->Generic_model->getJoinRecords('patient_investigation pin','patient_investigation_line_items pinl','pin.patient_investigation_id=pinl.patient_investigation_id',array('appointment_id'=>$app->appointment_id,'pinl.expert_opinion_id'=>$value->expert_opinion_id),'','*');
                            $drugInfo = $this->Generic_model->getJoinRecords('patient_prescription pp','patient_prescription_drug ppd','pp.patient_prescription_id=ppd.patient_prescription_id',array('appointment_id'=>$app->appointment_id,'ppd.expert_opinion_id'=>$value->expert_opinion_id),'','*');
                            // echo count($cdInfo);
                            // echo count($invInfo);
                            // echo count($drugInfo);

                            if(count($cdInfo) <= 0 && count($invInfo) <= 0 && count($drugInfo) <= 0){
                                continue;
                            }

                            $docInfo = doctorDetails($value->referred_doctor_id);      
                            $para['pa_appointment'][$a]['expert_opinion_status'] = 1;
                            $para['pa_appointment'][$a]['appointment_id'] = $app->appointment_id;
                            $para['pa_appointment'][$a]['doctor'] = getDoctorName($value->referred_doctor_id);
                            $para['pa_appointment'][$a]['doctor_id'] = $value->referred_doctor_id;
                            $para['pa_appointment'][$a]['expert_opinion_id'] = $value->expert_opinion_id;
                            $para['pa_appointment'][$a]['department'] = $docInfo->department_name;
                            $para['pa_appointment'][$a]['appointment_date'] = date('Y-m-d', strtotime($value->created_date_time));
                            $a++;
                        }
                    }
                }
            }
            else{
                $a = 0;
                // $appointments = $this->Generic_model->getAllRecords('expert_opinion', array('patient_id'=>$patient_id));
                $check = $this->db->query("select * from expert_opinion where patient_id='".$patient_id."' and referred_doctor_id='".$doctor_id."'")->result();
                if(count($check) > 0){
                    foreach($check as $value){
                        $docInfo = doctorDetails($value->referred_doctor_id);      
                        $para['pa_appointment'][$a]['expert_opinion_status'] = 1;
                        $para['pa_appointment'][$a]['appointment_id'] = $value->appointment_id;
                        $para['pa_appointment'][$a]['doctor'] = getDoctorName($value->referred_doctor_id);
                        $para['pa_appointment'][$a]['doctor_id'] = $value->referred_doctor_id;
                        $para['pa_appointment'][$a]['expert_opinion_id'] = $value->expert_opinion_id;
                        $para['pa_appointment'][$a]['department'] = $docInfo->department_name;
                        $para['pa_appointment'][$a]['appointment_date'] = date('Y-m-d', strtotime($value->created_date_time));
                        $a++;
                    }
                }
            }
            $this->response(array('code'=>'200','message'=>'Success','result'=>$para));  
        }
        else{
            $param = "Invalid Inputs";
            $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));   
        }
    }


    // get webview of expert opinion 
    public function eo_Webview_get($expert_opinion_id)
    {
        if(!empty($expert_opinion_id))
        {
            $param['webPage'] = base_url('PdfView/eo_webview/'.$expert_opinion_id);
            // $param['webPage'] = base_url('OnlinePayment/eo_payment_webview/UMDEXP326');
            
            $this->response(array('code'=>'200','message'=>'Success','result'=>$param));
        }
        else
        {
            $param = "Invalid Inputs";
            $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$param));
        }
    }

    // Prescriptions List 
    function getLatestPrescriptions($expert_opinion_id){
        $presInfo = $this->db->query("select * from patient_prescription where expert_opinion_id='".$expert_opinion_id."'")->row();
        // echo $this->db->last_query();
        $param['appointment_id'] = $presInfo->appointment_id;
        $param['clinic_id'] = $presInfo->clinic_id;
        $param['doctor_id'] = $presInfo->doctor_id;
        $param['patient_id'] = $presInfo->patient_id;
        $prescriptionsInfo = $this->db->query("select * from patient_prescription pp,patient_prescription_drug ppd where pp.patient_prescription_id=ppd.patient_prescription_id and pp.expert_opinion_id='".$expert_opinion_id."' order by ppd.patient_prescription_drug_id DESC LIMIT 1")->row();
        if(count($prescriptionsInfo)>0)
        {
            $i = 0;
            foreach($prescriptionsInfo as $value)
            {
                $param['prescription'][$i]['composition'] = $value->composition;
                $param['prescription'][$i]['day_schedule'] = $prescriptionsInfo->day_schedule;
                $param['prescription'][$i]['dosage_unit'] = $prescriptionsInfo->dosage_unit;
                $param['prescription'][$i]['dose_course'] = $prescriptionsInfo->dose_course;
                $param['prescription'][$i]['drug_dose'] = $prescriptionsInfo->drug_dose;
                $param['prescription'][$i]['drug_id'] = $prescriptionsInfo->drug_id;
                $param['prescription'][$i]['drug_status'] = $prescriptionsInfo->drug_status;
                $param['prescription'][$i]['medicine_name'] = $prescriptionsInfo->medicine_name;
                $param['prescription'][$i]['mode'] = $prescriptionsInfo->mode;
                $param['prescription'][$i]['preffered_intake'] = $prescriptionsInfo->preffered_intake;
                $param['prescription'][$i]['quantity'] = $prescriptionsInfo->quantity;
                $param['prescription'][$i]['remarks'] = $prescriptionsInfo->remarks;
                $param['prescription'][$i]['patient_prescription_drug_id'] = $prescriptionsInfo->patient_prescription_drug_id;
                $param['prescription'][$i]['patient_prescription_id'] = $prescriptionsInfo->patient_prescription_id;
                $i++;
            }
        }
        else
        {
            $param['prescription'] = [];
        }
        return $param;
    }

    // Saving Prescription
    // public function prescriptions_submit_post()
    // {
    //     if(!empty(isset($_POST)))
    //     {
    //         extract($_POST);
    //         // if()

    //     }
    //     else
    //     {
    //         $param = "UnAuthorized Access";
    //         $this->response(array('code'=>'201','message'=>'Error Occurred','result'=>$param));
    //     }
    // }


    public function walletAmount_get($doctor_id,$expert_doctor_id)
    {
        $this->db->select("doctors.doctor_id,doctors.first_name,doctors.specialist_type,doctor_wallet_prices.amount as walletAmount");
        $this->db->from("doctors");
        $this->db->join("doctor_wallet_prices","doctors.doctor_id=doctor_wallet_prices.doctor_id");
        $this->db->where("doctors.doctor_id = '".$doctor_id."'");
        $docInfo['doctor_details'] = $this->db->get()->result();
        // $this->db->select("doctors.doctor_id,doctors.first_name,doctors.specialist_type,
        // wallet_specilization_prices.amount as walletAmount");
        // $this->db->from("doctors");
        // $this->db->join("wallet_specilization_prices","doctors.specialist_type=wallet_specilization_prices.type");
        // $this->db->where("doctors.doctor_id = '".$doctor_id."'");
        // $docInfo['doctor_details'] = $this->db->get()->result();

        $this->db->select("doctors.doctor_id as id,doctors.first_name,doctors.specialist_type,wallet_specilization_prices.amount as price");
        $this->db->from("doctors");
        $this->db->join("wallet_specilization_prices","doctors.specialist_type=wallet_specilization_prices.type");
        $this->db->where("doctors.doctor_id = '".$expert_doctor_id."'");
        $docInfoo['doctor_detailss'] = $this->db->get()->result();

        if($docInfo['doctor_details'][0]->walletAmount >= $docInfoo['doctor_detailss'][0]->price)
        {
            $a = 1;
        }
        else
        {
            $a = 0;
        }
        
        $data['dotor_details'][0]['doctor_id'] = $docInfo['doctor_details'][0]->doctor_id;
        $data['dotor_details'][0]['doctor_name'] = $docInfo['doctor_details'][0]->first_name;
        $data['dotor_details'][0]['Wallet Amount'] = $docInfo['doctor_details'][0]->walletAmount;
        $data['dotor_details'][0]['expert_doctor_id'] = $docInfoo['doctor_detailss'][0]->id;
        $data['dotor_details'][0]['expert_doctor_name'] = $docInfoo['doctor_detailss'][0]->first_name;
        $data['dotor_details'][0]['expert_doctor_speciality'] = $docInfoo['doctor_detailss'][0]->specialist_type;
        $data['dotor_details'][0]['expert_doctor_priceValue'] = $docInfoo['doctor_detailss'][0]->price;
        $data['dotor_details'][0]['payment_button'] = $a;

        $this->response(array('code'=>'200','message'=>'Success','result'=>$data));

    }

    public function payment_post()
    {
        extract($_POST);
        if($wallet_amount > $expert_doctor_price && $a == 1)
        {
            $data['amount'] = $wallet_amount - $expert_doctor_price;
            $res = $this->Generic_model->updateData("doctor_wallet_prices",$data,array("doctor_id"=>$doctor_id));
            $param['result'] = "Updated";
            $this->response(array('code'=>'200','message'=>'Success','result'=>$param));
        }
        else
        {
            $param['result'] = "Insufficient Amount";
            $this->response(array('code'=>'200','message'=>'Check','result'=>$param));
        }
    }

    // Get My Wallet Amount
    public function myWallet_get($docId)
    {
        if(isset($_GET))
        {
            $walletInfo = $this->db->query("select amount from doctor_wallet_prices where doctor_id='".$docId."'")->row();
            $param['walletAmount'] = number_format($walletInfo->amount,2);
            $this->response(array('code'=>'200','message'=>'Success','result'=>$param));
        }
        else
        {
            $param = "UnAuthorized Access";
            $this->response(array('code'=>'200','message'=>'UnAuthorized Access'));
        }
    }

    // Get My Wallet History
    public function walletHistory_get($docId)
    {
        if(isset($_GET))
        {
            $walletHistory = $this->db->query("select *,ew.payment_status as pstatus from eo_wallet_history ew,expert_opinion eo where eo.expert_opinion_id=ew.expert_opinion_id and ew.doctor_id='".$docId."' order by ew.created_date_time DESC")->result();
            $i = 0;
            foreach($walletHistory as $value)
            {
                if($value->pstatus == 0)
                    $pstatus = "Pending";
                elseif($value->pstatus == 1)
                    $pstatus = "Completed";    
                elseif($value->pstatus == 2)
                    $pstatus = "Cancelled";    
                $param['walletHistory'][$i]['amount'] = $value->transaction_amount;
                $param['walletHistory'][$i]['transaction_type'] = $value->transaction_type;
                $param['walletHistory'][$i]['to_doc'] = getDoctorName($value->referred_doctor_id); 
                $param['walletHistory'][$i]['created_date'] = date("d-m-Y h:i A", strtotime($value->created_date_time));
                $param['walletHistory'][$i]['payment_status'] = $pstatus; 
                $i++;
            }

            $walletInfo = $this->db->query("select * from eo_wallet_history where doctor_id='".$docId."' and expert_opinion_id='0' order by created_date_time DESC")->result();
            foreach($walletInfo as $value)
            {
                if($value->payment_status == 0)
                    $pstatus = "Pending";
                elseif($value->payment_status == 1)
                    $pstatus = "Completed";    
                elseif($value->payment_status == 2)
                    $pstatus = "Cancelled";    
                $param['walletHistory'][$i]['amount'] = $value->transaction_amount;
                $param['walletHistory'][$i]['transaction_type'] = $value->transaction_type;
                $param['walletHistory'][$i]['to_doc'] = getDoctorName($value->created_by); 
                $param['walletHistory'][$i]['created_date'] = date("d-m-Y h:i A", strtotime($value->created_date_time)); 
                $param['walletHistory'][$i]['payment_status'] = $pstatus; 
                $i++;
            }
            // echo count($param['walletHistory']);
            if(count($param['walletHistory']) <= 0){
                $param['walletHistory'] = [];
            }

            $this->response(array('code'=>'200','message'=>'Success','result'=>$param));
        }
        else
        {
            $param = "UnAuthorized Access";
            $this->response(array('code'=>'200','message'=>'UnAuthorized Access'));
        }
    }
    

    // Send Message
    public function sendMessage_post()                                                                   
    {
        if(isset($_POST))
        {
            extract($_POST);
            $expInfo = $this->Generic_model->getSingleRecord('expert_opinion', array('expert_opinion_id'=>$expert_opinion_id));
            if($expInfo->status == 0 || $expInfo->status == 1){
                $data1['status'] = '2';
                $this->Generic_model->updateData('expert_opinion',$data1,array('expert_opinion_id'=>$expert_opinion_id));
            }
            // $expInfo = $this->db->query("select * from expert_opinion where expert_opinion_id='".$expert_opinion_id."'")->row();

            $data['expert_opinion_id'] = $expert_opinion_id;
            $data['sent_by'] = $user_id;
            $data['message'] = $comments;
            $data['created_by'] = $user_id;
            $data['created_date_time'] = date("Y-m-d H:i:s");
            $this->Generic_model->insertData("expert_opinion_conversations",$data);
            $param['result'] = "Message Sent";
            $this->response(array('code'=>'200','message'=>'Success','result'=>$param));
            $this->Generic_model->angularNotifications('', $expInfo->appointment_id, $expInfo->parent_doctor_id, '', 'EO_Comment', '');
            $this->Generic_model->angularNotifications('', $expInfo->appointment_id, $expInfo->referred_doctor_id, '', 'EO_Message_Sent', '');
        }
        else
        {
            $param = "UnAuthorized Access";
            $this->response(array('code'=>'200','message'=>'UnAuthorized Access'));
        }
    }

    // Send Message
    public function SendThanks_get($expert_opinion_id)                                                                   
    {
        if(isset($_GET))
        {
            extract($_POST);
            $data1['status'] = '2';
            $this->Generic_model->updateData('expert_opinion',$data1,array('expert_opinion_id'=>$expert_opinion_id));
            $expInfo = $this->db->query("select * from expert_opinion where expert_opinion_id='".$expert_opinion_id."'")->row();

            $data['expert_opinion_id'] = $expert_opinion_id;
            $data['sent_by'] = $expInfo->parent_doctor_id;
            $data['message'] = "Thanks For Giving Your Valuable Opinion.";
            $data['created_by'] = $expInfo->parent_doctor_id;
            $data['created_date_time'] = date("Y-m-d H:i:s");
            $this->Generic_model->insertData("expert_opinion_conversations",$data);
            $param = "Message Sent";
            $this->response(array('code'=>'200','message'=>'Success','result'=>$param));
            // $this->Generic_model->angularNotifications('', $expInfo->appointment_id, $expInfo->parent_doctor_id, '', 'EO_Comment', '');
            $this->Generic_model->angularNotifications('', $expInfo->appointment_id, $expInfo->referred_doctor_id, '', 'EO_Message_Sent', '');
        }
        else
        {
            $param = "UnAuthorized Access";
            $this->response(array('code'=>'200','message'=>'UnAuthorized Access'));
        }
    }

    //Get messages of Expert Opinion Id
    public function messages_get($id)
    {
        $expInfo = $this->db->query("select * from expert_opinion where expert_opinion_id='".$id."'")->row();
        // echo $this->db->last_query();
        if(count($expInfo)>0)
        {
            $messagesInfo = $this->db->query("select * from expert_opinion_conversations where expert_opinion_id='".$expInfo->expert_opinion_id."'")->result();
            $i = 0;
            if(count($messagesInfo)>0)
            {
                foreach($messagesInfo as $value)
                {
                    $param['messages'][$i]['sent_by'] = getDoctorName($value->sent_by);
                    $param['messages'][$i]['doctor_id'] = $value->sent_by;
                    $param['messages'][$i]['message'] = $value->message;
                    $i++;
                }
            }
            else
            {
                $param['messages'] = [];
            }
            
            $this->response(array('code'=>'200','message'=>'Success','result'=>$param));
        }
        else{
            $param['messages'] = [];
            $this->response(array('code'=>'201','message'=>'Error','result'=>$param));
        }
    }    

    // Get Latest Clinical Diagnosis
    public function latestClinicalDiagnosis_get($expert_opinion_id)
    {
        if(!empty($expert_opinion_id))
        {
            $check = $this->db->query("select * from expert_opinion where expert_opinion_id='".$expert_opinion_id."'")->row();
            if(count($check) > 0){
                $cdInfo = $this->db->query("select * from patient_clinical_diagnosis pcd, patient_cd_line_items pcdl where pcd.patient_clinical_diagnosis_id=pcdl.patient_clinical_diagnosis_id and pcd.appointment_id='".$check->appointment_id."' and pcdl.expert_opinion_id='".$expert_opinion_id."' and pcdl.created_by='".$check->referred_doctor_id."'")->result();
                if(count($cdInfo)>0)
                {       
                    $i = 0;
                    foreach($cdInfo as $value)
                    {
                        if($value->code == "" || $value->code == 0){
                            $cdMaster = $this->Generic_model->getSingleRecord('clinical_diagnosis', array('clinical_diagnosis_id' => $value->clinical_diagnosis_id));
                            if(count($cdMaster) > 0){
                                $code = $cdMaster->code;
                            }
                            else{
                                $code = "";
                            }
                        }
                        else{
                            $code = $value->code;
                        }
                        $param['clinicaldiagnosis'][$i]['patient_cd_line_item_id'] = $value->patient_cd_line_item_id;
                        $param['clinicaldiagnosis'][$i]['clinical_diagnosis_id'] = $value->patient_clinical_diagnosis_id;
                        $param['clinicaldiagnosis'][$i]['cd_id'] = $value->clinical_diagnosis_id;
                        $param['clinicaldiagnosis'][$i]['disease_name'] = $value->disease_name." ( ".$code." )";
                        $i++;
                    }
                }
                else
                {
                    $param['clinicaldiagnosis'] = [];
                }
            }
            else{
                $param['clinicaldiagnosis'] = [];
            }
            
            $this->response(array('code'=>'200','message'=>'success','result'=>$param));
        }
    }

    //get Latest Investigations
    public function latestInvestigations_get($expert_opinion_id){
        if(!empty($expert_opinion_id))
        {
            $check = $this->db->query("select * from expert_opinion where expert_opinion_id='".$expert_opinion_id."'")->row();
            if(count($check) > 0){
                $invInfo = $this->db->query("select * from patient_investigation pinv, patient_investigation_line_items pinvl where pinv.patient_investigation_id=pinvl.patient_investigation_id and pinv.appointment_id='".$check->appointment_id."' and pinvl.expert_opinion_id='".$expert_opinion_id."' and pinvl.created_by='".$check->referred_doctor_id."'")->result();
                if(count($invInfo)>0)
                {       
                    $i = 0;
                    foreach($invInfo as $value)
                    {
                        $param['investigations_list'][$i]['patient_investigation_line_item_id'] = $value->patient_investigation_line_item_id;
                        $param['investigations_list'][$i]['patient_investigation_id'] = $value->patient_investigation_id;
                        $param['investigations_list'][$i]['checked'] = 0;
                        $param['investigations_list'][$i]['investigation_id'] = $value->investigation_id;
                        $param['investigations_list'][$i]['investigation_name'] = $value->investigation_name;
                        $param['investigations_list'][$i]['clinic_status'] = 0;
                        $i++;
                    }
                }
                else
                {
                    $param['investigations_list'] = [];
                }
            }
            else{
                $param['investigations_list'] = [];
            }
            
            $this->response(array('code'=>'200','message'=>'success','result'=>$param));
        }
    }

    //get latest Prescriptions
    public function latestPrescriptions_get($expert_opinion_id){
        if(!empty($expert_opinion_id))
        {
            $check = $this->db->query("select * from expert_opinion where expert_opinion_id='".$expert_opinion_id."'")->row();
            if(count($check) > 0){
                $presInfo = $this->db->query("select * from patient_prescription pp, patient_prescription_drug ppd where pp.patient_prescription_id=ppd.patient_prescription_id and pp.appointment_id='".$check->appointment_id."' and ppd.expert_opinion_id='".$expert_opinion_id."' and ppd.created_by='".$check->referred_doctor_id."'")->result();
                // echo $this->db->last_query();
                if(count($presInfo)>0)
                {       
                    $i = 0;
                    foreach($presInfo as $value)
                    {
                        $param['prescription'][$i]['patient_prescription_drug_id'] = $value->patient_prescription_drug_id;
                        $param['prescription'][$i]['patient_prescription_id'] = $value->patient_prescription_id;
                        $param['prescription'][$i]['day_schedule'] = $value->day_schedule;
                        $param['prescription'][$i]['dose_course'] = $value->dose_course;
                        $param['prescription'][$i]['dosage_frequency'] = $value->dosage_frequency;
                        $param['prescription'][$i]['day_dosage'] = $value->day_dosage;
                        $param['prescription'][$i]['dose_course'] = $value->dose_course;
                        $param['prescription'][$i]['drug_dose'] = $value->drug_dose;
                        $param['prescription'][$i]['dosage_unit'] = $value->dosage_unit;
                        $param['prescription'][$i]['drug_id'] = $value->drug_id;
                        $param['prescription'][$i]['preffered_intake'] = $value->preffered_intake;
                        $param['prescription'][$i]['medicine_name'] = $value->medicine_name;
                        $param['prescription'][$i]['remarks'] = $value->remarks;
                        $param['prescription'][$i]['quantity'] = $value->quantity;
                        $i++;
                    }
                }
                else
                {
                    $param['prescription'] = [];
                }
            }
            else{
                $param['prescription'] = [];
            }
            
            $this->response(array('code'=>'200','message'=>'success','result'=>$param));
        }      
    }

    public function testing_post()                                                                   
    {
        //  extract($_POST);
        if(isset($_POST))
        {
            $key="fort367"; //posted merchant key from client
            $salt="hyliop876"; // add salt here from your credentials in payUMoney dashboard
            $txnid=$_POST['txnid']; //posted txnid from client
            $amount=$_POST["amount"]; //posted amount from client 
            $productInfo=$_POST["productInfo"]; // posted product info from client
            $firstName=$_POST["firstName"]; // posted firstname from and must be without space
            $email=$_POST["email"];

            /***************** USER DEFINED VARIABLES GOES HERE ***********************/
            //all varibles posted from client
            $udf1="umdaa1";
            $udf2="health2";
            $udf3="patient3";
            $udf4="care007";
            $udf5="private7";
            
            /***************** DO NOT EDIT ***********************/
    //  $payhash_str = $key . '|' . check($txnid) . '|' .check($amount)  . '|' .check($productInfo)  . '|' . check($firstName) . '|' . check($email) . '|' . check($udf1) . '|' . check($udf2) . '|' . check($udf3) . '|' . check($udf4) . '|' . check($udf5) . '||||||'. $salt;

 $payhash_str = $key . '|' . ($txnid) . '|' .($amount)  . '|' .($productInfo)  . '|' . ($firstName) . '|' . ($email) . '|' . ($udf1) . '|' . ($udf2) . '|' . ($udf3) . '|' . ($udf4) . '|' . ($udf5) . '||||||'. $salt;

            $hash = strtolower(hash('sha512', $payhash_str));
            /***************** DO NOT EDIT ***********************/
            // $errormsg = "";
            $status = 0;
            if (empty($txnid)){
                $errormsg = "Transaction ID should not be empty.";
                $arr['errormessage']=$errormsg;
            } elseif ($amount == null || $amount == "0"){
                $errormsg = "Amount should not be empty  or '0'.";
                $arr['errormessage']=$errormsg;
            } elseif ($productInfo == null){
                $errormsg = "Please fill Info.";
                $arr['errormessage']=$errormsg;
            } elseif ($firstName == null){
                $errormsg = "Please Enter Name.";
                $arr['errormessage']=$errormsg;
            } elseif(filter_var($email, FILTER_VALIDATE_EMAIL) == false){
                 $errormsg = "Invalid email.";
                 $arr['errormessage']=$errormsg;
            } else {
             
                $status = 200;
                $errormsg = "No errors Found";
                $arr['transaction_details']['hash_key'] = $hash;
                $arr['transaction_details']['status'] = $status;
                $arr['transaction_details']['errormessage']=$errormsg;
            }
           
            $output=$arr;
            $this->response(array('code'=>'200','message'=>'success','result'=>$output));
        }
        else
        {
            $param = "UnAuthorized Access";
            $this->response(array('code'=>'201','message'=>'UnAuthorized Access'));
        }
    }

    public function payuhash_post()                                                                   
    {
        extract($_POST);
        if(isset($_POST))
        {
            $key=$_POST["key"];
            $txnid=$_POST["txnid"];
            $amount=$_POST["amount"]; //Please use the amount value from database
            $productinfo=$_POST["productinfo"];
            $firstname=$_POST["firstname"];
            $email=$_POST["email"];
            $salt="HXs7wIkl2S"; //Please change the value with the live salt for production environment

            //hash sequence

            //String hashSequence = key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5||||||salt;


            $hashSeq = $key.'|'.$txnid.'|'.$amount.'|'.$productinfo.'|'.$firstname.'|'.$email.'|||||||||||'.$salt;

            $hash = hash("sha512", $hashSeq);       

            error_log("all posted variables:".print_r($_POST,true));

            if (empty($txnid)){
                $errormsg = "Transaction ID should not be empty.";
                $arr['errormessage']=$errormsg;
                  $output=$arr;
                $this->response(array('code'=>'201','message'=>'error','result'=>$output));
                 // $status = 201;
            } elseif ($amount == null || $amount == "0"){
                $errormsg = "Amount should not be empty  or '0'.";
                $arr['errormessage']=$errormsg;
                $output=$arr;
                $this->response(array('code'=>'201','message'=>'error','result'=>$output));
            } elseif ($productinfo == null){
                $errormsg = "Please fill Info.";
                $arr['errormessage']=$errormsg;
                $output=$arr;
                $this->response(array('code'=>'201','message'=>'error','result'=>$output));
            } elseif ($firstname == null){
                $errormsg = "Please Enter Name.";
                $arr['errormessage']=$errormsg;
                $output=$arr;
                $this->response(array('code'=>'201','message'=>'error','result'=>$output));
            } elseif ($key == null){
                $errormsg = "Key Not Found.";
                $arr['errormessage']=$errormsg;
                $output=$arr;
                $this->response(array('code'=>'201','message'=>'error','result'=>$output));
            } elseif(filter_var($email, FILTER_VALIDATE_EMAIL) == false){
                $errormsg = "Invalid email.";
                $arr['errormessage']=$errormsg;
                $output=$arr;
                $this->response(array('code'=>'201','message'=>'error','result'=>$output));
            } else {
             
                $status = 200;
                $errormsg = "";
                $arr['transaction_details']['hash_key'] = $hash;
                $arr['transaction_details']['status'] = $status;
                $arr['transaction_details']['internetHandlingFees'] = '8.26';
                $arr['transaction_details']['bookingFees'] = '7';
                $arr['transaction_details']['serviceTax'] = '18';
                $arr['transaction_details']['paymentGatewayCharges'] = '2.4';

            $output=$arr;
            $this->response(array('code'=>'200','message'=>'success','result'=>$output));
                // $arr['transaction_details']['errormessage']=$errormsg;
            }
           
            // $output=$arr;
            // $this->response(array('code'=>'200','message'=>'success','result'=>$output));
        }
        
    }

}
?> 