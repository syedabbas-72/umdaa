<div class="page-bar">
  <div class="page-title-breadcrumb">
      <!-- <div class=" pull-left">
        <div class="page-title">Form Layouts</div>
      </div> -->
      <ol class="breadcrumb page-breadcrumb pull-left">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="#"><?php echo $clinic_name ?></a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>   
          <li><a class="parent-item" href="<?php echo base_url('settings'); ?>">SETTINGS</a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>      
          <li class="active">VISIT TIMINGS</li>
      </ol>

  </div>
</div>
<!-- <div class="success" id="successMessage"> </div> -->
<!-- <div id="success_message" class="btn btn-success"></div> -->
<div class="row">
    <div class="col-2">
        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
          <?php  $this->load->view("settings/doctor_left_nav"); ?>               
        
        </div>
    </div>
    <div class="col-10">
        <div class="card">
            <div class="card-body">
                <div class="row">
                        <div class="col-12">
                            <div class="tab-content">
                                <div class="tab-pane active" id="info">  <div class="widget white-bg">
                                <span class="page-title"><?=getDoctorName($doctor_id)?>
                                  <a href="<?php echo base_url('settings/staff'); ?>" class = "btn btn-primary" style="float: right;margin-bottom: 5px;">BACK TO DOCTORS</a>
                                </span>

                                <div class="row col-md-12">
                                <div class="col-md-12 subHeader">
                                  <span class="font-weight-bold text-uppercase text-left">Visit Timings</span>
                                  <?php count($weekdays) 
                                  ?>
                                   <?php  if(count($weekdays)!='7'){
                                  ?>
                                  <!-- <button class="btn btn-primary" style="float:right;"
                                 
                                  >ADD SLOTS HERE</button> -->

                                  <!-- <a href="<?php echo base_url('settings/add_new_slots/'.$doctor_id); ?>"><button type="button" class="btn btn-primary"  style="float:right;">
  Add Slots  
</button></a> -->
<button type="button" class="btn btn-primary"  style="float:right;" data-toggle="modal" data-target="#add">
  Add Slots  
</button>


                                  <?php } ?>

                                  <!-- Modal -->
<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel" style="color:white;">Add Timings</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div class="form-group">
  <input type="hidden" id="doctorId" value="<?php echo $doctor_id?>">
    <label for="exampleFormControlSelect1">Select Days</label>
    <select class="form-control" id="daySelection" required>
      <option value="0">Select Day Here</option>
      <?php foreach($slotsDays as $slots) {?>
      <option value="<?php echo $slots ?>"><?php if($slots == '7') echo 'Sunday';
      elseif($slots == '1') echo 'Monday'; 
      elseif($slots == '2') echo 'Tuesday'; 
      elseif($slots == '3') echo 'Wednesday';  
      elseif($slots == '4') echo 'Thrusday'; 
      elseif($slots == '5') echo 'Friday';
      elseif($slots == '6') echo 'Saturday';
      else ''; ?></option>
      
      <?php }?>
      <!-- <option>4</option>
      <option>5</option> -->
    </select>
  </div>

  <div class="form-group">
    <label for="exampleFormControlSelect1">Select Session</label>
    <select class="form-control" id="sessionDay">
      <option value="0">Select Session</option>
      <option value="morning">Morning</option>
      <option value="afternoon">Afternoon</option>
      <option value="evening">Evening</option>
      <!-- <option>4</option>
      <option>5</option> -->
    </select>
  </div>
                  
  <div class="form-group">
  <label for="exampleFormControlSelect1">From Time</label>
      <div class="clockpicker   mini-time operationTimeFrom" 
      data-placement="left" data-align="top" data-autoclose="true">
          <select  class="form-control time_slot" name="to" id="toId">
          </select>

        
          </div>
  </div>

  <div class="form-group">
  <label for="exampleFormControlSelect1">To Time</label>
      <div class="clockpicker   mini-time operationTimeFrom" 
      data-placement="left" data-align="top" data-autoclose="true">
          <select  class="form-control time_slot" name="from" id="fromId">
          </select>

        
          </div>
  </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" id="submitForm">Save</button>
      </div>
    </div>
  </div>
