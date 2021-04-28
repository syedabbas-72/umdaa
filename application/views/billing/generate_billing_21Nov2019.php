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
<body>
	<htmlpageheader name="firstpageheader">
		<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
			<tr>
				<td style="width:40%">
					<!-- <?php echo base_url(); ?>uploads/clinic_logos/<?php echo $clinic_logo." "; ?>  -->
					<img style="width:50%" alt="" src="<?php echo base_url(); ?>uploads/clinic_logos/<?php echo $clinic_logo." "; ?>">
				</td>
				<td style="width: 25%"></td>
				<td style="width:35%;text-align: right;">
					<span style="font-weight: bold; font-size: 14px">Address</span><br><span style="font-weight: 700;font-size: 14px"><?php echo $address; ?></span><br><span style=" font-weight: bold;font-size: 14px">Phone:</span><span style="font-weight: 700; font-size: 14px"><?php echo $clinic_phone; ?></span>
				</td>
			</tr>
		</table>
	</htmlpageheader>
	<hr>
	<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%; font-size: 14px;">
		<tr>
			<td style="width:40%">
				<span style="line-height: 25px; text-transform: uppercase;"><?php echo $patient_name;?></span><br>
				<b>Patient Id:</b> <?php echo $umr_no; ?>
			</td>
			<td style="width:30%; text-align: right">
				<span style="line-height: 25px;"><?php echo "<b>Gender:</b> ".$gender;?> </span>
			</td>
			<td style="width:30%; text-align: right">
				<span style="line-height: 25px; text-align: right"> <?php echo "<b>Age:</b> ".$age; ?> <?php echo $age_unit; ?></span>
			</td>
		</tr>
	</table>
	<hr>
	<?php
	$b=1;
	$grand = 0;
	$actualTotal = $billing_master->total_amount;
	$discount = $billing_master->discount;
	$unit = $billing_master->discount_unit;

	if($unit == '%'){
		$payableAmount = $actualTotal - ($actualTotal * $discount/100);	
		$discount = $actualTotal*$discount/100;
	}else if($unit == 'INR'){
		$payableAmount = $actualTotal - $discount;	
	}

	$totalDiscountINR = 0;

	// foreach($billing as $bill)
	// {
	// 	$amount = $bill->amount;
	// 	$discount = $bill->discount;
	// 	$unit = $bill->discount_unit;	
	// 	$actualTotal = $actualTotal + $amount;

	// 	if($discount == 0){
 //            $grand = $grand + $amount;
 //        }else{
 //            if($unit == "%"){
 //                $totalDiscountINR = $totalDiscountINR + ($amount * $discount/100);
 //                $amount = $amount - ($amount * $discount/100);
 //            }else if($unit == "INR"){
 //            	$totalDiscountINR = $totalDiscountINR + $discount;
 //                $amount = $amount - $discount;
 //            }
 //            $grand = $grand + $amount;
 //        }

	// 	$b++;
	// }
	?>
	<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px 10px">
		<tr>
			<td style="width: 60%; font-size: 14px ">
				<span style="line-height: 50px; font-size: 16px; font-weight:bold;">Payments</span><br>
				<span style="line-height: 20px; font-size: 13px;">Received with thanks, amount of <?php echo $payableAmount; ?> INR towards the following:</span>
			</td>
			<td style="width: 40%;">
				<table cellspacing="0" cellpadding="0" style="width: 100%">
					<tr>
						<td style="text-align: right; padding-top:10px;">Date : <span style="font-weight: 600; padding-top:10px;"><?php echo date('d/m/Y'); ?></span></td>
					</tr>
					<tr>
						<td style="text-align: right;">Invoice Number : <span style="font-weight: 600;"><?php echo $invoice_no_alias; ?></span></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

	<hr>

	<table class="table app-invoice" cellspacing="0" cellpadding="0" style="width: 100%;">
		<tr>
			<th style="font-size: 13px; background: #333; padding:10px; color:#fff; width: 5%; text-align:right">#</th>
			<th style="font-size: 13px; background: #333; padding:10px; color:#fff; width: 65%; text-align:left">Treatment & Product</th>
			<th style="font-size: 13px; background: #333; padding:10px; color:#fff; width: 30%; text-align: right;">Total Cost (INR)</th>
		</tr>
		<?php
		$i=1;
		$grand1=0;

		foreach($billing as $bills) {
			?>
			<tr>
				<td style="font-size: 13px; width: 5%; padding:10px; border-bottom: 1px solid #ccc; border-right:1px dotted #ccc;text-align: right;"><?php echo $i++; ?></td>
				<td style="font-size: 13px; width: 45%; padding:10px; border-bottom: 1px solid #ccc; border-right:1px dotted #ccc;text-align: left;"><?php echo $bills->item_information; ?> <br><span><small><?php echo $doctor_name.",".$qualification." ".$department_name; ?></small></span></td>
				<td style="font-size: 13px; text-align: right; width: 35%; padding:10px; border-bottom: 1px solid #ccc; border-right:1px solid #ccc;text-align: right;"><span style="font-weight: 600; text-align: right"><?php echo number_format($bills->amount,2); ?></span></td>
			</tr>
		<?php } ?>
		<tr>
			<td style="font-size: 13px;" colspan="3" style="text-align: right; padding: 10px;">
				Total Cost : <span style="font-weight: 600;"><?php echo $actualTotal; ?></span> 
			</td>
		</tr>
		<tr>
			<td style="font-size: 13px;" colspan="3" style="text-align: right; padding: 10px;">
				Discount in INR : <?php echo $discount; ?>
			</td>
		</tr>
		<tr>
			<td style="font-size: 13px;" colspan="3" style="text-align: right; padding: 10px;">
				Net Payable : <span style="color: #000; font-weight: 600;"><?php echo $payableAmount; ?> </span> 
			</td>
		</tr>
		<tr>
			<td style="font-size: 13px;" colspan="3" style="padding: 10px; text-align: left; padding: 10px;">
				Mode of Payment : <span style="font-weight: 600;"><?php echo ucfirst($payment_method); ?></span>
			</td>
		</tr>
		<tr>
			<td style="font-size: 13px;" colspan="3" style="padding: 10px; text-align: left; padding: 5px 10px;">
				Valid for <?php echo $review_days; ?> Days
			</td>
		</tr>
	</table>
	<htmlpagefooter name="footer">
		<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
			<tr>
				<td style="font-size:14px; text-align: left;">
					Powered by umdaa.co 
				</td>
			</tr>
		</table>
	</htmlpagefooter>
</body>	
</html>