
<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">HOME</a></li>
          <li class="breadcrumb-item"><a href="<?php echo base_url('settings'); ?>">SETTINGS</a></li>
          <li class="breadcrumb-item active"><a href="#">DOCTOR WORK INFO</a></li>          
        </ol>
  </div>
</div>

        <section class="main-content">
          <div class="row">
        <div class="col-2">
            <?php $this->view("doctor_settings/left_nav"); ?>
        </div>
        <div class="col-10">
          
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="tab-content">
                                        <a href="<?php echo base_url('doctor_settings'); ?>" class = "btn btn-primary" style="float: right;margin-bottom: 5px;">BACK TO CLINICS</a>
                             
                                <div class="tab-pane active" id="info">  <div class="widget white-bg">
                 
                 <form method="POST" action="<?php echo base_url('doctor/update_profile/'.$doctor_info->doctor_id);?>" enctype="multipart/form-data" role="form">
              <div class="row col-md-12">
                          
      <div class="card" style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
    <div class="card-header" style="font-size: 15px;padding: 10px 20px">PROFILE INFORMATION</div>
  </div>

                          </div>
               <div class="row col-md-12">
              <div class="form-group col-md-6">
                                        <label>First Name</label>
                                            <input name="first_name" type="text" class="form-control" value="<?php echo $doctor_info->first_name; ?>">
                                    </div>
                                 

                                    <div class="form-group col-md-6">
                                        <label>Last Name</label>
                                            <input name="last_name" type="text" class="form-control" value="<?php echo $doctor_info->last_name; ?>">
                                    </div>

                </div>
                <div class="row col-md-12">
              <div class="form-group col-md-6">
                                        <label>Registration Number</label>
                                            <input name="reg_number" type="text" class="form-control" value="<?php echo $doctor_info->registration_code; ?>">
                                    </div>
                                 

                                    <div class="form-group col-md-6">
                                        <label>Speciality</label>
                                            <input name="speciality" type="text" class="form-control" value="<?php echo $doctor_info->department_name; ?>">
                                    </div>
                                    
                </div>
                  <?php $languages = explode(",",$doctor_info->languages);?>
                 <div class="row col-md-12">
                    <div class="form-group col-md-6">
                                        <label>About Yourself</label>
                                            <textarea name="about" class="form-control" rows="3" style="height: 100px"></textarea>
                                    </div>
             <div class="form-group col-md-6">
                                        <label>Language can communicate</label>
          
                      <div class="form-inline">
                                           <div class="checkbox checkbox-inline checkbox-success">
                                            <?php if(in_array('telugu',$languages)) { ?>
                          <input id="checkbox10" type="checkbox" name="language[]" checked value="telugu">
                          <?php }else { ?>
                            <input id="checkbox10" type="checkbox" name="language[]"  value="telugu">

                          <?php } ?>
                          <label for="checkbox10"> Telugu </label>
                        </div>
                                            <div class="checkbox checkbox-inline checkbox-success">
                                               <?php if(in_array('hindi',$languages)) { ?>
                          <input id="checkbox11" name="language[]" type="checkbox" checked value="hindi">
                            <?php }else { ?>
                               <input id="checkbox11" name="language[]" type="checkbox" value="hindi">
                              <?php } ?>
                          <label for="checkbox11"> Hindi </label>
                        </div>
                                            <div class="checkbox checkbox-inline checkbox-success" >
                                              <?php if(in_array('english',$languages)) { ?>
                          <input id="checkbox12" name="language[]" type="checkbox" checked value="english">
                           <?php }else { ?>

                            <input id="checkbox12" name="language[]" type="checkbox" value="english">
                             <?php } ?>
                          <label for="checkbox12"> English </label>
                        </div>
                                      </div>
                                    </div>

                                    
                                    </div>

                                    <div class = "row col-md-12">
                                      <div class="form-group col-md-4" style="margin-left: 0px;">
                                      <label>Experience</label>
                                            <input type="text" class = "form-control" name = "experience" value = "<?php echo $doctor_info->experience;?>">
                                    </div>
                                    </div>
                                  
                                   <div class="row col-md-12">
                  <div class="form-group col-md-12">
                    <label>Profile Image</label>
                    <div class="fileinput-new" data-provides="fileinput">
                      <div class="fileinput-preview" data-trigger="fileinput" style="width: 200px; height:150px;"><img style="width:100%;" src="<?php echo base_url('uploads/profile_image/'.$doctor_info->profile_image);?>" /></div>
                      <span class="btn btn-primary  btn-file">
                        <span class="fileinput-new">Select</span>
                        <span class="fileinput-exists">Change</span>
                         <input type="hidden" value="<?= $doctor_info->profile_image;?>">
                        <input type="file" id="image" name="profile_image" value="<?php echo $doctor_info->profile_image; ?>">
                      </span>
                      <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">Remove</a>
                    </div>
                  </div>

                                  </div>
                                    <div class="row col-md-12">
                  <div class="form-group col-md-12">
                    <label>Diseases Dealt With</label>
                    <textarea name="diseases_dealt_with" class="form-control" rows="3" style="height: 100px"></textarea>
                  </div>
                                  </div>
                  <!--                  <div class="row col-md-12">
                  <div class="form-group col-md-12">
                    <label>Services And Facilities Provided</label>
                    <textarea name="Services_provided" class="form-control" rows="3"></textarea>
                  </div>
                                  </div> -->
                                 <div class="row col-md-12">
                          
      <div class="card" style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
    <div class="card-header" style="font-size: 15px;padding: 10px 20px">PRACTICE ADDRESS</div>
  </div>

                          </div>
                                  <div class="row col-md-12">
                  <div class="form-group col-md-12">
                   
                      <div class="form-group col-md-6">
                                        <label>Clinic Name</label>
                                            <input name="clinic_name" type="text" readonly class="form-control" value="<?php echo $doctor_info->clinic_name; ?>">
                                    </div>
                                 

                                    <div class="form-group col-md-6">
                                        <label>Address</label>
                                            <textarea style="height:100px;" name="clinic_address" readonly type="text" class="form-control"><?php echo $doctor_info->address; ?></textarea>
                                    </div>
                  </div>
                                  </div>


                               
