<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
<style type="text/css">
  .white-bg{
    background-color: #ffffff;
  }
 
    .widget {
    border-radius: 3px;
    padding: 15px;
    border: 0;
    margin-bottom: 30px;
    box-shadow: 0 1px 15px 1px rgba(62, 57, 107, 0.07);
}

</style>
<style type="text/css">
  body{
        font-family: 'Work Sans', sans-serif;
  }
</style>
 <div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-right">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url('dashboard'); ?>">HOME</a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li class="active">DOCTOR SETTINGS</li>
        </ol>
    </div>
</div>


          <div class="row">
       
        <div class="col-12">
          <div class="card" style="padding:5px 30px !important">
            <div class="" style="padding: 0">
              <div class="row">
                            <div class="col-md-6" style="padding-top: 5px">
                                <span >CLINICS LIST</span>
                
                            </div>
                            <div class="col-md-6">
                  <a href="<?php echo base_url('doctor_settings/add_clinic'); ?>" class="btn btn-danger btn-icon pull-right"><i class="fa fa-plus"></i> Add New Clinic</a>
                            </div>
               </div>
                        </div>
                                 </div>
                <div class="card-body">
                    <div class="row col-md-12">
                       
                               
                                  <?php

                                  foreach ($clinic_list as $key => $value) { 
                                    if($value->primary_clinic == 1){
                                      $type = "(Primary)";
                                    }
                                    else{
                                      $type = "(Secondary)";
                                    }
                                    ?>
                                     <div class="col-md-4">
                                      <div class="widget white-bg text-center">
                        <img alt="Profile Picture" width="250px" class="mar-btm margin-b-10" src="<?php echo base_url('uploads/clinic_logos/'.$value->clinic_logo); ?>">
                        <h4 class="font-700 margin-b-10"><?php echo $value->clinic_name.$type; ?></h4>
                        <p class="text-muted margin-b-10"><?php echo $value->address; ?></p>
                        <div>
                            <a href="<?php echo base_url('doctor_settings/doctor_info/'.$value->clinic_id.'/'.$value->doctor_id); ?>" class="btn btn-info">Settings</a>
                            
                        </div>
                    </div>
                                     </div>
                               <?php  } ?>
                                    

                            
                           

                         
                                
    
                                
                                <!-- /.tab-pane -->
                            </div>
                        </div>
                    </div>
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
        </div>
                                               

                         
                            </div>
                     


      
<script type="text/javascript">
  $(document).on("click","#checkbox15",function(){
    if($(this).is(':checked')){
      $("#gstn_div").show();
    }
    else{
      $("#gstn_div").hide();
    }
  });
</script>