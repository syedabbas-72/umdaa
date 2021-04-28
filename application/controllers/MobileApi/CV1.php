<?php

defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);

ini_set('memory_limit', '-1');
define('headers', getallheaders());

require APPPATH . '/libraries/REST_Controller.php';

class CV1 extends REST_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->helper('file');
        // $this->load->library('PHPMailer');
        $this->load->library('mail_send', array('mailtype' => 'html'));
        $this->load->library('SMTP');
        $this->load->library('phpqrcode/qrlib');
        $this->load->library('zip');
        $this->load->model('Generic_model');

        if ($this->post('requestpara') != NULL || $this->post('requestpara') != '') {

            $fdata = json_decode($this->post('requestpara'));
            $this->load->library('upload');
            $config = array();
            //setting upload path for multipart data service
            if ($fdata->requestname == 'patient_registration') {
                $config['upload_path'] = './uploads/patients';
            } else if ($fdata->requestname == 'patient_registrations') {
                $config['upload_path'] = './uploads/patients';
            } else if ($fdata->requestname == 'patient_consent_form') {
                $config['upload_path'] = './uploads/patient_consentforms';
            } else if ($fdata->requestname == 'patient_update') {
                $config['upload_path'] = './uploads/patients';
            } else if ($fdata->requestname == 'patient_profile_edit') {
                $config['upload_path'] = './uploads/patients';
            }

            $config['allowed_types'] = 'jpg|JPG|png|PNG|csv|JPEG|jpeg';
            $filesCount = count($_FILES['file_i']['name']);

            if ($filesCount > 0) {
                for ($i = 0; $i < $filesCount; $i++) {
                    $_FILES['file_i[]']['name'] = $_FILES['file_i']['name'][$i];
                    $_FILES['file_i[]']['type'] = $_FILES['file_i']['type'][$i];
                    $_FILES['file_i[]']['tmp_name'] = $_FILES['file_i']['tmp_name'][$i];
                    $_FILES['file_i[]']['error'] = $_FILES['file_i']['error'][$i];
                    $_FILES['file_i[]']['size'] = $_FILES['file_i']['size'][$i];
                    $value = $i;

                    $this->upload->initialize($config);
                    $this->upload->do_upload('file_i[]');

                    $fname = $this->upload->data();
                    $fileName[$i] = $fname['file_name'];
                }
                $string_version = implode(',', $fileName);
            } else {
                $string_version = NULL;
            }

            $requestname = $fdata->requestname;

            if ($requestname == "patient_registration") {
                $pwd = $this->generateRandomString($length = 8);
                $ids = $this->db->query("SELECT p.profile_name,p.profile_id,r.role_id,r.role_name FROM profiles p left join roles r on r.role_name = p.profile_name WHERE p.profile_name='Patient'")->row();
                $user_reg['password'] = md5($pwd);
                $user_reg['email_id'] = DataCrypt($fdata->email_id,'encrypt');
                $user_reg['mobile'] = DataCrypt($fdata->mobile,'encrypt');
                $user_reg['user_type'] = 'patient';
                $user_reg['role_id'] = $ids->role_id;
                $user_reg['profile_id'] = $ids->profile_id;
                $user_reg['status'] = 1;
                $user_reg['created_by'] = $user_id;
                $user_reg['modified_by'] = $user_id;
                $user_reg['created_date_time'] = date('Y-m-d H:i:s');
                $user_reg['modified_date_time'] = date('Y-m-d H:i:s');

                $patient_id = $this->Generic_model->insertDataReturnId("users", $user_reg);

                $month = date('m');
                $year = date('y');
                $umr_no = 'OPD' . $month . $year . '0000' . $patient_id;
                $from = 'UMDAA';
                $to = DataCrypt($user_reg['email_id'],'decrypt');
                $subject = "New Patient Password";
                $header = "<p>Password : " . $pwd . "</p>";
                //$message = $message;
                //$this->mail_send->content_mail_ncl_all($from, $to, $subject, '', '',$header);

                $patient_username['username'] = $umr_no;
                $this->Generic_model->updateData("users", $patient_username, array('user_id' => $patient_id));

                $tempDir = './uploads/qrcodes/patients/';
                $codeContents = $patient_id;
                $qrname = $patient_id . md5($codeContents) . '.png';
                $pngAbsoluteFilePath = $tempDir . $qrname;
                $urlRelativeFilePath = base_url() . 'uploads/qrcodes/patients/' . $qrname;

                if (!file_exists($pngAbsoluteFilePath)) {
                    QRcode::png($codeContents, $pngAbsoluteFilePath);
                }

                $geoTag = 0; // instatiating a variable
                // get district name from district id
                if ($data->district_id != NULL || $data->district_id != '') {
                    $district = $this->db->query('select district_id, district_name from districts where district_id=' . $fdata->district_id)->row();
                    $geoTag = 1;
                } else {
                    $geoTag = 0;
                }

                // get state name from state id
                if ($fdata->state_id != NULL || $fdata->state_id != '') {
                    $statev = $this->db->query('select state_id, state_name from states where state_id=' . $fdata->state_id)->row();
                    $geoTag = 1;
                } else {
                    $geoTag = 0;
                }

                // Get GEO Location using state and district id
                if ($gepTag) { // if geoTag is 1 - then get latlong details form google
                    if ($this->input->post('pincode') == '') {
                        $staten = $statev->state_name;
                        $dis = $district->district_name;
                        $a1 = $dis . "," . $staten;
                    } else {
                        $staten = $statev->state_name;
                    }

                    $dis = $district->district_name;
                    $a1 = $dis . "," . $staten . "," . $this->input->post('pincode');

                    $address = $this->input->post('address') . "," . $a1;
                    
                    /* This API Key Need To Change Because This Key Belongs To Mr.Anil Kumar */
                    $api_key = "AIzaSyDExDMVdTUduc4nCmhAlSnOxl6ZT3yY0b0";
                    $url = "https://maps.google.com/maps/api/geocode/json?address=" . urlencode($address) . "&key=" . $api_key;

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $responseJson = curl_exec($ch);
                    curl_close($ch);

                    $response = json_decode($responseJson);

                    if ($response->status == 'OK') {
                        $latitude = $response->results[0]->geometry->location->lat;
                        $longitude = $response->results[0]->geometry->location->lng;
                        $lat_lng = $latitude . "," . $longitude;
                    } else {
                        $lat_lng = NULL;
                    }
                }

                $patient_reg['patient_id'] = $patient_id;
                $patient_reg['qrcode'] = $qrname;
                $patient_reg['umr_no'] = $umr_no;
                $patient_reg['clinic_id'] = $fdata->clinic_id;
                $patient_reg['title'] = $fdata->title;
                $patient_reg['first_name'] = $fdata->first_name;
                $patient_reg['middle_name'] = $fdata->middle_name;
                $patient_reg['last_name'] = $fdata->last_name;
                $patient_reg['occupation'] = $fdata->occupation;
                $patient_reg['country'] = $fdata->country;
                $patient_reg['last_name'] = $fdata->last_name;
                $patient_reg['gender'] = $fdata->gender;

                if(empty($fdata->date_of_birth)) { 
                    $patient_reg['date_of_birth'] = "";
                } else 
                {  
                    $patient_reg['date_of_birth'] = date('Y-m-d', strtotime($fdata->date_of_birth)); 
                }

                $patient_reg['age'] = $fdata->age;
                $patient_reg['age_unit'] = strtoupper(substr($fdata->age_unit, 0, 1));
                
                if($fdata->mobile != '' ||$fdata->mobile != null)
                    $patient_reg['mobile'] = DataCrypt($fdata->mobile,'encrypt');
                
                if($fdata->phone != '' ||$fdata->phone != null)
                    $patient_reg['alternate_mobile'] = DataCrypt($fdata->phone,'encrypt');
                
                if($fdata->email_id != '' ||$fdata->email_id != null)
                    $patient_reg['email_id'] = DataCrypt($fdata->email_id,'encrypt');

                $patient_reg['address_line'] = $fdata->address_line;
                $patient_reg['district_id'] = $fdata->district_id;
                $patient_reg['state_id'] = $fdata->state_id;
                $patient_reg['pincode'] = $fdata->pincode;
                $patient_reg['referred_by_type'] = $fdata->referred_by_type;
                $patient_reg['referred_by'] = $fdata->referred_by;
                $patient_reg['status'] = $fdata->status;
                $patient_reg['lat_long'] = $lat_long;
                $patient_reg['preferred_language'] = $fdata->preferred_language;
                $patient_reg['photo'] = $string_version;
                $patient_reg['created_by'] = $user_id;
                $patient_reg['created_date_time'] = date('Y-m-d H:i:s');
                $patient_reg['modified_by'] = $user_id;
                $patient_reg['modified_date_time'] = date('Y-m-d H:i:s');
                $this->Generic_model->insertData('patients', $patient_reg);

                $patient_con['patient_id'] = $patient_id;
                $patient_con['patient_condition'] = $fdata->condition_type;
                $patient_con['condition_months'] = $fdata->duration;
                $patient_con['created_by'] = $user_id;
                $patient_con['created_date_time'] = date('Y-m-d H:i:s');
                $patient_con['modified_by'] = $user_id;
                $patient_con['modified_date_time'] = date('Y-m-d H:i:s');

                if ($string_version == NULL) {
                    $path = NULL;
                } else {
                    $path = base_url() . 'uploads/patients/' . $string_version;
                }

                $param['patient_id'] = "$patient_id";
                $param['title'] = $fdata->title;
                $param['first_name'] = $fdata->first_name;
                $param['middle_name'] = $fdata->last_name;
                $param['last_name'] = $fdata->last_name;
                $param['umr_no'] = $umr_no;
                $param['occupation'] = $fdata->occupation;
                $param['country'] = $fdata->country;
                $param['referred_by'] = $patient_reg['referred_by'];
                $param['contact'] = $fdata->mobile;
                $param['age'] = $fdata->age . " " . $fdata->age_unit;
                $param['gender'] = $fdata->gender;
                $param['registartion_date'] = $patient_reg['created_date_time'];
                $param['patient_condition'] = $fdata->condition_type;
                $param['condition_months'] = $fdata->duration;
                $param['appointment_id'] = NULL;
                $param['appointment_date'] = NULL;
                $param['appointment_time'] = NULL;
                $param['status'] = "New";
                $param['check_in_time'] = NULL;
                if(empty($fdata->date_of_birth)) { 
                    $param['date_of_birth'] = "";
                } else 
                {  
                    $param['date_of_birth'] = date('Y-m-d', strtotime($fdata->date_of_birth)); 
                }
                $param['email'] = $fdata->email_id;
                $param['address'] = $address;
                $param['doctor_id'] = NULL;
                $param['doctor_name'] = NULL;
                $param['department'] = NULL;
                $param['color_code'] = NULL;
                $param['photo'] = $path;
                $param['qrcode'] = $urlRelativeFilePath;

                $clinic_info = $this->db->query("select * from clinics where clinic_id='" . $fdata->clinic_id . "'")->row();

                /* sending mail after successfull Registration of the parent */
                $from = 'UMDAA';
                $to = $fdata->email_id;
                $subject = "Successfully Registered With Umdaa";
                $header = "Dear " . $fdata->first_name . ' ' . $fdata->last_name . ",<br><br>
                Thank you for registering with " . $clinic_info->clinic_name . "<br><br>Have a good day";
 
                $result = array('code' => '200', 'message' => 'successfull', 'result' => $param, 'requestname' => $fdata->requestname);

            } 
            else if ($requestname == "patient_registrations") {

                $params = (array)$fdata;

                // Remove request name from the parameter
                unset($params['requestname']);
                unset($params['requesterid']); 


                if($params['referred_by_type'] == 'doctor'){
                	// Check if the referral doctor object
                   if(isset($params['referral_doctor'])){
	                    // if you are here then its a new referral doctor
                       $params['referral_doctor']->clinic_id = $params['clinic_id'];
                       $params['referral_doctor']->status = 1;
                       $params['referral_doctor']->created_by = $requesterid;
                       $params['referral_doctor']->modified_by = $requesterid;
                       $params['referral_doctor']->created_datetime = date('Y-m-d H:i:s');
                       $params['referral_doctor']->modified_datetime = date('Y-m-d H:i:s');

	                    // Create a new referral doctor
                       $params['referral_doctor_id'] = $this->Generic_model->insertDataReturnId('referral_doctors',$params['referral_doctor']);
                       $params['referred_by'] = $params['referral_doctor']->doctor_name;

                       unset($params['referral_doctor']);

                   }else{

	                	// Referral Doctor existing
                      $params['referral_doctor_id'] = $params['referred_by'];

						// Get the name of the referral doctor
                      $this->db->select('doctor_name');
                      $this->db->from('referral_doctors');
                      $this->db->where('rfd_id =', $params['referral_doctor_id']);

                      $referralDocRec = $this->db->get()->row();

                      $params['referred_by'] = $referralDocRec->doctor_name;

                  }	
              }                

              extract($params);

              // echo $mobile;

                $newPatient = 0; // Means that its not a new patient and so its a followup patient
                
                if($mobile != '' || $mobile != NULL){

                    // Check if the record is present in them db with mobile number and with the name
                    $patientChk = $this->db->select("patient_id, first_name, last_name, mobile, alternate_mobile")->from("patients")->where("mobile =",DataCrypt($mobile,'encrypt'))->get()->row();

                    // echo $this->db->last_query();
                    // exit();
                    if(count($patientChk) > 0){
                        // echo "1...";
                        /*
                        Senerio: 1
                        1. Mobile No. exists
                        2. Check if the person is same
                        */
                        $patientName = $first_name;

                        if(ucwords($patientName) == ucwords($patientChk->first_name)){
                            // echo "2...";
                            /*
                            3. Same person
                            4. No Registration required Send to book appointment
                            */
                            $params['patient_id'] = $patientChk->patient_id;
                            $newPatient = 0;
                        }else{
                            /* 
                            3. Not the same person
                            4. Check if there is a person with specified name exists with this mobile no. saved in the alternate mobile no.
                            */                                
                            $mobileRecChk = $this->db->select("patient_id, first_name, last_name, mobile, alternate_mobile, guardian_id")->from("patients")->where("first_name =",$first_name)->where("alternate_mobile =",DataCrypt($mobile,'encrypt'))->get()->result_array();

                            // echo $this->db->last_query();

                            if(count($mobileRecChk) > 0){
                                // echo "4...";
                                /*
                                5. Person exist with mobile saved in alternate mobile
                                6. No Registration required Send to book appointment   
                                */
                                $params['alternate_mobile'] = $mobile;
                                $params['mobile'] = '';
                                $params['guardian_id'] = $mobileRecChk[0]['guardian_id'];
                                $params['patient_id'] = $mobileRecChk[0]['patient_id'];
                                $newPatient = 0;    
                            }else{
                                // echo "5...";
                                /*
                                5. No Person exist with alternate mobile
                                6. New Registration required
                                */
                                $params['alternate_mobile'] = $mobile;
                                $params['mobile'] = '';
                                $params['guardian_id'] = $mobileRecChk[0]['patient_id'];
                                $newPatient = 1;    
                            }
                        }
                    }else{
                        // echo "6...";
                        // Resgiter as a new patient
                        $newPatient = 1;
                    }
                }

                // echo "Patient: ".$newPatient;
                // exit();


                if($newPatient == 0){

                    // Get Patient Complete Details
                    $patientRec = $this->db->select('patient_id,title,first_name,last_name,umr_no, date_of_birth, age,payment_status,clinic_id,referred_by_type,referred_by, email_id as email, referral_doctor_id,country,occupation,mobile,alternate_mobile,age,gender,location,state_id,district_id,preferred_language, address_line, pincode, photo, qrcode, created_date_time, status')->from('patients')->where('patient_id ='.$params['patient_id'])->get()->row(); 

                    if(count($patientRec) > 0) {
                        if($patientRec->mobile != null || $patientRec->mobile != ''){
                            $patientRec->mobile = DataCrypt($patientRec->mobile, 'decrypt');
                        }
                        if($patientRec->alternate_mobile != null || $patientRec->alternate_mobile != ''){
                            $patientRec->alternate_mobile = DataCrypt($patientRec->alternate_mobile, 'decrypt');
                        }
                        if($patientRec->email != null || $patientRec->email != ''){
                            $patientRec->email = DataCrypt($patientRec->email, 'decrypt');
                        }
                        $patientRec->priority = null;
                        $patientRec->patient_condition = null;
                        $patientRec->appointment_date = null;
                        $patientRec->appointment_time = null;
                        $patientRec->appointment_type = null;
                        $patientRec->condition_months = null;
                        $patientRec->appointment_id = null;
                        $patientRec->status = $patientRec->status;
                        $patientRec->check_in_time = null;
                        $patientRec->department = null;
                        $patientRec->doctor_comments = null;
                        $patientRec->address = $patientRec->address_line;
                        $patientRec->pincode = $patientRec->pincode;
                        $patientRec->doctor_id = null;
                        $patientRec->doctor_name = null;
                        $patientRec->department_id = null;
                        $patientRec->color_code = null;
                        $patientRec->photo = ($patientRec->photo ? base_url() . 'uploads/patients/'.$patientRec->photo : null);
                        $patientRec->qrcode = base_url() . 'uploads/qrcodes/patients/'.$patientRec->qrcode;
                        $patientRec->registration_date = $patientRec->created_date_time;
                        $patientRec->qb_user_id = null;
                        $patientRec->qb_user_login = null;
                        $patientRec->qb_user_fullname = null;
                        $patientRec->qb_user_tag = null;
                        $patientRec->immunization_status = 0;

                        $result = array('code' => '200', 'message' => 'Patient already exists with the mobile no. '.$mobile, 'result' => $patientRec, 'requestname' => $requestname);

                    }

                }elseif($newPatient == 1){

                    $pwd = $this->generateRandomString($length = 8);

                    $month = date('m');
                    $year = date('y');

                    // Generate UMR no.
                    // // Get last generated UMR No.                
                    // $last_umr = $this->db->select("umr_no")->from("patients")->order_by("patient_id DESC")->get()->row();

                    // // Generate UMR No.
                    // if(count($last_umr) > 0){
                    //     $umr_str   = trim($last_umr->umr_no);
                    //     $split_umr = substr($umr_str, 1, 4);
                    //     if ($split_umr == date("my")) {
                    //         $replace = str_replace("P" . $split_umr, "", $last_umr->umr_no);
                    //         $next_id = (++$replace);
                    //         $umr_no  = "P" . date("my") . $next_id;
                    //     } else {
                    //         $umr_no = "P" . date("my") . "1";
                    //     }  
                    // }else{
                    //     // No records found. Generate New UMR#
                    //     $umr_no = "P" . date("my") . "1";
                    // }
                    

                    // Instantiating a variable
                    $geoTag = 0; 
                    $address_line2 = "";
                    $lat_long = "";

                    // Get district name from district id if district id exists in parametre
                    if(isset($params['district_id'])){
                        if ($district_id != NULL || $district_id != '' || $district_id != 0) {
                            $districtRec = $this->db->select('district_id, district_name')->from('districts')->where('district_id="'. $district_id.'"')->get()->row();
                            $address_line2 = $districtRec->district_name;
                            $geoTag = 1;
                        } else {
                            $geoTag = 0;
                        }    
                    }
                    
                    // Get state name from state id
                    if(isset($params['state_id'])){
                        if ($state_id != NULL || $state_id != '' || $state_id != 0) {
                            $stateRec = $this->db->select('state_id, state_name')->from('states')->where('state_id="' . $state_id.'"')->get()->row();
                            ($address_line2 == '' ? $address_line2 .= $stateRec->state_name : $address_line2 .= ", ".$stateRec->state_name);
                            $geoTag = 1;
                        } else {
                            $geoTag = 0;
                        }
                    }
                    
                    // Get GEO Location using state and district id
                    // if geoTag is 1 - then get latlong details form google 
                    if ($geoTag) {

                        $pincode = $this->input->post('pincode');
                        $pincode != '' ? $address_line2 .= $pincode : $address_line2 = '';

                        $address = $this->input->post('address');
                        ($address_line2 == '' ? $address .= $address_line2 : $address .= ", ".$address_line2);
                        
                        /* This API Key Need To Change Because This Key Belongs To Mr.Anil Kumar */
                        $api_key = "AIzaSyDExDMVdTUduc4nCmhAlSnOxl6ZT3yY0b0";
                        $url = "https://maps.google.com/maps/api/geocode/json?address=".urlencode($address)."&key=".$api_key;

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $responseJson = curl_exec($ch);
                        curl_close($ch);

                        $response = json_decode($responseJson);

                        if ($response->status == 'OK') {
                            $latitude = $response->results[0]->geometry->location->lat;
                            $longitude = $response->results[0]->geometry->location->lng;
                            $lat_long = $latitude . "," . $longitude;
                        } else {
                            $lat_long = NULL;
                        }
                    }

                    $params['lat_long'] = $lat_long;
                    // $params['username'] = $umr_no;
                    $params['password'] = md5($pwd);
                    // $params['umr_no'] = $umr_no;

                    if($params['date_of_birth'] == '' || $params['date_of_birth'] == NULL){
                    	$params['date_of_birth'] = '';
                    }else{
                    	$params['date_of_birth'] = date('Y-m-d', strtotime($params['date_of_birth'])); 
                    }

                    if($params['mobile'] != '' || $params['mobile'] != NULL){
                        $params['mobile'] = DataCrypt($params['mobile'],'encrypt');
                    }
                    if($params['alternate_mobile'] != '' || $params['alternate_mobile'] != NULL){
                        $params['alternate_mobile'] = DataCrypt($params['alternate_mobile'],'encrypt');
                    }
                    if($params['email_id'] != '' || $params['email_id'] != NULL){
                        $params['email_id'] = DataCrypt($params['email_id'],'encrypt');
                    }

                    $params['status'] = 1;
                    $params['created_by'] = $requesterid;
                    $params['modified_by'] = $requesterid;
                    $params['created_date_time'] = date('Y-m-d H:i:s');
                    $params['modified_date_time'] = date('Y-m-d H:i:s');
                    $params['photo'] = $string_version;
                    $params['qrcode'] = $qrname;

                    if ($string_version == NULL) {
                        $path = NULL;
                    } else {
                        $path = base_url() . 'uploads/patients/' . $string_version;
                    }

                    $params['patient_id'] = $this->Generic_model->insertDataReturnId('patients', $params);  

                    $umr_no = 'P'.date('my').$params['patient_id'];

                    
                    $tempDir = './uploads/qrcodes/patients/';
                    $codeContents = $umr_no;
                    $qrname = $umr_no.md5($codeContents).'.png';
                    $pngAbsoluteFilePath = $tempDir . $qrname;
                    $urlRelativeFilePath = base_url().'uploads/qrcodes/patients/'.$qrname;

                    if (!file_exists($pngAbsoluteFilePath)) {
                        QRcode::png($codeContents, $pngAbsoluteFilePath);
                    }

                    $ptData['username'] = $umr_no;
                    $ptData['umr_no'] = $umr_no;
                    $ptData['qrcode'] = $qrname;
                    $this->Generic_model->updateData('patients', $ptData, array('patient_id'=>$params['patient_id']));

                    $clinic_info = $this->db->select('clinic_id, clinic_name')->from('clinics')->where('clinic_id =',$params['clinic_id'])->get()->result_array();

                    /* Sending mail after successfull Registration of the patient */
                    if($params['email_id'] != '' || $params['email_id'] != NULL){
                        $from = 'UMDAA';
                        $to = DataCrypt($params['email_id'],'decrypt');
                        $subject = "Successfully Registered With Umdaa";
                        $header = "Dear " . ucwords($params['first_name'].' '.$params['last_name']).",<br><br>
                        Thank you for registering with UMDAA Health Care.<br><br>You can have an access to your reports and be connected with oyur doctorusing our application. You can download Umdaa Citizens application from the below link.<br>https://play.google.com/store/apps/details?id=com.patient.umdaa&hl=en<br><br>Have A Good Day!";
                        
                        $emailRes = $this->mail_send->Content_send_all_mail($from, $to, $subject, '', '', $header);
                    }
                    /* End Of sending Mail */

                    // Remove username & password
                    unset($param['username']);
                    unset($params['password']);

                    $patientRec = '';

                    // Get Patient Complete Details
                    $patientRec = $this->db->select('patient_id,title,first_name,last_name,umr_no, date_of_birth, age, email_id as email, payment_status,clinic_id,referred_by_type,referred_by,referral_doctor_id,country,occupation,mobile,alternate_mobile,age,gender,location,state_id,district_id,preferred_language, address_line, pincode, photo, qrcode, created_date_time as registration_date, status')->from('patients')->where('patient_id ='.$params['patient_id'])->get()->row(); 

                    if(count($patientRec) > 0) {
                        $patientRec->full_name =  ($patientRec->title ? ucfirst($patientRec->title).". " : "").strtoupper(trim($patientRec->first_name)." ".trim($patientRec->last_name));
                        $patientRec->date_of_birth = ($patientRec->date_of_birth == '0000-00-00' ? "" : $patientRec->date_of_birth);
                        $patientRec->priority = null;
                        $patientRec->patient_condition = null;
                        $patientRec->appointment_date = null;
                        $patientRec->appointment_time = null;
                        $patientRec->appointment_type = null;
                        $patientRec->condition_months = null;
                        $patientRec->appointment_id = null;
                        $patientRec->status = $patientRec->status;
                        $patientRec->check_in_time = null;
                        $patientRec->department = null;
                        $patientRec->doctor_comments = null;
                        $patientRec->address = $patientRec->address_line;
                        $patientRec->pincode = $patientRec->pincode;
                        $patientRec->doctor_id = null;
                        $patientRec->doctor_name = null;
                        $patientRec->department_id = null;
                        $patientRec->color_code = null;
                        $patientRec->photo = ($patientRec->photo ? base_url() . 'uploads/patients/'.$patientRec->photo : null);
                        $patientRec->qrcode =  base_url() . 'uploads/qrcodes/patients/'.$patientRec->qrcode;
                        $patientRec->registration_date = date('Y-m-d', strtotime($patientRec->registration_date));
                        $patientRec->qb_user_id = null;
                        $patientRec->qb_user_login = null;
                        $patientRec->qb_user_fullname = null;
                        $patientRec->qb_user_tag = null;
                        $patientRec->immunization_status = 0;

                        if($patientRec->mobile != '' || $patientRec->mobile != NULL)
                            $patientRec->mobile = DataCrypt($patientRec->mobile, 'decrypt');

                        if($patientRec->alternate_mobile != '' || $patientRec->alternate_mobile != NULL)
                            $patientRec->alternate_mobile = DataCrypt($patientRec->alternate_mobile, 'decrypt');
                        
                        if($patientRec->email != '' || $patientRec->email != NULL)
                            $patientRec->email = DataCrypt($patientRec->email, 'decrypt');
                    }

                    $result = array('code' => '200', 'message' => 'successfull', 'result' => $patientRec, 'requestname' => $requestname);     
                }

            } 
            else if ($requestname == "patient_profile_edit") {

                //Patient Update
                $config['upload_path'] = './uploads/patients';

                $config['allowed_types'] = 'jpg|JPG|png|PNG|csv|JPEG|jpeg';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $value = $i;

                $this->upload->initialize($config);
                $this->upload->do_upload('file_i');

                $fname = $this->upload->data();
                $fileName = $fname['file_name'];
                $patient_id = $fdata->requesterid;                

                $patient_exist_image = $this->Generic_model->getSingleRecord('patients', array('patient_id' => $patient_id));

                if ($patient_exist_image->photo != NULL || $patient_exist_image->photo != "") {
                    if ($fileName != NULL) {
                        $patient_update['photo'] = $fileName;
                    } else {
                        $patient_update['photo'] = $patient_exist_image->photo;
                    }
                } else {
                    $patient_update['photo'] = $fileName;
                }

                $geoTag = 0; // instatiating a variable
                // get district name from district id
                if ($fdata->district_id != NULL || $fdata->district_id != '') {
                    $districtRec = $this->db->select('district_id, district_name')->from('districts')->where('district_id =',$fdata->district_id)->get()->row();
                    $geoTag = 1;
                } else {
                    $geoTag = 0;
                }

                // get state name from state id
                if ($fdata->state_id != NULL || $fdata->state_id != '') {
                    $stateRec = $this->db->select('state_id, state_name')->from('states')->where('state_id =',$fdata->state_id)->get()->row();
                    $geoTag = 1;
                } else {
                    $geoTag = 0;
                }

                if ($gepTag) { // if geoTag is 1 - then get latlong details form google
                    if ($this->input->post('pincode') == '') {
                        $stateName = $stateRec->state_name;
                        $districtName = $districtRec->district_name;
                        $a1 = $districtName . "," . $stateName;
                    } else {
                        $stateName = $stateRec->state_name;
                    }

                    $districtName = $districtRec->district_name;
                    $a1 = $districtName . "," . $stateName . "," . $this->input->post('pincode');

                    $address = $this->input->post('address') . "," . $a1;
                    /* This API Key Need To Change Because This Key Belongs To Mr.Anil Kumar */
                    $api_key = "AIzaSyDExDMVdTUduc4nCmhAlSnOxl6ZT3yY0b0";
                    $url = "https://maps.google.com/maps/api/geocode/json?address=" . urlencode($address) . "&key=" . $api_key;

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $responseJson = curl_exec($ch);
                    curl_close($ch);

                    $response = json_decode($responseJson);

                    if ($response->status == 'OK') {
                        $latitude = $response->results[0]->geometry->location->lat;
                        $longitude = $response->results[0]->geometry->location->lng;
                        $lat_long = $latitude . "," . $longitude;
                    } else {
                        $lat_long = NULL;
                    }
                }

                $patient_update['title'] = $fdata->title;
                $patient_update['first_name'] = $fdata->first_name;
                $patient_update['middle_name'] = $fdata->middle_name;
                $patient_update['last_name'] = $fdata->last_name;
                $patient_update['occupation'] = $fdata->occupation;
                $patient_update['country'] = $fdata->country;
                $patient_update['last_name'] = $fdata->last_name;
                $patient_update['gender'] = $fdata->gender;

                if(empty($fdata->date_of_birth)) { 
                    $patient_update['date_of_birth'] = "";
                } else 
                {  
                    $patient_update['date_of_birth'] = date('Y-m-d', strtotime($fdata->date_of_birth)); 
                }

                $patient_update['age'] = $fdata->age;
                $patient_update['age_unit'] = strtoupper(substr($fdata->age_unit, 0, 1));

                if($fdata->mobile != '' || $fdata->mobile != null)                
                    $patient_update['mobile'] = DataCrypt($fdata->mobile,'encrypt');

                if($fdata->alternate_mobile != '' || $fdata->alternate_mobile != null)
                    $patient_update['alternate_mobile'] = DataCrypt($fdata->alternate_mobile,'encrypt');
                
                if($fdata->email_id != '' || $fdata->email_id != null)
                    $patient_update['email_id'] = DataCrypt($fdata->email_id,'encrypt');

                $patient_update['address_line'] = $fdata->address_line;
                $patient_update['location'] = $fdata->location;
                $patient_update['district_id'] = $fdata->district_id;
                $patient_update['state_id'] = $fdata->state_id;
                $patient_update['pincode'] = $fdata->pincode;
                // $patient_update['referred_by_type'] = $fdata->referred_by_type;

                // If referred by type is a Doctor
                // Check if the referral doctor object
                // if(isset($fdata->referral_doctor)){
                //     // if you are here then its a new referral doctor which needs to be created with respect to the clinic
                //     $params['referral_doctor'] = $fdata->referral_doctor;
                //     $params['referral_doctor']->clinic_id = $fdata->clinic_id;
                //     $params['referral_doctor']->status = 1;
                //     $params['referral_doctor']->created_by = $requesterid;
                //     $params['referral_doctor']->modified_by = $requesterid;
                //     $params['referral_doctor']->created_datetime = date('Y-m-d H:i:s');
                //     $params['referral_doctor']->modified_datetime = date('Y-m-d H:i:s');

                //     // Create a new referral doctor
                //     $patient_update['referral_doctor_id'] = $this->Generic_model->insertDataReturnId('referral_doctors',$params['referral_doctor']);
                //     $patient_update['referred_by'] = $params['referral_doctor'];
                // }else{
                //     // If you are here then it means the referral doctor already exists and you are updateing with his name for referred_by
                //     $patient_update['referral_doctor_id'] = $fdata->referred_by;

                //     // Get the name of the doctor
                //     $this->db->select('doctor_name');
                //     $this->db->from('referral_doctors');
                //     $this->db->where('rfd_id =',$fdata->referred_by);
                //     $this->db->where('clinic_id =',$fdata->clinic_id);

                //     $referralDocRec = $this->db->get()->row();

                //     $patient_update['referred_by'] = $referralDocRec->doctor_name;
                // }

                $patient_update['status'] = $fdata->status;
                $patient_update['lat_long'] = $lat_long;
                $patient_update['preferred_language'] = $fdata->preferred_language;
                $patient_update['created_by'] = $user_id;
                $patient_update['created_date_time'] = date('Y-m-d H:i:s');
                $patient_update['modified_by'] = $user_id;
                $patient_update['modified_date_time'] = date('Y-m-d H:i:s');

                // Update the patient details
                $this->Generic_model->updateData('patients', $patient_update, array('patient_id' => $patient_id));

                if ($fileName == NULL) {
                    $path = NULL;
                } else {
                    $path = base_url() . 'uploads/patients/' . $fileName;
                }

                // Get Patient Complete Details
                $patientRec = $this->db->select('patient_id,title,first_name,last_name,umr_no, date_of_birth, age, email_id as email,payment_status,clinic_id,referred_by_type,referred_by,country,occupation,mobile,alternate_mobile,age,gender,location,state_id,district_id,preferred_language, address_line as address, pincode, photo, qrcode, created_date_time as registration_date')->from('patients')->where('patient_id ='.$patient_id)->get()->row(); 
                // Get Immunization Status 
                $immuneCount = $this->db->query('select * from patient_vaccine where patient_id="'.$patient_id.'"')->num_rows();

                if(count($patientRec) > 0) {

                    if($patientRec->date_of_birt == '0000-00-00'){
                        $patientRec->date_of_birt == '';
                    }
                    $patientRec->full_name =  ($patientRec->title ? ucfirst($patientRec->title)."." : "").strtoupper(trim($patientRec->first_name)." ".trim($patientRec->last_name));
                    $patientRec->priority = null;
                    $patientRec->patient_condition = null;
                    $patientRec->appointment_date = null;
                    $patientRec->appointment_time = null;
                    $patientRec->appointment_type = null;
                    $patientRec->condition_months = null;   
                    $patientRec->pincode = $patientRec->pincode;
                    $patientRec->photo = ($patientRec->photo ? base_url() . 'uploads/patients/'.$patientRec->photo : null);
                    $patientRec->qrcode =  base_url() . 'uploads/qrcodes/patients/'.$patientRec->qrcode;
                    $patientRec->registration_date = date('Y-m-d', strtotime($patientRec->registration_date));
                    $patientRec->qb_user_id = null;
                    $patientRec->qb_user_login = null;
                    $patientRec->qb_user_fullname = null;
                    $patientRec->qb_user_tag = null;
                }

                // Check if appointment id is coming then add the details of the appointment
                if($fdata->appointment_id != '' || $fdata->appointment_id != NULL){
                    // Get appointment details
                    $this->db->select('A.appointment_id, A.appointment_type, A.appointment_date, A.appointment_time_slot as appointment_time, A.priority, A.doctor_id, A.check_in_time, A.status, CONCAT("Dr.",Doc.first_name," ",Doc.last_name) as doctor_name, Doc.department_id, Dep.department_name as department, Doc.color_code');
                    $this->db->from('appointments A');
                    $this->db->join('doctors Doc','A.doctor_id = Doc.doctor_id');
                    $this->db->join('department Dep','Doc.department_id = Dep.department_id');
                    $this->db->where('A.appointment_id =',$fdata->appointment_id);
                    $this->db->where('A.clinic_id =',$fdata->clinic_id);
                    $this->db->where('A.patient_id =',$patient_id);

                    $appointmentRec = $this->db->get()->row();

                    if(count($appointmentRec) > 0){

                        // Get Doctor's comment on the patient
                        $this->db->select('doctor_comment');
                        $this->db->from('doctor_patient');
                        $this->db->where('patient_id =', $fdata->patient_id);
                        $this->db->where('doctor_id =', $fdata->doctor_id);

                        $commentRec = $this->db->get()->row();

                        //Merge all the arrays into $patientRec
                        $patientRec = array_merge((array)$patientRec, (array)$appointmentRec, (array)$commentRec);

                        // Immunization Status
                        $patientRec->immunization_status = $this->immunization_status($appointmentRec->department, $patient_id);
                    }
                }

                $result = array('code' => '200', 'message' => 'successfull', 'result' => $patientRec, 'requestname' => $fdata->requestname);

            } else if ($requestname == "previous_document_insert") {

                $this->load->library('upload');
                $config['upload_path'] = './uploads/previous_documents/';
                $config['allowed_types'] = 'jpg|JPG|png|PNG|csv|jpeg';
                $file_count = count($_FILES['file_i']['name']);
                for ($i = 0; $i < $file_count; $i++) {
                    $_FILES['file_i[]']['name'] = $_FILES['file_i']['name'][$i];
                    $_FILES['file_i[]']['type'] = $_FILES['file_i']['type'][$i];
                    $_FILES['file_i[]']['tmp_name'] = $_FILES['file_i']['tmp_name'][$i];
                    $_FILES['file_i[]']['error'] = $_FILES['file_i']['error'][$i];
                    $_FILES['file_i[]']['size'] = $_FILES['file_i']['size'][$i];
                    $this->upload->initialize($config);
                    $this->upload->do_upload('file_i[]');
                    $fname = $this->upload->data();
                    $fileName[$i] = $fname['file_name'];
                }

                $fileData = implode(',', $fileName);


                $data['appointment_id'] = $fdata->appointment_id;
                $data['patient_id'] = $fdata->patient_id;
                $data['cur_date'] = $fdata->current_date;
                $data['report_date'] = $fdata->report_date;
                $data['document_type'] = $fdata->document_type;
                $data['description'] = $fdata->description;
                $data['created_by'] = $fdata->requesterid;
                $data['modified_by'] = $fdata->requesterid;
                $data['created_date_time'] = date('Y-m-d H:i:s');
                $data['modified_date_time'] = date('Y-m-d H:i:s');
                $data['images'] = $fileData;

                $data1['previous_document_id'] = $this->Generic_model->insertDataReturnId('previous_documents', $data);

                $data1['current_date'] = $fdata->current_date;
                $data1['report_date'] = $fdata->report_date;
                $data1['document_type'] = $fdata->document_type;
                $data1['description'] = $fdata->description;



                $result = array('code' => '200', 'message' => 'Documents Submitted successfully', 'result' => $data1, 'requestname' => 'previous_document_insert');
            }
            //replacing old image with new image(vikram)
            else if ($requestname == "previous_document_edit") {

                $this->load->library('upload');
                $config['upload_path'] = './uploads/previous_documents/';
                $config['allowed_types'] = 'jpg|JPG|png|PNG|csv|jpeg';


                $check_image = './uploads/previous_documents/' . trim($fdata->file_name);
                $img = trim($fdata->file_name);
                //if image exists deleting from file system and uploading with same name
                $img_name = trim($_FILES['file_i']['name']);
                if (file_exists($check_image)) {
                    unlink($check_image);
                    $this->db->query("update previous_documents set images = REPLACE(images, '$img','$img_name') WHERE previous_document_id = '" . $fdata->id . "'");
                }

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];
                $this->upload->initialize($config);
                $this->upload->do_upload('file_i');
                $fname = $this->upload->data();
                $fileName = base_url() . "uploads/previous_documents/" . trim($fname['file_name']);




                $result = array('code' => '200', 'message' => 'successfull', 'result' => $fileName, 'requestname' => 'previous_document_edit');
            } else if ($requestname == "previous_document_update") {

                $this->load->library('upload');
                $config['upload_path'] = './uploads/previous_documents/';
                $config['allowed_types'] = 'jpg|JPG|png|PNG|csv|jpeg';
                $file_count = count($_FILES['file_i']['name']);
                for ($i = 0; $i < $file_count; $i++) {

                    $_FILES['file_i[]']['name'] = $_FILES['file_i']['name'][$i];
                    $_FILES['file_i[]']['type'] = $_FILES['file_i']['type'][$i];
                    $_FILES['file_i[]']['tmp_name'] = $_FILES['file_i']['tmp_name'][$i];
                    $_FILES['file_i[]']['error'] = $_FILES['file_i']['error'][$i];
                    $_FILES['file_i[]']['size'] = $_FILES['file_i']['size'][$i];
                    $this->upload->initialize($config);
                    $this->upload->do_upload('file_i[]');
                    $fname = $this->upload->data();
                    $fileName[$i] = $fname['file_name'];
                }
                $fileData = implode(',', $fileName);


                if ($fileData == "") {

                } else {
                    $this->db->query("update previous_documents set images = concat(images, ', ','$fileData') WHERE previous_document_id = '" . $fdata->id . "'");
                }


                $data['modified_by'] = $fdata->requesterid;

                $data['modified_date_time'] = date('Y-m-d H:i:s');

                $data1['previous_document_id'] = $fdata->previous_document_id;
                $data1['current_date'] = $fdata->current_date;
                $data1['report_date'] = $fdata->report_date;
                $data1['document_type'] = $fdata->document_type;
                $data1['description'] = $fdata->description;



                $this->Generic_model->updateData("previous_documents", $data, array('previous_document_id' => $fdata->previous_document_id));


                $result = array('code' => '200', 'message' => 'successfull', 'result' => $data1, 'requestname' => 'previous_document_update');
            } else if ($requestname == "patient_consent_form") {
                $para['patient_consent_form_id'] = $fdata->patient_consent_form_id;
                $para['patient_consent_form_image'] = $string_version;
                $para['created_by'] = $fdata->requesterid;
                $para['modified_by'] = $fdata->requesterid;
                $para['created_date_time'] = date('Y-m-d H:i:s');
                $para['modified_date_time'] = date('Y-m-d H:i:s');
                $this->Generic_model->insertData('patient_consentform_line_items', $para);
                $data1['success'] = "Success";
                $result = array('code' => '200', 'message' => 'successfull', 'result' => $data1, 'requestname' => 'patient_consent_form');
            } else if ($requestname == "save_custom_form") {


                if($fdata->form_image != '' || $fdata->form_image != NULL){
                    $image_base64_form = base64_decode($fdata->form_image);
                    $file = './uploads/section_images/' . uniqid() . '.jpg';
                    file_put_contents($file, $image_base64_form); 
                }else{
                    $file = NULL;
                }
                
                //inserting data into patient form table
                $form_master_insert['form_id'] = $fdata->form_id;
                $form_master_insert['patient_id'] = $fdata->patient_id;
                $form_master_insert['doctor_id'] = $fdata->doctor_id;
                $form_master_insert['form_type'] = $fdata->form_type;
                $form_master_insert['appointment_id'] = $fdata->appointment_id;
                $form_master_insert['sticky_note_image'] = $file;
                $form_master_insert['created_date_time'] = date('Y-m-d H:i:s');
                $form_master_insert['modified_date_time'] = date('Y-m-d H:i:s');
                $form_data = $fdata->labels;
                $form_data_sections = $fdata->main_section;

                $patient_form_master_id = $this->Generic_model->insertDataReturnId('patient_form', $form_master_insert);

                //inserting data into patient form line items
                for ($v = 0; $v < count($form_data); $v++) {
                    $form_items['field_id'] = $form_data[$v]->label_id;
                    $form_items['field_value'] = $form_data[$v]->label_value;
                    $form_items['section_id'] = $form_data[$v]->section_id;           
                    $form_items['option_id'] = $form_data[$v]->option_id;
                    $form_items['option_value'] = $form_data[$v]->option_value;
                    if($form_data[$v]->option_value == "Yes"){
                        $patient_condition['patient_id'] = $fdata->patient_id;
                        $patient_condition['patient_condition'] = $form_data[$v]->label_value;
                        $this->Generic_model->insertData('patient_condition', $patient_condition);
                    }
                    $form_items['parent_field_id'] = $form_data[$v]->parent_label_id;
                    $form_items['created_date_time'] = date('Y-m-d H:i:s');
                    $form_items['modified_date_time'] = date('Y-m-d H:i:s');
                    $form_items['patient_form_id'] = $patient_form_master_id;
                    $form_items['format'] = $form_data[$v]->format;          

                    $this->Generic_model->insertData('patient_form_line_items', $form_items);
                }
                
                
                
                for ($s = 0; $s < count($form_data_sections); $s++) {                
                    $image_base64_1 = base64_decode($form_data_sections[$s]->section_image);
                    $file_1 = './uploads/section_images/' . uniqid() . '.jpg';              
                    file_put_contents($file_1, $image_base64_1); 
                    $form_line_items['patient_form_id'] = $patient_form_master_id;
                    $form_line_items['section_id'] = $form_data_sections[$s]->section_id;
                    $form_line_items['section_text'] = $form_data_sections[$s]->section_description;
                    $form_line_items['section_id'] = $form_data_sections[$s]->section_id;
                    $form_line_items['section_image'] = $file_1;    
                    $this->Generic_model->insertData('patient_form_line_items', $form_line_items);
                }

                $result = array('code' => '200', 'message' => 'Form Details Saved Successfully', 'requestname' => 'save_custom_form');
            } else if ($requestname == "consentform_update") {

                $this->load->library('upload');
                $config['upload_path'] = './uploads/patient_consentforms/';
                $config['allowed_types'] = 'jpg|JPG|png|PNG|csv|jpeg';
                $file_count = count($_FILES['file_i']['name']);
                for ($i = 0; $i < $file_count; $i++) {
                    $_FILES['file_i[]']['name'] = $_FILES['file_i']['name'][$i];
                    $_FILES['file_i[]']['type'] = $_FILES['file_i']['type'][$i];
                    $_FILES['file_i[]']['tmp_name'] = $_FILES['file_i']['tmp_name'][$i];
                    $_FILES['file_i[]']['error'] = $_FILES['file_i']['error'][$i];
                    $_FILES['file_i[]']['size'] = $_FILES['file_i']['size'][$i];
                    $this->upload->initialize($config);
                    $this->upload->do_upload('file_i[]');
                    $fname = $this->upload->data();
                    $fileName[$i] = str_replace(' ', '', $fname['file_name']);
                }
                $fileData = str_replace(' ', '', implode(',', $fileName));
                $exists = $this->db->query('select * from patient_consentform_line_items where patient_consent_form_id = ' . $fdata->patient_consent_form_id)->row();

                if ($fileData == "") {

                } else {

                    if (count($exists) > 0) {
                        $this->db->query("update patient_consentform_line_items set patient_consent_form_image = concat(patient_consent_form_image, ',','$fileData') WHERE patient_consent_form_id  = '" . $fdata->patient_consent_form_id . "'");
                    } else {

                        $data['patient_consent_form_id'] = $fdata->patient_consent_form_id;
                        $data['patient_consent_form_image'] = $fileData;
                        $data['created_by'] = $fdata->requesterid;
                        $data['modified_by'] = $fdata->requesterid;
                        $data['created_date_time'] = date('Y-m-d H:i:s');
                        $data['modified_date_time'] = date('Y-m-d H:i:s');
                        $this->Generic_model->insertData('patient_consentform_line_items', $data);
                    }
                }

                $img_info = $this->db->query('select * from patient_consentform_line_items where patient_consent_form_id = ' . $fdata->patient_consent_form_id)->row();
                $img = $img_info->patient_consent_form_image;
                $pics = explode(",", $img);
                for ($k = 0; $k < count($pics); $k++) {

                    $data1['images'][$k]['image'] = base_url('uploads/patient_consentforms/' . trim($pics[$k]));
                }

                $result = array('code' => '200', 'message' => 'Images Uploaded successfully', 'requestname' => $requestname);
            } 
            else if($requestname == "my_records_insert")
            {
                $this->load->library('upload');
                $config['upload_path'] = './uploads/my_records/';
                $config['allowed_types'] = 'jpg|JPG|png|PNG|csv|jpeg';
                $file_count = count($_FILES['file_i']['name']);
                for ($i = 0; $i < $file_count; $i++) {
                    $_FILES['file_i[]']['name'] = $_FILES['file_i']['name'][$i];
                    $_FILES['file_i[]']['type'] = $_FILES['file_i']['type'][$i];
                    $_FILES['file_i[]']['tmp_name'] = $_FILES['file_i']['tmp_name'][$i];
                    $_FILES['file_i[]']['error'] = $_FILES['file_i']['error'][$i];
                    $_FILES['file_i[]']['size'] = $_FILES['file_i']['size'][$i];
                    $this->upload->initialize($config);
                    $this->upload->do_upload('file_i[]');
                    $fname = $this->upload->data();
                    $fileName[$i] = $fname['file_name'];
                }

                $fileData = implode(',', $fileName);

                $data['patient_id'] = $fdata->patient_id;
                $data['cur_date'] = $fdata->current_date;
                $data['report_date'] = $fdata->report_date;
                $data['document_type'] = $fdata->document_type;
                $data['description'] = $fdata->description;
                $data['created_by'] = $fdata->patient_id;
                $data['modified_by'] = $fdata->patient_id;
                $data['created_date_time'] = date('Y-m-d H:i:s');
                $data['modified_date_time'] = date('Y-m-d H:i:s');
                $data['images'] = $fileData;

                $data1['document_id'] = $this->Generic_model->insertDataReturnId('citizen_records', $data);

                $data1['current_date'] = $fdata->current_date;
                $data1['report_date'] = $fdata->report_date;
                $data1['document_type'] = $fdata->document_type;
                $data1['description'] = $fdata->description;

                $result = array('code' => '200', 'message' => 'Documents Submitted successfully', 'result' => $data1, 'requestname' => 'my_records_insert');
            }  else if ($requestname == "my_records_update") {

                $this->load->library('upload');
                $config['upload_path'] = './uploads/my_records/';
                $config['allowed_types'] = 'jpg|JPG|png|PNG|csv|jpeg';
                $file_count = count($_FILES['file_i']['name']);
                for ($i = 0; $i < $file_count; $i++) {

                    $_FILES['file_i[]']['name'] = $_FILES['file_i']['name'][$i];
                    $_FILES['file_i[]']['type'] = $_FILES['file_i']['type'][$i];
                    $_FILES['file_i[]']['tmp_name'] = $_FILES['file_i']['tmp_name'][$i];
                    $_FILES['file_i[]']['error'] = $_FILES['file_i']['error'][$i];
                    $_FILES['file_i[]']['size'] = $_FILES['file_i']['size'][$i];
                    $this->upload->initialize($config);
                    $this->upload->do_upload('file_i[]');
                    $fname = $this->upload->data();
                    $fileName[$i] = $fname['file_name'];
                }
                $fileData = implode(',', $fileName);


                if ($fileData == "") {

                } else {

                    $data['images'] = $fileData;
                    $data['modified_by'] = $fdata->requesterid;
                    $data['modified_date_time'] = date('Y-m-d H:i:s');
                    // $this->Generic_model->updateData("family_health_documents", $data, array('id' => $fdata->document_id));
                    $this->db->query("update citizen_records set images = concat(images, ', ','$fileData') WHERE citizen_record_id = '" . $fdata->document_id . "'");
                }

                $result = array('code' => '200', 'message' => 'successfull', 'result' => NULL, 'requestname' => 'my_records_update');
            }
            else if($requestname == "health_individual_family_records")
            {
                $this->load->library('upload');
                $config['upload_path'] = './uploads/health_records_documents/';
                $config['allowed_types'] = 'jpg|JPG|png|PNG|csv|jpeg';
                $file_count = count($_FILES['file_i']['name']);
                for ($i = 0; $i < $file_count; $i++) {
                    $_FILES['file_i[]']['name'] = $_FILES['file_i']['name'][$i];
                    $_FILES['file_i[]']['type'] = $_FILES['file_i']['type'][$i];
                    $_FILES['file_i[]']['tmp_name'] = $_FILES['file_i']['tmp_name'][$i];
                    $_FILES['file_i[]']['error'] = $_FILES['file_i']['error'][$i];
                    $_FILES['file_i[]']['size'] = $_FILES['file_i']['size'][$i];
                    $this->upload->initialize($config);
                    $this->upload->do_upload('file_i[]');
                    $fname = $this->upload->data();
                    $fileName[$i] = $fname['file_name'];
                }

                $fileData = implode(',', $fileName);

                $data['parent_patient_id'] = $fdata->parent_patient_id;
                $data['patient_id'] = $fdata->patient_id;
                $data['cur_date'] = $fdata->current_date;
                $data['report_date'] = $fdata->report_date;
                $data['document_type'] = $fdata->document_type;
                $data['description'] = $fdata->description;
                $data['created_by'] = $fdata->parent_patient_id;
                $data['modified_by'] = $fdata->parent_patient_id;
                $data['created_date_time'] = date('Y-m-d H:i:s');
                $data['modified_date_time'] = date('Y-m-d H:i:s');
                $data['images'] = $fileData;

                $data1['document_id'] = $this->Generic_model->insertDataReturnId('family_health_documents', $data);

                $data1['current_date'] = $fdata->current_date;
                $data1['report_date'] = $fdata->report_date;
                $data1['document_type'] = $fdata->document_type;
                $data1['description'] = $fdata->description;

                $result = array('code' => '200', 'message' => 'Documents Submitted successfully', 'result' => $data1, 'requestname' => 'health_individual_family_records');
            } else if ($requestname == "health_individual_family_records_update") {

                $this->load->library('upload');
                $config['upload_path'] = './uploads/health_records_documents/';
                $config['allowed_types'] = 'jpg|JPG|png|PNG|csv|jpeg';
                $file_count = count($_FILES['file_i']['name']);
                for ($i = 0; $i < $file_count; $i++) {

                    $_FILES['file_i[]']['name'] = $_FILES['file_i']['name'][$i];
                    $_FILES['file_i[]']['type'] = $_FILES['file_i']['type'][$i];
                    $_FILES['file_i[]']['tmp_name'] = $_FILES['file_i']['tmp_name'][$i];
                    $_FILES['file_i[]']['error'] = $_FILES['file_i']['error'][$i];
                    $_FILES['file_i[]']['size'] = $_FILES['file_i']['size'][$i];
                    $this->upload->initialize($config);
                    $this->upload->do_upload('file_i[]');
                    $fname = $this->upload->data();
                    $fileName[$i] = $fname['file_name'];
                }
                $fileData = implode(',', $fileName);


                if ($fileData == "") {

                } else {

                    $data['images'] = $fileData;
                    $data['modified_by'] = $fdata->requesterid;
                    $data['modified_date_time'] = date('Y-m-d H:i:s');
                    // $this->Generic_model->updateData("family_health_documents", $data, array('id' => $fdata->document_id));
                    $this->db->query("update family_health_documents set images = concat(images, ', ','$fileData') WHERE id = '" . $fdata->document_id . "'");
                }

                // $data1['document_id'] = $fdata->document_id;
                // $data1['current_date'] = $fdata->current_date;
                // $data1['report_date'] = $fdata->report_date;
                // $data1['document_type'] = $fdata->document_type;
                // $data1['description'] = $fdata->description;

                $result = array('code' => '200', 'message' => 'successfull', 'result' => NULL, 'requestname' => 'health_individual_family_records_update');
            }
            else {
                $result = array('code' => '200', 'message' => 'Error', 'result' => NULL, 'requestname' => 'consentform_update');
            }



            $this->response($result);
        } else {

            $entityBody = file_get_contents('php://input');

            $data = json_decode($entityBody, TRUE);

            $parameters = $data['requestparameters'];

            $method = $data['requestname'];

            $user_id = $data['requesterid'];

            $this->$method($parameters, $method, $user_id);
        }
        
    }

    

	// Patient Login OTP
    public function patient_login_otp($parameters, $method, $user_id) {
        extract($parameters);

        $check_user = $this->db->query("select * from patients_device_info  where mobile='".$parameters['mobile']."'")->row();

        if(count($check_user)>0)
        {
            $patient_info_update['fcm_id'] = $parameters['fcmId'];
            $this->Generic_model->updateData('patients_device_info',$patient_info_update,array('mobile'=>$parameters['mobile']));
        }
        else
        {
            $com['fcm_id'] = $parameters['fcmId'];
            $com['mobile'] = $parameters['mobile'];
            $com['device_id'] = $parameters['deviceid'];
            $com['modified_date_time'] = date("Y-m-d H:i:s");
            $com['created_date_time'] = date("Y-m-d H:i:s");
            $this->Generic_model->insertData('patients_device_info', $com); 
        }
       
        $check_user = $this->db->query("select * from patients where mobile='".DataCrypt($parameters['mobile'],'encrypt')."'")->row();

        // Generate OTP
        $last_four_digits = str_replace(range(0, 9), "*", substr($mobile, 0, -4)) . substr($mobile, -4);
        $random_otp = $otp = mt_rand(100000, 999999);
        $mobile = trim($mobile);
        // $message = "[#] Your verification code  is : ".$random_otp." \n rsavf/Vz89W";
        $otpsms = "Dear User, Your One Time Password (OTP) is $otp. Please enter the OTP to proceed. Thank You, UMDAA Health Care."; //working
        // $message = "Dear User, Your One Time Password (OTP) is ".$random_otp.". Please enter the OTP to proceed. Thank You, UMDAA Health Care.";

        // Send SMS to the mobile no.
        textlocalSend($mobile, $otpsms);

        $patientExist = 0;

        if(count($check_user) > 0){
            // Citizen/Patient exists with mobile no.
            // Update patient DB
            $patient_update['device_id'] = $parameters['deviceid'];
            $this->Generic_model->updateData('patients',$patient_update,array('patient_id'=>$check_user->patient_id));
            $patientExist = 1;
            $result['first_status'] = 0;
        }
        else
        {
            $last_umr_no = $this->db->select("*")->from("patients")->order_by("patient_id","desc")->get()->row();

            if($last_umr_no->umr_no == NULL || $last_umr_no->umr_no == ""){
                $umr = "P".date("my")."1";
            }else{
                $trim_umr = trim($last_umr_no->umr_no);
                $check_umr = substr($trim_umr,1,4);
                if($check_umr == date("my")){
                    $umr = (++$trim_umr);
                }else{
                    $umr = "P".date("my")."1";
                }   
            }
            
            $tempDir = './uploads/qrcodes/patients/';
            $codeContents = $umr;
            $qrname = $umr.md5($codeContents).'.png';
            $pngAbsoluteFilePath = $tempDir . $qrname;
            $urlRelativeFilePath = base_url().'uploads/qrcodes/patients/'.$qrname;

            if (!file_exists($pngAbsoluteFilePath)) {
                QRcode::png($codeContents, $pngAbsoluteFilePath);
            }
            
            $ptData['mobile'] = DataCrypt($mobile,'encrypt');
            // $userid = $this->Generic_model->insertDataReturnId('users',$ptData);
            $ptData['username'] = $umr;
            $ptData['umr_no'] = $umr;
            $ptData['qrcode'] = $qrname;
            // $ptData['patient_id'] = $userid;
            $this->Generic_model->insertData('patients', $ptData);
            $result['first_status'] = 1;
        }

        $patient_otp_check = $this->db->query("select * from patient_otp where mobile_no='".$mobile."'")->row();

        if(count($patient_otp_check) > 0)
        {
            $patient_otp_update['otp'] = $random_otp;
            $patient_otp_update['status'] = 1;
            $this->Generic_model->updateData('patient_otp',$patient_otp_update,array('mobile_no'=>$mobile));
        }
        else{
            $patient_otp_insert['patient_id'] = $check_user->patient_id;
            $patient_otp_insert['mobile_no'] = $check_user->mobile;
            $patient_otp_insert['otp'] = $random_otp;
            $this->Generic_model->insertData('patient_otp',$patient_otp_insert);
        }

     if($patientExist > 0){
        $result['otp'] = $random_otp;
        $result['user_id'] = $check_user->patient_id;
        $result['user_name'] = $check_user->first_name." ".$check_user->last_name;
        $this->response(array('code' => '200', 'message' => 'OTP Sent On Mobile ' . $last_four_digits, 'result' => $result, 'requestname' => $method));    
    }elseif($patientExist == 0){
        $result['otp'] = $random_otp;
        $result['mobile'] = $mobile;
        $this->response(array('code' => '201', 'message' => 'OTP Sent On Mobile' . $last_four_digits, 'result' => $result, 'requestname' => $method));    
    }
}

	// Patient Resend Login OTP
