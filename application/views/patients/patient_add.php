<div class="row page-header">
    <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><?php echo $_SESSION['clinic_name']; ?></a></li>
            <li class="breadcrumb-item"><a href="<?php echo base_url('Patients/'); ?>">Patients</a></li>
            <li class="breadcrumb-item active"><a href="#">ADD</a></li>          
        </ol>
    </div>
</div>

<!-- content start -->
<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <!-- card start -->
            <div class="card">
                <!-- <div class="card-body"> -->

                <form method="post" id="patient_form" action="<?php echo base_url('Patients/patient_add_save'); ?>" autocomplete="off" enctype="multipart/form-data" class="form customForm" name='Registration' >  
                    <input type="hidden" name="appointment_date" value="<?php echo $this->session->userdata('app_date'); ?>" />  
                    <input type="hidden" name="appointment_slot" value="<?php echo $this->session->userdata('app_slot'); ?>" />    
                    <input type="hidden" name="doctor_id" value="<?php echo $this->session->userdata('did'); ?>" />  
                    <input type="hidden" name="priority" value="<?php echo $this->session->userdata('priority'); ?>" />       
                    <input type="hidden" name="sms" value="<?php echo $this->session->userdata('sms'); ?>" />                          
                    <div id="patient_details">

                        <div class="row col-md-12">
                            <div class="col-md-12">
                                <h3>Demographic Details</h3>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="title" class="col-form-label">Title</label>
                                    <select id="title" name="title"  class="form-control">
                                        <option value="">-- Select --</option>
                                        <option value="Mr">Mr.</option>
										<option value="Miss">Miss</option>
										<option value="Ms">Ms.</option>
                                        <option value="Mrs">Mrs.</option>
										<option value="Master">Master</option>
										<option value="Baby">Baby</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="first_name" class="col-form-label">First Name <span class="imp">*</span></label>
                                    <input id="first_name" style="text-transform: capitalize;" name="first_name" type="text" class="form-control" required>
                                </div>
                            </div>
							
							<!-- <div class="col-md-4"><div class="form-group">
                               <label for="first_name" class="col-form-label">Middle Name</label>
                                    <input id="middle_name" name="middle_name" type="text" class="form-control">
                                </div>
                            </div> -->

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="last_name" class="col-form-label">Last Name</label>
                                    <input id="last_name" style="text-transform: capitalize;" name="last_name" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="gender" class="col-form-label">Gender</label>
                                    <select id="gender" name="gender" placeholder="" class="form-control" onchange="genderStatus(this.value)">
                                        <option value="">-- Select --</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>     
                                </div>
                            </div>
                        </div>

                        <div class="row col-md-12">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date_of_birth" class="col-form-label">Date of Birth</label>
                                   <!--  <input id="date_of_birth" name="date_of_birth" type="text" class="form-control" onchange="age_calculate(this.value)"> -->
                                   <input class="solo form-control" type="text" size=10 maxlength="10" name="date_of_birth" placeholder="DD / MM / YYYY"  />
                                </div>
                            </div> 

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="age" class="col-form-label">Age</label>
                                    <input id="age" name="age" type="number" placeholder="" class="form-control" value="0">
									<input id="age_unit" name="age_unit" type="hidden" placeholder="" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="occupation" class="col-form-label">Occupation</label>
                                    <input id="occupation" name="occupation" type="text" placeholder="Occupation" class="form-control" value="0">                                        
                                </div>
                            </div>											
                        </div>
                        
                        <div class="row col-md-12">

                            <div class="col-md-12 midGap">
                                <h3>Contact Details</h3>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="mobile" class="col-form-label">Mobile <span class="imp">*</span></label>
                                    <input id="mobile" name="mobile" type="text" placeholder="" class="form-control" required>
                                </div>
                            </div> 

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="phone" class="col-form-label">Alternate Mobile</label>
                                    <input id="phone" name="alternate_mobile" type="text" placeholder="" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="email_id" class="col-form-label">Email ID</label>
                                    <input id="email_id" name="email_id" type="text" placeholder="" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row col-md-12">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address" class="col-form-label">Address ()</label>
                                    <input id="address" name="address" type="text" placeholder="" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row col-md-12">                                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="state_id" class="col-form-label">State</label>
                                    <select id="state_id" name="state_id" placeholder="" class="form-control" onchange="getDistricts(this.value)" >
                                        <option value='0'>-- Select --</option>
                                        <?php foreach ($states as $value) { ?>
                                            <option value="<?php echo $value->state_id; ?>"><?php echo $value->state_name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="district_id" class="col-form-label">District</label>
                                    <select id="get_district_id" name="district_id" type="text" placeholder="" class="form-control">
                                        <option value='0'>-- Select --</option>
                                    </select>  
                                </div>
                            </div> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pincode" class="col-form-label">Pincode</label>
                                    <input id="pincode" name="pincode" type="text" placeholder="" class="form-control">
                                </div>
                            </div>                                           
                        </div>

                        <div class="col-md-12 midGap">
                            <h3>Other Details</h3>
                        </div>

                        <div class="row col-md-12">
							<div class="col-md-4">
                                <div class="form-group">
                                    <label for="preferred_language" class="col-form-label">Preferred Language</label>
									<select id="preferred_language" name="preferred_language" type="text" placeholder="" class="form-control">
                                        <option value="English">English</option>
                                        <option value="Hindi">Hindi</option>
                                        <option value="Telugu">Telugu</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="referred_by_type" class="col-form-label">Referred By</label>
                                    <select name="referred_by_type" placeholder="" class="form-control" onchange="referStatus(this.value)">
                                        <option value="">-- Select --</option>
                                        <option value="WOM">Word of Mouth</option>
                                        <option value="Doctor">By a Doctor</option>
                                        <option value="Online">Online</option>
                                    </select>
                                </div>
                            </div>
							<div class="col-md-4">
                                <!-- ask for person name -->
                                <div id="WOM" style="display: none;" class="form-group">
                                  <label for="referred_by_person" class="col-form-label">Person Name</label>
                                <input id="referred_by_person" name="referred_by" type="text" placeholder="Person Name" class="form-control" > 
                                </div>

                                <!-- ask for doctor name -->
                                <div id="Doctor" style="display: none;" class="form-group">
                                    <label for="doctor_name" class="col-form-label">Doctor Name</label>
                                    <!--<input id="doctor_name" name="referred_by" type="text" placeholder="Doctor name" class="form-control" >  -->
									<select name="referred_by" id="doctor_name" class="form-control">
									<option value="">--Select--</option>
									<?php foreach($doctors as $dresult){ ?>
									<option value="<?php echo $dresult['rfd_id'];?>" <?php if($patients_list->referred_by==$dresult['rfd_id']) echo "selected"; ?>><?php echo $dresult['doctor_name'];?></option>
									<?php } ?>
									</select>									
                                </div>  

                                <!-- ask for Online property -->
                                <div id="Online" style="display: none;" class="form-group">
                                    <label for="online_sb" class="col-form-label">Online</label>
                                    <select id="online_sb" name="referred_by" class="form-control">
                                        <option value="">-- Select --</option>
                                        <option value='Google'>Google</option>
                                        <option value='Facebook'>Facebook</option>
                                        <option value='Website'>Website</option>
                                        
                                    </select>    
                                </div>                                                                                              
                            </div>
                        </div>
						<!--
						<div class="row col-md-12">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="clinic_id" class="col-form-label">CLINIC</label>
                                    <select id="clinic_id" name="clinic_id" class="form-control" onchange="getDoctors(this.value)">
                                        <option value="">--select--</option>
                                        <?php foreach ($clinics as $value) { ?>
                                            <option value="<?php echo $value->clinic_id; ?>"><?php echo $value->clinic_name; ?></option>
                                        <?php } ?>
                                    </select>
                                    <input type="hidden" name="doctorid" id="doctorid" value="">
                                </div>
                            </div>
						</div>-->
						
						<!-- <div class="row col-md-12" id="book_aptmnt"> -->
                            <center>
                                <input type="submit" value="Register" name="submit" class="btn btn-success"> 
                            </center>
                        <!-- </div> -->
                    </div>                    
				</form>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">

function changeDiscount(val)
{	
	var totalamount = $("#total_amount").val();
	
	var ta = parseInt(totalamount)-parseInt(val);
	$("#totalamount").val(ta);
}

function age_calculate(dateString)
{
	
	if(dateString !='' || dateString != null) {
		var now = new Date();
        var today = new Date(now.getYear(),now.getMonth(),now.getDate());

        var yearNow = now.getYear();
        var monthNow = now.getMonth();
        var dateNow = now.getDate();
        var split = dateString.split("/");
        var dob = new Date(split[2],split[1]-1,split[0]);
        console.log(dob);
        var yearDob = dob.getYear();
        var monthDob = dob.getMonth();
        var dateDob = dob.getDate();
        var yearAge=0;
        var monthAge=0;
        var dateAge=0;
        
        if(yearDob >= 0){
            yearAge = yearNow - yearDob;

            if (monthNow >= monthDob){
                var monthAge = monthNow - monthDob;
            }else {
                yearAge--;
                var monthAge = 12 + monthNow -monthDob;
            }

            if (dateNow >= dateDob){
                var dateAge = dateNow - dateDob;
            }else {
                monthAge--;
                var dateAge = 31 + dateNow - dateDob;

                if (monthAge < 0) {
                    monthAge = 11;
                    yearAge--;
                }
            }

            if(yearAge ==0 && monthAge == 0){
                $('#au').empty();
                $('#age').val(dateAge);
                $('#age_unit').val('Day');
                $('#au').append('Days');
            }else if(yearAge ==0 && monthAge >0){
                $('#au').empty();
                $('#age').val(monthAge);
                $('#age_unit').val('Month');
                $('#au').append('Month');
            }else if(yearAge >0){
                $('#au').empty();
                $('#age').val(yearAge);
                $('#age_unit').val('Year');
                $('#au').append('Year');
            }else{
                $('#age').val(0);
                $('#age_unit').val('Year');
                $('#au').append('Year');
            }
        }else{
            $('#age').val(0);
            $('#age_unit').val('Years');
        }
	}
	 
}

    function changePayment(value) {
        var totalamount = "";
        totalamount = $("#total_amount").val();

        $("#totalamount").val(totalamount);
        $("#paymenttype").val(value);
        $(".payment_type").hide(1000);
        $("#patient_submit").show(1000);
        if (value == "CHEQUE") {
            $("#payment_cheque").show(1000);
        }
        if (value == "CARD") {
            $("#payment_card").show(1000);
        }
        if (value == "PAYTM") {
            $("#payment_paytm").show(1000);
        }
        if (value == "CASH") {
            $("#payment_cash").show(1000);
        }
		if (value == "FREE") {
           $("#totalamount").val(0);
        $("#paymenttype").val(value);
        }
    }


    $(function () {
        $("#transaction_date").datepicker({dateFormat: "yy-mm-dd"});
        $("#deposit_date").datepicker({dateFormat: "yy-mm-dd"});
        $("#date_of_birth").datepicker({dateFormat: "yy-mm-dd", maxDate: new Date(), changeYear: true, yearRange: "-100:+0"});
        $("#date_of_birth").on('change', function () {
            var dateString = Date.parse($(this).val());
            var today = new Date(),
                    dob = new Date(dateString),
                    age = new Date(today - dob).getFullYear() - 1970;

            //alert(age);
            $('#age').val(age);
        });
    });

    function getDistricts(id) {
        var url = "<?php echo base_url('Patients/getDistricts'); ?>";
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                id: id},
            beforeSend: function (data) {
                //$('#fpo_status_'+id).html('<img src="<?php echo base_url('assets/images/load.gif'); ?>" />');
            },
            success: function (data) {
                $('#get_district_id').html(data);
            }
        });
    }

    function genderStatus(val) {
        if (val == "Female") {
            $("#fe_status").show(1000);
        } else {
            $("#fe_status").hide(1000);
        }
    }
	
	function referStatus(val) {
		
		if (val == "WOM") {
            $("#WOM").show(1000);
			//$("#referred_by_person").prop("required",true);
			$("#Doctor").hide(1000);
			//$("#doctor_name").prop("required",false);
			$("#Online").hide(1000);
			//$("#online_sb").prop("required",false);
        } else if(val=='Doctor'){
            $("#WOM").hide(1000);
			//$("#referred_by_person").prop("required",false);
			$("#Doctor").show(1000);
			//$("#doctor_name").prop("required",true);
			$("#Online").hide(1000);
			//$("#online_sb").prop("required",false);
        }else if(val=='Online'){
			$("#WOM").hide(1000);
			//$("#referred_by_person").prop("required",false);
			$("#Doctor").hide(1000);
			//$("#doctor_name").prop("required",false);
			$("#Online").show(1000);
			//$("#online_sb").prop("required",true);
		}else{
			$("#WOM").hide(1000);
			//$("#referred_by_person").prop("required",false);
			$("#Doctor").hide(1000);
			//$("#doctor_name").prop("required",false);
			$("#Online").hide(1000);
			//$("#online_sb").prop("required",false);
		}
	}
	
    function getDoctors(clinic_id) {
        var url = "<?php echo base_url('Patients/getDoctors'); ?>";
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                clinic_id: clinic_id},
            beforeSend: function (data) {
                $('#doctors_details').html('<img src="<?php echo base_url('assets/images/load.gif'); ?>" />');
            },
            success: function (data) {
                $("label[for='patient_name']").html(first_name);
                /*  $( "#patient_details" ).slideToggle( "slow", function() {
                 
                 });*/
                //$('#patient_details').toggle();
                $('#doctors_details').html(data);
                // $("#appointment_date").show(1000);
                // $("#get_schedule").show(1000);
                // $("#book_aptmnt").hide(1000);

            }
        });
    }

    function checkDocters() {

        //$("#patient_details").hide(1000);doctors_dt
        var url = "<?php echo base_url('Patients/getClinicDocters'); ?>";
        var clinic_id = $("#clinic_id").val();
        var first_name = $("#first_name").val();
        /*if(first_name==""){alert("Please enter Firstname");
         $( "#first_name" ).focus();  return false;
         }
         var email_id = $("#email_id").val();
         if(email_id==""){
         alert("Please enter email id");
         $( "#email_id" ).focus();
         return false;
         }*/

        if (clinic_id == "") {
            alert("Please Select Clinic");
            $("#clinic_id").focus();
            return false;
        }

        //alert(clinic_id);
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                clinic_id: clinic_id},
            beforeSend: function (data) {
                $('#doctors_dt').html('<img src="<?php echo base_url('assets/images/load.gif'); ?>" />');
            },
            success: function (data) {
                $("label[for='patient_name']").html(first_name);
                /*  $( "#patient_details" ).slideToggle( "slow", function() {
                 
                 });*/
                //$('#patient_details').toggle();
                $('#doctors_dt').html(data);
                $("#appointment_date").show(1000);
                $("#get_schedule").show(1000);
                $("#book_aptmnt").hide(1000);

            }
        });

    }
    function getDoctWeekDays(id) {
        var url = "<?php echo base_url('Patients/getDoctWeekDays'); ?>";
        $("#appointment_dt").val('');
        $("#doctors_time_slot").html('');
        $("#patient_register").val();
        $("#get_schedule_res").show(1000);
        $("#book_aptmnt_submit").hide(1000);
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                clinic_id: id},
            beforeSend: function (data) {

// $('#doctors_dt').html('<img src="<?php //echo base_url('assets/images/load.gif');          ?>" />');
            }, success: function (data) {
                // getcal(data);
                // console.log(data);
                $('#xyz').html(data);
            }
        });
    }

    function getDoctorsWeekDays(id) {
        var url = "<?php echo base_url('Patients/getDoctorsWeekDays'); ?>";
        $("#appointment_dt").val('');
        $("#doctors_time_slot").html('');
        $("#patient_register").val();
        $("#get_schedule_res").show(1000);
        $("#book_aptmnt_submit").hide(1000);
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                clinic_id: id},
            beforeSend: function (data) {

            }, success: function (data) {

                $('#select_available_date').html(data);
            }
        });
    }
    function getPaymentInfo() {
        var url = "<?php echo base_url('Patients/getPaymentInfo'); ?>";
        var clinic_id = $("#clinic_id").val();
        var doctors_sch_id = $("#doctors_sch_id").val();
        var appointment_dt = $("#appointment_dt").val();
        var consulting_time = $("input[type='radio'][name='consulting_time']:checked").val();
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                doctors_sch_id: doctors_sch_id,
                clinic_id: clinic_id,
                appointment_dt: appointment_dt,
                consulting_time: consulting_time},
            beforeSend: function (data) {
                $('#doctor_payment_info').html('<img src="<?php echo base_url('assets/images/load.gif'); ?>" />');
            }, success: function (data) {
                // getcal(data);
                // console.log(data);
                $('#doctor_payment_info').html(data);

            }
        });
    }


    function getSchedule() {
		$("#doctor_payment_info").empty();
        var url = "<?php echo base_url('Patients/getSchedule'); ?>";
        var doctors_sch_id = $("#doctors_sch_id").val();
        var appointment_dt = $("#appointment_dt").val();
        var clinic_id = $("#clinic_id").val();
        $("#doctorid").val(doctors_sch_id)
        $("#get_schedule_res").hide(1000);
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                doctors_sch_id: doctors_sch_id,
                appointment_dt: appointment_dt,
                clinic_id: clinic_id},
            beforeSend: function (data) {
                $('#doctors_time_slot').html('<img src="<?php echo base_url('assets/images/load.gif'); ?>" />');
            }, success: function (data) {
                // getcal(data);
                // console.log(data);
                $('#doctors_time_slot').html(data);
                $("#get_schedule_res").hide(1000);
                $("#book_aptmnt_submit").show(1000);
            }
        });
    }


