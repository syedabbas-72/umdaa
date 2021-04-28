<?php

error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Indent extends MY_Controller {
public function __construct() 
{
    parent::__construct();
     $this->load->model("Ajx_model"); 
}
public function index(){
   
  $clinic_id = $this->session->userdata('clinic_id');
	$cond = '';
	if($clinic_id!=0)
		$cond = "where pi.clinic_id=".$clinic_id;
  //$data['indent_list'] = $this->db->query("select * from pharmacy_indent pi inner join pharmacy_indent_line_items pil on(pi.pharmacy_indent_id = pil.pharmacy_indent_id) group by pi.pharmacy_indent_id")->result();
  
  $data['indent_list'] = $this->db->query("select *,count(pharmacy_indent_line_item_id) as licnt from pharmacy_indent pi inner join pharmacy_indent_line_items pil on(pi.pharmacy_indent_id = pil.pharmacy_indent_id) ".$cond." group by pil.pharmacy_indent_id")->result();
  
	$data['view'] = 'indent/indent_list';
    $this->load->view('layout', $data);
}
public function indent_view($id)
{
	$data['indent_info'] = $this->db->query("select * from pharmacy_indent_line_items a inner join drug b on a.drug_id=b.drug_id where pharmacy_indent_id=".$id)->result();
	$data['view'] = 'indent/indent_view';
    $this->load->view('layout', $data);
}
public function indent_add()
{
	$clinic_id = $this->session->userdata('clinic_id');
	$user_id = $this->session->userdata('user_id');
	$trade_names = $this->db->query("select trade_name,formulation from drug")->result_array();
	// $data['tname'] = '';
	// foreach($trade_names as $tresult)
	// {
	// 	if($tresult['trade_name']!=''){
	// 	if($data['tname']=='')
	// 		$data['tname'] = $data['tname'].'"'.$tresult['formulation'].' '.$tresult['trade_name'].'"';
	// 	else
	// 		$data['tname'] = $data['tname'].',"'.$tresult['formulation'].' '.$tresult['trade_name'].'"';
	// 	}
	// }
	$param =$this->input->post();
    if(count($param)>0){
		$ind_cnt = $this->db->query("select * from pharmacy_indent where indent_no like '%IND-".$clinic_id."-%'")->result_array();
		//echo count($ind_cnt);exit;
		$icnt = (count($ind_cnt)+1);
		$indentinfo['indent_no'] = 'IND-'.$clinic_id."-".$icnt;
		$indentinfo['user_id'] = $user_id;
		$indentinfo['clinic_id'] = $clinic_id;
		$indentinfo['status'] = 1;
		$indentinfo['indent_date'] = date("Y-m-d");
		$indentinfo['created_by'] = $user_id;
		$indentinfo['modified_by'] = $user_id;
		$indentinfo['created_date_time'] = date("Y-m-d H:i:s");
		$indentinfo['modified_date_time'] = date("Y-m-d H:i:s");
		$last_inserted_id = $this->Generic_model->insertDataReturnId("pharmacy_indent",$indentinfo);
		$drug_ids = count($_POST['drgid']);
		for($i=0;$i<$drug_ids;$i++){
			$lineinfo['pharmacy_indent_id'] = $last_inserted_id;
			$lineinfo['drug_id'] = $_POST['drgid'][$i];
			$lineinfo['quantity'] = $_POST['rqty'][$i];
			$lineinfo['status'] = 1;
			$lineinfo['created_by'] = $user_id;
			$lineinfo['modified_by'] = $user_id;
			$lineinfo['created_date_time'] = date("Y-m-d H:i:s");
			$lineinfo['modified_date_time'] = date("Y-m-d H:i:s");
			$this->Generic_model->insertDataReturnId("pharmacy_indent_line_items",$lineinfo);			
		}
		redirect('Indent');
	}
	$data['view'] = 'indent/indent_add';
	

    $this->load->view('layout', $data);
}
}
?>