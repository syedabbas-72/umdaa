<style type="text/css">
	.btnSection{
		margin-bottom: 8px;
	}
</style>

<div class="row page-header no-background no-shadow margin-b-0">
	<div class="col-lg-6 align-self-center">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><?php echo $_SESSION['clinic_name']; ?></a></li>
			<li class="breadcrumb-item"><a href="<?php echo base_url('lab/templates'); ?>">LAB TEMPLATES</a></li>
			<li class="breadcrumb-item active">VIEW TEMPLATE</li>
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
						<form method="post" action="<?php echo base_url('Lab/add_clinic_template_parameters');?>">
						<input type="hidden" name="clinic_investigation_template_id" value="<?php echo $templateinfo->clinic_investigation_template_id; ?>" />
						<input type="hidden" name="template_type" value="<?php echo $templateinfo->template_type; ?>" />
							<table class="table table-bordered dt-responsive">
							<thead>
								<tr>
									<th class="text-center">Template Name</th>
									<th>Template Type </th>
								</tr>
							</thead>
							<tbody>
							<tr>
							<td><?php echo $templateinfo->template_name; ?></td>
							<td><?php echo $templateinfo->template_type; ?></td>
							</tr>
							</tbody>
							</table>
							<?php if($templateinfo->template_type=='Excel'){?>
							<div class="row">
								<div class="col-md-12">
									<table class="table table-bordered dt-responsive" id="orderlist_excel">
										<thead>
											<tr>												
												<th>Parameter</th>												
												<th>Value</th>												
												<th class="text-center">Low</th>
												<th class="text-center">High</th>	
												<th class="text-center">Unit</th>											
												<th class="text-center">Method</th>
												<th class="text-center">Other Information</th>
												<th class="text-center"></th>
											</tr>
										</thead>
										<tbody>
										<?php if(count($templatelineinfo)>0){?>
										<?php $i=1;foreach($templatelineinfo as $tresult){
										$otinfo = explode(",",$tresult->other_information);	
										?>
										<?php $otext='';foreach($otinfo as $oresult){ 
											$otext = $otext. $oresult."\r\n"; ?><?php 
										} ?>
											<tr id="orderlist_excel_<?php echo $i; ?>">												
												<td><input type="text" name="parameter[]" id="search_parameter_<?php echo $i; ?>" onclick="parametersearch('<?php echo $i; ?>');" value="<?php echo $tresult->parameter; ?>" /></td>						
												<td class="text-center"></td>
												<td><input type="text" name="low[]" id="low_<?php echo $i; ?>" value="<?php echo $tresult->low; ?>" /></td>
												<td><input type="text" name="high[]" id="high_<?php echo $i; ?>" value="<?php echo $tresult->high; ?>" /></td>
												<td><input type="text" name="unit[]" id="unit_<?php echo $i; ?>" value="<?php echo $tresult->unit; ?>" /></td>
												<td><input type="text" name="method[]" id="method_<?php echo $i; ?>" value="<?php echo $tresult->method; ?>" onclick="investigationmethodsearch(<?php echo $i; ?>)"; /></td>
												<td><textarea name="other_information[]" id="other_information_<?php echo $i; ?>" rows="5" cols="10"><?php echo $otext; ?></textarea></td>
												<td class="text-center"><button type="button" class="btn btn-sm btn-success" onclick="add_orderlist_excel('orderlist_excel');"><i class="fas fa-plus"></i></button><button class="btn btn-sm btn-danger" type="button" onclick="remove_orderlist_excel('orderlist_excel_<?php echo $i; ?>');"><i class="fa fa-minus"><i></i></i></button></td>
											</tr>
										<?php $i++; } ?>
										<?php }else{ ?>
										<tr id="orderlist_excel_1">												
												<td><input type="text" name="parameter[]" id="search_parameter_1" onclick="parametersearch('1');" /></td>						
												<td class="text-center"></td>
												<td><input type="text" name="low[]" id="low_1" /></td>
												<td><input type="text" name="high[]" id="high_1"  /></td>
												<td><input type="text" name="unit[]" id="unit_1"  /></td>
												<td><input type="text" name="method[]" id="method_1" onclick="investigationmethodsearch(1)"; /></td>
												<td><textarea name="other_information[]" id="other_information_1" rows="5" cols="10"></textarea></td>
												<td class="text-center"><button type="button" class="btn btn-sm btn-success" onclick="add_orderlist_excel('orderlist_excel');"><i class="fas fa-plus"></i></button></td>
											</tr>
										<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
							<?php }else if($templateinfo->template_type=='General'){ ?>
							<div class="row">
								<div class="col-md-12">
									<table class="table table-bordered dt-responsive" id="orderlist_general">
										<thead>
											<tr>												
												<th>Parameter</th>												
												<th>Remarks</th>
												<th class="text-center"></th>
											</tr>
										</thead>
										<tbody>	
										<?php if(count($templatelineinfo)>0){?>
										<?php $i=1;foreach($templatelineinfo as $tresult){?>
											<tr id="orderlist_general_<?php echo $i; ?>">												
												<td><input type="text" name="parameter[]" id="search_parameter_<?php echo $i; ?>" onclick="parametersearch('<?php echo $i; ?>');" value="<?php echo $tresult->parameter; ?>" /></td>						
												<td class="text-center"><textarea rows="5" cols="25" name="remarks[]"><?php echo $tresult->remarks; ?></textarea></td>											
												<td class="text-center"><button type="button" class="btn btn-sm btn-success" onclick="add_orderlist_general('orderlist_general');"><i class="fas fa-plus"></i></button><button class="btn btn-sm btn-danger" type="button" onclick="remove_orderlist_excel('orderlist_general_<?php echo $i; ?>');"><i class="fa fa-minus"><i></i></i></button></td>
											</tr>
										<?php $i++;} ?>
										<?php }else{ ?>
										<tr id="orderlist_general_1">												
												<td><input type="text" name="parameter[]" id="search_parameter_1" onclick="parametersearch('1');" /></td>						
												<td class="text-center"><textarea rows="5" cols="25" name="remarks[]"></textarea></td>												
												<td class="text-center"><button type="button" class="btn btn-sm btn-success" onclick="add_orderlist_general('orderlist_general');"><i class="fas fa-plus"></i></button></td>
											</tr>
										<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
							<?php } ?>
							<table id="orderlist1" class="table table-bordered dt-responsive nowrap">
						<tbody>						
						<tr><td colspan='5'><input type="submit" value="Submit" class="btn btn-success"></td></tr>
						</tbody>
						</table>
						</form>
                        </div>
                        
                    </div>
                </div>
			</div>
		</div>
	</div>
  </section>
<script>
  
  function doconfirm(){
  if(confirm("Do You Want to delete Investigation")){
    return true;
  }else{
    return false;  
  } 
}
  </script>
