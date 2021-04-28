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

<?php /*
<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
	<tr>
		<td style="width:40%">
			<span style="font-weight: bold; line-height: 25px;text-transform: uppercase;"><?php echo $appointments->pname;?></span><br>
			Patient Id: <?php echo $appointments->umr_no; ?>
		</td>
		<td style="width:30%">
			<span style=" font-weight: 600; line-height: 25px;"><?php echo "Gender: ".$appointments->gender;?>
		</td>
		<td style="width:30%">
			<span style=" font-weight: 600; line-height: 25px;"> <?php echo "Age: ".$appointments->age; ?> </span>
		</td>
	</tr>
</table>
*/ ?>
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