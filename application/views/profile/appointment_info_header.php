<?php
$CI =& get_instance();
$clinic_id = $this->session->userdata('clinic_id');        
$cond = '';
if ($clinic_id != 0){
    $cond = "where clinic_id=" . $clinic_id;
}
$doctors_list = $CI->db->query("select * from clinic_doctor " . $cond . " group by doctor_id")->result();
$procedures = $this->db->query("select * from clinic_procedures " . $cond)->result();
//$patient_data = $this->db->query("select p.*, d.district_name, s.state_name from patients p left join districts d on p.district_id = d.district_id left join states s on p.state_id = s.state_id where p.patient_id=".$patient_id."")->row();
//echo $this->db->last_query();
$c_appointment = $this->db->query("select * from appointments where patient_id='".$patient_id."' order by modified_date_time DESC")->row();

// echo '<pre>';
// print_r($appointmentInfo);
// echo '</pre>';
// exit();
?>

<style type="text/css">
    .error.help-block{
        color: #dd4b39;
        font-weight: bold;
        margin-top: 5px;
        margin-bottom: 5px;
    }
    .ui-autocomplete {
        z-index: 99999999999;
    }
    .daterangepicker {
        z-index: 10055 !important;
    }
    .modal {
        background-color: #333!important;
    }
    .text-cpaitalize {
        text-transform: capitalize;
    }

</style>

<?php 

// Should render appointments info and a patient info based on the Params.
// If appointment Id is null then render all the appointments with status 'Booked' and there payment status.

// Get clinic ID
$clinic_id = $this->session->userdata('clinic_id');

// Get accessing user's profile ID 
$profile_id = $this->session->userdata('profile_id');

// get appointments whose status are not in 'closed, dropped, absent';
$status = array('closed', 'drop', 'absent', 'rescheduled');

?>
<div class="container-fluid">
    <div class="row patientInfo">
        <div class="col-md-4" style="padding-right: 0px">
            <table cellspacing="0" cellpadding="0" class="profileMinInfo">
                <tr>
                    <td class="profileIcon">
                        <img src="<?php echo ($appointmentInfo[0]->photo=='') ? base_url('assets/img/profilePic.jpg') : base_url('uploads/patients/'.$appointmentInfo[0]->photo) ?>">
                    </td>
                    <td>
                        <?php
                        echo $appointmentInfo[0]->title != '' ? ucwords($appointmentInfo[0]->title).". " : '';
                        echo ucwords($appointmentInfo[0]->first_name.' '.$appointmentInfo[0]->last_name);
                        ?>
                        <br>
                        <span class="pid">
                            <b>Pid: </b><?=ucwords($appointmentInfo[0]->umr_no)?>
                            &nbsp;&nbsp;&nbsp;
                            <b>Mob: </b><?=DataCrypt($appointmentInfo[0]->mobile, 'decrypt')?>
                        </span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-md-6 appointmentStatus text-center" style="padding-left: 0px">
            <?php if($appointment_id) { ?>
                <p>With <span>Dr. <?=ucwords($appointmentInfo[0]->doctor_first_name.' '.$appointmentInfo[0]->doctor_last_name); ?></span> on <span><?=date('d M Y', strtotime($appointmentInfo[0]->appointment_date)); ?> @ <?=strtoupper(date('H:i a', strtotime($appointmentInfo[0]->appointment_time_slot))); ?></span></p>
                <h4 class='<?=$appointmentInfo[0]->appointment_status; ?>'>
                    <?php echo strtoupper(str_replace('_', ' ', $appointmentInfo[0]->appointment_status)); ?>
                </h4>
            <?php } ?>
        </div>
        <div class="col-md-2 text-right p-0" style="padding-left: 0px">
            <?php
            if($appointment_id){
                $possibleCheckOutStatus = array('checked_in','vital_signs','waiting','in_consultation');
                $possibleForwardStatus = array('checked_in','vital_signs');
                if(in_array($appointmentInfo[0]->appointment_status, $possibleCheckOutStatus))
                {
                    ?>
                    <p><a href="<?php echo base_url('profile/closeAppointment/'.$appointment_id.'/'.$patient_id); ?>" class="customBtn checkOut" style="margin-top: 8px">Check Out</a></p>
                    <?php
                    if(in_array($appointmentInfo[0]->appointment_status, $possibleForwardStatus))
                    {
                        ?>
                        <p><a href="<?php echo base_url('profile/forwardDoc/'.$appointment_id.'/'.$patient_id); ?>" class="customBtn checkOut">FWD TO Doctor</a></p>
                        <?php
                    }
                }
            }
            ?>
        </div>
    </div>
