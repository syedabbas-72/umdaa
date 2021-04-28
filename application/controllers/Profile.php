<?php
error_reporting(0);
include "phpqrcode/qrlib.php";

defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('mail_send', array('mailtype'=>'html'));		 
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');	
    }


    public function index($patient_id = '', $appointment_id = ''){       

        // Should render appointments info and a patient info based on the Params.
        // If appointment Id is null then render all the appointments with status 'Booked' and there payment status.

        // Get clinic ID
        $clinic_id = $this->session->userdata('clinic_id');

        // Get accessing user's profile ID 
        $profile_id=$this->session->userdata('profile_id');

        // get appointments whose status are not in 'closed, dropped, absent';
        $status = array('closed', 'drop', 'absent');

        // retrieve the appointments
        $this->db->select('A.appointment_id, P.clinic_id, A.patient_id, P.umr_no, A.doctor_id, Doc.first_name as doctor_first_name, Doc.last_name as doctor_last_name, Dep.department_name, A.appointment_type, A.appointment_date, A.appointment_time_slot, A.priority, A.description, A.payment_status as appointment_payment_status, A.status as appointment_status, P.title, P.first_name, P.last_name, P.gender, P.date_of_birth, P.age, P.occupation, P.mobile, P.alternate_mobile, P.email_id, P.address_line, P.district_id, P.payment_status as registration_payment_status, D.district_name, P.state_id, S.state_name, P.pincode, P.photo, P.qrcode, P.preferred_language, P.allergy');
        $this->db->from('appointments A');
        $this->db->join('patients P','P.patient_id = A.patient_id');
        $this->db->join('doctors Doc','A.doctor_id = Doc.doctor_id');
        $this->db->join('department Dep','Doc.department_id = Dep.department_id');
        $this->db->join('districts D','P.district_id = D.district_id','left');
        $this->db->join('states S','P.state_id = S.state_id','left');
        $this->db->where('A.patient_id =',$patient_id);
        // $this->db->where_not_in('A.status',$status);
        
        // If the appointment Id is specified
        if($appointment_id) 
            $this->db->where('A.appointment_id =',$appointment_id);

        $data['appointmentInfo'] = $this->db->get()->result();

        // Get appointments information
        $data['app_info'] = $info = $this->db->query("select *,a.status as app_status from appointments a inner join doctors d on(a.doctor_id = d.doctor_id) where a.patient_id='".$patient_id."' and a.appointment_date <= '".date('Y-m-d')."' and a.status!='drop' and a.status!='reschedule'  order by a.appointment_date desc")->row();

        $data['patient_info'] = $this->db->query("select * from patients where patient_id='".$patient_id."'")->row();

        $data['patient_dt'] = $this->db->query("select p.*, d.district_name, s.state_name from patients p left join districts d on p.district_id = d.district_id left join states s on p.state_id = s.state_id where p.patient_id=".$patient_id."")->row();

        $data['clinic_id']=$clinic_id;
        $data['patient_id']=$patient_id;
        $data['appointment_id']=$appointment_id;
        $data['view'] = 'profile/patient_profile';
        $this->load->view('layout', $data);

    }

    // Directly transfer the appointment to doctor without nurse from front office
    public function forwardDoc($appointment_id='',$patient_id)
    {
        // status for doctor to get start button
        $para['status'] = "waiting";
        $res = $this->Generic_model->updateData("appointments", $para, array('appointment_id'=>$appointment_id));
        $ap_details = $this->db->query("select * from appointments where appointment_id='".$appointment_id."'")->row();
        $this->Generic_model->pushNotifications($ap_details->patient_id, $appointment_id, $ap_details->doctor_id, $ap_details->clinic_id, 'push_to_consultant', 'dashboard');
        $this->Generic_model->angularNotifications($ap_details->patient_id, $appointment_id, $ap_details->doctor_id, $ap_details->clinic_id, 'push_to_consultant', 'dashboard');
        // echo $this->db->last_query();
        redirect("profile/index/".$patient_id."/".$appointment_id);
      
    }

    public function patient_vitals($patient_id='',$appointment_id=''){

        $clinic_id = $this->session->userdata('clinic_id');
        $profile_id=$this->session->userdata('profile_id');

        if($clinic_id==0)
        {
        $data['profile_pages']=$this->db->query("select a.user_entity_name,a.user_entity_alias,a.method_name from user_entities a inner join profile_permissions b on a.user_entity_id=b.user_entity_id where profile_id=".$profile_id." and a.level_alias='page' and p_read=1 and parent_id!='' ORDER BY position,user_entity_name asc")->result();
        //$data['profile_pages']=$this->db->query("SELECT b.user_entity_name,b.user_entity_alias FROM `profile_permissions` a inner join user_entities b on a.user_entity_id=b.user_entity_id where a.profile_id='".$profile_id."' and b.level_alias='page' and parent_id!='' ")->result();
        }
        else
        {
        $data['profile_pages']=$this->db->query("select c.user_entity_name,c.user_entity_alias,c.method_name from std_uac_entities a inner join user_entities c on a.user_entity_id=c.user_entity_id inner join profile_permissions b on a.user_entity_id=b.user_entity_id where profile_id=".$profile_id." and c.level_alias='page' and p_read=1  and parent_id!='' ORDER BY position,user_entity_name asc")->result();
        //echo $this->db->last_query();
        //$data['profile_pages']=$this->db->query("SELECT b.user_entity_name,b.user_entity_alias FROM `profile_permissions` a inner join user_entities b on a.user_entity_id=b.user_entity_id where a.profile_id='".$profile_id."' and b.level_alias='page' and parent_id!='' ")->result();
        }
        $data['patient_info'] = $this->db->query("select * from patients where patient_id=".$patient_id)->row();
        $data['result'] = $this->db->query('select vital_sign_recording_date_time from patient_vital_sign where patient_id = "'.$patient_id.'" and appointment_id = "'.$appointment_id.'" group by vital_sign_recording_date_time order by vital_sign_recording_date_time desc')->result();

        $data['clinic_id']=$clinic_id;
        $data['patient_id']=$patient_id;
        $data['appointment_id']=$appointment_id;
        $data['view'] = 'vitals/vitals_view';
        $this->load->view('layout', $data);

    }

  public function add_vitals($patient_id='',$appointment_id=''){
   $clinic_id = $this->session->userdata('clinic_id');
   $profile_id=$this->session->userdata('profile_id');


   if($clinic_id==0)
   {
      $data['profile_pages']=$this->db->query("select a.user_entity_name,a.user_entity_alias,a.entity_url,a.method_name from user_entities a inner join profile_permissions b on a.user_entity_id=b.user_entity_id where profile_id=".$profile_id." and a.level_alias='page' and p_read=1 and parent_id!='' ORDER BY position,user_entity_name asc")->result();

  }
  else
  {
      $data['profile_pages']=$this->db->query("select c.user_entity_name,c.entity_url,c.user_entity_alias,c.method_name from std_uac_entities a inner join user_entities c on a.user_entity_id=c.user_entity_id inner join profile_permissions b on a.user_entity_id=b.user_entity_id where profile_id=".$profile_id." and c.level_alias='page' and p_read=1  and parent_id!='' ORDER BY position,user_entity_name asc")->result();

  }
  $data['patient_info'] = $this->db->query("select * from patients where patient_id=".$patient_id)->row();

  $data['clinic_id']=$clinic_id;
  $data['patient_id']=$patient_id;
  $data['appointment_id']=$appointment_id;
  $data['view'] = 'vitals/add_vitals';
  $this->load->view('layout', $data);

}

