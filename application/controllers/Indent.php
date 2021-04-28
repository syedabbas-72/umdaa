<?php

error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
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
  
  $data['indent_list'] = $this->db->query("select *,count(pharmacy_indent_line_item_id) as licnt from pharmacy_indent pi inner join pharmacy_indent_line_items pil on(pi.pharmacy_indent_id = pil.pharmacy_indent_id) ".$cond." group by pil.pharmacy_indent_id order by pi.pharmacy_indent_id DESC")->result();
  
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
    $cond = '';
    if ($clinic_id != 0)
        $cond = "and b.clinic_id=" . $clinic_id;
	$user_id = $this->session->userdata('user_id');
	$trade_names = $this->db->query("select trade_name,formulation from drug")->result_array();
	$data['pinfo'] = $this->db->query("select *,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.archieve=0 " . $cond . " and expiry_date > CURRENT_DATE group by b.drug_id,b.batch_no")->result();

    $pi = 0;

    foreach ($data['pinfo'] as $result) {
        $disinfo = $this->db->query("select * from clinic_pharmacy_inventory where clinic_id=" . $clinic_id . " and drug_id=" . $result->drug_id)->row();
        $outward = $this->db->query("select sum(quantity) as ouqty from clinic_pharmacy_inventory_outward where drug_id=" . $result->drug_id . " and batch_no='" . $result->batch_no . "' and clinic_id=" . $result->clinic_id)->row();

        $sch_salt = array();

        if ($result->salt_id == "" || $result->salt_id == NULL) {
            $scheduled_salt = "";
        } else {
            $imp = "'" . implode("','", explode(",", $result->salt_id)) . "'";
            $salt_id = $this->db->query("SELECT GROUP_CONCAT(scheduled_salt) scheduled_salt FROM `salt` where salt_id in ($imp)")->row();
            $scheduled_salt = trim($salt_id->scheduled_salt, ",");
            $explode = explode(",", $scheduled_salt);
            foreach ($explode as $key => $svalue) {
                $sch_salt[] = "<span id=" . trim($svalue) . ">" . trim($svalue) . "</span>";
            }
        }
        if(($result->oqty - $outward->ouqty) <= 0)
            $qty = 0;
        else
            $qty = $result->oqty - $outward->ouqty;
        $data['parinfo'][$pi]['clinic_pharmacy_inventory_inward_id'] = $result->clinic_pharmacy_inventory_inward_id;
        $data['parinfo'][$pi]['trade_name'] = $result->trade_name . " " . implode(" ", $sch_salt);
        $data['parinfo'][$pi]['drug_id'] = $result->drug_id;
        $data['parinfo'][$pi]['formulation'] = $result->formulation;
        $data['parinfo'][$pi]['composition'] = $result->composition;
        $data['parinfo'][$pi]['batch_no'] = $result->batch_no;
        $data['parinfo'][$pi]['oqty'] = $qty;
        $data['parinfo'][$pi]['mrp'] = round($result->mrp, 2);
        $data['parinfo'][$pi]['reorder_level'] = $disinfo->reorder_level;
        $data['parinfo'][$pi]['hsn_code'] = $result->hsn_code;
        $data['parinfo'][$pi]['igst'] = $disinfo->igst;
        $data['parinfo'][$pi]['cgst'] = $disinfo->cgst;
        $data['parinfo'][$pi]['sgst'] = $disinfo->sgst;
        $data['parinfo'][$pi]['disc'] = $disinfo->max_discount_percentage;
        $data['parinfo'][$pi]['vendor_id'] = $disinfo->vendor_id;
        $data['parinfo'][$pi]['pack_size'] = $result->pack_size;
        $data['parinfo'][$pi]['expiry_date'] = $result->expiry_date;
        $pi++;
    }
        $data['vendor_list'] = $this->db->query("select * from vendor_master where clinic_id='" . $clinic_id . "'")->result();
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
		echo count($ind_cnt);exit;
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
	$data['drug_master_json_file'] = $this->Generic_model->getFieldValue('master_version', 'json_file_name', array('master_name' => 'drug'));
	

    $this->load->view('layout', $data);
}
}
?>