<div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?php echo $_SESSION['clinic_name']; ?></div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                              
                                <li class="active">Clinic Settings</li>
                            </ol>
                        </div>
                    </div>


            <div class="row">
              <div class="col-2">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <a class="nav-link active" href="#practice_details" data-toggle="tab">Practice Details</a>
                <a class="nav-link" href="#staff" data-toggle="tab">Practice Staff</a>                  
            
            </div>
        </div>
                        <div class="col-10">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="tab-content">
                                <div class="tab-pane active" id="practice_details">
                         
                          <form method="POST" action="<?php echo base_url('clinic/clinic_update/'.$clinic_list->clinic_id);?>" enctype="multipart/form-data" role="form">
                             <div class="row col-md-12">
                          
      <div class="card" style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
    <div class="card-header" style="font-size: 15px;padding: 10px 20px">CLINIC INFORMATION</div>
  </div>

                          </div>
                            <div class="row col-md-12">
                                <div class="col-md-6"><div class="form-group">
                                    <label for="clinic_name" class="col-form-label">CLINIC NAME<span style="color:red;">*</span></label>
                                    <input id="clinic_name" name="clinic_name" value="<?php echo $clinic_list->clinic_name;?>" type="text" placeholder="" class="form-control input-height" required="">
                                </div></div>

								
                                <div class="col-md-6"><div class="form-group">
                                    <label for="clinic_phone" class="col-form-label">CLINIC PHONE<span style="color:red;">*</span></label>    
                                    <input id="clinic_phone" name="clinic_phone" value="<?php echo $clinic_list->clinic_phone;?>" type="text" placeholder="" class="form-control input-height" required="">
                                </div>
                              </div>
                            </div> 
                            <div class="row col-md-12">
                               <div class="col-md-3"><div class="form-group">
                                   <label for="clinic_email" class="col-form-label">CLINIC E-MAIL<span style="color:red;">*</span></label>
                                      <input id="clinic_email" name="clinic_email" value="<?php echo $clinic_list->email;?>" type="text" placeholder="" class="form-control input-height" required="">
                                  </div>
                              </div>
                              <div class="col-md-3"><div class="form-group">
                                    <label for="address" class="col-form-label">ADDRESS<span style="color:red;">*</span></label>                
                                    <input id="address" name="address" value="<?php echo $clinic_list->address;?>" type="text" placeholder="" class="form-control " required="">
                                  </div>
                                </div>
                              <div class="col-md-3"><div class="form-group">
                                    <label for="district" class="col-form-label">DISTRICT</label>
                                      <input name="district" value="<?php echo $clinic_list->district_name;?>" id="district" class="form-control input-height">
                                    </div>
                                </div>
                            </div> 
                            <div class="row col-md-12">
                              
                                <div class="col-md-3"><div class="form-group">
                                    <label for="state" class="col-form-label">STATE<span style="color:red;">*</span></label>
                                      <select name="state" id="state" class="form-control" required="">
                                        <option>--select state--</option>
                                        <?php foreach ($state_list as $val) { ?>
                                        <option value="<?php echo $val->state_id;?>"<?php if($val->state_id==$clinic_list->state_id){echo "selected";}?>><?php echo $val->state_name;?></option>
                                        <?php } ?>
                                      </select>
                                  </div>
                                </div>
                                 <div class="col-md-3"><div class="form-group">
                                     <label for="pincode" class="col-form-label">PIN CODE</label>
                                      <input id="pincode" name="pincode" value="<?php echo $clinic_list->pincode;?>" type="text" placeholder="" class="form-control input-height">
                                    </div>
                                </div>
								
								 <div class="col-md-4"><div class="form-group">
                                     <label for="pincode" class="col-form-label">CLINIC ALIAS<span style="color:red;">*</span></label>
                                      <input id="clinic_alias" name="clinic_alias" type="text" value="<?php echo $clinic_list->clinic_alias;?>" placeholder="" class="form-control input-height" readonly required>
                                    </div>
                                </div>
								
                            </div>
                            <div class="row col-md-12">
                              <div class="col-md-4"><div class="form-group">
                                  <label>CLINIC LOGO</label>
                                  
                                  <div class="compose-editor">
                                    <div class="fileinput-preview" data-trigger="fileinput" style="width: 200px; height:150px;"><img style="width:100%;" src="<?php echo base_url('uploads/clinic_logos/'.$clinic_list->clinic_logo);?>" /></div>
                                                  <input type="file" name="clinic_logo" value="<?= $clinic_list->clinic_logo;?>" class="default" multiple="">
                                              </div>
                              </div></div>
							  
							  
							  
                            </div>

                          <div class="row col-md-12">
         
                    <div class="col-md-4"><div class="form-group">
                      <label for="registration_fee" class="col-form-label">REGISTRATION FEE<span style="color: red;">*</span></label>
                          <input id="registration_fee" name="registration_fee" type="text" placeholder="" class="form-control input-height" required="" value="<?php echo $clinic_list->registration_fee;?>">
                      </div>
                    </div>
                    <div class="col-md-4"><div class="form-group">
                      <label for="pharmacy_discount" class="col-form-label">PHARMACY DISCOUNT(%)<span style="color: red;">*</span></label>
                          <input id="pharmacy_discount" name="pharmacy_discount" type="text" placeholder="" class="form-control input-height" required="" value="<?php echo $clinic_list->pharmacy_discount;?>">
                      </div>
                    </div>
                   
                   
                    <div class="col-md-4"><div class="form-group">
                      <label for="lab_discount" class="col-form-label">LAB DISCOUNT(%)<span style="color: red;">*</span></label>
                        <input id="lab_discount" name="lab_discount" type="text" placeholder="" class="form-control input-height" required="" value="<?php echo $clinic_list->lab_discount;?>">
                      </div>
                    </div>
                  </div> 
                          <div class="row col-md-12">
                             <div class="card" style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
    <div class="card-header" style="font-size: 15px;padding: 10px 20px">CLINIC INCHARGE INFORMATION</div>
  </div>
                          </div>
                            <div class="row col-md-12">
                                <div class="col-md-3"><div class="form-group">
                                  <label for="clinic_head" class="col-form-label">CLINIC INCHARGE<span style="color:red;">*</span></label>          
                                  <input id="clinic_head" name="clinic_incharge" value="<?php echo $clinic_list->incharge_name;?>" type="text" placeholder="" class="form-control input-height" required="">
                                </div>
                                </div>
                                <div class="col-md-3"><div class="form-group">
                                     <label for="incharge_mobile" class="col-form-label">INCHARGE MOBILE<span style="color:red;">*</span></label>
                                      <input id="incharge_mobile" value="<?php echo $clinic_list->incharge_mobile;?>" name="incharge_mobile" type="text" placeholder="" class="form-control input-height" required="">
                                    </div>
                                </div>
                                 <div class="col-md-3"><div class="form-group">
                                    <label for="incharge_email" class="col-form-label">E-MAIL<span style="color:red;">*</span></label>    
                                     <input id="incharge_email" value="<?php echo $clinic_list->incharge_email;?>" name="incharge_email" type="text" placeholder="" class="form-control input-height" required="">
                                    </div>
                                </div>
                            </div> 
                            <div class="row col-md-12">
                          
      <div class="card" style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
    <div class="card-header" style="font-size: 15px;padding: 10px 20px">FACILITIES PROVIDED</div>
  </div>

                          </div>
                                  <div class="row col-md-12">
                  <div class="form-group col-md-12">
                 
                     <div class="form-inline">

                                           <div class="checkbox checkbox-inline checkbox-aqua">
                          <input id="checkboxbg4" type="checkbox" name="facilities[]" checked="" value="pharmacy">
                          <label for="checkboxbg4"> Pharmacy </label>
                        </div>
                                            <div class="checkbox checkbox-inline checkbox-success mx-sm-3">
                          <input id="checkbox16" name="facilities[]" type="checkbox"  value="lab">
                          <label for="checkbox16"> Lab </label>
                        </div>
                                            <div class="checkbox checkbox-inline checkbox-success mx-sm-3">
                          <input id="checkbox17" name="facilities[]" type="checkbox"  value="radiology">
                          <label for="checkbox17"> Radialogy </label>
                        </div>
                         <div class="checkbox checkbox-inline checkbox-success mx-sm-3">
                          <input id="checkbox18" name="facilities[]" type="checkbox"  value="parking">
                          <label for="checkbox18"> Parking </label>
                        </div>
                         <div class="checkbox checkbox-inline checkbox-success mx-sm-3">
                          <input id="checkbox19" name="facilities[]" type="checkbox" value="valetparking">
                          <label for="checkbox19"> Valet Parking </label>
                        </div>
                         <div class="checkbox checkbox-inline checkbox-success mx-sm-3">
                          <input id="checkbox20" name="facilities[]" type="checkbox"  value="lift">
                          <label for="checkbox20"> Lift if not on ground floor </label>
                        </div>
                        
                                      </div>
                  </div>
                                  </div>
                                <div class="col-sm-6">
                                  <div class="pull-right">
                                      <input type="submit" value="Save" name="submit" class="btn btn-success btn-rounded box-shadow">
                                  </div>
                                </div>

                                    <!-- <div class="col-sm-6">
                                        <div class="pull-left">
                                            <button type="submit" class="btn  btn-gray btn-rounded btn-border btn-sm">
                                                <i class=""></i> Cancel
                                            </button>
                                        </div>
                                    </div> -->
                                  
                                </form>
                         </div>
                                <div class="tab-pane" id="staff">

                                    

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



