<?php

defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
ini_set('memory_limit', '-1');

require APPPATH . '/libraries/REST_Controller1.php';
require APPPATH . '/libraries/ImplementJwt.php';

class Nurseapi extends REST_Controller1
{
    public function __construct() {
        parent::__construct();
        //Enable Nurse ASST DB
        $this->objOfJwt = new ImplementJwt();
        header('Content-Type: application/json');
        $this->load->library('mail_send', array('mailtype' => 'html'));
        $this->db2 = $this->load->database('second',TRUE);
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

    public function tokenGenerator($nurse_email,$nurse_password){

        $TokenData['nurse_email']=$nurse_email;
        $TokenData['nurse_password']=$nurse_password;
        $tokenData['timeStamp'] = Date('Y-m-d h:i:s');
        $jwtToken = $this->objOfJwt->GenerateToken($tokenData);
        $token= json_encode(array('Token'=>$jwtToken));

        $this->db2->query("UPDATE nurse_registration SET nurse_registration.Access_tokens = '".$jwtToken."' WHERE nurse_registration.nurse_email='".$nurse_email."'");

        $this->db2->query("UPDATE nurse_registration SET nurse_registration.Access_tokens = '".$jwtToken."' WHERE nurse_registration.nurse_phone_number='".$nurse_email."'");

        return $jwtToken;

     }


// nurse registration API-----------------------------------------------------

public function nurseRegistration_post(){

    if(!empty(isset($_POST))){
        extract($_POST);
        
        $password=md5($nurse_password);

        if($nurse_name !="" and $nurse_gender!="" and $nurse_email !="" and  $nurse_phone_number !="" and $password !="" and $nurse_clinic_name !="" and $nurse_location !="" and $nurse_age !="")
        {

            $validmail=$this->db2->query("SELECT nurse_registration.nurse_id FROM nurse_registration WHERE nurse_registration.nurse_email='".$nurse_email."'")->result();
            $validphone=$this->db2->query("SELECT nurse_registration.nurse_id from nurse_registration where nurse_registration.nurse_phone_number='".$nurse_phone_number."'")->result();

            if(empty($validmail))
            {

                            if(empty($validphone)){

                                                    $this->db2->query("INSERT INTO nurse_registration (nurse_name,nurse_email,nurse_phone_number,nurse_gender,nurse_password,nurse_clinic_name,nurse_location,nurse_age)  VALUES ('$nurse_name','$nurse_email','$nurse_phone_number','$nurse_gender','$password','$nurse_clinic_name','$nurse_location','$nurse_age')");

                                                    $this->response(array('code' => '200', 'message' => 'Your registration sucess'), 200); 

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

// nurse patientvitals_post API----------------------------------------------------------------------------------------

public function nursePatientVitals_post(){

    $received_Token = $this->input->request_headers('Authorization');

    $Header=$received_Token['Token'];
    
    $Tokquery=$this->db2->select('count(*) as val')->from('nurse_registration')->where('Access_tokens= "'.$Header.'"')->get()->row(); 

    $boolean=$Tokquery->val;
    
    if(!empty($boolean)){

        if(!empty(isset($_POST))){
            extract($_POST);
    
            if($nurse_id !="" and $patient_id !="")
    
                        {
    
                        $this->db2->query("UPDATE nurse_patient SET priority='".$vital_priority."' WHERE patient_id='".$patient_id."'");

                        $this->db2->query("INSERT INTO nurse_patient_vitals (patient_id,nurse_id,clinic_id,vital_priority,level_of_consciousness,heart_rate,blood_pressure,respiration,temperature,saturation,oxigen,copd,date)VALUES ('$patient_id','$nurse_id','$clinic_id','$vital_priority','$level_of_consciousness','$heart_rate','$blood_pressure','$respiration','$temperature','$saturation','$oxigen','$copd',CURDATE())");
                        
                        // echo "sucess";
                        $this->response(array('code' => '200', 'message' => 'Sucessfully added'), 200);
                        }
    
                        else{
                            $this->response(array('code' => '404', 'message' => 'Enter nurse_id and Patient_Id and clinic_id'), 200);
                        }
            
        }
        else
        {
            $this->response(array('code' => '404', 'message' => 'Not Entered properly'), 200);
            
        }
    
    }else{
        $this->response(array('code' => '404', 'message' => 'your Token is not valid'), 200);
    }
    

}

// nurse login API ------------------------------------------------------------------------------------------------------

public function nurseLogin_post(){

    extract($_POST);

    $nurse_email;
     $clinic_id;
     $trimclinicid=substr($clinic_id,4);
     $clinic_id=$trimclinicid;
    $password=md5($nurse_password);

    $cli=$this->db2->select('*')->from('clinic_admin')->where('clinic_id="'.$clinic_id.'"')->get()->row();

    $pasval=$this->db2->select('*')->from('nurse_registration')->where('nurse_email="'.$nurse_email.'" AND clinic_id="'.$clinic_id.'"')->get()->row();

    $pasval2=$this->db2->select('*')->from('nurse_registration')->where('nurse_phone_number="'.$nurse_email.'" AND clinic_id="'.$clinic_id.'"')->get()->row();

    $pasID=$pasval->nurse_id;

    $pasID2=$pasval2->nurse_id;

    if($pasID>0 || $pasID2>0 ){

        $res=$this->db2->select('*')->from('nurse_registration')->where('nurse_email="'.$nurse_email.'" AND nurse_password="'.$password.'" AND clinic_id="'.$clinic_id.'" OR nurse_phone_number="'.$nurse_email.'" AND nurse_password="'.$password.'" AND clinic_id="'.$clinic_id.'"')->get()->row();
        
       
        $id=$res->nurse_id;


        $nursefname=$res->first_name;
        $nurselname=$res->last_name;
        $nursename=$nursefname." ".$nurselname;
        $mail=$res->nurse_email;
        $ph=$res->nurse_phone_number;
        $password=$res->nurse_password;
        $clname=$res->nurse_clinic_name;
        $loc=$res->nurse_location;
        $active=$res->status;
    
        if($id>0 ){

            if($active>0){          

                 $tokenprint=$this->tokenGenerator($nurse_email,$nurse_password);

                $this->response(array('code' => '200','message' => 'sucesslly login','token'=>$tokenprint ,
                 'details'=>array('nurse_id' =>$id,
                 'nurse_name'=>$nursename,
                 'nurse_email'=>$mail,
                 'phonenumber'=>$ph,
                 'password'=>$password,
                 'clinic_name'=>$cli->clinic_name,
                 'clinic_id'=>$cli->clinic_id,
                 'clinic_location'=>$loc)));
                
        }else{ $this->response(array('code'=>'420','message'=>'permission not granted')); }

           
        }else

        $this->response(array('code' => '404', 'message' => 'Your password is wrong'));
}
else
{
    $this->response(array('code' => '404', 'message' => 'Your have entered wrong login Id or wrong clinic Id'));
}


}


// nurse patientvitals_get API--------------------------------------------------------------------------------------------------------------------------------


public function getNursePatientVItals_get($patient_id,$nurse_id){  

    $received_Token = $this->input->request_headers('Authorization');

    $Header=$received_Token['Token'];


    $Tokquery=$this->db2->select('count(*) as val')->from('nurse_registration')->where('Access_tokens= "'.$Header.'"')->get()->row(); 

    $boolean=$Tokquery->val;

    if(!empty($boolean)){

        $res=$this->db2->select('count(*) as imp')->from('nurse_patient_vitals')->where('patient_id="'.$patient_id.'" AND nurse_id="'.$nurse_id.'"')->get()->row();

        $val=$res->imp;
       
        if($val>0){
    
        $table=$this->db2->query("SELECT * FROM nurse_patient_vitals WHERE patient_id= '".$patient_id."' and nurse_id= '".$nurse_id."'")->result();
    
        // $djson=json_encode($table);
    
        // echo $djson;
    
        $data['vitals']=$table;
    
        $this->response($data);
    
    }else{
        
        $this->response(array('code' => '404', 'message' => 'No Records Found'), 200);
    }
    }
    else
    {
     $this->response(array('code' => '404', 'message' => 'your Token is not valid'), 200);
         
    }
}

   





// nursePatientRegistration API-------------------------------------------------------------------------------------------------------------------

public function nursePatientRegistration_post(){
   
    extract($_POST);

    $received_Token = $this->input->request_headers('Authorization');

    $Header=$received_Token['Token'];

    $Tokquery=$this->db2->select('count(*) as val')->from('nurse_registration')->where('Access_tokens= "'.$Header.'"')->get()->row(); 

    $boolean=$Tokquery->val;

    if(!empty($boolean)){ 

         $this->db2->query("INSERT into nurse_patient (nurse_id,patient_name,patient_age,patient_mobile_number,patient_location,patient_gender,priority,clinic_id) values ('$nurse_id','$patient_name','$patient_age','$patient_mobile_number','$patient_location','$patient_gender','$priority','$clinic_id')");

         $this->response(array('code' => '200', 'message' => 'sucessfully created'), 200);
         
    }else{
        $this->response(array('code' => '404', 'message' => 'your Token is not valid'), 200);
    }

}


public function nurseForgetPassword_post() {
    // $email;
    // $phone_number;


        extract($_POST);

        $check_user = $this->db2->select('*')->from('nurse_registration')->where( 'nurse_phone_number="'.$phone_number.'"')->get()->row();



        $h=count($check_user);
        
        if($h>0){
            $otp=rand(100000,999999);
            $mobile = $check_user->nurse_phone_number;
            $message="your OTP is  "  . $otp .  "  to reset your password.";
            $user_name=$check_user->nurse_name;
            $nurseID=$check_user->nurse_id;
            send_otp($mobile, $otp, $message);
            $this->response(array('code'=>'200','message'=>'OTP send on Mobile '.$mobile, 'otp'=>$otp, 'UserName'=> $user_name, 'nurse_id'=>$nurseID));
        }
        else
        {

            $this->response(array('code' => '404', 'message' => 'User Does Not Exist'), 200);
        }
        
}


public function updatePassword_post(){

    extract($_POST);

    $passwordmd5=md5($password);

$updte=$this->db2->query("UPDATE nurse_registration SET nurse_password='".$passwordmd5."' WHERE nurse_phone_number='".$nurse_phone_number."'");

$this->response(array('code'=>'200','message'=>'password sucessfully changed'));

}



public function clinicPatient_post(){

        extract($_POST); 
    
        $total=$this->db2->query("select * from nurse_patient where clinic_id='".$clinic_id."'")->result();

        if(!empty($total)){
        
        $total=$this->db2->query("select * from nurse_patient where clinic_id='".$clinic_id."'")->result();
    
        $data['clinic_patient']=$total;
    
        $this->response($data);
        }else{
            $this->response(array('code' => '200', 'message' => 'there are no patients'), 200);

        }
        // $clivalue=$this->db2->select('count(*) as val')->from('nurse_patient')->where('clinic_id="'.$clinic_id.'"')->get()->row();
}


public function getNursePatientVItals_post(){  

    $received_Token = $this->input->request_headers('Authorization');

    $Header=$received_Token['Token'];

    $Tokquery=$this->db2->select('count(*) as val')->from('nurse_registration')->where('Access_tokens= "'.$Header.'"')->get()->row();

    $boolean=$Tokquery->val;

    if(!empty($boolean)){

        extract($_POST);

        $res=$this->db2->select('count(*) as imp')->from('nurse_patient_vitals')->where('patient_id="'.$patient_id.'" AND clinic_id="'.$clinic_id.'"')->get()->row();
    
        $val=$res->imp;
       
        if($val>0){
    
        $table=$this->db2->query("SELECT * FROM nurse_patient_vitals WHERE patient_id= '".$patient_id."' and clinic_id= '".$clinic_id." ORDER BY `date`.`id` ASC'")->result();
    
    
        $data['vitals']=$table;
    
        $this->response($data);
    
    }else{
        
        $this->response(array('code' => '404', 'message' => 'No Records Found'), 200);
    }
    }
    else
    {

        $this->response(array('code' => '404', 'message' => 'your Token is not valid'), 200);
    }
}


public function registration_otp_post(){

    extract($_POST);

if (filter_var($nurse_email, FILTER_VALIDATE_EMAIL)) {

    $validmail=$this->db2->query("SELECT nurse_registration.nurse_id FROM nurse_registration WHERE nurse_registration.nurse_email='".$nurse_email."'")->result();
    $validphone=$this->db2->query("SELECT nurse_registration.nurse_id from nurse_registration where nurse_registration.nurse_phone_number='".$nurse_phone_number."'")->result();

        if(empty($validmail)){

                    if(empty($validphone)){

                        $otp=rand(100000,999999);
                        $mobile = $nurse_phone_number;
                        $message="Nurse Asst registration OTP  : ". $otp;
                        send_otp($mobile, $otp, $message);
                        
                        $this->response(array('code'=>'200','message'=>'OTP send on Mobile '.$mobile, 'otp'=>$otp));
                        
                    }else{
                        
                        $this->response(array('code' => '404', 'message' => 'Entered phone number already exists'), 200);
                    }

        }else{

            $this->response(array('code' => '404', 'message' => 'Entered email id already exists'), 200);

        }

} else {

            $this->response(array('code' => '404', 'message' => 'Email address is not valid '), 200);

}
               
}

        public function clinic(){

            $data['hai']="thank you sandeep";

            $data['view'] = 'nurse_clinic';
            $this->load->view('layout', $data);
            
        }

}
