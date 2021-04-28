
<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">HOME</a></li>
          <li class="breadcrumb-item"><a href="<?php echo base_url('doctor_settings'); ?>">SETTINGS</a></li>
          <li class="breadcrumb-item active"><a href="#">DOCTOR TIMINGS</a></li>          
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
                                <span > <h2><?php echo $clinic_name; ?></h2></span>
                
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
                 
                 <?php if(!empty($clinic_doctor_id)){?>
                                    <div class="row">
                                            <div class="col-md-12 col-xs-6 b-r"> <strong>SCHEDULE</strong>
                                              <a href='<?php echo base_url('clinic_doctor/add_week_day/'.$clinic_doctor_id)?>' class="btn btn-info btn-sm btn-rounded pull-right">Add Weekday</a>
                                            </div>
                                            
                                    </div>
                  
                 <?php }?>
                  <hr>
                 <?php foreach ($weekdays as $key => $value) {
                  $day_name = date('l', strtotime("Sunday +{$value->weekday} days")); ?>
                 
                    <div class="col-md-12" style="padding: 5px 15px;border-bottom:1px solid #f7f4f4">
                           <?php echo "<span style='font-size:15px;padding:5px;'>".strtoupper($day_name)."</span>"; ?>
              <a href='<?php echo base_url('clinic_doctor/add_sloat/'.$value->weekday.'/'.$value->clinic_doctor_weekday_id);?>' class="btn pull-right btn-info btn-rounded  btn-xs">Add Slot</a>
            </div>
                     
                        <div class="card-body">
                        <?php 
                        $slots = $this->db->query("select * from clinic_doctor_weekday_slots cws inner join clinic_doctor_weekdays cdw on(cws.clinic_doctor_weekday_id = cdw.clinic_doctor_weekday_id) where cws.clinic_doctor_weekday_id = '".$value->clinic_doctor_weekday_id."' and cdw.weekday='".$value->weekday."'")->result();
                        if(count($slots)>0){
                        foreach($slots as $key => $slot) { ?>
                          
                            <a  class="btn btn-default btn-rounded btn-border btn-xs"><?php echo date("h:i A", strtotime($slot->from_time)) ." - ".date("h:i A", strtotime($slot->to_time)); ?></a> <a  onclick="return confirm('Are you sure you want to delete?')" href="<?php echo base_url('clinic_doctor/delete_week_day_slot/'.$slot->clinic_doctor_weekday_slot_id)?>"><i class="fas fa-trash error"></i></a>
                          
                            <?php
                        }

                        echo "</div>";
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


<div class="modal fade bs-modal-md in" id="application-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-header">
    <h4 class="modal-title">Edit Booking Times</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
            <div class="modal-body">
    <form id="createProjectCategory" class="ajax-form" method="POST" autocomplete="off">
        <input type="hidden" name="_token" value="wtZLRqQHYUzGYSBchOHeD5XTuarjwAqEN014BACG">        <input type="hidden" name="_method" value="PUT">        <div class="form-body">
            <div class="row">
                <div class="col-sm-12 ">
                    <div class="form-group">
                        <h4 class="form-control-static">Monday</h4>
                    </div>

                    <div class="form-group">
                        <label>Open Time</label>

                        <div class="input-group date time-picker">
                            <input type="text" class="form-control" name="start_time" value="10:00 AM">
                            <span class="input-group-append input-group-addon">
                                <button type="button" class="btn btn-info"><span class="fa fa-clock-o"></span></button>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Close Time</label>

                        <div class="input-group date time-picker">
                            <input type="text" class="form-control" name="end_time" value="08:00 PM">
                            <span class="input-group-append input-group-addon">
                                <button type="button" class="btn btn-info"><span class="fa fa-clock-o"></span></button>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Allow multiple bookings for same date and time?</label>
                        <select name="multiple_booking" id="multiple_booking" class="form-control">
                            <option selected="" value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="status" class="form-control">
                            <option selected="" value="enabled">Enabled</option>
                            <option value="disabled">Disabled</option>
                        </select>
                    </div>

                </div>
            </div>
        </div>
        <div class="form-actions">
            <button type="button" id="save-category" class="btn btn-success"> <i class="fa fa-check"></i> Save</button>
        </div>
    </form></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                <button type="button" class="btn btn-success"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script>
        $('.edit-row').click(function () {
            var id = $(this).data('row-id');
            var url = 'http://appointo.froid.works/account/booking-times/:id/edit';
            url = url.replace(':id', id);

            $('#modelHeading').html('Edit Booking Times');
            $.ajaxModal('#application-modal', url);
        });

   $.ajaxModal = function (selector, url, onLoad) {

        $(selector).removeData('bs.modal').modal({
            show: true
        });
        $(selector + ' .modal-content').removeData('bs.modal').load(url);

        // Trigger to do stuff with form loaded in modal
        $(document).trigger("ajaxPageLoad");

        // Call onload method if it was passed in function call
        if (typeof onLoad != "undefined") {
            onLoad();
        }

        // Reset modal when it hides
        $(selector).on('hidden.bs.modal', function () {
            $(this).find('.modal-body').html('Loading...');
            $(this).find('.modal-footer').html('<button type="button" data-dismiss="modal" class="btn dark btn-outline">Cancel</button>');
            $(this).data('bs.modal', null);
        });
    };
    </script>