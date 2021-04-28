<?php $this->view('vitals/left_nav'); ?>
          <div class="col-md-9">
<?php

if($patient_dt->referred_by_type=='WOM')
	{
		$referred_by = $patient_dt->referred_by;
	}
	else if($patient_dt->referred_by_type=='Doctor')
	{
		$refdoctor = $this->db->query("select * from referral_doctors where rfd_id=".$patient_dt->referred_by)->row();
		
		$referred_by = $refdoctor->doctor_name;
	}
	else if($patient_dt->referred_by_type=='Online')
	{
		$referred_by = $patient_dt->referred_by;
	}
	else{
		$referred_by = "";
	}
	echo'<table id="doctorlist" class="table table-striped dt-responsive nowrap">
     <thead>  
      <tr>
        <th>PROFILE</th>
        <th style="text-align:right;"><a class="btn btn-primary btn-xs" href='.base_url("patients/patient_update/".$patient_dt->patient_id).'>Edit Profile</a></th>
        <th style="text-align:right;">';
          if($patient_dt->photo!=""){
              echo'<img width="150" src="'.base_url('uploads/patients/'.$patient_dt->photo).'" >';
            }
  echo'</th>
      </tr>       
    </thead>
    <tbody>';  
  echo'
      <tr><td>DOB : </td><td>'.$patient_dt->date_of_birth.'</td></tr>
      <tr><td>AGE : </td><td>'.$patient_dt->age.'</td></tr>
       <tr><td>Gender : </td><td>'.$patient_dt->gender.'</td></tr>
      <tr><td>Mobile : </td><td>'.$patient_dt->mobile.'</td></tr>
      <tr><td>UMR NO : </td><td>'.$patient_dt->umr_no.'</td></tr>
      <tr><td>Address : </td><td>'.$patient_dt->address_line.'</td></tr>
      <tr><td>District : </td><td>'.$patient_dt->district_name.'</td></tr>
      <tr><td>State : </td><td>'.$patient_dt->state_name.'</td></tr>
      <tr><td>Pincode : </td><td>'.$patient_dt->pincode.'</td></tr>
	  <tr><td>Referred By : </td><td>'.$referred_by.'</td></tr>';
    echo'</tbody></table>';

?>
          </div>
           </div></div>
     
    </div>