<html>
    <head>
    <meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<!-- <meta content="width=device-width, initial-scale=1" name="viewport" /> -->
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
	
    </head>

    <!-- <body class="bg-b-black"> -->
	<body>
        <div class="container-fluid">
		<div class="card mt-5">
			<div class="card-body">
				<?php 
				// echo $header;
				if(!isset($header))
				{
					$this->load->view('pdfViews/Header');
				}
				?>            
	        	<?=$this->load->view($view)?>   
				 <!-- <div class="row mt-5">
						<button onclick="window.print()"  class="ml-auto">Print</button>
				</div>                      -->
			</div>
		</div>
        </div>
    </body>

    <!-- bootstrap -->
    <script src="<?php echo base_url(); ?>assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <!-- Material -->
    <script src="<?php echo base_url(); ?>assets/plugins/material/material.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/summernote/summernote.min.js" ></script>
    <!--select2-->
    <script src="<?php echo base_url(); ?>assets/plugins/select2/js/select2.js" ></script>

</html>