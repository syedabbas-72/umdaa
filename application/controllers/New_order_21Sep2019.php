
<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class New_order extends MY_Controller {
public function __construct() 
{
    parent::__construct();
}
public function index(){
	$clinic_id = $this->session->userdata('clinic_id');
$cond = '';
if($clinic_id!=0)
	$cond = "where b.clinic_id=".$clinic_id." and b.billing_type='Pharmacy'";
else
	$cond = "where b.billing_type='Pharmacy'";
	
$data['billing'] = $this->db->query("SELECT b.*,p.first_name as pname,c.clinic_name,sum(bi.amount) as bamount
FROM `billing` b
left join billing_line_items bi on b.billing_id=bi.billing_id
left join patients p on p.patient_id=b.patient_id
left join clinics c on c.clinic_id = b.clinic_id ".$cond." and b.status!=0
group by bi.billing_id order by b.billing_id desc")->result();
/*  "SELECT a.*,c.clinic_name FROM `appointments` a 
left join clinics c on a.clinic_id = c.clinic_id 
left join patients p on p.patient_id = a.patient_id
left join doctors d on d.doctor_id = a.doctor_id
order by a.appointment_id desc"*/
	$data['view'] = 'billing/billing';
    $this->load->view('layout', $data);
}

public function add_order($pdid=NULL){
	$clinic_id = $this->session->userdata('clinic_id');
	$patients = $this->db->query("select * from patients")->result_array();
	$data['pname'] = '';
	$data['pdid'] = $pdid;
	$pharmacy_discount = $this->session->userdata('pharmacy_discount');
	foreach($patients as $presult)
	{
		if($presult['first_name']!=''){
			$name = $presult['first_name'];
			if($data['pname']==''){				
				$data['pname'] = $data['pname'].'"'.$name.'"';
			}
			else{
				$data['pname'] = $data['pname'].',"'.$name.'"';
			}
		}
		if($presult['mobile']!=''){
			if($data['pname']==''){				
				$data['pname'] = $data['pname'].'"'.$presult['mobile'].'"';
			}
			else{
				$data['pname'] = $data['pname'].',"'.$presult['mobile'].'"';
			}
		}
	}	  
	$trade_names = $this->db->query("select trade_name,formulation from drug where drug_id<=500")->result_array();
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

	if($pdid!=NULL)
	{
		
		// $pddrugs = $this->db->query("select * from patient_prescription a inner join patient_prescription_drug b on a.patient_prescription_id=b.patient_prescription_id inner join clinic_pharmacy_inventory_inward c on b.drug_id=c.drug_id inner join drug d on c.drug_id=d.drug_id where a.patient_prescription_id=".$pdid." and c.clinic_id=".$clinic_id." group by c.drug_id")->result_array();
		// echo $this->db->last_query();

		$prescriptionDrugs = $this->db->query("select PP.patient_id, PP.appointment_id, PP.doctor_id, PP.clinic_id, PPD.drug_id, PPD.quantity, CPI.igst, CPI.cgst, CPI.sgst, CPI.max_discount_percentage,D.formulation,D.trade_name from patient_prescription `PP` JOIN patient_prescription_drug PPD on PP.patient_prescription_id = PPD.patient_prescription_id Join clinic_pharmacy_inventory CPI on PPD.drug_id = CPI.drug_id join drug D on CPI.drug_id = D.drug_id where PP.patient_prescription_id = ".$pdid)->result_array();

		$i=0;

		foreach($prescriptionDrugs as $drugRec)
		{


			$data['pdrugs'][$i] = $drugRec;

			// Get Patient details
			$patientInfo = $this->db->select('patient_id,first_name, last_name, mobile, alternate_mobile')->from('patients')->where('patient_id =',$drugRec['patient_id'])->get()->row();

			$data['pdrugs'][$i]['patient_name'] = ucwords($patientInfo->first_name.' '.$patientInfo->last_name);
			
			if($patientInfo->mobile != '' || $patientInfo->mobile != NULL ){
				$data['pdrugs'][$i]['mobile'] = DataCrypt($patientInfo->mobile,'decrypt');	
			}else if($patientInfo->alternate_mobile != '' || $patientInfo->alternate_mobile != NULL ){
				$data['pdrugs'][$i]['mobile'] = DataCrypt($patientInfo->alternate_mobile,'decrypt');	
			}

			if($this->session->userdata('pharmacy_discount') != '' || $this->session->userdata('pharmacy_discount') != NULL){
				$pharmacy_discount = $this->session->userdata('pharmacy_discount');	
			}else{
				$pharmacy_discount = 10;
			}

			$maxDiscount = $drugRec['max_discount_percentage'];

			if($maxDiscount <= $pharmacy_discount)
				$discount = $drugRec['max_discount_percentage'];
			else
				$discount = $pharmacy_discount;

			$data['pdrugs'][$i]['discount'] = $discount;

			// Check the stock availability in the clinic pharmacy inventory
			$stockInfo = $this->db->query("SELECT inward.batch_no, sum(inward.quantity) as inward_qty, IFNULL(SUM(inward.quantity)-(SELECT SUM(outward.quantity) 
				FROM clinic_pharmacy_inventory_outward AS outward WHERE inward.drug_id=outward.drug_id and inward.batch_no = outward.batch_no), sum(inward.quantity)) as available_qty, inward.drug_id FROM clinic_pharmacy_inventory_inward AS inward where inward.drug_id = ".$drugRec['drug_id']." and inward.status = 1 and inward.expiry_date > CURRENT_DATE GROUP BY inward.batch_no")->result_array();

			$totalQtyAvailable = 0;
			if(count($stockInfo) > 0){
				for($x=0; $x<count($stockInfo); $x++){
					$totalQtyAvailable = $totalQtyAvailable + (int)$stockInfo[$x]['available_qty'];
				}
			}

			$data['pdrugs'][$i]['stock'] = $totalQtyAvailable;

			// $data['pdrugs'][$i] = $pdresult;
			/*
			// check the drug availability in the inventory w.r.to clinic
			$drugRec = $this->db->query("select * from clinic_pharmacy_inventory where drug_id = ".$pdresult->drug_id." and clinic_id=".$clinic_id)->row();	
		
			// if the drug exists in the clinic pharmacy then get the maximum discount on the drug
			if(count($drugRec)>0){

				$sch_salt = array();
				
				if($drugInfo->salt_id == "" || $drugInfo->salt_id == NULL){
					$scheduled_salt = "";
				}else{
					$imp =  "'".implode("','", explode(",", $drugInfo->salt_id))."'";
					$salt_id = $this->db->query("SELECT GROUP_CONCAT(scheduled_salt) scheduled_salt FROM `salt` where salt_id in ($imp)")->row();
		            $scheduled_salt = trim($salt_id->scheduled_salt,",");
		            $explode = explode(",",$scheduled_salt);
		            foreach ($explode as $key => $svalue) {
		            	$sch_salt[] = "<span id=".trim($svalue).">".trim($svalue)."</span>";
		            }
				}
					
				$maxDiscount = $drugRec->max_discount_percentage;

				if($maxDiscount <= $pharmacy_discount)
					$discount = $drugRec->max_discount_percentage;
				else
					$discount = $pharmacy_discount;

				// Check the stock availability in the clinic pharmacy inventory
				$stockInfo = $this->db->query("SELECT inward.batch_no, sum(inward.quantity) as inward_qty, IFNULL(SUM(inward.quantity)-(SELECT SUM(outward.quantity) 
	                   FROM clinic_pharmacy_inventory_outward AS outward WHERE inward.drug_id=outward.drug_id and inward.batch_no = outward.batch_no), sum(inward.quantity)) as available_qty, inward.drug_id FROM clinic_pharmacy_inventory_inward AS inward where inward.drug_id = ".$drugInfo->drug_id." and inward.status = 1 and inward.expiry_date > CURRENT_DATE GROUP BY inward.batch_no")->result_array();
				$totalQtyAvailable = 0;
				if(count($stockInfo) > 0){
					for($x=0; $x<count($stockInfo); $x++){
						$totalQtyAvailable = $totalQtyAvailable + (int)$stockInfo[$x]['available_qty'];
					}
				}
			
				echo $discount.":".$drugInfo->drug_id.":".$drugInfo->formulation.":".$drugInfo->composition.":".implode(" ",$sch_salt).":".$totalQtyAvailable;



			// $sch_salt = array();
			// $dis_amt = $this->db->query("select * from clinic_pharmacy_inventory where drug_id = ".$pdresult['drug_id']." and clinic_id=".$clinic_id)->row();
			// if($pdresult['salt_id'] == "" || $pdresult['salt_id'] == NULL){
			// 	$scheduled_salt = "";
			// }
			// else{
			// 	$imp =  "'".implode("','", explode(",", $pdresult['salt_id']))."'";
			// 	$salt_id = $this->db->query("SELECT GROUP_CONCAT(scheduled_salt) scheduled_salt FROM `salt` where salt_id in ($imp)")->row();
			// 	$scheduled_salt = trim($salt_id->scheduled_salt,",");
			// 	$explode = explode(",",$scheduled_salt);
			// 	foreach ($explode as $key => $svalue) {
			// 		$sch_salt[] = "<span id=".trim($svalue).">".trim($svalue)."</span>";
			// 	}
			// }
			// $data['pdrugs'][$i]['salt'] = implode(" ",$sch_salt);
			// $maxDiscount = $dis_amt->max_discount_percentage;

			// if($maxDiscount <= $pharmacy_discount)
			// 	$discount = $dis_amt->max_discount_percentage;
			// else
			// 	$discount = $pharmacy_discount;
			// $data['pdrugs'][$i]['discount'] = $discount;
			*/
			$i++;
		}
	}
	//echo "test<pre>";print_r($data['pdrugs']);exit;
	$master_version = $this->db->query("select * from master_version where clinic_id='".$clinic_id."' and master_name='clinic_inventory'")->row();
	$data['clinic_inventory_json_file_name'] = base_url().'uploads/clinic_inventory_json/'.$master_version->json_file_name;
	$data['view'] = 'new_order/new_orders';
    $this->load->view('layout', $data);
}

public function getpatient_details()
{
	$info = $_POST['info'];
	$pinfo = $this->db->query("select * from patients where first_name='".$info."'")->row();
	$pinfo_num = $this->db->query("select * from patients where mobile='".$info."'")->row();
	if(count($pinfo)>0)
	{
		$name = $pinfo->first_name." ".$pinfo->middle_name." ".$pinfo->last_name;
		echo "<table width='300' border='1' cell-spacing='0'><tr><td>".$name."<td><td>".$pinfo->mobile."</td></tr></table><input type='hidden' name='pid' value='".$pinfo->patient_id."' />";
	}
	else if(count($pinfo_num)>0)
	{
		$name = $pinfo_num->first_name." ".$pinfo_num->middle_name." ".$pinfo_num->last_name;
		echo "<table width='300' border='1' cell-spacing='0'><tr><td>".$name."<td><td>".$pinfo_num->mobile."</td></tr></table><input type='hidden' name='pid' value='".$pinfo->patient_id."' />";
	}
	else
	{
		echo "<input type='button' class='btn btn-success' value='New Patient' onclick='show_new_info();' />";
	}
}


public function show_new_info()
{
	echo "<table width='300' border='1' cell-spacing='0'><tr><td><input type='text' name='pname' placeholder='Patient Name' /><td><td><input type='text' name='pmobile' placeholder='Patient Mobile' /></td></tr></table>";
}


public function get_drug_info()
{
	$clinic_id = $this->session->userdata('clinic_id');
	$pharmacy_discount = $this->session->userdata('pharmacy_discount');

	extract($_POST);
	
	// get drug information from the drug master
	$drugInfo = $this->db->query("select * from drug where trade_name='".trim($trade_name)."' AND formulation = '".trim($formulation)."'")->row();
	
	if(count($drugInfo) > 0) { // if drug exists in the drug master

		// check the drug availability in the inventory w.r.to clinic
		$drugRec = $this->db->query("select * from clinic_pharmacy_inventory where drug_id = ".$drugInfo->drug_id." and clinic_id=".$clinic_id)->row();	
	
		// if the drug exists in the clinic pharmacy then get the maximum discount on the drug
		if(count($drugRec)>0){

			$sch_salt = array();
			
			if($drugInfo->salt_id == "" || $drugInfo->salt_id == NULL){
				$scheduled_salt = "";
			}else{
				$imp =  "'".implode("','", explode(",", $drugInfo->salt_id))."'";
				$salt_id = $this->db->query("SELECT GROUP_CONCAT(scheduled_salt) scheduled_salt FROM `salt` where salt_id in ($imp)")->row();
	            $scheduled_salt = trim($salt_id->scheduled_salt,",");
	            $explode = explode(",",$scheduled_salt);
	            foreach ($explode as $key => $svalue) {
	            	$sch_salt[] = "<span id=".trim($svalue).">".trim($svalue)."</span>";
	            }
			}
				
			$maxDiscount = $drugRec->max_discount_percentage;

			if($maxDiscount <= $pharmacy_discount)
				$discount = $drugRec->max_discount_percentage;
			else
				$discount = $pharmacy_discount;

			// Check the stock availability in the clinic pharmacy inventory
			$stockInfo = $this->db->query("SELECT inward.batch_no, sum(inward.quantity) as inward_qty, IFNULL(SUM(inward.quantity)-(SELECT SUM(outward.quantity) 
                   FROM clinic_pharmacy_inventory_outward AS outward WHERE inward.drug_id=outward.drug_id and inward.batch_no = outward.batch_no), sum(inward.quantity)) as available_qty, inward.drug_id FROM clinic_pharmacy_inventory_inward AS inward where inward.drug_id = ".$drugInfo->drug_id." and inward.status = 1 and inward.expiry_date > CURRENT_DATE GROUP BY inward.batch_no")->result_array();
			$totalQtyAvailable = 0;
			if(count($stockInfo) > 0){
				for($x=0; $x<count($stockInfo); $x++){
					$totalQtyAvailable = $totalQtyAvailable + (int)$stockInfo[$x]['available_qty'];
				}
			}
		
			echo $discount.":".$drugInfo->drug_id.":".$drugInfo->formulation.":".$drugInfo->composition.":".implode(" ",$sch_salt).":".$totalQtyAvailable;
			
		}else{ // if the drug not available in the clinic pharmacy inventory
			echo "NA";
			exit;
		}
		
	}
	else
	{		
		echo 'NO Records found';
	}
}


