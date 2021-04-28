<?php


defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
ini_set('memory_limit', '-1');

require APPPATH . '/libraries/REST_Controller1.php';

class Procedures extends REST_Controller1
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

    //search procedures 
    public function index_get($docId='',$search='')
    {
        $docInfo = $this->db->query("select * from doctors where doctor_id='".$docId."'")->row();
        $procedures = $this->db->query("select * from medical_procedures md,procedure_department pd where pd.medical_procedure_id=md.medical_procedure_id and pd.department_id='".$docInfo->department_id."' and md.medical_procedure LIKE '%".urldecode($search)."%' order by md.medical_procedure ASC LIMIT 20")->result();
        
        if(count($procedures)>0)
        {   
            $i = 0;
            foreach($procedures as $value)
            {
                $data['procedures'][$i]['medical_procedure'] = $value->medical_procedure;
                $data['procedures'][$i]['medical_procedure_id'] = $value->medical_procedure_id;
                $i++;
            }
            $this->response(array('code' => '200', 'message' => 'success', 'result' => $data));
        }
        else
        {
            $data['procedures'] = [];
            $this->response(array('code' => '201', 'message' => 'failed', 'result' => $data));
        }
    }

    // Patient Procedures List
    public function proceduresList_get($appointment_id='')
    {
        $check = $this->db->query("select * from appointments where appointment_id='".$appointment_id."'")->row();
        if(count($check)>0)
        {
            $docInfo = $this->db->query("select de.department_name from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$check->doctor_id."'")->row();
            $proceduresInfo = $this->db->query("select * from patient_procedure ppd,medical_procedures mp where mp.medical_procedure_id=ppd.medical_procedure_id and ppd.appointment_id='".$appointment_id."'")->result();
            if(count($proceduresInfo)>0)
            {
                $i = 0;
                foreach($proceduresInfo as $value)
                {
                    $data['procedure_list'][$i]['medical_procedure_id'] = $value->medical_procedure_id;
                    $data['procedure_list'][$i]['patient_procedure_id'] = $value->patient_procedure_id;
                    $data['procedure_list'][$i]['procedure_name'] = $value->medical_procedure;
                    $data['procedure_list'][$i]['department_name'] = $docInfo->department_name;
                    $data['procedure_list'][$i]['procedure_url'] = base_url('procedure_update/patient_producer_list/').$check->patient_id."/".$check->doctor_id."/".$appointment_id."/".$value->patient_procedure_id."/".$check->clinic_id;
                    $data['procedure_list'][$i]['pdf_file'] = base_url('uploads/procedures/').$value->file_name;
                    $i++;
                }
            }
            else
            {
                $data['procedures'] = [];
            }
        }
        else
        {
            $data['procedures'] = [];
        }
        $this->response(array('code'=>'200','message'=>'success','result'=>$data));
    }

    // add Procedure to patient
    public function AddProcedure_post()
    {
        extract($_POST);
        if(!empty($procedure_id))
        {
            $check = $this->db->query("select * from patient_procedure where appointment_id='".$appointment_id."' and medical_procedure_id='".$procedure_id."'")->row();
            if(count($check)>0)
            {
                $this->response(array('code'=>'201','message'=>'Error Occured','result'=>'Already Added To List'));
            }
            else
            {
                // $procedureInfo = 
                $appInfo = $this->db->query("select * from appointments where appointment_id='".$appointment_id."'")->row();
                $check_doctor_procedure = $this->db->select("*")->from("doctor_medical_procedures")->where("doctor_id ='" . $appInfo->doctor_id . "' and medical_procedure_id ='" . $procedure_id . "' and clinic_id ='" . $appInfo->clinic_id . "'")->get()->row();
                if (count($check_doctor_procedure) > 0) {
                    $data['medical_procedure'] = "<html><body style='padding:0px; margin:0px;'>" . $check_doctor_procedure->medical_procedure . "/body></html>";
                } else {
                    $standard_procedure = $this->db->select("*")->from("medical_procedures")->where("medical_procedure_id ='" . $procedure_id . "'")->get()->row();
                    $data['medical_procedure'] = "<html><body>" . $standard_procedure->procedure_description . "/body></html>";
                }
                $data['medical_procedure_id'] = $procedure_id;
                $data['patient_id'] = $appInfo->patient_id;
                $data['doctor_id'] = $appInfo->doctor_id;
                $data['appointment_id'] = $appointment_id;
                $data['clinic_id'] = $appInfo->clinic_id;
                $this->Generic_model->insertData("patient_procedure", $data);
                $this->response(array('code'=>'200','message'=>'success','result'=>'Procedure Added Successfully'));
            }
            
        }
        else
        {
            $this->response(array('code'=>'201','messge'=>'Error Occured','result'=>'Access Denied'));
        }
    }

    // update procedure which already added.
    public function updateProcedure_post()
    {
        extract($_POST);
        // surgeon
        // anesthetist
        // assisting_surgeon
        // type_of_anesthesia
        // assisting_nurse
        // preoperative_diagnosis
        // postoperative_diagnosis
        // indication
        // position
        
        if(isset($_POST))
        {
            $data['surgeon'] = $surgeon;
            $data['anesthetist'] = $anesthetist;
            $data['assisting_surgeon'] = $assisting_surgeon;
            $data['type_of_anesthesia'] = $type_of_anesthesia;
            $data['assisting_nurse'] = $assisting_nurse;
            $data['preoperative_diagnosis'] = $preoperative_diagnosis;
            $data['postoperative_diagnosis'] = $postoperative_diagnosis;
            $data['indication'] = $indication;
            $data['position'] = $position;
            $this->Generic_model->updateData("patient_procedure", $data, array('patient_procedure_id'=>$patient_procedure_id));
            $this->response(array('code'=>'200','message'=>'success','result'=>'Procedure Saved'));
        }
        else
        {
            $this->response(array('code'=>'201','message'=>'Error Occurred','result'=>'Error Occurred'));
        }

    }

    // Delete Procedure for Patient
    public function delProcedure_get($procedure_id)
    {
        $check = $this->db->query("select * from patient_procedure where patient_procedure_id='".$procedure_id."'")->row();
        if(count($check)>0)
        {
            $this->Generic_model->deleteRecord("patient_procedure", array('patient_procedure_id'=>$procedure_id));
            $this->response(array('code'=>'200','message'=>'Success','result'=>'Deleted Successfully'));
        }
        else
        {
            $this->response(array('code'=>'201','message'=>'Error Occured','result'=>'Procedure Not Exists. Please Go Back And Come Again'));
        }
    }

}
?>