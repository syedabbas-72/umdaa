 <div class="page-bar">
   <div class="page-title-breadcrumb">
      <div class=" pull-left">
         <div class="page-title"><?php
            echo $_SESSION['clinic_name'];
            ?></div>
      </div>
      <ol class="breadcrumb page-breadcrumb pull-right">
         <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php
            echo base_url("dashboard");
            ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
         </li>
         <li><a class="parent-item" href="<?php
            echo base_url("procedure");
            ?>">Medical Procedures</a>&nbsp;<i class="fa fa-angle-right"></i>
         </li>
         <li class="active">Procedure Edit</li>
      </ol>
   </div>
</div>
      <form method="POST" action="<?php echo base_url('procedure/update/'.$procedure_info->medical_procedure_id);?>" >
<div class="row">
         <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header card-default">
                           <!--  <?php echo $procedure->medical_procedure; ?> -->
                        </div>
                        <div class="card-body">
                            <textarea name="description" id="summernote"><?php echo $procedure_info->procedure_description; ?></textarea>

                        </div>
                        <input type="hidden" value="<?php echo $procedure_info->medical_procedure_id;?>" name="medical_procedure_id">
                        <input type="hidden" value="<?php echo $procedure_info->doctor_id ;?>" name="doctor_id">
                        <input type="hidden" value="<?php echo $procedure_info->user_id;?>" name="user_id">
                        <input type="hidden" value="<?php echo $procedure_info->clinic_id ;?>" name="clinic_id">
                    </div>
                     <div class="col-md-12" style="text-align: center">
                            <input class="btn btn-success"  type="submit" name="submit" value="Update Procedure">
                        </div>
                </div>
      </div>
  </form>

<script type="text/javascript">
 $(document).ready(function () {
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
