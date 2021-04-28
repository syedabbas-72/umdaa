<div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?php echo $_SESSION['clinic_name']; ?></div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                              
                                <li class="active">Patient Profile</li>
                            </ol>
                        </div>
                    </div>
        <div class="card">
        <div class="card-body">
        <div class="row col-md-12"> 
          <div class="col-md-3" id="view_casesheet">
      <div class="col-md-12">
      
      <div class="form-group ulgroup" >
        <ul>
          <?php
      foreach($profile_pages as $keys=>$values){
        ?><li class="ligroup" id="<?php echo $values->user_entity_alias; ?>"><a href="<?php echo base_url($values->entity_url);?>/<?php echo $patient_id; ?>/<?php echo $appointment_id; ?>"><?php echo $values->user_entity_name; ?></a></li>
      <?php }?>
        </ul>
      </div>
     </div>
      
      </div>
          <div class="col-md-9" id="view_caseresults" class="view_caseresults">

          <table id="doctorlist" class="table table-striped dt-responsive nowrap">
     <thead>  
      <tr>
        <th>PROFILE</th>
        <th style="text-align:right;"><a class="btn btn-primary btn-xs" href='<?php echo base_url("patients/patient_update/".$patient_id); ?>'>Edit Profile</a></th>
      </tr>       
    </thead>
    <tbody>  
<tr><td>Name</td><td><?php echo $patient_data->first_name.' '.$patient_data->last_name; ?></td></tr>
      <tr><td>DOB : </td><td><?php echo $patient_dt->date_of_birth; ?></td></tr>
      <tr><td>AGE : </td><td><?php echo $patient_dt->age; ?></td></tr>
       <tr><td>Gender : </td><td><?php echo $patient_dt->gender; ?></td></tr>
      <tr><td>Mobile : </td><td><?php echo $patient_dt->mobile; ?></td></tr>
      <tr><td>UMR NO : </td><td><?php echo $patient_dt->umr_no; ?></td></tr>
      <tr><td>Address : </td><td><?php echo $patient_dt->address_line; ?></td></tr>
      <tr><td>District : </td><td><?php echo $patient_dt->district_name; ?></td></tr>
      <tr><td>State : </td><td><?php echo $patient_dt->state_name; ?></td></tr>
      <tr><td>Pincode : </td><td><?php echo $patient_dt->pincode; ?></td></tr>
    </tbody></table>
         
         
        </div>

       

 </div>
     </div>
    </div>

