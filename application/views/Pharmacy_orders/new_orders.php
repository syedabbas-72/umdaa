<div class="page-bar">
	<div class="page-title-breadcrumb">
		<ol class="breadcrumb page-breadcrumb pull-left">
			<li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
			</li>
			<li><a href="<?=base_url('Pharmacy_orders')?>">Pharmacy Inventory</a>&nbsp;<i class="fa fa-angle-right"></i></li>
			<li class="active">Add New Drug</li>
		</ol>
	</div>
</div>

<section class="main-content">
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="row page-title">
					<div class="col-md-12">
						Pharmacy Inventory &nbsp; <span><i class="fas fa-caret-right"></i></span> &nbsp; Add New Drug
					</div>
				</div>
				<div class="row mainContent">
					<div class="col-md-12">	
						<div id="adddrug" style="display:none">
							<span style="color: red;font-weight: bold">Drug Is not Available Please add to master</span> <a href="<?=base_url('Pharmacy_orders/drug_add'); ?>" class="btn btn-primary btn-rounded btn-xs box-shadow" target="blank"> ADD NEW DRUG</a>
						</div>
						<div class="row customForm">
							<div class="col-md-12">
	                            <div class="form-group">
	                            	<label for="search_pharmacy" class="col-form-label">Search drug using Trade Name (Eg. Dolo 650) <span class="imp">*</span></label>
	                            	<input type="text" class="form-control" style="text-transform: capitalize;" id="search_drugs" onkeypress="Search(this.value)" />&nbsp;&nbsp;&nbsp;<span class="pluse"></span>
	                            </div>
	                        </div>
	                    </div>
	                    <div id="testDiv"></div>
						<?php 
						$clinic_id = $this->session->userdata("clinic_id");
						$vendor_list = $this->db->query("select * from vendor_master where clinic_id='".$clinic_id."'")->result();
						?>
						<form method="POST" action="<?php echo base_url('Pharmacy_orders/pharmacy_add');?>" role="form" class="form customForm">
							<div class="row col-md-12 " style="margin: 0px !important;padding: 0px !important">
								<div class="form-group">
									<table id="orderlist" class="table dt-responsive" >
									<tbody>
										
									</tbody>
								</table>
								</div>
							</div>
							<table id="orderlist1" class="table table-striped dt-responsive nowrap" style="display:none">
								<tbody>						
									<tr><td colspan='5'><input type="submit" value="Submit" class="btn btn-app"></td></tr>
								</tbody>
							</table>						
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<script>
// $(document).ready(function(){
// 	$('#search_drugs').on("input", function(){
// 		// console.log($(this).val())
// 		var drug = $(this).val()
// 		if(drug.length > 3)
// 		{
// 			console.log(drug)
// 			$.post("<?=base_url('Pharmacy_orders/getDrugs')?>",{searchParam: drug}, function(data){
// 				var drugs = data
// 				console.log(drugs)
// 				$('#search_drugs').autocomplete({
// 					source: drugs
// 				})
// 				// var results = [];
// 				// $.each(saltNames, function(k,v){
// 				// 	results.push(v);  
// 				// });   

// 				// $("#salt_name_tb").autocomplete({
// 				// 	select: function(event, ui){
// 				// 		var val = ui.item.value;
// 				// 		$('#salt_name_tb').val(val);
// 				// 		addSalt();
// 				// 	},
// 				// 	max:1,
// 				// 	minLength:1,
// 				// 	source: function( request, response ) {
// 				// 		var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
// 				// 		response( $.grep( results, function( item ){
// 				// 			return matcher.test(item);
// 				// 		}) );
// 				// 	}
// 				// });
// 			});

// 			// $.ajax({
// 			// 	method: "POST",
// 			// 	URL: "<?=base_url('MobileApi/Drug/getDrugs')?>",
// 			// 	data: {searchParam: drug},
// 			// 	// dataType: "json",
// 			// 	success: function(data){
// 			// 		console.log(data)
// 			// 	}
// 			// })
// 		}
// 	});
// });

function Search(drug)
{
	// console.log(drug)
	$("#search_drugs").autocomplete({
		delay: 300,
		minLength: 3,
		source: function(request, response) {
			$.post("<?=base_url('Pharmacy_orders/getDrugs')?>",{searchParam:drug}, function(data){
				// console.log(JSON.parse(data).length)
				// console.log(data.length)
				if(JSON.parse(data) == null){
					$('#adddrug').css("display","block");
				}
				else{
					response(JSON.parse(data));
				}
				
			});
		},
		select: function (event, ui) {
			// Set selection
			$('#search_drugs').val(ui.item.label); // display the selected text
			// console.log(ui.item.label)
			// console.log(ui.item.value)
			add_inventry_row('orderlist',ui.item.value)
			$('#search_drugs').val('');

			// $('#selectuser_id').val(ui.item.value); // save selected id to input
			return false;
		}
	});
}
$(function() {

	
});

