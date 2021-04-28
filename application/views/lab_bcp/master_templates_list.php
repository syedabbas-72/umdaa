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
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="#"><?php if($_SESSION['clinic_id'] == 0){ echo "UMDAA HEALTH CARE"; }else{ echo $_SESSION['clinic_name']; } ?></a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>
          <li><a class="parent-item" href="#">MASTERS</a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>         
          <li class="active">LAB TEMPLATES</li>
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
						    
							<div class="row title">
								<div class="col-lg-8">
									<h3>Lab Template Master</h3>
								</div>
								<div class="col-lg-4 align-self-center text-right btnSection">
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
													<a href="<?=base_url("Lab/mastertemplate_view/".$value['lab_template_id'])?>" ><i class="fa fa-eye" style="font-size: 18px;color: #333333bf;" title="View"></i></a>
													<a href="<?=base_url("Lab/edit_mastertemplate/".$value['lab_template_id'])?>" ><i class="fa fa-edit" style="font-size: 18px;color: #333333bf;" title="Edit"></i></a>				
													<a href="<?=base_url("Lab/mastertemplate_delete/".$value['lab_template_id'])?>" onclick="return doconfirm();"><i class="fas fa-trash" style="font-size: 17px;color: #333333bf;" title="Delete"></i></a>
													
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

