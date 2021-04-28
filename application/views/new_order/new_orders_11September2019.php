<style>
	.hdr {
		padding: 10px 15px;
		position: relative;
		display: block;
	}
	.batch > tbody > tr > td, .batch > tbody > tr > th {
		border: 1px solid #e9ecef;
		font-size: 13px;
		vertical-align: inherit;
	}
	.btchRow{
		margin-right: -14px;
		margin-left: -14px;
		display: flex;
		flex-wrap: wrap;
	}
</style>

<?php
if(count($pdrugs)>0){
	$customer_name = $pdrugs[0]['patient_name'];
	$customer_mobile = $pdrugs[0]['mobile'];
}else{
	$customer_name = $customer_mobile = '';	
}
?>

<div class="page-bar">
	<div class="page-title-breadcrumb">
		<ol class="breadcrumb page-breadcrumb pull-left">
			<li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
			</li>
			<li>Pharmacy&nbsp;<i class="fa fa-angle-right"></i></li>
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
                        Pharmacy &nbsp; <span><i class="fas fa-caret-right"></i></span> &nbsp; New Order
                    </div>
                </div>

                <div class="row mainContent">
                	<div class="col-md-12">		
						<form method="post" action="<?php echo base_url('New_order/save_order'); ?>" class="form customForm">
							<div class="row">
								<div class="col-md-3">
		                            <div class="form-group">
		                                <label for="pname" class="col-form-label">Customer Name <span class="imp">*</span></label>
		                                <input type="text" id="" maxlength="35" onkeypress="return alpha()" name="pname" style="text-transform: capitalize;" class="form-control" placeholder="Name" required value="<?php echo $customer_name; ?>">
		                            </div>
		                        </div>
		                        <div class="col-md-3">
		                            <div class="form-group">
		                                <label for="pmobile" class="col-form-label">Mobile No.</label>
		                                <input type="text" class="form-control" style="text-transform: capitalize;" id="pmobile" name="pmobile" placeholder="Mobile" required maxlength="10" minlength="10" onkeypress="return event.charCode >= 48 && event.charCode <= 57" value="<?php echo $customer_mobile; ?>">
		                            </div>
		                        </div>
		                        <div class="col-md-6">
		                        	<div class="form-group">
			                        	<label for="pmobile" class="col-form-label">Specify Drug Name</label> &nbsp; <span id="error-msg" class="col-md-12" style="padding: 10px"></span>
			                        	<input type="text" name="search_pharmacy" placeholder="Search Drug By Trade Name" id="search_pharmacy_tb" class="form-control" style="text-transform: capitalize;" />
		                        	</div>
		                        </div>
		                    </div>
							<div class="row" <?php //if(count($pdrugs)<=0) echo 'style="display:none"'; ?> id="listdiv">
								<div class="row col-md-12" style="padding-right: 5px; padding-left:35px;">
                            		<table id="orderlist" class="table dt-responsive nowrap customTable">
										<thead>
											<tr>
												<th>S.No:</th>
												<th>Drug Name</th>
												<th>Batch - Quantity</th>
												<th>Amount</th>
												<th>Discount (%)</th>
												<th>Pay Amount</th>	
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php 
											if(count($pdrugs)>0){
												$i=1;
												foreach($pdrugs as $presult){ 
													extract($presult);
													?>
													<tr id="orderlist_<?php echo $presult['drug_id']; ?>_tr">
														<td><?php echo $i; ?></td>
														<td>
															<span class="mrp">
																<?php echo $presult['trade_name']."<br>".$presult['salt']; ?>
															</span>
															<span class="formulation">
															<?php echo strtoupper($presult['formulation']); ?>
															</span>
														</td>
														<?php if($stock == 0) {
															?>
															<td>
																<input type="text" name="qty[]" id="qty_<?php echo $presult['drug_id']; ?>" class="form-control noStock drugQty" readonly placeholder="No Stock Available" style="color:#000">
															</td>
															<?php 
														}else{
															?>
															<td>
																<a onclick="get_batch_details('<?php echo $presult['drug_id']; ?>');" data-toggle="modal"  data-target="#myModal"><i class="fa fa-edit"></i></a><input type="text" required="" name="qty[]" id="qty_<?php echo $presult['drug_id']; ?>" class="form-control stock drugQty" style="color:#000">
															</td>
															<?php 
														} ?>
														
														<td><span id="actual_amt_span_<?php echo $presult['drug_id']; ?>" class="mrp"></span></td>
														<td>
															<input type="hidden" class="disc" name="disc[]" id="disc_<?php echo $presult['drug_id']; ?>" value="0"  onkeyup="return checkmax('<?php echo $presult['discount']; ?>',id,<?php echo $presult['drug_id']; ?>)">
														</td>
														<td>
															<span id="amt_span_<?php echo $presult['drug_id']; ?>" class="mrp" style="display:none"></span>
															<input type="hidden" name="toqty[]" id="tqty_<?php echo $presult['drug_id']; ?>" required />
															<input type="hidden" name="toamt[]" id="toamt_<?php echo $presult['drug_id']; ?>" />
															<input type="hidden" name="totrw[]" id="totrw<?php echo $presult['drug_id']; ?>" class="totrw" value="<?php echo $presult['drug_id']; ?>" />
															<input type="hidden" id="disc_tb_<?php echo $presult['drug_id']; ?>" value="<?php echo $presult['discount']; ?>" />
															<input type="hidden" name="amt[]" id="amt_<?php echo $presult['drug_id']; ?>" value="" class="testp" />
															<input type="hidden" name="drgid[]" id="drgid_<?php echo $i; ?>" value="<?php echo $presult['drug_id']; ?>" />
															<input type="hidden" name="pdid" value="<?php echo $pdid; ?>" />
														</td>
														<td><span><i class="fa fa-trash delete remove_drug_p" id="orderlist_<?php echo $presult['drug_id']; ?>_tr"><i></span></td>
													</tr>
													<?php 
													$i++;
												}
											}
											else{
												?>
												<tr id="noDrug_row">
													<td colspan="7" class="text-center">No drug has been added yet.</td>
												</tr>
												<?php
											}
											?>
										</tbody>
									</table>

									<table id="orderlist1" class="table table-borderless dt-responsive nowrap" style="display: none" >
										<tbody>
											<tr>
												<td>
													<input type="hidden" name="apdis" value="0">
													<input class="form-group" type="checkbox" value="1" onclick='enable_discount();' id="apdis" name="apdis" />&nbsp;Apply Discount
												</td>
												<td style="font-weight: bold;">
													Total
												</td>
												<td>
													<label id="p_total" style="margin-bottom: 0;font-weight: bold;"></label>
													<input type="hidden" id="ip_total" value="" /> 
												</td>
											</tr>
											<tr>
												<td colspan='5'>
													<input type="submit" value="Submit" class="btn btn-success" onclick="return validateDrugOrder()">
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<input type="hidden" id="pharmacy_discount" value="<?php echo $clinic_info->pharmacy_discount; ?>" />
						</form>
					</div>
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
			<div class="modal-header hdr">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" style="text-align: left;">Batches</h4>
			</div>

			<div class="modal-body">
				<div class="row btchRow" id="binfo"> </div>       
			</div>
			<div class="modal-footer">	   	
				<input type="button" value="Submit" class="btn btn-success" onclick="storelinedetails();" />
			</div>
		</div>
		<!-- Modal content-->
	</div>
</div>

