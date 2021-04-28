<?php

defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
ini_set('memory_limit', '-1');
require APPPATH . '/libraries/REST_Controller.php';

class Api_citizen extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('file');
        $this->load->library('PHPMailer');
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
                $user_reg['email_id'] = $fdata->email_id;
                $user_reg['mobile'] = $fdata->mobile;
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
                $to = $user_reg['email_id'];
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
               // $patient_reg['date_of_birth'] = date('Y-m-d', strtotime($fdata->date_of_birth));
                $patient_reg['age'] = $fdata->age;
                $patient_reg['age_unit'] = strtoupper(substr($fdata->age_unit, 0, 1));
                $patient_reg['mobile'] = $fdata->mobile;
                $patient_reg['alternate_mobile'] = $fdata->phone;
                $patient_reg['email_id'] = $fdata->email_id;
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
                //$this->Generic_model->insertData('patient_condition_type',$patient_con);


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
              //  $this->mail_send->content_mail_ncl_all($from, $to, $subject, '', '', $header);
                /* End Of sending Mail */



                $result = array('code' => '200', 'message' => 'successfull', 'result' => $param, 'requestname' => $fdata->requestname);

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
                $patient_update['mobile'] = $fdata->mobile;
                $patient_update['alternate_mobile'] = $fdata->alternate_mobile;
                $patient_update['email_id'] = $fdata->email_id;
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

    

	// Patient Login OTP
    public function patient_login_otp($parameters, $method, $user_id) {

        extract($parameters);

        $check_user = $this->db->query("select * from patients where mobile='".$parameters['mobile']."'")->row();

        // Generate OTP
        $last_four_digits = str_replace(range(0, 9), "*", substr($mobile, 0, -4)) . substr($mobile, -4);
        $random_otp = mt_rand(100000, 999999);
        $mobile = trim($mobile);
        $message = "[#] Your verification code  is : ".$random_otp." \n rsavf/Vz89W";

        // Send SMS to the mobile no.
        sendsms($mobile, $message);

        $patientExist = 0;

        if(count($check_user) > 0){
            // Citizen/Patient exists with mobile no.
            // Update patient DB
            $patient_update['device_id'] = $parameters['deviceid'];
            $this->Generic_model->updateData('patients',$patient_update,array('patient_id'=>$check_user->patient_id));
            $patientExist = 1;
        }

        $patient_otp_check = $this->db->query("select * from patient_otp where mobile_no='".$mobile."'")->row();

        if(count($patient_otp_check) > 0)
        {
         $patient_otp_update['otp'] = $random_otp;
         $patient_otp_update['status'] = 1;
         $this->Generic_model->updateData('patient_otp',$patient_otp_update,array('mobile_no'=>$mobile));
     }else{
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
    }else if($patientExist == 0){
        $result['otp'] = $random_otp;
        $result['mobile'] = $mobile;
        $this->response(array('code' => '201', 'message' => 'OTP Sent On Mobile' . $last_four_digits, 'result' => $result, 'requestname' => $method));    
    }
}

	// Patient Resend Login OTP
