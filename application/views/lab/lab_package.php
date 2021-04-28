<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li class="active">Packages</li>
        </ol>
    </div>
</div>

<section class="main-content">
	<div class="row">
        <div class="col-md-12">
            <div class="card noCardPadding">

            	<div class="row col-md-12 page-title">
            		<div class="pull-left col-md-6">Lab Packages</div>
            		<div class="pull-right col-md-6 text-right actionButtons">
            			 <a href="<?= base_url('Lab/add_clinic_package'); ?>"><i class="fas fa-plus add"></i></a>
            		</div>
            	</div>

				<div class="tabs">
                    <!-- Nav tabs -->	
                            
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="home">
						    
							<div class="row">
								<div class="col-md-12">
									<table id="doctorlist" class="table customTable">
										<thead>
											<tr>
												<th class="text-center" style="width: 5%">S.No:</th>
												<th style="width: 30%">Package Name</th>
												<th class="text-center" style="width: 20%">Package Code</th>
												<th class="text-right" style="width: 15%">Price</th>										
												<th class="text-center" style="width: 15%">Actions</th>
											</tr>
										</thead>
										<tbody>
										   	<?php 
										   	$i=1;
										   	foreach($click_invg_package as $value){
												?> 
												<tr>
													<td class="text-center"><?php echo $i;?></td>
													<td><?php echo ucwords($value['package_name']); ?></td>
													<td class="text-center"><?php echo $value['item_code']; ?></td>
													<td class="text-right"><?php echo $value['price']; ?></td>
													<td class="text-center actions">
														<a href="<?=base_url("Lab/view_clinic_package/".$value['clinic_investigation_package_id'])?>" ><i class="fas fa-search viewSmall" style="font-size: 17px;color: #333333bf;" title="View"></i></a>
														<a href="<?=base_url("Lab/edit_clinic_package/".$value['clinic_investigation_package_id'])?>" ><i class="fas fa-pencil-alt editSmall" style="font-size: 17px;color: #333333bf;" title="Edit"></i></a>														
														<a href="<?php echo base_url('lab/clinic_package_delete/'.$value['clinic_investigation_package_id']);?>" onClick="return doconfirm();"><i class="fas fa-trash-alt deleteSmall"></i></a>
													</td>
												</tr>
										  		<?php 
										  		$i++;
										  	} ?>
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
      $('#doctorlist').DataTable();
  });
  function doconfirm(){
  if(confirm("Do You Want to delete Investigation")){
    return true;
  }else{
    return false;  
  }
  </script>
