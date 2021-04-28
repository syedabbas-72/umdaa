

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

<body>

	<table cellpadding="0" cellspacing="0" style="width:800px; font-family: segoe ui; color: #333" align="center">

		<!-- header -->

		<tr>

			<td style="border-bottom:1px solid #ccc; padding:15px 10px;">

				<table cellspacing="0" cellpadding="0">

					<tr>

						<td style="width: 100px;"><img src="<?php echo base_url('uploads/clinic_logos/'.$clinic->clinic_logo);?>"/></td>

						<td style="width:370px; text-align: left;">

							<h2 style="font-size: 30px; padding:0px; margin: 0px;"><?php echo $clinic->clinic_name; ?></h2>

							<!--<h3 style="font-size: 16px; font-weight: 350; text-transform: uppercase; color: #757575; padding:0px; margin: 0px 0px 10px 0px;">Multi Speciality OPD Clinic</h3>-->

						</td>

						<td style="width:300px">

							<span style="color:#000; font-weight: 600">Address:</span>&nbsp;<?php echo $clinic->address; ?><br>

							

							<span style="color:#000; font-weight: 600">Phone:</span> <?php echo $clinic->clinic_phone; ?>

						</td>

					</tr>

				</table>

			</td>

		</tr>

		<tr>

			<td style="border-bottom: 1px solid #ccc; padding:15px 10px 20px 10px">

				<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">

					<tr>

						<td style="width:45%">

							<span style="color:#000; font-weight: 600; line-height: 25px;"><?php echo $patient->first_name;?></span><br>

							Patient Id: <?php echo $patient->umr_no; ?>

						</td>

						<td style="width:55%">

							<span style="color:#000; font-weight: 600; line-height: 25px;"><?php echo $patient->gender;?>, <?php echo $patient->age; ?> <?php echo $patient->age_unit; ?></span><br>

							<?php echo $patient->address_line; ?>

						</td>

					</tr>

				</table>

			</td>

		</tr>

		<?php

		$b=1;

		$grand=0;

		foreach($billing as $bill)

		{

			

			$grand=$grand+$bill->amount;

			$b++;

		}

		?>

		<tr>

			<td style="border-bottom: 1px solid #ccc">

				<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">

					<tr>

						<td style="width: 550px;">

							<span style="font-size: 28px; line-height: 50px">Payments</span><br>

							Received with thanks, amount of <?php echo $grand-$discount;?> INR towards the following : 

						</td>

						<td style="width: 250px;">

							<table cellspacing="0" cellpadding="0" style="width: 100%">

								<tr>

									<td style="text-align: right;">Date : <span style="font-weight: 600;"><?php echo date('d'); echo date('n'); echo date('Y'); ?></span></td>

								</tr>

								<tr>

									<td style="text-align: right; padding-bottom: 10px;">Receipt Number : <span style="font-weight: 600; padding-bottom: 10px;"><?php echo $invoice_no; ?></span></td>

								</tr>

								<tr>

									<td style="text-align: right; padding-top:10px;">Date : <span style="font-weight: 600; padding-top:10px;"><?php echo date('d'); echo date('n'); echo date('Y'); ?></span></td>

								</tr>

								<tr>

									<td style="text-align: right;">Invoice Number : <span style="font-weight: 600;"><?php echo $invoice_no; ?></span></td>

								</tr>

							</table>

						</td>

					</tr>

				</table>

			</td>

		</tr>

		<tr>

			<td style="background-color: #f5f5f5; padding:15px 10px; font-weight: 600; color:#000">

				<table cellspacing="0" cellpadding="0" style="width: 100%; text-align: center;">

					<tr>

						<td style="width:30px; border-right:1px dotted #999">S.No</td>

						<td style="width:335px; border-right:1px dotted #999; text-align: left; padding-left: 15px">Drug</td>

						<td style="width:100px; border-right:1px dotted #999; text-align: right; padding-left: 15px; padding-right: 15px">Unit Cost INR</td>

						<td style="border-right:1px dotted #999; text-align: center; padding-left: 15px; padding-right: 15px">Qty</td>

						<td style="width:150px; text-align: right; padding-left: 15px; padding-right: 15px">Total Cost INR</td>

					</tr>

				</table>

			</td>

		</tr>

		<tr>

			<td style="padding:10px;">

				<table cellspacing="0" cellpadding="0" style="width: 100%; text-align: center;">

				<?php

				

				$i=1;

				

				foreach($selected_drugs as $value)

				{

				$grand1=$grand1+$bills->amount;

				?>

					<tr>

						<td style="width:30px; border-right:1px dotted #ebebeb; padding-top:15px; padding-bottom: 15px; vertical-align: top"><?php echo $i++; ?></td>

						<td style="width:335px; border-right:1px dotted #ebebeb; text-align: left; padding-left: 15px; padding-top:15px; padding-bottom: 15px"><?php echo $value->trade_name; ?> 

						</td>

						<td style="width:100px; border-right:1px dotted #ebebeb; text-align: right; padding-left: 15px; padding-right: 15px; padding-top:15px; padding-bottom: 15px"><?php echo $value->mrp; ?></td>

						<td style="border-right:1px dotted #ebebeb; text-align: center; padding-left: 15px; padding-right: 15px; padding-top:15px; padding-bottom: 15px"><?php echo $value->quantity; ?></td>

						<td style="width:150px; text-align: right; padding-left: 15px; padding-right: 15px; padding-top:15px; padding-bottom: 15px;"><span style="color: #000; font-weight: 600;"><?php echo $value->mrp*$value->quantity; ?></span></td>

					</tr>

				

					<tr><td colspan="5" style="height: 10px;"></td></tr>
					<?php $price= $value->mrp*$value->quantity; 
					  $totalPrice += $price;    

					 } ?>

					<tr>

						<td colspan="2"></td>

					    <td colspan="3" style="padding: 10px; border-top:1px dotted #ebebeb; text-align: right">
                    
							Total Cost : <span style="color: #000; font-weight: 600;">Total <?php echo $totalPrice; ?> INR</span> 

						</td>

					</tr>

					<tr>

						<td colspan="2" style="padding: 10px; border-top:1px dotted #ebebeb; text-align: right;">Discount :<?php echo $discount; ?></td>

						<td colspan="3" style="padding: 10px; border-top:1px dotted #ebebeb; text-align: right">

							Grand Total : <span style="color: #000; font-weight: 600;"><?php echo $grand1-$discount; ?> INR</span> 

						</td>

					</tr>

					<tr><td colspan="5" style="height: 10px;"></td></tr>

					<tr>

						<td colspan="2"></td>

						<td colspan="3" style="padding: 10px; text-align: right">

							Amount Received on  : <span style="color: #000; font-weight: 600;"><?php echo $grand1-$discount; ?> INR</span> 

						</td>

					</tr>

					<tr>

						<td colspan="2"></td>

						<td colspan="3" style="padding: 10px; border-top:1px dotted #ebebeb; text-align: right">

							Balance Amount on  : <span style="color: #000; font-weight: 600;">0.00 INR</span> 

						</td>

					</tr>

					<tr><td colspan="5" style="height: 30px;"></td></tr>

					<tr>

						<td colspan="2" style="padding: 10px; text-align: left">

							Mode of Payment : <span style="color: #000; font-weight: 600;"><?php echo $payment_method; ?></span>

						</td>

					</tr>

					<tr>

						<td colspan="2" style="padding: 10px; text-align: left">

							Valid for Seven Days

						</td>

					</tr>

				</table>

			</td>

		</tr>

	</table>

</body>	

</html>