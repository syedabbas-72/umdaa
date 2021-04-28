<pre>
<?php
$res_vital_sign = $this->db->query("SELECT a.vital_sign, a.vital_result, a.patient_id, a.vital_sign_recording_date_time,b.unit FROM `patient_vital_sign` a left join vital_sign b on(a.vital_sign = b.short_form)  WHERE a.vital_sign_recording_date_time =(select max(a.vital_sign_recording_date_time) FROM `patient_vital_sign` a where a.patient_id = '".$appointments->patient_id."' and a.clinic_id = '".$appointments->clinic_id."') and a.vital_result != '' and a.sign_type = 'generic' ORDER BY a.patient_vital_id ASC")->result();
// print_r($res_vital_sign);
?>
</pre>
<div class="container-fluid">
		<div class="card mt-5">
			<div class="card-body">
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8">
			<h5><img style="width:43%;margin-top:22px;" 
			alt="" 
			src="<?php echo base_url(); ?>
			uploads/clinic_logos/<?php echo $clinicInfo->clinic_logo; ?>"></h5>
		</div>
		<div class="col-md-4">
			<!-- <div class="row"> -->
		<b>Name:</b><?php echo $clinicInfo->clinic_name?>
		<div>
			<p><b>Location:</b><?php 
			if($clinicInfo->location != '')
			{
				echo $clinicInfo->location;
			}else{?>
			-
			<?php } ?><br>
		<b>Place:</b><?php if($clinicInfo->address != '')
			{
				echo $clinicInfo->address;
			}else{?>-
				<?php } ?><br>
			<b>For More Details Contact:</b>
            <?php if($clinicInfo->clinic_phone != '')
			{
				echo $clinicInfo->clinic_phone;
			}else{?>-
				<?php } ?><br></p>
        </div>
<!-- </div> -->
        </div>
	</div>
</div>
	<hr style="margin-top: 0px;">

	<div class="container-fluid">

		<b style="margin-left:35px;font-size:15px;">Patient Details</b>
    
	<div class="row">
	
		
		<div class="col-md-12" style="margin-left:0px;margin-left:35px;margin-top:16px;">
		
			<p style="display: inline;"><b style="display: inline;">Name:</b>
			<?=$patientInfo->title.". ".$patientInfo->first_name." ".$patientInfo->last_name?>
		   </p>
		
	
		   <p style="display: inline;"><b style="display: inline;">Age:</b>
		   <?php if($patientInfo->age !=''){ 
			   echo $patientInfo->age;
			   }
			   else{?>
			  -
			   <?php } ?>
		   </p>
		
		   <p style="display: inline;"><b style="display: inline;">Gender:</b>
		   <?php if($patientInfo->gender !=''){ 
			   echo $patientInfo->gender;
			   }
			   else{?>
			  -
			   <?php } ?>
		
		   </p>

		   <!-- <p style="display: inline;"><b style="display: inline;">UMR NO:</b>
		   <?php if($patientInfo->umr_no !=''){ 
			   echo $patientInfo->umr_no;
			   }
			   else{?>
			   UMR Number Not Registered
			   <?php } ?>
		
		   </p> -->
		   
			<p style="display: inline;"><b style="display: inline;">Date Of Consultation:</b>
			<?=date("d M Y",strtotime($appointments->appointment_date))?>
		   </p>
		   
			<!-- <p style="display: inline;"><b style="display: inline;">Name:</b>
		Hero
		   </p> -->

		</div>
	</div>
</div>

<hr>

<div>
<!-- <td colspan="3"> -->
				<!-- <span style="font-weight: bold; font-size:12px;text-transform: uppercase; padding: 5px;">Drug Allergy: </span> -->
				<!-- <span style="font-size: 12px"> -->
				<b style="margin-left:50px;">Drug Allergy: </b>
				<?php 
				if($appointments->allergy != '' || $appointments->allergy != null){
					echo ucwords($appointments->allergy);
				}else{
					echo "No allergy mentioned";
				}
				?></span>
			<!-- </td> -->
</div>
<hr>

