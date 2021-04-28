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

<body style="font-family: 'Roboto', sans-serif;border-bottom: 1px solid #ddd;">
	<?php //if($pdf_settings->header != 1){  ?>
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
	<table cellpadding="0" cellspacing="0" style="margin: 0px;font-size: 12px; padding: 0px; width: 100%;">
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
			<td style="width:30%;text-align: right;vertical-align: top;font-size: 12px">
				<div>
				<span style="font-weight: bold"><b><?php echo "DR. ".strtoupper($appointments->dfname." ".$appointments->dlname); ?></b></span><br><span><?php echo strtoupper($appointments->qualification. ", ". $appointments->department_name); ?> </span><br>
				<span><b>Reg. No:</b> &nbsp;<?php echo $appointments->registration_code; ?> </span>	</div>					
			</td>
		</tr>
		<tr><td colspan="3"><hr></td></tr>
		<tr>
			<td colspan="3">
				<span style="font-weight: bold; font-size:12px;text-transform: uppercase; padding: 5px;">Drug Allergy: </span>
				<span style="font-size: 12px">
				<?php 
				if($appointments->allergy != '' || $appointments->allergy != null){
					echo ucwords($appointments->allergy);
				}else{
					echo "No allergy mentioned";
				}
				?></span>
			</td>
		</tr>
	</table>
	<hr>
		</htmlpageheader>
	<?php //}
	// else
	//{
		?>
		<!-- <div style="padding-top: <?=$pdf_settings->header_height?>px !important">&nbsp;
		</div> -->
		<?php
	//} ?>	
	<?php if(count($vital_sign)>0){ ?>
		<div class="cls_007"><span class="cls_007"  style="font-weight: bold; text-transform: uppercase;font-size: 12px">VITALS</span></div>
		<table cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px;min-height:50px !important;padding-left: 0px !important">
			<?php 
			$res_vital_sign = $this->db->query("SELECT a.vital_sign, a.vital_result, a.patient_id, a.vital_sign_recording_date_time,b.unit FROM `patient_vital_sign` a left join vital_sign b on(a.vital_sign = b.short_form)  WHERE a.vital_sign_recording_date_time =(select max(a.vital_sign_recording_date_time) FROM `patient_vital_sign` a where a.patient_id = '".$appointments->patient_id."' and a.clinic_id = '".$appointments->clinic_id."') and a.vital_result != '' and a.sign_type = 'generic' ORDER BY a.patient_vital_id ASC")->result();

			
			if(sizeof($res_vital_sign) == 0)
			{
				?>
				<tr style="padding: 5px">
					<td>&nbsp;</td>
				</tr>
				<?php
			}
			else
			{
				?>
				<tr style="padding: 5px">
					<?php 
						$SBP = 0;
						$DBP = 0;
						$BP = '';
						$i = 2;
						$vital_sign_results = array();

						// echo "<td>Array count: ".count($res_vital_sign)."</td>";

						foreach ($res_vital_sign as $value) 
						{ 
							// echo "<td>".$value->vital_sign.": ".$value->vital_result."</td>";
							if($value->vital_sign == "PR") {
								$vital_sign_results[0]['vital_sign'] = 'PR';
								$vital_sign_results[0]['value'] = $value->vital_result;
									$vital_sign_results[0]['unit'] = $value->unit;
							}elseif($value->vital_sign == "DBP" || $value->vital_sign == "SBP"){	
								if($value->vital_sign == "DBP"){
									$DBP = $value->vital_result;
								}elseif($value->vital_sign == "SBP"){
									$SBP = $value->vital_result;
								}

								if($SBP != 0 && $DBP != 0){
									$BP = $SBP."/".$DBP;
									$vital_sign_results[1]['vital_sign'] = 'BP';
									$vital_sign_results[1]['value'] = $BP;
									$vital_sign_results[1]['unit'] = $value->unit;
								}						
							}else{
								$vital_sign_results[$i]['vital_sign'] = $value->vital_sign;
								$vital_sign_results[$i]['value'] = $value->vital_result;
								$vital_sign_results[$i]['unit'] = $value->unit;
								$i++;
							}	

						} 

						// echo "<td>Array count: ".count($vital_sign_results[0])."</td>";
						$x = 0;
						for($j=0; $j<count($vital_sign_results); $j++){
							
							if($x == 4){
								echo "</tr><tr>"; // Add to new row after 4th column
								$x = 0;
							}

							?>
							<td  style='padding:10px;width: 20px;padding-left: 0px !important;padding-right: 0px !important'>
								<span style='font-weight:bold;font-size: 12px;'><?=$vital_sign_results[$j]['vital_sign']?>: </span><span style="font-size: 
								12px"><?=$vital_sign_results[$j]['value'].$vital_sign_results[$j]['unit']?></span>
							</td><?php
							$x++;
						}

				?>
			</tr>
			<?php
			}
			?>		
		</table>
		<hr>
	<?php } ?>
	
		<!-- symptoms -->
		<?php if(count($symptoms)>0){ ?>
		<div class="cls_007"><span class="cls_007"  style="font-weight: bold; text-transform: uppercase;font-size: 12px">SYMPTOMS</span></div>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">
			<?php $cd=0;$c=1;foreach ($symptoms as $key => $value) {
				$cd_code = "(".$value->code.")";
				?>
				<tr>
					<td style="padding: 10px">
						<span style="font-size:12px"><?php echo $c++ . ". " .ucwords($value->symptom_data)."-".ucwords($value->time_span)." ".ucwords($value->span_type); ?></span>
					</td>
					
				</tr>
				<?php $cd++;} ?>
				<?php?>
					<!-- <tr>
					<td style="padding: 10px">
						<span>welcome</span>
					</td>
					
				</tr> -->
			<?php	?>
			</table>
			<hr>
		<?php }?>
		<!-- Symptoms Ends -->

		
	<?php 
		if(count($get_past_history_info)>0){
			?>
			<div class="cls_007"><span class="cls_007"  style="font-weight: bold; text-transform: uppercase;font-size: 12px">PAST HISTORY</span></div>
			<?php
	$s=1;
foreach($get_past_history_info as $gphi)
{

    $past_history_data = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$gphi->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
    $get_past_history_form = $this->db->select("form_name")->from("form")->where("form_id='".$gphi->form_id."'")->get()->row();

	foreach($past_history_data as $pl){
	 $section_text = $this->db->query("select section_text from patient_form_line_items where patient_form_id='".$gphi->patient_form_id."' and section_id='".$pl->section_id."' and (section_text >'' or section_text IS NULL or section_text <'')")->row();
	
	if($section_text->section_text == '')
	{
		$s_text='';
	}else{
		$s_text=$section_text->section_text;
	}
	
	
	// $section_image = $this->db->query("select scribbling_image from patient_form_scribbling_images where patient_form_id='".$gphi->patient_form_id."'")->result();
	
	$section = $this->Generic_model->getSingleRecord('section',array('section_id'=>$pl->section_id),'');
	$parent_section_id = $section->parent_section_id;
	if($parent_section_id==0){
	?>
<?php if($s_text != ''){?>
		<!-- <span style="font-size: 12px;font-weight:bold;padding: 5px 15px"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?></span> -->
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">

				<tr>
					<td style="padding: 10px">
						<span style="font-size:12px"><?php echo $s . ". " .ucwords($s_text);?></span>
					</td>

				
					
				</tr>
				<?php?>
					<!-- <tr>
					<td style="padding: 10px">
						<span>welcome</span>
					</td>
					
				</tr> -->
			<?php	?>
			</table>
			<table  cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px; padding: 5px">
			<?php
			$labels_data = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$gphi->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
			$ld =0;
			foreach($labels_data as $ld)
			{
						if($ld->field_value=="")
							continue;
			?>
				<!-- <tr>
					<td style="font-size: 12px;">
							<span style="line-height:20px !important;font-size: 12px;font-weight: bold"><?php echo $ld->field_value?> : </span><span style="line-height:20px !important;font-size: 12px;"><?php echo $ld->option_value?></span> 
					</td>
				</tr> -->
	<?php $ld++; } ?>
			</table>
			<hr>
			<?php } ?>
	<?php }else{
		$section_tile = $this->Generic_model->getSingleRecord('section',array('section_id'=>$parent_section_id),'');
		?>
		<span style="font-weight: bold"><?php echo $section_tile->title; ?></span>
			
						<span style="font-weight: bold;"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?></span>
						<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px">
							<?php
								$labels_data1 = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$gphi->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
								$ld1=0;
								foreach($labels_data1 as $ld1)
								{
						if($ld1->field_value=="")
							continue;
								?>
									<!-- <tr>
										<td style="font-size: 12px;">
							<span style="line-height:20px !important;font-size: 12px;font-weight: bold"><?php echo $ld1->field_value?> : </span><span style="line-height:20px !important;font-size: 12px;"><?php echo $ld1->option_value?></span> 
										</td>
									</tr> -->
						<?php $ld1++; } ?>
						</table>
	
	<?php } $s++; }$cd++;
}
	 ?>
<!-- <hr> -->
<?php } ?>