public function vitals_save($patient_id='',$appointment_id='')
{


    // echo '<pre>';
   $get_vitals = $this->input->post('vitals');
    // print_r($this->input->post('vitals'));

    // if($get_vitals['BP'] != ''){
    //   $BP = explode("/",$get_vitals['BP']);
    //   $get_vitals['SBP'] = $BP[0];
    //   $get_vitals['DBP'] = $BP[1];
    //   unset($get_vitals['BP']);
    // }
  //  echo "<pre>test";print_r($_POST);exit;
   $appointment_info = $this->db->query('select * from appointments where appointment_id ='.$appointment_id.' AND patient_id='.$patient_id)->row();
   

   $j=0;
   foreach ($get_vitals as $name => $result) {
      if($result != ''){
        $data['patient_id'] = $appointment_info->patient_id;
        $data['umr_no'] = $appointment_info->umr_no;
        $data['clinic_id'] = $appointment_info->clinic_id;
        $data['appointment_id'] = $appointment_info->appointment_id;
        $data['vital_sign'] = $name;
        $data['vital_result'] = round($result,2);
        $data['position'] = $j;
        $data['vital_sign_recording_date_time'] = date('Y-m-d H:i:s');
        $data['status'] = 1;
        $data['created_by'] = $this->session->userdata('user_id');
        $data['modified_by'] = $this->session->userdata('user_id');
        $data['created_date_time'] = date('Y-m-d H:i:s');
        $data['modified_date_time'] = date('Y-m-d H:i:s');
       // echo "<pre>";print_r($data);
        $res = $this->Generic_model->insertData('patient_vital_sign',$data);
        if($res){

          $this->db->query("UPDATE appointments set status='vital_signs' where appointment_id='".$appointment_info->appointment_id."' and patient_id='".$appointment_info->patient_id."'");	
      }


  } 
  if($name == "SBP"){}else{$j++;}   /**/ 
}
$k=$j;
$count = count($this->input->post('vital_sign'));
$get_vital_sign = $this->input->post('vital_sign');
$get_vital_sign_val = $this->input->post('vital_sign_val');
$get_more_allergies = $this->input->post('more_allergies');

if($count){
  for ($i=0; $i<$count; $i++) {
    if($get_vital_sign[$i] !='' || $get_vital_sign[$i] != NULL)
    {
     $data['patient_id'] = $appointment_info->patient_id;
     $data['umr_no'] = $appointment_info->umr_no;
     $data['clinic_id'] = $appointment_info->clinic_id;
     $data['appointment_id'] = $appointment_info->appointment_id;
     $data['vital_sign'] = $get_vital_sign[$i];
     $data['vital_result'] = $get_vital_sign_val[$i];
     $data['position'] = $k++;
     $data['vital_sign_recording_date_time'] = date('Y-m-d H:i:s');
     $data['status'] = 1;
     $data['created_by'] = $this->session->userdata('user_id');
     $data['modified_by'] = $this->session->userdata('user_id');
     $data['created_date_time'] = date('Y-m-d H:i:s');
     $data['modified_date_time'] = date('Y-m-d H:i:s');

     $res = $this->Generic_model->insertData('patient_vital_sign',$data);
 }

}  
}


$get_more_allergies = $this->input->post('more_allergies');
if(trim($this->input->post("allergy")) !="" && trim($get_more_allergies) ==""){

  $pa['allergy'] = trim($this->input->post("allergy"));
  $this->Generic_model->updateData("patients",$pa,array('patient_id'=>$appointment_info->patient_id)); 
}
if(trim($this->input->post("allergy")) =="No" && trim($get_more_allergies) =="No"){

  $pa['allergy'] = trim($this->input->post("allergy"));
  $this->Generic_model->updateData("patients",$pa,array('patient_id'=>$appointment_info->patient_id)); 
}    
if(trim($this->input->post("allergy")) =="" && (trim($get_more_allergies) !="")){
   if(trim($get_more_allergies) =="No"){
       $pma['allergy'] = "No";

       $this->Generic_model->updateData("patients",$pma,array('patient_id'=>$appointment_info->patient_id)); 
   }
   else{

      $af = $this->db->query('select * from patients where patient_id='.$appointment_info->patient_id)->row();
      if($af->allergy == "" || $af->allergy =='No')
      {

         $pma['allergy'] = trim($get_more_allergies);
     }else{

         $pma['allergy'] = $af->allergy.",".trim($get_more_allergies);


     }
     $this->Generic_model->updateData("patients",$pma,array('patient_id'=>$appointment_info->patient_id)); 
 }
}


    // if($this->input->post("allergy") != "No" || $this->input->post("allergy") != ''){
    //   $pa['allergy'] = $this->input->post('drug_allergy').', '.$this->input->post("allergy");
    //   $this->Generic_model->updateData("patients",$pa,array('patient_id'=>$appointment_info->patient_id));  
    // }else{
    //   $pa['allergy'] = $this->input->post('drug_allergy');      
    //   $this->Generic_model->updateData("patients",$pa,array('patient_id'=>$appointment_info->patient_id));
    // }

	// if($more_allergies != "" || $more_allergies != NULL || $more_allergies != "No")
	// {
	// 	$af = $this->db->query('select * from patients where patient_id='.$appointment_info->patient_id)->row();
	// 	if($af->allergy == "" || $af->allergy =='No')
	// 	{
	// 		$pma['allergy'] = $more_allergies;
	// 	}else{
	// 		$pma['allergy'] = $af->allergy.",".$more_allergies;

	// 	}
	// 	$this->Generic_model->updateData("patients",$pma,array('patient_id'=>$appointment_info->patient_id));
	// }




$this->Generic_model->pushNotifications($ap_details->patient_id,$appointment_id,$ap_details->doctor_id,$ap_details->clinic_id,'push_to_consultant','patient_21_details_tab');


   // if($this->input->post("print")){
$this->print_vitals($appointment_id);
    // }else{
    //   redirect('Vitals/patient_vitals/'.$patient_id.'/'.$appointment_id);
    // }
}

