<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li>Lab</li>
            <li>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li>Packages</li>
            <li>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Add New</li>
        </ol>
    </div>
</div>

<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card"> 

                <div class="row page-title">
                    <div class="col-md-12">
                        New Investigation Package &nbsp; <span><i class="fas fa-caret-right"></i></span>
                    </div>
                </div>

                <div class="card-body"> 
                    <form method="POST" action="<?php echo base_url('Lab/add_clinic_package');?>" role="form" class="form">
                        <div class="row">
                            <div class="col-sm-12">
                                <p>Please specify the Package Name, Price &amp; create new one, later add tests under </p>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="package_name" class="col-form-label">Pakage Name <span style="color:red;">*</span></label>
                                            <input id="package_name" name="package_name" type="text" placeholder="" class="form-control" required="">
                                        </div>
                                    </div>															
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="price" class="col-form-label">Package Price <span style="color:red;">*</span></label>    
                                            <input id="price" name="price" type="text" placeholder="" class="form-control" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="price" class="col-form-label">&nbsp;</label><br>    
                                            <input type="submit" value="Save" name="submit" class="btn btn-success">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>