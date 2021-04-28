
<?php require APPPATH . '/controllers/translate.php'; 
    $translator = new Translate();  
    $translator->setLangFrom('en');
    $translator->setLangTo('hi');

?>
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
	<table cellpadding="0" cellspacing="0" style="width:800px; font-family: segoe ui; color: #333" align="center">
		<!-- header -->
		<tr>
			<td style="border-bottom:1px solid #ccc; padding:15px 10px;">
				<table cellspacing="0" cellpadding="0">
					<tr>
						<td style="width: 100px;"><img src="<?php echo base_url('uploads/clinic_logos/'.$getAppointment->clinic_logo);?>"/></td>
						<td style="width:370px; text-align: left">
							<h2 style="font-size: 30px; padding:0px; margin: 0px;"><?php echo $getAppointment->clinic_name;  ?></h2>
							<!--<h3 style="font-size: 16px; font-weight: 350; text-transform: uppercase; color: #757575; padding:0px; margin: 0px 0px 10px 0px;">Multi Speciality OPD Clinic</h3>-->
						</td>
						<td style="width:300px">
							<span style="color:#000; font-weight: 600">Address</span><br>
							<?php echo $getAppointment->clinic_address;  ?>
							<span style="color:#000; font-weight: 600">Phone:</span> <?php echo $getAppointment->clinic_phone; ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style="border-bottom: 1px solid #ccc; padding:15px 10px 20px 10px">
				<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
					<tr>
						<td style="width:45%">
							<span style="color:#000; font-weight: 600; line-height: 25px;"><b>Patient Name :</b>&nbsp;<?php echo $getAppointment->title." ".$getAppointment->pfname." ".$getAppointment->pmname." ".$getAppointment->plname; ?></span><br>
							Patient Id: &nbsp;<?php echo $getAppointment->umrno; ?>
						</td>
						<td style="width:55%">
							<span style="color:#000; font-weight: 600; line-height: 25px;"><b>Age/Gender:</b> &nbsp;<?php echo $getAppointment->age .$age_unit. ' / '. $getAppointment->gender; ?> </span><br>
							<b>Address:</b> &nbsp;<?php echo $getAppointment->address_line; ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		
		<?php
		foreach($pvrdt as $pv){
		
		$patient_vitals = $this->db->query("select * from patient_vital_sign where appointment_id='".$getAppointment->appointment_id."' and vital_sign NOT IN ('DBP','SBP') and vital_sign_recording_date_time='".$pv->vital_sign_time."'")->result();
		
		?>
		
		<tr>
			<td style="border-bottom: 1px solid #ccc">
				<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">
				
					<tr>
					<?php
					foreach($patient_vitals as $pvs){
						$unit = $this->db->query("SELECT unit FROM `vital_sign` where short_form='".$pvs->vital_sign."' ")->row();
					?>
						<td style="width: 25px;">
							<span style="font-weight: bold;"><?php echo $pvs->vital_sign;?></span> : <?php echo $pvs->vital_result."  ".$unit->unit; ?> </span> 
						</td>
						
					<?php } 
					$sbp = $this->db->query("select vital_result as SBP from patient_vital_sign where appointment_id='".$getAppointment->appointment_id."' and vital_sign ='SBP' and vital_sign_recording_date_time='".$pv->vital_sign_time."'")->row();
					$dbp = $this->db->query("select vital_result as DBP from patient_vital_sign where appointment_id='".$getAppointment->appointment_id."' and vital_sign ='DBP' and vital_sign_recording_date_time='".$pv->vital_sign_time."'")->row();
					if(count($sbp)>0 && count($dbp)>0)
					{
					?>
					<td style="width: 25px;">
							<span style="font-weight: bold;">BP</span> : <?php echo $sbp->SBP."/".$dbp->DBP."  mmHg"; ?> </span> 
						</td>
					<?php } ?>
					</tr>
				</table>
			</td>
		</tr>
		<?php } ?>
		<?php foreach ($consent_forms as $key => $cf_val) {?>
		<tr>
				<td style="width: 25px;">
					<span style="font-weight: bold;">Checked By:</span> : <?php echo $cf_val->checked_by; ?> </span> 
				</td>
				<td style="width: 25px;">
					<span style="font-weight: bold;">Done By:</span> : <?php echo $cf_val->done_by; ?> </span> 
				</td>
				<td style="width: 25px;">
					<span style="font-weight: bold;">Assigned By:</span> : <?php echo $cf_val->assigned_by; ?> </span> 
				</td>
				<td style="width: 25px;">
					<span style="font-weight: bold;">Nurse:</span> : <?php echo $cf_val->nurse; ?> </span> 
				</td>
				<td style="width: 25px;">
					<span style="font-weight: bold;">Anesthetist:</span> : <?php echo $cf_val->anesthetist; ?> </span> 
				</td>
				<!--<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">
				<?php
					$consent_form = $this->Generic_model->getAllRecords('patient_consentform_line_items',array('patient_consent_form_id'=>$value->patient_consent_form_id),'');
					foreach($consent_form as $cf){
					?>
            		<tr>
						<td style="width: 100px;"><img src="<?php echo base_url('uploads/patient_consentforms/'.$cf->patient_consent_form_image);?>"/></td>
					</tr>
					<?php } ?>
				</table>-->
			
		</tr>
		<?php } ?>
		<!--<tr>
			<td style="background-color: #f5f5f5; padding:15px 10px; font-weight: 600; color:#000">
				<table cellspacing="0" cellpadding="0" style="width: 100%; text-align: center;padding-top:10px;">
					<tr>
						<td style="width:30px; border-right:1px dotted #999">#</td>
						<td style="width:335px; border-right:1px dotted #999; text-align: left; padding-left: 15px">Medicine</td>
						<td style="width:100px; border-right:1px dotted #999; text-align: right; padding-left: 15px; padding-right: 15px">Dosage</td>
						<td style="border-right:1px dotted #999; text-align: center; padding-left: 15px; padding-right: 15px">Timing - Freq - Duration</td>
						
					</tr>
				</table>
			</td>
		</tr>--
		<tr>
			<td style="padding:10px;">
				<table cellspacing="0" cellpadding="0" style="width: 100%; text-align: center;">
				<?php 
            $i=1;
            foreach ($patient_prescription as $key => $value) { 
			$M = 0;
                      $N = 0;
                      $A = 0;
                        $dose = 1;
                        $Mday = '';
                    if($value->day_schedule=='M'){
                      $M = $dose;
                      $Mday = $dose.' - '.'Morning'; 
                    }else if($value->day_schedule=='N'){
                      $N = $dose;
                       $Mday = $dose.' - '.'Evening';
                    }else if($value->day_schedule=='A'){
                        $A = $dose;
                        $Mday = $dose.' - '.'Afternoon';
                    }
			
			
			?>
					<tr>
						<td style="width:30px; border-right:1px dotted #ebebeb; padding-top:15px; padding-bottom: 15px; vertical-align: top"><?php echo $i; ?></td>
						<td style="width:335px; border-right:1px dotted #ebebeb; text-align: left; padding-left: 15px; padding-top:15px; padding-bottom: 15px"><?php echo $value->trade_name; ?>
						<br/>
						<span style="font-size: 14px;width:25%;text-align: left;">Composition : </span> 
							<span style="font-size:13px;"><?php echo $value->composition; ?></span>
							<br/>
							<span style="font-style: italic;font-size: 14px; width:25%;text-align: left;">Timing : </span> 
								<span id="daytxt" style="font-size:14px;"><?php echo $Mday; ?> </span>
						</td>
						<td style="border-right:1px dotted #ebebeb; text-align: center; padding-left: 15px; padding-right: 15px; padding-top:15px; padding-bottom: 15px"><?php echo $M.' - '.$A.' - '.$N; ?></td>
						
						<td style="width:100px; border-right:1px dotted #ebebeb; text-align: right; padding-left: 15px; padding-right: 15px; padding-top:15px; padding-bottom: 15px"><?php echo $value->dose_course; ?> Day's </td>
					</tr>
					
					<?php } ?>
				
					
					<tr>
						<td colspan="2" style="padding: 10px; text-align: left">
							Advice :
						</td>
						<td colspan="2" style="padding: 10px; text-align: right">
						Test Prescribed :
						</td>
					</tr>
					<tr>
						<td colspan="2" style="padding: 10px; text-align: left">
							<img src="<?php echo base_url('uploads/qrcodes/patients/'.$appointments->qrcode);?>">
						</td>
						<td colspan="2" style="padding: 10px; text-align: right">
						<?php echo $appointments->salutation.' . '. $appointments->dname; ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>-->
	</table>
</body>	
</html>