public function print_vitals($appid) {

   $data['appointments'] = $this->db->query("select a.*, c.clinic_name,c.clinic_logo,c.address,c.clinic_phone,p.first_name as pname,p.last_name as plname,p.gender,p.age,p.allergy,p.address_line,p.mobile,p.qrcode,d.salutation,d.first_name as dfname,d.last_name as dlname,d.qualification,d.registration_code,dp.department_name from appointments a 
      left join clinics c on a.clinic_id = c.clinic_id
      left join patients p on a.patient_id = p.patient_id

      left join doctors d on a.doctor_id = d.doctor_id
      left join department dp on d.department_id = dp.department_id
      where a.appointment_id='".$appid."'")->row();
   $patient_name = $data['appointments']->pname." ".$data['appointments']->plname.date('Ymd').time();


   $data['vital_sign'] = $this->db->query("select * from patient_vital_sign where appointment_id='".$appid."' order by patient_vital_id")->result();



   $data['pdf_settings'] = $pdf_settings = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$data['appointments']->clinic_id."'")->row();

      // print_r($pdf_settings);exit;

   $this->load->library('M_pdf');
   $html = $this->load->view('reports/vital_print', $data, true);
   $pdfFilePath = strtolower(str_replace(" ", "_", $patient_name)) . ".pdf";
   $stylesheet  = '';
    $stylesheet .= file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
    $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");
    $this->m_pdf->pdf->autoScriptToLang = true;
    $this->m_pdf->pdf->autoLangToFont = true;

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
  'content' => '<span style="font-size:14px;"><b>Date: </b>'.date("d/m/Y").'</span>',
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
    
    if($pdf_settings->footer == 1){
        $this->m_pdf->pdf->SetFooter($Footer);
    }
    else{
        $this->m_pdf->pdf->SetFooter('<div style="height:'.$pdf_settings->footer_height.'px;"></div>');
    }
}


