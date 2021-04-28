<?php
error_reporting(0);
// defined('BASEPATH') OR exit('No direct script access allowed');

class PdfView extends CI_Controller {
//  public function __construct(){      
//     // parent::__construct();     
//  }

 //  Billing for Registration, Consultation, Procedure
  public function Invoice($id){
    //   $clinic_id = $this->session->userdata("clinic_id");
      $data['billingInfo'] = $this->db->select("*")->from("billing")->where("billing_id",$id)->get()->row();
      $data['billingLineItemsInfo'] = $this->db->select("*")->from("billing_line_items")->where("billing_id",$id)->get()->result();
      $data['patientInfo'] = $this->db->select("*")->from("patients")->where("patient_id",$data['billingInfo']->patient_id)->get()->row();
      $clinic_id = $data['billingInfo']->clinic_id;
      if($data['billingInfo']->doctor_id != 0)
      {
         $data['docInfo'] = $this->db->query("select d.*,de.department_name from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$data['billingInfo']->doctor_id."'")->row();
         $data['clinicDocInfo'] = $this->db->select("*")->from("clinic_doctor")->where("clinic_id",$clinic_id)->where("doctor_id",$data['billingInfo']->doctor_id)->get()->row();
      }
      $data['clinicInfo'] = $this->db->select("*")->from("clinics")->where("clinic_id",$clinic_id)->get()->row();
      $data['view'] = "pdfViews/Invoice";
      $this->load->view('pdfViews/pdfLayout',$data);
  } 

  //  Billing for Registration, Consultation, Procedure
   public function LabInvoice($id){
    //    $clinic_id = $this->session->userdata("clinic_id");
       $data['billingInvoiceInfo'] = $this->db->select("*")->from("billing_invoice")->where("billing_invoice_id",$id)->get()->row();
       $data['billingInfo'] = $this->db->select("*")->from("billing")->where("billing_id",$data['billingInvoiceInfo']->billing_id)->get()->row();
       $data['billingLineItemsInfo'] = $this->db->select("*")->from("billing_line_items")->where("billing_id",$data['billingInvoiceInfo']->billing_id)->get()->result();
       $data['patientInfo'] = $this->db->select("*")->from("patients")->where("patient_id",$data['billingInfo']->patient_id)->get()->row();
       $clinic_id = $data['billingInfo']->clinic_id;
       if($data['billingInfo']->doctor_id != 0)
       {
          $data['docInfo'] = $this->db->query("select d.*,de.department_name from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$data['billingInfo']->doctor_id."'")->row();
          $data['clinicDocInfo'] = $this->db->select("*")->from("clinic_doctor")->where("clinic_id",$clinic_id)->where("doctor_id",$data['billingInfo']->doctor_id)->get()->row();
       }
       $data['clinicInfo'] = $this->db->select("*")->from("clinics")->where("clinic_id",$clinic_id)->get()->row();
       $data['view'] = "pdfViews/LabInvoice";
       $this->load->view('pdfViews/pdfLayout',$data);
   } 

//  Pharmacy Prescription
public function Prescription($id)
{
//    $clinic_id = $this->session->userdata("clinic_id");
   $data['prescriptionInfo'] = $this->db->select("*")->from("patient_prescription")->where("patient_prescription_id",$id)->get()->row();
   $data['prescriptionLineItemsInfo'] = $this->db->select("*")->from("patient_prescription_drug")->where("patient_prescription_id",$id)->get()->result();
   $data['patientInfo'] = $this->db->select("*")->from("patients")->where("patient_id",$data['prescriptionInfo']->patient_id)->get()->row();
   $data['docInfo'] = $this->db->query("select d.*,de.department_name from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$data['prescriptionInfo']->doctor_id."'")->row();
   $clinic_id = $data['prescriptionInfo']->clinic_id;
   $data['clinicDocInfo'] = $this->db->select("*")->from("clinic_doctor")->where("clinic_id",$clinic_id)->where("doctor_id",$data['prescriptionInfo']->doctor_id)->get()->row();
   $data['clinicInfo'] = $this->db->select("*")->from("clinics")->where("clinic_id",$clinic_id)->get()->row();
   $data['view'] = "pdfViews/Prescription";
   $this->load->view('pdfViews/pdfLayout',$data);
}

//Vitals
public function Vitals($appId){
//    $clinic_id = $this->session->userdata("clinic_id");
   $data['appInfo'] = $this->db->select("*")->from("appointments")->where("appointment_id",$appId)->get()->row();
   $data['vital_sign'] = $this->db->query("select * from patient_vital_sign where appointment_id='".$data['appInfo']->appointment_id."' order by patient_vital_id")->result();
   $data['patientInfo'] = $this->db->select("*")->from("patients")->where("patient_id",$data['appInfo']->patient_id)->get()->row();
   $data['docInfo'] = $this->db->query("select d.*,de.department_name from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$data['appInfo']->doctor_id."'")->row();
   $clinic_id = $data['appInfo']->clinic_id;
   $data['clinicDocInfo'] = $this->db->select("*")->from("clinic_doctor")->where("clinic_id",$clinic_id)->where("doctor_id",$data['appInfo']->doctor_id)->get()->row();
   $data['clinicInfo'] = $this->db->select("*")->from("clinics")->where("clinic_id",$clinic_id)->get()->row();
   $data['view'] = "pdfViews/Vitals";
   $this->load->view('pdfViews/pdfLayout',$data);
}

// Pharmacy Billing
public function PharmacyBilling($id){
//    $clinic_id = $this->session->userdata('clinic_id');
   $data['billingInfo'] = $this->db->select("*")->from("billing")->where("billing_id",$id)->get()->row();
   $data['billingLineItemsInfo'] = $this->db->select("*")->from("billing_line_items")->where("billing_id",$id)->get()->result();
   $data['patientInfo'] = $this->db->select("*")->from("patients")->where("patient_id",$data['billingInfo']->patient_id)->get()->row();
   $clinic_id = $data['billingInfo']->clinic_id;
   if($data['billingInfo']->doctor_id != 0)
   {
      $data['docInfo'] = $this->db->query("select d.*,de.department_name from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$data['billingInfo']->doctor_id."'")->row();
      $data['clinicDocInfo'] = $this->db->select("*")->from("clinic_doctor")->where("clinic_id",$clinic_id)->where("doctor_id",$data['billingInfo']->doctor_id)->get()->row();
   }
   $data['clinicInfo'] = $this->db->select("*")->from("clinics")->where("clinic_id",$clinic_id)->get()->row();
   $data['view'] = "pdfViews/PharmacyBilling";
   $this->load->view('pdfViews/pdfLayout',$data);
}

// lab reports
public function labReport($billing_id, $investigation_id){
		$clinic_id = $this->session->userdata('clinic_id');
        $user_id = $this->session->userdata('user_id');
        $data['digitalSignature'] = $this->db->select("*")->from("employees")
        ->where("clinic_id",$clinic_id)
        ->where("employee_id",$user_id)
        ->get()->row();

        $data['billingInvoiceInfo'] = $this->db->select('*')
		->from('billing_invoice')
		->where('billing_id =',$billing_id)->get()->row();
        // echo '<pre>';
		// print_r($user_id);
		// echo '</pre>';
		// exit();
		// Get Patient/Guest Information of the order/billing

		//$data['billing_info'] = $this->db->select('B.billing_id, B.appointment_id, B.patient_id, B.doctor_id, B.guest_name, B.guest_mobile, B.position_status, A.appointment_date, A.appointment_time_slot, Doc.first_name as doc_first_name, Doc.last_name as doc_last_name, Doc.department_id, Dep.department_name')->from('billing B')->join('appointments A','B.appointment_id = A.appointment_id','left')->join('doctors Doc','B.doctor_id = Doc.doctor_id','left')->join('department Dep','Doc.department_id = Dep.department_id','left')->where('B.billing_id =', $billing_id)->where('B.clinic_id =',$clinic_id)->get()->row();
		$data['billingInfo'] = $this->db->select("*")->from("billing")->where("billing_id",$billing_id)->get()->row();
		// Get Template type
		$data['template_type'] = $this->Generic_model->getFieldValue('lab_templates','template_type',array('investigation_id'=>$investigation_id));
		
		// Get the lab report id 
		$data['patient_lab_report_id'] = $this->Generic_model->getFieldValue('patient_lab_reports','patient_lab_report_id',array('billing_id' => $billing_id, 'investigation_id' => $investigation_id,'clinic_id'=>$clinic_id));

		 $data['clinicInfo'] = $this->db->select("*")->from("clinics")->where("clinic_id",$clinic_id)->get()->row();

		$patient_labreport=$data['patient_lab_report_id'];
		// Get the medical test name
		$data['test_name'] = $this->Generic_model->getFieldValue('investigations','investigation',array('investigation_id' => $investigation_id));
		// echo '<pre>';
		// print_r($patient_labreport);
		// echo '</pre>';
		// Get the investigation results w.r.to investigation id
		$lab_results = $this->db->select('PLR.consultant_remark, LI.patient_lab_reports_line_item_id,
		 LI.investigation_id, LI.template_type, LI.value, LI.remarks, I.investigation, I.item_code')
		->from('patient_lab_report_line_items LI')
		// ->join('clinic_investigations CI','LI.investigation_id = CI.investigation_id','inner') 	//hide
		// ->join('clinic_investigation_range CIR','CI.investigation_id = CIR.investigation_id','inner')	//hide
		->join('investigations I','LI.investigation_id = I.investigation_id','inner')
		->join('patient_lab_reports PLR','LI.patient_lab_report_id = PLR.patient_lab_report_id')
		->where('LI.parent_investigation_id =', $investigation_id)
		// ->where('CI.clinic_id =', $clinic_id) 	//hide
		->where('LI.patient_lab_report_id =', $patient_labreport)
		// ->where('CIR.primary_rec =',1)			//hide
		->order_by('LI.position','ASC')
		->get()->result_array();

	// 	$lab_results = $this->db->select('PLR.consultant_remark, LI.patient_lab_reports_line_item_id,
	// 	LI.investigation_id, LI.template_type, LI.value, LI.remarks, CI.clinic_investigation_id, 
	// 	CI.clinic_id, CIR.low_range, CIR.high_range, CIR.units, CIR.method,
	// 	 CI.other_information, I.investigation, I.item_code')
	//    ->from('patient_lab_report_line_items LI')
	//    ->join('clinic_investigations CI','LI.investigation_id = CI.investigation_id','inner')
	//    ->join('clinic_investigation_range CIR','CI.investigation_id = CIR.investigation_id','inner')
	//    ->join('investigations I','LI.investigation_id = I.investigation_id','inner')
	//    ->join('patient_lab_reports PLR','LI.patient_lab_report_id = PLR.patient_lab_report_id')
	//    ->where('LI.parent_investigation_id =', $investigation_id)
	//    ->where('CI.clinic_id =', $clinic_id)
	//    ->where('LI.patient_lab_report_id =', $patient_labreport)
	//    ->where('CIR.primary_rec =',1)
	//    ->order_by('LI.position','ASC')
	//    ->get()->result_array();

		// echo '<pre>';
		// print_r($lab_results);
		// echo '</pre>';
		// exit();/zzz

		foreach($lab_results as $result){
			
			$result['condition'] = $this->db->select('condition, low_range, high_range, units')->from('clinic_investigation_range')->where('investigation_id =',$result['investigation_id'])->order_by('condition_position','ASC')->get()->result_array();

			$data['lab_results'][] = $result;
		}

		$data['investigation_id'] = $investigation_id;
		$data['billing_id'] = $billing_id;

		// echo '<pre>';
		// print_r($clinic_id);
		// echo '</pre>';
		// exit();
		$data['clinic_id']=$clinic_id;    $data['view'] = 'pdfViews/view_lab_report';
    $this->load->view('pdfViews/pdfLayout',$data);
 }

 public function eo_webview($expert_opinion_id){
    $data['header'] = 0;
    $data['expInfo'] = $expInfo = $this->db->query("select * from expert_opinion where expert_opinion_id='".$expert_opinion_id."'")->row();

    $data['cdInfo'] = $this->db->query("select * from patient_clinical_diagnosis pcd,patient_cd_line_items pcdl where pcd.patient_clinical_diagnosis_id=pcdl.patient_clinical_diagnosis_id and pcdl.expert_opinion_id='".$expert_opinion_id."' and pcdl.created_by='".$expInfo->referred_doctor_id."'")->result();
    $data['invInfo'] = $this->db->query("select * from patient_investigation pin,patient_investigation_line_items pil where pin.patient_investigation_id=pil.patient_investigation_id and pil.expert_opinion_id='".$expert_opinion_id."' and pil.created_by='".$expInfo->referred_doctor_id."'")->result();
    $data['presInfo'] = $this->db->query("select * from patient_prescription pp,patient_prescription_drug ppd where pp.patient_prescription_id=ppd.patient_prescription_id and ppd.expert_opinion_id='".$expert_opinion_id."' and ppd.created_by='".$expInfo->referred_doctor_id."'")->result();
    // echo $this->db->last_query();
    $data['view'] = "pdfViews/EOWebview";
    $this->load->view('pdfViews/pdfLayout',$data);
 }


// Short Summary
public function shortSummary($id){
   $appointment_id = $id;

    //HandWriting
    $section_image = $this->db->query("select patient_form_id from patient_form where appointment_id='".$appointment_id."'")->result();

    $i=0;
    foreach($section_image as $image)
    {
        $abc['investigation'][$i]['patient_form_id'] = $image->patient_form_id;
        $inv_lineitems = $this->db->select("*")->from("patient_form_scribbling_images pil")
        ->where("pil.patient_form_id='".$image->patient_form_id."'")
        ->get()
        ->result();
        $j=0;
        foreach($inv_lineitems as $inv_lineitem){
        $abcd['scribbling'][0]['images']= $inv_lineitem->scribbling_image;
        $j++;
        }
        $i++;
    }
    //End HandWriting 

        //$data['visit']=$visit;
    $data['appointments'] = $this->db->select("a.*, c.clinic_name,c.clinic_logo,c.address,c.clinic_phone,p.title,p.first_name as pname,p.last_name as plname,p.gender,p.age,p.address_line,p.umr_no,p.preferred_language,p.mobile,p.qrcode,p.allergy,d.salutation,d.first_name as dfname,d.last_name as dlname, d.qualification, d.registration_code, dep.department_name")
    ->from("appointments a")->join("clinics c","a.clinic_id = c.clinic_id","left")->join("patients p","a.patient_id = p.patient_id","left")->join("doctors d","a.doctor_id = d.doctor_id","left")->join('department dep','d.department_id = dep.department_id')->where("a.appointment_id='" . $appointment_id . "'")->get()->row();
    $clinic_id = $data['appointments']->clinic_id;
    $data['patientInfo'] = $this->db->select("*")->from("patients")->where("patient_id",$data['appointments']->patient_id)->get()->row();
    if($data['appointments']->doctor_id != 0)
    {
       $data['docInfo'] = $this->db->query("select d.*,de.department_name from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$data['appointments']->doctor_id."'")->row();
       $data['clinicDocInfo'] = $this->db->select("*")->from("clinic_doctor")->where("clinic_id",$clinic_id)->where("doctor_id",$data['appointments']->doctor_id)->get()->row();
    }
    $data['clinicInfo'] = $this->db->select("*")->from("clinics")->where("clinic_id",$clinic_id)->get()->row();


    $patient_name = $data['appointments']->pname . date('Ymd').$appointment_id;

    $visit_no = $this->Generic_model->getAllRecords('appointments',array('clinic_id'=>$data['appointments']->clinic_id,'patient_id'=>$data['appointments']->patient_id,'doctor_id'=>$data['appointments']->doctor_id),array('field'=>'appointment_id','type'=>'desc'));


    $visit_count = count($visit_no);

    foreach ($visit_no as $key => $value) {
        if ($value->appointment_id == $appointment_id) {
            $visit_count--;
            $data['visit'] = $visit_count;
        }
    }

    //$vital_sign =  $this->Generic_model->getAllRecords('patient_vital_sign',array('appointment_id'=>$appointment_id),array('field'=>'position','type'=>'asc'));

    $vital_sign = $this->db->query("SELECT patient_vital_id, appointment_id, clinic_id, patient_id, umr_no, vital_sign, vital_result, sign_type, position, vital_sign_recording_date_time  from patient_vital_sign WHERE vital_sign_recording_date_time IN (SELECT MAX(vital_sign_recording_date_time) AS date FROM patient_vital_sign WHERE appointment_id = ".$appointment_id.") ORDER BY position ASC , vital_sign_recording_date_time DESC")->result_object();

    foreach ($vital_sign as $key => $value) {
            if($value->vital_sign == 'SBP'){// Capture Systolic Blood Pressure Value
                if($bp != ''){
                    $bp = $value->vital_result."/".$bp;
                }else{
                    $bp = $value->vital_result;
                }
                
                // Overwirte BP
                $data['vital_sign']['BP'] = $bp.' mmHg';

            }else if($value->vital_sign == 'DBP'){// Capture Diastolic Blood Pressure Value
                if($bp != ''){
                    $bp = $bp."/".$value->vital_result;    
                }else{
                    $bp = $value->vital_result;    
                }

                // Overwirte BP
                $data['vital_sign']['BP'] = $bp.' mmHg';

            }else{
                $v_unit = $this->db->query("SELECT * FROM `vital_sign` where short_form='".$value->vital_sign."'")->row();
                $data['vital_sign'][$value->vital_sign] = $value->vital_result." ".$v_unit->unit;   
            }
        }

        $data['symptoms'] = $this->db->query("select * from patient_presenting_symptoms pps,patient_ps_line_items ppls where pps.patient_presenting_symptoms_id=ppls.patient_presenting_symptoms_id and pps.appointment_id='".$id."'")->result();

        // History 

        $data['get_past_history_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='".$appointment_id."' and form_type='Past History'")->get()->result();
        $data['get_gpe_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='".$appointment_id."' and form_type='GPE'")->get()->result();
        $data['get_se_info'] = $this->db->query("select * from patient_form where appointment_id='".$appointment_id."' and form_type='Systemic Examination' order by patient_form_id DESC")->result();        
       
        $data['pastHistory'] = $this->db->query("select * from patient_form pf,patient_form_line_items pfl where pf.patient_form_id=pfl.patient_form_id and pf.appointment_id='".$id."' and pfl.section_text!='' and pf.form_type='Past History'")->result();
        $data['presentHistory'] = $this->db->query("select * from patient_form pf,patient_form_line_items pfl where pf.patient_form_id=pfl.patient_form_id and pf.appointment_id='".$id."' and pfl.section_text!='' and pf.form_type='Present History'")->result();
        $data['socialHistory'] = $this->db->query("select * from patient_form pf,patient_form_line_items pfl where pf.patient_form_id=pfl.patient_form_id and pf.appointment_id='".$id."' and pfl.section_text!='' and pf.form_type='Social History'")->result();
        $data['treatmentHistory'] = $this->db->query("select * from patient_form pf,patient_form_line_items pfl where pf.patient_form_id=pfl.patient_form_id and pf.appointment_id='".$id."' and pfl.section_text!='' and pf.form_type='Treatment History'")->result();
        $data['familyHistory'] = $this->db->query("select * from patient_form pf,patient_form_line_items pfl where pf.patient_form_id=pfl.patient_form_id and pf.appointment_id='".$id."' and pfl.section_text!='' and pf.form_type='Family History'")->result();
        $data['seInfo'] = $this->db->query("select * from patient_form pf,patient_form_line_items pfl where pf.patient_form_id=pfl.patient_form_id and pf.appointment_id='".$id."' and pfl.section_text!='' and pf.form_type='Systemic Examination'")->result();
        $data['gpeInfo'] = $this->db->query("select * from patient_form pf,patient_form_line_items pfl where pf.patient_form_id=pfl.patient_form_id and pf.appointment_id='".$id."' and pfl.section_text!='' and pf.form_type='GPE'")->result();
        // History Ends
        
        $pcd = $this->Generic_model->getSingleRecord('patient_clinical_diagnosis',array('appointment_id'=>$appointment_id),'');
        
        $data['pcd'] = $this->Generic_model->getSingleRecord('patient_clinical_diagnosis',array('appointment_id'=>$appointment_id),'');

        $data['patient_clinical_diagnosis'] = $this->Generic_model->getAllRecords("patient_cd_line_items", array('patient_clinical_diagnosis_id'=>$pcd->patient_clinical_diagnosis_id), $order = '');

        $data['patient_investigations'] = $this->db->query("select * from patient_investigation pi,patient_investigation_line_items pil where pil.patient_investigation_id=pi.patient_investigation_id and pi.appointment_id='".$appointment_id."'")->result();

        $data['patient_prescription'] = $this->Generic_model->getSingleRecord('patient_prescription',array('appointment_id'=>$appointment_id),'');

        
        $parent_appointment = $this->Generic_model->getSingleRecord('appointments',array('appointment_id'=>$appointment_id),'');
        
        if($parent_appointment->parent_appointment_id==0)
        {
            $next_follow_up=$this->Generic_model->getSingleRecord('appointments',array('parent_appointment_id'=>$appointment_id),array('field'=>'appointment_id','type'=>'desc'));

            if($next_follow_up->appointment_id==$appointment_id)
            {
                $data['follow_up_date'] = '';
            }else{
                $data['follow_up_date'] = $next_follow_up->appointment_date;
            }
        }else{
            $next_follow_up=$this->Generic_model->getSingleRecord('appointments',array('parent_appointment_id'=>$parent_appointment->parent_appointment_id),array('field'=>'appointment_id','type'=>'desc'));

            if($next_follow_up->appointment_id==$appointment_id)
            {
                $data['follow_up_date'] = '';
            }else{
                $data['follow_up_date'] = $next_follow_up->appointment_date;
            }
        }
        $data['clinicInfo'] = $this->db->query("select * from clinics where clinic_id='".$data['appointments']->clinic_id."'")->row();
        $data['header'] = 1;
        // echo $this->db->last_query();
        // echo "<pre>";print_r($data);echo "</pre>";
        $data['view'] = "pdfViews/ShortSummary";
        $this->load->view('pdfViews/pdfLayout',$data);

}

// Full Summary
public function fullSummary($id){
   $appointment_id = $id;
    // dd($parameters);

    $data['appointments'] = $this->db->select("a.*, c.clinic_name,c.clinic_logo,c.address,c.clinic_phone,p.title, p.first_name as pname, p.last_name as plname,p.gender,p.age,p.address_line,p.umr_no,p.preferred_language,p.mobile,p.qrcode,p.allergy,d.salutation,d.first_name as dfname,d.last_name as dlname, d.qualification, d.registration_code, dep.department_name")
    ->from("appointments a")->join("clinics c","a.clinic_id = c.clinic_id","left")->join("patients p","a.patient_id = p.patient_id","left")->join("doctors d","a.doctor_id = d.doctor_id","left")->join('department dep','d.department_id = dep.department_id')->where("a.appointment_id='" . $appointment_id . "'")->get()->row();
    $patient_name = $data['appointments']->pname . date('Ymd').$appointment_id;
    $clinic_id = $data['appointments']->clinic_id;
    $data['patientInfo'] = $this->db->select("*")->from("patients")->where("patient_id",$data['appointments']->patient_id)->get()->row();
    if($data['appointments']->doctor_id != 0)
    {
       $data['docInfo'] = $this->db->query("select d.*,de.department_name from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$data['appointments']->doctor_id."'")->row();
       $data['clinicDocInfo'] = $this->db->select("*")->from("clinic_doctor")->where("clinic_id",$clinic_id)->where("doctor_id",$data['appointments']->doctor_id)->get()->row();
    }
    $data['clinicInfo'] = $this->db->select("*")->from("clinics")->where("clinic_id",$clinic_id)->get()->row();
    $visit_no = $this->Generic_model->getAllRecords('appointments',array('clinic_id'=>$data['appointments']->clinic_id,'patient_id'=>$data['appointments']->patient_id,'doctor_id'=>$data['appointments']->doctor_id),array('field'=>'appointment_id','type'=>'desc'));

    $section_image = $this->db->query("select patient_form_id from patient_form where appointment_id='".$appointment_id."'")->result();

    $i=0;
    foreach($section_image as $image)
    {
        $abc['investigation'][$i]['patient_form_id'] = $image->patient_form_id;
        $inv_lineitems = $this->db->select("*")->from("patient_form_scribbling_images pil")
        ->where("pil.patient_form_id='".$image->patient_form_id."'")
        ->get()
        ->result();
        $j=0;
        foreach($inv_lineitems as $inv_lineitem){
        $abcd['scribbling'][0]['images']= $inv_lineitem->scribbling_image;
        $j++;
        }
        $i++;
    }


    $visit_count = count($visit_no);

    foreach ($visit_no as $key => $value) {
        if ($value->appointment_id == $appointment_id) {
            $visit_count--;
            $data['visit'] = $visit_count;
        }
    }

    $vital_sign = $this->db->query("SELECT patient_vital_id, appointment_id, clinic_id, patient_id, umr_no, vital_sign, vital_result, sign_type, position, vital_sign_recording_date_time  from patient_vital_sign WHERE vital_sign_recording_date_time IN (SELECT MAX(vital_sign_recording_date_time) AS date FROM patient_vital_sign WHERE appointment_id = ".$appointment_id.") ORDER BY position ASC , vital_sign_recording_date_time DESC")->result_object();

    $sbp = '';

    foreach ($vital_sign as $key => $value) {

            if($value->vital_sign == 'SBP'){// Capture Systolic Blood Pressure Value
                if($bp != ''){
                    $bp = $value->vital_result."/".$bp;
                }else{
                    $bp = $value->vital_result;
                }
                
                // Overwirte BP
                $data['vital_sign']['BP'] = $bp.' mmHg';

            }else if($value->vital_sign == 'DBP'){// Capture Diastolic Blood Pressure Value
                if($bp != ''){
                    $bp = $bp."/".$value->vital_result;    
                }else{
                    $bp = $value->vital_result;    
                }

                // Overwirte BP
                $data['vital_sign']['BP'] = $bp.' mmHg';

            }else{
                $v_unit = $this->db->query("SELECT * FROM `vital_sign` where short_form='".$value->vital_sign."'")->row();
                $data['vital_sign'][$value->vital_sign] = $value->vital_result." ".$v_unit->unit;   
            }
        }

        
        $pcd = $this->Generic_model->getSingleRecord('patient_clinical_diagnosis',array('appointment_id'=>$appointment_id),'');
        $data['pcd'] = $this->Generic_model->getSingleRecord('patient_clinical_diagnosis',array('appointment_id'=>$appointment_id),'');

        $data['patient_clinical_diagnosis'] = $this->Generic_model->getAllRecords("patient_cd_line_items", array('patient_clinical_diagnosis_id'=>$pcd->patient_clinical_diagnosis_id), $order = '');

        $data['patient_investigations'] = $this->db->query("select * from patient_investigation pi,patient_investigation_line_items pil where pil.patient_investigation_id=pi.patient_investigation_id and pi.appointment_id='".$appointment_id."'")->result();

        $data['patient_prescription'] = $this->Generic_model->getSingleRecord('patient_prescription',array('appointment_id'=>$appointment_id),'');

        
        $parent_appointment = $this->Generic_model->getSingleRecord('appointments',array('appointment_id'=>$appointment_id),'');
        
        if($parent_appointment->parent_appointment_id==0)
        {
            $next_follow_up=$this->Generic_model->getSingleRecord('appointments',array('parent_appointment_id'=>$appointment_id),array('field'=>'appointment_id','type'=>'desc'));

            if($next_follow_up->appointment_id==$appointment_id)
            {
                $data['follow_up_date'] = '';
            }else{
                $data['follow_up_date'] = $next_follow_up->appointment_date;
            }
        }else{
            $next_follow_up=$this->Generic_model->getSingleRecord('appointments',array('parent_appointment_id'=>$parent_appointment->parent_appointment_id),array('field'=>'appointment_id','type'=>'desc'));

            if($next_follow_up->appointment_id==$appointment_id)
            {
                $data['follow_up_date'] = '';
            }else{
                $data['follow_up_date'] = $next_follow_up->appointment_date;
            }
        }

            // Previous Documents
        $data['previous_documents'] = $this->db->select("*")->from("previous_documents")->where("appointment_id='".$appointment_id."'")->order_by("previous_document_id","desc")->get()->result();

            //presenting symtoms
        $data['presenting_symptoms'] = $this->db->select("*")->from("patient_presenting_symptoms ps")->join("patient_ps_line_items psl","ps.patient_presenting_symptoms_id = psl.patient_presenting_symptoms_id")->where("appointment_id = '" . $appointment_id. "'")->get()->result();

        // Get Patient Consent Forms Checklist
        $data['patient_consent_form'] = $this->db->query("select * from patient_consent_forms pcf,consent_form cf where cf.consent_form_id=pcf.consent_form_id and pcf.appointment_id='".$appointment_id."'")->result();
        // $data['patient_consent_checklist'] = $this->db->query("select * from patient_checklist pc,checklist_master cm where pc.checklist_id=cm.checklist_id and  pc.appointment_id='".$appointment_id."' and pc.checked='1'")->result();


            // Get Patient's Systemic Examination Form Info

        // $data['get_se_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='".$appointment_id."' and form_type='Systemic Examination'")->get()->row();
        $data['get_se_info'] = $this->db->query("select * from patient_form where appointment_id='".$appointment_id."' and form_type='Systemic Examination' order by patient_form_id DESC")->result();        

             // Get Patient's General Physical Examination info
        $data['get_gpe_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='".$appointment_id."' and form_type='GPE'")->get()->result();

              // Get Patient's HOPI info
        $data['get_hopi_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='".$appointment_id."' and form_type='HOPI'")->get()->result();

            // Get Patient's Past History info
        $data['get_past_history_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='".$appointment_id."' and form_type='Past History'")->get()->result();

            // Get Patient's Past History info
        $data['get_personal_history_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='".$appointment_id."' and form_type='Personal History'")->get()->result();

            // Get Patient's Treatment History info
        $data['get_treatment_history_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='".$appointment_id."' and form_type='Treatment History'")->get()->result();

            // Get Patient's Treatment History info
        $data['get_family_history_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='".$appointment_id."' and form_type='Family History'")->get()->result();

            // Get Patient's Social History info
        $data['get_social_history_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='".$appointment_id."' and form_type='Social History'")->get()->result();

            // Get Patient's Other History info
        $data['get_other_systems_info'] = $this->db->select("*")->from("patient_form")->where("appointment_id='".$appointment_id."' and form_type='Other Systems'")->get()->result();

        // $data['other_systems_data'] = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$data['get_other_systems_info']->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();

        // $data['get_other_systems_form']= $this->db->select("form_name")->from("form")->where("form_id='".$data['get_other_systems_info']->form_id."'")->get()->row();


            //Get invoice information
        $data['get_billing_info'] = $this->db->select("*")->from("billing")->where("appointment_id='".$appointment_id."'")->get()->result();

            //Patient Procedures
        $data['patient_procedures'] = $this->db->query("select pp.*,mp.medical_procedure as procedure_title from patient_procedure pp,medical_procedures mp where pp.medical_procedure_id=mp.medical_procedure_id and pp.appointment_id='".$appointment_id."'")->result();

        // PDF Settings
        $data['pdf_settings'] = $pdf_settings = $this->db->select("*")->from("clinic_pdf_settings")->where("clinic_id='".$data['appointments']->clinic_id."'")->get()->row();
        // echo json_encode($data);
        $data['view'] = 'pdfViews/FullSummary';
        $this->load->view('pdfViews/pdfLayout',$data);
}

}
