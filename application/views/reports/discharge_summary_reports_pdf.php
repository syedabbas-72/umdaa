<!DOCTYPE html>
<html lang="en">
<head>
	<!-- Meta information -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
</head>

<body style="font-family: 'Roboto', sans-serif;">
	<?php if($pdf_settings->header != 1){  ?>
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
	<?php }
	else
	{
		?>
		<div style="padding-top: <?=$pdf_settings->header_height?>px !important">&nbsp;
		</div>
		<?php
	} ?>
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
					<span style="font-weight: bold">Address:</span><br><p><?php echo $appointments->address_line; ?></p>
				<?php } ?>
			</td>
			<td style="width:30%;text-align: right;vertical-align: top;font-size: 13px">
				<div style="<?=($pdf_settings->doc_details==0)?'display: none':''?>">
				<span style="font-weight: bold"><b><?php echo "DR. ".strtoupper($appointments->dfname." ".$appointments->dlname); ?></b></span><br><span><?php echo strtoupper($appointments->qualification. ", ". $appointments->department_name); ?> </span><br>
				<span><b>Reg. No:</b> &nbsp;<?php echo $appointments->registration_code; ?> </span>	</div>							
			</td>
		</tr>
	</table>
	<hr>
	<?php if(count($patient_clinical_diagnosis)>0){ ?>
		<div class="cls_007"><span class="cls_007"  style="font-weight: bold; line-height: 25px;text-transform: uppercase;">Clinical Diagnosis</span></div>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">
			<?php $cd=0;$c=1;foreach ($patient_clinical_diagnosis as $key => $value) {
				$cd_code = "(".$value->code.")";
				?>
				<tr>
					<td style="padding: 10px">
						<span><?php echo $c++ . ". " .ucwords($value->disease_name); ?>  <?=($value->code!='')?$cd_code:''?></span>
					</td>
					
				</tr>
				<?php $cd++;} ?>
			</table>
			<hr>
		<?php } ?>
		<div class="cls_007"><span class="cls_007"  style="font-weight: bold; line-height: 25px;text-transform: uppercase;">Course In Hospital</span></div>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">
			<tr>
				<td style="padding: 10px">
					<span><?php echo $appointments->course_in_hospital; ?></span>
				</td>
				
			</tr>
		</table>
		<hr>
		<?php if(count($patient_investigations)>0){ ?>
			<div class="cls_007">
				<span class="cls_007"  style="font-weight: bold; line-height: 25px;text-transform: uppercase;">Investigations</span>
			</div>
			<table class="table" cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">
				<tbody>
					<?php 
					$i=1;
					foreach ($patient_investigations as $key => $value) { 
						$inv_id = "(".$value->investigation_id.")";
						?>
						<tr>
							<td><?php echo $i++ .". ".ucwords($value->investigation_name); ?> <?=($value->investigation_id!=0)?$inv_id:''?></td>
						</tr>
					<?php
					$inv_id = '';
					 } ?>
				</tbody>
			</table>
			<hr>
		<?php } ?>
		<?php if(count($patient_prescription)>0) { ?>
			<div class="cls_007"><span class="cls_007" style="font-weight: bold; font-size:14px; line-height: 25px;">PRESCRIPTION(Rx)</span></div>
			<table class="table" cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">
				<thead>
					<tr>
						<th style="font-size: 14px;background: #f2f2f2; border-bottom: 1px solid #ccc; width: 10%">S#</th>
						<th style="font-size: 14px;background: #f2f2f2; border-bottom: 1px solid #ccc; width: 40%">Medicine</th>
						<th style="font-size: 14px;background: #f2f2f2; border-bottom: 1px solid #ccc; width: 20%">Frequency</th>
						<th style="font-size: 14px;background: #f2f2f2; border-bottom: 1px solid #ccc; width: 15%">Duration</th>
						<th style="font-size: 14px;background: #f2f2f2; border-bottom: 1px solid #ccc; width: 15%">Qty</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i=1;
					$patient_prescription_drug=$this->db->query("select pd.drug_id, pd.day_schedule, pd.preffered_intake,pd.day_dosage,pd.drug_dose,pd.dosage_unit,pd.dosage_frequency, pd.dose_course, pd.quantity,pd.medicine_name,d.formulation,d.trade_name, d.composition,pd.remarks from patient_prescription_drug pd left join drug d on (pd.drug_id=d.drug_id) where pd.patient_prescription_id='" . $patient_prescription->patient_prescription_id . "' ")->result();

					foreach ($patient_prescription_drug as $value) { 
						$M = 0;
						$dayM = "M";
						$dayA = "A";
						$dayN = "N";
						$N = 0;
						$A = 0;
						$dose = 1;
						$Mday = '';

						if($value->dosage_frequency != "")
							$freq = $value->dosage_frequency;
						elseif($value->day_schedule != "")
							$freq = "Day";

						if($value->preffered_intake == "AF"){
							$intake = "after food";
						}
						if($value->preffered_intake == "BF"){
							$intake = "before food";
						}
						if($appointments->preferred_language !="NULL" || $appointments->preferred_language !=""){
							if($appointments->preferred_language == "Telugu"){
								$plang = "te";
							}
							else if($appointments->preferred_language == "Hindi"){
								$plang = "hi";
							}
							else if($appointments->preferred_language == "Gujarati"){
								$plang = "gu";
							}
							else if($appointments->preferred_language == "Kannada"){
								$plang = "kn";
							}
							else if($appointments->preferred_language == "Malayalam"){
								$plang = "ml";
							}
							else if($appointments->preferred_language == "Marathi"){
								$plang = "mr";
							}
							else if($appointments->preferred_language == "Panjabi"){
								$plang = "pa";
							}
							else if($appointments->preferred_language == "Sindhi"){
								$plang = "sd";
							}
							else if($appointments->preferred_language == "Tamil"){
								$plang = "ta";
							}
							else if($appointments->preferred_language == "Urdu"){
								$plang = "ur";
							}
							else if($appointments->preferred_language == "Bengali"){
								$plang = "bn";
							}
							else{
								$plang = "en";
							}
						}
						else{
							$plang = "en";
						}
						$Intake_converted = translate($intake,"en",$plang);
						$remark_converted = translate(strtolower($value->remarks),"en",$plang);

						if($value->day_schedule !=""){
							$split_schedule = explode(",",$value->day_schedule);

							if(in_array("M", $split_schedule)){
								$M = "<span style='font-size:20px'>&#10004;</span>";
								$dayM = "<span>M</span>";

							}else{
								$M = "<span style='font-size:20px'>&#215;</span>";
								$dayM = "<span>M</span>";
							}

							if(in_array("A", $split_schedule)){
								$A = "<span style='font-size:20px'>&#10004;</span>";
								$dayA = "<span>A</span>";

							}else{
								$A = "<span style='font-size:20px'>&#215;</span>";
								$dayA = "<span>A</span>";
							}

							if(in_array("N", $split_schedule)){
								$N = "<span style='font-size:20px'>&#10004;</span>";
								$dayN = "<span>N</span>";

							}else{
								$N = "<span style='font-size:20px'>&#215;</span>";
								$dayN = "<span>N</span>";
							}
						}
						?>
						<tr>
							<td><?php echo $i++; ?></td>
							<td>
								<span style="font-weight: bold; font-size: 14px;"><?php if($value->trade_name != NULL || $value->trade_name != '') { echo strtoupper($value->formulation." ".$value->trade_name); }else{ echo strtoupper($value->formulation." ".$value->medicine_name); } ?></span><br>
								<p style="font-size: 13px; color:rgb(84,84,84);"><?php echo $value->composition; ?></p>
							</td>
							<td>
								<?php if($value->day_schedule==""||$value->day_schedule==NULL){ ?>
									<span><?php if($value->day_dosage =="stat" || $value->day_dosage =="sos" || $value->day_dosage =="HS"){ echo $value->day_dosage; } else { echo $value->day_dosage." times in a ".$value->dosage_frequency; } ?></span><br><span style="font-size: 13px;color:rgb(84,84,84);"><small><?php echo "(".$value->drug_dose." ".$value->dosage_unit." each )"; ?></small></span>
								<?php } else { ?>
									<span><?php echo $M.'   -   '.$A.'   -   '.$N; ?></span><br><span><?php echo $dayM.'&nbsp;&nbsp;&nbsp;&nbsp; '.$dayA.'&nbsp;&nbsp;&nbsp;&nbsp; '.$dayN; ?></span><br><span><small><?php echo $value->drug_dose." ".$value->dosage_unit." each"; ?></small></span>
								<?php } ?>

							</td>
							<td><?php if($value->dose_course == 0 ){ echo "--"; } else { echo $value->dose_course." ".$freq."(s)"; } ?><br>
								<span style="font-weight: bold;text-transform: capitalize;font-size: 12px"><?=$intake?></span>
							</td>
							<td><?php if($value->quantity == 0 ){ echo "--"; } else { echo $value->quantity; } ?></td>
						</tr>
						<?php if($value->remarks !="") { ?>
							<tr>
								<td colspan="1" style="padding-top: 0"></td>
								<td colspan="4" style="padding-top: 0"><span style="font-weight:bold">Remarks: </span> <?php echo ucfirst($value->remarks); ?><?php if($plang != "en"){ ?> <br><?php echo "(".ucfirst($remark_converted).")"; } ?></td>
							</tr>
						<?php } ?>
					<?php } ?>
				</tbody>
			</table>
			<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px;vertical-align: top">
				<tr> <td><div  style="font-size:13px;font-weight: bold;font-style: italic;">CAUTION : Take the medicine as prescribed by the doctor. Do not use in larger or smaller amounts than advised/prescribed. Use the medicine only for the prescribed purpose. Report any unusual symptoms to the doctor immediately. Do not stop the use of the medicine without consulting your doctor. Ensure that you complete the course as prescribed by your doctor.</div></td></tr>
			</table>
			<hr>
		<?php } ?>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px;vertical-align: top">
			<?php if($patient_prescription->general_instructions != ''){ ?>
				<tr>
					<td colspan="5" style="vertical-align: top; padding:10px 0px"><span style="font-weight: bold; line-height: 25px; font-size:14px;">General Instructions: </span>
					<p style="font-size: 13px; padding:10px 0px;"><?=$patient_prescription->general_instructions?></p>
					</td>
				</tr>
			<?php } ?>
			<?php if($patient_prescription->plan != ''){ ?>
				<tr>
					<td colspan="5" style="vertical-align: top; padding:10px 0px"><span style="font-weight: bold; line-height: 25px;">Plan: </span>
						<p><?=$patient_prescription->plan?></p>
					</td>
				</tr>
			<?php } ?>
		</table>
		<hr>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">
			<tr>
				<td colspan="5">
					<span style="font-weight: bold; line-height: 25px;text-transform: uppercase;">Followup Date: </span>
					<?php if($follow_up_date == "" ){echo "NA"; }else {echo date("d M Y",strtotime($follow_up_date));} ?>
				</td>
			</tr>
		</table>
		<hr>
		<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
			<tr>
				<td style="width:47%;text-align: left;vertical-align: bottom;">
					<span style="font-weight: bold; line-height: 25px;"><img src="<?php echo base_url('uploads/qrcodes/patients/'.$appointments->qrcode);?>"></span>
				</td>
				<td style="width:53%;text-align: right;vertical-align: middle;">
					<span style=" font-weight: bold; line-height: 25px;"><?php echo 'DR. '. strtoupper($appointments->dfname.'  '. $appointments->dlname); ?>
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