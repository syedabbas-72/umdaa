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
          <li class="active">LAB INVESTIGATIONS</li>
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
						        <?php /*if($pharmacy!=''){?>
							    <div class="row">
								   <div class="col-md-12">
									<!-- <div class="card">
										<div class="card-body"> -->
										<?php if(count($presult)>0){?>
											<table id="doctorlist" class="table table-striped dt-responsive nowrap">
												<thead>
													<tr>
														<th>S.No:</th>
														<th>Drug Name</th>
														<th>Batch No</th>
														<th>Quantity</th>
														<th>Expired Date</th>															
													</tr>
												</thead>
												<tbody>
												<?php $sno=1;foreach($presult as $result){ ?>
												<tr>
												<td><?php echo $sno; ?></td>
												<td><?php echo $result['drug_name']; ?></td>
												<td><?php echo $result['batch_no']; ?></td>
												<td><?php echo $result['quantity']; ?></td>
												<td><?php echo $result['edate']; ?></td>
												</tr>
												<?php $sno++;} ?>
												</tbody>
												</table>
										<?php }else{ ?>
										<p style="color:red">No Details Found</p>
										<?php } ?>
											<!-- </div>
										</div> -->
									</div>
									</div>
								<?php }*/ ?>
								<div class="card-body">
							<div class="row">
								<div class="col-lg-12 align-self-center text-right btnSection">
								<a href="<?= base_url('Lab/add_clinic_investigation'); ?>" class="btn btn-primary btn-icon"><i class="fas fa-plus-circle"></i> ADD</a>
								</div>
							</div>
							
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
												<th class="text-center">MRP</th>
												<th class="text-center">Low Range</th>
												<th class="text-center">High Range</th>	
												<th class="text-center">Units</th>											
												<th class="text-center">Method</th>
												<th class="text-center">Other Information</th>
												<th class="text-center"></th>
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
												<td class="text-center"><?php echo $value['price']; ?></td>
												<td><?php echo $value['low_range']; ?></td>
												<td><?php echo $value['high_range']; ?></td>
												<td><?php echo $value['units']; ?></td>
												<td><?php echo $value['method']; ?></td>
												<td><?php foreach($otinfo as $oresult){ ?>
												<?php echo $oresult."<br />"; ?><?php } ?></td>
												<td class="text-center"><a href="<?=base_url("Lab/edit_investigation/".$value['clinic_investigation_id'])?>" ><i class="fa fa-edit" style="font-size: 17px;color: #333333bf;" title="Edit"></i></a></td>
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
  </script>