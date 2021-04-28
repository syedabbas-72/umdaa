
<style type="text/css">
.select2{
    width:100% !important;
    height:35px !important;
}
.select2-container{
    z-index: 999999999999 !important;
}
.select2-container .select2-selection--single{
    height:32px !important;
}
    .single-mail:hover{
     background: #efefef;   
    }
   .ui-autocomplete {
   z-index: 99999999999;
   }
   .fc-ltr .fc-axis {
   text-align: right;
   color: #9aa5a6;
   }
   .fc td, .fc th {
   font-size: 15px;
   font-weight: initial;
   }
   .fc-time {
   text-transform: uppercase;
   }
   .fc-toolbar .fc-state-active, .fc-toolbar .ui-state-active {
   z-index: 4;
   background: #10367a !important;
   color: #fff;
   }
   .fc .fc-button-group > * {
   border: 1px solid #787887;
   float: left;
   margin: 0 0 0 -1px;
   background: #fff;
   }
   .fc-toolbar {
   padding: 10px 15px;
   margin-bottom: 0;
   }
   .fc-content {
   padding: 2.5px 3px;
   color: #fff;
   border:none !important;
   text-transform: uppercase;
   text-overflow: ellipsis;
   }
   .fc-toolbar h2{
   padding: 5px 12px;
   font-size: 15px;
   }
   .fc-event {
   border: none !important;
   }
   .fc-toolbar h2{
   margin-top: -12px;
   }
   .fc-head th {
   padding: 12px 4px;
   text-transform: uppercase;
   color: #787887;
   }
   .fc-time-grid .fc-event, .fc-time-grid .fc-bgevent {
   position: absolute;
   z-index: 1;
   height: 2em;
   }
   .fc-ltr .fc-time-grid .fc-event-container {
   margin: 0 !important;
   }
   .fc-time-grid-event.fc-v-event.fc-event.fc-start.fc-end {
   /* margin-right: 0 !important; */
   }
   .fc-time-grid .fc-slats td {
   height: 2.5em !important;
   border-bottom: 0;
   font-size: 11px;
   }
   .fc-time-grid-event{
      left: auto !important;
      width:auto !important;
      display:inline-flex !important;
      right:auto !important; 
      z-index: 1 !important;
      position:relative !important;
      /* margin-left:20px !important; */
   }
   .nav-link.disabled{
       background: #f2f2f2 !important;
   }
   
   .fc-prev-button,.fc-next-button,.fc-today-button,.fc-button-group
   {
       margin-top: 14px !important;
   }
   .selected
   {
       background: rgb(197, 194, 194);
   }
   
.daterangepicker{
    width: 37% !important;
}
.daterangepicker .right
{
    margin-left: 11px !important;
}
.daterangepicker .ranges
{
    width: 100% !important;
    text-align: center;
}
.form-group{
    padding: 5px 5px !important;
}
.radio{
    padding-left: 3px !important;
    padding-top: 10px !important;
}
.select2-search__field{
    width: auto !important;
}
</style>
<div class="page-bar">
   <div class="page-title-breadcrumb">
      <ol class="breadcrumb page-breadcrumb">
         <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php
            echo base_url("dashboard");
            ?>"><?=$this->session->userdata('clinic_name')?></a>&nbsp;<i class="fa fa-angle-right"></i>
         </li>
         <li><a class="parent-item active" href="<?php
            echo base_url("Calendar_view");
            ?>">Calendar</a>
         </li>
      </ol>
   </div>
 </div>

