
<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">HOME</a></li>
          <li class="breadcrumb-item"><a href="<?php echo base_url('doctor_settings'); ?>">DOCTOR SETTINGS</a></li>
          <li class="breadcrumb-item active"><a href="#">CONSULTATION</a></li>          
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
                                <span > <h2><?php echo $clinic_list->clinic_name; ?></h2></span>
                
                            </div>
                          
               </div>
                        </div>
                    </div>
            <div class="card">
              
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="tab-content">
                             
                                <div class="tab-pane active" id="info">
                                 
                                  <a href="<?php echo base_url('doctor_settings'); ?>" class = "btn btn-primary" style="float: right;margin-bottom: 5px;">BACK TO CLINICS</a>
                             <form method="POST" action="<?php echo base_url('doctor_settings/doctor_info/'.$clinic_doctor->clinic_id.'/'.$clinic_doctor->doctor_id);?>" role="form">
                   
          <div class="card" style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
    <div class="card-header" style="font-size: 15px;padding: 10px 20px">CLINIC SETTINGS</div>
  </div>
          <div class="row col-md-12">
          
                    <div class="col-md-4"><div class="form-group">
                      <label for="consulting_fee" class="col-form-label">CONSULTING FEE<span style="color:red;">*</span></label>
                          <input id="consulting_fee" name="consulting_fee" value="<?php echo $clinic_doctor->consulting_fee; ?>" type="number" placeholder="" class="form-control" required="">
                      </div>
                    </div>
                   
          <div class="col-md-4"><div class="form-group">
                      <label for="review_days" class="col-form-label">REVIEW DAYS<span style="color:red;">*</span></label>
                        <input id="review_days" name="review_days" value="<?php echo $clinic_doctor->review_days; ?>" type="number" placeholder="" class="form-control" required="">
                      </div>
                    </div>
                    <div class="col-md-4"><div class="form-group">
                      <label for="review_days" class="col-form-label">REVIEW TIMES<span style="color:red;">*</span></label>
                        <input id="review_times" name="review_times" value="<?php echo $clinic_doctor->review_times; ?>" type="number" placeholder="" class="form-control" required="">
                      </div>
                    </div>
                  </div>          
                

                        <div class="col-sm-6">
                          <div class="pull-right">
                              <input type="submit" value="Save" name="submit" class="btn btn-success btn-rounded box-shadow">
                          </div>
                        </div>
                       
                                </form>

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
