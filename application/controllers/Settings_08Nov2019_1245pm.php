<?php

error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends MY_Controller {
 public function __construct(){      
    parent::__construct();
    if(!$this->session->has_userdata('is_logged_in'))

      {

        redirect('Authentication/login');

      }
        
   }
public function index(){
  $clinic_id = $this->session->userdata('clinic_id');
  $cond = '';$condition='';
  if($clinic_id!=0){
    $cond = "clinic_id=".$clinic_id." and archieve=0";    
  }else{
    
    $cond = "archieve=0";
  }
  
    $data['state_list']=$this->Generic_model->getAllRecords('states', $condition='', $order='');
    $data['clinic_list']=$this->db->query('select * from clinics where clinic_id='.$clinic_id)->row();
   
      $data['view'] = 'settings/clinic_settings';
      $this->load->view('layout', $data);
    

}

 public function staff(){
  $clinic_id = $this->session->userdata('clinic_id');
  $data['clinic_doctor'] = $this->db->query('select * from clinic_doctor c inner join doctors d on(c.doctor_id = d.doctor_id)  inner join department dep on(d.department_id = dep.department_id) where c.clinic_id ='.$clinic_id)->result();
    $data['staff'] = $this->db->query('select * from employees e inner join users u on(e.employee_id = u.user_id) inner join roles r on(r.role_id = u.role_id) where e.archieve=0 and e.clinic_id ='.$clinic_id)->result();
      $data['view'] = 'settings/doctor_settings';
      $this->load->view('layout', $data);

}
 public function delete_procedure(){
    
                $this->db->query("DELETE from clinic_procedures where clinic_procedure_id='".$this->input->post('pid')."'");

    }

    public function update_procedure(){
      $this->db->query("update clinic_procedures set procedure_name = '".trim($this->input->post('pname'))."',procedure_cost='".trim($this->input->post('price')."' WHERE clinic_procedure_id  = '".$this->input->post("pid")."'"));

    }


    public function delete_doctor(){
    
                $this->db->query("DELETE from referral_doctors where rfd_id='".$this->input->post('did')."'");

    }

    public function update_doctor(){
      $mobile = $this->input->post('mobile');
      $email = $this->input->post('email');
      $doctor_name = trim($this->input->post('pname'));
      $qualification = trim($this->input->post('qualification'));
      $location = trim($this->input->post('location'));
      $d_id = $this->input->post("did");

      $this->db->query("update referral_doctors set doctor_name = '".$doctor_name."',mobile='".$mobile."',qualification='".$qualification."',email='".$email."',location='".$location."' WHERE rfd_id  = '".$d_id."'");

    }

    public function upload_gallery(){
       $clinic_id = $this->session->userdata('clinic_id');
       $this->load->library('upload');
              $config['upload_path'] = './uploads/clinic_gallery/';
              $config['allowed_types'] = 'jpg|JPG|png|PNG|csv|jpeg';
              $file_count=count($_FILES['file']['name']);
            for($i=0;$i<$file_count;$i++)
            {
              $_FILES['file[]']['name'] = $_FILES['file']['name'][$i];
              $_FILES['file[]']['type'] = $_FILES['file']['type'][$i];
              $_FILES['file[]']['tmp_name'] = $_FILES['file']['tmp_name'][$i];
              $_FILES['file[]']['error'] = $_FILES['file']['error'][$i];
              $_FILES['file[]']['size'] = $_FILES['file']['size'][$i];
              $this->upload->initialize($config);
              $this->upload->do_upload('file[]');
              $fname = $this->upload->data();
              $fileName[$i] = trim($fname['file_name']);
              $data['clinic_id'] = $clinic_id;
              $data['image'] = $fileName[$i];
             $this->Generic_model->insertData("clinic_gallery",$data);
            }
              
         
          redirect("settings/gallery");

    }

    public function gallery(){
      $clinic_id = $this->session->userdata('clinic_id'); 
      $data['gallery_images'] = $this->db->query("select * from clinic_gallery where clinic_id='".$clinic_id."'")->result();
      $data['view'] = 'settings/gallery';
      $this->load->view('layout', $data);
    }

    public function delete_gallery_image(){
    $clinic_id = $this->session->userdata('clinic_id');
   
    $this->db->query("delete from clinic_gallery where clinic_gallery_id = '".$this->input->post('gid')."' and clinic_id='".$clinic_id."'");

    }

public function procedures(){
  $clinic_id = $this->session->userdata('clinic_id');
  $data['procedures'] = $this->db->query('select * from clinic_procedures where clinic_id ='.$clinic_id)->result();
   
      $data['view'] = 'settings/clinic_procedures';
      $this->load->view('layout', $data);

}

public function referral_doctors(){
  $clinic_id = $this->session->userdata('clinic_id');
  $data['referral_doctors'] = $this->db->query('select * from referral_doctors where clinic_id ='.$clinic_id)->result();
  $data['view'] = 'settings/referral_doctors';
  $this->load->view('layout', $data);

}


public function save_procedure(){
  $clinic_id = $this->session->userdata('clinic_id');
  $procedure['clinic_id'] = $clinic_id;
  $procedure['procedure_name'] = $this->input->post("procedure_name");
  $procedure['procedure_cost'] = $this->input->post("procedure_cost");
  $procedure['created_by'] = $this->session->has_userdata('user_id');
  $procedure['modified_by'] = $this->session->has_userdata('user_id');
  $procedure['created_date_time'] = date("Y-m-d H:i:s");
  $procedure['modified_date_time'] = date("Y-m-d H:i:s");
  $this->Generic_model->insertData("clinic_procedures",$procedure);
  redirect("settings/procedures");
}

public function save_referral_doctor(){
  $clinic_id = $this->session->userdata('clinic_id');
  $rfd['clinic_id'] = $clinic_id;
  $rfd['doctor_name'] = $this->input->post("doctor_name");
  $rfd['mobile'] = $this->input->post("mobile");
  $rfd['email'] = $this->input->post("email");
  $rfd['qualification'] = $this->input->post("qualification");
  $rfd['location'] = $this->input->post("location");
  $rfd['created_by'] = $this->session->has_userdata('user_id');
  $rfd['modified_by'] = $this->session->has_userdata('user_id');
  $rfd['created_datetime'] = date("Y-m-d H:i:s");
  $rfd['modified_datetime'] = date("Y-m-d H:i:s");
  $this->Generic_model->insertData("referral_doctors",$rfd);
  redirect("settings/referral_doctors");
}

 public function doctor_info($id=''){

   $sclinic_id = $this->session->userdata('clinic_id');
  $cond = '';$condition='';
  if($sclinic_id!=0){
    $cond = "where clinic_id=".$sclinic_id."";    
  }
   $suser_id = $this->session->has_userdata('user_id'); 
  if($this->input->post('submit')){
   
   // $data['salutation'] = $this->input->post("salutation");
    $data['doctor_type']=$this->input->post('type');
    $data['first_name']=$this->input->post('first_name');
    $data['last_name']=$this->input->post('last_name');
    $data['registration_code']=$this->input->post('reg_code');  
    $data['gender']=$this->input->post('gender');
    $data['qualification']=implode(", ", $this->input->post('qualification'));
    $data['address']=$this->input->post('address');
    $data['state_id']=$this->input->post('state');
    $data['pincode']=$this->input->post('pincode');
    $data['mobile']=$this->input->post('mobile');
    $data['email']=$this->input->post('email');
    
    $data['status'] = "1";
    $data['created_by'] = "1";
    // $data['last_modified_by'] = date("Y-m-d H:i:s");
    $type = $this->input->post('type');
      if($type == "clinic"){
        $data['year_of_passing']=$this->input->post('year_pass');
        $data['university']=$this->input->post('university');
      }else if($type == "inhouse"){
        $data['working_hospital']=$this->input->post('working_hospital');
        $data['department_id']=$this->input->post('department');
        $data['experience']=$this->input->post('experience');
      }else if($type == "consultant"){
        $data['working_hospital']=$this->input->post('working_hospital');
        $data['department_id']=$this->input->post('department');
        $data['experience']=$this->input->post('experience');
      }else{
        echo "fail";
      }
    $this->Generic_model->updateData('doctors',$data,array('doctor_id'=>$id));
    
    $param_1['email_id'] = $this->input->post('email');
    $param_1['mobile'] = $this->input->post('mobile');
    $param_1['status'] = '1';
    $param_1['last_logged_in_date_time'] = date("Y-m-d H:i:s");
    $param_1['modified_by'] = "1";
    $param_1['modified_date_time'] = date("Y-m-d H:i:s");

        $this->Generic_model->updateData('users',$param_1, array('user_id'=>$id));
    
    // $package_price_info = $this->db->query("select * from package_price where package_id=".$this->input->post('package_id')." order by package_price_id desc")->row();
    // $rdate = ($package_price_info->no_days-30);
    // $pdata['clinic_id'] =  $sclinic_id;
    
    // $pdata['package_id'] = $this->input->post('package_id');
    // $pdata['package_price_id'] = $package_price_info->package_price_id;
    // if($this->input->post('package_subscription_date')!=''){
    //   $pdata['package_subscription_date'] = $this->input->post('package_subscription_date');
    //   $pdata['package_expiry_date'] = date('Y-m-d',strtotime("+".$package_price_info->no_days." days", strtotime($this->input->post('package_subscription_date'))));
    //   $pdata['package_renewal_date'] = date('Y-m-d',strtotime("+".$rdate." days", strtotime($this->input->post('package_subscription_date'))));
    // }
    
    
    // $pdata['modified_by'] = $suser_id;
    
    // $pdata['modified_date_time'] = date("Y-m-d H:i:s");
    // $this->Generic_model->updateData('doctor_package',$pdata, array('package_price_id'=>$this->input->post('package_price_id')));
    
    $clinic_doctor['consulting_fee']=$this->input->post('consulting_fee');
    $clinic_doctor['review_days']=$this->input->post('review_days');
    $clinic_doctor['review_times']=$this->input->post('review_times');
    
    $this->Generic_model->updateData('clinic_doctor',$clinic_doctor, array('doctor_id'=>$id,'clinic_id'=>$sclinic_id));
    
    
redirect('settings/staff'); 

}
else{
      $data['department_list']=$this->db->query('select * from department order by department_name ASC')->result();
    $data['state_list']=$this->db->query('select * from states order by state_name ASC')->result();
    // $data['doctor_list']=$this->db->query('select * from doctors a inner join doctor_package b on a.doctor_id=b.doctor_id where a.doctor_id='.$id)->row();
    $data['doctor_list']=$this->db->query('select * from doctors where doctor_id='.$id)->row();

    $data['clinic_list']=$this->db->query("select * from clinics ".$cond)->result();
    $data['packages_list']=$this->db->query('select * from subscription')->result();
    $data['clinic_doctor']=$this->db->query("select * from clinic_doctor ".$cond." and doctor_id='".$id."'")->row();

    // echo "<pre>";print_r($data['doctor_list']);exit();
    $data['view'] = 'settings/doctor_info';
    $this->load->view('layout', $data);
  }
}
public function communication()
   {  
    $clinic_id = $this->session->userdata('clinic_id');
    $data['sms_settings'] = $this->db->query("select * from clinic_sms_settings where clinic_id='".$clinic_id."'")->row();
     $data['view'] = 'settings/sms_settings';
   $this->load->view('layout',$data);
   }
   public function print(){  
     $clinic_id = $this->session->userdata('clinic_id');
     $data['pdf_settings'] = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$clinic_id."'")->row();
    
      $data['view'] = 'settings/pdf_settings';
    $this->load->view('layout',$data);
    }
