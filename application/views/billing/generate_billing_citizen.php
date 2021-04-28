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
<?php

?>

<body style="font-family: 'Roboto', sans-serif;">
		<htmlpageheader name="firstpageheader">
		<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
			<tr>
				<td style="width:40%">
					<!-- Image : <?php echo FCPATH; ?>uploads/clinic_logos/<?php echo $clinic_logo." "; ?>  -->
					<!-- <div style="background:url(<?=base_url()?>/uploads/clinic_logos/<?php echo $clinic_logo." "; ?>)">&nbsp;</div> -->
					<img style="width:50%" alt="" src="<?=base_url()?>/uploads/clinic_logos/<?php echo $clinic_logo." "; ?>">
				</td>
				<td style="width: 25%"></td>
				<td style="width:35%;text-align: right;">
					<span style="font-weight: bold; font-size: 14px">Address</span><br><span style="font-weight: 700;font-size: 14px"><?php echo $address; ?></span><br><span style=" font-weight: bold;font-size: 14px">Phone:</span><span style="font-weight: 700; font-size: 14px"><?php echo $clinic_phone; ?></span>
				</td>
			</tr>
		</table>
		</htmlpageheader>
			<hr>
<table cellpadding="0" cellspacing="0" style="margin: 0px;font-size: 12px; padding: 0px; width: 100%;">
	<tr>
		<td style="width:30%;font-size: 12px">
			<?php 
			// $patientName = '';
			// if($patientName->title){
			// 	$patientName = ucwords($patientInfo->title).". ";
			// }
			// $patientName .= $patientInfo->first_name." ".$patientInfo->last_name;
			?>
			<span style="font-weight: bold;"><b><?php echo strtoupper($patient_name); ?></b></span><br>
			<b>Patient Id:</b>&nbsp;<?php echo $umr_no; ?><br>
			<?php 
			$moreInfo = '';
			if($patientInfo->gender) { 
				$moreInfo .= ucwords(substr($patientInfo->gender, 0, 1));
			} 
			if($patientInfo->age) { 
				if($moreInfo){
					$moreInfo .= ", ";
				}
				$moreInfo .= $patientInfo->age." ".ucwords(substr($patientInfo->age_unit, 0, 1));
			} 							
			?>
			<p><?php echo $moreInfo; ?></p>
		</td>
		<td style="width:30%;text-align: center;vertical-align: top;font-size: 12px">
			<?php if($patientInfo->address_line != ''){ ?>
				<span style="font-weight: bold">Address:</span><br><p><?php echo $patientInfo->address_line; ?></p>
			<?php } ?>
		</td>
		<td style="width:30%;text-align: right;vertical-align: top;font-size: 12px">
			
			<span style="font-weight: bold;font-size: 12px"><b><?php echo "DR. ".strtoupper($doctor_name); ?></b></span><br><span><?php echo strtoupper($doctor_details->qualification. ", ". $department_name); ?> </span><br>
			<span><b>Reg. No:</b> &nbsp;<?php echo $doctor_details->registration_code; ?> </span>	
			<!-- </div>						 -->
		</td>
	</tr>
