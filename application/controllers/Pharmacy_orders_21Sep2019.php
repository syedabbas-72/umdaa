<?php
error_reporting(0);

defined('BASEPATH') OR exit('No direct script access allowed');

class Pharmacy_orders extends MY_Controller {

public function __construct() {

    parent::__construct();

    $this->load->library('mail_send', array('mailtype'=>'html'));		 

$this->form_validation->set_error_delimiters('<div class="error">', '</div>');	

}

public function index(){
	$c_date = date('Y-m-d');
	$lt_date = date("Y-m-d",strtotime("+3 month"));
	$clinic_id = $this->session->userdata('clinic_id');
	$cond = '';
	if($clinic_id!=0)
		$cond = "and b.clinic_id=".$clinic_id;
	if($clinic_id==0)
	{
		$expired = $this->db->query("select *,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.archieve=0 and b.expiry_date<'".$c_date."' group by b.drug_id,b.batch_no")->result_array();
		$sexpired = $this->db->query("select *,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.archieve=0 and b.expiry_date<'".$lt_date."' group by b.drug_id,b.batch_no order by b.expiry_date ASC")->result_array();
		$shortage = $this->db->query("select *,sum(quantity) as oqty from clinic_pharmacy_inventory_inward a inner join drug b on a.drug_id=b.drug_id where a.status=1 and a.archieve=0 group by a.drug_id")->result_array();
	}
	else{
		$expired = $this->db->query("select *,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.archieve=0 and b.expiry_date<'".$c_date."' and b.clinic_id=".$clinic_id." group by b.drug_id,b.batch_no")->result_array();

		$sexpired = $this->db->query("select *,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.archieve=0 and b.expiry_date<'".$lt_date."' and b.clinic_id=".$clinic_id." group by b.drug_id,b.batch_no order by b.expiry_date ASC")->result_array();

		$shortage = $this->db->query("select *,sum(quantity) as oqty from clinic_pharmacy_inventory_inward a inner join drug b on a.drug_id=b.drug_id where a.status=1 and a.clinic_id=".$clinic_id." and a.archieve=0 and (a.expiry_date<'".$c_date."' or a.expiry_date>'".$c_date."')  group by a.drug_id order by a.expiry_date ASC")->result_array();
	}
	$data['expired']=array();$data['sexpired']=array();$data['shortage']=array();$ei=0;$sei=0;$shi=0;
	
	foreach($expired as $eresult)
	{
		$data['expired'][$ei]['drug_name'] = $eresult['trade_name'];
		$data['expired'][$ei]['batch_no'] = $eresult['batch_no'];
		if($clinic_id==0)
		{
			$outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=".$eresult['drug_id']." and batch_no='".$eresult['batch_no']."'")->row();
		}
		else{
			$outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=".$eresult['drug_id']." and batch_no='".$eresult['batch_no']."' and clinic_id=".$clinic_id)->row();
		}
		$data['expired'][$ei]['quantity'] = ($eresult['oqty']-$outqnt->qty);
		$data['expired'][$ei]['edate'] = $eresult['expiry_date'];		
		$ei++;
	}
	foreach($sexpired as $seresult)
	{
		$data['sexpired'][$sei]['drug_name'] = $seresult['trade_name'];
		$data['sexpired'][$sei]['batch_no'] = $seresult['batch_no'];
		if($clinic_id==0)
		{
			$outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=".$seresult['drug_id']." and batch_no='".$seresult['batch_no']."'")->row();
		}
		else{
			$outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=".$seresult['drug_id']." and batch_no='".$seresult['batch_no']."' and clinic_id=".$clinic_id)->row();
		}
		
		$data['sexpired'][$sei]['quantity'] = ($seresult['oqty']-$outqnt->qty);	
		$data['sexpired'][$sei]['edate'] = $seresult['expiry_date'];
		$sei++;
	}
	foreach($shortage as $ssresult)
	{
		if($clinic_id==0)
		{
			$outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=".$ssresult['drug_id'])->row();
			$shqty = $this->db->query("select * from clinic_pharmacy_inventory where drug_id=".$ssresult['drug_id'])->row();
		}
		else{
			$outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=".$ssresult['drug_id']." and clinic_id=".$clinic_id)->row();
			$shqty = $this->db->query("select * from clinic_pharmacy_inventory where clinic_id=".$clinic_id." and drug_id=".$ssresult['drug_id'])->row();
		}
		
		$actual_qty = ($ssresult['oqty']-$outqnt->qty);
		
		// echo $ssresult['drug_id']."-".$ssresult['oqty']."-".$outqnt->qty."-".$actual_qty."-".$ssresult['batch_no']."<br />";
		if(($actual_qty<=$shqty->reorder_level) && ($shqty->indent_status==0))
		{
			// echo $ssresult['drug_id']."-".$ssresult['oqty']."-".$outqnt->qty."-".$actual_qty."-".$ssresult['batch_no']."<br />";
			$data['shortage'][$shi]['drug_id'] = $ssresult['drug_id'];
			$data['shortage'][$shi]['drug_name'] = $ssresult['trade_name'];
			$data['shortage'][$shi]['batch_no'] = $ssresult['batch_no'];
			$data['shortage'][$shi]['quantity'] = $actual_qty;
			$data['shortage'][$shi]['edate'] = $ssresult['expiry_date'];
		}
			$shi++;
	}

	
	$data['pinfo'] = $this->db->query("select *,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.archieve=0 ".$cond." and expiry_date > CURRENT_DATE group by b.drug_id,b.batch_no")->result();
	
	$pi=0;

	foreach($data['pinfo'] as $result)
	{
		$disinfo = $this->db->query("select * from clinic_pharmacy_inventory where clinic_id=".$clinic_id." and drug_id=".$result->drug_id)->row();
		$outward = $this->db->query("select sum(quantity) as ouqty from clinic_pharmacy_inventory_outward where drug_id=".$result->drug_id." and batch_no='".$result->batch_no."' and clinic_id=".$result->clinic_id)->row();
		
		$sch_salt = array();
		
		if($result->salt_id == "" || $result->salt_id == NULL){
			$scheduled_salt = "";
		}else{
			$imp =  "'".implode("','", explode(",", $result->salt_id))."'";
			$salt_id = $this->db->query("SELECT GROUP_CONCAT(scheduled_salt) scheduled_salt FROM `salt` where salt_id in ($imp)")->row();
            $scheduled_salt = trim($salt_id->scheduled_salt,",");
            $explode = explode(",",$scheduled_salt);
            foreach ($explode as $key => $svalue) {
            	$sch_salt[] = "<span id=".trim($svalue).">".trim($svalue)."</span>";
            }
		}

		$data['parinfo'][$pi]['clinic_pharmacy_inventory_inward_id'] = $result->clinic_pharmacy_inventory_inward_id;
		$data['parinfo'][$pi]['trade_name'] = $result->trade_name." ".implode(" ",$sch_salt);
		$data['parinfo'][$pi]['drug_id'] = $result->drug_id;
		$data['parinfo'][$pi]['formulation'] = $result->formulation;
		$data['parinfo'][$pi]['composition'] = $result->composition;
		$data['parinfo'][$pi]['batch_no'] = $result->batch_no;
		$data['parinfo'][$pi]['oqty'] = ($result->oqty-$outward->ouqty);
		$data['parinfo'][$pi]['mrp'] = round($result->mrp,2);
		$data['parinfo'][$pi]['reorder_level'] = $disinfo->reorder_level;
		$data['parinfo'][$pi]['hsn_code'] = $result->hsn_code;
		$data['parinfo'][$pi]['igst'] = $disinfo->igst;
		$data['parinfo'][$pi]['cgst'] = $disinfo->cgst;
		$data['parinfo'][$pi]['sgst'] = $disinfo->sgst;
		$data['parinfo'][$pi]['disc'] = $disinfo->max_discount_percentage;
		$data['parinfo'][$pi]['pack_size'] = $result->pack_size;
		$data['parinfo'][$pi]['expiry_date'] = $result->expiry_date;
		$pi++;
	}

	$data['vendor_list'] = $this->db->query("select * from vendor_master where clinic_id='".$clinic_id."'")->result();

	$data['view'] = 'Pharmacy_orders/Pharmacy_orders';
	
    $this->load->view('layout', $data);

}

public function delete_order($cinvid)
{
	//$this->db->query("delete from clinic_pharmacy_inventory_inward where clinic_pharmacy_inventory_inward_id=".$cinvid);
	$this->db->query("update clinic_pharmacy_inventory_inward set archieve=1 where clinic_pharmacy_inventory_inward_id=".$cinvid);
	
	redirect('Pharmacy_orders');
}

public function edit_order($did,$bno)
{
	$clinic_id = $this->session->userdata('clinic_id');
	$cond = '';
	if($clinic_id!=0)
		$cond = "and b.clinic_id=".$clinic_id;
	$data['info'] = $this->db->query("select * from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id and b.drug_id=".$did." and b.archieve=0 and b.batch_no='".$bno."' ".$cond)->result_array();
	//echo $this->db->last_query();
	$data['discount'] = $this->db->query("select * from clinic_pharmacy_inventory b where drug_id=".$did." ".$cond)->row();
	
	$data['view'] = 'Pharmacy_orders/edit_order';
	

    $this->load->view('layout', $data);
}

//Vendor Master
public function Pharmacy_vendors(){
	$clinic_id = $this->session->userdata("clinic_id");

	$data['vendor_list'] = $this->db->query("select * from vendor_master where clinic_id='".$clinic_id."'")->result();

	$data['view'] = 'Pharmacy_orders/vendor_list';
    $this->load->view('layout', $data);

}

//Vendor Add
public function addVendor(){
	$clinic_id = $this->session->userdata("clinic_id");

	$data['vendor_storeName'] = $this->input->post("storeName");
	$data['vendor_name'] = $this->input->post("vendorName");
	$data['vendor_mobile'] = $this->input->post("mobile");
	$data['vendor_email'] = $this->input->post("email");
	$data['vendor_address'] = $this->input->post("address");
	$data['vendor_location'] = $this->input->post("location");
	$data['clinic_id'] = $clinic_id;
	$this->Generic_model->insertData('vendor_master',$data);
	redirect("Pharmacy_orders/Pharmacy_vendors?asuccess");

}

//Edit Vendor
public function editVendor(){
	$clinic_id = $this->session->userdata("clinic_id");

	$vendor_id = $this->input->post("vendor_id");
	$data['vendor_storeName'] = $this->input->post("storeName");
	$data['vendor_name'] = $this->input->post("vendorName");
	$data['vendor_mobile'] = $this->input->post("mobile");
	$data['vendor_email'] = $this->input->post("email");
	$data['vendor_address'] = $this->input->post("address");
	$data['vendor_location'] = $this->input->post("location");
	$this->Generic_model->updateData('vendor_master',$data,array('vendor_id'=>$vendor_id));
	redirect("Pharmacy_orders/Pharmacy_vendors?usuccess");

}

//Delete Vendor
public function deleteVendor($id){
	$vendor_id = $id;
	$this->Generic_model->deleteRecord('vendor_master',array('vendor_id'=>$vendor_id));
	redirect("Pharmacy_orders/Pharmacy_vendors?dsuccess");
}

public function pharmacy_edit()
{
	date_default_timezone_set('Asia/Kolkata');
	$user_id = $this->session->userdata('user_id');
	$clinic_id = $this->session->userdata('clinic_id');
	$iinner_count = count($_POST['iid']);
	$igst=0;$cgst=0;$sgst=0;$rlevel=0;$mdiscount=0;$cid=0;$did=0;
	//echo "<pre>";print_r($_POST);exit;
	for($i=0;$i<$iinner_count;$i++){
		$replace_date_slash_with_hyphen = str_replace("/", "-", trim($_POST['expiry_date'][$i]));
		$iinfo['batch_no'] = $_POST['batch_no'][$i];
		$iinfo['quantity'] = $_POST['quantity'][$i];
		$iinfo['mrp'] = $_POST['mrp'][$i];
		
		$iinfo['pack_size'] = $_POST['pack_size'][$i];
		$iinfo['expiry_date'] = date("Y-m-d",strtotime($replace_date_slash_with_hyphen));
		$iinfo['modified_by'] = $user_id;		
		$iinfo['modified_date_time'] = date("Y-m-d H:i:s");
		$igst = $_POST['igst'][$i];
		$cgst = $_POST['cgst'][$i];
		$sgst = $_POST['sgst'][$i];
		$rlevel = $_POST['reorder_level'][$i];
		$mdiscount = $_POST['max_discount_percentage'][$i];
		$cid = $_POST['clinic_id'][$i];
		$did = $_POST['drug_id'][$i];

		$hsn['hsn_code'] = $_POST['hsn'][$i];
		$this->Generic_model->updateData("clinic_pharmacy_inventory_inward",$iinfo,array('	clinic_pharmacy_inventory_inward_id'=>$_POST['iid'][$i]));
		$this->Generic_model->updateData("drug",$hsn,array('drug_id'=>$did));
	}
	$this->db->query("update clinic_pharmacy_inventory set reorder_level=".$rlevel.",igst='".$igst."',cgst='".$cgst."',sgst='".$sgst."',max_discount_percentage='".$mdiscount."' where clinic_id=".$cid." and drug_id=".$did);
	//echo $this->db->last_query();
	redirect('Pharmacy_orders');
}
public function get_dashboard_details()
{
	$cid = $_POST['c_id'];
	$did = $_POST['d_id'];
    $start = $_POST['startDate'];
    $start = date('Y-m-d', strtotime($start));

    $end = $_POST['endDate'];
    $end = date('Y-m-d', strtotime($end));

    if($start == $end){
    	$regCond = "created_date_time LIKE '%".$start."%'";
    	$conCond = "b.created_date_time LIKE '%".$start."%' and item_information='Consultation'";
    }else{
    	$regCond = "created_date_time between '".$start."%' and '".$end."%'";
    	$conCond = "(b.created_date_time between '".$start."%' and '".$end."%') and item_information='Consultation'";
    }

	$tdate = date('Y-m-d');

	if($did=='all')
	{
		$registrations = $this->db->query("select count(patient_id) as pcnt from patients where ".$regCond)->row();
		$consultations = $this->db->query("select sum(amount) as camt from billing_line_items a inner join billing b on a.billing_id=b.billing_id where clinic_id=".$cid." and ".$conCond)->row();
	}
	else{
		$registrations = $this->db->query("select count(patient_id) as pcnt from patients where ".$regCond)->row();
		$consultations = $this->db->query("select sum(amount) as camt from billing_line_items a inner join billing b on a.billing_id=b.billing_id where clinic_id=".$cid." and doctor_id=".$did." and ".$conCond)->row();
	}


	echo '<table cellspacing="0" cellpadding="0" class="table finances">
			<tr>
				<td class="noBdr btmBdr"><span class="amt consultationsAmount">'.($consultations->camt!=''?$consultations->camt:0).'</span><br />CONSULTATIONS</td>
  			</tr>
			<tr>
				<td id="reg_td" class="noBdr btmBdr"><span class="amt registrationsData">'.($registrations->pcnt*100).'</span><br />REGISTRATIONS</td>
  			</tr>
  			<tr>
  				<td class="noBdr btmBdr"><span class="amt proceduresData">0</span><br />PROCEDURES</td>
			</tr>
			<tr>
  				<td class="noBdr"><span class="amt investigationData">0</span><br />INVESTIGATION</td>
  			</tr>
		</table>';
}


public function pharmacy_dashboard()
{
	$clinic_id = $this->session->userdata('clinic_id');

	$cond = '';

	if($clinic_id != 0)
		$cond = "where clinic_id=".$clinic_id;
	
	$tdate = date('Y-m-d');

	if($this->session->userdata('role_id') == 4){
        $data['doctors_list'] = $this->Generic_model->getAllRecords('clinic_doctor', array('clinic_id' => $clinic_id,'doctor_id'=>$this->session->userdata('user_id')), $order = '');    
    }else{
        $this->db->select('distinct(doctor_id)');
        $this->db->from('clinic_doctor');

        if($clinic_id != 0){
            $this->db->where("clinic_id = ",$clinic_id);
        }

        $data['doctors_list'] = $this->db->get()->result();
    }

	$patients = $this->db->query("select * from patient_prescription a inner join patients c on a.patient_id=c.patient_id inner join doctors d on a.doctor_id=d.doctor_id where a.created_date_time like '%".$tdate."%' group by a.patient_id")->result();

	/*
	// Get the list of patient prescriptions
	$patientPrescriptions = $this->db->select('PP.patient_id, PP.patient_prescription_id, PP.doctor_id, PP.clinic_id, PP.appointment_id')->from('patient_prescription as PP')->where('PP.clinic_id =',$clinic_id)->like('PP.created_date_time', $tdate)->group_by('PP.patient_id','ASC')->get()->result_array();

	if(count($patientPrescriptions) > 0){
		// Get the drugs and quantities prescribed
		foreach($patientPrescriptions as $prescription){
			$drugsRecords = $this->db->select('drug_id')
		}
	}

	echo '<pre>';
	print_r($patientPrescriptions);
	echo '</pre>';
	exit();

	echo $this->db->last_query();
	*/
	
	$i=0; $expected_revenue = 0; $tcamount = 0;

	foreach($patients as $presult)
	{
		$data['patients'][$i]['pname'] = $presult->first_name." ". $presult->last_name;
		$data['patients'][$i]['pid'] = $presult->patient_id;
		$data['patients'][$i]['age'] = $presult->age;
		$data['patients'][$i]['gender'] = $presult->gender;
		$data['patients'][$i]['pdid'] = $presult->patient_prescription_id;
		$data['patients'][$i]['dcstatus'] = $presult->dc_status;
		$data['patients'][$i]['doctor'] = $presult->salutation." ".$presult->first_name." ".$presult->last_name;

		$pdrugs = $this->db->query("select * from patient_prescription_drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where a.patient_prescription_id=".$presult->patient_prescription_id." and clinic_id=".$clinic_id." group by b.drug_id")->result();

		foreach($pdrugs as $pdresult)
		{
			$mrp = ($pdresult->mrp/$pdresult->pack_size);
			$expected_revenue = $expected_revenue+($pdresult->quantity*$mrp);
		}

		$camount = $this->db->query("select sum(amount) as camt from billing a inner join billing_line_items b on a.billing_id=b.billing_id where a.patient_prescription_id=".$presult->patient_prescription_id)->row();
		
		if(count($camount)>0){
			$tcamount = $tcamount+$camount->camt;
		}else{
			$tcamount = $tcamount+0;
		}

		$i++;
	}

	$data['erevenue'] = $expected_revenue;
	$data['crevenue'] = $tcamount;
	
	$data['drug'] = $this->db->query("select sum(amount) as oamt from billing a inner join billing_line_items b on a.billing_id=b.billing_id where a.created_date_time like '".$tdate."%' and (item_information!='Consultation' and item_information!='Registration' and item_information is Not NULL) and patient_prescription_id=0")->row();
	$data['view'] = 'Pharmacy_orders/pharmacy_dashboard';

    $this->load->view('layout', $data);
}


public function getFinances(){

	$clinic_id = $this->session->userdata('clinic_id');
	$start = $_POST['startDate'];
	$end = $_POST['endDate'];

	if($this->session->userdata('role_id') == 4){
        $data['doctors_list'] = $this->Generic_model->getAllRecords('clinic_doctor', array('clinic_id' => $clinic_id,'doctor_id'=>$this->session->userdata('user_id')), $order = '');    
    }else{
        $this->db->select('distinct(doctor_id)');
        $this->db->from('clinic_doctor');
        if($clinic_id != 0)
            $this->db->where("clinic_id = ",$clinic_id);
        $data['doctors_list'] = $this->db->get()->result();
    }
    if($d_id=="all")
    {
    	if($start==$end)
	    {
	    	$patients = $this->db->query("select * from patient_prescription a inner join patients c on a.patient_id=c.patient_id inner join doctors d on a.doctor_id=d.doctor_id where a.created_date_time like '".$start."%' group by a.patient_id")->result();
	    }
	    else
	    {
	    	$patients = $this->db->query("select * from patient_prescription a inner join patients c on a.patient_id=c.patient_id inner join doctors d on a.doctor_id=d.doctor_id where a.created_date_time between '".$start."%' and '".$end."%' group by a.patient_id")->result();
	    }
    }
    else
    {
    	if($start==$end)
	    {
	    	$patients = $this->db->query("select * from patient_prescription a inner join patients c on a.patient_id=c.patient_id inner join doctors d on a.doctor_id=d.doctor_id where a.created_date_time like '".$start."%' and a.doctor_id='".$d_id."' group by a.patient_id")->result();
	    }
	    else
	    {
	    	$patients = $this->db->query("select * from patient_prescription a inner join patients c on a.patient_id=c.patient_id inner join doctors d on a.doctor_id=d.doctor_id where a.doctor_id='".$d_id."' and (a.created_date_time between '".$start."%' and '".$end."%') group by a.patient_id")->result();
	    }
    }
	
	//echo $this->db->last_query();
	$i=0;$expected_revenue = 0;$tcamount = 0;

	foreach($patients as $presult)
	{
		$data['patients'][$i]['pname'] = $presult->first_name." ". $presult->last_name;
		$data['patients'][$i]['pid'] = $presult->patient_id;
		$data['patients'][$i]['age'] = $presult->age;
		$data['patients'][$i]['gender'] = $presult->gender;
		$data['patients'][$i]['pdid'] = $presult->patient_prescription_id;
		$data['patients'][$i]['dcstatus'] = $presult->dc_status;
		$data['patients'][$i]['doctor'] = $presult->salutation." ".$presult->first_name." ".$presult->last_name;

		$pdrugs = $this->db->query("select * from patient_prescription_drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where a.patient_prescription_id=".$presult->patient_prescription_id." and clinic_id=".$clinic_id." group by b.drug_id")->result();
	
		foreach($pdrugs as $pdresult)
		{
			$mrp = ($pdresult->mrp/$pdresult->pack_size);
			$expected_revenue = $expected_revenue+($pdresult->quantity*$mrp);
		}
	
		$camount = $this->db->query("select sum(amount) as camt from billing a inner join billing_line_items b on a.billing_id=b.billing_id where a.patient_prescription_id=".$presult->patient_prescription_id)->row();
	
		if(count($camount)>0){
			$tcamount = $tcamount+$camount->camt;
		}
		else
			$tcamount = $tcamount+0;
	
		$i++;
	}
	$erevenue = $expected_revenue;
	$crevenue = $tcamount;
	
	if($d_id=="all")
	{
		if($start==$end)
		{
			$drug = $this->db->query("select sum(amount) as oamt from billing a inner join billing_line_items b on a.billing_id=b.billing_id where a.created_date_time like '".$start."%' and (item_information!='Consultation' and item_information!='Registration' and item_information is Not NULL) and patient_prescription_id=0")->row();
		}
		else
		{
			$drug = $this->db->query("select sum(amount) as oamt from billing a inner join billing_line_items b on a.billing_id=b.billing_id where (a.created_date_time between '".$start."%' and '".$end."%') and (item_information!='Consultation' and item_information!='Registration' and item_information is Not NULL) and patient_prescription_id=0")->row();
		}
	}
	else
	{
		if($start==$end)
		{
			$drug = $this->db->query("select sum(amount) as oamt from billing a inner join billing_line_items b on a.billing_id=b.billing_id where a.created_date_time like '".$start."%' and a.doctor_id='".$doctor_id."' and (item_information!='Consultation' and item_information!='Registration' and item_information is Not NULL) and patient_prescription_id=0")->row();
		}
		else
		{
			$drug = $this->db->query("select sum(amount) as oamt from billing a inner join billing_line_items b on a.billing_id=b.billing_id where (a.created_date_time between '".$start."%' and '".$end."%') and a.doctor_id='".$doctor_id."' and (item_information!='Consultation' and item_information!='Registration' and item_information is Not NULL) and patient_prescription_id=0")->row();
		}	
	}

	echo round($erevenue,2)."*".round($crevenue,2)."*".round($drug->oamt,2)."*".round($erevenue-$crevenue,2);

}	

public function search_pharmacy()
{
	$pharmacy_main = $_POST['search_pharmacy'];
	//echo $pharmacy_main;exit;
	$data['pharmacy'] = $pharmacy_main;
	//$pharmacy = substr($pharmacy, strpos($pharmacy_main, " ")+1);
	$pharmacy = explode(' ', $pharmacy_main);
	//echo $pharmacy[1];
	$c_date = date('Y-m-d');
	$lt_date = date("Y-m-d",strtotime("+3 month"));
	$clinic_id = $this->session->userdata('clinic_id');
	$cond = '';
	if($clinic_id!=0)
		$cond = "and b.clinic_id=".$clinic_id;
	if($clinic_id==0)
	{
		$expired = $this->db->query("select *,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.archieve=0 and b.expiry_date<'".$c_date."' group by b.drug_id,b.batch_no")->result_array();
		$sexpired = $this->db->query("select *,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.archieve=0 and b.expiry_date<'".$lt_date."' group by b.drug_id,b.batch_no")->result_array();
		$shortage = $this->db->query("select *,sum(quantity) as oqty from clinic_pharmacy_inventory_inward a inner join drug b on a.drug_id=b.drug_id where a.status=1 and a.archieve=0 group by a.drug_id,a.batch_no")->result_array();
		//echo $this->db->last_query();
	}
	else{
		$expired = $this->db->query("select *,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.expiry_date<'".$c_date."' and b.clinic_id=".$clinic_id." and b.archieve=0 group by b.drug_id,b.batch_no")->result_array();
		$sexpired = $this->db->query("select *,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.expiry_date<'".$lt_date."' and b.clinic_id=".$clinic_id." and b.archieve=0 group by b.drug_id,b.batch_no")->result_array();
		$shortage = $this->db->query("select *,sum(quantity) as oqty from clinic_pharmacy_inventory_inward a inner join drug b on a.drug_id=b.drug_id where a.status=1 and a.archieve=0 and clinic_id=".$clinic_id." group by a.drug_id,a.batch_no")->result_array();
	}
	$data['expired']=array();$data['sexpired']=array();$data['shortage']=array();$ei=0;$sei=0;$shi=0;
	foreach($expired as $eresult)
	{
		$data['expired'][$ei]['drug_name'] = $eresult['trade_name'];
		$data['expired'][$ei]['batch_no'] = $eresult['batch_no'];
		if($clinic_id==0)
		{
			$outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=".$eresult['drug_id']." and batch_no='".$eresult['batch_no']."'")->row();
		}
		else{
			$outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=".$eresult['drug_id']." and batch_no='".$eresult['batch_no']."' and clinic_id=".$clinic_id)->row();
		}
		$data['expired'][$ei]['quantity'] = ($eresult['oqty']-$outqnt->qty);
		$data['expired'][$ei]['edate'] = $eresult['expiry_date'];		
		$ei++;
	}
	foreach($sexpired as $seresult)
	{
		$data['sexpired'][$sei]['drug_name'] = $seresult['trade_name'];
		$data['sexpired'][$sei]['batch_no'] = $seresult['batch_no'];
		if($clinic_id==0)
		{
			$outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=".$seresult['drug_id']." and batch_no='".$seresult['batch_no']."'")->row();
		}
		else{
			$outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=".$seresult['drug_id']." and batch_no='".$seresult['batch_no']."' and clinic_id=".$clinic_id)->row();
		}
		
		$data['sexpired'][$sei]['quantity'] = ($seresult['oqty']-$outqnt->qty);	
		$data['sexpired'][$sei]['edate'] = $seresult['expiry_date'];
		$sei++;
	}
	$drug_info = $this->db->query("SELECT * FROM drug where trade_name like '".$pharmacy[1]."%'")->row();
	//echo $this->db->last_query();exit;
	if(count($drug_info)>0){
		if($clinic_id==0)
		{
			$inventray = $this->db->query("select *,sum(quantity) as oqty from clinic_pharmacy_inventory_inward where drug_id=".$drug_info->drug_id." and archieve=0 group by drug_id,batch_no")->result_array();
		}
		else{
			$inventray = $this->db->query("select *,sum(quantity) as oqty from clinic_pharmacy_inventory_inward where drug_id=".$drug_info->drug_id." and archieve=0 and clinic_id=".$clinic_id." group by drug_id,batch_no")->result_array();
		}
	
	$data['presult']=array();$pi=0;
	foreach($inventray as $result)
	{
		$data['presult'][$pi]['drug_name'] = $drug_info->trade_name;
		$data['presult'][$pi]['batch_no']  = $result['batch_no'];
		if($clinic_id==0)
		{
			$outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=".$result['drug_id']." and batch_no='".$result['batch_no']."'")->row();
		}
		else{
			$outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=".$result['drug_id']." and batch_no='".$result['batch_no']."' and clinic_id=".$clinic_id)->row();
		}
		
		$data['presult'][$pi]['quantity']  = ($result['oqty']-$outqnt->qty);
		$data['presult'][$pi]['edate']  = $result['expiry_date'];
		$pi++;
	}
	}
	else
	{
		$data['presult']=array();
	}
	foreach($shortage as $ssresult)
	{
		if($clinic_id==0)
		{
			$outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=".$ssresult['drug_id']." and batch_no='".$ssresult['batch_no']."'")->row();
			$shqty = $this->db->query("select * from clinic_pharmacy_inventory where drug_id=".$ssresult['drug_id'])->row();
		}
		else{
			$outqnt = $this->db->query("select sum(quantity) as qty from clinic_pharmacy_inventory_outward where drug_id=".$ssresult['drug_id']." and batch_no='".$ssresult['batch_no']."' and clinic_id=".$clinic_id)->row();
			$shqty = $this->db->query("select * from clinic_pharmacy_inventory where clinic_id=".$clinic_id." and drug_id=".$ssresult['drug_id'])->row();
		}		
		$actual_qty = ($ssresult['oqty']-$outqnt->qty);		
		if($actual_qty<=$shqty->reorder_level)
		{
			$data['shortage'][$shi]['drug_name'] = $ssresult['trade_name'];
			$data['shortage'][$shi]['batch_no'] = $ssresult['batch_no'];
			$data['shortage'][$shi]['quantity'] = $actual_qty;
			$data['shortage'][$shi]['edate'] = $ssresult['expiry_date'];
			$shi++;
		}
	}
	$trade_names = $this->db->query("select trade_name,formulation from drug")->result_array();
	$data['tname'] = '';
	foreach($trade_names as $tresult)
	{
		if($tresult['trade_name']!=''){
		if($data['tname']=='')
			$data['tname'] = $data['tname'].'"'.$tresult['formulation'].' '.$tresult['trade_name'].'"';
		else
			$data['tname'] = $data['tname'].',"'.$tresult['formulation'].' '.$tresult['trade_name'].'"';
		}
	}
	$data['pinfo'] = $this->db->query("select *,sum(quantity) as oqty from drug a inner join clinic_pharmacy_inventory_inward b on a.drug_id=b.drug_id where b.status=1 and b.archieve=0 ".$cond." group by b.drug_id,b.batch_no")->result();
	$data['view'] = 'Pharmacy_orders/Pharmacy_orders';

    $this->load->view('layout', $data);
}

public function pharmacy_add()
{
	$clinic_id = $this->session->userdata('clinic_id');
	
	$user_id = $this->session->userdata('user_id');
	//$trade_names = $this->db->query("select trade_name,formulation from drug")->result_array();
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
		echo "<pre>"; print_r($_POST); echo "</pre>";
		$drug_ids = count($_POST['drgid']);
		for($i=0;$i<$drug_ids;$i++){
			$replace_date_slash_with_hyphen = str_replace("/", "-", trim($_POST['expiredate'][$i]));
			$lineinfo['drug_id'] = $_POST['drgid'][$i];
			$lineinfo['clinic_id'] = $clinic_id;
			$lineinfo['batch_no'] = preg_replace('/\s+/', '', $_POST['batchno'][$i]);
			$lineinfo['quantity'] = $_POST['qty'][$i];			
			$lineinfo['mrp'] = $_POST['mrp'][$i];
			$lineinfo['pack_size'] = $_POST['pack_size'][$i];
			// $lineinfo['expiry_date'] = date("Y-m-d",strtotime($replace_date_slash_with_hyphen));
			$expiryDate = "01";
			$expiryMonth = $_POST['expiryMonth'][$i];
			$expiryYear = $_POST['expiryYear'][$i];
			$lineinfo['expiry_date'] = $expiryYear."-".sprintf('%02d', $expiryMonth)."-".sprintf('%02d', $expiryDate);
			$lineinfo['supplied_date'] = date('Y-m-d');
			$lineinfo['status'] = 1;
			$lineinfo['created_by'] = $user_id;
			$lineinfo['modified_by'] = $user_id;
			$lineinfo['created_date_time'] = date("Y-m-d H:i:s");
			$lineinfo['modified_date_time'] = date("Y-m-d H:i:s");

			echo '<pre>';
			print_r($lineinfo);
			echo '</pre>';
			// exit();

			$this->Generic_model->insertDataReturnId("clinic_pharmacy_inventory_inward",$lineinfo);
			$this->db->query("update clinic_pharmacy_inventory set status=0 where drug_id=".$_POST['drgid'][$i]." and clinic_id=".$clinic_id);
			
			$ilineinfo['reorder_level'] = $_POST['rlevel'][$i];
			$ilineinfo['igst'] = $_POST['igst'][$i];
			$ilineinfo['cgst'] = $_POST['cgst'][$i];
			$ilineinfo['sgst'] = $_POST['sgst'][$i];
			$ilineinfo['vendor_id'] = $_POST['vendor'][$i];
			$ilineinfo['max_discount_percentage'] = $_POST['disc'][$i];
			$ilineinfo['status'] = 1;			
			$ilineinfo['modified_by'] = $user_id;			
			$ilineinfo['modified_date_time'] = date("Y-m-d H:i:s");

			$icheck = $this->db->query("select * from clinic_pharmacy_inventory where clinic_id=".$clinic_id." and drug_id=".$_POST['drgid'][$i])->row();
			
			if(count($icheck)>0){
				$this->Generic_model->updateData("clinic_pharmacy_inventory",$ilineinfo,array('clinic_id'=>$clinic_id,'drug_id'=>$icheck->drug_id));
			}else{
				$ilineinfo['clinic_id'] = $clinic_id;
				$ilineinfo['drug_id'] = $_POST['drgid'][$i];
				$ilineinfo['created_by'] = $user_id;$ilineinfo['created_date_time'] = date("Y-m-d H:i:s");
				$this->Generic_model->insertDataReturnId("clinic_pharmacy_inventory",$ilineinfo);
			}
		}
		$this->clinic_inventory_json();
		
	}
	$data['view'] = 'Pharmacy_orders/new_orders';
    $this->load->view('layout', $data);
}


