<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li>Settings&nbsp;<i class="fa fa-angle-right"></i></li>
            <li>Staff&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Add Employee </li>
        </ol>

        <div class="pull-right tPadding">
            <!-- <a href="<?= base_url('settings/staff'); ?>" class="btn btn-primary btn-icon"><i class="fas fa-backward"></i> Back</a> -->
        </div>
    </div>
</div>

<!-- content start -->
<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <!-- card start -->
            <div class="card">

                <div class="row page-title">
                    <div class="col-md-12">
                        New Employee
                    </div>
                </div>

                <div class="card-body">
                    <div class="tabs">
                        <div class="tab-content">
                            <div role="tabpanel" >       
                                <form method="POST" action="<?php echo base_url('Employee/employee_add');?>" role="form">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="first_name" class="col-form-label">First Name<span style="color: red;">*</span></label>
                                                <input style="text-transform: capitalize;" type="text" name="first_name" class="form-control" required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="last_name" class="col-form-label">Last Name<span style="color: red;">*</span></label>    
                                                <input style="text-transform: capitalize;" id="last_name" name="last_name" type="text" placeholder="" class="form-control" required="">
                                            </div>
                                        </div> 
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="gender" class="col-form-label">Gender<span style="color: red;">*</span></label>
                                                <select name="gender" id="gender" class="form-control" required="required">
                                                    <option>--Select Gender--</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="gender" class="col-form-label">Date of Birth</label>
                                                <input id="date_of_birth" name="date_of_birth" type="text" placeholder="" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">                                                                                
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="mobile" class="col-form-label">Mobile<span style="color: red;">*</span></label>
                                                <input type="text" onkeypress="return event.charCode >= 48 && event.charCode <= 57" maxlength="10" name="mobile" class="form-control" required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="first_name" class="col-form-label">Email<span style="color: red;">*</span></label>
                                                <input style="text-transform: lowercase;" type="email" name="email_id" class="form-control" required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="last_name" class="col-form-label">Address<span style="color: red;">*</span></label>    
                                                <textarea style="text-transform: capitalize;" rows="1" id="address" name="address" type="text" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="last_name" class="col-form-label">Password</label>    
                                                <div class="input-group mb-3">
                                                <input type="text" class="form-control" placeholder="Recipient's username" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button">Button</button>
                                                </div>
                                                </div>
                                                
                                            </div>
                                        </div> -->
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="title" class="col-form-label">Qualification<span style="color: red;">*</span></label>
                                                <input style="text-transform: capitalize;" id="title" name="qualification" type="text" placeholder="" class="form-control" required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="first_name" class="col-form-label">Adhaar No</label>
                                                <input type="text" name="adhaar_no" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="first_name" class="col-form-label">PAN No</label>
                                                <input style="text-transform: uppercase;" type="text" name="pan_no" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="first_name" class="col-form-label">Back Account No</label>
                                                <input type="text" name="bank_account_no" class="form-control" >
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="gender" class="col-form-label">Joining Date<span style="color: red;">*</span></label>
                                                <input id="date_of_joining" name="date_of_joining" type="text" placeholder="" class="form-control" required="">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="first_name" class="col-form-label">Designation<span style="color: red;">*</span></label>
                                                <select name="role_id" id="role_id" class="form-control" required="required"> 
                                                    <option value="">--Select Role--</option>
                                                    <?php foreach ($roles as $role) { 
                                                        if($role->role_name == "Patient")
                                                            continue;
                                                        ?>
                                                        <option value="<?php echo $role->role_id;?>"><?php echo $role->role_name;?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label">Assigned Roles</label>
                                                <select class="form-control assigned_roles" name="assigned_roles[]" multiple>
                                                    <option disabled>Select Roles</option>
                                                    <?php
                                                        if(count($clinic_roles) > 0){
                                                            foreach($clinic_roles as $val){
                                                                ?>
                                                                <option value="<?=$val->clinic_role_id?>"><?=$val->clinic_role_name?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?> 
                                                </select>
                                            </div>
                                        </div> 
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="col-form-label">Digital Signature</label>
                                                <input type="file" class="form-control" id="digital_sign" name="digital_sign">
                                            </div>
                                        </div>
                                        
                                        <!-- <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="first_name" class="col-form-label">Profile<span style="color: red;">*</span></label>
                                                <select name="profile_id" id="profile_id" class="form-control" required="required"> 
                                                    <option value="">--Select Profile--</option>
                                                    <?php foreach ($profiles as $profile) { ?>
                                                        <option value="<?php echo $profile->profile_id;?>"><?php echo $profile->profile_name;?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div> -->
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="pull-right">
                                                <input type="submit" value="Save" name="submit" class="btn customBtn">
                                            </div>
                                        </div>
                                    </div>
                                </form>                                
                            </div>     
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
$(document).ready(function(){
    $('.assigned_roles').select2();
})
</script>