<?php
error_reporting(0);
include "phpqrcode/qrlib.php";

defined('BASEPATH') OR exit('No direct script access allowed');

class CaseSheet extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('mail_send', array('mailtype'=>'html'));		 
  		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');	
    }


public function index(){
$clinic_id = $this->session->userdata('clinic_id');
$profile_id=$this->session->userdata('profile_id');
$cond = '';
if($clinic_id!=0)
	$cond = "clinic_id=".$clinic_id." and";
  $data['clinics']=$this->db->query('select clinic_id,clinic_name from clinics where '.$cond.' archieve=0 order by clinic_name asc')->result();
  
  $data['profile_pages']=$this->db->query("SELECT b.user_entity_name,b.user_entity_alias FROM `profile_permissions` a inner join user_entities b on a.user_entity_id=b.user_entity_id where a.profile_id='".$profile_id."' and b.level_alias='page' and parent_id!='' ")->result();
 
  
  $data['clinic_id']=$clinic_id;
	$data['view'] = 'reports/case-sheet';
    $this->load->view('layout', $data);
  
}


	/*public function patient_info($patient_id='',$appointment_id=''){

	 $clinic_id = $this->session->userdata('clinic_id');
	 $profile_id=$this->session->userdata('profile_id');
	$cond = '';
	if($clinic_id!=0)
		$cond = "clinic_id=".$clinic_id." and";
	  $data['clinics']=$this->db->query('select clinic_id,clinic_name from clinics where '.$cond.' archieve=0 order by clinic_name asc')->result();
	  $data['profile_pages']=$this->db->query("SELECT b.user_entity_name,b.user_entity_alias FROM `profile_permissions` a inner join user_entities b on a.user_entity_id=b.user_entity_id where a.profile_id='".$profile_id."' and b.level_alias='page' and parent_id!='' ")->result();
	  
	  $data['patient_info'] = $this->db->query("select * from patients where patient_id=".$patient_id)->row();
	  $data['clinic_id']=$clinic_id;
	  $data['appointment_id']=$appointment_id;
	  $data['patient_id']=$patient_id;  
	$data['view'] = 'reports/case-sheet';
	$this->load->view('layout', $data);
	  
	}*/
	
	public function patient_info($patient_id='',$appointment_id=''){

	 $clinic_id = $this->session->userdata('clinic_id');
	 $profile_id=$this->session->userdata('profile_id');
	$cond = '';
	if($clinic_id!=0)
		$cond = "clinic_id=".$clinic_id." and";
	  $data['clinics']=$this->db->query('select clinic_id,clinic_name from clinics where '.$cond.' archieve=0 order by clinic_name asc')->result();
	  
	  $data['profile_pages']=$this->db->query("SELECT b.user_entity_name,b.user_entity_alias FROM `profile_permissions` a inner join user_entities b on a.user_entity_id=b.user_entity_id where a.profile_id='".$profile_id."' and b.level_alias='page' and parent_id!='' ")->result();
	  
	  $data['patient_info'] = $this->db->query("select * from patients where patient_id=".$patient_id)->row();
	  $data['clinic_id']=$clinic_id;
	  $data['appointment_id']=$appointment_id;
	  $data['patient_id']=$patient_id;
	  
	  redirect('Vitals/index/'.$patient_id.'/'.$appointment_id);
	  
	 $data['view'] = 'reports/case-sheet';
	$this->load->view('layout', $data);
	}


public function getClinicPatients(){
  //$clinic_id = $_POST['clinic_id'];
  $clinic_id =$this->session->userdata('clinic_id');
/*  $patients = $this->db->query("select patient_id,first_name from patients where clinic_id='".$clinic_id."' ")->result();*/
  $patients = $this->db->query("select p.patient_id,p.first_name from  patients p  where p.clinic_id='".$clinic_id."' group by p.patient_id")->result();
 
  $patient = '<label for="patient_id" class="col-form-label">PATIENT</label>
  <select name="patient_id" id="patient_id" class="form-control" onchange="getPatientDetails(this.value)" required>
  <option value=""> Select Patient </option>';
  foreach ($patients as $key => $value) {
   $patient.='<option value="'.$value->patient_id.','.$value->appointment_id.'">'.$value->first_name.'</option>';
  }
  $patient.='</select>';
  echo $patient;
}

public function getConsentForms()
{
	$department_id = explode(",",$_POST['department_id']);
  
  $consent_forms=$this->Generic_model->getAllRecords('consent_form',array('department_id'=>$department_id[0]),$order='');
  $cf = '<div class="col-md-6"><label for="patient_id" class="col-form-label">CONSENT FORM</label>
  <select name="consent_form_id" id="consent_form_id" class="form-control" onchange="download_consent(this.value)" required>
  <option value=""> Select Consent Form </option>';
  foreach ($consent_forms as $c_f) {
	  
   $cf.='<option value="'.$c_f->consent_form_id.','.$department_id[1].'">'.$c_f->consent_form_title.'</option>';
  }
  $cf.='</select>
  </div><div id="cd"></div>';
  echo $cf;
}

