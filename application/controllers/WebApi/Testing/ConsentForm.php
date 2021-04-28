<?php


defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
ini_set('memory_limit', '-1');

require APPPATH . '/libraries/REST_Controller1.php';

class ConsentForm extends REST_Controller1
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

    public function searchForm_get($department_id,$search)
    {
        $consent_forms = $this->db->select("a.consent_form_id, a.consent_form_title, b.department_id")
        ->from("consent_form a")
        ->join("consent_form_department b", "a.consent_form_id = b.consent_form_id")
        ->where("b.department_id='" . $department_id. "'")
        ->where("a.consent_form_title LIKE '".urldecode($search)."%' LIMIT 20")
        ->get()->result();

        if(count($consent_forms)>0)
        {
            $i=0;
            foreach($consent_forms as $value)
            {
                $data['consent_form'][$i]['consent_form_id'] = $value->consent_form_id;
                $data['consent_form'][$i]['consent_form_title'] =  $value->consent_form_title;
                $i++;
            }
            $this->response(array('code'=>'200','message'=>'Consent Form Data','result'=>$data));
        }
        else{
            $data['consent_form'] ="No data Found";
            $this->response(array('code'=>'201','message'=>'No Data Found','result'=>$data));
        }
    }

    public function add_consent_form_post()
    {
        extract($_POST);
        if(!empty($consent_form_id))
        {
            $check = $this->db->query("select * from webpatients_consent_form where appointment_id='".$appointment_id."' and consent_form_id='".$consent_form_id."' ")->row();
            if(count($check)>0)
            {
                $this->response(array('code'=>'201','message'=>'Already Added To List','result'=>'Already Added To List'));
            }
            else
            {
                $appInfo = $this->db->query("select * from appointments where appointment_id='".$appointment_id."'")->row();
              
                $standard_consent_form = $this->db->select("*")
                ->from("consent_form")
                ->where("consent_form_id ='" . $consent_form_id . "'")
                ->get()->row();

                $data['consent_form_title'] = $standard_consent_form->consent_form_title;
                $data['consent_form_id'] = $consent_form_id;
                $data['patient_id'] = $appInfo->patient_id;
                $data['doctor_id'] = $appInfo->doctor_id;
                $data['appointment_id'] = $appointment_id;
                $data['clinic_id'] = $appInfo->clinic_id;
                $data['created_by'] = $appInfo->doctor_id;
                $data['modified_by'] = $appInfo->doctor_id;
                $this->Generic_model->insertData("webpatients_consent_form", $data);
                $this->response(array('code'=>'200','message'=>'success','result'=>'Consent Form Added Successfully'));
            }
            
        }
        else
        {
            $this->response(array('code'=>'201','messge'=>'Error Occured','result'=>'Access Denied'));
        }
    }

    public function consentform_saveforpatient_post()
    {
        extract($_POST);
    
        $checking_patient_consent_form = $this->db->query("select * from  webpatients_consent_form where 
        patient_id ='".$patient_id."' and  doctor_id = '".$doctor_id."' and
        appointment_id = '".$appointment_id."' and  
        consent_form_id = '".$consent_form_id."'")
        ->row();
    
        if(count($checking_patient_consent_form)>0){
            
            $param_1['patient_id'] = $patient_id;
            $param_1['doctor_id'] = $doctor_id;
            $param_1['appointment_id'] = $appointment_id;
            $param_1['consent_form_id'] = $consent_form_id;
            $param_1['clinic_id'] = $clinic_id;
            // $param_1['doctor_id'] = $doctor_id;
            $param_1['consent_form_title'] = $consent_form_title;
            $param_1['patient_consent_form_description'] = $patient_consent_form_description;
            $param_1['modified_by'] = $doctor_id;
            $param_1['created_by'] = $doctor_id;
            // $param_1['benefits'] = $benefits;
            // $param_1['complications'] = $complications;
            // $param_1['alternative'] = $alternative;
            // $param_1['anesthesia'] = $anesthesia;
            // $param_1['medical_conditions'] = $medical_conditions;
    
            $this->Generic_model->updateData("webpatients_consent_form",$param_1,array('patient_id'=>$patient_id,"doctor_id"=>$doctor_id,"appointment_id"=>$appointment_id,"consent_form_id"=>$consent_form_id));
            $this->response(array('code' => '200', 'message' => 'Patient Consent Form Updated sucessfully', 'result' => 'Success'));
       }else{
        // $param_1['patient_id'] = $patient_id;
        // $param_1['doctor_id'] = $doctor_id;
        // $param_1['appointment_id'] = $appointment_id;
        // $param_1['consent_form_id'] = $consent_form_id;
        // $param_1['clinic_id'] = $clinic_id;
        // // $param_1['doctor_id'] = $doctor_id;
        // $param_1['consent_form_title'] = $consent_form_title;
        // $param_1['patient_consent_form_description'] = $patient_consent_form_description;
        // $param_1['modified_by'] = $doctor_id;
        // $param_1['created_by'] = $doctor_id;
        // // $param_1['benefits'] = $benefits;
        // // $param_1['complications'] = $complications;
        // // $param_1['alternative'] = $alternative;
        // // $param_1['anesthesia'] = $anesthesia;
        // // $param_1['medical_conditions'] = $medical_conditions;
        // $this->Generic_model->insertData("webpatients_consent_form", $param_1);
        // // $this->Generic_model->insertData("webpatients_consent_form",$param_1,array('patient_id'=>$patient_id,"doctor_id"=>$doctor_id,"appointment_id"=>$appointment_id,"consent_form_id"=>$consent_form_id));
        // $this->response(array('code' => '200', 'message' => 'Patient Consent Form saved sucessfully', 'result' => 'Success'));
        $this->response(array('code'=>'201','message'=>'No Data Found','result'=>'No Data Found'));
       }
    }

    public function consentform_saveastemplate_post()
    {
        extract($_POST);
    
        $checking_doctor_consent_form = $this->db->query("select * from  doctor_consent_form where 
        patient_id ='".$patient_id."' and  doctor_id = '".$doctor_id."' and
        appointment_id = '".$appointment_id."' and  
        consent_form_id = '".$consent_form_id."'")
        ->row();
    
        if(count($checking_doctor_consent_form)>0){
            
            $param_1['patient_id'] = $patient_id;
            $param_1['doctor_id'] = $doctor_id;
            $param_1['appointment_id'] = $appointment_id;
            $param_1['consent_form_id'] = $consent_form_id;
            $param_1['clinic_id'] = $clinic_id;
            // $param_1['doctor_id'] = $doctor_id;
            $param_1['consent_form_title'] = $consent_form_title;
            $param_1['doctor_consent_form_description'] = $doctor_consent_form_description;
            // $param_1['benefits'] = $benefits;
            // $param_1['complications'] = $complications;
            // $param_1['alternative'] = $alternative;
            // $param_1['anesthesia'] = $anesthesia;
            // $param_1['medical_conditions'] = $medical_conditions;
    
            $this->Generic_model->updateData("doctor_consent_form",$param_1,array('patient_id'=>$patient_id,"doctor_id"=>$doctor_id,"appointment_id"=>$appointment_id,"consent_form_id"=>$consent_form_id));
            $this->response(array('code' => '200', 'message' => 'Doctor Consent Form saved sucessfully', 'result' => 'Success'));
       }else{
        $param_1['patient_id'] = $patient_id;
        $param_1['doctor_id'] = $doctor_id;
        $param_1['appointment_id'] = $appointment_id;
        $param_1['consent_form_id'] = $consent_form_id;
        $param_1['clinic_id'] = $clinic_id;
        // $param_1['doctor_id'] = $doctor_id;
        $param_1['consent_form_title'] = $consent_form_title;
        $param_1['doctor_consent_form_description'] = $doctor_consent_form_description;
        // $param_1['benefits'] = $benefits;
        // $param_1['complications'] = $complications;
        // $param_1['alternative'] = $alternative;
        // $param_1['anesthesia'] = $anesthesia;
        // $param_1['medical_conditions'] = $medical_conditions;

        $this->Generic_model->insertData("doctor_consent_form",$param_1);
        $this->response(array('code' => '200', 'message' => 'Doctor Consent Form saved sucessfully', 'result' => 'Success'));
       }
    }

    public function consentform_getDescriptionData_get($appointment_id,$consent_form_id)
    {
        extract($_POST);
    
        $appInfo = $this->db->query("select * from appointments where appointment_id='".$appointment_id."'")->row();


        $checking_patient_consent_form = $this->db->query("select * from  webpatients_consent_form where 
        patient_id ='".$appInfo->patient_id."' and  doctor_id = '".$appInfo->doctor_id."' and
        appointment_id = '".$appointment_id."' and  
        consent_form_id = '".$consent_form_id."'")
        ->row();

        if(count($checking_patient_consent_form) > 0 && $checking_patient_consent_form->patient_consent_form_description != 'null')
        {
            $param_1['web_patient_consent_form_id'] = $checking_patient_consent_form->web_patient_consent_form_id;
            $param_1['consent_form_id'] = $consent_form_id;
            $param_1['consent_form_title'] = $checking_patient_consent_form->consent_form_title;
            $param_1['patient_consent_form_description'] = $checking_patient_consent_form->patient_consent_form_description;
            $param_1['status'] = '1';
    
            $this->response(array('code' => '200', 'message' => 'Description data', 'result' => $param_1));

        }
        else{
            $checking_doctor_consent_form = $this->db->query("select * from  doctor_consent_form where 
             doctor_id = '".$appInfo->doctor_id."'  and  clinic_id='".$appInfo->clinic_id."' and
             consent_form_id = '".$consent_form_id."'")
            ->row();

            
            if(count($checking_doctor_consent_form)>0)
            {
                $param_1['clinic_id'] = $appInfo->clinic_id;
                $param_1['consent_form_title'] = $checking_doctor_consent_form->consent_form_title;
                $param_1['doctor_consent_form_description'] = $checking_doctor_consent_form->doctor_consent_form_description;
                $param_1['status'] = '2';
                $this->response(array('code' => '200', 'message' => 'Description data', 'result' => $param_1));
            }
            else{ 
                $standard_consent_form = $this->db->select("*")
                ->from("consent_form")
                ->where("consent_form_id ='".$consent_form_id . "'")
                ->get()->row();
                $param_1['web_patient_consent_form_id'] = $checking_patient_consent_form->web_patient_consent_form_id;
                $param_1['consent_form_id'] = $consent_form_id;
                $param_1['consent_form_title'] = $standard_consent_form->consent_form_title;
                $param_1['brief'] = $standard_consent_form->brief;
                $param_1['benefits'] = $standard_consent_form->benefits;
                $param_1['complications'] = $standard_consent_form->complications;
                $param_1['alternative'] = $standard_consent_form->alternative;
                $param_1['anesthesia'] = $standard_consent_form->anesthesia;
                $param_1['medical_conditions'] = $standard_consent_form->medical_conditions;
                $param_1['status'] = '0';
                $this->response(array('code' => '200', 'message' => 'Description data', 'result' => $param_1));
        
                }
        }
    }

    public function consentform_List_get($appointment_id)
    {
        extract($_POST);
    
        $appInfo = $this->db->query("select * from appointments where appointment_id='".$appointment_id."'")->row();


        $getList = $this->db->query("select * from  webpatients_consent_form where 
        appointment_id = '".$appointment_id."'")
        ->result();

        if(count($getList)>0){
            $i=0;
            foreach($getList as $list)
            {
            $param_1['list'][$i]['web_patient_consent_form_id'] = $list->web_patient_consent_form_id;
            $param_1['list'][$i]['patient_id'] = $appInfo->patient_id;
            $param_1['list'][$i]['doctor_id'] = $appInfo->doctor_id;
            $param_1['list'][$i]['appointment_id'] = $appointment_id;
            $param_1['list'][$i]['consent_form_id'] = $list->consent_form_id;
            $param_1['list'][$i]['clinic_id'] = $appInfo->clinic_id;
            $param_1['list'][$i]['doctor_id'] = $appInfo->doctor_id;
            $param_1['list'][$i]['consent_form_title'] = $list->consent_form_title;
            $i++;
            }
            $this->response(array('code' => '200', 'message' => 'Description data', 'result' => $param_1));
       }else{
        $this->response(array('code' => '201', 'message' => 'No Appointment Id Found', 'result' => 'Verify Id'));
       }
    }


}
?>