public function get_batch_details()
{
	$clinic_id = $this->session->userdata('clinic_id');
	$did = $_POST['drug'];
	$batches = $this->db->query("select *,sum(quantity) as oqty from clinic_pharmacy_inventory_inward where drug_id=".$did." and clinic_id=".$clinic_id." and archieve=0 group by batch_no")->result_array();
	echo "<table class='table'><tr><td>Batches</td><td>Available Quantity</td><td>Required Quantity</td></tr>";
	foreach($batches as $result)
	{
		$oqty = $this->db->query("select sum(quantity) as ouqty from clinic_pharmacy_inventory_outward where drug_id=".$did." and batch_no='".$result['batch_no']."' and clinic_id=".$clinic_id)->row();
		$aqty = ($result['oqty']-$oqty->ouqty);
		$unitp = $result['mrp']/$result['pack_size'];
		if($aqty == 0)
			continue;
		echo "<tr><td><input type='checkbox' class='batch_cb' value='".trim($result['batch_no'])."' name='batchno[]' onclick='enable_text_box(".'"'.stripslashes($result['batch_no']).'"'.")' id='batchno_".trim($result['batch_no'])."' />Batch".trim(stripslashes($result['batch_no']))."</td><td>".$aqty."</td><td><input type='textbox' id='bqty_".trim($result['batch_no'])."' readonly onkeyup='checkvalue(".$aqty.",\"".trim(stripslashes($result['batch_no']))."\");' /><input type='hidden' name='unitp' id='unitp_".stripslashes($result['batch_no'])."' value='".$unitp."' /></td></tr>";
	}
	echo "<table>";
}

