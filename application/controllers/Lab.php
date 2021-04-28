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
		$patientInvestigations = $this->db->select('PI.patient_id, PI.patient_investigation_id,
		 PI.doctor_id, PI.clinic_id, PI.appointment_id')
		 ->from('patient_investigation as PI')
		 ->where('PI.clinic_id =',$clinic_id)
		 ->like('PI.created_date_time', $currentDate)
		 ->group_by('PI.patient_id','ASC')->get()->result_array();


		// echo $this->db->last_query()."\n\n";

		$expectedRevenue =0;
		$convertedRevenue = 0;
		$outPeopleRevenue = 0;
		$outPeopleRevenuee = 0;

		if(count($patientInvestigations) > 0){

			$data['prescriptions_count'] = count($patientInvestigations);


			// Get the investigations prescribed in each prescription
			foreach($patientInvestigations as $prescription){

				// $investigationsPrescribed = $this->db->query('select PILI.investigation_id,CIP.price  from 
				// patient_investigation_line_items PILI INNER JOIN
				// clinic_investigations CI ON PILI.investigation_id = CI.investigation_id 
				// inner join clinic_investigation_price CIP ON CIP.clinic_investigation_id = CI.clinic_investigation_id
				// WHERE CI.clinic_id = '.$clinic_id.' AND
				//  PILI.patient_investigation_id = '.$prescription['patient_investigation_id'].'
				//   AND CI.status = 1')->result();

				  $investigationsPrescribed = $this->db->query('select PILI.investigation_id,CIP.price, PILI.patient_investigation_id   from 
				  patient_investigation_line_items PILI INNER JOIN
				  clinic_investigations CI ON PILI.investigation_id = CI.investigation_id 
				  inner join clinic_investigation_price CIP ON CIP.clinic_investigation_id = CI.clinic_investigation_id
				  WHERE CI.clinic_id = '.$clinic_id.' AND
				   PILI.patient_investigation_id = '.$prescription['patient_investigation_id'].'
					AND CI.status = 1')->result();

				// 	echo "<pre>";
				//    print_r($investigationsPrescribed);	
				//    echo "</pre>";
				//   $this->db->select('PILI.investigation_id, CIP.price');
				//   $this->db->from('patient_investigation_line_items PILI');
				//   $this->db->join('clinic_investigations CI','PILI.investigation_id = CI.investigation_id','inner');
				//   $this->db->join('clinic_investigation_price CIP','CIP.clinic_investigation_id = CI.clinic_investigation_id','inner');
				//   $this->db->where('CI.clinic_id =',$clinic_id);
				//   $this->db->where('CI.clinic_idPILI.patient_investigation_id =',$prescription['patient_investigation_id']);
				//   $this->db->where('CI.status = 1');
				// //   $this->db->order_by("PI.patient_investigation_id","DESC");
		  
				//   $data['investigationsPrescribed']=$investigationsPrescribed = $this->db->get()->result();

				if(count($investigationsPrescribed) > 0){
					foreach($investigationsPrescribed as $investigationAmount){
						// $expectedRevenue = (float)$expectedRevenue + (float)$investigationAmount->price;
						 $data['expectedRevenue'] =$expectedRevenue +=  (float)$investigationAmount->price;
						
						// $expectedRevenue =11;
					}				
				}
				// print_r('welcome');	

				// Check whether this prescription converted as a bill or no
				// If converted get the amount of the billing
				$convertedPrescription = $this->db->select('billing_id')->from('billing')
				->where('patient_investigation_id =',$prescription['patient_investigation_id'])->get()->result_array();

				if(count($convertedPrescription) > 0){

					$data['billing_count'] = count($convertedPrecription);

					foreach($convertedPrescription as $billing){

						// Get the line items of the billing and sum of the amounts
						// $billingInfo = $this->db->select('sum(amount) as amount')
						// ->from('billing_line_items')->where('billing_id =',$billing['billing_id'])->get()->row();
						// $billingInfo = $this->db->select('sum(amount) as amount')
						// ->from('billing_line_items')->where('billing_id =',$billing['billing_id'])->get()->row();
						$billingInfo = $this->db->select('sum(billing_amount) as amount,sum(osa) as osa')
						->from('billing')->where('billing_id =',$billing['billing_id'])
						->where('clinic_id =',$clinic_id)
						->get()->row();

						// $data['billingInfo'] = $this->db->select('billing_id, appointment_id, 
						// doctor_id, clinic_id, patient_id, guest_name, guest_mobile, billing_date_time,
						// total_amount, billing_amount, discount, discount_unit, osa, payment_status')
						// ->from('billing')->where('clinic_id =',$clinic_id)
						// ->where('billing_id =',$billing_id)->get()->row();

						$convertedRevenue = (float)$convertedRevenue + (float)$billingInfo->amount-(float)$billingInfo->osa;
						// $convertedRevenue =212;
					}
					
				
				}
// 				echo "<pre>";
// 				print_r($convertedRevenue);	
//    echo "</pre>";
			}

			$data['expected_revenue'] = number_format(round($expectedRevenue),2);
			$data['converted_revenue'] = number_format(round($convertedRevenue),2);

		}

		// Get the list of customers purchaseddrugs from the pharmacy who are outsiders w.r.to the date
		$outPeople = $this->db->select('billing_id, guest_name, guest_mobile')
		->from('billing')->where('patient_investigation_id =',0)
		->where('appointment_id =',0)
		->where('doctor_id =',0)
		->where('clinic_id =',$clinic_id)->where('billing_type =','Lab')
		->like('billing_date_time', $tdate)->get()->result_array();

		if(count($outPeople) > 0){

			$data['out_people_count'] = count($outPeople);

			foreach($outPeople as $person){

				// Get the billing line items info with amount summation
				// $personBillingInfo = $this->db->select('sum(amount) as amount')
				// ->from('billing_line_items')->where('billing_id ='.$person['billing_id'])->get()->row();
				$billingInfo = $this->db->select('sum(billing_amount) as amount,sum(osa) as osa')
				->from('billing')->where('billing_id =',$person['billing_id'])
				->where('clinic_id =',$clinic_id)
				->get()->row();
				$outPeopleRevenue = (float)$outPeopleRevenue + (float)$billingInfo->amount-(float)$billingInfo->osa;
				// $outPeopleRevenue = 3333;
			}

			//  echo "<pre>";
            // print_r($outPeopleRevenue);
            // echo "</pre>";

		}

		//out standing amount outside customers 
			$outPeoplee = $this->db->select('billing_id, guest_name, guest_mobile')
			->from('billing')->where('patient_investigation_id =',0)
			->where('appointment_id =',0)
			->where('doctor_id =',0)
			->where('clinic_id =',$clinic_id)->where('billing_type =','Lab')
			->like('billing_date_time', $tdate)->get()->result_array();
	
			if(count($outPeoplee) > 0){
	
				$data['out_people_count'] = count($outPeoplee);
	
				foreach($outPeoplee as $person){
	
					// Get the billing line items info with amount summation
					// $personBillingInfo = $this->db->select('sum(amount) as amount')
					// ->from('billing_line_items')->where('billing_id ='.$person['billing_id'])->get()->row();
					$billingInfo = $this->db->select('sum(billing_amount) as amount,sum(osa) as osa')
					->from('billing')->where('billing_id =',$person['billing_id'])
					->where('clinic_id =',$clinic_id)
					->get()->row();
					$outPeopleRevenuee = (float)$outPeopleRevenuee +(float)$billingInfo->osa;
					// $outPeopleRevenue = 3333;
				}
	
				//  echo "<pre>";
				// print_r($outPeopleRevenuee);
				// echo "</pre>";
	
			}

			
			// Discount Amount
			$discountAmount = $this->db->select('sum(total_amount) as total_amount,sum(billing_amount) as amount')->from('billing')
			->where('clinic_id =',$clinic_id)
			->like('created_date_time', $currentDate)
			->where('discount_status=1')
			->get()->result_array();

			if(($discountAmount[0]['total_amount'])>$discountAmount[0]['amount'])
			{
				$discountRevenue = (float)$discountAmount[0]['total_amount']  - (float)$discountAmount[0]['amount'];
			}
			
			// echo "<pre>";
			// print_r($discountAmount[0]['total_amount']);
			// print_r($discountAmount[0]['amount']);
			// print_r($discountRevenue);
			// echo "</pre>";
							
		$data['out_people_osa'] = number_format(round($outPeopleRevenuee),2);
		$data['out_people_revenue'] = number_format(round($outPeopleRevenue),2);
		$data['discountAmount'] = number_format(round($discountRevenue),2);
		$data['lost_revenue'] = number_format(round($expectedRevenue) - round($convertedRevenue),2);
		// print_r($discountRevenue);


		$data['view'] = 'lab/dashboard';
		$this->load->view('layout', $data);
	}
	// public function dashboard()
	// {
	// 	$clinic_id = $this->session->userdata('clinic_id');

	// 	$data['clinic_id'] = $clinic_id;

	// 	$cond = '';

	// 	if($clinic_id != 0)
	// 		$cond = "where clinic_id=".$clinic_id;

	// 	$currentDate = date('Y-m-d');

	// 	if($this->session->userdata('role_id') == 4){
	// 		$data['doctors_list'] = $this->Generic_model->getAllRecords('clinic_doctor', array('clinic_id' => $clinic_id,'doctor_id'=>$this->session->userdata('user_id')), $order = '');    
	// 	}else{
	// 		$this->db->select('distinct(doctor_id)');
	// 		$this->db->from('clinic_doctor');

	// 		if($clinic_id != 0){
	// 			$this->db->where("clinic_id = ",$clinic_id);
	// 		}

	// 		$data['doctors_list'] = $this->db->get()->result();
	// 	}

	// 	// Get the list of patient investigations
	// 	$patientInvestigations = $this->db->select('PI.patient_id, PI.patient_investigation_id, PI.doctor_id, PI.clinic_id, PI.appointment_id')->from('patient_investigation as PI')->where('PI.clinic_id =',$clinic_id)->like('PI.created_date_time', $currentDate)->group_by('PI.patient_id','ASC')->get()->result_array();

	// 	// echo $this->db->last_query()."\n\n";

	// 	$expectedRevenue = 0;
	// 	$convertedRevenue = 0;
	// 	$outPeopleRevenue = 0;

	// 	if(count($patientInvestigations) > 0){

	// 		$data['prescriptions_count'] = count($patientInvestigations);


	// 		// Get the investigations prescribed in each prescription
	// 		foreach($patientInvestigations as $prescription){

	// 			$investigationsPrescribed = $this->db->query('select PILI.investigation_id, CI.price from patient_investigation_line_items PILI INNER JOIN clinic_investigations CI ON PILI.investigation_id = CI.investigation_id WHERE CI.clinic_id = '.$clinic_id.' AND PILI.patient_investigation_id = '.$prescription['patient_investigation_id'].' AND CI.status = 1')->result();

	// 			if(count($investigationsPrescribed) > 0){
	// 				foreach($investigationsPrescribed as $investigationAmount){
	// 					$expectedRevenue = (float)$expectedRevenue + (float)$investigationAmount->price;	
	// 				}				
	// 			}

	// 			// Check whether this prescription converted as a bill or no
	// 			// If converted get the amount of the billing
	// 			$convertedPrescription = $this->db->select('billing_id')->from('billing')->where('patient_investigation_id =',$prescription['patient_investigation_id'])->get()->result_array();

	// 			if(count($convertedPrescription) > 0){

	// 				$data['billing_count'] = count($convertedPrecription);

	// 				foreach($convertedPrescription as $billing){

	// 					// Get the line items of the billing and sum of the amounts
	// 					$billingInfo = $this->db->select('sum(amount) as amount')->from('billing_line_items')->where('billing_id =',$billing['billing_id'])->get()->row();
	// 					$convertedRevenue = (float)$convertedRevenue + (float)$billingInfo->amount;
	// 				}
	// 			}

	// 		}

	// 		$data['expected_revenue'] = number_format(round($expectedRevenue),2);
	// 		$data['converted_revenue'] = number_format(round($convertedRevenue),2);

	// 	}

	// 	// Get the list of customers purchaseddrugs from the pharmacy who are outsiders w.r.to the date
	// 	$outPeople = $this->db->select('billing_id, guest_name, guest_mobile')->from('billing')->where('patient_investigation_id =',0)->where('clinic_id =',$clinic_id)->where('billing_type =','Lab')->like('billing_date_time', $tdate)->get()->result_array();

	// 	if(count($outPeople) > 0){

	// 		$data['out_people_count'] = count($outPeople);

	// 		foreach($outPeople as $person){

	// 			// Get the billing line items info with amount summation
	// 			$personBillingInfo = $this->db->select('sum(amount) as amount')->from('billing_line_items')->where('billing_id ='.$person['billing_id'])->get()->row();
	// 			$outPeopleRevenue = (float)$outPeopleRevenue + (float)$personBillingInfo->amount;
	// 		}

	// 	}

	// 	$data['out_people_revenue'] = number_format(round($outPeopleRevenue),2);
	// 	$data['lost_revenue'] = number_format(round($expectedRevenue) - round($convertedRevenue),2);

	// 	$data['view'] = 'lab/dashboard';
	// 	$this->load->view('layout', $data);
	// }


	// public function getFinances(){

	// 	$clinic_id = $this->session->userdata('clinic_id');
	// 	$start = $_POST['startDate'];
	// 	$end = date('Y-m-d', strtotime($_POST['endDate'] . ' +1 day'));
	// 	$d_id = $_POST['d_id'];

	// 	if($this->session->userdata('role_id') == 4){
	// 		$data['doctors_list'] = $this->Generic_model->getAllRecords('clinic_doctor', array('clinic_id' => $clinic_id,'doctor_id'=>$this->session->userdata('user_id')), $order = '');    
	// 	}else{
	// 		$this->db->select('distinct(doctor_id)');
	// 		$this->db->from('clinic_doctor');
	// 		if($clinic_id != 0)
	// 			$this->db->where("clinic_id = ",$clinic_id);
	// 		$data['doctors_list'] = $this->db->get()->result();
	// 	}

	// 	// Condition showing with respect to the doctor
	// 	if($d_id == "all"){
	// 		$docCondition = "";
	// 	}else{
	// 		$docCondition = " and PI.doctor_id = ".$d_id;
	// 	}

	// 	if($start == $end){
	// 		$patientInvestigations = $this->db->query("Select PI.patient_id, PI.patient_investigation_id, PI.doctor_id, PI.clinic_id, PI.appointment_id from patient_investigation as PI where PI.clinic_id ='".$clinic_id."'".$docCondition." and PI.created_date_time LIKE '".$start."%'")->result();
	// 	}else{
	// 		$patientInvestigations = $this->db->query("Select PI.patient_id, PI.patient_investigation_id, PI.doctor_id, PI.clinic_id, PI.appointment_id from patient_investigation as PI where PI.clinic_id ='".$clinic_id."'".$docCondition." and PI.created_date_time BETWEEN '".$start."%' and '".$end."%'")->result();
	// 	}

	// 	$expectedRevenue = 0;
	// 	$convertedRevenue = 0;
	// 	$outPeopleRevenue = 0;
	// 	$billingCount = 0;

	// 	if(count($patientInvestigations) > 0){

	// 		// Get the drugs prescribed in each prescription
	// 		foreach($patientInvestigations as $prescription){

	// 			// $investigationsPrescribed = $this->db->select('PILI.investigation_id, CI.price')->from('patient_investigation_line_items PILI')->join('clinic_investigations CI','PILI.investigation_id = CI.investigation_id','inner')->where('CI.clinic_id = '.$clinic_id)->where('PILI.patient_investigation_id = '.$prescription->patient_investigation_id)->get()->result_array();

	// 			// if(count($investigationsPrescribed) > 0){
	// 			// 	foreach($investigationsPrescribed as $investigationAmount){
	// 			// 		$expectedRevenue = (float)$expectedRevenue + (float)$investigationAmount['price'];
	// 			// 	}				
	// 			// }

	// 			$investigationsPrescribed = $this->db->query('select PILI.investigation_id, CI.price from patient_investigation_line_items PILI INNER JOIN clinic_investigations CI ON PILI.investigation_id = CI.investigation_id WHERE CI.clinic_id = '.$clinic_id.' AND PILI.patient_investigation_id = '.$prescription['patient_investigation_id'].' AND CI.status = 1')->result();

	// 			if(count($investigationsPrescribed) > 0){
	// 				foreach($investigationsPrescribed as $investigationAmount){
	// 					$expectedRevenue = (float)$expectedRevenue + (float)$investigationAmount->price;	
	// 				}				
	// 			}

	// 			// Check whether this prescription converted as a bill or no
	// 			// If converted get the amount of the billing
	// 			$convertedPrecription = $this->db->select('billing_id')->from('billing')->where('patient_investigation_id =',$prescription->patient_prescription_id)->get()->result_array();

	// 			if(count($convertedPrecription) > 0){

	// 				$billingCount = $billingCount++;

	// 				foreach($convertedPrecription as $billing){

	// 					// Get the line items of the billing and sum of the amounts
	// 					$billingInfo = $this->db->select('sum(amount) as amount')->from('billing_line_items')->where('billing_id =',$billing['billing_id'])->get()->row();
	// 					$convertedRevenue = (float)$convertedRevenue + (float)$billingInfo->amount;
	// 				}
	// 			}

	// 		}

	// 	}

	// 	$data['prescriptions_count'] = count($patientInvestigations);
	// 	$data['billing_count'] = $billingCount;
	// 	$data['expected_revenue'] = number_format(round($expectedRevenue),2);
	// 	$data['converted_revenue'] = number_format(round($convertedRevenue),2);
	// 	$data['lost_revenue'] = number_format(round($expectedRevenue) - round($convertedRevenue),2);

	// 	// Get the list of customers purchased drugs from the pharmacy who are outsiders w.r.to the date
	// 	if($start == $end){
	// 		$outPeople = $this->db->query("select billing_id, guest_name, guest_mobile from billing where patient_investigation_id = 0 and clinic_id = '".$clinic_id."' and billing_type = 'Lab' and billing_date_time like '".$start."%'")->result();
	// 	}else{
	// 		$outPeople = $this->db->query("select billing_id, guest_name, guest_mobile from billing where patient_investigation_id = 0 and clinic_id = '".$clinic_id."' and billing_type = 'Lab' and billing_date_time between '".$start."%' and '".$end."%'")->result();
	// 	}

	// 	if(count($outPeople) > 0){

	// 		$data['out_people_count'] = count($outPeople);

	// 		foreach($outPeople as $person){

	// 			// Get the billing line items info with amount summation
	// 			$personBillingInfo = $this->db->select('sum(amount) as amount')->from('billing_line_items')->where('billing_id ='.$person->billing_id)->get()->row();
	// 			$outPeopleRevenue = (float)$outPeopleRevenue + (float)$personBillingInfo->amount;
	// 		}

	// 	}else{
	// 		$data['out_people_count'] = 0;
	// 	}

	// 	$data['out_people_revenue'] = number_format(round($outPeopleRevenue),2);

	// 	echo $data['expected_revenue']."*".$data['converted_revenue']."*".$data['out_people_revenue']."*".$data['lost_revenue']."*".$data['prescriptions_count']."*".$data['billing_count']."*".$data['out_people_count'];

	// }	

	public function getFinances(){
		$clinic_id = $this->session->userdata('clinic_id');
        $start = $_POST['startDate'];
        $end = date('Y-m-d', strtotime($_POST['endDate'] . ' +1 day'));
        $d_id = $_POST['d_id'];
        $expected = 0;$converted = 0;$converted_discounts = 0;$out_discounts = 0;$outrevenue = 0;

        if($start == $end)
        {
            $exDateCond = "pi.created_date_time LIKE '".$start."%'";
            $billDateCond = "created_date_time LIKE '".$start."%'";
        }
        else
        {
            $exDateCond = "pi.created_date_time BETWEEN '".$start."%' AND '".$end."%'";
            $billDateCond = "created_date_time BETWEEN '".$start."%' AND '".$end."%'";
        }

        if($d_id == "all")
        {
            $exCon = 'and '.$exDateCond;
            $billCon = 'and '.$billDateCond;
            $outBillCon = "and ".$billDateCond;
        }
        else
        {
            $exCon = "and pi.doctor_id = '".$d_id."' and ".$exDateCond;
            $billCon = "and doctor_id = '".$d_id."' and ".$billDateCond;
            $outBillCon = "and ".$billDateCond;
        }
		// Expected Revenue
		$expectedInfo = $this->db->query("select pil.investigation_id from patient_investigation pi,patient_investigation_line_items pil where pi.patient_investigation_id=pil.patient_investigation_id and pi.clinic_id='".$clinic_id."' ".$exCon)->result();
		// echo $this->db->last_query();
        $expectedCount = $this->db->query("select count(*) as expectedCount from patient_investigation pi where EXISTS (SELECT * from patient_investigation_line_items pil where pi.patient_investigation_id=pil.patient_investigation_id) and pi.clinic_id='".$clinic_id."' ".$exCon)->row();
        
        foreach($expectedInfo as $value)
        {
            $expected += getInvestigationPrice($value->investigation_id,$clinic_id);
        }
        // Converted Revenue
        $billing_master = $this->db->query("select * from billing where clinic_id = '".$clinic_id."' and billing_type='Lab' and (status='0' or status='1') and patient_investigation_id!='0' ".$billCon)->result();
        foreach($billing_master as $value)
        {
            $billing_line_info = $this->db->select("sum(amount) as amount,sum(total_amount-amount) as discount")->from("billing_line_items")->where("billing_id='".$value->billing_id."'")->get()->row();
            $converted += $billing_line_info->amount;
            $converted_discounts += $billing_line_info->discount;
        }
        // Out Patients Revenue
        $outBills = $this->db->query("select * from billing where clinic_id = '".$clinic_id."' and billing_type='Lab' and (status='0' or status='1') and patient_investigation_id='0' ".$outBillCon)->result();
        foreach($outBills as $value)
        {
            $OutBill_line_info = $this->db->select("sum(amount) as amount,sum(total_amount-amount) as discount")->from("billing_line_items")->where("billing_id='".$value->billing_id."'")->get()->row();
            $outrevenue += $OutBill_line_info->amount;
            $out_discounts += $OutBill_line_info->discount;
        }


        $revenue['expected_revenue'] = number_format(round($expected,2),2);
        $revenue['expected_prescriptions_count'] = $expectedCount->expectedCount;
        $revenue['converted_revenue'] = number_format(round($converted,2),2);
        $revenue['indiscounts'] = number_format(round($converted_discounts,2),2);
        $revenue['converted_prescriptions_count'] = count($billing_master);
        $revenue['out_people_revenue'] = number_format(round($outrevenue,2),2);
        $revenue['out_people_count'] = count($outBills);
        $revenue['outdiscounts'] = number_format(round($out_discounts,2),2);
        $revenue['lost_revenue'] = (($revenue['expected_revenue']-$revenue['converted_revenue']) <= 0) ? '0.00' : number_format(round(($expected-$converted),2),2);
        
        echo json_encode($revenue);
	}

	public function settings()
	{

		$clinic_id = $this->session->userdata('clinic_id'); 

        // Check if the data is submitting
		if($this->input->post('submit')){

			$clinic_lab_id = $this->input->post('clinic_lab_id');

			unset($_POST['submit']);

			$labData = $this->input->post();

			$config['upload_path']="./uploads/lab_logos/";
			$config['allowed_types']='jpg|JPG|png|PNG|jpeg|JPEG';
			$this->load->library('upload');    
			$this->upload->initialize($config); 
			$this->upload->do_upload('logo');
			$fileData=$this->upload->data('file_name');

			$createdDateInfo = get_CM_by_dates();

			if($fileData!="")
			{
				$labData = array_merge($labData, array('logo' => $fileData), $createdDateInfo);
			}

            // Perform Insert if New data Or Edit if existing data
            // Check if the clinic_lab_id has got a value. If not It is a new Phamracy data otherwise its an existing lab data
			if($clinic_lab_id != ''){
                // Update Lab Data
				$this->Generic_model->updateData('clinic_lab', $labData, array('clinic_lab_id'=>$clinic_lab_id));
				redirect('lab/settings'); 
			}else{
                // Unset the clinic_lab_id object
				unset($labData['clinic_lab_id']);

				$labData['clinic_id'] = $clinic_id;

                // Insert New Pharmacy Data
				$this->Generic_model->insertData('clinic_lab',$labData);
				redirect('lab/settings'); 
			}
		}else{
            // Fetch Data for showing up the Lab Information
			$info = $this->db->select('clinic_lab_id, name, email, mobile, logo, gst_number, max_discount, min_advance, referral_doctor_max_discount, address')->from('clinic_lab')->where('clinic_id =',$clinic_id)->get()->row();

			if(count($info) > 0){
				$data['lab_info'] = $info;
			}else{
				$data['lab_info'] = $this->db->select('clinic_name as name, incharge_mobile as mobile, email, address, clinic_logo')->from('clinics')->where('clinic_id =',$clinic_id)->get()->row();
			}

			$roles = $this->db->select('role_id')->from('roles')->where('role_name =','Phlebotomist')->or_where('role_name =','Consultant')->or_where('role_name =','Data Entry Operator')->or_where('role_name =','Lab Technician')->get()->result_array();

            // echo "<pre>";
            // print_r($roles);
            // echo "</pre>";

			$data['staff'] = array();

			foreach($roles as $role){
				echo $role['role_id'].'.....';
				$staff = $this->db->select('E.employee_id, E.first_name, E.last_name, E.employee_code, E.gender, E.date_of_joining, E.mobile, E.phone, E.email_id, U.role_id, U.profile_id, R.role_name as designation, P.profile_name as profile')->from('employees E')->join('users U','E.employee_id = U.user_id','inner')->join('roles R','U.role_id = R.role_id','inner')->join('profiles P','U.profile_id = P.profile_id','inner')->where('U.role_id =',$role['role_id'])->where('U.clinic_id =',$clinic_id)->get()->result_array();
            	// echo $this->db->last_query();

            	// echo '<pre>';
            	// print_r($staff);
            	// echo '</pre>';

				$data['staff'] = array_merge($data['staff'],$staff);
			}

		}

		$data['clinic_name'] = $this->session->userdata('clinic_name');
		$data['view'] = 'lab/settings';
		$this->load->view('layout', $data);

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

		// echo $this->db->last_query();
		// exit();

		$data['view'] = 'lab/prescriptions';
		$this->load->view('layout', $data);
	}

	public function LabInvoice($id){

		$data['clinic_id'] = $clinic_id = $this->session->userdata('clinic_id');
		$data['clinic_information'] = $this->db->select("*")->from("clinics")->where("clinic_id",$clinic_id)->get()->row();

		//    $clinic_id = $this->session->userdata("clinic_id");
		   $data['billingInvoiceInfo'] = $this->db->select("*")->from("billing_invoice")->where("billing_invoice_id",$id)->get()->row();
		   $data['billingInfo'] = $this->db->select("*")->from("billing")->where("billing_id",$data['billingInvoiceInfo']->billing_id)->get()->row();
		   $data['billingInvoice'] = $this->db->select("*")->from("billing_invoice")->where("billing_id",$data['billingInvoiceInfo']->billing_id)->get()->row();
		   $data['billingInvoice_records'] = $this->db->select("*")->from("billing_invoice")->where("billing_id",$data['billingInvoiceInfo']->billing_id)->get()->result();  
		   $data['billingLineItemsInfo'] = $this->db->select("*")->from("billing_line_items")->where("billing_id",$data['billingInvoiceInfo']->billing_id)->get()->result();
		   $data['patientInfo'] = $this->db->select("*")->from("patients")->where("patient_id",$data['billingInfo']->patient_id)->get()->row();
		   $clinic_id = $data['billingInfo']->clinic_id;
		   if($data['billingInfo']->doctor_id != 0)
		   {
			  $data['docInfo'] = $this->db->query("select d.*,de.department_name from doctors d,department de where d.department_id=de.department_id and d.doctor_id='".$data['billingInfo']->doctor_id."'")->row();
			  $data['clinicDocInfo'] = $this->db->select("*")->from("clinic_doctor")->where("clinic_id",$clinic_id)->where("doctor_id",$data['billingInfo']->doctor_id)->get()->row();
		   }
		   $data['clinicInfo'] = $this->db->select("*")->from("clinics")->where("clinic_id",$clinic_id)->get()->row();
		//    $data['view'] = "pdfViews/LabInvoice";
		//    $this->load->view('pdfViews/pdfLayout',$data);

		   $this->load->library('M_pdf');
		   $html = $this->load->view('lab/billing_pdf',$data,true);
		   $pdfFilePath = $data['billingInvoiceInfo']->invoice_no.".pdf";
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

	public function view_billing($billing_id){

		// Clinic Id
		$clinic_id = $this->session->userdata('clinic_id');

		// Get Billing Information
		$data['billingInvoice'] = $this->db->select('*')
		->from('billing_invoice')
		->where('billing_id =',$billing_id)->get()->row();

		$data['billingInfo'] = $this->db->select('billing_id, appointment_id, doctor_id, clinic_id, patient_id, guest_name, guest_mobile, billing_date_time, total_amount, billing_amount, discount, discount_unit, osa, payment_status')->from('billing')->where('clinic_id =',$clinic_id)->where('billing_id =',$billing_id)->get()->row();

		$data['billingLineItemsInfo'] = $this->db->select('BLI.billing_line_item_id, BLI.billing_id, BLI.item_information, BLI.investigation_id, BLI.amount')->from('billing_line_items BLI')->where('BLI.billing_id =',$billing_id)->get()->result_array();

		if($data['billingInfo']->doctor_id != 0){ 
			$data['doctorInfo'] = $this->db->select('doc.first_name, doc.last_name, dep.department_name')->from('doctors doc')->join('department dep','doc.department_id = dep.department_id','inner')->where('doc.doctor_id =',$data['billingInfo']->doctor_id)->get()->row();
		}

		// Get all billing invoices belings to billing_id
		$data['billingInvoicesInfo'] = $this->db->select('billing_invoice_id, billing_id, invoice_no, invoice_no_alias, invoice_date, payment_type, payment_mode, transaction_id, invoice_amount')->from('billing_invoice')->where('billing_id =',$billing_id)->get()->result_array();

		$data['view'] = 'lab/view_billing';
		$this->load->view('layout', $data);
	}


	public function view_prescription($pid){
		$data['patient_investigation_id']=$pid;
		$data['clinic_id']=$clinic_id = $this->session->userdata('clinic_id');
		$data['patient_investigations'] = $this->db->query("select * from patient_investigation pin,patient_investigation_line_items pil where pin.patient_investigation_id=pil.patient_investigation_id and pin.patient_investigation_id='".$pid."'")->result();
		$data['appInfo'] = $this->db->query("select * from appointments a,patients p where a.patient_id=p.patient_id and a.appointment_id='".$data['patient_investigations'][0]->appointment_id."'")->row();
		$data['docInfo'] = $this->db->query("select * from doctors d,department de where de.department_id=d.department_id and d.doctor_id='".$data['appInfo']->doctor_id."'")->row();
		$this->db->distinct();
		$this->db->select('CI.clinic_investigation_id,PIL.patient_investigation_line_item_id, PIL.patient_investigation_id, PIL.investigation_name, INV.item_code, INV.investigation, CIP.price');
		$this->db->from('patient_investigation_line_items PIL');
		$this->db->join('investigations INV', 'PIL.investigation_id = INV.investigation_id','left');
		$this->db->join('clinic_investigations CI', 'INV.investigation_id = CI.investigation_id', 'left');
		$this->db->join('clinic_investigation_price CIP', 'CIP.clinic_investigation_id = CI.clinic_investigation_id', 'left');
		$this->db->join('patient_investigation PI', 'PIL.patient_investigation_id = PI.patient_investigation_id','left');
		// $this->db->join('patients P', 'PI.patient_id = P.patient_id','left');
		// $this->db->join('appointments A', 'PI.appointment_id = A.appointment_id','left');
		// $this->db->join('doctors Doc', 'PI.doctor_id = Doc.doctor_id','left');
		// $this->db->join('department Dep', 'Doc.department_id = Dep.department_id','left');
		// $data['appInfo'] = $this->db->query("select * from appointments a,patients p where a.patient_id=p.patient_id and a.appointment_id='".$data['patient_investigations'][0]->appointment_id."'")->row();
		$getdetails= $this->db->query("select * from patient_investigation where patient_investigation_id='".$pid."'")->row();
		$data['doctor_details']=$this->db->query("select * from doctors where doctor_id='".$getdetails->doctor_id."'")->row();
		$data['patients_details']=$this->db->query("select * from patients where patients.patient_id='".$getdetails->patient_id."'")->row();
		$umr=$this->db->query("select * from patients where patients.patient_id='".$getdetails->patient_id."'")->row();
		$finalumr=$umr->umr_no;
		$data['appointments']=$this->db->query("select * from appointments where appointments.appointment_id='".$getdetails->appointment_id."'")->row();
		$data['dep']=$this->db->query("SELECT * FROM department,doctors WHERE doctors.department_id=department.department_id AND doctors.doctor_id='".$getdetails->doctor_id."'")->row();
		$data['table']=$table =$this->db->query("SELECT * FROM patient_investigation join 
		patient_investigation_line_items WHERE
		 patient_investigation.patient_investigation_id=patient_investigation_line_items.patient_investigation_id
		  AND patient_investigation.patient_investigation_id='".$pid."' and patient_investigation.clinic_id='".$clinic_id."'")->result();

		// $i=0;
		//   foreach($table as $tab)
		//   {
		// 	$data['id'][$i]=$tab->investigation_id;
		//     // $data['tablee']=$this->db->query("SELECT * FROM clinic_investigation_price  WHERE
		// 	// 						investigation_id='".$tab->investigation_id."'
		// 	// 						AND clinic_id='".$clinic_id."'")->result();

		// 	$data['tablee']=$this->db->select("css.*")->from("clinic_investigation_price css")
		// 	->where("css.investigation_id='436' and css.clinic_id='".$clinic_id."' ")->get()->
		// 	result_array();
		// 	$i++;
		//   }

		//   for($i=0;$i<=count($table)-1;$i++)
		//   {
		// 	$data['investigation_idd']=$table[$i]->investigation_id;

		// //   echo $investigation_idd;
		//   }

	// 	  $data['tablee']=$tablee =$this->db->query("SELECT * FROM clinic_investigation_price  WHERE
	// 	  investigation_id='".$table[$i]->investigation_id."'
	//    AND clinic_id='".$clinic_id."'")->result();
			
		
		// $data['table']=$this->db->query("SELECT * FROM clinic_investigation_price t1 JOIN 
		// ( select m.* from patient_investigation s, patient_investigation_line_items m 
		// where s.patient_investigation_id = m.patient_investigation_id and s.umr_no='".$finalumr."') 
		// t2 ON t1.investigation_id = t2.investigation_id")->result();
			

		// $pri=$this->db->query("SELECT * FROM patient_investigation,patient_investigation_line_items WHERE patient_investigation.patient_investigation_id=patient_investigation_line_items.patient_investigation_id AND patient_investigation.umr_no='".$finalumr."'")->result();
		// $data['priary']=$this->db->query("SELECT * FROM patient_investigation,patient_investigation_line_items,clinic_investigation_price WHERE patient_investigation.patient_investigation_id= patient_investigation_line_items.patient_investigation_id AND patient_investigation.umr_no='".$finalumr."' AND patient_investigation_line_items.investigation_id=clinic_investigation_price.investigation_id")->result();
		// $data['ary']=$ary->investigation_id;
		// $this->db->where('PIL.patient_investigation_id =',$pid);
		// $data['id']=$pri->investigation_id;
		// $invid=$pri->investigation_id;
		// $price=$this->db->query("SELECT clinic_investigation_id FROM `clinic_investigations` WHERE clinic_id='.$clinic_id.' AND investigation_id='.$invid.'")->result();
		// $data['p']=$this->db->query("SELECT clinic_investigation_id FROM `clinic_investigations` WHERE clinic_id='.$clinic_id.' AND investigation_id='.$invid.'")->get()->row();
		// $data['code']=$this->db->query("SELECT * FROM investigations WHERE investigations.investigation_id='".$invid."'")->row();
		// $data['priceinv']=$this->db->query("SELECT * FROM `clinic_investigation_price` WHERE clinic_investigation_id='".$invid."'")->row();
		// $this->db->where('CI.clinic_id =',$clinic_id);
		// $data['s'] = $this->db->get()->result();
		// echo $this->db->last_query();
		// echo "<pre>";print_r($table);echo "</pre>";
		// exit;
		$data['view'] = 'lab/view_prescription';
		$this->load->view('layout', $data);

	}

	/*
	Function departments 
	Retrieves all the lab departments available
	*/
	public function departments(){

		$data['departments'] = $this->db->select('lab_department_id, department_name')->from('lab_departments')->get()->result_array();

		$data['view'] = 'lab/departments';
		$this->load->view('layout', $data);	

	}

	/*
	Function department_investigations
	Retrieves all the investigations w.r.to the departments selected while setting up the lab investigations
	*/
	public function department_investigations(){
		// Get Departments Information 
		$departments = $_POST['lab_department'];
		// echo  $_POST;
		// exit();

		// echo  $departments[0];
		// // echo $value;
		//  exit();

		$i = 0;

		foreach($departments as $key => $value){
			$data['department_investigations'][$i]['lab_department_id'] = $key;
			$data['department_investigations'][$i]['department_name'] = $value;

		

		 	// Get Investigations with in the Departments
			$investigations = $this->db->select('investigation_id, investigation')
			->from('investigations')->where('lab_department_id =',$key)->get()->result_array();

			//  print_r($investigations);

			$data['department_investigations'][$i]['investigations'] = $investigations;

			$i++;
		}

		$data['view'] = 'lab/department_investigations';
		$this->load->view('layout', $data);	
	}


	/*
	Function saves all the investigations to the clinic
	*/
	public function add_investigations(){

		$clinic_id = $this->session->userdata('clinic_id');

		// Get modified by, created by, created date time, modified date time
		$dates = get_CM_by_dates(); 

		$investigations = $_POST['investigation'];

		// echo '<pre>';
		// print_r($_POST);
		// echo '</pre>';
		// exit();
		
		foreach($investigations as $key => $value){

			/* 
			Info ::
			$key as $value ... array key is storing value, which is an investigation_id
			$key = investigation id
			*/

			$investigation['clinic_id'] = $clinic_id;
			$investigation['investigation_id'] = $key;

			$investigationInfo = $this->db->select('investigation_id, 
			lab_department_id, package, other_information')
			->from('investigations')->where('investigation_id =',$key)->get()->result_array();

			// echo '<pre>';
			// print_r($investigationInfo);
			// echo '</pre>';
			// exit();

			$investigation = array_merge($investigation, $investigationInfo[0], $dates);

			// echo '<pre>';
			// echo 'In add investigation: <br>';
			// print_r($investigation);
			// echo '</pre>';
			// exit();

			
		$lab_template_id = $this->Generic_model->getFieldValue('lab_templates','lab_template_id',array('investigation_id'=>$investigation['investigation_id']));
		$clinic_lab_template = $this->db->query("select * from clinic_lab_templates where lab_template_id='".$lab_template_id."' and investigation_id='".$investigation['investigation_id']."' and clinic_id='".$clinic_id."'")->row();
		$template_type= $this->db->query("select * from investigations where investigation_id='".$investigation['investigation_id']."'")->row();
	

		if(count($clinic_lab_template) == '0')
		{
			$inv_details = $this->db->query("select * from investigations where investigation_id='".$investigation['investigation_id']."'")->row();
			// $inv_details = $this->db->query("select * from investigations where investigation_id='".$investigation_id."'")->row();

			$clinic_lab_templates_details['clinic_id'] = $clinic_id;
			$clinic_lab_templates_details['lab_template_id'] = $lab_template_id;
			$clinic_lab_templates_details['investigation_id'] = $investigation['investigation_id'];
			$clinic_lab_templates_details['lab_department_id'] = $inv_details->lab_department_id;
			$clinic_lab_templates_details['template_name'] = $inv_details->investigation;
			$clinic_lab_templates_details['template_type'] = $inv_details->template_type;
			$clinic_details = array_merge($clinic_lab_templates_details, $dates);
			$clinic_lab_template_id = $this->Generic_model->insertDataReturnId('clinic_lab_templates',$clinic_details);
		}

		$packageInvestigations = $this->db->select('investigation_id')->from('lab_template_line_items')->where('lab_template_id =',$lab_template_id)->get()->result_array();
//   echo $packageInvestigations;
//   exit();
		if(count($packageInvestigations)>0){
			$i=1;
		foreach($packageInvestigations as $invRecord){
			$clinic_lab_template_line_items = $this->db->query("select * from clinic_lab_template_line_items where clinic_lab_template_id='".$clinic_lab_template_id."' and investigation_id='".$investigation['investigation_id']."' and clinic_id='".$clinic_id."'")->row();
		
			if(count($clinic_lab_template_line_items) == '0')
			{
				$inv_details = $this->db->query("select * from investigations where investigation_id='".$invRecord['investigation_id']."'")->row();
				
				$id = $this->db->query("select * from clinic_lab_templates where investigation_id='".$investigation['investigation_id']."' and clinic_id='".$clinic_id."' and lab_template_id='".$lab_template_id."'")->row();
				
				$clinic_lab_template_line_item_details['clinic_id'] = $clinic_id;
				$clinic_lab_template_line_item_details['clinic_lab_template_id'] = $id->clinic_lab_template_id;
				$clinic_lab_template_line_item_details['parent_investigation_id'] = $investigation['investigation_id'];
				$clinic_lab_template_line_item_details['investigation_id'] = $invRecord['investigation_id'];
				$clinic_lab_template_line_item_details['parameter'] = $inv_details->investigation;
				$clinic_lab_template_line_item_details['position'] = $i;
				
				$clinic_lab_item_details = array_merge($clinic_lab_template_line_item_details, $dates);

				$clinic_lab_template_line_item_id = $this->Generic_model->insertDataReturnId('clinic_lab_template_line_items',$clinic_lab_item_details);
		
			}	
	
			$i++;
	}
		}	
			else{
				$inv_detailss = $this->db->query("select * from investigations where investigation_id='".$investigation['investigation_id']."'")->row();
				
				$idd = $this->db->query("select * from clinic_lab_templates where investigation_id='".$investigation['investigation_id']."' and clinic_id='".$clinic_id."' and lab_template_id='".$lab_template_id."'")->row();
				
				$clinic_lab_template_line_item_detailss['clinic_id'] = $clinic_id;
				$clinic_lab_template_line_item_detailss['clinic_lab_template_id'] = $idd->clinic_lab_template_id;
				$clinic_lab_template_line_item_detailss['parent_investigation_id'] = $investigation['investigation_id'];
				$clinic_lab_template_line_item_detailss['investigation_id'] = $investigation['investigation_id'];
				$clinic_lab_template_line_item_detailss['parameter'] = $inv_details->investigation;
				$clinic_lab_template_line_item_detailss['position'] = '1';
				
				$clinic_lab_item_detailss = array_merge($clinic_lab_template_line_item_detailss, $dates);

				$clinic_lab_template_line_item_idd = $this->Generic_model->insertDataReturnId('clinic_lab_template_line_items',$clinic_lab_item_detailss);
		
			}

			$this->createInvestigaton($investigation);	

		}
// exit();
		// Rewrite Methods & Units JSON file & Update in master version
		// Params : master_table_name, field_name, master_name, clinic_id
		update_master_version('clinic_investigation_range','units','units',0);
		update_master_version('clinic_investigation_range','method','method',0);

		// Update the master version for Clinic Investigations and also with the new added investigations
		update_clinic_investigation_master_version($clinic_id);

		redirect("Lab/investigations");

	}

	/**
	Function createInvestigation and getPackage details are 
	Recursive functions and transactions do happen in between both functions while an investigation is created and if the type is a package
	An investigation Package may again contain package within... So these functions loop accordingly and do implement...
	* @author Uday Kanth Rapalli 
	*/
	public function createInvestigaton($investigation)
	{

		$clinic_id = $this->session->userdata('clinic_id');

		// echo 'Entered create Investigation<br>';

		echo '<pre>';
		echo '1';
		print_r($investigation);
		echo '</pre>';
		// exit();
		
		// extract($investigation);

		// Get modified by, created by, created date time, modified date time
		$dates = get_CM_by_dates(); 

		// Avoid duplicate entry
		// Check if the investigation is already exist in the clinic investigation DB
		$clinic_investigation_id = $this->Generic_model
		->getFieldValue('clinic_investigations','clinic_investigation_id',array('clinic_id'=>$investigation['clinic_id'],'investigation_id'=>$investigation['investigation_id']));
		
		// echo '<pre>';
		// print_r($clinic_investigation_id);
		// echo '</pre>';
		
		
		if($clinic_investigation_id == 0){
			// Insert the investigation into clinic investigation
			$clinic_investigation_id = $this->Generic_model->insertDataReturnId('clinic_investigations',$investigation);
		}
		
		// Get the package info of this investigation
		// $package = $this->Generic_model->getFieldValue('investigations','package',array('investigation_id'=>$investigation['investigation_id']));
		$package = $investigation['package'];

		// echo "Package: ".$package."<br>";
		// exit();

		// For General 
		if($package == 0){

		$lab_template_id = $this->Generic_model->getFieldValue('lab_templates','lab_template_id',array('investigation_id'=>$investigation['investigation_id']));
		$clinic_lab_template = $this->db->query("select * from clinic_lab_templates where lab_template_id='".$lab_template_id."' and investigation_id='".$investigation['investigation_id']."' and clinic_id='".$clinic_id."'")->row();
		$template_type= $this->db->query("select * from investigations where investigation_id='".$investigation['investigation_id']."'")->row();
	
		// echo '<pre>';
		// print_r($clinic_lab_template);
		// echo '</pre>';
		// exit();
// if($clinic_lab_template->template_type == 'General'){
		// if(count($clinic_lab_template) == '0')
		// {
		// 	$inv_details = $this->db->query("select * from investigations where investigation_id='".$investigation['investigation_id']."'")->row();
		// 	// $inv_details = $this->db->query("select * from investigations where investigation_id='".$investigation_id."'")->row();

		// 	$clinic_lab_templates_details['clinic_id'] = $clinic_id;
		// 	$clinic_lab_templates_details['lab_template_id'] = $lab_template_id;
		// 	$clinic_lab_templates_details['investigation_id'] = $investigation['investigation_id'];
		// 	$clinic_lab_templates_details['lab_department_id'] = $inv_details->lab_department_id;
		// 	$clinic_lab_templates_details['template_name'] = $inv_details->investigation;
		// 	$clinic_lab_templates_details['template_type'] = $inv_details->template_type;
		// 	$clinic_details = array_merge($clinic_lab_templates_details, $dates);
		// 	$clinic_lab_template_id = $this->Generic_model->insertDataReturnId('clinic_lab_templates',$clinic_details);
		// }
// 	}
		$packageInvestigations = $this->db->select('investigation_id')->from('lab_template_line_items')->where('lab_template_id =',$lab_template_id)->get()->result_array();
		// echo '<pre>';
		// print_r($packageInvestigations);
		// print_r($investigation['investigation_id']);
		// print_r($clinic_lab_template_id);
		// print_r($lab_template_id);
		// echo '</pre>';
		// exit();
		$i=1;
		foreach($packageInvestigations as $invRecord){
			// if(count($clinic_lab_template_line_items) == '0')
			// {
			// 	$inv_details = $this->db->query("select * from investigations where investigation_id='".$investigation['investigation_id']."'")->row();
				
			// 	$id = $this->db->query("select * from clinic_lab_templates where investigation_id='".$investigation['investigation_id']."' and clinic_id='".$clinic_id."' and lab_template_id='".$lab_template_id."'")->row();
				
			// 	$clinic_lab_template_line_item_details['clinic_id'] = $clinic_id;
			// 	$clinic_lab_template_line_item_details['clinic_lab_template_id'] = $id->clinic_lab_template_id;
			// 	$clinic_lab_template_line_item_details['parent_investigation_id'] = $investigation['investigation_id'];
			// 	$clinic_lab_template_line_item_details['investigation_id'] = $invRecord['investigation_id'];
			// 	$clinic_lab_template_line_item_details['parameter'] = $inv_details->investigation;
			// 	$clinic_lab_template_line_item_details['position'] = $i;
				
			// 	$clinic_lab_item_details = array_merge($clinic_lab_template_line_item_details, $dates);

			// 	$clinic_lab_template_line_item_id = $this->Generic_model->insertDataReturnId('clinic_lab_template_line_items',$clinic_lab_item_details);
			// }	
	}
			// echo "1...<br>";

			// Get the investigation range for clinic investigation range DB with investigation id
			$rangeInfo = $this->db->select('investigation_id, sample_type, condition, method, 
			low_range, high_range, units, remarks, result_condition')
			->from('investigation_range')
			->where('investigation_id =',$investigation['investigation_id'])->get()->result_array();
			
			echo '<pre>';
			// echo $this->db->last_query();
			echo 'rangeInfo';
			print_r($rangeInfo);
			echo '</pre>';
			// exit();

			// Get distinct sample types for pricing
			$sampleTypes = $this->db->select('sample_type')
			->distinct()->from('investigation_range')
			->where('investigation_id =',$investigation['investigation_id'])
			->get()->result_array();

					echo '<pre>';
					echo 'sampleTypes';
					print_r($sampleTypes);
					echo '</pre>';
					// exit();

			if(count($rangeInfo) > 0){

				foreach ($rangeInfo as $rangeRec) {

					//Extract $rangeRec array to variable
					extract($rangeRec);
					// echo '<pre>';
					// echo 'rangeRec';
					// print_r($rangeRec);
					// echo '</pre>';

					$rangeRec['clinic_investigation_id'] = $clinic_investigation_id;

					// Check if the record already exists in the clinic investigation range DB w.r.to clinic_investigation_id, investigation_id, sample_type, gender, patient_condition
					$rangeRecCount = $this->Generic_model->getNumberOfRecords('clinic_investigation_range',array('clinic_investigation_id'=>$clinic_investigation_id, 'investigation_id'=>$investigation['investigation_id'], 'condition'=>$condition, 'sample_type' => $sample_type));
					// $rangeRecCount = $this->Generic_model->getNumberOfRecords('clinic_investigation_range',array('clinic_investigation_id'=>$clinic_investigation_id, 'investigation_id'=>$investigation['investigation_id']));

					echo '<pre>';
					echo 'rangeRecCount';
					print_r($rangeRecCount);
					echo '</pre>';
					// exit();
					if($rangeRecCount == 0){
						// Create clinic investigation range record
						// Merge arrays
						$clinic_investigation_range = array_merge($rangeRec, $dates);
						// echo '<pre>';
						// echo 'clinic_investigation_range';
						// print_r($clinic_investigation_range);
						// echo '</pre>';
						// exit();
						// Insert investigation range record
						$res = $this->Generic_model->insertData('clinic_investigation_range',$clinic_investigation_range);

					}					
				}

				foreach($sampleTypes as $sample){
					// $ckeckPricingRecord = $this->Generic_model->getNumberOfRecords('clinic_investigation_price',array('clinic_investigation_id' => $clinic_investigation_id, 'investigation_id' => $investigation['investigation_id']));
					// Clinic investigation pricing information
					$ckeckPricingRecord = $this->Generic_model->getNumberOfRecords('clinic_investigation_price',array('clinic_investigation_id' => $clinic_investigation_id, 'investigation_id' => $investigation['investigation_id'], 'sample_type' => $sample['sample_type']));
					// echo '<pre>';
					// 	print_r($ckeckPricingRecord);
					// 	echo '</pre>';
					// echo "No. of records: ".$checkPricingRecord."<br>";
					$check = $this->db->select('*')
					->from('clinic_investigation_price')
					->where('clinic_investigation_id= ',$clinic_investigation_id)
					->where('investigation_id= ', $investigation['investigation_id'])
					->where('sample_type= ',  $sample['sample_type'])
					->get()->result_array();
					// echo '<pre>';
					// 		print_r(count($check));
					// 		echo '</pre>';

					// if($checkPricingRecord == 0){
						if(count($check) > 0){
						
						
					}	
					else{
						// Create new pricing record
						$pricingData['clinic_id'] = $clinic_id;
						$pricingData['clinic_investigation_id'] = $clinic_investigation_id;
						$pricingData['investigation_id'] = $investigation['investigation_id'];
						$pricingData['sample_type'] = $sample['sample_type'];
						$pricingData = array_merge($pricingData, $dates);
						// Insert data into clinic_investigation_pricing						
						$priceRes = $this->Generic_model->insertData('clinic_investigation_price',$pricingData);
					}
				}
			}
		}
		else if($package == 1){

			// Create a record for clinic investigation price for this GOI/Package
			$packagePriceData['clinic_id'] = $clinic_id;
			$packagePriceData['clinic_investigation_id'] = $clinic_investigation_id;
			$packagePriceData['investigation_id'] = $investigation['investigation_id'];
			
			$packagePriceData = array_merge($packagePriceData, $dates);
			// echo '<pre>';
			// print_r($packagePriceData);
			// echo 'entered 1';
			// echo '</pre>';
			$check = $this->db->select('*')
			->from('clinic_investigation_price')
			->where('clinic_investigation_id= ',$clinic_investigation_id)
			->where('investigation_id= ', $investigation['investigation_id'])
			// ->where('sample_type= ',  $sample['sample_type'])
			->get()->result_array();
			if(count($check) > 0){
									
									
			}
			else{
				$clinic_investigation_price_id = $this->Generic_model->insertDataReturnId('clinic_investigation_price', $packagePriceData);
			}	
		

			// If package 1 means, the investigation has list of investigations/parameters inside in it
			$this->getPackageInfo($investigation['investigation_id']);			
		}

	}


	public function getPackageInfo($investigation_id){
		echo $investigation_id;
		// exit();
		$clinic_id = $this->session->userdata('clinic_id');
		// Get the lab_template id for the investigation_id
		$lab_template_id = $this->Generic_model->getFieldValue('lab_templates','lab_template_id',array('investigation_id'=>$investigation_id));
	
		echo '<pre>';
		print_r($lab_template_id);
		echo "entered 1";
		echo '</pre>';
		// exit();

		$dates = get_CM_by_dates();
		$clinic_lab_template = $this->db->query("select * from clinic_lab_templates where lab_template_id='".$lab_template_id."' and investigation_id='".$investigation_id."' and clinic_id='".$clinic_id."'")->row();
		if(count($clinic_lab_template) == '0')
		{
			$inv_details = $this->db->query("select * from investigations where investigation_id='".$investigation_id."'")->row();
			// $inv_details = $this->db->query("select * from investigations where investigation_id='".$investigation_id."'")->row();

			$clinic_lab_templates_details['clinic_id'] = $clinic_id;
			$clinic_lab_templates_details['lab_template_id'] = $lab_template_id;
			$clinic_lab_templates_details['investigation_id'] = $investigation_id;
			$clinic_lab_templates_details['lab_department_id'] = $inv_details->lab_department_id;
			$clinic_lab_templates_details['template_name'] = $inv_details->investigation;
			$clinic_lab_templates_details['template_type'] = $inv_details->template_type;
			$clinic_details = array_merge($clinic_lab_templates_details, $dates);
			// $clinic_lab_template_id = $this->Generic_model->insertDataReturnId('clinic_lab_templates',$clinic_details);
		}
		// Get the list of investigations belongs to lab_template_id
		$packageInvestigations = $this->db->select('investigation_id')->from('lab_template_line_items')->where('lab_template_id =',$lab_template_id)->get()->result_array();

		echo '<pre>';
		echo "entered 2";
		print_r($packageInvestigations);
		echo "entered 2";
		echo '</pre>';
		// exit();
	
		$i=1;
		foreach($packageInvestigations as $invRecord){
			$clinic_lab_template_line_items = $this->db->query("select * from clinic_lab_template_line_items where clinic_lab_template_id='".$clinic_lab_template_id."' and investigation_id='".$invRecord['investigation_id']."' and clinic_id='".$clinic_id."'")->row();
			
			if(count($clinic_lab_template_line_items) == '0')
			{
				$inv_details = $this->db->query("select * from investigations where investigation_id='".$invRecord['investigation_id']."'")->row();
				
				$id = $this->db->query("select * from clinic_lab_templates where investigation_id='".$investigation_id."' and clinic_id='".$clinic_id."' and lab_template_id='".$lab_template_id."'")->row();
				

				$clinic_lab_template_line_item_details['clinic_id'] = $clinic_id;
				$clinic_lab_template_line_item_details['clinic_lab_template_id'] = $id->clinic_lab_template_id;
				$clinic_lab_template_line_item_details['parent_investigation_id'] = $investigation_id;
				$clinic_lab_template_line_item_details['investigation_id'] = $invRecord['investigation_id'];
				$clinic_lab_template_line_item_details['parameter'] = $inv_details->investigation;
				$clinic_lab_template_line_item_details['position'] = $i;
				
				$clinic_lab_item_details = array_merge($clinic_lab_template_line_item_details, $dates);

				// $clinic_lab_template_id = $this->Generic_model->insertDataReturnId('clinic_lab_template_line_items',$clinic_lab_item_details);
			}
			// echo '<pre>';
			// print_r($clinic_lab_template_id);
			// echo '</pre>';
			

			$investigation = $this->db->select('investigation_id, lab_department_id, package, other_information')
			->from('investigations')
			->where('investigation_id =',$invRecord['investigation_id'])->get()->result_array();

			$data['clinic_id'] = $this->session->userdata('clinic_id');

			$investigation = array_merge($data, $investigation[0], $dates);

			// echo '<pre>';
			// echo 'In Packages: <br>';
			// print_r($investigation);
			// echo "entered 3";
			// echo '</pre>';
		
			$i++;
			$this->createInvestigaton($investigation);
			// exit();
		}	
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


	public function delete_lab_investigation($clt_id,$inv){

		$clinic_id = $this->session->userdata('clinic_id');
		
		$range=$this->db->query("SELECT clinic_lab_template_line_items.investigation_id,clinic_lab_template_line_items.parent_investigation_id FROM clinic_lab_template_line_items WHERE clinic_lab_template_id='".$clt_id."' AND clinic_id='".$clinic_id."'")->result();

		foreach($range as $r){

			if($r->investigation_id!=$r->parent_investigation_id){

				 $clinic_inv=$this->db->query("select * from clinic_investigations where investigation_id='".$r->investigation_id."' and clinic_id='".$clinic_id."'")->result();
				 
						foreach($clinic_inv as $cli_inv){

							$this->db->query("delete from clinic_investigation_range where clinic_investigation_id='".$cli_inv->clinic_investigation_id."'");
							$this->db->query("delete from clinic_investigations where clinic_investigation_id='".$cli_inv->clinic_investigation_id."'");
						}


				}
		}

		$this->db->query("delete from clinic_investigations where investigation_id='".$inv."' and clinic_id='".$clinic_id."'");
		$this->db->query("delete from clinic_investigation_price where investigation_id='".$inv."' and clinic_id='".$clinic_id."'");
		$this->db->query("delete from clinic_investigation_range where clinic_investigation_id='".$clt_id."'");
		$this->db->query("DELETE FROM clinic_lab_template_line_items WHERE clinic_lab_template_id='".$clt_id."'");
		$this->db->query("delete from clinic_lab_templates where clinic_lab_template_id='".$clt_id."'");
		
	

		$this->db->query("DELETE FROM clinic_lab_template_line_items WHERE clinic_lab_template_id='".$id."'");

		$this->db->query("delete from clinic_lab_templates where clinic_lab_template_id='".$id."'");

		redirect("Lab/investigations");
	}


	public function investigations()
	{

		$clinic_id = $this->session->userdata('clinic_id');

		update_clinic_investigation_master_version($clinic_id);

		if($_POST['submit']){

			extract($_POST);

			echo '<pre>';
			print_r($_POST);
			echo '</pre>';
			// exit();
			// $this->Generic_model->updateData('clinic_lab_template_line_items',$rec,array('clinic_lab_template_id'=>$clinic_lab_template_id,'clinic_id'=>$clinic_id,'parent_investigation_id'=>$_POST['patient_lab_reports']['investigation_id'],'investigation_id'=>$line_item['investigation_id']));
			// Update price details of the clinic Investigation if it is not equal to '0'
			echo "one";
			// exit();
			if($_POST['price'] > 0){
				$res = $this->Generic_model->updateData('clinic_investigation_price',
				array('price' => $_POST['price']),array('clinic_id'=>$clinic_id,'investigation_id' => $_POST['investigation_id']));	
				// $res = $this->Generic_model->updateData('clinic_investigation_price',
				// array('price' => $_POST['price']),array('clinic_investigation_price_id' => $_POST['clinic_investigation_price_id']));	
				// echo "two";
				// echo $this->db->last_query()."<br>";
				// exit();
			}
			
			foreach($_POST['clinic_investigation'] as $clinicInvestigationRec){
				$clinic_investigation_id = $clinicInvestigationRec['clinic_investigation_id'];
				unset($clinicInvestigationRec['clinic_investigation_id']);
				$this->Generic_model->updateData('clinic_investigations',$clinicInvestigationRec,array('clinic_investigation_id' => $clinic_investigation_id));
				// echo $this->db->last_query()."<br>";
				// exit();
			}

			foreach($_POST['range'] as $rangeRec){	

				$clinic_investigation_range_id = $rangeRec['clinic_investigation_range_id'];
				unset($rangeRec['clinic_investigation_range_id']);
				$this->Generic_model->updateData('clinic_investigation_range',$rangeRec,array('clinic_investigation_range_id' => $clinic_investigation_range_id));
				// echo $this->db->last_query()."<br>";

				
				// $clinic_investigation_range_data = $this->db->select('*')
				// ->from('clinic_investigation_range')
				// ->where('clinic_investigation_range_id =',$clinic_investigation_range_id)
				// ->get()->row();
				
				
				// $data['other_information'] = $rangeRec['remarks'];
				// $this->Generic_model->updateData('clinic_investigations',$data,array('clinic_investigation_id' => $clinic_investigation_range_data->clinic_investigation_id));
				// echo $this->db->last_query()."<br>";

			
			}

			redirect("Lab/investigations");
			
		}
		// print_r($_POST['range']);
		// $investigations = $this->db->select('CI.clinic_investigation_id, CI.investigation_id,
		//  I.investigation,I.item_code, I.package, I.template_type')
		// ->from('clinic_investigations CI')
		// ->join('investigations I','CI.investigation_id = I.investigation_id')
		// ->where('CI.clinic_id =',$clinic_id)->get()->result_array();

		$investigations = $this->db->select('*')
	   ->from('clinic_lab_templates CI')
	   ->join('investigations I','CI.investigation_id = I.investigation_id')
	   ->where('CI.clinic_id =',$clinic_id)->get()->result_array();
		// echo $this->db->last_query().";<br>";  
		// exit();
		// check investigations query

		$i = 0;
		foreach($investigations as $inv){

			// Get Distinct Sample Type & Price
			$samples = $this->db->distinct()->select('clinic_investigation_price_id, sample_type, price')
			->from('clinic_investigation_price')
			->where('clinic_investigation_id =',$inv['clinic_investigation_id'])
			// where clinic_inv_id=inv_id sandy writing;
			->where('clinic_id =',$clinic_id)->get()->result_array();
			// echo $this->db->last_query().";<br>";  
			// exit();
			if(count($samples) > 0){
				 $j = $i;
				// $j=0;
				foreach($samples as $sample){
					if($sample['price'] == 0){
						$data['newInvestigations'][$j] = array_merge($inv, $sample);
						$data['newInvestigations'][$j]['conditions'] = $this->db->select('clinic_investigation_range_id, method, condition, 
						low_range, high_range, units')
						->from('clinic_investigation_range')->where('clinic_investigation_id =',$inv['clinic_investigation_id'])
							// where clinic_inv_id=inv_id sandy writing;
						->where('sample_type =', $sample['sample_type'])
						// ->where('clinic_investigation_id=1084')
						->get()->result_array();	
						// echo "<br>IN - ".$this->db->last_query().";<br>";
					}else{
						$data['investigations'][$j] = array_merge($inv, $sample);
						$data['investigations'][$j]['conditions'] = $this->db->
						select('clinic_investigation_range_id, method, condition, low_range, high_range, units')
						->from('clinic_investigation_range')->where('clinic_investigation_id =',$inv['clinic_investigation_id'])
							// where clinic_inv_id=inv_id sandy writing;
						->where('sample_type =', $sample['sample_type'])->get()->result_array();	
						// echo "<br>OUT - ".$this->db->last_query().";<br>";
					}
					
					$j++;
				}
				$i = (int)$j - 1; // While instantiating the value to $i, decreasing '$j' by 1 as '$j' increaments by 1 before coming out of the loop and $i increamets again at end of the master loop
			}
			else{
				$priceInfo = $this->db->select('sample_type, price')
				->from('clinic_investigation_price')
				->where('investigation_id =',$inv['investigation_id'])
				->where('clinic_id =',$clinic_id)->get()->row();
				// 5000
				if($priceInfo->price == 0){
					$data['newInvestigations'][$i] = array_merge($inv, (array)$priceInfo);	
				
				}else{
					$data['investigations'][$i] = array_merge($inv, (array)$priceInfo);	
				
				}				
			}
			$i++;
		}
		// echo "<pre>";print_r($data['investigations']); echo "</pre>";
		// exit();
		$tdate = date('Y-m-d');		
		
		
		$data['view'] = 'lab/investigations';
		$this->load->view('layout', $data);
	}


	/*
	Function view_package_info retrieves all th eloist of params/investigations included in that investigation
	*/
	public function view_package_info(){

		$clinic_id = $this->session->userdata('clinic_id');

		extract($_POST);

		// echo '<pre>';
		// print_r($_POST);
		// echo '</pre>';
		// exit();

		// Get Lab Template ID, Template Type w.r.to investigation_id
		// $templateInfo = $this->db->select('LT.lab_template_id, LT.template_type, LT.template_name, LT.investigation_id, I.package')->from('lab_templates LT')->join('investigations I','LT.investigation_id = I.investigation_id','inner')->where('LT.investigation_id =',$investigation_id)->get()->result_array();
		
		$templateInfo = $this->db->select('LT.clinic_lab_template_id,LT.lab_template_id, LT.template_type, LT.template_name, LT.investigation_id, I.package')->from('clinic_lab_templates LT')->join('investigations I','LT.investigation_id = I.investigation_id','inner')->where('LT.investigation_id =',$investigation_id)->where('LT.clinic_id =',$clinic_id)->get()->result_array();
		// echo $this->db->last_query()."<br>";
		// echo $templateInfo."<br>";
		// echo $templateInfo->clinic_lab_template_id."<br>";
		// echo $templateInfo[0]->clinic_lab_template_id."<br>";
		// exit();
		// echo $this->db->last_query()."<br>";
		// echo count($templateInfo)."<br>";
		// exit();
		// $templateInfo = $this->db->select('LT.clinic_lab_template_id, LT.template_type, LT.template_name, LT.investigation_id, I.package')->from('clinic_lab_templates LT')->join('investigations I','LT.investigation_id = I.investigation_id','inner')->where('LT.investigation_id =',$investigation_id)->get()->result_array();

		// Get Lab template Line items for the lab template
			// $lineItemsInfo = $this->db->select('LI.lab_template_line_item_id, LI.lab_template_id, 
			// LI.parent_investigation_id, LI.investigation_id, LI.parameter,
			// CI.clinic_investigation_id, CI.clinic_id, CI.price, CI.other_information, 
			// CIR.clinic_investigation_range_id, CIR.method, CIR.condition, CIR.low_range,
			// CIR.high_range, CIR.units, CIR.remarks')->from('lab_template_line_items LI')
			// ->join('clinic_investigations CI','LI.investigation_id = CI.investigation_id','left')
			// ->join('clinic_investigation_range CIR','CI.clinic_investigation_id = CIR.clinic_investigation_id','inner')
			// ->where('CI.clinic_id =',$clinic_id)
			// ->where('LI.lab_template_id =',$templateInfo[0]['lab_template_id'])
			// ->get()->result_array();

			// echo $this->db->last_query()."<br>";
			// exit();
			// echo $this->db->last_query()."<br>";
	

			// echo $this->db->last_query()."<br>";
		// $lineItemsInfo = $this->db->select('LI.clinic_lab_template_line_item_id, LI.clinic_lab_template_id,
		//  LI.parent_investigation_id, LI.investigation_id, LI.parameter, CI.clinic_investigation_id, CI.clinic_id, CI.price, CI.other_information, CIR.clinic_investigation_range_id, 
		// CIR.method, CIR.condition, CIR.low_range, CIR.high_range, CIR.units, CIR.remarks')
		// ->from(' clinic_lab_template_line_items LI')
		// ->join('clinic_investigations CI','LI.investigation_id = CI.investigation_id','left')
		// ->join('clinic_investigation_range CIR','CI.clinic_investigation_id = CIR.clinic_investigation_id','inner')
		// ->where('CI.clinic_id =',$clinic_id)
		// ->where('LI.clinic_lab_template_id =',$templateInfo[0]['clinic_lab_template_id'])
		// ->get()->result_array();

		// Get Distinct methods list
		// $info['methods'] = $this->db->select('method')->distinct()->from('clinic_investigation_range')->where('method !=','')->where('method !=',NULL)->get()->result_array();

		// // Get Distinct units list
		// $info['units'] = $this->db->select('units')->distinct()->from('clinic_investigation_range')->where('units !=','')->where('units !=',NULL)->get()->result_array();

		// $info['templateInfo'] = $templateInfo;
		// $info['package'] = $templateInfo[0];
		// $info['packageItems'] = $lineItemsInfo;

		// if(count($templateInfo) > 0)
		// {
		// 	$lineItemsInfo = $this->db->select('LI.lab_template_line_item_id, LI.lab_template_id, 
		// 	LI.parent_investigation_id, LI.investigation_id, LI.parameter,
		// 	CI.clinic_investigation_id, CI.clinic_id, CI.price, CI.other_information, 
		// 	CIR.clinic_investigation_range_id, CIR.method, CIR.condition, CIR.low_range,
		// 	CIR.high_range, CIR.units, CIR.remarks')->from('lab_template_line_items LI')
		// 	->join('clinic_investigations CI','LI.investigation_id = CI.investigation_id','left')
		// 	->join('clinic_investigation_range CIR','CI.clinic_investigation_id = CIR.clinic_investigation_id','inner')
		// 	->where('CI.clinic_id =',$clinic_id)
		// 	->where('LI.lab_template_id =',$templateInfo[0]['lab_template_id'])
		// 	->get()->result_array();
		// 	// echo $this->db->last_query()."<br>";
		// 	// exit();
		// 	$info['methods'] = $this->db->select('method')->distinct()->from('clinic_investigation_range')->where('method !=','')->where('method !=',NULL)->get()->result_array();

		// 	// Get Distinct units list
		// 	$info['units'] = $this->db->select('units')->distinct()->from('clinic_investigation_range')->where('units !=','')->where('units !=',NULL)->get()->result_array();
	
		// 	$info['templateInfo'] = $templateInfo;
		// 	$info['package'] = $templateInfo[0];
		// 	$info['packageItems'] = $lineItemsInfo;
		// 	$investigationJSON = json_encode($info);
		// 	echo $investigationJSON;
		// }
		// else{
			// $templateInfo = $this->db->select('LT.clinic_lab_template_id, LT.template_type, LT.template_name, LT.investigation_id, I.package')->from('clinic_lab_templates LT')->join('investigations I','LT.investigation_id = I.investigation_id','inner')->where('LT.investigation_id =',$investigation_id)->get()->result_array();
			// $lineItemsInfo = $this->db->select('LI.clinic_lab_template_line_item_id, LI.clinic_lab_template_id,
			// LI.parent_investigation_id, LI.investigation_id, CI.clinic_investigation_id,LI.parameter, CI.clinic_investigation_id, CI.clinic_id, CI.price, CI.other_information, CIR.clinic_investigation_range_id, 
			// CIR.method, CIR.condition, CIR.low_range, CIR.high_range, CIR.units, CIR.remarks')
			// ->from('clinic_lab_template_line_items LI')
			// ->join('clinic_investigations CI','LI.investigation_id = CI.investigation_id','left')
			// ->join('clinic_investigation_range CIR','CI.clinic_investigation_id = CIR.clinic_investigation_id','inner')
			// ->where('CI.clinic_id =',$clinic_id)
			// ->where('LI.parent_investigation_id =',$investigation_id)
			// ->where('LI.clinic_lab_template_id =',$templateInfo[0]['clinic_lab_template_id'])
			// ->order_by('LI.position','ASC')
			// ->get()->result_array();
			$lineItemsInfo = $this->db->select('LI.clinic_lab_template_line_item_id, LI.clinic_lab_template_id,
			LI.parent_investigation_id, LI.investigation_id, CI.clinic_investigation_id,LI.parameter, CI.clinic_investigation_id, CI.clinic_id, CI.price, CI.other_information, CIR.clinic_investigation_range_id, 
			CIR.method, CIR.condition, CIR.low_range, CIR.high_range, CIR.units, CIR.remarks')
			->from('clinic_lab_template_line_items LI')
			->join('clinic_investigations CI','LI.investigation_id = CI.investigation_id','left')
			->join('clinic_investigation_range CIR','CI.clinic_investigation_id = CIR.clinic_investigation_id','left')
			->where('CI.clinic_id =',$clinic_id)
			->where('LI.parent_investigation_id =',$investigation_id)
			->where('LI.clinic_lab_template_id =',$templateInfo[0]['clinic_lab_template_id'])
			->order_by('LI.position','ASC')
			->get()->result_array();
			// echo $this->db->last_query()."<br>";
			// exit();
			// echo $lineItemsInfo->parent_investigation_id."<br>";
			// exit();

			$info['methods'] = $this->db->select('method')->distinct()->from('clinic_investigation_range')->where('method !=','')->where('method !=',NULL)->get()->result_array();

			// Get Distinct units list
			$info['units'] = $this->db->select('units')->distinct()->from('clinic_investigation_range')->where('units !=','')->where('units !=',NULL)->get()->result_array();
	
			// $i = 0;
			// foreach($lineItemsInfo as $info){
				// $inv =  $this->db->select('*')
				// ->from('investigations')
				// ->where('investigation_id =',$investigation_id)->get()->row();
				// $info['templateInfo'][$i]['lab_template_id'] = '0';
				// $info['templateInfo'][$i]['investigation_id'] = $investigation_id;
                // $info['templateInfo'][$i]['template_type'] = $inv->template_type;
				// $info['templateInfo'][$i]['template_name'] = $inv->investigation;
				// $info['templateInfo'][$i]['package'] = $inv->package;

				// $info['package']['lab_template_id'] = '0';
				// $info['package']['investigation_id'] = $investigation_id;
                // $info['package']['template_type'] = $inv->template_type;
				// $info['package']['template_name'] = $inv->investigation;
				// $info['package']['package'] = $inv->package;
                // $i++;
			// }

			// $a = 0;
			// foreach($lineItemsInfo as $info){
				// $inv =  $this->db->select('*')
				// ->from('investigations')
				// ->where('investigation_id =',$investigation_id)->get()->row();
				// $info['packageItems'][$a]['lab_template_line_item_id'] = '0';
				// $info['packageItems'][$a]['investigation_id'] = $investigation_id;
				// $info['packageItems'][$a]['lab_template_id'] =  '0';
                // $info['packageItems'][$a]['parent_investigation_id'] = $lineItemsInfo->parent_investigation_id;
				// $info['packageItems'][$a]['parameter'] = $lineItemsInfo->parameter;
				// $info['packageItems'][$a]['clinic_investigation_id'] = $lineItemsInfo->clinic_investigation_id;
				// $info['packageItems'][$a]['price'] = $lineItemsInfo->price;
				// $info['packageItems'][$a]['clinic_id'] = $lineItemsInfo->clinic_id;
				// $info['packageItems'][$a]['other_information'] = $lineItemsInfo->other_information;
				// $info['packageItems'][$a]['clinic_investigation_range_id'] = $lineItemsInfo->clinic_investigation_range_id;
				// $info['packageItems'][$a]['condition'] = $lineItemsInfo->condition;
				// $info['packageItems'][$a]['low_range'] = $lineItemsInfo->low_range;
				// $info['packageItems'][$a]['high_range'] = $lineItemsInfo->high_range;
				// $info['packageItems'][$a]['units'] = $lineItemsInfo->units;
				// $info['packageItems'][$a]['remarks'] = $lineItemsInfo->remarks;
            //     $a++;
			// }

		//	$info['templateInfo'] = $data;

			// $info['package'] = $templateInfo[0];
			// $info['packageItems'] = $lineItemsInfo;
			// var a =[{"lab_template_id":"0","template_type":"Excel","template_name":"Total Leucocyte Count (TC)","investigation_id":"1752","package":"0"}];

			// $info['package'] = '0';
			// $info['packageItems'] = '0';

			
			// "package":{"lab_template_id":"1752","template_type":"Excel",
			// 	"template_name":"Total Leucocyte Count (TC)","investigation_id":"1752","package":"0"},
			// 	"packageItems":[{"lab_template_line_item_id":"1693","lab_template_id":"1752",
			// 		"parent_investigation_id":"1752","investigation_id":"1752",
			// 		"parameter":"Total Leucocyte Count (TC)","clinic_investigation_id":"17",
			// 		"clinic_id":"4","price":null,"other_information":"",
			// 		"clinic_investigation_range_id":"16","method":"Haemocytometer",
			// 		"condition":"","low_range":"0","high_range":"0","units":"","remarks":""}]}
			$info['methods'] = $this->db->select('method')->distinct()->from('clinic_investigation_range')->where('method !=','')->where('method !=',NULL)->get()->result_array();

			// Get Distinct units list
			$info['units'] = $this->db->select('units')->distinct()->from('clinic_investigation_range')->where('units !=','')->where('units !=',NULL)->get()->result_array();
	
			$info['templateInfo'] = $templateInfo;
			$info['package'] = $templateInfo[0];
			$info['packageItems'] = $lineItemsInfo;
			$investigationJSON = json_encode($info);
	    	echo $investigationJSON;
		// }
	}


	public function view_order($billing_id)
	{

		// Get the clinic_id
		$clinic_id = $this->session->userdata('clinic_id');

		// Get Patient/Guest Information of the order/billing
		$data['billing_info'] = $this->db->select('B.billing_id, B.appointment_id, B.patient_id, B.doctor_id, B.payment_status, B.guest_name, B.guest_mobile, B.position_status, A.appointment_date, A.appointment_time_slot, Doc.first_name as doc_first_name, Doc.last_name as doc_last_name, Doc.department_id, Dep.department_name')->from('billing B')->join('appointments A','B.appointment_id = A.appointment_id','left')->join('doctors Doc','B.doctor_id = Doc.doctor_id','left')->join('department Dep','Doc.department_id = Dep.department_id','left')->where('B.billing_id =', $billing_id)->get()->row();

		$data['billing_info']->guest_mobile = DataCrypt($data['billing_info']->guest_mobile, 'decrypt');

		// Get the list od lab investigations for billing ID
		$data['billing_line_items'] = $this->db->select('InvS.investigation_status_id, InvS.billing_id, InvS.billing_line_item_id, InvS.investigation_id, InvS.report_status, InvS.report_entry_status, Inv.investigation, Inv.template_type, Inv.item_code, CI.clinic_investigation_id')->from('investigation_status InvS')->join('billing_line_items BLI','InvS.billing_line_item_id = BLI.billing_line_item_id','inner')->join('investigations Inv','InvS.investigation_id = Inv.investigation_id','inner')->join('clinic_investigations CI','InvS.investigation_id = CI.investigation_id','inner')->where('BLI.billing_id =',$billing_id)->where('CI.clinic_id =',$clinic_id)->get()->result_array();

		$data['units'] = $this->db->distinct()->select('units')->from('investigation_range')->get()->result_array();
		$data['methods'] = $this->db->distinct()->select('method')->from('investigation_range')->get()->result_array();
		$data['sample_types'] = $this->db->distinct()->select('sample_type')->from('investigation_range')->get()->result_array();

		$data['view'] = 'lab/view_order';
		$this->load->view('layout', $data);
	}


	public function view_report($billing_id, $investigation_id){
		
		$clinic_id = $this->session->userdata('clinic_id');

		// Get Patient/Guest Information of the order/billing
		$data['billing_info'] = $this->db->select('B.billing_id, B.appointment_id, B.patient_id, B.doctor_id, B.guest_name, B.guest_mobile, B.position_status, A.appointment_date, A.appointment_time_slot, Doc.first_name as doc_first_name, Doc.last_name as doc_last_name, Doc.department_id, Dep.department_name')->from('billing B')->join('appointments A','B.appointment_id = A.appointment_id','left')->join('doctors Doc','B.doctor_id = Doc.doctor_id','left')->join('department Dep','Doc.department_id = Dep.department_id','left')->where('B.billing_id =', $billing_id)->where('B.clinic_id =',$clinic_id)->get()->row();
		
		$data['billing_invoice'] = $this->db->select('*')->from('billing_invoice')->where('billing_id =', $billing_id)->get()->row();	
		// Get Template type
		$data['template_type'] = $this->Generic_model->getFieldValue('lab_templates','template_type',array('investigation_id'=>$investigation_id));
		
		// Get the lab report id 
		$data['patient_lab_report_id'] = $this->Generic_model->getFieldValue('patient_lab_reports','patient_lab_report_id',array('billing_id' => $billing_id, 'investigation_id' => $investigation_id,'clinic_id'=>$clinic_id));

		$patient_labreport=$data['patient_lab_report_id'];
		// Get the medical test name
		$data['test_name'] = $this->Generic_model->getFieldValue('investigations','investigation',array('investigation_id' => $investigation_id));
		// echo '<pre>';
		// print_r($patient_labreport);
		// echo '</pre>';
		// Get the investigation results w.r.to investigation id
		$lab_results = $this->db->select('PLR.consultant_remark, LI.patient_lab_reports_line_item_id,
		 LI.investigation_id, LI.template_type, LI.value, LI.remarks, I.investigation, I.item_code')
		->from('patient_lab_report_line_items LI')
		// ->join('clinic_investigations CI','LI.investigation_id = CI.investigation_id','inner') 	//hide
		// ->join('clinic_investigation_range CIR','CI.investigation_id = CIR.investigation_id','inner')	//hide
		->join('investigations I','LI.investigation_id = I.investigation_id','inner')
		->join('patient_lab_reports PLR','LI.patient_lab_report_id = PLR.patient_lab_report_id')
		->where('LI.parent_investigation_id =', $investigation_id)
		// ->where('CI.clinic_id =', $clinic_id) 	//hide
		->where('LI.patient_lab_report_id =', $patient_labreport)
		// ->where('CIR.primary_rec =',1)			//hide
		->order_by('LI.position','ASC')
		->get()->result_array();

	// 	$lab_results = $this->db->select('PLR.consultant_remark, LI.patient_lab_reports_line_item_id,
	// 	LI.investigation_id, LI.template_type, LI.value, LI.remarks, CI.clinic_investigation_id, 
	// 	CI.clinic_id, CIR.low_range, CIR.high_range, CIR.units, CIR.method,
	// 	 CI.other_information, I.investigation, I.item_code')
	//    ->from('patient_lab_report_line_items LI')
	//    ->join('clinic_investigations CI','LI.investigation_id = CI.investigation_id','inner')
	//    ->join('clinic_investigation_range CIR','CI.investigation_id = CIR.investigation_id','inner')
	//    ->join('investigations I','LI.investigation_id = I.investigation_id','inner')
	//    ->join('patient_lab_reports PLR','LI.patient_lab_report_id = PLR.patient_lab_report_id')
	//    ->where('LI.parent_investigation_id =', $investigation_id)
	//    ->where('CI.clinic_id =', $clinic_id)
	//    ->where('LI.patient_lab_report_id =', $patient_labreport)
	//    ->where('CIR.primary_rec =',1)
	//    ->order_by('LI.position','ASC')
	//    ->get()->result_array();

		// echo '<pre>';
		// print_r($lab_results);
		// echo '</pre>';
		// exit();/zzz

		foreach($lab_results as $result){
			
			$result['condition'] = $this->db->select('condition, low_range, high_range, units')->from('clinic_investigation_range')->where('investigation_id =',$result['investigation_id'])->order_by('condition_position','ASC')->get()->result_array();

			$data['lab_results'][] = $result;
		}

		$data['investigation_id'] = $investigation_id;
		$data['billing_id'] = $billing_id;

		// echo '<pre>';
		// print_r($clinic_id);
		// echo '</pre>';
		// exit();
		$data['clinic_id']=$clinic_id;
		$data['view'] = 'lab/view_report';
		$this->load->view('layout', $data);

	}


	public function report_pdf($billing_id, $investigation_id){
		// echo '<pre>';
		// print_r($billing_id,$investigation_id);
		// echo '</pre>';
		// exit();
		// Get the clinic_id
		$clinic_id = $this->session->userdata('clinic_id');
		$data['clinic_id'] = $clinic_id;
		// Get Patient/Guest Information of the order/billing
		$data['billing_info'] = $this->db->select('B.billing_id,B.clinic_id,B.invoice_no, B.appointment_id, B.patient_id, B.doctor_id, B.guest_name, B.guest_mobile, B.position_status, A.appointment_date, A.appointment_time_slot, Doc.first_name as doc_first_name, Doc.last_name as doc_last_name, Doc.department_id, Dep.department_name')->from('billing B')->join('appointments A','B.appointment_id = A.appointment_id','left')->join('doctors Doc','B.doctor_id = Doc.doctor_id','left')->join('department Dep','Doc.department_id = Dep.department_id','left')->where('B.billing_id =', $billing_id)->get()->row();
		// echo '<pre>';
		// echo $this->db->last_query()."<br>";
		// echo '</pre>';
		// exit();

		$data['billing_invoice'] = $this->db->select('*')->from('billing_invoice')->where('billing_id =', $billing_id)->get()->row();	

		$patient_lab_reports = $this->db->select('*')->from('patient_lab_reports')
		->where('billing_id =', $billing_id)->where('investigation_id =', $investigation_id)->get()->row();	
		
		$data['patient_lab_report_line_items'] = $this->db->select('*')->from('patient_lab_report_line_items')
		->where('patient_lab_report_id =', $patient_lab_reports->patient_lab_report_id)->get()->result();
		
		$data['appointments'] = $this->db->select("a.*, c.clinic_name,c.clinic_logo,c.address,c.clinic_phone,c.clinic_id,p.title, p.first_name as pname, p.last_name as plname,p.gender,p.age,p.address_line,p.umr_no,p.preferred_language,p.mobile,p.qrcode,p.allergy,d.salutation,d.first_name as dfname,d.last_name as dlname, d.qualification, d.registration_code, dep.department_name")->from("appointments a")->join("clinics c","a.clinic_id = c.clinic_id","left")->join("patients p","a.patient_id = p.patient_id","left")->join("doctors d","a.doctor_id = d.doctor_id","left")->join('department dep','d.department_id = dep.department_id')->where("a.clinic_id='" . $data['billing_info']->clinic_id . "'")->get()->row();

		$data['clinic_information'] = $this->db->select('C.clinic_id, C.clinic_name, C.email, C.clinic_phone, C.clinic_logo, C.address, C.location, C.pincode, D.district_name, S.state_name')->from('clinics C')->join('districts D','C.district_id = C.district_id','inner')->join('states S','C.state_id = S.state_id','inner')->where('clinic_id =', $clinic_id)->get()->row();	

		// Get Template type 
		$data['template_type'] = $this->Generic_model->getFieldValue('lab_templates','template_type',array('investigation_id'=>$investigation_id));

		// Get the lab report id 
		$data['patient_lab_report_id'] = $this->Generic_model->getFieldValue('patient_lab_reports','patient_lab_report_id',array('billing_id' => $billing_id, 'investigation_id' => $investigation_id));

		// // Get the lab report id 
		$data['consultant_remark'] = $this->Generic_model->getFieldValue('patient_lab_reports','consultant_remark',array('billing_id' => $billing_id, 'investigation_id' => $investigation_id));

		// Get the medical test name
		$data['test_name'] = $this->Generic_model->getFieldValue('investigations','investigation',array('investigation_id' => $investigation_id));

		// Get the investigation results w.r.to investigation id
		// $lab_results = $this->db->select('PLR.consultant_remark, LI.patient_lab_reports_line_item_id,
		//  LI.investigation_id, LI.template_type, LI.value,CI.clinic_investigation_id,CIR.clinic_investigation_range_id,
		//   CI.clinic_id, CIR.low_range, CIR.high_range, CIR.units, CIR.method, CI.other_information,CIR.remarks,
		//   I.investigation, I.item_code')->from('patient_lab_report_line_items LI')
		//   ->join('clinic_investigations CI','LI.investigation_id = CI.investigation_id','inner')
		//   ->join('clinic_investigation_range CIR','CI.investigation_id = CIR.investigation_id','inner')
		//   ->join('investigations I','LI.investigation_id = I.investigation_id','inner')
		//   ->join('patient_lab_reports PLR','LI.patient_lab_report_id = PLR.patient_lab_report_id')
		//   ->where('LI.parent_investigation_id =', $investigation_id)
		//   ->where('LI.patient_lab_report_id =',$data['patient_lab_report_id'])
		//   ->where('CIR.primary_rec =',1)->order_by('LI.position','ASC')->get()->result_array();
		$lab_results = $this->db->select('PLR.consultant_remark, LI.patient_lab_reports_line_item_id,
		LI.investigation_id, LI.template_type, LI.value, LI.remarks, I.investigation, I.item_code')
	   ->from('patient_lab_report_line_items LI')
	   // ->join('clinic_investigations CI','LI.investigation_id = CI.investigation_id','inner') 	//hide
	   // ->join('clinic_investigation_range CIR','CI.investigation_id = CIR.investigation_id','inner')	//hide
	   ->join('investigations I','LI.investigation_id = I.investigation_id','inner')
	   ->join('patient_lab_reports PLR','LI.patient_lab_report_id = PLR.patient_lab_report_id')
	   ->where('LI.parent_investigation_id =', $investigation_id)
	   // ->where('CI.clinic_id =', $clinic_id) 	//hide
	   ->where('LI.patient_lab_report_id =', $data['patient_lab_report_id'])
	   // ->where('CIR.primary_rec =',1)			//hide
	   ->order_by('LI.position','ASC')
	   ->get()->result_array();
			// echo $this->db->last_query();
			// exit();
		foreach($lab_results as $result){
			$result['condition'] = $this->db->select('condition, low_range, high_range, units')->from('clinic_investigation_range')->where('investigation_id =',$result['investigation_id'])->order_by('condition_position','ASC')->get()->result_array();

			$data['lab_results'][] = $result;
		}

		$data['report_info'] = $this->db->select('RE.first_name as report_entry_emp_fname, RE.last_name as report_entry_emp_lname,AE.first_name as consultant_fname, AE.last_name as consultant_lname, AE.digital_sign')->from('investigation_status IS')->join('employees RE','IS.report_entry_by = RE.employee_id','inner')->join('employees AE','IS.authenticated_by = AE.employee_id','inner')->where('IS.billing_id =',$billing_id)->get()->row();

        // PDF Settings
		$data['pdf_settings'] = $pdf_settings = $this->db->select("*")->from("clinic_pdf_settings")->where("clinic_id='".$data['appointments']->clinic_id."'")->get()->row();

		$this->load->library('M_pdf');
		$html = $this->load->view('lab/report_pdf',$data,true);
		$pdfFilePath = time().rand(1000,9999).".pdf";
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

		$id = $this->db->select('*')
		 ->from('billing_invoice IS')
		->where('IS.billing_id =',$billing_id)->get()->row();

		$data['billing_id'] = $billing_id;

		// Get invoices list for the billing id
		$data['invoices'] = $this->db->select('BI.invoice_no, BI.billing_id, BI.invoice_no_alias,BI.age,
		 BI.invoice_date, BI.payment_type, BI.payment_mode, BI.transaction_id,
		  B.patient_id, B.guest_name, B.guest_mobile')
		  ->from('billing_invoice BI')
		  ->join('billing B','BI.billing_id = B.billing_id','inner')
		  ->where('BI.billing_id =',$billing_id)
		  ->get()
		  ->result_array();


		// Get OSA amount from the billing table
		$data['osa'] = $this->Generic_model->getFieldValue('billing','osa',array('billing_id'=>$billing_id));

		if($_POST['save_pay_osa']){
			unset($_POST['save_pay_osa']);

			$post_params = $_POST;

			$clinic_id = $this->session->userdata('clinic_id');
			$user_id = $this->session->userdata('user_id');

			// Generate the invoice no.
			$billing_invoice['invoice_no_alias'] = $invoice_no_alias = generate_invoice_no_lab($clinic_id);
			$billing_invoice['invoice_no'] = $clinic_id.$invoice_no_alias;
			$billing_invoice['payment_type'] = 'OSA';
			$billing_invoice['age'] = $id->age;
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

			redirect('Lab/view_billing/'.$billing_id);			

		}

		$data['view'] = 'lab/pay_osa';
		$this->load->view('layout', $data);
	}

	// Update Investigation Report Status
	public function reportStatusUpdate(){
		$status['report_status'] = $_POST['report_status'];
		$investigation_status_id = $_POST['investigation_status_id'];
		$result = $this->Generic_model->updateData('investigation_status',$status,array('investigation_status_id' => $investigation_status_id));

		if($result) {
			echo trim($_POST['report_status']);
		}else{
			echo 0;
		}
	}

	// Update Position Status
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

		//  echo '<pre>';
		//  print_r($_POST);
		//  echo '</pre>';
		//  exit();

		$clinic_id = $this->session->userdata('clinic_id');

		$cond = '';
		if($clinic_id != 0)
			$cond = "and clinic_id=".$clinic_id;

		/** POST values
		* clinic_investigation_id
		* billing_id
		* investigation_id
		* investigation_status_id
		# extracting POST items to variables
		*/

		extract($_POST);

		$lab_template_info = $this->db->select('lab_template_id, lab_department_id, template_name, template_type')
		->from('lab_templates')->where('investigation_id =',$investigation_id)->get()->row();
		
		$consultant_remark = $this->Generic_model->getFieldValue('patient_lab_reports','consultant_remark',array('billing_id'=>$billing_id, 'investigation_id'=>$investigation_id));

		// Check if the lab_template_id exists in clinic_lab_templates DB. if exixts pull all the lab_template_line_items form clinic_lab_template_line_items DB
		$clinic_lab_template_id = $this->Generic_model->getFieldValue('clinic_lab_templates','clinic_lab_template_id',array('clinic_id'=>$clinic_id,'lab_template_id'=>$lab_template_info->lab_template_id));

		if($clinic_lab_template_id == 0){
			// Get lab template line items concern to lab_template_id
			$labTemplateLineItems = $this->db->distinct()->select('LI.lab_template_line_item_id, LI.parent_investigation_id,
			 LI.investigation_id, LI.parameter, LI.position, CIR.remarks')
			 ->from('lab_template_line_items LI')->join('clinic_investigation_range CIR','LI.investigation_id = CIR.investigation_id','inner')
			 ->where('LI.lab_template_id =',$lab_template_info->lab_template_id)
			 ->order_by('LI.lab_template_line_item_id','ASC')->get()->result_array();
		}
		else{

			// Get lab template line items concern to lab_template_id
			// $labTemplateLineItems = $this->db->distinct()->select('CLI.clinic_lab_template_line_item_id as lab_template_line_item_id, 
			// CLI.parent_investigation_id, CLI.investigation_id, CLI.parameter, CLI.position, CIR.remarks')
			// ->from('clinic_lab_template_line_items CLI')->join('clinic_investigation_range CIR','CLI.investigation_id = CIR.investigation_id',
			// 'inner')->where('CLI.clinic_lab_template_id =',$clinic_lab_template_id)
			// ->order_by('CLI.clinic_lab_template_line_item_id','ASC')
			// ->get()->result_array();

			$labTemplateLineItems = $this->db->distinct()->select('CLI.clinic_lab_template_line_item_id as lab_template_line_item_id,
			CLI.parent_investigation_id, CLI.investigation_id, CLI.parameter, CLI.position, CIR.remarks')
			->from('clinic_lab_template_line_items CLI')->join('clinic_investigation_range CIR','CLI.investigation_id = CIR.investigation_id',
			'inner')->where('CLI.clinic_lab_template_id =',$clinic_lab_template_id)
			->where('CLI.clinic_id =',$clinic_id)
			->order_by('CLI.position','ASC')
			->get()->result_array();
			// echo $labTemplateLineItems;
		}
		// echo '<pre>';
		//  print_r($labTemplateLineItems);
		//  echo '</pre>';
		//  exit();
		// investigation status id will come from $_POST
		$report_status = $this->Generic_model->getFieldValue('investigation_status','report_status',array('investigation_status_id'=>$investigation_status_id));

		if($lab_template_info->template_type == 'Excel'){

			$i = 0;

			$tabId = "template_excel";

			$tinfo = '';
			$tinfo .= '<tr><th>'.$lab_template_info->template_name.'<input type="hidden" id="position_update_tb" name="position_update" value=0><input type="hidden" id="clinic_lab_template_id_tb" name="clinic_lab_template_id" value="'.$clinic_lab_template_id.'"></th><th colspan="3" class="text-center" style="padding-left:0px !important;padding-right:0px !important;"><span class="customBtn" name="template_excel" onclick="newParamModal(\''.$clinic_investigation_id.'\',\''.$billing_id.'\',\''.$investigation_id.'\',\''.$investigation_status_id.'\',\''.$report_entry_status.'\',\''.$investigation.'\');">Add New Parameter</span></th></tr>';
			// $tinfo .= '<tr><th>'.$lab_template_info->template_name.'<input type="hidden" id="position_update_tb" name="position_update" value=0><input type="hidden" id="clinic_lab_template_id_tb" name="clinic_lab_template_id" value="'.$clinic_lab_template_id.'"></th><th colspan="2" class="text-center" style="padding-left:0px !important;padding-right:0px !important;"><span  name="template_excel"></span></th></tr>';
			$tinfo .= '<tr><th style="width:65%">Parameter</th><th class="text-center" style="width:20%">Value</th><th class="text-center" style="width:15%">Position</th><th class="text-center" style="width:15%">Delete</th></tr>';
			// print_r($labTemplateLineItems);
			foreach($labTemplateLineItems as $tresult)
			{	
				if($clinic_lab_template_id == 0)
				{
				$templateLineItems = $this->db->
				select('RLI.value, RLI.patient_lab_reports_line_item_id, 
				R.patient_lab_report_id')->from('patient_lab_reports R')
				->join('patient_lab_report_line_items RLI',
				'R.patient_lab_report_id = RLI.patient_lab_report_id','inner')
				->where('R.billing_id =',$billing_id)
				->where('RLI.lab_template_line_item_id =',$tresult['lab_template_line_item_id'])
				->get()
				->row();
				}
				else
				{
				$templateLineItems = $this->db->
				select('RLI.value, RLI.patient_lab_reports_line_item_id, 
				R.patient_lab_report_id')->from('patient_lab_reports R')
				->join('patient_lab_report_line_items RLI',
				'R.patient_lab_report_id = RLI.patient_lab_report_id','inner')
				->where('R.billing_id =',$billing_id)
				->where('RLI.clinic_lab_template_line_item_id =',$tresult['lab_template_line_item_id'])
				->get()
				->row();	
				// if(count($templateLineItems )>0)
				// {

				// }
				// print_r($templateLineItems);
				}

				if(count($templateLineItems) > 0){
					$itemValue = $templateLineItems->value;
					$patient_lab_report_id = $templateLineItems->patient_lab_report_id;
					$patient_lab_reports_line_item_id = $templateLineItems->patient_lab_reports_line_item_id;
				}else{
					$itemValue = '';
					$patient_lab_report_id = '';
					$patient_lab_reports_line_item_id = '';
				}

				$tinfo .= '<tr onclick="excelRowSelected(this);"><td>'.$tresult['parameter'].'<input type="hidden" style="width:20px;" class="position" name="clinic_lab_template_line_items['.$i.'][position]" value="'.(int)($i+1).'"><input type="hidden" style="width:20px" class="old_position" value="'.(int)($i+1).'"><input type="hidden" style="width:20px;" class="position" name="line_item['.$i.'][position]" value="'.(int)($i+1).'"></td><td><input class="form-control" type="text" name="line_item['.$i.'][value]" value="'.$itemValue.'" "><input type="hidden" value="'.$investigation_id.'" name="patient_lab_reports[investigation_id]" /><input type="hidden" value="'.$tresult['lab_template_line_item_id'].'" name="clinic_lab_template_line_items['.$i.'][clinic_lab_template_line_item_id]" /><input type="hidden" value="'.$tresult['investigation_id'].'" name="clinic_lab_template_line_items['.$i.'][investigation_id]" /><input type="hidden" name="line_item['.$i.'][template_type]" value="'.$info->template_type.'"><input type="hidden" name="patient_lab_reports[clinic_investigation_id]" value="'.$clinic_investigation_id.'"><input type="hidden" value="'.$patient_lab_report_id.'" name="patient_lab_reports[patient_lab_report_id]" /><input type="hidden" value="'.$patient_lab_report_id.'" name="line_item['.$i.'][patient_lab_report_id]" /><input type="hidden" value="'.$patient_lab_reports_line_item_id.'" name="line_item['.$i.'][patient_lab_reports_line_item_id]" /><input class="form-control" type="hidden" value="'.$tresult['parent_investigation_id'].'" name="line_item['.$i.'][parent_investigation_id]" /><input type="hidden" value="'.$tresult['investigation_id'].'" name="line_item['.$i.'][investigation_id]" /><input type="hidden" value="'.$lab_template_info->lab_template_id.'" name="patient_lab_reports[lab_template_id]" /><input type="hidden" value="'.$lab_template_info->lab_department_id.'" name="patient_lab_reports[lab_department_id]" /><input type="hidden" value="'.$tresult['lab_template_line_item_id'].'" name="line_item['.$i.'][lab_template_line_item_id]" /></td><td class="upDown text-center" style="padding-left:0px !important;padding-right:0px !important"><span><i class="fas fa-chevron-circle-up"></i><i class="fas fa-chevron-circle-down"></i></span></td><td><span style="margin-left: 19px;" id="test" value="'.$tresult['lab_template_line_item_id'].'" onclick="deleteClinicInvestigation('.$tresult['lab_template_line_item_id'].');"><i class="fas fa-trash-alt"></i><span></td></tr>';
	
				$i++;

			}

			$tinfo .= '<input type="hidden" name="investigation_status_id" value="'.$investigation_status_id.'"><input type="hidden" name="billing_id" value="'.$billing_id.'">';

			if($report_status == 'Auth'){
				$tinfo .= '<tr><td colspan="3"><div class="form-group"><label for="title" class="col-form-label">Consultant Interpretation</label><textarea name="patient_lab_reports[consultant_remark]" class="form-control" id="excel_consultant_remark_ta" cols="57" rows="3">'.$consultant_remark.'</textarea></td></tr>';
			}

			echo "excel".":|:".$tinfo;
		}
		else{
			$tinfo = '';
			$tinfo .= '<tr><th colspan="4">'.$lab_template_info->template_name.'<input type="hidden" id="position_update_tb" name="position_update" value=0><input type="hidden" id="clinic_lab_template_id_tb" name="clinic_lab_template_id" value="'.$clinic_lab_template_id.'"><span class="customBtn pull-right mt-0" name="template_excel" onclick="newParamGeneralModal(\''.$clinic_investigation_id.'\',\''.$billing_id.'\',\''.$investigation_id.'\',\''.$investigation_status_id.'\',\''.$report_entry_status.'\',\''.$investigation.'\');">Add New Parameter</span></th></tr>';
			// $tinfo .= '<tr><th>'.$lab_template_info->template_name.'<input type="hidden" id="position_update_tb" name="position_update" value=0><input type="hidden" id="clinic_lab_template_id_tb" name="clinic_lab_template_id" value="'.$clinic_lab_template_id.'"></th><th colspan="2" class="text-center" style="padding-left:0px !important;padding-right:0px !important;"><span class="customBtn" name="template_excel" onclick="newParamModal(\''.$clinic_investigation_id.'\',\''.$billing_id.'\',\''.$investigation_id.'\',\''.$investigation_status_id.'\',\''.$report_entry_status.'\',\''.$investigation.'\');">Add New Parameter</span></th></tr>';
			// $tinfo .= '<tr><th colspan="3">'.$lab_template_info->template_name.'<input type="hidden" id="position_update_tb" name="position_update" value=0><input type="hidden" id="clinic_lab_template_id_tb" name="clinic_lab_template_id" value="'.$clinic_lab_template_id.'"><span class="pull-right mt-0" name="template_excel"></span></th></tr>';
			$tinfo .= '<tr><th style="width:20%">Parameter</th><th class="text-center" style="width:65%">Value</th><th class="text-center" style="width:15%">Position</th><th class="text-center" style="width:15%">Delete</th></tr>';
			$i = 0;
			foreach($labTemplateLineItems as $tresult)
			{

				$templateLine = $this->db->select('RLI.remarks, 
				RLI.patient_lab_reports_line_item_id,
				 RLI.investigation_id, R.patient_lab_report_id')
				 ->from('patient_lab_reports R')
				 ->join('patient_lab_report_line_items RLI',
				 'R.patient_lab_report_id = RLI.patient_lab_report_id','inner')
				 ->where('R.billing_id =',$billing_id)
				 ->where('RLI.lab_template_line_item_id =',$tresult['lab_template_line_item_id'])
				//  ->where('RLI.clinic_lab_template_line_item_id =4')
				 ->get()->row();

				 if($clinic_lab_template_id == 0)
				 {
					$templateLineItems=$this->db->select('RLI.remarks, 
					RLI.patient_lab_reports_line_item_id,
					 RLI.investigation_id, R.patient_lab_report_id')
					 ->from('patient_lab_reports R')
					 ->join('patient_lab_report_line_items RLI',
					 'R.patient_lab_report_id = RLI.patient_lab_report_id','inner')
					 ->where('R.billing_id =',$billing_id)
					 ->where('RLI.lab_template_line_item_id =',$tresult['lab_template_line_item_id'])
					//  ->where('RLI.clinic_lab_template_line_item_id =4')
					 ->get()->row();
				 }
				 else
				//  if($templateLine->clinic_lab_template_line_item_id != 'NULL')
				 {
					$templateLineItems=$this->db->select('RLI.remarks, 
					RLI.patient_lab_reports_line_item_id,
					 RLI.investigation_id, R.patient_lab_report_id')
					 ->from('patient_lab_reports R')
					 ->join('patient_lab_report_line_items RLI',
					 'R.patient_lab_report_id = RLI.patient_lab_report_id','inner')
					 ->where('R.billing_id =',$billing_id)
					 ->where('RLI.clinic_lab_template_line_item_id =',$tresult['lab_template_line_item_id'])
					//  ->where('RLI.clinic_lab_template_line_item_id =4')
					 ->get()->row();
	
				 }

				//  $templateLineItems = $this->db->select('RLI.remarks, 
				//  RLI.patient_lab_reports_line_item_id, 
				//  RLI.investigation_id, R.patient_lab_report_id')
				//  ->from('patient_lab_reports R')
				//  ->join('patient_lab_report_line_items RLI',
				//  'R.patient_lab_report_id = RLI.patient_lab_report_id','inner')
				//  ->where('R.billing_id =',$billing_id)
				//  ->where('RLI.lab_template_line_item_id =',$tresult['lab_template_line_item_id'])
				//  ->get()->row();

				 
				//  $templateLineItems = $this->db->select('*')
				//  ->from('lab_template_line_items R')
				//  ->where('R.lab_template_line_item_id =',$tresult['lab_template_line_item_id'])
				//  ->get()->result();

				//  $a=0;
				//  foreach( $templateLineItems as $templates)
				//  {
				// 	$id=$templates->lab_template_line_item_id;
				// 	$name=$templates->parameter;
				//  }



				//  $templateLineItems= $this->db->SELECT("RLI.remarks,
				//   RLI.patient_lab_reports_line_item_id,
				// 	RLI.investigation_id, R.patient_lab_report_id 
				// 	FROM patient_lab_reports R INNER JOIN 
				// 	patient_lab_report_line_items RLI on
				// 	R.patient_lab_report_id = RLI.patient_lab_report_id where 
				// 	R.billing_id ='1' and RLI.lab_template_line_item_id ='1984'")->get()->row();

				if(count($templateLineItems) > 0){
					$remark = $templateLineItems->remarks;
					// $remark = 'Aggi Value';
					$patient_lab_report_id = $templateLineItems->patient_lab_report_id;
					$patient_lab_reports_line_item_id = $templateLineItems->patient_lab_reports_line_item_id;
				}
				else{
					// $remark = $tresult['remarks'];
					$remark ='';
					$patient_lab_report_id = '';
					$patient_lab_reports_line_item_id = '';
				}

				$redata = $this->db->select('p.remarks')
				->from('patient_lab_report_line_items p')
				->where('p.clinic_lab_template_line_item_id =4')
				->get()->row();

				$tinfo .= '<tr onclick="generalRowSelected(this);""><td>'
				.$tresult['parameter'].
				'<input type="hidden" class="position" 
				name="clinic_lab_template_line_items['.$i.'][position]" 
				value="'.(int)($i+1).'">
				<input type="hidden" class="old_position" value="'.(int)($i+1).'">
				<input type="hidden" style="width:20px;" class="position" name="line_item['.$i.'][position]" value="'.(int)($i+1).'"></td>
				<td><textarea class="form-control" rows="5" cols="20" name="line_item['.$i.'][remarks]">'.$remark.'</textarea>
				<input type="hidden" value="'.$investigation_id.'" name="patient_lab_reports[investigation_id]" /><input type="hidden" value="'.$tresult['investigation_id'].'" name="clinic_lab_template_line_items['.$i.'][investigation_id]" /><input type="hidden" name="line_item['.$i.'][template_type]" value="'.$info->template_type.'"><input type="hidden" name="patient_lab_reports[clinic_investigation_id]" value="'.$clinic_investigation_id.'"><input class="form-control" type="hidden" value="'.$tresult['parent_investigation_id'].'" name="line_item['.$i.'][parent_investigation_id]" /><input type="hidden" name="patient_lab_reports[patient_lab_report_id]" value="'.$patient_lab_report_id.'"><input type="hidden" value="'.$patient_lab_report_id.'" name="line_item['.$i.'][patient_lab_report_id]" /><input type="hidden" name="line_item['.$i.'][patient_lab_reports_line_item_id]" value="'.$patient_lab_reports_line_item_id.'"><input type="hidden" value="'.$tresult['investigation_id'].'" name="line_item['.$i.'][investigation_id]" /><input type="hidden" value="'.$lab_template_info->lab_template_id.'" name="patient_lab_reports[lab_template_id]" /><input type="hidden" value="'.$lab_template_info->lab_department_id.'" name="patient_lab_reports[lab_department_id]" /><input type="hidden" value="'.$tresult['lab_template_line_item_id'].'" name="line_item['.$i.'][lab_template_line_item_id]" /></td><td class="upDown text-center"><span><i class="fas fa-chevron-circle-up"></i><i class="fas fa-chevron-circle-down"></i></span></td><td><span style="margin-left: 19px;" id="test" value="'.$tresult['lab_template_line_item_id'].'" onclick="deleteClinicInvestigationgeneral('.$tresult['lab_template_line_item_id'].');"><i class="fas fa-trash-alt"></i><span></td></tr>';
				$i++;
				// $tinfo .= '<tr onclick="generalRowSelected(this);""><td>'.$tresult['parameter'].'<input type="hidden" class="position" name="clinic_lab_template_line_items['.$i.'][position]" value="'.(int)($i+1).'"><input type="hidden" class="old_position" value="'.(int)($i+1).'"><input type="hidden" style="width:20px;" class="position" name="line_item['.$i.'][position]" value="'.(int)($i+1).'"></td><td><textarea class="form-control" rows="5" cols="20" name="line_item['.$i.'][remarks]">'.$remark.'</textarea><input type="hidden" value="'.$investigation_id.'" name="patient_lab_reports[investigation_id]" /><input type="hidden" value="'.$tresult['investigation_id'].'" name="clinic_lab_template_line_items['.$i.'][investigation_id]" /><input type="hidden" name="line_item['.$i.'][template_type]" value="'.$info->template_type.'"><input type="hidden" name="patient_lab_reports[clinic_investigation_id]" value="'.$clinic_investigation_id.'"><input class="form-control" type="hidden" value="'.$tresult['parent_investigation_id'].'" name="line_item['.$i.'][parent_investigation_id]" /><input type="hidden" name="patient_lab_reports[patient_lab_report_id]" value="'.$patient_lab_report_id.'"><input type="hidden" value="'.$patient_lab_report_id.'" name="line_item['.$i.'][patient_lab_report_id]" /><input type="hidden" name="line_item['.$i.'][patient_lab_reports_line_item_id]" value="'.$patient_lab_reports_line_item_id.'"><input type="hidden" value="'.$tresult['investigation_id'].'" name="line_item['.$i.'][investigation_id]" /><input type="hidden" value="'.$lab_template_info->lab_template_id.'" name="patient_lab_reports[lab_template_id]" /><input type="hidden" value="'.$lab_template_info->lab_department_id.'" name="patient_lab_reports[lab_department_id]" /><input type="hidden" value="'.$tresult['lab_template_line_item_id'].'" name="line_item['.$i.'][lab_template_line_item_id]" /></td><td class="upDown text-center"><span><i class="fas fa-chevron-circle-up"></i><i class="fas fa-chevron-circle-down"></i></span></td></tr>';
				// $tinfo .= '<tr onclick="generalRowSelected(this);""><td>'
				// .$tresult['parameter'].$tresult['lab_template_line_item_id'].
				// '<input type="hidden" class="position" 
				// name="clinic_lab_template_line_items['.$i.'][position]" 
				// value="'.(int)($i+1).'">
				// <input type="hidden" class="old_position" value="'.(int)($i+1).'">
				// <input type="hidden" style="width:20px;" class="position" name="line_item['.$i.'][position]" value="'.(int)($i+1).'"></td>
				// <td><textarea class="form-control" rows="5" cols="20" name="line_item['.$i.'][remarks]">'.$remark.'</textarea>
				// <input type="hidden" value="'.$investigation_id.'" name="patient_lab_reports[investigation_id]" /><input type="hidden" value="'.$tresult['investigation_id'].'" name="clinic_lab_template_line_items['.$i.'][investigation_id]" /><input type="hidden" name="line_item['.$i.'][template_type]" value="'.$info->template_type.'"><input type="hidden" name="patient_lab_reports[clinic_investigation_id]" value="'.$clinic_investigation_id.'"><input class="form-control" type="hidden" value="'.$tresult['parent_investigation_id'].'" name="line_item['.$i.'][parent_investigation_id]" /><input type="hidden" name="patient_lab_reports[patient_lab_report_id]" value="'.$patient_lab_report_id.'"><input type="hidden" value="'.$patient_lab_report_id.'" name="line_item['.$i.'][patient_lab_report_id]" /><input type="hidden" name="line_item['.$i.'][patient_lab_reports_line_item_id]" value="'.$patient_lab_reports_line_item_id.'"><input type="hidden" value="'.$tresult['investigation_id'].'" name="line_item['.$i.'][investigation_id]" /><input type="hidden" value="'.$lab_template_info->lab_template_id.'" name="patient_lab_reports[lab_template_id]" /><input type="hidden" value="'.$lab_template_info->lab_department_id.'" name="patient_lab_reports[lab_department_id]" /><input type="hidden" value="'.$tresult['lab_template_line_item_id'].'" name="line_item['.$i.'][lab_template_line_item_id]" /></td><td class="upDown text-center"><span><i class="fas fa-chevron-circle-up"></i><i class="fas fa-chevron-circle-down"></i></span></td></tr>';
				// $i++;
			}

			$tinfo .= '<input type="hidden" name="investigation_status_id" value="'.$investigation_status_id.'"><input type="hidden" name="billing_id" value="'.$billing_id.'">';

			if($report_status == 'Auth'){
				$tinfo .= '<tr><td colspan="3"><div class="form-group"><label for="title" class="col-form-label">Consultant Interpretation</label><textarea name="patient_lab_remarks[consultant_remark]" class="form-control" id="general_consultant_remark_ta" cols="57" rows="3">'.$consultant_remark.'</textarea></td></tr>';
			}

			echo "general".":|:".$tinfo;
		}
	}

	public function deleteClinicLabItemId()
	{
		extract($_POST);
		$clinic_lab_template_id = $this->Generic_model->getFieldValue('clinic_lab_template_line_items','clinic_lab_template_line_item_id',array('clinic_lab_template_line_item_id'=>$_POST['id']));
		$clinic_lab_template_line_item_id = $this->db->select('*')
		->from('clinic_lab_template_line_items')
		->where('clinic_lab_template_line_item_id =',$_POST['id'])
		->get()->row();

		if(count($clinic_lab_template_line_item_id)>0){
		$this->db->query("delete from clinic_lab_template_line_items where clinic_lab_template_line_item_id=".$_POST['id']);
		echo '1';
		}else{
			echo 'No Id Found';
		}
	}

	public function templates_input_save()
	{

		// echo '<pre>';
		// print_r($_POST);
		// echo '</pre>';
		// exit();

		$clinic_id = $this->session->userdata('clinic_id');
		$clinic_lab_template_id = $_POST['clinic_lab_template_id'];
		$extraData = get_CM_by_dates();

		if($_POST['position_update'] == 1){

			if($_POST['clinic_lab_template_id'] == 0){
				// Insert lab template info into clinic lab template info for lab template id of the parent investigation id
				// Check Clinic Lab template DB for duplication
				$clinic_lab_template_id = $this->Generic_model->getFieldValue('clinic_lab_templates','clinic_lab_template_id',array('clinic_id'=>$clinic_id, 'lab_template_id'=>$_POST['patient_lab_reports']['lab_template_id']));

				$lab_template_info = $this->db->select('lab_template_id, investigation_id, lab_department_id, template_name, template_type')->from('lab_templates')->where('lab_template_id =',$_POST['patient_lab_reports']['lab_template_id'])->get()->result_array();

				// Check Clinic Lab template DB for duplication
				$clinic_lab_template_id = $this->Generic_model->getFieldValue('clinic_lab_templates','clinic_lab_template_id',array('clinic_id'=>$clinic_id, 'lab_template_id'=>$_POST['patient_lab_reports']['lab_template_id']));

				if($clinic_lab_template_id == 0){
					// Group data for clinic lab template info
					$clinicLabTemplateData['clinic_id'] = $clinic_id;
					$clinicLabTemplateData = array_merge($clinicLabTemplateData, $lab_template_info[0], $extraData);

					// Create records into clinic lab templates
					$clinic_lab_template_id = $this->Generic_model->insertDataReturnId('clinic_lab_templates',$clinicLabTemplateData);	
				}

				// Get lab template line items w.r.to lab_template_id
				// $labTemplateLineItems = $this->db->select('parent_investigation_id, investigation_id, parameter, position')->from('lab_template_line_items')->where('lab_template_id =',$_POST['patient_lab_reports']['lab_template_id'])->get()->result_array();
				$labTemplateLineItems = $this->db->select('parent_investigation_id, investigation_id, parameter,position')->from('lab_template_line_items')->where('lab_template_id =',$_POST['patient_lab_reports']['lab_template_id'])->get()->result_array();
				if(count($labTemplateLineItems) > 0){
					foreach($labTemplateLineItems as $lineItem){

						// Check Duplications
						$chkRes = $this->Generic_model->getFieldValue('clinic_lab_template_line_items','clinic_lab_template_line_item_id',array('clinic_lab_template_id'=>$clinic_lab_template_id,'clinic_id'=>$clinic_id,'parent_investigation_id'=>$_POST['patient_lab_reports']['investigation_id'],'investigation_id'=>$lineItem['investigation_id']));

						if($chkRes == 0){
							$clinicLabTemplateLineItem['clinic_id'] = $clinic_id;
							$clinicLabTemplateLineItem['clinic_lab_template_id'] = $clinic_lab_template_id;

							// $clinicLabTemplateLineItem['position'] = $clinic_lab_template_id;

							$clinicLabTemplateLineItemData = array_merge($clinicLabTemplateLineItem, $lineItem, $extraData);	

							// Create record for clinic lab template line items
							$this->Generic_model->insertData('clinic_lab_template_line_items',$clinicLabTemplateLineItemData);
						}
					}				
				}
			}

			$clinic_lab_template_line_items = $_POST['clinic_lab_template_line_items'];
 	// foreach($clinic_lab_template_line_items as $line_item){
	// 		   $rec['clinic_lab_template_id'] = $clinic_lab_template_id;
	// 		   $rec['clinic_id'] = $clinic_id;
	// 		   $rec['parent_investigation_id'] = $_POST['patient_lab_reports']['investigation_id'];
	// 		   $rec['investigation_id'] = $line_item['investigation_id'];
	// 		   $rec['parameter'] = $line_item['position'];
	// 		   $rec['position'] = $line_item['position'];
	// 			$rec['position'] = $line_item['position'];
	// 			$res = $this->Generic_model->updateData('clinic_lab_template_line_items',$rec,array('clinic_lab_template_id'=>$clinic_lab_template_id,'clinic_id'=>$clinic_id,'parent_investigation_id'=>$_POST['patient_lab_reports']['investigation_id'],'investigation_id'=>$line_item['investigation_id']));
	// 		}
			// Update all the position values in the table
			foreach($clinic_lab_template_line_items as $line_item){
				$rec['position'] = $line_item['position'];
				$res = $this->Generic_model->updateData('clinic_lab_template_line_items',$rec,array('clinic_lab_template_id'=>$clinic_lab_template_id,'clinic_id'=>$clinic_id,'parent_investigation_id'=>$_POST['patient_lab_reports']['investigation_id'],'investigation_id'=>$line_item['investigation_id']));
			}
		}

		$user_id = $this->session->userdata('user_id');

		$patient_lab_reports = $_POST['patient_lab_reports'];
		$patient_lab_reports['clinic_id'] = $clinic_id;	

		// echo '<pre>';
		// print_r($patient_lab_reports['clinic_id']);
		// echo '</pre>';
		// exit();


		if($patient_lab_reports['patient_id'] != ''){
			$patient_lab_reports['umr_no'] = $this->Generic_model->getFieldValue('patients','umr_no',array('patient_id'=>$patient_lab_reports['patient_id']));
		}

		$templateLineItems = $this->db->select('R.*')
		 ->from('patient_lab_reports R')
		 ->where('R.investigation_id =',$patient_lab_reports['investigation_id'])
		 ->where('R.clinic_investigation_id =',$patient_lab_reports['clinic_investigation_id'])
		 ->where('R.billing_id =',$patient_lab_reports['billing_id'])
		 ->get()->row();

		//  echo '<pre>';
		//  print_r($templateLineItems);
		//  echo '</pre>';
		//  exit();

		 if(count($templateLineItems)>0)
		 {
			$patient['modified_by'] = $user_id;
			$patient['modified_date_time'] = date('Y-m-d H:i:s');

			// Update Patient lab report master record with changes
			$updateRecRes = $this->Generic_model->updateData('patient_lab_reports',$patient,array('patient_lab_report_id'=>$templateLineItems->patient_lab_report_id));
			$patient_lab_report_id = $patient_lab_reports['patient_lab_report_id'];			
		 }
		 else{
			$patient_lab_reports['created_by'] = $patient_lab_reports['modified_by'] = $user_id;
			$patient_lab_reports['created_date_time'] = $patient_lab_reports['modified_date_time'] = date('Y-m-d H:i:s');

			// Create Patient lab report master record
			$patient_lab_report_id = $this->Generic_model->insertDataReturnId('patient_lab_reports',$patient_lab_reports);
		 }


		// if($patient_lab_reports['patient_lab_report_id'] == ''){

		// 	$patient_lab_reports['created_by'] = $patient_lab_reports['modified_by'] = $user_id;
		// 	$patient_lab_reports['created_date_time'] = $patient_lab_reports['modified_date_time'] = date('Y-m-d H:i:s');

		// 	// Create Patient lab report master record
		// 	$patient_lab_report_id = $this->Generic_model->insertDataReturnId('patient_lab_reports',$patient_lab_reports);
		// }
		// else{

		// 	$patient_lab_reports['modified_by'] = $user_id;
		// 	$patient_lab_reports['modified_date_time'] = date('Y-m-d H:i:s');

		// 	// Update Patient lab report master record with changes
		// 	$updateRecRes = $this->Generic_model->updateData('patient_lab_reports',$patient_lab_reports,array('patient_lab_report_id'=>$patient_lab_reports['patient_lab_report_id']));
		// 	$patient_lab_report_id = $patient_lab_reports['patient_lab_report_id'];			
		// }

		
		$templateLineItemsss = $this->db->select('R.*')
		 ->from('patient_lab_reports R')
		 ->where('R.investigation_id =',$patient_lab_reports['investigation_id'])
		 ->where('R.clinic_investigation_id =',$patient_lab_reports['clinic_investigation_id'])
		 ->where('R.billing_id =',$patient_lab_reports['billing_id'])
		 ->get()->row();

		 $templateLineItemss = $this->db->select('R.*')
		 ->from('patient_lab_report_line_items R')
		 ->where('R.patient_lab_report_id =',$templateLineItemsss->patient_lab_report_id)
		 // ->where('R.investigation_id =',$line_item[$i]['investigation_id'])
		 // ->where('R.parent_investigation_id =', $line_item[$i]['parent_investigation_id'])
		 ->get()->result();
		 

	

		// echo '<pre>';
		// // print_($_POST);
		// print_r($templateLineItemss);
		// echo '</pre>';
		// exit();

		// print_r($templateLineItemss);

		$templates = $this->db->select('R.*')
		->from('clinic_lab_templates R')
		->where('R.lab_template_id =',$patient_lab_reports['lab_template_id'])
		->where('R.investigation_id =',$patient_lab_reports['investigation_id'])
		->where('R.clinic_id =',$patient_lab_reports['clinic_id'])
		->get()->row();

	

		$line_item = $_POST['line_item'];

		$count = count($line_item);
		echo "checkingCount=".$count;
		
		$clinic_lab_template_id =$templates->clinic_lab_template_id;

		for($i=0; $i<$count; $i++){
			// $templateLineItemss = $this->db->select('R.*')
			// ->from('patient_lab_report_line_items R')
			// ->where('R.patient_lab_report_id =',$templateLineItemsss->patient_lab_report_id)
			// ->where('R.investigation_id =',$line_item[$i]['investigation_id'])
			// ->where('R.parent_investigation_id =', $line_item[$i]['parent_investigation_id'])
			// ->get()->row();

		
	
			if(count($templateLineItemss)>0)
			{	
				
				$clinic_lab_template_line_items_id = $this->db->select('R.*')
				->from('clinic_lab_template_line_items R')
				->where('R.clinic_lab_template_id =',$templates->clinic_lab_template_id)
				->where('R.investigation_id =',$line_item[$i]['investigation_id'])
				->where('R.parent_investigation_id =', $line_item[$i]['parent_investigation_id'])
				->where('R.clinic_id =',$patient_lab_reports['clinic_id'])
				->get()->row();

	
				
					if($patient_lab_reports['template_type']=='excel')
					{	
						$checknewparameter = $this->db->select('R.*')
						->from('patient_lab_report_line_items R')
						->where('R.patient_lab_report_id =',$templateLineItemsss->patient_lab_report_id)
						->where('R.investigation_id =',$line_item[$i]['investigation_id'])
						->where('R.parent_investigation_id =', $line_item[$i]['parent_investigation_id'])
						->get()->row();

						if(count($checknewparameter)>0)
						{
							$line['position'] = $line_item[$i]['position'];
							$line['value'] = $line_item[$i]['value'];
							$line['template_type'] = $line_item[$i]['template_type'];
							$line['parent_investigation_id'] = $line_item[$i]['parent_investigation_id'];
							$line['patient_lab_report_id'] = $templateLineItemsss->patient_lab_report_id;
							// $line['patient_lab_reports_line_item_id'] => 
							$line['investigation_id'] = $line_item[$i]['investigation_id'];
							$line['lab_template_line_item_id'] = $line_item[$i]['lab_template_line_item_id'];
							$line['clinic_lab_template_line_item_id'] = $clinic_lab_template_line_items_id->clinic_lab_template_line_item_id;
							$this->Generic_model->updateData('patient_lab_report_line_items', $line, array('patient_lab_reports_line_item_id'=>$checknewparameter->patient_lab_reports_line_item_id));
							echo "success".$line_item[$i]['investigation_id'];
						}	
						else{
							$line['position'] = $line_item[$i]['position'];
							$line['value'] = $line_item[$i]['value'];
							$line['template_type'] = $line_item[$i]['template_type'];
							$line['parent_investigation_id'] = $line_item[$i]['parent_investigation_id'];
							$line['patient_lab_report_id'] = $templateLineItemsss->patient_lab_report_id;
							// $line['patient_lab_reports_line_item_id'] => 
							$line['investigation_id'] = $line_item[$i]['investigation_id'];
							$line['lab_template_line_item_id'] = $line_item[$i]['lab_template_line_item_id'];
							$line['clinic_lab_template_line_item_id'] = $clinic_lab_template_line_items_id->clinic_lab_template_line_item_id;
							$this->Generic_model->insertDataReturnId('patient_lab_report_line_items',$line);
						}
					}
					else
					{
						$clinic_lab_template_line_items_id = $this->db->select('R.*')
						->from('clinic_lab_template_line_items R')
						->where('R.clinic_lab_template_id =',$templates->clinic_lab_template_id)
						->where('R.investigation_id =',$line_item[$i]['investigation_id'])
						->where('R.parent_investigation_id =', $line_item[$i]['parent_investigation_id'])
						->where('R.clinic_id =',$patient_lab_reports['clinic_id'])
						->get()->row();
		
						$checknewparameter = $this->db->select('R.*')
						->from('patient_lab_report_line_items R')
						->where('R.patient_lab_report_id =',$templateLineItemsss->patient_lab_report_id)
						->where('R.investigation_id =',$line_item[$i]['investigation_id'])
						->where('R.parent_investigation_id =', $line_item[$i]['parent_investigation_id'])
						// ->where('R.clinic_id =',$clinic_id)
						->get()->row();
					
						if(count($checknewparameter)>0)
						{
						$line['position'] = $line_item[$i]['position'];
						$line['remarks'] = $line_item[$i]['remarks'];
						$line['template_type'] = $line_item[$i]['template_type'];
						$line['parent_investigation_id'] = $line_item[$i]['parent_investigation_id'];
						$line['patient_lab_report_id'] = $templateLineItemsss->patient_lab_report_id;
						// $line['patient_lab_reports_line_item_id'] => 
						$line['investigation_id'] = $line_item[$i]['investigation_id'];
						$line['lab_template_line_item_id'] = $line_item[$i]['lab_template_line_item_id'];
						$line['clinic_lab_template_line_item_id'] = $clinic_lab_template_line_items_id->clinic_lab_template_line_item_id;
						$this->Generic_model->updateData('patient_lab_report_line_items', $line, array('patient_lab_reports_line_item_id'=>$checknewparameter->patient_lab_reports_line_item_id));
						}
						else{
							$line['position'] = $line_item[$i]['position'];
							$line['remarks'] = $line_item[$i]['remarks'];
							$line['template_type'] = $line_item[$i]['template_type'];
							$line['parent_investigation_id'] = $line_item[$i]['parent_investigation_id'];
							$line['patient_lab_report_id'] = $templateLineItemsss->patient_lab_report_id;
							// $line['patient_lab_reports_line_item_id'] => 
							$line['investigation_id'] = $line_item[$i]['investigation_id'];
							$line['lab_template_line_item_id'] = $line_item[$i]['lab_template_line_item_id'];
							$line['clinic_lab_template_line_item_id'] = $clinic_lab_template_line_items_id->clinic_lab_template_line_item_id;
							$this->Generic_model->insertDataReturnId('patient_lab_report_line_items',$line);
						}
					}
			
			}
			else{
				$clinic_lab_template = $this->db->select('R.*')
				->from('clinic_lab_template_line_items R')
				->where('R.clinic_lab_template_id =',$templates->clinic_lab_template_id)
				->where('R.investigation_id =',$line_item[$i]['investigation_id'])
				->where('R.parent_investigation_id =', $line_item[$i]['parent_investigation_id'])
				->where('R.clinic_id =',$patient_lab_reports['clinic_id'])
				->get()->row();
				// print_r($clinic_lab_template_line_items_id);
				if($patient_lab_reports['template_type']=='excel')
				{
					$line['position'] = $line_item[$i]['position'];
					$line['value'] = $line_item[$i]['value'];
					$line['template_type'] = $line_item[$i]['template_type'];
					$line['parent_investigation_id'] = $line_item[$i]['parent_investigation_id'];
					$line['patient_lab_report_id'] = $templateLineItemsss->patient_lab_report_id;
					// $line['patient_lab_reports_line_item_id'] => 
					$line['investigation_id'] = $line_item[$i]['investigation_id'];
					$line['lab_template_line_item_id'] = $line_item[$i]['lab_template_line_item_id'];
					$line['clinic_lab_template_line_item_id'] = $clinic_lab_template->clinic_lab_template_line_item_id;
					$this->Generic_model->insertDataReturnId('patient_lab_report_line_items',$line);
				}
				else{
					$line['position'] = $line_item[$i]['position'];
					$line['remarks'] = $line_item[$i]['remarks'];
					$line['template_type'] = $line_item[$i]['template_type'];
					$line['parent_investigation_id'] = $line_item[$i]['parent_investigation_id'];
					$line['patient_lab_report_id'] = $templateLineItemsss->patient_lab_report_id;
					// $line['patient_lab_reports_line_item_id'] => 
					$line['investigation_id'] = $line_item[$i]['investigation_id'];
					$line['lab_template_line_item_id'] = $line_item[$i]['lab_template_line_item_id'];
					$line['clinic_lab_template_line_item_id'] = $clinic_lab_template->clinic_lab_template_line_item_id;
					$this->Generic_model->insertDataReturnId('patient_lab_report_line_items',$line);
				}
	
			}
	
		}


		// $line_item = $_POST['line_item'];

		// $count = count($line_item);
		
		// for($i=0; $i<$count; $i++){
		// 	$line_item[$i]['patient_lab_report_id'] = $patient_lab_report_id;
		// 	if($line_item[$i]['patient_lab_reports_line_item_id'] == ''){
		// 		// Create the record
		// 		$this->Generic_model->insertDataReturnId('patient_lab_report_line_items',$line_item[$i]);
		// 	}else{
		// 		// Update the existing record with the changes
		// 		$this->Generic_model->updateData('patient_lab_report_line_items', $line_item[$i], array('patient_lab_reports_line_item_id'=>$line_item[$i]['patient_lab_reports_line_item_id']));
		// 	}
		// }

		// Update the report status & report entry status in investigation_status table for an investigation
		$report_status['report_entry_status'] = $_POST['report_entry_status'];

		if($_POST['report_entry_status'] == 1){
			// If 1, that means the entry of the results were done and the report is put fwd for authentication. Status is 'Auth' - 'AUTHENTICATION'
			if($_POST['report_authentication'] == 1){
				// If 1, that means the entry of the results were done and the report is put fwd for authentication. Status is 'Auth' - 'AUTHENTICATION'
				$report_status['report_status'] = 'RDY';
				$report_status['authenticated_date_time'] = date('Y-m-d H:i:s');
				$report_status['report_authentication'] = 1;
				$report_status['authenticated_by'] = $user_id;
			}else{
				$report_status['report_entry_status'] = 1;
				$report_status['report_entry_date_time'] = date('Y-m-d H:i:s');	
				$report_status['report_status'] = 'Auth';
				$report_status['report_entry_by'] = $user_id;	
			}	
		}else{
			// If 0, then report entry is still pending and in RE - 'REPORT ENTRY' status
			$report_status['report_status'] = 'RE';
			$report_status['report_entry_date_time'] = date('Y-m-d H:i:s');
			$report_status['report_entry_by'] = $user_id;
			$report_status['report_entry_status'] = 0;
		}
			
		$report_status['modified_by'] = $user_id;
		$report_status['modified_date_time'] = date('Y-m-d H:i:s');

		$this->Generic_model->updateData('investigation_status', $report_status, array('investigation_status_id' => $_POST['investigation_status_id']));	
		
		$type=$_POST['patient_lab_reports']['template_type'];
		$patient_lab_report_id=$_POST['patient_lab_reports']['patient_lab_report_id'];

		if($type == 'general')
		{
			$consultant_remark['consultant_remark']=$_POST['patient_lab_remarks']['consultant_remark'];
			$result = $this->Generic_model->updateData('patient_lab_reports',$consultant_remark,array('patient_lab_report_id' => $templateLineItemsss->patient_lab_report_id));
		}
		else{
			$consultant_remark['consultant_remark']=$_POST['patient_lab_reports']['consultant_remark'];
			$result = $this->Generic_model->updateData('patient_lab_reports',$consultant_remark,array('patient_lab_report_id' => $templateLineItemsss->patient_lab_report_id));
		}
		redirect('Lab/view_order/'.$_POST['billing_id']);

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

	/**
	* Function createParameter 
	*/
	public function createParameter(){

		$clinic_id = $this->session->userdata('clinic_id');

		// Declare required variables
		extract($_POST);
		$extraData = get_CM_by_dates();
		$parent_id = $_POST['parent_investigation_id'];

		echo "POST: ";
		print_r($_POST);
		// exit();
		
		$postParams = $_POST;
		// print_r($postParams);
		/**
		* Check the new investigation with investigation master 
		* if exists 
		* 	1. Get the investigation_id
		*	2. Check investigation range DB w.r.to the investigation id and sample type... if exists : Leave intact; else : create new record
		* else
		*	1. Insert into investigations (master) and get investigation id
		*	2. Insert into investigation_range (master)
		
		* Check the clinic_investigations for duplication with investigation_id
		* if exists
		*	1. Check the clinic_investigation_range for duplication
		*		if exists: leave intact; else: create range
		* else
		*	1. Create clinic_investigation and clinic_investigation_range records
		
		* Get lab_template_id w.r.to the parent_investigation_id and insert all records concern to tha lab_template_id from lab_templates into clinic_lab_templates
		* Do same with lab_template_line_items into clinic_lab_templates_line_items
		*/

		// Get required information for new parameter/investigation from the parent investigation id
		$investigationInfo = $this->db->select('I.investigation_id, I.lab_department_id, I.investigation as template_name, 
		I.template_type, LT.lab_template_id')->from('investigations I')
		->join('lab_templates LT','I.investigation_id = LT.investigation_id','inner')
		->where('I.investigation_id =', $parent_investigation_id)->get()->result_array();

		// echo "Investigation Info:";
		// print_r($investigationInfo);
		// print_r($investigationInfo[0]['template_name']);
		// echo " 1...";
		// exit();

		// Check the new investigation with investigation master DB
		$investigation_id = $this->Generic_model->getFieldValue('investigations','investigation_id',array('investigation' => $investigation));

		// echo 'Investigation Id: '.$investigation_id." 2...";
		
		if($investigation_id == 0) {
			
			// Investigation doesn't exists.. Create an investigation and get its investigation id
			// Group all Investigation Data
			$invData['investigation'] = $_POST['investigation'];
			$invData['other_information'] = $_POST['other_information'];
			$invData['package'] = 0;
			$invData['admin_review'] = 0;
			$invData['status'] = 1;

			// Generate Item Code
	        // Get the last item code of investigations
	        $item_code = $this->db->select("item_code")->from('investigations')->order_by('item_code','DESC')->limit(1)->get()->row();

	        if($item_code != ''){
	            $invData['item_code']= ++$item_code->item_code;    
	        }else{
	            $invData['item_code']= 'UMD0001';
	        }

			// Merge Arrays
			$invData = array_merge($investigationInfo[0],$invData,$extraData);

			// Template name, Lab template is fields does not exists in investigations db, So unset it
			unset($invData['investigation_id']);
			unset($invData['template_name']);
			unset($invData['lab_template_id']);

			// print_r($invData);
			// echo "3...";

			// Create Investigation
			$investigation_id = $this->Generic_model->insertDataReturnId('investigations',$invData);

			// echo $investigation_id;
			// echo "4...";

			// Group all Investigation Range variables
			$invRangeData['investigation_id'] = $investigation_id;	

			// If the investigation inserted is of template type : Excel 
			if($investigationInfo[0]['template_type'] == 'Excel'){
				$invRangeData['low_range'] = $_POST['low_range'];
				$invRangeData['high_range'] = $_POST['high_range'];
				$invRangeData['units'] = $_POST['units'];
				$invRangeData['sample_type'] = $_POST['sample_type'];
				$invRangeData['method'] = $_POST['method'];
			}else{
				$invRangeData['remarks'] = $_POST['remarks'];
			}

			// Merge Arrays
			$invRangeDataa = array_merge($invRangeData,$extraData);

			print_r($invRangeDataa);

			// Create Investigation Range Record
			$investigation_range_id = $this->Generic_model->insertDataReturnId('investigation_range',$invRangeDataa);	
		}

		// echo "Investigation range created & investigation range id: ".$investigation_range_id;

		//Inserting New Investigations To Lab_Templates:
		$lab_templates = $this->Generic_model->getFieldValue('lab_templates','lab_template_id',array('investigation_id' => $investigation_id));
		// print_r($investigation_id);
		// if($lab_templates == 0) {

		// 	$invest = $this->db->select('*')->from('investigations')
		// 	->where('investigation_id=',$investigation_id)
		// 	->get()->row();

		// 	$invest_name = $this->db->select('*')->from('investigations')
		// 	->where('investigation_id=',$_POST['parent_investigation_id'])
		// 	->get()->row();

		// 	$lab_templates_data['investigation_id'] = $investigation_id;
		// 	$lab_templates_data['lab_department_id'] = $invest->lab_department_id;
		// 	$lab_templates_data['template_name'] = $_POST['investigation'];
		// 	$lab_templates_data['template_type'] = $invest->template_type;
		// 	$lab_templates_data['status'] = 1;
		// 	$lab_templates_data['archive'] = '0';
		// 	$lab_templates_data['created_by'] = 1;
		// 	$lab_templates_data['modified_by'] = 1;
		// 	$lab_template_id = $this->Generic_model->insertDataReturnId('lab_templates',$lab_templates_data);	
		// }

		//Inserting New Investigations To Lab_Templates Line Items wr to lab_template_id:
		// $lab_template_line_items = $this->Generic_model->getFieldValue('lab_template_line_items','lab_template_line_item_id',array('lab_template_id' => $lab_template_id));

		// if($lab_template_line_items == 0) {
		// 	$lab_template_line_items_data['lab_template_id'] = $lab_template_id;
		// 	$lab_template_line_items_data['parent_investigation_id'] = $_POST['parent_investigation_id'];
		// 	$lab_template_line_items_data['investigation_id'] = $investigation_id;
		// 	$lab_template_line_items_data['parameter'] =  $_POST['investigation'];
		// 	$lab_template_line_items_data['position'] = '0';
		// 	$lab_template_line_items_data['archieve'] = '0';
		// 	$lab_template_line_items_data['created_by'] = 1;
		// 	$lab_template_line_items_data['modified_by'] = 1;
		// 	$lab_template_line_items_data['status'] = 1;
		// 	$lab_template_line_item_id = $this->Generic_model->insertDataReturnId('lab_template_line_items',$lab_template_line_items_data);
		// }

		
		//Inserting New Investigations To Lab_Templates Line Items wrto parent_investigation_id:
		// $lab_template_line_items = $this->Generic_model->getFieldValue('lab_template_line_items','lab_template_line_item_id',array('lab_template_id' => $investigation_id));

		// if($lab_template_line_items == 0) {
		// 	$lab_template_line_items_data['lab_template_id'] = $_POST['parent_investigation_id'];
		// 	$lab_template_line_items_data['parent_investigation_id'] = $_POST['parent_investigation_id'];
		// 	$lab_template_line_items_data['investigation_id'] = $investigation_id;
		// 	$lab_template_line_items_data['parameter'] =  $_POST['investigation'];
		// 	$lab_template_line_items_data['position'] = '0';
		// 	$lab_template_line_items_data['archieve'] = '0';
		// 	$lab_template_line_items_data['created_by'] = 1;
		// 	$lab_template_line_items_data['modified_by'] = 1;
		// 	$lab_template_line_items_data['status'] = 1;
		// 	$lab_template_line_item_id = $this->Generic_model->insertDataReturnId('lab_template_line_items',$lab_template_line_items_data);
		// }

		// Check clinic investigations for duplications
		$clinic_investigation_id = $this->Generic_model->getFieldValue('clinic_investigations','clinic_investigation_id',array('clinic_id'=>$clinic_id,'investigation_id' => $investigation_id));

		// echo 'Clinic Investigation Id: '.$clinic_investigation_id;

		if($clinic_investigation_id == 0) {

			echo 'Creating new records for clinic investigations';

			// Group all Clinic Investigations Data
			$clinicInvData['clinic_id'] = $clinic_id;
			$clinicInvData['investigation_id'] = $investigation_id;
			$clinicInvData['package'] = 0;
			$clinicInvData['lab_department_id'] = $investigationInfo[0]['lab_department_id'];

			if($investigationInfo['template_type'] = 'Excel'){
				$clinicInvData['other_information'] = $_POST['other_information'];
			}

			// Merge Arrays
			$clinicInvData = array_merge($clinicInvData, $extraData);

			// print_r($clinicInvData);

			// Create Clinic Investigation Record
			$clinic_investigation_id = $this->Generic_model->insertDataReturnId('clinic_investigations',$clinicInvData);

			// echo 'Clinic investigation id just created: '.$clinic_investigation_id;

			$clinic_investigation_price_id = $this->Generic_model->getFieldValue('clinic_investigation_price','clinic_investigation_id',array('clinic_id'=>$clinic_id,'investigation_id' => $investigation_id));
		
			if(count($clinic_investigation_price_id) >0){
			$clinicPriceData['clinic_id'] = $clinic_id;
			$clinicPriceData['clinic_investigation_id'] = $clinic_investigation_id;
			$clinicPriceData['investigation_id'] = $investigation_id;
			$clinicPriceData['sample_type'] = $_POST['sample_type']?$_POST['sample_type']:'';
			$clinicPriceData['price'] = '0';
			$clinicPriceData['status'] = '1';

			// Create Clinic Investigation Price Record
			$clinic_investigation_price_id = $this->Generic_model->insertDataReturnId('clinic_investigation_price',$clinicPriceData);
			}
			// Create Clinic Investigation Range Record
			$invRangeData['investigation_id'] = $investigation_id;	
			$invRangeData['clinic_investigation_id'] = $clinic_investigation_id;
				// If the investigation inserted is of template type : Excel 
				if($investigationInfo[0]['template_type'] == 'Excel'){
					$invRangeData['low_range'] = $_POST['low_range'];
					$invRangeData['high_range'] = $_POST['high_range'];
					$invRangeData['units'] = $_POST['units'];
					$invRangeData['sample_type'] = $_POST['sample_type']?$_POST['sample_type']:'';
					$invRangeData['method'] = $_POST['method'];
					$invRangeData['remarks'] = $_POST['other_information'];
					$invRangeData['condition'] = '';
					// $invRangeData['remarks'] = $_POST['remarks'];
				
				}
				else{
					// $invRangeData['low_range'] = '';
					// $invRangeData['high_range'] = '';
					// $invRangeData['units'] = '';
					// $invRangeData['sample_type'] = '';
					// $invRangeData['method'] = $_POST['method'];
					// $invRangeData['remarks'] = $_POST['remarks'];
					$invRangeData['remarks'] = $_POST['remarks'];
				}
				$clinicInvRangeData = array_merge($invRangeData, $extraData);
				$this->Generic_model->insertData('clinic_investigation_range',$clinicInvRangeData);
				// if($investigationInfo['template_type'] == 'General'){
				
					// $invRangeData['remarks'] = $_POST['remarks'];
				// }
	

			// $clinicInvRangeData = array_merge($invRangeData, $extraData);

			// // print_r($clinicInvRangeData);
			
			// // Create record
			// $this->Generic_model->insertData('clinic_investigation_range',$clinicInvRangeData);
			// echo 'Clinic investigation id just created: '.$clinic_investigation_id;

			
		}
		
		// Check Clinic Lab template DB for duplication
		$clinic_lab_template_id = $this->Generic_model->getFieldValue('clinic_lab_templates','clinic_lab_template_id',array('clinic_id'=>$clinic_id, 'lab_template_id'=>$investigationInfo[0]['lab_template_id']));

		if($clinic_lab_template_id == 0){
			// Group data for clinic lab template info
			$clinicLabTemplateData['clinic_id'] = $clinic_id;
			$clinicLabTemplateData = array_merge($clinicLabTemplateData, $investigationInfo[0], $extraData);

			// Create records into clinic lab templates
			$clinic_lab_template_id = $this->Generic_model->insertDataReturnId('clinic_lab_templates',$clinicLabTemplateData);	
			echo 'clinic_lab_template_id just created: '.$clinic_lab_template_id;

		}

		// Get lab template line items w.r.to lab_template_id
		$labTemplateLineItems = $this->db->select('parent_investigation_id, investigation_id, parameter,position')
		->from('lab_template_line_items')->where('lab_template_id =',$investigationInfo[0]['lab_template_id'])->get()->result_array();

		$check = $this->Generic_model->getFieldValue('clinic_lab_template_line_items','clinic_lab_template_line_item_id',array('clinic_lab_template_id'=>$clinic_lab_template_id,'clinic_id'=>$clinic_id,'parent_investigation_id'=>$parent_investigation_id));
		
		if(count($check) >  0){

			$template_id = $this->Generic_model->getFieldValue('clinic_lab_templates','clinic_lab_template_id',array('lab_template_id'=>$investigationInfo[0]['lab_template_id'],'clinic_id'=>$clinic_id,'investigation_id'=>$_POST['parent_investigation_id']));
			echo $_POST['parent_investigation_id'];
			echo $investigationInfo[0]['lab_template_id'];
			// exit();
			        $clinicLabTemp['clinic_id'] = $clinic_id;
					$clinicLabTemp['clinic_lab_template_id'] = $template_id;
					$clinicLabTemp['position'] ='0';
					$clinicLabTemp['parent_investigation_id'] = $_POST['parent_investigation_id'];
					$clinicLabTemp['investigation_id'] = $investigation_id;
					$clinicLabTemp['parameter'] =$_POST['investigation'];
					
					$clinicLab = array_merge($clinicLabTemp,  $extraData);	
					
					// Create record for clinic lab template line items
					$resultt= $this->Generic_model->insertDataReturnId('clinic_lab_template_line_items',$clinicLab);
					echo 'clinic_lab_template_line_items  created: '.$resultt;
					
		}
		else{
		if(count($labTemplateLineItems) > 0){
			$i=1;
			foreach($labTemplateLineItems as $lineItem){

				// Check Duplications
				// $chkRes = $this->Generic_model->getFieldValue('clinic_lab_template_line_items','clinic_lab_template_line_item_id',array('clinic_lab_template_id'=>$clinic_lab_template_id,'clinic_id'=>$clinic_id,'parent_investigation_id'=>$parent_investigation_id,'investigation_id'=>$lineItem['investigation_id']));
				$chkRes = $this->Generic_model->getFieldValue('clinic_lab_template_line_items','clinic_lab_template_line_item_id',array('clinic_lab_template_id'=>$clinic_lab_template_id,'clinic_id'=>$clinic_id,'parent_investigation_id'=>$parent_investigation_id));
				
				if(count($chkRes) == 0){
					$clinicLabTemplateLineItem['clinic_id'] = $clinic_id;
					$clinicLabTemplateLineItem['clinic_lab_template_id'] = $clinic_lab_template_id;
					$clinicLabTemplateLineItem['position'] =$i;
					$clinicLabTemplateLineItem['parent_investigation_id'] = $lineItem['parent_investigation_id'];
					$clinicLabTemplateLineItem['investigation_id'] = $lineItem['investigation_id'];
			        $clinicLabTemplateLineItem['parameter'] = $lineItem['parameter'];
					$i++;
					$clinicLabTemplateLineItemData = array_merge($clinicLabTemplateLineItem,  $extraData);	
					
					// Create record for clinic lab template line items
					$resultt= $this->Generic_model->insertDataReturnId('clinic_lab_template_line_items',$clinicLabTemplateLineItemData);
					echo 'clinic_lab_template_line_items  created: '.$resultt;

					if($_POST['billing_id'] != '')
					{
						$patient_lab_report_id = $this->db->select('*')->from('patient_lab_reports')
						->where('billing_id=',$_POST['billing_id'])
						->where('investigation_id=',$lineItem['parent_investigation_id'])
						->where('clinic_id=',$clinic_id)
						->get()->row();

						echo "Aggi Data=".$patient_lab_report_id;
						echo "Aggi ID=".$lineItem['investigation_id'],$_POST['billing_id'],$clinic_id;
						echo "Aggi Count=".count($patient_lab_report_id);

						if(count($patient_lab_report_id)>0)
						{
							$patient_lab_report_line_item_id = $this->db->select('*')->from('patient_lab_report_line_items')
							->where('patient_lab_report_id=',$patient_lab_report_id->patient_lab_report_id)
							->where('parent_investigation_id=',$lineItem['parent_investigation_id'])
							->where('investigation_id=',$lineItem['investigation_id'])
							->get()->row();

							if(count($patient_lab_report_line_item_id)>0)
							{
								$data['clinic_lab_template_line_item_id'] = $resultt;
								$this->Generic_model->updateData('patient_lab_report_line_items', $data, array('patient_lab_reports_line_item_id' => $patient_lab_report_line_item_id->patient_lab_reports_line_item_id));
								echo 'patient_lab_report_line_item_id Data updated Success!';
							}
						}
					}
					
					// $clinic_lab_template_line_items_id = $this->db->select('R.*')
					// ->from('clinic_lab_template_line_items R')
					// ->where('R.clinic_lab_template_id =',$clinic_lab_template_id)
					// ->where('R.investigation_id =',$lineItem['investigation_id'])
					// ->where('R.parent_investigation_id =',$parent_investigation_id)
					// ->where('R.clinic_id =',$clinic_id)
					// ->get()->row();

					
					// if($patient_lab_reports['template_type']=='excel')
					// {
					// $line['position'] = $line_item[$i]['position'];
					// $line['value'] = $line_item[$i]['value'];
					// $line['template_type'] = $line_item[$i]['template_type'];
					// $line['parent_investigation_id'] = $line_item[$i]['parent_investigation_id'];
					// $line['patient_lab_report_id'] = $templateLineItemsss->patient_lab_report_id;
					// // $line['patient_lab_reports_line_item_id'] => 
					// $line['investigation_id'] = $line_item[$i]['investigation_id'];
					// $line['lab_template_line_item_id'] = $line_item[$i]['lab_template_line_item_id'];
					// $line['clinic_lab_template_line_item_id'] = $clinic_lab_template_line_items_id->clinic_lab_template_line_item_id;
					// $this->Generic_model->updateData('patient_lab_report_line_items', $line, array('patient_lab_reports_line_item_id'=>$templateLineItemss[$i]->patient_lab_reports_line_item_id));
					// }
					// else
					// {
					// 	$line['position'] = $line_item[$i]['position'];
					// 	$line['remarks'] = $line_item[$i]['remarks'];
					// 	$line['template_type'] = $line_item[$i]['template_type'];
					// 	$line['parent_investigation_id'] = $line_item[$i]['parent_investigation_id'];
					// 	$line['patient_lab_report_id'] = $templateLineItemsss->patient_lab_report_id;
					// 	// $line['patient_lab_reports_line_item_id'] => 
					// 	$line['investigation_id'] = $line_item[$i]['investigation_id'];
					// 	$line['lab_template_line_item_id'] = $line_item[$i]['lab_template_line_item_id'];
					// 	$line['clinic_lab_template_line_item_id'] = $clinic_lab_template_line_items_id->clinic_lab_template_line_item_id;
					// 	$this->Generic_model->updateData('patient_lab_report_line_items', $line, array('patient_lab_reports_line_item_id'=>$templateLineItemss[$i]->patient_lab_reports_line_item_id));
					// }
				}
				// imp
				// if($chkRes == 0){
				// 	$clinicLabTemplateLineItem['clinic_id'] = $clinic_id;
				// 	$clinicLabTemplateLineItem['clinic_lab_template_id'] = $clinic_lab_template_id;
				// 	$clinicLabTemplateLineItem['position'] =$i;
				// 	$clinicLabTemplateLineItem['parent_investigation_id'] = $lineItem['parent_investigation_id'];
				// 	$clinicLabTemplateLineItem['investigation_id'] = $lineItem['investigation_id'];
			    //     $clinicLabTemplateLineItem['parameter'] = $lineItem['parameter'];
				// 	$i++;
				// 	$clinicLabTemplateLineItemData = array_merge($clinicLabTemplateLineItem,  $extraData);	
					
				// 	// Create record for clinic lab template line items
				// 	$resultt= $this->Generic_model->insertDataReturnId('clinic_lab_template_line_items',$clinicLabTemplateLineItemData);
				// 	echo 'clinic_lab_template_line_items  created: '.$resultt;

				// 	if($_POST['billing_id'] != '')
				// 	{
				// 		$patient_lab_report_id = $this->db->select('*')->from('patient_lab_reports')
				// 		->where('billing_id=',$_POST['billing_id'])
				// 		->where('investigation_id=',$lineItem['parent_investigation_id'])
				// 		->where('clinic_id=',$clinic_id)
				// 		->get()->row();

				// 		echo "Aggi Data=".$patient_lab_report_id;
				// 		echo "Aggi ID=".$lineItem['investigation_id'],$_POST['billing_id'],$clinic_id;
				// 		echo "Aggi Count=".count($patient_lab_report_id);

				// 		if(count($patient_lab_report_id)>0)
				// 		{
				// 			$patient_lab_report_line_item_id = $this->db->select('*')->from('patient_lab_report_line_items')
				// 			->where('patient_lab_report_id=',$patient_lab_report_id->patient_lab_report_id)
				// 			->where('parent_investigation_id=',$lineItem['parent_investigation_id'])
				// 			->where('investigation_id=',$lineItem['investigation_id'])
				// 			->get()->row();

				// 			if(count($patient_lab_report_line_item_id)>0)
				// 			{
				// 				$data['clinic_lab_template_line_item_id'] = $resultt;
				// 				$this->Generic_model->updateData('patient_lab_report_line_items', $data, array('patient_lab_reports_line_item_id' => $patient_lab_report_line_item_id->patient_lab_reports_line_item_id));
				// 				echo 'patient_lab_report_line_item_id Data updated Success!';
				// 			}
				// 		}
				// 	}
					
				// 	// $clinic_lab_template_line_items_id = $this->db->select('R.*')
				// 	// ->from('clinic_lab_template_line_items R')
				// 	// ->where('R.clinic_lab_template_id =',$clinic_lab_template_id)
				// 	// ->where('R.investigation_id =',$lineItem['investigation_id'])
				// 	// ->where('R.parent_investigation_id =',$parent_investigation_id)
				// 	// ->where('R.clinic_id =',$clinic_id)
				// 	// ->get()->row();

					
				// 	// if($patient_lab_reports['template_type']=='excel')
				// 	// {
				// 	// $line['position'] = $line_item[$i]['position'];
				// 	// $line['value'] = $line_item[$i]['value'];
				// 	// $line['template_type'] = $line_item[$i]['template_type'];
				// 	// $line['parent_investigation_id'] = $line_item[$i]['parent_investigation_id'];
				// 	// $line['patient_lab_report_id'] = $templateLineItemsss->patient_lab_report_id;
				// 	// // $line['patient_lab_reports_line_item_id'] => 
				// 	// $line['investigation_id'] = $line_item[$i]['investigation_id'];
				// 	// $line['lab_template_line_item_id'] = $line_item[$i]['lab_template_line_item_id'];
				// 	// $line['clinic_lab_template_line_item_id'] = $clinic_lab_template_line_items_id->clinic_lab_template_line_item_id;
				// 	// $this->Generic_model->updateData('patient_lab_report_line_items', $line, array('patient_lab_reports_line_item_id'=>$templateLineItemss[$i]->patient_lab_reports_line_item_id));
				// 	// }
				// 	// else
				// 	// {
				// 	// 	$line['position'] = $line_item[$i]['position'];
				// 	// 	$line['remarks'] = $line_item[$i]['remarks'];
				// 	// 	$line['template_type'] = $line_item[$i]['template_type'];
				// 	// 	$line['parent_investigation_id'] = $line_item[$i]['parent_investigation_id'];
				// 	// 	$line['patient_lab_report_id'] = $templateLineItemsss->patient_lab_report_id;
				// 	// 	// $line['patient_lab_reports_line_item_id'] => 
				// 	// 	$line['investigation_id'] = $line_item[$i]['investigation_id'];
				// 	// 	$line['lab_template_line_item_id'] = $line_item[$i]['lab_template_line_item_id'];
				// 	// 	$line['clinic_lab_template_line_item_id'] = $clinic_lab_template_line_items_id->clinic_lab_template_line_item_id;
				// 	// 	$this->Generic_model->updateData('patient_lab_report_line_items', $line, array('patient_lab_reports_line_item_id'=>$templateLineItemss[$i]->patient_lab_reports_line_item_id));
				// 	// }
				// }
				// imp end
			}				
		}
	}

		// Finally create clinic lab template line item record for the new parameter/investigation just added
		// Check for duplication
		$chkRes = $this->Generic_model->getFieldValue('clinic_lab_template_line_items','clinic_lab_template_line_item_id',array('clinic_lab_template_id'=>$clinic_lab_template_id,'clinic_id'=>$clinic_id,'parent_investigation_id'=>$parent_investigation_id,'investigation_id'=>$investigation_id));
		// print_r($chkRes);
		// exit();
		// $chkRes = $this->Generic_model->getFieldValue('clinic_lab_template_line_items','clinic_lab_template_line_item_id',array('clinic_lab_template_id'=>$clinic_lab_template_id,'clinic_id'=>$clinic_id,'parent_investigation_id'=>$parent_investigation_id,'investigation_id'=>$investigation_id));
		
		// $chkRes = $this->db->select("*")->from("clinic_lab_template_line_items cf")
        // ->where("cf.clinic_lab_template_id='" . $clinic_lab_template_id. "' and "
		// ."cf.clinic_id='" . $clinic_id."'")
		// ->where("cf.parent_investigation_id='" . $parent_investigation_id. "' and "
		// ."cf.investigation_id='" . $investigation_id."'")
        // ->get()->result();

		// if(($chkRes) == 0){
			$newLineItemData['clinic_lab_template_id'] = $clinic_lab_template_id;
			$newLineItemData['clinic_id'] = $clinic_id;
			$newLineItemData['parent_investigation_id'] = $parent_investigation_id;
			$newLineItemData['investigation_id'] = $investigation_id;
			$newLineItemData['parameter'] = $investigation;
			// $newLineItemData['position'] = '33';

			// Merge Arrays
			$newLineItemData = array_merge($newLineItemData, $extraData);

			// Create a record into clinic lab template line item
			// $result = $this->Generic_model->insertData('clinic_lab_template_line_items',$newLineItemData);	
			echo 'clinic_lab_template_line_items just created: '.$result;
		// }

		return true;

	}
	// End Function createParameter

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

		$billing = $this->db->select('billing_id, appointment_id, doctor_id, clinic_id, patient_id, guest_name, guest_mobile, billing_date_time, total_amount, billing_amount, discount, discount_unit, osa, payment_status, position_status')->from('billing')->where('clinic_id =',$clinic_id)->where('billing_type =','Lab')->order_by('billing_id','DESC')->get()->result_array();

		$i=0;

		foreach($billing as $billingRec)
		{
			$billing[$i] = $billingRec;
			$billing[$i]['guest_mobile'] = DataCrypt($billing[$i]['guest_mobile'], 'decrypt');
			$item_information = '';
			$html = '';

			// Get the list od lab investigations for billing ID
			$investigations = $this->db->select('InvS.investigation_status_id, InvS.billing_id, InvS.billing_line_item_id, InvS.investigation_id, InvS.report_status, Inv.investigation as item_information, Inv.template_type')->from('investigation_status InvS')->join('billing_line_items BLI','InvS.billing_line_item_id = BLI.billing_line_item_id','inner')->join('investigations Inv','InvS.investigation_id = Inv.investigation_id','inner')->where('BLI.billing_id =',$billingRec['billing_id'])->get()->result_array();
			
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
						$html .= '<i class="fas fa-vial '.$status[0].'" title="Sample Collection"></i>';
						$html .= '<i class="fas fa-microscope '.$status[2].'" title="Laboratory Testing"></i>';
					}else{
						$html .= '<i class="fas fa-radiation '.$status[1].'" title="Scan/Test"></i>';
					}				
					$html .= '<i class="fas fa-hourglass-half '.$status[3].'" title="Processing Result"></i>';
					$html .= '<i class="fas fa-notes-medical '.$status[4].'" title="Report Entry"></i>';
					$html .= '<i class="fas fa-file-signature '.$status[5].'" title="Authentication"></i>';
					$html .= '<i class="fas fa-print '.$status[6].'" title="Report Print"></i>';
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
		// echo $data['billing_info'][0]['item_information'];
		// echo $data['billing_info'][0]['investigation_id'];
		// exit();

		$data['view'] = 'lab/order_list';
		$this->load->view('layout', $data);
	}


	public function billing()
	{

		$clinic_id = $this->session->userdata('clinic_id');
		$tdate = date('Y-m-d');

		$billing = $this->db->select('billing_id, appointment_id, doctor_id, clinic_id, patient_id, guest_name, guest_mobile, billing_date_time, total_amount, billing_amount, discount, discount_unit, osa, payment_status')->from('billing')->where('clinic_id =',$clinic_id)->where('billing_type =','Lab')->order_by('billing_id','DESC')->get()->result_array();

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
		$this->db->query("delete from billing where billing_id=".$bid);
		$this->db->query("delete from billing_line_items where billing_id=".$bid);
		$this->db->query("delete from billing_invoice where billing_id=".$bid);

		// $billing_line_item_id = $this->db->select('*')->from('billing_line_items')
		// ->where('patient_lab_report_id=',$patient_lab_report_id->patient_lab_report_id)
		// ->where('parent_investigation_id=',$lineItem['parent_investigation_id'])
		// ->where('investigation_id=',$lineItem['investigation_id'])
		// ->get()->row();

		// $this->db->query("delete from billing_invoice where billing_id=".$bid);
		// delete from clinic_investigation_packages where clinic_investigation_package_id=".$cpid
		// $this->db->query("delete billing from billing inner join billing_line_items on billing.billing_id = billing_line_items.billing_id where billing.billing_id='".$bid."'");
		// $this->db->query("delete * from billing_invoice where billing_id='".$bid."'");
		redirect('Lab/orders');
	}

	public function add_order($prescription_id = NULL)
	{

		$clinic_id = $this->session->userdata('clinic_id');

		$data['clinic_doctors'] = $this->db->select('*')
		->from('clinic_doctor')->where('clinic_id =',$clinic_id)->get()->result();

		// echo $this->db->last_query();
		// exit();

		// Get Lab Discount
		// $data['lab_discount'] = $this->Generic_model->getFieldValue('clinics','lab_discount',array('clinic_id'=>$clinic_id));
		$data['lab_discount'] = $this->Generic_model->getFieldValue('clinic_lab','max_discount',array('clinic_id'=>$clinic_id));

		// $data['referral_doctor_lab_discount'] = $this->Generic_model->getFieldValue('clinics','referral_doctor_lab_discount',array('clinic_id'=>$clinic_id));
		$data['referral_doctor_lab_discount'] = $this->Generic_model->getFieldValue('clinic_lab','referral_doctor_max_discount',array('clinic_id'=>$clinic_id));

		$data['referral_doctors'] = $this->db->select('rfd_id, doctor_name, mobile, clinic_id, department, qualification, email')
		->from('referral_doctors')->where('clinic_id =',$clinic_id)->get()->result_array();

		// Get clinic investigation master version file
		$data['clinic_investigations_master_json_file'] = $this->Generic_model->getFieldValue('master_version','json_file_name',array('master_name'=>'clinic_investigations', 'clinic_id'=>$clinic_id));

		$tdate = date('Y-m-d');

		// If any prescription is requested to convert into an order
		if($prescription_id != NULL){

			$this->db->select('PIL.patient_investigation_line_item_id, PIL.investigation_name, INV.item_code, INV.short_form, INV.investigation, INV.investigation_id, INV.package, CI.clinic_investigation_id, CI.clinic_id, CIP.price, P.patient_id,P.gender,P.first_name, P.last_name, P.umr_no,P.age, P.mobile, P.alternate_mobile, PI.patient_investigation_id, PI.appointment_id, A.appointment_date, A.appointment_time_slot, Doc.doctor_id, Doc.first_name as doc_first_name, Doc.last_name as doc_last_name, Dep.department_id, Dep.department_name');
			$this->db->from('patient_investigation_line_items PIL');
			$this->db->join('investigations INV', 'PIL.investigation_id = INV.investigation_id','left');
			$this->db->join('clinic_investigations CI', 'INV.investigation_id = CI.investigation_id','left');
			$this->db->join('clinic_investigation_price CIP', 'CIP.clinic_investigation_id = CI.clinic_investigation_id','left');
			$this->db->join('patient_investigation PI', 'PIL.patient_investigation_id = PI.patient_investigation_id','left');
			$this->db->join('patients P', 'PI.patient_id = P.patient_id','left');
			$this->db->join('appointments A', 'PI.appointment_id = A.appointment_id','left');
			$this->db->join('doctors Doc', 'PI.doctor_id = Doc.doctor_id','left');
			$this->db->join('department Dep', 'Doc.department_id = Dep.department_id','left');
			$this->db->where('PIL.patient_investigation_id =',$prescription_id);
			$this->db->where('CIP.clinic_id =',$clinic_id);

			$data['investigation_cart'] = $getOrder = $this->db->get()->result();

		}
		//  echo "<pre>";
		// print_r($getOrder);	
		//  echo "</pre>";
		$data['view'] = 'lab/orders';
		$this->load->view('layout', $data);
	}



	// public function add_order($prescription_id = NULL)
	// {

	// 	$clinic_id = $this->session->userdata('clinic_id');
	// 	$data['patient_investigation_id']=$prescription_id;
	// 	// $data['billingCheck']=$billingCheckInfo=$this->db->select('*')
	// 	// ->from('billing')
	// 	// ->where('patient_investigation_id =',$prescription_id)
	// 	// ->where('clinic_id =',$clinic_id )
	// 	// ->get()
	// 	// ->row();
	// 	// $data['count']=count($billingCheckInfo);
	// 	// echo "<pre>";
	// 	// print_r(count($billingCheckInfo));
	// 	// echo "</pre>";
	// 	// Get Lab Discount
	// 	// $data['lab_discount'] = $this->Generic_model->getFieldValue('clinics','lab_discount',array('clinic_id'=>$clinic_id));
	// 	$data['lab_discount'] = $this->Generic_model->getFieldValue('clinic_lab','max_discount',array('clinic_id'=>$clinic_id));

	// 	// $data['referral_doctor_lab_discount'] = $this->Generic_model->getFieldValue('clinics','referral_doctor_lab_discount',array('clinic_id'=>$clinic_id));
	// 	$data['referral_doctor_lab_discount'] = $this->Generic_model->getFieldValue('clinic_lab','referral_doctor_max_discount',array('clinic_id'=>$clinic_id));

	// 	$data['referral_doctors'] = $this->db->select('rfd_id, doctor_name, mobile, clinic_id, department, qualification, email')->from('referral_doctors')->where('clinic_id =',$clinic_id)->get()->result_array();

	// 	// Get clinic investigation master version file
	// 	$data['clinic_investigations_master_json_file'] = $this->Generic_model->getFieldValue('master_version','json_file_name',array('master_name'=>'clinic_investigations', 'clinic_id'=>$clinic_id));

	// 	$tdate = date('Y-m-d');

	// 	// If any prescription is requested to convert into an order
	// 	if($prescription_id != NULL){

	// 		$this->db->select('PIL.patient_investigation_line_item_id, PIL.investigation_name, INV.item_code, INV.short_form, INV.investigation, INV.investigation_id, INV.package, CI.clinic_investigation_id, CI.clinic_id, CIP.price, P.patient_id, P.first_name, P.last_name, P.umr_no, P.mobile, P.alternate_mobile, PI.patient_investigation_id, PI.appointment_id, A.appointment_date, A.appointment_time_slot, Doc.doctor_id, Doc.first_name as doc_first_name, Doc.last_name as doc_last_name, Dep.department_id, Dep.department_name');
	// 		$this->db->from('patient_investigation_line_items PIL');
	// 		$this->db->join('investigations INV', 'PIL.investigation_id = INV.investigation_id','left');
	// 		$this->db->join('clinic_investigations CI', 'INV.investigation_id = CI.investigation_id','left');
	// 		$this->db->join('clinic_investigation_price CIP', 'CIP.clinic_investigation_id = CI.clinic_investigation_id','left');
	// 		$this->db->join('patient_investigation PI', 'PIL.patient_investigation_id = PI.patient_investigation_id','left');
	// 		$this->db->join('patients P', 'PI.patient_id = P.patient_id','left');
	// 		$this->db->join('appointments A', 'PI.appointment_id = A.appointment_id','left');
	// 		$this->db->join('doctors Doc', 'PI.doctor_id = Doc.doctor_id','left');
	// 		$this->db->join('department Dep', 'Doc.department_id = Dep.department_id','left');
	// 		$this->db->where('PIL.patient_investigation_id =',$prescription_id);
	// 		$this->db->where('CIP.clinic_id =',$clinic_id);

	// 		$data['investigation_cart'] = $getOrder = $this->db->get()->result_array();

	// 	}
	// 	//  echo "<pre>";
	// 	// print_r($getOrder);	
	// 	//  echo "</pre>";
	// 	$data['view'] = 'lab/orders';
	// 	$this->load->view('layout', $data);
	// }


	public function save_order()
	{

		// echo '<pre>';
		// print_r($_POST);
		// echo '</pre>';

		// echo '<pre>';
		// print_r($_POST['gender']);
		// echo '</pre>';

		// exit();


		// // $a=0;
		// foreach($_POST['billing_line_items'] as $key => $value)
		// {
		// 	// print_r($key['investigation_id']);
		// 	foreach($value as $sub_key => $sub_val)
		// 	{
		// 		print_r($sub_key);
		// 	}
		// 	$a++;
		// }

		// Segregate required params
		$clinic_id = $this->session->userdata('clinic_id');
		$user_id = $this->session->userdata('user_id');

		// Generate the invoice no.
		$invoice_no_alias = generate_invoice_no($clinic_id);
		$invoice_no = $clinic_id.$invoice_no_alias; 

		$billing = $_POST['billing'];

		// Encrypt the customer mobile no.
		$billing['guest_mobile'] = DataCrypt($billing['guest_mobile'], 'encrypt');
		// $billing['referral_doctor_id'] = $referral_doctor_id;
		$billing['invoice_no_alias'] = $invoice_no_alias;
		$billing['invoice_no'] = $invoice_no;
		$billing['doctor_id'] = $_POST['doctor_id'];
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

		foreach($billing_line_items as $lineItemInfo){
			$template_type = $this->Generic_model->getFieldValue('lab_templates','template_type',array('investigation_id'=>$lineItemInfo['investigation_id']));
			$lineItemInfoo['report_status'] = ($template_type == 'Excel') ? "SC" : "ST";
			$lineItemInfoo['item_information'] = $lineItemInfo['item_information'];
			if($lineItemInfo['clinic_investigation_package_id'] == '0')
			{
				$lineItemInfoo['clinic_investigation_id'] = $lineItemInfo['clinic_investigation_id'];
				$lineItemInfoo['clinic_investigation_package_id'] = $lineItemInfo['clinic_investigation_package_id'];
			}else{
				$lineItemInfoo['clinic_investigation_id'] = '0';
			}

			$lineItemInfoo['investigation_id'] = $lineItemInfo['investigation_id'];
			$lineItemInfoo['billing_id'] = $billing_id;
			$lineItemInfoo['created_by'] = $lineItemInfo['modified_by'] = $user_id;
			$lineItemInfoo['created_date_time'] = $lineItemInfo['modified_date_time'] = date('Y-m-d H:i:s');

    		// Save billing line item records into billing_line_item db
			$billing_line_item_id = $this->Generic_model->insertDataReturnId('billing_line_items',$lineItemInfoo);    	


			$investigationStatus['billing_id']  = $billing_id;
			$investigationStatus['billing_line_item_id'] = $billing_line_item_id;

			// Save records into patient investigation status
			// check for clini package or an individual investigaton
			if($lineItemInfo['investigation_id'] != 0){
				// Its an Investigaton
				$investigationStatus['item_information'] = $lineItemInfo['item_information'];
				$investigationStatus['investigation_id'] = $lineItemInfo['investigation_id'];
				$investigationStatus['report_status'] = ($template_type == 'Excel') ? "SC" : "ST";
				$investigationInfo = $lineItem[] = array_merge($investigationStatus, get_CM_by_dates());

				// insert new patient investogation status record
				$this->Generic_model->insertDataReturnId('investigation_status',$investigationInfo);
			}else{
				// Its a Clinic Package
				// Get the list of investigations clubbed in the package
				$investigationList = $this->db->select('PLI.investigation_id, INV.investigation as item_information')->from('clinic_investigation_package_line_items PLI')->join('investigations INV','PLI.investigation_id = INV.investigation_id','inner')->where('PLI.clinic_investigation_package_id =',$lineItemInfo['clinic_investigation_package_id'])->get()->result_array();
				// echo $this->db->last_query();
				foreach($investigationList as $investigation){

					$template_type = $this->Generic_model->getFieldValue('lab_templates','template_type',array('investigation_id'=>$lineItemInfo['investigation_id']));
					$investigationStatus['report_status'] = ($template_type == 'Excel') ? "SC" : "ST";
					$investigationStatus = array_merge($investigationStatus, $investigation);	
					$investigationInfo = $lineItem[] = array_merge($investigationStatus, get_CM_by_dates());

					// insert new patient investogation status record
					$this->Generic_model->insertDataReturnId('investigation_status',$investigationInfo);
				}
			}
		}

    	// Get billing invoice data
		$billing_invoice = $_POST['billing_invoice'];
		$billing_invoice['billing_id'] = $billing_id;
		$billing_invoice['age'] =$_POST['age'];
		$billing_invoice['gender'] =$_POST['gender'];
		$billing_invoice['invoice_no'] = $billing['invoice_no'];
		$billing_invoice['invoice_no_alias'] = $billing['invoice_no_alias'];
		$billing_invoice['invoice_date'] = date('Y-m-d');
		$billing_invoice['created_by'] = $billing_invoice['modified_by'] = $user_id;
		$billing_invoice['created_date_time'] = $billing_invoice['modified_date_time'] = date('Y-m-d H:i:s');

		if($billing_invoice['invoice_amount'] == $billing['billing_amount']){
			$billing_invoice['payment_type'] = 'Net';
		}

		if($_POST['referral_doctor_id']!='' || $_POST['referral_doctor_id']!='null')
		{
			$billing_invoice['referral_doctor_id'] = $_POST['referral_doctor_id'];
		}

		// Save invoice information into billing_invoice
		$this->Generic_model->insertData('billing_invoice', $billing_invoice);

		redirect('Lab/view_billing/'.$billing_id);

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

		// $method = $this->db->select("DISTINCT(method) as meth")->from("clinic_investigations")->get()->result_array();

		// $methods = '';

		// foreach($method as $result)
		// {
		// 	if($methods == '')
		// 		$methods = '"'.$result['meth'].'"';
		// 	else
		// 		$methods = $methods.',"'.$result['meth'].'"';
		// }

		// $data['methods'] = $methods;

		// Save the investigation to Clinic Investigation
		$param = $this->input->post();

		if(count($param) > 0){	

			foreach($param['clinic_investigation'] as $clinicInvestigationRecord){
				$clinicInvestigationRecord['clinic_id'] = $clinic_id;
				$clinicInvestigationRecord['status'] = 1;
				$clinicInvestigationRecord['created_by'] = $clinicInvestigationRecord['modified_by'] = $user_id;
				$clinicInvestigationRecord['created_date_time'] = $clinicInvestigationRecord['modified_date_time'] = date('Y-m-d H:i:s');

				$this->Generic_model->insertDataReturnId("clinic_investigations",$clinicInvestigationRecord);		

			}

			redirect('Lab/investigations');

		}

		$data['investigation_master_json_file'] = $this->Generic_model->getFieldValue('master_version','json_file_name',array('master_name'=>'investigation'));

		$data['view'] = 'lab/add_clinic_investigation';
		$this->load->view('layout', $data);
	}


	/*
	Function setup()
	To show up the different options to set the lab
	w.r.to clinic id and investigation id
	author: Uday Kanth rapalli
	*/
	public function setup(){
		$data['view'] = 'lab/setup';
		$this->load->view('layout', $data);
	}


	/*
	Function checkClinicInvestigation()
	To find the specific investigation record existence in the clinic investigation table
	w.r.to clinic id and investigation id
	Dev: Uday Kanth rapalli
	*/
	public function checkClinicInvestigation()
	{
		$clinic_id = $this->session->userdata('clinic_id');
		
		$investigationInfo = $this->db->select('investigation_id')->from('investigations')->where('investigation =',$_POST['investigation'])->get()->row();
		
		if(count($investigationInfo) > 0)
		{
			$investigationRec = $this->db->select('clinic_investigation_id')->from('clinic_investigations')->where('investigation_id =',$investigationInfo->investigation_id)->where('clinic_id =',$clinic_id)->get()->row();		

			if(count($investigationRec) > 0){
				echo $investigationRec->clinic_investigation_id;
			}else{
				echo 0;
			}
		}
	}


	/*
	Function populateList()
	Will retrieve the list of distinct units and distinct methods based on investigation
	w.r.to investigation id
	Dev: Uday Kanth Rapalli
	*/
	public function populateList()
	{
		// Post carries 2 params :: field name, table name
		extract($_POST); 

		// Get all the distinct populate field from the table
		$populateInfo = $this->db->select('DISTINCT('.$field.')')->from('clinic_investigation_range')->where($field.' IS NOT NULL')->where($field." !=","")->get()->result_array();

		$info = '';

		foreach($populateInfo as $rec){
			if($info != ''){
				$info .= ",".$rec[$field];
			}else{
				$info .= $rec[$field];
			}
		}

		echo $info;

		// if(count($populateInfo) > 0){
		// 	$infoJSON = json_encode($populateInfo);
		// 	echo $infoJSON;	
		// }else{
		// 	echo "0";
		// }		

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

		// // Get investigation id from the name
		// $investigationInfo = $this->db->select('investigation_id, item_code, investigation, short_form, template_type, package')->from('investigations')->where('investigation =',$investigationName)->get()->row();

		// // Get the lab template belongs to the above investigation in the post varaible
		// $labTemplateInfo = $this->db->select('lab_template_id, template_type')->from('lab_templates')->where('investigation_id =',$investigationInfo['investigation_id'])->get()->row();

		// Get investigation and Concern Lab Template Id using INNER JOIN
		$investigationInfo = $this->db->select('I.investigation_id, I.investigation, I.item_code, I.template_type, I.package, LT.lab_template_id')->from('investigations I')->join('lab_templates LT','I.investigation_id = LT.investigation_id','inner')->where('I.investigation =',$investigationName)->get()->row();

		$data['investigationInfo'] = array($investigationInfo);

		// Get all the packaged investigaton list with range and remarks information
		$data['lineItemsInfo'] = $this->db->select('LI.lab_template_line_item_id, I.investigation_id, I.investigation, I.item_code, I.sample_type, I.short_form, I.lab_department_id, I.other_information, IR.condition, IR.method, IR.low_range, IR.high_range, IR.units, IR.remarks, IR.result_condition')->from('lab_template_line_items LI')->join('investigations I','LI.investigation_id = I.investigation_id','inner')->join('investigation_range IR','I.investigation_id = IR.investigation_id','inner')->where('LI.lab_template_id =', $investigationInfo->lab_template_id)->get()->result_array();


		// echo "Template Type: ";
		// print_r($labTemplateInfo);

		// $investigation[0] = array_merge($investigationInfo,array('template_type'=>$labTemplateInfo->template_type));

		// print_r($investigation);
		// // exit();
		// // If lab_template exists
		// // Get the lab_templates_line_items
		// if(count($labTemplateInfo) > 0){

		// 	$template_type = $labTemplateInfo->template_type;

		// 	echo $template_type;

		// 	// Get investigation rangees and remarks w.r.to lab template line items
		// 	$labTemplateLineItems = $this->db->select('LI.investigation_id, IR.low_range, IR.high_range, IR.units, IR.remarks
		// 		')->from('lab_template_line_items LI')->join('investigation_range IR','LI.investigation_id = IR.investigation_id','inner')->where('lab_template_id =',$labTemplateInfo->lab_template_id)->get()->result_array();

		// 	print_r($labTemplateLineItems);
		// 	exit();

		// 	if(count($labTemplateLineItems) > 1){
		// 		$i = 1;
		// 		foreach($labTemplateLineItems as $lineItemRec){
		// 			$investigationRec = $this->db->select('investigation_id, item_code, investigation, short_form, sample_type')->from('investigations')->where('investigation_id=',$lineItemRec['investigation_id'])->get()->result_array();					
		// 			$investigation[$i] = array_merge($investigationRec[0], array('template_type'=>$template_type), array('remarks'=>$lineItemRec['remarks']));
		// 			$i++;
		// 		}
		// 	}
		// }

		// $info = $this->db->query("select * from investigations where investigation like '".$investigation."%'")->row();
		// echo $this->db->last_query();
		// print_r($investigation);
		$investigationJSON = json_encode($data);
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

		$allInvestigations = $this->db->select('*')->from('clinic_investigations I')
		->where('I.clinic_id =',$clinic_id)->get()->result();

		extract($_POST);

		$sample_type = '';

		if(strpos($investigation, "(Sample") !== false) {
			$investigation = substr($investigation, 0, strpos($investigation, "(Sample"));	
			$sampleInfo = explode("(Sample:",$investigation);	
		}
	
		// Trim the investigation - Remove unwanted spaces at both the ends if any
		$investigation = trim($investigation);

		if(count($sampleInfo) > 1){
			$sample_type = trim(substr($sampleInfo[1], 0, -1));
		}

		// Get investigation_id
		$investigationInfo = $this->db->select('CI.clinic_investigation_id, I.investigation_id, I.investigation, I.item_code, I.short_form')->from('investigations I')->join('clinic_investigations CI','I.investigation_id = CI.investigation_id','inner')->where('I.investigation =',$investigation)->get()->row();

		// Get the price of the investigaion w.r.to clinic_ic, investigation_id and sample_type
		if($sample_type !== ''){
			$price = $this->Generic_model->getFieldValue('clinic_investigation_price','price',array('clinic_id' => $clinic_id,'investigation_id' => $investigationInfo->investigation_id, 'sample_type' => $sample_type));	
		}else{
			$price = $this->Generic_model->getFieldValue('clinic_investigation_price','price',array('clinic_id' => $clinic_id,'investigation_id' => $investigationInfo->investigation_id));
		}

		// Get Package Info
		$packageInfo = $this->db->select('clinic_investigation_package_id, package_name, price')->from('clinic_investigation_packages')->where('clinic_id =',$clinic_id)->like('package_name',$investigation)->get()->row();

		$lab_discount = $this->Generic_model->getFieldValue('clinics','lab_discount',array('clinic_id =', $clinic_id));

		if(count($investigationInfo) > 0){
			echo $investigationInfo->clinic_investigation_id."*|*".$investigationInfo->investigation."*|*".$investigationInfo->item_code."*|*".$price."*|*".$investigationInfo->short_form."*|*".$lab_discount."*|*".$investigationInfo->investigation_id."*|*investigation";	
			$masterDataIds = $this->db->select("*")->from("clinic_investigation_packages")->where('clinic_id =', $clinic_id)->get()->result();
			if(count($masterDataIds)>0)
			{
				$a=0;
				foreach($masterDataIds as $test)
				{
					echo "*|*".$test->clinic_investigation_package_id;
					$clinicPackagesLineItems = $this->db->select("*")->from("clinic_investigation_package_line_items")->where('clinic_investigation_package_id =', $test->clinic_investigation_package_id)->get()->result();
					$i=0;
					foreach($clinicPackagesLineItems as $LineItems)
					{		
						echo ",".$LineItems->clinic_investigation_id;		
						$i++;
					}
					// echo ")";
					// echo "*|*".$test->clinic_investigation_package_id;
					$a++;
				}
			}	
		}else if(count($packageInfo) > 0){
			echo $packageInfo->clinic_investigation_package_id."*|*".$packageInfo->package_name."*|*".$packageInfo->item_code."*|*".$packageInfo->price."*|*".$packageInfo->short_form."*|*".$lab_discount."*|*package";
		}else{		
			echo '';
		}
	}


	public function get_clinic_package_info()
	{
		$clinic_id = $this->session->userdata('clinic_id');
		
		$package_name = $_POST['package_name'];
		// print_r($package_name);
		// print_r("fruit");

		$package_name = substr($package_name,0,-10); // This will cut the string " (Package)" form the package name var;

		$info = $this->db->select("clinic_investigation_package_id, clinic_id, package_name, item_code, short_form, price, status")->from("clinic_investigation_packages")->where("package_name =",$package_name)->get()->row();

		$lab_discount = $this->Generic_model->getFieldValue('clinics','lab_discount',array('clinic_id =', $clinic_id));
		
		if(count($info) > 0){
			echo $info->clinic_investigation_package_id."*|*".$info->package_name."*|*".$info->item_code."*|*".$info->price."*|*".$info->short_form."*|*".$lab_discount."*|*package"."*|*";	
			$masterDataIds = $this->db->select("*")->from("clinic_investigation_package_line_items")->where("clinic_investigation_package_id =",$info->clinic_investigation_package_id)->get()->result();
			if(count($masterDataIds)>0)
			{
				$a=0;
				foreach($masterDataIds as $test)
				{
					$clinic_investigations = $this->db->select("*")
					->from("clinic_investigations")
					->where("investigation_id =",$test->investigation_id)->get()->row();
					echo "*|*".$clinic_investigations->clinic_investigation_id;
					$a++;
				}
			}
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
			
			// Generate Item Code
            // Get the last item code of clinic package
			$item_code = $this->db->select("item_code")->from('clinic_investigation_packages')->order_by('item_code','DESC')->limit(1)->get()->row();

			if($item_code != ''){
				$lineinfo['item_code']= ++$item_code->item_code;    
			}else{
				$lineinfo['item_code']= 'PKG001';
			}

			$lineinfo['status'] = 1;
			$lineinfo['created_by'] = $user_id;
			$lineinfo['modified_by'] = $user_id;
			$lineinfo['created_date_time'] = date("Y-m-d H:i:s");
			$lineinfo['modified_date_time'] = date("Y-m-d H:i:s");
			$this->Generic_model->insertDataReturnId("clinic_investigation_packages",$lineinfo);

			// Update the master version for Clinic Investigations and also with the new added investigations
			update_clinic_investigation_master_version($clinic_id);

			redirect('Lab/lab_packages');
		}
		$data['view'] = 'lab/add_clinic_package';
		$this->load->view('layout', $data);
	}


	public function edit_clinic_package($invgpid)
	{
		$clinic_id = $this->session->userdata('clinic_id');
		$data['invgpinfo'] = $this->db->query("select * from clinic_investigation_packages where clinic_investigation_package_id=".$invgpid)->row();
		$param =$this->input->post();
		if(count($param)>0){
			$package_name = $_POST['package_name'];		
			$price = $_POST['price'];	
			$item_code = $_POST['item_code'];	

			$this->db->query("update clinic_investigation_packages set package_name='".$package_name."',price='".$price."', item_code='".$item_code."' where clinic_investigation_package_id=".$_POST['clinic_investigation_package_id']);
			redirect('Lab/lab_packages');
		}

		// Update the master version for Clinic Investigations and also with the new added investigations
		update_clinic_investigation_master_version($clinic_id);

		$data['view'] = 'lab/edit_clinic_package';
		$this->load->view('layout', $data);
	}


	public function view_clinic_package($invgpid)
	{
		$clinic_id = $this->session->userdata('clinic_id');
		$cond = '';
		if($clinic_id!=0){
			$cond = "and clinic_id=".$clinic_id;
		}
		$data['invgpinfo'] = $this->db->query("select * from clinic_investigation_packages where clinic_investigation_package_id=".$invgpid)->row();
		$data['plineitems'] = $this->db->query("select * from clinic_investigation_package_line_items a inner join clinic_investigations b on a.clinic_investigation_id=b.clinic_investigation_id inner join investigations c on b.investigation_id=c.investigation_id where clinic_investigation_package_id=".$invgpid)->result_array();

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

		// Get clinic investigation master version file
		$data['clinic_investigations_master_json_file'] = $this->Generic_model->getFieldValue('master_version','json_file_name',array('master_name'=>'clinic_investigations', 'clinic_id'=>$clinic_id));

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
		$this->db->query("delete from clinic_investigation_package_line_items where clinic_investigation_package_id=".$cpid);
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
				$this->Generic_model->updateData('lab_template_line_items',$templateLineItem,array('lab_template_line_item_id'=>$_POST['lab_template_line_item_id'][$i]));

			}else{
				// We are here, because it is a new line item... Insert it into lab_template_line_item DB
				$this->Generic_model->insertData('lab_template_line_items',$templateLineItem);
			}
		}

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
		$data['clinic_templates'] = $this->db->query("select * from lab_templates where archive=0 ".$cond)->result_array();
		$data['view'] = 'lab/master_templates_list';
		$this->load->view('layout', $data);
	}


	public function print_invoice($billing_invoice_id)
	{

		$clinic_id = $this->session->userdata('clinic_id');

		// Get clinic pdf printing settings information
		$data['pdf_settings'] = $pdf_settings = $this->db->select('clinic_pdf_setting_id, paper_type, doc_details, header, head_height, foot_height, footer, header_height, footer_height')->from('clinic_pdf_settings')->where('clinic_id =',$clinic_id)->get()->row();

		// Get Billing Invoice Information
		$data['invoice_information'] = $invoice_information = $this->db->select('billing_invoice_id, billing_id, invoice_no, invoice_no_alias, invoice_date, payment_type, payment_mode, transaction_id, invoice_amount')->from('billing_invoice')->where('billing_invoice_id =',$billing_invoice_id)->get()->row();

		// Get Billing Information includes Patient, Doctor and Department details etc
		$data['billing_information'] = $this->db->select('B.billing_id, B.appointment_id, B.patient_id, B.umr_no, B.guest_name, B.guest_mobile, B.total_amount, B.billing_amount, B.osa, B.discount, B.discount_unit, A.appointment_date, A.appointment_time_slot, Doc.first_name as doc_first_name, Doc.last_name as doc_last_name, Doc.registration_code, Doc.qualification, Dep.department_name, P.title, P.first_name as patient_first_name, P.last_name as patient_last_name, P.gender, P.age, P.address_line, P.email_id, P.mobile')->from('billing B')->join('appointments A','B.appointment_id = A.appointment_id','left')->join('doctors Doc','B.doctor_id = Doc.doctor_id','left')->join('department Dep','Doc.department_id = Dep.department_id','left')->join('patients P','B.patient_id = P.patient_id','left')->where('B.clinic_id =', $clinic_id)->where('B.billing_id =', $invoice_information->billing_id)->get()->row();

		if($data['billing_information']->patient_id == 0){
			$data['billing_information']->patient_name = ucwords($data['billing_information']->patient_first_name.' '.$data['billing_information']->pateint_last_name);
		}

		// Decrypt Patients mobile number
		$data['billing_information']->guest_mobile = DataCrypt($data['billing_information']->guest_mobile, 'decrypt');

		// Get billing order line items information
		$data['billing_line_items'] = $this->db->select('billing_line_item_id, billing_id, item_information, clinic_investigation_id, clinic_investigation_package_id, investigation_id, amount')->from('billing_line_items')->where('billing_id =',$invoice_information->billing_id)->get()->result_array();

		// Get Lab Basic Information from clinic_lab
		$data['lab_information'] = $this->db->select('clinic_lab_id, clinic_id, name, email, mobile, logo, gst_number, address')->from('clinic_lab')->where('clinic_id =', $clinic_id)->get()->row();

		$data['clinic_information'] = $this->db->select('C.clinic_id, C.clinic_name, C.email, C.clinic_phone, C.clinic_logo, C.address, C.location, C.pincode, D.district_name, S.state_name')->from('clinics C')->join('districts D','C.district_id = C.district_id','inner')->join('states S','C.state_id = S.state_id','inner')->where('clinic_id =', $clinic_id)->get()->row();	


		$this->load->library('M_pdf');

		$html = $this->load->view('lab/print_invoice',$data,true);
		$pdfFilePath = $data['invoice_information']->invoice_no_alias.".pdf";
        $stylesheet  = '';
        $stylesheet .= file_get_contents(base_url()."assets/css/bootstrap.min.css"); // external css
        $stylesheet .= file_get_contents(base_url()."assets/css/pdf.css");

        $this->m_pdf->pdf->autoScriptToLang = true;
        $this->m_pdf->pdf->autoLangToFont = true;
        $this->m_pdf->pdf->shrink_tables_to_fit = 1;
        $this->m_pdf->pdf->setAutoTopMargin = "stretch";
        $this->m_pdf->pdf->setAutoBottomMargin = "stretch";
        $this->m_pdf->pdf->defaultheaderline = 0;
        $this->m_pdf->pdf->WriteHTML($stylesheet,1);
        $this->m_pdf->pdf->WriteHTML($html,2);
        $this->m_pdf->pdf->Output("./uploads/billings/".$pdfFilePath, "F"); 

		redirect('uploads/billings/'.$pdfFilePath);

	}

	public function lab_employee()
	{
		$data['roles']=$this->Generic_model->getAllRecords('roles', $condition='', $order='');
		$data['profiles']=$this->Generic_model->getAllRecords('profiles', $condition='', $order='');
		$data['view'] = 'lab/lab_staff';
		$this->load->view('layout', $data);
	}

	public function lab_employee_add() {

        $user_id = $this->session->has_userdata('user_id');

        if($this->input->post('submit')){
    		//$pwd = $this->generateRandomString($length = 8);		
            $pwd = 1234;
            $user['password']=md5($pwd);
            $user['email_id']=$this->input->post('email_id');
            $user['mobile']=$this->input->post('mobile');
            $user['clinic_id']= $this->session->userdata('clinic_id');
            $user['user_type']='employee';
            $user['role_id']=$this->input->post('role_id');
            $user['profile_id']=$this->input->post('profile_id');
            $user['created_by'] = $user_id;
            $user['modified_by'] = $user_id;
            $user['created_date_time'] = date('Y-m-d H:i:s');
            $user['modified_date_time'] = date('Y-m-d H:i:s');

            $emp_id = $this->Generic_model->insertDataReturnId("users",$user);	

            // Send email if the email address is specified
            // if($user['email_id'] != '' || $user['email_id'] != NULL){
            //     $from='UMDAA';
            //     $to = $user['email_id'];  
            //     $subject = "Your Credential to use UMDAA portal";
            //     $message = "<p>Password : ".$pwd."</p>";  
            //     $ok = $this->mail_send->Content_send_all_mail($from,$to,$subject,'','',$message);
            // }

            $empcode = 'EMP-'.date('Ymd').$emp_id;
            $emp['employee_id']=$emp_id;
            $emp['employee_code']=$empcode;
            $emp['first_name']=$this->input->post('first_name');
            $emp['last_name']=$this->input->post('last_name');
            $emp['gender']=$this->input->post('gender');
            $emp['date_of_birth']= date('Y-m-d',strtotime($this->input->post('date_of_birth')));
            $emp['date_of_joining']=date('Y-m-d',strtotime($this->input->post('date_of_joining')));
            $emp['qualification']=$this->input->post('qualification');
            $emp['mobile']=$this->input->post('mobile');
            $emp['email_id']=$this->input->post('email_id');
            $emp['adhaar_no']=$this->input->post('adhaar_no');
            $emp['pan_no']=$this->input->post('pan_no');
            $emp['bank_account_no']=$this->input->post('bank_account_no');
            $emp['clinic_id']=$this->session->userdata('clinic_id');
            $emp['address']=$this->input->post('address');
            $emp['status']=1;
            $emp['created_by']=$user_id;
            $emp['modified_by']=$user_id;
            $emp['created_date_time']=date('Y-m-d H:i:s');
            $emp['modified_date_time']=date('Y-m-d H:i:s');

            $this->Generic_model->insertData('employees',$emp);

            $empCu['username']=$empcode;
            $emp_id = $this->Generic_model->updateData("users",$empCu,array('user_id'=>$emp_id));

            redirect('lab/settings');
		}
		// else{
        //     $data['roles']=$this->Generic_model->getAllRecords('roles', $condition='', $order='');
        //     $data['profiles']=$this->Generic_model->getAllRecords('profiles', $condition='', $order='');
        //     $data['view'] = 'employees/employee_add';
        //     $this->load->view('layout', $data);
        // }

	}
	
	public function getClinicPackagesData()
	{
		extract($_POST);

				$clinicPackagesLineItems = $this->db->select("*")->from("clinic_investigation_package_line_items")->where('clinic_investigation_package_id =', $id)->get()->result();
				$i=0;
				foreach($clinicPackagesLineItems as $LineItems)
				{		
					echo $LineItems->clinic_investigation_id.",";		
					$i++;
				}


	}
}
?>