public function resendOtp($parameters, $method, $user_id) {
    $check_user = $this->db->query("select * from patients where  mobile='".$parameters['mobile']."' ")->row();

    if (count($check_user) > 0) {
     $patient_otp_check = $this->db->query("select * from patient_otp where  patient_id='".$check_user->patient_id."' ")->row();

     $last_four_digits = str_replace(range(0, 9), "*", substr($check_user->mobile, 0, -4)) . substr($check_user->mobile, -4);
     $random_otp = $patient_otp_check->otp;
     $mobile = trim($check_user->mobile);
     $message = "<#> Your verification code  is : ".$random_otp." \n NeUL8p35WG0";
     sendsms($mobile, $message);

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

        $getAppointments = $this->db->query("select a.*,b.*,c.* from appointments a inner join doctors b on (a.doctor_id=b.doctor_id) inner join department c on (b.department_id=c.department_id) where a.patient_id='".$parameters['patient_id']."' order by a.appointment_id DESC")->result();

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
    
    public function healtRecordPDF($parameters, $method, $user_id) {

        $appointment_id = $parameters['appointment_id'];
        //$data['visit']=$visit;
        $data['appointments'] = $this->db->query("select a.*, c.clinic_name,c.clinic_logo,c.address,c.clinic_phone,p.first_name as pname,p.gender,p.age,p.address_line,p.umr_no,p.preferred_language,p.mobile,p.qrcode,d.salutation,d.first_name as dfname,d.last_name as dlname from appointments a left join clinics c on a.clinic_id = c.clinic_id left join patients p on a.patient_id = p.patient_id left join doctors d on a.doctor_id = d.doctor_id where a.appointment_id='" . $appointment_id . "'")->row();
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

        $data['patient_investigations'] = $this->db->query("Select * from patient_investigation_line_items pil inner join investigations inv on (pil.investigation_id=inv.investigation_id) inner join patient_investigation pi on (pil.patient_investigation_id=pi.patient_investigation_id) where  pi.appointment_id='".$appointment_id."'")->result();

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



        
        $data['pdf_settings'] = $pdf_settings = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$data['appointments']->clinic_id."'")->row();

        
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
    $para['health_card'] = base_url() . 'uploads/summary_reports/short-' . $pdfFilePath;

    $this->response(array('code' => '200', 'message' => 'PDF File', 'result' => $para, 'requestname' => $method));
}

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

public function patientdoctor_profile($parameters, $method, $user_id){
    $patient_id = $parameters['patient_id'];
    $doctor_appoinment_list = $this->db->query("select doctor_id from appointments where patient_id ='".$patient_id."'  group by doctor_id")->result();
    $j=0;
    foreach($doctor_appoinment_list as $doctor_val){            
        $doctor_list = $this->db->query("select * from doctors where doctor_id ='".$doctor_val->doctor_id."'")->row();
        $doctor_degrees_list = $this->db->query("select * from doctor_degrees where doctor_id ='".$doctor_val->doctor_id."'")->result();
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

    	 //list of health education articles in patient module 
public function health_education_articles($parameters, $method, $user_id) {
    $article_info = $this->db->query("select * from health_education")->result();

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

            $get_comments = $this->db->query("select * from health_education_comments where article_id='".$article->article_id."'")->result();
            if(count($get_comments)>0){
                $k=0;
                foreach ($get_comments as $comment) {
                    $get_patient = $this->db->query("select * from patients where patient_id='".$comment->patient_id."'")->row();
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
        $data['transaction'][$b]['billing_type'] = $billing->billing_type;
        $data['transaction'][$b]['date'] = date('d-M-Y',strtotime($billing->created_date_time));
        $data['transaction'][$b]['pdf'] = base_url().'uploads/billings/'.$billing->invoice_pdf.'.pdf';
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

    /*
    Function providing complete patient details
    */
    public function patient_profile($parameters,$method,$user_id){

        extract($parameters);

        $patient_rec = $this->db->select('patient_id, title, first_name, middle_name, last_name, CONCAT(title,".",first_name," ",last_name) as full_name, umr_no, gender, date_of_birth, age, occupation, country, mobile, alternate_mobile, email_id as email, address_line as address, district_id, state_id, pincode,  payment_status, clinic_id, referred_by_type, referred_by, country, occupation, photo, qrcode, location, preferred_language, created_date_time as registration_date, status')->from('patients')->where('patient_id =',$patient_id)->get()->row();

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
                $this->response(array('code' => '200', 'message' => 'Successfull','result' =>$param, 'requestname' => $method));
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
            $patient_rec['mobile'] = DataCrypt($mobile,'encrypt');
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

            $patient_details = $this->db->select('patient_id, umr_no, CONCAT(title,".",first_name," ",last_name) as name, mobile')->from('patients')->where('patient_id =',$patient_id)->get()->row();

            $param['doctor'] = NULL;
            $param['patient'] = $patient_details;
            $param['mobile'] = DataCrypt($patient_details->mobile, 'decrypt');

            $this->response(array('code' => '201', 'message' => 'Successfull','result'=>$param, 'requestname' => $method));

        }
    }
}

?>