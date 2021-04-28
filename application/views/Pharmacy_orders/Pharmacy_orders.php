<?php
$clinic_id = $this->session->userdata('clinic_id');
?>
<style type="text/css">
	.btnSection{
		margin-bottom: 8px;

	}
	.table > .thdClr{
	background: #f6f6f6;
	text-transform: uppercase;
}
.sorting_1
{
	background: transparent !important;
}
#H{
	display:none !important;
}
</style>

<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li class="active">Pharmacy Inventory</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card noCardPadding">
			<div class="tabs">
                <!-- Nav tabs -->	
                <ul class="nav nav-tabs tabAction">
                    <li class="nav-item" role="presentation"><a class="nav-link  active" href="#home" aria-controls="home" role="tab" data-toggle="tab"><span style="color:black;">Drugs Inventory</span> <span class="appbadge-app"><?=sizeof($parinfo)?></span></a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link" href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><span style="color:black;">Drugs Shortage</span> <span class="appbadge-grey"><?=sizeof($shortage)?></span></a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link" href="#messages" aria-controls="messages" role="tab" data-toggle="tab"><span style="color:black;">Drugs Expiring soon</span> <span class="appbadge-orange"><?=sizeof($sexpired)?></span></a></li>
					 <li class="nav-item" role="presentation"><a class="nav-link" href="#notif" aria-controls="messages" role="tab" data-toggle="tab"><span style="color:black;">Drugs Expired</span> <span class="appbadge-danger"><?=sizeof($expired)?></span></a></li>
                </ul>
                        
                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="home">					
						<div class="col-lg-12 align-self-center text-right btnSection">
							<button class="btn btn-success btn-icon printInventory"><i class="fas fa-print"></i> Print Inventory <i class="fa fa-spinner fa-spin spinloader"></i></button>
							<a href="<?= base_url('Pharmacy_orders/pharmacy_add'); ?>" class="btn btn-primary btn-icon "><i class="fas fa-plus-circle"></i> ADD</a>
						</div>
					
						<div class="row">
							<div class="col-md-12">
								<table id="inventoryList" class="table table-bordered dt-responsive customTable">
									<thead class="thdClr">
										<tr>
											<th>S#</th>
											<th>Trade Name (Formulation)<br>Composition</th>
											<th>Batch#<br> EXP.</th>
											<th>Ava. Qty</th>
											<th>MRP - PACK SIZE</th>
											<th>HSN CODE</th>	
											<th>IGST</th>
											<th>CGST</th>
											<th>SGST</th>
											<th>Max. Disc</th>
											<th>RL</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
									   	<?php $i=1; foreach ($parinfo as $value) {
										$today = strtotime(date("Y-m-d"));	
										$edate = strtotime($value['expiry_date']);
										$nxt_date = strtotime("+3 month");
										?> 
										<tr>
											<td><?php echo $i;?></td>
											<td>
												<?php echo '<span class="">'.$value['trade_name'].'</span> <span class="formulation">'.strtoupper($value['formulation']).'</span><br>'.$value['composition'];
												?>
												<!-- <span class="formulation"><?=$value['drug_id']?></span> -->
												<?php
												if($edate<$today)
												{
													$title = " ";
													echo "<font class='appbadge-danger pull-right' style='padding:5px'>".$title."</font>";
												}
												elseif($edate<$nxt_date)	
												{
													$title = " "; 
													echo "<font class='appbadge-orange pull-right' style='padding:5px'>".$title."</font>";
												}
												elseif($value['oqty']<$value['reorder_level']) 
												{
													$title = " "; 
													echo "<font class='appbadge-grey pull-right' style='padding:5px'>".$title."</font>";													
												}
												?>
												
											</td>
											<td><?php echo '<span style="font-weight:600">'.strtoupper($value['batch_no']).'</span><br>'.date("M. 'y",strtotime($value['expiry_date']));?></td>
											<td><?php echo $value['oqty']; ?></td>
											<td><?php echo '<span style="font-weight:600">'.round($value['mrp'],2).'</span> - '.$value['pack_size']; ?></td>
											<td><?php echo $value['hsn_code']; ?></td>
											<td><?php echo $value['igst']; ?></td>
											<td><?php echo $value['cgst']; ?></td>
											<td><?php echo $value['sgst']; ?></td>
											<td><?php echo $value['disc']; ?></td> 
											<td><?php echo $value['reorder_level']; ?></td>
											<td class="text-center actions" style="padding: 0px 10px !important">
												<a href="<?=base_url("Pharmacy_orders/edit_order/".$value['drug_id']."/".$value['batch_no'])?>" ><i class="fas fa-pencil-alt editSmall" title="Edit"></i></a>
												<?php
												// echo $permissions->pharmacy_del_access;
												if($permissions->pharmacy_del_access == "1"){
													?>
													<a href="<?=base_url("Pharmacy_orders/delete_order/".$value['clinic_pharmacy_inventory_inward_id'])?>" onclick="return doconfirm();"><i class="fas fa-trash-alt deleteSmall"></i></a>
													<?php
												}
												?>
												
											</td>
										</tr>
								  		<?php $i++;} ?>
									</tbody>
								</table>
							</div>
						</div>							
                    </div>
                    <div role="tabpanel" class="tab-pane" id="profile">
                        <div class="col-md-12">
							<form method="post" action="<?php echo base_url('Pharmacy_orders/raise_shortage_indent'); ?>">
								<div class="row">
									<div class="col-md-12">
										<a class="pull-left btn btn-primary" href="<?=base_url('Indent')?>">Rasie Indent</a>
										<button type="button" class="btn btn-success pull-right printShortage"><i class="fa fa-print"></i> Print Shortage  <i class="fa fa-spinner fa-spin shortagespinloader"></i></button>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12" id="btn_show" style="display:none">
										<input type="submit" class="btn btn-app pull-right" value="Raise Indent" id="rind" />
									</div>
								</div>
								<table class="table dataTable table-bordered dt-responsive  customTable">
									<thead class="thdClr">
										<tr>
											<!-- <th><input type="checkbox" id="all" onClick="toggle(this)"></th> -->
											<th>S.No:</th>
											<th>Drug Name</th>	
											<th>Batch No</th>
											<th>Quantity</th>
											<th>Status</th>
											<!-- <th>Required Quantity</th>	
											<th>Vendor</th>	 -->
										</tr>
									</thead>
									<tbody>
									<?php $ssno=1;
									foreach($shortage as $result){ 
										$indentInfo = $this->db->query("select * from pharmacy_indent_line_items pl,pharmacy_indent p where p.pharmacy_indent_id=pl.pharmacy_indent_id and p.clinic_id='".$clinic_id."' and pl.drug_id='".$result['drug_id']."' and pl.status='1'")->row();
										$status = '';
										if(sizeof($indentInfo) != 0)
										{
											$status = "Indent Raised";
										}
										$c_date = date("Y-m-d");
										?>
									<tr <?=($c_date>=$result['edate'])?'class="bg-danger"':''?>>
									<!-- <td><input type="checkbox" name="all_check" id="rchk_<?php echo $ssno; ?>" onclick="enable_textbox('<?php echo $ssno; ?>');" /></td> -->
									<td><input type="hidden" name="drgid[]" value="<?php echo $result['drug_id']; ?>" /><?php echo $ssno; ?></td>
									<td><?php echo $result['drug_name']; ?></td>
									<td><?php echo strtoupper($result['batch_no']); ?></td>
									<!-- <td><?=($c_date>=$result['edate'])?'0':$result['quantity']?></td> -->
									<td><?=($result['quantity']<0)?'0':$result['quantity']?></td>
									<td><?=@$status?></td>
									<!-- <td><input type="text" onkeypress="numeric();" id="rqty_<?php echo $ssno; ?>" name="rqty[]" class="input-small form-control"  /></td>
									<td>
										<select class="form-control input-small" required="" name="vendor_<?=$result['drug_id']?>">
											<option selected="" disabled="">Select Vendor</option>
										<?php

										foreach ($vendor_list as $value) {
											?>
											<option value="<?=$value->vendor_id?>" <?=($value->vendor_id==$result['vendor_id'])?'selected':''?>><?=$value->vendor_storeName.", ".$value->vendor_location?></option>
											<?php
										}
										?>
										</select>
									</td> -->
									</tr>
									<?php $ssno++;} ?>
									</tbody>
									</table>
							</form>
						</div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="messages">
                      <div class="col-md-12">
								<div class="row">
									<div class="col-md-12">
										<button type="button" class="btn btn-success pull-right printExpSoon"><i class="fa fa-print"></i> Print Expiring Soon  <i class="fa fa-spinner fa-spin expsoonspinloader"></i></button>
									</div>
								</div>
								<table class="table dataTable table-bordered dt-responsive  customTable">
									<thead>
										<tr>
											<th>S.No:</th>
											<th>Drug Name</th>
											<th>Batch No</th>
											<th>Quantity</th>
											<th>Expiry Date</th>		
											<th>Actions</th>													
										</tr>
									</thead>
									<tbody>
									<?php $ssno=1;foreach($sexpired as $result){
										$today = strtotime(date("Y-m-d"));	
										$edate = strtotime($result['edate']);
										$nxt_date = strtotime("+1 month");
										$class = "";$textClass="";
										if($edate<$nxt_date)	
										{
											$class="class='appbadge-orange'";
											$textClass = "style='color:#fff !important'";
										}	
																		
									?>
									<tr <?=$class?>>
									<td><?php echo $ssno; ?></td>
									<td><?php echo '<span class="trade_name " '.$textClass.'>'.$result['drug_name'].'</span> <span class="formulation">'.strtoupper($result['formulation']).'</span>';?></td>
									<td><?php echo strtoupper($result['batch_no']); ?></td>
									<td><?php echo ($result['quantity']<0)?'0':$result['quantity']; ?></td>
									<td><?php echo date("d-m-Y",strtotime($result['edate'])); ?></td>
									<td class="actions"><a href="<?=base_url("Pharmacy_orders/edit_order/".$result['drug_id']."/".$result['batch_no'])?>" ><i class="fas fa-pencil-alt editSmall" style="font-size: 17px;color: #333333bf;" title="Edit"></i></a>
									<!-- <a href="<?=base_url("Pharmacy_orders/delete_order/".$result['clinic_pharmacy_inventory_inward_id'])?>" onclick="return doconfirm();"><i class="fas fa-trash-alt deleteSmall"></i></a> -->
									</td>
									</tr>
									<?php $ssno++;} ?>
									</tbody>
									</table>
						</div>
                    </div>
					<div role="tabpanel" class="tab-pane" id="notif">                                      
					  	<div class="col-md-12">
							<div class="row">
								<div class="col-md-12">
									<button type="button" class="btn btn-success pull-right printExpired"><i class="fa fa-print"></i> Print Expired  <i class="fa fa-spinner fa-spin expiredspinloader"></i></button>
								</div>
							</div>
							<table id="doctorlist" class="table dataTable table-bordered dt-responsive  customTable">
								<thead class="thdClr">
									<tr>
										<th>S.No</th>
										<th>Drug Name</th>
										<th>Batch No</th>
										<th>Quantity</th>
										<th>Expired Date</th>	
										<th>Actions</th>														
									</tr>
								</thead>
								<tbody>
									<?php $sno=1;foreach($expired as $result){ ?>
									<tr>
									<td><?php echo $sno; ?></td>
									<td><?php echo '<span class="trade_name">'.$result['drug_name'].'</span> <span class="formulation">'.strtoupper($result['formulation']).'</span>';?></td>
									<td><?php echo strtoupper($result['batch_no']); ?></td>
									<td><?php echo ($result['quantity']<0)?'0':$result['quantity']; ?></td>
									<td><?php echo date("d-m-Y",strtotime($result['edate'])); ?></td>
									<td class="actions"><a href="<?=base_url("Pharmacy_orders/edit_order/".$result['drug_id']."/".$result['batch_no'])?>" ><i class="fas fa-pencil-alt editSmall" style="font-size: 17px;color: #333333bf;" title="Edit"></i></a>
									<!-- <a href="<?=base_url("Pharmacy_orders/delete_order/".$result['clinic_pharmacy_inventory_inward_id'])?>" onclick="return doconfirm();"><i class="fas fa-trash-alt deleteSmall"></i></a> -->
									</td>
									</tr>
									<?php $sno++;} ?>
								</tbody>
							</table>
						</div>									  
                    </div>
                </div>
            </div>
		</div>
	</div>
