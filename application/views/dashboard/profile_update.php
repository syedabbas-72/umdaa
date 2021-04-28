<div class="row">
    <div class="col-md-12">
        <div class="card">
           <div class="card-header card-default">
          <h3 class="box-title">Edit Profile</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <div class="card-body">
          <?php if(isset($msg) || validation_errors() !== ''): ?>
              <div class="alert alert-warning alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <h4><i class="icon fa fa-warning"></i> Alert!</h4>
                  <?= validation_errors();?>
                  <?= isset($msg)? $msg: ''; ?>
              </div>
            <?php endif; ?>

           
            <?php echo form_open(base_url('dashboard/profile_update'),'class="form-horizontal" enctype="multipart/form-data"');?> 
              <div class="form-group">
                <label for="employee_name"> Name</label>
                <?php if($this->session->userdata('user_type') == 'Manager'||$this->session->userdata('user_type') == 'marketing officer'||$this->session->userdata('user_type')  == 'Managing director'){ ?>
                  <input type="text" name="employee_name" value="<?= $user->employee_name;?>" class="form-control" id="employee_name" placeholder="">
                <?php } else { ?>
                  
                <input type="text" name="employee_name" value="<?= $user->employee_name;?>" class="form-control" id="employee_name" placeholder="">
             <?php } ?>
              </div>

              <div class="form-group">
                <label for="employee_name">Mobile</label>
                <?php if($this->session->userdata('user_type') == 'Manager'||$this->session->userdata('user_type') == 'marketing officer'||$this->session->userdata('user_type')  == 'Managing director'){ ?>
                  <input type="text" name="employee_name" value="<?= $user->mobile_number;?>" class="form-control" id="employee_name" placeholder="">
                <?php } else { ?>
                  
                <input type="text" name="employee_name" value="<?= $user->employee_name;?>" class="form-control" id="employee_name" placeholder="">
             <?php } ?>
              </div>

              <div class="form-group">
                <label for="employee_name">Email Id</label>
                <?php if($this->session->userdata('user_type') == 'Manager'||$this->session->userdata('user_type') == 'marketing officer'||$this->session->userdata('user_type')  == 'Managing director'){ ?>
                  <input type="text" name="employee_name" value="<?= $user->email_id;?>" class="form-control" id="employee_name" placeholder="">
                <?php } else { ?>
                  
                <input type="text" name="employee_name" value="<?= $user->employee_name;?>" class="form-control" id="employee_name" placeholder="">
             <?php } ?>
              </div>
              
      
               
               <div class="form-group">
                    <label>Image</label>
                    <div class="fileinput-new" data-provides="fileinput">
                      <div class="fileinput-preview" data-trigger="fileinput" style="width: 200px; height:150px;"><img style="width:100%;" src="<?php echo base_url('public/uploads/employee_images/'.$user->employee_image);?>" /></div>
                      <span class="btn btn-primary  btn-file">
                        <span class="fileinput-new">Choose Image</span>
                        <span class="fileinput-exists">Change</span>
                        <input type="hidden" value="<?= $user->employee_image; ?>" name="employee_img"><input type="file" id="image" name="employee_img">
                      </span>
                      <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">Remove</a>
                    </div>
                  </div>
              <!-- <a href="<?= base_url('employee/edit/'.$user->employee_id); ?>" class="btn btn-success btn-icon" name = "submit"><i class="fa fa-floppy-o "></i>Update</a> -->
              <input type = "submit" class= "btn btn-success btn-icon" name = "submit" value = "update"/>
              <a href="<?= base_url(''); ?>" class="btn btn-warning btn-icon"><i class="fa fa-reply"></i>Back</a>
            <?php echo form_close(); ?>
         
          <!-- /.box-body -->
      </div>
    </div>
  </div>  