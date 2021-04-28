
<div class="page-bar">
  <div class="page-title-breadcrumb">
      <!-- <div class=" pull-left">
        <div class="page-title">Form Layouts</div>
      </div> -->
      <ol class="breadcrumb page-breadcrumb pull-right">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="#"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>
          <li><a class="parent-item" href="<?= base_url('Lab/templates'); ?>">LAB TEMPLATES</a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>         
          <li class="active"><a href="#">EDIT TEMPLATE</a></li>
      </ol>
  </div>
</div>

 
            <div class="row">             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                          <form method="POST" action="<?php echo base_url('Lab/edit_template');?>" role="form">
						  <input type="hidden" value="<?php echo $templateinfo->clinic_investigation_template_id; ?>" name="clinic_investigation_template_id" />
                           <div class="row col-md-12">
                                <div class="col-md-3">
								<div class="form-group">
                                    <label for="template_name" class="col-form-label">TEMPLATE NAME<span style="color:red;">*</span></label>
                                    <input id="template_name" name="template_name" type="text" placeholder="" class="form-control" required value="<?php echo $templateinfo->template_name; ?>">
                                </div></div>															
                                <div class="col-md-3"><div class="form-group">
                                    <label for="template_type" class="col-form-label">TEMPLATE TYPE<span style="color:red;">*</span></label>    
                                    <select id="template_type" name="template_type" required class="form-control">
									<option value=''>--Select--</option>
									<option value='Excel' <?php if($templateinfo->template_type=='Excel') echo "selected"; ?>>Excel</option>
									<option value='General' <?php if($templateinfo->template_type=='General') echo "selected"; ?>>General</option>
									</select>
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
    


