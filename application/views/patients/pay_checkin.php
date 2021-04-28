
<style type="text/css">
	.table td, .table th{
		border: none;
	}
  hr{
    margin: 10px 0;
  }
</style>
</head>
<body>
  <?php if($patient_payment_status->payment_status != 0 && $app_payment_status->payment_status != 0){ ?>
<div class="row">        
            <div class="col-md-12">
                <div class="card">
                  <div class="card-header font-weight-bold">
                    No amount due for this appointment <a class="btn btn-info" href="<?php echo base_url('calendar_view'); ?>">Go to Appointments</a>
                  </div>
                </div>
              </div>
            </div>
<?php  exit; } ?>
<div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?php echo $_SESSION['clinic_name']; ?></div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                            	<li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li><a  href="<?php echo base_url("calendar_view"); ?>">Appointments</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                              
                                <li class="active">Patient Payment</li>
                            </ol>
                        </div>
</div>
	
	<!-- hidden input text storing selected procedure -->
	<input type="hidden" name="procedure_id" value="<?php echo $this->session->userdata('selected_procedures'); ?>">

        <div class="row">        
            <div class="col-md-12">
                <div class="card">
                	<div class="card-header font-weight-bold">


								 
                      				<?php if(isset($app_info)){ ?>
                        				
                            				<span>APPOINTMENT BOOKING WITH <span class="margin-b-10  text-primary"><?php echo "DR." .strtoupper($app_info->first_name." ".$app_info->last_name); ?></span> <span class="margin-b-10 ">ON <?php echo date("d F Y",strtotime($app_info->appointment_date))." @ ".date("h:i A",strtotime($app_info->appointment_time_slot)); ?></span></span>
                        				
                        				
                      				<?php } ?>
                   				
                	</div>
                    <div class="card-body">						
						<?php 
						
						  	echo form_open(site_url("patients/pay_checkin/".$app_id), array("class" => "form-horizontal","id"=>"app_form")); ?>
						
						  
                      
                      	<div class="col-md-6 billing">
                      		<!-- Registration Fee -->
                      		<?php if($patient_payment_status->payment_status == 0){ ?>
                      		<div class="row col-md-12" style="margin-top: 10px">
                            
	                      		<div class="col-md-10 checkbox">
	                      			
										<input id="registration_cb" type="checkbox" name="registration" value="<?php echo $get_fee->registration_fee; ?>" onclick="feePayment();" checked> 
									<label for="registration_cb">Registration Fee</label>
	                      		</div>

	                      		<div class="col-md-2 text-left font-weight-bold">
	                      			<label id="registration_fee_lbl" class="price"><?php echo $get_fee->registration_fee; ?>/-</label>
									<input type="hidden" id="registration_fee_tb" name="registration_fee" value="<?php echo $get_fee->registration_fee; ?>">
	                      		</div>
	                      		<div class="col-md-2"></div>
                         
	                      	</div>
                          <hr>
	                      	<?php }else{ ?>
								<!-- hidden text input storing registration fees -->
                          		<input type="hidden" id="registration_fee_tb" name="registration_fee" value="0">
                          	<?php } ?>

                          	<!-- Consultation Fee -->
                          	<?php if($app_payment_status->payment_status == 0){ ?>
                              
	                      	<div class="row col-md-12">
                            
	                      		<div class="col-md-10 checkbox">
	                      			
										<input id="consultation_cb" type="checkbox" name="consultation" value="<?php echo $get_info->consulting_fee; ?>" onclick="feePayment();" checked> 
									<label for="consultation_cb">Consultation Fee</label>
	                      		</div>
	                      		<div class="col-md-2 text-left font-weight-bold">
	                      			<label id="consultation_fee_lbl" class="price"><?php echo $get_info->consulting_fee; ?>/-</label>
									<input type="hidden" id="consultation_fee_tb" name="consultation_fee" value="<?php echo $get_info->consulting_fee; ?>">
	                      		</div>
	                      		<div class="col-md-2"></div>
                        
	                      	</div>
                          <hr>
	                      	<?php }else{ ?>
                <!-- hidden text input storing registration fees -->
                              <input type="hidden" id="consultation_fee_tb" name="consultation_fee" value="0">
                            <?php } ?>
	                      	<!-- Procedures select box -->
	                      	<div class="row col-md-12">
	                      		<div class="col-md-8 btmGap">
	                      			<label for="procedure_select" class="noGap">Select Procedure for billing(if any)</label>
    								<select id="procedure_select" name="procedure_select" class="form-control select2">
        								<option value="">-- Select Procedure --</option>
        								<?php
        								foreach ($procedures as  $value) { ?>
            								<option data-val="<?php echo $value->procedure_cost; ?>" value="<?php echo $value->clinic_procedure_id; ?>"><?php echo $value->procedure_name; ?></option>
       									<?php } ?>
									</select> 
	                      		</div>
	                      		<div class="col-md-4"></div>
	                      	</div>


	                      	<!-- Existing procedures list (if any were already selected) -->
	                      	<div id="cart-table" class="col-md-12" style="margin: 10px 0">
	                      		<?php
								if(isset($selected_procedures) && count($selected_procedures)>0){
									foreach ($selected_procedures as  $value) { ?>
										<div class="row pdngBtm" style="margin:15px 0;" id="<?php echo $value->clinic_procedure_id.'_div'; ?>">
											<div class="col-md-8">
												<input type="hidden" class="cart-service-<?php echo $value->clinic_procedure_id; ?>" name="cart_services[]" value="<?php echo $value->procedure_name; ?>"><?php echo $value->procedure_name; ?>							
											</div>
											<div class="col-md-2 text-right">
												<input type="hidden" name="cart_prices[]" class="cart-price-<?php echo $value->clinic_procedure_id; ?>" value="<?php echo $value->procedure_cost; ?>"><?php echo $value->procedure_cost; ?> /-
											</div>
											<div class="col-md-2 text-left error delete-cart-row">
												<i class="fas fa-times-circle" onclick="return delDiv('<?php echo $value->clinic_procedure_id.'_div'; ?>');"></i>
											</div>
										</div>
									<?php } 
									}
								?>
							</div>	              
              <hr>
							<!-- Total Amount -->
							<div class="row col-md-12" style="background: #fbfbfb;padding-top: 10px">
								<div class="col-md-10 btmGap">
	                      			<label for="procedure_select" class="noGap font-weight-bold">Total Amount</label> 
	                      		</div>
	                      		<div class="col-md-2 text-left" id="total_amt_td"></div>
	                      		
	                      			<input id="billing_amount_tb" type="hidden" name="total_amt" value="">
	                      		
							</div>

							<!-- Discount text Field -->
							<div class="row col-md-12" style="background: #fbfbfb">
								<div class="col-md-10 btmGap">
									<label for="procedure_select" class="noGap control-label font-weight-bold">Discount</label>
									<select onchange="changeDiscount();" id="discount_type_sb" name="discount_type" class="form-control" style="width: 70px">
										<option>INR</option>
										<option>%</option>
									</select>
								</div>
								<div class="col-md-2">
									<input id="discount_tb" onkeyup = "calcDiscount();" type="number" min="0" max="100" name="discount" class="text-left form-control" value="0">
								</div>
								
							</div>
                        <div class="row col-md-12" style="background: #fbfbfb;padding-bottom: 10px">
                <div class="col-md-10 btmGap">
                  <label for="procedure_select" class="noGap control-label font-weight-bold">NET PAY</label>
                 
                </div>
                <div class="col-md-2">
                  <span id="net_pay"></span>
                </div>
                
              </div>

              <hr>
                     
                      	
                      	<div class="row col-md-12">
                            <div class="col-md-6">
                          		PAYMENT MODE<br>
								<select class="form-control" name="payment_mode" id="payment_type">
									<option value="cash">Cash</option>
									<option value="card">Card</option>
									<option value="online">Online</option>
									<option value="paytm">Paytm</option>
									<option value="googlepay">Google Pay</option>
								</select>
                            </div>
                      	</div>
					
    					<div class="row col-md-6 text-center" style="margin-top: 20px;float: right;margin-right: 10px">
          					<input style="margin-right:10px" type="submit" class="btn btn-primary" name='confirm_payment' id="submit" value="Accept Payment">  
								   
        				</div>
                  </div>
        				<?php echo form_close() ?>
					</div>
				</div>
			</div>
			</div>
		</div>


