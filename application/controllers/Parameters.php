<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Parameters extends MY_Controller {
public function __construct() 
{
    parent::__construct();
}
 public function index(){
 	$data['parameters_list'] = $this->db->query("select * from  parameters where archieve != 1")->result();
 	$data['view'] = 'parameters/parameters_list';
 	$this->load->view('layout', $data);
 }

 public function add(){
 	$param = $this->input->post();
 	$user_id = $this->session->has_userdata('user_id');
 	if(count($param) >0) {
 		
 		$param_1['Parameter_name'] = $this->input->post("name");
 		$param_1['parameter_type'] = $this->input->post("type");
 		$param_1['low_range'] = $this->input->post("low_range");
 		$param_1['high_range'] = $this->input->post("high_range");
 		$param_1['created_by'] = $user_id;
 		$param_1['modified_by'] = $user_id;
 		$param_1['created_date_time'] = date("Y-m-d H:i:s");
 		$param_1['modified_date_time'] = date("Y-m-d H:i:s");
 		$ok = $this->Generic_model->insertData('parameters',$param_1);
 		if($ok ==1){
 			redirect('parameters');
 		}else{
 			redirect('parameters');
 		}
 	}else{
	 	
	 	$data['view'] = 'parameters/parameters_insert';
	 	$this->load->view('layout', $data);
	}
 }

 public function  edit($id){
 	$param = $this->input->post();
 	$user_id = $this->session->has_userdata('user_id');
 	if(count($param)>0){
 		
 		$param_1['parameter_name'] = $this->input->post("name");
 		$param_1['parameter_type'] = $this->input->post("type");
 		$param_1['modified_by'] = $user_id;
 		$param_1['modified_date_time'] = date("Y-m-d H:i:s");
 		$ok = $this->Generic_model->updateData('parameters', $param_1, array('parameter_id'=>$id));
 		if($ok ==1){
 			redirect('parameters');
 		}else{
 			redirect('parameters');
 		}
 	}else{
 		$data['parameter_val'] = $this->db->query("select * from parameters where parameter_id = '".$id."'")->row();
	 	
	 	$data['view'] = 'parameters/parameters_edit';
	 	$this->load->view('layout', $data);
 	}
 	
 }

 public function delete($id){
	 	$user_id = $this->session->has_userdata('user_id');
	 	$param_1['archieve'] = "1";
	 	$param_1['modified_by'] = $user_id;
		$param_1['modified_date_time'] = date("Y-m-d H:i:s");
		$ok = $this->Generic_model->updateData('parameters', $param_1, array('parameter_id'=>$id));
		if($ok ==1){
			redirect('parameters');
		}else{
			redirect('parameters');
		}
 	}
  
 public function consentform_pdf(){
 	$this->load->library('M_pdf');
 	$html = $this->load->view('Consentform/consentform_pdf',$data,true);
 	$pdfFilePath = "consentform.pdf";
 	$this->m_pdf->pdf->WriteHTML($html);
 	$this->m_pdf->pdf->Output("./uploads/consentforms/".$pdfFilePath, "D");
 	//$this->load->view('Consentform/consentform_pdf');
 }
}
?>