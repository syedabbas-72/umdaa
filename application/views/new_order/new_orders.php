<style>
	.hdr {
		padding: 10px 15px;
		position: relative;
		display: block;
	}
	.batch > tbody > tr > td, .batch > tbody > tr > th {
		border: 1px solid #e9ecef;
		font-size: 13px;
		vertical-align: inherit;
	}
	.btchRow{
		margin-right: -14px;
		margin-left: -14px;
		display: flex;
		flex-wrap: wrap;
	}
	.ui-autocomplete
	{
		overflow-x: hidden;
		overflow-y: scroll;
		min-height: 50px;
		max-height:300px;
	}
</style>

<?php
$clinic_id = $this->session->userdata("clinic_id");
if(count($pdrugs) > 0){
	$customer_name = $pdrugs[0]['patient_name'];
	$customer_mobile = $pdrugs[0]['mobile'];
}else{
	$customer_name = $customer_mobile = '';	
}
?>

<div class="page-bar">
	<div class="page-title-breadcrumb">
		<ol class="breadcrumb page-breadcrumb">
			<li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
			</li>
			<li>Pharmacy&nbsp;<i class="fa fa-angle-right"></i></li>
			<li class="active">New Order</li>
		</ol>
	</div>
</div>

<section class="main-content">
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="row page-title">
                    <div class="col-md-12">
                        Pharmacy &nbsp; <span><i class="fas fa-caret-right"></i></span> &nbsp; New Order
                    </div>
                </div>
                <?php
                if(isset($prescriptionDrugsCount))
                {
                	?>
                	<div class="row">
	                	<div class="col-md-12 text-center">
	                		<h4>Prescribed Drugs are not Available in Inventory</h4>
	                		<a href="<?=base_url('Pharmacy_prescription')?>" class="btn btn-primary btn-sm">Go Back</a>
	                	</div>
	                </div>
                	<?php
                }
                ?>
                	<div class="row mainContent">
	                	<div class="col-md-12">	
							<form method="post" action="<?php echo base_url('New_order/save_order'); ?>" class="form customForm">
								<div class="row">
			                        <div class="col-md-3">
			                            <div class="form-group">
			                                <label for="pmobile" class="col-form-label">Mobile No. <span class="imp">*</span></label>
			                                <input type="text" list="mobileData"  class="form-control mobile_search" onkeypress = "getPatientInfo(this.value)" style="text-transform: capitalize;" id="pmobile" name="pmobile" placeholder="Mobile" required maxlength="10"  onkeypress="return event.charCode >= 48 && event.charCode <= 57" value="<?=$patient['mobile']?>" autocomplete="new-password">
			                            </div>
			                        </div>
									<div class="col-md-3">
			                            <div class="form-group">
			                            	<?php $pname = $patient['patient_name']; ?>
			                                <label for="pname" class="col-form-label">Customer Name <span class="imp">*</span></label>
			                                <input type="text" id="pname" maxlength="35" onkeypress="return alpha()" name="pname" style="text-transform: capitalize;" class="form-control customer_name" placeholder="Name" required value="<?=$pname?>" autocomplete="new-password">
			                            </div>
			                        </div>
			                        <div class="col-md-6">
			                        	<div class="form-group">
			                        		<input type="hidden" name="pid" id="pid" value="<?=$patient['patient_id']?>" class="pid">
			                        		<input type="hidden" name="umr_no" id="umr" value="<?=$patient['umr_no']?>" class="umr_no">
			                        		<input type="hidden" name="appointment_id" value="<?=$appointment_id?>">
			                        		<input type="hidden" name="doctor_id" value="<?=$doctor_id?>">
				                        	<label for="pmobile" class="col-form-label">Specify Drug Name</label> &nbsp; <span id="error-msg" class="col-md-12" style="padding: 10px"></span>
				                        	<input type="text" name="search_pharmacy" onclick="this.value=''" onkeypress="getClinicDrugs(this.value)" placeholder="Search Drug By Trade Name" id="search_clinic_inventory" class="form-control" style="text-transform: capitalize;" />
			                        	</div>
			                        </div>
			                    </div>
								<div class="row" <?php //if(count($pdrugs)<=0) echo 'style="display:none"'; ?> id="listdiv">
									<div class="col-md-12" style="padding-right: 5px; padding-left:35px;">
	                            		<table id="orderlist" class="table dt-responsive customTable">
											<thead>
												<!-- <tr> -->
													<th style="width: 5%; text-align: right; padding-right: 10px;">S.No.</th>
													<th style="width: 30%; text-align: left">Drug Name</th>
													<th style="width: 25%" class="text-left">Batch - Quantity</th>
													<th style="width: 10%; padding-right: 10px" class="text-right">Amount</th>
													<th style="width: 10%" class="text-center">Disc (%)</th>
													<th style="width: 10%; padding-right: 10px" class="text-right">To Pay</th>	
													<th style="width: 10%" class="text-center">Action</th>
												<!-- </tr> -->
											</thead>
											<tbody>
												<?php 
												if(count($pdrugs)>0){
													$i=1;$total = 0;
													foreach($pdrugs as $presult){ 
														$ind_amount = 0;
														$presQty =0;
														extract($presult);														
														$batches = $this->db->query("select *,sum(quantity) as oqty from clinic_pharmacy_inventory_inward where drug_id=".$presult['drug_id']." and clinic_id=".$clinic_id." and archieve=0 group by batch_no")->result_array();
														// echo $this->db->last_query();
														$batch_count = count($batches);
														$presQty = $presult['quantity'];
														$av_qty = $batches[0]['oqty'];
														if($av_qty<$presQty)
														{
															$trClass = "bg-warning";
														}
														if($batch_count == 0)
															continue;
														?>
														<tr id="orderlist_<?php echo $presult['drug_id']; ?>_tr" class="<?=$trClass?>">
															<td class="text-right" style="padding-right: 10px;"><?php echo $i."."; ?></td>
															<td class="text-left">
																<!-- <?=$av_qty?> -->
																<span class="mrp"><?php echo $presult['trade_name']; ?></span>
																<span class="formulation"><?php echo strtoupper($presult['formulation']); ?></span>
															</td>
															<?php if($stock == 0) {
																?>
																<td class="text-left">
																	<input type="text" name="qty[]" id="qty_<?php echo $presult['drug_id']; ?>" class="form-control noStock" readonly placeholder="No Stock Available" style="color:#000" value="">
																</td>
																<?php 
															}else{
																if($batch_count>1)
																{
																	echo "s";
																	?>
																	<td class="text-left">
																		<label id="qty_lbl_<?php echo $presult['drug_id']; ?>" onclick="get_batch_details('<?php echo $presult['drug_id']; ?>');" data-toggle="modal" placeholder="quantity" data-target="#myModal" data-value="<?=$presQty?>" class="clk_lbl">Click here to add quantity</label>
																		<input type="hidden" required="" name="qty[]" id="qty_<?php echo $presult['drug_id']; ?>" class="form-control stock drugQty" style="color:#000" value="">
																	</td>
																	<?php
																}
																else
																{
																	echo "0";
																	$ind_amount = ($batches[0]['mrp']/$batches[0]['pack_size']) * $presQty;
																	$total += $ind_amount;
																	?>
																	<td>
																		<label id="qty_lbl_<?php echo $presult['drug_id']; ?>" onclick="get_batch_details(<?php echo $presult['drug_id']; ?>);" data-toggle="modal" placeholder="quantity" data-target="#myModal" data-value="<?=$presQty?>" class="clk_lbl">Batch(<?=$batches[0]['batch_no']?>) - <?=$presult['quantity']?></label><input type="hidden" name="qty[]" id="qty_<?php echo $presult['drug_id']; ?>" class="form-control batchTxt stock drugQty" placeholder="Click and Place Required Quantity" required="" value="<?=$batches[0]['batch_no']?> :: <?=$presult['quantity']?>">
																	</td>		
																	<?php
																}
																?>
																
																<?php 
															} ?>
															
															<td class="text-right" style="padding-right: 10px;"><span id="actual_amt_span_<?php echo $presult['drug_id']; ?>" class="mrp drugMrp"><?=number_format($ind_amount,2)?></span></td>
															<td>
																<input type="hidden" class="disc form-control" name="disc[]" id="disc_<?php echo $presult['drug_id']; ?>" value="0"  onkeyup="return checkmax('<?php echo $presult['discount']; ?>',id,<?php echo $presult['drug_id']; ?>)">
															</td>
															<td class="text-right" style="padding-right: 10px;">
																<span id="amt_span_<?php echo $presult['drug_id']; ?>" class="mrp"><?=number_format($ind_amount,2)?></span>
																<input type="hidden" name="toqty[]" value="<?=$presQty?>" id="tqty_<?php echo $presult['drug_id']; ?>" required />
																<input type="hidden" name="toamt[]" value="<?=$ind_amount?>" id="toamt_<?php echo $presult['drug_id']; ?>" />
																<input type="hidden" name="totrw[]" id="totrw<?php echo $presult['drug_id']; ?>" class="totrw" value="<?php echo $presult['drug_id']; ?>" />
																<input type="hidden" id="disc_tb_<?php echo $presult['drug_id']; ?>" value="<?php echo $presult['discount']; ?>" />
																<input type="hidden" name="amt[]" id="amt_<?php echo $presult['drug_id']; ?>" value="<?=$ind_amount?>" class="testp" />
																<input type="hidden" name="drgid[]" id="drgid_<?php echo $i; ?>" value="<?php echo $presult['drug_id']; ?>" />
																<input type="hidden" name="pdid" value="<?php echo $pdid; ?>" />
															</td>
															<!-- <td class="actions"><span><i class="fa fa-trash delete remove_drug_p" id="orderlist_<?php echo $presult['drug_id']; ?>_tr"><i></span></td> -->
															<td class="actions text-center"><i onclick="get_batch_details('<?php echo $presult['drug_id']; ?>');" data-toggle="modal" placeholder="quantity" data-target="#myModal" class="fas fa-pencil-alt editSmall"></i><i class="fas fa-trash-alt deleteSmall remove_drug_p" id="orderlist_<?php echo $presult['drug_id']; ?>_tr"><i></td>	
														</tr>
														<?php 
														$i++;
													}
												}
												else{
													?>
													<!-- <tr id="noDrug_row">
														<td colspan="7" class="text-center">No drug has been added yet.</td>
													</tr> -->
													<?php
												}
												?>
											</tbody>
										</table>

										<table id="orderlist1" class="table table-borderless dt-responsive nowrap customTable " <?php if(count($pdrugs) == 0) { echo 'style="display: none"'; } ?> >
											<tbody>
												<tr>
													<td class="text-left" style="width: 70%">
														<input type="hidden" name="apdis" value="0">
														<input class="form-group" type="checkbox" value="1" onclick='enable_discount();' id="apdis" name="apdis" />&nbsp;Apply Discount
													</td>
													<td class="text-right" style="width: 10%">
														<span>Price: </span>
													</td>
													<td style="width: 10%" class="text-right">
														<label id="p_total" style="margin-bottom: 0;font-weight: bold;"><?=number_format($total,2)?></label>
														<input type="hidden" id="ip_total" value="" /> 
													</td>
													<td style="width: 10%">&nbsp;</td>
												</tr>
												<tr>
													<td class="text-left" style="width: 60%">
														<div class="row">
															<div class="col-md-12 mr-2">
																<div class="row">
																	<div class="col-md-4">
																		<label class="col-form-label font-weight-bold">Mode of Payment</label>
																	<!-- </div>
																	<div class="col-md-6"> -->
																		<select name="payment_mode" class="form-control payment_mode">
																			<option value="cash">Cash</option>
																			<option value="card">Card</option>
																			<option value="online">Online</option>
																		</select>
																	</div>

																	<div class="col-md-4 transaction_id_div">
			                                                            <label class="col-form-label font-weight-bold">Transaction ID</label>
																		<input type="text" name="transaction_id" class="form-control text-uppercase" maxlength="20">
		                                                        	</div>
		                                                    	</div>
															</div>
														</div>
													</td>
													<td class="text-right" style="width: 10%">
														<span>Savings: </span>
													</td>
													<td style="width: 10%" class="text-right">
														<label id="savings_total" style="margin-bottom: 0;font-weight: bold;">0.00</label>
														<input type="hidden" id="savings_total_val" value="" /> 
													</td>
													<td style="width: 10%">&nbsp;</td>
												</tr>
												<tr>
													<td class="text-right" colspan="2" style="width: 80%">
														<span>Total Amount To Be Payable: </span>
													</td>
													<td style="width: 10%" class="text-right">
														<label id="payable_total" style="margin-bottom: 0;font-weight: bold;"><?=number_format($total,2)?></label>
														<input type="hidden" id="payable_total_val" value="" /> 
													</td>
													<td style="width: 10%">&nbsp;</td>
												</tr>
												<tr>
													<td colspan='5' class="text-center">
														<input type="submit" value="Submit" class="btn btn-success" onclick="return validateDrugOrder()">
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<input type="hidden" id="pharmacy_discount" value="<?php echo $clinic_info->pharmacy_discount; ?>" />
							</form>
						</div>
					</div>
                	               
			</div>
		</div>
	</div>
