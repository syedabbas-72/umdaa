<style type="text/css">
	.btnSection{
		margin-bottom: 8px;
	}
</style>

 <div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li><a class="parent-item" href="<?php echo base_url("lab"); ?>">Lab</a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Billing</li>
            <li><i class="fa fa-angle-right"></i></li>
            <li class="active">Invoices</li>
        </ol>
    </div>
</div>

<section class="main-content">
	<div class="row">
        <div class="col-md-12">
            <div class="card noCardPadding">

            	<div class="row col-md-12 page-title">
					<div class="pull-left col-md-6">Billing Info &nbsp; <span><i class="fas fa-caret-right"></i></span> &nbsp; Invoice List</div>
				</div>

				<div class="tabs">
                    <!-- Nav tabs -->	
                            
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="home">
                        	<div class="row">
								<div class="col-md-12">
									<table class="table infoTable">								
										<tr>
											<td style="vertical-align: top; border-top:none;">
												<h3 style="padding-left: 0px; margin-left: 0px;">Customer Info:</h3>
												<span><?=strtoupper($billingInfo->guest_name)?>,<?=strtoupper($billingInvoice->age)?>Y</span><br>
												<small><?=DataCrypt($billingInfo->guest_mobile, 'decrypt')?></small>
												<br><br>
												<h3 style="padding-left: 0px; margin-left: 0px;">Doctor Info:</h3>
												<?php if($billingInfo->doctor_id != 0){ ?>
													<span><?=ucwords($doctorInfo->first_name." ".$doctorInfo->$last_name)?></span><br>
													<small><i><?=ucwords($doctorInfo->department)?></i></small>
												<?php }else{ ?>
													<span>Self</span><br>
												<?php } ?>	
											</td>											
											<td style="vertical-align: top; border-top:none;">
												<h3 style="padding-left: 0px; margin-left: 0px;">Billing Items Info:</h3>
												<!-- <table class="customTable">
													<tr>
														<th>Item</th>
														<th class="text-right">Amount</th>
													</tr> -->
													<?php
													// foreach($billingLineItemsInfo as $item){
													// 	echo "<tr><td><span>".ucwords($item['item_information'])."</span></td><td class='text-right'><i class='fas fa-rupee-sign'></i>".number_format($item['amount'], 2)."</td></tr>";
													// }
													foreach($billingLineItemsInfo as $item){
														echo "<li><span>".ucwords($item['item_information'])."</li>";
														// echo "<li><span>".ucwords($item['item_information'])."</span> - <i class='fas fa-rupee-sign'></i>".number_format($item['amount'], 2)."</li>";
													}
													?>
												<!-- </table> -->
											</td>
											<td style="vertical-align: top; border-top:none;">
												<h3 style="padding-left: 0px; margin-left: 0px;">Payment Info:</h3>
												<li><span>Total Amount</span> - <i class='fas fa-rupee-sign'></i><?=number_format($billingInfo->total_amount, 2)?></li>
												<li><span>Discount</span> - <?=$billingInfo->discount?> <i class="fas fa-percentage"></i></li>
												<li><span>Billing Amount</span> - <i class='fas fa-rupee-sign'></i><?=$billingInfo->billing_amount?></li>
												<li><span>Amount Paid</span> - <i class='fas fa-rupee-sign'></i><?=(float)$billingInfo->billing_amount - (float)$billingInfo->osa?></li>
												<li><span>OSA</span> - <i class='fas fa-rupee-sign'></i><?=$billingInfo->osa?></li>	
											</td>
											<td style="padding-left: 20px; padding-right: 20px; border-left: 1px dotted #ebebeb; vertical-align: middle; border-top:none;" class="text-center">
												<?php if($billingInfo->osa != 0){ ?>
													<a href="<?=base_url('Lab/pay_osa/'.$billingInfo->billing_id)?>"><i class='fas fa-rupee-sign' style="font-size: 40px; color: red; cursor: pointer"></i></a><br>Pay	
												<?php }else{ ?>
													<span><i class="fas fa-check-circle" style="font-size: 40px; color: green; cursor: pointer"></i></span><br>Paid	
												<?php } ?>
											</td>
										</tr>
									</table>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<table class="table dt-responsive customTable">
										<thead>
											<tr>
												<th class="text-center" style="width: 5%">S.No.</th>
												<th class="text-center" style="width: 15%">Invoice No.</th>
												<th class="text-center" style="width: 15%">Invoice Date</th>
												<th style="width: 15%" class="text-center">Payment Type</th>
												<th style="width: 15%" class="text-center">Payment Mode</th>
												<th style="width: 15%" class="text-center">Transaction No.</th>
												<th class="text-center" style="width: 10%">Invoice Amt.</th>	
												<th class="text-right" style="width: 10%">Actions</th>
											</tr>
										</thead>
										<tbody>
										   	<?php 
										   	$i=1; 
										   	foreach ($billingInvoicesInfo as $value) {	
										   		// echo "<pre>"; print_r($value); echo '</pre>';										
											?> 
											<tr>
												<td class="text-center"><?php echo $i;?></td>
												<td class="text-center"><span><?=$value['invoice_no']; ?></span></td>
												<td class="text-center"><?php echo date("d M. 'y", strtotime($value['invoice_date'])); ?></td>
												<td class="text-center"><?php echo ucwords($value['payment_type']); ?></td>
												<td class="text-center"><?php echo ucwords($value['payment_mode']); ?></td>
												<td class="text-center">
													<?php 
													if($value['payment_mode'] != 'Cash'){
														echo $value['transaction_id'];
													}else{
														echo "--";
													}
													?>		
												</td>
												<td class="text-right"><?php echo number_format($value['invoice_amount'], 2); ?></td>
												<td class="text-right actions">
													<a href="<?php echo base_url('PdfView/LabInvoice/'.$value['billing_invoice_id']);?>" target="_blank"><i class="fas fa-print print"></i></a>
												</td>
											</tr>
									  		<?php $i++;} ?>
										</tbody>
									</table>
								</div>
							</div>							
                        </div>
                    </div>
                </div>
			</div>
		</div>
	</div>
</section>

<script type="text/javascript">
	function doconfirm(){
		if(confirm("Delete selected messages ?")){
			return true;
		}else{
			return false;  
		} 
	}
</script>