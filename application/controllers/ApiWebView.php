<?php



error_reporting(0);

defined('BASEPATH') OR exit('No direct script access allowed');



class ApiWebView extends CI_Controller {

  public function __construct() {
        parent::__construct();   
    }

    public function mySummary($app_id){
        $appointment_id = $app_id;
        //$data['visit']=$visit;
        $data['appointments'] = $this->db->query("select a.*, c.clinic_name,c.clinic_logo,c.address,c.clinic_phone,p.first_name as pname,p.gender,p.age,p.address_line,p.umr_no,p.preferred_language,p.mobile,p.qrcode,d.salutation,d.first_name as dfname,d.last_name as dlname,d.registration_code,d.qualification,d.department_id from appointments a left join clinics c on a.clinic_id = c.clinic_id left join patients p on a.patient_id = p.patient_id left join doctors d on a.doctor_id = d.doctor_id where a.appointment_id='" . $appointment_id . "'")->row();
        $patient_name = $data['appointments']->pname . date('Ymd').$appointment_id;

        $visit_no = $this->Generic_model->getAllRecords('appointments',array('clinic_id'=>$data['appointments']->clinic_id,'patient_id'=>$data['appointments']->patient_id,'doctor_id'=>$data['appointments']->doctor_id),array('field'=>'appointment_id','type'=>'desc'));


        $visit_count = count($visit_no);

        foreach ($visit_no as $key => $value) {
            if ($value->appointment_id == $appointment_id) {
                $visit_count--;
                $data['visit'] = $visit_count;
            }
        }
        $vital_sign =  $this->Generic_model->getAllRecords('patient_vital_sign',array('appointment_id'=>$appointment_id),array('field'=>'position','type'=>'asc'));


        foreach ($vital_sign as $key => $value) {
            if($value->vital_sign=='DBP'){
                $dbp = $value->vital_result;
            } else if($value->vital_sign=='SBP'){
                $data['vital_sign']['BP'] = $value->vital_result.'/'.$dbp. ' mmHg';
            }else{
                $v_unit = $this->db->query("SELECT * FROM `vital_sign` where short_form='".$value->vital_sign."'")->row();
                $data['vital_sign'][$value->vital_sign] = $value->vital_result." ".$v_unit->unit;   
            }
        }
        
        $pcd = $this->Generic_model->getSingleRecord('patient_clinical_diagnosis',array('appointment_id'=>$appointment_id),'');

        $data['patient_clinical_diagnosis'] = $this->Generic_model->getAllRecords("patient_cd_line_items", array('patient_clinical_diagnosis_id'=>$pcd->patient_clinical_diagnosis_id), $order = '');

        $data['patient_investigations'] = $this->db->query("Select * from patient_investigation_line_items pil inner join investigations inv on (pil.investigation_id=inv.investigation_id) inner join patient_investigation pi on (pil.patient_investigation_id=pi.patient_investigation_id) where  pi.appointment_id='".$appointment_id."'")->result();

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



        
        $data['pdf_settings'] = $pdf_settings = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$data['appointments']->clinic_id."'")->row();
        $this->load->view('reports/citizen_short_summary_reports_pdf', $data);
    }


    public function myTransaction($app_id,$billing_id){
        $app_id = $app_id;
        $billing_id123 = $billing_id;
        $info = $this->db->select('*')->from('appointments A')->join('doctors Doc','A.doctor_id = Doc.doctor_id')->where('A.appointment_id =',$app_id)->get()->row();
        $clinic_deatails = $this->Generic_model->getSingleRecord('clinics',array('clinic_id'=>$info->clinic_id),$order='');

        $doctor_deatails = $this->Generic_model->getSingleRecord('doctors',array('doctor_id'=>$info->doctor_id),$order='');
        $review_deatails = $this->Generic_model->getSingleRecord('clinic_doctor',array('doctor_id'=>$info->doctor_id,'clinic_id'=>$info->clinic_id),$order='');

        $departments = $this->Generic_model->getSingleRecord('department',array('department_id'=>$doctor_deatails->department_id),$order='');
        $billing_master = $this->Generic_model->getSingleRecord('billing',array('billing_id'=>$billing_id123),$order='');
        $billing = $this->Generic_model->getAllRecords('billing_line_items',array('billing_id'=>$billing_id123),$order='');
        $patient_details = $this->Generic_model->getSingleRecord('patients',array('patient_id'=>$info->patient_id),$order='');     

        $district_details = $this->Generic_model->getSingleRecord('districts',array('district_id'=>$patient_details->district_id),$order='');

        $state_details = $this->Generic_model->getSingleRecord('states',array('state_id'=>$patient_details->state_id),$order='');

        $data['clinic_logo']=$clinic_deatails->clinic_logo;
        $data['review_days']=$review_deatails->review_days;
        $data['clinic_phone']=$clinic_deatails->clinic_phone;
        $data['clinic_name']=$clinic_deatails->clinic_name;
        $data['address']=$clinic_deatails->address;
        $data['doctor_name']="Dr. ".strtoupper($doctor_deatails->first_name." ".$doctor_deatails->last_name);
        $data['qualification']=$doctor_deatails->qualification;
        $data['department_name']=$departments->department_name;
        $data['patient_name']=strtoupper($patient_details->first_name." ".$patient_details->last_name);
        $data['age_unit']=$patient_details->age_unit;
        $data['age']=$patient_details->age;
        $data['gender']=$patient_details->gender;
        $data['umr_no']=$patient_details->umr_no;
        $data['doctorInfo']=$doctor_deatails;
        $data['patientInfo']=$patient_details;

        if($patient_details->pincode!=="") { 
            $pincode = ",".$patient_details->pincode; 
        } else { 
            $pincode="";
        }

        $data['patient_address']=$patient_details->address_line." ".$pincode;
        $data['billing']=$billing;
        $data['billing_master']=$billing_master;
        $data['invoice_no']=$billing_master->invoice_no;
        $data['invoice_no_alias']=$billing_master->invoice_no_alias;
        if($this->input->post("payment_mode")!=""){
            $data['payment_method']=$this->input->post("payment_mode");
        }
        else{
            $data['payment_method']=$billing_master->payment_mode;
        }

        $pdfFilePath = "billing_".$info->patient_id.$billing_id.".pdf";
        $data['file_name'] = $pdfFilePath;

        $data['pdf_settings'] = $pdf_settings = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$info->clinic_id."'")->row();
        $this->load->view('billing/generate_billing_citizen',$data);
    }
}

