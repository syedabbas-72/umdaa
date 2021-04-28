<style type="text/css">
  .table > tbody > tr > td, .table > tbody > tr > th{
    border:none !important;
  }
</style>
<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">HOME</a></li>
          <li class="breadcrumb-item"><a href="<?php echo base_url('settings'); ?>">SETTINGS</a></li>
          <li class="breadcrumb-item active"><a href="#">DOCTOR </a></li>          
        </ol>
  </div>
</div>
              <!-- content start -->
<section class="main-content">
<div class="row">
               
                <div class="col-md-12">
            
           <div class="card">
                        <div class="card-body">
           
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
                 <div class="row col-md-12">
                    <div class="form-group col-md-6">
                                        <label>About Yourself</label>
                                            <textarea name="about" class="form-control" rows="3" style="height: 100px"></textarea>
                                    </div>
             <div class="form-group col-md-6">
                                        <label>Language can communicate</label>
          
                      <div class="form-inline">
                                           <div class="checkbox checkbox-inline checkbox-success">
                          <input id="checkbox10" type="checkbox" name="language[]" checked="" value="telugu">
                          <label for="checkbox10"> Telugu </label>
                        </div>
                                            <div class="checkbox checkbox-inline checkbox-success">
                          <input id="checkbox11" name="language[]" type="checkbox" value="hindi">
                          <label for="checkbox11"> Hindi </label>
                        </div>
                                            <div class="checkbox checkbox-inline checkbox-success" >
                          <input id="checkbox12" name="language[]" type="checkbox" value="english">
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
   if(count($doctor_info)>0){
  $qualification= explode(",",$doctor_info->qualification);
   for($i=0;$i<count($qualification);$i++){
 ?>
<tr>
<td><input type="text" name="Degree[0][degree]" value = "<?php echo $qualification[$i];?>"></td>
<td><input type="text" name="Degree[0][college]" value = "<?php echo $doctor_info->university;?>"></td>
<td><input type="text" name="Degree[0][year]" value = "<?php echo $doctor_info->year_of_passing;?>"></td>
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

<tr>
<td><input type="text" name="membership[]" value = "<?php echo $doctor_info->membership_in;?>"></td>
<td></td>
</tr>

 
</tbody></table>

 </div>
</div>

 <!-- <div class="row col-md-12">
                  <div class="form-group col-md-12">
                    <label>FEEDBACK</label>
                    <textarea name="feedback" class="form-control" rows="3" style="height: 150px"></textarea>
                  </div>
                                  </div> -->


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
            </div>
</section>
<script>
// $(function () {
        
//         $("#package_subscription_date,#package_subscription_date1,#package_subscription_date").datepicker({dateFormat: "yy-mm-dd", changeYear: true, yearRange: "-100:+0"});
        
//     });
</script>       
      
<script type="text/javascript">
  $(document).on('click','#add_row',function(){
      var length = $('#add_degree').find('tr').length;
      var i = length;
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