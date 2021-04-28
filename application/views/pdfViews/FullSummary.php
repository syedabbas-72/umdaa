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
		<?php
		$cdImgs = explode(",", $pcd->images);
		for($j=0;$j<count($cdImgs);$j++)
		{
			?>
			<img src="<?=base_url("uploads/clinical_diagnosis/").$cdImgs[$j]?>" style="width: 45%">
			<?php
		}

		
		?>
		<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
			<tr>
				<td style="width:47%;text-align: left;vertical-align: bottom;">
					<span style="font-weight: bold; "><img src="<?php echo base_url('uploads/qrcodes/patients/'.$appointments->qrcode);?>"></span>
				</td>
				<td style="width:53%;text-align: right;vertical-align: middle;">
					<span style=" font-weight: bold; font-size: 12px"><?php echo 'DR. '. strtoupper($appointments->dfname.'  '. $appointments->dlname); ?></span>
				</td>
			</tr>
		</table>
		<div class="page-break"></div>

<!-- Full Summary Starts from here -->

<?php if(count($previous_documents)>0){ 
	if(count($vital_sign>0))
	{
		$this->load->view("reports/default_pdf_header");
	}
	?>

				<div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-transform: uppercase;">Medical Report</span></div>
				<?php 
            $i=1;
			
			
            foreach ($previous_documents as $key => $value) { ?>
					
						
						<?php
						 $images = trim($value->images, ",");
                $picture_explode = explode(",", $images);
                for ($k = 0; $k < count($picture_explode); $k++) { ?>
                    <img  src="<?php echo base_url('uploads/previous_documents/' . trim($picture_explode[$k])); ?>" style="width: 50%;" /><br><br>
              <?php  } ?>

						
						
						
					
					
					<?php } ?>
				
				
	<div class="page-break"></div>


		<?php } ?>
		
		<?php
		if(count($patient_consent_form)>0)
		{
			$this->load->view("reports/default_pdf_header");
			$i=0;
			foreach ($patient_consent_form as $value) {

				if($i>0)
				{

					?>
					<div class="page-break"></div>
					<?php
					$this->load->view("reports/default_pdf_header");
				}

				unset($before);
				unset($after);
				unset($during);

				$patient_consent_checklist = $this->db->query("select * from patient_checklist pc,checklist_master cm where pc.checklist_id=cm.checklist_id and  pc.appointment_id='".$appointments->appointment_id."' and pc.checked='1' and pc.patient_consent_form_id='".$value->patient_consent_form_id."' order by cm.checklist_id ASC")->result();

				?>
				<div style="font-weight: bold;font-size: 12px;text-transform: uppercase;">CONSENT FORM - <?=$value->consent_form_title?></div><br>
				<table style="width: 100%">
					<tr>
						<td style="font-size: 12px;"><span style="font-size: 12px;font-weight:bold;">Checked By : </span><?=$value->checked_by?></td>
						<td style="font-size: 12px;"><span style="font-size: 12px;font-weight:bold;">Procedure Done By : </span><?=$value->done_by?></td>
					</tr>
					<tr>
						<td style="font-size: 12px;"><span style="font-size: 12px;font-weight:bold;">Assisted By : </span><?=$value->assisted_by?></td>
						<td style="font-size: 12px;"><span style="font-size: 12px;font-weight:bold;">UHID NO. : </span><?=$value->umr_no?></td>
					</tr>
					<tr>
						<td style="font-size: 12px;"><span style="font-size: 12px;font-weight:bold;">Nurse : </span><?=$value->nurse?></td>
						<td style="font-size: 12px;"><span style="font-size: 12px;font-weight:bold;">Anesthetist : </span><?=$value->anesthetist?></td>
					</tr>
				</table><br>
				<!-- <span style="font-weight: bold; text-transform: uppercase;">Instructions Taken</span><br> -->
				<?php
				foreach ($patient_consent_checklist as $value) 
				{
					if($value->category=="before")
						$before[] = $value;
					elseif($value->category=="during")
						$during[] = $value;
					elseif($value->category=="after")
						$after[] = $value;
				}

				if(count($before)>0)
				{
					?>
					<span style="font-weight: bold;font-size: 12px;">Before Procedure Check</span><br>
					<?php
					foreach ($before as $value) {
						?>
						<span style="font-size: 12px;padding:5px;padding-top:12px;<?=($value->type=='Title')?'font-weight:bold':''?>"><?=($value->type!="Title")?'&#8227;':''?> <?=$value->description?></span><br>
						<?php
						if($value->remark!="")
						{
							?>
							<span style="font-size: 12px;padding:10px;margin-left: 25px;">&emsp;&emsp; &bull; <?=$value->remark?></span><br><br>
							<?php
						}
						?>
						<?php
					}
				}

				if(count($during)>0)
				{
					?>
					<span style="font-weight: bold;font-size: 12px;line-height: 25px">During Procedure Check</span><br>
					<?php
					foreach ($during as $value) {
						?>
						<span style="font-size: 12px;padding:5px;padding-top:12px;<?=($value->type=='Title')?'font-weight:bold':''?>"><?=($value->type!="Title")?'&#8227;':''?> <?=$value->description?></span><br>
						<?php
						if($value->remark!="")
						{
							?>
							<span style="font-size: 12px;padding:10px;margin-left: 25px;">&emsp;&emsp; &bull; <?=$value->remark?></span><br>
							<?php
						}
						?>
						<?php
					}
				}

				if(count($after)>0)
				{
					?>
					<span style="font-weight: bold;font-size: 12px;line-height: 25px">After Procedure Check</span><br>
					<?php
					foreach ($after as $value) {
						?>
						<span style="font-size: 12px;padding:5px;padding-top:12px;<?=($value->type=='Title')?'font-weight:bold':''?>"><?=($value->type!="Title")?'&#8227;':''?> <?=$value->description?></span><br>
						<?php
						if($value->remark!="")
						{
							?>
							<span style="font-size: 12px;padding:10px;margin-left: 25px;">&emsp;&emsp; &bull; <?=$value->remark?></span><br>
							<?php
						}
						?>
						<?php
					}
				}

				?>

				
				<?php
				$i++;
			}
			?>

			<?php
		}
		?>
	<div class="page-break"></div>
			
		<?php if(count($presenting_symptoms)>0){ 
$this->load->view("reports/default_pdf_header");
			?>
	<div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-transform: uppercase;">Symptoms</span></div><br>
				<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px">
					
				<?php $cd=0;$c=1;
				foreach ($presenting_symptoms as $key => $value) {
					$hopi_info = $this->db->query("select * from form where form_type='".HOPI."' and form_id='".$value->form_id."'")->row();
					?>           
         			<tr>
						<td>
							<span style="font-size: 12px"><?php echo $c++ . ". " .ucwords($value->symptom_data)." (".$value->time_span." ".$value->span_type.")"; ?></span>
						</td>
						
					</tr>
					<?php $cd++;} ?>
				</table>
						<!-- <div class="page-break"></div> -->
			<?php } ?>

