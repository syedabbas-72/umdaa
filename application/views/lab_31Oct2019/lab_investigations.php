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

            <li><a class="parent-item" href="<?php echo base_url("lab"); ?>">Lab</a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
           
            <li class="active">Investigations</li>
        </ol>

        <div class="pull-right tPadding">
			<a href="<?= base_url('Lab/add_clinic_investigation'); ?>" class="btn btn-primary btn-icon"><i class="fas fa-plus-circle"></i> Add New Investigation</a>
        </div>
    </div>
</div>

<section class="main-content">
	<div class="row">
        <div class="col-md-12">
            <div class="card noCardPadding">

            	<div class="pull-left page-title">Investigations</div>

				<div class="tabs">
                    <!-- Nav tabs -->	
			        
                            
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="home">
						    <!--<form method="post" action="<?php echo base_url('Pharmacy_orders/search_pharmacy'); ?>">
								<div class="row searchAction">
									<div class="col-md-11">
                                       <input type="text" placeholder="Search Drug by Trade name" name="search_pharmacy" id="search_pharmacy" onclick="tnamesearch();" />
                                   	</div>
                                   	<div class="col-md-1 buttonAction">
                                   		<button type="submit" class="btn btn-success" value="" id="search"/><i class="fas fa-check"></i></button>
                                	</div>
                                </div>
						    </form>-->
						        
							<!-- <div class="row">
								<div class="col-lg-12 align-self-center text-right btnSection">
								<a href="<?= base_url('Lab/add_clinic_investigation'); ?>" class="btn btn-primary btn-icon"><i class="fas fa-plus-circle"></i> ADD</a>
								</div>
							</div> -->
							
							<div class="row">
								<div class="col-md-12">
									<table id="doctorlist" class="table table-bordered dt-responsive">
										<thead>
											<tr>
												<th class="text-center">S.No:</th>
												<th>Investigation Name </th>
												<th>Item Code </th>												
												<th>Category</th>
												<th>Short Form</th>
												<th class="text-right">MRP</th>

												<th class="text-center">Method</th>

												<th class="text-center">Actions</th>
											</tr>
										</thead>
										<tbody>
										   	<?php $i=1; foreach ($labinvg_info as $value) {
											$otinfo = explode(",",$value['other_information']);
											?> 
											<tr>
												<td class="text-center"><?php echo $i;?></td>
												<td><?php echo $value['investigation']; ?></td>
												<td><?php echo $value['item_code']; ?></td>
												<td><?php echo $value['category']; ?></td>
												<td><?php echo $value['short_form']; ?></td>
												<td class="text-right"><?php echo "Rs. ".$value['price'].".00"; ?></td>
		
												<td class="text-center"><?php echo ucwords($value['method']); ?></td>
												<td class="text-center"><a href="<?=base_url("Lab/edit_investigation/".$value['clinic_investigation_id'])?>" ><i class="fa fa-edit" style="font-size: 17px;color: #333333bf;" title="Edit"></i></a>
													<?php if(hasPermission("Lab","p_delete")){ ?>
													<a onclick="return doconfirm()" href="<?=base_url("Lab/delete_lab_investigation/".$value['clinic_investigation_id'])?>" ><i class="fa fa-trash" style="font-size: 17px;color: #333333bf;" title="Edit"></i></a>
												<?php } ?>
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
  var doIt = confirm("Delete selected investigation ?");
  if(doIt){
    return true;
  }else{
    return false;  
  } 
 
}
  </script>