</script>      
 <script type="text/javascript" src="<?php echo base_url(); ?>assets/lib/jquery-validate/jquery.validate.js"></script>
        <script type="text/javascript">
        var soloInput = $('input.solo');

soloInput.on('keyup', function(){
  var v = $(this).val();
  if (v.match(/^\d{2}$/) !== null) {
    $(this).val(v + '/');
  } else if (v.match(/^\d{2}\/\d{2}$/) !== null) {
    $(this).val(v + '/');
  }  
  if(v.length == 10){
    age_calculate(v);
  }
});

        $( document ).ready( function () {
            $( "#patient_form" ).validate( {
                rules: {
                    first_name: "required",
                    mobile: {
                        required: true,
                        minlength: 10,
                        maxlength:10,
                        remote: { 
                  url: '<?php echo base_url('patients/check_mobile'); ?>', 
                    type: "post",} 
                       
                    },
                    alternate_mobile: {
                        minlength: 10,
                        maxlength:10,
                        remote: { 
                  url: '<?php echo base_url('patients/check_mobile'); ?>', 
                    type: "post",} 
                    }
                },
                messages: {
                    first_name: "Please enter your first name",
                    mobile:{
                        remote: "This mobile is already taken! Try another."
                    }
                  
                },
                errorElement: "em",
                errorPlacement: function ( error, element ) {
                    // Add the `help-block` class to the error element
                    error.addClass( "help-block" );

                    if ( element.prop( "type" ) === "checkbox" ) {
                        error.insertAfter( element.parent( "label" ) );
                    } else {
                        error.insertAfter( element );
                    }
                },
                highlight: function ( element, errorClass, validClass ) {
                    $( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
                },
                unhighlight: function (element, errorClass, validClass) {
                    $( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
                }
            } );

        } );
    </script>
