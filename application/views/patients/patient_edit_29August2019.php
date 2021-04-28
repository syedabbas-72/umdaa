<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li>Patient Profile&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Edit</li>
        </ol>
    </div>
</div>

<!-- Section Start -->
<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card noCardPadding">
                <div class="pull-left page-title">
                    <?php count($appointmentInfo) > 0 ? $this->load->view('profile/appointment_info_header', $appointmentInfo[0]->patient_id) : ''; ?>
                </div>
                <div class="row col-md-12 "> 
                    <div class="col-md-3" id="view_casesheet">
                        <div class="col-md-12 ">
                            <?php $this->load->view('profile/patient_info_left_nav'); ?>
                        </div>
                    </div>
                    <div class="col-md-9" id="view_caseresults" class="view_caseresults">
                        <div class="row page-title">
                            <div class="col-md-12">
                                Profile Edit
                            </div>
                        </div>

                        <form method="post" id="patient_form" action="<?php echo base_url('Patients/patient_update/'.$appointmentInfo[0]->patient_id.'/'.$appointmentInfo[0]->appointment_id); ?>" autocomplete="off" enctype="multipart/form-data" class="form customForm" name='Registration' >

                            <!-- Profile Pic - Save to DB with Ajax -->
                            <div class="row" style="padding-top: 10px">
                                <div class="col-md-2 editProfilePic fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-preview" data-trigger="fileinput">
                                        <img src="<?php echo ($patients_list->photo=='') ? base_url('assets/img/profilePic.jpg') :base_url('uploads/patients/'.$patients_list->photo) ?>" />
                                    </div>
                                </div>    
                                <div class="col-md-4 chooseProfilePic">
                                    Upload an image!
                                    <input type="hidden" value="<?= $patients_list->photo;?>">
                                    <input type="file" id="image" name="profile_image" value="<?php echo $patients_list->photo; ?>">
                                    <input type="hidden" id="profilePic" name="profilePic" value="">
                                    <div id="results"></div>
                                </div>
                                <div class="col-md-4 webCamProfilePic">
                                    Take a pic using Web Cam!
                                    <input type="button" value="Webcam Upload" onClick="setup(); $(this).hide().next().show();">
                                    <input type="button" value="Capture Image" onClick="take_snapshot()" style="display:none">
                                    <div id="my_camera"></div>
                                </div>
                            </div>
                            <div class="row col-md-12" style="padding: 10px" >
                                <div class="col-md-12 formDiv">   
                                     
                                    <!-- Hidden input types -->
                                    <input type="hidden" name="appointment_date" value="<?php echo $this->session->userdata('app_date'); ?>" />  
                                    <input type="hidden" name="appointment_slot" value="<?php echo $this->session->userdata('app_slot'); ?>" />    
                                    <input type="hidden" name="doctor_id" value="<?php echo $this->session->userdata('did'); ?>" />  
                                    <input type="hidden" name="priority" value="<?php echo $this->session->userdata('priority'); ?>" />       
                                    <input type="hidden" name="sms" value="<?php echo $this->session->userdata('sms'); ?>" />

                                    <!-- Sub header in the form -->
                                    <div class="row text-center docInfoHdr">
                                        <div class="col-md-6 text-left">
                                            Demographic Details
                                        </div>
                                    </div>

                                    <!-- Demographic fields start -->
                                    <div class="row">                          
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="title" class="col-form-label">Title</label>
                                                <select id="title" name="title"  class="form-control">
                                                    <option value="">-- Select --</option>
                                                    <option value="Mr" <?php if($patients_list->title=='Mr'){echo 'selected';} ?>>Mr.</option>
                                                    <option value="Miss" <?php if($patients_list->title=='Miss'){echo 'selected';} ?>>Miss</option>
                                                    <option value="Ms" <?php if($patients_list->title=='Ms'){echo 'selected';} ?>>Ms.</option>
                                                    <option value="Mrs" <?php if($patients_list->title=='Mrs'){echo 'selected';} ?>>Mrs.</option>
                                                    <option value="Master" <?php if($patients_list->title=='Master'){echo 'selected';} ?>>Master</option>
                                                    <option value="Baby" <?php if($patients_list->title=='Baby'){echo 'selected';} ?>>Baby</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="first_name" class="col-form-label">First Name <span class="imp">*</span></label>
                                                <input id="first_name" value='<?php echo ucfirst($patients_list->first_name); ?>' style="text-transform: capitalize;" name="first_name" type="text" class="form-control" required  onkeypress="return alpha()">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="last_name" class="col-form-label">Last Name</label>
                                                <input id="last_name" value='<?php echo ucfirst($patients_list->last_name); ?>' style="text-transform: capitalize;" name="last_name" type="text" class="form-control"  onkeypress="return alpha()">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="gender" class="col-form-label">Gender</label>
                                                <select id="gender" name="gender" placeholder="" class="form-control" onchange="genderStatus(this.value)">
                                                    <option value="">-- Select --</option>
                                                    <option value="Male" <?php if($patients_list->gender=='Male'){echo 'selected';}?>>Male</option>
                                                    <option value="Female" <?php if($patients_list->gender=='Female'){echo 'selected';}?>>Female</option>
                                                    <option value="Other" <?php if($patients_list->gender=='Other'){echo 'selected';}?>>Other</option>
                                                </select>     
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="date_of_birth" class="col-form-label">Date of Birth</label>
                                                <input class="solo form-control dob" id="dob" readonly type="text" name="date_of_birth" placeholder="DD / MM / YYYY" <?php if($patients_list->date_of_birth == '' || $patients_list->date_of_birth == NULL || $patient_list->date_of_birth == '0000-00-00') { echo "value = ''"; }else{ echo "value = '".date("d/m/Y",strtotime($patients_list->date_of_birth))."'"; } ?>>
                                            </div>
                                        </div> 
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="age" class="col-form-label">Age</label>
                                                <div class="form-inline">
                                                    <input id="age" name="age" type="number" placeholder="" class="form-control" value="<?php echo $patients_list->age?>" style="width: 70px" >
                                                    <select class="form-control" name="age_unit" >
                                                        <option>Select</option>
                                                        <option value="Days" <?=($patients_list->age_unit=="Days")?'selected':''?> >Days</option>
                                                        <option value="Weeks" <?=($patients_list->age_unit=="Weeks")?'selected':''?> >Weeks</option>
                                                        <option value="Months" <?=($patients_list->age_unit=="Months")?'selected':''?> >Months</option>
                                                        <option value="Years" <?=($patients_list->age_unit=="Years")?'selected':''?> >Years</option>
                                                    </select>
                                                </div>
                                                <!-- <input id="age_unit" name="age_unit" type="hidden" placeholder="" class="form-control"> -->
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="occupation" class="col-form-label">Occupation</label>
                                                <input id="occupation" style="text-transform: capitalize;" name="occupation" type="text" placeholder="Occupation" class="form-control" value="<?php echo ucfirst($patients_list->occupation); ?>">                                        
                                            </div>
                                        </div>                                          
                                    </div>

                                    <!-- Sub header in the form -->
                                    <div class="row text-center docInfoHdr">
                                        <div class="col-md-6 text-left">
                                            Contact Information
                                        </div>
                                    </div>

                                    <!-- Contact Information form -->
                                    <div class="row">         
                                        <?php if($patients_list->alternate_mobile != "" || $patients_list->alternate_mobile != NULL){
                                            $required = "";
                                        }
                                        else{
                                            $required = "required";
                                        }
                                        ?>  

                                        <!-- New line: Mobile, Alternate Mobile & Email Id -->               
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="mobile" class="col-form-label">Mobile <span class="imp">*</span></label>
                                                <input id="mobilea" name="mobile" type="text" onkeypress="return event.charCode >= 48 && event.charCode <= 57" placeholder="" class="form-control" value="<?php echo DataCrypt($patients_list->mobile, 'decrypt')?>" maxlength="10" <?php echo $required; ?>>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="phone" class="col-form-label">Alternate Mobile</label>
                                                <input id="phone" name="alternate_mobile" onkeypress="return event.charCode >= 48 && event.charCode <= 57" type="text" placeholder="" class="form-control" value="<?php echo DataCrypt($patients_list->alternate_mobile, 'decrypt')?>" maxlength="10">    
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="email_id" class="col-form-label">Email ID</label>
                                                <input id="email_id" name="email_id" type="email" placeholder="" class="form-control" value="<?php echo DataCrypt( $patients_list->email_id, 'decrypt')?>">
                                            </div>
                                        </div>

                                        <!-- New line: Address & Ares -->
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="address" class="col-form-label">Address (Building/Floor/Street/City)</label>
                                                <input id="address" style="text-transform: capitalize;" name="address" type="text" placeholder="" class="form-control" value='<?php echo $patients_list->address_line?>'>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="address" class="col-form-label">Area<span class="imp">*</span></label>
                                                <input id="location"  style="text-transform: capitalize;" name="location" type="text" placeholder="" class="form-control"  value='<?php echo $patients_list->location?>' required>
                                            </div>
                                        </div>

                                        <!-- New line: State, District & Pincode -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="state_id" class="col-form-label">State</label>
                                                <select id="state_id" name="state_id" placeholder="" class="form-control" onchange="getDistricts(this.value)" >
                                                    <option value='0'>-- Select --</option>
                                                    <?php foreach ($states as $value) { ?>
                                                        <option value="<?php echo $value->state_id; ?>" <?php if($patients_list->state_id==$value->state_id){echo "selected";} ?>><?php echo $value->state_name; ?></option>
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
                                                <input id="pincode" name="pincode" type="text" onkeypress="return event.charCode >= 48 && event.charCode <= 57" placeholder="" class="form-control" value="<?php echo $patients_list->pincode;?>"  maxlength="6">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sub header in the form -->
                                    <div class="row text-center docInfoHdr">
                                        <div class="col-md-6 text-left">
                                            Other Information
                                        </div>
                                    </div>

                                    <!-- New line: Preferred Language, Referred by & Dependent Field -->
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="preferred_language" class="col-form-label">Preferred Language</label>
                                                <select id="preferred_language" name="preferred_language" type="text" placeholder="" class="form-control">

                                                    <option <?php if($patients_list->preferred_language == "English"){echo "selected";} ?> value="English">English</option>
                                                    <option <?php if($patients_list->preferred_language == "Hindi"){echo "selected";} ?> value="Hindi">Hindi</option>
                                                    <option <?php if($patients_list->preferred_language == "Telugu"){echo "selected";} ?> value="Telugu">Telugu</option>
                                                    <option <?php if($patients_list->preferred_language == "Kannada"){echo "selected";} ?> value="Kannada">Kannada</option>
                                                    <option <?php if($patients_list->preferred_language == "Malayalam"){echo "selected";} ?> value="Malayalam">Malayalam</option>
                                                    <option <?php if($patients_list->preferred_language == "Tamil"){echo "selected";} ?> value="Tamil">Tamil</option>
                                                    <option <?php if($patients_list->preferred_language == "Urdu"){echo "selected";} ?> value="Urdu">Urdu</option>
                                                    <option <?php if($patients_list->preferred_language == "Sindhi"){echo "selected";} ?> value="Sindhi">Sindhi</option>
                                                    <option <?php if($patients_list->preferred_language == "Panjabi"){echo "selected";} ?> value="Panjabi">Panjabi</option>
                                                    <option <?php if($patients_list->preferred_language == "Gujarati"){echo "selected";} ?> value="Gujarati">Gujarati</option>
                                                    <option <?php if($patients_list->preferred_language == "Marathi"){echo "selected";} ?> value="Marathi">Marathi</option>
                                                    <option <?php if($patients_list->preferred_language == "Bengali"){echo "selected";} ?> value="Bengali">Bengali</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="referred_by_type" class="col-form-label">Referred By</label>
                                                <select name="referred_by_type" placeholder="" class="form-control" onchange="referStatus(this.value)" id="referred_by_type">
                                                    <option value="">-- Select --</option>
                                                    <option value="WOM" <?php if($patients_list->referred_by_type=='WOM'){echo 'selected';}?>>Word of Mouth</option>
                                                    <option value="Doctor" <?php if($patients_list->referred_by_type=='Doctor'){echo 'selected';}?>>By a Doctor</option>
                                                    <option value="Online" <?php if($patients_list->referred_by_type=='Online'){echo 'selected';}?>>Online</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">

                                            <!-- If Word of mouth selected: Ask for person's name -->
                                            <div id="WOMDiv" style="display: none;" class="form-group">
                                                <label for="referred_by_person" class="col-form-label">Person Name</label>
                                                <input id="referred_by_person" style="text-transform: capitalize" name="referred_by_p" type="text" placeholder="Person Name" class="form-control" value='<?php echo $patients_list->referred_by; ?>'> 
                                            </div>

                                            <!-- If Doctor selected: Ask to select a doctor or add a new one -->
                                            <div id="DoctorDiv" style="display: none;" class="form-group">
                                                <label for="referred_by_person" class="col-form-label">Doctor Name</label>
                                                <select name="referred_by_d" id="doctor_name" class="form-control">
                                                    <option value="">--Select--</option>
                                                    <?php foreach($doctors as $dresult){ ?>
                                                        <option value="<?php echo $dresult['rfd_id'];?>" <?php if($patients_list->referred_by==$dresult['rfd_id']) echo "selected"; ?>><?php echo $dresult['doctor_name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <!-- If Doctor selected: Ask to select a doctor or add a new one -->
                                            <div id="OnlineDiv" style="display: none;" class="form-group">
                                                <label for="online_sb" class="col-form-label">Online</label>
                                                <select id="online_sb" name="referred_by_o" class="form-control">
                                                    <option value="">-- Select --</option>
                                                    <option value='Google' <?php if($patients_list->referred_by=='Google'){echo "selected";}?>>Google</option>
                                                    <option value='Facebook' <?php if($patients_list->referred_by=='Facebook'){echo "selected";}?>>Facebook</option>
                                                    <option value='Website' <?php if($patients_list->referred_by=='Website'){echo "selected";}?>>Website</option>

                                                </select>    
                                            </div> 
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 submitBtn">
                                            <div class="text-center">
                                                <input type="submit" value="Save" name="submit" class="btn btn-success btn-rounded box-shadow">
                                            </div>
                                        </div>
                                    </div>               
                                    
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
    var CurrentYear = new Date().getFullYear();
    $('.dob').datepicker({
        maxDate : new Date(),
        changeYear : true,
        changeMonth : true,        
        shortYearCutoff: 50,
        yearRange: "1800:"+CurrentYear,
        dateFormat : 'dd/mm/yy'
    });
</script>

<script type="text/javascript">

$( document ).ready(function() {
    var selected_state = '<?php echo $patients_list->state_id; ?>';
  
    getDistricts(selected_state);

    var val='<?php echo $patients_list->referred_by_type; ?>';

    if (val == "WOM") {
        $("#WOMDiv").show();
        $("#DoctorDiv").hide();
        $("#OnlineDiv").hide();
    } else if(val=='Doctor'){
        $("#WOMDiv").hide();
        $("#DoctorDiv").show();
        $("#OnlineDiv").hide();
    }else if(val=='Online'){
        $("#WOMDiv").hide();
        $("#DoctorDiv").hide();
        $("#OnlineDiv").show();
    }

    $( "#patient_form" ).validate( {
        rules: {
            first_name: "required",
            location: "required",
            <?php if($patients_list->alternate_mobile=="" || $patients_list->alternate_mobile==NULL){ ?>
                mobile: {
                    required: true,
                    minlength: 10,
                    maxlength:10,
                },
            <?php } ?>
            alternate_mobile: {
                minlength: 10,
                maxlength:10,
            }
        },
        messages: {
            first_name: "* Required!",
            mobile:{
                remote: "* Already taken! Try another."
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
    });
});


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


function getDistricts(id) {
    var url = "<?php echo base_url('Patients/getDistricts'); ?>";
    var selected_district = '<?php echo $patients_list->district_id; ?>';
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
            if(selected_district!=0|| selected_district!= "NULL"|| selected_district!=""){

                $("#get_district_id").val(selected_district);
            }
        }
    });
}

function referStatus(val) {
    if (val == "WOM") {
        $("#WOMDiv").show();
        $("#DoctorDiv").hide();
        $("#OnlineDiv").hide();
    } else if(val=='Doctor'){
        $("#WOMDiv").hide();
        $("#DoctorDiv").show();
        $("#OnlineDiv").hide();
    }else if(val=='Online'){
        $("#WOMDiv").hide();
        $("#DoctorDiv").hide();
        $("#OnlineDiv").show();
    }else{
        $("#WOMDiv").hide();
        $("#DoctorDiv").hide();
        $("#OnlineDiv").hide();
    }
}


var soloInput = $('input.solo').val();

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

</script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/webcam.min.js"></script>

<script language="JavaScript">
Webcam.set({
    width: 320,
    height: 240,
    image_format: 'jpeg',
    jpeg_quality: 90
});

// Code to handle taking the snapshot and displaying it locally
function setup() {
    Webcam.attach('#my_camera');
}

function take_snapshot() {
    Webcam.snap(function(data_uri) {
        document.getElementById('results').innerHTML = '<img src="'+data_uri+'"/>';
        $('#profilePic').val(data_uri);
    } );
}

</script>  



<script type="text/javascript">
    $(document).ready(function(){
        $('.dob').on("change",function(){
            var value = $(this).val();
            var split = value.split("/");
            var formattedDOB = split[2]+"/"+split[1]+"/"+split[0];
            var dob = new Date(formattedDOB);
            var today = new Date();
            var age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
            $('#age').val(age);
        });
    });
</script>