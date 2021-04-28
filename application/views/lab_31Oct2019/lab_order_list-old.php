<style type="text/css">
	.btnSection{
		margin-bottom: 8px;
	}
</style>

 <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?php echo $_SESSION['clinic_name']; ?></div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>

                                <li><a class="parent-item" href="<?php echo base_url("lab"); ?>">Lab</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                               
                                <li class="active">Orders</li>
                            </ol>
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
								<div class="col-lg-12 align-self-center text-right btnSection">
								<a href="<?= base_url('Lab/add_lab_order'); ?>" class="btn btn-primary btn-icon"><i class="fas fa-plus-circle"></i> NEW ORDER</a>
								</div>
							</div>
							
							<div class="row">
								<div class="col-md-12">
									<table id="doctorlist" class="table table-bordered dt-responsive">
										<thead>
											<tr>
												<th class="text-center">S#</th>
												<th>Order# & Date</th>
												<th>Patient Info </th>
												<th>Inv. Info </th>	
												<th>Total</th>	
												<th>Paid</th>
												<th>OSA</th>	
												<th>Status</th>
												<th class="text-center">Actions</th>
											</tr>
										</thead>
										<tbody>
										   	<?php 
										   	$i=1; 
										   	$disc = 0;
										   	foreach ($billing_info as $value) {											
											?> 
											<tr>
												<td class="text-center"><?php echo $i;?></td>
												<td><?php echo $value['order_number']; ?><br><?php echo date("d-m-Y",strtotime($value['inv_date'])); ?></td>
												<td><?php echo $value['guest_name'].'<br>'.$value['guest_mobile']; ?></td>
												<td><?php echo $value['ctests']; ?></td>
												<td><?php echo $value['inv_amt']; ?></td>
												<td><?php echo $value['out_amt']; ?></td>
												<td><?php echo $value['osa_amt']; ?></td>
												<td><?php if($value['osa_amt']<=0){ echo "<span style='color:green;font-weight:bold'>Completed</span>"; } else { echo "<span style='color:red;font-weight:bold'>Pending</span>"; } ?></td>
												<td class="text-center">
												<a href="<?php echo base_url('Lab/view_order/'.$value['billing_id']);?>"><i class="fa fa-eye"></i></a>
													
												<a href="<?=base_url("Lab/order_delete/".$value['billing_id'])?>" onclick="return doconfirm();"><i class="fas fa-trash" style="font-size: 17px;color: #333333bf;" title="Delete"></i></a>
												
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