public function save_order()
{
	$clinic_id = $this->session->userdata('clinic_id');
	$user_id = $this->session->userdata('user_id');
	//echo "<pre>";print_r($_POST);exit;

	if(isset($_POST['pid']))
		$patient_id = $_POST['pid'];
	else
		$patient_id = '';

	//Generate Receipt No.
	$last_id = $this->db->query("select * from billing where receipt_no like 'RECEIPT-".$clinic_id."-%' and status!=0")->result_array();
	$lcount = count($last_id);
	$data['receipt_no'] = 'RECEIPT-'.$clinic_id."-".($lcount+1);
	$data['invoice_no'] = 'INV-'.$clinic_id."-".($lcount+1);
	$data['clinic_id'] = $clinic_id;

	if($patient_id != ''){
		$pinfo = $this->db->query("select * from patients where patient_id=".$patient_id)->row();
		$data['patient_id'] = $patient_id;
		$data['umr_no'] = $pinfo->umr_no;
	}else{
		$data['guest_name'] = $_POST['pname'];
		$data['guest_mobile'] = $_POST['pmobile'];
	}

	$data['billing_type'] = 'Pharmacy';
	$data['patient_prescription_id'] = ($_POST['pdid']!=NULL?$_POST['pdid']:0);
	$data['billing_date_time'] = date("Y-m-d H:i:s");
	$data['discount_status'] = $_POST['apdis'];
	$data['status'] = 1;
	$data['created_by'] = $user_id;
	$data['modified_by'] = $user_id;
	$data['created_date_time'] = date("Y-m-d H:i:s");
	$data['modified_date_time'] = date("Y-m-d H:i:s");

	// inserting biling master record
	// capturing last inserted billing id
	$last_inserted_id = $this->Generic_model->insertDataReturnId("billing",$data);
	$billing_id = $last_inserted_id;

	$drug_ids = count($_POST['totrw']);

	for($i=0;$i<$drug_ids;$i++){

		$batch_array = explode(",",$_POST['qty'][$i]);
		$drug_info = $this->db->query("SELECT * FROM drug where drug_id=".$_POST['totrw'][$i])->row();
		$gsts_info = $this->db->query("select * from clinic_pharmacy_inventory where clinic_id=".$clinic_id." and drug_id=".$drug_info->drug_id)->row();
		
		//echo $this->db->last_query();

		$invoice_amount = 0;

		for($j=0;$j<count($batch_array);$j++)
		{

			$batchList = explode(" :: ",$batch_array[$j]);
			//$batchno = explode("-",$batchli[0]);
			$batchno = $batchList[0];
			$reqQty = $batchList[1];
			//echo "<pre>";print_r($batchno);exit;
			// $unitp = $this->db->query("select * from clinic_pharmacy_inventory_inward where drug_id=".$drug_info->drug_id." and clinic_id=".$clinic_id." and batch_no='".$batchno."'")->row();
			$drugRec = $this->db->query("select * from clinic_pharmacy_inventory_inward where drug_id=".$drug_info->drug_id." and clinic_id=".$clinic_id." and batch_no='".trim($batchno)."' and archieve=0")->row();

			/*echo '<pre>';
			echo "select * from clinic_pharmacy_inventory_inward where drug_id=".trim($drug_info->drug_id)." and clinic_id=".$clinic_id." and batch_no='".trim($batchno)."'<br>";
			echo 'Drug Record: <br>';
			print_r($drugRec);*/
			//exit();

			// $unitpr = ($unitp->mrp/$unitp->pack_size);
			$unitPrice = ($drugRec->mrp/$drugRec->pack_size);
			//$price = $batchli[1] * $unitpr;
			$price = $reqQty * $unitPrice;

			// accountable price if any discounts applying
			$accountablePrice =  $price - ($price * ($_POST['disc'][$i] / 100));
        	//$discount_price =  $price - ($price * ($_POST['disc'][$i] / 100));

        	// Taxation
        	// Value inclding GST = mrp ($accountablePrice)
        	// TaxValue = (mrp * 100)/(100 + CGST + SGST)
        	$taxValue = ($accountablePrice * 100)/(100 + $gsts_info->cgst + $gsts_info->sgst + $gsts_info->igst);

        	// $cgst = round($discount_price * ($gsts_info->cgst / 100),2);
        	// $sgst = round($discount_price * ($gsts_info->sgst/ 100),2);
        	// $igst = round($discount_price * ($gsts_info->igst / 100),2);

	       	//$total = $discount_price + $cgst + $sgst + $igst;

	       	$invoice_amount = $invoice_amount+$accountablePrice;
			
			$lineinfo['billing_id'] = $billing_id;
			$lineinfo['item_information'] = $drug_info->trade_name;
			$lineinfo['drug_id'] = $drug_info->drug_id;
			$lineinfo['hsn_code'] = $drug_info->hsn_code;
			$lineinfo['batch_no'] = $batchno;
			$lineinfo['quantity'] = $reqQty;
			$lineinfo['discount'] = $_POST['disc'][$i];
			$lineinfo['discount_unit'] = '%';
			$lineinfo['unit_price'] = round(($drugRec->mrp/$drugRec->pack_size),2);
			$lineinfo['igst'] = $gsts_info->igst;
			$lineinfo['cgst'] = $gsts_info->cgst;
			$lineinfo['sgst'] = $gsts_info->sgst;
			$lineinfo['amount'] = $accountablePrice; 
			$lineinfo['status'] = 1;
			$lineinfo['created_by'] = $user_id;
			$lineinfo['modified_by'] = $user_id;
			$lineinfo['created_date_time'] = date("Y-m-d H:i:s");
			$lineinfo['modified_date_time'] = date("Y-m-d H:i:s");

			// echo '<pre>';
			// echo 'Billing Line Items: <br>';
			// print_r($lineinfo);
			// echo '<br><br>';
			// exit();

			// insert data into Billing Line items table
			$this->Generic_model->insertDataReturnId("billing_line_items",$lineinfo);

			// Gather information for Clinic Pharmacy Inventory Outward
			$outward['clinic_id'] = $clinic_id;
			$outward['drug_id'] = $_POST['totrw'][$i];
			$outward['batch_no'] = $batchno;
			$outward['outward_date'] = date("Y-m-d");
			$outward['quantity'] = $reqQty;
			$outward['status'] = 1;
			$outward['billing_id'] = $billing_id;
			$outward['created_by'] = $user_id;
			$outward['modified_by'] = $user_id;
			$outward['created_date_time'] = date("Y-m-d H:i:s");
			$outward['modified_date_time'] = date("Y-m-d H:i:s");

			/*echo '<pre>';
			echo 'Inventory Outward: <br>';
			print_r($outward);
			echo '<br><br>';*/			

			// insert data into Clinic Pharmacy Inventory Outward
			$this->Generic_model->insertDataReturnId("clinic_pharmacy_inventory_outward",$outward);
			$outqty = $this->db->query("select sum(quantity) as oqty from clinic_pharmacy_inventory_outward where drug_id=".$drug_info->drug_id." and batch_no='".$batchno."' and clinic_id=".$clinic_id)->row();
			$iqty = $this->db->query("select sum(quantity) as iqty from clinic_pharmacy_inventory_inward where drug_id=".$drug_info->drug_id." and batch_no='".$batchno."' and archieve=0 and clinic_id=".$clinic_id)->row();
			if($outqty->oqty>=$iqty->iqty)
			{
				$this->db->query("update clinic_pharmacy_inventory_inward set status=0 where drug_id=".$drug_info->drug_id." and batch_no='".$batchno."' and archieve=0 and clinic_id=".$clinic_id);
			}
		}

		// update invoice amount in the billing table for total_amount field for billing_id
		$this->db->query("UPDATE billing SET total_amount = '".$invoice_amount."' WHERE billing_id='".$billing_id."'");
		
	}

	redirect('New_order/view_order/'.$last_inserted_id);

}

