<?php

error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Doctor_settings extends MY_Controller {
 public function __construct(){      
    parent::__construct();
    if(!$this->session->has_userdata('is_logged_in'))

      {

        redirect('Authentication/login');

      }
        
   }
public function index(){
  $clinic_id = $this->session->userdata('clinic_id');
  $doctor_id = $this->session->userdata('user_id');
 
  
    $data['clinic_list']=$this->db->query("select * from clinics c inner join clinic_doctor cd on(cd.clinic_id = c.clinic_id) where cd.doctor_id='".$doctor_id."' ")->result();
      $data['view'] = 'doctor_settings/clinic_settings';
      $this->load->view('layout', $data);
    

}
public function user($cid='',$did=''){
  $clinic_id = $cid;
  $doctor_id = $did;
 
    $data['clinic_id'] = $clinic_id;
    $data['doctor_id'] = $doctor_id;
     $data['user_info']=$this->db->query("select * from users u inner join user_doctor ud on(u.user_id = ud.user_id) where ud.doctor_id='".$doctor_id."' and ud.clinic_id='".$clinic_id."'")->row();
  
    if(count($data['user_info'])>0){
      $data['employee_info'] = $this->db->query("select * from employees where employee_id='".$data['user_info']->user_id."'")->row();

       $data['view'] = 'doctor_settings/user_edit';
    }
    else{
      
       $data['view'] = 'doctor_settings/user_add';
    }
     
      $this->load->view('layout', $data);
  
   
    

}

public function user_add($cid='',$did=''){
    $clinic_id = $cid;
  $doctor_id = $did;
    $data['clinic_id'] = $clinic_id;
    $data['doctor_id'] = $doctor_id;
  if($this->input->post('submit')){
    $pwd = "1234";
    
    $user['password']=md5($pwd);
    $user['email_id']=$this->input->post('email_id');
    $user['mobile']=$this->input->post('mobile');
    $user['clinic_id']= $this->session->userdata('clinic_id');
    $user['user_type']='employee';
    $user['role_id']=3;
    $user['profile_id']=3;
    $user['created_by'] = $user_id;
    $user['modified_by'] = $user_id;
    $user['created_date_time'] = date('Y-m-d H:i:s');
    $user['modified_date_time'] = date('Y-m-d H:i:s');
    
    $emp_id = $this->Generic_model->insertDataReturnId("users",$user);  
  
    $empcode = 'EMP-'.date('Ymd').$emp_id;
    $emp['employee_id']=$emp_id;
    $emp['employee_code']=$empcode;
    //$emp['title']=$this->input->post('title');
    $emp['first_name']=$this->input->post('first_name');
    $emp['last_name']=$this->input->post('last_name');
    $emp['gender']=$this->input->post('gender');
    $emp['date_of_birth']= date('Y-m-d',strtotime($this->input->post('date_of_birth')));
    $emp['date_of_joining']=date('Y-m-d',strtotime($this->input->post('date_of_joining')));
    $emp['qualification']=$this->input->post('qualification');
    $emp['mobile']=$this->input->post('mobile');
    $emp['email_id']=$this->input->post('email_id');
        $emp['adhaar_no']=$this->input->post('adhaar_no');
        $emp['pan_no']=$this->input->post('pan_no');
        $emp['bank_account_no']=$this->input->post('bank_account_no');
    $emp['clinic_id']=$cid;
    $emp['address']=$this->input->post('address');
    $emp['status']=1;
    $emp['created_by']=$did;
    $emp['modified_by']=$did;
    $emp['created_date_time']=date('Y-m-d H:i:s');
    $emp['modified_date_time']=date('Y-m-d H:i:s');
    $this->Generic_model->insertData('employees',$emp);
    $empCu['username']=$empcode;
   $this->Generic_model->updateData("users",$empCu,array('user_id'=>$emp_id));

    $ud['user_id'] = $emp_id;
    $ud['doctor_id'] = $doctor_id;
    $ud['clinic_id'] = $clinic_id;
    $ud['created_date_time'] = date("Y-m-d H:i:s");
    $this->Generic_model->insertData("user_doctor",$ud); 
    
      redirect('doctor_settings/user/'.$cid.'/'.$did);
  }
}
public function user_update($cid='',$did='',$uid){
    $clinic_id = $cid;
  $doctor_id = $did;
    $data['clinic_id'] = $clinic_id;
    $data['doctor_id'] = $doctor_id;
 if($this->input->post('submit')){
    $emp['first_name']=$this->input->post('first_name');
    $emp['last_name']=$this->input->post('last_name');
    $emp['gender']=$this->input->post('gender');
    $emp['date_of_birth']= date('Y-m-d',strtotime($this->input->post('date_of_birth')));
    $emp['date_of_joining']=date('Y-m-d',strtotime($this->input->post('date_of_joining')));
    $emp['qualification']=$this->input->post('qualification');
    $emp['mobile']=$this->input->post('mobile');
        $emp['adhaar_no']=$this->input->post('adhaar_no');
        $emp['pan_no']=$this->input->post('pan_no');
        $emp['bank_account_no']=$this->input->post('bank_account_no');
    $emp['email_id']=$this->input->post('email_id');
    $emp['clinic_id']= $cid;
    $emp['address']=$this->input->post('address');
    $emp['status']=$this->input->post('status');
    $emp['modified_by']= $did;
    $emp['modified_date_time']=date('Y-m-d H:i:s');

    $this->Generic_model->updateData('employees',$emp,array('employee_id'=>$uid));

    $user['email_id']=$this->input->post('email_id');
    $user['mobile']=$this->input->post('mobile');
    $user['clinic_id']= $cid;
    $user['modified_by'] =  $did;
    $user['modified_date_time'] = date('Y-m-d H:i:s');
    $this->Generic_model->updateData('users',$user,array('user_id'=>$uid));
    redirect('doctor_settings/user/'.$cid.'/'.$did);
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

 public function doctor_info($cid='',$did=''){

 
   $suser_id = $this->session->has_userdata('user_id'); 
  if($this->input->post('submit')){

    
    $clinic_doctor['consulting_fee']=$this->input->post('consulting_fee');
    $clinic_doctor['review_days']=$this->input->post('review_days');
    $clinic_doctor['review_times']=$this->input->post('review_times');
    
    $this->Generic_model->updateData('clinic_doctor',$clinic_doctor, array('doctor_id'=>$did,'clinic_id'=>$cid));
    
    
redirect('doctor_settings/doctor_info/'.$cid.'/'.$did); 

}
else{
      $data['department_list']=$this->db->query('select * from department')->result();
    $data['state_list']=$this->db->query('select * from states')->result();
    $data['doctor_list']=$this->db->query('select * from doctors a inner join doctor_package b on a.doctor_id=b.doctor_id where b.clinic_id="'.$cid.'" and a.doctor_id="'.$did.'"')->row();
    $data['clinic_list']=$this->db->query("select * from clinics where clinic_id='".$cid."'")->row();
    $data['packages_list']=$this->db->query('select * from subscription')->result();
    $data['clinic_doctor']=$this->db->query("select * from clinic_doctor where clinic_id='".$cid."' and doctor_id=".$did)->row();
    $data['type'] = $data['clinic_doctor']->primary_clinic;
    //echo "<pre>";print_r($data['doctor_list']);
    $data['view'] = 'doctor_settings/doctor_info';
    $this->load->view('layout', $data);
  }
}

public function doctor_timings($clinic_id='',$doctor_id='')
   {
     //$clinic_id = $this->session->userdata('clinic_id');
     $data['doctor_info'] =$this->db->query('select doc.*, dep.department_name from doctors doc INNER JOIN department dep ON doc.department_id = dep.department_id where doctor_id ='.$doctor_id)->row();
   
    $data['weekdays']=$this->db->query('select * from clinic_doctor_weekdays cd inner join clinic_doctor_weekday_slots cs on(cd.clinic_doctor_weekday_id = cs.clinic_doctor_weekday_id) left join clinic_doctor cdd on cdd.clinic_doctor_id = cd.clinic_doctor_id where cdd.clinic_id = "'.$clinic_id.'" and cdd.doctor_id = "'.$doctor_id.'" group by cd.clinic_doctor_weekday_id,cd.weekday')->result();

     $doctor_id = $this->db->query('select * from clinic_doctor cd inner join clinics c on(cd.clinic_id = c.clinic_id)  where cd.clinic_id = "'.$clinic_id.'"and cd.doctor_id = "'.$doctor_id.'"')->row();
     
   $data['clinic_doctor_id']=$doctor_id->clinic_doctor_id;
   $data['clinic_name']=$doctor_id->clinic_name;
   $data['type'] = $doctor_id->primary_clinic;
 
     $data['view'] = 'doctor_settings/doctor_timings';
   $this->load->view('layout',$data);
   }

 public function block_dates($clinic_id='',$doctor_id = '')
 {
  //$clinic_id = $this->session->userdata('clinic_id');
    $data['weekdays']=$this->db->query('select * from calendar_blocking where clinic_id="'.$clinic_id.'" and doctor_id ='.$doctor_id)->result();
    $data['clinic_info'] = $this->db->query('select * from clinics where clinic_id="'.$clinic_id.'"')->row();
    $data['clinic_doctor']=$this->db->query("select * from clinic_doctor where clinic_id='".$clinic_id."' and doctor_id=".$doctor_id)->row();
    $data['type'] = $data['clinic_doctor']->primary_clinic;
    $data['view'] = 'doctor_settings/block_dates';

    $this->load->view('layout',$data);

 }

 public function cal_date_del($id = '')
 {
   $ok = $this->db->query('delete from calendar_blocking where calendar_blocking_id = '.$id);
   if($ok){
   redirect('doctor_settings/block_dates/'.$doctor_id);
  }
 }

 public function add_clinic()
 {
   $data['view'] = 'doctor_settings/add_clinic';
   $data['clinic_list']=$this->db->query("select * from clinics")->result();
    $this->load->view('layout',$data);
  }

  public function search_clinic()
 {
  $data['clinic_list'] = $this->db->query("select * from clinics where clinic_name like '%".$this->input->post('clinic_name')."%' and clinic_id NOT IN(".$this->session->userdata('clinic_id').")")->result();
 
   $data['view'] = 'doctor_settings/search_clinic';

    $this->load->view('layout',$data);
  }
    public function map_clinic()
 {

  if($this->input->post("clinic_name") == "new"){
      redirect("doctor_settings/create_clinic");
  }

else{
  $check_clinic = $this->db->query("select * from clinic_doctor where clinic_id='".$this->input->post('clinic_name')."' and doctor_id='".$this->session->userdata('user_id')."'")->num_rows();
  $url = base_url("doctor_settings");
  if($check_clinic > 0){
   echo "<script>alert('Clinic already Added');window.location.href='".$url."';</script>";
   //redirect("doctor_settings");
  }
  else{
      $data['clinic_id']=$this->input->post('clinic_name');
      $data['doctor_id']= $this->session->userdata('user_id'); 
      $data['created_date_time']= date("Y-m-d H:i:s"); 
      $data['modified_date_time']= date("Y-m-d H:i:s"); 
      $this->Generic_model->insertData('clinic_doctor',$data);
      redirect("Admin/doctorSlots/".$data['doctor_id']."/".$data['clinic_id']);
      // redirect("doctor_settings");
  }

 
 
}
  }
 

  public function create_clinic()
 {
  if($this->input->post('submit')){
      $config['upload_path']="./uploads/clinic_logos/";
      $config['allowed_types']='jpg|JPG|png|PNG|jpeg|JPEG'; 
      $this->load->library('upload');    
      $this->upload->initialize($config);
      $this->upload->do_upload('clinic_logo');
      $fileData=$this->upload->data('file_name');
     

    $data['clinic_name']=$this->input->post('clinic_name');
    $data['clinic_logo']=$fileData;
    $data['clinic_type']='CUSTOMER';
    $data['clinic_phone']=$this->input->post('clinic_phone');
    $data['email']=$this->input->post('clinic_email');
    $data['address']=$this->input->post('address');
    $data['pharmacy_discount']=$this->input->post('pharmacy_discount');
    $data['lab_discount']=$this->input->post('lab_discount');
    $data['registration_fee']=$this->input->post('registration_fee');
    $data['incharge_name']=$this->input->post('clinic_incharge');
    $data['incharge_mobile']=$this->input->post('incharge_mobile');
    $data['incharge_email']=$this->input->post('incharge_email');
    $data['modified_date_time']=date('Y-m-d H:i:s');
    $cid = $this->Generic_model->insertDataReturnId('clinics',$data);

     $cdata['clinic_id']=$cid;
  $cdata['doctor_id']= $this->session->userdata('user_id'); 
  $cdata['created_date_time']= date("Y-m-d H:i:s"); 
  $cdata['modified_date_time']= date("Y-m-d H:i:s"); 
  $this->Generic_model->insertData('clinic_doctor',$cdata);

        $this->session->set_flashdata('msg','Clinic created successfully');
        redirect('doctor_settings');
      
     
  }
  else{
    $data['view'] = 'doctor_settings/create_clinic';

    $this->load->view('layout',$data);
  }
    
 }

 
}
?>