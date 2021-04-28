<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item"><a href="<?php echo base_url('Users');?>">USER</a></li>
          <li class="breadcrumb-item active"><a href="#">ADD</a></li>          
        </ol>
  </div>
</div>
              <!-- content start -->
<section class="main-content">
<div class="row">
  <div class="col-md-12">
    <!-- card start -->
    <div class="card">
      <!-- <div class="card-header card-default">Inline form</div> -->
       <div class="card-body">
          <div class="tabs">
            
              <div class="tab-content">
                  <div role="tabpanel" >
                    <form method="POST" action="<?php echo base_url('Users/user_update/'.$usetrs_list->user_id);?>" role="form">                                   
                        
                          <div class="row col-md-12">
                              <div class="col-md-4"><div class="form-group">
                                <label for="username" class="col-form-label">UserName</label>
                                  <input id="username" name="username" type="text" placeholder="" class="form-control" value="<?php echo $usetrs_list->username;?>">
                                </div>
                              </div>
                             <!--  <div class="col-md-4"><div class="form-group">
                                <label for="password" class="col-form-label">Password</label>
                                  <input id="password" name="password" type="password" placeholder="" class="form-control" required="">
                                </div>
                              </div> -->
                              
                          </div>
                          <div class="row col-md-12">
                          	<div class="col-md-4"><div class="form-group">
                                <label for="email_id" class="col-form-label">Email ID</label>
                                    <input id="email_id" name="email_id" type="text" placeholder="" class="form-control" value="<?php echo $usetrs_list->email_id;?>">
                                </div>
                              </div>

                               <div class="col-md-4"><div class="form-group">
                                <label for="user_type" class="col-form-label">USERTYPE</label>
                                    <input id="user_type" name="user_type" type="text" placeholder="" class="form-control" value="<?php echo $usetrs_list->user_type;?>">
									
									
									<select id="user_type" name="user_type" type="text" placeholder="" class="form-control">
                                       <!--<option value='0'>--select--</option> -->                                     
                                        <!--<option value="doctor" <?php if($usetrs_list->user_type=='doctor')echo "selected";?>>Doctor</option>-->
										<option value="employee" <?php if($usetrs_list->user_type=='employee')echo "selected";?>>Employee</option>
										<!--<option value="patient" <?php if($usetrs_list->user_type=='patient')echo "selected";?>>Patient</option>-->
                                     
                                    </select> 
									
									
                                </div>
                              </div>
                          </div>
                          <div class="row col-md-12">
                            <div class="col-md-4"><div class="form-group">
                              <label for="role_id" class="col-form-label">ROLE</label>
                                 <select id="role_id" name="role_id" type="text" placeholder="" class="form-control">
                                        <option>--select--</option>
                                        <?php foreach ($roles as $value) {

                                        	if($usetrs_list->role_id==$value->role_id){ ?>
                                        <option value="<?php echo $value->role_id;?>" selected><?php echo $value->role_name;?></option>
                                    <?php }else{ ?>
                                    	<option value="<?php echo $value->role_id;?>" ><?php echo $value->role_name;?></option>
                                      <?php }} ?>
                                    </select>
                              </div>
                            </div>
                            <div class="col-md-4"><div class="form-group">
                              <label for="profile_id" class="col-form-label">PROFILE</label>
                                <select id="profile_id" name="profile_id" type="text" placeholder="" class="form-control">
                                        <option>--select--</option>
                                        <?php foreach ($profile as $value) {
                                        	if($usetrs_list->profile_id==$value->profile_id){ ?>
                                        	?>
                                        <option value="<?php echo $value->profile_id;?>" selected><?php echo $value->profile_name;?></option>
                                      <?php }else{ ?>
                                      	<option value="<?php echo $value->profile_id;?>" selected><?php echo $value->profile_name;?></option>
                                      <?php }} ?>
                                    </select>                                         
                              </div>
                            </div>  
                                                         
                          </div> 
                          <div class="row col-md-12">
                            <div class="col-md-4"><div class="form-group">
                              <label for="reports_to" class="col-form-label">Reports To</label>
                                <select id="reports_to" name="reports_to" type="text" placeholder="" class="form-control">
                                        <option>--select--</option>
                                        <?php foreach ($reports_users as $value) { 
                                        	if($usetrs_list->reports_to==$value->user_id){
                                        	?>
                                        <option value="<?php echo $value->user_id;?>" selected><?php echo $value->username;?></option>
                                    <?php }else{ ?>
                                    	  <option value="<?php echo $value->user_id;?>" ><?php echo $value->username;?></option>
                                      <?php } } ?>
                                    </select>                                         
                              </div>
                            </div>  
                             <div class="col-md-4"><div class="form-group">
                              <label for="status" class="col-form-label">Status</label>
                                <select id="status" name="status" type="text" placeholder="" class="form-control">
                                        <option>--select--</option>
                                        
                                        <option value="1" selected>Active</option>
                                        <option value="0">InActive</option>
                                     
                                    </select>                                         
                              </div>
                            </div> 
                          </div>
                           <div class="col-md-6">
                              <div class="pull-right">
                                  <input type="submit" value="Save" name="submit" class="btn btn-success"> 
                              </div>
                          </div>
                      </form>
                  </div>
                  
          
              

          </div>   <!--Tab end -->
        </div>
      </div>
    </div>
  </div>
</section>
        