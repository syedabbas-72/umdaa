<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">HOME</a></li>
          <li class="breadcrumb-item"><a href="#">CLINIC</a></li>
          <li class="breadcrumb-item active"><a href="#">ADD</a></li>          
        </ol>
  </div>
</div>

        <section class="main-content">
            <div class="row">             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                         
                          <form method="POST" action="<?php echo base_url('FormBuilder/form_add');?>" enctype="multipart/form-data" role="form" id="clinicForm">
                           <div class="row col-md-12">
                                <div class="col-md-4">
								<div class="form-group">
                                    <label for="form_name" class="col-form-label">FORM NAME<span style="color:red;">*</span></label>
                                    <input id="form_name" name="form_name" type="text" placeholder="" class="form-control" required="">
                                </div></div>
								</div> 
								<div class="row col-md-12">
                                <div class="col-md-4"><div class="form-group">
                                    <label for="form_type" class="col-form-label">FORM TYPE<span style="color:red;">*</span></label>
                                        
										
								<select class="form-control" id="form_type" name="form_type" required>
								<option value="">Select Type</option>
								<option value='Past History'>Past History</option>
								<option value='Personal History'>Personal History</option>
								<option value='Treatment History'>Treatment History</option>
								<option value='Family History'>Family History</option>
								<option value='Social History'>Social History</option>
								<option value='GPE'>GPE</option>
								<option value='Systemic Examination'>Systemic Examination</option>
								<option value='Other Systems'>Other Systems</option>
							</select>
										
                                    </div>
                                </div>
								</div>  

								<div class="row col-md-12">
                                <div class="col-md-4"><div class="form-group">
                                    <label for="department_type" class="col-form-label">DEPARTMENT TYPE<span style="color:red;">*</span></label>
                                        
										
								<select class="form-control" id="department_type" name="department_type" required>
								<option value="">Select Type</option>
								<?php foreach($departments as $key=>$value){?>
								<option value='<?php echo $value->department_id;?>'><?php echo $value->department_name;?></option>
								<?php }?>
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

        <script type="text/javascript" src="<?php echo base_url(); ?>assets/lib/jquery-validate/jquery.validate.js"></script>
        