public function doctor_timings($doctor_id)
   {
     $clinic_id = $this->session->userdata('clinic_id');
     $data['doctor_info'] =$this->db->query('select doc.*, dep.department_name from doctors doc INNER JOIN department dep ON doc.department_id = dep.department_id where doctor_id ='.$doctor_id)->row();
   
    $data['weekdays']=$this->db->query('select * from clinic_doctor_weekdays cd inner join clinic_doctor_weekday_slots cs on(cd.clinic_doctor_weekday_id = cs.clinic_doctor_weekday_id) left join clinic_doctor cdd on cdd.clinic_doctor_id = cd.clinic_doctor_id where cdd.clinic_id = "'.$clinic_id.'" and cdd.doctor_id = "'.$doctor_id.'" group by cd.clinic_doctor_weekday_id,cd.weekday')->result();

     $doctor_id = $this->db->query('select * from clinic_doctor where clinic_id = "'.$clinic_id.'"and doctor_id = "'.$doctor_id.'"')->row();
     
   $data['clinic_doctor_id']=$doctor_id->clinic_doctor_id;
 
     $data['view'] = 'settings/doctor_timings';
   $this->load->view('layout',$data);
   }
   public function doctor_work($id=''){

  $clinic_id=$this->session->userdata('clinic_id');
  $cond = '';$condition='';
  if($clinic_id!=0){
    $cond = "where a.clinic_id=".$clinic_id." and a.doctor_id=".$id;    
  }
  $data['doctor_info'] = $this->db->query("select * from clinic_doctor a inner join doctors b on a.doctor_id=b.doctor_id inner join department d on d.department_id = b.department_id inner join clinics c on c.clinic_id = a.clinic_id ".$cond)->row();

  $data['degrees'] = $this->db->query("select * from doctor_degrees where doctor_id='".$id."'")->result();
$data['view'] = 'settings/doctor_work';
$this->load->view('layout', $data);

}
 public function block_dates($doctor_id = '')
 {
  $clinic_id = $this->session->userdata('clinic_id');
    $data['weekdays']=$this->db->query('select * from calendar_blocking where doctor_id ='.$doctor_id)->result();
    $data['doctors_list'] = $this->db->select("*")->from("clinic_doctor")->where("clinic_id",$this->session->userdata("clinic_id"))->group_by("doctor_id")->get()->result();
    $data['view'] = 'settings/block_dates';

    $this->load->view('layout',$data);

 }

 //Change Password
 public function change_password(){
  $user_id = $this->session->userdata('user_id');
  $userPwdInfo = $this->db->query("select password from users where user_id='".$user_id."'")->row();
  if($_POST['Action']=="ChangePassword"){
    $old_password = md5($_POST['old_password']);
    $new_password = md5($_POST['new_password']);
    if($userPwdInfo->password==$old_password)
    {
      $data['password'] = $new_password;
      $this->Generic_model->updateData('users', $data, array('user_id' => $user_id));
      echo "1";
    }
    else
    {
      echo "0";
    }
  }
  else
  {
    $data['view'] = 'settings/change_password';
    $this->load->view('layout',$data);
  }
 }

  public function update_profile($id){
//print_r($this->input->post());exit;
 $config['upload_path']="./uploads/profile_image/";
      $config['allowed_types']='jpg|JPG|png|PNG|jpeg|JPEG'; 
      $this->load->library('upload');    
      $this->upload->initialize($config);
      $this->upload->do_upload('profile_image');
      $fileData=$this->upload->data('file_name');
      if($fileData==""){
        $doctor_img=$this->db->query('select profile_image from doctors where doctor_id='.$id)->row();
        $img=$doctor_img->profile_image;
      }else{
        $img = $fileData;
      }

$languages = implode(",",$this->input->post("language")); 
$achievements = implode(",",$this->input->post("achievements")); 
$membership = implode(",",$this->input->post("membership")); 
$data['first_name'] = $this->input->post("first_name");
$data['profile_image'] = $img;
$data['last_name'] = $this->input->post("last_name");
$data['about'] = $this->input->post("about");
$data['registration_code'] = $this->input->post("reg_number");
$data['speciality'] = $this->input->post("speciality");
$data['languages'] = $languages;
$data['acheivements'] = $achievements;
$data['membership_in'] = $membership;
$data['last_modified_by'] = $this->session->has_userdata('user_id'); 
$this->Generic_model->updateData('doctors', $data, array('doctor_id' => $id));

$degrees = $this->input->post("Degree");

for($i=0;$i<count($degrees); $i++) {
$dd['doctor_id'] = $id; 
$dd['degree_name'] = $degrees[$i]['degree'];
$dd['university'] = $degrees[$i]['college'];
$dd['year'] = $degrees[$i]['year'];
$this->Generic_model->insertData('doctor_degrees',$dd);

}

redirect("settings/doctor_work/".$id);
 }

