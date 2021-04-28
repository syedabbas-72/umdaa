<?php
error_reporting(0);
include "phpqrcode/qrlib.php";

defined('BASEPATH') OR exit('No direct script access allowed');

class SummaryReports extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('mail_send', array('mailtype'=>'html'));		 
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');	
    }


    public function index($patient_id='', $appointment_id = NULL){
        $data['appointmentInfo'] = $this->getPatientAppointmentInfo($patient_id, $appointment_id);
        
        $data['clinic_id']=$clinic_id;
        $data['patient_id']=$patient_id;
        $data['appointment_id']=$appointment_id;
        $appInfo = $this->Generic_model->getSingleRecord('appointments', array('appointment_id'=>$appointment_id));
        
        $data['appointments'] = $this->db->query("select a.appointment_id, a.appointment_date, d.doctor_id, d.salutation, d.first_name, d.last_name, de.department_id, de.department_name from appointments a 
            left join doctors d on (a.doctor_id=d.doctor_id)
            left join department de on (d.department_id=de.department_id)
            where a.patient_id='" . $patient_id . "' and a.doctor_id='".$appInfo->doctor_id."' and a.status='closed' and a.status!='drop' and a.status!='reschedule'  and a.appointment_date <= '".date('Y-m-d')."' order by a.appointment_date desc")->result();

        $data['view'] = 'profile/summary_list';
        $this->load->view('layout', $data);
    }

    public function shortSummary($appointment_id){
        $appInfo = $this->Generic_model->getSingleRecord('appointments',array('appointment_id'=>$appointment_id));

        if(count($appInfo) > 0){

            $data['clinicsInfo'] = clinicDetails($appInfo->clinic_id);
            $data['docInfo'] = doctorDetails($appInfo->doctor_id);
            $data['patientInfo'] = getPatientDetails($appInfo->patient_id);
            $data['appointments'] = $appInfo;
            $data['symptoms'] = $this->Generic_model->getJoinRecords('patient_presenting_symptoms pps','patient_ps_line_items ppsl','pps.patient_presenting_symptoms_id=ppsl.patient_presenting_symptoms_id',array('pps.appointment_id'=>$appointment_id),'','*');
            // echo $this->db->last_query();
            $data['clinicalDiagnosis'] = $this->Generic_model->getJoinRecords('patient_clinical_diagnosis pcd','patient_cd_line_items pcdl','pcd.patient_clinical_diagnosis_id=pcdl.patient_clinical_diagnosis_id',array('appointment_id'=>$appointment_id),'','*');
            $data['investigations'] = $this->Generic_model->getJoinRecords('patient_investigation pin','patient_investigation_line_items pinl','pin.patient_investigation_id=pinl.patient_investigation_id',array('appointment_id'=>$appointment_id),'','*');
            $data['prescriptions'] = $this->Generic_model->getJoinRecords('patient_prescription pp','patient_prescription_drug ppd','pp.patient_prescription_id=ppd.patient_prescription_id',array('appointment_id'=>$appointment_id),'','*');
            
            $data['pdfSettings'] = $this->Generic_model->getSingleRecord('clinic_pdf_settings', array('clinic_id'=>$appInfo->clinic_id));
            
            $data['notes'] = $this->Generic_model->getJoinRecords('patient_notes pn','patient_notes_line_items pnl','pn.patient_notes_id=pnl.patient_notes_id',array('appointment_id'=>$appointment_id),'','*');    

            $data['prescriptionsInfo'] = $this->Generic_model->getSingleRecord('patient_prescription', array('appointment_id'=>$appointment_id));
            
            $data['get_past_history_info'] = $this->Generic_model->getJoinRecords('patient_form pf','patient_form_line_items pfl','pf.patient_form_id=pfl.patient_form_id',array('appointment_id'=>$appointment_id,'form_type'=>'Past History'),'','*');
            $data['gpe_info'] = $this->Generic_model->getJoinRecords('patient_form pf','patient_form_line_items pfl','pf.patient_form_id=pfl.patient_form_id',array('appointment_id'=>$appointment_id,'form_type'=>'GPE'),'','*');
            $data['se_info'] = $this->Generic_model->getJoinRecords('patient_form pf','patient_form_line_items pfl','pf.patient_form_id=pfl.patient_form_id',array('appointment_id'=>$appointment_id,'form_type'=>'Systemic Examination'),'','*');

            $data['pastHistory'] = $this->db->query("select * from patient_form pf,patient_form_line_items pfl where pf.patient_form_id=pfl.patient_form_id and pf.appointment_id='".$appointment_id."' and pfl.section_text!='' and pf.form_type='Past History'")->result();
            $data['symptoms'] = $this->db->query("select * from patient_presenting_symptoms pps,patient_ps_line_items ppls where pps.patient_presenting_symptoms_id=ppls.patient_presenting_symptoms_id and pps.appointment_id='".$appointment_id."'")->result();

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

            $this->load->library('M_pdf');
            $html = $this->load->view('reports/short_prescription_summary', $data, true);
            $pdfFilePath = $appointment_id.time().".pdf";
            $stylesheet  = '';
            $stylesheet .= file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
            $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");
            // $stylesheet .= file_get_contents(base_url()."assets/css/print.css");
            $this->m_pdf->pdf->autoScriptToLang = true;
            $this->m_pdf->pdf->autoLangToFont = true;

            $this->m_pdf->pdf->shrink_tables_to_fit = 1;
            $this->m_pdf->pdf->setAutoTopMargin = "stretch";
            $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
            $this->m_pdf->pdf->defaultheaderline = 0;

            // $this->m_pdf->pdf->SetFont("poppins");
            $this->m_pdf->pdf->WriteHTML($stylesheet,1);
            // $this->m_pdf->pdf->DefHTMLFooterByName('Chapter2Footer','<div style="text-align: right; font-weight: bold; font-size: 8pt; font-style: italic;">Chapter 2 Footer</div>');
            $this->m_pdf->pdf->WriteHTML($html,2);
            $this->m_pdf->pdf->Output("./uploads/summary_reports/short-" . $pdfFilePath, "F");
            
            redirect(base_url() . 'uploads/summary_reports/short-' . $pdfFilePath);
        }

        
    }

    /**
    * Used to get the patient's all open appointment/specific appointment complete information based on the Patient id & Appointment id
    * @name getPatientAppointmentInfo
    * @access public
    * @author Uday Kanth Rapalli
    */
    public function getPatientAppointmentInfo($patient_id, $appointment_id = null) {

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

        return $this->db->get()->result();
    }

    public function print_summary($appointment_id){
        
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
        if($data['appointments']->doctor_id != 0)
        {
           $data['docInfo'] = $this->db->query("select d.*,de.department_name from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$data['appointments']->doctor_id."'")->row();
           $data['clinicDocInfo'] = $this->db->select("*")->from("clinic_doctor")->where("clinic_id",$clinic_id)->where("doctor_id",$data['appointments']->doctor_id)->get()->row();
        }

    // $data['docInfo'] = $this->db->query("select * from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$data['appointments']->doctor_id."'")->row();

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



        
        $data['pdf_settings'] = $pdf_settings = $this->db->select("*")->from("clinic_pdf_settings")->where("clinic_id='".$data['appointments']->clinic_id."'")->get()->row();
        // echo $this->db->last_query();

        
        $this->load->library('M_pdf');
        $html = $this->load->view('reports/short_summary_reports_pdf', $data, true);
        $pdfFilePath = time() . ".pdf";
        $stylesheet  = '';
    $stylesheet .= file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
    $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");
    $this->m_pdf->pdf->autoScriptToLang = true;
    $this->m_pdf->pdf->autoLangToFont = true;

    $this->m_pdf->pdf->shrink_tables_to_fit = 1;
    $this->m_pdf->pdf->setAutoTopMargin = "stretch";
    $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
    $this->m_pdf->pdf->defaultheaderline = 0;

    $this->m_pdf->pdf->WriteHTML($stylesheet,1);
    $this->m_pdf->pdf->WriteHTML($html,2);
    $this->m_pdf->pdf->Output("./uploads/summary_reports/short-" . $pdfFilePath, "F");

        // $data['appointments'] = $this->db->query("select a.*, c.clinic_name,c.clinic_logo,c.address,c.clinic_phone,p.first_name as pname,p.last_name as plname,p.gender,p.age,p.title,p.referred_by,p.allergy,p.address_line,p.mobile,p.qrcode,d.salutation,d.first_name as dfname,d.last_name as dlname,d.qualification,d.registration_code,dp.department_name from appointments a 
        //     left join clinics c on a.clinic_id = c.clinic_id
        //     left join patients p on a.patient_id = p.patient_id
        //     left join doctors d on a.doctor_id = d.doctor_id
        //     left join department dp on d.department_id = dp.department_id
        //     where a.appointment_id='".$appointment_id."'")->row();

        // $patient_name = $data['appointments']->pname . date('Ymd').$appointment_id;

        // $data['appointments']->patient_full_name = $patient_name;

        // $visit_no = $this->Generic_model->getAllRecords('appointments',array('clinic_id'=>$data['appointments']->clinic_id,'patient_id'=>$data['appointments']->patient_id,'doctor_id'=>$data['appointments']->doctor_id),array('field'=>'appointment_id','type'=>'desc'));


        // $visit_count = count($visit_no);

        // foreach ($visit_no as $key => $value) {
        //     if ($value->appointment_id == $appointment_id) {
        //         $visit_count--;
        //         $data['visit'] = $visit_count;
        //     }
        // }
        // $vital_sign =  $this->Generic_model->getAllRecords('patient_vital_sign',array('appointment_id'=>$appointment_id),array('field'=>'position','type'=>'asc'));


        // foreach ($vital_sign as $key => $value) {
        //     if($value->vital_sign=='DBP'){
        //         $dbp = $value->vital_result;
        //     } else if($value->vital_sign=='SBP'){
        //         $data['vital_sign']['BP'] = $value->vital_result.'/'.$dbp. ' mmHg';
        //     }else{
        //         $v_unit = $this->db->query("SELECT * FROM `vital_sign` where short_form='".$value->vital_sign."'")->row();
        //         $data['vital_sign'][$value->vital_sign] = $value->vital_result." ".$v_unit->unit;   
        //     }
        // }

        // $pcd = $this->Generic_model->getSingleRecord('patient_clinical_diagnosis',array('appointment_id'=>$appointment_id),'');

        // $data['patient_clinical_diagnosis'] = $this->Generic_model->getAllRecords("patient_cd_line_items", array('patient_clinical_diagnosis_id'=>$pcd->patient_clinical_diagnosis_id), $order = '');

        // $data['patient_investigations'] = $this->db->query("Select * from patient_investigation_line_items pil inner join investigations inv on (pil.investigation_id=inv.investigation_id) inner join patient_investigation pi on (pil.patient_investigation_id=pi.patient_investigation_id) where  pi.appointment_id='".$appointment_id."'")->result();

        // $data['patient_prescription'] = $this->Generic_model->getSingleRecord('patient_prescription',array('appointment_id'=>$appointment_id),'');

        // $parent_appointment = $this->Generic_model->getSingleRecord('appointments',array('parent_appointment_id'=>$appointment_id),'');

        // if(count($parent_appointment)>0)
        // {
        //     $data['follow_up_date'] = $parent_appointment->appointment_date;
        // }else{
        //     $data['follow_up_date'] = "";
        // }

        // $data['pdf_settings'] = $pdf_settings = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$data['appointments']->clinic_id."'")->row();

        // $this->load->library('M_pdf');
        // $html = $this->load->view('reports/short_summary_reports_pdf', $data, true);
        // $pdfFilePath = strtolower(str_replace(" ", "_", $patient_name)) . ".pdf";
        // $stylesheet  = '';
        // $stylesheet .= file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
        // $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");
        // $this->m_pdf->pdf->autoScriptToLang = true;
        // $this->m_pdf->pdf->autoLangToFont = true;

        // $this->m_pdf->pdf->shrink_tables_to_fit = 1;
        // $this->m_pdf->pdf->setAutoTopMargin = "stretch";
        // $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
        // $this->m_pdf->pdf->defaultheaderline = 0;

        // $footerConfiguration = 
        // [
        //     'L' => [ // L for Left part of the header
        //     'content' => 'Powered by umdaa',
        // ],
        //     'C' => [ // C for Center part of the header
        //     'content' => '',
        // ],
        // 'R' => [
        //     'content' => '<span style="font-size:12px;"><b>Date: </b>'.date("d/m/Y").'</span>',
        // ],
        //     'line' => 0, // That's the relevant parameter
        // ];

        // $Footer = [
        //     'odd' => $footerConfiguration,
        //     'even' => $footerConfiguration
        // ];
        // $this->m_pdf->pdf->SetHTMLFooter($Footer);
        // $this->m_pdf->pdf->WriteHTML($stylesheet,1);
        // $this->m_pdf->pdf->WriteHTML($html,2);

        // $this->m_pdf->pdf->Output("./uploads/summary_reports/summary-" . $pdfFilePath, "F");
        redirect(base_url() . 'uploads/summary_reports/short-' . $pdfFilePath);
    }

}