//creating inventory json w.r.t to clinic
	public function clinic_inventory_json(){
		$clinic_id = $this->session->userdata('clinic_id');
		$master_version= $this->db->query("select * from master_version where clinic_id='".$clinic_id."' and master_name='clinic_inventory'")->row();
		if(sizeof($master_version)==0)
		{
			$json_file_name = $clinic_id.'_clinic_inventory_v1.json';
			$data['clinic_id'] = $clinic_id;
			$data['master_name'] = 'clinic_inventory';
			$data['version_code'] = '1';
			$data['json_file_name'] = $json_file_name;
			$this->Generic_model->insertData('master_version',$data);
		}
		else
		{
			unlink($master_version->json_file_name);
			$version_code = $master_version->version_code+1;
			$json_file_name = $clinic_id."_clinic_inventory_v".$version_code.".json";
			$data['clinic_id'] = $clinic_id;
			$data['master_name'] = 'clinic_inventory';
			$data['version_code'] = $version_code;
			$data['json_file_name'] = $json_file_name;
			$this->Generic_model->updateData("master_version",$data,array('master_version_id'=>$master_version->master_version_id));
		}

    	$drugs_list = $this->db->query("select CONCAT(d.formulation,' ',d.trade_name) as drug_name from drug d inner join clinic_pharmacy_inventory cp on(d.drug_id = cp.drug_id) where cp.clinic_id='".$clinic_id."'")->result(); 

		$prefix = '';
		$prefix .= '[';
		foreach($drugs_list as $row) {
			$prefix .= json_encode($row->drug_name);
			$prefix .= ',';
		}
		$prefix .= ']';

		$json_file = str_replace(",]","]",trim($prefix,","));

		$path_user = './uploads/clinic_inventory_json/'.$json_file_name;

		if (!file_exists($path_user)) {                   
			$fp = fopen('./uploads/clinic_inventory_json/'.$json_file_name, 'w');
			fwrite($fp, $json_file);
		} else {
			unlink($path_user);
			$fp = fopen('./uploads/clinic_inventory_json/'.$json_file_name, 'w');
			fwrite($fp, $json_file);
		}
		redirect('Pharmacy_orders');
	}
