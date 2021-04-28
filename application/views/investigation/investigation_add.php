<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li><a class="parent-item" href="<?php echo base_url("lab/lab_investigations"); ?>">Investigation Master</a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Add New Investigation</li>
        </ol>
    </div>
</div>

<!-- content start -->
<section class="main-content">
    <div class="row">             
        <div class="col-md-12">
            <div class="card">

                <div class="row page-title">
                    <div class="col-md-12">
                        Investigation Master &nbsp; <span><i class="fas fa-caret-right"></i></span> &nbsp; New Investigation
                    </div>
                </div>

                <div class="card-body">

                    <form method="post" action="<?php echo base_url('Investigation/investigation_add');?>" autocomplete="off" enctype="multipart/form-data" class="form"> 

                        <p>Add new investigation to the investigation master by specifying it in the below fields</p>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="investigation" class="col-form-label">Investigation<span class="imp">*</span></label>
                                    <input type="text" name="investigation[investigation]" type="text" placeholder="" class="form-control" required="">
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="investigation" class="col-form-label">Short Form</label>
                                    <input type="text" name="investigation[short_form]" type="text" placeholder="" class="form-control" required="">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="investigation" class="col-form-label">Sample Type</label>
                                    <input type="text" name="investigation[sample_type]" type="text" placeholder="" class="form-control" required="">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="category" class="col-form-label">Category<span class="imp">*</span></label>
                                    <select id="category_sb" name="investigation[category]" class="form-control" required="">
                                        <option value="">Select Category</option>
                                        <option value="Lab">Lab</option>
                                        <option value="Pathology">Pathology</option>
                                        <option value="Micro Biology">Micro Biology</option>
                                        <option value="Radiology">Radiology</option>
                                        <option value="Other Diagnostics">Other Diagnostics</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Sub header in the form - Clinic Investigation -->
                        <div class="row text-center docInfoHdr">
                            <div class="col-md-6 text-left">
                                Clinic Investigation information
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="price_tb" class="col-form-label">Price</label>
                                    <input type="text" id="price_tb" name="clinic_investigation[price]" type="text" placeholder="Price" class="form-control" onkeypress="return decimal();" required="required">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="low_range_tb" class="col-form-label">Low Range</label>
                                    <input type="text" id="low_range_tb" name="clinic_investigation[low_range]" type="text" placeholder="Low Range" class="form-control" onkeypress="return decimal();">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="high_range_tb" class="col-form-label">Low Range</label>
                                    <input type="text" id="high_range_tb" name="clinic_investigation[high_range]" type="text" placeholder="High Range" class="form-control" onkeypress="return decimal();">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="units_tb" class="col-form-label">Unit</label>
                                    <input type="text" id="units_tb" name="clinic_investigation[units]" type="text" placeholder="" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="method_tb" class="col-form-label">Method</label>
                                    <input type="text" id="method_tb" name="clinic_investigation[method]" type="text" placeholder="" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="other_information_tb" class="col-form-label">Other Information</label>
                                    <input type="text" id="other_information_tb" name="clinic_investigation[other_information]" type="text" placeholder="" class="form-control">
                                </div>
                            </div>                            
                        </div>

                        <div class="row btnRow">
                            <div class="col-md-6">
                                <div class="pull-left">
                                    <input type="submit" value="Create Investigation" name="submit" class="customBtn"> 
                                </div>
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

