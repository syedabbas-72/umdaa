<div class="row page-header">
   <div class="col-lg-6 align-self-center">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#"><?php echo $_SESSION['clinic_name']; ?></a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('Lab/lab_packages'); ?>">LAB PACKAGE</a></li>
          <li class="breadcrumb-item active"><a href="#">EDIT PACKAGE ITEM</a></li>
        </ol>
  </div>
</div>

        <section class="main-content">
            <div class="row">             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                          <form method="POST" action="<?php echo base_url('Lab/edit_clinic_package/'.$invgpinfo->clinic_investigation_package_id);?>" role="form">
						  <input id="clinic_investigation_package_id" name="clinic_investigation_package_id" type="hidden" value="<?php echo $invgpinfo->clinic_investigation_package_id; ?>" class="form-control">
                           <div class="row col-md-12">
                                <div class="col-md-3">
								<div class="form-group">
                                    <label for="package_name" class="col-form-label">PACKAGE NAME<span style="color:red;">*</span></label>
                                    <input id="package_name" name="package_name" type="text" placeholder="" class="form-control" required value="<?php echo $invgpinfo->package_name; ?>">
                                </div></div>															
                                <div class="col-md-3"><div class="form-group">
                                    <label for="price" class="col-form-label">PACKAGE PRICE<span style="color:red;">*</span></label>    
                                    <input id="price" name="price" type="text" placeholder="" class="form-control" required value="<?php echo $invgpinfo->price; ?>">
                                </div>
                              </div>
                                <div class="col-md-3"><div class="form-group">
                                    <label for="price" class="col-form-label">ITEM CODE<span style="color:red;">*</span></label>    
                                    <input id="item_code" name="item_code" type="text" placeholder="" class="form-control" required value="<?php echo $invgpinfo->item_code; ?>">
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
        </section>  

