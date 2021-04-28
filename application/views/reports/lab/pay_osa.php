<div class="page-bar">
	<div class="page-title-breadcrumb">
		<ol class="breadcrumb page-breadcrumb pull-left">
			<li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
			</li>

			<li><a class="parent-item" href="<?php echo base_url("lab"); ?>">Lab</a>&nbsp;<i class="fa fa-angle-right"></i>
			</li>

			<li class="active">Pay OSA</li>
		</ol>
	</div>
</div>
<section class="main-content">
	<div class="row">
		<div class="col-md-12">
			<div class="card"> 

				<div class="row page-title">
					<div class="col-md-12">
						Lab &nbsp; <span><i class="fas fa-caret-right"></i></span> &nbsp; Pay OSA
					</div>
				</div>

				<?php
				// if(isset($billing_info)){
				// 	echo '<pre>';
				// 	print_r($billing_info);
				// 	echo '</pre>';
				// }
				?>

				<div class="card-body"> 
					<form method="post" action="<?php echo base_url('Lab/pay_osa/'.$billing_id); ?>" class="form">
						<div class="row">
							<div class="col-sm-12">
								<p>Please select the payment mode, transaction id and confirm the payment...</p>
								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<label for="pname" class="col-form-label">Customer Name <span class="imp">*</span></label>
											<input class="form-control" type="text" onkeypress="return alpha();" style="text-transform: capitalize;" placeholder="Name" required value="<?php echo ucwords($invoices[0]['guest_name']);?>">
										</div>
									</div>
									<div class="col-md-2">
										<?php 
										if($invoices[0]['mobile'] != ''){
											$mobile = DataCrypt($invoices[0]['guest_mobile'], 'decrypt');
										}else{
											$mobile = DataCrypt($invoices[0]['guest_mobile'], 'decrypt');
										}
										?>
										<div class="form-group">
											<label for="pname" class="col-form-label">Mobile No. <span class="imp">*</span></label>
											<input class="form-control" type="text" onkeypress="return numeric();" maxlength="10" style="text-transform: capitalize;" placeholder="Mobile" required value="<?php echo $mobile; ?>">
										</div>
	
									</div>
									<div class="col-md-2">
									<div class="form-group">
											<label for="pname" class="col-form-label">Age. <span class="imp">*</span></label>
											<input class="form-control" type="text" onkeypress="return numeric();" maxlength="10" style="text-transform: capitalize;" placeholder="Age" required value="<?php echo $invoices[0]['age']; ?>">
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<label for="osa_tb" class="col-form-label">OSA</label>
											<input class="form-control" type="text" readonly value="<?=$osa?>" id="osa_tb" name="invoice_amount">
											<input type="hidden" id="calc_osa_tb" name="" value="<?=$osa?>">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label for="osa_tb" class="col-form-label">Payment Mode<span class="imp">*</span></label>
											<select id="payment_mode_sb" class="form-control" name="payment_mode" required onchange="return showTransactionCode(this.id);">
												<option value="">Select payment mode</option>
												<option value="Cash">Cash</option>
												<option value="Card">Card</option>
												<option value="Online">Online</option>
											</select>
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group" id="transaction_div" style="display: none;">
											<label for="osa_tb" class="col-form-label">Transaction Code<span class="imp">*</span></label>
											<input class="form-control" type="text" value="" id="transaction_id_tb" name="transaction_id">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-3 text-center">
										<div class="form-group">
											<label>&nbsp;</label>
											<input class="form-control customBtn" name="save_pay_osa" type="submit" value="Confirm Payment" />
										</div>			                               
									</div>
								</div>
							</div>							
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>

<script type="text/javascript">
	function showTransactionCode(id){
		value = $('#'+id).val();

		if(value == "Online" || value == "Card"){
			// Show transaction Id div
			$('#transaction_div').show();
			$('#transaction_id_tb').prop('required');
		}else{
			// Hide transaction Id div
			$('#transaction_div').hide();
			$('#transaction_id_tb').removeProp('required');
		}
	}
</script>