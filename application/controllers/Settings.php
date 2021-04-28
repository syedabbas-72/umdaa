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
  $data['clinic_name'] = $this->session->userdata('clinic_name');
  $cond = '';$condition='';
  if($clinic_id!=0){
    $cond = "clinic_id=".$clinic_id." and archieve=0";    
  }else{
    
    $cond = "archieve=0";
  }
  
    $data['state_list']=$this->Generic_model->getAllRecords('states', $condition='', $order='');
    $data['district_list']=$this->Generic_model->getAllRecords('districts', $condition='', $order='');
    $data['clinic_list']=$this->db->query('select * from clinics where clinic_id='.$clinic_id)->row();
   
      $data['view'] = 'settings/clinic_settings';
      $this->load->view('layout', $data);
    

}

public function pdfSettings(){
  $clinic_id = $this->session->userdata("clinic_id");
  // echo "<pre>";
  // print_r($_POST);
  extract($_POST);
    if($form_type==1)
    {
      $data['clinic_id'] = $clinic_id;
      // $data['paper_type'] = $paper_type;
      $data['header'] = $header;
      $data['footer'] = $footer;
      $data['doc_details'] = $doc_details;
      $data['head_height'] = $head_height;
      $data['foot_height'] = $foot_height;
      $data['header_height'] = $head_height*37.7952755906;
      $data['footer_height'] = $foot_height*37.7952755906;
      $this->Generic_model->insertData("clinic_pdf_settings",$data);
    }
    elseif($form_type==2)
    {
      $data['clinic_id'] = $clinic_id;
      // $data['paper_type'] = $paper_type;
      $data['header'] = $header;
      $data['footer'] = $footer;
      $data['doc_details'] = $doc_details;
      $data['head_height'] = $head_height;
      $data['foot_height'] = $foot_height;
      $data['header_height'] = $head_height*37.7952755906;
      $data['footer_height'] = $foot_height*37.7952755906;
      $this->Generic_model->updateData("clinic_pdf_settings",$data, array('clinic_id'=>$clinic_id));
    }
    redirect('settings/print');
}


public function staff(){

    $clinic_id = $this->session->userdata('clinic_id');
  
    $data['clinic_doctor'] = $this->db->query('select * from clinic_doctor c inner join doctors d on(c.doctor_id = d.doctor_id)  inner join department dep on(d.department_id = dep.department_id) where c.clinic_id ='.$clinic_id)->result();

    // $data['staff'] = $this->db->query('select * from employees e inner join users u on(e.employee_id = u.user_id) inner join roles r on(r.role_id = u.role_id) where e.archieve=0 and e.clinic_id ='.$clinic_id)->result();

    $data['staff'] = $this->db->select('EMP.employee_id, EMP.title, EMP.first_name, EMP.last_name, EMP.employee_code, EMP.email_id, R.role_name, P.profile_name')->from('employees EMP')->join('users U','EMP.employee_id = U.user_id','inner')->join('roles R','U.role_id = R.role_id','inner')->join('profiles P','U.profile_id = P.profile_id','inner')->where('EMP.archieve =',0)->where('EMP.clinic_id =',$clinic_id)->get()->result_array();

    $data['view'] = 'settings/doctor_settings';
    $data['clinic_name'] = $this->session->userdata('clinic_name');

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
            //  $this->session->set_flashdata('msg', 'Success');
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
   $data['clinic_name'] = $this->session->userdata('clinic_name');
      $data['view'] = 'settings/clinic_procedures';

      $this->load->view('layout', $data);

}

public function referral_doctors(){
  $clinic_id = $this->session->userdata('clinic_id');
  $data['referral_doctors'] = $this->db->query('select * from referral_doctors where clinic_id ='.$clinic_id)->result();
  $data['clinic_name'] = $this->session->userdata('clinic_name');
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
  $this->session->set_flashdata('msg', 'Sucessfully Inserted');
  redirect("settings/procedures");
}