</div>
<?php

if($appointment_id != '' || $appointment_id != NULL){
    if($appointmentInfo[0]->appointment_status == 'booked' || $appointmentInfo[0]->appointment_payment_status == 0){
        $appHeaderFlag = 1;
    }else{
        $appHeaderFlag = 0;
    }
}elseif($appointment_id == '' || $appointment_id == NULL){
    $appHeaderFlag = 1;
}

if($appHeaderFlag == 1){

    $cntr = 0;
    // for($cntr = 0; $cntr < count($appointmentInfo); $cntr++)
    // {

        // echo "<br>Appointment Id: ".$appointmentInfo[$cntr]->appointment_id."<br>";
        // echo "<br>Appointment Status: ".$appointmentInfo[$cntr]->appointment_status."<br>";

        $appointmentDate = strtotime($appointmentInfo[$cntr]->appointment_date);
        $today = strtotime(date('Y-m-d'));

        $appointmentStatus = array('booked','checked_in','vital_signs','waiting','in_consultation');

        // if($appointmentInfo[$cntr]->appointment_status == 'booked') {
        if(in_array($appointmentInfo[$cntr]->appointment_status, $appointmentStatus)) {

            // Check for check-in button availability
            // Depending on check in priority section will show up
            if($appointmentInfo[$cntr]->appointment_status == 'booked'){
                // check for today's date
                // if its today then visible check in button. otherwise no
                if($appointmentDate <= $today) {
                    // check in button code
                    $checkInSection = '<a href="'.base_url('calendar_view/patient_check_in/'.$patient_id.'/'.$appointmentInfo[$cntr]->appointment_id).'" class="btn btn-primary checkInBtn" id="'.$appointment_id.'" class="customeButtons">Check in</a>';
                    $colClass = 'col-md-9';
                    $prioritySection = 1;
                }else{
                    $checkInSection = '';
                    $colClass = 'col-md-12';
                    $prioritySection = 0;
                }
            }else{
                $colClass = 'col-md-12';
                $prioritySection = 0;
            }

            if($appointment_id)
            {
                ?>
                <div class="row col-md-12 appointmentInfoDiv" id="appointment_info_<?php echo $cntr; ?>"  style="background: transparent !important;">        
                <div class='<?=$colClass;?>' style="border-top: 0px !important">
                    <div class="panel-body appointmentInfoPanelBody" style="border: none !important;">
                        <div class="appointmentInfo" style="border-top: 0px !important">
                            <?php 
                            $doctor = ucwords($appointmentInfo[$cntr]->doctor_first_name." ".$appointmentInfo[$cntr]->doctor_last_name);
                            // echo "Appointment with <span>Dr. ".$doctor."</span> on ".date("d M Y", strtotime($appointmentInfo[$cntr]->appointment_date))." at ".strtoupper(date("H:i a",strtotime($appointmentInfo[$cntr]->appointment_time_slot))); 
                            ?>
                        </div>
                        <?php

                        // If Payment Status of appointment/ registration is pending then show up collect payment option
                        if($appointmentInfo[$cntr]->appointment_payment_status == 0 || $appointmentInfo[$cntr]->registration_payment_status == 0) {
                            //if any mistake comes in if condition add this "!isset($paymentPage) && "
                            // if ((profileCompletion($patient_id)>80)) {
                                ?>
                                <a class="customBtn" href="<?php echo base_url('patients/confirm_payment/'.$patient_id.'/'.$appointmentInfo[$cntr]->appointment_id); ?>" id="<?php echo $appointment_id; ?>">Collect Payment</a>
                                <?php
                            // } // checking Appointment Payment Page or No > if loop close
                            // else
                            // {
                                 ?>
                            <!-- //     <label style="font-weight: bold">Complete Profile to Collect Payment</label> -->
                                 <?php
                            // }
                        }else{
                            // Else check in button and the priority section to visible
                            echo $checkInSection;
                        }   

                        // If the appointment status is Booked - That Appointment can be rescheduled/dropped/check-in 
                        // Show up Check in & Drop & Reschedule button
                        if($appointmentInfo[$cntr]->appointment_status == 'booked') {
                            ?>
                            <a class="customBtn cancelBtn" id="<?php echo $app_info->appointment_id; ?>" onclick="return dropAppointment('<?php echo $cntr; ?>','<?php echo $appointmentInfo[$cntr]->appointment_id; ?>','<?php echo $doctor; ?>')">Drop</a>
                            <a class="customBtn rdyBtn" id="<?php echo $appointmentInfo[$cntr]->appointment_id; ?>" onclick="return rescheduleModal('<?=$appointmentInfo[$cntr]->appointment_id; ?>')">Reschedule</a>
                            <?php // close
                        }

                        $possibleCheckOutStatus = array('checked_in','vital_signs','waiting','in_consultation');
                        if(in_array($appointmentInfo[$cntr]->appointment_status, $possibleCheckOutStatus) && $appointmentInfo[$cntr]->appointment_payment_status != 0)
                        {
                            if($appointment_id)
                            {
                                ?>
                            <a href="<?php echo base_url('profile/closeAppointment/'.$appointmentInfo[$cntr]->appointment_id.'/'.$appointmentInfo[$cntr]->patient_id); ?>" class="customBtn checkOut" style="margin-top: 8px">Check Out</a>
                            <?php
                            }
                            
                        }
                        ?>
                    </div>
                </div>

                <?php if($prioritySection) { ?>
                    <!-- Priority Div -->
                    <div class="col-md-3">
                        <div class="panel-body appointmentInfoPanelBody" style="border: none !important">
                            <div class="row appointmentInfo" style="border-top: 0px !important">
                                    <?php $priority = array('disability','pregnancy','elderly','children','sick','none'); ?>
                                    <div class="col-md-4 text-right" style="margin-top: -7px"><label style="margin-bottom: 5px">Priority</label></div>
                                    <div class="col-md-8">
                                        <select id="priority_SB_<?php echo $cntr ?>" class="form-control" onchange="updatePriority('<?php echo $cntr; ?>','<?php echo $appointmentInfo[$cntr]->appointment_id; ?>');">
                                            <option value="">Select Priority</option>
                                            <?php 
                                            $x = 0;
                                            for($x=0; $x<count($priority); $x++) { ?>
                                                <option value="<?php echo $priority[$x]; ?>" <?php echo ($appointmentInfo[$cntr]->priority == $priority[$x] ? 'selected="selected"' : ''); ?>><?php echo ucwords($priority[$x]); ?></option>    
                                            <?php } ?>
                                        </select>
                                    </div>
                                <div class="col-md-12" id="ps_msg_<?php echo $cntr; ?>" style="color:green;font-size:16px;font-weight:bold;text-align:left;padding:10px;display:none">Priority Updated</div>
                            </div>
                        </div>
                    </div> 
                <?php } ?>      
            </div>  
                <?php
            }
            ?>  

            <!-- Reschedule Modal Code -->
            <div class="modal fade" id="rescheduleModal<?=$appointmentInfo[$cntr]->appointment_id; ?>" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">

                    <!-- modal content -->
                    <div class="modal-content">
                        <!-- modal header -->
                        <div class="modal-header">
                            <h4 class="modal-title">Reschedule Appointment</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <p>
                                Reschedule an appointment for <?=ucwords($appointmentInfo[$cntr]->first_name.' '.$appointmentInfo[$cntr]->last_name); ?> with Dr. <?=ucwords($appointmentInfo[$cntr]->doctor_first_name.' '.$appointmentInfo[$cntr]->doctor_last_name); ?>. Select the date and available time slot.
                            </p>
                            <div class="row col-md-12">
                                <div class="row col-md-12 col-sm-12 text-center reschedule_div" style="margin-top:10px;padding: 5px">
                                    <form method="post" class="form-inline" action="<?php echo base_url(); ?>appointment/reschedule">
                                        <input type="hidden" id="doctor_id<?=$appointmentInfo[$cntr]->appointment_id;?>" name="doctor_id" value="<?php echo $appointmentInfo[$cntr]->doctor_id; ?>">
                                        <input type="hidden" id="patient_id<?=$appointmentInfo[$cntr]->appointment_id;?>" name="patient_id" value="<?php echo $appointmentInfo[$cntr]->patient_id; ?>">
                                        <input type="hidden" id="umr_no<?=$appointmentInfo[$cntr]->appointment_id;?>" name="umr_no" value="<?php echo $appointmentInfo[$cntr]->umr_no; ?>">
                                        <input type="hidden" id="appointment_id<?=$appointmentInfo[$cntr]->appointment_id;?>" name="appointment_id" value="<?php echo $appointmentInfo[$cntr]->appointment_id; ?>">
                                         <input type="hidden" id="payment_status<?=$appointmentInfo[$cntr]->appointment_id;?>" name="payment_status" value="<?php echo $appointmentInfo[$cntr]->appointment_payment_status; ?>">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="input-date" class="sr-only">Choose Date</label>
                                                <input class="form-control" id="res_date<?=$appointmentInfo[$cntr]->appointment_id;?>" type="text" name="date" placeholder="DD-MM-YYYY" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mx-sm-3">
                                                <label for="input-password" class="sr-only">Choose Time Slot</label>
                                                <select class="form-control" id="slots<?=$appointmentInfo[$cntr]->appointment_id;?>" name="slots" style="width:150px"></select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-sm-12 text-center" style="padding: 20px">
                                            <input class="btn btn-success" type="submit" value="Reschedule" name="submit">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div> 
        <?php
        }
    //}   // For loop close
} // Appointment Id check If loop close
?>

