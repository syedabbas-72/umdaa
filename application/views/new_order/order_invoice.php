
<!DOCTYPE html>

<html lang="en">

<head>

    <!-- Meta information -->

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="">

    <meta name="author" content="">


<link href="<?= base_url() ?>assets/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= base_url() ?>assets/css/pdf.css" rel="stylesheet">
    
    

</head>

<body style="font-family: 'Roboto', sans-serif;">
	<?php if($pdf_settings->header != 1){  
		?>
		<htmlpageheader name="firstpageheader">
		<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
			<tr>
				<td style="width:40%">
					<!-- <?php echo base_url(); ?>uploads/clinic_logos/<?php echo $clinic_logo." "; ?>  -->
					<img style="width:50%" alt="" src="<?php echo base_url(); ?>uploads/pharmacy_logos/<?php echo $clinic_logo." "; ?>">
				</td>
				<td style="width: 25%"></td>
				<td style="width:35%;text-align: right;">
					<span style="font-weight: bold; font-size: 14px">Address</span><br><span style="font-weight: 700;font-size: 14px"><?php echo $address; ?></span><br><span style=" font-weight: bold;font-size: 14px">Phone:</span><span style="font-weight: 700; font-size: 14px"><?php echo $clinic_phone; ?></span>
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
					<img style="width:50%" alt="" src="<?php echo base_url(); ?>uploads/pharmacy_logos/<?php echo $clinic_logo." "; ?>">
				</td>
				<td style="width: 25%"></td>
				<td style="width:35%;text-align: right;">
					<span style="font-weight: bold; font-size: 14px">Address</span><br><span style="font-weight: 700;font-size: 14px"><?php echo $address; ?></span><br><span style=" font-weight: bold;font-size: 14px">Phone:</span><span style="font-weight: 700; font-size: 14px"><?php echo $clinic_phone; ?></span>
				</td>
			</tr>
		</table>
			<hr>
		</htmlpageheader>
	<?php }
	elseif($pdf_settings->header==1)
	{
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
	}
		?>
				<?php
				if($billing_master->appointment_id==0)
				{
					if($billing_master->patient_id!="")
					{
						?>
						<table cellpadding="0" cellspacing="0" style="margin: 0px;font-size: 14px; padding: 0px; width: 100%;">
							<tr>
								<td style="width:50%">
									<span style="font-weight: bold;"><b><?php echo strtoupper($patient_name); ?></b></span><br>
									<b>Patient Id:</b>&nbsp;<?php echo $patient_id; ?><br>
									<?php 
									$moreInfo = '';
									if($gender) { 
										$moreInfo .= ucwords(substr($gender, 0, 1));
									} 
									if($age) { 
										if($moreInfo){
											$moreInfo .= ", ";
										}
										$moreInfo .= $age." ".ucwords(substr($age_unit, 0, 1));
									} 							
									?>
									<p><?php echo $moreInfo; ?></p>
								</td>
								<td style="width:50%;text-align: right;vertical-align: top">
									<?php if($paddress != ''){ ?>
										<span style="font-weight: bold">Address:</span><br><p><?php echo $paddress; ?></p>
									<?php } ?>
								</td>

							</tr>
						</table>
						<?php
					}
					else
					{
						?>
						<table cellpadding="0" cellspacing="0" style="margin: 0px;font-size: 14px; padding: 0px; width: 100%;">
							<tr>
								<td style="width:50%">
									<span style="font-weight: bold;"><b><?php echo strtoupper($guest_name); ?></b></span><br>
									<?php echo $billing_master->guest_mobile; ?><br>
									
								</td>
							</tr>
						</table>
						<?php
					}
					?>
					
					<?php
				}
				else
				{
					?>
					<table cellpadding="0" cellspacing="0" style="margin: 0px;font-size: 14px; padding: 0px; width: 100%;">
						<tr>
							<td style="width:30%">
								<span style="font-weight: bold;font-size: 14px"><b><?php echo strtoupper($patient_name); ?></b></span><br>
								<b>Patient Id:</b>&nbsp;<?php echo $patient_id; ?><br>
								<?php 
								$moreInfo = '';
								if($gender) { 
									$moreInfo .= ucwords(substr($gender, 0, 1));
								} 
								if($age) { 
									if($moreInfo){
										$moreInfo .= ", ";
									}
									$moreInfo .= $age." ".ucwords(substr($age_unit, 0, 1));
								} 							
								?>
								<p><?php echo $moreInfo; ?></p>
							</td>
							<td style="width:30%;text-align: center;vertical-align: top">
								<?php if($paddress != ''){ ?>
									<span style="font-weight: bold">Address:</span><br><p><?php echo $paddress; ?></p>
								<?php } ?>
							</td>
							<td style="width:30%;text-align: right;vertical-align: top;font-size: 14px">
								<span style="font-weight: bold"><b><?php echo "DR. ".strtoupper($doctorInfo->first_name." ".$doctorInfo->last_name); ?></b></span><br><span><?php echo strtoupper($doctorInfo->qualification. ", ". $department_name); ?> </span><br>
								<span><b>Reg. No:</b> &nbsp;<?php echo $doctorInfo->registration_code; ?> </span>							
							</td>
						</tr>
					</table>
					<?php
				}
				?>
				
				
