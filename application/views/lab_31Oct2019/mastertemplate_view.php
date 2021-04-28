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

                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("lab/master_template"); ?>">Lab Templates</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                               
                                <li class="active">View Template</li>
                            </ol>
                        </div>
                    </div>

<section class="main-content">
	<div class="row">
        <div class="col-md-12">
            <div class="card noCardPadding">
				<div class="tabs">
                    <!-- Nav tabs -->	
                    
                            
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="home">						    
						<form method="post" action="<?php echo base_url('Lab/add_template_parameters');?>">
						<input type="hidden" name="lab_template_id" value="<?php echo $templateinfo->lab_template_id; ?>" />
						<input type="hidden" name="template_type" value="<?php echo $templateinfo->template_type; ?>" />
							<table class="table table-bordered dt-responsive">
								<thead>
									<tr>
										<th>Template : <?php echo $templateinfo->template_name; ?></th>
										<th>Type : <?php echo $templateinfo->template_type; ?></th>
									</tr>
								</thead>
							</table>
							<?php if($templateinfo->template_type=='Excel'){?>
							<div class="row">
								<div class="col-md-12">
									<table class="table table-bordered dt-responsive" id="orderlist_excel">
										<thead>
											<tr>												
												<th style="width:80%">Parameter</th>	
												<th></th>
											</tr>
										</thead>
										<tbody class="row_position">
										<?php if(count($templatelineinfo)>0){?>
										<?php $i=1;foreach($templatelineinfo as $tresult){
										
										?>
										
											<tr class="type_excel" param_id="<?php echo $tresult->lab_template_line_item_id; ?>" id="orderlist_excel_<?php echo $i; ?>">												
												<td>
													<input class="form-control" type="text" name="parameter[]" id="search_parameter_<?php echo $i; ?>" onclick="mparametersearch('<?php echo $i; ?>');" value="<?php echo $tresult->parameter; ?>" />
												</td>																		
												<td class="text-center">
													<button type="button" class="btn btn-sm btn-success" onclick="add_orderlist_excel_m('orderlist_excel');"><i class="fas fa-plus"></i></button>
													&nbsp;&nbsp;
													<button class="btn btn-sm btn-danger" type="button" onclick="remove_orderlist_excel('orderlist_excel_<?php echo $i; ?>');"><i class="fa fa-minus"></i></button>
												</td>
											</tr>
										<?php $i++; } ?>
										<?php }else{ ?>
										<tr id="orderlist_excel_1">												
												<td><input type="text" name="parameter[]" id="search_parameter_1" onclick="mparametersearch('1');" /></td>						
												
												<td class="text-center"><button type="button" class="btn btn-sm btn-success" onclick="add_orderlist_excel_m('orderlist_excel');"><i class="fas fa-plus"></i></button></td>
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
												<th style="width: 20%">Parameter</th>											
												<th style="width:70%">Remark</th>
												<th class="text-center">Actions</th>
											</tr>
										</thead>
										<tbody  class="row_position">	
										<?php if(count($templatelineinfo)>0){?>
										<?php $i=1;foreach($templatelineinfo as $tresult){?>
											<tr  class="type_general" param_id="<?php echo $tresult->lab_template_line_item_id; ?>" id="orderlist_general_<?php echo $i; ?>">			
												<td><input class="form-control"  type="text" name="parameter[]" id="search_parameter_<?php echo $i; ?>" value="<?php echo $tresult->parameter; ?>" /></td>						
												<td class="text-center"><textarea class="form-control" rows="5" cols="25" name="remarks[]"><?php echo $tresult->remarks; ?></textarea></td>											
												<td class="text-center"><button type="button" class="btn btn-sm btn-success" onclick="add_orderlist_general_m('orderlist_general');"><i class="fas fa-plus"></i></button>&nbsp;&nbsp;<button class="btn btn-sm btn-danger" type="button" onclick="remove_orderlist_excel('orderlist_general_<?php echo $i; ?>');"><i class="fa fa-minus"><i></i></i></button></td>
											</tr>
										<?php $i++;} ?>
										<?php }else{ ?>
										<tr id="orderlist_general_1">												
												<td><input  class="form-control" type="text" name="parameter[]" id="search_parameter_1" /></td>						
												<td style="width:100%" class="text-center"><textarea rows="5" cols="25" name="remarks[]"></textarea></td>												
												<td class="text-center">
													<button type="button" class="btn btn-sm btn-success" onclick="add_orderlist_general_m('orderlist_general');"><i class="fas fa-plus"></i></button></td>
											</tr>
										<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
							<?php } ?>
							<table id="orderlist1" class="table table-striped dt-responsive nowrap">
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

<script type="text/javascript">
	var base_url = '<?php echo base_url(); ?>';
    $( ".row_position" ).sortable({
        delay: 150,
        stop: function() {
            var selectedData = new Array();
            $('.type_excel').each(function() {
                selectedData.push($(this).attr("param_id"));
            });

            updateOrder(selectedData);
        }
    });

    $( ".row_position" ).sortable({
        delay: 150,
        stop: function() {
            var selectedData = new Array();
            $('.type_general').each(function() {
                selectedData.push($(this).attr("param_id"));
            });

            updateOrder(selectedData);
        }
    });


    function updateOrder(data) {
        $.ajax({
            url:base_url+"lab/update_parameter_order",
            type:'post',
            data:{position:data},
            success:function(){
                alert('Order successfully saved');
            }
        })
    }
</script>