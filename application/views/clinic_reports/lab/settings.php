<?php
// get CRUD info
$crudInfo = getcrudInfo('Lab/settings');
?>

<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li>Lab&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Settings</li>
        </ol>
    </div>
</div>

<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card"> 

                <div class="row page-title">
                    <div class="col-md-12">
                        Lab &nbsp; <span><i class="fas fa-caret-right"></i></span> &nbsp; Settings
                    </div>
                </div>

                <div class="card-body">

                    <!-- <pre><?php print_r($lab_info);?></pre> -->
                    <form method="POST" action="<?php echo base_url('Lab/settings');?>" enctype="multipart/form-data" role="form" class="customForm">

                        <!-- Sub header in the form -->
                        <div class="row text-center docInfoHdr">
                            <div class="col-md-6 text-left">
                                Lab Information
                            </div>
                        </div>

                        <div class="row col-md-12">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="pharmacy_name">Lab Name&nbsp;<span style="color:red;">*</span></label>
                                    <input type="text" class="form-control" id="lab_name" placeholder="" name="name" value="<?php echo $lab_info->name;?>" required="">
                                    <input type="hidden" class="form-control" name="clinic_lab_id" value="<?php echo $lab_info->clinic_lab_id;?>" required="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="mobile_tb">Phone / Mobile&nbsp;<span style="color:red;">*</span></label> 
                                    <input id="mobile_tb" pattern="[0-9]{1}[0-9]{9}" maxlength="10" name="mobile" value="<?php echo $lab_info->mobile;?>" type="text" placeholder="" class="form-control" required="" onkeypress="return numeric();">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="lab_email" class="">Email&nbsp;<span style="color:red;">*</span></label>
                                    <input type="email" id="lab_email" name="email" value="<?php echo $lab_info->email;?>" type="text" placeholder="" class="form-control" required="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="gst_number_tb" class="">GST Number</label>
                                    <input type="text" id="gst_number_tb" name="gst_number" value="<?php echo $lab_info->gst_number;?>" type="text" placeholder="" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row col-md-12">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="max_discount_tb" class="">Discount (in %)&nbsp;<span style="color:red;">*</span></label>
                                    <input id="max_discount_tb" name="max_discount" value="<?php echo $lab_info->max_discount;?>" maxlength="2" max="99" type="text" placeholder="" class="form-control" required="" onkeypress="return numeric();">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="referral_doctor_max_discount_tb" class="">Ref. Doc. Discount (in %)&nbsp;<span style="color:red;">*</span></label>
                                    <input id="referral_doctor_max_discount_tb" name="referral_doctor_max_discount" value="<?php echo $lab_info->referral_doctor_max_discount;?>" maxlength="2" max="99" type="text" placeholder="" class="form-control" required="" onkeypress="return numeric();">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="min_advance_tb" class="">Advance (in %)&nbsp;<span style="color:red;">*</span></label>
                                    <input id="min_advance_tb" name="min_advance" value="<?php echo $lab_info->min_advance;?>" maxlength="2" max="99" type="text" placeholder="" class="form-control" required="" onkeypress="return numeric();">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address_ta" class="">Address</label>                
                                    <textarea cols="5" rows="1" id="address_ta" name="address" placeholder="" class="form-control"><?php echo $lab_info->address;?></textarea>
                                </div>
                            </div>                                                     
                        </div>

                        <div class="row col-md-12">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Lab Logo</label>
                                    <div class="fileinput-new" data-provides="fileinput" style="padding-top:0px !important; padding-left: 0px !important; padding-bottom: 0px !important">
                                        <?php if(isset($lab_info->logo) != ''){ ?>
                                        <div class="fileinput-preview text-center" data-trigger="fileinput" style="padding-left: 0px !important; padding-top:0px !important;"><img style="width:100%; border:1px solid #ccc; background: #f3f3f3; margin-bottom: 5px" src="<?php echo base_url('uploads/lab_logos/'.$lab_info->logo);?>" /></div>
                                        <?php }else if(isset($lab_info->clinic_logo) != ''){ ?>
                                            <div class="fileinput-preview text-center" data-trigger="fileinput" style="padding-left: 0px !important; padding-top:0px !important;"><img style="width:100%; border:1px solid #ccc; background: #f3f3f3; margin-bottom: 5px" src="<?php echo base_url('uploads/clinic_logos/'.$lab_info->clinic_logo);?>" /></div>
                                        <?php } ?>
                                        <input type="file" id="logo" name="logo" value="<?= $lab_info->logo; ?>" class="form-control">
                                        <input type="file" class="custom-file-input" id="inputGroupFile01"
                                        aria-describedby="logoUpload" name="clinic_logo" accept="image/x-png,image/gif,image/jpeg">
                                        <br>
                                        <input type="submit" value="Save Changes" name="submit" class="customBtn">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sub header in the form -->
                        <div class="row text-center docInfoHdr">
                            <div class="col-md-6 text-left">
                                Lab Staff Members
                            </div>
                            <div class="col-md-6 col-xs-6 cols-sm-6 text-right">
                                        <a href = "<?php echo base_url('lab/lab_employee')?>" class = "btn btn-primary pull-right" style="margin-top: 10px;">Add</a>
                                    </div>
                        </div>

                        <div class="row col-md-12">
                            <table cellpadding="0" cellspacing="0" class="customTable">
                                <tr>
                                    <th style="width: 10%">S.No.</th>
                                    <th style="width: 25%">Member Name</th>
                                    <th style="width: 15%">Designation</th>
                                    <th style="width: 15%">Work Profile</th>
                                    <th style="width: 20%" class="text-center">Digital Sign</th>
                                    <th style="width: 10%" class="text-center">Actions</th>
                                </tr>
                                <?php if(count($staff) > 0){  $i=1; ?>
                                    <?php foreach($staff as $member){ ?>
                                        <tr>
                                            <td><?=$i?>. </td>
                                            <td><?=ucwords($member['first_name']." ".$member['last_name'])?></td>
                                            <td><?=ucwords($member['designation'])?></td>
                                            <td><?=ucwords($member['profile'])?></td>
                                            <td class="text-center">
                                                <?php if($member['digital_sign'] != '') { ?>
                                                    <img src="<?php echo base_url('uploads/digital_sign/'.$member['digital_sign']);?>" title="Consultant's Digital Sign" style="border:1px solid #cccccc; padding:5px;">
                                                <?php }else{ echo "---"; } ?>        
                                            </td>
                                            <td class="text-center">
                                                <!-- Edit -->
                                                <?php if($crudInfo->p_update){ ?>
                                                    <a href="<?=base_url('employee/employee_update/'.$member['employee_id'])?>" title="Edit Staff Employee Details"><i class="fas fa-pencil-alt editSmall" title="Edit Staff Member"></i></a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php $i++; } ?>
                                <?php }else{ ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No Staff Member Added Yet</td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div> 
    </div>
</section>