<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">HOME</a></li>
          <li class="breadcrumb-item"><a href="#">CLINICS</a></li>
          <li class="breadcrumb-item active"><a href="#">CLINIC TYPE ADD</a></li>          
        </ol>
  </div>
</div>

        <section class="main-content">
            <div class="row">             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                         
                          <form method="POST" action="<?php echo base_url('Clinic/clinic_type_add');?>" enctype="multipart/form-data" role="form" id="clinicForm" autocomplete="off">
                            <div class="row col-md-12">
                            <label for="demographic" id="demo"> <u class="demo_under_line">CLINIC INFORMATION</u></label>
                          </div>
                            <div class="row col-md-12">
                                <div class="col-md-4"><div class="form-group">
                                    <label for="clinic_type" class="col-form-label">CLINIC TYPE<span style="color:red;">*</span></label>
                                        <input name="clinic_type" id="clinic_type" class="form-control" required="" <?php echo set_value('clinic_type'); ?> />
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
					clinic_type: {
						required: true,
						minlength: 3
					}
				},
				messages: {
					clinic_type: {
						required: "Please enter your Clinic Type",
						minlength: "Your Clinic Type must consist of at least 8 characters"
					}
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