public function print_vitals($appid) {

 $data['appointments'] = $this->db->query("select a.*, c.clinic_name,c.clinic_logo,c.address,c.clinic_phone,p.first_name as pname,p.last_name as plname,p.gender,p.age,p.allergy,p.address_line,p.mobile,p.qrcode,d.salutation,d.first_name as dfname,d.doctor_id as did,d.last_name as dlname,d.qualification,d.registration_code,dp.department_name from appointments a 
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

    $this->m_pdf->pdf->shrink_tables_to_fit = 1;
    $this->m_pdf->pdf->setAutoTopMargin = "stretch";
     $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
    $this->m_pdf->pdf->defaultheaderline = 0;

    //setting for header layout
//     $HeaderConfiguration = 
//  [
//     'L' => [ // L for Left part of the header
//       'content' => '<div><img style="width:50%" alt="" src="'.base_url().'uploads/clinic_logos/health_inn3.png"></div>',
//     ],
//     'C' => [ // C for Center part of the header
//       'content' => '<div style="width:150px"></div>',
//     ],
//     'R' => [
//       'content' => '<div><span style="font-weight: bold;font-size: 13px">Address</span><br><span style="font-weight: 700;font-size: 13px">'.$data['appointments']->address.'</span><br><span style=" font-weight: bold;font-size: 13px">Phone:</span><span style="font-weight: 700;font-size: 13px">'.$data['appointments']->clinic_phone.'</span></div>',
//     ],
//     'line' => 0, // That's the relevant parameter
//   ];

//     $footerConfiguration = 
//     [
//      'L' => [ // L for Left part of the header
//       'content' => '',
//     ],
//     'C' => [ // C for Center part of the header
//       'content' => '',
//     ],
//     'R' => [
//       'content' => '<span style="font-size:14px;"><b>Date: </b>'.date("d/m/Y").'</span>',
//     ],
//     'line' => 0, // That's the relevant parameter
//     ];


  
// $Header = [
//   'odd' => $HeaderConfiguration,
//   'even' => $HeaderConfiguration
// ];
// $Footer = [
//   'odd' => $footerConfiguration,
//   'even' => $footerConfiguration
// ];
//   $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
//     $this->m_pdf->pdf->defaultheaderline = 0;
// if(count($pdf_settings)>0){

//     if($pdf_settings->header == 1){
//         $this->m_pdf->pdf->SetHeader($Header);
//     }
//     else{
//         $this->m_pdf->pdf->SetHeader('<div style="height:'.$pdf_settings->header_height.'px;border:none"></div>');
//     }
    
//     if($pdf_settings->footer == 1){
//         $this->m_pdf->pdf->SetFooter($Footer);
//     }
//     else{
//         $this->m_pdf->pdf->SetFooter('<div style="height:'.$pdf_settings->footer_height.'px;"></div>');
//     }
// }


    $this->m_pdf->pdf->WriteHTML($stylesheet,1);
    $this->m_pdf->pdf->WriteHTML($html,2);
    $this->m_pdf->pdf->Output("./uploads/vital_reports/".$pdfFilePath, "F"); 
    redirect("uploads/vital_reports/".$pdfFilePath);

} 

public function getPatientDetails(){
  //$clinic_id = $_POST['clinic_id'];
  
  $clinic_id =$this->session->userdata('clinic_id');
  $patient_id = $this->input->post('patient_id');
  $profile_id=$this->session->userdata('profile_id');
   $appointment_id=$this->input->post('appointment_id');
  
  $this->db->query("SELECT b.user_entity_name,b.user_entity_alias FROM `profile_permissions` a inner join user_entities b on a.user_entity_id=b.user_entity_id where a.profile_id=12 and b.level_alias='page'")->result();
  
  /*$result = '<div class="col-md-12">
    <div class="form-group ulgroup" ><ul>';
      $result.= "<li class='ligroup' id='PROFILE' onclick=getPatientInfo('PROFILE',".$clinic_id.",".$patient_id[0].",".$patient_id[1].")> PROFILE </li>
       <li class='ligroup' id='VITALS' onclick=getPatientInfo('VITALS',".$clinic_id.",".$patient_id[0].",".$patient_id[1].") >VITALS</li>
      <li class='ligroup' id='CONSENT' onclick=getPatientInfo('CONSENT',".$clinic_id.",".$patient_id[0].",".$patient_id[1].")>CONSENT</li>
      <li class='ligroup' id='DOCUMENTS' onclick=getPatientInfo('DOCUMENTS',".$clinic_id.",".$patient_id[0].",".$patient_id[1].")>DOCUMENTS</li>
      <li class='ligroup' id='SYMPTOMS' onclick=getPatientInfo('SYMPTOMS',".$clinic_id.",".$patient_id[0].",".$patient_id[1].")>SYMPTOMS</li>
      <li class='ligroup' id='HOPI' onclick=getPatientInfo('HOPI',".$clinic_id.",".$patient_id[0].",".$patient_id[1].")>HOPI</li>
      <li class='ligroup' id='PAST' onclick=getPatientInfo('PAST',".$clinic_id.",".$patient_id[0].",".$patient_id[1].")>PAST</li>
      <li class='ligroup' id='PERSONAL' onclick=getPatientInfo('PERSONAL',".$clinic_id.",".$patient_id[0].",".$patient_id[1].")>PERSONAL</li>
      <li class='ligroup' id='TREATMENT' onclick=getPatientInfo('TREATMENT',".$clinic_id.",".$patient_id[0].",".$patient_id[1].")>TREATMENT</li>
      <li class='ligroup' id='FAMILY' onclick=getPatientInfo('FAMILY',".$clinic_id.",".$patient_id[0].",".$patient_id[1].")>FAMILY</li>
      <li class='ligroup' id='SOCIAL' onclick=getPatientInfo('SOCIAL',".$clinic_id.",".$patient_id[0].",".$patient_id[1].")>SOCIAL</li>
      <li class='ligroup' id='GPE' onclick=getPatientInfo('GPE',".$clinic_id.",".$patient_id[0].",".$patient_id[1].")>GPE</li>
      <li class='ligroup' id='SYSTEMIC' onclick=getPatientInfo('SYSTEMIC',".$clinic_id.",".$patient_id[0].",".$patient_id[1].")>SYSTEMIC</li>
      <li class='ligroup'  id='OTHER' onclick=getPatientInfo('OTHER',".$clinic_id.",".$patient_id[0].",".$patient_id[1].")>OTHER</li>
      <li class='ligroup' id='CLINICAL' onclick=getPatientInfo('CLINICAL',".$clinic_id.",".$patient_id[0].",".$patient_id[1].")>CLINICAL</li>
      <li class='ligroup' id='PRESCRIPTION' onclick=getPatientInfo('PRESCRIPTION',".$clinic_id.",".$patient_id[0].",".$patient_id[1].")>PRESCRIPTION</li>
       <li class='ligroup' id='INVESTIGATION' onclick=getPatientInfo('INVESTIGATION',".$clinic_id.",".$patient_id[0].",".$patient_id[1].")>INVESTIGATION</li>
      
      <li class='ligroup' id='FOLLOWUP' onclick=getPatientInfo('FOLLOWUP',".$clinic_id.",".$patient_id[0].",".$patient_id[1].")>FOLLOW UP</li> 
      <li class='ligroup' id='PROCEDURES' onclick=getPatientInfo('PROCEDURES',".$clinic_id.",".$patient_id[0].",".$patient_id[1].")>PROCEDURES</li>
       <li class='ligroup' id='SUMMARY' onclick=getPatientInfo('SUMMARY',".$clinic_id.",".$patient_id[0].",".$patient_id[1].")>SUMMARY VIEW</li>
      ";
      $result.= '
     </ul></div></div>';
  echo $result;*/
  
  if($clinic_id==0)
	{
		$data['profile_pages']=$this->db->query("select a.user_entity_name,a.user_entity_alias from user_entities a inner join profile_permissions b on a.user_entity_id=b.user_entity_id where profile_id=".$profile_id." and a.level_alias='page' and p_read=1 and parent_id!='' ORDER BY position,user_entity_name asc")->result();
		//$data['profile_pages']=$this->db->query("SELECT b.user_entity_name,b.user_entity_alias FROM `profile_permissions` a inner join user_entities b on a.user_entity_id=b.user_entity_id where a.profile_id='".$profile_id."' and b.level_alias='page' and parent_id!='' ")->result();
	}
	else
	{
		$data['profile_pages']=$this->db->query("select c.user_entity_name,c.user_entity_alias from std_uac_entities a inner join user_entities c on a.user_entity_id=c.user_entity_id inner join profile_permissions b on a.user_entity_id=b.user_entity_id where profile_id=".$profile_id." and c.level_alias='page' and p_read=1  and parent_id!='' ORDER BY position,user_entity_name asc")->result();
		//echo $this->db->last_query();
		//$data['profile_pages']=$this->db->query("SELECT b.user_entity_name,b.user_entity_alias FROM `profile_permissions` a inner join user_entities b on a.user_entity_id=b.user_entity_id where a.profile_id='".$profile_id."' and b.level_alias='page' and parent_id!='' ")->result();
	}
  
  //$data['profile_pages']=$this->db->query("SELECT b.user_entity_name,b.user_entity_alias FROM `profile_permissions` a inner join user_entities b on a.user_entity_id=b.user_entity_id where a.profile_id='".$profile_id."' and b.level_alias='page' and parent_id!='' ")->result();
  
   $result = '<div class="col-md-12">
    <div class="form-group ulgroup" ><ul>';
	
	foreach($data['profile_pages'] as $keys=>$values){
      $result.= "<li class='ligroup' id='".$values->user_entity_alias."' onclick=getPatientInfo('".$values->user_entity_alias."',".$clinic_id.",".$patient_id.",".$appointment_id.")>". $values->user_entity_name ."</li>";
	}
      
      $result.= '
     </ul></div></div>';
  echo $result;
   
  
}
public function vitals_add($patient_id='',$appointment_id=''){
	$data['patient_id']=$patient_id;
	$data['appointment_id']=$appointment_id;
   $data['patient_info']= $this->db->query('select * from patient_vital_sign  where patient_id = "'.$patient_id.'" and appointment_id = "'.$appointment_id.'" group by vital_sign')->result();
  $data['vital_sign'] = $this->db->query('select * from vital_sign ')->result();
   //echo count($data['patient_info']);exit;
  // print_r($data['patient_info']);exit;
    $data['view'] = 'vitals/add_vital';
    $this->load->view('layout', $data);
}
// public function vitals_edit()


public function vitals_save(){
// print_r('working on page');exit;
 // print_r($this->input->post());exit;
$clinic_id =$this->session->userdata('clinic_id');
$user_id =$this->session->userdata('user_id');

$inner_count = count($this->input->post('vital_sign'));

			if($inner_count>0)
	 				{
	 					for($i=0;$i<$inner_count;$i++)
	 					{
							
							$patient_umr=$this->db->query("SELECT umr_no from patients where patient_id='".$this->input->post('patient_id')."'")->row();
							$patient_vital_sign['patient_id']=$this->input->post('patient_id');
							$patient_vital_sign['umr_no']=$patient_umr->umr_no;
							$patient_vital_sign['clinic_id']=$clinic_id;
							$patient_vital_sign['appointment_id']=$this->input->post('appointment_id');
							
							if($_POST['vital_sign'][$i]=='SBP'){
								
								$bp_array=explode('/',$_POST['vital_sign_val'][$i]);
								
								for($j=0;$j<count($bp_array);$j++){
									
									if($j==0){
										$vital_sign='SBP';
										
									}else{
									$vital_sign='DBP';	
										
									}
							$patient_vital_sign['vital_sign']=$vital_sign;
							$patient_vital_sign['vital_result']=$bp_array[$j];
							$patient_vital_sign['vital_sign_recording_date_time']=date('Y-m-d H:i:s');
							$patient_vital_sign['created_by']=$user_id;
							$patient_vital_sign['modified_by']=$user_id;
							$patient_vital_sign['created_date_time']=date('Y-m-d H:i:s');
							$patient_vital_sign['modified_date_time']=date('Y-m-d H:i:s');
							$ok = $this->Generic_model->insertData('patient_vital_sign',$patient_vital_sign);
									
								}
								
							}else{
							
							$patient_vital_sign['vital_sign']=$_POST['vital_sign'][$i];
							$patient_vital_sign['vital_result']=$_POST['vital_sign_val'][$i];
							$patient_vital_sign['vital_sign_recording_date_time']=date('Y-m-d H:i:s');
							$patient_vital_sign['created_by']=$user_id;
							$patient_vital_sign['modified_by']=$user_id;
							$patient_vital_sign['created_date_time']=date('Y-m-d H:i:s');
							$patient_vital_sign['modified_date_time']=date('Y-m-d H:i:s');
							$ok = $this->Generic_model->insertData('patient_vital_sign',$patient_vital_sign);
							
							}
						}
					}




    redirect('Appointment');
}





public function getPatientInfo(){
  $name = $_POST['name'];
  $clinic_id = $_POST['clinic_id'];
  $patient_id = $_POST['patient_id'];
 $appointment_id = $_POST['appointment_id'];

   $appointments = $this->db->query("select a.*,d.salutation,d.first_name,d.last_name,d.department_id,de.department_name,de.department_id from appointments a 
   left join doctors d on a.doctor_id = d.doctor_id left join department de on (d.department_id=de.department_id) where a.clinic_id='".$clinic_id."' and a.patient_id ='".$patient_id."'  order by a.appointment_id desc ")->result();

  // echo '<table><tbody><tr><td>Name</td><td>'.$patient_dt->title.'  . '.$patient_dt->first_name.' '.$patient_dt->last_name.'</td></tr></tbody></table>';
  if($name=='Profile'){
      //echo'PROFILE';
    $patient_dt = $this->db->query("select p.*, d.district_name, s.state_name from patients p left join districts d on p.district_id = d.district_id left join states s on p.state_id = s.state_id where p.patient_id=".$patient_id."")->row();
    
	if($patient_dt->referred_by_type=='WOM')
	{
		$referred_by = $patient_dt->referred_by;
	}
	else if($patient_dt->referred_by_type=='Doctor')
	{
		$refdoctor = $this->db->query("select * from referral_doctors where rfd_id=".$patient_dt->referred_by)->row();
		
		$referred_by = $refdoctor->doctor_name;
	}
	else if($patient_dt->referred_by_type=='Online')
	{
		$referred_by = $patient_dt->referred_by;
	}
echo'<table id="doctorlist" class="table table-striped dt-responsive nowrap">
     <thead>  
      <tr>
        <th>PROFILE</th>
        <th style="text-align:right;"><a class="btn btn-primary btn-xs" href='.base_url("patients/patient_update/".$patient_dt->patient_id).'>Edit Profile</a></th>
        <th style="text-align:right;">';
          if($patient_dt->photo!=""){
              echo'<img width="150" src="'.base_url('uploads/patients/'.$patient_dt->photo).'" >';
            }
  echo'</th>
      </tr>       
    </thead>
    <tbody>';  
  echo'
      <tr><td>DOB : </td><td>'.$patient_dt->date_of_birth.'</td></tr>
      <tr><td>AGE : </td><td>'.$patient_dt->age.'</td></tr>
       <tr><td>Gender : </td><td>'.$patient_dt->gender.'</td></tr>
      <tr><td>Mobile : </td><td>'.$patient_dt->mobile.'</td></tr>
      <tr><td>UMR NO : </td><td>'.$patient_dt->umr_no.'</td></tr>
      <tr><td>Address : </td><td>'.$patient_dt->address_line.'</td></tr>
      <tr><td>District : </td><td>'.$patient_dt->district_name.'</td></tr>
      <tr><td>State : </td><td>'.$patient_dt->state_name.'</td></tr>
      <tr><td>Pincode : </td><td>'.$patient_dt->pincode.'</td></tr>
	  <tr><td>Referred By : </td><td>'.$referred_by.'</td></tr>';
    echo'</tbody></table>';
  }


  if($name == "Vitals"){
    
  	$clinic_id = $this->input->post('clinic_id');
  	 $patient_id = $this->input->post('patient_id');
  	 $appointment_id = $this->input->post('appointment_id');
  	 $vital_data = '';

  	 $bse_url=base_url("CaseSheet/vitals_add/".$patient_id.'/'.$appointment_id);
  	 $base_url = base_url("CaseSheet/vitals_edit/".$patient_id.'/'.$appointment_id);
   $vital_data  .= '<div class="row col-md-12" >
  <div class="col-md-4"> <h3>VITALS INFORMATION </h3></div>
  <div class="col-md-8">
  <a href="'.base_url('caseSheet/print_vitals/'.$appointment_id).'" class="btn btn-primary pull-right" style="padding:10px;margin-right:10px">Print</a>

  <button style="padding:10px;margin-right:10px"  id = "vital_edit" " value = "'.$patient_id.'/'.$appointment_id.'" class="btn btn-primary pull-right">Edit</button>
  <button style="padding:10px;margin-left:10px;margin-right:10px"  id = "vital_add" " value = "'.$patient_id.'/'.$appointment_id.'" class="btn btn-primary pull-right">Add</button>
  </div><div class="row col-md-12 text-center" ></div></div>'; 

  	  $result = $this->db->query('select * from patient_vital_sign where patient_id = "'.$patient_id.'" and appointment_id = "'.$appointment_id.'" group by vital_sign_recording_date_time')->result();


  	  
  	  for($j=0;$j<count($result);$j++)
  	  {
  	  	  $vital_result = $this->db->query('select * from patient_vital_sign where patient_id = "'.$patient_id.'" and appointment_id = "'.$appointment_id.'" and vital_sign_recording_date_time = "'.$result[$j]->vital_sign_recording_date_time.'"')->result();

           $vital_data .= '<div class = "card"><div class = "card-body">'.date('d-m-Y H:i',strtotime($vital_result[$j]->vital_sign_recording_date_time)).'<div class = "row">';
  	  	  for($k = 0;$k<count($vital_result);$k++){
                 if($vital_result[$j]->vital_result != ""){

                $test = $this->db->query('select * from vital_sign where short_form = "'.$vital_result[$k]->vital_sign.'"')->row();
  	  	  	 if($vital_result[$k]->vital_sign == "BP"){
                $status_info1 = $this->db->query('select * from vital_sign where short_form ="SBP"')->row();
              $status_info2 = $this->db->query('select * from vital_sign where short_form ="DBP"')->row();
               $bp_arr = explode("/", $vital_result[$k]->vital_result);
               if($bp_arr[0] >= $status_info1->low_range && $bp_arr[0] <= $status_info1->high_range)
              {
                $sbp_color = "black";
              }
              else
              {
                $sbp_color = "red";
              }
              if($bp_arr[1] >= $status_info2->low_range && $bp_arr[1] <= $status_info2->high_range)
              {
                $dbp_color = "black";
              }
              else
              {
                $dbp_color = "red";
              }

               $vital_data .= '<div class = "col-md-4" style="padding:10px;"><h5>'.$vital_result[$k]->vital_sign .'</h5><h3><span style="color:'.$sbp_color.';">' .$bp_arr[0]. '</span>/<span style="color:'.$dbp_color.';">' .$bp_arr[1]. '</span><span style = "font-size:10px;">'.$test->unit.'</span></h3></div>';
              }
              else{
               
                $status_info = $this->db->query("select * from vital_sign where short_form ='".$vital_result[$k]->vital_sign."'")->row();
              //print_r($status_info->low_range);exit;
              
                  if($vital_result[$j]->vital_result >= $status_info->low_range  && $vital_result[$j]->vital_result <= $status_info->high_range)
                  {
                 $color = "black";
             }
             else
             {
                $color = "red";
             }

              $vital_data .= '<div class = "col-md-4" style="padding:10px"><h5>'.$vital_result[$k]->vital_sign .'</h5><h3 style="color:'.$color.';">' .$vital_result[$k]->vital_result. '<span style = "font-size:10px;">'.$test->unit.'</span></h3></div>';
              }
            
  	  	  
  	  	
  	  	

  	  	   	}
  	  	   }
  	  	   

  	  	   	$vital_data .= '</div></div></div>';

  	  	   
  	  }
    echo $vital_data;
  }

  if($name == "Consent"){
	  
	  foreach ($appointments as $key => $value1) {
		  $dpartments[]=$value1->department_id.','.$value1->appointment_id;
	  }
	  
	  $dpartments1=array_filter(array_unique($dpartments));
	  
	  
	  $de = '<div class="col-md-6"><label for="patient_id" class="col-form-label">DEPARTMENT</label>
  <select name="department_id" id="patient_id" class="form-control" onchange="getConsentForms(this.value)" required>
  <option value=""> Select Department </option>';
  for ($d=0;$d<count($dpartments1);$d++) {
	  $dd=explode(",",$dpartments1[$d]);
	  $department=$this->Generic_model->getSingleRecord('department',array('department_id'=>$dd[0]),$order='');
   $de.='<option value="'.$dpartments1[$d].'">'.$department->department_name.'</option>';
  }
  $de.='</select></div><div class="col-md-12" id="conForm">
  </div>';
  echo $de;
}
  if($name == "Dx"){
      echo' <div class="row col-md-12 text-center" ><h3> INVESTIGATION </h3>'; 
if(!empty($appointments)){
foreach ($appointments as $key => $value) {
  

$investigation = $this->db->query("select  d.salutation,d.first_name,d.last_name,i.investigation_code,i.investigation,i.category,i.mrp from patient_investigation pi
      left join investigations i on pi.investigation_id = i.investigation_id
      left join doctors d on pi.doctor_id=d.doctor_id
     where appointment_id='".$value->appointment_id."' and clinic_id='".$clinic_id."' and patient_id='".$patient_id."' ")->result();
 
   // echo $this->db->last_query();
echo'<table id="doctorlist" class="table table-striped dt-responsive nowrap">
     <thead><tr><th colspan="2">'.$value->appointment_date.'</th>
                <th colspan="2">'.$value->appointment_type.'</th>
     </tr>
      <tr>
        <th> Doctor </th>
        <th> Investigation Code </th>
        <th> Investigation </th>
        <th> Category </th>
      </tr>
     </thead>
    <tbody>';  
foreach ($investigation as $key => $res) {
echo'<tr>
      <td>'.$res->salutation.' '.$res->first_name.' '.$res->last_name.'</td>
      <td>'.$res->investigation_code.'</td>
      <td>'.$res->investigation.'</td>
      <td>'.$res->category.'</td>
    </tr>';
}
echo'</tbody></table>';
echo'</div>';
  }
}
}
  if($name == "Reports"){

    $patient_reports = $this->db->query("select pr.*,pi.investigation_id,d.salutation,d.first_name,d.last_name,i.investigation_code,i.investigation,i.category  from patient_report pr
    left join patient_investigation pi on pr.patient_investigation_id = pi.investigation_id
    left join doctors d on pr.doctor_id=d.doctor_id
     left join investigations i on pi.investigation_id = i.investigation_id
    where pr.clinic_id='".$clinic_id."' and pr.patient_id='".$patient_id."' order by pr.appointment_id asc")->result();
    //echo $this->db->last_query();
    if(empty($patient_reports)){
       echo'<div class="col-md-4 form-group"><span style="font-weight:bold; color:red;"> No Records  </span></div>';
    }else{
      foreach ($patient_reports as $key => $value) {
  
 echo'<table id="doctorlist" class="table table-striped dt-responsive nowrap">
     <thead>
    <tr><th colspan="4">DOCUMENTS</th></tr>
        <tr>
            <th>Report type</th>
            <th>Description</th>
            <th>Doctor</th>
            <th>Category</th>
       </tr>
    </thead>
    <tbody>';  
  foreach ($patient_reports as $key => $value) {
  echo'<tr>
      <td>'.$value->report_type.'</td>
      <td>'.$value->description.'</td>
      <td>'.$value->salutation.' . '.$value->first_name.' '.$value->last_name.'</td>
      <td>'.$value->category.'</td>
    </tr>';

  }
   echo'</tbody></table>';
              
      }
    }
  }
  if($name == "Symptoms"){
    
    $symptoms = $this->db->query("select pps.patient_presenting_symptoms_id,ps.symptom_data,
      ps.time_span,ps.span_type
      from patient_presenting_symptoms pps 
      left join patient_ps_line_items ps on pps.patient_presenting_symptoms_id = ps.patient_presenting_symptoms_id where pps.clinic_id='".$clinic_id."' and pps.patient_id ='".$patient_id."' ")->result();
  // echo $this->db->last_query();
 if(count($symptoms)>0){
  echo'<table id="doctorlist" class="table table-striped dt-responsive nowrap">
    <thead>
        <tr>
            <th>Symptom</th>
            <th>Time span</th>
            <th>Span Type</th>
       </tr>
    </thead>
    <tbody>';  
  foreach ($symptoms as $key => $value) {
  echo'<tr>
      <td>'.$value->symptom_data.'</td>
      <td>'.$value->time_span.'</td>
      <td>'.$value->span_type.'</td>
    </tr>';
  }
   echo'</tbody></table>';
}else{
    echo'<div class="col-md-4 form-group"><span style="font-weight:bold; color:red;"> No Records  </span></div>';
}
  
  }
  if($name == "HOPI"){
    echo' <div class="row col-md-12 text-center" ><h3> HOPI </h3></div>'; 
  }

if($name == "Reports"){
    $previous_documents = $this->db->query("select * from previous_documents where patient_id= '".$patient_id."' order by previous_document_id desc")->result();
    //echo $this->db->last_query();
if(count($previous_documents)>0){
     
echo'<table id="doctorlist" class="table table-striped dt-responsive nowrap">
     <thead>  
      <tr><th colspan="5">PAST</th></tr>  
      <tr>
        <th>Report date</th>
        <th>Document type</th>
        <th>Description</th>
        <th>Documents</th>
      </tr>
    </thead>
    <tbody>';  
 foreach ($previous_documents as $key => $value) {
  echo'<tr>
      <td>'.$value->report_date.'</td>
      <td>'.$value->document_type.'</td>
      <td>'.$value->description.'</td>
      <td>'.$value->document_type.'</td>
    </tr>';
echo'<tr><td colspan="4">';
$images=$value->images;
$picture_explode=explode(",",$images);  
 echo'<div class="row col-md-12 text-center" >';
for($k=0;$k<count($picture_explode);$k++){
    echo'<div class="col-md-4 text-left" style="margin:3px;">
      <div class="form-group">
      <img width="150" src="'.base_url('uploads/previous_documents/'. trim($picture_explode[$k])).'" >
    </div></div>';
}
echo'</td></tr>';
}
echo'</tbody></table>';
 }else{
         echo'<div class="col-md-4 form-group"><span style="font-weight:bold; color:red;"> No Records  </span></div>';
    }
  }

  if($name == "Personal"){
    echo' <div class="row col-md-12 text-center" ><h3> PERSONAL </h3></div>'; 
  }

  if($name == "Treatment"){
    echo' <div class="row col-md-12 text-center" ><h3> TREATMENT </h3></div>'; 
  }

  if($name == "Family"){
    echo' <div class="row col-md-12 text-center" ><h3> FAMILY </h3></div>'; 
  }

  if($name == "Social"){
    echo' <div class="row col-md-12 text-center" ><h3> SOCIAL </h3></div>'; 
  }

  if($name == "GPE"){
    echo' <div class="row col-md-12 text-center" ><h3> GPE </h3></div>'; 
  }

  if($name == "SE"){
    echo' <div class="row col-md-12 text-center" ><h3> SYSTEMIC </h3></div>'; 
  }

  if($name == "OS"){
    echo' <div class="row col-md-12 text-center" ><h3> OTHER </h3></div>'; 
  }

if($name == "Diagnosis"){
$clinical = $this->db->query("SELECT cd.*, p.title,p.first_name,p.last_name,p.middle_name,cd.clinical_diagnosis,d.salutation,d.first_name,d.last_name  FROM patient_clinical_diagnosis cd
left join patients p on cd.patient_id = p.patient_id
left join clinics c on cd.clinic_id=c.clinic_id
left join doctors d on cd.doctor_id = d.doctor_id
 WHERE cd.clinic_id='".$clinic_id."' and cd.patient_id='".$patient_id."' ")->result();
   // echo $this->db->last_query();
if(count($clinical)>0){
 echo'<table id="doctorlist" class="table table-striped dt-responsive nowrap">
    <thead><tr><th colspan="5"> CLINICAL DIAGNOSIS </th></tr>  
        <tr>
            <th>Diagnosis/th>
            <th>Doctor</th>
       </tr>
    </thead>
    <tbody>';  
  foreach ($clinical as $key => $value) {
  echo'<tr>
      <td>'.$value->clinical_diagnosis.'</td>
    
       <td>'.$value->salutation.' . '.$value->first_name.' '.$value->last_name.'</td>
    </tr>';

  }
   echo'</tbody></table>';
 }else{
echo'<div class="col-md-4 form-group"><span style="font-weight:bold; color:red;"> No Records  </span></div>';
 }

   // echo' <div class="row col-md-12 text-center" ><h3> CLINICAL </h3></div>'; 
  }
   if($name == "Rx"){
    echo'<table id="doctorlist" class="table table-striped dt-responsive nowrap">
    <thead><tr><th colspan="5">PRESCRIPTION</th></tr>';  
    foreach ($appointments as $key => $value) {
    $patient_prescription= $this->db->query("select pp.patient_id,pp.appointment_id,pd.drug_id, pd.day_schedule, pd.preffered_intake, pd.dose_course, pd.quantity,d.trade_name, d.composition from patient_prescription pp left join patient_prescription_drug pd on pp.patient_prescription_id = pd.patient_prescription_id
left join drug d on d.drug_id=pd.drug_id where pp.appointment_id='".$value->appointment_id."' and pp.clinic_id='".$clinic_id."' and pp.patient_id='".$patient_id."' ")->result();
    //echo $this->db->last_query();
if(count($patient_prescription>0)){
   echo'
   <tr>
      <th colspan="1">'.$value->appointment_date.'</th>
      <th colspan="2">'.$value->appointment_type.'</th>
   </tr>
   <tr>
       <th>Medicine</th>
        <th>Dosage</th>
        <th> Timing - Freq - Duration </th>
   </tr>
</thead>
<tbody>'; 
   
  foreach ($patient_prescription as $key => $r) { 
    $M = 0;
    $N = 0;
    $A = 0;
    $dose = 1;
    $Mday = '';
  if($r->day_schedule=='M'){
    $M = $dose;
    $Mday = $dose.' - '.'Morning'; 
  }else if($r->day_schedule=='N'){
    $N = $dose;
     $Mday = $dose.' - '.'Night';
  }else if($r->day_schedule=='A'){
      $A = $dose;
      $Mday = $dose.' - '.'Afternoon';
  }

//echo $M.' - '.$A.' - '.$N;
  echo'<tr>
      <td>'.$r->trade_name.'</td>
      <td>'. $M.' - '.$A.' - '.$N.'</td>
      <td>'.$r->dose_course.' Day\'s</td>
    </tr>
<tr>
   <td colspan="3" style="width:100%;text-align: left;height: 60px;"><span style="font-style: italic;font-size: 16px; width:25%;text-align: left;">Timing : </span> 
    <span>'.$Mday.'</span>
   </td>
 </tr>   <tr> <td colspan="3" ><span style="font-style: italic;font-size: 16px;width:25%;text-align: left;">Composition : </span> 
              <span>'.$r->composition.'</span>
             </td></tr>';
  }
}else{
   echo'<div class="col-md-4 form-group"><span style="font-weight:bold; color:red;"> No Records  </span></div>';
}
    
    }
       echo'</tbody></table>';
    //echo' <div class="row col-md-12 text-center" ><h3> PRESCRIPTION </h3></div>'; 
  }
if($name == "FOLLOWUP"){
  echo' <div class="row col-md-12 text-center" ><h3> FOLLOW UP  </h3>'; 
if(!empty($appointments)){
   $followup = $this->db->query("select  a.*, d.salutation, d.first_name, d.last_name from appointments a
    left join doctors d on a.doctor_id = d.doctor_id
    where clinic_id='".$clinic_id."' and patient_id ='".$patient_id."' order by appointment_id desc ")->result();
//echo $this->db->last_query();
echo'<table id="doctorlist" class="table table-striped dt-responsive nowrap">
     <thead>
      <tr>
        <th> Doctor </th>
        <th> Appointment Date </th>
        <th> Appointment Type </th>
      </tr>
     </thead>
    <tbody>';  
foreach ($followup as $key => $value) {
echo'<tr>
      <td>'.$value->salutation.' '.$value->first_name.' '.$value->last_name.'</td>
      <td>'.$value->appointment_date.'</td>
      <td>'.$value->appointment_type.'</td>
    </tr>';
}
echo'</tbody></table>';
echo'</div>';
 
}
}

if($name == "FLW-UP"){
 $procedures = $this->db->query("select mp.*,dp.department_name
  from medical_procedures mp left join department dp on mp.department_id = dp.department_id where mp.clinic_id='".$clinic_id."' ")->result();
//echo $this->db->last_query();
  echo'<div class="row col-md-12 text-center" ><h3> PROCEDURES </h3></div>'; 
}

}

public function vital_add()
  {
     $url = base_url('CaseSheet/add');
      $patient_id = $this->input->post('patient_id');
      $appointment_id = $this->input->post('appointment_id');

      $patient_info = $this->Generic_model->getSingleRecord('patients',array('patient_id'=>$patient_id));
      $drug_allergy = $patient_info->allergy;
      /*<table>
      <tr><td>Patient Name : '.$patient_info->first_name.''.$patient_info->last_name.' </td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>UMR NO : '.$patient_info->umr_no.'</td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>Mobile : '.$patient_info->mobile.'</td></tr>
      </table></h3>*/
      echo '<div class="card">	  
      <form action="'.$url.'" method="POST" onsubmit="return validate();" class="form customForm">
        <input type = "hidden" name = "patient_id" value = "'.$patient_id.'"/>
        <input type = "hidden" name = "appointment_id" value = "'.$appointment_id.'"/>
        <div class="row col-md-12">
          <div class="col-md-12"><h3>Add Vitals</h3></div>
          <div class="col-md-9">
            <table id="vital" class="table vitals_info">
              <tr>
                <td><label class="col-form-label">Pulse Rate</label></td>
                <td>:</td>
                <td colspan="3">
                  <input type="number" name="vitals[PR]" id="PR" class ="check form-control" value = "" />
                </td>
                <td><label>Per Min</label></td>
              </tr>
              <tr>
                <td><label class="col-form-label">BP</label></td>
                <td>:</td>
                <td><input type="number" value="" name="vitals[SBP]" id="SBP" class="check form-control" placeholder="Systolic" onkeyup="return bp();" /></td>
                <td> / </td>
                <td><input type="number" name="vitals[DBP]" value="" id="DBP" class="check form-control" placeholder="Diastolic" onkeyup="return bp();"/>
                  <input type = "hidden" id = "BP" style="border-top:0px;border-left:0px;border-right:0px;"/>
                </td>
                <td class="text-left"><label>mm/HG</label></td>
              </tr>
              <tr>
                <td><label class="col-form-label">Respiratory Rate</label></td>
                <td>:</td>
                <td colspan="3"><input type = "number"  name = "vitals[RR]" value = "" id = "RR" class = "check form-control" /></td>
                <td class="text-left"><label>Per Min</label></td>
              </tr>
              <tr>
                <td><label class="col-form-label">Temperature</label></td>
                <td>:</td>
                <td colspan="3"><input type = "number"  name = "vitals[Temp]" value = "" id = "Temp" class = "check form-control" /></td>
                <td class="text-left"><label>°F</label></td>
              </tr>
              <tr>
                <td><label class="col-form-label">SaO2</label></td>
                <td>:</td>
                <td colspan="3"><input type = "number"  name = "vitals[SaO2]" value = "" id = "SaO2" class = "check form-control"/></td>
                <td class="text-left"><label></label></td>
              </tr>
              <tr>
                <td><label class="col-form-label">Height</label></td>
                <td>:</td>
                <td colspan="3"><input type = "number"  name = "vitals[Height]" value = "" id = "Height" class = "check form-control"/></td>
                <td class="text-left"><label>CM</label></td>
              </tr>
              <tr>
                <td><label class="col-form-label">Weight</label></td>
                <td>:</td>
                <td colspan="3"><input type = "number"  name = "vitals[Weight]" value = "" class = "check form-control" id = "Weight" /></td>
                <td class="text-left"><label>KG</label></td>
              </tr>
              <tr>
                <td><label class="col-form-label">BMI</label></td>
                <td>:</td>
                <td colspan="3"><input type = "number"  name = "vitals[BMI]" value = "" id = "BMI" class = "check form-control" onclick = "myFunction(this.id)" /></td>
                <td class="text-left"><label>kg/m2</label></td>
              </tr>
              <tr>
                <td><label class="col-form-label">BSA</label></td>
                <td>:</td>
                <td colspan="3"><input type = "number"  name = "vitals[BSA]" readonly value = "" id = "bsa" class = "check form-control" onclick="myFunction(this.id)" /></td>
                <td class="text-left"><label>kg/m2</label></td>
              </tr>
              <tr>
                <td><label class="col-form-label">WH Ratio</label></td>
                <td>:</td>
                <td colspan="3"><input type = "number"  name = "vitals[WH_ratio]" value = "" id = "vhr" class = "check form-control" /></td>
                <td class="text-left"><label></label></td>
              </tr>
              <tr>
                <td><input type = "text" class = "form-control" name = "vital_sign[]"></td>
                <td>:</td>
                <td colspan="3"><input type = "number" class = "form-control" name = "vital_sign_val[]" /></td>
                <td style="text-align: center;"><button type="button" id="test1" onclick="add_vital()" class="btn btn-success">+</button></td>
              </tr>
            </table>
          </div>
          <div class="col-md-3">&nbsp;</div>
        </div>'  
        ;

        if($drug_allergy != '') {
          echo '<div class="col-md-12" style="padding-top:20px;"><strong>Drug Allergy</strong><br><p class="dg">'.trim($drug_allergy,",").'</p><input type="hidden" name="drug_allergy" value="'.trim($drug_allergy,",").'" ></div>
		  <div class="col-md-12" style="padding-top:20px;">
            <strong>More Drug Allergies<span class="error" style="color:red">*</span>
              <div class="form-group">
                <div class="row" id="check_ptype">
                  <div class="radio radio-success">
                    <input type="radio" class="radio-ma" id="radio14" value="yes" required="required">
                    <label for="radio14" > Yes </label>
                  </div>
                  <div class="radio radio-success">
                    <input type="radio" class="radio-ma"  id="radio15" value="no">
                    <label for="radio15" > No </label>
                  </div>
                </div>
              </div>
            </strong>
          </div>		  '; 
        }else{
          echo '<div class="col-md-12" style="padding-top:20px;">
            <strong>Drug Allergy<span class="error" style="color:red">*</span>
              <div class="form-group">
                <div class="row" id="check_ptype">
                  <div class="radio radio-success">
                    <input type="radio" class="radio-ip" id="radio12" value="yes" required="required">
                    <label for="radio12" > Yes </label>
                  </div>
                  <div class="radio radio-success">
                    <input type="radio" class="radio-ip"  id="radio13" value="no">
                    <label for="radio13" > No </label>
                  </div>
                </div>
              </div>
            </strong>
          </div>';
        }        

        echo '<div class="col-md-12" > 
           <input type = "text" id="input-allergy" class = "form-control" name = "allergy" style="display:none;margin-top:10px;border-top:0px;border-left:0px;border-right:0px;width: 300px;"/>
           </div>
           <div class="col-md-12">
           <textarea class="form-control input-ma" rows="5" name = "more_allergies" id="input-ma" style="display:none;margin-top: 0px; margin-bottom: 0px; height: 89px;"" ></textarea>
           </div>
		  
        <input type = "submit" name="save_vitals" class = "btn btn-success" value = "Submit" style="margin-top:20px;margin-right:50px;"/>
         <input type = "submit" name="print" class = "btn btn-success" value = "Submit & Print" style="margin-top:20px;margin-right:50px;"/>
       </form></div>';
	   
	   
  }


/*
  public function vital_add()
  {
     $url = base_url('CaseSheet/add');
      $patient_id = $this->input->post('patient_id');
      $appointment_id = $this->input->post('appointment_id');

      $patient_info = $this->Generic_model->getSingleRecord('patients',array('patient_id'=>$patient_id));
      $drug_allergy = $patient_info['allergy'];

      echo '<div class="card"><div class="card-body"><h3>Add Vitals</h3>
       <form action="'.$url.'" method="POST">
       <input type = "hidden" name = "patient_id" value = "'.$patient_id.'"/>
       <input type = "hidden" name = "appointment_id" value = "'.$appointment_id.'"/>
          <table style="width:100%;" id="vital">
              <tr>
               <td><b>Pulse Rate</b></td>
            <td><input type = "text" name = "vitals[PR]" id = "PR"  class = "check col-md-12" value = ""  style="border-top:0px;border-left:0px;border-right:0px;"/></td>
              <td style="font-size: 12px;">Per Min</td>
             </tr>
             <tr>
               <td><b>BP</b></td>
            <td><input type = "text" name = "SBP" value = "" id = "SBP"  class = "check" placeholder ="SBP" style="border-top:0px;border-left:0px;border-right:0px;"/></td><td><input type = "text" name = "SBP1"  value = "" id = "DBP" class = "check" placeholder = "DBP" style="border-top:0px;border-left:0px;border-right:0px;"/>
            <input type = "hidden"  name = "vitals[BP]" value = "" id = "BP"  placeholder = "DBP" style="border-top:0px;border-left:0px;border-right:0px;"/>
            </td>
            
              <td style="font-size: 12px;">mm/HG</td>
             </tr>
             <tr>
               <td><b>Respiratory Rate</b></td>
            <td><input type = "text"  name = "vitals[RR]" value = "" id = "RR" class = "check" style="border-top:0px;border-left:0px;border-right:0px;"/></td>
              <td style="font-size: 12px;">Per Min</td>
             </tr>
             <tr>
               <td><b>Temperature</b></td>
            <td><input type = "text"  name = "vitals[Temp]" value = "" id = "Temp" class = "check"  style="border-top:0px;border-left:0px;border-right:0px;"/></td>
              <td style="font-size: 12px;">°F</td>
             </tr>

             <tr>
               <td><b>SaO2</b></td>
            <td><input type = "text"  name = "vitals[SaO2]" value = "" id = "SaO2" class = "check" style="border-top:0px;border-left:0px;border-right:0px;"/></td>
              <td style="font-size: 12px;"></td>
             </tr>

             <tr>
               <td><b>Height</b></td>
            <td><input type = "text"  name = "vitals[Height]" value = "" id = "Height" class = "check" style="border-top:0px;border-left:0px;border-right:0px;"/></td>
              <td style="font-size: 12px;">CM</td>
             </tr>

             <tr>
               <td><b>Weight</b></td>
            <td><input type = "text"  name = "vitals[Weight]" value = "" class = "check" id = "Weight"  style="border-top:0px;border-left:0px;border-right:0px;"/></td>
              
              <td style="font-size: 12px;">KG</td>
             </tr>

             <tr>
               <td><b>BMI</b></td>
            <td><input type = "text"  name = "vitals[BMI]" value = "" id = "BMI" class = "check" onclick = "myFunction(this.id)" style="border-top:0px;border-left:0px;border-right:0px;"/></td>
              
              <td style="font-size: 12px;">kg/m2</td>
             </tr>

             <tr>
               <td><b>BSA</b></td>
            <td><input type = "text"  name = "vitals[bsa]" readonly value = "" id = "bsa" class = "check" onclick = "myFunction(this.id)"style="border-top:0px;border-left:0px;border-right:0px;"/></td>
              <td style="font-size: 12px;">kg/m2</td>
             </tr>

             <tr>
               <td><b>WH Ratio</b></td>
            <td><input type = "text"  name = "vitals[wh_ratio]" value = "" id = "vhr" class = "check" style="border-top:0px;border-left:0px;border-right:0px;"/></td>
              <td style="font-size: 12px;"></td>
             </tr>
      
        <tr>
            <td><input type = "text" class = "form-control" name = "vital_sign[]" style="border-top:0px;border-left:0px;border-right:0px;width: 200px;"></td>
            <td><input type = "text" class = "form-control" name = "vital_sign_val[]" style="border-top:0px;border-left:0px;border-right:0px;"/></td>
            <td style="text-align: center;"><button type="button" id="test1" onclick="add_vital()" class="btn btn-success">+</button></td>
          </tr>

        </table>

        <div class="col-md-12" style="padding-top:20px;">
          <strong>Drug Allergy<span class="error" style="color:red">*</span>
            <div class="form-group">
              <div class="row" id="check_ptype">
                <div class="radio radio-success">
                  <input type="radio" class="radio-ip" id="radio12" value="yes" required="required">
                  <label for="radio12" > Yes </label>
                </div>
                <div class="radio radio-success">
                  <input type="radio" class="radio-ip"  id="radio13" value="no">
                  <label for="radio13" > No </label>
                </div>
              </div>
            </div>
          </strong>
        </div>

        <div class="col-md-12"> 
           <input type = "text" id="input-allergy" class = "form-control" name = "allergy" style="display:none;margin-top:10px;border-top:0px;border-left:0px;border-right:0px;width: 300px;"/>
           </div>
        <input type = "submit" name="save_vitals" class = "btn btn-success" value = "Submit" style="margin-top:20px;margin-right:50px;"/>
         <input type = "submit" name="print" class = "btn btn-success" value = "Submit & Print" style="margin-top:20px;margin-right:50px;"/>
       </form>
    </div></div>';
  }
*/
  public function check()
  {
     $vital_sign = $this->input->post('id');
     
  }


  // below funciton is used to save the patient vitals
  public function add()
  {

   // echo '<pre>';
   // print_r($this->input->post());
   // exit();
    // echo '<pre>';
     $get_vitals = $this->input->post('vitals');
    // print_r($this->input->post('vitals'));
    
    // if($get_vitals['BP'] != ''){
    //   $BP = explode("/",$get_vitals['BP']);
    //   $get_vitals['SBP'] = $BP[0];
    //   $get_vitals['DBP'] = $BP[1];
    //   unset($get_vitals['BP']);
    // }
    //echo "<pre>test";print_r($_POST);exit;
    $appointment_info = $this->db->query('select * from appointments where appointment_id ='.$this->input->post('appointment_id').' AND patient_id='.$this->input->post('patient_id'))->row();
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
    if($get_more_allergies != "No"){
      $more_allergies = implode(",",$get_more_allergies);
    }
    else{
      $more_allergies = $more_allergies;
    }
	
    if($this->input->post("allergy") != "No"){
      $pa['allergy'] = $this->input->post('drug_allergy').', '.$this->input->post("allergy");
      $this->Generic_model->updateData("patients",$pa,array('patient_id'=>$appointment_info->patient_id));  
    }else{     
      $pa['allergy'] = $this->input->post('drug_allergy');      
      $this->Generic_model->updateData("patients",$pa,array('patient_id'=>$appointment_info->patient_id));
    }
	
	if($more_allergies != "" || $more_allergies != NULL || $more_allergies != "No")
	{
		
		$af = $this->db->query('select * from patients where patient_id='.$appointment_info->patient_id)->row();
		if($af->allergy == "" || $af->allergy =='No')
		{
			$pma['allergy'] = $more_allergies;
		}else{
			$pma['allergy'] = $af->allergy.",".$more_allergies;
			
		}
		$this->Generic_model->updateData("patients",$pma,array('patient_id'=>$appointment_info->patient_id));
	}
  else{
      $pma['allergy'] = "No";
    $this->Generic_model->updateData("patients",$pma,array('patient_id'=>$appointment_info->patient_id));
  }
    
	
	$this->Generic_model->pushNotifications($ap_details->patient_id,$appointment_id,$ap_details->doctor_id,$ap_details->clinic_id,'push_to_consultant','patient_21_details_tab');
	
	
    if($this->input->post("print")){
      $this->print_vitals($this->input->post('appointment_id'));
    }else{
      redirect('caseSheet/patient_info/'.$appointment_info->patient_id.'/'.$appointment_info->appointment_id);
    }
  }


  public function vital_edit()
  {

     $url = base_url('CaseSheet/edit');
     $patient_id = $this->input->post('patient_id');
      $appointment_id = $this->input->post('appointment_id');
       $result = $this->db->query('select * from patient_vital_sign where patient_id = "'.$patient_id.'" and appointment_id = "'.$appointment_id.'"  and  vital_sign_recording_date_time = (select max(vital_sign_recording_date_time) From patient_vital_sign) order by  vital_sign_recording_date_time desc,position asc')->result();
       
      echo '<div class="card"><div class="card-body"><h3>Edit Vitals</h3>
       <form action="'.$url.'" method="POST">
       <input type = "hidden" name = "patient_id" value = "'.$patient_id.'"/>
       <input type = "hidden" name = "appointment_id" value = "'.$appointment_id.'"/>
       <input type = "hidden" name = "vital_sign_date_time" value = "'.$result[0]->vital_sign_recording_date_time.'"/>
          <table style="width:100%;" id="vital">';
          for($j=0;$j<count($result);$j++){
          	 $vital = $result[$j]->vital_result;
          	  $name = $this->db->query('select * from vital_sign where short_form = "'.$result[$j]->vital_sign.'"')->row();

          	  $element_name = $result[$j]->vital_sign;

              if($element_name == "BP"){
                $status_info1 = $this->db->query('select * from vital_sign where short_form ="SBP"')->row();
              $status_info2 = $this->db->query('select * from vital_sign where short_form ="DBP"')->row();
               $bp_arr = explode("/", $vital);
               if($bp_arr[0] >= $status_info1->low_range && $bp_arr[0] <= $status_info1->high_range)
              {
                $sbp_color = "black";
              }
              else
              {
                $sbp_color = "red";
              }
              if($bp_arr[1] >= $status_info2->low_range && $bp_arr[1] <= $status_info2->high_range)
              {
                $dbp_color = "black";
              }
              else
              {
                $dbp_color = "red";
              }

            echo '<tr><td><b>'.$element_name.':</b></td><td><input type = "text" name = "SBP" id = "SBP" value = "'.$bp_arr[0].'" class = "check" style="border-top:0px;border-left:0px;border-right:0px; color:'.$sbp_color.';"/></td>
            <td><input type = "text" name = "DBP" id = "DBP" value = "'.$bp_arr[1].'" class = "check" style="border-top:0px;border-left:0px;border-right:0px; color:'.$$dbp_color.';"/></td>
              <td style="font-size: 12px;">'.$name->unit.'</td>
              <input type = "hidden" name = "vitals[BP]" value = "'.$bp_arr[0].'/'.$bp_arr[1].'" id = "BP"  placeholder = "DBP" style="border-top:0px;border-left:0px;border-right:0px;"/>
             </tr>';
              }
              else{
                if($element_name == "bsa"){
                  $ro = "readonly";
                }
                else{
                  $ro = "";
                }
                $status_info = $this->db->query("select * from vital_sign where short_form ='".$element_name."'")->row();
              //print_r($status_info->low_range);exit;
              
                  if($vital >= $status_info->low_range  && $vital <= $status_info->high_range)
                  {
                 $color = "black";
             }
             else
             {
                $color = "red";
             }

            echo '<tr><td><b>'.$element_name.':</b></td><td><input type = "text" name = "vitals['.$element_name.']" id = "'.$element_name.'" value = "'.$vital.'" '.$ro.' class = "check" style="border-top:0px;border-left:0px;border-right:0px; color:'.$color.';"/></td>
              <td style="font-size: 12px;">'.$name->unit.'</td>
             </tr>';
              }
  	  	  	
         

         
         }
             
        echo '<tr>
            <td><input type = "text" class = "form-control" name = "vital_sign[]" style="border-top:0px;border-left:0px;border-right:0px;width: 200px;"></td>
            <td><input type = "text" class = "form-control" name = "vital_sign_val[]" style="border-top:0px;border-left:0px;border-right:0px;"/></td>
            <td style="text-align: center;"><button type="button" id="test1" onclick="add_vital();" class="btn btn-success">+</button></td>
          </tr>

        </table>
        <input type = "submit" class = "btn btn-success" value = "Submit" style="margin-top:20px;margin-right:50px;"/>
       </form>
    </div></div>';

  }


  public function edit()
  {
    /*echo "<pre>";print_R($this->input->post());
    exit;*/
       $get_vitals = $this->input->post('vitals');
  //print_r($this->input->post('vital_sign'));exit;
     $appointment_info = $this->db->query('select * from appointments where appointment_id ='.$this->input->post('appointment_id'))->row();

     $j=0;
     foreach ($get_vitals as $name => $result) {
       $data['patient_id'] = $appointment_info->patient_id;
       $data['umr_no'] = $appointment_info->umr_no;
       $data['clinic_id'] = $appointment_info->clinic_id;
       $data['appointment_id'] = $appointment_info->appointment_id;
       $data['vital_sign'] = $name;
       $data['vital_result'] = $result;
       $data['position'] = $j;
       $data['vital_sign_recording_date_time'] = date('Y-m-d H:i:s');
       $data['status'] = 0;
       $data['created_by'] = $this->session->userdata('user_id');
       $data['modified_by'] = $this->session->userdata('user_id');
       $data['created_date_time'] = date('Y-m-d H:i:s');
       $data['modified_date_time'] = date('Y-m-d H:i:s');
       if($name == "SBP"){}else{$j++;}
   
       $res=$this->db->query("SELECT count(*) as num_rows from patient_vital_sign where patient_id ='".$appointment_info->patient_id."'  and vital_sign_recording_date_time ='".$this->input->post('vital_sign_date_time')."'")->row();
      
    
      if($res->num_rows >0){
      $this->db->query("DELETE FROM patient_vital_sign where patient_id ='".$appointment_info->patient_id."'  and vital_sign_recording_date_time ='".$this->input->post('vital_sign_date_time')."'");
      $this->Generic_model->insertData('patient_vital_sign',$data);
      }else{
        
        $this->Generic_model->insertData('patient_vital_sign',$data);
      }
      //echo "<pre>";print_R($data);
     }
    // exit;
    

    redirect('caseSheet/patient_info/'.$appointment_info->patient_id.'/'.$appointment_info->appointment_id);

  }

  public function change()
  {
  	   $value = $this->input->post('value');
  	   $id = $this->input->post('id');
  	    $check = $this->db->query('select * from vital_sign where short_form = "'.$id.'"')->row();


  	    if($value >= $check->low_range  && $value <= $check->high_range)
                  {
                 echo "normal";
             }
             else
             {
                echo "abnormal";
             }
  	   
  }




}