</div>

                                </div>
                                </div>
           
                  <div class="row col-md-12">
                    
                  </div>

                  <!-- <hr> -->
                 <?php foreach ($weekdays as $key => $value) {
                  $day_name = date('l', strtotime("Sunday +{$value->weekday} days")); ?>
                 <!--MONDAY,TUESDAY  -->
              <div class="col-md-12">
                      <?php echo "<span style='color: #3a405b;padding: 10px 0;text-align: center;font-size: 16px;cursor: pointer;font-weight: 700;'>".strtoupper($day_name)."</span>"; ?>
          <!-- <a href='<?php echo base_url('settings/add_sloat/'.$value->weekday.'/'.$value->clinic_doctor_weekday_id);?>' class="btn pull-right btn-info btn-rounded  btn-xs">Add Slot</a>   -->
      </div>

  <!-- Modal -->
   <!-- Modal content-->
   <!-- <div id="testmodal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
               
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> -->

<!-- <div id="element" class="btn btn-default show-modal">show modal</div> -->
   <!-- END MODAL -->

<br>
  
                        <div class="card-body">
                        <div class="row">
                        <div class="col-md-4" >
                          <p style="font-size:15px;">Morning</p>
                          
                          <?php
                               $slots = $this->db->query("select * from 
                               clinic_doctor_weekday_slots cws inner join 
                               clinic_doctor_weekdays cdw 
                               on(cws.clinic_doctor_weekday_id = cdw.clinic_doctor_weekday_id) 
                               where cws.clinic_doctor_weekday_id = '".$value->clinic_doctor_weekday_id."' 
                               and cdw.weekday='".$value->weekday."'   and cws.session='morning'")->result();
                               if(count($slots)>0){
                               
                               foreach($slots as $key => $slot) { 
                                 // if($slot->session == 'morning')
                                 {
                                 ?>
                              <!-- <p>Morning</p><p>Afternoon</p><p>Evening</p> -->
                                  <!-- <a  class="btn  btn-rounded btn-border btn-xs" style="width: 25.285%;padding: 4px;letter-spacing: normal;border-radius: 3px;border: 1px solid #e0e0e4;padding: 5px 0;display: inline-block;text-align: center; color: #3a405b;cursor: pointer;"><?php echo date("h:i A", strtotime($slot->from_time)) ." - ".date("h:i A", strtotime($slot->to_time)); ?></a> <a  onclick="return confirm('Are you sure you want to delete?')" href="<?php echo base_url('settings/delete_week_day_slot/'.$slot->clinic_doctor_weekday_slot_id)?>"><i style="color:#FF3636 !important" class="fas fa-trash error"></i></a>  -->
       
       <a  class="btn  btn-rounded btn-border btn-xs"
        style="width: 91.285%;padding: 4px;letter-spacing: normal;border-radius: 3px;border: 1px solid #e0e0e4;padding: 5px 0;display: inline-block;text-align: center; color: #3a405b;cursor: pointer;">
        <?php echo date("h:i A", strtotime($slot->from_time)) ." - ".date("h:i A", strtotime($slot->to_time)); ?></a>
        <a data-id="<?php echo $slot->clinic_doctor_weekday_slot_id?>" class="show-modal" 
        data-value='<?php echo date("H:i", strtotime($slot->from_time)) ."-".date("H:i", strtotime($slot->to_time)); ?>'>
        <i style="color:blue !important" class="fa fa-pencil-alt"></i></a> 
       
        <?php  
         }
       ?>
         <!-- START MODAL -->
           <div id="testmodal" class="modal fade">
           <div class="modal-dialog">
               <div class="modal-content">
                   <div class="modal-header">
                       <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
                       <h3 style="color:white;">Modify Visit Timings</h3>
                   </div>
                   <div class="modal-body">
                   <!-- <input type="text" id="bookId"> -->
                   <input type="hidden" class="suprise" id="suprise">
                     <div class="row">
                         <div class="col-md-6">
                                 <p>From Time</p>
                                                
                                 <!-- <div class="operationDayTimeContainer" id="Sun1"><div class="row col-md-12 operationTime" style="height: 50px;padding: 5px;"><div class="clockpicker col-md-3  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                             <select class="form-control from_7 time_slot" name="from_7_1[]"></select>
                              
                           </div>-->
                           <div class="clockpicker   mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                             <select  class="form-control time_slot" name="to" id="to">
                             </select>
                             <input type="hidden" id="halfcooked" >
                         
                           </div>
                   
                         </div>
                         <div class="col-md-6">
                                 <p>To Time</p>
       
                                 <!-- START TIME -->
                                 <div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                             <select class="form-control to_7 time_slot" name="from" id="from"></select>
                             <input type="hidden" id="fullcooked">
                           </div>
                                 <!-- END TIME -->
       
                         </div>
                     </div>
                   </div>
                   <div class="modal-footer">
                       <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                       <button type="button" class="btn btn-primary" data-dismiss="modal" id="submit123">SAVE</button>
                   </div>
               </div>
           </div>
       </div>
                                   <?php
                               }
       
                               // AFTERNOON
                           
                               // AFTERNOON
                       
       
                               
                               // EVENING
                           
                               // EVENING
                           
                              //  echo "<hr></div>";
                           }
                           else{
                          ?>
       <button type="button" class="btn btn-primary user_dialoggg" data-toggle="modal" 
                    data-id=" <?php echo $value->weekday;?>"
                    data-target="#examMorning">
  Add Morning Slots  
</button>

<!-- ADD AFTERNOON MODULE -->
<div class="modal fade" id="examMorning" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel" style="color:white;">Add Morning Slots</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
      
        <input type="hidden" class="suprise" id="sSessionM" value="morning">
        <input type="hidden" id="dayNummm" value="">
        <input type="hidden" class="suprise" id="sclinic_idM" value="<?php echo $clinic_id ?>">
        <input type="hidden" id="sdoctor_idM" value="<?=($doctor_id)?>">
       
        <div class="col-md-6">
                          <p>From Time</p>
                                         
                          <!-- <div class="operationDayTimeContainer" id="Sun1"><div class="row col-md-12 operationTime" style="height: 50px;padding: 5px;"><div class="clockpicker col-md-3  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select class="form-control from_7 time_slot" name="from_7_1[]"></select>
                       
                    </div>-->
                    <div class="clockpicker   mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select  class="form-control time_slot" name="too" id="toIdM">
                      </select>
                      <input type="hidden" id="halfcookedd" >
                  
                    </div>
            
                  </div>
                  <div class="col-md-6">
                          <p>To Time</p>

                          <!-- START TIME -->
                          <div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select class="form-control to_7 time_slot" name="fromm" id="fromIdM"></select>
                      <input type="hidden" id="fullcookedd">
                    </div>
                          <!-- END TIME -->

                  </div>
        </div>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
        <!-- <button type="button" class="btn btn-primary" id="submit1234">Save </button> -->
        <button type="button" class="btn btn-primary" data-dismiss="modal" id="addSlotsM">SAVE</button>
      </div>
    </div>
  </div>
</div>
<!-- ADD AFTERNOON MODULE -->

                           <?php  } ?>
                          </div>
                          <div class="col-md-4">
                          <p style="font-size:15px;">Afternoon</p>
                            
                          <?php
                               $slots = $this->db->query("select * from 
                               clinic_doctor_weekday_slots cws inner join 
                               clinic_doctor_weekdays cdw 
                               on(cws.clinic_doctor_weekday_id = cdw.clinic_doctor_weekday_id) 
                               where cws.clinic_doctor_weekday_id = '".$value->clinic_doctor_weekday_id."' 
                               and cdw.weekday='".$value->weekday."'   and cws.session='afternoon'")->result();
                               if(count($slots)>0){
                               
                               foreach($slots as $key => $slot) { 
                                 // if($slot->session == 'morning')
                                 {
                                 ?>
                              <!-- <p>Morning</p><p>Afternoon</p><p>Evening</p> -->
                                  <!-- <a  class="btn  btn-rounded btn-border btn-xs" style="width: 25.285%;padding: 4px;letter-spacing: normal;border-radius: 3px;border: 1px solid #e0e0e4;padding: 5px 0;display: inline-block;text-align: center; color: #3a405b;cursor: pointer;"><?php echo date("h:i A", strtotime($slot->from_time)) ." - ".date("h:i A", strtotime($slot->to_time)); ?></a> <a  onclick="return confirm('Are you sure you want to delete?')" href="<?php echo base_url('settings/delete_week_day_slot/'.$slot->clinic_doctor_weekday_slot_id)?>"><i style="color:#FF3636 !important" class="fas fa-trash error"></i></a>  -->
       
       <a  class="btn  btn-rounded btn-border btn-xs"
        style="width: 91.285%;padding: 4px;letter-spacing: normal;border-radius: 3px;border: 1px solid #e0e0e4;padding: 5px 0;display: inline-block;text-align: center; color: #3a405b;cursor: pointer;">
        <?php echo date("h:i A", strtotime($slot->from_time)) ." - ".date("h:i A", strtotime($slot->to_time)); ?></a>
        <a data-id="<?php echo $slot->clinic_doctor_weekday_slot_id?>" class="show-modal" 
        data-value='<?php echo date("H:i", strtotime($slot->from_time)) ."-".date("H:i", strtotime($slot->to_time)); ?>'>
        <i style="color:blue !important" class="fa fa-pencil-alt"></i></a> 
       
        <?php  
         }
       ?>
         <!-- START MODAL -->
           <div id="testmodal" class="modal fade">
           <div class="modal-dialog">
               <div class="modal-content">
                   <div class="modal-header">
                       <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
                       <h3 style="color:white;">Modify Visit Timings</h3>
                   </div>
                   <div class="modal-body">
                   <!-- <input type="text" id="bookId"> -->
                   <input type="hidden" class="suprise" id="suprise">
                     <div class="row">
                         <div class="col-md-6">
                                 <p>From Time</p>
                                                
                                 <!-- <div class="operationDayTimeContainer" id="Sun1"><div class="row col-md-12 operationTime" style="height: 50px;padding: 5px;"><div class="clockpicker col-md-3  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                             <select class="form-control from_7 time_slot" name="from_7_1[]"></select>
                              
                           </div>-->
                           <div class="clockpicker   mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                             <select  class="form-control time_slot" name="to" id="to">
                             </select>
                             <input type="hidden" id="halfcooked" >
                         
                           </div>
                   
                         </div>
                         <div class="col-md-6">
                                 <p>To Time</p>
       
                                 <!-- START TIME -->
                                 <div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                             <select class="form-control to_7 time_slot" name="from" id="from"></select>
                             <input type="hidden" id="fullcooked">
                           </div>
                                 <!-- END TIME -->
       
                         </div>
                     </div>
                   </div>
                   <div class="modal-footer">
                       <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                       <button type="button" class="btn btn-primary" data-dismiss="modal" id="submit123">SAVE</button>
                   </div>
               </div>
           </div>
       </div>
                                   <?php
                               }
       
                               // AFTERNOON
                           
                               // AFTERNOON
                       
       
                               
                               // EVENING
                           
                               // EVENING
                           
                              //  echo "<hr></div>";
                           }
                           else{
                          ?>
       <button type="button" class="btn btn-primary user_dialog" data-toggle="modal" 
                    data-id=" <?php echo $value->weekday;?>"
                    data-target="#exampleModalll">
  Add Afternoon Slots 
</button>

<!-- ADD AFTERNOON MODULE -->
<div class="modal fade" id="exampleModalll" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel" style="color:white;">Add Afternoon Slots</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
      
        <input type="hidden" class="suprise" id="sSession" value="afternoon">
        <input type="hidden" id="dayNum" value="">
        <input type="hidden" class="suprise" id="sclinic_id" value="<?php echo $clinic_id ?>">
        <input type="hidden" id="sdoctor_id" value="<?=($doctor_id)?>">
       
        <div class="col-md-6">
                          <p>From Time</p>
                                         
                          <!-- <div class="operationDayTimeContainer" id="Sun1"><div class="row col-md-12 operationTime" style="height: 50px;padding: 5px;"><div class="clockpicker col-md-3  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select class="form-control from_7 time_slot" name="from_7_1[]"></select>
                       
                    </div>-->
                    <div class="clockpicker   mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select  class="form-control time_slot" name="too" id="toIdA">
                      </select>
                      <input type="hidden" id="halfcookedd" >
                  
                    </div>
            
                  </div>
                  <div class="col-md-6">
                          <p>To Time</p>

                          <!-- START TIME -->
                          <div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select class="form-control to_7 time_slot" name="fromm" id="fromIdA"></select>
                      <input type="hidden" id="fullcookedd">
                    </div>
                          <!-- END TIME -->

                  </div>
        </div>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
        <!-- <button type="button" class="btn btn-primary" id="submit1234">Save </button> -->
        <button type="button" class="btn btn-primary" data-dismiss="modal" id="addSlots">SAVE</button>
      </div>
    </div>
  </div>
</div>
<!-- ADD AFTERNOON MODULE -->

                           <?php  } ?>
                          </div>
                          <div class="col-md-4"><p style="font-size:15px;">Evening</p>
                          <?php
                               $slots = $this->db->query("select * from 
                               clinic_doctor_weekday_slots cws inner join 
                               clinic_doctor_weekdays cdw 
                               on(cws.clinic_doctor_weekday_id = cdw.clinic_doctor_weekday_id) 
                               where cws.clinic_doctor_weekday_id = '".$value->clinic_doctor_weekday_id."' 
                               and cdw.weekday='".$value->weekday."'   and cws.session='evening'")->result();
                               if(count($slots)>0){
                               
                               foreach($slots as $key => $slot) { 
                                 // if($slot->session == 'morning')
                                 {
                                 ?>
                              <!-- <p>Morning</p><p>Afternoon</p><p>Evening</p> -->
                                  <!-- <a  class="btn  btn-rounded btn-border btn-xs" style="width: 25.285%;padding: 4px;letter-spacing: normal;border-radius: 3px;border: 1px solid #e0e0e4;padding: 5px 0;display: inline-block;text-align: center; color: #3a405b;cursor: pointer;"><?php echo date("h:i A", strtotime($slot->from_time)) ." - ".date("h:i A", strtotime($slot->to_time)); ?></a> <a  onclick="return confirm('Are you sure you want to delete?')" href="<?php echo base_url('settings/delete_week_day_slot/'.$slot->clinic_doctor_weekday_slot_id)?>"><i style="color:#FF3636 !important" class="fas fa-trash error"></i></a>  -->
       
       <a  class="btn  btn-rounded btn-border btn-xs"
        style="width: 91.285%;padding: 4px;letter-spacing: normal;border-radius: 3px;border: 1px solid #e0e0e4;padding: 5px 0;display: inline-block;text-align: center; color: #3a405b;cursor: pointer;">
        <?php echo date("h:i A", strtotime($slot->from_time)) ." - ".date("h:i A", strtotime($slot->to_time)); ?></a>
        <a data-id="<?php echo $slot->clinic_doctor_weekday_slot_id?>" class="show-modal" 
        data-value='<?php echo date("H:i", strtotime($slot->from_time)) ."-".date("H:i", strtotime($slot->to_time)); ?>'>
        <i style="color:blue !important" class="fa fa-pencil-alt"></i></a> 
       
        <?php  
         }
       ?>
         <!-- START MODAL -->
           <div id="testmodal" class="modal fade">
           <div class="modal-dialog">
               <div class="modal-content">
                   <div class="modal-header">
                       <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
                       <h3 style="color:white;">Modify Visit Timings</h3>
                   </div>
                   <div class="modal-body">
                   <!-- <input type="text" id="bookId"> -->
                   <input type="hidden" class="suprise" id="suprise">
                     <div class="row">
                         <div class="col-md-6">
                                 <p>From Time</p>
                                                
                                 <!-- <div class="operationDayTimeContainer" id="Sun1"><div class="row col-md-12 operationTime" style="height: 50px;padding: 5px;"><div class="clockpicker col-md-3  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                             <select class="form-control from_7 time_slot" name="from_7_1[]"></select>
                              
                           </div>-->
                           <div class="clockpicker   mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                             <select  class="form-control time_slot" name="to" id="to">
                             </select>
                             <input type="hidden" id="halfcooked" >
                         
                           </div>
                   
                         </div>
                         <div class="col-md-6">
                                 <p>To Time</p>
       
                                 <!-- START TIME -->
                                 <div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                             <select class="form-control to_7 time_slot" name="from" id="from"></select>
                             <input type="hidden" id="fullcooked">
                           </div>
                                 <!-- END TIME -->
       
                         </div>
                     </div>
                   </div>
                   <div class="modal-footer">
                       <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                       <button type="button" class="btn btn-primary" data-dismiss="modal" id="submit123">SAVE</button>
                   </div>
               </div>
           </div>
       </div>
                                   <?php
                               }
       
                               // AFTERNOON
                           
                               // AFTERNOON
                       
       
                               
                               // EVENING
                           
                               // EVENING
                           
                              //  echo "<hr></div>";
                           }
                           else{
                          ?>
               <button type="button" class="btn btn-primary user_dialogg" data-toggle="modal" 
                    data-id=" <?php echo $value->weekday;?>"
                    data-target="#exampleModalevening">
  Add EVENING Slots 
</button>

<!-- ADD AFTERNOON MODULE -->
<div class="modal fade" id="exampleModalevening" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel" style="color:white;">Add Evening Slots
       </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
    
        <input type="hidden" class="suprise" id="sSessionn" value="evening">
        <input type="hidden" id="dayNumm" value="">
        <input type="hidden" class="suprise" id="sclinic_idd" value="<?php echo $clinic_id ?>">
        <input type="hidden" id="sdoctor_idd" value="<?=($doctor_id)?>">
       
        <div class="col-md-6">
                          <p>From Time</p>
                                         
                          <!-- <div class="operationDayTimeContainer" id="Sun1"><div class="row col-md-12 operationTime" style="height: 50px;padding: 5px;"><div class="clockpicker col-md-3  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select class="form-control from_7 time_slot" name="from_7_1[]"></select>
                       
                    </div>-->
                    <div class="clockpicker   mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select  class="form-control time_slot" name="too" id="toIdd">
                      </select>
                      <input type="hidden" id="halfcookeddd" >
                  
                    </div>
            
                  </div>
                  <div class="col-md-6">
                          <p>To Time</p>

                          <!-- START TIME -->
                          <div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select class="form-control to_7 time_slot" name="fromm" id="fromIdd"></select>
                      <input type="hidden" id="fullcookeddd">
                    </div>
                          <!-- END TIME -->

                  </div>
        </div>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
        <!-- <button type="button" class="btn btn-primary" id="submit1234">Save </button> -->
        <button type="button" class="btn btn-primary" data-dismiss="modal" id="addSlotss">SAVE</button>
      </div>
    </div>
  </div>
</div>
<!-- ADD AFTERNOON MODULE -->

                           <?php  } ?>
                          </div>
                          </div>
                        <?php 
                      
                        // $slots = $this->db->query("select * from 
                        // clinic_doctor_weekday_slots cws inner join 
                        // clinic_doctor_weekdays cdw 
                        // on(cws.clinic_doctor_weekday_id = cdw.clinic_doctor_weekday_id) 
                        // where cws.clinic_doctor_weekday_id = '".$value->clinic_doctor_weekday_id."' 
                        // and cdw.weekday='".$value->weekday."'")->result();
                        // if(count($slots)>0){
                        
                        foreach($slots as $key => $slot) { 
                          // if($slot->session == 'morning')
                          {
                          ?>
                       <!-- <p>Morning</p><p>Afternoon</p><p>Evening</p> -->
                           <!-- <a  class="btn  btn-rounded btn-border btn-xs" style="width: 25.285%;padding: 4px;letter-spacing: normal;border-radius: 3px;border: 1px solid #e0e0e4;padding: 5px 0;display: inline-block;text-align: center; color: #3a405b;cursor: pointer;"><?php echo date("h:i A", strtotime($slot->from_time)) ." - ".date("h:i A", strtotime($slot->to_time)); ?></a> <a  onclick="return confirm('Are you sure you want to delete?')" href="<?php echo base_url('settings/delete_week_day_slot/'.$slot->clinic_doctor_weekday_slot_id)?>"><i style="color:#FF3636 !important" class="fas fa-trash error"></i></a>  -->



 <?php  
  }
?>
  <!-- START MODAL -->
    <div  class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
                <h3 style="color:white;">Modify Visit Timings</h3>
            </div>
            <div class="modal-body">
            <!-- <input type="text" id="bookId"> -->
            <input type="hidden" class="suprise" id="suprise">
              <div class="row">
                  <div class="col-md-6">
                          <p>From Time</p>
                                         
                          <!-- <div class="operationDayTimeContainer" id="Sun1"><div class="row col-md-12 operationTime" style="height: 50px;padding: 5px;"><div class="clockpicker col-md-3  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select class="form-control from_7 time_slot" name="from_7_1[]"></select>
                       
                    </div>-->
                    <div class="clockpicker   mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select  class="form-control time_slot" name="to" id="to">
                      </select>
                      <input type="hidden" id="halfcooked" >
                  
                    </div>
            
                  </div>
                  <div class="col-md-6">
                          <p>To Time</p>

                          <!-- START TIME -->
                          <div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select class="form-control to_7 time_slot" name="from" id="from"></select>
                      <input type="hidden" id="fullcooked">
                    </div>
                          <!-- END TIME -->

                  </div>
              </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="submit123">SAVE</button>
            </div>
        </div>
    </div>
</div>
                            <?php
                        }

                        // AFTERNOON
                    
                        // AFTERNOON
                

                        
                        // EVENING
                    
                        // EVENING
                    
                        echo "<hr></div>";
                    // }
               
                    
                    // else{
                    //   echo "No Slots Available On This Day";
                    // }

                 }
                 ?>
                </div>
                            

              
                  
          </div>

                                        </div>
                                 </div>
                                    

                                </div>
                                
    
                                
                                <!-- /.tab-pane -->
                            </div>
                        </div>
               
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
        </div>
                                               

                         
                            </div>
                         
    <script>

        $('.show-modal').click(function(){
              var id = $(this).data('id');
              var data_value = $(this).attr("data-value");
              var a = data_value.split("-");
              $('#to option[value="'+a[0]+'"]').attr('selected','selected');
              $('#from option[value="'+a[1]+'"]').attr('selected','selected');
              $("#testmodal").modal('show');
              $('#suprise').val(id); 
                  $('#to').click(function(){
                  var idto = $(this).val();
                  $('#halfcooked').val(idto); 
                  });
                  $('#from').click(function(){
                  var idfrom = $(this).val();
                  $('#fullcooked').val(idfrom); 
                  });
        });
    </script>  
    <script>
        var url = "<?php echo base_url('settings/changeTimings'); ?>";
        $('#success_message').hide();
        $('#submit123').click(function(){
          var id = $('#suprise').val();
          // alert(id);
          var id1= $('#halfcooked').val();
          // alert(bla);
          var id2 = $('#fullcooked').val();
          // alert(blaa);
          $.ajax({
                type:'POST',
                url:url,
                data:{id: id,id1:id1,id2:id2},
                success:function(data){
                       window.location.reload();
                       confirm("Success!");
                    //  $('#success_message').show();
                    //  $('#success_message').fadeIn().html("Success");
                    //     setTimeout(function() {
                    //     $('#success_message').fadeOut("slow");
                    //     }, 3000 );
     }
          });
        });
    </script>    

      <script>
              // var url = "<?php echo base_url('settings/insertTimings'); ?>";
        $('#addSlots').click(function(){
          // alert('Hello');
          // var url = "<?php echo base_url('settings/insertTimings'); ?>";
          var sdoctor_id = $('#sdoctor_id').val();
          //  alert('sdoctor_id= '+sdoctor_id);
          var sclinic_id= $('#sclinic_id').val();
          // alert('sclinic_id= '+sclinic_id);
          var sSession = $('#sSession').val();
          // alert('sSession= '+sSession);
           var toId = $('#toIdA').val();
            //  alert('toId= '+toId);
           var fromId = $('#fromIdA').val();
          //  alert('fromId= '+fromId);
           var dayNum = $('#dayNum').val();

          $.ajax({
                type:'POST',
                url:'<?php echo base_url('settings/insertTimings'); ?>',
                // dataType : "json",
                data:{sdoctor_id: sdoctor_id,sclinic_id:sclinic_id,sSession:sSession,
                toId:toId,fromId:fromId,dayNum:dayNum},
                success:function(data){
  
                        window.location.reload();
                        confirm("Success!");
                 
     }
          });
        });
    </script>     

    
      <script>
              // var url = "<?php echo base_url('settings/insertTimings'); ?>";
        $('#addSlotss').click(function(){
          // alert('Hello');
          // var url = "<?php echo base_url('settings/insertTimings'); ?>";
          var sdoctor_id = $('#sdoctor_idd').val();
          //  alert('sdoctor_id= '+sdoctor_id);
          var sclinic_id= $('#sclinic_idd').val();
          // alert('sclinic_id= '+sclinic_id);
          var sSession = $('#sSessionn').val();
          // alert('sSession= '+sSession);
           var toId = $('#toIdd').val();
            // alert('toId= '+toId);
           var fromId = $('#fromIdd').val();
            // alert('fromId= '+fromId);
           var dayNum = $('#dayNumm').val();

          $.ajax({
                type:'POST',
                url:'<?php echo base_url('settings/insertTimings'); ?>',
                // dataType : "json",
                data:{sdoctor_id: sdoctor_id,sclinic_id:sclinic_id,sSession:sSession,
                  toId:toId,fromId:fromId,dayNum:dayNum},
                success:function(data){
  
                        window.location.reload();
                        confirm("Success!");
                 
     }
          });
        });
    </script>                   
                
        <script type="text/javascript">

          var timeSelect = '';
                   
            for(var i=0; i < 12; i++) {
              var hour = i;
              var dis_h = i;
              if(hour <= 9) hour = '0' + hour;
              if(dis_h == 0) dis_h = 12;
              if(dis_h <= 9) dis_h = '0' + dis_h
              var time1 = hour + ':00';
              var time2 = hour + ':30';
              timeSelect +='<option value="' + time1 + '">' + dis_h  + ':00 AM</option>';
              timeSelect +='<option value="' + time2 + '">' + dis_h  + ':30 AM</option>';
            }
            timeSelect +='<option value="12:00">12:00 PM</option>';
            timeSelect +='<option value="12:30">12:30 PM</option>';
            for(var i=1; i < 12; i++) {
              var hour = i +12;
              var dis_h = i;
              if(hour <= 9) hour = '0' + hour;
              if(dis_h == 1) dis_h = 1;
              if(dis_h <= 9) dis_h = '0' + dis_h
              var time1 = hour + ':00';
              var time2 = hour + ':30';
              timeSelect +='<option value="' + time1 + '">' + dis_h  + ':00 PM</option>';
              timeSelect +='<option value="' + time2 + '">' + dis_h  + ':30 PM</option>';
            }
          
          $(".time_slot").html(timeSelect);
  function picker_add(day_id,name,id){
     $("#add_"+id).attr("disabled","disabled");
    var next_row_id = parseInt(id)+1;
      $("#"+name+id).after('<div class="operationDayTimeContainer" id="'+name+next_row_id+'"><div class="operationTime row col-md-12" style="height: 50px;padding: 5px;"><div class="clockpicker col-md-3  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true"><select class="form-control time_slot from_'+day_id+'[]" name="from_'+day_id+'_'+next_row_id+'[]"></select></div><div class="clockpicker col-md-3 mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true"><select class="form-control time_slot to_'+day_id+'_'+next_row_id+'[]" name="to_'+day_id+'_'+next_row_id+'[]"></select></div><input type="hidden" name="total[]" value="'+day_id+'_'+next_row_id+'"><div class="mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true"><input type="button" id="add_'+next_row_id+'" class="btn btn-success" onclick= "picker_add(\'' + day_id  +'\',\'' + name  +'\',\'' + next_row_id  +'\')" value="+" style="padding: 6px;margin-right:10px;width: 100%"/></div><div class="mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true"><input type="button" id="delete_'+next_row_id+'" class="btn btn-danger" onclick= "picker_delete(\'' + name  +'\',\'' + next_row_id  +'\')" value="-" style="padding: 6px;margin-left:10px;width: 100%" /></div></div></div>');
      $(".time_slot").html(timeSelect);
    
   }
   function picker_delete(name,id){
    $("#"+name+id).remove();
 $("#add_"+id).removeAttr("disabled");
   }

   $(document).on('click','#submit',function(){
 
       for(i=1;i<=7;i++){
        alert($(".from_"+i).val());
       }
  
   });
   

   $(document).on("click", ".user_dialog", function (e) {
     var UserName = $(this).data('id');
     $("#dayNum").val(UserName);
});

$(document).on("click", ".user_dialogg", function (e) {
     var UserNamee = $(this).data('id');
     $("#dayNumm").val(UserNamee);
});

$(document).on("click", ".user_dialoggg", function (e) {
     var UserNameee = $(this).data('id');
     $("#dayNummm").val(UserNameee);
});


</script>    

<script>
        $('#addSlotsM').click(function(){
          var sdoctor_id = $('#sdoctor_idM').val();
          //  alert('sdoctor_id= '+sdoctor_id);
          var sclinic_id= $('#sclinic_idM').val();
          // alert('sclinic_id= '+sclinic_id);
          var sSession = $('#sSessionM').val();
          // alert('sSession= '+sSession);
           var toId = $('#toIdM').val();
            // alert('toId= '+toId);
           var fromId = $('#fromIdM').val();
            // alert('fromId= '+fromId);
           var dayNum = $('#dayNummm').val();

          $.ajax({
                type:'POST',
                url:'<?php echo base_url('settings/insertTimings'); ?>',
                // dataType : "json",
                data:{sdoctor_id: sdoctor_id,sclinic_id:sclinic_id,sSession:sSession,
                  toId:toId,fromId:fromId,dayNum:dayNum},
                success:function(data){
  
                        window.location.reload();
                        confirm("Success!");
                 
     }
          });
        });
    </script>      

<script>
        $('#submitForm').click(function(){
          var daySelection = $('#daySelection').val();
           var sessionDay = $('#sessionDay').val();
           var toId = $('#toId').val();      
           var fromId = $('#fromId').val();
           var doctorId = $('#doctorId').val();
          //  alert(sessionDay);
           if(daySelection=='0' && sessionDay == '0')
          {
            alert('Please All Fields');
          }
          else
          {
            $.ajax({
                type:'POST',
                url:'<?php echo base_url('settings/addSlotTimings'); ?>',
                // dataType : "json",
                data:{daySelection: daySelection,sessionDay:sessionDay,toId:toId,
                  fromId:fromId,doctorId:doctorId},
                success:function(data){
  
                        window.location.reload();
                        confirm("Success!");
                 
           }
          });
          }
       
        });
    </script>  
 
