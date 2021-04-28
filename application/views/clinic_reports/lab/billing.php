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
        </ol>
    </div>
</div>

<section class="main-content">
	<div class="row">
        <div class="col-md-12">
            <div class="card noCardPadding">

            	<div class="row col-md-12 page-title">
					<div class="pull-left col-md-6">Billing List</div>
				</div>

				<div class="tabs">
                    <!-- Nav tabs -->	
                            
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="home">
							
							<div class="row">
								<div class="col-md-12">
									<table id="doctorlist" class="table dt-responsive customTable">
										<thead>
											<tr>
												<th class="text-center" style="width: 5%">S.No.</th>
												<th class="text-center" style="width: 8%">Date</th>
												<th style="width: 13%">Guest/Patient</th>
												<th style="width: 15%">Investigations</th>
												<th class="text-center" style="width: 7%">Amt.</th>	
												<th class="text-center" style="width: 7%">Disc.(%)</th>	
												<th class="text-center" style="width: 10%">Payable Amt.</th>	
												<th class="text-center" style="width: 7.5%">Paid</th>
												<th class="text-center" style="width: 7.5%">OSA</th>	
												<th class="text-center" style="width: 5%">Status</th>
												<th class="text-right" style="width: 12%">Actions</th>
											</tr>
										</thead>
										<tbody>
										   	<?php 
										   	$i=1; 
										   	$disc = 0;
										   	foreach ($billing_info as $value) {	
										   		// echo "<pre>"; print_r($value); echo '</pre>';										
											?> 
											<tr>
												<td class="text-center"><?php echo $i;?></td>
												<td class="text-center"><?php echo date("d M. 'y", strtotime($value['billing_date_time'])); ?></td>
												<td><?php echo ucwords($value['guest_name']).'<br>'.$value['guest_mobile']; ?></td>
												<td><?php echo ucwords($value['item_information']); ?></td>
												<td class="text-center"><?php echo number_format($value['total_amount'], 2); ?></td>
												<td class="text-center"><?php echo $value['discount']; ?></td>
												<td class="text-center"><?php echo number_format($value['billing_amount'], 2); ?></td>
												<td class="text-center"><?php echo number_format(((float)$value['billing_amount'] - (float)$value['osa']), 2); ?></td>
												<td class="text-center"><?php echo number_format($value['osa'], 2); ?></td>
												<td class="text-center"><?php if($value['payment_status'] == 0){ echo "<div class='pending'>Pending</div>"; } else { echo "<div class='paid'>Paid</div>"; } ?>
												</td>
												<td class="text-right actions">
													<?php if($value['payment_status'] == 0) { ?>
														<a href="<?php echo base_url('Lab/pay_osa/'.$value['billing_id']);?>"><i class="fas fa-rupee-sign rupeeSmall"></i></a>	
													<?php } ?>
													<a href="<?php echo base_url('Lab/view_billing/'.$value['billing_id']);?>"><i class="fas fa-search viewSmall"></i></a>	
													<a href="<?=base_url("Lab/order_delete/".$value['billing_id'])?>" onclick="return doconfirm();"><i class="fas fa-trash-alt deleteSmall"></i></a>
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
<script>
  $(document).ready(function () {
      $('#doctorlist').dataTable();
  });
  function doconfirm(){
  if(confirm("Delete selected messages ?")){
    return true;
  }else{
    return false;  
  } 
}
  </script>

