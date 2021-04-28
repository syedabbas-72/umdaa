<?php

defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
ini_set('memory_limit', '-1');

require APPPATH . '/libraries/REST_Controller1.php';
require APPPATH . '/libraries/ImplementJwt.php';

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



    public function patientCreation_post(){

            extract($_POST);
            
        $this->db3->query("INSERT INTO rural_rmp_patients (patient_id,rmp_id,patient_name,patient_gender,patient_age,patient_mobile_number,patient_id_number,patient_location,created_by,created_date) VALUES ('$patient_id','$rmp_id','$patient_name','$patient_gender','$patient_age','$patient_mobile_number','$patient_id_number','$patient_location','$rmp_id',CURRENT_TIMESTAMP())");
        
        $this->response(array('code' => '200', 'message' => 'patient sucessfully created'), 200);

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

                                                        $this->db3->query("INSERT INTO rural_rmp_registration (rmp_name,rmp_gender,rmp_age,rmp_email,rmp_phone,rmp_password,rmp_clinic_name,rmp_city,profile_pic) VALUES ('$rmp_name','$rmp_gender','$rmp_age','$rmp_email','$rmp_phone','$password','$rmp_clinic_name','$rmp_city','$dp')");
    
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
    
    
    public function vital_POST(){

    if(!empty( isset($_POST))){
        extract($_POST);

        if($heart_rate!="" and $blood_pressure!="" and $respiratory!="" and $temperature!="" and $weight!="" and $height!="")
        {
            $this->db3->query("INSERT INTO rural_rmp_patient_vitals (patient_id,rmp_id,heart_rate,blood_pressure,respiratory,temperature,patient_weight,patient_height,created_by) VALUES ('$patient_id','$rmp_id','$heart_rate','$blood_pressure','$respiratory','$temperature','$patient_weight','$patient_height','$rmp_id')");
    
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
    
}

?>