<?php
/**
* Completely work on this module
* Got many errors and unwanted code
* @commented by Uday Kanth Rapalli
* @Dated: 25 July 2019 03:34 AM
* @access public
* @author Vikram
*/
?>
<!-- Book Appointment Modal Code -->
<?php if (!isset($paymentPage)) { ?>
<div class="modal" id="addModal"  role="dialog" aria-labelledby="myModalLabel" >
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Book Appointment</h4>
                <button type="button" class="close" data-dismiss="modal" onclick="close_modal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php echo form_open(site_url("calendar_view/book_appointment"), array("class" => "form-horizontal","id" => "app_form")); ?>
            <div class="modal-body">                
                <!-- block information -->
                <div class="row col-md-12 block_info"></div>

                <div class="row">
                    <div class="col-md-4">
                        <label for="city-code">Scheduled On </label>
                        <div class="form-group">
                            <div class="input-group m-b">
                                <input type="text" class="form-control" id="app_date" name="app_date" data-inputmask="'alias': 'date'" value="" />
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="city-code">Doctor</label>
                            <select name="doctor_name" id="doctor_name" class="form-control" onchange="get_doctor(this.value)">
                                <?php
                                foreach ($doctors_list as $key => $value) {
                                    $doctor_info = $this->db->query("select * from doctors where doctor_id='" . $value->doctor_id . "'")->row();
                                    ?>
                                    <option value="<?php echo $value->doctor_id;?>"><?php echo "DR. " . strtoupper($doctor_info->first_name." ".$doctor_info->last_name);?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="city-code">Time Slot</label>
                            <select name="time_slot" id="time_slot" class="form-control" id="time_slot" onchange="checkslots();"></select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 text-center">
                        <div class="form-header-group " style="background: #f5f5f5;">
                            <div class="header-text httal htvam">
                                <h2 id="header_13" class="form-header" data-component="header" style="font-size: 18px !important;margin:0 10px 10px 10px;font-weight: 500;line-height: 30px;">
                                    BOOKING TYPE
                                </h2>
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0">
                            <div class="row">
                                <div class="radio radio-success col-md-6">
                                    <input type="radio" name="btype" id="walkInRB" value="walkin" checked>
                                    <label for="walkInRB"> Walk-In </label>
                                </div>
                                <div class="radio radio-success col-md-6">
                                    <input type="radio" name="btype" id="phoneCallRB" value="phone">
                                    <label for="phoneCallRB"> Phone Call </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" id="priority_id">
                    <div class="col-md-12 text-center">
                        <div class="form-header-group " style="background: #f5f5f5;">
                            <div class="header-text httal htvam">
                                <h2 id="header_13" class="form-header" data-component="header" style="font-size: 18px !important;margin:0 10px 10px 10px;font-weight: 500;line-height: 30px;">
                                    PRIORITY
                                </h2>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="radio radio-success">
                                    <input type="radio" name="priority" id="radio12" value="pregnancy">
                                    <label for="radio12"> Pregnancy </label>
                                </div>
                                <div class="radio radio-success">
                                    <input type="radio" name="priority" id="radio13" value="elderly">
                                    <label for="radio13"> Elderly </label>
                                </div>
                                <div class="radio radio-success">
                                    <input type="radio" name="priority" id="radio14" value="children">
                                    <label for="radio14"> Children </label>
                                </div>
                                <div class="radio radio-success">
                                    <input type="radio" name="priority" id="radio15" value="sick">
                                    <label for="radio15"> Sick </label>
                                </div>
                                <div class="radio radio-success">
                                    <input type="radio" name="priority" id="radio16" value="other">
                                    <label for="radio16"> Other </label>
                                </div>
                                <div class="radio radio-success">
                                    <input type="radio" name="priority" id="radio17" value="none"  checked="">
                                    <label for="radio17"> None </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="btn-group col-md-12">
                        <button id="new" type="button" class="btn  ptype col-md-6">New</button>
                        <button id="followup" type="button" class="btn  ptype col-md-6">Followup</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" id="empty-message" style="display: none"><b>No results found.</b></div>
                </div>
                <div id="newDiv" style="display: none;margin-top: 20px">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="city-code">Mobile<span class="color-red">*</span></label>
                                <input class="form-control" readonly id="npmobile" name="nmobile" value="<?php echo $patient_info->mobile; ?>" type="text" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="city-code">Name<span class="color-red">*</span></label>
                                <input class="form-control text-cpaitalize" readonly id="nppname" name="npname" value="<?php echo $patient_info->first_name." ".$patient_info->last_name; ?>" type="text" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox-primary margin-r-5" style="margin-top:35px">
                                <input id="smsCB" type="checkbox" name="sms" value="yes">
                                <label for="smsCB"> SMS Reminder </label>
                            </div>
                        </div>
                        <div class="row no-margin" id="npapp_info">
                            <div class="row col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group has-success">
                                        <label for="city-code">Relative Name<span class="color-red">*</span></label>
                                        <input class="form-control" id="relative_name" name="relative_name" value="" type="text" required="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="city-code">Relation With Patient<span class="color-red">*</span></label>
                                        <input class="form-control" id="relation" name="relation" value="" type="text" required="">
                                    </div>
                                </div>
                            </div>
                        </div>
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
                                <input id="referred_by_person" name="referred_by_p" type="text" placeholder="Person Name" class="form-control text-cpaitalize" > 
                            </div>
                            <!-- ask for doctor name -->
                            <div id="Doctor" style="display: none;" class="form-group">
                                <label for="doctor_name" class="col-form-label">Doctor Name</label>
                                <!--<input id="dname" name="referred_by_doctor" type="text" placeholder="Doctor name" class="form-control" >  -->
                                <select name="referred_by_d" id="referred_by_d" class="form-control" onchange="check_ref_doctor(this.value)">
                                    <option value="">--Select--</option>
                                    <?php foreach ($doctors as $dresult) { ?>
                                        <option value="<?php echo $dresult['rfd_id']; ?>" ><?php echo $dresult['doctor_name']; ?></option>
                                    <?php } ?>
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
                    <div class="row col-md-12" id="new_ref_doctor_div" style="display: none">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ref_doctor_name" class="col-form-label">Doctor Name</label>
                                <input type="text" name="ref_doctor_name" id="ref_doctor_name" class="form-control text-cpaitalize" value="">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ref_doctor_name" class="col-form-label">Mobile</label>
                                <input type="text" name="ref_doctor_mobile" id="ref_doctor_mobile" class="form-control" value="">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ref_doctor_name" class="col-form-label">Location</label>
                                <input type="text" name="ref_doctor_location" id="ref_doctor_location" class="form-control text-cpaitalize" value="">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- follow up div -->
                <div id="followUpDiv" style="display: none;margin-top: 20px">
                    <div class="row">
                        <input type="hidden" id="id" name="patient_id" value="">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="city-code">Mobile<span class="color-red">*</span></label>
                                <input class="form-control" maxlength="10" onkeyup="checknull(this.value)" id="fuMobile" name="mobile" value="<?php echo $patient_info->mobile; ?>" type="text">
                            </div>
                        </div>
                        <div class="form-group no-margin-bottom col-md-3">
                            <label for="patientId">Patient ID</label>
                            <div>
                                <input type="text" id="umr" value="<?php echo $patient_info->umr_no; ?>" name="umr_no" class="form-control" placeholder="Patient ID">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="city-code">Patient Name<span class="color-red">*</span></label>
                                <input class="form-control text-cpaitalize" id="pname" name="pname" onkeyup="checknull(this.value)" value="<?php echo $patient_info->first_name." ".$patient_info->last_name; ?>" type="text">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="checkbox-primary margin-r-5" style="margin-top:35px">
                                <input id="smsCB" type="checkbox" name="sms" value="yes">
                                <label for="smsCB"> SMS Reminder </label>
                            </div>
                        </div>
                    </div>
                    <div class="row no-margin" id="app_info">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"  onclick="close_modal()">Cancel</button>
                <a style="display:none;cursor: pointer;" class="btn btn-primary" id="submit">Book</a>
                <a class="btn btn-primary" style="display:none;cursor:pointer" id="addSubmit">Book</a>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>      
<?php } ?>                      
<script src="<?php echo base_url(); ?>assets/plugins/moment/moment.min.js" ></script>
<script src="<?php echo base_url(); ?>assets/plugins/fullcalendar/fullcalendar.min.js" ></script>
<link href="<?php echo base_url(); ?>assets/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" rel="stylesheet"/>
<script type="text/javascript">

    var select_priority = '<?php echo $app_info->priority; ?>';
    var base_url = '<?php echo base_url(); ?>';
    var todayDate = '<?php echo date("Y-m-d"); ?>';
    var clinic_id = '<?php echo $app_info->clinic_id; ?>';
    var currentTime = '<?php echo date('H:i:s'); ?>';
    var cTime = '<?php echo date('H:i'); ?>';
    show_selected_priority(select_priority);


// Updating the Priority 
function updatePriority(counter, appointment_id) {
// get the id for priority select box
var priority = $("#priority_SB_"+counter).val();
$.ajax({
    type: "POST",
    url: '<?php echo base_url(); ?>calendar_view/change_priority',
    data:{appointment_id:appointment_id, priority:priority},
    success: function(result){
        $("#ps_msg_"+counter).show();
        setTimeout(function() {
            $("#ps_msg_"+counter).hide('blind', {}, 500)
        }, 2000);
    } 
});
}

function dropAppointment(counter, appointment_id, doctor){
    $('<div></div>').appendTo('body')
    .html('<div><h6>Are you sure you want to drop the appointment with Dr.'+doctor+'?</h6></div>')
    .dialog({
        modal: true, title: 'Please confirm dropping an appointment', zIndex: 10000, autoOpen: true,
        width: 'auto', resizable: false,
        buttons: {
            Yes: function () {
                $(this).dialog("close");
                // Drop the appointment
                $.ajax({
                    type: "POST",
                    url: base_url+'calendar_view/drop_app',
                    data:{appointment_id:appointment_id},
                    success: function(result)
                    {
                        $("#appointment_info_"+counter).empty();
                        $("#appointment_info_"+counter).text("Appointment with Dr."+doctor+" has been dropped successfully").
                        setTimeout(function() {
                            $("#appointment_info_"+counter).hide('blind', {}, 500)
                        }, 2000);
                    }       
                });  
            },
            No: function () {                                                                 
                $(this).dialog("close");
                return false;
            }
        },
        close: function (event, ui) {
            $(this).remove();
        }
    });    
}

function show_modal(){
    $("#addModal").show();

    $('input[name="app_date"]').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        startDate: moment(),
        minDate:moment(),
        locale: {
            format: 'DD/MM/YYYY'
        }
    });
    var date= $("#app_date").val();

    check_doctor_slots(date,'');
}

function close_modal(){
    $("#addModal").hide();
}

</script>  

<?php if (!isset($paymentPage)) { ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/calendar_view.js"></script>
<?php } ?>

<script type="text/javascript">
    $(document).on("click","#followup",function(){
        var pid = '<?php echo $patient_info->patient_id; ?>';
        var mobile = '<?php echo $patient_info->mobile; ?>';
        var umr = '<?php echo $patient_info->umr_no; ?>';
        var name = '<?php echo $patient_info->first_name." ".$patient_info->last_name; ?>';
        $("#mobile").val(mobile);
        $("#umr").val(umr);
        $("#pname").val(name);
        checkappointments(pid);
    });
</script>