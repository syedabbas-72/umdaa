<div class="page-bar">
	<div class="page-title-breadcrumb">
		<ol class="breadcrumb page-breadcrumb pull-left">
			<li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
			<li>Lab&nbsp;<i class="fa fa-angle-right"></i></li>
			<li><a href="<?= base_url('Lab/lab_packages'); ?>">Packages</a>&nbsp;<i class="fa fa-angle-right"></i></li>
			<li class="active">View</li>
		</ol>
	</div>
</div>

<section class="main-content">
	<div class="row">
		<div class="col-md-12">
			<div class="card noCardPadding">

				<div class="row page-title">
					<div class="pull-left col-md-6">Package &nbsp; <span><i class="fas fa-caret-right"></i></span> &nbsp;<?php echo $invgpinfo->package_name; ?> ( <i class="fas fa-rupee-sign"></i> <?php echo $invgpinfo->price; ?> )</div>
					<div class="pull-right col-md-6 text-right">
						<a href="<?= base_url('Lab/add_clinic_package_lineitems/'.$invgpinfo->clinic_investigation_package_id); ?>" class="btn btn-primary btn-icon"><i class="fas fa-plus-circle"></i> ADD INVESTIATIONS TO THE PACKAGE</a>
					</div>
				</div>

				<div class="tabs">
					<!-- Nav tabs -->				        

					<!-- Tab panes -->
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="home">							
							<div class="row">
								<div class="col-md-12">
									<table id="investigationList_tbl" class="table customTable">
										<thead>
											<tr>
												<th style="width: 5%" class="text-center">S.No:</th>
												<th style="width: 25%">Investigation Name</th>
												<th style="width: 25%">Item Code </th>												
												<!-- <th style="width: 10%">Short Form</th> -->
												<th style="width: 10%" class="text-center">Actions</th>
											</tr>
										</thead>
										<tbody>
											<?php 
											$i=1; 
											foreach ($plineitems as $value) {
												$otinfo = explode(",",$value['other_information']);
												?> 
												<tr>
													<td class="text-center"><?php echo $i;?></td>
													<td><?php echo $value['investigation']; ?></td>
													<td><?php echo $value['item_code']; ?></td>
													<!-- <td><?php echo $value['short_form']; ?></td> -->
													<td class="text-center">
														<a href="<?php echo base_url('lab/clinic_package_lineitem_delete/'.$value['investigation_package_line_item_id']."/".$invgpinfo->clinic_investigation_package_id);?>" onClick="return doconfirm();"><i class="fas fa-trash"></i></a>
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

<script type="text/javascript">
	$(document).ready(function () {
		$('#investigationList_tbl').dataTable();
	});
	function doconfirm(){
		if(confirm("Do You Want to delete Investigation")){
			return true;
		}else{
			return false;  
		} 
	}
</script>