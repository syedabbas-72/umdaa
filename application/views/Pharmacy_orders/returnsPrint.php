
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
					<img style="width:50%" alt="" src="<?php echo base_url($logo); ?>">
				</td>
				<td style="width: 25%"></td>
				<td style="width:35%;text-align: right;">
                    <span style="font-weight: bold; font-size: 12px"><?=$clinicname?></span><br>
					<span style="font-weight: bold; font-size: 12px">Address</span><br><span style="font-weight: 700;font-size: 12px"><?php echo $address; ?></span><br><span style=" font-weight: bold;font-size: 12px">Phone:</span><span style="font-weight: 700; font-size: 12px"><?php echo $contact; ?></span>
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
					<img style="width:50%" alt="" src="<?php echo base_url($logo); ?>">
				</td>
				<td style="width: 25%"></td>
				<td style="width:35%;text-align: right;">
                    <span style="font-weight: bold; font-size: 12px"><?=$clinicname?></span><br>
					<span style="font-weight: bold; font-size: 12px">Address</span><br><span style="font-weight: 700;font-size: 12px"><?php echo $address; ?></span><br><span style=" font-weight: bold;font-size: 12px">Phone:</span><span style="font-weight: 700; font-size: 12px"><?php echo $contact; ?></span>
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
				if($billing->appointment_id==0)
				{
					if($billing->patient_id!="")
					{
						?>
						<table cellpadding="0" cellspacing="0" style="margin: 0px;font-size: 12px; padding: 0px; width: 100%;">
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
						<table cellpadding="0" cellspacing="0" style="margin: 0px;font-size: 12px; padding: 0px; width: 100%;">
							<tr>
								<td style="width:50%">
									<span style="font-weight: bold;"><b><?php echo strtoupper($patient_name); ?></b></span><br>
									<?php echo $billing->guest_mobile; ?><br>
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
					<table cellpadding="0" cellspacing="0" style="margin: 0px;font-size: 12px; padding: 0px; width: 100%;">
						<tr>
							<td style="width:30%">
								<span style="font-weight: bold;font-size: 12px"><b><?php echo strtoupper($patient_name); ?></b></span><br>
								<span style="font-weight:bold;font-size: 12px">UMR:</span>&nbsp;<?php echo $pinfo->umr_no; ?><br>
								<?php 
								$moreInfo = '';
								if($gender) { 
									$moreInfo .= ucwords(substr($pinfo->gender, 0, 1));
								} 
								if($age) { 
									if($moreInfo){
										$moreInfo .= ", ";
									}
									$moreInfo .= $age." ".ucwords(substr($pinfo->age_unit, 0, 1));
								} 							
								?>
								<p><?php echo $moreInfo; ?></p>
							</td>
							<td style="width:30%;text-align: center;vertical-align: top">
								<?php if($paddress != ''){ ?>
									<span style="font-weight: bold">Address:</span><br><p><?php echo $paddress; ?></p>
								<?php } ?>
							</td>
							<td style="width:30%;text-align: right;vertical-align: top;font-size: 12px">
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

        $totalAmt = 0;
		foreach($pharmacy_returns as $pr)
        {
            $disc = 0;
            $price = 0;
            $drugInfo = $this->db->query("select unit_price,discount,discount_unit from billing_line_items where billing_line_item_id='".$pr->billing_line_item_id."'")->row();
            $billItems = $this->Generic_model->getSingleRecord("billing_line_items", array('billing_line_item_id'=>$pr->billing_line_item_id));
            
            $price = $drugInfo->unit_price * $pr->return_qty;
            if($drugInfo->discount_unit == "%"){
                $disc = $price - (($price * $drugInfo->discount)/100);
            }
            elseif($drugInfo->discount_unit == "INR"){
                $disc = $price-$drugInfo->discount;
            }
            $totalAmt += $disc;

		}

		?>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">

					<tr>

						<td style="font-size: 12px;">

							<span style=" font-weight: bold">Payments</span><br>

							Amount of <span style="font-weight: bold;">Rs.<?php echo number_format($totalAmt,2);?>/-</span> INR returning to customer towards the following : 

						</td>

						<td >

							<table cellspacing="0" cellpadding="0" style="width: 100%">

								<tr>

									<td style="text-align: right;font-size: 12px;"><span style="font-weight:bold">Date : </span><span style="font-weight: 600 !important;"><?php echo date('d/m/Y'); ?></span></td>

								</tr>


								

								<tr>

									<td style="text-align: right;font-size: 12px;"><span style="font-weight:bold">Invoice Number : </span><span style="font-weight: 600 !important;"><?php echo $billing->invoice_no; ?></span></td>

								</tr>

							</table>

						</td>

					</tr>

				</table>
<hr>
<table style="border-collapse: collapse;width:100%">
            <thead style="background: #f6f6f6;">
              <tr style="font-size: 18px">
                <th>S#</th>
                <th  style="font-size: 12px;">Drug Desc.</th>
                <th  style="font-size: 12px">MRP / Unit</th>
                <th style="font-size: 12px">Taken Qty</th>  
                <th style="font-size: 12px">Return Qty</th>  
                <th style="font-size: 12px">Amount In-GST</th>      
              </tr>
            </thead>
            <tbody>

				<?php

				

				$i=1;
                $totalAmt = 0;


				foreach($pharmacy_returns as $pr)
				{
                    $disc = 0;
                    $price = 0;
                    $drugInfo = $this->db->query("select unit_price,discount,discount_unit from billing_line_items where billing_line_item_id='".$pr->billing_line_item_id."'")->row();
                    $billItems = $this->Generic_model->getSingleRecord("billing_line_items", array('billing_line_item_id'=>$pr->billing_line_item_id));
                    
                    $price = $drugInfo->unit_price * $pr->return_qty;
                    if($drugInfo->discount_unit == "%"){
                        $disc = $price - (($price * $drugInfo->discount)/100);
                    }
                    elseif($drugInfo->discount_unit == "INR"){
                        $disc = $price-$drugInfo->discount;
                    }
                    $totalAmt += $disc;
                    $drgs = $this->Generic_model->getSingleRecord("drug", array('drug_id'=>$pr->drug_id));
                    ?>
                    <tr>
                        <td  style="font-size: 12px"><?php echo $i++; ?></td>
                        <td style="font-size: 12px"><span style="font-weight:bold"><?php echo $drgs->trade_name; ?></span><br>BATCH#: <?=$pr->batch_no?></td>
                        <td style="font-size: 12px"><?php echo number_format($billItems->unit_price,2); ?></td>
                        <td style="font-size: 12px"><?php echo $pr->total_qty; ?></td>
                        <td style="font-size: 12px"><?php echo $pr->return_qty; ?></td>
                        <td style="font-size: 12px" class="text-right"><?php echo number_format($price,2); ?></td>
                    </tr>
                    <?php 
                } 
                ?>           
                 <tr>
                <td  colspan="6" style=" text-align: right;font-size: 12px;font-weight: bold;">
                  Amount Returned to Customer : <span><?php echo number_format($totalAmt,2); ?> INR</span> 
                </td>
              </tr>
					<?php
							if($billing->transaction_id!="")
							{
								?>
						<tr>
						<td colspan="12" style="padding: 10px; text-align: left;font-size: 12px;font-weight: bold;">
							
								Transaction ID : <span><?php echo ucwords($billing->transaction_id); ?></span>
								
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
						<td style="font-size:12px; text-align: left;font-weight: bold">
							Powered by umdaa.co 
						</td>
					</tr>
				</table>
			</htmlpagefooter>
</body>	

</html>