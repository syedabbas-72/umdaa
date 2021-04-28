
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
		$data['view'] = 'billing/billing';
		$this->load->view('layout', $data);
	}

	// Get Drugs From Masters
    public function getDrugs(){
        extract($_POST);
        $druginfo = $this->db->query("select trade_name,drug_id,formulation from drug where trade_name LIKE '%".$searchParam."%' order by trade_name ASC LIMIT 30")->result();
        echo $druginfo;

    }

	//Get Patient Info through Mobile Number
	public function getPatientInfo(){
		$clinic_id = $this->session->userdata("clinic_id");
		extract($_POST);
		if(isset($_POST))
		{
			$mobile_input = DataCrypt($mobile,'encrypt');
			$patientInfo = $this->db->select("first_name,last_name,mobile,umr_no,patient_id,title")->from("patients")->where("mobile='".$mobile_input."' or alternate_mobile='".$mobile_input."'")->get()->result();
			if(count($patientInfo)>0)
			{
				$i = 0;
				foreach ($patientInfo as $value) 
				{
					if($value->title == "")
						$title = "";
					else
						$title = $value->title.". ";
					$data[$i]['customer_name'] = $title.ucwords(strtolower($value->first_name." ".$value->last_name));
					$data[$i]['mobile'] = DataCrypt($value->mobile,'decrypt');
					$data[$i]['umr_no'] = $value->umr_no;
					$data[$i]['patient_id'] = $value->patient_id;
					$i++;
				}
			}
			else
			{
				if($from == "ph")
				{
					$patientInfo = $this->db->select("guest_name,guest_mobile")->from("billing")->where("guest_mobile='".$mobile."'")->get()->row();	
					$data['customer_name'] = $patientInfo->guest_name;
					$data['mobile'] = $patientInfo->guest_mobile;
				}				
			}
			$json = json_encode($data);

			echo count($data)."-".json_encode($data);
		}
		else
		{
			echo "Unauthorized Access";
		}
	}

	public function add_order($pdid=NULL){
		
		$clinic_id = $this->session->userdata('clinic_id');
		// $patients = $this->db->query("select * from patients")->result();
		$data['pname'] = '';
		$data['pdid'] = $pdid;
		$pharmacy_discount = $this->session->userdata('pharmacy_discount');
		// foreach($patients as $presult)
		// {
		// 	if($presult['first_name']!=''){
		// 		$name = $presult['first_name'];
		// 		if($data['pname']==''){				
		// 			$data['pname'] = $data['pname'].'"'.$name.'"';
		// 		}
		// 		else{
		// 			$data['pname'] = $data['pname'].',"'.$name.'"';
		// 		}
		// 	}
		// 	if($presult['mobile']!=''){
		// 		if($data['pname']==''){				
		// 			$data['pname'] = $data['pname'].'"'.$presult['mobile'].'"';
		// 		}
		// 		else{
		// 			$data['pname'] = $data['pname'].',"'.$presult['mobile'].'"';
		// 		}
		// 	}
		// }	  
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

			$prescriptionDrugs = $this->db->query("select PP.patient_id, PP.appointment_id, PP.doctor_id, PP.clinic_id, PPD.drug_id, PPD.quantity, CPI.igst, CPI.cgst, CPI.sgst, CPI.max_discount_percentage,D.formulation,D.trade_name from patient_prescription `PP` JOIN patient_prescription_drug PPD on PP.patient_prescription_id = PPD.patient_prescription_id Join clinic_pharmacy_inventory CPI on PPD.drug_id = CPI.drug_id join drug D on CPI.drug_id = D.drug_id where PP.patient_prescription_id = '".$pdid."' group by CPI.drug_id")->result_array();
		
			if(count($prescriptionDrugs)>0)
			{
				$data['appointment_id'] = $prescriptionDrugs[0]['appointment_id'];
				$data['doctor_id'] = $prescriptionDrugs[0]['doctor_id'];

				$i=0;

				$patientDetails = $this->db->select('patient_id,first_name, last_name, mobile, alternate_mobile,umr_no')->from('patients')->where('patient_id =',$prescriptionDrugs[0]['patient_id'])->get()->row();
				$data['patient']['patient_name'] = ucwords($patientDetails->first_name.' '.$patientDetails->last_name);
				$data['patient']['umr_no'] = $patientDetails->umr_no;
				$data['patient']['patient_id'] = $patientDetails->patient_id;

				if($patientDetails->mobile != '' || $patientDetails->mobile != NULL ){
					$data['patient']['mobile'] = DataCrypt($patientDetails->mobile,'decrypt');	
				}else if($patientDetails->alternate_mobile != '' || $patientDetails->alternate_mobile != NULL ){
					$data['patient']['mobile'] = DataCrypt($patientDetails->alternate_mobile,'decrypt');	
				}

				foreach($prescriptionDrugs as $drugRec)
				{
					$data['pdrugs'][$i] = $drugRec;

					// Get Patient details
					// $patientDetails = $this->db->select('patient_id,first_name, last_name, mobile, alternate_mobile,umr_no')->from('patients')->where('patient_id =',$drugRec['patient_id'])->get()->row();

					

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
// echo $this->db->last_query();
// exit();
					$totalQtyAvailable = 0;
					if(count($stockInfo) > 0){
						for($x=0; $x<count($stockInfo); $x++){
							$totalQtyAvailable = $totalQtyAvailable + (int)$stockInfo[$x]['available_qty'];
						}
					}

					$data['pdrugs'][$i]['stock'] = $totalQtyAvailable;
					$i++;
				}

			}
			else
			{
				$data['prescriptionDrugsCount'] = 0;
			}
		}
		$master_version = $this->db->query("select * from master_version where clinic_id='".$clinic_id."' and master_name='clinic_inventory'")->row();
		$data['clinic_inventory_json_file_name'] = base_url().'uploads/clinic_inventory_json/'.$master_version->json_file_name;
		$data['view'] = 'new_order/new_orders';
		$this->load->view('layout', $data);
	}


	public function getInventoryDrugs(){
		extract($_POST);
		$clinic_id = $this->session->userdata('clinic_id');
		$clinicCheck = $this->db->query("select *,sum(CPI.quantity) as invSum from clinic_pharmacy_inventory_inward CPI,drug d where d.drug_id=CPI.drug_id  and CPI.archieve=0 and CPI.clinic_id='".$clinic_id."' and d.trade_name LIKE '%".urldecode($drug)."%' group by CPI.batch_no,CPI.drug_id LIMIT 30")->result();
		// echo $this->db->last_query();
		if(count($clinicCheck)>0)
		{
			$i = 0;
			foreach($clinicCheck as $result)
			{
				$outward = $this->db->query("select sum(quantity) as ouqty from clinic_pharmacy_inventory_outward where drug_id=" . $result->drug_id . " and batch_no='" . $result->batch_no . "' and clinic_id=" . $result->clinic_id)->row();
				// echo $this->db->last_query();
				// echo "<br>".$result->quantity - $outward->ouqty."<br>";
				// echo $result->quantity - $outward->ouqty."<br>";
				if(($result->invSum - $outward->ouqty) <= 0)
				{
					// $qty = 0;
					continue;
				}
				else
				{
					$qty = $result->invSum - $outward->ouqty;
					if($qty <= 0)
						continue;
					else
						$qty = $qty;
				}

				// $stockInfo[$i]->clinic_id = $clinic_id;
				$stockInfo[$i]->batch_no = $result->batch_no;
				$stockInfo[$i]->drug_id = $result->drug_id;
				// $stockInfo[$i]->quantity_supplied = $result->quantity;
				$stockInfo[$i]->expiry_date = $result->expiry_date;
				// $stockInfo[$i]->status = 1;
				$stockInfo[$i]->available_quantity = $qty;
				$stockInfo[$i]->label = $result->trade_name;
				$stockInfo[$i]->formulation = $result->formulation;
				// $stockInfo[$i]->category = $result->category;
				// $ids[$i] = $result->drug_id;
				$i++; 
			}
			echo json_encode($stockInfo);
		}
		else
		{
			$stockInfo[] = "No Results Found";
			echo json_encode($stockInfo);
		}
	}

	public function get_patient_info(){
		extract($_POST);
		// echo $mobile;
		$patientInfo = $this->db->query("select * from patients where mobile='".DataCrypt($mobile, 'encrypt')."'")->result();
		// echo $this->db->last_query();
		if(count($patientInfo) > 0)
		{
			$i = 0;
			foreach($patientInfo as $value)
			{
				$data[$i]['label'] = $value->title.". ".$value->first_name." ".$value->last_name;
				$data[$i]['value'] = DataCrypt($value->mobile, 'decrypt');
				$data[$i]['pname'] = getPatientName($value->patient_id);
				$data[$i]['patient_id'] = $value->patient_id;
				$data[$i]['umr_no'] = $value->umr_no;
				$i++;
			}
		}
		else
		{

		}
		echo json_encode($data);
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
			
			if($maxDiscount == '' || $maxDiscount == NULL){
				$discount = 0;
			}else{
				$discount = $maxDiscount;
			}	

			/* Commenting this code to release the constraint on giving maximum discout figure
			// if($maxDiscount <= $pharmacy_discount)
			// 	$discount = $drugRec->max_discount_percentage;
			// else
			// 	$discount = $pharmacy_discount;
			*/

			// Check the stock availability in the clinic pharmacy inventory
			$stockInfo = $this->db->query("SELECT inward.batch_no, sum(inward.quantity) as inward_qty, IFNULL(SUM(inward.quantity)-(SELECT SUM(outward.quantity) 
				FROM clinic_pharmacy_inventory_outward AS outward WHERE inward.drug_id=outward.drug_id and inward.batch_no = outward.batch_no), sum(inward.quantity)) as available_qty, inward.drug_id FROM clinic_pharmacy_inventory_inward AS inward where inward.drug_id = ".$drugInfo->drug_id." and inward.status = 1 and inward.expiry_date > CURRENT_DATE GROUP BY inward.batch_no")->result_array();

			$batches = $this->db->query("select *,sum(quantity) as oqty from clinic_pharmacy_inventory_inward where drug_id=".$drugInfo->drug_id." and clinic_id=".$clinic_id." and archieve=0 group by batch_no")->result_array();
			$batch_count = count($batches);

			$totalQtyAvailable = 0;
			if(count($stockInfo) > 0){
				for($x=0; $x<count($stockInfo); $x++){
					$totalQtyAvailable = $totalQtyAvailable + (int)$stockInfo[$x]['available_qty'];
				}
			}
			if($batch_count == 1)
			{
				echo $discount.":".$drugInfo->drug_id.":".$drugInfo->formulation.":".$drugInfo->composition.":".implode(" ",$sch_salt).":".$totalQtyAvailable.":".$batch_count.":".$batches[0]['batch_no'];
			}
			else
			{
				echo $discount.":".$drugInfo->drug_id.":".$drugInfo->formulation.":".$drugInfo->composition.":".implode(" ",$sch_salt).":".$totalQtyAvailable.":".$batch_count;
			}

			
			
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

