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
          <li class="breadcrumb-item"><a href="<?php echo base_url('doctor_settings'); ?>">DOCTOR SETTINGS</a></li>  
           <li class="breadcrumb-item active"><a href="#">CREATE NEW CLINIC</a></li>            
        </ol>
  </div>
</div>

        <section class="main-content">
          <div class="row">
       
        <div class="col-12">
           <div class="card">
                <div class="card-body">
                  <form method="POST" action="<?php echo base_url('doctor_settings/create_clinic'); ?>" enctype="multipart/form-data" role="form">
                             <div class="row col-md-12">
                          
    

                          </div>
                            <div class="row col-md-12">
                                <div class="col-md-6"><div class="form-group">
                                    <label for="clinic_name" class="col-form-label">CLINIC NAME<span style="color:red;">*</span></label>
                                    <input id="clinic_name" name="clinic_name" value="" type="text" placeholder="" class="form-control-demo" required="">
                                </div></div>
            
                                <div class="col-md-6"><div class="form-group">
                                    <label for="clinic_phone" class="col-form-label">CLINIC PHONE<span style="color:red;">*</span></label>    
                                    <input id="clinic_phone" name="clinic_phone" value="" type="text" placeholder="" class="form-control-demo" required="">
                                </div>
                              </div>
                            </div> 
                
                           
                            <div class="row col-md-12">
                              <div class="col-md-4"><div class="form-group">
                                  <label>CLINIC LOGO</label>
                                  <div class="fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-preview" data-trigger="fileinput" style="width: 200px; height:150px;"><img style="width:100%;" src=""></div>
                                    <span class="btn btn-primary  btn-file">
                                      <span class="fileinput-new">Choose Image</span>
                                    
                                      <input type="hidden" value=""><input type="file" name="clinic_logo" value="">
                                    </span>
                                   
                                  </div>
                              </div></div>
                
                              <div class="col-md-6"><div class="form-group">
                                    <label for="address" class="col-form-label">ADDRESS</label>                
                                    <textarea style="height:150px" id="address" name="address" placeholder="" class="form-control"></textarea>
                                  </div>
                                </div>
                
                            </div>

                          <div class="row col-md-12">
         
                    <div class="col-md-4"><div class="form-group">
                      <label for="registration_fee" class="col-form-label">REGISTRATION FEE<span style="color: red;">*</span></label>
                          <input id="registration_fee" name="registration_fee" type="text" placeholder="" class="form-control" required="" value="">
                      </div>
                    </div>
                    <div class="col-md-4"><div class="form-group">
                      <label for="pharmacy_discount" class="col-form-label">MAX PHARMACY DISCOUNT(%)<span style="color: red;">*</span></label>
                          <input id="pharmacy_discount" name="pharmacy_discount" type="text" placeholder="" class="form-control" required="" value="">
                      </div>
                    </div>
                   
                   
                    <div class="col-md-4"><div class="form-group">
                      <label for="lab_discount" class="col-form-label">MAX LAB DISCOUNT(%)<span style="color: red;">*</span></label>
                        <input id="lab_discount" name="lab_discount" type="text" placeholder="" class="form-control" required="" value="">
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
                                  <input id="clinic_head" name="clinic_incharge" value="" type="text" placeholder="" class="form-control" required="">
                                </div>
                                </div>
                                <div class="col-md-3"><div class="form-group">
                                     <label for="incharge_mobile" class="col-form-label">INCHARGE MOBILE<span style="color:red;">*</span></label>
                                      <input id="incharge_mobile" value="" name="incharge_mobile" type="text" placeholder="" class="form-control" required="">
                                    </div>
                                </div>
                                 <div class="col-md-3"><div class="form-group">
                                    <label for="incharge_email" class="col-form-label">E-MAIL<span style="color:red;">*</span></label>    
                                     <input id="incharge_email" value="" name="incharge_email" type="text" placeholder="" class="form-control" required="">
                                    </div>
                                </div>
                            </div> 
                          
                               
                                 
                                <div class="col-md-6" style="margin-top: 20px">
                                  <div class="pull-right">
                                      <input type="submit" value="Save" name="submit" class="btn btn-success btn-rounded box-shadow">
                                  </div>
                                </div>

                                   
                                </form>
                                    </div>

                          
                                    

                         
                                
    
                                
                                <!-- /.tab-pane -->
                            </div>
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
<script type="text/javascript">
  $(document).on("click","#checkbox15",function(){
    if($(this).is(':checked')){
      $("#gstn_div").show();
    }
    else{
      $("#gstn_div").hide();
    }
  });
</script>