<?php if(count($get_gpe_info)>0) { 
	?>
	<div class="cls_007"><span class="cls_007"  style="font-weight: bold; text-transform: uppercase;font-size: 12px">GENERAL PHYSICAL EXAMINATION</span></div>
	<?php 
	$s=1;
foreach($get_gpe_info as $gpeinf)
{

    $gpe_data = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$gpeinf->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
    $get_gpe_form = $this->db->select("form_name")->from("form")->where("form_id='".$gpeinf->form_id."'")->get()->row();
	
	foreach($gpe_data as $pl){
	$section_text = $this->db->query("select section_text from patient_form_line_items where patient_form_id='".$gpeinf->patient_form_id."' and section_id='".$pl->section_id."' and (section_text >'' or section_text IS NULL or section_text <'')")->row();
	
	if($section_text->section_text == '')
	{
		$s_text='';
	}else{
		$s_text=$section_text->section_text;
	}
	
	
	$section_image = $this->db->query("select scribbling_image from patient_form_scribbling_images where patient_form_id='".$gpeinf->patient_form_id."'")->result();
	
	$section = $this->Generic_model->getSingleRecord('section',array('section_id'=>$pl->section_id),'');
	$parent_section_id = $section->parent_section_id;
	if($parent_section_id==0){
	?>
	<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">
<?php if($s_text!=''){?>
<tr>
	<td style="padding: 10px">
		<span style="font-size:12px"><?php echo  $s . ". " .ucwords($s_text); ?></span>
	</td>
	
</tr>
<?php } ?>
<?php?>
	<!-- <tr>
	<td style="padding: 10px">
		<span>welcome</span>
	</td>
	
</tr> -->
<?php	?>
</table>

			<table  cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px; padding: 5px">
			<?php
			$labels_data = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$gpeinf->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
			$ld =0;
			foreach($labels_data as $ld)
			{
						if($ld->field_value=="")
							continue;
			?>
				<!-- <tr>
					<td style="font-size: 12px;">
							<span style="line-height:20px !important;font-size: 12px;font-weight: bold"><?php echo $ld->field_value?> : </span><span style="line-height:20px !important;font-size: 12px;"><?php echo $ld->option_value?></span> 
					</td>
				</tr> -->
	<?php $ld++; } ?>
			</table><hr>
		
	<?php }else{
		$section_tile = $this->Generic_model->getSingleRecord('section',array('section_id'=>$parent_section_id),'');
		?>
		<span style="font-weight: bold;font-size:12px;"><?php echo $section_tile->title; ?></span>
			
						<span style="font-weight: bold;"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?></span>
						<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px">
							<?php
								$labels_data1 = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$gpeinf->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
								$ld1=0;
								foreach($labels_data1 as $ld1)
								{
						if($ld1->field_value=="")
							continue;
								?>
									<!-- <tr>
										<td style="font-size: 12px;">
							<span style="line-height:20px !important;font-size: 12px;font-weight: bold"><?php echo $ld1->field_value?> : </span><span style="line-height:20px !important;font-size: 12px;"><?php echo $ld1->option_value?></span> 
										</td>
									</tr> -->
						<?php $ld1++; } ?>
						</table>
	<hr>
	<?php } $s++; }
}$cd++;
 } ?>
