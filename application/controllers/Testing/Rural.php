<?php

defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
ini_set('memory_limit', '-1');

require APPPATH . '/libraries/REST_Controller1.php';
require APPPATH . '/libraries/ImplementJwt.php';

require_once("PaytmChecksum.php");

class Rural extends REST_Controller1
{
    public function __construct() {
        parent::__construct();
        //Enable Nurse ASST DB
        $this->objOfJwt = new ImplementJwt();
        header('Content-Type: application/json');
        $this->load->library('mail_send', array('mailtype' => 'html'));
        
        $this->db3 = $this->load->database('third',TRUE);
        $this->load->helper('file');
        $this->load->library('PHPMailer');
        $this->load->library('mail_send', array('mailtype' => 'html'));
        $this->load->library('SMTP');
        $this->load->library('phpqrcode/qrlib');
        $this->load->library('zip');
        $this->load->model('Generic_model');
        // $headers = getallheaders();
        // echo "<pre>";print_r($headers);echo "</pre>";
    }

    public function tokenGenerator($rmp_email,$password){

        
        $TokenData['rmp_email']=$rmp_email;
        $TokenData['password']=$password;
        $tokenData['timeStamp'] = Date('Y-m-d h:i:s');
        $jwtToken = $this->objOfJwt->GenerateToken($tokenData);
        $token= json_encode(array('Token'=>$jwtToken));
    
        $this->db3->query("UPDATE rural_rmp_registration SET rural_rmp_registration.access_token = '".$jwtToken."' WHERE rural_rmp_registration.rmp_email='".$rmp_email."'");
    
        $this->db3->query("UPDATE rural_rmp_registration SET rural_rmp_registration.access_token = '".$jwtToken."' WHERE rural_rmp_registration.rmp_phone='".$rmp_email."'");
    
        return $jwtToken;
    
     }



    public function Rural_doctor_registration_post(){

        if(!empty(isset($_POST))){
            extract($_POST);
            $password=md5($rmp_password);

            if($rmp_name !="" and $rmp_gender!="" and $rmp_age !="" and  $rmp_email !="" and $rmp_phone !="" and $password !="" and $rmp_clinic_name !="" and $rmp_city !="")
            {

                $validmail=$this->db3->query("SELECT rural_rmp_registration.rmp_id FROM rural_rmp_registration WHERE rural_rmp_registration.rmp_email='".$rmp_email."'")->result();
                $validphone=$this->db3->query("SELECT rural_rmp_registration.rmp_id from rural_rmp_registration where rural_rmp_registration.rmp_phone='".$rmp_phone."'")->result();
    
                if(empty($validmail))
                {
    
                                if(empty($validphone)){
                                                    
                                                        $date = date("Y-m-d:H:i:s");
                                                        $this->load->library('upload'); 
                                                        $filetype = pathinfo($_FILES["file_i"]["name"], PATHINFO_EXTENSION);
                                                        $profile_pic = "doctor_pro_pic-".$date.".".$filetype;
                                                        $config['upload_path'] = './uploads/rural/doc_profile_pic/';
                                                        $config['file_name'] = $profile_pic;
                                                        $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG';
                                        
                                                        $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                                                        $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                                                        $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                                                        $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                                                        $_FILES['file_i']['size'] = $_FILES['file_i']['size'];
                                        
                                                        $files = ['jpg','png','jpeg'];
                                                        $filename = $_FILES['file_i']['name'];

                                                        $dp="doctor_pro_pic.jpg";
                                                        if(!in_array($filetype, $files))
                                                        {
                                                            $param['type'] = pathinfo($_FILES["file_i"]["name"], PATHINFO_EXTENSION);
                                                            $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
                                                        }
                                                        else
                                                        {  
                                                                $this->upload->initialize($config);
                                                                $this->upload->do_upload('file_i');            		
                                                                $fname = $this->upload->data();
                                                                // $this->db3->query("UPDATE rural_rmp_registration SET profile_pic='".$profile_pic."' WHERE rmp_id=1");
                                                                $dp=$profile_pic;
                                                                $param = "File Uploaded";
                                                                // $this->response(array('code'=>'200','message'=>'File Uploaded','result'=>$param)); 
                                                        } 

                                                        $tabledate= date("d-m-Y: h:i a");

                                                        $this->db3->query("INSERT INTO rural_rmp_registration (rmp_name,rmp_gender,rmp_age,rmp_email,rmp_phone,rmp_password,rmp_clinic_name,rmp_city,profile_pic,created_date) VALUES ('$rmp_name','$rmp_gender','$rmp_age','$rmp_email','$rmp_phone','$password','$rmp_clinic_name','$rmp_city','$dp','$tabledate')");
    
                                                        $this->response(array('code' => '200', 'message' => 'Your registration success'), 200); 
                                                        
    
                                                  }else{ 
    
                                                       $this->response(array('code' => '404', 'message' => 'your mobile number is already exist'), 200);
    
                                                     }
    
                }else
                {
                  
                    $this->response(array('code' => '404', 'message' => 'your email ID is already exist'), 200);
                }
    
             }
            else
            {
    
                $this->response(array('code' => '404', 'message' => 'Enter Total Fields'), 200);
            }
    
    }
    else
    {
        
        // echo "enter details";
        $this->response(array('code' => '404', 'message' => 'Enter Details'), 200);
    }
    
    
    }


    public function Rmplogin_post(){

        extract($_POST);
    
        // $rmp_phone_number;
        // $rmp_password;
        $word=md5($rmp_password);
    
    
        $pasval2=$this->db3->select('*')->from('rural_rmp_registration')->where('rmp_phone="'.$rmp_phone.'" OR rmp_email="'.$rmp_phone.'"')->get()->row();
    
        $pasID2=$pasval2->rmp_id;
    
        if($pasID2>0){
            
            $res2=$this->db3->select('*')->from('rural_rmp_registration')->where('rmp_phone="'.$rmp_phone.'" AND rmp_password="'.$word.'" OR rmp_email="'.$rmp_phone.'" AND rmp_password="'.$word.'"')->get()->row();
            // nurse_email="'.$nurse_email.'" AND nurse_password="'.$password.'" AND clinic_id="'.$clinic_id.'" OR nurse_phone_number="'.$nurse_email.'" AND nurse_password="'.$password.'" AND clinic_id="'.$clinic_id.'"'
            $id2=$res2->rmp_id;
            $name=$res2->rmp_name;
            $rmp_gender=$res2->rmp_gender;
            $rmp_email=$res2->rmp_email;
            $rmp_phone=$res2->rmp_phone;
            $clinic=$res2->rmp_clinic_name;
            $location=$res2->rmp_city;
            $profilePic = base_url('uploads/rural/doc_profile_pic/'.$res2->profile_pic);
    
            if( $id2>0){
                                $tokenprint=$this->tokenGenerator($rmp_email,$password);
                    
                            $this->response(array('code' => '200',
                                                            'message' => 'sucesslly login',
                                                            'token'=>$tokenprint ,
                                                            'details'=>array('rmp_id' =>$id2,
                                                                    'rmp_name'=>$name,
                                                                    'rmp_gender'=>$rmp_gender,
                                                                    'rmp_email'=>$rmp_email,
                                                                    'phonenumber'=>$rmp_phone,
                                                                    'clinic_name'=>$clinic,
                                                                    'clinic_location'=>$location,
                                                                    'profile_pic'=>$profilePic)
                                                                    )); 
                    }
                    else
                    {
                
                        $this->response(array('code' => '404', 'message' => 'your password is wrong'));
                    }
        }
        else
        {
                $this->response(array('code' => '404', 'message' => 'your Login id is wrong'));
        }
    
    
    }

// reset rural_doctor password through otp 

    public function resetpassword_post(){

        extract($_POST);
    
        $check_user=$this->db3->select('*')->from('rural_rmp_registration')->where('rmp_phone="'.$rmp_phone.'"')->get()->row();
    
        $count=count($check_user);
        if($count>0){
            $otp=rand(100000,999999);
            $mobile=$check_user->rmp_phone;
            $message="your OTP is  "  . $otp . " to reset your password."; 
            $rmp_name=$check_user->rmp_name;
            $rmp_id=$check_user->rmp_id;
            send_otp($mobile, $otp, $message);
            $this->response(array('code'=>'200','message'=>'OTP has sent on mobile ' .$mobile, 'otp'=>$otp,'username'=>$rmp_name,'rmp_id'=>$rmp_id));
        }else{
            
         $this->response(array('code'=>'404','message'=>'User Does Not Exist'),200);   
        }
    }


// password updated api...
//  this api is used to updated password 



    public function updatePassword_post(){

        extract($_POST);
    
        $passwordmd5=md5($password);
    
        $this->db3->query("UPDATE rural_rmp_registration SET rmp_password='".$passwordmd5."' WHERE rmp_phone='".$rmp_phone."'");
    
    $this->response(array('code'=>'200','message'=>'password sucessfully changed'));
    
    }

    // vitals creation api
    
    public function vital_POST(){


    if(!empty( isset($_POST))){
        extract($_POST);

        $date = date("d-m-Y: h:i a");

        if($heart_rate !="" and $blood_pressure !="" and $respiratory !="" and $temperature !="" and $patient_weight !="" and $patient_height !="" and $patient_id !="" and $rmp_id !="" and $saturation !="")
        {
            $this->db3->query("INSERT INTO rural_rmp_patient_vitals (patient_id,rmp_id,heart_rate,blood_pressure,respiratory,temperature,saturation,patient_weight,patient_height,created_by,created_date) VALUES ('$patient_id','$rmp_id','$heart_rate','$blood_pressure','$respiratory','$temperature','$saturation','$patient_weight','$patient_height','$rmp_id','$date')");
    
            $this->response(array('code' => '200', 'message' => 'vitals  successfully created'), 200); 
        }
        else{
            $this->response(array('code' => '404', 'message' => 'Enter total Fields'), 404);
        }

    }
    else
    {
        $this->response(array('code' => '404', 'message' => 'Enter Fields'), 404);
    }

    }
    

// vitals get api


    public function vitalsget_POST(){
        
            if(!empty(isset($_POST))){
                extract($_POST);
                if($patient_id!="" and $rmp_id!=""){

                $vital=$this->db3->query("select * from rural_rmp_patient_vitals where patient_id='".$patient_id."' and rmp_id='".$rmp_id."' ORDER BY created_date DESC")->result_array();
                
                    $data['patient_vitals']=$vital;
                    $this->response($data);
                }else{
                    $this->response(array('code' => '404', 'message' => 'enter patient_id and rmp_id'), 404);    
                }
            }
            else
            {
                $this->response(array('code' => '404', 'message' => 'Enter Fields'), 404);
            }

    }

// patient creation api
    