</section>
<!-- Patients Modal -->
<div id="patientsModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<!-- Modal content-->
		<div class="modal-content">	
			<input type="hidden" name="d_id" id="d_id" value="" />
			<div class="modal-header hdr">
				<h4 class="modal-title" style="text-align: left;">Patients</h4>
			</div>
			<div class="modal-body patientsBody">

			</div>
		</div>
		<!-- Modal content-->
	</div>
</div>

<div id="myModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">	
			<input type="hidden" name="d_id" id="d_id" value="" />
			<div class="modal-header hdr">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" style="text-align: left;">Batches</h4>
			</div>
			<div class="modal-body">
				<div class="row btchRow" id="binfo"> </div>       
			</div>
			<div class="modal-footer">	   	
				<input type="button" value="Submit" class="btn btn-success" onclick="storelinedetails();" />
			</div>
		</div>
		<!-- Modal content-->
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('.transaction_id_div').hide();
		$('.payment_mode').on("change",function(){
			var value = $(this).val();
			if(value != "cash")
			{
				$('.transaction_id_div').show(500);
			}
			else
			{
				$('.transaction_id_div').hide(500);
			}
		});

		// $('.mobile_search').on("input",function(){
		// 	var mobile_input = $(this).val();
		// 	if(mobile_input.length == 10)
		// 	{
		// 		$.post("<?=base_url('New_order/getPatientInfo')?>",{mobile:mobile_input},function(data){
		// 			var data = data.split("-");
		// 			var json = $.parseJSON(data[1]);
		// 			var pdata = '';
		// 			$('.patientsBody').empty();
		// 			for(var i=0;i<data[0];i++)
		// 			{
		// 				pdata = json[i].customer_name+"*"+json[i].umr_no+"*"+json[i].patient_id;
		// 				$('.patientsBody').append("<p class='pickpatient' id='"+pdata+"'><a class='pt-2 pb-2'>"+(parseInt(i)+1)+". "+json[i].customer_name+" - "+json[i].umr_no+"</a></p>");
		// 			}
		// 			if(json!=null)
		// 			{
		// 				$('#patientsModal').modal();	
		// 			}
		// 		 });
		// 	}			
		// });

		// $('.pickpatient').on("click",function(){
		// 	var id = $(this).attr("id");
		// 	alert("ugskdhfk");
		// 	var str = id.split("*");
		// 	$('.customer_name').val(str[0]);
		// 	$('.umr_no').val(str[1]);
		// 	$('.pid').val(str[2]);
		// });

	});
	$(document).on("click", ".pickpatient",function(){  
		var id = $(this).attr("id");
		var str = id.split("*");
		$('.customer_name').val(str[0]);
		$('.umr_no').val(str[1]);
		$('.pid').val(str[2]);
		$('#patientsModal').modal('toggle');
	});