public function delete_week_day_slot($clinic_doctor_weekday_slot_id=''){
  $doctor_res=$this->db->query("SELECT doctor_id,clinic_id FROM clinic_doctor_weekdays a inner join clinic_doctor_weekday_slots b on a.clinic_doctor_weekday_id=b.clinic_doctor_weekday_id inner join clinic_doctor c on a.clinic_doctor_id=c.clinic_doctor_id where b.clinic_doctor_weekday_slot_id='".$clinic_doctor_weekday_slot_id."'")->row();

  $isdeleted=$this->db->query('delete from clinic_doctor_weekday_slots where clinic_doctor_weekday_slot_id = '.$clinic_doctor_weekday_slot_id);
if($isdeleted){

  
redirect('settings/doctor_timings/'.$doctor_res->doctor_id);

}
}
 public function clinic_doctor_add_sloat(){
  
  if($this->input->post('submit')){
    
    $from_array = array();
    $to_array = array();

    $total_count =  $this->input->post('total');  
    foreach ($total_count as $key => $value) {
    $data1['clinic_doctor_weekday_id']=$this->input->post('clinic_doctor_weekday_id');
    $data1['from_time']=preg_replace('/\s+/', '', $this->input->post('from_'.$value)[0]);
    $data1['to_time']=preg_replace('/\s+/', '', $this->input->post('to_'.$value)[0]);
    $data1['created_by']=$this->session->userdata('user_id');
    $data1['modified_by']=$this->session->userdata('user_id');
    $data1['created_date_time']=date('Y-m-d H:i:s');
    $data1['modified_date_time']=date('Y-m-d H:i:s');
    $cwd_id2=$this->Generic_model->insertDataReturnId('clinic_doctor_weekday_slots',$data1);
    }
     $doctor_id = $this->db->query('select * from clinic_doctor cd inner join clinic_doctor_weekdays cw on(cd.clinic_doctor_id = cw.clinic_doctor_id) where clinic_doctor_weekday_id = '.$data1['clinic_doctor_weekday_id'])->row()->doctor_id;
    
      
   redirect('settings/doctor_timings/'.$doctor_id);
      
    
  
  }
  
 }
 
 
 
 public function clinic_doctor_add_weekday_slot(){
  
  if($this->input->post('submit')){
    $from_array = array();
    $to_array = array();
    $total_count =  $this->input->post('total');
    $weekdays=$this->input->post('weekdays');
    
    
    if(count($weekdays)>0)
    {
            
      foreach($weekdays as $key=>$value1){
        
      $total_split1 = explode("_",$value1);
      $data2['clinic_doctor_id']=$this->input->post('clinic_doctor_id');
      $data2['weekday'] = $total_split1[0];
      $data2['created_by']=$this->session->userdata('user_id');
      $data2['modified_by']=$this->session->userdata('user_id');
      $data2['created_date_time']=date('Y-m-d H:i:s');
      $data2['modified_date_time']=date('Y-m-d H:i:s');
       $cwd_id=$this->Generic_model->insertDataReturnId('clinic_doctor_weekdays',$data2);
      
      foreach ($total_count as $key => $value) {
      
      $total_split = explode("_",$value);
      if($total_split[0]==$value1)
      {
      $data1['clinic_doctor_weekday_id']=$cwd_id;
      $data1['from_time']=preg_replace('/\s+/', '', $this->input->post('from_'.$value)[0]);
      $data1['to_time']=preg_replace('/\s+/', '', $this->input->post('to_'.$value)[0]);
      $data1['created_by']=$this->session->userdata('user_id');
      $data1['modified_by']=$this->session->userdata('user_id');
      $data1['created_date_time']=date('Y-m-d H:i:s');
      $data1['modified_date_time']=date('Y-m-d H:i:s');
      $cwd_id2=$this->Generic_model->insertDataReturnId('clinic_doctor_weekday_slots',$data1);
      
      
      }
      
      }

      }
      $doctor_id = $this->db->query('select * from clinic_doctor cd inner join clinic_doctor_weekdays cw on(cd.clinic_doctor_id = cw.clinic_doctor_id) where clinic_doctor_weekday_id = '.$data1['clinic_doctor_weekday_id'])->row()->doctor_id;
      
   redirect('settings/doctor_timings/'.$doctor_id);
      
    } 
  
  }
  
 }

 public function add_sloat($weekday='',$clinic_doctor_weekday_id=''){
  $data['weekday']=$weekday;
  $data['clinic_doctor_weekday_id']=$clinic_doctor_weekday_id;
  $array_of_time = array();
    //$start_time = strtotime($starttime); //change to strtotime
    //$end_time = strtotime($endtime); //change to strtotime
    $from = "07:00";
    $to = "23:45";
    $start_time = strtotime($from); //change to strtotime
    $end_time = strtotime($to);
      $duration = '15';
    $add_mins = $duration * 60;



    while ($start_time <= $end_time) // loop between time
    {
    $array_of_time[] = date("H:i", $start_time);
    $start_time += $add_mins; // to check endtime
    }
    $booked_slots[] = $array_of_time;
  
 
  $data['timings'] = $booked_slots;
  $data['view'] = 'clinic_doctor/doctor_add_slot';
   $this->load->view('layout',$data);
}