    public function patientCreation_post(){
            extract($_POST);
            
            $patient_profile_pic=$_FILES["file_profile_pic"]["name"];
            $patient_id_proof=$_FILES["file_id_proof"]["name"];
                
            $patient_profile="NULL"; $proof_id="NULL";
            // patient_profile pic upload...
            if(!empty($patient_profile_pic)){
               
        $date = date("Y-m-d:H:i:s");
        $this->load->library('upload'); 
        $filetype = pathinfo($_FILES["file_profile_pic"]["name"], PATHINFO_EXTENSION);
        $profile_pic = "patient_pro_pic-".$date.".".$filetype;
        $config['upload_path'] = './uploads/rural/patient_profile_pic/';
        $config['file_name'] = $profile_pic;
        $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG';

        $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
        $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
        $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
        $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
        $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

        $files = ['jpg','png','jpeg'];
        $filename = $_FILES['file_i']['name'];

        $dp="doctor_pro_pic.jpg";
        if(!in_array($filetype, $files))
        {
            $param['type'] = pathinfo($_FILES["file_profile_pic"]["name"], PATHINFO_EXTENSION);
            $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
        }
        else
        {  
                $this->upload->initialize($config);
                $this->upload->do_upload('file_profile_pic');            		
                $fname = $this->upload->data();
                $patient_profile=$profile_pic;
                $param = "File Uploaded";
                
        } 

    }
             // patient id proof upload...
            if(!empty($patient_id_proof)){

                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload'); 
                $filetype = pathinfo($_FILES["file_id_proof"]["name"], PATHINFO_EXTENSION);
                $id_proof = "patient_id_pic-".$date.".".$filetype;
                $config['upload_path'] = './uploads/rural/patient_id_pic/';
                $config['file_name'] = $id_proof;
                $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG';
        
                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];
        
                $files = ['jpg','png','jpeg'];
                $filename = $_FILES['file_i']['name'];
        
                $dp="doctor_pro_pic.jpg";
                if(!in_array($filetype, $files))
                {
                    $param['type'] = pathinfo($_FILES["file_id_proof"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
                }
                else
                {  
                        $this->upload->initialize($config);
                        $this->upload->do_upload('file_id_proof');            		
                        $fname = $this->upload->data();
                        $proof_id=$id_proof;
                        $param = "File Uploaded";
                        
                } 
   
              
            }
         

            if($rmp_id !="" and $patient_name !="" and $patient_gender !="" and $patient_age!="" and $patient_mobile_number !="" and $patient_location !="" )

            { 
                $tabledate= date("d-m-Y: h:i a");
            
            $this->db3->query("INSERT INTO rural_rmp_patients (rmp_id,patient_name,patient_gender,patient_age,patient_mobile_number,patient_id_proof,patient_profile_pic,patient_location,created_by,created_date) VALUES
             ('$rmp_id','$patient_name','$patient_gender','$patient_age','$patient_mobile_number','$proof_id','$patient_profile','$patient_location','$rmp_id','$tabledate')");
            
            $this->response(array('code' => '200', 'message' => 'patient sucessfully created'), 200);
            }

        else
        {
             $this->response(array('code' =>'404','message' =>'Enter Fields')); 
    
        }
}

public function totalpatients_post(){

    if(!empty( isset($_POST))){
        extract($_POST);

        if($rmp_id!="")
        {
            $vital=$this->db3->query("select * from rural_rmp_patients where  rmp_id='".$rmp_id."' ORDER BY created_date DESC")->result();
        
            $this->response( array('patient_id_proof_url'=>'https://www.devumdaa.in/dev/uploads/rural/patient_id_pic/',
                                   'patient_profile_pic_url'=>'https://www.devumdaa.in/dev/uploads/rural/patient_profile_pic/',
                                    'total_patients'=>$vital));
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
        }
        else
        {
            $this->response(array('code' => '404', 'message' => 'Enter rmp ID'), 404);
        }

    }
    else
    {
        $this->response(array('code' => '404', 'message' => 'Enter Fields'), 404);
    }

}



/* 
this is total gpe api total seven images upload process 

*/

public function gpexamination_post(){
                extract($_POST);
                
                $patient_id;$rmp_id;

                $sclera=$_FILES["sclera"]["name"];
                $palpebral_conjunctiva=$_FILES["palpebral_conjunctiva"]["name"];
                $oral_cavity=$_FILES["oral_cavity"]["name"];
                $neck=$_FILES["neck"]["name"];
                $dorsal_hand=$_FILES["dorsal_hand"]["name"];
                $palms=$_FILES["palms"]["name"];
                $leg=$_FILES["leg"]["name"];

// upload scleta image

        if(!empty($sclera)){
            
            $date = date("Y-m-d:H:i:s");
            $this->load->library('upload'); 
            $filetype = pathinfo($_FILES["sclera"]["name"], PATHINFO_EXTENSION);
            $patient_sclera = "sclera-pic-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
            $config['upload_path'] = './uploads/rural/Gpe_files/patient_sclera/';
            
            $config['file_name'] = $patient_sclera;
            $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG';
    
            $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
            $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
            $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
            $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
            $_FILES['file_i']['size'] = $_FILES['file_i']['size'];
    
            $files = ['jpg','png','jpeg'];
            
    
            
            if(!in_array($filetype, $files))
            {
                $param['type'] = pathinfo($_FILES["sclera"]["name"], PATHINFO_EXTENSION);
                $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
            }
            else
            {  
                    $this->upload->initialize($config);
                    $this->upload->do_upload('sclera');            		
                    $fname = $this->upload->data();
                    $patient_sclera_db_id=$patient_sclera;
                    $param = "File Uploaded";
                    
            } 
        }

// upload palpebral_conjunctiva

        if(!empty($palpebral_conjunctiva)){
            $date = date("Y-m-d:H:i:s");
            $this->load->library('upload'); 
            $filetype = pathinfo($_FILES["palpebral_conjunctiva"]["name"], PATHINFO_EXTENSION);
            $palpebral_conjunctiva = "palpebral_conjunctiva-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
            $config['upload_path'] = './uploads/rural/Gpe_files/patient_palpebral_conjunctiva/';
            
            $config['file_name'] = $palpebral_conjunctiva;
            $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG';
    
            $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
            $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
            $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
            $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
            $_FILES['file_i']['size'] = $_FILES['file_i']['size'];
    
            $files = ['jpg','png','jpeg'];
            
    
            
            if(!in_array($filetype, $files))
            {
                $param['type'] = pathinfo($_FILES["palpebral_conjunctiva"]["name"], PATHINFO_EXTENSION);
                $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
            }
            else
            {  
                    $this->upload->initialize($config);
                    $this->upload->do_upload('palpebral_conjunctiva');            		
                    $fname = $this->upload->data();
                    $palpebral_conjunctiva_db_id=$palpebral_conjunctiva;
                    $param = "File Uploaded";
                    
            } 
        }
        
// upload oral_cavity image
        
        if(!empty($oral_cavity)){
            $date = date("Y-m-d:H:i:s");
            $this->load->library('upload'); 
            $filetype = pathinfo($_FILES["oral_cavity"]["name"], PATHINFO_EXTENSION);
            $oral_cavity = "oral_cavity-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
            $config['upload_path'] = './uploads/rural/Gpe_files/patient_oral_cavity/';
            
            $config['file_name'] = $oral_cavity;
            $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG';
    
            $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
            $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
            $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
            $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
            $_FILES['file_i']['size'] = $_FILES['file_i']['size'];
    
            $files = ['jpg','png','jpeg'];
            
    
            
            if(!in_array($filetype, $files))
            {
                $param['type'] = pathinfo($_FILES["oral_cavity"]["name"], PATHINFO_EXTENSION);
                $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
            }
            else
            {  
                    $this->upload->initialize($config);
                    $this->upload->do_upload('oral_cavity');            		
                    $fname = $this->upload->data();
                    $oral_cavity_db_id=$oral_cavity;
                    $param = "File Uploaded";
                    
            } 
        }


// upload neck image


        if(!empty($neck)){
            $date = date("Y-m-d:H:i:s");
            $this->load->library('upload'); 
            $filetype = pathinfo($_FILES["neck"]["name"], PATHINFO_EXTENSION);
            $neck = "neck-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
            $config['upload_path'] = './uploads/rural/Gpe_files/patient_neck/';
            
            $config['file_name'] = $neck;
            $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG';
    
            $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
            $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
            $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
            $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
            $_FILES['file_i']['size'] = $_FILES['file_i']['size'];
    
            $files = ['jpg','png','jpeg'];
            
    
            
            if(!in_array($filetype, $files))
            {
                $param['type'] = pathinfo($_FILES["neck"]["name"], PATHINFO_EXTENSION);
                $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
            }
            else
            {  
                    $this->upload->initialize($config);
                    $this->upload->do_upload('neck');            		
                    $fname = $this->upload->data();
                    $neck_db_id=$neck;
                    $param = "File Uploaded";
                    
            } 
        }

//  upload dorsal_hand image

        if(!empty($dorsal_hand)){
            $date = date("Y-m-d:H:i:s");
            $this->load->library('upload'); 
            $filetype = pathinfo($_FILES["dorsal_hand"]["name"], PATHINFO_EXTENSION);
            $dorsal_hand = "dorsal_hand-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
            $config['upload_path'] = './uploads/rural/Gpe_files/patient_dorsal_hand/';
            
            $config['file_name'] = $dorsal_hand;
            $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG';
    
            $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
            $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
            $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
            $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
            $_FILES['file_i']['size'] = $_FILES['file_i']['size'];
    
            $files = ['jpg','png','jpeg'];
            
    
            
            if(!in_array($filetype, $files))
            {
                $param['type'] = pathinfo($_FILES["dorsal_hand"]["name"], PATHINFO_EXTENSION);
                $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
            }
            else
            {  
                    $this->upload->initialize($config);
                    $this->upload->do_upload('dorsal_hand');            		
                    $fname = $this->upload->data();
                    $dorsal_hand_db_id=$dorsal_hand;
                    $param = "File Uploaded";
                    
            } 
        }

// upload palms

        if(!empty($palms)){
            $date = date("Y-m-d:H:i:s");
            $this->load->library('upload'); 
            $filetype = pathinfo($_FILES["palms"]["name"], PATHINFO_EXTENSION);
            $palms = "palms-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
            $config['upload_path'] = './uploads/rural/Gpe_files/patient_palms/';
            
            $config['file_name'] = $palms;
            $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG';
    
            $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
            $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
            $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
            $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
            $_FILES['file_i']['size'] = $_FILES['file_i']['size'];
    
            $files = ['jpg','png','jpeg'];
            
    
            
            if(!in_array($filetype, $files))
            {
                $param['type'] = pathinfo($_FILES["palms"]["name"], PATHINFO_EXTENSION);
                $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
            }
            else
            {  
                    $this->upload->initialize($config);
                    $this->upload->do_upload('palms');            		
                    $fname = $this->upload->data();
                    $palms_db_id=$palms;
                    $param = "File Uploaded";
                    
            } 
        }

// leg


if(!empty($leg)){
    $date = date("Y-m-d:H:i:s");
    $this->load->library('upload'); 
    $filetype = pathinfo($_FILES["leg"]["name"], PATHINFO_EXTENSION);
    $leg = "leg-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
    $config['upload_path'] = './uploads/rural/Gpe_files/patient_leg/';
    
    $config['file_name'] = $leg;
    $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG';

    $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
    $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
    $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
    $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
    $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

    $files = ['jpg','png','jpeg'];
    

    
    if(!in_array($filetype, $files))
    {
        $param['type'] = pathinfo($_FILES["leg"]["name"], PATHINFO_EXTENSION);
        $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
    }
    else
    {  
            $this->upload->initialize($config);
            $this->upload->do_upload('leg');            		
            $fname = $this->upload->data();
            $leg_db_id=$leg;
            $param = "File Uploaded";
            
    } 
}

$tabledate = date("d-m-Y: h:i a");

$this->db3->query("INSERT INTO patient_gpe_images (rmp_id,patient_id,patient_oral_cavity,patient_neck,patient_dorsal_hand,patient_palms,patient_leg,patient_sclera,patient_palpebral_conjunctiva,created_by,created_date) VALUES 
('$rmp_id','$patient_id','$oral_cavity_db_id','$neck_db_id','$dorsal_hand_db_id','$palms_db_id','$leg_db_id','$patient_sclera_db_id','$palpebral_conjunctiva_db_id','$rmp_id','$tabledate')");

        $this->response(array('code' => '200', 'message' => 'Patient Gpe successfully submitted'), 200);


}

/*
        this api is used to get gpe 7 images using patient_id and rmp_id
*/

public function gpeget_post(){
    extract($_POST);

    if($patient_id !="" and $rmp_id !=""){

        $gpeimages=$this->db3->query("select * from patient_gpe_images where patient_id='".$patient_id."' and rmp_id='".$rmp_id."' ORDER BY patient_gpe_images.created_date DESC")->result();
        $a=0;

        // print_r($gpeimages);

        foreach($gpeimages as $gpe){

        $data['patient_gpe'][$a]['patient_gpe_img_id']= $gpe->patient_gpe_img_id;
        $data['patient_gpe'][$a]['rmp_id'] = $gpe->rmp_id;
        $data['patient_gpe'][$a]['patient_id'] = $gpe->patient_id;
        $data['patient_gpe'][$a]['patient_oral_cavity'] ='http://devumdaa.in/dev/uploads/rural/Gpe_files/patient_oral_cavity/'.$gpe->patient_oral_cavity;
        $data['patient_gpe'][$a]['patient_neck'] = 'http://devumdaa.in/dev/uploads/rural/Gpe_files/patient_neck/'.$gpe->patient_neck;
        $data['patient_gpe'][$a]['patient_dorsal_hand'] = 'http://devumdaa.in/dev/uploads/rural/Gpe_files/patient_dorsal_hand/'.$gpe->patient_dorsal_hand;
        $data['patient_gpe'][$a]['patient_palms'] = 'http://devumdaa.in/dev/uploads/rural/Gpe_files/patient_palms/'.$gpe->patient_palms;
        $data['patient_gpe'][$a]['patient_leg'] = 'http://devumdaa.in/dev/uploads/rural/Gpe_files/patient_leg/'.$gpe->patient_leg;
        $data['patient_gpe'][$a]['patient_sclera']= 'http://devumdaa.in/dev/uploads/rural/Gpe_files/patient_sclera/'.$gpe->patient_sclera;
        $data['patient_gpe'][$a]['patient_palpebral_conjunctiva'] = 'http://devumdaa.in/dev/uploads/rural/Gpe_files/patient_palpebral_conjunctiva/'.$gpe->patient_palpebral_conjunctiva;
        $data['patient_gpe'][$a]['modified_by'] = $gpe->modified_by;
        $data['patient_gpe'][$a]['created_by'] = $gpe->created_by;
        $data['patient_gpe'][$a]['created_date'] = $gpe->created_date;
        $data['patient_gpe'][$a]['modified_date'] = $gpe->modified_date;
        $a++;

        $this->response(array('code'=>'200','message'=>'success','gpefiles'=>$data['patient_gpe']));
    }
    }else{
        $this->response(array('code'=>'404','message'=>'enter patient_id and rmp_id'));
    }

}

public function hai_post(){

    $patient_profile_pic=$_FILES["file_profile_pic"]["name"];
    $patient_id_proof=$_FILES["file_id_proof"]["name"];

if(!empty($patient_id_proof)){
    echo "id proof submited"; 
    echo "<br>";
}else if(!empty($patient_profile_pic)){
    echo "<br>";
    echo "profile pic submited";
}else{
    echo "no image submited";
}

}

/**symptoms add funciton with audio file  */ 

public function addsymptoms_post(){

        extract($_POST);
        
        $symptoms_audio_file=$_FILES["symptoms_audio"]["name"];
        
            
         $symptoms_audio="NULL";
        
        if(!empty($symptoms_audio_file)){
           
    $date = date("Y-m-d:H:i:s");
    $this->load->library('upload'); 
    $filetype = pathinfo($_FILES["symptoms_audio"]["name"], PATHINFO_EXTENSION);
    $audio_name = "patient_symptoms_audio-".$patient_id."-".$rmp_id."-".time().".".$filetype;
    $config['upload_path'] = './uploads/rural/patient_symptoms/patient_symptoms_audio/';
    $config['file_name'] = $audio_name;
    $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

    $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
    $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
    $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
    $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
    $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

    $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
    

    
    if(!in_array($filetype, $files))
    {
        $param['type'] = pathinfo($_FILES["symptoms_audio"]["name"], PATHINFO_EXTENSION);
        $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
    }
    else
    {  
        
            $this->upload->initialize($config);
            $this->upload->do_upload('symptoms_audio');            		
           $fname = $this->upload->data();
            $symptoms_audio=$audio_name;
            $param = "File Uploaded";
            
    } 
                       

    }
     

        if($rmp_id!="" and $patient_id!="")

        {
            $tabledate= date("d-m-Y: h:i a");   
        
        $this->db3->query("INSERT INTO patient_symptoms (patient_id,rmp_id,describe_symptom_text,symptoms_audio,symptoms_duration,symptom_range,created_by,created_date) VALUES ('$patient_id','$rmp_id','$describe_symptom_text','$symptoms_audio','$symptoms_duration','$symptom_range','$rmp_id','$tabledate')");
        
        $this->response(array('code' => '200', 'message' => 'patient symptom sucessfully created'), 200);
        }

    else
    {
         $this->response(array('code' =>'404','message' =>'Enter Total Fields')); 

    }

    

}


public function getsymptoms_post(){
     extract($_POST);

        if($patient_id!="" and $rmp_id!=""){

            
            $symptoms=$this->db3->query("select * from patient_symptoms where patient_id='".$patient_id."' and rmp_id='".$rmp_id."' ORDER BY created_date DESC")->result();
            // $data['symptoms']=$symptoms;
            // $this->response($data);
       

            $a=0;
            foreach($symptoms as $sym)
            {
                $data['Symptoms_data'][$a]['patient_symptom_id'] = $sym->patient_symptom_id;
                $data['Symptoms_data'][$a]['patient_id']=$sym->patient_id;
                $data['Symptoms_data'][$a]['rmp_id'] = $sym->rmp_id;
                $data['Symptoms_data'][$a]['describe_symptom_text'] =$sym->describe_symptom_text;
                $data['Symptoms_data'][$a]['symptoms_audio'] = 'https://www.devumdaa.in/dev/uploads/rural/patient_symptoms/patient_symptoms_audio/'.$sym->symptoms_audio;
                $data['Symptoms_data'][$a]['symptoms_duration'] = $sym->symptoms_duration;
                $data['Symptoms_data'][$a]['symptom_range'] = $sym->symptom_range;
                $data['Symptoms_data'][$a]['modified_by'] = $sym->modified_by;
                $data['Symptoms_data'][$a]['created_by'] = $sym->created_by;
                $data['Symptoms_data'][$a]['created_date'] = $sym->created_date;
                $data['Symptoms_data'][$a]['modified_date'] = $sym->modified_date;
                $a++;
                // $obj=json_encode($data[]);
                
            }
            $this->response(array('code' => '200','message'=>'success','Symptoms_data'=>$data['Symptoms_data']));
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
                
        }else{
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
public function cardiopost_post(){
    extract($_POST);

    if($rmp_id !="" and $patient_id !=""){

    $mitral_audio=$_FILES["mitral_audio"]["name"];
    $arotic_audio=$_FILES["arotic_audio"]["name"];
    $pulmonary_audio=$_FILES["pulmonary_audio"]["name"];
    $tricuspid_audio=$_FILES["tricuspid_audio"]["name"];

    if(!empty($mitral_audio)){
           
        $date = date("Y-m-d:H:i:s");
        $this->load->library('upload'); 
        $filetype = pathinfo($_FILES["mitral_audio"]["name"], PATHINFO_EXTENSION);
        $audio_name = "mitral_audio-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
        $config['upload_path'] = './uploads/rural/sc/Cardio/Mitral/';
        $config['file_name'] = $audio_name;
        $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';
    
        $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
        $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
        $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
        $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
        $_FILES['file_i']['size'] = $_FILES['file_i']['size'];
    
        $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
        $filename = $_FILES['file_i']['name'];
    
        // $dp="doctor_pro_pic.jpg";
        if(!in_array($filetype, $files))
        {
            $param['type'] = pathinfo($_FILES["mitral_audio"]["name"], PATHINFO_EXTENSION);
            $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
        }
        else
        {  
                $this->upload->initialize($config);
                $this->upload->do_upload('mitral_audio');            		
                $fname = $this->upload->data();
                $mitral_audio=$audio_name;
                $param = "File Uploaded";
                
        } 
    
    }
    
    // arotic_audio

    if(!empty($arotic_audio)){
           
        $date = date("Y-m-d:H:i:s");
        $this->load->library('upload'); 
        $filetype = pathinfo($_FILES["arotic_audio"]["name"], PATHINFO_EXTENSION);
        $audio_name = "arotic_audio-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
        $config['upload_path'] = './uploads/rural/sc/Cardio/Arotic/';
        $config['file_name'] = $audio_name;
        $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';
    
        $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
        $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
        $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
        $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
        $_FILES['file_i']['size'] = $_FILES['file_i']['size'];
    
        $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
        $filename = $_FILES['file_i']['name'];
    
        // $dp="doctor_pro_pic.jpg";
        if(!in_array($filetype, $files))
        {
            $param['type'] = pathinfo($_FILES["arotic_audio"]["name"], PATHINFO_EXTENSION);
            $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
        }
        else
        {  
                $this->upload->initialize($config);
                $this->upload->do_upload('arotic_audio');            		
                $fname = $this->upload->data();
                $arotic_audio=$audio_name;
                $param = "File Uploaded";
                
        } 
    
    }


// pulmonary_audio 

    if(!empty($pulmonary_audio)){
           
        $date = date("Y-m-d:H:i:s");
        $this->load->library('upload'); 
        $filetype = pathinfo($_FILES["pulmonary_audio"]["name"], PATHINFO_EXTENSION);
        $audio_name = "pulmonary_audio-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
        $config['upload_path'] = './uploads/rural/sc/Cardio/Pulmonary/';
        $config['file_name'] = $audio_name;
        $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';
    
        $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
        $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
        $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
        $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
        $_FILES['file_i']['size'] = $_FILES['file_i']['size'];
    
        $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
        $filename = $_FILES['file_i']['name'];
    
        // $dp="doctor_pro_pic.jpg";
        if(!in_array($filetype, $files))
        {
            $param['type'] = pathinfo($_FILES["pulmonary_audio"]["name"], PATHINFO_EXTENSION);
            $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
        }
        else
        {  
                $this->upload->initialize($config);
                $this->upload->do_upload('pulmonary_audio');            		
                $fname = $this->upload->data();
                $pulmonary_audio=$audio_name;
                $param = "File Uploaded";
                
        } 
    
    }


// tricuspid_audio

if(!empty($tricuspid_audio)){
           
    $date = date("Y-m-d:H:i:s");
    $this->load->library('upload'); 
    $filetype = pathinfo($_FILES["tricuspid_audio"]["name"], PATHINFO_EXTENSION);
    $audio_name = "tricuspid_audio-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
    $config['upload_path'] = './uploads/rural/sc/Cardio/Tricuspid/';
    $config['file_name'] = $audio_name;
    $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

    $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
    $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
    $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
    $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
    $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

    $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
    $filename = $_FILES['file_i']['name'];

    // $dp="doctor_pro_pic.jpg";
    if(!in_array($filetype, $files))
    {
        $param['type'] = pathinfo($_FILES["tricuspid_audio"]["name"], PATHINFO_EXTENSION);
        $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
    }
    else
    {  
            $this->upload->initialize($config);
            $this->upload->do_upload('tricuspid_audio');            		
            $fname = $this->upload->data();
            $tricuspid_audio=$audio_name;
            $param = "File Uploaded";
            
    } 

}
$table_date= date("d-m-Y: h:i a");

$this->db3->query("INSERT INTO sc_cardio (patient_id,rmp_id,mitral_audio,arotic_audio,pulmonary_audio,tricuspid_audio,created_by,created_date) VALUES ('$patient_id','$rmp_id','$mitral_audio','$arotic_audio','$pulmonary_audio','$tricuspid_audio','$rmp_id','$table_date')");
        
$this->response(array('code' => '200', 'message' => 'patient Systematic Examination sucessfully created'), 200);

     
    }
    else
    {
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



public function frontresp_post(){
    extract($_POST);

    if($rmp_id !="" and $patient_id !=""){

    $right_infra_clavicle=$_FILES["right_infra_clavicle"]["name"];
    $left_infra_clavicle=$_FILES["left_infra_clavicle"]["name"];
    $right_infra_mammary=$_FILES["right_infra_mammary"]["name"];
    $left_infra_mammary=$_FILES["left_infra_mammary"]["name"];


// right_infra_clavicle


    if(!empty($right_infra_clavicle)){
           
        $date = date("Y-m-d:H:i:s");
        $this->load->library('upload'); 
        $filetype = pathinfo($_FILES["right_infra_clavicle"]["name"], PATHINFO_EXTENSION);
        $audio_name = "right_infra_clavicle-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
        $config['upload_path'] = './uploads/rural/sc/Resp_front/Right_Infra_Clavicle/';
        $config['file_name'] = $audio_name;
        $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';
    
        $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
        $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
        $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
        $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
        $_FILES['file_i']['size'] = $_FILES['file_i']['size'];
    
        $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
        $filename = $_FILES['file_i']['name'];
    
        // $dp="doctor_pro_pic.jpg";
        if(!in_array($filetype, $files))
        {
            $param['type'] = pathinfo($_FILES["right_infra_clavicle"]["name"], PATHINFO_EXTENSION);
            $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
        }
        else
        {  
                $this->upload->initialize($config);
                $this->upload->do_upload('right_infra_clavicle');            		
                $fname = $this->upload->data();
                $right_infra_clavicle=$audio_name;
                $param = "File Uploaded";
                
        } 
    
    }
// left_infra_clavicle

    if(!empty($left_infra_clavicle)){
           
        $date = date("Y-m-d:H:i:s");
        $this->load->library('upload'); 
        $filetype = pathinfo($_FILES["left_infra_clavicle"]["name"], PATHINFO_EXTENSION);
        $audio_name = "left_infra_clavicle-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
        $config['upload_path'] = './uploads/rural/sc/Resp_front/Left_Infra_Clavicle/';
        $config['file_name'] = $audio_name;
        $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';
    
        $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
        $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
        $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
        $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
        $_FILES['file_i']['size'] = $_FILES['file_i']['size'];
    
        $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
        $filename = $_FILES['file_i']['name'];
    
        // $dp="doctor_pro_pic.jpg";
        if(!in_array($filetype, $files))
        {
            $param['type'] = pathinfo($_FILES["left_infra_clavicle"]["name"], PATHINFO_EXTENSION);
            $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
        }
        else
        {  
                $this->upload->initialize($config);
                $this->upload->do_upload('left_infra_clavicle');            		
                $fname = $this->upload->data();
                $left_infra_clavicle=$audio_name;
                $param = "File Uploaded";
                
        } 
    
    }


// right_infra_mammary 

    if(!empty($right_infra_mammary)){
           
        $date = date("Y-m-d:H:i:s");
        $this->load->library('upload'); 
        $filetype = pathinfo($_FILES["right_infra_mammary"]["name"], PATHINFO_EXTENSION);
        $audio_name = "right_infra_mammary-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
        $config['upload_path'] = './uploads/rural/sc/Resp_front/Right_Infra_Mammary/';
        $config['file_name'] = $audio_name;
        $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';
    
        $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
        $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
        $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
        $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
        $_FILES['file_i']['size'] = $_FILES['file_i']['size'];
    
        $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
        $filename = $_FILES['file_i']['name'];
    
        // $dp="doctor_pro_pic.jpg";
        if(!in_array($filetype, $files))
        {
            $param['type'] = pathinfo($_FILES["right_infra_mammary"]["name"], PATHINFO_EXTENSION);
            $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
        }
        else
        {  
                $this->upload->initialize($config);
                $this->upload->do_upload('right_infra_mammary');            		
                $fname = $this->upload->data();
                $right_infra_mammary=$audio_name;
                $param = "File Uploaded";
                
        } 
    
    }


// left_infra_mammary

if(!empty($left_infra_mammary)){
           
    $date = date("Y-m-d:H:i:s");
    $this->load->library('upload'); 
    $filetype = pathinfo($_FILES["left_infra_mammary"]["name"], PATHINFO_EXTENSION);
    $audio_name = "left_infra_mammary-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
    $config['upload_path'] = './uploads/rural/sc/Resp_front/Left_Infra_Mammary/';
    $config['file_name'] = $audio_name;
    $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

    $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
    $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
    $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
    $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
    $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

    $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
    $filename = $_FILES['file_i']['name'];

    // $dp="doctor_pro_pic.jpg";
    if(!in_array($filetype, $files))
    {
        $param['type'] = pathinfo($_FILES["left_infra_mammary"]["name"], PATHINFO_EXTENSION);
        $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
    }
    else
    {  
            $this->upload->initialize($config);
            $this->upload->do_upload('left_infra_mammary');            		
            $fname = $this->upload->data();
            $left_infra_mammary=$audio_name;
            $param = "File Uploaded";
            
    } 

}

$tabledate= date("d-m-Y: h:i a");

$this->db3->query("INSERT INTO sc_resp_front (patient_id,rmp_id,right_infra_clavicle_audio,left_infra_clavicle_audio,right_infra_mammary_audio,left_infra_mammary_audio,created_by,created_date) VALUES ('$patient_id','$rmp_id','$right_infra_clavicle','$left_infra_clavicle','$right_infra_mammary','$left_infra_mammary','$rmp_id','$tabledate')");
        
$this->response(array('code' => '200', 'message' => 'patient Systematic Examination ( front respiration ) sucessfully created'), 200);

     
    }
    else
    {
        $this->response(array('code' => '404', 'message' => 'enter patient_id and rmp_id'), 404); 
    }
    
}



public function backresp_post(){
    extract($_POST);

    if($rmp_id !="" and $patient_id !=""){

    $left_interscapular_audio=$_FILES["left_interscapular_audio"]["name"];
    $right_interscapular_audio=$_FILES["right_interscapular_audio"]["name"];
    $left_infrascapular_audio=$_FILES["left_infrascapular_audio"]["name"];
    $right_infrascapular_audio=$_FILES["right_infrascapular_audio"]["name"];


// left_interscapular_audio


    if(!empty($left_interscapular_audio)){
           
        $date = date("Y-m-d:H:i:s");
        $this->load->library('upload'); 
        $filetype = pathinfo($_FILES["left_interscapular_audio"]["name"], PATHINFO_EXTENSION);
        $audio_name = "left_interscapular_audio-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
        $config['upload_path'] = './uploads/rural/sc/resp_back/Left_Interscapular/';
        $config['file_name'] = $audio_name;
        $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';
    
        $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
        $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
        $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
        $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
        $_FILES['file_i']['size'] = $_FILES['file_i']['size'];
    
        $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
        $filename = $_FILES['file_i']['name'];
    
        // $dp="doctor_pro_pic.jpg";
        if(!in_array($filetype, $files))
        {
            $param['type'] = pathinfo($_FILES["left_interscapular_audio"]["name"], PATHINFO_EXTENSION);
            $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
        }
        else
        {  
                $this->upload->initialize($config);
                $this->upload->do_upload('left_interscapular_audio');            		
                $fname = $this->upload->data();
                $left_interscapular_audio=$audio_name;
                $param = "File Uploaded";
                
        } 
    
    }
// left_infra_clavicle

    if(!empty($right_interscapular_audio)){
           
        $date = date("Y-m-d:H:i:s");
        $this->load->library('upload'); 
        $filetype = pathinfo($_FILES["right_interscapular_audio"]["name"], PATHINFO_EXTENSION);
        $audio_name = "right_interscapular_audio-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
        $config['upload_path'] = './uploads/rural/sc/resp_back/Right_Interscapular/';
        $config['file_name'] = $audio_name;
        $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';
    
        $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
        $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
        $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
        $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
        $_FILES['file_i']['size'] = $_FILES['file_i']['size'];
    
        $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
        $filename = $_FILES['file_i']['name'];
    
        // $dp="doctor_pro_pic.jpg";
        if(!in_array($filetype, $files))
        {
            $param['type'] = pathinfo($_FILES["right_interscapular_audio"]["name"], PATHINFO_EXTENSION);
            $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
        }
        else
        {  
                $this->upload->initialize($config);
                $this->upload->do_upload('right_interscapular_audio');            		
                $fname = $this->upload->data();
                $right_interscapular_audio=$audio_name;
                $param = "File Uploaded";
                
        } 
    
    }


// left_infrascapular_audio 

    if(!empty($left_infrascapular_audio)){
           
        $date = date("Y-m-d:H:i:s");
        $this->load->library('upload'); 
        $filetype = pathinfo($_FILES["left_infrascapular_audio"]["name"], PATHINFO_EXTENSION);
        $audio_name = "left_infrascapular_audio-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
        $config['upload_path'] = './uploads/rural/sc/resp_back/Left_Infrascapular/';
        $config['file_name'] = $audio_name;
        $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';
    
        $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
        $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
        $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
        $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
        $_FILES['file_i']['size'] = $_FILES['file_i']['size'];
    
        $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
        $filename = $_FILES['file_i']['name'];
    
        // $dp="doctor_pro_pic.jpg";
        if(!in_array($filetype, $files))
        {
            $param['type'] = pathinfo($_FILES["left_infrascapular_audio"]["name"], PATHINFO_EXTENSION);
            $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
        }
        else
        {  
                $this->upload->initialize($config);
                $this->upload->do_upload('left_infrascapular_audio');            		
                $fname = $this->upload->data();
                $left_infrascapular_audio=$audio_name;
                $param = "File Uploaded";
                
        } 
    
    }


// right_infrascapular_audio

if(!empty($right_infrascapular_audio)){
           
    $date = date("Y-m-d:H:i:s");
    $this->load->library('upload'); 
    $filetype = pathinfo($_FILES["right_infrascapular_audio"]["name"], PATHINFO_EXTENSION);
    $audio_name = "right_infrascapular_audio-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
    $config['upload_path'] = './uploads/rural/sc/resp_back/Right_Infrascapular/';
    $config['file_name'] = $audio_name;
    $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

    $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
    $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
    $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
    $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
    $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

    $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
    $filename = $_FILES['file_i']['name'];

    // $dp="doctor_pro_pic.jpg";
    if(!in_array($filetype, $files))
    {
        $param['type'] = pathinfo($_FILES["right_infrascapular_audio"]["name"], PATHINFO_EXTENSION);
        $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
    }
    else
    {  
            $this->upload->initialize($config);
            $this->upload->do_upload('right_infrascapular_audio');            		
            $fname = $this->upload->data();
            $right_infrascapular_audio=$audio_name;
            $param = "File Uploaded";
            
    } 

}

$tabledate= date("d-m-Y: h:i a");

$this->db3->query("INSERT INTO sc_resp_back (patient_id,rmp_id,left_interscapular_audio,right_interscapular_audio,left_infrascapular_audio,right_infrascapular_audio,created_by,created_date)
 VALUES ('$patient_id','$rmp_id','$left_interscapular_audio','$right_interscapular_audio','$left_infrascapular_audio','$right_infrascapular_audio','$rmp_id','$tabledate')");
        
$this->response(array('code' => '200', 'message' => 'patient Systematic Examination ( back respiration ) sucessfully created'), 200);

     
    }
    else
    {
        $this->response(array('code' => '404', 'message' => 'enter patient_id and rmp_id'), 404); 
    }
    
}

public function cardioget_post(){
extract($_POST);
if($patient_id!="" and $rmp_id!=""){
$cardio=$this->db3->query("select * from sc_cardio where rmp_id='".$rmp_id."' and patient_id='".$patient_id."' ORDER BY created_date DESC")->result();
if(!empty($cardio)){

     $a=0;
foreach($cardio as $car)
{
    $data['cardioget'][$a]['sc_cardio_id']=$car->sc_cardio_id;
    $data['cardioget'][$a]['rmp_id']=$car->rmp_id;
    $data['cardioget'][$a]['patient_id']=$car->patient_id;
    $data['cardioget'][$a]['mitral_audio']='http://devumdaa.in/dev/uploads/rural/sc/Cardio/Mitral/'.$car->mitral_audio;
    $data['cardioget'][$a]['arotic_audio']='http://devumdaa.in/dev/uploads/rural/sc/Cardio/Arotic/'.$car->arotic_audio;
    $data['cardioget'][$a]['pulmonary_audio']='http://devumdaa.in/dev/uploads/rural/sc/Cardio/Pulmonary/'.$car->pulmonary_audio;
    $data['cardioget'][$a]['tricuspid_audio']='http://devumdaa.in/dev/uploads/rural/sc/Cardio/Tricuspid/'.$car->tricuspid_audio;
    $data['cardioget'][$a]['modified_by']=$car->modified_by;
    $data['cardioget'][$a]['created_by']=$car->created_by;
    $data['cardioget'][$a]['created_date']=$car->created_date;
    $data['cardioget'][$a]['modified_date']=$car->modified_date;
    $a++;
}
    
$this->response(array('code' => '200','message'=>'success','cardio_data'=>$data['cardioget']));
}

else
{
 
    $this->response(array('code'=>'404','message'=>'no data fount'));
}
}else{
  $this->response(array('code'=>'404','message'=>'enter patient_id and rmp_id'));  
}
}



public function frontrespget_post(){
    extract($_POST);
    if($patient_id!="" and $rmp_id!=""){
    $front=$this->db3->query("select * from sc_resp_front where rmp_id='".$rmp_id."' and patient_id='".$patient_id."'")->result();
    if(!empty($front)){
    
         $a=0;
    foreach($front as $fro)
    {
        $data['frontres'][$a]['sc_resp_front_id']=$fro->sc_resp_front_id;
        $data['frontres'][$a]['rmp_id']=$fro->rmp_id;
        $data['frontres'][$a]['patient_id']=$fro->patient_id;
        $data['frontres'][$a]['Right_Infra_Mammary']='http://devumdaa.in/dev/uploads/rural/sc/Resp_front/Right_Infra_Mammary/'.$fro->right_infra_mammary_audio;
        $data['frontres'][$a]['Right_Infra_Clavicle']='http://devumdaa.in/dev/uploads/rural/sc/Resp_front/Right_Infra_Clavicle/'.$fro->right_infra_clavicle_audio;
        $data['frontres'][$a]['Left_Infra_Mammary']='http://devumdaa.in/dev/uploads/rural/sc/Resp_front/Left_Infra_Mammary/'.$fro->left_infra_mammary_audio;
        $data['frontres'][$a]['Left_Infra_Clavicle']='http://devumdaa.in/dev/uploads/rural/sc/Resp_front/Left_Infra_Clavicle/'.$fro->left_infra_clavicle_audio;
        $data['frontres'][$a]['modified_by']=$fro->modified_by;
        $data['frontres'][$a]['created_by']=$fro->created_by;
        $data['frontres'][$a]['created_date']=$fro->created_date;
        $data['frontres'][$a]['modified_date']=$fro->modified_date;
        $a++;
    }
        
    $this->response(array('code' => '200','message'=>'success','frontres_data'=>$data['frontres']));
    }
    
    else
    {
     
        $this->response(array('code'=>'404','message'=>'no data fount'));
    }
    }else{
      $this->response(array('code'=>'404','message'=>'enter patient_id and rmp_id'));  
    }
    }



    public function backrespget_post(){
        extract($_POST);
        if($patient_id!="" and $rmp_id!=""){
        $backk=$this->db3->query("select * from sc_resp_back where rmp_id='".$rmp_id."' and patient_id='".$patient_id."'")->result();
        if(!empty($backk)){
        
             $a=0;
        foreach($backk as $bac)
        {
            $data['backresp'][$a]['sc_resp_back_id']=$bac->sc_resp_back_id;
            $data['backresp'][$a]['rmp_id']=$bac->rmp_id;
            $data['backresp'][$a]['patient_id']=$bac->patient_id;
            $data['backresp'][$a]['left_interscapular_audio']='http://devumdaa.in/dev/uploads/rural/sc/resp_back/Left_Interscapular/'.$bac->left_interscapular_audio;
            $data['backresp'][$a]['right_interscapular_audio']='http://devumdaa.in/dev/uploads/rural/sc/resp_back/Right_Interscapular/'.$bac->right_interscapular_audio;
            $data['backresp'][$a]['left_infrascapular_audio']='http://devumdaa.in/dev/uploads/rural/sc/resp_back/Left_Infrascapular/'.$bac->left_infrascapular_audio;
            $data['backresp'][$a]['right_infrascapular_audio']='http://devumdaa.in/dev/uploads/rural/sc/resp_back/Right_Infrascapular/'.$bac->right_infrascapular_audio;
            $data['backresp'][$a]['modified_by']=$bac->modified_by;
            $data['backresp'][$a]['created_by']=$bac->created_by;
            $data['backresp'][$a]['created_date']=$bac->created_date;
            $data['backresp'][$a]['modified_date']=$bac->modified_date;
            $a++;
        }
            
        $this->response(array('code' => '200','message'=>'success','backresp_data'=>$data['backresp']));
        }
        
        else
        {
         
            $this->response(array('code'=>'404','message'=>'no data fount'));
        }

        }
        else
        {
          $this->response(array('code'=>'404','message'=>'enter patient_id and rmp_id'));  
        }
    }

    /*
    abdominalcns_post this api is used to upload one audio file and two text fields into sc_abdominal_cns table
    
    */ 

public function abdominalcns_post(){

    extract($_POST);
    if($patient_id !="" and $rmp_id !=""){
        
        $examination_audio=$_FILES['abdominalcns']['name'];
        $exa_audio="";

        if(!empty($examination_audio)){
           
            $date = date("Y-m-d:H:i:s");
            $this->load->library('upload'); 
            $filetype = pathinfo($_FILES["abdominalcns"]["name"], PATHINFO_EXTENSION);
            $audio_name = "abdominal_cns-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
            $config['upload_path'] = './uploads/rural/sc/abdominal_cns/';
            $config['file_name'] = $audio_name;
            $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';
        
            $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
            $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
            $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
            $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
            $_FILES['file_i']['size'] = $_FILES['file_i']['size'];
        
            $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
            $filename = $_FILES['file_i']['name'];
        
            // $dp="doctor_pro_pic.jpg";
            if(!in_array($filetype, $files))
            {
                $param['type'] = pathinfo($_FILES["abdominalcns"]["name"], PATHINFO_EXTENSION);
                $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
            }
            else
            {  
                    $this->upload->initialize($config);
                    $this->upload->do_upload('abdominalcns');            		
                    $fname = $this->upload->data();
                    $right_infrascapular_audio=$audio_name;
                    $param = "File Uploaded";
                    
            } 
        
        }

        $tabledate= date("d-m-Y: h:i a");

        $this->db3->query("INSERT INTO sc_abdominal_cns (patient_id,rmp_id,abdominal_comment,cns_comment,examination_audio,created_by,created_date) VALUES ('$patient_id','$rmp_id','$abdominal_comment','$cns_comment','$right_infrascapular_audio','$rmp_id','$tabledate')");
        
        $this->response(array('code' => '200', 'message' => 'abdominal and c.n.s examination is sucessfully created'), 200);
        

    }else{

        $this->response(array('code'=>'404','message'=>'enter patient_id and rmp_id'));

    }

}

//  abdominal and cns get this api is used to get abdominal and cns get


public function abdominalcnsget_post(){
    extract($_POST);

    if($patient_id!="" and $rmp_id!=""){

    $abdominal=$this->db3->query("select * from sc_abdominal_cns where rmp_id='".$rmp_id."' and patient_id='".$patient_id."'")->result();

    if(!empty($abdominal)){
        $a=0;
        foreach($abdominal as $abd){


            $data['abdominal_cns'][$a]['patient_abdominal_cns_id']=$abd->patient_abdominal_cns_id;
            $data['abdominal_cns'][$a]['rmp_id']=$abd->rmp_id;
            $data['abdominal_cns'][$a]['patient_id']=$abd->patient_id;
            $data['abdominal_cns'][$a]['abdominal_comment']=$abd->abdominal_comment;
            $data['abdominal_cns'][$a]['cns_comment']=$abd->cns_comment;
            $data['abdominal_cns'][$a]['examination_audio']='http://devumdaa.in/dev/uploads/rural/sc/abdominal_cns/'.$abd->examination_audio;
            $data['abdominal_cns'][$a]['modified_by']=$abd->modified_by;
            $data['abdominal_cns'][$a]['created_by']=$abd->created_by;
            $data['abdominal_cns'][$a]['created_date']=$abd->created_date;
            $data['abdominal_cns'][$a]['modified_date']=$abd->modified_date;

            $a++;
        }

        $this->response(array('code' => '200','message'=>'success','abdominal_cns'=>$data['abdominal_cns']));
       
    }
    else
    {
    $this->response(array('code'=>'404','message'=>'no data fount'));
    }
    
}
else
{
$this->response(array('code'=>'404','message'=>'enter patient_id and rmp_id'));  
}
}


public function reports_post(){

    $tabledate= date("d-m-Y: h:i a");

    extract($_POST);
    if( $rmp_id !="" and $patient_id !="")
    {
        $this->db3->query("INSERT INTO patients_reports (rmp_id,patient_id,report_date,report_type,report_description,created_by,created_date) VALUES ('$rmp_id','$patient_id','$report_date','$report_type','$report_description','$rmp_id','$tabledate')");
        
        $this->response(array('code' =>'200','message'=> 'report created successfully'));
    }
    else
    {
        $this->response(array('code' =>'404','message'=> 'enter rmp_id and patient_id'));
    } 

}

public function reportsget_post(){

    extract($_POST);
    if($patient_id !="" and $rmp_id !=""){

        $value=$this->db3->query("select * from patients_reports where patient_id='".$patient_id."' and rmp_id='".$rmp_id."'")->result();

        $this->response(array('code' =>'200','patient_reports'=>$value));

    }else{
        $this->response(array('code' =>'404','message'=> 'enter rmp_id and patient_id'));
    }
}

// this api is used to upload the pdf documents

public function reportslineitempdf_post(){


    $tabledate= date("d-m-Y: h:i a");
    extract($_POST);

        if($rmp_id !="" and $patient_id !="" and $reports_id !=""){

            $report_image=$_FILES['report_pdf']['name'];
            // $exa_audio="";
    
            if(!empty($report_image)){
               
                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload'); 
                $filetype = pathinfo($_FILES["report_pdf"]["name"], PATHINFO_EXTENSION);
                $file_name = "reports-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
                $config['upload_path'] = './uploads/rural/patient_reports/';
                $config['file_name'] = $file_name;
                $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG|pdf|PDF';
            
                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];
            
                $files = ['jpg','png','jpeg','JPG','PNG','JPEG','pdf','PDF'];
                $filename = $_FILES['file_i']['name'];
            
                // $dp="doctor_pro_pic.jpg";
                if(!in_array($filetype, $files))
                {
                
                    $param['type'] = pathinfo($_FILES["report_pdf"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
                }
                else
                {  
                        $this->upload->initialize($config);
                        $this->upload->do_upload('report_pdf');            		
                        $fname = $this->upload->data();
                        // $right_infrascapular_audio=$file_name;
                        $param = "File Uploaded";
                        
                } 
            
            }

    $this->db3->query("INSERT INTO patient_report_line_items (rmp_id,patient_id,reports_id,report_image,created_by,created_date) VALUES ('$rmp_id','$patient_id','$reports_id','$file_name','$rmp_id','$tabledate')");
    

        $this->response(array('code' => '200', 'message' => 'report pdf successfully uploaded'));

        }else{

    $this->response(array('code' => '404', 'message' => 'enter rmp_id and patient_id and report_id'));
    }


    


}

public function reportslineitem_post(){
    $tabledate= date("d-m-Y: h:i a");
    extract($_POST);

        if($rmp_id !="" and $patient_id !="" and $reports_id !=""){

            $report_image=$_FILES['report_image']['name'];
            // $exa_audio="";
    
            if(!empty($report_image)){
               
                $date = date("Y-m-d:H:i:s");
                $this->load->library('upload'); 
                $filetype = pathinfo($_FILES["report_image"]["name"], PATHINFO_EXTENSION);
                $file_name = "reports-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
                $config['upload_path'] = './uploads/rural/patient_reports/';
                $config['file_name'] = $file_name;
                $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG|pdf|PDF';
            
                $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
                $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
                $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
                $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
                $_FILES['file_i']['size'] = $_FILES['file_i']['size'];
            
                $files = ['jpg','png','jpeg','JPG','PNG','JPEG','pdf','PDF'];
                $filename = $_FILES['file_i']['name'];
            
                // $dp="doctor_pro_pic.jpg";
                if(!in_array($filetype, $files))
                {
                    $param['type'] = pathinfo($_FILES["report_image"]["name"], PATHINFO_EXTENSION);
                    $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
                }
                else
                {  
                        $this->upload->initialize($config);
                        $this->upload->do_upload('report_image');            		
                        $fname = $this->upload->data();
                        // $right_infrascapular_audio=$file_name;
                        $param = "File Uploaded";
                        
                } 
            
            }

    $this->db3->query("INSERT INTO patient_report_line_items (rmp_id,patient_id,reports_id,report_image,created_by,created_date) VALUES ('$rmp_id','$patient_id','$reports_id','$file_name','$rmp_id','$tabledate')");
    

        $this->response(array('code' => '200', 'message' => 'report image successfully uploaded'));

        }else{

    $this->response(array('code' => '404', 'message' => 'enter rmp_id and patient_id and report_id'));
    }

    }


    public function reportslineitemget_post(){

        extract($_POST);
        
        if($patient_id !="" and $rmp_id !="" and $reports_id !=""){

            $value=$this->db3->query("select * from patient_report_line_items where patient_id='".$patient_id."' and rmp_id='".$rmp_id."' and reports_id='".$reports_id."'")->result();
            
            if(!empty($value)){

                        $a=0;
                        foreach($value as $val){

                            $data['reportslineitem'][$a]['patient_report_line_item_id']=$val->patient_report_line_item_id;
                            $data['reportslineitem'][$a]['reports_id']=$val->reports_id;
                            $data['reportslineitem'][$a]['rmp_id']=$val->rmp_id;
                            $data['reportslineitem'][$a]['patient_id']=$val->patient_id;
                            $data['reportslineitem'][$a]['report_image']='http://devumdaa.in/dev/uploads/rural/patient_reports/'.$val->report_image;
                            $data['reportslineitem'][$a]['modified_by']=$val->modified_by;
                            $data['reportslineitem'][$a]['created_by']=$val->created_by;
                            $data['reportslineitem'][$a]['created_date']=$val->created_date;
                            $data['reportslineitem'][$a]['modified_date']=$val->modified_date;

                            $a++;

                        }

                            $this->response(array('code' =>'200','reports_line_items'=>$data['reportslineitem']));
        
        }else{ 
            
            $this->response(array('code' =>'404','message'=>'No records found'));
        
            }
    
        }else{

            $this->response(array('code' =>'404','message'=> 'enter rmp_id and patient_id and reports_id'));
        }
        
    }

//     public function resp_post(){

//     extract($_POST);
//     if($patient_id!="" and $rmp_id!=""){
//     $front=$this->db3->query("select * from sc_resp_front where rmp_id='".$rmp_id."' and patient_id='".$patient_id."'")->result();
   
//     $backk=$this->db3->query("select * from sc_resp_back where rmp_id='".$rmp_id."' and patient_id='".$patient_id."'")->result();
      
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


public function respiration_post(){

    extract($_POST);

    if($rmp_id !="" and $patient_id !=""){

    $right_infra_clavicle=$_FILES["right_infra_clavicle"]["name"];
    $left_infra_clavicle=$_FILES["left_infra_clavicle"]["name"];
    $right_infra_mammary=$_FILES["right_infra_mammary"]["name"];
    $left_infra_mammary=$_FILES["left_infra_mammary"]["name"];
    $left_interscapular_audio=$_FILES["left_interscapular_audio"]["name"];
    $right_interscapular_audio=$_FILES["right_interscapular_audio"]["name"];
    $left_infrascapular_audio=$_FILES["left_infrascapular_audio"]["name"];
    $right_infrascapular_audio=$_FILES["right_infrascapular_audio"]["name"];

    // right_infra_clavicle


    if(!empty($right_infra_clavicle)){
       
    $date = date("Y-m-d:H:i:s");
    $this->load->library('upload'); 
    $filetype = pathinfo($_FILES["right_infra_clavicle"]["name"], PATHINFO_EXTENSION);
    $audio_name = "right_infra_clavicle-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
    $config['upload_path'] = './uploads/rural/sc/Resp_front/Right_Infra_Clavicle/';
    $config['file_name'] = $audio_name;
    $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

    $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
    $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
    $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
    $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
    $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

    $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
    $filename = $_FILES['file_i']['name'];

    // $dp="doctor_pro_pic.jpg";
    if(!in_array($filetype, $files))
    {
        $param['type'] = pathinfo($_FILES["right_infra_clavicle"]["name"], PATHINFO_EXTENSION);
        $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
    }
    else
    {  
            $this->upload->initialize($config);
            $this->upload->do_upload('right_infra_clavicle');            		
            $fname = $this->upload->data();
            $right_infra_clavicle=$audio_name;
            $param = "File Uploaded";
            
    } 

    }
    // left_infra_clavicle

    if(!empty($left_infra_clavicle)){
       
    $date = date("Y-m-d:H:i:s");
    $this->load->library('upload'); 
    $filetype = pathinfo($_FILES["left_infra_clavicle"]["name"], PATHINFO_EXTENSION);
    $audio_name = "left_infra_clavicle-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
    $config['upload_path'] = './uploads/rural/sc/Resp_front/Left_Infra_Clavicle/';
    $config['file_name'] = $audio_name;
    $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

    $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
    $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
    $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
    $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
    $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

    $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
    $filename = $_FILES['file_i']['name'];

    // $dp="doctor_pro_pic.jpg";
    if(!in_array($filetype, $files))
    {
        $param['type'] = pathinfo($_FILES["left_infra_clavicle"]["name"], PATHINFO_EXTENSION);
        $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
    }
    else
    {  
            $this->upload->initialize($config);
            $this->upload->do_upload('left_infra_clavicle');            		
            $fname = $this->upload->data();
            $left_infra_clavicle=$audio_name;
            $param = "File Uploaded";
            
    } 

    }


    // right_infra_mammary 

    if(!empty($right_infra_mammary)){
       
    $date = date("Y-m-d:H:i:s");
    $this->load->library('upload'); 
    $filetype = pathinfo($_FILES["right_infra_mammary"]["name"], PATHINFO_EXTENSION);
    $audio_name = "right_infra_mammary-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
    $config['upload_path'] = './uploads/rural/sc/Resp_front/Right_Infra_Mammary/';
    $config['file_name'] = $audio_name;
    $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

    $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
    $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
    $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
    $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
    $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

    $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
    $filename = $_FILES['file_i']['name'];

    // $dp="doctor_pro_pic.jpg";
    if(!in_array($filetype, $files))
    {
        $param['type'] = pathinfo($_FILES["right_infra_mammary"]["name"], PATHINFO_EXTENSION);
        $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
    }
    else
    {  
            $this->upload->initialize($config);
            $this->upload->do_upload('right_infra_mammary');            		
            $fname = $this->upload->data();
            $right_infra_mammary=$audio_name;
            $param = "File Uploaded";
            
    } 

    }


    // left_infra_mammary

            if(!empty($left_infra_mammary)){
            
        $date = date("Y-m-d:H:i:s");
        $this->load->library('upload'); 
        $filetype = pathinfo($_FILES["left_infra_mammary"]["name"], PATHINFO_EXTENSION);
        $audio_name = "left_infra_mammary-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
        $config['upload_path'] = './uploads/rural/sc/Resp_front/Left_Infra_Mammary/';
        $config['file_name'] = $audio_name;
        $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

        $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
        $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
        $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
        $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
        $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

        $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
        $filename = $_FILES['file_i']['name'];

        // $dp="doctor_pro_pic.jpg";
        if(!in_array($filetype, $files))
        {
            $param['type'] = pathinfo($_FILES["left_infra_mammary"]["name"], PATHINFO_EXTENSION);
            $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
        }
        else
        {  
                $this->upload->initialize($config);
                $this->upload->do_upload('left_infra_mammary');            		
                $fname = $this->upload->data();
                $left_infra_mammary=$audio_name;
                $param = "File Uploaded";
                
        } 

        }


        if(!empty($left_interscapular_audio)){
            
        $date = date("Y-m-d:H:i:s");
        $this->load->library('upload'); 
        $filetype = pathinfo($_FILES["left_interscapular_audio"]["name"], PATHINFO_EXTENSION);
        $audio_name = "left_interscapular_audio-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
        $config['upload_path'] = './uploads/rural/sc/resp_back/Left_Interscapular/';
        $config['file_name'] = $audio_name;
        $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

        $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
        $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
        $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
        $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
        $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

        $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
        $filename = $_FILES['file_i']['name'];

        // $dp="doctor_pro_pic.jpg";
        if(!in_array($filetype, $files))
        {
            $param['type'] = pathinfo($_FILES["left_interscapular_audio"]["name"], PATHINFO_EXTENSION);
            $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
        }
        else
        {  
                $this->upload->initialize($config);
                $this->upload->do_upload('left_interscapular_audio');            		
                $fname = $this->upload->data();
                $left_interscapular_audio=$audio_name;
                $param = "File Uploaded";
                
        } 

        }
        // left_infra_clavicle

        if(!empty($right_interscapular_audio)){
        
        $date = date("Y-m-d:H:i:s");
        $this->load->library('upload'); 
        $filetype = pathinfo($_FILES["right_interscapular_audio"]["name"], PATHINFO_EXTENSION);
        $audio_name = "right_interscapular_audio-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
        $config['upload_path'] = './uploads/rural/sc/resp_back/Right_Interscapular/';
        $config['file_name'] = $audio_name;
        $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

        $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
        $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
        $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
        $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
        $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

        $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
        $filename = $_FILES['file_i']['name'];

        // $dp="doctor_pro_pic.jpg";
        if(!in_array($filetype, $files))
        {
            $param['type'] = pathinfo($_FILES["right_interscapular_audio"]["name"], PATHINFO_EXTENSION);
            $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
        }
        else
        {  
                $this->upload->initialize($config);
                $this->upload->do_upload('right_interscapular_audio');            		
                $fname = $this->upload->data();
                $right_interscapular_audio=$audio_name;
                $param = "File Uploaded";
                
        } 

        }


    // left_infrascapular_audio 

        if(!empty($left_infrascapular_audio)){
        
        $date = date("Y-m-d:H:i:s");
        $this->load->library('upload'); 
        $filetype = pathinfo($_FILES["left_infrascapular_audio"]["name"], PATHINFO_EXTENSION);
        $audio_name = "left_infrascapular_audio-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
        $config['upload_path'] = './uploads/rural/sc/resp_back/Left_Infrascapular/';
        $config['file_name'] = $audio_name;
        $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

        $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
        $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
        $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
        $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
        $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

        $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
        $filename = $_FILES['file_i']['name'];

        // $dp="doctor_pro_pic.jpg";
        if(!in_array($filetype, $files))
        {
            $param['type'] = pathinfo($_FILES["left_infrascapular_audio"]["name"], PATHINFO_EXTENSION);
            $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
        }
        else
        {  
                $this->upload->initialize($config);
                $this->upload->do_upload('left_infrascapular_audio');            		
                $fname = $this->upload->data();
                $left_infrascapular_audio=$audio_name;
                $param = "File Uploaded";
                
        } 

        }


        // right_infrascapular_audio

        if(!empty($right_infrascapular_audio)){
        
        $date = date("Y-m-d:H:i:s");
        $this->load->library('upload'); 
        $filetype = pathinfo($_FILES["right_infrascapular_audio"]["name"], PATHINFO_EXTENSION);
        $audio_name = "right_infrascapular_audio-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
        $config['upload_path'] = './uploads/rural/sc/resp_back/Right_Infrascapular/';
        $config['file_name'] = $audio_name;
        $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

        $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
        $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
        $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
        $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
        $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

        $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
        $filename = $_FILES['file_i']['name'];

        // $dp="doctor_pro_pic.jpg";
        if(!in_array($filetype, $files))
        {
        $param['type'] = pathinfo($_FILES["right_infrascapular_audio"]["name"], PATHINFO_EXTENSION);
        $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
        }
        else
        {  
            $this->upload->initialize($config);
            $this->upload->do_upload('right_infrascapular_audio');            		
            $fname = $this->upload->data();
            $right_infrascapular_audio=$audio_name;
            $param = "File Uploaded";
            
        } 

        }

        // left_interscapular_audio


    if(!empty($left_interscapular_audio)){
       
    $date = date("Y-m-d:H:i:s");
    $this->load->library('upload'); 
    $filetype = pathinfo($_FILES["left_interscapular_audio"]["name"], PATHINFO_EXTENSION);
    $audio_name = "left_interscapular_audio-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
    $config['upload_path'] = './uploads/rural/sc/resp_back/Left_Interscapular/';
    $config['file_name'] = $audio_name;
    $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

    $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
    $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
    $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
    $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
    $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

    $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
    $filename = $_FILES['file_i']['name'];

    // $dp="doctor_pro_pic.jpg";
    if(!in_array($filetype, $files))
    {
        $param['type'] = pathinfo($_FILES["left_interscapular_audio"]["name"], PATHINFO_EXTENSION);
        $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
    }
    else
    {  
            $this->upload->initialize($config);
            $this->upload->do_upload('left_interscapular_audio');            		
            $fname = $this->upload->data();
            $left_interscapular_audio=$audio_name;
            $param = "File Uploaded";
            
    } 

    }
    // left_infra_clavicle

    if(!empty($right_interscapular_audio)){
       
    $date = date("Y-m-d:H:i:s");
    $this->load->library('upload'); 
    $filetype = pathinfo($_FILES["right_interscapular_audio"]["name"], PATHINFO_EXTENSION);
    $audio_name = "right_interscapular_audio-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
    $config['upload_path'] = './uploads/rural/sc/resp_back/Right_Interscapular/';
    $config['file_name'] = $audio_name;
    $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

    $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
    $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
    $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
    $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
    $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

    $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
    $filename = $_FILES['file_i']['name'];

    // $dp="doctor_pro_pic.jpg";
    if(!in_array($filetype, $files))
    {
        $param['type'] = pathinfo($_FILES["right_interscapular_audio"]["name"], PATHINFO_EXTENSION);
        $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
    }
    else
    {  
            $this->upload->initialize($config);
            $this->upload->do_upload('right_interscapular_audio');            		
            $fname = $this->upload->data();
            $right_interscapular_audio=$audio_name;
            $param = "File Uploaded";
            
    } 

    }


    // left_infrascapular_audio 

    if(!empty($left_infrascapular_audio)){
       
    $date = date("Y-m-d:H:i:s");
    $this->load->library('upload'); 
    $filetype = pathinfo($_FILES["left_infrascapular_audio"]["name"], PATHINFO_EXTENSION);
    $audio_name = "left_infrascapular_audio-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
    $config['upload_path'] = './uploads/rural/sc/resp_back/Left_Infrascapular/';
    $config['file_name'] = $audio_name;
    $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

    $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
    $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
    $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
    $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
    $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

    $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
    $filename = $_FILES['file_i']['name'];

    // $dp="doctor_pro_pic.jpg";
    if(!in_array($filetype, $files))
    {
        $param['type'] = pathinfo($_FILES["left_infrascapular_audio"]["name"], PATHINFO_EXTENSION);
        $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
    }
    else
    {  
            $this->upload->initialize($config);
            $this->upload->do_upload('left_infrascapular_audio');            		
            $fname = $this->upload->data();
            $left_infrascapular_audio=$audio_name;
            $param = "File Uploaded";
            
    } 

    }


    // right_infrascapular_audio

    if(!empty($right_infrascapular_audio)){
       
    $date = date("Y-m-d:H:i:s");
    $this->load->library('upload'); 
    $filetype = pathinfo($_FILES["right_infrascapular_audio"]["name"], PATHINFO_EXTENSION);
    $audio_name = "right_infrascapular_audio-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
    $config['upload_path'] = './uploads/rural/sc/resp_back/Right_Infrascapular/';
    $config['file_name'] = $audio_name;
    $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';

    $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
    $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
    $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
    $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
    $_FILES['file_i']['size'] = $_FILES['file_i']['size'];

    $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
    $filename = $_FILES['file_i']['name'];

    // $dp="doctor_pro_pic.jpg";
    if(!in_array($filetype, $files))
    {
        $param['type'] = pathinfo($_FILES["right_infrascapular_audio"]["name"], PATHINFO_EXTENSION);
        $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
    }
    else
    {  
            $this->upload->initialize($config);
            $this->upload->do_upload('right_infrascapular_audio');            		
            $fname = $this->upload->data();
            $right_infrascapular_audio=$audio_name;
            $param = "File Uploaded";
            
    } 

    }

    $tabledate= date("d-m-Y:   h:i a");

    $this->db3->query("INSERT INTO sc_respiration (patient_id,rmp_id,left_interscapular_audio,right_interscapular_audio,left_infrascapular_audio,right_infrascapular_audio,right_infra_clavicle_audio,left_infra_clavicle_audio,right_infra_mammary_audio,left_infra_mammary_audio,created_by,created_date) VALUES ('$patient_id','$rmp_id','$left_interscapular_audio','$right_interscapular_audio','$left_infrascapular_audio','$right_infrascapular_audio','$right_infra_clavicle','$left_infra_clavicle','$right_infra_mammary','$left_infra_mammary','$rmp_id','$tabledate')");
            
    $this->response(array('code' => '200', 'message' => 'patient Systematic Examination  respiration  sucessfully created'), 200);

    
    }
    else
    {
        $this->response(array('code' => '404', 'message' => 'enter patient_id and rmp_id'), 404); 
    }

    }

    //  respiration post api completed 2304 line total 8 audios uploaded  


    // total respiration get api 8 audio get

    public function respirationget_post(){
        extract($_POST);
        if($patient_id!="" and $rmp_id!=""){
        $respa=$this->db3->query("select * from sc_respiration where rmp_id='".$rmp_id."' and patient_id='".$patient_id."'")->result();
        if(!empty($respa)){
        
            $a=0;
        foreach($respa as $res)
        {
            $data['respiration'][$a]['sc_respiration_id']=$res->sc_respiration_id;
            $data['respiration'][$a]['rmp_id']=$res->rmp_id;
            $data['respiration'][$a]['patient_id']=$res->patient_id;
            $data['respiration'][$a]['Right_Infra_Mammary']='http://devumdaa.in/dev/uploads/rural/sc/Resp_front/Right_Infra_Mammary/'.$res->right_infra_mammary_audio;
            $data['respiration'][$a]['Right_Infra_Clavicle']='http://devumdaa.in/dev/uploads/rural/sc/Resp_front/Right_Infra_Clavicle/'.$res->right_infra_clavicle_audio;
            $data['respiration'][$a]['Left_Infra_Mammary']='http://devumdaa.in/dev/uploads/rural/sc/Resp_front/Left_Infra_Mammary/'.$res->left_infra_mammary_audio;
            $data['respiration'][$a]['Left_Infra_Clavicle']='http://devumdaa.in/dev/uploads/rural/sc/Resp_front/Left_Infra_Clavicle/'.$res->left_infra_clavicle_audio;
            $data['respiration'][$a]['left_interscapular_audio']='http://devumdaa.in/dev/uploads/rural/sc/resp_back/Left_Interscapular/'.$res->left_interscapular_audio;
            $data['respiration'][$a]['right_interscapular_audio']='http://devumdaa.in/dev/uploads/rural/sc/resp_back/Right_Interscapular/'.$res->right_interscapular_audio;
            $data['respiration'][$a]['left_infrascapular_audio']='http://devumdaa.in/dev/uploads/rural/sc/resp_back/Left_Infrascapular/'.$res->left_infrascapular_audio;
            $data['respiration'][$a]['right_infrascapular_audio']='http://devumdaa.in/dev/uploads/rural/sc/resp_back/Right_Infrascapular/'.$res->right_infrascapular_audio;
            $data['respiration'][$a]['modified_by']=$res->modified_by;
            $data['respiration'][$a]['created_by']=$res->created_by;
            $data['respiration'][$a]['created_date']=$res->created_date;
            $data['respiration'][$a]['modified_date']=$res->modified_date;
            $a++;
        }
            
        $this->response(array('code' => '200','message'=>'success','sc_respiration'=>$data['respiration']));
        }
        
        else
        {
        
            $this->response(array('code'=>'404','message'=>'no data fount'));
        }

        }
        else
        {
        $this->response(array('code'=>'404','message'=>'enter patient_id and rmp_id'));  
        }
    }


    // abdominal_post api 


    public function abdominal_post(){

    extract($_POST);
    if($patient_id !="" and $rmp_id !=""){
        
        

        if(!empty($abdominal)){
           
            $date = date("Y-m-d:H:i:s");
            $this->load->library('upload'); 
            $filetype = pathinfo($_FILES["abdominal"]["name"], PATHINFO_EXTENSION);
            $audio_name = "abdominal-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
            $config['upload_path'] = './uploads/rural/sc/abdominal/';
            $config['file_name'] = $audio_name;
            $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';
        
            $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
            $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
            $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
            $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
            $_FILES['file_i']['size'] = $_FILES['file_i']['size'];
        
            $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
            $filename = $_FILES['file_i']['name'];
        
            // $dp="doctor_pro_pic.jpg";
            if(!in_array($filetype, $files))
            {
                $param['type'] = pathinfo($_FILES["abdominal"]["name"], PATHINFO_EXTENSION);
                $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
            }
            else
            {  
                    $this->upload->initialize($config);
                    $this->upload->do_upload('abdominal');            		
                    $fname = $this->upload->data();
                    $param = "File Uploaded";
                    
            } 
        
        }

        $tabledate= date("d-m-Y: h:i a");

        $this->db3->query("INSERT INTO sc_abdominal (patient_id,rmp_id,abdominal_comment,abdominal_audio,created_by,created_date) VALUES ('$patient_id','$rmp_id','$abdominal_comment','$audio_name','$rmp_id','$tabledate')");
        
        $this->response(array('code' => '200', 'message' => 'Abdominal examination is sucessfully created'), 200);
        

    }else{

        $this->response(array('code'=>'404','message'=>'enter patient_id and rmp_id'));

    }

}

// cns post api
public function cns_post(){

    extract($_POST);
    if($patient_id !="" and $rmp_id !=""){
        
        $cns=$_FILES['cns']['name'];

        if(!empty($cns)){
           
            $date = date("Y-m-d:H:i:s");
            $this->load->library('upload'); 
            $filetype = pathinfo($_FILES["cns"]["name"], PATHINFO_EXTENSION);
            $audio_name = "cns-".$patient_id."-".$rmp_id."-".$date.".".$filetype;
            $config['upload_path'] = './uploads/rural/sc/cns/';
            $config['file_name'] = $audio_name;
            $config['allowed_types'] = 'mp3|MP3|mp4|MP4|WAV|wav|3GP|3gp|mpeg|MPEG';
        
            $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
            $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
            $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
            $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
            $_FILES['file_i']['size'] = $_FILES['file_i']['size'];
        
            $files = ['mp3','MP3','mp4','MP4','WAV','wav','3gp','3GP','mpeg','MPEG'];
            $filename = $_FILES['file_i']['name'];
        
            // $dp="doctor_pro_pic.jpg";
            if(!in_array($filetype, $files))
            {
                $param['type'] = pathinfo($_FILES["cns"]["name"], PATHINFO_EXTENSION);
                $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
            }
            else
            {  
                    $this->upload->initialize($config);
                    $this->upload->do_upload('cns');            		
                    $fname = $this->upload->data();
                    $param = "File Uploaded";
                    
            } 
        
        }

        $tabledate= date("d-m-Y: h:i a");

        $this->db3->query("INSERT INTO sc_cns (patient_id,rmp_id,cns_comment,cns_audio,created_by,created_date) VALUES ('$patient_id','$rmp_id','$cns_comment','$audio_name','$rmp_id','$tabledate')");
        
        $this->response(array('code' => '200', 'message' => 'Cns examination is sucessfully created'), 200);
        

    }else{

        $this->response(array('code'=>'404','message'=>'enter patient_id and rmp_id'));

    }

}



// abdominal get api

public function abdominalget_post(){
    extract($_POST);

    if($patient_id!="" and $rmp_id!=""){

    $abdominal=$this->db3->query("select * from sc_abdominal where rmp_id='".$rmp_id."' and patient_id='".$patient_id."'")->result();

    if(!empty($abdominal)){
        $a=0;
        foreach($abdominal as $abd){


            $data['abdominal'][$a]['abdominal_id']=$abd->abdominal_id;
            $data['abdominal'][$a]['rmp_id']=$abd->rmp_id;
            $data['abdominal'][$a]['patient_id']=$abd->patient_id;
            $data['abdominal'][$a]['abdominal_comment']=$abd->abdominal_comment;
            $data['abdominal'][$a]['abdominal_audio']='http://devumdaa.in/dev/uploads/rural/sc/abdominal/'.$abd->abdominal_audio;
            $data['abdominal'][$a]['modified_by']=$abd->modified_by;
            $data['abdominal'][$a]['created_by']=$abd->created_by;
            $data['abdominal'][$a]['created_date']=$abd->created_date;
            $data['abdominal'][$a]['modified_date']=$abd->modified_date;

            $a++;
        }

        $this->response(array('code' => '200','message'=>'success','abdominal'=>$data['abdominal']));
       
    }
    else
    {
    $this->response(array('code'=>'404','message'=>'no data fount'));
    }
    
}
else
{
$this->response(array('code'=>'404','message'=>'enter patient_id and rmp_id'));  
}
}


// sc cns get api


public function cnsget_post(){
    extract($_POST);

    if($patient_id!="" and $rmp_id!=""){

    $cns=$this->db3->query("select * from sc_cns where rmp_id='".$rmp_id."' and patient_id='".$patient_id."'")->result();

    if(!empty($cns)){
        $a=0;
        foreach($cns as $ns){


            $data['cns'][$a]['cns_id']=$ns->cns_id;
            $data['cns'][$a]['rmp_id']=$ns->rmp_id;
            $data['cns'][$a]['patient_id']=$ns->patient_id;
            $data['cns'][$a]['cns_comment']=$ns->cns_comment;
            $data['cns'][$a]['cns_audio']='http://devumdaa.in/dev/uploads/rural/sc/cns/'.$ns->cns_audio;
            $data['cns'][$a]['modified_by']=$ns->modified_by;
            $data['cns'][$a]['created_by']=$ns->created_by;
            $data['cns'][$a]['created_date']=$ns->created_date;
            $data['cns'][$a]['modified_date']=$ns->modified_date;

            $a++;
        }

        $this->response(array('code' => '200','message'=>'success','cns'=>$data['cns']));
       
    }
    else
    {
    $this->response(array('code'=>'404','message'=>'no data fount'));
    }
    
}
else
{
$this->response(array('code'=>'404','message'=>'enter patient_id and rmp_id'));  
}
}

// patient_search api


public function patientsearch_get($rmp_id,$search){

    $result=$this->db3->query("SELECT * FROM `rural_rmp_patients` WHERE rmp_id='".$rmp_id."' AND patient_name LIKE '%".$search."%' ORDER BY `patient_id` ASC LIMIT 20")->result();
    
    if(!empty($result)){

        $a=0;
    foreach($result as $res){

        $data['patient_search'][$a]['patient_id']=$res->patient_id;
        $data['patient_search'][$a]['rmp_id']=$res->rmp_id;
        $data['patient_search'][$a]['patient_name']=$res->patient_name;
        $data['patient_search'][$a]['patient_gender']=$res->patient_gender;
        $data['patient_search'][$a]['patient_age']=$res->patient_age;
        $data['patient_search'][$a]['patient_mobile_number']=$res->patient_mobile_number;
        $data['patient_search'][$a]['patient_id_proof']='https://www.devumdaa.in/dev/uploads/rural/patient_id_pic/'.$res->patient_id_proof;
        $data['patient_search'][$a]['patient_profile_pic']='https://www.devumdaa.in/dev/uploads/rural/patient_profile_pic/'.$res->patient_profile_pic;
        $data['patient_search'][$a]['patient_location']=$res->patient_location;
        $data['patient_search'][$a]['modified_by']=$res->modified_by;
        $data['patient_search'][$a]['created_by']=$res->created_by;
        $data['patient_search'][$a]['created_date']=$res->created_date;
        $data['patient_search'][$a]['modified_date']=$res->modified_date;

        $a++;
        }

        $this->response(array('code'=>'200','patients_search_list'=>$data['patient_search']));  

        }else
        {
            $this->response(array('code'=>'404','message'=>'no patients found'));
        }
    }

    public function editprofile_post(){
        
        extract($_POST);

        $modified_date = date("d-m-Y: h:i a");
        
        // $profile=$_FILES['updated_profile_pic']['name'];

        if($profile_pic =="")
        {

        $date = date("Y-m-d:H:i:s");
        $this->load->library('upload'); 
        $filetype = pathinfo($_FILES["updated_profile_pic"]["name"], PATHINFO_EXTENSION);
        $updated_profile_pic = "doctor_pro_pic-".$date.".".$filetype;
        $config['upload_path'] = './uploads/rural/doc_profile_pic/';
        $config['file_name'] = $updated_profile_pic;
        $config['allowed_types'] = 'jpg|JPG|png|PNG|jpeg|JPEG';
                                    
        $_FILES['file_i']['name'] = $_FILES['file_i']['name'];
        $_FILES['file_i']['type'] = $_FILES['file_i']['type'];
        $_FILES['file_i']['tmp_name'] = $_FILES['file_i']['tmp_name'];
        $_FILES['file_i']['error'] = $_FILES['file_i']['error'];
        $_FILES['file_i']['size'] = $_FILES['file_i']['size'];
                                        
        $files = ['jpg','png','jpeg'];
        $filename = $_FILES['file_i']['name'];

        // $dp="doctor_pro_pic.jpg";
        if(!in_array($filetype, $files))
        {
        $param['type'] = pathinfo($_FILES["updated_profile_pic"]["name"], PATHINFO_EXTENSION);
        $this->response(array('code'=>'201','message'=>'Wrong File Type','result'=>$param));
        }
            else
        {  
            $this->upload->initialize($config);
            $this->upload->do_upload('updated_profile_pic');            		
            $fname = $this->upload->data();
            $profile_pic=$updated_profile_pic;
            $param = "File Uploaded";
        }
                                                               //  $this->db3->query("UPDATE rural_rmp_registration SET profile_pic='".$profile_pic."' WHERE rmp_id=1");
 
                                                                // $this->response(array('code'=>'200','message'=>'File Uploaded','result'=>$param)); 
 
        }

        if($rmp_name !="" and $rmp_gender !="" and $rmp_age !="" and $rmp_email !="" and $rmp_phone !=""  and $rmp_clinic_name !="" and $rmp_city !="" and $profile_pic !="") 
        {

            $this->db3->query("UPDATE rural_rmp_registration SET rmp_name='".$rmp_name."',rmp_gender='".$rmp_gender."',rmp_age='".$rmp_age."',rmp_email='".$rmp_email."',rmp_phone='".$rmp_phone."', rmp_clinic_name='".$rmp_clinic_name."',rmp_city='".$rmp_city."',profile_pic='".$profile_pic."',modified_date='".$modified_date."' WHERE rmp_id='".$rmp_id."'");
        
            $this->response(array('code'=>'200','message'=>'profile successfully updated'));

        }
        else
        {

            $this->response(array('code'=>'404','message'=>'enter total details'));
        }

        // $this->db3->query("UPDATE rural_rmp_registration SET rmp_name='".$rmp_name."',rmp_gender='".$rmp_gender."',rmp_age='".$rmp_age."',rmp_email='".$rmp_email."',rmp_phone='".$rmp_phone."',rmp_password='".$password."',rmp_clinic_name='".$rmp_clinic_name."',rmp_city='".$rmp_city."',profile_pic='".$profile_pic."',access_token='".$access_token."',modified_date='".$modified_date."' WHERE rmp_id='".$rmp_id."'");

    }

    //  this api is used to add the wallet cash for rmp

    public function addcash_post(){
    
        extract($_POST);
        
        $rmp_vald=$this->db3->query("select * from rural_rmp_registration where rmp_id='".$rmp_id."'")->result();

        if(!empty($rmp_vald)){

            $date = date("d-m-Y: h:i a");
            
            $transaction=$this->db3->query("INSERT INTO payment_history (rmp_id,patient_id,doctor_id,payment_status,amount,payment_date,created_date) VALUES ('$rmp_id','$patient_id','$doctor_id','$payment_status','$amount','$date','$date')");
            
            $balance=$this->db3->query("SELECT SUM(amount) as bal FROM payment_history WHERE rmp_id='".$rmp_id."'")->row();            

            $wallet_cash=$balance->bal;

            $walletadd=$this->db3->query("UPDATE rural_rmp_registration SET wallet_cash='".$wallet_cash."' where rmp_id='".$rmp_id."'");

            $this->response(array('code'=> '200','message'=>'cash successfully added'));

        }else{
        
            $this->response(array('code'=> '404','message'=>'this rmp_id is not valid'));
        }
        
        
    }

    public function paymenthistory_post(){
        
        extract($_POST);
        
        $rmp_vald=$this->db3->query("select * from rural_rmp_registration where rmp_id='".$rmp_id."'")->result();

        if(!empty($rmp_vald))
        {
            $history=$this->db3->query("SELECT rural_doctor_registration.doctor_name,rural_doctor_registration.doctor_hospital,rural_doctor_registration.doctor_phone_number,payment_history.payment_status,payment_history.amount,payment_history.payment_date FROM payment_history RIGHT JOIN rural_doctor_registration ON payment_history.doctor_id = rural_doctor_registration.doctor_id WHERE payment_history.rmp_id='".$rmp_id."' ORDER BY payment_history.payment_date ASC")->result();
            // $history=$this->db3->query("SELECT * FROM payment_history WHERE rmp_id='".$rmp_id."'")->result();
            $rmp_wallet=$this->db3->query("SELECT * FROM rural_rmp_registration WHERE rmp_id='".$rmp_id."'")->row();
            
            $data['wallet_cash']=$rmp_wallet->rmp_wallet;

            $cash=$rmp_wallet->wallet_cash;
            $data['wallet_cash']=$cash;
            

            $a=0;
            foreach($history as $hist)
            {
                $doc_details=$this->db3->query("SELECT * FROM rural_doctor_registration WHERE doctor_id='".$hist->doctor_id."'")->result();
           
                   
                    $data['transaction_history'][$a]['doctor_name']=$hist->doctor_name;
                    $data['transaction_history'][$a]['doctor_hospital']=$hist->doctor_hospital;
                    $data['transaction_history'][$a]['doctor_phone_number']=$hist->doctor_phone_number;
                    $data['transaction_history'][$a]['payment_status']=$hist->payment_status;
                    $data['transaction_history'][$a]['amount']=$hist->amount;
                    $data['transaction_history'][$a]['payment_date']=$hist->payment_date;
                    $a++;
                
            }
            
            $this->response(array('code' => '200', 'message' =>$data));
            
              
        }

        else
        {
            $this->response(array('code' => '404', 'message' => 'enter patient_id and rmp_id')); 
        }


    }


    public function requestcash_post(){
        extract($_POST);
        $date = date("d-m-Y: h:i a");

        if($request_amount !="")

        {

        $payment_request=$this->db3->query("INSERT INTO cash_request (rmp_id,request_amount,request_status,created_by,created_date) VALUES ('$rmp_id','$request_amount','pending','$rmp_id','$date')");

                $this->response(array('code'=>'200','message'=>'your request created successfully'));
        }
        else
        {
                
        }
    }


    public function walletstatus_post(){

            extract($_POST);
           
            $cash=$this->db3->query("SELECT wallet_cash FROM rural_rmp_registration WHERE rmp_id='".$rmp_id."'")->row();

            $data['current_wallet_amount']=$cash->wallet_cash;

            $request=$this->db3->query("SELECT * FROM cash_request WHERE rmp_id='".$rmp_id."' ORDER BY cash_request.cash_request_id DESC LIMIT 1")->row();
            
            $status= $request->request_status;

            if($status=="pending"){
                
                $data['request_amount']=$request->request_amount;
                $data['request_status']=$request->request_status;
                $data['request_date']=$request->created_date;

                $this->response(array('code'=>'200','response'=> $data));  

            }else{

                $this->response(array('code'=>'202','message'=>'there is no pending add cash request','response'=>$data));

            }      
        
    }

/*

    patient summary api

  this api get total report details of the patient


*/ 


    public function patientsummary_post(){
        
        extract($_POST);

        $vitals=$this->db3->query("SELECT * FROM rural_rmp_patient_vitals WHERE patient_id='".$patient_id."' AND rmp_id='".$rmp_id."' ORDER BY rural_rmp_patient_vitals.rmp_patient_vitals_id DESC LIMIT 1")->row();

        $gpe=$this->db3->query("SELECT * FROM patient_gpe_images WHERE patient_id='".$patient_id."' AND rmp_id='".$rmp_id."' ORDER BY patient_gpe_images.patient_gpe_img_id DESC LIMIT 1")->row();

        
        $gpe_images['rmp_id']=$gpe->rmp_id;
        $gpe_images['patient_id']=$gpe->patient_id;
        $gpe_images['patient_oral_cavity']='http://devumdaa.in/dev/uploads/rural/Gpe_files/patient_oral_cavity/'.$gpe->patient_oral_cavity;
        $gpe_images['patient_neck']='http://devumdaa.in/dev/uploads/rural/Gpe_files/patient_neck/'.$gpe->patient_neck;
        $gpe_images['patient_dorsal_hand']='http://devumdaa.in/dev/uploads/rural/Gpe_files/patient_dorsal_hand/'.$gpe->patient_dorsal_hand;
        $gpe_images['patient_palms']='http://devumdaa.in/dev/uploads/rural/Gpe_files/patient_palms/'.$gpe->patient_palms;
        $gpe_images['patient_leg']='http://devumdaa.in/dev/uploads/rural/Gpe_files/patient_leg/'.$gpe->patient_leg;
        $gpe_images['patient_sclera']='http://devumdaa.in/dev/uploads/rural/Gpe_files/patient_sclera/'.$gpe->patient_sclera;
        $gpe_images['patient_palpebral_conjunctiva']='http://devumdaa.in/dev/uploads/rural/Gpe_files/patient_palpebral_conjunctiva/'.$gpe->patient_palpebral_conjunctiva;
        $gpe_images['modified_by']=$gpe->modified_by;
        $gpe_images['created_by']=$gpe->created_by;
        $gpe_images['created_date']=$gpe->created_date;
        $gpe_images['modified_date']=$gpe->modified_date;
        

            $sc_abdominal=$this->db3->query("SELECT * FROM sc_abdominal WHERE patient_id='".$patient_id."' AND rmp_id='".$rmp_id."' ORDER BY sc_abdominal.abdominal_id DESC LIMIT 1")->row();
            
            $abdominal['rmp_id']=$sc_abdominal->rmp_id;
            $abdominal['patient_id']=$sc_abdominal->patient_id;
            $abdominal['abdominal_comment']=$sc_abdominal->abdominal_comment;
            $abdominal['abdominal_audio']='http://devumdaa.in/dev/uploads/rural/sc/abdominal/'.$sc_abdominal->abdominal_audio;
            $abdominal['modified_by']=$sc_abdominal->modified_by;
            $abdominal['created_by']=$sc_abdominal->created_by;
            $abdominal['created_date']=$sc_abdominal->created_date;
            $abdominal['modified_date']=$sc_abdominal->modified_date;
        
            $sc_cardio=$this->db3->query("SELECT * FROM sc_cardio WHERE patient_id='".$patient_id."' AND rmp_id='".$rmp_id."' ORDER BY sc_cardio.sc_cardio_id DESC LIMIT 1")->row();

            $cardio['rmp_id']=$sc_cardio->rmp_id;
            $cardio['patient_id']=$sc_cardio->patient_id;
            $cardio['mitral_audio']='http://devumdaa.in/dev/uploads/rural/sc/Cardio/Mitral/'.$sc_cardio->mitral_audio;
            $cardio['arotic_audio']='http://devumdaa.in/dev/uploads/rural/sc/Cardio/Arotic/'.$sc_cardio->arotic_audio;
            $cardio['pulmonary_audio']='http://devumdaa.in/dev/uploads/rural/sc/Cardio/Pulmonary/'.$sc_cardio->pulmonary_audio;
            $cardio['tricuspid_audio']='http://devumdaa.in/dev/uploads/rural/sc/Cardio/Tricuspid/'.$sc_cardio->tricuspid_audio;
            $cardio['modified_by']=$sc_cardio->modified_by;
            $cardio['created_by']=$sc_cardio->created_by;
            $cardio['created_date']=$sc_cardio->created_date;
            $cardio['modified_date']=$sc_cardio->modified_date;

        $sc_cns=$this->db3->query("SELECT * FROM sc_cns WHERE patient_id='".$patient_id."' AND rmp_id='".$rmp_id."' ORDER BY sc_cns.cns_id DESC LIMIT 1")->row();

        $cns['rmp_id']=$sc_cns->rmp_id;
        $cns['patient_id']=$sc_cns->patient_id;
        $cns['cns_comment']=$sc_cns->cns_comment;
        $cns['cns_audio']='http://devumdaa.in/dev/uploads/rural/sc/abdominal_cns/'.$sc_cns->cns_audio;
        $cns['modified_by']=$sc_cns->modified_by;
        $cns['created_by']=$sc_cns->created_by;
        $cns['created_date']=$sc_cns->created_date;
        $cns['modified_date']=$sc_cns->modified_date;

        
        $sc_respiration=$this->db3->query("SELECT * FROM sc_respiration WHERE patient_id='".$patient_id."' AND rmp_id='".$rmp_id."' ORDER BY sc_respiration.sc_respiration_id DESC LIMIT 1")->row();

        $respiration['rmp_id']=$sc_respiration->rmp_id;
        $respiration['patient_id']=$sc_respiration->patient_id;
        $respiration['right_infra_clavicle_audio']='http://devumdaa.in/dev/uploads/rural/sc/Resp_front/Right_Infra_Clavicle/'.$sc_respiration->right_infra_clavicle_audio;
        $respiration['left_infra_clavicle_audio']='http://devumdaa.in/dev/uploads/rural/sc/Resp_front/Left_Infra_Clavicle/'.$sc_respiration->left_infra_clavicle_audio;
        $respiration['right_infra_mammary_audio']='http://devumdaa.in/dev/uploads/rural/sc/Resp_front/Right_Infra_Mammary/'.$sc_respiration->right_infra_mammary_audio;
        $respiration['left_infra_mammary_audio']='http://devumdaa.in/dev/uploads/rural/sc/Resp_front/Left_Infra_Mammary/'.$sc_respiration->left_infra_mammary_audio;
        $respiration['left_interscapular_audio']='http://devumdaa.in/dev/uploads/rural/sc/resp_back/Left_Interscapular/'.$sc_respiration->left_interscapular_audio;
        $respiration['right_interscapular_audio']='http://devumdaa.in/dev/uploads/rural/sc/resp_back/Right_Interscapular/'.$sc_respiration->right_interscapular_audio;
        $respiration['left_infrascapular_audio']='http://devumdaa.in/dev/uploads/rural/sc/resp_back/Left_Infrascapular/'.$sc_respiration->left_infrascapular_audio;
        $respiration['right_infrascapular_audio']='http://devumdaa.in/dev/uploads/rural/sc/resp_back/Right_Infrascapular/'.$sc_respiration->right_infrascapular_audio;
        $respiration['modified_by']=$sc_respiration->modified_by;
        $respiration['created_by']=$sc_respiration->created_by;
        $respiration['created_date']=$sc_respiration->created_date;
        $respiration['modified_date']=$sc_respiration->modified_date;


        $symptoms=$this->db3->query("SELECT * FROM patient_symptoms WHERE patient_id='".$patient_id."' AND rmp_id='".$rmp_id."' ORDER by patient_symptoms.patient_symptom_id DESC LIMIT 1")->row();

        $patient_symptoms['rmp_id']=$symptoms->rmp_id;
        $patient_symptoms['patient_id']=$symptoms->patient_id;
        $patient_symptoms['describe_symptom_text']=$symptoms->describe_symptom_text;
        $patient_symptoms['symptoms_audio']='https://www.devumdaa.in/dev/uploads/rural/patient_symptoms/patient_symptoms_audio/'.$symptoms->symptoms_audio;
        $patient_symptoms['symptoms_duration']=$symptoms->symptoms_duration;
        $patient_symptoms['symptom_range']=$symptoms->symptom_range;
        $patient_symptoms['modified_by']=$symptoms->modified_by;
        $patient_symptoms['created_by']=$symptoms->created_by;
        $patient_symptoms['created_date']=$symptoms->created_date;
        $patient_symptoms['modified_date']=$symptoms->modified_date;


        $this->response(array('code'=>'200','patient_summary'=>array('vitals'=> $vitals,'gpe'=>$gpe_images,'sc_abdominal'=>$abdominal,'sc_cardio'=>$cardio,'sc_cns'=>$cns,'sc_respiration'=>$respiration,'symptoms'=>$patient_symptoms)));

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
 
    public function totalgeneraldoctors_post(){

                    $total_general=$this->db3->query("select * from rural_doctor_registration where doctor_type='general' order by doctor_id")->result();
                         $a=0; 
                     foreach($total_general as $doc){
                        
                    $languages = $this->db3->query("SELECT GROUP_CONCAT(l.Language) as doclanguages FROM `doctor_languages` dl,Languages l where l.Languages_id=dl.languages_id and dl.doctor_id='".$doc->doctor_id."'")->row();    

                    $data['total_general_doctors'][$a]['doctor_id']=$doc->doctor_id;
                    $data['total_general_doctors'][$a]['doctor_name']=$doc->doctor_name;
                    $data['total_general_doctors'][$a]['doctor_age']=$doc->doctor_age;
                    $data['total_general_doctors'][$a]['doctor_gender']=$doc->doctor_gender;
                    $data['total_general_doctors'][$a]['doctor_email']=$doc->doctor_email;
                    $data['total_general_doctors'][$a]['doctor_phone_number']=$doc->doctor_phone_number;
                    $data['total_general_doctors'][$a]['doctor_hospital']=$doc->doctor_hospital;
                    $data['total_general_doctors'][$a]['doctor_city']=$doc->doctor_city;
                    $data['total_general_doctors'][$a]['doctor_profile_pic']=$doc->doctor_profile_pic;
                    $data['total_general_doctors'][$a]['appointment_charge']=$doc->appointment_charge;
                    $data['total_general_doctors'][$a]['doctor_experience']=$doc->doctor_experience;
                    $data['total_general_doctors'][$a]['languages']=$languages->doclanguages;

                    $a++;

                    }

                    $this->response(array('code'=>'200','total_general_doctors' =>  $data['total_general_doctors']));

                }

//  this api is used to display gender wise doctors

    public function generaldoctorsgender_post(){

        extract($_POST);
        if(!empty($doctor_gender)){

                $gender_doctor=$this->db3->query("select * from rural_doctor_registration where doctor_type='general' and doctor_gender='".$doctor_gender."'")->result();

                if(empty(!$gender_doctor)){

                        $a=0;

                        foreach($gender_doctor as $doc){

                            $languages = $this->db3->query("SELECT GROUP_CONCAT(l.Language) as doclanguages FROM `doctor_languages` dl,Languages l where l.Languages_id=dl.languages_id and dl.doctor_id='".$doc->doctor_id."'")->row();    


                    $data['total_general_doctors'][$a]['doctor_id']=$doc->doctor_id;
                    $data['total_general_doctors'][$a]['doctor_name']=$doc->doctor_name;
                    $data['total_general_doctors'][$a]['doctor_age']=$doc->doctor_age;
                    $data['total_general_doctors'][$a]['doctor_gender']=$doc->doctor_gender;
                    $data['total_general_doctors'][$a]['doctor_email']=$doc->doctor_email;
                    $data['total_general_doctors'][$a]['doctor_phone_number']=$doc->doctor_phone_number;
                    $data['total_general_doctors'][$a]['doctor_hospital']=$doc->doctor_hospital;
                    $data['total_general_doctors'][$a]['doctor_city']=$doc->doctor_city;
                    $data['total_general_doctors'][$a]['doctor_profile_pic']=$doc->doctor_profile_pic;
                    $data['total_general_doctors'][$a]['appointment_charge']=$doc->appointment_charge;
                    $data['total_general_doctors'][$a]['doctor_experience']=$doc->doctor_experience;
                    $data['total_general_doctors'][$a]['doctor_languages']=$languages->doclanguages;
                    $a++;
                    }
                    $this->response(array('code'=>'200','total_'.$doctor_gender.'_doctors' =>  $data['total_general_doctors']));


                }else{

                    $this->response(array('code'=>'404','response' =>'there is no '.$doctor_gender.' doctors' ));
                    }
                

                 }

                else
                {
                    $this->response(array('code'=>'404','response' =>'enter doctor_gender' ));
                }      
        
     }


     public function generaldoctorlanguage_post(){

                extract($_POST);

                // $mater=$this->db3->query("SELECT * FROM rural_doctor_registration,Languages,doctor_languages WHERE doctor_languages.Languages_id=Languages.Languages_id AND rural_doctor_registration.doctor_id=doctor_languages.doctor_id AND Languages.Languages_id='.$language_id.'")->result();
                
                $lang_gend=$this->db3->query("SELECT * FROM rural_doctor_registration,Languages,doctor_languages WHERE doctor_languages.Languages_id=Languages.Languages_id AND rural_doctor_registration.doctor_id=doctor_languages.doctor_id AND Languages.Languages_id='".$language_id."' AND rural_doctor_registration.doctor_gender='".$doctor_gender."'")->result();
                
                $a=0;

                foreach($lang_gend as $ma){

                    $data['general_doctor_language'][$a]['doctor_name']=$ma->doctor_name;
                    $data['general_doctor_language'][$a]['doctor_age']=$ma->doctor_age;
                    $data['general_doctor_language'][$a]['doctor_gender']=$ma->doctor_gender;
                    $data['general_doctor_language'][$a]['doctor_phone_number']=$ma->doctor_phone_number;
                    $data['general_doctor_language'][$a]['doctor_hospital']=$ma->doctor_hospital;
                    $data['general_doctor_language'][$a]['doctor_profile_pic']=$ma->doctor_profile_pic;
                    $data['general_doctor_language'][$a]['doctor_type']=$ma->doctor_type;
                    $data['general_doctor_language'][$a]['doctor_experience']=$ma->doctor_experience;
                    
                    $a++;

                }

                $this->response(array('code'=>'200','total_departments' =>  $data['general_doctor_language']));
            }


    //  this api is used to display total departments
    public function totaldepartments_get(){

        $departments=$this->db3->query("CALL department();")->result();                                                                   
        $a=0;
        foreach($departments as $dep){
            $data['total_departments'][$a]['department_id']=$dep->department_id;
            $data['total_departments'][$a]['department_name']=$dep->department_name;
            $data['total_departments'][$a]['department_icon']='http://devumdaa.in/dev/uploads/rural/department_icons/'.$dep->department_icon;
            $a++;
        }

        $this->response(array('code'=>'200','total_departments' => $data['total_departments']));

    }

    public function totallanguages_get(){
            
        $languages=$this->db3->query("SELECT Languages_id,Language FROM `Languages`")->result();
    
        $this->response(array('code'=>'200','languages'=> $languages)); 

    }

    /**
     * this api is used to filter the general doctor filter **/ 

public function generaldoctorfilters_post(){
        
        extract($_POST); 
        
                if($doctor_gender != "")
                {
                    $genderQ = " AND doctor_gender = '".$doctor_gender."'";
                }
                if($doctor_language !="")
                {
                    $doct_lang="AND rd.doctor_id=dl.doctor_id AND dl.languages_id IN(".$doctor_language.")";

                }
   

    $filter_reslut=$this->db3->query("SELECT * FROM rural_doctor_registration rd,doctor_languages dl WHERE rd.doctor_type='general'$genderQ $doct_lang")->result();

    if(!empty($filter_reslut)){
    
                $a=0;
                
    foreach($filter_reslut as $res){

        $languages = $this->db3->query("SELECT GROUP_CONCAT(l.Language) as doclanguages FROM `doctor_languages` dl,Languages l where l.Languages_id=dl.languages_id and dl.doctor_id='".$res->doctor_id."'")->row();

        $data['filter_result'][$a]['doctor_id']=$res->doctor_id;
        $data['filter_result'][$a]['doctor_name']=$res->doctor_name;
        $data['filter_result'][$a]['doctor_age']=$res->doctor_age;
        $data['filter_result'][$a]['doctor_gender']=$res->doctor_gender;
        $data['filter_result'][$a]['doctor_email']=$res->doctor_email;
        $data['filter_result'][$a]['doctor_phone_number']=$res->doctor_phone_number;
        $data['filter_result'][$a]['doctor_hospital']=$res->doctor_hospital;
        $data['filter_result'][$a]['doctor_city']=$res->doctor_city;
        $data['filter_result'][$a]['doctor_profile_pic']='http://devumdaa.in/dev/uploads/rural/test/'.$res->doctor_profile_pic;  #pic
        $data['filter_result'][$a]['doctor_type']=$res->doctor_type;
        $data['filter_result'][$a]['doctor_experience']=$res->doctor_experience;
        $data['filter_result'][$a]['languages']=$languages->doclanguages;
        
        $a++;
    }

        $this->response(array('code'=>'200','response'=> $data['filter_result'])); 

    }
        else
        {
            $this->response(array('code'=>'200','response'=> 'there is no doctors'));
        }

}    

// doctors specialist api with filters

    /**
    this api is used to filter the specialist wise 
     **/
public function specialistfilter_post(){

    extract($_POST);
    $mater="";
    if($doctor_gender != "")
    {
        $genderQ = " AND doctor_gender = '".$doctor_gender."'";
    }
    if($doctor_language !="")
    {
        $doct_lang="AND rd.doctor_id=dl.doctor_id AND dl.languages_id IN(".$doctor_language.")";

    }

    $filter_reslut=$this->db3->query("SELECT * FROM rural_doctor_registration rd,doctor_languages dl WHERE rd.doctor_type='".$doctor_type."' $genderQ $doct_lang")->result();

    if(!empty($filter_reslut)){
    
        $a=0;
        
            foreach($filter_reslut as $res){

            $languages = $this->db3->query("SELECT GROUP_CONCAT(l.Language) as doclanguages FROM `doctor_languages` dl,Languages l where l.Languages_id=dl.languages_id and dl.doctor_id='".$res->doctor_id."'")->row();

            $data['filter_result'][$a]['doctor_id']=$res->doctor_id;
            $data['filter_result'][$a]['doctor_name']=$res->doctor_name;
            $data['filter_result'][$a]['doctor_age']=$res->doctor_age;
            $data['filter_result'][$a]['doctor_gender']=$res->doctor_gender;
            $data['filter_result'][$a]['doctor_email']=$res->doctor_email;
            $data['filter_result'][$a]['doctor_phone_number']=$res->doctor_phone_number;
            $data['filter_result'][$a]['doctor_hospital']=$res->doctor_hospital;
            $data['filter_result'][$a]['doctor_city']=$res->doctor_city;
            $data['filter_result'][$a]['doctor_profile_pic']='http://devumdaa.in/dev/uploads/rural/test/'.$res->doctor_profile_pic;  #pic
            $data['filter_result'][$a]['doctor_type']=$res->doctor_type;
            $data['filter_result'][$a]['doctor_experience']=$res->doctor_experience;
            $data['filter_result'][$a]['languages']=$languages->doclanguages;

            $a++;

            }

        $this->response(array('code'=>'200','response'=> $data['filter_result'])); 

        }
        else
        {
            $this->response(array('code'=>'200','response'=> 'there is no doctors'));
        }


}



// testing purpose filter


    /**
     * this api is used to filter the general doctor filter **/ 

    public function testgeneraldoctorfilters_post(){   
        
        extract($_POST); 
        
                if($doctor_gender =="male" or $doctor_gender=="female")
                {
                    $genderQ = " AND doctor_gender = '".$doctor_gender."'";
                }
                if($doctor_language !="")
                {
                    $doct_lang="AND rd.doctor_id=dl.doctor_id AND dl.languages_id IN(".$doctor_language.")";

                }
   

    $filter_reslut=$this->db3->query("SELECT DISTINCT(rd.doctor_id) FROM rural_doctor_registration rd,doctor_languages dl WHERE rd.doctor_type='general'$genderQ $doct_lang")->result();

    foreach($filter_reslut as $doc_id){
        
        $this->response(array('code'=>'200','response'=> 'hai'));

        $result=$this->db3->query("SELECT * FROM rural_doctor_registration WHERE doctor_id='".$doc_id->doctor_id."'")->result();

        $a=0;

        foreach($result as $res){


            $data['filter_result'][$a]['doctor_id']=$res->doctor_id;
            $data['filter_result'][$a]['doctor_name']=$res->doctor_name;
            $data['filter_result'][$a]['doctor_age']=$res->doctor_age;
            $data['filter_result'][$a]['doctor_gender']=$res->doctor_gender;
            $data['filter_result'][$a]['doctor_email']=$res->doctor_email;
            $data['filter_result'][$a]['doctor_phone_number']=$res->doctor_phone_number;
            $data['filter_result'][$a]['doctor_hospital']=$res->doctor_hospital;
            $data['filter_result'][$a]['doctor_city']=$res->doctor_city;
            $data['filter_result'][$a]['doctor_profile_pic']='http://devumdaa.in/dev/uploads/rural/test/'.$res->doctor_profile_pic;  #pic
            $data['filter_result'][$a]['doctor_type']=$res->doctor_type;
            $data['filter_result'][$a]['doctor_experience']=$res->doctor_experience;

            $a++;

        }
        $this->response(array('code'=>'200','response'=> $data['filter_result']));
    }

    

        


    // if(!empty($filter_reslut)){
    
    //             $a=0;
                
    // foreach($filter_reslut as $res){

    //     $languages = $this->db3->query("SELECT GROUP_CONCAT(l.Language) as doclanguages FROM `doctor_languages` dl,Languages l where l.Languages_id=dl.languages_id and dl.doctor_id='".$res->doctor_id."'")->row();

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



    public function payChecksum_get($parameters, $method, $user_id){
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
        
        $this->response(array('code' => '200', 'message' => 'Success', 'result' =>$paytmParams,'requestname' => $method));


    }



// last semi
}

?>