<div class="row col-md-12">
                          
      <div class="card" style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
    <div class="card-header" style="font-size: 15px;padding: 10px 20px">DEGREES<i class="fa fa-plus" aria-hidden="true" style="float: right;" id = "add_row"></i></div>


  </div>

                          </div>
                                  <div class="row col-md-12">
<div class="form-group col-md-12">

                                    <tr>
 
<td>
<table>
 
<tbody id = "add_degree"><tr>

<td style="width: 30%" align="center"><b>Degree</b></td>
<td style="width: 60%" align="center"><b>College/University</b></td>
<td align="center"><b>Year</b></td>
</tr>
 <?php
   if(count($degrees)>0){

   for($i=0;$i<count($degrees);$i++){
 ?>
<tr>
<td><input type="text" name="Degree[<?php echo $i; ?>][degree]" value = "<?php echo $degrees[$i]->degree_name;?>"></td>
<td><input type="text" name="Degree[<?php echo $i; ?>][college]" value = "<?php echo $degrees[$i]->university;?>"></td>
<td><input type="text" name="Degree[<?php echo $i; ?>][year]" value = "<?php echo $degrees[$i]->year;?>"></td>
</tr>
<?php } } else { ?>

  <tr>
<td><input type="text" name="Degree[0][degree]"></td>
<td><input type="text" name="Degree[0][college]"></td>
<td><input type="text" name="Degree[0][year]"></td>
</tr>

<?php } ?>


 
</tbody></table>
 
</td>
</tr></div>


                                  </div>
<div class="row col-md-12">
                          
      <div class="card" style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
    <div class="card-header" style="font-size: 15px;padding: 10px 20px">ACHIEVEMENTS<i class="fa fa-plus" aria-hidden="true" style="float: right;" id = "add_acheviement"></i></div>
  </div>

                          </div>
                                  <div class="row col-md-12">
<div class="form-group col-md-12">
                       
<table class="table">
 
<tbody id = "add_acheviement_row">

  <?php
  if(count($doctor_info->acheivements)>0) { 
   $acheivements = explode(",",$doctor_info->acheivements);
   for($j=0;$j<count($acheivements);$j++){
   ?>
<tr>
<td><input type="text" name="achievements[]" value = "<?php echo $acheivements[$j];?>"></td>
<td></td>
</tr>
<?php } } else { ?>
<tr>
<td><input type="text" name="achievements[]"></td>
<td></td>
</tr>


<?php } ?>

 
</tbody></table>

 </div>
</div>
<div class="row col-md-12">
                          
      <div class="card" style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
    <div class="card-header" style="font-size: 15px;padding: 10px 20px">MEMBERSHIP<i class="fa fa-plus" aria-hidden="true" style="float: right;" id = "add_membership"></i></div>
  </div>

                          </div>
                                  <div class="row col-md-12">
<div class="form-group col-md-12">
                  
                     
<table class="table">
 
<tbody id = "add_membership_row">

 <?php
  if(count($doctor_info->membership_in)>0) { 
   $membership_in = explode(",",$doctor_info->membership_in);
   for($j=0;$j<count($membership_in);$j++){
   ?>
<tr>
<td><input type="text" name="membership[]" value = "<?php echo $membership_in[$j];?>"></td>
<td></td>
</tr>
<?php } } else { ?>
<tr>
<td><input type="text" name="membership[]"></td>
<td></td>
</tr>


<?php } ?>

 
</tbody></table>

 </div>
</div>




                                  </div>
                                  <div class="col-sm-6">
                                  <div class="pull-right">
                                      <input type="submit" value="Save" name="submit" class="btn btn-success btn-rounded box-shadow">
                                  </div>
                                </div>
            </form>
                                        </div>
                                 </div>
                                    

                                </div>
                                
    
                                
                                <!-- /.tab-pane -->
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
  $(document).on('click','#add_row',function(){
      var length = $('#add_degree').find('tr').length;
      var i = length - 1;
       $('#add_degree').append('<tr id = "degree_'+length+'"><td><input type="text" name="Degree['+i+'][degree]"></td><td><input type="text" name="Degree['+i+'][college]" ></td><td><input type="text" name="Degree['+i+'][year]" ></td><td><i class="fa fa-minus" aria-hidden="true" onclick = "del_degree('+length+')"></i></td></tr>');
  });
$(document).on('click','#add_acheviement',function(){
       var length = $('#add_acheviement_row').find('tr').length;
       $('#add_acheviement_row').append('<tr id = "acheviement_'+length+'"><td><input type="text" name="achievements[]"></td><td><i class="fa fa-minus" aria-hidden="true" onclick = "del_acheivements('+length+')"></i></td></tr>');
  });
$(document).on('click','#add_membership',function(){
        var length = $('#add_membership_row').find('tr').length;
       $('#add_membership_row').append('<tr id = "membership_'+length+'"><td><input type="text" name="membership[]" ></td><td><i class="fa fa-minus" aria-hidden="true" onclick = "del_membership('+length+')"></i></td></tr>');
  });

</script>
<script type="text/javascript">
  function del_degree(length)
  {
    $('#degree_'+length).remove();
  }
  function del_acheivements(length)
  {
    $('#acheviement_'+length).remove();
  }
  function del_membership(length)
  {
    $('#membership_'+length).remove();
  }
</script>