</div>

<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLongTitle">Upload File</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<?php
				$output = ''; 
				$output .= form_open(base_url('Pharmacy_orders/bulk_save'), 'class="form-horizontal" enctype="multipart/form-data"');  
				$output .= '<div class="row">';
				$output .= '<div class="col-lg-12 col-sm-12"><div class="form-group">';
				$output .= form_label('Choose file', 'file');
				$data = array(
					'name' => 'userfile',
					'id' => 'userfile',
					'class' => 'form-control filestyle',
					'value' => '',
					'data-icon' => 'false'
				);
				$output .= form_upload($data);
				$output .= '</div> <span style="color:red;">*Please choose an Excel file(.xls or .xlxs) as Input</span></div>';
				$output .= '<div class="col-lg-12 col-sm-12"><div class="form-group text-right">';
				$data = array(
					'name' => 'importfile',
					'id' => 'importfile-id',
					'class' => 'btn btn-primary',
					'value' => 'Import',
				);
				$output .= form_submit($data, 'Import Data');
				$output .= '</div></div></div>';
				$output .= form_close();
				echo $output;
				?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('.spinloader').hide();
		$('.shortagespinloader').hide();
		$('.expsoonspinloader').hide();
		$('.expiredspinloader').hide();

		$('.printInventory').on("click",function(){
			$('.spinloader').show();
			$.get("<?=base_url('Pharmacy_orders/generateinventorypdf')?>",function(data){
				$('.spinloader').hide();
				window.open("<?=base_url('uploads/inventoryPdf/')?>"+data.trim(),"_blank");
			});
		});
		$('.printShortage').on("click",function(){
			$('.shortagespinloader').show();
			$.get("<?=base_url('Pharmacy_orders/generateshortagepdf')?>",function(data){
				$('.shortagespinloader').hide();
				window.open("<?=base_url('uploads/inventoryPdf/')?>"+data.trim(),"_blank");
			});
		});
		$('.printExpSoon').on("click",function(){
			$('.expsoonspinloader').show();
			$.get("<?=base_url('Pharmacy_orders/generateexpsoonpdf')?>",function(data){
				$('.expsoonspinloader').hide();
				console.log(data);
				window.open("<?=base_url('uploads/inventoryPdf/')?>"+data.trim(),"_blank");
			});
		});
		$('.printExpired').on("click",function(){
			$('.expiredspinloader').show();
			$.get("<?=base_url('Pharmacy_orders/generateexpiredpdf')?>",function(data){
				$('.expsoonspinloader').hide();
				window.open("<?=base_url('uploads/inventoryPdf/')?>"+data.trim(),"_blank");
			});
		});
	});
</script>
<script type="text/javascript">
	function toggle(source) {
		checkboxes = document.getElementsByName('all_check');
		for(var i=0, n=checkboxes.length;i<n;i++) {
			checkboxes[i].checked = source.checked;
		}
	}

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
<script type="text/javascript">
	$(document).ready(function(){
		$('.dataTable').dataTable();
		$('#inventoryList').DataTable();
	});
</script>