<?php if(count($get_hopi_info)>0) { 
// $this->load->view("reports/default_pdf_header");
	?>

<br>
<div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-tranform: uppercase;">HOPI</span></div><br>
		
<?php
foreach ($get_hopi_info as $get_hinfo) 
{
	$s=0;
	$hopi_data = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$get_hinfo->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
    $get_hopi_form = $this->db->select("form_name")->from("form")->where("form_id='".$get_hinfo->form_id."'")->get()->row();
	
	foreach($hopi_data as $pl){
	$section_text = $this->db->query("select section_text from patient_form_line_items where patient_form_id='".$get_hinfo->patient_form_id."' and section_id='".$pl->section_id."' and (section_text >'' or section_text IS NULL or section_text <'')")->row();
	
	if($section_text->section_text == '')
	{
		$s_text='';
	}else{
		$s_text=$section_text->section_text;
	}
		
	
	$section = $this->Generic_model->getSingleRecord('section',array('section_id'=>$pl->section_id),'');
	$parent_section_id = $section->parent_section_id;
	if($parent_section_id==0){
	?>
	
		<span style="font-size: 12px;padding: 5px"><span style="font-weight: bold"><?php echo $section->title; ?>: </span><?php echo $s_text; ?></span><br>
		<?php
		$section_image = $this->db->query("select scribbling_image from patient_form_scribbling_images where patient_form_id='".$get_hinfo->patient_form_id."'")->result();
		$img = '';$s_image = '';
		
		foreach($section_image as $sec_image)
		{
			if($sec_image->scribbling_image == '' || $sec_image->scribbling_image == NULL)
			{
				$img='';
			}else{
				$s_image = $sec_image->scribbling_image;
				$s_image = ltrim($s_image, './');
				$head = array_change_key_case(get_headers(base_url($s_image), TRUE));
				$filesize = $head['content-length'];
				if($filesize>0)
					echo $img = "<img style='width:45%' src='".base_url($s_image)."' />";
			}
		}
		?>
				
		<table cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px; padding: 5px">
		<?php
		$labels_data = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$get_hinfo->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
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
		<span style="font-weight: bold;font-size: 12px;"><?php echo $section_tile->title; ?></span>				
		<span style="font-weight: bold;font-size: 12px;"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?></span>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px">
			<?php
				$labels_data1 = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$get_hinfo->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
				$ld1=0;
				foreach($labels_data1 as $ld1)
				{
						if($ld1->field_value=="")
							continue;
				?>
					<tr>
						<td style="font-size: 12px; ">
							<span style="line-height:20px !important;font-size: 12px;font-weight: bold"><?php echo $ld1->field_value?> : </span><span style="line-height:20px !important;font-size: 12px;"><?php echo $ld1->option_value?></span> 
						</td>
					</tr>
		<?php $ld1++; } ?>
		</table>
	
	<?php } $s++; } ?>
<div class="page-break"></div>
<?php 
}

}
?>