public function save_referral_doctor(){

    $clinic_id = $this->session->userdata('clinic_id');
    $rfd['clinic_id'] = $clinic_id;
    $rfd['doctor_name'] = $this->input->post("doctor_name");
    $rfd['mobile'] = $this->input->post("mobile");
    $rfd['email'] = $this->input->post("email");

    // if(count($this->input->post("qualification")) > 0){
    //     foreach($_POST['qualification'] as $rec){
    //         $qualification .= $rec.", ";
    //     }
    // }

    $rfd['qualification'] =  $this->input->post("qualification");
    $rfd['location'] = $this->input->post("location");
    $rfd['created_by'] = $this->session->has_userdata('user_id');
    $rfd['modified_by'] = $this->session->has_userdata('user_id');
    $rfd['created_datetime'] = date("Y-m-d H:i:s");
    $rfd['modified_datetime'] = date("Y-m-d H:i:s");

    $this->Generic_model->insertData("referral_doctors",$rfd);
      // set flash data
      $this->session->set_flashdata('msg', 'Successfully Inserted');
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
    $data['department_id']=$this->input->post('department');
    $data['qualification']=$this->input->post('qualification');
    $data['experience']=$this->input->post('experience');
    $data['address']=$this->input->post('address');
    $data['state_id']=$this->input->post('state');
    $data['pincode']=$this->input->post('pincode');
    // $data['mobile']=$this->input->post('mobile');
    // $data['email']=$this->input->post('email');
    
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
    $this->session->set_flashdata('msg', 'Doctor Information Added Successfully');
    
    // $param_1['email_id'] = $this->input->post('email');
    // $param_1['mobile'] = $this->input->post('mobile');
    $param_1['status'] = '1';
    $param_1['last_logged_in_date_time'] = date("Y-m-d H:i:s");
    $param_1['modified_by'] = "1";
    $param_1['modified_date_time'] = date("Y-m-d H:i:s");

        $this->Generic_model->updateData('users',$param_1, array('user_id'=>$id));
        $this->session->set_flashdata('msg', 'Doctor Information Added Successfully');
    
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
    $clinic_doctor['online_consulting_fee']=$this->input->post('online_consulting_fee');
    $clinic_doctor['review_days']=$this->input->post('review_days');
    $clinic_doctor['review_times']=$this->input->post('review_times');
    
    $this->Generic_model->updateData('clinic_doctor',$clinic_doctor, array('doctor_id'=>$id,'clinic_id'=>$sclinic_id));
    $this->session->set_flashdata('msg', 'Doctor Information Added Successfully');
    
    
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
   
    $data['clinic_name'] = $this->session->userdata('clinic_name');
    // echo "<pre>";print_r($data['doctor_list']);exit();
    $data['view'] = 'settings/doctor_info';
    $this->load->view('layout', $data);
  }
}
public function communication()
   {  
    $clinic_id = $this->session->userdata('clinic_id');
    $data['sms_settings'] = $this->db->query("select * from clinic_sms_settings where clinic_id='".$clinic_id."'")->row();
    $data['clinic_name'] = $this->session->userdata('clinic_name');
     $data['view'] = 'settings/sms_settings';
   $this->load->view('layout',$data);
   }
   public function print(){  
     $clinic_id = $this->session->userdata('clinic_id');
     $data['pdf_settings'] = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$clinic_id."'")->row();
     $data['clinic_name'] = $this->session->userdata('clinic_name');
      $data['view'] = 'settings/pdf_settings';
      $data['clinic_name'] = $this->session->userdata('clinic_name');
    $this->load->view('layout',$data);
    }

    public function addSlots(){
      if(isset($_POST))
      {
        extract($_POST);
        // echo "<pre>";print_r($_POST);echo "</pre>";
        // $doctor_id='24';
        $slots = $this->Generic_model->getAllRecords('clinic_doctor_weekdays',array('clinic_doctor_id'=>$clinic_doctor_id,'slot'=>$slot_type));
        // echo $this->db->last_query();
        // echo "<pre>";print_r($slots);echo "</pre>";
        // exit;
        if(count($slots) > 0){
          foreach($slots as $value){
            $this->Generic_model->deleteRecord('clinic_doctor_weekday_slots', array('clinic_doctor_weekday_id' => $value->clinic_doctor_weekday_id));
          }
          $this->Generic_model->deleteRecord('clinic_doctor_weekdays', array('clinic_doctor_id' => $clinic_doctor_id,'slot'=>$slot_type));
        }

        for($i = 0;$i <= 6;$i++)
        {
          $data['weekday'] = $i+1;
          $data['clinic_doctor_id'] = $clinic_doctor_id;
          $data['status'] = 0;
          $data['slot'] = $slot_type;
          $data['created_by'] = $this->session->userdata('user_id');
          $data['modified_by'] = $this->session->userdata('user_id');
          $data['created_date_time'] = date("Y-m-d H:i:s");
          $data['modified_date_time'] = date("Y-m-d H:i:s");
          $cdw_id = $this->Generic_model->insertDataReturnId('clinic_doctor_weekdays', $data);

          // Morning Slots
          if($mr_start[$i] != "" && $mr_end[$i] != "")
          {
              $morning['clinic_doctor_weekday_id'] = $cdw_id;
              $morning['from_time'] = ($mr_start[$i]!="")?$mr_start[$i]:'';
              $morning['to_time'] = ($mr_end[$i]!="")?$mr_end[$i]:'';
              $morning['session'] = "morning";
              $morning['created_by'] = $this->session->userdata('user_id');
              $morning['modified_by'] = $this->session->userdata('user_id');
              $morning['created_date_time'] = date("Y-m-d H:i:s");
              $morning['modified_date_time'] = date("Y-m-d H:i:s");
              $this->Generic_model->insertData('clinic_doctor_weekday_slots', $morning);
          }

          // Afternoon Slots
          if($af_start[$i] != "" && $af_end[$i] != "")
          {
              $afternoon['clinic_doctor_weekday_id'] = $cdw_id;
              $afternoon['from_time'] = ($af_start[$i]!="")?$af_start[$i]:'';
              $afternoon['to_time'] = ($af_end[$i]!="")?$af_end[$i]:'';
              $afternoon['session'] = "afternoon";
              $afternoon['created_by'] = $this->session->userdata('user_id');
              $afternoon['modified_by'] = $this->session->userdata('user_id');
              $afternoon['created_date_time'] = date("Y-m-d H:i:s");
              $afternoon['modified_date_time'] = date("Y-m-d H:i:s");
              $this->Generic_model->insertData('clinic_doctor_weekday_slots', $afternoon);
          }

          // Evening Slots
          if($ev_start[$i] != "" && $ev_end[$i] != "")
          {
              $evening['clinic_doctor_weekday_id'] = $cdw_id;
              $evening['from_time'] = ($ev_start[$i]!="")?$ev_start[$i]:'';
              $evening['to_time'] = ($ev_end[$i]!="")?$ev_end[$i]:'';
              $evening['session'] = "evening";
              $evening['created_by'] = $this->session->userdata('user_id');
              $evening['modified_by'] = $this->session->userdata('user_id');
              $evening['created_date_time'] = date("Y-m-d H:i:s");
              $evening['modified_date_time'] = date("Y-m-d H:i:s");
              $this->Generic_model->insertData('clinic_doctor_weekday_slots', $evening);
          }
        }
          
        $this->session->set_flashdata('msg', 'Slots Saved Successfully.');
        redirect('Settings/doctor_timings/'.$doctor_id);
      }
    }

    public function removeSlot(){
      extract($_POST);
      $check = $this->db->query("select * from clinic_doctor_weekday_slots where clinic_doctor_weekday_slot_id='".$cdws_id."'")->row();
      if(count($check) > 0)
      {
        $this->Generic_model->deleteRecord('clinic_doctor_weekday_slots', array('clinic_doctor_weekday_slot_id' => $cdws_id));
        echo "1";
      }
      else
      {
        echo "0";
      }
    }