public function resendOtp($parameters, $method, $user_id) {
    echo "skdghfkashdf";
    $check_user = $this->db->query("select * from patients where  mobile='".$parameters['mobile']."' ")->row();

    if (count($check_user) > 0) {
     $patient_otp_check = $this->db->query("select * from patient_otp where  patient_id='".$check_user->patient_id."' ")->row();

     $last_four_digits = str_replace(range(0, 9), "*", substr($check_user->mobile, 0, -4)) . substr($check_user->mobile, -4);
    //  $random_otp = $patient_otp_check->otp;
    //  $mobile = trim($check_user->mobile);
    //  $message = "<#> Your verification code  is : ".$random_otp." \n NeUL8p35WG0";
    //  sendsms($mobile, $message);
    
    $random_otp = $otp = mt_rand(100000, 999999);
    $mobile = trim($mobile);
    // $message = "[#] Your verification code  is : ".$random_otp." \n rsavf/Vz89W";
    $otpsms = "Dear User, Your One Time Password (OTP) is $otp. Please enter the OTP to proceed. Thank You, UMDAA Health Care."; //working
    // $message = "Dear User, Your One Time Password (OTP) is ".$random_otp.". Please enter the OTP to proceed. Thank You, UMDAA Health Care.";

    // Send SMS to the mobile no.
    textlocalSend($mobile, $otpsms);

			//$patient_update['fcm_id'] = $parameters['fcmId'];
     $patient_update['device_id'] = $parameters['deviceid'];

     $this->Generic_model->updateData('patients',$patient_update,array('patient_id'=>$check_user->patient_id));

     $result['otp'] = $random_otp;
     $result['user_id'] = $check_user->patient_id;
     $result['user_name'] = $check_user->first_name." ".$check_user->last_name;
     $this->response(array('code' => '200', 'message' => 'OTP Sent On Mobile ' . $last_four_digits, 'result' => $result, 'requestname' => $method));
 } else {
    $this->response(array('code' => '404', 'message' => 'User Does Not Exist'), 200);
}
}


// public function getPatientsTreeList($parameters,$method,$user_id){
//     extract($parameters);
//     $check = $this->db->query("select * from patient_family_health_records where parent_patient_id='".$patient_id."' ")->result();
//     // echo $this->db->last_query();
//     // exit();
//     if(count($check) > 0){
//         $i = 0;
//         foreach($check as $value){
//             // echo $primary;
//             $para['patient_list'][$i]['parent_patient_id'] = $value->parent_patient_id;
//             $para['patient_list'][$i]['full_name'] = $value->full_name;
//             $para['patient_list'][$i]['mobile'] = $value->phone_number;
//             $para['patient_list'][$i]['gender'] = $value->gender;
//             $para['patient_list'][$i]['age'] = $value->age;
//             $para['patient_list'][$i]['relationship'] = $value->relationship;
//             // $para['patient_list'][$i]['profilePic'] = base_url('uploads/patients/'.$value->photo);
//             // $para['patient_list'][$i]['']
//             // $para['patient_list'][$i]['relation'] = $value->relation;
//             $i++;
//         }
//         $this->response(array('code'=>'200','message'=>'Success','result'=>$para,'requestname'=>$method));
//     }
//     else{
//         $para['patient_list'] = [];
//         $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$para,'requestname'=>$method));
//     }
// }

public function getPatientsTreeList($parameters,$method,$user_id){
    extract($parameters);
    $check = $this->db->query("select * from patients where mobile='".DataCrypt($mobile, 'encrypt')."' or alternate_mobile='".DataCrypt($mobile, 'encrypt')."'")->result();
    // echo $this->db->last_query();
    // exit();
    if(count($check) > 0){
        $i = 0;
        foreach($check as $value){
            $primary = 0;
            if($value->mobile == ""){
                $mobile_number = DataCrypt($value->alternate_mobile, 'decrypt');
                $primary = 0;
            }   
            else{
                $mobile_number = DataCrypt($value->mobile, 'decrypt');
                $primary = 1;
            }
            // echo $primary;
            $para['patient_list'][$i]['name'] = getPatientName($value->patient_id);
            $para['patient_list'][$i]['mobile'] = $mobile_number;
            $para['patient_list'][$i]['patient_id'] = $value->patient_id;
            $para['patient_list'][$i]['umr_no'] = $value->umr_no;
            $para['patient_list'][$i]['primary'] = $primary;
            $para['patient_list'][$i]['profilePic'] = base_url('uploads/patients/'.$value->photo);
            // $para['patient_list'][$i]['']
            // $para['patient_list'][$i]['relation'] = $value->relation;
            $i++;
        }
        $this->response(array('code'=>'200','message'=>'Success','result'=>$para,'requestname'=>$method));
    }
    else{
        $para['patient_list'] = [];
        $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$para,'requestname'=>$method));
    }
}



//Get Token Number
public function getTokenNumber($parameters, $method, $user_id){
    extract($parameters);
    if($patient_id == "" || $patient_id == "NULL")
    {
        $this->response(array('code' => '201', 'message' => 'Patient Does Not Exist', 'requestname' => $method));
    }
    else
    {
        $today = date("Y-m-d");
        $patientInfo = $this->db->select("*")->from("appointments")->where("status!='closed' and patient_id='".$patient_id."' and appointment_date<='".$today."'")->get()->result();
        $i = 0;
        if(count($patientInfo)>0)
        {
            foreach ($patientInfo as $value) 
            {
                $clinicInfo = $this->db->select("clinic_name,location")->from("clinics")->where("clinic_id='".$value->clinic_id."'")->get()->row();
                $docInfo = $this->db->select("first_name,last_name,doctor_id")->from("doctors")->where("doctor_id='".$value->doctor_id."'")->get()->row();
                if($value->appointment_type == "New")
                    $type = "N";
                elseif($value->appointment_type == "Follow-up")
                    $type = "F";
                $data['patientToken'][$i]['token_number'] = $type.$value->appointment_id;
                $data['patientToken'][$i]['appointment_id'] = $value->appointment_id;
                $data['patientToken'][$i]['status'] = $value->status;
                $data['patientToken'][$i]['appointment_type'] = $value->appointment_type;
                $data['patientToken'][$i]['appointment_date'] = $value->appointment_date;
                $data['patientToken'][$i]['time_slot'] = $value->appointment_time_slot;
                $data['patientToken'][$i]['clinic_id'] = $value->clinic_id;
                $data['patientToken'][$i]['clinic_name'] = $clinicInfo->clinic_name.", ".$clinicInfo->location;
                $data['patientToken'][$i]['docName'] = "Dr. ".$docInfo->first_name." ".$docInfo->last_name;
                $i++;
            }
        }
        else
        {
            $data['patientToken'] = "No Appointments Found";
        }
        $this->response(array('code' => '200', 'message' => 'Patient Appointments ', 'result' => $data, 'requestname' => $method));
    }
    
}


