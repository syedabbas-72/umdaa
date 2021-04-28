<style type="text/css">
	.fa-plus-circle.plus,.fa-minus-circle.minus{
		font-size: 20px !important;
	}
</style>
<script type="text/javascript">
	function addSalt(){
		counter = $('#counter_tb').val();
		saltName = $('#salt_name_tb').val();
		var base_url = '<?php echo base_url(); ?>';
		if(saltName == ''){
			alert('Please enter the salt name & search');
			$('#salt_name_tb').focus();
		}else{
		
		$.ajax({
          url : base_url+"/Pharmacy_orders/getsalt_details",
          method : "POST",
          data : {"saltName":saltName},
          success : function(rdata) { 
			rdata = rdata.replace(/\s+/g, '');
			
			var a = rdata.split(":");
			
			html = '';
			html = '<div class="row" id="'+counter+'_row">';
			html += '<input type="hidden" name="salt['+counter+'][salt_id]" id="salt_id_'+counter+'_tb" class="form-control " value="'+a[0]+'"><div class="form-group col-md-6"><input type="text" name="salt['+counter+'][salt_name]" id="salt_name_'+counter+'_tb" class="form-control readonly" readonly="readonly" value="'+saltName+'"></div>';
			html += '<div class="form-group col col-md-2"><input type="text" class="form-control" id="dossage_'+counter+'_tb" name="salt['+counter+'][dossage]" placeholder="Dossage"></div>';
			html += '<div class="form-group col col-md-2"><select class="form-control" name="salt['+counter+'][unit]" id="unit_'+counter+'_sb"><option value="">Select Unit</option><option>mg</option><option>gm</option><option>% W/V</option><option>%</option><option>units</option><option>IU</option><option>ml</option><option>Million Spores</option><option>mg SR</option><option>mcg</option><option>UI</option></select></div>';
			html += '<div class="form-group col col-md-1"><input type="text" class="form-control" id="schedule_'+counter+'_tb" name="salt['+counter+'][schedule]" placeholder="Sch" value="'+a[1]+'"></div>';
			html += '<div class="form-group col-md-1 padding-0"><i class="fas fa-minus-circle minus" onclick="return removeDiv(\''+counter+'_row\');"></i></div>';
			// html += '<div class="form-group col-md-1 padding-0"><input type="text" id="SDU_'+counter+'_tb" name="salt_name['+counter+'][\'SDU\']" value="'+saltName+'"></div>';
			html += '</div>';
			counter++;
			$('#counter_tb').val(counter);
			$('#saltsDiv').append(html);
			$('#salt_name_tb').val('');	
          }
		});	
		}		
	}

	function removeDiv(id){
		$('#'+id).remove();
	}

	function concatSDU(id){
		SDU = $('#salt_name_'+id+'_tb').val()+' '+$('#dossage_'+id+'_tb').val()+$('#unit_'+id+'_sb').val();
		$('#SDU_'+id+'_tb').val(SDU);
	}

	function addBatch(){
		counter = $('#counter_tb').val();
		html = '';
		html = '<div class="row" id="'+counter+'_row">';
		html += '<div class="form-group col-md-2">';
		html += '<input type ="text" class="form-control" id = "batch_'+counter+'_tb" name="drug['+counter+'][batch_no]" value="0">';
		html += '</div>';
		html += '<div class="form-group col-md-2">';
		html += '<input type ="text" class="form-control" id = "pack_size_'+counter+'_tb" name="drug['+counter+'][pack_size]" value="0" >';
		html += '</div>';
		html += '<div class="form-group col-md-2">';
		html += '<input type ="text" class="form-control" id = "quanity_'+counter+'_tb" name="drug['+counter+'][quantity]" value="0" required="required">';
		html += '</div>';
		html += '<div class="form-group col-md-2">';
		html += '<input type ="text" class="form-control" id = "mrp_'+counter+'_tb" name="drug['+counter+'][mrp]" value="0" required="required">';
		html += '</div>';
		html += '<div class="form-group col-md-2">';
		html += '<input type ="text" class="solo form-control" maxlength="10"  name="drug['+counter+'][expirydate]" value="" required="required" placeholder="DD / MM / YYYY">';
		html += '</div>';
		html += '<div class="form-group col-md-2">';
		html += '<i class="fas fa-plus-circle plus" onclick="return addBatch();"></i>';
		html += '<i class="fas fa-minus-circle minus" onclick="return removeDiv(\''+counter+'_row\');"></i>';
		html += '</div>';
		html += '</div>';
		counter++;
		$('#counter_tb').val(counter);
		$('#inventoryDiv').append(html);
	}

	$(function () {
        $(".expirydate_tb").datepicker({dateFormat: "yy-mm-dd", changeYear: true, minDate: 0,dateFormat: "dd/mm/yy",});
    });
