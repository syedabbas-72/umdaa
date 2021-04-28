<!DOCTYPE html>

<html lang="en">
<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Umdaa Health Care</title>
                <!-- <link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url('assets/img/icon.png'); ?>"> -->

         <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet" type="text/css" />
    <!-- icons -->
     <link href="<?php echo base_url(); ?>assets/css/all.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <!--bootstrap -->
    <link href="<?php echo base_url(); ?>assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

    <!-- Theme Styles -->
    <link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet" type="text/css" />    
    <link href="<?php echo base_url(); ?>assets/css/plugins.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/css/theme-color.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/plugins/summernote/summernote.css" rel="stylesheet">
    <script src="<?php echo base_url(); ?>assets/plugins/jquery/jquery.min.js" ></script>
<style type="text/css">
  
</style>
    </head>
    <body>
    	<?php $url_parameters = $patient_id."/".$doctor_id."/".$appointment_id."/".$medical_procedure_id."/".$clinic_id; ?>
    	
	<div class="card text-center">
        <div class="card-body">
<div class="col-md-12" style="text-align: center;margin-top: 20px;margin-bottom: 20px;">
         <!--    <a href="<?php echo base_url('Procedure_update/patient_producer/'.$url_parameters.'');?>" name="Edit" class ="btn btn-success" value="EDIT">EDIT</a> -->
        </div>
	<div style="text-align: justify;list-style-type: upper-roman; background-color: white;">
		<?php echo $procedure_description; ?>
	</div>
</div>

</div>
  <script src="<?php echo base_url(); ?>assets/plugins/popper/popper.min.js" ></script>
   
    <!-- bootstrap -->
    <script src="<?php echo base_url(); ?>assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
<script src="<?php echo base_url(); ?>assets/plugins/summernote/summernote.min.js" ></script>
<script type="text/javascript">
 $(document).ready(function () {
  console.log($(document).height());
 // $("#summernote").summernote("fullscreen.toggle");
   $('#summernote').summernote({
    toolbar: [
  // [groupName, [list of button]]
 // image and doc are customized buttons
  // ['misc', ['fullscreen']],
]
  });
});
</script>
</body>
</html>