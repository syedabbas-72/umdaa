
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

<body style="font-family: 'Poppins', sans-serif;">
<?php if($pdf_settings->header == 1){  ?>
<htmlpageheader name="firstpageheader">

  <table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">

					<tr>

						<td style="width:40%">

							<img style="width:50%" alt="" src="<?php echo base_url(); ?>uploads/clinic_logos/<?php echo $appointments->clinic_logo; ?>">

						</td>

						<td style="width: 25%">

						</td>


						<td style="width:35%;text-align: right;">

							<span style="font-weight: bold;font-size: 14px">Address</span><br><span style="font-weight: 700;font-size: 14px"><?php echo $appointments->address; ?></span><br><span style=" font-weight: bold;font-size: 14px">Phone:</span><span style="font-weight: 700;font-size: 14px"><?php echo $appointments->clinic_phone; ?></span>

						</td>

					</tr>

				</table>
				<hr>
</htmlpageheader>
	<?php } ?>	
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
					<span style="font-weight: bold"><b><?php echo "DR. ".strtoupper($appointments->dfname." ".$appointments->dlname); ?></b></span><br><span><?php echo strtoupper($appointments->qualification. ", ". $appointments->department_name); ?> </span><br>
					<span><b>Reg. No:</b> &nbsp;<?php echo $appointments->registration_code; ?> </span>							
				</td>
			</tr>
		</table>
<hr>
<?php if(count($vital_sign)>0){ ?>
	<div class="cls_007"><span class="cls_007"  style="font-weight: bold; line-height: 25px;text-transform: uppercase;">VITALS</span></div>
		<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px;">

		<!-- <tr>
			<td>
				<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">
				
					<tr> -->
						<?php $i=1;foreach($vital_sign as $key=>$value){?>
							<tr>
								<td style="padding:10px"><span style="font-weight: bold;"><?php echo $key; ?></span></td>
								<td style="width: 200px;padding: 10px"><span>: <?php echo $value; ?></span></td>
							</tr>
						<?php
				// 									if($i % 3 == 0)
    // {
    // 	echo "</tr><tr style='padding:10px;margin-top:10px'>";
    // }
						 
						$i++;
						} 
						?>
					<!-- </tr>
				
				</table>
			</td>
		</tr> -->
	</table>
	<hr>
<?php } ?>
	<?php if(count($patient_clinical_diagnosis)>0){ ?>
	<div class="cls_007"><span class="cls_007"  style="font-weight: bold; line-height: 25px;text-transform: uppercase;">Clinical Diagnosis</span></div>
				<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">
					
				<?php $cd=0;$c=1;foreach ($patient_clinical_diagnosis as $key => $value) {?>
            
          
					<tr>
						<td style="padding: 10px">
							<span><?php echo $c++ . ". " .ucwords($value->disease_name); ?></span>
						</td>
						
					</tr>
					<?php $cd++;} ?>
				</table>
				<hr>
			<?php } ?>
		
			
		<?php if(count($patient_investigations)>0){ ?>

				<div class="cls_007"><span class="cls_007"  style="font-weight: bold; line-height: 25px;text-transform: uppercase;">Investigations</span></div>
				<table class="table" cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">
				
				<tbody>

	
				
				<?php 
            $i=1;
			
			
            foreach ($patient_investigations as $key => $value) { ?>
					<tr>
						<td><?php echo $i++ .". ".ucwords($value->investigation); ?></td>
						
						
						
					</tr>
					
					<?php } ?>
				
				
	</tbody>
	</table>
	
	<hr>

		<?php } ?>

		<?php if(count($patient_prescription)>0) { ?>

			<div class="cls_007"><span class="cls_007"  style="font-weight: bold; line-height: 25px;text-transform: uppercase;">Prescription(Rx)</span></div>
				<table class="table" cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">
					<thead>
					<tr>
						<th  style="font-size: 15px;background: #f6f6f6;">#</th>
						<th  style="font-size: 15px;background: #f6f6f6;">Medicine</th>
						<th  style="font-size: 15px;background: #f6f6f6;">Frequency</th>
						<th  style="font-size: 15px;background: #f6f6f6;">Duration</th>
						<th  style="font-size: 15px;background: #f6f6f6;">Qty</th>
					</tr>
				</thead>
				<tbody>

	
				
				<?php 
            $i=1;
			
			$patient_prescription_drug=$this->db->query("select pd.drug_id, pd.day_schedule, pd.preffered_intake,pd.day_dosage,pd.drug_dose,pd.dosage_unit,pd.dosage_frequency, pd.dose_course, pd.quantity,pd.medicine_name,d.formulation,d.trade_name, d.composition,pd.remarks from patient_prescription_drug pd left join drug d on (pd.drug_id=d.drug_id) where pd.patient_prescription_id='" . $patient_prescription->patient_prescription_id . "' ")->result();
			
            foreach ($patient_prescription_drug as $key => $value) { 
			$M = 0;
			$dayM = "M";
			$dayA = "A";
			$dayN = "N";
                      $N = 0;
                      $A = 0;
                        $dose = 1;
                        $Mday = '';

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
                   
                    }
                    else{
                    	$M = "<span style='font-size:20px'>&#215;</span>";
                    	$dayM = "<span>M</span>";
                    }
                     if(in_array("A", $split_schedule)){
                      $A = "<span style='font-size:20px'>&#10004;</span>";
                      $dayA = "<span>A</span>";
                   
                    }
                    else{
                    	$A = "<span style='font-size:20px'>&#215;</span>";
                    	$dayA = "<span>A</span>";
                    }
                     if(in_array("N", $split_schedule)){
                      $N = "<span style='font-size:20px'>&#10004;</span>";
                      $dayN = "<span>N</span>";
                   
                    }
                    else{
                    	$N = "<span style='font-size:20px'>&#215;</span>";
                    	$dayN = "<span>N</span>";
                    }
			}

			
			?>
					<tr>
						<td><?php echo $i++; ?></td>
						<td><span style="font-weight: bold"><?php if($value->trade_name != NULL || $value->trade_name != '') { echo strtoupper($value->formulation." ".$value->trade_name); }else{ echo strtoupper($value->formulation." ".$value->medicine_name); } ?></span><br>
							<span style="font-size: 13px;color:rgb(84,84,84);"><?php echo $value->composition; ?></span>
						
						</td>
						<td>
							<?php if($value->day_schedule==""||$value->day_schedule==NULL){ ?>
							<span><?php if($value->day_dosage =="stat" || $value->day_dosage =="sos" || $value->day_dosage =="HS"){ echo $value->day_dosage; } else { echo $value->day_dosage." times in a ".$value->dosage_frequency; } ?></span><br><span style="font-size: 13px;color:rgb(84,84,84);"><small><?php echo "(".$value->drug_dose." ".$value->dosage_unit." each )"; ?></small></span>
						<?php } else { ?>
							<span><?php echo $M.'   -   '.$A.'   -   '.$N; ?></span><br><span><?php echo $dayM.'&nbsp;&nbsp;&nbsp;&nbsp; '.$dayA.'&nbsp;&nbsp;&nbsp;&nbsp; '.$dayN; ?></span><br><span><small><?php echo $value->drug_dose." ".$value->dosage_unit." each"; ?></small></span>
						<?php } ?>

						</td>
						
						<!-- <td><?php if($value->dosage_frequency == "" || $value->dosage_frequency == NULL|| $value->dosage_frequency == "--") { echo "--"; } else { echo $value->dose_course." ".$value->dosage_frequency."(s)"; } ?> <?php if($plang != "en"){ ?> <br>
							<span style="font-size: 16px;padding-top:5px;"><?php  if($Intake_converted!="" || $Intake_converted!=NULL){ echo "(".$Intake_converted.")" ;} ?></span><?php } ?>
						</td> -->
						<td><?php if($value->dose_course == 0 ){ echo "--"; } else { echo $value->dose_course." Days"; } ?></td>
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
	<tr>
			<td colspan="5" style="height:100px;vertical-align: top;"><span style="font-weight: bold; line-height: 25px;text-transform: uppercase;">Remarks: </span>
				<p><?=$patient_prescription->general_instructions?></p>
			 </td>
		</tr>
	</table>
		<hr>