</script>
<script>
function getPatientInfo(mobile){
	// var 

	// console.log(mobile.length)
	// var mobile = mobile;
	// $('#pmobile').autocomplete({
	// 	delay: 300,
	// 	minLength: 3,
	// 	source: function(request, response) {
	// 		$.post("<?=base_url('New_order/get_patient_info')?>",{ mobile:mobile }, function(data){
	// 			console.log(data)
	// 			console.log(JSON.parse(data))
	// 			response(JSON.parse(data));
	// 		});
	// 	},
	// 	select: function (event, ui) {
	// 		// Set selection
	// 		$('#pmobile').val(ui.item.label); // display the selected text
	// 		// console.log(ui.item.label)
	// 		// console.log(ui.item.value)
	// 		// add_inventry_row('orderlist',ui.item.value)
	// 		// $('#pmobile').val('');

	// 		// $('#selectuser_id').val(ui.item.value); // save selected id to input
	// 		return false;
	// 	}
	// })
    $('#pmobile').attr("autocomplete","new-password");
	$("#pmobile").autocomplete({

        source: function(request, response) {
            //Pass the selected country to the php  to limit the stateProvince selection to just that country
            $.ajax({
                url: base_url + "New_order/get_patient_info",
                data: {
                    mobile: request.term,
                    //Pass the selected countryAbbreviation
                },
                type: "POST", // POST transmits in querystring format (key=value&key1=value1) in utf-8
                success: function(data) {
					console.log(data)
                    if ($.trim(data) == "no") {
                        $("#app_info").html("<div class='alert alert-danger'><small><div class='text-muted'></div><b>No results Found. Check your number</b></small></div>");
                        $("#id").val("");
                        $("#pname").val("");
                        // $("#mobile").val("");
                        $("#umr").val("");
                        $("#submit").hide();
                    } else {
                        $("#submit").show();
                        $("#app_info").html("");
                        response($.parseJSON($.trim(data)));
                    }
                }
            });
        },
        select: function(event, ui) {
            $("#pid").val(ui.item.key);
            $("#pname").val(ui.item.pname);
            $("#pmobile").val(ui.item.value);
            $("#umr").val(ui.item.umr_no);
        },
        create: function() {
            $(this).data('ui-autocomplete')._renderItem = function(ul, item) {
                return $('<li>')
                    .append('<a><div class="inline-block srchRes"><table cellspacing="0" cellpadding="0"><tr><td class="infoDiv"><h1>' + item.pname + '<br><span><strong>PID:</strong> ' + item.umr_no + '</h1><p><strong>M: </strong>' + item.value + '</p></td></tr></table></div></a>')
                    .appendTo(ul);
            };
        },
        minLength: 10
    });

}
</script>

