<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1" name="viewport" />
	<meta name="description" content="Health Care Management System" />
	<meta name="author" content="UMDAA" />
	<title>UMDAA Health Care</title>
	<!-- google font -->
	<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet" type="text/css" />
	<!-- icons -->
	<link href="<?php echo base_url(); ?>assets/css/all.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<!--bootstrap -->
	<link href="<?php echo base_url(); ?>assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url(); ?>assets/plugins/sweet-alerts2/sweetalert2.min.css" rel="stylesheet">
	<link href="<?php echo base_url(); ?>assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url(); ?>assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
	<link href="<?php echo base_url(); ?>assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">
	<link href="<?php echo base_url(); ?>assets/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url(); ?>assets/plugins/bootstrap-editable/inputs-ext/address/address.css" rel="stylesheet" type="text/css" />
	<!-- data tables -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/plugins/datatables/datatables.min.css"/>
	<!-- <link href="<?php echo base_url(); ?>assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/> -->
	<!-- <link href="<?php echo base_url(); ?>assets/buttons/1.5.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />   -->
	<!--tagsinput-->
	<link href="<?php echo base_url(); ?>assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">

	<!--select2-->
	<link href="<?php echo base_url(); ?>assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url(); ?>assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

	<!-- Material Design Lite CSS -->
	<link href="<?php echo base_url(); ?>assets/plugins/material/material.min.css" rel="stylesheet" >
	<link href="<?php echo base_url(); ?>assets/css/material_style.css" rel="stylesheet">
	<!-- Theme Styles -->
	<link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet" type="text/css" />    
	<link href="<?php echo base_url(); ?>assets/css/plugins.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url(); ?>assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url(); ?>assets/css/responsive.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url(); ?>assets/css/theme-color.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo base_url(); ?>assets/plugins/summernote/summernote.css" rel="stylesheet">
	<script src="<?php echo base_url(); ?>assets/plugins/jquery/jquery.min.js" ></script>
	<script src="https://code.jquery.com/jquery-migrate-3.0.0.min.js"></script>
	<link href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" type="text/css" rel="stylesheet" />
	<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<!-- favicon -->
	<link rel="shortcut icon" href="" /> 
	<style type="text/css">
		#tableExport_wrapper .dt-buttons {
			display: none;
		}
		.switchToggle input:checked + .slider:before {
			-webkit-transform: translateX(26px) !important;
			-ms-transform: translateX(26px) !important;
			transform: translateX(50px) !important;
		}
		.switchToggle {
			width: 80px !important;
			height: 30px !important;
		}
		.switchToggle .slider:before{
			width: 22px !important;
			height: 22px !important
		}
		.pull-left{
			float: left;
		}
		.page-header.navbar .page-logo{
			background: #fff !important;
			padding: 10px 20px 0px 20px !important;
		}

		input[type=number]::-webkit-inner-spin-button, 
		input[type=number]::-webkit-outer-spin-button { 
			-webkit-appearance: none; 
			margin: 0; 
		}


		.checkbox input[type='checkbox'] + label:before {
			font-family: 'Font Awesome 5 Free';
			content: "\f00c";
			color: #fff;
		}
		/* font weight is the only important one. The size and padding makes it look nicer */
		.checkbox input[type='checkbox']:checked + label:before {
			font-weight:900;
			color: #000;
			font-size: 10px;
			padding-left: 3px;
			padding-top: 0px;
		}
		.checkbox input[type="checkbox"]:checked + label::after,
		.checkbox input[type="radio"]:checked + label::after {
			content: "";
		}
	</style>

</head>

<body class="page-header-fixed sidemenu-closed-hidelogo page-content-white page-md header-white dark-color logo-dark">

<div></div>

<form method="POST" action="https://www.devumdaa.in/dev/Nurselogin/think" enctype="multipart/form-data" role="form" class="customForm">
                                    
                                    <!-- Sub header in the form -->
                                    <div class="row text-center docInfoHdr">
                                        <div class="col-md-6 text-left">
                                            Clinic Creation
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="clinic_name">Clinic Name<span style="color:red;">*</span></label>
                                                <input type="text" class="form-control" id="clinic_name" placeholder="Enter clinic name" name="clinic_name" required="">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="clinic_phone">Clinic Phone<span style="color:red;">*</span></label> 
                                                <input id="clinic_phone" name="clinic_phone"  maxlength="10" value="" type="text" placeholder="enter phone number" class="form-control" required="">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="clinic_email" class="">Clinic Email<span style="color:red;">*</span></label>
                                                <input id="clinic_email" name="email" value="" type="text" placeholder="Enter clinic Email" class="form-control" required="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">                                        
                                        <div class="col-md-3">
                                        </div>

            
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="location" class="">Location</label>
                                                <input id="location" name="location" value="enter your location" type="text" placeholder="Location" class="form-control" required="">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="pincode" class="">Pincode</label>
                                                <input id="pincode" name="pincode"  maxlength="6" value="" type="text" placeholder="Pincode" class="form-control" required="">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="address" class="">Address</label>                
                                                <textarea cols="5" rows="5" id="address" name="address" placeholder="" class="form-control" autocomplete="off" spellcheck="false" required="">
                                            </textarea>
                                        </div>


                                        <div class="row col-md-3">
                                            <div class="col-md-6">
                                                <div class="pull-left">
                                                    <input type="submit" value="Save Changes" name="submit" class="customBtn">
                                                    <!-- <input type="button" class="customBtn" value="Cancel" onclick="window.history.go(-1)">     -->
                                                </div>
                                            </div>
                                        
                                    </div>  

                                    </div> 
                                    
                                    </div>
                                    <!-- Sub header in the form -->
                                    <!-- <div class="row text-center docInfoHdr">
                                        <div class="col-md-6 text-left">
                                            Business Information
                                        </div>
                                    </div> -->

                                    <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="location" class="">Location</label>
                                                <input id="location" name="location" value="enter your location" type="text" placeholder="Location" class="form-control" required="">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="pincode" class="">Pincode</label>
                                                <input id="pincode" name="pincode"  maxlength="6" value="" type="text" placeholder="Pincode" class="form-control" required="">
                                            </div>
                                        </div>
                                       
</form>

 <div class="col-md-12" style="background: #042942;">

                              
            <form action="https://www.devumdaa.in/dev/authentication/login" class="login10-form validate-form" autocomplete="off" method="post" accept-charset="utf-8">
            <span class="login10-form-logo">
                <!-- <img alt="" src="https://www.devumdaa.in/dev//assets/img/umdaa_logo.png"> -->
            </span>
            <span class="login100-form-title p-b-34 p-t-27">
                
            </span>
            <div class="wrap-input100 validate-input" data-validate="Enter username">
                <input class="input100" type="text" name="email" placeholder="Username">
                <span class="focus-input100" data-placeholder=""></span>
            </div>
            <div class="wrap-input100 validate-input" data-validate="Enter password">
                <input class="input100" type="password" name="password" placeholder="Password">
                <span class="focus-input100" data-placeholder=""></span>
            </div>

            <div class="container-login100-form-btn" style="margin-bottom: 20px;">
                <input class="login100-form-btn" type="submit" name="submit" id="submit" value="Login">
            </div>
            <div class="text-center p-t-30">
                <!-- <a class="txt1" href="forgot_password.html">
                    Forgot Password?
                </a> -->
</div>
</form> 
</div>


                                
<!-- <p>jesus love</p> -->

</body>
</html>