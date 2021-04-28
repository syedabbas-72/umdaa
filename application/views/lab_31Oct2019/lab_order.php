 <div class="page-bar">
    <div class="page-title-breadcrumb">
        <?php /*
        <div class=" pull-left">
            <div class="page-title"><?php echo $_SESSION['clinic_name']; ?></div>
        </div>
        */ ?>
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>

            <li><a class="parent-item" href="<?php echo base_url("lab"); ?>">Lab</a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
           
            <li class="active">New Order</li>
        </ol>
    </div>
</div>
<section class="main-content">
	<div class="row">
	    <div class="col-md-12">
	        <div class="card">
	        	<div class="card-body">	        						
					<div class="row">
						<div class="col-sm-12">
							Please specify the name & mobile no. of the customer
							<div class="row">
								<div class="col-md-4">
									<input class="form-control" type="text" name="pname" style="text-transform: capitalize;" placeholder="Name" required>
								</div>
								<div class="col-md-4">
									<input class="form-control" type="text" style="text-transform: capitalize;" name="pmobile" placeholder="Mobile" required>
								</div>
							</div>
							<div class="row">
								<span id="error-msg" class="col-md-12" style="padding: 10px"></span>
								<div class="col-md-11">
									<input  class="form-control" type="text" name="search_investigation" placeholder="Search by investigation name" id="search_investigation" onclick="clinicinvestigationsearch();" />
							    </div>
							    <div class="col-md-1 buttonAction">
							    	<button type="button" class="btn btn-success" value="" id="drugAddBtn" onclick="add_investigation_order_row('orderlist');"/><i class="fas fa-plus"></i></button>
							    </div>		
							</div>
						</div>							
				    </div>
				</div>
			</div>

			<div class="card"> 
				<div class="card-body">   	
					<form method="post" action="<?php echo base_url('Lab/save_lab_order'); ?>">
						<div class="row" style="display:none; border:5px solid red" id="listdiv">
							<div class="col-md-12">	
								<table id="orderlist" class="table table-striped dt-responsive nowrap" >
									<thead>
										<tr>
											<th style="width: 250px">Investigation / Package </th>
											<th style="width: 150px">Item Code</th>
											<th>Category</th>
											<th style="width: 100px">Short Form</th>
											<th style="width: 100px">MRP</th>									
											<th style="width: 50px"></th>																
										</tr>
									</thead>
									<tbody>
										<!-- empty -->
									</tbody>
								</table>
										
								<table id="orderlist1" class="table table-striped dt-responsive nowrap" >
									<tbody>
										<tr>
											<td>
												<input type="hidden" name="iapdis" value="0">
												<input class="form-group" type="checkbox" value="1" onclick="ienable_discount('<?php echo $cl_discount; ?>');" id="iapdis" name="iapdis" />&nbsp;Apply Discount
											</td>
										</tr>
										<tr>
											<td style="width: 250px">
												Payment
												<select id='ptm' name='ptm' onchange="getptm_textbox();">
												<option value=''>--Select--</option>
												<option value="Advance">Advance</option>
												<option value="Net">Net</option>
												</select>
											</td>
											<td style="width: 650px">
											<input type="text" id="ptm_txt" name="ptm_txt" style="display:none" />
											</td>
											<td colspan="3" style="width: 250px">Total
											</td>
											<td style="width: 100px"><label id="p_total"></label>
											<input type="hidden" id="ip_total" value="0" /> 
											</td>
											<td style="width: 50px"></td>
										</tr>
										<tr>
											<td>
											<input type="hidden" id="lab_discount" name="lab_discount" value="<?php echo $cl_discount; ?>" />
												<input type="submit" value="Submit" class="btn btn-success">
											</td>
										</tr>
									</tbody>
								</table>
						    </div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>

	<div id="myModal" class="modal fade" role="dialog">
	  	<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">	
				<input type="hidden" name="d_id" id="d_id" value="" />
			    <div class="modal-header">
			    <button type="button" class="close" data-dismiss="modal">&times;</button>
			    <h4 class="modal-title">Batches</h4>
		    </div>

		    <div class="modal-body">
				<div class="row" id="binfo"> </div>       
			</div>

			<div class="modal-footer">	   	
				<input type="button" value="Submit" class="btn btn-success" onclick="storelinedetails();" />
		   </div>
		</div>
 		<!-- Modal content-->
   	</div>
</div>
<script>
function getptm_textbox()
{
	val = $("#ptm").val();
	if(val!='')
		$("#ptm_txt").css("display","block");
	else
		$("#ptm_txt").css("display","none");
}
</script>
