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
          <li class="breadcrumb-item active"><a href="#">DOCTOR SETTINGS</a></li>          
        </ol>
  </div>
</div>

        <section class="main-content">
          <div class="row">
        <div class="col-2">
           <?php $this->view("doctor_settings/left_nav"); ?>
        </div>
        <div class="col-10">
               <div class="card">
                <div class="card-body">
                     <form method="POST" action="<?php echo base_url('doctor_settings/user_add/'.$clinic_id.'/'.$doctor_id);?>" role="form">
                            <div class="row col-md-12">
                               
                                <div class="col-md-4"><div class="form-group">
                                    <label for="first_name" class="col-form-label">First Name<span style="color: red;">*</span></label>
                                        <input type="text" name="first_name" class="form-control-demo" required="required">
                                    </div>
                                </div>
                                <div class="col-md-4"><div class="form-group">
                                    <label for="last_name" class="col-form-label">Last Name<span style="color: red;">*</span></label>    
                                    <input id="last_name" name="last_name" type="text" placeholder="" class="form-control-demo" required="">
                                </div>
                              </div> 
                              <div class="col-md-4"><div class="form-group">
                                    <label for="gender" class="col-form-label">Gender<span style="color: red;">*</span></label>
                                   <select name="gender" id="gender" class="form-control" required="required">
                      <option>--Select Gender--</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        
                      </select>
                                </div></div>
                            </div>
              
              <div class="row col-md-12">
                                
                                <div class="col-md-4">
                <div class="form-group">
                <label for="gender" class="col-form-label">Date of Birth</label>
                    <input id="date_of_birth" name="date_of_birth" type="text" placeholder="" class="form-control">
                  </div>
                                </div>
                                <div class="col-md-4">
                <div class="form-group">
                <label for="gender" class="col-form-label">Date of Join<span style="color: red;">*</span></label>
                    <input id="date_of_joining" name="date_of_joining" type="text" placeholder="" class="form-control" required="">
                  </div>
                              </div> 
                              <div class="col-md-4"><div class="form-group">
                                    <label for="title" class="col-form-label">Qualification<span style="color: red;">*</span></label>
                                    <input id="title" name="qualification" type="text" placeholder="" class="form-control-demo" required="required">
                                </div></div>
                            </div>
              
              <div class="row col-md-12">
                                
                                <div class="col-md-4"><div class="form-group">
                                    <label for="mobile" class="col-form-label">Mobile<span style="color: red;">*</span></label>
                                        <input type="text" onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="10" name="mobile" class="form-control-demo" required="required">
                                    </div>
                                </div>
                                 <div class="col-md-4"><div class="form-group">
                                    <label for="first_name" class="col-form-label">Email<span style="color: red;">*</span></label>
                                        <input type="email" name="email_id" class="form-control-demo" required="required">
                                    </div>
                                </div>

                            
                            </div>

                            <div class="row col-md-12">
                               <div class="col-md-4"><div class="form-group">
                                    <label for="first_name" class="col-form-label">Adhaar No</label>
                                        <input type="text" name="adhaar_no" class="form-control-demo">
                                    </div>
                                </div>
                                <div class="col-md-4"><div class="form-group">
                                    <label for="first_name" class="col-form-label">PAN No</label>
                                        <input type="text" name="pan_no" class="form-control-demo">
                                    </div>
                                </div>
                                <div class="col-md-4"><div class="form-group">
                                    <label for="first_name" class="col-form-label">Back Account No</label>
                                        <input type="text" name="bank_account_no" class="form-control-demo" >
                                    </div>
                                </div>

                            </div>
              
              <div class="row col-md-12">
                               

                                <div class="col-md-6"><div class="form-group">
                                    <label for="last_name" class="col-form-label">Address<span style="color: red;">*</span></label>    
                                    <textarea style="height: 150px" id="address" name="address" type="text" class="form-control-demo"></textarea>
                                </div>
                              </div> 
              </div>
              
            

                                <div class="col-sm-6">
                                        <div class="pull-right">
                                            <input type="submit" value="Save" name="submit" class="btn btn-warning btn-rounded box-shadow">
                                        </div>
                                    </div>
                                </form>
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