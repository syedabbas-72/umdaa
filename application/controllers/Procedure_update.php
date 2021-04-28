<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Procedure_update extends CI_Controller {



    public function view($param,$doctor_id ="",$user_id,$clinic_id){
        $data['doctor_id'] = $doctor_id;
        $data['medical_procedure_id'] = $param;
        $data['user_id'] = $user_id;
        $data['clinic_id'] = $clinic_id;
       $checking_doctor_procedure = $this->db->query("select * from doctor_medical_procedures where doctor_id='".$data['doctor_id']."' and  medical_procedure_id ='".$param."'")->row();
       if(!empty($checking_doctor_procedure)){
         $data['procedure_description'] = $checking_doctor_procedure->medical_procedure;
       }else{
         /*$data['procedure'] = $this->Generic_model->getSingleRecord('medical_procedures',array('medical_procedure_id'=>$param),$order='');*/

         $standard_procedure = $this->db->query("select * from medical_procedures where medical_procedure_id ='".$param."'")->row();
         $data['procedure_description'] = $standard_procedure->procedure_description;
       }


       
       $this->load->view("procedures/procedure_update",$data);
    }
    public function update(){

       $doctor_id = $this->input->post("doctor_id");
       $clinic_id = $this->input->post("clinic_id");
       $user_id = $this->input->post("user_id");
       $medical_procedure_id = $this->input->post("medical_procedure_id");
       $data['procedure_description'] = $this->input->post('description');
       $checking_doctor_procedure = $this->db->query("select * from doctor_medical_procedures where doctor_id ='".$doctor_id."' and medical_procedure_id = '".$medical_procedure_id."'")->row();
        if(count($checking_doctor_procedure) >0){
            $param_1['medical_procedure'] = $this->input->post("description");
            $param_1['modified_by'] = $user_id;
            $param_1['modified_date_time'] = date("Y-m-d");
            $this->Generic_model->updateData("doctor_medical_procedures",$param_1,array('doctor_medical_procedure_id'=>$checking_doctor_procedure->doctor_medical_procedure_id));
        }else{
            $param_2['doctor_id'] = $doctor_id;
            $param_2['clinic_id'] = $clinic_id;
            $param_2['medical_procedure_id'] = $medical_procedure_id;
            $param_2['medical_procedure'] = $this->input->post("description");
            $param_2['status'] = "Active";
            $param_2['created_by'] = $user_id;
            $param_2['modified_by'] = $user_id;
            $param_2['created_date_time'] = date("Y-m-d");
            $param_2['modified_date_time'] = date("Y-m-d");
         $this->Generic_model->insertData('doctor_medical_procedures',$param_2);
        }
        $url_parameters = $medical_procedure_id."/".$doctor_id."/".$user_id."/".$clinic_id;
      
      redirect("procedure_update/view/".$url_parameters);
    }

    public function patient_producer($patient_id,$doctor_id,$appointment_id,$medical_procedure_id,$clinic_id){

    $data['patient_id'] = $patient_id;
    $data['doctor_id'] = $doctor_id;
    $data['appointment_id']  =$appointment_id;
    $data['medical_procedure_id'] = $medical_procedure_id;
    $data['clinic_id'] = $clinic_id;

    $checking_patient_procedure = $this->db->query("select * from patient_procedure where patient_id ='".$patient_id."' and  doctor_id = '".$doctor_id."' and appointment_id = '".$appointment_id."' and  medical_procedure_id = '".$medical_procedure_id."'")->row();
    // echo $this->db->last_query();
    if($checking_patient_procedure){
        if($checking_patient_procedure->medical_procedure == ""){
            $check_doctor_procedure = $this->db->query("select * from  doctor_medical_procedures where doctor_id ='".$doctor_id."' and medical_procedure_id ='".$medical_procedure_id."' and clinic_id ='".$clinic_id."'")->row();
            // echo $this->db->last_query();
            if($check_doctor_procedure){
                $data['procedure_description'] = $check_doctor_procedure->medical_procedure;
            }else{

                $standard_procedure = $this->db->query("select * from medical_procedures where medical_procedure_id ='".$medical_procedure_id."'")->row();
                // echo $this->db->last_query();
                $data['procedure_description'] = $standard_procedure->procedure_description;
            }
        }
        else{
            $data['procedure_description'] = $checking_patient_procedure->medical_procedure;
        }
    }else{

        $check_doctor_procedure = $this->db->query("select * from  doctor_medical_procedures where doctor_id ='".$doctor_id."' and medical_procedure_id ='".$medical_procedure_id."' and clinic_id ='".$clinic_id."'")->row();
        // echo $this->db->last_query();
        if($check_doctor_procedure){
            $data['procedure_description'] = $check_doctor_procedure->medical_procedure;
        }else{

            $standard_procedure = $this->db->query("select * from medical_procedures where medical_procedure_id ='".$medical_procedure_id."'")->row();
            // echo $this->db->last_query();
            $data['procedure_description'] = $standard_procedure->procedure_description;
        }
        
    }
    $this->load->view("procedures/patient_procedure_update",$data);
    // $this->response(array('code' => '200', 'message' => 'Patient Procedure', 'result' => $data, 'requestname' => $method));

 }
 public function patient_producer_list($patient_id,$doctor_id,$appointment_id,$medical_procedure_id,$clinic_id){

    $data['patient_id'] = $patient_id;
    $data['doctor_id'] = $doctor_id;
    $data['appointment_id']  =$appointment_id;
    $data['medical_procedure_id'] = $medical_procedure_id;
    $data['clinic_id'] = $clinic_id;
    $checking_patient_procedure = $this->db->query("select * from patient_procedure where patient_procedure_id = '".$medical_procedure_id."'")->row();
	//echo $this->db->last_query();
    if(!empty($checking_patient_procedure)){
        $data['procedure_description'] = $checking_patient_procedure->medical_procedure;
    }else{

        $check_doctor_procedure = $this->db->query("select * from  doctor_medical_procedures where doctor_id ='".$doctor_id."' and medical_procedure_id ='".$medical_procedure_id."' and clinic_id ='".$clinic_id."'")->row();
        if(!empty($check_doctor_procedure)){
            $data['procedure_description'] = $check_doctor_procedure->medical_procedure;
        }else{

            $standard_procedure = $this->db->query("select * from medical_procedures where medical_procedure_id ='".$medical_procedure_id."'")->row();
            $data['procedure_description'] = $standard_procedure->procedure_description;
        }
        
    }

    // print_r($data);
    // exit;

    $this->load->view("procedures/patient_procedure_view",$data);

 }

 public function patient_procedure_update(){
    // print_r($this->input->post());
    // exit;
    $patient_id = $this->input->post("patient_id");
    $doctor_id = $this->input->post("doctor_id");
    $appointment_id = $this->input->post("appointment_id");
    $medical_procedure_id = $this->input->post("medical_procedure_id");
    $clinic_id = $this->input->post("clinic_id");
    $medical_procedure = $this->input->post("description");
    $type = $this->input->post("submit");

   $checking_patient_procedure = $this->db->query("select * from patient_procedure where patient_id ='".$patient_id."' and  doctor_id = '".$doctor_id."' and appointment_id = '".$appointment_id."' and  medical_procedure_id = '".$medical_procedure_id."'")->row();
//    echo $this->db->last_query();
//    exit;
    if(count($checking_patient_procedure)>0){
        
        $param_1['patient_id'] = $patient_id;
        $param_1['doctor_id'] = $doctor_id;
        $param_1['appointment_id'] = $appointment_id;
        $param_1['medical_procedure_id'] = $medical_procedure_id;
        if($type == "Save As Template")
        {
            $check = $this->db->query("select * from doctor_medical_procedures where doctor_id='".$doctor_id."' and clinic_id='".$clinic_id."' and medical_procedure_id='".$medical_procedure_id."' order by doctor_medical_procedure_id DESC")->row();
            if(count($check) > 0){
                $para3['medical_procedure'] = $medical_procedure;
                $this->Generic_model->updateData("doctor_medical_procedures",$para3, array('doctor_medical_procedure_id'=>$check->doctor_medical_procedure_id));
            }
            else{
                $para3['clinic_id'] = $clinic_id;
                $para3['doctor_id'] = $doctor_id;
                $para3['medical_procedure_id'] = $medical_procedure_id;
                $para3['medical_procedure'] = $medical_procedure;
                $this->Generic_model->insertData("doctor_medical_procedures",$para3);
            }
        }
        elseif($type == "Save For This Patient")
        {
        	$param_1['template'] = 0;
        }
        //$param_1['clinic_id'] = $clinic_id;
        $param_1['medical_procedure'] = $medical_procedure;

        $ok = $this->Generic_model->updateData("patient_procedure",$param_1,array('patient_id'=>$patient_id,"doctor_id"=>$doctor_id,"appointment_id"=>$appointment_id,"medical_procedure_id"=>$medical_procedure_id));

    }else{

        $param_2['patient_id'] = $patient_id;
        $param_2['doctor_id'] = $doctor_id;
        $param_2['appointment_id'] = $appointment_id;
        $param_2['medical_procedure_id'] = $medical_procedure_id;
        if($type == "Save As Template")
        {
            $para3['clinic_id'] = $clinic_id;
            $para3['doctor_id'] = $doctor_id;
            $para3['medical_procedure_id'] = $medical_procedure_id;
            $para3['medical_procedure'] = $medical_procedure;
            $this->Generic_model->insertData("doctor_medical_procedures",$para3);
        }
        elseif($type == "Save For This Patient")
        {
        	$param_2['template'] = 0;
        }
        //$param_2['clinic_id'] = $clinic_id;
        $param_2['medical_procedure'] = $medical_procedure;
        $ok = $this->Generic_model->insertData('patient_procedure',$param_2);
        
    }
    if($ok == 1){
        $url_parameters = $patient_id."/".$doctor_id."/".$appointment_id."/".$medical_procedure_id."/".$clinic_id;
        redirect("procedure_update/patient_producer/".$url_parameters);
        //redirect("patient_producer")
    }


 }


}
