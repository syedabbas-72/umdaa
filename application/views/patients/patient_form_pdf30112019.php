
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
<h3><?php echo $form_name;?></h4>
	<table cellpadding="0" cellspacing="0" style="width:800px; font-family: segoe ui; color: #333" align="center">
		<tr>
			<td style="border-bottom: 1px solid #ccc; padding:15px 10px 20px 10px">
				<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
					<tr>
						<td style="width:45%">
							<span style="color:#000; font-weight: 600; line-height: 25px;"><b>Patient Name :</b>&nbsp;<?php echo $patient_list->title." ".$patient_list->first_name." ".$patient_list->middle_name." ".$patient_list->last_name; ?></span><br>
							Patient Id: &nbsp;<?php echo $patient_list->umr_no; ?>
						</td>
						<td style="width:55%">
							<span style="color:#000; font-weight: 600; line-height: 25px;"><b>Age/Gender:</b> &nbsp;<?php 
							if ($patient_list->age_unit == "years" || $patient_list->age_unit == "year") {
								$pau = "Y";
							} else if ($patient_list->age_unit == "months" || $patient_list->age_unit == "month") {
								$pau = "M";
							} else {
								$pau = "";
							}
							
							echo $patient_list->age .$pau. ' / '. $patient_list->gender; ?> </span><br>
							<b>Address:</b> &nbsp;<?php echo $patient_list->address_line; ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<h3>Doctor's quick note</h3>
				<br>
			</td>
		</tr>
		<tr>
			<td style="text-align: center">
				<img src="<?php echo base_url().$sticky_note_image; ?>" style="border:1px solid #000" >
			</td>
		</tr>
		<?php
		$s=0;
		
		foreach($plform_data as $pl){
		$section_text = $this->db->query("select section_text from patient_form_line_items where patient_form_id='".$patient_form_id."' and section_id='".$pl->section_id."' and (section_text >'' or section_text IS NULL or section_text <'')")->row();
		
		if($section_text->section_text == '')
		{
			$s_text='';
		}else{
			$s_text=$section_text->section_text;
		}
		
		
		$section_image = $this->db->query("select section_image from patient_form_line_items where patient_form_id='".$patient_form_id."' and section_id='".$pl->section_id."' and (section_image >'' or section_image IS NULL or section_image <'')")->row();
		
		if($section_image->section_image == '')
		{
			$s_image='';
		}else{
			$s_image = $section_image->section_image;
			$s_image = ltrim($s_image, './');
			$img = "<img src='".base_url($s_image)."' />";
		}
		
		$section = $this->Generic_model->getSingleRecord('section',array('section_id'=>$pl->section_id),'');
		$parent_section_id = $section->parent_section_id;
		if($parent_section_id==0){
		?>
		<tr>
			<td style="border-bottom: 1px solid #ccc"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?>
			
			<?php echo $img;?>
			
				<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">
				<?php
				$labels_data = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
				$ld =0;
				foreach($labels_data as $ld)
				{
				?>
					<tr>
						<td style="width: 25px;">
							<span><?php echo $ld->field_value?> : <b><?php echo $ld->option_value?></b></span> 
						</td>
					</tr>
		<?php $ld++; } ?>
				</table>
			</td>
		</tr>
		<?php }else{
			$section_tile = $this->Generic_model->getSingleRecord('section',array('section_id'=>$parent_section_id),'');
			?>
			<tr>
			<td style="border-bottom: 1px solid #ccc"><b><?php echo $section_tile->title; ?></b>
				<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">
				
					<tr>
						<td style="width: 25px;">
							<span style="font-weight: bold;"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?></span>
							<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">
								<?php
									$labels_data1 = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
									$ld1=0;
									foreach($labels_data1 as $ld1)
									{
									?>
										<tr>
											<td style="width: 25px;">
												<span><?php echo $ld1->field_value?> : <b><?php echo $ld1->option_value?></b></span> 
											</td>
										</tr>
							<?php $ld1++; } ?>
							</table>
						</td>
						
					
					</tr>
				</table>
			</td>
		</tr>
		<?php } $s++; } ?>
		
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