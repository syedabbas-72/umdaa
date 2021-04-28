<!DOCTYPE html>

<html lang="en">

<head>

    <!-- Meta information -->

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="">

    <meta name="author" content="">


<link href="<?= base_url() ?>assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= base_url() ?>assets/scss/pdf.css" rel="stylesheet">
    

</head>

<body>
	<table cellspacing="0" cellpadding="0">

					<tr>

						<td style="width: 100px;"><img src="<?php echo base_url('uploads/clinic_logos/'.$clinic_logo);?>"/></td>

						<td style="width:270px; text-align: left">

							<h2 style="font-size: 30px; padding:0px; margin: 0px;"><?php echo $clinic_name; ?></h2>

							<!--<h3 style="font-size: 16px; font-weight: 350; text-transform: uppercase; color: #757575; padding:0px; margin: 0px 0px 10px 0px;">Multi Speciality OPD Clinic</h3>-->

						</td>

						<td style="width:400px;">

							<span style="font-weight: bold;font-size: 25px">Address</span><br>

							<span style="font-weight: 700;font-size: 25px"><?php echo $address; ?></span><br>

							<span style=" font-weight: bold;font-size: 25px">Phone:</span><span style="font-weight: 700;font-size: 25px"> <?php echo $clinic_phone; ?></span>

						</td>

					</tr>

				</table>
				<hr>
				<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">

					<tr>

						<td style="width:47%">

							<span style="font-weight: bold; line-height: 25px;"><?php echo $patient_name;?></span><br>

							Patient Id: <?php echo $patient_id; ?>

						</td>

						<td style="width:53%">

							<span style=" font-weight: 600; line-height: 25px;"><?php echo $gender;?>, <?php echo $age; ?> </span><br>

							<?php echo $paddress; ?>

						</td>

					</tr>

				</table>
<hr>
<?php

		$b=1;

		$total=0;

		foreach($billing_line_items as $bills){				
			$price = round(($bills->quantity * $bills->unit_price),2);
			
			// Accountable price if any discounts applying
            $accountablePrice =  round($price - ($price * ($bills->discount / 100)),2);
           
            // Taxation
            // Value inclding GST = mrp ($accountablePrice)
            // TaxValue = (mrp * 100)/(100 + CGST + SGST)
            $taxValue = round(($accountablePrice * 100)/(100 + $bills->cgst + $bills->sgst + $bills->igst),2);

            $cgst = round($taxValue * ($bills->cgst / 100),2);
            $sgst = round($taxValue * ($bills->sgst / 100),2);
            $igst = round($taxValue * ($bills->igst / 100),2);

            $total = $total + $accountablePrice;

		}

		?>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">

					<tr>

						<td style="width: 550px;">

							<span style="font-size: 25px; line-height: 50px">Payments</span><br>

							Received with thanks, amount of <?php echo $total;?> INR towards the following : 

						</td>

						<td style="width: 250px;">

							<table cellspacing="0" cellpadding="0" style="width: 100%">

								<tr>

									<td style="text-align: right;">Date : <span style="font-weight: 600;"><b  style="font-weight: bold"><?php echo date('d-m-Y'); ?></b></span></td>

								</tr>

								<tr>

									<td style="text-align: right; padding-bottom: 10px;">Receipt Number : <span style="font-weight: 600; padding-bottom: 10px;"><b style="font-weight: bold"><?php echo $billing_master->receipt_no; ?></b></span></td>

								</tr>

								<tr>

									<td style="text-align: right; padding-top:10px;">Date : <span style="font-weight: 600; padding-top:10px;"><b style="font-weight: bold"><?php echo date('d-m-Y'); ?></b></span></td>

								</tr>

								<tr>

									<td style="text-align: right;">Invoice Number : <span style="font-weight: 600;"><b style="font-weight: bold"><?php echo $billing_master->invoice_no; ?></b></span></td>

								</tr>

							</table>

						</td>

					</tr>

				</table>
