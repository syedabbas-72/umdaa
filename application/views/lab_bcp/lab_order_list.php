<style type="text/css">
	.btnSection{
		margin-bottom: 8px;
	}
</style>
<div class="page-bar">
  <div class="page-title-breadcrumb">
      <!-- <div class=" pull-left">
        <div class="page-title">Form Layouts</div>
      </div> -->
      <ol class="breadcrumb page-breadcrumb pull-right">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="#"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>       
          <li class="active">LAB ORDERS</li>
      </ol>
  </div>
</div>

	<div class="row">
        <div class="col-md-12">
            <div class="card noCardPadding">
				<div class="tabs">
                    <!-- Nav tabs -->	
                    
                            
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="home">
						    <div class="card-body">
							<div class="row">
								<div class="col-lg-12 align-self-center text-right btnSection">
								<a href="<?= base_url('Lab/add_lab_order'); ?>" class="btn btn-primary btn-icon"><i class="fas fa-plus-circle"></i> ADD</a>
								</div>
							</div>
							
							<div class="row">
								<div class="col-md-12">
									<table id="doctorlist" class="table table-bordered dt-responsive">
										<thead>
											<tr>
												<th class="text-center">S.No:</th>
												<th>Date </th>
												<th>Order</th>
												<th>Patient Info </th>
												<th>Tests Info </th>	
												<th class="text-center"></th>
											</tr>
										</thead>
										<tbody>
										   	<?php $i=1; foreach ($billing_info as $value) {											
											?> 
											<tr>
												<td class="text-center"><?php echo $i;?></td>
												<td><?php echo $value['inv_date']; ?></td>
												<td><?php echo $value['order_number']; ?></td>
												<td><?php echo $value['guest_name'].'<br>'.$value['guest_mobile']; ?></td>
												<td><?php echo $value['ctests']; ?></td>
												<td class="text-center">
												<a href="<?php echo base_url('Lab/view_order/'.$value['billing_id']);?>"><i class="fa fa-eye"></i></a>
												<!--<a href="<?=base_url("Lab/edit_template/".$value['clinic_investigation_template_id'])?>" ><i class="fa fa-edit" style="font-size: 17px;color: #333333bf;" title="Edit"></i></a>	-->			
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
	</div>

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