<script>
function getClinicDrugs(drug){
	$('#search_clinic_inventory').autocomplete({
		minLength: 3,
		source: function(request,response){
			$.ajax({
				url: '<?=base_url('New_order/getInventoryDrugs')?>',
				type: 'POST',
				data: {drug: drug},
				success: function(data){
					console.log(data)
					if(data!="")
					{
						response($.parseJSON($.trim(data)));
					}
					else{
						response($.parseJSON($.trim(data)));
					}
				}
			})
		},
        select: function(event, ui) {
			var val = ui.item.formulation+" "+ui.item.label
			var drugId = ui.item.drug_id
			console.log(val)
			add_bill_row('orderlist',drugId,val)
        },
        create: function() {
            $(this).data('ui-autocomplete')._renderItem = function(ul, item) {
				console.log(item)
				if(item.label != "No Results Found")
				{
					return $('<li>')
                    .append('<a><div class="inline-block srchRes w-100"><div class="row"><div class="col-md-12"><p class="m-0 p-0 font-weight-bold trade_name">'+item.label+'<span class="code bg-primary" style="color:white !important">'+item.formulation+'</span> </p></div><div class="col-md-12 mt-2"><span class="text-uppercase font-weight-bold small">Batch No : '+item.batch_no+'</span><span class="mx-4 text-uppercase font-weight-bold small">Available <span class="badge badge-primary">'+item.available_quantity+'</span></span></div></div></div></a>')
                    .appendTo(ul);
				}
				else{
					alert("Drug Not Available In Pharmacy Inventory")
				}
				// console.log(''+item.trade_name+' <span class="formulation">'+item.formulation+'</span> <span class="badge badge-primary">'+item.batch_no+' - '+item.qty+'</span>')
               
            };
        },
	});
}