$this->m_pdf->pdf->WriteHTML($stylesheet,1);
$this->m_pdf->pdf->WriteHTML($html,2);
$this->m_pdf->pdf->Output("./uploads/vital_reports/".$pdfFilePath, "F"); 
redirect("uploads/vital_reports/".$pdfFilePath);

} 

public function vital_edit($patient_id='',$appointment_id='')
{

 $clinic_id = $this->session->userdata('clinic_id');
 $profile_id=$this->session->userdata('profile_id');


 if($clinic_id==0)
 {
  $data['profile_pages']=$this->db->query("select a.user_entity_name,a.user_entity_alias,a.method_name from user_entities a inner join profile_permissions b on a.user_entity_id=b.user_entity_id where profile_id=".$profile_id." and a.level_alias='page' and p_read=1 and parent_id!='' ORDER BY position,user_entity_name asc")->result();
}
else
{
  $data['profile_pages']=$this->db->query("select c.user_entity_name,c.user_entity_alias,c.method_name from std_uac_entities a inner join user_entities c on a.user_entity_id=c.user_entity_id inner join profile_permissions b on a.user_entity_id=b.user_entity_id where profile_id=".$profile_id." and c.level_alias='page' and p_read=1  and parent_id!='' ORDER BY position,user_entity_name asc")->result();
}
$data['patient_info'] = $this->db->query("select * from patients where patient_id=".$patient_id)->row();

$result = $this->db->query('select * from patient_vital_sign where patient_id = "'.$patient_id.'" and appointment_id = "'.$appointment_id.'"  and  vital_sign_recording_date_time = (select max(vital_sign_recording_date_time) From patient_vital_sign where patient_id="'.$patient_id.'") order by  vital_sign_recording_date_time desc,position asc')->result();

foreach($result as $vital)
{
   $is_exist = $this->Generic_model->getSingleRecord('vital_sign',array('short_form'=>$vital->vital_sign),'');
   if(count($is_exist)>0){
     $vital_key_sign[$vital->vital_sign] = $vital->vital_result;
 }else{
  if($vital->vital_sign == 'WH_ratio' || $vital->vital_sign == 'BSA'){
     $vital_key_sign[$vital->vital_sign] = $vital->vital_result;
 }else{
  $vital_key_sign_other[$vital->vital_sign] = $vital->vital_result; 
} 
}
$vital_recording_time[] = $vital->vital_sign_recording_date_time;
}

$data['vital_key_sign'] = $vital_key_sign;
$data['vital_key_sign_other'] = $vital_key_sign_other;
$data['vital_r_time'] = $vital_recording_time;
$data['clinic_id']=$clinic_id;
$data['patient_id']=$patient_id;
$data['appointment_id']=$appointment_id;
$data['view'] = 'vitals/edit_vital_dev';
$this->load->view('layout', $data);
}

