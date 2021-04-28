
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

	<link href="<?= base_url() ?>assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?= base_url() ?>assets/css/pdf.css" rel="stylesheet">

	<!-- <script src="<?php echo base_url(); ?>assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script type="text/javascript">
		$(document).ready(function(){
			var type = $('.Submit_type').val();
			if(type == "SP")
			{
				window.open('<?php echo base_url(uri_string()); ?>','_blank');
			}
		});
	</script> -->

</head>
<body class="vitals">
	<!-- <input type="hidden" value="<?=$type?>" class="Submit_type"> -->
	<!-- <p><?=$type?></p> -->
	<?php if($pdf_settings->header != 1){  
		?>
		<htmlpageheader name="firstpageheader">
			<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
				<tr>
					<td style="width:40%">
						<img style="width:50%" alt="" src="<?php echo base_url(); ?>uploads/clinic_logos/<?php echo $appointments->clinic_logo; ?>">
					</td>
					<td style="width: 25%"></td>
					<td style="width:35%;text-align: right;">
						<span style="font-weight: bold;font-size: 12px">Address</span><br><span style="font-weight: 700;font-size: 12px"><?php echo $appointments->address; ?></span><br><span style=" font-weight: bold;font-size: 12px">Phone:</span><span style="font-weight: 700;font-size: 12px"><?php echo $appointments->clinic_phone; ?></span>
					</td>
				</tr>
			</table>
			<hr>
		</htmlpageheader>
		<htmlpageheader name="otherheader">
			<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
				<tr>
					<td style="width:40%">
						<img style="width:50%" alt="" src="<?php echo base_url(); ?>uploads/clinic_logos/<?php echo $appointments->clinic_logo; ?>">
					</td>
					<td style="width: 25%"></td>
					<td style="width:35%;text-align: right;">
						<span style="font-weight: bold;font-size: 12px">Address</span><br><span style="font-weight: 700;font-size: 12px"><?php echo $appointments->address; ?></span><br><span style=" font-weight: bold;font-size: 12px">Phone:</span><span style="font-weight: 700;font-size: 12px"><?php echo $appointments->clinic_phone; ?></span>
					</td>
				</tr>
			</table>
			<hr>
		</htmlpageheader>
	<?php }
	elseif($pdf_settings->header==1)
	{
		$style = "margin-bottom: ".$pdf_settings->header_height."px !important;";
		?>
		<htmlpageheader name="firstpageheader">
			<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;<?=$style?>">
				<tr>
					<td>&nbsp;</td>
				</tr>
			</table>
			<hr>
		</htmlpageheader>
		<htmlpageheader name="otherheader">
			<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;<?=$style?>">
				<tr>
					<td>&nbsp;</td>
				</tr>
			</table>
			<hr>
		</htmlpageheader>
		<?php
	}
		?>
		<table cellpadding="0" cellspacing="0" style="margin: 0px;font-size: 14px; padding: 0px; width: 100%;">
			<tr>
				<td style="width:30%">
					<?php 
					$patientName = '';
					if($appointments->title){
						$patientName = ucwords($appointments->title).". ";
					}
					$patientName .= $appointments->pname." ".$appointments->plname;
					?>
					<span style="font-weight: bold;"><b><?php echo strtoupper($patientName); ?></b></span><br>
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
				<td style="width:30%;text-align: center;vertical-align: top">
					<?php if($appointments->address_line != ''){ ?>
						<span style="font-weight: bold">Address:</span><br><p><?php echo $appointments->address_line.", ".$appointments->location; ?></p>
					<?php } ?>
				</td>
				<td style="width:30%;text-align: right;vertical-align: top;font-size: 13px">
					<span style="font-weight: bold"><b><?php echo "DR. ".strtoupper($appointments->dfname." ".$appointments->dlname); ?></b></span><br><span><?php echo strtoupper($appointments->qualification. ", ". $appointments->department_name); ?> </span><br>
					<span><b>Reg. No:</b> &nbsp;<?php echo $appointments->registration_code; ?> </span>							
				</td>
			</tr>
		</table>
		<hr>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px;font-size: 14px;min-height:50px !important;padding-left: 0px !important">
			<?php 

			$res_vital_sign = $this->db->query("SELECT a.vital_sign, a.vital_result, a.patient_id, a.vital_sign_recording_date_time,b.unit FROM `patient_vital_sign` a left join vital_sign b on(a.vital_sign = b.short_form)  WHERE a.vital_sign_recording_date_time =(select max(a.vital_sign_recording_date_time) FROM `patient_vital_sign` a where a.patient_id = '".$appointments->patient_id."' and a.clinic_id = '".$appointments->clinic_id."') and a.vital_result != '' and a.sign_type = 'generic' ORDER BY a.patient_vital_id ASC")->result();
			
			if(sizeof($res_vital_sign) == 0)
			{
				?>
				<tr style="padding: 10px">
					<td>&nbsp;</td>
				</tr>
				<?php
			}
			else
			{

				?>
			<tr style="padding: 10px">
				<?php 
				$BP = '';
				$i=0;
				foreach ($res_vital_sign as $value) { 

					if($value->vital_sign == 'SBP' ){
						$SBP = $value->vital_result;
					} else if($value->vital_sign=='DBP'){
						$BP = $SBP.'/'.$value->vital_result. ' mmHg';
						?>
							<td style="padding:10px;width: 20px;padding-left: 0px !important;padding-right: 0px !important">
								<span style="font-weight: bold;">BP</span>
							<!-- </td>
							<td style="padding:10px;"> -->
								 <span>: <?php echo $BP; ?></span> 
							</td>
						<?php
					}else{

						?>							
							<td style="padding:10px;width: 20px;padding-left: 0px !important;padding-right: 0px !important">
								<span style="font-weight: bold;"><?php echo $value->vital_sign; ?></span>
							<!-- </td>
							<td style="padding:10px;"> -->
								 <span>: <?php echo round($value->vital_result,2); ?> <?php echo $value->unit; ?> </span>
							</td>

						<?php
					}
						if($i%4==0 && $i!=0)
						{
							echo "</tr><tr style='padding:10px;margin-top:10px'>";
						}
					$i++;
				} 
				?>
			</tr>
			<?php
			}
			?>		</table>

		<hr>
		<?php if($appointments->allergy) { ?>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px;font-size: 14px">
			<tr>
				<td style="width:100%;">
					<span style="font-weight: bold;">Drug Allergy </span> : <?php echo ucfirst($appointments->allergy); ?> </span>
				</td>
			</tr>
		</table>
		<hr>
		<?php } ?>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px;font-size: 14px">
			<tr>
				<td style="width:50%;">
					<?php
					$reviewDays = $this->db->query("SELECT review_days FROM clinic_doctor WHERE clinic_id = '".$appointments->clinic_id."' AND doctor_id = '".$appointments->doctor_id."'")->row();

					$check = $this->db->query("select * from appointments a inner join doctors d on(a.doctor_id= d.doctor_id) where a.patient_id='" . $appointments->patient_id . "' and a.doctor_id='" . $appointments->doctor_id . "' and a.parent_appointment_id=0 and a.appointment_date <= '".date('Y-m-d')."' and (a.status !='booked' or a.status !='drop') order by a.appointment_date  desc")->row();

					$get_review_days = $this->db->query("select * from clinic_doctor  where clinic_id='" . $appointments->clinic_id . "' and doctor_id ='".$appointments->doctor_id."'")->row();

					$get_review_times = $get_review_days->review_times;

