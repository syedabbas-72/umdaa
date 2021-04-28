
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
	<title>UMDAA</title>
	<!-- google font -->
	<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet" type="text/css" />
	<!-- icons -->
	<link href="<?php echo base_url(); ?>assets/css/all.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<!--bootstrap -->
	<link href="<?php echo base_url(); ?>assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= base_url() ?>assets/plugins/sweet-alerts2/sweetalert2.min.css" rel="stylesheet">
	<link href="<?php echo base_url(); ?>assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url(); ?>assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
	<link href="<?php echo base_url(); ?>assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">
	<link href="<?php echo base_url(); ?>assets/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url(); ?>assets/plugins/bootstrap-editable/inputs-ext/address/address.css" rel="stylesheet" type="text/css" />
	<!-- data tables -->
	<link href="<?php echo base_url(); ?>assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
	<link href="<?php echo base_url(); ?>assets/buttons/1.5.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />  
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
<script src="<?php echo base_url(); ?>assets/plugins/datatables/jquery.dataTables.min.js" ></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js" ></script>
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
</script>

<script>
	$(document).ready(function(){
		$('[data-toggle="tooltip"]').tooltip();   
		$("#drugAddBtn").attr("disabled","disabled");
		tname_search_drug_master();
		tnamesearch_inventory();
	});
