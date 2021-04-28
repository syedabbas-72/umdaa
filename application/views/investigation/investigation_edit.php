<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li><a class="parent-item" href="<?php echo base_url("Investigation"); ?>">Investigation</a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Edit</li>
        </ol>

        <div class="pull-right tPadding">
            <a href="<?= base_url('Lab/add_order'); ?>" class="btn btn-primary btn-icon"><i class="fas fa-plus-circle"></i> NEW ORDER</a>
        </div>
    </div>
</div>


<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <!-- card start -->
            <div class="card">
                <!-- <div class="card-header card-default">Inline form</div> -->
                <div class="card-body">
                    <div class="tabs">
                        <div class="tab-content">
                            <div role="tabpanel">
                                <form method="post" action="<?php echo base_url('Investigation/investigation_update/'.$investigations->investigation_id);?>"  autocomplete="off" enctype="multipart/form-data" class="form">                                  
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="investigation" class="col-form-label">INVESTIGATION<span style="color: red;">*</span></label>
                                                <input type="text" name="investigation" type="text" placeholder="" class="form-control" required="" value="<?php echo $investigations->investigation; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="first_name" class="col-form-label">SHORT FORM<span style="color: red;">*</span></label>
                                                <input id="investigation_code" name="short_form" type="text" class="form-control" required="" value="<?php echo $investigations->short_form; ?> " required="">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="category" class="col-form-label">CATEGORY<span style="color: red;">*</span></label>
                                                <!-- <input id="category" name="category" value="<?php echo $investigations->category; ?>" type="text" class="form-control" required=""> -->
                                                <?php $cat = $investigations->category; ?>
                                                <select id="category_sb" name="category" class="form-control" required="">
                                                    <option value="">Select Category</option>
                                                    <option value="Lab" <?php echo ($cat == 'Lab') ? 'selected' : ''; ?> >Lab</option>
                                                    <option value="Pathology" <?php echo ($cat == 'Pathology') ? 'selected' : ''; ?>>Pathology</option>
                                                    <option value="Micro Biology" <?php echo ($cat == 'Micro Biology') ? 'selected' : ''; ?>>Micro Biology</option>
                                                    <option value="Radiology" <?php echo ($cat == 'Radiology') ? 'selected' : ''; ?>>Radiology</option>
                                                    <option value="Other Diagnostics" <?php echo ($cat == 'Other Diagnostics') ? 'selected' : ''; ?>>Other Diagnostics</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="pull-right">
                                                <input type="submit" value="Save" name="submit" class="btn btn-success"> 
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
    </div>
</section>   