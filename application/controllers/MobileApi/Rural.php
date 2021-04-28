<?php

defined('BASEPATH') or exit('No direct script access allowed');

error_reporting(0);
ini_set('memory_limit', '-1');

require APPPATH . '/libraries/REST_Controller1.php';
require APPPATH . '/libraries/ImplementJwt.php';

require_once("PaytmChecksum.php");

class Rural extends REST_Controller1
{
    public function __construct()
    {
        parent::__construct();
        // Enable Nurse ASST DB
        $this->objOfJwt = new ImplementJwt();
        header('Content-Type: application/json');
        $this->load->library('mail_send', array('mailtype' => 'html'));
        // $this->db = $this->load->database('third', TRUE);
        $this->load->helper('file');
        $this->load->library('PHPMailer');
        $this->load->library('mail_send', array('mailtype' => 'html'));
        $this->load->library('SMTP');
        $this->load->library('phpqrcode/qrlib');
        $this->load->library('zip');
        $this->load->model('Generic_model');
        $this->load->model('PayModel');
        // $headers = getallheaders();
        // echo "<pre>";print_r($headers);echo "</pre>";
    }

    public function tokenGenerator($rmp_email, $password)
    {

        $TokenData['rmp_email'] = $rmp_email;
        $TokenData['password'] = $password;
        $tokenData['timeStamp'] = Date('Y-m-d h:i:s');
        $jwtToken = $this->objOfJwt->GenerateToken($tokenData);
        $token = json_encode(array('Token' => $jwtToken));

        $this->db->query("UPDATE rural_rmp_registration SET rural_rmp_registration.access_token = '" . $jwtToken . "' WHERE rural_rmp_registration.rmp_email='" . $rmp_email . "'");

        $this->db->query("UPDATE rural_rmp_registration SET rural_rmp_registration.access_token = '" . $jwtToken . "' WHERE rural_rmp_registration.rmp_phone='" . $rmp_email . "'");

        return $jwtToken;
    }



