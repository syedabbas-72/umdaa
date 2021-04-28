<style type="text/css">
	.avatar{
		width:150px;
		height: 150px;
		padding: 10px;
	}
</style>
 <div class="row">
                <div class="col-md-12">
                    <div class="card">
           <div class="card-header card-default">
          <h3 class="box-title">Profile Update</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <div class="card-body">

    <div class="row">
      <div class="col-sm-4"><!--left col-->
          <form class="form" action="<?php echo base_url('dashboard/profile_update')?>" method="post" id="registrationForm" enctype="multipart/form-data">
       <div class="form-group" style="text-align: center;">
       		<div style="padding: 20px;">
            <?php if(isset($users->employee_image)){?>
       				<img src="<?php echo base_url('public/uploads/employee_images/'.$users->employee_image);?>" class="avatar img-circle img-thumbnail" alt="avatar">
            <?php } else {?>
              <img src="<?php echo base_url('public/uploads/employee_images/'.$users->employee_image);?>" class="avatar img-circle img-thumbnail" alt="avatar">>
            <?php }?>
       				</div>
                  
                    <div class="fileinput input-group fileinput-new" data-provides="fileinput">
                        <div class="form-control" data-trigger="fileinput"><span class="fileinput-filename"></span></div>
                        <span class="input-group-addon btn btn-primary btn-file ">
                        <span class="fileinput-new">Select</span>
                        <span class="fileinput-exists">Change</span>
                        <input type="hidden" value="" name="customer_img"><input type="file" name="customer_img">
                        </span>
                        <a href="#" class="input-group-addon btn btn-danger  hover fileinput-exists" data-dismiss="fileinput">Remove</a>
                    </div>
                  </div>
        </div><!--/col-3-->
      <div class="col-sm-6">
            
                      <div class="form-group">
                          <div class="col-xs-6">
                              <label for="first_name"><h4>Name</h4></label>
                              <?php if(isset($users->employee_name)){?>
                              <input type="text" class="form-control" name="first_name" value ="<?php echo $users->employee_name;?>">
                            <?php } else {?>
                               <input type="text" class="form-control" name="first_name" value = "">
                             <?php }?>
                          </div>
                      </div>
                    
                      <div class="form-group">
                          <div class="col-xs-6">
                             <label for="mobile"><h4>Mobile</h4></label>
                              <?php if(isset($users->mobile_number)){?>
                              <input type="text" class="form-control" name="mobile" value ="<?php echo $users->mobile_number;?>">
                            <?php } else {?>
                               <input type="text" class="form-control" name="mobile" value =>
                             <?php }?>
                          </div>
                      </div>
                      <div class="form-group">
                          
                          <div class="col-xs-6">
                              <label for="email"><h4>Email</h4></label>
                             <?php if(isset($users->email_id)){?>
                              <input type="text" class="form-control" name="email" value ="<?php echo $users->email_id;?>">
                            <?php } else {?>
                               <input type="text" class="form-control" name="email" value =>
                             <?php }?>
                          </div>
                      </div>

                      <div class="col-xs-6">
                              <label for="password"><h4>Password</h4></label>
                             <?php if(isset($users->password)){?>
                              <input type="password" class="form-control" name="password" value ="<?php echo base64_decode($users->password);?>">
                            <?php } else {?>
                               <input type="password" class="form-control" name="password" value =>
                             <?php }?>
                          </div>
                      
                     
                      <div class="form-group">
                           <div class="col-xs-12">
                                <br>
                                <input class="btn btn-lg btn-success" value="update" name="submit" type="submit">
                            </div>
                      </div>
                      </div>
              
          </div>
        </div><!--/col-9-->
    </div><!--/row-->
</form>

      </div>
    </div>
  </div>  
</div>