<hr>
<?php

		$b=1;

		$total=0;

		foreach($billing_line_items as $bills){				
			$price = $bills->quantity * $bills->unit_price;
			
			// Accountable price if any discounts applying
            $accountablePrice =  $price - ($price * ($bills->discount / 100));
           
            // Taxation
            // Value inclding GST = mrp ($accountablePrice)
            // TaxValue = (mrp * 100)/(100 + CGST + SGST)
            $taxValue = ($accountablePrice * 100)/(100 + $bills->cgst + $bills->sgst + $bills->igst);

            $cgst = round($taxValue * ($bills->cgst / 100),2);
            $sgst = round($taxValue * ($bills->sgst / 100),2);
            $igst = round($taxValue * ($bills->igst / 100),2);

            $total = $total + $accountablePrice;

		}

		?>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">

					<tr>

						<td style="width: 550px;font-size: 14px;">

							<span style="font-size: 14px; line-height: 50px;font-weight: bold">Payments</span><br>

							Received with thanks, amount of <span style="font-weight: bold;">Rs.<?php echo number_format($total,2);?>/-</span> INR towards the following : 

						</td>

						<td style="width: 250px;">

							<table cellspacing="0" cellpadding="0" style="width: 100%">

								<tr>

									<td style="text-align: right;font-size: 14px">Date : <span style="font-weight: 600;"><b  style="font-weight: bold"><?php echo date('d/m/Y', strtotime($billing_master->created_date_time)); ?></b></span></td>

								</tr>


								

								<tr>

									<td style="text-align: right;font-size: 14px">Invoice Number : <span style="font-weight: 600;"><b style="font-weight: bold"><?php echo $billing_master->invoice_no; ?></b></span></td>

								</tr>

							</table>

						</td>

					</tr>

				</table>