</script>
<script>
/*
add_inventory_row function adds a row while adding a new drug to the clinic pharmacy inventory
*/
function add_inventry_row(id,drug_id)
{

	// var drgm = drug_val;
	// var drg = drgm.substr(drgm.indexOf(' ')+1);
	// var comp = drgm.substr(drgm.indexOf(' ')+2);

	count = $("#"+id).find('tr').length;
	$("#listdiv").css("display","block");
	var base_url = '<?php echo base_url(); ?>';
	$.ajax({
		url : base_url+"/Pharmacy_orders/getDrugInfo",
		method : "POST",
		data : {"drug":drug_id},
		success : function(drugRecord) {
			console.log(drugRecord)
			var drugInfo = $.parseJSON(drugRecord);
			var vendorCount = drugInfo.vendor_list.length;
			var vendors = '';

			// Replace the null values to 0
			Object.keys(drugInfo).forEach(function(key) {
				if(drugInfo[key] == null)
					drugInfo[key] = 0;
			})

			for(i = 0; i < vendorCount; i++) {
				if(drugInfo.vendor_id == drugInfo.vendor_list[i].vendor_id){
					vendors += "<option selected value='"+drugInfo.vendor_list[i].vendor_id+"'>"+drugInfo.vendor_list[i].vendor_storeName+", "+drugInfo.vendor_list[i].vendor_location+"</option>";
				}else{
					vendors += "<option value='"+drugInfo.vendor_list[i].vendor_id+"'>"+drugInfo.vendor_list[i].vendor_storeName+", "+drugInfo.vendor_list[i].vendor_location+"</option>";
				}
			}

			var yearHtml = '';
			var year = new Date().getFullYear();
			for(x=1; x<=20; x++){
				yearHtml += "<option value='"+year+"'>"+year+"</option>"
				year++;
			}

			var monthHtml = '';
			var monthNames = [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ];
			for(y=0; y<12; y++){
				monthHtml += "<option value='"+(y+1)+"'>"+monthNames[y]+"</option>";
			}

			var reorder_level = (drugInfo.reorder_level) ? drugInfo.reorder_level : 0;
			var igst = (drugInfo.igst) ? drugInfo.igst : 0;
			var cgst = (drugInfo.cgst) ? drugInfo.cgst : 0;
			var sgst = (drugInfo.sgst) ? drugInfo.sgst : 0;
			var discount = (drugInfo.discount) ? drugInfo.discount : 0;
			var hsn_code = (drugInfo.hsn_code) ? drugInfo.hsn_code : '';

			var html = '';
			html = '<tr id="'+drugInfo.drug_id+'_tr" data-toggle="tooltip" data-placement="top" title="'+drugInfo.composition+'">';
			html += '<td class="p-0"><span class="page-title p-1 m-0">'+drugInfo.trade_name+'&nbsp;('+drugInfo.formulation+')</span>';
			html += '<div class="row col-md-12 p-0">';
			html += '<div class="col-md-2"><label class="col-form-label">HSN Code</label><input type="text" class="form-control" name="hsn_code[]" onkeypress="return numeric()" maxlength="10"  value="'+hsn_code+'"></div>';
			html += '<div class="col-md-2"><label class="col-form-label">Batch No.</label><input type="text" maxlength="25" class="form-control text-uppercase" name="batchno[]" required></div>';
			html += '<div class="col-md-1"><label class="col-form-label">QTY</label><input type="text" class="form-control" maxlength="10" name="qty[]" required onkeypress="return numeric()"></div>';
			html += '<div class="col-md-1"><label class="col-form-label">MRP</label><input type="text" class="form-control" maxlength="10" name="mrp[]" required onkeypress="return decimal()"></div>';
			html += '<div class="col-md-1"><label class="col-form-label">R-Ord Lvl</label><input type="text" class="form-control" name="rlevel[]" maxlength="3" value="'+reorder_level+'" required onkeypress="return numeric()"></div>';
			html += '<div class="col-md-1"><label class="col-form-label">IGST</label><input type="text" class="form-control" name="igst[]" onkeyup="return gst(this.value,id)" id="igst'+drugInfo.drug_id+'" value="'+igst+'" required onkeypress="return numeric()"></div>';
			html += '<div class="col-md-1"><label class="col-form-label">CGST</label><input type="text" class="form-control" name="cgst[]" onkeyup="return gst(this.value,id)" id="cgst'+drugInfo.drug_id+'" value="'+cgst+'" required onkeypress="return numeric()"></div>';
			html += '<div class="col-md-1"><label class="col-form-label">SGST</label><input type="text" class="form-control" name="sgst[]" onkeyup="return gst(this.value,id)" id="sgst'+drugInfo.drug_id+'" value="'+sgst+'" required onkeypress="return numeric()"></div>';
			html += '<div class="col-md-1"><label class="col-form-label">Disc</label><input type="text" class="form-control" name="disc[]" value="'+discount+'" required onkeypress="return numeric()"></div>';
			html += '<div class="col-md-1"><label class="col-form-label">Pck Sz</label><input type="text" class="form-control" name="pack_size[]" maxlength="3" required onkeypress="return numeric()"></div>';
			html += '</div><div class="row">';
			html += '<div class="col-md-2"><label class="col-form-label">Expiry Date</label>';
			html += '<select name="expiryMonth[]" class="form-control" required><option value="">Select Month</option>';
			html += monthHtml;
			html += '</select>';
			html += '</div><div class="col-md-2"><label class="col-form-label">&nbsp;</label>';
			html += '<select name="expiryYear[]" class="form-control" required><option value="">Select Year</option>';
			html += yearHtml;
			html += '</select></div>';
			html += '<div class="col-md-4"><label class="col-form-label">Vendor</label><select class="form-control" name="vendor[]" required=""><option>Select Vendor</option>';
			html += vendors;
			html += '</select></div>';
			html += '<div class="col-md-2 pull-center actions" style="padding-top:15px;"><br><button class="btn btn-app mt-1"><i onclick="return delDrugRow('+drugInfo.drug_id+')" id="'+drugInfo.drug_id+'_i" class="fas fa-trash-alt" style="margin-top:0px !important"></i></button></div><input class="form-control" type="hidden" name="drgid[]" value="'+drugInfo.drug_id+'"/></div></td></tr>';

			$("#"+id).append(html);

			$("#search_pharmacy").val(''); 
			$("#orderlist1").show();
		}
	});
}

</script>

<!-- <script type="text/javascript">
	$(document).ready(function(){
		tname_search_drug_master();
	});
</script> -->