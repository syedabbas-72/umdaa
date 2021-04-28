
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta information -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="author" content="">

    <style>
    #daytxt { font-family: freeserif; }
</style>
</head>
<body>
	<table cellpadding="0" cellspacing="0" style="width:800px; height:100%; font-family: segoe ui; font-size:20px; color: #333;" align="center" >
		<!-- header -->
		<tr>
			<td style="border-bottom:1px solid #ccc; padding:15px 10px;">
				<table cellspacing="0" cellpadding="0">
					<tr>
						<td style="width: 100px;"><img src="<?php echo base_url('uploads/clinic_logos/'.$appointments->clinic_logo);?>" /></td>
						<td style="width:310px; text-align: left">
						</td>
						<td style="width:300px">
							<span style="color:#000;"><b>Address</b></span><br>
							<?php echo $appointments->address;  ?>
							<span style="color:#000; font-weight: 600">Phone:</span> <?php echo $appointments->clinic_phone; ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style="border-bottom: 1px solid #ccc; padding:15px 10px 20px 10px">
				<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
					<tr>
						<td style="width:300px">
							<?php 
							$patientName = '';
							if($appointments->title){
								$patientName = ucwords($appointments->title).". ";
							}
							$patientName .= $appointments->pname." ".$appointments->plname;
							?>
							<span style="color:#000; font-weight: 600; line-height: 25px;"><b><?php echo strtoupper($patientName); ?></b></span><br>
							<b>Patient Id:</b>&nbsp;<?php echo $appointments->umr_no; ?><br>
							<?php 
							$moreInfo = '';
							if($appointments->gender) { 
								$moreInfo .= ucwords(substr($appointments->gender, 0, 1));
							} 
							if($appointments->age) { 
								if($moreInfo){
									$moreInfo .= ", ";
								}
								$moreInfo .= $appointments->age." ".ucwords(substr($appointments->age_unit, 0, 1));
							} 							
							?>
							<p><?php echo $moreInfo; ?></p>
						</td>
						<td style="width:610px;text-align: center;vertical-align: top">
							<?php if($appointments->address_line != ''){ ?>
							<b>Address:</b><br><p><?php echo $appointments->address_line; ?></p>
							<?php } ?>
						</td>
						<td>
							<span style="color:#000; font-weight: 600; line-height: 25px;"><b><?php echo "Dr. ".strtoupper($doctor_info->first_name." ".$doctor_info->last_name); ?></b></span><br><span style="color:#000; font-weight: 600; line-height: 25px;"><?php echo strtoupper($doctor_info->qualification. ", ". $doctor_info->department_name); ?> </span><br>
							<span style="color:#000; font-weight: 600; line-height: 25px;"><b>Reg. No:</b> &nbsp;<?php echo $doctor_info->registration_code; ?> </span>							
						</td>
					</tr>
				</table>
			</td>
		</tr>
		
		<tr>
			<td style="border-bottom: 1px solid #ccc">
				<?php 
				// get the last appointment id
				
				?>
				<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">
					<?php 
					// get latest vitals signs records
					//$res_vital_sign = $this->db->query("SELECT vital_sign, vital_result, patient_id, vital_sign_recording_date_time FROM `patient_vital_sign` WHERE vital_sign_recording_date_time =(select max(vital_sign_recording_date_time) FROM `patient_vital_sign` where patient_id = '".$appointments->patient_id."') and vital_result != '' and sign_type = 'generic' ORDER BY FIELD(vital_sign, 'PR','SBP','DBP','SaO2','Temp','Weight','Height','RR','BMI','BSA','WH_ratio')")->result();
					$res_vital_sign = $this->db->query("SELECT a.vital_sign, a.vital_result, a.patient_id, a.vital_sign_recording_date_time,b.unit FROM `patient_vital_sign` a inner join vital_sign b on(a.vital_sign = b.short_form)  WHERE a.vital_sign_recording_date_time =(select max(a.vital_sign_recording_date_time) FROM `patient_vital_sign` a where a.patient_id = '".$appointments->patient_id."' and a.clinic_id = '".$appointments->clinic_id."') and a.vital_result != '' and a.sign_type = 'generic' ORDER BY a.patient_vital_id ASC")->result();
					?>
					<tr>
						<?php 
						$BP = '';
						foreach ($res_vital_sign as $value) { 

							if($value->vital_sign == 'SBP' && $value->vital_result != ''){
								$BP = $value->vital_result." / ";
							}elseif($value->vital_sign == 'DBP' && $value->vital_result != ''){
								$BP .= $value->vital_result;
								?>
								<td style="padding:0px 10px">
									<span style="font-weight: bold;">BP</span> : <?php echo $BP; ?> mmHG</span> 
								</td>
								<?php 
							}else{
							?>							
						<td style="padding:0px 10px">
							<span style="font-weight: bold;"><?php echo $value->vital_sign; ?></span> : <?php echo round($value->vital_result,2); ?> <?php echo $value->unit; ?> </span> 
						</td>
						<?php }
						} 
						?>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">
					<tr>
					
						
						<td style="width:100%;">
							<span style="font-weight: bold;">Drug Allergy </span> : <?php echo $appointments->allergy; ?> </span> 
						</td>
				
					</tr>

				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">
					<tr>
						<td style="width:100%;">
							<?php
							$reviewDays = $this->db->query("SELECT review_days FROM clinic_doctor WHERE clinic_id = '".$appointments->clinic_id."' AND doctor_id = '".$doctor_info->doctor_id."'")->row();
							$review_check_date = date('d M, Y.', strtotime($appointments->appointment_date. ' + '.$reviewDays->review_days.' days'));
							echo "Valid Till ".$review_check_date;
							?>							
						</td>
					</tr>
				</table>
			</td>
		</tr>
				
	</table>	
</body>	
</html>