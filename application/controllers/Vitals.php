<?php
error_reporting(0);
// include "phpqrcode/qrlib.php";
ob_start();

defined('BASEPATH') OR exit('No direct script access allowed');

class Vitals extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('mail_send', array('mailtype'=>'html'));		 
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');	
    }

    public function index($patient_id='',$appointment_id=''){

        $clinic_id = $this->session->userdata('clinic_id');
        $profile_id=$this->session->userdata('role_id');

        // if($clinic_id==0)
        // {
        //     $data['profile_pages']=$this->db->query("select a.user_entity_name,a.user_entity_alias,a.entity_url,a.method_name from user_entities a inner join profile_permissions b on a.user_entity_id=b.user_entity_id where profile_id=".$profile_id." and a.level_alias='page' and p_read=1 and parent_id!='' ORDER BY position,user_entity_name asc")->result();
        // }
        // else
        // {
        //     $data['profile_pages']=$this->db->query("select c.user_entity_name,c.entity_url,c.user_entity_alias,c.method_name from std_uac_entities a inner join user_entities c on a.user_entity_id=c.user_entity_id inner join profile_permissions b on a.user_entity_id=b.user_entity_id where profile_id=".$profile_id." and c.level_alias='page' and p_read=1  and parent_id!='' ORDER BY position,user_entity_name asc")->result();
        // }

        // retrieve the appointments
        $this->db->select('A.appointment_id, P.clinic_id, A.patient_id, P.umr_no, A.doctor_id, Doc.first_name as doctor_first_name, Doc.last_name as doctor_last_name, Dep.department_name, A.appointment_type, A.appointment_date, A.appointment_time_slot, A.priority, A.description, A.payment_status as appointment_payment_status, A.status as appointment_status, P.title, P.first_name, P.last_name, P.gender, P.date_of_birth, P.age, P.occupation, P.mobile, P.alternate_mobile, P.email_id, P.address_line, P.district_id, P.payment_status as registration_payment_status, D.district_name, P.state_id, S.state_name, P.pincode, P.photo, P.qrcode, P.preferred_language, P.allergy');
        $this->db->from('appointments A');
        $this->db->join('patients P','P.patient_id = A.patient_id');
        $this->db->join('doctors Doc','A.doctor_id = Doc.doctor_id');
        $this->db->join('department Dep','Doc.department_id = Dep.department_id');
        $this->db->join('districts D','P.district_id = D.district_id','left');
        $this->db->join('states S','P.state_id = S.state_id','left');
        $this->db->where('A.patient_id =',$patient_id);
        $this->db->where_not_in('A.status',$status);

        // If the appointment Id is specified
        if($appointment_id) 
            $this->db->where('A.appointment_id =',$appointment_id);

        $data['appointmentInfo'] = $this->db->get()->result();
        // echo $this->db->last_query();

        $data['app_info'] = $info = $this->db->query("select *,a.status as app_status from appointments a inner join doctors d on(a.doctor_id = d.doctor_id) where a.patient_id='".$patient_id."' and a.appointment_date <= '".date('Y-m-d')."' and a.status!='drop' and a.status!='reschedule' order by a.appointment_date desc")->row();

        $data['patient_info'] = $this->db->query("select * from patients where patient_id=".$patient_id)->row();
        $data['result'] = $this->db->query('select * from patient_vital_sign where patient_id = "'.$patient_id.'" group by vital_sign_recording_date_time order by vital_sign_recording_date_time desc')->result();
        // echo $this->db->last_query();
        // echo "<pre>";
        // print_r($data['result']);
        // echo "</pre>";
        // exit();

        $data['clinic_id']=$clinic_id;
        $data['patient_id']=$patient_id;
        $data['appointment_id']=$appointment_id;
        $data['view'] = 'vitals/vitals_view';
        $this->load->view('layout', $data);

    }

    
    public function OCR($appointment_id){
        $appInfo = $this->Generic_model->getSingleRecord('appointments',array('appointment_id'=>$appointment_id));

        if(count($appInfo) > 0){

            $data['clinicsInfo'] = clinicDetails($appInfo->clinic_id);
            $data['docInfo'] = doctorDetails($appInfo->doctor_id);
            $data['patientInfo'] = getPatientDetails($appInfo->patient_id);
            $data['appointments'] = $appInfo;
            $data['billingInfo'] = $this->db->query("select * from billing where appointment_id='".$appointment_id."' and (billing_type='Registration & Consultation' or billing_type='Consultation')")->row();
            $data['symptoms'] = $this->Generic_model->getJoinRecords('patient_presenting_symptoms pps','patient_ps_line_items ppsl','pps.patient_presenting_symptoms_id=ppsl.patient_presenting_symptoms_id',array('pps.appointment_id'=>$appointment_id),'','*');
            // echo $this->db->last_query();
            $data['clinicalDiagnosis'] = $this->Generic_model->getJoinRecords('patient_clinical_diagnosis pcd','patient_cd_line_items pcdl','pcd.patient_clinical_diagnosis_id=pcdl.patient_clinical_diagnosis_id',array('appointment_id'=>$appointment_id),'','*');
            $data['investigations'] = $this->Generic_model->getJoinRecords('patient_investigation pin','patient_investigation_line_items pinl','pin.patient_investigation_id=pinl.patient_investigation_id',array('appointment_id'=>$appointment_id),'','*');
            $data['prescriptions'] = $this->Generic_model->getJoinRecords('patient_prescription pp','patient_prescription_drug ppd','pp.patient_prescription_id=ppd.patient_prescription_id',array('appointment_id'=>$appointment_id),'','*');
            
            $data['pdfSettings'] = $this->Generic_model->getSingleRecord('clinic_pdf_settings', array('clinic_id'=>$appInfo->clinic_id));
            
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
            $html = $this->load->view('reports/ocr', $data, true);
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
            $this->m_pdf->pdf->Output("./uploads/summary_reports/ocr-" . $pdfFilePath, "F");
            
            redirect(base_url() . 'uploads/summary_reports/ocr-' . $pdfFilePath);
        }

        
    }

    public function patient_vitals($patient_id='',$appointment_id=''){

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
        $data['result'] = $this->db->query('select vital_sign_recording_date_time from patient_vital_sign where patient_id = "'.$patient_id.'" and appointment_id = "'.$appointment_id.'" group by vital_sign_recording_date_time order by vital_sign_recording_date_time desc')->result();

        $data['clinic_id']=$clinic_id;
        $data['patient_id']=$patient_id;
        $data['appointment_id']=$appointment_id;
        $data['view'] = 'vitals/vitals_view';
        $this->load->view('layout', $data);

    }


    public function add_vitals($patient_id='',$appointment_id=''){

        // retrieve the appointments
        $this->db->select('A.appointment_id, P.clinic_id, A.patient_id, P.umr_no, A.doctor_id, Doc.first_name as doctor_first_name, Doc.last_name as doctor_last_name, Dep.department_name, A.appointment_type, A.appointment_date, A.appointment_time_slot, A.priority, A.description, A.payment_status as appointment_payment_status, A.status as appointment_status, P.title, P.first_name, P.last_name, P.gender, P.date_of_birth, P.age, P.occupation, P.mobile, P.alternate_mobile, P.email_id, P.address_line, P.district_id, P.payment_status as registration_payment_status, D.district_name, P.state_id, S.state_name, P.pincode, P.photo, P.qrcode, P.preferred_language, P.allergy');
        $this->db->from('appointments A');
        $this->db->join('patients P','P.patient_id = A.patient_id');
        $this->db->join('doctors Doc','A.doctor_id = Doc.doctor_id');
        $this->db->join('department Dep','Doc.department_id = Dep.department_id');
        $this->db->join('districts D','P.district_id = D.district_id','left');
        $this->db->join('states S','P.state_id = S.state_id','left');
        $this->db->where('A.patient_id =',$patient_id);
        $this->db->where_not_in('A.status',$status);

        // If the appointment Id is specified
        if($appointment_id) 
            $this->db->where('A.appointment_id =',$appointment_id);

        $data['appointmentInfo'] = $this->db->get()->result();

        $clinic_id = $this->session->userdata('clinic_id');
        $profile_id=$this->session->userdata('profile_id');

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
        // echo $this->input->post('allergyCheck').'<br>';
        // print_r($this->input->post());
        // echo '</pre>';
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
        //  echo "<pre>test";print_r($_POST);exit;

        // $appointment_info = $this->db->query('select * from appointments where appointment_id ='.$appointment_id.' AND patient_id='.$patient_id)->row();

        $appointment_info = $this->db->query("select *,a.status as app_status from appointments a inner join doctors d on(a.doctor_id = d.doctor_id) where a.patient_id='".$patient_id."' and a.appointment_date <= '".date('Y-m-d')."' and a.status!='drop' and a.status!='reschedule' order by a.appointment_date desc")->row();

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
                
                $res = $this->Generic_model->insertData('patient_vital_sign',$data);

                if($res){
                    $this->db->query("UPDATE appointments set status='vital_signs' where appointment_id='".$appointment_info->appointment_id."' and patient_id='".$appointment_info->patient_id."'");   
                }
            } 
            if($name == "SBP"){}else{$j++;} 
        }

        $k=$j;

        // Extra Vitals which not a gneric/standard
        $count = count($this->input->post('vital_sign'));

        if($count){

            $get_vital_sign = $this->input->post('vital_sign');
            $get_vital_sign_val = $this->input->post('vital_sign_val');

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

        // Update Patient Allergy
        if($this->input->post('allergyCheck') == 'No' && $this->input->post('allergy') == '' ){
            $patientAllergy['allergy'] = 'No'; 
        }else if($this->input->post('allergy') != ''){
            $patientAllergy['allergy'] = $this->input->post('allergy');
        }
        
        $this->Generic_model->updateData("patients",$patientAllergy,array('patient_id' => $appointment_info->patient_id)); 

        $this->Generic_model->pushNotifications($ap_details->patient_id,$appointment_id,$ap_details->doctor_id,$ap_details->clinic_id,'push_to_consultant','patient_21_details_tab');

        // redirect('Vitals/index/'.$patient_id.'/'.$appointment_id);
        $stype=$this->input->post('save_vitals');
        // exit;
        if($stype=="Submit")
        {
            redirect('Vitals/index/'.$patient_id.'/'.$appointment_id, 'refresh');
            // $url = base_url("Vitals/index/".$patient_id."/".$appointment_id);
            // $this->redirect($url);
        }        
        else
        {
            // $url = base_url("Vitals/print_vitals/".$patient_id);
            redirect('Vitals/print_vitals/'.$patient_id, 'refresh');
        }
        // echo $url;
        // exit;
        // header("location: ".$url);
        // exit;
    }

    function redirect($url){
        redirect($url);
    }

    public function print_vitals($patient_id='', $type='') {

        $appointment_info = $this->db->query("select *,a.status as app_status from appointments a inner join doctors d on(a.doctor_id = d.doctor_id) where a.patient_id='".$patient_id."' and a.appointment_date <= '".date('Y-m-d')."' and a.status!='drop' and a.status!='reschedule' order by a.appointment_date desc")->row();

        $data['appointments'] = $this->db->query("select a.*, c.clinic_name,c.clinic_logo,c.address,c.clinic_phone,p.first_name as pname,p.last_name as plname,p.gender,p.age,p.title,p.referred_by,p.referred_by_type,p.allergy,p.address_line,p.location,p.mobile,p.qrcode,d.salutation,d.first_name as dfname,d.last_name as dlname,d.qualification,d.registration_code,dp.department_name from appointments a 
            left join clinics c on a.clinic_id = c.clinic_id
            left join patients p on a.patient_id = p.patient_id

            left join doctors d on a.doctor_id = d.doctor_id
            left join department dp on d.department_id = dp.department_id
            where a.appointment_id='".$appointment_info->appointment_id."'")->row();
        $patient_name = $data['appointments']->pname." ".$data['appointments']->plname.date('Ymd').time();

        $data['doctor_info'] = $this->db->select("*")->from("doctors a")->join("department b","a.department_id = b.department_id")->where("a.doctor_id='" . $data['appointments']->doctor_id . "'")->get()->row();

        $data['vital_sign'] = $this->db->query("select * from patient_vital_sign where appointment_id='".$appointment_info->appointment_id."' order by patient_vital_id")->result();

        $data['pdf_settings'] = $pdf_settings = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$data['appointments']->clinic_id."'")->row();

        //For Submit & Print
        $data['type'] = $type;

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
        $HeaderConfiguration = [
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

        $footerConfiguration = [
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
        // if($type == "SP")
        // {
        //     $file = "./uploads/vital_reports/".$pdfFilePath;
        //     $this->m_pdf->pdf->SetJS('window.open("'.$file.'","_blank")');
        // }
        $this->m_pdf->pdf->Output("./uploads/vital_reports/".$pdfFilePath, "F"); 
        redirect("uploads/vital_reports/".$pdfFilePath);

    } 

    // Function used to edit the vitals
    public function vital_edit($patient_id='',$appointment_id='')
    {

        // Retrieve the appointments
        $this->db->select('A.appointment_id, P.clinic_id, A.patient_id, P.umr_no, A.doctor_id, Doc.first_name as doctor_first_name, Doc.last_name as doctor_last_name, Dep.department_name, A.appointment_type, A.appointment_date, A.appointment_time_slot, A.priority, A.description, A.payment_status as appointment_payment_status, A.status as appointment_status, P.title, P.first_name, P.last_name, P.gender, P.date_of_birth, P.age, P.occupation, P.mobile, P.alternate_mobile, P.email_id, P.address_line, P.district_id, P.payment_status as registration_payment_status, D.district_name, P.state_id, S.state_name, P.pincode, P.photo, P.qrcode, P.preferred_language, P.allergy');
        $this->db->from('appointments A');
        $this->db->join('patients P','P.patient_id = A.patient_id');
        $this->db->join('doctors Doc','A.doctor_id = Doc.doctor_id');
        $this->db->join('department Dep','Doc.department_id = Dep.department_id');
        $this->db->join('districts D','P.district_id = D.district_id','left');
        $this->db->join('states S','P.state_id = S.state_id','left');
        $this->db->where('A.patient_id =',$patient_id);
        $this->db->where_not_in('A.status',$status);

        // If the appointment Id is specified
        if($appointment_id) 
            $this->db->where('A.appointment_id =',$appointment_id);

        $data['appointmentInfo'] = $this->db->get()->result();

        $clinic_id = $this->session->userdata('clinic_id');
        $profile_id=$this->session->userdata('profile_id');

        $info = $this->db->query("select *,a.status as app_status from appointments a inner join doctors d on(a.doctor_id = d.doctor_id) where a.patient_id='".$patient_id."' and a.appointment_date <= '".date('Y-m-d')."' and a.status!='drop' and a.status!='reschedule' order by a.appointment_date desc")->row();

        $data['patient_info'] = $this->db->query("select * from patients where patient_id=".$patient_id)->row();

        $result = $this->db->query('select * from patient_vital_sign where patient_id = "'.$patient_id.'" and appointment_id = "'.$info->appointment_id.'"  and  vital_sign_recording_date_time = (select max(vital_sign_recording_date_time) From patient_vital_sign where patient_id="'.$patient_id.'") order by  vital_sign_recording_date_time desc,position asc')->result();

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

        $get_vitals = $this->input->post('vitals');
        $appointment_info = $this->db->query("select *,a.status as app_status from appointments a inner join doctors d on(a.doctor_id = d.doctor_id) where a.patient_id='".$patient_id."' and a.appointment_date <= '".date('Y-m-d')."' and a.status!='drop' and a.status!='reschedule' order by a.appointment_date desc")->row();

        $j=0;
        $cntr = 0;
        foreach ($get_vitals as $name => $result) {
        
            $data['patient_id'] = $appointment_info->patient_id;
            $data['umr_no'] = $appointment_info->umr_no;
            $data['clinic_id'] = $appointment_info->clinic_id;
            $data['appointment_id'] = $appointment_info->appointment_id;
            $data['vital_sign'] = $name;
            $data['vital_result'] = $result;
            if($name == 'SBP' || $name == 'DBP'){
                if($cntr == 0){
                    $positionCounter = $cntr = $j++;  
                }else{
                    $positionCounter = $cntr;
                }                
                $data['position'] = $cntr;
            }else{
                $data['position'] = $j++;
            }
            
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
                //$pma['allergy'] = "";
                //$this->Generic_model->updateData("patients",$pma,array('patient_id'=>$appointment_info->patient_id)); 
            }else{
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
        if($this->input->post('save_vitals') == "Submit")
        {
            redirect('Vitals/index/'.$patient_id);
        }        
        elseif($this->input->post('save_vitals') == "Submit & Print")
        {
            redirect('Vitals/print_vitals/'.$patient_id.'/SP');
        }
        

    }

}