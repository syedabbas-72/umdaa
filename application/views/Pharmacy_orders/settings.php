<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li>Pharmacy&nbsp;<i class="fa fa-angle-right"></i></li>
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
                        Pharmacy &nbsp; <span><i class="fas fa-caret-right"></i></span> &nbsp; Settings
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="<?php echo base_url('Pharmacy_orders/pharmacySettings');?>" enctype="multipart/form-data" role="form" class="customForm">

                        <!-- Sub header in the form -->
                        <div class="row text-center docInfoHdr">
                            <div class="col-md-6 text-left">
                                Pharmacy Information
                            </div>
                        </div>

                        <div class="row col-md-12">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="pharmacy_name">Pharmacy Name&nbsp;<span style="color:red;">*</span></label>
                                    <input type="text" class="form-control" id="pharmacy_name" placeholder="" maxlength="30" name="name" value="<?php echo $pharmacy_info->name;?>" required="">
                                    <input type="hidden" class="form-control" name="clinic_pharmacy_id" value="<?php echo $pharmacy_info->clinic_pharmacy_id;?>" required="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="mobile_tb">Phone / Mobile&nbsp;<span style="color:red;">*</span></label> 
                                    <input id="mobile_tb" pattern="[7-9]{1}[0-9]{9}" maxlength="10" onkeypress="return numeric()" name="mobile" value="<?php echo $pharmacy_info->mobile;?>" type="text" placeholder="" class="form-control" required="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="pharmacy_email" class="">Email&nbsp;<span style="color:red;">*</span></label>
                                    <input type="email" id="pharmacy_email" name="email" value="<?php echo $pharmacy_info->email;?>" maxlength="50" placeholder="" class="form-control" required="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="gst_number_tb" class="">GST Number</label>
                                    <input type="text" id="gst_number_tb" name="gst_number" value="<?php echo $pharmacy_info->gst_number;?>"  type="text" placeholder="" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row col-md-12">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="max_discount_tb" class="">Max Discount&nbsp;<span style="color:red;">*</span></label>
                                    <input id="max_discount_tb" name="max_discount" value="<?php echo $pharmacy_info->max_discount;?>" onkeypress="return numeric()" maxlength="2" max="99" type="text" placeholder="" class="form-control" required="">
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="address_ta" class="">Address</label>                
                                    <textarea cols="5" rows="1" id="address_ta" name="address" placeholder="" class="form-control"><?php echo $pharmacy_info->address;?></textarea>
                                </div>
                            </div>                                                     
                        </div>

                        <div class="row col-md-12">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Pharmacy Logo</label>
                                    <div class="fileinput-new" data-provides="fileinput" style="padding-top:0px !important; padding-left: 0px !important">
                                        <?php if($pharmacy_info->logo != ''){ ?>
                                        <div class="fileinput-preview text-center" data-trigger="fileinput" style="padding-left: 0px !important; padding-top:0px !important;"><img style="width:100%; border:1px solid #ccc; background: #f3f3f3; margin-bottom: 5px" src="<?php echo base_url('uploads/pharmacy_logos/'.$pharmacy_info->logo);?>" /></div>
                                        <?php } ?>
                                        <input type="file" id="pharmacy_logo" name="pharmacy_logo" value="<?= $pharmacy_info->logo; ?>" class="form-control">
                                        <input type="file" class="custom-file-input" id="inputGroupFile01"
                                        aria-describedby="logoUpload" name="clinic_logo" accept="image/x-png,image/gif,image/jpeg">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row col-md-12">
                            <div class="col-md-6">
                                <div class="pull-left">
                                    <input type="submit" value="Save Changes" name="submit" class="customBtn">
                                </div>
                            </div>
                        </div>           
                    </form>
                </div>
            </div>
        </div> 
    </div>
</section>