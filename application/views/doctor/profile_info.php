          <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title">Doctor Profile</div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="index-2.html">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li><a class="parent-item" href="#">Doctors</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li class="active">Doctor Profile</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN PROFILE SIDEBAR -->
                            <div class="profile-sidebar">
                                <div class="card card-topline-aqua">
                                    <div class="card-body no-padding height-9">
                                        <div class="row">
                                            <div class="profile-userpic">
                                                <?php if($doctor_info->profile_image == "") { ?>
                                                <img src="<?php echo base_url(); ?>assets/img/default-avatar-user.png" class="img-responsive" style="height: 130px" alt="">
                                            <?php } else { ?>
                                                <img src="<?php echo base_url(); ?>uploads/profile_image/<?php echo $doctor_info->profile_image; ?>" style="height: 130px" class="img-responsive" alt="">
                                            <?php } ?>
                                                 </div>
                                        </div>
                                        <div class="profile-usertitle">
                                            <div class="profile-usertitle-name"> <?php echo "Dr. ".strtoupper($doctor_info->first_name." ".$doctor_info->last_name); ?></div>
                                            <div class="profile-usertitle-job"> <?php echo strtoupper($doctor_info->department_name);?></div>
                                            <div class="profile-usertitle-job"> <?php echo strtoupper($doctor_info->registration_code);?></div>
                                        </div>
                                       
                                        <!-- END SIDEBAR USER TITLE -->
                                        <!-- SIDEBAR BUTTONS -->
                                        <div class="profile-userbuttons">
                                            
                                            <a href="<?php echo base_url('settings/doctor_info/'.$doctor_info->doctor_id); ?>" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-pink">Edit Profile</a>
                                        </div>
                                        <!-- END SIDEBAR BUTTONS -->
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-head card-topline-aqua">
                                        <header>About Me</header>
                                    </div>
                                    <div class="card-body no-padding height-9">
                                      
                                        <ul class="list-group list-group-unbordered">
                                            <li class="list-group-item">
                                                <b>Gender </b>
                                                <div class="profile-desc-item pull-right"><?php echo ucfirst($doctor_info->gender); ?></div>
                                            </li>
                                           <li class="list-group-item">
                                                <b>Experience </b>
                                                <div class="profile-desc-item pull-right"><?php echo $doctor_info->experience; ?></div>
                                            </li>
                                            
                                            <li class="list-group-item">
                                                <b>Qualification</b>
                                                <div class="profile-desc-item pull-right"><?php echo strtoupper($doctor_info->qualification); ?></div>
                                            </li>
                                        </ul>
                                      
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-head card-topline-aqua">
                                        <header>Practice Address</header>
                                    </div>
                                    <div class="card-body no-padding height-9">
                                        <div class="row text-center m-t-10">
                                    <div class="col-md-12">
                                        <p><?php echo $clinic_info->address; ?></p>
                                    </div>
                                </div>
                                    </div>
                                </div>
                       
                            </div>
                            <!-- END BEGIN PROFILE SIDEBAR -->
                            <!-- BEGIN PROFILE CONTENT -->
                            <div class="profile-content">
                                <div class="row">
                  <div class="profile-tab-box">
                    <div class="p-l-20">
                      <ul class="nav ">
                        <li class="nav-item tab-all"><a
                          class="nav-link active show" href="#tab1" data-toggle="tab">About Me</a></li>
                      
                        <li class="nav-item tab-all p-l-20"><a class="nav-link"
                          href="#tab3" data-toggle="tab">Timings</a></li>
                      </ul>
                    </div>
                  </div>
                  <div class="white-box">
                                      <!-- Tab panes -->
                                    <div class="tab-content">
                                        <div class="tab-pane active fontawesome-demo" id="tab1">
                            <div id="biography" >
                                                <div class="row">
                                                    <div class="col-md-3 col-sm-6 b-r"> <strong>Full Name</strong>
                                                        <br>
                                                        <p class="text-muted"><?php echo "Dr. ".ucwords($doctor_info->first_name." ".$doctor_info->last_name); ?></p>
                                                    </div>
                                                    <div class="col-md-3 col-sm-6 b-r"> <strong>Mobile</strong>
                                                        <br>
                                                        <p class="text-muted"><?php echo $doctor_info->mobile;?></p>
                                                    </div>
                                                    <div class="col-md-3 col-sm-6 b-r"> <strong>Email</strong>
                                                        <br>
                                                        <p class="text-muted" style="word-break: break-all;white-space: normal;"><?php echo $doctor_info->email;?></p>
                                                    </div>
                                                    <div class="col-md-3 col-sm-6"> <strong>Location</strong>
                                                        <br>
                                                        <p class="text-muted"><?php echo $doctor_info->address;?></p>
                                                    </div>
                                                </div>
                                                <hr>
                                                <p class="m-t-30"><?php echo $doctor_info->about; ?></p>
                                                <br>
                                                <h4 class="font-bold">Education</h4>
                                                <hr>
                                                <ul>

                                                   
                                                    <?php if(count($education_info)>0){ 
                                                    foreach ($education_info as $key => $value) { ?>
                                                        <li><?php echo ucfirst($value->degree_name)." , ".ucfirst($value->university)." , ".$value->year; ?></li>

                                                  <?php } } ?>
                                                </ul>
                                                <br>
                                                <h4 class="font-bold">Membership</h4>
                                                <hr>
                                                <ul>
                                                  <?php if($doctor_info->membership_in !="") { 

                                                    $split_ms = explode(",",$doctor_info->membership_in);
                                                    foreach ($split_ms as  $msvalue) { ?>
                                                     

                                                    <li><?php echo $msvalue; ?></li>
                                                   
                                                  <?php } }?>
                                                </ul>
                                                <br>
                                                <h4 class="font-bold">Achievements</h4>
                                                <hr>
                                                <ul>
                                                   <?php if($doctor_info->acheivements !="") { 

                                                    $split_am = explode(",",$doctor_info->acheivements);
                                                    foreach ($split_am as  $amvalue) { ?>
                                                     

                                                    <li><?php echo $amvalue; ?></li>
                                                   
                                                  <?php } }?>
                                                </ul>
                                                <br>
                                            </div>
                          </div>
                       
                          <div class="tab-pane" id="tab3">
                            <div class="row">
                              <div class="col-md-12 col-sm-12">
                                                <div class="card-head">
                                                    <header>Schedule</header>
                                                   <!--  <a href='<?php echo base_url('clinic_doctor/add_week_day/'.$clinic_doctor_id)?>' class="btn btn-info btn-sm btn-rounded pull-right">Add Weekday</a> -->
                                                 
                                              
                                                </div>
                                                <div class="card-body " id="bar-parent1">
                                                  <div class="widget white-bg">
                                

                 <?php foreach ($weekdays as $key => $value) {
                  $day_name = date('l', strtotime("Sunday +{$value->weekday} days")); ?>
                 
                    <div class="col-md-12">
                           <?php echo "<span style='color: #3a405b;padding: 10px 0;text-align: center;font-size: 16px;cursor: pointer;font-weight: 700;'>".strtoupper($day_name)."</span>"; ?>
            <!--   <a href='<?php echo base_url('clinic_doctor/add_sloat/'.$value->weekday.'/'.$value->clinic_doctor_weekday_id);?>' class="btn pull-right btn-info btn-rounded  btn-xs">Add Slot</a> -->
            </div>
                     
                        <div class="card-body">
                        <?php 
                        $slots = $this->db->query("select * from clinic_doctor_weekday_slots cws inner join clinic_doctor_weekdays cdw on(cws.clinic_doctor_weekday_id = cdw.clinic_doctor_weekday_id) where cws.clinic_doctor_weekday_id = '".$value->clinic_doctor_weekday_id."' and cdw.weekday='".$value->weekday."'")->result();
                        if(count($slots)>0){
                        foreach($slots as $key => $slot) { ?>
                          
                            <a  class="btn  btn-rounded btn-border btn-xs" style="width: 25.285%;padding: 4px;letter-spacing: normal;border-radius: 3px;border: 1px solid #e0e0e4;padding: 5px 0;display: inline-block;text-align: center; color: #3a405b;cursor: pointer;"><?php echo date("h:i A", strtotime($slot->from_time)) ." - ".date("h:i A", strtotime($slot->to_time)); ?></a> <!-- <a  onclick="return confirm('Are you sure you want to delete?')" href="<?php echo base_url('clinic_doctor/delete_week_day_slot/'.$slot->clinic_doctor_weekday_slot_id)?>"><i style="color:#FF3636 !important" class="fas fa-trash error"></i></a> -->
                          
                            <?php
                        }

                        echo "<hr></div>";
                    }else{
                      echo "No Slots Available On This Day";
                    }

                 }
                 ?>
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
                                <!-- END PROFILE CONTENT -->
                            </div>
                        </div>