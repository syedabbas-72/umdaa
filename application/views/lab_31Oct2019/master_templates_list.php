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

                               
                                <li class="active">Lab Templates</li>
                            </ol>
                        </div>
                    </div>
<section class="main-content">
	<div class="row">
        <div class="col-md-12">
            <div class="card">
				<div class="card-header">

					<div class="col-lg-12 align-self-center text-right btnSection">
									<a href="<?= base_url('Lab/master_add_template'); ?>" class="btn btn-primary btn-icon"><i class="fas fa-plus-circle"></i> ADD</a>
								</div>
				</div>
						  
							
							<div class="row">
								<div class="col-md-12">
									<table id="doctorlist" class="table table-bordered dt-responsive">
										<thead>
											<tr>
												<th class="text-center" style="width:30px">S.No:</th>
												<th style="width: 200px;">Template Name </th>
												<th style="width: 150px;text-align: center;">Category </th>	
												<th style="width: 150px;" class="text-center"></th>
											</tr>
										</thead>
										<tbody>
										   	<?php $i=1; foreach ($clinic_templates as $value) {	?> 
											<tr>
												<td class="text-center"><?php echo $i;?></td>
												<td><?php echo $value['template_name']; ?></td>
												<td class="text-center"><?php echo $value['template_type']; ?></td>
												
												<td class="text-center" class="gridActions">
													<a class="btn btn-primary btn-xs" href="<?=base_url("Lab/mastertemplate_view/".$value['lab_template_id'])?>" ><i class="fa fa-eye"  title="View"></i></a>
													<a class="btn btn-primary btn-xs" href="<?=base_url("Lab/edit_mastertemplate/".$value['lab_template_id'])?>" ><i class="fa fa-edit"  title="Edit"></i></a>				
													<a class="btn btn-primary btn-xs" href="<?=base_url("Lab/mastertemplate_delete/".$value['lab_template_id'])?>" onclick="return doconfirm();"><i class="fas fa-trash"  title="Delete"></i></a>
													
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

