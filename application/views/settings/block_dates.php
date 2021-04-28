<div class="page-bar">
  <div class="page-title-breadcrumb">
      <!-- <div class=" pull-left">
        <div class="page-title">Form Layouts</div>
      </div> -->
      <ol class="breadcrumb page-breadcrumb pull-left">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url('dashboard'); ?>">HOME</a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>  
          <li><a class="parent-item" href="<?php echo base_url('settings'); ?>">SETTINGS</a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>        
          <li class="active">DOCTOR BLOCKED DATES</li>
      </ol>
  </div>
</div>


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
                                <button class="btn btn-primary" onclick="blockCalendar()"  style="float: right;margin-bottom: 5px;"><i class="fas fa-calendar-alt"></i>BLOCK CALENDAR</button>
                                &nbsp;&nbsp;
                                  <a href="<?php echo base_url('settings/staff'); ?>" class = "btn btn-primary" style="float: right;margin-bottom: 5px;margin-right:5px;">BACK TO DOCTORS</a>
                                </span>
           
                                <!-- <div class="row">
                                        <div class="col-md-12 col-xs-6 b-r"> <strong>CALENDAR BLOCKING</strong>
                                          <a href='<?php echo base_url('clinic_doctor/add_week_day/'.$clinic_doctor_id)?>' class="btn btn-info btn-sm btn-rounded pull-right">Add Weekday</a>
                                        </div>
                                        
                                </div> -->
              
             
              <!-- <hr> -->
              
                    <div class="card-body">
                      <table class="table customTable">
                        <thead>
                          <th>Sno</th>
                          <th>Doctor Name</th>
                          <th>Blocked Dates</th>
                          <th>Remark</th>
                          <th>Actions</th>
                        </thead>
                        <tbody>
                          <?php
                          $i=1;
                          foreach ($block_dates as $value) {
                            $doctor = $this->db->query("select * from doctors where doctor_id='".$value->doctor_id."'")->row();
                            ?>
                            <tr>
                              <td><?=$i?></td>
                              <td>Dr. <?=$doctor->first_name." ".$doctor->last_name?></td>
                              <td><?=$value->dates?></td>
                              <td><?=$value->remark?></td>
                              <td><a  onclick="return confirm('Are you sure you want to delete?')" href="<?php echo base_url('settings/cal_date_del/'.$value->calendar_blocking_id.'/'.$value->doctor_id)?>"><i  class="fas fa-trash"></i></a></td>
                            </tr>
                            <?php
                            $i++;
                          }
                          ?>
                        </tbody>
                      </table>

                    
            
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
<div class="modal fade" id="BlockModal"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php
            echo form_open(site_url("Settings/block_calendar"), array(
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
                            <select name="block_doctor" id="doctor_name" readonly class="form-control doctorName" style="padding-top: 5px">
                              <option>Select Doctor</option>
                                <?php
                                foreach ($doctors_list as $key => $value) {
                                    $doctor_info = $this->db->query("select * from doctors where doctor_id='" . $value->doctor_id . "'")->row();
                                    ?>
                                    <option value="<?php echo $value->doctor_id; ?>" <?=($value->doctor_id==$doctor_id)?'selected':''?>><?php echo "DR. " . strtoupper($doctor_info->first_name . " " . $doctor_info->last_name); ?></option>
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
<script src="<?php
   echo base_url();
   ?>assets/plugins/moment/moment.min.js" ></script>
<link href="<?php echo base_url(); ?>assets/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bootstrap-daterangepicker/daterangepicker.js"></script>
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
function blockCalendar(){
   $("#BlockModal").modal();
   }
</script>
   
                     



