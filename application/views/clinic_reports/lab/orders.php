<div class="page-bar">
	<div class="page-title-breadcrumb">
		<ol class="breadcrumb page-breadcrumb pull-left">
			<li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
			</li>

			<li><a class="parent-item" href="<?php echo base_url("lab"); ?>">Lab</a>&nbsp;<i class="fa fa-angle-right"></i>
			</li>

			<li class="active">New Order</li>
		</ol>
	</div>
</div>
<section class="main-content">
	<div class="row">
		<div class="col-md-12">
			<div class="card"> 

				<div class="row page-title">
					<div class="col-md-12">
						Lab &nbsp; <span><i class="fas fa-caret-right"></i></span> &nbsp; New Order
					</div>
				</div>

				<div class="card-body"> 
					<?php
					$total_amount = 0;
					$cartCount = count($investigation_cart);
					?>  	
					<form method="post" action="<?php echo base_url('Lab/save_order'); ?>" class="form">
						<div class="row">
							<div class="col-sm-12">
								<p>Please specify the Customer's name, Mobile no. &amp; Search Investigaiton by name</p>
								<div class="row">
									<div class="col-md-3">
										<?php 
										if($investigation_cart[0]->mobile != ''){
											$mobile = DataCrypt($investigation_cart[0]->mobile, 'decrypt');
										}else{
											$mobile = DataCrypt($investigation_cart[0]->alternate_mobile, 'decrypt');
										}
										?>
										<div class="form-group">
											<label for="pname" class="col-form-label">Mobile No. <span class="imp">*</span></label>
											<input class="form-control" type="text" autocomplete="off" onkeypress="return numeric();" maxlength="10" style="text-transform: capitalize;" id="guest_mobile" name="billing[guest_mobile]" placeholder="Mobile" required value="<?php if($cartCount > 0) { echo $mobile; } ?>">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label for="pname" class="col-form-label">Customer Name <span class="imp">*</span></label>
											<input class="form-control" type="text" autocomplete="off" onkeypress="return alpha();" name="billing[guest_name]" style="text-transform: capitalize;" placeholder="Name" required value="<?php if($cartCount > 0) { echo ucwords($investigation_cart[0]->first_name.' '.$lat_name); } ?>">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label for="pname" class="col-form-label">Age</label>
											<input class="form-control" type="text" autocomplete="off" onkeypress="return numeric();" name="age" style="text-transform: capitalize;" placeholder="Age" value="<?php if($cartCount > 0) { echo ucwords($investigation_cart[0]->age); } ?>">
										</div>
										<!-- <div class="form-group">
											<label for="pname" class="col-form-label">Age</label>
											<input class="form-control" type="text" autocomplete="off" onkeypress="return numeric();" name="age" style="text-transform: capitalize;" placeholder="Age" value="<?php if($cartCount > 0) { echo ucwords($investigation_cart[0]->age); } ?>">
										</div> -->
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label for="pname" class="col-form-label">Gender <span class="imp">*</span></label>
											<select class="form-control" id="sel1" name='gender' value="<?php if($cartCount > 0) { echo ucwords($investigation_cart[0]->gender); } ?>">
										     	<!-- <option value=''>Select</option> -->
												<option value='Male' <?=($investigation_cart[0]->gender == "Male")?'selected':''?>>Male</option>
												<option  value='Female' <?=($investigation_cart[0]->gender == "Female")?'selected':''?>>Female</option>
											</select>
											<!-- <input class="form-control" type="text" autocomplete="off" onkeypress="return alpha();" name="billing[guest_name]" style="text-transform: capitalize;" placeholder="Gender" required value="<?php if($cartCount > 0) { echo ucwords($investigation_cart[0]->first_name.' '.$lat_name); } ?>"> -->
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="pname" class="col-form-label">Search the investigation<span class="imp">*</span></label>
											<input  class="form-control" type="text" autocomplete="off" placeholder="Search by investigation name" id="search_investigation" onclick="return clinicInvestigationSearch();" />
										</div>			                               
									</div>
								</div>
							</div>							
						</div>

						<div class="row" id="listdiv">
							<div class="col-md-12">	
								<table id="orderlist" class="table dt-responsive customTable" >
									<thead>
										<tr>
											<th style="width: 5%">S.No.</th>
											<th style="width: 60%" class="text-center">Investigation / Package </th>
											<th style="width: 15%" class="text-center">Item Code</th>
											<!-- <th style="width: 15%" class="text-center">Short Form</th> -->
											<th style="width: 12%" class="text-center">MRP</th>									
											<th style="width: 13%" class="text-center">Actions</th>
										</tr>
									</thead>
									<!-- <tbody> -->
										<!-- empty -->
									<!-- </tbody>								 -->
									<?php
									if($cartCount > 0){
										$i = 1;
										foreach($investigation_cart as $investigation){
											if($investigation->price > 0){
												// echo '<tr id="inv_'.$investigation->clinic_investigation_id.'"><td class="text-center">'.$i.'.</td><td style="text-transform:UpperCase"><span>'.$investigation->investigation_name.'</span><span class="code">'.$investigation->item_code.'</span><input type="hidden" name="billing_line_items['.$investigation->clinic_investigation_id.'][item_information]" value="'.$investigation->investigation_name.'"></td><td class="text-center">'.$investigation->short_form.'</td><td class="text-right"><span id="amt_span_'.$investigation->clinic_investigation_id.'" >'.$investigation->price.'</span><input type="hidden" name="billing_line_items['.$investigation->clinic_investigation_id.'][clinic_investigation_id]" value="'.$investigation->clinic_investigation_id.'" class="invgids" /><input type="hidden" name="billing_line_items['.$investigation->clinic_investigation_id.'][investigation_id]" value="'.$investigation->investigation_id.'" class="invgids" /><input type="hidden" name="billing_line_items['.$investigation->clinic_investigation_id.'][amount]" id="mrp_tb_'.$investigation->clinic_investigation_id.'" value="'.$investigation->price.'" class="test" /></td><td class="text-center actions"><i class="fas fa-trash-alt deleteSmall" onclick="return removeInvestigation(\''.$investigation->clinic_investigation_id.'\',\'inv_'.$investigation->clinic_investigation_id.'\',\''.$lab_discount.','.$referral_doctor_lab_discount.'\')"><i></td></tr>';
												echo '<tr id="inv_'.$investigation->clinic_investigation_id.'" class="invDataTr">
												<td><span style="margin-left:14px;">'.$i.'</span></td>
												<td class="text-center" style="text-transform:UpperCase"><span>'.$investigation->investigation_name.'</span>
											
												<input type="hidden"
												name="billing_line_items['.$investigation->clinic_investigation_id.'][item_information]"
												 value="'.$investigation->investigation_name.'"><input type="hidden" id="kasak"
												 name="billing_line_items['.$investigation->clinic_investigation_id.'][patient_investigation_line_item_id]"
												  value="'.$investigation->patient_investigation_line_item_id.'"></td>
												 <td class="text-center" style="text-transform:UpperCase"><span class="code">'.$investigation->item_code.'</span></td>
												 <td class="text-center"><i class="fas fa-rupee-sign"></i><span style="margin-left:6px;" id="amt_span_'.$investigation->clinic_investigation_id.'" >'. $investigation->price.'</span><input type="hidden" name="billing_line_items['.$investigation->clinic_investigation_id.'][clinic_investigation_id]" value="'.$investigation->clinic_investigation_id.'" class="invgids" /><input type="hidden" name="billing_line_items['.$investigation->clinic_investigation_id.'][investigation_id]" value="'.$investigation->investigation_id.'" class="invgids" /><input type="hidden" name="billing_line_items['.$investigation->clinic_investigation_id.'][amount]" id="mrp_tb_'.$investigation->clinic_investigation_id.'" value="'.$investigation->price.'" class="test" /></td>
												 <td class="text-center actions"><i class="fas fa-trash-alt deleteSmall" onclick="return removeInvestigation(\''.$investigation->clinic_investigation_id.'\',\'inv_'.$investigation->clinic_investigation_id.'\',\''.$lab_discount.','.$referral_doctor_lab_discount.'\')"><i></td></tr>';
												$total_amount = (float)$total_amount + (float)$investigation->price;
											}
											$i++;
										}
									}else{
										?>
										<tr id="noInvestigation_row">
											<td colspan="7" class="text-center">No investigation has been added yet.</td>
										</tr>
										<?php
									}		
									?>
									<input type="hidden" id="itemCount_tb" value="0">
									<input type="hidden" id="referral_doctor_lab_discount_id" value="<?=$referral_doctor_lab_discount?>" >
									<input type="hidden" name="billing[clinic_id]" value="<?php echo $this->session->userdata('clinic_id'); ?>">
									<input type="hidden" name="billing[billing_type]" value="Lab">
									<input type="hidden" id="total_amount_tb" name="billing[total_amount]" value="<?php echo $total_amount; ?>" >
									<input type="hidden" id="payable_amount_tb" name="billing_invoice[invoice_amount]" value="<?php echo $total_amount; ?>">	
									<input type="hidden" id="billing_amount_tb" name="billing[billing_amount]" value="<?php echo $total_amount; ?>">	
									<input type="hidden" id="osa_flag_tb" value="0">
									<input type="hidden" id="osa_tb" name="billing[osa]" value="0">
									<input type="hidden" name="billing[appointment_id]" value="<?php if($cartCount > 0) { echo $investigation_cart[0]->appointment_id; }else{ echo '0'; } ?>">
									<input type="hidden" name="billing[doctor_id]" value="<?php if($cartCount > 0) { echo $investigation_cart[0]->doctor_id; }else{ echo '0'; } ?>">
									<input type="hidden" name="billing[patient_id]" value="<?php if($cartCount > 0) { echo $investigation_cart[0]->patient_id; }else{ echo '0'; } ?>">
									<input type="hidden" name="billing[umr_no]" value="<?php if($cartCount > 0) { echo $investigation_cart[0]->umr_no; }else{ echo '0'; } ?>">
									<input type="hidden" name="billing[patient_investigation_id]" value="<?php if($cartCount > 0) { echo $investigation_cart[0]->patient_investigation_id; }else{ echo '0'; } ?>">
								</table>

								<table id="paymentInfo_tbl" class="table dt-responsive nowrap customTable" <?php if(count($investigation_cart) == 0){ ?> style="display: none" <?php } ?>>
									<tbody>
										<tr>
											<td style="width:30%" class="text-left">
												<input type="hidden" id="discount_status_tb" name="billing[discount_status]" value="0">
												<input class="form-group" type="checkbox" value="1" onclick="inv_enable_discount(0,'<?=$lab_discount?>', '<?=$referral_doctor_lab_discount?>');" id="applyDiscount_cb" name="billing[discount_status]" />
												<label for="applyDiscount_cb">Apply Discount</label>
											</td>
											<td style="width:35%;" class="text-left form-group">
												<select name="referral_doctor_id" id="referral_doctor_id_sb" class="form-control" onchange="return adjustDiscount(0,'<?=$lab_discount?>','<?=$referral_doctor_lab_discount?>');">
													<option value="">Select who referred</option>
													<?php foreach($referral_doctors as $doctorRec){
														$department = ($doctorRec['department'] != '') ? "(".$doctorRec['department'].")" : "";
														$qualification = ($doctorRec['qualification'] != '') ? "(".substr_replace($doctorRec['qualification'],"",-2).")" : "";
														?>
														<option value="<?=$doctorRec['rfd_id']?>"><?=ucwords($doctorRec['doctor_name'])." ".ucwords($department)." ".strtoupper($qualification)?></option>
														<?php
													}
													?>
												</select>
											</td>		
											<td style="width: 15%" class="text-right"><span id="amt_span_title">Payable Amount :</span></td>
											<td style="width: 12%" class="text-right">
												<span><label id="total_amount_lbl"><?php if(count($investigation_cart) > 0){ echo $total_amount; } ?></label></span>
											</td>	
											<td style="width: 13%">&nbsp;</td>								
										</tr>
										<tr id="discount_tr" style="display: none;">
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td class="text-right">
												<span>Discount(%)</span>
												<input type="number" value="0" name="billing[discount]" id='discount_tb' style="width:30px" class="text-center" onkeyup="return inv_enable_discount(this.value,'<?=$lab_discount?>','<?=$referral_doctor_lab_discount?>');" onkeypress="return numeric();">
											</td>
											<td class="text-right">
												<span><label id="discount_amount_lbl"></label></span>
												<input type="hidden" id="discount_amount_tb" value="0">
											</td>
										</tr>
										<tr id="payable_amount_tr" style="display: none;">
											<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td class="text-right"><span>Payable Amount : </span></td>
											<td class="text-right">
												<span><label id="payable_amount_lbl"></label></span>
											</td>
										</tr>
										<tr>
											<td></td>
											<td>
												<div class="pull-right form-group" id="payment_mode_div" style="width: 50%; padding-right: 5px">
													<select id="payment_mode_sb" name="billing_invoice[payment_mode]" class="form-control" onchange="return chkMode(this.value);">
														<option value="">Select Payment Mode</option>
														<option>Cash</option>
														<option>Card</option>
														<option>PayTm</option>
														<option>Google Pay</option>
													</select>
												</div>
												<div class="pull-right form-group" id="transaction_no_div" style="width: 50%; padding-left: 5px; display: none;">
													<input type="text" name="billing_invoice[transaction_id]" id="transaction_no_tb" value="" class="form-control" placeholder="Transaction No.">
												</div>
											</td>
											<td class="text-right">
												<input type="hidden" value='Net' name="billing_invoice[payment_type]">
												<input type="checkbox" id="advance_cb" value="Advance" name="billing_invoice[payment_type]" onchange="return enableAdvance();">
												<label for="advance_cb">Advance (if any) : </label>
											</td>
											<td class="text-right form-group">
												<input type="text" name="billing_invoice[invoice_amount]" id="advance_tb" value="" autocomplete="off" class="text-right form-control" onkeyup="return osa();" onkeypress="return decimal();" disabled="disabled">
											</td>
											<td class="text-center">
												<!-- <div id="minAdvance_div" class="gray" style="display: none"></div> -->
												<input type="hidden" id="minAdvance_tb" value="">
												<div id="advance_status_div" class="inadequate" style="display: none;">INADEQUATE</div>
											</td>
										</tr>
										<tr id="osa_tr" style="display: none">
											<td colspan="2"></td>
											<td class="text-right" id="osa_text"><span>OSA &nbsp;</span><h6 style="color:dimgrey;">(Out standing Amount):</h6></td>
											<!-- <td class="text-right"><span>OSA &nbsp;</span><h6 style="color:dimgrey;">(Out standing Amount):</h6></td> -->
											<td class="text-right">
												<span id="osa_span">0.00</span>												
											</td>
											<td colspan="2"></td>
										</tr>
										<tr>
											<td colspan="2"></td>
											<td class="text-left">
												&nbsp;
											</td>
											<td class="text-right">
												<input type="submit" value="Submit" class="btn btn-success" onclick="return validateLabOrder();">
											</td>
											<td colspan="2"></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					
					</form>
				</div>
			</div>
		</div>
	</div>
</section>

<div id="myModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">	
			<input type="hidden" name="d_id" id="d_id" value="" />
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Batches</h4>
			</div>

			<div class="modal-body">
				<div class="row" id="binfo"> </div>       
			</div>

			<div class="modal-footer">	   	
				<input type="button" value="Submit" class="btn btn-success" onclick="storelinedetails();" />
			</div>
		</div>
		<!-- Modal content-->
	</div>
</div>


<script type="text/javascript">
	function getptm_textbox()
	{
		val = $("#ptm").val();
		if(val!='')
			$("#ptm_txt").css("display","block");
		else
			$("#ptm_txt").css("display","none");
	}

	// $("#referral_doctor_id_sb").select2();

	function chkMode(value){
		$("#transacrion_no_tb").val("");
		if(value != 'Cash'){
			$("#transaction_no_div").show();
			$("#payment_mode_div").removeClass('pull-right');
			$("#payment_mode_div").addClass('pull-left');
			$("#transaction_no_tb").focus();
		}else{
			$("#transaction_no_div").hide();
			$("#payment_mode_div").removeClass('pull-left');
			$("#payment_mode_div").addClass('pull-right');
		}
	}
</script>