<div class="container-fluid">
<b style="margin-left:35px;font-size:15px;">Vitals</b><?php if(count($vital_sign)==0){echo '&nbsp;&nbsp;&nbsp;-';}?>
<div class="row" style="margin-left:12px;margin-top:7px;">
<?php if(count($vital_sign)>0){ ?>
		<!-- <div class="cls_007"><span class="cls_007"  style="font-weight: bold; text-transform: uppercase;font-size: 12px">VITALS</span></div> -->
		<table cellspacing="0" cellpadding="0" style="margin-left:35px;font-size:15px;width: 100%;font-size: 12px;min-height:50px !important;padding-left: 0px !important,">
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
							}
							elseif($value->vital_sign == "Weight")
							{
								$vital_sign_results[0]['vital_sign'] = 'Weight';
								$vital_sign_results[0]['value'] = $value->vital_result;
									$vital_sign_results[0]['unit'] = $value->unit;
							}
							elseif($value->vital_sign == "Height")
							{
								$vital_sign_results[0]['vital_sign'] = 'Height';
								$vital_sign_results[0]['value'] = $value->vital_result;
									$vital_sign_results[0]['unit'] = $value->unit;
							}
							elseif($value->vital_sign == "RR")
							{
								$vital_sign_results[0]['vital_sign'] = 'RR';
								$vital_sign_results[0]['value'] = $value->vital_result;
									$vital_sign_results[0]['unit'] = $value->unit;
							}
							elseif($value->vital_sign == "Temp")
							{
								$vital_sign_results[0]['vital_sign'] = 'Temp';
								$vital_sign_results[0]['value'] = $value->vital_result;
									$vital_sign_results[0]['unit'] = $value->unit;
							}
							elseif($value->vital_sign == "SaO2")
							{
								$vital_sign_results[0]['vital_sign'] = 'SaO2';
								$vital_sign_results[0]['value'] = $value->vital_result;
									$vital_sign_results[0]['unit'] = $value->unit;
							}
							elseif($value->vital_sign == "WH Ratio")
							{
								$vital_sign_results[0]['vital_sign'] = 'WH Ratio';
								$vital_sign_results[0]['value'] = $value->vital_result;
									$vital_sign_results[0]['unit'] = $value->unit;
							}
							elseif($value->vital_sign == "BSA")
							{
								$vital_sign_results[0]['vital_sign'] = 'BSA';
								$vital_sign_results[0]['value'] = $value->vital_result;
									$vital_sign_results[0]['unit'] = $value->unit;
							}
							elseif($value->vital_sign == "BMI")
							{
								$vital_sign_results[0]['vital_sign'] = 'BMI';
								$vital_sign_results[0]['value'] = $value->vital_result;
									$vital_sign_results[0]['unit'] = $value->unit;
							}
							else{
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
	
	<?php }?>
		</div>

</div>
<hr>
<div class="container-fluid">
<b style="margin-left:35px;font-size:15px;">Symptoms</b><?php if(count($symptoms)==0){echo '&nbsp;&nbsp;&nbsp;-';}?>
<div class="row"  style="margin-left:-8px;margin-top:7px;">
			<!-- <div class="col-md-12"> -->
				<ul class="menu mb-0"  style="list-style: none;">
					<?php $cd=0;$c=1;
					if(count($symptoms)>0)
					{
						foreach($symptoms as $value)
						{
							?>
							<li><?php  echo $c++ . ". " .$value->symptom_data?></li>
							<?php
						}$cd++;
					}	
	
					?>
				</ul>
			<!-- </div> -->
		</div>

</div>

<hr>
<div class="container-fluid">
<b style="margin-left:35px;font-size:15px;">History</b>
<div class="row">
			<div class="col-md-12"  style="margin-left:-8px;margin-top:7px;">
		<!-- PAST -->
