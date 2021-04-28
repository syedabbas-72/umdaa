<?php
error_reporting(0);

defined('BASEPATH') OR exit('No direct script access allowed');

class Lab extends MY_Controller {

public function __construct() {

    parent::__construct();

    $this->load->library('mail_send', array('mailtype'=>'html'));		 

$this->form_validation->set_error_delimiters('<div class="error">', '</div>');	

}
public function index(){
}

public function lab_dashboard()
{
	$clinic_id = $this->session->userdata('clinic_id');

	$cond = '';
	if($clinic_id!=0)
		$cond = "where clinic_id=".$clinic_id;
	
	$tdate = date('Y-m-d');
	
	$data['view'] = 'lab/lab_dashboard';
	

    $this->load->view('layout', $data);
}
public function prescriptions()
{
	$clinic_id = $this->session->userdata('clinic_id');

	$cond = '';
	if($clinic_id!=0)
		$cond = "where clinic_id=".$clinic_id;
	
	$tdate = date('Y-m-d');
	
	$data['view'] = 'lab/prescriptions';
	

    $this->load->view('layout', $data);
}

public function delete_lab_investigation($id=''){
	$this->db->query("delete from clinic_investigations where clinic_investigation_id='".$id."'");
	redirect("lab/lab_investigations");
}
public function lab_investigations()
{
	$clinic_id = $this->session->userdata('clinic_id');

	$cond = '';
	if($clinic_id!=0)
		$cond = "where clinic_id=".$clinic_id;
	
	$data['labinvg_info'] = $this->db->query("select * from clinic_investigations a inner join investigations b on a.investigation_id=b.investigation_id ".$cond)->result_array();
	
	$tdate = date('Y-m-d');
	
	$data['view'] = 'lab/lab_investigations';
	

    $this->load->view('layout', $data);
}
public function lab_billing()
{
	$tdate = date('Y-m-d');
	
	$clinic_id = $this->session->userdata('clinic_id');
	$cond = '';
	if($clinic_id!=0)
		$cond = "where clinic_id=".$clinic_id." and billing_type='Lab'";
	else
		$cond = "where billing_type='Lab'";
	
	$billing = $this->db->query("select * from billing ".$cond)->result();
	$i=0;
	foreach($billing as $result)
	{
		$data['billing_info'][$i]['billing_id'] = $result->billing_id;
		$invoiceinfo = $this->db->query("select * from invoice where billing_id=".$result->billing_id)->row();
		$blineitems = $this->db->query("select * from billing_line_items a inner join clinic_investigations b on a.investigation_id=b.clinic_investigation_id inner join investigations c on b.investigation_id=c.investigation_id where a.billing_id=".$result->billing_id." and b.clinic_id=".$clinic_id)->result();
		$tests = '';
		foreach($blineitems as $br)
		{
			if($tests=='')
				$tests = $tests.$br->investigation;
			else
				$tests = $tests.",".$br->investigation;
		}
		$data['billing_info'][$i]['investigations'] = $tests;
		$data['billing_info'][$i]['order'] = $invoiceinfo->order_no;
		$data['billing_info'][$i]['ptype'] = $invoiceinfo->payment_type;
		$data['billing_info'][$i]['invoice'] = $invoiceinfo->invoice_no;
		$data['billing_info'][$i]['guest_name'] = $result->guest_name;
		$data['billing_info'][$i]['guest_mobile'] = $result->guest_mobile;
		$data['billing_info'][$i]['inv_date'] = date("y-m-d",strtotime($result->billing_date_time));
		$invoice = $this->db->query("select sum(total_amount) as iamt,sum(amount) as aamount from invoice where billing_id=".$result->billing_id)->row();
		$data['billing_info'][$i]['p_amt'] = $invoice->aamount;
		$data['billing_info'][$i]['inv_amt'] = $invoice->iamt;
		$data['billing_info'][$i]['out_amt'] = ($invoice->iamt-$invoice->aamount);
		$i++;
	}
	
	$data['view'] = 'lab/lab_billing';
	

    $this->load->view('layout', $data);
}
public function view_order($bid)
{
	$data['billing_master'] = $this->db->query("select * from billing where billing_id=".$bid)->row();
	$billinglineitems = $this->db->query("select * from billing_line_items where billing_id=".$bid)->result_array();
	$i=0;
	foreach($billinglineitems as $bresult)
	{
		
		if($bresult['clinic_investigation_package_id']!=0)
		{
			$piname = $this->db->query("select * from clinic_investigation_packages a inner join clinic_investigation_package_line_items b on a.clinic_investigation_package_id=b.clinic_investigation_package_id inner join clinic_investigations c on b.clinic_investigation_id=c.clinic_investigation_id inner join investigations d on c.investigation_id=d.investigation_id where a.clinic_investigation_package_id=".$bresult['clinic_investigation_package_id'])->result();
			
			foreach($piname as $presult)
			{
				$data['billing_line_items'][$i] = $bresult;
				$data['billing_line_items'][$i]['opname'] = $presult->package_name;
				$data['billing_line_items'][$i]['pname'] = $presult->investigation;
				$data['billing_line_items'][$i]['item_code'] = $presult->item_code;
				$data['billing_line_items'][$i]['short_form'] = $presult->short_form;
				$data['billing_line_items'][$i]['category'] = $presult->category;
				$data['billing_line_items'][$i]['price'] = $presult->price;
				$data['billing_line_items'][$i]['type'] = "package";
				$data['billing_line_items'][$i]['oid'] = $presult->clinic_investigation_id;
				$data['billing_line_items'][$i]['pid'] = $presult->clinic_investigation_package_id;
				$data['billing_line_items'][$i]['picount'] = count($piname);
				$i++;				
			}
		}
		else{
			$invgname = $this->db->query("select * from clinic_investigations a inner join investigations b on a.investigation_id=b.investigation_id where clinic_investigation_id=".$bresult['investigation_id'])->row();
			$data['billing_line_items'][$i] = $bresult;
				$data['billing_line_items'][$i]['pname'] = $invgname->investigation;
				$data['billing_line_items'][$i]['item_code'] = $invgname->item_code;
				$data['billing_line_items'][$i]['short_form'] = $invgname->short_form;
				$data['billing_line_items'][$i]['category'] = $invgname->category;
				$data['billing_line_items'][$i]['price'] = $invgname->price;
				$data['billing_line_items'][$i]['type'] = "invg";
				$data['billing_line_items'][$i]['oid'] = $invgname->clinic_investigation_id;
				$data['billing_line_items'][$i]['pid'] = 0;
				$data['billing_line_items'][$i]['picount'] = 0;
			$i++;
		}		
	}
	//echo "<pre>";print_r($data);
	$data['invoice'] = $this->db->query("select sum(total_amount) as iamt,sum(amount) as aamount from invoice where billing_id=".$bid)->row();

	$data['view'] = 'lab/view_billing_order';
    $this->load->view('layout', $data);
}
public function get_template_info()
{
	$clinic_id = $this->session->userdata('clinic_id');

	$cond = '';
	if($clinic_id!=0)
		$cond = "and clinic_id=".$clinic_id;
	$invgid = $_POST['invg'];
	$bid = $_POST['bid'];
	$pid = $_POST['pid'];
	
	$cinfo = $this->db->query("select * from clinic_investigations where clinic_investigation_id=".$invgid." ".$cond)->row();
	
	$info = $this->db->query("select * from lab_templates where investigation_id=".$cinfo->investigation_id)->row();
	//echo $this->db->last_query();
	$tlineifno = $this->db->query("select * from lab_template_line_items where lab_template_id=".$info->lab_template_id)->result_array();
	if($info->template_type=='Excel')
	{
		$tinfo = '';
		foreach($tlineifno as $tresult)
		{	
			$clinic_invg = $this->db->query("select * from clinic_investigations where investigation_id=".$tresult['investigation_id']." ".$cond)->row();
			if(count($clinic_invg)>0)
			{
				$patient_info = $this->db->query("select * from patient_lab_reports a inner join patient_lab_report_line_items b on a.patient_lab_report_id=b.patient_lab_report_id where a.billing_id=".$bid." and b.investigation_id=".$tresult['investigation_id'])->row();
				if($patient_info->value!='')
					$pvalue = $patient_info->value;
				else
					$pvalue = 0;
				
				$tinfo = $tinfo.'<tr><td>'.$tresult['parameter'].'</td><td><input class="form-control" type="text" name="value[]" value="'.$pvalue.'"></td><td><input class="form-control" type="text" name="lowrange[]" value="'.$clinic_invg->low_range.'"></td><td><input class="form-control" type="text" name="highrange[]" value="'.$clinic_invg->high_range.'"></td><td><input class="form-control" type="text" name="units[]" value="'.$clinic_invg->units.'"></td><td><input class="form-control" type="text" name="method[]" value="'.$clinic_invg->method.'"></td><td><textarea class="form-control" name="oinformation[]" rows="5" cols="20">'.$clinic_invg->other_information.'</textarea><input class="form-control" type="hidden" value="'.$tresult['investigation_id'].'" name="invgid[]" /></td><input type="hidden" value="'.$info->lab_template_id.'" name="ltempid" /><input type="hidden" value="'.$tresult['lab_template_line_item_id'].'" name="ltempliid[]" /></td></tr>';
			}
			else
			{
				$tinfo = $tinfo.'<tr><td>'.$tresult['parameter'].'</td><td><input class="form-control" type="text" name="value[]" value=""></td><td><input class="form-control" type="text" name="lowrange[]" value=""></td><td><input class="form-control" type="text" name="highrange[]" value=""></td><td><input class="form-control" type="text" name="units[]" value=""></td><td><input class="form-control" type="text" name="method[]" value=""></td><td><textarea class="form-control" name="oinformation[]" rows="5" cols="20"></textarea></td><input type="hidden" value="'.$tresult['investigation_id'].'" name="invgid[]" /><input type="hidden" value="'.$info->lab_template_id.'" name="ltempid" /><input type="hidden" value="'.$tresult['lab_template_line_item_id'].'" name="ltempliid[]" /></td></tr>';
			}
		}
		echo "excel".":".$tinfo;
	}
	else
	{
		$tinfo = '';
		foreach($tlineifno as $tresult)
		{
			$patient_info = $this->db->query("select * from patient_lab_reports a inner join patient_lab_report_line_items b on a.patient_lab_report_id=b.patient_lab_report_id where a.billing_id=".$bid." and b.investigation_id=".$tresult['investigation_id'])->row();
			//echo $this->db->last_query();
				if($patient_info->remark!='')
					$pvalue = $patient_info->remark;
				else
					$pvalue = $tresult['remarks'];
			$tinfo = $tinfo.'<tr><td>'.$tresult['parameter'].'</td><td><textarea rows="5" cols="20" name="remarks[]">'.$pvalue.'</textarea></td><input type="hidden" value="'.$tresult['investigation_id'].'" name="invgid[]" /><input type="hidden" value="'.$info->lab_template_id.'" name="ltempid" /><input type="hidden" value="'.$tresult['lab_template_line_item_id'].'" name="ltempliid[]" /></td></tr>';
		}
		echo "general".":".$tinfo;
	}
}
public function templates_input_save()
{
	$clinic_id = $this->session->userdata('clinic_id');
	$user_id = $this->session->userdata('user_id');
	
	$pinfo = $this->db->query("select * from patient_lab_reports where billing_id=".$_POST['bid']." and lab_template_id=".$_POST['ltempid'])->row();
	if(count($pinfo)>0)
	{
		$this->db->query("delete from patient_lab_report_line_items where patient_lab_report_id=".$pinfo->patient_lab_report_id);
		$this->db->query("delete from patient_lab_reports where patient_lab_report_id=".$pinfo->patient_lab_report_id." and lab_template_id=".$_POST['ltempid']);
	}
	
	
	$data['clinic_id'] = $clinic_id;
	$data['billing_id'] = $_POST['bid'];
	$data['guest_name'] = $_POST['pname'];
	$data['guest_mobile'] = $_POST['pmobile'];
	$data['lab_template_id'] = $_POST['ltempid'];
	$data['status'] = 1;
	$data['created_by'] = $user_id;
	$data['modified_by'] = $user_id;
	$data['created_date_time'] = date("Y-m-d H:i:s");
	$data['modified_date_time'] = date("Y-m-d H:i:s");
	$last_inserted_id = $this->Generic_model->insertDataReturnId("patient_lab_reports",$data);
	$invgid = count($_POST['invgid']);
	for($i=0;$i<$invgid;$i++){
		$lineinfo['patient_lab_report_id'] = $last_inserted_id;
		$lineinfo['investigation_id'] = $_POST['invgid'][$i];
		$lineinfo['lab_template_line_item_id'] = $_POST['ltempliid'][$i];		
		if($_POST['type']=='excel')
			$lineinfo['value'] = $_POST['value'][$i];
		else
			$lineinfo['remark'] = $_POST['remarks'][$i];
		$lineinfo['status'] = 1;
		$lineinfo['created_by'] = $user_id;
		$lineinfo['modified_by'] = $user_id;
		$lineinfo['created_date_time'] = date("Y-m-d H:i:s");
		$lineinfo['modified_date_time'] = date("Y-m-d H:i:s");
		$this->Generic_model->insertDataReturnId("patient_lab_report_line_items",$lineinfo);
		
		if($_POST['type']=='excel'){
			$invgidd = $_POST['invgid'][$i];
			$cinvg_chk = $this->db->query("select * from clinic_investigations where investigation_id=".$invgidd." and clinic_id=".$clinic_id)->row();
			if(count($cinvg_chk)<=0)
			{
				$cinvg['clinic_id'] = $clinic_id;
				$cinvg['investigation_id'] = $_POST['invgid'][$i];
				$cinvg['low_range'] = $_POST['lowrange'][$i];
				$cinvg['high_range'] = $_POST['highrange'][$i];
				$cinvg['units'] = $_POST['units'][$i];
				$cinvg['method'] = $_POST['method'][$i];
				$cinvg['other_information'] = $_POST['oinformation'][$i];
				$cinvg['status'] = 1;
				$cinvg['created_by'] = $user_id;
				$cinvg['modified_by'] = $user_id;
				$cinvg['created_date_time'] = date("Y-m-d H:i:s");
				$cinvg['modified_date_time'] = date("Y-m-d H:i:s");
				$this->Generic_model->insertDataReturnId("clinic_investigations",$cinvg);
			}
		}
	}
	redirect('Lab/view_order/'.$_POST['bid']);
}
public function print_bill($bid)
{
	//$this->db->query("update billing set payment_mode='".$this->input->post('payment_mode')."' AND payment_status = 1 where billing_id=".$bid);
	$pdf_path = $this->generatepdf($bid);
	$this->db->query("update billing set invoice_pdf='".$pdf_path."' where billing_id=".$bid);
	redirect('uploads/billings/'.$pdf_path);
}
public function generatepdf($bid)
{
	$data['billing_master'] = $this->db->query("select * from billing where billing_id=".$bid)->row();
	$data['billing_line_items'] = $this->db->query("select * from billing_line_items a inner join clinic_investigations b on a.investigation_id=b.clinic_investigation_id inner join investigations c on b.investigation_id=c.investigation_id where billing_id=".$bid)->result();
	$data['invoice'] = $this->db->query("select sum(total_amount) as iamt,sum(amount) as aamount from invoice where billing_id=".$bid)->row();
	if($data['billing_master']->patient_id!=''){
		$pinfo = $this->db->query("select * from patients where patient_id=".$data['billing_master']->patient_id)->row();
		$data['patient_name'] = $pinfo->first_name." ".$pinfo->middle_name." ".$pinfo->last_name;
		$data['gender'] = $pinfo->gender;
		$data['age'] = $pinfo->age;
		$data['patient_id'] = $pinfo->umr_no;
		$data['paddress'] = $pinfo->address_line.",".$pinfo->district_id.",".$pinfo->state_id.",".$pinfo->pincode;
		$clinic_info = $this->db->query("select * from clinics where clinic_id=".$pinfo->clinic_id)->row();
		$data['address'] = $clinic_info->address;
		$data['clinic_phone'] = $clinic_info->clinic_phone;
		$data['clinic_logo'] = $clinic_info->clinic_logo;
	}
	else
	{
		$clinic_info = $this->db->query("select * from clinics where clinic_id=".$data['billing_master']->clinic_id)->row();
		$data['address'] = $clinic_info->address;
		$data['clinic_phone'] = $clinic_info->clinic_phone;
		$data['clinic_logo'] = $clinic_info->clinic_logo;
		$data['patient_name'] = $data['billing_master']->guest_name;
		$data['gender'] = '';
		$data['patient_id'] = '';
		
	}
	//echo "<pre>";print_r($data);exit;
	$this->load->library('M_pdf');
    $html = $this->load->view('lab/order_invoice',$data,true);
    $pdfFilePath = $data['billing_master']->invoice_no.".pdf";
    $this->m_pdf->pdf->WriteHTML($html);
    $this->m_pdf->pdf->Output("./uploads/prescriptions/".$pdfFilePath, "F");
    $this->m_pdf->pdf->Output("./uploads/billings/".$pdfFilePath, "F");
	return $pdfFilePath;
}
public function make_lab_payment($bid)
{
	$clinic_id = $this->session->userdata('clinic_id');

	$cond = '';
	if($clinic_id!=0)
		$cond = "where clinic_id=".$clinic_id;
	$data['view'] = 'lab/make_lab_payment';
	

    $this->load->view('layout', $data);
}
public function lab_orders()
{
	$clinic_id = $this->session->userdata('clinic_id');
	$cond = '';
	$tdate = date('Y-m-d');
	if($clinic_id!=0)
		$cond = "where clinic_id=".$clinic_id." and billing_type='Lab'";
	else
		$cond = "where billing_type='Lab'";
	
	$billing = $this->db->query("select * from billing ".$cond)->result();
	
	$i=0;
	foreach($billing as $result)
	{
		//echo "<pre>";print_r($result);
		$data['billing_info'][$i]['billing_id'] = $result->billing_id;
		$blineitems = $this->db->query("select *,a.clinic_investigation_package_id as civp from billing_line_items a inner join billing b on a.billing_id=b.billing_id where a.billing_id=".$result->billing_id." and b.clinic_id=".$clinic_id)->result();		
		$tests = '';
		foreach($blineitems as $br)
		{			
			if($br->clinic_investigation_package_id!=0)
			{
				$piname = $this->db->query("select * from clinic_investigation_packages where clinic_investigation_package_id=".$br->civp)->row();
				//echo $this->db->last_query();
				if($tests=='')
					$tests = $tests.$piname->package_name;
				else
					$tests = $tests.",".$piname->package_name;
			}
			else{
				$invgname = $this->db->query("select * from clinic_investigations a inner join investigations b on a.investigation_id=b.investigation_id where clinic_investigation_id=".$br->investigation_id)->row();
				if($tests=='')
					$tests = $tests.$invgname->investigation;
				else
					$tests = $tests.",".$invgname->investigation;				
			}
		}
		$data['billing_info'][$i]['ctests'] = $tests;
		$data['billing_info'][$i]['order_number'] = $result->billing_no;
		$data['billing_info'][$i]['guest_name'] = $result->guest_name;
		$data['billing_info'][$i]['guest_mobile'] = $result->guest_mobile;
		$data['billing_info'][$i]['inv_date'] = date("y-m-d",strtotime($result->billing_date_time));
		$invoice = $this->db->query("select sum(total_amount) as iamt,sum(amount) as aamount from invoice where billing_id=".$result->billing_id)->row();
		$data['billing_info'][$i]['inv_amt'] = $invoice->iamt;

		$data['billing_info'][$i]['osa_amt'] = ($invoice->iamt-$invoice->aamount);
		$data['billing_info'][$i]['out_amt'] = $data['billing_info'][$i]['inv_amt'] - $data['billing_info'][$i]['osa_amt'];
		$i++;
	}
	
	
	//echo "<pre>";print_r($data);
	
	$data['view'] = 'lab/lab_order_list';
	

    $this->load->view('layout', $data);
}
public function order_delete($bid='')
{
	$this->db->query("delete billing from billing inner join billing_line_items on billing.billing_id = billing_line_items.billing_id where billing.billing_id='".$bid."'");
	redirect('Lab/lab_orders');
}
public function add_lab_order()
{
	$clinic_id = $this->session->userdata('clinic_id');
	$cond = '';
	if($clinic_id!=0)
		$cond = "where clinic_id=".$clinic_id;
	$clinic_discount = $this->db->query("select * from clinics where clinic_id=".$clinic_id)->row();
	$data['cl_discount'] = ($clinic_discount->lab_discount!=NULL)?$clinic_discount->lab_discount:$clinic_discount->lab_discount;
	$clinic_invg = $this->db->query("select DISTINCT(investigation) from investigations a inner join clinic_investigations b on a.investigation_id=b.investigation_id ".$cond)->result_array();
	$cinvg='';
	foreach($clinic_invg as $result)
	{
		if($cinvg=='')
			$cinvg = $cinvg.'"'.$result['investigation'].'"';
		else
			$cinvg = $cinvg.',"'.$result['investigation'].'"';
	}
	
	$packages = $this->db->query("select DISTINCT(package_name) from clinic_investigation_packages")->result_array();
	foreach($packages as $presult)
	{
		if($cinvg=='')
			$cinvg = $cinvg.'"'.$presult['package_name'].'"';
		else
			$cinvg = $cinvg.',"'.$presult['package_name'].'"';
	}
	
	$data['cinvg'] = $cinvg;
	
	$tdate = date('Y-m-d');
	
	$data['view'] = 'lab/lab_order';
	

    $this->load->view('layout', $data);
}
public function save_lab_order()
{
	//echo "<pre>";print_r($_POST);exit;
	$clinic_id = $this->session->userdata('clinic_id');
	$user_id = $this->session->userdata('user_id');
	$last_id = $this->db->query("select * from billing where receipt_no like 'RECEIPT-".$clinic_id."-%'")->result_array();
	$ilast_id = $this->db->query("select * from invoice a inner join billing b on a.billing_id=b.billing_id where b.receipt_no like 'RECEIPT-".$clinic_id."-%'")->result_array();
	$bnumbers = $this->db->query("select * from billing where billing_no like 'ORD-".$clinic_id."-%'")->result_array();
	$lcount = count($last_id);
	$ilcount = count($ilast_id);
	$data['receipt_no'] = 'RECEIPT-'.$clinic_id."-".($lcount+1);
	$data['invoice_no'] = 'INV-'.$clinic_id."-".($lcount+1);
	$data['billing_no'] = 'ORD-'.$clinic_id."-".($lcount+1);
	$iinvoice_no = 'INV-'.$clinic_id."-".($ilcount+1);
	$order_no = 'ORD'.($ilcount+1);
	$data['clinic_id'] = $clinic_id;
	$data['guest_name'] = $_POST['pname'];
	$data['guest_mobile'] = $_POST['pmobile'];
	$data['billing_type'] = 'Lab';
	$data['billing_date_time'] = date("Y-m-d H:i:s");
	$data['status'] = 1;
	$data['created_by'] = $user_id;
	$data['modified_by'] = $user_id;
	$data['created_date_time'] = date("Y-m-d H:i:s");
	$data['modified_date_time'] = date("Y-m-d H:i:s");
	$last_inserted_id = $this->Generic_model->insertDataReturnId("billing",$data);
	$billing_id = $last_inserted_id;
	$invgid = count($_POST['invgid']);
	$tmrp=0;$cldiscount = $_POST['lab_discount']; $desc = round(($_POST['lab_discount']/100),2);
	for($i=0;$i<$invgid;$i++){
		$lineinfo['billing_id'] = $billing_id;
		if($_POST['type'][$i]=='invg'){
			$lineinfo['investigation_id'] = $_POST['invgid'][$i];
			$lineinfo['clinic_investigation_package_id'] = 0;
		}
		else if($_POST['type'][$i]=='package'){
			$lineinfo['clinic_investigation_package_id'] = $_POST['invgid'][$i];
			$lineinfo['investigation_id'] = 0;
		}
		$lineinfo['status'] = 1;
		$lineinfo['discount'] = $cldiscount;
		$lineinfo['amount'] = ($_POST['mrp'][$i]-($desc*$_POST['mrp'][$i]));
		$lineinfo['created_by'] = $user_id;
		$lineinfo['modified_by'] = $user_id;
		$lineinfo['created_date_time'] = date("Y-m-d H:i:s");
		$lineinfo['modified_date_time'] = date("Y-m-d H:i:s");
		$this->Generic_model->insertDataReturnId("billing_line_items",$lineinfo);
		$tmrp = $tmrp+($_POST['mrp'][$i]-($desc*$_POST['mrp'][$i]));
	}
	$outward['invoice_no'] = $iinvoice_no;
	$outward['billing_id'] = $billing_id;
	$outward['payment_type'] = $_POST['ptm'];
	$outward['invoice_date'] = date("Y-m-d");
	$outward['total_amount'] = $tmrp;
	$outward['amount'] = $_POST['ptm_txt'];	
	$outward['created_by'] = $user_id;
	$outward['modified_by'] = $user_id;
	$outward['created_datetime'] = date("Y-m-d H:i:s");
	$outward['modified_datetime'] = date("Y-m-d H:i:s");
	$this->Generic_model->insertDataReturnId("invoice",$outward);
	redirect('Lab/lab_orders');
}
public function lab_packages()
{
	$clinic_id = $this->session->userdata('clinic_id');

	$cond = '';
	if($clinic_id!=0)
		$cond = "where clinic_id=".$clinic_id;
	
	$tdate = date('Y-m-d');
	
	$data['click_invg_package'] = $this->db->query("select * from clinic_investigation_packages ".$cond)->result_array();
	
	$data['view'] = 'lab/lab_package';
	

    $this->load->view('layout', $data);
}
public function add_clinic_investigation()
{
	$clinic_id = $this->session->userdata('clinic_id');
	
	$user_id = $this->session->userdata('user_id');
	$param =$this->input->post();
	$method = $this->db->query("select DISTINCT(method) as meth from clinic_investigations")->result_array();
	
	$methods = '';
	foreach($method as $result)
	{
		if($methods=='')
			$methods = $methods.'"'.$result['meth'].'"';
		else
			$methods = $methods.',"'.$result['meth'].'"';
	}
	
	$data['methods'] = $methods;
    if(count($param)>0){		
		$invg_ids = count($_POST['invgid']);
		for($i=0;$i<$invg_ids;$i++){
			$lineinfo['investigation_id'] = $_POST['invgid'][$i];
			$lineinfo['clinic_id'] = $clinic_id;
			$lineinfo['price'] = $_POST['mrp'][$i];
			$lineinfo['low_range'] = $_POST['lowrange'][$i];	
			$lineinfo['high_range'] = $_POST['highrange'][$i];	
			$lineinfo['units'] = $_POST['units'][$i];	
			$lineinfo['method'] = $_POST['method'][$i];	
			$lineinfo['other_information'] = $_POST['oinfo'][$i];	
			
			$lineinfo['status'] = 1;
			$lineinfo['created_by'] = $user_id;
			$lineinfo['modified_by'] = $user_id;
			$lineinfo['created_date_time'] = date("Y-m-d H:i:s");
			$lineinfo['modified_date_time'] = date("Y-m-d H:i:s");
			$this->Generic_model->insertDataReturnId("clinic_investigations",$lineinfo);
			if($_POST['shortform'][$i]!='')
				$this->db->query("update investigations set short_form='".$_POST['shortform'][$i]."' where investigation_id=".$_POST['invgid'][$i]);
		}
		redirect('Lab/lab_investigations');
	}
	
	$data['view'] = 'lab/add_clinic_investigation';
    $this->load->view('layout', $data);
}
public function findclinicinvestigation()
{
	$clinic_id = $this->session->userdata('clinic_id');

	$cond = '';
	if($clinic_id!=0)
		$cond = " and clinic_id=".$clinic_id;
	$invg = $_POST['info'];
	$investigation = $this->db->query("select * from investigations where investigation='".$invg."'")->row();	
	if(count($investigation)>0)
	{
		$cinvg = $this->db->query("select * from clinic_investigations where investigation_id=".$investigation->investigation_id.$cond)->row();		
		if(count($cinvg)>0)
			echo $cinvg->clinic_investigation_id;
	}
}
public function edit_investigation($invgid)
{
	$data['invginfo'] = $this->db->query("select * from clinic_investigations a inner join investigations b on a.investigation_id=b.investigation_id where a.clinic_investigation_id=".$invgid)->row();
	$param =$this->input->post();
    if(count($param)>0){
		$mrp = $_POST['imrp'];		
		$low_range = $_POST['low_range'];
		$high_range = $_POST['high_range'];
		$units = $_POST['units'];
		$method = $_POST['method'];
		$other_information = $_POST['other_information'];
		$this->db->query("update clinic_investigations set price='".$mrp."',low_range='".$low_range."',high_range='".$high_range."',units='".$units."',method='".$method."',other_information='".$other_information."' where clinic_investigation_id=".$_POST['clinic_investigation_id']);
		redirect('Lab/lab_investigations');
	}
	$data['view'] = 'lab/edit_investigation';
    $this->load->view('layout', $data);
}
public function get_investigation_info()
{
	$clinic_id = $this->session->userdata('clinic_id');
	$cond = '';
	if($clinic_id!=0)
		$cond = "and clinic_id=".$clinic_id;
	$ing_name = $_POST['invg'];
	$info = $this->db->query("select * from investigations where investigation like '".$ing_name."%'")->row();
	//echo $this->db->last_query();
	if(count($info)>0)
	{
		$clinic_investigation = $this->db->query("select * from clinic_investigations where investigation_id=".$info->investigation_id." ".$cond)->row();
		if(count($clinic_investigation)>0)
			echo $info->investigation_id.":".$info->investigation.":".$info->item_code.":".$info->category.":".$info->mrp.":".$info->short_form.":".$clinic_investigation->low_range.":".$clinic_investigation->high_range.":".$clinic_investigation->units.":".$clinic_investigation->method.":".$clinic_investigation->other_information;
		else
			echo $info->investigation_id.":".$info->investigation.":".$info->item_code.":".$info->category.":".$info->mrp.":".$info->short_form.":"."".":"."".":"."".":"."".":"."";
	}
	else
	{		
		echo '';
	}
}
public function get_clinic_investigation_info_order()
{
	$clinic_id = $this->session->userdata('clinic_id');
	$cond = '';
	if($clinic_id!=0)
		$cond = "and clinic_id=".$clinic_id;
	$ing_name = $_POST['invg'];
	$rcount = $_POST['rcount'];
	$info = $this->db->query("select * from investigations a inner join clinic_investigations b on a.investigation_id=b.investigation_id where investigation like '".$ing_name."%' ".$cond)->row();
	$pinfo = $this->db->query("select * from clinic_investigation_packages where package_name like '%".$ing_name."%' ".$cond)->row();
	//echo $this->db->last_query();
	if(count($info)>0)
	{
		echo $info->clinic_investigation_id.":".$info->investigation.":".$info->item_code.":".$info->category.":".$info->price.":".$info->short_form.":"."invg";		
	}
	else if(count($pinfo)>0)
	{
		echo $pinfo->clinic_investigation_package_id.":".$pinfo->investigation.":".$pinfo->item_code.":"."Package".":".$pinfo->price.":"."".":"."package";
	}
	else
	{		
		echo '';
	}
}
public function get_clinic_investigation_info()
{
	$clinic_id = $this->session->userdata('clinic_id');
	$cond = '';
	if($clinic_id!=0)
		$cond = "and clinic_id=".$clinic_id;
	$ing_name = $_POST['invg'];
	$info = $this->db->query("select * from investigations a inner join clinic_investigations b on a.investigation_id=b.investigation_id where investigation like '".$ing_name."%' ".$cond)->row();
	//echo $this->db->last_query();
	if(count($info)>0)
	{
		echo $info->clinic_investigation_id.":".$info->investigation.":".$info->item_code.":".$info->category.":".$info->mrp.":".$info->short_form.":".$info->low_range.":".$info->high_range.":".$info->units.":".$info->method.":".$info->other_information;		
	}
	else
	{		
		echo '';
	}
}
public function add_clinic_package()
{
	$clinic_id = $this->session->userdata('clinic_id');
	$user_id = $this->session->userdata('user_id');
	$param =$this->input->post();
	if(count($param)>0){
		
		$lineinfo['clinic_id'] = $clinic_id;
		$lineinfo['package_name'] = $_POST['package_name'];
		$lineinfo['price'] = $_POST['price'];	
		$lineinfo['item_code'] = $_POST['item_code'];	
		//$lineinfo['category'] = $_POST['category'];	
		
		$lineinfo['status'] = 1;
		$lineinfo['created_by'] = $user_id;
		$lineinfo['modified_by'] = $user_id;
		$lineinfo['created_date_time'] = date("Y-m-d H:i:s");
		$lineinfo['modified_date_time'] = date("Y-m-d H:i:s");
		$this->Generic_model->insertDataReturnId("clinic_investigation_packages",$lineinfo);
		redirect('Lab/lab_packages');
	}
	$data['view'] = 'lab/add_clinic_package';
    $this->load->view('layout', $data);
}
public function edit_clinic_package($invgpid)
{
	$data['invgpinfo'] = $this->db->query("select * from clinic_investigation_packages where clinic_investigation_package_id=".$invgpid)->row();
	$param =$this->input->post();
    if(count($param)>0){
		$package_name = $_POST['package_name'];		
		$price = $_POST['price'];	
		$item_code = $_POST['item_code'];	
		//$category = $_POST['category'];	
		
		$this->db->query("update clinic_investigation_packages set package_name='".$package_name."',price='".$price."', item_code='".$item_code."' where clinic_investigation_package_id=".$_POST['clinic_investigation_package_id']);
		redirect('Lab/lab_packages');
	}
	$data['view'] = 'lab/edit_clinic_package';
	

    $this->load->view('layout', $data);
}
public function view_clinic_package($invgpid)
{
	$clinic_id = $this->session->userdata('clinic_id');
	$cond = '';
	if($clinic_id!=0)
		$cond = "and clinic_id=".$clinic_id;
	$data['invgpinfo'] = $this->db->query("select * from clinic_investigation_packages where clinic_investigation_package_id=".$invgpid)->row();
	$data['plineitems'] = $this->db->query("select * from clinic_investigation_package_line_items a inner join clinic_investigations b on a.clinic_investigation_id=b.clinic_investigation_id inner join investigations c on b.investigation_id=c.investigation_id where clinic_investigation_package_id=".$invgpid." ".$cond)->result_array();
	//echo $this->db->last_query();
	$data['view'] = 'lab/view_clinic_package';
	

    $this->load->view('layout', $data);
}
public function add_clinic_package_lineitems($pid)
{
	$data['package_id'] = $pid;
	$clinic_id = $this->session->userdata('clinic_id');
	$cond = '';
	if($clinic_id!=0)
		$cond = " and clinic_id=".$clinic_id;
	
	$package_investigations = $this->db->query("select group_concat(clinic_investigation_id) as cnvg from clinic_investigation_package_line_items where clinic_investigation_package_id=".$pid)->row();
	//echo $this->db->last_query();
	
	if($package_investigations->cnvg=='')
		$cnvgids = 0;
	else
		$cnvgids = $package_investigations->cnvg;	
	
	$clinic_invg = $this->db->query("select DISTINCT(investigation) from investigations a inner join clinic_investigations b on a.investigation_id=b.investigation_id where clinic_investigation_id not in (".$cnvgids.") ".$cond)->result_array();
	//echo $this->db->last_query();
	
	$cinvg='';
	foreach($clinic_invg as $result)
	{
		if($cinvg=='')
			$cinvg = $cinvg.'"'.$result['investigation'].'"';
		else
			$cinvg = $cinvg.',"'.$result['investigation'].'"';
	}
	$data['cinvg'] = $cinvg;
	$user_id = $this->session->userdata('user_id');
	$param =$this->input->post();
	if(count($param)>0){		
		$invg_ids = count($_POST['invgid']);
		for($i=0;$i<$invg_ids;$i++){
			$lineinfo['clinic_investigation_id'] = $_POST['invgid'][$i];			
			$lineinfo['clinic_investigation_package_id'] = $pid;	
			$lineinfo['status'] = 1;
			$lineinfo['created_by'] = $user_id;
			$lineinfo['modified_by'] = $user_id;
			$lineinfo['created_date_time'] = date("Y-m-d H:i:s");
			$lineinfo['modified_date_time'] = date("Y-m-d H:i:s");
			$this->Generic_model->insertDataReturnId("clinic_investigation_package_line_items",$lineinfo);
		}
		redirect('Lab/view_clinic_package/'.$pid);
	}
	$data['view'] = 'lab/add_clinic_package_lineitems';
	

    $this->load->view('layout', $data);
}
public function clinic_package_lineitem_delete($pliid,$cpid)
{
	$this->db->query("delete from clinic_investigation_package_line_items where investigation_package_line_item_id=".$pliid);
	redirect('Lab/view_clinic_package/'.$cpid);
}
public function clinic_package_delete($cpid)
{
	$this->db->query("delete from clinic_investigation_packages where clinic_investigation_package_id=".$cpid);
	redirect('Lab/lab_packages');
}
public function templates()
{
	$cond = '';
	if($clinic_id!=0)
		$cond = "and clinic_id=".$clinic_id;
	$data['clinic_templates'] = $this->db->query("select * from clinic_investigation_template where archive=0 ".$cond)->result_array();
	
	$data['view'] = 'lab/templates_list';
	

    $this->load->view('layout', $data);
}
public function master_add_template()
{
	$clinic_id = $this->session->userdata('clinic_id');
	$user_id = $this->session->userdata('user_id');
	$param =$this->input->post();
	if(count($param)>0){
		
		$lineinfo['investigation_id'] = $_POST['template_name'];
		$ingname = $this->db->query("select * from investigations where investigation_id=".$_POST['template_name'])->row();
		$lineinfo['template_name'] = $ingname->investigation;
		$lineinfo['template_type'] = $_POST['template_type'];		
		$lineinfo['status'] = 1;
		$lineinfo['created_by'] = $user_id;
		$lineinfo['modified_by'] = $user_id;
		$lineinfo['created_date_time'] = date("Y-m-d H:i:s");
		$lineinfo['modified_date_time'] = date("Y-m-d H:i:s");
		$this->Generic_model->insertDataReturnId("lab_templates",$lineinfo);
		redirect('Lab/master_template');
	}
	$created_templates = $this->db->query("SELECT DISTINCT(investigation_id) FROM lab_templates ORDER BY investigation_id ASC")->result();
	$lab_templates = array();
	foreach ($created_templates as $key => $template_value) {
		$lab_templates[] = $template_value->investigation_id;
	}

	$iids = implode(",",$lab_templates);

	// $siids = substr($iids, -1);
	// if($siids==',')
	// 	$iids = rtrim($iids, ",");
	$data['investigations'] = $this->db->query("select * from investigations where investigation_id not in (".trim($iids).")")->result();
	//echo $created_templates->invg;
	$data['view'] = 'lab/master_add_template';
    $this->load->view('layout', $data);
}
public function edit_template($tid)
{
	$data['templateinfo'] = $this->db->query("select * from clinic_investigation_template where clinic_investigation_template_id=".$tid)->row();
	$param =$this->input->post();
    if(count($param)>0){
		$template_name = $_POST['template_name'];		
		$template_type = $_POST['template_type'];		
		$this->db->query("update clinic_investigation_template set template_name='".$template_name."',template_type='".$template_type."'   where clinic_investigation_template_id=".$_POST['clinic_investigation_template_id']);
		redirect('Lab/templates');
	}
	
	$data['view'] = 'lab/edit_template';
    $this->load->view('layout', $data);
}
public function edit_mastertemplate($tid=NULL)
{
	if($tid==NULL)
		$tid = $_POST['lab_template_id'];
	
	$param =$this->input->post();
    if(count($param)>0){
		$ing_id = $_POST['template_name'];
		$ingname = $this->db->query("select * from investigations where investigation_id=".$_POST['template_name'])->row();
		$template_name = $ingname->investigation;		
		$template_type = $_POST['template_type'];		
		$this->db->query("update lab_templates set investigation_id=".$ing_id.",template_name='".$template_name."',template_type='".$template_type."'   where lab_template_id=".$_POST['lab_template_id']);
		redirect('Lab/master_template');
	}
	$data['templateinfo'] = $this->db->query("select * from lab_templates where lab_template_id=".$tid)->row();
	$created_templates = $this->db->query("SELECT GROUP_CONCAT(DISTINCT(investigation_id) SEPARATOR ',') as invg FROM lab_templates where investigation_id not in(".$data['templateinfo']->investigation_id.")")->row();
	$iids = $created_templates->invg;
	$siids = substr($iids, -1);
	if($siids==',')
		$iids = rtrim($iids, ",");
	
	
	
	$data['investigations'] = $this->db->query("select * from investigations where investigation_id not in (".$iids.")")->result();
	
	$data['view'] = 'lab/edit_mastertemplate';
    $this->load->view('layout', $data);
}
public function template_delete($tid)
{
	$this->db->query("update clinic_investigation_template set archive=1 where clinic_investigation_template_id=".$tid);
	redirect('Lab/templates');
}
public function mastertemplate_delete($tid)
{
	$this->db->query("update lab_templates set archive=1 where lab_template_id=".$tid);
	redirect('Lab/master_template');
}
public function template_view($tid)
{
	$clinic_id = $this->session->userdata('clinic_id');
	$cond = '';
	if($clinic_id!=0)
		$cond = "where clinic_id=".$clinic_id;
	$clinic_invg = $this->db->query("select DISTINCT(investigation) from investigations a inner join clinic_investigations b on a.investigation_id=b.investigation_id ".$cond)->result_array();
	$cinvg='';
	foreach($clinic_invg as $result)
	{
		if($cinvg=='')
			$cinvg = $cinvg.'"'.$result['investigation'].'"';
		else
			$cinvg = $cinvg.',"'.$result['investigation'].'"';
	}
	$data['cinvg'] = $cinvg;
	$method = $this->db->query("select DISTINCT(method) as meth from clinic_inv_tmplt_parameters")->result_array();
	
	$methods = '';
	foreach($method as $result)
	{
		if($methods=='')
			$methods = $methods.'"'.$result['meth'].'"';
		else
			$methods = $methods.',"'.$result['meth'].'"';
	}
	
	$data['methods'] = $methods;
	$data['templateinfo'] = $this->db->query("select * from clinic_investigation_template where clinic_investigation_template_id=".$tid)->row();
	$data['templatelineinfo'] = $this->db->query("select * from clinic_inv_tmplt_parameters where clinic_investigation_template_id=".$tid)->result();
	$data['view'] = 'lab/template_view';
    $this->load->view('layout', $data);
}
public function mastertemplate_view($tid)
{
	$clinic_id = $this->session->userdata('clinic_id');
	$cond = '';
	if($clinic_id!=0)
		$cond = "where clinic_id=".$clinic_id;
	
	
	
	$data['methods'] = $methods;
	$data['templateinfo'] = $this->db->query("select * from lab_templates where lab_template_id=".$tid)->row();
	$data['templatelineinfo'] = $this->db->query("select * from lab_template_line_items where lab_template_id='".$tid."' order by position")->result();
	$data['view'] = 'lab/mastertemplate_view';
    $this->load->view('layout', $data);
}
public function add_clinic_template_parameters()
{
	$c_invg_id = $_POST['clinic_investigation_template_id'];
	$user_id = $this->session->userdata('user_id');
	$template_type = $_POST['template_type'];
	$this->db->query("delete from clinic_inv_tmplt_parameters where clinic_investigation_template_id=".$c_invg_id);
	if($template_type=='Excel')
	{
		$invg_ids = count($_POST['parameter']);		
		for($i=0;$i<$invg_ids;$i++){
			$lineinfo['clinic_investigation_template_id'] = $c_invg_id;
			$lineinfo['parameter'] = $_POST['parameter'][$i];
			$lineinfo['low'] = $_POST['low'][$i];	
			$lineinfo['high'] = $_POST['high'][$i];	
			$lineinfo['unit'] = $_POST['unit'][$i];	
			$lineinfo['method'] = $_POST['method'][$i];	
			$lineinfo['other_information'] = $_POST['other_information'][$i];	
			$lineinfo['status'] = 1;
			$lineinfo['created_by'] = $user_id;
			$lineinfo['modified_by'] = $user_id;
			$lineinfo['created_date_time'] = date("Y-m-d H:i:s");
			$lineinfo['modified_date_time'] = date("Y-m-d H:i:s");
			$this->Generic_model->insertDataReturnId("clinic_inv_tmplt_parameters",$lineinfo);	
		}
	}
	else if($template_type=='General')
	{
		$invg_ids = count($_POST['parameter']);
		for($i=0;$i<$invg_ids;$i++){
			$lineinfo['clinic_investigation_template_id'] = $c_invg_id;
			$lineinfo['parameter'] = $_POST['parameter'][$i];			
			$lineinfo['remarks'] = $_POST['remarks'][$i];	
			$lineinfo['status'] = 1;
			$lineinfo['created_by'] = $user_id;
			$lineinfo['modified_by'] = $user_id;
			$lineinfo['created_date_time'] = date("Y-m-d H:i:s");
			$lineinfo['modified_date_time'] = date("Y-m-d H:i:s");
			$this->Generic_model->insertDataReturnId("clinic_inv_tmplt_parameters",$lineinfo);	
		}
	}
	redirect('Lab/template_view/'.$c_invg_id);
}

public function update_parameter_order(){
	$position = $this->input->post("position");

$i=1;
foreach($position as $k=>$v){
    $this->db->query("Update lab_template_line_items SET position='".$i."' WHERE lab_template_line_item_id='".$v."'");

    $i++;
}
}

public function add_template_parameters()
{
	$c_invg_id = $_POST['lab_template_id'];
	$user_id = $this->session->userdata('user_id');
	$template_type = $_POST['template_type'];
	//echo "<pre>";print_r($_POST);exit;
	$this->db->query("delete from lab_template_line_items where lab_template_id=".$c_invg_id);
	if($template_type=='Excel')
	{
		$invg_ids = count($_POST['parameter']);	
		for($i=0;$i<$invg_ids;$i++){
			$lineinfo['lab_template_id'] = $c_invg_id;
			$ingname = str_replace("'", "''", $_POST['parameter'][$i]);
			$ingv = $this->db->query("select * from investigations where investigation='".$ingname."'")->row();
			$last_id = $this->db->query("select * from investigations")->result_array();
			$lcount = count($last_id);
			if(count($ingv)>0)
				$lineinfo['investigation_id'] = $ingv->investigation_id;
			else{
				$insinfo['item_code'] = "UMD".($lcount+1);
				$insinfo['investigation'] = $ingname;				
				$insinfo['category'] = "Lab";
				$insinfo['status'] = 1;
				$insinfo['review'] = 1;
				$insinfo['created_by'] = $user_id;
				$insinfo['modified_by'] = $user_id;
				$insinfo['created_date_time'] = date("Y-m-d H:i:s");
				$insinfo['modified_date_time'] = date("Y-m-d H:i:s");
				$last_inserted_id = $this->Generic_model->insertDataReturnId("investigations",$insinfo);
				$lineinfo['investigation_id'] = $last_inserted_id;
				$this->investigation_json();
			}
			$lineinfo['parameter'] = $_POST['parameter'][$i];
				
			$lineinfo['status'] = 1;
			$lineinfo['created_by'] = $user_id;
			$lineinfo['modified_by'] = $user_id;
			$lineinfo['created_date_time'] = date("Y-m-d H:i:s");
			$lineinfo['modified_date_time'] = date("Y-m-d H:i:s");
			$this->Generic_model->insertDataReturnId("lab_template_line_items",$lineinfo);
			//echo $this->db->last_query()."<br />";
		}
	}
	else if($template_type=='General')
	{
		$invg_ids = count($_POST['parameter']);
		for($i=0;$i<$invg_ids;$i++){
			$lineinfo['lab_template_id'] = $c_invg_id;
			$ingname = $_POST['parameter'][$i];
			$ingv = $this->db->query("select * from investigations where investigation='".$ingname."'")->row();
			if(count($ingv)>0)
				$lineinfo['investigation_id'] = $ingv->investigation_id;
			else
				$lineinfo['investigation_id'] = 0;
			$lineinfo['parameter'] = $_POST['parameter'][$i];			
			$lineinfo['remarks'] = $_POST['remarks'][$i];	
			$lineinfo['status'] = 1;
			$lineinfo['created_by'] = $user_id;
			$lineinfo['modified_by'] = $user_id;
			$lineinfo['created_date_time'] = date("Y-m-d H:i:s");
			$lineinfo['modified_date_time'] = date("Y-m-d H:i:s");
			$this->Generic_model->insertDataReturnId("lab_template_line_items",$lineinfo);	
		}
	}
	redirect('Lab/mastertemplate_view/'.$c_invg_id);
}

//creating json with investigation masters
    public function investigation_json()
    {
        $investigation_list = $this->db->query("select investigation from investigations")->result();
        
        $prefix = '';
        $prefix .= '[';
        foreach ($investigation_list as $row) {
            $prefix .= json_encode($row->investigation);
            $prefix .= ',';
        }
        $prefix .= ']';
        
        $json_file = str_replace(",]", "]", trim($prefix, ","));
        
        $path_user = './uploads/investigation.json';
        
        if (!file_exists($path_user)) {
            $fp = fopen('./uploads/investigation.json', 'w');
            fwrite($fp, $json_file);
        } else {
            unlink($path_user);
            $fp = fopen('./uploads/investigation.json', 'w');
            fwrite($fp, $json_file);
        }
   
    }

public function findinvestigation()
{
	$clinic_id = $this->session->userdata('clinic_id');
	$parameter = $_POST['info'];
	$data = $this->db->query("select * from clinic_inv_tmplt_parameters a inner join clinic_investigation_template b on a.clinic_investigation_template_id=b.clinic_investigation_template_id where a.parameter='".$parameter."' and clinic_id=".$clinic_id)->row();	
	if(count($data)>0)
	{
		echo $data->low.":".$data->high.":".$data->unit.":".$data->method.":".$data->other_information;
	}
	else
	{
		echo "";
	}
}
public function master_template()
{
	$cond = '';
	if($clinic_id!=0)
		$cond = "and clinic_id=".$clinic_id;
	$data['clinic_templates'] = $this->db->query("select * from lab_templates where archive=0 ".$cond)->result_array();
	
	$data['view'] = 'lab/master_templates_list';
	

    $this->load->view('layout', $data);
}
}
?>