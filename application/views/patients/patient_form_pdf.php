
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

</head>
<body>
<h3><?=$form_name?></h3>
<table cellpadding="0" cellspacing="0" style="margin: 0px;font-size: 14px; padding: 0px; width: 100%;">
			<tr>
				<td style="width:30%">
					<?php 
					$patientName = '';
					if($patient_list->title){
						$patientName = ucwords($patient_list->title).". ";
					}
					$patientName .= $patient_list->first_name." ".$patient_list->last_name;
					?>
					<span style="font-weight: bold;"><b><?php echo strtoupper($patientName); ?></b></span><br>
					<b>Patient Id:</b>&nbsp;<?php echo $patient_list->umr_no; ?><br>
					<?php 
					$moreInfo = '';
					if($patient_list->gender) { 
						$moreInfo .= ucwords(substr($patient_list->gender, 0, 1));
					} 
					if($patient_list->age) { 
						if($moreInfo){
							$moreInfo .= ", ";
						}
						$moreInfo .= $patient_list->age." ".ucwords(substr($patient_list->age_unit, 0, 1));
					} 							
					?>
					<p><?php echo $moreInfo; ?></p>
				</td>
				<td style="width:30%;text-align: center;vertical-align: top">
					<?php if($patient_list->address_line != ''){ ?>
						<span style="font-weight: bold">Address:</span><br><p><?php echo $patient_list->address_line.", ".$patient_list->location; ?></p>
					<?php } ?>
				</td>
				<td style="width:30%;text-align: right;vertical-align: top;font-size: 13px">
					<span style="font-weight: bold"><b><?php echo "DR. ".strtoupper($doctor_info->first_name." ".$doctor_info->dlname); ?></b></span><br><span><?php echo strtoupper($doctor_info->qualification. ", ". $doctor_info->department_name); ?> </span><br>
					<span><b>Reg. No:</b> &nbsp;<?php echo $doctor_info->registration_code; ?> </span>							
				</td>
			</tr>
		</table>
		<hr>
	<table style="width: 100%">
		<tr>
			<td colspan="2">
				<span style="font-weight: bold;text-transform: capitalize;font-size: 13px">Doctor's quick note</span>
			</td>
		</tr>

		<tr>
		<?php
		 if(count($sugg)>0){?>
			<td>
			<span style="font-weight: bold;font-size: 12px">
			<?php echo $form_typee." data"; ?>
			</span><br>
			<?php $cd=0;$c=1; foreach($sugg as $sug){
		 ?>
		 	
			 <span style="font-weight: 500;font-size: 12px">
			 <?php echo $c++ . ". " .$sug->suggestion_name; ?><br>
			
				<!-- <span style="font-weight: 500;font-size: 12px"><?php echo $s_text; ?></span><br><br></td> -->
		 <?php } $cd++;?>
		 </span>
			 </td><?php } ?>
	
		</tr>

		
		<tr>
				<?php
		
				foreach($scribbling_images as $value)
				{
				
					if($value->scribbling_image != '')
					{
						    $img = $value->scribbling_image;					
						
						?>
						<td>
						<img src="<?=$img?>" style="width: 50%" >
						</td>
						<?php
					}
					else{
						?>
						<p>No Image</p>
						<?php
					}
				}
				?>
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
			$img='';
			$s_image='';
		}else{
			$s_image = $section_image->section_image;
			$s_image = ltrim($s_image, './');
			$img = "<img src='".$s_image."'  style='width:45%;float:right'/>";
		}
		
		$section = $this->Generic_model->getSingleRecord('section',array('section_id'=>$pl->section_id),'');
		$parent_section_id = $section->parent_section_id;
		if($parent_section_id==0){
		?>
		<tr>
			<td colspan="2">
				<span style="font-weight: bold;font-size: 12px"><?php echo $section->title; ?></span><br>
				<span style="font-weight: 500;font-size: 12px"><?php echo $s_text; ?></span><br><br>			
			<?php
			$head = array_change_key_case(get_headers(base_url($s_image), TRUE));
			$filesize = $head['content-length'];
			if($filesize>0)
			{
				echo $img;
			}
			?>
			
				<table style="width: 100%; padding: 15px 10px;padding-top: 0px">
				<?php
				$labels_data = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
				$ld =0;
				foreach($labels_data as $ld)
				{
						if($ld->field_value=="")
							continue;
				?>
					<tr>
						<td style="width: 25px;">
							<span style="line-height:20px !important;font-size: 12px;font-weight: bold"><?php echo $ld->field_value?> : </span><span style="line-height:20px !important;font-size: 12px;"><?php echo $ld->option_value?></span> 
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
			<td colspan="2">
				<table style="width: 100%;">
				
					<tr>
						<td style="width: 25px;">
				<span style="font-weight: bold;"><?php echo $section->title; ?></span><br>
				
				<span style="font-weight: 500;"><?php echo $s_text; ?></span><br><br>
							<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">
								<?php
									$labels_data1 = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
									$ld1=0;
									foreach($labels_data1 as $ld1)
									{
										if($ld1->field_value=="")
											continue;
									?>
										<tr>
											<td style="width: 25px;">
							<span style="line-height:20px !important;font-size: 12px;font-weight: bold"><?php echo $ld1->field_value?> : </span><span style="line-height:20px !important;font-size: 12px;"><?php echo $ld1->option_value?></span> 
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
	</table>
</body>	
</html>