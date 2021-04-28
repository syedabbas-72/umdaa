<style type="text/css">
  .radio label::after{
    top: 15px !important;
  }
</style>
<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">HOME</a></li>
          <li class="breadcrumb-item"><a href="#">SETTINGS</a></li>
          <li class="breadcrumb-item active"><a href="#">CLINIC</a></li>          
        </ol>
  </div>
</div>
              <!-- content start -->
<section class="main-content">
<div class="row">
  <div class="col-md-12">
    <!-- card start -->
    <div class="card">
      <!-- <div class="card-header card-default">Inline form</div> -->
       <div class="card-body">
    
             
                      <form method="POST" class="custom-form" action="<?php echo base_url('settings/clinic');?>" role="form">
                      
                 <div class="form-group row col-md-12">
                  <label for="clinic" class="col-sm-2 col-form-label">SELECT CLINIC</label>
                  <div class="col-sm-4">
                  <select name="clinic_name" class="form-control">
                    <?php foreach ($clinic_list as $key => $clinic) { ?>
                     <option value="<?php echo $clinic->clinic_id; ?>"><?php echo $clinic->clinic_name; ?></option>
                   <?php }
                    ?>
                  </select>
                  </div>
                </div>
              


                  <div class="row col-md-12">
				 
                    <div class="col-md-4"><div class="form-group">
                      <label for="registration_fee" class="col-form-label">REGISTRATION FEE<span style="color: red;">*</span></label>
                          <input id="registration_fee" name="registration_fee" type="text" placeholder="" class="form-control" required="">
                      </div>
                    </div>
                    <div class="col-md-4"><div class="form-group">
                      <label for="pharmacy_discount" class="col-form-label">PHARMACY DISCOUNT<span style="color: red;">*</span></label>
                          <input id="pharmacy_discount" name="pharmacy_discount" type="text" placeholder="" class="form-control" required="">
                      </div>
                    </div>
                   
                   
                    <div class="col-md-4"><div class="form-group">
                      <label for="lab_discount" class="col-form-label">LAB DISCOUNT<span style="color: red;">*</span></label>
                        <input id="lab_discount" name="lab_discount" type="text" placeholder="" class="form-control" required="">
                      </div>
                    </div>
                  </div> 
                  <div class="row col-md-12">
                    <div class="col-md-6">
                        <div class="pull-right">
                          <input type="submit" name="submit" value="Save" class="btn btn-success"> 
                        </div>
                    </div>

                  </div> 
                    </form>
                
                  </div>                
              
    
      </div>
    </div>
  </div>
</section>
<script>
$(function () {
        
        $("#package_subscription_date,#package_subscription_date1,#package_subscription_date").datepicker({dateFormat: "yy-mm-dd", changeYear: true, yearRange: "-100:+0"});
        
    });
</script>       
      
