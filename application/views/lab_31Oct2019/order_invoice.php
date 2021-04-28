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

							<?php echo $address; ?>

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

						<td style="width:47%">

							<span style="color:#000; font-weight: 600; line-height: 25px;"><?php echo $patient_name;?></span><br>

							Patient Id: <?php echo $patient_id; ?>

						</td>

						<td style="width:53%">

							<span style="color:#000; font-weight: 600; line-height: 25px;"><?php echo $gender;?>, <?php echo $age; ?> </span><br>

							<?php echo $paddress; ?>

						</td>

					</tr>

				</table>

			</td>

		</tr>

		<?php

		$b=1;

		$total=0;

		foreach($billing_line_items as $bills){				
			$price = round(($bills->quantity * $bills->unit_price),2);
			
			

		}

		?>

		<tr>

			<td style="border-bottom: 1px solid #ccc">

				<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">

					<tr>

						<td style="width: 550px;">

							<span style="font-size: 28px; line-height: 50px">Payments</span><br>

							Received with thanks, amount of <?php echo $total;?> INR towards the following : 

						</td>

						<td style="width: 250px;">

							<table cellspacing="0" cellpadding="0" style="width: 100%">

								<tr>

									<td style="text-align: right;">Date : <span style="font-weight: 600;"><b><?php echo date('d-m-Y'); ?></b></span></td>

								</tr>

								<tr>

									<td style="text-align: right; padding-bottom: 10px;">Receipt Number : <span style="font-weight: 600; padding-bottom: 10px;"><b><?php echo $billing_master->receipt_no; ?></b></span></td>

								</tr>

								<tr>

									<td style="text-align: right; padding-top:10px;">Date : <span style="font-weight: 600; padding-top:10px;"><b><?php echo date('d-m-Y'); ?></b></span></td>

								</tr>

								<tr>

									<td style="text-align: right;">Invoice Number : <span style="font-weight: 600;"><b><?php echo $billing_master->invoice_no; ?></b></span></td>

								</tr>

							</table>

						</td>

					</tr>

				</table>

			</td>

		</tr>

		<tr>

			<td style="  color:#000">

				<table cellspacing="0" cellpadding="0" style="width: 100%; text-align: center;table-layout: fixed;">

					<thead style="font-weight: 600;background-color: #f5f5f5; ">
					<tr><td style="width:30px; border-right:1px dotted #999" rowspan="2">#</td><td style="width:270px; border-right:1px dotted #999; text-align: left; padding-left: 15px" rowspan="2">Investigation</td> <td style="width:70px; border-right:1px dotted #999; text-align: left; padding-left: 15px" rowspan="2">Item Code</td>
						<td style="width:70px; border-right:1px dotted #999; text-align: left; padding-left: 15px" rowspan="2">Short Form</td>
						<td style="width:70px; border-right:1px dotted #999; text-align: left; padding-left: 15px" rowspan="2">Category</td>
						<td style="width:40px; border-right:1px dotted #999; text-align: left; padding-left: 15px" rowspan="2">MRP</td></tr>

				

			</td>

		</tr>
	</thead>
	<tbody>
		<tr>

			<td>


				<?php

				

				$i=1;

				$grand_total=0;

				foreach($billing_line_items as $bills)

				{

				
				
	            $grand_total = $grand_total + $bills->mrp;

				// $discount_price =  $price - ($price * ($bills->discount / 100));
				// $cgst = round($discount_price * ($bills->cgst / 100),2);
				// $sgst = round($discount_price * ($bills->sgst / 100),2);
				// $igst = round($discount_price * ($bills->igst / 100),2);
				// $total = round(($discount_price - ($cgst + $sgst + $igst)),2);
				// $grand1= round(($grand1+$total),2);

				?>

					<tr>

						<td style="width:30px; border-right:1px dotted #ebebeb; border-left: 1px dotted #ebebeb;padding-top:15px; padding-bottom: 15px; vertical-align: top"><?php echo $i++; ?></td>

						<td style="width:270px; border-right:1px dotted #ebebeb; text-align: left; padding-left: 15px; padding-top:15px; padding-bottom: 15px"><?php echo $bills->investigation; ?>

						</td>
						<td style="width:70px; border-right:1px dotted #ebebeb; text-align: right; padding-left: 15px; padding-right: 15px; padding-top:15px; padding-bottom: 15px"><?php echo $bills->item_code; ?></td>
						<td style="width:50px; border-right:1px dotted #ebebeb; text-align: right; padding-left: 15px; padding-right: 15px; padding-top:15px; padding-bottom: 15px"><?php echo $bills->short_form; ?></td>
						<td style="width:50px; border-right:1px dotted #ebebeb; text-align: right; padding-left: 15px; padding-right: 15px; padding-top:15px; padding-bottom: 15px"><?php echo $bills->category; ?></td>						
						
						<td style="width:50px; border-right:1px dotted #ebebeb; text-align: right; padding-left: 15px; padding-right: 15px; padding-top:15px; padding-bottom: 15px"><?php echo $bills->mrp; ?></td>
						

						<!--<td style="width:100px; border-right:1px dotted #ebebeb;border-left: 1px dotted #ebebeb; text-align: center; padding-left: 5px"><span style="color: #000; font-weight: 600;"><?php echo $grand_total; ?></span></td>-->

					</tr>

				

					<tr><td colspan="6" style="height: 10px;"></td></tr>

					<?php } ?>

					<tr>

						<td colspan="5" style="padding: 10px; border-top:1px dotted #ebebeb;border-left: 1px dotted #ebebeb; text-align: right"></td>

						<td style="padding: 10px; border-right: 1px dotted #ebebeb;border-top:1px dotted #ebebeb; text-align: right">

							Total Cost : <span style="color: #000; font-weight: 600;"><?php echo ($invoice->iamt); ?> INR</span> 

						</td>

					</tr>

					<tr>

						
						<td colspan="5" style="padding: 10px; border-top:1px dotted #ebebeb;border-left: 1px dotted #ebebeb; text-align: right"></td>
						<td style="padding: 10px; border-top:1px dotted #ebebeb; border-right: 1px dotted #ebebeb; text-align: right">

							Grand Total : <span style="color: #000; font-weight: 600;"><?php echo $grand_total; ?> INR</span> 

						</td>

					</tr>

					<tr><td colspan="6" style="height: 10px;"></td></tr>

					<tr>

						<td colspan="5" style="padding: 10px;border-left: 1px dotted #ebebeb; border-top:1px dotted #ebebeb; text-align: right"></td>

						<td style="padding: 10px; border-top:1px dotted #ebebeb; border-right: 1px dotted #ebebeb; text-align: right">

							Amount Received on  : <span style="color: #000; font-weight: 600;"><?php echo $invoice->aamount; ?> INR</span> 

						</td>

					</tr>

					<tr>

						<td colspan="5" style="padding: 10px;border-left: 1px dotted #ebebeb; border-top:1px dotted #ebebeb; text-align: right"> </td>

						<td style="padding: 10px; border-top:1px dotted #ebebeb; border-right: 1px dotted #ebebeb; text-align: right">

							Balance Amount on  : <span style="color: #000; font-weight: 600;"><?php echo ($invoice->iamt-$invoice->aamount); ?> INR</span> 

						</td>

					</tr>

					<tr><td colspan="6" style="height:30px;padding: 10px; border-top:1px dotted #ebebeb; text-align: right"></td></tr>

					<tr>

						<td colspan="2" style="padding: 10px; text-align: left">

							Mode of Payment : <span style="color: #000; font-weight: 600;"><?php echo $billing_master->payment_mode; ?></span>

						</td>

					</tr>

					<tr>

						<td colspan="2" style="padding: 10px; text-align: left">

						

						</td>

					</tr>
				</td>
			</tr>
		</tbody>
				</table>

			</td>

		</tr>

	</table>

</body>	

</html>