<hr>
<table class="table-bordered table">
            <thead style="background: #f6f6f6;">
              <tr style="font-size: 22px">
                <th>S#</th>
                <th  style="font-size: 18px">Drug Desc.</th>
                <th  style="font-size: 18px">Batch#</th>
                <th  style="font-size: 18px">HSN Code</th>
                <th  style="font-size: 18px" class="text-right">MRP / Unit</th>
                <th style="font-size: 18px" class="text-center">Qty</th>                
                <th style="font-size: 18px" class="text-center">Disc(%)</th>
                <th style="font-size: 18px" class="text-right">Value<br>In-GST</th>                
                <th style="font-size: 18px" class="text-right">TaxValue</th>
                <th style="font-size: 18px" class="text-center">CGST %<br>Amt.</th>
                <th style="font-size: 18px" class="text-center">SGST %<br>Amt</th>
                <th style="font-size: 18px" class="text-center">IGST %<br>Amt.</th>
              </tr>
            </thead>
            <tbody>

				<?php

				

				$i=1;

				$grand_total=0;

				foreach($billing_line_items as $bills)

				{

				
				$price = round(($bills->quantity * $bills->unit_price),2);
				
				// Accountable price if any discounts applying
	            $accountablePrice =  round($price - ($price * ($bills->discount / 100)),2);
	           
	            // Taxation
	            // Value inclding GST = mrp ($accountablePrice)
	            // TaxValue = (mrp * 100)/(100 + CGST + SGST)
	            $taxValue = round(($accountablePrice * 100)/(100 + $bills->cgst + $bills->sgst + $bills->igst),2);

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
                <td  style="font-size: 18px"class="text-center"><?php echo $i++; ?></td>
                <td style="font-size: 18px"><?php echo $bills->item_information; ?></td>
                <td style="font-size: 18px"><?php echo $bills->batch_no; ?></td>
                <td style="font-size: 18px"><?php echo $bills->hsn_code; ?></td>
                <td style="font-size: 18px" class="text-right"><span class="price"><?php echo $bills->discount; ?></span>&nbsp;&nbsp;<span class="tiny">INR</span></td>
                <td style="font-size: 18px" class="text-center"><?php echo $bills->quantity; ?></td>
                <td style="font-size: 18px" class="text-center"><?php echo round($bills->unit_price,2); ?></td>
                <td style="font-size: 18px" class="text-right"><span class="price"><?php echo $accountablePrice; ?></span></td>
                <td style="font-size: 18px" class="text-right"><span class="price"><?php echo $taxValue; ?></span></td>
                <td style="font-size: 18px" class="text-center"><?php echo $bills->cgst; ?><br><span class="price"><?php echo $cgst; ?></span>&nbsp;&nbsp;<span class="tiny">INR</span></td>
                <td style="font-size: 18px" class="text-center"><?php echo $bills->sgst; ?><br><span class="price"><?php echo $sgst; ?></span>&nbsp;&nbsp;<span class="tiny">INR</span></td>
                <td style="font-size: 18px" class="text-center" a=""><?php echo $bills->igst; ?><br><span class="price"><?php echo $igst; ?></span>&nbsp;&nbsp;<span class="tiny">INR</span></td>
              </tr>
                

					<?php } ?>           
                <tr>
                <td  colspan="12" style=" text-align: right;font-size: 20px">
                  Total Cost : <span><?php echo $grand_total; ?> INR</span> 
                </td>
              </tr>
              <tr>
                <td colspan="12" style=" text-align: right;font-size: 20px">
                  Amount Received : <span><?php echo $grand_total; ?> INR</span> 
                </td>
              </tr>
              <tr>
                <td colspan="12" style=" text-align: right;font-size: 20px">
                  Balance Amount : <span>0.00 INR</span> 
                </td>
              </tr>
              <tr>

						<td  colspan="5" style="padding: 10px; text-align: left;font-size: 20px">

							Mode of Payment : <span><?php echo $billing_master->payment_mode; ?></span>

						</td>
						<td colspan="7" style="padding: 10px; text-align: left">

						</td>

					</tr>

					
            </tbody>
          </table>
</body>	

</html>