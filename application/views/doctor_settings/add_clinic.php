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
            <li><a class="parent-item" href="<?php echo base_url('doctor_settings'); ?>">DOCTOR SETTINGS</a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li class="active">ADD NEW CLINIC</li>
        </ol>
    </div>
</div>

    
          <div class="row">
       
        <div class="col-12">
           <div class="card">
                <div class="card-body">
                  <form method="POST" action="<?php echo base_url('doctor_settings/search_clinic');?>" enctype="multipart/form-data" role="form">
                    <div class="row col-md-12">
                       
                        <div class="col-md-6">
                                 <div class="form-group">
                                      
                              <input type="text" class="form-control" name="clinic_name" value="">
                                     </div>
                                   </div>
                                   <div class="col-md-3">
                                    <input type="submit" name="submit" value="Search clinic" class="btn btn-success" />
                                   </div>
                                  
                                   </div>
                                    </form>
                                    </div>

                          
                                    

                         
                                
    
                                
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