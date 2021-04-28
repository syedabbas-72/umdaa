<?php

use function PHPSTORM_META\expectedReturnValues;

error_reporting(0);
defined('BASEPATH') or exit('No direct script access allowed');

class LabNew extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->library('mail_send', array('mailtype' => 'html'));
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		$this->load->model('Lab_model');
		$this->load->model('Generic_model');
		// $this->load->library('ssp.class');
	}


	public function index()
	{
		$clinic_id = $this->session->userdata('clinic_id');
		$total = $this->db->query("SELECT * FROM lab_packages limit 600")->result();
		$data['pdfSettings'] = $this->Generic_model->getSingleRecord("lab_pdf_settings", array('clinic_id' => $clinic_id));
		$data['total_records'] = $total;
		$data['view'] = 'LabNew/add_clinic_investigations';
		$this->load->view('layout', $data);
	}

	public function Ajax()
	{
		$data = $row = array();
		// Fetch member's records
		$memData = $this->Lab_model->getRows($_POST);


		$i = $_POST['start'];
		foreach ($memData as $member) {
			$i++;
			// $created = date( 'jS M Y', strtotime($member->created));
			// $status = ($member->status == 1)?'Active':'Inactive';
			// $data[] = array($i, $member->investigation, $member->department, '<a href="https://www.google.com/webhp?hl=en&ictx=2&sa=X&ved=0ahUKEwi4ucbI78LtAhVIWH0KHQg0CewQPQgI" title="click hear to add"><b> EDIT</b></a>');
			$data[] = array($i, $member->package_name, '&#x20B9; ' . $member->price, '<div class="d-flex flex-row"><button class="btn btn-primary mr-1 investigation_edit" data-toggle="modal" value="' . $member->clinic_lab_package_id . '" data-target="#inv_setings_popup"  onclick="inv_settings_edit(this.value)"><b><i class="fa fa-pencil" aria-hidden="true"></i></b></button><button id="delete_id"  value="' . $member->clinic_lab_package_id . '" class="btn btn-primary" onclick="delete_clinic_inv(this.value)"><b><i class="fa fa-trash" aria-hidden="true"></i></b></button></div>');
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Lab_model->countAll(),
			"recordsFiltered" => $this->Lab_model->countFiltered($_POST),
			"data" => $data,
		);

		echo json_encode($output);
	}

	public function price_update(){
		if(isset($_POST)){
			extract($_POST);
			$data['price'] = $price;
			$res = $this->Generic_model->updateData('clinic_lab_packages', $data, array('clinic_lab_package_id'=>$clinic_lab_package_id));
			if($res){
				echo "updated";
			}
			else{
				echo "0";
			}
		}
	}

	public function pdfSettings(){
		extract($_POST);
		echo "<pre>";
		print_r($_POST);
		echo "</pre>";
		// exit;
		$clinic_id = $this->session->userdata('clinic_id');
		$check = $this->Generic_model->getSingleRecord("lab_pdf_settings", array('clinic_id' => $clinic_id));
		if(count($check) <= 0){
			$data['clinic_id'] = $clinic_id;
			$data['paper_type'] = $paper_type;
			$data['header_report'] = $report_header;
			$data['header_invoice'] = $Invoice_header;
			$data['header_report_height'] = $report_header_height;
			$data['header_invoice_height'] = $invoice_header_height;
			$data['footer_report'] = $report_footer;
			$data['footer_invoice'] = $Invoice_footer;
			$data['footer_report_height'] = $report_footer_height;
			$data['footer_invoice_height'] = $invoice_footer_height;
			$data['created_by'] = $this->session->userdata('user_id');
			$data['created_date_time'] = date("Y-m-d H:i:s");
			$data['modified_by'] = $this->session->userdata('user_id');
			$data['modified_date_time'] = date("Y-m-d H:i:s");
			$this->Generic_model->insertData('lab_pdf_settings', $data);
		}
		else{
			$data['paper_type'] = $paper_type;
			$data['header_report'] = $report_header;
			$data['header_invoice'] = $Invoice_header;
			$data['header_report_height'] = $report_header_height;
			$data['header_invoice_height'] = $invoice_header_height;
			$data['footer_report'] = $report_footer;
			$data['footer_invoice'] = $Invoice_footer;
			$data['footer_report_height'] = $report_footer_height;
			$data['footer_invoice_height'] = $invoice_footer_height;
			$data['modified_by'] = $this->session->userdata('user_id');
			$data['modified_date_time'] = date("Y-m-d H:i:s");
			$this->Generic_model->updateData('lab_pdf_settings', $data, array('lab_pdf_setting_id' => $check->lab_pdf_setting_id));
		}

		$this->session->set_flashdata('msg', 'Successfully Updated');
		redirect("LabNew");
	}


	public function UpdatePositions(){
		if(isset($_POST)){
			extract($_POST);
			$check = $this->Generic_model->getSingleRecord('clinic_lab_package_line_items', array('clinic_lab_package_line_item_id'=>$clpli_id));
			$data['positions'] = $latest;
			if(count($positions) > 0){
				$i = 0;
				foreach($positions as $val){
					$data['positions'] = $i;
					$this->Generic_model->updateData('clinic_lab_package_line_items', $data, array('clinic_lab_package_line_item_id'=>$val));
					$i++;
				}
			}
			// $this->Generic_model->updateData('clinic_lab_package_line_items', $data, array('clinic_lab_package_line_item_id'=>$clpli_id));
			// echo $this->db->last_query();
		}
	}

	public function printReportStatus(){
		if(isset($_POST)){
			extract($_POST);
			$check = $this->db->query("select b.* from billing b, billing_line_items bl where b.billing_id=bl.billing_id and bl.billing_line_item_id='".$billing_line_item_id."'")->row();
			// echo $this->db->last_query();
			if($check->osa == "0.00" || $check->osa == 0){
				echo "1";
			}
			else{
				echo "0";
			}
		}
	}

	public function Investigation_search()
	{

		$search_key = $_POST['search'];
		$searchData = $this->db->query('SELECT * FROM lab_packages WHERE package_name LIKE "%' . $search_key . '%" LIMIT 20')->result();
		echo json_encode($searchData);
	}

	public function delete_clinic_inv()
	{

		$clinic_id = $this->session->userdata('clinic_id');

		extract($_POST);

		$this->db->query("DELETE FROM clinic_lab_packages WHERE clinic_lab_package_id ='" . $cli_inv_id . "' AND clinic_id ='" . $clinic_id . "'");
		$this->db->query("DELETE FROM clinic_lab_package_line_items WHERE clinic_lab_package_id ='" . $cli_inv_id . "' AND clinic_id ='" . $clinic_id . "' ");
	}

	public function add_investigaton()
	{
		extract($_POST);

		$price = $_POST['price'];

		$clinic_id = $this->session->userdata('clinic_id');

		$validation = $this->db->query("SELECT * FROM clinic_lab_packages where package_id='" . $inv_id . "' AND clinic_id ='" . $clinic_id . "'")->row();

		if (!empty($validation)) {
			echo 1;
		} else {

			$master = $this->db->query("SELECT * FROM lab_packages where package_id='" . $inv_id . "'")->row();
			$master->price = $price;
			$master->clinic_id = $clinic_id;

			$clinic_iv = $this->Generic_model->insertDataReturnId('clinic_lab_packages', $master);
			// echo $clinic_iv;

			$line_items = $this->db->query("SELECT * FROM `lab_package_line_items` WHERE package_id='" . $inv_id . "'")->result();

			foreach ($line_items as $line) {

				$line->clinic_id = $clinic_id;
				$line->clinic_lab_package_id = $clinic_iv;
				$this->Generic_model->insertDataReturnId('clinic_lab_package_line_items', $line);
			}
		}
	}

	public function settings_pop_up()
	{
		extract($_POST);
		$mater = $this->db->query("SELECT * FROM clinic_lab_packages cl,clinic_lab_package_line_items cli where cl.clinic_lab_package_id=cli.clinic_lab_package_id and cl.clinic_lab_package_id='".$clinic_package_id."' order by cli.positions ASC")->result();
		// echo $this->db->last_query();
		// exit();
		if(count($mater) > 0){
			echo "1*$".json_encode($mater);
		}
		else{
			$check = $this->db->query("select * from clinic_lab_packages where clinic_lab_package_id='".$clinic_package_id."'")->row();
			if(count($check) > 0){
				// echo "Naveen";
				echo "2*$".json_encode($check);
			}
			else{
				echo "0";
			}
		}

	}

	public function get_paramters_info()
	{
		extract($_POST);

		$check = $this->db->query("select * from clinic_lab_package_line_items where clinic_lab_package_line_item_id='".$clinic_lab_package_line_item_id."'")->row();
		echo json_encode($check);
		// echo '1';
		// exit();
	}

	public function orders_list()
	{
		$data['view'] = 'LabNew/Total_orders';
		$this->load->view('layout', $data);
	}

	public function total_orders()
	{

		$clinic_id = $this->session->userdata('clinic_id');

		$billing = $this->db->query("SELECT * FROM `billing` WHERE billing_type='lab' AND clinic_id='" . $clinic_id . "' ORDER BY `billing`.`billing_id` DESC")->result();

		$data['billing_details'] = $billing;

		$data['view'] = 'LabNew/Orders';

		$this->load->view('layout', $data);
	}

	public function investigation_names($billing_id)
	{

		$result = $this->db->query("SELECT * FROM `billing_line_items` WHERE billing_id='" . $billing_id . "'")->result();

		$output = "";

		$q = 1;

		foreach ($result as $res) {

			$output .= "<p class='m-0 p-0 my-2 font-weight-bold text-truncate'>" . $q . ".    " . "   " . $res->item_information . "</p>";

			$q++;
		}

		return $output;
	}

	public function icons($billing_id)
	{
		$clinic_id = $this->session->userdata('clinic_id');
		$result = $this->db->query("SELECT bl.*,b.osa FROM `billing_line_items` bl,`billing` b WHERE b.billing_id=bl.billing_id and bl.billing_id='" . $billing_id . "'")->result();
		$billIds = $this->db->query("select group_concat(billing_line_item_id) as ids from billing_line_items where billing_id='".$billing_id."'")->row();
		$ids = explode(",", $billIds->ids);
		if(count($ids) > 1){
			$checkDelStatus = $this->db->query("select * from lab_patient_reports where billing_line_item_id IN (".$billIds->ids.")")->row();
			if(count($checkDelStatus) > 0){
				if($checkDelStatus->status > 0){
					$delStatus = 0;
				}
				else{
					$delStatus = 1;
				}
			}
			else{
				$delStatus = 1;
			}
		}
		else{
			$delStatus = 0;
		}
		$departments = ['Consultation','Dopplers','CT Scan','MRI','Ultrasound','Contrast Examination','Dental','X-Ray'];
		// if()
		$output = "";
		foreach ($result as $res) {
			$checkInvStatus = $this->Generic_model->getSingleRecord('lab_patient_reports', array('billing_line_item_id'=>$res->billing_line_item_id));
			$clinicLabInfo = $this->Generic_model->getSingleRecord('clinic_lab_packages', array('package_id'=>$res->investigation_id,'clinic_id'=>$clinic_id));
			if(in_array($clinicLabInfo->department_name, $departments)){
				$icon = "fa-radiation";
			}
			else{
				$icon = "fa-vial";
			}
			if($checkInvStatus->status == 0){
				$sampleClass = "text-warning collectSample";
				$entryClass = "";
				$entry = '';
				$auth = '';
				$authenticateClass = "";
			}
			elseif($checkInvStatus->status == 1){
				$sampleClass = "text-success";
				$entryClass = "text-warning report_entry";
				$entry = 'data-toggle="modal" data-target="#report_entry_popup"';
				$auth = '';
				$authenticateClass = "";
			}
			elseif($checkInvStatus->status == 2){
				$sampleClass = "text-success";
				$entryClass = "text-success";
				$entry = '';
				$auth = 'data-toggle="modal" data-target="#authenticate_popup_entry"';
				$authenticateClass = "text-warning authenticate";
			}
			elseif($checkInvStatus->status == 3){
				$sampleClass = "text-success";
				$entryClass = "text-success";
				$entry = '';
				$auth = '';
				$authenticateClass = "text-success";
			}
			
			if($checkInvStatus->status == 3 && $res->osa == 0){
				$output .= '<p class="m-0 p-0 my-2 text-center"><a class="" href="'.base_url('LabNew/patient_report_pdf/'.$res->billing_line_item_id.'/'.$checkInvStatus->clinic_lab_package_id).'" target="_blank"><i class="fas fa-print"></i></a></p>';
			}
			else{
				$output .= '<p class="m-0 p-0 my-2 icons text-center">';
				$output .= '<i class="fas '.$icon.' icon mr-1 p-2 '.$sampleClass.'" data-value="' . $res->billing_line_item_id . '"></i>';
				$output .= '<i class="fas fa-file-alt icon mr-1 p-2 '.$entryClass.'" '.$entry.' data-value="' . $res->investigation_id.",".$res->billing_line_item_id . '"></i>';
				$output .= '<i class="fas fa-file-signature icon mr-1 p-2 '.$authenticateClass.'" '.$auth.' data-value="' .  $checkInvStatus->lab_patient_report_id . '"></i>';
			}
			if($delStatus == 1){
				$output .= '<i class="fas fa-trash trashicon text-danger mr-1 p-2 deleteLineItems" data-value="' . $res->billing_line_item_id . '" ></i></p>';
			}
			
			// $output .= '<button value="' . $res->investigation_id . "," . $res->billing_line_item_id . '" onclick="report_entry_popup(this.value)" title="Report Entry" type="button" data-toggle="modal" data-target="#report_entry_popup" style="background-color: Transparent; background-repeat:no-repeat; border: none;cursor:pointer;  color:#10367a;" title="authentication"><i class="far fa-file-alt fa-2x" style="font-size:23px;"></i></button> | <button title="Delete this investigation" value="' . $res->billing_line_item_id . '" onclick="delete_billing_line_items(this.value)" style="background-color: Transparent; background-repeat:no-repeat; border: none;cursor:pointer;  color:red;" > <i style="font-size:23px;" class="fas fa-trash fa-2x"></i> </button> ' . "<br><br>";
			// $output .= '<button style="background-color: Transparent; background-repeat:no-repeat; border: none;cursor:pointer;  color:#10367a;" title="Sample Collection"><i class="fas fa-vial icon icon fa-2x"></i></button> | <button style="background-color: Transparent; background-repeat:no-repeat; border: none;cursor:pointer;  color:#10367a;" title="Processing"><i class="fas fa-sync-alt fa-2x"></i></button> | <button value="' . $res->investigation_id . "," . $res->billing_line_item_id . '" onclick="report_entry_popup(this.value)" title="Report Entry" type="button" data-toggle="modal" data-target="#report_entry_popup" style="background-color: Transparent; background-repeat:no-repeat; border: none;cursor:pointer;  color:#10367a;" title="authentication"><i class="far fa-file-alt fa-2x"></i></button> |  <button  style="background-color: Transparent; background-repeat:no-repeat; border: none;cursor:pointer;  color:#10367a;"><i class="fas fa-file-signature fa-2x"></i></button>  | <button title="Delete this investigation" value="' . $res->billing_line_item_id . '" onclick="delete_billing_line_items(this.value)" style="background-color: Transparent; background-repeat:no-repeat; border: none;cursor:pointer;  color:red;" > <i class="fas fa-trash fa-2x"></i> </button> ' . "<br><br>";
		}

		return $output;
		// return '<button style="background-color: Transparent; background-repeat:no-repeat; border: none;cursor:pointer;  color:#10367a;"><i class="fas fa-vial icon icon fa-2x"></i></button> | <button style="background-color: Transparent; background-repeat:no-repeat; border: none;cursor:pointer;  color:#10367a;"><i class="fas fa-sync-alt fa-2x"></i></button> | <button type="button" data-toggle="modal" data-target="#report_entry_popup" id="cam_btn" style="background-color: Transparent; background-repeat:no-repeat; border: none;cursor:pointer;  color:#10367a;"><i class="far fa-file-alt fa-2x"></i></button> |  <button style="background-color: Transparent; background-repeat:no-repeat; border: none;cursor:pointer;  color:#10367a;"><i class="fas fa-file-signature fa-2x"></i></button>';
	}

	// Sample Collection
	public function Sample(){
		extract($_POST);
		$clinic_id = $this->session->userdata('clinic_id');
		if(isset($_POST['billing_line_item_id'])){
			$check = $this->Generic_model->getSingleRecord('lab_patient_reports', array('billing_line_item_id'=>$billing_line_item_id));
			if(count($check) > 0){
				$data['status'] = 1;
				$res = $this->Generic_model->updateData('lab_patient_reports', $data, array('billing_line_item_id'=>$billing_line_item_id));
				echo "1";
			}
			else{
				$billInfo = $this->Generic_model->getSingleRecord('billing_line_items', array('billing_line_item_id'=>$billing_line_item_id));
				$clinicInvInfo = $this->Generic_model->getSingleRecord('clinic_lab_packages', array('package_id'=>$billInfo->investigation_id,'clinic_id'=>$clinic_id));
				// echo  $this->db->last_query();
				if(count($billInfo) > 0){
					$data['clinic_lab_package_id'] = $clinicInvInfo->clinic_lab_package_id;
					$data['billing_line_item_id'] = $billing_line_item_id;
					$data['patient_package_name'] = $billInfo->item_information;
					$data['clinic_id'] = $clinic_id;
					$data['status'] = 1;
					$data['created_by'] = $this->session->userdata('user_id');
					$data['created_date_time'] = date('Y-m-d H:i:s');
					$data['modified_by'] = $this->session->userdata('user_id');
					$data['modified_date_time'] = date('Y-m-d H:i:s');
					$this->Generic_model->insertData('lab_patient_reports', $data);
					echo "1";
				}
				else{
					echo "0";
				}
			}
		}
	}

	public function report_entry_popup(){
		if(isset($_POST)){
			extract($_POST);
			$clinic_id = $this->session->userdata('clinic_id');
			$lineItemsInfo = $this->Generic_model->getSingleRecord('billing_line_items', array('billing_line_item_id'=>$billing_line_item_id));
			if(count($lineItemsInfo) > 0){
				$invInfo = $this->db->query("select * from clinic_lab_packages cp,clinic_lab_package_line_items cpl where cp.clinic_lab_package_id=cpl.clinic_lab_package_id and cp.package_id='".$lineItemsInfo->investigation_id."' and cp.clinic_id='".$clinic_id."' order by cpl.positions asc")->result();
				// echo $this->db->last_query();
				
				if(count($invInfo) > 0){
					?>
						<div class="row">
							<div class="col-12 p-0">
								<h3 class="text-center m-0 text-uppercase font-weight-bold">
									<button class="bg-danger close pull-right rounded-circle text-white" style="height:30px;width:30px;padding:0px !important" data-dismiss="modal">&times;</button><u><?=$invInfo[0]->package_name?></u>
								</h3>
							</div>
						</div>
					<?php
					$last_heading = "";
					foreach($invInfo as $value){
						if($value->investigation_name == ""){
							?>
							<div class="row mb-2">
								<div class="col-12">
									<?php
									if($value->g_e == "1"){
										?>
										<input type="text" name="patient_report_line_item_value[]" value="<?=$value->content?>" data-value="<?=$value->clinic_lab_package_line_item_id?>" class="form-control patient_report_line_item_value">
										<?php
									}
									elseif($value->g_e == "2"){
										?>
										<textarea data-value="<?=$value->clinic_lab_package_line_item_id?>" class="form-control summernote patient_report_line_item_value" rows="5"><?=$value->content?></textarea>
										<?php
									}
									elseif($value->g_e == "3"){
										$opt = explode(";", $value->dropdowns);
										?>
										<div class="col-6">
											<select name="patient_report_line_item_value[]" data-value="<?=$value->clinic_lab_package_line_item_id?>" class="custom-select patient_report_line_item_value">
											<?php
											foreach($opt as $val){
												?>
												<option value="<?=trim($val)?>"><?=trim($val)?></option>
												<?php
											}
											?>
											</select>
										</div>
										<div class="col-2">
											<h5 class="m-0 mt-2 p-0"><?=$value->units?></h5>
										</div>
										<div class="col-4">
											<h5 class="m-0 ml-4 mt-2 p-0"><?=$min_max?></h5>
										</div>
										<?php
									}
									elseif($value->g_e == "4"){
										if($value->inv_right != ""){
											?>
											<input type="text" name="patient_report_line_item_value[]" value="<?=$value->inv_right?>" data-value="<?=$value->clinic_lab_package_line_item_id?>" class="form-control patient_report_right">
											<?php
										}
										if($value->inv_left != ""){
											?>
											<input type="text" name="patient_report_line_item_value[]" value="<?=$value->inv_left?>" data-value="<?=$value->clinic_lab_package_line_item_id?>" class="form-control patient_report_left">
											<?php
										}
									}
									elseif($value->g_e == "43"){
										$rht = explode(";", $value->inv_right);
										$lft = explode(";", $value->inv_left);
										?>
										<div class="row">
											<div class="col-3">&nbsp;</div>
											<div class="col-9">
												<div class="row">
													<div class="col-6">
														<select name="patient_report_line_item_value[]" data-value="<?=$value->clinic_lab_package_line_item_id?>" class="custom-select patient_report_right">
														<?php
														foreach($rht as $rht_val){
															?>
															<option value="<?=trim($rht_val)?>"><?=trim($rht_val)?></option>
															<?php
														}
														?>
														</select>
													</div>
													<div class="col-6">
														<select name="patient_report_line_item_value[]" data-value="<?=$value->clinic_lab_package_line_item_id?>" class="custom-select patient_report_left">
														<?php
														foreach($lft as $lft_val){
															?>
															<option value="<?=trim($lft_val)?>"><?=trim($lft_val)?></option>
															<?php
														}
														?>
														</select>
													</div>
												</div>
											</div>
										</div>
										<?php
									}
									?>
								</div>
							</div>
							<?php
						}
						else{
							?>
							<div class="row mb-2">
							<?php
								$heading = explode(";", $value->heading);
								if(count($heading) > 1){
									?>
									<div class="col-4">&nbsp;</div>
									<div class="col-4">
										<h5 class="font-weight-bold m-0 mt-2 mb-2"><u><?=$heading[0]?></u></h5>
									</div>
									<div class="col-4">
										<h5 class="font-weight-bold m-0 mt-2 mb-2"><u><?=$heading[1]?></u></h5>									
									</div>
									<?php
								}
								else{
									if(trim($heading[0]) != ""){
										if(trim($heading[0]) != $last_heading){
	
											?>
											<div class="col-4">
												<h5 class="font-weight-bold m-0 mt-2 mb-2"><u><?=$value->heading?></u></h5>
											</div>
											<?php
											if($value->inv_right != ""){
												?>
												<div class="col-4">
													<h5 class="font-weight-bold m-0 mt-2 mb-2"><u>Right</u></h5>
												</div>
												<?php
											}
											if($value->inv_left != ""){
												?>
												<div class="col-4">
													<h5 class="font-weight-bold m-0 mt-2 mb-2"><u>Left</u></h5>
												</div>
												<?php
											}
										}
									}
								}
								?>
							</div>
							<div class="row mb-2">
								<div class="col-3">
									<h5 class="m-0 mt-2"><?=$value->investigation_name?></h5>
								</div>
								<div class="col-9">
									<div class="row">
									<?php
									$min_max = "";
									if($value->normal_range != ""){
										$min_max = $value->normal_range;
									}
									if($value->g_e == "1"){
										?>
										<div class="col-6">
											<input type="text" name="patient_report_line_item_value[]" value="<?=$value->content?>" data-value="<?=$value->clinic_lab_package_line_item_id?>" class="form-control patient_report_line_item_value">
										</div>
										<div class="col-2">
											<h5 class="m-0 mt-2 p-0"><?=$value->units?></h5>
										</div>
										<div class="col-4">
											<h5 class="m-0 ml-4 mt-2 p-0"><?=$min_max?></h5>
										</div>
										<?php
									}
									elseif($value->g_e == "2"){
										?>
										<div class="col-12">
											<textarea data-value="<?=$value->clinic_lab_package_line_item_id?>" class="form-control summernote patient_report_line_item_value" rows="5"><?=$value->content?></textarea>
										</div>
										<!-- <div class="col-2">
											<h5 class="m-0 mt-2 p-0"><?=$value->units?></h5>
										</div>
										<div class="col-4">
											<h5 class="m-0 ml-4 mt-2 p-0"><?=$min_max?></h5>
										</div> -->
										<?php
									}
									elseif($value->g_e == "3"){
										$opt = explode(";", $value->dropdowns);
										?>
										<div class="col-6">
											<select name="patient_report_line_item_value[]" data-value="<?=$value->clinic_lab_package_line_item_id?>" class="custom-select patient_report_line_item_value">
											<?php
											foreach($opt as $val){
												?>
												<option value="<?=trim($val)?>"><?=trim($val)?></option>
												<?php
											}
											?>
											</select>
										</div>
										<div class="col-2">
											<h5 class="m-0 mt-2 p-0"><?=$value->units?></h5>
										</div>
										<div class="col-4">
											<h5 class="m-0 ml-4 mt-2 p-0"><?=$min_max?></h5>
										</div>
										<?php
									}
									elseif($value->g_e == "4"){
										if($value->inv_right != ""){
											?>
											<div class="col-6">
												<input type="text" name="patient_report_line_item_value[]" value="<?=$value->inv_right?>" data-value="<?=$value->clinic_lab_package_line_item_id?>" class="form-control patient_report_right">
											</div>
											<?php
										}
										if($value->inv_left != ""){
											?>
											<div class="col-6">
												<input type="text" name="patient_report_line_item_value[]" value="<?=$value->inv_left?>" data-value="<?=$value->clinic_lab_package_line_item_id?>" class="form-control patient_report_left">
											</div>
											<?php
										}
									}
									elseif($value->g_e == "43"){
										$rht = explode(";", $value->inv_right);
										$lft = explode(";", $value->inv_left);
										?>
										<div class="col-6">
											<select name="patient_report_line_item_value[]" data-value="<?=$value->clinic_lab_package_line_item_id?>" class="custom-select patient_report_right">
											<?php
											foreach($rht as $rht_val){
												?>
												<option value="<?=trim($rht_val)?>"><?=trim($rht_val)?></option>
												<?php
											}
											?>
											</select>
										</div>
										<div class="col-6">
											<select name="patient_report_line_item_value[]" data-value="<?=$value->clinic_lab_package_line_item_id?>" class="custom-select patient_report_left">
											<?php
											foreach($lft as $lft_val){
												?>
												<option value="<?=trim($lft_val)?>"><?=trim($lft_val)?></option>
												<?php
											}
											?>
											</select>
										</div>
										<?php
									}
									?>
									</div>
								</div>
							</div>
							<?php
						}
						$last_heading = $value->heading;
					}
					?>
					<div class="row">
						<div class="col-12 text-center my-3">
							<button class="btn btn-app save_report" data-value="<?=$billing_line_item_id?>">Save Report</button>
						</div>
					</div>
					<?php
				}	
				else{
					?>
					<h4 class='text-center p-5 m-0'>No Investigations Found for this package</h4>
					<?php
				}
			}
		}
	}

	public function getLabPatientReports($package_line_item_id, $lab_patient_report_id, $type){
		$info = $this->db->query("select * from lab_patient_report_line_items where lab_patient_report_id='".$lab_patient_report_id."' and clinic_lab_package_line_item_id='".$package_line_item_id."'")->row();
		if(count($info) > 0){
			return $info->$type;
		}
		else{
			return "";
		}
	}

	public function getFinances(){

        $clinic_id = $this->session->userdata('clinic_id');
        $start = $_POST['startDate'];
        $end = $_POST['endDate'];
		$report_type = $_POST['report_type'];
		if($start == $end){
			$invDateCond = "and DATE(b.created_date_time) LIKE '".$start."%'";
		}
		else{
			$invDateCond = "and DATE(b.created_date_time) BETWEEN '".$start."%' and '".$end."%'";
		}

		if($report_type == "dashboard"){
			$amounts = $this->db->query("select * from billing b where b.billing_type='Lab' and b.clinic_id='".$clinic_id."' ".$invDateCond)->result();
			$data['query'] = $this->db->last_query();
			if(count($amounts) > 0){
				$disc = 0;
				$amt = 0;
				$i = 0;
				foreach($amounts as $value){
					$disc += ($value->total_amount*$value->discount)/100;
					$amt += ($value->total_amount - (($value->total_amount*$value->discount)/100)) - $value->osa;
					$i++;
				}
				$discounts = $disc;
				$collected = $amt;
			}
			else{
				$discounts = 0.00;
				$collected = 0.00;
			}
			
			$investigations = $this->db->query("select count(*) as count from billing b,billing_line_items bl where b.billing_id=bl.billing_id and b.billing_type='Lab' and b.clinic_id='".$clinic_id."' ".$invDateCond)->row();
			$osa = $this->db->query("select sum(b.osa) as osa from billing b,billing_line_items bl where b.billing_id=bl.billing_id and b.billing_type='Lab' and b.clinic_id='".$clinic_id."' ".$invDateCond)->row();
			
			$data['invCount'] = $investigations->count;
			$data['outstandingBal'] = number_format($osa->osa,2);
			$data['totalDiscount'] = number_format($discounts,2);
			$data['collectedAmount'] = number_format($collected,2);
			echo json_encode($data);
		}
		elseif($report_type == "referrals"){
			$info = $this->db->query("select count(b.referred_by) as count,group_concat(b.billing_id) as grp,b.referred_by from billing b where b.billing_type='Lab' and b.clinic_id='".$clinic_id."' ".$invDateCond." group by b.referred_by order by count DESC")->result();
			// echo $this->db->last_query();
			if(count($info) > 0){
				$i = 1;
				foreach($info as $value){
					$lineItemsInfo = $this->db->query("select count(*) as cnt from billing_line_items where billing_id IN (".$value->grp.")")->row();
					?>
					<tr>
						<td><?=$i?></td>
						<td><span><?=($value->referred_by == "")?'No Referrals':$value->referred_by?></span></td>
						<td><span><?=$lineItemsInfo->cnt?></span></td>
						<td><span><?=$lineItemsInfo->cnt?></span></td>
						<td><span><?=$lineItemsInfo->cnt?></span></td>
						<td><span><?=$lineItemsInfo->cnt?></span></td>
					</tr>
					<?php
					$i++;
				}
			}
			else{
				?>
				<tr>
					<td colspan="3" class="text-center">No Data Available</td>
				</tr>
				<?php
			}
		}

		

    }

	public function authenticate_popup_entry(){
		if(isset($_POST)){
			extract($_POST);
			$clinic_id = $this->session->userdata('clinic_id');
			$labReportsInfo = $this->Generic_model->getSingleRecord('lab_patient_reports', array('lab_patient_report_id'=>$lab_patient_report_id));
			// $lineItemsInfo = $this->Generic_model->getSingleRecord('billing_line_items', array('billing_line_item_id'=>$billing_line_item_id));
			if(count($labReportsInfo) > 0){
				$invInfo = $this->db->query("select * from clinic_lab_packages cp,clinic_lab_package_line_items cpl where cp.clinic_lab_package_id=cpl.clinic_lab_package_id and cp.clinic_lab_package_id='".$labReportsInfo->clinic_lab_package_id."' order by cpl.positions asc")->result();
				// echo $this->db->last_query();
				
				if(count($invInfo) > 0){
					?>
						<div class="row">
							<div class="col-12 p-0">
								<h3 class="text-center m-0 text-uppercase font-weight-bold">
									<button class="bg-danger close pull-right rounded-circle text-white" style="height:30px;width:30px;padding:0px !important" data-dismiss="modal">&times;</button><u><?=$invInfo[0]->package_name?></u>
								</h3>
							</div>
						</div>
					<?php
					$last_heading = "";
					foreach($invInfo as $value){
						
						if($value->investigation_name == ""){
							?>
							<div class="row mb-2">
								<div class="col-12">
									<?php
									if($value->g_e == "1"){
										?>
										<input type="text" name="patient_report_line_item_value[]" value="<?=$this->getLabPatientReports($value->clinic_lab_package_line_item_id,$lab_patient_report_id,'patient_package_line_item_value')?>" data-value="<?=$value->clinic_lab_package_line_item_id?>" class="form-control patient_report_line_item_value">
										<?php
									}
									elseif($value->g_e == "2"){
										?>
										<textarea data-value="<?=$value->clinic_lab_package_line_item_id?>" class="form-control summernote patient_report_line_item_value" rows="5"><?=$this->getLabPatientReports($value->clinic_lab_package_line_item_id,$lab_patient_report_id,'patient_package_line_item_value')?></textarea>
										<?php
									}
									elseif($value->g_e == "3"){
										$opt = explode(";", $value->dropdowns);
										?>
										<div class="col-6">
											<select name="patient_report_line_item_value[]" data-value="<?=$value->clinic_lab_package_line_item_id?>" class="custom-select patient_report_line_item_value">
											<?php
											foreach($opt as $val){
												?>
												<option value="<?=trim($val)?>" <?=($this->getLabPatientReports($value->clinic_lab_package_line_item_id,$lab_patient_report_id,'patient_package_line_item_value') == trim($val)?'selected':'')?>><?=trim($val)?></option>
												<?php
											}
											?>
											</select>
										</div>
										<div class="col-2">
											<h5 class="m-0 mt-2 p-0"><?=$value->units?></h5>
										</div>
										<div class="col-4">
											<h5 class="m-0 ml-4 mt-2 p-0"><?=$min_max?></h5>
										</div>
										<?php
									}
									elseif($value->g_e == "4"){
										if($value->inv_right != ""){
											?>
											<input type="text" name="patient_report_line_item_value[]" value="<?=$this->getLabPatientReports($value->clinic_lab_package_line_item_id,$lab_patient_report_id,'patient_inv_right')?>" data-value="<?=$value->clinic_lab_package_line_item_id?>" class="form-control patient_report_right">
											<?php
										}
										if($value->inv_left != ""){
											?>
											<input type="text" name="patient_report_line_item_value[]" value="<?=$this->getLabPatientReports($value->clinic_lab_package_line_item_id,$lab_patient_report_id,'patient_inv_left')?>" data-value="<?=$value->clinic_lab_package_line_item_id?>" class="form-control patient_report_left">
											<?php
										}
									}
									elseif($value->g_e == "43"){
										$rht = explode(";", $value->inv_right);
										$lft = explode(";", $value->inv_left);
										?>
										<div class="row">
											<div class="col-3">&nbsp;</div>
											<div class="col-9">
												<div class="row">
													<div class="col-6">
														<select name="patient_report_line_item_value[]" data-value="<?=$value->clinic_lab_package_line_item_id?>" class="custom-select patient_report_right">
														<?php
														foreach($rht as $rht_val){
															?>
															<option value="<?=trim($rht_val)?>" <?=($this->getLabPatientReports($value->clinic_lab_package_line_item_id,$lab_patient_report_id,'patient_inv_right') == trim($rht_val)?'selected':'')?>><?=trim($rht_val)?></option>
															<?php
														}
														?>
														</select>
													</div>
													<div class="col-6">
														<select name="patient_report_line_item_value[]" data-value="<?=$value->clinic_lab_package_line_item_id?>" class="custom-select patient_report_left">
														<?php
														foreach($lft as $lft_val){
															?>
															<option value="<?=trim($lft_val)?>" <?=($this->getLabPatientReports($value->clinic_lab_package_line_item_id,$lab_patient_report_id,'patient_inv_left') == trim($lft_val)?'selected':'')?>><?=trim($lft_val)?></option>
															<?php
														}
														?>
														</select>
													</div>
												</div>
											</div>
										</div>
										<?php
									}
									?>
								</div>
							</div>
							<?php
						}
						else{
							?>
							<div class="row mb-2">
							<?php
								$heading = explode(";", $value->heading);
								if(count($heading) > 1){
									?>
									<div class="col-4">&nbsp;</div>
									<div class="col-4">
										<h5 class="font-weight-bold m-0 mt-2 mb-2"><u><?=$heading[0]?></u></h5>
									</div>
									<div class="col-4">
										<h5 class="font-weight-bold m-0 mt-2 mb-2"><u><?=$heading[1]?></u></h5>									
									</div>
									<?php
								}
								else{
									if(trim($heading[0]) != ""){
										if(trim($heading[0]) != $last_heading){
	
											?>
											<div class="col-4">
												<h5 class="font-weight-bold m-0 mt-2 mb-2"><u><?=$value->heading?></u></h5>
											</div>
											<?php
											if($value->inv_right != ""){
												?>
												<div class="col-4">
													<h5 class="font-weight-bold m-0 mt-2 mb-2"><u>Right</u></h5>
												</div>
												<?php
											}
											if($value->inv_left != ""){
												?>
												<div class="col-4">
													<h5 class="font-weight-bold m-0 mt-2 mb-2"><u>Left</u></h5>
												</div>
												<?php
											}
										}
									}
								}
								?>
							</div>
							<div class="row mb-2">
								<div class="col-3">
									<h5 class="m-0 mt-2"><?=$value->investigation_name?></h5>
								</div>
								<div class="col-9">
									<div class="row">
									<?php
									$min_max = "";
									if($value->normal_range != ""){
										$min_max = $value->normal_range;
									}
									if($value->g_e == "1"){
										?>
										<div class="col-6">
											<input type="text" name="patient_report_line_item_value[]" value="<?=$this->getLabPatientReports($value->clinic_lab_package_line_item_id,$lab_patient_report_id,'patient_package_line_item_value')?>" data-value="<?=$value->clinic_lab_package_line_item_id?>" class="form-control patient_report_line_item_value">
										</div>
										<div class="col-2">
											<h5 class="m-0 mt-2 p-0"><?=$value->units?></h5>
										</div>
										<div class="col-4">
											<h5 class="m-0 ml-4 mt-2 p-0"><?=$min_max?></h5>
										</div>
										<?php
									}
									elseif($value->g_e == "2"){
										?>
										<div class="col-12">
											<textarea data-value="<?=$value->clinic_lab_package_line_item_id?>" class="form-control summernote patient_report_line_item_value" rows="5"><?=$this->getLabPatientReports($value->clinic_lab_package_line_item_id,$lab_patient_report_id,'patient_package_line_item_value')?></textarea>
										</div>
										<!-- <div class="col-2">
											<h5 class="m-0 mt-2 p-0"><?=$value->units?></h5>
										</div>
										<div class="col-4">
											<h5 class="m-0 ml-4 mt-2 p-0"><?=$min_max?></h5>
										</div> -->
										<?php
									}
									elseif($value->g_e == "3"){
										$opt = explode(";", $value->dropdowns);
										?>
										<div class="col-6">
											<select name="patient_report_line_item_value[]" data-value="<?=$value->clinic_lab_package_line_item_id?>" class="custom-select patient_report_line_item_value">
											<?php
											foreach($opt as $val){
												?>
												<option value="<?=trim($val)?>" <?=($this->getLabPatientReports($value->clinic_lab_package_line_item_id,$lab_patient_report_id,'patient_package_line_item_value') == trim($val)?'selected':'')?>><?=trim($val)?></option>
												<?php
											}
											?>
											</select>
										</div>
										<div class="col-2">
											<h5 class="m-0 mt-2 p-0"><?=$value->units?></h5>
										</div>
										<div class="col-4">
											<h5 class="m-0 ml-4 mt-2 p-0"><?=$min_max?></h5>
										</div>
										<?php
									}
									elseif($value->g_e == "4"){
										if($value->inv_right != ""){
											?>
											<div class="col-6">
												<input type="text" name="patient_report_line_item_value[]" value="<?=$this->getLabPatientReports($value->clinic_lab_package_line_item_id,$lab_patient_report_id,'patient_inv_right')?>" data-value="<?=$value->clinic_lab_package_line_item_id?>" class="form-control patient_report_right">
											</div>
											<?php
										}
										if($value->inv_left != ""){
											?>
											<div class="col-6">
												<input type="text" name="patient_report_line_item_value[]" value="<?=$this->getLabPatientReports($value->clinic_lab_package_line_item_id,$lab_patient_report_id,'patient_inv_left')?>" data-value="<?=$value->clinic_lab_package_line_item_id?>" class="form-control patient_report_left">
											</div>
											<?php
										}
									}
									elseif($value->g_e == "43"){
										$rht = explode(";", $value->inv_right);
										$lft = explode(";", $value->inv_left);
										?>
										<div class="col-6">
											<select name="patient_report_line_item_value[]" data-value="<?=$value->clinic_lab_package_line_item_id?>" class="custom-select patient_report_right">
											<?php
											foreach($rht as $rht_val){
												?>
												<option value="<?=trim($rht_val)?>" <?=($this->getLabPatientReports($value->clinic_lab_package_line_item_id,$lab_patient_report_id,'patient_inv_right') == trim($rht_val)?'selected':'')?>><?=trim($rht_val)?></option>
												<?php
											}
											?>
											</select>
										</div>
										<div class="col-6">
											<select name="patient_report_line_item_value[]" data-value="<?=$value->clinic_lab_package_line_item_id?>" class="custom-select patient_report_left">
											<?php
											foreach($lft as $lft_val){
												?>
												<option value="<?=trim($lft_val)?>" <?=($this->getLabPatientReports($value->clinic_lab_package_line_item_id,$lab_patient_report_id,'patient_inv_left') == trim($lft_val)?'selected':'')?>><?=trim($lft_val)?></option>
												<?php
											}
											?>
											</select>
										</div>
										<?php
									}
									?>
									</div>
								</div>
							</div>
							<?php
						}
						$last_heading = $value->heading;
					}
					?>
					<div class="row">
                        
						<div class="col-12 text-center my-3">
                            <p class="text-center">
                                <input id="auth_<?=$lab_patient_report_id?>" class="authCheck" type="checkbox">
                                <label for="auth_<?=$lab_patient_report_id?>" class="ml-2">I authenticate all the above results</label>
                            </p>    
							<button class="btn btn-app authenticate_report" data-value="<?=$lab_patient_report_id?>">Authenticate Report</button>
						</div>
					</div>
					<?php
				}	
				else{
					?>
					<h4 class='text-center p-5 m-0'>No Investigations Found for this package</h4>
					<?php
				}
			}
		}
	}

	public function LabInvoice($id)
	{

		$data['clinic_id'] = $clinic_id = $this->session->userdata('clinic_id');
		$data['clinic_information'] = $this->db->select("*")->from("clinics")->where("clinic_id", $clinic_id)->get()->row();

		//    $clinic_id = $this->session->userdata("clinic_id");
		$data['billingInvoiceInfo'] = $this->db->select("*")->from("billing_invoice")->where("billing_invoice_id", $id)->get()->row();
		// echo $this->db->last_query();
		$data['billingInfo'] = $this->db->select("*")->from("billing")->where("billing_id", $data['billingInvoiceInfo']->billing_id)->get()->row();
		$data['billingInvoice'] = $this->db->select("*")->from("billing_invoice")->where("billing_id", $data['billingInvoiceInfo']->billing_id)->get()->row();
		$data['billingInvoice_records'] = $this->db->select("*")->from("billing_invoice")->where("billing_id", $data['billingInvoiceInfo']->billing_id)->get()->result();
		$data['billingLineItemsInfo'] = $this->db->select("*")->from("billing_line_items")->where("billing_id", $data['billingInvoiceInfo']->billing_id)->get()->result();
		$data['patientInfo'] = $this->db->select("*")->from("patients")->where("patient_id", $data['billingInfo']->patient_id)->get()->row();
		$clinic_id = $data['billingInfo']->clinic_id;
		if ($data['billingInfo']->doctor_id != 0) {
			$data['docInfo'] = $this->db->query("select d.*,de.department_name from doctors d,department de where d.department_id=de.department_id and d.doctor_id='" . $data['billingInfo']->doctor_id . "'")->row();
			$data['clinicDocInfo'] = $this->db->select("*")->from("clinic_doctor")->where("clinic_id", $clinic_id)->where("doctor_id", $data['billingInfo']->doctor_id)->get()->row();
		}
		$data['clinicInfo'] = $this->db->select("*")->from("clinics")->where("clinic_id", $clinic_id)->get()->row();
		//    $data['view'] = "pdfViews/LabInvoice";
		// //    $this->load->view('pdfViews/pdfLayout',$data);
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";

		$this->load->library('M_pdf');
		$html = $this->load->view('LabNew/billing_pdf', $data, true);
		$pdfFilePath = $data['billingInvoiceInfo']->invoice_no . ".pdf";
		$stylesheet = file_get_contents(base_url() . "assets/css/bootstrap.min.css"); // external css
		$stylesheet .= file_get_contents(base_url() . "assets/css/pdf.css");
		$this->m_pdf->pdf->autoScriptToLang = true;
		$this->m_pdf->pdf->autoLangToFont = true;

		$this->m_pdf->pdf->shrink_tables_to_fit = 1;
		$this->m_pdf->pdf->setAutoTopMargin = "stretch";
		$this->m_pdf->pdf->setAutoBottomMargin = "stretch";
		$this->m_pdf->pdf->defaultheaderline = 0;

		$this->m_pdf->pdf->WriteHTML($stylesheet, 1);
		$this->m_pdf->pdf->WriteHTML($html, 2);
		$this->m_pdf->pdf->Output("./uploads/lab_reports/" . $pdfFilePath, "F");

		redirect("uploads/lab_reports/" . $pdfFilePath);
	}

	public function investigation_price($billing_id)
	{
		$clinic_id = $this->session->userdata('clinic_id');

		// $billing_invoice = $this->db->query("SELECT * FROM billing_invoice WHERE billing_id='" . $billing_id . "'")->result();
		// echo "<pre>";
		// print_r($billing_invoice);
		// echo "</pre>";
		$billMasterInfo  = $this->Generic_model->getSingleRecord('billing', array('billing_id' => $billing_id));
		// echo $this->db->last_query();
		$bill = $this->db->query("SELECT SUM(amount)as total_amount FROM billing_line_items where billing_id='" . $billing_id . "'")->row();
		// echo $billMasterInfo->discount . $billMasterInfo->discount_unit;

		$amount = $bill->total_amount - (($bill->total_amount * $billMasterInfo->discount . $billMasterInfo->discount_unit) / 100);

		
		// if($billMasterInfo->osa > "0.00")
		// {
		// 	$osadata = $billMasterInfo->billing_id."*$".$billMasterInfo->osa;
		// 	$osa_btn = '<button class="btn btn-app btn-xs clearosa" data-toggle="modal" data-target="#osaModal" data-value="'.$osadata.'">Clear OSA</button> ';
		// }
		// else{
		// 	$osa_btn = "";
		// }

		if($billMasterInfo->osa == 0){
			// $status = '<div class="paid">Paid</div>';
		}
		else{
			$osadata = $billMasterInfo->billing_id."*$".$billMasterInfo->osa;
			$status = '<button class="btn btn-danger btn-block btn-xs clearosa" data-toggle="modal" data-target="#osaModal" data-value="'.$osadata.'">Clear Balance</button> ';
			// $status = '<button class="btn btn-danger btn-block btn-xs">Clear Balance</button>';
		}

		// print_r($bill->total_amount);

		// // return  "<h5 class='font-weight-bold text-primary'>fire&nbsp;&#8377;</h5><br>";
		// $result = $this->db->query("SELECT investigation_id,billing_line_item_id FROM `billing_line_items` WHERE billing_id='" . $billing_id . "'")->result();

		// $output = "";

		// foreach ($result as $res) {

		// 	$sec_result = $this->db->query("SELECT amount FROM `billing_line_items` WHERE investigation_id='" . $res->investigation_id . "' AND billing_line_item_id='" . $res->billing_line_item_id . "'")->result();

		// foreach ($sec_result as $sec) {
		if($billMasterInfo->discount != 0){
			$disc = "<small class='font-weight-bold font-italic text-primary'>( INCL . DISC - ". $billMasterInfo->discount." ".$billMasterInfo->discount_unit." )</small>";
		}
		else{
			$disc = "";
		}
		$output = "<h5 class='font-weight-bold text-center'><i class='fas fa-rupee-sign'></i> " . $amount . "/-  ".$disc."</h5>".$status;
		// 	}
		// }

		return $output;
	}

	public function delete_order()
	{

		$billing_id = $_POST['order_id'];

		$billing_array = $this->db->query("SELECT * FROM billing_line_items WHERE billing_id='" . $billing_id . "'")->result();

		foreach ($billing_array as $ary) {

			$this->db->query("DELETE FROM billing_line_items WHERE billing_line_item_id='" . $ary->billing_line_item_id . "'");
		}

		$this->db->query("DELETE FROM billing WHERE billing_id='" . $billing_id . "'");
	}

	public function ClearOsa(){
		extract($_POST);
		$clinic_id = $this->session->userdata('clinic_id');
		$lab_invoice = generate_billing_invoice_no($clinic_id);
		$billInfo = $this->Generic_model->getSingleRecord('billing', array('billing_id'=>$osa_billing_id));
		$data['osa'] = "0.00";
		$this->Generic_model->updateData('billing', $data, array('billing_id'=>$osa_billing_id));
		$billingInvoice['billing_id'] = $osa_billing_id;
		$billingInvoice['invoice_no'] = $clinic_id . $lab_invoice;
		$billingInvoice['invoice_no_alias'] = $lab_invoice;
		$billingInvoice['invoice_date'] = date('Y-m-d');
		$billingInvoice['invoice_amount'] = $billInfo->osa;
		$billingInvoice['payment_type'] = "OSA";
		$billingInvoice['payment_mode'] = "cash";
		$billingInvoice['status'] = 1;
		$billingInvoice['created_by'] = 1;
		$billingInvoice['created_date_time'] = date('Y-m-d H:i:s');
		$billingInvoice['modified_by'] = 1;
		$billingInvoice['modified_date_time'] = date('Y-m-d H:i:s');
		$bin_id = $this->Generic_model->insertDataReturnId('billing_invoice', $billingInvoice);
		echo "1";
	}

	public function orders()
	{
		extract($_POST);

		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));
		$clinic_id = $this->session->userdata('clinic_id');
		if($start_date == $end_date){
			$cond = "DATE(created_date_time) LIKE '".$start_date."%'";
		}
		else{
			$cond = "DATE(created_date_time) BETWEEN '".$start_date."%' and '".$end_date."%'";
		}

		$query = $this->db->get("billing where billing_type='lab' AND clinic_id='" . $clinic_id . "' and ".$cond." ORDER BY `billing`.`billing_id` DESC");
		
		$data = [];

		$i = 1;
		// $btns='<button style="background-color: Transparent; background-repeat:no-repeat; border: none;cursor:pointer;  color:#10367a;"><i class="fas fa-vial icon icon fa-2x"></i></button> | <button style="background-color: Transparent; background-repeat:no-repeat; border: none;cursor:pointer;  color:#10367a;"><i class="fas fa-sync-alt fa-2x"></i></button> | <button type="button" data-toggle="modal" data-target="#report_entry_popup" id="cam_btn" style="background-color: Transparent; background-repeat:no-repeat; border: none;cursor:pointer;  color:#10367a;"><i class="far fa-file-alt fa-2x"></i></button> |  <button style="background-color: Transparent; background-repeat:no-repeat; border: none;cursor:pointer;  color:#10367a;"><i class="fas fa-file-signature fa-2x"></i></button>';
		foreach ($query->result() as $r) {

			if($r->osa > "0.00")
			{
				$osadata = $r->billing_id."*$".$r->osa;
				$osa_btn = '<button class="btn btn-app btn-xs clearosa" data-toggle="modal" data-target="#osaModal" data-value="'.$osadata.'">Clear OSA</button> ';
			}
			else{
				$osa_btn = "";
			}
			$mob = DataCrypt($r->guest_mobile, 'decrypt');
			$printBtn = '<a data-toggle="modal" data-target="#invoiceModal" class="getInvoiceList mr-2" data-value="'.$r->billing_id.'"><i class="fas fa-file-invoice-dollar text-primary"></i></a>';

			$billIds = $this->db->query("select group_concat(billing_line_item_id) as ids from billing_line_items where billing_id='".$r->billing_id."'")->row();
			$checkDelStatus = $this->db->query("select * from lab_patient_reports where billing_line_item_id IN (".$billIds->ids.")")->row();
			if(count($checkDelStatus) > 0){
				if($checkDelStatus->status > 0){
					$delBtn = "";
				}
				else{
					$delBtn = '<i class="fas fa-trash delOrder text-danger mr-1" data-toggle="tooltip" data-placement="top" title="Delete total record" class="" data-value=' . $r->billing_id . ' ></i>';
				}
			}
			else{
				$delBtn = '<i class="fas fa-trash delOrder text-danger mr-1" data-toggle="tooltip" data-placement="top" title="Delete total record" class="" data-value=' . $r->billing_id . ' ></i>';
			}

			$data[] = array(

				$i,
				"<span class='trade_name'>" . $r->guest_name ."</span><p class='p-0 m-0'>". $mob . "</p>",

				$this->investigation_names($r->billing_id),

				$this->icons($r->billing_id),

				// "this is price",
				$this->investigation_price($r->billing_id),
				
				'<p class="m-0 p-0 icons">'.$printBtn.$delBtn.'</p>'
			);

			$i++;
		}

		$result = array(
			"draw" => $draw,
			"start" => $start,
			"length" => $length,
			"recordsTotal" => $query->num_rows(),
			"recordsFiltered" => $query->num_rows(),
			"data" => $data
		);

		echo json_encode($result);
	}

	// patient investigation ajax api

	public function patient_investigations()
	{
		$draw = intval($this->input->get("draw"));
		$start = intval($this->input->get("start"));
		$length = intval($this->input->get("length"));
		$clinic_id = $this->session->userdata('clinic_id');

		$query = $this->db->get("billing where billing_type='lab' AND clinic_id='" . $clinic_id . "'");
		$data = [];
		$i = 1;
		foreach ($query->result() as $r) {

			$data[] = array(
				$i,
				$r->guest_name,
				$r->guest_mobile,
				$m = '<button onclick="delete_patient_investigaton(this.value)" value=' . "$r->billing_id" . ' style="background-color: Transparent; background-repeat:no-repeat; border: none;cursor:pointer;  color:#f23c00;"><i class="fas fa-trash fa-1x"></i></button>'
			);
			$i++;
		}

		$result = array(
			"draw" => $draw,
			"recordsTotal" => $query->num_rows(),
			"recordsFiltered" => $query->num_rows(),
			"data" => $data
		);
		echo json_encode($result);
	}

	public function getInvoices(){
		extract($_POST);
		$check = $this->Generic_model->getAllRecords('billing_invoice', array('billing_id'=>$billing_id));
		if(count($check) > 0){
			$i = 1;
			foreach($check as $val){
				?>
				<tr>
					<td><?=$i?></td>
					<td><span><?=$val->invoice_no_alias?></span></td>
					<td><?=date('d-m-Y', strtotime($val->invoice_date))?></td>
					<td>
						<a href="<?=base_url('LabNew/LabInvoice/'.$val->billing_invoice_id)?>" target="blank"><i class="fas fa-print"></i></a>
					</td>
				</tr>
				<?php
				$i++;
			}
		}
		else{
			?>
			<tr>
				<td colspan="4" class="text-center">No Records Found.</td>
			</tr>
			<?php
		}
	}


	public function clinic_Investigation_search()
	{

		$clinic_id = $this->session->userdata('clinic_id');

		$search_key = $_POST['search'];

		$searchData = $this->db->query("SELECT * FROM clinic_lab_packages WHERE clinic_id='" . $clinic_id . "' AND package_name LIKE '%" . $search_key . "%' LIMIT 10")->result();

		echo json_encode($searchData);
	}

	public function delete_patient_investigatons()
	{
		extract($_POST);
		$this->db->query("DELETE FROM billing_line_items where billing_id='" . $patient_inv_id . "'");
		$this->db->query("DELETE FROM billing WHERE billing_id='" . $patient_inv_id . "'");
	}

	public function add_patient_investigation()
	{
		$clinic_id = $this->session->userdata('clinic_id');
		extract($_POST);

		// echo "<pre>";
		$invNo = generate_invoice_no($clinic_id);
		$lab_invoice = generate_billing_invoice_no($clinic_id);
		$count = count($_POST['inv_data']);

		for ($i = 0; $i < $count; $i++) {
			$totAmt += $_POST['inv_data'][$i]['price'];
		}

		
			// if(($totAmt-$toPay) <= 0){
			// 	$osa = 0.00;
			// }
			// else{
			// 	$osa = $totAmt-$toPay;
			// }
		
		// print_r($_POST);
		// exit();

		$billing['invoice_no'] = $clinic_id . $invNo;
		$billing['invoice_no_alias'] = $invNo;
		$billing['clinic_id'] = $this->session->userdata('clinic_id');
		$billing['billing_type'] = 'Lab';
		$billing['discount'] = $discount;
		$billing['total_amount'] = $totAmt;
		$billing['discount_unit'] = "%";
		$billing['osa'] = $osa;
		$billing['guest_name'] = $patient_name;
		$billing['guest_mobile'] = DataCrypt($patient_number, 'encrypt');
		$billing['age'] = $patient_age;
		$billing['age_unit'] = $age_unit;
		$billing['email'] = $email;
		$billing['gender'] = $patient_gender;
		$billing['referred_by'] = $referred_by;
		$billing['billing_date_time'] = date('Y-m-d H:i:s');
		$billing['created_by'] = $this->session->userdata('user_id');
		$billing['created_date_time'] = date('Y-m-d H:i:s');
		$billing['modified_by'] = $this->session->userdata('user_id');
		$billing['modified_date_time'] = date('Y-m-d H:i:s');
		// echo "<pre>";
		// print_r($billing);
		// echo "</pre>";
		// exit;

		$bid = $this->Generic_model->insertDataReturnId('billing', $billing);

		for ($x = 0; $x < $count; $x++) {

			// $billing_line_items['item_information'] = "hai ra bava";
			$billing_line_items['item_information'] = $_POST['inv_data'][$x]['name'];
			$billing_line_items['investigation_id'] = $_POST['inv_data'][$x]['id'];
			$billing_line_items['billing_id'] =	$bid;
			$billing_line_items['amount'] = $_POST['inv_data'][$x]['price'];
			$billing_line_items['discount'] = $discount;
			$billing_line_items['discount_unit'] = "%";


			$this->Generic_model->insertDataReturnId('billing_line_items', $billing_line_items);
		}

		$billingInvoice['billing_id'] = $bid;
		$billingInvoice['invoice_no'] = $clinic_id . $lab_invoice;
		$billingInvoice['invoice_no_alias'] = $lab_invoice;
		$billingInvoice['invoice_date'] = date('Y-m-d');
		$billingInvoice['invoice_amount'] = $toPay;
		if ($advance_check == 1) {
			$billingInvoice['payment_type'] = "Advance";
		} else {
			$billingInvoice['payment_type'] = "Net";
		}
		$billingInvoice['payment_mode'] = "cash";
		$billingInvoice['status'] = 1;
		$billingInvoice['created_by'] = 1;
		$billingInvoice['created_date_time'] = date('Y-m-d H:i:s');
		$billingInvoice['modified_by'] = 1;
		$billingInvoice['modified_date_time'] = date('Y-m-d H:i:s');
		$bin_id = $this->Generic_model->insertDataReturnId('billing_invoice', $billingInvoice);


		// echo "</pre>";

		$clinic_inv_id = $this->db->query("SELECT package_id FROM `clinic_lab_packages` WHERE package_id='" . $inv_id . "'")->row();

		$package_id = $clinic_inv_id->package_id;

		// $billing_line_items['billing_id']=$bid;
		// $billing_line_items['item_information']=$bid;
		// $billing_line_items['clinic_investigation_id']=$inv_id;
		echo $bin_id;

		// redirect('LabNew/total_orders');
	}








	public function add_paramters()
	{

		// echo "<pre>";
		// print_r($_POST);
		// echo "<pre>";
		// exit;
		extract($_POST);

		$package_line_items = $this->db->query("SELECT * FROM clinic_lab_package_line_items where clinic_lab_package_id='" .  $_POST['package_id'] . "'")->result();

		$line_items['clinic_id'] = $this->session->userdata('clinic_id');
		$line_items['clinic_lab_package_id'] = $_POST['package_id'];
		$line_items['investigation_name'] = $_POST['investigation_name'];
		$line_items['units'] = $_POST['unit'];
		$line_items['content'] = $_POST['content'];
		$line_items['normal_range'] = $_POST['range'];
		$line_items['lab_package_line_item_id'] = 0;
		$line_items['g_e'] = 1;
		$line_items['package_id'] = $package_id;
		$line_items['positions'] = count($package_line_items)+1;
		// $line_items['min_value'] = $_POST['min_value'];
		// $line_items['max_value'] = $_POST['max_value'];
		// $line_items['comments'] = $_POST['comments'];
		// $line_items['gender'] = $_POST['gender'];
		// $line_items['remarks'] = $_POST['remarks'];
		// $line_items['content'] = $_POST['content'];

		$id = $this->Generic_model->insertDataReturnId('clinic_lab_package_line_items', $line_items);
		echo $id;
		exit();
		
	}

	public function delete_package_line_items()
	{
		extract($_POST);

		$clinic_id = $this->session->userdata('clinic_id');

		$this->db->query("DELETE FROM clinic_lab_package_line_items WHERE clinic_lab_package_line_item_id ='" . $clinic_lab_package_line_item_id . "' AND clinic_id ='" . $clinic_id . "'");
		echo $this->db->last_query();
	}


	public function delete_billing_line_items()
	{
		extract($_POST);

		$billing_items = $this->db->query("select * from billing_line_items where billing_line_item_id='" . $billing_line_item_id . "'")->row();

		$billing_details = $this->db->query("select * from billing_line_items where billing_id='" . $billing_items->billing_id . "'")->result();

		if (count($billing_details) > 1) {

			$this->db->query("DELETE FROM billing_line_items WHERE billing_line_item_id ='" . $billing_line_item_id . "'");
		} else {
			echo 1;
		}
	}


	public function popup_report_entry()
	{

		extract(($_POST));

		$clinic_id = $this->session->userdata('clinic_id');

		$patient_report_details = $this->db->query("SELECT * FROM lab_patient_reports INNER JOIN lab_patient_report_line_items ON lab_patient_reports.lab_patient_report_id=lab_patient_report_line_items.lab_patient_report_id AND lab_patient_reports.billing_line_item_id='" . $billing_line_item_id . "' and lab_patient_reports.clinic_id='" . $clinic_id . "' ")->result();

		$masterr = $this->db->query("SELECT * FROM clinic_lab_packages cl, clinic_lab_package_line_items cli where cl.clinic_lab_package_id=cli.clinic_lab_package_id and cl.package_id='".$package_id."' and cl.clinic_id='".$clinic_id."'")->result();

		$i = 0;
		if (count($masterr) > 0) {

			foreach ($masterr as $mater) {

				$reports[$i]['Name_of_Template'] = $mater->Name_of_Template;
				$reports[$i]['No_of_Templets'] = $mater->No_of_Templets;
				$reports[$i]['clinic_id'] = $mater->clinic_id;
				$reports[$i]['clinic_lab_package_id'] = $mater->clinic_lab_package_id;
				$reports[$i]['clinic_lab_package_line_item_id'] = $mater->clinic_lab_package_line_item_id;
				$reports[$i]['code'] = $mater->code;
				$reports[$i]['comments'] = $mater->comments;
				$reports[$i]['content'] = $mater->content;
				$reports[$i]['count'] = $mater->count;
				$reports[$i]['dropdowns'] = $mater->dropdowns;
				$reports[$i]['excel_word'] = $mater->excel_word;
				$reports[$i]['g_e'] = $mater->g_e;
				$reports[$i]['gender'] = $mater->gender;
				$reports[$i]['heading'] = $mater->heading;
				$reports[$i]['investigation_name'] = $mater->investigation_name;
				$reports[$i]['lab_package_line_item_id'] = $mater->lab_package_line_item_id;
				$reports[$i]['max_value'] = $mater->max_value;
				$reports[$i]['mian_heading'] = $mater->mian_heading;
				$reports[$i]['min_max_units'] = $mater->min_max_units;
				$reports[$i]['min_value'] = $mater->min_value;
				$reports[$i]['package_id'] = $mater->package_id;
				$reports[$i]['positions'] = $mater->positions;
				$reports[$i]['price'] = $mater->price;
				$reports[$i]['remarks'] = $mater->remarks;
				$reports[$i]['specimen'] = $mater->specimen;
				$reports[$i]['units'] = $mater->units;

				$reports[$i]['billing_line_item_id'] = $patient_report_details[$i]->billing_line_item_id;
				// $reports[$i]['clinic_lab_package_line_item_id'] = $patient_report_details[$i]->clinic_lab_package_line_item_id;
				$reports[$i]['lab_patient_report_id'] = $patient_report_details[$i]->lab_patient_report_id;
				$reports[$i]['lab_patient_report_line_item_id'] = $patient_report_details[$i]->lab_patient_report_line_item_id;
				$reports[$i]['patient_package_line_item_value'] = $patient_report_details[$i]->patient_package_line_item_value;
				$reports[$i]['patient_package_name'] = $patient_report_details[$i]->patient_package_name;

				$i++;
			}
			echo json_encode($reports);
		} else {
			$reports = [];
			echo json_encode($reports);
		}
	}

	// public function authenticate_popup_entry()
	// {

	// 	extract(($_POST));

	// 	$clinic_id = $this->session->userdata('clinic_id');

	// 	$patient_report_details = $this->db->query("select * from lab_patient_reports lpr, lab_patient_report_line_items lprli where lpr.lab_patient_report_id=lprli.lab_patient_report_id and lpr.lab_patient_report_id='".$lab_patient_report_id."'")->result();
	// 	// echo $this->db->last_query();

	// 	if (count($patient_report_details) > 0) {
	// 		$i = 0;

	// 		foreach ($patient_report_details as $value) {

	// 			$labLineItemsInfo = $this->db->query("select * from clinic_lab_packages cp,clinic_lab_package_line_items cpi where cp.clinic_lab_package_id=cpi.clinic_lab_package_id and cpi.clinic_lab_package_line_item_id='".$value->clinic_lab_package_line_item_id."'")->row();

	// 			$reports[$i]['Name_of_Template'] = $labLineItemsInfo->Name_of_Template;
	// 			$reports[$i]['No_of_Templets'] = $labLineItemsInfo->No_of_Templets;
	// 			$reports[$i]['clinic_id'] = $labLineItemsInfo->clinic_id;
	// 			$reports[$i]['clinic_lab_package_id'] = $labLineItemsInfo->clinic_lab_package_id;
	// 			$reports[$i]['clinic_lab_package_line_item_id'] = $labLineItemsInfo->clinic_lab_package_line_item_id;
	// 			$reports[$i]['code'] = $labLineItemsInfo->code;
	// 			$reports[$i]['comments'] = $labLineItemsInfo->comments;
	// 			$reports[$i]['content'] = $labLineItemsInfo->content;
	// 			$reports[$i]['count'] = $labLineItemsInfo->count;
	// 			$reports[$i]['dropdowns'] = $labLineItemsInfo->dropdowns;
	// 			$reports[$i]['excel_word'] = $labLineItemsInfo->excel_word;
	// 			$reports[$i]['g_e'] = $labLineItemsInfo->g_e;
	// 			$reports[$i]['gender'] = $labLineItemsInfo->gender;
	// 			$reports[$i]['heading'] = $labLineItemsInfo->heading;
	// 			$reports[$i]['investigation_name'] = $labLineItemsInfo->investigation_name;
	// 			$reports[$i]['lab_package_line_item_id'] = $labLineItemsInfo->lab_package_line_item_id;
	// 			$reports[$i]['max_value'] = $labLineItemsInfo->max_value;
	// 			$reports[$i]['mian_heading'] = $labLineItemsInfo->mian_heading;
	// 			$reports[$i]['min_max_units'] = $labLineItemsInfo->min_max_units;
	// 			$reports[$i]['min_value'] = $labLineItemsInfo->min_value;
	// 			$reports[$i]['package_id'] = $labLineItemsInfo->package_id;
	// 			$reports[$i]['positions'] = $labLineItemsInfo->positions;
	// 			$reports[$i]['price'] = $labLineItemsInfo->price;
	// 			$reports[$i]['remarks'] = $labLineItemsInfo->remarks;
	// 			$reports[$i]['specimen'] = $labLineItemsInfo->specimen;
	// 			$reports[$i]['units'] = $labLineItemsInfo->units;

	// 			$reports[$i]['billing_line_item_id'] = $value->billing_line_item_id;
	// 			$reports[$i]['lab_patient_report_id'] = $value->lab_patient_report_id;
	// 			$reports[$i]['lab_patient_report_line_item_id'] = $value->lab_patient_report_line_item_id;
	// 			$reports[$i]['patient_package_line_item_value'] = $value->patient_package_line_item_value;
	// 			$reports[$i]['patient_package_name'] = $value->patient_package_name;

	// 			$i++;
	// 		}
	// 		echo json_encode($reports);
	// 	} else {
	// 		$reports = [];
	// 		echo json_encode($reports);
	// 	}
	// }

	public function popup_report_details()
	{
		extract($_POST);

		$patient_report_details = $this->db->query("SELECT * FROM lab_patient_reports INNER JOIN lab_patient_report_line_items ON lab_patient_reports.lab_patient_report_id=lab_patient_report_line_items.lab_patient_report_id AND lab_patient_reports.billing_line_item_id='" . $billing_line_item_id . "'")->result();

		echo json_encode($patient_report_details);
	}


	public function save_patient_lab_reports()
	{
		extract($_POST);

		// echo "<pre>";print_r($_POST);echo "</pre>";
		// exit;

		$bil = $this->db->query("SELECT * FROM billing_line_items where billing_line_item_id='" . $billing_line_item_id . "'")->row();

		$clinic_id = $this->session->userdata('clinic_id');

		$patient_report = $this->db->query("SELECT * FROM `clinic_lab_package_line_items` INNER JOIN clinic_lab_packages WHERE clinic_lab_package_line_items.clinic_lab_package_id=clinic_lab_packages.clinic_lab_package_id AND clinic_lab_package_line_item_id='" . $patient_values[0]['clinic_lab_package_line_item_id'] . "'")->row();

		$checkLabReports = $this->Generic_model->getSingleRecord('lab_patient_reports', array('clinic_lab_package_id'=>$patient_report->clinic_lab_package_id,'billing_line_item_id'=>$billing_line_item_id));

		if(count($checkLabReports) > 0){
			$lab_patient_report_id = $checkLabReports->lab_patient_report_id;
		}
		else{
			$patient_lab_report['clinic_lab_package_id'] = $patient_report->clinic_lab_package_id;
			$patient_lab_report['patient_package_name'] =  $patient_report->package_name;
			$patient_lab_report['clinic_id'] = $clinic_id;
			$patient_lab_report['billing_line_item_id'] = $bil->billing_line_item_id;
			$patient_lab_report['created_by'] = $this->session->userdata('user_id');
			$patient_lab_report['created_date_time'] = date('Y-m-d H:i:s');
			$patient_lab_report['modified_by'] = $this->session->userdata('user_id');
			$patient_lab_report['modified_date_time'] = date('Y-m-d H:i:s');
	
			$lab_patient_report_id = $this->Generic_model->insertDataReturnId('lab_patient_reports', $patient_lab_report);
		}
		$stat['status'] = 2;
		$this->Generic_model->updateData('lab_patient_reports', $stat, array('lab_patient_report_id'=>$lab_patient_report_id));
		for ($i = 0; $i < count($patient_values); $i++) {
			$patient_report_line_item['clinic_lab_package_line_item_id'] = $patient_values[$i]['clinic_lab_package_line_item_id'];
			$patient_report_line_item['patient_package_line_item_value'] = $patient_values[$i]['patient_package_line_item_value'];
			$patient_report_line_item['lab_patient_report_id'] = $lab_patient_report_id;
			$this->Generic_model->insertDataReturnId("lab_patient_report_line_items", $patient_report_line_item);
		}
		for ($i = 0; $i < count($right); $i++) {
			$checkRight = $this->Generic_model->getSingleRecord('lab_patient_report_line_items', array('lab_patient_report_id'=>$lab_patient_report_id,'clinic_lab_package_line_item_id'=>$right[$i]['clinic_lab_package_line_item_id']));
			if(count($checkRight) > 0){
				$updateRight['patient_inv_right'] = $right[$i]['patient_inv_right'];
				$this->Generic_model->updateData('lab_patient_report_line_items', $updateRight, array('lab_patient_report_line_item_id'=>$checkRight->lab_patient_report_line_item_id));
			}
			else{
				$insertRight['clinic_lab_package_line_item_id'] = $right[$i]['clinic_lab_package_line_item_id'];
				$insertRight['patient_inv_right'] = $right[$i]['patient_inv_right'];
				$insertRight['lab_patient_report_id'] = $lab_patient_report_id;
				$this->Generic_model->insertDataReturnId("lab_patient_report_line_items", $insertRight);
			}
			
		}
		for ($i = 0; $i < count($left); $i++) {
			$checkLeft = $this->Generic_model->getSingleRecord('lab_patient_report_line_items', array('lab_patient_report_id'=>$lab_patient_report_id,'clinic_lab_package_line_item_id'=>$left[$i]['clinic_lab_package_line_item_id']));
			if(count($checkLeft) > 0){
				$updateLeft['patient_inv_left'] = $left[$i]['patient_inv_left'];
				$this->Generic_model->updateData('lab_patient_report_line_items', $updateLeft, array('lab_patient_report_line_item_id'=>$checkLeft->lab_patient_report_line_item_id));
			}
			else{
				$insertLeft['clinic_lab_package_line_item_id'] = $left[$i]['clinic_lab_package_line_item_id'];
				$insertLeft['patient_inv_left'] = $left[$i]['patient_inv_left'];
				$insertLeft['lab_patient_report_id'] = $lab_patient_report_id;
				$this->Generic_model->insertDataReturnId("lab_patient_report_line_items", $insertLeft);
			}
			
		}

		
		echo "1";
	}

	public function authenticate_patient_reports()
	{
		extract($_POST);

		// echo "<pre>";print_r($_POST);echo "</pre>";
		// exit;


		$clinic_id = $this->session->userdata('clinic_id');

		$patient_report = $this->db->query("SELECT * FROM `clinic_lab_package_line_items` INNER JOIN clinic_lab_packages WHERE clinic_lab_package_line_items.clinic_lab_package_id=clinic_lab_packages.clinic_lab_package_id AND clinic_lab_package_line_item_id='" . $patient_values[0]['clinic_lab_package_line_item_id'] . "'")->row();

		$checkLabReports = $this->Generic_model->getSingleRecord('lab_patient_reports', array('lab_patient_report_id'=>$lab_patient_report_id));

		if(count($checkLabReports) > 0){
			$lab_patient_report_id = $checkLabReports->lab_patient_report_id;
		}
		else{
			$bil = $this->db->query("SELECT * FROM billing_line_items where billing_line_item_id='" . $checkLabReports->billing_line_item_id . "'")->row();
			$patient_lab_report['clinic_lab_package_id'] = $patient_report->clinic_lab_package_id;
			$patient_lab_report['patient_package_name'] =  $patient_report->package_name;
			$patient_lab_report['clinic_id'] = $clinic_id;
			$patient_lab_report['billing_line_item_id'] = $bil->billing_line_item_id;
			$patient_lab_report['created_by'] = $this->session->userdata('user_id');
			$patient_lab_report['created_date_time'] = date('Y-m-d H:i:s');
			$patient_lab_report['modified_by'] = $this->session->userdata('user_id');
			$patient_lab_report['modified_date_time'] = date('Y-m-d H:i:s');
	
			$lab_patient_report_id = $this->Generic_model->insertDataReturnId('lab_patient_reports', $patient_lab_report);
		}
		$stat['status'] = 3;
		$stat['authenticated_by'] = $this->session->userdata('user_id');
		$stat['authenticated_date_time'] = date('Y-m-d H:i:s');
		$stat['modified_by'] = $this->session->userdata('user_id');
		$stat['modified_date_time'] = date('Y-m-d H:i:s');
		$this->Generic_model->updateData('lab_patient_reports', $stat, array('lab_patient_report_id'=>$lab_patient_report_id));

		for ($i = 0; $i < count($patient_values); $i++) {
			$checkValues = $this->Generic_model->getSingleRecord('lab_patient_report_line_items', array('lab_patient_report_id'=>$lab_patient_report_id,'clinic_lab_package_line_item_id'=>$patient_values[$i]['clinic_lab_package_line_item_id']));
			if(count($checkValues) > 0){
				$updateValues['patient_package_line_item_value'] = $patient_values[$i]['patient_package_line_item_value'];
				$this->Generic_model->updateData('lab_patient_report_line_items', $updateValues, array('lab_patient_report_line_item_id'=>$checkValues->lab_patient_report_line_item_id));
			}
			else{
				$patient_report_line_item['clinic_lab_package_line_item_id'] = $patient_values[$i]['clinic_lab_package_line_item_id'];
				$patient_report_line_item['patient_package_line_item_value'] = $patient_values[$i]['patient_package_line_item_value'];
				$patient_report_line_item['lab_patient_report_id'] = $lab_patient_report_id;
				$this->Generic_model->insertDataReturnId("lab_patient_report_line_items", $patient_report_line_item);
			}
		}

		for ($i = 0; $i < count($right); $i++) {
			$checkRight = $this->Generic_model->getSingleRecord('lab_patient_report_line_items', array('lab_patient_report_id'=>$lab_patient_report_id,'clinic_lab_package_line_item_id'=>$right[$i]['clinic_lab_package_line_item_id']));
			if(count($checkRight) > 0){
				$updateRight['patient_inv_right'] = $right[$i]['patient_inv_right'];
				$this->Generic_model->updateData('lab_patient_report_line_items', $updateRight, array('lab_patient_report_line_item_id'=>$checkRight->lab_patient_report_line_item_id));
			}
			else{
				$insertRight['clinic_lab_package_line_item_id'] = $right[$i]['clinic_lab_package_line_item_id'];
				$insertRight['patient_inv_right'] = $right[$i]['patient_inv_right'];
				$insertRight['lab_patient_report_id'] = $lab_patient_report_id;
				$this->Generic_model->insertDataReturnId("lab_patient_report_line_items", $insertRight);
			}
			
		}

		for ($i = 0; $i < count($left); $i++) {
			$checkLeft = $this->Generic_model->getSingleRecord('lab_patient_report_line_items', array('lab_patient_report_id'=>$lab_patient_report_id,'clinic_lab_package_line_item_id'=>$left[$i]['clinic_lab_package_line_item_id']));
			if(count($checkLeft) > 0){
				$updateLeft['patient_inv_left'] = $left[$i]['patient_inv_left'];
				$this->Generic_model->updateData('lab_patient_report_line_items', $updateLeft, array('lab_patient_report_line_item_id'=>$checkLeft->lab_patient_report_line_item_id));
			}
			else{
				$insertLeft['clinic_lab_package_line_item_id'] = $left[$i]['clinic_lab_package_line_item_id'];
				$insertLeft['patient_inv_left'] = $left[$i]['patient_inv_left'];
				$insertLeft['lab_patient_report_id'] = $lab_patient_report_id;
				$this->Generic_model->insertDataReturnId("lab_patient_report_line_items", $insertLeft);
			}
			
		}

		
		echo "1";
	}

	// Backup of lab_patient_reports Save
	// public function save_patient_lab_reports()
	// {
	// 	extract($_POST);

	// 	echo "<pre>";print_r($_POST);echo "</pre>";
	// 	exit;

	// 	$bil = $this->db->query("SELECT * FROM billing_line_items where billing_line_item_id='" . $billing_line_item_id . "'")->row();

	// 	$clinic_id = $this->session->userdata('clinic_id');

	// 	$patient_report = $this->db->query("SELECT * FROM `clinic_lab_package_line_items` INNER JOIN clinic_lab_packages WHERE clinic_lab_package_line_items.clinic_lab_package_id=clinic_lab_packages.clinic_lab_package_id AND clinic_lab_package_line_item_id='" . $patient_report_array[0]['clinic_lab_package_line_item_id'] . "'")->row();

	// 	$checkLabReports = $this->Generic_model->getSingleRecord('lab_patient_reports', array('clinic_lab_package_id'=>$patient_report->clinic_lab_package_id,'billing_line_item_id'=>$billing_line_item_id));

	// 	if(count($checkLabReports) > 0){
	// 		$lab_patient_report_id = $checkLabReports->lab_patient_report_id;
	// 	}
	// 	else{
	// 		$patient_lab_report['clinic_lab_package_id'] = $patient_report->clinic_lab_package_id;
	// 		$patient_lab_report['patient_package_name'] =  $patient_report->package_name;
	// 		$patient_lab_report['clinic_id'] = $clinic_id;
	// 		$patient_lab_report['billing_line_item_id'] = $bil->billing_line_item_id;
	// 		$patient_lab_report['created_by'] = $this->session->userdata('user_id');
	// 		$patient_lab_report['created_date_time'] = date('Y-m-d H:i:s');
	// 		$patient_lab_report['modified_by'] = $this->session->userdata('user_id');
	// 		$patient_lab_report['modified_date_time'] = date('Y-m-d H:i:s');
	
	// 		$lab_patient_report_id = $this->Generic_model->insertDataReturnId('lab_patient_reports', $patient_lab_report);
	// 	}
	// 	$stat['status'] = 2;
	// 	$this->Generic_model->updateData('lab_patient_reports', $stat, array('lab_patient_report_id'=>$lab_patient_report_id));
	// 	for ($i = 0; $i < count($patient_report_array); $i++) {
	// 		$patient_report_line_item['clinic_lab_package_line_item_id'] = $patient_report_array[$i]['clinic_lab_package_line_item_id'];
	// 		$patient_report_line_item['patient_package_line_item_value'] = $patient_report_array[$i]['patient_package_line_item_value'];
	// 		$patient_report_line_item['lab_patient_report_id'] = $lab_patient_report_id;
	// 		$this->Generic_model->insertDataReturnId("lab_patient_report_line_items", $patient_report_line_item);
	// 	}
	// 	echo "1";
	// }


	// this is from old lab controller

	public function patient_report_pdf($billing_line_item_id, $package_id)
	{

		$clinic_id = $this->session->userdata('clinic_id');

		$data['billing_line_item_id'] = $billing_line_item_id = $this->db->query("SELECT * FROM billing_line_items WHERE billing_line_item_id='" . $billing_line_item_id . "'")->row();

		$data['billingdetails'] = $this->db->query("SELECT * FROM `billing` where billing_id='" . $billing_line_item_id->billing_id . "'")->row();
		
		$data['clinicsInfo'] = clinicDetails($clinic_id);
		
		$data['pdfSettings'] = $this->Generic_model->getSingleRecord('clinic_pdf_settings', array('clinic_id'=>$clinic_id));
		
		// $data['headings'] = $this->db->query("select GROUP_CONCAT(clinic_lab_package_line_item_id order by positions ASC) as ids,heading from clinic_lab_package_line_items where package_id='".$billing_line_item_id->investigation_id."' and clinic_id='".$clinic_id."' group by heading order by positions ASC")->result();

		// $data['side_heading'] = $this->db->query("SELECT clpli.investigation_name,lprli.patient_package_line_item_value,clpli.min_value,clpli.max_value,clpli.units,lprli.clinic_lab_package_line_item_id FROM lab_patient_reports lpr INNER JOIN lab_patient_report_line_items lprli ON lprli.lab_patient_report_id=lpr.lab_patient_report_id AND lpr.billing_line_item_id='" . $billing_line_item_id->billing_line_item_id . "' INNER JOIN clinic_lab_package_line_items clpli ON clpli.clinic_lab_package_line_item_id=lprli.clinic_lab_package_line_item_id")->result();
	
		// $data['packageInfo'] = $this->Generic_model->getSingleRecord('clinic_lab_packages', array('package_id'=>$package_id));
		$data['reportsInfo'] = $this->Generic_model->getSingleRecord('lab_patient_reports', array('billing_line_item_id'=>$billing_line_item_id->billing_line_item_id));

		$data['clinicLabPackgesInfo'] = $this->db->query("select * from clinic_lab_packages cp,clinic_lab_package_line_items cpl where cp.clinic_lab_package_id=cpl.clinic_lab_package_id and cp.clinic_lab_package_id='".$data['reportsInfo']->clinic_lab_package_id."' and cp.clinic_id='".$clinic_id."' and cpl.parent='0' order by cpl.positions ASC")->result();
		// echo $this->db->last_query();
		// exit;
		// $data['fullReports'] = $this->db->query("select * ")->result();

		// $data['clinic_information'] = $this->db->query("SELECT * FROM `clinics` WHERE clinic_id='" . $clinic_id . "'")->row();

		$this->load->library('M_pdf');
		echo $data['clinicLabPackgesInfo'][0]->package_id;
		if($data['clinicLabPackgesInfo'][0]->package_id == "1"){
			// echo "sjdfh";
			$html = $this->load->view('LabNew/echo_diagram_pdf', $data, true);
		}
		else{
			// echo "hsgdfkkujr";
			$html = $this->load->view('LabNew/report_pdf', $data, true);
		}
		// exit;
		$pdfFilePath = time() . rand(1000, 9999) . ".pdf";
		$stylesheet = file_get_contents(base_url() . "assets/css/bootstrap.min.css"); // external css
		$stylesheet .= file_get_contents(base_url() . "assets/css/pdf.css");
		$this->m_pdf->pdf->autoScriptToLang = true;
		$this->m_pdf->pdf->autoLangToFont = true;

		$this->m_pdf->pdf->shrink_tables_to_fit = 1;
		$this->m_pdf->pdf->setAutoTopMargin = "stretch";
		$this->m_pdf->pdf->setAutoBottomMargin = "stretch";
		$this->m_pdf->pdf->defaultheaderline = 0;

		$this->m_pdf->pdf->WriteHTML($stylesheet, 1);
		$this->m_pdf->pdf->WriteHTML($html, 2);
		$this->m_pdf->pdf->Output("./uploads/lab_reports/" . $pdfFilePath, "F");

		redirect("uploads/lab_reports/" . $pdfFilePath);
	}

	// this is from old lab controller

	public function invoice_gen($billing_id)
	{
		$billing_invoice = $this->db->query("select * from billing_invoice where billing_id='" . $billing_id . "'")->result();

		print_r($billing_invoice);
	}

	public function edit_investigation_data()
	{
		extract($_POST);
		$id = $this->db->query("SELECT * FROM `clinic_lab_package_line_items` where clinic_lab_package_line_item_id='" . $clinic_lab_package_line_item_id . "'")->row();

		if(count($id)>0)
		{
			$data['investigation_name'] = $edit_inv_name != 'undefined'?$edit_inv_name:'';
			$data['normal_range'] = $normal_range != 'undefined'?$normal_range:'';
			$data['dropdowns'] = $dropdowns != 'undefined'?$dropdowns:'';
			$data['units'] = $units != 'undefined'?$units:'';
			$data['content'] = $content != 'undefined'?$content:'';
			$data['g_e'] = 1;

			$this->Generic_model->updateData('clinic_lab_package_line_items', $data, array('clinic_lab_package_line_item_id'=>$clinic_lab_package_line_item_id));
			echo '1';
		}else{
			echo '0';
		}
	}
}