<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">
	<tr>
			<td colspan="5"> <span style="font-weight: bold; line-height: 25px;text-transform: uppercase;">Followup Date: </span><?php if($follow_up_date == "" ){echo "NA"; }else {echo date("d M Y",strtotime($follow_up_date));} ?></td>
		</tr>
	</table>
		<hr>

	<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">

					<tr>

						<td style="width:47%;text-align: left;vertical-align: bottom;">

							<span style="font-weight: bold; line-height: 25px;"><img src="<?php echo base_url('uploads/qrcodes/patients/'.$appointments->qrcode);?>">

						</td>

						<td style="width:53%;text-align: right;vertical-align: middle;">

							<span style=" font-weight: bold; line-height: 25px;"><?php echo 'DR. '. strtoupper($appointments->dfname.'  '. $appointments->dlname); ?>
 
						</td>

					</tr>

				</table>
	
   <htmlpagefooter name="footer">
   	<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">

					<tr>

						<td style="width:45%">

							<span style="font-weight: bold;font-size: 14px">Powered By umdaa.co</span>

						</td>

						<td style="width: 20%">
							<span style="font-weight: bold;font-size: 14px;text-align: center;">Page {PAGENO} of {nb}</span>
						</td>


						<td style="width:35%;text-align: right;">

							<span style="font-weight: bold;font-size: 14px">Date: </b><?php echo date("d/m/Y"); ?></span>

						</td>

					</tr>

				</table>
</htmlpagefooter>

</body>	
</html>