<script>
var total = 0;
var payment_status = '<?php echo $patient_payment_status->payment_status; ?>';
if($("#input_registration_val").length == 1) {
  var reg_fee = '<?php echo $get_fee->registration_fee; ?>';
  var reg_val = $("input[id='input_registration_val']").val();
  }
  else{
    var reg_fee= 0;
    var reg_val = 0;
  }

  if($("#input_consultation_val").length == 1) {
  var cons_fee = '<?php echo $get_info->consulting_fee; ?>';
    
    var cons_val = cons_fee;
  }
  else{
   var cons_fee = 0;
    
    var cons_val = 0;
  }
    
    
  var payment_status = '<?php echo $patient_payment_status->payment_status; ?>';
  $(document).ready(function(){

    billing();
    
  });


  function billing(){

 if($('#discount_tb').val() == '' || $('#discount_tb').val() < 0){
     var disc = 0;
    }
    else{
      var disc = $("#discount_tb").val();
    }
  	var total = 0;
    var procedureTotal = 0;
    
    $("input[name='cart_prices[]']").each(function( index ) {
        var Price = $(this).val();
        procedureTotal = (procedureTotal + (parseFloat(Price)));
    });

    total = parseInt($("#consultation_fee_tb").val()) + parseInt($("#registration_fee_tb").val()) + parseFloat(procedureTotal);
    $("#billing_amount_tb").val(total);
    
    $("#total_amt_td").html(total+' /-');

    if($("#discount_type_sb option:selected").val() == 'INR'){
      var total = total - parseInt($("#discount_tb").val());
    }else{
      var total = total - (total * parseInt($("#discount_tb").val())/100);
    }

    if(total == 0){
      $("#discount_tb").attr("readonly","readonly");
    }else{
      $("#discount_tb").removeAttr("readonly");
    }

    $("#net_pay").html(total);
  //  $("#procedure_select").val("");

  }


  function feePayment(){
    //check if both consultation & registration fee check boxes are checked
    if($("#consultation_cb").is(':checked') && $("#registration_cb").is(':checked')){
      
      $("input[id='free_cb']").prop("checked",false);
      // remove readonly attribute for discount textbox
      $("#discount_tb").removeAttr("readonly");
      // lable color to dark black
      $("#consultation_fee_lbl").css('color','#000');
      $("#consultation_fee_lbl").css('text-decoration','none');
      $("#registration_fee_lbl").css('color','#000');
      $("#registration_fee_lbl").css('text-decoration','none');

      $("#consultation_fee_tb").val($("#consultation_fee_lbl").html());
      $("#registration_fee_tb").val($("#registration_fee_lbl").html());

      billing();

    }else if($("#consultation_cb").is(':checked') || $("#registration_cb").is(':checked')){// if any one is checked

      // uncheck free checkbox
      $("input[id='free_cb']").prop("checked",false);
      // remove readonly attribute for discount textbox
      $("#discount_tb").removeAttr("readonly");

      if($("#consultation_cb").is(':checked')){ // if consultation fee 
        // label color to dark black
        $("#consultation_fee_lbl").css('color','#000');
        $("#consultation_fee_lbl").css('text-decoration','none');
        $("#consultation_fee_tb").val($("#consultation_fee_lbl").html());

        // turn the label color into light grey
        $("#registration_fee_lbl").css('color','#999');
        $("#registration_fee_lbl").css('text-decoration','line-through');
        // Make the textbox value to 0
        $("#registration_fee_tb").val(0);        

      }else if($("#registration_cb").is(':checked')){
        //alert("registration checked");
        // lable color to dark black
        $("#registration_fee_lbl").css('color','#000');
        $("#registration_fee_lbl").css('text-decoration','none');
        $("#registration_fee_tb").val($("#registration_fee_lbl").html());

        // turn the label color into light grey
        $("#consultation_fee_lbl").css('color','#999');
        $("#consultation_fee_lbl").css('text-decoration','line-through');
        // Make the textbox value to 0
        $("#consultation_fee_tb").val(0);        
      }
      billing();
    }else{

    	if($('#billing_amount_tb').val() == 0) { // if the billing amount is 0;
    		$("input[id='free_cb']").prop("checked",true);		
    	}
      
		// label color to light grey 
		$("#consultation_fee_lbl").css('color','#999');
		$("#consultation_fee_lbl").css('text-decoration','line-through');
		$("#registration_fee_lbl").css('color','#999');
		$("#registration_fee_lbl").css('text-decoration','line-through');

		// make the both fee to 0
		$("#consultation_fee_tb").val('0');
		$("#registration_fee_tb").val('0');

		billing();
    }
}