function add_bill_row(id,drug_id,val)
	{
		var drug = val;
		var drug_id = drug_id
		var drugInfo = drug.trim().split(" ");
		var formulation = drugInfo.shift(); //pop - last word // shift - first word
		var trade_name = drug.substr(drug.indexOf(' ') + 1);
		count = $("#"+id).find('tr').length - 1;
		console.log(count)
		$("#listdiv").css("display","block");
		var base_url = '<?php echo base_url(); ?>';
		$.ajax({
			url : base_url+"/New_order/get_drug_id_info",
			method : "POST",
			data : {"drug_id":drug_id},
			success : function(rdata) { 
				console.log(rdata)
				if($.trim(rdata) == "NA"){
					$("#error-msg").html("<span style='color:red;font-weight:bold;padding:10px;'>"+trade_name.toUpperCase()+" Not Avaialable in Inventory")
				}else{
					$("#error-msg").html("");
					//rdata = rdata.replace(/\s+/g, '');
					rdata = $.trim(rdata);
					var a = rdata.split(":"); // discount : drug_id : formulation : composition : scheduledDrugInfo : stockAvailable;
					
					if($('#'+id+'_'+a[1]+'_tr').length > 0){
						alert("Drug '"+trade_name.trim()+"' already added to the order");
						//return false;
					}else{
						tableRow = '<tr id="'+id+'_'+a[1]+'_tr">';
						tableRow += '<td class="text-right" style="padding-right:10px">'+(parseInt(count)+1)+'. </td>';
						tableRow += '<td class="text-left"><span class="mrp"> '+trade_name+' '+a[4]+'</span><span class="formulation">'+a[2]+'</span></td>';

						if(a[5] == 0){
							tableRow += '<td><input type="text" name="qty[]" id="qty_'+a[1]+'" class="form-control batchTxt noStock" readonly placeholder="No Stock Available" value=""></td>';
						}else{
							tableRow += '<td><label id="qty_lbl_'+a[1]+'" onclick="get_batch_details('+a[1]+');" data-toggle="modal" placeholder="quantity" data-target="#myModal" class="clk_lbl">Click here to add quantity</label><input type="hidden" name="qty[]" id="qty_'+a[1]+'" class="form-control batchTxt stock drugQty" placeholder="Click and Place Required Quantity" required value=""></td>';						
						}					
						
						tableRow += '<td class="text-right"><span id="actual_amt_span_'+a[1]+'" class="mrp drugMrp"></span></td>';
						tableRow += '<td><input type="hidden" class="disc form-control" name="disc[]" id="disc_'+a[1]+'" value="" onkeypress="return numeric()" onkeyup="return checkmax('+a[0]+',id,'+a[1]+')"></td>';
						tableRow += '<td class="text-right"><span id="amt_span_'+a[1]+'" class="mrp" style="display:none"></span><input type="hidden" name="toqty[]" id="tqty_'+a[1]+'" /><input type="hidden" name="toamt[]" id="toamt_'+a[1]+'" /><input type="hidden" name="totrw[]" id="totrw'+count+'" class="totrw" value="'+a[1]+'" /><input type="hidden" id="disc_tb_'+a[1]+'" value="'+a[0]+'" /><input type="hidden" name="amt[]" id="amt_'+a[1]+'" value="" class="testp" /><input type="hidden" name="drgid[]" id="drgid_'+count+'" value="'+a[1]+'" /></td>';
						tableRow += '<td class="actions text-center"><i onclick="get_batch_details('+a[1]+');" data-toggle="modal" placeholder="quantity" data-target="#myModal" class="fas fa-pencil-alt editSmall"></i><i class="fas fa-trash-alt deleteSmall remove_drug_p" id="'+id+'_'+a[1]+'_tr"><i></td>';
						tableRow += '</tr>';

						$("#"+id).append(tableRow);
						$("#noDrug_row").hide();
						$("#orderlist1").show();
						// Once added disable the button back
						$("#drugAddBtn").attr("disabled","disabled");

						// call this function to get the max discounts of the drugs
						enable_discount();
					}
				}
				$("#search_pharmacy_tb").val('');
			}
		});
	}
</script>