<div class="row">
    <!-- doctors list -->
    <div class="col-md-3">
        <input type="hidden" id="clinic_id" value="<?=$clinic_id?>">
        <input type="hidden" id="doctor_id" value="all">
        <input type="hidden" id="doctor_slot" value="">
        <input type="hidden" id="doctor_id_list" value="<?=$doctor_id?>">
        <div class="card-box" >
            <div class="card-head text-center">
               <header class="text-center"> <button class="btn btn-app btn-block" data-toggle="modal" data-target="#BlockModal">Block Calendar</button></header>
            </div>
            <div class="card-body">
                <div class="inbox-sidebar">
                <ul class="nav nav-pills nav-stacked docList " style="margin-top:0px !important">
                    <?php 
                    if(count($doctors_list) > 0)
                    {
                        ?>
                        <li class="w-100 px-2 selected " id="all" data-id="all" data-name="all" onclick="getIndDocDetails('all')"><a href="#" class="text-dark font-weight-bold px-0 ml-3">
                        <i class="fas fa-user-md"></i> ALL DOCTORS 
                        <span class="pull-right mr-1" id="doc_all_count">0</span></a></li>    
                        <!-- <div class="loopLists"> -->
                        <?php
                        $count = count($doctors_list);
                        foreach($doctors_list as $value)
                        {
                            $docInfo = doctorDetails($value->doctor_id);
                            ?>
                            <li class="w-100 px-2 lists " data-id="<?=$value->doctor_id?>" data-name="<?=getDoctorName($value->doctor_id)?>" onclick="getIndDocDetails('<?=$value->doctor_id?>')">
                                <a href="#" class="text-uppercase text-dark font-weight-bold px-0">
                                    <i class="fa fa-circle" style="color:<?php echo $docInfo->color_code; ?>;font-size:13px;"></i>
                                    <?=getDoctorName($value->doctor_id)?>
                                    <span class="pull-right mr-1" id="doc_<?=$value->doctor_id?>_count">0</span>
                                    <p class="small p-0 m-0 mt-2 "><?=$docInfo->department_name?>
                                    <?php
                                      $tdate = date("m/Y");
                                      $blockDatesInfo = $this->db->query("select * from calendar_blocking where dates like '%".$tdate."%' and clinic_id='".$clinic_id."' and doctor_id='".$value->doctor_id."'")->row();
                                      $bDates = $blockDatesInfo->dates;
                                      $remark = $blockDatesInfo->remark;
                                      if($bDates!="")
                                      {
                                        $split = explode("-", $bDates);
                                        $start_date = $split[0];
                                        $end_date = $split[1];
                                        $title = "Dates are blocked from<br>".$start_date." to ".$end_date."<br>".$remark;
                                        ?>
                                        <span data-toggle="tooltip" data-html="true" title="<?=$title?>" class="badge badge-primary pull-right">B</span>
                                        <?php
                                      }
                                    ?> 
                                    </p>
                                </a>
                            </li>
                            <?php
                        }
                    }
                    ?>
                    <!-- </div> -->
                </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Doctors List Ends -->
                    
    <!-- Calendar -->
    <div class="col-md-9">
        <div class="card-box">
            <div class="card-body p-2">
                <div id="calendar" class="has-toolbar"></div>
            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="BlockModal"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php
            echo form_open(site_url("calendar_view/block_calendar"), array(
                "class" => "form-horizontal",
                "id" => "app_form"
            ));
            ?>
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Block Calendar</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                  <div class="col-md-12 blockDatesBody"></div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="city-code">Doctor</label>
                            <select name="block_doctor" id="docs_name" class="form-control doctorName" style="padding-top: 5px">
                              <option disabled="" selected="">Select Doctor</option>
                                <?php
                                foreach ($doctors_list as $key => $value) {
                                    $doctor_info = $this->db->query("select * from doctors where doctor_id='" . $value->doctor_id . "'")->row();
                                    ?>
                                    <option value="<?php echo $value->doctor_id; ?>"><?php echo "DR. " . strtoupper($doctor_info->first_name . " " . $doctor_info->last_name); ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="city-code">Select Date Range</label>
                            <input type="text" class="form-control" required name="daterange" value="" />
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="city-code">Remark</label>
                            <input type="text" name="remark" class="form-control rounded-0" id="remark_ta" maxlength="30">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-center">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <input type="submit" class="btn btn-primary" id="block_submit" value="Save">
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
</div>
</div>
<!-- Block Modal -->

<!-- Block Modal Ends -->


<!-- Doctor Select Alert Modal -->

<div class="modal fade" id="DocAlert" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Select Doctor</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h4 class="text-center text-danger">Please Select Doctor to Book Appointment</h4>
        <img src="<?=base_url('assets/img/calendar-suggestion.gif')?>" class="w-100">
      </div>
    </div>
  </div>
</div>

<!-- Doctor Select Alert Modal Ends -->


<!-- Patients List Modal  -->
<div class="modal fade" id="patientsListModal"  role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" style="z-index:9999999 !important">
    <div class="modal-dialog modal-sm" role="dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Patient Details</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
            <div class="modal-body">
                <div class="row justify-content-around mb-2">
                    <div class="col-8">
                        <button class="btn btn-block btn-primary addNewPatient" data-dismiss="modal">Add Patient</button>
                    </div>
                    <div class="col-12" style="max-height:500px;overflow-y:scroll;">
                        <div class="noti-information">
                            <div class="notification-list mail-list not-list" id="patientsListBody" style="height:auto !important">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Patient List Modal Ends -->



<!-- Calendar Booking Modal -->
<div class="modal fade" id="addModal"  role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg " role="dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalTitleForCalendar"></h4>
                <!-- <a class="close" data-dismiss="modal" aria-label="Close" id="closeCalModal">
                    <i class="fas fa-times-circle"></i>
                </a> -->
            </div>
            <div class="p-2">
                <div>
                <ul class="nav nav-pills nav-fill mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link text-left active" id="pills-home-tab" data-toggle="pill" href="#pills-book" role="tab" aria-controls="pills-book" >1. Book Appointment
                            <i class="fa fa-arrow-right pull-right mt-1"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-left disabled" id="pills-profile-tab" data-toggle="pill" href="#pills-payment" role="tab" aria-controls="pills-payment">2. Payment Info
                        <i class="fa fa-arrow-right pull-right mt-1"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-left disabled" id="pills-contact-tab" data-toggle="pill" href="#pills-finish" role="tab" aria-controls="pills-finish">3. Finish
                        <i class="fa fa-arrow-right pull-right mt-1"></i>
                        </a>
                    </li>
                </ul>
                <div class="tab-content py-4 px-2" id="pills-tabContent">
                    <div class="tab-pane fade  show active" id="pills-book" role="tabpanel" aria-labelledby="pills-book-tab">
                                
                        <!-- <hr class="my-3"> -->
                            <div class="row col-12 p-0 m-0">
                                <div class="col-4">
                                    <input type="hidden" class="appointment_date">
                                    <input type="hidden" class="appointment_time_slot">
                                    <input type="hidden" class="doctor_id">
                                    <input type="hidden" id="patient_id">
                                    <input type="hidden" id="patient_type">
                                    <label class="font-weight-bold">Booking Type</label>
                                    <div class="d-flex p-0">
                                        <div class="radio radio-primary mr-2">
                                            <input id="radiobg9" name="booking_type" class="booking_type" value="walkin" type="radio" checked="checked">
                                            <label for="radiobg9">Walk-In</label>
                                        </div>
                                        <div class="radio radio-primary">
                                            <input id="radiobg10" name="booking_type" class="booking_type" value="phone" type="radio">
                                            <label for="radiobg10">Phone Call</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <label class="font-weight-bold">Priority (If Any)</label>
                                    <div class="d-flex p-0">
                                        <div class="radio radio-primary mr-2">
                                            <input id="radiobg3" name="priority" value="none" type="radio" checked="checked">
                                            <label for="radiobg3">None</label>
                                        </div>
                                        <div class="radio radio-primary mr-2">
                                            <input id="radiobg4" name="priority" value="pregnancy" type="radio">
                                            <label for="radiobg4">Pregnancy</label>
                                        </div>
                                        <div class="radio radio-primary mr-2">
                                            <input id="radiobg5" name="priority" value="elderly" type="radio">
                                            <label for="radiobg5">Elderly</label>
                                        </div>
                                        <div class="radio radio-primary mr-2">
                                            <input id="radiobg6" name="priority" value="children" type="radio">
                                            <label for="radiobg6">Children</label>
                                        </div>
                                        <div class="radio radio-primary mr-2">
                                            <input id="radiobg7" name="priority" value="sick" type="radio">
                                            <label for="radiobg7">Sick</label>
                                        </div>
                                        <div class="radio radio-primary">
                                            <input id="radiobg8" name="priority" value="other" type="radio">
                                            <label for="radiobg8">Other</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-12 p-0 m-0 mt-3">
                                <div class="col-4">                                
                                    <label class="font-weight-bold">Mobile Number</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control border-right-0" id="mobile" autocomplete="new-password" name="mobile" onkeyup="checkFollowupNumber()" onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="10" style="border-top-right-radius:0px !important;border-bottom-right-radius:0px !important">    
                                        <div class="input-group-append">
                                            <span class="input-group-text bg-transparent border-left-0">
                                                <i class="fa fa-spinner fa-spin mobile-loader d-none"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                            <!-- </div>
                            <div class="row col-12 mt-2" id="patientRegistration" > -->
                                <div class="col-md-4 ">
                                    <label for="name" class="font-weight-bold">Name</label>
                                    <input type="text" class="form-control text-capitalize" onkeypress="return alpha()" id="npname" name="npname" value="" type="text" required >
                                </div>
                                <div class="col-md-4 ">
                                    <label for="age" class="font-weight-bold">Age</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" value="" onkeypress="return numeric()" name="age" id="age" >
                                        <div class="input-group-append">
                                            <!-- <span class="input-group-text">Years</span> -->
                                            <select class="form-control" id="age_unit">
                                                <option value="Days">Days</option>
                                                <option value="Weeks">Weeks</option>
                                                <option value="Months">Months</option>
                                                <option value="Years" selected>Years</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 ">
                                    <label for="gender" class="font-weight-bold">Gender</label>
                                    <select name="gender" id="gender" class="form-control" >
                                        <option label="Select Gender" disabled >Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="location" class="font-weight-bold">Location</label>
                                    <input type="text" class="form-control" name="location" id="location" >
                                    <input type="hidden" id="payment_status"name="payment_status">
                                </div>
                                <div class="col-md-4">
                                    <label for="location" class="font-weight-bold">Language</label>
                                    <select id="preferred_language" name="preferred_language" class="form-control">
                                        <option label="Select Language">Select Language</option>
                                        <option value="English">English</option>
                                        <option value="Hindi">Hindi</option>
                                        <option value="Telugu">Telugu</option>
                                        <option value="Kannada">Kannada</option>
                                        <option value="Malayalam">Malayalam</option>
                                        <option value="Tamil">Tamil</option>
                                        <option value="Urdu">Urdu</option>
                                        <option value="Sindhi">Sindhi</option>
                                        <option value="Panjabi">Panjabi</option>
                                        <option value="Gujarati">Gujarati</option>
                                        <option value="Marathi">Marathi</option>
                                        <option value="Bengali">Bengali</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-4 mt-3">
                                    <label for="referred_by" class="font-weight-bold">Referred By</label>
                                    <select class="form-control" name="referred_by" id="ref_by_type">
                                        <option label="Select Referred By"></option>
                                        <option value="WOM">Word Of Mouth</option>
                                        <option value="Online">Online</option>
                                        <option value="Doctor">Doctor</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mt-3 d-none" id="person_div">
                                    <label class="font-weight-bold">Person Name</label>
                                    <input type="text" class="form-control" id="person_name" name="person_name">
                                </div>
                                <div class="col-md-4 mt-3 d-none" id="online_div">
                                    <label class="font-weight-bold">Online</label>
                                    <select class="form-control" id="online">
                                        <option selected disabled>Select Online Type</option>
                                        <option value="Google">Google</option>
                                        <option value="Facebook">Facebook</option>
                                        <option value="Website">Website</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mt-3 d-none" id="doc_div">
                                    <label class="font-weight-bold">Doctor</label>
                                    <select class="form-control w-100" id="refDoctor" >
                                        <option selected disabled>Select Doctor</option>
                                        <option value="others" style="background-color: #10367a !important;color: #fff !important">Add Doctor</option>
                                        <?php
                                        foreach ($doctors as $dresult) {
                                            ?>
                                            <option value="<?php echo $dresult['rfd_id']; ?>" ><?php echo $dresult['doctor_name']; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3 col-12 p-0 m-0 d-none" id="new_ref_doc_div">
                                <div class="col-md-4">
                                    <label for="ref_doctor_name" class="font-weight-bold">Doctor Name</label>
                                    <input type="text" name="ref_doctor_name" id="ref_doctor_name" class="form-control text-capitalize" value="">
                                </div>
                                <div class="col-md-4">
                                    <label for="ref_doctor_name" class="font-weight-bold">Mobile</label>
                                    <input type="text" name="ref_doctor_mobile" id="ref_doctor_mobile" class="form-control" value=""   onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="10" >
                                </div>
                                <div class="col-md-4">
                                    <label for="ref_doctor_name" class="font-weight-bold">Location</label>
                                    <input type="text" name="ref_doctor_location" id="ref_doctor_location" class="form-control text-capitalize" value="">
                                </div>
                                <div class="col-md-12 my-3 d-none" id="ref_err">
                                    <div class="alert alert-danger">
                                        <small id="refErrText"></small>
                                    </div>
                                </div>
                            </div>

                            <div class="row no-margin formDiv d-none" id="errorMsg" >
                                <div class="col-md-12 form-group" id="app_info"></div>
                                <input id="payment_status" type="hidden" >
                            </div>
                            
                            <div class="row col-12 p-0 m-0 my-4">
                                <div class="col-md-12">
                                    <div class="d-flex pull-right">
                                        <div class="checkbox checkbox-icon-primary mt-2 mr-2">
                                            <input id="sms_reminder" type="checkbox" name="sms_reminder" checked="checked">
                                            <label for="sms_reminder" class="font-weight-bold">
                                                SMS Reminder
                                            </label>
                                        </div>
                                        <div>
                                            <input type="hidden" id="pType">
                                            <button class="btn btn-primary pull-right" id="bookApp">Submit</button>
                                            <p class="btnLoader mt-1">Please Wait <i class="fa fa-spinner fa-spin"></i></p>
                                            <a href="<?=base_url('Calendar_view')?>" class="btn btn-secondary cancelBtn mr-2 pull-right">Cancel</a>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>


                    </div>
                    <div class="tab-pane fade" id="pills-payment" role="tabpanel" aria-labelledby="pills-payment-tab">
                        <div class="row col-12">
                            <div class=" col-md-4">
                                <label class="font-weight-bold"><input type="checkbox" id="register" checked="checked"> Registration</label>
                                <input type="hidden" class="form-control" id="reg_fee">
                                <input type="hidden" class="form-control" id="main_reg_fee">
                                <h4 class="m-0 p-0" id="registration_fee"></h4>
                            </div>
                            <div class=" col-md-4">
                                <label class="font-weight-bold"><input type="checkbox" id="cons" checked="checked"> Consultation</label>
                                <input type="hidden" class="form-control" id="cons_fee">
                                <input type="hidden" class="form-control" id="conFee">
                                <input type="hidden" class="form-control" id="main_cons_fee">
                                <h4 class="m-0 p-0" id="consultationn_fee"></h4>
                            </div>
                            <div class="col-md-4">
                                <label class="font-weight-bold">Procedure</label>
                                <select class="form-control" id="procedures_div">
                                    <option selected disabled>Select Procedure</option>
                                    <?php 
                                    if(count($procedures) > 0){
                                        foreach($procedures as $value){
                                            $data = $value->clinic_procedure_id."*$".$value->procedure_name."*$".$value->procedure_cost;
                                            ?>
                                            <option value="<?=$data?>"><?=$value->procedure_name?></option>
                                            <?php 
                                        }
                                    }
                                    ?>
                                </select> 
                            </div>
                            <!-- <div class="col-md-4 ">
                                <label class="font-weight-bold">Discount</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" value="" onkeypress="return numeric()" name="discount" id="discount" >
                                    <div class="input-group-append">
                                        <select class="form-control" id="disc_unit">
                                            <option value="INR">INR</option>
                                            <option value="%">%</option>
                                        </select>
                                    </div>
                                </div>
                            </div> -->
                        </div>

                        <div class="row col-12 my-3 mb-4">
                            <!-- <div class="col-md-12">
                                <label class="font-weight-bold">Procedure</label>
                                <select class="form-control" id="procedures_div">
                                    <option selected disabled>Select Procedure</option>
                                    <?php 
                                    if(count($procedures) > 0){
                                        foreach($procedures as $value){
                                            $data = $value->clinic_procedure_id."*$".$value->procedure_name."*$".$value->procedure_cost;
                                            ?>
                                            <option value="<?=$data?>"><?=$value->procedure_name?></option>
                                            <?php 
                                        }
                                    }
                                    ?>
                                </select> 
                            </div> -->
                            <!-- <div class="col-md-4">
                                <label class="font-weight-bold">Price</label>
                                <h4 class="m-0 p-0">Rs. 2500 /-</h4>
                            </div> -->
                        </div>

                        <div class="row col-md-12 my-4" id="proList">
                            <div class="col-md-12">
                                <label class="font-weight-bold">Procedures List</label>
                                <hr class="my-2">
                            </div>
                            <!-- <div class="col-md-12" id="proList"></div> -->
                        </div>

                        <div class="row col-12 mt-2">
                            <div class="col-md-4">
                                <label class="font-weight-bold">Payment Mode</label>
                                <select class="form-control" id="payment_mode">
                                    <option value="cash">Cash</option>
                                    <option value="online">Online</option>
                                    <option value="card">Card</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="font-weight-bold">Transaction ID</label>
                                <input type="text" class="form-control" disabled id="transaction_id">
                            </div>
                            <div class="col-md-4 ">
                                <label class="font-weight-bold">Discount</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" value="0" onkeypress="return numeric()" name="discount" id="discount" >
                                    <div class="input-group-append">
                                        <select class="form-control" id="disc_unit">
                                            <option value="INR">INR</option>
                                            <option value="%">%</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="row col-md-12 my-3" id="advanceRow">
                            <div class="col-md-4">&nbsp;</div>
                            <div class="col-md-4">
                                <label class="font-weight-bold">Advance Payment ( If Any )</label>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control" id="advancePay" type="text" disabled="disabled" onkeypress="return numeric()">
                            </div>
                        </div> -->

                        <div class="row col-12">
                            <div class="col-12 text-right">
                                <input type="hidden" id="appID">
                                <input type="hidden" id="total_fees">
                                <h4>Total : <span id="total_fee"></span></h4>
                                <p class="collectLoader mt-1 text-right">Please Wait <i class="fa fa-spinner fa-spin"></i></p>
                                <button class="btn btn-primary" id="billing">Collect Payment</button>
                            </div>
                        </div>

                    </div>
                    <div class="tab-pane fade" id="pills-finish" role="tabpanel" aria-labelledby="pills-finish-tab">
                        <div class="row justify-content-center">
                            <div class="col-md-8 text-center">
                                <a href="<?=base_url('Calendar_view')?>" class="btn btn-danger mr-2">Back</a>
                                <a id="invoice_link" target="blank" class="btn btn-primary mr-2">Invoice</a>
                                <a id="ocr_link" target="blank" class="btn btn-primary mr-2">Case Sheet</a>
                                <a id="vitals_link" class="btn btn-primary mr-2">Vitals</a>
                            </div>
                        </div>
                    </div>
                </div>
                        
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
</div>
<!-- Calendar Booking Modal Ends -->

<!-- Alert Modal Starts -->
<div class="modal" id="alertModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Book Appointment</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body text-center">
        <h4>You can't book appointment for this time slot.</h4>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- Alert Modal Ends -->

<link href="<?php echo base_url(); ?>assets/plugins/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css" />


<script src="<?php echo base_url(); ?>assets/plugins/moment/moment.min.js" ></script>
<script src="<?php echo base_url(); ?>assets/plugins/fullcalendar/fullcalendar.min.js" ></script>
<link href="<?php echo base_url(); ?>assets/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" rel="stylesheet"/>
<script>
var logger = function()
{
    var oldConsoleLog = null;
    var pub = {};

    pub.enableLogger =  function enableLogger() 
                        {
                            if(oldConsoleLog == null)
                                return;

                            window['console']['log'] = oldConsoleLog;
                        };

    pub.disableLogger = function disableLogger()
                        {
                            oldConsoleLog = console.log;
                            window['console']['log'] = function() {};
                        };

    return pub;
}();
// logger.disableLogger()
</script>

<script>
function billing(){
    var mcon = $('#main_cons_fee').val()
    var mreg = $('#main_reg_fee').val()
    var disc_unit = $('#disc_unit').val()
    var discount = $('#discount').val()
    var proCount = $('.proceduresList').length
    var amount = 0
    var advance = $('#advancePay').val()
    console.log(advance)
    if(advance == ""){
        advance = 0
    }
    if(discount == ""){
        discount = 0
    }

    if($('#register').prop('checked') == true){
        var reg = $('#reg_fee').val()
    }
    else{
        var reg = 0
    }

    if($('#cons').prop('checked') == true){
        var con = $('#cons_fee').val()
    }
    else{
        var con = 0
    }
    // console.log("Reg: "+reg)
    // console.log("Con: "+con)
    if(proCount > 0){
        $('.proceduresCost').each(function(index,value){
            amount = parseInt(amount)+parseInt($(this).val())
        })
        

        if(disc_unit == "INR"){
            if(discount <= amount){
                var total = parseInt(amount) - parseInt(discount)
            }
            else{
                alert("You cannot exceed more than "+amount)
                $('#discount').val('0')
                billing()
            }
        }
        else if(disc_unit == "%"){
            if(discount <= 100){
                var total = parseFloat(amount)-((parseFloat(amount)*parseFloat(discount))/100)
            }
            else{
                alert("You cannot exceed more than 100%")
                $('#discount').val('100')
                billing()
            }
        }
        else{
            var total = amount
        }
        var total = parseInt(total)+parseInt(reg)+parseInt(con)
        // console.log("Amount: "+amount)
        // console.log("Total: "+total)
    }
    else{
        amount = parseInt(con)
        if(disc_unit == "INR"){
            if(discount <= amount){
                var total = parseInt(amount) - parseInt(discount)
            }
            else{
                alert("You cannot exceed more than "+amount)
                $('#discount').val('0')
                billing()
            }
        }
        else if(disc_unit == "%"){
            if(discount <= 100){
                var total = parseFloat(amount)-((parseFloat(amount)*parseFloat(discount))/100)
            }
            else{
                alert("You cannot exceed more than 100%")
                $('#discount').val('100')
                billing()
            }
        }
        else{
            var total = amount
        }
        var total = parseInt(reg)+parseInt(total)
        // console.log("Amount= "+amount)
        // console.log("Total= "+total)
    }
    $('#total_fee').html('Rs. '+total+' /-')
    $('#total_fees').val(total)

}
</script>

<script>

    $(document).ready(function(){
        // $('.select2').select();
        var pro = [];
        $('#procedures_div').on("change", function(){
            var data = $(this).val()
            data = data.split("*$")
            
            $('.proceduresList').each(function(index, value){
                pro[index] = $(this).val()
            })
            
            if(jQuery.inArray(data[0], pro) != -1){
                alert("Procedure already added to list")
            }
            else{
                $('#proList').append('<div class="col-md-12 my-2 proclist" id="proDiv_'+data[0]+'"><input type="hidden" class="proceduresList" value="'+data[0]+'"><input type="hidden" class="proceduresNames" value="'+data[1]+'"><input type="hidden" class="proceduresCost" value="'+data[2]+'"><div class="row"><div class="col-md-8"><h5 class="m-0 p-0">'+data[1]+'</h5></div><div class="col-md-4"><h5 class="p-0 m-0 text-right">Rs. '+data[2]+'/-<span class="removePro ml-2 btn btn-xs btn-app" id="'+data[0]+'"><i class="fas fa-trash-alt"></i></span></h5></div></div></div>')
            }

            // var prolen = $('.proceduresList').length
            // // alert(prolen)

            // if(prolen == 0){
            //     $('#advancePay').attr('disabled', true)
            // }
            // else{
            //     $('#advancePay').removeAttr('disabled')
            // }
            billing()
        })


        $('#ref_doctor_mobile').on("input", function(){
            var mobile = $(this).val()
            if(mobile.length == 10){
                var mob = $('#ref_doctor_mobile').val()
                $.post("<?=base_url('Calendar_view/checkrefDoc')?>", { mobile:mobile }, function(data){
                    console.log(data)
                    data = data.split("*$");
                    if(data[0] == 0){
                        $('#ref_err').removeClass('d-none')
                        $('#refErrText').html(data[1])
                        $('#bookApp').attr('disabled',true)
                    }
                    else{
                        $('#ref_err').addClass('d-none')
                        $('#bookApp').removeAttr('disabled')
                    }
                });
            }
        })

        $('#ref_by_type').on("change", function(){
            var refType = $(this).val();
            if(refType == "WOM"){
                $('#person_div').removeClass('d-none')
                $('#online_div').addClass('d-none')
                $('#doc_div').addClass('d-none')
                $('#new_ref_doc_div').addClass('d-none')
            }
            else if(refType == "Online"){
                $('#person_div').addClass('d-none')
                $('#online_div').removeClass('d-none')
                $('#doc_div').addClass('d-none')
                $('#new_ref_doc_div').addClass('d-none')
            }
            else if(refType == "Doctor"){
                $('#person_div').addClass('d-none')
                $('#online_div').addClass('d-none')
                $('#doc_div').removeClass('d-none')
                $('#new_ref_doc_div').addClass('d-none')
            }
        })

        $('#refDoctor').on("change", function(){
            var val = $(this).val()
            if(val == "others"){
                $('#new_ref_doc_div').removeClass('d-none')
                $('#ref_doctor_mobile').val('')
                $('#ref_err').addClass('d-none')
            }
            else{
                $('#new_ref_doc_div').addClass('d-none')
                $('#bookApp').removeAttr('disabled')
            }
        })

        $('.btnLoader').hide();
        $('.collectLoader').hide();
        $('#transaction_row').hide();
        $('#refDoctor').select2();

        $("#register").on("click", function(){
            var con = $('#cons_fee').val()
            var reg = $('#reg_fee').val()
            var mreg = $('#main_reg_fee').val()
            // alert(mreg)
            // if($(this).prop('checked')){
            //     $('#reg_fee').val(mreg)
            //     $('#registration_fee').html(mreg)
            // }
            // else{
            //     $('#reg_fee').val('0')
            //     $('#registration_fee').html('0')
            // }
            billing()
        })

        $("#cons").on("click", function(){
            var con = $('#cons_fee').val()
            var reg = $('#reg_fee').val()
            var mreg = $('#main_reg_fee').val()
            var mcon = $('#main_con_fee').val()
            // alert(mreg)
            // if($(this).prop('checked')){
            //     $('#cons_fee').val(mcon)
            //     $('#consultation_fee').html(mcon)
            // }
            // else{
            //     $('#cons_fee').val('0')
            //     $('#consultation_fee').html('0')
            // }
            billing()
        })
        // $('#addModal').modal()
        $('.booking_type').on('change', function(){
            var value = $('.booking_type:checked').val();
            if(value == "walkin"){
                $('#sms_reminder').prop('checked', false);
            }
            else{
                $('#sms_reminder').prop('checked', true);
            }
        });

        $('.addNewPatient').on("click", function(){
            $('#npname').val();
            $('#patient_type').val('New');
            $('#pType').val('New');
            $('#npname,#location,#age,#gender,#age_unit,#preferred_language').removeAttr("disabled");
        })

    });

    $(document).on('click','.removePro',function(){
        var id = $(this).attr('id')
        $('#proDiv_'+id).remove()
        
        var prolen = $('.proceduresList').length

        if(prolen == 0){
            $('#advancePay').attr('disabled', true)
        }
        else{
            $('#advancePay').removeAttr('disabled')
        }
        billing()
    })

    $(document).on('click','.addExisting',function(){
        var encData = $(this).attr('data');
        // con
        var appointment_date = $('.appointment_date').val();
        var appointment_time_slot = $('.appointment_time_slot').val();
        var doctor_id = $('.doctor_id').val();
        var patient_type = $(this).attr('data-value')
        var ptype = $(this).attr('data-ptype')
        encData = JSON.parse(encData)
        console.log(encData)
        $("#patient_id").val(encData.key);
        $('#patient_type').val(patient_type);
        $('#pType').val(ptype);
        $("#npname").val(encData.pname);
        $("#age").val(encData.age);
        // $("#age_unit").val(encData.age_unit);
        $("#age_unit option [value='"+encData.age_unit+"']").attr("selected","selected")
        $("#gender option[value='"+encData.sex+"']").attr("selected","selected")
        // $('#gender').val(encData.sex)
        $("#location").val(encData.location);
        $("#preferred_language option[value='"+encData.language+"']").attr("selected","selected")
        checkappointments(encData.key,doctor_id,appointment_date);
        if(encData.pname == "" || encData.pname == null){
            $('#npname').removeAttr("disabled");
        }    
        if(encData.age == "" || encData.age == null){
            $('#age').removeAttr("disabled");
        }    
        if(encData.age_unit == "" || encData.age_unit == null){
            $('#age_unit').removeAttr("disabled");
            $("#age_unit  [option='Years']").val(encData.age_unit);
        }  
        if(encData.language == "" || encData.language == null){
            $('#preferred_language').removeAttr("disabled");
        }    
        if(encData.location == "" || encData.location == null){
            $('#location').removeAttr("disabled");
        }    
        if(encData.sex == "" || encData.sex == null){
            $('#gender').removeAttr("disabled");
        } 
        $('#patientsListModal').modal('hide')



    });
</script>
<script>
</script>
<script>
    $(document).ready(function(){
        $('#payment_mode').on("change", function(){
            var val = $(this).val()
            if(val != "cash"){
                $('#transaction_id').removeAttr('disabled')
            }
            else{
                $('#transaction_id').attr('disabled',true)
                $('#transaction_id').val('')
            }
        });
        $('#disc_unit').on("change", function(){
            $('#discount').val('0')
            billing()
        })
        $('#discount').on('input', function(){
            // var discount = 
            billing()
        });
        // $('#discount').on('input', function(){
        //     var value = $(this).val()
        //     // console.log(value)
        //     var con = $('#cons_fee').val()
        //     var reg = $('#reg_fee').val()
        //     var mcon = $('#main_cons_fee').val()
        //     var mreg = $('#main_reg_fee').val()
        //     var disc_unit = $('#disc_unit').val()
        //     if(disc_unit == "INR"){
        //         if(parseInt(value) <= con)
        //         {
        //             var sum = parseInt(con)-parseInt(value)
        //             console.log(sum)
        //             $('#conFee').val(sum)
        //             $('#total_fee').html('Rs. '+(parseInt(sum)+parseInt(reg))+' /-')
        //             $('#total_fees').val((parseInt(sum)+parseInt(reg)))
        //         }
        //         else{
        //             $('#discount').val('')
        //             $('#cons_fee').val(mcon)
        //             $('#conFee').val(mcon)
        //             $('#consultationn_fee').html("Rs. "+mcon+" /-")
        //             $('#reg_fee').val(mreg)
        //             $('#registration_fee').html(mreg)
        //             $('#total_fee').html('Rs. '+(parseInt(mcon)+parseInt(mreg))+' /-')
        //             $('#total_fees').val((parseInt(mcon)+parseInt(mreg)))
        //         }
        //     }
        //     else if(disc_unit == "%"){
        //         if(parseInt(value) <= 100)
        //         {
        //             var sum = parseInt(con)-(parseInt(value)*parseInt(con)/100)
        //             console.log(sum)
        //             $('#conFee').val(sum)
        //             $('#total_fee').html('Rs. '+(parseInt(sum)+parseInt(reg))+' /-')
        //             $('#total_fees').val((parseInt(sum)+parseInt(reg)))
        //         }
        //         else
        //         {
        //             $('#discount').val('')
        //             $('#cons_fee').val(mcon)
        //             $('#conFee').val(mcon)
        //             $('#consultationn_fee').html("Rs. "+mcon+" /-")
        //             $('#reg_fee').val(mreg)
        //             $('#registration_fee').html(mreg)
        //             $('#total_fee').html('Rs. '+(parseInt(mcon)+parseInt(mreg))+' /-')
        //             $('#total_fees').val((parseInt(mcon)+parseInt(mreg)))
        //         }
        //     }
        // });
        $('#billing').on("click",function(){
            
            var con = $('#cons_fee').val()
            var reg = $('#reg_fee').val()
            // var register = $('#register').val()
            // var cons = $('#cons').val()
            var total = $('#total_fees').val()
            var discount = $('#discount').val()
            var disc_type = $('#disc_unit').val()
            var appId = $('#appID').val()
            var transaction_id = $('#transaction_id').val()
            var mode = $('#payment_mode').val()
            var proCount = $('.proclist').length
            var pro = []
            var proCost = []
            var proNames = []

            if($('#register').prop('checked') == true){
                var register = 1
            }
            else{
                var register = 0
            }

            if($('#cons').prop('checked') == true){
                var cons = 1
            }
            else{
                var cons = 0
            }

            if(proCount > 0){
                $('.proceduresList').each(function(index,value){
                    pro[index] = $(this).val()
                })
                $('.proceduresCost').each(function(index,value){
                    proCost[index] = $(this).val()
                })
                $('.proceduresCost').each(function(index,value){
                    proNames[index] = $(this).val()
                })
            }
            $('.collectLoader').show();
            $('#billing').hide();
            $.post("<?=base_url('Calendar_view/SaveBill')?>",{
                appId : appId,
                con : con,
                reg : reg,
                register : register,
                cons : cons,
                disc_type : disc_type,
                discount : discount,
                mode: mode,
                pro : pro,
                proNames : proNames,
                proCost : proCost,
                transaction_id: transaction_id
            }, function(data){
                console.log(data)
                data = data.split("*")
                if(data[0] == 1){
                    $('#pills-profile-tab').addClass('disabled')
                    $('#pills-profile-tab').removeClass('active')
                    $('#pills-contact-tab').addClass('active')
                    $('#pills-contact-tab').removeClass('disabled')
                    $('#pills-payment').removeClass('show active')
                    $('#pills-finish').addClass('show active')
                    $('#ocr_link').attr("href","<?=base_url('Vitals/OCR/')?>"+data[2])
                    $('#vitals_link').attr("href","<?=base_url('Vitals/index/')?>"+data[3]+"/"+data[2])
                    $('#invoice_link').attr("href","<?=base_url('Billing/printBilling/')?>"+data[1])
                    $('#closeCalModal').attr("href","<?=base_url('Calendar_view')?>")

                }
                $('.collectLoader').hide();
            })
        });
        $('#bookApp').on("click", function(){
            var appointment_date = $('.appointment_date').val();
            var appointment_time_slot = $('.appointment_time_slot').val();
            var doctor_id = $('.doctor_id').val();
            var booking_type = $('input[name="booking_type"]:checked').val();
            var priority = $('input[name="priority"]:checked').val();
            var sms = $('input[name="sms_reminder"]:checked').val();
            var patient_type = $('#patient_type').val();
            var mobile = $('#mobile').val()
            var patient_id = $("#patient_id").val();
            var name = $("#npname").val();
            var age = $("#age").val();
            var age_unit = $("#age_unit").val();
            var gender = $("#gender").val()
            var location = $("#location").val();
            var language = $("#preferred_language").val()
            var payment_status = $("#payment_status").val()
            var ptype = $("#pType").val()
            var ref_by_type = $('#ref_by_type').val()
            var person_name = $('#person_name').val()
            var online = $('#online').val()
            var refDoctor = $('#refDoctor').val()
            var refdocName = $('#ref_doctor_name').val()
            var refdocMobile = $('#ref_doctor_mobile').val()
            var refdocLocation = $('#ref_doctor_location').val()

            if(mobile != "" && name != ""){
                // alert(ptype)
                $('#bookApp').hide()
                $('.cancelBtn').hide()
                $('.btnLoader').show()
                $.post("<?=base_url('Calendar_view/bookAppointment')?>",{
                    appointment_date: appointment_date,
                    appointment_time_slot: appointment_time_slot,
                    doctor_name: doctor_id,
                    patient_id: patient_id,
                    patient_type: patient_type,
                    booking_type: booking_type,
                    priority: priority,
                    mobile: mobile,
                    name: name,
                    age: age,
                    age_unit: age_unit,
                    gender: gender,
                    location: location,
                    language: language,
                    payment_status: payment_status,
                    sms:sms,
                    ptype: ptype,
                    referred_by_type: ref_by_type,
                    referred_by_p: person_name,
                    referred_by_o: online,
                    referred_by_doctor: refDoctor,
                    ref_doctor_name: refdocName,
                    ref_doctor_mobile: refdocMobile,
                    ref_doctor_location: refdocLocation
                }, function(data){
                    console.log(data)
                    // exit
                    data = data.split("*")
                    if(data[0] == 1){
                        $('#loader').modal('hide')
                        $('#pills-home-tab').addClass('disabled')
                        $('#pills-home-tab').removeClass('active')
                        $('#pills-contact-tab').addClass('active')
                        $('#pills-contact-tab').removeClass('disabled')
                        $('#pills-book').removeClass('show active')
                        $('#pills-finish').addClass('show active')
                        $('#ocr_link').attr("href","<?=base_url('Vitals/OCR/')?>"+data[1])
                        $('#vitals_link').attr("href","<?=base_url('Vitals/index/')?>"+data[2]+"/"+data[1])
                        $('#invoice_link').hide();
                        $('#closeCalModal').attr("href","<?=base_url('Calendar_view')?>")
                    }
                    // if(data[0] == 2){
                    else{
                        $('#loader').modal('hide')
                        $('#pills-home-tab').addClass('disabled')
                        $('#pills-home-tab').removeClass('active')
                        $('#pills-profile-tab').addClass('active')
                        $('#pills-profile-tab').removeClass('disabled')
                        $('#pills-book').removeClass('show active')
                        $('#pills-payment').addClass('show active')
                        // data = data.split('*');
                        $('#appID').val(data[1])
                        if(data[2] == 1){
                            $('#cons_fee').val(data[3])
                            $('#main_cons_fee').val(data[3])
                            $('#consultationn_fee').html("Rs. "+data[3]+" /-")
                            $('#reg_fee').val(data[4])
                            $('#main_reg_fee').val(data[4])
                            $('#registration_fee').html(data[4])
                            $('#total_fee').html('Rs. '+(parseInt(data[3])+parseInt(data[4]))+' /-')
                            $('#total_fees').val((parseInt(data[3])+parseInt(data[4])))
                        }   
                        else{
                            $('#cons_fee').val(data[3])
                            $('#main_cons_fee').val(data[3])
                            $('#consultationn_fee').html("Rs. "+data[3]+" /-")
                            $('#reg_fee').val('0')
                            $('#main_reg_fee').val('0')
                            $('#registration_fee').html('0')
                            $('#total_fee').html('Rs. '+data[3]+' /-')
                            $('#total_fees').val(parseInt(data[3]))
                        } 
                    }
                });
            }
            else{
                alert("Please Enter Mobile Number and Name")
            }

            
        })
    })
</script>

<script>
$(document).ready(function(){
    $("#newDiv").hide(); 
    $(".referDiv").hide(); 
    
    // $('#addModal').modal();

    $('.relationBtn').on("click", function(){
        console.log("clicked")
    })

// $('#patientsListModal').modal();
    // Disable These on startup
    $('#npname,#location,#age,#gender,#age_unit,#preferred_language').attr("disabled","disabled");
    // $('#age').attr("disabled","disabled");
    // $('#gender').attr("disabled","disabled");
    // $('#location').attr("disabled","disabled");


})

// Patient type is two types (New/Followup)         
$(document).on("click", ".ptype", function() { // bind a function to the change event
    $("#app_form").find("input[type='text']").not("input[name='app_date']").not("input[id='nppname']").not("input[id='npmobile']").val("");
    $('#procedure_div').show();
    $('.ptype').not(this).removeClass('btn-app active');
    $(this).addClass('btn-app active');
    var val = $(this).attr("id"); // Retrieve the value
    // alert(val);
    if (val == "new") {
        $("#app_info").html("");
        $("#newDiv").show(); // Show new div    
        $("#addSubmit").show();
        $("#followUpDiv").hide(); // hide follow up div        
        $("#npname").val("");
        $("#submit").hide();
    } else if (val == "followup") {
        $("#app_info").html("");
        $("#newDiv").hide(); // Hide new div    
        $("#followUpDiv").show(); // Show follow up div
        $("#submit").show();
        $("#npname").val("");
        $("#addSubmit").hide();
    }
    // sms reminder
    // Check what type of booking is it
    var bookingType = $("input[name='btype']:checked").val()

    // Uncheck if the booking type is walk in 
    // Check if the booking type is Phone 
    if (bookingType == "phone") {
        $("#npSmsCB").prop("checked", true);
        $("#fpSmsCB").prop("checked", true);
    } else {
        $("#npSmsCB").prop("checked", false);
        $("#fpSmsCB").prop("checked", false);
    }
});

function checkDocorBlockSlots(doctor_id){
    var time_slot = $('#time_slot').val();
    var app_date = $('#app_date').val();
    
    $.post("<?=base_url('Calendar_view/CheckDoctorBlockDates')?>",{doctor_id:doctor_id,time_slot:time_slot,app_date:app_date}, function(data){
        if($.trim(data) == 1){
            // alert("You can't book appointment for this slot.");
            $('#alertModal').modal()
            $('#addModal').modal('hide')
        }
    })
}

function check_ref_doctor(val) {
    if (val == "others") {
        $("#new_ref_doctor_div").show();
    } else {
        $("#ref_doctor_name").val("");
        $("#ref_doctor_mobile").val("");
        $("#ref_doctor_location").val("");
        $("#new_ref_doctor_div").hide();
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
    } else if (val == 'Doctor') {
        $("#WOM").hide(1000);
        //$("#referred_by_person").prop("required",false);
        $("#Doctor").show(1000);
        //$("#doctor_name").prop("required",true);
        $("#Online").hide(1000);
        //$("#online_sb").prop("required",false);
    } else if (val == 'Online') {
        $("#WOM").hide(1000);
        //$("#referred_by_person").prop("required",false);
        $("#Doctor").hide(1000);
        //$("#doctor_name").prop("required",false);
        $("#Online").show(1000);
        //$("#online_sb").prop("required",true);
    } else {
        $("#WOM").hide(1000);
        $("#Doctor").hide(1000);

        $("#Online").hide(1000);
    }
}
$(document).ready(function(){
    
    $("#procedures").select2({
        multiple: true,
        width: '100%',
        placeholder: "Add Procedure"
    });
    $("#procedures").val(null).trigger("change");
    $('input[type=radio][name=btype]').change(function() {
        if (this.value == 'phone') {
            $("#priority_id").hide();
            $("#npSmsCB").prop("checked", true);
            $("#fpSmsCB").prop("checked", true);
        } else if (this.value == 'walkin') {
            $("#priority_id").show();
            $("#npSmsCB").prop("checked", false);
            $("#fpSmsCB").prop("checked", false);
        }
    });
        $('#new_ref_doctor_div').hide(); 
        $("#referred_by_type").on("change",function(){
            var value = $(this).val();
            var referred_by_doctor = $('#referred_by_doctor').val();
            if(value == "Doctor" && referred_by_doctor == "others")
            {
                $('#new_ref_doctor_div').show();
            }
            else
            {
                $('#new_ref_doctor_div').hide(); 
            }
        });
    });
</script>
<script>

// New Patient On Click Event   
$(document).on("click", "#addSubmit", function() {
// alert($("#doctor_name").val())
if ($("#doctor_name").val() == "" || $("#doctor_name").val() == null) {
    alert("please select doctor");
    return false;
}

var procedures = $("#procedures").val();
var date = $('input[name="app_date"]').val();
var d_id = $('#doctor_name option:selected').val();
var time_slot = $('#time_slot option:selected').val();
var priority = $('input[name="priority"]:checked').val();
var sms = $('input[name="sms"]:checked').length;
var mobile = $('input[name="nmobile"]').val();
var pname = $('input[name="npname"]').val();

if (pname == "") {
    alert("enter patient name");
    return false;
}

if (mobile == "") {
    alert("enter mobile number");
    return false;
}

var btype = $('input[name="btype"]:checked').val();
var rbt = $('#referred_by_type').val();
var rbp = $('#referred_by_person').val();
var rbd = $('#referred_by_doctor').val();
var rbo = $('#online_sb').val();
var relation = $('#relation').val();
var relative_name = $('#relative_name').val();
var nrd_name = $('#ref_doctor_name').val();
var nrd_mobile = $('#ref_doctor_mobile').val();
var nrd_location = $('#ref_doctor_location').val();

//alert(btype);

if (sms == 1) {
    sms = "yes";
} else {
    sms = "no";
}

$('#addModal').modal('hide');
$("#loader").modal();
$.ajax({
    type: "POST",
    url: base_url + 'calendar_view/patient_add_save',
    data: {
        d_id: d_id,
        date: date,
        slot: time_slot,
        priority: priority,
        sms: sms,
        pname: pname,
        mobile: mobile,
        btype: btype,
        rbp: rbp,
        rbt: rbt,
        rbd: rbd,
        rbo: rbo,
        relation: relation,
        relative_name: relative_name,
        nrd_name: nrd_name,
        nrd_mobile: nrd_mobile,
        nrd_location: nrd_location,
        procedures: procedures
    },
    success: function(result) {

        result = $.trim(result);
        var split_result = result.split(":");

        var patient_id = split_result[0];
        var appointment_id = split_result[1];

        // alert('Patient ID: '+patient_id);
        // alert('Appointment ID: '+appointment_id);

        // Hide the loader
        $(".appointment-loader").hide();

        if (btype == "walkin") {
            location.href = base_url + 'patients/patient_update/' + patient_id + '/' + appointment_id;
        } else {
            location.href = base_url + 'calendar_view';
            // location.href= base_url+'profile/index/'+result;
        }
    }
});
});


// Follow up Patient Booking Appointment
$(document).on("click", "#submit", function() {

if ($("#doctor_name").val() == "" || $("#doctor_name").val() == null) {
    alert("please select doctor");
    return false;
}

$('#addModal').modal('hide');
// $("#loader").modal();

var date = $('input[name="app_date"]').val();
var d_id = $('#doctor_name option:selected').val();
var patient_id = $('#id').val();
var umr = $('#umr').val();
var time_slot = $('#time_slot option:selected').val();
var priority = $('input[name="priority"]:checked').val();
var sms = $('input[name="sms"]:checked').length;
var mobile = $('input[name="mobile"]').val();
var btype = $('input[name="btype"]:checked').val();
var procedures = $("#procedures").val();
var payment_status = $("#payment_status").val();

if (sms == 1) {
    sms = "yes";
} else {
    sms = "no";
}

$.ajax({
    type: "POST",
    url: base_url + 'calendar_view/book_appointment',
    data: {
        d_id: d_id,
        date: date,
        slot: time_slot,
        priority: priority,
        sms: sms,
        p_id: patient_id,
        mobile: mobile,
        umr: umr,
        btype: btype,
        procedures: procedures,
        payment_status: payment_status
    },
    success: function(result) {
        // alert(result);
        console.log(umr)
        console.log(result)
        // exit();
        result = $.trim(result);

        if (result == 'existing') {
            var c = confirm("An open appointment already exist with the doctor on the choosen date. You can reschedule the appointment. Click OK to Navigate to Patient Appointments. Thank you.");
            if(c == true)
            {
                window.open("<?=base_url('Patients/getAppointments/')?>"+patient_id, '_blank');
            }
            else
            {
                // $('#loader').modal('hide');
                // location.reload();
            }
            
        } else {
            // alert(result);
            // exit();
            result = $.trim(result);
            var split_result = result.split(":");

            var appointment_id = split_result[0];
            var payment = split_result[1];

            // alert("Appointment id: "+appointment_id);
            // alert("Payment: "+payment);

            if (result) {
                //appointment_id = result;
                if (btype == 'walkin') {
                    if (payment == 1) {
                        location.href = base_url + 'patients/confirm_payment/' + patient_id + '/' + appointment_id;
                    } else if (payment == 0) {
                        location.href = base_url + 'profile/index/' + patient_id + '/' + appointment_id;
                    }
                } else { // By a phone Redirect back to the Calendar
                    location.href = base_url + 'calendar_view';
                }
            } else {
                location.href = base_url + 'calendar_view';
            }
        }
    }
});
});


function checkappointments(p_id,d_id,date) {
    // var d_id = $('#doctor_name option:selected').val();
    // var date = $("#app_date").val();
    $.ajax({
        type: "POST",
        url: base_url + 'calendar_view/checkAppointments',
        data: { patient_id: p_id, doctor_id: d_id, date: date },
        success: function(result) {
            // console.log(result)
            var status = 0;
            var data = result.split('*UMD*');
            if($.trim(data[0]) == "STATUS_OK")
            {
                status = "1";   
                $('#errorMsg').removeClass("d-none");
            }
            else if($.trim(data[0]) == "STATUS_NOT_OK")
            {
                status = "0";   
                $('#errorMsg').removeClass("d-none");

            }
            $('#payment_status').val(status);
            // $('#errorMsg').removeClass("d-none");
            $("#app_info").html(data[1]);
        }
    });
}

function checknull(val) {
    if (val == "") {
        $("#id").val("");
        $("#pname").val("");
        $("#mobile").val("");
        $("#umr").val("");
        $("#submit").hide();
        $("#app_info").html("");
    }
}
function check_patient_mobile() {
    var chk_length = $("#nmobile").val().length;
    var mobile = $("#nmobile").val();
    var doctor_id = $('#doctor_name').val();
    // alert(doctor_id)
    // console.log("mobile"+mobile)
    if(doctor_id != null)
    {
        if (chk_length == 10) {
            $.ajax({
                type: "POST",
                url: base_url + 'calendar_view/check_patient_mobile',
                data: { mobile: mobile, doctor_id : doctor_id },
                success: function(result) {
                    // alert(result);
                    console.log(result)
                    result = $.trim(result);
                    var split_result = result.split(":");
                    if (split_result[1] == "Yes") {
                        $("#napp_info").html('<div class="row col-md-12"><div class="col-md-12"><span style="color:red;font-size:14px; font-weight:bold;padding10px;">Patient Exist with mobile Number. Family member?</span></div><div class="col-md-6"><div class="form-group has-success"><label for="city-code">Relative Name<span class="color-red">*</span></label><input class="form-control text-capitalize" id="relative_name" name="relative_name" value="" type="text" required=""></div></div><div class="col-md-6"><div class="form-group"><label for="city-code">Relation With Patient<span class="color-red">*</span></label><select  id="relation" class="form-control" name="relation" required=""><option selected="" disabled="">Select Relation</option><option value="Spouse">Spouse</option><option value="Husband">Husband</option><option value="Wife">Wife</option><option value="Father">Father</option><option value="Mother">Mother</option><option value="Step-father">Step-Father</option><option value="Step-mother">Step-Mother</option><option value="Legal_guardian">Legal Guardian</option><option value="Friend">Friend</option><option value="Son">Son</option><option value="Daughter">Daughter</option><option value="Step-son">Step-Son</option><option value="Step-daughter">Step-Daughter</option><option value="Brother">Brother</option><option value="Sister">Sister</option><option value="Grandfather">Grandfather</option><option value="Grandmother">Grandmother</option><option value="Grandson">Grandson</option><option value="Granddaughter">Granddaughter</option><option value="Uncle">Uncle</option><option value="Aunt">Aunt</option><option value="Cousin">Cousin</option><option value="Nephew">Nephew</option><option value="Niece">Niece</option><option value="Father-in-law">Father-In-Law</option><option value="Mother-in-law">Mother-In-Law</option><option value="Brother-in-law">Brother-In-Law</option><option value="Sister-in-law">Sister-In-Law</option><option value="Caretaker">Caretaker</option><option value="Neighbour">Neighbour</option><option value="Colleague">Colleague</option></select></div></div></div>');
                        $("#npname").val(split_result[0]);
                    } else {
                        $("#napp_info").html('<input class="form-control"  id="relation" name="relation" value="norelation" type="hidden">');
                        $("#npname").val("");
                        $("#addSubmit").show();
                    }
                }


            });
        } else {
            $("#napp_info").html("");
            $("#npname").val("");
        }
    }
    else{
        alert('Please Select Doctor');
    }
}

function checkFollowupNumber(){
    var doctor_id =  $('.docList li.selected').attr('data-id');
    // var doctor_id = "3";
    var mobile = $('#mobile').val()
    $('#mobile').attr("autocomplete","new-password");
    if(mobile == null || mobile == ""){
        $('#age').val('');                
        $('#npname').val('');                
        $('#location').val('');            
        $('#gender option[label="Select Gender"]').attr('selected','selected');                
        $('#preferred_language option[label="Select Language"]').attr('selected','selected');                    
        $('#npname,#location,#age,#gender,#age_unit,#preferred_language').attr("disabled","disabled");
    }
    if(doctor_id == null){
        alert("Please Select Doctor")
        $('#mobile').val('')
    }
    else{
        if(mobile.length == 10){
            $('.mobile-loader').removeClass('d-none')
            $.ajax({
                url: base_url + "calendar_view/confirm_mobile",
                data: {
                    mobile: mobile,
                    doctor_id: doctor_id
                },
                type: "POST", 
                success: function(data) {
                    // console.log(data)
                    if ($.trim(data) == "no") {
                        $('#age').val('');                
                        $('#npname').val('');                
                        $('#location').val('');            
                        $('#gender option[label="Select Gender"]').attr('selected','selected');                
                        $('#preferred_language option[label="Select Language"]').attr('selected','selected');          
                        $('#patientsListBody').html('')
                        $('#npname,#location,#age,#gender,#age_unit,#preferred_language').removeAttr("disabled");
                        $('#patient_type').val('New')
                        $('#pType').val('New')
                    } else {
                        $('#npname,#location,#age,#gender,#age_unit,#preferred_language').attr("disabled","disabled");
                        $('#patientsListBody').html(data)
                        $('#patientsListModal').modal();
                    }
                    $('.mobile-loader').addClass('d-none')
                }
            });
        }
    }
}



$(function() {
    $(".fu_patient_name").autocomplete({

        source: function(request, response) {
            $.ajax({
                url: base_url + "calendar_view/search_pname",
                data: {
                    pname: request.term,

                },
                type: "POST",
                success: function(data) {
                    if ($.trim(data) == "no") {
                        $("#app_info").html("<div class='alert alert-danger'><small><b>No results Found.</b></small></div>");
                        $("#id").val("");
                        $("#pname").val("");
                        $("#mobile").val("");
                        $("#umr").val("");
                        $("#app_info").html("");
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
            console.log(event)
            $("#id").val(ui.item.key);
            $("#pname").val(ui.item.value);
            $("#mobile").val(ui.item.mobile);
            $("#umr").val(ui.item.umr_no);
            checkappointments(ui.item.key);
        },
        create: function() {
            $(this).data('ui-autocomplete')._renderItem = function(ul, item) {
                return $('<li>')
                    .append('<a><div class="inline-block srchRes"><table cellspacing="0" cellpadding="0"><tr><td class="infoDiv"><h1>' + item.value + '<br><span><strong>PID:</strong> ' + item.umr_no + '</h1><p><strong>M: </strong>' + item.mobile + '</p></td></tr></table></div></a>')
                    .appendTo(ul);
            };
        },
        minLength: 3
    });
});


$(function() {

    // Single Select
    $("#umr").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: base_url + "calendar_view/search_umr",
                data: {
                    umr: request.term,

                },
                type: "POST",
                success: function(data) {
                    if ($.trim(data) == "no") {
                        $("#app_info").html("<div class='alert alert-danger'><small><b>No results Found.</b></small></div>");
                        $("#id").val("");
                        $("#pname").val("");
                        $("#mobile").val("");
                        $("#umr").val("");
                        $("#app_info").html("");
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
            $("#id").val(ui.item.key);
            $("#pname").val(ui.item.pname);
            $("#mobile").val(ui.item.mobile);
            $("#umr").val(ui.item.value);
            checkappointments(ui.item.key);
        },
        create: function() {
            $(this).data('ui-autocomplete')._renderItem = function(ul, item) {
                return $('<li>')
                    .append('<a><div class="inline-block srchRes"><table cellspacing="0" cellpadding="0"><tr><td class="infoDiv"><h1>' + item.pname + '<br><span><strong>PID:</strong> ' + item.value + '</h1><p><strong>M: </strong>' + item.mobile + '</p></td></tr></table></div></a>')
                    .appendTo(ul);
            };
        },
        minLength: 3
    });
});

</script>
<script>
$(document).ready(function(){
    $('.docList li').on("click", function(){
        $('.docList li').removeClass('selected');
        $(this).addClass('selected');
    });

    



    $('.doctorName').on("change",function(){
        var base_url = '<?=base_url()?>';
        var id = $(this).val();
        $.ajax({
            url : base_url+"calendar_view/checkblockdates",
            type : "POST",
            data : {
                doctor_id : id
            },
            success : function(data){
                $('.blockDatesBody').html(data);
            }
        });
    });

});
</script>
<script>
function delBlockDate(id)  {
    $.ajax({
        url : base_url+"calendar_view/cal_date_del",
        type : "POST",
        data : {
            block_id : id
        },
        success : function(data){
            if(data==1)
            {
                $('#b_'+id).hide();
                location="<?=base_url('Calendar_view')?>";
            }
            else
            {
                alert("Error Occured. Please try After Some Time");
            }
        }
    });
}
</script>
<script>
$(function() {
    $('input[name="daterange"]').daterangepicker({
        timePicker : true,
        startDate: moment(),
        locale: {
            format: 'DD/MM/YYYY hh:mm A'
        }
    });
});
</script>
<script>
$(document).on('click','.fc-slats tr',function(){
})
</script>
<script>
$(document).ready(function(){
    var anno = new Anno({
                        target: '.docList',
                        content: 'Please Select any one of these doctors to book appointment'
                    })
    $('#app_date').daterangepicker({
        singleDatePicker: true,
        startDate: moment(),
        locale: {
            format: 'DD/MM/YYYY'
        }
    });
   

    // $('#loader').modal();
    var scrollTime = moment().format("HH:mm:ss");
    $('#calendar').fullCalendar({
        loading: function(isLoading, view){
            console.log(view)
        },
        eventLimit: true,
        allDaySlot: false,
        defaultView: 'agendaDay',
        slotDuration: '00:05:00',
        nowIndicator: true,
        lazyFetching: true,
        scrollTime: scrollTime,
        slotLabelInterval: 5,
        slotEventOverlap: false,
        agendaEventHeight: 50,
        //hiddenDays: [ 0 ],
        minTime: '00:00:00',
        maxTime: '23:59:00',
        axisFormat: 'h:mm a',
        timeFormat: 'h:mm a',
        slotLabelFormat: 'h:mm a',
        slotMinutes: 5,
        displayEventTime: false,
        defaultDate: new Date(),
        views: {
            month: {
                eventLimit: 2
            },
            agendaWeek: {
                columnFormat: 'ddd D',
                eventLimit: 2
            }
        },
        header: {
            left: '',
            center: 'prev title next today',
            right: 'agendaDay,agendaWeek,month'
        },
        viewRender: function(view, element){
            var docId = $('.docList li.selected').attr('data-id');
            var b = $('#calendar').fullCalendar('getDate')
            var month = b.month() + 1 
            if(view.name == "agendaDay")
            {
                var date = b.format('YYYY-MM-DD')
                getEvents(view.name,date,docId);
            }
            else if(view.name == "agendaWeek")
            {
                var beginOfWeek = moment(b).startOf('week');
                var endOfWeek = moment(b).endOf('week');
                var start = beginOfWeek.format('YYYY-MM-DD')
                var end = endOfWeek.format('YYYY-MM-DD')
                getEvents(view.name,'',docId,start,end);
            }
            else if(view.name == "month")
            {
                getEvents(view.name,'',docId,start,end,month);
            }
        },
        dayClick: function(view){
            // console.log(view)
            var  hrs = new Date().getHours()
            if(hrs.toString().length == 1)
                var z = '0'+hrs
            else
                var z = hrs
            var now = z+":"+('0'+new Date().getMinutes()).slice(-2)+":00"
            // alert(moment(calEvent.start))
            // console.log("s"+now)
            $('.fc-slats tr').on("click", function(){
                var selected_time = $(this).attr('data-time')
                // console.log(selected_time)
                var splitStr = selected_time.split(':')
                var time = splitStr[0]+":"+splitStr[1]
                $('#time_slot option[value="'+time+'"]').prop('selected',true)

                var selectedDoc = $('.docList li.selected').attr('data-id');
                var selectedDocName = $('.docList li.selected').attr('data-name');
                
                $('#doctor_name option[value="'+selectedDoc+'"]').attr("selected","selected")

                var b = $('#calendar').fullCalendar('getDate')
                $('#app_date').val(b.format('DD/MM/YYYY'))
                $('.appointment_date').val(b.format('YYYY-MM-DD'))
                $('.appointment_time_slot').val(time)
                $('.doctor_id').val(selectedDoc)

                if(selectedDoc != "all"){
                    // console.log(selectedDoc)
                    checkDocorBlockSlots(selectedDoc);
                    var titleStr = "Appointment With <b>"+selectedDocName+'</b> on '+b.format('DD/MM/YYYY')+' '+time; 
                    var time1 = $(this).attr('data-time')
                    console.log(time1)
                    if(time <= now){
                        $('#alertModal').modal()
                    }
                    else{
                        $('#addModal').modal()
                        $('#modalTitleForCalendar').html(titleStr);
                    }
                }
                else{
                    anno.show()
                }

               
                console.log(time)
                
            })
            // alert(moment(calEvent.start).format('h:'));
            // $('#addModal').modal()
            // alert(view)
            // view = new Date(view)
            // var selected_time = view
            // $('#time_slot option[value="'+selected_time+'"]').prop('selected',true)
            // check_dynamic_slots(b.format('YYYY-MM-DD'))
        },
        eventRender: function(eventObj, $el) {
            var b = $('#calendar').fullCalendar('getDate');
            var time1 = b.format('YYYY-MM-DD');

            $el.addClass(eventObj.slot1);

            if (eventObj.booking_type == 'walkin') {
                $el.find('.fc-title').prepend('<span class="glyphicon"><i class="fas fa-walking"></i></span> ');
            } else {
                $el.find('.fc-title').prepend('<span class="glyphicon"><i class="fa fa-phone"></i></span> ');
            }
        },
        eventClick: function(calEvent, jsEvent, view) {
            // $.post()
            // console.log(calEvent)
            // if(calEvent.astatus == "booked" && calEvent.vitals == 0){
            //     location.href =base_url + 'Vitals/add_vitals/' + calEvent.app_patient_id + '/' + calEvent.id;
            // }
            // else if(calEvent.vitals == 0){
                location.href = base_url + 'profile/index/' + calEvent.app_patient_id + '/' + calEvent.id;
            // }
            
        }
    });
});
</script>
<script>
    $(document).ready(function(){
        // .fc-prev-button,.fc-next-button,.fc-today-button,.fc-agendaDay-button,.fc-agendaWeek-button,.fc-month-button
        $('.fc-button').on("click", function(){
            var docId = $('.docList li.selected').attr('data-id');
            getIndDocDetails(docId);
        })  
    })
</script>

<script>

    function tabs(tab){
        if(tab == 'new'){
            alert('New Tab');
        }else if(tab == "followup"){
            alert("Followup");
        }
    }
    function getEvents(view,date,docId,start,end,month){
        $.ajax({
            method: "POST",
            url: "<?=base_url('Calendar_view/getEvents')?>",
            data: {view:view,date:date,docId:docId,start:start,end:end,month:month},
            success: function(data){
                // console.log(data)
                
                var obj = data.split("*UMD*");
                console.log(obj)
                if(obj.length > 1)
                {
                    // console.log(obj)
                    if(obj[0]!="")
                    {
                        var object = jQuery.parseJSON($.trim(obj[0]));
                        $('#calendar').fullCalendar('removeEvents');
                        $('#calendar').fullCalendar('addEventSource', object);
                        $('#calendar').fullCalendar('rerenderEvents');
                    }
                    if(obj[1]!="")
                    {
                        var countObj = jQuery.parseJSON($.trim(obj[1]));
                        // console.log(countObj)
                        for(var i = 0;i < countObj.length;i++)
                        {
                            $('#doc_'+countObj[i]['doctor_id']+'_count').html(countObj[i]['count']);
                        }
                    }
                    $('#doc_all_count').html(obj[2]);
                }
                else
                {
                    // $('#calendar').fullCalendar('removeEvents');
                        var object = jQuery.parseJSON($.trim(obj[0]));
                        $('#calendar').fullCalendar('removeEvents',);
                        $('#calendar').fullCalendar('addEventSource', object);
                        $('#calendar').fullCalendar('rerenderEvents');
                }
            }
        })
    }

    function getIndDocDetails(doctor_id)
    {
        var view = $('#calendar').fullCalendar('getView');

        var docId = doctor_id;
        var b = $('#calendar').fullCalendar('getDate')
        var month = b.month() + 1 
        if(view.name == "agendaDay")
        {
            var date = b.format('YYYY-MM-DD')
            getEvents(view.name,date,docId);
        }
        else if(view.name == "agendaWeek")
        {
            var beginOfWeek = moment(b).startOf('week');
            var endOfWeek = moment(b).endOf('week');
            var start = beginOfWeek.format('YYYY-MM-DD')
            var end = endOfWeek.format('YYYY-MM-DD')
            getEvents(view.name,'',docId,start,end);
        }
        else if(view.name == "month")
        {
            getEvents(view.name,'',docId,start,end,month);
        }
    }
</script>