// Temp. commented this option
// function paymentFree(){
//     if($("#free_cb").is(":checked")){
//       $("input[id='consultation_cb']").prop("checked",false);
//       $("input[id='registration_cb']").prop("checked",false);

//       // Make the textbox value to 0
//       $("#consultation_fee_tb").val(0);
//       $("#registration_fee_tb").val(0);

//       $("#consultation_fee_lbl").css('color','#999');
//       $("#consultation_fee_lbl").css('text-decoration','line-through');

//       $("#registration_fee_lbl").css('color','#999');
//       $("#registration_fee_lbl").css('text-decoration','line-through');

//       // make the type of discount to %
//       //$("#discount_type_sb option:selected").val() == 'INR';
//       $('#discount_type_sb').val('%').change();

//       // make discount textbox readonly, as attribute for discount textbox
//       $("#discount_tb").prop("readonly", true);
//       $("#discount_tb").val(100);
//     }else{
//       $("input[id='consultation_cb']").prop("checked",true);
//       $("input[id='registration_cb']").prop("checked",true);

//       $("#consultation_fee_tb").val($("#consultation_fee_lbl").html());
//       $("#registration_fee_tb").val($("#registration_fee_lbl").html());

//       $("#registration_fee_lbl").css('color','#000');
//       $("#registration_fee_lbl").css('text-decoration','none');