    public function Rural_doctor_registration_post()
    {

        if (!empty(isset($_POST))) {
            extract($_POST);
            $password = md5($rmp_password);

            if ($rmp_name != "" and $rmp_gender != "" and $rmp_age != "" and  $rmp_email != "" and $rmp_phone != "" and $password != "" and $rmp_clinic_name != "" and $rmp_city != "") {

                $validmail = $this->db->query("SELECT rural_rmp_registration.rmp_id FROM rural_rmp_registration WHERE rural_rmp_registration.rmp_email='" . $rmp_email . "'")->result();
                $validphone = $this->db->query("SELECT rural_rmp_registration.rmp_id from rural_rmp_registration where rural_rmp_registration.rmp_phone='" . $rmp_phone . "'")->result();

                if (empty($validmail)) {

                    if (empty($validphone)) {

                        $date = date("Y-m-d:H:i:s");
                        $this->load->library('upload');
                        $filetype = pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION);
                        $profile_pic = "doctor_pro_pic-" . $date . "." . $filetype;
                        $config['upload_path'] = './uploads/rural/doc_profile_pic/';
                        $config['file_name'] = $profile_pic;
                        $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG|webp|WEBP';
                        $this->upload->initialize($config);
                        $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                        $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                        $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                        $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                        $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                        $files = ['jpg', 'png', 'jpeg', 'JPG', 'JPEG', 'webp', 'WEBP'];
                        $filename = $_FILES['profile_pic']['name'];

                        $dp = "doctor_pro_pic.jpg";
                        if (!in_array($filetype, $files)) {
                            $param['type'] = pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION);

                            $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                        } else {

                            $this->upload->do_upload('profile_pic');
                            $fname = $this->upload->data();
                            // $this->db->query("UPDATE rural_rmp_registration SET profile_pic='".$profile_pic."' WHERE rmp_id=1");
                            $dp = $profile_pic;
                            $param = "File Uploaded";

                            $this->response(array('code' => '200', 'message' => 'File Uploaded', 'result' => $param));
                        }

                        $tabledate = date("d-m-Y: h:i a");

                        $this->db->query("INSERT INTO rural_rmp_registration (rmp_name,rmp_gender,rmp_age,rmp_email,rmp_phone,rmp_password,rmp_clinic_name,rmp_city,profile_pic,created_date) VALUES ('$rmp_name','$rmp_gender','$rmp_age','$rmp_email','$rmp_phone','$password','$rmp_clinic_name','$rmp_city','$dp','$tabledate')");

                        $this->response(array('code' => '200', 'message' => 'Your registration success'), 200);
                    } else {

                        $this->response(array('code' => '404', 'message' => 'your mobile number is already exist'), 200);
                    }
                } else {

                    $this->response(array('code' => '404', 'message' => 'your email ID is already exist'), 200);
                }
            } else {

                $this->response(array('code' => '404', 'message' => 'Enter Total Fields'), 200);
            }
        } else {

            // echo "enter details";
            $this->response(array('code' => '404', 'message' => 'Enter Details'), 200);
        }
    }


    public function Rmplogin_post()
    {

        extract($_POST);

        // $rmp_phone_number;
        // $rmp_password;
        $word = md5($rmp_password);

        $pasval2 = $this->db->select('*')->from('rural_rmp_registration')->where('rmp_phone="' . $rmp_phone . '" OR rmp_email="' . $rmp_phone . '"')->get()->row();

        $pasID2 = $pasval2->rmp_id;

        if ($pasID2 > 0) {

            $res2 = $this->db->select('*')->from('rural_rmp_registration')->where('rmp_phone="' . $rmp_phone . '" AND rmp_password="' . $word . '" OR rmp_email="' . $rmp_phone . '" AND rmp_password="' . $word . '"')->get()->row();
            // nurse_email="'.$nurse_email.'" AND nurse_password="'.$password.'" AND clinic_id="'.$clinic_id.'" OR nurse_phone_number="'.$nurse_email.'" AND nurse_password="'.$password.'" AND clinic_id="'.$clinic_id.'"'

            $id2 = $res2->rmp_id;
            $name = $res2->rmp_name;
            $rmp_gender = $res2->rmp_gender;
            $rmp_email = $res2->rmp_email;
            $rmp_phone = $res2->rmp_phone;
            $rmp_age = $res2->rmp_age;
            $clinic = $res2->rmp_clinic_name;
            $location = $res2->rmp_city;
            $profilePic = base_url('uploads/rural/doc_profile_pic/' . $res2->profile_pic);

            $data['fcm_id'] = $fcm_id;
            $data['device_id'] = $device_id;

            $wherecond = "( ( ( rmp_phone ='" . $rmp_phone . "' AND rmp_password='" . $word . "') OR (rmp_email='" . $rmp_phone . "' AND rmp_password='" . $word . "') ) )";

            $this->Generic_model->updateData("rural_rmp_registration", $data, $wherecond);


            if ($id2 > 0) {
                $tokenprint = $this->tokenGenerator($rmp_email, $password);

                $this->response(array(
                    'code' => '200',
                    'message' => 'sucesslly login',
                    'token' => $tokenprint,
                    'details' => array(
                        'rmp_id' => $id2,
                        'rmp_name' => $name,
                        'rmp_gender' => $rmp_gender,
                        'rmp_age' => $rmp_age,
                        'rmp_email' => $rmp_email,
                        'phonenumber' => $rmp_phone,
                        'clinic_name' => $clinic,
                        'clinic_location' => $location,
                        'profile_pic' => $profilePic
                    )
                ));
            } else {

                $this->response(array('code' => '404', 'message' => 'your password is wrong'));
            }
        } else {
            $this->response(array('code' => '404', 'message' => 'your Login id is wrong'));
        }
    }

    // reset rural_doctor password through otp 

    public function resetpassword_post()
    {

        extract($_POST);

        $check_user = $this->db->select('*')->from('rural_rmp_registration')->where('rmp_phone="' . $rmp_phone . '"')->get()->row();

        $count = count($check_user);
        if ($count > 0) {
            $otp = rand(100000, 999999);
            $mobile = $check_user->rmp_phone;
            $message = "your OTP is  "  . $otp . " to reset your password.";
            $rmp_name = $check_user->rmp_name;
            $rmp_id = $check_user->rmp_id;
            send_otp($mobile, $otp, $message);
            $this->response(array('code' => '200', 'message' => 'OTP has sent on mobile ' . $mobile, 'otp' => $otp, 'username' => $rmp_name, 'rmp_id' => $rmp_id));
        } else {

            $this->response(array('code' => '404', 'message' => 'User Does Not Exist'), 200);
        }
    }


    // password updated api...
    //  this api is used to updated password 



    public function updatePassword_post()
    {

        extract($_POST);

        $passwordmd5 = md5($password);

        $this->db->query("UPDATE rural_rmp_registration SET rmp_password='" . $passwordmd5 . "' WHERE rmp_phone='" . $rmp_phone . "'");

        $this->response(array('code' => '200', 'message' => 'password sucessfully changed'));
    }

    // vitals creation api

    public function vital_POST()
    {

        if (!empty(isset($_POST))) {
            extract($_POST);

            $date = date("d-m-Y: h:i a");

            if ($heart_rate != "" and $blood_pressure != "" and $respiratory != "" and $temperature != "" and $patient_weight != "" and $patient_height != "" and $patient_id != "" and $rmp_id != "" and $saturation != "") {
                $this->db->query("INSERT INTO rural_rmp_patient_vitals (patient_id,rmp_id,heart_rate,blood_pressure,respiratory,temperature,saturation,patient_weight,patient_height,created_by,created_date) VALUES ('$patient_id','$rmp_id','$heart_rate','$blood_pressure','$respiratory','$temperature','$saturation','$patient_weight','$patient_height','$rmp_id','$date')");

                $this->response(array('code' => '200', 'message' => 'vitals  successfully created'), 200);
            } else {
                $this->response(array('code' => '404', 'message' => 'Enter total Fields'), 404);
            }
        } else {
            $this->response(array('code' => '404', 'message' => 'Enter Fields'), 404);
        }
    }


    // vitals get api


    public function vitalsget_POST()
    {

        if (!empty(isset($_POST))) {
            extract($_POST);
            if ($patient_id != "" and $rmp_id != "") {

                $vital = $this->db->query("select * from rural_rmp_patient_vitals where patient_id='" . $patient_id . "' and rmp_id='" . $rmp_id . "' ORDER BY created_date DESC")->result_array();

                $data['patient_vitals'] = $vital;
                $this->response($data);
            } else {
                $this->response(array('code' => '404', 'message' => 'enter patient_id and rmp_id'), 404);
            }
        } else {
            $this->response(array('code' => '404', 'message' => 'Enter Fields'), 404);
        }
    }

    // patient creation api

    public function patientCreation_post()
    {
        extract($_POST);

        $patient_profile_pic = $_FILES["file_profile_pic"]["name"];
        $patient_id_proof = $_FILES["file_id_proof"]["name"];

        $patient_profile = "NULL";
        $proof_id = "NULL";
        // patient_profile pic upload...
        if (!empty($patient_profile_pic)) {

            $date = date("Y-m-d:H:i:s");
            $this->load->library('upload');
            $filetype = pathinfo($_FILES["file_profile_pic"]["name"], PATHINFO_EXTENSION);
            $profile_pic = "patient_pro_pic-" . $date . "." . $filetype;
            $config = array();
            $config['upload_path'] = './uploads/rural/patient_profile_pic/';
            $config['file_name'] = $profile_pic;
            $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG|webp|WEBP';
            $this->upload->initialize($config);

            // $_FILES['file_i']['name'] = $_FILES['file_profile_pic']['name'];
            // $_FILES['file_i']['type'] = $_FILES['file_profile_pic']['type'];
            // $_FILES['file_i']['tmp_name'] = $_FILES['file_profile_pic']['tmp_name'];
            // $_FILES['file_i']['error'] = $_FILES['file_profile_pic']['error'];
            // $_FILES['file_i']['size'] = $_FILES['file_profile_pic']['size'];

            $files = ['jpg', 'png', 'jpeg'];
            $filename = $_FILES['file_profile_pic']['name'];

            $dp = "doctor_pro_pic.jpg";
            // if (!in_array($filetype, $files)) {
            //     $param['type'] = pathinfo($_FILES["file_profile_pic"]["name"], PATHINFO_EXTENSION);
            //     $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
            // } else {
            $this->upload->do_upload('file_profile_pic');
            $fname = $this->upload->data();
            $patient_profile = $profile_pic;
            $param = "File Uploaded";
            // }
        }
        // patient id proof upload...
        if (!empty($patient_id_proof)) {

            // $config['upload_path'] = './uploads/rural/patient_id_pic/';

            // $config['allowed_types'] = 'jpg|JPG|png|PNG|csv|JPEG|jpeg';

            // $_FILES['file_i']['name'] = $_FILES['file_id_proof']['name'];
            // $_FILES['file_i']['type'] = $_FILES['file_id_proof']['type'];
            // $_FILES['file_i']['tmp_name'] = $_FILES['file_id_proofile_i']['tmp_name'];
            // $_FILES['file_i']['error'] = $_FILES['file_id_proof']['error'];
            // $_FILES['file_i']['size'] = $_FILES['file_id_proof']['size'];

            // $this->upload->initialize($config);
            // $this->upload->do_upload('file_id_proof');

            // $fname = $this->upload->data();
            // $fileName = $fname['file_name'];

            // $date = date("Y-m-d:H:i:s");
            // $this->load->library('upload');
            // $filetype = pathinfo($_FILES["file_id_proof"]["name"], PATHINFO_EXTENSION);
            // $id_proof = "patient_id_pic-" . $date . "." . $filetype;
            // $config['upload_path'] = './uploads/rural/patient_id_pic/';
            // $config['file_name'] = $id_proof;
            // $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG';

            // $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
            // $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
            // $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
            // $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
            // $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

            // $files = ['jpg', 'png', 'jpeg'];
            // $filename = $_FILES['file_i']['name'];

            $dp = "doctor_pro_pic.jpg";
            if (!in_array($filetype, $files)) {
                $param['type'] = pathinfo($_FILES["file_id_proof"]["name"], PATHINFO_EXTENSION);
                $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
            } else {
                $this->upload->initialize($config);
                $this->upload->do_upload('file_id_proof');
                $fname = $this->upload->data();
                $proof_id = $id_proof;
                $param = "File Uploaded";
            }
        }


        if ($rmp_id != "" and $patient_name != "" and $patient_gender != "" and $patient_age != "" and $patient_mobile_number != "" and $patient_location != "") {
            $tabledate = date("d-m-Y: h:i a");

            $this->db->query("INSERT INTO rural_rmp_patients (rmp_id,patient_name,patient_gender,patient_age,patient_mobile_number,patient_id_proof,patient_profile_pic,patient_location,created_by,created_date) VALUES
             ('$rmp_id','$patient_name','$patient_gender','$patient_age','$patient_mobile_number','$proof_id','$patient_profile','$patient_location','$rmp_id','$tabledate')");

            $this->response(array('code' => '200', 'message' => $patient_profile), 200);
        } else {
            $this->response(array('code' => '404', 'message' => 'Enter Fields'));
        }
    }

    public function totalpatients_post()
    {

        if (!empty(isset($_POST))) {
            extract($_POST);

            if ($rmp_id != "") {
                $vital = $this->db->query("select * from rural_rmp_patients where  rmp_id='" . $rmp_id . "' ORDER BY created_date DESC")->result();

                $this->response(array(
                    'patient_id_proof_url' => 'https://www.devumdaa.in/dev/uploads/rural/patient_id_pic/',
                    'patient_profile_pic_url' => 'https://www.devumdaa.in/dev/uploads/rural/patient_profile_pic/',
                    'total_patients' => $vital
                ));
                // $id=$vital->patient_id;



                // $this->response(array('code' => '200','message' => 'total patients',
                //                     'details'=>
                //                     array(  'patient_id' =>$id,
                //                             'rmp_id'=>$vital->rmp_id,
                //                             'patient_name'=>$vital->patient_name,
                //                             'patient_gender'=>$vital->patient_gender,
                //                             'patient_age'=>$vital->patient_age,
                //                             'patient_mobile_number'=>$vital->patient_mobile_number,
                //                             'patient_id_proof'=>$vital->patient_id_proof,
                //                             'patient_profile_pic'=>$vital->patient_profile_pic,
                //                             'patient_location'=>$vital->patient_location,
                //                             'modified_by'=>$vital->modified_by,
                //                             'created_by'=>$vital->created_by,
                //                             'created_date'=>$vital->created_date,
                //                             'modified_date'=>$vital->modified_date,
                //                              )));
            } else {
                $this->response(array('code' => '404', 'message' => 'Enter rmp ID'), 404);
            }
        } else {
            $this->response(array('code' => '404', 'message' => 'Enter Fields'), 404);
        }
    }
    /* 
this is total gpe api total seven images upload process 

*/
    public function gpexamination_post()
    {
        extract($_POST);

        $patient_id;
        $rmp_id;

        $sclera = $_FILES["sclera"]["name"];
        $palpebral_conjunctiva = $_FILES["palpebral_conjunctiva"]["name"];
        $oral_cavity = $_FILES["oral_cavity"]["name"];
        $neck = $_FILES["neck"]["name"];
        $dorsal_hand = $_FILES["dorsal_hand"]["name"];
        $palms = $_FILES["palms"]["name"];
        $leg = $_FILES["leg"]["name"];

        // upload scleta image

        if (!empty($sclera)) {

            $date = date("Y-m-d:H:i:s");
            $this->load->library('upload');
            $filetype = pathinfo($_FILES["sclera"]["name"], PATHINFO_EXTENSION);
            $patient_sclera = "sclera-pic-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
            $config['upload_path'] = './uploads/rural/Gpe_files/patient_sclera/';

            $config['file_name'] = $patient_sclera;
            $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG|webp|WEBP';

            $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
            $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
            $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
            $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
            $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

            $files = ['jpg', 'png', 'jpeg', 'JPG', 'JPEG', 'png', 'PNG', 'webp', 'WEBP'];

            if (!in_array($filetype, $files)) {
                $param['type'] = pathinfo($_FILES["sclera"]["name"], PATHINFO_EXTENSION);
                $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
            } else {
                $this->upload->initialize($config);
                $this->upload->do_upload('sclera');
                $fname = $this->upload->data();
                $patient_sclera_db_id = $patient_sclera;
                $param = "File Uploaded";
            }
        }

        // upload palpebral_conjunctiva

        if (!empty($palpebral_conjunctiva)) {
            $date = date("Y-m-d:H:i:s");
            $this->load->library('upload');
            $filetype = pathinfo($_FILES["palpebral_conjunctiva"]["name"], PATHINFO_EXTENSION);
            $palpebral_conjunctiva = "palpebral_conjunctiva-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
            $config['upload_path'] = './uploads/rural/Gpe_files/patient_palpebral_conjunctiva/';

            $config['file_name'] = $palpebral_conjunctiva;
            $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG';

            $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
            $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
            $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
            $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
            $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

            $files = ['jpg', 'png', 'jpeg'];



            if (!in_array($filetype, $files)) {
                $param['type'] = pathinfo($_FILES["palpebral_conjunctiva"]["name"], PATHINFO_EXTENSION);
                $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
            } else {
                $this->upload->initialize($config);
                $this->upload->do_upload('palpebral_conjunctiva');
                $fname = $this->upload->data();
                $palpebral_conjunctiva_db_id = $palpebral_conjunctiva;
                $param = "File Uploaded";
            }
        }

        // upload oral_cavity image

        if (!empty($oral_cavity)) {
            $date = date("Y-m-d:H:i:s");
            $this->load->library('upload');
            $filetype = pathinfo($_FILES["oral_cavity"]["name"], PATHINFO_EXTENSION);
            $oral_cavity = "oral_cavity-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
            $config['upload_path'] = './uploads/rural/Gpe_files/patient_oral_cavity/';

            $config['file_name'] = $oral_cavity;
            $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG';

            $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
            $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
            $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
            $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
            $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

            $files = ['jpg', 'png', 'jpeg'];



            if (!in_array($filetype, $files)) {
                $param['type'] = pathinfo($_FILES["oral_cavity"]["name"], PATHINFO_EXTENSION);
                $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
            } else {
                $this->upload->initialize($config);
                $this->upload->do_upload('oral_cavity');
                $fname = $this->upload->data();
                $oral_cavity_db_id = $oral_cavity;
                $param = "File Uploaded";
            }
        }


        // upload neck image


        if (!empty($neck)) {
            $date = date("Y-m-d:H:i:s");
            $this->load->library('upload');
            $filetype = pathinfo($_FILES["neck"]["name"], PATHINFO_EXTENSION);
            $neck = "neck-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
            $config['upload_path'] = './uploads/rural/Gpe_files/patient_neck/';

            $config['file_name'] = $neck;
            $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG';

            $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
            $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
            $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
            $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
            $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

            $files = ['jpg', 'png', 'jpeg'];



            if (!in_array($filetype, $files)) {
                $param['type'] = pathinfo($_FILES["neck"]["name"], PATHINFO_EXTENSION);
                $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
            } else {
                $this->upload->initialize($config);
                $this->upload->do_upload('neck');
                $fname = $this->upload->data();
                $neck_db_id = $neck;
                $param = "File Uploaded";
            }
        }

        //  upload dorsal_hand image

        if (!empty($dorsal_hand)) {
            $date = date("Y-m-d:H:i:s");
            $this->load->library('upload');
            $filetype = pathinfo($_FILES["dorsal_hand"]["name"], PATHINFO_EXTENSION);
            $dorsal_hand = "dorsal_hand-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
            $config['upload_path'] = './uploads/rural/Gpe_files/patient_dorsal_hand/';

            $config['file_name'] = $dorsal_hand;
            $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG';

            $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
            $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
            $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
            $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
            $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

            $files = ['jpg', 'png', 'jpeg'];



            if (!in_array($filetype, $files)) {
                $param['type'] = pathinfo($_FILES["dorsal_hand"]["name"], PATHINFO_EXTENSION);
                $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
            } else {
                $this->upload->initialize($config);
                $this->upload->do_upload('dorsal_hand');
                $fname = $this->upload->data();
                $dorsal_hand_db_id = $dorsal_hand;
                $param = "File Uploaded";
            }
        }

        // upload palms

        if (!empty($palms)) {
            $date = date("Y-m-d:H:i:s");
            $this->load->library('upload');
            $filetype = pathinfo($_FILES["palms"]["name"], PATHINFO_EXTENSION);
            $palms = "palms-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
            $config['upload_path'] = './uploads/rural/Gpe_files/patient_palms/';

            $config['file_name'] = $palms;
            $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG';

            $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
            $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
            $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
            $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
            $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

            $files = ['jpg', 'png', 'jpeg'];



            if (!in_array($filetype, $files)) {
                $param['type'] = pathinfo($_FILES["palms"]["name"], PATHINFO_EXTENSION);
                $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
            } else {
                $this->upload->initialize($config);
                $this->upload->do_upload('palms');
                $fname = $this->upload->data();
                $palms_db_id = $palms;
                $param = "File Uploaded";
            }
        }

        // leg


        if (!empty($leg)) {
            $date = date("Y-m-d:H:i:s");
            $this->load->library('upload');
            $filetype = pathinfo($_FILES["leg"]["name"], PATHINFO_EXTENSION);
            $leg = "leg-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
            $config['upload_path'] = './uploads/rural/Gpe_files/patient_leg/';

            $config['file_name'] = $leg;
            $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG';

            $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
            $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
            $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
            $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
            $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

            $files = ['jpg', 'png', 'jpeg'];



            if (!in_array($filetype, $files)) {
                $param['type'] = pathinfo($_FILES["leg"]["name"], PATHINFO_EXTENSION);
                $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
            } else {
                $this->upload->initialize($config);
                $this->upload->do_upload('leg');
                $fname = $this->upload->data();
                $leg_db_id = $leg;
                $param = "File Uploaded";
            }
        }

        $tabledate = date("d-m-Y: h:i a");

        $this->db->query("INSERT INTO patient_gpe_images (rmp_id,patient_id,patient_oral_cavity,patient_neck,patient_dorsal_hand,patient_palms,patient_leg,patient_sclera,patient_palpebral_conjunctiva,created_by,created_date) VALUES 
('$rmp_id','$patient_id','$oral_cavity_db_id','$neck_db_id','$dorsal_hand_db_id','$palms_db_id','$leg_db_id','$patient_sclera_db_id','$palpebral_conjunctiva_db_id','$rmp_id','$tabledate')");

        $this->response(array('code' => '200', 'message' => 'Patient Gpe successfully submitted'), 200);
    }

    /*
        this api is used to get gpe 7 images using patient_id and rmp_id
*/

    public function gpeget_post()
    {
        extract($_POST);

        if ($patient_id != "" and $rmp_id != "") {

            $gpeimages = $this->db->query("select * from patient_gpe_images where patient_id='" . $patient_id . "' and rmp_id='" . $rmp_id . "' ORDER BY patient_gpe_images.created_date DESC")->result();
            $a = 0;

            // print_r($gpeimages);

            foreach ($gpeimages as $gpe) {

                $data['patient_gpe'][$a]['patient_gpe_img_id'] = $gpe->patient_gpe_img_id;
                $data['patient_gpe'][$a]['rmp_id'] = $gpe->rmp_id;
                $data['patient_gpe'][$a]['patient_id'] = $gpe->patient_id;
                $data['patient_gpe'][$a]['patient_oral_cavity'] = 'http://devumdaa.in/dev/uploads/rural/Gpe_files/patient_oral_cavity/' . $gpe->patient_oral_cavity;
                $data['patient_gpe'][$a]['patient_neck'] = 'http://devumdaa.in/dev/uploads/rural/Gpe_files/patient_neck/' . $gpe->patient_neck;
                $data['patient_gpe'][$a]['patient_dorsal_hand'] = 'http://devumdaa.in/dev/uploads/rural/Gpe_files/patient_dorsal_hand/' . $gpe->patient_dorsal_hand;
                $data['patient_gpe'][$a]['patient_palms'] = 'http://devumdaa.in/dev/uploads/rural/Gpe_files/patient_palms/' . $gpe->patient_palms;
                $data['patient_gpe'][$a]['patient_leg'] = 'http://devumdaa.in/dev/uploads/rural/Gpe_files/patient_leg/' . $gpe->patient_leg;
                $data['patient_gpe'][$a]['patient_sclera'] = 'http://devumdaa.in/dev/uploads/rural/Gpe_files/patient_sclera/' . $gpe->patient_sclera;
                $data['patient_gpe'][$a]['patient_palpebral_conjunctiva'] = 'http://devumdaa.in/dev/uploads/rural/Gpe_files/patient_palpebral_conjunctiva/' . $gpe->patient_palpebral_conjunctiva;
                $data['patient_gpe'][$a]['modified_by'] = $gpe->modified_by;
                $data['patient_gpe'][$a]['created_by'] = $gpe->created_by;
                $data['patient_gpe'][$a]['created_date'] = $gpe->created_date;
                $data['patient_gpe'][$a]['modified_date'] = $gpe->modified_date;
                $a++;

                $this->response(array('code' => '200', 'message' => 'success', 'gpefiles' => $data['patient_gpe']));
            }
        } else {
            $this->response(array('code' => '404', 'message' => 'enter patient_id and rmp_id'));
        }
    }

    public function hai_post()
    {

        $patient_profile_pic = $_FILES["file_profile_pic"]["name"];
        $patient_id_proof = $_FILES["file_id_proof"]["name"];

        if (!empty($patient_id_proof)) {
            echo "id proof submited";
            echo "<br>";
        } else if (!empty($patient_profile_pic)) {
            echo "<br>";
            echo "profile pic submited";
        } else {
            echo "no image submited";
        }
    }

    /**symptoms add funciton with audio file  */

    public function addsymptoms_post()
    {

        extract($_POST);

        $symptoms_audio_file = $_FILES["symptoms_audio"]["name"];


        $symptoms_audio = "NULL";

        if (!empty($symptoms_audio_file)) {

            $date = date("Y-m-d:H:i:s");
            $this->load->library('upload');
            $filetype = pathinfo($_FILES["symptoms_audio"]["name"], PATHINFO_EXTENSION);
            $audio_name = "patient_symptoms_audio-" . $patient_id . "-" . $rmp_id . "-" . time() . "." . $filetype;
            $config['upload_path'] = './uploads/rural/patient_symptoms/patient_symptoms_audio/';
            $config['file_name'] = $audio_name;
            $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

            $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
            $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
            $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
            $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
            $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

            $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];



            if (!in_array($filetype, $files)) {
                $param['type'] = pathinfo($_FILES["symptoms_audio"]["name"], PATHINFO_EXTENSION);
                $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
            } else {

                $this->upload->initialize($config);
                $this->upload->do_upload('symptoms_audio');
                $fname = $this->upload->data();
                $symptoms_audio = $audio_name;
                $param = "File Uploaded";
            }
        }


        if ($rmp_id != "" and $patient_id != "") {
            $tabledate = date("d-m-Y: h:i a");

            $this->db->query("INSERT INTO patient_symptoms (patient_id,rmp_id,describe_symptom_text,symptoms_audio,created_by,created_date) VALUES ('$patient_id','$rmp_id','$describe_symptom_text','$symptoms_audio','$rmp_id','$tabledate')");

            $this->response(array('code' => '200', 'message' => 'patient symptom sucessfully created'), 200);
        } else {
            $this->response(array('code' => '404', 'message' => 'Enter Total Fields'));
        }
    }


    public function getsymptoms_post()
    {
        extract($_POST);

        if ($patient_id != "" and $rmp_id != "") {


            $symptoms = $this->db->query("select * from patient_symptoms where patient_id='" . $patient_id . "' and rmp_id='" . $rmp_id . "' ORDER BY created_date DESC")->result();
            // $data['symptoms']=$symptoms;
            // $this->response($data);


            $a = 0;
            foreach ($symptoms as $sym) {
                $data['Symptoms_data'][$a]['patient_symptom_id'] = $sym->patient_symptom_id;
                $data['Symptoms_data'][$a]['patient_id'] = $sym->patient_id;
                $data['Symptoms_data'][$a]['rmp_id'] = $sym->rmp_id;
                $data['Symptoms_data'][$a]['describe_symptom_text'] = $sym->describe_symptom_text;
                $data['Symptoms_data'][$a]['symptoms_audio'] = 'https://www.devumdaa.in/dev/uploads/rural/patient_symptoms/patient_symptoms_audio/' . $sym->symptoms_audio;
                $data['Symptoms_data'][$a]['symptoms_duration'] = $sym->symptoms_duration;
                $data['Symptoms_data'][$a]['symptom_range'] = $sym->symptom_range;
                $data['Symptoms_data'][$a]['modified_by'] = $sym->modified_by;
                $data['Symptoms_data'][$a]['created_by'] = $sym->created_by;
                $data['Symptoms_data'][$a]['created_date'] = $sym->created_date;
                $data['Symptoms_data'][$a]['modified_date'] = $sym->modified_date;
                $a++;
                // $obj=json_encode($data[]);

            }
            $this->response(array('code' => '200', 'message' => 'success', 'Symptoms_data' => $data['Symptoms_data']));
            // $a=0;
            // foreach($symptoms as $sym){
            //     $this->response(array('code' => '200', 'message' => 'Procedure List', 'result' => $sym[$a]->patient_symptom_id, 'requestname' => $method));
            //     $a++;
            //     // print_r($sym->patient_symptom_id);
            //     // $this->response(array('patient_symptom_id'=>$sym->patient_symptom_id,
            //     //                         'patient_id'=>$sym->patient_id,
            //     //                         'rmp_id'=>$sym->rmp_id,
            //     //                         'describe_symptom_text'=>$sym->describe_symptom_text,
            //     //                         'symptoms_audio'=>'https://www.devumdaa.in/dev/uploads/rural/patient_symptoms/patient_symptoms_audio/'.$sym->symptoms_audio,
            //     //                         'symptoms_duration'=>$sym->symptoms_duration,
            //     //                         'symptom_range'=>$sym->symptom_range,
            //     //                         'modified_by'=>$sym->modified_by,
            //     //                         'created_by'=>$sym->created_by,
            //     //                         'created_date'=>$sym->created_date,
            //     //                         'modified_date'=>$sym->modified_date
            //     //                         ),200);

            // }

        } else {
            $this->response(array('code' => '404', 'message' => 'enter patient_id and rmp_id'), 404);
        }
    }
    /* 
cardio post  api
--------------------
this api is used to post the SC audio files that is 
1) Mitral_audio
2) Arotic_audio
3) Pulmonary_audio
4) Tricuspid_audio
*/
    public function cardiopost_post()
    {
        extract($_POST);

        if ($rmp_id != "" and $patient_id != "") {

            $mitral_audio = $_FILES["mitral_audio"]["name"];
            $arotic_audio = $_FILES["arotic_audio"]["name"];
            $pulmonary_audio = $_FILES["pulmonary_audio"]["name"];
            $tricuspid_audio = $_FILES["tricuspid_audio"]["name"];

            if (!empty($mitral_audio)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["mitral_audio"]["name"], PATHINFO_EXTENSION);
                $audio_name = "mitral_audio-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/Cardio/Mitral/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["mitral_audio"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('mitral_audio');
                    $fname = $this->upload->data();
                    $mitral_audio = $audio_name;
                    $param = "File Uploaded";
                }
            }

            // arotic_audio

            if (!empty($arotic_audio)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["arotic_audio"]["name"], PATHINFO_EXTENSION);
                $audio_name = "arotic_audio-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/Cardio/Arotic/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["arotic_audio"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('arotic_audio');
                    $fname = $this->upload->data();
                    $arotic_audio = $audio_name;
                    $param = "File Uploaded";
                }
            }


            // pulmonary_audio 

            if (!empty($pulmonary_audio)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["pulmonary_audio"]["name"], PATHINFO_EXTENSION);
                $audio_name = "pulmonary_audio-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/Cardio/Pulmonary/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["pulmonary_audio"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('pulmonary_audio');
                    $fname = $this->upload->data();
                    $pulmonary_audio = $audio_name;
                    $param = "File Uploaded";
                }
            }


            // tricuspid_audio

            if (!empty($tricuspid_audio)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["tricuspid_audio"]["name"], PATHINFO_EXTENSION);
                $audio_name = "tricuspid_audio-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/Cardio/Tricuspid/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["tricuspid_audio"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('tricuspid_audio');
                    $fname = $this->upload->data();
                    $tricuspid_audio = $audio_name;
                    $param = "File Uploaded";
                }
            }
            $table_date = date("d-m-Y: h:i a");

            $this->db->query("INSERT INTO sc_cardio (patient_id,rmp_id,mitral_audio,arotic_audio,pulmonary_audio,tricuspid_audio,created_by,created_date) VALUES ('$patient_id','$rmp_id','$mitral_audio','$arotic_audio','$pulmonary_audio','$tricuspid_audio','$rmp_id','$table_date')");

            $this->response(array('code' => '200', 'message' => 'patient Systematic Examination sucessfully created'), 200);
        } else {
            $this->response(array('code' => '404', 'message' => 'enter patient_id and rmp_id'), 404);
        }
    }


    /*
this is frontresp api 
this api is used to upload 4 audio files that is 
1) right_infra_clavicle
2) left_infra_clavicle
3) right_infra_mammary
4) left_infra_mammary
*/



    public function frontresp_post()
    {
        extract($_POST);

        if ($rmp_id != "" and $patient_id != "") {

            $right_infra_clavicle = $_FILES["right_infra_clavicle"]["name"];
            $left_infra_clavicle = $_FILES["left_infra_clavicle"]["name"];
            $right_infra_mammary = $_FILES["right_infra_mammary"]["name"];
            $left_infra_mammary = $_FILES["left_infra_mammary"]["name"];


            // right_infra_clavicle


            if (!empty($right_infra_clavicle)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["right_infra_clavicle"]["name"], PATHINFO_EXTENSION);
                $audio_name = "right_infra_clavicle-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/Resp_front/Right_Infra_Clavicle/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["right_infra_clavicle"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('right_infra_clavicle');
                    $fname = $this->upload->data();
                    $right_infra_clavicle = $audio_name;
                    $param = "File Uploaded";
                }
            }
            // left_infra_clavicle

            if (!empty($left_infra_clavicle)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["left_infra_clavicle"]["name"], PATHINFO_EXTENSION);
                $audio_name = "left_infra_clavicle-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/Resp_front/Left_Infra_Clavicle/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["left_infra_clavicle"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('left_infra_clavicle');
                    $fname = $this->upload->data();
                    $left_infra_clavicle = $audio_name;
                    $param = "File Uploaded";
                }
            }


            // right_infra_mammary 

            if (!empty($right_infra_mammary)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["right_infra_mammary"]["name"], PATHINFO_EXTENSION);
                $audio_name = "right_infra_mammary-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/Resp_front/Right_Infra_Mammary/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["right_infra_mammary"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('right_infra_mammary');
                    $fname = $this->upload->data();
                    $right_infra_mammary = $audio_name;
                    $param = "File Uploaded";
                }
            }


            // left_infra_mammary

            if (!empty($left_infra_mammary)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["left_infra_mammary"]["name"], PATHINFO_EXTENSION);
                $audio_name = "left_infra_mammary-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/Resp_front/Left_Infra_Mammary/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["left_infra_mammary"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('left_infra_mammary');
                    $fname = $this->upload->data();
                    $left_infra_mammary = $audio_name;
                    $param = "File Uploaded";
                }
            }

            $tabledate = date("d-m-Y: h:i a");

            $this->db->query("INSERT INTO sc_resp_front (patient_id,rmp_id,right_infra_clavicle_audio,left_infra_clavicle_audio,right_infra_mammary_audio,left_infra_mammary_audio,created_by,created_date) VALUES ('$patient_id','$rmp_id','$right_infra_clavicle','$left_infra_clavicle','$right_infra_mammary','$left_infra_mammary','$rmp_id','$tabledate')");

            $this->response(array('code' => '200', 'message' => 'patient Systematic Examination ( front respiration ) sucessfully created'), 200);
        } else {
            $this->response(array('code' => '404', 'message' => 'enter patient_id and rmp_id'), 404);
        }
    }



    public function backresp_post()
    {
        extract($_POST);

        if ($rmp_id != "" and $patient_id != "") {

            $left_interscapular_audio = $_FILES["left_interscapular_audio"]["name"];
            $right_interscapular_audio = $_FILES["right_interscapular_audio"]["name"];
            $left_infrascapular_audio = $_FILES["left_infrascapular_audio"]["name"];
            $right_infrascapular_audio = $_FILES["right_infrascapular_audio"]["name"];


            // left_interscapular_audio


            if (!empty($left_interscapular_audio)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["left_interscapular_audio"]["name"], PATHINFO_EXTENSION);
                $audio_name = "left_interscapular_audio-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/resp_back/Left_Interscapular/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["left_interscapular_audio"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('left_interscapular_audio');
                    $fname = $this->upload->data();
                    $left_interscapular_audio = $audio_name;
                    $param = "File Uploaded";
                }
            }
            // left_infra_clavicle

            if (!empty($right_interscapular_audio)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["right_interscapular_audio"]["name"], PATHINFO_EXTENSION);
                $audio_name = "right_interscapular_audio-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/resp_back/Right_Interscapular/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["right_interscapular_audio"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('right_interscapular_audio');
                    $fname = $this->upload->data();
                    $right_interscapular_audio = $audio_name;
                    $param = "File Uploaded";
                }
            }


            // left_infrascapular_audio 

            if (!empty($left_infrascapular_audio)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["left_infrascapular_audio"]["name"], PATHINFO_EXTENSION);
                $audio_name = "left_infrascapular_audio-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/resp_back/Left_Infrascapular/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["left_infrascapular_audio"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('left_infrascapular_audio');
                    $fname = $this->upload->data();
                    $left_infrascapular_audio = $audio_name;
                    $param = "File Uploaded";
                }
            }


            // right_infrascapular_audio

            if (!empty($right_infrascapular_audio)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["right_infrascapular_audio"]["name"], PATHINFO_EXTENSION);
                $audio_name = "right_infrascapular_audio-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/resp_back/Right_Infrascapular/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["right_infrascapular_audio"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('right_infrascapular_audio');
                    $fname = $this->upload->data();
                    $right_infrascapular_audio = $audio_name;
                    $param = "File Uploaded";
                }
            }

            $tabledate = date("d-m-Y: h:i a");

            $this->db->query("INSERT INTO sc_resp_back (patient_id,rmp_id,left_interscapular_audio,right_interscapular_audio,left_infrascapular_audio,right_infrascapular_audio,created_by,created_date)
 VALUES ('$patient_id','$rmp_id','$left_interscapular_audio','$right_interscapular_audio','$left_infrascapular_audio','$right_infrascapular_audio','$rmp_id','$tabledate')");

            $this->response(array('code' => '200', 'message' => 'patient Systematic Examination ( back respiration ) sucessfully created'), 200);
        } else {
            $this->response(array('code' => '404', 'message' => 'enter patient_id and rmp_id'), 404);
        }
    }

    public function cardioget_post()
    {
        extract($_POST);
        if ($patient_id != "" and $rmp_id != "") {
            $cardio = $this->db->query("select * from sc_cardio where rmp_id='" . $rmp_id . "' and patient_id='" . $patient_id . "' ORDER BY created_date DESC")->result();
            if (!empty($cardio)) {

                $a = 0;
                foreach ($cardio as $car) {
                    $data['cardioget'][$a]['sc_cardio_id'] = $car->sc_cardio_id;
                    $data['cardioget'][$a]['rmp_id'] = $car->rmp_id;
                    $data['cardioget'][$a]['patient_id'] = $car->patient_id;
                    $data['cardioget'][$a]['mitral_audio'] = 'http://devumdaa.in/dev/uploads/rural/sc/Cardio/Mitral/' . $car->mitral_audio;
                    $data['cardioget'][$a]['arotic_audio'] = 'http://devumdaa.in/dev/uploads/rural/sc/Cardio/Arotic/' . $car->arotic_audio;
                    $data['cardioget'][$a]['pulmonary_audio'] = 'http://devumdaa.in/dev/uploads/rural/sc/Cardio/Pulmonary/' . $car->pulmonary_audio;
                    $data['cardioget'][$a]['tricuspid_audio'] = 'http://devumdaa.in/dev/uploads/rural/sc/Cardio/Tricuspid/' . $car->tricuspid_audio;
                    $data['cardioget'][$a]['modified_by'] = $car->modified_by;
                    $data['cardioget'][$a]['created_by'] = $car->created_by;
                    $data['cardioget'][$a]['created_date'] = $car->created_date;
                    $data['cardioget'][$a]['modified_date'] = $car->modified_date;
                    $a++;
                }

                $this->response(array('code' => '200', 'message' => 'success', 'cardio_data' => $data['cardioget']));
            } else {

                $this->response(array('code' => '404', 'message' => 'no data fount'));
            }
        } else {
            $this->response(array('code' => '404', 'message' => 'enter patient_id and rmp_id'));
        }
    }



    public function frontrespget_post()
    {
        extract($_POST);
        if ($patient_id != "" and $rmp_id != "") {
            $front = $this->db->query("select * from sc_resp_front where rmp_id='" . $rmp_id . "' and patient_id='" . $patient_id . "'")->result();
            if (!empty($front)) {

                $a = 0;
                foreach ($front as $fro) {
                    $data['frontres'][$a]['sc_resp_front_id'] = $fro->sc_resp_front_id;
                    $data['frontres'][$a]['rmp_id'] = $fro->rmp_id;
                    $data['frontres'][$a]['patient_id'] = $fro->patient_id;
                    $data['frontres'][$a]['Right_Infra_Mammary'] = 'http://devumdaa.in/dev/uploads/rural/sc/Resp_front/Right_Infra_Mammary/' . $fro->right_infra_mammary_audio;
                    $data['frontres'][$a]['Right_Infra_Clavicle'] = 'http://devumdaa.in/dev/uploads/rural/sc/Resp_front/Right_Infra_Clavicle/' . $fro->right_infra_clavicle_audio;
                    $data['frontres'][$a]['Left_Infra_Mammary'] = 'http://devumdaa.in/dev/uploads/rural/sc/Resp_front/Left_Infra_Mammary/' . $fro->left_infra_mammary_audio;
                    $data['frontres'][$a]['Left_Infra_Clavicle'] = 'http://devumdaa.in/dev/uploads/rural/sc/Resp_front/Left_Infra_Clavicle/' . $fro->left_infra_clavicle_audio;
                    $data['frontres'][$a]['modified_by'] = $fro->modified_by;
                    $data['frontres'][$a]['created_by'] = $fro->created_by;
                    $data['frontres'][$a]['created_date'] = $fro->created_date;
                    $data['frontres'][$a]['modified_date'] = $fro->modified_date;
                    $a++;
                }

                $this->response(array('code' => '200', 'message' => 'success', 'frontres_data' => $data['frontres']));
            } else {

                $this->response(array('code' => '404', 'message' => 'no data fount'));
            }
        } else {
            $this->response(array('code' => '404', 'message' => 'enter patient_id and rmp_id'));
        }
    }



    public function backrespget_post()
    {
        extract($_POST);
        if ($patient_id != "" and $rmp_id != "") {
            $backk = $this->db->query("select * from sc_resp_back where rmp_id='" . $rmp_id . "' and patient_id='" . $patient_id . "'")->result();
            if (!empty($backk)) {

                $a = 0;
                foreach ($backk as $bac) {
                    $data['backresp'][$a]['sc_resp_back_id'] = $bac->sc_resp_back_id;
                    $data['backresp'][$a]['rmp_id'] = $bac->rmp_id;
                    $data['backresp'][$a]['patient_id'] = $bac->patient_id;
                    $data['backresp'][$a]['left_interscapular_audio'] = 'http://devumdaa.in/dev/uploads/rural/sc/resp_back/Left_Interscapular/' . $bac->left_interscapular_audio;
                    $data['backresp'][$a]['right_interscapular_audio'] = 'http://devumdaa.in/dev/uploads/rural/sc/resp_back/Right_Interscapular/' . $bac->right_interscapular_audio;
                    $data['backresp'][$a]['left_infrascapular_audio'] = 'http://devumdaa.in/dev/uploads/rural/sc/resp_back/Left_Infrascapular/' . $bac->left_infrascapular_audio;
                    $data['backresp'][$a]['right_infrascapular_audio'] = 'http://devumdaa.in/dev/uploads/rural/sc/resp_back/Right_Infrascapular/' . $bac->right_infrascapular_audio;
                    $data['backresp'][$a]['modified_by'] = $bac->modified_by;
                    $data['backresp'][$a]['created_by'] = $bac->created_by;
                    $data['backresp'][$a]['created_date'] = $bac->created_date;
                    $data['backresp'][$a]['modified_date'] = $bac->modified_date;
                    $a++;
                }

                $this->response(array('code' => '200', 'message' => 'success', 'backresp_data' => $data['backresp']));
            } else {

                $this->response(array('code' => '404', 'message' => 'no data fount'));
            }
        } else {
            $this->response(array('code' => '404', 'message' => 'enter patient_id and rmp_id'));
        }
    }

    /*
    abdominalcns_post this api is used to upload one audio file and two text fields into sc_abdominal_cns table
    
    */

    public function abdominalcns_post()
    {

        extract($_POST);
        if ($patient_id != "" and $rmp_id != "") {

            $examination_audio = $_FILES['abdominalcns']['name'];
            $exa_audio = "";

            if (!empty($examination_audio)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["abdominalcns"]["name"], PATHINFO_EXTENSION);
                $audio_name = "abdominal_cns-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/abdominal_cns/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["abdominalcns"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('abdominalcns');
                    $fname = $this->upload->data();
                    $right_infrascapular_audio = $audio_name;
                    $param = "File Uploaded";
                }
            }

            $tabledate = date("d-m-Y: h:i a");

            $this->db->query("INSERT INTO sc_abdominal_cns (patient_id,rmp_id,abdominal_comment,cns_comment,examination_audio,created_by,created_date) VALUES ('$patient_id','$rmp_id','$abdominal_comment','$cns_comment','$right_infrascapular_audio','$rmp_id','$tabledate')");

            $this->response(array('code' => '200', 'message' => 'abdominal and c.n.s examination is sucessfully created'), 200);
        } else {

            $this->response(array('code' => '404', 'message' => 'enter patient_id and rmp_id'));
        }
    }

    //  abdominal and cns get this api is used to get abdominal and cns get


    public function abdominalcnsget_post()
    {
        extract($_POST);

        if ($patient_id != "" and $rmp_id != "") {

            $abdominal = $this->db->query("select * from sc_abdominal_cns where rmp_id='" . $rmp_id . "' and patient_id='" . $patient_id . "'")->result();

            if (!empty($abdominal)) {
                $a = 0;
                foreach ($abdominal as $abd) {


                    $data['abdominal_cns'][$a]['patient_abdominal_cns_id'] = $abd->patient_abdominal_cns_id;
                    $data['abdominal_cns'][$a]['rmp_id'] = $abd->rmp_id;
                    $data['abdominal_cns'][$a]['patient_id'] = $abd->patient_id;
                    $data['abdominal_cns'][$a]['abdominal_comment'] = $abd->abdominal_comment;
                    $data['abdominal_cns'][$a]['cns_comment'] = $abd->cns_comment;
                    $data['abdominal_cns'][$a]['examination_audio'] = 'http://devumdaa.in/dev/uploads/rural/sc/abdominal_cns/' . $abd->examination_audio;
                    $data['abdominal_cns'][$a]['modified_by'] = $abd->modified_by;
                    $data['abdominal_cns'][$a]['created_by'] = $abd->created_by;
                    $data['abdominal_cns'][$a]['created_date'] = $abd->created_date;
                    $data['abdominal_cns'][$a]['modified_date'] = $abd->modified_date;

                    $a++;
                }

                $this->response(array('code' => '200', 'message' => 'success', 'abdominal_cns' => $data['abdominal_cns']));
            } else {
                $this->response(array('code' => '404', 'message' => 'no data fount'));
            }
        } else {
            $this->response(array('code' => '404', 'message' => 'enter patient_id and rmp_id'));
        }
    }



    //     public function resp_post(){

    //     extract($_POST);
    //     if($patient_id!="" and $rmp_id!=""){
    //     $front=$this->db->query("select * from sc_resp_front where rmp_id='".$rmp_id."' and patient_id='".$patient_id."'")->result();

    //     $backk=$this->db->query("select * from sc_resp_back where rmp_id='".$rmp_id."' and patient_id='".$patient_id."'")->result();

    //     $i=1;
    //     foreach($front as $fro){
    //         $data['frontres'][$i]['sc_resp_front_id']=$fro->sc_resp_front_id;
    //         $data['frontres'][$i]['rmp_id']=$fro->rmp_id;
    //         $data['frontres'][$i]['patient_id']=$fro->patient_id;
    //         $data['frontres'][$i]['Right_Infra_Mammary']='http://devumdaa.in/dev/uploads/rural/sc/Resp_front/Right_Infra_Mammary/'.$fro->right_infra_mammary_audio;
    //         $data['frontres'][$i]['Right_Infra_Clavicle']='http://devumdaa.in/dev/uploads/rural/sc/Resp_front/Right_Infra_Clavicle/'.$fro->right_infra_clavicle_audio;
    //         $data['frontres'][$i]['Left_Infra_Mammary']='http://devumdaa.in/dev/uploads/rural/sc/Resp_front/Left_Infra_Mammary/'.$fro->left_infra_mammary_audio;
    //         $data['frontres'][$i]['Left_Infra_Clavicle']='http://devumdaa.in/dev/uploads/rural/sc/Resp_front/Left_Infra_Clavicle/'.$fro->left_infra_clavicle_audio;
    //         $data['frontres'][$i]['modified_by']=$fro->modified_by;
    //         $data['frontres'][$i]['created_by']=$fro->created_by;
    //         $data['frontres'][$i]['created_date']=$fro->created_date;
    //         $data['frontres'][$i]['modified_date']=$fro->modified_date;
    //         $i++;

    //         $i++;
    //     }
    //             $a=0;
    //         foreach($backk as $bac){
    //             $data['backresp'][$a]['sc_resp_back_id']=$bac->sc_resp_back_id;
    //             $data['backresp'][$a]['rmp_id']=$bac->rmp_id;
    //             $data['backresp'][$a]['patient_id']=$bac->patient_id;
    //             $data['backresp'][$a]['left_interscapular_audio']='http://devumdaa.in/dev/uploads/rural/sc/resp_back/Left_Interscapular/'.$bac->left_interscapular_audio;
    //             $data['backresp'][$a]['right_interscapular_audio']='http://devumdaa.in/dev/uploads/rural/sc/resp_back/Right_Interscapular/'.$bac->right_interscapular_audio;
    //             $data['backresp'][$a]['left_infrascapular_audio']='http://devumdaa.in/dev/uploads/rural/sc/resp_back/Left_Infrascapular/'.$bac->left_infrascapular_audio;
    //             $data['backresp'][$a]['right_infrascapular_audio']='http://devumdaa.in/dev/uploads/rural/sc/resp_back/Right_Infrascapular/'.$bac->right_infrascapular_audio;
    //             $data['backresp'][$a]['modified_by']=$bac->modified_by;
    //             $data['backresp'][$a]['created_by']=$bac->created_by;
    //             $data['backresp'][$a]['created_date']=$bac->created_date;
    //             $data['backresp'][$a]['modified_date']=$bac->modified_date;
    //             $a++;
    //         }


    //                 $resparation=array_merge($data['frontres'],$data['backresp']);

    //          $this->response(array('code' => '200','message'=>'success','resparation'=>$resparation));

    // }


    // }



    // respiration post api total 8 audios uploaded 


    public function respiration_post()
    {

        extract($_POST);

        if ($rmp_id != "" and $patient_id != "") {

            $right_infra_clavicle = $_FILES["right_infra_clavicle"]["name"];
            $left_infra_clavicle = $_FILES["left_infra_clavicle"]["name"];
            $right_infra_mammary = $_FILES["right_infra_mammary"]["name"];
            $left_infra_mammary = $_FILES["left_infra_mammary"]["name"];
            $left_interscapular_audio = $_FILES["left_interscapular_audio"]["name"];
            $right_interscapular_audio = $_FILES["right_interscapular_audio"]["name"];
            $left_infrascapular_audio = $_FILES["left_infrascapular_audio"]["name"];
            $right_infrascapular_audio = $_FILES["right_infrascapular_audio"]["name"];

            // right_infra_clavicle


            if (!empty($right_infra_clavicle)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["right_infra_clavicle"]["name"], PATHINFO_EXTENSION);
                $audio_name = "right_infra_clavicle-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/Resp_front/Right_Infra_Clavicle/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["right_infra_clavicle"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('right_infra_clavicle');
                    $fname = $this->upload->data();
                    $right_infra_clavicle = $audio_name;
                    $param = "File Uploaded";
                }
            }
            // left_infra_clavicle

            if (!empty($left_infra_clavicle)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["left_infra_clavicle"]["name"], PATHINFO_EXTENSION);
                $audio_name = "left_infra_clavicle-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/Resp_front/Left_Infra_Clavicle/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["left_infra_clavicle"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('left_infra_clavicle');
                    $fname = $this->upload->data();
                    $left_infra_clavicle = $audio_name;
                    $param = "File Uploaded";
                }
            }


            // right_infra_mammary 

            if (!empty($right_infra_mammary)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["right_infra_mammary"]["name"], PATHINFO_EXTENSION);
                $audio_name = "right_infra_mammary-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/Resp_front/Right_Infra_Mammary/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["right_infra_mammary"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('right_infra_mammary');
                    $fname = $this->upload->data();
                    $right_infra_mammary = $audio_name;
                    $param = "File Uploaded";
                }
            }


            // left_infra_mammary

            if (!empty($left_infra_mammary)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["left_infra_mammary"]["name"], PATHINFO_EXTENSION);
                $audio_name = "left_infra_mammary-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/Resp_front/Left_Infra_Mammary/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["left_infra_mammary"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('left_infra_mammary');
                    $fname = $this->upload->data();
                    $left_infra_mammary = $audio_name;
                    $param = "File Uploaded";
                }
            }


            if (!empty($left_interscapular_audio)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["left_interscapular_audio"]["name"], PATHINFO_EXTENSION);
                $audio_name = "left_interscapular_audio-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/resp_back/Left_Interscapular/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["left_interscapular_audio"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('left_interscapular_audio');
                    $fname = $this->upload->data();
                    $left_interscapular_audio = $audio_name;
                    $param = "File Uploaded";
                }
            }
            // left_infra_clavicle

            if (!empty($right_interscapular_audio)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["right_interscapular_audio"]["name"], PATHINFO_EXTENSION);
                $audio_name = "right_interscapular_audio-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/resp_back/Right_Interscapular/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["right_interscapular_audio"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('right_interscapular_audio');
                    $fname = $this->upload->data();
                    $right_interscapular_audio = $audio_name;
                    $param = "File Uploaded";
                }
            }


            // left_infrascapular_audio 

            if (!empty($left_infrascapular_audio)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["left_infrascapular_audio"]["name"], PATHINFO_EXTENSION);
                $audio_name = "left_infrascapular_audio-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/resp_back/Left_Infrascapular/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["left_infrascapular_audio"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('left_infrascapular_audio');
                    $fname = $this->upload->data();
                    $left_infrascapular_audio = $audio_name;
                    $param = "File Uploaded";
                }
            }


            // right_infrascapular_audio

            if (!empty($right_infrascapular_audio)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["right_infrascapular_audio"]["name"], PATHINFO_EXTENSION);
                $audio_name = "right_infrascapular_audio-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/resp_back/Right_Infrascapular/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["right_infrascapular_audio"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('right_infrascapular_audio');
                    $fname = $this->upload->data();
                    $right_infrascapular_audio = $audio_name;
                    $param = "File Uploaded";
                }
            }

            // left_interscapular_audio


            if (!empty($left_interscapular_audio)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["left_interscapular_audio"]["name"], PATHINFO_EXTENSION);
                $audio_name = "left_interscapular_audio-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/resp_back/Left_Interscapular/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["left_interscapular_audio"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('left_interscapular_audio');
                    $fname = $this->upload->data();
                    $left_interscapular_audio = $audio_name;
                    $param = "File Uploaded";
                }
            }
            // left_infra_clavicle

            if (!empty($right_interscapular_audio)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["right_interscapular_audio"]["name"], PATHINFO_EXTENSION);
                $audio_name = "right_interscapular_audio-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/resp_back/Right_Interscapular/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["right_interscapular_audio"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('right_interscapular_audio');
                    $fname = $this->upload->data();
                    $right_interscapular_audio = $audio_name;
                    $param = "File Uploaded";
                }
            }


            // left_infrascapular_audio 

            if (!empty($left_infrascapular_audio)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["left_infrascapular_audio"]["name"], PATHINFO_EXTENSION);
                $audio_name = "left_infrascapular_audio-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/resp_back/Left_Infrascapular/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["left_infrascapular_audio"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('left_infrascapular_audio');
                    $fname = $this->upload->data();
                    $left_infrascapular_audio = $audio_name;
                    $param = "File Uploaded";
                }
            }


            // right_infrascapular_audio

            if (!empty($right_infrascapular_audio)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["right_infrascapular_audio"]["name"], PATHINFO_EXTENSION);
                $audio_name = "right_infrascapular_audio-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/resp_back/Right_Infrascapular/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["right_infrascapular_audio"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('right_infrascapular_audio');
                    $fname = $this->upload->data();
                    $right_infrascapular_audio = $audio_name;
                    $param = "File Uploaded";
                }
            }

            $tabledate = date("d-m-Y:   h:i a");

            $this->db->query("INSERT INTO sc_respiration (patient_id,rmp_id,left_interscapular_audio,right_interscapular_audio,left_infrascapular_audio,right_infrascapular_audio,right_infra_clavicle_audio,left_infra_clavicle_audio,right_infra_mammary_audio,left_infra_mammary_audio,created_by,created_date) VALUES ('$patient_id','$rmp_id','$left_interscapular_audio','$right_interscapular_audio','$left_infrascapular_audio','$right_infrascapular_audio','$right_infra_clavicle','$left_infra_clavicle','$right_infra_mammary','$left_infra_mammary','$rmp_id','$tabledate')");

            $this->response(array('code' => '200', 'message' => 'patient Systematic Examination  respiration  sucessfully created'), 200);
        } else {
            $this->response(array('code' => '404', 'message' => 'enter patient_id and rmp_id'), 404);
        }
    }

    //  respiration post api completed 2304 line total 8 audios uploaded  


    // total respiration get api 8 audio get

    public function respirationget_post()
    {
        extract($_POST);
        if ($patient_id != "" and $rmp_id != "") {
            $respa = $this->db->query("select * from sc_respiration where rmp_id='" . $rmp_id . "' and patient_id='" . $patient_id . "'")->result();
            if (!empty($respa)) {

                $a = 0;
                foreach ($respa as $res) {
                    $data['respiration'][$a]['sc_respiration_id'] = $res->sc_respiration_id;
                    $data['respiration'][$a]['rmp_id'] = $res->rmp_id;
                    $data['respiration'][$a]['patient_id'] = $res->patient_id;
                    $data['respiration'][$a]['Right_Infra_Mammary'] = 'http://devumdaa.in/dev/uploads/rural/sc/Resp_front/Right_Infra_Mammary/' . $res->right_infra_mammary_audio;
                    $data['respiration'][$a]['Right_Infra_Clavicle'] = 'http://devumdaa.in/dev/uploads/rural/sc/Resp_front/Right_Infra_Clavicle/' . $res->right_infra_clavicle_audio;
                    $data['respiration'][$a]['Left_Infra_Mammary'] = 'http://devumdaa.in/dev/uploads/rural/sc/Resp_front/Left_Infra_Mammary/' . $res->left_infra_mammary_audio;
                    $data['respiration'][$a]['Left_Infra_Clavicle'] = 'http://devumdaa.in/dev/uploads/rural/sc/Resp_front/Left_Infra_Clavicle/' . $res->left_infra_clavicle_audio;
                    $data['respiration'][$a]['left_interscapular_audio'] = 'http://devumdaa.in/dev/uploads/rural/sc/resp_back/Left_Interscapular/' . $res->left_interscapular_audio;
                    $data['respiration'][$a]['right_interscapular_audio'] = 'http://devumdaa.in/dev/uploads/rural/sc/resp_back/Right_Interscapular/' . $res->right_interscapular_audio;
                    $data['respiration'][$a]['left_infrascapular_audio'] = 'http://devumdaa.in/dev/uploads/rural/sc/resp_back/Left_Infrascapular/' . $res->left_infrascapular_audio;
                    $data['respiration'][$a]['right_infrascapular_audio'] = 'http://devumdaa.in/dev/uploads/rural/sc/resp_back/Right_Infrascapular/' . $res->right_infrascapular_audio;
                    $data['respiration'][$a]['modified_by'] = $res->modified_by;
                    $data['respiration'][$a]['created_by'] = $res->created_by;
                    $data['respiration'][$a]['created_date'] = $res->created_date;
                    $data['respiration'][$a]['modified_date'] = $res->modified_date;
                    $a++;
                }

                $this->response(array('code' => '200', 'message' => 'success', 'sc_respiration' => $data['respiration']));
            } else {

                $this->response(array('code' => '404', 'message' => 'no data fount'));
            }
        } else {
            $this->response(array('code' => '404', 'message' => 'enter patient_id and rmp_id'));
        }
    }


    // abdominal_post api 


    public function abdominal_post()
    {

        extract($_POST);
        if ($patient_id != "" and $rmp_id != "") {

            $abdominal = $_FILES["abdominal"]["name"];

            if (!empty($abdominal)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["abdominal"]["name"], PATHINFO_EXTENSION);
                $audio_name = "abdominal-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/abdominal/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                // $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["abdominal"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('abdominal');
                    $fname = $this->upload->data();
                    $aud_name = $audio_name;
                    $param = "File Uploaded";
                }
            }

            $tabledate = date("d-m-Y: h:i a");

            $this->db->query("INSERT INTO sc_abdominal (patient_id,rmp_id,abdominal_comment,abdominal_audio,created_by,created_date) VALUES ('$patient_id','$rmp_id','$abdominal_comment','$aud_name','$rmp_id','$tabledate')");

            $this->response(array('code' => '200', 'message' => 'Abdominal examination is sucessfully created'), 200);
        } else {

            $this->response(array('code' => '404', 'message' => 'enter patient_id and rmp_id'));
        }
    }

    // cns post api
    public function cns_post()
    {

        extract($_POST);
        if ($patient_id != "" and $rmp_id != "") {

            $cns = $_FILES['cns']['name'];

            if (!empty($cns)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["cns"]["name"], PATHINFO_EXTENSION);
                $audio_name = "cns-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/sc/cns/';
                $config['file_name'] = $audio_name;
                $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['mp3', 'MP3', 'mp4', 'MP4', 'WAV', 'wav', '3gp', '3GP', 'mpeg', 'MPEG'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["cns"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('cns');
                    $fname = $this->upload->data();
                    $param = "File Uploaded";
                }
            }

            $tabledate = date("d-m-Y: h:i a");

            $this->db->query("INSERT INTO sc_cns (patient_id,rmp_id,cns_comment,cns_audio,created_by,created_date) VALUES ('$patient_id','$rmp_id','$cns_comment','$audio_name','$rmp_id','$tabledate')");

            $this->response(array('code' => '200', 'message' => 'Cns examination is sucessfully created'), 200);
        } else {

            $this->response(array('code' => '404', 'message' => 'enter patient_id and rmp_id'));
        }
    }



    // abdominal get api

    public function abdominalget_post()
    {
        extract($_POST);

        if ($patient_id != "" and $rmp_id != "") {

            $abdominal = $this->db->query("select * from sc_abdominal where rmp_id='" . $rmp_id . "' and patient_id='" . $patient_id . "'")->result();

            if (!empty($abdominal)) {
                $a = 0;
                foreach ($abdominal as $abd) {


                    $data['abdominal'][$a]['abdominal_id'] = $abd->abdominal_id;
                    $data['abdominal'][$a]['rmp_id'] = $abd->rmp_id;
                    $data['abdominal'][$a]['patient_id'] = $abd->patient_id;
                    $data['abdominal'][$a]['abdominal_comment'] = $abd->abdominal_comment;
                    $data['abdominal'][$a]['abdominal_audio'] = 'http://devumdaa.in/dev/uploads/rural/sc/abdominal/' . $abd->abdominal_audio;
                    $data['abdominal'][$a]['modified_by'] = $abd->modified_by;
                    $data['abdominal'][$a]['created_by'] = $abd->created_by;
                    $data['abdominal'][$a]['created_date'] = $abd->created_date;
                    $data['abdominal'][$a]['modified_date'] = $abd->modified_date;

                    $a++;
                }

                $this->response(array('code' => '200', 'message' => 'success', 'abdominal' => $data['abdominal']));
            } else {
                $this->response(array('code' => '404', 'message' => 'no data fount'));
            }
        } else {
            $this->response(array('code' => '404', 'message' => 'enter patient_id and rmp_id'));
        }
    }


    // sc cns get api


    public function cnsget_post()
    {
        extract($_POST);

        if ($patient_id != "" and $rmp_id != "") {

            $cns = $this->db->query("select * from sc_cns where rmp_id='" . $rmp_id . "' and patient_id='" . $patient_id . "'")->result();

            if (!empty($cns)) {
                $a = 0;
                foreach ($cns as $ns) {


                    $data['cns'][$a]['cns_id'] = $ns->cns_id;
                    $data['cns'][$a]['rmp_id'] = $ns->rmp_id;
                    $data['cns'][$a]['patient_id'] = $ns->patient_id;
                    $data['cns'][$a]['cns_comment'] = $ns->cns_comment;
                    $data['cns'][$a]['cns_audio'] = 'http://devumdaa.in/dev/uploads/rural/sc/cns/' . $ns->cns_audio;
                    $data['cns'][$a]['modified_by'] = $ns->modified_by;
                    $data['cns'][$a]['created_by'] = $ns->created_by;
                    $data['cns'][$a]['created_date'] = $ns->created_date;
                    $data['cns'][$a]['modified_date'] = $ns->modified_date;

                    $a++;
                }

                $this->response(array('code' => '200', 'message' => 'success', 'cns' => $data['cns']));
            } else {
                $this->response(array('code' => '404', 'message' => 'no data fount'));
            }
        } else {
            $this->response(array('code' => '404', 'message' => 'enter patient_id and rmp_id'));
        }
    }

    // patient_search api


    public function patientsearch_get($rmp_id, $search)
    {

        $result = $this->db->query("SELECT * FROM `rural_rmp_patients` WHERE rmp_id='" . $rmp_id . "' AND patient_name LIKE '%" . $search . "%' ORDER BY `patient_id` ASC LIMIT 20")->result();

        if (!empty($result)) {

            $a = 0;
            foreach ($result as $res) {

                $data['patient_search'][$a]['patient_id'] = $res->patient_id;
                $data['patient_search'][$a]['rmp_id'] = $res->rmp_id;
                $data['patient_search'][$a]['patient_name'] = $res->patient_name;
                $data['patient_search'][$a]['patient_gender'] = $res->patient_gender;
                $data['patient_search'][$a]['patient_age'] = $res->patient_age;
                $data['patient_search'][$a]['patient_mobile_number'] = $res->patient_mobile_number;
                $data['patient_search'][$a]['patient_id_proof'] = 'https://www.devumdaa.in/dev/uploads/rural/patient_id_pic/' . $res->patient_id_proof;
                $data['patient_search'][$a]['patient_profile_pic'] = 'https://www.devumdaa.in/dev/uploads/rural/patient_profile_pic/' . $res->patient_profile_pic;
                $data['patient_search'][$a]['patient_location'] = $res->patient_location;
                $data['patient_search'][$a]['modified_by'] = $res->modified_by;
                $data['patient_search'][$a]['created_by'] = $res->created_by;
                $data['patient_search'][$a]['created_date'] = $res->created_date;
                $data['patient_search'][$a]['modified_date'] = $res->modified_date;

                $a++;
            }

            $this->response(array('code' => '200', 'patients_search_list' => $data['patient_search']));
        } else {
            $this->response(array('code' => '404', 'message' => 'no patients found'));
        }
    }

    public function editprofile_post()
    {

        extract($_POST);

        $modified_date = date("d-m-Y: h:i a");

        // $profile=$_FILES['updated_profile_pic']['name'];

        if ($profile_pic == "") {

            $date = date("Y-m-d:H:i:s");
            $this->load->library('upload');
            $filetype = pathinfo($_FILES["updated_profile_pic"]["name"], PATHINFO_EXTENSION);
            $updated_profile_pic = "doctor_pro_pic-" . $date . "." . $filetype;
            $config['upload_path'] = './uploads/rural/doc_profile_pic/';
            $config['file_name'] = $updated_profile_pic;
            $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG';

            $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
            $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
            $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
            $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
            $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

            $files = ['jpg', 'png', 'jpeg'];
            $filename = $_FILES['file_i']['name'];

            // $dp="doctor_pro_pic.jpg";
            if (!in_array($filetype, $files)) {
                $param['type'] = pathinfo($_FILES["updated_profile_pic"]["name"], PATHINFO_EXTENSION);
                $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
            } else {
                $this->upload->initialize($config);
                $this->upload->do_upload('updated_profile_pic');
                $fname = $this->upload->data();
                $profile_pic = $updated_profile_pic;
                $param = "File Uploaded";
            }
            //  $this->db->query("UPDATE rural_rmp_registration SET profile_pic='".$profile_pic."' WHERE rmp_id=1");

            // $this->response(array('code'=>'200','message'=>'File Uploaded','result'=>$param)); 

        }

        if ($rmp_name != "" and $rmp_gender != "" and $rmp_age != "" and $rmp_email != "" and $rmp_phone != ""  and $rmp_clinic_name != "" and $rmp_city != "" and $profile_pic != "") {

            $this->db->query("UPDATE rural_rmp_registration SET rmp_name='" . $rmp_name . "',rmp_gender='" . $rmp_gender . "',rmp_age='" . $rmp_age . "',rmp_email='" . $rmp_email . "',rmp_phone='" . $rmp_phone . "', rmp_clinic_name='" . $rmp_clinic_name . "',rmp_city='" . $rmp_city . "',profile_pic='" . $profile_pic . "',modified_date='" . $modified_date . "' WHERE rmp_id='" . $rmp_id . "'");

            $this->response(array('code' => '200', 'message' => 'profile successfully updated'));
        } else {

            $this->response(array('code' => '404', 'message' => 'enter total details'));
        }

        // $this->db->query("UPDATE rural_rmp_registration SET rmp_name='".$rmp_name."',rmp_gender='".$rmp_gender."',rmp_age='".$rmp_age."',rmp_email='".$rmp_email."',rmp_phone='".$rmp_phone."',rmp_password='".$password."',rmp_clinic_name='".$rmp_clinic_name."',rmp_city='".$rmp_city."',profile_pic='".$profile_pic."',access_token='".$access_token."',modified_date='".$modified_date."' WHERE rmp_id='".$rmp_id."'");

    }

    //  this api is used to add the wallet cash for rmp

    public function addcash_post()
    {
        extract($_POST);

        $rmp_vald = $this->db->query("select * from rural_rmp_registration where rmp_id='" . $rmp_id . "'")->result();

        if (!empty($rmp_vald)) {

            $date = date("d-m-Y: h:i a");

            $transaction = $this->db->query("INSERT INTO payment_history (rmp_id,patient_id,doctor_id,payment_status,amount,payment_date,created_date) VALUES ('$rmp_id','$patient_id','$doctor_id','$payment_status','$amount','$date','$date')");

            $balance = $this->db->query("SELECT SUM(amount) as bal FROM payment_history WHERE rmp_id='" . $rmp_id . "'")->row();

            $wallet_cash = $balance->bal;

            $walletadd = $this->db->query("UPDATE rural_rmp_registration SET wallet_cash='" . $wallet_cash . "' where rmp_id='" . $rmp_id . "'");

            $this->response(array('code' => '200', 'message' => 'cash successfully added'));
        } else {

            $this->response(array('code' => '404', 'message' => 'this rmp_id is not valid'));
        }
    }

    public function paymenthistory_post()
    {

        extract($_POST);

        $rmp_vald = $this->db->query("select * from rural_rmp_registration where rmp_id='" . $rmp_id . "'")->result();

        if (!empty($rmp_vald)) {
            $history = $this->db->query("SELECT rural_doctor_registration.doctor_name,rural_doctor_registration.doctor_hospital,rural_doctor_registration.doctor_phone_number,payment_history.payment_status,payment_history.amount,payment_history.payment_date FROM payment_history RIGHT JOIN rural_doctor_registration ON payment_history.doctor_id = rural_doctor_registration.doctor_id WHERE payment_history.rmp_id='" . $rmp_id . "' ORDER BY payment_history.payment_date ASC")->result();
            // $history=$this->db->query("SELECT * FROM payment_history WHERE rmp_id='".$rmp_id."'")->result();
            $rmp_wallet = $this->db->query("SELECT * FROM rural_rmp_registration WHERE rmp_id='" . $rmp_id . "'")->row();

            $data['wallet_cash'] = $rmp_wallet->rmp_wallet;

            $cash = $rmp_wallet->wallet_cash;
            $data['wallet_cash'] = $cash;


            $a = 0;

            foreach ($history as $hist) {

                $doc_details = $this->db->query("SELECT * FROM rural_doctor_registration WHERE doctor_id='" . $hist->doctor_id . "'")->result();

                $data['transaction_history'][$a]['doctor_name'] = $hist->doctor_name;
                $data['transaction_history'][$a]['doctor_hospital'] = $hist->doctor_hospital;
                $data['transaction_history'][$a]['doctor_phone_number'] = $hist->doctor_phone_number;
                $data['transaction_history'][$a]['payment_status'] = $hist->payment_status;
                $data['transaction_history'][$a]['amount'] = $hist->amount;
                $data['transaction_history'][$a]['payment_date'] = $hist->payment_date;
                $a++;
            }

            $this->response(array('code' => '200', 'message' => $data));
        } else {
            $this->response(array('code' => '404', 'message' => 'enter patient_id and rmp_id'));
        }
    }


    public function requestcash_post()
    {
        extract($_POST);
        $date = date("d-m-Y: h:i a");

        if ($request_amount != "") {

            $payment_request = $this->db->query("INSERT INTO cash_request (rmp_id,request_amount,request_status,created_by,created_date) VALUES ('$rmp_id','$request_amount','pending','$rmp_id','$date')");

            $this->response(array('code' => '200', 'message' => 'your request created successfully'));
        } else {
        }
    }




    /*

    patient summary api

  this api get total report details of the patient


*/


    public function patientsummary_post()
    {

        extract($_POST);

        $patient = $this->db->query("select * from patients where patient_id='" . $patient_id . "'")->row();

        $patient_details['first_name'] = $patient->first_name;
        $patient_details['last_name'] = $patient->last_name;
        $patient_details['age'] = $patient->age;
        $patient_details['age_unit'] = $patient->age_unit;
        $patient_details['patient_image'] = base_url() . "uploads/rural/patient_profile_pic/" . $patient->photo;
        $patient_details['gender'] = $patient->gender;
        $patient_details['date_of_birth'] = $patient->date_of_birth;
        $patient_details['umr_no'] = $patient->umr_no;
        $patient_details['mobile'] =  DataCrypt($patient->mobile, 'decrypt');
        $patient_details['location'] = $patient->location;

        $vitals = $this->db->query("SELECT * FROM rural_rmp_patient_vitals WHERE patient_id='" . $patient_id . "' AND rmp_id='" . $rmp_id . "' ORDER BY rural_rmp_patient_vitals.rmp_patient_vitals_id DESC LIMIT 1")->row();

        $gpe = $this->db->query("SELECT * FROM patient_gpe_images WHERE patient_id='" . $patient_id . "' AND rmp_id='" . $rmp_id . "' ORDER BY patient_gpe_images.patient_gpe_img_id DESC LIMIT 1")->row();

        $gpe_images['rmp_id'] = $gpe->rmp_id;
        $gpe_images['patient_id'] = $gpe->patient_id;
        $gpe_images['patient_oral_cavity'] = !empty($gpe->patient_oral_cavity) ?  base_url() . 'uploads/rural/Gpe_files/patient_oral_cavity/' . $gpe->patient_oral_cavity : "";
        $gpe_images['patient_neck'] = !empty($gpe->patient_neck) ? base_url() . 'uploads/rural/Gpe_files/patient_neck/' . $gpe->patient_neck : "";
        $gpe_images['patient_dorsal_hand'] = !empty($gpe->patient_dorsal_hand) ?  base_url() . 'uploads/rural/Gpe_files/patient_dorsal_hand/' . $gpe->patient_dorsal_hand : "";
        $gpe_images['patient_palms'] = !empty($gpe->patient_palms) ? base_url() . 'uploads/rural/Gpe_files/patient_palms/' . $gpe->patient_palms : "";
        $gpe_images['patient_leg'] = !empty($gpe->patient_leg) ? base_url() . 'uploads/rural/Gpe_files/patient_leg/' . $gpe->patient_leg : "";
        $gpe_images['patient_sclera'] = base_url() . 'uploads/rural/Gpe_files/patient_sclera/' . $gpe->patient_sclera;
        $gpe_images['patient_palpebral_conjunctiva'] = !empty($gpe->patient_palpebral_conjunctiva) ? base_url() . 'uploads/rural/Gpe_files/patient_palpebral_conjunctiva/' . $gpe->patient_palpebral_conjunctiva : "";
        $gpe_images['modified_by'] = $gpe->modified_by;
        $gpe_images['created_by'] = $gpe->created_by;
        $gpe_images['created_date'] = $gpe->created_date;
        $gpe_images['modified_date'] = $gpe->modified_date;


        $sc_abdominal = $this->db->query("SELECT * FROM sc_abdominal WHERE patient_id='" . $patient_id . "' AND rmp_id='" . $rmp_id . "' ORDER BY sc_abdominal.abdominal_id DESC LIMIT 1")->row();

        $abdominal['rmp_id'] = $sc_abdominal->rmp_id;
        $abdominal['patient_id'] = $sc_abdominal->patient_id;
        $abdominal['abdominal_comment'] = $sc_abdominal->abdominal_comment;
        $abdominal['abdominal_audio'] =  !empty($sc_abdominal->abdominal_audio) ?  base_url() . 'uploads/rural/sc/abdominal/' . $sc_abdominal->abdominal_audio : '';
        $abdominal['modified_by'] = $sc_abdominal->modified_by;
        $abdominal['created_by'] = $sc_abdominal->created_by;
        $abdominal['created_date'] = $sc_abdominal->created_date;
        $abdominal['modified_date'] = $sc_abdominal->modified_date;

        $sc_cardio = $this->db->query("SELECT * FROM sc_cardio WHERE patient_id='" . $patient_id . "' AND rmp_id='" . $rmp_id . "' ORDER BY sc_cardio.sc_cardio_id DESC LIMIT 1")->row();

        $cardio['rmp_id'] = $sc_cardio->rmp_id;
        $cardio['patient_id'] = $sc_cardio->patient_id;
        $cardio['mitral_audio'] = !empty($sc_cardio->mitral_audio) ? base_url() . 'uploads/rural/sc/Cardio/Mitral/' . $sc_cardio->mitral_audio : "";
        $cardio['arotic_audio'] = !empty($sc_cardio->arotic_audio) ? base_url() . 'uploads/rural/sc/Cardio/Arotic/' . $sc_cardio->arotic_audio : "";
        $cardio['pulmonary_audio'] = !empty($sc_cardio->pulmonary_audio) ? base_url() . 'uploads/rural/sc/Cardio/Pulmonary/' . $sc_cardio->pulmonary_audio : "";
        $cardio['tricuspid_audio'] = !empty($sc_cardio->tricuspid_audio) ? base_url() . 'uploads/rural/sc/Cardio/Tricuspid/' . $sc_cardio->tricuspid_audio : "";
        $cardio['modified_by'] = $sc_cardio->modified_by;
        $cardio['created_by'] = $sc_cardio->created_by;
        $cardio['created_date'] = $sc_cardio->created_date;
        $cardio['modified_date'] = $sc_cardio->modified_date;

        $sc_cns = $this->db->query("SELECT * FROM sc_cns WHERE patient_id='" . $patient_id . "' AND rmp_id='" . $rmp_id . "' ORDER BY sc_cns.cns_id DESC LIMIT 1")->row();

        $cns['rmp_id'] = $sc_cns->rmp_id;
        $cns['patient_id'] = $sc_cns->patient_id;
        $cns['cns_comment'] = $sc_cns->cns_comment;
        $cns['cns_audio'] = !empty($sc_cns->cns_audio) ? base_url() . 'uploads/rural/sc/cns/' . $sc_cns->cns_audio : "";
        $cns['modified_by'] = $sc_cns->modified_by;
        $cns['created_by'] = $sc_cns->created_by;
        $cns['created_date'] = $sc_cns->created_date;
        $cns['modified_date'] = $sc_cns->modified_date;


        $sc_respiration = $this->db->query("SELECT * FROM sc_respiration WHERE patient_id='" . $patient_id . "' AND rmp_id='" . $rmp_id . "' ORDER BY sc_respiration.sc_respiration_id DESC LIMIT 1")->row();

        $respiration['rmp_id'] = $sc_respiration->rmp_id;
        $respiration['patient_id'] = $sc_respiration->patient_id;
        $respiration['right_infra_clavicle_audio'] = !empty($sc_respiration->right_infra_clavicle_audio) ?  base_url() . 'uploads/rural/sc/Resp_front/Right_Infra_Clavicle/' . $sc_respiration->right_infra_clavicle_audio : "";
        $respiration['left_infra_clavicle_audio'] = !empty($sc_respiration->left_infra_clavicle_audio) ? base_url() . 'uploads/rural/sc/Resp_front/Left_Infra_Clavicle/' . $sc_respiration->left_infra_clavicle_audio : "";
        $respiration['right_infra_mammary_audio'] = !empty($sc_respiration->right_infra_mammary_audio) ? base_url() . 'uploads/rural/sc/Resp_front/Right_Infra_Mammary/' . $sc_respiration->right_infra_mammary_audio : "";
        $respiration['left_infra_mammary_audio'] = !empty($sc_respiration->left_infra_mammary_audio) ? base_url() . 'uploads/rural/sc/Resp_front/Left_Infra_Mammary/' . $sc_respiration->left_infra_mammary_audio : "";
        $respiration['left_interscapular_audio'] = !empty($sc_respiration->left_interscapular_audio) ? base_url() . 'uploads/rural/sc/resp_back/Left_Interscapular/' . $sc_respiration->left_interscapular_audio : "";
        $respiration['right_interscapular_audio'] = !empty($sc_respiration->right_interscapular_audio) ? base_url() . 'uploads/rural/sc/resp_back/Right_Interscapular/' . $sc_respiration->right_interscapular_audio : "";
        $respiration['left_infrascapular_audio'] = !empty($sc_respiration->left_infrascapular_audio) ? base_url() . 'uploads/rural/sc/resp_back/Left_Infrascapular/' . $sc_respiration->left_infrascapular_audio : "";
        $respiration['right_infrascapular_audio'] = !empty($sc_respiration->right_infrascapular_audio) ?  base_url() . 'uploads/rural/sc/resp_back/Right_Infrascapular/' . $sc_respiration->right_infrascapular_audio : "";
        $respiration['modified_by'] = $sc_respiration->modified_by;
        $respiration['created_by'] = $sc_respiration->created_by;
        $respiration['created_date'] = $sc_respiration->created_date;
        $respiration['modified_date'] = $sc_respiration->modified_date;


        $symptoms = $this->db->query("SELECT * FROM patient_symptoms WHERE patient_id='" . $patient_id . "' AND rmp_id='" . $rmp_id . "' ORDER by patient_symptoms.patient_symptom_id DESC LIMIT 1")->row();

        $patient_symptoms['rmp_id'] = $symptoms->rmp_id;
        $patient_symptoms['patient_id'] = $symptoms->patient_id;
        $patient_symptoms['describe_symptom_text'] = $symptoms->describe_symptom_text;
        $patient_symptoms['symptoms_audio'] = !empty($symptoms->symptoms_audio) ?  base_url() . 'uploads/rural/patient_symptoms/patient_symptoms_audio/' . $symptoms->symptoms_audio : "";
        $patient_symptoms['symptoms_duration'] = $symptoms->symptoms_duration;
        $patient_symptoms['symptom_range'] = $symptoms->symptom_range;
        $patient_symptoms['modified_by'] = $symptoms->modified_by;
        $patient_symptoms['created_by'] = $symptoms->created_by;
        $patient_symptoms['created_date'] = $symptoms->created_date;
        $patient_symptoms['modified_date'] = $symptoms->modified_date;

        $patient_reports = $this->db->query("SELECT * FROM patients_reports pr INNER JOIN patient_report_line_items prl ON pr.patients_report_id=prl.patients_report_id WHERE pr.rmp_id='" . $rmp_id . "' AND pr.patient_id='" . $patient_id . "'")->result();
        $patient_report_data = [];
        $i = 0;
        $patient_report_data['desc'] = $patient_reports[0]->description;

        foreach ($patient_reports as $pr) {

            $patient_report_data['images'][$i] = !empty($pr->report_image) ? base_url() . 'uploads/rural/patient_reports/' . $pr->report_image : "";
            $i++;
        }

        $this->response(array('code' => '200', 'patient_summary' => array('vitals' => $vitals, 'gpe' => $gpe_images, 'sc_abdominal' => $abdominal, 'sc_cardio' => $cardio, 'sc_cns' => $cns, 'sc_respiration' => $respiration, 'symptoms' => $patient_symptoms, 'reports' => $patient_report_data, 'patient_details' => $patient_details)));

        // $final=(array_merge($vitals,$gpe,$sc_abdominal,$sc_cardio,$sc_cns,$sc_respiration,$symptoms));

        // $data['vitals']=$vitals;
        // $data['gpe']=$gpe;
        // $data['sc_abdominal']=$sc_abdominal;
        // $data['sc_cardio']=$sc_cardio;
        // $data['sc_cns']=$sc_cns;
        // $data['sc_respiration']=$sc_respiration;
        // $data['symptoms']=$symptoms;


        // $this->response(array('code'=>'200','response'=> $data));  
    }



    //  this api is used to display total general doctors

    public function totalgeneraldoctors_post()
    {

        $total_general = $this->db->query("select * from rural_doctor_registration where doctor_type='general' order by doctor_id")->result();
        $a = 0;
        foreach ($total_general as $doc) {

            $languages = $this->db->query("SELECT GROUP_CONCAT(l.Language) as doclanguages FROM `doctor_languages` dl,Languages l where l.Languages_id=dl.languages_id and dl.doctor_id='" . $doc->doctor_id . "'")->row();

            $data['total_general_doctors'][$a]['doctor_id'] = $doc->doctor_id;
            $data['total_general_doctors'][$a]['doctor_name'] = $doc->doctor_name;
            $data['total_general_doctors'][$a]['doctor_age'] = $doc->doctor_age;
            $data['total_general_doctors'][$a]['doctor_gender'] = $doc->doctor_gender;
            $data['total_general_doctors'][$a]['doctor_email'] = $doc->doctor_email;
            $data['total_general_doctors'][$a]['doctor_phone_number'] = $doc->doctor_phone_number;
            $data['total_general_doctors'][$a]['doctor_hospital'] = $doc->doctor_hospital;
            $data['total_general_doctors'][$a]['doctor_city'] = $doc->doctor_city;
            $data['total_general_doctors'][$a]['doctor_profile_pic'] = $doc->doctor_profile_pic;
            $data['total_general_doctors'][$a]['appointment_charge'] = $doc->appointment_charge;
            $data['total_general_doctors'][$a]['doctor_experience'] = $doc->doctor_experience;
            $data['total_general_doctors'][$a]['languages'] = $languages->doclanguages;

            $a++;
        }

        $this->response(array('code' => '200', 'total_general_doctors' =>  $data['total_general_doctors']));
    }

    //  this api is used to display gender wise doctors

    public function generaldoctorsgender_post()
    {

        extract($_POST);
        if (!empty($doctor_gender)) {

            $gender_doctor = $this->db->query("select * from rural_doctor_registration where doctor_type='general' and doctor_gender='" . $doctor_gender . "'")->result();

            if (empty(!$gender_doctor)) {

                $a = 0;

                foreach ($gender_doctor as $doc) {

                    $languages = $this->db->query("SELECT GROUP_CONCAT(l.Language) as doclanguages FROM `doctor_languages` dl,Languages l where l.Languages_id=dl.languages_id and dl.doctor_id='" . $doc->doctor_id . "'")->row();


                    $data['total_general_doctors'][$a]['doctor_id'] = $doc->doctor_id;
                    $data['total_general_doctors'][$a]['doctor_name'] = $doc->doctor_name;
                    $data['total_general_doctors'][$a]['doctor_age'] = $doc->doctor_age;
                    $data['total_general_doctors'][$a]['doctor_gender'] = $doc->doctor_gender;
                    $data['total_general_doctors'][$a]['doctor_email'] = $doc->doctor_email;
                    $data['total_general_doctors'][$a]['doctor_phone_number'] = $doc->doctor_phone_number;
                    $data['total_general_doctors'][$a]['doctor_hospital'] = $doc->doctor_hospital;
                    $data['total_general_doctors'][$a]['doctor_city'] = $doc->doctor_city;
                    $data['total_general_doctors'][$a]['doctor_profile_pic'] = $doc->doctor_profile_pic;
                    $data['total_general_doctors'][$a]['appointment_charge'] = $doc->appointment_charge;
                    $data['total_general_doctors'][$a]['doctor_experience'] = $doc->doctor_experience;
                    $data['total_general_doctors'][$a]['doctor_languages'] = $languages->doclanguages;
                    $a++;
                }
                $this->response(array('code' => '200', 'total_' . $doctor_gender . '_doctors' =>  $data['total_general_doctors']));
            } else {

                $this->response(array('code' => '404', 'response' => 'there is no ' . $doctor_gender . ' doctors'));
            }
        } else {
            $this->response(array('code' => '404', 'response' => 'enter doctor_gender'));
        }
    }


    public function generaldoctorlanguage_post()
    {

        extract($_POST);

        // $mater=$this->db->query("SELECT * FROM rural_doctor_registration,Languages,doctor_languages WHERE doctor_languages.Languages_id=Languages.Languages_id AND rural_doctor_registration.doctor_id=doctor_languages.doctor_id AND Languages.Languages_id='.$language_id.'")->result();

        $lang_gend = $this->db->query("SELECT * FROM rural_doctor_registration,Languages,doctor_languages WHERE doctor_languages.Languages_id=Languages.Languages_id AND rural_doctor_registration.doctor_id=doctor_languages.doctor_id AND Languages.Languages_id='" . $language_id . "' AND rural_doctor_registration.doctor_gender='" . $doctor_gender . "'")->result();

        $a = 0;

        foreach ($lang_gend as $ma) {

            $data['general_doctor_language'][$a]['doctor_name'] = $ma->doctor_name;
            $data['general_doctor_language'][$a]['doctor_age'] = $ma->doctor_age;
            $data['general_doctor_language'][$a]['doctor_gender'] = $ma->doctor_gender;
            $data['general_doctor_language'][$a]['doctor_phone_number'] = $ma->doctor_phone_number;
            $data['general_doctor_language'][$a]['doctor_hospital'] = $ma->doctor_hospital;
            $data['general_doctor_language'][$a]['doctor_profile_pic'] = $ma->doctor_profile_pic;
            $data['general_doctor_language'][$a]['doctor_type'] = $ma->doctor_type;
            $data['general_doctor_language'][$a]['doctor_experience'] = $ma->doctor_experience;

            $a++;
        }

        $this->response(array('code' => '200', 'total_departments' =>  $data['general_doctor_language']));
    }


    //  this api is used to display total departments
    public function totaldepartments_get()
    {

        $departments = $this->db->query("CALL department();")->result();
        $a = 0;
        foreach ($departments as $dep) {
            $data['total_departments'][$a]['department_id'] = $dep->department_id;
            $data['total_departments'][$a]['department_name'] = $dep->department_name;
            $data['total_departments'][$a]['department_icon'] = 'http://devumdaa.in/dev/uploads/rural/department_icons/' . $dep->department_icon;
            $a++;
        }

        $this->response(array('code' => '200', 'total_departments' => $data['total_departments']));
    }

    public function totallanguages_get()
    {

        $languages = $this->db->query("SELECT Languages_id,Language FROM `Languages`")->result();

        $this->response(array('code' => '200', 'languages' => $languages));
    }

    /**
     * this api is used to filter the general doctor filter **/

    public function generaldoctorfilters_post()
    {

        extract($_POST);

        if ($doctor_gender != "") {
            $genderQ = " AND doctor_gender = '" . $doctor_gender . "'";
        }
        if ($doctor_language != "") {
            $doct_lang = "AND rd.doctor_id=dl.doctor_id AND dl.languages_id IN(" . $doctor_language . ")";
        }


        $filter_reslut = $this->db->query("SELECT * FROM rural_doctor_registration rd,doctor_languages dl WHERE rd.doctor_type='general'$genderQ $doct_lang")->result();

        if (!empty($filter_reslut)) {

            $a = 0;

            foreach ($filter_reslut as $res) {

                $languages = $this->db->query("SELECT GROUP_CONCAT(l.Language) as doclanguages FROM `doctor_languages` dl,Languages l where l.Languages_id=dl.languages_id and dl.doctor_id='" . $res->doctor_id . "'")->row();

                $data['filter_result'][$a]['doctor_id'] = $res->doctor_id;
                $data['filter_result'][$a]['doctor_name'] = $res->doctor_name;
                $data['filter_result'][$a]['doctor_age'] = $res->doctor_age;
                $data['filter_result'][$a]['doctor_gender'] = $res->doctor_gender;
                $data['filter_result'][$a]['doctor_email'] = $res->doctor_email;
                $data['filter_result'][$a]['doctor_phone_number'] = $res->doctor_phone_number;
                $data['filter_result'][$a]['doctor_hospital'] = $res->doctor_hospital;
                $data['filter_result'][$a]['doctor_city'] = $res->doctor_city;
                $data['filter_result'][$a]['doctor_profile_pic'] = 'http://devumdaa.in/dev/uploads/rural/test/' . $res->doctor_profile_pic;  #pic
                $data['filter_result'][$a]['doctor_type'] = $res->doctor_type;
                $data['filter_result'][$a]['doctor_experience'] = $res->doctor_experience;
                $data['filter_result'][$a]['languages'] = $languages->doclanguages;

                $a++;
            }

            $this->response(array('code' => '200', 'response' => $data['filter_result']));
        } else {
            $this->response(array('code' => '200', 'response' => 'there is no doctors'));
        }
    }

    // doctors specialist api with filters

    /**
    this api is used to filter the specialist wise 
     **/
    public function specialistfilter_post()
    {

        extract($_POST);
        $mater = "";
        if ($doctor_gender != "") {
            $genderQ = " AND doctor_gender = '" . $doctor_gender . "'";
        }
        if ($doctor_language != "") {
            $doct_lang = "AND rd.doctor_id=dl.doctor_id AND dl.languages_id IN(" . $doctor_language . ")";
        }

        $filter_reslut = $this->db->query("SELECT * FROM rural_doctor_registration rd,doctor_languages dl WHERE rd.doctor_type='" . $doctor_type . "' $genderQ $doct_lang")->result();

        if (!empty($filter_reslut)) {

            $a = 0;

            foreach ($filter_reslut as $res) {

                $languages = $this->db->query("SELECT GROUP_CONCAT(l.Language) as doclanguages FROM `doctor_languages` dl,Languages l where l.Languages_id=dl.languages_id and dl.doctor_id='" . $res->doctor_id . "'")->row();

                $data['filter_result'][$a]['doctor_id'] = $res->doctor_id;
                $data['filter_result'][$a]['doctor_name'] = $res->doctor_name;
                $data['filter_result'][$a]['doctor_age'] = $res->doctor_age;
                $data['filter_result'][$a]['doctor_gender'] = $res->doctor_gender;
                $data['filter_result'][$a]['doctor_email'] = $res->doctor_email;
                $data['filter_result'][$a]['doctor_phone_number'] = $res->doctor_phone_number;
                $data['filter_result'][$a]['doctor_hospital'] = $res->doctor_hospital;
                $data['filter_result'][$a]['doctor_city'] = $res->doctor_city;
                $data['filter_result'][$a]['doctor_profile_pic'] = 'http://devumdaa.in/dev/uploads/rural/test/' . $res->doctor_profile_pic;  #pic
                $data['filter_result'][$a]['doctor_type'] = $res->doctor_type;
                $data['filter_result'][$a]['doctor_experience'] = $res->doctor_experience;
                $data['filter_result'][$a]['languages'] = $languages->doclanguages;

                $a++;
            }

            $this->response(array('code' => '200', 'response' => $data['filter_result']));
        } else {
            $this->response(array('code' => '200', 'response' => 'there is no doctors'));
        }
    }



    // testing purpose filter


    /**
     * this api is used to filter the general doctor filter **/

    public function testgeneraldoctorfilters_post()
    {

        extract($_POST);

        if ($doctor_gender == "male" or $doctor_gender == "female") {
            $genderQ = " AND doctor_gender = '" . $doctor_gender . "'";
        }
        if ($doctor_language != "") {
            $doct_lang = "AND rd.doctor_id=dl.doctor_id AND dl.languages_id IN(" . $doctor_language . ")";
        }


        $filter_reslut = $this->db->query("SELECT DISTINCT(rd.doctor_id) FROM rural_doctor_registration rd,doctor_languages dl WHERE rd.doctor_type='general'$genderQ $doct_lang")->result();

        foreach ($filter_reslut as $doc_id) {

            $this->response(array('code' => '200', 'response' => 'hai'));

            $result = $this->db->query("SELECT * FROM rural_doctor_registration WHERE doctor_id='" . $doc_id->doctor_id . "'")->result();

            $a = 0;

            foreach ($result as $res) {


                $data['filter_result'][$a]['doctor_id'] = $res->doctor_id;
                $data['filter_result'][$a]['doctor_name'] = $res->doctor_name;
                $data['filter_result'][$a]['doctor_age'] = $res->doctor_age;
                $data['filter_result'][$a]['doctor_gender'] = $res->doctor_gender;
                $data['filter_result'][$a]['doctor_email'] = $res->doctor_email;
                $data['filter_result'][$a]['doctor_phone_number'] = $res->doctor_phone_number;
                $data['filter_result'][$a]['doctor_hospital'] = $res->doctor_hospital;
                $data['filter_result'][$a]['doctor_city'] = $res->doctor_city;
                $data['filter_result'][$a]['doctor_profile_pic'] = 'http://devumdaa.in/dev/uploads/rural/test/' . $res->doctor_profile_pic;  #pic
                $data['filter_result'][$a]['doctor_type'] = $res->doctor_type;
                $data['filter_result'][$a]['doctor_experience'] = $res->doctor_experience;

                $a++;
            }
            $this->response(array('code' => '200', 'response' => $data['filter_result']));
        }






        // if(!empty($filter_reslut)){

        //             $a=0;

        // foreach($filter_reslut as $res){

        //     $languages = $this->db->query("SELECT GROUP_CONCAT(l.Language) as doclanguages FROM `doctor_languages` dl,Languages l where l.Languages_id=dl.languages_id and dl.doctor_id='".$res->doctor_id."'")->row();

        //     $data['filter_result'][$a]['doctor_id']=$res->doctor_id;
        //     $data['filter_result'][$a]['doctor_name']=$res->doctor_name;
        //     $data['filter_result'][$a]['doctor_age']=$res->doctor_age;
        //     $data['filter_result'][$a]['doctor_gender']=$res->doctor_gender;
        //     $data['filter_result'][$a]['doctor_email']=$res->doctor_email;
        //     $data['filter_result'][$a]['doctor_phone_number']=$res->doctor_phone_number;
        //     $data['filter_result'][$a]['doctor_hospital']=$res->doctor_hospital;
        //     $data['filter_result'][$a]['doctor_city']=$res->doctor_city;
        //     $data['filter_result'][$a]['doctor_profile_pic']='http://devumdaa.in/dev/uploads/rural/test/'.$res->doctor_profile_pic;  #pic
        //     $data['filter_result'][$a]['doctor_type']=$res->doctor_type;
        //     $data['filter_result'][$a]['doctor_experience']=$res->doctor_experience;
        //     $data['filter_result'][$a]['languages']=$languages->doclanguages;

        //     $a++;
        // }

        //     $this->response(array('code'=>'200','response'=> $data['filter_result'])); 

        // }
        // else
        // {
        //     $this->response(array('code'=>'200','response'=> 'there is no doctors'));
        // }

    }



    public function payChecksum_get($parameters, $method, $user_id)
    {
        extract($parameters);

        $paytmParams = array();

        $paytmParams["MID"] = "UuWuPW69290928887235";
        $paytmParams["ORDERID"] = $ORDER_ID;
        $paytmParams["CHANNEL_ID"] = $CHANNEL_ID; //WEB for mobile
        $paytmParams["CUST_ID"] = $CUST_ID;
        $paytmParams["INDUSTRY_TYPE_ID"] = $INDUSTRY_TYPE_ID; //Retail
        $paytmParams["WEBSITE"] = $WEBSITE;  //WEBSTAGING
        $paytmParams["TXN_AMOUNT"] = $TXN_AMOUNT;
        $paytmParams["CALLBACK_URL"] = $CALLBACK_URL;

        $paytmChecksum = PaytmChecksum::generateSignature($paytmParams, 'UuWuPW69290928887235');

        $verifySignature = PaytmChecksum::verifySignature($paytmParams, 'UuWuPW69290928887235', $paytmChecksum);

        $paytmParams['checksum'] = $paytmChecksum;
        $paytmParams['signature'] = $verifySignature;

        $this->response(array('code' => '200', 'message' => 'Success', 'result' => $paytmParams, 'requestname' => $method));
    }



    public function consultant_post()
    {
        $consultant = $this->db->query("SELECT * FROM doctor_consult_types")->result();
        $i = 0;
        foreach ($consultant as $con) {

            $result[$i]['consult_type_id'] = $con->consult_type_id;
            $result[$i]['consult_type_name'] = $con->consult_type_name;
            $result[$i]['consult_type_price'] = $con->consult_type_price;
            $result[$i]['consult_type_image'] = $con->consult_type_image;

            $i++;
        }

        $this->response(array('code' => '200', 'message' => 'consultant_types', 'result' => $consultant));
    }


    public function departments_post()
    {
        $departments = $this->db->query("SELECT * FROM department  ORDER BY department.positions ASC")->result();

        $i = 0;

        foreach ($departments as $dept) {

            $result[$i]['department_id'] = $dept->department_id;
            $result[$i]['department_name'] = $dept->department_name;
            $result[$i]['department_icon'] = base_url('uploads/departments/' . $dept->department_icon);
            $result[$i]['status'] = $dept->status;
            $result[$i]['review'] = $dept->review;

            $i++;
        }

        $this->response(array('code' => '200', 'message' => 'departments', 'result' => $result));
    }

    public function consultant_type_mbbs_post()
    {
        extract($_POST);

        // $order_by = '';
        if ($order_by == "price_desc") {
            $order_by = ' ORDER BY clinic_doctor.online_consulting_fee DESC ';
        }
        if ($order_by == "price_asc") {
            $order_by = ' ORDER BY clinic_doctor.online_consulting_fee ASC ';
        }
        if ($order_by == "experience_desc") {
            $order_by = ' ORDER BY doctors.experience DESC ';
        }
        if ($order_by == "experience_asc") {
            $order_by = ' ORDER BY doctors.experience ASC ';
        }
        $search_res = "";
        if (!empty($search)) {

            $search_res = " where doctors.first_name like '%" . $search . "%' or doctors.last_name like '%" . $search . "%'";
        }


        $result = $this->db->query("SELECT doctors.doctor_id,doctors.salutation,doctors.first_name,doctors.last_name,doctors.experience,doctors.qualification,doctors.languages,doctors.profile_image,department.department_name,doctors.gender,clinic_doctor.online_consulting_fee FROM doctors INNER JOIN department ON doctors.department_id=department.department_id INNER JOIN clinic_doctor ON clinic_doctor.doctor_id=doctors.doctor_id " . $search_res . " GROUP BY doctors.doctor_id " . $order_by)->result();

        $j = 0;

        foreach ($result as $res) {

            $arr = explode(",", $res->qualification);
            if (in_array('MD', $arr))
                continue;
            $MD[$j]['doctor_id'] = $res->doctor_id;
            $MD[$j]['name'] = $res->first_name . " " . $res->last_name;
            $MD[$j]['experience'] = $res->experience;
            $MD[$j]['qualification'] = $res->qualification;
            $MD[$j]['languages'] = $this->languagetrans($res->doctor_id);
            $MD[$j]['profile_image'] =  base_url('uploads/doctors/') . $res->profile_image;
            $MD[$j]['department_name'] = $res->department_name;
            $MD[$j]['gender'] = $res->gender;
            $MD[$j]['online_consulting_fee'] = $res->online_consulting_fee;
            $j++;
        }
        $this->response(array('code' => '200', 'message' => $doc_type . '_doctors_list', 'result' =>  $MD));
    }



    public function consultant_type_md_post()
    {
        extract($_POST);

        // $order_by = '';
        if ($order_by == "price_desc") {
            $order_by = " ORDER BY clinic_doctor.online_consulting_fee DESC ";
        }
        if ($order_by == "price_asc") {
            $order_by = " ORDER BY clinic_doctor.online_consulting_fee ASC ";
        }
        if ($order_by == "experience_desc") {
            $order_by = " ORDER BY doctors.experience DESC ";
        }
        if ($order_by == "experience_asc") {
            $order_by = " ORDER BY doctors.experience ASC ";
        }

        $search_result = "";

        if (!empty($search)) {
            $search_result = "where doctors.first_name like '%" . $search . "%' OR doctors.last_name like '%" . $search . "%'";
        }

        $result = $this->db->query("SELECT doctors.doctor_id,doctors.salutation,doctors.first_name,doctors.last_name,doctors.experience,doctors.qualification,doctors.languages,doctors.profile_image,department.department_name,doctors.gender,clinic_doctor.online_consulting_fee FROM doctors INNER JOIN department ON doctors.department_id=department.department_id INNER JOIN clinic_doctor ON clinic_doctor.doctor_id=doctors.doctor_id " . $search_result . "GROUP BY doctors.doctor_id" . $order_by)->result();
        // echo $this->db->last_query();
        $j = 0;

        foreach ($result as $res) {

            $arr = explode(",", $res->qualification);
            if (!in_array('MD', $arr))
                continue;
            $MD[$j]['doctor_id'] = $res->doctor_id;
            $MD[$j]['name'] = $res->first_name . " " . $res->last_name;
            $MD[$j]['experience'] = $res->experience;
            $MD[$j]['qualification'] = $res->qualification;
            $MD[$j]['languages'] = $this->languagetrans($res->doctor_id);
            $MD[$j]['profile_image'] =  base_url('uploads/doctors/') . $res->profile_image;
            $MD[$j]['department_name'] = $res->department_name;
            $MD[$j]['gender'] = $res->gender;
            $MD[$j]['online_consulting_fee'] = $res->online_consulting_fee;
            $j++;
        }
        $this->response(array('code' => '200', 'message' => $doc_type . '_doctors_list', 'result' =>  $MD));
    }


    public function doctor_info_post()
    {
        extract($_POST);

        $doctor_info = $this->db->query("SELECT doctors.doctor_id,doctors.first_name,doctors.last_name,doctors.gender,doctors.qualification,doctors.experience,doctors.department_id,doctors.profile_image,doctors.languages,doctors.address,doctors.department_id,department.department_id,department.department_name FROM doctors,department where doctors.department_id=department.department_id and doctor_id='" . $dcotor_id . "'")->row();


        $result['doctor_id'] = $doctor_info->doctor_id;
        $result['name'] = $doctor_info->salutation . " " . $doctor_info->first_name . " " . $doctor_info->last_name;
        $result['experience'] = $doctor_info->experience;
        $result['qualification'] = $doctor_info->qualification;
        $result['languages'] = $doctor_info->languages;
        $result['profile_image'] = base_url('uploads/doctors/' . $doctor_info->profile_image);
        $result['department_name'] = $doctor_info->department_name;
        $result['gender'] = $doctor_info->gender;
        $result['online_consulting_fee'] = $doctor_info->online_consulting_fee;
        $result['address'] = $doctor_info->address;
        $this->response(array('code' => '200', 'message' => 'doctors_details', 'doctor_info' =>  $result));
    }

    public function tele_slots_post()
    {
        extract($_POST);

        if ($doctor_id != "") {

            $days = array('', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
            $check = $this->db->query("select cwd.slot,cd.clinic_id,cd.online_consulting_time,cwd.clinic_doctor_weekday_id,cwd.weekday from clinic_doctor cd,clinic_doctor_weekdays cwd where cd.clinic_doctor_id=cwd.clinic_doctor_id and cd.doctor_id='" . $doctor_id . "' and cwd.slot='video call' group by cwd.weekday,cd.clinic_id")->result();
            // echo $this->db->last_query();
            // print_r($check);
            if ($check[0]->online_consulting_time == "" and $check[0]->online_consulting_time == null) {
                $time = "10";
            } else {
                $time = $check[0]->online_consulting_time;
            }

            if (count($check) > 0) {
                $i = 0;
                foreach ($check as $value) {
                    $slotsInfo = $this->db->query("select * from clinic_doctor_weekday_slots where clinic_doctor_weekday_id='" . $value->clinic_doctor_weekday_id . "'")->result();


                    if (count($slotsInfo) > 0) {
                        $j = 0;
                        $para['consultation'][$i]['day'] = $days[$value->weekday];
                        foreach ($slotsInfo as $val) {
                            $para['consultation'][$i]['timings'][$j]['session'] = $val->session;
                            $para['consultation'][$i]['timings'][$j]['schedule'] = date('h:i A', strtotime($val->from_time)) . '-' . date('h:i A', strtotime($val->to_time));

                            $j++;
                        }
                    }
                    $i++;
                }

                $this->response(array('code' => '200', 'message' => 'Success', 'slot_duration' =>  $time, 'result' => $para));
            } else {
                $para['consultation'] = [];
                $this->response(array('code' => '201', 'message' => 'Slots Not Present', 'result' => $para));
            }
        } else {
            $para['consultation'] = [];
            $this->response(array('code' => '201', 'message' => 'Error Occured', 'result' => $para));
        }
    }

    public function booked_slots_blocked_slots_post()
    {
        extract($_POST);
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
            $bs = $this->db->select("*")->from("appointments")->where("doctor_id='" . $doctor_id . "' and appointment_date='" . $date . "'")->get()->result();
            if (count($bs) > 0) {
                $b_slots = [];
                foreach ($bs as $bss) {
                    $b_slots[] = date('h:i A', strtotime($bss->appointment_time_slot));
                }
                $param['booked_slots'][$k]['time_slot'] = $b_slots;
            } else {
                $param['booked_slots'][$k]['time_slot'] = NULL;
            }
            $k++;
            $blocking = $this->db->select("*")->from("calendar_blocking")->where(" doctor_id='" . $doctor_id . "'")->get()->row();

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
        if (isset($parameters['patient_id'])) {
            $patient_id = $parameters['patient_id'];

            // get the list of open appointments i.e whose status is 'booked'
            $this->db->select('appointment_id, patient_id, doctor_id, appointment_date, appointment_time_slot');
            $this->db->from('appointments');
            $this->db->where('status=', 'booked');
            $this->db->where('doctor_id=', $doctor_id);
            $this->db->where('patient_id=', $patient_id);
            // $this->db->where('slot_type=', 'video call');
            $booked_list = $this->db->get()->result();

            if (count($booked_list) > 0) {
                $x = 0;
                foreach ($booked_list as $booked) {
                    $param['booked_appointments'][$x] = $booked;
                    $x++;
                }
            } else {
                $param['booked_appointments'] = $booked_list;
            }
        }

        $this->response(array('code' => '200', 'message' => 'Doctor Time Slots', 'result' => $param, 'requestname' => $method));
    }



    public function specialist_doctors_post()
    {
        extract($_POST);

        $specialist_doctors = $this->db->query("SELECT doctors.doctor_id,salutation,first_name,last_name,experience,qualification,Languages,profile_image,department_name,doctors.gender,clinic_doctor.online_consulting_fee FROM doctors,department,clinic_doctor WHERE doctors.department_id=department.department_id AND department.department_id='" . $department_id . "' AND clinic_doctor.doctor_id=doctors.doctor_id group by doctors.doctor_id order by doctors.experience desc")->result();
        // echo $this->db->last_query();

        $i = 0;

        if (count($specialist_doctors) > 0) {
            foreach ($specialist_doctors as $list) {

                if (count($list->online_consulting_fee) > 0 and $list->online_consulting_fee != 0) {

                    $result[$i]['doctor_id'] = $list->doctor_id;
                    $result[$i]['name'] = $list->salutation . " " . $list->first_name . " " . $list->last_name;
                    $result[$i]['experience'] = $list->experience;
                    $result[$i]['qualification'] = $list->qualification;
                    $result[$i]['languages'] = $this->languagetrans($list->doctor_id);
                    $result[$i]['profile_image'] = base_url('uploads/doctors/' . $list->profile_image);
                    $result[$i]['department_name'] = $list->department_name;
                    $result[$i]['gender'] = $list->gender;
                    $result[$i]['online_consulting_fee'] = $list->online_consulting_fee;
                    $i++;
                }
            }
        } else {
            $result = [];
        }

        $this->response(array('code' => '200', 'message' => 'specialist_doctors_list', 'result' => $result));
    }

    public function department_wise_search_post()
    {
        extract($_POST);

        if ($order_by == "price_desc") {
            $order_by = 'ORDER BY clinic_doctor.online_consulting_fee DESC';
        }
        if ($order_by == "price_asc") {
            $order_by = 'ORDER BY clinic_doctor.online_consulting_fee ASC';
        }
        if ($order_by == "experience_desc") {
            $order_by = 'ORDER BY doctors.experience DESC';
        }
        if ($order_by == "experience_asc") {
            $order_by = 'ORDER BY doctors.experience ASC';
        }

        $search_result = $this->db->query("SELECT doctors.doctor_id,doctors.first_name,doctors.last_name,department.department_id,doctors.experience,doctors.qualification,doctors.profile_image,doctors.address,doctors.gender,doctors.languages,department.department_name,clinic_doctor.online_consulting_fee FROM doctors INNER JOIN department ON doctors.department_id=department.department_id INNER JOIN clinic_doctor ON clinic_doctor.doctor_id=doctors.doctor_id WHERE doctors.department_id='" . $department_id . "' AND (doctors.first_name LIKE '%" . $search . "%' OR doctors.last_name LIKE '%" . $search . "%') GROUP BY doctors.doctor_id "  . $order_by)->result();

        if (!empty($search_result)) {

            $i = 0;

            foreach ($search_result as $doctor_info) {
                $result[$i]['doctor_id'] = $doctor_info->doctor_id;
                $result[$i]['name'] = $doctor_info->salutation . " " . $doctor_info->first_name . " " . $doctor_info->last_name;
                $result[$i]['experience'] = $doctor_info->experience;
                $result[$i]['qualification'] = $doctor_info->qualification;
                $result[$i]['languages'] = $doctor_info->languages;
                $result[$i]['profile_image'] = base_url('uploads/doctors/' . $doctor_info->profile_image);
                $result[$i]['department_name'] = $doctor_info->department_name;
                $result[$i]['gender'] = $doctor_info->gender;
                $result[$i]['online_consulting_fee'] = $doctor_info->online_consulting_fee;
                $result[$i]['address'] = $doctor_info->address;

                $i++;
            }

            $this->response(array('code' => '200', 'message' => 'doctor search result', 'result' => $result));
        } else {
            $this->response(array('code' => '201', 'message' => 'there is no doctos'));
        }
    }



    public function patient_creation_post()
    {
        extract($_POST);

        $patient_mobile = DataCrypt($mobile, 'encrypt');

        $already_exists = $this->db->query("select * from patients where mobile='" . $patient_mobile . "'")->result();

        if (count($already_exists) == 0) {

            $for_umr = $this->db->query("SELECT patient_id FROM patients ORDER BY `patients`.`patient_id` DESC")->row();

            $i = $for_umr->patient_id;
            $i += 1;
            $umr_no = 'P' . date('my') . $i;

            $photo = $_FILES['profile_pic']['name'];

            if (!empty($photo)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION);
                $photo_name = "photo-" . $for_umr->patient_id . "-" . $umr_no . "." . $filetype;
                $config['upload_path'] = './uploads/patients/';
                $config['file_name'] = $photo_name;
                $config['allowed_types'] = 'jpg|JPG|png|PNG|csv|jpeg|webp|WEBP';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['jpg', 'JPG', 'png', 'PNG', 'jpeg', 'csv', 'JPEG', 'webp', 'WEBP'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('profile_pic');
                    $fname = $this->upload->data();
                    $param = "File Uploaded";
                }
            }

            // adhar card photo upload
            $aadhar_card = $_FILES['aadhar_card']['name'];

            if (!empty($aadhar_card)) {
                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["aadhar_card"]["name"], PATHINFO_EXTENSION);
                $adhar_card_name = "photo-aadhar" . $for_umr->patient_id . "-" . $umr_no . "." . $filetype;
                $config['upload_path'] = './uploads/patients/';
                $config['file_name'] = $adhar_card_name;
                $config['allowed_types'] = 'jpg|JPG|png|PNG|csv|jpeg|webp|WEBP';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['jpg', 'JPG', 'png', 'PNG', 'jpeg', 'csv', 'JPEG', 'webp', 'WEBP'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["aadhar_card"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('aadhar_card');
                    $fname = $this->upload->data();
                    $param = "File Uploaded";
                }
            }

            // aadhar card photo upload completed

            $tempDir = './uploads/qrcodes/patients/';
            $codeContents = $umr_no;
            $qrname = $umr_no . md5($codeContents) . '.png';
            $pngAbsoluteFilePath = $tempDir . $qrname;
            $urlRelativeFilePath = base_url() . 'uploads/qrcodes/patients/' . $qrname;

            if (!file_exists($pngAbsoluteFilePath)) {
                QRcode::png($codeContents, $pngAbsoluteFilePath);
            }

            $data['first_name'] = $first_name;
            $data['last_name'] = $last_name;
            $data['umr_no'] = $umr_no;
            $data['mobile'] = DataCrypt($mobile, 'encrypt');
            $data['qrcode'] = $qrname;
            $data['age'] = $age;
            $data['location'] = $location;
            $data['aadhar_card_number'] = $aadhar_card_number;
            $data['aadhar_card_photo'] = $adhar_card_name;
            $data['photo'] = $photo_name;
            $data['created_by'] = $rmp_id;
            $data['gender'] = $gender;
            $data['created_date_time'] = date('Y-m-d H:i:s');
            $data['modified_by'] = $rmp_id;
            $data['modified_date_time'] = date('Y-m-d H:i:s');
            // $data['aadhar_card_photo']=$adhar_card_photo;

            $patient_id = $this->Generic_model->insertDataReturnId("patients", $data);

            $fruit = $this->db->query("select * from patients where patient_id='" . $patient_id . "'")->row();

            $patients_details['patient_id'] = $fruit->patient_id;
            $patients_details['patient_name'] = $fruit->first_name . " " . $fruit->last_name;
            $patients_details['age'] = $fruit->age;
            $patients_details['mobile'] = ($fruit->mobile != "") ? DataCrypt($fruit->mobile, 'decrypt') : DataCrypt($fruit->alternate_mobile, 'decrypt');
            $patients_details['gender'] = $fruit->gender;
            $patients_details['umr_no'] = $fruit->umr_no;

            $this->response(array('code' => '200', 'message' => 'patient successfully created', 'result' => $patients_details));
        } else {
            $this->response(array('code' => '201', 'message' => 'patient details already exists', 'result' => ''));
        }
    }


    public function checkExists_post()
    {
        extract($_POST);
        $mobile = Datacrypt($patient_mobile, 'encrypt');
        $check = $this->db->query("select * from patients where mobile='" . $mobile . "' or alternate_mobile='" . $mobile . "'")->result();
        if (count($check) > 0) {
            $i = 0;
            foreach ($check as $value) {
                $data['patients'][$i]['patient_id'] = $value->patient_id;
                $data['patients'][$i]['patient_name'] = $value->salutation . "" . $value->first_name . "" . $value->last_name;
                $data['patients'][$i]['age'] = $value->age;
                $data['patients'][$i]['mobile'] = ($value->mobile != "") ? DataCrypt($value->mobile, 'decrypt') : DataCrypt($value->alternate_mobile, 'decrypt');
                $data['patients'][$i]['dob'] = $value->dob;
                $data['patients'][$i]['gender'] = $value->gender;
                $data['patients'][$i]['umr_no'] = $value->umr_no;
                $data['patients'][$i]['location'] = $value->location;
                $i++;
            }
        } else {
            $data['patients'] = [];
        }
        $this->response(array('code' => '200', 'message' => 'success', 'result' => $data));
    }


    public function getHashToken_get($order_id, $amount)
    {
        // extract($parameters);
        $paytmParams = array();

        $paytmParams["body"] = array(
            "requestType"   => "Payment",
            // "mid"           => "UuWuPW69290928887235", #development mid
            "mid"           => "nCOgOt45031947680542", #production mid
            "websiteName"   => "WEBSTAGING",
            "orderId"       => $order_id,
            // "callbackUrl"   => "https://securegw-stage.paytm.in/theia/paytmCallback?ORDER_ID=$order_id",        # "https://doctor.umdaa.co"

            "callbackUrl"   =>  "https://securegw.paytm.in/theia/paytmCallback?ORDER_ID=$order_id",
            // "callbackUrl"   => 'https://securegw-stage.paytm.in/theia/paytmCallback?ORDER_ID=' . $order_id,
            "txnAmount"     => array(
                "value"     => $amount,
                "currency"  => "INR",
            ),
            "userInfo"      => array(
                "custId"    => "CUST_001",
            ),
        );

        /*
    * Generate checksum by parameters we have in body
    * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
    */
        //dev
        // $checksum = $this->PayModel->generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), "75MLC!_y%E#gCPAs"); #develpoment key 75MLC!_y%E#gCPAs
        //pro
        $checksum = $this->PayModel->generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), "vpnFB25M5balcys@");

        $paytmParams["head"] = array(
            "signature"    => $checksum
        );

        $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

        /* for Staging */
        //dev
        // $url = "https://securegw-stage.paytm.in/theia/api/v1/initiateTransaction?mid=UuWuPW69290928887235&orderId=$order_id";

        //pro
        $url = "https://securegw.paytm.in/theia/api/v1/initiateTransaction?mid=nCOgOt45031947680542&orderId=$order_id";

        /* for Production */
        // $url = "https://securegw.paytm.in/theia/api/v1/initiateTransaction?mid=YOUR_MID_HERE&orderId=ORDERID_98765";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        $response = curl_exec($ch);
        // print_r($response);
        $token = json_decode($response, true);
        // $token['sriraj'] = $number;
        $this->response(array('code' => '200', 'message' => 'Success', 'result' => $token));
        // $this->response(array('code'=>'200','message'=>'Doctors Data','result'=>$amount,'requestname'=>$method));
    }

    public function paymentCharges_post()
    {
        $paraa['payment_charges']['internetHandlingFees'] = '8.26';
        $paraa['payment_charges']['bookingFees'] = '7';
        $paraa['payment_charges']['serviceTax'] = '18';
        $paraa['payment_charges']['paymentGatewayCharges'] = '2.4';

        $this->response(array('code' => '200', 'message' => 'success ', 'result' => $paraa,));
    }


    public function book_appointment_post()
    { 
        extract($_POST);

            
            $Get_clinic_id = $this->db->query("SELECT clinic_id FROM clinic_doctor WHERE doctor_id='" . $doctor_id . "' AND primary_clinic=1")->row();

            if (count($Get_clinic_id) >  0) {
                $clinic_id = $Get_clinic_id->clinic_id;
            } else {
                $clinic_data = $this->db->query("SELECT * FROM clinic_doctor WHERE doctor_id='" . $doctor_id . "'")->result();
                $clinic_id = $clinic_data[0]->clinic_id;
            }

        $data['clinic_id'] = $clinic_id;
        $data['booking_type'] = 'video call';
        $data['slot_type'] = 'video call';
        $data['status'] = 'waiting';
        // $data['rmp_id'] = $rmp_id;
        $data['umr_no'] = $umr_no;
        $data['appointment_date'] =  date_format(date_create($appointment_date), "Y-m-d");
        $data['appointment_time_slot'] = $appointment_time_slot;
        $data['doctor_id'] = $doctor_id;
        $data['rmp_id'] =  $rmp_id;
        $data['patient_id'] = $patient_id;
        $data['appointment_type'] = $appointment_type;
     

        $appointment_id = $this->Generic_model->insertDataReturnId("appointments", $data);

        $rmp_detail = $this->db->query("SELECT * FROM rural_rmp_registration where rmp_id='" . $rmp_id . "'")->row();

        $total_amount['wallet_cash'] = $rmp_detail->wallet_cash - $amount;

        $this->Generic_model->updateData('rural_rmp_registration', $total_amount, array('rmp_id' => $rmp_id));


        $bill['doctor_id'] = $doctor_id;
        $bill['appointment_id'] = $appointment_id;
        $bill['patient_id'] = $patient_id;
        $bill['umr_no'] = $umr_no;
        $bill['billing_type'] = 'Registration & Consultation';
        $bill['payment_mode'] = 'online';
        $bill['payment_status'] = 1;
        $bill['total_amount'] = $amount;
        // $bill['transaction_id'] = $transaction_id;
        $bill['created_date_time'] = date('Y-m-d H:i:s');
        $bill['modified_date_time'] = date('Y-m-d H:i:s');
        $bill['rmp_id'] = $rmp_id;
        $this->Generic_model->insertDataReturnId("billing", $bill);

        $this->response(array('code' => '200', 'message' => 'your appointment successfully created'));
    }

    function translate_post()
    {
        $lan = ["telugu", "hindi", "urdu"];

        $lang =  translate("urdu", "ur");

        echo  $lang;
    }

    public function languagetrans($doctor_id)
    {
        $langzzz = $this->db->query("SELECT languages as lang FROM `doctors` WHERE doctor_id='" . $doctor_id . "'")->row();

        $str = $langzzz->lang;

        $ary = explode(",", $str);

        $langary = '';
        for ($i = 0; $i < count($ary); $i++) {

            $value = $this->db->query("SELECT local_language FROM `Languages` where Language='" . $ary[$i] . "'")->row();

            $langary .= $value->local_language . ',';
        }
        return substr($langary, 0, -1);
    }


    public function todoList_post()
    {
        extract($_POST);
        // $todo_list = $this->db->query("SELECT * FROM `appointments` WHERE rmp_id='" . $rmp_id . "' GROUP BY patient_id")->result();
        $todo_list = $this->db->query("SELECT doc.doctor_id,doc.salutation,doc.first_name AS doc_first_name,doc.last_name AS doc_last_name,pat.patient_id,pat.age_unit,pat.photo,pat.first_name as pat_first_name,pat.last_name as pat_last_name,pat.umr_no,pat.photo,pat.age,pat.gender,pat.date_of_birth,app.appointment_type,app.appointment_date,app.appointment_time_slot,app.status,app.appointment_id FROM appointments app INNER JOIN patients pat ON app.patient_id=pat.patient_id INNER JOIN doctors doc ON app.doctor_id=doc.doctor_id AND app.rmp_id='" . $rmp_id . "' GROUP BY app.appointment_id order by app.created_date_time desc")->result();

        if (count($todo_list) > 0) {
            $i = 0;
            foreach ($todo_list as $todo) {

                $data[$i]['doctor_id'] = $todo->doctor_id;
                $data[$i]['doctor_name'] = $todo->salutation . " " . $todo->doc_first_name . " " . $todo->doc_last_name;
                $data[$i]['patient_id'] = $todo->patient_id;
                $data[$i]['patient_name'] = $todo->pat_first_name . " " . $todo->pat_last_name;
                $data[$i]['umr_no'] = $todo->umr_no;
                $data[$i]['photo'] = base_url() . "./uploads/rural/patient_profile_pic/" . $todo->photo;
                $data[$i]['age'] = $todo->age;
                $data[$i]['age_unit'] = $todo->age_unit;
                $data[$i]['profile_pic'] = $todo->photo;
                $data[$i]['status'] = $todo->status;
                $data[$i]['gender'] = $todo->gender;
                $data[$i]['appointment_id'] = $todo->appointment_id;
                $data[$i]['appointment_type'] = $todo->appointment_type;
                $data[$i]['appointment_date'] = $todo->appointment_date;
                $data[$i]['appointment_time_slot'] = $todo->appointment_time_slot;
                $i++;
            }

            $this->response(array('code' => '200', 'message' => 'todolist', 'result' => $data));
        } else {

            $this->response(array('code' => '200', 'message' => 'todolist', 'result' => "your todo list is empty"));
        }
    }



    // public function paymentStatus()
    // {
    //     $data['postdata'] = $_POST;
    //     extract($_POST);
    //     if ($STATUS == "TXN_SUCCESS") {
    //         $eoHistoryInfo = $this->Generic_model->getSingleRecord('eo_wallet_history', array('transaction_id' => $ORDERID));
    //         $doctorWallet = $this->Generic_model->getSingleRecord('doctor_wallet_prices', array('doctor_id' => $eoHistoryInfo->doctor_id));
    //         if (count($doctorWallet) > 0) {
    //             $data1['amount'] = $TXNAMOUNT + $doctorWallet->amount;
    //             $this->Generic_model->updateData("doctor_wallet_prices", $data1, array('doctor_wallet_id' => $doctorWallet->doctor_wallet_id));
    //         } else {
    //             $data1['doctor_id'] = $eoHistoryInfo->doctor_id;
    //             $data1['created_by'] = $eoHistoryInfo->doctor_id;
    //             $data1['created_date_time'] = date("Y-m-d H:i:s");
    //             $data1['amount'] = $TXNAMOUNT + $doctorWallet->amount;
    //             $this->Generic_model->insertData("doctor_wallet_prices", $data1);
    //         }
    //         $updateData['payment_status'] = 1;
    //         $this->Generic_model->updateData('eo_wallet_history', $updateData, array('transaction_id' => $ORDERID));
    //         $this->Generic_model->angularNotifications('', '', $eoHistoryInfo->doctor_id, '', 'EO_Wallet_Money_Added', '');
    //     } elseif ($STATUS == "TXN_FAILURE") {
    //         $updateData['payment_status'] = 2;
    //         $this->Generic_model->updateData('eo_wallet_history', $updateData, array('transaction_id' => $ORDERID));
    //     }
    //     $this->load->view("onlinepayment/status", $data);
    // }



    public function shortSummary($appointment_id)
    {

        // extract($_POST);

        $appInfo = $this->Generic_model->getSingleRecord('appointments', array('appointment_id' => $appointment_id));

        if (count($appInfo) > 0) {

            $data['clinicsInfo'] = clinicDetails($appInfo->clinic_id);
            $data['docInfo'] = doctorDetails($appInfo->doctor_id);
            $data['patientInfo'] = getPatientDetails($appInfo->patient_id);
            $data['appointments'] = $appInfo;
            $data['symptoms'] = $this->Generic_model->getJoinRecords('patient_presenting_symptoms pps', 'patient_ps_line_items ppsl', 'pps.patient_presenting_symptoms_id=ppsl.patient_presenting_symptoms_id', array('pps.appointment_id' => $appointment_id), '', '*');

            $data['pdfSettings'] = $this->Generic_model->getSingleRecord('clinic_pdf_settings', array('clinic_id' => $appInfo->clinic_id));

            $data['notes'] = $this->Generic_model->getJoinRecords('patient_notes pn', 'patient_notes_line_items pnl', 'pn.patient_notes_id=pnl.patient_notes_id', array('appointment_id' => $appointment_id), '', '*');

            $data['prescriptionsInfo'] = $this->Generic_model->getSingleRecord('patient_prescription', array('appointment_id' => $appointment_id));

            $data['clinicalDiagnosis'] = $this->Generic_model->getJoinRecords('patient_clinical_diagnosis pcd', 'patient_cd_line_items pcdl', 'pcd.patient_clinical_diagnosis_id=pcdl.patient_clinical_diagnosis_id', array('appointment_id' => $appointment_id, 'pcdl.expert_opinion_id' => '0'), '', '*');
            $data['cdInfo'] = $this->Generic_model->getSingleRecord('patient_clinical_diagnosis', array('appointment_id' => $appointment_id));
            $data['investigations'] = $this->Generic_model->getJoinRecords('patient_investigation pin', 'patient_investigation_line_items pinl', 'pin.patient_investigation_id=pinl.patient_investigation_id', array('appointment_id' => $appointment_id, 'pinl.expert_opinion_id' => '0'), '', '*');
            $data['prescriptions'] = $this->Generic_model->getJoinRecords('patient_prescription pp', 'patient_prescription_drug ppd', 'pp.patient_prescription_id=ppd.patient_prescription_id', array('appointment_id' => $appointment_id, 'ppd.expert_opinion_id' => '0'), '', '*');

            $data['get_past_history_info'] = $this->Generic_model->getJoinRecords('patient_form pf', 'patient_form_line_items pfl', 'pf.patient_form_id=pfl.patient_form_id', array('appointment_id' => $appointment_id, 'form_type' => 'Past History'), '', '*');
            $data['gpe_info'] = $this->Generic_model->getJoinRecords('patient_form pf', 'patient_form_line_items pfl', 'pf.patient_form_id=pfl.patient_form_id', array('appointment_id' => $appointment_id, 'form_type' => 'GPE'), '', '*');
            $data['se_info'] = $this->Generic_model->getJoinRecords('patient_form pf', 'patient_form_line_items pfl', 'pf.patient_form_id=pfl.patient_form_id', array('appointment_id' => $appointment_id, 'form_type' => 'Systemic Examination'), '', '*');

            $data['pastHistory'] = $this->db->query("select * from patient_form pf,patient_form_line_items pfl where pf.patient_form_id=pfl.patient_form_id and pf.appointment_id='" . $appointment_id . "' and pfl.section_text!='' and pf.form_type='Past History'")->result();
            $data['symptoms'] = $this->db->query("select * from patient_presenting_symptoms pps,patient_ps_line_items ppls where pps.patient_presenting_symptoms_id=ppls.patient_presenting_symptoms_id and pps.appointment_id='" . $appointment_id . "'")->result();

            $section_image = $this->db->query("select patient_form_id from patient_form where appointment_id='" . $appointment_id . "'")->result();

            $i = 0;
            foreach ($section_image as $image) {
                $abc['investigation'][$i]['patient_form_id'] = $image->patient_form_id;
                $inv_lineitems = $this->db->select("*")->from("patient_form_scribbling_images pil")
                    ->where("pil.patient_form_id='" . $image->patient_form_id . "'")
                    ->get()
                    ->result();
                $j = 0;
                foreach ($inv_lineitems as $inv_lineitem) {
                    $abcd['scribbling'][0]['images'] = $inv_lineitem->scribbling_image;
                    $j++;
                }
                $i++;
            }

            $this->load->library('M_pdf');
            $html = $this->load->view('reports/short_prescription_summary', $data, true);
            $pdfFilePath = $appointment_id . time() . ".pdf";
            $stylesheet  = '';
            $stylesheet .= file_get_contents(base_url() . "assets/css/bootstrap.min.css"); // external css
            $stylesheet .= file_get_contents(base_url() . "assets/css/pdf.css");
            // $stylesheet .= file_get_contents(base_url()."assets/css/print.css");
            $this->m_pdf->pdf->autoScriptToLang = true;
            $this->m_pdf->pdf->autoLangToFont = true;

            $this->m_pdf->pdf->shrink_tables_to_fit = 1;
            $this->m_pdf->pdf->setAutoTopMargin = "stretch";
            $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
            $this->m_pdf->pdf->defaultheaderline = 0;

            // $this->m_pdf->pdf->SetFont("poppins");
            $this->m_pdf->pdf->WriteHTML($stylesheet, 1);
            // $this->m_pdf->pdf->DefHTMLFooterByName('Chapter2Footer','<div style="text-align: right; font-weight: bold; font-size: 8pt; font-style: italic;">Chapter 2 Footer</div>');
            $this->m_pdf->pdf->WriteHTML($html, 2);
            $this->m_pdf->pdf->Output("./uploads/summary_reports/short-" . $pdfFilePath, "F");
            $para['pdf_name'] = base_url() . 'uploads/summary_reports/short-' . $pdfFilePath;

            // $this->response(array('code' => '200', 'message' => 'PDF File', 'result' => $para,));
        }

        return  $para['pdf_name'];
    }

    //



    public function fullSummary_post()
    {

        extract($_POST);

        $appInfo = $this->Generic_model->getSingleRecord('appointments', array('appointment_id' => $appointment_id));

        if (count($appInfo) > 0) {

            $data['clinicsInfo'] = clinicDetails($appInfo->clinic_id);
            $data['docInfo'] = doctorDetails($appInfo->doctor_id);
            $data['patientInfo'] = getPatientDetails($appInfo->patient_id);
            $data['appointments'] = $appInfo;

            $data['symptoms'] = $this->Generic_model->getJoinRecords('patient_presenting_symptoms pps', 'patient_ps_line_items ppsl', 'pps.patient_presenting_symptoms_id=ppsl.patient_presenting_symptoms_id', array('pps.appointment_id' => $appointment_id), '', '*');
            // echo $this->db->last_query();
            $data['clinicalDiagnosis'] = $this->Generic_model->getJoinRecords('patient_clinical_diagnosis pcd', 'patient_cd_line_items pcdl', 'pcd.patient_clinical_diagnosis_id=pcdl.patient_clinical_diagnosis_id', array('appointment_id' => $appointment_id, 'pcdl.expert_opinion_id' => '0'), '', '*');
            $data['investigations'] = $this->Generic_model->getJoinRecords('patient_investigation pin', 'patient_investigation_line_items pinl', 'pin.patient_investigation_id=pinl.patient_investigation_id', array('appointment_id' => $appointment_id, 'pinl.expert_opinion_id' => '0'), '', '*');
            $data['prescriptions'] = $this->Generic_model->getJoinRecords('patient_prescription pp', 'patient_prescription_drug ppd', 'pp.patient_prescription_id=ppd.patient_prescription_id', array('appointment_id' => $appointment_id, 'ppd.expert_opinion_id' => '0'), '', '*');
            // echo $this->db->last_query();
            $data['prescriptionsInfo'] = $this->Generic_model->getSingleRecord('patient_prescription', array('appointment_id' => $appointment_id));

            $data['get_past_history_info'] = $this->Generic_model->getJoinRecords('patient_form pf', 'patient_form_line_items pfl', 'pf.patient_form_id=pfl.patient_form_id', array('appointment_id' => $appointment_id, 'form_type' => 'Past History'), '', '*');
            $data['get_personal_history_info'] = $this->Generic_model->getJoinRecords('patient_form pf', 'patient_form_line_items pfl', 'pf.patient_form_id=pfl.patient_form_id', array('appointment_id' => $appointment_id, 'form_type' => 'Personal History'), '', '*');
            $data['get_family_history_info'] = $this->Generic_model->getJoinRecords('patient_form pf', 'patient_form_line_items pfl', 'pf.patient_form_id=pfl.patient_form_id', array('appointment_id' => $appointment_id, 'form_type' => 'Family History'), '', '*');
            $data['get_social_history_info'] = $this->Generic_model->getJoinRecords('patient_form pf', 'patient_form_line_items pfl', 'pf.patient_form_id=pfl.patient_form_id', array('appointment_id' => $appointment_id, 'form_type' => 'Social History'), '', '*');
            $data['get_treatment_history_info'] = $this->Generic_model->getJoinRecords('patient_form pf', 'patient_form_line_items pfl', 'pf.patient_form_id=pfl.patient_form_id', array('appointment_id' => $appointment_id, 'form_type' => 'Treatment History'), '', '*');


            $data['get_hopi_info'] = $this->Generic_model->getAllRecords('patient_form', array('appointment_id' => $appointment_id, 'form_type' => 'HOPI'));
            $data['get_past_history'] = $this->Generic_model->getAllRecords('patient_form', array('appointment_id' => $appointment_id, 'form_type' => 'Past History'));
            $data['get_personal_history'] = $this->Generic_model->getAllRecords('patient_form', array('appointment_id' => $appointment_id, 'form_type' => 'Personal History'));
            $data['get_family_history'] = $this->Generic_model->getAllRecords('patient_form', array('appointment_id' => $appointment_id, 'form_type' => 'Family History'));
            $data['get_social_history'] = $this->Generic_model->getAllRecords('patient_form', array('appointment_id' => $appointment_id, 'form_type' => 'Social History'));
            $data['get_treatment_history'] = $this->Generic_model->getAllRecords('patient_form', array('appointment_id' => $appointment_id, 'form_type' => 'Treatment History'));
            $data['getgpe_info'] = $this->Generic_model->getAllRecords('patient_form', array('appointment_id' => $appointment_id, 'form_type' => 'GPE'));
            $data['getse_info'] = $this->Generic_model->getAllRecords('patient_form', array('appointment_id' => $appointment_id, 'form_type' => 'Systemic Examination'));
            $data['getos_info'] = $this->Generic_model->getAllRecords('patient_form', array('appointment_id' => $appointment_id, 'form_type' => 'Other Systems'));

            $data['gpe_info'] = $this->Generic_model->getJoinRecords('patient_form pf', 'patient_form_line_items pfl', 'pf.patient_form_id=pfl.patient_form_id', array('appointment_id' => $appointment_id, 'form_type' => 'GPE'), '', '*');
            $data['se_info'] = $this->Generic_model->getJoinRecords('patient_form pf', 'patient_form_line_items pfl', 'pf.patient_form_id=pfl.patient_form_id', array('appointment_id' => $appointment_id, 'form_type' => 'Systemic Examination'), '', '*');

            $data['presenting_symptoms'] = $this->Generic_model->getJoinRecords('patient_presenting_symptoms ps', 'patient_ps_line_items pps', 'ps.patient_presenting_symptoms_id=pps.patient_presenting_symptoms_id', array('appointment_id' => $appointment_id), '', '*');
            $data['patient_consent_form'] = $this->Generic_model->getJoinRecords('patient_consent_forms pcf', 'consent_form cf', 'pcf.consent_form_id=cf.consent_form_id', array('appointment_id' => $appointment_id), '', '*');

            $data['patient_procedures'] = $this->Generic_model->getJoinRecords('patient_procedure pp', 'medical_procedures mp', 'pp.medical_procedure_id=mp.medical_procedure_id', array('appointment_id' => $appointment_id), '', 'pp.*,mp.medical_procedure as procedure_title');

            $data['previous_documents'] = $this->Generic_model->getAllRecords('previous_documents', array('appointment_id' => $appointment_id));

            // web_consent_forms
            $data['web_consent_forms'] = $this->db->select("*")->from("webpatients_consent_form")->where("appointment_id='" . $appointment_id . "'")->get()->result();

            // More_consent_forms
            $data['more_forms'] = $this->db->select("*")->from("patient_form_list")->where("appointment_id='" . $appointment_id . "'")->get()->result();


            $section_image = $this->db->query("select patient_form_id from patient_form where appointment_id='" . $appointment_id . "'")->result();

            $i = 0;
            foreach ($section_image as $image) {
                $abc['investigation'][$i]['patient_form_id'] = $image->patient_form_id;
                $inv_lineitems = $this->db->select("*")->from("patient_form_scribbling_images pil")
                    ->where("pil.patient_form_id='" . $image->patient_form_id . "'")
                    ->get()
                    ->result();
                $j = 0;
                foreach ($inv_lineitems as $inv_lineitem) {
                    $abcd['scribbling'][0]['images'] = $inv_lineitem->scribbling_image;
                    $j++;
                }
                $i++;
            }

            // echo "<pre>";
            // print_r($data);
            // echo "</pre>";
            // exit;
            $this->load->library('M_pdf');
            $html = $this->load->view('reports/fullSummary', $data, true);
            $pdfFilePath = $appointment_id . time() . ".pdf";
            $stylesheet  = '';
            $stylesheet .= file_get_contents(base_url() . "assets/css/bootstrap.min.css"); // external css
            $stylesheet .= file_get_contents(base_url() . "assets/css/pdf.css");
            // $stylesheet .= file_get_contents(base_url()."assets/css/print.css");
            $this->m_pdf->pdf->autoScriptToLang = true;
            $this->m_pdf->pdf->autoLangToFont = true;

            $this->m_pdf->pdf->shrink_tables_to_fit = 1;
            $this->m_pdf->pdf->setAutoTopMargin = "stretch";
            $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
            $this->m_pdf->pdf->defaultheaderline = 0;

            // $this->m_pdf->pdf->SetFont("poppins");
            $this->m_pdf->pdf->WriteHTML($stylesheet, 1);
            // $this->m_pdf->pdf->DefHTMLFooterByName('Chapter2Footer','<div style="text-align: right; font-weight: bold; font-size: 8pt; font-style: italic;">Chapter 2 Footer</div>');
            $this->m_pdf->pdf->WriteHTML($html, 2);
            $this->m_pdf->pdf->Output("./uploads/summary_reports/full-" . $pdfFilePath, "F");

            $para = base_url() . 'uploads/summary_reports/full-' . $pdfFilePath;

            $this->response(array('code' => '200', 'message' => 'PDF File', 'result' => $para,));
        }
    }




    public function old_walletstatus_post()
    {

        extract($_POST);

        $cash = $this->db->query("SELECT wallet_cash FROM rural_rmp_registration WHERE rmp_id='" . $rmp_id . "'")->row();

        $data['current_wallet_amount'] = $cash->wallet_cash;

        $request = $this->db->query("SELECT * FROM cash_request WHERE rmp_id='" . $rmp_id . "' ORDER BY cash_request.cash_request_id DESC LIMIT 1")->row();

        $status = $request->request_status;

        if ($status == "pending") {

            $data['request_amount'] = $request->request_amount;
            $data['request_status'] = $request->request_status;
            $data['request_date'] = $request->created_date;

            $this->response(array('code' => '200', 'response' => $data));
        } else {

            $this->response(array('code' => '202', 'message' => 'there is no pending add cash request', 'response' => $data));
        }
    }



    public function walletstatus_post()
    {
        extract($_POST);

        $cash = $this->db->query("SELECT wallet_cash FROM rural_rmp_registration WHERE rmp_id='" . $rmp_id . "'")->row();

        $this->response(array('code' => '200', 'response' => $cash));
    }

    public function editRmpProfile_post()
    {
        extract($_POST);
        $date['rmp_name'] = $rmp_name;
        $date['rmp_gender'] = $rmp_gender;
        $date['rmp_age'] = $rmp_age;
        $date['rmp_email'] = $rmp_email;
        // $date['phonenumber'] = $phonenumber;
        $date['rmp_clinic_name'] = $clinic_name;
        $date['rmp_city'] = $clinic_location;
        $date['profile_pic'] = $profile_pic;

        $this->Generic_model->updateData('rural_rmp_registration', $date, array('rmp_id' => $rmp_id));

        $this->response(array('code' => '200', 'message' => 'profile successfully updated'));
    }



    public function transactionHistory_post()
    {
        extract($_POST);

        $paytm_credited_history = $this->db->query("SELECT rmp_payment_id as id,Transaction_ID as Transaction_id,amount as amount,Transaction_time as created_date,'credited' AS transaction_type,'' as doc_name,'' as patient_name  FROM payment_history WHERE rmp_id='" . $rmp_id . "'")->result_array();
        $wallet_debited_history = $this->db->query("SELECT b.billing_id as id,b.invoice_no as Transaction_id,b.total_amount as amount,b.created_date_time as created_date,'debited' AS transaction_type,CONCAT(d.first_name,' ',d.last_name) as doc_name,CONCAT(p.first_name,' ',p.last_name) AS patient_name FROM billing b INNER JOIN doctors d ON d.doctor_id=b.doctor_id INNER JOIN patients p ON p.patient_id=b.patient_id WHERE b.rmp_id='" . $rmp_id . "'")->result_array();

        // print_r($paytm_credited_history);
        // print_r($wallet_debited_history);
        $output = array_merge($paytm_credited_history, $wallet_debited_history);

        function cmp($a, $b)
        {
            if ($a["created_date"] == $b["created_date"]) {
                return 0;
            }
            return ($a["created_date"] < $b["created_date"]) ? -1 : 1;
        }

        usort($output, "cmp");
        $i = array_reverse($output);
        // print_r($i);
        // $history = $this->db->query("SELECT * FROM payment_history where rmp_id='" . $rmp_id . "'")->result();
        $this->response(array('code' => '200', 'response' => $i));
    }

    public function addPayment_post()
    {
        extract($_POST);

        $data['rmp_id'] = $rmp_id;
        $data['mode_of_payment'] = $mode_of_payment;
        $data['amount'] = $amount;
        $data['Transaction_ID'] = $Transaction_ID;
        $data['Order_id'] = $order_id;
        $data['Transaction_time'] = $Transaction_time;

        $rmp_payment_id = $this->Generic_model->insertDataReturnId("payment_history", $data);

        $rmp_detail = $this->db->query("SELECT * FROM rural_rmp_registration where rmp_id='" . $rmp_id . "'")->row();

        $total_amount['wallet_cash'] = $rmp_detail->wallet_cash + $amount;

        $this->Generic_model->updateData('rural_rmp_registration', $total_amount, array('rmp_id' => $rmp_id));

        $this->response(array('code' => '200', 'message' => "your amount was successfully added"));
    }


    // reports related apis

    public function reportsget_post()
    {

        extract($_POST);
        if ($patient_id != "" and $rmp_id != "") {

            $value = $this->db->query("select * from patients_reports where patient_id='" . $patient_id . "' and rmp_id='" . $rmp_id . "'")->result();

            $this->response(array('code' => '200', 'patient_reports' => $value));
        } else {
            $this->response(array('code' => '404', 'message' => 'enter rmp_id and patient_id'));
        }
    }

    // this api is used to upload the pdf documents

    public function reportslineitempdf_post()
    {
        $tabledate = date("d-m-Y: h:i a");
        extract($_POST);

        if ($rmp_id != "" and $patient_id != "" and $reports_id != "") {

            $report_image = $_FILES['report_pdf']['name'];
            // $exa_audio="";

            if (!empty($report_image)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["report_pdf"]["name"], PATHINFO_EXTENSION);
                $file_name = "reports-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/patient_reports/';
                $config['file_name'] = $file_name;
                $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG|pdf|PDF';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['jpg', 'png', 'jpeg', 'JPG', 'PNG', 'JPEG', 'pdf', 'PDF'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {

                    $param['type'] = pathinfo($_FILES["report_pdf"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('report_pdf');
                    $fname = $this->upload->data();
                    // $right_infrascapular_audio=$file_name;
                    $param = "File Uploaded";
                }
            }

            $this->db->query("INSERT INTO patient_report_line_items (rmp_id,patient_id,reports_id,report_image,created_by,created_date) VALUES ('$rmp_id','$patient_id','$reports_id','$file_name','$rmp_id','$tabledate')");


            $this->response(array('code' => '200', 'message' => 'report pdf successfully uploaded'));
        } else {

            $this->response(array('code' => '404', 'message' => 'enter rmp_id and patient_id and report_id'));
        }
    }

    public function reportslineitem_post()
    {

        $tabledate = date("d-m-Y: h:i a");
        extract($_POST);

        if ($rmp_id != "" and $patient_id != "" and $reports_id != "") {

            $report_image = $_FILES['report_image']['name'];
            // $exa_audio="";

            if (!empty($report_image)) {

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload');
                $filetype = pathinfo($_FILES["report_image"]["name"], PATHINFO_EXTENSION);
                $file_name = "reports-" . $patient_id . "-" . $rmp_id . "-" . $date . "." . $filetype;
                $config['upload_path'] = './uploads/rural/patient_reports/';
                $config['file_name'] = $file_name;
                $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG|pdf|PDF';

                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

                $files = ['jpg', 'png', 'jpeg', 'JPG', 'PNG', 'JPEG', 'pdf', 'PDF'];
                $filename = $_FILES['file_i']['name'];

                // $dp="doctor_pro_pic.jpg";
                if (!in_array($filetype, $files)) {
                    $param['type'] = pathinfo($_FILES["report_image"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code' => '201', 'message' => 'Wrong File Type', 'result' => $param));
                } else {
                    $this->upload->initialize($config);
                    $this->upload->do_upload('report_image');
                    $fname = $this->upload->data();
                    // $right_infrascapular_audio=$file_name;
                    $param = "File Uploaded";
                }
            }

            $this->db->query("INSERT INTO patient_report_line_items (rmp_id,patient_id,reports_id,report_image,created_by,created_date) VALUES ('$rmp_id','$patient_id','$reports_id','$file_name','$rmp_id','$tabledate')");


            $this->response(array('code' => '200', 'message' => 'report image successfully uploaded'));
        } else {

            $this->response(array('code' => '404', 'message' => 'enter rmp_id and patient_id and report_id'));
        }
    }


    public function reportslineitemget_post()
    {

        extract($_POST);

        if ($patient_id != "" and $rmp_id != "" and $reports_id != "") {

            $value = $this->db->query("select * from patient_report_line_items where patient_id='" . $patient_id . "' and rmp_id='" . $rmp_id . "' and reports_id='" . $reports_id . "'")->result();

            if (!empty($value)) {

                $a = 0;
                foreach ($value as $val) {

                    $data['reportslineitem'][$a]['patient_report_line_item_id'] = $val->patient_report_line_item_id;
                    $data['reportslineitem'][$a]['reports_id'] = $val->reports_id;
                    $data['reportslineitem'][$a]['rmp_id'] = $val->rmp_id;
                    $data['reportslineitem'][$a]['patient_id'] = $val->patient_id;
                    $data['reportslineitem'][$a]['report_image'] = 'http://devumdaa.in/dev/uploads/rural/patient_reports/' . $val->report_image;
                    $data['reportslineitem'][$a]['modified_by'] = $val->modified_by;
                    $data['reportslineitem'][$a]['created_by'] = $val->created_by;
                    $data['reportslineitem'][$a]['created_date'] = $val->created_date;
                    $data['reportslineitem'][$a]['modified_date'] = $val->modified_date;

                    $a++;
                }

                $this->response(array('code' => '200', 'reports_line_items' => $data['reportslineitem']));
            } else {

                $this->response(array('code' => '404', 'message' => 'No records found'));
            }
        } else {

            $this->response(array('code' => '404', 'message' => 'enter rmp_id and patient_id and reports_id'));
        }
    }

    public function patientsReports_post()
    {
        extract($_POST);
        $date = date("Y-m-d H:i:s");
        $data['patient_id'] = $patient_id;
        $data['rmp_id'] = $rmp_id;
        $data['description'] = $description;
        $data['created_date'] = $date;
        $data['modified_date'] = $date;

        $patients_report_id = $this->Generic_model->insertDataReturnId("patients_reports", $data);

        $config['upload_path'] = './uploads/rural/patient_reports/';
        $config['allowed_types'] = 'jpg|JPG|png|PNG|JPEG|jpeg|pdf|PDF';
        $file_count = count($_FILES['file_i']['name']);

        for ($i = 0; $i < $file_count; $i++) {

            $this->load->library('upload');

            $_FILES['file_i[]']['name'] = $_FILES['file_i']['name'][$i];
            $_FILES['file_i[]']['type'] = $_FILES['file_i']['type'][$i];
            $_FILES['file_i[]']['tmp_name'] = $_FILES['file_i']['tmp_name'][$i];
            $_FILES['file_i[]']['error'] = $_FILES['file_i']['error'][$i];
            $_FILES['file_i[]']['size'] = $_FILES['file_i']['size'][$i];
            $this->upload->initialize($config);
            $this->upload->do_upload('file_i[]');
            $fname = $this->upload->data();
            $fileName[$i] = $fname['file_name'];

            $bata['rmp_id'] = $rmp_id;
            $bata['patient_id'] = $patient_id;
            $bata['report_image'] = $fname['file_name'];
            $bata['patients_report_id'] = $patients_report_id;
            $bata['created_date'] = $date;
            $bata['modified_date'] = $date;
            $id = $this->Generic_model->insertDataReturnId("patient_report_line_items", $bata);
        }

        $this->response(array('code' => '200', 'message' => 'Documents Submitted successfully'));
    }

    public function recent_consultation_post()
    {
        extract($_POST);

        $recent_doc = $this->db->query("SELECT doctor_id FROM appointments WHERE rmp_id='" . $rmp_id . "'  AND doctor_id > 0  GROUP BY appointments.doctor_id ORDER BY appointments.created_date_time DESC")->result();

        $i = 0;

        foreach ($recent_doc as $doctor_info) {

            $recent_doctors = $this->db->query("SELECT doctors.*,department.department_name,clinic_doctor.online_consulting_fee FROM doctors INNER JOIN clinic_doctor ON doctors.doctor_id=clinic_doctor.doctor_id inner join department on doctors.department_id=department.department_id WHERE doctors.doctor_id='" . $doctor_info->doctor_id . "'")->row();

            $result[$i]['doctor_id'] = $recent_doctors->doctor_id;
            $result[$i]['name'] = $recent_doctors->salutation . " " . $recent_doctors->first_name . " " . $recent_doctors->last_name;
            $result[$i]['experience'] = $recent_doctors->experience;
            $result[$i]['qualification'] = $recent_doctors->qualification;
            $result[$i]['languages'] = $recent_doctors->languages;
            $result[$i]['profile_image'] = base_url('uploads/doctors/' . $recent_doctors->profile_image);
            $result[$i]['department_name'] = $recent_doctors->department_name;
            $result[$i]['gender'] = $recent_doctors->gender;
            $result[$i]['online_consulting_fee'] = $recent_doctors->online_consulting_fee;
            $result[$i]['address'] = $recent_doctors->address;

            $i++;
        }

        $this->response(array('code' => '200', 'message' => 'recent consltations', 'result' => $result));
    }

    public function telecallPushNotifications_post()
    {
        extract($_POST);
        // $user_id=$parameters['user_id'];
        $mobile_number = $m_number;
        // $this->Generic_model->pushNotifications($mobile_number,'','','','TelecallNotification');

        $checkMobileNumber = $this->db->select("*")
            ->from("rural_rmp_registration")
            ->where("rmp_phone ='" . $mobile_number . "'")
            ->order_by('rmp_id', 'DESC')
            ->get()
            ->row();

        // echo  $this->db->last_query();
        if (count($checkMobileNumber) > 0) {
            $fcm_id = $checkMobileNumber->fcm_id;
            $this->pushNotificationsCitizens($mobile_number, 'TelecallNotification');
            $this->response(array('code' => '200', 'message' =>   $mobile_number, 'result' => 'Success', 'requestname' => $method));
        } else {
            $fcm_id = "No Number Registered";
            $this->response(array('code' => '201', 'message' =>   $mobile_number, 'result' => $fcm_id, 'requestname' => $method));
        }
    }

    public function pushNotificationsCitizens($mobile_number, $notification_type)
    {
        $type = "";
        // echo "Notification Type : ".$notification_type;
        if ($notification_type == "TelecallNotification") {
            $checkMobileNumber = $this->db->select("*")
                ->from("rural_rmp_registration")
                ->where("rmp_phone ='" . $mobile_number . "'")
                ->get()
                ->row();
            $user_fcm_id[] = $checkMobileNumber->fcm_id;
            $type = "Call Notification";
            $msg = $checkMobileNumber->rmp_phone;
        }

        $registrationIds = $user_fcm_id;


        $message = array(
            'body' => $msg,
            'title' => ' $clinic->clinic_name',
            'appointment_id' => '$appointment_id',
            'patient_id' => '$patient_id',
            'sound' => 1,
            'type' => $type,
            //    'largeIcon' => 'large_icon',
            //    'smallIcon' => 'small_icon',
            'screen' => '$page',
            'image' => 'base_url("uploads/clinic_logos/" . $clinic->clinic_logo)'
        );


        $fields = array(
            'registration_ids' => $registrationIds,
            'data' => $message
        );

        $headers = array(
            //    'Authorization: key=' . API_ACCESS_KEY,
            'Authorization: key=AAAABCNnhe4:APA91bHga4VrGtmZbD3m10qZVzdhtxuE8sW35X6OI8sYnSxtB0pdRTAJtAj6blz8EdLBxlZlillz4gV3iYn39bICdIszkf6HhEEAV24SJoAUSAFW_lYFpKQ4pHprRHHd_FF5bOx-0z0s',
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        //    echo json_encode($fields);
        //    echo $result;
        //    echo json_encode($headers);
        curl_close($ch);
    }

    public function test_get(){

        echo "this is sandeep testing function assssasaas";
        exit();
    }

    public function health_education_articles_post()
    {
        $article_info = $this->db->query("SELECT * from articles INNER JOIN article_department ON articles.article_id=article_department.article_id where article_department.partner_visibility=1 order by articles.article_id DESC ")->result();

        $j = 0;

        foreach ($article_info as $value) {

            $para['articles'][$j]['department_name'] = $value->department_name;
            $para['articles'][$j]['fullname'] = "Dr. " . $value->first_name . " " . $value->last_name;
            $para['articles'][$j]['profile_image'] = base_url("uploads/doctors/" . $value->profile_image);
            $para['articles'][$j]['work_place_location'] = $value->work_place_location;
            $para['articles'][$j]['article_title'] = $value->article_title;
            $para['articles'][$j]['article_id'] = $value->article_id;
            $para['articles'][$j]['posted_by'] = $value->posted_by;
            $para['articles'][$j]['description'] = $value->short_description;
            $para['articles'][$j]['article_status'] = $value->article_status;
            $para['articles'][$j]['type'] = $value->article_type;
            $para['articles'][$j]['posted_date'] = $value->posted_date;
            if ($value->article_status == "waiting") {
                $para['articles'][$j]['posted_url'] = base_url('uploads/article_videos/' . $value->posted_url);
            }

            if (strtolower($value->article_type) == "video") {
                $para['articles'][$j]['image_url'] = $value->video_image;
                $para['articles'][$j]['video'] = $value->video_url;
            } elseif (strtolower($value->article_type) == "image") {
                $images = explode(",", $value->posted_url);
                foreach ($images as $value) {
                    $para['articles'][$j]['article_image'][] = base_url('uploads/article_images/' . $value);
                }
            } elseif (strtolower($value->article_type) == "pdf") {
                $files = explode(",", $value->posted_url);
                foreach ($files as $value) {
                    $para['articles'][$j]['pdf'] = base_url('uploads/article_pdf/' . $value);
                }
            }

            $j++;
        }

        $this->response(array('code' => '200', 'message' => 'Health Education Articles', 'result' => $para));
    }




    public function articles_search_post()
    {

        extract($_POST);

        $article_info = $this->db->query("SELECT * from articles INNER JOIN article_department ON articles.article_id=article_department.article_id where article_department.partner_visibility=1 and articles.article_title LIKE '%" . urldecode($search) . "%' ")->result();
        $j = 0;
        foreach ($article_info as $value) {
            $para['articles'][$j]['department_name'] = $doctor_list->department_name;
            $para['articles'][$j]['fullname'] = "Dr. " . $doctor_list->first_name . " " . $doctor_list->last_name;
            $para['articles'][$j]['profile_image'] = base_url("uploads/doctors/" . $doctor_list->profile_image);
            $para['articles'][$j]['work_place_location'] = $doctor_list->work_place_location;
            $para['articles'][$j]['article_title'] = $value->article_title;
            $para['articles'][$j]['article_id'] = $value->article_id;
            $para['articles'][$j]['posted_by'] = $value->posted_by;
            $para['articles'][$j]['description'] = $value->short_description;
            $para['articles'][$j]['article_status'] = $value->article_status;
            $para['articles'][$j]['type'] = $value->article_type;
            if ($value->article_status == "waiting") {
                $para['articles'][$j]['posted_url'] = base_url('uploads/article_videos/' . $value->posted_url);
            }
            if (strtolower($value->article_type) == "video") {
                $para['articles'][$j]['image_url'] = $value->video_image;
                $para['articles'][$j]['video'] = $value->video_url;
            } elseif (strtolower($value->article_type) == "image") {
                $images = explode(",", $value->posted_url);
                foreach ($images as $value) {
                    $para['articles'][$j]['article_image'][] = base_url('uploads/article_images/' . $value);
                }
            } elseif (strtolower($value->article_type) == "pdf") {
                $files = explode(",", $value->posted_url);
                foreach ($files as $value) {
                    $para['articles'][$j]['pdf'] = base_url('uploads/article_pdf/' . $value);
                }
            }
            $j++;
        }

        $this->response(array('code' => '200', 'message' => 'Health Education Articles', 'result' => $para));
    }


    public function about_get()
    {

        $result[] = "https://www.youtube.com/watch?v=8jvmZ5Ans14";

        $this->response(array('code' => '200', 'message' => 'about video', 'result' => $result));
    }


    // last semi
}