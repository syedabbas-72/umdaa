<link href="<?php echo base_url('assets/css/select2.min.css');?>" rel='stylesheet' type='text/css'>
<style type="text/css">
  .select2-container {
    border: 1px solid #dde6e9;
    outline: 0;
}
</style>
 <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?php echo $_SESSION['clinic_name']; ?></div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>

                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("lab/master_template"); ?>">Lab Templates</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                               
                                <li class="active">Edit Template</li>
                            </ol>
                        </div>
                    </div>

        <section class="main-content">
            <div class="row">             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                          <form method="POST" action="<?php echo base_url('Lab/edit_mastertemplate');?>" role="form">
						  <input type="hidden" value="<?php echo $templateinfo->lab_template_id; ?>" name="lab_template_id" />
                           <div class="row col-md-12">
                                <div class="col-md-6">
								<div class="form-group">
                                    <label for="template_name" class="col-form-label">TEMPLATE NAME<span style="color:red;">*</span></label>
                                    <!--<input id="template_name" name="template_name" type="text" placeholder="" class="form-control" required value="<?php echo $templateinfo->template_name; ?>">-->
									<select name="template_name" id="template_name" class="form-control"   required>
									<option value=''>--Select--</option>
									<?php
									  foreach($investigations as $row)
									  {
									  ?>
									  <option value='<?php echo $row->investigation_id; ?>' <?php if($templateinfo->investigation_id==$row->investigation_id) echo "selected"; ?>><?php echo $row->investigation; ?></option>
									  <?php
									  }
									  ?>
									</select>
                                </div></div>															
                                <div class="col-md-6"><div class="form-group">
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
        </section>  
<script src="<?php echo base_url('assets/js/select2.min.js');?>" type='text/javascript'></script>		
<script type="text/javascript">
    $(document).ready(function(){
      $("#template_name").select2({
        
      });
    });
</script>