<hr>
<table class="table-bordered table app-invoice" style="border-collapse: collapse;">
            <thead style="background: #f6f6f6;">
              <tr style="font-size: 18px">
                <th>S#</th>
                <th  style="font-size: 14px;">Drug Desc.</th>
                <th  style="font-size: 14px">Batch#</th>
                <th  style="font-size: 14px">HSN Code</th>
                <th  style="font-size: 14px" class="text-right">MRP / Unit</th>
                <th style="font-size: 14px" class="text-center">Qty</th>                
                <th style="font-size: 14px" class="text-center">Disc(%)</th>
                <th style="font-size: 14px" class="text-right">Value<br>In-GST</th>                
                <th style="font-size: 14px" class="text-right">TaxValue</th>
                <th style="font-size: 14px" class="text-center">CGST %<br>Amt.</th>
                <th style="font-size: 14px" class="text-center">SGST %<br>Amt</th>
                <th style="font-size: 14px" class="text-center">IGST %<br>Amt.</th>
              </tr>
            </thead>
            <tbody>

				<?php

				

				$i=1;

				$grand_total=0;
				$total_price=0;
				$total_discount=0;

				foreach($billing_line_items as $bills)

				{

				
				$price = ($bills->quantity * $bills->unit_price);
				
				// Accountable price if any discounts applying
	            $accountablePrice =  $price - ($price * ($bills->discount / 100));
	           	$total_price = ($total_price + $price);
           		$total_discount = (float)$total_discount + ($price * ($bills->discount / 100));

	            // Taxation
	            // Value inclding GST = mrp ($accountablePrice)
	            // TaxValue = (mrp * 100)/(100 + CGST + SGST)
	            $taxValue = number_format(($accountablePrice * 100)/(100 + $bills->cgst + $bills->sgst + $bills->igst),2);

	            $cgst = round($taxValue * ($bills->cgst / 100),2);
	            $sgst = round($taxValue * ($bills->sgst / 100),2);
	            $igst = round($taxValue * ($bills->igst / 100),2);

	            $grand_total = $grand_total + $accountablePrice;

				// $discount_price =  $price - ($price * ($bills->discount / 100));
				// $cgst = round($discount_price * ($bills->cgst / 100),2);
				// $sgst = round($discount_price * ($bills->sgst / 100),2);
				// $igst = round($discount_price * ($bills->igst / 100),2);
				// $total = round(($discount_price - ($cgst + $sgst + $igst)),2);
				// $grand1= round(($grand1+$total),2);

				?>
                            <tr>
                <td  style="font-size: 14px"class="text-center"><?php echo $i++; ?></td>
                <td style="font-size: 14px"><?php echo $bills->item_information; ?></td>
                <td style="font-size: 14px"><?php echo $bills->batch_no; ?></td>
                <td style="font-size: 14px"><?php echo $bills->hsn_code; ?></td>
                <td style="font-size: 14px" class="text-center"><?php echo number_format($bills->unit_price,2); ?></td>
                
                <td style="font-size: 14px" class="text-center"><?php echo $bills->quantity; ?></td>
                <td style="font-size: 14px" class="text-right"><span class="price"><?php echo $bills->discount; ?></span></td>
                <td style="font-size: 14px" class="text-right"><span class="price"><?php echo $accountablePrice; ?></span></td>
                <td style="font-size: 14px" class="text-right"><span class="price"><?php echo $taxValue; ?></span></td>
                <td style="font-size: 14px" class="text-center"><?php echo $bills->cgst; ?><br><span class="price"><?php echo $cgst; ?></span>&nbsp;&nbsp;<span class="tiny">INR</span></td>
                <td style="font-size: 14px" class="text-center"><?php echo $bills->sgst; ?><br><span class="price"><?php echo $sgst; ?></span>&nbsp;&nbsp;<span class="tiny">INR</span></td>
                <td style="font-size: 14px" class="text-center" a=""><?php echo $bills->igst; ?><br><span class="price"><?php echo $igst; ?></span>&nbsp;&nbsp;<span class="tiny">INR</span></td>
              </tr>
                

					<?php } ?>           
                 <tr>
                <td  colspan="12" style=" text-align: right;font-size: 14px;font-weight: bold;">
                  Total Cost : <span><?php echo number_format($total_price,2); ?> INR</span> 
                </td>
              </tr>
                <tr>
                <td  colspan="12" style=" text-align: right;font-size: 14px;font-weight: bold;">
                  Total Savings : <span><?php echo number_format($total_discount,2); ?> INR</span> 
                </td>
              </tr>
                <tr>
                <td  colspan="12" style=" text-align: right;font-size: 14px;font-weight: bold;">
                  Total Amount To Be  Payable : <span><?php echo number_format($grand_total,2); ?> INR</span> 
                </td>
              </tr>
             
            
              <tr>

						<td  colspan="12" style="padding: 10px; text-align: left;font-size: 14px;font-weight: bold;">

							Mode of Payment : <span><?php echo ucwords($billing_master->payment_mode); ?></span>

						</td>
					</tr>
					<?php
							if($billing_master->transaction_id!="")
							{
								?>
						<tr>
						<td colspan="12" style="padding: 10px; text-align: left;font-size: 14px;font-weight: bold;">
							
								Transaction ID : <span><?php echo ucwords($billing_master->transaction_id); ?></span>
								
						</td>

					</tr>
<?php
							}
							?>
					
            </tbody>
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
						<td style="font-size:14px; text-align: left;font-weight: bold">
							Powered by umdaa.co 
						</td>
					</tr>
				</table>
			</htmlpagefooter>
</body>	

</html>