<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
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


        <section class="main-content">
          <div class="row">
       
        <div class="col-12">
           <div class="card">
                <div class="card-body">
                  <form method="POST" action="<?php echo base_url('doctor_settings/map_clinic');?>" enctype="multipart/form-data" role="form">
                    <div class="row col-md-12">
                       
                                 <div class="form-group">
                                        <label><b style="font-weight: bold;font-size: 18px">Select your matched clinic</b></label>
                                        </div>
                                        <div class="row col-md-12">
                                          <div class="col">

                                  <?php
                                  if(count($clinic_list)>0){
                                  $i=2;
                                  foreach ($clinic_list as $key => $value) { 
                                    
                                    ?>
                                   
                      <!-- <div class="row col-md-12">
                        <div class="col-md-4">
                          <div class="widget white-bg text-center">
                            <input type="radio" name="clinic_name" id="radio<?php echo $i; ?>" value="<?php echo $value->clinic_id; ?>">
                          <label for="radio<?php echo $i; ?>"> <?php echo $value->clinic_name; ?> </label>
                          <img src="<?php echo base_url('uploads/clinic_logos/'.$value->clinic_logo); ?>" alt="clinic" width="500px" height="100px">
                          <p class="text-muted margin-b-10"><?php echo $value->address; ?></p>
                            
                          </div>
                        </div>
                      </div> -->
                        <div class="radio radio-success">
                          <input type="radio" name="clinic_name" id="radio<?php echo $i; ?>" value="<?php echo $value->clinic_id; ?>">
                          <label for="radio<?php echo $i; ?>"> <?php echo $value->clinic_name; ?> </label>
                          <img src="<?php echo base_url('uploads/clinic_logos/'.$value->clinic_logo); ?>" alt="clinic" width="500px" height="100px">
                          <p class="text-muted margin-b-10" style="margin-left: 60px;"><?php echo $value->address; ?></p>
                        </div>
                       
                                         
                               <?php $i++; } ?>

                               <div class="radio radio-success">
                          <input type="radio" name="clinic_name" id="radio1" value="new">
                          <label for="radio1">If  not matching your clinic, Create new clinic </label>
                        </div>

                            <?php    } else { ?>

<div class="radio radio-success">
                          <input type="radio" name="clinic_name" id="radio1" value="new">
                          <label for="radio1"> Didn't find any clinics. Create new clinic </label>
                        </div>
                               <?php } ?>
                              
                             </div>

                           </div>
                           <div class="row col-md-12">
                            <div class="form-group col-md-12" style="margin-top: 10px">
                             <input type="submit" name="submit" value="Next" class="btn btn-success" />
                               </div>
                           </div>
                                     
                                    </div>

                          
                                    

                         
                                
    </form>
                                
                                <!-- /.tab-pane -->
                            </div>
                        </div>
                    </div>
                  </div>
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
        </div>
                                               

                         
                            </div>
                     


        </section>  
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