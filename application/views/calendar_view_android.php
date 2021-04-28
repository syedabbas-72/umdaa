<link href="<?php echo base_url(); ?>assets/plugins/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css" />
<style type="text/css">
   .select2-container {
   border: 1px solid #dde6e9;
   outline: 0;
   }
   .error.help-block{
      color: #dd4b39;
      font-weight: bold;
      margin-top: 5px;
      margin-bottom: 5px;
   }
   .ui-autocomplete {
   z-index: 99999999999;
   }
   .select2-search__field {
   width: 100% !important;
   }
   .select2-container {
   width: 100% !important;
   }
   .text-capitalize{
      text-transform: capitalize;
   }
</style>
<style type="text/css">
   .appointment-loader {
   position: absolute;
   height: 100%;
   width: 100%;
   background: rgba(255,255,255,.7);
   z-index: 7;
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
   hr {
    margin: 10px 0 !important;
}
   .fc-content {
   padding: 2.5px 3px;
   color: #fff;
   border:none !important;
   text-transform: uppercase;
   text-overflow: ellipsis;
   }
   body.modal-open{
   overflow: hidden !important;
   position: fixed;
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
   .list-group li {
   text-align: center;
   }
   .list-group li a{
   margin:0 5px;
   }
   .popover {
   max-width: 450px !important;
   width: 100%;
   }
   #app_info {
   padding: 0 15px;
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
   margin-right: 0 !important;
   }
   .doctor-name {
   font-size: 13px;
   text-transform: uppercase;
   }
   #external-events ul{
   padding:0px;
   margin:0px;
   }
   #external-events ul li{
   list-style: none;
   border-bottom: 1px dotted #ccc;
   padding:10px 15px;
   text-align: left;
   cursor: pointer;
   }
   #external-events ul li.selected{
   background: rgb(197, 194, 194);
   font-weight:600;
   }
   .fc-time-grid .fc-slats td {
   height: 2.5em !important;
   border-bottom: 0;
   font-size: 11px;
   }
   .fc-time-grid-event{
      right: auto !important;
      left: 0px !important;
   }
</style>
<style type="text/css">
 .bg-light-gradient {
     background: #f8f9fa;
     background: -o-linear-gradient(white, #f8f9fa);
     color: #1F2D3D;
 }
 .booking-time {
     font-weight: 500;
     font-size: 1rem;
 }
 .booking-time .badge {
     font-size: 90%;
 }
 .ml-0, .mx-0 {
     margin-left: 0 !important;
 }
 .mr-0, .mx-0 {
     margin-right: 0 !important;
 }
 .rounded {
     border-radius: 0.25rem !important;
 }
 .mb-3, .small-box, .card, .info-box, .callout, .my-3 {
     margin-bottom: 1rem !important;
 }
 table.dataTable {
     clear: both;
     margin-top: 6px !important;
     margin-bottom: 6px !important;
     max-width: none !important;
     border-collapse: separate !important;
     border-spacing: 0;
 }
 .img-size-100 {
     width: 100px;
 }
 button.view-booking-detail {
     box-shadow: none !important;
     overflow: visible !important;
 }
 .bg-secondary, .bg-secondary a {
     color: #ffffff !important;
 }
 .btn-outline-primary {
     color: #007bff;
     background-color: transparent;
     background-image: none;
     border-color: #007bff;
 }
 .modal {
    -webkit-transition: none;
    -moz-transition: none;
    -ms-transition: none;
    -o-transition: none;
    transition: none;
}
.btn-outline-dark {
 color: #343a40;
 background-color: transparent;
 background-image: none;
 border-color: #343a40;
}
.btn-outline-danger {
 color: #dc3545 !important;
 background-color: transparent;
 background-image: none;
 border-color: #dc3545;
}
.btn-outline-warning {
 color: #f0ad4e !important;
 background-color: transparent;
 background-image: none;
 border-color: #f0ad4e;
}
.btn-outline-warning:hover {
  color: #000000 !important;
}
.btn-outline-danger:hover {
 color: #ffffff !important;
}
.btn-outline-info {
 color: #17a2b8;
 background-color: transparent;
 background-image: none;
 border-color: #17a2b8;
}
.border-dark {
 border-color: #ffffff !important;
}
.badge{
 background: none !important;
}
.bg-light, .bg-light a {
 color: #1F2D3D !important;
}
.view-booking-detail {
 font-size: 1.8rem;
}
#normal-countdown .time-sec {
 position: relative;
 display: inline-block;
 margin: 12px;
 height: 70px;
 width: 70px;
 border-radius: 100px;
 box-shadow: 0px 0px 0px 5px rgba(255,255,255,.5);
 background: #fff;
 color: #333;
}
#normal-countdown .time-sec .main-time {
 font-weight: 500;
 line-height: 50px;
 /* bottom: 6px; */
 font-size: 27px;
 color: #F84982;
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
</style>

