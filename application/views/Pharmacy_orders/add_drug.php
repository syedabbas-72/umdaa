<style type="text/css">
	.fa-plus-circle.plus,.fa-minus-circle.minus{
		font-size: 20px !important;
	}
</style>

<script type="text/javascript">

	
</script>

<div class="page-bar">
	<div class="page-title-breadcrumb">
		<ol class="breadcrumb page-breadcrumb pull-left">
			<li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
			</li>
			<li><a href="<?=base_url('Pharmacy_orders')?>">Pharmacy Inventory</a>&nbsp;<i class="fa fa-angle-right"></i></li>
			<li class="active">Add New Drug</li>
		</ol>
	</div>
</div>


	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
				<form method="post" action="<?php echo base_url('Pharmacy_orders/drug_add');?>" class="form customForm">
				
					<!-- Sub header in the form -->
	                <div class="row text-center docInfoHdr">
	                    <div class="col-md-6 text-left">
	                        New Drug Information
	                    </div>
	                </div>
					
					<!-- hidden text input for counter -->
					<input type="hidden" name="counter" id="counter_tb" value="1">

					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label for = "name">Formulation</label>
								<select class="form-control" name="drug[formulation]" required="required">
									<option value="">Select Formulation</option>
									<option>Tablet</option>
									<option>Syrup</option>
									<option>Capsule</option>
									<option>Suspension</option>
									<option>Infusion</option>
									<option>Cream</option>
									<option>Liquid</option>
									<option>Soap</option>
									<option>Wipe</option>
									<option>Plaster</option>
									<option>Powder</option>
									<option>Drops</option>
									<option>Patch</option>
									<option>Injection</option>
									<option>Claris</option>
									<option>Nirlife</option>
									<option>Combi Device Kit</option>
									<option>Needle</option>
									<option>Gel</option>
									<option>Suppository</option>
									<option>Sachet</option>
									<option>Ointment</option>
									<option>Granules</option>
									<option>Oil</option>
									<option>Shampoo</option>
								</select>
					        </div>
					    </div>
				        <div class="col-md-3">
				        	<div class="form-group">
							    <label for = "trade_name_tb">Trade Name</label>
					            <input type = "text" class = "form-control" id = "trade_name_tb" name="drug[trade_name]" required="required">
							</div>
						</div>						
						<div class="col-md-3">
							<div class="form-group">
							    <label for = "trade_name_tb">Manufacturer</label>
					            <input type = "text" class = "form-control" id = "trade_name_tb" name="drug[manufacturer]" required="required" onkeypress="return alpha();" style="text-transform: capitalize;">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
							    <label for = "category_sb">Category</label>
					            <select class="form-control" name="drug[category]" id="category_sb" required="required">
									<option value="">Select Category</option>
									<option>Drug</option>
									<option>Supply</option>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="salt_tb">Salt</label>
								<div class="input-group ">
									<input type="text" class="form-control" id="salt_name_tb" onclick="saltMasterSearch();">
									<div class="input-group-append">
										<i class="fas fa-check-circle tick" onclick="return addSalt('salt_name_tb');"></i>
									</div>
								</div>
							</div>
						</div>			
						<div class="col-md-3">
							<div class="form-group">
								<label for="trade_name_tb">Drug HSN Code</label>
					            <input type="text" class="form-control" id="hsn_code_tb" name="drug[hsn_code]">
							</div>
						</div>
					</div>
					
					<!-- below Div 'saltsDiv' shows up when a new salt is added for the composition of the drug -->
					<div class="row">
						<!-- <div class="col-md-12 saltsHeadersDiv" id="saltsHeadersDiv" style="display: none;">
							<table cellpadding="0" cellspacing="0" class="customTable" id="saltsTbl">
								<tr>
									<th>Salt Name</th>
									<th>Dossage</th>
									<th>Unit</th>
									<th>Sch. Drug</th>
									<th>Actions</th>
								</tr>

							</table>
						</div> -->
						<div class="saltsDiv col-md-12" id="saltsDiv">
							
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<input type="hidden" name="inventoryInward" value="0">
								<input type="checkbox" name="inventoryInward" id="inventoryInward_cb" onchange="return showUpDrugInward();" value="1">
								<label for="inventoryInward_cb">Input drug inward information</label>
							</div>
						</div>
					</div>


					<div class="inventoryInwardInfo">
						<!-- Sub header in the form - Drug information -->
		                <div class="row text-center docInfoHdr">
		                    <div class="col-md-6 text-left">
		                        Drug Inventory Inward Information 
		                    </div>
		                </div>

		                <div class="row">
							<div class="col-md-2">
								<div class="form-group">
									<label for="">Maximum Discount(%) <span class="mandatory pricingInfo" style="display: none">*</span></label>
									<input type ="text" class="form-control requiredInfo" id="max_discount_percentage_tb" maxlength="2" onkeypress="return numeric()" onkeyup="return validateAddNewDrug();" name="clinic_pharmacy[max_discount_percentage]" value="">
								</div>
							</div>

							<div class="col-md-2">
								<div class="form-group">
									<label for="sgst">Re-Order Level <span class="mandatory pricingInfo" style="display: none">*</span></label>
									<input type ="text" class="form-control requiredInfo" id="reorder_level_tb" maxlength="3" onkeypress="return numeric()" onkeyup="return validateAddNewDrug();" name="clinic_pharmacy[reorder_level]" value="">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="igst">IGST(%)</label>
									<input type ="text" class="form-control" maxlength="2" id = "igst_tb" name="clinic_pharmacy[igst]"  onkeypress="return numeric()" value="">
								</div>
							</div>						
							<div class="col-md-2">
								<div class="form-group">
									<label for="cgst">CGST(%)</label>
									<input type ="text" class="form-control" maxlength="2" id = "cgst_tb" name="clinic_pharmacy[cgst]" onkeypress="return numeric()" value="">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="sgst">SGST(%)</label>
									<input type ="text" class="form-control" maxlength="2" id="sgst_tb" name="clinic_pharmacy[sgst]" onkeypress="return numeric()" value="">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="vendor_sb">Vendor <span class="mandatory pricingInfo" style="display: none">*</span></label>
									<select id="vendor_id_sb" name="clinic_pharmacy[vendor_id]" class="form-control " onchanges="return validateAddNewDrug();">
										<option value="">Select Vendor</option>
										<?php 
										foreach($vendors as $vendor){
											echo "<option value='".$vendor['vendor_id']."'>".ucwords($vendor['vendor_storeName'].", ".$vendor['vendor_location'])."</option>";
										}	
										?>									
									</select>
								</div>
							</div>
						</div>

						<!-- Sub header in the form - Drug information -->
		                <div class="row text-center docInfoHdr">
		                    <div class="col-md-6 text-left">
		                        Drug Batch Information 
		                    </div>
		                </div>

						<div class="row col-md-12" id="drugBatchDiv">
							<div class="row">
								<div class="col-md-2">
									<div class="form-group">
										<label for="batch_tb">Batch</label>
										<input type ="text" class="form-control requiredInfo" id = "batch_tb" name="cp_inward[0][batch_no]" value="">
									</div>
								</div>
								<div class="col-md-1">
									<div class="form-group">
										<label for="batch_tb">Pack Size</label>
										<input type ="text" class="form-control requiredInfo" id = "pack_size_tb" maxlength="2" onkeypress="return numeric()" name="cp_inward[0][pack_size]" value="">
									</div>
								</div>
								<div class="col-md-1">
									<div class="form-group">
										<label for="batch_tb">Quantity</label>
										<input type ="text" class="form-control requiredInfo" id="quantity_tb" onkeypress="return numeric()" maxlength="5" name="cp_inward[0][quantity]" value="">
									</div>
								</div>
								<div class="col-md-1">
									<div class="form-group">
										<label for="batch_tb">MRP/Pack</label>
										<input type ="text" class="form-control requiredInfo" id = "mrp_tb" name="cp_inward[0][mrp]" onkeypress="return decimal()" maxlength="7" value="">
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label for="batch_tb">Expiry Month</label>
										<select id="expiry_year_sb" name="cp_inward[0][expiry_month]" class="form-control requiredInfo">
											<option value="">Select Month</option>
											<option value="1">January</option>
											<option value="2">February</option>
											<option value="3">March</option>
											<option value="4">April</option>
											<option value="5">May</option>
											<option value="6">June</option>
											<option value="7">July</option>
											<option value="8">August</option>
											<option value="9">September</option>
											<option value="10">October</option>
											<option value="11">November</option>
											<option value="12">December</option>
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label for="batch_tb">Expiry Year</label>
										<select id="expiry_year_sb" name="cp_inward[0][expiry_year]" class="form-control requiredInfo">
											<option value="">Select Year</option>
											<?php
												(int)$currentYear = date('Y');
												(int)$yearAhead = $currentYear+11;
												for($i=$currentYear; $i<$yearAhead; $i++){
													echo "<option value='".$currentYear."'>".$currentYear."</option>";
													$currentYear++;
												}
											?>
										</select>
									</div>
								</div>
								<div class="col-md-1">
									<i class="fas fa-plus-circle plus" onclick="return addBatch();"></i>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-12 text-center">
						<div class="form-group">
							<input type="submit" value="Save" name="submit" class="btn btn-success" onclick="return validateAddNewDrug()"> 		
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	var soloInput = $('input.solo');

	soloInput.on('keyup', function(){
		var v = $(this).val();
		if (v.match(/^\d{2}$/) !== null) {
			$(this).val(v + '/');
		} else if (v.match(/^\d{2}\/\d{2}$/) !== null) {
			$(this).val(v + '/');
		}  
	});

	function showUpDrugInward(){
		if($("#inventoryInward_cb").is(":checked")){
			$(".inventoryInwardInfo").show();
			$(".requiredInfo").attr('required','required');
		}else{
			$(".inventoryInwardInfo").hide();		
			$(".requiredInfo").removeAttr('required');
		}
	}

	function validateAddNewDrug(){

		if($("#inventoryInward_cb").is(":checked")){

			// console.log("Discount: "+$("#max_discount_percentage_tb").val()+", Re-Order Level: "+$("#reorder_level_tb").val()+", Vendor: "+$("#vendor_id_sb").val());

			// if($("#max_discount_percentage_tb").val() != '' || $("#reorder_level_tb").val() != '' || $("#vendor_id_sb").val() != '') {
			// 	console.log($("#vendor_id_sb").val());
			// 	$('.pricingInfo').show();
			// 	console.log('1...');
			// }else if($("#max_discount_percentage_tb").val() == '' && $("#reorder_level_tb").val() == '' && $("#vendor_id_sb").val() == '') {
			// 	$('.pricingInfo').hide();
			// 	console.log('2...');
			// }	
		}
		
		// return false;
	}

	function removeDiv(id){
		$('#'+id).remove();
	}

	function concatSDU(id){
		SDU = $('#salt_name_'+id+'_tb').val()+' '+$('#dossage_'+id+'_tb').val()+$('#unit_'+id+'_sb').val();
		$('#SDU_'+id+'_tb').val(SDU);
	}

	$(function () {
        $(".expirydate_tb").datepicker({dateFormat: "yy-mm-dd", changeYear: true, minDate: 0, dateFormat: "dd/mm/yy",});
    });

</script>