<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">HOME</a></li>
          <li class="breadcrumb-item"><a href="#">CLINIC</a></li>
          <li class="breadcrumb-item active"><a href="#">EDIT</a></li>          
        </ol>
  </div>
</div>

        <section class="main-content">
            <div class="row">             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                         
                          <form method="POST" action="<?php echo base_url('clinic/clinic_update/'.$clinic_list->clinic_id);?>" enctype="multipart/form-data" role="form">
                             <div class="row col-md-12">
                            <label for="demographic" id="demo"> <u class="demo_under_line">CLINIC INFORMATION</u></label>
                          </div>
                            <div class="row col-md-12">
                                <div class="col-md-6"><div class="form-group">
                                    <label for="clinic_name" class="col-form-label">CLINIC NAME<span style="color:red;">*</span></label>
                                    <input id="clinic_name" name="clinic_name" value="<?php echo $clinic_list->clinic_name;?>" type="text" placeholder="" class="form-control-demo" required="">
                                </div></div>
								<!--
                                <div class="col-md-3"><div class="form-group">
                                    <label for="clinic_type" class="col-form-label">CLINIC TYPE<span style="color:red;">*</span></label>
                                        <input name="clinic_type" value="<?php echo $clinic_list->clinic_type;?>" id="clinic_type" class="form-control" required="">
                                    </div>
                                </div>
								-->
								
                                <div class="col-md-6"><div class="form-group">
                                    <label for="clinic_phone" class="col-form-label">CLINIC PHONE<span style="color:red;">*</span></label>    
                                    <input id="clinic_phone" name="clinic_phone" value="<?php echo $clinic_list->clinic_phone;?>" type="text" placeholder="" class="form-control-demo" required="">
                                </div>
                              </div>
                            </div> 
                            <div class="row col-md-12">
                               <div class="col-md-3"><div class="form-group">
                                   <label for="clinic_email" class="col-form-label">CLINIC E-MAIL<span style="color:red;">*</span></label>
                                      <input id="clinic_email" name="clinic_email" value="<?php echo $clinic_list->email;?>" type="text" placeholder="" class="form-control" required="">
                                  </div>
                              </div>
                              <div class="col-md-3"><div class="form-group">
                                    <label for="address" class="col-form-label">ADDRESS<span style="color:red;">*</span></label>                
                                    <input id="address" name="address" value="<?php echo $clinic_list->address;?>" type="text" placeholder="" class="form-control" required="">
                                  </div>
                                </div>
                              <div class="col-md-3"><div class="form-group">
                                    <label for="district" class="col-form-label">DISTRICT</label>
                                      <input name="district" value="<?php echo $clinic_list->district_name;?>" id="district" class="form-control">
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
                                      <input id="pincode" name="pincode" value="<?php echo $clinic_list->pincode;?>" type="text" placeholder="" class="form-control">
                                    </div>
                                </div>
								
								 <div class="col-md-4"><div class="form-group">
                                     <label for="pincode" class="col-form-label">CLINIC ALIAS<span style="color:red;">*</span></label>
                                      <input id="clinic_alias" name="clinic_alias" type="text" value="<?php echo $clinic_list->clinic_alias;?>" placeholder="" class="form-control" readonly required>
                                    </div>
                                </div>
								
                            </div>
                            <div class="row col-md-12">
                              <div class="col-md-4"><div class="form-group">
                                  <label>CLINIC LOGO</label>
                                  <div class="fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-preview" data-trigger="fileinput" style="width: 200px; height:150px;"><img style="width:100%;" src="<?php echo base_url('uploads/clinic_logos/'.$clinic_list->clinic_logo);?>" /></div>
                                    <span class="btn btn-primary  btn-file">
                                      <span class="fileinput-new">Choose Image</span>
                                      <span class="fileinput-exists">Change</span>
                                      <input type="hidden" value="<?= $clinic_list->clinic_logo;?>"><input type="file" name="clinic_logo" value="<?= $clinic_list->clinic_logo;?>">
                                    </span>
                                    <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">Remove</a>
                                  </div>
                              </div></div>
							  
							  
							   <div class="col-md-4"><div class="form-group">
                                  <label>CLINIC EMBLEM</label>
                                  <div class="fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-preview" data-trigger="fileinput" style="width: 200px; height:150px;"><img style="width:100%;" src="<?php echo base_url('uploads/clinic_logos/'.$clinic_list->clinic_emblem);?>" /></div>
                                    <span class="btn btn-primary  btn-file">
                                      <span class="fileinput-new">Choose Image</span>
                                      <span class="fileinput-exists">Change</span>
                                      <input type="hidden" value="<?= $clinic_list->clinic_logo;?>"><input type="file" name="clinic_emblem" value="<?= $clinic_list->clinic_emblem;?>">
                                    </span>
                                    <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">Remove</a>
                                  </div>
                              </div></div>
							  
							  
							  <div class="col-md-4"><div class="form-group">
                                  <label>CLINIC QRCODE</label>
                                  <div class="fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-preview" data-trigger="fileinput" style="width: 200px; height:150px;"><img style="width:100%;" src="<?php echo base_url('uploads/clinic_qrcode/'.$clinic_list->clinic_qrcode);?>" /></div>
                                    <span class="btn btn-primary  btn-file">
                                      <span class="fileinput-new">Choose Image</span>
                                      <span class="fileinput-exists">Change</span>
                                      <input type="hidden" value="<?= $clinic_list->clinic_qrcode;?>"><input type="file" name="clinic_qrcode" value="<?= $clinic_list->clinic_qrcode;?>">
                                    </span>
                                    <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">Remove</a>
                                  </div>
                              </div></div>
                            </div>
                          <div class="row col-md-12">
                            <label for="demographic" id="demo"> <u class="demo_under_line">CLINIC INCHARGE INFORMATION</u></label>
                          </div>
                            <div class="row col-md-12">
                                <div class="col-md-3"><div class="form-group">
                                  <label for="clinic_head" class="col-form-label">CLINIC INCHARGE<span style="color:red;">*</span></label>          
                                  <input id="clinic_head" name="clinic_incharge" value="<?php echo $clinic_list->incharge_name;?>" type="text" placeholder="" class="form-control" required="">
                                </div>
                                </div>
                                <div class="col-md-3"><div class="form-group">
                                     <label for="incharge_mobile" class="col-form-label">INCHARGE MOBILE<span style="color:red;">*</span></label>
                                      <input id="incharge_mobile" value="<?php echo $clinic_list->incharge_mobile;?>" name="incharge_mobile" type="text" placeholder="" class="form-control" required="">
                                    </div>
                                </div>
                                 <div class="col-md-3"><div class="form-group">
                                    <label for="incharge_email" class="col-form-label">E-MAIL<span style="color:red;">*</span></label>    
                                     <input id="incharge_email" value="<?php echo $clinic_list->incharge_email;?>" name="incharge_email" type="text" placeholder="" class="form-control" required="">
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
                    </div>
                </div>
            </div>
        </section>  


