
<?php 
$cur_tab = $this->uri->segment(1)==''?'dashboard': $this->uri->segment(1);
?>  
<!DOCTYPE html>
<html lang="en">
<!-- BEGIN HEAD -->

<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1" name="viewport" />
	<meta name="description" content="Health Care Management System" />
	<meta name="author" content="UMDAA" />
	<title>UMDAA Health Care</title>
	<!-- google font -->
	<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet" type="text/css" />
	<!-- icons -->
	<link href="<?php echo base_url(); ?>assets/css/all.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<!--bootstrap -->
	<link href="<?php echo base_url(); ?>assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url(); ?>assets/plugins/sweet-alerts2/sweetalert2.min.css" rel="stylesheet">
	<link href="<?php echo base_url(); ?>assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url(); ?>assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
	<link href="<?php echo base_url(); ?>assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">
	<link href="<?php echo base_url(); ?>assets/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url(); ?>assets/plugins/bootstrap-editable/inputs-ext/address/address.css" rel="stylesheet" type="text/css" />
	<!-- data tables -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/plugins/datatables/datatables.min.css"/>
	<!-- <link href="<?php echo base_url(); ?>assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/> -->
	<!-- <link href="<?php echo base_url(); ?>assets/buttons/1.5.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />   -->
	<!--tagsinput-->
	<link href="<?php echo base_url(); ?>assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">

	<!--select2-->
	<link href="<?php echo base_url(); ?>assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url(); ?>assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

	<!-- Material Design Lite CSS -->
	<link href="<?php echo base_url(); ?>assets/plugins/material/material.min.css" rel="stylesheet" >
	<link href="<?php echo base_url(); ?>assets/css/material_style.css" rel="stylesheet">
	<!-- Theme Styles -->
	<link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet" type="text/css" />    
	<link href="<?php echo base_url(); ?>assets/css/plugins.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url(); ?>assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url(); ?>assets/css/responsive.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url(); ?>assets/css/theme-color.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url(); ?>assets/plugins/summernote/summernote.css" rel="stylesheet">
	<script src="<?php echo base_url(); ?>assets/plugins/jquery/jquery.min.js" ></script>
	<script src="https://code.jquery.com/jquery-migrate-3.0.0.min.js"></script>
	<link href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" type="text/css" rel="stylesheet" />
	<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<!-- favicon -->
	<link rel="shortcut icon" href="" /> 
	<style type="text/css">
		#tableExport_wrapper .dt-buttons {
			display: none;
		}
		.switchToggle input:checked + .slider:before {
			-webkit-transform: translateX(26px) !important;
			-ms-transform: translateX(26px) !important;
			transform: translateX(50px) !important;
		}
		.switchToggle {
			width: 80px !important;
			height: 30px !important;
		}
		.switchToggle .slider:before{
			width: 22px !important;
			height: 22px !important
		}
		.pull-left{
			float: left;
		}
		.page-header.navbar .page-logo{
			background: #fff !important;
			padding: 10px 20px 0px 20px !important;
		}

		input[type=number]::-webkit-inner-spin-button, 
		input[type=number]::-webkit-outer-spin-button { 
			-webkit-appearance: none; 
			margin: 0; 
		}


		.checkbox input[type='checkbox'] + label:before {
			font-family: 'Font Awesome 5 Free';
			content: "\f00c";
			color: #fff;
		}
		/* font weight is the only important one. The size and padding makes it look nicer */
		.checkbox input[type='checkbox']:checked + label:before {
			font-weight:900;
			color: #000;
			font-size: 10px;
			padding-left: 3px;
			padding-top: 0px;
		}
		.checkbox input[type="checkbox"]:checked + label::after,
		.checkbox input[type="radio"]:checked + label::after {
			content: "";
		}
	</style>

</head>
<!-- END HEAD -->
<body class="page-header-fixed sidemenu-closed-hidelogo page-content-white page-md header-white dark-color logo-dark">
	<div class="page-wrapper">

		<!-- ============================================================== -->
		<!-- 						Topbar Start 							-->
		<!-- ============================================================== -->

		<?php include 'include/topnav.php'; ?>

		<!-- ============================================================== -->
		<!--                        Topbar End                              -->
		<!-- ============================================================== -->

		<!-- ============================================================== -->
		<!--                        Sidebar Start                              -->
		<!-- ============================================================== -->
		<div class="page-container">

			<?php include 'include/sidenav.php'; ?>

			<!-- ============================================================== -->
			<!--                        Sidebar End                              -->
			<!-- ============================================================== -->


			<!-- ============================================================== -->
			<!-- 						Content Start	 						-->
			<!-- ============================================================== -->

			<!-- start page content -->
			<div class="page-content-wrapper">


				<div class="page-content">

					<?php if($this->session->flashdata('msg') != ''): ?>
						<div class="alert bg-info flash-msg alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<h4> Success!</h4>
							<?= $this->session->flashdata('msg'); ?> 
						</div>
					<?php endif; ?> 
					<!-- page start-->

					<?php $this->load->view($view);?>
				</div>
			</div>
		</div>
	</div>
	<!-- page end-->

	<!-- start footer -->
	<div class="page-footer">
		<div class="page-footer-inner"> <?php echo date("Y"); ?> &copy; UMDAA

		</div>
		<div class="scroll-to-top">
			<i class="material-icons">eject</i>
		</div>
	</div>
	<!-- end footer -->

</div>

<!-- ============================================================== -->
<!-- 						Content End		 						-->
<!-- ============================================================== -->

<input type="hidden" class="json_url" >

<!-- start js include path -->

<script src="<?php echo base_url(); ?>assets/plugins/popper/popper.min.js" ></script>
<script src="<?php echo base_url(); ?>assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
<script src="<?php echo base_url(); ?>assets/plugins/jquery-validation/js/jquery.validate.min.js" ></script>
<script src="<?php echo base_url(); ?>assets/plugins/jquery-validation/js/additional-methods.min.js" ></script>
<script src="<?php echo base_url(); ?>assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<!-- bootstrap -->
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js"></script>
<!-- data tables -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/plugins/datatables/datatables.min.js"></script>
<!-- <script src="<?php echo base_url(); ?>assets/plugins/datatables/jquery.dataTables.min.js" ></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js" ></script> -->
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" ></script>
<script src="<?php echo base_url(); ?>assets/plugins/jquery-tags-input/jquery-tags-input.js" ></script>
<script src="<?php echo base_url(); ?>assets/plugins/jquery-tags-input/jquery-tags-input-init.js" ></script>

<!-- Common js-->
<script src="<?= base_url() ?>assets/plugins/sweet-alerts2/sweetalert2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/app.js" ></script>
<script src="<?php echo base_url(); ?>assets/js/pages/validation/form-validation.js" ></script>
<script src="<?php echo base_url(); ?>assets/js/layout.js" ></script>
<script src="<?php echo base_url(); ?>assets/js/theme-color.js" ></script>
<!-- Material -->
<script src="<?php echo base_url(); ?>assets/plugins/material/material.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/summernote/summernote.min.js" ></script>
<!--select2-->
<script src="<?php echo base_url(); ?>assets/plugins/select2/js/select2.js" ></script>


