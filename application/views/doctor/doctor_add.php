
<div class="row page-header">
    <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">HOME</a></li>
            <li class="breadcrumb-item"><a href="#">DOCTOR</a></li>
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
                <div class="card-body">
                    <div class="tabs">
                        <ul class="nav nav-tabs">
                            <li class="nav-item" role="presentation"><a class="nav-link" active href="#clinic" aria-controls="home" role="tab" data-toggle="tab" aria-selected="false">CLINIC</a></li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="inhouse">
                                <form method="POST" class="custom-form" action="<?php echo base_url('doctor/doctor_add');?>" role="form">
                                    <div class="row col-md-12">
                                        <label for="demographic" id="demo"> <u class="demo_under_line">DEMOGRAPHIC DETAILS </u></label>
                                    </div>
                                    <div class="row col-md-12">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="first_name" class="col-form-label">FIRST NAME<span style="color: red;">*</span></label>
                                                <input id="first_name" name="first_name" type="text" placeholder="" class="form-control" required="">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="last_name" class="col-form-label">LAST NAME</label>
                                                <input id="last_name" name="last_name" type="text" placeholder="" class="form-control">
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="row col-md-12">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="consulting_fee" class="col-form-label">CONSULTATION FEE</label>
                                                <input id="consulting_fee" name="consulting_fee" type="text" placeholder="" class="form-control" >
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="consulting_time" class="col-form-label">CONSULTATION TIME</label>
                                                <input id="consulting_time" name="consulting_time" type="text" placeholder="" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="reg_code" class="col-form-label">REVIEW DAYS</label>
                                                <input id="review_days" name="review_days" type="text" placeholder="" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row col-md-12">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="reg_code" class="col-form-label">Registration Code</label>
                                                <input id="reg_code" name="reg_code" type="text" placeholder="" class="form-control">
                                            </div>
                                        </div>

                                        <div class="col-md-4">                                       
                                            <div class="form-group">
                                                <label>Gender</label>
                                                <div class="row">
                                                    <div class="radio radio-success">
                                                        <input type="radio" name="gender" id="radio12" value="male" checked>
                                                        <label for="radio12"> Male </label>
                                                    </div>
                                                    <div class="radio radio-danger">
                                                        <input type="radio" name="gender" id="radio13" value="female">
                                                        <label for="radio13"> Female </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>   
                                    </div> 
                                    <div class="row col-md-12">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="clinic_id" class="col-form-label">Clinic<span style="color: red;">*</span></label>
                                                <select name="clinic_id" id="clinic_id" class="form-control" required="">
                                                    <option>Select Clinic</option>
                                                    <option value="0">Umdaa Health Care</option>
                                                    <?php foreach($clinic_list as $value) { ?>
                                                        <option value="<?php echo $value->clinic_id?>"><?php echo $value->clinic_name; ?> </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="package_id" class="col-form-label">Subscription Package<span style="color: red;">*</span></label>
                                                <select name="package_id" id="package_id" class="form-control" required="">
                                                    <option>Select Subscription package</option>
                                                    <?php foreach($packages_list as $value) { ?>
                                                        <option value="<?php echo $value->package_id?>"><?php echo $value->package_name; ?> </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="package_subscription_date" class="col-form-label">Package Subscription Start Date<span style="color: red;">*</span></label>
                                                <input id="package_subscription_date" name="package_subscription_date" type="text" placeholder="" class="form-control" required="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row col-md-12">
                                        <label for="qualification-details" id="demo"> <u class="demo_under_line">Qualification Details</u></label>
                                    </div>
                                    <div class="row col-md-12">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="qualification" class="col-form-label">Qualification<span style="color: red;">*</span></label>
                                                <input id="qualification" name="qualification" type="text" placeholder="" class="form-control" required="">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="department" class="col-form-label">Department<span style="color: red;">*</span></label>
                                                <select name="department" id="department" class="form-control" required="">
                                                    <option>Select Department</option>
                                                    <?php foreach($department_list as $value) { ?>
                                                        <option value="<?php echo $value->department_id?>"><?php echo $value->department_name; ?> </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="experience" class="col-form-label">EXPERIENCE</label>
                                                <input id="experience" name="experience" type="text" placeholder="" class="form-control">
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="row col-md-12">
                                        <label for="contact_details" id="demo"> <u class="demo_under_line">CONTACT DETAILS </u></label>
                                    </div>
                                    <div class="row col-md-12">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="address" class="col-form-label">ADDRESS<span style="color: red;">*</span></label>
                                                <input id="address" name="address" type="text" placeholder="" class="form-control" required="">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="state" class="col-form-label">STATE<span style="color: red;">*</span></label>
                                                <select name="state" id="state" class="form-control" required="">
                                                    <option value="">Select State--</option>
                                                    <?php foreach ($state_list as $key => $value) {?>
                                                        <option value="<?php echo $value->state_id;?>"><?php echo $value->state_name;?> </option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="pincode" class="col-form-label">PINCODE</label>
                                                <input id="pincode" name="pincode" type="text" placeholder="" class="form-control">
                                            </div>
                                        </div>
                                    </div> 

                                    <div class="row col-md-12">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="mobile" class="col-form-label">MOBILE<span style="color: red;">*</span></label>
                                                <input id="mobile" name="mobile" type="text" placeholder="" class="form-control" required="">
                                            </div>
                                        </div>

                                        <div class="col-md-4"><div class="form-group">
                                            <label for="phone" class="col-form-label">Email<span style="color: red;">*</span></label>
                                            <input id="phone" name="email" type="text" placeholder="" class="form-control" required="">
                                        </div>
                                    </div>                               
                                </div> 
                                <div class="row col-md-12">
                                    <div class="col-md-6">
                                        <div class="pull-right">
                                            <input type="submit" name="submit" value="Save" class="btn btn-success"> 
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
</section>
<script>
    $(function () {

        $("#package_subscription_date,#package_subscription_date1,#package_subscription_date").datepicker({dateFormat: "yy-mm-dd", changeYear: true, yearRange: "-100:+0"});

    });
</script>       