</script>
<script type="text/javascript">

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

   /* $("#date_of_birth").on('change', function () {
      //var dateString = Date.parse($(this).val());
      
      //alert();
  });*/
  
  $("#date_of_joining").datepicker({ shortYearCutoff: 1,
  	changeMonth: true,
  	changeYear: true,
  	dateFormat: "dd-mm-yy",
  	minDate: "-70Y", 
  	maxDate: 0,
  	yearRange: "2000:<?php echo date('Y'); ?>" });

  

} );

	function snamesearch()
	{
		var devices = [<?php echo $sname; ?>];

		var results = [];
		$.each(devices, function(k,v){
			results.push(v);  
		});   

		$("#salt_name_tb").autocomplete({ 
			select: function(event, ui)
			{
    //$("#operation_seq").show(); 

    $("#div-block").removeClass('div-block-2-col-seq');
    $("#div-block").addClass('div-block-2-col-ad'); 

    $("#div-cont2").removeClass('div-block-2-col');
    $("#div-cont2").addClass('div-block-2-col-ad');
    var val= ui.item.value; 


    //loadGarmet(val); 
    //findoperations(ui.item.value);
    
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
	function parametersearch(rid)
	{

		var devices = [<?php echo $cinvg; ?>];

		var results = [];
		$.each(devices, function(k,v){
			results.push(v);  
		});   
    //results.push('New Patient'); 
    $("#search_parameter_"+rid).autocomplete({ 
    	select: function(event, ui)
    	{
    //$("#operation_seq").show(); 
    $("#div-block").removeClass('div-block-2-col-seq');
    $("#div-block").addClass('div-block-2-col-ad'); 

    $("#div-cont2").removeClass('div-block-2-col');
    $("#div-cont2").addClass('div-block-2-col-ad');
    $("#drugAddBtn").removeAttr("disabled");
    var val= ui.item.value;
    //loadGarmet(val);  
    findinvestigation(ui.item.value,rid);
},
max:1,
source: results});


}
function mparametersearch(rid)
{

	var results = [];
	var json_url = '<?php echo base_url(); ?>uploads/investigation.json';
	$.getJSON(json_url, function(data) {
		for (var i = 0, len = data.length; i < len; i++) {
			var res = data[i].replace("&#39;","'");
			results.push(res);
		}   
	}); 

	$("#search_parameter_"+rid).autocomplete({    
		select: function(event, ui){
      //$("#operation_seq").show(); 
      $("#error-msg").html("");
      $("#div-block").removeClass('div-block-2-col-seq');
      $("#div-block").addClass('div-block-2-col-ad'); 

      $("#div-cont2").removeClass('div-block-2-col');
      $("#div-cont2").addClass('div-block-2-col-ad');

      // alert('enabling the button');

      // enable the button when the product is added to the autocomplete text box
      //$("#drugAddBtn").removeAttr("disabled");
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
			if(rdata=='')
			{

			}
			else{
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
function clinicinvestigationsearch()
{
	var devices = [<?php echo $cinvg; ?>];

	var results = [];
	$.each(devices, function(k,v){
		results.push(v);  
	});   
    //results.push('New Patient'); 
    $("#search_investigation").autocomplete({ 
    	select: function(event, ui)
    	{
    //$("#operation_seq").show(); 
    $("#div-block").removeClass('div-block-2-col-seq');
    $("#div-block").addClass('div-block-2-col-ad'); 

    $("#div-cont2").removeClass('div-block-2-col');
    $("#div-cont2").addClass('div-block-2-col-ad');
    $("#drugAddBtn").removeAttr("disabled");
},
response: function(event, ui) {
            // ui.content is the array that's about to be sent to the response callback.


            if (ui.content.length === 0) {
            	$("#adddrug").css("display","block");
            	$("#search_investigation").val('');
            } else {        
            	$("#adddrug").css("display","none");
            }
        },
        max:1,
        source: results});


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
      //$("#operation_seq").show(); 
      $("#error-msg").html("");
      $("#div-block").removeClass('div-block-2-col-seq');
      $("#div-block").addClass('div-block-2-col-ad'); 

      $("#div-cont2").removeClass('div-block-2-col');
      $("#div-cont2").addClass('div-block-2-col-ad');

      // alert('enabling the button');
      
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
    // source: function( request, response ) {
    //  var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
  //          response( $.grep( results, function( item ){           
   //           return matcher.test(item);
  //          }) );

  //      }
});

}
function cinvestigationsearch()
{
	$("#alreadyadded").css("display","none");
	var results = [];
	var json_url = '<?php echo base_url(); ?>uploads/investigation.json';
	$.getJSON(json_url, function(data) {
		for (var i = 0, len = data.length; i < len; i++) {
			results.push(data[i]);
		}   
	}); 

	$("#search_investigation").autocomplete({   
		select: function(event, ui){
      //$("#operation_seq").show(); 
      $("#error-msg").html("");
      $("#div-block").removeClass('div-block-2-col-seq');
      $("#div-block").addClass('div-block-2-col-ad'); 

      $("#div-cont2").removeClass('div-block-2-col');
      $("#div-cont2").addClass('div-block-2-col-ad');

      // alert('enabling the button');
      findclinicinvestigation(ui.item.value);
      // enable the button when the product is added to the autocomplete text box
      $("#drugAddBtn").removeAttr("disabled");
  },
  response: function(event, ui) {
            // ui.content is the array that's about to be sent to the response callback.
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
    // source: function( request, response ) {
    //  var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
  //          response( $.grep( results, function( item ){           
   //           return matcher.test(item);
  //          }) );

  //      }
});

}
function findclinicinvestigation(data)
{
	var base_url = '<?php echo base_url(); ?>';
	$.ajax({
		url : base_url+"/Lab/findclinicinvestigation",
		method : "POST",
		data : {"info":data},
		success : function(rdata) {       
			rdata = $.trim(rdata);            
			if(rdata=='')
			{
				$("#alreadyadded").css("display","none");
			}
			else{
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
			$("#div-block").removeClass('div-block-2-col-seq');
			$("#div-block").addClass('div-block-2-col-ad'); 

			$("#div-cont2").removeClass('div-block-2-col');
			$("#div-cont2").addClass('div-block-2-col-ad');
			add_row('orderlist',ui.item.value)
		},
		response: function(event, ui) {
            // ui.content is the array that's about to be sent to the response callback.
            if (ui.content.length === 0) {
            	$("#adddrug").css("display","block");
            } else {
            	$("#adddrug").css("display","none");
            	$("#orderlist1").css("display","block");
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

// $('#search_pharmacy_tb').keyup(function() {
//     if(globalTimeout != null) {
//         alert(globalTimeout);
//         clearTimeout(globalTimeout);
//     }
//     globalTimeout = setTimeout(function() {
//         globalTimeout = null; 
//         //ajax code
//         $.ajax({
//             url : base_url+"/Pharmacy_orders/searchDrugs",
//             method : "POST",
//             data : {"trade_name":$('#search_pharmacy_tb').val()},
//             success : function(drugInfo) {       
//                 alert(drugInfo);
//             }
//         });
//     }, 500);  
// });   


// function searchDrugs(value){
//     $.ajax({
//         url : base_url+"/Pharmacy_orders/searchDrugs",
//         method : "POST",
//         data : {"trade_name":value},
//         success : function(drugInfo) {       
//             alert(drugInfo);
//         }
//     });
// }


function tname_search_drug_master() {

	var autoComplete = [];
	var json_url = '<?php echo base_url(); ?>uploads/drugs.json';
	$.getJSON(json_url, function(data) {
		for (var i = 0, len = data.length; i < len; i++) {
			autoComplete.push(data[i]);
		}   
	});  

	$("#search_pharmacy").autocomplete({

		select: function(event, ui){
			$("#error-msg").html("");
			$("#div-block").removeClass('div-block-2-col-seq');
			$("#div-block").addClass('div-block-2-col-ad'); 

			$("#div-cont2").removeClass('div-block-2-col');
			$("#div-cont2").addClass('div-block-2-col-ad');
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
    //results.push('New Patient'); 
    $("#search_patients").autocomplete({ 
    	select: function(event, ui)
    	{
    //$("#operation_seq").show(); 
    $("#div-block").removeClass('div-block-2-col-seq');
    $("#div-block").addClass('div-block-2-col-ad'); 

    $("#div-cont2").removeClass('div-block-2-col');
    $("#div-cont2").addClass('div-block-2-col-ad');
    var val= ui.item.value;
    //loadGarmet(val);  
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
        //$("#tabsearch").show();
        $("#patient_info").empty();
        $("#patient_info").append(rdata);
    }
});
}
function show_new_info()
{
	var base_url = '<?php echo base_url(); ?>';
	$.ajax({
		url : base_url+"/New_order/show_new_info",
		method : "POST",
		data : {"info":''},
		success : function(rdata) {       
        //$("#tabsearch").show();
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
  count = $("#"+id).find('tr').length;
  $("#listdiv").css("display","block");
  var base_url = '<?php echo base_url(); ?>';
  $.ajax({
  	url : base_url+"/New_order/get_drug_info",
  	method : "POST",
  	data : {"trade_name":trade_name, "formulation":formulation},
  	success : function(rdata) { 
            //console.log(rdata);
            if($.trim(rdata) == "NA"){
            	$("#error-msg").html("<span style='color:red;font-weight:bold;padding:10px;'>"+trade_name.toUpperCase()+" Not Avaialable in Inventory")
            }else{
            	$("#error-msg").html("");
              //rdata = rdata.replace(/\s+/g, '');
              rdata = $.trim(rdata);
        var a = rdata.split(":"); // discount : drug_id : formulation : composition;
        if($('#'+id+'_'+a[1]+'_tr').length > 0){
        	alert("Drug '"+trade_name.trim()+"' already added to the order");
          //return false;
      }else{
      	tableRow = '<tr id="'+id+'_'+a[1]+'_tr">';
      	tableRow += '<td class="text-center">'+count+' </td>';
      	tableRow += '<td> '+trade_name+' '+a[4]+'<br />('+a[2]+')</td>';
      	tableRow += '<td><a onclick="get_batch_details('+a[1]+');" data-toggle="modal" placeholder="quantity" data-target="#myModal"><input type="text" name="qty[]" id="qty_'+a[1]+'" class="form-control batchTxt" readonly placeholder="Place Required Quantity"> </a></td>';
      	tableRow += '<td><span id="actual_amt_span_'+a[1]+'" class="mrp text-right"></span></td>';
      	tableRow += '<td class="text-center"><input type="hidden" class="disc text-center" name="disc[]" id="disc_'+a[1]+'" value="0" style="width:80px"></td>';
      	tableRow += '<td class="text-right"><span id="amt_span_'+a[1]+'" class="mrp" style="display:none"></span><input type="hidden" name="toqty[]" id="tqty_'+a[1]+'" /><input type="hidden" name="toamt[]" id="toamt_'+a[1]+'" /><input type="hidden" name="totrw[]" id="totrw'+count+'" class="totrw" value="'+a[1]+'" /><input type="hidden" id="disc_tb_'+a[1]+'" value="'+a[0]+'" /><input type="hidden" name="amt[]" id="amt_'+a[1]+'" value="" class="testp" /><input type="hidden" name="drgid[]" id="drgid_'+count+'" value="'+a[1]+'" /></td>';
      	tableRow += '<td class="text-center" style="vertical-align: center"><i class="fa fa-minus-circle delete remove_drug_p" id="'+id+'_'+a[1]+'_tr"><i></td>';
      	tableRow += '</tr>';
      	$("#"+id).append(tableRow);

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
	if(eqty>mqty)
	{
		alert("Enter Correct Quantity");
		$("#bqty_"+id).val('');
	}
}

function storelinedetails()
{ 
	var did = $("#d_id").val();
	var val = [];
	var amt = $("#amt_"+did).text();
	var ptotal = parseFloat($("#ip_total").val());
	var qty=0; var info=''; var mrp = 0; var ramt = 0;

	$('.batch_cb:checkbox:checked').each(function(i){

		val[i] = $(this).val();
		if (val[i].indexOf('/') > 0) {
			val[i] = val[i].replace('/', '\\/');
		}

		if(info=='')
			info = info+$(this).val()+' :: '+$("#bqty_"+val[i]).val();
		else
			info = info+', '+$(this).val()+' :: '+$("#bqty_"+val[i]).val();

		qty = parseInt(qty)+parseInt($("#bqty_"+val[i]).val());
		ramt = parseFloat(ramt)+(parseInt($("#bqty_"+val[i]).val()))*(parseFloat($("#unitp_"+val[i]).val()).toFixed(2));
	});

	if(!isNaN(qty)){
		$('.mrp').css("display","block");
		$("#tqty_"+did).val(qty);
		$("#qty_"+did).val(info);
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
		alert("Please enter Quantity");
	}
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
function ienable_discount(damt){
	var ptotal = $("#ip_total").val();
	var total = 0;
	var j=0;
	$(".invgids").each(function () {
		var index = $(this).val();

		if(!isNaN(index)){
			if ( $('#iapdis').is(":checked")){
				$('.disc').prop('type','text');
				$('.disc').attr('readonly','readonly');
				$('#disc_'+index).val($("#disc_tb_"+index).val());
				var amt = $("#mrp_"+index).val();
				var dis = damt;
				var dec = (dis/100).toFixed(2);
				var mult = amt*dec;
				$("#amt_span_"+index).text((amt-mult).toFixed(2));  
				total = parseFloat(total) + parseFloat((amt-mult).toFixed(2));
			}
			else
			{
				var amt = $("#mrp_"+index).val();
				$("#amt_span_"+index).text(parseFloat(amt).toFixed(2));     
				if(amt!='')
				{
					total = parseFloat(total) + parseFloat(parseFloat(amt).toFixed(2));       
					j++;
				}
			}
		}
	});

	if(j>=1){

		if ( $('#iapdis').is(":checked")){
			$("#p_total").text(total.toFixed(2));$("#ip_total").val(total.toFixed(2));  
		}
		else
		{
			$("#p_total").text(total.toFixed(2));$("#ip_total").val(total.toFixed(2));
		}
	}
	else{
		if ( $('#iapdis').is(":checked")){
			$("#p_total").text(total.toFixed(2));$("#ip_total").val(total.toFixed(2));  
		}
		else{
			$("#p_total").text('0');$("#ip_total").val('0');
		}
	}
}


function enable_discount(){

	var ptotal = $("#ip_total").val();
	var total = 0;
	var j=0;
	$(".totrw").each(function () {
		var index = $(this).val();
		if(!isNaN(index)){
			if ( $('#apdis').is(":checked")){
				$('.disc').prop('type','text');
				$('.disc').attr('readonly','readonly');
				$('#disc_'+index).val($("#disc_tb_"+index).val());
				var amt = $("#toamt_"+index).val();
				var dis = $("#disc_tb_"+index).val();
				var dec = (dis/100).toFixed(2);
				var mult = amt*dec;
				$("#amt_span_"+index).text((amt-mult).toFixed(2));  
				total = parseFloat(total) + parseFloat((amt-mult).toFixed(2));
			}
			else
			{

				$('.disc').val(0);
				var amt = $("#toamt_"+index).val();
				$("#amt_span_"+index).text(parseFloat(amt).toFixed(2));     
				if(amt!='')
				{
					total = parseFloat(total) + parseFloat(parseFloat(amt).toFixed(2));       
					j++;
				}

			}
		}
	});

	if(j>=1){

		if ( $('#apdis').is(":checked")){
			$("#p_total").text(total.toFixed(2));$("#ip_total").val(total.toFixed(2));  
		}
		else
		{
			$("#p_total").text(total.toFixed(2));$("#ip_total").val(total.toFixed(2));
		}
	}
	else{
		if ( $('#apdis').is(":checked")){
			$("#p_total").text(total.toFixed(2));$("#ip_total").val(total.toFixed(2));  
		}
		else{
			$("#p_total").text('0');$("#ip_total").val('0');
		}
	}
}


function add_orderlist_excel_m(id)
{
	count = $("#"+id).find('tr').length;
	$("#"+id).append('<tr id="'+id+'_'+count+'" data-toggle="tooltip" data-placement="top" title=""><td><input class="form-control" type="text" name="parameter[]" id="search_parameter_'+count+'" onclick="mparametersearch('+"'"+count+"'"+');" /></td><td class="text-center"><button type="button" class="btn btn-sm btn-success" onclick="add_orderlist_excel_m('+"'orderlist_excel'"+');"><i class="fas fa-plus"></i></button><button class="btn btn-sm btn-danger" type="button" onclick="remove_orderlist_excel('+"'"+id+'_'+count+"'"+');" ><i class="fa fa-minus"><i></td><button></tr>');
}

function add_orderlist_excel(id)
{
	count = $("#"+id).find('tr').length;
	$("#"+id).append('<tr id="'+id+'_'+count+'" data-toggle="tooltip" data-placement="top" title=""><td><input class="form-control" type="text" name="parameter[]" id="search_parameter_'+count+'" onclick="parametersearch('+"'"+count+"'"+');" /></td><td></td><td><input type="text" name="low[]" id="low_'+count+'" /></td><td><input type="text" name="high[]" id="high_'+count+'" /></td><td><input type="text" name="unit[]" id="unit_'+count+'" /></td><td><input type="text" name="method[]" id="method_'+count+'" onclick="investigationmethodsearch('+"'"+count+"'"+')"; /></td><td><textarea name="other_information[]" id="other_information_'+count+'" rows="5" cols="10"></textarea></td><td class="text-center"><button type="button" class="btn btn-sm btn-success" onclick="add_orderlist_excel('+"'orderlist_excel'"+');"><i class="fas fa-plus"></i></button><button class="btn btn-sm btn-danger" type="button" onclick="remove_orderlist_excel('+"'"+id+'_'+count+"'"+');" ><i class="fa fa-minus"><i></td><button></tr>');
}

function remove_orderlist_excel(id)
{
	$("#orderlist_excel tr#"+id).remove();
}

function add_orderlist_general_m(id)
{
	count = $("#"+id).find('tr').length;
	$("#"+id).append('<tr id="'+id+'_'+count+'" data-toggle="tooltip" data-placement="top" title=""><td><input type="text" name="parameter[]" id="search_parameter_'+count+'" /></td><td><textarea rows="5" cols="25" name="remarks[]"></textarea></td><td class="text-center"><button type="button" class="btn btn-sm btn-success" onclick="add_orderlist_general_m('+"'orderlist_general'"+');"><i class="fas fa-plus"></i></button><button class="btn btn-sm btn-danger" type="button" onclick="remove_orderlist_general('+"'"+id+'_'+count+"'"+');" ><i class="fa fa-minus"><i></td><button></tr>');
}

function add_orderlist_general(id)
{
	count = $("#"+id).find('tr').length;
	$("#"+id).append('<tr id="'+id+'_'+count+'" data-toggle="tooltip" data-placement="top" title=""><td><input type="text" name="parameter[]" id="search_parameter_'+count+'" onclick="parametersearch('+"'"+count+"'"+');" /></td><td><textarea rows="5" cols="25" name="remarks[]"></textarea></td><td class="text-center"><button type="button" class="btn btn-sm btn-success" onclick="add_orderlist_general('+"'orderlist_general'"+');"><i class="fas fa-plus"></i></button><button class="btn btn-sm btn-danger" type="button" onclick="remove_orderlist_general('+"'"+id+'_'+count+"'"+');" ><i class="fa fa-minus"><i></td><button></tr>');
}


function remove_orderlist_general(id)
{
	$("#orderlist_general tr#"+id).remove();
}


function add_inventory_row(id,drug_val)
{
	var drgm = drug_val;
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
			$("#"+id).append('<tr id="'+id+'_'+count+'" data-toggle="tooltip" data-placement="top" title="'+a[2]+'"><td> '+drg+'<br />('+a[1]+')<br /> </td><td><input class="form-control" type="text" name="batchno[]" required /></td><td><input class="form-control"  type="text" name="qty[]" required /></td><td><input class="form-control"  type="text" name="mrp[]" required /></td><td><input class="form-control"  type="text" name="rlevel[]" required value="'+a[3]+'" /></td><td><input  class="form-control" type="text" name="igst[]" required value="'+a[4]+'" /></td><td><input class="form-control"  type="text" name="cgst[]" required value="'+a[5]+'" /></td><td><input class="form-control"  type="text" name="sgst[]" required value="'+a[6]+'" /></td><td><input class="form-control"  type="text" name="disc[]" required value="'+a[7]+'" /></td><td><input class="form-control"  type="text" name="pack_size[]" required /></td><td><input type="text" class="form-control"  name="expiredate[]" class="form-control hasDatepicker" placeholder="dd/mm/yyyy" required /><input  class="form-control" type="hidden" name="drgid[]" value="'+a[0]+'" /></td><td><button class="btn btn-sm btn-danger remove_drug" id="'+id+'_'+count+'"><i class="fa fa-minus"><i></td><button></tr>'); 
			$("#search_pharmacy").val(''); 
			$("#orderlist1").css("display","block");
		}
	});
}


function add_investigation_package_row(id)
{
	var invg = $("#search_investigation").val();

  //var drg = drgm.substr(drgm.indexOf(' ')+1);
  //var drg = $("#search_pharmacy").val();
  count = $("#"+id).find('tr').length;
  $("#listdiv").css("display","block");
  var base_url = '<?php echo base_url(); ?>';
  if(invg!=''){
  	$.ajax({
  		url : base_url+"/Lab/get_clinic_investigation_info",
  		method : "POST",
  		data : {"invg":invg},
  		success : function(drgid) {
        //drgid = drgid.replace(/\s+/g, '');
        drgid = $.trim(drgid);
        //alert(drgid);
        var a = drgid.split(":");
        
        $("#"+id).append('<tr id="'+id+'_'+count+'" data-toggle="tooltip" data-placement="top" title="'+a[1]+'"><td> '+invg+'</td><td>'+a[2]+'</td><td>'+a[3]+'</td><td>'+a[5]+'</td><td>'+a[6]+'</td><td>'+a[7]+'</td><td>'+a[8]+'</td><td>'+a[9]+'</td><td>'+a[10]+'</td><input type="hidden" name="invgid[]" value="'+a[0]+'" /><input type="hidden" name="mrp[]" value="'+a[4]+'" /></td><td><button class="btn btn-sm btn-danger remove_drug" id="'+id+'_'+count+'"><i class="fa fa-minus"><i></td><button></tr>');  
        $("#search_investigation").val(''); 
        $("#orderlist1").css("display","block");
    }
});
  }
}
function add_investigation_order_row(id)
{
	var invg = $("#search_investigation").val();
	var ptotal = parseFloat($("#ip_total").val());

  //var drg = drgm.substr(drgm.indexOf(' ')+1);
  //var drg = $("#search_pharmacy").val();
  count = $("#"+id).find('tr').length;
  $("#listdiv").css("display","block");
  var base_url = '<?php echo base_url(); ?>';
  if(invg!=''){
  	$.ajax({
  		url : base_url+"/Lab/get_clinic_investigation_info_order",
  		method : "POST",
  		data : {"invg":invg,"rcount":count},
  		success : function(drgid) {
        //drgid = drgid.replace(/\s+/g, '');
        drgid = $.trim(drgid);
        //alert(drgid);
        var a = drgid.split(":");
        if(a[4]!='')    
        	ptotal = ptotal+parseFloat(a[4]);
        $("#"+id).append('<tr id="'+id+'_'+count+'" data-toggle="tooltip" data-placement="top" title="'+a[1]+'"><td> '+invg+'</td><td>'+a[2]+'</td><td>'+a[3]+'</td><td>'+a[5]+'</td><td><span id="amt_span_'+a[0]+'" >'+a[4]+'</span></td><input type="hidden" name="invgid[]" value="'+a[0]+'" class="invgids" /><input type="hidden" name="mrp[]" id="mrp_'+a[0]+'" value="'+a[4]+'" class="test" /><input type="hidden" name="type[]" value="'+a[6]+'" /></td><td><button class="btn btn-sm btn-danger remove_drug_i" id="'+id+'_'+count+'"><i class="fa fa-minus"><i></td><button></tr>'); 
        $("#search_investigation").val(''); 
        $("#orderlist1").css("display","block");
        $("#p_total").text(ptotal);$("#ip_total").val(ptotal);
    }
});
  }
}
function add_investigation_row(id)
{
	var invg = $("#search_investigation").val();
  //var drg = drgm.substr(drgm.indexOf(' ')+1);
  //var drg = $("#search_pharmacy").val();
  count = $("#"+id).find('tr').length;
  $("#listdiv").css("display","block");
  var base_url = '<?php echo base_url(); ?>';
  if(invg!=''){
  	$.ajax({
  		url : base_url+"/Lab/get_investigation_info",
  		method : "POST",
  		data : {"invg":invg},
  		success : function(drgid) {
  			drgid = drgid.replace(/\s+/g, '');
        //alert(drgid);
        var a = drgid.split(":");
        $("#"+id).append('<tr id="'+id+'_'+count+'" data-toggle="tooltip" data-placement="top" title="'+a[1]+'"><td> '+invg+'</td><td>'+a[2]+'</td><td>'+a[3]+'</td><td><input class="form-control" type="text" name="shortform[]" value="'+a[5]+'" /></td><td><input  class="form-control" type="text" name="mrp[]" required  /></td><td><input class="form-control"  type="text" name="lowrange[]" value="'+a[6]+'" /></td><td><input class="form-control"  type="text" name="highrange[]" value="'+a[7]+'" /></td><td><input class="form-control"  type="text" name="units[]" value="'+a[8]+'" /></td><td><input class="form-control"  type="text" name="method[]" id="method_'+count+'" onclick="investigationmethodsearch('+count+');" /></td><td><textarea class="form-control"  rows="2" cols="15" name="oinfo[]">'+a[9]+'</textarea></td><input type="hidden" name="invgid[]" value="'+a[0]+'" /></td><td><button class="btn btn-sm btn-danger remove_drug" id="'+id+'_'+count+'"><i class="fa fa-minus"><i></td><button></tr>');  
        $("#search_investigation").val(''); 
        $("#orderlist1").css("display","block");
    }
});
  }
}
function investigationmethodsearch(id)
{
	var devices = [<?php echo $methods; ?>];

	var results = [];
	$.each(devices, function(k,v){
		results.push(v);  
	});   
  //results.push('New Patient'); 
  $("#method_"+id).autocomplete({ 
  	select: function(event, ui)
  	{
  //$("#operation_seq").show(); 
  $("#div-block").removeClass('div-block-2-col-seq');
  $("#div-block").addClass('div-block-2-col-ad'); 

  $("#div-cont2").removeClass('div-block-2-col');
  $("#div-cont2").addClass('div-block-2-col-ad');
  var val= ui.item.value;
  //loadGarmet(val);  
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

	$("#orderlist tr#"+row_id).remove();
	count = $("#orderlist tbody").find('tr').length;  
	var total =0;
	if(count==0)
	{
		$("#orderlist1").css("display","none");
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
        if (charCode >= 48 || charCode <= 57 || charCode == 8)
            return true;
        else
            return false;
    }

</script>
</body>
</html>