<?php 

 ?>
 <!-- SE -->
 <?php if(count($get_se_info)>0 || count($seShortData)>0) { 
	?>
	<div class="cls_007"><span class="cls_007"  style="font-weight: bold; text-transform: uppercase;font-size: 12px">SYSTEMIC EXAMINATION</span></div>
 <?php $cd=0;$c=1;
		foreach($seShortData as $seshort){?>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">

<tr>
	<td style="padding: 10px">
		<span style="font-size:12px"><?php echo  $c++ . ". " .ucwords($seshort->suggestion_name); ?></span>
	</td>


	
</tr>
<?php?>
<?php	?>
</table>
	<?php }?>

	<?php 
foreach($get_se_info as $gpeinf)
{
	$s=0;

    $gpe_data = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$gpeinf->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
    $get_gpe_form = $this->db->select("form_name")->from("form")->where("form_id='".$gpeinf->form_id."'")->get()->row();
	
	foreach($gpe_data as $pl){
	$section_text = $this->db->query("select section_text from patient_form_line_items where patient_form_id='".$gpeinf->patient_form_id."' and section_id='".$pl->section_id."' and (section_text >'' or section_text IS NULL or section_text <'')")->row();
	
	if($section_text->section_text == '')
	{
		$s_text='';
	}else{
		$s_text=$section_text->section_text;
	}
	
	
	$section_image = $this->db->query("select scribbling_image from patient_form_scribbling_images where patient_form_id='".$gpeinf->patient_form_id."'")->result();
	
	$section = $this->Generic_model->getSingleRecord('section',array('section_id'=>$pl->section_id),'');
	$parent_section_id = $section->parent_section_id;
	if($parent_section_id==0){
	?>
	<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">
	<?php if($s_text!=''){?>
<tr>
	<td style="padding: 10px">
	<span style="font-size: 12px;padding: 5px;margin-left:42px;">
	<?php $section->title; ?> 
	<?php echo $c++ . ". " .$s_text; ?></p></span>
	</td>
	
</tr>
	<?php } ?>
<?php?>
	<!-- <tr>
	<td style="padding: 10px">
		<span>welcome</span>
	</td>
	
</tr> -->
<?php	?>
</table>

			<table  cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px; padding: 5px">
			<?php
			$labels_data = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$gpeinf->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
			$ld =0;
			foreach($labels_data as $ld)
			{
						if($ld->field_value=="")
							continue;
			?>
				<!-- <tr>
					<td style="font-size: 12px;">
							<span style="line-height:20px !important;font-size: 12px;font-weight: bold"><?php echo $ld->field_value?> : </span><span style="line-height:20px !important;font-size: 12px;"><?php echo $ld->option_value?></span> 
					</td>
				</tr> -->
	<?php $ld++; } ?>
			</table>
		
	<?php }else{
		$section_tile = $this->Generic_model->getSingleRecord('section',array('section_id'=>$parent_section_id),'');
		?>
		<span style="font-weight: bold;font-size:12px"><?php echo $section_tile->title; ?></span>
			
						<span style="font-weight: bold;"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?></span>
						<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px">
							<?php
								$labels_data1 = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$gpeinf->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
								$ld1=0;
								foreach($labels_data1 as $ld1)
								{
						if($ld1->field_value=="")
							continue;
								?>
									<!-- <tr>
										<td style="font-size: 12px;">
							<span style="line-height:20px !important;font-size: 12px;font-weight: bold"><?php echo $ld1->field_value?> : </span><span style="line-height:20px !important;font-size: 12px;"><?php echo $ld1->option_value?></span> 
										</td>
									</tr> -->
						<?php $ld1++; } ?>
						</table><hr>
	
	
						<?php } $s++; }
}$cd++;
 } ?>