public function edit_save($patient_id='',$appointment_id='')
{
    /*echo "<pre>";print_R($this->input->post());
    exit;*/
    $get_vitals = $this->input->post('vitals');
    $appointment_info = $this->db->query('select * from appointments where appointment_id ='.$appointment_id)->row();

    $j=0;
    foreach ($get_vitals as $name => $result) {
     $data['patient_id'] = $appointment_info->patient_id;
     $data['umr_no'] = $appointment_info->umr_no;
     $data['clinic_id'] = $appointment_info->clinic_id;
     $data['appointment_id'] = $appointment_info->appointment_id;
     $data['vital_sign'] = $name;
     $data['vital_result'] = $result;
     $data['position'] = $j++;
     $data['vital_sign_recording_date_time'] = date('Y-m-d H:i:s');
     $data['status'] = 0;
     $data['created_by'] = $this->session->userdata('user_id');
     $data['modified_by'] = $this->session->userdata('user_id');
     $data['created_date_time'] = date('Y-m-d H:i:s');
     $data['modified_date_time'] = date('Y-m-d H:i:s');

     $res=$this->db->query("SELECT count(*) as num_rows from patient_vital_sign where patient_id ='".$appointment_info->patient_id."'  and vital_sign_recording_date_time ='".$this->input->post('vital_sign_date_time')."'")->row();


     if($res->num_rows >0){
      $this->db->query("DELETE FROM patient_vital_sign where patient_id ='".$appointment_info->patient_id."'  and vital_sign_recording_date_time ='".$this->input->post('vital_sign_date_time')."'");
      $this->Generic_model->insertData('patient_vital_sign',$data);
  }else{
    $this->Generic_model->insertData('patient_vital_sign',$data);
}
}

$k=$j;
$count = count($this->input->post('vital_sign'));
$get_vital_sign = $this->input->post('vital_sign');
$get_vital_sign_val = $this->input->post('vital_sign_val');
$get_more_allergies = $this->input->post('more_allergies');

if($count){
  for ($i=0; $i<$count; $i++) {
    if($get_vital_sign[$i] !='' || $get_vital_sign[$i] != NULL)
    {
     $data['patient_id'] = $appointment_info->patient_id;
     $data['umr_no'] = $appointment_info->umr_no;
     $data['clinic_id'] = $appointment_info->clinic_id;
     $data['appointment_id'] = $appointment_info->appointment_id;
     $data['vital_sign'] = $get_vital_sign[$i];
     $data['vital_result'] = $get_vital_sign_val[$i];
     $data['position'] = $k++;
     $data['vital_sign_recording_date_time'] = date('Y-m-d H:i:s');
     $data['status'] = 1;
     $data['created_by'] = $this->session->userdata('user_id');
     $data['modified_by'] = $this->session->userdata('user_id');
     $data['created_date_time'] = date('Y-m-d H:i:s');
     $data['modified_date_time'] = date('Y-m-d H:i:s');

     $res=$this->db->query("SELECT count(*) as num_rows from patient_vital_sign where patient_id ='".$appointment_info->patient_id."'  and vital_sign_recording_date_time ='".$this->input->post('vital_sign_date_time')."'")->row();


     if($res->num_rows >0){
      $this->db->query("DELETE FROM patient_vital_sign where patient_id ='".$appointment_info->patient_id."'  and vital_sign_recording_date_time ='".$this->input->post('vital_sign_date_time')."'");
      $this->Generic_model->insertData('patient_vital_sign',$data);
  }else{
    $this->Generic_model->insertData('patient_vital_sign',$data);
}
}

}  
}



redirect('Vitals/patient_vitals/'.$patient_id.'/'.$appointment_id);

}

public function closeAppointment($appointment_id, $patient_id='')
{
    $data['status'] = "closed";
    $data['check_out_time'] = date("Y-m-d H:i:s");

    $this->Generic_model->updateData('appointments',$data,array('appointment_id'=>$appointment_id));

    redirect("profile/index/".$patient_id);

}



}