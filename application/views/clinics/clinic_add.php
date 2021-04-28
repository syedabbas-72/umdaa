
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
                <?php if(isset($msg) || validation_errors() !== ''): ?>
                <div class="alert bg-danger alert-dismissible" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button> <strong>Alert !</strong> <?= validation_errors();?>
                    <?= isset($msg)? $msg: ''; ?></div>
                    <?php endif; ?>
                         
                          <form method="POST" action="<?php echo base_url('clinic/clinic_add');?>" enctype="multipart/form-data" class="custom-form" role="form" id="clinicForm">
                            <div class="row col-md-12">
                            <label for="demographic" id="demo"> <u class="demo_under_line">CLINIC INFORMATION</u></label>
                          </div>
                            <div class="row col-md-12">
                                <div class="col-md-3">
								<div class="form-group">
                                    <label for="clinic_name" class="col-form-label">CLINIC NAME<span style="color:red;">*</span></label>
                                    <input id="clinic_name" name="clinic_name" type="text" placeholder="" class="form-control" required="">
                                </div></div>
								<!--
                                <div class="col-md-4"><div class="form-group">
                                    <label for="clinic_type" class="col-form-label">CLINIC TYPE<span style="color:red;">*</span></label>
                                        <input name="clinic_type" id="clinic_type" class="form-control" required="">
                                    </div>
                                </div>
								-->
								
                                <div class="col-md-3"><div class="form-group">
                                    <label for="clinic_phone" class="col-form-label">CLINIC PHONE<span style="color:red;">*</span></label>    
                                    <input id="clinic_phone" name="clinic_phone" type="text" placeholder="" class="form-control" required="">
                                </div>
                              </div>
                               <div class="col-md-3"><div class="form-group">
                                   <label for="clinic_email" class="col-form-label">CLINIC E-MAIL<span style="color:red;">*</span></label>
                                      <input id="clinic_email" name="clinic_email" type="text" placeholder="" class="form-control" required="">
                                  </div>
                              </div>
                              <div class="col-md-3"><div class="form-group">
                                     <label for="pincode" class="col-form-label">CLINIC ALIAS<span style="color:red;">*</span></label>
                                      <input id="clinic_alias" name="clinic_alias" type="text" placeholder="" class="form-control" required>
                                    </div>
                                </div>
                            </div> 
                            <div class="row col-md-12">
                              
                              <div class="col-md-12"><div class="form-group">
                                    <label for="address" class="col-form-label">ADDRESS<span style="color:red;">*</span></label>                
                                    <textarea id="address" name="address" class="form-control" style="height: 150px"></textarea>
                                  </div>
                                </div>
                              
                            </div> 
                            <div class="row col-md-12">
                              
                                <div class="col-md-4"><div class="form-group">
                                    <label for="state" class="col-form-label">STATE<span style="color:red;">*</span></label>
                                      <select name="state" id="state" class="form-control" required="">
                                        <option>--select state--</option>
                                        <?php foreach ($state_list as $val) { ?>
                                          <option value="<?php echo $val->state_id;?>"><?php echo $val->state_name;?></option>
                                        <?php } ?>
                                      </select>
                                  </div>
                                </div>
                                 <div class="col-md-4"><div class="form-group">
                                    <label for="district" class="col-form-label">DISTRICT<span style="color:red;">*</span></label>
                                      <input name="district" id="district" class="form-control" required="">
                                    </div>
                                </div>
                                 <div class="col-md-4"><div class="form-group">
                                     <label for="pincode" class="col-form-label">PIN CODE</label>
                                      <input id="pincode" name="pincode" type="text" placeholder="" class="form-control">
                                    </div>
                                </div>
                               
								 
                            </div>
						  <div class="row col-md-12">
							<div class="col-md-4"><div class="form-group">
							<label>CLINIC LOGO</label>
							  <div class="fileinput fileinput-new input-group" data-provides="fileinput">
								<div class="form-control" data-trigger="fileinput"><span class="fileinput-filename"></span></div>
								<span class="input-group-addon btn btn-primary btn-file ">
								<span class="fileinput-new">Select</span>
								<span class="fileinput-exists">Change</span>
								<input type="file"  name="clinic_logo">
								</span>
								<a href="#" class="input-group-addon btn btn-danger  hover fileinput-exists" data-dismiss="fileinput">Remove</a>
							  </div>
							</div>
							</div>
							<div class="col-md-4"><div class="form-group">
							<label>CLINIC EMBLEM</label>
							  <div class="fileinput fileinput-new input-group" data-provides="fileinput">
								<div class="form-control" data-trigger="fileinput"><span class="fileinput-filename"></span></div>
								<span class="input-group-addon btn btn-primary btn-file ">
								<span class="fileinput-new">Select</span>
								<span class="fileinput-exists">Change</span>
								<input type="file"  name="clinic_emblem">
								</span>
								<a href="#" class="input-group-addon btn btn-danger  hover fileinput-exists" data-dismiss="fileinput">Remove</a>
							  </div>
							</div>
							</div>
							
							<div class="col-md-4"><div class="form-group">
							<label>CLINIC QRCODE</label>
							  <div class="fileinput fileinput-new input-group" data-provides="fileinput">
								<div class="form-control" data-trigger="fileinput"><span class="fileinput-filename"></span></div>
								<span class="input-group-addon btn btn-primary btn-file ">
								<span class="fileinput-new">Select</span>
								<span class="fileinput-exists">Change</span>
								<input type="file"  name="clinic_qrcode">
								</span>
								<a href="#" class="input-group-addon btn btn-danger  hover fileinput-exists" data-dismiss="fileinput">Remove</a>
							  </div>
							</div>
							</div>
						  </div>
                          <div class="row col-md-12">
                            <label for="demographic" id="demo"> <u class="demo_under_line">CLINIC INCHARGE INFORMATION</u></label>
                          </div>
                            <div class="row col-md-12">
                                <div class="col-md-4"><div class="form-group">
                                  <label for="clinic_head" class="col-form-label">CLINIC INCHARGE<span style="color:red;">*</span></label>          
                                  <input id="clinic_head" name="clinic_incharge" type="text" placeholder="" class="form-control" required="">
                                </div>
                                </div>
                                <div class="col-md-4"><div class="form-group">
                                     <label for="incharge_mobile" class="col-form-label">INCHARGE MOBILE<span style="color:red;">*</span></label>
                                      <input id="incharge_mobile" name="incharge_mobile" type="text" placeholder="" class="form-control" required="">
                                    </div>
                                </div>
                                 <div class="col-md-3"><div class="form-group">
                                    <label for="incharge_email" class="col-form-label">E-MAIL<span style="color:red;">*</span></label>    
                                     <input id="incharge_email" name="incharge_email" type="text" placeholder="" class="form-control" required="">
                                    </div>
                                </div>
								 <div class="col-md-1"><div class="form-group">
                                    <label for="pharmacy" class="col-form-label">PHARMACY<span style="color:red;">*</span></label>    
                                     <input id="pharmacy" name="pharmacy" type="checkbox" value='1' class="form-control" checked required>
                                    </div>
                                </div>
								
                            </div> 
                                <div class="col-sm-6">
                                        <div class="pull-right">
                                            <input type="submit" value="Save" name="submit" class="btn btn-success btn-rounded box-shadow">
                                        </div>
                                    </div>

                                    <!-- <div class="col-sm-6">
                                        <div class="pull-left">
                                            <button type="submit" class="btn  btn-gray btn-rounded btn-border btn-sm">
                                                <i class=""></i> Cancel
                                            </button>
                                        </div>
                                    </div> -->
                                  
                                </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>  

        <script type="text/javascript" src="<?php echo base_url(); ?>assets/lib/jquery-validate/jquery.validate.js"></script>
        <script type="text/javascript">
		

		$( document ).ready( function () {
			$( "#clinicForm" ).validate( {
				rules: {
					clinic_name: "required",
					clinic_phone: {
						required: true,
						number: true,
						 minlength:10,
  						 maxlength:10,
						
						maxlength:10
					},
					incharge_mobile: {
						required: true,
						 minlength:10,
  						 maxlength:10,
						number: true,
						maxlength:10
					},
					
					clinic_email: {
						required: true,
						email: true
					},
					incharge_email: {
						required: true,
						email: true
					},
					clinic_incharge: "required"
				},
				messages: {
					clinic_name: "Enter your clinic_name",
					clinic_phone:"Enter 10 digit mobile number",
					incharge_mobile:"Enter 10 digit mobile number",
					clinic_email: "Enter a valid email address",
					incharge_email: "Please enter a valid email address"
				},
				errorElement: "em",
				errorPlacement: function ( error, element ) {
					// Add the `help-block` class to the error element
					error.addClass( "help-block" );

					if ( element.prop( "type" ) === "checkbox" ) {
						error.insertAfter( element.parent( "label" ) );
					} else {
						error.insertAfter( element );
					}
				},
				highlight: function ( element, errorClass, validClass ) {
					$( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
				},
				unhighlight: function (element, errorClass, validClass) {
					$( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
				}
			} );

		} );
	</script>
