<style type="text/css">
	.btnSection{
		margin-bottom: 8px;
	}
</style>

 <div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>

            <li>Lab&nbsp;<i class="fa fa-angle-right"></i>
            </li>
           
            <li class="active">Orders</li>
        </ol>

        <div class="pull-right tPadding">
			<a href="<?= base_url('Lab/add_order'); ?>" class="btn btn-primary btn-icon"><i class="fas fa-plus-circle"></i> NEW ORDER</a>
        </div>
    </div>
</div>

<section class="main-content">
	<div class="row">
        <div class="col-md-12">
            <div class="card noCardPadding">
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
												<th class="text-center" style="width: 10%">Date</th>
												<th style="width: 12%">Guest/Patient</th>
												<th style="width: 63%">Investigations &amp; Status</th>	
												<th class="text-center" style="width: 10%">Actions</th>
											</tr>
										</thead>
										<tbody>
										   	<?php 
										   	$i=1; 
										   	$disc = 0;
										   	foreach ($billing_info as $value) {	
										   		$positionStatus = $value['position_status'];										
												?> 
												<tr>
													<td class="text-center"><?php echo $i;?></td>
													<td class="text-center"><?php echo date('d M Y', strtotime($value['billing_date_time'])); ?>
													<?php if($value['payment_status'] == 0){ echo "<div class='pending'>Pending</div>"; } else { echo "<div class='paid'>Paid</div>"; } ?>
													</td>
													<td><span><?php echo ucwords($value['guest_name']).'</span><br>'.$value['guest_mobile']; ?></td>
													<td><?php echo $value['item_information']; ?></td>
													<td class="text-center actions">
														<a href="<?php echo base_url('Lab/view_order/'.$value['billing_id']);?>"><i class="fas fa-search viewSmall"></i></a>
														<a href="<?=base_url("Lab/order_delete/".$value['billing_id'])?>" onclick="return doconfirm();"><i class="fas fa-trash-alt deleteSmall"></i></a>
													</td>
												</tr>
										  		<?php 
										  		$i++;
										  	} 
										  	?>
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
  if(confirm("Delete selected order ?")){
    return true;
  }else{
    return false;  
  } 
}
  </script>

