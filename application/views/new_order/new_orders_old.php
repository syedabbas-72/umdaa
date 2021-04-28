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
		                                <input type="text" id="psearch_neworder" name="pname" style="text-transform: capitalize;" class="form-control" placeholder="Name" required>
		                            </div>
		                        </div>
		                        <div class="col-md-3">
		                            <div class="form-group">
		                                <label for="pmobile" class="col-form-label">Mobile No.</label>
		                                <input type="text" class="form-control" style="text-transform: capitalize;" id="pmobile" name="pmobile" placeholder="Mobile" required>
		                            </div>
		                        </div>
		                        <div class="col-md-6">
		                        	<div class="form-group">
			                        	<label for="pmobile" class="col-form-label">Specify Drug Name</label> &nbsp; <span id="error-msg" class="col-md-12" style="padding: 10px"></span>
			                        	<?php /*<input type="text" name="search_pharmacy" placeholder="Search Drug By Trade Name" id="search_pharmacy" onclick="tnamesearch_inventory();" class="form-control" style="text-transform: capitalize;" /> */ ?>
			                        	<input type="text" name="search_pharmacy" placeholder="Search Drug By Trade Name" id="search_pharmacy_tb" class="form-control" style="text-transform: capitalize;" />
		                        	</div>
		                        </div>
		                    </div>
							<div class="row" <?php if(count($pdrugs)<=0) echo 'style="display:none"'; ?> id="listdiv">
								<div class="col-md-12">	
									<table id="orderlist" class="table table-bordered dt-responsive nowrap" >
										<thead>
											<tr>
												<th class="text-center" style="width: 5%">S.No:</th>
												<th style="width: 35%">Drug Name</th>
												<th style="width: 30%">Batch - Quantity</th>
												<th style="width: 15%" class="text-center">Max. Discount (%)</th>
												<th style="width: 15%" class="text-right">Amount</th>	
											</tr>
										</thead>
										<tbody>
											<?php 
											if(count($pdrugs)>0){
												$i=1;
												foreach($pdrugs as $presult){ 
													?>
													<tr id="orderlist_<?php echo $presult['drug_id']; ?>_tr">
														<td class="text-center"><?php echo $i; ?></td>
														<td>
															<?php echo $presult['trade_name']." ".$presult['salt']; ?><br />
															<?php echo $presult['formulation']; ?>
														</td>
														<td>
															<a onclick="get_batch_details('<?php echo $presult['drug_id']; ?>');" data-toggle="modal" placeholder="quantity" data-target="#myModal"><input type="text" name="qty[]" id="qty_<?php echo $presult['drug_id']; ?>" class="form-control" readonly> </a>
														</td>
														<td>
															<input type="hidden" class="disc" name="disc[]" id="disc_<?php echo $presult['drug_id']; ?>" value="0">
														</td>
														<td>
															<span id="amt_span_<?php echo $presult['drug_id']; ?>" class="mrp" style="display:none"></span><input type="hidden" name="toqty[]" id="tqty_<?php echo $presult['drug_id']; ?>" /><input type="hidden" name="toamt[]" id="toamt_<?php echo $presult['drug_id']; ?>" /><input type="hidden" name="totrw[]" id="totrw<?php echo $presult['drug_id']; ?>" class="totrw" value="<?php echo $presult['drug_id']; ?>" /><input type="hidden" id="disc_tb_<?php echo $presult['drug_id']; ?>" value="<?php echo $presult['discount']; ?>" /><input type="hidden" name="amt[]" id="amt_<?php echo $presult['drug_id']; ?>" value="" class="testp" /><input type="hidden" name="drgid[]" id="drgid_<?php echo $presult['drug_id']; ?>" value="<?php echo $presult['drug_id']; ?>" /><input type="hidden" name="pdid" value="<?php echo $pdid; ?>" />
														</td>
														<!-- <button> -->
													</tr>
													<?php 
													$i++;
												}
											} 
											?>
										</tbody>
									</table>

									<table id="orderlist1" class="table table-borderless dt-responsive nowrap" >
										<tbody>
											<tr>
												<td colspan='5'>
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
													<input type="submit" value="Submit" class="btn btn-success">
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

