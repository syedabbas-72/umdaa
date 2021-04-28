<style>
	.row1{
		padding:8px;
		border-bottom: 1px dotted #ccc;
		border-top: 1px dotted #ccc;
	}
	#demo{
		font-size: 18px;
	}
</style>
<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">HOME</a></li>
          <li class="breadcrumb-item"><a href="#">CLINIC</a></li>
          <li class="breadcrumb-item active"><a href="#">VIEW</a></li>          
        </ol>
  </div>
</div>

        <section class="main-content">
            <div class="row">             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                                                   
                           <div class="row col-md-12">
                           <label for="demographic" id="demo"> <b>CLINIC INFORMATION</b></label>
                          </div>
                          <div class="table-responsive">
                          	<table class="" style="width:100%;"><tbody>
                          		
                          	 <tr>
                          		<td class="row1"><label for="clinic_name" class="col-form-label">CLINIC NAME :&nbsp;<b><?php echo $clinic_list->clinic_name; ?></b></label></td>
                               
                                <td class="row1"><label for="clinic_type" class="col-form-label">CLINIC TYPE:&nbsp;<b><?php echo $clinic_type->clinic_type; ?></b></label></td>
                            </tr>
                            <tr>
                          		<td class="row1"><label for="clinic_phone" class="col-form-label">CLINIC PHONE:&nbsp;<b><?php echo $clinic_list->clinic_phone; ?></b></label></td>
                               
                                <td class="row1"><label for="clinic_email" class="col-form-label">CLINIC E-MAIL:&nbsp;<b><?php echo $clinic_list->email; ?></b></label></td>
                            </tr>
                            <tr>
                          		<td class="row1"><label for="address" class="col-form-label">ADDRESS:&nbsp;<b><?php echo $clinic_list->address; ?></b></label> </td>
                               
                                <td class="row1"><label for="district" class="col-form-label">DISTRICT:&nbsp;<b><?php echo $clinic_list->address; ?></b></label></td>
                            </tr>
                            <tr>
                          		<td class="row1"><label for="state" class="col-form-label">STATE:&nbsp;<b><?php echo $state_list->state_name; ?></b></label> </td>
                               
                                <td class="row1"><label for="pincode" class="col-form-label">PIN CODE&nbsp;<b><?php echo $clinic_list->pincode; ?></b></label></td>
                            </tr>
                            <tr>
                          		<td class="" style="padding:8px;"><label>CLINIC LOGO:</label>
                                  <div class="fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-preview" data-trigger="fileinput" style="width: 200px; height:150px;"><img style="width:100%;" src="<?php echo base_url('uploads/clinic_logos/'.$clinic_list->clinic_logo);?>" /></div>
                               
                                  </div></td>
                          	</tr>
                          	<tr><td class="" colspan="2">
                          		<label for="demographic" id="demo" style="margin-top:10px;"><b>CLINIC INCHARGE INFORMATION</b></label>
                          	</td></tr>
                          	<tr>
                          		<td class="row1"><label for="clinic_head" class="col-form-label">CLINIC INCHARGE:&nbsp;<b><?php echo $clinic_list->incharge_email; ?></b></label>  </td>
                               
                                <td class="row1"><label for="incharge_mobile" class="col-form-label">INCHARGE MOBILE:&nbsp;<b><?php echo $clinic_list->incharge_mobile; ?></b></label></td>
                            </tr>
                            <tr>
                          		<td class="row1" colspan="2"><label for="incharge_email" class="col-form-label">E-MAIL:&nbsp;<b><?php echo $clinic_list->address; ?></b></label>   </td>
                            </tr>
                            
                                </tbody>
                               </table>    
                                    
                            </div>

                            <div class="row" style="margin-top: 20px;">
                              <div class="col-md-6">
                                <h4 style="font-weight: bold">DOCTORS</h4>
                                <table class = "table datatable">
                                  <thead style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
                                    <tr>
                                  <th>NAME</th>
                                  <th>DEPARTMENT</th>
                                  <th>REGISTRATION CODE</th>
                                  <th>ACTION</th>
                                </tr>
                              </thead>
                                  <tbody>
                                    <?php for($i=0;$i<count($clinic_doctor);$i++) { ?>
                                      <tr>
                                        <td>DR.<?php echo strtoupper($clinic_doctor[$i]->first_name).' '.strtoupper($clinic_doctor[$i]->last_name);?><br><small><?php echo $clinic_doctor[$i]->qualification; ?></small></td>
                                        <td><?php echo $clinic_doctor[$i]->department_name;?></td>
                                        <td><?php echo strtoupper($clinic_doctor[$i]->registration_code);?></td>
                                        
                                        <td>
                                          <a href = "<?php echo base_url('settings/doctor_info/'.$clinic_doctor[$i]->doctor_id)?>">EDIT</a>
                                        
                                        </td>
                                      </tr>
                                    <?php } ?>
                                    
                                  </tbody>
                                  
                                </table>
                              </div>

                              <div class="col-md-6">
                                <h4 style="font-weight: bold">EMPLOYEES</h4>
                                <table class = "table datatable">
                                <thead style="margin-bottom: 10px;padding: 0 !important;width: 100%;background-color: rgba(0,0,0,.03);box-shadow: none !important">
                                  <tr>
                                <th>NAME</th>
                                
                                <th>EMPLOYEE CODE</th>
                                <th>ROLE</th>
                                <th>ACTION</th>
                              </tr>
                            </thead>
                                <tbody>
                                  <?php for($i=0;$i<count($staff);$i++) { 

                                    ?>
                                    <tr>
                                      <td data-toggle="tooltip" data-placement="top" title="front office, pharmacy"><?php echo strtoupper($staff[$i]->first_name).' '.strtoupper($staff[$i]->last_name);?></td>
                                      <td><?php echo $staff[$i]->employee_code;?></td>
                                      <td><?php echo $staff[$i]->role_name;?></td>
                                      
                                      
                                      <td>
                                        <a href = "<?php echo base_url('employee/employee_update/'.$staff[$i]->employee_id)?>">EDIT</a>
                                       
                                      </td>
                                    </tr>
                                  <?php } ?>
                                  
                                </tbody>
                                
                              </table>
                              </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>  
       
<script type="text/javascript">
  $(document).ready(function(){
    $('.datatable').dataTable();
  });
</script>  
       
