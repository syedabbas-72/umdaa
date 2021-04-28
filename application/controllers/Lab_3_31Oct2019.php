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

	public function dashboard()
	{
		$clinic_id = $this->session->userdata('clinic_id');

		$data['clinic_id'] = $clinic_id;

		$cond = '';

		if($clinic_id != 0)
			$cond = "where clinic_id=".$clinic_id;

		$currentDate = date('Y-m-d');

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

		// Get the list of patient investigations
		$patientInvestigations = $this->db->select('PI.patient_id, PI.patient_investigation_id, PI.doctor_id, PI.clinic_id, PI.appointment_id')->from('patient_investigation as PI')->where('PI.clinic_id =',$clinic_id)->like('PI.created_date_time', $currentDate)->group_by('PI.patient_id','ASC')->get()->result_array();

		// echo $this->db->last_query()."\n\n";

		$expectedRevenue = 0;
		$convertedRevenue = 0;
		$outPeopleRevenue = 0;

		if(count($patientInvestigations) > 0){

			$data['prescriptions_count'] = count($patientInvestigations);


			// Get the investigations prescribed in each prescription
			foreach($patientInvestigations as $prescription){

				$investigationsPrescribed = $this->db->query('select PILI.investigation_id, CI.price from patient_investigation_line_items PILI INNER JOIN clinic_investigations CI ON PILI.investigation_id = CI.investigation_id WHERE CI.clinic_id = '.$clinic_id.' AND PILI.patient_investigation_id = '.$prescription['patient_investigation_id'].' AND CI.status = 1')->result();

				if(count($investigationsPrescribed) > 0){
					foreach($investigationsPrescribed as $investigationAmount){
						$expectedRevenue = (float)$expectedRevenue + (float)$investigationAmount->price;	
					}				
				}

				// Check whether this prescription converted as a bill or no
				// If converted get the amount of the billing
				$convertedPrescription = $this->db->select('billing_id')->from('billing')->where('patient_investigation_id =',$prescription['patient_investigation_id'])->get()->result_array();

				if(count($convertedPrescription) > 0){

					// echo "1...";

					$data['billing_count'] = count($convertedPrecription);

					foreach($convertedPrescription as $billing){

						// Get the line items of the billing and sum of the amounts
						$billingInfo = $this->db->select('sum(amount) as amount')->from('billing_line_items')->where('billing_id =',$billing['billing_id'])->get()->row();

						// echo "\n\nFigure from the Summation: ".$this->db->last_query()."\n\n";
						$convertedRevenue = (float)$convertedRevenue + (float)$billingInfo->amount;
						// echo "From query output is :".(float)$billingInfo->amount."\n\n";
						// echo "Converted Revenue: ".$convertedRevenue."\n\n";

					}
				}

			}

			$data['expected_revenue'] = number_format(round($expectedRevenue),2);
			$data['converted_revenue'] = number_format(round($convertedRevenue),2);

		}

		// Get the list of customers purchaseddrugs from the pharmacy who are outsiders w.r.to the date
		$outPeople = $this->db->select('billing_id, guest_name, guest_mobile')->from('billing')->where('patient_investigation_id =',0)->where('clinic_id =',$clinic_id)->where('billing_type =','Lab')->like('billing_date_time', $tdate)->get()->result_array();

		if(count($outPeople) > 0){

			$data['out_people_count'] = count($outPeople);

			foreach($outPeople as $person){

				// Get the billing line items info with amount summation
				$personBillingInfo = $this->db->select('sum(amount) as amount')->from('billing_line_items')->where('billing_id ='.$person['billing_id'])->get()->row();
				$outPeopleRevenue = (float)$outPeopleRevenue + (float)$personBillingInfo->amount;
			}

		}

		$data['out_people_revenue'] = number_format(round($outPeopleRevenue),2);
		$data['lost_revenue'] = number_format(round($expectedRevenue) - round($convertedRevenue),2);

		$data['view'] = 'lab/dashboard';
		$this->load->view('layout', $data);
	}


	public function getFinances(){

		$clinic_id = $this->session->userdata('clinic_id');
		$start = $_POST['startDate'];
		$end = date('Y-m-d', strtotime($_POST['endDate'] . ' +1 day'));
		$d_id = $_POST['d_id'];

		if($this->session->userdata('role_id') == 4){
			$data['doctors_list'] = $this->Generic_model->getAllRecords('clinic_doctor', array('clinic_id' => $clinic_id,'doctor_id'=>$this->session->userdata('user_id')), $order = '');    
		}else{
			$this->db->select('distinct(doctor_id)');
			$this->db->from('clinic_doctor');
			if($clinic_id != 0)
				$this->db->where("clinic_id = ",$clinic_id);
			$data['doctors_list'] = $this->db->get()->result();
		}

		// Condition showing with respect to the doctor
		if($d_id == "all"){
			$docCondition = "";
		}else{
			$docCondition = " and PI.doctor_id = ".$d_id;
		}

		if($start == $end){
			$patientInvestigations = $this->db->query("Select PI.patient_id, PI.patient_investigation_id, PI.doctor_id, PI.clinic_id, PI.appointment_id from patient_investigation as PI where PI.clinic_id ='".$clinic_id."'".$docCondition." and PI.created_date_time LIKE '".$start."%'")->result();
		}else{
			$patientInvestigations = $this->db->query("Select PI.patient_id, PI.patient_investigation_id, PI.doctor_id, PI.clinic_id, PI.appointment_id from patient_investigation as PI where PI.clinic_id ='".$clinic_id."'".$docCondition." and PI.created_date_time BETWEEN '".$start."%' and '".$end."%'")->result();
		}

		$expectedRevenue = 0;
		$convertedRevenue = 0;
		$outPeopleRevenue = 0;
		$billingCount = 0;

		if(count($patientInvestigations) > 0){

			// Get the drugs prescribed in each prescription
			foreach($patientInvestigations as $prescription){

				// $investigationsPrescribed = $this->db->select('PILI.investigation_id, CI.price')->from('patient_investigation_line_items PILI')->join('clinic_investigations CI','PILI.investigation_id = CI.investigation_id','inner')->where('CI.clinic_id = '.$clinic_id)->where('PILI.patient_investigation_id = '.$prescription->patient_investigation_id)->get()->result_array();

				// if(count($investigationsPrescribed) > 0){
				// 	foreach($investigationsPrescribed as $investigationAmount){
				// 		$expectedRevenue = (float)$expectedRevenue + (float)$investigationAmount['price'];
				// 	}				
				// }

				$investigationsPrescribed = $this->db->query('select PILI.investigation_id, CI.price from patient_investigation_line_items PILI INNER JOIN clinic_investigations CI ON PILI.investigation_id = CI.investigation_id WHERE CI.clinic_id = '.$clinic_id.' AND PILI.patient_investigation_id = '.$prescription['patient_investigation_id'].' AND CI.status = 1')->result();

				if(count($investigationsPrescribed) > 0){
					foreach($investigationsPrescribed as $investigationAmount){
						$expectedRevenue = (float)$expectedRevenue + (float)$investigationAmount->price;	
					}				
				}

				// Check whether this prescription converted as a bill or no
				// If converted get the amount of the billing
				$convertedPrecription = $this->db->select('billing_id')->from('billing')->where('patient_investigation_id =',$prescription->patient_prescription_id)->get()->result_array();

				if(count($convertedPrecription) > 0){

					$billingCount = $billingCount++;

					foreach($convertedPrecription as $billing){

						// Get the line items of the billing and sum of the amounts
						$billingInfo = $this->db->select('sum(amount) as amount')->from('billing_line_items')->where('billing_id =',$billing['billing_id'])->get()->row();
						$convertedRevenue = (float)$convertedRevenue + (float)$billingInfo->amount;
					}
				}

			}

		}

		$data['prescriptions_count'] = count($patientInvestigations);
		$data['billing_count'] = $billingCount;
		$data['expected_revenue'] = number_format(round($expectedRevenue),2);
		$data['converted_revenue'] = number_format(round($convertedRevenue),2);
		$data['lost_revenue'] = number_format(round($expectedRevenue) - round($convertedRevenue),2);

		// Get the list of customers purchased drugs from the pharmacy who are outsiders w.r.to the date
		if($start == $end){
			$outPeople = $this->db->query("select billing_id, guest_name, guest_mobile from billing where patient_investigation_id = 0 and clinic_id = '".$clinic_id."' and billing_type = 'Lab' and billing_date_time like '".$start."%'")->result();
		}else{
			$outPeople = $this->db->query("select billing_id, guest_name, guest_mobile from billing where patient_investigation_id = 0 and clinic_id = '".$clinic_id."' and billing_type = 'Lab' and billing_date_time between '".$start."%' and '".$end."%'")->result();
		}

		if(count($outPeople) > 0){

			$data['out_people_count'] = count($outPeople);

			foreach($outPeople as $person){

				// Get the billing line items info with amount summation
				$personBillingInfo = $this->db->select('sum(amount) as amount')->from('billing_line_items')->where('billing_id ='.$person->billing_id)->get()->row();
				$outPeopleRevenue = (float)$outPeopleRevenue + (float)$personBillingInfo->amount;
			}

		}else{
			$data['out_people_count'] = 0;
		}

		$data['out_people_revenue'] = number_format(round($outPeopleRevenue),2);

		echo $data['expected_revenue']."*".$data['converted_revenue']."*".$data['out_people_revenue']."*".$data['lost_revenue']."*".$data['prescriptions_count']."*".$data['billing_count']."*".$data['out_people_count'];

	}	
	

	/* This funciton will return the lab prescriptions list written by the doctor to the patients */
	public function prescriptions()
	{

		$clinic_id = $this->session->userdata('clinic_id');

    	// Get Patient Prescriptions Date wise (DESC)
		$this->db->select('PI.patient_investigation_id, PI.patient_id, PI.doctor_id, PI.appointment_id, Doc.first_name as doc_first_name, Doc.last_name as doc_last_name, P.first_name as patient_first_name, P.last_name as patient_last_name, P.umr_no, PI.created_date_time as investigation_date, A.appointment_date, A.appointment_time_slot');
		$this->db->from('patient_investigation PI');
		$this->db->join('patients P','PI.patient_id = P.patient_id','inner');
		$this->db->join('doctors Doc','PI.doctor_id = Doc.doctor_id','inner');
		$this->db->join('appointments A','PI.appointment_id = A.appointment_id','inner');
		$this->db->where('PI.clinic_id =',$clinic_id);
		$this->db->order_by("PI.patient_investigation_id","DESC");

		$data['patient_prescription'] = $this->db->get()->result();
		$data['view'] = 'lab/prescriptions';

		$this->load->view('layout', $data);

	}


	public function view_prescription($pid){

		$this->db->select('PIL.patient_investigation_line_item_id, PIL.patient_investigation_id, PIL.investigation_name, INV.item_code, INV.investigation, CI.price, P.patient_id, P.first_name, P.last_name, P.umr_no, PI.patient_investigation_id, PI.appointment_id, A.appointment_date, A.appointment_time_slot, Doc.first_name as doc_first_name, Doc.last_name as doc_last_name, Dep.department_id, Dep.department_name');
		$this->db->from('patient_investigation_line_items PIL');
		$this->db->join('investigations INV', 'PIL.investigation_id = INV.investigation_id','left');
		$this->db->join('clinic_investigations CI', 'INV.investigation_id = CI.investigation_id','left');
		$this->db->join('patient_investigation PI', 'PIL.patient_investigation_id = PI.patient_investigation_id','left');
		$this->db->join('patients P', 'PI.patient_id = P.patient_id','left');
		$this->db->join('appointments A', 'PI.appointment_id = A.appointment_id','left');
		$this->db->join('doctors Doc', 'PI.doctor_id = Doc.doctor_id','left');
		$this->db->join('department Dep', 'Doc.department_id = Dep.department_id','left');
		$this->db->where('PIL.patient_investigation_id =',$pid);
		$data['prescription_investigations'] = $this->db->get()->result();

		$data['view'] = 'lab/view_prescription';
		$this->load->view('layout', $data);

	}


	public function raise_order($prescription_id = NULL){

		$clinic_id = $this->session->userdata('clinic_id');
		$patients = $this->db->query("select * from patients")->result_array();

		$data['pname'] = '';
		$data['prescription_id'] = $prescription_id;

		$lab_discount = $this->session->userdata('lab_discount');

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

				$i++;
			}
		}

		$master_version = $this->db->query("select * from master_version where clinic_id='".$clinic_id."' and master_name='clinic_inventory'")->row();
		$data['clinic_inventory_json_file_name'] = base_url().'uploads/clinic_inventory_json/'.$master_version->json_file_name;
		$data['view'] = 'new_order/new_orders';

		$this->load->view('layout', $data);

	}


	public function delete_lab_investigation($id=''){
		$this->db->query("delete from clinic_investigations where clinic_investigation_id='".$id."'");
		redirect("Lab/investigations");
	}


	public function investigations()
	{
		$clinic_id = $this->session->userdata('clinic_id');

		$data['labinvg_info'] = $this->db->select('*')->from('clinic_investigations CINV')->join('investigations INV','CINV.investigation_id = INV.investigation_id','inner')->where('CINV.clinic_id =',$clinic_id)->get()->result_array();

		$tdate = date('Y-m-d');

		// // Get investigations from lab_templates_line_items with investigation_id = 0
		// $paramsInfo = $this->db->select('lab_template_id, lab_template_line_item_id, parent_investigation_id, investigation_id, parameter')->from('lab_template_line_items')->where('investigation_id =',0)->get()->result_array();

		// // echo "Parameters Count: ".count($paramsInfo);

		// if(count($paramsInfo) > 0){
		// 	$i=0;
		// 	// Check investigations table for parameter existence
		// 	foreach($paramsInfo as $param){
		// 		// $data['parameters'][$i]['param'] = $param;
		// 		$chkInv = $this->db->select('investigation_id, investigation')->from('investigations')->where('investigation =',$param['parameter'])->get()->row();	
		// 		if(count($chkInv) == 0){
		// 			// $data['parameters'][$i]['investigation_id'] = 0;

		// 			// Insert new investigation
		// 			// Get last item_code and increment by 1
		// 			$itemCode = $this->db->query('select item_code from investigations order by investigation_id DESC LIMIT 1')->row();

		// 			// Generate new item code
		// 			if(count($itemCode)){
		// 				$item_code = substr($itemCode->item_code, 3);
		// 				$item_code = (int)$item_code + 1;
		// 				unset($investigation['investigation_id']);
		// 				$investigation['item_code'] = 'UMD'.$item_code;
		// 				$investigation['investigation'] = $param['parameter'];
		// 				$investigation['status'] = 1;
		// 				$investigation['created_by'] = $this->session->userdata('user_id');
		// 				$investigation['modified_by'] = $this->session->userdata('user_id');
		// 				$investigation['created_date_time'] = date('Y-m-d H:i:s');
		// 				$investigation['modified_date_time'] = date('Y-m-d H:i:s');

		// 				// insert new investigation into investigation master DB
		// 				$investigation['investigation_id'] = $newInvestigation['investigation_id'] = $this->Generic_model->insertDataReturnId('investigations',$investigation);

		// 				// Update investigation id in lab_templates line items
		// 				$this->Generic_model->updateData('lab_template_line_items', $newInvestigation, array('lab_template_line_item_id'=>$param['lab_template_line_item_id']));
		// 			}

		// 			$data['parameters'][$i]['investigation'] = $investigation;
		// 		}else{
		// 			$data['parameters'][$i]['investigation_id'] = $chkInv->investigation_id;
		// 		}

		// 		$i++;
		// 	}		
		// }

		// $data['parameters'] = $paramsInfo;

		$data['view'] = 'lab/investigations';
		$this->load->view('layout', $data);
	}


	public function view_order($billing_id)
	{

		// Get the clinic_id
		$clinic_id = $this->session->userdata('clinic_id');

		// Get Patient/Guest Information of the order/billing
		$data['billing_info'] = $this->db->select('B.billing_id, B.appointment_id, B.patient_id, B.doctor_id, B.guest_name, B.guest_mobile, B.position_status, A.appointment_date, A.appointment_time_slot, Doc.first_name as doc_first_name, Doc.last_name as doc_last_name, Doc.department_id, Dep.department_name')->from('billing B')->join('appointments A','B.appointment_id = A.appointment_id','left')->join('doctors Doc','B.doctor_id = Doc.doctor_id','left')->join('department Dep','Doc.department_id = Dep.department_id','left')->where('B.billing_id =', $billing_id)->get()->row();

		$data['billing_info']->guest_mobile = DataCrypt($data['billing_info']->guest_mobile, 'decrypt');

		// Get the line items of the billing
		$data['billing_line_items'] = $this->db->select('BLI.billing_line_item_id, BLI.billing_id, BLI.clinic_investigation_id, BLI.investigation_id, BLI.report_status, BLI.status, BLI.report_entry_status, BLI.report_authentication, INV.investigation, INV.item_code, INV.short_form, INV.category, LT.template_type')->from('billing_line_items BLI')->join('investigations INV','BLI.Investigation_id = INV.investigation_id')->join('lab_templates LT','BLI.investigation_id = LT.investigation_id')->where("BLI.billing_id =",$billing_id)->get()->result_array();

		$data['view'] = 'lab/view_order';
		$this->load->view('layout', $data);
	}


	public function view_report($billing_id, $investigation_id){

		// Get Patient/Guest Information of the order/billing
		$data['billing_info'] = $this->db->select('B.billing_id, B.appointment_id, B.patient_id, B.doctor_id, B.guest_name, B.guest_mobile, B.position_status, A.appointment_date, A.appointment_time_slot, Doc.first_name as doc_first_name, Doc.last_name as doc_last_name, Doc.department_id, Dep.department_name')->from('billing B')->join('appointments A','B.appointment_id = A.appointment_id','left')->join('doctors Doc','B.doctor_id = Doc.doctor_id','left')->join('department Dep','Doc.department_id = Dep.department_id','left')->where('B.billing_id =', $billing_id)->get()->row();

		// Get Template type
		$data['template_type'] = $this->Generic_model->getFieldValue('lab_templates','template_type',array('investigation_id'=>$investigation_id));

		// Get the lab report id 
		$data['patient_lab_report_id'] = $this->Generic_model->getFieldValue('patient_lab_reports','patient_lab_report_id',array('billing_id' => $billing_id, 'investigation_id' => $investigation_id));

		// Get the medical test name
		$data['test_name'] = $this->Generic_model->getFieldValue('investigations','investigation',array('investigation_id' => $investigation_id));

		// Get the investigation results w.r.to investigation id
		$data['lab_results'] = $this->db->select('PLR.consultant_remark, LI.patient_lab_reports_line_item_id, LI.investigation_id, LI.template_type, LI.value, LI.remarks, CI.clinic_investigation_id, CI.clinic_id, CI.low_range, CI.high_range, CI.units, CI.method, CI.other_information, I.investigation, I.item_code')->from('patient_lab_report_line_items LI')->join('clinic_investigations CI','LI.investigation_id = CI.investigation_id','inner')->join('investigations I','LI.investigation_id = I.investigation_id','inner')->join('patient_lab_reports PLR','LI.patient_lab_report_id = PLR.patient_lab_report_id')->where('LI.parent_investigation_id =', $investigation_id)->where('LI.patient_lab_report_id =',$data['patient_lab_report_id'])->get()->result_array();

		$data['investigation_id'] = $investigation_id;
		$data['billing_id'] = $billing_id;

		$data['view'] = 'lab/view_report';
		$this->load->view('layout', $data);

	}


	public function report_pdf($billing_id, $investigation_id){


		// Get Patient/Guest Information of the order/billing
		$data['billing_info'] = $this->db->select('B.billing_id,B.clinic_id,B.invoice_no, B.appointment_id, B.patient_id, B.doctor_id, B.guest_name, B.guest_mobile, B.position_status, A.appointment_date, A.appointment_time_slot, Doc.first_name as doc_first_name, Doc.last_name as doc_last_name, Doc.department_id, Dep.department_name')->from('billing B')->join('appointments A','B.appointment_id = A.appointment_id','left')->join('doctors Doc','B.doctor_id = Doc.doctor_id','left')->join('department Dep','Doc.department_id = Dep.department_id','left')->where('B.billing_id =', $billing_id)->get()->row();

		$data['appointments'] = $this->db->select("a.*, c.clinic_name,c.clinic_logo,c.address,c.clinic_phone,c.clinic_id,p.title, p.first_name as pname, p.last_name as plname,p.gender,p.age,p.address_line,p.umr_no,p.preferred_language,p.mobile,p.qrcode,p.allergy,d.salutation,d.first_name as dfname,d.last_name as dlname, d.qualification, d.registration_code, dep.department_name")->from("appointments a")->join("clinics c","a.clinic_id = c.clinic_id","left")->join("patients p","a.patient_id = p.patient_id","left")->join("doctors d","a.doctor_id = d.doctor_id","left")->join('department dep','d.department_id = dep.department_id')->where("a.clinic_id='" . $data['billing_info']->clinic_id . "'")->get()->row();

		// Get Template type
		$data['template_type'] = $this->Generic_model->getFieldValue('lab_templates','template_type',array('investigation_id'=>$investigation_id));

		// Get the lab report id 
		$data['patient_lab_report_id'] = $this->Generic_model->getFieldValue('patient_lab_reports','patient_lab_report_id',array('billing_id' => $billing_id, 'investigation_id' => $investigation_id));

		// Get the lab report id 
		$data['consultant_remark'] = $this->Generic_model->getFieldValue('patient_lab_reports','consultant_remark',array('billing_id' => $billing_id, 'investigation_id' => $investigation_id));

		// Get the medical test name
		$data['test_name'] = $this->Generic_model->getFieldValue('investigations','investigation',array('investigation_id' => $investigation_id));

		// Get the investigation results w.r.to investigation id
		$data['lab_results'] = $this->db->select('PLR.consultant_remark, LI.patient_lab_reports_line_item_id, LI.investigation_id, LI.template_type, LI.value, LI.remarks, CI.clinic_investigation_id, CI.clinic_id, CI.low_range, CI.high_range, CI.units, CI.method, CI.other_information, I.investigation, I.item_code')->from('patient_lab_report_line_items LI')->join('clinic_investigations CI','LI.investigation_id = CI.investigation_id','inner')->join('investigations I','LI.investigation_id = I.investigation_id','inner')->join('patient_lab_reports PLR','LI.patient_lab_report_id = PLR.patient_lab_report_id')->where('LI.parent_investigation_id =', $investigation_id)->where('LI.patient_lab_report_id =',$data['patient_lab_report_id'])->get()->result_array();

        // PDF Settings
        $data['pdf_settings'] = $pdf_settings = $this->db->select("*")->from("clinic_pdf_settings")->where("clinic_id='".$data['appointments']->clinic_id."'")->get()->row();

		$this->load->library('M_pdf');
		$html = $this->load->view('lab/report_pdf',$data,true);
		$pdfFilePath = $data['billing_info']->invoice_no.".pdf";
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
	    $this->m_pdf->pdf->Output("./uploads/lab_reports/".$pdfFilePath, "F");
	    redirect("uploads/lab_reports/".$pdfFilePath);
	}

	public function pay_osa($billing_id){

		$data['billing_id'] = $billing_id;

		// Get invoices list for the billing id
		$data['invoices'] = $this->db->select('BI.invoice_no, BI.billing_id, BI.invoice_no_alias, BI.invoice_date, BI.payment_type, BI.payment_mode, BI.transaction_id, B.patient_id, B.guest_name, B.guest_mobile')->from('billing_invoice BI')->join('billing B','BI.billing_id = B.billing_id','inner')->where('BI.billing_id =',$billing_id)->get()->result_array();

		// Get OSA amount from the billing table
		$data['osa'] = $this->Generic_model->getFieldValue('billing','osa',array('billing_id'=>$billing_id));

		if($_POST['save_pay_osa']){
			unset($_POST['save_pay_osa']);

			$post_params = $_POST;

			$clinic_id = $this->session->userdata('clinic_id');
			$user_id = $this->session->userdata('user_id');

			// Generate the invoice no.
			$billing_invoice['invoice_no_alias'] = $invoice_no_alias = generate_invoice_no($clinic_id);
			$billing_invoice['invoice_no'] = $clinic_id.$invoice_no_alias;
			$billing_invoice['payment_type'] = 'OSA';
			$billing_invoice['billing_id'] = $billing_id;
			$billing_invoice['created_by'] = $billing_invoice['modified_by'] = $user_id;
			$billing_invoice['invoice_date'] = $billing_invoice['created_date_time'] = $billing_invoice['modified_date_time'] = date('Y-m-d H:i:s');

			$billing_invoice_data = array_merge($billing_invoice, $post_params);

			$result = $this->Generic_model->insertData('billing_invoice', $billing_invoice_data);

			if($result){
				// Update the billing table with records concern to billing_id making osa to 0.00 as it was paid
				$update_billing['osa'] = '0.00';
				$update_billing['payment_status'] = 1;
				$update_billing['modified_by'] = $user_id;
				$update_billing['modified_date_time'] = date('Y-m-d H:i:s');

				$updateResult = $this->Generic_model->updateData('billing',$update_billing,array('billing_id'=>$billing_id));
			}

			redirect('Lab/billing');			

		}

		$data['view'] = 'lab/pay_osa';
		$this->load->view('layout', $data);
	}


	public function reportStatusUpdate(){
		$status['report_status'] = $_POST['report_status'];
		$billing_line_item_id = $_POST['billing_line_item_id'];
		$result = $this->Generic_model->updateData('billing_line_items',$status,array('billing_line_item_id' => $billing_line_item_id));
		if($result) {
			echo trim($_POST['report_status']);
		}else{
			echo 0;
		}
	}


	public function positionStatusUpdate(){
		extract($_POST);
		if($lineItemCount == $sampleCollectionCount){
			$status['position_status'] = 'LT';
		}else{
			$status['position_status'] = 'SC';
		}

		$result = $this->Generic_model->updateData('billing',$status,array('billing_id' => $billing_id));

		if($result) {
			echo $status['position_status'];
		}else{
			echo 0;
		}
	}


	// public function order_report_entry($billing_id)
	// {

	// 	// Get the clinic_id
	// 	$clinic_id = $this->session->userdata('clinic_id');

	// 	// Get Patient/Guest Information of the order/billing
	// 	$data['billing_info'] = $this->db->select('B.billing_id, B.appointment_id, B.patient_id, B.doctor_id, B.guest_name, B.guest_mobile, B.position_status, B.osa, A.appointment_date, A.appointment_time_slot, Doc.first_name as doc_first_name, Doc.last_name as doc_last_name, Doc.department_id, Dep.department_name')->from('billing B')->join('appointments A','B.appointment_id = A.appointment_id','left')->join('doctors Doc','B.doctor_id = Doc.doctor_id','left')->join('department Dep','Doc.department_id = Dep.department_id','left')->where('B.billing_id =', $billing_id)->get()->row();


	// 	// Get the line items of the billing
	// 	$data['billing_line_items'] = $this->db->select('BLI.billing_line_item_id, BLI.billing_id, BLI.item_information, BLI.clinic_investigation_id, BLI.investigation_id, BLI.sample, BLI.report_status, BLI.status, CI.price, CI.clinic_id, INV.item_code, INV.short_form, INV.category')->from('billing_line_items BLI')->join('clinic_investigations CI','BLI.clinic_investigation_id = CI.clinic_investigation_id')->join('investigations INV','BLI.Investigation_id = INV.investigation_id')->where("BLI.billing_id =",$billing_id)->where('CI.clinic_id =',$clinic_id)->get()->result_array();

	// 	$data['view'] = 'lab/order_report_entry';
	// 	$this->load->view('layout', $data);
	// }


	public function get_template_info()
	{
		$clinic_id = $this->session->userdata('clinic_id');

		$cond = '';
		if($clinic_id != 0)
			$cond = "and clinic_id=".$clinic_id;

		$investigation_id = $_POST['investigation_id'];
		$clinic_investigation_id = $_POST['clinic_investigation_id'];
		$billing_line_item_id = $_POST['billing_line_item_id'];
		$billing_id = $_POST['billing_id'];

		// $cinfo = $this->db->query("select * from clinic_investigations where clinic_investigation_id=".$clinic_investigation_id." ".$cond)->row();
		// $info = $this->db->query("select * from lab_templates where investigation_id=".$cinfo->investigation_id)->row();
		// $tlineifno = $this->db->query("select * from lab_template_line_items where lab_template_id=".$info->lab_template_id)->result_array();
		// $lab_template_id = $this->Generic_model->getFieldValue('lab_templates','lab_template_id',array('investigation_id'=>$investigation_id));

		$lab_template_info = $this->db->select('lab_template_id, template_name, template_type')->from('lab_templates')->where('investigation_id =',$investigation_id)->get()->row();

		$consultant_remark = $this->Generic_model->getFieldValue('patient_lab_reports','consultant_remark',array('billing_id'=>$billing_id, 'investigation_id'=>$investigation_id));
		// echo $this->db->last_query();

		// Get lab template line items concern to lab_template_id
		$labTemplateLineItems = $this->db->select('lab_template_line_item_id, parent_investigation_id, investigation_id, parameter, remarks')->from('lab_template_line_items')->where('lab_template_id =',$lab_template_info->lab_template_id)->get()->result_array();

		// print_r($_POST);
		// echo "Consultant Remark: ".$consultant_remark."\n";
		// print_r($lab_template_info);
		// print_r($labTemplateLineItems);
		
		// echo "cinfo: "; 
		// print_r($cinfo);

		// echo "Info: ";
		// print_r($info);

		// echo "tlineInfo: ";
		// print_r($tlineifno);

		// exit();

		if($lab_template_info->template_type == 'Excel'){

			$i = 0;

			$tinfo = '';
			foreach($labTemplateLineItems as $tresult)
			{	

				$clinic_invg = $this->db->query("select * from clinic_investigations where investigation_id=".$tresult['investigation_id']." ".$cond)->row();
				
				if(count($clinic_invg) > 0)
				{

					$report_status = $this->Generic_model->getFieldValue('billing_line_items','report_status',array('billing_line_item_id'=>$billing_line_item_id));

					$templateLineItems = $this->db->select('RLI.value, RLI.patient_lab_reports_line_item_id, R.patient_lab_report_id')->from('patient_lab_reports R')->join('patient_lab_report_line_items RLI','R.patient_lab_report_id = RLI.patient_lab_report_id','inner')->where('R.billing_id =',$billing_id)->where('RLI.lab_template_line_item_id =',$tresult['lab_template_line_item_id'])->get()->row();

					if(count($templateLineItems) > 0){
						$itemValue = $templateLineItems->value;
						$patient_lab_report_id = $templateLineItems->patient_lab_report_id;
						$patient_lab_reports_line_item_id = $templateLineItems->patient_lab_reports_line_item_id;
					}
					else{
						$itemValue = '';
						$patient_lab_report_id = '';
						$patient_lab_reports_line_item_id = '';
					}


					$tinfo .= '<tr><td>'.$tresult['parameter'].'</td><td><input class="form-control" type="text" name="line_item['.$i.'][value]" value="'.$itemValue.'" onkeypress="return decimal();"><input type="hidden" value="'.$investigation_id.'" name="patient_lab_reports[investigation_id]" /><input type="hidden" name="line_item['.$i.'][template_type]" value="'.$info->template_type.'"><input type="hidden" name="patient_lab_reports[clinic_investigation_id]" value="'.$clinic_investigation_id.'"><input type="hidden" value="'.$patient_lab_report_id.'" name="patient_lab_reports[patient_lab_report_id]" /><input type="hidden" value="'.$patient_lab_report_id.'" name="line_item['.$i.'][patient_lab_report_id]" /><input type="hidden" value="'.$patient_lab_reports_line_item_id.'" name="line_item['.$i.'][patient_lab_reports_line_item_id]" /><input class="form-control" type="hidden" value="'.$tresult['parent_investigation_id'].'" name="line_item['.$i.'][parent_investigation_id]" /><input type="hidden" value="'.$tresult['investigation_id'].'" name="line_item['.$i.'][investigation_id]" /><input type="hidden" value="'.$info->lab_template_id.'" name="patient_lab_reports[lab_template_id]" /><input type="hidden" value="'.$billing_line_item_id.'" name="billing_line_item_id" /><input type="hidden" value="'.$tresult['lab_template_line_item_id'].'" name="line_item['.$i.'][lab_template_line_item_id]" /></td></tr>';
				}
				else
				{

					$tinfo .= '<tr><td>'.$tresult['parameter'].'</td><td><input class="form-control" type="text" name="line_item['.$i.'][value]" value="" onkeypress="return decimal();"><input type="hidden" value="'.$investigation_id.'" name="patient_lab_reports[investigation_id]" /><input type="hidden" name="line_item['.$i.'][template_type]" value="'.$info->template_type.'"><input type="hidden" name="patient_lab_reports[clinic_investigation_id]" value="'.$clinic_investigation_id.'"><input type="hidden" value="'.$patient_lab_report_id.'" name="patient_lab_reports[patient_lab_report_id]" /><input type="hidden" value="'.$patient_lab_report_id.'" name="line_item['.$i.'][patient_lab_report_id]" /><input type="hidden" value="'.$patient_lab_reports_line_item_id.'" name="line_item['.$i.'][patient_lab_reports_line_item_id]" /><input class="form-control" type="hidden" value="'.$tresult['parent_investigation_id'].'" name="line_item['.$i.'][parent_investigation_id]" /><input type="hidden" value="'.$tresult['investigationId'].'" name="line_item['.$i.'][investigation_id]" /><input type="hidden" value="'.$info->lab_template_id.'" name="patient_lab_reports[lab_template_id]" /><input type="hidden" value="'.$billing_line_item_id.'" name="billing_line_item_id" /><input type="hidden" value="'.$tresult['lab_template_line_item_id'].'" name="line_item['.$i.'][lab_template_line_item_id]" /></td></tr>';
				}

				$i++;

			}

			$tinfo .= '<input type="hidden" name="billing_line_item_id" value="'.$billing_line_item_id.'"><input type="hidden" name="billing_id" value="'.$billing_id.'">';

			if($report_status == 'Auth'){
				$tinfo .= '<tr><td colspan="2"><div class="form-group"><label for="title" class="col-form-label">Consulting remark</label><textarea name="patient_lab_reports[consultant_remark]" id="excel_consultant_remark_ta" cols="57" rows="3">'.$consultant_remark.'</textarea></td></tr>';
			}

			echo "excel".":".$tinfo;
		}else{
			$tinfo = '';
			$i = 0;
			foreach($labTemplateLineItems as $tresult)
			{

				$templateLineItems = $this->db->select('RLI.remarks, RLI.patient_lab_reports_line_item_id, RLI.investigation_id, R.patient_lab_report_id')->from('patient_lab_reports R')->join('patient_lab_report_line_items RLI','R.patient_lab_report_id = RLI.patient_lab_report_id','inner')->where('R.billing_id =',$billing_id)->where('RLI.lab_template_line_item_id =',$tresult['lab_template_line_item_id'])->get()->row();

				if(count($templateLineItems) > 0){
					$remark = $templateLineItems->remarks;
					$patient_lab_report_id = $templateLineItems->patient_lab_report_id;
					$patient_lab_reports_line_item_id = $templateLineItems->patient_lab_reports_line_item_id;
				}
				else{
					$remark = $tresult['remarks'];
					$patient_lab_report_id = '';
					$patient_lab_reports_line_item_id = '';
				}

				$tinfo .= '<tr><td>'.$tresult['parameter'].'</td><td><textarea class="form-control" rows="5" cols="20" name="line_item['.$i.'][remarks]">'.$remark.'</textarea><input type="hidden" value="'.$investigation_id.'" name="patient_lab_reports[investigation_id]" /><input type="hidden" name="line_item['.$i.'][template_type]" value="'.$info->template_type.'"><input type="hidden" name="patient_lab_reports[clinic_investigation_id]" value="'.$clinic_investigation_id.'"><input class="form-control" type="hidden" value="'.$tresult['parent_investigation_id'].'" name="line_item['.$i.'][parent_investigation_id]" /><input type="hidden" name="patient_lab_reports[patient_lab_report_id]" value="'.$patient_lab_report_id.'"><input type="hidden" value="'.$patient_lab_report_id.'" name="line_item['.$i.'][patient_lab_report_id]" /><input type="hidden" name="line_item['.$i.'][patient_lab_reports_line_item_id]" value="'.$patient_lab_reports_line_item_id.'"><input type="hidden" value="'.$tresult['investigation_id'].'" name="line_item['.$i.'][investigation_id]" /><input type="hidden" value="'.$info->lab_template_id.'" name="patient_lab_reports[lab_template_id]" /><input type="hidden" value="'.$tresult['lab_template_line_item_id'].'" name="line_item['.$i.'][lab_template_line_item_id]" /></td></tr>';

				$i++;
			}

			$tinfo .= '<input type="hidden" name="billing_line_item_id" value="'.$billing_line_item_id.'"><input type="hidden" name="billing_id" value="'.$billing_id.'">';

			if($report_status == 'Auth'){
				$tinfo .= '<tr><td colspan="2"><div class="form-group"><label for="title" class="col-form-label">Consulting remark</label><textarea name="patient_lab_remarks[consultant_remark]" id="general_consultant_remark_ta" cols="57" rows="3">'.$consultant_remark.'</textarea></td></tr>';
			}

			echo "general".":".$tinfo;
		}
	}


	public function templates_input_save()
	{

		echo '<pre>';
		print_r($_POST);
		echo '</pre>';
		exit();

		$clinic_id = $this->session->userdata('clinic_id');
		$user_id = $this->session->userdata('user_id');

		$patient_lab_reports = $_POST['patient_lab_reports'];
		$patient_lab_reports['clinic_id'] = $clinic_id;	

		if($patient_lab_reports['patient_id'] != ''){
			$patient_lab_reports['umr_no'] = $this->Generic_model->getFieldValue('patients','umr_no',array('patient_id'=>$patient_lab_reports['patient_id']));
		}

		if($patient_lab_reports['patient_lab_report_id'] == ''){

			$patient_lab_reports['created_by'] = $patient_lab_reports['modified_by'] = $user_id;
			$patient_lab_reports['created_date_time'] = $patient_lab_reports['modified_date_time'] = date('Y-m-d H:i:s');

			// Create Patient lab report master record
			$patient_lab_report_id = $this->Generic_model->insertDataReturnId('patient_lab_reports',$patient_lab_reports);
		}else{

			$patient_lab_reports['modified_by'] = $user_id;
			$patient_lab_reports['modified_date_time'] = date('Y-m-d H:i:s');

			// Update Patient lab report master record with changes
			$updateRecRes = $this->Generic_model->updateData('patient_lab_reports',$patient_lab_reports,array('patient_lab_report_id'=>$patient_lab_reports['patient_lab_report_id']));
			$patient_lab_report_id = $patient_lab_reports['patient_lab_report_id'];			
		}

		$line_item = $_POST['line_item'];
		$count = count($line_item);
		
		for($i=0; $i<$count; $i++){
			$line_item[$i]['patient_lab_report_id'] = $patient_lab_report_id;
			if($line_item[$i]['patient_lab_reports_line_item_id'] == ''){
				// Create the record
				$this->Generic_model->insertDataReturnId('patient_lab_report_line_items',$line_item[$i]);
			}else{
				// Update the existing record with the changes
				$this->Generic_model->updateData('patient_lab_report_line_items', $line_item[$i], array('patient_lab_reports_line_item_id'=>$line_item[$i]['patient_lab_reports_line_item_id']));
			}
		}

		// Update the report status & report entry status for billing_line_items for an investigation
		$report_status['report_entry_status'] = $_POST['report_entry_status'];

		if($_POST['report_entry_status'] == 1){
			// If 1, that means the entry of the results were done and the report is put fwd for authentication. Status is 'Auth' - 'AUTHENTICATION'
			$report_status['report_status'] = 'Auth';
			$report_status['report_entry_date_time'] = date('Y-m-d H:i:s');
			$report_status['report_entry_by'] = $user_id;
		}else{
			// If 0, then report entry is still pending and in RE - 'REPORT ENTRY' status
			$report_status['report_status'] = 'RE';
			$report_status['report_entry_date_time'] = date('Y-m-d H:i:s');
			$report_status['report_entry_by'] = $user_id;
			$report_status['report_entry_status'] = 0;
		}

		if($_POST['report_authentication'] == 1){
			// If 1, that means the entry of the results were done and the report is put fwd for authentication. Status is 'Auth' - 'AUTHENTICATION'
			$report_status['report_status'] = 'RDY';
			$report_status['authenticated_date_time'] = date('Y-m-d H:i:s');
			$report_status['authenticated_by'] = $user_id;
		}		

		$report_status['modified_by'] = $user_id;
		$report_status['modified_date_time'] = date('Y-m-d H:i:s');

		$this->Generic_model->updateData('billing_line_items', $report_status, array('billing_line_item_id' => $_POST['billing_line_item_id']));	

		// For time being the below code for updating postion_status is commenting
		// Check if all the billing line item -> reports status are 1. If yes, then billing->position_status should be made ro RD - 'REPORTS DONE'
		// Check by report_status count	
		// $reportStatusRes = $this->db->query("SELECT report_status, COUNT(*) FROM `billing_line_items` where billing_id = ".$_POST['billing_id']." group by report_status")->result_array();
		// $statusRecCount = count($reportStatusRes);

		// if($statusRecCount == 1){
		// 	// Then check if the resport_status = 1/0
		// 	// If report_status = 1, then change update the position_status to RD - RPORTS DONE else make no changes
		// 	if($reportStatusRes[0]['report_entry_status'] == 1){
		// 		// Update the position_status in billing db to 'WA' - WAITING FOR AUTHENTICATION
		// 		$position_status['position_status'] = "WA";
		// 		$this->Generic_model->updateData('billing',$position_status,array('billing_id'=>$_POST['billing_id']));
		// 	}elseif($reportStatusRes[0]['report_entry_status'] == 0){
		// 		// Update the position_status in billing DB to 'LT' - LABORATORY TESTING
		// 		$position_status['position_status'] = "LT";
		// 		$this->Generic_model->updateData('billing',$position_status,array('billing_id'=>$_POST['billing_id']));
		// 	}
		// }else if($statusRecCount > 1){
		// 	// If the result is > 1 then its obvious statuses would be 1 and 0, it meane report entry is in middle of something
		// 	// Update the position_status in billing DB to 'RE' - REPORT ENTRY
		// 	$position_status['position_status'] = "RE";
		// 	$this->Generic_model->updateData('billing',$position_status,array('billing_id'=>$_POST['billing_id']));
		// }

		redirect('Lab/view_order/'.$_POST['billing_id']);

		// if($new){
		// 	// Insert new records into patient lab report line items
		// 	$count = count($line_item);
		// 	for($i=0; $i<$count; $i++){

		// 	}
		// }

		// Check for the existing patient lab report record
		// $checkReport = $this->db->select('patient_lab_report_id')->from('patient_lab_reports')->where('clinic_id =',$clinic_id)->where('billing_id =',$_POST['billing_id'])->where('lab_template_id =',$_POST['lab_template_id'])->get()->row();

		// if(count($checkReport) > 0)
		// {
		// 	$delRes = $this->db->query("delete from patient_lab_report_line_items where patient_lab_report_id=".$checkReport->patient_lab_report_id);
		// 	$delRes = $this->db->query("delete from patient_lab_reports where patient_lab_report_id=".$checkReport->patient_lab_report_id." and lab_template_id=".$_POST['lab_template_id']);
		// }

		// $data['clinic_id'] = $clinic_id;
		// $data['billing_id'] = $_POST['billing_id'];
		// $data['guest_name'] = $_POST['guest_name'];
		// $data['guest_mobile'] = $_POST['guest_mobile'];
		// $data['lab_template_id'] = $_POST['lab_template_id'];
		// $data['status'] = 1;
		// $data['created_by'] = $user_id;
		// $data['modified_by'] = $user_id;
		// $data['created_date_time'] = date("Y-m-d H:i:s");
		// $data['modified_date_time'] = date("Y-m-d H:i:s");
		// $last_inserted_id = $this->Generic_model->insertDataReturnId("patient_lab_reports",$data);

		// $investigationsCount = count($_POST['lab_template_line_item_id']);

		// for($i=0; $i<$investigationsCount; $i++){

		// 	$lineinfo['patient_lab_report_id'] = $last_inserted_id;
		// 	$lineinfo['investigation_id'] = $_POST['investigation_id'][$i];
		// 	$lineinfo['lab_template_line_item_id'] = $_POST['lab_template_line_item_id'][$i];

		// 	if($_POST['template_type']=='excel')
		// 		$lineinfo['value'] = $_POST['value'][$i];
		// 	else
		// 		$lineinfo['remark'] = $_POST['remarks'][$i];

		// 	$lineinfo['status'] = 1;
		// 	$lineinfo['created_by'] = $user_id;
		// 	$lineinfo['modified_by'] = $user_id;
		// 	$lineinfo['created_date_time'] = date("Y-m-d H:i:s");
		// 	$lineinfo['modified_date_time'] = date("Y-m-d H:i:s");

		// 	$this->Generic_model->insertDataReturnId("patient_lab_report_line_items",$lineinfo);

			// if($_POST['template_type']=='excel'){
			// 	$invgidd = $_POST['invgid'][$i];
			// 	$cinvg_chk = $this->db->query("select * from clinic_investigations where investigation_id=".$invgidd." and clinic_id=".$clinic_id)->row();
			// 	if(count($cinvg_chk)<=0)
			// 	{
			// 		$cinvg['clinic_id'] = $clinic_id;
			// 		$cinvg['investigation_id'] = $_POST['invgid'][$i];
			// 		$cinvg['low_range'] = $_POST['lowrange'][$i];
			// 		$cinvg['high_range'] = $_POST['highrange'][$i];
			// 		$cinvg['units'] = $_POST['units'][$i];
			// 		$cinvg['method'] = $_POST['method'][$i];
			// 		$cinvg['other_information'] = $_POST['oinformation'][$i];
			// 		$cinvg['status'] = 1;
			// 		$cinvg['created_by'] = $user_id;
			// 		$cinvg['modified_by'] = $user_id;
			// 		$cinvg['created_date_time'] = date("Y-m-d H:i:s");
			// 		$cinvg['modified_date_time'] = date("Y-m-d H:i:s");
			// 		$this->Generic_model->insertDataReturnId("clinic_investigations",$cinvg);
			// 	}
			// }

		// }

	}


	public function print_bill($bid)
	{
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


	public function orders()
	{

		$clinic_id = $this->session->userdata('clinic_id');
		$tdate = date('Y-m-d');

		$billing = $this->db->select('billing_id, appointment_id, doctor_id, clinic_id, patient_id, guest_name, guest_mobile, billing_date_time, total_amount, billing_amount, discount, discount_unit, osa, payment_status, position_status')->from('billing')->where('clinic_id =',$clinic_id)->where('billing_type =','Lab')->get()->result_array();

		$i=0;

		foreach($billing as $billingRec)
		{
			$billing[$i] = $billingRec;
			$billing[$i]['guest_mobile'] = DataCrypt($billing[$i]['guest_mobile'], 'decrypt');
			$item_information = '';
			$html = '';

			// Get lab investigation details for this billing id
			$investigations = $this->db->select('BLI.item_information, BLI.investigation_id, BLI.report_status, LT.template_type')->from('billing_line_items BLI')->join('lab_templates LT','BLI.investigation_id = LT.investigation_id')->where('BLI.billing_id =',$billingRec['billing_id'])->get()->result_array();

			if(count($investigations) > 0){
				$html .= '<table cellspacing="0" cellpadding="0" class="reportStatusTable">';

				$invCount = count($investigations);
				$cntr = 1;

				foreach($investigations as $investigation){

					$status = array('SC','ST','LT','PR','RE','Auth','RDY');
					$statusFlag = 0;

					for($j=0; $j<count($status); $j++){
						if($investigation['report_status'] == '0'){
							$status[$j] = 'waiting';
						}else if($investigation['report_status'] == $status[$j]){
							$status[$j] = 'current';
							$statusFlag = 1;
						}else{
							if($statusFlag == 1){
								$status[$j] = 'waiting';
							}else{
								$status[$j] = 'done';
							}
						}
					}

					if($investigation['report_status'] == 'LT'){
	                    $report_status = '<small>in</small> Laboratory Testing';
	                }else if($investigation['report_status'] == 'SC'){
	                    $report_status = '<small>for</small> Sample Collection';
	                }else if($investigation['report_status'] == 'ST'){
	                    $report_status = '<small>for</small> Scan/Test';
	                }else if($investigation['report_status'] == 'PR'){
	                    $report_status = 'Processing Result';
	                }else if($investigation['report_status'] == 'RE'){
	                    $report_status = '<small>for</small> Report Entry';
	                }else if($investigation['report_status'] == 'Auth'){
	                    $report_status = '<small>for</small> Authentication';
	                }else if($investigation['report_status'] == 'RDY'){
	                    $report_status = 'Report Ready';
	                }

					$bdrPaddingCls = ($cntr == $invCount) ? ' noBtmPadding noBdr' : '';

					$html .= '<tr>';
					$html .= '<td style="width:45%" class="title'.$bdrPaddingCls.'">'.$cntr.'. '.ucwords(strtolower($investigation['item_information'])).'</td>';
					$html .= '<td style="width:30%" class="icons'.$bdrPaddingCls.'">';
					if($investigation['template_type'] == 'Excel'){
						$html .= '<i class="fas fa-vial '.$status[0].'"></i>';
						$html .= '<i class="fas fa-microscope '.$status[2].'"></i>';
					}else{
						$html .= '<i class="fas fa-radiation '.$status[1].'"></i>';
					}				
					$html .= '<i class="fas fa-hourglass-half '.$status[3].'"></i>';
					$html .= '<i class="fas fa-file-medical-alt '.$status[4].'"></i>';
					$html .= '<i class="fas fa-file-signature '.$status[5].'"></i>';
					$html .= '<i class="fas fa-print '.$status[6].'"></i>';
					$html .= '</td>';
					$html .= '<td style="width:25%" class="text-left">';
					$html .= '<span class="current">'.$report_status.'</span>';
					$html .= '</td></tr>';

					$cntr++;
				}
				$html .= '</table>'; 
			}

			$billing[$i]['item_information'] = $html;

			// echo $html;

			if($billingRec['appointment_id'] != '' || $billingRec['appointment_id'] == 0){

				// Get doctor and appointment details
				$appointmentInfo = $this->db->select('A.appointment_id, A.appointment_date, A.appointment_time_slot, Doc.first_name as doc_first_name, Doc.last_name as doc_last_name, P.first_name as patient_first_name, P.last_name as patient_last_name')->from('appointments A')->join('doctors Doc','A.doctor_id = Doc.doctor_id','inner')->join('patients P','A.patient_id = P.patient_id')->where('A.clinic_id =', $clinic_id)->where('appointment_id =',$billingRec['appointment_id'])->get()->row();

				if(count($appointmentInfo) > 0){
					$billing[$i]['doctor_name'] = "Dr.".ucwords($appointmentInfo->doc_first_name)." ".ucwords($appointmentInfo->doc_last_name);
					$billing[$i]['patient_name'] = ucwords($appointmentInfo->patient_first_name)." ".ucwords($appointmentInfo->patient_last_name);
					$billing[$i]['appointment_date'] = $appointmentInfo->appointment_date;
					$billing[$i]['appointment_time_slot'] = $appointmentInfo->appointment_time_slot;
				}
			}

			$i++;
		}

		$data['billing_info'] = $billing;

		$data['view'] = 'lab/order_list';
		$this->load->view('layout', $data);
	}


	public function billing()
	{

		$clinic_id = $this->session->userdata('clinic_id');
		$tdate = date('Y-m-d');

		$billing = $this->db->select('billing_id, appointment_id, doctor_id, clinic_id, patient_id, guest_name, guest_mobile, billing_date_time, total_amount, billing_amount, discount, discount_unit, osa, payment_status')->from('billing')->where('clinic_id =',$clinic_id)->where('billing_type =','Lab')->get()->result_array();

		$i=0;

		foreach($billing as $billingRec)
		{
			$billing[$i] = $billingRec;
			$billing[$i]['guest_mobile'] = DataCrypt($billing[$i]['guest_mobile'], 'decrypt');
			$item_information = '';

			// Get lab investigation details for this billing id
			$investigations = $this->db->select('item_information, investigation_id')->from('billing_line_items')->where('billing_id =',$billingRec['billing_id'])->get()->result_array();

			foreach($investigations as $investigation){
				if($item_information == '')
					$item_information .= strtolower($investigation['item_information']);
				else
					$item_information .= ", ".strtolower($investigation['item_information']);
			}

			$billing[$i]['item_information'] = ucwords($item_information);

			if($billingRec['appointment_id'] != '' || $billingRec['appointment_id'] == 0){

				// Get doctor and appointment details
				$appointmentInfo = $this->db->select('A.appointment_id, A.appointment_date, A.appointment_time_slot, Doc.first_name as doc_first_name, Doc.last_name as doc_last_name, P.first_name as patient_first_name, P.last_name as patient_last_name')->from('appointments A')->join('doctors Doc','A.doctor_id = Doc.doctor_id','inner')->join('patients P','A.patient_id = P.patient_id')->where('A.clinic_id =', $clinic_id)->where('appointment_id =',$billingRec['appointment_id'])->get()->row();

				if(count($appointmentInfo) > 0){
					$billing[$i]['doctor_name'] = "Dr.".ucwords($appointmentInfo->doc_first_name)." ".ucwords($appointmentInfo->doc_last_name);
					$billing[$i]['patient_name'] = ucwords($appointmentInfo->patient_first_name)." ".ucwords($appointmentInfo->patient_last_name);
					$billing[$i]['appointment_date'] = $appointmentInfo->appointment_date;
					$billing[$i]['appointment_time_slot'] = $appointmentInfo->appointment_time_slot;
				}
			}

			$i++;
		}

		$data['billing_info'] = $billing;

		$data['view'] = 'lab/billing';
		$this->load->view('layout', $data);
	}


	public function order_delete($bid='')
	{
		$this->db->query("delete billing from billing inner join billing_line_items on billing.billing_id = billing_line_items.billing_id where billing.billing_id='".$bid."'");
		redirect('Lab/orders');
	}


	public function add_order($prescription_id = NULL)
	{

		$clinic_id = $this->session->userdata('clinic_id');

		// Get Lab Discount
		$data['lab_discount'] = $this->Generic_model->getFieldValue('clinics','lab_discount',array('clinic_id'=>$clinic_id));
		$data['referral_doctor_lab_discount'] = $this->Generic_model->getFieldValue('clinics','referral_doctor_lab_discount',array('clinic_id'=>$clinic_id));

		$data['referral_doctors'] = $this->db->select('rfd_id, doctor_name, mobile, clinic_id, department, qualification, email')->from('referral_doctors')->where('clinic_id =',$clinic_id)->get()->result_array();

		$cond = '';

		if($clinic_id!=0){
			$cond = "where clinic_id=".$clinic_id;
		}

		$clinic_invg = $this->db->query("select DISTINCT(investigation) from investigations a inner join clinic_investigations b on a.investigation_id=b.investigation_id ".$cond)->result_array();

		$cinvg = '';

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
				$cinvg = $cinvg.'"'.$presult['package_name'].' (Package)"';
			else
				$cinvg = $cinvg.',"'.$presult['package_name'].' (Package)"';
		}

		$data['cinvg'] = $cinvg;

		$tdate = date('Y-m-d');

		// If any prescription is requested to convert into an order
		if($prescription_id != NULL){

			$this->db->select('PIL.patient_investigation_line_item_id, PIL.investigation_name, INV.item_code, INV.category, INV.short_form,  INV.investigation, INV.investigation_id, CI.clinic_investigation_id, CI.clinic_id, CI.price, P.patient_id, P.first_name, P.last_name, P.umr_no, P.mobile, P.alternate_mobile, PI.patient_investigation_id, PI.appointment_id, A.appointment_date, A.appointment_time_slot, Doc.doctor_id, Doc.first_name as doc_first_name, Doc.last_name as doc_last_name, Dep.department_id, Dep.department_name');
			$this->db->from('patient_investigation_line_items PIL');
			$this->db->join('investigations INV', 'PIL.investigation_id = INV.investigation_id','left');
			$this->db->join('clinic_investigations CI', 'INV.investigation_id = CI.investigation_id','left');
			$this->db->join('patient_investigation PI', 'PIL.patient_investigation_id = PI.patient_investigation_id','left');
			$this->db->join('patients P', 'PI.patient_id = P.patient_id','left');
			$this->db->join('appointments A', 'PI.appointment_id = A.appointment_id','left');
			$this->db->join('doctors Doc', 'PI.doctor_id = Doc.doctor_id','left');
			$this->db->join('department Dep', 'Doc.department_id = Dep.department_id','left');
			$this->db->where('PIL.patient_investigation_id =',$prescription_id);

			$data['investigation_cart'] = $this->db->get()->result();

		}

		$data['view'] = 'lab/orders';
		$this->load->view('layout', $data);
	}


	public function save_order()
	{

		// Segregate required params
		$clinic_id = $this->session->userdata('clinic_id');
		$user_id = $this->session->userdata('user_id');

		// Generate the invoice no.
		$invoice_no_alias = generate_invoice_no($clinic_id);
		$invoice_no = $clinic_id.$invoice_no_alias; 

		$billing = $_POST['billing'];

		// Encrypt the customer mobile no.
		$billing['guest_mobile'] = DataCrypt($billing['guest_mobile'], 'encrypt');

		$billing['invoice_no_alias'] = $invoice_no_alias;
		$billing['invoice_no'] = $invoice_no;
		$billing['created_by'] = $billing['modified_by'] = $user_id;
		$billing['billing_date_time'] = $billing['created_date_time'] = $billing['modified_date_time'] = date("Y-m-d H:i:s");

    	// If OSA exists then payment status will be pending
		if($billing['osa'] == '0'){
			$billing['payment_status'] = 1; 
		}else{
			$billing['payment_status'] = 0; 
		}

    	// Create Lab Order by creating a new billing master record
		$billing_id = $this->Generic_model->insertDataReturnId('billing',$billing);

		$billing_line_items = $_POST['billing_line_items'];
		$lineItemCount = count($billing_line_items);

		foreach($billing_line_items as $key => $lineItemInfo){
			$template_type = $this->Generic_model->getFieldValue('lab_templates','template_type',array('investigation_id'=>$lineItemInfo['investigation_id']));
			$lineItemInfo['report_status'] = ($template_type == 'Excel') ? "SC" : "ST";
			$lineItemInfo['billing_id'] = $billing_id;
			$lineItemInfo['created_by'] = $lineItemInfo['modified_by'] = $user_id;
			$lineItemInfo['created_date_time'] = $lineItemInfo['modified_date_time'] = date('Y-m-d H:i:s');

    		// Save billing line item records into billing_line_item db
			$this->Generic_model->insertDataReturnId('billing_line_items',$lineItemInfo);    	
		}

    	// get billing invoice data
		$billing_invoice = $_POST['billing_invoice'];
		$billing_invoice['billing_id'] = $billing_id;
		$billing_invoice['invoice_no'] = $billing['invoice_no'];
		$billing_invoice['invoice_no_alias'] = $billing['invoice_no_alias'];
		$billing_invoice['invoice_date'] = date('Y-m-d');
		$billing_invoice['created_by'] = $billing_invoice['modified_by'] = $user_id;
		$billing_invoice['created_date_time'] = $billing_invoice['modified_date_time'] = date('Y-m-d H:i:s');

		// Save invoice information into billing_invoice
		$this->Generic_model->insertData('billing_invoice', $billing_invoice);

		redirect('Lab/orders');

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


	/*
	Function addClinicInvestigation()
	To add an investigation from the master into clinic investigation DB
	w.r.to clinic id and investigation id
	Dev: Uday Kanth Rapalli
	*/
	public function add_clinic_investigation()
	{

		$clinic_id = $this->session->userdata('clinic_id');	
		$user_id = $this->session->userdata('user_id');	

		$method = $this->db->select("DISTINCT(method) as meth")->from("clinic_investigations")->get()->result_array();

		$methods = '';

		foreach($method as $result)
		{
			if($methods == '')
				$methods = '"'.$result['meth'].'"';
			else
				$methods = $methods.',"'.$result['meth'].'"';
		}

		$data['methods'] = $methods;

		// Save the investigation to Clinic Investigation
		$param = $this->input->post();

		echo '<pre>';
		print_r($param);
		echo '</pre>';

		if(count($param) > 0){	

			foreach($param['clinic_investigation'] as $clinicInvestigationRecord){
				$clinicInvestigationRecord['clinic_id'] = $clinic_id;
				$clinicInvestigationRecord['status'] = 1;
				$clinicInvestigationRecord['created_by'] = $clinicInvestigationRecord['modified_by'] = $user_id;
				$clinicInvestigationRecord['created_date_time'] = $clinicInvestigationRecord['modified_date_time'] = date('Y-m-d H:i:s');

				$this->Generic_model->insertDataReturnId("clinic_investigations",$clinicInvestigationRecord);		

			}

			// exit();		

			// $invg_ids = count($_POST['invgid']);

			// for($i=0;$i<$invg_ids;$i++){
			// 	$lineinfo['investigation_id'] = $_POST['invgid'][$i];
			// 	$lineinfo['clinic_id'] = $clinic_id;
			// 	$lineinfo['price'] = $_POST['mrp'][$i];
			// 	$lineinfo['low_range'] = $_POST['lowrange'][$i];	
			// 	$lineinfo['high_range'] = $_POST['highrange'][$i];	
			// 	$lineinfo['units'] = $_POST['units'][$i];	
			// 	$lineinfo['method'] = $_POST['method'][$i];	
			// 	$lineinfo['other_information'] = $_POST['oinfo'][$i];	

			// 	$lineinfo['status'] = 1;
			// 	$lineinfo['created_by'] = $user_id;
			// 	$lineinfo['modified_by'] = $user_id;
			// 	$lineinfo['created_date_time'] = date("Y-m-d H:i:s");
			// 	$lineinfo['modified_date_time'] = date("Y-m-d H:i:s");

			// 	$this->Generic_model->insertDataReturnId("clinic_investigations",$lineinfo);

			// 	if($_POST['shortform'][$i]!='')
			// 		$this->db->query("update investigations set short_form='".$_POST['shortform'][$i]."' where investigation_id=".$_POST['invgid'][$i]);
			// }	

			redirect('Lab/investigations');

		}

		$data['investigation_master_json_file'] = $this->Generic_model->getFieldValue('master_version','json_file_name',array('master_name'=>'investigation'));

		$data['view'] = 'lab/add_clinic_investigation';
		$this->load->view('layout', $data);
	}


	/*
	Function checkClinicInvestigation()
	To find the specific investigation record existence in the clinic investigation table
	w.r.to clinic id and investigation id
	author: Uday Kanth rapalli
	*/
	public function checkClinicInvestigation()
	{
		$clinic_id = $this->session->userdata('clinic_id');
		
		$investigationInfo = $this->db->select('investigation_id')->from('investigations')->where('investigation =',$_POST['investigation'])->get()->row();

		// echo $this->db->last_query();
		
		if(count($investigationInfo) > 0)
		{
			$investigationRec = $this->db->select('clinic_investigation_id')->from('clinic_investigations')->where('investigation_id =',$investigationInfo->investigation_id)->where('clinic_id =',$clinic_id)->get()->row();		

			// echo $this->db->last_query();

			if(count($investigationRec) > 0){
				echo $investigationRec->clinic_investigation_id;
			}else{
				echo 0;
			}
		}
	}


	/*
	* Function edit_investigation
	* This method/function will edit the informaiton concern to the clinic investigation
	* w.r.to the clinic id & investigation id & clinic investigation id
	* Dev: Uday Kanth Rapalli
	*/
	public function edit_investigation($clinic_investigation_id = '', $investigation_id = '', $type = '')
	{

		$data['template_type'] = $this->Generic_model->getFieldValue('lab_templates','template_type',array('investigation_id'=>$investigation_id));
		$data['type'] = $type;
		$data['investigationInfo'] = $this->db->select('CI.clinic_investigation_id, CI.short_form, CI.price, CI.low_range, CI.high_range, CI.units, CI.method, CI.other_information, CI.remarks, I.item_code, I.investigation, I.category')->from('clinic_investigations CI')->join('investigations I','CI.investigation_id = I.investigation_id')->where('CI.clinic_investigation_id =',$clinic_investigation_id)->get()->row();

		if($data['template_type'] == 'General'){
			$data['remarks'] = $this->Generic_model->getFieldValue('lab_template_line_items','remarks',array('investigation_id'=>$investigation_id));	
		}

		// Get the parent investigation id for the lab template line item
		// $parentInvestigationId = $this->Generic_model->getFieldValue('lab_template_line_items','parent_investigation_id',array('investigation_id'=>$data['investigation_id']));

		// echo '<br>Parent Investigation ID: '.$parentInvestigationId."<br><br>";

		// if($parentInvestigationId != ''){
		// 	$template_type = $this->Generic_model->getFieldValue('lab_templates','template_type',array('investigation_id'=>$parentInvestigationId));
		// }else{
		// 	$template_type = $this->Generic_model->getFieldValue('lab_templates','template_type',array('investigation_id'=>$data['investigation_id']));
		// }

		// echo $this->db->last_query()."<br>";

		// echo '<pre>';
		// echo "Template Type: ".$data['template_type'];
		// echo '</pre>';
		// echo '<pre>';
		// print_r($data);
		// exit();

		$param = $this->input->post();

		if(count($param)>0){

			$result = $this->Generic_model->updateData('clinic_investigations', $param, array('clinic_investigation_id' => $_POST['clinic_investigation_id']));

			if($result){
				redirect('Lab/investigations');
			}
		}
		$data['view'] = 'lab/edit_investigation';
		$this->load->view('layout', $data);
	}


	public function get_investigation_info()
	{
		$clinic_id = $this->session->userdata('clinic_id');
		
		$investigationName = $_POST['invg'];

		// Get investigation id from the name
		$investigationInfo = $this->db->select('investigation_id, item_code, investigation, short_form, sample_type, category')->from('investigations')->where('investigation =',$investigationName)->get()->row();

		$investigationInfo = (array)$investigationInfo;

		// Get the lab template belongs to the above investigation id
		$labTemplateInfo = $this->db->select('lab_template_id, template_type')->from('lab_templates')->where('investigation_id =',$investigationInfo['investigation_id'])->get()->row();

		$investigation[0] = array_merge($investigationInfo,array('template_type'=>$labTemplateInfo->template_type));
		
		// If lab_template exists
		// Get the lab_templates_line_items
		if(count($labTemplateInfo) > 0){

			$template_type = $labTemplateInfo->template_type;
			$labTemplateLineItems = $this->db->select('investigation_id, remarks')->from('lab_template_line_items')->where('lab_template_id =',$labTemplateInfo->lab_template_id)->get()->result_array();

			if(count($labTemplateLineItems) > 1){
				$i = 1;
				// $investigation['template_type'] = $template_type;
				foreach($labTemplateLineItems as $lineItemRec){
					$investigationRec = $this->db->select('investigation_id, item_code, investigation, short_form, sample_type, category')->from('investigations')->where('investigation_id=',$lineItemRec['investigation_id'])->get()->result_array();					
					$investigation[$i] = array_merge($investigationRec[0], array('template_type'=>$template_type), array('remarks'=>$lineItemRec['remarks']));
					$i++;
				}
			}
		}

		// $info = $this->db->query("select * from investigations where investigation like '".$investigation."%'")->row();
		// echo $this->db->last_query();
		// print_r($investigation);
		$investigationJSON = json_encode($investigation);
		echo $investigationJSON;
		// exit();

		// if(count($info)>0)
		// {
		// 	$clinic_investigation = $this->db->query("select * from clinic_investigations where investigation_id=".$info->investigation_id." ".$cond)->row();
		// 	if(count($clinic_investigation)>0)
		// 		echo $info->investigation_id.":".$info->investigation.":".$info->item_code.":".$info->category.":".$info->mrp.":".$info->short_form.":".$clinic_investigation->low_range.":".$clinic_investigation->high_range.":".$clinic_investigation->units.":".$clinic_investigation->method.":".$clinic_investigation->other_information;
		// 	else
		// 		echo $info->investigation_id.":".$info->investigation.":".$info->item_code.":".$info->category.":".$info->mrp.":".$info->short_form.":"."".":"."".":"."".":"."".":"."";
		// }
		// else
		// {		
		// 	echo '';
		// }
	}


	public function get_clinic_investigation_info_order()
	{
		$clinic_id = $this->session->userdata('clinic_id');
		
		$investigation_name = $_POST['investigation'];
		
		$info = $this->db->select('I.investigation_id, I.investigation, I.item_code, I.category, I.short_form, CI.clinic_investigation_id, CI.price')->from('clinic_investigations CI')->join('investigations I','CI.investigation_id = I.investigation_id','inner')->where('I.investigation =',$investigation_name)->where('clinic_id =',$clinic_id)->get()->row();

		$packageInfo = $this->db->select('clinic_investigation_package_id, package_name, price')->from('clinic_investigation_packages')->where('clinic_id =',$clinic_id)->like('package_name',$investigation_name)->get()->row();

		$lab_discount = $this->Generic_model->getFieldValue('clinics','lab_discount',array('clinic_id =', $clinic_id));

		if(count($info) > 0){
			echo $info->clinic_investigation_id.":".$info->investigation.":".$info->item_code.":".$info->category.":".$info->price.":".$info->short_form.":".$lab_discount.":".$info->investigation_id.":investigation";		
		}else if(count($packageInfo) > 0){
			echo $packageInfo->clinic_investigation_package_id.":".$packageInfo->package_name.":::".$packageInfo->price.":".$lab_discount.":package";
		}else{		
			echo '';
		}
	}


	public function get_clinic_package_info()
	{
		$clinic_id = $this->session->userdata('clinic_id');
		
		$package_name = $_POST['package_name'];
		$package_name = substr($package_name,0,-10); // This will cut the string " (Package)" form the package name var;

		$info = $this->db->select("clinic_investigation_package_id, clinic_id, package_name, item_code, price, status")->from("clinic_investigation_packages")->where("package_name =",$package_name)->get()->row();

		// echo $this->db->last_query();

		$lab_discount = $this->Generic_model->getFieldValue('clinics','lab_discount',array('clinic_id =', $clinic_id));
		
		if(count($info) > 0){
			echo $info->clinic_investigation_package_id.":".$info->package_name.":".$info->item_code.":".$info->price.":".$lab_discount;		
		}else{		
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

		if($package_investigations->cnvg=='')
			$cnvgids = 0;
		else
			$cnvgids = $package_investigations->cnvg;	

		$clinic_invg = $this->db->query("select DISTINCT(investigation) from investigations a inner join clinic_investigations b on a.investigation_id=b.investigation_id where clinic_investigation_id not in (".$cnvgids.") ".$cond)->result_array();

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

		if(count($param) > 0){

			$line_items = count($_POST['billing_line_items']);

			for($i=1; $i<=$line_items; $i++){
				$lineinfo['clinic_investigation_package_id'] = $pid;	
				$lineinfo['clinic_investigation_id'] = $_POST['billing_line_items'][$i]['clinic_investigation_id'];			
				$lineinfo['investigation_id'] = $_POST['billing_line_items'][$i]['investigation_id'];
				$lineinfo['status'] = 1;
				$lineinfo['created_by'] = $user_id;
				$lineinfo['modified_by'] = $user_id;
				$lineinfo['created_date_time'] = date("Y-m-d H:i:s");
				$lineinfo['modified_date_time'] = date("Y-m-d H:i:s");
				$this->Generic_model->insertDataReturnId("clinic_investigation_package_line_items", $lineinfo);
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
		if($clinic_id != 0)
			$cond = "where clinic_id=".$clinic_id;

		$data['methods'] = $methods;
		$data['templateinfo'] = $this->db->query("select * from lab_templates where lab_template_id=".$tid)->row();
		$data['templatelineinfo'] = $this->db->query("select * from lab_template_line_items where lab_template_id='".$tid."' order by position")->result();

		$data['investigation_master_json_file'] = $this->Generic_model->getFieldValue('master_version','json_file_name',array('master_name'=>'investigation'));

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

		$lab_template_id = $_POST['lab_template_id'];
		$user_id = $this->session->userdata('user_id');
		$template_type = $_POST['template_type'];

		echo '<pre>';
		print_r($_POST);

		$count = count($_POST['parameter']);

		for($i=0; $i<$count; $i++){

			// Check the investigation & Create one if not found any investigation w.r.to the parameter data
			if($_POST['investigation_id'][$i] == '' || $_POST['investigation_id'][$i] == 0){
				// Get the investigation ID for the parameter
				$investigation_id = $this->Generic_model->getFieldValue('investigations','investigation_id',array('investigation'=>$_POST['parameter'][$i]));

				if($investigation_id != ''){
					$_POST['investigation_id'][$i] = $investigation_id;
				}else{
					// Not found any investigation with parameter name
					// Create a new investigation in investigations db
					$investigation['investigation'] = $_POST['parameter'][$i];
					$investigation['admin_review'] = 0;
					$investigation['status'] = 1;

					// Get the last item_code of the investigations and generate the new one
					$get_item_code = $this->db->select("item_code")->from('investigations')->order_by('item_code','DESC')->limit(1)->get()->row();
                	$investigation['item_code']= ++$get_item_code->item_code;

                	$investigation['created_by'] = $investigation['modified_by'] = $user_id;
                	$investigation['created_date_time'] = $investigation['modified_date_time'] = date('Y-m-d H:i:s');

					$_POST['investigation_id'][$i] = $this->Generic_model->insertDataReturnId('investigations',$investigation);

					// Params : master_table_name, field_name, master_name, clinic_id
                	update_master_version('investigations','investigation','investigation',0);
				}
			}

			// Get the data array ready
			$templateLineItem['lab_template_id'] = $_POST['lab_template_id'];
			$templateLineItem['parent_investigation_id'] = $_POST['parent_investigation_id'];
			$templateLineItem['parameter'] = $_POST['parameter'][$i];
			$templateLineItem['investigation_id'] = $_POST['investigation_id'][$i];
			$templateLineItem['remarks'] = $_POST['remarks'][$i];


			// If already existing lab template line item then should update it
			if($_POST['lab_template_line_item_id'][$i] != '' || $_POST['lab_template_line_item_id'][$i] != 0){
				
				// Update the line item record w.r.to line item Id
				// echo '<pre>';
				// echo "Update";
				// print_r($templateLineItem);
				$this->Generic_model->updateData('lab_template_line_items',$templateLineItem,array('lab_template_line_item_id'=>$_POST['lab_template_line_item_id'][$i]));

			}else{
				// We are here, because it is a new line item... Insert it into lab_template_line_item DB
				// echo '<pre>';
				// echo "New insert";
				// print_r($templateLineItem);
				$this->Generic_model->insertData('lab_template_line_items',$templateLineItem);
			}
		}

		// exit();
	
		// $this->db->query("delete from lab_template_line_items where lab_template_id=".$c_invg_id);

		// if($template_type == 'Excel')
		// {

		// 	$invg_ids = count($_POST['parameter']);	
			
		// 	for($i=0;$i<$invg_ids;$i++){
		// 		$lineinfo['lab_template_id'] = $c_invg_id;
		// 		$ingname = str_replace("'", "''", $_POST['parameter'][$i]);
		// 		$ingv = $this->db->query("select * from investigations where investigation='".$ingname."'")->row();
		// 		$last_id = $this->db->query("select * from investigations")->result_array();
		// 		$lcount = count($last_id);
		// 		if(count($ingv)>0)
		// 			$lineinfo['investigation_id'] = $ingv->investigation_id;
		// 		else{
		// 			$insinfo['item_code'] = "UMD".($lcount+1);
		// 			$insinfo['investigation'] = $ingname;				
		// 			$insinfo['category'] = "Lab";
		// 			$insinfo['status'] = 1;
		// 			$insinfo['review'] = 1;
		// 			$insinfo['created_by'] = $user_id;
		// 			$insinfo['modified_by'] = $user_id;
		// 			$insinfo['created_date_time'] = date("Y-m-d H:i:s");
		// 			$insinfo['modified_date_time'] = date("Y-m-d H:i:s");
		// 			$last_inserted_id = $this->Generic_model->insertDataReturnId("investigations",$insinfo);
		// 			$lineinfo['investigation_id'] = $last_inserted_id;
		// 			$this->investigation_json();
		// 		}
		// 		$lineinfo['parameter'] = $_POST['parameter'][$i];

		// 		$lineinfo['status'] = 1;
		// 		$lineinfo['created_by'] = $user_id;
		// 		$lineinfo['modified_by'] = $user_id;
		// 		$lineinfo['created_date_time'] = date("Y-m-d H:i:s");
		// 		$lineinfo['modified_date_time'] = date("Y-m-d H:i:s");
		// 		$this->Generic_model->insertDataReturnId("lab_template_line_items",$lineinfo);
			
		// 	}
		// }
		// else if($template_type=='General')
		// {

		// 	$invg_ids = count($_POST['parameter']);

		// 	for($i=0;$i<$invg_ids;$i++){
		// 		$lineinfo['lab_template_id'] = $c_invg_id;
		// 		$ingname = $_POST['parameter'][$i];
		// 		$ingv = $this->db->query("select * from investigations where investigation='".$ingname."'")->row();
		// 		if(count($ingv)>0)
		// 			$lineinfo['investigation_id'] = $ingv->investigation_id;
		// 		else
		// 			$lineinfo['investigation_id'] = 0;
		// 		$lineinfo['parameter'] = $_POST['parameter'][$i];			
		// 		$lineinfo['remarks'] = $_POST['remarks'][$i];	
		// 		$lineinfo['status'] = 1;
		// 		$lineinfo['created_by'] = $user_id;
		// 		$lineinfo['modified_by'] = $user_id;
		// 		$lineinfo['created_date_time'] = date("Y-m-d H:i:s");
		// 		$lineinfo['modified_date_time'] = date("Y-m-d H:i:s");
		// 		$this->Generic_model->insertDataReturnId("lab_template_line_items",$lineinfo);	
		// 	}

		// }
		redirect('Lab/mastertemplate_view/'.$lab_template_id);
	}


	// Creating json with investigation masters
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

	public function getInvestigationId(){
		if($_POST['investigation']){
			extract($_POST);
			$investigation_id = $this->Generic_model->getFieldValue('investigations','investigation_id',array('investigation' => $investigation));

			echo $investigation_id;
		}else{
			echo 0;
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
		// $cond = '';
		// if($clinic_id!=0)
		// 	$cond = "and clinic_id=".$clinic_id;

		$data['clinic_templates'] = $this->db->query("select * from lab_templates where archive=0 ".$cond)->result_array();

		// $data['clinic_templates'] = $this->db->select()

		$data['view'] = 'lab/master_templates_list';
		$this->load->view('layout', $data);
	}
}
?>