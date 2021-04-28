<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#"><?php echo $_SESSION['clinic_name']; ?></a></li>
          <li class="breadcrumb-item"><a href="#">VITAL</a></li>
         <?php if(isset($patient_vital_val) && !empty($patient_vital_val)){?>
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
       	<?php if(isset($patient_vital_val) && !empty($patient_vital_val)){?>
       	<?php echo form_open('PatientsVital/vital_masters_edit/'.$patient_vital_val->vital_sign_id.''); ?>
       	<?php }else{ ?>
       		<?php echo form_open('PatientsVital/vital_masters_insert'); ?>
       <?php } ?>

       	<div class="row col-md-12">
                    <div class="col-md-6"><div class="form-group">
                        <label for="vital_sign" class="col-form-label">Vital Sign</label>
                        <input id="vital_sign" name="vital_sign" type="text" placeholder="" class="form-control" value="<?php echo $patient_vital_val->vital_sign ;?>">
                      </div>
                    </div>
					
					<div class="col-md-6"><div class="form-group">
                        <label for="low_range" class="col-form-label">Low Range</label>
                        <input id="low_range" name="low_range" type="text" placeholder="" class="form-control" value="<?php echo $patient_vital_val->low_range ;?>">
                      </div>
                    </div>
					
					<div class="col-md-6"><div class="form-group">
                        <label for="high_range" class="col-form-label">High Range</label>
                        <input id="high_range" name="high_range" type="text" placeholder="" class="form-control" value="<?php echo $patient_vital_val->high_range ;?>">
                      </div>
                    </div>
                    
                    <div class="col-md-6"><div class="form-group">
                      <label for="status" class="col-form-label">STATUS</label>
                          <select id="status" name="status"  placeholder="" class="form-control">
                          	<option value="">--Select--</option>
                          	<option value="1" <?php if($patient_vital_val->status == "1"){echo "selected";}?>>Active</option>
                          	<option value="0" <?php if($patient_vital_val->status == "0"){echo "selected";}?>>Inactive</option>
                          </select>
                      </div>
                    </div>
                  </div> 
                  <div class="row col-md-12">
                    <div class="col-md-6">
                        <div class="pull-right">
                          <input type="submit" name="submit" value="Save" class="btn btn-warning"> 
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