public function doctor_timings($doctor_id)
   {
     $clinic_id = $this->session->userdata('clinic_id');
     $data['doctor_info'] =$this->db->query("select d.*,dep.department_name from doctors d,department dep where d.department_id=dep.department_id and d.doctor_id='".$doctor_id."'")->row();

     $clinicDocInfo = $this->db->query('select * from clinic_doctor where clinic_id = "'.$clinic_id.'"and doctor_id = "'.$doctor_id.'"')->row();
    $data['weekdays']=$this->db->query('select * from clinic_doctor_weekdays where clinic_doctor_id="'.$clinicDocInfo->clinic_doctor_id.'" and clinic_doctor_weekdays.slot="walkin" group by weekday order by weekday ASC')->result();

    // echo $this->db->last_query();
    // exit();
    $data['doctor_id'] = $data['doctor_info']->doctor_id;
    $data['clinic_doctor_id']=$clinicDocInfo->clinic_doctor_id;
    // echo "<pre>";
    // print_r($data);
    // exit();
  
    $data['clinic_name'] = $this->session->userdata('clinic_name');
    $data['clinic_id'] = $this->session->userdata('clinic_id');


    // $clinic_id = $this->session->userdata('clinic_id');
    // $clinicDocInfo = $this->db->query('select * from clinic_doctor where clinic_id = "'.$clinic_id.'"and doctor_id = "'.$doctor_id.'"')->row();
    $weekdays=$this->db->query('select weekday from clinic_doctor_weekdays where 
    clinic_doctor_id="'.$clinicDocInfo->clinic_doctor_id.'" 
    group by weekday order by weekday ASC')->result();
  
    $allDays=['1','2','3','4','5','6','7'];
    $data['result']=$weekdays[0]->weekday;
    $allDayss=[];
    for($i=0;$i<=7;$i++)
    {
      $abc = $weekdays[$i]->weekday;
      if($abc != '')
      {
        array_push($allDayss,$abc);
      }
    }
  
    $data['slotsDays'] =  (array_merge($allDays,$allDayss));
     $all = array_diff($allDays, $allDayss);
     $data['slotsDays'] = $all;
  
    $data['doctor_id'] =  $doctor_id;
    $data['clinic_doctor_id']=$clinicDocInfo->clinic_doctor_id;
    // echo "<pre>";
    // print_r($data);
    // exit();
  
    $data['clinic_name'] = $this->session->userdata('clinic_name');
    $data['clinic_id'] = $this->session->userdata('clinic_id');
 
     $data['view'] = 'settings/doctor_schedule';
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

// Block days with respect to doctor
    public function block_calendar()
    {
        $clinic_id   = $this->session->userdata('clinic_id');
        $split       = explode("-", $this->input->post("daterange"));
        $spl1        = explode("/", $split[0]);
        $spl2        = explode("/", $split[1]);
        $start       = $spl1[2]."-".$spl1[1]."-".$spl1[0];
        $end         = $spl2[2]."-".$spl2[1]."-".$spl2[0];
        $dates       = $start." ".$end;

        $user_id = $this->session->has_userdata('user_id');

        $data['doctor_id']          = $this->input->post("block_doctor");
        $data['remark']             = $this->input->post("remark");
        $data['clinic_id']          = $clinic_id;
        $data['dates']              = $this->input->post("daterange");
        $data['status']             = 1;
        $data['created_by']         = $user_id;
        $data['modified_by']        = $user_id;
        $data['created_date_time']  = date('Y-m-d H:i:s');
        $data['modified_date_time'] = date('Y-m-d H:i:s');

        $this->Generic_model->insertData('calendar_blocking', $data);
        redirect("Settings/block_dates/".$this->input->post("block_doctor"));
    }


 public function block_dates($doctor_id = '')
 {
    $clinic_id = $this->session->userdata('clinic_id');
    $data['block_dates']=$this->db->query("select * from calendar_blocking where clinic_id='".$clinic_id."' and doctor_id='".$doctor_id."'")->result();
    $data['doctor_id'] = $doctor_id;
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
    $config['upload_path']="./uploads/doctors/";
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
    $data['experience'] = $this->input->post("experience");
    $data['diseases_dealt'] = $this->input->post("diseases_dealt");
    $data['languages'] = $languages;
    $data['acheivements'] = $achievements;
    $data['membership_in'] = $membership;
    // $data['last_modified_by'] = $this->session->has_userdata('user_id'); 
    $this->Generic_model->updateData('doctors', $data, array('doctor_id' => $id));
    $this->session->set_flashdata('msg', 'Successfully Added');

    $degrees = $this->input->post("Degree");

    for($i=0;$i<count($degrees); $i++) {
      $idd = $degrees[$i]['doctor_degree_id'];

      if(count($idd) == 0)
      {
        $dd['doctor_id'] = $id; 
        $dd['degree_name'] = $degrees[$i]['degree'];
        $dd['university'] = $degrees[$i]['college'];
        $dd['year'] = $degrees[$i]['year'];
        $this->Generic_model->insertData('doctor_degrees',$dd);
        $this->session->set_flashdata('msg', 'Successfully Added');
      }
      else{
      }
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
  
  $data['doctor_weekday']=$this->db->query("SELECT cd.weekday FROM clinic_doctor_weekdays cd,clinic_doctor_weekday_slots cdw where cdw.clinic_doctor_weekday_id=cd.clinic_doctor_weekday_id and cd.clinic_doctor_id='".$clinic_doctor_id."'")->result_array();
  $data['clinic_doctor'] = $this->db->query("select * from clinic_doctor where clinic_doctor_id='".$clinic_doctor_id."'")->row();
  $data['weekdayCount'] = $this->db->query("select count(*) as count from clinic_doctor_weekdays cd,clinic_doctor_weekday_slots cdw where cdw.clinic_doctor_weekday_id=cd.clinic_doctor_weekday_id and cd.clinic_doctor_id='".$clinic_doctor_id."' group by cdw.clinic_doctor_weekday_id ")->result();

  $data['clinic_doctor_id']=$clinic_doctor_id;  
  $data['doctor_id']=$data['clinic_doctor']->doctor_id;  
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
  $pdf['head_height'] = $this->input->post("header_height");
  $pdf['header_height'] = round($this->input->post("header_height")*37.7952755906,2);
  // $pdf['footer_height'] = round($this->input->post("footer_height")*37.7952755906,2);

  if($pdf_settings == 0){
    $this->Generic_model->insertData("clinic_pdf_settings",$pdf);
    $this->session->set_flashdata('msg', 'Header Information Added Successfully');
  }
  else{
  $this->Generic_model->updateData('clinic_pdf_settings',$pdf, array('clinic_id'=>$clinic_id));
  $this->session->set_flashdata('msg', 'Header Information Added Successfully');
  }
  
  redirect("settings/print");
}

public function save_pdf_settingss($doctor_id = '')
{
 $clinic_id = $this->session->userdata('clinic_id');
 $pdf_settings = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$clinic_id."'")->num_rows();
 $pdf['clinic_id'] = $clinic_id;
 $pdf['foot_height'] = $this->input->post("footer_height");
 $pdf['footer'] = $this->input->post("footer");
//  $pdf['header_height'] = round($this->input->post("header_height")*37.7952755906,2);
 $pdf['footer_height'] = round($this->input->post("footer_height")*37.7952755906,2);

 if($pdf_settings == 0){
   $this->Generic_model->insertData("clinic_pdf_settings",$pdf);
   $this->session->set_flashdata('msg', 'Footer Information Added Successfully');
 }
 else{
 $this->Generic_model->updateData('clinic_pdf_settings',$pdf, array('clinic_id'=>$clinic_id));
 $this->session->set_flashdata('msg', 'Footer Information Added Successfully');
 }
 
 redirect("settings/print");
}

public function changeTimings()
{
  $id = $this->input->post('id');//slot id
  $id1 = $this->input->post('id1');//to
  $id2 = $this->input->post('id2');//from
  // $pdf_settings = $this->db->query("update clinic_doctor_weekday_slots set from_time = '".$id1."',to_time = '".$id2."' where clinic_doctor_weekday_slot_id = '".$id."'");
  // if($id2 == '')
  // {
  //   $pdf_settings = $this->db->query("update clinic_doctor_weekday_slots set from_time = '".$id."' where clinic_doctor_weekday_slot_id = '".$id1."'");
  // }
  // elseif($id == '')
  // {
  //   $pdf_settings = $this->db->query("update clinic_doctor_weekday_slots set to_time = '".$id2."' where clinic_doctor_weekday_slot_id = '".$id1."'");
  // }else{
  //   $pdf_settings = $this->db->query("update clinic_doctor_weekday_slots set from_time = '".$id."',to_time = '".$id2."' where clinic_doctor_weekday_slot_id = '".$id1."'");
  // }
  if($id2 == '')
  {
    $pdf_settings = $this->db->query("update clinic_doctor_weekday_slots set from_time = '".$id1."' where clinic_doctor_weekday_slot_id = '".$id."'");
    // $this->session->set_flashdata('msg', 'Successfully Added');
  }
  elseif($id1 == '')
  {
    $pdf_settings = $this->db->query("update clinic_doctor_weekday_slots set to_time = '".$id2."' where clinic_doctor_weekday_slot_id = '".$id."'");
    // $this->session->set_flashdata('msg', 'Successfully Added');
  }else{
    $pdf_settings = $this->db->query("update clinic_doctor_weekday_slots set from_time = '".$id1."',to_time = '".$id2."' where clinic_doctor_weekday_slot_id = '".$id."'");
    // $this->session->set_flashdata('msg', 'Successfully Added');
  }
  
  
  // $this->load->view('doctor_timings', $pdf_settings);
}

public function insertTimings()
{
  print_r($_POST);
  $abc =  $this->input->post('dayNum');
   print_r($abc);
  if($abc == '1')
  {
      $def = '1';
  }
  elseif($abc == '2')
  {
    $def = '2';
  } 
  elseif($abc == '3')
  {
    $def = '3';
  } 
  elseif($abc == '4')
  {
    $def = '4';
  }
  elseif($abc == '5')
  {
    $def = '5';
  }
  elseif($abc == '6')
  {
    $def = '6';
  }
  elseif($abc == '7')
  {
    $def = '7';
  }
  else{
    
  }

  $sdoctor_id = $this->input->post('sdoctor_id');
  $sclinic_id = $this->input->post('sclinic_id');
  $sSession = $this->input->post('sSession');
  $toId = $this->input->post('toId');
  $fromId = $this->input->post('fromId');
  // $dayNum = $this->input->post('dayNum');
  // print_r($dayNum);
  // $day = '3';
  // print_r($day);

  $clinincDocInfo = $this->db->query("select * from clinic_doctor where clinic_id='".$sclinic_id."' and doctor_id='".$sdoctor_id."'")->row();

        $cdwInfo = $this->db->query("select * from clinic_doctor_weekdays where 
        clinic_doctor_id='".$clinincDocInfo->clinic_doctor_id."' 
        and weekday='".$def."'")->row();

        // if(count($cdwInfo)>0)
        // {
            $cdw_id = $cdwInfo->clinic_doctor_weekday_id;
            $data1['clinic_doctor_weekday_id'] =$cdw_id;
            $data1['from_time'] = $toId;
            $data1['to_time'] = $fromId;
            $data1['session'] = $sSession;
            $data1['created_date_time'] = date("Y-m-d H:i:s");
            $data1['modified_date_time'] = date("Y-m-d H:i:s");
            $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);    


}

public function add_new_slots($id)
{

  $clinic_id = $this->session->userdata('clinic_id');
  $clinicDocInfo = $this->db->query('select * from clinic_doctor where clinic_id = "'.$clinic_id.'"and doctor_id = "'.$id.'"')->row();
  $weekdays=$this->db->query('select weekday from clinic_doctor_weekdays where clinic_doctor_id="'.$clinicDocInfo->clinic_doctor_id.'" group by weekday order by weekday ASC')->result();

  $allDays=['1','2','3','4','5','6','7'];
  $data['result']=$weekdays[0]->weekday;
  $allDayss=[];
  for($i=0;$i<=7;$i++)
  {
    $abc = $weekdays[$i]->weekday;
    if($abc != '')
    {
      array_push($allDayss,$abc);
    }
  }

  $data['slotsDays'] =  (array_merge($allDays,$allDayss));
   $all = array_diff($allDays, $allDayss);
   $data['slotsDays'] = $all;

  $data['doctor_id'] =  $id;
  $data['clinic_doctor_id']=$clinicDocInfo->clinic_doctor_id;
  // echo "<pre>";
  // print_r($data);
  // exit();

  $data['clinic_name'] = $this->session->userdata('clinic_name');
  $data['clinic_id'] = $this->session->userdata('clinic_id');
    $data['view'] = 'settings/add_new_slots';
    $this->load->view('layout',$data);
}

public function addSlotTimings()
{
  // extract($_POST);
  $clinic_id = $this->session->userdata('clinic_id');
  $doctorId = $this->input->post('doctorId');
  $sessionDay = $this->input->post('sessionDay');
  $daySelection = $this->input->post('daySelection');
  $toId = $this->input->post('toId');
  $fromId = $this->input->post('fromId');

  $clinincDocInfo = $this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."'
   and doctor_id='".$doctorId."'")->row();
   
  if(count($clinincDocInfo)>0)
  {
    $data['weekday'] =  $daySelection;
    $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
    $data['created_date_time'] = date("Y-m-d H:i:s");
    $data['modified_date_time'] = date("Y-m-d H:i:s");
    $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
    $data1['clinic_doctor_weekday_id'] = $cdw_id;
    $data1['from_time'] = $toId;
    $data1['to_time'] = $fromId;
    $data1['session'] = $sessionDay;
    $data1['created_date_time'] = date("Y-m-d H:i:s");
    $data1['modified_date_time'] = date("Y-m-d H:i:s");
    $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
  }
  // redirect('settings/doctor_timings/'.$doctorId);
  // redirect('settings/staff');
}

//Video Calling code
public function doctor_videocall_timings($doctor_id)
   {
     $clinic_id = $this->session->userdata('clinic_id');
     $data['doctor_info'] =$this->db->query("select d.*,dep.department_name from doctors d,department dep where d.department_id=dep.department_id and d.doctor_id='".$doctor_id."'")->row();

     $clinicDocInfo = $this->db->query('select * from clinic_doctor where clinic_id = "'.$clinic_id.'"and doctor_id = "'.$doctor_id.'"')->row();
    $data['weekdays']=$this->db->query('select * from clinic_doctor_weekdays where clinic_doctor_id="'.$clinicDocInfo->clinic_doctor_id.'" and clinic_doctor_weekdays.slot="video call" group by weekday order by weekday ASC')->result();

    // echo $this->db->last_query();
    // exit();
    $data['doctor_id'] = $data['doctor_info']->doctor_id;
    $data['clinic_doctor_id']=$clinicDocInfo->clinic_doctor_id;
    // echo "<pre>";
    // print_r($data);
    // exit();
  
    $data['clinic_name'] = $this->session->userdata('clinic_name');
    $data['clinic_id'] = $this->session->userdata('clinic_id');


    // $clinic_id = $this->session->userdata('clinic_id');
    // $clinicDocInfo = $this->db->query('select * from clinic_doctor where clinic_id = "'.$clinic_id.'"and doctor_id = "'.$doctor_id.'"')->row();
    $weekdays=$this->db->query('select weekday from clinic_doctor_weekdays where clinic_doctor_id="'.$clinicDocInfo->clinic_doctor_id.'" and clinic_doctor_weekdays.slot="video call" group by weekday order by weekday ASC')->result();
  
    $allDays=['1','2','3','4','5','6','7'];
    $data['result']=$weekdays[0]->weekday;
    $allDayss=[];
    for($i=0;$i<=7;$i++)
    {
      $abc = $weekdays[$i]->weekday;
      if($abc != '')
      {
        array_push($allDayss,$abc);
      }
    }
  
    $data['slotsDays'] =  (array_merge($allDays,$allDayss));
     $all = array_diff($allDays, $allDayss);
     $data['slotsDays'] = $all;
  
    $data['doctor_id'] =  $doctor_id;
    $data['clinic_doctor_id']=$clinicDocInfo->clinic_doctor_id;
    // echo "<pre>";
    // print_r($data);
    // exit();
  
    $data['clinic_name'] = $this->session->userdata('clinic_name');
    $data['clinic_id'] = $this->session->userdata('clinic_id');
 
     $data['view'] = 'settings/doctor_videocall_timings';
     $this->load->view('layout',$data);
   }

   //Save Video Calling Timings
public function addSlotTimingss()
{
  // extract($_POST);
  $clinic_id = $this->session->userdata('clinic_id');
  $doctorId = $this->input->post('doctorId');
  $sessionDay = $this->input->post('sessionDay');
  $daySelection = $this->input->post('daySelection');
  $toId = trim($this->input->post('toId'), '"');
  $fromId = $this->input->post('fromId');
  // trim($clinic_id, '"');

  $clinincDocInfo = $this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."'
   and doctor_id='".$doctorId."'")->row();

   $walkinSlot = $this->db->query("select * from clinic_doctor_weekdays where 
   clinic_doctor_id='".$clinincDocInfo->clinic_doctor_id."'
   and weekday='".$daySelection."' and slot='walkin'")->row();
   
   if(count($walkinSlot)>0)
   {
    $walkinSlotTime = $this->db->query("select * from clinic_doctor_weekday_slots where 
    clinic_doctor_weekday_id='".$walkinSlot->clinic_doctor_weekday_id."' and session='".$sessionDay."'")->row();
    $fromTime = $walkinSlotTime->from_time;
    $toTime = $walkinSlotTime->to_time;

    $checkTime = $this->db->query("select * from clinic_doctor_weekday_slots where
    clinic_doctor_weekday_id='".$walkinSlotTime->clinic_doctor_weekday_id."'
    and session='".$sessionDay."' and '".$toId."' BETWEEN '".$fromTime."' AND '".$toTime."'")->row();
    
    // $checkTime = $this->db->query("select * from clinic_doctor_weekday_slots where
    //  clinic_doctor_weekday_id='".$walkinSlotTime->clinic_doctor_weekday_id."'
    //  and session='".$sessionDay."' and '".$toId."' BETWEEN '".$fromTime."' AND '".$toTime."'")->row();

     if(count($checkTime)>0)
     {
      print json_encode(array("status"=>"Failure","message"=>"Your morningtime is confilcting with walkin"));
     }
     else{
      $data['weekday'] =  $daySelection;
      $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
      $data['slot'] = 'video call';
      $data['created_date_time'] = date("Y-m-d H:i:s");
      $data['modified_date_time'] = date("Y-m-d H:i:s");
      $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
      $data1['clinic_doctor_weekday_id'] = $cdw_id;
      $data1['from_time'] = $toId;
      $data1['to_time'] = $fromId;
      $data1['session'] = $sessionDay;
      $data1['created_date_time'] = date("Y-m-d H:i:s");
      $data1['modified_date_time'] = date("Y-m-d H:i:s");
      $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
     }
   }
   else{
    // if(count($clinincDocInfo)>0)
    // {
      $data['weekday'] =  $daySelection;
      $data['clinic_doctor_id'] = $clinincDocInfo->clinic_doctor_id;
      $data['slot'] = 'video call';
      $data['created_date_time'] = date("Y-m-d H:i:s");
      $data['modified_date_time'] = date("Y-m-d H:i:s");
      $cdw_id = $this->Generic_model->insertDataReturnId("clinic_doctor_weekdays",$data);
      $data1['clinic_doctor_weekday_id'] = $cdw_id;
      $data1['from_time'] = $toId;
      $data1['to_time'] = $fromId;
      $data1['session'] = $sessionDay;
      $data1['created_date_time'] = date("Y-m-d H:i:s");
      $data1['modified_date_time'] = date("Y-m-d H:i:s");
      $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);
    // }
   }

  // redirect('settings/doctor_timings/'.$doctorId);
  // redirect('settings/staff');
}

public function insertTimingss()
{
  print_r($_POST);
  $abc =  $this->input->post('dayNum');
   print_r($abc);
  if($abc == '1')
  {
      $def = '1';
  }
  elseif($abc == '2')
  {
    $def = '2';
  } 
  elseif($abc == '3')
  {
    $def = '3';
  } 
  elseif($abc == '4')
  {
    $def = '4';
  }
  elseif($abc == '5')
  {
    $def = '5';
  }
  elseif($abc == '6')
  {
    $def = '6';
  }
  elseif($abc == '7')
  {
    $def = '7';
  }
  else{
    
  }

  $sdoctor_id = $this->input->post('sdoctor_id');
  $sclinic_id = $this->input->post('sclinic_id');
  $sSession = $this->input->post('sSession');
  $toId = $this->input->post('toId');
  $fromId = $this->input->post('fromId');
  // $dayNum = $this->input->post('dayNum');
  // print_r($dayNum);
  // $day = '3';
  // print_r($day);

  $clinincDocInfo = $this->db->query("select * from clinic_doctor where clinic_id='".$sclinic_id."' and doctor_id='".$sdoctor_id."'")->row();

        $cdwInfo = $this->db->query("select * from clinic_doctor_weekdays where 
        clinic_doctor_id='".$clinincDocInfo->clinic_doctor_id."' 
        and weekday='".$def."'")->row();

        // if(count($cdwInfo)>0)
        // {
            $cdw_id = $cdwInfo->clinic_doctor_weekday_id;
            $data1['clinic_doctor_weekday_id'] =$cdw_id;
            $data1['from_time'] = $toId;
            $data1['to_time'] = $fromId;
            $data1['session'] = $sSession;
            $data1['created_date_time'] = date("Y-m-d H:i:s");
            $data1['modified_date_time'] = date("Y-m-d H:i:s");
            $this->Generic_model->insertData('clinic_doctor_weekday_slots',$data1);    


}

public function patient_flow($doctor_id)
{
  $clinic_id = $this->session->userdata('clinic_id');
  $data['doctor_id']=$doctor_id;
  $data['clinic_doctor']=$clinic_doctor= $this->db->query("select * from clinic_doctor where 
  clinic_id='".$clinic_id."' 
  and doctor_id='".$doctor_id."'")->row();

  $data['fo_doc_flow']=$clinic_doctor->fo_doc_flow;

  // extract($_POST);

  //   // $data['fo_doc_flow'] = $patient_flow;
  //   $this->db->query("update clinic_doctor set 
  //   fo_doc_flow = '".$this->input->post("submitFlow")."' WHERE
  //   clinic_doctor_id  = '".$clinic_doctor->clinic_doctor_id."'");
    // $this->Generic_model->updateData("clinic_doctor",$data, array('clinic_doctor_id'=>$clinic_doctor->clinic_doctor_id));

  // redirect('settings/print');

  
  $data['view'] = 'settings/patient_flow';
  $this->load->view('layout',$data);
}



public function patient_floww()
{
  $abc =  $this->input->post('submitFlow');
  $doctor_id=$this->input->post('doctor_id');
  $clinic_id = $this->session->userdata('clinic_id');
  $data['clinic_doctor']=$clinic_doctor= $this->db->query("select * from clinic_doctor where 
  clinic_id='".$clinic_id."' 
  and doctor_id='".$doctor_id."'")->row();
  // extract($_POST);

  //   // $data['fo_doc_flow'] = $patient_flow;
    $this->db->query("update clinic_doctor set 
    fo_doc_flow = '".$abc."' WHERE
    clinic_doctor_id  = '".$clinic_doctor->clinic_doctor_id."'");
    // $this->Generic_model->updateData("clinic_doctor",$data, array('clinic_doctor_id'=>$clinic_doctor->clinic_doctor_id));

  // redirect('settings/print');

  
  // $data['view'] = 'settings/patient_flow';
  // $this->load->view('layout',$data);
}

}
?>