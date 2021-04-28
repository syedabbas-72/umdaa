<style type="text/css">
  .table > tbody > tr > td, .table > tbody > tr > th{
    border:none !important;
  }
</style>
<div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?php echo $_SESSION['clinic_name']; ?></div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                              
                                <li class="active">PROFILE</li>
                            </ol>
                        </div>
                    </div>
              <!-- content start -->

          <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="card card-box">
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
                                   <div class="row col-md-12">
                  <div class="form-group col-md-12">
                    <label>Profile Image</label>
                    <div class="compose-editor">
                                    <div class="fileinput-preview" data-trigger="fileinput" style="width: 200px; height:150px;"><img style="width:100%;" src="<?php echo base_url('uploads/clinic_logos/'.$doctor_info->profile_image);?>" /></div>
                                                  <input type="file" name="profile_image" value="<?= $doctor_info->profile_image;?>" class="default" multiple="">
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
    <div class="card-header" style="font-size: 15px;padding: 10px 20px">DEGREES</div>
  </div>

                          </div>
                                  <div class="row col-md-12">
<div class="form-group col-md-12">

                                    <tr>
 
<td>
<table>
 
<tbody><tr>

<td style="width: 30%" align="center"><b>Degree</b></td>
<td style="width: 60%" align="center"><b>College/University</b></td>
<td align="center"><b>Year</b></td>
</tr>
 
<tr>
<td><input  class="col-md-12" type="text" name="Degree[0][degree]"></td>
<td><input class="col-md-12" type="text" name="Degree[0][college]" ></td>
<td><input class="col-md-12"  type="text" name="Degree[0][year]" ></td>
</tr>

 
</tbody></table>
 
</td>
</tr></div>


                                  </div>
<div class="row col-md-12">
                          
      <div class="card" style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
    <div class="card-header" style="font-size: 15px;padding: 10px 20px">ACHIEVEMENTS</div>
  </div>

                          </div>
                                  <div class="row col-md-12">
<div class="form-group col-md-12">
                       
<table class="table">
 
<tbody>

<tr>
<td><input  class="col-md-12" type="text" name="achievements[]" ></td>
</tr>


 
</tbody></table>

 </div>
</div>
<div class="row col-md-12">
                          
      <div class="card" style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
    <div class="card-header" style="font-size: 15px;padding: 10px 20px">MEMBERSHIP</div>
  </div>

                          </div>
                                  <div class="row col-md-12">
<div class="form-group col-md-12">
                  
                     
<table class="table">
 
<tbody>

<tr>
<td><input class="col-md-12" type="text" name="membership[]" ></td>
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

<script>
// $(function () {
        
//         $("#package_subscription_date,#package_subscription_date1,#package_subscription_date").datepicker({dateFormat: "yy-mm-dd", changeYear: true, yearRange: "-100:+0"});
        
//     });
</script>       
      