<?php if(count($get_past_history_info)>0) { 

	?>
		
<!-- <div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-transform: uppercase;">Past History</span></div> -->

<?php 
foreach($get_past_history_info as $gphi)
{
	$s=0;

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
	
	
	$section_image = $this->db->query("select scribbling_image from patient_form_scribbling_images where patient_form_id='".$gphi->patient_form_id."'")->result();
	
	$section = $this->Generic_model->getSingleRecord('section',array('section_id'=>$pl->section_id),'');
	$parent_section_id = $section->parent_section_id;
	if($parent_section_id==0){
	?>
	
		<!-- <span style="font-size: 12px;font-weight:bold;padding: 5px 15px"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?></span> -->
		<span style="font-size: 12px;padding: 5px;margin-left:36px;"><font style="font-weight: bold;font-size:15px;"><?php echo $section->title; ?>: </font><?php echo $s_text; ?></span><br>
		<?php
		// foreach ($section_image as $sec_image) 
		// {
		// 	if($sec_image->scribbling_image == '' || $sec_image->scribbling_image == NULL)
		// 	{
		// 		$img='';
		// 	}else{
		// 		$s_image = $sec_image->scribbling_image;
		// 		$s_image = ltrim($s_image, './');
		// 		$head = array_change_key_case(get_headers(base_url($s_image), TRUE));
		// 		$filesize = $head['content-length'];
		// 		if($filesize>0)
		// 			echo $img = "<img style='width:12%;margin-left:42px;' src='".base_url($s_image)."' />";
		// 	}
		// }
		?>
		
		<br>
			<table  cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px; padding: 5px">
			<?php
			$labels_data = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$gphi->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
			$ld =0;
			foreach($labels_data as $ld)
			{
						if($ld->field_value=="")
							continue;
			?>
				<tr>
					<td style="font-size: 12px;">
							<span style="line-height:20px !important;font-size: 12px;font-weight: bold"><?php echo $ld->field_value?> : </span><span style="line-height:20px !important;font-size: 12px;"><?php echo $ld->option_value?></span> 
					</td>
				</tr>
	<?php $ld++; } ?>
			</table>
		
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
									<tr>
										<td style="font-size: 12px;">
							<span style="line-height:20px !important;font-size: 12px;font-weight: bold"><?php echo $ld1->field_value?> : </span><span style="line-height:20px !important;font-size: 12px;"><?php echo $ld1->option_value?></span> 
										</td>
									</tr>
						<?php $ld1++; } ?>
						</table>
	
	<?php } $s++; }
}
	 ?>

<div class="page-break"></div>
<?php }else{ ?>
	<b style="margin-left:42px;font-size:15px;">Past History </b>&nbsp;&nbsp;&nbsp;-
<?php } ?>

			</div>

		</div>

</div>

<hr>
<div class="container-fluid">
<b style="margin-left:35px;font-size:15px;">Examination</b>
<div class="row">
			<div class="col-md-12"  style="margin-left:-8px;margin-top:7px;">
		
			<!-- se -->

<!-- gpe -->

<?php if(count($get_gpe_info)>0) { 
	?>
		
<!-- <div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-transform: uppercase;">General Physical Examination</span></div> -->

<?php 
foreach($get_gpe_info as $gpeinf)
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
	
		<!-- <span style="font-size: 12px;font-weight:bold;padding: 5px 15px"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?></span> -->
		<span style="font-size: 12px;padding: 5px;margin-left:42px;"><font style="font-weight: bold;font-size:15px;"><?php echo $section->title; ?>: </font><?php echo $s_text; ?></span><br>
		<?php
		// foreach ($section_image as $sec_image) 
		// {
		// 	if($sec_image->scribbling_image == '' || $sec_image->scribbling_image == NULL)
		// 	{
		// 		$img='';
		// 	}else{
		// 		$s_image = $sec_image->scribbling_image;
		// 		$s_image = ltrim($s_image, './');
		// 		$head = array_change_key_case(get_headers(base_url($s_image), TRUE));
		// 		$filesize = $head['content-length'];
		// 		if($filesize>0)
		// 			echo $img = "<img style='width:12%;margin-left:42px;' src='".base_url($s_image)."' />";
		// 	}
		// }
		?>
		
		<br>
			<table  cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px; padding: 5px">
			<?php
			$labels_data = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$gpeinf->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
			$ld =0;
			foreach($labels_data as $ld)
			{
						if($ld->field_value=="")
							continue;
			?>
				<tr>
					<td style="font-size: 12px;">
							<span style="line-height:20px !important;font-size: 12px;font-weight: bold"><?php echo $ld->field_value?> : </span><span style="line-height:20px !important;font-size: 12px;"><?php echo $ld->option_value?></span> 
					</td>
				</tr>
	<?php $ld++; } ?>
			</table>
		
	<?php }else{
		$section_tile = $this->Generic_model->getSingleRecord('section',array('section_id'=>$parent_section_id),'');
		?>
		<span style="font-weight: bold"><?php echo $section_tile->title; ?></span>
			
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
									<tr>
										<td style="font-size: 12px;">
							<span style="line-height:20px !important;font-size: 12px;font-weight: bold"><?php echo $ld1->field_value?> : </span><span style="line-height:20px !important;font-size: 12px;"><?php echo $ld1->option_value?></span> 
										</td>
									</tr>
						<?php $ld1++; } ?>
						</table>
	
	<?php } $s++; }
}
	 ?>