public function get_drug_id_info()
	{
		$clinic_id = $this->session->userdata('clinic_id');
		$pharmacy_discount = $this->session->userdata('pharmacy_discount');

		extract($_POST);

	// get drug information from the drug master
		$drugInfo = $this->db->query("select * from drug where drug_id='".$drug_id."'")->row();

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
			
			if($maxDiscount == '' || $maxDiscount == NULL){
				$discount = 0;
			}else{
				$discount = $maxDiscount;
			}	

			/* Commenting this code to release the constraint on giving maximum discout figure
			// if($maxDiscount <= $pharmacy_discount)
			// 	$discount = $drugRec->max_discount_percentage;
			// else
			// 	$discount = $pharmacy_discount;
			*/

			// Check the stock availability in the clinic pharmacy inventory
			$stockInfo = $this->db->query("SELECT inward.batch_no, sum(inward.quantity) as inward_qty, IFNULL(SUM(inward.quantity)-(SELECT SUM(outward.quantity) 
				FROM clinic_pharmacy_inventory_outward AS outward WHERE inward.drug_id=outward.drug_id and inward.batch_no = outward.batch_no), sum(inward.quantity)) as available_qty, inward.drug_id FROM clinic_pharmacy_inventory_inward AS inward where inward.drug_id = ".$drugInfo->drug_id." and inward.status = 1 and inward.expiry_date > CURRENT_DATE GROUP BY inward.batch_no")->result_array();

			$batches = $this->db->query("select *,sum(quantity) as oqty from clinic_pharmacy_inventory_inward where drug_id=".$drugInfo->drug_id." and clinic_id=".$clinic_id." and archieve=0 group by batch_no")->result_array();
			$batch_count = count($batches);

			$totalQtyAvailable = 0;
			if(count($stockInfo) > 0){
				for($x=0; $x<count($stockInfo); $x++){
					$totalQtyAvailable = $totalQtyAvailable + (int)$stockInfo[$x]['available_qty'];
				}
			}
			if($batch_count == 1)
			{
				echo $discount.":".$drugInfo->drug_id.":".$drugInfo->formulation.":".$drugInfo->composition.":".implode(" ",$sch_salt).":".$totalQtyAvailable.":".$batch_count.":".$batches[0]['batch_no'];
			}
			else
			{
				echo $discount.":".$drugInfo->drug_id.":".$drugInfo->formulation.":".$drugInfo->composition.":".implode(" ",$sch_salt).":".$totalQtyAvailable.":".$batch_count;
			}

			
			
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
	$qty = $_POST['qty'];
	$batches = $this->db->query("select *,sum(quantity) as oqty from clinic_pharmacy_inventory_inward where drug_id=".$did." and clinic_id=".$clinic_id." and archieve=0 group by batch_no")->result_array();
	$batch_count = count($batches);
	if($qty != "")
	{
		echo '<h5 class="font-weight-bold">Recommended Quantity - '.$qty.'</h5>';
	}	
	echo "<table class='table customTable'><tr><th class='text-left'>Batch No.</th><th class='text-center'>Available Quantity</th><th class='text-center'>Required Quantity</th></tr>";
	foreach($batches as $result)
	{
		$value ='';
		$oqty = $this->db->query("select sum(quantity) as ouqty from clinic_pharmacy_inventory_outward where drug_id=".$did." and batch_no='".$result['batch_no']."' and clinic_id=".$clinic_id)->row();
		$aqty = ($result['oqty']-$oqty->ouqty);
		$unitp = $result['mrp']/$result['pack_size'];
		if($aqty <= 0)
			continue;
		$checkstat = '';
		$readstat ='readonly';
		if($batch_count==1)
		{
			$readstat = "";
			$checkstat = "checked";
			$value =  "value='".$qty."'";
		}
		else
		{
			if($insert != 1)
			{
				if($aqty>$qty)
				{
					$readstat = "";
					$checkstat = "checked";
					$value = "value='".$qty."'";
					$insert = 1;
				}	
				else
				{
					$value = '';
				}
			}			
		}
		

		echo "<tr><td><input type='checkbox' ".$checkstat." class='batch_cb' value='".trim($result['batch_no'])."' name='batchno[]' onclick='enable_text_box(".'"'.stripslashes($result['batch_no']).'"'.")' id='batchno_".trim($result['batch_no'])."' /> ".trim(stripslashes($result['batch_no']))."</td><td>".$aqty."</td><td><input type='number' ".$value." class='digits' id='bqty_".trim($result['batch_no'])."' ".$readstat." onkeyup='checkvalue(".$aqty.",\"".trim(stripslashes($result['batch_no']))."\");' /><input type='hidden' name='unitp' id='unitp_".stripslashes($result['batch_no'])."' value='".$unitp."' /></td></tr>";
	}
	echo "<table>";
}


