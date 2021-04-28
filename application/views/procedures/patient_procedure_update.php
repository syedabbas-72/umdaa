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

    </head>
    <body>





      <form method="POST" action="<?php echo base_url('procedure_update/patient_procedure_update')?>" >
<div class="row">
         <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header card-default">
                            <?php  ?>
                        </div>
                        <input type="hidden" name="patient_id" value="<?php echo $patient_id;?>">
                        <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">
                        <input type="hidden" name="appointment_id" value="<?php echo $appointment_id; ?>">
                        <input type="hidden" name="medical_procedure_id" value="<?php echo $medical_procedure_id ;?>">
                        <input type="hidden" name="clinic_id" value="<?php echo $clinic_id ;?>">
                        <div class="card-body">
                            <textarea name="description" id="summernote" style="height: 100%;"><?php echo $procedure_description; ?></textarea>

                        </div>
                       
                    </div>
                     <div class="col-md-12" style="text-align: center">
                            <input class="btn btn-success" type="submit" onclick="submit.performClick();" name="submit" value="Save For This Patient">
                            <input class="btn btn-primary" type="submit" onclick="submit.performClick();" name="submit" value="Save As Template">
                        </div>
                </div>
      </div>
  </form>

        
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