<div class="page-break"></div>
<?php } else{?>
	<b style="margin-left:42px;font-size:15px;">General PhysicalExamination</b>&nbsp;&nbsp;&nbsp;-
<!-- <p style="margin-left:28px;">-</p> -->
<?php }?>
<!-- gpe -->
<br>
<!-- SE -->
<?php if(count($get_se_info)>0) { 
	?>
			<b  style="margin-left:42px;font-size:15px;">Systemic Examination</b><br>
<!-- <div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-transform: uppercase;">General Physical Examination</span></div> -->

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
	
		<!-- <span style="font-size: 12px;font-weight:bold;padding: 5px 15px"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?></span> -->
		<span style="font-size: 12px;padding: 5px;margin-left:42px;"><font style="font-weight: bold;font-size:15px;"><?php echo $section->title; ?>: </font><?php echo $s_text; ?></span><br>
		<?php
		// foreach ($section_image as $sec_image) 
		// {
		// 	if($sec_image->scribbling_image == '' || $sec_image->scribbling_image == NULL)
		// 	{
		// 		$img='';
		// 	}else{
		// 		$s_image = $sec_image->scribbling_image;
		// 		$s_image = ltrim($s_image, './');
		// 		$head = array_change_key_case(get_headers(base_url($s_image), TRUE));
		// 		$filesize = $head['content-length'];
		// 		if($filesize>0)
		// 			echo $img = "<img style='width:12%;margin-left:42px;' src='".base_url($s_image)."' />";
		// 	}
		// }
		?>
		
		<br>
			<table  cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px; padding: 5px">
			<?php
			$labels_data = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$gpeinf->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
			$ld =0;
			foreach($labels_data as $ld)
			{
						if($ld->field_value=="")
							continue;
			?>
				<tr>
					<td style="font-size: 12px;">
							<span style="line-height:20px !important;font-size: 12px;font-weight: bold"><?php echo $ld->field_value?> : </span><span style="line-height:20px !important;font-size: 12px;"><?php echo $ld->option_value?></span> 
					</td>
				</tr>
	<?php $ld++; } ?>
			</table>
		
	<?php }else{
		$section_tile = $this->Generic_model->getSingleRecord('section',array('section_id'=>$parent_section_id),'');
		?>
		<span style="font-weight: bold"><?php echo $section_tile->title; ?></span>
			
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
									<tr>
										<td style="font-size: 12px;">
							<span style="line-height:20px !important;font-size: 12px;font-weight: bold"><?php echo $ld1->field_value?> : </span><span style="line-height:20px !important;font-size: 12px;"><?php echo $ld1->option_value?></span> 
										</td>
									</tr>
						<?php $ld1++; } ?>
						</table>
	
	<?php } $s++; }
}
	 ?>

<div class="page-break"></div>
<?php } else{?>
			<!-- se -->
				<b  style="margin-left:42px;font-size:15px;">Systemic Examination</b>&nbsp;&nbsp;&nbsp;-
				<!-- <p style="margin-left: 26px;">-</p> -->
				<?php } ?>