<?php  ?>
 <!-- SE -->
	 

	<?php if(count($patient_clinical_diagnosis)>0){ ?>
		<div class="cls_007"><span class="cls_007"  style="font-weight: bold; text-transform: uppercase;font-size: 12px">Clinical Diagnosis</span></div>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding:5px">
			<?php $cd=0;$c=1;foreach ($patient_clinical_diagnosis as $key => $value) {
				$cd_code = "(".$value->code.")";
				?>
				<tr>
					<td style="padding: 5px">
						<span style="font-size: 12px"><?php echo $c++ . ". " .ucwords($value->disease_name); ?>  <?=($value->code!='')?$cd_code:''?></span>
					</td>
					
				</tr>
				<?php $cd++;} ?>
			</table>
			<hr>
		<?php } ?>
		<?php if(count($patient_investigations)>0){ ?>
			<div class="cls_007">
				<span class="cls_007"  style="font-weight: bold; text-transform: uppercase;font-size: 12px">Investigations</span>
			</div>
			<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px">
				<tbody>
					<?php 
					$i=1;
					foreach ($patient_investigations as $key => $value) { 
						$inv_id = "(".$value->investigation_id.")";
						?>
						<tr>
							<td style="font-size: 12px;padding: 5px"><?php echo $i++ .". ".ucwords($value->investigation_name); ?> <?=($value->investigation_id!=0)?$inv_id:''?></td>
						</tr>
					<?php
					$inv_id = '';
					 } ?>
				</tbody>
			</table>
			<hr>
		<?php } ?>
		<?php if(count($patient_prescription)>0) { ?>
			<div class="cls_007"><span class="cls_007" style="font-weight: bold; font-size:12px;">PRESCRIPTION(Rx)</span></div>
			<table class="table" cellspacing="0" cellpadding="0" style="width: 100%;">
				<thead>
					<tr>
						<th style="font-size: 12px;background: #f2f2f2; border-bottom: 1px solid #ccc; width: 10%">S#</th>
						<th style="font-size: 12px;background: #f2f2f2; border-bottom: 1px solid #ccc; width: 40%">Medicine</th>
						<th style="font-size: 12px;background: #f2f2f2; border-bottom: 1px solid #ccc; width: 20%">Frequency</th>
						<th style="font-size: 12px;background: #f2f2f2; border-bottom: 1px solid #ccc; width: 15%">Duration</th>
						<th style="font-size: 12px;background: #f2f2f2; border-bottom: 1px solid #ccc; width: 15%">Qty</th>
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
								<span style="font-weight: bold; font-size: 12px;"><?php if($value->trade_name != NULL || $value->trade_name != '') { echo strtoupper($value->formulation." ".$value->trade_name); }else{ echo strtoupper($value->formulation." ".$value->medicine_name); } ?></span><br>
								<p style="font-size: 12px; color:rgb(84,84,84);"><?php echo $value->composition; ?></p>
							</td>
							<td>
								<?php if($value->day_schedule==""||$value->day_schedule==NULL){ ?>
									<span><?php if($value->day_dosage =="stat" || $value->day_dosage =="sos" || $value->day_dosage =="HS"){ echo $value->day_dosage; } else { echo $value->day_dosage." times in a ".$value->dosage_frequency; } ?></span><br><span style="font-size: 12px;color:rgb(84,84,84);"><small><?php echo "(".$value->drug_dose." ".$value->dosage_unit." each )"; ?></small></span>
								<?php } else { ?>
									<span><?php echo $M.'   -   '.$A.'   -   '.$N; ?></span><br><span><?php echo $dayM.'&nbsp;&nbsp;&nbsp;&nbsp; '.$dayA.'&nbsp;&nbsp;&nbsp;&nbsp; '.$dayN; ?></span><br><span><small><?php echo $value->drug_dose." ".$value->dosage_unit." each"; ?></small></span>
								<?php } ?>

							</td>
							<td><?php if($value->dose_course == 0 ){ echo "--"; } else { echo $value->dose_course." ".$freq."(s)"; } ?><br>
								<span style="font-weight: bold;text-transform: capitalize;font-size: 12px"><?=$intake?></span>
								<?php if($plang != "en"){ ?> <?php echo "(".ucfirst($Intake_converted).")"; } ?>
							</td>
							<td><?php if($value->quantity == 0 ){ echo "--"; } else { echo $value->quantity; } ?></td>
						</tr>
						<?php if($value->remarks !="") { ?>
							<tr>
								<td colspan="1" style="padding-top: 0"></td>
								<td colspan="4" style="padding-top: 0"><span style="font-weight:bold;font-size: 12px">Remarks: </span> <?php echo ucfirst($value->remarks); ?><?php if($plang != "en"){ ?> <br><?php echo "(".ucfirst($remark_converted).")"; } ?></td>
							</tr>
						<?php } ?>
					<?php } ?>
				</tbody>
			</table>
			<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px;vertical-align: top">
				<tr> <td><div  style="font-size: 12px;font-weight: bold;font-style: italic;">CAUTION : Take the medicine as prescribed by the doctor. Do not use in larger or smaller amounts than advised/prescribed. Use the medicine only for the prescribed purpose. Report any unusual symptoms to the doctor immediately. Do not stop the use of the medicine without consulting your doctor. Ensure that you complete the course as prescribed by your doctor.</div></td></tr>
			</table>
			<hr>
		<?php } ?>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px;vertical-align: top">
			<?php if($patient_prescription->general_instructions != ''){ ?>
				<tr>
					<td colspan="5" style="vertical-align: top; padding: 5px"><span style="font-weight: bold;  font-size:12px;">General Instructions: </span>
					<p style="font-size: 12px; padding: 5px;"><?=$patient_prescription->general_instructions?></p>
					</td>
				</tr>
			<?php } ?>
			<?php if($patient_prescription->plan != ''){ ?>
				<tr>
					<td colspan="5" style="vertical-align: top; padding: 5px"><span style="font-weight: bold; font-size: 12px">Plan: </span>
						<p style="font-size: 12px"><?=$patient_prescription->plan?></p>
					</td>
				</tr>
			<?php } ?>
		</table>
		<hr>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px">
			<tr>
				<td colspan="5">
					<span style="font-weight: bold; text-transform: uppercase;font-size: 12px">Followup Date: </span>
					<span style="font-size: 12px">
					<?php if($follow_up_date == "" ){echo "NA"; }else {echo date("d M Y",strtotime($follow_up_date));} ?></span>
				</td>
			</tr>
		</table>
		<hr>
		<div class="cls_007"><span class="cls_007" style="font-weight: bold; font-size:12px;">CLINICAL DIAGNOSIS</span></div>
		<?php
		$cdImgs = explode(",", $pcd->images);
		for($j=0;$j<count($cdImgs);$j++)
		{
			?>
			<img src="<?=base_url("uploads/clinical_diagnosis/").$cdImgs[$j]?>" style="width: 45%">
			<?php
		}

		
		?>
		<!-- <table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
			<tr>
				<td style="width:47%;text-align: left;vertical-align: bottom;">
					<span style="font-weight: bold; "><img src="<?php echo base_url('uploads/qrcodes/patients/'.$appointments->qrcode);?>"></span>
				</td>
				<td style="width:53%;text-align: right;vertical-align: middle;">
					<span style=" font-weight: bold; font-size: 12px"><?php echo 'DR. '. strtoupper($appointments->dfname.'  '. $appointments->dlname); ?></span>
				</td>
			</tr>
		</table> -->
		
		<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
			<tr>
				<td style="width:47%;text-align: left;vertical-align: bottom;">
					<span style="font-weight: bold; line-height: 25px;"><img src="<?php echo base_url('uploads/qrcodes/patients/'.$appointments->qrcode);?>"></span>
				</td>
				<td style="width:53%;text-align: right;vertical-align: middle;">
				<?php
			$digiSignInfo = $this->db->query("select * from digital_signatures where user_id='".$docInfo->doctor_id."'")->row();
			// echo $this->db->last_query();

			?>
				<p class="font-weight-bold text-right"><img src="<?=base_url('uploads/docDigitalSign/'.$digiSignInfo->digital_signature)?>" class="mr-3" style="width:200px !important"></p>	
				<p class="font-weight-bold text-right" style="font-weight:bold"><?php echo 'DR. '. strtoupper($appointments->dfname.'  '. $appointments->dlname); ?></p>
				<p class="font-weight-bold text-right">Registration Number:<?php echo $appointments->registration_code?></p>
					<!-- <span style=" font-weight: bold; line-height: 25px;"><?php echo 'DR. '. strtoupper($appointments->dfname.'  '. $appointments->dlname); ?> -->
				</td>
			</tr>
			<?php if($appointmentdetails->slot_type=='video call'){?>
			
			<tr>
			<td>
			<!-- <hr> -->
				<p style="font-weight: bold; line-height: 25px;">This Is Tele Consultation Appointment</p>
			</td>
			</tr>
			<?php }?>
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