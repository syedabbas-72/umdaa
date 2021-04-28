<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
<style type="text/css">
  body{
        font-family: 'Work Sans', sans-serif;
  }
</style>
<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">HOME</a></li>
          <li class="breadcrumb-item"><a href="<?php echo base_url('settings'); ?>">SETTINGS</a></li>
          <li class="breadcrumb-item active"><a href="#">STAFF</a></li>          
        </ol>
  </div>
</div>

        <section class="main-content">
          <div class="row">
        <div class="col-2">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <?php $this->view("settings/settings_left_nav"); ?>            
            
            </div>
        </div>
        <div class="col-10">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="tab-content">
                             
                                <div class="tab-pane active" id="staff">
                                        <div class = "row col-md-12">
                                  <div><span>DOCTORS</span>
                                       <span style="margin-left: 790px;"><a href = "<?php echo base_url('doctor/doctor_add')?>" class = "btn btn-primary" style="float: right;">Add</a></span>
                                  </div>
                                  <div></div>

                                          <!-- <div class="card" style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
                              <span class="card-header" style="font-size: 15px;padding: 10px 20px">DOCTORS</span></div> -->

                              <table class = "table">
                                <thead style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
                                  <tr>
                                <th>NAME</th>
                                <th>QUALIFICATION</th>
                                <th>REGISTRATION CODE</th>
                                <th>ACTION</th>
                              </tr>
                            </thead>
                                <tbody>
                                  <?php for($i=0;$i<count($clinic_doctor);$i++) { ?>
                                    <tr>
                                      <td>DR.<?php echo strtoupper($clinic_doctor[$i]->first_name).' '.strtoupper($clinic_doctor[$i]->last_name);?></td>
                                      <td><?php echo $clinic_doctor[$i]->qualification;?></td>
                                      <td><?php echo $clinic_doctor[$i]->registration_code;?></td>
                                      
                                      <td>
                                        <a href = "<?php echo base_url('settings/doctor_info/'.$clinic_doctor[$i]->doctor_id)?>">EDIT</a>
                                      
                                      </td>
                                    </tr>
                                  <?php } ?>
                                  
                                </tbody>
                                
                              </table>

                               </div>

                                 <div class = "row col-md-12" style="margin-top: 50px">
                                  <div><span>EMPLOYEES</span>
                                       <span style="margin-left: 826px;"><a href = "<?php echo base_url('employee/employee_add')?>" class = "btn btn-primary" style="float: right;">Add</a></span>
                                  </div>
                                  <div></div>

                              <table class = "table">
                                <thead style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
                                  <tr>
                                <th>NAME</th>
                                
                                <th>EMPLOYEE CODE</th>
                                <th>ROLE</th>
                                <th>ACTION</th>
                              </tr>
                            </thead>
                                <tbody>
                                  <?php for($i=0;$i<count($staff);$i++) { 

                                    ?>
                                    <tr>
                                      <td data-toggle="tooltip" data-placement="top" title="front office, pharmacy"><?php echo strtoupper($staff[$i]->first_name).' '.strtoupper($staff[$i]->last_name);?></td>
                                      <td><?php echo $staff[$i]->employee_code;?></td>
                                      <td><?php echo $staff[$i]->role_name;?></td>
                                      
                                      
                                      <td>
                                        <a href = "<?php echo base_url('employee/employee_update/'.$staff[$i]->employee_id)?>">EDIT</a>
                                       
                                      </td>
                                    </tr>
                                  <?php } ?>
                                  
                                </tbody>
                                
                              </table>

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
