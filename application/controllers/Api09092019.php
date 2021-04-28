<?php

defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
ini_set('memory_limit', '-1');
require APPPATH . '/libraries/REST_Controller.php';

class Api extends REST_Controller {

    public function __construct() {

        parent::__construct();
        $this->load->helper('file');
        $this->load->library('PHPMailer');
        $this->load->library('mail_send', array('mailtype' => 'html'));
        $this->load->library('SMTP');
        $this->load->library('phpqrcode/qrlib');
        $this->load->library('zip');
        $this->load->model('Generic_model');

        // Check multipart service parameter 'requestname' has got data/NO
        if ($this->post('requestpara') != NULL || $this->post('requestpara') != '') {

            $fdata = json_decode($this->post('requestpara'));
            $this->load->library('upload');
            $config = array();

            // Setting upload path for multipart data service
            if ($fdata->requestname == 'patient_registration') {
                $config['upload_path'] = './uploads/patients';
            } else if ($fdata->requestname == 'patient_consent_form') {
                $config['upload_path'] = './uploads/patient_consentforms';
            } else if ($fdata->requestname == 'patient_update') {
                $config['upload_path'] = './uploads/patients';
            } else if ($fdata->requestname == 'patient_profile_edit') {
                $config['upload_path'] = './uploads/patients';
            } else if ($fdata->requestname == 'doctor_profile_edit') {
                $config['upload_path'] = './uploads/doctors';
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
            $requesterid = $fdata->requesterid;

            if ($requestname == "patient_registration") {

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

                $newPatient = 0; // Means that its not a new patient and so its a followup patient
                
                if($mobile != '' || $mobile != NULL){
                    // Check if the record is present in them db with mobile number and with the name
                    $patientChk = $this->db->select("patient_id, first_name, last_name, mobile, alternate_mobile")->from("patients")->where("mobile =",$mobile)->get()->row();

                    if(count($patientChk) > 0){
                        /*
                        Senerio: 1
                        1. Mobile No. exists
                        2. Check if the person is same
                        */
                        $patientName = $first_name;

                        if(ucwords($patientName) == ucwords($patientChk->first_name)){
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
                            $mobileRecChk = $this->db->select("patient_id, first_name, last_name, mobile, alternate_mobile, guardian_id")->from("patients")->where("first_name =",$first_name)->where("alternate_mobile =",$mobile)->get()->result_array();

                            if(count($mobileRecChk) > 0){
                                /*
                                5. Person exist with mobile saved in alternate mobile
                                4. No Registration required Send to book appointment   
                                */
                                $params['alternate_mobile'] = $mobile;
                                $params['mobile'] = '';
                                $params['guardian_id'] = $mobileRecChk[0]['guardian_id'];
                                $params['patient_id'] = $mobileRecChk[0]['patient_id'];
                                $newPatient = 0;    
                            }else{
                                /*
                                5. No Person exist with alternate mobile
                                4. New Registration required
                                */
                                $params['alternate_mobile'] = $mobile;
                                $params['mobile'] = '';
                                $params['guardian_id'] = $mobileRecChk[0]['patient_id'];
                                $newPatient = 1;    
                            }
                        }
                    }else{
                        // Resgiter as a new patient
                        $newPatient = 1;
                    }
                }


                if($newPatient == 0){

                    // Get Patient Complete Details
                    $patientRec = $this->db->select('patient_id,title,first_name,last_name,umr_no, date_of_birth, age, email_id,payment_status,clinic_id,referred_by_type,referred_by, referral_doctor_id,country,occupation,mobile,alternate_mobile,age,gender,location,state_id,district_id,preferred_language, address_line, pincode, photo, qrcode, created_date_time, status')->from('patients')->where('patient_id ='.$params['patient_id'])->get()->row(); 

                    if(count($patientRec) > 0) {
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
                        $patientRec->doctor_id = $patientRec->district_id;
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
                    // Get last generated UMR No.                
                    $last_umr = $this->db->select("umr_no")->from("patients")->order_by("patient_id DESC")->get()->row();

                    // Generate UMR No.
                    if(count($last_umr) > 0){
                        $umr_str   = trim($last_umr->umr_no);
                        $split_umr = substr($umr_str, 1, 4);
                        if ($split_umr == date("my")) {
                            $replace = str_replace("P" . $split_umr, "", $last_umr->umr_no);
                            $next_id = (++$replace);
                            $umr_no  = "P" . date("my") . $next_id;
                        } else {
                            $umr_no = "P" . date("my") . "1";
                        }  
                    }else{
                        // No records found. Generate New UMR#
                        $umr_no = "P" . date("my") . "1";
                    }
                    
                    $tempDir = './uploads/qrcodes/patients/';
                    $codeContents = $umr_no;
                    $qrname = $umr_no.md5($codeContents).'.png';
                    $pngAbsoluteFilePath = $tempDir . $qrname;
                    $urlRelativeFilePath = base_url().'uploads/qrcodes/patients/'.$qrname;

                    if (!file_exists($pngAbsoluteFilePath)) {
                        QRcode::png($codeContents, $pngAbsoluteFilePath);
                    }

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
                    $params['username'] = $umr_no;
                    $params['password'] = md5($pwd);
                    $params['umr_no'] = $umr_no;

                    if($params['date_of_birth'] == '' || $params['date_of_birth'] == NULL){
                    	$params['date_of_birth'] = '';
                    }else{
                    	$params['date_of_birth'] = date('Y-m-d', strtotime($params['date_of_birth'])); 
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

                    $clinic_info = $this->db->select('clinic_id, clinic_name')->from('clinics')->where('clinic_id =',$params['clinic_id'])->get()->result_array();

                    /* Sending mail after successfull Registration of the patient */
                    if($params['email_id'] != '' || $params['email_id'] != NULL){
                        $from = 'UMDAA';
                        $to = $params['email_id'];
                        $subject = "Successfully Registered With Umdaa";
                        $header = "Dear " . ucwords($params['first_name'].' '.$params['last_name']).",<br><br>
                        Thank you for registering with " .ucwords($clinic_info->clinic_name). "<br><br>Have a good day";
                        
                        $this->mail_send->Content_send_all_mail($from, $to, $subject, '', '', $header);
                    }
                    /* End Of sending Mail */

                    // Remove username & password
                    unset($param['username']);
                    unset($params['password']);

                    $patientRec = '';

                    // Get Patient Complete Details
                    $patientRec = $this->db->select('patient_id,title,first_name,last_name,umr_no, date_of_birth, age, email_id as email,payment_status,clinic_id,referred_by_type,referred_by,referral_doctor_id,country,occupation,mobile,alternate_mobile,age,gender,location,state_id,district_id,preferred_language, address_line, pincode, photo, qrcode, created_date_time as registration_date, status')->from('patients')->where('patient_id ='.$params['patient_id'])->get()->row(); 

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
                        $patientRec->doctor_id = $patientRec->district_id;
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
                    }

                    $result = array('code' => '200', 'message' => 'successfull', 'result' => $patientRec, 'requestname' => $requestname);     
                }

            } else if ($requestname == "patient_profile_edit") {

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

                $patient_id = $fdata->patient_id;

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
                $patient_update['mobile'] = $fdata->mobile;
                $patient_update['alternate_mobile'] = $fdata->alternate_mobile;
                $patient_update['email_id'] = $fdata->email_id;
                $patient_update['address_line'] = $fdata->address_line;
                $patient_update['location'] = $fdata->location;
                $patient_update['district_id'] = $fdata->district_id;
                $patient_update['state_id'] = $fdata->state_id;
                $patient_update['pincode'] = $fdata->pincode;
                $patient_update['referred_by_type'] = $fdata->referred_by_type;

                // If referred by type is a Doctor
                // Check if the referral doctor object
                if(isset($fdata->referral_doctor)){
                    // if you are here then its a new referral doctor which needs to be created with respect to the clinic
                    $params['referral_doctor'] = $fdata->referral_doctor;
                    $params['referral_doctor']->clinic_id = $fdata->clinic_id;
                    $params['referral_doctor']->status = 1;
                    $params['referral_doctor']->created_by = $requesterid;
                    $params['referral_doctor']->modified_by = $requesterid;
                    $params['referral_doctor']->created_datetime = date('Y-m-d H:i:s');
                    $params['referral_doctor']->modified_datetime = date('Y-m-d H:i:s');

                    // Create a new referral doctor
                    $patient_update['referral_doctor_id'] = $this->Generic_model->insertDataReturnId('referral_doctors',$params['referral_doctor']);
                    $patient_update['referred_by'] = $params['referral_doctor'];
                }else{
                	// If you are here then it means the referral doctor already exists and you are updateing with his name for referred_by
                    $patient_update['referral_doctor_id'] = $fdata->referred_by;

                    // Get the name of the doctor
                    $this->db->select('doctor_name');
                    $this->db->from('referral_doctors');
                    $this->db->where('rfd_id =',$fdata->referred_by);
                    $this->db->where('clinic_id =',$fdata->clinic_id);

                    $referralDocRec = $this->db->get()->row();

                    $patient_update['referred_by'] = $referralDocRec->doctor_name;
                }

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
            } else if ($requestname == "clincal_dianosis_image") {

                $this->load->library('upload');
                $config['upload_path'] = './uploads/clinical_diagnosis/';
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

                // Check if the clinical diagnosis record exists with appointment id
                $check_exists = $this->db->select('patient_clinical_diagnosis_id')->from('patient_clinical_diagnosis')->where('appointment_id =',$fdata->appointment_id)->get()->row();

                if(count($check_exists) > 0){
                    // Exist then just update the image data with new one
                    $data['images'] = $fileData;
                    $res['patient_clinical_diagnosis'] = $this->Generic_model->updateData('patient_clinical_diagnosis', $data, array('patient_clinical_diagnosis_id' => $check_exists->patient_clinical_diagnosis_id));
                }else{
                    // New record entry for clinical diagnosis
                    $data['appointment_id'] = $fdata->appointment_id;
                    $data['patient_id'] = $fdata->patient_id;
                    $data['clinic_id'] = $fdata->clinic_id;
                    $data['doctor_id'] = $fdata->requesterid;
                    $data['images'] = $fileData;
                    $data['created_by'] = $fdata->requesterid;
                    $data['modified_by'] = $fdata->requesterid;
                    $data['created_date_time'] = date('Y-m-d H:i:s');
                    $data['modified_date_time'] = date('Y-m-d H:i:s');

                    $res['patient_clinical_diagnosis_id'] = $this->Generic_model->insertDataReturnId('patient_clinical_diagnosis', $data);
                }

                $result = array('code' => '200', 'message' => 'Clinical Diagnosis Image Saved Successfully', 'result' => $res, 'requestname' => 'clincal_dianosis_image');

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

            } else if ($requestname == "doctor_profile_edit") {

                //Doctor Update
                $config['upload_path'] = './uploads/doctors';

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

                $doctor_id = $fdata->requesterid;

                $doctor_exist_image = $this->db->select('profile_image')->from('doctors')->where('doctor_id =',$doctor_id)->get()->row();

                if ($doctor_exist_image->profile_image != NULL || $doctor_exist_image->profile_image != "") {
                    if ($fileName != NULL || $fileName != '') {
                        $doctor_update['profile_image'] = $fileName;
                    } else {
                        $doctor_update['profile_image'] = $doctor_exist_image->profile_image;
                    }
                } else {
                    $doctor_update['profile_image'] = $fileName;
                }

                $doctor_update['first_name'] = $fdata->first_name;
                $doctor_update['last_name'] = $fdata->last_name;
                $doctor_update['email'] = $fdata->email;
                $doctor_update['department_id'] = $fdata->department_id;
                $doctor_update['mobile'] = $fdata->mobile;
                $doctor_update['qualification'] = $fdata->qualification;
                $doctor_update['registration_code'] = $fdata->registration_code;                
                $doctor_update['modified_by'] = $doctor_id;
                $doctor_update['modified_date_time'] = date('Y-m-d H:i:s');

                // Update the patient details
                $this->Generic_model->updateData('doctors', $doctor_update, array('doctor_id' => $doctor_id));

                $doctor_update['profile_image'] = base_url().'uploads/doctors/'.$doctor_update['profile_image'];

                $result = array('code' => '200', 'message' => 'successfull', 'result' => $doctor_update, 'requestname' => $fdata->requestname);

            } else {
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


    public function clinic_master($parameters, $method, $user_id){
    	// Get all clinic names and id
    	$this->db->select("clinic_id, clinic_name, location, CONCAT(clinic_name,' - ',location) as clinic");
    	$this->db->from('clinics');
    	$clinics['clinics'] = $this->db->get()->result();

        $this->db->select("department_id, department_name");
        $this->db->from('department');
        $clinics['departments'] = $this->db->get()->result();

        $this->response(array('code' => '200', 'message' => 'success ', 'result' => $clinics, 'requestname' => $method));
    }


    public function doctor_registration($parameters, $method, $user_id){
        
        extract($parameters);

        // Check if the doctor exists
        $docCount = $this->db->select('doctor_id, mobile')->from('doctors')->where('mobile =',$mobile)->get()->num_rows();

        if($docCount == 0){

            if($clinic_id == 0){
                // New clinic, create it and then get the clinic_id
                $clinicData['clinic_name'] = $clinic_name;
                $clinicData['location'] = $location;
                $parameters['clinic_id'] = $this->Generic_model->insertDataReturnId('clinics', $clinicData);
            }

            // Get the role_id for Doctor
            $roleInfo = $this->db->select('role_id')->from('roles')->where('role_name =','Doctor')->get()->row();

            // Get the profile id for Doctor
            $profileInfo = $this->db->select('profile_id')->from('profiles')->where('profile_name =','Doctor')->get()->row();

            // Create doctor credentials
            $userData['clinic_id'] = $parameters['clinic_id'];
            $userData['username'] = $registration_code;
            $userData['password'] = md5($password);
            $userData['email_id'] = $email;
            $userData['mobile'] = $mobile;
            $userData['user_type'] = 'doctor';
            $userData['role_id'] = $roleInfo->role_id;
            $userData['profile_id'] = $profileInfo->profile_id;

            // Create user & get credentials
            $user_id = $this->Generic_model->insertDataReturnId('users',$userData);
            $doctor_id = $user_id;

            $doctorData = $parameters;
            $doctorData['doctor_id'] = $user_id;
            $doctorData['salutation'] = 'Dr';
            unset($doctorData['clinic_id']);
            unset($doctorData['clinic_name']);
            unset($doctorData['location']);
            unset($doctorData['password']);
            unset($doctorData['requesterid']);

            // Create a Doctor
            $this->Generic_model->insertData('doctors',$doctorData);

            // Map Clinic and Doctor
            $mapData['doctor_id'] = $doctor_id;
            $mapData['clinic_id'] = $parameters['clinic_id'];       
            
            // Check if the doctor id is already mapped with same clinic id
            $mapCheckRes = $this->db->select('*')->from('clinic_doctor')->where('clinic_id='.$parameters['clinic_id'].' and doctor_id='.$doctor_id)->get()->num_rows();

            if($mapCheckRes == 0){
                // Create mapping for doctor and clinic
                $this->Generic_model->insertData('clinic_doctor',$mapData);
            }

            // Subscription Package Enrollment
            // With registration, a trial period of 15days will be enrolled
            // Package name: Trial (15 Days)
            //$subscription_id = $this->Generic_model->insertDataReturnId('subscription', $subscription);

            // insert records 


            $doctor['full_name'] = trim("Dr.".ucwords($first_name.' '.$last_name));
            $this->response(array('code' => '200', 'message' => 'success ', 'result' => $doctor, 'requestname' => $method));        
        }else{
            $doctor['full_name'] = trim("Dr.".ucwords($first_name.' '.$last_name));
            $this->response(array('code' => '201', 'message' => 'Doctor already exist with mobile no. '.$mobile, 'result' => $doctor, 'requestname' => $method));
        }       

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
    //patient history pdf generation
    public function print_history($parameters, $method, $user_id) {
        $appointment_id = $parameters['appointment_id'];
        //list of appointments
        $data['appointments'] = $this->db->select("a.*, c.clinic_name,c.clinic_logo,c.address,c.clinic_phone,p.first_name as pname,p.gender,p.age,p.address_line,p.mobile,p.qrcode,d.salutation,d.first_name as dname")
        -> from("appointments a")
        ->join("clinics c","a.clinic_id = c.clinic_id","left")
        ->join("patients p","a.patient_id = p.patient_id","left")
        ->join("doctors d","a.doctor_id = d.doctor_id")
        ->where("a.appointment_id='" . $appointment_id . "'")->get()->row();
        $patient_name = $data['appointments']->pname . date('Ymd') . time();

        //list of vitals
        $vital_sign = $this->db->select("*")->from("patient_vital_sign where appointment_id='" . $appointment_id . "'")->get()->result();

        $data['vital_sign'] = '';
        foreach ($vital_sign as $key => $value) {
            $data['vital_sign'][$value->vital_sign] = $value->vital_result;
        }
        //list of clinical diagnosis
        $data['patient_clinical_diagnosis'] = $this->db->select("*")->from("patient_clinical_diagnosis")-> where("appointment_id='" . $appointment_id . "'")->get()->result();
        //list of patient prescription
        $data['patient_prescription'] = $this->db->select("pp.patient_id,pp.appointment_id,pd.drug_id, pd.day_schedule, pd.preffered_intake, pd.dose_course, pd.quantity,d.trade_name, d.composition")->from("patient_prescription pp")
        ->join("patient_prescription_drug pd","pp.patient_prescription_id = pd.patient_prescription_id","left")
        ->join("drug d","d.drug_id=pd.drug_id")
        ->where("pp.appointment_id='" . $appointment_id . "' ")->get()->result();

        //pdf settings for printing
        $data['pdf_settings'] = $pdf_settings = $this->db->select("*")->from("clinic_pdf_settings")->where("clinic_id='".$data['appointments']->clinic_id."'")->get()->row();

        $this->load->library('M_pdf');
        $html = $this->load->view('reports/summary_reports_pdf', $data, true);
        $pdfFilePath = strtolower(str_replace(" ", "_", $patient_name)) . ".pdf";
        $stylesheet = file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
        $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");


    //setting for header layout
        $HeaderConfiguration = 
        [
    'L' => [ // L for Left part of the header
    'content' => '<div><img style="width:50%" alt="" src="'.base_url().'uploads/clinic_logos/health_inn3.png"></div>',
],
    'C' => [ // C for Center part of the header
    'content' => '<div style="width:150px"></div>',
],
'R' => [
  'content' => '<div><span style="font-weight: bold;font-size: 13px">Address</span><br><span style="font-weight: 700;font-size: 13px">'.$data['appointments']->address.'</span><br><span style=" font-weight: bold;font-size: 13px">Phone:</span><span style="font-weight: 700;font-size: 13px">'.$data['appointments']->clinic_phone.'</span></div>',
],
    'line' => 0, // That's the relevant parameter
];

$footerConfiguration = 
[
     'L' => [ // L for Left part of the header
     'content' => '',
 ],
    'C' => [ // C for Center part of the header
    'content' => '',
],
'R' => [
  'content' => '<span style="font-size:12px;"><b>Date: </b>'.date("d/m/Y").'</span>',
],
    'line' => 0, // That's the relevant parameter
];



$Header = [
  'odd' => $HeaderConfiguration,
  'even' => $HeaderConfiguration
];
$Footer = [
  'odd' => $footerConfiguration,
  'even' => $footerConfiguration
];
$this->m_pdf->pdf->setAutoTopMargin = "stretch";
$this->m_pdf->pdf->defaultheaderline = 0;
if(count($pdf_settings)>0){

    if($pdf_settings->header == 1){
        $this->m_pdf->pdf->SetHeader($Header);
    }
    else{
        $this->m_pdf->pdf->SetHeader('<div style="height:'.$pdf_settings->header_height.'px;border:none"></div>');
    }
    

}


$this->m_pdf->pdf->WriteHTML($stylesheet,1);
$this->m_pdf->pdf->WriteHTML($html,2);
$param['summary_pdf'] = base_url() . 'uploads/summary_reports/' . $pdfFilePath;
if ($appointment_id != NULL) {
    $result = array('code' => '200', 'message' => 'form_details', 'result' => array("appointment_id" => $appointment_id, 'pdf_file' => $param['summary_pdf']), 'requestname' => $method);
    $this->response($result);
} else {
    $result = array('code' => '200', 'message' => 'Patient pdf Failed', 'result' => NULL, 'requestname' => $method);
    $this->response($result);
}
}


    // login for all employees except patient
public function login_details($parameters, $method, $user_id) {

    $email = $parameters['email'];
    $password = md5($parameters['password']);

    $result = $this->db->select("*")->from("users")->where("(username = '" . $email . "' or email_id = '" . $email . "' or mobile ='" . $email . "' ) and password ='" . $password . "'")->get()->row();

    if ($result == TRUE) {

        $user_type = $result->user_type;

        // if($user_type == "patient" || $user_type == "Patient")
        // {
        //     $users_details = $this->db->select("a.*)")->from("users a")->where("a.user_id ='" . $result->user_id . "' and a.status ='1'")->get()->row();
        // }else{
        // $users_details = $this->db->select("a.*,a.clinic_id as clinic_id,b.*,c.*")->from("users a")->join("roles b","a.role_id = b.role_id","left")
        // ->join("profiles c","a.profile_id = c.profile_id","left")
        // ->where("a.user_id ='" . $result->user_id . "' and a.status ='1'")->get()->row();
        // // }

        // echo ($users_details);
        // print_r($users_details);
        // echo $this->db->last_query();

        $users_details = $this->db->select('U.*, R.role_name, P.profile_name, P.archieve')->from('users U')->join('roles R','U.role_id = R.role_id')->join('profiles P','U.profile_id = P.profile_id')->where("U.user_id =",$result->user_id)->where('U.status =',1)->get()->row();

        /* FCM ID Update */
        $fcm_id['fcm_id'] = $parameters['fcmId'];
        $fcm_id['device_id'] = $parameters['deviceid'];
        $this->Generic_model->updateData('users', $fcm_id, array('user_id' => $result->user_id));

        /* used to get the details of a user based on user type */
        if (count($users_details) > 0) {

            if ($user_type == "doctor") {
                $list_val = $this->Generic_model->getSingleRecord('doctors',array('doctor_id'=>$result->user_id),'');

                $name = $list_val->first_name . " " . $list_val->last_name;
                $gender = $list_val->gender;

                $clinics_list = $this->db->select("*")->from("clinic_doctor cd")->join("clinics c","cd.clinic_id=c.clinic_id")->where("cd.doctor_id ='" . $result->user_id . "'")->group_by("c.clinic_id")->get()->result();
            } else if ($user_type == "patient" || $user_type == "Patient") {

                $list_val =  $this->Generic_model->getSingleRecord('patients',array('patient_id'=>$result->user_id),'');

                $name = $list_val->first_name;
                $gender = $list_val->gender;
                $clinics_list = $this->db->select("*")->from("patients p")->join("clinics c","p.clinic_id=c.clinic_id")->where("p.patient_id ='" . $result->user_id . "'")->group_by("c.clinic_id")->get()->result();

            } else if ($user_type == "employee" || $user_type == "employees") {

                $list_val = $this->Generic_model->getSingleRecord('employees',array('employee_id'=>$result->user_id),'');

                $name = $list_val->first_name;
                $gender = $list_val->gender;
                $clinic_id = $list_val->clinic_id;
                $clinics_list =  $this->db->select("*")->from("employees e")->join("clinics c","e.clinic_id=c.clinic_id")->where("e.employee_id ='" . $result->user_id . "'")->group_by("c.clinic_id")->get()->result();


            } else {
                $this->response(array('code' => '404', 'message' => 'Authentication Failed contact to Admin'), 200);
            }

            $param['user_id'] = $users_details->user_id;
            $param['password'] = $users_details->password;
            $param['username'] = $users_details->username;
            $param['clinic_id'] = $users_details->clinic_id;
            $param['department_id'] = $list_val->department_id;
            $param['name'] = $name;
            $param['gender'] = $gender;
            $param['email'] = $users_details->email_id;
            $param['user_type'] = $users_details->user_type;
            $param['role_id'] = $users_details->role_id;
            $param['role_name'] = $users_details->role_name;
            $param['profile_id'] = $users_details->profile_id;
            $param['profile_name'] = $users_details->profile_name;
            $param['status'] = $users_details->status;

                //role based menu
            $profile_permissions = $this->db->select("*")->from("user_entities a")
            ->join("profile_permissions b","a.user_entity_id = b.user_entity_id")
            ->where("b.profile_id = '" . $users_details->profile_id . "' and is_mobile_module = '1' and level='0' and parent_id = '0' and (p_create !=0 or p_read !=0 or p_update !=0 or p_delete !=0)")
            ->order_by("position asc")->get()->result();

            if(count($profile_permissions)>0)
            {
                $i = 0;
                foreach ($profile_permissions as $access_val) {
                    $param['left_nav'][$i]['id'] = $access_val->user_entity_id;
                    $param['left_nav'][$i]['method_name'] = $access_val->method_name;
                    $param['left_nav'][$i]['name'] = $access_val->user_entity_name;
                    $param['left_nav'][$i]['read'] = $access_val->p_read;
                    $param['left_nav'][$i]['create'] = $access_val->p_create;
                    $param['left_nav'][$i]['update'] = $access_val->p_update;
                    $param['left_nav'][$i]['delete'] = $access_val->p_delete;
                    $i++;
                }
            }else{
                $param['left_nav'] = array();
            }


            $c = 0;
            if (count($clinics_list) > 0) {
                foreach ($clinics_list as $clinic_val) {
                    $param['clinics'][$c]['clinic_id'] = $clinic_val->clinic_id;
                    $param['clinics'][$c]['clinic_name'] = $clinic_val->clinic_name;
                    if($clinic_val->clinic_logo == "" || $clinic_val->clinic_logo == null){$clinic_logo = "null";}else{ $clinic_logo =  base_url("/uploads/clinic_logos/".$clinic_val->clinic_logo."");}
                    if($clinic_val->clinic_emblem == "" || $clinic_val->clinic_emblem == null){$clinic_emblem = "";}else{ $clinic_emblem =  base_url("/uploads/clinic_logos/".$clinic_val->clinic_emblem."");}

                    $param['clinics'][$c]['clinic_logo'] = $clinic_logo;
                    $param['clinics'][$c]['clinic_emblem'] = $clinic_emblem;


                    $c++;
                }
            } else {
                $param['clinics'] = array();
            }

            $case_sheet_list = $this->db->select("*")->from("user_entities a")
            ->join(" profile_permissions b ","a.user_entity_id = b.user_entity_id")
            ->where("b.profile_id = '" . $users_details->profile_id . "' and is_mobile_module = '1' and level='1' and parent_id != 0")->get()->result();
            $i = 0;
            $j = 1;
            foreach ($case_sheet_list as $case_values) {
                $param['titles'][$i]['title_name'] = $case_values->user_entity_alias;
                $param['titles'][$i]['method_name'] = $case_values->method_name;
                $param['titles'][$i]['position'] = $case_values->position;
                $param['titles'][$i]['read'] = $case_values->p_read;

                $param['titles'][$i]['create'] = $case_values->p_create;
                $param['titles'][$i]['update'] = $case_values->p_update;
                $param['titles'][$i]['delete'] = $case_values->p_delete;

                $checking_parent_list = $this->db->select("*")->from("user_entities a")
                ->join("profile_permissions b","a.user_entity_id = b.user_entity_id")
                ->where("b.profile_id = '" . $users_details->profile_id . "' and is_mobile_module = '1' and level = '2' and parent_id = '" . $case_values->user_entity_id . "'")
                ->order_by("a.user_entity_id")->get()->result();
                $k = 0;
                foreach ($checking_parent_list as $parent_case_val) {
                    $param['titles'][$i]['titles'][$k]['title_name'] = $parent_case_val->user_entity_name;
                    $param['titles'][$i]['titles'][$k]['method_name'] = $parent_case_val->method_name;
                    $param['titles'][$i]['titles'][$k]['position'] = $j++;
                    $param['titles'][$i]['titles'][$k]['read'] = $parent_case_val->p_read;
                    $param['titles'][$i]['titles'][$k]['create'] = $parent_case_val->p_create;
                    $param['titles'][$i]['titles'][$k]['update'] = $parent_case_val->p_update;
                    $param['titles'][$i]['titles'][$k]['delete'] = $parent_case_val->p_delete;


                    $k++;
                }

                $i++;
            }

            // Details of user
            if($user_type == 'doctor'){
                // Get details
                $doctor_details = $this->db->select('Doc.doctor_id, Doc.first_name, Doc.last_name, C.clinic_id, C.clinic_name, C.location, Doc.department_id, Doc.email, Doc.mobile, Doc.qualification, Doc.registration_code')->from('doctors Doc')->join('clinic_doctor CD','Doc.doctor_id = CD.doctor_id')->join('clinics C','CD.clinic_id = C.clinic_id')->where('Doc.doctor_id =',$param['user_id'])->get()->row();

                $param['doctor_details'] = $doctor_details;
            }

            $this->response(array('code' => '200', 'message' => 'successfully Login ', 'result' => $param, 'requestname' => $method));
        } else {
            $this->response(array('code' => '404', 'message' => 'Authentication Failed contact to Admin'), 200);
        }
    } else {
        $this->response(array('code' => '404', 'message' => 'Authentication Failed contact to Admin'), 200);
    }
}

    //update password(vikram)
public function change_password($parameters, $method, $user_id) {
    $password = $parameters['new_password'];
    $old_password = $parameters['old_password'];
        //checking old password with user input
    $check_old_password = $this->db->select("password")->from("users")->where("password = '" . $old_password . "' and user_id='".$user_id."'")->get()->row();
    if ($check_old_password->password == $old_password) {
        if ($password != "") {
            $param['password'] = md5($password);
            $param['modified_date_time'] = date("Y-m-d H:i:s");
            $param['modified_by'] = $user_id;
            $ok = $this->Generic_model->updateData('users', $param, array('user_id' => $user_id));
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

    //sending otp to mobile and email(vikram)
public function forgot_password_otp($parameters, $method, $user_id) {
    $check_user = $this->db->select("*")->from("users")->where("email_id='" . $parameters['email_id'] . "' or mobile='".$parameters['email_id']."')")->get()->row();

    if (count($check_user) > 0) {
        $last_four_digits = str_replace(range(0, 9), "*", substr($check_user->mobile, 0, -4)) . substr($check_user->mobile, -4);
        $random_otp = mt_rand(100000, 999999);
        $mobile = $check_user->mobile;
        $message = "OTP is " . $random_otp . " to reset your password.";
        send_otp($mobile, $random_otp, $message);
        $result['otp'] = $random_otp;
        $result['user_id'] = $check_user->user_id;
        $result['user_name'] = $check_user->username;
        $this->response(array('code' => '200', 'message' => 'OTP Sent On Mobile ' . $last_four_digits, 'result' => $result, 'requestname' => $method));
    } else {
        $this->response(array('code' => '404', 'message' => 'User Does Not Exist'), 200);
    }
}

    //after otp verification updating new password(vikram)
public function forgot_password_update($parameters, $method, $user_id) {

    $param['password'] = md5($parameters['password']);
    $ok = $this->Generic_model->updateData('users', $param, array('user_id' => $parameters['user_id']));
    if ($ok == 1) {
        $this->response(array('code' => '200', 'message' => 'Successfully Password Changed ', 'requestname' => $method));
    } else {
        $this->response(array('code' => '404', 'message' => 'Update Password Failed'), 200);
    }
}

    //List of patient presenting systems(vikram)
public function presenting_symptoms_list($parameters, $method, $user_id) {

    $presenting_symptoms = $this->db->select("*")->from("patient_presenting_symptoms ps")
    ->join("patient_ps_line_items psl","ps.patient_presenting_symptoms_id = psl.patient_presenting_symptoms_id")->where("patient_id= '" . $parameters['patient_id'] . "' and appointment_id= '" . $parameters['appointment_id'] . "' and doctor_id= '" . $parameters['doctor_id'] . "' and clinic_id= '" . $parameters['clinic_id'] . "'")->get()->result();

    $ps = 0;
    if (count($presenting_symptoms) > 0) {

        foreach ($presenting_symptoms as $psl) {
            $param['presenting_symptoms_list'][$ps]['patient_presenting_symptoms_id'] = $psl->patient_presenting_symptoms_id;
            $param['presenting_symptoms_list'][$ps]['pps_line_item_id'] = $psl->pps_line_item_id;
            $param['presenting_symptoms_list'][$ps]['span_type'] = $psl->span_type;
            $param['presenting_symptoms_list'][$ps]['symptom_data'] = $psl->symptom_data;
            $param['presenting_symptoms_list'][$ps]['time_span'] = $psl->time_span;

            $ps++;
        }
    } else {
        $param['presenting_symptoms_list'] = NULL;
    }
    $this->response(array('code' => '200', 'message' => 'Presenting Symptoms List', 'result' => $param, 'requestname' => $method));
}

    //updating present symptoms(vikram)
public function presenting_symptoms_update($parameters, $method, $user_id) {

    $param['span_type'] = $parameters['span_type'];
    $param['symptom_data'] = $parameters['symptom_data'];
    $param['time_span'] = $parameters['time_span'];
    $ok = $this->Generic_model->updateData('patient_ps_line_items', $param, array('pps_line_item_id' => $parameters['pps_line_item_id']));
    if ($ok == 1) {
        $this->response(array('code' => '200', 'message' => 'Successfully Updated ', 'requestname' => $method));
    } else {
        $this->response(array('code' => '404', 'message' => 'Update Failed'), 200);
    }
}

    //deleting present symptoms(vikram)
public function presenting_symptoms_delete($parameters, $method, $user_id) {


    $ok = $this->db->delete('patient_ps_line_items', array('pps_line_item_id' => $parameters['pps_line_item_id']));

    if ($ok == 1) {
        $this->response(array('code' => '200', 'message' => 'Successfully Updated ', 'requestname' => $method));
    } else {
        $this->response(array('code' => '404', 'message' => 'Update Failed'), 200);
    }
}

public function masters_zip($parameters, $method, $user_id) {
    $clinic_id = $parameters['clinic_id'];
    $role_id = $parameters['role_id'];
    if ($role_id == 1) {
            //$doctors=$this->db->query("select * from doctors ");
    } else if ($role_id == 2) {

    } else if ($role_id == 3) {

    } else if ($role_id == 4) {

    } else if ($role_id == 5) {

    } else if ($role_id == 6) {

    }



    /* States Zip */

    $state_details = $this->Generic_model->getAllRecords("states", $condition = '', $order = '');
    $s = 0;
    foreach ($state_details as $state) {
        $states['state'][$s]['state_id'] = $state->state_id;
        $states['state'][$s]['state_name'] = $state->state_name;
        $district_details = $this->db->select("*")->from("districts")->where("state_id='" . $state->state_id . "' ")->get()->result();
        $d = 0;
        if (count($district_details) > 0) {
            foreach ($district_details as $district) {
                $states['state'][$s]['districts'][$d]['district_id'] = $district->district_id;
                $states['state'][$s]['districts'][$d]['district_name'] = $district->district_name;
                $states['state'][$s]['districts'][$d]['state_id'] = $district->state_id;
                $d++;
            }
        } else {
            $states['state'][$s]['districts'] = array();
        }
        $s++;
    }

    $state_file = './uploads/masters_json/states.json';
    $state_data = json_encode($states);
    $this->zip->add_data($state_file, $state_data);


        // Write the zip file to a folder on your server. Name it "my_backup.zip"
    $this->zip->archive('./uploads/masters_json/masters.zip');

    $patient_val['path'] = base_url() . "uploads/masters_json/masters.zip";
    $this->response(array('code' => '200', 'message' => 'success ', 'result' => $patient_val, 'requestname' => $method));


}


    //list of patient medical reports
public function previous_documents($parameters, $method, $user_id) {

    $previous_documents = $this->db->select("*")->from("previous_documents")->where("patient_id= '" . $parameters['patient_id'] . "'")->order_by("previous_document_id","desc")->get()->result();

    $pv = 0;
    if (count($previous_documents) > 0) {

        foreach ($previous_documents as $pav) {
            $param['previous_documents'][$pv]['previous_document_id'] = $pav->previous_document_id;
            $param['previous_documents'][$pv]['appointment_id'] = $pav->appointment_id;
            $param['previous_documents'][$pv]['current_date'] = $pav->cur_date;
            $param['previous_documents'][$pv]['patient_id'] = $pav->patient_id;
            $param['previous_documents'][$pv]['report_date'] = $pav->report_date;
            $param['previous_documents'][$pv]['document_type'] = $pav->document_type;

            $param['previous_documents'][$pv]['description'] = $pav->description;
            $images = trim($pav->images, ",");
            $picture_explode = explode(",", $images);
            for ($k = 0; $k < count($picture_explode); $k++) {
                $param['previous_documents'][$pv]['images'][$k]['image'] = base_url('uploads/previous_documents/' . trim($picture_explode[$k]));
            }
            $pv++;
        }
    } else {
        $param['previous_documents'] = NULL;
    }
    $this->response(array('code' => '200', 'message' => 'Previous Documents', 'result' => $param, 'requestname' => $method));
}

    //list of followup template list. if patient followup empty showing master followus
public function followup_template_list($parameters, $method, $user_id) {
    $followup_id = "";
    $follow_array = array();
    $check_exists = $this->Generic_model->getSingleRecord('appointments',array('appointment_id'=>$parameters['appointment_id']),'');


    if ($check_exists->parent_appointment_id == 0 || $check_exists->parent_appointment_id == NULL) {
        $check_template = $this->db->select("GROUP_CONCAT(pf.followup_id) as followup_id")
        ->from(" patient_followup pf")
        ->join("patient_followup_line_items pfl","pf.patient_followup_id = pfl.patient_followup_id","left outer")
        ->where("pf.appointment_id IN(" . $parameters['appointment_id'] . ")")->get()->row();

    } else {
        $appoint_ment_ids = $this->db->select("GROUP_CONCAT(appointment_id) as appointment_id")->from(" appointments")->where("parent_appointment_id='".$check_exists->parent_appointment_id."'")->get()->row();

        $new_app_id = array($appoint_ment_ids->appointment_id, $check_exists->parent_appointment_id);
        $new_ids = implode(",", $new_app_id);

        $check_template = $this->db->select("GROUP_CONCAT(DISTINCT(pf.followup_id)) as followup_id")
        ->from("patient_followup pf")->join("patient_followup_line_items pfl","pf.patient_followup_id = pfl.patient_followup_id","left outer")->where("pf.appointment_id IN(" . $new_ids . ")")->get()->row();

    }
    if (count($check_template) > 0 && $check_template->followup_id != NULL) {

        $followup_id = $check_template->followup_id;
        $follow_array = explode(",",$check_template->followup_id);

        $appnt_id = $parameters['appointment_id'];

        $app_info = $this->Generic_model->getSingleRecord('appointments',array('appointment_id'=>$parameters['appointment_id']),'');

        $template_info = $this->db->select("*")->from("appointments")->where("clinic_id='" . $app_info->clinic_id . "' and patient_id ='" . $app_info->patient_id . "' and status!='reschedule' and status!='drop' and appointment_date <='" . date('Y-m-d') . "'")->group_by(array("appointment_date", "appointment_id"))->get()->result();
        $data["follow_up_columns"][0]['column_id'] = "0";
        $data["follow_up_columns"][0]['title'] = "Visit No";
        $app_id = array();
        $i = 1;
        foreach ($template_info as $key => $value) {
            $data["follow_up_columns"][$i]['column_id'] = "$i";
            $data["follow_up_columns"][$i]['title'] = "$i";
            $app_id[] = $value->appointment_id;
            $app_dates[] = $value->appointment_date;
            $i++;
        }
        $k = 0;
        $data["follow_up_rows"][$k]['row_id'] = "1";
        $data["follow_up_rows"][$k]['row_data'][0]['column_id'] = "0";
        $data["follow_up_rows"][$k]['row_data'][0]['title'] = "Visit Date";
        $data["follow_up_rows"][$k]['row_data'][0]['edit_permission'] = 0;
        $j = 1;
        foreach ($template_info as $key => $value) {
            $data["follow_up_rows"][$k]['row_data'][$j]['column_id'] = "$j";
            $data["follow_up_rows"][$k]['row_data'][$j]['appointment_id'] = $value->appointment_id;
            $data["follow_up_rows"][$k]['row_data'][$j]['title'] = date('d-M-Y',strtotime($value->appointment_date));
            $data["follow_up_rows"][$k]['row_data'][$j]['edit_permission'] = 0;
            $j++;
        }
                //clinical diagnosys
        $cd= 1;
        $data["follow_up_rows"][1]['row_id'] = "2";
        $data["follow_up_rows"][1]['row_data'][0]['column_id'] = "0";
        $data["follow_up_rows"][1]['row_data'][0]['title'] = "Clinical Diagnosis";
        $data["follow_up_rows"][1]['row_data'][0]['edit_permission'] = 0;
                //$cd=$l+1;

        $pcd=1;
        foreach($template_info as $key => $cd_info)
        {

            $pcd_info = $this->db->select("*")->from("patient_clinical_diagnosis")->where(" appointment_id='".$cd_info->appointment_id."'")->group_by("appointment_id")->get()->result();
            $data["follow_up_rows"][1]['row_id'] = "2";
            $data["follow_up_rows"][1]['row_data'][$pcd]['appointment_id'] = $cd_info->appointment_id;
            $data["follow_up_rows"][1]['row_data'][$pcd]['column_id'] = "$pcd";
            $data["follow_up_rows"][1]['row_data'][$pcd]['title'] = "View";
            $data["follow_up_rows"][1]['row_data'][$pcd]['edit_permission'] = 0;
            $pcd++;
            $cd++;
        }


        $data["follow_up_rows"][2]['row_id'] = "3";
        $data["follow_up_rows"][2]['row_data'][0]['column_id'] = "0";
        $data["follow_up_rows"][2]['row_data'][0]['title'] = "Vitals";
        $data["follow_up_rows"][2]['row_data'][0]['edit_permission'] = 0;
        $m = 1;
        $l = 2;
        foreach ($template_info as $key => $value) {
            $data["follow_up_rows"][$l]['row_id'] = "$l";
            $data["follow_up_rows"][$l]['row_data'][$m]['column_id'] = "$m";
            $data["follow_up_rows"][$l]['row_data'][$m]['appointment_id'] = $value->appointment_id;
            $data["follow_up_rows"][$l]['row_data'][$m]['title'] = "";
            $data["follow_up_rows"][$l]['row_data'][$m]['edit_permission'] = 0;
            $m++;
        }



        $r = 3;
        $n = 0;
        $test = array();
        $join = implode(",", $app_id);
        $join2 = implode(",", $app_dates);
        foreach ($template_info as $key => $value) {
            $test[] = $this->db->select("*")->from("patient_vital_sign")->where("vital_sign_recording_date_time = (select max(vital_sign_recording_date_time) From patient_vital_sign where vital_sign_recording_date_time like '" . $value->appointment_date . "%')")->order_by("vital_sign_recording_date_time","DESC")->get()->result();
            $r++;
            $n++;
        }

                //vitals info
        $vitals_details = $this->db->select("*")->from("patient_vital_sign")->where("appointment_id in (" . $join . ") and vital_sign NOT IN ('SBP','DBP')")->group_by(" vital_sign")->get()->result();

        $j = 0;
        $c = 3;
        foreach ($vitals_details as $key => $value) {

            $data["follow_up_rows"][$c]['row_id'] = "$c";
            $data["follow_up_rows"][$c]['row_data'][0]['column_id'] = "$rc";
            $data["follow_up_rows"][$c]['row_data'][0]['title'] = $value->vital_sign;
            $data["follow_up_rows"][$c]['row_data'][0]['edit_permission'] = 0;
            $i = 0;
            $rc = 1;
            foreach ($app_dates as $key2 => $value2) {
                $data["follow_up_rows"][$c]['row_id'] = "$c";
                $data["follow_up_rows"][$c]['row_data'][$rc]['appointment_id'] = $value->appointment_id;
                $data["follow_up_rows"][$c]['row_data'][$rc]['column_id'] = "$rc";
                $data["follow_up_rows"][$c]['row_data'][$rc]['title'] = getvitalsbydate($value2, $value->vital_sign);
                $data["follow_up_rows"][$c]['row_data'][$rc]['edit_permission'] = 0;
                $i++;
                $rc++;
            }
            $j++;
            $c++;
        }
        $sbp_dbp = $this->db->select("GROUP_CONCAT(DISTINCT(vital_sign)) as vital_sign")->from(" patient_vital_sign")->where("appointment_id in (" . $join . ") and vital_sign IN ('SBP','DBP') ")->get()->row();

        if(count($sbp_dbp)>0)
        {
            $d=$c;

            if($sbp_dbp->vital_sign != NULL)
            {
                $data["follow_up_rows"][$d]['row_id'] = "$d";
                $data["follow_up_rows"][$d]['row_data'][0]['column_id'] = "0";
                $data["follow_up_rows"][$d]['row_data'][0]['title'] = 'BP';
                $data["follow_up_rows"][$d]['row_data'][0]['edit_permission'] = 0;
                $rs=0;
                
                $a = 0;
                $rs = 1;
                foreach ($template_info as $key2 => $value2) {
                    $sbp = $this->db->select("*")->from("patient_vital_sign")->where("vital_sign='SBP' and appointment_id in (".$value2->appointment_id.")")->order_by("patient_vital_id","desc")->get()->row();
                    $dbp = $this->db->select("*")->from("patient_vital_sign")->where("vital_sign='DBP' and appointment_id in (".$value2->appointment_id.")")->order_by("patient_vital_id","desc")->get()->row();
                        //echo $this->db->last_query();exit();
                    $data["follow_up_rows"][$d]['row_id'] = "$d";
                    $data["follow_up_rows"][$d]['row_data'][$rs]['appointment_id'] = $value2->appointment_id;
                    $data["follow_up_rows"][$d]['row_data'][$rs]['column_id'] = "$rs";
                    $data["follow_up_rows"][$d]['row_data'][$rs]['title'] = $sbp->vital_result." / ".$dbp->vital_result;
                    $data["follow_up_rows"][$d]['row_data'][$rs]['edit_permission'] = 0;
                    $a++;
                    $rs++;
                }
                $e = $d+1;
            }else{
                $e = $d;    
            }
        }else{
            $e = $d;
        }

        $pci = $this->db->select("GROUP_CONCAT(parameter_id) as parameter_id")->from(" followup_parameter")->where("followup_id IN (" . $followup_id . ")")->get()->row();

        $params = $pci->parameter_id;
                // Clinical Parameters
        $pc_info = $this->db->select("*")->from("parameters")->where("parameter_type='Clinical' and parameter_id IN (" . $params . ")")->get()->result();

        $data["follow_up_rows"][$e]['row_id'] = "$e";
        $data["follow_up_rows"][$e]['row_data'][0]['column_id'] = "0";
        $data["follow_up_rows"][$e]['row_data'][0]['title'] = "Clinical Parameters";
        $data["follow_up_rows"][$e]['row_data'][0]['edit_permission'] = 0;
        $cp = 1;
        foreach ($template_info as $key => $value) {
            $data["follow_up_rows"][$e]['row_id'] = "$e";
            $data["follow_up_rows"][$e]['row_data'][$cp]['column_id'] = "$cp";
            $data["follow_up_rows"][$e]['row_data'][$cp]['appointment_id'] = $value->appointment_id;
            $data["follow_up_rows"][$e]['row_data'][$cp]['title'] = "";
            $data["follow_up_rows"][$e]['row_data'][$cp]['edit_permission'] = 0;
            $cp++;
        }
        $pr = $e + 1;
        $cr = 1;
        foreach ($pc_info as $key => $pvalue) {

            $data["follow_up_rows"][$pr]['row_id'] = "$pr";
            $data["follow_up_rows"][$pr]['row_data'][0]['column_id'] = "0";
            $data["follow_up_rows"][$pr]['row_data'][0]['title'] = $pvalue->parameter_name;
            $data["follow_up_rows"][$pr]['row_data'][0]['edit_permission'] = 0;
            $pcr = 1;
            $fcr = 1;
            foreach ($app_id as $key => $dvalue) {

                $data["follow_up_rows"][$pr]['row_id'] = "$pr";
                $data["follow_up_rows"][$pr]['row_data'][$pcr]['column_id'] = "$fcr";
                $data["follow_up_rows"][$pr]['row_data'][$pcr]['appointment_id'] = $dvalue;
                $data["follow_up_rows"][$pr]['row_data'][$pcr]['title'] = getparameters($pvalue->parameter_id, $dvalue);
                $data["follow_up_rows"][$pr]['row_data'][$pcr]['edit_permission'] = 0;
                $pcr++;
                $fcr++;
            }
            $pr++;
            $cr++;
        }
                 //Lab Parameters
        $pl_info = $this->db->select("*")->from("parameters")->where("parameter_type='Lab' and parameter_id IN (" . $params . ")")->get()->result();


        $data["follow_up_rows"][$pr]['row_id'] = "$pr";
        $data["follow_up_rows"][$pr]['row_data'][0]['column_id'] = "0";
        $data["follow_up_rows"][$pr]['row_data'][0]['title'] = "Lab Parameters";
        $data["follow_up_rows"][$pr]['row_data'][0]['edit_permission'] = 0;

        $lp = 1;
        foreach ($template_info as $key => $value) {
            $data["follow_up_rows"][$pr]['row_id'] = "$pr";
            $data["follow_up_rows"][$pr]['row_data'][$lp]['column_id'] = "$lp";
            $data["follow_up_rows"][$pr]['row_data'][$lp]['appointment_id'] = $value->appointment_id;
            $data["follow_up_rows"][$pr]['row_data'][$lp]['title'] = "";
            $data["follow_up_rows"][$pr]['row_data'][$lp]['edit_permission'] = 0;
            $lp++;
        }

        $pl = $pr + 1;
        $cl = 1;
        foreach ($pl_info as $key => $plvalue) {
            $name = $pvalue->parameter_id;
            $data["follow_up_rows"][$pl]['row_id'] = "$pl";
            $data["follow_up_rows"][$pl]['row_data'][0]['column_id'] = "0";
            $data["follow_up_rows"][$pl]['row_data'][0]['title'] = $plvalue->parameter_name;
            $data["follow_up_rows"][$pl]['row_data'][0]['edit_permission'] = 0;

            $pcl = 1;
            $fcl = 1;
            foreach ($app_id as $key => $dvalue) {
                $data["follow_up_rows"][$pl]['row_id'] = "$pl";
                $data["follow_up_rows"][$pl]['row_data'][$pcl]['appointment_id'] = $dvalue;
                $data["follow_up_rows"][$pl]['row_data'][$pcl]['column_id'] = "$fcl";
                $data["follow_up_rows"][$pl]['row_data'][$pcl]['title'] = getparameters($plvalue->parameter_id, $dvalue);
                $data["follow_up_rows"][$pl]['row_data'][$pcl]['edit_permission'] = 0;
                $pcl++;
                $fcl++;
            }
            $pl++;
            $cl++;
        }

        $r = 1;
        $s = 0;
        foreach ($template_info as $key => $vinfo) {
            $data["visits_info"][$s]['visit_no'] = $r;
            $data["visits_info"][$s]['date'] = $vinfo->appointment_date;

            $pci = $this->db->select("GROUP_CONCAT(parameter_id) as parameter_id")->from(" followup_parameter")->where("followup_id IN (" . $followup_id . " )")->get()->row();

            $pc_info = $this->db->select("*")->from("parameters")->where("parameter_type='Clinical' and parameter_id IN (" . $pci->parameter_id . ")")->get()->result();

            $pl_info = $this->db->select("*")->from("parameters")->where("parameter_type='Lab' and parameter_id IN (" . $pci->parameter_id . ")")->get()->result();
            $pc = 0;
            foreach ($pc_info as $key => $value) {
                $data["visits_info"][$s]['Clinical_params'][$pc]['param_id'] = "$value->parameter_id";
                $data["visits_info"][$s]['Clinical_params'][$pc]['param'] = $value->parameter_name;
                $data["visits_info"][$s]['Clinical_params'][$pc]['value'] = getparameters($value->parameter_id, $vinfo->appointment_id);
                $pc++;
            }
            $pl = 0;
            foreach ($pl_info as $key => $value2) {
                $data["visits_info"][$s]['Lab_parameters'][$pl]['param_id'] = "$value2->parameter_id";
                $data["visits_info"][$s]['Lab_parameters'][$pl]['param'] = $value2->parameter_name;
                $data["visits_info"][$s]['Lab_parameters'][$pl]['value'] = getparameters($value2->parameter_id, $vinfo->appointment_id);
                $pl++;
            }
            $msef_complaince = $this->Generic_model->getSingleRecord('patient_msef_complaince',array('appointment_id'=>$vinfo->appointment_id),'');

            if(count($msef_complaince)>0)
            {


                $com=0;
                $data["visits_info"][$s]['Compliance'][$com]['param_id'] = "$msef_complaince->patient_msef_complaince_id";
                $data["visits_info"][$s]['Compliance'][$com]['param'] = "Medication Compliance & Side Effects";
                $data["visits_info"][$s]['Compliance'][$com]['value'] = $msef_complaince->medication_complaince;

                $data["visits_info"][$s]['Compliance'][$com+1]['param_id'] = "$msef_complaince->patient_msef_complaince_id";
                $data["visits_info"][$s]['Compliance'][$com+1]['param'] = "Socio Economic Factors";
                $data["visits_info"][$s]['Compliance'][$com+1]['value'] = $msef_complaince->socio_economic_factors;

            }else{

                $com=0;
                $data["visits_info"][$s]['Compliance'][$com]['param_id'] = "0";
                $data["visits_info"][$s]['Compliance'][$com]['param'] = "Medication Compliance & Side Effects";
                $data["visits_info"][$s]['Compliance'][$com]['value'] = "";

                $data["visits_info"][$s]['Compliance'][$com+1]['param_id'] = "0";
                $data["visits_info"][$s]['Compliance'][$com+1]['param'] = "Socio Economic Factors";
                $data["visits_info"][$s]['Compliance'][$com+1]['value'] = "";
            }


            $pf_info = $this->db->select("*")->from("patient_followup")->where("appointment_id='".$vinfo->appointment_id."'")->get()->result();
            $pfi = 0;
            foreach ($pf_info as $key =>$pfi_value) {
                $data["added_templete_ids"][$pfi]['patient_followup_id'] = $pfi_value->patient_followup_id;
                $data["added_templete_ids"][$pfi]['templete_id']= $pfi_value->followup_id;
                $pfi++;
            }
            $r++;
            $s++;
        }

        $inv= $pl+$pr+1;
        $data["follow_up_rows"][$inv]['row_id'] = "$inv";
        $data["follow_up_rows"][$inv]['row_data'][0]['column_id'] = "0";
        $data["follow_up_rows"][$inv]['row_data'][0]['title'] = "Investigations";
        $data["follow_up_rows"][$inv]['row_data'][0]['edit_permission'] = 0;


        $ie=1;
        foreach($template_info as $key => $cd_info)
        {
            $data["follow_up_rows"][$inv]['row_id'] = "$inv";
            $data["follow_up_rows"][$inv]['row_data'][$ie]['appointment_id'] = $cd_info->appointment_id;
            $data["follow_up_rows"][$inv]['row_data'][$ie]['column_id'] = "$ie";
            $data["follow_up_rows"][$inv]['row_data'][$ie]['title'] = "View";
            $data["follow_up_rows"][$inv]['row_data'][$ie]['edit_permission'] = 0;
            $ie++;
        }


        $mn= $inv+1;
        $data["follow_up_rows"][$mn]['row_id'] = "$mn";
        $data["follow_up_rows"][$mn]['row_data'][0]['column_id'] = "0";
        $data["follow_up_rows"][$mn]['row_data'][0]['title'] = "Medications";
        $data["follow_up_rows"][$mn]['row_data'][0]['edit_permission'] = 0;
                //$cd=$l+1;

        $me=1;
        foreach($template_info as $key => $cd_info)
        {
            $data["follow_up_rows"][$mn]['row_id'] = "$mn";
            $data["follow_up_rows"][$mn]['row_data'][$me]['appointment_id'] = $cd_info->appointment_id;
            $data["follow_up_rows"][$mn]['row_data'][$me]['column_id'] = "$me";
            $data["follow_up_rows"][$mn]['row_data'][$me]['title'] = "View";
            $data["follow_up_rows"][$mn]['row_data'][$me]['edit_permission'] = 0;
            $me++;
        }

        $mc= $mn+1;
        $data["follow_up_rows"][$mc]['row_id'] = "$mc";
        $data["follow_up_rows"][$mc]['row_data'][0]['column_id'] = "0";
        $data["follow_up_rows"][$mc]['row_data'][0]['title'] = "Medical Compliance & Side Effects";
        $data["follow_up_rows"][$mc]['row_data'][0]['edit_permission'] = 0;
                //$cd=$l+1;

        $mco=1;
        foreach($template_info as $key => $cd_info)
        {
            $medication_complaince = $this->Generic_model->getSingleRecord('patient_msef_complaince',array('appointment_id'=>$cd_info->appointment_id),'');
            $data["follow_up_rows"][$mc]['row_id'] = "$mc";
            $data["follow_up_rows"][$mc]['row_data'][$mco]['appointment_id'] = $cd_info->appointment_id;
            $data["follow_up_rows"][$mc]['row_data'][$mco]['column_id'] = "$mco";
            $data["follow_up_rows"][$mc]['row_data'][$mco]['title'] = $medication_complaince->medication_complaince;
            $data["follow_up_rows"][$mc]['row_data'][$mco]['edit_permission'] = 0;
            $mco++;
        }

        $se= $mc+1;
        $data["follow_up_rows"][$se]['row_id'] = "$se";
        $data["follow_up_rows"][$se]['row_data'][0]['column_id'] = "0";
        $data["follow_up_rows"][$se]['row_data'][0]['title'] = "Socio Economic Factors";
        $data["follow_up_rows"][$se]['row_data'][0]['edit_permission'] = 0;
                //$cd=$l+1;

        $seff=1;
        foreach($template_info as $key => $cd_info)
        {
            $sef = $this->Generic_model->getSingleRecord('patient_msef_complaince',array('appointment_id'=>$cd_info->appointment_id),'');
            $data["follow_up_rows"][$se]['row_id'] = "$se";
            $data["follow_up_rows"][$se]['row_data'][$seff]['appointment_id'] = $cd_info->appointment_id;
            $data["follow_up_rows"][$se]['row_data'][$seff]['column_id'] = "$seff";
            $data["follow_up_rows"][$se]['row_data'][$seff]['title'] = $sef->socio_economic_factors;
            $data["follow_up_rows"][$se]['row_data'][$seff]['edit_permission'] = 0;
            $seff++;
        }

    }

    $followup_template_list = $this->db->select("*")->from("followup_department")->where("department_id='" . $parameters['department_id'] . "'")->get()->result();
    if(count($followup_template_list)>0){
        $i = 0;
        foreach ($followup_template_list as $key => $value) {
            $follow_up = $this->Generic_model->getSingleRecord('followup', array('followup_id' => $value->followup_id), '');

            if(count($follow_array)>0)
            {
                if(in_array($follow_up->followup_id,$follow_array))
                {
                    $is_checked = 1;
                }else{
                    $is_checked=0;
                }
            }else{
                $is_checked = 0;
            }

            $data['template_list'][$i]['template_id'] = $follow_up->followup_id;
            $data['template_list'][$i]['template_name'] = $follow_up->followup_name;
            $data['template_list'][$i]['department_id'] = $value->department_id;
            $data['template_list'][$i]['is_checked'] = $is_checked;
            $i++;
        }
    }else{
        $data['template_list']=array();
    }


    $this->response(array('code' => '200', 'message' => 'Follow-up Template List', 'result' => $data, 'requestname' => $method));
}

    //creating followup template
public function create_followup_template($parameters, $method, $user_id) {
    $data['followup_name'] = $parameters['name'];
    $data['department_id'] = $parameters['department_id'];
    $data['created_by'] = $user_id;
    $data['modified_by'] = $user_id;
    $data['created_date_time'] = date("Y-m-d H:i:s");
    $data['modified_date_time'] = date("Y-m-d H:i:s");
    $data1 = $this->Generic_model->insertDataReturnId('patient_followup', $data);
    $this->response(array('code' => '200', 'message' => 'Followup Created Successfully', 'result' => $data1, 'requestname' => $method));
}

    //inserting followup template to patient
public function patient_followup_insert($parameters, $method, $user_id) {

    $app_info = $this->Generic_model->getSingleRecord('appointments',array('appointment_id'=>$parameters['appointment_id']),'');


    if(count($parameters['params_list'])>0)
    {
        for ($v = 0; $v < count($parameters['params_list']); $v++) {

            $follow_up_id = $this->db->select("GROUP_CONCAT(followup_id) as followup_id")->from("followup_parameter")->where("FIND_IN_SET('".$parameters['params_list'][$v]['param_id']."',parameter_id)")->get()->row();

            $check_exists = $this->db->select("GROUP_CONCAT(patient_followup_id) as patient_followup_id")->from("patient_followup")->where("appointment_id='".$parameters['appointment_id']."' and followup_id IN ('".$follow_up_id->followup_id."')")->get()->row();

            $pfa = explode(",",$check_exists->patient_followup_id);

            for($pf=0;$pf<count($pfa);$pf++)
            {
                $check = $this->db->select('*')->from('patient_followup_line_items')->where('patient_followup_id = "' . $pfa[$pf] . '" and parameter_id = "' . $parameters['params_list'][$v]['param_id'] . '" and appointment_id="' . $parameters['appointment_id'] . '"')->get()->row();
                if (count($check) <= 0) {
                    $data1['patient_followup_id'] = $pfa[$pf];
                    $data1['date'] = $parameters['date'];
                    $data1['visit_no'] = 1;
                    $data1['appointment_id'] = $parameters['appointment_id'];
                    $data1['parameter_id'] = $parameters['params_list'][$v]['param_id'];
                    $data1['parameter_value'] = $parameters['params_list'][$v]['value'];
                    $data1['created_by'] = $user_id;
                    $data1['modified_by'] = $user_id;
                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                    $data1['modified_date_time'] = date("Y-m-d H:i:s");

                    $this->Generic_model->insertData('patient_followup_line_items', $data1);
                }else {
                    $this->db->query('update patient_followup_line_items SET parameter_value="' . $parameters['params_list'][$v]['value'] . '" where patient_followup_id = "' . $check_exists->patient_followup_id . '" and parameter_id = "' . $parameters['params_list'][$v]['param_id'] . '" and appointment_id="' . $parameters['appointment_id'] . '" and patient_followup_line_item_id="'.$check->patient_followup_line_item_id.'"');
                }
            }
        }
    }

    $appointment_id = $prescription_drug['appointment_id'];
    $medication_complaince = $prescription_drug['medication_complaince'];
    $socio_economic_factors = $prescription_drug['socio_economic_factors'];
    $check_msef = $this->Generic_model->getSingleRecord('patient_msef_complaince',array('appointment_id'=>$parameters['appointment_id']),'');

    if(count($check_msef)>0)
    {
        $compliance = $parameters['compliance'];
        for($c=0;$c<count($compliance);$c++){
            $c_o[]=$compliance[$c]['value'];
        }
        $update_msef['medication_complaince'] = $c_o[0];
        $update_msef['socio_economic_factors'] = $c_o[1];
        $update_msef['modified_by'] = $user_id;
        $update_msef['modified_date_time'] = date('Y-m-d H:i:s');
        $this->Generic_model->updateData('patient_msef_complaince',$update_msef,array('patient_msef_complaince_id'=>$check_msef->patient_msef_complaince_id));
    }else{
        $compliance = $parameters['compliance'];
        for($c=0;$c<count($compliance);$c++){
            $c_o[]=$compliance[$c]['value'];
        }
        $insert_msef['appointment_id'] = $parameters['appointment_id'];
        $insert_msef['medication_complaince'] = $c_o[0];
        $insert_msef['socio_economic_factors'] = $c_o[1];
        $insert_msef['created_by'] = $user_id;
        $insert_msef['created_date_time'] = date('Y-m-d H:i:s');
        $insert_msef['modified_by'] = $user_id;
        $insert_msef['modified_date_time'] = date('Y-m-d H:i:s');
        $this->Generic_model->insertData('patient_msef_complaince',$insert_msef);
    }


    $this->response(array('code' => '200', 'message' => 'Followup Added Successfully', 'requestname' => $method));
}


    //inserting parameters in followup template popup
public function parameter_insert($parameters, $method, $user_id) {

    $data['parameter_name'] = $parameters['parameter_name'];
    if ($parameters['type'] == "Clinical Parameter") {
        $data['parameter_type'] = "Clinical";
    } else if ($parameters['type'] == "Lab Parameter") {
        $data['parameter_type'] = "Lab";
    }

    $data['low_range'] = $parameters['low_range'];
    $data['high_range'] = $parameters['high_range'];
        // $data['status'] = 1;
    $data['created_by'] = $user_id;
    $data['modified_by'] = $user_id;
    $data['created_date_time'] = date("Y-m-d H:i:s");
    $data['modified_date_time'] = date("Y-m-d H:i:s");

    $par_id = $this->Generic_model->insertDataReturnId('parameters', $data);

    $data1['template_id'] = $parameters['template_id'];
    $data1['parameter_id'] = $par_id;
    $data1['parameter_value'] = $parameters['value'];
    $data1['date'] = date("Y-m-d");
    $data1['status'] = 1;
    $data1['created_by'] = $user_id;
    $data1['modified_by'] = $user_id;
    $data1['created_date_time'] = date("Y-m-d H:i:s");
    $data1['modified_date_time'] = date("Y-m-d H:i:s");
    $this->Generic_model->insertData('followup_parameter', $data1);
    $this->response(array('code' => '200', 'message' => 'Parameter Added Successfully', 'requestname' => $method));
}



    /*     * **
      @Add Template
      @autor Narasimha
     * ** */

      public function addFollowup_template($parameters, $method, $user_id) {

        $app_info = $this->Generic_model->getSingleRecord('appointments',array('appointment_id'=>$parameters['appointment_id']),'');

        $templates = explode(",", $parameters['template_id']);

        for ($f = 0; $f < count($templates); $f++) {

            $check_exists = $this->db->select("*")->from("patient_followup")->where("patient_id='" . $app_info->patient_id . "' and doctor_id='" . $app_info->doctor_id . "' and followup_id='" . $templates[$f] . "' and appointment_id='" . $parameters['appointment_id'] . "' ")->get()->row();

            if (count($check_exists) == 0) {
                $data['followup_id'] = $templates[$f];
                $data['appointment_id'] = $parameters['appointment_id'];
                $data['patient_id'] = $app_info->patient_id;
                $data['doctor_id'] = $app_info->doctor_id;
                $data['created_by'] = $user_id;
                $data['modified_by'] = $user_id;
                $data['created_date_time'] = date("Y-m-d H:i:s");
                $data['modified_date_time'] = date("Y-m-d H:i:s");
                $pf_id[] = $this->Generic_model->insertDataReturnId('patient_followup', $data);
            } else {
                $pf_id[] = $check_exists->patient_followup_id;
            }
        }

        $f_id = implode(",",$pf_id);

        $follow_ids = $this->db->select("GROUP_CONCAT(followup_id) as followup_id")->from("patient_followup")->where("patient_followup_id in (".$f_id.")")->get()->row();
        $followup_id = $follow_ids->followup_id;

        $template_info = $this->db->select("*")->from("appointments")->where("clinic_id='" . $parameters['clinic_id'] . "' and patient_id ='" . $parameters['patient_id'] . "' and status!='reschedule' and status!='drop' and appointment_date <='" . date('Y-m-d') . "'")->group_by(array("appointment_date","appointment_id"))->get()->result();


        $data["follow_up_columns"][0]['column_id'] = "0";
        $data["follow_up_columns"][0]['title'] = "Visit No";
        $app_id = array();
        $i = 1;
        foreach ($template_info as $key => $value) {
            $data["follow_up_columns"][$i]['column_id'] = "$i";
            $data["follow_up_columns"][$i]['title'] = "$i";
            $app_id[] = $value->appointment_id;
            $app_dates[] = $value->appointment_date;
            $i++;
        }
        $k = 0;
        $data["follow_up_rows"][$k]['row_id'] = "1";
        $data["follow_up_rows"][$k]['row_data'][0]['column_id'] = "0";
        $data["follow_up_rows"][$k]['row_data'][0]['title'] = "Visit Date";
        $data["follow_up_rows"][$k]['row_data'][0]['edit_permission'] = 0;
        $j = 1;
        foreach ($template_info as $key => $value) {
            $data["follow_up_rows"][$k]['row_data'][$j]['column_id'] = "$j";
            $data["follow_up_rows"][$k]['row_data'][$j]['appointment_id'] = $value->appointment_id;
            $data["follow_up_rows"][$k]['row_data'][$j]['title'] = date('d-M-Y',strtotime($value->appointment_date));
            $data["follow_up_rows"][$k]['row_data'][$j]['edit_permission'] = 0;
            $j++;
        }

        $cd= 1;
        $data["follow_up_rows"][1]['row_id'] = "2";
        $data["follow_up_rows"][1]['row_data'][0]['column_id'] = "0";
        $data["follow_up_rows"][1]['row_data'][0]['title'] = "Clinical Diagnosis";
        $data["follow_up_rows"][1]['row_data'][0]['edit_permission'] = 0;
                //$cd=$l+1;

        $pcd=1;
        foreach($template_info as $key => $cd_info)
        {

            $pcd_info = $this->db->select("*")->from ("patient_clinical_diagnosis")->where(" appointment_id='".$cd_info->appointment_id."'")->group_by("appointment_id")->get()->result();
            $data["follow_up_rows"][1]['row_id'] = "2";
            $data["follow_up_rows"][1]['row_data'][$pcd]['appointment_id'] = $cd_info->appointment_id;
            $data["follow_up_rows"][1]['row_data'][$pcd]['column_id'] = "$pcd";
            $data["follow_up_rows"][1]['row_data'][$pcd]['title'] = "View";
            $data["follow_up_rows"][1]['row_data'][$pcd]['edit_permission'] = 0;
            $pcd++;
            $cd++;
        }


        $data["follow_up_rows"][2]['row_id'] = "3";
        $data["follow_up_rows"][2]['row_data'][0]['column_id'] = "0";
        $data["follow_up_rows"][2]['row_data'][0]['title'] = "Vitals";
        $data["follow_up_rows"][2]['row_data'][0]['edit_permission'] = 0;
        $m = 1;
        $l = 2;
        foreach ($template_info as $key => $value) {
            $data["follow_up_rows"][$l]['row_id'] = "$l";
            $data["follow_up_rows"][$l]['row_data'][$m]['column_id'] = "$m";
            $data["follow_up_rows"][$l]['row_data'][$m]['appointment_id'] = $value->appointment_id;
            $data["follow_up_rows"][$l]['row_data'][$m]['title'] = "";
            $data["follow_up_rows"][$l]['row_data'][$m]['edit_permission'] = 0;
            $m++;
        }



        $r = 3;
        $n = 0;
        $test = array();
        $join = implode(",", $app_id);
        $join2 = implode(",", $app_dates);
        foreach ($template_info as $key => $value) {
            $test[] = $this->db->select("*")->from("patient_vital_sign")->where("vital_sign_recording_date_time = (select max(vital_sign_recording_date_time) From patient_vital_sign where vital_sign_recording_date_time like '" . $value->appointment_date . "%')")->order_by("vital_sign_recording_date_time","DESC")->get()->result();
            $r++;
            $n++;
        }
        $vitals_details = $this->db->select("*")->from("patient_vital_sign")->where("appointment_id in (" . $join . ") and vital_sign NOT IN ('SBP','DBP')")->group_by(" vital_sign")->get()->result();

        $j = 0;
        $c = 3;
        foreach ($vitals_details as $key => $value) {

            $data["follow_up_rows"][$c]['row_id'] = "$c";
            $data["follow_up_rows"][$c]['row_data'][0]['column_id'] = "$rc";
            $data["follow_up_rows"][$c]['row_data'][0]['title'] = $value->vital_sign;
            $data["follow_up_rows"][$c]['row_data'][0]['edit_permission'] = 0;
            $i = 0;
            $rc = 1;
            foreach ($app_dates as $key2 => $value2) {
                $data["follow_up_rows"][$c]['row_id'] = "$c";
                $data["follow_up_rows"][$c]['row_data'][$rc]['appointment_id'] = $value->appointment_id;
                $data["follow_up_rows"][$c]['row_data'][$rc]['column_id'] = "$rc";
                $data["follow_up_rows"][$c]['row_data'][$rc]['title'] = getvitalsbydate($value2, $value->vital_sign);
                $data["follow_up_rows"][$c]['row_data'][$rc]['edit_permission'] = 0;
                $i++;
                $rc++;
            }
            $j++;
            $c++;
        }
        $sbp_dbp = $this->db->select("GROUP_CONCAT(DISTINCT(vital_sign)) as vital_sign")->from(" patient_vital_sign where appointment_id in (" . $join . ") and vital_sign IN ('SBP','DBP') ")->get()->row();
        if(count($sbp_dbp)>0)
        {

            $d=$c;
            $data["follow_up_rows"][$d]['row_id'] = "$d";
            $data["follow_up_rows"][$d]['row_data'][0]['column_id'] = "0";
            $data["follow_up_rows"][$d]['row_data'][0]['title'] = 'BP';
            $data["follow_up_rows"][$d]['row_data'][0]['edit_permission'] = 0;
            $rs=0;

            $a = 0;
            $rs = 1;
            foreach ($template_info as $key2 => $value2) {
                $sbp = $this->db->select("*")->from("patient_vital_sign")->where("vital_sign='SBP' and appointment_id in (".$value2->appointment_id.")")->order_by(" patient_vital_id","desc")->get()->row();
                $dbp = $this->db->select("*")->from("patient_vital_sign")->where("vital_sign='DBP' and appointment_id in (".$value2->appointment_id.")")->order_by("patient_vital_id desc ")->get()->row();
                        //echo $this->db->last_query();exit();
                $data["follow_up_rows"][$d]['row_id'] = "$d";
                $data["follow_up_rows"][$d]['row_data'][$rs]['appointment_id'] = $value2->appointment_id;
                $data["follow_up_rows"][$d]['row_data'][$rs]['column_id'] = "$rs";
                $data["follow_up_rows"][$d]['row_data'][$rs]['title'] = $sbp->vital_result." / ".$dbp->vital_result;
                $data["follow_up_rows"][$d]['row_data'][$rs]['edit_permission'] = 0;
                $a++;
                $rs++;
            }
            $d = $d+1;
        }else{
            $d = $d;
        }



        $pci = $this->db->select("GROUP_CONCAT(parameter_id) as parameter_id")->from(" followup_parameter")->where("followup_id IN (" . $followup_id . ")")->get()->row();
        $params = $pci->parameter_id;
                // Clinical 
        $pc_info = $this->db->select("*")->from("parameters")->where("parameter_type='Clinical' and parameter_id IN (" . $params . ")")->get()->result();

        $data["follow_up_rows"][$d]['row_id'] = "$d";
        $data["follow_up_rows"][$d]['row_data'][0]['column_id'] = "0";
        $data["follow_up_rows"][$d]['row_data'][0]['title'] = "Clinical Parameters";
        $data["follow_up_rows"][$d]['row_data'][0]['edit_permission'] = 0;
        $cp = 1;
        foreach ($template_info as $key => $value) {
            $data["follow_up_rows"][$d]['row_id'] = "$d";
            $data["follow_up_rows"][$d]['row_data'][$cp]['column_id'] = "$cp";
            $data["follow_up_rows"][$d]['row_data'][$cp]['appointment_id'] = $value->appointment_id;
            $data["follow_up_rows"][$d]['row_data'][$cp]['title'] = "";
            $data["follow_up_rows"][$d]['row_data'][$cp]['edit_permission'] = 0;
            $cp++;
        }
        $pr = $d + 1;
        $cr = 1;
        foreach ($pc_info as $key => $pvalue) {

            $data["follow_up_rows"][$pr]['row_id'] = "$pr";
            $data["follow_up_rows"][$pr]['row_data'][0]['column_id'] = "0";
            $data["follow_up_rows"][$pr]['row_data'][0]['title'] = $pvalue->parameter_name;
            $data["follow_up_rows"][$pr]['row_data'][0]['edit_permission'] = 0;
            $pcr = 1;
            $fcr = 1;
            foreach ($app_id as $key => $dvalue) {

                $data["follow_up_rows"][$pr]['row_id'] = "$pr";
                $data["follow_up_rows"][$pr]['row_data'][$pcr]['column_id'] = "$fcr";
                $data["follow_up_rows"][$pr]['row_data'][$pcr]['appointment_id'] = $dvalue;
                $data["follow_up_rows"][$pr]['row_data'][$pcr]['title'] = getparameters($pvalue->parameter_id, $dvalue);
                $data["follow_up_rows"][$pr]['row_data'][$pcr]['edit_permission'] = 0;
                $pcr++;
                $fcr++;
            }
            $pr++;
            $cr++;
        }
                // //Lab
        $pl_info = $this->db->select("*")->from("parameters")->where("parameter_type='Lab' and parameter_id IN (" . $params . ")")->get()->result();


        $data["follow_up_rows"][$pr]['row_id'] = "$pr";
        $data["follow_up_rows"][$pr]['row_data'][0]['column_id'] = "0";
        $data["follow_up_rows"][$pr]['row_data'][0]['title'] = "Lab Parameters";
        $data["follow_up_rows"][$pr]['row_data'][0]['edit_permission'] = 0;

        $lp = 1;
        foreach ($template_info as $key => $value) {
            $data["follow_up_rows"][$pr]['row_id'] = "$pr";
            $data["follow_up_rows"][$pr]['row_data'][$lp]['column_id'] = "$lp";
            $data["follow_up_rows"][$pr]['row_data'][$lp]['appointment_id'] = $value->appointment_id;
            $data["follow_up_rows"][$pr]['row_data'][$lp]['title'] = "";
            $data["follow_up_rows"][$pr]['row_data'][$lp]['edit_permission'] = 0;
            $lp++;
        }

        $pl = $pr + 1;
        $cl = 1;

        foreach ($pl_info as $key => $plvalue) {
            $name = $pvalue->parameter_id;
            $data["follow_up_rows"][$pl]['row_id'] = "$pl";
            $data["follow_up_rows"][$pl]['row_data'][0]['column_id'] = "0";
            $data["follow_up_rows"][$pl]['row_data'][0]['title'] = $plvalue->parameter_name;
            $data["follow_up_rows"][$pl]['row_data'][0]['edit_permission'] = 0;

            $pcl = 1;
            $fcl = 1;
            foreach ($app_id as $key => $dvalue) {
                $data["follow_up_rows"][$pl]['row_id'] = "$pl";
                $data["follow_up_rows"][$pl]['row_data'][$pcl]['appointment_id'] = $dvalue;
                $data["follow_up_rows"][$pl]['row_data'][$pcl]['column_id'] = "$fcl";
                $data["follow_up_rows"][$pl]['row_data'][$pcl]['title'] = getparameters($plvalue->parameter_id, $dvalue);
                $data["follow_up_rows"][$pl]['row_data'][$pcl]['edit_permission'] = 0;
                $pcl++;
                $fcl++;
            }
            $pl++;
            $cl++;
        }

        $r = 1;
        $s = 0;
        foreach ($template_info as $key => $vinfo) {
            $data["visits_info"][$s]['visit_no'] = $r;
            $data["visits_info"][$s]['date'] = $vinfo->appointment_date;

            $pci = $this->db->select("GROUP_CONCAT(parameter_id) as parameter_id")->from(" followup_parameter")->where("followup_id in (" . $followup_id . " )")->get()->row();

            $pc_info = $this->db->select("*")->from("parameters")->where("parameter_type='Clinical' and parameter_id IN (" . $pci->parameter_id . ")")->get()->result();
            $pl_info = $this->db->select("*")->from("parameters")->where("parameter_type='Lab' and parameter_id IN(" . $pci->parameter_id . ")")->get()->result();
            $pc = 0;
            foreach ($pc_info as $key => $value) {
                $data["visits_info"][$s]['Clinical_params'][$pc]['param_id'] = "$value->parameter_id";
                $data["visits_info"][$s]['Clinical_params'][$pc]['param'] = $value->parameter_name;
                $data["visits_info"][$s]['Clinical_params'][$pc]['value'] = getparameters($value->parameter_id, $vinfo->appointment_id);
                $pc++;
            }
            $pl = 0;
            foreach ($pl_info as $key => $value2) {
                $data["visits_info"][$s]['Lab_parameters'][$pl]['param_id'] = "$value2->parameter_id";
                $data["visits_info"][$s]['Lab_parameters'][$pl]['param'] = $value2->parameter_name;
                $data["visits_info"][$s]['Lab_parameters'][$pl]['value'] = getparameters($value2->parameter_id, $vinfo->appointment_id);
                $pl++;
            }
            $msef_complaince = $this->Generic_model->getSingleRecord('patient_msef_complaince',array('appointment_id'=>$vinfo->appointment_id),'');

            if(count($msef_complaince)>0)
            {


                $com=0;
                $data["visits_info"][$s]['Compliance'][$com]['param_id'] = "$msef_complaince->patient_msef_complaince_id";
                $data["visits_info"][$s]['Compliance'][$com]['param'] = "Medication Compliance & Side Effects";
                $data["visits_info"][$s]['Compliance'][$com]['value'] = $msef_complaince->medication_complaince;

                $data["visits_info"][$s]['Compliance'][$com+1]['param_id'] = "$msef_complaince->patient_msef_complaince_id";
                $data["visits_info"][$s]['Compliance'][$com+1]['param'] = "Socio Economic Factors";
                $data["visits_info"][$s]['Compliance'][$com+1]['value'] = $msef_complaince->socio_economic_factors;

            }else{

                $com=0;
                $data["visits_info"][$s]['Compliance'][$com]['param_id'] = "0";
                $data["visits_info"][$s]['Compliance'][$com]['param'] = "Medication Compliance & Side Effects";
                $data["visits_info"][$s]['Compliance'][$com]['value'] = "";

                $data["visits_info"][$s]['Compliance'][$com+1]['param_id'] = "0";
                $data["visits_info"][$s]['Compliance'][$com+1]['param'] = "Socio Economic Factors";
                $data["visits_info"][$s]['Compliance'][$com+1]['value'] = "";
            }


            $pf_info = $this->db->select("*")->from("patient_followup")->where("appointment_id='".$vinfo->appointment_id."'")->get()->result();
            $pfi = 0;
            foreach ($pf_info as $key =>$pfi_value) {

                $data["added_templete_ids"][$pfi]['patient_followup_id'] = $pfi_value->patient_followup_id;
                $data["added_templete_ids"][$pfi]['templete_id']= $pfi_value->followup_id;
                $pfi++;
            }
            $r++;
            $s++;
        }

        $inv= $pl+$pr+1;
        $data["follow_up_rows"][$inv]['row_id'] = "$inv";
        $data["follow_up_rows"][$inv]['row_data'][0]['column_id'] = "0";
        $data["follow_up_rows"][$inv]['row_data'][0]['title'] = "Investigations";
        $data["follow_up_rows"][$inv]['row_data'][0]['edit_permission'] = 0;


        $ie=1;
        foreach($template_info as $key => $cd_info)
        {
            $data["follow_up_rows"][$inv]['row_id'] = "$inv";
            $data["follow_up_rows"][$inv]['row_data'][$ie]['appointment_id'] = $cd_info->appointment_id;
            $data["follow_up_rows"][$inv]['row_data'][$ie]['column_id'] = "$ie";
            $data["follow_up_rows"][$inv]['row_data'][$ie]['title'] = "View";
            $data["follow_up_rows"][$inv]['row_data'][$ie]['edit_permission'] = 0;
            $ie++;
        }


        $mn= $inv+1;
        $data["follow_up_rows"][$mn]['row_id'] = "$mn";
        $data["follow_up_rows"][$mn]['row_data'][0]['column_id'] = "0";
        $data["follow_up_rows"][$mn]['row_data'][0]['title'] = "Medications";
        $data["follow_up_rows"][$mn]['row_data'][0]['edit_permission'] = 0;
                //$cd=$l+1;

        $me=1;
        foreach($template_info as $key => $cd_info)
        {
            $data["follow_up_rows"][$mn]['row_id'] = "$mn";
            $data["follow_up_rows"][$mn]['row_data'][$me]['appointment_id'] = $cd_info->appointment_id;
            $data["follow_up_rows"][$mn]['row_data'][$me]['column_id'] = "$me";
            $data["follow_up_rows"][$mn]['row_data'][$me]['title'] = "View";
            $data["follow_up_rows"][$mn]['row_data'][$me]['edit_permission'] = 0;
            $me++;
        }

        $mc= $mn+1;
        $data["follow_up_rows"][$mc]['row_id'] = "$mc";
        $data["follow_up_rows"][$mc]['row_data'][0]['column_id'] = "0";
        $data["follow_up_rows"][$mc]['row_data'][0]['title'] = "Medical Compliance & Side Effects";
        $data["follow_up_rows"][$mc]['row_data'][0]['edit_permission'] = 0;
                //$cd=$l+1;

        $mco=1;
        foreach($template_info as $key => $cd_info)
        {
            $medication_complaince = $this->Generic_model->getSingleRecord('patient_msef_complaince',array('appointment_id'=>$cd_info->appointment_id),'');
            $data["follow_up_rows"][$mc]['row_id'] = "$mc";
            $data["follow_up_rows"][$mc]['row_data'][$mco]['appointment_id'] = $cd_info->appointment_id;
            $data["follow_up_rows"][$mc]['row_data'][$mco]['column_id'] = "$mco";
            $data["follow_up_rows"][$mc]['row_data'][$mco]['title'] = $medication_complaince->medication_complaince;
            $data["follow_up_rows"][$mc]['row_data'][$mco]['edit_permission'] = 0;
            $mco++;
        }

        $se= $mc+1;
        $data["follow_up_rows"][$se]['row_id'] = "$se";
        $data["follow_up_rows"][$se]['row_data'][0]['column_id'] = "0";
        $data["follow_up_rows"][$se]['row_data'][0]['title'] = "Socio Economic Factors";
        $data["follow_up_rows"][$se]['row_data'][0]['edit_permission'] = 0;
                //$cd=$l+1;

        $seff=1;
        foreach($template_info as $key => $cd_info)
        {
            $sef = $this->Generic_model->getSingleRecord('patient_msef_complaince',array('appointment_id'=>$cd_info->appointment_id),'');
            $data["follow_up_rows"][$se]['row_id'] = "$se";
            $data["follow_up_rows"][$se]['row_data'][$seff]['appointment_id'] = $cd_info->appointment_id;
            $data["follow_up_rows"][$se]['row_data'][$seff]['column_id'] = "$seff";
            $data["follow_up_rows"][$se]['row_data'][$seff]['title'] = $sef->socio_economic_factors;
            $data["follow_up_rows"][$se]['row_data'][$seff]['edit_permission'] = 0;
            $seff++;
        }

        for ($pf = 0; $pf < count($pf_id); $pf++) {
            $data['patient_followup'][$pf]['patient_followup_id'] = $pf_id[$pf];
        }


        $this->response(array('code' => '200', 'message' => 'Template View', 'result' => $data, 'requestname' => $method));
    }

    // MASTERS CONCERN TO THE DRUGS

    public function drugs_master($parameters, $method, $user_id) {

        if(file_exists("./uploads/drugs_master/" . $parameters['clinic_id'] . "_masters.zip")){
            // echo "File Exists";
        }else{
            // echo "File Not Exists";

            // DRUG MASTER  
            // Get all the drug for drug master using getallRecords function    
            $res = $this->db->select('COUNT(*) as count')->from('drug')->get()->row();
            $drugs_count = $res->count;
            $partCount = round($drugs_count/30000);

            $x = 0;
            $y = 30000;

            for($j=1; $j<=$partCount; $j++) {
                if($j == $partCount){
                    $y = $drugs_count + 10000;
                }
                $drug_records = $this->db->select("*")->FROM("drug")->where("drug_id >= '".$x."' AND drug_id < '".$y."'")->order_by("drug_id")->get()->result();

                $i = 0;

                if (count($drug_records) > 0) {

                    foreach ($drug_records as $drug) {

                        unset($drug->created_by);
                        unset($drug->created_date_time);
                        unset($drug->modified_by);
                        unset($drug->modified_date_time);

                        $drugs['drug'][$i] = $drug;
                        $i++;
                    }
                } else {
                    $drugs['drug'] = array();
                }

                // make a physical file for drug master
                $drug_file = 'drug_master_part_'.$j.'.json';
                $drugs_data = json_encode($drugs);
                $this->zip->add_data($drug_file, $drugs_data);

                $x = $y + 1;
                $y = $y + 30000;            
            }


            // SALTS MASTER
            // below code retunrs all the available salts
            $salt_records = $this->Generic_model->getAllRecords("salt", $condition = '', $order = '');

            $i = 0;

            if (count($salt_records) > 0) {
                foreach ($salt_records as $salt) {

                    unset($salt->created_by);
                    unset($salt->created_date_time);
                    unset($salt->modified_by);
                    unset($salt->modified_date_time);

                    $salts['salt'][$i] = $salt;
                    $i++;
                }
            } else {
                $salts['salt'] = array();
            }

            // make a physical file for salt master
            $salt_file = 'salt_master.json';
            $salts_data = json_encode($salts);
            $this->zip->add_data($salt_file, $salts_data);

            //condtion contraindications
            $condition_contraindications = $this->Generic_model->getAllRecords("condition_contraindication", $condition = '', $order = '');

            $i = 0;

            if (count($condition_contraindications) > 0) {
                foreach ($condition_contraindications as $c_ind) {



                    $cind['condition'][$i] = $c_ind->condition;
                    $cind['contraindication'][$i] = trim($c_ind->contraindication,",");
                    $cind['salt_id'][$i] = trim($c_ind->salt_id);
                    $i++;
                }
            } else {
                $cind[] = array();
            }
            $cond_file = 'condition_contraindication.json';
            $cond_data = json_encode($cind);
            $this->zip->add_data($cond_file, $cond_data);

             // investigations masters json
            $investigation_details = $this->Generic_model->getAllRecords('investigations', $condition = '', $order = '');
            $in = 0;
            if (count($investigation_details) > 0) {
                foreach ($investigation_details as $investigation) {
                    $inv['investigation'][$in]['investigation_id'] = $investigation->investigation_id;
                    $inv['investigation'][$in]['investigation_code'] = $investigation->item_code;
                    $inv['investigation'][$in]['investigation_name'] = $investigation->investigation;
                    $inv['investigation'][$in]['category'] = $investigation->category;

                    $in++;
                }
            } else {
                $inv['investigation'] = array();
            }
            $inv_file = 'investigations_master.json';
            $inv_data = json_encode($inv);
            $this->zip->add_data($inv_file, $inv_data);


            //clinic investigations json
            $c_condition['clinic_id'] = $parameters['clinic_id'];
            $clinic_inv_details = $this->db->select("*")->from("clinic_investigations ci")->join("investigations i","ci.investigation_id = i.investigation_id")->get()->result();
            $in = 0;
            if (count($clinic_inv_details) > 0) {
                foreach ($clinic_inv_details as $c_investigation) {
                    $clinic_inv['investigation'][$in]['clinic_investigation_id'] = $c_investigation->clinic_investigation_id;
                    $clinic_inv['investigation'][$in]['investigation_id'] = $c_investigation->investigation_id;
                    $clinic_inv['investigation'][$in]['investigation_code'] = $c_investigation->item_code;
                    $clinic_inv['investigation'][$in]['investigation_name'] = $c_investigation->investigation;
                    $clinic_inv['investigation'][$in]['category'] = $c_investigation->category;
                    $clinic_inv['investigation'][$in]['mrp'] = $c_investigation->price;


                    $in++;
                }
            } else {
                $clinic_inv['investigation'] = array();
            }
            $clinic_inv_file = 'clinic_investigations.json';
            $clinic_inv_data = json_encode($clinic_inv);
            $this->zip->add_data($clinic_inv_file, $clinic_inv_data);


            // Clinical Diagnosis Masters
            $clinical_diagnosis_master = $this->db->select('clinical_diagnosis_id, disease_name, code')->from('clinical_diagnosis')->where('status =',1)->get()->result_array();
            
            if (count($clinical_diagnosis_master) > 0) {
                $cntr = 0;
                foreach ($clinical_diagnosis_master as $clinical_diagnosis) {
                    $clinicalDiagnosis['Clinical_Diagnosis'][$cntr]= $clinical_diagnosis;
                    $cntr++;
                }
            } else {
                $clinicalDiagnosis['Clinical_Diagnosis'] = array();
            }
            $cd_file = 'clinical_diagnosis_master.json';
            $cd_data = json_encode($clinicalDiagnosis);
            $this->zip->add_data($cd_file, $cd_data);


            // get salt-contraindications records for masters
            $sci_details = $this->Generic_model->getAllRecords("salt_contraindication", $condition = '', $order = '');

            $sc = 0;

            if (count($sci_details) > 0) {
                foreach ($sci_details as $sci) {
                    $s_contraindication['s_contraindication'][$sc]['salt_contraindication_id'] = $sci->salt_contraindication_id;
                    $s_contraindication['s_contraindication'][$sc]['salt_id'] = $sci->salt_id;
                    $s_contraindication['s_contraindication'][$sc]['contraindication'] = $sci->contraindication;
                    $sc++;
                }
            } else {
                $s_contraindication['s_contraindication'] = array();
            }

            $s_contraindication_file = 's_contraindication_master.json';
            $s_contraindication_data = json_encode($s_contraindication);
            $this->zip->add_data($s_contraindication_file, $s_contraindication_data);


            // PHRAMACY INVENTORY
            // below code returns all the pharmacy inventory with batch no., quantity w.r.to clinic
            $inward = $this->db->select("clinic_id, batch_no, drug_id, sum(quantity) as quantity_supplied, expiry_date, status")->from("clinic_pharmacy_inventory_inward")->where("clinic_id = '" . $parameters['clinic_id'] . "' AND status = 1")->group_by(array("batch_no","drug_id"))->get()->result();

            $i = 0;
            if (count($inward) > 0) {
                foreach ($inward as $pharmacy_inventory) {

                    $outward = $this->db->select("batch_no, drug_id, sum(quantity) as quantity_sold")->from("clinic_pharmacy_inventory_outward")->where("batch_no = '" . $pharmacy_inventory->batch_no . "' AND drug_id = '" . $pharmacy_inventory->drug_id . "' AND clinic_id = '" . $parameters['clinic_id'] . "'")->group_by(array("batch_no","drug_id"))->get()->row();

                    if (count($outward)) {
                        $qty_supplied = $pharmacy_inventory->quantity_supplied;
                        $qty_sold = $outward->quantity_sold;

                        // available quantity
                        $pharmacy_inventory->available_quantity = ($qty_supplied - $qty_sold);
                    } else {
                        // available quantity
                        $pharmacy_inventory->available_quantity = (int) ($pharmacy_inventory->quantity_supplied);
                    }

                    $pinventory['p_inventory'][$i] = $pharmacy_inventory;
                    $i++;
                }
            } else {
                $pinventory['p_inventory'] = array();
            }
            $pinventory_file = 'pharmacy_inventory.json';
            $pinventory_data = json_encode($pinventory);
            $this->zip->add_data($pinventory_file, $pinventory_data);


            // PHARMACY INVENTORY RE-ORDER LEVEL n DISCOUNTS INFORMATION
            // below code will return all the re-order level, discount information of the drug inventory
            $drugDiscounts = $this->db->select("*")->from("clinic_pharmacy_inventory")->where("clinic_id = '" . $parameters['clinic_id'] . "'")->get()->result();

            $i = 0;
            if (count($drugDiscounts) > 0) {
                foreach ($drugDiscounts as $discountRec) {

                    unset($discountRec->created_by);
                    unset($discountRec->created_date_time);
                    unset($discountRec->modified_by);
                    unset($discountRec->modified_date_time);

                    $discounts['standards'][$i] = $discountRec;
                    $i++;
                }
            } else {
                $discounts['standards'] = array();
            }

            // make a physical file for discount n reorder level master
            $discount_file = 'drug_standards.json';
            $discounts_data = json_encode($discounts);
            $this->zip->add_data($discount_file, $discounts_data);


            // CONSENT FORMS
            // below code will return all the consent forms available w.r.to the department
            // $consent_forms = $this->Generic_model->getAllRecords("consent_forms")
            $this->zip->archive("./uploads/drugs_master/" . $parameters['clinic_id'] . "_masters.zip");
        }
        
        $patient_val['path'] = "uploads/drugs_master/" . $parameters['clinic_id'] . "_masters.zip";

        $this->response(array('code' => '200', 'message' => 'success ', 'result' => $patient_val, 'requestname' => $method));
    }


    // Dashboard service for all users based on role
    public function commonDashboard($parameters, $method, $user_id) {

        $clinic_id = $parameters['clinic_id'];
        $role_id = $parameters['role_id'];
        $profile_id = $parameters['profile_id'];
        $dashboard_type = $parameters['dashboard_type'];
        $from_date = $parameters['from_date'];
        $to_date = $parameters['to_date'];
        $today = date('Y-m-d');

        if ($role_id == 6) { // Nurse
            $data['leftPane']['header'] = 'ALL APPOINTMENTS';
            $patients = $this->db->select("*,a.status as pstatus,b.first_name as pfname,b.gender as pgender,b.last_name as plname")->from("appointments a")->join("patients b","a.patient_id=b.patient_id")->join("doctors c","a.doctor_id=c.doctor_id")->join("department d","c.department_id=d.department_id")->where("(a.status='vital_signs' or a.status='checked_in') and a.clinic_id=" . $clinic_id . " and a.appointment_date='".$today."'")
            ->order_by("FIELD(a.status,'vital_signs','checked_in')")
            ->order_by("FIELD(a.priority, 'pregnancy', 'elderly', 'children','none') ")
            ->order_by("a.appointment_time_slot","asc")
            ->order_by("a.appointment_date","asc")
            ->order_by("a.check_in_time","asc")->get()->result_array();

            
            $i = 0;
            foreach ($patients as $result) {
                if ($result["qrcode"] != NULL) {
                    $qrcode = base_url() . 'uploads/qrcodes/patients/' . $result["qrcode"];
                } else {
                    $qrcode = NULL;
                }
                $data['leftPane']['PatientsList'][$i]['patient_id'] = $result['patient_id'];
                $data['leftPane']['PatientsList'][$i]['clinic_id'] = $result['clinic_id'];
                $data['leftPane']['PatientsList'][$i]['first_name'] = $result['pfname'];
                $data['leftPane']['PatientsList'][$i]['last_name'] = $result['plname'];
                $data['leftPane']['PatientsList'][$i]['age_unit'] = $result['age_unit'];
                $data['leftPane']['PatientsList'][$i]['title'] = $result['title'];
                $data['leftPane']['PatientsList'][$i]['umr_no'] = $result['umr_no'];
                $data['leftPane']['PatientsList'][$i]['age'] = $result['age'];
                $data['leftPane']['PatientsList'][$i]['gender'] = $result['pgender'];
                $data['leftPane']['PatientsList'][$i]['qrcode'] = $qrcode;
                $data['leftPane']['PatientsList'][$i]['priority'] = $result['priority'];
                $data['leftPane']['PatientsList'][$i]['appointment_id'] = $result['appointment_id'];
                $data['leftPane']['PatientsList'][$i]['appointment_date'] = $result['appointment_date'];
                $data['leftPane']['PatientsList'][$i]['appointment_time'] = $result['appointment_time_slot'];
                $data['leftPane']['PatientsList'][$i]['doctor_id'] = $result['doctor_id'];
                $data['leftPane']['PatientsList'][$i]['doctor'] = $result['first_name'] . " " . $result['last_name'];
                $data['leftPane']['PatientsList'][$i]['address'] = $result['address_line'] . "," . $result['district_name'] . "," . $result['state_name'] . "," . $result['ppcode'];

                //eliminate comms
                $data['leftPane']['PatientsList'][$i]['address'] = $this->eliminateComma($data['leftPane']['PatientsList'][$i]['address']);

                $data['leftPane']['PatientsList'][$i]['contact'] = DataCrypt($result['mobile'],'decrypt');
                $data['leftPane']['PatientsList'][$i]['date_of_birth'] = $result['date_of_birth'];
                $data['leftPane']['PatientsList'][$i]['color_code'] = $result['color_code'];
                $data['leftPane']['PatientsList'][$i]['designation'] = $result['qualification'];
                $data['leftPane']['PatientsList'][$i]['department'] = $result['department_name'];
                $data['leftPane']['PatientsList'][$i]['department_id'] = $result['department_id'];
                $data['leftPane']['PatientsList'][$i]['photo'] = $result['photo'];
                $data['leftPane']['PatientsList'][$i]['check-in-time'] = $result['check_in_time'];
                $data['leftPane']['PatientsList'][$i]['status'] = $result['pstatus'];
                $data['leftPane']['PatientsList'][$i]['waiting_time'] = '';
                $data['leftPane']['PatientsList'][$i]['prescription_id'] = '';
                $data['leftPane']['PatientsList'][$i]['investigation_id'] = '';
                $data['leftPane']['PatientsList'][$i]['sub_list'] = array();
                $i++;
            }
            $data['rightPane']['header'] = 'ANALYTICS';
            $data['rightPane']['analyticalList'][0]['number'] = count($patients);
            $data['rightPane']['analyticalList'][0]['title'] = "Appointments";
            $data['rightPane']['analyticalList'][0]['split'] = array();
        }
        
        if($role_id == 4){ // Doctor

          $data['leftPane']['header'] = 'ALL APPOINTMENTS';
          $patients = $this->db->select("*,a.status as pstatus,b.first_name as pfname,b.gender as pgender,b.last_name as plname")->from("appointments a")->join("patients b","a.patient_id=b.patient_id")->join("doctors c","a.doctor_id=c.doctor_id")->join("department d","c.department_id=d.department_id")->where("(a.status='in_consultation' or a.status='waiting') and a.clinic_id=" . $clinic_id . " and a.doctor_id='".$user_id."' and a.appointment_date = '" . $today . "'")
          ->order_by("FIELD(a.status,'in_consultation','waiting')")
          ->order_by("FIELD(a.priority, 'sick','pregnancy', 'elderly', 'children','other','none')")
          ->order_by("a.check_in_time","desc")
          ->order_by("a.appointment_time_slot","desc")
          ->order_by("a.appointment_date","desc")->get()->result_array();

          $i = 0;
          foreach ($patients as $result) {
            if ($result["qrcode"] != NULL) {
                $qrcode = base_url() . 'uploads/qrcodes/patients/' . $result["qrcode"];
            } else {
                $qrcode = NULL;
            }
            $data['leftPane']['PatientsList'][$i]['patient_id'] = $result['patient_id'];
            $data['leftPane']['PatientsList'][$i]['clinic_id'] = $result['clinic_id'];
            $data['leftPane']['PatientsList'][$i]['first_name'] = $result['pfname'];
            $data['leftPane']['PatientsList'][$i]['last_name'] = $result['plname'];
            $data['leftPane']['PatientsList'][$i]['age_unit'] = $result['age_unit'];
            $data['leftPane']['PatientsList'][$i]['title'] = $result['title'];
            $data['leftPane']['PatientsList'][$i]['umr_no'] = $result['umr_no'];
            $data['leftPane']['PatientsList'][$i]['age'] = $result['age'];
            $data['leftPane']['PatientsList'][$i]['gender'] = $result['pgender'];
            $data['leftPane']['PatientsList'][$i]['qrcode'] = $qrcode;
            $data['leftPane']['PatientsList'][$i]['priority'] = $result['priority'];
            $data['leftPane']['PatientsList'][$i]['appointment_id'] = $result['appointment_id'];
            $data['leftPane']['PatientsList'][$i]['appointment_date'] = $result['appointment_date'];
            $data['leftPane']['PatientsList'][$i]['appointment_time'] = $result['appointment_time_slot'];
            $data['leftPane']['PatientsList'][$i]['doctor_id'] = $result['doctor_id'];
            $data['leftPane']['PatientsList'][$i]['doctor'] = $result['first_name'] . " " . $result['last_name'];
            $data['leftPane']['PatientsList'][$i]['address'] = $result['address_line'] . "," . $result['district_name'] . "," . $result['state_name'] . "," . $result['ppcode'];

                //eliminate comms
            $data['leftPane']['PatientsList'][$i]['address'] = $this->eliminateComma($data['leftPane']['PatientsList'][$i]['address']);

            $data['leftPane']['PatientsList'][$i]['contact'] =DataCrypt( $result['mobile'],'decrypt');
            $data['leftPane']['PatientsList'][$i]['date_of_birth'] = $result['date_of_birth'];
            $data['leftPane']['PatientsList'][$i]['color_code'] = $result['color_code'];
            $data['leftPane']['PatientsList'][$i]['designation'] = $result['qualification'];
            $data['leftPane']['PatientsList'][$i]['department'] = $result['department_name'];
            $data['leftPane']['PatientsList'][$i]['department_id'] = $result['department_id'];
            $data['leftPane']['PatientsList'][$i]['photo'] = $result['photo'];
            $data['leftPane']['PatientsList'][$i]['check-in-time'] = $result['check_in_time'];
            $data['leftPane']['PatientsList'][$i]['status'] = $result['pstatus'];
            $data['leftPane']['PatientsList'][$i]['waiting_time'] = '';
            $data['leftPane']['PatientsList'][$i]['prescription_id'] = '';
            $data['leftPane']['PatientsList'][$i]['investigation_id'] = '';
            $data['leftPane']['PatientsList'][$i]['sub_list'] = array();
            $i++;
        }

        if(trim($from_date) == trim($to_date)){
            $data['rightPane']['header'] = 'TODAY ('.date('d M. Y', strtotime($from_date)).')';    
        }else{
            $data['rightPane']['header'] = 'FROM ('.date('d M. Y', strtotime($from_date)).' - '.date('d M. Y', strtotime($to_date)).')';
        }
        

        // Get Total Billing records for today
        $this->db->select('billing_id, billing_type, clinic_id, appointment_id, doctor_id, patient_id, total_amount, discount, discount_unit, created_date_time, deposit_date');
        $this->db->from('billing');
        $this->db->where('clinic_id =',$clinic_id);
        $this->db->where('doctor_id =',$user_id);

        if(trim($from_date) == trim($to_date)){
            $this->db->like("created_date_time",$from_date);
        }else{
            $this->db->where("(created_date_time between '".$from_date."%' AND '".$to_date."%')");
        }

        $finances = $this->db->get()->result_array();

        $totalRevenue = 0;
        $totalDiscount = 0;
        
        $totalConsultationRevenue = 0;
        $totalConsultations = 0;
        
        $totalProcedureRevenue = 0;
        $totalProcedures = 0;

        $totalPharmacyRevenue = 0;
        $totalPrescriptions = 0;

        if(count($finances) > 0){
            foreach($finances as $financeRec){
                $sql = "SELECT billing_line_item_id, amount, discount, discount_unit, created_date_time,  
                IF(discount_unit = 'INR',amount-discount, IF(discount_unit = '%', ROUND(amount - (amount*discount/100),2), amount))
                AS payable_amount,
                IF(discount_unit = 'INR',discount, IF(discount_unit = '%', ROUND((amount*discount)/100,2), 0))
                AS discounted_amount
                FROM billing_line_items WHERE billing_id = ?";

                $itemRec = $this->db->query($sql, array($financeRec['billing_id']))->row(); 

                if($financeRec['billing_type'] == 'Registration & Consultation' || $financeRec['billing_type'] == 'Consultation'){
                    $totalConsultationRevenue = $totalConsultationRevenue + $itemRec->payable_amount;
                    $totalConsultations = $totalConsultations + 1;
                }else if($financeRec['billing_type'] == 'Procedure'){
                    $totalProcedureRevenue = $totalProcedureRevenue + $itemRec->payable_amount;
                    $totalProcedures = $totalProcedures + 1;
                }else if($financeRec['billing_type'] == 'Pharmacy'){
                    $totalPharmacyRevenue = $totalPharmacyRevenue + $itemRec->payable_amount;
                    $totalPrescriptions = $totalPrescriptions + 1;
                }

                $totalRevenue = $totalRevenue + $itemRec->payable_amount;
                $totalDiscount = $totalDiscount + $itemRec->discounted_amount;
            }
        }

        // Ready the right pane JSON
        $data['rightPane']['analyticalList'][0]['number'] = NULL;
        $data['rightPane']['analyticalList'][0]['title'] = "Finances";
        $data['rightPane']['analyticalList'][0]['split'][0]['number'] = NULL;
        $data['rightPane']['analyticalList'][0]['split'][0]['title'] = "Revenue";
        $data['rightPane']['analyticalList'][0]['split'][0]['value'] = $totalRevenue;

        $data['rightPane']['analyticalList'][0]['split'][1]['number'] = NULL;
        $data['rightPane']['analyticalList'][0]['split'][1]['title'] = "Discounts";
        $data['rightPane']['analyticalList'][0]['split'][1]['value'] = $totalDiscount;

        // Consultation JSON
        $data['rightPane']['analyticalList'][1]['number'] = (int)$totalConsultations;
        $data['rightPane']['analyticalList'][1]['title'] = "Consultation";
        $data['rightPane']['analyticalList'][1]['value'] = $totalConsultationRevenue;

        // Procedure Revenue JSON
        $data['rightPane']['analyticalList'][2]['number'] = (int)$totalProcedures;
        $data['rightPane']['analyticalList'][2]['title'] = "Procedures";
        $data['rightPane']['analyticalList'][2]['value'] = $totalProcedureRevenue;

        // Pharmacy Revenue JSON
        $data['rightPane']['analyticalList'][3]['number'] = (int)$totalPrescriptions;
        $data['rightPane']['analyticalList'][3]['title'] = "Pharmacy";
        $data['rightPane']['analyticalList'][3]['value'] = $totalPharmacyRevenue;
    }

    $this->response(array('code' => '200', 'message' => 'success ', 'result' => $data, 'requestname' => $method));
}


public function eliminateComma($str){
        //get no. of possible comma
    $commas = explode(',', $str);
    $commaCount = count($commas);

    if($commaCount == 2){
        $str = str_replace(',,', '', $str);
    }else if($commaCount == 1){
        $str = str_replace(',', '', $str);
    }else{
        for($i=$commaCount; $i>0; $i--){
            if($i == 2){
                $str = str_replace(',,', ',', $str);
            }else if($i == 1){
                $str = str_replace(',', ', ', $str);
            }else{
                $commaStr = str_repeat(',',$i);
                $str = str_replace($commaStr,'',$str);
            }
        }    
    }

    return trim($str);
}

    // public function dashboard($parameters, $method, $user_id) {
    //     $clinic_id = $parameters['clinic_id'];
    //     $role_id = $parameters['role_id'];
    //     $profile_id = $parameters['profile_id'];
    //     $dashboard_type = $parameters['dashboard_type'];
    //     if ($role_id == 4) {
    //         $left_pane = $this->db->query("select Doc.doctor_id,Doc.first_name,Doc.last_name,Doc.color_code,Doc.qualification,Dep.department_id,Dep.department_name from clinic_doctor CDoc inner join doctors Doc on (CDoc.doctor_id=Doc.doctor_id) inner join department Dep on (Doc.department_id=Dep.department_id) where CDoc.clinic_id='" . $clinic_id . "' and Doc.doctor_id='".$user_id."' ")->result();

    //         $dashboard['leftPane']['header'] = "ALL DOCTORS (" . count($left_pane) . ")";
    //         // $dashboard['leftPane']['date']=date('Y-m-d');
    //         $l = 0;
    //         foreach ($left_pane as $left) {
    //             $appointments = $this->db->query("select count(*) as appointment_count from appointments where clinic_id='" . $clinic_id . "' and doctor_id='" . $left->doctor_id . "' and appointment_date='" . date('Y-m-d') . "'")->row();

    //             $dashboard['leftPane']['doctors'][$l]['doctor_id'] = $left->doctor_id;
    //             $dashboard['leftPane']['doctors'][$l]['doctor_name'] = $left->first_name . " " . $left->last_name;
    //             $dashboard['leftPane']['doctors'][$l]['color_code'] = $left->color_code;
    //             $dashboard['leftPane']['doctors'][$l]['designation'] = $left->qualification;
    //             $dashboard['leftPane']['doctors'][$l]['department'] = $left->department_name;
    //             $dashboard['leftPane']['doctors'][$l]['appointments'] = $appointments->appointment_count;

    //             $right_pane = $this->db->query("select a.appointment_id,a.appointment_type,a.appointment_time_slot,a.check_in_time,a.status as appointment_status,d.doctor_id,d.first_name as df_name,d.last_name as dl_name,d.color_code,p.*,p.patient_id,p.first_name as pf_name,p.middle_name as pm_name,p.last_name as pl_name,p.age,p.gender,p.title,p.qrcode,p.umr_no,p.photo,d.qualification,de.department_name from appointments a inner join doctors d on (a.doctor_id=d.doctor_id) inner join department de on (d.department_id=de.department_id) inner join patients p on (a.patient_id=p.patient_id) where a.clinic_id='" . $clinic_id . "' and a.doctor_id='".$left->doctor_id."' and a.appointment_date='" . date('Y-m-d') . "' and (a.status ='waiting' or a.status='in_consultation') ORDER BY a.check_in_time, FIELD(a.status,'waiting','in_consultation') ASC, FIELD(a.priority, 'pregnancy', 'elderly', 'children','Low')")->result();


    //         $r = 0;
    //         if(count($right_pane)<=0)
    //         {
    //             $dashboard['leftPane']['doctors'][$l]['patients'] = array();
    //         }else{
    //                 foreach ($right_pane as $right) {
    //             if ($right->photo != NULL) {
    //                 $path = base_url() . 'uploads/patients/' . $right->photo;
    //             } else {
    //                 $path = NULL;
    //             }

    //             if ($right->qrcode != NULL) {
    //                 $qrcode = base_url() . 'uploads/qrcodes/patients/' . $right->qrcode;
    //             } else {
    //                 $qrcode = NULL;
    //             }

    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['patient_id'] = $right->patient_id;
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['first_name'] = $right->pf_name;
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['middle_name'] = $right->pm_name;
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['last_name'] = $right->pl_name;
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['age_unit'] = $right->age . " " . ucfirst($right->age_unit);
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['title'] = ucwords($right->title);
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['umr_no'] = $right->umr_no;
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['referred_by'] = $right->referred_by;
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['patient_condition'] = $right->patient_condition;
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['condition_months'] = $right->condition_months;
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['occupation'] = $right->occupation;
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['date_of_birth'] = $right->date_of_birth;
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['email'] = $right->email_id;
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['country'] = $right->country;
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['address'] = $right->address_line . "," . $right->district_name . "," . $right->state_name . "," . $right->ppcode;

    //             // eliminate comma
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['address'] = $this->eliminateComma($dashboard['leftPane']['doctors'][$l]['patients'][$r]['address']);

    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['registartion_date'] = date("Y-m-d", strtotime($right->created_date_time));
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['age'] = $right->age;
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['gender'] = $right->gender;
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['appointment_time'] = $right->appointment_time_slot;
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['doctor_id'] = $right->doctor_id;
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['doctor'] = $right->df_name . " " . $right->last_name;
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['color_code'] = $right->color_code;
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['designation'] = $right->qualification;
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['department'] = $right->department_name;
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['photo'] = $path;
    //             $dashboard['leftPane']['doctors'][$l]['patients'][$r]['qrcode'] = $qrcode;
    //             $r++;
    //         }
    //         }

    //             $l++;
    //         }

    //     } else if ($role_id == 1) {
    //         $left_pane = $this->db->query("select d.doctor_id,d.first_name,d.last_name,d.color_code,d.qualification,de.department_name from clinic_doctor cd inner join doctors d on (cd.doctor_id=d.doctor_id) inner join department de on (d.department_id=de.department_id) where cd.clinic_id='" . $clinic_id . "' ")->result();
    //         $dashboard['leftPane']['header'] = "ALL DOCTORS (" . count($left_pane) . ")";
    //         $dashboard['leftPane']['date'] = date('Y-m-d');
    //         $l = 0;
    //         foreach ($left_pane as $left) {
    //             $appointments = $this->db->query("select count(*) as appointment_count from appointments where clinic_id='" . $clinic_id . "' and doctor_id='" . $left->doctor_id . "' and appointment_date='" . date('Y-m-d') . "'")->row();
    //             $consultation = $this->db->query("select count(*) as consultation_count from billing_line_items bl inner join billing b on(bl.billing_id = b.billing_id) inner join appointments a on(a.doctor_id = b.doctor_id) where b.clinic_id='" . $clinic_id . "' and b.doctor_id='" . $left->doctor_id . "' and a.appointment_date='" . date('Y-m-d') . "' and b.billing_type='Consultation'")->row();
    //             $pharmacy = $this->db->query("select count(*) as pharmacy_count from billing_line_items bl inner join billing b on(bl.billing_id = b.billing_id) inner join appointments a on(a.doctor_id = b.doctor_id) where b.clinic_id='" . $clinic_id . "' and b.doctor_id='" . $left->doctor_id . "' and a.appointment_date='" . date('Y-m-d') . "' and b.billing_type='Pharmacy'")->row();
    //             $investigations = $this->db->query("select count(*) as investigations_count from billing_line_items bl inner join billing b on(bl.billing_id = b.billing_id) inner join appointments a on(a.doctor_id = b.doctor_id) where b.clinic_id='" . $clinic_id . "' and b.doctor_id='" . $left->doctor_id . "' and a.appointment_date='" . date('Y-m-d') . "' and b.billing_type='Investigation'")->row();
    //             $procedures = $this->db->query("select count(*) as procedures_count from billing_line_items bl inner join billing b on(bl.billing_id = b.billing_id) inner join appointments a on(a.doctor_id = b.doctor_id) where b.clinic_id='" . $clinic_id . "' and b.doctor_id='" . $left->doctor_id . "' and a.appointment_date='" . date('Y-m-d') . "' and b.billing_type='Procedure'")->row();

    //             $dashboard['leftPane']['doctors'][$l]['doctor_id'] = $left->doctor_id;
    //             $dashboard['leftPane']['doctors'][$l]['doctor_name'] = $left->first_name . " " . $left->last_name;
    //             $dashboard['leftPane']['doctors'][$l]['color_code'] = $left->color_code;
    //             $dashboard['leftPane']['doctors'][$l]['designation'] = $left->qualification;
    //             $dashboard['leftPane']['doctors'][$l]['department'] = $left->department_name;
    //             $dashboard['leftPane']['doctors'][$l]['appointments'] = $consultation->consultation;
    //             $dashboard['leftPane']['doctors'][$l]['pharmacy'] = $pharmacy->pharmacy_count;
    //             $dashboard['leftPane']['doctors'][$l]['investigations'] = $investigations->investigations_count;
    //             $dashboard['leftPane']['doctors'][$l]['procedures'] = $procedures->procedures_count;

    //             $patients = $this->db->query("SELECT p.*,a.*,a.status as appointment_status,p.title,p.patient_id,p.umr_no,p.gender,a.check_in_time,p.age FROM `patients` p left JOIN appointments a on (p.patient_id=a.patient_id)  inner join doctors d on (a.doctor_id=d.doctor_id)  where p.clinic_id='" . $clinic_id . "' and d.doctor_id='" . $left->doctor_id . "' order by p.patient_id desc")->result();
    //             $r = 0;
    //             if (empty($patients)) {
    //                 $dashboard['leftPane']['doctors'][$l]['patients'] = [];
    //             } else {
    //                 foreach ($patients as $key => $plist) {
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['patient_id'] = $plist->patient_id;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['name'] = $plist->first_name . " " . $plist->last_name;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['gender'] = $plist->gender;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['age'] = $plist->age;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['UMRNo'] = $plist->umr_no;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['appointment_time'] = $plist->appointment_time_slot;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['check_in_time'] = $plist->check_in_time;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['appointment_type'] = $plist->appointment_type;
    //                     $r++;
    //                 }
    //             }

    //             $l++;
    //         }

    //         $consultation_count = $this->db->query("select * from billing b inner join billing_line_items bl on (b.billing_id = bl.billing_id) where b.clinic_id = '" . $clinic_id . "' and b.billing_type= 'Consultation'")->num_rows();
    //         $pharmacy_count = $this->db->query("select * from billing b inner join billing_line_items bl on (b.billing_id = bl.billing_id) where b.clinic_id = '" . $clinic_id . "' and billing_type= 'Pharmacy'")->num_rows();
    //         $investigations_count = $this->db->query("select * from billing b inner join billing_line_items bl on (b.billing_id = bl.billing_id) where b.clinic_id = '" . $clinic_id . "' and b.billing_type= 'Investigation'")->num_rows();
    //         $procedure_count = $this->db->query("select * from billing b inner join billing_line_items bl on (b.billing_id = bl.billing_id) where b.clinic_id = '" . $clinic_id . "' and b.billing_type= 'procedure'")->num_rows();
    //         $dashboard['rightPane']['header'] = "finances";
    //         $dashboard['rightPane']['Consultation'] = $consultation_count;
    //         $dashboard['rightPane']['Pharmacy'] = $pharmacy_count;
    //         $dashboard['rightPane']['Investigations'] = $investigations_count;
    //         $dashboard['rightPane']['procedures'] = $procedure_count;
    //     } else if ($role_id == 2) {

    //         $left_pane = $this->db->query("select d.doctor_id,d.first_name,d.last_name,d.color_code,d.qualification,de.department_name from clinic_doctor cd inner join doctors d on (cd.doctor_id=d.doctor_id) inner join department de on (d.department_id=de.department_id) where cd.clinic_id='" . $clinic_id . "' group by cd.doctor_id ")->result();
    //         $dashboard['leftPane']['header'] = "ALL DOCTORS (" . count($left_pane) . ")";
    //         $dashboard['leftPane']['date'] = date('Y-m-d');
    //         $l = 0;
    //         foreach ($left_pane as $left) {
    //             $consultation_count = $this->db->query("select *, sum(bl.amount) as ctotal from billing b inner join billing_line_items bl on (b.billing_id = bl.billing_id) where b.billing_date_time like '" . date('Y-m-d') . "%' and b.clinic_id = '" . $clinic_id . "' and b.doctor_id= '" . $left->doctor_id . "' and b.billing_type= 'Consultation'")->row();
    //             $pharmacy_count = $this->db->query("select *, sum(bl.amount) as ptotal from billing b inner join billing_line_items bl on (b.billing_id = bl.billing_id) where b.billing_date_time like '" . date('Y-m-d') . "%' and b.clinic_id = '" . $clinic_id . "' and b.doctor_id= '" . $left->doctor_id . "' and b.billing_type= 'Pharmacy'")->row();
    //             $investigations_count = $this->db->query("select *, sum(bl.amount) as invtotal from billing b inner join billing_line_items bl on (b.billing_id = bl.billing_id) where b.billing_date_time like '" . date('Y-m-d') . "%' and b.clinic_id = '" . $clinic_id . "' and b.doctor_id= '" . $left->doctor_id . "' and b.billing_type= 'Investigations'")->row();
    //             $procedure_count = $this->db->query("select *, sum(bl.amount) as prototal from billing b inner join billing_line_items bl on (b.billing_id = bl.billing_id) where b.billing_date_time like '" . date('Y-m-d') . "%' and b.clinic_id = '" . $clinic_id . "' and b.doctor_id= '" . $left->doctor_id . "' and b.billing_type = 'procedures'")->row();

    //             $dashboard['leftPane']['doctors'][$l]['doctor_id'] = $left->doctor_id;
    //             $dashboard['leftPane']['doctors'][$l]['doctor_name'] = $left->first_name . " " . $left->last_name;
    //             $dashboard['leftPane']['doctors'][$l]['color_code'] = $left->color_code;
    //             $dashboard['leftPane']['doctors'][$l]['designation'] = $left->qualification;
    //             $dashboard['leftPane']['doctors'][$l]['department'] = $left->department_name;
    //             $dashboard['leftPane']['doctors'][$l]['appointments'] = $consultation_count->ctotal;
    //             $dashboard['leftPane']['doctors'][$l]['pharmacy'] = $pharmacy_count->ptotal;
    //             $dashboard['leftPane']['doctors'][$l]['investigations'] = $investigations_count->invtotal;
    //             $dashboard['leftPane']['doctors'][$l]['procedures'] = $procedure_count->prototal;

    //             $patients = $this->db->query("SELECT p.*,a.*,a.status as appointment_status,p.title,p.patient_id,p.umr_no,p.gender,a.check_in_time,p.age FROM `patients` p left JOIN appointments a on (p.patient_id=a.patient_id)  inner join doctors d on (a.doctor_id=d.doctor_id)  where  a.appointment_date = '" . date('Y-m-d') . "' and d.doctor_id='" . $left->doctor_id . "' ORDER BY a.check_in_time, FIELD(a.status,'checked_in') ASC, FIELD(a.priority, 'pregnancy', 'elderly', 'children','Low')")->result();
    //             $r = 0;
    //             if (empty($patients)) {
    //                 $dashboard['leftPane']['doctors'][$l]['patients'] = [];
    //             } else {
    //                 foreach ($patients as $key => $plist) {

    //                     if ($plist->photo != NULL) {
    //                         $path = base_url() . 'uploads/patients/' . $plist->photo;
    //                     } else {
    //                         $path = NULL;
    //                     }

    //                     if ($plist->qrcode != NULL) {
    //                         $qrcode = base_url() . 'uploads/qrcodes/patients/' . $plist->qrcode;
    //                     } else {
    //                         $qrcode = NULL;
    //                     }

    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['patient_id'] = $plist->patient_id;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['first_name'] = $plist->first_name;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['middle_name'] = $plist->middle_name;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['last_name'] = $plist->last_name;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['title'] = ucwords($plist->title);
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['contact'] = $plist->mobile;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['age'] = $plist->age . " " . ucfirst($plist->age_unit);
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['referred_by'] = $plist->referred_by;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['patient_condition'] = $plist->patient_condition;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['condition_months'] = $plist->condition_months;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['occupation'] = $plist->occupation;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['country'] = $plist->country;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['date_of_birth'] = $plist->date_of_birth;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['email'] = $plist->email_id;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['registartion_date'] = date("Y-m-d", strtotime($plist->created_date_time));
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['photo'] = $path;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['qrcode'] = $qrcode;

    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['address'] = $plist->address_line . "," . $plist->district_name . "," . $plist->state_name . "," . $plist->ppcode;

    //                     //eliminate comma
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['address'] = $this->eliminateComma($dashboard['leftPane']['doctors'][$l]['patients'][$r]['address']);

    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['doctor_id'] = $left->doctor_id;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['doctor_name'] = $left->first_name . " " . $left->last_name;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['color_code'] = $left->color_code;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['designation'] = $left->qualification;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['department'] = $left->department_name;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['department_id'] = $plist->department_id;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['gender'] = $plist->gender;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['age'] = $plist->age;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['umr_no'] = $plist->umr_no;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['appointment_time'] = $plist->appointment_time_slot;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['appointment_status'] = $plist->appointment_status;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['check_in_time'] = $plist->check_in_time;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['appointment_type'] = $plist->appointment_type;
    //                     $r++;
    //                 }
    //             }

    //             $l++;
    //         }

    //         $consultation_total = $this->db->query("select *, sum(bl.amount) as ctotal from billing b inner join billing_line_items bl on (b.billing_id = bl.billing_id) where b.billing_date_time like '" . date('Y-m-d') . "%' and b.clinic_id = '" . $clinic_id . "' and b.billing_type= 'Consultation'")->row();
    //         $pharmacy_total = $this->db->query("select *, sum(bl.amount) as ptotal from billing b inner join billing_line_items bl on (b.billing_id = bl.billing_id) where b.billing_date_time like '" . date('Y-m-d') . "%' and b.clinic_id = '" . $clinic_id . "' and b.billing_type= 'Pharmacy'")->row();
    //         $investigations_total = $this->db->query("select *, sum(bl.amount) as invtotal from billing b inner join billing_line_items bl on (b.billing_id = bl.billing_id) where b.billing_date_time like '" . date('Y-m-d') . "%' and b.clinic_id = '" . $clinic_id . "' and b.billing_type= 'Investigations'")->row();
    //         $procedure_total = $this->db->query("select *, sum(bl.amount) as prototal from billing b inner join billing_line_items bl on (b.billing_id = bl.billing_id) where b.billing_date_time like '" . date('Y-m-d') . "%' and b.clinic_id = '" . $clinic_id . "' and b.billing_type= 'procedures'")->row();
    //         $dashboard['rightPane']['header'] = "finances";
    //         $dashboard['rightPane']['Consultation'] = $consultation_total->ctotal;
    //         $dashboard['rightPane']['Pharmacy'] = $pharmacy_total->ptotal;
    //         $dashboard['rightPane']['Investigations'] = $investigations_total->invtotal;
    //         $dashboard['rightPane']['procedures'] = $procedure_total->prototal;
    //     } else if ($role_id == 3) {

    //         $left_pane = $this->db->query("select d.doctor_id,d.first_name,d.last_name,d.color_code,d.qualification,de.department_name from clinic_doctor cd inner join doctors d on (cd.doctor_id=d.doctor_id) inner join department de on (d.department_id=de.department_id) where cd.clinic_id='" . $clinic_id . "' group by cd.doctor_id ")->result();

    //         $dashboard['leftPane']['header'] = "ALL DOCTORS (" . count($left_pane) . ")";
    //         $dashboard['leftPane']['date'] = date('Y-m-d');
    //         $l = 0;
    //         foreach ($left_pane as $left) {
    //             $consultation_count = $this->db->query("select *, sum(bl.amount) as ctotal from billing b inner join billing_line_items bl on (b.billing_id = bl.billing_id) where b.billing_date_time like '" . date('Y-m-d') . "%' and b.clinic_id = '" . $clinic_id . "' and b.doctor_id= '" . $left->doctor_id . "' and b.billing_type= 'Consultation'")->row();
    //             $pharmacy_count = $this->db->query("select *, sum(bl.amount) as ptotal from billing b inner join billing_line_items bl on (b.billing_id = bl.billing_id) where b.billing_date_time like '" . date('Y-m-d') . "%' and b.clinic_id = '" . $clinic_id . "' and b.doctor_id= '" . $left->doctor_id . "' and b.billing_type= 'Pharmacy'")->row();
    //             $investigations_count = $this->db->query("select *, sum(bl.amount) as invtotal from billing b inner join billing_line_items bl on (b.billing_id = bl.billing_id) where b.billing_date_time like '" . date('Y-m-d') . "%' and b.clinic_id = '" . $clinic_id . "' and b.doctor_id= '" . $left->doctor_id . "' and b.billing_type= 'Investigations'")->row();
    //             $procedure_count = $this->db->query("select *, sum(bl.amount) as prototal from billing b inner join billing_line_items bl on (b.billing_id = bl.billing_id) where b.billing_date_time like '" . date('Y-m-d') . "%' and b.clinic_id = '" . $clinic_id . "' and b.doctor_id= '" . $left->doctor_id . "' and b.billing_type= 'procedures'")->row();



    //             $dashboard['leftPane']['doctors'][$l]['doctor_id'] = $left->doctor_id;
    //             $dashboard['leftPane']['doctors'][$l]['doctor_name'] = $left->first_name . " " . $left->last_name;
    //             $dashboard['leftPane']['doctors'][$l]['color_code'] = $left->color_code;
    //             $dashboard['leftPane']['doctors'][$l]['designation'] = $left->qualification;
    //             $dashboard['leftPane']['doctors'][$l]['department'] = $left->department_name;
    //             $dashboard['leftPane']['doctors'][$l]['appointments'] = $consultation_count->ctotal;
    //             $dashboard['leftPane']['doctors'][$l]['pharmacy'] = $pharmacy_count->ptotal;
    //             $dashboard['leftPane']['doctors'][$l]['investigations'] = $investigations_count->invtotal;
    //             $dashboard['leftPane']['doctors'][$l]['procedures'] = $procedure_count->prototal;

    //             $patients = $this->db->query("SELECT p.*,a.*,a.status as appointment_status,p.title,p.patient_id,p.umr_no,p.gender,a.check_in_time,p.age FROM `patients` p left JOIN appointments a on (p.patient_id=a.patient_id)  inner join doctors d on (a.doctor_id=d.doctor_id)  where  a.appointment_date = '" . date('Y-m-d') . "' and d.doctor_id='" . $left->doctor_id . "' ORDER BY a.check_in_time, FIELD(a.status,'checked_in') ASC, FIELD(a.priority, 'pregnancy', 'elderly', 'children','Low')")->result();
    //             $r = 0;
    //             if (empty($patients)) {
    //                 $dashboard['leftPane']['doctors'][$l]['patients'] = [];
    //             } else {
    //                 foreach ($patients as $key => $plist) {


    //                     if ($plist->photo != NULL) {
    //                         $path = base_url() . 'uploads/patients/' . $plist->photo;
    //                     } else {
    //                         $path = NULL;
    //                     }

    //                     if ($plist->qrcode != NULL) {
    //                         $qrcode = base_url() . 'uploads/qrcodes/patients/' . $plist->qrcode;
    //                     } else {
    //                         $qrcode = NULL;
    //                     }

    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['patient_id'] = $plist->patient_id;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['first_name'] = $plist->first_name;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['middle_name'] = $plist->middle_name;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['last_name'] = $plist->last_name;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['title'] = ucwords($plist->title);
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['contact'] = $plist->mobile;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['age'] = $plist->age . " " . ucfirst($plist->age_unit);
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['referred_by'] = $plist->referred_by;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['patient_condition'] = $plist->patient_condition;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['condition_months'] = $plist->condition_months;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['occupation'] = $plist->occupation;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['country'] = $plist->country;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['date_of_birth'] = $plist->date_of_birth;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['email'] = $plist->email_id;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['registartion_date'] = date("Y-m-d", strtotime($plist->created_date_time));
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['address'] = $plist->address_line . "," . $plist->district_name . "," . $plist->state_name . "," . $plist->ppcode;

    //                     // eliminate comma
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['address'] = $this->eliminateComma($dashboard['leftPane']['doctors'][$l]['patients'][$r]['address']);

    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['photo'] = $path;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['qrcode'] = $qrcode;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['doctor_id'] = $left->doctor_id;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['doctor_name'] = $left->first_name . " " . $left->last_name;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['color_code'] = $left->color_code;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['designation'] = $left->qualification;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['department'] = $left->department_name;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['department_id'] = $plist->department_id;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['gender'] = $plist->gender;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['age'] = $plist->age;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['umr_no'] = $plist->umr_no;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['appointment_time'] = $plist->appointment_time_slot;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['appointment_status'] = $plist->appointment_status;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['check_in_time'] = $plist->check_in_time;
    //                     $dashboard['leftPane']['doctors'][$l]['patients'][$r]['appointment_type'] = $plist->appointment_type;
    //                     $r++;
    //                 }
    //             }

    //             $l++;
    //         }

    //         $consultation_total = $this->db->query("select *, sum(bl.amount) as ctotal from billing b inner join billing_line_items bl on (b.billing_id = bl.billing_id) where b.billing_date_time like '" . date('Y-m-d') . "%' and b.clinic_id = '" . $clinic_id . "' and b.doctor_id='" . $user_id . "' and b.billing_type= 'Consultation'")->row();
    //         $pharmacy_total = $this->db->query("select *, sum(bl.amount) as ptotal from billing b inner join billing_line_items bl on (b.billing_id = bl.billing_id) where b.billing_date_time like '" . date('Y-m-d') . "%' and b.clinic_id = '" . $clinic_id . "' and b.doctor_id='" . $user_id . "' and b.billing_type= 'Pharmacy'")->row();
    //         $investigations_total = $this->db->query("select *, sum(bl.amount) as invtotal from billing b inner join billing_line_items bl on (b.billing_id = bl.billing_id) where b.billing_date_time like '" . date('Y-m-d') . "%' and b.clinic_id = '" . $clinic_id . "' and b.doctor_id='" . $user_id . "' and b.billing_type= 'Investigations'")->num_rows();
    //         $procedure_total = $this->db->query("select *, sum(bl.amount) as prototal from billing b inner join billing_line_items bl on (b.billing_id = bl.billing_id) where b.billing_date_time like '" . date('Y-m-d') . "%' and b.clinic_id = '" . $clinic_id . "' and b.doctor_id='" . $user_id . "' and b.billing_type= 'procedures'")->num_rows();
    //         $dashboard['rightPane']['header'] = "finances";
    //         $dashboard['rightPane']['Consultation'] = $consultation_total->ctotal;
    //         $dashboard['rightPane']['Pharmacy'] = $pharmacy_total->ptotal;
    //         $dashboard['rightPane']['Investigations'] = $investigations_total->invtotal;
    //         $dashboard['rightPane']['procedures'] = $procedure_total->prototal;
    //     } else if ($role_id == 7) {
    //         $left_pane = $this->db->query("select *,p.status as prescription_status from patient_prescription p inner join patient_prescription_drug pd on(p.patient_prescription_id = pd.patient_prescription_id) where p.clinic_id= '" . $parameters['clinic_id'] . "' group by p.patient_prescription_id order by p.patient_prescription_id desc")->result();

    //         $pv = 0;
    //         foreach ($left_pane as $pav) {
    //             $quantity_data = $this->db->query("select * from  patient_prescription_drug pd inner join drug d on(pd.drug_id = d.drug_id) where patient_prescription_id = '" . $pav->patient_prescription_id . "'")->result();
    //             $total = 0;
    //             foreach ($quantity_data as $key => $qty) {
    //                 $total += $qty->quantity * $qty->mrp;
    //             }
    //             $dashboard['leftPane']['prescriptions'][$pv]['patient_prescription_id'] = $pav->patient_prescription_id;
    //             $dashboard['leftPane']['prescriptions'][$pv]['appointment_id'] = $pav->appointment_id;
    //             $dashboard['leftPane']['prescriptions'][$pv]['patient_id'] = $pav->patient_id;
    //             $patient_deatails = $this->Generic_model->getSingleRecord('patients', array('patient_id' => $pav->patient_id), $order = '');
    //             $dashboard['leftPane']['prescriptions'][$pv]['patient_name'] = $patient_deatails->first_name . " " . $patient_deatails->last_name;
    //             $dashboard['leftPane']['prescriptions'][$pv]['umr_no'] = $patient_deatails->umr_no;
    //             $app_deatails = $this->Generic_model->getSingleRecord('appointments', array('appointment_id' => $pav->appointment_id), $order = '');
    //             $doctor_deatails = $this->Generic_model->getSingleRecord('doctors', array('doctor_id' => $app_deatails->doctor_id), $order = '');
    //             $dashboard['leftPane']['prescriptions'][$pv]['doctor_name'] = $doctor_deatails->first_name . " " . $doctor_deatails->last_name;
    //             $dashboard['leftPane']['prescriptions'][$pv]['doctor_id'] = $app_deatails->doctor_id;
    //             $dashboard['leftPane']['prescriptions'][$pv]['amount'] = $total;
    //             $dashboard['leftPane']['prescriptions'][$pv]['status'] = $pav->prescription_status;


    //             $pv++;
    //         }

    //         $p_data = $this->db->query("select * from  patient_prescription_drug pd inner join drug d on(pd.drug_id = d.drug_id)")->result();
    //         $closed_data = $this->db->query("select * from  patient_prescription_drug pd inner join drug d on(pd.drug_id = d.drug_id) inner join  patient_prescription p on(p.patient_prescription_id = pd.patient_prescription_id) where p.status = 2")->result();
    //         $date = date('Y-m-d');
    //         $exp_data = $this->db->query("select * from  pharmacy_inventory  where expiry_date <='" . $date . "' ")->result();
    //         $exp_soon_data = $this->db->query("select * from  pharmacy_inventory  where expiry_date <='" . checkexpiry() . "' ")->result();
    //         $c_count = count($closed_data);
    //         $p_count = count($p_data);
    //         $exp_count = count($exp_data);
    //         $exp_soon_count = count($exp_soon_data);
    //         $total_amount = 0;
    //         $c_total = 0;
    //         foreach ($p_data as $key => $val1) {
    //             $total_amount += $val1->quantity * $val1->mrp;
    //         }
    //         foreach ($closed_data as $key => $val) {
    //             $c_total += $val2->quantity * $val2->mrp;
    //         }

    //         $dashboard['rightPane']['date'] = date("Y-m-d");
    //         $dashboard['rightPane']['header'] = "finances";
    //         $dashboard['rightPane']['Prescription_count'] = $p_count;
    //         $dashboard['rightPane']['Prescription_amount'] = $total_amount;
    //         $dashboard['rightPane']['closed_count'] = $c_count;
    //         $dashboard['rightPane']['closed_amount'] = $c_total;
    //         $dashboard['rightPane']['expiring_soon_count'] = $exp_soon_count;
    //         $dashboard['rightPane']['shortage_count'] = 0;
    //         $dashboard['rightPane']['expired_count'] = $exp_count;
    //     } else if ($role_id == 6) {
    //         $investigations = $this->db->query("select * from patient_investigation pi inner join patient_investigation_line_items pil on (pi.patient_investigation_id = pil.patient_investigation_id) where pi.clinic_id= '" . $parameters['clinic_id'] . "' group by pi.patient_investigation_id order by pi.patient_investigation_id desc ")->result();

    //         $pv = 0;
    //         if (count($investigations) > 0) {

    //             foreach ($investigations as $pav) {
    //                 $dashboard['leftPane']['investigations'][$pv]['patient_investigation_id'] = $pav->patient_investigation_id;
    //                 $dashboard['leftPane']['investigations'][$pv]['appointment_id'] = $pav->appointment_id;
    //                 $dashboard['leftPane']['investigations'][$pv]['patient_id'] = $pav->patient_id;
    //                 $patient_deatails = $this->Generic_model->getSingleRecord('patients', array('patient_id' => $pav->patient_id), $order = '');
    //                 $dashboard['leftPane']['investigations'][$pv]['patient_name'] = $patient_deatails->first_name . " " . $patient_deatails->last_name;
    //                 $dashboard['leftPane']['investigations'][$pv]['umr_no'] = $pav->umr_no;

    //                 $doctor_deatails = $this->Generic_model->getSingleRecord('doctors', array('doctor_id' => $pav->doctor_id), $order = '');
    //                 $dashboard['leftPane']['investigations'][$pv]['doctor_name'] = $doctor_deatails->first_name . " " . $doctor_deatails->last_name;
    //                 $dashboard['leftPane']['investigations'][$pv]['doctor_id'] = $pav->doctor_id;
    //                 $dashboard['leftPane']['investigations'][$pv]['clinic_id'] = $pav->clinic_id;
    //                 $dashboard['leftPane']['investigations'][$pv]['status'] = $pav->status;
    //                 $investigations_list = $this->db->query("select * from patient_investigation_line_items  where patient_investigation_id = '" . $pav->patient_investigation_id . "' order by patient_investigation_id desc")->result();
    //                 $pv1 = 0;
    //                 foreach ($investigations_list as $key => $value) {
    //                     $inv_deatails = $this->Generic_model->getSingleRecord('investigations', array('investigation_id' => $value->investigation_id), $order = '');
    //                     $dashboard['leftPane']['investigations'][$pv]['investigation_id'][$pv1]['investigation_code'] = $inv_deatails->investigation_id;
    //                     $dashboard['leftPane']['investigations'][$pv]['patient_investigation_line_item_id'][$pv1]['investigation_code'] = $inv_deatails->patient_investigation_line_item_id;
    //                     $dashboard['leftPane']['investigations'][$pv]['investigations_list'][$pv1]['investigation_code'] = $inv_deatails->investigation_code;
    //                     $dashboard['leftPane']['investigations'][$pv]['investigations_list'][$pv1]['investigation_name'] = $inv_deatails->investigation;
    //                     $dashboard['leftPane']['investigations'][$pv]['investigations_list'][$pv1]['category'] = $inv_deatails->category;
    //                     $dashboard['leftPane']['investigations'][$pv]['investigations_list'][$pv1]['mrp'] = $inv_deatails->mrp;
    //                     $pv1++;
    //                 }


    //                 $pv++;
    //             }

    //             $dashboard['rightPane']['date'] = date("Y-m-d");
    //             $dashboard['rightPane']['investigation_count'] = 0;
    //             $dashboard['rightPane']['investigation_amount'] = 0;
    //             $dashboard['rightPane']['closed_count'] = 0;
    //             $dashboard['rightPane']['closed_amount'] = $c_total;
    //             $dashboard['rightPane']['expiring_soon_count'] = 0;
    //             $dashboard['rightPane']['shortage_count'] = 0;
    //             $dashboard['rightPane']['expired_count'] = 0;
    //         }
    //     }
    //     $this->response(array('code' => '200', 'message' => 'success ', 'result' => $dashboard, 'requestname' => $method));
    // }
    //generating invoice for investigation
public function investigation_invoice($parameters, $method, $user_id) {
    $data['patient_investigation_id'] = $parameters['patient_investigation_id'];
    $data2['patient_id'] = $parameters['patient_id'];
    $patient_info = $this->Generic_model->getSingleRecord('patients',array('patient_id'=>$parameters['patient_id']),'');


    $data2['patient_name'] = strtoupper($patient_info->first_name . ' ' . $patient_info->last_name);
    $data2['umr_no'] = $patient_info->umr_no;
    $appointment_info = $this->Generic_model->getSingleRecord('appointments',array('appointment_id'=>$parameters['appointment_id']),'');

    $doctors_info = $this->Generic_model->getSingleRecord('doctors',array('doctor_id'=>$parameters['doctor_id']),'');
    $data['appointment_id'] = $appointment_info->appointment_id;
    $data['appointment_date'] = $appointment_info->appointment_date;
    $data['doctor_id'] = $doctors_info->doctor_id;
    $data2['doctor_name'] = "Dr. " . strtoupper($doctors_info->first_name . ' ' . $doctors_info->last_name);


    // $inr = $this->db->select("count(*) as invoiceno")->from("billing")->where("clinic_id='" . $parameters['clinic_id'] . "'")->get()->row();
    // $inv_gen = ($inr->invoiceno) + 1;
    // $receipt_no = 'RCT-' . $parameters['clinic_id'] . '-' . $inv_gen;
    // $invoice_no = 'INV-' . $parameters['clinic_id'] . '-' . $inv_gen;

    // Generate Invoice and Receipt no
    $invoice_no_alias = generate_invoice_no($parameters['clinic_id']);
    $invoice_no = $parameters['clinic_id'].$invoice_no_alias;     

    $billing_p['invoice_no'] = $invoice_no;
    $billing_p['invoice_no_alias'] = $invoice_no_alias;
    $billing_p['patient_id'] = $parameters['patient_id'];
    $billing_p['clinic_id'] = $parameters['clinic_id'];
    $billing_p['umr_no'] = $parameters['umr_no'];
    $billing_p['invoice_pdf'] = "INV_" . $parameters['clinic_id'] . "_" . date('dhi') . ".pdf";
    $billing_p['created_by'] = $user_id;
    $billing_p['created_date_time'] = date('Y-m-d H:i:s');
    $billing_p['modified_by'] = $user_id;
    $billing_p['modified_date_time'] = date('Y-m-d H:i:s');

    $billing_id = $this->Generic_model->insertDataReturnId('billing', $billing_p);

    for ($i = 0; $i < count($parameters['investigations_list']); $i++) {
        $data['investigation_id'] = $parameters['investigations_list'][$i]['investigation_id'];
        $update['checked'] = 1;
        $this->Generic_model->updateData("patient_investigation_line_items", $update, array('patient_investigation_line_item_id' => $parameters['investigations_list'][$i]['patient_investigation_line_item_id']));

        $patient_bank['billing_id'] = $billing_id;
        $patient_bank['doctor_id'] = $doctors_info->doctor_id;
        $patient_bank['billing_type'] = 'Investigations';
        $patient_bank['quantity'] = 1;
        $patient_bank['mode_of_payment'] = 'Cash';
        $patient_bank['billing_date_time'] = date('Y-m-d H:i:s');


        $patient_bank['amount'] = round($parameters['investigations_list'][$i]['mrp'], 2);
        $patient_bank['item_information'] = $parameters['investigations_list'][$i]['investigation_name'];

        $patient_bank['created_date_time'] = date('Y-m-d H:i:s');

        $patient_bank['modified_date_time'] = date('Y-m-d H:i:s');
        $this->Generic_model->insertData('billing_line_items', $patient_bank);
    }

    $data2['doctor_name'] = $doctors_info->first_name . " " . $doctors_info->last_name;
    $data2['qualification'] = $doctors_info->qualification;
    $address = $this->db->query('select * from clinics where clinic_id = "' . $parameters['clinic_id'] . '"')->row();
    $data2['clinic_address'] = $address->address;
    $data2['clinic_name'] = $address->clinic_name;
    $data2['clinic_logo'] = $address->clinic_logo;
    $data2['clinic_phone'] = $address->clinic_phone;
    $billing_info = $this->db->query('select * from billing where billing_id = ' . $billing_id)->row();
    $data2['invoice_number'] = $billing_info->invoice_no;

    $billing_status['status'] = 2;
    $billing_status['modified_by'] = $user_id;
    $billing_status['modified_date_time'] = date('Y-m-d H:i:s');
    $condition['patient_investigation_id'] = $parameters['patient_investigation_id'];
    $this->Generic_model->updateData("patient_investigation", $billing_status, $condition);

    $data2['patient_address'] = $patient_info->address_line;
    $data2['mode_of_payment'] = 'Cash';
    $data2['updated_info'] = $parameters['investigations_list'];

    $html = $this->load->view('investigation/investigation_invoice', $data2, true);

    $pdfFilePath = "INV_" . $parameters['clinic_id'] . "_" . date('dhi') . ".pdf";
    $data3['pdf_name'] = base_url() . 'uploads/investigation_invoice/' . $pdfFilePath;

    $this->load->library('M_pdf');
    $this->m_pdf->pdf->WriteHTML($html);
    $this->m_pdf->pdf->Output("./uploads/investigation_invoice/" . $pdfFilePath, "F");

    $this->response(array('code' => '200', 'message' => 'Invoice Generated Successfully', 'result' => $data3, 'requestname' => $method));
}


public function prescription_view($parameters, $method, $user_id) {

    $prescriptions = $this->db->select("*,p.status as prescription_status")->from("patient_prescription p")->join("patient_prescription_drug pd","p.patient_prescription_id = pd.patient_prescription_id")->where("p.patient_prescription_id= '" . $parameters['patient_prescription_id'] . "'")->get()->row();
    $param['prescriptions']['patient_prescription_id'] = $prescriptions->patient_prescription_id;
    $param['prescriptions']['appointment_id'] = $prescriptions->appointment_id;
    $param['prescriptions']['patient_id'] = $prescriptions->patient_id;
    $patient_deatails = $this->Generic_model->getSingleRecord('patients', array('patient_id' => $prescriptions->patient_id), $order = '');
    $param['prescriptions']['patient_name'] = $patient_deatails->first_name . " " . $patient_deatails->last_name;
    $param['prescriptions']['umr_no'] = $patient_deatails->umr_no;
    $app_deatails = $this->Generic_model->getSingleRecord('appointments', array('appointment_id' => $prescriptions->appointment_id), $order = '');
    $doctor_deatails = $this->Generic_model->getSingleRecord('doctors', array('doctor_id' => $app_deatails->doctor_id), $order = '');
    $param['prescriptions']['doctor_name'] = $doctor_deatails->first_name . " " . $doctor_deatails->last_name;
    $param['prescriptions']['doctor_id'] = $app_deatails->doctor_id;
    $param['prescriptions']['appointment_date'] = $app_deatails->appointment_date;
    $param['prescriptions']['clinic_id'] = $prescriptions->clinic_id;
    $param['prescriptions']['status'] = $prescriptions->prescription_status;

    $drug_list = $this->db->select("*")->from("patient_prescription_drug")->where(" patient_prescription_id= '" . $parameters['patient_prescription_id'] . "'")->get()->result();
    $pv1 = 0;
    if (count($drug_list) > 0) {

        foreach ($drug_list as $pav) {

            $drug_deatails = $this->Generic_model->getSingleRecord('drug', array('drug_id' => $pav->drug_id), $order = '');

            $param['prescriptions']['prescription_detail'][$pv1]['drug_id'] = $pav->drug_id;
            $param['prescriptions']['prescription_detail'][$pv1]['trade_name'] = $drug_deatails->trade_name;
            $param['prescriptions']['prescription_detail'][$pv1]['composition'] = $drug_deatails->composition;
            $param['prescriptions']['prescription_detail'][$pv1]['formulation'] = $drug_deatails->formulation;
            $param['prescriptions']['prescription_detail'][$pv1]['checked'] = $pav->checked;
            $param['prescriptions']['prescription_detail'][$pv1]['quantity'] = $pav->quantity;
            $param['prescriptions']['prescription_detail'][$pv1]['amount'] = $pav->quantity * round($drug_deatails->mrp, 2);
            $param['prescriptions']['prescription_detail'][$pv1]['mrp'] = $drug_deatails->mrp;

            $pv1++;
        }
    } else {
        $param['prescriptions'] = NULL;
    }
    $this->response(array('code' => '200', 'message' => 'Prescription View', 'result' => $param, 'requestname' => $method));
}


    //new patient registration
public function patient_registrations($parameters, $method, $user_id) {

    $pwd = $this->generateRandomString($length = 8);

    $ids = $this->db->select("p.profile_name,p.profile_id,r.role_id,r.role_name")->from("profiles p")->join("roles r","r.role_name = p.profile_name","left")->where("p.profile_name='Patient'")->get()->row();

        // USER REGISTRATION
    $user_reg['password'] = md5($pwd);
    $user_reg['email_id'] = $parameters['email_id'];
    $user_reg['mobile'] = $parameters['mobile'];
    $user_reg['user_type'] = 'patients';
    $user_reg['role_id'] = $ids->role_id;
    $user_reg['profile_id'] = $ids->profile_id;
    $user_reg['status'] = 1;
    $user_reg['created_by'] = $user_id;
    $user_reg['modified_by'] = $user_id;
    $user_reg['created_date_time'] = date('Y-m-d H:i:s');
    $user_reg['modified_date_time'] = date('Y-m-d H:i:s');

    $patient_id = $this->Generic_model->insertDataReturnId("users", $user_reg);
    $patient_details = $this->db->query("select * from patients where patient_id='" . $patient_id . "' ")->row();
    $month = date('m');
    $year = date('y');
    
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
    $from = 'UMDAA';
    $to = $user_reg['email_id'];
    $subject = "New Patient Password";
    $header = '<html>
    <body>
    <h3>Dear ' . $patient_details->title . ' ' . $patient_details->first_name . ' ' . $patient_details->last_name . ',</h3> 
    <table cellspacing="0" style="border: 2px dashed #FB4314; width: 100%; height: auto;padding:20px">
    <tr>
    <td>Your registration with UMDAA Clinics is successfull. Please find below for your registration & doctor appointment booking details</td>
    </tr>
    <tr style="background-color: #e0e0e0;">
    <th>Credentials:</th><td></td>
    </tr>
    <tr>
    <th>Username: </th><td>' . $patient_details->email_id . '</td>
    </tr>
    <tr>
    <th>Password: </th><td>' . $pwd . ' (You can change your password once log in to the application)</td>
    </tr>
    <tr>
    <th>Clinic Address:  </th><td>' . $patient_details->address . '</td>
    </tr>
    <tr>
    <th>Thanks,  </th>
    </tr>
    <tr>
    <td>UMDAA Clinics</td>
    </tr>
    </table>
    </body>
    </html>';
        //$message = $message;
    $this->mail_send->content_mail_ncl_all($from, $to, $subject, '', '', $header);
        //$patient_id=1;OPD121800001

    $patient_username['username'] = $umr_no;
    $this->Generic_model->updateData("users", $patient_username, array('user_id' => $patient_id));


    $tempDir = './uploads/qrcodes/patients';
    $codeContents = $patient_id;
    $qrname = $patient_id . md5($codeContents) . '.png';
    $pngAbsoluteFilePath = $tempDir . $qrname;
    $urlRelativeFilePath = base_url() . 'uploads/qrcodes/patients/' . $qrname;

    if (!file_exists($pngAbsoluteFilePath)) {
        QRcode::png($codeContents, $pngAbsoluteFilePath);
    }


        // Patient registration
    $patient_reg['patient_id'] = $patient_id;
    $patient_reg['qrcode'] = $qrname;
    $patient_reg['umr_no'] = $umr_no;
    $patient_reg['clinic_id'] = $parameters['clinic_id'];
    $patient_reg['title'] = $parameters['title'];
    $patient_reg['first_name'] = $parameters['first_name'];
    $patient_reg['last_name'] = $parameters['last_name'];
    $patient_reg['alias_name'] = $parameters['alias_name'];
    $patient_reg['gender'] = $parameters['gender'];
    if(empty($fdata->date_of_birth)) { 
        $patient_reg['date_of_birth'] = "";
    } else 
    {  
        $patient_reg['date_of_birth'] = date('Y-m-d', strtotime($fdata->date_of_birth)); 
    }
    $patient_reg['age'] = $parameters['age'];
    $patient_reg['mobile'] = DataCrypt($parameters['mobile'],'encrypt');
    $patient_reg['phone'] = DataCrypt($parameters['phone'],'encrypt');
    $patient_reg['email_id'] = DataCrypt($parameters['email_id'],'encrypt');
    $patient_reg['organization_id'] = $parameters['organization_id'];
    $patient_reg['address'] = $parameters['address'];
    $patient_reg['district_id'] = $parameters['district_id'];
    $patient_reg['state_id'] = $parameters['state_id'];
    $patient_reg['pincode'] = $parameters['pincode'];
    $patient_reg['referred_by'] = $parameters['referred_by'];
    $patient_reg['payment_status'] = $parameters['reg_payment_status'];
    $patient_reg['status'] = $parameters['status'];
    $patient_reg['occupation'] = $parameters['occupation'];
    $patient_reg['country'] = $parameters['country'];
        //$patient_reg['photo']=$string_version;
    $patient_reg['created_by'] = $user_id;
    $patient_reg['created_date_time'] = date('Y-m-d H:i:s');
    $patient_reg['modified_by'] = $user_id;
    $patient_reg['modified_date_time'] = date('Y-m-d H:i:s');
    $this->Generic_model->insertData('patients', $patient_reg);

    $patient_con['patient_id'] = $patient_id;
    $patient_con['patient_condition'] = $parameters['condition_type'];
    $patient_con['condition_months'] = $parameters['duration'];
    $patient_con['created_by'] = $user_id;
    $patient_con['created_date_time'] = date('Y-m-d H:i:s');
    $patient_con['modified_by'] = $user_id;
    $patient_con['modified_date_time'] = date('Y-m-d H:i:s');
        //$this->Generic_model->insertData('patient_condition_type',$patient_con);


    $appointment_date = date('Y-m-d', strtotime($parameters['appointment_date']));
    $appointment_time_slot = date('H:i', strtotime($parameters['appointment_time_slot']));

    $appointment['clinic_id'] = $parameters['clinic_id'];
    $appointment['patient_id'] = $patient_id;
    $appointment['umr_no'] = $umr_no;
    $appointment['doctor_id'] = $parameters['doctor_id'];
    $appointment['appointment_type'] = "New";
    $appointment['appointment_date'] = $appointment_date;
    $appointment['appointment_time_slot'] = $appointment_time_slot;
    $appointment['priority'] = "none";
    $appointment['payment_status'] = $parameters['con_payment_status'];
    $appointment['status'] = "booked";
    $appointment['created_by'] = $user_id;
    $appointment['modified_by'] = $user_id;
    $appointment['created_date_time'] = date('Y-m-d H:i:s');
    $appointment['modified_date_time'] = date('Y-m-d H:i:s');
    $appointment_id = $this->Generic_model->insertDataReturnId("appointments", $appointment);

    if ($parameters['payment_type'] == 'payment') {
        
        // Generate Invoice and Receipt no
        $invoice_no_alias = generate_invoice_no($parameters['clinic_id']);
        $invoice_no = $parameters['clinic_id'].$invoice_no_alias;     

        $billing_p['invoice_no'] = $invoice_no;
        $billing_p['invoice_no_alias'] = $invoice_no_alias;        
        $billing_p['patient_id'] = $patient_id;
        $billing_p['clinic_id'] = $parameters['clinic_id'];
        $billing_p['umr_no'] = $umr_no;
        $billing_p['created_by'] = $user_id;
        $billing_p['created_date_time'] = date('Y-m-d H:i:s');
        $billing_p['modified_by'] = $user_id;
        $billing_p['modified_date_time'] = date('Y-m-d H:i:s');

        $billing_id = $this->Generic_model->insertDataReturnId('billing', $billing_p);
        $billing = $parameters['billing'];
        if ($billing_id != '') {
            for ($b = 0; $b < count($billing); $b++) {
                $patient_bank['billing_id'] = $billing_id;
                $patient_bank['doctor_id'] = $parameters['doctor_id'];
                $patient_bank['billing_type'] = $billing[$b]['billing_type'];

                $patient_bank['mode_of_payment'] = $parameters['mode_of_payment'];

                $patient_bank['cheque_no'] = $parameters['cheque_no'];

                $patient_bank['deposit_date'] = date('Y-m-d', strtotime($parameters['deposit_date']));

                $patient_bank['neft_rtgs'] = $parameters['neft_rtgs'];

                $patient_bank['billing_date_time'] = date('Y-m-d H:i:s');

                $patient_bank['bank_name'] = $parameters['bank_name'];

                $patient_bank['amount'] = $billing[$b]['amount'];

                $patient_bank['created_by'] = $user_id;

                $patient_bank['created_date_time'] = date('Y-m-d H:i:s');

                $patient_bank['modified_by'] = $user_id;

                $patient_bank['modified_date_time'] = date('Y-m-d H:i:s');
                $this->Generic_model->insertData('billing_line_items', $patient_bank);
            }
        }
    }


    if ($parameters['mode_of_payment'] != 'free') {
        $clinic_deatails = $this->Generic_model->getSingleRecord('clinics', array('clinic_id' => $parameters['clinic_id']), $order = '');
        $doctor_deatails = $this->Generic_model->getSingleRecord('doctors', array('doctor_id' => $parameters['doctor_id']), $order = '');
        $departments = $this->Generic_model->getSingleRecord('department', array('department_id' => $doctor_deatails->department_id), $order = '');
        $billing = $this->Generic_model->getAllRecords('billing_line_items', array('billing_id' => $billing_id), $order = '');
        $data['clinic_logo'] = $clinic_deatails->clinic_logo;
        $data['clinic_phone'] = $clinic_deatails->clinic_phone;
        $data['clinic_name'] = $clinic_deatails->clinic_name;
        $data['clinic_address'] = $clinic_deatails->address;
        $data['doctor_name'] = "Dr. " . strtoupper($doctor_deatails->first_name . " " . $doctor_deatails->last_name);
        $data['qualification'] = $doctor_deatails->qualification;
        $data['department_name'] = $departments->departmentname;
        $data['patient_name'] = ucwords($parameters['title'] . "." . $parameters['first_name'] . " " . $parameters['last_name']);
        $data['age'] = $parameters['age'];
        $data['gender'] = $parameters['gender'];
        $data['umr_no'] = $umr_no;
        $data['patient_address'] = $parameters['address'];
        $data['billing'] = $billing;
        $data['invoice_no'] = $invoice_no;
        $data['invoice_no_alias'] = $invoice_no_alias;
        $data['payment_method'] = $parameters['mode_of_payment'];
        $html = $this->load->view('billing/generate_billing', $data, true);
        $pdfFilePath = "billing_" . $patient_id . $billing_id . ".pdf";
        $data['file_name'] = $pdfFilePath;

        $this->load->library('M_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output("./uploads/billings/" . $pdfFilePath, "F");


        $billFile['invoice_pdf'] = $data['file_name'];
        $this->Generic_model->updateData('billing', $billFile, array('billing_id' => $billing_id));
        $param['invoice_pdf'] = base_url() . 'uploads/billings/' . $pdfFilePath;
    } else {
        $param['invoice_pdf'] = NULL;
    }


    if ($patient_id != NULL) {

        $result = array('code' => '200', 'message' => 'Patient Registartion Successfully Done!', 'result' => array("umr_no" => $patient_reg['umr_no'], "patient_id" => "$patient_id", 'pdf_file' => $param['invoice_pdf']), 'requestname' => $method);

        $this->response($result);
    } else {

        $result = array('code' => '200', 'message' => 'Patient Registartion Failed', 'result' => NULL, 'requestname' => $method);

        $this->response($result);
    }
}

    // function used to call the list of patient w.r.to clinic
public function patient_list($parameters, $method, $user_id) {
    extract($parameters);

    $patients = $this->db->select("*")->from("patients")->where("clinic_id IS NULL")->order_by(" first_name ASC")->get()->result();

    if (count($patients) > 0) {
        $i = 0;
        foreach ($patients as $patient) {
            $patient_record['patient_list'][$i] = $patient;
            $i++;
        }

        $this->response(array('code' => '200', 'message' => 'success ', 'result' => $patient_record, 'requestname' => $method));

    }
}

   //list of patients w.r.t to clinic
public function patientsList($parameters, $method, $user_id) {

    extract($parameters);

    if (isset($parameters['mobile'])) {

        $mobile = DataCrypt($mobile,'decrypt');

        $patientRecord = $this->Generic_model->getSingleRecord('patients', array('clinic_id' => $clinic_id, 'mobile' => $mobile), $order = '');

        if (count($patientRecord)) {
            $patient['patient_list'][] = $patientRecord;
            $patient['patient_list']['mobile'] = DataCrypt($patientRecord->mobile,'decrypt');
            $this->response(array('code' => '200', 'message' => 'success ', 'result' => $patient, 'requestname' => $method));
        } else {
            $this->response(array('code' => '200', 'message' => 'No Data Found', 'result' => NULL), 200);
        }
    } else {

        $patients = $this->db->select("p.*,p.title,p.patient_id,p.age_unit,p.middle_name,p.umr_no,p.gender,p.email_id,p.photo,p.qrcode,s.state_id,s.state_name,d.district_id,d.district_name,p.pincode as ppcode")->from("patients p")->join("states s","p.state_id=s.state_id","left")->join("districts d","p.district_id=d.district_id","left")->where("p.clinic_id='".$clinic_id."'")->order_by("p.patient_id","desc")->get()->result();

        if (count($patients) > 0) {
            $i = 0;
            foreach ($patients as $patient) {
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

                $patient_val['patient_list'][$i]['patient_id'] = $patient->patient_id;
                $patient_val['patient_list'][$i]['title'] = ucwords($patient->title);
                $patient_val['patient_list'][$i]['first_name'] = strtoupper($patient->first_name);
                $patient_val['patient_list'][$i]['middle_name'] = strtoupper($patient->middle_name);
                $patient_val['patient_list'][$i]['last_name'] = strtoupper($patient->last_name);
                $patient_val['patient_list'][$i]['umr_no'] = $patient->umr_no;
                $patient_val['patient_list'][$i]['contact'] = DataCrypt($patient->mobile,'decrypt');
                $patient_val['patient_list'][$i]['age'] = $patient->age . " " . ucfirst($patient->age_unit);
                $patient_val['patient_list'][$i]['gender'] = $patient->gender;
                $patient_val['patient_list'][$i]['referred_by'] = $patient->referred_by;
                $patient_val['patient_list'][$i]['payment_status'] = $patient->payment_status;
                $patient_val['patient_list'][$i]['clinic_id'] = $patient->clinic_id;
                $patient_val['patient_list'][$i]['patient_condition'] = $patient->patient_condition;
                $patient_val['patient_list'][$i]['condition_months'] = $patient->condition_months;
                $patient_val['patient_list'][$i]['status'] = $patient_status;
                $patient_val['patient_list'][$i]['occupation'] = $patient->occupation;
                $patient_val['patient_list'][$i]['country'] = $patient->country;
                $patient_val['patient_list'][$i]['date_of_birth'] = $patient->date_of_birth;
                $patient_val['patient_list'][$i]['email'] = DataCrypt($patient->email_id,'decrypt');
                $patient_val['patient_list'][$i]['address'] = $patient->address_line . "," . $patient->district_name . "," . $patient->state_name . "," . $patient->ppcode;

                    //eliminate comma
                $patient_val['patient_list'][$i]['address'] = $this->eliminateComma($patient_val['patient_list'][$i]['address']);

                $patient_val['patient_list'][$i]['registartion_date'] = date("Y-m-d", strtotime($patient->created_date_time));
                $patient_val['patient_list'][$i]['photo'] = $path;
                $patient_val['patient_list'][$i]['qrcode'] = $qrcode;

                $video_info = $this->db->query("SELECT * from users where user_id='" . $patient->patient_id . "'")->row();

                $patient_val['patient_list'][$i]['qb_user_id'] = $video_info->qb_user_id;
                    //$patient_val['patient_list'][$i]['qb_password'] =$video_info->qb_password;
                $patient_val['patient_list'][$i]['qb_user_login'] = $video_info->qb_user_login;
                $patient_val['patient_list'][$i]['qb_user_fullname'] = $video_info->qb_user_fullname;
                $patient_val['patient_list'][$i]['qb_user_tag'] = $video_info->qb_user_tag;


                $i++;
            }
            $this->response(array('code' => '200', 'message' => 'success ', 'result' => $patient_val, 'requestname' => $method));
        } else {
            $this->response(array('code' => '200', 'message' => 'NO data Found', 'result' => NULL), 200);
        }
    }
}


public function immunization_status($department = NULL, $patient_id = NULL){
    if($department == 'Pediatrics'){
        $this->db->select('*');
        $this->db->from('patient_vaccine');
        $this->db->where('patient_id =',$patient->patient_id);
        $this->db->where('clinic_id =',$patient->clinic_id);

        $immunization_status = $this->db->get()->num_rows(); 

        if($immunization_status > 0){
            return 1;
        }else{
            return 0;
        }
    }else{
        return 0;
    }    
}

    //list of appointments w.r.t to user(nurse,doctor etc)
public function appointments($parameters, $method, $user_id) {

    $clinic_id = $parameters['clinic_id'];
    $role_id = $parameters['role_id'];
    $from_date = date('Y-m-d',strtotime($parameters['from_date']));
    $to_date = date('Y-m-d',strtotime($parameters['to_date']));

    $qry = "SELECT a.*,p.*,a.status as `appointment_status`,a.appointment_type,a.payment_status as `appointment_payment_status`,p.title,p.middle_name,p.patient_id,p.umr_no,p.age_unit,p.gender,p.payment_status as `registration_payment_status`,a.check_in_time,a.check_in_time,p.email_id,p.photo,p.qrcode,d.doctor_id,d.first_name as `d_firstname`,d.last_name as `d_lastname`, d.color_code, d.salutation,de.department_name,de.department_id,p.location,s.state_id,s.state_name,dis.district_id,dis.district_name,p.pincode as `ppcode`,p.created_date_time as registration_date FROM `appointments` a 
    inner join patients p on (a.patient_id=p.patient_id) 
    left join states s on (p.state_id=s.state_id)
    left join districts dis on (p.district_id=dis.district_id) 
    inner join doctors d on (a.doctor_id=d.doctor_id) 
    inner join department de on (d.department_id=de.department_id) 
    where a.clinic_id='" . $clinic_id . "' AND a.status not in ('drop','reschedule') ";

    if($role_id == 4){
        $qry .="and a.doctor_id='".$user_id."'";
    }

    // if mobile no is specified, then it should pull all the appointments of the concern person irrespective of the dates with different status
    if (isset($parameters['mobile'])) {
        $qry .= " AND (p.mobile = '" . $parameters['mobile'] . "' OR p.alternate_mobile = '".$parameters['mobile']."') ";
        } else { // if no mobile number specified then it will pull all the appointments for the current date
            $qry .= " AND a.appointment_date >= '" . $from_date . "' and a.appointment_date <= '".$to_date."'";
        }

        if($role_id == 6){ // Role is a Nurse
            $qry .= " ORDER BY FIELD(a.status,'vital_signs','checked_in','waiting','booked','in_consultation','closed'), if(FIELD(a.priority,'sick','pregnancy', 'elderly','children','other')=0,'none',0),a.appointment_time_slot asc,a.appointment_date desc,a.check_in_time asc";
        }

        if($role_id == 4){ // Role is a Doctor
            $qry .= " ORDER BY FIELD(a.status,'in_consultation','waiting','vital_signs','checked_in','booked','closed'), if(FIELD(a.priority,'sick','pregnancy', 'elderly','children','other')=0,'none',0),a.check_in_time asc,a.appointment_time_slot asc,a.appointment_date desc";
        }

        $patients = $this->db->query($qry)->result();

        if (count($patients) > 0) {

            $i = 0;

            foreach ($patients as $patient) {

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

                // Get Department of Doctor
                $department = $patient->department_name;

                $patient->age_unit = strtoupper(substr($patient->age_unit, 0, 1));
                
                $patient_ids[] = $patient->patient_id;
                $patient_val['appointment_list'][$i]['patient_id'] = (int)$patient->patient_id;
                $patient_val['appointment_list'][$i]['title'] = ucfirst($patient->title);
                $patient_val['appointment_list'][$i]['first_name'] = strtoupper($patient->first_name);
                $patient_val['appointment_list'][$i]['middle_name'] = strtoupper($patient->middle_name);
                $patient_val['appointment_list'][$i]['last_name'] = strtoupper($patient->last_name);
                $patient_val['appointment_list'][$i]['full_name'] =  ($patient->title ? ucfirst($patient->title).". " : "").strtoupper(trim($patient->first_name)." ".trim($patient->last_name));
                $patient_val['appointment_list'][$i]['umr_no'] = $patient->umr_no;
                $patient_val['appointment_list'][$i]['payment_status'] = (int)$patient->appointment_payment_status;
                $patient_val['appointment_list'][$i]['clinic_id'] = $patient->clinic_id;
                $patient_val['appointment_list'][$i]['referred_by_type'] = $patient->referred_by_type;
                $patient_val['appointment_list'][$i]['referred_by'] = $patient->referred_by;
                $patient_val['appointment_list'][$i]['country'] = $patient->country;
                $patient_val['appointment_list'][$i]['priority'] = $patient->priority;
                $patient_val['appointment_list'][$i]['occupation'] = $patient->occupation;
                $patient_val['appointment_list'][$i]['mobile'] = DataCrypt($patient->mobile,'decrypt');
                $patient_val['appointment_list'][$i]['alternate_mobile'] = DataCrypt($patient->alternate_mobile,'decrypt');
                $patient_val['appointment_list'][$i]['age'] = $patient->age . " " . ucfirst($patient->age_unit);
                $patient_val['appointment_list'][$i]['gender'] = $patient->gender;
                $patient_val['appointment_list'][$i]['location'] = $patient->location;
                $patient_val['appointment_list'][$i]['state_id'] = $patient->state_id;
                $patient_val['appointment_list'][$i]['district_id'] = $patient->district_id;
                $patient_val['appointment_list'][$i]['preferred_language'] = $patient->preferred_language;
                $patient_val['appointment_list'][$i]['patient_condition'] = $patient->patient_condition;
                $patient_val['appointment_list'][$i]['condition_months'] = $patient->condition_months;
                $patient_val['appointment_list'][$i]['appointment_id'] = (int)$patient->appointment_id;
                $patient_val['appointment_list'][$i]['appointment_date'] = $patient->appointment_date;
                $patient_val['appointment_list'][$i]['appointment_time'] = $patient->appointment_time_slot;
                $patient_val['appointment_list'][$i]['appointment_type'] = strtoupper(substr($patient->appointment_type, 0, 1));
                $patient_val['appointment_list'][$i]['status'] = $patient_status;
                $patient_val['appointment_list'][$i]['check_in_time'] = $check_in_time;
                $patient_val['appointment_list'][$i]['date_of_birth'] = ($patient->date_of_birth == '0000-00-00' ? "" : $patient->date_of_birth);
                $patient_val['appointment_list'][$i]['email'] = DataCrypt($patient->email_id,'decrypt');

                // Get Doctor Comments from doctor_patient
                $docComment = $this->Generic_model->getSingleRecord('doctor_patient',array('doctor_id'=>$patient->doctor_id,'patient_id'=>$patient->patient_id));

                $patient_val['appointment_list'][$i]['doctor_comments'] = $docComment->doctor_comment;

                $patient_val['appointment_list'][$i]['address'] = ucwords($patient->address_line);
                $patient_val['appointment_list'][$i]['pincode'] = $patient->pincode;
                $patient_val['appointment_list'][$i]['doctor_id'] = (int)$patient->doctor_id;
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

                $patient_val['appointment_list'][$i]['immunization_status'] = $this->immunization_status($department, $patient->patient_id);
                
                $i++;
            }
            
            $patientids = implode(",",$patient_ids);

            $patientRecord = $this->db->select("p.*,p.title,p.patient_id,p.age_unit,p.middle_name,p.umr_no,p.gender,p.email_id,p.photo,p.qrcode,p.location,s.state_id,s.state_name,d.district_id,d.district_name,p.pincode as ppcode")->from("patients p")->join("states s","p.state_id=s.state_id","left")->join("districts d","p.district_id=d.district_id","left")->where("p.patient_id IN (".$patientids.")")->order_by("p.patient_id desc")->get()->result();

            $p=0;

            // foreach($patientRecord as $patient1)
            // {
            //     if ($patient1->photo != NULL) {
            //         $path = base_url() . 'uploads/patients/' . $patient1->photo;
            //     } else {
            //         $path = NULL;
            //     }

            //     if ($patient1->qrcode != NULL) {
            //         $qrcode = base_url() . 'uploads/qrcodes/patients/' . $patient1->qrcode;
            //     } else {
            //         $qrcode = NULL;
            //     }

            //     $patient_val['patient_list'][$p]['patient_id'] = (int)$patient1->patient_id;
            //     $patient_val['patient_list'][$p]['title'] = ucwords($patient1->title);
            //     $patient_val['patient_list'][$p]['first_name'] = strtoupper($patient1->first_name);
            //     $patient_val['patient_list'][$p]['middle_name'] = strtoupper($patient1->middle_name);
            //     $patient_val['patient_list'][$p]['last_name'] = strtoupper($patient1->last_name);
            //     $patient_val['patient_list'][$p]['full_name'] =  ($patient1->title ? ucfirst($patient1->title).". " : "").strtoupper(trim($patient1->first_name)." ".trim($patient1->last_name));
            //     $patient_val['patient_list'][$p]['umr_no'] = $patient1->umr_no;
            //     $patient_val['patient_list'][$p]['mobile'] = $patient1->mobile;
            //     $patient_val['patient_list'][$p]['alternate_mobile'] = $patient1->alternate_mobile;
            //     $patient_val['patient_list'][$p]['age'] = $patient1->age . " " . ucfirst($patient1->age_unit);
            //     $patient_val['patient_list'][$p]['gender'] = $patient1->gender;
            //     $patient_val['patient_list'][$p]['referred_by'] = $patient1->referred_by;
            //     $patient_val['patient_list'][$p]['payment_status'] = (int)$patient1->payment_status;
            //     $patient_val['patient_list'][$p]['clinic_id'] = $patient1->clinic_id;
            //     $patient_val['patient_list'][$p]['referred_by_type'] = $patient1->referred_by_type;
            //     $patient_val['patient_list'][$p]['referred_by'] = $patient1->referred_by;
            //     $patient_val['patient_list'][$p]['patient_condition'] = $patient1->patient_condition;
            //     $patient_val['patient_list'][$p]['condition_months'] = $patient1->condition_months;
            //     $patient_val['patient_list'][$p]['status'] = $patient_status;
            //     $patient_val['patient_list'][$p]['occupation'] = $patient1->occupation;
            //     $patient_val['patient_list'][$p]['location'] = $patient->location;
            //     $patient_val['patient_list'][$p]['state_id'] = $patient1->state_id;
            //     $patient_val['patient_list'][$p]['district_id'] = $patient1->district_id;
            //     $patient_val['patient_list'][$p]['preferred_language'] = $patient1->preferred_language;
            //     $patient_val['patient_list'][$p]['country'] = $patient1->country;
            //     $patient_val['patient_list'][$p]['date_of_birth'] = ($patient1->date_of_birth == '0000-00-00' ? "" : $patient1->date_of_birth);
            //     $patient_val['patient_list'][$p]['email'] = $patient1->email_id;
            //     $patient_val['patient_list'][$p]['address'] = $patient1->address_line . "," . $patient1->district_name . "," . $patient1->state_name . "," . $patient1->ppcode;

            //         //eliminate comma
            //     $patient_val['patient_list'][$p]['address'] = $this->eliminateComma($patient_val['patient_list'][$p]['address']);
            //     $patient_val['patient_list'][$p]['pincode'] = $patient1->pincode;

            //     $patient_val['patient_list'][$p]['registartion_date'] = date("Y-m-d", strtotime($patient1->created_date_time));
            //     $patient_val['patient_list'][$p]['photo'] = $path;
            //     $patient_val['patient_list'][$p]['qrcode'] = $qrcode;

            //     $video_info = $this->db->select("*")->from("users")->where("user_id='" . $patient1->patient_id . "'")->get()->row();

            //     $patient_val['patient_list'][$p]['qb_user_id'] = $video_info->qb_user_id;
            //     $patient_val['patient_list'][$p]['qb_user_login'] = $video_info->qb_user_login;
            //     $patient_val['patient_list'][$p]['qb_user_fullname'] = $video_info->qb_user_fullname;
            //     $patient_val['patient_list'][$p]['qb_user_tag'] = $video_info->qb_user_tag;

            //     $patient_val['patient_list'][$p]['immunization_status'] = $this->immunization_status($department, $patient->patient_id);

            //     $p++;

            // }

            // Referal Doctors list
            // $this->db->select('rfd_id, doctor_name, mobile');
            // $this->db->from('referral_doctors');
            // $this->db->where('clinic_id',$clinic_id);

            // $referralDocRec = $this->db->get()->result_array();

            // if(count($referralDocRec) > 0){
            //     $doc = 0;
            //     foreach($referralDocRec as $refDoc) {
            //         $patient_val['referral_doctor_list'][$doc] = $refDoc;
            //         $doc++;
            //     }
            // }            
            
            $this->response(array('code' => '200', 'message' => 'success ', 'result' => $patient_val, 'requestname' => $method));
        } else {
            $this->response(array('code' => '200', 'message' => 'NO data Found', 'result' => NULL), 200);
        }
    }


    // Below function retrieves clinics referral doctors from Referral Doctors Masters
    public function referral_doctors($parameters, $method, $user_id){

        $clinic_id = $parameters['clinic_id'];

        // Referal Doctors list
        $this->db->select('rfd_id,clinic_id,doctor_name,department,qualification,mobile');
        $this->db->from('referral_doctors');
        $this->db->where('clinic_id',$clinic_id);

        $referralDocRec = $this->db->get()->result_array();
        
        if(count($referralDocRec) > 0){
            $doc = 0;
            foreach($referralDocRec as $refDoc) {
                $referral_doctor['doctors_list'][$doc] = $refDoc;
                $doc++;
            }
        }else{
            $referral_doctor = (object)[];
        }         

        $this->response(array('code' => '200', 'message' => 'success ', 'result' => $referral_doctor, 'requestname' => $method));
    }


    /*  
        03 Jul 2019 :: 1243pm 
        search_patient function 
        author: Uday Kanth Rapalli
    */ 
        public function search_patient($parameters, $method, $user_id) {

        // extract pramas
            extract($parameters);

        // get all patients related to the name 
        // get patient and related patients with mobile no.
        // get patient details with UMR no.
            $patientsRec = $this->db->select('patient_id, title, first_name, last_name, umr_no, gender, clinic_id, date_of_birth, age, occupation, country, mobile, alternate_mobile, photo, qrcode, allergy, location, guardian_id, payment_status')
            ->from('patients')
            ->group_start()
            ->where('mobile =',$srchParam)
            ->or_where('alternate_mobile =',$srchParam)
            ->or_where('umr_no =',$srchParam)
            ->or_like('mobile',$srchParam)
            ->or_like('alternate_mobile',$srchParam)
            ->or_like('first_name',$srchParam)
            ->or_like('last_name',$srchParam)
            ->group_end()
            ->where('clinic_id =',$clinic_id)->get()->result_array();

            if(count($patientsRec) > 0){
                $x = 0;
                foreach($patientsRec as $patient) {
                    extract($patient);
                    $full_name = '';
                // Make full name as per title
                    $title != '' ? $full_name = ucwords($title.". ".$first_name) : $full_name = $first_name;
                    $last_name != '' ? $full_name .= ucwords(" ".$last_name) : ''; 
                    $patient['photo'] = base_url('uploads/patients/'.$patient['photo']);
                    $patient['qrcode'] = base_url('uploads/qrcodes/patients/'.$patient['qrcode']);    
                    $patient['full_name'] = ucwords(strtolower($full_name));
                    $patients['appointment_list'][$x] = $patient;
                    $x++;
                }
                $code = "200";
                $message = "success";
            }else{
                $code = "201";
                $message = "No patients found";
            }

            $this->response(array('code' => $code, 'message' => $message, 'result' => $patients, 'requestname' => $method));

        }    


    /*  
        03 Jul 2019 :: 1243pm 
        search_patient_appointment function 
        author: Uday Kanth Rapalli
    */ 
        public function search_patient_appointment($parameters, $method, $user_id) {

        // extract pramas
            extract($parameters);

        // get all patients related to the name 
        // get patient and related patients with mobile no.
        // get patient details with UMR no.
            $patientsRec = $this->db->select('patient_id, title, first_name, last_name, umr_no, gender, clinic_id, referred_by, referred_by_type, date_of_birth, age, occupation, country, mobile, alternate_mobile, email_id as email, photo, qrcode, allergy, address_line as address, location, state_id, district_id, pincode, preferred_language, guardian_id, created_date_time as registartion_date')
            ->from('patients')
            ->group_start()
            ->where('mobile =',$srchParam)
            ->or_where('alternate_mobile =',$srchParam)
            ->or_where('umr_no =',$srchParam)
            ->or_like('mobile',$srchParam)
            ->or_like('alternate_mobile',$srchParam)
            ->or_like('first_name',$srchParam)
            ->or_like('last_name',$srchParam)
            ->group_end()
            ->where('clinic_id =',$clinic_id)->get()->result_array();

            if(count($patientsRec) > 0){

                $x = 0;
                foreach($patientsRec as $patient) {

                // Get Immunization Status 
                    $immuneCount = $this->db->query('select * from patient_vaccine where patient_id="'.$patient['patient_id'].'"')->num_rows();

                    ($immuneCount > 0 ? $patient['immunization_status'] = '1' : $patient['immunization_status'] = '0');

                    extract($patient);
                    $full_name = '';
                // Make full name as per title
                    $title != '' ? $full_name = ucwords($title.". ".$first_name) : $full_name = $first_name;
                    $last_name != '' ? $full_name .= ucwords(" ".$last_name) : ''; 
                    $patient['full_name'] = ucwords(strtolower($full_name));
                    $patient['photo'] = base_url('uploads/patients/'.$patient['photo']);
                    $patient['qrcode'] = base_url('uploads/qrcodes/patients/'.$patient['qrcode']);

                // Get the latest appointment id of this patient 
                    $this->db->select('A.appointment_id, A.doctor_id, CONCAT("Dr.",Doc.first_name, " ", Doc.last_name) as doctor_name, A.appointment_date, A.appointment_time_slot as appointment_time, A.check_in_time, A.check_out_time, A.priority, A.consultation_start_time, A.consultation_end_time, A.course_in_hospital as doctor_comments, A.status, Doc.department_id, Doc.color_code, Dep.department_name as department');
                    $this->db->from('appointments A');
                    $this->db->join('doctors Doc', 'A.doctor_id = Doc.doctor_id','left');
                    $this->db->join('department Dep', 'Doc.department_id = Dep.department_id','left');
                    $this->db->where('A.patient_id =',$patient['patient_id']);
                    $this->db->where('A.clinic_id =',$clinic_id);                
                    $this->db->group_start();
                    $this->db->where('A.status !=','drop');
                    $this->db->or_where('A.status !=','rescheduled');
                    $this->db->or_where('A.status !=','absent');
                    $this->db->group_end();
                    $this->db->order_by('A.appointment_id', 'DESC');
                    $this->db->limit(1);

                    $appointment = $this->db->get()->result_array();

                    $patient['qb_user_id'] = '';
                    $patient['qb_user_login'] = '';
                    $patient['qb_user_fullname'] = '';
                    $patient['qb_user_tag'] = '';

                // Merge Patient & Appointment array
                    $patient = array_merge($patient,$appointment[0]);

                    $patients['appointment_list'][$x] = $patient;

                    $x++;
                }
                $code = "200";
                $message = "success";
            }else{
                $code = "201";
                $message = "No patients found";
            }

            $this->response(array('code' => $code, 'message' => $message, 'result' => $patients, 'requestname' => $method));

        }    


    /* 
        function 'patient_details' renders full demographic details of the patient
        parameters : patient id
    */
        public function patient_details($parameters, $method, $user_id) {
            $patientRes = $this->Generic_model->getAllRecords('patients',array('patient_id'=>$parameters['patient_id']));
        }  


    /*
      funciton 'masters' renders master records of the following tables
      states
      districts
      doctors & doctor weekdays, time slots, consultaiton fee, consultation time information
    */

      public function masters($parameters, $method, $user_id) {

        $clinic_id = $parameters['clinic_id'];
        $profile_id = $parameters['profile_id'];
        $role_id = $parameters['role_id'];

        // get all states
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


        // get all doctors belongs to the clinic '$clinic_id'
        $doctors_list = $this->db->select("cd.*,d.*,de.*,cd.clinic_id")->from("clinic_doctor cd")->join(" doctors d","cd.doctor_id=d.doctor_id")->join("department de","d.department_id=de.department_id")->where("cd.clinic_id ='" . $clinic_id . "'")->group_by("cd.doctor_id")->order_by("d.doctor_id","asc")->get()->result();


        $d = 0;
        if (count($doctors_list) > 0) {

            foreach ($doctors_list as $doctor) {

                $video_info = $this->db->select("*")->from("users")->where("user_id='" . $doctor->doctor_id . "'")->get()->row();
                $param['doctor'][$d]['doctor_id'] = $doctor->doctor_id;
                $param['doctor'][$d]['doctor_name'] = "Dr. " . strtoupper($doctor->first_name . " " . $doctor->last_name);
                $param['doctor'][$d]['designation'] = $doctor->qualification;
                $param['doctor'][$d]['department_id'] = $doctor->department_id;
                $param['doctor'][$d]['department'] = $doctor->department_name;
                $param['doctor'][$d]['registration_code'] = $doctor->registration_code;
                $param['doctor'][$d]['color_code'] = $doctor->color_code;

                $param['doctor'][$d]['qb_user_id'] = $video_info->qb_user_id;
                //$param['doctor'][$d]['qb_password'] =$video_info->qb_password;
                $param['doctor'][$d]['qb_user_login'] = $video_info->qb_user_login;
                $param['doctor'][$d]['qb_user_fullname'] = $video_info->qb_user_fullname;
                $param['doctor'][$d]['qb_user_tag'] = $video_info->qb_user_tag;


                $clinics_list = $this->db->select("cd.*,c.*,cd.clinic_id")->from("clinic_doctor cd")->join("clinics c","cd.clinic_id=c.clinic_id")->where("cd.doctor_id ='" . $doctor->doctor_id . "'")->group_by("cd.clinic_id")->order_by("c.clinic_id","asc")->get()->result();

                if (count($clinics_list) > 0) {
                    $c = 0;
                    foreach ($clinics_list as $clinic) {
                        $cdw = 0;
                        $param['doctor'][$d]['clinics'][$c]['clinic_id'] = $clinic->clinic_id;
                        $param['doctor'][$d]['clinics'][$c]['clinic_name'] = $clinic->clinic_name;
                        $param['doctor'][$d]['clinics'][$c]['consultation_time'] = $clinic->consulting_time;
                        $param['doctor'][$d]['clinics'][$c]['consulting_fee'] = $clinic->consulting_fee;
                        $param['doctor'][$d]['clinics'][$c]['registration_fee'] = $clinic->registration_fee;
                        $param['doctor'][$d]['clinics'][$c]['pharmacy_discount'] = $clinic->pharmacy_discount;
                        $param['doctor'][$d]['clinics'][$c]['lab_discount'] = $clinic->lab_discount;

                        $week_day_list = $this->db->select("clinic_doctor_weekday_id,weekday")->from("clinic_doctor_weekdays")->where("clinic_doctor_id ='" . $clinic->clinic_doctor_id . "'")->group_by("weekday")->order_by("clinic_doctor_weekday_id","asc")->get()->result();

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
                                $sessions_list[] = date('h:i A', strtotime($wdls->from_time)) . "-" . date('h:i A', strtotime($wdls->to_time));
                                $cdws++;
                            }

                            $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['day'] = $week;

                            for ($i = 0; $i < count($sessions_list); $i++) {
                                $sl = explode("-", $sessions_list[$i]);
                                $time = date("H", strtotime($sl[0]));

                                if ($time < 12) {
                                    $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = "Morning";
                                    $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                                } else if ($time > 12 && $time < 17) {
                                    $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = "Afternoon";
                                    $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                                } else if ($time >= 17) {
                                    $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = "Evening";
                                    $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                                }
                            }
                            unset($sessions_list);
                            $cdw++;
                        }

                        $c++;
                    }
                } else {
                    $param['doctor'][$d]['clinics'] = array();
                }

                $d++;
            }
        } else {
            $param['doctor'] = array();
        }

        $this->response(array('code' => '200', 'message' => 'Region Masters', 'result' => $param, 'requestname' => $method));
    }

    /*
      funciton 'getDoctors' renders doctor records with the following information
      - Doctors
      - Doctor weekdays,
      - Day time slots,
      - Consultaiton fee,
      - Consultation time information
     */

      public function getDoctors($parameters, $method, $user_id) {

        $clinic_id = $parameters['clinic_id'];
        $profile_id = $parameters['profile_id'];
        $role_id = $parameters['role_id'];

        // get all doctors belongs to the clinic '$clinic_id'
        $doctors_list = $this->db->select("cd.*,d.*,de.*,cd.clinic_id")->from("clinic_doctor cd")->join("doctors d","cd.doctor_id=d.doctor_id")->join("department de","d.department_id=de.department_id")->where("cd.clinic_id ='" . $clinic_id . "'")->group_by("cd.doctor_id")->order_by("d.doctor_id","asc")->get()->result();

        $d = 0;
        if (count($doctors_list) > 0) {

            foreach ($doctors_list as $doctor) {
                $param['doctor'][$d]['doctor_id'] = $doctor->doctor_id;
                $param['doctor'][$d]['doctor_name'] = "Dr. " . strtoupper($doctor->first_name . " " . $doctor->last_name);
                $param['doctor'][$d]['designation'] = $doctor->qualification;
                $param['doctor'][$d]['department'] = $doctor->department_name;
                $param['doctor'][$d]['registration_code'] = $doctor->registration_code;
                $param['doctor'][$d]['color_code'] = $doctor->color_code;

                $clinics_list = $this->db->select("cd.*,c.*,cd.clinic_id")->from("clinic_doctor cd")->join("clinics c","cd.clinic_id=c.clinic_id")->where("cd.doctor_id ='" . $doctor->doctor_id . "'")->group_by("cd.clinic_id")->order_by("c.clinic_id","asc")->get()->result();

                if (count($clinics_list) > 0) {
                    $c = 0;
                    foreach ($clinics_list as $clinic) {
                        $cdw = 0;
                        $param['doctor'][$d]['clinics'][$c]['clinic_id'] = $clinic->clinic_id;
                        $param['doctor'][$d]['clinics'][$c]['clinic_name'] = $clinic->clinic_name;
                        $param['doctor'][$d]['clinics'][$c]['consultation_time'] = $clinic->consulting_time;
                        $param['doctor'][$d]['clinics'][$c]['consulting_fee'] = $clinic->consulting_fee;
                        $param['doctor'][$d]['clinics'][$c]['registration_fee'] = $clinic->registration_fee;
                        $param['doctor'][$d]['clinics'][$c]['pharmacy_discount'] = $clinic->pharmacy_discount;
                        $param['doctor'][$d]['clinics'][$c]['lab_discount'] = $clinic->lab_discount;

                        $week_day_list = $this->db->select("clinic_doctor_weekday_id,weekday")->from("clinic_doctor_weekdays")->where("clinic_doctor_id ='" . $clinic->clinic_doctor_id . "'")->group_by("weekday")->order_by("clinic_doctor_weekday_id","asc")->get()->result();



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
                                $sessions_list[] = date('h:i A', strtotime($wdls->from_time)) . "-" . date('h:i A', strtotime($wdls->to_time));
                                $cdws++;
                            }

                            $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['day'] = $week;

                            for ($i = 0; $i < count($sessions_list); $i++) {
                                $sl = explode("-", $sessions_list[$i]);
                                $time = date("H", strtotime($sl[0]));

                                if ($time < 12) {
                                    $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = "Morning";
                                    $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                                } else if ($time > 12 && $time < 17) {
                                    $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = "Afternoon";
                                    $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                                } else if ($time >= 17) {
                                    $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['session'] = "Evening";
                                    $param['doctor'][$d]['clinics'][$c]['working_days'][$cdw]['timings'][$i]['schedule'] = $sl[0] . '-' . $sl[1];
                                }
                            }
                            unset($sessions_list);
                            $cdw++;
                        }

                        $c++;
                    }
                } else {
                    $param['doctor'][$d]['clinics'] = array();
                }

                $d++;
            }
        } else {
            $param['doctor'] = array();
        }

        $this->response(array('code' => '200', 'message' => 'Region Masters', 'result' => $param, 'requestname' => $method));
    }

    public function investigations($parameters, $method, $user_id) {
        $clinic_id = $parameters['clinic_id'];

        $investigation_details = $this->Generic_model->getAllRecords('investigations', $condition = '', $order = '');
        $in = 0;
        if (count($investigation_details) > 0) {
            foreach ($investigation_details as $investigation) {
                $param['investigation'][$in]['investigation_id'] = $investigation->investigation_id;
                $param['investigation'][$in]['investigation_code'] = $investigation->investigation_code;
                $param['investigation'][$in]['investigation_name'] = $investigation->investigation;
                $param['investigation'][$in]['category'] = $investigation->category;
                $param['investigation'][$in]['mrp'] = $investigation->mrp;
                $in++;
            }
        } else {
            $param['investigation'] = array();
        }
        $this->response(array('code' => '200', 'message' => 'Investigations', 'result' => $param, 'requestname' => $method));
    }

    public function procedure_list($parameters, $method, $user_id) {
        $doctor_id = $parameters["doctor_id"];
        $clinic_id = $parameters['clinic_id'];
        $patient_id = $parameters['patient_id'];
        $appointmet_id = $parameters['appointment_id'];
        $list = $this->db->select("*")->from("medical_procedures mp")->join("procedure_department pd","mp.medical_procedure_id = pd.medical_procedure_id")->where("department_id='".$parameters['department_id']."'")->get()->result();
        $i = 0;
        if (count($list) > 0) {
            foreach ($list as $key => $value) {
                $dept_info = $this->Generic_model->getSingleRecord('department',array('department_id'=>$value->department_id),'');
                $data['procedure_list'][$i]['medical_procedure_id'] = $value->medical_procedure_id;
                $data['procedure_list'][$i]['department_name'] = $dept_info->department_name;
                $data['procedure_list'][$i]['procedure_name'] = $value->medical_procedure;
                $data['procedure_list'][$i]['pdf_file'] = base_url('uploads/procedures/' . $value->file_name);
                $data['procedure_list'][$i]['procedure_url'] = base_url("procedure_update/patient_producer_list/" . $patient_id . "/" . $doctor_id . "/" . $appointmet_id . "/" . $value->medical_procedure_id . "/" . $clinic_id);
                $i++;
            }
        } else {
            $data['procedure_list'] = NULL;
        }
        $this->response(array('code' => '200', 'message' => 'Procedure List', 'result' => $data, 'requestname' => $method));
    }

    // this function 
    public function get_schedule($parameters, $method, $user_id) {

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
            $bs = $this->db->select("*")->from("appointments")->where("clinic_id='" . $clinic_id . "' and doctor_id='" . $doctor_id . "' and appointment_date='" . $date . "'")->get()->result();
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


    //saving blocked dates of doctor
    public function calendar_blocking($parameters, $method, $user_id) {
        if (count($parameters) > 0) {
            $data['doctor_id'] = $parameters['doctor_id'];
            $data['clinic_id'] = $parameters['clinic_id'];
            $data['dates'] = implode(",", $parameters['blocked_dates']);
            $data['status'] = 1;
            $data['created_by'] = $user_id;
            $data['modified_by'] = $user_id;
            $data['created_date_time'] = date('Y-m-d H:i:s');
            $data['modified_date_time'] = date('Y-m-d H:i:s');

            $exists = $this->db->select('*')->from('calendar_blocking')->where('clinic_id = "' . $parameters['clinic_id'] . '" and doctor_id = "' . $parameters['doctor_id'] . '"')->get()->row();
            if ($exists) {
                $ok = $this->Generic_model->updateData('calendar_blocking', $data, array('doctor_id' => $parameters['doctor_id'], 'clinic_id' => $parameters['clinic_id']));
            } else {
                $ok = $this->Generic_model->insertData('calendar_blocking', $data);
            }
            if ($ok) {
                $this->response(array('code' => '200', 'message' => 'Requested Dates Are Blocked Successfully', 'requestname' => $method));
            } else {
                $this->response(array('code' => '400', 'message' => 'error'));
            }
        } else {
            $this->response(array('code' => '400', 'message' => 'no data'));
        }
    }

    // below funciton helps to update the due amount payment in appointments and get invoice
    public function pay_due_amount($parameters, $method, $user_id) {

        $appointment_id = $parameters['appointment_id'];
        $appointment_info = $this->db->select("*")->from("appointments")->where("appointment_id='" . $appointment_id . "'")->get()->row();

        // Generate Invoice and Receipt no
        $invoice_no_alias = generate_invoice_no($parameters['clinic_id']);
        $invoice_no = $parameters['clinic_id'].$invoice_no_alias;     

        // Params for billing master
        $billing_master['discount_status'] = $parameters['discount_status'];
        $billing_master['invoice_no'] = $invoice_no;
        $billing_master['invoice_no_alias'] = $invoice_no_alias;
        $billing_master['patient_id'] = $patient_id;
        $billing_master['clinic_id'] = $parameters['clinic_id'];
        $billing_master['umr_no'] = $umr_no;
        $billing_master['created_by'] = $user_id;
        $billing_master['created_date_time'] = date('Y-m-d H:i:s');
        $billing_master['modified_by'] = $user_id;
        $billing_master['modified_date_time'] = date('Y-m-d H:i:s');
        $billing_master['billing_type'] = $parameters['billing_type'];
        $billing_master['payment_mode'] = $parameters['payment_mode'];
        $billing_master['cheque_no'] = $parameters['cheque_no'];
        $billing_master['refference_no'] = $parameters['refference_no'];
        $billing_master['deposit_date'] = $parameters['deposit_date'];
        $billing_master['discount_status'] = $parameters['discount_status'];

        // get inserted billing id
        $billing_id = $this->Generic_model->insertDataReturnId('billing', $billing_master);

        // update payment status of appointment
        $this->Generic_model->updateData('appointments', array('payment_status' => $parameters['con_payment_status']), array('appointment_id' => $appointment_id));

        $billing_line_items = $parameters['billing_line_items'];

        if ($billing_id != '') { // if billing id exists
            for ($b = 0; $b < count($billing_line_items); $b++) {
                $patient_bank['billing_id'] = $billing_id;
                $patient_bank['item_information'] = isset($billing_line_items[$b]['item_information']) ? $billing_line_items[$b]['item_information'] : '';
                $patient_bank['quantity'] = isset($billing_line_items[$b]['quantity']) ? $billing_line_items[$b]['quantity'] : 0;
                $patient_bank['discount'] = isset($billing_line_items[$b]['discount']) ? $billing_line_items[$b]['discount'] : 0;
                $patient_bank['amount'] = isset($billing_line_items[$b]['amount']) ? $billing_line_items[$b]['amount'] : 0;
                $patient_bank['status'] = 1;
                $patient_bank['created_by'] = $user_id;
                $patient_bank['created_date_time'] = date('Y-m-d H:i:s');
                $patient_bank['modified_by'] = $user_id;
                $patient_bank['modified_date_time'] = date('Y-m-d H:i:s');

                $this->Generic_model->insertData('billing_line_items', $patient_bank);
            }

            // get doctor appointment information
            $doctors_info = $this->db->select("*")->from("doctors")->where("doctor_id='" . $appointment_info->doctor_id . "'")->get()->row();
            $dept_info = $this->db->select("*")->from("department")->where("department_id='" . $doctors_info->department_id . "'")->get()->row();
            $param['appointment']['appointment_id'] = $appointment_id;
            $param['appointment']['clinic_id'] = $appointment_info->clinic_id;
            $param['appointment']['patient_id'] = $appointment_info->patient_id;
            $param['appointment']['umr_no'] = $umr_no;
            $param['appointment']['doctor_id'] = $appointment_info->doctor_id;
            $param['appointment']['doctor_name'] = "Dr. " . strtoupper($doctors_info->first_name . " " . $doctors_info->last_name);
            $param['appointment']['department'] = $dept_info->department_name;
            $param['appointment']['department_id'] = $doctors_info->department_id;
            $param['appointment']['appointment_type'] = $appointment_info->appointment_type;
            $param['appointment']['appointment_date'] = $appointment_info->appointment_date;
            $param['appointment']['appointment_time_slot'] = $appointment_info->appointment_time_slot;
            $param['appointment']['priority'] = $appointment_info->priority;
            $param['appointment']['status'] = $appointment_info->status;

            // success response
            $msg = 'Payment done successfully';
        } else {
            // error response
            $msg = "Payment failed";
            $param = array();
        }

        $this->response(array('code' => '200', 'message' => $msg, 'result' => $param, 'requestname' => $method));
    }

    /*
    //creating appointment with payment
    public function book_appointment($parameters, $method, $user_id) {


        echo '<pre>';
        print_r($parameters);
        echo '</pre>';
        exit();

        // Default status of an appointment
        $status = 'booked';

        // Get role of the logged in user
        $userRec = $this->db->select('U.role_id, R.role_name')->from('users U')->join('roles R','U.role_id = R.role_id')->where('U.user_id =',$user_id)->get()->row();

        if(count($userRec) > 0){
            if($userRec->role_name == 'Doctor') {
                $status = 'in_consultation';
            }else if($userRec->role_name == 'Nurse'){
                $status = 'checked_in';
            }
        }

        $clinic_id = $parameters['clinic_id'];
        $doctor_id = $parameters['doctor_id'];
        $patient_id = $parameters['patient_id'];
        $umr_no = $parameters['umr_no'];
        $priority = $parameters['priority'];
        $billing_type = $parameters['billing_type'];

        // Checking Appointment Duplication
        $duplicationStatus = $this->db->select("status")->from("appointments")->where("clinic_id='" . $clinic_id . "' and doctor_id='" . $doctor_id . "' and patient_id='" . $patient_id . "' and appointment_date='" . $parameters['appointment_date'] . "' and (status != 'closed' OR status != 'drop' OR status != 'rescheduled')")->get()->num_rows();

        if($duplicationStatus == 0){
        	// Create an appointment
        	// Start capturing the variables
        	if ($priority != '' || $priority != NULL) {
	            $pr = $priority;
	        } else {
	            $pr = "none";
	        }

	        $appointment_date = date('Y-m-d', strtotime($parameters['appointment_date']));
        	$appointment_time_slot = date('H:i', strtotime($parameters['appointment_time_slot']));

        	
        }else{
        	// An open appointment exists
        }

        exit();

        

        

        $parent_appointment = $this->db->select("*")->from("appointments")->where("clinic_id='" . $clinic_id . "' and doctor_id='" . $doctor_id . "' and patient_id='" . $patient_id . "'")->order_by("appointment_id","asc")->get()->row();

        // Payment status of patient updating
        $this->Generic_model->updateData('patients', array('payment_status' => $parameters['reg_payment_status']), array('patient_id' => $patient_id));

        if (count($parent_appointment) == 0) {
            // $appointment_date = date('Y-m-d', strtotime($parameters['appointment_date']));
            // $appointment_time_slot = date('H:i', strtotime($parameters['appointment_time_slot']));

            $appointment['clinic_id'] = $parameters['clinic_id'];
            $appointment['patient_id'] = $patient_id;
            $appointment['umr_no'] = $umr_no;
            $appointment['doctor_id'] = $parameters['doctor_id'];
            $appointment['appointment_type'] = "New";
            $appointment['appointment_date'] = $appointment_date;
            $appointment['appointment_time_slot'] = $appointment_time_slot;
            $appointment['priority'] = $pr;
            $appointment['payment_status'] = $parameters['con_payment_status'];
            $appointment['status'] = $status;
            $appointment['created_by'] = $user_id;
            $appointment['modified_by'] = $user_id;
            $appointment['created_date_time'] = date('Y-m-d H:i:s');
            $appointment['modified_date_time'] = date('Y-m-d H:i:s');

            // Get appointment id
            $appointment_id = $this->Generic_model->insertDataReturnId("appointments", $appointment);

            // Update the payment status for the created appointment
            // $this->Generic_model->updateData('appointments', array('payment_status' => $parameters['con_payment_status']), array('appointment_id' => $appointment_id));

            $doc_info = $this->Generic_model->getSingleRecord('doctors',array('doctor_id'=>$parameters['doctor_id']),'');
            $clinic_info = $this->Generic_model->getSingleRecord('clinics',array('clinic_id'=>$parameters['clinic_id']),'');
            $patient_info = $this->Generic_model->getSingleRecord('patients',array('patient_id'=>$patient_id),'');
            $user_info = $this->Generic_model->getSingleRecord('users',array('user_id'=>$patient_id),'');

            // Sending mail after appointment booking if the patient record is having email information 
            if($patient_info->email_id != '') {
                $from = 'donotreply';
                $to = $user_info->email_id;
                $subject = "Appointment with Dr." . $doc_info->first_name . ' ' . $doc_info->last_name . " on " . $appointment_date . " @" . $appointment_time_slot;

                $message = "Dear " . ucwords($patient_info->first_name . ' &nbsp;' . $patient_info->last_name) . ",
                <br><br>Your appointment  is fixed with Dr." . $doc_info->first_name . ' ' . $doc_info->last_name . ". at " . $clinic_info->clinic_name . " on " . $appointment_date . " @ " . $appointment_time_slot . ".<br><br> Please be present atleast 15 minutes before your scheduled time.
                <br><br>Have a good day.";

                $this->mail_send->Content_send_all_mail($from, $to, $subject, '', '', $message);
            }
            // END 

            if ($parameters['payment_type'] != 'free') {
                $inr = $this->db->select("count(*) as invoiceno")->from("billing")->where("clinic_id='" . $parameters['clinic_id'] . "'")->get()->row();
                $inv_gen = ($inr->invoiceno) + 1;

                $receipt_no = 'RECEIPT-' . $parameters['clinic_id'] . '-' . $inv_gen;
                $invoice_no = 'INV-' . $parameters['clinic_id'] . '-' . $inv_gen;

                $billing_master['receipt_no'] = $receipt_no;
                $billing_master['discount_status'] = $parameters['discount_status'];
                $billing_master['invoice_no'] = $invoice_no;
                $billing_master['patient_id'] = $patient_id;
                $billing_master['clinic_id'] = $parameters['clinic_id'];
                $billing_master['umr_no'] = $umr_no;
                $billing_master['created_by'] = $user_id;
                $billing_master['created_date_time'] = date('Y-m-d H:i:s');
                $billing_master['modified_by'] = $user_id;
                $billing_master['modified_date_time'] = date('Y-m-d H:i:s');
                $billing_master['billing_type'] = $billing_type;
                $billing_master['payment_mode'] = $parameters['payment_mode'];
                $billing_master['cheque_no'] = $parameters['cheque_no'];
                $billing_master['refference_no'] = $parameters['refference_no'];
                $billing_master['deposit_date'] = $parameters['deposit_date'];
                $billing_master['discount_status'] = $parameters['discount_status'];

                $billing_id = $this->Generic_model->insertDataReturnId('billing', $billing_master);
                $billing_line_items = $parameters['billing_line_items'];

                if ($billing_id != '') {
                    for ($b = 0; $b < count($billing_line_items); $b++) {
                        $patient_bank['billing_id'] = $billing_id;
                        $patient_bank['item_information'] = $billing_line_items[$b]['item_information'];
                        $patient_bank['quantity'] = $billing_line_items[$b]['quantity'];
                        $patient_bank['discount'] = $billing_line_items[$b]['discount'];
                        $patient_bank['amount'] = $billing_line_items[$b]['amount'];
                        $patient_bank['created_by'] = $user_id;
                        $patient_bank['created_date_time'] = date('Y-m-d H:i:s');
                        $patient_bank['modified_by'] = $user_id;
                        $patient_bank['modified_date_time'] = date('Y-m-d H:i:s');

                        $this->Generic_model->insertData('billing_line_items', $patient_bank);
                    }
                }

                $clinic_deatails = $this->Generic_model->getSingleRecord('clinics', array('clinic_id' => $parameters['clinic_id']), $order = '');
                $doctor_deatails = $this->Generic_model->getSingleRecord('doctors', array('doctor_id' => $parameters['doctor_id']), $order = '');
                $departments = $this->Generic_model->getSingleRecord('department', array('department_id' => $doctor_deatails->department_id), $order = '');
                $billing = $this->Generic_model->getAllRecords('billing_line_items', array('billing_id' => $billing_id), $order = '');
                $patient_details = $this->Generic_model->getSingleRecord('patients', array('patient_id' => $patient_id), $order = '');
                $district_details = $this->Generic_model->getSingleRecord('districts', array('district_id' => $patient_details->district_id), $order = '');
                $state_details = $this->Generic_model->getSingleRecord('states', array('state_id' => $patient_details_state_id), $order = '');

                $data['clinic_logo'] = $clinic_deatails->clinic_logo;
                $data['clinic_phone'] = $clinic_deatails->clinic_phone;
                $data['clinic_name'] = $clinic_deatails->clinic_name;
                $data['clinic_address'] = $clinic_deatails->address;
                $data['doctor_name'] = "Dr. " . strtoupper($doctor_deatails->first_name . " " . $doctor_deatails->last_name);
                $data['qualification'] = $doctor_deatails->qualification;
                $data['department_name'] = $departments->departmentname;
                $data['patient_name'] = "Mr. " . strtoupper($patient_details->title . "." . $patient_details->first_name . " " . $patient_details->last_name);
                $data['age'] = $patient_details->age . ' ' . $patient_details->age_unit;
                $data['gender'] = $patient_details->gender;
                $data['umr_no'] = $umr_no;
                $data['patient_address'] = $patient_details->address_line . "," . $district_details->district_name . "," . $state_details->state_name . "," . $patient_details->pincode;
                $data['billing'] = $billing;
                $data['invoice_no'] = $invoice_no;
                $data['receipt_no'] = $receipt_no;
                $data['payment_method'] = $parameters['mode_of_payment'];
                $data['discount'] = $parameters['discount'];
                $html = $this->load->view('billing/generate_billing', $data, true);
                $pdfFilePath = "billing_" . $patient_id . round($billing_id) . ".pdf";
                $data['file_name'] = $pdfFilePath;

                $this->load->library('M_pdf');
                $this->m_pdf->pdf->WriteHTML($html);
                $this->m_pdf->pdf->Output("./uploads/billings/" . $pdfFilePath, "F");
                $billFile['invoice_pdf'] = $data['file_name'];
                $this->Generic_model->updateData('billing', $billFile, array('billing_id' => $billing_id));
                $pdf = base_url() . 'uploads/billings/' . $pdfFilePath;
                $param['appointment']['pdf_file'] = $pdf;
            } else {
                $param['appointment']['pdf_file'] = NULL;
            }
        } else {
            $appointment_date = date('Y-m-d', strtotime($parameters['appointment_date']));
            $appointment_time_slot = date('H:i', strtotime($parameters['appointment_time_slot']));
            $appointment['clinic_id'] = $parameters['clinic_id'];
            $appointment['patient_id'] = $patient_id;
            $appointment['umr_no'] = $umr_no;
            $appointment['doctor_id'] = $parameters['doctor_id'];
            $appointment['appointment_type'] = "New";
            $appointment['appointment_date'] = $appointment_date;
            $appointment['appointment_time_slot'] = $appointment_time_slot;
            $appointment['priority'] = $pr;
            $appointment['status'] = $status;
            $appointment['created_by'] = $user_id;
            $appointment['modified_by'] = $user_id;
            $appointment['created_date_time'] = date('Y-m-d H:i:s');
            $appointment['modified_date_time'] = date('Y-m-d H:i:s');

            $appointment_id = $this->Generic_model->insertDataReturnId("appointments", $appointment);

            $this->Generic_model->updateData('appointments', array('payment_status' => $parameters['con_payment_status']), array('appointment_id' => $appointment_id));

            if ($parameters['payment_type'] != 'free') {
                $inr = $this->db->select("count(*) as invoiceno")->from("billing")->where("clinic_id='" . $parameters['clinic_id'] . "'")->get()->row();
                $inv_gen = ($inr->invoiceno) + 1;
                $receipt_no = 'RECEIPT-' . $parameters['clinic_id'] . '-' . $inv_gen;
                $invoice_no = 'INV-' . $parameters['clinic_id'] . '-' . $inv_gen;

                $billing_master['receipt_no'] = $receipt_no;
                $billing_master['discount_status'] = $parameters['discount_status'];
                $billing_master['invoice_no'] = $invoice_no;
                $billing_master['patient_id'] = $patient_id;
                $billing_master['clinic_id'] = $parameters['clinic_id'];
                $billing_master['umr_no'] = $umr_no;
                $billing_master['created_by'] = $user_id;
                $billing_master['created_date_time'] = date('Y-m-d H:i:s');
                $billing_master['modified_by'] = $user_id;
                $billing_master['modified_date_time'] = date('Y-m-d H:i:s');
                $billing_master['billing_type'] = $billing_type;
                $billing_master['payment_mode'] = $parameters['payment_mode'];
                $billing_master['cheque_no'] = $parameters['cheque_no'];
                $billing_master['refference_no'] = $parameters['refference_no'];
                $billing_master['deposit_date'] = $parameters['deposit_date'];
                $billing_master['discount_status'] = $parameters['discount_status'];

                $billing_id = $this->Generic_model->insertDataReturnId('billing', $billing_master);
                $billing_line_items = $parameters['billing'];
                if ($billing_id != '') {
                    for ($b = 0; $b < count($billing); $b++) {
                        $patient_bank['billing_id'] = $billing_id;
                        $patient_bank['item_information'] = $billing_line_items[$b]['item_information'];
                        $patient_bank['quantity'] = $billing_line_items[$b]['quantity'];
                        $patient_bank['discount'] = $billing_line_items[$b]['discount'];
                        $patient_bank['amount'] = $billing_line_items[$b]['amount'];
                        $patient_bank['created_by'] = $user_id;
                        $patient_bank['created_date_time'] = date('Y-m-d H:i:s');
                        $patient_bank['modified_by'] = $user_id;
                        $patient_bank['modified_date_time'] = date('Y-m-d H:i:s');

                        $this->Generic_model->insertData('billing_line_items', $patient_bank);
                    }
                }

                $clinic_deatails = $this->Generic_model->getSingleRecord('clinics', array('clinic_id' => $parameters['clinic_id']), $order = '');
                $doctor_deatails = $this->Generic_model->getSingleRecord('doctors', array('doctor_id' => $parameters['doctor_id']), $order = '');
                $departments = $this->Generic_model->getSingleRecord('department', array('department_id' => $doctor_deatails->department_id), $order = '');
                $billing = $this->Generic_model->getAllRecords('billing_line_items', array('billing_id' => $billing_id), $order = '');
                $patient_details = $this->Generic_model->getSingleRecord('patients', array('patient_id' => $patient_id), $order = '');
                $district_details = $this->Generic_model->getSingleRecord('districts', array('district_id' => $patient_details->district_id), $order = '');

                $state_details = $this->Generic_model->getSingleRecord('states', array('state_id' => $patient_details_state_id), $order = '');

                $data['clinic_logo'] = $clinic_deatails->clinic_logo;
                $data['clinic_phone'] = $clinic_deatails->clinic_phone;
                $data['clinic_name'] = $clinic_deatails->clinic_name;
                $data['clinic_address'] = $clinic_deatails->address;
                $data['doctor_name'] = "Dr. " . strtoupper($doctor_deatails->first_name . " " . $doctor_deatails->last_name);
                $data['qualification'] = $doctor_deatails->qualification;
                $data['department_name'] = $departments->departmentname;
                $data['patient_name'] = ucfirst($patient_details->title) . "." . strtoupper($patient_details->first_name . " " . $patient_details->last_name);
                $data['age'] = $patient_details->age . ' ' . $patient_details->age_unit;
                $data['gender'] = $patient_details->gender;
                $data['umr_no'] = $umr_no;
                $data['patient_address'] = $patient_details->address_line . "," . $district_details->district_name . "," . $state_details->state_name . "," . $patient_details->pincode;
                $data['billing'] = $billing;
                $data['invoice_no'] = $invoice_no;
                $data['payment_method'] = $parameters['mode_of_payment'];
                $data['discount'] = $parameters['discount'];
                $html = $this->load->view('billing/generate_billing', $data, true);
                $pdfFilePath = "billing_" . $patient_id . $billing_id . ".pdf";
                $data['file_name'] = $pdfFilePath;

                $this->load->library('M_pdf');
                $this->m_pdf->pdf->WriteHTML($html);
                $this->m_pdf->pdf->Output("./uploads/billings/" . $pdfFilePath, "F");
                $billFile['invoice_pdf'] = $data['file_name'];
                $this->Generic_model->updateData('billing', $billFile, array('billing_id' => $billing_id));
                $pdf = base_url() . 'uploads/billings/' . $pdfFilePath;
                $param['appointment']['pdf_file'] = $pdf;
            } else {
                $param['appointment']['pdf_file'] = NULL;
            }
        }

        $doctors_info = $this->Generic_model->getSingleRecord('doctors',array('doctor_id'=>$doctor_id),'');

        $dept_info = $this->Generic_model->getSingleRecord('department',array('department_id'=>$doctors_info->doctor_id),'');

        $param['appointment']['appointment_id'] = $appointment_id;
        $param['appointment']['clinic_id'] = $clinic_id;
        $param['appointment']['patient_id'] = $patient_id;
        $param['appointment']['umr_no'] = $umr_no;
        $param['appointment']['doctor_id'] = $doctor_id;
        $param['appointment']['doctor_name'] = "Dr. " . strtoupper($doctors_info->first_name . " " . $doctors_info->last_name);
        $param['appointment']['department'] = $dept_info->department_name;
        $param['appointment']['department_id'] = $doctors_info->department_id;
        $param['appointment']['appointment_type'] = $appointment['appointment_type'];
        $param['appointment']['appointment_date'] = $appointment_date;
        $param['appointment']['appointment_time_slot'] = $parameters['appointment_time_slot'];
        $param['appointment']['priority'] = $appointment['priority'];
        $param['appointment']['status'] = $appointment['status'];

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
    */


    // Book and appointment
    public function book_appointment($parameters, $method, $user_id) {

        // By default the payment = 0
        $payment = 0;

        extract($parameters);
        
        // Get clinic info
        //$clinic_info = $this->Generic_model->getSingleRecord('clinics', array('clinic_id' => $clinic_id), $order = '');
        $clinic_info = $this->db->select('clinic_id,clinic_name')->from('clinics')->where('clinic_id =',$clinic_id)->get()->row();
        
        // Get patient info
        $patient_info = $this->db->select('patient_id, title, first_name, last_name, mobile, email_id, payment_status, status')->from('patients')->where('patient_id =',$patient_id)->get()->row();

        // Get doctor info
        $doctor_info = $this->db->select('doctor_id, salutation, first_name, last_name, registration_code, department_id')->from('doctors')->where('doctor_id =',$doctor_id)->get()->row();

        // Get Department info
        $dept_info = $this->db->select('department_id, department_name')->from('department')->where('department_id =', $doctor_info->department_id)->get()->row();


        // Convert date to m/d/Y format. Currently it is d/m/Y
        $appointment_date = date("Y-m-d", strtotime($appointment_date));

        /*
        Check if there is already an appointment on the following below criteria
        Selected doctor
        Selected Date
        Selected Patient
        */
        $this->db->select('appointment_id, appointment_date, appointment_time_slot, patient_id, doctor_id');
        $this->db->from('appointments');
        $this->db->where('appointment_date =',$appointment_date);
        $this->db->where('clinic_id =',$clinic_id);
        $this->db->where('doctor_id =',$doctor_id);
        $this->db->where('patient_id =',$patient_id);
        $this->db->group_start();
        $this->db->where('status =','booked');
        $this->db->or_where('status =','checked_in');
        $this->db->or_where('status =','vital_signs');
        $this->db->or_where('status =','waiting');
        $this->db->or_where('status =','in_consultation');       
        $this->db->group_end();

        $checkAppointment = $this->db->get()->result();

        if(count($checkAppointment) > 0) {
            // Appointment exist
            $this->response(array('code' => '201', 'message' => 'Appointment already exists', 'result' => NULL, 'requestname' => $method));
        }else{
            // Get review days & review times for this doctor id
            $get_review_info = $this->db->select('review_days, review_times')->from('clinic_doctor')->where("clinic_id =", $clinic_id)->where('doctor_id =',$doctor_id)->get()->row();

            $review_days = $get_review_info->review_days;
            $review_times = $get_review_info->review_times;

            $today = date('Y-m-d');

            // Get the date in which the review days would fall
            $max_possible_review_date = date('Y-m-d', strtotime('-'.$review_days.' days', strtotime($today)));

            // Check when was the last appointment with this doctor
            $this->db->select("appointment_id, appointment_date, appointment_time_slot, parent_appointment_id, review_no, status");
            $this->db->from("appointments");
            $this->db->where("patient_id =", $patient_id);
            $this->db->where("clinic_id =", $clinic_id);
            $this->db->where("doctor_id =", $doctor_id);
            $this->db->where("appointment_date >",$max_possible_review_date);
            $this->db->where("parent_appointment_id =",0);
            $this->db->where("status =",'closed');
            $this->db->order_by("appointment_id DESC");
            $this->db->limit("1","0");

            $checkLastAppointment = $this->db->get()->row();

            if(count($checkLastAppointment) > 0) {
                // Appointment will fall in review days
                // Check for the last appointment record which is holding this appointment id as its parent appointment id
                $checkChildAppointment = $this->db->select("appointment_id, review_no, parent_appointment_id")->from("appointments")->where("parent_appointment_id =",$checkLastAppointment->appointment_id)->order_by("appointment_id DESC")->limit("1","0")->get()->row();

                if(count($checkChildAppointment) > 0){

                    // Check its review no with review times
                    if($checkChildAppointment->review_no < $review_times){
                        $data['parent_appointment_id'] = $checkChildAppointment->parent_appointment_id;    
                        $data['review_no'] = ++$checkChildAppointment->review_no;    
                        $data['payment_status'] = 2; // Free
                        $payment = 0;
                    }else{
                        // Appointment doesn't fall into review days as no. of review times has reached
                        // Need to Collect the payment
                        $data['review_no'] = 0;
                        $data['payment_status'] = 0; // Collect Payment
                        $payment = 1;
                    }                
                }else{
                    $data['parent_appointment_id'] = $checkLastAppointment->appointment_id;    
                    $data['review_no'] = 1;
                    $data['payment_status'] = 2; // That means the appointment is free
                    $payment = 0;
                }
            }else{
                $data['parent_appointment_id'] = 0;    
                $data['review_no'] = 0;
                $data['payment_status'] = 0; // That means collect payment
                $payment = 1;
            }

            $data['clinic_id'] = $clinic_id;
            $data['patient_id'] = $patient_id;
            $data['appointment_type'] = $appointment_type;
            $data['umr_no'] = $umr_no;
            $data['booking_type'] = $booking_type;
            $data['doctor_id'] = $doctor_id;
            $data['appointment_date'] = $appointment_date;
            $data['appointment_time_slot'] = $appointment_time_slot;
            $data['created_by'] = $user_id;
            $data['created_date_time'] = date('Y-m-d H:i:s');
            $data['modified_by'] = $user_id;
            $data['modified_date_time'] = date('Y-m-d H:i:s');
            $data['status'] = "in_consultation";
            $data['priority'] = $priority;

            // Capture the last inserted Appointment ID
            $appointment_id = $this->Generic_model->insertDataReturnId("appointments", $data);

            // Get params to respond
            $param['appointment']['appointment_id'] = $appointment_id;
            $param['appointment']['clinic_id'] = $clinic_id;
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
            $param['appointment']['status'] = "in_consultation";
            $param['appointment']['pdf_file'] = null;

            $param['appointment']['reg_payment_status'] = (int)$patient_info->payment_status;
            
            if($payment == 1){
                $param['appointment']['con_payment_status'] = 0;
            }else if($payment == 0){
                $param['appointment']['con_payment_status'] = 1;    
            }

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


    public function save_billing($parameters, $method, $user_id) {
 

        extract($parameters);

        // Generate Invoice and Receipt no
        $invoice_no_alias = generate_invoice_no($clinic_id);
        $invoice_no = $clinic_id.$invoice_no_alias;     

        $billing_master = $parameters;
        unset($billing_master['billing_line_items']);
        unset($billing_master['con_payment_status']);
        unset($billing_master['reg_payment_status']);
        unset($billing_master['priority']);


        // Get patient Info
        $this->db->select('patient_id, umr_no, first_name, last_name, mobile, alternate_mobile');
        $this->db->from('patients');
        $this->db->where('patient_id =', $patient_id);
        $patientInfo = $this->db->get()->row();

        $billing_master['invoice_no'] = $invoice_no;
        $billing_master['invoice_no_alias'] = $invoice_no_alias;
        $billing_master['guest_name'] = ucwords($patientInfo->$first_name." ".$patientInfo->$last_name);
        $billing_master['guest_mobile'] = ($patientInfo->mobile != '' ? $patientInfo->mobile : $patientInfo->alternate_mobile);
        $billing_master['billing_date_time'] = date('Y-m-d H:i:s');
        $billing_master['created_by'] = $user_id;
        $billing_master['created_date_time'] = date('Y-m-d H:i:s');
        $billing_master['modified_by'] = $user_id;
        $billing_master['modified_date_time'] = date('Y-m-d H:i:s');
        $billing_master['payment_status'] = $con_payment_status;

        // Insert billing master informaiton and get Billing Id
        $billing_id = $this->Generic_model->insertDataReturnId('billing', $billing_master);

        // Create Billing line items
        $billing_line_items = $parameters['billing_line_items'];

        $invoiceAmount = 0;

        for($i=0; $i<count($billing_line_items); $i++){
            $billing_line_items[$i]['billing_id'] = $billing_id;
            $billing_line_items[$i]['quantity'] = 1;
            $billing_line_items[$i]['unit_price'] = $billing_line_items[$i]['amount'];
            $billing_line_items[$i]['created_by'] = $user_id;
            $billing_line_items[$i]['created_date_time'] = date('Y-m-d H:i:s');
            $billing_line_items[$i]['modified_by'] = $user_id;
            $billing_line_items[$i]['modified_date_time'] = date('Y-m-d H:i:s'); 

            $amount = $billing_line_items[$i]['amount'];
            $discount = $billing_line_items[$i]['discount'];
            $unit = $billing_line_items[$i]['discount_unit'];

            if($discount == 0){
                $invoiceAmount = $invoiceAmount + $amount;
            }else{
                if($unit == "%"){
                    $amount = $amount - ($amount * $discount/100); 
                }else if($unit == "INR"){
                    $amount = $amount - $discount;
                }
                $invoiceAmount = $invoiceAmount + $amount;
            }

            $invoiceData['total_amount'] = $invoiceAmount;    
            $patientData['payment_status'] = $reg_payment_status;

            $this->Generic_model->insertData('billing_line_items', $billing_line_items[$i]);

            // Update calculated total invoice amount in the billing db for billing id
            $this->Generic_model->updateData('billing', $invoiceData, array('billing_id' => $billing_id));

            // Update patient table with payment status
            $this->Generic_model->updateData('patients', $patientData, array('patient_id' => $patient_id));

        }

        // $clinic_details = $this->Generic_model->getSingleRecord('clinics', array('clinic_id' => $parameters['clinic_id']), $order = '');
        // $doctor_details = $this->Generic_model->getSingleRecord('doctors', array('doctor_id' => $parameters['doctor_id']), $order = '');
        // $departments = $this->Generic_model->getSingleRecord('department', array('department_id' => $doctor_details->department_id), $order = '');
        // $billing = $this->Generic_model->getAllRecords('billing_line_items', array('billing_id' => $billing_id), $order = '');
        // $patient_details = $this->Generic_model->getSingleRecord('patients', array('patient_id' => $patient_id), $order = '');
        // $district_details = $this->Generic_model->getSingleRecord('districts', array('district_id' => $patient_details->district_id), $order = '');
        // $review_details = $this->Generic_model->getSingleRecord('clinic_doctor',array('doctor_id'=>$$doctor_details->doctor_id,'clinic_id'=>$clinic_details->clinic_id),$order='');
        //$state_details = $this->Generic_model->getSingleRecord('states', array('state_id' => $patient_details_state_id), $order = '');


        $info = $this->db->select('*')->from('appointments A')->join('doctors Doc','A.doctor_id = Doc.doctor_id')->where('A.appointment_id =',$appointement_id)->get()->row();

        $clinic_details = $this->Generic_model->getSingleRecord('clinics',array('clinic_id'=>$clinic_id),$order='');

        $doctor_details = $this->Generic_model->getSingleRecord('doctors',array('doctor_id'=>$doctor_id),$order='');
        $review_details = $this->Generic_model->getSingleRecord('clinic_doctor',array('doctor_id'=>$doctor_id,'clinic_id'=>$clinic_id),$order='');

        $departments = $this->Generic_model->getSingleRecord('department',array('department_id'=>$doctor_details->department_id),$order='');
        $billing_master = $this->Generic_model->getSingleRecord('billing',array('billing_id'=>$billing_id),$order='');
        $billing = $this->Generic_model->getAllRecords('billing_line_items',array('billing_id'=>$billing_id),$order='');
        $patient_details = $this->Generic_model->getSingleRecord('patients',array('patient_id'=>$patient_id),$order='');     

        $district_details = $this->Generic_model->getSingleRecord('districts',array('district_id'=>$patient_details->district_id),$order='');

        $state_details = $this->Generic_model->getSingleRecord('states',array('state_id'=>$patient_details->state_id),$order='');

        $data['clinic_logo'] = $clinic_details->clinic_logo;
        $data['review_days']=$review_details->review_days;
        $data['clinic_phone'] = $clinic_details->clinic_phone;
        $data['clinic_name'] = $clinic_details->clinic_name;
        $data['address'] = $clinic_details->address;
        $data['doctor_name'] = "Dr." . strtoupper($doctor_details->first_name . " " . $doctor_details->last_name);
        $data['qualification'] = $doctor_details->qualification;
        $data['department_name'] = $departments->departmentname;
        $data['patient_name'] = ucfirst($patient_details->title) . "." . strtoupper($patient_details->first_name . " " . $patient_details->last_name);
        $data['age'] = $patient_details->age . ' ' . $patient_details->age_unit;
        $data['age_unit']=$patient_details->age_unit;
        $data['gender'] = $patient_details->gender;
        $data['umr_no'] = $umr_no;
        $data['patient_address'] = $patient_details->address_line . "," . $district_details->district_name . "," . $state_details->state_name . "," . $patient_details->pincode;
        $data['billing'] = $billing;
        $data['invoice_no'] = $invoice_no;
        $data['invoice_no_alias'] = $invoice_no_alias;

        $html = $this->load->view('billing/generate_billing', $data, true);
        $pdfFilePath = "billing_" . $patient_id . $billing_id . ".pdf";
        $data['file_name'] = $pdfFilePath;

        $this->load->library('M_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output("./uploads/billings/" . $pdfFilePath, "F");
        $billFile['invoice_pdf'] = $data['file_name'];
        $this->Generic_model->updateData('billing', $billFile, array('billing_id' => $billing_id));
        $pdf = base_url() . 'uploads/billings/' . $pdfFilePath;
        $param['appointment']['pdf_file'] = $pdf;

        $doctors_info = $this->Generic_model->getSingleRecord('doctors',array('doctor_id'=>$doctor_id),'');

        $dept_info = $this->Generic_model->getSingleRecord('department',array('department_id'=>$doctors_info->department_id),'');

        $param['appointment']['appointment_id'] = $appointment_id;
        $param['appointment']['clinic_id'] = $clinic_id;
        $param['appointment']['patient_id'] = $patient_id;
        $param['appointment']['umr_no'] = $umr_no;
        $param['appointment']['doctor_id'] = $doctor_id;
        $param['appointment']['doctor_name'] = "Dr. " . strtoupper($doctors_info->first_name . " " . $doctors_info->last_name);
        $param['appointment']['department'] = $dept_info->department_name;
        $param['appointment']['department_id'] = $doctors_info->department_id;
        $param['appointment']['appointment_type'] = $appointmentInfo->appointment_type;
        $param['appointment']['appointment_date'] = $appointmentInfo->$appointment_date;
        $param['appointment']['appointment_time_slot'] = $appointmentInfo->appointment_time_slot;
        $param['appointment']['priority'] = $appointmentInfo->priority;
        $param['appointment']['status'] = $appointmentInfo->status;

        $this->response(array('code' => '200', 'message' => 'Appointment Booked', 'result' => $param, 'requestname' => $method));

    }


    // Reschedule appointment to another date
    public function reschedule_appointment($parameters, $method, $user_id) {

        $clinic_id = $parameters['clinic_id'];
        $doctor_id = $parameters['doctor_id'];
        $patient_id = $parameters['patient_id'];
        $umr_no = $parameters['umr_no'];
        $priority = $parameters['priority'];
        if ($priority != '' || $priority != NULL) {
            $pr = $priority;
        } else {
            $pr = "none";
        }
        $appointment_date = $parameters['appointment_date'];
        $appointment_id = $parameters['appointment_id'];
        $appointment_time_slot = $parameters['appointment_time_slot'];

        $update['status'] = $parameters['status'];
        $this->Generic_model->updateData('appointments', $update, array('appointment_id' => $appointment_id));

        $appointment['parent_appointment_id'] = $appointment_id;
        $appointment['clinic_id'] = $clinic_id;
        $appointment['patient_id'] = $patient_id;
        $appointment['umr_no'] = $umr_no;
        $appointment['doctor_id'] = $doctor_id;
        $appointment['appointment_type'] = "New";
        $appointment['appointment_date'] = $appointment_date;
        $appointment['appointment_time_slot'] = $appointment_time_slot;
        $appointment['priority'] = $pr;
        $appointment['status'] = "booked";
        $appointment['created_by'] = $user_id;
        $appointment['modified_by'] = $user_id;
        $appointment['created_date_time'] = date('Y-m-d H:i:s');
        $appointment['modified_date_time'] = date('Y-m-d H:i:s');
        $param['appointment']['pdf_file'] = NULL;
        $new_appointment_id = $this->Generic_model->insertDataReturnId("appointments", $appointment);

        $doctors_info = $this->Generic_model->getSingleRecord('doctors',array('doctor_id'=>$doctor_id),'');
        $dept_info = $this->Generic_model->getSingleRecord('department',array('department_id'=>$doctors_info->department_id),'');
        $param['appointment']['appointment_id'] = $new_appointment_id;
        $param['appointment']['clinic_id'] = $clinic_id;
        $param['appointment']['patient_id'] = $patient_id;
        $param['appointment']['umr_no'] = $umr_no;
        $param['appointment']['doctor_id'] = $doctor_id;
        $param['appointment']['doctor_name'] = "Dr. " . strtoupper($doctors_info->first_name . " " . $doctors_info->last_name);
        $param['appointment']['department'] = $dept_info->department_name;
        $param['appointment']['department_id'] = $doctors_info->department_id;
        $param['appointment']['appointment_type'] = "New";
        $param['appointment']['appointment_date'] = $appointment_date;
        $param['appointment']['appointment_time_slot'] = $parameters['appointment_time_slot'];
        $param['appointment']['priority'] = $appointment['priority'];
        $param['appointment']['status'] = "booked";
        $this->response(array('code' => '200', 'message' => 'Appointment Rescheduled', 'result' => $param, 'requestname' => $method));
    }

    //adding review date or next followup date for current appointment
    public function appointment_followup($parameters, $method, $user_id) {

        $parent_appointment = $this->db->select("*")->from("appointments")->where("appointment_id='" . $parameters['appointment_id'] . "'")->order_by("appointment_id","asc")->get()->row();


        $next_appointment = $date = date('Y-m-d', strtotime('+' . $parameters['days'] . ' day', strtotime($parent_appointment->appointment_date)));
        $priority = $parameters['priority'];
        if ($parent_appointment->priority != '' || $parent_appointment->priority != NULL) {
            $pr = $parent_appointment->priority;
        } else {
            $pr = "none";
        }
        $appointment['parent_appointment_id'] = $parameters['appointment_id'];
        $appointment['clinic_id'] = $parent_appointment->clinic_id;
        $appointment['patient_id'] = $parent_appointment->patient_id;
        $appointment['umr_no'] = $parent_appointment->umr_no;
        $appointment['doctor_id'] = $parent_appointment->doctor_id;
        $appointment['appointment_type'] = "Follow-up";
        $appointment['appointment_date'] = $next_appointment;
        $appointment['appointment_time_slot'] = $parent_appointment->appointment_time_slot;
        $appointment['priority'] = $pr;
        $appointment['status'] = "booked";
        $appointment['created_by'] = $user_id;
        $appointment['modified_by'] = $user_id;
        $appointment['created_date_time'] = date('Y-m-d H:i:s');
        $appointment['modified_date_time'] = date('Y-m-d H:i:s');
        $param['appointment']['next_followup_date'] = $next_appointment;
        $param['appointment']['time_slot'] = $parent_appointment->appointment_time_slot;
        $appointment_id = $this->Generic_model->insertDataReturnId("appointments", $appointment);

        $this->response(array('code' => '200', 'message' => 'Appointment Booked', 'result' => $param, 'requestname' => $method));
    }

    //list of master followup 
    public function followup_list($parameters, $method, $user_id) {

        $parent_appointment = $this->db->select("*")->from("appointments")->where("parent_appointment_id='" . $parameters['appointment_id'] . "'")->order_by("appointment_id","asc")->get()->row();

        if (count($parent_appointment) > 0) {

            $data['appointment']['appointment_id'] = $parent_appointment->appointment_id;
            $data['appointment']['next_followup_date'] = $parent_appointment->appointment_date;
            $data['appointment']['time_slot'] = $parent_appointment->appointment_time_slot;
            $data['appointment']['parent_appointment_id'] = $parent_appointment->parent_appointment_id;
        } else {
            $data['appointment'] = NULL;
        }
        $this->response(array('code' => '200', 'message' => 'Followup List', 'result' => $data, 'requestname' => $method));
    }


    //updating appointment status
    public function appointment_status($parameters, $method, $user_id) {
        $status_type = $parameters['status_type'];
        $appointment_id = $parameters['appointment_id'];
        $condition = array('appointment_id' => $appointment_id);
        if ($status_type == 'checked_in') {
            $appointment['status'] = 'checked_in';
            $appointment['check_in_time'] = date('Y-m-d H:i:s');
            $appointment['modified_by'] = $user_id;
            $appointment['modified_date_time'] = date('Y-m-d H:i:s');
            $this->Generic_model->updateData("appointments", $appointment, $condition);

            $ap_details = $this->Generic_model->getSingleRecord('appointments', array('appointment_id' => $appointment_id), '');

            $this->Generic_model->pushNotifications($ap_details->patient_id, $appointment_id, $ap_details->doctor_id, $ap_details->clinic_id, 'check_in', 'dashboard');


            $param['appointment']['appointment_id'] = $appointment_id;
            $param['appointment']['status'] = $appointment['status'];
        } else if ($status_type == 'vital_signs') {
            $appointment['status'] = 'vital_signs';
            $appointment['modified_by'] = $user_id;
            $appointment['modified_date_time'] = date('Y-m-d H:i:s');
            $this->Generic_model->updateData("appointments", $appointment, $condition);

            $param['appointment']['appointment_id'] = $appointment_id;
            $param['appointment']['status'] = $appointment['status'];
        } else if ($status_type == 'drop') {
            $appointment['status'] = 'drop';
            $appointment['modified_by'] = $user_id;
            $appointment['modified_date_time'] = date('Y-m-d H:i:s');
            $this->Generic_model->updateData("appointments", $appointment, $condition);

            $param['appointment']['appointment_id'] = $appointment_id;
            $param['appointment']['status'] = $appointment['Dropped'];
        } else if ($status_type == 'waiting') {

            $appointment['status'] = 'waiting';
            $appointment['modified_by'] = $user_id;
            $appointment['modified_date_time'] = date('Y-m-d H:i:s');
            $this->Generic_model->updateData("appointments", $appointment, $condition);

            $ap_details = $this->Generic_model->getSingleRecord('appointments', array('appointment_id' => $appointment_id), '');
            $this->Generic_model->pushNotifications($ap_details->patient_id, $appointment_id, $ap_details->doctor_id, $ap_details->clinic_id, 'push_to_consultant', 'dashboard');

            $param['appointment']['appointment_id'] = $appointment_id;
            $param['appointment']['status'] = $appointment['status'];
        } else if ($status_type == 'closed') {
            $appointment['status'] = 'closed';
            $appointment['modified_by'] = $user_id;
            $appointment['modified_date_time'] = date('Y-m-d H:i:s');
            $this->Generic_model->updateData("appointments", $appointment, $condition);

            $param['appointment']['appointment_id'] = $appointment_id;
            $param['appointment']['status'] = $appointment['status'];
        } else if ($status_type == 'in_consultation') {
            $appointment['status'] = 'in_consultation';
            $appointment['modified_by'] = $user_id;
            $appointment['modified_date_time'] = date('Y-m-d H:i:s');
            $this->Generic_model->updateData("appointments", $appointment, $condition);



            $param['appointment']['appointment_id'] = $appointment_id;
            $param['appointment']['status'] = $appointment['status'];
        } else {
            $param['appointment']['appointment_id'] = $appointment_id;
            $param['appointment']['status'] = 'Failed';
        }

        $this->response(array('code' => '200', 'message' => 'Patient Status', 'result' => $param, 'requestname' => $method));
    }

    //saving clinical diagnosys list of patients with respect to appointmet
    public function clinical_diagnosis_submit($parameters, $method, $user_id) {
        $clinic_id = $parameters['clinic_id'];
        $doctor_id = $parameters['doctor_id'];
        $patient_id = $parameters['patient_id'];
        $umr_no = $parameters['umr_no'];
        $appointment_id = $parameters['appointment_id'];
        $clinicaldiagnosis = $parameters['clinicaldiagnosis'];
        if (count($clinicaldiagnosis) > 0) {
            $check_pcd_exist=$this->db->select("*")->from("patient_clinical_diagnosis")->where("patient_id='".$patient_id."' and appointment_id='".$appointment_id."' and clinic_id='".$clinic_id."' and doctor_id='".$doctor_id."'")->get()->row();
            if ($check_pcd_exist > 0) {
                $this->db->query("DELETE  FROM patient_cd_line_items WHERE patient_clinical_diagnosis_id='".$check_pcd_exist->patient_clinical_diagnosis_id."' ");
                for($cd=0;$cd<count($clinicaldiagnosis);$cd++)
                {
                    $pcdl['patient_clinical_diagnosis_id'] = $check_pcd_exist->patient_clinical_diagnosis_id;
                    $pcdl['disease_name'] = htmlentities($clinicaldiagnosis[$cd]['description']);
                    $pcdl['created_by'] = $user_id;
                    $pcdl['modified_by'] = $user_id;
                    $pcdl['created_date_time'] = date('Y-m-d H:i:s');
                    $pcdl['modified_date_time'] = date('Y-m-d H:i:s');
                    $this->Generic_model->insertData('patient_cd_line_items',$pcdl);
                }
            }else{
                $pcd['clinic_id'] = $clinic_id;
                $pcd['doctor_id'] = $doctor_id;
                $pcd['patient_id'] = $patient_id;
                $pcd['umr_no'] = $umr_no;
                $pcd['appointment_id'] = $appointment_id;
                $pcd['created_by'] = $user_id;
                $pcd['modified_by'] = $user_id;
                $pcd['created_date_time'] = date('Y-m-d H:i:s');
                $pcd['modified_date_time'] = date('Y-m-d H:i:s');
                $pcd_id = $this->Generic_model->insertDataReturnId('patient_clinical_diagnosis', $pcd);
                for($cd=0;$cd<count($clinicaldiagnosis);$cd++)
                {
                    $pcdl['patient_clinical_diagnosis_id'] = $pcd_id;
                    $pcdl['disease_name'] = htmlentities($clinicaldiagnosis[$cd]['description']);
                    $pcdl['created_by'] = $user_id;
                    $pcdl['modified_by'] = $user_id;
                    $pcdl['created_date_time'] = date('Y-m-d H:i:s');
                    $pcdl['modified_date_time'] = date('Y-m-d H:i:s');
                    $this->Generic_model->insertData('patient_cd_line_items',$pcdl);
                }
            }

            $param['appointment_id'] = $appointment_id;

            $this->response(array('code' => '200', 'message' => 'Patient Clinical Diagnosis Done', 'result' => $param, 'requestname' => $method));
        } else {
            $param['appointment_id'] = $appointment_id;
            $this->response(array('code' => '200', 'message' => 'Patient Clinical Diagnosis Not Done', 'result' => $param, 'requestname' => $method));
        }
    }


    // Save/Delete Clinical Diagnosis of the patient
    public function patient_clinical_diagnosis($parameters, $method, $user_id) {

        $type = $parameters['clinicaldiagnosis'][0]['type'];
        $patient_clinical_diagnosis_id = "";

        // Get patient clinical diagnosis id with appointment id
        $this->db->select('patient_clinical_diagnosis_id');
        $this->db->from('patient_clinical_diagnosis');
        $this->db->where('appointment_id =',$parameters['appointment_id']);
        $pcdRec = $this->db->get()->row();

        // Count of an array
        $count = count($pcdRec);

        if($count > 0){
            $patient_clinical_diagnosis_id = $pcdRec->patient_clinical_diagnosis_id;
        }else{
            $patient_clinical_diagnosis_id = 0;
        }

        // unset type
        unset($parameters['position']);

        if($type == 'del'){

            // Delete the line item
            $res = $this->Generic_model->deleteRecord('patient_cd_line_items',array('patient_cd_line_item_id'=>$parameters['clinicaldiagnosis'][0]['patient_cd_line_item_id']));

            // Check number of line items with master record id
            $count = $this->Generic_model->getNumberOfRecords('patient_cd_line_items',array('patient_clinical_diagnosis_id'=>$patient_clinical_diagnosis_id));

            if($count == 0){
                // Delete master record as well
                $res = $this->Generic_model->deleteRecord('patient_clinical_diagnosis',array('patient_clinical_diagnosis_id'=>$patient_clinical_diagnosis_id));
                if($res){
                    $param['patient_clinical_diagnosis_id'] = 0;        
                }
            }    
            
            $this->response(array('code' => '200', 'message' => 'Record Deleted Successfully', 'result' => null, 'requestname' => $method));

        }else{

            if($patient_clinical_diagnosis_id == 0) {
                // Create Patient Clinical Dianosis Master Record
                $masterRec = $parameters;
                $masterRec['created_by'] = $user_id;
                $masterRec['modified_by'] = $user_id;
                $masterRec['created_date_time'] = date('Y-m-d H:i:s');
                $masterRec['modified_date_time'] = date('Y-m-d H:i:s');

                unset($masterRec['clinicaldiagnosis']);

                // Create master record and get Patient Clinical Diagnosis Id
                $patient_clinical_diagnosis_id = $this->Generic_model->insertDataReturnId('patient_clinical_diagnosis',$masterRec);
            }

            // If Patient Clinical Diagnosis Line item Object exist, unset it
            if(isset($parameters['clinicaldiagnosis'][0]['patient_cd_line_item_id'])){
                unset($parameters['clinicaldiagnosis'][0]['patient_cd_line_item_id']);
            }

            unset($parameters['clinicaldiagnosis'][0]['isEnabled']);

            $cdLineItems = $parameters['clinicaldiagnosis'][0];

            // Add master id to the line items array
            $cdLineItems['patient_clinical_diagnosis_id'] = $patient_clinical_diagnosis_id;
            $cdLineItems['created_date_time'] = date('Y-m-d H:i:s');
            $cdLineItems['modified_date_time'] = date('Y-m-d H:i:s');
            $cdLineItems['created_by'] = $user_id;
            $cdLineItems['modified_by'] = $user_id;

            // Create clinical diagnosis line items and get the line item id
            $parameters['clinicaldiagnosis'][0]['patient_cd_line_item_id'] = $this->Generic_model->insertDataReturnId('patient_cd_line_items',$cdLineItems);

            $this->response(array('code' => '200', 'message' => 'Patient Clinical Diagnosis Created Successfully', 'result' => $parameters, 'requestname' => $method));
        }
    }


    // Saving patient investigations with respect to the appointment
    public function investigations_submit($parameters, $method, $user_id) {

        $investigation = $parameters['investigations_list'];
        $type = $investigation[0]['type'];

        if (count($investigation) > 0) {

            // Check for patient investogation master record
            $check_inv_exist = $this->db->select("*")->from("patient_investigation")->where("patient_id='".$parameters['patient_id']."' and appointment_id='".$parameters['appointment_id']."' and clinic_id='".$parameters['clinic_id']."' and doctor_id='".$parameters['doctor_id']."'")->get()->row();

            // Deleting investigation line item
            if($type == "del"){
                // Delete line item
                $this->Generic_model->deleteRecord('patient_investigation_line_items',array('patient_investigation_line_item_id'=>$investigation[0]['patient_investigation_line_item_id']));

                // Check number of line items with master record id
                $count = $this->Generic_model->getNumberOfRecords('patient_investigation_line_items',array('patient_investigation_id'=>$check_inv_exist->patient_investigation_id));

                if($count == 0){
                    // Delete master record as well
                    $res = $this->Generic_model->deleteRecord('patient_investigation',array('patient_investigation_id'=>$check_inv_exist->patient_investigation_id));
                }    
                
                $this->response(array('code' => '200', 'message' => 'Record Deleted Successfully', 'result' => null, 'requestname' => $method));
            }

            if(count($check_inv_exist) > 0) {
                
                // Insert line items for the master patient investigation record
                for ($in = 0; $in < count($investigation); $in++) {
                    $inv['patient_investigation_id'] = $check_inv_exist->patient_investigation_id;
                    $inv['investigation_id'] = $investigation[$in]['investigation_id'];
                    $inv['investigation_name'] = $investigation[$in]['investigation_name'];
                    $inv['status'] = 1;
                    $inv['created_by'] = $user_id;
                    $inv['modified_by'] = $user_id;
                    $inv['created_date_time'] = date('Y-m-d H:i:s');
                    $inv['modified_date_time'] = date('Y-m-d H:i:s');

                    $patient_investigation_line_item_id = $this->Generic_model->insertDataReturnId('patient_investigation_line_items', $inv);
                }

            }else{

                $data['clinic_id'] = $parameters['clinic_id'];
                $data['doctor_id'] = $parameters['doctor_id'];
                $data['patient_id'] = $parameters['patient_id'];
                $data['umr_no'] = $parameters['umr_no'];
                $data['appointment_id'] = $parameters['appointment_id'];
                $data['status'] = 1;
                $data['created_by'] = $user_id;
                $data['modified_by'] = $user_id;
                $data['created_date_time'] = date('Y-m-d H:i:s');
                $data['modified_date_time'] = date('Y-m-d H:i:s');

                // Inserting new master record for patient investigation and getting its ID back
                $patient_investigation_id = $this->Generic_model->insertDataReturnId('patient_investigation', $data);

                $parameters['count of investigations'] = count($investigation);

                for ($in = 0; $in < count($investigation); $in++) {
                    $inv['patient_investigation_id'] = $patient_investigation_id;
                    $inv['investigation_id'] = $investigation[$in]['investigation_id'];
                    $inv['investigation_name'] = $investigation[$in]['investigation_name'];
                    $inv['status'] = 1;
                    $inv['created_by'] = $user_id;
                    $inv['modified_by'] = $user_id;
                    $inv['created_date_time'] = date('Y-m-d H:i:s');
                    $inv['modified_date_time'] = date('Y-m-d H:i:s');

                    // inserting patient investigation line item record
                    $patient_investigation_line_item_id = $this->Generic_model->insertDataReturnId('patient_investigation_line_items', $inv);
                }
                // $this->response(array('code' => '200', 'message' => 'Patient Investigations Saved Successfully', 'result' => null, 'requestname' => $method));
            }

            // Add line item id just inserted to the parameters
            $parameters['investigations_list'][0]['patient_investigation_line_item_id'] = $patient_investigation_line_item_id;

            // Get appointment details 
            $ap_details = $this->Generic_model->getSingleRecord('appointments', array('appointment_id' => $parameters['appointment_id']), '');

            // Push notification to the dashboard of the lab assistant
            $this->Generic_model->pushNotifications($ap_details->patient_id, $appointment_id, $ap_details->doctor_id, $ap_details->clinic_id, 'investigation', 'dashboard');

            $this->response(array('code' => '200', 'message' => 'Patient Investigations Saved Successfully', 'result' => $parameters, 'requestname' => $method));
        } else {
            $this->response(array('code' => '200', 'message' => 'Failed Saving Patient Investigations', 'result' => $parameters, 'requestname' => $method));
        }
    }


    // Saving symtoms w.r.t to appoiintment
    public function presenting_symptoms_submit($parameters, $method, $user_id) {
        $clinic_id = $parameters['clinic_id'];
        $doctor_id = $parameters['doctor_id'];
        $patient_id = $parameters['patient_id'];
        $umr_no = $parameters['umr_no'];
        $appointment_id = $parameters['appointment_id'];
        $form_id = $parameters['form_id'];

        $symptoms = $parameters['symptoms'];
        if (count($symptoms) > 0) {
            $presenting_symptoms = $this->db->select("*")->from("patient_presenting_symptoms ps")->join(" patient_ps_line_items psl","ps.patient_presenting_symptoms_id = psl.patient_presenting_symptoms_id")->where("patient_id= '" . $parameters['patient_id'] . "' and appointment_id= '" . $parameters['appointment_id'] . "' and doctor_id= '" . $parameters['doctor_id'] . "' and clinic_id= '" . $parameters['clinic_id'] . "'")->get()->row();
            if (count($presenting_symptoms) > 0) {
                $patient_presenting_symptoms_id = $presenting_symptoms->patient_presenting_symptoms_id;
            } else {

                $ps['clinic_id'] = $clinic_id;
                $ps['doctor_id'] = $doctor_id;
                $ps['patient_id'] = $patient_id;
                $ps['umr_no'] = $umr_no;
                $ps['appointment_id'] = $appointment_id;
                $ps['created_by'] = $user_id;
                $ps['modified_by'] = $user_id;
                $ps['created_date_time'] = date('Y-m-d H:i:s');
                $ps['modified_date_time'] = date('Y-m-d H:i:s');
                $patient_presenting_symptoms_id = $this->Generic_model->insertDataReturnId('patient_presenting_symptoms', $ps);
            }

            if ($patient_presenting_symptoms_id != '') {
                for ($s = 0; $s < count($symptoms); $s++) {
                   if($symptoms[$s]['form_id'] == 0){
                       $get_generic_form = $this->db->select("form_id")->from("form")->where("form_name='Generic' and form_type='HOPI'")->get()->row();
                       $form_id = $get_generic_form->form_id;
                   }
                   else{
                       $form_id = $symptoms[$s]['form_id'];
                   }
                   $psl['patient_presenting_symptoms_id'] = $patient_presenting_symptoms_id;
                   $psl['symptom_data'] = $symptoms[$s]['symptom_data'];
                   $psl['time_span'] = $symptoms[$s]['time_span'];
                   $psl['form_id'] = $form_id;
                   $psl['span_type'] = $symptoms[$s]['span_type'];
                   $psl['created_by'] = $user_id;
                   $psl['modified_by'] = $user_id;
                   $psl['created_date_time'] = date('Y-m-d H:i:s');
                   $psl['modified_date_time'] = date('Y-m-d H:i:s');
                   $pps_line_item_id =  $this->Generic_model->insertDataReturnId('patient_ps_line_items', $psl);
               }
           }

           $param['appointment_id'] = $appointment_id;
           $param['pps_line_item_id'] = $pps_line_item_id;

           $this->response(array('code' => '200', 'message' => 'Presenting Symptoms Done', 'result' => $param, 'requestname' => $method));
       } else {
        $param['appointment_id'] = $appointment_id;
        $this->response(array('code' => '200', 'message' => 'Presenting Symptoms Not Done', 'result' => $param, 'requestname' => $method));
    }
}

    // Saving drug informationm to the patient prescription added by the doctor
public function prescription_submit($parameters, $method, $user_id) {

        // check for existing prescription 
    $check_exist = $this->db->select("*")->from("patient_prescription")->where("appointment_id ='" . $parameters['appointment_id'] . "'")->get()->row();

    $prescription_drug = $parameters['prescription'];
    $type = $prescription_drug[0]['type'];

        // Deleting drug line item
    if($type == "del"){
            // Delete line item
        $res = $this->Generic_model->deleteRecord('patient_prescription_drug',array('patient_prescription_drug_id'=>$prescription_drug[0]['patient_prescription_drug_id']));

            // echo $this->db->last_query();
            // exit();

        if($res){
                // Check number of line items with master record id
            $count = $this->Generic_model->getNumberOfRecords('patient_prescription_drug',array('patient_prescription_id'=>$check_exist->patient_prescription_id));

            if($count == 0){
                    // Delete master record as well
                $res = $this->Generic_model->deleteRecord('patient_prescription',array('patient_prescription_id'=>$check_exist->patient_prescription_id));
            }

            $this->response(array('code' => '200', 'message' => 'Record Deleted Successfully', 'result' => null, 'requestname' => $method));    
        }else{
            $this->response(array('code' => '201', 'message' => 'Record Delete Failed', 'result' => null, 'requestname' => $method));
        }
    }else if($type == ''){
        if ($check_exist > 0) {

                //$patient_prescription_update['plan'] = $parameters['plan'];
                //$patient_prescription_update['general_instructions'] = $parameters['instructions'];

                //$this->Generic_model->updateData('patient_prescription',$patient_prescription_update,array('patient_prescription_id'=>$check_exist->patient_prescription_id));

                //$this->db->query("DELETE FROM patient_prescription_drug WHERE patient_prescription_id = '" . $check_exist->patient_prescription_id . "'");  

            if (count($prescription_drug) > 0) {
                for ($i = 0; $i < count($prescription_drug); $i++) {
                    $prescription_drug_insert[$i]['patient_prescription_id'] = $check_exist->patient_prescription_id;
                    $prescription_drug_insert[$i]['drug_id'] = $prescription_drug[$i]['drug_id'];
                    $prescription_drug_insert[$i]['medicine_name'] = $prescription_drug[$i]['medicine_name'];
                    $prescription_drug_insert[$i]['day_schedule'] = $prescription_drug[$i]['day_schedule'];
                    $prescription_drug_insert[$i]['day_dosage'] = $prescription_drug[$i]['day_dosage'];
                    $prescription_drug_insert[$i]['dosage_frequency'] = $prescription_drug[$i]['dosage_frequency'];
                    $prescription_drug_insert[$i]['preffered_intake'] = $prescription_drug[$i]['preffered_intake'];
                    $prescription_drug_insert[$i]['preffered_time_gap'] = $prescription_drug[$i]['preffered_time_gap'];
                    $prescription_drug_insert[$i]['dose_course'] = $prescription_drug[$i]['dose_course'];
                    $prescription_drug_insert[$i]['quantity'] = $prescription_drug[$i]['quantity'];
                    $prescription_drug_insert[$i]['status'] = 1;
                    $prescription_drug_insert[$i]['drug_status'] = $prescription_drug[$i]['drug_status'];
                    $prescription_drug_insert[$i]['mode'] = $prescription_drug[$i]['mode'];
                    $prescription_drug_insert[$i]['created_by'] = $user_id;
                    $prescription_drug_insert[$i]['modified_by'] = $user_id;
                    $prescription_drug_insert[$i]['created_date_time'] = date('Y-m-d H:i:s');
                    $prescription_drug_insert[$i]['modified_date_time'] = date('Y-m-d H:i:s');
                    $prescription_drug_insert[$i]['remarks'] = $prescription_drug[$i]['remarks'];
                    $prescription_drug_insert[$i]['drug_dose'] = $prescription_drug[$i]['drug_dose'];
                    $prescription_drug_insert[$i]['dosage_unit'] = $prescription_drug[$i]['dosage_unit'];

                    $parameters['prescription'][$i]['patient_prescription_drug_id'] = $this->Generic_model->insertDataReturnId('patient_prescription_drug', $prescription_drug_insert[$i]);
                }   
            } 
        }else{

                // Create master prescription and then line items
            $patient_prescription_insert['patient_id'] = $parameters['patient_id'];
            $patient_prescription_insert['appointment_id'] = $parameters['appointment_id'];
            $patient_prescription_insert['clinic_id'] = $parameters['clinic_id'];
            $patient_prescription_insert['doctor_id'] = $parameters['doctor_id'];
            $patient_prescription_insert['plan'] = $parameters['plan'];
            $patient_prescription_insert['general_instructions'] = $parameters['instructions'];
            $patient_prescription_insert['status'] = 1;
            $patient_prescription_insert['created_by'] = $user_id;
            $patient_prescription_insert['modified_by'] = $user_id;
            $patient_prescription_insert['created_date_time'] = date('Y-m-d H:i:s');
            $patient_prescription_insert['modified_date_time'] = date('Y-m-d H:i:s');

            $patient_prescription_id = $this->Generic_model->insertDataReturnId('patient_prescription', $patient_prescription_insert);

            if (count($prescription_drug) > 0) {
                for ($i = 0; $i < count($prescription_drug); $i++) {
                    $prescription_drug_insert[$i]['patient_prescription_id'] = $patient_prescription_id;
                    $prescription_drug_insert[$i]['drug_id'] = $prescription_drug[$i]['drug_id'];
                    $prescription_drug_insert[$i]['medicine_name'] = $prescription_drug[$i]['medicine_name'];
                    $prescription_drug_insert[$i]['day_schedule'] = $prescription_drug[$i]['day_schedule'];
                    $prescription_drug_insert[$i]['day_dosage'] = $prescription_drug[$i]['day_dosage'];
                    $prescription_drug_insert[$i]['dosage_frequency'] = $prescription_drug[$i]['dosage_frequency'];
                    $prescription_drug_insert[$i]['preffered_intake'] = $prescription_drug[$i]['preffered_intake'];
                    $prescription_drug_insert[$i]['preffered_time_gap'] = $prescription_drug[$i]['preffered_time_gap'];
                    $prescription_drug_insert[$i]['dose_course'] = $prescription_drug[$i]['dose_course'];
                    $prescription_drug_insert[$i]['dose_course'] = $prescription_drug[$i]['dose_course'];
                    $prescription_drug_insert[$i]['quantity'] = $prescription_drug[$i]['quantity'];
                    $prescription_drug_insert[$i]['mode'] = $prescription_drug[$i]['mode'];
                    $prescription_drug_insert[$i]['status'] = 1;
                    $prescription_drug_insert[$i]['drug_status'] = $prescription_drug[$i]['drug_status'];
                    $prescription_drug_insert[$i]['created_by'] = $user_id;
                    $prescription_drug_insert[$i]['modified_by'] = $user_id;
                    $prescription_drug_insert[$i]['created_date_time'] = date('Y-m-d H:i:s');
                    $prescription_drug_insert[$i]['modified_date_time'] = date('Y-m-d H:i:s');
                    $prescription_drug_insert[$i]['remarks'] = $prescription_drug[$i]['remarks'];
                    $prescription_drug_insert[$i]['drug_dose'] = $prescription_drug[$i]['drug_dose'];
                    $prescription_drug_insert[$i]['dosage_unit'] = $prescription_drug[$i]['dosage_unit'];

                    $parameters['prescription'][$i]['patient_prescription_drug_id'] = $this->Generic_model->insertDataReturnId('patient_prescription_drug', $prescription_drug_insert[$i]);
                }   
            } 
        }

        $this->response(array('code' => '200', 'message' => 'Prescription Created Successfully', 'result' => $parameters, 'requestname' => $method));

        $ap_details = $this->Generic_model->getSingleRecord('appointments', array('appointment_id' => $parameters['appointment_id']), '');
        $this->Generic_model->pushNotifications($ap_details->patient_id, $appointment_id, $ap_details->doctor_id, $ap_details->clinic_id, 'pharmacy', 'dashboard');
    }



        // if($parameters['days']!="" || $parameters['days']!=NULL){

        //     $parent_appointment = $this->db->select("*")->from("appointments")->where("appointment_id='" . $parameters['appointment_id'] . "' order by appointment_id asc ")->get()->row();


        //     $next_appointment = $date = date('Y-m-d', strtotime('+' . $parameters['days'] . ' day', strtotime($parent_appointment->appointment_date)));

        //     if ($parent_appointment->priority != '' || $parent_appointment->priority != NULL) {
        //         $pr = $parent_appointment->priority;
        //     } else {
        //         $pr = "none";
        //     }
        //     $appointment['parent_appointment_id'] = $parameters['appointment_id'];
        //     $appointment['clinic_id'] = $parent_appointment->clinic_id;
        //     $appointment['patient_id'] = $parent_appointment->patient_id;
        //     $appointment['umr_no'] = $parent_appointment->umr_no;
        //     $appointment['doctor_id'] = $parent_appointment->doctor_id;
        //     $appointment['appointment_type'] = "Follow-up";
        //     $appointment['appointment_date'] = $next_appointment;
        //     $appointment['appointment_time_slot'] = $parent_appointment->appointment_time_slot;
        //     $appointment['priority'] = $pr;
        //     $appointment['status'] = "booked";
        //     $appointment['created_by'] = $user_id;
        //     $appointment['modified_by'] = $user_id;
        //     $appointment['created_date_time'] = date('Y-m-d H:i:s');
        //     $appointment['modified_date_time'] = date('Y-m-d H:i:s');
        //     $param['appointment']['next_followup_date'] = $next_appointment;
        //     $param['appointment']['time_slot'] = $parent_appointment->appointment_time_slot;
        //     $appointment_id = $this->Generic_model->insertDataReturnId("appointments", $appointment);
        //     $prescription_drug_insert[0]['appointment_id'] =  $parameters['appointment_id'];
        //     $prescription_drug_insert[0]['next_followup_date'] = $next_appointment;
        //     $prescription_drug_insert[0]['time_slot'] = $parent_appointment->appointment_time_slot;
        //     $prescription_drug_insert[0]['parent_appointment_id'] = $appointment_id;

        // }
        // else{
        //     $prescription_drug_insert[0]['appointment_id'] =  "";
        //     $prescription_drug_insert[0]['next_followup_date'] = "";
        //     $prescription_drug_insert[0]['time_slot'] = "";
        //     $prescription_drug_insert[0]['parent_appointment_id'] = "";
        // }

        //$this->response(array('code' => '200', 'message' => 'Prescription created successfully', 'result' => $prescription_drug_insert[0], 'requestname' => $method));
}


public function followup_plan_instruction($parameters, $method, $user_id) {

        // Check if the prescription master exist for the specified appointment id
    $check_exist = $this->db->select("*")->from("patient_prescription")->where("appointment_id ='" . $parameters['appointment_id'] . "'")->get()->row();

    $patient_prescription_id = $check_exist->patient_prescription_id;

    if(count($check_exist) > 0){

        $patient_prescription_update['plan'] = $parameters['plan'];
        $patient_prescription_update['general_instructions'] = $parameters['instructions'];

            // Master prescription exist
            // Update it with the instruction and plan provided in the paramaeters    
        $this->Generic_model->updateData('patient_prescription',$patient_prescription_update,array('patient_prescription_id'=>$patient_prescription_id));
    }else{
            // No master prescription exist
            // Create new prescription master record and then 
            // Create master prescription and then line items
        $patient_prescription_insert['patient_id'] = $parameters['patient_id'];
        $patient_prescription_insert['appointment_id'] = $parameters['appointment_id'];
        $patient_prescription_insert['clinic_id'] = $parameters['clinic_id'];
        $patient_prescription_insert['doctor_id'] = $parameters['doctor_id'];
        $patient_prescription_insert['plan'] = $parameters['plan'];
        $patient_prescription_insert['general_instructions'] = $parameters['instructions'];
        $patient_prescription_insert['status'] = 1;
        $patient_prescription_insert['created_by'] = $user_id;
        $patient_prescription_insert['modified_by'] = $user_id;
        $patient_prescription_insert['created_date_time'] = date('Y-m-d H:i:s');
        $patient_prescription_insert['modified_date_time'] = date('Y-m-d H:i:s');

        $patient_prescription_id = $this->Generic_model->insertDataReturnId('patient_prescription', $patient_prescription_insert);
    }

        // Check if the follow up days are provided in the parameters
    if($parameters['days'] != "" || $parameters['days'] != NULL){

        $parent_appointment = $this->db->select("appointment_date, appointment_time_slot")->from("appointments")->where("appointment_id='" . $parameters['appointment_id'] . "'")->get()->row();

            // Calculate the next appointment date with respect to the days specified
        $next_appointment_date = $date = date('Y-m-d', strtotime('+' . $parameters['days'] . ' day', strtotime($parent_appointment->appointment_date)));

        if($parameters['followup_appointment_id'] != '' || $parameters['followup_appointment_id'] != NULL){
                // Update the appointment date & time slot of the existing
            $followup_appointment['appointment_date'] = $next_appointment_date;
            $followup_appointment['appointment_time_slot'] = $parent_appointment->appointment_time_slot;

            $this->Generic_model->updateData('appointments',$followup_appointment,array('appointment_id'=>$parameters['followup_appointment_id']));
        }else{
                // Create the new appointment for the follow days/date provided
            $appointment['parent_appointment_id'] = $parameters['appointment_id'];
            $appointment['clinic_id'] = $parameters['clinic_id'];
            $appointment['patient_id'] = $parameters['patient_id'];
            $appointment['umr_no'] = $parameters['umr_no'];
            $appointment['doctor_id'] = $parameters['doctor_id'];
            $appointment['appointment_type'] = "Follow-up";
            $appointment['appointment_date'] = $next_appointment_date;
            $appointment['appointment_time_slot'] = $parent_appointment->appointment_time_slot;
            $appointment['priority'] = "none";
            $appointment['status'] = "booked";
            $appointment['created_by'] = $user_id;
            $appointment['modified_by'] = $user_id;
            $appointment['created_date_time'] = date('Y-m-d H:i:s');
            $appointment['modified_date_time'] = date('Y-m-d H:i:s');

                // Create new appointment with specified days and generated next appointment date
            $followup_appointment_id = $this->Generic_model->insertDataReturnId("appointments", $appointment);

                // Create a followup_appointment_id item 
            $parameters['followup_appointment_id'] = $followup_appointment_id;
        }          

        $parameters['next_followup_date'] = $next_appointment_date;
        $parameters['time_slot'] = $parent_appointment->appointment_time_slot;

    }

    $this->response(array('code' => '200', 'message' => 'Prescription created successfully', 'result' => $parameters, 'requestname' => $method));

}


public function billing_details($parameters, $method, $user_id) {

    $from_date = $parameters['from_date'];
    $to_date = $parameters['to_date'];
    $clinic_id = $parameters['clinic_id'];
    $patient_id = $parameters['patient_id'];
    $doctor_id = $parameters['doctor_id'];

    $billing_info = $this->db->select("b.created_date_time,SUM(b.amount)as totalamount,c.umr_no,c.first_name,c.last_name,c.title,c.patient_id")->from("billing a")->join(" billing_line_items b","a.billing_id=b.billing_id")->join("patients c","a.patient_id=c.patient_id")->where("a.clinic_id='" . $clinic_id . "'  and  b.created_date_time BETWEEN '" . $from_date . "' AND '" . $to_date . "'group by b.billing_id")->get()->result();

    $i = 0;
    foreach ($billing_info as $key => $value) {

        $doctor_name = $this->Generic_model->getSingleRecord('doctors', $condition = array('doctor_id' => $doctor_id), $order = '');
        $param[$i]['patient_id'] = $value->patient_id;
        $param[$i]['patient_name'] = $value->title . ' ' . $value->first_name . ' ' . $value->last_name;
        $param[$i]['patient_umr'] = $value->umr_no;
        $param[$i]['patient_billing_date'] = $value->created_date_time;
        $param[$i]['patient_billing_amount'] = $value->totalamount;

        $i++;
    }
    $this->response(array('code' => '200', 'message' => 'Billing Details', 'result' => $param, 'requestname' => $method));
}


public function patient_billing_details($parameters, $method, $user_id) {



    $patient_id = $parameters['patient_id'];
    $billing_date = date('Y-m-d', strtotime($parameters['patient_billing_date']));




    $billing_info = $this->db->select("*")->from("billing a")->join("billing_line_items b","a.billing_id=b.billing_id")->where("patient_id='" . $patient_id . "' and  STR_TO_DATE(a.created_date_time, '%Y-%m-%d')='" . $billing_date . "'")->get()->result();




    $i = 0;
    foreach ($billing_info as $key => $value) {


        $param['billing_history'][$i]['billing_type'] = $value->billing_type;
        $param['billing_history'][$i]['mode_of_payment'] = $value->mode_of_payment;
        $param['billing_history'][$i]['amount'] = $value->amount;


        $i++;
    }
    $this->response(array('code' => '200', 'message' => 'Billing Details', 'result' => $param, 'requestname' => $method));
}


public function patient_vitals($parameters, $method, $user_id) {
    $appointment_id = $parameters['appointment_id'];
    $patient_vitals = $this->db->select("*")->from("patient_vital_sign")->where("appointment_id='" . $appointment_id . "'")->get()->result();

    $pv = 0;
    if (count($patient_vitals) > 0) {
        foreach ($patient_vitals as $pav) {
            $vital_info = $this->db->query("select * from vital_sign where short_form ='" . $pav->vital_sign . "'")->row();
            $param['patient_vital'][$pv]['patient_vital_id'] = $pav->patient_vital_id;
            if ($pav->vital_sign == "BP") {
                $param['patient_vital'][$pv]['unit'] = "mmHG";
            } else {
                $param['patient_vital'][$pv]['unit'] = $vital_info->unit;
            }
            $param['patient_vital'][$pv]['vital_sign'] = $pav->vital_sign;

            $param['patient_vital'][$pv]['vital_result'] = $pav->vital_result;
            $pv++;
        }
    } else {
        $param['patient_vital'] = NULL;
    }
    $this->response(array('code' => '200', 'message' => 'Patient Vitals', 'result' => $param, 'requestname' => $method));
}


public function patient_vitals_insert($parameters, $method, $user_id) {
    $patient_id = $parameters['patient_id'];
    $appointment_id = $parameters['appointment_id'];
    $umr_no = $parameters['umr_no'];
    $clinic_id = $parameters['clinic_id'];
    $vitalsign = $parameters['vitalsign'];
    if(count($vitalsign)>0)
    {
        for ($v = 0; $v < count($vitalsign); $v++) {
            $patient_vital['patient_id'] = $patient_id;
            $patient_vital['umr_no'] = $umr_no;
            $patient_vital['clinic_id'] = $clinic_id;
            $patient_vital['appointment_id'] = $appointment_id;
            $patient_vital['vital_sign'] = $vitalsign[$v]['vital_sign_name'];
            $patient_vital['vital_result'] = $vitalsign[$v]['value'];
            //$patient_vital['vital_sign_recording_date_time']=$vitalsign[$v]['vital_sign_recording_date_time'];
            $patient_vital['vital_sign_recording_date_time'] = date("Y-m-d H:i:s");
            $patient_vital['position'] = $vitalsign[$v]['position'];
            $patient_vital['created_by'] = $user_id;
            $patient_vital['modified_by'] = $user_id;
            $patient_vital['created_date_time'] = date('Y-m-d H:i:s');
            $patient_vital['modified_date_time'] = date('Y-m-d H:i:s');
            $this->Generic_model->insertData('patient_vital_sign', $patient_vital);
        }
    }


    $condition = array('appointment_id' => $appointment_id);
        //$appointment['status'] = 'in_consultation';
    $appointment['modified_by'] = $user_id;
    $appointment['modified_date_time'] = date('Y-m-d H:i:s');
    $this->Generic_model->updateData("appointments", $appointment, $condition);

    if($parameters['select_flag'] != 0)
    {
        $d_allergy['allergy'] = $parameters['drug_allergy'];
        $this->Generic_model->updateData("patients", $d_allergy, array('patient_id'=>$patient_id));
    }

    $param['appointment']['appointment_id'] = $appointment_id;
        //$param['appointment']['status'] = $appointment['status'];
    $this->response(array('code' => '200', 'message' => 'Patient Vitals Insert Successfully', 'result' => $param, 'requestname' => $method));
}


public function patient_vitals_update($parameters, $method, $user_id) {
    $patient_id = $parameters['patient_id'];
    $appointment_id = $parameters['appointment_id'];
    $umr_no = $parameters['umr_no'];
    $clinic_id = $parameters['clinic_id'];
    $vitalsign = $parameters['vitalsign'];
    $drug_allergy = $parameters['drug_allergy'];
    for ($v = 0; $v < count($vitalsign); $v++) {
        $patient_vital['patient_id'] = $patient_id;
        $patient_vital['umr_no'] = $umr_no;
        $patient_vital['clinic_id'] = $clinic_id;
        $patient_vital['appointment_id'] = $appointment_id;
        $patient_vital['vital_sign'] = $vitalsign[$v]['vital_sign_name'];
        $patient_vital['vital_result'] = $vitalsign[$v]['value'];
        $patient_vital['position'] = $vitalsign[$v]['position'];
            // $patient_vital['vital_sign_recording_date_time']=$vitalsign[$v]['vital_sign_recording_date_time'];
        $patient_vital['vital_sign_recording_date_time'] = date("Y-m-d H:i:s");
        $patient_vital['created_by'] = $user_id;
        $patient_vital['modified_by'] = $user_id;
        $patient_vital['created_date_time'] = date('Y-m-d H:i:s');
        $patient_vital['modified_date_time'] = date('Y-m-d H:i:s');

        if($vitalsign[$v]['vital_sign_name']=='DBP'){
            $dbp = explode(",",$vitalsign[$v]['patient_vital_id']);

            $res = $this->db->select("count(*) as num_rows")->from("patient_vital_sign")->where("patient_id ='" . $patient_id . "' and patient_vital_id='" . $dbp[0] . "' and vital_sign_recording_date_time ='" . $vitalsign[$v]['vital_sign_recording_date_time'] . "'")->get()->row();
        }else if($vitalsign[$v]['vital_sign_name']=='SBP'){
            $sbp = explode(",",$vitalsign[$v]['patient_vital_id']);
            $res = $this->db->select("count(*) as num_rows")->from("patient_vital_sign")->where(" patient_id ='" . $patient_id . "' and patient_vital_id='" . $sbp[1] . "' and vital_sign_recording_date_time ='" . $vitalsign[$v]['vital_sign_recording_date_time'] . "'")->get()->row();
        }else{
            $res = $this->db->select("count(*) as num_rows")->from("patient_vital_sign")->where("patient_id ='" . $patient_id . "' and patient_vital_id='" . $vitalsign[$v]['patient_vital_id'] . "' and vital_sign_recording_date_time ='" . $vitalsign[$v]['vital_sign_recording_date_time'] . "'")->get()->row();
        }



        if ($res->num_rows > 0) {
            if($vitalsign[$v]['vital_sign_name']=='DBP'){
                $dbp = explode(",",$vitalsign[$v]['patient_vital_id']);
                $this->db->query("DELETE FROM patient_vital_sign where patient_id ='" . $patient_id . "' and patient_vital_id='" . $dbp[0] . "' and vital_sign_recording_date_time ='" . $vitalsign[$v]['vital_sign_recording_date_time'] . "'");
                $this->Generic_model->insertData('patient_vital_sign', $patient_vital);
            }else if($vitalsign[$v]['vital_sign_name']=='SBP'){
                $sbp = explode(",",$vitalsign[$v]['patient_vital_id']);
                $this->db->query("DELETE FROM patient_vital_sign where patient_id ='" . $patient_id . "' and patient_vital_id='" . $sbp[1] . "' and vital_sign_recording_date_time ='" . $vitalsign[$v]['vital_sign_recording_date_time'] . "'");
                $this->Generic_model->insertData('patient_vital_sign', $patient_vital);
            }else{
                $this->db->query("DELETE FROM patient_vital_sign where patient_id ='" . $patient_id . "' and patient_vital_id='" . $vitalsign[$v]['patient_vital_id'] . "' and vital_sign_recording_date_time ='" . $vitalsign[$v]['vital_sign_recording_date_time'] . "'");
                $this->Generic_model->insertData('patient_vital_sign', $patient_vital);
            }

        } else {

            $this->Generic_model->insertData('patient_vital_sign', $patient_vital);
        }
    }
    $condition = array('appointment_id' => $appointment_id);
    $appointment['status'] = 'in_consultation';
    $appointment['modified_by'] = $user_id;
    $appointment['modified_date_time'] = date('Y-m-d H:i:s');
    $this->Generic_model->updateData("appointments", $appointment, $condition);
    /* Start Drug Allergy Updated */
    if($drug_allergy != 'No' || $drug_allergy !=''){
        $condition_1 = array('patient_id' => $patient_id);
        $patient_DA['allergy'] = $drug_allergy;
        $this->Generic_model->updateData("patients", $patient_DA, $condition_1);
    }

    /* END */
    $param['appointment']['appointment_id'] = $appointment_id;
    $param['appointment']['status'] = $appointment['status'];
    $this->response(array('code' => '200', 'message' => 'Patient Vitals updated Successfully', 'result' => $param, 'requestname' => $method));
}


public function patient_vitals_info($parameters, $method, $user_id) {
    $patient_id = $parameters['patient_id'];
    $patient_info = $this->db->select('*')->from('patients')->where('patient_id = "' . $patient_id.'"')->get()->row();

    if (count($patient_info) > 0) {

        $data['patient_id'] = $patient_info->patient_id;
        $data['patient_name'] = strtoupper($patient_info->first_name);
        $data['umr_no'] = $patient_info->umr_no;
        $data['drug_allergy'] = $patient_info->allergy;

        $patient_vitals = $this->db->select("*")->from("patient_vital_sign")->where("patient_id='" . $patient_id . "'")->group_by("vital_sign_recording_date_time")->get()->result();

        if (count($patient_vitals) > 0) {
            for ($i = 0; $i < count($patient_vitals); $i++) {
                $data['vitals'][$i]['appointment_id'] = $patient_vitals[$i]->appointment_id;
                $appointment_info = $this->db->select('*')->from('appointments')->where('appointment_id ="' . $patient_vitals[$i]->appointment_id.'"')->get()->row();

                $data['vitals'][$i]['appointment_date'] = $appointment_info->appointment_date;
                $data['vitals'][$i]['vital_sign_recording_date_time'] = $patient_vitals[$i]->vital_sign_recording_date_time;
                $doctor_info = $this->db->select("*")->from("doctors")->where("doctor_id ='" . $appointment_info->doctor_id . "'")->get()->row();

                $data['vitals'][$i]['doctor_id'] = $doctor_info->doctor_id;
                $data['vitals'][$i]['doctor_name'] = "Dr. " . strtoupper($doctors_info->first_name . " " . $doctors_info->last_name);
                $patient_vitals1 = $this->db->select("*")->from("patient_vital_sign")->where("patient_id='" . $patient_id . "' and vital_sign_recording_date_time='" . $patient_vitals[$i]->vital_sign_recording_date_time . "'")->order_by(" created_date_time desc ")->get()->result();

                for ($j = 0; $j < count($patient_vitals1); $j++) {
                    $vital_info = $this->db->select("*")->from("vital_sign")->where("short_form ='" . $patient_vitals1[$j]->vital_sign . "'")->get()->row();

                    if ($patient_vitals1[$j]->vital_sign != "SBP" || $patient_vitals1[$j]->vital_sign != "DBP") {
                     $data['vitals'][$i]['vital_signs'][$j]['unit'] = $vital_info->unit;
                 }

                 $data['vitals'][$i]['vital_signs'][$j]['patient_vital_id'] = $patient_vitals1[$j]->patient_vital_id;
                 $data['vitals'][$i]['vital_signs'][$j]['vital_sign_name'] = $patient_vitals1[$j]->vital_sign;
                 $data['vitals'][$i]['vital_signs'][$j]['value'] = $patient_vitals1[$j]->vital_result;
                 $data['vitals'][$i]['vital_signs'][$j]['position'] = $patient_vitals1[$j]->position;
                 $data['vitals'][$i]['vital_signs'][$j]['vital_sign_recording_date_time'] = $patient_vitals1[$j]->vital_sign_recording_date_time;


                 $bp_arr = explode("/", $patient_vitals1[$j]->vital_result);


                 if ($patient_vitals1[$j]->vital_sign == "SBP") {
                    $status_info1 = $this->db->select('*')->from('vital_sign')->where('short_form ="SBP"')->get()->row();
                    $bp_arr1['SBP'] = $patient_vitals1[$j]->vital_result;
                    $pa_id[] = $patient_vitals1[$j]->patient_vital_id;
                    $vsrdt = $patient_vitals1[$j]->vital_sign_recording_date_time;
                    if ($bp_arr['SBP'] >= $status_info1->low_range && $bp_arr['SBP'] <= $status_info1->high_range) {
                        $sbp_status = "normal";
                    } else {
                        $sbp_status = "abnormal";
                    }
                    $data['vitals'][$i]['vital_signs'][$j]['status'] = $sbp_status;
                    $position_val = $patient_vitals1[$j]->position;
                } else if($patient_vitals1[$j]->vital_sign == "DBP"){
                    $status_info2 = $this->db->select('*')->from('vital_sign')->where('short_form ="DBP"')->get()->row();
                    $pa_id[] = $patient_vitals1[$j]->patient_vital_id;
                    $bp_arr1['DBP'] = $patient_vitals1[$j]->vital_result;
                    if ($bp_arr['DBP'] >= $status_info2->low_range && $bp_arr['DBP'] <= $status_info2->high_range) {
                        $dbp_status = "normal";
                    } else {
                        $dbp_status = "abnormal";
                    }
                    $data['vitals'][$i]['vital_signs'][$j]['status'] = $dbp_status;
                    $position_val = $patient_vitals1[$j]->position;
                }else {
                    $status_info = $this->db->select("*")->from("vital_sign")->where("short_form ='" . $patient_vitals1[$j]->vital_sign . "'")->get()->row();
                            //print_r($status_info->low_range);exit;
                    if(count($status_info)>0){
                        if ($patient_vitals1[$j]->vital_result >= $status_info->low_range && $patient_vitals1[$j]->vital_result <= $status_info->high_range) {
                            $data['vitals'][$i]['vital_signs'][$j]['status'] = "normal";
                        } else {
                            $data['vitals'][$i]['vital_signs'][$j]['status'] = "abnormal";
                        }
                    }else{
                        $data['vitals'][$i]['vital_signs'][$j]['status'] = "normal";
                    }
                }
            }
            if(count($pa_id)>0){
                $status_info1 = $this->db->select('*')->from('vital_sign')->where('short_form ="SBP"')->get()->row();
                $status_info2 = $this->db->select('*')->from('vital_sign')->where('short_form ="DBP"')->get()->row();
                   // $bp_arr = explode("/", $patient_vitals1[$j]->vital_result);
                if ($bp_arr1['SBP'] >= $status_info1->low_range && $bp_arr1['SBP'] <= $status_info1->high_range) {
                    $sbp_status1 = "normal";
                } else {
                    $sbp_status1 = "abnormal";
                }
                if ($bp_arr1['DBP'] >= $status_info2->low_range && $bp_arr1['DBP'] <= $status_info2->high_range) {
                    $dbp_status1 = "normal";
                } else {
                    $dbp_status1 = "abnormal";
                }
                $data['vitals'][$i]['vital_signs'][$j]['patient_vital_id'] = implode(",",$pa_id);
                $data['vitals'][$i]['vital_signs'][$j]['unit'] = 'mmHg';
                $data['vitals'][$i]['vital_signs'][$j]['vital_sign_name']="BP";
                $data['vitals'][$i]['vital_signs'][$j]['value'] = $bp_arr1['SBP']."/".$bp_arr1['DBP'];
                $data['vitals'][$i]['vital_signs'][$j]['position'] = $position_val;
                $data['vitals'][$i]['vital_signs'][$j]['status'] = $sbp_status1 . "/" . $dbp_status1;
                $data['vitals'][$i]['vital_signs'][$j]['vital_sign_recording_date_time'] = $vsrdt;
                            //$data['vitals'][$i]['vital_signs'][$j]['status'] = $sbp_status;
                unset($pa_id);
            }

        }

        $this->response(array('code' => '200', 'message' => 'Patient Vitals', 'result' => $data, 'requestname' => $method));
    } else {
        $data['vitals'] = [];
        $this->response(array('code' => '200', 'message' => 'No Vitals', 'result' => $data, 'requestname' => $method));
    }
} else {
    $this->response(array('code' => '400', 'message' => 'no patient information'));
}
}

public function custom_form_recursive_dup($parameters, $method, $user_id) {
    $condition = array('form_id' => $parameters['form_id']);
    $form = $this->Generic_model->getAllRecords("form", $condition = '', $order = '');
    $sections = $this->db->query("SELECT b.form_name,b.form_type,a.section_id from section a inner join form b on a.form_id=b.form_id where b.form_id=" . $parameters['form_id'])->result();

    $i = 0;
    foreach ($sections as $key => $value) {
        $data['form']['formType'] = $value->form_type;
        $data['form']['formName'] = $value->form_name;
        $data['form']['sections'][$i]['title'] = $value->title;
        $data['form']['sections'][$i]['brief'] = $value->brief;
        $data['form']['sections'][$i]['format_type'] = $value->format_type;
        $label_result = $this->db->query("select * from field where section_id='" . $value->section_id . "' and parent_field_id IS NULL and parent_option_id IS NULL")->result();

        $j = 0;
        foreach ($label_result as $key2 => $value2) {
            $data['form']['sections'][$i]['labels'][$j]['id'] = $value2->field_id;
            $data['form']['sections'][$i]['labels'][$j]['field_type'] = $value2->field_type;
            $data['form']['sections'][$i]['labels'][$j]['labeltext'] = $value2->field_name;
            $field_option_result = $this->db->query('select * from field_option  where field_id=' . $value2->field_id)->result();
            $k = 0;
            foreach ($field_option_result as $key3 => $value3) {
                if ($value3->dependency == 1) {
                    $depresult = $this->db->query("select * from field where section_id='" . $value->section_id . "' and parent_field_id=" . $value2->field_id . " and parent_option_id=" . $value3->option_id)->result_array();

                    $depinfo = $this->getchilddetails($depresult, $value2->field_id, $value3->option_id, $value->section_id);
                } else {
                    $depinfo = $this->db->query("select * from field where section_id='" . $value->section_id . "' and parent_field_id='" . $value2->field_id . "' and parent_option_id='" . $value3->option_id . "'")->result_array();
                }
                $k++;
            }
            $j++;
        }
    }
}

public function custom_form_recursive_bcp($parameters, $method, $user_id) {
    $condition = array('form_type' => $parameters['form_type']);
    $form = $this->Generic_model->getAllRecords("form", $condition = '', $order = '');
    $sections = $this->db->query("SELECT b.form_id,b.form_name,b.form_type,a.section_id,a.format_type,a.title,a.brief from section a inner join form b on a.form_id=b.form_id where b.form_type='" . $parameters['form_type'] . "'")->result();

    $i = 0;
    foreach ($sections as $key => $value) {
        $data['form']['formType'] = $value->form_type;
        $data['form']['formName'] = $value->form_name;
        $data['form']['form_id'] = $value->form_id;
        $data['form']['sections'][$i]['title'] = $value->title;
        $data['form']['sections'][$i]['brief'] = $value->brief;
        $data['form']['sections'][$i]['section_id'] = $value->section_id;
        $data['form']['sections'][$i]['format'] = $value->format_type;

        if ($value->format_type == 'tabular') {
            $label_result = $this->db->query("select * from field where section_id='" . $value->section_id . "' and parent_field_id IS NULL and parent_option_id IS NULL group by row_index")->result();
            $j = 0;
            $di = 0;
            $array_dup = array();
            foreach ($label_result as $key2 => $value2) {
                $data['form']['sections'][$i]['rows'][$j]['row_title'] = strtok($value2->field_name, '_');
                $data['form']['sections'][$i]['rows'][$j]['row_index'] = $value2->row_index;
                $row_elements = $this->db->query("select * from field where section_id='" . $value->section_id . "' and row_index=" . $value2->row_index)->result();

                $k = 0;
                foreach ($row_elements as $key => $rresult) {
                    $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['id'] = $rresult->field_id;
                    $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['widgetType'] = $rresult->field_type;
                    $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['labelText'] = substr($rresult->field_name, (strpos($rresult->field_name, '_') ?: -1) + 1);
                    $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['row_index'] = $rresult->row_index;
                    $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['column_index'] = $rresult->column_index;
                    $options = $this->db->query("select * from field_option where field_id=" . $rresult->field_id)->result();
                    $l = 0;
                    foreach ($options as $okey => $oresult) {
                        $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['optionText'] = $oresult->option_name;
                        $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['default'] = $oresult->option_default;
                        $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['id'] = $oresult->option_id;
                        $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['label_id'] = $oresult->field_id;
                        if ($oresult->dependency == 1) {
                            $dep_chk = $this->db->query("select * from field where parent_field_id=" . $oresult->field_id . " and parent_option_id=" . $oresult->option_id)->result();
                            $m = 0;
                            $n = 0;
                            foreach ($dep_chk as $depkey => $depresult) {
                                $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['id'] = $depresult->field_id;
                                $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['widgetType'] = $depresult->field_type;
                                $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['labelText'] = substr($depresult->field_name, (strpos($depresult->field_name, '_') ?: -1) + 1);
                                $dep_options = $this->db->query("select * from field_option where field_id=" . $depresult->field_id)->result();
                                $o = 0;
                                foreach ($dep_options as $depokey => $deporesult) {
                                    $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['optionText'] = $deporesult->option_name;
                                    $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['default'] = $deporesult->option_default;
                                    $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['id'] = $deporesult->option_id;
                                    $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['label_id'] = $deporesult->field_id;
                                    $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['dependency'] = array();
                                    $o++;
                                }
                                $m++;
                                $n++;
                            }
                        } else {
                            $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'] = array();
                        }
                        $l++;
                    }
                    $k++;
                }
                $j++;
            }
        } else {
            $label_result = $this->db->query("select * from field where section_id='" . $value->section_id . "' and parent_field_id IS NULL and parent_option_id IS NULL")->result();
            $j = 0;
            $di = 0;
            $array_dup = array();
            foreach ($label_result as $key2 => $value2) {
                $data['form']['sections'][$i]['labels'][$j]['id'] = $value2->field_id;
                $data['form']['sections'][$i]['labels'][$j]['widgetType'] = $value2->field_type;
                $data['form']['sections'][$i]['labels'][$j]['labelText'] = $value2->field_name;
                $field_option_result = $this->db->query('select * from field_option  where field_id=' . $value2->field_id)->result();
                $k = 0;
                foreach ($field_option_result as $key3 => $value3) {
                    $data['form']['sections'][$i]['labels'][$j]['options'][$k]['optionText'] = $value3->option_name;
                    $data['form']['sections'][$i]['labels'][$j]['options'][$k]['default'] = $value3->option_default;
                    $data['form']['sections'][$i]['labels'][$j]['options'][$k]['id'] = $value3->option_id;
                    $data['form']['sections'][$i]['labels'][$j]['options'][$k]['label_id'] = $value2->field_id;
                    if ($value3->dependency == 1) {
                        $depresult = $this->db->query("select * from field where section_id='" . $value->section_id . "' and parent_field_id=" . $value2->field_id . " and parent_option_id=" . $value3->option_id)->result_array();

                        $depinfo = $this->getchilddetails($depresult, $value2->field_id, $value3->option_id, $value->section_id, $di, $data['form']['sections'][$i]['labels'][$j]['options'][$k]['dependency'][0]);
                    } else {
                        $depinfo = $this->db->query("select * from field where section_id='" . $value->section_id . "' and parent_field_id='" . $value2->field_id . "' and parent_option_id='" . $value3->option_id . "'")->result_array();
                    }

                    $k++;
                }
                $j++;
            }
        }$i++;
    }

    $this->response(array('code' => '200', 'message' => 'form_details', 'result' => $data, 'requestname' => $method));
}

public function custom_form_recursive($parameters, $method, $user_id) {
    $condition = array('form_type' => $parameters['form_type']);
    $form = $this->Generic_model->getAllRecords("form", $condition = '', $order = '');

    $sections = $this->db->select("b.form_id,b.form_name,b.form_type,a.section_id,a.format_type,a.title,a.brief")->from("section a")->join("form b","a.form_id=b.form_id")->where("b.form_type='" . $parameters['form_type'] . "' and parent_section_id = 0")->get()->result();
    $a = 0;
    foreach ($sections as $key => $pvalue) {
        $data['form']['formType'] = $pvalue->form_type;
        $data['form']['formName'] = $pvalue->form_name;
        $data['form']['form_id'] = $pvalue->form_id;
        $data['form']['sections'][$a]['section_id'] = $pvalue->section_id;
        $data['form']['sections'][$a]['title'] = $pvalue->title;
        $data['form']['sections'][$a]['brief'] = $pvalue->brief;
        $data['form']['sections'][$a]['textbox'] = 1;
        $data['form']['sections'][$a]['collapse'] = 0;

        $sub_sections = $this->db->select("b.form_id,b.form_name,b.form_type,a.section_id,a.format_type,a.title,a.brief")->from("section a")->join("form b","a.form_id=b.form_id")->where("b.form_type='" . $parameters['form_type'] . "' and parent_section_id='" . $pvalue->section_id . "'")->get()->result();

        $i = 0;
        foreach ($sub_sections as $key => $value) {
            $data['form']['sections'][$a]['sub_sections'][$i]['sub_section_id'] = $value->section_id;
            $data['form']['sections'][$a]['sub_sections'][$i]['title'] = $value->title;
            $data['form']['sections'][$a]['sub_sections'][$i]['format'] = $value->format_type;

            if ($value->format_type == 'tabular') {
                $label_result = $this->db->select("*")->from("field")->where("section_id='" . $value->section_id . "' and parent_field_id='0' and parent_option_id='0'")->group_by(" row_index")->get()->result();
                $j = 0;
                $di = 0;
                $array_dup = array();
                foreach ($label_result as $key2 => $value2) {
                    $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['row_title'] = strtok($value2->field_name, '_');
                    $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['row_index'] = $value2->row_index;
                    $row_elements = $this->db->select("*")->from("field")->where("section_id='" . $value->section_id . "' and row_index=" . $value2->row_index)->get()->result();

                    $k = 0;
                    foreach ($row_elements as $key => $rresult) {
                        $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['id'] = $rresult->field_id;
                        $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['widgetType'] = $rresult->field_type;
                        $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['labelText'] = substr($rresult->field_name, (strpos($rresult->field_name, '_') ?: -1) + 1);
                        $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['row_index'] = $rresult->row_index;
                        $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['column_index'] = $rresult->column_index;
                        $options = $this->db->select("*")->from("field_option")->where("field_id=" . $rresult->field_id)->get()->result();
                        $l = 0;
                        foreach ($options as $okey => $oresult) {
                            $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['optionText'] = $oresult->option_name;
                            $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['default'] = $oresult->option_default;
                            $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['id'] = $oresult->option_id;
                            $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['label_id'] = $oresult->field_id;
                            if ($oresult->dependency == 1) {
                                $dep_chk = $this->db->select("*")->from("field")->where("parent_field_id=" . $oresult->field_id . " and parent_option_id=" . $oresult->option_id)->get()->result();
                                $m = 0;
                                $n = 0;
                                foreach ($dep_chk as $depkey => $depresult) {
                                    $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['id'] = $depresult->field_id;
                                    $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['widgetType'] = $depresult->field_type;
                                    $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['labelText'] = substr($depresult->field_name, (strpos($depresult->field_name, '_') ?: -1) + 1);
                                    $dep_options = $this->db->select("*")->from("field_option")->where(") field_id=" . $depresult->field_id)->get()->result();
                                    $o = 0;
                                    foreach ($dep_options as $depokey => $deporesult) {
                                        $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['optionText'] = $deporesult->option_name;
                                        $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['default'] = $deporesult->option_default;
                                        $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['id'] = $deporesult->option_id;
                                        $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['label_id'] = $deporesult->field_id;
                                        $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['dependency'] = array();
                                        $o++;
                                    }
                                    $m++;
                                    $n++;
                                }
                            } else {
                                $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'] = array();
                            }
                            $l++;
                        }
                        $k++;
                    }
                    $j++;
                }
            } else {
                $label_result = $this->db->select("*")->from("field")->where("section_id='" . $value->section_id . "' and parent_field_id='0' and parent_option_id='0'")->get()->result();
                $j = 0;
                $di = 0;
                $array_dup = array();
                foreach ($label_result as $key2 => $value2) {
                    $data['form']['sections'][$a]['sub_sections'][$i]['labels'][$j]['id'] = $value2->field_id;
                    $data['form']['sections'][$a]['sub_sections'][$i]['labels'][$j]['widgetType'] = $value2->field_type;
                    $data['form']['sections'][$a]['sub_sections'][$i]['labels'][$j]['labelText'] = $value2->field_name;
                    $field_option_result = $this->db->query('select * from field_option  where field_id=' . $value2->field_id)->result();
                    $k = 0;
                    foreach ($field_option_result as $key3 => $value3) {
                        $data['form']['sections'][$a]['sub_sections'][$i]['labels'][$j]['options'][$k]['optionText'] = $value3->option_name;
                        $data['form']['sections'][$a]['sub_sections'][$i]['labels'][$j]['options'][$k]['default'] = $value3->option_default;
                        $data['form']['sections'][$a]['sub_sections'][$i]['labels'][$j]['options'][$k]['id'] = $value3->option_id;
                        $data['form']['sections'][$a]['sub_sections'][$i]['labels'][$j]['options'][$k]['label_id'] = $value2->field_id;
                        if ($value3->dependency == 1) {
                            $depresult = $this->db->select("*")->from("field")->where("section_id='" . $value->section_id . "' and parent_field_id=" . $value2->field_id . " and parent_option_id=" . $value3->option_id)->get()->result_array();

                            $depinfo = $this->getchilddetails($depresult, $value2->field_id, $value3->option_id, $value->section_id, $di, $data['form']['sections'][$a]['sub_sections'][$i]['labels'][$j]['options'][$k]['dependency'][0]);
                        } else {
                            $depinfo = $this->db->select("*")->from("field")->where("section_id='" . $value->section_id . "' and parent_field_id='" . $value2->field_id . "' and parent_option_id='" . $value3->option_id . "'")->get()->result_array();
                        }

                        $k++;
                    }
                    $j++;
                }
            }$i++;
        }$a++;
    }

    $this->response(array('code' => '200', 'message' => 'form_details', 'result' => $data, 'requestname' => $method));
}

public function getchilddetails(array $elements, $parentfield_id = 0, $parentoption_id = 0, $section_id = 0, &$rdi, &$in_arr = array()) {
        //$recuarray = $this->db->query("select * from field where section_id=".$section_id." and parent_field_id=".$parentfield_id." and parent_option_id=".$parentoption_id)->result_array();
    $ri = 0;

    foreach ($elements as $result) {
        $in_arr['labels'][$ri]['id'] = $result['field_id'];
        $in_arr['labels'][$ri]['widgetType'] = $result['field_type'];
        $in_arr['labels'][$ri]['labelText'] = $result['field_name'];
        $dep_field_option_result = $this->db->select('*')->from('field_option')->where('field_id=' . $result['field_id'])->get()->result();
        $l = 0;
        if (count($dep_field_option_result) > 0) {
            foreach ($dep_field_option_result as $key5 => $value5) {
                $in_arr['labels'][$ri]['options'][$l]['optionText'] = $value5->option_name;
                $in_arr['labels'][$ri]['options'][$l]['default'] = $value5->option_default;
                $in_arr['labels'][$ri]['options'][$l]['id'] = $value5->option_id;
                $in_arr['labels'][$ri]['options'][$l]['label_id'] = $result['field_id'];
                $recuarray = $this->db->select("*")->from("field")->where("section_id=" . $section_id . " and parent_field_id=" . $result['field_id'] . " and parent_option_id=" . $value5->option_id)->get()->result_array();
                    //$in_arr['labels'][$ri]['options'][$l]['dependency'] = array();
                $recuresult = $this->getchilddetails($recuarray, $result['field_id'], $value5->option_id, $section_id, $rdi);
                if (count($recuresult) > 0) {
                    $in_arr['labels'][$ri]['options'][$l]['dependency'][] = $recuresult;
                    $rdi++;
                } else {
                    $in_arr['labels'][$ri]['options'][$l]['dependency'] = array();
                }
                $l++;
            }
            $ri++;
        }
        else{
            $ri++;
        }
    }
    return $in_arr;
}

public function custom_form($parameters, $method, $user_id) {
    $condition = array('form_id' => $parameters['form_id']);
    $form = $this->Generic_model->getAllRecords("form", $condition = '', $order = '');


    $sections = $this->db->select("b.form_name,b.form_type,a.section_id")->from("section a")->join("form b","a.form_id=b.form_id")->where("b.form_id=" . $parameters['form_id'])->get()->result();
    $i = 0;
    foreach ($sections as $key => $value) {
        $data['form']['formType'] = $value->form_type;
        $data['form']['formName'] = $value->form_name;

        $data['form']['sections'][$i]['title'] = $value->title;
        $data['form']['sections'][$i]['brief'] = $value->brief;
        $data['form']['sections'][$i]['format_type'] = $value->format_type;
        $label_result = $this->db->select("*")->from("field")->where("section_id='" . $value->section_id . "' and parent_field_id IS NULL and parent_option_id IS NULL")->get()->result();
        $j = 0;
        foreach ($label_result as $key2 => $value2) {
            $data['form']['sections'][$i]['labels'][$j]['id'] = $value2->field_id;
            $data['form']['sections'][$i]['labels'][$j]['widgetType'] = $value2->field_type;
            $data['form']['sections'][$i]['labels'][$j]['labeltext'] = $value2->field_name;
            $field_option_result = $this->db->select('*')->from('field_option')->where('field_id=' . $value2->field_id)->get()->result();
            $k = 0;
            foreach ($field_option_result as $key3 => $value3) {
                $data['form']['sections'][$i]['labels'][$j]['options'][$k]['optionText'] = $value3->option_name;
                $data['form']['sections'][$i]['labels'][$j]['options'][$k]['default'] = $value3->option_default;

                $label_result_dependency = $this->db->select("*")->from("field")->where("section_id='" . $value->section_id . "' and parent_field_id='" . $value2->field_id . "' and parent_option_id='" . $value3->option_id . "'")->get()->result();

                if (count($label_result_dependency) > 0) {
                    $m = 0;
                    foreach ($label_result_dependency as $key4 => $value4) {
                        $data['form']['sections'][$i]['labels'][$j]['options'][$k]['dependency'][$m]['id'] = $value4->field_id;
                        $data['form']['sections'][$i]['labels'][$j]['options'][$k]['dependency'][$m]['widgetType'] = $value4->field_type;
                        $data['form']['sections'][$i]['labels'][$j]['options'][$k]['dependency'][$m]['labeltext'] = $value4->field_name;
                        $dep_field_option_result = $this->db->select('*')->from('field_option')->where('field_id="' . $value4->field_id.'"')->get()->result();
                        $l = 0;
                        foreach ($dep_field_option_result as $key5 => $value5) {

                            $data['form']['sections'][$i]['labels'][$j]['options'][$k]['dependency'][$m]['options'][$l]['optionText'] = $value5->option_name;
                            $data['form']['sections'][$i]['labels'][$j]['options'][$k]['dependency'][$m]['options'][$l]['default'] = $value5->option_default;
                            $l++;
                        }
                        $label_result_dependency = $this->db->select("*")->from("field")->where(" section_id='" . $value->section_id . "' and parent_field_id='" . $value2->field_id . "' and parent_option_id='" . $value3->option_id . "'")->get()->result();

                        $m++;
                    }
                }

                $k++;
            }

            $j++;
        }

        $i++;
    }

    $this->response(array('code' => '200', 'message' => 'form_details', 'result' => $data, 'requestname' => $method));
}

public function indent_insert($parameters, $method, $user_id) {

    $indent['user_id'] = $user_id;
    $indent['clinic_id'] = $parameters['clinic_id'];
    $indent['indent_date'] = $parameters['indent_date'];
    $indent['created_by'] = $user_id;
    $indent['status'] = 1;
    $indent['modified_by'] = $user_id;
    $indent['created_date_time'] = date('Y-m-d H:i:s');
    $indent['modified_date_time'] = date('Y-m-d H:i:s');
    $indent_id = $this->Generic_model->insertDataReturnId('pharmacy_indent', $indent);

    for ($v = 0; $v < count($parameters['indent_line_items']); $v++) {

        $indent_items['pharmacy_indent_id'] = $indent_id;
        $indent_items['drug_id'] = $parameters['indent_line_items'][$v]['drug_id'];
        $indent_items['quantity'] = $parameters['indent_line_items'][$v]['quantity'];
        $indent_items['created_by'] = $user_id;
        $indent_items['status'] = 1;
        $indent_items['modified_by'] = $user_id;
        $indent_items['created_date_time'] = date('Y-m-d H:i:s');
        $indent_items['modified_date_time'] = date('Y-m-d H:i:s');
        $this->Generic_model->insertData('pharmacy_indent_line_items', $indent_items);
    }

    $this->response(array('code' => '200', 'message' => 'Indent Insert Successfully', 'requestname' => $method));
}

public function patient_checklist_update($parameters, $method, $user_id) {

    for ($v = 0; $v < count($parameters['consent_check_list']); $v++) {
        $insert_checklist['category'] = $parameters['consent_check_list'][$v]['category'];
        $insert_checklist['checked'] = $parameters['consent_check_list'][$v]['checked'];
        if ($parameters['consent_check_list'][$v]['nurse_review'] == 0) {
            $doctor_review = 1;
            $nurse_review = 0;
        }
        if ($parameters['consent_check_list'][$v]['doctor_review'] == 0) {
            $doctor_review = 0;
            $nurse_review = 1;
        }
        $insert_checklist['nurse_review'] = $nurse_review;
        $insert_checklist['doctor_review'] = $doctor_review;
        $insert_checklist['remark'] = $parameters['consent_check_list'][$v]['remark'];
        $insert_checklist['created_by'] = $user_id;
        $insert_checklist['modified_by'] = $user_id;
        $insert_checklist['modified_date_time'] = date('Y-m-d H:i:s');
        $this->Generic_model->updateData("patient_checklist", $insert_checklist, array('patient_checklist_id' => $parameters['consent_check_list'][$v]['patient_checklist_id']));
    }
//updating in patient consent form
    $data1['checked_by'] = $parameters['checked_by'];
    $data1['done_by'] = $parameters['done_by'];
    $data1['assisted_by'] = $parameters['assisted_by'];
    $data1['nurse'] = $parameters['nurse'];
    $data1['anesthetist'] = $parameters['anesthetist'];
    $this->Generic_model->updateData("patient_consent_forms", $data1, array('patient_consent_form_id' => $parameters['patient_consent_form_id']));

    $this->response(array('code' => '200', 'message' => 'Check List Updated Successfully', 'requestname' => $method));
}


public function consent_check_list($parameters, $method, $user_id) {
    $clist = $this->db->select("*")->from("patient_checklist cf")->join("checklist_master cm","cf.checklist_id = cm.checklist_id")->where("cf.patient_consent_form_id='" . $parameters['patient_consent_form_id'] . "'")->order_by("cm.checklist_id")->get()->result();
    $check_data = $this->db->select("*")->from("patient_consent_forms")->where("patient_consent_form_id='" . $parameters['patient_consent_form_id'] . "'")->get()->row();
    $data['checked_by'] = $check_data->checked_by;
    $data['done_by'] = $check_data->done_by;
    $data['assisted_by'] = $check_data->assisted_by;
    $data['nurse'] = $check_data->nurse;
    $data['anesthetist'] = $check_data->anesthetist;

    if (count($clist) > 0) {
        $i = 0;
        foreach ($clist as $cdata) {

            $data['consent_check_list'][$i]['patient_checklist_id'] = $cdata->patient_checklist_id;
            $data['consent_check_list'][$i]['checklist_id'] = $cdata->checklist_id;
            $data['consent_check_list'][$i]['name'] = $cdata->description;
            $data['consent_check_list'][$i]['doctor_id'] = $cdata->doctor_id;
            $data['consent_check_list'][$i]['type'] = $cdata->type;
            $data['consent_check_list'][$i]['category'] = $cdata->category;
            $data['consent_check_list'][$i]['appointment_id'] = $cdata->appointment_id;
            $data['consent_check_list'][$i]['patient_consent_form_id'] = $cdata->patient_consent_form_id;
            $data['consent_check_list'][$i]['checked'] = $cdata->checked;
            $data['consent_check_list'][$i]['nurse_review'] = $cdata->nurse_review;
            $data['consent_check_list'][$i]['doctor_review'] = $cdata->doctor_review;
            $data['consent_check_list'][$i]['remark'] = $cdata->remark;
            $data['consent_check_list'][$i]['checked_by'] = $cdata->checked_by;
            $data['consent_check_list'][$i]['changed_by'] = $cdata->changed_by;
            $i++;
        }
    } else {
        $data['consent_check_list'] = NULL;
    }
    $this->response(array('code' => '200', 'message' => 'Consent Checklist', 'result' => $data, 'requestname' => $method));
}

public function pharmacy_billing_list($parameters, $method, $user_id) {
    $bill_list = $this->db->select('*')->from('billing b')->join('billing_line_items bl','b.billing_id = bl.billing_id')->where('b.billing_type = "Pharmacy" and clinic_id="' . $parameters['clinic_id'] . '"')->get()->result();
    if (count($bill_list) > 0) {
        $i = 0;
        $j = 0;
        foreach ($bill_list as $bills) {

            $data['billing'][$i]['invoice_no'] = $bills->invoice_no;
            $data['billing'][$i]['invoice_no_alias'] = $bills->invoice_no_alias;
            $data['billing'][$i]['clinic_id'] = $bills->clinic_id;
            $data['billing'][$i]['patient_id'] = $bills->patient_id;
            $data['billing'][$i]['umr_no'] = $bills->umr_no;
            $data['billing'][$i]['doctor_id'] = $bills->doctor_id;
            $data['billing'][$i]['mode_of_payment'] = $bills->mode_of_payment;
            $data['billing'][$i]['billing_date_time'] = $bills->billing_date_time;
            $data['billing'][$i]['pdf_path'] = base_url() . "uploads/prescriptions/" . $bills->invoice_pdf;
            $data['billing'][$i]['patient_id'] = $bills->patient_id;
            $j = 0;
            $bill_info = $this->db->select("*")->from("billing_line_items")->where("billing_id='" . $bills->billing_id . "'")->get()->result();
            foreach ($bill_info as $key => $value) {

                $data['billing'][$i]['billing_line_items'][$j]['item_information'] = $value->item_information;
                $data['billing'][$i]['billing_line_items'][$j]['quantity'] = $value->quantity;
                $data['billing'][$i]['billing_line_items'][$j]['amount'] = $value->amount;
                $j++;
            }

            $i++;
        }
    } else {
        $data['billing'] = NULL;
    }
    $this->response(array('code' => '200', 'message' => 'Billing list', 'result' => $data, 'requestname' => $method));
}

public function investigation_billing_list($parameters, $method, $user_id) {
    $bill_list = $this->db->select('*')->from('billing b')->join('billing_line_items bl','b.billing_id = bl.billing_id')->where('bl.billing_type = "Investigations" and clinic_id="' . $parameters['clinic_id'] . '"')->group_by('b.billing_id')->get()->result();
    if (count($bill_list) > 0) {
        $i = 0;
        foreach ($bill_list as $bills) {

            $data['billing'][$i]['invoice_no'] = $bills->invoice_no;
            $data['billing'][$i]['invoice_no_alias'] = $bills->invoice_no_alias;
            $data['billing'][$i]['clinic_id'] = $bills->clinic_id;
            $data['billing'][$i]['patient_id'] = $bills->patient_id;
            $data['billing'][$i]['umr_no'] = $bills->umr_no;
            $data['billing'][$i]['doctor_id'] = $bills->doctor_id;
            $data['billing'][$i]['mode_of_payment'] = $bills->mode_of_payment;
            $data['billing'][$i]['billing_date_time'] = $bills->billing_date_time;
            $data['billing'][$i]['pdf_path'] = base_url() . "uploads/investigation_invoice/" . $bills->invoice_pdf;
            $data['billing'][$i]['patient_id'] = $bills->patient_id;
            $j = 0;
            $bill_info = $this->db->select("*")->from("billing_line_items")->where("billing_id='" . $bills->billing_id . "'")->get()->result();
            foreach ($bill_info as $key => $value) {

                $data['billing'][$i]['billing_line_items'][$j]['item_information'] = $value->item_information;
                $data['billing'][$i]['billing_line_items'][$j]['amount'] = $value->amount;
                $j++;
            }

            $i++;
        }
    } else {
        $data['billing'] = NULL;
    }
    $this->response(array('code' => '200', 'message' => 'Billing list', 'result' => $data, 'requestname' => $method));
}

public function indent_list($parameters, $method, $user_id) {
    $drug_list = $this->db->select('*')->from('pharmacy_indent a')->join('pharmacy_indent_line_items b','a.pharmacy_indent_id = b.pharmacy_indent_id')->where('a.clinic_id="' . $parameters['clinic_id'] . '"')->get()->result();
    if (count($drug_list) > 0) {
        for ($i = 0; $i < count($drug_list); $i++) {

            $data['pharmacy_indent'][$i]['indent_date'] = $drug_list[$i]->indent_date;
            $data['pharmacy_indent'][$i]['clinic_id'] = $parameters['clinic_id'];
            $data['pharmacy_indent'][$i]['drug_id'] = $drug_list[$i]->drug_id;
            $drug_info = $this->db->select('*')->from('drug')->where('drug_id = ' . $drug_list[$i]->drug_id)->row();
            $data['pharmacy_indent'][$i]['drug_name'] = $drug_info->trade_name;
            $data['pharmacy_indent'][$i]['quantity'] = $drug_list[$i]->quantity;
            $clinic_info = $this->db->query('select * from clinics where clinic_id = ' . $parameters['clinic_id'])->row();
            $data['pharmacy_indent'][$i]['clinic_name'] = $clinic_info->clinic_name;
        }
    } else {
        $data['pharmacy_indent'] = NULL;
    }
    $this->response(array('code' => '200', 'message' => 'indent list', 'result' => $data, 'requestname' => $method));
}

public function patient_investigation_list($parameters, $method, $user_id) {

    $investigations = $this->db->select("*,pi.status as inv_status")->from("patient_investigation pi")->join("patient_investigation_line_items pil","pi.patient_investigation_id = pil.patient_investigation_id")->where("pi.clinic_id= '" . $parameters['clinic_id'] . "' and pil.checked=0")->group_by("pi.patient_investigation_id")->order_by("pi.patient_investigation_id","desc")->get()->result();

    $pv = 0;
    if (count($investigations) > 0) {

        foreach ($investigations as $pav) {
            $param['investigations'][$pv]['patient_investigation_id'] = $pav->patient_investigation_id;
            $param['investigations'][$pv]['appointment_id'] = $pav->appointment_id;
            $param['investigations'][$pv]['patient_id'] = $pav->patient_id;
            $patient_deatails = $this->Generic_model->getSingleRecord('patients', array('patient_id' => $pav->patient_id), $order = '');
            $param['investigations'][$pv]['patient_name'] = "Mr. " . strtoupper($patient_deatails->first_name . " " . $patient_deatails->last_name);
            $param['investigations'][$pv]['umr_no'] = $pav->umr_no;

            $doctor_deatails = $this->Generic_model->getSingleRecord('doctors', array('doctor_id' => $pav->doctor_id), $order = '');
            $param['investigations'][$pv]['doctor_name'] = "Dr. " . strtoupper($doctor_deatails->first_name . " " . $doctor_deatails->last_name);
            $param['investigations'][$pv]['status'] = $pav->inv_status;
            $param['investigations'][$pv]['doctor_id'] = $pav->doctor_id;
            $param['investigations'][$pv]['clinic_id'] = $pav->clinic_id;
            $param['investigations'][$pv]['status'] = $pav->status;


            $pv++;
        }
    } else {
        $param['investigations'] = NULL;
    }
    $this->response(array('code' => '200', 'message' => 'Investigations List', 'result' => $param, 'requestname' => $method));
}

public function patient_investigation_view($parameters, $method, $user_id) {
    $investigations_list = $this->db->select("*")->from("patient_investigation_line_items")->where(" patient_investigation_id = '" . $parameters['patient_investigation_id'] . "'")->order_by(" patient_investigation_id","desc")->get()->result();
    $pv1 = 0;
    foreach ($investigations_list as $key => $value) {
        $inv_deatails = $this->Generic_model->getSingleRecord('investigations', array('investigation_id' => $value->investigation_id), $order = '');
        $param['investigations_list'][$pv1]['investigation_id'] = $value->investigation_id;
        $param['investigations_list'][$pv1]['patient_investigation_line_item_id'] = $value->patient_investigation_line_item_id;
        $param['investigations_list'][$pv1]['investigation_code'] = $inv_deatails->investigation_code;
        $param['investigations_list'][$pv1]['investigation_name'] = $inv_deatails->investigation;
        $param['investigations_list'][$pv1]['category'] = $inv_deatails->category;
        $param['investigations_list'][$pv1]['mrp'] = $inv_deatails->mrp;
        $pv1++;
    }
    $this->response(array('code' => '200', 'message' => 'Investigations List', 'result' => $param, 'requestname' => $method));
}

public function inventory_details($parameters, $method, $user_id) {

    $inventory = $this->db->select("*,(pi.quantity - pd.issued_quantity) as total_qty,pi.drug_id as drugs_id")->from("pharmacy_inventory pi")->join("inventory_outward pd","pi.drug_id = pd.drug_id")->join("drug d","d.drug_id = pi.drug_id")->where("pd.clinic_id= '" . $parameters['clinic_id'] . "' HAVING  total_qty <= d.drug_notify_quantity")->get()->result();

    $pv = 0;
    if (count($inventory) > 0) {

        foreach ($inventory as $pav) {

            $data['shortage'][$pv]['drug_id'] = $pav->drugs_id;
            $data['shortage'][$pv]['trade_name'] = $pav->trade_name;
            $data['shortage'][$pv]['salt'] = $pav->salt_id;
            $data['shortage'][$pv]['batch_no'] = $pav->batch_no;
            $data['shortage'][$pv]['expiry_date'] = $pav->expiry_date;
            $data['shortage'][$pv]['composition'] = $pav->composition;
            $data['shortage'][$pv]['formulation'] = $pav->formulation;
            $data['shortage'][$pv]['mrp'] = round($pav->mrp, 2);
            $data['shortage'][$pv]['remaining'] = $pav->total_qty;
            $pv++;
        }
    } else {
        $data['shortage'] = NULL;
    }
    $date = date("Y-m-d");
    $exp_data = $this->db->select("*")->from("pharmacy_inventory p")->join("drug d","d.drug_id = p.drug_id")->where("p.expiry_date <='" . $date . "' ")->get()->result();
    $ed = 0;
    if (count($exp_data) > 0) {

        foreach ($exp_data as $edv) {

            $data['expired'][$ed]['drug_id'] = $edv->drug_id;
            $data['expired'][$ed]['trade_name'] = $edv->trade_name;
            $data['expired'][$ed]['batch_no'] = $edv->batch_no;
            $data['expired'][$ed]['expiry_date'] = $edv->expiry_date;
            $data['expired'][$ed]['composition'] = $edv->composition;
            $data['expired'][$ed]['formulation'] = $edv->formulation;
            $data['expired'][$ed]['mrp'] = round($edv->mrp, 2);

            $ed++;
        }
    } else {
        $data['expired'] = NULL;
    }
    $exp_soon_data = $this->db->select("*")->from("pharmacy_inventory p")->join("drug d","d.drug_id = p.drug_id")->where("p.expiry_date <='" . checkexpiry() . "' ")->get()->result();
    $es = 0;
    if (count($exp_soon_data) > 0) {

        foreach ($exp_soon_data as $esv) {

            $data['expiring_soon'][$es]['drug_id'] = $esv->drug_id;
            $data['expiring_soon'][$es]['trade_name'] = $esv->trade_name;
            $data['expiring_soon'][$es]['batch_no'] = $esv->batch_no;
            $data['expiring_soon'][$es]['expiry_date'] = $esv->expiry_date;
            $data['expiring_soon'][$es]['composition'] = $esv->composition;
            $data['expiring_soon'][$es]['formulation'] = $esv->formulation;
            $data['expiring_soon'][$es]['mrp'] = round($esv->mrp, 2);

            $es++;
        }
    } else {
        $data['expiring_soon'] = NULL;
    }
    $this->response(array('code' => '200', 'message' => 'inventory Details', 'result' => $data, 'requestname' => $method));
}

    // below function is used to retrieve information from patient prescriptions
function patient_prescription_list($parameters, $method, $user_id) {
    extract($parameters);

        // Get list of current day prescriptions for Clinic
    $prescription_info = $this->db->select("PP.*,PP.doctor_id, CONCAT('Dr. ',UPPER(D.first_name),' ',UPPER(D.last_name)) as doctor_name, P.title,P.first_name,P.middle_name,P.last_name,P.umr_no")->from("patient_prescription PP")->join("patients P","PP.patient_id = P.patient_id","left")->join("doctors D","PP.doctor_id = D.doctor_id","left")->where("PP.clinic_id = '" . $clinic_id . "' AND PP.created_date_time like '%" . date('Y-m-d') . "%'")->get()->result();


    if (count($prescription_info) > 0) {

        $i = 0;
        foreach ($prescription_info as $prescription) {

            foreach ($prescription as $key => $value) {
                $prescription_master['prescriptions'][$i][$key] = $value;
            }
            $patient_name = ucwords($prescription->title) . ". " . strtoupper($prescription->first_name . " " . $prescription->middle_name . ' ' . $prescription->last_name);
            $prescription_master['prescriptions'][$i]['patient_name'] = str_replace('  ', ' ', $patient_name);
            $prescription_master['prescriptions'][$i]['doctor_name'] = str_replace('  ', ' ', $prescription->doctor_name);


            $i++;
        }

        $this->response(array('code' => '200', 'message' => 'Prescription List', 'result' => (object) $prescription_master, 'requestname' => $method));
    } else {
        $this->response(array('code' => '400', 'message' => 'No Prescriptions Found'));
    }
}

    // Billing function
public function billing($parameters, $method, $user_id) {
        // get clinic details
    if ($parameters['clinic_id']) {
        $clinic_info = $this->Generic_model->getSingleRecord('clinics', array('clinic_id' => $parameters['clinic_id']));

            // get all printable information
        $invoice_print['clinic_name'] = $clinic_info->clinic_name;
        $invoice_print['clinic_logo'] = $clinic_info->clinic_logo;
        $invoice_print['clinic_emblem'] = $clinic_info->clinic_emblem;
        $invoice_print['clinic_address'] = $clinic_info->clinic_address;
        $invoice_print['clinic_phone'] = $clinic_info->clinic_phone;
    }

    $invoice_print['payment_mode'] = $parameters['payment_mode'];
    $invoice_print['updated_info'] = $parameters['prescription_detail'];

        // check if the person is patient or a guest
    if ($parameters['patient_id']) {
            // get patient details for name and other information for invoice printing
        $patient_info = $this->db->select("title, first_name, last_name, middle_name, umr_no, clinic_id, address_line")->from("patients")->where("patient_id = '" . $parameters['patient_id'] . "'")->get()->row();

        if (count($patient_info) > 0) {
                // patient information
            extract($patient_info);

                // get details for printing the invoice
            $invoice_print['patient_name'] = strtoupper($first_name) . " ". strtoupper($last_name);
            $invoice_print['umr_no'] = $billing_master['umr_no'] = strtoupper($umr_no);
            $invoice_print['patient_address'] = $patient_info['address_line'];

                // get details for billing master
            $billing_master['patient_id'] = $parameters['patient_id'];
            $billing_master['umr_no'] = $patient_info->umr_no;
        }

            // Check if the prescription exists or no
        if ($parameters['patient_prescription_id']) {
                // Get Doctor & Appointment information from prescription id
            $billing_master['patient_prescription_id'] = $parameters['patient_prescription_id'];
            $billing_master['appointment_id'] = $parameters['appointment_id'];
            $billing_master['doctor_id'] = $parameters['doctor_id'];
        }
        } else { // Person is a Guest - Provided guest name and guest mobile no.
            $billing_master['guest_name'] = $parameters['guest_name'];
            $billing_master['guest_mobile'] = $parameters['guest_mobile'];
            $invoice_print['patient_name'] = $parameters['guest_name'];
        }

        // Generate Invoice and Receipt no
        $invoice_no_alias = generate_invoice_no($parameters['clinic_id']);
        $invoice_no = $parameters['clinic_id'].$invoice_no_alias;     

        // $inr = $this->db->select("*")->from("billing")->where("clinic_id =" . $parameters['clinic_id'])->get()->num_rows();
        // $inv_gen = $inr + 1;
        // $receipt_no = 'RCT-' . $parameters['clinic_id'] . '-' . $inv_gen; // receipt no
        // $invoice_no = 'INV-' . $parameters['clinic_id'] . '-' . $inv_gen; // invoice no

        // Enter common params for Billing Master record creation
        $billing_master['clinic_id'] = $parameters['clinic_id'];
        //$billing_master['receipt_no'] = $receipt_no;
        $billing_master['invoice_no'] = $invoice_no;
        $billing_master['invoice_no_alias'] = $invoice_no_alias;
        $billing_master['billing_type'] = $parameters["billing_type"];
        $billing_master['billing_date_time'] = date('Y-m-d H:i:s');
        $billing_master['discount_status'] = $parameters['discount_status'];
        $billing_master['payment_mode'] = $parameters['payment_mode'];
        $billing_master['bank_name'] = ($parameters['bank_name'] != '') ? $parameters['bank_name'] : "";
        $billing_master['cheque_no'] = ($parameters['cheque_no'] != '') ? $parameters['cheque_no'] : "";
        $billing_master['refference_no'] = ($parameters['refference_no'] != '') ? $parameters['refference_no'] : "";
        $billing_master['deposit_date'] = ($parameters['deposit_date'] != '') ? $parameters['deposit_date'] : "";
        $billing_master['deposit_date'] = ($parameters['deposit_date'] != '') ? $parameters['deposit_date'] : "";
        $billing_master['invoice_pdf'] = "INV_" . $parameters['clinic_id'] . "_" . date('dhi') . ".pdf";
        $billing_master['created_by'] = $user_id;
        $billing_master['created_date_time'] = date('Y-m-d H:i:s');
        $billing_master['modified_by'] = $user_id;
        $billing_master['modified_date_time'] = date('Y-m-d H:i:s');

        // echo '<pre>';
        // print_r($parameters);
        // exit();
        // Insert the billing details into billing master and get billing id
        $billing_id = $this->Generic_model->insertDataReturnId('billing', $billing_master);

        // insert prescription drugs - billing line items into patient_prescription_drug table
        for ($i = 0; $i < count($parameters['prescription_detail']); $i++) {
            $billing_line_items['billing_id'] = $billing_id;
            $billing_line_items['item_information'] = str_replace('  ', '', str_replace(' ,', '', ucwords($parameters['prescription_detail'][$i]['trade_name'] . ", " . $parameters['composition']) . ", Batch no: " . strtoupper($parameters['prescription_detail'][$i]['batchNo'])));
            $billing_line_items['batch_number'] = $parameters['prescription_detail'][$i]['batchNo'];
            $billing_line_items['quantity'] = $parameters['prescription_detail'][$i]['quantity'];
            $billing_line_items['discount'] = $parameters['prescription_detail'][$i]['discount'];
            $billing_line_items['amount'] = $parameters['prescription_detail'][$i]['amount'];
            $billing_line_items['created_by'] = $user_id;
            $billing_line_items['modified_by'] = $user_id;
            $billing_line_items['created_date_time'] = date('Y-m-d H:i:s');
            $billing_line_items['modified_date_time'] = date('Y-m-d H:i:s');

            // insert data into inventory outward
            $outward['clinic_id'] = $parameters['clinic_id'];
            $outward['drug_id'] = $parameters['prescription_detail'][$i]['drug_id'];
            $outward['batch_no'] = $parameters['prescription_detail'][$i]['batchNo'];
            $outward['outward_date'] = date('Y-m-d H:i:s');
            $outward['quantity'] = $parameters['prescription_detail'][$i]['quantity'];
            $outward['created_by'] = $user_id;
            $outward['modified_by'] = $user_id;
            $outward['created_date_time'] = date('Y-m-d H:i:s');
            $outward['modified_date_time'] = date('Y-m-d H:i:s');

            // invoice print data
            // insert the record into billing line items
            $ok = $this->Generic_model->insertData('billing_line_items', $billing_line_items);

            if ($ok) {

                // insert records into pharmacy inventory outward
                $okay = $this->Generic_model->insertData('clinic_pharmacy_inventory_outward', $outward);

                if ($okay) {
                    // records inserted successfully

                    $html = $this->load->view('patients/prescription_invoice_test', $invoice_print, true);
                    $pdfFilePath = "INV_" . $patient_details->clinic_id . "_" . date('dhi') . ".pdf";
                    $invoice_print['pdf_name'] = base_url() . 'uploads/prescriptions/' . $pdfFilePath;

                    $this->load->library('M_pdf');
                    $this->m_pdf->pdf->WriteHTML($html);
                    $this->m_pdf->pdf->Output("./uploads/prescriptions/" . $pdfFilePath, "F");

                    $this->response(array('code' => '200', 'message' => 'Billing details inserted successfully', 'result' => $invoice_print, 'requestname' => $method));
                } else {
                    // records inserted failed
                    $this->response(array('code' => '400', 'message' => 'Pharmacy outward insertion failed'));
                }
            } else {
                // billing line items records insertion failed
                $this->response(array('code' => '400', 'message' => 'Billing line items insertion failed'));
            }
        }
    }

    // below function is used to retrieve information from patient prescriptions
    public function prescription_view_submit($parameters, $method, $user_id) {
        $data['patient_prescription_id'] = $parameters['patient_prescription_id'];
        $patient_details = $this->db->select('*')->from("patient_prescription p")->join("patient_prescription_drug pd","p.patient_prescription_id = pd.patient_prescription_id")->where('p.patient_prescription_id = "' . $parameters['patient_prescription_id'].'"')->get()->row();

        $data2['patient_id'] = $patient_details->patient_id;
        $patient_info = $this->db->select('*')->from('patients')->where('patient_id',$patient_details->patient_id)->get()->row();

        $data2['patient_name'] = strtoupper($patient_info->first_name . ' ' . $patient_info->last_name);
        $data2['umr_no'] = $patient_info->umr_no;
        $appointment_info = $this->db->select('*')->from('appointments')->where('appointment_id', $patient_details->appointment_id)->row();

        $doctors_info = $this->db->query('select * from doctors where doctor_id = ' . $appointment_info->doctor_id)->row();
        $data['appointment_id'] = $appointment_info->appointment_id;
        $data['appointment_date'] = $appointment_info->appointment_date;
        $data['doctor_id'] = $doctors_info->doctor_id;
        $data2['doctor_name'] = "Dr. " . strtoupper($doctors_info->first_name . ' ' . $doctors_info->last_name);

        // Generate Invoice and Receipt no
        $invoice_no_alias = generate_invoice_no($patient_details->clinic_id);
        $invoice_no = $patient_details->clinic_id.$invoice_no_alias;     

        // $inr = $this->db->select("*")->from("billing")->where("clinic_id =" . $patient_details->clinic_id)->num_rows();
        // $inv_gen = $inr + 1;
        // $receipt_no = 'RCT-' . $patient_details->clinic_id . '-' . $inv_gen;
        // $invoice_no = 'INV-' . $patient_details->clinic_id . '-' . $inv_gen;

        //$billing_p['receipt_no'] = $receipt_no;
        $billing_p['invoice_no'] = $invoice_no;
        $billing_p['invoice_no_alias'] = $invoice_no_alias;
        $billing_p['patient_id'] = $patient_details->patient_id;
        $billing_p['clinic_id'] = $patient_details->clinic_id;
        $billing_p['umr_no'] = $patient_info->umr_no;
        $billing_p['invoice_pdf'] = "INV_" . $patient_details->clinic_id . "_" . date('dhi') . ".pdf";
        $billing_p['created_by'] = $user_id;
        $billing_p['created_date_time'] = date('Y-m-d H:i:s');
        $billing_p['modified_by'] = $user_id;
        $billing_p['modified_date_time'] = date('Y-m-d H:i:s');

        $billing_id = $this->Generic_model->insertDataReturnId('billing', $billing_p);

// print_r($parameters['prescription_detail']);
        for ($i = 0; $i < count($parameters['prescription_detail']); $i++) {
            $check = $this->db->select('*')->from('patient_prescription_drug')->where('patient_prescription_id = "' . $parameters['patient_prescription_id'] . '"and drug_id = "' . $parameters['prescription_detail'][$i]['drug_id'] . '"')->get()->row();
            if (count($check) == 0) {
                $patient_prescription['patient_prescription_id'] = $parameters['patient_prescription_id'];
                $patient_prescription['drug_id'] = $parameters['prescription_detail'][$i]['drug_id'];
                $patient_prescription['day_schedule'] = 'M';
                $patient_prescription['day_dosage'] = 2;
                $patient_prescription['preffered_intake'] = 'AF';
                $patient_prescription['drug_dose'] = '1/2';
                $patient_prescription['preffered_time_gap'] = '';
                $patient_prescription['dose_course'] = 15;
                $patient_prescription['quantity'] = $parameters['prescription_detail'][$i]['issue_qty'];
                $patient_prescription['checked'] = 1;
                $patient_prescription['status'] = 2;
                $patient_prescription['remarks'] = "test";
                $patient_prescription['created_by'] = $user_id;
                $patient_prescription['modified_by'] = $user_id;
                $patient_prescription['created_date_time'] = date('Y-m-d H:i:s');
                $patient_prescription['modified_date_time'] = date('Y-m-d H:i:s');
                $ok = $this->Generic_model->insertData('patient_prescription_drug', $patient_prescription);
            } else {
                $data['drug_id'] = $parameters['prescription_detail'][$i]['drug_id'];
                $data1['checked'] = 1;
                $data1['status'] = 2;
                $data['quantity'] = $data1['quantity'] = $parameters['prescription_detail'][$i]['issue_qty'];
                $ok = $this->db->query('update patient_prescription_drug SET checked=1,quantity="' . $data['quantity'] . '" where patient_prescription_id = "' . $parameters['patient_prescription_id'] . '" and drug_id="' . $parameters['prescription_detail'][$i]['drug_id'] . '"');
            }

            $patient_bank['billing_id'] = $billing_id;
            $patient_bank['doctor_id'] = $doctors_info->doctor_id;
            $patient_bank['billing_type'] = 'Pharmacy';
            $patient_bank['quantity'] = $parameters['prescription_detail'][$i]['issue_qty'];
            $patient_bank['mode_of_payment'] = 'Cash';
            $patient_bank['billing_date_time'] = date('Y-m-d H:i:s');
            $drug_info = $this->db->query('select * from drug where drug_id =' . $parameters['prescription_detail'][$i]['drug_id'])->row();

            $patient_bank['amount'] = round($drug_info->mrp, 2) * $data['quantity'];
            $patient_bank['item_information'] = $drug_info->trade_name;
            $patient_bank['created_date_time'] = date('Y-m-d H:i:s');
            $patient_bank['modified_date_time'] = date('Y-m-d H:i:s');
            $this->Generic_model->insertData('billing_line_items', $patient_bank);

//inventory outwards insert

            $inventory['patient_id'] = $patient_details->patient_id;
            $inventory['prescription_id'] = $parameters['patient_prescription_id'];
            $inventory['doctor_id'] = $doctors_info->doctor_id;
            $inventory['outward_date'] = date('Y-m-d');
            $inventory['clinic_id'] = $patient_details->clinic_id;
            $inventory['drug_id'] = $parameters['prescription_detail'][$i]['drug_id'];
            $drug_inventory = $this->db->query('select * from pharmacy_inventory where status = 1 and drug_id = ' . $parameters['prescription_detail'][$i]['drug_id'])->row();
            $inventory['expiry_date'] = $drug_inventory->expiry_date;
            $inventory['batch_no'] = $drug_inventory->batch_no;
            $inventory['issued_quantity'] = $parameters['prescription_detail'][$i]['issue_qty'];
            $inventory['status'] = 1;
            $inventory['created_by'] = $user_id;
            $inventory['modified_by'] = $user_id;
            $inventory['created_date_time'] = date('Y-m-d H:i:s');
            $inventory['modified_date_time'] = date('Y-m-d H:i:s');
            $this->Generic_model->insertData('inventory_outward', $inventory);
        }
        $qualification = $this->db->query('select * from doctors where doctor_id = ' . $doctors_info->doctor_id)->row();
        $data2['doctor_name'] = "Dr. " . strtoupper($doctors_info->first_name . " " . $doctors_info->last_name);
        $data2['qualification'] = $doctors_info->qualification;
        $address = $this->db->query('select * from clinics where clinic_id = ' . $patient_details->clinic_id)->row();
        $data2['clinic_address'] = $address->address;
        $data2['clinic_name'] = $address->clinic_name;
        $data2['clinic_logo'] = $address->clinic_logo;
        $data2['clinic_phone'] = $address->clinic_phone;
        $billing_info = $this->db->query('select * from billing where billing_id = ' . $billing_id)->row();
        $data2['invoice_number'] = $billing_info->invoice_no;
        $data2['receipt_no'] = $billing_info->receipt_no;

        $patients = $this->db->query('select address_line from patients where patient_id = ' . $patient_details->patient_id)->row();
        $data2['patient_address'] = $patients->address_line;
        $data2['mode_of_payment'] = 'Cash';
        $data2['updated_info'] = $parameters['prescription_detail'];

        $html = $this->load->view('patients/prescription_invoice_test', $data2, true);
        $pdfFilePath = "INV_" . $patient_details->clinic_id . "_" . date('dhi') . ".pdf";
        $data3['pdf_name'] = base_url() . 'uploads/prescriptions/' . $pdfFilePath;

        $this->load->library('M_pdf');
        $stylesheet  = '';
        $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");
        $this->m_pdf->pdf->autoScriptToLang = true;
        $this->m_pdf->pdf->autoLangToFont = true;

        $this->m_pdf->pdf->shrink_tables_to_fit = 1;
        $this->m_pdf->pdf->setAutoTopMargin = "stretch";
        $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
        $this->m_pdf->pdf->defaultheaderline = 0;


        $this->m_pdf->pdf->WriteHTML($stylesheet,1);
        $this->m_pdf->pdf->WriteHTML($html,2);
        $this->m_pdf->pdf->Output("./uploads/prescriptions/" . $pdfFilePath, "F");
        if ($ok) {
            $this->response(array('code' => '200', 'message' => 'Invoice Generated Successfully', 'result' => $data3, 'requestname' => $method));
        } else {
            $this->response(array('code' => '400', 'message' => 'not updated'));
        }
    }

    public function save_custom_form_bcp($parameters, $method, $user_id) {

        $form_master_insert['form_id'] = $parameters['form_id'];
        $form_master_insert['patient_id'] = $parameters['patient_id'];
        $form_master_insert['doctor_id'] = $parameters['doctor_id'];
        $form_master_insert['form_type'] = $parameters['form_type'];
        $form_master_insert['appointment_id'] = $parameters['appointment_id'];
        $form_master_insert['created_date_time'] = date('Y-m-d H:i:s');
        $form_master_insert['modified_date_time'] = date('Y-m-d H:i:s');
        $form_data = $parameters['labels'];

        $patient_form_master_id = $this->Generic_model->insertDataReturnId('patient_form', $form_master_insert);

        for ($v = 0; $v < count($form_data); $v++) {
            $form_line_items['field_id'] = $form_data[$v]['label_id'];
            $form_line_items['field_value'] = $form_data[$v]['label_value'];
            $form_line_items['option_id'] = $form_data[$v]['option_id'];
            $form_line_items['option_value'] = $form_data[$v]['option_value'];
            $form_line_items['parent_field_id'] = $form_data[$v]['parent_label_id'];
            $form_line_items['created_date_time'] = date('Y-m-d H:i:s');
            $form_line_items['modified_date_time'] = date('Y-m-d H:i:s');
            $form_line_items['patient_form_id'] = $patient_form_master_id;
            $form_line_items['row_index'] = $form_data[$v]['row_index'];
            $form_line_items['column_index'] = $form_data[$v]['column_index'];
            $form_line_items['format'] = $form_data[$v]['format'];
            $form_line_items['row_title'] = $form_data[$v]['row_title'];

            $patient_form_id = $this->Generic_model->insertDataReturnId('patient_form_line_items', $form_line_items);
        }

        $this->response(array('code' => '200', 'message' => 'form Details Saved Successfully', 'result' => $param, 'requestname' => $method));
    }

    public function getSaveddata($parameters, $method, $user_id) {

        $gform_data = $this->db->select("b.format")->from("patient_form a")->join("patient_form_line_items b","a.patient_form_id=b.patient_form_id")->where("form_type ='" . $parameters['form_type'] . "' and patient_id='" . $parameters['patient_id'] . "'")->group_by(array("b.format","b.created_date_time"))->get()->result();
        $k = 0;
        foreach ($gform_data as $result) {

            $data['forms'][$k]['form_type'] = $result->format;
            if ($result->format == 'normal') {

                $fom_data = $this->db->select("GROUP_CONCAT(option_value) as option_value,b.parent_field_id,b.field_value,b.field_id,b.format,b.row_title,b.row_index,b.column_index")->from(" patient_form a")->join("patient_form_line_items b","a.patient_form_id=b.patient_form_id")->where("form_type ='" . $parameters['form_type'] . "' and patient_id='" . $parameters['patient_id'] . "' and b.format='" . $result->format . "'")->group_by(array("field_id","parent_field_id","b.patient_form_id"))->get()->result_array();
                $i = 0;
                foreach ($fom_data as $key => $value) {

                    $data['forms'][$k]['labels'][$i]['label_name'] = $value['field_value'];
                    $data['forms'][$k]['labels'][$i]['label_value'] = $value['option_value'];
                    $data['forms'][$k]['labels'][$i]['label_id'] = $value['field_id'];
                    $data['forms'][$k]['labels'][$i]['parent_label_id'] = $value['parent_field_id'];
                    $data['forms'][$k]['labels'][$i]['format'] = $value['format'];
                    $data['forms'][$k]['labels'][$i]['row_title'] = $value['row_title'];
                    $data['forms'][$k]['labels'][$i]['row_index'] = $value['row_index'];
                    $data['forms'][$k]['labels'][$i]['column_index'] = $value['column_index'];
                    $i++;
                }
            } else {
                $fom_data_title = $this->db->select("DISTINCT(row_title)")->from("patient_form a")->join("patient_form_line_items b","a.patient_form_id=b.patient_form_id")->where("form_type ='" . $parameters['form_type'] . "' and patient_id='" . $parameters['patient_id'] . "' and b.format='" . $result->format . "'")->get()->result();
                $j = 0;
                foreach ($fom_data_title as $tresult) {
                    $fom_data = $this->db->select("GROUP_CONCAT(option_value) as option_value,b.parent_field_id,b.field_value,b.field_id,b.format,b.row_title,b.row_index,b.column_index")->from(" patient_form a")->join("patient_form_line_items b","a.patient_form_id=b.patient_form_id")->where("form_type ='" . $parameters['form_type'] . "' and patient_id='" . $parameters['patient_id'] . "' and b.format='" . $result->format . "' and row_title='" . $tresult->row_title . "'")->group_by(" field_id,parent_field_id,b.patient_form_id")->get()->result_array();
                    $i = 0;
                    $data['forms'][$k]['rows'][$j]['row_title'] = $tresult->row_title;
                    foreach ($fom_data as $key => $value) {
                        $data['forms'][$k]['rows'][$j]['labels'][$i]['label_name'] = $value['field_value'];
                        $data['forms'][$k]['rows'][$j]['labels'][$i]['label_value'] = $value['option_value'];
                        $data['forms'][$k]['rows'][$j]['labels'][$i]['label_id'] = $value['field_id'];
                        $data['forms'][$k]['rows'][$j]['labels'][$i]['parent_label_id'] = $value['parent_field_id'];
                        $data['forms'][$k]['rows'][$j]['labels'][$i]['format'] = $value['format'];
                        $data['forms'][$k]['rows'][$j]['labels'][$i]['row_title'] = $value['row_title'];
                        $data['forms'][$k]['rows'][$j]['labels'][$i]['row_index'] = $value['row_index'];
                        $data['forms'][$k]['rows'][$j]['labels'][$i]['column_index'] = $value['column_index'];
                        $i++;
                    }
                    $j++;
                }
            }
            $k++;
        }
        $this->response(array('code' => '200', 'message' => 'form_details', 'result' => $data, 'requestname' => $method));
    }

    //list of consent forms w.r.t to department
    public function getConsentforms($parameters, $method, $user_id) {

        $consent_forms = $this->db->select("*")->from("consent_form a")->join("consent_form_department b","a.consent_form_id = b.consent_form_id")->where("b.department_id='" . $parameters['department_id'] . "'")->get()->result();
        
        $i = 0;
        foreach ($consent_forms as $cform) {
            $dept_info = $this->db->query("select * from department where department_id='" . $cform->department_id . "'")->row();
            $para['consent_form'][$i]['department_name'] = $dept_info->department_name;
            $para['consent_form'][$i]['consent_form_id'] = $cform->consent_form_id;
            $para['consent_form'][$i]['consent_form_title'] = $cform->consent_form_title;
            $i++;
        }

        $this->response(array('code' => '200', 'message' => 'Consent Form Details ', 'result' => $para, 'requestname' => $method));
    }

    //adding consent form and checklist to patient add generating consent form pdf
    public function downloadConsentform($parameters, $method, $user_id) {
        $appointment_id = $parameters['appointment_id'];
        $patient_id = $parameters['patient_id'];
        $umr_no = $parameters['umr_no'];
        $clinic_id = $parameters['clinic_id'];
        $doctor_id = $user_id;
        $consent_form_id = $parameters['consent_form_id'];
        $data['appointment'] = $this->db->select("a.*,d.salutation,d.first_name,d.last_name,d.department_id,de.department_name,de.department_id,p.patient_id,p.title,p.first_name as pf_name,p.middle_name as pm_name,p.last_name as pl_name,p.umr_no,p.age,p.age_unit,p.gender as p_gender,c.clinic_id,c.clinic_name")
        ->from("appointments a")
        ->join("doctors d","a.doctor_id = d.doctor_id","left")
        ->join("department de","d.department_id=de.department_id","left")->join("patients p","a.patient_id=p.patient_id","left")
        ->join("clinics c","a.clinic_id=c.clinic_id","left")
        ->where("a.appointment_id='" . $appointment_id . "'")->order_by("a.appointment_id","desc")->get()->row();

        $data['Consentform_val'] = $this->db->select("*,a.status")->from("consent_form a")->join(" consent_form_department c","c.consent_form_id = a.consent_form_id")->join("department b","c.department_id = b.department_id")->where("a.archieve != 1 and a.consent_form_id ='" . $consent_form_id . "'")->get()->row();
        $data['consent_form_id'] = $id[0];
        $html = $this->load->view('consentform/consentform_patient_pdf', $data, true);
        $pdfFilePath = $clinic_id."_".$patient_id."_".$appointment_id."_".date("dmy") . ".pdf";
        $this->load->library('M_pdf');
        $stylesheet  = '';
        //$stylesheet .= file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
        $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");
        $this->m_pdf->pdf->SetFont('timesnewroman');
        $this->m_pdf->pdf->WriteHTML($stylesheet,1);
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output("./uploads/consentforms/" . $pdfFilePath, "F");

        $para['pdf_name'] = base_url() . 'uploads/consentforms/' . $pdfFilePath;

        $cf['consent_form_id'] = $consent_form_id;
        $cf['appointment_id'] = $appointment_id;
        $cf['patient_id'] = $patient_id;
        $cf['umr_no'] = $umr_no;
        $cf['clinic_id'] = $clinic_id;
        $cf['doctor_id'] = $doctor_id;
        $cf['patient_consent_form'] = $pdfFilePath;
        $cf['created_by'] = $user_id;
        $cf['modified_by'] = $user_id;
        $cf['created_date_time'] = date('Y-m-d H:i:s');
        $cf['modified_date_time'] = date('Y-m-d H:i:s');
        $pcf_id = $this->Generic_model->insertDataReturnId('patient_consent_forms', $cf);

        $clist = $this->db->select("*")->from("checklist_consent_form cf")->join("checklist_master cm","cf.checklist_id = cm.checklist_id")->where("cf.patient_consent_form_id='" . $consent_form_id . "' order by cf.position")->get()->result();
        $i = 0;
        foreach ($clist as $cvalue) {
            $insert_checklist['patient_consent_form_id'] = $pcf_id;
            $insert_checklist['checklist_id'] = $cvalue->checklist_id;
            $insert_checklist['doctor_id'] = $doctor_id;
            $insert_checklist['appointment_id'] = $appointment_id;
            $insert_checklist['category'] = strtolower($cvalue->category);
            $insert_checklist['created_by'] = $user_id;
            $insert_checklist['status'] = 1;
            $insert_checklist['modified_by'] = $user_id;
            $insert_checklist['created_date_time'] = date('Y-m-d H:i:s');
            $insert_checklist['modified_date_time'] = date('Y-m-d H:i:s');
            $this->Generic_model->insertData('patient_checklist', $insert_checklist);
            $i++;
        }

        $this->response(array('code' => '200', 'message' => 'PDF File', 'result' => $para, 'requestname' => $method));
    }

    //list of patient consent forms
    public function patientConsentForms($parameters, $method, $user_id) {
        $appointment_id = $parameters['appointment_id'];
        $patient_id = $parameters['patient_id'];

        $patient_consent_form = $this->db->select("pc.*,cf.consent_form_id as cf_id,cf.consent_form_title,d.doctor_id,d.salutation,d.first_name,d.last_name")->from("patient_consent_forms pc")->join("consent_form cf","pc.consent_form_id=cf.consent_form_id","left")->join("doctors d","pc.doctor_id=d.doctor_id","left")->where("pc.patient_id = '" . $patient_id . "'")->get()->result();
        $cf = 0;
        if (count($patient_consent_form) > 0) {
            foreach ($patient_consent_form as $pcf) {
                $para['patient_consent'][$cf]['appointment_id'] = $appointment_id;
                $para['patient_consent'][$cf]['patient_consent_form_id'] = $pcf->patient_consent_form_id;
                $para['patient_consent'][$cf]['consent_form_id'] = $pcf->cf_id;
                $para['patient_consent'][$cf]['consent_form_title'] = $pcf->consent_form_title;
                $para['patient_consent'][$cf]['consent_form_date_time'] = $pcf->created_date_time;
                $para['patient_consent'][$cf]['doctor'] = $pcf->salutation . " " . $pcf->first_name . " " . $pcf->last_name;
                $para['patient_consent'][$cf]['patient_consent_form'] = base_url() . 'uploads/consentforms/' . $pcf->patient_consent_form;

                $patient_consent_form_lines = $this->Generic_model->getSingleRecord("patient_consentform_line_items", array("patient_consent_form_id" => $pcf->patient_consent_form_id), $order = "");

                if (count($patient_consent_form_lines) > 0) {
                    $pcfl_i = $patient_consent_form_lines->patient_consent_form_image;
                    $pcfl_img = explode(",", $pcfl_i);
                    for ($cfl = 0; $cfl < count($pcfl_img); $cfl++) {
                        $para['patient_consent'][$cf]['pcf_line_items'][$cfl]['patient_consent_form_image'] = base_url() . 'uploads/patient_consentforms/' . $pcfl_img[$cfl];
                    }
                } else {
                    $para['patient_consent'][$cf]['pcf_line_items'] = array();
                }

                $cf++;
            }
        } else {
            $para['patient_consent'] = array();
        }
        $this->response(array('code' => '200', 'message' => 'Patient Consent Forms', 'result' => $para, 'requestname' => $method));
    }

    public function patient_summary_list($parameters, $method, $user_id) {
        $patient_id = $parameters['patient_id'];
        $appointments = $this->db->select("a.appointment_id,a.course_in_hospital, a.appointment_date, d.doctor_id, d.salutation, d.first_name, d.last_name, de.department_id, de.department_name")->from("appointments a")->join("doctors d","a.doctor_id=d.doctor_id","left")->join("department de","d.department_id=de.department_id","left")->where("a.patient_id='" . $patient_id . "' and a.appointment_date <= '".date('Y-m-d')."'")->order_by("a.appointment_date","desc")->get()->result();

        if (count($appointments) > 0) {
            $a = 0;
            foreach ($appointments as $app) {
                $para['pa_appointment'][$a]['appointment_id'] = $app->appointment_id;
                if($app->course_in_hospital!="" || $app->course_in_hospital!=NULL){
                    $para['pa_appointment'][$a]['discharge_status'] = 1;
                }
                else{
                   $para['pa_appointment'][$a]['discharge_status'] = 0;   
               }
               $para['pa_appointment'][$a]['appointment_date'] = $app->appointment_date;
               $para['pa_appointment'][$a]['doctor'] = $app->salutation . " " . $app->first_name . " " . $app->last_name;
               $para['pa_appointment'][$a]['department'] = $app->department_name;
               $a++;
           }
       } else {
        $para['pa_appointment'] = array();
    }
    $this->response(array('code' => '200', 'message' => 'Appointments Of patients', 'result' => $para, 'requestname' => $method));
}


    //generating patient summary report in pdf format
public function shortSummary($parameters, $method, $user_id) {

    $appointment_id = $parameters['appointment_id'];
        //$data['visit']=$visit;
    $data['appointments'] = $this->db->select("a.*, c.clinic_name,c.clinic_logo,c.address,c.clinic_phone,p.title,p.first_name as pname,p.last_name as plname,p.gender,p.age,p.address_line,p.umr_no,p.preferred_language,p.mobile,p.qrcode,d.salutation,d.first_name as dfname,d.last_name as dlname, d.qualification, d.registration_code, dep.department_name")
    ->from("appointments a")->join("clinics c","a.clinic_id = c.clinic_id","left")->join("patients p","a.patient_id = p.patient_id","left")->join("doctors d","a.doctor_id = d.doctor_id","left")->join('department dep','d.department_id = dep.department_id')->where("a.appointment_id='" . $appointment_id . "'")->get()->row();

    $patient_name = $data['appointments']->pname . date('Ymd').$appointment_id;

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

        $data['patient_clinical_diagnosis'] = $this->Generic_model->getAllRecords("patient_cd_line_items", array('patient_clinical_diagnosis_id'=>$pcd->patient_clinical_diagnosis_id), $order = '');

        $data['patient_investigations'] = $this->db->select("*")->from("patient_investigation_line_items pil")->join("investigations inv","pil.investigation_id=inv.investigation_id")->join("patient_investigation pi","pil.patient_investigation_id=pi.patient_investigation_id")->where(" pi.appointment_id='".$appointment_id."'")->get()->result();

        $data['patient_prescription'] = $this->Generic_model->getSingleRecord('patient_prescription',array('appointment_id'=>$appointment_id),'');

        
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



        
        $data['pdf_settings'] = $pdf_settings = $this->db->select("*")->from("clinic_pdf_settings")->where("clinic_id='".$data['appointments']->clinic_id."'")->get()->row();

        
        $this->load->library('M_pdf');
        $html = $this->load->view('reports/short_summary_reports_pdf', $data, true);
        $pdfFilePath = strtolower(str_replace(" ", "_", $patient_name)) . ".pdf";
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
    $this->m_pdf->pdf->Output("./uploads/summary_reports/short-" . $pdfFilePath, "F");
    //$this->m_pdf->pdf->Output("./uploads/summary_reports/".$pdfFilePath, "D");
    $para['pdf_name'] = base_url() . 'uploads/summary_reports/short-' . $pdfFilePath;

    $this->response(array('code' => '200', 'message' => 'PDF File', 'result' => $para, 'requestname' => $method));
}

    //generating patient discharge summary report in pdf format
public function dischargeSummary($parameters, $method, $user_id) {

    $appointment_id = $parameters['appointment_id'];
        //$data['visit']=$visit;
    $data['appointments'] = $this->db->select("a.*, c.clinic_name,c.clinic_logo,c.address,c.clinic_phone,p.first_name as pname,p.gender,p.age,p.address_line,p.umr_no,p.preferred_language,p.mobile,p.qrcode,d.salutation,d.first_name as dfname,d.last_name as dlname")
    ->from("appointments a")->join("clinics c","a.clinic_id = c.clinic_id","left")->join("patients p","a.patient_id = p.patient_id","left")->join("doctors d","a.doctor_id = d.doctor_id","left")->where("a.appointment_id='" . $appointment_id . "'")->get()->row();
    $patient_name = $data['appointments']->pname . date('Ymd').$appointment_id;

    $visit_no = $this->Generic_model->getAllRecords('appointments',array('clinic_id'=>$data['appointments']->clinic_id,'patient_id'=>$data['appointments']->patient_id,'doctor_id'=>$data['appointments']->doctor_id),array('field'=>'appointment_id','type'=>'desc'));


    $visit_count = count($visit_no);

    foreach ($visit_no as $key => $value) {
        if ($value->appointment_id == $appointment_id) {
            $visit_count--;
            $data['visit'] = $visit_count;
        }
    }
    $vital_sign =  $this->Generic_model->getAllRecords('patient_vital_sign',array('appointment_id'=>$appointment_id),array('field'=>'position','type'=>'asc'));


    foreach ($vital_sign as $key => $value) {
        if($value->vital_sign=='DBP'){
            $dbp = $value->vital_result;
        } else if($value->vital_sign=='SBP'){
            $data['vital_sign']['BP'] = $value->vital_result.'/'.$dbp. ' mmHg';
        }else{
            $v_unit = $this->db->query("SELECT * FROM `vital_sign` where short_form='".$value->vital_sign."'")->row();
            $data['vital_sign'][$value->vital_sign] = $value->vital_result." ".$v_unit->unit;   
        }
    }

    $pcd = $this->Generic_model->getSingleRecord('patient_clinical_diagnosis',array('appointment_id'=>$appointment_id),'');

    $data['patient_clinical_diagnosis'] = $this->Generic_model->getAllRecords("patient_cd_line_items", array('patient_clinical_diagnosis_id'=>$pcd->patient_clinical_diagnosis_id), $order = '');

    $data['patient_investigations'] = $this->db->select("*")->from("patient_investigation_line_items pil")->join("investigations inv","pil.investigation_id=inv.investigation_id")->join("patient_investigation pi","pil.patient_investigation_id=pi.patient_investigation_id")->where(" pi.appointment_id='".$appointment_id."'")->get()->result();

    $data['patient_prescription'] = $this->Generic_model->getSingleRecord('patient_prescription',array('appointment_id'=>$appointment_id),'');


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
    



    $data['pdf_settings'] = $pdf_settings = $this->db->select("*")->from("clinic_pdf_settings")->where("clinic_id='".$data['appointments']->clinic_id."'")->get()->row();


    $this->load->library('M_pdf');
    $html = $this->load->view('reports/discharge_summary_reports_pdf', $data, true);
    $pdfFilePath = strtolower(str_replace(" ", "_", $patient_name)) . ".pdf";
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
    $this->m_pdf->pdf->Output("./uploads/summary_reports/discharge-" . $pdfFilePath, "F");
    //$this->m_pdf->pdf->Output("./uploads/summary_reports/".$pdfFilePath, "D");
    $para['pdf_name'] = base_url() . 'uploads/summary_reports/discharge-' . $pdfFilePath;

    $this->response(array('code' => '200', 'message' => 'PDF File', 'result' => $para, 'requestname' => $method));
}

    //generating patient summary report in pdf format
public function fullSummary($parameters, $method, $user_id) {

    $appointment_id = $parameters['appointment_id'];

    $data['appointments'] = $this->db->select("a.*, c.clinic_name,c.clinic_logo,c.address,c.clinic_phone,p.title, p.first_name as pname, p.last_name as plname,p.gender,p.age,p.address_line,p.umr_no,p.preferred_language,p.mobile,p.qrcode,d.salutation,d.first_name as dfname,d.last_name as dlname, d.qualification, d.registration_code, dep.department_name")
    ->from("appointments a")->join("clinics c","a.clinic_id = c.clinic_id","left")->join("patients p","a.patient_id = p.patient_id","left")->join("doctors d","a.doctor_id = d.doctor_id","left")->join('department dep','d.department_id = dep.department_id')->where("a.appointment_id='" . $appointment_id . "'")->get()->row();
    $patient_name = $data['appointments']->pname . date('Ymd').$appointment_id;

    $visit_no = $this->Generic_model->getAllRecords('appointments',array('clinic_id'=>$data['appointments']->clinic_id,'patient_id'=>$data['appointments']->patient_id,'doctor_id'=>$data['appointments']->doctor_id),array('field'=>'appointment_id','type'=>'desc'));


    $visit_count = count($visit_no);

    foreach ($visit_no as $key => $value) {
        if ($value->appointment_id == $appointment_id) {
            $visit_count--;
            $data['visit'] = $visit_count;
        }
    }

    $vital_sign = $this->db->query("SELECT patient_vital_id, appointment_id, clinic_id, patient_id, umr_no, vital_sign, vital_result, sign_type, position, vital_sign_recording_date_time  from patient_vital_sign WHERE vital_sign_recording_date_time IN (SELECT MAX(vital_sign_recording_date_time) AS date FROM patient_vital_sign WHERE appointment_id = ".$appointment_id.") ORDER BY position ASC , vital_sign_recording_date_time DESC")->result_object();

    $sbp = '';

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

        $data['patient_clinical_diagnosis'] = $this->Generic_model->getAllRecords("patient_cd_line_items", array('patient_clinical_diagnosis_id'=>$pcd->patient_clinical_diagnosis_id), $order = '');

        $data['patient_investigations'] = $this->db->select("*")->from("patient_investigation_line_items pil")->join("investigations inv","pil.investigation_id=inv.investigation_id")->join("patient_investigation pi","pil.patient_investigation_id=pi.patient_investigation_id")->where(" pi.appointment_id='".$appointment_id."'")->get()->result();

        $data['patient_prescription'] = $this->Generic_model->getSingleRecord('patient_prescription',array('appointment_id'=>$appointment_id),'');

        
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

            // Previous Documents
        $data['previous_documents'] = $this->db->select("*")->from("previous_documents")->where("appointment_id='".$appointment_id."'")->order_by("previous_document_id","desc")->get()->result();

            //presenting symtoms
        $data['presenting_symptoms'] = $this->db->select("*")->from("patient_presenting_symptoms ps")->join("patient_ps_line_items psl","ps.patient_presenting_symptoms_id = psl.patient_presenting_symptoms_id")->where("appointment_id = '" . $appointment_id. "'")->get()->result();


            // Get Patient's Systemic Examination Form Info
        $data['get_se_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='".$appointment_id."' and form_type='Systemic Examination'")->get()->row();
        $data['systemic_examination_data'] = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$data['get_se_info']->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
        $data['get_se_form']= $this->db->select("form_name")->from("form")->where("form_id='".$data['get_se_info']->form_id."'")->get()->row();


             // Get Patient's General Physical Examination info
        $data['get_gpe_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='".$appointment_id."' and form_type='GPE'")->get()->row();
        $data['gpe_data'] = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$data['get_gpe_info']->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
        $data['get_gpe_form']= $this->db->select("form_name")->from("form")->where("form_id='".$data['get_gpe_info']->form_id."'")->get()->row();

              // Get Patient's HOPI info
        $data['get_hopi_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='".$appointment_id."' and form_type='HOPI'")->get()->row();
        $data['hopi_data'] = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$data['get_hopi_info']->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
        $data['get_hopi_form']= $this->db->select("form_name")->from("form")->where("form_id='".$data['get_hopi_info']->form_id."'")->get()->row();

            // Get Patient's Past History info
        $data['get_past_history_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='".$appointment_id."' and form_type='Past History'")->get()->row();
        $data['past_history_data'] = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$data['get_past_history_info']->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
        $data['get_past_history_form']= $this->db->select("form_name")->from("form")->where("form_id='".$data['get_past_history_info']->form_id."'")->get()->row();

            // Get Patient's Past History info
        $data['get_personal_history_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='".$appointment_id."' and form_type='Present History'")->get()->row();
        $data['personal_history_data'] =$this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$data['get_personal_history_info']->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
        $data['get_personal_history_form']= $this->db->select("form_name")->from("form")->where("form_id='".$data['get_personal_history_info']->form_id."'")->get()->row();

            // Get Patient's Treatment History info
        $data['get_treatment_history_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='".$appointment_id."' and form_type='Treatment History'")->get()->row();
        $data['treatment_history_data'] = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$data['get_treatment_history_info']->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
        $data['get_treatment_history_form']= $this->db->select("form_name")->from("form")->where("form_id='".$data['get_treatment_history_info']->form_id."'")->get()->row();

            // Get Patient's Treatment History info
        $data['get_family_history_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='".$appointment_id."' and form_type='Family History'")->get()->row();
        $data['family_history_data'] =  $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$data['get_family_history_info']->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
        $data['get_family_history_form']= $this->db->select("form_name")->from("form")->where("form_id='".$data['get_family_history_info']->form_id."'")->get()->row();

            // Get Patient's Social History info
        $data['get_social_history_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='".$appointment_id."' and form_type='Social History'")->get()->row();
        $data['social_history_data'] = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$data['get_social_history_info']->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
        $data['get_social_history_form']= $this->db->select("form_name")->from("form")->where("form_id='".$data['get_social_history_info']->form_id."'")->get()->row();

            // Get Patient's Other History info
        $data['get_other_systems_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='".$appointment_id."' and form_type='Other Systems'")->get()->row();
        $data['other_systems_data'] = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$data['get_other_systems_info']->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
        $data['get_other_systems_form']= $this->db->select("form_name")->from("form")->where("form_id='".$data['get_other_systems_info']->form_id."'")->get()->row();


            //Get invoice information
        $data['get_billing_info'] = $this->db->select("*")->from("billing")->where("appointment_id='".$appointment_id."'")->get()->result();
        
        
        $data['pdf_settings'] = $pdf_settings = $this->db->select("*")->from("clinic_pdf_settings")->where("clinic_id='".$data['appointments']->clinic_id."'")->get()->row();
        
        $this->load->library('M_pdf');
        $html = $this->load->view('reports/full_summary_reports_pdf', $data, true);
        $pdfFilePath = strtolower(str_replace(" ", "_", $patient_name)) . ".pdf";
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

    $this->m_pdf->pdf->Output("./uploads/summary_reports/full-" . $pdfFilePath, "F");
    //$this->m_pdf->pdf->Output("./uploads/summary_reports/".$pdfFilePath, "D");
    $para['pdf_name'] = base_url() . 'uploads/summary_reports/full-' . $pdfFilePath;

    $this->response(array('code' => '200', 'message' => 'PDF File', 'result' => $para, 'requestname' => $method));
}


public function getSystemExamination($parameters, $method, $user_id) {
    $data1['para'] = $this->db->select("form_id,form_name,form_type,department_id")->from("form")->where("form_type='" . $parameters['form_type'] . "'")->get()->result();

    $i = 0;
    foreach ($data1['para'] as $key => $value) {
        $res = $this->db->select("count(*) as cnt")->from("patient_form")->where("form_id='" . $value->form_id . "' and patient_id='" . $parameters['patient_id'] . "'")->get()->row();

        $data['para'][$i]['form_id'] = $value->form_id;
        $data['para'][$i]['form_name'] = $value->form_name;
        $data['para'][$i]['form_type'] = $value->form_type;
        $data['para'][$i]['department_id'] = $value->department_id;
        if ($res->cnt > 0) {
            $data['para'][$i]['status'] = 1;
        } else {
            $data['para'][$i]['status'] = 0;
        }
        $i++;
    }
    $this->response(array('code' => '200', 'message' => 'Systemic Examination Forms', 'result' => $data, 'requestname' => $method));
}

public function custom_form_recursive_systemic($parameters, $method, $user_id) {
    $condition = array('form_type' => $parameters['form_type']);
    $form = $this->Generic_model->getAllRecords("form", $condition = '', $order = '');

    $sections = $this->db->select("b.form_id,b.form_name,b.form_type,a.section_id,a.format_type,a.title,a.brief ,a.section_image")->from("section a")->join("form b","a.form_id=b.form_id")->where("b.form_type='" . $parameters['form_type'] . "' and b.form_id='" . $parameters['form_id'] . "' and parent_section_id = 0")->order_by("a.section_id ASC")->get()->result();

    $a = 0;
    foreach ($sections as $key => $pvalue) {
        $data['form']['formType'] = $pvalue->form_type;
        $data['form']['formName'] = $pvalue->form_name;
        $data['form']['form_id'] = $pvalue->form_id;
        $data['form']['sections'][$a]['section_id'] = $pvalue->section_id;
        if ($pvalue->section_image != "" || $pvalue->section_image != NULL) {
            $data['form']['sections'][$a]['image_path'] = base_url() . "/uploads/section_images/" . $pvalue->section_image;
        } else {
            $data['form']['sections'][$a]['image_path'] = "";
        }

        $data['form']['sections'][$a]['title'] = $pvalue->title;
        $data['form']['sections'][$a]['brief'] = $pvalue->brief;
        $data['form']['sections'][$a]['textbox'] = 1;
        $data['form']['sections'][$a]['collapse'] = 0;

        $sub_sections = $this->db->select("b.form_id,b.form_name,b.form_type,a.section_id,a.format_type,a.title,a.brief")->from("section a")->join("form b","a.form_id=b.form_id")->where("b.form_type='" . $parameters['form_type'] . "' and b.form_id='" . $parameters['form_id'] . "' and parent_section_id='" . $pvalue->section_id . "'")->order_by("a.section_id")->get()->result();

        $i = 0;
        foreach ($sub_sections as $key => $value) {
            $data['form']['sections'][$a]['sub_sections'][$i]['sub_section_id'] = $value->section_id;
            $data['form']['sections'][$a]['sub_sections'][$i]['title'] = $value->title;
            $data['form']['sections'][$a]['sub_sections'][$i]['format'] = $value->format_type;

            if ($value->format_type == 'tabular') {
                $label_result = $this->db->select("*")->from("field")->where("row_index IS NOT NULL and column_index IS NOT NULL and section_id='" . $value->section_id . "' and parent_field_id='0' and parent_option_id='0'")->group_by("row_index")->order_by("field_id")->get()->result();

                $j = 0;
                $di = 0;
                $array_dup = array();
                foreach ($label_result as $key2 => $value2) {
                    $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['row_title'] = strtok($value2->field_name, '_');
                    $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['row_index'] = $value2->row_index;
                    $row_elements = $this->db->select("*")->from("field")->where("section_id='" . $value->section_id . "' and row_index='" . $value2->row_index."'")->order_by("field_id","asc")->get()->result();


                    $k = 0;
                    foreach ($row_elements as $key => $rresult) {
                        $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['id'] = $rresult->field_id;
                        $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['widgetType'] = $rresult->field_type;
                        $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['labelText'] = substr($rresult->field_name, (strpos($rresult->field_name, '_') ?: -1) + 1);
                        $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['row_index'] = $rresult->row_index;
                        $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['column_index'] = $rresult->column_index;
                        $options = $this->db->select("*")->from("field_option")->where("field_id='" . $rresult->field_id."'")->order_by("field_id","ASC")->get()->result();
                        $l = 0;
                        foreach ($options as $okey => $oresult) {
                            $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['optionText'] = $oresult->option_name;
                            $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['default'] = $oresult->option_default;
                            $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['id'] = $oresult->option_id;
                            $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['label_id'] = $oresult->field_id;
                                // if ($oresult->dependency == 1) {
                                //     $dep_chk = $this->db->query("select * from field where parent_field_id=" . $oresult->field_id . " and parent_option_id=" . $oresult->option_id." order by field_id ASC")->result();
                                //     $m = 0;
                                //     $n = 0;
                                //     foreach ($dep_chk as $depkey => $depresult) {
                                //         $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['id'] = $depresult->field_id;
                                //         $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['widgetType'] = $depresult->field_type;
                                //         $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['labelText'] = substr($depresult->field_name, (strpos($depresult->field_name, '_') ?: -1) + 1);
                                //         $dep_options = $this->db->query("select * from field_option where field_id=" . $depresult->field_id." order by field_id ASC")->result();
                                //         $o = 0;
                                //         foreach ($dep_options as $depokey => $deporesult) {
                                //             $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['optionText'] = $deporesult->option_name;
                                //             $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['default'] = $deporesult->option_default;
                                //             $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['id'] = $deporesult->option_id;
                                //             $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['label_id'] = $deporesult->field_id;
                                //             $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['dependency'] = array();
                                //             $o++;
                                //         }
                                //         $m++;
                                //         $n++;
                                //     }
                                // } else {
                                //     $data['form']['sections'][$a]['sub_sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'] = array();
                                // }
                            $l++;
                        }
                        $k++;
                    }
                    $j++;
                }
            } else {
                $label_result = $this->db->select("*")->from("field")->where("section_id='" . $value->section_id . "' and parent_field_id='0' and parent_option_id='0'")->order_by("field_id","ASC")->get()->result();
                $j = 0;
                $di = 0;
                $array_dup = array();
                foreach ($label_result as $key2 => $value2) {
                    $data['form']['sections'][$a]['sub_sections'][$i]['labels'][$j]['id'] = $value2->field_id;
                    $data['form']['sections'][$a]['sub_sections'][$i]['labels'][$j]['widgetType'] = $value2->field_type;
                    $data['form']['sections'][$a]['sub_sections'][$i]['labels'][$j]['labelText'] = $value2->field_name;
                    $field_option_result = $this->db->query('select * from field_option  where field_id=' . $value2->field_id)->result();
                    $k = 0;
                    foreach ($field_option_result as $key3 => $value3) {
                        $data['form']['sections'][$a]['sub_sections'][$i]['labels'][$j]['options'][$k]['optionText'] = $value3->option_name;
                        $data['form']['sections'][$a]['sub_sections'][$i]['labels'][$j]['options'][$k]['default'] = $value3->option_default;
                        $data['form']['sections'][$a]['sub_sections'][$i]['labels'][$j]['options'][$k]['id'] = $value3->option_id;
                        $data['form']['sections'][$a]['sub_sections'][$i]['labels'][$j]['options'][$k]['label_id'] = $value2->field_id;
                        if ($value3->dependency == 1) {
                            $depresult = $this->db->select("*")->from("field")->where("section_id='" . $value->section_id . "' and parent_field_id='" . $value2->field_id . "' and parent_option_id='" . $value3->option_id."'")->order_by("field_id","ASC")->get()->result_array();

                            $depinfo = $this->getchilddetails($depresult, $value2->field_id, $value3->option_id, $value->section_id, $di, $data['form']['sections'][$a]['sub_sections'][$i]['labels'][$j]['options'][$k]['dependency'][0]);
                        } else {
                            $depinfo = $this->db->select("*")->from("field")->where("section_id='" . $value->section_id . "' and parent_field_id='" . $value2->field_id . "' and parent_option_id='" . $value3->option_id . "'")->order_by("field_id","ASC")->get()->result_array();
                        }

                        $k++;
                    }
                    $j++;
                }
            }
            $i++;
        }$a++;
    }
    $this->response(array('code' => '200', 'message' => 'form_details', 'result' => $data, 'requestname' => $method));
}

    // public function custom_form_recursive_systemic_bcp($parameters, $method, $user_id) {
    //     $condition = array('form_id' => $parameters['form_id']);
    //     $form = $this->Generic_model->getAllRecords("form", $condition = '', $order = '');
    //     $sections = $this->db->query("SELECT b.form_id,b.form_name,b.form_type,a.section_id,a.format_type,a.title,a.brief from section a inner join form b on a.form_id=b.form_id where b.form_id=" . $parameters['form_id'])->result();

    //     $i = 0;
    //     foreach ($sections as $key => $value) {
    //         $data['form']['formType'] = $value->form_type;
    //         $data['form']['formName'] = $value->form_name;
    //         $data['form']['form_id'] = $value->form_id;
    //         $data['form']['sections'][$i]['title'] = $value->title;
    //         $data['form']['sections'][$i]['brief'] = $value->brief;
    //         $data['form']['sections'][$i]['format'] = $value->format_type;

    //         if ($value->format_type == 'tabular') {
    //             $label_result = $this->db->query("select * from field where section_id='" . $value->section_id . "' and parent_field_id IS NULL and parent_option_id IS NULL group by row_index")->result();
    //             $j = 0;
    //             $di = 0;
    //             $array_dup = array();
    //             foreach ($label_result as $key2 => $value2) {
    //                 $data['form']['sections'][$i]['rows'][$j]['row_title'] = strtok($value2->field_name, '_');
    //                 $data['form']['sections'][$i]['rows'][$j]['row_index'] = $value2->row_index;
    //                 $row_elements = $this->db->query("select * from field where section_id='" . $value->section_id . "' and row_index=" . $value2->row_index)->result();

    //                 $k = 0;
    //                 foreach ($row_elements as $key => $rresult) {
    //                     $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['id'] = $rresult->field_id;
    //                     $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['widgetType'] = $rresult->field_type;
    //                     $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['labelText'] = substr($rresult->field_name, (strpos($rresult->field_name, '_') ?: -1) + 1);
    //                     $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['row_index'] = $rresult->row_index;
    //                     $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['column_index'] = $rresult->column_index;
    //                     $options = $this->db->query("select * from field_option where field_id=" . $rresult->field_id)->result();
    //                     $l = 0;
    //                     foreach ($options as $okey => $oresult) {
    //                         $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['optionText'] = $oresult->option_name;
    //                         $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['default'] = $oresult->option_default;
    //                         $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['id'] = $oresult->option_id;
    //                         $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['label_id'] = $oresult->field_id;
    //                         if ($oresult->dependency == 1) {
    //                             $dep_chk = $this->db->query("select * from field where parent_field_id=" . $oresult->field_id . " and parent_option_id=" . $oresult->option_id)->result();
    //                             $m = 0;
    //                             $n = 0;
    //                             foreach ($dep_chk as $depkey => $depresult) {
    //                                 $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['id'] = $depresult->field_id;
    //                                 $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['widgetType'] = $depresult->field_type;
    //                                 $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['labelText'] = substr($depresult->field_name, (strpos($depresult->field_name, '_') ?: -1) + 1);
    //                                 $dep_options = $this->db->query("select * from field_option where field_id=" . $depresult->field_id)->result();
    //                                 $o = 0;
    //                                 foreach ($dep_options as $depokey => $deporesult) {
    //                                     $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['optionText'] = $deporesult->option_name;
    //                                     $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['default'] = $deporesult->option_default;
    //                                     $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['id'] = $deporesult->option_id;
    //                                     $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['label_id'] = $deporesult->field_id;
    //                                     $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'][$m]['labels'][$n]['options'][$o]['dependency'] = array();
    //                                     $o++;
    //                                 }
    //                                 $m++;
    //                                 $n++;
    //                             }
    //                         } else {
    //                             $data['form']['sections'][$i]['rows'][$j]['labels'][$k]['options'][$l]['dependency'] = array();
    //                         }
    //                         $l++;
    //                     }
    //                     $k++;
    //                 }
    //                 $j++;
    //             }
    //         } else {
    //             $label_result = $this->db->query("select * from field where section_id='" . $value->section_id . "' and parent_field_id IS NULL and parent_option_id IS NULL")->result();
    //             $j = 0;
    //             $di = 0;
    //             $array_dup = array();
    //             foreach ($label_result as $key2 => $value2) {
    //                 $data['form']['sections'][$i]['labels'][$j]['id'] = $value2->field_id;
    //                 $data['form']['sections'][$i]['labels'][$j]['widgetType'] = $value2->field_type;
    //                 $data['form']['sections'][$i]['labels'][$j]['labelText'] = $value2->field_name;
    //                 $field_option_result = $this->db->query('select * from field_option  where field_id=' . $value2->field_id)->result();
    //                 $k = 0;
    //                 foreach ($field_option_result as $key3 => $value3) {
    //                     $data['form']['sections'][$i]['labels'][$j]['options'][$k]['optionText'] = $value3->option_name;
    //                     $data['form']['sections'][$i]['labels'][$j]['options'][$k]['default'] = $value3->option_default;
    //                     $data['form']['sections'][$i]['labels'][$j]['options'][$k]['id'] = $value3->option_id;
    //                     $data['form']['sections'][$i]['labels'][$j]['options'][$k]['label_id'] = $value2->field_id;
    //                     if ($value3->dependency == 1) {
    //                         $depresult = $this->db->query("select * from field where section_id='" . $value->section_id . "' and parent_field_id=" . $value2->field_id . " and parent_option_id=" . $value3->option_id)->result_array();

    //                         $depinfo = $this->getchilddetails($depresult, $value2->field_id, $value3->option_id, $value->section_id, $di, $data['form']['sections'][$i]['labels'][$j]['options'][$k]['dependency'][0]);
    //                     } else {
    //                         $depinfo = $this->db->query("select * from field where section_id='" . $value->section_id . "' and parent_field_id='" . $value2->field_id . "' and parent_option_id='" . $value3->option_id . "'")->result_array();
    //                     }

    //                     $k++;
    //                 }
    //                 $j++;
    //             }
    //         }$i++;
    //     }

    //     $this->response(array('code' => '200', 'message' => 'form_details', 'result' => $data, 'requestname' => $method));
    // }

public function getSaveddata_systemic($parameters, $method, $user_id) {


    $gform_data = $this->db->select("b.format")->from("patient_form a")->join("patient_form_line_items b","a.patient_form_id=b.patient_form_id")->where("form_id ='" . $parameters['form_id'] . "' and patient_id='" . $parameters['patient_id'] . "'")->group_by("b.format")->get()->result();
    $k = 0;
    foreach ($gform_data as $result) {

        $data['forms'][$k]['form_type'] = $result->format;
        if ($result->format == 'normal') {

            $fom_data = $this->db->select("GROUP_CONCAT(option_value) as option_value,b.parent_field_id,b.field_value,b.field_id,b.format,b.row_title,b.row_index,b.column_index")->from("patient_form a")->join("patient_form_line_items b"," a.patient_form_id=b.patient_form_id")->where("form_id ='" . $parameters['form_id'] . "' and patient_id='" . $parameters['patient_id'] . "' and b.format='" . $result->format . "' group by field_id,parent_field_id,b.patient_form_id")->get()->result_array();

            $fom_data_sections = $this->db->select("b.section_text,section_image,b.section_id")->from("patient_form a")->join("patient_form_line_items b"," a.patient_form_id=b.patient_form_id")->where("a.form_id='" . $parameters['form_id'] . "' and a.patient_id='" . $parameters['patient_id'] . "' and b.format='" . $result->format . "' and b.section_text !=''")->get()->result_array();



            $i = 0;
            foreach ($fom_data as $key => $value) {

                $data['forms'][$k]['labels'][$i]['label_name'] = $value['field_value'];
                $data['forms'][$k]['labels'][$i]['label_value'] = $value['option_value'];
                $data['forms'][$k]['labels'][$i]['label_id'] = $value['field_id'];
                $data['forms'][$k]['labels'][$i]['parent_label_id'] = $value['parent_field_id'];
                $data['forms'][$k]['labels'][$i]['format'] = $value['format'];
                $data['forms'][$k]['labels'][$i]['row_title'] = $value['row_title'];
                $data['forms'][$k]['labels'][$i]['row_index'] = $value['row_index'];
                $data['forms'][$k]['labels'][$i]['column_index'] = $value['column_index'];
                $i++;
            }
            $j=0;
            foreach ($fom_data_sections as $key2 => $value2) {

                $data['forms'][$k]['sections'][$j]['section_id'] = $value2['section_id'];
                $data['forms'][$k]['sections'][$j]['section_text'] = $value2['section_text'];
                $data['forms'][$k]['sections'][$j]['section_image'] =base_url(ltrim($value2['section_image'],'.'));                    
                $j++;

            }

        } else {
            $fom_data_title = $this->db->select("DISTINCT(row_title)")->from("patient_form a")->join(" patient_form_line_items b","a.patient_form_id=b.patient_form_id")->where("form_id ='" . $parameters['form_id'] . "' and patient_id='" . $parameters['patient_id'] . "'  and b.format='" . $result->format . "'")->get()->result();
            $j = 0;
            foreach ($fom_data_title as $tresult) {
                $fom_data = $this->db->select("GROUP_CONCAT(option_value) as option_value,b.parent_field_id,b.field_value,b.field_id,b.format,b.row_title,b.row_index,b.column_index")->from(" patient_form a")->join("patient_form_line_items b"," a.patient_form_id=b.patient_form_id")->where("form_id ='" . $parameters['form_id'] . "' and patient_id='" . $parameters['patient_id'] . "' and b.format='" . $result->format . "' and row_title='" . $tresult->row_title . "'")->group_by(array("field_id","parent_field_id","b.patient_form_id"))->get()->result_array();
                $i = 0;
                $data['forms'][$k]['rows'][$j]['row_title'] = $tresult->row_title;
                foreach ($fom_data as $key => $value) {
                    $data['forms'][$k]['rows'][$j]['labels'][$i]['label_name'] = $value['field_value'];
                    $data['forms'][$k]['rows'][$j]['labels'][$i]['label_value'] = $value['option_value'];
                    $data['forms'][$k]['rows'][$j]['labels'][$i]['label_id'] = $value['field_id'];
                    $data['forms'][$k]['rows'][$j]['labels'][$i]['parent_label_id'] = $value['parent_field_id'];
                    $data['forms'][$k]['rows'][$j]['labels'][$i]['format'] = $value['format'];
                    $data['forms'][$k]['rows'][$j]['labels'][$i]['row_title'] = $value['row_title'];
                    $data['forms'][$k]['rows'][$j]['labels'][$i]['row_index'] = $value['row_index'];
                    $data['forms'][$k]['rows'][$j]['labels'][$i]['column_index'] = $value['column_index'];
                    $i++;
                }
                $j++;
            }
        }
        $k++;
    }
    $this->response(array('code' => '200', 'message' => 'form_details', 'result' => $data, 'requestname' => $method));
}

public function patient_oppintment_status($parameters, $method, $user_id) {

    $status = $this->db->select("status")->from("appointments")->where("patient_id='" . $parameters['patient_id'] . "' and status !='closed'")->get()->row();

    if ($status->status == 'checked_in' || $status->status == 'vital-signs') {

        $data['status'] = 1;
    } else {
        $data['status'] = 0;
    }

    $this->response(array('code' => '200', 'message' => 'patient_oppintment_status', 'result' => $data, 'requestname' => $method));
}

/* method for inserting prescription templte saving */

public function prescription_saveas_template($parameters, $method, $user_id) {

    $insert['doctor_id'] = $parameters['doctor_id'];
    $insert['prescription_template_name'] = $parameters['prescription_template_name'];
    $insert['clinic_id'] = $parameters['clinic_id'];
    $insert['created_by'] = $user_id;
    $insert['modified_by'] = $user_id;
    $insert['created_date_time'] = date('Y-m-d H:i:s');
    $insert['modified_date_time'] = date('Y-m-d H:i:s');
    $prescription_template_id = $this->Generic_model->insertDataReturnId('prescription_template', $insert);
    $drugs_info = $parameters['prescription'];
    for ($i = 0; $i < count($drugs_info); $i++) {
        $insert_line_items['prescription_template_id'] = $prescription_template_id;
        $insert_line_items['composition'] = $drugs_info[$i]['composition'];
        $insert_line_items['drug_id'] = $drugs_info[$i]['drug_id'];
        $insert_line_items['medicine_name'] = $drugs_info[$i]['medicine_name'];
        if(isset($drugs_info[$i]['day_schedule'])){
            $insert_line_items['day_schedule'] = $drugs_info[$i]['day_schedule'];
        }
        else{
            $insert_line_items['day_schedule'] = "";
        }

        $insert_line_items['dose_course'] = $drugs_info[$i]['dose_course'];
        $insert_line_items['drug_dose'] = $drugs_info[$i]['drug_dose'];
        $insert_line_items['preffered_intake'] = $drugs_info[$i]['preffered_intake'];
        $insert_line_items['quantity'] = $drugs_info[$i]['quantity'];
        $insert_line_items['remarks'] = $drugs_info[$i]['remarks'];
        $this->Generic_model->insertDataReturnId('prescription_template_line_items', $insert_line_items);
    }
    $this->response(array('code' => '200', 'message' => 'Prescription Template Saved Successfully', 'result' => $param, 'requestname' => $method));
}

/* method for investigation templte saving */

public function investigation_saveas_template($parameters, $method, $user_id) {
//echo "hi";exit();
    $insert['doctor_id'] = $parameters['doctor_id'];
    $insert['investigation_template_name'] = $parameters['investigation_template_name'];
    $insert['clinic_id'] = $parameters['clinic_id'];
    $insert['created_by'] = $user_id;
    $insert['modified_by'] = $user_id;
    $insert['created_date_time'] = date('Y-m-d H:i:s');
    $insert['modified_date_time'] = date('Y-m-d H:i:s');
    $investigation_template_id = $this->Generic_model->insertDataReturnId('doctor_investigation_template', $insert);
    $investgating_info = $parameters['investgating_ids'];

    for ($i = 0; $i < count($investgating_info); $i++) {
        $insert_line_items['investigation_template_id'] = $investigation_template_id;
        $insert_line_items['investigation_id'] = $investgating_info[$i]['investigation_id'];
        $insert_line_items['investigation_name'] = $investgating_info[$i]['investigation_name'];
        $insert_line_items['investigation_code'] = $investgating_info[$i]['investigation_code'];
        $insert_line_items['category'] = $investgating_info[$i]['category'];
        $insert_line_items['mrp'] = $investgating_info[$i]['mrp'];
        $insert_line_items['checked'] = $investgating_info[$i]['checked'];
        $this->Generic_model->insertDataReturnId('doctor_investigation_template_line_items', $insert_line_items);
    }
    $this->response(array('code' => '200', 'message' => 'Investigation Template Saved Successfully', 'result' => $param, 'requestname' => $method));
}

public function get_investigation_template($parameters, $method, $user_id) {

    $getlist = $this->db->select("*")->from("doctor_investigation_template it")->join("doctor_investigation_template_line_items itl","it.investigation_template_id = itl.investigation_template_id")->where("it.clinic_id='" . $parameters['clinic_id'] . "' and it.doctor_id='" . $parameters['doctor_id'] . "'")->group_by("it.investigation_template_id")->order_by("it.investigation_template_id")->get()->result();
    if (count($getlist) > 0) {
        $i = 0;
        foreach ($getlist as $key => $value) {
            $data['template_list'][$i]['investigation_template_id'] = $value->investigation_template_id;
            $data['template_list'][$i]['investigation_template_name'] = $value->investigation_template_name;
            $data['template_list'][$i]['clinic_id'] = $value->clinic_id;
            $data['template_list'][$i]['doctor_id'] = $value->doctor_id;

            $getchild = $this->db->select("*")->from("doctor_investigation_template_line_items")->where("investigation_template_id='" . $value->investigation_template_id . "'")->get()->result();
            $j = 0;
            foreach ($getchild as $key => $value2) {
                $data['template_list'][$i]['investigation'][$j]['investigation_template_line_item_id'] = $value2->investigation_template_line_item_id;
                $data['template_list'][$i]['investigation'][$j]['investigation_template_id'] = $value2->investigation_template_id;
                $data['template_list'][$i]['investigation'][$j]['investigation_name'] = $value2->investigation_name;
                $data['template_list'][$i]['investigation'][$j]['investigation_id'] = $value2->investigation_id;
                $data['template_list'][$i]['investigation'][$j]['category'] = $value2->category;
                $data['template_list'][$i]['investigation'][$j]['investigation_code'] = $value2->investigation_code;
                $data['template_list'][$i]['investigation'][$j]['mrp'] = $value2->mrp;
                $data['template_list'][$i]['investigation'][$j]['checked'] = $value2->checked;
                $j++;
            }
            $i++;
        }
    } else {
        $data['template_list'] = NULL;
    }

    $this->response(array('code' => '200', 'message' => 'Investigation Template List', 'result' => $data, 'requestname' => $method));
}

public function get_prescription_template($parameters, $method, $user_id) {

    $getlist = $this->db->select("*")->from("prescription_template it")->join("prescription_template_line_items itl","it.prescription_template_id = itl.prescription_template_id")->where("it.clinic_id='" . $parameters['clinic_id'] . "' and it.doctor_id='" . $parameters['doctor_id'] . "'")->group_by("it.prescription_template_id")->order_by("it.prescription_template_id")->get()->result();
    if (count($getlist) > 0) {
        $i = 0;
        foreach ($getlist as $key => $value) {
            $data['template_list'][$i]['prescription_template_id'] = $value->prescription_template_id;
            $data['template_list'][$i]['prescription_template_name'] = $value->prescription_template_name;
            $data['template_list'][$i]['clinic_id'] = $value->clinic_id;
            $data['template_list'][$i]['doctor_id'] = $value->doctor_id;

            $getchild = $this->db->query("select * from prescription_template_line_items where prescription_template_id='" . $value->prescription_template_id . "'")->result();
            $j = 0;
            foreach ($getchild as $key => $value2) {
                $data['template_list'][$i]['prescriptions'][$j]['prescription_template_line_item_id'] = $value2->investigation_template_line_item_id;
                $data['template_list'][$i]['prescriptions'][$j]['prescription_template_id'] = $value2->investigation_template_id;
                $data['template_list'][$i]['prescriptions'][$j]['drug_id'] = $value2->drug_id;
                $data['template_list'][$i]['prescriptions'][$j]['medicine_name'] = $value2->medicine_name;
                $data['template_list'][$i]['prescriptions'][$j]['day_schedule'] = $value2->day_schedule;
                $data['template_list'][$i]['prescriptions'][$j]['dose_course'] = $value2->dose_course;
                $data['template_list'][$i]['prescriptions'][$j]['drug_dose'] = $value2->drug_dose;
                $data['template_list'][$i]['prescriptions'][$j]['preffered_intake'] = $value2->preffered_intake;
                $data['template_list'][$i]['prescriptions'][$j]['quantity'] = $value2->quantity;
                $data['template_list'][$i]['prescriptions'][$j]['remarks'] = $value2->remarks;
                $j++;
            }
            $i++;
        }
    } else {
        $data['template_list'] = NULL;
    }

    $this->response(array('code' => '200', 'message' => 'Prescription Template List', 'result' => $data, 'requestname' => $method));
}

public function patient_procedure($parameters, $method, $user_id) {

    $data['patient_id'] = $parameters['patient_id'];
    $data['doctor_id'] = $parameters['doctor_id'];
    $data['appointment_id'] = $parameters['appointment_id'];
    $data['surgeon'] = $parameters['surgeon'];
    $data['anesthetist'] = $parameters['anesthetist'];
    $data['assisting_surgeon'] = $parameters['assisting_surgeon'];
    $data['type_of_anesthesia'] = $parameters['type_of_anesthesia'];
    $data['assisting_nurse'] = $parameters['assisting_nurse'];
    $data['postoperative_diagnosis'] = $parameters['postoperative_diagnosis'];
    $data['preoperative_diagnosis'] = $parameters['preoperative_diagnosis'];
        //$medical_procedure =  $parameters['medical_procedure'];
    $data['indication'] = $parameters['indication'];
    $data['medical_procedure_id'] = $parameters['medical_procedure_id'];
    $data['position'] = $parameters['position'];


    $doctor_medical_procedures_list = $this->db->select("*")->from("doctor_medical_procedures")->where("medical_procedure_id ='" . $parameters['medical_procedure_id'] . "' and doctor_id = '" . $parameters['doctor_id'] . "'")->get()->row();

    if (count($doctor_medical_procedures_list) > 0) {
        $data['medical_procedure'] = $doctor_medical_procedures_list->medical_procedure;
    } else {
        $medical_procedures_list = $this->db->select("*")->from("medical_procedures")->where("medical_procedure_id ='" . $parameters['medical_procedure_id'] . "'")->get()->row();

        $data['medical_procedure'] = $medical_procedures_list->procedure_description;
    }


    if ($parameters['patient_id'] != "" || $parameters['patient_id'] != NULL) {
        $data_val['patient_list'] = $this->db->select("*")->from("patients")->where("patient_id = '" . $parameters['patient_id'] . "'")->get()->row();
    }

    $checking_patient_procedure = $this->db->select("*")->from("patient_procedure")->where("patient_id ='" . $data['patient_id'] . "' and  doctor_id = '" . $data['doctor_id'] . "' and appointment_id = '" . $data['appointment_id'] . "' and  medical_procedure_id = '" . $data['medical_procedure_id'] . "'")->get()->row();
    if (count($checking_patient_procedure) > 0) {
        unset($data['medical_procedure']);

        $ok = $this->Generic_model->updateData("patient_procedure", $data, array('patient_id' => $data['patient_id'], "doctor_id" => $data['doctor_id'], "appointment_id" => $data['appointment_id'], "medical_procedure_id" => $data['medical_procedure_id']));
        $patient_procedure_id = $checking_patient_procedure->patient_procedure_id;
    } else {
        $patient_procedure_id = $this->Generic_model->insertDataReturnId('patient_procedure', $data);
    }

    if ($patient_procedure_id != "" || $patient_procedure_id != NULL) {
        $data_val['patient_procedure'] = $this->db->select("*")->from("patient_procedure")->where("patient_procedure_id = '" . $patient_procedure_id . "'")->get()->row();
            //$data_val['']
        $pdf_name_val = str_replace(" ", "_", $data['patient_list']->first_name);
        $html = $this->load->view('procedures/generate_patient_pdf', $data_val, true);
        $pdfFilePath = strtolower($pdf_name_val . "" . date('md')) . ".pdf";
        $data['procedure_patient_pdf'] = $pdfFilePath;
        $this->load->library('M_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
            //download it.
        $this->m_pdf->pdf->Output("./uploads/procedures/" . $pdfFilePath, "F");
        $data_1['procedure_patient_pdf'] = base_url("uploads/procedures/" . $pdfFilePath . "");
        $this->response(array('code' => '200', 'message' => 'Patient Procedure', 'result' => $data_1, 'requestname' => $method));
    } else {
        $this->response(array('code' => '400', 'message' => 'not updated'));
    }
}

public function patient_procedure_edit($parameters, $method, $user_id) {
    $patient_id = $parameters['patient_id'];
    $doctor_id = $parameters['doctor_id'];
    $appointment_id = $parameters['appointment_id'];
    $medical_procedure_id = $parameters['medical_procedure_id'];
    $clinic_id = $parameters['clinic_id'];
    $checking_patient_procedure = $this->db->select("*")->from("patient_procedure")->where("patient_id ='" . $patient_id . "' and  doctor_id = '" . $doctor_id . "' and appointment_id = '" . $appointment_id . "' and  medical_procedure_id = '" . $medical_procedure_id . "'")->get()->row();
    if (count($checking_patient_procedure) > 0) {
        $data['procedure_description'] = $checking_patient_procedure->medical_procedure;
    } else {
        $check_doctor_procedure = $this->db->select("*")->from("doctor_medical_procedures")->where("doctor_id ='" . $doctor_id . "' and medical_procedure_id ='" . $medical_procedure_id . "' and clinic_id ='" . $clinic_id . "'")->get()->row();
        if (count($check_doctor_procedure) > 0) {
            $data['procedure_description'] = "<html><body>" . $check_doctor_procedure->medical_procedure . "/body></html>";
        } else {
            $standard_procedure = $this->db->select("*")->from("medical_procedures")->where("medical_procedure_id ='" . $medical_procedure_id . "'")->get()->row();
            $data['procedure_description'] = "<html><body>" . $standard_procedure->procedure_description . "/body></html>";
        }
    }
    $url_parameters = $patient_id . "/" . $doctor_id . "/" . $appointment_id . "/" . $medical_procedure_id . "/" . $clinic_id;
    $data["procedure_url"] = base_url("Procedure_update/patient_producer/" . $url_parameters);
    $this->response(array('code' => '200', 'message' => 'Patient Procedure', 'result' => $data, 'requestname' => $method));
}

public function updateQuickBloxData($parameters, $method, $user_id) {

    $quickbox['qb_user_id'] = $parameters['qb_user_id'];
        //$quickbox['qb_password']=$parameters['qb_password'];
    $quickbox['qb_user_login'] = $parameters['qb_user_login'];
    $quickbox['qb_user_fullname'] = $parameters['qb_user_fullname'];
    $quickbox['qb_user_tag'] = $parameters['qb_user_tag'];
    $ok = $this->Generic_model->updateData("users", $quickbox, array('user_id' => $user_id));

    if ($ok) {
        $this->response(array('code' => '200', 'message' => 'Quickbox data updated success fully', 'requestname' => $method));
    }
}

    //print latest patient vitals and generate pdf
public function print_vitals($parameters, $method, $user_id) {

    $data['appointments'] = $this->db->select("a.*, c.clinic_name,c.clinic_logo,c.address,c.clinic_phone,p.first_name as pname,p.last_name as plname,p.gender,p.age,p.allergy,p.address_line,p.mobile,p.qrcode,ap.appointment_date")->from("patient_vital_sign a")->join("clinics c","a.clinic_id = c.clinic_id","left")->join("patients p ","a.patient_id = p.patient_id","left")->join("appointments ap","a.appointment_id = ap.appointment_id","left")->where("a.patient_id='" . $parameters['patient_id'] . "' and a.clinic_id='" . $parameters['clinic_id'] . "'")->order_by("a.vital_sign_recording_date_time","desc")->get()->row();

    $data['doctor_info'] = $this->db->select("*")->from("doctors a")->join("department b","a.department_id = b.department_id")->where("a.doctor_id='" . $parameters['doctor_id'] . "'")->get()->row();

    $patient_name = $data['appointments']->pname . " " . $data['appointments']->plname . date('Ymd') . time();

    $data['pdf_settings'] = $pdf_settings = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$data['appointments']->clinic_id."'")->row();




      // print_r($pdf_settings);exit;

    $this->load->library('M_pdf');
    $html = $this->load->view('reports/vital_print_android', $data, true);
    $pdfFilePath = strtolower(str_replace(" ", "_", $patient_name)) . ".pdf";
    $stylesheet  = '';
    $stylesheet .= file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
    $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");
    $this->m_pdf->pdf->autoScriptToLang = true;
    $this->m_pdf->pdf->autoLangToFont = true;

    $footerConfiguration = 
    [
     'L' => [ // L for Left part of the header
     'content' => '',
 ],
    'C' => [ // C for Center part of the header
    'content' => '',
],
'R' => [
  'content' => '<span style="font-size:14px;"><b>Date: </b>'.date("d/m/Y").'</span>',
],
    'line' => 0, // That's the relevant parameter
];


$Footer = [
  'odd' => $footerConfiguration,
  'even' => $footerConfiguration
];
$this->m_pdf->pdf->setAutoBottomMargin = "stretch";
$this->m_pdf->pdf->defaultheaderline = 0;
if(count($pdf_settings)>0){


    if($pdf_settings->footer == 1){
        $this->m_pdf->pdf->SetFooter($Footer);
    }
    else{
        $this->m_pdf->pdf->SetFooter('<div style="height:'.$pdf_settings->footer_height.'px;"></div>');
    }
}


$this->m_pdf->pdf->shrink_tables_to_fit = 1;
$this->m_pdf->pdf->setAutoTopMargin = "stretch";
$this->m_pdf->pdf->setAutoBottomMargin = "stretch";
$this->m_pdf->pdf->defaultheaderline = 0;
$this->m_pdf->pdf->WriteHTML($stylesheet,1);
$this->m_pdf->pdf->WriteHTML($html,2);
$this->m_pdf->pdf->Output("./uploads/vital_reports/".$pdfFilePath, "F"); 

$result['pdf_path'] = base_url() . "uploads/vital_reports/" . $pdfFilePath;

$this->response(array('code' => '200', 'message' => 'Vitals Print Successful', 'result' => $result, 'requestname' => $method));
}

    /****
    @Service : Get Final Impression
    @Author : Narasimha
    @Date 16-04-2019
    ****/
    
    public function get_final_impression($parameters, $method, $user_id) {

        $getAppointments = $this->db->select("*")->from("appointments")->where("appointment_id='".$parameters['appointment_id']."' and clinic_id='".$parameters['clinic_id']."'")->get()->row();
        $parent_appointment_id = $getAppointments->parent_appointment_id;
        $data['appointment_id'] = $getAppointments->appointment_id;
        $data['clinic_id'] = $getAppointments->clinic_id;
        $data['doctor_id'] = $getAppointments->doctor_id;
        $data['patient_id'] = $getAppointments->patient_id;
        $data['umr_no'] = $getAppointments->umr_no;
        
        $p_cd_lineitems = $this->db->select("pcdl.disease_name,pcdl.patient_cd_line_item_id")->from(" patient_cd_line_items pcdl")->join("patient_clinical_diagnosis pcd","pcdl.patient_clinical_diagnosis_id=pcd.patient_clinical_diagnosis_id")->where("pcd.patient_id='".$getAppointments->patient_id."' and pcd.appointment_id='".$getAppointments->appointment_id."' and pcd.clinic_id='".$getAppointments->clinic_id."' and pcd.doctor_id='".$getAppointments->doctor_id."'")->get()->result();
        
        $inv_lineitems = $this->db->select("*")->from("patient_investigation_line_items pil")->join("investigations inv","pil.investigation_id=inv.investigation_id")->join("patient_investigation pi","pil.patient_investigation_id=pi.patient_investigation_id")->where("pi.patient_id='".$getAppointments->patient_id."' and pi.appointment_id='".$getAppointments->appointment_id."' and pi.clinic_id='".$getAppointments->clinic_id."' and pi.doctor_id='".$getAppointments->doctor_id."'")->get()->result();

        $p_p_lineitems = $this->db->select("*")->from("patient_prescription_drug ppd")->join("patient_prescription pp","ppd.patient_prescription_id=pp.patient_prescription_id")->where("pp.patient_id='".$getAppointments->patient_id."' and pp.appointment_id='".$getAppointments->appointment_id."' and pp.clinic_id='".$getAppointments->clinic_id."' ")->get()->result();
        
        if($parent_appointment_id == 0 || $parent_appointment_id == NULL)
        {
            $apt_id = $getAppointments->appointment_id;
        }else{
            $get_all_appointments = $this->db->select("*")->from("appointments")->where ("(parent_appointment_id='".$parent_appointment_id."' or appointment_id='".$parent_appointment_id."') and appointment_id NOT IN  (".$getAppointments->appointment_id.")")->get()->result();
            
            $previous_patient_id = $get_all_appointments[0]->patient_id;
            $previous_appointment_id = $get_all_appointments[0]->appointment_id;
            $previous_clinic_id = $get_all_appointments[0]->clinic_id;
            $previous_doctor_id = $get_all_appointments[0]->doctor_id;
            
            $apt_id = $get_all_appointments[0]->appointment_id;
            
            if(count($p_cd_lineitems)<=0)
            {
                $p_cd_lineitems = $this->db->select("pcdl.disease_name,pcdl.patient_cd_line_item_id,pcdl.patient_clinical_diagnosis_id")->from("patient_cd_line_items pcdl")->join("patient_clinical_diagnosis pcd","pcdl.patient_clinical_diagnosis_id=pcd.patient_clinical_diagnosis_id")->where("pcd.patient_id='".$previous_patient_id."' and pcd.appointment_id='".$previous_appointment_id."' and pcd.clinic_id='".$previous_clinic_id."' and pcd.doctor_id='".$previous_doctor_id."'")->get()->result();
            }
            
            if(count($inv_lineitems)<=0)
            {
                $inv_lineitems = $this->db->select("*")->from("patient_investigation_line_items pil")-> join("investigations inv","pil.investigation_id=inv.investigation_id")->join(" patient_investigation pi","pil.patient_investigation_id=pi.patient_investigation_id")->where("pi.patient_id='".$previous_patient_id."' and pi.appointment_id='".$previous_appointment_id."' and pi.clinic_id='".$previous_clinic_id."' and pi.doctor_id='".$previous_doctor_id."'")->get()->result();
            }
            if(count($p_p_lineitems)<=0)
            {
                $p_p_lineitems = $this->db->select("*")->from("patient_prescription_drug ppd")->join("patient_prescription pp","ppd.patient_prescription_id=pp.patient_prescription_id")->where("pp.patient_id='".$previous_patient_id."' and pp.appointment_id='".$previous_appointment_id."' and pp.clinic_id='".$previous_clinic_id."' ")->get()->result();
            }           
        }
        
        $c=0;
        if(count($p_cd_lineitems)>0)
        {
            foreach($p_cd_lineitems as $pcdl){

                $data['clinicaldiagnosis'][$c]['patient_cd_line_item_id'] = $pcdl->patient_cd_line_item_id;
                $data['clinicaldiagnosis'][$c]['patient_clinical_diagnosis_id'] = $pcdl->patient_clinical_diagnosis_id;
                $data['clinicaldiagnosis'][$c]['appointment_id'] = $apt_id;
                $data['clinicaldiagnosis'][$c]['description'] = $pcdl->disease_name;
                $c++;
            }
        }else{
            $data['clinicaldiagnosis'] = array();
        }

        $i=0;
        if(count($inv_lineitems)>0){
            foreach($inv_lineitems as $inl)
            {
                $data['investigation'][$i]['patient_investigation_line_item_id'] = $inl->patient_investigation_line_item_id;
                $data['investigation'][$i]['patient_investigation_id'] = $inl->patient_investigation_id;
                $data['investigation'][$i]['appointment_id'] = $apt_id;
                $data['investigation'][$i]['category'] = $inl->category;
                $data['investigation'][$i]['checked'] = $inl->checked;
                $data['investigation'][$i]['investigation_id'] = $inl->investigation_id;
                $data['investigation'][$i]['investigation'] = $inl->investigation;
                $data['investigation'][$i]['mrp'] = $inl->mrp;
                $i++;
            }
        }else{
            $data['investigation'] = array();
        }

        $p=0;
        if(count($p_p_lineitems)>0){
            foreach($p_p_lineitems as $ppl)
            {
                $data['prescription'][$p]['patient_prescription_drug_id'] = $ppl->patient_prescription_drug_id;
                $data['prescription'][$p]['patient_prescription_id'] = $ppl->patient_prescription_id;
                $data['prescription'][$p]['appointment_id'] = $apt_id;
                $data['prescription'][$p]['day_schedule'] = $ppl->day_schedule;
                $data['prescription'][$p]['day_dosage'] = $ppl->day_dosage;
                $data['prescription'][$p]['dose_course'] = $ppl->dose_course;
                $data['prescription'][$p]['drug_dose'] = $ppl->drug_dose;
                $data['prescription'][$p]['dosage_frequency'] = $ppl->dosage_frequency;
                $data['prescription'][$p]['dosage_unit'] = $ppl->dosage_unit;
                $data['prescription'][$p]['drug_id'] = $ppl->drug_id;
                $data['prescription'][$p]['medicine_name'] = $ppl->medicine_name;
                $data['prescription'][$p]['preffered_intake'] = $ppl->preffered_intake;
                $data['prescription'][$p]['quantity'] = $ppl->quantity;
                $data['prescription'][$p]['remarks'] = $ppl->remarks;
                $p++;
            }
            $data['plan'] = $p_p_lineitems[0]->plan;
        }else{
            $data['plan'] = NULL;
            $data['prescription'] = array();
        }

        $parent_appointment = $this->Generic_model->getSingleRecord('appointments',array('appointment_id'=>$parameters['appointment_id']),'');
        
        if($parent_appointment->parent_appointment_id==0)
        {
            $next_follow_up=$this->db->select("*")->from("appointments")->where("parent_appointment_id='".$parameters['appointment_id']."'")->order_by("appointment_id","desc")->get()->row();
            if($next_follow_up->appointment_id==$appointment_id)
            {
                $data['follow_up_date'] = '';
            }else{
                $data['follow_up_date'] = date('d-M-Y',strtotime($next_follow_up->appointment_date))." ".date('H:i A',strtotime($next_follow_up->appointment_time_slot));
            }
        }else{
            $next_follow_up=$this->db->select("*")->from("appointments")->where("parent_appointment_id='".$parent_appointment->parent_appointment_id."'")->order_by("appointment_id","desc")->get()->row();
            if($next_follow_up->appointment_id==$parameters['appointment_id'])
            {
                $data['follow_up_date'] = '';
            }else{
                $data['follow_up_date'] = date('d-M-Y',strtotime($next_follow_up->appointment_date))." ".date('H:i A',strtotime($next_follow_up->appointment_time_slot));
            }
        }



        $this->response(array('code' => '200', 'message' => 'Final Impression List', 'result' => $data, 'requestname' => $method));
    }
    
    public function get_latestPrescription($parameters, $method, $user_id) {
        $getAppointments = $this->db->select("*")->from("appointments")->where("appointment_id='".$parameters['appointment_id']."' and clinic_id='".$parameters['clinic_id']."'")->get()->row();
        $parent_appointment_id = $getAppointments->parent_appointment_id;
        $data['appointment_id'] = $getAppointments->appointment_id;
        $data['clinic_id'] = $getAppointments->clinic_id;
        $data['doctor_id'] = $getAppointments->doctor_id;
        $data['patient_id'] = $getAppointments->patient_id;
        $data['umr_no'] = $getAppointments->umr_no;

        $parent_appointment = $this->db->select("*")->from("appointments")->where("parent_appointment_id='" . $parameters['appointment_id'] . "'")->order_by("appointment_id","asc")->get()->row();

        if (count($parent_appointment) > 0) {

            $data['appointment_id'] = $parent_appointment->appointment_id;
            $data['next_followup_date'] = $parent_appointment->appointment_date;
            $data['time_slot'] = $parent_appointment->appointment_time_slot;
            $data['parent_appointment_id'] = $parent_appointment->parent_appointment_id;
        } else {
            $data['appointment_id'] = "";
            $data['next_followup_date'] = "";
            $data['time_slot'] = "";
            $data['parent_appointment_id'] = "";
        }

        

        $p_p_lineitems = $this->db->select("*")->from("patient_prescription_drug ppd")->join("patient_prescription pp","ppd.patient_prescription_id=pp.patient_prescription_id")->where("pp.patient_id='".$getAppointments->patient_id."' and pp.appointment_id='".$getAppointments->appointment_id."' and pp.clinic_id='".$getAppointments->clinic_id."' ")->get()->result();
        
        $prescription_plan = $this->Generic_model->getSingleRecord('patient_prescription',array('appointment_id'=>$getAppointments->appointment_id),'');
        
        if($parent_appointment_id == 0 || $parent_appointment_id == NULL)
        {
            $apt_id = $getAppointments->appointment_id;
        }else{
            $get_all_appointments = $this->db->select("*")->from("appointments")->where("(parent_appointment_id='".$parent_appointment_id."' or appointment_id='".$parent_appointment_id."') and appointment_id NOT IN  (".$getAppointments->appointment_id.")")->order_by('appointment_id','DESC')->get()->result();
            
            $previous_patient_id = $get_all_appointments[0]->patient_id;
            $previous_appointment_id = $get_all_appointments[0]->appointment_id;
            $previous_clinic_id = $get_all_appointments[0]->clinic_id;
            $previous_doctor_id = $get_all_appointments[0]->doctor_id;
            
            $apt_id = $get_all_appointments[0]->appointment_id;
            
            if(count($p_p_lineitems)<=0)
            {
                $prescription_plan = $this->Generic_model->getSingleRecord('patient_prescription',array('appointment_id'=>$previous_appointment_id),'');
                
                $p_p_lineitems = $this->db->select("*")->from("patient_prescription_drug ppd")->join("patient_prescription pp","ppd.patient_prescription_id=pp.patient_prescription_id")->where("pp.patient_id='".$previous_patient_id."' and pp.appointment_id='".$previous_appointment_id."' and pp.clinic_id='".$previous_clinic_id."'")->get()->result();
            }           
        }
        
        $data['plan'] = $prescription_plan->plan;
        $data['instructions'] = $prescription_plan->general_instructions;
        
        $p=0;
        if(count($p_p_lineitems)>0){
            foreach($p_p_lineitems as $ppl)
            {
                $data['prescription'][$p]['patient_prescription_drug_id'] = $ppl->patient_prescription_drug_id;
                $data['prescription'][$p]['patient_prescription_id'] = $ppl->patient_prescription_id;
                $data['prescription'][$p]['appointment_id'] = $apt_id;
                $data['prescription'][$p]['day_schedule'] = $ppl->day_schedule;
                $data['prescription'][$p]['day_dosage'] = $ppl->day_dosage;
                $data['prescription'][$p]['dosage_frequency'] = $ppl->dosage_frequency;
                $data['prescription'][$p]['dose_course'] = $ppl->dose_course;
                $data['prescription'][$p]['drug_dose'] = $ppl->drug_dose;
                $data['prescription'][$p]['dosage_unit'] = $ppl->dosage_unit;
                $data['prescription'][$p]['mode'] = $ppl->mode;
                $data['prescription'][$p]['drug_id'] = $ppl->drug_id;
                $get_composition = $this->db->select("composition")->from("drug")->where("drug_id='".$ppl->drug_id."'")->get()->row();
                $data['prescription'][$p]['composition'] = $get_composition->composition;
                $data['prescription'][$p]['medicine_name'] = $ppl->medicine_name;
                $data['prescription'][$p]['preffered_intake'] = $ppl->preffered_intake;
                $data['prescription'][$p]['quantity'] = $ppl->quantity;
                $data['prescription'][$p]['remarks'] = $ppl->remarks;

                $p++;
            }
        }else{
            $data['prescription'] = array();
        }

        $this->response(array('code' => '200', 'message' => 'Precription List', 'result' => $data, 'requestname' => $method));
    }
    
    public function get_latestClinicalDiagnosis($parameters, $method, $user_id) {

        $getAppointments = $this->db->select("*")->from("appointments")->where("appointment_id='".$parameters['appointment_id']."' and clinic_id='".$parameters['clinic_id']."'")->get()->row();
        $parent_appointment_id = $getAppointments->parent_appointment_id;
        $data['appointment_id'] = $getAppointments->appointment_id;
        $data['clinic_id'] = $getAppointments->clinic_id;
        $data['doctor_id'] = $getAppointments->doctor_id;
        $data['patient_id'] = $getAppointments->patient_id;
        $data['umr_no'] = $getAppointments->umr_no;
        
        $p_cd_lineitems = $this->db->select("pcdl.disease_name,pcdl.patient_cd_line_item_id")->from("patient_cd_line_items pcdl")->join("patient_clinical_diagnosis pcd","pcdl.patient_clinical_diagnosis_id=pcd.patient_clinical_diagnosis_id")->where("pcd.patient_id='".$getAppointments->patient_id."' and pcd.appointment_id='".$getAppointments->appointment_id."' and pcd.clinic_id='".$getAppointments->clinic_id."' and pcd.doctor_id='".$getAppointments->doctor_id."'")->get()->result();
        
        
        
        if($parent_appointment_id == 0 || $parent_appointment_id == NULL)
        {
            $apt_id = $getAppointments->appointment_id;
        }else{
            $get_all_appointments = $this->db->select("*")->from("appointments")->where("(parent_appointment_id='".$parent_appointment_id."' or appointment_id='".$parent_appointment_id."') and appointment_id NOT IN  (".$getAppointments->appointment_id.")")->order_by('appointment_id','DESC')->get()->result();
            
            $previous_patient_id = $get_all_appointments[0]->patient_id;
            $previous_appointment_id = $get_all_appointments[0]->appointment_id;
            $previous_clinic_id = $get_all_appointments[0]->clinic_id;
            $previous_doctor_id = $get_all_appointments[0]->doctor_id;
            
            $apt_id = $get_all_appointments[0]->appointment_id;
            
            if(count($p_cd_lineitems)<=0)
            {
                $p_cd_lineitems = $this->db->select("pcdl.disease_name,pcdl.patient_cd_line_item_id,pcdl.patient_clinical_diagnosis_id")->from("patient_cd_line_items pcdl")->join("patient_clinical_diagnosis pcd","pcdl.patient_clinical_diagnosis_id=pcd.patient_clinical_diagnosis_id")->where("pcd.patient_id='".$previous_patient_id."' and pcd.appointment_id='".$previous_appointment_id."' and pcd.clinic_id='".$previous_clinic_id."' and pcd.doctor_id='".$previous_doctor_id."'")->get()->result();
            }

        }
        
        $c=0;
        if(count($p_cd_lineitems)>0)
        {
            foreach($p_cd_lineitems as $pcdl){

                $data['clinicaldiagnosis'][$c]['patient_cd_line_item_id'] = $pcdl->patient_cd_line_item_id;
                $data['clinicaldiagnosis'][$c]['patient_clinical_diagnosis_id'] = $pcdl->patient_clinical_diagnosis_id;
                $data['clinicaldiagnosis'][$c]['appointment_id'] = $apt_id;
                $data['clinicaldiagnosis'][$c]['disease_name'] = $pcdl->disease_name;
                $c++;
            }
        }else{
            $data['clinicaldiagnosis'] = array();
        }



        $this->response(array('code' => '200', 'message' => 'Clinical Diagnosis List', 'result' => $data, 'requestname' => $method));
    }
    
    public function get_latestInvestigations($parameters, $method, $user_id) {

        $getAppointments = $this->db->select("*")->from("appointments")->where("appointment_id='".$parameters['appointment_id']."' and clinic_id='".$parameters['clinic_id']."'")->get()->row();
        $parent_appointment_id = $getAppointments->parent_appointment_id;
        $data['appointment_id'] = $getAppointments->appointment_id;
        $data['clinic_id'] = $getAppointments->clinic_id;
        $data['doctor_id'] = $getAppointments->doctor_id;
        $data['patient_id'] = $getAppointments->patient_id;
        $data['umr_no'] = $getAppointments->umr_no;
        
        
        $inv_lineitems = $this->db->select("*")->from("patient_investigation_line_items pil")->join("patient_investigation pi","pil.patient_investigation_id=pi.patient_investigation_id")->where("pi.appointment_id='".$getAppointments->appointment_id."' and pi.clinic_id='".$getAppointments->clinic_id."' and pi.doctor_id='".$getAppointments->doctor_id."'")->get()->result();


        if($parent_appointment_id == 0 || $parent_appointment_id == NULL)
        {
            $apt_id = $getAppointments->appointment_id;
        }else{
            $get_all_appointments = $this->db->select("*")->from("appointments")->where("(parent_appointment_id='".$parent_appointment_id."' or appointment_id='".$parent_appointment_id."') and appointment_id NOT IN  (".$getAppointments->appointment_id.")")->order_by('appointment_id','DESC')->get()->result();
            
            $previous_patient_id = $get_all_appointments[0]->patient_id;
            $previous_appointment_id = $get_all_appointments[0]->appointment_id;
            $previous_clinic_id = $get_all_appointments[0]->clinic_id;
            $previous_doctor_id = $get_all_appointments[0]->doctor_id;
            
            $apt_id = $get_all_appointments[0]->appointment_id;

            if(count($inv_lineitems)<=0)
            {
                $inv_lineitems = $this->db->select("*")->from("patient_investigation_line_items pil")->join("patient_investigation pi","pil.patient_investigation_id=pi.patient_investigation_id")->where("pi.patient_id='".$previous_patient_id."' and pi.appointment_id='".$previous_appointment_id."' and pi.clinic_id='".$previous_clinic_id."' and pi.doctor_id='".$previous_doctor_id."'")->get()->result();
            }   
        }
        $i=0;
        if(count($inv_lineitems)>0){
            foreach($inv_lineitems as $inl)
            {

                $data['investigations_list'][$i]['patient_investigation_line_item_id'] = $inl->patient_investigation_line_item_id;
                $data['investigations_list'][$i]['patient_investigation_id'] = $inl->patient_investigation_id;
                $data['investigations_list'][$i]['appointment_id'] = $apt_id;
                $data['investigations_list'][$i]['category'] = $inl->category;
                $data['investigations_list'][$i]['checked'] = $inl->checked;
                $data['investigations_list'][$i]['investigation_id'] = $inl->investigation_id;
                $data['investigations_list'][$i]['investigation_code'] = $inl->item_code;
                $data['investigations_list'][$i]['investigation_name'] = $inl->investigation_name;
                $data['investigations_list'][$i]['mrp'] = $inl->mrp;
                $i++;
            }
        }else{
            $data['investigation'] = array();
        }

        $this->response(array('code' => '200', 'message' => 'Latest Investigations List', 'result' => $data, 'requestname' => $method));
    }

    // Get Discharge Summary
    public function get_dischargeSummary($parameters, $method, $user_id) {

        // Get Course in Hospital information for an appointment
        $this->db->select('appointment_id, patient_id, doctor_id, clinic_id, course_in_hospital');
        $this->db->from('appointments');
        $this->db->where('appointment_id =',$parameters['appointment_id']);
        $this->db->where('clinic_id =',$parameters['clinic_id']);

        $getCourseInHospital = $this->db->get()->row();

        if(count($getCourseInHospital) > 0) {
            $this->response(array('code' => '200', 'message' => 'Discharge Summary', 'result' => $getCourseInHospital, 'requestname' => $method));
        }else{
            $this->response(array('code' => '202', 'message' => 'No Data Found', 'result' => NULL, 'requestname' => $method));
        }
    }

    
    public function patientProfile($parameters, $method, $user_id) {

        $getPatient = $this->db->select("*")->from("patients a")->join("districts b","a.district_id=b.district_id","left")->join("states c","a.state_id=c.state_id","left")->where("a.patient_id='".$parameters['patient_id']."'")->get()->row();
        
        $data['patient_id'] = $getPatient->patient_id;
        $data['title'] = $getPatient->title;
        $data['first_name'] = $getPatient->first_name;
        $data['middle_name'] = $getPatient->middle_name;
        $data['last_name'] = $getPatient->last_name;
        $data['umr_no'] = $getPatient->umr_no;
        $data['gender'] = $getPatient->gender;
        if(empty($getPatient->date_of_birth)) { 
            $data['date_of_birth'] = "";
        } else 
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
    
    public function patientHealthRecords($parameters, $method, $user_id) {

        $getAppointments = $this->db->select("a.*,b.*,c.*")->from("appointments a")->join("doctors b","a.doctor_id=b.doctor_id")->join("department c","b.department_id=c.department_id")->where("a.patient_id='".$parameters['patient_id']."'")->order_by("a.appointment_id","DESC")->get()->result();
        
        $a=0;
        foreach($getAppointments as $ga)
        {
            $data['health_records'][$a]['appointmet_id'] = $ga->appointment_id;
            $data['health_records'][$a]['doctor_name'] = "Dr.".$ga->first_name." ".$ga->last_name;
            $data['health_records'][$a]['designation'] = $ga->department_name;
            $data['health_records'][$a]['appointment_date'] = date('d-M-Y',strtotime($ga->appointment_date));
            $data['health_records'][$a]['appointment_time'] = $ga->appointment_time_slot;
            $a++;
        }   
        $this->response(array('code' => '200', 'message' => 'Patient Appoitment Records', 'result' => $data, 'requestname' => $method));
    }
    
    public function healtRecordPDF($parameters, $method, $user_id) {

        $data['getAppointment'] = $this->db->select("a.*,b.*,c.*,c.address as clinic_address,d.*,b.first_name as dfname,b.last_name as dlname,e.*,e.first_name as pfname,e.last_name as plname,e.middle_name as pmname,e.umr_no as umrno")->from("appointments a")->join("doctors b","a.doctor_id=b.doctor_id")->join("clinics c","a.clinic_id = c.clinic_id")->join("department d","b.department_id = d.department_id")->join("patients e","a.patient_id=e.patient_id")->where("a.appointment_id='".$parameters['appointment_id']."' ")->get()->row();
        
        $data['pvrdt'] = $this->db->select("DISTINCT(vital_sign_recording_date_time) as vital_sign_time")->  from("patient_vital_sign")->where("appointment_id = '".$parameters['appointment_id']."'")->get()->result();
        
        $data['consent_forms'] = $this->db->select("*")->from("patient_consent_forms")->where("appointment_id = '".$parameters['appointment_id']."'")->get()->result();
        
        
        $patient_name = $parameters['appointment_id']."_".$data['getAppointment']->pfname."_".$data['getAppointment']->plname."_".$data['getAppointment']->appointment_date;
        $this->load->library('M_pdf');
        $html = $this->load->view('patients/health_cards_pdf', $data, true);
        $pdfFilePath = strtolower(str_replace(" ", "_", $patient_name)) . ".pdf";
        $this->load->library('M_pdf');
        //$this->m_pdf->pdf->SetAutoFont();
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output("./uploads/health_cards/" . $pdfFilePath, "F");
        $param['health_card'] = base_url() . 'uploads/health_cards/' . $pdfFilePath;
        
        $this->response(array('code' => '200', 'message' => 'Patient Appoitment Records', 'result' => $param, 'requestname' => $method));
    }
    

    public function doctorPatientComments($parameters, $method, $user_id)
    {
        $patient_id = $parameters['patient_id'];
        $data['doctor_comment'] = $parameters['doctor_comments'];
        
        // Check doctor's comment on the concern patient
        $this->Generic_model->updateData('doctor_patient',$data,array('patient_id'=>$patient_id,'doctor_id'=>$user_id));
        //$this->Generic_model->updateData('patients',$data,array('patient_id'=>$patient_id));
        $this->response(array('code' => '200', 'message' => 'Docotor Comments Update Done', 'result' => $data, 'requestname' => $method));
    }


    public function patientdoctor_profile($parameters, $method, $user_id){
        $patient_id = $parameters['patient_id'];
        $doctor_appoinment_list = $this->db->select("doctor_id")->from("appointments")->where("patient_id ='".$patient_id."'")->group_by("doctor_id")->get()->result();
        $j=0;
        foreach($doctor_appoinment_list as $doctor_val){            
            $doctor_list = $this->db->select("*")->from("doctors")->where("doctor_id ='".$doctor_val->doctor_id."'")->get()->row();
            $doctor_degrees_list = $this->db->select("*")->from("doctor_degrees")->where("doctor_id ='".$doctor_val->doctor_id."'")->get()->result();
            $degree_name = array();
            foreach($doctor_degrees_list as $degree_val){
                $degree_name[] = $degree_val->degree_name;
            }
            $doctor_degree = implode(",", $degree_name);
            $data['doctors'][$j]['doctor_id'] = $doctor_val->doctor_id;
            $data['doctors'][$j]['specialization'] =$doctor_list->speciality ;
            $data['doctors'][$j]['name'] = $doctor_list->first_name." ".$doctor_list->last_name;
            $data['doctors'][$j]['experience'] = $doctor_list->experience ;
            $data['doctors'][$j]['about_doctor'] = $doctor_list->about ;
            $data['doctors'][$j]['contact'] = $doctor_list->mobile ;
            $data['doctors'][$j]['visit_time'] = "" ;
            $data['doctors'][$j]['known_languages'] = $doctor_list->languages ;
            $data['doctors'][$j]['dealt_deseases'] = $doctor_list->diseases_dealt;
            $data['doctors'][$j]['degrees'] = $doctor_degree;

            $achievements = $doctor_list->acheivements;
            $arra_ach = explode(",", $achievements);
            
            if($achievements == "" || $achievements == "null"){
                $data['doctors'][$j]['achievements'] = array();
            }else{
                $data['doctors'][$j]['achievements'] = $arra_ach;
                
            }
            
            $data['doctors'][$j]['membership'] = $doctor_list->membership_in;
            $data['doctors'][$j]['address'] = $doctor_list->address;
            $data['doctors'][$j]['doctor_image'] =  base_url("uploads/profile_image/".$doctor_list->profile_image."");
            $j++;
        }
        $this->response(array('code' => '200', 'message' => 'Docotor Profile', 'result' => $data, 'requestname' => $method));
    }
    
    public function getSavedRecursiveForm($parameters, $method, $user_id)
    {
        $form_type = $parameters['form_type'];
        $department_id = $parameters['department_id'];
        $patient_id = $parameters['patient_id'];
        $appointment_id = $parameters['appointment_id'];
        $doctor_id = $parameters['doctor_id'];

        $get_form = $this->db->select('GROUP_CONCAT(form_id) as form_ids')->from('patient_form')->where('form_type="'.$form_type.'" and patient_id="'.$patient_id.'" and doctor_id="'.$doctor_id.'"')->get()->row();
        
        if($get_form->form_ids!=""){
           $presenting_symptoms = $this->db->select("*")->from("patient_presenting_symptoms ps")
           ->join("patient_ps_line_items psl","ps.patient_presenting_symptoms_id = psl.patient_presenting_symptoms_id")->where("appointment_id = '".$parameters['app_id'] . "' and form_id NOT IN(".$get_form->form_ids.")")->get()->result();

       }
       else{
          $presenting_symptoms = $this->db->select("*")->from("patient_presenting_symptoms ps")
          ->join("patient_ps_line_items psl","ps.patient_presenting_symptoms_id = psl.patient_presenting_symptoms_id")->where("appointment_id = '".$parameters['app_id'] . "'")->get()->result();
      }


      $ps = 0;
      if (count($presenting_symptoms) > 0) {

        foreach ($presenting_symptoms as $psl) {
           if($psl->form_id != 0){

              $form_name = $this->Generic_model->getSingleRecord('form',array('form_id'=>$psl->form_id),'');
              $form_type = $form_name->form_type;
          }
          else{
              $form_name = "Generic";
              $form_type = "HOPI";
          }
          $data['forms'][$ps]['form_id'] = $psl->form_id;
          $data['forms'][$ps]['form_name'] = $form_name->form_name;
          $data['forms'][$ps]['form_type'] = $form_type;
          $data['forms'][$ps]['type'] = 1;
          $data['forms'][$ps]['appointment_id'] = (string)$parameters['app_id'];
          $data['forms'][$ps]['date'] = date("Y-m-d H:i:s",strtotime($psl->created_date_time));

          $ps++;
      }
  } 


  $gform_data = $this->Generic_model->getAllRecords('patient_form',array('form_type'=>$form_type,'patient_id'=>$patient_id,'doctor_id'=>$doctor_id),array('field'=>'created_date_time','type'=>'desc'));

  $gform_data_appointments = $this->Generic_model->getAllRecords('patient_form',array('form_type'=>$form_type,'patient_id'=>$patient_id,'doctor_id'=>$doctor_id,'appointment_id'=>$appointment_id),array('field'=>'created_date_time','type'=>'desc'));

  if(count($gform_data)>0){
    $k = $ps;
    foreach ($gform_data as $result) {
        $form_name = $this->Generic_model->getSingleRecord('form',array('form_id'=>$result->form_id),'');
        $data['forms'][$k]['patient_form_id'] = $result->patient_form_id;
        $data['forms'][$k]['form_type'] = $result->form_type;
        $data['forms'][$k]['form_id'] = $result->form_id;
        $data['forms'][$k]['form_name'] = $form_name->form_name;
        $data['forms'][$k]['appointment_id'] = $result->appointment_id;
        $data['forms'][$k]['date'] = $result->created_date_time;
        $data['forms'][$k]['type'] =2;
        $k++;
    }
}

if(empty($data['forms'])){
   $data['forms'] = array();
}

if($department_id == '' || $department_id == NULL || $department_id == 0)
{
    $departcondition = "";
}else{
    $departcondition=$department_id;
}

if($form_type=='Systemic Examination')
{
    if($departcondition !='' || $departcondition != NULL || $departcondition !=0)
    {
        $systemic_examination = $this->db->select("b.form_id,b.form_name,b.form_type,a.department_id")->from("form_department a")->join("form b","b.form_id=a.form_id")->where("b.form_type='" . $form_type . "' and a.department_id='".$departcondition."'")->get()->result();

    }

    if(count($systemic_examination)>0){
        $i=0;
        foreach($systemic_examination as $value){
            $data['para'][$i]['form_id'] = $value->form_id;
            $data['para'][$i]['form_name'] = $value->form_name;
            $data['para'][$i]['form_type'] = $value->form_type;
            $data['para'][$i]['department_id'] = $value->department_id;
            $i++;
        }
    }else{
        $data['para'] = array();
    }

}else{
    $ak=0;
    foreach($gform_data_appointments as $result1)
    {
        $form_name1 = $this->Generic_model->getSingleRecord('form',array('form_id'=>$result1->form_id),'');
        $form_names[] = "'".$form_name1->form_name."'";
    }
    if(count($form_names)>0){
        $form_names = implode(",",$form_names);
        $systemic_examination = $this->db->select("form_id,form_name,form_type,department_id")->from("form")->where("form_type='" . $form_type . "' and department_id='0' and form_name NOT IN (".$form_names.")")->get()->result();
        if(count($systemic_examination)>0)
        {
            $i=0;
            foreach($systemic_examination as $value){
                $data['para'][$i]['form_id'] = $value->form_id;
                $data['para'][$i]['form_name'] = $value->form_name;
                $data['para'][$i]['form_type'] = $value->form_type;
                $data['para'][$i]['department_id'] = $value->department_id;
                $i++;
            }
        }else{
            $data['para']=array();
        }
    }else{
        $systemic_examination = $this->db->select("form_id,form_name,form_type,department_id")->from("form")->where("form_type='" . $form_type . "' ".$departcondition."")->get()->result();
        if(count($systemic_examination)>0){
            $i=0;
            foreach($systemic_examination as $value){
                $data['para'][$i]['form_id'] = $value->form_id;
                $data['para'][$i]['form_name'] = $value->form_name;
                $data['para'][$i]['form_type'] = $value->form_type;
                $data['para'][$i]['department_id'] = $value->department_id;
                $i++;
            }
        }else{
            $data['para'] = array();
        }
    }



}


$this->response(array('code' => '200', 'message' => 'form_details', 'result' => $data, 'requestname' => $method));
}


public function getSavedRecursiveForm_pdf($parameters, $method, $user_id)
{
    $patient_form_id = $parameters['patient_form_id'];
    $patient_id = $parameters['patient_id'];
    $appointment_id = $parameters['appointment_id'];
    $doctor_id = $parameters['doctor_id'];

    $data['patient_list'] = $this->Generic_model->getSingleRecord('patients',array('patient_id'=>$patient_id),'');
    $pform_data = $this->Generic_model->getSingleRecord('patient_form',array('patient_form_id'=>$patient_form_id,'patient_id'=>$patient_id,'doctor_id'=>$doctor_id),'');

    $data['plform_data'] = $this->db->select("DISTINCT(section_id) as section_id")->from(" patient_form_line_items")->where("patient_form_id='".$pform_data->patient_form_id."'")->order_by("section_id","asc")->get()->result();

    $form_name = $this->Generic_model->getSingleRecord('form',array('form_id'=>$pform_data->form_id),'');
    $data['form_name'] = $form_name->form_name;
    $data['patient_form_id'] = $patient_form_id;

    $pdf_name_val = str_replace(" ", "_", $data['patient_list']->first_name);
    $html = $this->load->view('patients/patient_form_pdf', $data, true);
    $pdfFilePath = strtolower($pdf_name_val . "" . date('md')) . ".pdf";
    $data['patient_form_pdf'] = $pdfFilePath;
    $this->load->library('M_pdf');
    $this->m_pdf->pdf->WriteHTML($html);
            //download it.
    $this->m_pdf->pdf->Output("./uploads/patient_form/" . $pdfFilePath, "F");
    $data_1['procedure_patient_pdf'] = base_url("uploads/patient_form/" . $pdfFilePath . "");

    $this->response(array('code' => '200', 'message' => 'form_details', 'result' => $data_1, 'requestname' => $method));
}

public function deletePatienForm($parameters, $method, $user_id)
{
    $patient_form_id = $parameters['patient_form_id'];
    $ok = $this->Generic_model->deleteRecord('patient_form_line_items',array('patient_form_id'=>$patient_form_id));
    if($ok==1)
    {
        $this->Generic_model->deleteRecord('patient_form',array('patient_form_id'=>$patient_form_id));
    }
    $data['result'] = 'Successfully Deleted';
    $this->response(array('code' => '200', 'message' => 'Form Deleted Successfully', 'result' => $data, 'requestname' => $method));
}
     //taking appreciation and feedback from patient 
public function feedback_submit($parameters, $method, $user_id) {

    $appreciation = $parameters['appreciation'];
    $feedback = $parameters['feedback'];
    $patient_id = $parameters['patient_id'];
    $doctor_id = $parameters['doctor_id'];
    $clinic_id = $parameters['clinic_id'];

    $get_last_appointment = $this->db->select("appointment_id")->from("appointments")->where("patient_id = '".$patient_id."' and doctor_id='".$doctor_id."' and (status!='drop' or status!='booked')")->order_by("appointment_date","DESC")->get()->row();

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



    $appreciation_info = $this->db->select("*")->from("patient_doctor_appreciation")->where("patient_id='".$user_id."'")->get()->result();

    if(count($appreciation_info)>0){


      $j=0;
      foreach($appreciation_info as $app_info){            
        $doctor_list = $this->db->select("*")->from("doctors")->where("doctor_id ='".$app_info->doctor_id."'")->get()->row();

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

$feedback_info = $this->db->select("*")->from("patient_doctor_feedback")->where("patient_id='".$user_id."'")->get()->result();

if(count($feedback_info)>0){


  $j=0;
  foreach($feedback_info as $fb_info){            
    $doctor_list = $this->db->select("*")->from("doctors")->where("doctor_id ='".$fb_info->doctor_id."'")->get()->row();

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

         //list of health education articles in patient module 
public function health_education_articles($parameters, $method, $user_id) {
    $article_info = $this->db->select("*")->from("health_education")->get()->result();

    if(count($article_info)>0){


        $j=0;
        foreach($article_info as $article){ 

            $data['health_education'][$j]['title'] = $article->article_title;
            $data['health_education'][$j]['posted_on'] = date("d-M-Y",strtotime($article->posted_on));
            $data['health_education'][$j]['posted_by'] = $article->posted_by;
            $data['health_education'][$j]['type'] = $article->article_type;
            $data['health_education'][$j]['video'] = $article->video;
            $data['health_education'][$j]['article_id'] = $article->article_id;
            $data['health_education'][$j]['article'] = $article->article_description;

            $get_comments = $this->db->select("*")->from("health_education_comments")->where("article_id='".$article->article_id."'")->get()->result();
            if(count($get_comments)>0){
                $k=0;
                foreach ($get_comments as $comment) {
                    $get_patient = $this->db->select("*")->from("patients")->where("patient_id='".$comment->patient_id."'")->get()->row();
                    if ($get_patient->photo != NULL) {
                        $path = base_url() . 'uploads/patients/' . $patient->photo;
                    } else {
                        $path = NULL;
                    }
                    $data['health_education'][$j]['comments'][$k]['patient_image'] = $path;
                    $data['health_education'][$j]['comments'][$k]['patient_name'] = $get_patient->first_name." ".$get_patient->last_name;
                    $data['health_education'][$j]['comments'][$k]['comment'] = $comment->comments;
                    $data['health_education'][$j]['comments'][$k]['comment_id'] = $comment->health_education_comment_id;

                    $get_reply = $this->db->query("select * from health_education_comment_reply where comment_id='".$comment->health_education_comment_id."'")->result();
                    if(count($get_reply)>0){
                        $l=0;
                        foreach ($get_reply as $key => $rs) {
                            $data['health_education'][$j]['comments'][$k]['reply_list'][$l]['comment'] = $rs->reply;
                            $l++;
                        }

                    }
                    else{
                        $data['health_education'][$j]['comments'][$k]['reply_list'] = [];
                    }
                    $k++;
                }
            }
            else{
                $data['health_education'][$j]['comments']=[];
            }
            $j++;
        }
    }
    else{
        $data['health_education'] = []; 
    }
    $this->response(array('code' => '200', 'message' => 'Health Education Articles', 'result' => $data, 'requestname' => $method));
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

    //inserting reply to comments 
public function health_education_comment_reply($parameters, $method, $user_id) {

    $com['comment_id'] = $parameters['comment_id'];
    $com['reply'] = $parameters['reply'];
    $com['created_date_time'] = date("Y-m-d H:i:s");
    $this->Generic_model->insertData('health_education_comment_reply', $com);
    $this->response(array('code' => '200', 'message' => 'Reply Inserted Successfully', 'requestname' => $method));
}


    //getting patient feedback status
public function feedback_status($parameters, $method, $user_id) {

   $get_last_appointment = $this->db->select("appointment_id,d.first_name,d.last_name,d.doctor_id")->from("appointments a")->join("doctors d","a.doctor_id = d.doctor_id")->where("a.patient_id = '".$parameters['patient_id']."' and (a.status!='drop' or a.status='booked')")->order_by("a.appointment_date","DESC")->get()->row();
   $get_count = $this->db->select("*")->from("appointments")->where("patient_id='".$parameters['patient_id']."' and doctor_id='".$get_last_appointment->doctor_id."'")->get()->num_rows();

   $get_app_status = $this->db->select("appreciation_status")->from("patient_doctor_appreciation")->where("appreciation_status='1'")->get()->num_rows();
   $get_fb_status = $this->db->select("feedback_status")->from("patient_doctor_feedback")->where("feedback_status='1'")->get()->num_rows();
   if($get_app_status > 0 || $get_fb_status >0){
    $status = 1;
}
else{
    $status = 0;
}
$get_skip_status = $get_fb_status = $this->db->select("skip_status")->from("patient_doctor_feedback")->where("appointment_id='".$get_last_appointment->appointment_id."' and skip_status='1'")->get()->num_rows();
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

    //Patient Transactions
public function patientTransactions($parameters, $method, $user_id)
{
    $patient_id = $parameters['patient_id'];

    $patient_billings = $this->Generic_model->getAllRecords('billing',array('patient_id'=>$patient_id),array('field'=>'billing_id','type'=>'desc'));

    $b=0;
    foreach($patient_billings as $key => $billing)
    {
        $amount = 0;
        $data['transaction'][$b]['biling_id'] = $billing->billing_id;
        $data['transaction'][$b]['receipt_no'] = $billing->receipt_no;
        $data['transaction'][$b]['invoice_no'] = $billing->invoice_no;
        $data['transaction'][$b]['invoice_no_alias'] = $billing->invoice_no_alias;
        $data['transaction'][$b]['billing_type'] = $billing->billing_type;
        $data['transaction'][$b]['date'] = date('d-M-Y',strtotime($billing->created_date_time));
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

      //get form from symtoms
public function hopi_symptoms($parameters, $method, $user_id){
    $symptoms = $this->db->select("*")->from("hopi_synonyms hs")->join("form f","hs.form_id = f.form_id")->order_by("f.form_id")->get()->result();
    if(count($symptoms)>0){
        $i=0;
        foreach ($symptoms as $value) {
            $data['hopi_symptoms'][$i]['form_id'] = $value->form_id;
            $data['hopi_symptoms'][$i]['form_name'] = $value->form_name;
            $data['hopi_symptoms'][$i]['symptom_name'] = $value->synonym;
            $data['hopi_symptoms'][$i]['position'] = $value->position;
            $data['hopi_symptoms'][$i]['key'] = $value->hopi_synonym_id;
            $i++;
        }
    }
    $this->response(array('code' => '200', 'message' => 'HOPI Symptoms', 'result' => $data, 'requestname' => $method));
}

    // Getting patient immunization list. If empty inserting 
public function immunization_list($parameters, $method, $user_id){

    $pageType = '';

    if(isset($parameters['type']) == 'profile'){
        $pageType = 'profile';
    }

        // Get patient date of birth
    $patient_dob = $parameters['dob'];
    $today = date("Y-m-d");

        // Calculate the patient's age by current date in no. of days 
    $current_date = date_create($today);
    $patient_dob = date_create($parameters['dob']);    
    $days = date_diff($patient_dob,$current_date);
    $age_in_no_of_days = $days->format("%a");

    $vaccine_info = $this->db->select('DISTINCT(no_of_days),from_age, age_unit')->from('vaccine')->order_by('position','ASC')->get()->result();
    $i=0;
    foreach ($vaccine_info as  $vaccine_value) {

        if($vaccine_value->from_age ==0){
            $age =  "BIRTH";
        }else{
            $age =  $vaccine_value->from_age." ".$vaccine_value->age_unit;
        }

        $im_data['immunazation_list'][$i]['age'] = $age;

            // Get immunization standard time table
        $immunization_time_table = $this->db->select('vaccine_id, vaccine, parent_vaccine_id, no_of_days, position')->from('vaccine')->where('no_of_days="'.$vaccine_value->no_of_days.'"')->order_by('position','ASC')->get()->result();

        if(count($immunization_time_table) > 0){
           $patient_info = $this->Generic_model->getSingleRecord('patients',array('patient_id'=>$parameters['patient_id']),'');
           $im_data['patient_id'] = $patient_info->patient_id;
           $im_data['patient_name'] = $patient_info->first_name." ".$patient_info->last_name;
           $im_data['umr_no'] = $patient_info->umr_no;
           $im_data['dob'] = $patient_info->date_of_birth;
           $j=0;
           foreach($immunization_time_table as $key => $vaccineRecord){

            $status = "";

                    // check if there is any record in patient_vaccine concern to the patient_id & vaccine_id
            $patient_vaccine = $this->db->select('*')->from('patient_vaccine')->where('patient_id = "'.$parameters['patient_id'].'" and vaccine_id = "'.$vaccineRecord->vaccine_id.'"')->get()->row();

                    // if record exists then get given date and send its status as 'Completed' in the JSON.
                    if(count($patient_vaccine) > 0){ // Vaccines completed
                        $due_on = $patient_vaccine->due_on;
                        $given_on = $patient_vaccine->given_on;
                        $status = 'Completed';
                    }else{ // Vaccines not given yet
                        $given_on = '';

                        // get due date
                        if($vaccineRecord->parent_vaccine_id != ''){// check if the related vaccine exists
                            // calculate due date w.r.to related vaccine given date
                            $related_vaccine_given_date = $this->db->select('PV.given_on, V.no_of_days')->from('patient_vaccine PV')->join('vaccine V','PV.vaccine_id = V.vaccine_id')->where('PV.patient_id = "'.$parameters['patient_id'].'" AND PV.vaccine_id = "'.$vaccineRecord->parent_vaccine_id.'"')->get()->row();

                            if(count($related_vaccine_given_date) > 0){
                                // calculate w.r.to the given date and age of the vaccine in no. of days
                                // no. of the days to add to the given date of the related vaccine = age of the given vaccine - age of the vaccine from the patient's dob
                                $days_to_add = intval($vaccineRecord->no_of_days) - intval($related_vaccine_given_date->no_of_days);
                                $due_on = date('Y-m-d', strtotime($related_vaccine_given_date->given_on. ' + '.intval($days_to_add).' days'));

                            }else{
                                // calculate w.r.to the patient age in no. of days and age of the vaccine
                                // add the vaccine's age in no. of days to patients dob and get the date
                                $due_on = date('Y-m-d', strtotime($parameters['dob']. ' + '.intval($vaccineRecord->no_of_days).' days'));

                            }

                            // get status
                            // status becomes pending if the due on is less than curent date
                            // compare with current date
                            $vaccineDueDate = new DateTime($due_on);
                            $todayDate = new DateTime($today);

                            if($vaccineDueDate < $todayDate){
                                $status = 'Pending';
                            }else{
                                // get inline Vaccine status
                                $inlineVaccine = $this->db->select("MIN(no_of_days) as 'no_of_days'")->from("vaccine")->where("no_of_days >= '".intval($age_in_no_of_days)."'")->limit(1,10)->get()->row();
                                
                                if($inlineVaccine->no_of_days == $vaccineRecord->no_of_days){
                                    if(date('m') == date('m', strtotime($due_on))){
                                        $status = 'Inline';    
                                    }else{
                                        $status = 'Upcoming';    
                                    }
                                }else{
                                    $status = 'Upcoming';
                                }       
                            }
                        }

                    }

                    // add to JSON
                    if($pageType == 'profile'){
                        if($status == 'Pending' || $status == 'Inline'){
                            $im_data['immunazation_list'][$i]['vaccine_list'][$j]['vaccine_id'] = $vaccineRecord->vaccine_id;
                            $im_data['immunazation_list'][$i]['vaccine_list'][$j]['vaccine'] = $vaccineRecord->vaccine;
                            $im_data['immunazation_list'][$i]['vaccine_list'][$j]['due_on'] = $due_on;
                            $im_data['immunazation_list'][$i]['vaccine_list'][$j]['given_on'] = $given_on;
                            $im_data['immunazation_list'][$i]['vaccine_list'][$j]['status'] = $status;
                            $j++;
                        }                      
                    }else{
                        $im_data['immunazation_list'][$i]['vaccine_list'][$j]['vaccine_id'] = $vaccineRecord->vaccine_id;
                        $im_data['immunazation_list'][$i]['vaccine_list'][$j]['vaccine'] = $vaccineRecord->vaccine;
                        $im_data['immunazation_list'][$i]['vaccine_list'][$j]['due_on'] = $due_on;
                        $im_data['immunazation_list'][$i]['vaccine_list'][$j]['given_on'] = $given_on;
                        $im_data['immunazation_list'][$i]['vaccine_list'][$j]['status'] = $status;
                        $j++;
                    }                  
                }

                if($pageType == 'profile'){
                    if(count($im_data['immunazation_list'][$i]['vaccine_list']) == 0){
                        unset($im_data['immunazation_list'][$i]);
                    }else{
                        $i++;
                    }
                }else{
                    $i++;    
                }
                
            }

        }

        $this->response(array('code' => '200', 'message' => 'immunization List', 'result' => $im_data, 'requestname' => $method));

    }


    //updating patient vaccination details. 
    public function update_vaccine($parameters, $method, $user_id){

        $res = 0;
        $message = "";

        if($parameters['type'] == 'del'){ // Deleting the existing record
            $res = $this->Generic_model->deleteRecord('patient_vaccine',array('patient_id'=>$parameters['patient_id'], 'vaccine_id'=>$parameters['vaccine_id']));
            $message = "Record deleted successfully";
        }else{ // Inserting a new record or Updating the existing record
            // check if the record exists
            // if exist then update it with the parameters data
            $patientVaccineRec = $this->Generic_model->getAllRecords('patient_vaccine',array('patient_id'=>$parameters['patient_id'],'vaccine_id'=>$parameters['vaccine_id']));

            // remove type parameter from the array...
            unset($parameters['type']);

            if(count($patientVaccineRec) > 0) { // record existing -> update record with the new data
                $res = $this->Generic_model->updateData('patient_vaccine', $parameters, array("patient_id"=>$parameters['patient_id'],"vaccine_id"=>$parameters['vaccine_id']));
                if($res){
                    $message = "Changes updated successfully";                    
                }else{
                    $message = 'Attempt failed, please try again';
                }
                
            }else{ // no record found -. Insert new record
                $res = $this->Generic_model->insertData('patient_vaccine',$parameters);
                if($res){
                    $message = "New vaccine information saved successfully";
                }else{
                    $message = 'Attempt failed, please try again';
                }
            }
        }

        if($res){
            $this->response(array('code' => '200', 'message' => $message, 'requestname' => $method));    
        }else{
            $this->response(array('code' => '201', 'message' => $message, 'requestname' => $method));    
        }

    }

    //update course in hospital
    public function submit_discharge_summary($parameters, $method, $user_id) {
        $course['course_in_hospital']= $parameters['course_in_hospital'];
        $this->Generic_model->updateData('appointments', $course, array("appointment_id"=>$parameters['appointment_id']));
        $this->response(array('code' => '200', 'message' => 'Updated Successfully', 'requestname' => $method));
    }


	// Patient Login OTP
    public function patient_login_otp($parameters, $method, $user_id) {
        $check_user = $this->db->select("*")->from("patients")->where("mobile='".$parameters['mobile']."' ")->get()->row();

        if (count($check_user) > 0) {
            $last_four_digits = str_replace(range(0, 9), "*", substr($check_user->mobile, 0, -4)) . substr($check_user->mobile, -4);
            $random_otp = mt_rand(100000, 999999);
            $mobile = trim($check_user->mobile);
            $message = "<#> Your verification code  is : ".$random_otp." \n NeUL8p35WG0";
            sendsms($mobile, $message);

			//$patient_update['fcm_id'] = $parameters['fcmId'];
            $patient_update['device_id'] = $parameters['deviceid'];

            $this->Generic_model->updateData('patients',$patient_update,array('patient_id'=>$check_user->patient_id));

            $patient_otp_check = $this->db->select("*")->from("patient_otp")->where("patient_id='".$check_user->patient_id."' ")->get()->row();
            if(count($patient_otp_check)>0)
            {
                $patient_otp_update['otp'] = $random_otp;
                $this->Generic_model->updateData('patient_otp',$patient_otp_update,array('patient_id'=>$check_user->patient_id));
            }else{
                $patient_otp_insert['patient_id'] = $check_user->patient_id;
                $patient_otp_insert['otp'] = $random_otp;
                $this->Generic_model->insertData('patient_otp',$patient_otp_insert);
            }

            $result['otp'] = $random_otp;
            $result['user_id'] = $check_user->patient_id;
            $result['user_name'] = $check_user->first_name." ".$check_user->last_name;
            $this->response(array('code' => '200', 'message' => 'OTP Sent On Mobile ' . $last_four_digits, 'result' => $result, 'requestname' => $method));
        } else {
            $this->response(array('code' => '404', 'message' => 'User Does Not Exist'), 200);
        }
    }

    public function verified_otp($parameters,$method,$user_id){
        $patient_id=$parameters['user_id'];
        $mobile=$parameters['mobile'];
        $doctor_ids = $this->db->select("GROUP_CONCAT(DISTINCT(doctor_id)) as doctor")->from("appointments")->where("patient_id='".$patient_id."' ")->get()->row();

        $doctors = $this->db->select("*")->from("doctors")->where("doctor_id in (".$doctor_ids->doctor.") ")->get()->result();

        $d=0;
        foreach($doctors as $value)
        {
         $profile_image = $value->profile_image;
         if($profile_image == NULL || $profile_image == "")
         {
            $doctor_image = NULL;
        }else{
            $doctor_image = base_url().'uploads/profile_image/'.$profile_image;
        }

        $doctorDegree = $this->db->select("GROUP_CONCAT(degree_name) as doctor_degree")->from("doctor_degrees")->where("doctor_id='".$value->doctor_id."' ")->get()->row();
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

    $patient_details = $this->Generic_model->getSingleRecord('patients', array('patient_id' => $parameters['user_id']),'');

    $param['patient']['patient_id'] = $patient_details->patient_id;
    $param['patient']['name'] = $patient_details->first_name." ".$patient_details->last_name;
    $param['patient']['UMRNo'] = $patient_details->umr_no;
    $param['patient']['Gender'] = $patient_details->gender;
    $param['patient']['age'] = $patient_details->age;
    $otp_up['otp'] = "";
    $ok = $this->Generic_model->updateData('patient_otp',$otp_up,array('patient_id' => $parameters['user_id']));
    if ($ok == 1) {
        $this->response(array('code' => '200', 'message' => 'Successfull','result' =>$param, 'requestname' => $method));
    } else {
        $this->response(array('code' => '404', 'message' => 'Failed'), 200);
    }
}

    //after otp verification updating new password(vikram)
public function forgot_password_updatxe($parameters, $method, $user_id,$h) {

    $param['password'] = md5($parameters['password']);
    $ok = $this->Generic_model->updateData('users', $param, array('user_id' => $parameters['user_id']));
    if ($ok == 1) {
        $this->response(array('code' => '200', 'message' => 'Successfully Password Changed ', 'requestname' => $method));
    } else {
        $this->response(array('code' => '404', 'message' => 'Update Password Failed'), 200);
    }
}

public function get_help($parameters,$method,$user_id){

    extract($parameters);

    $helpRecords['tutorials'] = $this->db->select('VT.video_tutorial_id, VT.module_name, VT.video_link, CONCAT("'.base_url().'uploads/tutorials/",VT.thumbnail_path) as thumbnail_path')->from('video_tutorial VT')->join('video_tutorial_profile VTP','VT.video_tutorial_id = VTP.video_tutorial_id')->where('VTP.profile_id =', $profile_id)->get()->result_array();

    if(count($helpRecords) > 0) {
        $this->response(array('code' => '200', 'message' => 'Help Records','result' =>$helpRecords, 'requestname' => $method));
    }else{
        $this->response(array('code' => '201', 'message' => 'No Help Records Found','result' =>$helpRecords, 'requestname' => $method));
    }
}


public function app_version($parameters,$method,$user_id)
{
    $result=$this->Generic_model->getSingleRecord("app_version",array("app_category"=>"Doctors"),$order='');

    if(count($result)>0)
    {
        $data['app_id']=$result->app_id;
        $data['app_version_id']=$result->app_version_id;
        $data['app_version_name']=$result->app_version_name;
        $data['contact_number']=$result->contact_number;
        $data['updated_date_time']=$result->updated_date_time;

        if(isset($parameters['doctor_id'])){
                // Get department of the doctor
            $department = $this->db->select('department_id')->from('doctors')->where('doctor_id=',$parameters['doctor_id'])->get()->row();

                // Get promotional pic belongs to Generic and above pulled Department ID
            $this->db->select('promotional_pic_id, department_id, pharma_company_id, pic_name');
            $this->db->from('promotional_pics');
            $this->db->where('department_id=',$department->department_id);
            $this->db->or_where('department_id=','26');
            $this->db->order_by('promotional_pic','RANDOM');
            $this->db->limit(1);
            $promotionalPic = $this->db->get()->row();
            $promotionalPic->doctor_id = $parameters['doctor_id'];
            if($promotionalPic->pic_path == NULL || $promotionalPic->pic_path == ''){
                $promotionalPic->pic_path = NULL; 
            }else{
                $promotionalPic->pic_path = base_url().'uploads/promotional_pics/'.$promotionalPic->pic_name; 
            }

            if(count($promotionalPic) > 0){
                $data['promotionalPic'] = $promotionalPic;
            }else{
                $data['promotionalPic'] = '';
            }
        }

        $this->response(array('code'=>'200','message' => 'appversion','result'=>$data,'requestname'=>$method));

    }else{

        $this->response(array('code' => '404','message' => 'appversion', 'Authentication Failed'), 200);
        
    }

}

}

?> 