public function save_order()
{
	$clinic_id = $this->session->userdata('clinic_id');
	$user_id = $this->session->userdata('user_id');
	// echo "<pre>";print_r($_POST);echo "</pre>";
	// exit;
	if(isset($_POST['pid']))
		$patient_id = $_POST['pid'];
	else
		$patient_id = '';

	// $patientInfo = $this->db->select("umr_no,first_name,last_name,title")->from("")->where()->get()->result();

	//Generate Receipt No.
	$last_id = $this->db->query("select * from billing where receipt_no like 'RECEIPT-".$clinic_id."-%' and status!=0")->result_array();
	$lcount = count($last_id);
	$data['receipt_no'] = 'RECEIPT-'.$clinic_id."-".($lcount+1);
	$data['invoice_no'] = 'INV-'.$clinic_id."-".($lcount+1);
	$data['clinic_id'] = $clinic_id;

	if($patient_id != ''){
		$data['patient_id'] = $patient_id;
		$data['umr_no'] = $_POST['umr_no'];
		$data['appointment_id'] = $_POST['appointment_id'];
		$data['doctor_id'] = $_POST['doctor_id'];
	}else{
		$data['guest_name'] = $_POST['pname'];
		$data['guest_mobile'] = $_POST['pmobile'];
	}

	$data['billing_type'] = 'Pharmacy';
	$data['payment_mode'] = $_POST['payment_mode'];
	$data['transaction_id'] = $_POST['transaction_id'];
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
	// echo "<pre>";print_r($data);echo "</pre>";
	$last_inserted_id = $this->Generic_model->insertDataReturnId("billing",$data);

	// echo $this->db->last_query();
	// exit();

	$billing_id = $last_inserted_id;

	$drug_ids = count($_POST['totrw']);

	for($i=0; $i<$drug_ids; $i++){

		if($_POST['qty'][$i] != '' || $_POST['qty'][$i] != NULL){
			$batch_array = explode(",",$_POST['qty'][$i]);
			$drug_info = $this->db->query("SELECT * FROM drug where drug_id=".$_POST['totrw'][$i])->row();
			$gsts_info = $this->db->query("select * from clinic_pharmacy_inventory where clinic_id=".$clinic_id." and drug_id=".$drug_info->drug_id)->row();
			
			//echo $this->db->last_query();
	
			$invoice_amount = 0;
	
			for($j=0;$j<count($batch_array);$j++)
			{
	
				$batchList = explode(" :: ",$batch_array[$j]);
				//$batchno = explode("-",$batchli[0]);
				$batchno = trim($batchList[0]);
				$reqQty = trim($batchList[1]);
				// echo "<pre>";print_r($batchList);echo "</pre>";//exit;
				// $unitp = $this->db->query("select * from clinic_pharmacy_inventory_inward where drug_id=".$drug_info->drug_id." and clinic_id=".$clinic_id." and batch_no='".$batchno."'")->row();
				$drugRec = $this->db->query("select * from clinic_pharmacy_inventory_inward where drug_id=".$drug_info->drug_id." and clinic_id=".$clinic_id." and batch_no='".trim($batchno)."' and archieve=0")->row();
	
				// echo '<pre>';
				// echo "select * from clinic_pharmacy_inventory_inward where drug_id=".trim($drug_info->drug_id)." and clinic_id=".$clinic_id." and batch_no='".trim($batchno)."'<br>";
				// echo 'Drug Record: <br>';
				// print_r($drugRec);
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
				$lineinfo['total_amount'] = $price; 
				$lineinfo['status'] = 1;
				$lineinfo['created_by'] = $user_id;
				$lineinfo['modified_by'] = $user_id;
				$lineinfo['created_date_time'] = date("Y-m-d H:i:s");
				$lineinfo['modified_date_time'] = date("Y-m-d H:i:s");
	
				// echo '<pre>';
				// echo 'Billing Line Items: <br>';
				// print_r($lineinfo);
				// echo '</pre>';
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
	
				// echo '<pre>';
				// echo 'Inventory Outward: <br>';
				// print_r($outward);
				// echo '<br><br>';			
	
				// insert data into Clinic Pharmacy Inventory Outward
				$this->Generic_model->insertDataReturnId("clinic_pharmacy_inventory_outward",$outward);
				$outqty = $this->db->query("select sum(quantity) as oqty from clinic_pharmacy_inventory_outward where drug_id=".$drug_info->drug_id." and batch_no='".$batchno."' and clinic_id=".$clinic_id)->row();
				$iqty = $this->db->query("select sum(quantity) as iqty from clinic_pharmacy_inventory_inward where drug_id=".$drug_info->drug_id." and batch_no='".$batchno."' and archieve=0 and clinic_id=".$clinic_id)->row();
				if($outqty->oqty>=$iqty->iqty)
				{
					$this->db->query("update clinic_pharmacy_inventory_inward set status=0 where drug_id=".$drug_info->drug_id." and batch_no='".$batchno."' and archieve=0 and clinic_id=".$clinic_id);
					// echo $this->db->last_query()."<br>";
				}
			}
		}

		// update invoice amount in the billing table for total_amount field for billing_id
		$this->db->query("UPDATE billing SET total_amount = '".$invoice_amount."' WHERE billing_id='".$billing_id."'");

        // $this->Generic_model->pushNotifications('','','',$clinic_id,'PharmacyCurrentStock');
	}
	// exit;
	redirect('New_order/view_order/'.$last_inserted_id);

}