//get Departments
public function departments($parameters, $method, $user_id){
    $departmentInfo = $this->db->select("department_id,department_name,department_icon")->from("department")->get()->result();
    if(count($departmentInfo)>0)
    {
        $i = 0;
        foreach($departmentInfo as $value)
        {
            if($value->department_icon == "")
            {
                 $src = "dummyDEPT.png";
            }
            else
            {
                 $src = $value->department_icon;
            }
            $para['departments'][$i]['department_id'] = $value->department_id;
            $para['departments'][$i]['department_name'] = $value->department_name;
            $para['departments'][$i]['department_icon'] = base_url('uploads/departments/'.$src);
            $i++;
        }
        $para['departments'][$i]['department_id'] = 0;
        $para['departments'][$i]['department_name'] = "Other";
        $para['departments'][$i]['department_icon'] = base_url('uploads/departments/dummyDEPT.png');

        $this->response(array('code'=>'200','messsage'=>'Departments List','result'=>$para,'requestname'=>$method));
    }
    else
    {
        $para['departments'] = [];
        $this->response(array('code'=>'201','messsage'=>'Empty Departments List','result'=>$para,'requestname'=>$method));
    }
}

// get doctors list by department_id
public function doctorsList($parameters, $method, $user_id)
{
    extract($parameters);
    $docInfo = $this->db->query("select * from doctors d,department de where d.department_id=de.department_id and d.department_id='".$department_id."'")->result();
    if(count($docInfo)>0)
    {
        $j = 0;
        foreach($docInfo as $value)
        {
            $doctor_degrees_list = $this->db->query("select * from doctor_degrees where doctor_id ='".$value->doctor_id."'")->result();
            $clinicsInfo = $this->db->query("select c.clinic_id,c.clinic_name,CONCAT(c.address,' ',c.location) as clinic_address,c.state_id,c.district_id,c.clinic_phone,c.email,c.pincode from clinics c,clinic_doctor cd where c.clinic_id=cd.clinic_id and cd.doctor_id='".$value->doctor_id."'")->result();
            // echo $this->db->last_query();
            $clinics = array();
            $k = 0;
            foreach($clinicsInfo as $clinicValue){
                $stateInfo = $this->db->query("select * from states where state_id='".$clinicValue->state_id."'")->row();
                $districtInfo = $this->db->query("select * from districts where district_id='".$clinicValue->district_id."'")->row();
                $clinics[$k]['clinic_id'] = $clinicValue->clinic_id;
                $clinics[$k]['clinic_name'] = $clinicValue->clinic_name;
                $clinics[$k]['clinic_address'] = $clinicValue->clinic_address.", ".$districtInfo->district_name.", ".$stateInfo->state_name." - ".$clinicValue->pincode;
                $clinics[$k]['clinic_phone'] = $clinicValue->clinic_phone;
                $clinics[$k]['email'] = $clinicValue->email;
                $k++;
            }
            $degree_name = array();
            foreach($doctor_degrees_list as $degree_val){
                $degree_name[] = $degree_val->degree_name;
            }
            $doctor_degree = implode(",", $degree_name);
            $data['doctors'][$j]['doctor_id'] = $value->doctor_id;
            $data['doctors'][$j]['specialization'] =$value->speciality ;
            $data['doctors'][$j]['doctor_name'] = $value->first_name." ".$value->last_name;
            $data['doctors'][$j]['department_name'] = $value->department_name;
            $data['doctors'][$j]['experience'] = $value->experience." Years";
            $data['doctors'][$j]['about_doctor'] = $value->about ;
            $data['doctors'][$j]['contact'] = $value->mobile ;
            $data['doctors'][$j]['google_review_link'] = $value->google_review_link ;
            $data['doctors'][$j]['gender'] = strtoupper($value->gender) ;
            $data['doctors'][$j]['visit_time'] = "" ;
            $data['doctors'][$j]['known_languages'] = $value->languages ;
            $data['doctors'][$j]['dealt_deseases'] = $value->diseases_dealt;
            $data['doctors'][$j]['degrees'] = $doctor_degree;
            $data['doctors'][$j]['qualification'] = $value->qualification;
            $data['doctors'][$j]['clinics'] = $clinics;
    
            $achievements = $value->acheivements;
            $arra_ach = explode(",", $achievements);
    
            if($achievements == "" || $achievements == "null"){
                $data['doctors'][$j]['achievements'] = array();
            }else{
                $data['doctors'][$j]['achievements'] = $arra_ach;
    
            }
    
            $data['doctors'][$j]['membership'] = $value->membership_in;
            $data['doctors'][$j]['address'] = $value->address;
            $data['doctors'][$j]['doctor_image'] =  base_url("uploads/doctors/".$value->profile_image."");
            // $param['doctors'][$i]['doctor_id'] = $value->doctor_id;
            // $param['doctors'][$i]['doctor_name'] = "Dr. ".$value->first_name." ".$value->last_name;
            // $param['doctors'][$i]['department_name'] = $value->department_name;
            $j++;
        }
        $this->response(array('code'=>'200','message'=>'Doctors List','result'=>$data,'requestname'=>$method));
    }
    else
    {
        $param['doctors'] = [];
        $this->response(array('code'=>'201','message'=>'Empty Doctors List','result'=>$param,'requestname'=>$method));
    }
}

// get doctors list by department_id
public function docClinicList($parameters, $method, $user_id)
{
    extract($parameters);
    $clinics_list = $this->db->select("cd.*,c.*,cd.clinic_id")->from("clinic_doctor cd")->join("clinics c","cd.clinic_id=c.clinic_id")->where("cd.doctor_id ='" . $doctor_id . "'")->group_by("cd.clinic_id")->order_by("c.clinic_id","asc")->get()->result();
    if (count($clinics_list) > 0) {
        $c = 0;
        foreach ($clinics_list as $clinic) {
            $clinicPatientInfo = $this->db->query("select * from clinic_doctor_patient where clinic_id='".$clinic->clinic_id."' and patient_id='".$patient_id."'")->row();
            // $param['query'][$c]['q'] = $this->db->last_query();
            if(count($clinicPatientInfo)>0)
            {
                $param['clinics'][$c]['registration'] = "0";
            }
            else
            {
                $param['clinics'][$c]['registration'] = "1";
            }
            $param['clinics'][$c]['clinic_id'] = $clinic->clinic_id;
            $param['clinics'][$c]['clinic_name'] = $clinic->clinic_name;
            $param['clinics'][$c]['consultation_time'] = $clinic->consulting_time;
            $param['clinics'][$c]['consulting_fee'] = $clinic->consulting_fee;
            $param['clinics'][$c]['online_consulting_fee'] = $clinic->online_consulting_fee;
            $param['clinics'][$c]['registration_fee'] = $clinic->registration_fee;
            $param['clinics'][$c]['pharmacy_discount'] = $clinic->pharmacy_discount;
            $param['clinics'][$c]['lab_discount'] = $clinic->lab_discount;
            $c++;
        }
    } else {
        $param['doctor']['clinics'] = array();
    }
    $this->response(array('code'=>'200','message'=>'Clinics List','result'=>$param,'requestname'=>$method));
}