//getting review date in y-m-d format
					$review_check_date = date('Y-m-d', strtotime($check->appointment_date. ' + '.$get_review_days->review_days.' days'));

					$check_review_count = $this->db->query("select * from appointments where  patient_id='" . $appointments->patient_id . "' and doctor_id='" . $appointments->doctor_id . "' and appointment_date >'".$check->appointment_date."' and appointment_date <='".$review_check_date."' and status !='booked'")->num_rows();

					if($check){


						if($appointments->appointment_date > $review_check_date){
							$status = "Valid ";
						}
						else{

							if($check_review_count <= $get_review_times){
								$review_time_diff = $get_review_times - $check_review_count;
								$status = "You have ".$review_time_diff." Review Visits";
							}
							else{
								$status = "Valid ";
							}
						}


					}
					else{
						$status = "Valid ";
					}
					$review_check_date = date('d M, Y.', strtotime($check->appointment_date. ' + '.$reviewDays->review_days.' days'));
					echo "Valid Till ".$review_check_date.". ".$status;
					?>							
				</td>
				<td style="text-align: right;width: 50%">
					<span>Referred By : 
						<?php
						if($appointments->referred_by_type=="Doctor")
							echo getreferalDoctorname($appointments->referred_by);
						else
							echo $appointments->referred_by;
						?>
					</span>
				</td>
			</tr>
		</table>
		<?php   
		if($pdf_settings->footer==1)
		{
			$style = "margin-bottom: ".$pdf_settings->footer_height."px !important;";
		}
		?>
			<htmlpagefooter name="footer">
				<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;<?=$style?>">
					<tr>
						<td style="width:45%">
							<span style="font-weight: bold;font-size: 12px">Powered By umdaa.co</span>
						</td>
						<td style="width: 20%">
							<span style="font-weight: bold;font-size: 12px;text-align: center;">Page {PAGENO} of {nb}</span>
						</td>
						<td style="width:35%;text-align: right;">
							<span style="font-weight: bold;font-size: 12px"><b>Date: </b><?php echo date("d M Y h:i A", strtotime($appointments->created_date_time)); ?></span>
						</td>
					</tr>
				</table>
			</htmlpagefooter>		

	</body>	
</html>