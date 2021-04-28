<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Procedure extends MY_Controller {
public function __construct() 
{
    parent::__construct();
}
public function index(){
	$clinic_id = $this->session->userdata('clinic_id');
	$cond = '';
	if($clinic_id!=0)
		$cond = "clinic_id=".$clinic_id;
    $data['procedure_list']=$this->Generic_model->getAllRecords('medical_procedures',$cond,$order='');
	$data['view'] = 'procedures/procedure_list';
    $this->load->view('layout', $data);
}
public function add(){
    if($this->input->post('submit')){

    	$data['medical_procedure']=$this->input->post('procedure_name');
        $data['clinic_id']=$this->session->userdata('clinic_id');
        //$data['department_id']='';
    	$data['status']=1;
        $data['procedure_description']=$this->input->post('procedure_description');
    	$data['created_by']=$this->session->userdata('user_id');
    	$data['modified_by']=$this->session->userdata('user_id');
		$data['created_date_time']=date('Y-m-d H:i:s');
		$data['modified_date_time']=date('Y-m-d H:i:s');
        $html=$this->load->view('procedures/generate_pdf',$data,true);
     $pdfFilePath = strtolower(str_replace(" ","_",$this->input->post('procedure_name'))).".pdf";
     $data['file_name'] = $pdfFilePath;

     $this->load->library('M_pdf');
      $this->m_pdf->pdf->WriteHTML($html);

  //download it.
  $this->m_pdf->pdf->Output("./uploads/procedures/".$pdfFilePath, "F");  
	 	$this->Generic_model->insertData('medical_procedures',$data);
	 	redirect('procedure');
	}else{
        $data['clinic_list']=$this->Generic_model->getAllRecords('clinics',$condition='',$order='');
        $data['department_list']=$this->Generic_model->getAllRecords('department',$condition='',$order='');
		$data['view'] = 'procedures/procedure_add';
		$this->load->view('layout',$data);
    }
    	
 }

 public function update($id=''){
        if(count($this->input->post()) > 0){
            $param_1['procedure_description'] = $this->input->post("description");
            $param_1['modified_by'] = $user_id;
            $param_1['modified_date_time'] = date("Y-m-d");
            $this->Generic_model->updateData("medical_procedures",$param_1,array('medical_procedure_id'=>$id));
            redirect("procedure");
       
    }
    $data['procedure_info'] = $this->Generic_model->getSingleRecord('medical_procedures', array('medical_procedure_id' => $id));
    $data['view'] = 'procedures/procedure_edit';
		$this->load->view('layout',$data);
 
 }

}