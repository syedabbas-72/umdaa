<?php


defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
ini_set('memory_limit', '-1');

require APPPATH . '/libraries/REST_Controller1.php';

class Registration extends REST_Controller1
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
    
    public function digitalSign_post(){
        if(isset($_POST))
        {
            extract($_POST);
            
            $date = date("Y-m-d H:i:s");
            $docid = trim($docid, '"');

            // $output = base_url('uploads/docDigitalSign/'.time().rand(1000,9999));

            // $filetype = pathinfo($_FILES["file_i"]["name"], PATHINFO_EXTENSION);

            $fileName = "digitalSign-".$docid.".jpg";
            // base64_to_jpeg($file_i, $output);
            $data_uri = $file_i;
            $encoded_image = explode(",", $data_uri)[1];
            $decoded_image = base64_decode($encoded_image);
            
            $image_base64_form = base64_decode($file_i); 
            $file = './uploads/docDigitalSign/digitalSign-'.$docid.'.jpg'; 
            file_put_contents($file, $decoded_image); 
            $data['user_id'] = $docid;
            $data['digital_signature'] = $fileName;
            $data['created_date_time'] = date("Y-m-d H:i:s");
            $this->Generic_model->insertData('digital_signatures', $data);
            $param = "File Uploaded";
            $this->response(array('code'=>'200','message'=>'File Uploaded','result'=>$param));
                
        }
        else
        {
            $para = "No File Found";
            $this->repsonse(array('code'=>'201','message'=>'No File Found','result'=>$para));
        }
    }

    // check mobile number
    public function checkmobile_post(){
        if(isset($_POST)){
            extract($_POST);
            $check = $this->db->query("select * from doctors where mobile='".$mobile."'")->row();
            if(count($check) <= 0){
                $this->response(array('code'=>'200','message'=>'Mobile Number Not Exists'));
            }
            else{
                $this->response(array('code'=>'201','message'=>'Mobile Number Exists'));
            }
        }
        else{
            $this->repsonse(array('code'=>'201','message'=>'Error Occured'));
        }
    }

    // check Email
    public function checkemail_post(){
        if(isset($_POST)){
            extract($_POST);
            $check = $this->db->query("select * from doctors where email='".$email."'")->row();
            if(count($check) <= 0){
                $this->response(array('code'=>'200','message'=>'Email Not Exists'));
            }
            else{
                $this->response(array('code'=>'201','message'=>'Email Exists'));
            }
        }
        else{
            $this->repsonse(array('code'=>'201','message'=>'Error Occured'));
        }
    }

    // Slots for doctors
    public function doctorSlots_post(){
        if(isset($_POST))
        {
            extract($_POST);
            $user_id = trim($user_id, '"');
            $clinic_id = trim($clinic_id, '"');
            $morningFrom = date("H:i:s",strtotime(trim($morningFrom, '"')));
            $morningTo = date("H:i:s",strtotime(trim($morningTo, '"')));
            $afternoonFrom = date("H:i:s",strtotime(trim($afternoonFrom, '"')));
            $afternoonTo = date("H:i:s", strtotime(trim($afternoonTo,'"')));
            $eveningFrom = date("H:i:s",strtotime(trim($eveningFrom,'"')));
            $eveningTo = date("H:i:s",strtotime(trim($eveningTo,'"')));

            $check = $this->db->query("select * from doctors where doctor_id='".$user_id."'")->row();
            if(count($check)>0)
            {
                $clinincDocInfo = $this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."' and doctor_id='".$user_id."'")->row();
                if(count($clinincDocInfo)>0)
                {
                    $con['consulting_fee'] = trim($consultation_fee, '"');
                    $this->Generic_model->updateData('clinic_doctor',$con,array('clinic_doctor_id'=>$clinincDocInfo->clinic_doctor_id));
                    $morning = json_decode(trim($morning, '"'));
                    $afternoon = json_decode(trim($afternoon, '"'));
                    $evening = json_decode(trim($evening, '"'));
                    // echo count($morning)."*".count($afternoon)."*".count($evening);
                    if(count($morning)>0)
                    {
                        unset($data);unset($data1);
                        foreach($morning as $mng)
                        {
                            $cdwInfo = $this->db->query("select * from clinic_doctor_weekdays where clinic_doctor_id='".$clinincDocInfo->clinic_doctor_id."' and weekday='".$mng."'")->row();
                            if(count($cdwInfo)>0)
                            {
                                $cdw_id = $cdwInfo->clinic_doctor_weekday_id;
                                $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                $data1['from_time'] = $morningFrom;
                                $data1['to_time'] = $morningTo;
                                $data1['session'] = 'morning';
                                $data1['created_date_time'] = date("Y-m-d H:i:s");
                                $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);    
                            }
                            else
                            {
                                $data['weekday'] = $mng;
                                $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
                                $data['created_date_time'] = date("Y-m-d H:i:s");
                                $data['modified_date_time'] = date("Y-m-d H:i:s");
                                $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
                                $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                $data1['from_time'] = $morningFrom;
                                $data1['to_time'] = $morningTo;
                                $data1['to_time'] = $morningTo;
                                $data1['session'] = 'morning';
                                $data1['created_date_time'] = date("Y-m-d H:i:s");
                                $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
                            }
                        }
                    }
    
                    if(count($afternoon)>0)
                    {
                        unset($data);unset($data1);
                        foreach($afternoon as $aft)
                        {
                            $cdwInfo = $this->db->query("select * from clinic_doctor_weekdays where clinic_doctor_id='".$clinincDocInfo->clinic_doctor_id."' and weekday='".$aft."'")->row();
                            if(count($cdwInfo)>0)
                            {
                                $cdw_id = $cdwInfo->clinic_doctor_weekday_id;
                                $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                $data1['from_time'] = $afternoonFrom;
                                $data1['to_time'] = $afternoonTo;
                                $data1['session'] = 'afternoon';
                                $data1['created_date_time'] = date("Y-m-d H:i:s");
                                $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);    
                            }
                            else
                            {
                                $data['weekday'] = $aft;
                                $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
                                $data['created_date_time'] = date("Y-m-d H:i:s");
                                $data['modified_date_time'] = date("Y-m-d H:i:s");
                                $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
                                $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                $data1['from_time'] = $afternoonFrom;
                                $data1['to_time'] = $afternoonTo;
                                $data1['session'] = 'afternoon';
                                $data1['created_date_time'] = date("Y-m-d H:i:s");
                                $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
                            }
                        }
                    }
                    if(count($evening)>0)
                    {
                        unset($data);unset($data1);
                        foreach($evening as $evng)
                        {
                            $cdwInfo = $this->db->query("select * from clinic_doctor_weekdays where clinic_doctor_id='".$clinincDocInfo->clinic_doctor_id."' and weekday='".$evng."'")->row();
                            if(count($cdwInfo)>0)
                            {
                                $cdw_id = $cdwInfo->clinic_doctor_weekday_id;
                                $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                $data1['from_time'] = $eveningFrom;
                                $data1['to_time'] = $eveningTo;
                                $data1['session'] = 'evening';
                                $data1['created_date_time'] = date("Y-m-d H:i:s");
                                $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);    
                            }
                            else
                            {
                                $data['weekday'] = $evng;
                                $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
                                $data['created_date_time'] = date("Y-m-d H:i:s");
                                $data['modified_date_time'] = date("Y-m-d H:i:s");
                                $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
                                $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                $data1['from_time'] = $eveningFrom;
                                $data1['to_time'] = $eveningTo;
                                $data1['session'] = 'evening';
                                $data1['created_date_time'] = date("Y-m-d H:i:s");
                                $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
                            }
                        }
                    }    
                    $param = "Slots Created";
                    $this->response(array('code'=>'200','message'=>'Success','result'=>$param));
                }
                else
                {
                    $param = "Clinic Doctor Mapping Not Found";
                    $this->response(array('code'=>'201','message'=>'Error Ocuured','result'=>$param));
                }
            }
            else
            {
                $param = "Doctor Not Found";
                $this->response(array('code'=>'201','message'=>'Error Ocuured','result'=>$param));
            }
        }
    }

    //Get Slots For Doctor based on clinic 
    public function getDoctorSlots_get($doctor_id,$clinic_id)
    {
        $clinincDocInfo = $this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."' 
        and doctor_id='".$doctor_id."'")->row();
        if(count($clinincDocInfo)>0)
        {
            $clinics_list = $this->db->select("weekday")
            ->from("clinic_doctor_weekday_slots  e")
            ->join("clinic_doctor_weekdays  c","e.clinic_doctor_weekday_id =c.clinic_doctor_weekday_id ")
            ->where("c.clinic_doctor_id ='".$clinincDocInfo->clinic_doctor_id."' and c.slot='walkin'")
            ->group_by("c.weekday")->get()->result();

            if(count($clinics_list)>0)
            {
                $digiSignInfo = $this->db->query("select * from digital_signatures where user_id='".$doctor_id."'")->row();
                if(count($digiSignInfo)>0)
                {
                    $data['digitalSignature'] = 1;
                }
                else
                {
                    $data['digitalSignature'] = 0;
                }
                $consult_fees = $this->db->query("select * from 
                clinic_doctor where doctor_id='".$doctor_id."' and clinic_id='".$clinic_id."'")->row();
                if(count($consult_fees)>0)
                {
                        $data['consultation_fees'] = $consult_fees->consulting_fee;   
                }
                else
                {
                    $data['consultation_fees'] = '';
                }


                $j=0;
                foreach ($clinics_list as $clinic) {  
                $data['weekdays'][$j]['day'] = $clinic->weekday;

                $slots_data= $this->db->select("*")
                ->from("clinic_doctor_weekday_slots  e")
                ->join("clinic_doctor_weekdays  c","e.clinic_doctor_weekday_id =c.clinic_doctor_weekday_id ")
                ->where("c.clinic_doctor_id ='".$clinincDocInfo->clinic_doctor_id."'
                and c.weekday ='".  $clinic->weekday."' and c.slot='walkin'")
                ->get()->result();

                    if(count($slots_data)>0)
                    {
                        $c=0;
                        foreach($slots_data as $slots)
                        {
                            $data['weekdays'][$j]['slots'][$c]['clinic_doctor_weekday_slot_id'] = $slots->clinic_doctor_weekday_slot_id;
                            $data['weekdays'][$j]['slots'][$c]['session'] = $slots->session;
                            $data['weekdays'][$j]['slots'][$c]['from_time'] = $slots->from_time;
                            $data['weekdays'][$j]['slots'][$c]['to_time'] = $slots->to_time;
                            $c++;
                        }
                    }
                   $j++;
                }
                $this->response(array('code'=>'200','message'=>'Success','result'=>$data));
            }
               else{
                $digiSignInfo = $this->db->query("select * from digital_signatures where user_id='".$doctor_id."'")->row();
                if(count($digiSignInfo)>0)
                {
                    $data['digitalSignature'] = 1;
                }
                else
                {
                    $data['digitalSignature'] = 0;
                }
                $consult_fees = $this->db->query("select * from 
                clinic_doctor where doctor_id='".$doctor_id."' and clinic_id='".$clinic_id."'")->row();
                if(count($consult_fees)>0)
                {
                        $data['consultation_fees'] = $consult_fees->consulting_fee;   
                }
                else
                {
                    $data['consultation_fees'] = '';
                }
                $data['weekdays'] = array();
                $this->response(array('code'=>'200','message'=>'Success','result'=>$data));
            }
        }
        // $this->response(array('code'=>'200','message'=>'Success','result'=>$data));
    }


        // Slots for doctors
        public function doctorEditSlots_post(){
            if(isset($_POST))
            {
                extract($_POST);
                // echo "inndss";
                $arr = json_decode($weekday,true);
                $clinincDocInfo = $this->db->query("select * from
                clinic_doctor where clinic_id='".$clinic_id."'
                and doctor_id='".$doctor_id."'")->row();
                $con['consulting_fee'] = trim($consultation_fee, '"');
                $this->Generic_model->updateData
                ('clinic_doctor',$con,array('clinic_doctor_id'=>$clinincDocInfo->clinic_doctor_id));
                
                for($x = 0; $x <=count($arr); $x++) {
                   
                    // if($arr[$x]['day'] == "1")
                    // {
                        for($a=0;$a<count($arr[$x]['slots']);$a++)
                        {                        
                            // echo ($arr[$x]['slots'][$a]['clinic_doctor_weekday_slot_id']);
                            // echo ($arr[$x]['slots'][$a]['session']);
                            // echo ($arr[$x]['slots'][$a]['from_time']);
                            // echo ($arr[$x]['slots'][$a]['to_time']);
                            if($arr[$x]['slots'][$a]['clinic_doctor_weekday_slot_id'] != '0')
                            {
                                // echo $arr[$x]['slots'][$a]['from_time'];
                                $getData = $this->db->query("select * from
                                clinic_doctor_weekday_slots
                                where
                                clinic_doctor_weekday_slot_id=
                                '".$arr[$x]['slots'][$a]['clinic_doctor_weekday_slot_id']."'")->row();
                                if(count($getData)>0)
                                {
                                    $data1['clinic_doctor_weekday_id'] = $getData->clinic_doctor_weekday_id;
                                    $data1['from_time'] = $arr[$x]['slots'][$a]['from_time'];
                                    $data1['to_time'] = $arr[$x]['slots'][$a]['to_time'];
                                    $data1['session'] = $getData->session;
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->updateData("clinic_doctor_weekday_slots",
                                     $data1,
                                    array('clinic_doctor_weekday_slot_id' => $getData->clinic_doctor_weekday_slot_id));
                                }
                                $this->response(array('code'=>'200','message'=>'Sucess','result'=>'Successfully Updated'));
                            }
                            else
                            {

                                $clinincDocInfo = $this->db->query("select * from
                                 clinic_doctor where clinic_id='".$clinic_id."'
                                 and doctor_id='".$doctor_id."'")->row();
                                $cdwInfo = $this->db->query("select * from clinic_doctor_weekdays where clinic_doctor_id='".$clinincDocInfo->clinic_doctor_id."' and weekday='".$arr[$x]['day']."'and slot='walkin'")->row();
                                if(count($cdwInfo)>0)
                                {
                                    $cdw_id = $cdwInfo->clinic_doctor_weekday_id;
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] =  $arr[$x]['slots'][$a]['from_time'];
                                    $data1['to_time'] = $arr[$x]['slots'][$a]['to_time'];
                                    $data1['session'] = $arr[$x]['slots'][$a]['session'];
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);   
                                    $this->response(array('code'=>'200','message'=>'Sucess','result'=>'Successfully Inserted')); 
                                }
                                else
                                {
                                    $data['weekday'] = $arr[$x]['day'];
                                    $data['slot'] = 'walkin';
                                    $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
                                    $data['created_date_time'] = date("Y-m-d H:i:s");
                                    $data['modified_date_time'] = date("Y-m-d H:i:s");
                                    $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] =$arr[$x]['slots'][$a]['from_time'];
                                    $data1['to_time'] = $arr[$x]['slots'][$a]['to_time'];
                                    $data1['session'] =  $arr[$x]['slots'][$a]['session'];
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
                                    $this->response(array('code'=>'200','message'=>'Sucess','result'=>'Successfully Inserted'));
                                }

                                // $clinincDocInfo = $this->db->query("select * from
                                //  clinic_doctor where clinic_id='".$clinic_id."'
                                //  and doctor_id='".$doctor_id."'")->row();
                                //  if(count($clinincDocInfo)>0)
                                //  {
                                //     $data['weekday'] = $arr[$x]['day'];
                                //     $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
                                //     $data['created_date_time'] = date("Y-m-d H:i:s");
                                //     $data['modified_date_time'] = date("Y-m-d H:i:s");
                                //     $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
                                //     $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                //     $data1['from_time'] = $arr[$x]['slots'][$a]['from_time'];
                                //     $data1['to_time'] = $arr[$x]['slots'][$a]['to_time'];
                                //     $data1['session'] =  $arr[$x]['slots'][$a]['session'];
                                //     $data1['created_date_time'] = date("Y-m-d H:i:s");
                                //     $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                //     $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
                                //     $this->response(array('code'=>'200','message'=>'Sucess','result'=>'Successfully Inserted'));
                                //  }
                                //  else
                                //  {
                                //     $this->response(array('code'=>'200','message'=>'Failure','result'=>'Doctor,Clinic Id are not matching'));
                                //  }
                   
                            }
                        
                        }
                    // }
                    // else{
                    //     echo "not";
                    // }
                  }            
            }
        }

           // Slots for doctors
    public function videocallDoctorSlots_post(){
        if(isset($_POST))
        {
            extract($_POST);
            $user_id = trim($user_id, '"');
            $clinic_id = trim($clinic_id, '"');
            $morningFrom = date("H:i:s",strtotime(trim($morningFrom, '"')));
            $morningTo = date("H:i:s",strtotime(trim($morningTo, '"')));
            $afternoonFrom = date("H:i:s",strtotime(trim($afternoonFrom, '"')));
            $afternoonTo = date("H:i:s", strtotime(trim($afternoonTo,'"')));
            $eveningFrom = date("H:i:s",strtotime(trim($eveningFrom,'"')));
            $eveningTo = date("H:i:s",strtotime(trim($eveningTo,'"')));

            $check = $this->db->query("select * from doctors where doctor_id='".$user_id."'")->row();
            if(count($check)>0)
            {
                $clinincDocInfo = $this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."' and doctor_id='".$user_id."'")->row();
                if(count($clinincDocInfo)>0)
                {
                    $con['online_consulting_fee'] = trim($consultation_fee, '"');
                    $this->Generic_model->updateData('clinic_doctor',$con,array('clinic_doctor_id'=>$clinincDocInfo->clinic_doctor_id));
                    $morning = json_decode(trim($morning, '"'));
                    $afternoon = json_decode(trim($afternoon, '"'));
                    $evening = json_decode(trim($evening, '"'));
                    // echo count($morning)."*".count($afternoon)."*".count($evening);
                    if(count($morning)>0)
                    {
                        unset($data);unset($data1);
                        foreach($morning as $mng)
                        {
                            $cdwInfo = $this->db->query("select * from clinic_doctor_weekdays where clinic_doctor_id='".$clinincDocInfo->clinic_doctor_id."' and weekday='".$mng."' and slot='video call'")->row();
                            if(count($cdwInfo)>0)
                            {
                                $cdw_id = $cdwInfo->clinic_doctor_weekday_id;
                                $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                $data1['from_time'] = $morningFrom;
                                $data1['to_time'] = $morningTo;
                                $data1['session'] = 'morning';
                                $data1['created_date_time'] = date("Y-m-d H:i:s");
                                $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);    
                            }
                            else
                            {
                                $data['weekday'] = $mng;
                                $data['slot'] = 'video call';
                                $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
                                $data['created_date_time'] = date("Y-m-d H:i:s");
                                $data['modified_date_time'] = date("Y-m-d H:i:s");
                                $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
                                $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                $data1['from_time'] = $morningFrom;
                                $data1['to_time'] = $morningTo;
                                $data1['to_time'] = $morningTo;
                                $data1['session'] = 'morning';
                                $data1['created_date_time'] = date("Y-m-d H:i:s");
                                $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
                            }
                        }
                    }
    
                    if(count($afternoon)>0)
                    {
                        unset($data);unset($data1);
                        foreach($afternoon as $aft)
                        {
                            $cdwInfo = $this->db->query("select * from clinic_doctor_weekdays where clinic_doctor_id='".$clinincDocInfo->clinic_doctor_id."' and weekday='".$aft."'and slot='video call'")->row();
                            if(count($cdwInfo)>0)
                            {
                                $cdw_id = $cdwInfo->clinic_doctor_weekday_id;
                                $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                $data1['from_time'] = $afternoonFrom;
                                $data1['to_time'] = $afternoonTo;
                                $data1['session'] = 'afternoon';
                                $data1['created_date_time'] = date("Y-m-d H:i:s");
                                $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);    
                            }
                            else
                            {
                                $data['weekday'] = $aft;
                                $data['slot'] = 'video call';
                                $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
                                $data['created_date_time'] = date("Y-m-d H:i:s");
                                $data['modified_date_time'] = date("Y-m-d H:i:s");
                                $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
                                $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                $data1['from_time'] = $afternoonFrom;
                                $data1['to_time'] = $afternoonTo;
                                $data1['session'] = 'afternoon';
                                $data1['created_date_time'] = date("Y-m-d H:i:s");
                                $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
                            }
                        }
                    }
                    if(count($evening)>0)
                    {
                        unset($data);unset($data1);
                        foreach($evening as $evng)
                        {
                            $cdwInfo = $this->db->query("select * from clinic_doctor_weekdays where clinic_doctor_id='".$clinincDocInfo->clinic_doctor_id."' and weekday='".$evng."' and slot='video call'")->row();
                            if(count($cdwInfo)>0)
                            {
                                $cdw_id = $cdwInfo->clinic_doctor_weekday_id;
                                $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                $data1['from_time'] = $eveningFrom;
                                $data1['to_time'] = $eveningTo;
                                $data1['session'] = 'evening';
                                $data1['created_date_time'] = date("Y-m-d H:i:s");
                                $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);    
                            }
                            else
                            {
                                $data['weekday'] = $evng;
                                $data['slot'] = 'video call';
                                $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
                                $data['created_date_time'] = date("Y-m-d H:i:s");
                                $data['modified_date_time'] = date("Y-m-d H:i:s");
                                $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
                                $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                $data1['from_time'] = $eveningFrom;
                                $data1['to_time'] = $eveningTo;
                                $data1['session'] = 'evening';
                                $data1['created_date_time'] = date("Y-m-d H:i:s");
                                $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
                            }
                        }
                    }    
                    $param = "Slots Created";
                    $this->response(array('code'=>'200','message'=>'Success','result'=>$param));
                }
                else
                {
                    $param = "Clinic Doctor Mapping Not Found";
                    $this->response(array('code'=>'201','message'=>'Error Ocuured','result'=>$param));
                }
            }
            else
            {
                $param = "Doctor Not Found";
                $this->response(array('code'=>'201','message'=>'Error Ocuured','result'=>$param));
            }
        }
    }

       //Get Video Slots For Doctor based on clinic 
       public function getVideoSlotsData_get($doctor_id,$clinic_id)
       {
           $clinincDocInfo = $this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."' 
           and doctor_id='".$doctor_id."'")->row();
           if(count($clinincDocInfo)>0)
           {
               $clinics_list = $this->db->select("weekday")
               ->from("clinic_doctor_weekday_slots  e")
               ->join("clinic_doctor_weekdays  c","e.clinic_doctor_weekday_id =c.clinic_doctor_weekday_id ")
               ->where("c.clinic_doctor_id ='".$clinincDocInfo->clinic_doctor_id."' and c.slot='video call'")
               ->group_by("c.weekday")->get()->result();
   
               if(count($clinics_list)>0)
               {
                   $digiSignInfo = $this->db->query("select * from digital_signatures where user_id='".$doctor_id."'")->row();
                   if(count($digiSignInfo)>0)
                   {
                       $data['digitalSignature'] = 1;
                   }
                   else
                   {
                       $data['digitalSignature'] = 0;
                   }
                   $consult_fees = $this->db->query("select * from 
                   clinic_doctor where doctor_id='".$doctor_id."' and clinic_id='".$clinic_id."'")->row();
                   if(count($consult_fees)>0)
                   {
                           $data['consultation_fees'] = $consult_fees->online_consulting_fee;   
                   }
                   else
                   {
                       $data['consultation_fees'] = '';
                   }
   
   
                   $j=0;
                   foreach ($clinics_list as $clinic) {  
                   $data['weekdays'][$j]['day'] = $clinic->weekday;
   
                   $slots_data= $this->db->select("*")
                   ->from("clinic_doctor_weekday_slots  e")
                   ->join("clinic_doctor_weekdays  c","e.clinic_doctor_weekday_id =c.clinic_doctor_weekday_id ")
                   ->where("c.clinic_doctor_id ='".$clinincDocInfo->clinic_doctor_id."'
                   and c.weekday ='".  $clinic->weekday."' and c.slot='video call'")
                   ->get()->result();
   
                       if(count($slots_data)>0)
                       {
                           $c=0;
                           foreach($slots_data as $slots)
                           {
                               $data['weekdays'][$j]['slots'][$c]['clinic_doctor_weekday_slot_id'] = $slots->clinic_doctor_weekday_slot_id;
                               $data['weekdays'][$j]['slots'][$c]['session'] = $slots->session;
                               $data['weekdays'][$j]['slots'][$c]['from_time'] = $slots->from_time;
                               $data['weekdays'][$j]['slots'][$c]['to_time'] = $slots->to_time;
                               $c++;
                           }
                       }
                      $j++;
                   }
                   $this->response(array('code'=>'200','message'=>'Success','result'=>$data));
               }
                  else{
                   $digiSignInfo = $this->db->query("select * from digital_signatures where user_id='".$doctor_id."'")->row();
                   if(count($digiSignInfo)>0)
                   {
                       $data['digitalSignature'] = 1;
                   }
                   else
                   {
                       $data['digitalSignature'] = 0;
                   }
                   $consult_fees = $this->db->query("select * from 
                   clinic_doctor where doctor_id='".$doctor_id."' and clinic_id='".$clinic_id."'")->row();
                   if(count($consult_fees)>0)
                   {
                           $data['consultation_fees'] = $consult_fees->online_consulting_fee;   
                   }
                   else
                   {
                       $data['consultation_fees'] = '';
                   }
                   $data['weekdays'] = array();
                   $this->response(array('code'=>'200','message'=>'Success','result'=>$data));
               }
           }
           // $this->response(array('code'=>'200','message'=>'Success','result'=>$data));
       }

        // video Slots for doctors edit
        public function doctorEditVideoSlots_post()
        {
            if(isset($_POST))
            {
                extract($_POST);
                // echo "inndss";
                $arr = json_decode($weekday,true);
                $clinincDocInfo = $this->db->query("select * from
                clinic_doctor where clinic_id='".$clinic_id."'
                and doctor_id='".$doctor_id."'")->row();
                $con['online_consulting_fee'] = trim($consultation_fee, '"');
                $this->Generic_model->updateData
                ('clinic_doctor',$con,array('clinic_doctor_id'=>$clinincDocInfo->clinic_doctor_id));
                
                for($x = 0; $x <=count($arr); $x++)
                 {
                        for($a=0;$a<count($arr[$x]['slots']);$a++)
                        {                        
                            if($arr[$x]['slots'][$a]['clinic_doctor_weekday_slot_id'] != '0')
                            {
                                // echo $arr[$x]['slots'][$a]['from_time'];
                                $getData = $this->db->query("select * from
                                clinic_doctor_weekday_slots
                                where
                                clinic_doctor_weekday_slot_id=
                                '".$arr[$x]['slots'][$a]['clinic_doctor_weekday_slot_id']."'")->row();
                                if(count($getData)>0)
                                {
                                    $data1['clinic_doctor_weekday_id'] = $getData->clinic_doctor_weekday_id;
                                    $data1['from_time'] = $arr[$x]['slots'][$a]['from_time'];
                                    $data1['to_time'] = $arr[$x]['slots'][$a]['to_time'];
                                    $data1['session'] = $getData->session;
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->updateData("clinic_doctor_weekday_slots",
                                     $data1,
                                    array('clinic_doctor_weekday_slot_id' => $getData->clinic_doctor_weekday_slot_id));
                                }
                                $this->response(array('code'=>'200','message'=>'Sucess','result'=>'Successfully Updated'));
                            }
                            else
                            {

                                $clinincDocInfo = $this->db->query("select * from
                                clinic_doctor where clinic_id='".$clinic_id."'
                                and doctor_id='".$doctor_id."'")->row();
                               $cdwInfo = $this->db->query("select * from clinic_doctor_weekdays where clinic_doctor_id='".$clinincDocInfo->clinic_doctor_id."' and weekday='".$arr[$x]['day']."'and slot='video call'")->row();
                               if(count($cdwInfo)>0)
                               {
                                   $cdw_id = $cdwInfo->clinic_doctor_weekday_id;
                                   $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                   $data1['from_time'] =  $arr[$x]['slots'][$a]['from_time'];
                                   $data1['to_time'] = $arr[$x]['slots'][$a]['to_time'];
                                   $data1['session'] = $arr[$x]['slots'][$a]['session'];
                                   $data1['created_date_time'] = date("Y-m-d H:i:s");
                                   $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                   $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);   
                                   $this->response(array('code'=>'200','message'=>'Sucess','result'=>'Successfully Inserted')); 
                               }
                               else
                               {
                                   $data['weekday'] = $arr[$x]['day'];
                                   $data['slot'] = 'video call';
                                   $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
                                   $data['created_date_time'] = date("Y-m-d H:i:s");
                                   $data['modified_date_time'] = date("Y-m-d H:i:s");
                                   $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
                                   $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                   $data1['from_time'] =$arr[$x]['slots'][$a]['from_time'];
                                   $data1['to_time'] = $arr[$x]['slots'][$a]['to_time'];
                                   $data1['session'] =  $arr[$x]['slots'][$a]['session'];
                                   $data1['created_date_time'] = date("Y-m-d H:i:s");
                                   $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                   $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
                                   $this->response(array('code'=>'200','message'=>'Sucess','result'=>'Successfully Inserted'));
                               }

                                // $clinincDocInfo = $this->db->query("select * from
                                //  clinic_doctor where clinic_id='".$clinic_id."'
                                //  and doctor_id='".$doctor_id."'")->row();
                                //  if(count($clinincDocInfo)>0)
                                //  {
                                //     $data['weekday'] = $arr[$x]['day'];
                                //     $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
                                //     $data['slot'] = 'video call';
                                //     $data['created_date_time'] = date("Y-m-d H:i:s");
                                //     $data['modified_date_time'] = date("Y-m-d H:i:s");
                                //     $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
                                //     $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                //     $data1['from_time'] = $arr[$x]['slots'][$a]['from_time'];
                                //     $data1['to_time'] = $arr[$x]['slots'][$a]['to_time'];
                                //     $data1['session'] =  $arr[$x]['slots'][$a]['session'];
                                //     $data1['created_date_time'] = date("Y-m-d H:i:s");
                                //     $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                //     $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
                                //     $this->response(array('code'=>'200','message'=>'Sucess','result'=>'Successfully Inserted'));
                                //  }
                                //  else{
                                //     $this->response(array('code'=>'200','message'=>'Sucess','result'=>'Doctor,Clinic Id are not matching'));
                                //  }
                   
                            }
                        
                        }
                 }  
                 
                 
                //  $weekdays=$this->db->query('select weekday from clinic_doctor_weekdays where
                //  clinic_doctor_id="'.$clinincDocInfo->clinic_doctor_id.'" 
                // and clinic_doctor_weekdays.slot="video call" 
                // group by weekday order by weekday ASC')->result();

                // $allDayss=[];
                // for($i=0;$i<=7;$i++)
                // {
                //   $abc = $weekdays[$i]->weekday;
                //   if($abc != '')
                //   {
                //     array_push($allDayss,$abc);
                //   }
                // }

                // $data= array_merge(array_diff($allDayss, $arr)) ;

                // for($j=0;$j<=count($data)-1;$j++)
                // {
                // $weekdaysSlots=$this->db->query('select * from clinic_doctor_weekdays where
                // clinic_doctor_id="'.$clinincDocInfo->clinic_doctor_id.'" 
                // and clinic_doctor_weekdays.slot="video call" 
                // and weekday="'.$data[$j].'"')->row();

                // $res = $this->Generic_model->deleteRecord('clinic_doctor_weekdays',
                // array('clinic_doctor_weekday_id'=>
                // $weekdaysSlots->clinic_doctor_weekday_id));

                // }
            }
        }


            // video Slots for doctors edit
            public function doctorEditVideoSlotss_post()
            {
                if(isset($_POST))
                {
                    extract($_POST);
                    // echo "inndss";
                    $arr = json_decode($weekday,true);
                    $clinincDocInfo = $this->db->query("select * from
                    clinic_doctor where clinic_id='".$clinic_id."'
                    and doctor_id='".$doctor_id."'")->row();
                    $con['consulting_fee'] = trim($consultation_fee, '"');
                    $this->Generic_model->updateData
                    ('clinic_doctor',$con,array('clinic_doctor_id'=>$clinincDocInfo->clinic_doctor_id));

                    $weekdays=$this->db->query('select weekday from clinic_doctor_weekdays where
                     clinic_doctor_id="'.$clinincDocInfo->clinic_doctor_id.'" 
                    and clinic_doctor_weekdays.slot="video call" 
                    group by weekday order by weekday ASC')->result();

                    $allDayss=[];
                    for($i=0;$i<=7;$i++)
                    {
                      $abc = $weekdays[$i]->weekday;
                      if($abc != '')
                      {
                        array_push($allDayss,$abc);
                      }
                    }

                    $data= array_merge(array_diff($allDayss, $arr)) ;
                    // $all = array_diff($arr,$allDayss);

                    for($j=0;$j<=count($data)-1;$j++)
                    {
                    $weekdaysSlots=$this->db->query('select * from clinic_doctor_weekdays where
                    clinic_doctor_id="'.$clinincDocInfo->clinic_doctor_id.'" 
                    and clinic_doctor_weekdays.slot="video call" 
                    and weekday="'.$data[$j].'"')->row();

                    $res = $this->Generic_model->deleteRecord('clinic_doctor_weekdays',
                    array('clinic_doctor_weekday_id'=>
                    $weekdaysSlots->clinic_doctor_weekday_id));

                    }
                 

                    $this->response(array('code'=>'200','message'=>$res,'result'=>'Deleted'));
                    
                //     for($x = 0; $x <=count($arr);$x++)
                //      {
                //             for($a=0;$a<count($arr[$x]['slots']);$a++)
                //             {                        
                //                 if($arr[$x]['slots'][$a]['clinic_doctor_weekday_slot_id'] != '0')
                //                 {
                //                     // echo $arr[$x]['slots'][$a]['from_time'];
                //                     $getData = $this->db->query("select * from
                //                     clinic_doctor_weekday_slots
                //                     where
                //                     clinic_doctor_weekday_slot_id=
                //                     '".$arr[$x]['slots'][$a]['clinic_doctor_weekday_slot_id']."'")->row();
                //                     if(count($getData)>0)
                //                     {
                //                         $data1['clinic_doctor_weekday_id'] = $getData->clinic_doctor_weekday_id;
                //                         $data1['from_time'] = $arr[$x]['slots'][$a]['from_time'];
                //                         $data1['to_time'] = $arr[$x]['slots'][$a]['to_time'];
                //                         $data1['session'] = $getData->session;
                //                         $data1['created_date_time'] = date("Y-m-d H:i:s");
                //                         $data1['modified_date_time'] = date("Y-m-d H:i:s");
                //                         $this->Generic_model->updateData("clinic_doctor_weekday_slots",
                //                          $data1,
                //                         array('clinic_doctor_weekday_slot_id' => $getData->clinic_doctor_weekday_slot_id));
                //                     }
                //                     $this->response(array('code'=>'200','message'=>'Sucess','result'=>'Successfully Updated'));
                //                 }
                //                 else
                //                 {
    
                //                     $clinincDocInfo = $this->db->query("select * from
                //                     clinic_doctor where clinic_id='".$clinic_id."'
                //                     and doctor_id='".$doctor_id."'")->row();
                //                    $cdwInfo = $this->db->query("select * from clinic_doctor_weekdays where clinic_doctor_id='".$clinincDocInfo->clinic_doctor_id."' and weekday='".$arr[$x]['day']."'and slot='video call'")->row();
                //                    if(count($cdwInfo)>0)
                //                    {
                //                        $cdw_id = $cdwInfo->clinic_doctor_weekday_id;
                //                        $data1['clinic_doctor_weekday_id'] = $cdw_id;
                //                        $data1['from_time'] =  $arr[$x]['slots'][$a]['from_time'];
                //                        $data1['to_time'] = $arr[$x]['slots'][$a]['to_time'];
                //                        $data1['session'] = $arr[$x]['slots'][$a]['session'];
                //                        $data1['created_date_time'] = date("Y-m-d H:i:s");
                //                        $data1['modified_date_time'] = date("Y-m-d H:i:s");
                //                        $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);   
                //                        $this->response(array('code'=>'200','message'=>'Sucess','result'=>'Successfully Inserted')); 
                //                    }
                //                    else
                //                    {
                //                        $data['weekday'] = $arr[$x]['day'];
                //                        $data['slot'] = 'video call';
                //                        $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
                //                        $data['created_date_time'] = date("Y-m-d H:i:s");
                //                        $data['modified_date_time'] = date("Y-m-d H:i:s");
                //                        $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
                //                        $data1['clinic_doctor_weekday_id'] = $cdw_id;
                //                        $data1['from_time'] =$arr[$x]['slots'][$a]['from_time'];
                //                        $data1['to_time'] = $arr[$x]['slots'][$a]['to_time'];
                //                        $data1['session'] =  $arr[$x]['slots'][$a]['session'];
                //                        $data1['created_date_time'] = date("Y-m-d H:i:s");
                //                        $data1['modified_date_time'] = date("Y-m-d H:i:s");
                //                        $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
                //                        $this->response(array('code'=>'200','message'=>'Sucess','result'=>'Successfully Inserted'));
                //                    }
    
                //                     // $clinincDocInfo = $this->db->query("select * from
                //                     //  clinic_doctor where clinic_id='".$clinic_id."'
                //                     //  and doctor_id='".$doctor_id."'")->row();
                //                     //  if(count($clinincDocInfo)>0)
                //                     //  {
                //                     //     $data['weekday'] = $arr[$x]['day'];
                //                     //     $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
                //                     //     $data['slot'] = 'video call';
                //                     //     $data['created_date_time'] = date("Y-m-d H:i:s");
                //                     //     $data['modified_date_time'] = date("Y-m-d H:i:s");
                //                     //     $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
                //                     //     $data1['clinic_doctor_weekday_id'] = $cdw_id;
                //                     //     $data1['from_time'] = $arr[$x]['slots'][$a]['from_time'];
                //                     //     $data1['to_time'] = $arr[$x]['slots'][$a]['to_time'];
                //                     //     $data1['session'] =  $arr[$x]['slots'][$a]['session'];
                //                     //     $data1['created_date_time'] = date("Y-m-d H:i:s");
                //                     //     $data1['modified_date_time'] = date("Y-m-d H:i:s");
                //                     //     $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
                //                     //     $this->response(array('code'=>'200','message'=>'Sucess','result'=>'Successfully Inserted'));
                //                     //  }
                //                     //  else{
                //                     //     $this->response(array('code'=>'200','message'=>'Sucess','result'=>'Doctor,Clinic Id are not matching'));
                //                     //  }
                       
                //                 }
                            
                //             }
                //       }            
                // }
            }
        }

        // web walkin slots post
        public function walkinDoctorSlots_post()
        {
         
            // print_r($_POST['slots']);
            // print_r($_POST['morning']);
            // print_r($_POST['afternoon']);
            // print_r($_POST['evening']);
            // print_r($_POST['consultationfees']);
            // print_r($_POST['scheduling']);
            // print_r($_POST['consultationscheduling']);
            // print_r($_POST);
            // echo $_POST($doctorId);
            // echo $_POST($clinicId);

            if(isset($_POST))
            {
                extract($_POST);
                $user_id = trim($doctorId, '"');
                $clinic_id = trim($clinicId, '"');
                $slotss = json_decode($slots,true);
                // $morningFrom = date("H:i:s",strtotime(trim($morningFrom, '"')));
                // $morningTo = date("H:i:s",strtotime(trim($morningTo, '"')));
                // $afternoonFrom = date("H:i:s",strtotime(trim($afternoonFrom, '"')));
                // $afternoonTo = date("H:i:s", strtotime(trim($afternoonTo,'"')));
                // $eveningFrom = date("H:i:s",strtotime(trim($eveningFrom,'"')));
                // $eveningTo = date("H:i:s",strtotime(trim($eveningTo,'"')));
    
                $check = $this->db->query("select * from doctors where doctor_id='".$user_id."'")->row();
                if(count($check)>0)
                {
                    $clinincDocInfo = $this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."' and doctor_id='".$user_id."'")->row();
                    if(count($clinincDocInfo)>0)
                    {
                        $con['consulting_fee'] = trim($consultationfees, '"');
                        $con['consulting_time'] = $consultationscheduling;
                        $this->Generic_model->updateData('clinic_doctor',$con,array('clinic_doctor_id'=>$clinincDocInfo->clinic_doctor_id));
                        $morning = json_decode(trim($morning, '"'));
                        $afternoon = json_decode(trim($afternoon, '"'));
                        $evening = json_decode(trim($evening, '"'));
                        // echo count($morning)."*".count($afternoon)."*".count($evening);
                        if(count($morning)>0)
                        {
                            unset($data);unset($data1);
                            foreach($morning as $mng)
                            {
                                if(count($slotss)>0){
                                    for($x = 0; $x <= count($slotss); $x++)
                                    {
                                       if($slotss[$x]['count'] == $mng){
                                           
                                $cdwInfo = $this->db->query("select * from clinic_doctor_weekdays where clinic_doctor_id='".$clinincDocInfo->clinic_doctor_id."' and weekday='".$mng."' and slot='walkin'")->row();
                                if(count($cdwInfo)>0)
                                {
                                    $cdw_id = $cdwInfo->clinic_doctor_weekday_id;
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] = $slotss[$x]['sessions'][0]['Morning'][0]['from_time'];
                                    $data1['to_time'] = $slotss[$x]['sessions'][0]['Morning'][0]['to_time'];
                                    $data1['session'] = 'morning';
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);    
                                }
                                else
                                {
                                    $data['weekday'] = $mng;
                                    $data['slot'] = 'walkin';
                                    $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
                                    $data['created_date_time'] = date("Y-m-d H:i:s");
                                    $data['modified_date_time'] = date("Y-m-d H:i:s");
                                    $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] = $slotss[$x]['sessions'][0]['Morning'][0]['from_time'];
                                    $data1['to_time'] = $slotss[$x]['sessions'][0]['Morning'][0]['to_time'];
                                    $data1['session'] = 'morning';
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
                                }
                                       }
                                    }
                                }

                            }
                        }
        
                        if(count($afternoon)>0)
                        {
                            unset($data);unset($data1);
                            foreach($afternoon as $aft)
                            {
                                if(count($slotss)>0){
                                    for($x = 0; $x <= count($slotss); $x++)
                                    {
                                       if($slotss[$x]['count'] == $aft){
                                           
                                $cdwInfo = $this->db->query("select * from clinic_doctor_weekdays where clinic_doctor_id='".$clinincDocInfo->clinic_doctor_id."' and weekday='".$aft."' and slot='walkin'")->row();
                                if(count($cdwInfo)>0)
                                {
                                    $cdw_id = $cdwInfo->clinic_doctor_weekday_id;
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] = $slotss[$x]['sessions'][1]['Afternoon'][0]['from_time'];
                                    $data1['to_time'] = $slotss[$x]['sessions'][1]['Afternoon'][0]['to_time'];
                                    $data1['session'] = 'afternoon';
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1); 
                                }
                                else
                                {
                                    $data['weekday'] = $aft;
                                    $data['slot'] = 'walkin';
                                    $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
                                    $data['created_date_time'] = date("Y-m-d H:i:s");
                                    $data['modified_date_time'] = date("Y-m-d H:i:s");
                                    $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] = $slotss[$x]['sessions'][1]['Afternoon'][0]['from_time'];
                                    $data1['to_time'] =  $slotss[$x]['sessions'][1]['Afternoon'][0]['to_time'];
                                    $data1['session'] = 'afternoon';
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
                                }
                                       }
                                    }
                                }

                    
                            }
                        }
                        if(count($evening)>0)
                        {
                            unset($data);unset($data1);
                            foreach($evening as $evng)
                            {

                                if(count($slotss)>0){
                                    for($x = 0; $x <= count($slotss); $x++)
                                    {
                                       if($slotss[$x]['count'] == $evng){
                                           
                                $cdwInfo = $this->db->query("select * from clinic_doctor_weekdays where clinic_doctor_id='".$clinincDocInfo->clinic_doctor_id."' and weekday='".$evng."' and slot='walkin'")->row();
                                if(count($cdwInfo)>0)
                                {
                                    $cdw_id = $cdwInfo->clinic_doctor_weekday_id;
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] =$slotss[$x]['sessions'][2]['Evening'][0]['from_time'];
                                    $data1['to_time'] = $slotss[$x]['sessions'][2]['Evening'][0]['to_time'];
                                    $data1['session'] = 'evening';
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1); 
                                }
                                else
                                {
                                    $data['weekday'] = $evng;
                                    $data['slot'] = 'walkin';
                                    $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
                                    $data['created_date_time'] = date("Y-m-d H:i:s");
                                    $data['modified_date_time'] = date("Y-m-d H:i:s");
                                    $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] = $slotss[$x]['sessions'][2]['Evening'][0]['from_time'];
                                    $data1['to_time'] =  $slotss[$x]['sessions'][2]['Evening'][0]['to_time'];
                                    $data1['session'] = 'evening';
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
                                }
                                       }
                                    }
                                }

                            }
                        }    
                        $param = "Slots Created";
                        $this->response(array('code'=>'200','message'=>'Success Yahoo','result'=>$param));
                    }
                    else
                    {
                        $param = "Clinic Doctor Mapping Not Found";
                        $this->response(array('code'=>'201','message'=>'Error Ocuured','result'=>$param));
                    }
                }
                else
                {
                    $param = "Doctor Not Found";
                    $this->response(array('code'=>'201','message'=>'Error Ocuured','result'=>$param));
                }
            }
        }

        public function walkinDoctorSlotsMorEdit_post()
        {
        
            if(isset($_POST))
            {
                extract($_POST);
                $user_id = trim($doctor_id, '"');
                $clinic_id = trim($clinic_id, '"');
                $slotss = json_decode($slots,true);
                $check = $this->db->query("select * from doctors where doctor_id='".$user_id."'")->row();
                if(count($check)>0)
                {
                    $clinincDocInfo = $this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."' and doctor_id='".$user_id."'")->row();
                    if(count($clinincDocInfo)>0)
                    {
                        // $con['consulting_fee'] = trim($consultationfees, '"');
                        // $con['consulting_time'] = round($scheduling/$consultationscheduling);
                        // $this->Generic_model->updateData('clinic_doctor',$con,array('clinic_doctor_id'=>$clinincDocInfo->clinic_doctor_id));
                        $morning = json_decode(trim($morning, '"'));
                        // $afternoon = json_decode(trim($afternoon, '"'));
                        // $evening = json_decode(trim($evening, '"'));
                        // echo count($morning)."*".count($afternoon)."*".count($evening);
                        if(count($morning)>0)
                        {
                            unset($data);unset($data1);
                            foreach($morning as $mng)
                            {
                                if(count($slotss)>0){
                                    for($x = 0; $x <= count($slotss); $x++)
                                    {
                                       if($slotss[$x]['count'] == $mng){
                                           
                                $cdwInfo = $this->db->query("select * from clinic_doctor_weekdays where clinic_doctor_id='".$clinincDocInfo->clinic_doctor_id."' and weekday='".$mng."' and slot='walkin'")->row();
                                if(count($cdwInfo)>0)
                                {
                                    $cdw_id = $cdwInfo->clinic_doctor_weekday_id;
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] = $slotss[$x]['sessions'][0]['Morning'][0]['from_time'];
                                    $data1['to_time'] = $slotss[$x]['sessions'][0]['Morning'][0]['to_time'];
                                    $data1['session'] = 'morning';
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);    
                                }
                                else
                                {
                                    $data['weekday'] = $mng;
                                    $data['slot'] = 'walkin';
                                    $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
                                    $data['created_date_time'] = date("Y-m-d H:i:s");
                                    $data['modified_date_time'] = date("Y-m-d H:i:s");
                                    $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] = $slotss[$x]['sessions'][0]['Morning'][0]['from_time'];
                                    $data1['to_time'] = $slotss[$x]['sessions'][0]['Morning'][0]['to_time'];
                                    $data1['session'] = 'morning';
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
                                }
                                       }
                                    }
                                }

                            }
                        }
        
                        $param = "Slots Created";
                        $this->response(array('code'=>'200','message'=>'Success Yahoo','result'=>$param));
                    }
                    else
                    {
                        $param = "Clinic Doctor Mapping Not Found";
                        $this->response(array('code'=>'201','message'=>'Error Ocuured','result'=>$param));
                    }
                }
                else
                {
                    $param = "Doctor Not Found";
                    $this->response(array('code'=>'201','message'=>'Error Ocuured','result'=>$param));
                }
            }
        }

        public function walkinDoctorSlotsAftEdit_post()
        {
        
            if(isset($_POST))
            {
                extract($_POST);
                $user_id = trim($doctor_id, '"');
                $clinic_id = trim($clinic_id, '"');
                $slotss = json_decode($slots,true);
                $check = $this->db->query("select * from doctors where doctor_id='".$user_id."'")->row();
                if(count($check)>0)
                {
                    $clinincDocInfo = $this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."' and doctor_id='".$user_id."'")->row();
                    if(count($clinincDocInfo)>0)
                    {
                        // $con['consulting_fee'] = trim($consultationfees, '"');
                        // $con['consulting_time'] = round($scheduling/$consultationscheduling);
                        // $this->Generic_model->updateData('clinic_doctor',$con,array('clinic_doctor_id'=>$clinincDocInfo->clinic_doctor_id));
                        // $morning = json_decode(trim($morning, '"'));
                        $afternoon = json_decode(trim($afternoon, '"'));
                        // $evening = json_decode(trim($evening, '"'));
                        // echo count($morning)."*".count($afternoon)."*".count($evening);
                   
        
                        if(count($afternoon)>0)
                        {
                            unset($data);unset($data1);
                            foreach($afternoon as $aft)
                            {
                                if(count($slotss)>0){
                                    for($x = 0; $x <= count($slotss); $x++)
                                    {
                                       if($slotss[$x]['count'] == $aft){
                                           
                                $cdwInfo = $this->db->query("select * from clinic_doctor_weekdays where clinic_doctor_id='".$clinincDocInfo->clinic_doctor_id."' and weekday='".$aft."' and slot='walkin'")->row();
                                if(count($cdwInfo)>0)
                                {
                                    $cdw_id = $cdwInfo->clinic_doctor_weekday_id;
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] = $slotss[$x]['sessions'][1]['Afternoon'][0]['from_time'];
                                    $data1['to_time'] = $slotss[$x]['sessions'][1]['Afternoon'][0]['to_time'];
                                    $data1['session'] = 'afternoon';
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1); 
                                }
                                else
                                {
                                    $data['weekday'] = $aft;
                                    $data['slot'] = 'walkin';
                                    $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
                                    $data['created_date_time'] = date("Y-m-d H:i:s");
                                    $data['modified_date_time'] = date("Y-m-d H:i:s");
                                    $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] = $slotss[$x]['sessions'][1]['Afternoon'][0]['from_time'];
                                    $data1['to_time'] =  $slotss[$x]['sessions'][1]['Afternoon'][0]['to_time'];
                                    $data1['session'] = 'afternoon';
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
                                }
                                       }
                                    }
                                }

                    
                            }
                        }
                     
                        $param = "Slots Created";
                        $this->response(array('code'=>'200','message'=>'Success Yahoo','result'=>$param));
                    }
                    else
                    {
                        $param = "Clinic Doctor Mapping Not Found";
                        $this->response(array('code'=>'201','message'=>'Error Ocuured','result'=>$param));
                    }
                }
                else
                {
                    $param = "Doctor Not Found";
                    $this->response(array('code'=>'201','message'=>'Error Ocuured','result'=>$param));
                }
            }
        }

        public function walkinDoctorSlotsEvnEdit_post()
        {
        
            if(isset($_POST))
            {
                extract($_POST);
                $user_id = trim($doctor_id, '"');
                $clinic_id = trim($clinic_id, '"');
                $slotss = json_decode($slots,true);
                $check = $this->db->query("select * from doctors where doctor_id='".$user_id."'")->row();
                if(count($check)>0)
                {
                    $clinincDocInfo = $this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."' and doctor_id='".$user_id."'")->row();
                    if(count($clinincDocInfo)>0)
                    {
                        // $con['consulting_fee'] = trim($consultationfees, '"');
                        // $con['consulting_time'] = round($scheduling/$consultationscheduling);
                        // $this->Generic_model->updateData('clinic_doctor',$con,array('clinic_doctor_id'=>$clinincDocInfo->clinic_doctor_id));
                        // $morning = json_decode(trim($morning, '"'));
                        // $afternoon = json_decode(trim($afternoon, '"'));
                        $evening = json_decode(trim($evening, '"'));
                        // echo count($morning)."*".count($afternoon)."*".count($evening);
                   
        
                        if(count($evening)>0)
                        {
                            unset($data);unset($data1);
                            foreach($evening as $evng)
                            {

                                if(count($slotss)>0){
                                    for($x = 0; $x <= count($slotss); $x++)
                                    {
                                       if($slotss[$x]['count'] == $evng){
                                           
                                $cdwInfo = $this->db->query("select * from clinic_doctor_weekdays where clinic_doctor_id='".$clinincDocInfo->clinic_doctor_id."' and weekday='".$evng."' and slot='walkin'")->row();
                                if(count($cdwInfo)>0)
                                {
                                    $cdw_id = $cdwInfo->clinic_doctor_weekday_id;
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] =$slotss[$x]['sessions'][2]['Evening'][0]['from_time'];
                                    $data1['to_time'] = $slotss[$x]['sessions'][2]['Evening'][0]['to_time'];
                                    $data1['session'] = 'evening';
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1); 
                                }
                                else
                                {
                                    $data['weekday'] = $evng;
                                    $data['slot'] = 'walkin';
                                    $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
                                    $data['created_date_time'] = date("Y-m-d H:i:s");
                                    $data['modified_date_time'] = date("Y-m-d H:i:s");
                                    $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] = $slotss[$x]['sessions'][2]['Evening'][0]['from_time'];
                                    $data1['to_time'] =  $slotss[$x]['sessions'][2]['Evening'][0]['to_time'];
                                    $data1['session'] = 'evening';
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
                                }
                                       }
                                    }
                                }

                            }
                        }  
                     
                        $param = "Slots Created";
                        $this->response(array('code'=>'200','message'=>'Success Yahoo','result'=>$param));
                    }
                    else
                    {
                        $param = "Clinic Doctor Mapping Not Found";
                        $this->response(array('code'=>'201','message'=>'Error Ocuured','result'=>$param));
                    }
                }
                else
                {
                    $param = "Doctor Not Found";
                    $this->response(array('code'=>'201','message'=>'Error Ocuured','result'=>$param));
                }
            }
        }

        public function teleDoctorSlotsMorEdit_post()
        {
        
            if(isset($_POST))
            {
                extract($_POST);
                $user_id = trim($doctor_id, '"');
                $clinic_id = trim($clinic_id, '"');
                $slotss = json_decode($slots,true);
                $check = $this->db->query("select * from doctors where doctor_id='".$user_id."'")->row();
                if(count($check)>0)
                {
                    $clinincDocInfo = $this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."' and doctor_id='".$user_id."'")->row();
                    if(count($clinincDocInfo)>0)
                    {
                        // $con['consulting_fee'] = trim($consultationfees, '"');
                        // $con['consulting_time'] = round($scheduling/$consultationscheduling);
                        // $this->Generic_model->updateData('clinic_doctor',$con,array('clinic_doctor_id'=>$clinincDocInfo->clinic_doctor_id));
                        $morning = json_decode(trim($morning, '"'));
                        // $afternoon = json_decode(trim($afternoon, '"'));
                        // $evening = json_decode(trim($evening, '"'));
                        // echo count($morning)."*".count($afternoon)."*".count($evening);
                        if(count($morning)>0)
                        {
                            unset($data);unset($data1);
                            foreach($morning as $mng)
                            {
                                if(count($slotss)>0){
                                    for($x = 0; $x <= count($slotss); $x++)
                                    {
                                       if($slotss[$x]['count'] == $mng){
                                           
                                $cdwInfo = $this->db->query("select * from clinic_doctor_weekdays where clinic_doctor_id='".$clinincDocInfo->clinic_doctor_id."' and weekday='".$mng."' and slot='video call'")->row();
                                if(count($cdwInfo)>0)
                                {
                                    $cdw_id = $cdwInfo->clinic_doctor_weekday_id;
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] = $slotss[$x]['sessions'][0]['Morning'][0]['from_time'];
                                    $data1['to_time'] = $slotss[$x]['sessions'][0]['Morning'][0]['to_time'];
                                    $data1['session'] = 'morning';
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);    
                                }
                                else
                                {
                                    $data['weekday'] = $mng;
                                    $data['slot'] = 'video call';
                                    $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
                                    $data['created_date_time'] = date("Y-m-d H:i:s");
                                    $data['modified_date_time'] = date("Y-m-d H:i:s");
                                    $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] = $slotss[$x]['sessions'][0]['Morning'][0]['from_time'];
                                    $data1['to_time'] = $slotss[$x]['sessions'][0]['Morning'][0]['to_time'];
                                    $data1['session'] = 'morning';
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
                                }
                                       }
                                    }
                                }

                            }
                        }
        
                        $param = "Slots Created";
                        $this->response(array('code'=>'200','message'=>'Success Yahoo','result'=>$param));
                    }
                    else
                    {
                        $param = "Clinic Doctor Mapping Not Found";
                        $this->response(array('code'=>'201','message'=>'Error Ocuured','result'=>$param));
                    }
                }
                else
                {
                    $param = "Doctor Not Found";
                    $this->response(array('code'=>'201','message'=>'Error Ocuured','result'=>$param));
                }
            }
        }

        public function teleDoctorSlotsAftEdit_post()
        {
        
            if(isset($_POST))
            {
                extract($_POST);
                $user_id = trim($doctor_id, '"');
                $clinic_id = trim($clinic_id, '"');
                $slotss = json_decode($slots,true);
                $check = $this->db->query("select * from doctors where doctor_id='".$user_id."'")->row();
                if(count($check)>0)
                {
                    $clinincDocInfo = $this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."' and doctor_id='".$user_id."'")->row();
                    if(count($clinincDocInfo)>0)
                    {
                        // $con['consulting_fee'] = trim($consultationfees, '"');
                        // $con['consulting_time'] = round($scheduling/$consultationscheduling);
                        // $this->Generic_model->updateData('clinic_doctor',$con,array('clinic_doctor_id'=>$clinincDocInfo->clinic_doctor_id));
                        // $morning = json_decode(trim($morning, '"'));
                        $afternoon = json_decode(trim($afternoon, '"'));
                        // $evening = json_decode(trim($evening, '"'));
                        // echo count($morning)."*".count($afternoon)."*".count($evening);
                   
        
                        if(count($afternoon)>0)
                        {
                            unset($data);unset($data1);
                            foreach($afternoon as $aft)
                            {
                                if(count($slotss)>0){
                                    for($x = 0; $x <= count($slotss); $x++)
                                    {
                                       if($slotss[$x]['count'] == $aft){
                                           
                                $cdwInfo = $this->db->query("select * from clinic_doctor_weekdays where clinic_doctor_id='".$clinincDocInfo->clinic_doctor_id."' and weekday='".$aft."' and slot='video call'")->row();
                                if(count($cdwInfo)>0)
                                {
                                    $cdw_id = $cdwInfo->clinic_doctor_weekday_id;
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] = $slotss[$x]['sessions'][1]['Afternoon'][0]['from_time'];
                                    $data1['to_time'] = $slotss[$x]['sessions'][1]['Afternoon'][0]['to_time'];
                                    $data1['session'] = 'afternoon';
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1); 
                                }
                                else
                                {
                                    $data['weekday'] = $aft;
                                    $data['slot'] = 'video call';
                                    $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
                                    $data['created_date_time'] = date("Y-m-d H:i:s");
                                    $data['modified_date_time'] = date("Y-m-d H:i:s");
                                    $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] = $slotss[$x]['sessions'][1]['Afternoon'][0]['from_time'];
                                    $data1['to_time'] =  $slotss[$x]['sessions'][1]['Afternoon'][0]['to_time'];
                                    $data1['session'] = 'afternoon';
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
                                }
                                       }
                                    }
                                }

                    
                            }
                        }
                     
                        $param = "Slots Created";
                        $this->response(array('code'=>'200','message'=>'Success Yahoo','result'=>$param));
                    }
                    else
                    {
                        $param = "Clinic Doctor Mapping Not Found";
                        $this->response(array('code'=>'201','message'=>'Error Ocuured','result'=>$param));
                    }
                }
                else
                {
                    $param = "Doctor Not Found";
                    $this->response(array('code'=>'201','message'=>'Error Ocuured','result'=>$param));
                }
            }
        }

        public function teleDoctorSlotsEvnEdit_post()
        {
        
            if(isset($_POST))
            {
                extract($_POST);
                $user_id = trim($doctor_id, '"');
                $clinic_id = trim($clinic_id, '"');
                $slotss = json_decode($slots,true);
                $check = $this->db->query("select * from doctors where doctor_id='".$user_id."'")->row();
                if(count($check)>0)
                {
                    $clinincDocInfo = $this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."' and doctor_id='".$user_id."'")->row();
                    if(count($clinincDocInfo)>0)
                    {
                        // $con['consulting_fee'] = trim($consultationfees, '"');
                        // $con['consulting_time'] = round($scheduling/$consultationscheduling);
                        // $this->Generic_model->updateData('clinic_doctor',$con,array('clinic_doctor_id'=>$clinincDocInfo->clinic_doctor_id));
                        // $morning = json_decode(trim($morning, '"'));
                        // $afternoon = json_decode(trim($afternoon, '"'));
                        $evening = json_decode(trim($evening, '"'));
                        // echo count($morning)."*".count($afternoon)."*".count($evening);
                   
        
                        if(count($evening)>0)
                        {
                            unset($data);unset($data1);
                            foreach($evening as $evng)
                            {

                                if(count($slotss)>0){
                                    for($x = 0; $x <= count($slotss); $x++)
                                    {
                                       if($slotss[$x]['count'] == $evng){
                                           
                                $cdwInfo = $this->db->query("select * from clinic_doctor_weekdays where clinic_doctor_id='".$clinincDocInfo->clinic_doctor_id."' and weekday='".$evng."' and slot='video call'")->row();
                                if(count($cdwInfo)>0)
                                {
                                    $cdw_id = $cdwInfo->clinic_doctor_weekday_id;
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] =$slotss[$x]['sessions'][2]['Evening'][0]['from_time'];
                                    $data1['to_time'] = $slotss[$x]['sessions'][2]['Evening'][0]['to_time'];
                                    $data1['session'] = 'evening';
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1); 
                                }
                                else
                                {
                                    $data['weekday'] = $evng;
                                    $data['slot'] = 'video call';
                                    $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
                                    $data['created_date_time'] = date("Y-m-d H:i:s");
                                    $data['modified_date_time'] = date("Y-m-d H:i:s");
                                    $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] = $slotss[$x]['sessions'][2]['Evening'][0]['from_time'];
                                    $data1['to_time'] =  $slotss[$x]['sessions'][2]['Evening'][0]['to_time'];
                                    $data1['session'] = 'evening';
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
                                }
                                       }
                                    }
                                }

                            }
                        }  
                     
                        $param = "Slots Created";
                        $this->response(array('code'=>'200','message'=>'Success Yahoo','result'=>$param));
                    }
                    else
                    {
                        $param = "Clinic Doctor Mapping Not Found";
                        $this->response(array('code'=>'201','message'=>'Error Ocuured','result'=>$param));
                    }
                }
                else
                {
                    $param = "Doctor Not Found";
                    $this->response(array('code'=>'201','message'=>'Error Ocuured','result'=>$param));
                }
            }
        }

        public function teleDoctorSlots_post()
        {
         
            // print_r($_POST['slots']);
            // print_r($_POST['morning']);
            // print_r($_POST['afternoon']);
            // print_r($_POST['evening']);
            // print_r($_POST['consultationfees']);
            // print_r($_POST['scheduling']);
            // print_r($_POST['consultationscheduling']);
            // print_r($_POST);
            // echo $_POST($doctorId);
            // echo $_POST($clinicId);

            if(isset($_POST))
            {
                extract($_POST);
                $user_id = trim($doctorId, '"');
                $clinic_id = trim($clinicId, '"');
                $slotss = json_decode($slots,true);
                // $morningFrom = date("H:i:s",strtotime(trim($morningFrom, '"')));
                // $morningTo = date("H:i:s",strtotime(trim($morningTo, '"')));
                // $afternoonFrom = date("H:i:s",strtotime(trim($afternoonFrom, '"')));
                // $afternoonTo = date("H:i:s", strtotime(trim($afternoonTo,'"')));
                // $eveningFrom = date("H:i:s",strtotime(trim($eveningFrom,'"')));
                // $eveningTo = date("H:i:s",strtotime(trim($eveningTo,'"')));
    
                $check = $this->db->query("select * from doctors where doctor_id='".$user_id."'")->row();
                if(count($check)>0)
                {
                    $clinincDocInfo = $this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."' and doctor_id='".$user_id."'")->row();
                    if(count($clinincDocInfo)>0)
                    {
                        $con['online_consulting_fee'] = trim($teleconsultationfees, '"');
                        $con['online_consulting_time'] = $teleconsultationscheduling;
                        $this->Generic_model->updateData('clinic_doctor',$con,array('clinic_doctor_id'=>$clinincDocInfo->clinic_doctor_id));
                        $morning = json_decode(trim($morning, '"'));
                        $afternoon = json_decode(trim($afternoon, '"'));
                        $evening = json_decode(trim($evening, '"'));
                        // echo count($morning)."*".count($afternoon)."*".count($evening);
                        if(count($morning)>0)
                        {
                            unset($data);unset($data1);
                            foreach($morning as $mng)
                            {
                                if(count($slotss)>0){
                                    for($x = 0; $x <= count($slotss); $x++)
                                    {
                                       if($slotss[$x]['count'] == $mng){
                                           
                                $cdwInfo = $this->db->query("select * from clinic_doctor_weekdays where clinic_doctor_id='".$clinincDocInfo->clinic_doctor_id."' and weekday='".$mng."' and slot='video call'")->row();
                                if(count($cdwInfo)>0)
                                {
                                    $cdw_id = $cdwInfo->clinic_doctor_weekday_id;
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] = $slotss[$x]['sessions'][0]['Morning'][0]['from_time'];
                                    $data1['to_time'] = $slotss[$x]['sessions'][0]['Morning'][0]['to_time'];
                                    $data1['session'] = 'morning';
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);    
                                }
                                else
                                {
                                    $data['weekday'] = $mng;
                                    $data['slot'] = 'video call';
                                    $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
                                    $data['created_date_time'] = date("Y-m-d H:i:s");
                                    $data['modified_date_time'] = date("Y-m-d H:i:s");
                                    $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] = $slotss[$x]['sessions'][0]['Morning'][0]['from_time'];
                                    $data1['to_time'] = $slotss[$x]['sessions'][0]['Morning'][0]['to_time'];
                                    $data1['session'] = 'morning';
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
                                }
                                       }
                                    }
                                }

                            }
                        }
        
                        if(count($afternoon)>0)
                        {
                            unset($data);unset($data1);
                            foreach($afternoon as $aft)
                            {
                                if(count($slotss)>0){
                                    for($x = 0; $x <= count($slotss); $x++)
                                    {
                                       if($slotss[$x]['count'] == $aft){
                                           
                                $cdwInfo = $this->db->query("select * from clinic_doctor_weekdays where clinic_doctor_id='".$clinincDocInfo->clinic_doctor_id."' and weekday='".$aft."' and slot='video call'")->row();
                                if(count($cdwInfo)>0)
                                {
                                    $cdw_id = $cdwInfo->clinic_doctor_weekday_id;
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] = $slotss[$x]['sessions'][1]['Afternoon'][0]['from_time'];
                                    $data1['to_time'] = $slotss[$x]['sessions'][1]['Afternoon'][0]['to_time'];
                                    $data1['session'] = 'afternoon';
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1); 
                                }
                                else
                                {
                                    $data['weekday'] = $aft;
                                    $data['slot'] = 'video call';
                                    $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
                                    $data['created_date_time'] = date("Y-m-d H:i:s");
                                    $data['modified_date_time'] = date("Y-m-d H:i:s");
                                    $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] = $slotss[$x]['sessions'][1]['Afternoon'][0]['from_time'];
                                    $data1['to_time'] =  $slotss[$x]['sessions'][1]['Afternoon'][0]['to_time'];
                                    $data1['session'] = 'afternoon';
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
                                }
                                       }
                                    }
                                }

                    
                            }
                        }
                        if(count($evening)>0)
                        {
                            unset($data);unset($data1);
                            foreach($evening as $evng)
                            {

                                if(count($slotss)>0){
                                    for($x = 0; $x <= count($slotss); $x++)
                                    {
                                       if($slotss[$x]['count'] == $evng){
                                           
                                $cdwInfo = $this->db->query("select * from clinic_doctor_weekdays where clinic_doctor_id='".$clinincDocInfo->clinic_doctor_id."' and weekday='".$evng."' and slot='video call'")->row();
                                if(count($cdwInfo)>0)
                                {
                                    $cdw_id = $cdwInfo->clinic_doctor_weekday_id;
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] =$slotss[$x]['sessions'][2]['Evening'][0]['from_time'];
                                    $data1['to_time'] = $slotss[$x]['sessions'][2]['Evening'][0]['to_time'];
                                    $data1['session'] = 'evening';
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1); 
                                }
                                else
                                {
                                    $data['weekday'] = $evng;
                                    $data['slot'] = 'video call';
                                    $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
                                    $data['created_date_time'] = date("Y-m-d H:i:s");
                                    $data['modified_date_time'] = date("Y-m-d H:i:s");
                                    $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
                                    $data1['clinic_doctor_weekday_id'] = $cdw_id;
                                    $data1['from_time'] = $slotss[$x]['sessions'][2]['Evening'][0]['from_time'];
                                    $data1['to_time'] =  $slotss[$x]['sessions'][2]['Evening'][0]['to_time'];
                                    $data1['session'] = 'evening';
                                    $data1['created_date_time'] = date("Y-m-d H:i:s");
                                    $data1['modified_date_time'] = date("Y-m-d H:i:s");
                                    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
                                }
                                       }
                                    }
                                }

                            }
                        }    
                        $param = "Slots Created";
                        $this->response(array('code'=>'200','message'=>'Success Yahoo','result'=>$param));
                    }
                    else
                    {
                        $param = "Clinic Doctor Mapping Not Found";
                        $this->response(array('code'=>'201','message'=>'Error Ocuured','result'=>$param));
                    }
                }
                else
                {
                    $param = "Doctor Not Found";
                    $this->response(array('code'=>'201','message'=>'Error Ocuured','result'=>$param));
                }
            }
        }

        public function getGeneralInfo_get($doctor_id,$clinic_id)
        {
            $clinincDocInfo = $this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."' 
            and doctor_id='".$doctor_id."'")->row();

            $registartion_fees = $this->db->query("select * from clinics where clinic_id='".$clinic_id."'")->row();

            if(count($clinincDocInfo)>0)
            {
                $data['walkinConsultationFees']=$clinincDocInfo->consulting_fee;
                $data['teleConsultationFees']=$clinincDocInfo->online_consulting_fee;
                $data['walkinConsultationTime']=$clinincDocInfo->consulting_time;
                $data['teleConsultationTime']=$clinincDocInfo->online_consulting_time;
                $data['registration_fees']=$registartion_fees->registration_fee;
                $this->response(array('code'=>'200','message'=>'Success','result'=>$data));
            }else{
                $this->response(array('code'=>'201','message'=>'Fail','result'=>'Not Found'));
            }
        }

        public function editModifiedTime_post()
        {
            extract($_POST);
            // $day = trim($day, '"');
            $data1['from_time'] = $from;
            $data1['to_time'] = $to;
            $data1['modified_date_time'] = date("Y-m-d H:i:s");
            $this->Generic_model->updateData('clinic_doctor_weekday_slots',$data1,array('clinic_doctor_weekday_slot_id'=>$day));
            $this->response(array('code'=>'200','message'=>'Success','result'=>'success'));
        }

        public function generalSettingsData_post()
        {
            extract($_POST);
            $walking_fees = trim($wf, '"');
            $walking_time = trim($wt, '"');
            $tele_fees = trim($tf, '"');
            $tele_time = trim($tt, '"');
            $registration_fees = trim($rf, '"');
            $doctor_id = trim($dId, '"');
            $clinic_id = trim($cId, '"');

            if($registration_fees != '' || $registration_fees != 'undefined')
            {
                $data1['registration_fee'] =  $registration_fees;
                $this->Generic_model->updateData('clinics',$data1,array('clinic_id'=>$clinic_id));
            }

            if($walking_fees != '' || $walking_fees != 'undefined')
            {
                $clinincDocInfo = $this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."' 
                and doctor_id='".$doctor_id."'")->row();

                if(count($clinincDocInfo)>0)
                {
                    $data['consulting_fee'] =  $walking_fees;
                    $this->Generic_model->updateData('clinic_doctor',$data,array('clinic_doctor_id'=>$clinincDocInfo->clinic_doctor_id));
                }

            }

            if($walking_time != '' || $walking_time != 'undefined')
            {
                $clinincDocInfo = $this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."' 
                and doctor_id='".$doctor_id."'")->row();

                if(count($clinincDocInfo)>0)
                {
                    $dat['consulting_time'] =  $walking_time;
                    $this->Generic_model->updateData('clinic_doctor',$dat,array('clinic_doctor_id'=>$clinincDocInfo->clinic_doctor_id));
                }

            }

            
            if($tele_fees != '' || $tele_fees != 'undefined')
            {
                $clinincDocInfo = $this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."' 
                and doctor_id='".$doctor_id."'")->row();

                if(count($clinincDocInfo)>0)
                {
                    $data11['online_consulting_fee'] =  $tele_fees;
                    $this->Generic_model->updateData('clinic_doctor',$data11,array('clinic_doctor_id'=>$clinincDocInfo->clinic_doctor_id));
                }

            }

            if($tele_time != '' || $tele_time != 'undefined')
            {
                $clinincDocInfo = $this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."' 
                and doctor_id='".$doctor_id."'")->row();

                if(count($clinincDocInfo)>0)
                {
                    $data12['online_consulting_time'] =  $tele_time;
                    $this->Generic_model->updateData('clinic_doctor',$data12,array('clinic_doctor_id'=>$clinincDocInfo->clinic_doctor_id));
                }

            }
            $this->response(array('code'=>'200','message'=>$registration_fees,'result'=>'success'));
        }

        public function deleteData_post()
        {
            extract($_POST);

            $clinincDocInfo = $this->db->query("select * from clinic_doctor_weekday_slots where 
            clinic_doctor_weekday_slot_id='".$day."'")->row();

            if(count($clinincDocInfo)>0)
            {
                $res = $this->Generic_model->deleteRecord('clinic_doctor_weekday_slots',
                array('clinic_doctor_weekday_slot_id'=>
                $day));
                $this->response(array('code'=>'200','message'=>'Success','result'=>'success'));
            }else{
                $this->response(array('code'=>'201','message'=>'Fail','result'=>'Fail'));
            }
        }

        public function getDoctorSlotsimp_get($doctor_id,$clinic_id)
        {
            $clinincDocInfo = $this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."' 
            and doctor_id='".$doctor_id."'")->row();
            if(count($clinincDocInfo)>0)
            {
                $clinics_list = $this->db->select("weekday")
                ->from("clinic_doctor_weekday_slots  e")
                ->join("clinic_doctor_weekdays  c","e.clinic_doctor_weekday_id =c.clinic_doctor_weekday_id ")
                ->where("c.clinic_doctor_id ='".$clinincDocInfo->clinic_doctor_id."' and c.slot='walkin'")
                ->group_by("c.weekday")->get()->result();
    
                if(count($clinics_list)>0)
                {
                    $digiSignInfo = $this->db->query("select * from digital_signatures where user_id='".$doctor_id."'")->row();
                    if(count($digiSignInfo)>0)
                    {
                        $data['digitalSignature'] = 1;
                    }
                    else
                    {
                        $data['digitalSignature'] = 0;
                    }
                    $consult_fees = $this->db->query("select * from 
                    clinic_doctor where doctor_id='".$doctor_id."' and clinic_id='".$clinic_id."'")->row();
                    if(count($consult_fees)>0)
                    {
                            $data['consultation_fees'] = $consult_fees->consulting_fee;   
                    }
                    else
                    {
                        $data['consultation_fees'] = '';
                    }
    
    
                    $j=0;
                    foreach ($clinics_list as $clinic) {  
                    $data['weekdays'][$j]['day'] = $clinic->weekday;
    
                    $slots_data= $this->db->select("*")
                    ->from("clinic_doctor_weekday_slots  e")
                    ->join("clinic_doctor_weekdays  c","e.clinic_doctor_weekday_id =c.clinic_doctor_weekday_id ")
                    ->where("c.clinic_doctor_id ='".$clinincDocInfo->clinic_doctor_id."'
                    and c.weekday ='".  $clinic->weekday."' and c.slot='walkin'")
                    ->get()->result();
    
                        if(count($slots_data)>0)
                        {
                            $c=0;
                            foreach($slots_data as $slots)
                            {
                                $data['weekdays'][$j]['slots'][$c]['clinic_doctor_weekday_slot_id'] = $slots->clinic_doctor_weekday_slot_id;
                                $data['weekdays'][$j]['slots'][$c]['session'] = $slots->session;
                                $data['weekdays'][$j]['slots'][$c]['from_time'] = $slots->from_time;
                                $data['weekdays'][$j]['slots'][$c]['to_time'] = $slots->to_time;
                                $c++;
                            }
                        }
                       $j++;
                    }
                    $this->response(array('code'=>'200','message'=>'Success','result'=>$data));
                }
                   else{
                    $digiSignInfo = $this->db->query("select * from digital_signatures where user_id='".$doctor_id."'")->row();
                    if(count($digiSignInfo)>0)
                    {
                        $data['digitalSignature'] = 1;
                    }
                    else
                    {
                        $data['digitalSignature'] = 0;
                    }
                    $consult_fees = $this->db->query("select * from 
                    clinic_doctor where doctor_id='".$doctor_id."' and clinic_id='".$clinic_id."'")->row();
                    if(count($consult_fees)>0)
                    {
                            $data['consultation_fees'] = $consult_fees->consulting_fee;   
                    }
                    else
                    {
                        $data['consultation_fees'] = '';
                    }
                    $data['weekdays'] = array();
                    $this->response(array('code'=>'200','message'=>'Success','result'=>$data));
                }
            }
            // $this->response(array('code'=>'200','message'=>'Success','result'=>$data));
        }

        public function docLanguages_post()
        {
            extract($_POST);

            $doctor_details = $this->db->query("select * from doctors where 
            doctor_id='".$docid."'")->row();

            if(count($doctor_details)>0)
            {
                $con['languages'] = trim($lan, '"');
                $this->Generic_model->updateData('doctors',$con,array('doctor_id'=>$doctor_details->doctor_id));
                $this->response(array('code'=>'200','message'=>'Updated Succesfully','result'=>'Success'));
            }
            else
            {
                $this->response(array('code'=>'200','message'=>'Fail','result'=>'Fail'));
            }
        }

        public function newClinic_post()
        {
            extract($_POST);

            $data1['clinic_name'] = $clinic_name;
            $data1['address'] = $clinic_address;
            $data1['created_date_time'] = date("Y-m-d H:i:s");
            $data1['modified_date_time'] = date("Y-m-d H:i:s");

            $ret_id =  $this->Generic_model->insertDataReturnId('clinics',$data1); 

            $data11['clinic_id'] = $ret_id;
            $data11['doctor_id'] = $doctor_id;
            $data11['primary_clinic'] = '0';
            $data11['created_date_time'] = date("Y-m-d H:i:s");
            $data11['modified_date_time'] = date("Y-m-d H:i:s");
            
            $this->Generic_model->insertData('clinic_doctor',$data11); 

            $this->response(array('code'=>'200','message'=>'Success','result'=>'new clinic added'));

        }

        public function secondaryClinic_post()
        {
            extract($_POST);
            // echo $clinic_id;
            // if($clinic_id != '')
            // {
                $data1['clinic_id'] = $clinic_id;
                $data1['doctor_id'] = $doctor_id;
                $data1['primary_clinic'] = '0';
                $data1['created_date_time'] = date("Y-m-d H:i:s");
                $data1['modified_date_time'] = date("Y-m-d H:i:s");
                
                $this->Generic_model->insertData('clinic_doctor',$data1); 
                $this->response(array('code'=>'200','message'=>'Success','result'=>'Success'));
            // }

        }

        public function googleReviewLink_post()
        {
            extract($_POST);

            $doctor_details = $this->db->query("select * from doctors where 
            doctor_id='".$docid."'")->row();

            if(count($doctor_details)>0)
            {
                $data1['google_review_link'] = $google_review_link;
                $data1['modified_date_time'] = date("Y-m-d H:i:s");

                $this->Generic_model->updateData('doctors',$data1,array('doctor_id'=>$docid));
                $this->response(array('code'=>'200','message'=>'Updated Succesfully','result'=>'Success'));
            }
            else
            {
                $this->response(array('code'=>'201','message'=>'Doctor Id not found','result'=>'failure'));
            }
        }
}
?>