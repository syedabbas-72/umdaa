<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li><a href="<?= base_url('settings'); ?>">Settings</a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li><a href="<?= base_url('settings/staff'); ?>">Staff</a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Edit Employee</li>
        </ol>

        <div class="pull-right tPadding">
            <!-- <a href="<?= base_url('settings/staff'); ?>" class="btn btn-primary btn-icon"><i class="fas fa-backward"></i> Back</a> -->
        </div>
    </div>
</div>

<?php if($this->session->flashdata('msg')): ?>
<p><?php echo $this->session->flashdata('msg'); ?></p>
<?php endif; ?>

<!-- content start -->
<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <!-- card start -->
            <div class="card">

                <div class="row page-title">
                    <div class="col-md-12">
                        Edit Employee
                    </div>
                </div>
                <?php
                $assigned = explode(",", $employee_info->assigned_roles);
                ?>
                <div class="card-body">
                    <div class="tabs">
                        <div class="tab-content">
                            <div role="tabpanel">
                                <form method="POST" action="<?php echo base_url('employee/employee_update/'.$employee_info->employee_id);?>" enctype="multipart/form-data"  role="form" class="form">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="employee_code" class="col-form-label">Employee ID<span style="color: red;">*</span></label>
                                                <input id="employee_code" name="employee_code" value="<?php echo $employee_info->employee_code; ?>" type="text" placeholder="" class="form-control" disabled="disabled">
                                            </div>
                                        </div>
                                    
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="first_name" class="col-form-label">Username</label>
                                                <input type="text" style="text-transform: capitalize;" readonly value="<?php echo ucfirst($employee_info->username);?>" class="form-control" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <!-- Hidden Text -->
                                        <input type="hidden" class="form-control" name="back_url" value=<?=$_SERVER['HTTP_REFERER']?>>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="first_name" class="col-form-label">First Name<span style="color: red;">*</span></label>
                                                <input type="text" style="text-transform: capitalize;" name="first_name" value="<?php echo ucfirst($employee_info->first_name);?>" class="form-control" required="">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="last_name" class="col-form-label">Last Name</label>    
                                                <input id="last_name" style="text-transform: capitalize;" name="last_name" value="<?php echo ucfirst($employee_info->last_name);?>" type="text" placeholder="" class="form-control" >
                                            </div>
                                        </div> 
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="gender" class="col-form-label">Gender<span style="color: red;">*</span></label>
                                                <select name="gender" id="gender" class="form-control" required="required">
                                                    <option>--Select Gender--</option>
                                                    <option value="Male"<?php if($employee_info->gender=="Male"){echo "selected";} ?>>Male</option>
                                                    <option value="Female"<?php if($employee_info->gender=="Female"){echo "selected";} ?>>Female</option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="gender" class="col-form-label">Date of Birth</label>
                                                <input id="date_of_birth" name="date_of_birth" value="<?php echo date("d-m-Y",strtotime($employee_info->date_of_birth)); ?>" type="text" placeholder="" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="mobile" class="col-form-label">Mobile<span style="color: red;">*</span></label>
                                                <input type="text" name="mobile" value="<?=$employee_info->mobile?>" <?=($employee_info->mobile == "")?'':'readonly'?> class="form-control" required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="first_name" class="col-form-label">Email<span style="color: red;">*</span></label>
                                                <input style="text-transform: lowercase;" type="email" <?=($employee_info->email_id == "")?'':'readonly'?> name="email_id" value="<?=$employee_info->email_id?>" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="last_name" class="col-form-label">Address<span style="color: red;">*</span></label>    
                                                <textarea style="text-transform: capitalize;" id="address" name="address" rows="1" class="form-control"><?php echo $employee_info->address;?></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="qualification" class="col-form-label">Qualification<span style="color: red;">*</span></label>
                                                <input style="text-transform: capitalize;" id="qualification" name="qualification" value="<?php echo ucfirst($employee_info->qualification);?>" type="text" placeholder="" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="first_name" class="col-form-label">Adhaar No</label>
                                                <input type="text" name="adhaar_no" class="form-control" value="<?php echo $employee_info->adhaar_no;?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="first_name" class="col-form-label">PAN No</label>
                                                <input style="text-transform: uppercase;" type="text" name="pan_no" class="form-control" value="<?php echo strtoupper($employee_info->pan_no);?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="first_name" class="col-form-label">Back Account No</label>
                                                <input type="text" name="bank_account_no" class="form-control" value="<?php echo $employee_info->bank_account_no;?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="date_of_joining" class="col-form-label">Date of Join<span style="color: red;">*</span></label>
                                                <input id="date_of_joining" name="date_of_joining" value="<?php echo date("d-m-Y", strtotime($employee_info->date_of_joining)); ?>" type="text" placeholder="" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-form-label" for="assigned_roles">Assigned Roles</label>
                                                <select class="form-control assigned_roles" id="assigned_roles" name="assigned_roles[]" multiple="multiple">
                                                    <option  disabled>Select Roles</option>
                                                    <?php
                                                    if(count($clinic_roles) > 0){
                                                        foreach($clinic_roles as $val){
                                                            ?>
                                                            <option value="<?=$val->clinic_role_id?>" <?=(in_array($val->clinic_role_id, $assigned))?'selected':''?>><?=$val->clinic_role_name?></option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="role_id" class="col-form-label">Designation<span style="color: red;">*</span></label>
                                                <select name="role_id" id="role_id" class="form-control" required="required"> 
                                                    <option value="">--Select Role--</option>
                                                    <?php foreach ($roles as $role) { 
                                                        if($role->role_name=="Patient")
                                                            continue;
                                                        ?>
                                                        <option value="<?php echo $role->role_id;?>" <?=($employee_info->role_id==$role->role_id)?'selected':''?>><?php echo $role->role_name;?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>                
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="first_name" class="col-form-label">Profile<span style="color: red;">*</span></label>
                                                <select name="profile_id" id="profile_id" class="form-control" required="required"> 
                                                    <option value="">--Select Profile--</option>
                                                    <?php foreach ($profiles as $profile) { 
                                                        if($profile->profile_name=="Patient")
                                                            continue;
                                                        ?>
                                                        <option value="<?php echo $profile->profile_id;?>" <?=($employee_info->profile_id==$profile->profile_id)?'selected':''?>><?php echo $profile->profile_name;?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>                 -->
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Digital Signature <span class="mandatory">*</span></label>
                                                <div class="fileinput-new" data-provides="fileinput" style="padding-top:0px !important; padding-left: 0px !important; padding-bottom: 0px !important">
                                                    <?php if(isset($employee_info->digital_sign) != ''){ ?>
                                                    <div class="fileinput-preview text-center" data-trigger="fileinput" style="padding-left: 0px !important; padding-top:0px !important;"><img style="width:100%; border:1px solid #ccc; background: #f3f3f3; margin-bottom: 5px" src="<?php echo base_url('uploads/digital_sign/'.$employee_info->digital_sign);?>" /></div>
                                                    <?php }else if(isset($employee_info->digital_sign) != ''){ ?>
                                                        <div class="fileinput-preview text-center" data-trigger="fileinput" style="padding-left: 0px !important; padding-top:0px !important;"><img style="width:100%; border:1px solid #ccc; background: #f3f3f3; margin-bottom: 5px" src="<?php echo base_url('uploads/digital_sign/'.$employee_info->digital_sign);?>" /></div>
                                                    <?php } ?>
                                                    <input type="file" id="digital_sign" name="digital_sign" value="<?= $employee_info->digital_sign; ?>" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <input type="submit" value="Save" name="submit" class="btn customBtn">
                                                <!-- <a href="<?=base_url('settings/staff')?>" class="btn btn-danger mt-1">Cancel</a> -->
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