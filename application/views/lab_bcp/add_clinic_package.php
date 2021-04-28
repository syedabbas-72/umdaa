
<div class="page-bar">
  <div class="page-title-breadcrumb">
      <!-- <div class=" pull-left">
        <div class="page-title">Form Layouts</div>
      </div> -->
      <ol class="breadcrumb page-breadcrumb pull-right">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="#"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>
          <li><a class="parent-item" href="<?php echo base_url('lab/lab_packages'); ?>">LAB PACKAGES</a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>         
          <li class="active">PACKAGE ADD</li>
      </ol>
  </div>
</div>

            <div class="row">             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                          <form method="POST" action="<?php echo base_url('Lab/add_clinic_package');?>" role="form">
                           <div class="row col-md-12">
                                <div class="col-md-3">
								<div class="form-group">
                                    <label for="package_name" class="col-form-label">PACKAGE NAME<span style="color:red;">*</span></label>
                                    <input id="package_name" name="package_name" type="text" placeholder="" class="form-control" required="">
                                </div></div>															
                                <div class="col-md-3"><div class="form-group">
                                    <label for="price" class="col-form-label">PACKAGE PRICE<span style="color:red;">*</span></label>    
                                    <input id="price" name="price" type="text" placeholder="" class="form-control" required="">
                                </div>
                              </div>
							  <div class="col-md-3"><div class="form-group">
                                    <label for="price" class="col-form-label">ITEM CODE<span style="color:red;">*</span></label>    
                                    <input id="item_code" name="item_code" type="text" placeholder="" class="form-control" required="">
                                </div>
                              </div>
							  
							  </div>
							  
							   
							<div class="col-sm-6">
                                        <div class="pull-right">
                                            <input type="submit" value="Save" name="submit" class="btn btn-success btn-rounded box-shadow">
                                        </div>
                                    </div>
                          </form>
                        </div>
                    </div>
                </div>
            </div>