public function get_drug_info()
{
	$clinic_id = $this->session->userdata('clinic_id');
	$drg_name = $_POST['drug'];
	$dinfo = $this->db->query("select * from drug where trade_name like '".$drg_name."%'")->row();
	//echo $this->db->last_query();
	if(count($dinfo)>0)
	{
		$invinfo = $this->db->query("select * from clinic_pharmacy_inventory where clinic_id=".$clinic_id." and drug_id=".$dinfo->drug_id)->row();

		$drugInfo = array();

		$drugInfo['drug_id'] = $dinfo->drug_id;
		$drugInfo['formulation'] = $dinfo->formulation;
		$drugInfo['composition'] = $difno->composition;
		$drugInfo['reorder_level'] = $invinfo->reorder_level;
		$drugInfo['igst'] = $invinfo->igst;
		$drugInfo['cgst'] = $invInfo->cgst;
		$drugInfo['sgst'] = $invInfo->sgst;
		$drugInfo['discount'] = $invinfo->max_discount_percentage;

		// Get vendor information
		$vendorInfo = $this->db->select('vendor_id, vendor_name, vendor_storeName, vendor_location')->from('vendor_master')->where('clinic_id =',$clinic_id)->get()->result_array();

		if(count($vendorInfo) > 0){
			$drugInfo['vendor_list'] = $vendorInfo;
		}
		
		echo json_encode($drugInfo);
	}
	else
	{		
		echo '';
	}
}
function bulk_save(){
	$clinic_id = $this->session->userdata('clinic_id');
  $this->load->library('excel');

         if ($this->input->post('importfile')) {
            $path = './uploads/pharmacy_inventory_bulk/';
            $config['upload_path'] = './uploads/pharmacy_inventory_bulk/';
            $config['allowed_types'] = 'xlsx|xls|jpg|png';
            $config['remove_spaces'] = TRUE;
           
        //echo $_FILES['userfile']['name']=$_FILES['userfile']['name'];exit;
                  
             $this->load->library('upload');
            $this->upload->initialize($config);
             $this->upload->do_upload('userfile'); //uploading file to server
      $fileData=$this->upload->data('file_name');
      $inputFileName = $path . $fileData;
            
            if(file_exists($inputFileName)){
              $inputFileName = $path . $fileData;
            }
            else{
              move_uploaded_file($fileData,$path);
                $inputFileName = $path . $fileData;
            }
      
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME)
                        . '": ' . $e->getMessage());
            }
            $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
          
            $arrayCount = count($allDataInSheet);
 

            $flag = 0;
            $createArray = array('drug','batch_no','quantity','expiry_date');
            $makeArray = array('drug' =>'drug','batch_no' =>'batch_no','quantity' =>'quantity','expiry_date' =>'expiry_date');
            $SheetDataKey = array();
            foreach ($allDataInSheet as $dataInSheet) {
                foreach ($dataInSheet as $key => $value) {
                    if (in_array(trim($value), $createArray)) {
                        $value = preg_replace('/\s+/', '', $value);
                        $SheetDataKey[trim($value)] = $key;
                        
                    } 
                }
            }
             
            $data = array_diff_key($makeArray, $SheetDataKey);
            
            if (isset($data)) {
                 
                $flag = 1;
            }
            if ($flag == 1) {
              for ($i = 2; $i <= $arrayCount; $i++) {

                
                  // $addresses = array();
                  $drug = $SheetDataKey['drug'];
                  $batch_no = $SheetDataKey['batch_no'];
                  $quantity = $SheetDataKey['quantity'];
                  $expiry_date = $SheetDataKey['expiry_date'];

                  $d_drug = filter_var(trim($allDataInSheet[$i][$drug]), FILTER_SANITIZE_STRING);
                  $d_batch_no = filter_var(trim($allDataInSheet[$i][$batch_no]), FILTER_SANITIZE_STRING);
                  $d_quantity = filter_var(trim($allDataInSheet[$i][$quantity]), FILTER_SANITIZE_STRING);
                  $d_expiry_date = filter_var(trim($allDataInSheet[$i][$expiry_date]), FILTER_SANITIZE_STRING);
                
                   $checking_drug  = $this->db->query("select * from drug where trade_name like '".$d_drug."%'")->row();
                   $drug_id = $checking_drug->drug_id;
                   $clinic_id = $clinic_id;
                   $supplied_date = date("Y-m-d");

                   $fetchdata2 = array('drug_id'=>$drug_id, 'clinic_id'=>$clinic_id, 'batch_no'=> $d_batch_no, 'quantity'=>$d_quantity, 'supplied_date'=>$supplied_date, 'expiry_date'=>date("Y-m-d",strtotime($d_expiry_date)), 'status' => 1, 'created_by' => $this->session->userdata('user_id'), 'modified_by' => $this->session->userdata('user_id'), 'created_date_time' => date('Y-m-d H:i:s'), 'modified_date_time' => date('Y-m-d H:i:s'));
                        $this->Generic_model->insertData('clinic_pharmacy_inventory_inward', $fetchdata2);
                }
              }else{
                 echo "Please import correct file";
              }
              redirect('Pharmacy_orders');

            }
    }