public function add_week_day($clinic_doctor_id=''){
  
  $data['doctor_weekday']=$this->db->query("SELECT weekday FROM clinic_doctor_weekdays where clinic_doctor_id='".$clinic_doctor_id."'")->result_array();
  $data['clinic_doctor_id']=$clinic_doctor_id;  
  $data['view'] = 'clinic_doctor/doctor_add_week_days';
  $this->load->view('layout',$data);
}

 public function cal_date_del($id = '',$doctor_id = '')
 {
   $ok = $this->db->query('delete from calendar_blocking where calendar_blocking_id = '.$id);
   if($ok){
   redirect('settings/block_dates/'.$doctor_id);
  }
 }

 public function sms_settings_insert($doctor_id = '')
 {
  $clinic_id = $this->session->userdata('clinic_id');
  $sms_settings = $this->db->query("select * from clinic_sms_settings where clinic_id='".$clinic_id."'")->num_rows();
  $sms['clinic_id'] = $clinic_id;
  $sms['appointment_sms'] = $this->input->post("confirmationSMS");
  $sms['reminder_sms'] = $this->input->post("reminderSMS");
  $sms['followup_sms'] = $this->input->post("followupSMS");
  $sms['appointment_email'] = $this->input->post("confirmationEmail");
  $sms['reminder_email'] = $this->input->post("reminderEmail");
  $sms['followup_email'] = $this->input->post("followupEmail");
  if($sms_settings == 0){
    $this->Generic_model->insertData("clinic_sms_settings",$sms);
  }
  else{
$this->Generic_model->updateData('clinic_sms_settings',$sms, array('clinic_id'=>$clinic_id));
  }
  
  redirect("settings/communication");


}
 public function save_pdf_settings($doctor_id = '')
 {
  $clinic_id = $this->session->userdata('clinic_id');
  $pdf_settings = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$clinic_id."'")->num_rows();
  $pdf['clinic_id'] = $clinic_id;
  $pdf['header'] = $this->input->post("header");
  $pdf['footer'] = $this->input->post("footer");
  $pdf['header_height'] = round($this->input->post("header_height")*37.7952755906,2);
  $pdf['footer_height'] = round($this->input->post("footer_height")*37.7952755906,2);

  if($pdf_settings == 0){
    $this->Generic_model->insertData("clinic_pdf_settings",$pdf);
  }
  else{
$this->Generic_model->updateData('clinic_pdf_settings',$pdf, array('clinic_id'=>$clinic_id));
  }
  
  redirect("settings/print");


}
}
?>