</script>
<div class="page-bar">
  <div class="page-title-breadcrumb">
     <!--  <div class=" pull-left">
        <div class="page-title">Form Layouts</div>
      </div> -->
      <ol class="breadcrumb page-breadcrumb pull-right">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="#"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
          </li> 
          <li><a class="parent-item" href="#">PHARMACY</a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>   
          <li><a class="parent-item" href="#">NEW DRUG</a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>         
          <li class="active">ADD</li>
      </ol>
  </div>
</div>

	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
				<form method="post" action="<?php echo base_url('Pharmacy_orders/drug_add');?>" class="form customForm">
				<?php //echo "<pre>";print_r($salt_info); ?>
					<div class="row col-md-12">                          
		              <div class="card" style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
		                <div class="card-header" style="font-size: 15px;padding: 10px 20px">New Drug Information</div>
		              </div>
		            </div>
					
					<!-- hidden text input for counter -->
					<input type="hidden" name="counter" id="counter_tb" value="1">
					<div class="row">
						<div class="form-group col-md-3">
							<label for = "name">Formulation</label>
							<select class="form-control" name="formulation_sb" required="required">
								<option value="">Select Formulation</option>
								<option>Tablet</option>
								<option>Syrup</option>
								<option>Capsule</option>
								<option>Suspension</option>
								<option>Infusion</option>
								<option>Cream</option>
								<option>Liquid</option>
								<option>Soap</option>
								<option>Wipe</option>
								<option>Plaster</option>
								<option>Powder</option>
								<option>Drops</option>
								<option>Patch</option>
								<option>Injection</option>
								<option>Claris</option>
								<option>Nirlife</option>
								<option>Combi Device Kit</option>
								<option>Needle</option>
								<option>Gel</option>
								<option>Suppository</option>
								<option>Sachet</option>
								<option>Ointment</option>
								<option>Granules</option>
								<option>Oil</option>
								<option>Shampoo</option>
							</select>
				        </div>
				        <div class="form-group col-md-3">
						    <label for = "trade_name_tb">Trade Name</label>
				            <input type = "text" class = "form-control" id = "trade_name_tb" name="trade_name" required="required">
						</div>						
						<div class="form-group col-md-3">
						    <label for = "trade_name_tb">Manufacturer</label>
				            <input type = "text" class = "form-control" id = "trade_name_tb" name="manufacturer">
						</div>
						<div class="form-group col-md-3">
						    <label for = "trade_name_tb">Category</label>
				            <select class="form-control" name="formulation_sb">
								<option value="">Select Category</option>
								<option>Drug</option>
								<option>Supply</option>
							</select>
						</div>

						<!-- Help text search textbox for the salts to be added for the new drug addition -->
						<div class="form-group col-md-6">
							<label for="salt_tb">Salt</label>
							<div class="input-group ">
								<input type="text" class="form-control" id="salt_name_tb" onclick="snamesearch();">
								<div class="input-group-append">
									<button class="btn btn-success" type="button" onclick="return addSalt('salt_name_tb');" >Add</button>
								</div>
							</div>
						</div>			
						<div class="form-group col-md-3">
							<label for="trade_name_tb">Drug HSN Code</label>
				            <input type="text" class="form-control" id="hsn_code_tb" name="hsn_code">
						</div>

						<!-- below Div 'saltsDiv' shows up when a new salt is added for the composition of the drug -->
						<div class="saltsDiv col-md-12" id="saltsDiv">
							
						</div>

					</div>

					<div class="midGap"></div>
					<div class="row col-md-12">                          
		              <div class="card" style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
		                <div class="card-header" style="font-size: 15px;padding: 10px 20px">Taxation Info</div>
		              </div>
		            </div>

					<div class="row">
						<div class="form-group col-md-3">
							<label for="igst">IGST</label>
							<input type ="text" class="form-control" id = "igst_tb" name="igst" value="0" required="required">
				        </div>
				        <div class="form-group col-md-3">
							<label for="cgst">CGST</label>
							<input type ="text" class="form-control" id = "cgst_tb" name="cgst" value="0" required="required">
				        </div>
				        <div class="form-group col-md-3">
							<label for="sgst">SGST</label>
							<input type ="text" class="form-control" id = "sgst_tb" name="sgst" value="0" required="required">
				        </div>
					</div>

					<div class="midGap"></div>
					<div class="midGap"></div>
						<div class="row col-md-12">                          
						  <div class="card" style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
						    <div class="card-header" style="font-size: 15px;padding: 10px 20px">Inventory Input information</div>
						  </div>
						</div>

					<div class="row">
						<div class="form-group col-md-3">
							<label for="">Maximum Discount</label>
							<input type ="text" class="form-control" id = "max_discount_tb" name="max_discount" value="0" required="required">
						</div>
						<div class="form-group col-md-3">
							<label for="sgst">Re-Order Level</label>
							<input type ="text" class="form-control" id = "reorder_level_tb" name="reorder_level" value="0" required="required">
				        </div>
					</div>

					<div class="inventoryDiv" id="inventoryDiv">
						<div class="row">
							<div class="rowHeader col-md-2">Batch No.</div>
							<div class="rowHeader col-md-2">Pack Size</div>
							<div class="rowHeader col-md-2">Quantity</div>
							<div class="rowHeader col-md-2">MRP (per pack)</div>
							<div class="rowHeader col-md-2">Expiry Date</div>
						</div>	
						<div class="row">
							<div class="form-group col-md-2">
								<input type ="text" class="form-control" id = "batch_tb" name="drug[0][batch_no]" value="0" required="required">
							</div>
							<div class="form-group col-md-2">
								<input type ="text" class="form-control" id = "pack_size_tb" name="drug[0][pack_size]" value="0" required="required">
					        </div>
					        <div class="form-group col-md-2">
								<input type ="text" class="form-control" id = "quantity_tb" name="drug[0][quantity]" value="0" required="required">
					        </div>
					        <div class="form-group col-md-2">
								<input type ="text" class="form-control" id = "mrp_tb" name="drug[0][mrp]" value="0" required="required">
					        </div>
							<div class="form-group col-md-2">
								<input type ="text" class="form-control solo" name="drug[0][expirydate]" value="" maxlength="10" required="required" placeholder="DD / MM / YYYY">
					        </div>
					        <div class="form-group col-md-2">
								<!-- Plus button -->
								<i class="fas fa-plus-circle plus" onclick="return addBatch();"></i>
					        </div>
						</div>	
					</div>

					<div class="col-md-12 text-center">
						<div class="form-group">
							<input type="submit" value="Save" name="submit" class="btn btn-success"> 		
						</div>
					</div>
				</form></div>
			</div>
		</div>
	</div>


<script type="text/javascript">
	 var soloInput = $('input.solo');

soloInput.on('keyup', function(){
  var v = $(this).val();
  if (v.match(/^\d{2}$/) !== null) {
    $(this).val(v + '/');
  } else if (v.match(/^\d{2}\/\d{2}$/) !== null) {
    $(this).val(v + '/');
  }  

});

</script>