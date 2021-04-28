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

						<td style="width: 100px;"><img src="<?php echo base_url('uploads/clinic_logos/'.$clinic_logo);?>"/></td>

						<td style="width:370px; text-align: left">

							<h2 style="font-size: 30px; padding:0px; margin: 0px;"><?php echo $clinic_name; ?></h2>

							<!--<h3 style="font-size: 16px; font-weight: 350; text-transform: uppercase; color: #757575; padding:0px; margin: 0px 0px 10px 0px;">Multi Speciality OPD Clinic</h3>-->

						</td>

						<td style="width:300px">

							<span style="color:#000; font-weight: 600">Address</span><br>

							<?php echo $clinic_address; ?>

							<span style="color:#000; font-weight: 600">Phone:</span> <?php echo $clinic_phone; ?>

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

							<span style="color:#000; font-weight: 600; line-height: 25px;"><?php echo $patient_name;?></span><br>

							Patient Id: <?php echo $umr_no; ?>

						</td>

						<td style="width:55%">

							

							<span style="color:#000; font-weight: 600; line-height: 25px;"><?php echo $doctor_name;?></span><br>

							<?php echo $qualification; ?>

						</td>

					</tr>

				</table>

			</td>

		</tr>

		<?php

		$b=1;

		$grand=0;

		foreach($updated_info as $bill)

		{

			


				$grand=$grand+ (round($bill['mrp'],2));

			$b++;

		}



		?>

		<tr>

			<td style="border-bottom: 1px solid #ccc">

				<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">

					<tr>

						<td style="width: 550px;">

							<span style="font-size: 28px; line-height: 50px">Payments</span><br>

							Received with thanks, amount of <?php echo $grand; ?> INR towards the following : 

						</td>

						<td style="width: 250px;">

							<table cellspacing="0" cellpadding="0" style="width: 100%">

								<tr>

									<td style="text-align: right;">Date : <span style="font-weight: 600;"><?php echo date('m'); echo date('d'); echo date('Y'); ?></span></td>

								</tr>

								<tr>

									<td style="text-align: right; padding-bottom: 10px;">Receipt Number : <span style="font-weight: 600; padding-bottom: 10px;"><?php echo $receipt_no;?></span></td>

								</tr>

								<tr>

									<td style="text-align: right; padding-top:10px;">Date : <span style="font-weight: 600; padding-top:10px;"><?php echo date('m'); echo date('d'); echo date('Y'); ?></span></td>

								</tr>

								<tr>

									<td style="text-align: right;">Invoice Number : <span style="font-weight: 600;"><?php echo $invoice_number?></span></td>

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

						<td style="width:30px; border-right:1px dotted #999">#</td>

						<td style="width:335px; border-right:1px dotted #999; text-align: left; padding-left: 15px">Name</td>

						<td style="width:335px; border-right:1px dotted #999; text-align: left; padding-left: 15px">Code</td>

						<td style="width:335px; border-right:1px dotted #999; text-align: left; padding-left: 15px">category</td>

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

				$grand1=0;

				foreach($updated_info as $bills)

				{
				

			

				?>

					<tr>

						<td style="width:30px; border-right:1px dotted #ebebeb; padding-top:15px; padding-bottom: 15px; vertical-align: top"><?php echo $i++; ?></td>

						<td style="width:335px; border-right:1px dotted #ebebeb; text-align: left; padding-left: 15px; padding-top:15px; padding-bottom: 15px">
							<span style="color: #000; font-weight: 600;"><?php echo $bills['investigation_name']; ?></span>
						</td>


						<td style="width:335px; border-right:1px dotted #ebebeb; text-align: left; padding-left: 15px; padding-top:15px; padding-bottom: 15px">
							<span style="color: #000; font-weight: 600;"><?php echo $bills['investigation_code']; ?></span>
						</td>


						<td style="width:335px; border-right:1px dotted #ebebeb; text-align: left; padding-left: 15px; padding-top:15px; padding-bottom: 15px">
							<span style="color: #000; font-weight: 600;"><?php echo $bills['category']; ?></span>
						</td>

						<td style="width:100px; border-right:1px dotted #ebebeb; text-align: right; padding-left: 15px; padding-right: 15px; padding-top:15px; padding-bottom: 15px"><?php echo round($bills['mrp'],2); ?></td>

						

					</tr>
				

					<tr><td colspan="5" style="height: 10px;"></td></tr>

					<?php } ?>

					<tr>

						<td colspan="2"></td>

						<td colspan="3" style="padding: 10px; border-top:1px dotted #ebebeb; text-align: right">

							Total Cost : <span style="color: #000; font-weight: 600;"><?php echo $grand; ?> INR</span> 

						</td>

					</tr>

					<tr>
						<td colspan="2" style="padding: 10px; border-top:1px dotted #ebebeb; text-align: right;">Discount :</td>
						<td colspan="3" style="padding: 10px; border-top:1px dotted #ebebeb; text-align: right">
							Grand Total : <span style="color: #000; font-weight: 600;"><?php echo $grand; ?> INR</span> 
						</td>
					</tr>
					

					<tr><td colspan="5" style="height: 30px;"></td></tr>

					<tr>

						<td colspan="2" style="padding: 10px; text-align: left">

							Mode of Payment : <span style="color: #000; font-weight: 600;"><?php echo $mode_of_payment; ?></span>

						</td>

					</tr>

				

				</table>

			</td>

		</tr>

	</table>

</body>	

</html>