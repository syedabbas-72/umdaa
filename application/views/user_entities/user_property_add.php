<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;UMDAA Clinics&nbsp;<i class="fa fa-angle-right"></i></li>
            <li><a href="<?php echo base_url('User_Entities'); ?>">User Entities</a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li><a href="<?php echo base_url('User_Entities#pillsProperties'); ?>">Properties</a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Add New Property</li>
        </ol>
    </div>
</div>

<!-- content start -->
<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <!-- card start -->
            <div class="card">

                <div class="row col-md-12 page-title">
                    <div class="pull-left col-md-6">User Entities <i class="fas fa-caret-right"></i> Add User Property</div>
                </div>

                <div class="card-body">
                    <div class="tabs">
                        <div class="tab-content">
                            <div role="tabpanel" >
                                <form method="post" action="<?php echo base_url('User_Entities/user_property_add');?>" autocomplete="off" class="form">
                                    <div class="row">
                                        <div class="col-md-4 form-group">
                                            <label class="col-form-label">Property Name *</label>
                                            <input type="text" name="property_name" class="form-control" value="" required="">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label class="col-form-label">Property Icon</label>
                                            <input type="text" name="property_icon" class="form-control" value="" placeholder="Ex: <i class='fa fa-dashboard'></i>">
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 20px;">
                                        <div class="col-md-12 text-left">
                                            <input type="submit" value="Save" name="submit" class="btn btn-success"> 
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