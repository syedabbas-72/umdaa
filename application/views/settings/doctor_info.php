<div class="page-bar">
  <div class="page-title-breadcrumb">
      <!-- <div class=" pull-left">
        <div class="page-title">Form Layouts</div>
      </div> -->
      <ol class="breadcrumb page-breadcrumb pull-left">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url('dashboard'); ?>"><?php echo $clinic_name?></a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>  
          <li><a class="parent-item" href="<?php echo base_url('settings'); ?>">SETTINGS</a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>        
          <li class="active">DOCTOR INFO</li>
      </ol>
  </div>
</div>
<?php if($this->session->flashdata('msg')): ?>
    <p><?php echo $this->session->flashdata('msg'); ?></p>
<?php endif; ?>
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
                             
                                <div class="tab-pane active" id="info">
                                <span class="page-title"><?=getDoctorName($doctor_list->doctor_id)?>
                                  <a href="<?php echo base_url('settings/staff'); ?>" class = "btn btn-primary" style="float: right;margin-bottom: 5px;">BACK TO DOCTORS</a></span>
                             <form method="POST" action="<?php echo base_url('settings/doctor_info/'.$doctor_list->doctor_id);?>" role="form">
                   
          <div class="card" style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
    <div class="card-header" style="font-size: 15px;padding: 10px 20px">CLINIC INFORMATION</div>
  </div>
          <div class="row col-md-12">
          
          <div class="col-md-3"><div class="form-group">
            <label for="consulting_fee" class="col-form-label">CONSULTING FEE<span style="color:red;">*</span></label>
                <input id="consulting_fee" name="consulting_fee" value="<?php echo $clinic_doctor->consulting_fee; ?>" type="text" onkeypress="return numeric()" maxlength="4" placeholder="" class="form-control" required="" onkeypress="return numeric()">
            </div>
          </div>
          
          <div class="col-md-3"><div class="form-group">
            <label for="online_consulting_fee" class="col-form-label">TELE CONSULTATION FEE<span style="color:red;">*</span></label>
                <input id="online_consulting_fee" name="online_consulting_fee" value="<?php echo $clinic_doctor->online_consulting_fee; ?>" type="text" onkeypress="return numeric()" maxlength="4" placeholder="" class="form-control" required="" onkeypress="return numeric()">
            </div>
          </div>
                   
          <div class="col-md-3"><div class="form-group">
                      <label for="review_days" class="col-form-label">REVIEW DAYS<span style="color:red;">*</span></label>
                        <input id="review_days" name="review_days" value="<?php echo $clinic_doctor->review_days; ?>" type="text" onkeypress="return numeric()"  placeholder="" class="form-control" required="" onkeypress="return numeric()" maxlength="2">
                      </div>
                    </div>
                    <div class="col-md-3"><div class="form-group">
                      <label for="review_days" class="col-form-label">REVIEW TIMES<span style="color:red;">*</span></label>
                        <input id="review_times" name="review_times" value="<?php echo $clinic_doctor->review_times; ?>" type="text" maxlength="2" placeholder="" class="form-control" required="" onkeypress="return numeric()">
                      </div>
                    </div>
                  </div>          
                 <div class="card" style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
    <div class="card-header" style="font-size: 15px;padding: 10px 20px">DEMOGRAPHIC DETAILS</div>
  </div>

                  <div class="row col-md-12">

          
                    <div class="col-md-6"><div class="form-group">
                      <label for="first_name" class="col-form-label">FIRST NAME<span style="color:red;">*</span></label>
                          <input id="first_name" name="first_name" style="text-transform: capitalize;" value="<?php echo ucfirst($doctor_list->first_name); ?>" type="text" placeholder="" class="form-control" required="" onkeypress="return alpha()" maxlength="35">
                      </div>
                    </div>
                    <div class="col-md-6"><div class="form-group">
                      <label for="last_name" class="col-form-label">LAST NAME<span style="color:red;">*</span></label>
                        <input id="last_name" name="last_name"  style="text-transform: capitalize;" value="<?php echo ucfirst($doctor_list->last_name); ?>" type="text" placeholder="" class="form-control" required="" onkeypress="return alpha()" maxlength="35">
                      </div>
                    </div>
                  </div> 


                  <div class="row col-md-12">

          
                  <div class="col-md-6"><div class="form-group">
                        <label for="reg_code" class="col-form-label">REGISTRATION CODE<span style="color:red;">*</span></label>
                          <input id="reg_code" style="text-transform: uppercase;" name="reg_code" value="<?php echo $doctor_list->registration_code; ?>" type="text" placeholder="" class="form-control" required="" maxlength="10">
                      </div>
                    </div>
                    <div class="col-md-6"><div class="form-group">
                        <label for="gender" class="col-form-label">GENDER<span style="color:red;">*</span></label>
                        <div class="row">
                          <div class="radio radio-success">
                            <input type="radio" name="gender" id="radio12" value="male" <?=($doctor_list->gender=="male")?'checked':''?> >
                            <label for="radio12"> MALE </label>
                          </div>
                          <div class="radio radio-danger">
                            <input type="radio" name="gender" id="radio13" value="female" <?=($doctor_list->gender=="female")?'checked':''?> >
                            <label for="radio13"> FEMALE </label>
                          </div>
                        </div>
                          <!-- <input id="gender" style="text-transform: uppercase;" name="gender" value="<?php echo $doctor_list->gender; ?>" type="text" placeholder="" class="form-control" required="" maxlength="10"> -->
                      </div>
                    </div>
                    </div> 

                      <!-- <div class="col-md-4">                                       
                        <div class="form-group">
                          <label>Change GENDER</label>
                            <div class="row">
                             <label for="reg_code" class="col-form-label">Gender<span style="color:red;">*</span></label>
                            <input id="reg_code" style="text-transform: uppercase;" name="reg_code" value="<?php echo $doctor_list->gender; ?>" type="text" placeholder="" class="form-control" required="" maxlength="10"> 
                              <div class="radio radio-success">
                                <input type="radio" name="gender" id="radio12" value="male">
                                <label for="radio12"> MALE </label>
                              </div>
                              <div class="radio radio-danger">
                                <input type="radio" name="gender" id="radio13" value="female">
                                <label for="radio13"> FEMALE </label>
                              </div>
                            </div>
                        </div>
                    </div> -->               
                  </div>
         <!-- <div class="row col-md-12">
                     <div class="card" style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
    <div class="card-header" style="font-size: 15px;padding: 10px 20px">PACKAGE INFORMATION</div>
  </div>
              <div class="col-md-4"><div class="form-group">
                
                   <select name="package_id" id="package_id" class="form-control" required="">
                    <option>--Select Clinic--</option>
                    <?php foreach($packages_list as $value) { ?>
                        <option value="<?php echo $value->package_id?>" <?php if($value->package_id==$doctor_list->package_id){echo "selected";} ?>><?php echo $value->package_name; ?> </option>
                    <?php } ?>
                  </select> 
                  <div class="patientInfo">
					<p class="lead text-center">PACKAGE </p>
					<p class="text-muted text-center">
						<?php echo $value->package_name; ?>	
					</p>
				</div>
                  
                </div>
              </div>
              <div class="col-md-4"><div class="form-group">
                 <div class="patientInfo">
					<p class="lead text-center">PACKAGE SUBSCRIPTION DATE</p>
					<p class="text-muted text-center">
						<?php echo date("d-m-Y",strtotime($doctor_list->package_subscription_date)); ?>	
					</p>
				</div>
                </div>
              </div>
              <div class="col-md-4"><div class="form-group">
                 <div class="patientInfo">
					<p class="lead text-center">PACKAGE EXPIRY DATE</p>
					<p class="text-muted text-center">
						<?php echo date("d-m-Y",strtotime($doctor_list->package_expiry_date)); ?>	
					</p>
				</div>
                </div>
              </div>
              </div>-->
                  <div class="card" style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
    <div class="card-header" style="font-size: 15px;padding: 10px 20px">QUALIFICATION DETAILS</div>
  </div>
                  
                  <div class="row col-md-12">
                    <?php
                          $qualification = explode(", ", $doctor_list->qualification);
                          ?>
                    <div class="col-md-4"><div class="form-group">
                        <label for="qualification" class="col-form-label">QUALIFICATION<span style="color:red;">*</span></label>
                        <input type="text" name="qualification" class="form-control" value="<?=$doctor_list->qualification?>" >
                      <!--   <select class="form-control qualification" name="qualification[]" required="" multiple="">
                          
                          <option disabled="">Select</option>
                          <option value="MBBS" <?=(in_array("MBBS", $qualification))?'selected':''?> >MBBS</option>
                          <option value="MD" <?=(in_array("MD", $qualification))?'selected':''?> >MD</option>
                          <option value="DM" <?=(in_array("DM", $qualification))?'selected':''?> >DM</option>
                          <option value="M ch" <?=(in_array("M ch", $qualification))?'selected':''?> >M ch</option>
                          <option value="DA" <?=(in_array("DA", $qualification))?'selected':''?> >DA</option>
                          <option value="DO" <?=(in_array("DO", $qualification))?'selected':''?> >DO</option>
                          <option value="DL" <?=(in_array("DL", $qualification))?'selected':''?> >DL</option>
                          <option value="DD" <?=(in_array("DD", $qualification))?'selected':''?> >DD</option>
                          <option value="DGO" <?=(in_array("DGO", $qualification))?'selected':''?> >DGO</option>
                          <option value="MS" <?=(in_array("MS", $qualification))?'selected':''?> >MS</option>
                          <option value="BSC" <?=(in_array("BSC", $qualification))?'selected':''?> >BSC</option>
                          <option value="PHD" <?=(in_array("PHD", $qualification))?'selected':''?> >PHD</option>
                        </select> -->
                        <!-- <input id="qualification" style="text-transform: uppercase;" name="qualification" value="" type="text" placeholder="" class="form-control" required=""> -->
                      </div>
                    </div>
                    <div class="col-md-4"><div class="form-group">
                        <label for="department" class="col-form-label">DEPARTMENT<span style="color:red;">*</span></label>
                            <select name="department" id="department" class="form-control" required="">
                                <option>--select department--</option>
                                <?php foreach($department_list as $value) { ?>
                                      <option value="<?php echo $value->department_id?>"<?php if($value->department_id==$doctor_list->department_id){echo "selected";} ?>><?php echo $value->department_name; ?> </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4"><div class="form-group">
                      <label for="experience" class="col-form-label">EXPERIENCE<span style="color:red;">*</span></label>
                          <div class="row">
                            <div class="col-md-3">
                              <input id="experience" value="<?php echo $doctor_list->experience; ?>" name="experience" type="text" placeholder="" class="form-control input-small" required="" onkeypress="return numeric()" maxlength="2"> 
                            </div>
                            <div class="col-md-4"><label class="col-form-label">Years</label></div>
                          </div>
                      </div>
                    </div>
                  </div> 

                 <div class="card" style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
    <div class="card-header" style="font-size: 15px;padding: 10px 20px">CONTACT DETAILS</div>
  </div>
                <div class="row col-md-12">
                  <div class="col-md-4"><div class="form-group">
                      <label for="address" class="col-form-label">ADDRESS<span style="color:red;">*</span></label>
                        <input id="address"  style="text-transform: capitalize;" name="address" value="<?php echo ucwords($doctor_list->address); ?>" type="text" placeholder="" class="form-control" required="" maxlength="255">
                      </div>
                  </div>
                  <div class="col-md-4"><div class="form-group">
                       <label for="state" class="col-form-label">STATE<span style="color:red;">*</span></label>
                              <select name="state" id="state" class="form-control" required="">
                                <option>--select state--</option>
                                <?php foreach ($state_list as $key => $value) {?>
                                <option value="<?php echo $value->state_id; ?>"<?php if($value->state_id==$doctor_list->state_id){echo "selected";} ?>><?php echo $value->state_name;?> </option>
                              <?php }?>
                            </select>
                      </div>
                  </div>
                  <div class="col-md-4"><div class="form-group">
                        <label for="pincode" class="col-form-label">PINCODE</label>
                          <input id="pincode" name="pincode" value="<?php echo $doctor_list->pincode; ?>" type="text" placeholder="" class="form-control" onkeypress="return numeric()" maxlength="6">
                      </div>
                    </div>
                  
                  
                </div> 

                <div class="row col-md-12">
                     <div class="col-md-4"><div class="form-group">
                        <label for="mobile" class="col-form-label">MOBILE</label>
                          <input id="mobile" name="mobile" readonly="true" value="<?php echo $doctor_list->mobile; ?>" type="text" placeholder="" class="form-control" required="" onkeypress="return numeric()" maxlength="10">
                      </div>
                    </div>
                     <div class="col-md-4"><div class="form-group">
                         <label for="phone" class="col-form-label">Email</label>
                          <input  style="text-transform: lowercase;" readonly="true"  id="phone" name="email" value="<?php echo $doctor_list->email; ?>" type="email" placeholder="" class="form-control" required=""maxlength="35">
                                
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
<script type="text/javascript">
  $(document).ready(function(){
    $('.qualification').select2();
  });
</script>
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