// get working schedule and booked & blocked slots 
public function workingTimings($parameters,$method,$user_id)
{
    $clinic_id = $parameters['clinic_id'];
    $doctor_id = $parameters['doctor_id'];

    // get all doctors belongs to the clinic '$clinic_id'
    $doctorInfo = $this->db->select("cd.*,d.*,de.*,cd.clinic_id")->from("clinic_doctor cd")->join("doctors d","cd.doctor_id=d.doctor_id")->join("department de","d.department_id=de.department_id")->where("d.doctor_id='".$doctor_id."'")->group_by("cd.doctor_id")->order_by("d.doctor_id","asc")->get()->row();

    if (count($doctorInfo) > 0) {
            $param['doctor']['doctor_id'] = $doctorInfo->doctor_id;
            $param['doctor']['doctor_name'] = "Dr. " . strtoupper($doctorInfo->first_name . " " . $doctorInfo->last_name);
            $param['doctor']['designation'] = $doctorInfo->qualification;
            $param['doctor']['department'] = $doctorInfo->department_name;
            $param['doctor']['registration_code'] = $doctorInfo->registration_code;
            $param['doctor']['color_code'] = $doctorInfo->color_code;

            $clinics_list = $this->db->select("cd.*,c.*,cd.clinic_id")->from("clinic_doctor cd")->join("clinics c","cd.clinic_id=c.clinic_id")->where("cd.doctor_id ='" . $doctorInfo->doctor_id . "'")->group_by("cd.clinic_id")->order_by("c.clinic_id","asc")->get()->result();

            if (count($clinics_list) > 0) {
                $c = 0;
                foreach ($clinics_list as $clinic) {
                    $cdw = 0; $cdww = 0;
                    $param['doctor']['clinics'][$c]['clinic_id'] = $clinic->clinic_id;
                    $param['doctor']['clinics'][$c]['clinic_name'] = $clinic->clinic_name;
                    $param['doctor']['clinics'][$c]['consultation_time'] = $clinic->consulting_time;
                    $param['doctor']['clinics'][$c]['consulting_fee'] = $clinic->consulting_fee;
                    $param['doctor']['clinics'][$c]['registration_fee'] = $clinic->registration_fee;
                    $param['doctor']['clinics'][$c]['pharmacy_discount'] = $clinic->pharmacy_discount;
                    $param['doctor']['clinics'][$c]['lab_discount'] = $clinic->lab_discount;

                    $date = date('Y-m-d'); 
        
                    $weekOfdays = array();
                    
                    for ($i = 1; $i <= 7; $i++) {
                        $date = date('Y-m-d', strtotime('+1 day', strtotime($date)));
                        $weekOfdays[] = date('Y-m-d', strtotime($date));
                    }
                    
                    $k = 0;

                    foreach ($weekOfdays as $key => $value) {
                        $date = date('Y-m-d', strtotime('-1 day', strtotime($value)));
                        $wday = date('N', strtotime($date));
                        $param['doctor']['clinics'][$c]['booked_slots'][$k]['date'] = $date;
                        $bs = $this->db->select("*")->from("appointments")->where("clinic_id='".$clinic->clinic_id."' and doctor_id='".$doctor_id."' and appointment_date='".$date."'")->get()->result();
                        // $param[$c]['query'] = $this->db->last_query();
                        if (count($bs) > 0) {
                            $b_slots = [];
                            foreach ($bs as $bss) {
                                $b_slots[] = date('h:i A', strtotime($bss->appointment_time_slot));
                            }
                            $param['doctor']['clinics'][$c]['booked_slots'][$k]['time_slot'] = $b_slots;
                        } else {
                            $param['doctor']['clinics'][$c]['booked_slots'][$k]['time_slot'] = NULL;
                        }
                        $k++;
                        $blocking = $this->db->select("*")->from("calendar_blocking")->where("clinic_id='" . $clinic_id . "' and doctor_id='" . $doctor_id . "'")->get()->row();

                        $cal_dates = explode(",", $blocking->dates);
                        $blocked_dates = array();
                        foreach ($cal_dates as $key => $final) {

                            if (in_array($final, $weekOfdays)) {
                                $blocked_dates[] = $final;
                            }
                        }
                        $blocked_list = implode(',', $blocked_dates);
                        $param['doctor']['clinics'][$c]['blocked_dates'] = $blocked_list;
                    }

                    $week_day_list = $this->db->select("clinic_doctor_weekday_id,weekday")->from("clinic_doctor_weekdays")->where("clinic_doctor_id ='" . $clinic->clinic_doctor_id . "' and slot='walkin'")->group_by("weekday")->order_by("clinic_doctor_weekday_id","asc")->get()->result();


                    foreach ($week_day_list as $weekday) {
                        // initiates weekdays to the number
                        if ($weekday->weekday == 1) {
                            $week = "Monday";
                        }
                        if ($weekday->weekday == 2) {
                            $week = "Tuesday";
                        }
                        if ($weekday->weekday == 3) {
                            $week = "Wednesday";
                        }
                        if ($weekday->weekday == 4) {
                            $week = "Thursday";
                        }
                        if ($weekday->weekday == 5) {
                            $week = "Friday";
                        }
                        if ($weekday->weekday == 6) {
                            $week = "Saturday";
                        }
                        if ($weekday->weekday == 7) {
                            $week = "Sunday";
                        }

                        $week_day_list_slots = $this->db->select("*")->from("clinic_doctor_weekday_slots")->where("clinic_doctor_weekday_id ='" . $weekday->clinic_doctor_weekday_id . "'")->order_by("clinic_doctor_weekday_id","asc")->get()->result();
                                
                        $cdws = 0;
                        foreach ($week_day_list_slots as $wdls) {
                            // $day[]=$wdls->session;
                            $sessions_list[] = date('h:i A', strtotime($wdls->from_time)) . "-" . date('h:i A', strtotime($wdls->to_time)). "-" .$wdls->session;
                            $cdws++;
                        }

                        $param['doctor']['clinics'][$c]['working_days'][$cdw]['day'] = $week;
                        // $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['session'] = $sessions_list;

                        for ($i = 0; $i < count($sessions_list); $i++) {
                            $sl = explode("-", $sessions_list[$i]);
                            $time = date("H", strtotime($sl[0]));
                            if ($sl[2] == 'morning') {
                                $param['doctor']['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = 'Morning';
                                $param['doctor']['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                            } else if ($sl[2] == 'afternoon') {
                                $param['doctor']['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = 'Afternoon';
                                $param['doctor']['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                            } else if ($sl[2] == 'evening') {
                                $param['doctor']['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] ='Evening';
                                $param['doctor']['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                            }

                            // if ($time < 12) {
                            //     $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = $sl[2];
                            //     $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                            // } else if ($time > 12 && $time < 17) {
                            //     $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = $sl[2];
                            //     $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                            // } else if ($time >= 17) {
                            //     $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = $sl[2];
                            //     $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                            // }
                        }
                        unset($sessions_list);
                        $cdw++;
                        // else
                        // {
                        //     $param['doctor']['clinics'][$c]['working_days'][$cdw]['day'] = $week;
                        //     $param['doctor']['clinics'][$c]['working_days'][$cdw]['timings'] = "null";
                        // }
                        
                        // unset($sessions_list);
                        // $cdw++;
                    }

                    $week_day_list = $this->db->select("clinic_doctor_weekday_id,weekday")->from("clinic_doctor_weekdays")->where("clinic_doctor_id ='" . $clinic->clinic_doctor_id . "' and slot='video call'")->group_by("weekday")->order_by("clinic_doctor_weekday_id","asc")->get()->result();

                    foreach ($week_day_list as $weekday) {
                        // initiates weekdays to the number
                        if ($weekday->weekday == 1) {
                            $week = "Monday";
                        }
                        if ($weekday->weekday == 2) {
                            $week = "Tuesday";
                        }
                        if ($weekday->weekday == 3) {
                            $week = "Wednesday";
                        }
                        if ($weekday->weekday == 4) {
                            $week = "Thursday";
                        }
                        if ($weekday->weekday == 5) {
                            $week = "Friday";
                        }
                        if ($weekday->weekday == 6) {
                            $week = "Saturday";
                        }
                        if ($weekday->weekday == 7) {
                            $week = "Sunday";
                        }
                
                        $week_day_list_slots = $this->db->select("*")->from("clinic_doctor_weekday_slots")->where("clinic_doctor_weekday_id ='" . $weekday->clinic_doctor_weekday_id . "'")->order_by("clinic_doctor_weekday_id","asc")->get()->result();
                
                        $cdws = 0;
                        foreach ($week_day_list_slots as $wdls) {
                            // $sessions_list[] = date('h:i A', strtotime($wdls->from_time)) . "-" . date('h:i A', strtotime($wdls->to_time));
                            $sessions_list[] = date('h:i A', strtotime($wdls->from_time)) . "-" . date('h:i A', strtotime($wdls->to_time)). "-" .$wdls->session;
                            $cdws++;
                        }
                
                        $param['doctor']['clinics'][$c]['videocall_working_days'][$cdww]['day'] = $week;
                        // $param['doctor'][$d]['clinics'][$c]['videocall_working_days'][$cdww]['session'] = $sessions_listt;
                
                        for ($i = 0; $i < count($sessions_list); $i++) {
                            $sl = explode("-", $sessions_list[$i]);
                            $time = date("H", strtotime($sl[0]));
                
                            if ($sl[2] == 'morning') {
                                $param['doctor']['clinics'][$c]['videocall_working_days'][$cdww]['timings'][$i]['session'] = 'Morning';
                                $param['doctor']['clinics'][$c]['videocall_working_days'][$cdww]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                            } else if ($sl[2] == 'afternoon') {
                                $param['doctor']['clinics'][$c]['videocall_working_days'][$cdww]['timings'][$i]['session'] = 'Afternoon';
                                $param['doctor']['clinics'][$c]['videocall_working_days'][$cdww]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                            } else if ($sl[2] == 'evening') {
                                $param['doctor']['clinics'][$c]['videocall_working_days'][$cdww]['timings'][$i]['session'] ='Evening';
                                $param['doctor']['clinics'][$c]['videocall_working_days'][$cdww]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                            }
                        }
                        unset($sessions_list);
                        $cdww++;
                    }
                    $c++;
                }
            } else {
                $param['doctor']['clinics'] = array();
            }

    } else {
        $param['doctor'] = array();
    }

    $this->response(array('code' => '200', 'message' => 'Doctor Time Slots Session Wise', 'result' => $param, 'requestname' => $method));    
}



	//list of appointments w.r.t to user(nurse,doctor etc)
public function appointments($parameters, $method, $user_id) {

    $clinic_id = $parameters['clinic_id'];
    $role_id = $parameters['role_id'];
    $from_date = date('Y-m-d',strtotime($parameters['from_date']));
    $to_date = date('Y-m-d',strtotime($parameters['to_date']));

    $qry = "SELECT a.*,p.*,a.status as `appointment_status`,a.appointment_type,a.payment_status as `appointment_payment_status`,p.title,p.middle_name,p.patient_id,p.umr_no,p.age_unit,p.gender,p.payment_status as `registration_payment_status`,a.check_in_time,a.check_in_time,p.email_id,p.photo,p.qrcode,d.doctor_id,d.first_name as `d_firstname`,d.last_name as `d_lastname`, d.color_code, d.salutation,de.department_name,de.department_id,s.state_id,s.state_name,dis.district_id,dis.district_name,p.pincode as `ppcode`,p.created_date_time as registration_date FROM `appointments` a 
    inner join patients p on (a.patient_id=p.patient_id) 
    left join states s on (p.state_id=s.state_id)
    left join districts dis on (p.district_id=dis.district_id) 
    inner join doctors d on (a.doctor_id=d.doctor_id) 
    inner join department de on (d.department_id=de.department_id) 
    where a.clinic_id='" . $clinic_id . "' AND a.status not in ('drop','reschedule') ";

    if($role_id == 4){
            //$qry .="and (a.status='in_consultation' or a.status='waiting') and a.doctor_id='".$user_id."'";
        $qry .="and a.doctor_id='".$user_id."'";
    }

        // if mobile no is specified, then it should pull all the appointments of the concern person irrespective of the dates with different status
    if (isset($parameters['mobile'])) {
        $qry .= " AND (p.mobile = '" . $parameters['mobile'] . "' OR p.alternate_mobile = '".$parameters['mobile']."') ";
        } else { // if no mobile number specified then it will pull all the appointments for the current date
            $qry .= " AND a.appointment_date >= '" . $from_date . "' and a.appointment_date <= '".$to_date."'";
        }

        if($role_id == 6)
        {
            $qry .= " ORDER BY FIELD(a.status,'vital_signs','checked_in','waiting','booked','in_consultation','closed'), if(FIELD(a.priority,'sick','pregnancy', 'elderly','children','other')=0,'none',0),a.appointment_time_slot asc,a.appointment_date desc,a.check_in_time asc";
        }

        if($role_id == 4){    
            $qry .= " ORDER BY FIELD(a.status,'in_consultation','waiting','vital_signs','checked_in','booked','closed'), if(FIELD(a.priority,'sick','pregnancy', 'elderly','children','other')=0,'none',0),a.check_in_time asc,a.appointment_time_slot asc,a.appointment_date desc";
        }

        $patients = $this->db->query($qry)->result();

        if (count($patients) > 0) {
            $i = 0;
            foreach ($patients as $patient) {
                $im_count = $this->db->query('select * from patient_vaccine where patient_id="'.$patient->patient_id.'"')->num_rows();

                if ($patient->appointment_status != NULL) {
                    $patient_status = $patient->appointment_status;
                } else {
                    $patient_status = 'Book Appointment';
                }

                if ($patient->check_in_time != NULL) {
                    $check_in_time = date('Y-m-d H:i:s', strtotime($patient->check_in_time));
                } else {
                    $check_in_time = NULL;
                }

                if ($patient->photo != NULL) {
                    $path = base_url() . 'uploads/patients/' . $patient->photo;
                } else {
                    $path = NULL;
                }

                if ($patient->qrcode != NULL) {
                    $qrcode = base_url() . 'uploads/qrcodes/patients/' . $patient->qrcode;
                } else {
                    $qrcode = NULL;
                }

                $patient->age_unit = strtoupper(substr($patient->age_unit, 0, 1));
                
                $patient_ids[] = $patient->patient_id;
                $patient_val['appointment_list'][$i]['patient_id'] = $patient->patient_id;
                $patient_val['appointment_list'][$i]['title'] = ucfirst($patient->title);
                $patient_val['appointment_list'][$i]['first_name'] = strtoupper($patient->first_name);
                $patient_val['appointment_list'][$i]['middle_name'] = strtoupper($patient->middle_name);
                $patient_val['appointment_list'][$i]['last_name'] = strtoupper($patient->last_name);
                $patient_val['appointment_list'][$i]['full_name'] =  ($patient->title ? ucfirst($patient->title).". " : "").strtoupper(trim($patient->first_name)." ".trim($patient->last_name));
                $patient_val['appointment_list'][$i]['umr_no'] = $patient->umr_no;
                $patient_val['appointment_list'][$i]['payment_status'] = $patient->appointment_payment_status;
                $patient_val['appointment_list'][$i]['clinic_id'] = $patient->clinic_id;
                $patient_val['appointment_list'][$i]['referred_by'] = $patient->referred_by;
                $patient_val['appointment_list'][$i]['country'] = $patient->country;
                $patient_val['appointment_list'][$i]['priority'] = $patient->priority;
                $patient_val['appointment_list'][$i]['occupation'] = $patient->occupation;
                $patient_val['appointment_list'][$i]['contact'] = $patient->mobile;
                $patient_val['appointment_list'][$i]['alternate_mobile'] = $patient->alternate_mobile;
                $patient_val['appointment_list'][$i]['age'] = $patient->age . " " . ucfirst($patient->age_unit);
                $patient_val['appointment_list'][$i]['gender'] = $patient->gender;
                $patient_val['appointment_list'][$i]['patient_condition'] = $patient->patient_condition;
                $patient_val['appointment_list'][$i]['condition_months'] = $patient->condition_months;
                $patient_val['appointment_list'][$i]['appointment_id'] = $patient->appointment_id;
                $patient_val['appointment_list'][$i]['appointment_date'] = $patient->appointment_date;
                $patient_val['appointment_list'][$i]['appointment_time'] = $patient->appointment_time_slot;
                $patient_val['appointment_list'][$i]['appointment_type'] = strtoupper(substr($patient->appointment_type, 0, 1));
                $patient_val['appointment_list'][$i]['status'] = $patient_status;
                $patient_val['appointment_list'][$i]['check_in_time'] = $check_in_time;
                $patient_val['appointment_list'][$i]['date_of_birth'] = ($patient->date_of_birth == '0000-00-00' ? "" : $patient->date_of_birth);
                $patient_val['appointment_list'][$i]['email'] = $patient->email_id;
                $patient_val['appointment_list'][$i]['doctor_comments'] = $patient->doctor_comments;
                $patient_val['appointment_list'][$i]['address'] = $patient->address_line . "," . $patient->district_name . "," . $patient->state_name . "," . $patient->ppcode;

                // eliminate comma
                $patient_val['appointment_list'][$i]['address'] = $this->eliminateComma($patient_val['appointment_list'][$i]['address']);

                $patient_val['appointment_list'][$i]['doctor_id'] = $patient->doctor_id;
                $patient_val['appointment_list'][$i]['doctor_name'] = "Dr. " . strtoupper($patient->d_firstname . " " . $patient->d_lastname);
                $patient_val['appointment_list'][$i]['department'] = $patient->department_name;
                $patient_val['appointment_list'][$i]['department_id'] = $patient->department_id;
                $patient_val['appointment_list'][$i]['color_code'] = $patient->color_code;
                $patient_val['appointment_list'][$i]['photo'] = $path;
                $patient_val['appointment_list'][$i]['qrcode'] = $qrcode;
                $patient_val['appointment_list'][$i]['registartion_date'] = date("Y-m-d", strtotime($patient->registration_date));
                $patient_val['appointment_list'][$i]['qb_user_id'] = null;
                $patient_val['appointment_list'][$i]['qb_user_login'] = null;
                $patient_val['appointment_list'][$i]['qb_user_fullname'] = null;
                $patient_val['appointment_list'][$i]['qb_user_tag'] = null;
                if($im_count > 0){
                    $patient_val['appointment_list'][$i]['immunization_status'] = 1;
                }
                else{
                    $patient_val['appointment_list'][$i]['immunization_status'] = 0;
                }
                $i++;
            }
            
            $patientids = implode(",",$patient_ids);
            $patientRecord = $this->db->query("SELECT p.*,p.title,p.patient_id,p.age_unit,p.middle_name,p.umr_no,p.gender,p.email_id,p.photo,p.qrcode,s.state_id,s.state_name,d.district_id,d.district_name,p.pincode as ppcode FROM `patients` p       
                left join states s on (p.state_id=s.state_id)
                left join districts d on (p.district_id=d.district_id)
                where p.patient_id IN (".$patientids.")
                order by p.patient_id desc")->result();
            $p=0;
            foreach($patientRecord as $patient1)
            {
                if ($patient1->photo != NULL) {
                    $path = base_url() . 'uploads/patients/' . $patient1->photo;
                } else {
                    $path = NULL;
                }

                if ($patient1->qrcode != NULL) {
                    $qrcode = base_url() . 'uploads/qrcodes/patients/' . $patient1->qrcode;
                } else {
                    $qrcode = NULL;
                }

                $patient_val['patient_list'][$p]['patient_id'] = $patient1->patient_id;
                $patient_val['patient_list'][$p]['title'] = ucwords($patient1->title);
                $patient_val['patient_list'][$p]['first_name'] = strtoupper($patient1->first_name);
                $patient_val['patient_list'][$p]['middle_name'] = strtoupper($patient1->middle_name);
                $patient_val['patient_list'][$p]['last_name'] = strtoupper($patient1->last_name);
                $patient_val['patient_list'][$p]['full_name'] =  ($patient1->title ? ucfirst($patient1->title).". " : "").strtoupper(trim($patient1->first_name)." ".trim($patient1->last_name));
                $patient_val['patient_list'][$p]['umr_no'] = $patient1->umr_no;
                $patient_val['patient_list'][$p]['contact'] = $patient1->mobile;
                $patient_val['patient_list'][$p]['alternate_mobile'] = $patient1->alternate_mobile;
                $patient_val['patient_list'][$p]['age'] = $patient1->age . " " . ucfirst($patient1->age_unit);
                $patient_val['patient_list'][$p]['gender'] = $patient1->gender;
                $patient_val['patient_list'][$p]['referred_by'] = $patient1->referred_by;
                $patient_val['patient_list'][$p]['payment_status'] = $patient1->payment_status;
                $patient_val['patient_list'][$p]['clinic_id'] = $patient1->clinic_id;
                $patient_val['patient_list'][$p]['patient_condition'] = $patient1->patient_condition;
                $patient_val['patient_list'][$p]['condition_months'] = $patient1->condition_months;
                $patient_val['patient_list'][$p]['status'] = $patient_status;
                $patient_val['patient_list'][$p]['occupation'] = $patient1->occupation;
                $patient_val['patient_list'][$p]['country'] = $patient1->country;
                $patient_val['patient_list'][$p]['date_of_birth'] = ($patient1->date_of_birth == '0000-00-00' ? "" : $patient1->date_of_birth);
                $patient_val['patient_list'][$p]['email'] = $patient1->email_id;
                $patient_val['patient_list'][$p]['address'] = $patient1->address_line . "," . $patient1->district_name . "," . $patient1->state_name . "," . $patient1->ppcode;

                    //eliminate comma
                $patient_val['patient_list'][$p]['address'] = $this->eliminateComma($patient_val['patient_list'][$p]['address']);

                $patient_val['patient_list'][$p]['registartion_date'] = date("Y-m-d", strtotime($patient1->created_date_time));
                $patient_val['patient_list'][$p]['photo'] = $path;
                $patient_val['patient_list'][$p]['qrcode'] = $qrcode;

                $video_info = $this->db->query("SELECT * from users where user_id='" . $patient1->patient_id . "'")->row();

                $patient_val['patient_list'][$p]['qb_user_id'] = $video_info->qb_user_id;
                    //$patient_val['patient_list'][$p]['qb_password'] =$video_info->qb_password;
                $patient_val['patient_list'][$p]['qb_user_login'] = $video_info->qb_user_login;
                $patient_val['patient_list'][$p]['qb_user_fullname'] = $video_info->qb_user_fullname;
                $patient_val['patient_list'][$p]['qb_user_tag'] = $video_info->qb_user_tag;
                if($im_count > 0){
                    $patient_val['patient_list'][$p]['immunization_status'] = 1;
                }
                else{
                    $patient_val['patient_list'][$p]['immunization_status'] = 0;
                }
                $p++;

            }
            
            
            $this->response(array('code' => '200', 'message' => 'success ', 'result' => $patient_val, 'requestname' => $method));
        } else {
            $this->response(array('code' => '200', 'message' => 'NO data Found', 'result' => NULL), 200);
        }
    }


	//sending otp to mobile and email(vikram)
    public function forgot_password_otp($parameters, $method, $user_id) {
        $check_user = $this->db->query("select * from patients where (email_id='" . $parameters['email_id'] . "' or mobile='".$parameters['email_id']."')")->row();

        if (count($check_user) > 0) {
            $last_four_digits = str_replace(range(0, 9), "*", substr($check_user->mobile, 0, -4)) . substr($check_user->mobile, -4);
            $random_otp = mt_rand(100000, 999999);
            $mobile = $check_user->mobile;
            $message = "OTP is " . $random_otp . " to reset your password.";
            sendsms($mobile, $message);
            $result['otp'] = $random_otp;
            $result['user_id'] = $check_user->user_id;
            $result['user_name'] = $check_user->username;
            $this->response(array('code' => '200', 'message' => 'OTP Sent On Mobile ' . $last_four_digits, 'result' => $result, 'requestname' => $method));
        } else {
            $this->response(array('code' => '404', 'message' => 'User Does Notasd Exist'), 200);
        }
    }

    //after otp verification updating new password(vikram)
    public function forgot_password_update($parameters, $method, $user_id) {

        $param['password'] = md5($parameters['password']);
        $ok = $this->Generic_model->updateData('patients', $param, array('patient_id' => $parameters['user_id']));
        if ($ok == 1) {
            $this->response(array('code' => '200', 'message' => 'Successfully Password Changed ', 'requestname' => $method));
        } else {
            $this->response(array('code' => '404', 'message' => 'Update Password Failed'), 200);
        }
    }

	//update password(vikram)
    public function change_password($parameters, $method, $user_id) {
        $password = $parameters['new_password'];
        $old_password = $parameters['old_password'];
        //checking old password with user input
        $check_old_password = $this->db->query("select password from patients where password = '" . $old_password . "' and patient_id='".$user_id."'")->row();
        if ($check_old_password->password == $old_password) {
            if ($password != "") {
                $param['password'] = md5($password);
                $param['modified_date_time'] = date("Y-m-d H:i:s");
                $param['modified_by'] = $user_id;
                $ok = $this->Generic_model->updateData('patients', $param, array('patient_id' => $user_id));
                if ($ok == 1) {
                    $this->response(array('code' => '200', 'message' => 'successfully Password Changed ', 'requestname' => $method));
                } else {
                    $this->response(array('code' => '404', 'message' => 'Update Password Failed'), 200);
                }
            } else {
                $this->response(array('code' => '404', 'message' => 'Empty Password'), 200);
            }
        } else {
            $this->response(array('code' => '404', 'message' => 'Old password Not matching'), 200);
        }
    }

    public function patientHealthRecords($parameters, $method, $user_id) {

        $today = date('Y-m-d');
        $getAppointments = $this->db->query("select a.*,b.*,c.* from appointments a inner join doctors b on (a.doctor_id=b.doctor_id) inner join department c on (b.department_id=c.department_id) where a.patient_id='".$parameters['patient_id']."' and a.appointment_date<='".$today."'  order by a.appointment_id DESC")->result();

        // echo $this->db->last_query();
        // exit();
        
        $a=0;
        foreach($getAppointments as $ga)
        {
            $data['health_records'][$a]['patient_id'] = $ga->patient_id;
            $data['health_records'][$a]['appointmet_id'] = $ga->appointment_id;
            $data['health_records'][$a]['doctor_name'] = "Dr.".$ga->first_name." ".$ga->last_name;
            $data['health_records'][$a]['designation'] = $ga->department_name;
            $data['health_records'][$a]['appointment_date'] = date('d-M-Y',strtotime($ga->appointment_date));
            $data['health_records'][$a]['appointment_time'] = $ga->appointment_time_slot;
            $a++;
        }   
        $this->response(array('code' => '200', 'message' => 'Patient Appoitment Records', 'result' => $data, 'requestname' => $method));
    }
    
    // public function healtRecordPDF($parameters, $method, $user_id) {
    //     extract($parameters);
    //     $data['url'] = base_url("ApiWebView/mySummary/".$appointment_id);

    //     $this->response(array('code' => '200', 'message' => 'PDF File', 'result' => $data['url'], 'requestname' => $method));
    // }

    //taking appreciation and feedback from patient 
public function feedback_submit($parameters, $method, $user_id) {

    $appreciation = $parameters['appreciation'];
    $feedback = $parameters['feedback'];
    $patient_id = $parameters['patient_id'];
    $doctor_id = $parameters['doctor_id'];
    $clinic_id = $parameters['clinic_id'];

    $get_last_appointment = $this->db->query("select appointment_id from appointments where patient_id = '".$patient_id."' and doctor_id='".$doctor_id."' and (status!='drop' or status!='booked') order by appointment_date DESC")->row();

    if($appreciation !=""){
        $app['patient_id'] = $patient_id;
        $app['doctor_id']  = $doctor_id;
        $app['clinic_id'] = $clinic_id;
        $app['appointment_id'] = $get_last_appointment->appointment_id;
        $app['description']  = $appreciation;
        $app['appreciation_status'] = 1;
        $app['skip_status'] = 0;
        $app['created_by'] = $user_id;
        $app['created_date_time'] = date("Y-m-d H:i:s");
        $this->Generic_model->insertDataReturnId('patient_doctor_appreciation', $app);
    }
    if($feedback !=""){
        $fb['patient_id'] = $patient_id;
        $fb['doctor_id']  = $doctor_id;
        $fb['clinic_id'] = $clinic_id;
        $app['feedback_status'] = 1;
        $app['skip_status'] = 0;
        $app['appointment_id'] = $get_last_appointment->appointment_id;
        $fb['description']  = $feedback;
        $fb['created_by'] = $user_id;
        $fb['created_date_time'] = date("Y-m-d H:i:s");
        $this->Generic_model->insertDataReturnId('patient_doctor_feedback', $fb);
    }


    $this->response(array('code' => '200', 'message' => 'Data Submitted Successfully','requestname' => $method));
}

      //fetching appreciation and feedback of patient 
public function feedback_list($parameters, $method, $user_id) {



    $appreciation_info = $this->db->query("select * from patient_doctor_appreciation  where patient_id='".$user_id."'")->result();

    if(count($appreciation_info)>0){


      $j=0;
      foreach($appreciation_info as $app_info){            
        $doctor_list = $this->db->query("select * from doctors where doctor_id ='".$app_info->doctor_id."'")->row();

        $data['appreciation'][$j]['doctor_name'] = $doctor_list->first_name." ".$doctor_list->last_name;
        $data['appreciation'][$j]['posted_date'] = date("d-M-Y",strtotime($app_info->created_date_time));
        $data['appreciation'][$j]['appointmet_id'] = $app_info->appointmet_id;
        $data['appreciation'][$j]['remark'] = $app_info->description;


        $j++;
    }
}
else{
    $data['appreciation'] = [];
}

$feedback_info = $this->db->query("select * from patient_doctor_feedback  where patient_id='".$user_id."'")->result();

if(count($feedback_info)>0){


  $j=0;
  foreach($feedback_info as $fb_info){            
    $doctor_list = $this->db->query("select * from doctors where doctor_id ='".$fb_info->doctor_id."'")->row();

    $data['feedback'][$j]['doctor_name'] = $doctor_list->first_name." ".$doctor_list->last_name;
    $data['feedback'][$j]['posted_date'] = date("d-M-Y",strtotime($fb_info->created_date_time));
    $data['feedback'][$j]['remark'] = $fb_info->description;
    $data['feedback'][$j]['appointmet_id'] = $fb_info->appointmet_id;


    $j++;
}
}
else{
    $data['feedback'] = [];
}


$this->response(array('code' => '200', 'message' => 'Feedback Records', 'result' => $data, 'requestname' => $method));


}

public function patientProfile($parameters, $method, $user_id) {

    $getPatient = $this->db->query("Select * from patients a left join districts b on (a.district_id=b.district_id) left join states c on (a.state_id=c.state_id) where a.patient_id='".$parameters['patient_id']."'")->row();

    $data['patient_id'] = $getPatient->patient_id;
    $data['title'] = $getPatient->title;
    $data['first_name'] = $getPatient->first_name;
    $data['middle_name'] = $getPatient->middle_name;
    $data['last_name'] = $getPatient->last_name;
    $data['umr_no'] = $getPatient->umr_no;
    $data['gender'] = $getPatient->gender;
    if(empty($getPatient->date_of_birth)) { 
        $data['date_of_birth'] = "";
    } 
    else 
    {  
        $data['date_of_birth'] = date('Y-m-d', strtotime($getPatient->date_of_birth)); 
    }
    $data['age'] = $getPatient->age;
    $data['occupation'] = $getPatient->occupation;
    $data['country'] = $getPatient->country;
    $data['mobile'] = $getPatient->mobile;
    $data['email_id'] = $getPatient->email_id;
    $data['address_line'] = $getPatient->address_line;
    $data['district'] = $getPatient->district_name;
    $data['state'] = $getPatient->state_name;
    $data['alternate_mobile'] = $getPatient->alternate_mobile;
    $data['pincode'] = $getPatient->pincode;
    $data['preferred_language'] = $getPatient->preferred_language;
    $data['referred_by_type'] = $getPatient->referred_by_type;
    $data['referred_by'] = $getPatient->referred_by;
    $data['photo'] = base_url('uploads/patients/'.$getPatient->photo);
    $data['qrcode'] = base_url('uploads/qrcodes/'.$getPatient->qrcode);

    $this->response(array('code' => '200', 'message' => 'Patient Appoitment Records', 'result' => $data, 'requestname' => $method));
}

// Filters for doctors search
public function docList($parameters, $method, $user_id){
    extract($parameters);
    $presentDay = date("N");
    $present_time = date("H:i:s");
    if($experience != ""){
        $exQ = $experience;
    }
    elseif($experience == ""){
        $exQ = "DESC";
    }

    $doctors = $this->db->query("select * from doctors where department_id='".$department_id."' order by experience ".$exQ)->result();
    if(count($doctors) > 0){
        $i = 0;
        foreach($doctors as $doc){
            // if($price != ""){
            //     $priceQ = "cd."
            // }
            // else{
            //     $priceQ = "";
            // }
            $clinicsInfo = $this->db->query("select c.clinic_id,c.clinic_name,CONCAT(c.address,' ',c.location) as clinic_address,c.state_id,c.district_id,c.clinic_phone,c.email,c.pincode from clinics c,clinic_doctor cd where c.clinic_id=cd.clinic_id and cd.doctor_id='".$doc->doctor_id."'")->result();
            $docInfo = $this->db->query("SELECT cd.doctor_id,cd.consulting_fee,cd.online_consulting_fee,cd.clinic_id from clinic_doctor cd,clinic_doctor_weekdays cwd, clinic_doctor_weekday_slots cwds where cd.clinic_doctor_id=cwd.clinic_doctor_id and cwd.clinic_doctor_weekday_id=cwds.clinic_doctor_weekday_id and cwd.weekday='".$presentDay."' and cd.doctor_id='".$doc->doctor_id."' and '".$present_time."' > cwds.from_time and '".$present_time."' < cwds.to_time group by cd.doctor_id order by cwds.from_time ASC")->row();
            // echo $this->db->last_query();
            $clinicsData = getDocClinicDetails($doc->doctor_id);
            $docDetails = doctorDetails($doc->doctor_id);
            if(count($docInfo) > 0){
                    $para['docList'][$i]['doctor_id'] = $docInfo->doctor_id;
                    $para['docList'][$i]['doctor_name'] = getDoctorName($docInfo->doctor_id);
                    $para['docList'][$i]['experience'] = $doc->experience;
                    $para['docList'][$i]['languages'] = ucwords(implode(", ", explode(",", $doc->languages)));
                    $para['docList'][$i]['consultation_fee'] = $docInfo->consulting_fee;
                    $para['docList'][$i]['online_consulting_fee'] = $docInfo->online_consulting_fee;
                    $para['docList'][$i]['qualification'] = $docDetails->qualification;
                    $para['docList'][$i]['department_name'] = $docDetails->department_name;
                    $para['docList'][$i]['profile_pic'] = base_url('uploads/doctors/'.$docDetails->profile_image);
                    $para['docList'][$i]['availability'] = "Available";
                    $clinics = array();
                    $k = 0;
                    foreach($clinicsInfo as $clinicValue){
                        $stateInfo = $this->db->query("select * from states where state_id='".$clinicValue->state_id."'")->row();
                        $districtInfo = $this->db->query("select * from districts where district_id='".$clinicValue->district_id."'")->row();
                        $clinics[$k]['clinic_id'] = $clinicValue->clinic_id;
                        $clinics[$k]['clinic_name'] = $clinicValue->clinic_name;
                        $clinics[$k]['clinic_address'] = $clinicValue->clinic_address.", ".$districtInfo->district_name.", ".$stateInfo->state_name." - ".$clinicValue->pincode;
                        $clinics[$k]['clinic_phone'] = $clinicValue->clinic_phone;
                        $clinics[$k]['email'] = $clinicValue->email;
                        $k++;
                    }
                    $para['docList'][$i]['clinics'] = $clinics;
            }
            else{
                $docQ = $this->db->query("SELECT doctor_id,consulting_fee,online_consulting_fee,clinic_id from clinic_doctor where doctor_id='".$doc->doctor_id."' group by doctor_id ")->row();
                if(count($docQ) > 0){
                    $para['docList'][$i]['doctor_id'] = $docQ->doctor_id;
                    $para['docList'][$i]['doctor_name'] = getDoctorName($docQ->doctor_id);
                    $para['docList'][$i]['experience'] = $doc->experience;
                    $para['docList'][$i]['languages'] = ucwords(implode(", ", explode(",", $doc->languages)));
                    $para['docList'][$i]['consultation_fee'] = $docQ->consulting_fee;
                    $para['docList'][$i]['online_consulting_fee'] = $docQ->online_consulting_fee;
                    $para['docList'][$i]['qualification'] = $docDetails->qualification;
                    $para['docList'][$i]['department_name'] = $docDetails->department_name;
                    $para['docList'][$i]['profile_pic'] = base_url('uploads/doctors/'.$docDetails->profile_image);
                    $para['docList'][$i]['availability'] = "Not Available";
                    $clinics = array();
                    $k = 0;
                    foreach($clinicsInfo as $clinicValue){
                        $stateInfo = $this->db->query("select * from states where state_id='".$clinicValue->state_id."'")->row();
                        $districtInfo = $this->db->query("select * from districts where district_id='".$clinicValue->district_id."'")->row();
                        $clinics[$k]['clinic_id'] = $clinicValue->clinic_id;
                        $clinics[$k]['clinic_name'] = $clinicValue->clinic_name;
                        $clinics[$k]['clinic_address'] = $clinicValue->clinic_address.", ".$districtInfo->district_name.", ".$stateInfo->state_name." - ".$clinicValue->pincode;
                        $clinics[$k]['clinic_phone'] = $clinicValue->clinic_phone;
                        $clinics[$k]['email'] = $clinicValue->email;
                        $k++;
                    }
                    $para['docList'][$i]['clinics'] = $clinics;
                }
                
            }
            $i++;            
        }    
        
        $this->response(array('code'=>'200','message'=>'Success','result'=>$para));
    }
    else{
        $para['docList'] = [];
        $this->response(array('code'=>'201','message'=>'Error Occurred','result'=>$para));
    }
}

public function getDocTeleSlots($parameters, $method, $user_id){
    extract($parameters);
    if($doctor_id != ""){
        $days = array('','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
        $check = $this->db->query("select cwd.slot,cd.clinic_id,cwd.clinic_doctor_weekday_id,cwd.weekday from clinic_doctor cd,clinic_doctor_weekdays cwd where cd.clinic_doctor_id=cwd.clinic_doctor_id and cd.doctor_id='".$doctor_id."' and cwd.slot='video call' group by cwd.weekday,cd.clinic_id")->result();  
        // echo $this->db->last_query();
        if(count($check) > 0){
            $i = 0;
            foreach($check as $value){
                $slotsInfo = $this->db->query("select * from clinic_doctor_weekday_slots where clinic_doctor_weekday_id='".$value->clinic_doctor_weekday_id."'")->result();  
                if(count($slotsInfo) > 0){
                    $j = 0;
                    $para['consultation'][$i]['day'] = $days[$value->weekday];
                    foreach($slotsInfo as $val){
                        $para['consultation'][$i]['timings'][$j]['session'] = $val->session;
                        $para['consultation'][$i]['timings'][$j]['schedule'] = date('h:i A', strtotime($val->from_time)).'-'.date('h:i A', strtotime($val->to_time));
                        // $para['consultation'][$i]['timings'][$j]['to_time'] = $val->to_time;
                        $j++;
                    }
                }
                $i++;
            }
            
            $this->response(array('code'=>'200','message'=>'Success','result'=>$para,'requestname'=>$method));  
        }
        else{
            $para['consultation'] = [];
            $this->response(array('code'=>'201','message'=>'Slots Not Present','result'=>$para,'requestname'=>$method));    
        }
    }
    else{
        $para['consultation'] = [];
        $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$para,'requestname'=>$method));
    }
}

// public function getDocTeleSlots($parameters, $method, $user_id){
//     extract($parameters);
//     if($doctor_id != ""){
//         $days = array('','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
//         $check = $this->db->query("select cwd.slot,cd.clinic_id,cwd.clinic_doctor_weekday_id,cwd.weekday from clinic_doctor cd,clinic_doctor_weekdays cwd where cd.clinic_doctor_id=cwd.clinic_doctor_id and cd.doctor_id='".$doctor_id."' and cwd.slot='video call'")->result();  
//         if(count($check) > 0){
//             $i = 0;
//             foreach($check as $value){
//                 $slotsInfo = $this->db->query("select * from clinic_doctor_weekday_slots where clinic_doctor_weekday_id='".$value->clinic_doctor_weekday_id."'")->result();  
//                 if(count($slotsInfo) > 0){
//                     $j = 0;
//                     $para['consultation'][$i]['day'] = $days[$value->weekday];
//                     foreach($slotsInfo as $val){
//                         $para['consultation'][$i]['timings'][$j]['session'] = $val->session;
//                         $para['consultation'][$i]['timings'][$j]['from_time'] = $val->from_time;
//                         $para['consultation'][$i]['timings'][$j]['to_time'] = $val->to_time;
//                         $j++;
//                     }
//                 }
//                 $i++;
//             }
            
//             $this->response(array('code'=>'200','message'=>'Success','result'=>$para,'requestname'=>$method));  
//         }
//         else{
//             $para['consultation'] = [];
//             $this->response(array('code'=>'201','message'=>'Invalid Doctor ID','result'=>$para,'requestname'=>$method));    
//         }
//     }
//     else{
//         $para['consultation'] = [];
//         $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$para,'requestname'=>$method));
//     }
// }

public function getDocProfile($parameters, $method, $user_id){
    extract($parameters);
    $doctor_list = $this->Generic_model->getSingleRecord('doctors', array('doctor_id'=>$doctor_id));
    if(count($doctor_list) > 0){
        $doctor_degrees_list = $this->db->query("select * from doctor_degrees where doctor_id ='".$doctor_id."'")->result();
        $clinicsInfo = $this->db->query("select c.clinic_id,c.clinic_name,CONCAT(c.address,' ',c.location) as clinic_address,c.state_id,c.district_id,c.clinic_phone,c.email,c.pincode from clinics c,clinic_doctor cd where c.clinic_id=cd.clinic_id and cd.doctor_id='".$doctor_id."'")->result();
        // echo $this->db->last_query();
        $clinics = array();
        $k = 0;
        foreach($clinicsInfo as $clinicValue){
            $stateInfo = $this->db->query("select * from states where state_id='".$clinicValue->state_id."'")->row();
            $districtInfo = $this->db->query("select * from districts where district_id='".$clinicValue->district_id."'")->row();
            $clinics[$k]['clinic_id'] = $clinicValue->clinic_id;
            $clinics[$k]['clinic_name'] = $clinicValue->clinic_name;
            $clinics[$k]['clinic_address'] = $clinicValue->clinic_address.", ".$districtInfo->district_name.", ".$stateInfo->state_name." - ".$clinicValue->pincode;
            $clinics[$k]['clinic_phone'] = $clinicValue->clinic_phone;
            $clinics[$k]['email'] = $clinicValue->email;
            $k++;
        }
        $degree_name = array();
        foreach($doctor_degrees_list as $degree_val){
            $degree_name[] = $degree_val->degree_name;
        }
        $doctor_degree = implode(",", $degree_name);
        $deptInfo = $this->db->query("select * from department where department_id='".$doctor_list->department_id."'")->row();
        $data['doctors']['doctor_id'] = $doctor_list->doctor_id;
        $data['doctors']['specialization'] =$doctor_list->speciality ;
        $data['doctors']['department_name'] =$deptInfo->department_name;
        $data['doctors']['name'] = $doctor_list->first_name." ".$doctor_list->last_name;
        $data['doctors']['experience'] = $doctor_list->experience." Years";
        $data['doctors']['about_doctor'] = $doctor_list->about ;
        $data['doctors']['contact'] = $doctor_list->mobile ;
        $data['doctors']['google_review_link'] = $doctor_list->google_review_link ;
        $data['doctors']['gender'] = strtoupper($doctor_list->gender) ;
        $data['doctors']['visit_time'] = "" ;
        $data['doctors']['known_languages'] = $doctor_list->languages ;
        $data['doctors']['dealt_deseases'] = $doctor_list->diseases_dealt;
        $data['doctors']['degrees'] = $doctor_degree;
        $data['doctors']['qualification'] = $doctor_list->qualification;
        $data['doctors']['clinics'] = $clinics;

        $achievements = $doctor_list->acheivements;
        $arra_ach = explode(",", $achievements);

        if($achievements == "" || $achievements == "null"){
            $data['doctors']['achievements'] = array();
        }else{
            $data['doctors']['achievements'] = $arra_ach;

        }

        $data['doctors']['membership'] = $doctor_list->membership_in;
        $data['doctors']['address'] = $doctor_list->address;
        $data['doctors']['doctor_image'] =  base_url("uploads/doctors/".$doctor_list->profile_image."");
        $this->response(array('code'=>'200','message'=>'Doctors Data','result'=>$data,'requestname'=>$method));
    }
}


//Get Individual Doctor Slots

public function getIndDoc($parameters, $method, $user_id) {

            $clinic_id = $parameters['clinic_id'];
            $doctor_id = $parameters['doctor_id'];
    
            // get all doctors belongs to the clinic '$clinic_id'
            $doctorInfo = $this->db->select("cd.*,d.*,de.*,cd.clinic_id")->from("clinic_doctor cd")->join("doctors d","cd.doctor_id=d.doctor_id")->join("department de","d.department_id=de.department_id")->where("d.doctor_id='".$doctor_id."'")->group_by("cd.doctor_id")->order_by("d.doctor_id","asc")->get()->row();
    
            if (count($doctorInfo) > 0) {
                    $param['doctor']['doctor_id'] = $doctorInfo->doctor_id;
                    $param['doctor']['doctor_name'] = "Dr. " . strtoupper($doctorInfo->first_name . " " . $doctorInfo->last_name);
                    $param['doctor']['designation'] = $doctorInfo->qualification;
                    $param['doctor']['department'] = $doctorInfo->department_name;
                    $param['doctor']['registration_code'] = $doctorInfo->registration_code;
                    $param['doctor']['color_code'] = $doctorInfo->color_code;
    
                    $clinics_list = $this->db->select("cd.*,c.*,cd.clinic_id")->from("clinic_doctor cd")->join("clinics c","cd.clinic_id=c.clinic_id")->where("cd.doctor_id ='" . $doctorInfo->doctor_id . "'")->group_by("cd.clinic_id")->order_by("c.clinic_id","asc")->get()->result();
    
                    if (count($clinics_list) > 0) {
                        $c = 0;
                        foreach ($clinics_list as $clinic) {
                            $cdw = 0;$cdww = 0;
                            $param['doctor']['clinics'][$c]['clinic_id'] = $clinic->clinic_id;
                            $param['doctor']['clinics'][$c]['clinic_name'] = $clinic->clinic_name;
                            $param['doctor']['clinics'][$c]['consultation_time'] = $clinic->consulting_time;
                            $param['doctor']['clinics'][$c]['online_consulting_fee'] = $clinic->online_consulting_fee;
                            $param['doctor']['clinics'][$c]['consulting_fee'] = $clinic->consulting_fee;
                            $param['doctor']['clinics'][$c]['registration_fee'] = $clinic->registration_fee;
                            $param['doctor']['clinics'][$c]['pharmacy_discount'] = $clinic->pharmacy_discount;
                            $param['doctor']['clinics'][$c]['lab_discount'] = $clinic->lab_discount;
    
                            $week_day_list = $this->db->select("clinic_doctor_weekday_id,weekday")->from("clinic_doctor_weekdays")->where("clinic_doctor_id ='" . $clinic->clinic_doctor_id . "' and slot='walkin'")->group_by("weekday")->order_by("clinic_doctor_weekday_id","asc")->get()->result();
    
                
    
                            foreach ($week_day_list as $weekday) {
                                // initiates weekdays to the number
                                if ($weekday->weekday == 1) {
                                    $week = "Monday";
                                }
                                if ($weekday->weekday == 2) {
                                    $week = "Tuesday";
                                }
                                if ($weekday->weekday == 3) {
                                    $week = "Wednesday";
                                }
                                if ($weekday->weekday == 4) {
                                    $week = "Thursday";
                                }
                                if ($weekday->weekday == 5) {
                                    $week = "Friday";
                                }
                                if ($weekday->weekday == 6) {
                                    $week = "Saturday";
                                }
                                if ($weekday->weekday == 7) {
                                    $week = "Sunday";
                                }
        
                                $week_day_list_slots = $this->db->select("*")->from("clinic_doctor_weekday_slots")->where("clinic_doctor_weekday_id ='" . $weekday->clinic_doctor_weekday_id . "'")->order_by("clinic_doctor_weekday_id","asc")->get()->result();
                                
                                $cdws = 0;
                                foreach ($week_day_list_slots as $wdls) {
                                    // $day[]=$wdls->session;
                                    $sessions_list[] = date('h:i A', strtotime($wdls->from_time)) . "-" . date('h:i A', strtotime($wdls->to_time)). "-" .$wdls->session;
                                    $cdws++;
                                }
        
                                $param['doctor']['clinics'][$c]['working_days'][$cdw]['day'] = $week;
                                // $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['session'] = $sessions_list;
        
                                for ($i = 0; $i < count($sessions_list); $i++) {
                                    $sl = explode("-", $sessions_list[$i]);
                                    $time = date("H", strtotime($sl[0]));
                                    if ($sl[2] == 'morning') {
                                        $param['doctor']['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = 'Morning';
                                        $param['doctor']['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                                    } else if ($sl[2] == 'afternoon') {
                                        $param['doctor']['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = 'Afternoon';
                                        $param['doctor']['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                                    } else if ($sl[2] == 'evening') {
                                        $param['doctor']['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] ='Evening';
                                        $param['doctor']['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                                    }
        
                                    // if ($time < 12) {
                                    //     $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = $sl[2];
                                    //     $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                                    // } else if ($time > 12 && $time < 17) {
                                    //     $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = $sl[2];
                                    //     $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                                    // } else if ($time >= 17) {
                                    //     $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = $sl[2];
                                    //     $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                                    // }
                                }
                                unset($sessions_list);
                                $cdw++;
                            }
                            $week_day_list = $this->db->select("clinic_doctor_weekday_id,weekday")->from("clinic_doctor_weekdays")->where("clinic_doctor_id ='" . $clinic->clinic_doctor_id . "' and slot='video call'")->group_by("weekday")->order_by("clinic_doctor_weekday_id","asc")->get()->result();
    
                
                            if(count($week_day_list)>0)
                            {
                            foreach ($week_day_list as $weekday) {
                                // initiates weekdays to the number
                                if ($weekday->weekday == 1) {
                                    $week = "Monday";
                                }
                                if ($weekday->weekday == 2) {
                                    $week = "Tuesday";
                                }
                                if ($weekday->weekday == 3) {
                                    $week = "Wednesday";
                                }
                                if ($weekday->weekday == 4) {
                                    $week = "Thursday";
                                }
                                if ($weekday->weekday == 5) {
                                    $week = "Friday";
                                }
                                if ($weekday->weekday == 6) {
                                    $week = "Saturday";
                                }
                                if ($weekday->weekday == 7) {
                                    $week = "Sunday";
                                }
        
                                $week_day_list_slots = $this->db->select("*")->from("clinic_doctor_weekday_slots")->where("clinic_doctor_weekday_id ='" . $weekday->clinic_doctor_weekday_id . "'")->order_by("clinic_doctor_weekday_id","asc")->get()->result();
        
                                $cdws = 0;
                                foreach ($week_day_list_slots as $wdls) {
                                    // $sessions_list[] = date('h:i A', strtotime($wdls->from_time)) . "-" . date('h:i A', strtotime($wdls->to_time));
                                    $sessions_list[] = date('h:i A', strtotime($wdls->from_time)) . "-" . date('h:i A', strtotime($wdls->to_time)). "-" .$wdls->session;
                                    $cdws++;
                                }
        
                                $param['doctor']['clinics'][$c]['videocall_working_days'][$cdww]['day'] = $week;
                                // $param['doctor'][$d]['clinics'][$c]['videocall_working_days'][$cdww]['session'] = $sessions_listt;
        
                                for ($i = 0; $i < count($sessions_list); $i++) {
                                    $sl = explode("-", $sessions_list[$i]);
                                    $time = date("H", strtotime($sl[0]));
        
                                    if ($sl[2] == 'morning') {
                                        $param['doctor']['clinics'][$c]['videocall_working_days'][$cdww]['timings'][$i]['session'] = 'Morning';
                                        $param['doctor']['clinics'][$c]['videocall_working_days'][$cdww]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                                    } else if ($sl[2] == 'afternoon') {
                                        $param['doctor']['clinics'][$c]['videocall_working_days'][$cdww]['timings'][$i]['session'] = 'Afternoon';
                                        $param['doctor']['clinics'][$c]['videocall_working_days'][$cdww]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                                    } else if ($sl[2] == 'evening') {
                                        $param['doctor']['clinics'][$c]['videocall_working_days'][$cdww]['timings'][$i]['session'] ='Evening';
                                        $param['doctor']['clinics'][$c]['videocall_working_days'][$cdww]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                                    }
                                }
                                unset($sessions_list);
                                $cdww++;
                            }
                        }
                        else{
                            $param['doctor']['clinics'][$c]['videocall_working_days'] = array();
                        }
        
        
    
                            $c++;
                        }
                    } else {
                        $param['doctor']['clinics'] = array();
                    }
    
            } else {
                $param['doctor'] = array();
            }
    
            $this->response(array('code' => '200', 'message' => 'Doctor Time Slots Session Wise', 'result' => $param, 'requestname' => $method));
        }
    

// public function getIndDoc($parameters, $method, $user_id) {

//         $clinic_id = $parameters['clinic_id'];
//         $doctor_id = $parameters['doctor_id'];

//         // get all doctors belongs to the clinic '$clinic_id'
//         $doctorInfo = $this->db->select("cd.*,d.*,de.*,cd.clinic_id")->from("clinic_doctor cd")->join("doctors d","cd.doctor_id=d.doctor_id")->join("department de","d.department_id=de.department_id")->where("d.doctor_id='".$doctor_id."'")->group_by("cd.doctor_id")->order_by("d.doctor_id","asc")->get()->row();

//         if (count($doctorInfo) > 0) {
//                 $param['doctor']['doctor_id'] = $doctorInfo->doctor_id;
//                 $param['doctor']['doctor_name'] = "Dr. " . strtoupper($doctorInfo->first_name . " " . $doctorInfo->last_name);
//                 $param['doctor']['designation'] = $doctorInfo->qualification;
//                 $param['doctor']['department'] = $doctorInfo->department_name;
//                 $param['doctor']['registration_code'] = $doctorInfo->registration_code;
//                 $param['doctor']['color_code'] = $doctorInfo->color_code;

//                 $clinics_list = $this->db->select("cd.*,c.*,cd.clinic_id")->from("clinic_doctor cd")->join("clinics c","cd.clinic_id=c.clinic_id")->where("cd.doctor_id ='" . $doctorInfo->doctor_id . "'")->group_by("cd.clinic_id")->order_by("c.clinic_id","asc")->get()->result();

//                 if (count($clinics_list) > 0) {
//                     $c = 0;
//                     foreach ($clinics_list as $clinic) {
//                         $cdw = 0;
//                         $param['doctor']['clinics'][$c]['clinic_id'] = $clinic->clinic_id;
//                         $param['doctor']['clinics'][$c]['clinic_name'] = $clinic->clinic_name;
//                         $param['doctor']['clinics'][$c]['consultation_time'] = $clinic->consulting_time;
//                         $param['doctor']['clinics'][$c]['consulting_fee'] = $clinic->consulting_fee;
//                         $param['doctor']['clinics'][$c]['registration_fee'] = $clinic->registration_fee;
//                         $param['doctor']['clinics'][$c]['pharmacy_discount'] = $clinic->pharmacy_discount;
//                         $param['doctor']['clinics'][$c]['lab_discount'] = $clinic->lab_discount;

//                         $week_day_list = $this->db->select("clinic_doctor_weekday_id,weekday")->from("clinic_doctor_weekdays")->where("clinic_doctor_id ='" . $clinic->clinic_doctor_id . "' and slot='walkin'")->group_by("weekday")->order_by("clinic_doctor_weekday_id","asc")->get()->result();


//                         foreach ($week_day_list as $weekday) {
//                             // initiates weekdays to the number
//                             if ($weekday->weekday == 1) {
//                                 $week = "Monday";
//                             }
//                             if ($weekday->weekday == 2) {
//                                 $week = "Tuesday";
//                             }
//                             if ($weekday->weekday == 3) {
//                                 $week = "Wednesday";
//                             }
//                             if ($weekday->weekday == 4) {
//                                 $week = "Thursday";
//                             }
//                             if ($weekday->weekday == 5) {
//                                 $week = "Friday";
//                             }
//                             if ($weekday->weekday == 6) {
//                                 $week = "Saturday";
//                             }
//                             if ($weekday->weekday == 7) {
//                                 $week = "Sunday";
//                             }

//                             $week_day_list_slots = $this->db->select("*")->from("clinic_doctor_weekday_slots")->where("clinic_doctor_weekday_id ='" . $weekday->clinic_doctor_weekday_id . "'")->order_by("clinic_doctor_weekday_id","asc")->get()->result();

//                             if(count($week_day_list_slots)>0)
//                             {
//                                 $cdws = 0;
//                                 foreach ($week_day_list_slots as $wdls) {
//                                     $sessions_list[] = date('h:i A', strtotime($wdls->from_time)) . "-" . date('h:i A', strtotime($wdls->to_time));
//                                     $cdws++;
//                                 }

//                                 $param['doctor']['clinics'][$c]['working_days'][$cdw]['day'] = $week;

//                                 for ($i = 0; $i < count($sessions_list); $i++) {
//                                     $sl = explode("-", $sessions_list[$i]);
//                                     $time = date("H", strtotime($sl[0]));

//                                     if ($time < 12) {
//                                         $param['doctor']['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = "Morning";
//                                         $param['doctor']['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
//                                     } else if ($time > 12 && $time < 17) {
//                                         $param['doctor']['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = "Afternoon";
//                                         $param['doctor']['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
//                                     } else if ($time >= 17) {
//                                         $param['doctor']['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = "Evening";
//                                         $param['doctor']['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
//                                     }
//                                 }
//                             }
//                             else
//                             {
//                                 $param['doctor']['clinics'][$c]['working_days'][$cdw]['day'] = $week;
//                                 $param['doctor']['clinics'][$c]['working_days'][$cdw]['timings'] = "null";
//                             }
                            
//                             unset($sessions_list);
//                             $cdw++;
//                         }

//                         $c++;
//                     }
//                 } else {
//                     $param['doctor']['clinics'] = array();
//                 }

//         } else {
//             $param['doctor'] = array();
//         }

//         $this->response(array('code' => '200', 'message' => 'Doctor Time Slots Session Wise', 'result' => $param, 'requestname' => $method));
//     }


public function patientdoctorProfile($parameters, $method, $user_id){
    $patient_id = $parameters['patient_id'];
    $presentDay = date("N");
    $present_time = date("H:i:s");
    $doctor_appointment_list = $this->db->query("select * from doctor_patient where patient_id='".$patient_id."' group by doctor_id")->result();
    if(count($doctor_appointment_list) > 0){
        $i = 0;
        foreach($doctor_appointment_list as $doc){         
            $docInfo = $this->db->query("SELECT cd.doctor_id,cd.consulting_fee,cd.online_consulting_fee,cd.clinic_id from clinic_doctor cd,clinic_doctor_weekdays cwd, clinic_doctor_weekday_slots cwds where cd.clinic_doctor_id=cwd.clinic_doctor_id and cwd.clinic_doctor_weekday_id=cwds.clinic_doctor_weekday_id and cwd.weekday='".$presentDay."' and cd.doctor_id='".$doc->doctor_id."' and '".$present_time."' > cwds.from_time and '".$present_time."' < cwds.to_time group by cd.doctor_id order by cwds.from_time ASC")->row();
            $clinicsData = getDocClinicDetails($doc->doctor_id);
            $docDetails = doctorDetails($doc->doctor_id);
            $clinicsInfo = $this->db->query("select c.clinic_id,c.clinic_name,CONCAT(c.address,' ',c.location) as clinic_address,c.state_id,c.district_id,c.clinic_phone,c.email,c.pincode from clinics c,clinic_doctor cd where c.clinic_id=cd.clinic_id and cd.doctor_id='".$doc->doctor_id."'")->result();
            if(count($docInfo) > 0){
                    $para['docList'][$i]['doctor_id'] = $docInfo->doctor_id;
                    $para['docList'][$i]['doctor_name'] = getDoctorName($docDetails->doctor_id);
                    $para['docList'][$i]['experience'] = $docDetails->experience;
                    $para['docList'][$i]['languages'] = ucwords(implode(", ", explode(",", $docDetails->languages)));
                    $para['docList'][$i]['consultation_fee'] = $docInfo->consulting_fee;
                    $para['docList'][$i]['online_consulting_fee'] = $docInfo->online_consulting_fee;
                    $para['docList'][$i]['qualification'] = $docDetails->qualification;
                    $para['docList'][$i]['department_name'] = $docDetails->department_name;
                    $para['docList'][$i]['profile_pic'] = base_url('uploads/doctors/'.$docDetails->profile_image);
                    $para['docList'][$i]['availability'] = "Available";
                    $clinics = array();
                    $k = 0;
                    foreach($clinicsInfo as $clinicValue){
                        $stateInfo = $this->db->query("select * from states where state_id='".$clinicValue->state_id."'")->row();
                        $districtInfo = $this->db->query("select * from districts where district_id='".$clinicValue->district_id."'")->row();
                        $clinics[$k]['clinic_id'] = $clinicValue->clinic_id;
                        $clinics[$k]['clinic_name'] = $clinicValue->clinic_name;
                        $clinics[$k]['clinic_address'] = $clinicValue->clinic_address.", ".$districtInfo->district_name.", ".$stateInfo->state_name." - ".$clinicValue->pincode;
                        $clinics[$k]['clinic_phone'] = $clinicValue->clinic_phone;
                        $clinics[$k]['email'] = $clinicValue->email;
                        $k++;
                    }
                    $para['docList'][$i]['clinics'] = $clinics;
            }
            else{
                $docQ = $this->db->query("SELECT doctor_id,consulting_fee,online_consulting_fee,clinic_id from clinic_doctor where doctor_id='".$doc->doctor_id."' group by doctor_id ")->row();
                if(count($docQ) > 0){
                    $para['docList'][$i]['doctor_id'] = $docQ->doctor_id;
                    $para['docList'][$i]['doctor_name'] = getDoctorName($docQ->doctor_id);
                    $para['docList'][$i]['experience'] = $docDetails->experience;
                    $para['docList'][$i]['languages'] = ucwords(implode(", ", explode(",", $docDetails->languages)));
                    $para['docList'][$i]['consultation_fee'] = $docQ->consulting_fee;
                    $para['docList'][$i]['online_consulting_fee'] = $docQ->online_consulting_fee;
                    $para['docList'][$i]['qualification'] = $docDetails->qualification;
                    $para['docList'][$i]['department_name'] = $docDetails->department_name;
                    $para['docList'][$i]['profile_pic'] = base_url('uploads/doctors/'.$docDetails->profile_image);
                    $para['docList'][$i]['availability'] = "Not Available";
                    $clinics = array();
                    $k = 0;
                    foreach($clinicsInfo as $clinicValue){
                        $stateInfo = $this->db->query("select * from states where state_id='".$clinicValue->state_id."'")->row();
                        $districtInfo = $this->db->query("select * from districts where district_id='".$clinicValue->district_id."'")->row();
                        $clinics[$k]['clinic_id'] = $clinicValue->clinic_id;
                        $clinics[$k]['clinic_name'] = $clinicValue->clinic_name;
                        $clinics[$k]['clinic_address'] = $clinicValue->clinic_address.", ".$districtInfo->district_name.", ".$stateInfo->state_name." - ".$clinicValue->pincode;
                        $clinics[$k]['clinic_phone'] = $clinicValue->clinic_phone;
                        $clinics[$k]['email'] = $clinicValue->email;
                        $k++;
                    }
                    $para['docList'][$i]['clinics'] = $clinics;
                }
                
            }
            $i++;    
        }
        $this->response(array('code' => '200', 'message' => 'Docotor Profile', 'result' => $para, 'requestname' => $method));
    }
    else{
        $para['docList'] = [];
        $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$para,'requestname'=>$method));
    }
}

// public function patientdoctor_profile($parameters, $method, $user_id){
//     $patient_id = $parameters['patient_id'];
//     $doctor_appointment_list = $this->db->query("select * from doctor_patient where patient_id='".$patient_id."'")->result();
//     // echo $this->db->last_query();
//     // $doctor_appointment_list = $this->db->query("select * from doctors")->result();

//     $j=0;
//     foreach($doctor_appointment_list as $doctor_val){            
//         $doctor_list = $this->db->query("select * from doctors where doctor_id ='".$doctor_val->doctor_id."'")->row();
//         $doctor_degrees_list = $this->db->query("select * from doctor_degrees where doctor_id ='".$doctor_val->doctor_id."'")->result();
//         $clinicsInfo = $this->db->query("select c.clinic_id,c.clinic_name,CONCAT(c.address,' ',c.location) as clinic_address,c.state_id,c.district_id,c.clinic_phone,c.email,c.pincode from clinics c,clinic_doctor cd where c.clinic_id=cd.clinic_id and cd.doctor_id='".$doctor_val->doctor_id."'")->result();
//         // echo $this->db->last_query();
//         $clinics = array();
//         $k = 0;
//         foreach($clinicsInfo as $clinicValue){
//             $stateInfo = $this->db->query("select * from states where state_id='".$clinicValue->state_id."'")->row();
//             $districtInfo = $this->db->query("select * from districts where district_id='".$clinicValue->district_id."'")->row();
//             $clinics[$k]['clinic_id'] = $clinicValue->clinic_id;
//             $clinics[$k]['clinic_name'] = $clinicValue->clinic_name;
//             $clinics[$k]['clinic_address'] = $clinicValue->clinic_address.", ".$districtInfo->district_name.", ".$stateInfo->state_name." - ".$clinicValue->pincode;
//             $clinics[$k]['clinic_phone'] = $clinicValue->clinic_phone;
//             $clinics[$k]['email'] = $clinicValue->email;
//             $k++;
//         }
//         $degree_name = array();
//         foreach($doctor_degrees_list as $degree_val){
//             $degree_name[] = $degree_val->degree_name;
//         }
//         $doctor_degree = implode(",", $degree_name);
//         $deptInfo = $this->db->query("select * from department where department_id='".$doctor_list->department_id."'")->row();
//         $data['doctors'][$j]['doctor_id'] = $doctor_val->doctor_id;
//         $data['doctors'][$j]['specialization'] =$doctor_list->speciality ;
//         $data['doctors'][$j]['department_name'] =$deptInfo->department_name;
//         $data['doctors'][$j]['name'] = $doctor_list->first_name." ".$doctor_list->last_name;
//         $data['doctors'][$j]['experience'] = $doctor_list->experience." Years";
//         $data['doctors'][$j]['about_doctor'] = $doctor_list->about ;
//         $data['doctors'][$j]['contact'] = $doctor_list->mobile ;
//         $data['doctors'][$j]['google_review_link'] = $doctor_list->google_review_link ;
//         $data['doctors'][$j]['gender'] = strtoupper($doctor_list->gender) ;
//         $data['doctors'][$j]['visit_time'] = "" ;
//         $data['doctors'][$j]['known_languages'] = $doctor_list->languages ;
//         $data['doctors'][$j]['dealt_deseases'] = $doctor_list->diseases_dealt;
//         $data['doctors'][$j]['degrees'] = $doctor_degree;
//         $data['doctors'][$j]['qualification'] = $doctor_list->qualification;
//         $data['doctors'][$j]['clinics'] = $clinics;

//         $achievements = $doctor_list->acheivements;
//         $arra_ach = explode(",", $achievements);

//         if($achievements == "" || $achievements == "null"){
//             $data['doctors'][$j]['achievements'] = array();
//         }else{
//             $data['doctors'][$j]['achievements'] = $arra_ach;

//         }

//         $data['doctors'][$j]['membership'] = $doctor_list->membership_in;
//         $data['doctors'][$j]['address'] = $doctor_list->address;
//         $data['doctors'][$j]['doctor_image'] =  base_url("uploads/doctors/".$doctor_list->profile_image."");
//         $j++;
//     }
//     $this->response(array('code' => '200', 'message' => 'Docotor Profile', 'result' => $data, 'requestname' => $method));
// }


public function patientdoctor_profile($parameters, $method, $user_id){
    $patient_id = $parameters['patient_id'];
    $presentDay = date("N");
    $present_time = date("H:i:s");
    $doctor_appointment_list = $this->db->query("select dp.* from doctor_patient dp, doctors d where d.doctor_id=dp.doctor_id and dp.patient_id='".$patient_id."' group by dp.doctor_id")->result();
    // echo $this->db->last_query();
    // $doctor_appointment_list = $this->db->query("select * from doctors")->result();
       
    if(count($doctor_appointment_list) > 0){
         $i = 0;
        foreach($doctor_appointment_list as $doc){         
            $docInfo = $this->db->query("SELECT cd.doctor_id,cd.consulting_fee,cd.online_consulting_fee,cd.clinic_id from clinic_doctor cd,clinic_doctor_weekdays cwd, clinic_doctor_weekday_slots cwds where cd.clinic_doctor_id=cwd.clinic_doctor_id and cwd.clinic_doctor_weekday_id=cwds.clinic_doctor_weekday_id and cwd.weekday='".$presentDay."' and cd.doctor_id='".$doc->doctor_id."' and '".$present_time."' > cwds.from_time and '".$present_time."' < cwds.to_time group by cd.doctor_id order by cwds.from_time ASC")->row();
            // echo $this->db->last_query();

            $clinicsData = getDocClinicDetails($doc->doctor_id);
            $docDetails = doctorDetails($doc->doctor_id);
            $clinicsInfo = $this->db->query("select c.clinic_id,c.clinic_name,CONCAT(c.address,' ',c.location) as clinic_address,c.state_id,c.district_id,c.clinic_phone,c.email,c.pincode from clinics c,clinic_doctor cd where c.clinic_id=cd.clinic_id and cd.doctor_id='".$doc->doctor_id."'")->result();
            if(count($docInfo) > 0){
                // echo "srinu";
                    $para['docList'][$i]['doctor_id'] = $docInfo->doctor_id;
                    $para['docList'][$i]['doctor_name'] = getDoctorName($docDetails->doctor_id);
                    $para['docList'][$i]['experience'] = $docDetails->experience;
                    $para['docList'][$i]['languages'] = ucwords(implode(", ", explode(",", $docDetails->languages)));
                    $para['docList'][$i]['consultation_fee'] = $docInfo->consulting_fee;
                    $para['docList'][$i]['online_consulting_fee'] = $docInfo->online_consulting_fee;
                    $para['docList'][$i]['qualification'] = $docDetails->qualification;
                    $para['docList'][$i]['department_name'] = $docDetails->department_name;
                    $para['docList'][$i]['profile_pic'] = base_url('uploads/doctors/'.$docDetails->profile_image);
                    $para['docList'][$i]['availability'] = "Available";
                    $clinics = array();
                    $k = 0;
                    foreach($clinicsInfo as $clinicValue){
                        $stateInfo = $this->db->query("select * from states where state_id='".$clinicValue->state_id."'")->row();
                        $districtInfo = $this->db->query("select * from districts where district_id='".$clinicValue->district_id."'")->row();
                        $clinics[$k]['clinic_id'] = $clinicValue->clinic_id;
                        $clinics[$k]['clinic_name'] = $clinicValue->clinic_name;
                        $clinics[$k]['clinic_address'] = $clinicValue->clinic_address.", ".$districtInfo->district_name.", ".$stateInfo->state_name." - ".$clinicValue->pincode;
                        $clinics[$k]['clinic_phone'] = $clinicValue->clinic_phone;
                        $clinics[$k]['email'] = $clinicValue->email;
                        $k++;
                    }
                    $para['docList'][$i]['clinics'] = $clinics;
            }
            else{
                $docQ = $this->db->query("SELECT doctor_id,consulting_fee,online_consulting_fee,clinic_id from clinic_doctor where doctor_id='".$doc->doctor_id."' group by doctor_id ")->row();
                //  echo $this->db->last_query();
                if(count($docQ) > 0){
                    // echo "naveen";
                    $para['docList'][$i]['doctor_id'] = $docQ->doctor_id;
                    $para['docList'][$i]['doctor_name'] = getDoctorName($docQ->doctor_id);
                    $para['docList'][$i]['experience'] = $docDetails->experience;
                    $para['docList'][$i]['languages'] = ucwords(implode(", ", explode(",", $docDetails->languages)));
                    $para['docList'][$i]['consultation_fee'] = $docQ->consulting_fee;
                    $para['docList'][$i]['online_consulting_fee'] = $docQ->online_consulting_fee;
                    $para['docList'][$i]['qualification'] = $docDetails->qualification;
                    $para['docList'][$i]['department_name'] = $docDetails->department_name;
                    $para['docList'][$i]['profile_pic'] = base_url('uploads/doctors/'.$docDetails->profile_image);
                    $para['docList'][$i]['availability'] = "Not Available";
                    $clinics = array();
                    $k = 0;
                    foreach($clinicsInfo as $clinicValue){
                        $stateInfo = $this->db->query("select * from states where state_id='".$clinicValue->state_id."'")->row();
                        $districtInfo = $this->db->query("select * from districts where district_id='".$clinicValue->district_id."'")->row();
                        $clinics[$k]['clinic_id'] = $clinicValue->clinic_id;
                        $clinics[$k]['clinic_name'] = $clinicValue->clinic_name;
                        $clinics[$k]['clinic_address'] = $clinicValue->clinic_address.", ".$districtInfo->district_name.", ".$stateInfo->state_name." - ".$clinicValue->pincode;
                        $clinics[$k]['clinic_phone'] = $clinicValue->clinic_phone;
                        $clinics[$k]['email'] = $clinicValue->email;
                        $k++;
                    }
                    $para['docList'][$i]['clinics'] = $clinics;
                }
                
            }
            $paras['docList'][]['doctor_name'] = getDoctorName($docDetails->doctor_id);
            $i++;    
            // $doctor_list = $this->db->query("select * from doctors where doctor_id ='".$doctor_val->doctor_id."'")->row();
            // $doctor_degrees_list = $this->db->query("select * from doctor_degrees where doctor_id ='".$doctor_val->doctor_id."'")->result();
            // $clinicsInfo = $this->db->query("select c.clinic_id,c.clinic_name,CONCAT(c.address,' ',c.location) as clinic_address,c.state_id,c.district_id,c.clinic_phone,c.email,c.pincode from clinics c,clinic_doctor cd where c.clinic_id=cd.clinic_id and cd.doctor_id='".$doctor_val->doctor_id."'")->result();
            // // echo $this->db->last_query();
            // $clinics = array();
            // $k = 0;
            // foreach($clinicsInfo as $clinicValue){
            //     $stateInfo = $this->db->query("select * from states where state_id='".$clinicValue->state_id."'")->row();
            //     $districtInfo = $this->db->query("select * from districts where district_id='".$clinicValue->district_id."'")->row();
            //     $clinics[$k]['clinic_id'] = $clinicValue->clinic_id;
            //     $clinics[$k]['clinic_name'] = $clinicValue->clinic_name;
            //     $clinics[$k]['clinic_address'] = $clinicValue->clinic_address.", ".$districtInfo->district_name.", ".$stateInfo->state_name." - ".$clinicValue->pincode;
            //     $clinics[$k]['clinic_phone'] = $clinicValue->clinic_phone;
            //     $clinics[$k]['email'] = $clinicValue->email;
            //     $k++;
            // }
            // $degree_name = array();
            // foreach($doctor_degrees_list as $degree_val){
            //     $degree_name[] = $degree_val->degree_name;
            // }
            // $doctor_degree = implode(",", $degree_name);
            // $deptInfo = $this->db->query("select * from department where department_id='".$doctor_list->department_id."'")->row();
            // $data['doctors'][$j]['doctor_id'] = $doctor_val->doctor_id;
            // $data['doctors'][$j]['specialization'] =$doctor_list->speciality ;
            // $data['doctors'][$j]['department_name'] =$deptInfo->department_name;
            // $data['doctors'][$j]['name'] = $doctor_list->first_name." ".$doctor_list->last_name;
            // $data['doctors'][$j]['experience'] = $doctor_list->experience." Years";
            // $data['doctors'][$j]['about_doctor'] = $doctor_list->about ;
            // $data['doctors'][$j]['contact'] = $doctor_list->mobile ;
            // $data['doctors'][$j]['google_review_link'] = $doctor_list->google_review_link ;
            // $data['doctors'][$j]['gender'] = strtoupper($doctor_list->gender) ;
            // $data['doctors'][$j]['visit_time'] = "" ;
            // $data['doctors'][$j]['known_languages'] = $doctor_list->languages ;
            // $data['doctors'][$j]['dealt_deseases'] = $doctor_list->diseases_dealt;
            // $data['doctors'][$j]['degrees'] = $doctor_degree;
            // $data['doctors'][$j]['qualification'] = $doctor_list->qualification;
            // $data['doctors'][$j]['clinics'] = $clinics;
    
            // $achievements = $doctor_list->acheivements;
            // $arra_ach = explode(",", $achievements);
    
            // if($achievements == "" || $achievements == "null"){
            //     $data['doctors'][$j]['achievements'] = array();
            // }else{
            //     $data['doctors'][$j]['achievements'] = $arra_ach;
    
            // }
    
            // $data['doctors'][$j]['membership'] = $doctor_list->membership_in;
            // $data['doctors'][$j]['address'] = $doctor_list->address;
            // $data['doctors'][$j]['doctor_image'] =  base_url("uploads/doctors/".$doctor_list->profile_image."");
            // $j++;
        }
        $this->response(array('code' => '200', 'message' => 'Docotor Profile', 'result' => $para, 'requestname' => $method));
    }
    else{
        $para['docList'] = [];
        $this->response(array('code'=>'201','message'=>'Error Occured','result'=>$para,'requestname'=>$method));
    }
    
}

    	 //list of health education articles in patient module 
public function health_education_articles($parameters, $method, $user_id) {

    $patient_id = $parameters['patient_id'];
    $doctor_appointment_list = $this->db->query("select doctor_id from clinic_doctor_patient where patient_id ='".$patient_id."' group by doctor_id")->result();
    // echo $this->db->last_query();
    // exit;
        $i=0;
    if(count($doctor_appointment_list)>0)
    {
        foreach($doctor_appointment_list as $doctor_val){        
            $doctor_list = $this->db->query("select d.*,dp.department_name from doctors d,department dp where dp.department_id=d.department_id and d.doctor_id ='".$doctor_val->doctor_id."'")->row();
            //  $para['department'][$i]['department_id'] = $doctor_list->department_id; 
            //  $para['department'][$i]['first_name'] = $doctor_list->first_name; 
            //  $para['department'][$i]['last_name'] = $doctor_list->last_name; 
            //  $para['department'][$i]['salutation'] = $doctor_list->salutation; 
            //  $para['department'][$i]['profile_image'] = $doctor_list->profile_image; 
            //  $para['department'][$i]['work_place_location'] = $doctor_list->work_place_location; 
            $article_info = $this->db->query("SELECT * from articles INNER JOIN article_department ON articles.article_id=article_department.article_id where (article_department.department_id='".$doctor_list->department_id."' or article_department.department_id='0') and articles.article_status='published' and (articles.citizens='1' or articles.citizens='2') order by articles.article_id DESC")->result();
            //  echo $this->db->last_query();
            //  $articles = $this->db->query("select * from articles a,article_department ad where a.article_id=ad.article_id and a.posted_by='".$id."' group by a.article_id")->result();
            $j = 0;
            foreach ($article_info as $value) 
            {
                $para['articles'][$j]['department_name'] = $doctor_list->department_name;
                $para['articles'][$j]['fullname'] = getDoctorName($value->posted_by); 
                $para['articles'][$j]['profile_image'] = base_url("uploads/doctors/".$doctor_list->profile_image);
                $para['articles'][$j]['work_place_location'] =$doctor_list->work_place_location; 
                $para['articles'][$j]['article_title'] = $value->article_title;
                $para['articles'][$j]['article_id'] = $value->article_id;
                $para['articles'][$j]['posted_by'] = $value->posted_by;
                $para['articles'][$j]['posted_time'] = date('d-m-Y h:i A', strtotime($value->created_date_time));
                $para['articles'][$j]['description'] = $value->short_description;
                $para['articles'][$j]['article_status'] = $value->article_status;
                $para['articles'][$j]['type'] = $value->article_type;
                if($value->article_status == "waiting")
                {
                    $para['articles'][$j]['posted_url'] = base_url('uploads/article_videos/'.$value->posted_url);
                }
                if(strtolower($value->article_type) == "video")
                {
                    $para['articles'][$j]['image_url'] = $value->video_image;
                    $para['articles'][$j]['video'] = $value->video_url;
                }
                elseif(strtolower($value->article_type) == "image")
                {
                    $images = explode(",", $value->posted_url);
                    foreach($images as $value)
                    {
                        $para['articles'][$j]['article_image'][] = base_url('uploads/article_images/'.$value);
                    }                    
                }
                elseif(strtolower($value->article_type) == "pdf")
                {
                    $files = explode(",", $value->posted_url);
                    foreach($files as $value)
                    {
                        $para['articles'][$j]['pdf'] = base_url('uploads/article_pdf/'.$value);
                    }
                }
                $j++;
                }
            $i++;
        // } 

        }
    }
    else
    {
        $article_info = $this->db->query("SELECT * from articles INNER JOIN article_department ON articles.article_id=article_department.article_id where article_department.department_id='0' and articles.article_status='published' and (articles.citizens='1' or articles.citizens='2') order by articles.article_id DESC")->result();
        //  echo $this->db->last_query();
        //  $articles = $this->db->query("select * from articles a,article_department ad where a.article_id=ad.article_id and a.posted_by='".$id."' group by a.article_id")->result();
        $j = 0;
        foreach ($article_info as $value) 
        {
            $para['articles'][$j]['department_name'] = $doctor_list->department_name;
            $para['articles'][$j]['fullname'] = "Dr. ".$doctor_list->first_name." ".$doctor_list->last_name; 
            $para['articles'][$j]['profile_image'] = base_url("uploads/doctors/".$doctor_list->profile_image);
            $para['articles'][$j]['work_place_location'] =$doctor_list->work_place_location; 
            $para['articles'][$j]['article_title'] = $value->article_title;
            $para['articles'][$j]['article_id'] = $value->article_id;
            $para['articles'][$j]['posted_by'] = $value->posted_by;
            $para['articles'][$j]['description'] = $value->short_description;
            $para['articles'][$j]['article_status'] = $value->article_status;
            $para['articles'][$j]['type'] = $value->article_type;
            if($value->article_status == "waiting")
            {
                $para['articles'][$j]['posted_url'] = base_url('uploads/article_videos/'.$value->posted_url);
            }
            if(strtolower($value->article_type) == "video")
            {
                $para['articles'][$j]['image_url'] = $value->video_image;
                $para['articles'][$j]['video'] = $value->video_url;
            }
            elseif(strtolower($value->article_type) == "image")
            {
                $images = explode(",", $value->posted_url);
                foreach($images as $value)
                {
                    $para['articles'][$j]['article_image'][] = base_url('uploads/article_images/'.$value);
                }                    
            }
            elseif(strtolower($value->article_type) == "pdf")
            {
                $files = explode(",", $value->posted_url);
                foreach($files as $value)
                {
                    $para['articles'][$j]['pdf'] = base_url('uploads/article_pdf/'.$value);
                }
            }
            $j++;
            }
    }
    
 

 
    $this->response(array('code' => '200', 'message' => 'Health Education Articles', 'result' => $para, 'requestname' => $method));
}
// }

// Articles Search
public function articles_search($parameters, $method, $user_id) {

    $patient_id = $parameters['patient_id'];
    $search = $parameters['search'];
    $doctor_appointment_list = $this->db->query("select doctor_id from clinic_doctor_patient where patient_id ='".$patient_id."' group by doctor_id")->result();
    // echo $this->db->last_query();
    // exit;
    $i=0;
    foreach($doctor_appointment_list as $doctor_val){        
         $doctor_list = $this->db->query("select d.*,dp.department_name from doctors d,department dp where dp.department_id=d.department_id and d.doctor_id ='".$doctor_val->doctor_id."'")->row();
        //  $para['department'][$i]['department_id'] = $doctor_list->department_id; 
        //  $para['department'][$i]['first_name'] = $doctor_list->first_name; 
        //  $para['department'][$i]['last_name'] = $doctor_list->last_name; 
        //  $para['department'][$i]['salutation'] = $doctor_list->salutation; 
        //  $para['department'][$i]['profile_image'] = $doctor_list->profile_image; 
        //  $para['department'][$i]['work_place_location'] = $doctor_list->work_place_location; 
         $article_info = $this->db->query("SELECT * from articles INNER JOIN article_department ON articles.article_id=article_department.article_id where (article_department.department_id='".$doctor_list->department_id."' or article_department.department_id='0') and articles.article_status='published' and articles.article_title LIKE '%".urldecode($search)."%' and (articles.citizens='1' or articles.citizens='2')")->result();
        //  echo $this->db->last_query();
        //  $articles = $this->db->query("select * from articles a,article_department ad where a.article_id=ad.article_id and a.posted_by='".$id."' group by a.article_id")->result();
         $j = 0;
         foreach ($article_info as $value) 
         {
             $para['articles'][$j]['department_name'] = $doctor_list->department_name;
             $para['articles'][$j]['fullname'] = "Dr. ".$doctor_list->first_name." ".$doctor_list->last_name; 
             $para['articles'][$j]['profile_image'] = base_url("uploads/doctors/".$doctor_list->profile_image);
             $para['articles'][$j]['work_place_location'] =$doctor_list->work_place_location; 
             $para['articles'][$j]['article_title'] = $value->article_title;
             $para['articles'][$j]['article_id'] = $value->article_id;
             $para['articles'][$j]['posted_by'] = $value->posted_by;
             $para['articles'][$j]['description'] = $value->short_description;
             $para['articles'][$j]['article_status'] = $value->article_status;
             $para['articles'][$j]['type'] = $value->article_type;
             if($value->article_status == "waiting")
             {
                 $para['articles'][$j]['posted_url'] = base_url('uploads/article_videos/'.$value->posted_url);
             }
             if(strtolower($value->article_type) == "video")
             {
                 $para['articles'][$j]['image_url'] = $value->video_image;
                 $para['articles'][$j]['video'] = $value->video_url;
             }
             elseif(strtolower($value->article_type) == "image")
             {
                 $images = explode(",", $value->posted_url);
                 foreach($images as $value)
                 {
                     $para['articles'][$j]['article_image'][] = base_url('uploads/article_images/'.$value);
                 }                    
             }
             elseif(strtolower($value->article_type) == "pdf")
             {
                 $files = explode(",", $value->posted_url);
                 foreach($files as $value)
                 {
                     $para['articles'][$j]['pdf'] = base_url('uploads/article_pdf/'.$value);
                 }
             }
             $j++;
            }
         $i++;
    // } 

    }
 

 
    $this->response(array('code' => '200', 'message' => 'Health Education Articles', 'result' => $para, 'requestname' => $method));
}

    //inserting comments for health education articles
public function health_education_comments($parameters, $method, $user_id) {
    $com['patient_id'] = $user_id;
    $com['article_id'] = $parameters['article_id'];
    $com['comments'] = $parameters['comment'];
    $com['created_by'] = $user_id;
    $com['created_date_time'] = date("Y-m-d H:i:s");
    $this->Generic_model->insertData('health_education_comments', $com);
    $this->response(array('code' => '200', 'message' => 'Comment Inserted Successfully', 'requestname' => $method));
}

 // this function 
    public function get_schedule($parameters, $method, $user_id) {

        $clinic_id = $parameters['clinic_id'];
        $doctor_id = $parameters['doctor_id'];
        $week_day = date('N', strtotime($date));

        // Today's date
        $date = date('Y-m-d'); 
        // echo $date;
        
        $weekOfdays = array();
        
        for ($i = 1; $i <= 7; $i++) {
            $date = date('Y-m-d', strtotime('+1 day', strtotime($date)));
            $weekOfdays[] = date('Y-m-d', strtotime($date));
        }
        
        $k = 0;

        foreach ($weekOfdays as $key => $value) {
            $date = date('Y-m-d', strtotime('-1 day', strtotime($value)));
            $wday = date('N', strtotime($date));
            $param['booked_slots'][$k]['date'] = $date;
            $bs = $this->db->select("*")->from("appointments")->where("clinic_id='".$clinic_id."' and doctor_id='".$doctor_id."' and appointment_date='".$date."' and slot_type='walkin'")->get()->result();
            // echo $this->db->last_query();
            // echo $bs;
            // echo count($bs);
            if (count($bs) > 0) {
                foreach ($bs as $bss) {
                    $b_slots[] = date('h:i A', strtotime($bss->appointment_time_slot));
                }
                $param['booked_slots'][$k]['time_slot'] = $b_slots;
            } else {
                $param['booked_slots'][$k]['time_slot'] = NULL;
            }
            $k++;
            $blocking = $this->db->select("*")->from("calendar_blocking")->where("clinic_id='" . $clinic_id . "' and doctor_id='" . $doctor_id . "'")->get()->row();

            $cal_dates = explode(",", $blocking->dates);
            $blocked_dates = array();
            foreach ($cal_dates as $key => $final) {

                if (in_array($final, $weekOfdays)) {
                    $blocked_dates[] = $final;
                }
            }
            $blocked_list = implode(',', $blocked_dates);
            $param['blocked_dates'] = $blocked_list;
        }

        // check if the param patient id exists
        if(isset($parameters['patient_id'])){
            $patient_id = $parameters['patient_id'];

            // get the list of open appointments i.e whose status is 'booked'
            $this->db->select('appointment_id, patient_id, doctor_id, appointment_date, appointment_time_slot');
            $this->db->from('appointments');
            $this->db->where('slot_type=','walkin');
            $this->db->where('status=','booked');
            $this->db->where('doctor_id=',$doctor_id);
            $this->db->where('patient_id=',$patient_id);
            $booked_list = $this->db->get()->result();

            if(count($booked_list) > 0){
                $x = 0;    
                foreach($booked_list as $booked){
                    $param['booked_appointments'][$x] = $booked;
                    $x++;
                }
            }else{
                $param['booked_appointments'] = $booked_list;
            }           
        }

        $this->response(array('code' => '200', 'message' => 'Doctor Time Slots', 'result' => $param, 'requestname' => $method));
    }


    //getting patient feedback status
public function feedback_status($parameters, $method, $user_id) {

   $get_last_appointment = $this->db->query("select appointment_id,d.first_name,d.last_name,d.doctor_id from appointments a inner join doctors d on(a.doctor_id = d.doctor_id) where a.patient_id = '".$parameters['patient_id']."' and (a.status!='drop' or a.status='booked')  order by a.appointment_date DESC ")->row();
   $get_count = $this->db->query("select * from appointments where patient_id='".$parameters['patient_id']."' and doctor_id='".$get_last_appointment->doctor_id."'")->num_rows();

   $get_app_status = $this->db->query("select appreciation_status from patient_doctor_appreciation where appreciation_status='1'")->num_rows();
   $get_fb_status = $this->db->query("select feedback_status from patient_doctor_feedback where feedback_status='1'")->num_rows();
   if($get_app_status > 0 || $get_fb_status >0){
    $status = 1;
}
else{
    $status = 0;
}
$get_skip_status = $get_fb_status = $this->db->query("select skip_status from patient_doctor_feedback where appointment_id='".$get_last_appointment->appointment_id."' and skip_status='1'")->num_rows();
if($get_skip_status > 0){
    $skip_status = 1;
}
else{
    $skip_status = 0;
}
$data['status'] = $status;
$data['skip_staus'] = $skip_status;
$data['count'] = $get_count;
$data['appointmet_id'] = $get_last_appointment->appointment_id;
$data['doctor_id'] = $get_last_appointment->doctor_id;
$data['doctor_name'] = $get_last_appointment->first_name." ".$get_last_appointment->last_name;
$this->response(array('code' => '200', 'message' => 'Feedback Status', 'result' => $data, 'requestname' => $method));
}


    //inserting reply to comments 
public function health_education_comment_reply($parameters, $method, $user_id) {

    $com['comment_id'] = $parameters['comment_id'];
    $com['reply'] = $parameters['reply'];
    $com['created_date_time'] = date("Y-m-d H:i:s");
    $this->Generic_model->insertData('health_education_comment_reply', $com);
    $this->response(array('code' => '200', 'message' => 'Reply Inserted Successfully', 'requestname' => $method));
}



// Below function will retrieve feedbacks regarding the doctor from all the patients
public function doctor_feedback_list($parameters, $method, $user_id){

    $doctor_id = $parameters['doctor_id'];

    $feedbackList = $this->db->select('doctor_id,description, created_date_time')->from('patient_doctor_appreciation')->where('doctor_id =',$doctor_id)->get()->result_array();

    // echo $this->db->last_query();
    // exit();

    $doctors_feedback['feedbackList'] = $feedbackList;

    if(count($feedbackList) > 0){
        $this->response(array('code' => '200', 'message' => 'Feedback list', 'result' => $doctors_feedback, 'requestname' => $method));
    }else{
        $this->response(array('code' => '201', 'message' => 'No Feedbacks Found', 'result' => NULL, 'requestname' => $method));
    }

}

    //How to use umdaa
    public function getTutorialLinks($parameters, $method, $user_id){
        $tutorialLinks = $this->db->select("*")->from("umdaa_tutorials")->where("tutorial_type='citizen'")->get()->result();
        if(sizeof($tutorialLinks)>0)
        {
            $i = 0;
            foreach ($tutorialLinks as $value) {
                $data['tutorial'][$i]['tutorial_id'] = $value->umdaa_tutorial_id;
                $data['tutorial'][$i]['tutorial_name'] = $value->tutorial_name;
                $data['tutorial'][$i]['tutorial_link'] = $value->tutorial_link;
                $data['tutorial'][$i]['video_thumbnail'] = base_url()."uploads/thumbnails/".$value->video_thumbnail;
                $i++;
            }
            $this->response(array('code' => '200', 'message' => 'Tutorial Videos', 'result' => $data, 'requestname' => $method));
        }
        else
        {
            $this->response(array('code' => '201', 'message' => 'No Videos Found', 'requestname' => $method));
        }   
    }

    public function remove($parameters, $method, $user_id)
    {
        echo $path = base_url().'uploads/billings/2002151.pdf';
        shell_exec('rm '.$path);
        $this->response(array('code' => '201', 'message' => 'No Videos Found', 'requestname' => $method));
    }

//Patient Transactions
public function patientTransactions($parameters, $method, $user_id)
{
    $patient_id = $parameters['patient_id'];

    $patient_billings = $this->Generic_model->getAllRecords('billing',array('patient_id'=>$patient_id),array('field'=>'billing_id','type'=>'desc'));
    
    $b=0;
    foreach($patient_billings as $key => $billing)
    {
            $amount = 0;
            $data['transaction'][$b]['billing_id'] = $billing->billing_id;
            $data['transaction'][$b]['receipt_no'] = $billing->receipt_no;
            $data['transaction'][$b]['invoice_no'] = $billing->invoice_no;
            $data['transaction'][$b]['billing_type'] = $billing->billing_type;
            $data['transaction'][$b]['appointment_id'] = $billing->appointment_id;
            $data['transaction'][$b]['date'] = date('d-M-Y',strtotime($billing->created_date_time));

            $data['transaction'][$b]['pdf'] = base_url().'uploads/billings/'.$billing->invoice_pdf;
            $billing_line_items = $this->Generic_model->getAllRecords('billing_line_items',array('billing_id'=>$billing->billing_id),'');
            $bl=0;

            foreach($billing_line_items as $billing1)
            {
                if($billing1->discount_unit == 'INR' || $billing1->discount_unit == NULL || $billing1->discount_unit=='')
                {
                   $amount = $amount+(($billing1->amount)-($billing1->discount));
               }else if($billing1->discount_unit=='%')
               {
                $dis_amount = ($billing1->amount)-(($billing1->amount*$billing1->discount)/100);
                $amount = $amount+$dis_amount;
            }else{
               $amount = $amount+(($billing1->amount)-($billing1->discount));
           }
           $bl++;
       }
       $data['transaction'][$b]['amount'] = $amount;

       $b++;
    }
    $this->response(array('code' => '200', 'message' => 'Patients Transactions', 'result' => $data, 'requestname' => $method));
}

// Patient Transaction PDF
public function getTransactionPDF($parameters, $method, $user_id)
{
    $billing_id = $parameters['billing_id'];
    if(!empty($billing_id))
    {
        $data['billing'] = $this->db->query("select * from billing where billing_id='".$billing_id."'")->row(); 
        $data['billing_line_items'] = $this->db->query("select * from billing_line_items where billing_id='".$billing_id."'")->result(); 
        $data['patient'] = $this->db->query("select * from patients p,appointments a where a.patient_id=p.patient_id and a.appointment_id='".$data['billing']->appointment_id."'")->row();
        $data['doctors'] = $this->db->query("select * from doctors d,appointments a where a.doctor_id=d.doctor_id and a.appointment_id='".$data['billing']->appointment_id."'")->row();
        $data['department'] = $this->db->query("select * from department where department_id='".$data['doctors']->doctor_id."'")->row();
        $data['clinics'] = $this->db->query("select * from clinics c,appointments a where a.clinic_id=c.clinic_id and a.appointment_id='".$data['billing']->appointment_id."'")->row();
        $this->load->library('M_pdf');
		$html = $this->load->view('patients/PatientInvoice',$data,true);
		$pdfFilePath = $data['billing']->invoice_no.".pdf";
        $stylesheet  = '';
        $stylesheet .= file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
        $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");
        $this->m_pdf->pdf->autoScriptToLang = true;
        $this->m_pdf->pdf->autoLangToFont = true;

        $this->m_pdf->pdf->shrink_tables_to_fit = 1;
        $this->m_pdf->pdf->setAutoTopMargin = "stretch";
        $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
        $this->m_pdf->pdf->defaultheaderline = 0;


        $this->m_pdf->pdf->WriteHTML($stylesheet,1);
        $this->m_pdf->pdf->WriteHTML($html,2);
        $this->m_pdf->pdf->Output("./uploads/billings/".$pdfFilePath, "F"); 
        $para['path'] = base_url('uploads/billings/'.$pdfFilePath);
        $this->response(array('code'=>'200','message'=>'Traansaction Document','result'=>$para,'requestname'=>$method));
    }
    else
    {
        $this->response(array('code' => '201', 'message' => 'Invalid Request', 'requestname' => $method));
    }
}

//Patient Transactions
// public function patientTransactions($parameters, $method, $user_id)
// {
//     $patient_id = $parameters['patient_id'];
//     $patientInfo = $this->db->select("mobile,alternate_mobile")->from("patients")->where("patient_id='".$patient_id."'")->get()->row();
//     if($patientInfo->mobile!="")
//     {
//         $cond = "mobile='".$patientInfo->mobile."' or alternate_mobile='".$patientInfo->mobile."'";
//     }
//     elseif($patientInfo->alternate_mobile!="")
//     {
//         $cond = "mobile='".$patientInfo->alternate_mobile."' or alternate_mobile='".$patientInfo->alternate_mobile."'";
//     }
//     $alt_patients = $this->db->select("patient_id")->from("patients")->where($cond)->get()->result();
//     $i=0;
//     foreach($alt_patients as $value)
//     {
//         $str .= $value->patient_id.",";
//     }
//     $str =substr($str,0,-1);
//     // $patient_billings = $this->Generic_model->getAllRecords('billing',array('patient_id'=>$value->patient_id),array('field'=>'billing_id','type'=>'desc'));
//     $patient_billings = $this->db->query("select * from billing where patient_id IN (".$str.") order by billing_id DESC")->result();
//     // echo $this->db->last_query();
//     $b=0;
//     foreach($patient_billings as $key => $billing)
//     {
//             $amount = 0;
//             $data['transaction'][$b]['biling_id'] = $billing->billing_id;
//             $data['transaction'][$b]['appointment_id'] = $billing->appointment_id;
//             $data['transaction'][$b]['receipt_no'] = $billing->receipt_no;
//             $data['transaction'][$b]['invoice_no'] = $billing->invoice_no;
//             $data['transaction'][$b]['billing_type'] = $billing->billing_type;
//             $data['transaction'][$b]['date'] = date('d-M-Y',strtotime($billing->created_date_time));
//             $data['transaction'][$b]['pdf'] = base_url().'uploads/billings/'.$billing->invoice_pdf;
//             $billing_line_items = $this->Generic_model->getAllRecords('billing_line_items',array('billing_id'=>$billing->billing_id),'');
//             $bl=0;

//             foreach($billing_line_items as $billing1)
//             {
//                 if($billing1->discount_unit == 'INR' || $billing1->discount_unit == NULL || $billing1->discount_unit=='')
//                 {
//                    $amount = $amount+(($billing1->amount)-($billing1->discount));
//                }else if($billing1->discount_unit=='%')
//                {
//                 $dis_amount = ($billing1->amount)-(($billing1->amount*$billing1->discount)/100);
//                 $amount = $amount+$dis_amount;
//             }else{
//                $amount = $amount+(($billing1->amount)-($billing1->discount));
//            }
//            $bl++;
//        }
//        $b++;
//     }

    
//        $data['transaction'][$b]['amount'] = $amount;
//            $i++;
//     $this->response(array('code' => '200', 'message' => 'Patients Transactions', 'result' => $data, 'requestname' => $method));

//     }
// }

    // public function getPatientTransactionView($parameters, $method, $user_id){
    //     $app_id = $parameters['appointment_id'];
    //     $billing_id123 = $parameters['biling_id'];
        
    //     $data1['url'] = base_url("ApiWebView/myTransaction/".$app_id."/".$billing_id123);
    //     $this->response(array('code' => '200', 'message' => 'PDF File', 'result' => $data1['url'], 'requestname' => $method));

    // }

    public function getPatientTransactionView($parameters, $method, $user_id){
        // print_r($parameters);
        extract($parameters);

        $billing_date=$this->db->select('*')->from('billing A')->where('A.billing_id =', $billing_id)->get()->row();
        $data['billing_date']=$billing_date->billing_date_time;

        $info = $this->db->select('*')->from('appointments A')
        ->where('A.appointment_id =',$appointment_id)->get()->row();

           // Get patient Info
           $this->db->select('patient_id, umr_no, first_name, last_name, mobile, alternate_mobile');
           $this->db->from('patients');
           $this->db->where('patient_id =', $info->patient_id);
           $patientInfo = $this->db->get()->row();
   

        $clinic_details = $this->Generic_model->getSingleRecord('clinics',array('clinic_id'=>$info->clinic_id));

        $doctor_details = $this->Generic_model->getSingleRecord('doctors',array('doctor_id'=>$info->doctor_id));
        $review_details = $this->Generic_model->getSingleRecord('clinic_doctor',array('doctor_id'=>$info->doctor_id,'clinic_id'=>$info->clinic_id));

        $departments = $this->Generic_model->getSingleRecord('department',array('department_id'=>$doctor_details->department_id));
        // print_r($departments);
        $billing_master = $this->Generic_model->getSingleRecord('billing',array('billing_id'=>$billing_id));
        $billing = $this->Generic_model->getAllRecords('billing_line_items',array('billing_id'=>$billing_id));
        $patient_details = $this->Generic_model->getSingleRecord('patients',array('patient_id'=>$info->patient_id));     

        $district_details = $this->Generic_model->getSingleRecord('districts',array('district_id'=>$patient_details->district_id),$order='');

        $state_details = $this->Generic_model->getSingleRecord('states',array('state_id'=>$patient_details->state_id),$order='');

        $data['clinic_logo'] = $clinic_details->clinic_logo;
        $data['review_days']=$review_details->review_days;
        $data['clinic_phone'] = $clinic_details->clinic_phone;
        $data['clinic_name'] = $clinic_details->clinic_name;
        $data['address'] = $clinic_details->address;
        $data['doctor_name'] = "Dr." . strtoupper($doctor_details->first_name . " " . $doctor_details->last_name);
        $data['qualification'] = $doctor_details->qualification;
        $data['department_name'] = $departments->department_name;
        $data['patient_name'] = ucfirst($patient_details->title) . "." . strtoupper($patient_details->first_name . " " . $patient_details->last_name);
        $data['age'] = $patient_details->age . ' ' . $patient_details->age_unit;
        $data['age_unit']=$patient_details->age_unit;
        $data['gender'] = $patient_details->gender;
        $data['umr_no'] = $patientInfo->umr_no;
        $data['patient_address'] = $patient_details->address_line . "," . $district_details->district_name . "," . $state_details->state_name . "," . $patient_details->pincode;
        $data['billing'] = $billing;
        $data['invoice_no'] = $billing_date->invoice_no;
        $data['invoice_no_alias'] = $billing_date->invoice_no_alias;
        $data['payment_method'] = $billing_date->payment_mode;
        $data['transaction_id'] = $billing_date->transaction_id;
        $data['doctor_details'] = $doctor_details;

        $data['pdf_settings'] = $pdf_settings = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$clinic_details->clinic_id."'")->row();
        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        // exit;

        $html = $this->load->view('billing/generate_billing_citizen', $data,true);
        $pdfFilePath = "billing_" . $info->patient_id . time() . ".pdf";
        // chmod
        // if(file_exists(FCPATH.'uploads/billings/'.$pdfFilePath))
        // {
            // echo "exists";
        // }
        // $param['mod'] = chmod(FCPATH.'uploads/billings/'.$pdfFilePath, 0777);
        // $param['res'] = unlink(FCPATH.'uploads/billings/'.$pdfFilePath);
        
        $data['file_name'] = $pdfFilePath;

        $this->load->library('M_pdf');
        // $this->m_pdf->showImageErrors = true;$stylesheet  = '';
        $stylesheet .= file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
        $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");
        $this->m_pdf->pdf->autoScriptToLang = true;
        $this->m_pdf->pdf->autoLangToFont = true;

        $this->m_pdf->pdf->shrink_tables_to_fit = 1;
        $this->m_pdf->pdf->setAutoTopMargin = "stretch";
        $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
        $this->m_pdf->pdf->defaultheaderline = 0;


        $this->m_pdf->pdf->WriteHTML($stylesheet,1);
        $this->m_pdf->pdf->WriteHTML($html,2);
        $this->m_pdf->pdf->Output("./uploads/billings/" . $pdfFilePath, "F");
        $billFile['invoice_pdf'] = $data['file_name'];
        $this->Generic_model->updateData('billing', $billFile, array('billing_id' => $billing_id));
        $pdf = base_url() . 'uploads/billings/' . $pdfFilePath;
        $param['pdf_name'] = $pdf;

        $this->response(array('code' => '200', 'message' => 'PDF File', 'result' =>$param, 'requestname' => $method));
        // $this->response(array('code' => '200', 'message' => 'Appointment Booked', 'result' => $param, 'requestname' => $method));
    }


    public function healtRecordPDF($parameters, $method, $user_id){
        $appointment_id = $parameters['appointment_id'];
        $data['appointmentdetails']= $this->db->query("select * from appointments where appointment_id='".$appointment_id."'")->row();
    
        //HandWriting
        $section_image = $this->db->query("select patient_form_id from patient_form where appointment_id='".$appointment_id."'")->result();
    
        $i=0;
        foreach($section_image as $image)
        {
            $abc['investigation'][$i]['patient_form_id'] = $image->patient_form_id;
            $inv_lineitems = $this->db->select("*")->from("patient_form_scribbling_images pil")
            ->where("pil.patient_form_id='".$image->patient_form_id."'")
            ->get()
            ->result();
            $j=0;
            foreach($inv_lineitems as $inv_lineitem){
            $abcd['scribbling'][0]['images']= $inv_lineitem->scribbling_image;
            $j++;
            }
            $i++;
        }
        //End HandWriting 
    
            //$data['visit']=$visit;
        $data['appointments'] = $this->db->select("a.*, c.clinic_name,c.clinic_logo,c.address,c.clinic_phone,p.title,p.first_name as pname,p.last_name as plname,p.gender,p.age,p.address_line,p.umr_no,p.preferred_language,p.mobile,p.qrcode,p.allergy,d.salutation,d.first_name as dfname,d.last_name as dlname, d.qualification, d.registration_code, dep.department_name")
        ->from("appointments a")->join("clinics c","a.clinic_id = c.clinic_id","left")->join("patients p","a.patient_id = p.patient_id","left")->join("doctors d","a.doctor_id = d.doctor_id","left")->join('department dep','d.department_id = dep.department_id')->where("a.appointment_id='" . $appointment_id . "'")->get()->row();
        if($data['appointments']->doctor_id != 0)
        {
           $data['docInfo'] = $this->db->query("select d.*,de.department_name from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$data['appointments']->doctor_id."'")->row();
           $data['clinicDocInfo'] = $this->db->select("*")->from("clinic_doctor")->where("clinic_id",$clinic_id)->where("doctor_id",$data['appointments']->doctor_id)->get()->row();
        }
        $patient_name = $data['appointments']->pname.date('Ymd').$appointment_id;
    
        $visit_no = $this->Generic_model->getAllRecords('appointments',array('clinic_id'=>$data['appointments']->clinic_id,'patient_id'=>$data['appointments']->patient_id,'doctor_id'=>$data['appointments']->doctor_id),array('field'=>'appointment_id','type'=>'desc'));
    
    
        $visit_count = count($visit_no);
    
        foreach ($visit_no as $key => $value) {
            if ($value->appointment_id == $appointment_id) {
                $visit_count--;
                $data['visit'] = $visit_count;
            }
        }
    
        //$vital_sign =  $this->Generic_model->getAllRecords('patient_vital_sign',array('appointment_id'=>$appointment_id),array('field'=>'position','type'=>'asc'));
    
        $vital_sign = $this->db->query("SELECT patient_vital_id, appointment_id, clinic_id, patient_id, umr_no, vital_sign, vital_result, sign_type, position, vital_sign_recording_date_time  from patient_vital_sign WHERE vital_sign_recording_date_time IN (SELECT MAX(vital_sign_recording_date_time) AS date FROM patient_vital_sign WHERE appointment_id = ".$appointment_id.") ORDER BY position ASC , vital_sign_recording_date_time DESC")->result_object();
    
        foreach ($vital_sign as $key => $value) {
                if($value->vital_sign == 'SBP'){// Capture Systolic Blood Pressure Value
                    if($bp != ''){
                        $bp = $value->vital_result."/".$bp;
                    }else{
                        $bp = $value->vital_result;
                    }
                    
                    // Overwirte BP
                    $data['vital_sign']['BP'] = $bp.' mmHg';
    
                }else if($value->vital_sign == 'DBP'){// Capture Diastolic Blood Pressure Value
                    if($bp != ''){
                        $bp = $bp."/".$value->vital_result;    
                    }else{
                        $bp = $value->vital_result;    
                    }
    
                    // Overwirte BP
                    $data['vital_sign']['BP'] = $bp.' mmHg';
    
                }else{
                    $v_unit = $this->db->query("SELECT * FROM `vital_sign` where short_form='".$value->vital_sign."'")->row();
                    $data['vital_sign'][$value->vital_sign] = $value->vital_result." ".$v_unit->unit;   
                }
            }
            
            $pcd = $this->Generic_model->getSingleRecord('patient_clinical_diagnosis',array('appointment_id'=>$appointment_id),'');
            
            $data['pcd'] = $this->Generic_model->getSingleRecord('patient_clinical_diagnosis',array('appointment_id'=>$appointment_id),'');
    
            $data['patient_clinical_diagnosis'] = $this->Generic_model->getAllRecords("patient_cd_line_items", array('patient_clinical_diagnosis_id'=>$pcd->patient_clinical_diagnosis_id), $order = '');
    
            $data['patient_investigations'] = $this->db->query("select * from patient_investigation pi,patient_investigation_line_items pil where pil.patient_investigation_id=pi.patient_investigation_id and pi.appointment_id='".$appointment_id."'")->result();
    
            $data['patient_prescription'] =$patient_prescription= $this->Generic_model->getSingleRecord('patient_prescription',array('appointment_id'=>$appointment_id),'');
            $data['patient_prescription_drug'] =$this->db->query("select * from patient_prescription_drug where patient_prescription_id='".$patient_prescription->patient_prescription_id."'")->result();
    
            
            $parent_appointment = $this->Generic_model->getSingleRecord('appointments',array('appointment_id'=>$appointment_id),'');
            
            if($parent_appointment->parent_appointment_id==0)
            {
                $next_follow_up=$this->Generic_model->getSingleRecord('appointments',array('parent_appointment_id'=>$appointment_id),array('field'=>'appointment_id','type'=>'desc'));
    
                if($next_follow_up->appointment_id==$appointment_id)
                {
                    $data['follow_up_date'] = '';
                }else{
                    $data['follow_up_date'] = $next_follow_up->appointment_date;
                }
            }else{
                $next_follow_up=$this->Generic_model->getSingleRecord('appointments',array('parent_appointment_id'=>$parent_appointment->parent_appointment_id),array('field'=>'appointment_id','type'=>'desc'));
    
                if($next_follow_up->appointment_id==$appointment_id)
                {
                    $data['follow_up_date'] = '';
                }else{
                    $data['follow_up_date'] = $next_follow_up->appointment_date;
                }
            }
            $data['get_past_history_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='".$appointment_id."' and form_type='Past History'")->get()->result();
            $data['pastHistory'] = $this->db->query("select * from patient_form pf,patient_form_line_items pfl where pf.patient_form_id=pfl.patient_form_id and pf.appointment_id='".$id."' and pfl.section_text!='' and pf.form_type='Past History'")->result();
            $data['symptoms'] = $this->db->query("select * from patient_presenting_symptoms pps,patient_ps_line_items ppls where pps.patient_presenting_symptoms_id=ppls.patient_presenting_symptoms_id and pps.appointment_id='".$appointment_id."'")->result();
            $data['get_gpe_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='".$appointment_id."' and form_type='GPE'")->get()->result();
            $data['get_se_info'] = $this->db->query("select * from patient_form where appointment_id='".$appointment_id."' and form_type='Systemic Examination' order by patient_form_id DESC")->result();        
    
            $data['pdf_settings'] = $pdf_settings = $this->db->select("*")->from("clinic_pdf_settings")->where("clinic_id='".$data['appointments']->clinic_id."'")->get()->row();
            
            $getId = $this->db->select("*")->from("appointments")->where("appointment_id='".$appointment_id."'")->get()->row();
            $data['shortData'] = $this->db->query("select * from suggestions_list where patient_id='".   $getId ->patient_id."' and  doctor_id='".$getId->doctor_id."'  and form_type='Past History'")->result();
    
            $data['gpeShortData'] = $this->db->query("select * from suggestions_list where patient_id='".   $getId ->patient_id."' and  doctor_id='".$getId->doctor_id."'  and form_type='GPE'")->result();
    
            $data['seShortData'] = $this->db->query("select * from suggestions_list where patient_id='".   $getId ->patient_id."' and  doctor_id='".$getId->doctor_id."'  and form_type='SE'")->result();
            // echo $this->db->last_query();
    
            
            $this->load->library('M_pdf');
            $html = $this->load->view('reports/citizen_short_summary_reports_pdf', $data, true);
            // $pdfFilePath = strtolower(str_replace(" ", "_", $patient_name)) . ".pdf";
            $pdfFilePath = time().rand(111,999).".pdf";
            $stylesheet  = '';
        $stylesheet .= file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
        $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");
        $this->m_pdf->pdf->autoScriptToLang = true;
        $this->m_pdf->pdf->autoLangToFont = true;
    
        $this->m_pdf->pdf->shrink_tables_to_fit = 1;
        $this->m_pdf->pdf->setAutoTopMargin = "stretch";
        $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
        $this->m_pdf->pdf->defaultheaderline = 0;
    
        
        // $this->m_pdf->pdf->SetHTMLHeader('welcome');
        $this->m_pdf->pdf->WriteHTML($stylesheet,1);
        $this->m_pdf->pdf->WriteHTML($html,2);
        $fullpath = base_url('uploads/summary_reports/short-'.$pdfFilePath);
        // chmod($fullpath,0777);
        // unlink($fullpath);
        $this->m_pdf->pdf->Output("./uploads/summary_reports/short-".$pdfFilePath, "F");
        // $this->m_pdf->pdf->Output("./uploads/summary_reports/".$pdfFilePath, "D");
        $para['pdf_name'] = base_url() . 'uploads/summary_reports/short-'.$pdfFilePath;
    
        $this->response(array('code' => '200', 'message' => 'PDF File', 'result' => $para, 'requestname' => $method));

    }

    public function patientTransactions_pdf($parameters, $method, $user_id)
    {
        $patient_id = $parameters['patient_id'];
        $billing_id = $parameters['billing_id'];

        $data['patient_billings_line_items'] = $this->Generic_model->getAllRecords('billing_line_items',array('billing_id'=>$billing_id),'');

        $html = $this->load->view('patients/patient_transaction_pdf', $data, true);
        $pdfFilePath = $patient_id.$billing_id . ".pdf";
        $data['patient_form_pdf'] = $pdfFilePath;
        $this->load->library('M_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
                //download it.
        $this->m_pdf->pdf->Output("./uploads/patient_form/" . $pdfFilePath, "F");
        $data_1['patient_transaction_pdf'] = base_url("uploads/patient_form/" . $pdfFilePath . "");

        $this->response(array('code' => '200', 'message' => 'form_details', 'result' => $data_1, 'requestname' => $method));
    }

    /*
    Function providing complete patient details
    */
    public function patient_profile($parameters,$method,$user_id){

        extract($parameters);

        $patient_rec = $this->db->select('patient_id, title, first_name, middle_name, last_name, CONCAT(title,".",first_name," ",last_name) as full_name, umr_no, gender, date_of_birth, age,age_unit, occupation, country, mobile, alternate_mobile, email_id as email, address_line as address, district_id, state_id, pincode,  payment_status, clinic_id, referred_by_type, referred_by, country, occupation, photo, qrcode, location,photo, preferred_language, created_date_time as registration_date, status')->from('patients')->where('patient_id =',$patient_id)->get()->row();

        //echo $this->db->last_query();
        //exit();

        if(count($patient_rec) > 0){
            $param['patient'] = $patient_rec;
            $param['patient']->priority = NULL;
            $param['patient']->patient_condition = NULL;
            $param['patient']->condition_months = NULL;
            $param['patient']->appointment_id = NULL;
            $param['patient']->appointment_date = NULL;
            $param['patient']->appointment_time = NULL;
            $param['patient']->appointment_type = NULL;
            $param['patient']->check_in_time = NULL;
            $param['patient']->date_of_birth = NULL;
            $param['patient']->doctor_comments = NULL;
            $param['patient']->doctor_id = NULL;
            $param['patient']->doctor_name = NULL;
            $param['patient']->department = NULL;
            $param['patient']->department_id = NULL;
            $param['patient']->color_code  = NULL;
            $param['patient']->qb_user_id = null;
            $param['patient']->qb_user_login = null;
            $param['patient']->qb_user_fullname = null;
            $param['patient']->qb_user_tag = null;
            $param['patient']->immunization_status = NULL;
            $param['patient']->image = base_url().'uploads/patients/'.$patient_rec->photo;

            $param['patient']->mobile = DataCrypt($patient_rec->mobile, 'decrypt');
            
            if($param['patient']->alternate_mobile != NULL || $param['patient']->alternate_mobile != '')
                $param['patient']->alternate_mobile = DataCrypt($patient_rec->alternate_mobile, 'decrypt');

            if($param['patient']->email != NULL || $param['patient']->email != '')
                $param['patient']->email = DataCrypt($patient_rec->email, 'decrypt');

            // Get all states
            $state_details = $this->Generic_model->getAllRecords("states", $condition = '', $order = '');
            $s = 0;
            foreach ($state_details as $state) {
                $param['state'][$s]['state_id'] = $state->state_id;
                $param['state'][$s]['state_name'] = $state->state_name;
                $district_details = $this->db->select("*")->from("districts")->where("state_id='" . $state->state_id . "' ")->get()->result();
                $d = 0;

                if (count($district_details) > 0) {
                    foreach ($district_details as $district) {
                        $param['state'][$s]['districts'][$d]['district_id'] = $district->district_id;
                        $param['state'][$s]['districts'][$d]['district_name'] = $district->district_name;
                        $param['state'][$s]['districts'][$d]['state_id'] = $district->state_id;
                        $d++;
                    }
                } else {
                    $param['state'][$s]['districts'] = array();
                }

                $s++;
            }

            $this->response(array('code' => '200', 'message' => 'Patient Complete Profile', 'result' => $param, 'requestname' => $method));
        }else{
            $param['patient'] = (object)[];
            $this->response(array('code' => '201', 'message' => 'No Patient Found', 'result' => $param, 'requestname' => $method));
        }
    }


    /*
    While verifying the OTP, check if the user exist with the mobile no. 
    if No., then create the user and send result
    */ 
    public function verified_otp($parameters,$method,$user_id){

        extract($parameters);

        // Check if the user exist with the mobile no.
        $check_user = $this->db->query("select * from patients where mobile='".DataCrypt($parameters['mobile'], 'encrypt')."'")->row();
        if(count($check_user) > 0){

            $patient_id = $check_user->patient_id;
            // $mobile = DataCrypt($parameters['mobile'], 'encrypt');
            $mobile = $parameters['mobile'];

            $doctor_ids = $this->db->query("Select doctor_id as doctor from appointments where patient_id='".$patient_id."' order by appointment_id desc")->row();

            $doctors = $this->db->query("Select * from doctors where doctor_id in ('".$doctor_ids->doctor."')")->result();

            $d = 0;

            if(count($doctors) > 0){

                foreach($doctors as $value)
                {
                    $profile_image = $value->profile_image;

                    if($profile_image == NULL || $profile_image == "")
                    {
                        $doctor_image = NULL;
                    }else{
                        $doctor_image = base_url().'uploads/profile_image/'.$profile_image;
                    }

                    $doctorDegree = $this->db->query("Select GROUP_CONCAT(degree_name) as doctor_degree from doctor_degrees where doctor_id='".$value->doctor_id."' ")->row();
                
                    if($value->acheivements == '' || $value->acheivements == NULL)
                    {
                        $acheivements = array();
                    }else{
                        $acheivements = explode(",",$value->acheivements);
                    }
                
                    $param['doctor'][$d]['doctor_id'] = $value->doctor_id;
                    $param['doctor'][$d]['name'] = "Dr. ".$value->first_name." ".$value->last_name;
                    $param['doctor'][$d]['specialization'] = $value->speciality;
                    $param['doctor'][$d]['experience'] = $value->experience;
                    $param['doctor'][$d]['visit_time'] = $value->consulting_times;
                    $param['doctor'][$d]['about_doctor'] = $value->about;
                    $param['doctor'][$d]['known_languages'] = $value->languages;
                    $param['doctor'][$d]['dealt_deseases'] = $value->diseases_dealt;
                    $param['doctor'][$d]['degrees'] = $doctorDegree->doctor_degree;
                    $param['doctor'][$d]['achievements'] = $acheivements;
                    $param['doctor'][$d]['membership'] = $value->membership_in;
                    $param['doctor'][$d]['address'] = $value->address;
                    $param['doctor'][$d]['doctor_image'] = $doctor_image;
                    $param['doctor'][$d]['contact'] = $value->mobile;

                    $d++;
                
                }

            }else{
                $param['doctor'] = NULL;
            }
            
            $patient_details = $this->db->select('patient_id, umr_no, CONCAT(title,".",first_name," ",last_name) as name, mobile')->from('patients')->where('patient_id =',$patient_id)->get()->row();

            $patient_details->mobile = DataCrypt($patient_details->mobile,'decrypt');

            $param['patient'] = $patient_details;
        
            $otp_up['otp'] = "";

            $ok = $this->Generic_model->updateData('patient_otp',$otp_up,array('patient_id' => $parameters['user_id']));

            if ($ok == 1) {
                $this->response(array('code' => '200', 'message' => 'Successfully','result' =>$param, 'requestname' => $method));
            } else {
                $this->response(array('code' => '404', 'message' => 'Failed'), 200);
            }

        }else{

            // No patient exists with mobile no.
            // But the OTP is verified
            // No create a patient & send user_id

            // Generate UMR No
            $last_umr_no = $this->db->select("*")->from("patients")->order_by("patient_id","desc")->get()->row();

            if($last_umr_no->umr_no == NULL || $last_umr_no->umr_no == ""){
                $umr = "P".date("my")."1";
            }else{
                $trim_umr = trim($last_umr_no->umr_no);
                $check_umr = substr($trim_umr,1,4);
                if($check_umr == date("my")){
                    $umr = (++$trim_umr);
                }else{
                    $umr = "P".date("my")."1";
                }   
            }

            $patient_rec['umr_no'] = $umr;
            $patient_rec['mobile'] = DataCrypt($mobile,'decrypt');
            $patient_rec['username'] = $umr;
            $patient_rec['password'] = md5($umr);
            $patient_rec['clinic_id'] = 0;
            $patient_rec['payment_status'] = 0;
            $patient_rec['status'] = 1;
            $patient_rec['created_date_time'] = date('Y-m-d H:i:s');
            $patient_rec['modified_date_time'] = date('Y-m-d H:i:s');

            $patient_id = $this->Generic_model->insertDataReturnId('patients',$patient_rec);

            $patientUpdate['created_by'] = $patient_id;
            $patientUpdate['modified_by'] = $patient_id;

            $res = $this->Generic_model->updateData('patients',$patientUpdate,array('patient_id' => $patient_id));

            $bulksmsnumbers = $this->db->query("select * from bulksms_numbers where mobile='".$parameters['mobile']."'")->row();
            if(count($bulksmsnumbers)>0)
            {
                $CDP['patient_id'] = $patient_id;
                $CDP['doctor_id'] = $bulksmsnumbers->user_id;
                $CDP['clinic_id'] = $bulksmsnumbers->clinic_id;
                $CDP['status'] = 1;
                $this->Generic_model->insertData("clinic_doctor_patient",$CDP);
            }

            $patient_details = $this->db->select('patient_id, umr_no, CONCAT(title,".",first_name," ",last_name) as name, mobile')->from('patients')->where('patient_id =',$patient_id)->get()->row();

            $param['doctor'] = NULL;
            $param['patient'] = $patient_details;
            $param['mobile'] = DataCrypt($patient_details->mobile, 'decrypt');

            $this->response(array('code' => '201', 'message' => 'Successfulll','result'=>$param, 'requestname' => $method));

        }
    }

    public function doctor_videos($parameters,$method,$user_id)
    {
        $doctor_id = $parameters['doctor_id'];
        // $sql = $this->db->select('department_id')
        // ->from('doctors')
        // ->where('doctor_id =',$doctor_id)
        // ->get()
        // ->row();
        // $sql1 = $this->db->select('*')
        // ->from('articles a')
        // ->join('article_department ad','a.article_id = ad.article_id')
        // ->where("ad.department_id ='" . $sql->department_id . "' ")
        // ->get()
        // ->result();


        $doctorInfo = $this->db->select('CONCAT("Dr. ",Doc.first_name," ",Doc.last_name) as doctor_name, Doc.profile_image as profile_pic, Doc.work_place_location as location, Dep.department_name as department')->from('doctors Doc')->join('department Dep','Doc.department_id = Dep.department_id','inner')->where('Doc.doctor_id =',$doctor_id)->get()->result_array();

        $videoInfo = $this->db->query("select * from articles where created_by = '".$doctor_id."' and article_status='published' order by created_date_time DESC")->result();
        // echo $this->db->last_query();

        $i=0;
        foreach($videoInfo as $value)
        {
            $data['videos'][$i]['article_id'] = $value->article_id;
            $data['videos'][$i]['article_title'] = $value->article_title;
            $data['videos'][$i]['short_description'] = $value->short_description;
            $data['videos'][$i]['article_description'] = $value->article_description;
            $data['videos'][$i]['article_type'] = $value->article_type;
            $data['videos'][$i]['video_url'] = $value->video_url;
            $data['videos'][$i]['video_image'] = $value->video_image;
            $data['videos'][$i]['article_image'] = $value->video_image;
            $data['videos'][$i]['created_date_time'] = $value->created_date_time;

            $data['videos'][$i] = array_merge($data['videos'][$i], $doctorInfo[0]);

            $i++;
        }
    
        $this->response(array('code' => '200', 'message' => 'Doctor Videos', 'result' => $data, 'requestname' => $method));
    }

    // Check appointment Status
    public function checkAppStatus($parameters,$method,$user_id)
    {
        extract($parameters);
        if($patient_id != '' && $doctor_id != '')
        {
            $this->db->select('appointment_id, status, appointment_date, appointment_time_slot, patient_id, doctor_id');
            $this->db->from('appointments');
            $this->db->where('doctor_id =',$doctor_id);
            $this->db->where('patient_id =',$patient_id);
            $this->db->group_start();
            $this->db->where('status =','booked');
            $this->db->or_where('status =','checked_in');
            $this->db->or_where('status =','vital_signs');
            $this->db->or_where('status =','waiting');
            $this->db->or_where('status =','in_consultation');       
            $this->db->group_end();
            $this->db->order_by('appointment_id','DESC');
            $this->db->limit(1);
            $check =$this->db->get()->row();
            // echo $this->db->last_query();
            if(count($check) > 0)
            {
                $data['appointments']['appStatus'] = 1;
                $data['appointments']['app'] = "You already have an appointment with ".getDoctorName($check->doctor_id)." is in ".strtoupper(str_replace('_',' ',$check->status));
            }
            else
            {
                $data['appointments']['appStatus'] = 0;
                $data['appointments']['app'] = "";
            }
            $this->response(array('code'=>'200','message'=>'success','result'=>$data,'method'=>$method));
        }
        else
        {
            $data['appointments']['appStatus'] = 2;
            $data['appointments']['app'] = "Parameters Missing";
            $this->response(array('code'=>'201','message'=>'error','result'=>$data,'method'=>$method));
        }
    }


    // save billing for appointment 
    public function save_billing($parameters,$method,$user_id){
        extract($parameters);
        // echo $payment_status;
        if($payment_status == "1")
        {
            $appInfo = $this->db->query("select * from appointments where appointment_id='".$appointment_id."'")->row();
            $clinic_id = $appInfo->clinic_id;
            // Generate Invoice and Receipt no
            $invoice_no_alias = generate_invoice_no($clinic_id);
            $invoice_no = $clinic_id.$invoice_no_alias;     
            // Patient Details 
            $patientInfo = $this->db->query("select * from patients where patient_id='".$appInfo->patient_id."'")->row();
            // billing Details 
            $billing_master['invoice_no'] = $invoice_no;
            $billing_master['invoice_no_alias'] = $invoice_no_alias;
            $billing_master['appointment_id'] = $appointment_id;
            $billing_master['patient_id'] = $appInfo->patient_id;
            $billing_master['doctor_id'] = $appInfo->doctor_id;
            $billing_master['clinic_id'] = $appInfo->clinic_id;
            $billing_master['umr_no'] = $appInfo->umr_no;
            $billing_master['payment_mode'] = $payment_mode;
            $billing_master['payment_status'] = $payment_status;
            $billing_master['transaction_id'] = $transaction_id;
            $billing_master['guest_name'] = ucwords($patientInfo->$first_name." ".$patientInfo->$last_name);
            $billing_master['guest_mobile'] = ($patientInfo->mobile != '' ? $patientInfo->mobile : $patientInfo->alternate_mobile);
            $billing_master['billing_date_time'] = date('Y-m-d H:i:s');
            $billing_master['created_by'] = $appInfo->doctor_id;
            $billing_master['created_date_time'] = date('Y-m-d H:i:s');
            $billing_master['modified_by'] = $appInfo->doctor_id;
            $billing_master['modified_date_time'] = date('Y-m-d H:i:s');
            // $billing_master['payment_status'] = $con_payment_status;
            if($registration_fee != "")
            {
                $billing_master['billing_type'] = "Registration & Consultation";
            }
            else
            {
                $billing_master['billing_type'] = "Consultation";
            }
            
            // echo "<pre>";print_r($billing_master);echo "</pre>";
            $billing_id = $this->Generic_model->insertDataReturnId('billing',$billing_master);
            // echo $this->db->last_query();
            if($registration_fee != "")
            {
                $billingLineItems['billing_id'] = $billing_id;
                $billingLineItems['item_information'] = "Registration";
                $billingLineItems['discount'] = "0";
                $billingLineItems['discount_unit'] = "INR";
                $billingLineItems['amount'] = $registration_fee;
                $billingLineItems['created_by'] = $appInfo->doctor_id;
                $billingLineItems['created_date_time'] = date('Y-m-d H:i:s');
                $billingLineItems['modified_by'] = $appInfo->doctor_id;
                $billingLineItems['modified_date_time'] = date('Y-m-d H:i:s');
                $this->Generic_model->insertData('billing_line_items',$billingLineItems);
            }
            $billingLineItems['billing_id'] = $billing_id;
            $billingLineItems['item_information'] = "Conultation";
            $billingLineItems['discount'] = "0";
            $billingLineItems['discount_unit'] = "INR";
            $billingLineItems['amount'] = $consultation_fee;
            $billingLineItems['created_by'] = $appInfo->doctor_id;
            $billingLineItems['created_date_time'] = date('Y-m-d H:i:s');
            $billingLineItems['modified_by'] = $appInfo->doctor_id;
            $billingLineItems['modified_date_time'] = date('Y-m-d H:i:s');
            $this->Generic_model->insertData('billing_line_items',$billingLineItems);
            $apData['payment_status'] = 1;
            $apData['status'] = "waiting";
            $this->Generic_model->updateData('appointments', $apData, array('appointment_id'=>$appointment_id));
            $this->response(array('code'=>'200','message'=>'Payment Success','result'=>'Payment Success','requestname'=>$method));
        }
        else
        {
            $this->Generic_model->deleteRecord("appointments",array('appointment_id'=>$appointment_id));
        }
        

    }

      // Book and appointment
      public function book_appointment($parameters, $method, $user_id) {

        // By default the payment = 0
        $payment = 0;

        extract($parameters);
        /*
        Check if there is already an appointment on the following below criteria
        Selected doctor
        Selected Date
        Selected Patient
        */
        $this->db->select('appointment_id, appointment_date, appointment_time_slot, patient_id, doctor_id');
        $this->db->from('appointments');
        $this->db->where('appointment_date =',$appointment_date);
        $this->db->where('appointment_time_slot =',$appointment_time_slot);
        $this->db->where('patient_id =',$patient_id);
        $this->db->where('status !=','closed');    

        $checkAppointment = $this->db->get()->result();

        if(count($checkAppointment) > 0) {
            // Appointment exist
            $this->response(array('code' => '201', 'message' => 'Appointment already exists', 'result' => $checkAppointment, 'requestname' => $method));
        }else{
            $clinicPatientInfo = $this->db->query("select * from clinic_doctor_patient where clinic_id='".$clinic_id."' and patient_id='".$patient_id."'")->row();
            $clinicDocInfo = $this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."' and doctor_id='".$doctor_id."'")->row();
            $clinicsInfo  =$this->db->query("select * from clinics where clinic_id='".$clinic_id."'")->row();
            // $param['query'][$c]['q'] = $this->db->last_query();
            if(count($clinicPatientInfo)>0)
            {
                $param['appointment']['registration_fee'] = 0;
                $param['appointment']['consultation_fee'] = (int)$clinicDocInfo->consulting_fee;
            }
            else
            {
                $param['appointment']['registration_fee'] = $clinicsInfo->registration_fee;
                $param['appointment']['consultation_fee'] = (int)$clinicDocInfo->consulting_fee;
            }
            if($slot_type == 'video call')
            {
                $clinic_doctor =  $this->db->select("*")
                ->from("clinic_doctor")
                ->where("doctor_id='".$doctor_id."'")
                ->get()->result();

                $data['clinic_id'] = $clinic_doctor[0]->clinic_id;

            }else{
                $data['clinic_id'] = $clinic_id;
            }

            $data['patient_id'] = $patient_id;
            $data['appointment_type'] = $appointment_type;
            $data['umr_no'] = $umr_no;
            $data['booking_type'] = $booking_type;
            $data['doctor_id'] = $doctor_id;
            $data['appointment_date'] = $appointment_date;
            $data['slot_type'] = $slot_type;
            $data['appointment_time_slot'] = $appointment_time_slot;
            $data['created_by'] = $user_id;
            $data['created_date_time'] = date('Y-m-d H:i:s');
            $data['modified_by'] = $user_id;
            $data['modified_date_time'] = date('Y-m-d H:i:s');
            $data['status'] = "booked";
            
            $data['priority'] = $priority;

            // Capture the last inserted Appointment ID
            $appointment_id = $this->Generic_model->insertDataReturnId("appointments", $data);

            $date_split = explode("-", date("Y-m-d", strtotime($appointment_date)));
            $month = date("F", mktime(0, 0, 0, $date_split[1], 10));

            $patientMobile = DataCrypt($patient_info->mobile,'decrypt');
            $alternateMobile = DataCrypt($patient_info->alternate_mobile,'decrypt');

            // Appointment information SMS for patient
            $patientSMSContent = "Dear " . ucwords($patient_info->first_name).", Your appointment is fixed with Dr. " . ucwords($doctor_info->first_name . " " . $doctor_info->last_name) . ",  " . ucwords($clinic_info->clinic_name) . " on " . $date_split[2] . " " . $month . " " . $date_split[0] . " at " . date("h:i A", strtotime($appointment_time_slot));
            sendsms($patientMobile, $patientSMSContent);

            // Map Clinic, Doctor & Patient if not already mapped
            $chkMapping = $this->db->select('*')->from('clinic_doctor_patient')->where('clinic_id =',$clinic_id)->where('doctor_id =',$doctor_id)->where('patient_id =',$patient_id)->get()->num_rows();

            if($chkMapping == 0){
                // Create Mapping between clinic,doctor & patient
                if($slot_type == 'video call')
                {
                    $clinic_doctor =  $this->db->select("*")
                    ->from("clinic_doctor")
                    ->where("doctor_id='".$doctor_id."'")
                    ->get()->result();
    
                    $mappingData['clinic_id'] = $clinic_doctor[0]->clinic_id;
    
                }else{
                    $mappingData['clinic_id'] = $clinic_id;
                }
                // $mappingData['clinic_id'] =  $clinic_id;
                $mappingData['doctor_id'] = $doctor_id;
                $mappingData['patient_id'] = $patient_id;
                $mappingData['created_by'] = $mappingData['modified_by'] = $user_id;
                $mappingData['created_date_time'] = $mappingData['modified_date_time'] = date('Y-m-d H:i:s');
                $this->Generic_model->insertData('clinic_doctor_patient', $mappingData);
            }

            // Get params to respond
            $param['appointment']['appointment_id'] = $appointment_id;
            if($slot_type == 'video call')
            {
                $clinic_doctor =  $this->db->select("*")
                ->from("clinic_doctor")
                ->where("doctor_id='".$doctor_id."'")
                ->get()->result();

                $param['appointment']['clinic_id'] =  $clinic_doctor[0]->clinic_id;

                // $mappingData['clinic_id'] = $clinic_doctor[0]->clinic_id;

            }else{
                $param['appointment']['clinic_id']  = $clinic_id;
            }
            // $param['appointment']['clinic_id'] =  $clinic_id;
            $param['appointment']['patient_id'] = $patient_id;
            $param['appointment']['umr_no'] = $umr_no;
            $param['appointment']['doctor_id'] = $doctor_id;
            $param['appointment']['doctor_name'] = "Dr. " . strtoupper($doctor_info->first_name . " " . $doctor_info->last_name);
            $param['appointment']['department_id'] = $doctor_info->department_id;
            $param['appointment']['department'] = $dept_info->department_name;
            $param['appointment']['appointment_type'] = $appointment_type;
            $param['appointment']['appointment_date'] = $appointment_date;
            $param['appointment']['appointment_time_slot'] = $appointment_time_slot;
            $param['appointment']['priority'] = $priority;
            $param['appointment']['status'] = "booked";

            $check_data = $this->db->select("*")->from("doctor_patient")->where("doctor_id='" . $doctor_id . "' and patient_id='" . $patient_id . "'")->get()->row();
            if (count($check_data) == 0) {
                $dp['doctor_id'] = $doctor_id;
                $dp['patient_id'] = $patient_id;
                $dp['created_by'] = $user_id;
                $dp['modified_by'] = $user_id;
                $dp['created_date_time'] = date("Y-m-d H:i:s");
                $dp['modified_date_time'] = date("Y-m-d H:i:s");
                $this->Generic_model->insertData('doctor_patient', $dp);
            } 

            $this->response(array('code' => '200', 'message' => 'Appointment Booked', 'result' => $param, 'requestname' => $method));

        }

    }

    public function family_health_records($parameters, $method, $user_id)
    {
        extract($parameters);
        $parentInfo = getPatientDetails($user_id);
        if($phone_number != ""){
            $mobile = DataCrypt($phone_number, 'encrypt');
            $data['mobile'] = $mobile; 
        }

        $data['first_name'] = $full_name;
        $data['age'] = $age;
        $data['age_unit'] = $age_unit;
        $data['alternate_mobile'] = $parentInfo->mobile;
        $data['created_by'] = $user_id;
        $data['created_date_time'] = date('Y-m-d H:i:s');
        $data['modified_by'] = $user_id;
        $data['modified_date_time'] = date('Y-m-d H:i:s');
        $patient_id = $this->Generic_model->insertDataReturnId('patients', $data);

        $umr_no = 'P'.date('my').$patient_id;

        $tempDir = './uploads/qrcodes/patients/';
        $codeContents = $umr_no;
        $qrname = $umr_no.md5($codeContents).'.png';
        $pngAbsoluteFilePath = $tempDir . $qrname;
        $urlRelativeFilePath = base_url().'uploads/qrcodes/patients/'.$qrname;

        if (!file_exists($pngAbsoluteFilePath)) {
            QRcode::png($codeContents, $pngAbsoluteFilePath);
        }

        $ptData['username'] = $umr_no;
        $ptData['umr_no'] = $umr_no;
        $ptData['qrcode'] = $qrname;
        $this->Generic_model->updateData('patients', $ptData, array('patient_id'=>$patient_id));
        
        $this->response(array('code' => '200', 'message' => 'Success', 'result' =>$insert_checklist, 'requestname' => $method));
    }

    // update family health records
    public function family_health_records_update($parameters, $method, $user_id)
    {
        $id = $parameters['id'];
        $full_name = $parameters['full_name'];
        $patient_id = $parameters['parent_patient_id'];
        $age = $parameters['age'];
        $gender = $parameters['gender'];
        $relationship =  $parameters['relationship'];
        $phone_number =  $parameters['phone_number'];

        $insert_checklist['parent_patient_id'] = $patient_id;
        $insert_checklist['full_name'] = $full_name;
        $insert_checklist['phone_number'] = $phone_number;
        $insert_checklist['gender'] = $gender;
        $insert_checklist['age'] = $age;
        $insert_checklist['relationship'] = $relationship;
        $insert_checklist['created_by'] = $patient_id;
        $insert_checklist['modified_by'] = $patient_id;
        $insert_checklist['created_date_time'] = date('Y-m-d H:i:s');
        $insert_checklist['modified_date_time'] = date('Y-m-d H:i:s');
        $this->Generic_model->updateData('patient_family_health_records', $insert_checklist, array('id'=>$id));
        $this->response(array('code' => '200', 'message' => 'Success', 'result' =>$insert_checklist, 'requestname' => $method));
    }

    public function get_family_health_records($parameters, $method, $user_id)
    {
        $patient_id = $parameters['patient_id'];
        $clist1 = $this->db->select("*")->from("patient_family_health_records pr")
            ->where("pr.parent_patient_id='" .$patient_id  . "' ")
            ->get()
            ->result();
        if(count($clist1)>0)
        {
            $i = 0;
            foreach ($clist1 as $cform) {
                $para['family_health_records'][$i]['id'] = $cform->id;
                $para['family_health_records'][$i]['parent_patient_id'] = $cform->parent_patient_id;
                $para['family_health_records'][$i]['full_name'] = $cform->full_name;
                $para['family_health_records'][$i]['phone_number'] = $cform->phone_number;
                $para['family_health_records'][$i]['gender'] = $cform->gender;
                $para['family_health_records'][$i]['age'] = $cform->age;
                $para['family_health_records'][$i]['relationship'] = $cform->relationship;
                $i++;
            }
        }
        else{
             $para['family_health_records'] = array();
            // $this->response(array('code' => '200', 'message' => 'Success', 'result' =>'', 'requestname' => $method));
        }
            // $i = 0;
            // foreach ($clist1 as $cform) {
            //     $para['family_health_records'][$i]['id'] = $cform->id;
            //     $para['family_health_records'][$i]['parent_patient_id'] = $cform->parent_patient_id;
            //     $para['family_health_records'][$i]['full_name'] = $cform->full_name;
            //     $para['family_health_records'][$i]['phone_number'] = $cform->phone_number;
            //     $para['family_health_records'][$i]['gender'] = $cform->gender;
            //     $para['family_health_records'][$i]['age'] = $cform->age;
            //     $para['family_health_records'][$i]['relationship'] = $cform->relationship;
            //     $i++;
            // }
        $this->response(array('code' => '200', 'message' => 'Success', 'result' =>$para, 'requestname' => $method));

    }

    // public function deleteAndEdit($parameters, $method, $user_id)
    // {
    //     echo "welcome";
    //     // $this->response(array('code' => '200', 'message' => 'Family Records Saved Successfully', 'result' =>"Welcome", 'requestname' => $method));
    // }

    public function my_records_delete($parameters, $method, $user_id)
    {
        extract($parameters);
        $check_exist = $this->db->select("*")->from("citizen_records")->where("citizen_record_id='".$document_id."'")->get()->row();
        if(count($check_exist)>0)
        {
            $images = trim($check_exist->images, ",");
            $picture_explode = explode(",", $images);
            for ($k = 0; $k < count($picture_explode); $k++) {
                unlink(base_url('uploads/my_records/' . trim($picture_explode[$k])));
            }
            $res = $this->Generic_model->deleteRecord('citizen_records',array('citizen_record_id'=>$document_id));
            $this->response(array('code' => '200', 'message' => 'Record Deleted Successfully', 'result' => "Success", 'requestname' => $method));  
        }
    }

    public function family_records_delete($parameters, $method, $user_id)
    {
        extract($parameters);
        $check_exist = $this->db->select("*")->from("family_health_documents")->where("id='".$document_id."'")->get()->row();
        if(count($check_exist)>0)
        {
            $images = trim($check_exist->images, ",");
            $picture_explode = explode(",", $images);
            for ($k = 0; $k < count($picture_explode); $k++) {
                unlink(base_url('uploads/health_records_documents/' . trim($picture_explode[$k])));
            }
            $res = $this->Generic_model->deleteRecord('family_health_documents',array('id'=>$document_id));
            $this->response(array('code' => '200', 'message' => 'Record Deleted Successfully', 'result' => "Success", 'requestname' => $method));  
        }
    }

    public function health_records_modifications($parameters, $method, $user_id)
    {

        extract($parameters);
        $check_exist = $this->db->select("*")->from("patient_family_health_records")->where("id ='" . $parameters['id'] . "'")->get()->row();

        $id = $parameters['id'];
        $type=$parameters['type'];
        $full_name = $parameters['full_name'];
        // $this->response(array('code' => '200', 'message' => 'Family Records Saved Successfully', 'result' =>NULL, 'requestname' => $method));
        if($type == "del")
        {
            $res = $this->Generic_model->deleteRecord('patient_family_health_records',array('id'=>$parameters['id']));
            $this->response(array('code' => '200', 'message' => 'Record Deleted Successfully', 'result' => $type, 'requestname' => $method));  
        }
        else if($type == 'edit'){
            $i = 0;
            $family_records_edit['parent_patient_id'] =  $parameters['parent_patient_id'];
            $family_records_edit['phone_number'] =  $parameters['phone_number'];
            $family_records_edit['age'] =  $parameters['age'];
            $family_records_edit['gender'] =  $parameters['gender'];
            $family_records_edit['relationship'] =  $parameters['relationship'];
            $family_records_edit['full_name'] =  $parameters['full_name'];
            $updateRes = $this->Generic_model->updateData('patient_family_health_records', $family_records_edit, array('id'=>$id));

        $this->response(array('code' => '200', 'message' => 'Family Records Saved Successfully', 'result' =>NULL, 'requestname' => $method));
        }
        else{
            $this->response(array('code' => '200', 'message' => 'Send Type', 'result' => $type, 'requestname' => $method));
        }
    }

    public function patient_details_info($parameters, $method, $user_id)
    {
        $patient_id = $parameters['patient_id'];
        $today = date('Y-m-d');
        $clist =  $this->db->select("*,ps.first_name as f_name,ps.gender as p_gender")
        ->from("appointments ap")
        ->join("patients ps","ps.patient_id=ap.patient_id")
        ->join("clinics cs","cs.clinic_id=ap.clinic_id")
        ->join("doctors ds","ds.doctor_id=ap.doctor_id")
        ->join("department dt","dt.department_id=ds.department_id")
        ->where("ps.patient_id='". $patient_id."' ")
        ->where("ap.appointment_date<='". $today."' ")
        ->get()
        ->result();


        $i = 0;
        foreach ($clist as $cform) {
            $para['patient_details_info'][$i]['patient_name'] = $cform->f_name;
           
            $para['patient_details_info'][$i]['umr_no'] = $cform->umr_no;
            $para['patient_details_info'][$i]['patient_gender'] = $cform->p_gender;
            $para['patient_details_info'][$i]['patient_age'] = $cform->age;
            $para['patient_details_info'][$i]['clinic_name']=$cform->clinic_name;
            $para['patient_details_info'][$i]['doctor_name'] = "Dr. " . strtoupper($cform->first_name . ' ' . $cform->last_name);
            $para['patient_details_info'][$i]['doctor_department']=$cform->department_name;
            $para['patient_details_info'][$i]['appointment_id'] = $cform->appointment_id;
            $para['patient_details_info'][$i]['patient_name'] = $cform->f_name;
            $para['patient_details_info'][$i]['clinic_location']=$cform->location;
            $para['patient_details_info'][$i]['appointment_time_slot'] = $cform->appointment_time_slot;
            $para['patient_details_info'][$i]['appointment_date'] = $cform->appointment_date;
            $i++; 
     
        }

  
        
        $this->response(array('code' => '200', 'message' => 'success', 'result' => $para, 'requestname' => $method));
    }

    public function get_health_family_records_list($parameters, $method, $user_id) {

        $previous_documents = $this->db->select("*")->from("family_health_documents")->where("patient_id= '" . $parameters['patient_id'] . "'")->order_by("id","desc")->get()->result();
    
        $pv = 0;
        if (count($previous_documents) > 0) {
    
            foreach ($previous_documents as $pav) {
                $param['previous_documents'][$pv]['document_id'] = $pav->id;
                $param['previous_documents'][$pv]['parent_patient_id'] = $pav->parent_patient_id;
                $param['previous_documents'][$pv]['current_date'] = $pav->cur_date;
                $param['previous_documents'][$pv]['patient_id'] = $pav->patient_id;
                $param['previous_documents'][$pv]['report_date'] = $pav->report_date;
                $param['previous_documents'][$pv]['document_type'] = $pav->document_type;
    
                $param['previous_documents'][$pv]['description'] = $pav->description;
                $images = trim($pav->images, ",");
                $picture_explode = explode(",", $images);
                for ($k = 0; $k < count($picture_explode); $k++) {
                    $param['previous_documents'][$pv]['images'][$k]['image'] = base_url('uploads/health_records_documents/' . trim($picture_explode[$k]));
                }
                $pv++;
            }
        } else {
            $param['previous_documents'] = NULL;
        }
        $this->response(array('code' => '200', 'message' => 'Success', 'result' => $param, 'requestname' => $method));
    }

    public function get_my_records_list($parameters, $method, $user_id) {

        $citizen_records = $this->db->select("*")->from("citizen_records")->where("patient_id= '" . $parameters['patient_id'] . "'")->order_by("citizen_record_id","desc")->get()->result();
    
        $pv = 0;
        if (count($citizen_records) > 0) {
    
            foreach ($citizen_records as $pav) {
                $param['previous_documents'][$pv]['document_id'] = $pav->citizen_record_id;
                $param['previous_documents'][$pv]['current_date'] = $pav->cur_date;
                $param['previous_documents'][$pv]['patient_id'] = $pav->patient_id;
                $param['previous_documents'][$pv]['report_date'] = $pav->report_date;
                $param['previous_documents'][$pv]['document_type'] = $pav->document_type;
                $param['previous_documents'][$pv]['description'] = $pav->description;
                $images = trim($pav->images, ",");
                $picture_explode = explode(",", $images);
                for ($k = 0; $k < count($picture_explode); $k++) {
                    $param['previous_documents'][$pv]['images'][$k]['image'] = base_url('uploads/my_records/' . trim($picture_explode[$k]));
                }
                $pv++;
            }
            $this->response(array('code' => '200', 'message' => 'Success', 'result' => $param, 'requestname' => $method));
        } else {
            $param['previous_documents'] = NULL;
            $this->response(array('code' => '201', 'message' => 'No Records Found', 'result' => $param, 'requestname' => $method));
        }
    }

        //How to use umdaa
        public function howToUseCitizen($parameters, $method, $user_id){
            $tutorialLinks = $this->db->select("*")->from("umdaa_tutorials")->where("tutorial_type='citizen'")->get()->result();
            if(sizeof($tutorialLinks)>0)
            {
                $i = 0;
                foreach ($tutorialLinks as $value) {
                    $data['tutorial'][$i]['tutorial_id'] = $value->umdaa_tutorial_id;
                    $data['tutorial'][$i]['tutorial_name'] = $value->tutorial_name;
                    $data['tutorial'][$i]['tutorial_link'] = $value->tutorial_link;
                    $data['tutorial'][$i]['video_thumbnail'] = base_url()."uploads/thumbnails/".$value->video_thumbnail;
                    $i++;
                }
                $this->response(array('code' => '200', 'message' => 'Tutorial Videos', 'result' => $data, 'requestname' => $method));
            }
            else
            {
                $this->response(array('code' => '201', 'message' => 'No Videos Found', 'requestname' => $method));
            }   
        }

        public function paymentCharges($parameters, $method, $user_id)
        {
           $paraa['payment_charges']['internetHandlingFees'] = '8.26';
           $paraa['payment_charges']['bookingFees'] = '7';
           $paraa['payment_charges']['serviceTax'] = '18';
           $paraa['payment_charges']['paymentGatewayCharges'] = '2.4';
       
           $this->response(array('code' => '200', 'message' => 'success ', 'result' =>$paraa, 'requestname' => $method));    
        }

public function citizen_app_version($parameters,$method,$user_id)
{
    $result=$this->Generic_model->getSingleRecord("app_version",array("app_category"=>"Citizens"),$order='');

    if(count($result)>0)
    {
        $data['app_id']=$result->app_id;
        $data['app_version_id']=$result->app_version_id;
        $data['app_version_name']=$result->app_version_name;
        $data['contact_number']=$result->contact_number;
        $data['updated_date_time']=$result->updated_date_time;
        
        $this->response(array('code'=>'200','message' => 'appversion','result'=>$data,'requestname'=>$method));

    }else{

        $this->response(array('code' => '404','message' => 'appversion', 'Authentication Failed'), 200);
        
    }

}

    public function doctor_patient_documents($parameters,$method,$user_id)
    {
        extract($parameters);

        if(count($documents_list)>0)
        {
            for($n=0;$n<count($documents_list);$n++)
            {     
                $data['doctor_id'] = $doctor_id;
                $data['patient_id'] = $patient_id;
                $data['document_id'] = $documents_list[$n];
                $data['created_by'] = $doctor_id;
                $data['modified_by'] = $doctor_id;
                $data['created_date_time'] =date('Y-m-d H:i:s');
                $data['modified_date_time'] = date('Y-m-d H:i:s');
                $this->Generic_model->insertData("doctor_patient_documents",$data);
            }
            
        $this->response(array('code'=>'200','message' => 'Success.Documents Shared','requestname'=>$method));
        }
        else{
            $this->response(array('code'=>'201','message' => 'Empty documents List','requestname'=>$method));
        }

    } 
    
    public function checkPatientInformation($parameters,$method,$user_id)
    {
        extract($parameters);
        // Patient Verification based on doctor id and clinic id
        $patientverification =  $this->db->select("*")
        ->from("appointments")
        ->where("patient_id= '" . $patient_id . "' and doctor_id='".$doctor_id."'")
        ->order_by("created_date_time","desc")
        ->get()->result();

        // echo $this->db->last_query();
        // exit();

        if(count($patientverification)>0)
        {
            $clinic_doctor =  $this->db->select("*")
            ->from("clinic_doctor")
            ->where("clinic_id= '" . $patientverification[0]->clinic_id . "' and doctor_id='".$doctor_id."'")
            ->get()->row();

            //  echo $this->db->last_query();
            //  exit();

             if($clinic_doctor->review_days != 0)
             {
                 $days = $clinic_doctor->review_days-1;
             }
             else
             {
                $days = '6';
             }
    
            $start_date=$patientverification[0]->appointment_date;

            // echo $start_date;
            // exit();
            // $data['start_day']=$start_date;
            
            $checkDate = date("Y-m-d",strtotime($start_date."+".$days." day"));

            // echo $checkDate;
            // exit();
            // $data['checkDate']=$checkDate;
            // $data['oneday']=$oneday = date("Y-m-d",strtotime($start_date."+1 day"));
            // $data['twoday']=$twoday = date("Y-m-d",strtotime($checkDate."+1 day"));
            // $data['start_date']=$start_date;
            // $data['checkDate']=$checkDate;
            // $data['currentDate']=$currentDate = date("Y-m-d");
            $oneday = date("Y-m-d",strtotime($start_date."+1 day"));
            $twoday = date("Y-m-d",strtotime($checkDate."+1 day"));

            // echo $twoday;
            // exit();

            $getData =  $this->db->select("*")
            ->from("appointments")
            ->where("patient_id= '" .$patient_id. "' 
            and doctor_id='".$doctor_id."'
            and created_date_time >= '".$oneday."' and created_date_time < '".$twoday."'")
            ->get()->result();
        
            //  echo date("Y-m-d");
            //  echo $this->db->last_query();
            //  exit();

            if($checkDate<date("Y-m-d"))
            {
                $data['patient_details']['Message']="previous consultation period expired and amount should pay";
                $data['patient_details']['walkin_consultation_fee']=$clinic_doctor->consulting_fee;
                $data['patient_details']['online_consultation_fee']=$clinic_doctor->online_consulting_fee;
                $data['patient_details']['registration_fee']=0;
                $data['patient_details']['status']="1";
            }
            else
            {
                if(count($getData)>2)
                {
                    $data['patient_details']['Message']="only two free visits per consultation expires and amount should pay";
                    $data['patient_details']['walkin_consultation_fee']=$clinic_doctor->consulting_fee;
                    $data['patient_details']['online_consultation_fee']=$clinic_doctor->online_consulting_fee;
                    $data['patient_details']['registration_fee']=0;
                    $data['patient_details']['status']="2";
                }
                else
                {
                    $data['patient_details']['Message']="can check under previous consultation without payment";
                    $data['patient_details']['consultation_count']=count($getData);
                    $data['patient_details']['consultation_fee']=0;
                    $data['patient_details']['registration_fee']=0;
                    $data['patient_details']['status']="3";
                }

                // $getData =  $this->db->select("*")
                // ->from("appointments")
                // ->where("patient_id= '" . $patient_id . "' 
                // and doctor_id='".$doctor_id."'
                // and created_date_time BETWEEN '".$start_date."' AND '".$currentDate."'
                // ")
                // ->get()->result();


                // $data['patient_details']="can check under previous consultation";
                // $data['getDetails']=count($getData);
            }
        }
        else
        {
            // $clinic_doctor =  $this->db->select("*")
            // ->from("clinic_doctor")
            // ->where("doctor_id='".$doctor_id."'")
            // ->get()->row();

            $clinic_doctor =  $this->db->select("*")
            ->from("clinic_doctor")
            ->where("doctor_id='".$doctor_id."'")
            ->get()->result();
           
            $data['patient_details']['Message']="First Time Consulting";
            $data['patient_details']['walkin_consultation_fee']=$clinic_doctor[0]->consulting_fee;
            $data['patient_details']['online_consultation_fee']=$clinic_doctor[0]->online_consulting_fee;
            $data['patient_details']['registration_fee']=100;
            $data['patient_details']['status']="0";
        }
        $this->response(array('code'=>'200','message' =>'Success','result' => $data,'requestname'=> $method));
    }

    // public function checkPatientInformation($parameters,$method,$user_id)
    // {
    //     extract($parameters);
    //     // Patient Verification based on doctor id and clinic id
    //     $patientverification =  $this->db->select("*")
    //     ->from("appointments")
    //     ->where("patient_id= '" . $patient_id . "' and doctor_id='".$doctor_id."'")
    //     ->order_by("created_date_time","desc")
    //     ->get()->result();

    //     if(count($patientverification)>0)
    //     {
    //         $clinic_doctor =  $this->db->select("*")
    //         ->from("clinic_doctor")
    //         ->where("clinic_id= '" . $clinic_id . "' and doctor_id='".$doctor_id."'")
    //         ->get()->row();

    //          if($clinic_doctor->review_days != 0)
    //          {
    //              $days = $clinic_doctor->review_days-1;
    //          }
    //          else
    //          {
    //             $days = '6';
    //          }
    
    //         $start_date=$patientverification[0]->appointment_date;
    //         // $data['start_day']=$start_date;
            
    //         $checkDate = date("Y-m-d",strtotime($start_date."+".$days." day"));
    //         // $data['checkDate']=$checkDate;
    //         // $data['oneday']=$oneday = date("Y-m-d",strtotime($start_date."+1 day"));
    //         // $data['twoday']=$twoday = date("Y-m-d",strtotime($checkDate."+1 day"));
    //         // $data['start_date']=$start_date;
    //         // $data['checkDate']=$checkDate;
    //         // $data['currentDate']=$currentDate = date("Y-m-d");
    //         $oneday = date("Y-m-d",strtotime($start_date."+1 day"));
    //         $twoday = date("Y-m-d",strtotime($currentDate."+1 day"));

    //         $getData =  $this->db->select("*")
    //         ->from("appointments")
    //         ->where("patient_id= '" .$patient_id. "' 
    //         and doctor_id='".$doctor_id."'
    //         and created_date_time >= '".$oneday."' and created_date_time < '".$twoday."'")
    //         ->get()->result();

    //         if($checkDate<date("Y-m-d"))
    //         {
    //             $data['patient_details']['Message']="previous consultation period expired and amount should pay";
    //             $data['patient_details']['walkin_consultation_fee']=$clinic_doctor->consulting_fee;
    //             $data['patient_details']['online_consultation_fee']=$clinic_doctor->online_consulting_fee;
    //             $data['patient_details']['registration_fee']=0;
    //             $data['patient_details']['status']="1";
    //         }
    //         else
    //         {
    //             if(count($getData)>2)
    //             {
    //                 $data['patient_details']['Message']="only two free visits per consultation expires and amount should pay";
    //                 $data['patient_details']['walkin_consultation_fee']=$clinic_doctor->consulting_fee;
    //                 $data['patient_details']['online_consultation_fee']=$clinic_doctor->online_consulting_fee;
    //                 $data['patient_details']['registration_fee']=0;
    //                 $data['patient_details']['status']="2";
    //             }
    //             else
    //             {
    //                 $data['patient_details']['Message']="can check under previous consultation without payment";
    //                 $data['patient_details']['consultation_count']=count($getData);
    //                 $data['patient_details']['consultation_fee']=0;
    //                 $data['patient_details']['registration_fee']=0;
    //                 $data['patient_details']['status']="3";
    //             }

    //             // $getData =  $this->db->select("*")
    //             // ->from("appointments")
    //             // ->where("patient_id= '" . $patient_id . "' 
    //             // and doctor_id='".$doctor_id."'
    //             // and created_date_time BETWEEN '".$start_date."' AND '".$currentDate."'
    //             // ")
    //             // ->get()->result();


    //             // $data['patient_details']="can check under previous consultation";
    //             // $data['getDetails']=count($getData);
    //         }
    //     }
    //     else
    //     {
    //         $clinic_doctor =  $this->db->select("*")
    //         ->from("clinic_doctor")
    //         ->where("clinic_id= '" . $clinic_id . "' and doctor_id='".$doctor_id."'")
    //         ->get()->row();
           
    //         $data['patient_details']['Message']="First Time Consulting";
    //         $data['patient_details']['walkin_consultation_fee']=$clinic_doctor->consulting_fee;
    //         $data['patient_details']['online_consultation_fee']=$clinic_doctor->online_consulting_fee;
    //         $data['patient_details']['registration_fee']=100;
    //         $data['patient_details']['status']="0";
    //     }
    //     $this->response(array('code'=>'200','message' =>'Success','result' => $data,'requestname'=> $method));
    // }

    public function get_videocall_schedule($parameters, $method, $user_id) {

        $clinic_id = $parameters['clinic_id'];
        $doctor_id = $parameters['doctor_id'];
        $week_day = date('N', strtotime($date));

        // Today's date
        $date = date('Y-m-d'); 
        
        $weekOfdays = array();
        
        for ($i = 1; $i <= 7; $i++) {
            $date = date('Y-m-d', strtotime('+1 day', strtotime($date)));
            $weekOfdays[] = date('Y-m-d', strtotime($date));
        }
        
        $k = 0;

        foreach ($weekOfdays as $key => $value) {
            $date = date('Y-m-d', strtotime('-1 day', strtotime($value)));
            $wday = date('N', strtotime($date));
            $param['booked_slots'][$k]['date'] = $date;
            $bs = $this->db->select("*")->from("appointments")->where("clinic_id='".$clinic_id."' and doctor_id='".$doctor_id."' and appointment_date='".$date."' and slot_type='video call'")->get()->result();
            if (count($bs) > 0) {
                foreach ($bs as $bss) {
                    $b_slots[] = date('h:i A', strtotime($bss->appointment_time_slot));
                }
                $param['booked_slots'][$k]['time_slot'] = $b_slots;
            } else {
                $param['booked_slots'][$k]['time_slot'] = NULL;
            }
            $k++;
            $blocking = $this->db->select("*")->from("calendar_blocking")->where("clinic_id='" . $clinic_id . "' and doctor_id='" . $doctor_id . "'")->get()->row();

            $cal_dates = explode(",", $blocking->dates);
            $blocked_dates = array();
            foreach ($cal_dates as $key => $final) {

                if (in_array($final, $weekOfdays)) {
                    $blocked_dates[] = $final;
                }
            }
            $blocked_list = implode(',', $blocked_dates);
            $param['blocked_dates'] = $blocked_list;
        }

        // check if the param patient id exists
        if(isset($parameters['patient_id'])){
            $patient_id = $parameters['patient_id'];

            // get the list of open appointments i.e whose status is 'booked'
            $this->db->select('appointment_id, patient_id, doctor_id, appointment_date, appointment_time_slot');
            $this->db->from('appointments');
            $this->db->where('slot_type=','video call');
            $this->db->where('status=','booked');
            $this->db->where('doctor_id=',$doctor_id);
            $this->db->where('patient_id=',$patient_id);
            $booked_list = $this->db->get()->result();

            if(count($booked_list) > 0){
                $x = 0;    
                foreach($booked_list as $booked){
                    $param['booked_appointments'][$x] = $booked;
                    $x++;
                }
            }else{
                $param['booked_appointments'] = $booked_list;
            }           
        }

        $this->response(array('code' => '200', 'message' => 'Doctor Time Slots', 'result' => $param, 'requestname' => $method));
    }

    public function generateRandomString($length = 8) {

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $charactersLength = strlen($characters);

        $randomString = '';

        for ($i = 0; $i < $length; $i++) {

            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }


    public function checkPatientByMobile($parameters, $method, $user_id)
    {
        extract($parameters);

        // $clinic_id = $parameters['clinic_id'];
        // $doctor_id = $parameters['doctor_id'];
        // $umr_no = $parameters['umr_no'];
        $patientChk = $this->db->select("*")->from("patients")->where("mobile =",DataCrypt($mobile,'encrypt'))->get()->row();

        // $patientChk = $this->db->select("*")->from("patients")->where("mobile =",DataCrypt($mobile,'encrypt'))->get()->row();

        $clist =  $this->db->select("*")
        ->from("appointments")
        ->where("
         doctor_id='".$doctor_id."' 
         and patient_id='".$patientChk->patient_id."'")
        ->order_by("created_date_time","desc")
        ->get()
        ->result();

        if(count($clist)>0)
        {
            $clinic_doctor =  $this->db->select("*")
            ->from("clinic_doctor")
            ->where("clinic_id= '" . $clist->clinic_id . "' and doctor_id='".$doctor_id."'")
            ->get()->row();

             if($clinic_doctor->review_days != 0)
             {
                 $days = $clinic_doctor->review_days-1;
             }
             else
             {
                $days = '6';
             }
    
            $start_date=$clist[0]->appointment_date;
            $patient_id=$clist[0]->patient_id;
            // $data['start_day']=$start_date;
            
            $checkDate = date("Y-m-d",strtotime($start_date."+".$days." day"));
            // $data['checkDate']=$checkDate;
            // // $data['oneday']=$oneday = date("Y-m-d",strtotime($start_date."+1 day"));
            // // $data['twoday']=$twoday = date("Y-m-d",strtotime($checkDate."+1 day"));
            // $data['start_date']=$start_date;
            // $data['checkDate']=$checkDate;
            // $data['currentDate']=$currentDate = date("Y-m-d");
            $oneday = date("Y-m-d",strtotime($start_date."+1 day"));
            $twoday = date("Y-m-d",strtotime($currentDate."+1 day"));

            $getData =  $this->db->select("*")
            ->from("appointments")
            ->where("patient_id= '" .$patientChk->patient_id. "' 
            and doctor_id='".$doctor_id."'
            and created_date_time >= '".$oneday."' and created_date_time < '".$twoday."'")
            ->get()->result();

            
            $getPatientDetails = $this->db->select("*")
            ->from("patients")
            ->where("patient_id= '" .$patientChk->patient_id. "'")
            ->get()->row();

            if($checkDate<date("Y-m-d"))
            {
                $data['patient_details']['patient_id']=  $getPatientDetails->patient_id;
                $data['patient_details']['patient_name']=  $getPatientDetails->title." ".$getPatientDetails->first_name." ".$getPatientDetails->last_name;
                $data['patient_details']['umr_no']=  $getPatientDetails->umr_no;
                $data['patient_details']['Message']="previous consultation period expired and amount should pay";
                $data['patient_details']['walkin_consultation_fee']=$clinic_doctor->consulting_fee;
                $data['patient_details']['online_consultation_fee']=$clinic_doctor->online_consulting_fee;
                $data['patient_details']['registration_fee']=0;
                $data['patient_details']['status']="1";
                $data['patient_details']['patient_details_status']="1";
            }
            else
            {
                if(count($getData)>2)
                {
                    $data['patient_details']['patient_id']=  $getPatientDetails->patient_id;
                    $data['patient_details']['patient_name']=  $getPatientDetails->title." ".$getPatientDetails->first_name." ".$getPatientDetails->last_name;
                    $data['patient_details']['umr_no']=  $getPatientDetails->umr_no;
                    $data['patient_details']['Message']="only two free visits per consultation expires and amount should pay";
                    $data['patient_details']['walkin_consultation_fee']=$clinic_doctor->consulting_fee;
                    $data['patient_details']['online_consultation_fee']=$clinic_doctor->online_consulting_fee;
                    $data['patient_details']['registration_fee']=0;
                    $data['patient_details']['status']="2";
                    $data['patient_details']['patient_details_status']="1";
                }
                else
                {
                    $data['patient_details']['patient_id']=  $getPatientDetails->patient_id;
                    $data['patient_details']['patient_name']=  $getPatientDetails->title." ".$getPatientDetails->first_name." ".$getPatientDetails->last_name;
                    $data['patient_details']['umr_no']=  $getPatientDetails->umr_no;
                    $data['patient_details']['Message']="can check under previous consultation without payment";
                    $data['patient_details']['consultation_count']=count($getData);
                    $data['patient_details']['consultation_fee']=0;
                    $data['patient_details']['registration_fee']=0;
                    $data['patient_details']['status']="3";
                    $data['patient_details']['patient_details_status']="1";
                }

                // $getData =  $this->db->select("*")
                // ->from("appointments")
                // ->where("patient_id= '" . $patient_id . "' 
                // and doctor_id='".$doctor_id."'
                // and created_date_time BETWEEN '".$start_date."' AND '".$currentDate."'
                // ")
                // ->get()->result();


                // $data['patient_details']="can check under previous consultation";
                // $data['getDetails']=count($getData);
            }
 
        }
        else
        {

            $data['patient_details']['Message']="Patient Id Not Found";
            $data['patient_details']['patient_details_status']="0";
            // $clinic_doctor =  $this->db->select("*")
            // ->from("clinic_doctor")
            // ->where("clinic_id= '" . $clinic_id . "' and doctor_id='".$doctor_id."'")
            // ->get()->row();
           
            // $data['patient_details']['Message']="First Time Consulting";
            // $data['patient_details']['walkin_consultation_fee']=$clinic_doctor->consulting_fee;
            // $data['patient_details']['online_consultation_fee']=$clinic_doctor->online_consulting_fee;
            // $data['patient_details']['registration_fee']=100;
            // $data['patient_details']['status']="0";
        }
        $this->response(array('code'=>'200','message' =>'Success','result' => $data,'requestname'=> $method));

        // $this->response(array('code' => '200', 'message' => 'Doctor Time Slots', 'result' => $param, 'requestname' => $method));
    } 

    public function checkPatientUmrNo($parameters, $method, $user_id)
    {
        extract($parameters);

        // $clinic_id = $parameters['clinic_id'];
        // $doctor_id = $parameters['doctor_id'];
        // $umr_no = $parameters['umr_no'];

        $clist =  $this->db->select("*")
        ->from("appointments")
        ->where("doctor_id='".$doctor_id."' 
         and umr_no='".$umr_no."'")
        ->order_by("created_date_time","desc")
        ->get()
        ->result();

        // echo $this->db->last_query();
        // exit();

        if(count($clist)>0)
        {
            $clinic_doctor =  $this->db->select("*")
            ->from("clinic_doctor")
            ->where("clinic_id= '" . $clist[0]->clinic_id . "' and doctor_id='".$doctor_id."'")
            ->get()->row();

             if($clinic_doctor->review_days != 0)
             {
                 $days = $clinic_doctor->review_days-1;
             }
             else
             {
                $days = '6';
             }
    
            $start_date=$clist[0]->appointment_date;
            $patient_id=$clist[0]->patient_id;
            // $data['start_day']=$start_date;
            
            $checkDate = date("Y-m-d",strtotime($start_date."+".$days." day"));
            // $data['checkDate']=$checkDate;
            // // $data['oneday']=$oneday = date("Y-m-d",strtotime($start_date."+1 day"));
            // // $data['twoday']=$twoday = date("Y-m-d",strtotime($checkDate."+1 day"));
            // $data['start_date']=$start_date;
            // $data['checkDate']=$checkDate;
            // $data['currentDate']=$currentDate = date("Y-m-d");
            $oneday = date("Y-m-d",strtotime($start_date."+1 day"));
            $twoday = date("Y-m-d",strtotime($currentDate."+1 day"));

            $getData =  $this->db->select("*")
            ->from("appointments")
            ->where("patient_id= '" .$patient_id. "' 
            and doctor_id='".$doctor_id."'
            and created_date_time >= '".$oneday."' and created_date_time < '".$twoday."'")
            ->get()->result();

            $getPatientDetails = $this->db->select("*")
            ->from("patients")
            ->where("patient_id= '" .$patient_id. "'")
            ->get()->row();

            if($checkDate<date("Y-m-d"))
            {
                $data['patient_details']['patient_id']=  $getPatientDetails->patient_id;
                $data['patient_details']['patient_name']=  $getPatientDetails->title." ".$getPatientDetails->first_name." ".$getPatientDetails->last_name;
                $data['patient_details']['umr_no']=  $getPatientDetails->umr_no;
                $data['patient_details']['Message']="previous consultation period expired and amount should pay";
                $data['patient_details']['walkin_consultation_fee']=$clinic_doctor->consulting_fee;
                $data['patient_details']['online_consultation_fee']=$clinic_doctor->online_consulting_fee;
                $data['patient_details']['registration_fee']=0;
                $data['patient_details']['status']="1";
                $data['patient_details']['patient_details_status']="1";
            }
            else
            {
                if(count($getData)>2)
                {
                    $data['patient_details']['patient_id']=  $getPatientDetails->patient_id;
                    $data['patient_details']['patient_name']=  $getPatientDetails->title." ".$getPatientDetails->first_name." ".$getPatientDetails->last_name;
                    $data['patient_details']['umr_no']=  $getPatientDetails->umr_no;
                    $data['patient_details']['Message']="only two free visits per consultation expires and amount should pay";
                    $data['patient_details']['walkin_consultation_fee']=$clinic_doctor->consulting_fee;
                    $data['patient_details']['online_consultation_fee']=$clinic_doctor->online_consulting_fee;
                    $data['patient_details']['registration_fee']=0;
                    $data['patient_details']['status']="2";
                    $data['patient_details']['patient_details_status']="1";
                }
                else
                {
                    $data['patient_details']['patient_id']=  $getPatientDetails->patient_id;
                    $data['patient_details']['patient_name']=  $getPatientDetails->title." ".$getPatientDetails->first_name." ".$getPatientDetails->last_name;
                    $data['patient_details']['umr_no']=  $getPatientDetails->umr_no;
                    $data['patient_details']['Message']="can check under previous consultation without payment";
                    $data['patient_details']['consultation_count']=count($getData);
                    $data['patient_details']['consultation_fee']=0;
                    $data['patient_details']['registration_fee']=0;
                    $data['patient_details']['status']="3";
                    $data['patient_details']['patient_details_status']="1";
                }

                // $getData =  $this->db->select("*")
                // ->from("appointments")
                // ->where("patient_id= '" . $patient_id . "' 
                // and doctor_id='".$doctor_id."'
                // and created_date_time BETWEEN '".$start_date."' AND '".$currentDate."'
                // ")
                // ->get()->result();


                // $data['patient_details']="can check under previous consultation";
                // $data['getDetails']=count($getData);
            }
        }
        else
        {

            $data['patient_details']['Message']="Patient Id Not Found";
            $data['patient_details']['patient_details_status']="0";
            // $clinic_doctor =  $this->db->select("*")
            // ->from("clinic_doctor")
            // ->where("clinic_id= '" . $clinic_id . "' and doctor_id='".$doctor_id."'")
            // ->get()->row();
           
            // $data['patient_details']['Message']="First Time Consulting";
            // $data['patient_details']['walkin_consultation_fee']=$clinic_doctor->consulting_fee;
            // $data['patient_details']['online_consultation_fee']=$clinic_doctor->online_consulting_fee;
            // $data['patient_details']['registration_fee']=100;
            // $data['patient_details']['status']="0";
        }
        $this->response(array('code'=>'200','message' =>'Success','result' => $data,'requestname'=> $method));

        // $this->response(array('code' => '200', 'message' => 'Doctor Time Slots', 'result' => $param, 'requestname' => $method));
    } 

    public function get_schedule_video($parameters, $method, $user_id) {

        $clinic_id = $parameters['clinic_id'];
        $doctor_id = $parameters['doctor_id'];
        $week_day = date('N', strtotime($date));
    
        // Today's date
        $date = date('Y-m-d'); 
        
        $weekOfdays = array();
        
        for ($i = 1; $i <= 7; $i++) {
            $date = date('Y-m-d', strtotime('+1 day', strtotime($date)));
            $weekOfdays[] = date('Y-m-d', strtotime($date));
        }
        
        $k = 0;
    
        foreach ($weekOfdays as $key => $value) {
            $date = date('Y-m-d', strtotime('-1 day', strtotime($value)));
            $wday = date('N', strtotime($date));
            $param['booked_slots'][$k]['date'] = $date;
            $bs = $this->db->select("*")->from("appointments")->where("doctor_id='".$doctor_id."' and appointment_date='".$date."' and slot_type='video call'")->get()->result();
            if (count($bs) > 0) {
                $b_slots =[];
                foreach ($bs as $bss) {
                    $b_slots[] = date('h:i A', strtotime($bss->appointment_time_slot));
                }
                $param['booked_slots'][$k]['time_slot'] = $b_slots;
            } else {
                $param['booked_slots'][$k]['time_slot'] = NULL;
            }
            $k++;
            $blocking = $this->db->select("*")->from("calendar_blocking")->where("clinic_id='" . $clinic_id . "' and doctor_id='" . $doctor_id . "'")->get()->row();
    
            $cal_dates = explode(",", $blocking->dates);
            $blocked_dates = array();
            foreach ($cal_dates as $key => $final) {
    
                if (in_array($final, $weekOfdays)) {
                    $blocked_dates[] = $final;
                }
            }
            $blocked_list = implode(',', $blocked_dates);
            $param['blocked_dates'] = $blocked_list;
        }
    
        // check if the param patient id exists
        if(isset($parameters['patient_id'])){
            $patient_id = $parameters['patient_id'];
    
            // get the list of open appointments i.e whose status is 'booked'
            $this->db->select('appointment_id, patient_id, doctor_id, appointment_date, appointment_time_slot');
            $this->db->from('appointments');
            $this->db->where('status=','booked');
            $this->db->where('doctor_id=',$doctor_id);
            $this->db->where('patient_id=',$patient_id);
            $this->db->where('slot_type=','video call');
            $booked_list = $this->db->get()->result();
    
            if(count($booked_list) > 0){
                $x = 0;    
                foreach($booked_list as $booked){
                    $param['booked_appointments'][$x] = $booked;
                    $x++;
                }
            }else{
                $param['booked_appointments'] = $booked_list;
            }           
        }
    
        $this->response(array('code' => '200', 'message' => 'Doctor Time Slots', 'result' => $param, 'requestname' => $method));
    }
    
    // get upcoming Appointments 
    public function getUpcomingAppointments($parameters, $method, $user_id){
        extract($parameters);
        // $today = date("Y-m-d");
        $datetime = date("Y-m-d H:i:s");
        // and CONCAT(a.appointment_date, ' ', a.appointment_time_slot)  > '".$datetime."'
        $check = $this->db->query("select a.*,p.title,p.title,p.first_name,p.last_name,p.umr_no,p.patient_id from appointments a,patients p where a.patient_id=p.patient_id and a.patient_id='".$patient_id."' and a.status!='closed'")->result();
        // echo $this->db->last_query();
        if(count($check) > 0)
        {
            $i = 0;
            foreach($check as $cform)
            {
                $docInfo = $this->db->query("select * from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$cform->doctor_id."'")->row();
                $clinicInfo = $this->db->query("select * from clinics where clinic_id='".$cform->clinic_id."'")->row();
                $para['patient_details_info'][$i]['patient_name'] = $cform->title.". ".$cform->first_name." ".$cform->last_name;
               
                $para['patient_details_info'][$i]['umr_no'] = $cform->umr_no;
                $para['patient_details_info'][$i]['patient_gender'] = $cform->gender;
                $para['patient_details_info'][$i]['patient_age'] = $cform->age;
                $para['patient_details_info'][$i]['clinic_name']=$clinicInfo->clinic_name;
                $para['patient_details_info'][$i]['doctor_name'] = "Dr. " . strtoupper($docInfo->first_name . ' ' . $docInfo->last_name);
                $para['patient_details_info'][$i]['doctor_department']=$docInfo->department_name;
                $para['patient_details_info'][$i]['appointment_id'] = $cform->appointment_id;
                $para['patient_details_info'][$i]['clinic_location']=$clinicInfo->location;
                $para['patient_details_info'][$i]['appointment_time_slot'] = $cform->appointment_time_slot;
                $para['patient_details_info'][$i]['appointment_date'] = $cform->appointment_date;
                $i++;
            }
            $this->response(array('code'=>'200','message'=>'Success','result'=>$para,'method'=>$method));
        }
        else
        {
            $para['patient_details_info'] = [];
            $this->response(array('code'=>'201','message'=>'No Data Found.','result'=>$data,'method'=>$method));
        }
    }
    
    // get Previous Appointments including present appointments
    public function getPreviousAppointments($parameters, $method, $user_id){
        extract($parameters);
        // $today = date('Y-m-d');
        $datetime = date("Y-m-d H:i:s");
        // and CONCAT(a.appointment_date, ' ', a.appointment_time_slot) < '".$datetime."'
        $check = $this->db->query("select a.*,p.title,p.title,p.first_name,p.last_name,p.umr_no,p.patient_id from appointments a,patients p where a.patient_id=p.patient_id and a.patient_id='".$patient_id."' and a.status='closed'")->result();
        // echo $this->db->last_query();
        if(count($check) > 0)
        {
            $i = 0;
            foreach($check as $cform)
            {
                $docInfo = $this->db->query("select * from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$cform->doctor_id."'")->row();
                $clinicInfo = $this->db->query("select * from clinics where clinic_id='".$cform->clinic_id."'")->row();
                $para['patient_details_info'][$i]['patient_name'] = $cform->title.". ".$cform->first_name." ".$cform->last_name;
               
                $para['patient_details_info'][$i]['umr_no'] = $cform->umr_no;
                $para['patient_details_info'][$i]['patient_gender'] = $cform->gender;
                $para['patient_details_info'][$i]['patient_age'] = $cform->age;
                $para['patient_details_info'][$i]['clinic_name']=$clinicInfo->clinic_name;
                $para['patient_details_info'][$i]['doctor_name'] = "Dr. " . strtoupper($docInfo->first_name . ' ' . $docInfo->last_name);
                $para['patient_details_info'][$i]['doctor_department']=$docInfo->department_name;
                $para['patient_details_info'][$i]['appointment_id'] = $cform->appointment_id;
                $para['patient_details_info'][$i]['clinic_location']=$clinicInfo->location;
                $para['patient_details_info'][$i]['appointment_time_slot'] = $cform->appointment_time_slot;
                $para['patient_details_info'][$i]['appointment_date'] = $cform->appointment_date;
                $i++;
            }
            $this->response(array('code'=>'200','message'=>'Success','result'=>$para,'method'=>$method));
        }
        else
        {
            $para['patient_details_info'] = [];
            $this->response(array('code'=>'201','message'=>'No Data Found.','result'=>$data,'method'=>$method));
        }
    }

    // Search Departments & Doctors
    public function SearchDepts($parameters, $method, $user_id){
        extract($parameters);
        // Departments
        $deptInfo = $this->db->query("select * from department where department_name LIKE '%".urldecode($search)."%'")->result();
        if(count($deptInfo) > 0){
            $i = 0;
            foreach($deptInfo as $value){
                $data['search'][$i]['type'] = "DepartmentCard";
                $data['search'][$i]['department_id'] = $value->department_id;
                $data['search'][$i]['department_name'] = $value->department_name;
                $i++;
            }
        }

        // Doctors
        echo $i;
        $docInfo = $this->db->query("select * from doctors d,department de where d.department_id=de.department_id and CONCAT(first_name,' ',last_name) LIKE '%".urldecode($search)."%'")->result();
        if(count($deptInfo) > 0){
            foreach($docInfo as $value){
                // $data['search'][$i]
            }
        }

        $this->response($data);
    }

    // add patient family member this will creates as a new patient under the existing patient
    public function addFamilyPatient($parameters, $method, $user_id){
        extract($parameters);
        // if()
    }

    public function departSearch($parameters, $method, $user_id)
    {
        extract($parameters);

        $deptInfo = $this->db->query("select department_id from department where department_name LIKE '%".urldecode($search)."%'")->result();
        // echo $this->db->last_query();
        $symptomInfo =$this->db->query("select department_id from doctors where diseases_dealt LIKE '%".urldecode($search)."%'")->result();


        // echo $this->db->last_query();
        // exit();
        if(count($deptInfo) > 0 || count($symptomInfo) > 0){
            if(count($deptInfo) > 0){
                $i = 0;
                foreach($deptInfo as $value){
                    $dept_details = $this->db->query("select * from department where department_id='".$value->department_id."'")->row();
                    if($value->department_icon == "")
                    {
                         $src = "dummyDEPT.png";
                    }
                    else
                    {
                         $src = $value->department_icon;
                    }
                    // $para['departments'][$i]['department_id'] = $value->department_id;
                    // $para['departments'][$i]['department_name'] = $value->department_name;
                    // $para['departments'][$i]['department_icon'] = base_url('uploads/departments/'.$src);

                    $data['departments'][$i]['department_id'] = $dept_details->department_id;
                    $data['departments'][$i]['department_name'] = $dept_details->department_name;
                    $data['departments'][$i]['department_icon'] = base_url('uploads/departments/'.$src);
                    $i++;
                }
                // $this->response(array('code'=>'200','message'=>'Success','result'=>$data,'method'=>$method));
            }
    
            if(count($symptomInfo) > 0){
                $a = 0;
                foreach($symptomInfo as $value){
                    $dept_details = $this->db->query("select * from department where department_id='".$value->department_id."'")->row();
                    if($value->department_icon == "")
                    {
                         $src = "dummyDEPT.png";
                    }
                    else
                    {
                         $src = $value->department_icon;
                    }
                    $data['departments'][$a]['department_id'] = $dept_details->department_id;
                    $data['departments'][$a]['department_name'] = $dept_details->department_name;
                    $data['departments'][$a]['department_icon'] = base_url('uploads/departments/'.$src);
                    $a++;
                }
                
            }
            $this->response(array('code'=>'200','message'=>'Success','result'=>$data,'method'=>$method));
        }
        else{
            $data['search']=[];
            $this->response(array('code'=>'200','message'=>'Error','result'=>$data,'method'=>$method));
        }
   
    }

}

?>