<script type="text/javascript">	
	
	$(".flash-msg").fadeTo(2000, 500).slideUp(500, function(){
		$(".flash-msg").slideUp(500);
	});

	$(document).ready(function(){
		$('.patientsData').dataTable();
		$('[data-toggle="tooltip"]').tooltip();   
		$("#drugAddBtn").attr("disabled","disabled");
		tname_search_drug_master();
		tnamesearch_inventory();
	});

	$( function() {

		$( "#date_of_birth" ).datepicker({
			shortYearCutoff: 1,
			changeMonth: true,
			changeYear: true,
			dateFormat: "dd-mm-yy",
			minDate: "-70Y", 
			maxDate: 0,
			yearRange: "1940:<?php echo date('Y'); ?>"
		});
		$( "#appointment_date_from" ).datepicker({
			dateFormat: "dd-mm-yy",
			maxDate: '0'
		});
		$( "#appointment_date_to" ).datepicker({
			dateFormat: "dd-mm-yy",
			maxDate: '0'
		});
		$( "#chart_date_from" ).datepicker({
			dateFormat: "dd-mm-yy",
			maxDate: '0'
		});
		$( "#chart_date_to" ).datepicker({
			dateFormat: "dd-mm-yy",
			maxDate: '0'
		});


		$("#date_of_joining").datepicker({ shortYearCutoff: 1,
			changeMonth: true,
			changeYear: true,
			dateFormat: "dd-mm-yy",
			minDate: "-70Y", 
			maxDate: 0,
			yearRange: "2000:<?php echo date('Y'); ?>"
		});
	});


	function saltMasterSearch()
	{

		var saltNames = [<?php echo $sname; ?>];

		var results = [];
		$.each(saltNames, function(k,v){
			results.push(v);  
		});   

		$("#salt_name_tb").autocomplete({
			select: function(event, ui){
				var val = ui.item.value;
				$('#salt_name_tb').val(val);
				addSalt();
			},
			max:1,
			minLength:1,
			source: function( request, response ) {
				var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
				response( $.grep( results, function( item ){
					return matcher.test(item);
				}) );
			}
		});
	}


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
					html += '<input type="hidden" name="salt['+counter+'][salt_id]" id="salt_id_'+counter+'_tb" class="form-control " value="'+a[0]+'">';
					html += '<div class="col-md-6"><div class="form-group"><input type="text" name="salt['+counter+'][salt_name]" id="salt_name_'+counter+'_tb" class="form-control readonly" readonly="readonly" value="'+saltName+'"></div></div>';
					html += '<div class="col col-md-2"><div class="form-group"><input type="text" class="form-control" id="dossage_'+counter+'_tb" name="salt['+counter+'][dossage]" placeholder="Dossage"></div></div>';
					html += '<div class="col col-md-2"><div class="form-group"><select class="form-control" name="salt['+counter+'][unit]" id="unit_'+counter+'_sb"><option value="">Select Unit</option><option>mg</option><option>gm</option><option>% W/V</option><option>%</option><option>units</option><option>IU</option><option>ml</option><option>Million Spores</option><option>mg SR</option><option>mcg</option><option>UI</option></select></div></div>';
					html += '<div class="col col-md-1"><div class="form-group"><input type="text" class="form-control" id="schedule_'+counter+'_tb" name="salt['+counter+'][scheduled_salt]" placeholder="Sch" value="'+a[1]+'"></div></div>';
					html += '<div class="col-md-1 padding-0"><div class="form-group"><i class="fas fa-trash-alt deleteSmall" onclick="return removeDiv(\''+counter+'_row\');"></i></div></div>';
					html += '</div>';

					counter++;
					
					$('#counter_tb').val(counter);
					$('#saltsDiv').append(html);
					$('#saltsHeadersDiv').show();
					$('#salt_name_tb').val('');	
				}
			});	
		}		
	}


	function addBatch(){
		counter = $('#counter_tb').val();
		html = '';
		html = '<div class="row" id="'+counter+'_row">';
		html += '<div class="col-md-2"><div class="form-group">';
		html += '<input type ="text" class="form-control" id = "batch_'+counter+'_tb" name="cp_inward['+counter+'][batch_no]" value="0">';
		html += '</div></div>';
		html += '<div class="col-md-1"><div class="form-group">';
		html += '<input type ="text" class="form-control" id = "pack_size_'+counter+'_tb" maxlength="2" onkeypress="return numeric()" name="cp_inward['+counter+'][pack_size]" value="0" >';
		html += '</div></div>';
		html += '<div class="col-md-1"><div class="form-group">';
		html += '<input type ="text" class="form-control" id = "quanity_'+counter+'_tb" maxlength="5" onkeypress="return numeric()" name="cp_inward['+counter+'][quantity]" value="0" required="required">';
		html += '</div></div>';
		html += '<div class="col-md-1"><div class="form-group">';
		html += '<input type ="text" class="form-control" id = "mrp_'+counter+'_tb" onkeypress="return decimal()" maxlength="7" name="cp_inward['+counter+'][mrp]" value="0" required="required">';
		html += '</div></div>';
		html += '<div class="col-md-3"><div class="form-group">';
		html += '<select id="expiry_year_sb" name="cp_inward['+counter+'][expiry_month]" class="form-control"><option value="">Select Month</option><option value="1">January</option><option value="2">February</option><option value="3">March</option><option value="4">April</option><option value="5">May</option><option value="6">June</option><option value="7">July</option><option value="8">August</option><option value="9">September</option><option value="10">October</option><option value="11">November</option><option value="12">December</option></select>';
		html += '</div></div>';
		html += '<div class="col-md-3"><div class="form-group">';
		html += '<select id="expiry_year_sb" name="cp_inward['+counter+'][expiry_year]" class="form-control"><option value="">Select Year</option>';
				<?php
					(int)$currentYear = date('Y');
					(int)$yearAhead = $currentYear+11;
					for($i=$currentYear; $i<$yearAhead; $i++){
						?>
						html += "<option value='<?php echo $currentYear; ?>'><?php echo $currentYear; ?></option>";
						<?php
						$currentYear++;
					}
				?>
		html += '</select></div></div>';
		html += '<div class="col-md-1">';
		html += '<i class="fas fa-minus-circle minus" onclick="return removeDiv(\''+counter+'_row\');"></i>';
		html += '</div>';
		html += '</div>';
		counter++;
		$('#counter_tb').val(counter);
		$('#drugBatchDiv').append(html);
	}


	function parametersearch(rid){

		var devices = [<?php echo $cinvg; ?>];

		var results = [];
		$.each(devices, function(k,v){
			results.push(v);  
		});   

		$("#search_parameter_"+rid).autocomplete({ 
			select: function(event, ui)
			{
				$("#div-block").removeClass('div-block-2-col-seq');
				$("#div-block").addClass('div-block-2-col-ad'); 

				$("#div-cont2").removeClass('div-block-2-col');
				$("#div-cont2").addClass('div-block-2-col-ad');
				$("#drugAddBtn").removeAttr("disabled");
				var val= ui.item.value;
				findinvestigation(ui.item.value,rid);
			},
			max:1,
			source: results
		});
	}


	function mparametersearch(rid)
	{
		var results = [];
		var json_url = '<?php echo base_url(); ?>uploads/<?=$investigation_master_json_file?>';
		$.getJSON(json_url, function(data) {
			for (var i = 0, len = data.length; i < len; i++) {
				var res = data[i].replace("&#39;","'");
				results.push(res);
			}   
		}); 

		$("#search_parameter_"+rid).autocomplete({    
			select: function(event, ui){
				$("#error-msg").html("");
				investigationName = ui.item.value;
				var base_url = '<?php echo base_url(); ?>';
				$.ajax({
					url : base_url+"/Lab/getInvestigationId",
					method : "POST",
					data : {"investigation":investigationName},
					success : function(investigation_id) {
						if(investigation_id){
							$("#investigation_id_"+rid+"_tb").val($.trim(investigation_id));
						}
					}
				});
			},    
			max:1,
			minLength:3,
			source: function( request, response ) {
				var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
				response( $.grep( results, function( item ){
					return matcher.test(item);
				}) );
			}
		});
	}


	function findinvestigation(data,id)
	{
		var base_url = '<?php echo base_url(); ?>';
		$.ajax({
			url : base_url+"/Lab/findinvestigation",
			method : "POST",
			data : {"info":data},
			success : function(rdata) {       
				rdata = $.trim(rdata);
				if(rdata != ''){
					var a = rdata.split(":");
					$("#low_"+id).val(a[0]);
					$("#high_"+id).val(a[1]);
					$("#unit_"+id).val(a[2]);
					$("#method_"+id).val(a[3]);
					$("#other_information_"+id).val(a[4]);
				}
			}
		});
	}


	/*
	Clinic Investigation Search 
	A function searches list of investigations of the logged-in clinic that matches the text entered
	While placing a new order in the Lab module
	*/
	function clinicInvestigationSearch()
	{
		var investigations = [<?php echo $cinvg; ?>];

		var results = [];
		$.each(investigations, function(k,v){
			results.push(v);  
		});   

		$("#search_investigation").autocomplete({ 
			select: function(event, ui)
			{
				var searchSelection = ui.item.value;
				$("#search_investigation").val(searchSelection);
				if(searchSelection.indexOf(' (Package)') > 0){
					add_investigation_package_row('orderlist');
				}else{
					add_investigation_order_row('orderlist');
				}
			},
			response: function(event, ui) {
				if (ui.content.length === 0) {
					$("#search_investigation").attr("style","border:1px solid red; color:red");
				} else {       
					$("#search_investigation").attr("style","border:1px solid green; color:green");
				}
			},
			max:1,
			source: results
		});
	}


	function investigationsearch()
	{
		var results = [];
		var json_url = '<?php echo base_url(); ?>uploads/investigation.json';
		$.getJSON(json_url, function(data) {
			for (var i = 0, len = data.length; i < len; i++) {
				results.push(data[i]);
			}   
		}); 

		$("#search_investigation").autocomplete({   
			select: function(event, ui){
				$("#error-msg").html("");

				// enable the button when the product is added to the autocomplete text box
				$("#drugAddBtn").removeAttr("disabled");
	  		},
	  		response: function(event, ui) {
	            // ui.content is the array that's about to be sent to the response callback.
	            if (ui.content.length === 0) {
	            	$("#adddrug").css("display","block");
	            } else {        
	            	$("#adddrug").css("display","none");
	            }
	        },
	        max:1,
	        minLength:3,
	        source: results
	    });
	}


	function investigationMasterSearch()
	{

		$("#alreadyadded").css("display","none");

		var results = [];
		var json_url = '<?php echo base_url(); ?>uploads/<?=$investigation_master_json_file?>';

		$('#search_investigation').val('Loading Investigations... Please wait!');
		$('#search_investigation').attr("disabled", "disabled");

		$.getJSON(json_url, function(data) {
			var len = data.length;
			var chkPoint = len-1;
			for (var i = 0; i < len; i++) {
				results.push(data[i]);
				if(i == chkPoint){
					$('#search_investigation').val('');
					$('#search_investigation').attr("placeholder","Search by trade name");
					$('#search_investigation').removeAttr("disabled");			
					$('#search_investigation').focus();
				}
			}   
		}); 

		$("#search_investigation").autocomplete({   
			select: function(event, ui){
				$("#search_investigation").val(ui.item.value);
				$("#error-msg").html("");

				checkClinicInvestigation(ui.item.value);
			},
			response: function(event, ui) {
				$("#alreadyadded").css("display","none");
				
				if (ui.content.length === 0) {
					$("#adddrug").css("display","block");
				} else {        
					$("#adddrug").css("display","none");
				}
			},
			max:1,
			minLength:3,
			source: results
		});
	}


	function checkClinicInvestigation(inv)
	{
		var base_url = '<?php echo base_url(); ?>';
		$.ajax({
			url : base_url+"/Lab/checkClinicInvestigation",
			method : "POST",
			data : {"investigation":inv},
			success : function(rdata) {
				rdata = $.trim(rdata);        
				if(rdata == 0){
					$("#alreadyadded").css("display","none");
					addInvestigationRow('orderlist');
				}else{
					$("#alreadyadded").css("display","block");
					$("#search_investigation").val('');
				}
			}
		});
	}



	function tnamesearch_inventory() {

		var clinic_id = '<?php echo $this->session->userdata('clinic_id'); ?>';
		var autoComplete = [];

		var json_url = '<?=$clinic_inventory_json_file_name?>';
		$.getJSON(json_url, function(data) {
			for (var i = 0, len = data.length; i < len; i++) {
				autoComplete.push(data[i]);
			}   
		});  

		$("#search_pharmacy_tb").autocomplete({

			select: function(event, ui){ 
				$("#error-msg").html("");
				add_row('orderlist',ui.item.value)
			},
			response: function(event, ui) {
	            // ui.content is the array that's about to be sent to the response callback.
	            if (ui.content.length === 0) {
	            	$("#adddrug").css("display","block");
	            } else {
	            	$("#adddrug").css("display","none");
	            	// $("#orderlist1").css("display","block");
	            	$("#p_total").text('0');$("#ip_total").val('0');
	            }
	        },
	        max:1,
	        minLength:3,
	        source: function( request, response ) {
	        	var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
	        	response( $.grep( autoComplete, function( item ){
	        		var original = item;
	        		var drug = original.substr(original.indexOf(" ") + 1);
	        		return matcher.test(drug);
	        	}));
	        }
	    });
	}


	var globalTimeout = null;  

	function tname_search_drug_master() {
		var autoComplete = [];

		var json_url = '<?php echo base_url(); ?>uploads/<?=$drug_master_json_file?>';

		$('#search_pharmacy').val('Loading Drugs... Please wait!');
		$('#search_pharmacy').attr("disabled", "disabled");

		$.getJSON(json_url, function(data) {
			var len = data.length;
			var chkPoint = len-1;
			for (var i = 0; i < len; i++) {
				autoComplete.push(data[i]);
				if(i == chkPoint){
					$('#search_pharmacy').val('');
					$('#search_pharmacy').attr("placeholder","Search by trade name");
					$('#search_pharmacy').removeAttr("disabled");					
				}
			}   
		});  

		$("#search_pharmacy").autocomplete({

			select: function(event, ui){
				$("#error-msg").html("");
				add_inventory_row('orderlist',ui.item.value)
			},
			response: function(event, ui) {
	            // ui.content is the array that's about to be sent to the response callback.
	            if (ui.content.length === 0) {
	            	$("#adddrug").css("display","block");
	            } else {
	            	$("#adddrug").css("display","none");
	            }
	        },
	        max:1,
	        minLength:2,
	        source: function( request, response ) {
	        	var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
	        	response( $.grep( autoComplete, function( item ){
	        		var original = item;
	        		var drug = original.substr(original.indexOf(" ") + 1);
	        		return matcher.test(drug);
	        	}) );
	        }
	    });
	}


	function patientsearch()
	{
		var devices = [<?php echo $pname; ?>];

		var results = [];
		$.each(devices, function(k,v){
			results.push(v);  
		});   

		$("#search_patients").autocomplete({ 
			select: function(event, ui)
			{

				$("#div-block").removeClass('div-block-2-col-seq');
				$("#div-block").addClass('div-block-2-col-ad'); 

				$("#div-cont2").removeClass('div-block-2-col');
				$("#div-cont2").addClass('div-block-2-col-ad');
				var val= ui.item.value;

				finddetails(ui.item.value);
			},
			max:1,
			source: results});
		var pid = $("#search_patients").val();
		finddetails(pid);
	}


	function finddetails(data)
	{
		var base_url = '<?php echo base_url(); ?>';
		$.ajax({
			url : base_url+"/New_order/getpatient_details",
			method : "POST",
			data : {"info":data},
			success : function(rdata) {     
				$("#patient_info").empty();
				$("#patient_info").append(rdata);
			}
		});
	}


	// Enables the advance textbox
	function enableAdvance(){
		if ($('#advance_cb').is(":checked")){
			$("#advance_tb").removeAttr('disabled');
			// minAmount();
			osa();
		}else{
			$("#advance_tb").attr("disabled","disabled");
			$("#advance_tb").val('');
			// minAmount();
			osa();
		}
	}

	function minAmount(){

		if ($('#advance_cb').is(":checked")){
			// $("#minAdvance_div").show();

			if($("#applyDiscount_cb").is(":checked")){
				var payableAmount = $("#payable_amount_lbl").text();
			}else{
				var payableAmount = $("#payable_amount_tb").val();	
			}			

			var minAdvance = parseFloat(parseInt(payableAmount) * 0.65).toFixed(2);
			// $("#minAdvance_div").text("Min: "+minAdvance);
			$("#minAdvance_tb").val(minAdvance);

			if($('#advance_tb').val() == ''){
				// $("#minAdvance_div").attr('class','gray');
				$("#advance_tb").css('border-color','#CCCCCC');	
			}else{
				advanceAmount = $('#advance_tb').val();
				if(parseFloat(advanceAmount) >= parseFloat(minAdvance)){
					// $("#minAdvance_div").attr('class','okay');	
					$("#advance_tb").css('border-color','#52BE80');	
				}else{
					// $("#minAdvance_div").attr('class','pending');	
					$("#advance_tb").css('border-color','#E74C3C');
				}
			}
		}else{
			// $("#minAdvance_div").text('');
			$("#minAdvance_tb").val('');
			// $("#minAdvance_div").hide();
		}
	}


	function adjustDiscount(discount = '', max_lab_discount = '', ref_doc_lab_discount = ''){
		if ($("#applyDiscount_cb").is(":checked")){
			$("#discount_tb").val(discount);
			inv_enable_discount(0,max_lab_discount,ref_doc_lab_discount);
		}
	}


	function show_new_info()
	{
		var base_url = '<?php echo base_url(); ?>';
		$.ajax({
			url : base_url+"/New_order/show_new_info",
			method : "POST",
			data : {"info":''},
			success : function(rdata) {       
				$("#patient_info").empty();
				$("#patient_info").append(rdata);
			}
		});
	}


	function add_row(id,drug_val)
	{
		var drug = drug_val;
		var drugInfo = drug.trim().split(" ");
		var formulation = drugInfo.shift(); //pop - last word // shift - first word
		var trade_name = drug.substr(drug.indexOf(' ') + 1);
		count = $("#"+id).find('tr').length - 1;
		$("#listdiv").css("display","block");
		var base_url = '<?php echo base_url(); ?>';
		$.ajax({
			url : base_url+"/New_order/get_drug_info",
			method : "POST",
			data : {"trade_name":trade_name, "formulation":formulation},
			success : function(rdata) { 
			
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
						tableRow += '<td class="text-right" style="padding-right:10px">'+count+'. </td>';
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


	function get_batch_details(did)
	{
		$("#d_id").val(did);
		$("#binfo").empty();  
		var base_url = '<?php echo base_url(); ?>';
		$.ajax({
			url : base_url+"/New_order/get_batch_details",
			method : "POST",
			data : {"drug":did},
			success : function(rdata) {     
				$("#binfo").append(rdata);  
			}
		});
	}


	function checkvalue(mqty,id)
	{
		if (id.indexOf('/') > 0) {
			id = id.replace('/', '\\/');
		}
		var eqty = $("#bqty_"+id).val();
		if(eqty != ''){
			if(eqty>mqty || eqty == 0)
			{
				alert("Please check the quantity entered");
				$("#bqty_"+id).val('');
			}
		}
	}


	function storelinedetails()
	{ 
		var did = $("#d_id").val();
		var val = [];
		var amt = $("#amt_"+did).text();
		var ptotal = parseFloat($("#ip_total").val());
		var qty=0; var info=''; var lbl_info=''; var mrp = 0; var ramt = 0;

		$('.batch_cb:checkbox:checked').each(function(i){

			val[i] = $(this).val();
			if (val[i].indexOf('/') > 0) {
				val[i] = val[i].replace('/', '\\/');
			}

			if(info==''){
				info = info+$(this).val()+' :: '+$("#bqty_"+val[i]).val();
				lbl_info = "Batch("+$(this).val()+") - "+$("#bqty_"+val[i]).val();
			}else{
				info = info+', '+$(this).val()+' :: '+$("#bqty_"+val[i]).val();
				lbl_info = lbl_info+"<br>Batch("+$(this).val()+") - "+$("#bqty_"+val[i]).val();
			}

			qty = parseInt(qty)+parseInt($("#bqty_"+val[i]).val());
			ramt = parseFloat(ramt)+(parseInt($("#bqty_"+val[i]).val()))*(parseFloat($("#unitp_"+val[i]).val()).toFixed(2));
		});

		if(lbl_info == ''){
			lbl_info = 'Click here to add quantity';
		}

		if(!isNaN(qty)){
			$('.mrp').css("display","block");
			$("#tqty_"+did).val(qty);
			$("#qty_"+did).val(info);
			$("#qty_lbl_"+did).html(lbl_info);
			$("#amt_span_"+did).text(parseFloat(ramt).toFixed(2));
			$("#actual_amt_span_"+did).text(parseFloat(ramt).toFixed(2));
			ptotal = ptotal+ramt;
			$("#p_total").text(parseFloat(ptotal).toFixed(2));
			$("#ip_total").val(parseFloat(ptotal).toFixed(2));
			$("#amt_"+did).val(ramt);
			$("#toamt_"+did).val(ramt);
			$('#myModal').modal('hide');
			enable_discount();
		}else{
			alert("Please enter quantity");
		}
	}

	function validateDrugOrder(){
		
		var flag = 0;
		
		if($("input").hasClass("drugQty")){
			$('.drugQty').each(function(){
				if($(this).val() == ''){
					flag = 0;
				}else{
					flag = 1;
				}
			});		
		}else{
			flag = 2;
		}


		if($("span").hasClass("drugMrp")){
			$('.drugMrp').each(function(){
				if($(this).text() == '' || $(this).text() == '0.00'){
					flag = 0;
				}else{
					flag = 1;
				}
			});
		}


		if(flag == 0){
			alert("Quantity cannot be 0 or empty for any of the selected drugs.");
			return false;
		}else if(flag == 2){
			alert("Please try adding drugs");
			return false;
		}else if(flag == 1){
			return true;
		}

	}


	function validateLabOrder(){
		var trCount = $("#orderlist").find('tr').length;

		if(trCount < 2){
			alert("Please try adding investigations");
			return false;
		}else{
			if($("#payment_mode_sb").val() == ''){
				alert("Please select Payment Mode");
				$("#payment_mode_sb").focus();
				return false;
			}
		}

		// if advance checkbox is checked and then no advance amount is given
		if ($('#advance_cb').is(":checked")){
			if($('#advance_tb').val() == ''){
				alert('Please specify the Advance amount');
				$('#advance_tb').focus();
				return false;
			}else{
				var advanceAmount = parseFloat($("#advance_tb").val());
				var minAdvance = parseFloat($('#minAdvance_tb').val());
				if (advanceAmount < minAdvance){
					alert('Advance amount cannot be less than '+minAdvance.toFixed(2));
					$('#advance_tb').focus();
					return false;	
				}
			}
		}	
	}


	function cloneEntry(cls, id){
		value = $("#"+id).val(); 
		$("."+cls).val(value);
	}


	function enable_text_box(id){
		if (id.indexOf('/') > 0) {
			id = id.replace('/', '\\/');
		}
		if ($('#batchno_'+id).is(":checked")){    
			$("#bqty_"+id).prop("readonly",false);
		}else{
			$("#bqty_"+id).val('');
			$("#bqty_"+id).prop("readonly",true);
		}
	}


// function ienable_discount(damt, index){
// 	var ptotal = $("#ip_total").val();
// 	var total = 0;
// 	var j = 0;
// 	$(".invgids").each(function () {

// 		if(!isNaN(index)){
// 			if ( $('#iapdis').is(":checked")){
// 				$('.disc').prop('type','text');
// 				$('.disc').attr('readonly','readonly');
// 				$('#disc_'+index).val($("#disc_tb_"+index).val());
// 				var amt = $("#mrp_"+index).val();
// 				var dis = damt;
// 				var dec = (dis/100).toFixed(2);
// 				var mult = amt*dec;
// 				$("#amt_span_"+index).text((amt-mult).toFixed(2));  
// 				total = parseFloat(total) + parseFloat((amt-mult).toFixed(2));
// 			}
// 			else
// 			{
// 				var amt = $("#mrp_"+index).val();
// 				$("#amt_span_"+index).text(parseFloat(amt).toFixed(2));     
// 				if(amt!='')
// 				{
// 					total = parseFloat(total) + parseFloat(parseFloat(amt).toFixed(2));       
// 					j++;
// 				}
// 			}
// 		}
// 	});

// 	if(j>=1){

// 		if ( $('#iapdis').is(":checked")){
// 			$("#p_total").text(total.toFixed(2));$("#ip_total").val(total.toFixed(2));  
// 		}else{
// 			$("#p_total").text(total.toFixed(2));$("#ip_total").val(total.toFixed(2));
// 		}
// 	}
// 	else{
// 		if ( $('#iapdis').is(":checked")){
// 			$("#p_total").text(total.toFixed(2));$("#ip_total").val(total.toFixed(2));  
// 		}else{
// 			$("#p_total").text('0');$("#ip_total").val('0');
// 		}
// 	}
// }


function inv_enable_discount(discount, max_lab_discount, ref_doc_lab_discount){

	if(discount != ''){
		if(discount < 0){
			discount = 0;
			$("#discount_tb").val(0);
		}
		discount = parseInt(discount);
	}else{
		discount = 0;
	}

	referral_doctor_id = $("#referral_doctor_id_sb").val();

	if(referral_doctor_id != ''){
		max_lab_discount = parseInt(ref_doc_lab_discount);
	}else{
		max_lab_discount = parseInt(max_lab_discount);
	}	

	if(discount > max_lab_discount){
		alert("Cannot give more than "+max_lab_discount+"% discount");
		$('#discount_tb').val(max_lab_discount);
		inv_enable_discount(max_lab_discount, max_lab_discount, ref_doc_lab_discount);
		return false;
	}

	if ($("#applyDiscount_cb").is(":checked")){
		$("#amt_span_title").text("Total Amount");
		$("#discount_tr").show();
		$("#payable_amount_tr").show();
	}else{
		$("#amt_span_title").text("Payable Amount");
		$("#discount_tr").hide();
		$("#payable_amount_tr").hide();
		$("#discount_tb").val(0);
		discount = 0;
	}

	var amount = parseFloat($("#total_amount_tb").val());
	var discount_in_inr =  (parseFloat(amount) * parseInt(discount))/100;
	var payableAmount = amount - discount_in_inr;

	$("#discount_amount_lbl").text(discount_in_inr.toFixed(2));
	$("#discount_amount_tb").val(discount_in_inr.toFixed(2));
	$("#payable_amount_lbl").text(payableAmount.toFixed(2));
	$("#payable_amount_tb").val(payableAmount.toFixed(2));
	$("#billing_amount_tb").val(payableAmount.toFixed(2));

	// Check if the Out standing amount calculation is showing up
	if($("#osa_flag_tb").val() == '1'){
		osa();
	}

	minAmount();
}


function checkmax(max, id, index){
	var value = $("#"+id).val();
	if(parseInt(value) > 100) 
	{
		$("#"+id).val(100);
	}
	enable_discount()
}


function enable_discount(){

	var ptotal = $("#ip_total").val();
	var total = 0;
	var mainTotal = 0;
	var savings = 0;
	var j=0;

	$(".totrw").each(function () {
		var index = $(this).val();

		if(!isNaN(index)){
			if ( $('#apdis').is(":checked")){
				
				var type = $('#disc_'+index).attr('type');
				
				if(type == 'hidden'){
					$('#disc_'+index).val($("#disc_tb_"+index).val());
					$('#disc_'+index).prop('type', 'text');
				}

				if($('#disc_'+index).val() == ''){
					$('#disc_'+index).val();
				}
				
				var amt = $("#toamt_"+index).val();
				var dis = $("#disc_"+index).val();
				var dec = (dis/100).toFixed(2);
				var mult = amt*dec;
				$("#amt_span_"+index).text((amt-mult).toFixed(2));  
				total = parseFloat(total) + parseFloat((amt-mult).toFixed(2));
				mainTotal = parseFloat(mainTotal)+parseFloat(amt);
				savings = parseFloat(savings)+parseFloat(mult);
			}
			else
			{
				$('#disc_'+index).prop('type', 'hidden');
				var amt = $("#toamt_"+index).val();
				$("#amt_span_"+index).text(parseFloat(amt).toFixed(2));     
				if(amt!='')
				{
					total = parseFloat(total) + parseFloat(parseFloat(amt).toFixed(2));   
					mainTotal = parseFloat(mainTotal) + parseFloat(parseFloat(amt).toFixed(2));  
					savings = 0;    
					j++;
				}
			}
		}
	});
	if(j>=1){
		if ( $('#apdis').is(":checked")){
			$("#p_total").text(mainTotal.toFixed(2));
			$("#ip_total").val(mainTotal.toFixed(2));
			$("#savings_total").html(savings.toFixed(2));
			$("#savings_total_val").val(savings.toFixed(2)); 
			$("#payable_total").html(total.toFixed(2));
			$("#payable_total_val").val(total.toFixed(2));   
		}
		else
		{
			$("#p_total").text(mainTotal.toFixed(2));
			$("#ip_total").val(mainTotal.toFixed(2));
			$("#savings_total").html(savings.toFixed(2));
			$("#savings_total_val").val(savings.toFixed(2)); 
			$("#payable_total").html(total.toFixed(2));
			$("#payable_total_val").val(total.toFixed(2));   
		}
	}
	else{
		if ( $('#apdis').is(":checked")){
			$("#p_total").text(mainTotal.toFixed(2));
			$("#ip_total").val(mainTotal.toFixed(2));
			$("#savings_total").html(savings.toFixed(2));
			$("#savings_total_val").val(savings.toFixed(2)); 
			$("#payable_total").html(total.toFixed(2));
			$("#payable_total_val").val(total.toFixed(2));      
		}
		else{
			$("#p_total").text('0');
			$("#ip_total").val('0');
			$("#savings_total").text('0');
			$("#savings_total_val").val('0');  
			$("#payable_total").text('0');
			$("#payable_total_val").val('0');  
		}
	}
}


function checkVitalsMax(type)
{
	var pr = "300";
	var sbp = "250";
	var dbp = "250";
	var rr = "60";
	var temp = "110";
	var sao = "100";
	var height = "250";
	var weight = "500";

	var value = $('.'+type).val();
	if(type=="pr")
	{
		if(parseInt(value)>parseInt(pr)) 
		{
			$('.'+type).val(pr);
		}
	}
	else if(type=="sbp")
	{
		if(parseInt(value)>parseInt(sbp)) 
		{
			$('.'+type).val(sbp);
		}
	}
	else if(type=="dbp")
	{
		if(parseInt(value)>parseInt(dbp)) 
		{
			$('.'+type).val(dbp);
		}
	}
	else if(type=="rr")
	{
		if(parseInt(value)>parseInt(rr)) 
		{
			$('.'+type).val(rr);
		}
	}
	else if(type=="temp")
	{
		if(parseInt(value)>parseInt(temp)) 
		{
			$('.'+type).val(temp);
		}
	}
	else if(type=="sao")
	{
		if(parseInt(value)>parseInt(sao)) 
		{
			$('.'+type).val(sao);
		}
	}
	else if(type=="height")
	{
		if(parseInt(value)>parseInt(height)) 
		{
			$('.'+type).val(height);
		}
	}
	else if(type=="weight")
	{
		if(parseInt(value)>parseInt(weight)) 
		{
			$('.'+type).val(weight);
		}
	}

}

function add_orderlist_excel_m(id)
{
	count = $("#"+id).find('tr').length;
	$("#"+id).append('<tr id="'+id+'_'+count+'" data-toggle="tooltip" data-placement="top" title=""><td><input class="form-control" type="text" name="parameter[]" id="search_parameter_'+count+'" onkeypress="mparametersearch('+"'"+count+"'"+');" /><input type="hidden" name="investigation_id[]" id="investigation_id_'+count+'_tb" value=""><input type="hidden" name="lab_template_line_item_id[]" value=""></td><td class="text-center actions"><i class="fas fa-plus-circle plusSmall" onclick="add_orderlist_excel_m('+"'orderlist_excel'"+');"></i><i class="fa fa-minus-circle minusSmall" onclick="remove_orderlist_excel('+"'"+id+'_'+count+"'"+');"></i></td></tr>');
}

function add_orderlist_excel(id)
{
	count = $("#"+id).find('tr').length;
	$("#"+id).append('<tr id="'+id+'_'+count+'" data-toggle="tooltip" data-placement="top" title=""><td><input class="form-control" type="text" name="parameter[]" id="search_parameter_'+count+'" onclick="parametersearch('+"'"+count+"'"+');" /></td><td></td><td><input type="text" name="low[]" id="low_'+count+'" /></td><td><input type="text" name="high[]" id="high_'+count+'" /></td><td><input type="text" name="unit[]" id="unit_'+count+'" /></td><td><input type="text" name="method[]" id="method_'+count+'" onclick="investigationmethodsearch('+"'"+count+"'"+')"; /></td><td><textarea name="other_information[]" id="other_information_'+count+'" rows="5" cols="10"></textarea></td><td class="text-center actions"><i class="fas fa-plus-circle plusSmall" onclick="add_orderlist_excel('+"'orderlist_excel'"+');"></i><i class="fa fa-minus-circle minusSmall" onclick="remove_orderlist_excel('+"'"+id+'_'+count+"'"+');"></i></td></tr>');
}

function remove_orderlist_excel(id)
{
	$("#orderlist_excel tr#"+id).remove();
}

function add_orderlist_general_m(id)
{
	count = $("#"+id).find('tr').length;
	$("#"+id).append('<tr id="'+id+'_'+count+'" data-toggle="tooltip" data-placement="top" title=""><td><input type="text" class="form-control" name="parameter[]" id="search_parameter_'+count+'" onkeypress="mparametersearch('+"'"+count+"'"+');" /><input type="hidden" name="investigation_id[]" id="investigation_id_'+count+'_tb" value=""><input type="hidden" name="lab_template_line_item_id[]" value=""></td><td><textarea class="form-control" rows="3" cols="25" name="remarks[]"></textarea></td><td class="text-center actions"><i class="fas fa-plus-circle plusSmall" onclick="add_orderlist_general_m('+"'orderlist_general'"+');"></i><i class="fa fa-minus-circle minusSmall" onclick="remove_orderlist_general('+"'"+id+'_'+count+"'"+');"></i></td></tr>');
}

function add_orderlist_general(id)
{
	count = $("#"+id).find('tr').length;
	$("#"+id).append('<tr id="'+id+'_'+count+'" data-toggle="tooltip" data-placement="top" title=""><td><input type="text" class="form-control" name="parameter[]" id="search_parameter_'+count+'" onclick="parametersearch('+"'"+count+"'"+');" /></td><td><textarea class="form-control" rows="3" cols="25" name="remarks[]"></textarea></td><td class="text-center actions"><i class="fas fa-plus-circle plusSmall" onclick="add_orderlist_general('+"'orderlist_general'"+');"></i><i class="fa fa-minus-circle minusSmall" onclick="remove_orderlist_general('+"'"+id+'_'+count+"'"+');"></i></td></tr>');
}


function remove_orderlist_general(id)
{
	$("#orderlist_general tr#"+id).remove();
}

/*
add_inventory_row function adds a row while adding a new drug to the clinic pharmacy inventory
*/
function add_inventory_row(id,drug_val)
{

	var drgm = drug_val;
	var drg = drgm.substr(drgm.indexOf(' ')+1);
	var comp = drgm.substr(drgm.indexOf(' ')+2);

	count = $("#"+id).find('tr').length;
	$("#listdiv").css("display","block");
	var base_url = '<?php echo base_url(); ?>';
	$.ajax({
		url : base_url+"/Pharmacy_orders/get_drug_info",
		method : "POST",
		data : {"drug":drg},
		success : function(drugRecord) {
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
			html += '<td><span class="page-title">'+drg+'&nbsp;('+drugInfo.formulation+')</span>';
			html += '<div class="row">';
			html += '<div class="col-md-2"><label class="col-form-label">HSN Code</label><input type="text" class="form-control" name="hsn_code[]" value="'+hsn_code+'"></div>';
			html += '<div class="col-md-2"><label class="col-form-label">Batch No.</label><input type="text" class="form-control" name="batchno[]" required></div>';
			html += '<div class="col-md-1"><label class="col-form-label">QTY</label><input type="text" class="form-control" name="qty[]" required onkeypress="return numeric()"></div>';
			html += '<div class="col-md-1"><label class="col-form-label">MRP</label><input type="text" class="form-control" name="mrp[]" required onkeypress="return decimal()"></div>';
			html += '<div class="col-md-1"><label class="col-form-label">R-Ord Lvl</label><input type="text" class="form-control" name="rlevel[]" value="'+reorder_level+'" required onkeypress="return numeric()"></div>';
			html += '<div class="col-md-1"><label class="col-form-label">IGST</label><input type="text" class="form-control" name="igst[]" value="'+igst+'" required onkeypress="return numeric()"></div>';
			html += '<div class="col-md-1"><label class="col-form-label">CGST</label><input type="text" class="form-control" name="cgst[]" value="'+cgst+'" required onkeypress="return numeric()"></div>';
			html += '<div class="col-md-1"><label class="col-form-label">SGST</label><input type="text" class="form-control" name="sgst[]" value="'+sgst+'" required onkeypress="return numeric()"></div>';
			html += '<div class="col-md-1"><label class="col-form-label">Disc</label><input type="text" class="form-control" name="disc[]" value="'+discount+'" required onkeypress="return numeric()"></div>';
			html += '<div class="col-md-1"><label class="col-form-label">Pck Sz</label><input type="text" class="form-control" name="pack_size[]" required onkeypress="return numeric()"></div>';
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
			html += '<div class="col-md-2 pull-center actions" style="padding-top:15px;"><i onclick="return delDrugRow('+drugInfo.drug_id+')" id="'+drugInfo.drug_id+'_i" style="line-height:35px;" class="fas fa-trash-alt deleteSmall"></i></div><input class="form-control" type="hidden" name="drgid[]" value="'+drugInfo.drug_id+'"/></div></td></tr>';

			$("#"+id).append(html);

			$("#search_pharmacy").val(''); 
			$("#orderlist1").show();
		}
	});
}


function add_investigation_package_row(id)
{
	var package_name = $("#search_investigation").val();
	var totalPayableAmount = parseFloat($("#total_amount_tb").val());
	count = $("#"+id).find('tr').length;
	$("#listdiv").css("display","block");
	var base_url = '<?php echo base_url(); ?>';
	if(package_name != ''){
		$.ajax({
			url : base_url+"/Lab/get_clinic_package_info",
			method : "POST",
			data : {"package_name":package_name},
			success : function(packageInfo) {
				packageInfo = $.trim(packageInfo);
				var a = packageInfo.split(":"); // [0]Clinic_investigation_package_id : [1]Package_name : [2]Item_code : [3]Price : [4]Discount

				// Check if the package already added to the list or no
				chkCount = $("#pkg_"+a[0]).length;

				if(chkCount > 0){
					alert('Package already added to the list');
					$("#search_investigation").val('');
					$("#search_investigation").focus();
					return false;
				}else{
					// Increase the counter by 1
					var oldValue = $("#itemCount_tb").val();
					$("#itemCount_tb").val(++oldValue);
				}
				counter = $("#itemCount_tb").val();

				if(a[3] != '')    
					totalPayableAmount = totalPayableAmount + parseFloat(a[3]);

				$("#"+id).append('<tr id="pkg_'+a[0]+'"><td class="text-center">'+counter+'.</td><td><span style="text-transform:UpperCase">'+a[1]+'</span><input type="hidden" name="billing_line_items['+counter+'][item_information]"></td><td class="text-center">'+a[2]+'</td><td class="text-center">Package</td><td class="text-center">- -</td><td class="text-right"><span>'+a[3]+'</span><input type="hidden" name="billing_line_items['+counter+'][clinic_investigation_id]" value="'+a[0]+'"><input type="hidden" name="billing_line_items['+counter+'][amount]" id="mrp_'+counter+'" value="'+a[3]+'"></td><td class="text-center actions"><i class="fas fa-trash-alt deleteSmall" onclick="return removeInvestigation('+a[0]+',pkg_'+a[0]+','+a[4]+','+$("#referral_doctor_lab_discount_id").val()+')"><i></td></tr>');  

				$("#noInvestigation_row").hide();
				$("#search_investigation").val(''); 
				$("#paymentInfo_tbl").show();

				$("#total_amount_tb").val(totalPayableAmount.toFixed(2));

				if ($("#applyDiscount_cb").is(":checked")){
					$('#discount_status_tb').val(1);
					inv_enable_discount($("#discount_tb").val(),a[4]);
				}else{
					$('#discount_status_tb').val(0);
					$("#payable_amount_tb").val(totalPayableAmount.toFixed(2));	
					$("#billing_amount_tb").val(totalPayableAmount.toFixed(2));	
				}
			
				$("#total_amount_lbl").text(totalPayableAmount.toFixed(2));

				// Check if the Out standing amount calculation is showing up
				if($("#osa_flag_tb").val() == '1'){
					osa();
				}
				minAmount();
			}
		});
	}
}


function add_investigation_order_row(id)
{
	var investigation = $("#search_investigation").val();
	var totalPayableAmount = parseFloat($("#total_amount_tb").val());
	$("#noInvestigation_row").hide();
	count = $("#"+id).find('tr .invDataTr').length+1; // This will count the nummber of Table rows (tr) in the div of id (orderlist)
	$("#listdiv").css("display","block");
	var base_url = '<?php echo base_url(); ?>';
	if(investigation != ''){
		$.ajax({
			url : base_url+"/Lab/get_clinic_investigation_info_order",
			method : "POST",
			data : {"investigation":investigation},
			success : function(investigationInfo) {

				investigationInfo = $.trim(investigationInfo);
				var a = investigationInfo.split(":"); // [0]clinic_investigation_id : [1]investigation : [2]item_code : [3]category : [4]price : [5]short_form : [6]Discount : [7]investigation_id : [8]investigation/package

				// Check the investigation already added to the list or no
				chkCount = $("#inv_"+a[0]).length;

				if(chkCount > 0){
					alert('Already Added to the list');
					$("#search_investigation").val('');
					$("#search_investigation").focus();
					return false;
				}else{
					// Increase the counter by 1
					var oldValue = $("#itemCount_tb").val();
					$("#itemCount_tb").val(++oldValue);
				}
				counter = $("#itemCount_tb").val();

				if(a[4] != '')    
					totalPayableAmount = totalPayableAmount + parseFloat(a[4]);

				$("#"+id).append('<tr id="inv_'+a[0]+'" data-toggle="tooltip" data-placement="top" title="'+a[1]+' class="invDataTr"><td class="text-center">'+(count)+'.</td><td style="text-transform:UpperCase"><span>'+investigation+'</span><input type="hidden" name="billing_line_items['+counter+'][item_information]" value="'+investigation+'"></td><td class="text-center">'+a[2]+'</td><td class="text-center">'+a[3]+'</td><td class="text-center">'+a[5]+'</td><td class="text-right"><span id="amt_span_'+a[0]+'">'+a[4]+'</span><input type="hidden" name="billing_line_items['+counter+'][investigation_id]" value="'+a[7]+'" class="invgids" /><input type="hidden" name="billing_line_items['+counter+'][clinic_investigation_id]" value="'+a[0]+'" class="invgids" /><input type="hidden" name="billing_line_items['+counter+'][amount]" id="mrp_tb_'+a[0]+'" value="'+a[4]+'" class="test" /></td><td class="text-center actions"><i class="fas fa-trash-alt deleteSmall" onclick="return removeInvestigation(\''+a[0]+'\',\'inv_'+a[0]+'\',\''+a[6]+'\',\''+$("#referral_doctor_lab_discount_id").val()+'\')"><i></td></tr>'); 
				
				$("#search_investigation").val(''); 
				$("#paymentInfo_tbl").show();

				$("#total_amount_tb").val(totalPayableAmount.toFixed(2));

				if ($("#applyDiscount_cb").is(":checked")){
					$('#discount_status_tb').val(1);
					inv_enable_discount($("#discount_tb").val(),a[6]);
				}else{
					$('#discount_status_tb').val(0);
					$("#payable_amount_tb").val(totalPayableAmount.toFixed(2));	
					$("#billing_amount_tb").val(totalPayableAmount.toFixed(2));	
				}
			
				$("#total_amount_lbl").text(totalPayableAmount.toFixed(2));

				// Check if the Out standing amount calculation is showing up
				if($("#osa_flag_tb").val() == '1'){
					osa();
				}
				minAmount();
			}
		});
	}
}


function addInvestigationRow(id)
{
	var invg = $("#search_investigation").val();
	count = $("#"+id).find('tr').length;
	var base_url = '<?php echo base_url(); ?>';
	if(invg!=''){
		$.ajax({
			url : base_url+"/Lab/get_investigation_info",
			method : "POST",
			data : {"invg":invg},
			success : function(drgid) {	
				var investigationJson = JSON.parse(drgid);
				var template_type = investigationJson[0]['template_type'];
				var investigationCount = Object.keys(investigationJson).length;
				var html = '';
				var existContent = 'Investigation(s) - <b>';
				var x = 0;
				
				for(i=0; i<investigationCount; i++){

					if(investigationJson[i]['short_form']){
						shortForm = "("+investigationJson[i]['short_form']+")";
						shortFormPlaceHolder = investigationJson[i]['short_form'];
					}else{
						shortForm = '';
						shortFormPlaceHolder = '';
					}

					if($("#"+investigationJson[i]['investigation_id']+"_div").length){

						$("#exist_div").show();
						if(x > 0){
							existContent += ", ";
						}
						existContent += investigationJson[i]['investigation'];						
						x++;

					}else{

						// Main div row start
						html += '<div class="row" id="'+investigationJson[i]['investigation_id']+'_div">';

						// Investigation Title
						html += '<div class="col-md-6 headCell title"><input type="hidden" name="clinic_investigation['+investigationJson[i]['investigation_id']+'][investigation_id]" value="'+investigationJson[i]['investigation_id']+'">';
						html += '<span>'+investigationJson[i]['investigation']+' ('+investigationJson[i]['item_code']+') '+shortForm+'</span></div>';

						// Short Form 
						html += '<div class="col-md-2 headCell"><div class="form-group"><label for="short_form_'+investigationJson[i]['investigation_id']+'_tb" class="col-form-label">Short Form</label><input class="form-control" type="text" name="clinic_investigation['+investigationJson[i]['investigation_id']+'][short_form]" id="short_form_'+investigationJson[i]['investigation_id']+'_tb"  style="text-transform:UpperCase" placeholder="Short form" value="'+shortFormPlaceHolder+'"></div></div>';

						// Price
						html += '<div class="col-md-2 headCell"><div class="form-group"><label for="mrp_'+investigationJson[i]['investigation_id']+'_tb" class="col-form-label">MRP</label><input class="form-control" type="text" name="clinic_investigation['+investigationJson[i]['investigation_id']+'][price]" id="mrp_'+investigationJson[i]['investigation_id']+'_tb" onkeypress="return decimal();" placeholder="MRP"></div></div>';

						html += '<div class="col-md-2 headCell actions text-center"><a><i class="fas fa-trash-alt deleteSmall" onclick="return removeInvestigationRow(\''+investigationJson[i]['investigation_id']+'_div\')"></i></a></div>';

						if(template_type == 'Excel'){

							// Low Range TextBox
							html += '<div class="col-md-2"><div class="form-group">';
							html += '<label for="lrange_'+investigationJson[i]['investigation_id']+'_tb" class="col-form-label">Low Range</label><input class="form-control" type="text" id="lrange_'+investigationJson[i]['investigation_id']+'_tb" name="clinic_investigation['+investigationJson[i]['investigation_id']+'][low_range]" onkeypress="return decimal();" placeholder="Low Range">';
							html += '</div></div>';

							// High Range TextBox
							html += '<div class="col-md-2"><div class="form-group">';
							html += '<label for="hrange_'+investigationJson[i]['investigation_id']+'_tb" class="col-form-label">High Range</label><input class="form-control" type="text" id="hrange_'+investigationJson[i]['investigation_id']+'_tb" name="clinic_investigation['+investigationJson[i]['investigation_id']+'][high_range]" onkeypress="return decimal();" placeholder="High Range">';
							html += '</div></div>';

							// Units TextBox
							html += '<div class="col-md-2"><div class="form-group">';
							html += '<label for="units_'+investigationJson[i]['investigation_id']+'_tb" class="col-form-label">Unit</label><input class="form-control" type="text" id="units_'+investigationJson[i]['investigation_id']+'_tb" name="clinic_investigation['+investigationJson[i]['investigation_id']+'][units]" placeholder="Unit">';
							html += '</div></div>';

							// Method TextBox
							html += '<div class="col-md-2"><div class="form-group">';
							html += '<label for="method_'+investigationJson[i]['investigation_id']+'_tb" class="col-form-label">Method</label><input class="form-control" type="text" id="units_'+investigationJson[i]['investigation_id']+'_tb" name="clinic_investigation['+investigationJson[i]['investigation_id']+'][method]" placeholder="Method">';
							html += '</div></div>';

							// Other Information
							html += '<div class="col-md-4"><div class="form-group">';
							html += '<label for="otherInformation_'+investigationJson[i]['investigation_id']+'_tb" class="col-form-label">Other Information</label><input class="form-control" type="text" id="otherInformation_'+investigationJson[i]['investigation_id']+'_tb" name="clinic_investigation['+investigationJson[i]['investigation_id']+'][other_information]" placeholder="Other Information">';
							html += '</div></div>';

						}else{

							if(i!=0){
								html += '<div class="col-md-12"><div class="form-group">';
								html += '<label for="remark_'+investigationJson[i]['investigation_id']+'_tb" class="col-form-label">Remark</label><textarea class="form-control" col="12" rows="3" id="remark_'+investigationJson[i]['investigation_id']+'_tb" name="clinic_investigation['+investigationJson[i]['investigation_id']+'][remarks]" placeholder="Remark">'+investigationJson[i]['remarks']+'</textarea>';
								html += '</div></div>';
							}
						}

						html += '</div>'; // Main div row close

						existContent = '';					
						$("#exist_div").hide();

					}	

				}

				$("#exist_span").html(existContent+'</b> already added to the below list');
				$("#search_investigation").val(''); 
				$("#noData_tr").hide();
				$("#submit_tr").show();
				$("#investigation_list").append(html);
				$("#investigation_list").show();

				// drgid = drgid.replace(/\s+/g, '');
				// var a = drgid.split(":");
				// $("#"+id).append('<tr id="'+id+'_'+count+'" data-toggle="tooltip" data-placement="top" title="'+a[1]+'"><td> '+invg+'</td><td>'+a[2]+'</td><td>'+a[3]+'</td><td><input class="form-control" type="text" name="shortform[]" value="'+a[5]+'" /></td><td><input  class="form-control" type="text" name="mrp[]" required  /></td><td><input class="form-control"  type="text" name="lowrange[]" value="'+a[6]+'" /></td><td><input class="form-control"  type="text" name="highrange[]" value="'+a[7]+'" /></td><td><input class="form-control"  type="text" name="units[]" value="'+a[8]+'" /></td><td><input class="form-control"  type="text" name="method[]" id="method_'+count+'" onclick="investigationmethodsearch('+count+');" /></td><td><textarea class="form-control"  rows="2" cols="15" name="oinfo[]">'+a[9]+'</textarea></td><input type="hidden" name="invgid[]" value="'+a[0]+'" /></td><td><button class="btn btn-sm btn-danger remove_drug" id="'+id+'_'+count+'"><i class="fa fa-minus"><i></td><button></tr>');  
				// $("#search_investigation").val(''); 
				// $("#orderlist1").show();
			}
		});
	}
}


function removeInvestigationRow(id){
	$("#"+id).remove();
	divCount = $("#investigation_list").find("div").length;
	if(divCount == 0){
		$("#investigation_list").hide();
		$("#noData_tr").show();
		$("#submit_tr").hide();		
	}
}

function removeInvestigation(id = '', tr_id = '', max_lab_discount = '', referral_doctor_lab_discount = ''){

	var removedAmount = parseFloat($("#mrp_tb_"+id).val());
	var totalAmount = parseFloat($("#total_amount_tb").val());
	var currentAmount = totalAmount - removedAmount;
	var currentDiscount = $("#discount_tb").val();

	$("#total_amount_tb").val(currentAmount.toFixed(2));
	$("#total_amount_lbl").text(currentAmount.toFixed(2));

	$("#"+tr_id).remove();
	$("#noInvestigation_row").show();

	minAmount();

	inv_enable_discount(currentDiscount, max_lab_discount, referral_doctor_lab_discount);
}


function osa(){	
	var value = $("#advance_tb").val();
	if(value != ''){
		var payableAmount = parseFloat($("#payable_amount_tb").val());
		var advanceAmount = parseFloat($("#advance_tb").val());

		if(advanceAmount > payableAmount){
			alert("Advance amount cannot exceed more than "+payableAmount+" amount");
			$("#advance_tb").val(payableAmount);
			osa();			
		}

		var osa = payableAmount - advanceAmount;
		$("#osa_span").text(osa.toFixed(2));	
		$("#osa_tb").val(osa.toFixed(2));
		$("#osa_tr").show();
		$("#osa_flag_tb").val('1'); // Sets OSA ON
	}else{
		$("#osa_span").text('0.00');
		$("#osa_tb").val(0);	
		$("#osa_flag_tb").val('0'); // Sets OSA OFF
		$("#osa_tr").hide();
	}	
	minAmount();
}


function investigationmethodsearch(id)
{
	var devices = [<?php echo $methods; ?>];

	var results = [];
	$.each(devices, function(k,v){
		results.push(v);  
	});   
	$("#method_"+id).autocomplete({ 
		select: function(event, ui)
		{
			$("#div-block").removeClass('div-block-2-col-seq');
			$("#div-block").addClass('div-block-2-col-ad'); 

			$("#div-cont2").removeClass('div-block-2-col');
			$("#div-cont2").addClass('div-block-2-col-ad');
			var val= ui.item.value;
			alert(val);
		},
		max:1,
		source: results});
}


$(document).on("click",".remove_drug",function(){

	var row_id = $(this).attr("id");

	$("#orderlist tr#"+row_id).remove();
	count = $("#orderlist").find('tr').length;
	if(count==1)
	{
		$("#orderlist1").css("display","none");
	}

});


function delDrugRow(drug_id){

	var row_id = drug_id+"_tr";

	$("#"+row_id).remove();
	
	count = $("#orderlist").find('tr').length;
	
	if(count==1)
	{
		$("#orderlist1").css("display","none");
	}	
}


$(document).on("click",".remove_drug_i",function(){
	var row_id = $(this).attr("id");

	$("#orderlist tr#"+row_id).remove();
	count = $("#orderlist tbody").find('tr').length;  
	var total =0;
	if(count==0)
	{
		$("#orderlist1").css("display","none");
	}
	$(".test").each(function() {

		total = total+parseFloat($(this).val());
	});

	$("#p_total").text(total);$("#ip_total").val(total);
});

$(document).on("click",".remove_drug_p",function(){
	var row_id = $(this).attr("id");
	// alert(row_id);

	$("#"+row_id).remove();
	var count = $("#orderlist tbody tr").length;  
	// alert(count);
	var total =0;
	if(count == 0)
	{
		$("#orderlist1").css("display","none");
		$("#noDrug_row").show();
	}
	$(".testp").each(function() {

		total = total+parseFloat($(this).val());
	});
  /*for(i=1;i<=count;i++)
  {
    var did = $("#drgid_"+i).val();
    total = total+parseFloat($("#amt_"+did).val());
}*/
$("#p_total").text(parseFloat(total).toFixed(2));$("#ip_total").val(parseFloat(total).toFixed(2));
enable_discount();
});
function add_indent_row(id)
{
	var drgm = $("#search_pharmacy").val();
	var drg = drgm.substr(drgm.indexOf(' ')+1);
	count = $("#"+id).find('tr').length;
	$("#listdiv").css("display","block");
	var base_url = '<?php echo base_url(); ?>';
	$.ajax({
		url : base_url+"/Pharmacy_orders/get_drug_info",
		method : "POST",
		data : {"drug":drg},
		success : function(drgid) {
			drgid = drgid.replace(/\s+/g, '');
			var a = drgid.split(":");
			$("#"+id).append('<tr id="'+id+'_'+count+'"><td>'+count+' </td><td> '+drg+' <br />('+a[1]+')<br />('+a[2]+') </td><td><input type="text" name="rqty[]" required /></td><input type="hidden" name="drgid[]" value="'+a[0]+'" /></tr>');  
			$("#search_pharmacy").val(''); 

		}
	});
}
$('body').on('focus',".hasDatepicker", function(){
	$(this).datepicker();
});


function enable_textbox(id)
{
	if ( $('#rchk_'+id).is(":checked")){
		$("#rqty_"+id).prop("readonly",false);$("#rqty_"+id).prop("required",true);
	}
	else
	{
		$("#rqty_"+id).val('');
		$("#rqty_"+id).prop("readonly",true);$("#rqty_"+id).prop("required",false);
	}
	var ci=0;
	$(':checkbox:checked').each(function(i){
		ci++;
	});
	if(ci>0)
		$("#btn_show").css("display","block");
	else
		$("#btn_show").css("display","none");

}
</script>  
<!-- ALERT FADEOUT -->

<script type="text/javascript">
     // $(".flash-msg").fadeTo(2000, 500).slideUp(500, function(){
       // $(".flash-msg").slideUp(500);
   // });
</script>   
<?php
$CI =& get_instance();
$nr                = array();
$clinic_id = $this->session->userdata('clinic_id');
$p_list            = $CI->db->query("select *  from patients where clinic_id='".$clinic_id."'")->result();
foreach ($p_list as $key => $value) {


	$nr[] = array(
		'key' => $value->patient_id,
		'value' => $value->first_name." ".$value->last_name,
		'umr' => $value->umr_no
	);


}

?>
<script type="text/javascript">


	var pinfo = <?php echo json_encode($nr); ?>;
	var base_url = '<?php echo base_url(); ?>';
 //var results = [];
 $(function() {
 	$( "#psearch" ).autocomplete({
 		minLength: 1,

 		source: function(req, resp) {
 			var results = [];
 			$.each(pinfo, function(k, v) {
        // Make a pass for names
        if (v.value.toLowerCase().indexOf(req.term) != -1) {
        	results.push(v);
        	return;
        }
        if (v.umr.toLowerCase().indexOf(req.term) != -1) {
        	results.push(v);
        	return;
        }

    });
 			resp(results);
 		},
 		select: function( event, ui ) {
 			window.location.href= '<?php echo base_url(); ?>profile/index/'+ui.item.key;
 		}, 

 		create: function () {
 			$(this).data('ui-autocomplete')._renderItem = function (ul, item) {
 				return $('<li>')
 				.append('<a><div class="inline-block srchRes"><table cellspacing="0" cellpadding="0"><tr><td class="infoPic"><img src="'+base_url+'/assets/img/default-avatar-user.png"></td><td class="infoDiv"><h1>'+item.value+'<br><span><strong>PID:</strong> '+item.umr+'</h1></td></tr></table></div></a>')
 				.appendTo(ul);
 			};
 		}
 	})


 });

 $(function() {
 	$( "#psearch_neworder" ).autocomplete({
 		minLength: 1,

 		source: function(req, resp) {
 			var results = [];
 			$.each(pinfo, function(k, v) {
        // Make a pass for names
        if (v.value.toLowerCase().indexOf(req.term) != -1) {
        	results.push(v);
        	return;
        }

    });
 			if(results.length == 0){
 				$("#pmobile").val("");
 			}
 			resp(results);
 		},
 		select: function( event, ui ) {
 			$("#psearch_neworder").val(ui.item.value);
 			$("#pmobile").val(ui.item.label); 
 		}, 


 		create: function () {
 			$(this).data('ui-autocomplete')._renderItem = function (ul, item) {
 				return $('<li>')
 				.append('<a><div class="inline-block" style="padding:5px 10px;display:block;"><img style="width:20%;margin-top:3px" src="'+base_url+'/assets/img/default-avatar-user.png" class="border img-bordered-sm img-size-100 img-circle"><div style="width:78%;padding-left:2%;display:inline-block;vertical-align:top;"><p style="text-transform:capitalize;display:inline-block;color:#414146;margin:0;"><strong>'+item.value+'</strong></p></div></div></a>')
 				.appendTo(ul);
 			};
 		}
 	})


 });

 $(function() {

 	$( "#pname" ).autocomplete({
 		minLength: 1,

 		source: function(req, resp) {
 			var results = [];

 			$.each(pinfo, function(k, v) {
        // Make a pass for names
        if (v.value.toLowerCase().indexOf(req.term) != -1) {
        	results.push(v);
        	return;
        }

    });

 			if(results.length == 0){
 				$("#id").val("");
 				$("#pname").val("");
 				$("#mobile").val("");
 				$("#umr").val("");
 				$("#submit").hide();
 				$("#app_info").html("");
 			}
 			else{
 				$("#submit").show();

 			}
 			resp(results);
 		},
 		select: function( event, ui ) {
 			$("#id").val(ui.item.key);
 			$("#pname").val(ui.item.value);
 			$("#mobile").val(ui.item.label);
 			$("#umr").val(ui.item.umr);
 			checkappointments(ui.item.key);
 		}, 


 		create: function () {
 			$(this).data('ui-autocomplete')._renderItem = function (ul, item) {
 				return $('<li>')
 				.append('<a><div class="inline-block srchRes"><table cellspacing="0" cellpadding="0"><tr><td class="infoPic"><img src="'+base_url+'/assets/img/default-avatar-user.png"></td><td class="infoDiv"><h1>'+item.value+'<br><span><strong>PID:</strong> '+item.umr+'</h1></td></tr></table></div></a>')
 				.appendTo(ul);
 			};
 		}
 	})


 });


 if ($("body").hasClass("sidemenu-closed")) {
 	$(".nav-heading").hide();
 	$("#emblem").show();
 	$("#navLogo").hide();
 	$('.page-logo').css("padding-left","0px !important");
 }
 else{
 	$(".nav-heading").show();
 	$("#emblem").hide();
 	$("#navLogo").show();
 	$('.page-logo').css("padding-left","30px !important");
 }

 $('body').on('click', '.sidebar-toggler', function (e) {

 	if ($("body").hasClass("sidemenu-closed")) {
 		$(".nav-heading").hide();

 		$("#emblem").hide();
 		$("#navLogo").show();
 	}
 	else{
 		$(".nav-heading").hide();
 		$("#emblem").show();
 		$("#navLogo").hide();

 	}

 });

// $(".nav-item").mouseover(function(){

//    setTimeout(function(){
//        $("body").removeClass("sidemenu-closed");
//    }, 100);
// });
// $('.nav-item').mouseleave(function () {

//   setTimeout(function(){
//     $("body").addClass("sidemenu-closed");
//    }, 100);
// });

function alpha()
{
	var charCode = event.keyCode;
	if ((charCode > 64 && charCode < 91) || (charCode > 96 && charCode < 123) || charCode == 8 || charCode==32)
		return true;
	else
		return false;
}

function numeric()
{
	var charCode = event.keyCode;

	if ((charCode >= 48 && charCode <= 57) || charCode == 8)
		return true;
	else
		return false;
}

function decimal()
{
	var charCode = event.keyCode;
	if (charCode==46 || (charCode>=48 && charCode<=57) || charCode == 8)
		return true;
	else 
		return false;
}

function alphaNumeric()
{
	var charCode = event.keyCode;
	if ((charCode >= 48 && charCode <= 57) || (charCode > 64 && charCode < 91) || (charCode > 96 && charCode < 123) || charCode == 8 || charCode==32)
		return true;
	else
		return false;
}

function alphaNumericDecimal()
{
	var charCode = event.keyCode;
	if (charCode >= 46 || (charCode >= 48 && charCode <= 57) || (charCode > 64 && charCode < 91) || (charCode > 96 && charCode < 123) || charCode == 8 || charCode==32)
		return true;
	else
		return false;
}

</script>
<script type="text/javascript">
	$(document).ready(function(){
		$('.expiryDatePicker').datepicker({
			minDate : new Date()
		});
	});
</script>
</body>
</html>