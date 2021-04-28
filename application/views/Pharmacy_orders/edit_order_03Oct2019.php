<div class="row page-header">
	<div class="col-lg-6 align-self-center">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><?php echo $_SESSION['clinic_name']; ?></a></li>
			<li class="breadcrumb-item"><a href="#">PHARMACY</a></li>
			<li class="breadcrumb-item active"><a href="#">Edit</a></li>
		</ol>
	</div>
</div>
<section class="main-content">
	<div class="row">             
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
					<form method="POST" action="<?php echo base_url('Pharmacy_orders/pharmacy_edit');?>" role="form">
						<table id="orderlist" class="table table-bordered dt-responsive nowrap" >
							<tbody>
								<?php 
								$clinic_id = $this->session->userdata("clinic_id");
								$vendor_list = $this->db->query("select * from vendor_master where clinic_id = '".$clinic_id."'")->result();
								foreach($info as $result){ 
									$expiryYear = explode("-", $result['expiry_date'])[0];
									$expiryDate = explode("-", $result['expiry_date'])[1];
									$dateObj   = DateTime::createFromFormat('!m', $expiryDate);
									$monthName = $dateObj->format('F');
									?>
									<tr>
										<td>
											<input type="hidden" name="clinic_id[]" value="<?php echo $this->session->userdata("clinic_id"); ?>" />
											<input type="hidden" name="drug_id[]" value="<?php echo $result['drug_id']; ?>" />
											<input type="hidden" name="cpi_inw_id[]" value="<?php echo $result['clinic_pharmacy_inventory_inward_id']; ?>" />
											<input type="hidden" name="cpi_id[]" value="<?php echo $result['clinic_pharmacy_inventory_id']; ?>" />

											<span class="page-title"><?=$result['trade_name']?> <small>( Supplied on: <?=date('d M Y', strtotime($result['supplied_date'])); ?> )</small></span>
											<div class="row">
												<div class="col-md-2">
													<label class="col-form-label">HSN Code</label>
													<input type="text" id="<?php echo $result['clinic_pharmacy_inventory_inward_id'].'_hsn_code_tb'; ?>" class="form-control clone" name="hsn_code[]" onkeyup="return cloneEntry('clone', '<?php echo $result['clinic_pharmacy_inventory_inward_id'].'_hsn_code_tb'; ?>');" value="<?php echo $result['hsn_code']; ?>">
												</div>
												<div class="col-md-2">
													<label class="col-form-label">Batch No.</label>
													<input type="text" class="form-control" name="batchno[]" required value="<?php echo $result['batch_no']; ?>">
												</div>
												<div class="col-md-1">
													<label class="col-form-label">Quantity</label>
													<input type="text" class="form-control text-center" name="quantity[]" required onkeypress="return numeric()" value="<?php echo $result['quantity']; ?>">
												</div>
												<div class="col-md-1">
													<label class="col-form-label">MRP</label>
													<input type="text" class="form-control text-center" name="mrp[]" required onkeypress="return decimal()" value="<?php echo $result['mrp']; ?>">
												</div>
												<div class="col-md-1">
													<label class="col-form-label">R-Odr Lvl</label>
													<input type="text" class="form-control text-center" name="reorder_level[]" value="<?php echo $result['reorder_level']; ?>" required onkeypress="return numeric()">
												</div>
												<div class="col-md-1">
													<label class="col-form-label">IGST(%)</label>
													<input type="text" class="form-control text-center" name="igst[]" value="<?php echo $result['igst']; ?>" required onkeypress="return numeric()">
												</div>
												<div class="col-md-1">
													<label class="col-form-label">CGST(%)</label>
													<input type="text" class="form-control text-center" name="cgst[]" value="<?php echo $result['cgst']; ?>" required onkeypress="return numeric()">
												</div>
												<div class="col-md-1">
													<label class="col-form-label">SGST(%)</label>
													<input type="text" class="form-control text-center" name="sgst[]" value="<?php echo $result['sgst']; ?>" required onkeypress="return numeric()">
												</div>
												<div class="col-md-1">
													<label class="col-form-label">Discount(%)</label>
													<input type="text" class="form-control text-center" name="max_discount_percentage[]" value="<?php echo $result['max_discount_percentage']; ?>" required onkeypress="return numeric()">
												</div>
												<div class="col-md-1">
													<label class="col-form-label">Pack Size</label>
													<input type="text" class="form-control text-center" name="pack_size[]" value="<?php echo $result['pack_size']; ?>" required onkeypress="return numeric()">
												</div>
											</div>
											<div class="row">
												<div class="col-md-2">
													<label class="col-form-label">Expiry Date - Month</label>
													<select name="expiryMonth[]" class="form-control" required>
														<option value="">Select Month</option>
														<?php
														$month = array('January','February','March','April','May','June','July ','August','September','October','November','December');
														$i=0;
														foreach ($month as $value) {
															?>
															<option value="<?=($i<9)?'0':''?><?=$i+1?>" <?=($value==$monthName)?'selected':''?>><?=$value?></option>
															<?php	
															$i++;								
														}
														?>
													</select>
												</div>
												<div class="col-md-2">
													<label class="col-form-label">Year</label>
													<select name="expiryYear[]" class="form-control" required>
														<option value="">Select Year</option>
														<?php
														$year = date("Y");
														for($i=0;$i<=20;$i++)
														{
															?>
															<option value="<?=$year?>" <?=($year==$expiryYear)?'selected':''?>><?=$year?></option>
															<?php
															$year++;
														}
														?>
													</select>
												</div>
												<div class="col-md-4">
													<label class="col-form-label">Vendor</label>
													<select class="form-control" name="vendor[]" required="">
														<option selected="" disabled="">Select Vendor</option>
														<?php
														foreach ($vendor_list as $value) {
															?>
															<option value="<?=$value->vendor_id?>" <?=($value->vendor_id==$result['vendor_id'])?'selected':''?>><?=$value->vendor_storeName.", ".$value->vendor_location?></option>
															<?php
														}
														?>
													</select>
												</div>
											</div>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
						<table id="orderlist1" class="table table-striped dt-responsive nowrap" >
							<tbody>						
								<tr><td colspan='5'><input type="submit" value="Submit" class="btn btn-success"></td></tr>
							</tbody>
						</table>						
					</form>
				</div>
			</div>
		</div>
	</div>
</section>