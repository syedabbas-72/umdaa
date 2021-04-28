<style type="text/css">
	.btnSection{
		margin-bottom: 8px;
	}
</style>

<div class="row page-header no-background no-shadow margin-b-0">
	<div class="col-lg-6 align-self-center">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><?php echo $_SESSION['clinic_name']; ?></a></li>
			<li class="breadcrumb-item active">LAB TEMPLATES</li>
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
								<a href="<?= base_url('Lab/add_template'); ?>" class="btn btn-primary btn-icon"><i class="fas fa-plus-circle"></i> ADD</a>
								</div>
							</div>
							
							<div class="row">
								<div class="col-md-12">
									<table id="doctorlist" class="table table-striped dt-responsive">
										<thead>
											<tr>
												<th class="text-center">S.No:</th>
												<th>Template Name </th>
												<th>Category </th>								
												
												<th class="text-center"></th>
											</tr>
										</thead>
										<tbody>
										   	<?php $i=1; foreach ($clinic_templates as $value) {											
											?> 
											<tr>
												<td class="text-center"><?php echo $i;?></td>
												<td><?php echo $value['template_name']; ?></td>
												<td><?php echo $value['template_type']; ?></td>
												
												<td class="text-center">
												<a href="<?=base_url("Lab/template_view/".$value['clinic_investigation_template_id'])?>" ><i class="fa fa-eye" style="font-size: 17px;color: #333333bf;" title="View"></i></a>
												<a href="<?=base_url("Lab/edit_template/".$value['clinic_investigation_template_id'])?>" ><i class="fa fa-edit" style="font-size: 17px;color: #333333bf;" title="Edit"></i></a>				
												<a href="<?=base_url("Lab/template_delete/".$value['clinic_investigation_template_id'])?>" onclick="return doconfirm();"><i class="fas fa-trash" style="font-size: 17px;color: #333333bf;" title="Delete"></i></a>
												
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