<!-- SE -->
			</div>
		</div>
</div>
<hr>

<div class="container-fluid">
<b style="margin-left:35px;font-size:15px;">Clinical Diagnosis</b><?php if(count($patient_clinical_diagnosis)==0)
{echo '&nbsp;&nbsp;&nbsp;-';}?>
<div class="row">
<div class="col-md-12" style="margin-left:18px;margin-top:7px;">
<?php if(count($patient_clinical_diagnosis)>0){ ?>
		<!-- <div class="cls_007"><span class="cls_007"  style="font-weight: bold; text-transform: uppercase;font-size: 12px">Clinical Diagnosis</span></div> -->
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding:5px;margin-left:15px;">
			<?php $cd=0;$c=1;foreach ($patient_clinical_diagnosis as $key => $value) {
				$cd_code = "(".$value->code.")";
				?>
				<tr>
					<td style="padding: 5px;font-size:14px;">
						<?php echo $c++ . ". " .ucwords($value->disease_name); ?>  <?=($value->code!='')?$cd_code:''?>
					</td>
					
				</tr>
				<?php $cd++;} ?>
			</table>
			<!-- <hr> -->
		<?php } ?>
			</div>	</div>
		<hr>

		<div class="container-fluid">
<b style="margin-left:17px;font-size:15px;">Investigation</b><?php if(count($patient_investigations)==0)
{echo '&nbsp;&nbsp;&nbsp;-';}?>
<div class="row">
			<div class="col-md-12" style="margin-left:-24px;margin-top:7px;">
				<?php
				if(count($patient_investigations)>0)
				{
					?>
					<ul class="menu mb-0" style="list-style: none;">
					<?php $cd=0;$c=1;
					foreach($patient_investigations as $value)
					{
						$inv_id = "(".$value->investigation_id.")";
						?>
						<li><?php  echo $c++ . ". " .ucwords($value->investigation_name)?> <?=($value->investigation_id!=0)?$inv_id:''?></li>
						<?php
					}
					$cd++;
					?>
					</ul>
					<?php
				}
				?>	
			</div>
		</div>

</div>
<hr>
<!-- <div class="page-break-after: always;"></div> -->
		<div class="container-fluid">
<b style="margin-left:20px;font-size:15px;">Prescription</b>
<div class="row">

					<?php 
					$i=1;
					$patient_prescription_drug=$this->db->query("select pd.drug_id,
					 pd.day_schedule, pd.preffered_intake,pd.day_dosage,
					 pd.drug_dose,pd.dosage_unit,pd.dosage_frequency,
					  pd.dose_course, pd.quantity,pd.medicine_name,d.formulation,
					  d.trade_name, d.composition,pd.remarks from
					   patient_prescription_drug pd left join drug d on 
					   (pd.drug_id=d.drug_id) where
					    pd.patient_prescription_id='" . $patient_prescription->patient_prescription_id . "' ")->result();?>
						<div class="col-md-12" style="margin-left:4px;margin-top:7px;">
						<?php if(count($patient_prescription_drug)>0) { ?>
						<!-- <div class="cls_007"><span class="cls_007" style="font-weight: bold; font-size:12px;">PRESCRIPTION(Rx)</span></div> -->
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
				<?php	foreach ($patient_prescription_drug as $key => $value) { 
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
								<td colspan="1" style="padding-top: 0;border:none;"></td>
								<td colspan="4" style="padding-top: 0;border:none;"><span style="font-weight:bold;font-size: 12px">Remarks: </span> <?php echo ucfirst($value->remarks); ?><?php if($plang != "en"){ ?> <br><?php echo "(".ucfirst($remark_converted).")"; } ?></td>
							</tr>
						<?php } ?>
					<?php } ?>
				</tbody>
			</table>
			<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px;vertical-align: top">
				<tr> <td><div  style="font-size: 12px;font-weight: bold;font-style: italic;">CAUTION : Take the medicine as prescribed by the doctor. Do not use in larger or smaller amounts than advised/prescribed. Use the medicine only for the prescribed purpose. Report any unusual symptoms to the doctor immediately. Do not stop the use of the medicine without consulting your doctor. Ensure that you complete the course as prescribed by your doctor.</div></td></tr>
			</table>
			<!-- <hr> -->
		<?php } ?>

			</div>
		</div>