/*
Get the drugs which match with the trade name
*/
public function searchDrugs(){
	
	$trade_name = $_POST['trade_name'];

	$this->db->select('drug_id, trade_name, formulation');
	$this->db->from('drug');
	$this->db->like('trade_name',$trade_name,'before');

	$drugs = $this->db->get()->result_array();

	echo $this->db->last_query();

	// if(count($drugs) > 0){
	// 	echo $drugs;	
	// }else{
	// 	echo 0;
	// }
	
}


function raise_shortage_indent()
{
	$clinic_id = $this->session->userdata('clinic_id');
	$user_id = $this->session->userdata('user_id');
	$ind_cnt = $this->db->query("select * from pharmacy_indent where indent_no like '%IND-".$clinic_id."-%'")->row();
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
		if($_POST['rqty'][$i]=="")
			continue;
		$lineinfo['pharmacy_indent_id'] = $last_inserted_id;
		$lineinfo['drug_id'] = $_POST['drgid'][$i];
		$lineinfo['quantity'] = $_POST['rqty'][$i];
		$lineinfo['status'] = 1;
		$lineinfo['created_by'] = $user_id;
		$lineinfo['modified_by'] = $user_id;
		$lineinfo['created_date_time'] = date("Y-m-d H:i:s");
		$lineinfo['modified_date_time'] = date("Y-m-d H:i:s");
		$this->Generic_model->insertDataReturnId("pharmacy_indent_line_items",$lineinfo);
		$this->db->query("update clinic_pharmacy_inventory set status=1 where drug_id=".$_POST['drgid'][$i]." and clinic_id=".$clinic_id);
	}
	redirect('Pharmacy_orders');
}
public function drug_add()
{

	$clinic_id = $this->session->userdata('clinic_id');
	
	$user_id = $this->session->userdata('user_id');
	$data['salt_info'] = $this->db->query("select * from salt")->result_array();
	$data['sname'] = '';
	foreach($data['salt_info'] as $sresult)
	{
		if($sresult['salt_name']!=''){
		if($data['sname']=='')
			$data['sname'] = $data['sname'].'"'.$sresult['salt_name'].'"';
		else
			$data['sname'] = $data['sname'].',"'.$sresult['salt_name'].'"';
		}
	}
	$param =$this->input->post();
	
    if(count($param)>0){
    	//print_r($param);exit; 
		$druginfo['formulation'] = $_POST['formulation_sb'];	
		$druginfo['trade_name'] = $_POST['trade_name'];
		$druginfo['manufacturer'] = $_POST['manufacturer'];
		$druginfo['hsn_code'] = $_POST['hsn_code'];
		$druginfo['cgst'] = $_POST['cgst'];
		$druginfo['sgst'] = $_POST['sgst'];
		$druginfo['igst'] = $_POST['igst'];
		$druginfo['created_by'] = $user_id;
		$druginfo['modified_by'] = $user_id;
		$druginfo['created_date_time'] = date("Y-m-d H:i:s");
		$druginfo['modified_date_time'] = date("Y-m-d H:i:s");
		$drug_id = $this->Generic_model->insertDataReturnId("drug",$druginfo);
		
		$salt_array = $_POST['salt'];
		$sids = '';$composition='';
		foreach($salt_array as $sresult)
		{
			if($sresult['salt_id']==0)
			{
				$sinfo['salt_name']= $sresult['salt_name'];
				$sinfo['scheduled_salt']= $sresult['schedule'];
				$sinfo['created_by']= $user_id;
				$sinfo['modified_by']= $user_id;
				$sinfo['created_date_time']= date("Y-m-d H:i:s");
				$sinfo['modified_date_time']= date("Y-m-d H:i:s");
				$salt_id = $this->Generic_model->insertDataReturnId("salt",$sinfo);
			}
			else
			{
				$salt_id = $sresult['salt_id'];
			}
			if($composition=='')
				$composition = $sresult['salt_name']." ".$sresult['dossage'].$sresult['unit'];
			else
				$composition = $composition." + ".$sresult['salt_name']." ".$sresult['dossage'].$sresult['unit'];
			if($sids=='')
				$sids = $salt_id;
			else
				$sids = $sids.",".$salt_id;
		}
		$this->db->query("update drug set salt_id='".$sids."',composition='".$composition."' where drug_id=".$drug_id);
		
		$drug_array = $_POST['drug'];	
		foreach($drug_array as $dresult)
		{
			$split_date = explode("/",$dresult['expirydate']);
			$new_date = trim($split_date[1]).'/'.trim($split_date[0]).'/'.trim($split_date[2]);
			$dinfo['drug_id'] = $drug_id;
			$dinfo['clinic_id'] = $clinic_id;
			$dinfo['batch_no'] = $dresult['batch_no'];
			$dinfo['quantity'] = $dresult['quantity'];
			$dinfo['mrp'] = $dresult['mrp'];
			$dinfo['pack_size'] = $dresult['pack_size'];
			$dinfo['expiry_date'] = date("Y-m-d",strtotime($new_date));
			$dinfo['created_by']= $user_id;
			$dinfo['modified_by']= $user_id;
			$dinfo['created_date_time']= date("Y-m-d H:i:s");
			$dinfo['modified_date_time']= date("Y-m-d H:i:s");
			$this->Generic_model->insertDataReturnId("clinic_pharmacy_inventory_inward",$dinfo);
		}
		
		if($_POST['max_discount']!=''&&$_POST['reorder_level']!='')
		{
			$cinfo['clinic_id'] = $clinic_id;
			$cinfo['drug_id'] = $drug_id;
			$cinfo['reorder_level'] = $_POST['reorder_level'];
			$cinfo['igst'] = $_POST['igst'];
			$cinfo['cgst'] = $_POST['cgst'];
			$cinfo['sgst'] = $_POST['sgst'];
			$cinfo['max_discount_percentage'] = $_POST['max_discount'];
			$cinfo['created_by']= $user_id;
			$cinfo['modified_by']= $user_id;
			$cinfo['created_date_time']= date("Y-m-d H:i:s");
			$cinfo['modified_date_time']= date("Y-m-d H:i:s");
			$this->Generic_model->insertDataReturnId("clinic_pharmacy_inventory",$cinfo);
		}
		
		/*$lineinfo['salt_id'] = $_POST['salt_id'];
		$lineinfo['durg_reorder_level'] = $_POST['durg_reorder_level'];
		$lineinfo['durg_reorder_level'] = $_POST['durg_reorder_level'];
		$lineinfo['trade_name'] = $_POST['trade_name'];
		$lineinfo['formulation'] = $_POST['formulation'];
		$lineinfo['composition'] = $_POST['composition'];
		$lineinfo['manufacturer'] = $_POST['manufacturer'];
		$lineinfo['pack_size'] = $_POST['pack_size'];
		$lineinfo['category'] = $_POST['category'];
		$lineinfo['mrp'] = $_POST['mrp'];				
		$lineinfo['created_by'] = $user_id;
		$lineinfo['modified_by'] = $user_id;
		$lineinfo['created_date_time'] = date("Y-m-d H:i:s");
		$lineinfo['modified_date_time'] = date("Y-m-d H:i:s");
		$this->Generic_model->insertDataReturnId("drug",$lineinfo);	*/
		$this->drug_json();		
		redirect('Pharmacy_orders');
	}
	//echo "<pre>";print_r($data);
	$data['view'] = 'Pharmacy_orders/add_drug';
    $this->load->view('layout', $data);
}
	public function getsalt_details()
	{
		$saltName = $_POST['saltName'];
		$sinfo = $this->db->query("select * from salt where salt_name='".$saltName."'")->row();
		if(count($sinfo)>0)
			echo $sinfo->salt_id.":".$sinfo->scheduled_salt;
		else
			echo "0".":"."";
	}

    public function drug_json(){
    	$drugs_list = $this->db->query("select CONCAT(formulation,' ',trade_name) as drug_name from drug")->result(); 

		$prefix = '';
		$prefix .= '[';
		foreach($drugs_list as $row) {
			$prefix .= json_encode($row->drug_name);
			$prefix .= ',';
		}
		$prefix .= ']';

		$json_file = str_replace(",]","]",trim($prefix,","));

		$path_user = './uploads/drugs.json';

		if (!file_exists($path_user)) {                   
			$fp = fopen('./uploads/drugs.json', 'w');
			fwrite($fp, $json_file);
		} else {
			unlink($path_user);
			$fp = fopen('./uploads/drugs.json', 'w');
			fwrite($fp, $json_file);
		}
	}

}