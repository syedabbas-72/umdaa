
<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">HOME</a></li>
          <li class="breadcrumb-item"><a href="<?php echo base_url('doctor_settings'); ?>">DOCTOR SETTINGS</a></li>
          <li class="breadcrumb-item active"><a href="#">CALENDAR BLOCKING</a></li>          
        </ol>
  </div>
</div>

        <section class="main-content">
          <div class="row">
        <div class="col-2">
           <?php $this->view("doctor_settings/left_nav"); ?>
        </div>
        <div class="col-10">
          <div class="card" style="padding: 5px 30px !important;margin-bottom: 10px">
              <div class="card-header" style="padding: 0">
              <div class="row">
                            <div class="col-md-6" style="padding-top: 5px">
                                <span > <h2><?php echo $clinic_info->clinic_name; ?></h2></span>
                
                            </div>
                          
               </div>
                        </div>
                    </div>
                    <div class="card">
                <div class="card-body">

                    <div class="row">
                        <div class="col-12">
                            <div class="tab-content">
                             
                                <div class="tab-pane active" id="info">  <div class="widget white-bg">
                 
                                        <a href="<?php echo base_url('doctor_settings'); ?>" class = "btn btn-primary" style="float: right;margin-bottom: 5px;">BACK TO CLINICS</a>
               
                                    <div class="row">
                                            <div class="col-md-12 col-xs-6 b-r"> <strong>BLOCK DATES</strong>
                                              <!-- <a href='<?php echo base_url('clinic_doctor/add_week_day/'.$clinic_doctor_id)?>' class="btn btn-info btn-sm btn-rounded pull-right">Add Weekday</a> -->
                                            </div>
                                            
                                    </div>
                  
                 
                  <hr>
                  
                        <div class="card-body">
                        <?php if(count($weekdays)>0){
                        foreach($weekdays as $key => $slot) { ?>
                          
                            <a  class="btn btn-default btn-rounded btn-border btn-xs" style="width: 250px;"><?php echo date("d-m-Y", strtotime($slot->dates)); ?></a> <a  onclick="return confirm('Are you sure you want to delete?')" href="<?php echo base_url('settings/cal_date_del/'.$slot->calendar_blocking_id.'/'.$slot->doctor_id)?>"><i class="fas fa-trash error"></i></a>
                          
                            <?php
                        }}else{ ?>

                            <div><b>NO BLOCK DATES AVAILABLE</b></div>
                        <?php } ?>

                        
                
                </div>
                            

                 
                  
          </div>

                                        </div>
                                 </div>
                                    

                                </div>
                                
    
                                
                                <!-- /.tab-pane -->
                            </div>
                        </div>
               </div>
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
        </div>
                                               

                         
                            </div>
                     


        </section>  