//       $("#consultation_fee_lbl").css('color','#000');
//       $("#consultation_fee_lbl").css('text-decoration','none');

//       // make discount textbox readonly, as attribute for discount textbox
//       $("#discount_tb").removeAttr("readonly");
//       $("#discount_tb").val(0)
//     }   
//     billing();
// }

function delDiv(id){
  	$("#"+id).remove();
  	// call billing
  	billing();
}
</script>

   
<script type="text/javascript">

	$(function(){
   $('#procedure_select').select2({
  theme: "bootstrap",
    allowClear: true,
    placeholder: "select procedure"
});

	    $('#procedure_select').on('change', function() {
	        var procedureName = $("#procedure_select option:selected").text();

	        var procedureId = $("#procedure_select option:selected").val();

	     	var procedureAmount = $("#procedure_select option:selected").attr("data-val");
        if(procedureId!=""){
          var isAdded = checkExists(procedureId);
          if(isAdded == 0){

          var cartDiv =   '<div class="row pdngBtm" style="margin:15px 0;" id="'+procedureId+'_div">\n' + 
                    '<div class="col-md-8"><input type="hidden" class="cart-service-'+procedureId+'" name="cart_services[]" value="'+procedureName+'">'+procedureName+'</div>\n' +
                    '<div class="col-md-2 text-right"><input type="hidden" name="cart_prices[]" class="cart-price-'+procedureId+'" value="'+procedureAmount+'">'+procedureAmount+' /-</div>\n' +
                    '<div class="col-md-2 text-left error delete-cart-row"><i class="fas fa-times-circle" onclick="return delDiv(\''+procedureId+'_div\');"></i></div>\n' + 
                    '</div>';

              $("#cart-table").append(cartDiv);
              $('#procedure_select').val('').trigger("change");
  //$("#procedure_select").select2('val', '');
//               $("#procedure_select").select2("destroy");

//                $('#procedure_select').select2({
//   theme: "bootstrap",
//     allowClear: true,
//     placeholder: "select procedure"
// });

              billing();
          }
        }
	     	

	    	
	    })

	});

	function checkExists(pId) { 

		var isAdded = $(".cart-service-"+pId).length;
	   	console.log(isAdded);
	    if(isAdded > 0){
	        alert("procedure already added to list");
          $('#procedure_select').val('').trigger("change");
	        return 1;
	    }
	    return 0;
     // $('#procedure_select').val(null).trigger('change');
	}

	function changeDiscount(){
		// if($("#discount_type_sb option:selected").val() == 'INR'){
		// 	if()		
		// }else{
			
		// }
		$("#discount_tb").val(0);
		billing();
	}

	function calcDiscount(){
		
		$("#discount_tb").focus();
		billing();
	}
</script>
</body>
</html>