</table>
	<hr>
	<?php
	// $b=1;
	// $grand = 0;
	// $actualTotal = $billing_master->total_amount;
	// $discount = $billing_master->discount;
	// $unit = $billing_master->discount_unit;

	// if($unit == '%'){
	// 	$payableAmount = $actualTotal - ($actualTotal * $discount/100);	
	// 	$discount = $actualTotal*$discount/100;
	// }else if($unit == 'INR'){
	// 	$payableAmount = $actualTotal - $discount;	
	// }

	// $totalDiscountINR = 0;
	$total = 0;
	foreach($billing as $bill)
	{
		$discAmount = 0;
    	$paid = 0;
    	if($bill->item_information == "Pharmacy" || $bill->item_information == "Lab")
    		continue;
    	if($bill->status==2)
		  		continue;
	  	if($bill->discount_unit == "INR")
	  	{
	  		$discAmount = $bill->discount;
	  	}
	  	elseif($bill->discount_unit == "%")
	  	{
	  		$discAmount = (($bill->amount*$bill->discount)/100);
	  	}
	  	$paid = $bill->amount-$discAmount;
	  	$total += $paid;
	}
	?>
	<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px 10px">
		<tr>
			<td style="width: 60%; font-size: 12px ">
				<span style="line-height: 50px; font-size: 12px; font-weight:bold;">Payments</span><br>
				<span style="line-height: 20px; font-size: 12px">Received with thanks, amount of <span style="font-weight: bold"><?php echo number_format($total,2); ?></span> INR towards the following:</span>
			</td>
			<td style="width: 40%;">
				<table cellspacing="0" cellpadding="0" style="width: 100%">
					<tr>
					<!-- <td style="text-align: right; padding-top:10px;font-size: 12px">Date : <span style="font-weight: 600; padding-top:10px;font-size: 12px"><?php echo $billing_date; ?></span></td> -->
						<td style="text-align: right; padding-top:10px;font-size: 12px">Date : <span style="font-weight: 600; padding-top:10px;font-size: 12px"><?php echo date('d/m/Y',strtotime($billing_date)); ?></span></td>
					</tr>
					<tr>
						<td style="text-align: right;font-size: 12px">Invoice Number : <span style="font-weight: 600;font-size: 12px"><?php echo $invoice_no_alias; ?></span></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

	<hr>

	<table class="app-invoice" cellspacing="0" cellpadding="0" style="width: 100%;">
		<tr>
			<th style="font-size: 12px; background: #333; padding:10px; color:#fff; width: 5%; text-align:right">#</th>
			<th style="font-size: 12px; background: #333; padding:10px; color:#fff; width: 50%; text-align:left">Treatment & Product</th>
			<th style="font-size: 12px; background: #333; padding:10px; color:#fff; width: 10%; text-align: right;">Cost</th>
			<th style="font-size: 12px; background: #333; padding:10px; color:#fff; width: 15%; text-align: right;">Disc. Amt</th>
			<th style="font-size: 12px; background: #333; padding:10px; color:#fff; width: 15%; text-align: right;">Amt. Paid</th>
		</tr>
		<?php
		$i=1;
		$grand1=0;
		$total = 0;
		foreach($billing as $bills) {

			$discAmount = 0;
	    	$paid = 0;
	    	if($bills->item_information == "Pharmacy" || $bills->item_information == "Lab")
	    		continue;
	    	if($bills->status==2)
			  		continue;
		  	if($bills->discount_unit == "INR")
		  	{
		  		$discAmount = $bills->discount;
		  	}
		  	elseif($bills->discount_unit == "%")
		  	{
		  		$discAmount = (($bills->amount*$bills->discount)/100);
		  	}
		  	$paid = $bills->amount-$discAmount;
		  	$ptotal += $paid;
		  	$total += $bills->amount;
			?>
			<tr>
				<td style="font-size: 12px; width: 5%; padding:10px;text-align: right;"><?php echo $i++; ?></td>
				<td style="font-size: 12px; width: 50%; padding:10px;text-align: left;"><?php echo $bills->item_information; ?> <br><span><small><?php echo $doctor_name.",".$qualification." ".$department_name; ?></small></span></td>
				<td style="font-size: 12px; text-align: right; width: 10%; padding:10px; border-bottom: 1px solid #ccc; border-right:1px solid #ccc;text-align: right;">
					<span style="font-weight: 600; text-align: right"><?php echo number_format($bills->amount,2); ?></span></td>
				<td style="font-size: 12px; text-align: right; width: 15%; padding:10px; border-bottom: 1px solid #ccc; border-right:1px solid #ccc;text-align: right;">
					<span style="font-weight: 600; text-align: right"><?php echo number_format($discAmount,2); ?></span></td>
				<td style="font-size: 12px; text-align: right; width: 15%; padding:10px; border-bottom: 1px solid #ccc; border-right:1px solid #ccc;text-align: right;">
					<span style="font-weight: 600; text-align: right"><?php echo number_format($paid,2); ?></span></td>
			</tr>
		<?php } ?>
		<tr>
			<td colspan="5" style="text-align: right; padding: 10px;font-size: 12px">
				Total Amount : <span style="font-weight: 600;"><?php echo number_format($total,2); ?></span> 
			</td>
		</tr>
		<tr>
			<td colspan="5" style="text-align: right; padding: 10px;font-size: 12px">
				Total Savings : <?php echo number_format($total-$ptotal,2); ?>
			</td>
		</tr>
		<tr>
			<td colspan="5" style="text-align: right; padding: 10px;font-size: 12px">
				Total Amount To Be Payable : <span style="color: #000; font-weight: 600;"><?php echo number_format($ptotal,2); ?> </span> 
			</td>
		</tr>
		<tr>
			<td colspan="5" style="padding: 10px; text-align: left; padding: 10px;font-size: 12px">
				Mode of Payment : <span style="font-weight: 600;"><?php echo ucfirst($payment_method); ?></span>
			</td>
		</tr>
		<?php 
		if(!empty($transaction_id))
		{
			?>
			<tr>
				<td colspan="5" style="padding: 10px; text-align: left; padding: 10px;font-size: 12px">
					Transaction ID : <span style="font-weight: 600;"><?php echo ucfirst($transaction_id); ?></span>
				</td>
			</tr>
			<?php
		}
		?>
		<tr>
			<td colspan="5" style="padding: 10px; text-align: left; padding: 5px 10px;font-size: 12px">
				Valid for <?php echo $review_days; ?> Days
			</td>
		</tr>
	</table>
	<htmlpagefooter name="footer">
		<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
			<tr>
				<td style="font-size:12px; text-align: left;">
					Powered by umdaa.co 
				</td>
			</tr>
		</table>
	</htmlpagefooter>
		<?php   
		if($pdf_settings->footer==1)
		{
			$style = "margin-bottom: ".$pdf_settings->footer_height."px !important;";
		}
		?>
			<htmlpagefooter name="footer">
				<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;<?=$style?>">
					<tr>
						<td style="font-size:12px; text-align: left;">
							Powered by umdaa.co 
						</td>
					</tr>
				</table>
			</htmlpagefooter>
</body>	
</html>