<div class="page-bar">
    <div class="page-title-breadcrumb">
        <div class=" pull-left">
            <div class="page-title"><?php
            echo $_SESSION['clinic_name'];
            ?></div>
        </div>
        <ol class="breadcrumb page-breadcrumb pull-right">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php
            echo base_url("dashboard");
            ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Appointments</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-md-3 col-sm-12">
        <input type="hidden" id="clinic_id" value="<?php
        echo $clinic_id;
        ?>">
        <input type="hidden" id="doctor_id" value="all">
        <input type="hidden" id="doctor_slot" value="">
        <input type="hidden" id="doctor_id_list" value="<?php
        echo $doctor_id;
        ?>">
        <div class="card-box">
            <div class="card-head text-center">
                <header style="font-size:16px" onclick="blockCalendar()"><button class="btn btn-app">BLOCK CALENDAR</button></header>
            </div>
            <div class="card-body ">
                <div id="external-events">
                    <input type="hidden" value="all" id="dispval" />  
                    <ul class="doctors-list">
                        <li id="all" class="all-doctors selected" onclick="getdetails('<?php
                            echo $clinic_id;
                            ?>','all')">
                            <span  class="doctor-category-list-item">
                                <span class="doctor-name">&nbsp;All doctors</span>
                                <span id="total_apnts" class="num-appoint pull-right">
                                </span>
                            </span>
                        </li>
                        <?php
                        foreach ($doctors_list as $key => $values) {
                            $get_doctor = $this->db->query("select * from doctors d inner join department dep on(d.department_id = dep.department_id) where d.doctor_id='" . $values->doctor_id . "'")->row();
                            ?>
                            <li class="all-doctors" id="<?php
                            echo $values->doctor_id;
                            ?>_cnt" onclick="getdetails('<?php
                                echo $values->clinic_id;
                                ?>','<?php
                                echo $values->doctor_id;
                                ?>')">
                                <span  class="doctor-category-list-item">
                                  <div class="row">
                                    <div class="col-md-1" style="padding: 0px">
                                      <i class="fa fa-circle" style="color:<?php echo $get_doctor->color_code; ?>;font-size:13px;"></i>
                                  </div>
                                  <div class="col-md-9" style="padding: 0px">
                                      <span class="doctor-name">&nbsp;<?php echo "Dr. " . strtoupper($get_doctor->first_name . "  " . $get_doctor->last_name); ?></span>
                                  </div>
                                  <div class="col-md-1" style="padding-right:0px;padding-left: 5px">
                                      <?php
                                      $tdate = date("m/Y");
                                      $blockDatesInfo = $this->db->query("select * from calendar_blocking where dates like '%".$tdate."%' and doctor_id='".$get_doctor->doctor_id."'")->row();
                                      $bDates = $blockDatesInfo->dates;
                                      $remark = $blockDatesInfo->remark;
                                      if($bDates!="")
                                      {
                                        $split = explode("-",$bDates);
                                        $start_date = $split[0];
                                        $end_date = $split[1];
                                        $title = "Dates are blocked from<br>".$start_date." to ".$end_date."<br>".$remark;
                                        ?>
                                        <span data-toggle="tooltip" data-html="true" title="<?=$title?>" class="btn btn-xs btn-app" style="padding:0px 8px;">B</span>
                                        <?php
                                    }
                                    ?>                                      
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-10" style="padding: 0px">
                                  <span style="margin-left: 25px"><small><?php  echo $get_doctor->department_name; ?></small></span>
                              </div>
                              <div class="col-md-2" style="padding-right: 15px">
                                  <span class="num-appoint pull-right"></span>
                              </div>
                          </div>



                      </span>
                  </li>
                  <?php
              }
              ?>
          </ul>
      </div>
  </div>
</div>
</div>
<div class="col-md-9 col-sm-12">
    <div class="card-box">
        <div class="card-body ">
            <div class="panel-body">
                <div id="calendar" class="has-toolbar"> </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="modal" id="addModal"  role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg bookAppointmentModal" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Book Appointments</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>
            <div class="modal-body" style="padding-top: 0px !important">
                <?php
                echo form_open(site_url("calendar_view/book_appointment"), array(
                    "class" => "form-horizontal",
                    "id" => "app_form"
                ));
                ?>
                <div class="row formDiv">
                    <div class="col-md-4 form-group">
                        <label for="app_date">Scheduled On </label>
                        <input type="text" class="form-control" id="app_date" name="app_date" data-inputmask="'alias': 'date'" value="" />                           
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="city-code">Time Slot</label>
                        <select name="time_slot" id="time_slot" class="form-control" id="time_slot" onchange="checkslots();"></select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="city-code">Doctor <span id="docNameErr">* Choose Doctor</span></label>
                        <select name="doctor_name" id="doctor_name" class="form-control" onchange="get_doctor(this.value)">
                            <?php
                            foreach ($doctors_list as $key => $value) {
                                $doctor_info = $this->db->query("select * from doctors where doctor_id='" . $value->doctor_id . "'")->row();
                                ?>
                                <option value="<?php
                                echo $value->doctor_id;
                                ?>"><?php
                                echo "DR. " . strtoupper($doctor_info->first_name . " " . $doctor_info->last_name);
                                ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="subHeader">
                            Booking Type
                        </div>                        
                    </div>
                </div>
                <div class="row bookingType">
                    <div class="col-md-2"></div>
                    <div class="col-md-4 text-center radio radio-success form-check">
                        <input type="radio" class="form-check-input" name="btype" id="walkInRB" value="walkin" checked>
                        <label class="form-check-label" for="walkInRB"> Walk-In </label>
                    </div>
                    <div class="col-md-4 text-center radio radio-success form-check">
                        <input type="radio" name="btype" id="phoneCallRB" value="phone">
                        <label class="form-check-label" for="phoneCallRB"> Phone Call </label>
                    </div>
                    <div class="col-md-2"></div>
                </div>
                <div class="row priority" id="priority_id">
                    <div class="col-md-12">
                        <div class="subHeader">
                            Choose Priority <span>(if any)</span>
                        </div>                        
                    </div>
                    <div class="col-md-1">
                        <!-- empty -->
                    </div>
                    <div class="col-md-10 text-center">
                        <div class="row">
                            <div class="radio radio-success form-check col-md-2 text-center">
                                <input type="radio" name="priority" class="form-check-input" id="radio12" value="pregnancy">
                                <label for="radio12"> Pregnancy </label>
                            </div>
                            <div class="radio radio-success form-check col-md-2 text-center">
                                <input type="radio" name="priority" class="form-check-input" id="radio13" value="elderly">
                                <label for="radio13"> Elderly </label>
                            </div>
                            <div class="radio radio-success form-check col-md-2 text-center">
                                <input type="radio" name="priority" class="form-check-input" id="radio14" value="children">
                                <label for="radio14"> Children </label>
                            </div>
                            <div class="radio radio-success form-check col-md-2 text-center">
                                <input type="radio" name="priority" class="form-check-input" id="radio15" value="sick">
                                <label for="radio15"> Sick </label>
                            </div>
                            <div class="radio radio-success form-check col-md-2 text-center">
                                <input type="radio" name="priority" class="form-check-input" id="radio16" value="other">
                                <label for="radio16"> Other </label>
                            </div>
                            <div class="radio radio-success form-check col-md-2 text-center">
                                <input type="radio" name="priority" class="form-check-input" id="radio17" value="none"  checked="">
                                <label for="radio17"> None </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <!-- empty -->
                    </div>
                </div>
                <div class="row">
                    <div class="btn-group col-md-12 text-center appType">
                        <div class="pull-left col-md-6" style="padding:0px;">
                            <button id="new" type="button" class="btn btn-block btn-lg ptype newBtn">New</button>
                        </div>
                        <div class="pull-right col-md-6" style="padding:0px;">
                            <button id="followup" type="button" class="btn ptype btn-block btn-lg fuBtn">Followup</button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12" id="empty-message" style="display: none"><b>No results found.</b></div>
                </div>

                <div id="newDiv" class="newPatientForm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="city-code">Mobile<span class="color-red">*</span></label>
                                <input class="form-control" onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="10" id="nmobile" name="nmobile" value="" type="text" required onkeyup="check_patient_mobile()">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="city-code">Name<span class="color-red">*</span></label>
                                <input class="form-control text-capitalize" onkeypress="return alpha()" id="npname" name="npname" value="" type="text" required >
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox-primary margin-r-5" style="margin-top:35px">
                                <input id="npSmsCB" type="checkbox" name="sms" value="yes">
                                <label for="npSmsCB"> SMS Reminder </label>
                            </div>
                        </div>
                        <div class="row no-margin" id="napp_info"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="referred_by_type" class="col-form-label">Referred By</label>
                                <select name="referred_by_type" placeholder="" class="form-control" onchange="referStatus(this.value)" id="referred_by_type">
                                    <option value="">-- Select --</option>
                                    <option value="WOM">Word of Mouth</option>
                                    <option value="Doctor">By a Doctor</option>
                                    <option value="Online">Online</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- ask for person name -->
                            <div id="WOM" style="display: none;" class="form-group">
                                <label for="referred_by_person" class="col-form-label">Person Name</label>
                                <input id="referred_by_person" name="referred_by_p" type="text" placeholder="Person Name" class="form-control text-capitalize" > 
                            </div>
                            <!-- ask for doctor name -->
                            <div id="Doctor" style="display: none;" class="form-group">
                                <label for="doctor_name" class="col-form-label">Doctor Name</label>
                                <select name="referred_by_doctor" id="referred_by_doctor" class="form-control" onchange="check_ref_doctor(this.value)">
                                    <option value="">--Select--</option>
                                    <?php
                                    foreach ($doctors as $dresult) {
                                        ?>
                                        <option value="<?php echo $dresult['rfd_id']; ?>" ><?php echo $dresult['doctor_name']; ?></option>
                                        <?php
                                    }
                                    ?>
                                    <option value="others">Add Doctor</option>
                                </select>
                            </div>
                            <!-- ask for Online property -->
                            <div id="Online" style="display: none;" class="form-group">
                                <label for="online_sb" class="col-form-label">Online</label>
                                <select id="online_sb" name="referred_by_o" class="form-control">
                                    <option value="">-- Select --</option>
                                    <option value='Google'>Google</option>
                                    <option value='Facebook'>Facebook</option>
                                    <option value='Website'>Website</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="new_ref_doctor_div">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ref_doctor_name" class="col-form-label">Doctor Name</label>
                                <input type="text" name="ref_doctor_name" id="ref_doctor_name" class="form-control text-capitalize" value="">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ref_doctor_name" class="col-form-label">Mobile</label>
                                <input type="text" name="ref_doctor_mobile" id="ref_doctor_mobile" class="form-control" value=""  onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="10" >
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ref_doctor_name" class="col-form-label">Location</label>
                                <input type="text" name="ref_doctor_location" id="ref_doctor_location" class="form-control text-capitalize" value="">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FOLLOW UP Tab -->
                <div id="followUpDiv" style="display: none;">
                    <div class="row">
                        <input type="hidden" id="id" name="patient_id" value="">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="city-code">Mobile<span class="color-red">*</span></label>
                                <input class="form-control" maxlength="10"   onkeyup="checknull(this.value)" id="mobile" name="mobile" value="" type="text"  onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="10" >
                            </div>
                        </div>
                        <div class="form-group no-margin-bottom col-md-3">
                            <label for="patientId">
                                Patient ID
                            </label>
                            <div>
                                <input type="text" id="umr" value="" name="umr_no" class="form-control" placeholder="Patient ID">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="city-code">Patient Name<span class="color-red">*</span></label>
                                <input class="form-control text-capitalize fu_patient_name" id="pname" name="pname"  value="" type="text">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="checkbox-primary margin-r-5" style="margin-top:35px">
                                <input id="fpSmsCB" type="checkbox" name="sms" value="yes">
                                <label for="fpSmsCB"> SMS Reminder </label>
                            </div>
                        </div>
                    </div>
                    <div class="row no-margin" id="app_info"></div>
                </div>
                <div class="row" id="procedure_div" style="display: none">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="category">Planned Procedures</label>
                            <select name="procedures" id="procedures" class="form-control">
                                <?php
                                foreach ($procedures as $value) {
                                    ?>
                                    <option value="<?php
                                    echo $value->clinic_procedure_id;
                                    ?>"><?php
                                    echo $value->procedure_name;
                                    ?></option>
                                    <?php
                                }

                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a style="display:none;cursor: pointer;" class="btn btn-app" id="submit">Book</a>
                <a class="btn btn-app" style="display:none;cursor:pointer"  id="addSubmit">Book</a>
                <?php
                echo form_close();
                ?>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
</div></section>
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
                            <select name="block_doctor" id="doctor_name" class="form-control doctorName" style="padding-top: 5px">
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
                <center>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <input type="submit" class="btn btn-primary" id="block_submit" value="Save">
                </center>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
</div>
</div>
<div class="modal fade" id="eventInfo"  role="dialog" aria-labelledby="myModalLabel">
 <div class="modal-dialog" role="document">
  <div class="modal-content">
   <div class="modal-header" style="text-align: center">
    <h5 class="modal-title" id="eventDoctor"></h5>
    <button type="button" class="close btn-danger" style="padding: 5px;font-size: 2rem;width:35px;height:35px;border-radius:50%;margin: 0" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body" id="EventDescription">
</div>
</div>
</div>
</div>

<script src="<?php echo base_url(); ?>assets/plugins/moment/moment.min.js" ></script>
<script src="<?php echo base_url(); ?>assets/plugins/fullcalendar/fullcalendar.min.js" ></script>
<link href="<?php echo base_url(); ?>assets/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" rel="stylesheet"/>

<script type="text/javascript">
    var base_url = '<?php echo base_url(); ?>';
    var todayDate = '<?php echo date("Y-m-d"); ?>';
    var clinic_id = '<?php echo $clinic_id; ?>';
    var currentTime = '<?php echo date('H:i:s'); ?>';
    var cTime = '<?php echo date('H:i'); ?>';
    $('.date').datepicker({
        format: 'yyyy-mm-dd',
        multidate: true
    });
</script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/calendar_view.js"></script>

<script type="text/javascript">
    $( document ).ready( function () {
        $( "#app_form" ).validate( {
            rules: {
                npname: "required",
                nmobile: {
                    required: true,
                    minlength: 10,
                    maxlength:10,                   
                }
            },
            messages: {
                npname: "Enter first name",             
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

    $(function() {
        $('input[name="daterange"]').daterangepicker({
            timePicker : true,
            startDate: moment(),
            locale: {
                format: 'DD/MM/YYYY hh:mm A'
            }
        });
    });

    function tabs(tab){
        if(tab == 'new'){
            alert('New Tab');
        }else if(tab == "followup"){
            alert("Followup");
        }
    }

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
                }
                else
                {
                    alert("Error Occured. Please try After Some Time");
                }
            }
        });
    }

    $(document).ready(function(){
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
</script>