public function print_bill($bid)
{
	echo $bid;
	// $this->db->query("update billing set payment_mode='".$this->input->post('payment_mode')."',payment_status = 1 where billing_id=".$bid);
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
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		// exit();
	$data['view'] = 'new_order/view_order';
	$this->load->view('layout', $data);
}
public function drop_pharmacy_invoice()
{
	$bid = $_POST['bid'];
	echo $bid;
	$clinic_id = $this->session->userdata('clinic_id');
	$user_id = $this->session->userdata('user_id');
	$this->db->query("update billing set status='2' where billing_id=".$bid);
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
    $this->Generic_model->pushNotifications('','','',$clinic_id,'PharmacyCurrentStock');
	
	echo "result";
}
public function generatepdf($bid)
{

	// echo $bid;

	$data['clinic_id'] = $clinic_id = $this->session->userdata('clinic_id');
	$data['billing_master'] = $this->db->query("select * from billing where billing_id=".$bid)->row();
	$data['billing_line_items'] = $this->db->query("select * from billing_line_items where billing_id=".$bid)->result();
	$data['pdf_settings'] = $pdf_settings = $this->db->query("select * from clinic_pdf_settings where clinic_id='".$clinic_id."'")->row();
	$dinfo = $this->db->query("select * from doctors where doctor_id='".$data['billing_master']->doctor_id."'")->row();

	if(count($dinfo)>0)
	{
		$data['doctorInfo'] = $dinfo;
	}

	if($data['billing_master']->patient_id!=''){
		$pinfo = $this->db->query("select * from patients where patient_id=".$data['billing_master']->patient_id)->row();
		// echo $pinfo;
		$data['patient_name'] = $pinfo->title.". ".$pinfo->first_name." ".$pinfo->middle_name." ".$pinfo->last_name;
		$data['gender'] = $pinfo->gender;
		$data['age'] = $pinfo->age;
		$data['age_unit'] = $pinfo->age_unit;
		$data['patient_id'] = $pinfo->umr_no;
		$data['paddress'] = $pinfo->address_line;
		$clinic_info = $this->db->query("select * from clinics where clinic_id=".$pinfo->clinic_id)->row();
		$data['address'] = $clinic_info->address;
		$data['clinic_phone'] = $clinic_info->clinic_phone;
		// $data['clinic_id'] = $this->session->userdata('clinic_id');
		$clinic_infooo = $this->db->query("select * from clinic_pharmacy where clinic_id=".$clinic_id)->row();
		$data['clinic_logo'] = $clinic_infooo->logo;
	}
	else
	{
		// $clinic_info = $this->db->query("select * from clinics where clinic_id=".$pinfo->clinic_id)->row();
		$clinic_info = $this->db->query("select * from clinics where clinic_id=".$data['billing_master']->clinic_id)->row();
		$data['address'] = $clinic_info->address;
		$data['clinic_phone'] = $clinic_info->clinic_phone;
		// $data['clinic_logo'] = $clinic_info->clinic_logo;
		$data['patient_name'] = $data['billing_master']->guest_name;
		$data['gender'] = '';
		$data['patient_id'] = '';
		$data['guest_name'] = $data['billing_master']->guest_name;
		$clinic_infooo = $this->db->query("select * from clinic_pharmacy where clinic_id=".$clinic_id)->row();
		$data['clinic_logo'] = $clinic_infooo->logo;
		// $data['clinic_id'] = $this->session->userdata('clinic_id');
		
	}
	// echo "<pre>";print_r($data);exit;
	$this->load->library('M_pdf');
	$html = $this->load->view('new_order/order_invoice',$data,true);
	$pdfFilePath = time().$data['billing_master']->invoice_no.".pdf";
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