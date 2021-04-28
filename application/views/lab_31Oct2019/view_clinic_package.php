<style type="text/css">
	.btnSection{
		margin-bottom: 8px;
	}
</style>

<div class="row page-header no-background no-shadow margin-b-0">
	<div class="col-lg-6 align-self-center">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><?php echo $_SESSION['clinic_name']; ?></a></li>
			<li class="breadcrumb-item"><a href="<?php echo base_url('lab/packages'); ?>">LAB PACKAGES</a></li>
			<li class="breadcrumb-item active">VIEW PACKAGE</li>
		</ol>
	</div>
	<div class="col-lg-6 align-self-center text-right">
	     <!--<a href="<?= base_url('Pharmacy_orders/drug_add'); ?>" class="btn btn-primary btn-rounded box-shadow btn-icon"><i class="fa fa-plus"></i> ADD NEW DRUG</a>-->
		 <!--<a href = ""  class="btn btn-primary btn-rounded box-shadow btn-icon" data-toggle="modal" data-target="#exampleModalCenter">Bulk Upload</a> -->
	</div>
</div><section class="main-content">
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
								<a href="<?= base_url('Lab/add_clinic_package_lineitems/'.$invgpinfo->clinic_investigation_package_id); ?>" class="btn btn-primary btn-icon"><i class="fas fa-plus-circle"></i> ADD</a>
								</div>
							</div>
							<table class="table table-striped dt-responsive">
							<thead>
								<tr>
									<th class="text-center">Package Name</th>
									<th>Price </th>
								</tr>
							</thead>
							<tbody>
							<tr>
							<td><?php echo $invgpinfo->package_name; ?></td>
							<td><?php echo $invgpinfo->price; ?></td>
							</tr>
							</tbody>
							</table>
							<div class="row">
								<div class="col-md-12">
									<table id="doctorlist" class="table table-striped dt-responsive">
										<thead>
											<tr>
												<th class="text-center">S.No:</th>
												<th>Investigation Name </th>
												<th>Item Code </th>												
												<th>Category</th>
												<th>Short Form</th>
												<!--<th class="text-center">MRP</th>-->
												<th class="text-center">Low Range</th>
												<th class="text-center">High Range</th>	
												<th class="text-center">Units</th>											
												<th class="text-center">Method</th>
												<th class="text-center">Other Information</th>
												<th class="text-center"></th>
											</tr>
										</thead>
										<tbody>
										   	<?php $i=1; foreach ($plineitems as $value) {
											$otinfo = explode(",",$value['other_information']);
											?> 
											<tr>
												<td class="text-center"><?php echo $i;?></td>
												<td><?php echo $value['investigation']; ?></td>
												<td><?php echo $value['item_code']; ?></td>
												<td><?php echo $value['category']; ?></td>
												<td><?php echo $value['short_form']; ?></td>
												<!--<td class="text-center"><?php echo $value['price']; ?></td>-->
												<td><?php echo $value['low_range']; ?></td>
												<td><?php echo $value['high_range']; ?></td>
												<td><?php echo $value['units']; ?></td>
												<td><?php echo $value['method']; ?></td>
												<td><?php foreach($otinfo as $oresult){ ?>
												<?php echo $oresult."<br />"; ?><?php } ?></td>
												<td class="text-center"><a href="<?php echo base_url('lab/clinic_package_lineitem_delete/'.$value['investigation_package_line_item_id']."/".$invgpinfo->clinic_investigation_package_id);?>" onClick="return doconfirm();"><i class="fas fa-trash"></i></a></td>
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
  if(confirm("Do You Want to delete Investigation")){
    return true;
  }else{
    return false;  
  } 
}
  </script>