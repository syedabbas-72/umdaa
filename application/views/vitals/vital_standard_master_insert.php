<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item"><a href="#">VITAL VIEW</a></li>
         <?php if(isset($vital_standard_master_val) && !empty($vital_standard_master_val)){?>
          <li class="breadcrumb-item active"><a href="#">EDIT</a></li>   
          <?php }else{?>
          	<li class="breadcrumb-item active"><a href="#">ADD</a></li>   
          <?php } ?>       
        </ol>
  </div>
</div>
              <!-- content start -->
<section class="main-content">
<div class="row">
  <div class="col-md-12">
  	<div class="card">
      <!-- <div class="card-header card-default">Inline form</div> -->
       <div class="card-body">
       	<?php if(isset($vital_standard_master_val) && !empty($vital_standard_master_val)){?>
       	<?php echo form_open('PatientsVital/vital_standard_master_edit/'.$vital_standard_master_val->vital_sign_standard_id.''); ?>
       	<?php }else{ ?>
       		<?php echo form_open('PatientsVital/vital_standard_master_insert/'.$vital_sign_id.''); ?>
       <?php } ?>

       	<div class="row col-md-12">
                    <div class="col-md-6"><div class="form-group">
                        <label for="parameter" class="col-form-label">Parameter</label>
                        <input id="parameter" name="parameter" type="text" placeholder="" class="form-control" value="<?php echo $vital_standard_master_val->parameter ;?>">
                      </div>
                    </div>
                    <input type="hidden" name="vital_sign_id" value="<?php echo $vital_standard_master_val->vital_sign_id ;?>">
                    <div class="col-md-6"><div class="form-group">
                      <label for="age_from" class="col-form-label">Age From</label>
                          <input id="age_from" name="age_from" type="number" placeholder="" class="form-control" value="<?php echo $vital_standard_master_val->age_from ;?>">
                      </div>
                    </div>
                  </div> 
                  <div class="row col-md-12">
                    <div class="col-md-6"><div class="form-group">
                        <label for="age_to" class="col-form-label"> Age To</label>
                        <input id="age_to" name="age_to" type="number" placeholder="" class="form-control" value="<?php echo $vital_standard_master_val->age_to ;?>">
                      </div>
                    </div>
                    
                    <div class="col-md-6"><div class="form-group">
                      <label for="age_type" class="col-form-label">Age Type</label>
                          <select id="age_type" name="age_type"  placeholder="" class="form-control">
                            <option value="">--Select--</option>
                            <option value="MONTHS" <?php if($vital_standard_master_val->age_type == "MONTHS"){echo "selected";}?>>MONTHS</option>
                            <option value="YEARS" <?php if($vital_standard_master_val->age_type == "YEARS"){echo "selected";}?>>YEARS</option>
                          </select>
                      </div>
                    </div>
                  </div> 
                  <div class="row col-md-12">
                    <div class="col-md-6"><div class="form-group">
                        <label for="value_low" class="col-form-label">Value Low</label>
                        <input id="value_low" name="value_low" type="text" placeholder="" class="form-control" value="<?php echo $vital_standard_master_val->value_low ;?>">
                      </div>
                    </div>
                    
                    <div class="col-md-6"><div class="form-group">
                      <label for="value_high" class="col-form-label">Value High</label>
                           <input id="value_high" name="value_high" type="text" placeholder="" class="form-control" value="<?php echo $vital_standard_master_val->value_high ;?>">
                      </div>
                    </div>
                  </div> 
                  <div class="row col-md-12">
                    
                    <div class="col-md-6"><div class="form-group">
                      <label for="status" class="col-form-label">Status</label>
                          <select id="status" name="status"  placeholder="" class="form-control">
                            <option value="">--Select--</option>
                            <option value="1" <?php if($vital_standard_master_val->status == "1"){echo "selected";}?>>Active</option>
                            <option value="0" <?php if($vital_standard_master_val->status == "0"){echo "selected";}?>>Inactive</option>
                          </select>
                      </div>
                    </div>
                  </div> 
                  <div class="row col-md-12">
                    <div class="col-md-6">
                        <div class="pull-right">
                          <input type="submit" name="submit" value="Save" class="btn btn-success"> 
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="pull-left">
                         <input type="submit" value="cancel" class="btn btn-success">
                        </div>
                    </div>
                  </div>                
                </form>
       </div>
   </div>
  </div>
</div>
</section>
