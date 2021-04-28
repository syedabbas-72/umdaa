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
	<?php if($pdf_settings->header != 1){ ?>
		<htmlpageheader name="firstpageheader">
			<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
				<tr>
					<td style="width:40%">
						<!-- <?php echo base_url(); ?>uploads/clinic_logos/<?php echo $clinic_logo." "; ?>  -->
						<img style="height:50px;" alt="" src="<?php echo base_url(); ?>uploads/clinic_logos/<?php echo $clinic_information->clinic_logo." "; ?>">
					</td>
					<td style="width: 25%"></td>
					<td style="width:35%;text-align: right;">
						<span style="font-weight: bold; font-size: 14px">Address</span><br><span style="font-weight: 700;font-size: 14px"><?php echo $clinic_information->address; ?></span><br><span style=" font-weight: bold;font-size: 14px">Phone:</span><span style="font-weight: 700; font-size: 14px"><?php echo $clinic_information->clinic_phone; ?></span>
					</td>
				</tr>
			</table>
			<hr>
		</htmlpageheader>
		<htmlpageheader name="otherheader">
			<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
				<tr>
					<td style="width:40%">
						<!-- <?php echo base_url(); ?>uploads/clinic_logos/<?php echo $clinic_logo." "; ?>  -->
						<img style="width:50%" alt="" src="<?php echo base_url(); ?>uploads/clinic_logos/<?php echo $clinic_information->clinic_logo." "; ?>">
					</td>
					<td style="width: 25%"></td>
					<td style="width:35%;text-align: right;">
						<span style="font-weight: bold; font-size: 14px">Address</span><br><span style="font-weight: 700;font-size: 14px"><?php echo $clinic_information->address; ?></span><br><span style=" font-weight: bold;font-size: 14px">Phone:</span><span style="font-weight: 700; font-size: 14px"><?php echo $clinic_information->clinic_phone; ?></span>
					</td>
				</tr>
			</table>
			<hr>
		</htmlpageheader>
	<?php }elseif($pdf_settings->header == 1){
		$style = "margin-bottom: ".$pdf_settings->header_height."px !important;";
		?>
		<htmlpageheader name="firstpageheader">
			<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;<?=$style?>">
				<tr>
					<td>&nbsp;</td>
				</tr>
			</table>
			<hr>
		</htmlpageheader>
		<htmlpageheader name="otherheader">
			<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;<?=$style?>">
				<tr>
					<td>&nbsp;</td>
				</tr>
			</table>
			<hr>
		</htmlpageheader>
		<?php
	} ?>
	<table cellpadding="0" cellspacing="0" style="margin: 0px;font-size: 12px; padding: 0px; width: 100%;">
		<tr>
			<td style="width:30%;font-size: 12px">
				<?php 
				$patientName = '';
				if($billing_information->umr_no != 0) {
					if($billing_information->title){
						$patientName = ucwords($billing_information->title).". ";
					}
					$patientName .= $billing_information->patient_first_name." ".$billing_information->patient_last_name;					
				}else{
					$patientName = $billing_information->guest_name;
					$patientMobile = $billing_information->guest_mobile;					
				}

				?>
				<span style="font-weight: bold;"><b><?php echo strtoupper($patientName); ?></b></span><br>
				<b><?php echo ($billing_information->umr_no) ? "Patient Id: </b>&nbsp;".$billing_information->umr_no : ""; ?>
				<?php 
				$moreInfo = '';
				if($billing_information->gender) { 
					$moreInfo .= ucwords(substr($billing_information->gender, 0, 1));
				} 
				if($billing_information->age) { 
					if($moreInfo){
						$moreInfo .= ", ";
					}
					$moreInfo .= $billing_information->age." ".ucwords(substr($billing_information->age_unit, 0, 1));
				} 							
				?>
				<p><?php echo $moreInfo; ?></p>
			</td>
			<td style="width:30%;text-align: center;vertical-align: top;font-size: 12px">
				<?php if($billing_information->address_line != ''){ ?>
					<span style="font-weight: bold">Address:</span><br><p><?php echo $billing_information->address_line; ?></p>
				<?php } ?>
			</td>
			<td style="width:30%;text-align: right;vertical-align: top;font-size: 12px">
				<div style="<?=($pdf_settings->doc_details == 0)?'display: none':''?>">
					<?php if($billing_information->doc_first_name != ''){ ?>
						<span style="font-weight: bold;font-size: 12px"><b><?php echo "DR. ".strtoupper($billing_information->doc_first_name); ?></b></span><br><span><?php echo strtoupper($billing_information->doc_qualification. ", ". $billing_information->department_name); ?></span><br>
						<span><b>Reg. No:</b> &nbsp;<?php echo $billing_information->registration_code; ?></span>	
					<?php } ?>
				</div>						
			</td>
		</tr>
	</table>
	<hr>
	<?php
	// Bill Calculations & Information
	$totalAmount = (float)$billing_information->total_amount;
	$billingAmount = (float)$billing_information->billing_amount;
	$osa = (float)$billing_information->osa;
	$discount = (float)$billing_information->discount;
	$discountedAmount = $totalAmount * $discount/100;	

	// Payable amount = Billing Amount - Discounted Amount 
	$totalPayableAmount = $totalAmount - $discountedAmount;

	// Amount Paid Info
	$amountPaid = (float)$invoice_information->invoice_amount;
	?>

	<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px 10px">
		<tr>
			<td style="width: 60%; font-size: 12px ">
				<span style="line-height: 50px; font-size: 12px; font-weight:bold;">Payments</span><br>
				<span style="line-height: 20px; font-size: 12px">Received with thanks, amount of <span style="font-weight: bold"><?php echo number_format($amountPaid, 2); ?></span> INR towards the following:</span>
			</td>
			<td style="width: 40%;">
				<table cellspacing="0" cellpadding="0" style="width: 100%">
					<tr>
						<td style="text-align: right; padding-top:10px;font-size: 12px">Date : <span style="font-weight: 600; padding-top:10px;font-size: 12px"><?php echo date('d/m/Y',strtotime($invoice_information->invoice_date)); ?></span></td>
					</tr>
					<tr>
						<td style="text-align: right;font-size: 12px">Invoice Number : <span style="font-weight: 600;font-size: 12px"><?php echo $invoice_information->invoice_no_alias; ?></span></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

	<hr>

	<table class="app-invoice" cellspacing="0" cellpadding="0" style="width: 100%;">
		<tr>
			<th style="font-size: 12px; background: #333; padding:10px; color:#fff; width: 5%; text-align:right">#</th>
			<th style="font-size: 12px; background: #333; padding:10px; color:#fff; width: 75%; text-align:left">Investigation</th>
			<th style="font-size: 12px; background: #333; padding:10px; color:#fff; width: 20%; text-align: right;">Price</th>
		</tr>
		<tr>
			<td colspan="3">
				<pre>
				<?php print_r($billing_information); ?>
				</pre>
			</td>
		</tr>
		<?php
		$i = 1;
		foreach($billing_line_items as $bills) {
			?>
			<tr>
				<td style="font-size: 12px; width: 5%; padding:10px;text-align: right;"><?php echo $i++; ?></td>
				<td style="font-size: 12px; width: 50%; padding:10px;text-align: left;"><?php echo $bills['item_information']; ?></td>
				<td style="font-size: 12px; text-align: right; width: 10%; padding:10px; border-bottom: 1px solid #ccc; border-right:1px solid #ccc;text-align: right;">
					<span style="font-weight: 600; text-align: right"><?php echo number_format($bills['amount'],2); ?></span>
				</td>
			</tr>
		<?php } ?>
		<tr>
			<td colspan="2" style="text-align: right; padding: 10px; font-size: 12px;">Total Amount: </td>
			<td style="text-align: right; padding:10px; font-size: 12px;"><?=number_format($totalAmount,2)?></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: right; padding: 10px; font-size: 12px;">Discount (<?=$billing_information->discount?>%): </td>
			<td style="text-align: right; padding:10px; font-size: 12px;"><?=number_format($discountedAmount,2)?></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: right; padding: 10px; font-size: 12px;">Amount Payable: </td>
			<td style="text-align: right; padding:10px; font-size: 12px;"><?=number_format($totalPayableAmount,2)?></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: right; padding: 10px;font-size: 12px">Amount Paid (as <?=ucwords($invoice_information->payment_type)?>)</td>
			<td style="text-align: right; padding: 10px;font-size: 12px; font-weight: bold;"><?php echo number_format($amountPaid,2); ?></td>
		</tr>

		<?php if($discountedAmount > 0) { ?>
			<tr>
				<td colspan="2" style="text-align: right; padding: 10px;font-size: 12px">Total Savings</td>
				<td style="text-align: right; padding: 10px;font-size: 12px"><?php echo number_format($discountedAmount,2); ?></td>
			</tr>
		<?php } ?>

		<?php if($osa > 0) { ?>
			<tr>
				<td colspan="2" style="text-align: right; padding: 10px;font-size: 12px">Amount Due</td>
				<td style="color: #000; font-weight: bold; padding: 10px;font-size: 12px; text-align: right"><?php echo number_format($osa,2); ?></td>
			</tr>
		<?php } ?>

		<tr>
			<td colspan="3" style="padding: 10px; text-align: left; padding: 10px; font-size: 12px">
				Mode of Payment : <span style="font-weight: 600;"><?php echo ucwords($invoice_information->payment_mode); ?></span>
			</td>
		</tr>
	</table>

	<?php   
	if($pdf_settings->footer == 1){
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