</div>
<hr>
<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px;vertical-align: top">
			<?php $cd=0;$c=1; if($patient_prescription->general_instructions != ''){ ?>
				<tr>
					<td colspan="5" style="vertical-align: top; padding: 5px;margin-left: 35px;"><span style="font-weight: bold;  font-size:15px;margin-left: 35px;"><b>General Instructions: </b></span>
					<p style="font-size: 12px; padding: 5px;margin-left:30px;"><?php echo $c++ . ". " .$patient_prescription->general_instructions?></p>
					</td>
				</tr>
			<?php } $cd++;?>
			</table>
		<!-- <hr> -->
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px;vertical-align: top">
			<?php $cd=0;$c=1; if($patient_prescription->plan != ''){ ?>
				<tr>
					<td colspan="5" style="vertical-align: top; padding: 5px;"><span style="font-weight: bold; font-size: 15px;margin-left: 35px;"><b>Plan: </b></span>
						<p style="font-size: 12px;margin-left:18px;"><?php echo $c++ . ". " .$patient_prescription->plan?></p>
					</td>
				</tr>
			<?php } $cd++;?>
		</table>
		<!-- <hr> -->
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px">
			<tr>
				<td colspan="5">
					<span style="font-weight: bold; text-transform: uppercase;font-size: 15px;margin-left: 35px;"><b>Followup Date: </b></span>
					<span style="font-size: 12px">
					<?php if($follow_up_date == "" ){echo "NA"; }else {echo date("d M Y",strtotime($follow_up_date));} ?></span>
				</td>
			</tr>
		</table>
<hr>
<div class="row">
<div class="col-md-6">
<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
			<tr>
				<td style="width:47%;text-align: left;vertical-align: bottom;">
					<span style="font-weight: bold;margin-left:30px; "><img src="<?php echo base_url('uploads/qrcodes/patients/'.$appointments->qrcode);?>"></span>
				</td>
			</tr>
		</table>
</div>
			<div class="col-md-6">

			<?php
			$digiSignInfo = $this->db->query("select * from digital_signatures where user_id='".$docInfo->doctor_id."'")->row();

			?>
				<p class="font-weight-bold text-right"><img src="<?=base_url('uploads/docDigitalSign/'.$digiSignInfo->digital_signature)?>" class="mr-3" style="width:200px !important"></p>	
				<p class="font-weight-bold text-right"><?php echo 'DR. '. strtoupper($appointments->dfname.'  '. $appointments->dlname); ?></p>
				<p class="font-weight-bold text-right">Registration Number:<?php echo $appointments->registration_code?></p>
				
		
			</div>
		</div>
<hr>
		<div class="row"> 
		<div class="col-md-12">
		<?php
		// if(count($pcd->images)>0)
		// {
			$cdImgs = explode(",", $pcd->images);
			if(count($cdImgs)>0)
			{
				?>
				<!-- <p style="font-size:15px;margin-left:20px;"><b>Clinical Diagnosis Image</b></p> -->
				<?php
				for($j=0;$j<count($cdImgs);$j++)
				{
					?>
		
					<img src="<?=base_url("uploads/clinical_diagnosis/").$cdImgs[$j]?>" style="width: 20%;margin-left:37px;">
					<?php
				}
		
			}
		// }

		?>
		</div>
		</div>
				</div>
				</div>

				<div class="row mt-5 bg-primary text-white p-2">
    <div class="col-md-6">
        <label class="font-weight-bold text-white">POWERED BY UMDAA</label>
    </div>
    <div class="col-md-6 text-right">
        <label class="font-weight-bold pr-3 text-white"><?=date("d-m-Y h:i A", strtotime($appointments->created_date_time))?>&nbsp;</label>
    </div>
</div>