<?php if(count($get_past_history_info)>0) { 
$this->load->view("reports/default_pdf_header");
	?>
		
<div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-transform: uppercase;">Past History</span></div>

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
		<span style="font-size: 12px;padding: 5px"><font style="font-weight: bold"><?php echo $section->title; ?>: </font><?php echo $s_text; ?></span><br>
		<?php
		foreach ($section_image as $sec_image) 
		{
			if($sec_image->scribbling_image == '' || $sec_image->scribbling_image == NULL)
			{
				$img='';
			}else{
				$s_image = $sec_image->scribbling_image;
				$s_image = ltrim($s_image, './');
				$head = array_change_key_case(get_headers(base_url($s_image), TRUE));
				$filesize = $head['content-length'];
				if($filesize>0)
					echo $img = "<img style='width:45%' src='".base_url($s_image)."' />";
			}
		}
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
<?php } ?>



<?php if(count($get_personal_history_info)>0) { 
$this->load->view("reports/default_pdf_header");
	?>
		
<div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-transform: uppercase;">Personal History</span></div>

<?php 
foreach($get_personal_history_info as $gpshi)
{
	$s=0;

    $personal_history_data = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$gpshi->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
    $get_personal_history_form = $this->db->select("form_name")->from("form")->where("form_id='".$gpshi->form_id."'")->get()->row();
	
	foreach($personal_history_data as $pl){
	$section_text = $this->db->query("select section_text from patient_form_line_items where patient_form_id='".$gpshi->patient_form_id."' and section_id='".$pl->section_id."' and (section_text >'' or section_text IS NULL or section_text <'')")->row();
	
	if($section_text->section_text == '')
	{
		$s_text='';
	}else{
		$s_text=$section_text->section_text;
	}
	
	
	$section_image = $this->db->query("select scribbling_image from patient_form_scribbling_images where patient_form_id='".$gpshi->patient_form_id."'")->result();
	
	$section = $this->Generic_model->getSingleRecord('section',array('section_id'=>$pl->section_id),'');
	$parent_section_id = $section->parent_section_id;
	if($parent_section_id==0){
	?>
	
		<!-- <span style="font-size: 12px;font-weight:bold;padding: 5px 15px"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?></span> -->
		<span style="font-size: 12px;padding: 5px"><font style="font-weight: bold"><?php echo $section->title; ?>: </font><?php echo $s_text; ?></span><br>
		<?php
		foreach ($section_image as $sec_image) 
		{
			if($sec_image->scribbling_image == '' || $sec_image->scribbling_image == NULL)
			{
				$img='';
			}else{
				$s_image = $sec_image->scribbling_image;
				$s_image = ltrim($s_image, './');
				$head = array_change_key_case(get_headers(base_url($s_image), TRUE));
				$filesize = $head['content-length'];
				if($filesize>0)
					echo $img = "<img style='width:45%' src='".base_url($s_image)."' />";
			}
		}
		?>
		
		<br>
			<table  cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px; padding: 5px">
			<?php
			$labels_data = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$gpshi->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
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
								$labels_data1 = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$gpshi->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
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
<?php } ?>


<?php if(count($get_family_history_info)>0) { 
$this->load->view("reports/default_pdf_header");
	?>
		
<div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-transform: uppercase;">Family History</span></div>

<?php 
foreach($get_family_history_info as $gfhi)
{
	$s=0;

    $family_history_data = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$gfhi->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
    $get_family_history_form = $this->db->select("form_name")->from("form")->where("form_id='".$gfhi->form_id."'")->get()->row();
	
	foreach($family_history_data as $pl){
	$section_text = $this->db->query("select section_text from patient_form_line_items where patient_form_id='".$gfhi->patient_form_id."' and section_id='".$pl->section_id."' and (section_text >'' or section_text IS NULL or section_text <'')")->row();
	
	if($section_text->section_text == '')
	{
		$s_text='';
	}else{
		$s_text=$section_text->section_text;
	}
	
	
	$section_image = $this->db->query("select scribbling_image from patient_form_scribbling_images where patient_form_id='".$gfhi->patient_form_id."'")->result();
	
	$section = $this->Generic_model->getSingleRecord('section',array('section_id'=>$pl->section_id),'');
	$parent_section_id = $section->parent_section_id;
	if($parent_section_id==0){
	?>
	
		<!-- <span style="font-size: 12px;font-weight:bold;padding: 5px 15px"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?></span> -->
		<span style="font-size: 12px;padding: 5px"><font style="font-weight: bold"><?php echo $section->title; ?>: </font><?php echo $s_text; ?></span><br>
		<?php
		foreach ($section_image as $sec_image) 
		{
			if($sec_image->scribbling_image == '' || $sec_image->scribbling_image == NULL)
			{
				$img='';
			}else{
				$s_image = $sec_image->scribbling_image;
				$s_image = ltrim($s_image, './');
				$head = array_change_key_case(get_headers(base_url($s_image), TRUE));
				$filesize = $head['content-length'];
				if($filesize>0)
					echo $img = "<img style='width:45%' src='".base_url($s_image)."' />";
			}
		}
		?>
		
		<br>
			<table  cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px; padding: 5px">
			<?php
			$labels_data = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$gfhi->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
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
								$labels_data1 = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$gfhi->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
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
<?php } ?>



<?php if(count($get_treatment_history_info)>0) { 
$this->load->view("reports/default_pdf_header");
	?>
		
<div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-transform: uppercase;">Treatment History</span></div>

<?php 
foreach($get_treatment_history_info as $gthi)
{
	$s=0;

    $treatment_history_data = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$gthi->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
    $get_treatment_history_form = $this->db->select("form_name")->from("form")->where("form_id='".$gthi->form_id."'")->get()->row();
	
	foreach($treatment_history_data as $pl){
	$section_text = $this->db->query("select section_text from patient_form_line_items where patient_form_id='".$gthi->patient_form_id."' and section_id='".$pl->section_id."' and (section_text >'' or section_text IS NULL or section_text <'')")->row();
	
	if($section_text->section_text == '')
	{
		$s_text='';
	}else{
		$s_text=$section_text->section_text;
	}
	
	
	$section_image = $this->db->query("select scribbling_image from patient_form_scribbling_images where patient_form_id='".$gthi->patient_form_id."'")->result();
	
	$section = $this->Generic_model->getSingleRecord('section',array('section_id'=>$pl->section_id),'');
	$parent_section_id = $section->parent_section_id;
	if($parent_section_id==0){
	?>
	
		<!-- <span style="font-size: 12px;font-weight:bold;padding: 5px 15px"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?></span> -->
		<span style="font-size: 12px;padding: 5px"><font style="font-weight: bold"><?php echo $section->title; ?>: </font><?php echo $s_text; ?></span><br>
		<?php
		foreach ($section_image as $sec_image) 
		{
			if($sec_image->scribbling_image == '' || $sec_image->scribbling_image == NULL)
			{
				$img='';
			}else{
				$s_image = $sec_image->scribbling_image;
				$s_image = ltrim($s_image, './');
				$head = array_change_key_case(get_headers(base_url($s_image), TRUE));
				$filesize = $head['content-length'];
				if($filesize>0)
					echo $img = "<img style='width:45%' src='".base_url($s_image)."' />";
			}
		}
		?>
		
		<br>
			<table  cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px; padding: 5px">
			<?php
			$labels_data = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$gthi->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
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
								$labels_data1 = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$gthi->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
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
<?php } ?>


<?php if(count($get_social_history_info)>0) { 
$this->load->view("reports/default_pdf_header");
	?>
		
<div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-transform: uppercase;">Social History</span></div>

<?php 
foreach($get_social_history_info as $gschi)
{
	$s=0;

    $social_history_data = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$gschi->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
    $get_social_history_form = $this->db->select("form_name")->from("form")->where("form_id='".$gschi->form_id."'")->get()->row();
	
	foreach($social_history_data as $pl){
	$section_text = $this->db->query("select section_text from patient_form_line_items where patient_form_id='".$gschi->patient_form_id."' and section_id='".$pl->section_id."' and (section_text >'' or section_text IS NULL or section_text <'')")->row();
	
	if($section_text->section_text == '')
	{
		$s_text='';
	}else{
		$s_text=$section_text->section_text;
	}
	
	
	$section_image = $this->db->query("select scribbling_image from patient_form_scribbling_images where patient_form_id='".$gschi->patient_form_id."'")->result();
	
	$section = $this->Generic_model->getSingleRecord('section',array('section_id'=>$pl->section_id),'');
	$parent_section_id = $section->parent_section_id;
	if($parent_section_id==0){
	?>
	
		<!-- <span style="font-size: 12px;font-weight:bold;padding: 5px 15px"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?></span> -->
		<span style="font-size: 12px;padding: 5px"><font style="font-weight: bold"><?php echo $section->title; ?>: </font><?php echo $s_text; ?></span><br>
		<?php
		foreach ($section_image as $sec_image) 
		{
			if($sec_image->scribbling_image == '' || $sec_image->scribbling_image == NULL)
			{
				$img='';
			}else{
				$s_image = $sec_image->scribbling_image;
				$s_image = ltrim($s_image, './');
				$head = array_change_key_case(get_headers(base_url($s_image), TRUE));
				$filesize = $head['content-length'];
				if($filesize>0)
					echo $img = "<img style='width:45%' src='".base_url($s_image)."' />";
			}
		}
		?>
		
		<br>
			<table  cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px; padding: 5px">
			<?php
			$labels_data = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$gschi->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
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
								$labels_data1 = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$gschi->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
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
<?php } ?>


<?php if(count($get_gpe_info)>0) { 
$this->load->view("reports/default_pdf_header");
	?>
		
<div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-transform: uppercase;">General Physical Examination</span></div>

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
		<span style="font-size: 12px;padding: 5px"><font style="font-weight: bold"><?php echo $section->title; ?>: </font><?php echo $s_text; ?></span><br>
		<?php
		foreach ($section_image as $sec_image) 
		{
			if($sec_image->scribbling_image == '' || $sec_image->scribbling_image == NULL)
			{
				$img='';
			}else{
				$s_image = $sec_image->scribbling_image;
				$s_image = ltrim($s_image, './');
				$head = array_change_key_case(get_headers(base_url($s_image), TRUE));
				$filesize = $head['content-length'];
				if($filesize>0)
					echo $img = "<img style='width:45%' src='".base_url($s_image)."' />";
			}
		}
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
<?php } ?>

<?php if(count($get_se_info)>0) { 
$this->load->view("reports/default_pdf_header");
	?>
		<div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-tranform: uppercase;">Systemic Examination</span></div>
	<?php

foreach ($get_se_info as $gesei) 
{
	 $systemic_examination_data = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$gesei->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
     $get_se_form = $this->db->select("form_name")->from("form")->where("form_id='".$gesei->form_id."'")->get()->row();

        $s=0;
		
		$section_image = $this->db->query("select scribbling_image from patient_form_scribbling_images where patient_form_id='".$gesei->patient_form_id."'")->result();
		foreach ($section_image as $sec_image) 
		{
			if($sec_image->scribbling_image == '' || $sec_image->scribbling_image == NULL)
			{
				$img='';
			}else{
				$s_image = $sec_image->scribbling_image;
				$s_image = ltrim($s_image, './');
				$head = array_change_key_case(get_headers(base_url($s_image), TRUE));
				$filesize = $head['content-length'];
				if($filesize>0)
					echo $img = "<img style='width:45%' src='".base_url($s_image)."' />";
			}
		}

		foreach($systemic_examination_data as $pl){
		$img = '';
		$section_text = $this->db->query("select section_text from patient_form_line_items where patient_form_id='".$gesei->patient_form_id."' and section_id='".$pl->section_id."' and (section_text >'' or section_text IS NULL or section_text <'')")->result();
		
		if($section_text->section_text == '')
		{
			$s_text='';
		}else{
			$s_text=$section_text->section_text;
		}
		
		
		$section = $this->Generic_model->getSingleRecord('section',array('section_id'=>$pl->section_id),'');
		$parent_section_id = $section->parent_section_id;
		if($parent_section_id==0){
		?>
		
			<!-- <span style="font-size: 12px;font-weight:bold;padding: 5px 15px"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?></span><br> -->
			<span style="font-size: 12px;padding: 5px "><font style="font-weight: bold"><?php echo $section->title; ?>: </font><?php echo $s_text; ?></span>
			<br>
			
				<table class="table" cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px; padding: 5px">
				<?php
				$labels_data = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$gesei->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
				$ld =0;
				foreach($labels_data as $ld)
				{
					// if($ld->option_value=="")
					// 	continue;
					$in_img = '';
					if($ld->section_image == '' || $ld->section_image == NULL)
					{
						$in_img='';
					}else{
						$sc_image = $ld->section_image;
						$head = array_change_key_case(get_headers(base_url($sc_image), TRUE));
						$filesize = $head['content-length'];
						if($filesize>0)
							$in_img = "<img style='width:45%' src='".base_url($sc_image)."' />";
					}

				?>
				<tr>
					<td style="font-size: 12px;padding: 5px 15px">
						<span><?php if($ld->field_value!="" || $ld->field_value!=NULL) { echo $ld->field_value?> : <b><?php echo $ld->option_value; } ?></b></span> 
						<?=$in_img?>
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
					$labels_data1 = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$get_se_info->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
					$ld1=0;
					foreach($labels_data1 as $ld1)
					{
						// if($ld1->field_value=="")
						// 	continue;
					?>
						<tr>
							<td style="font-size: 12px;">
								<span><?php if($ld1->field_value!="" || $ld1->field_value!=NULL) { echo $ld1->field_value?> : <b><?php echo $ld1->option_value; } ?></b></span> 
							</td>
						</tr>
			<?php $ld1++; } ?>
			</table>
		
		<?php } $s++; }
}
?>
<div class="page-break"></div>
<?php } ?>



<?php if(count($get_other_systems_info)>0) { 
$this->load->view("reports/default_pdf_header");
	?>
		
<div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-transform: uppercase;">Other Systems</span></div>

<?php 
foreach($get_other_systems_info as $osd)
{
	$s=0;

    $osd_data = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$osd->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
    $get_osd_form = $this->db->select("form_name")->from("form")->where("form_id='".$osd->form_id."'")->get()->row();
	
	foreach($osd_data as $pl){
	$section_text = $this->db->query("select section_text from patient_form_line_items where patient_form_id='".$osd->patient_form_id."' and section_id='".$pl->section_id."' and (section_text >'' or section_text IS NULL or section_text <'')")->row();
	
	if($section_text->section_text == '')
	{
		$s_text='';
	}else{
		$s_text=$section_text->section_text;
	}
	
	
	$section_image = $this->db->query("select scribbling_image from patient_form_scribbling_images where patient_form_id='".$osd->patient_form_id."'")->result();
	
	$section = $this->Generic_model->getSingleRecord('section',array('section_id'=>$pl->section_id),'');
	$parent_section_id = $section->parent_section_id;
	if($parent_section_id==0){
	?>
	
		<!-- <span style="font-size: 12px;font-weight:bold;padding: 5px 15px"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?></span> -->
		<span style="font-size: 12px;padding: 5px"><font style="font-weight: bold"><?php echo $section->title; ?>: </font><?php echo $s_text; ?></span><br>
		<?php
		foreach ($section_image as $sec_image) 
		{
			if($sec_image->scribbling_image == '' || $sec_image->scribbling_image == NULL)
			{
				$img='';
			}else{
				$s_image = $sec_image->scribbling_image;
				$s_image = ltrim($s_image, './');
				$head = array_change_key_case(get_headers(base_url($s_image), TRUE));
				$filesize = $head['content-length'];
				if($filesize>0)
					echo $img = "<img style='width:45%' src='".base_url($s_image)."' />";
			}
		}
		?>
		
		<br>
			<table  cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px; padding: 5px">
			<?php
			$labels_data = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$osd->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
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
								$labels_data1 = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$osd->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
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
<?php } ?>

<?php
	if(count($patient_procedures)>0){
		$this->load->view("reports/default_pdf_header");
		$i=0;
		foreach ($patient_procedures as $value) {
			if($i>0)
			{
				?>
				<div class="page-break"></div>
				<?php
				$this->load->view("reports/default_pdf_header");
			}
			?>

			<div style="font-weight: bold;text-align: center;text-transform: uppercase"><?=$value->procedure_title?> - PROCEDURE</div><br>
			<table cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px;">
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Performed By : </span>
						<span style="font-size: 12px"><?=$value->surgeon?></span></td>
					<td><span style="font-weight: bold;font-size: 12px">Anaesthetist : </span>
						<span style="font-size: 12px"><?=$value->anesthetist?></span></td>
				</tr>
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Assisted Doctor : </span>
						<span style="font-size: 12px"><?=$value->assisting_surgeon?></span></td>
					<td><span style="font-weight: bold;font-size: 12px">Assisted Nurse : </span>
						<span style="font-size: 12px"><?=$value->assisting_nurse?></span></td>
				</tr>
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Type Of Anaesthesia : </span>
						<span style="font-size: 12px"><?=$value->type_of_anesthesia?></span></td>
				</tr>
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Preoperative Diagnosis : </span>
						<span style="font-size: 12px"><?=$value->preoperative_diagnosis?></span></td>
				</tr>
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Postoperative Diagnosis : </span>
						<span style="font-size: 12px"><?=$value->postoperative_diagnosis?></span></td>
				</tr>
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Indication : </span>
						<span style="font-size: 12px"><?=$value->indication?></span></td>
				</tr>
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Position : </span>
						<span style="font-size: 12px"><?=$value->position?></span></td>
				</tr>
			</table>
			<div style="font-size: 12px">
				<?=$value->medical_procedure?>
			</div>
			<?php
			$i++;
		}
		?>

	<!-- <div class="page-break"></div> -->
		<?php
	}
?>
<!-- Footer Info -->
<div class="row mt-5">
    <div class="col-md-6">
        <label class="font-weight-bold">POWERED BY UMDAA</label>
    </div>
    <div class="col-md-6 text-right">
        <label class="font-weight-bold"><?=date("d-m-Y h:i A", strtotime($appInfo->created_date_time))?></label>
    </div>
</div>