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
          <li class="active">LAB PACKAGES</li>
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
								<a href="<?= base_url('Lab/add_clinic_package'); ?>" class="btn btn-primary btn-icon"><i class="fas fa-plus-circle"></i> ADD</a>
								</div>
							</div>
							
							<div class="row">
								<div class="col-md-12">
									<table id="doctorlist" class="table table-bordered dt-responsive">
										<thead>
											<tr>
												<th class="text-center">S.No:</th>
												<th>Package Name </th>
												<th>Price </th>												
												
												<th class="text-center"></th>
											</tr>
										</thead>
										<tbody>
										   	<?php $i=1; foreach ($click_invg_package as $value) {
											
											?> 
											<tr>
												<td class="text-center"><?php echo $i;?></td>
												<td><?php echo $value['package_name']; ?></td>
												<td><?php echo $value['price']; ?></td>
												<td class="text-center"><a href="<?=base_url("Lab/edit_clinic_package/".$value['clinic_investigation_package_id'])?>" ><i class="fa fa-edit" style="font-size: 17px;color: #333333bf;" title="Edit"></i></a>
												<a href="<?=base_url("Lab/view_clinic_package/".$value['clinic_investigation_package_id'])?>" ><i class="fa fa-eye" style="font-size: 17px;color: #333333bf;" title="View"></i>
												<a href="<?php echo base_url('lab/clinic_package_delete/'.$value['clinic_investigation_package_id']);?>" onClick="return doconfirm();"><i class="fas fa-trash"></i></a>
												</a>
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
  if(confirm("Do You Want to delete Investigation")){
    return true;
  }else{
    return false;  
  }
  </script>