public function print_bill($bid)
{
	$this->db->query("update billing set payment_mode='".$this->input->post('payment_mode')."',payment_status = 1 where billing_id=".$bid);
	$pdf_path = $this->generatepdf($bid);
	$this->db->query("update billing set invoice_pdf='".$pdf_path."' where billing_id=".$bid);
	redirect('uploads/billings/'.$pdf_path);
}

public function print_bill2($bid)
{
	$this->db->query("update billing set payment_mode='".$this->input->post('payment_mode')."' AND payment_status = 1 where billing_id=".$bid);
	$pdf_path = $this->generatepdf1($bid);
	$this->db->query("update billing set invoice_pdf='".$pdf_path."' where billing_id=".$bid);
	redirect('uploads/billings/'.$pdf_path);
}

public function view_order($bid)
{
	$data['billing_master'] = $this->db->query("select * from billing where billing_id=".$bid)->row();
	$data['billing_line_items'] = $this->db->query("select * from billing_line_items where billing_id=".$bid)->result();

	$data['view'] = 'new_order/view_order';
    $this->load->view('layout', $data);
}
public function drop_pharmacy_invoice()
{
	$bid = $_POST['bid'];
	$clinic_id = $this->session->userdata('clinic_id');
	$user_id = $this->session->userdata('user_id');
	$this->db->query("update billing set status=0 where billing_id=".$bid);
	$billing_lineitems = $this->db->query("select * from billing_line_items where billing_id=".$bid)->result();
	foreach($billing_lineitems as $bresult)
	{
		$this->db->query("delete from clinic_pharmacy_inventory_outward where drug_id=".$bresult->drug_id." and batch_no='".$bresult->batch_no."' and clinic_id=".$clinic_id." and billing_id=".$bid);
		$outqty = $this->db->query("select sum(quantity) as oqty from clinic_pharmacy_inventory_outward where drug_id=".$bresult->drug_id." and batch_no='".$bresult->batch_no."' and clinic_id=".$clinic_id)->row();
		$iqty = $this->db->query("select sum(quantity) as iqty from clinic_pharmacy_inventory_inward where drug_id=".$bresult->drug_id." and batch_no='".$bresult->batch_no."' and archieve=0 and clinic_id=".$clinic_id)->row();
		if($outqty->oqty>=$iqty->iqty)
		{
			$this->db->query("update clinic_pharmacy_inventory_inward set status=0 where drug_id=".$drug_info->drug_id." and batch_no='".$batchno."' and archieve=0 and clinic_id=".$clinic_id);
		}
		else
		{
			$this->db->query("update clinic_pharmacy_inventory_inward set status=1 where drug_id=".$bresult->drug_id." and batch_no='".$bresult->batch_no."' and archieve=0 and clinic_id=".$clinic_id);
		}
	}
	
	echo "result";
}
public function generatepdf($bid)
{
	$clinic_id = $this->session->userdata('clinic_id');
	$data['billing_master'] = $this->db->query("select * from billing where billing_id=".$bid)->row();
	$data['billing_line_items'] = $this->db->query("select * from billing_line_items where billing_id=".$bid)->result();
	$data['pdf_settings'] = $pdf_settings = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$clinic_id."'")->row();
	if($data['billing_master']->patient_id!=''){
		$pinfo = $this->db->query("select * from patients where patient_id=".$data['billing_master']->patient_id)->row();
		$data['patient_name'] = $pinfo->first_name." ".$pinfo->middle_name." ".$pinfo->last_name;
		$data['gender'] = $pinfo->gender;
		$data['age'] = $pinfo->age;
		$data['patient_id'] = $pinfo->umr_no;
		$data['paddress'] = $pinfo->address_line;
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
    $html = $this->load->view('new_order/order_invoice',$data,true);
    $pdfFilePath = $data['billing_master']->invoice_no.".pdf";
    $stylesheet = file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
    $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");
    $this->m_pdf->pdf->autoScriptToLang = true;
    $this->m_pdf->pdf->autoLangToFont = true;

    $this->m_pdf->pdf->shrink_tables_to_fit = 1;
    $this->m_pdf->pdf->setAutoTopMargin = "stretch";
    $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
    $this->m_pdf->pdf->defaultheaderline = 0;

	$this->m_pdf->pdf->WriteHTML($stylesheet,1);
    $this->m_pdf->pdf->WriteHTML($html,2);
    $this->m_pdf->pdf->Output("./uploads/prescriptions/".$pdfFilePath, "F");
    $this->m_pdf->pdf->Output("./uploads/billings/".$pdfFilePath, "F");
	return $pdfFilePath;
}

public function getMasterVersion($master_name){
	$clinic_id = $this->session->userdata("clinic_id");

	$master_version = $this->db->query("select * from master_version where clinic_id='".$clinic_id."' and master_name='".$master_name."'")->row();
	echo base_url().'uploads/clinic_inventory_json/'.$master_version->json_file_name;
}


}