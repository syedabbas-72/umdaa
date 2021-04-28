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
    <?php if ($pdf_settings->header != 1) { ?>
    <htmlpageheader name="firstpageheader">
        <table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
            <tr>
                <td style="width:40%">
                    <!-- <?php echo base_url(); ?>uploads/clinic_logos/<?php echo $clinic_logo . " "; ?>  -->
                    <img style="height:50px;" alt=""
                        src="<?php echo base_url(); ?>uploads/clinic_logos/<?php echo $clinic_information->clinic_logo . " "; ?>">
                </td>
                <td style="width: 25%"></td>
                <td style="width:35%;text-align: right;">
                    <span style="font-weight: bold; font-size: 14px">Address</span><br><span
                        style="font-weight: 700;font-size: 14px"><?php echo $clinic_information->address; ?></span><br><span
                        style=" font-weight: bold;font-size: 14px">Phone:</span><span
                        style="font-weight: 700; font-size: 14px"><?php echo $clinic_information->clinic_phone; ?></span>
                </td>
            </tr>
        </table>
        <hr>
    </htmlpageheader>
    <htmlpageheader name="otherheader">
        <table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
            <tr>
                <td style="width:40%">
                    <!-- <?php echo base_url(); ?>uploads/clinic_logos/<?php echo $clinic_logo . " "; ?>  -->
                    <img style="width:50%" alt=""
                        src="<?php echo base_url(); ?>uploads/clinic_logos/<?php echo $clinic_information->clinic_logo . " "; ?>">
                </td>
                <td style="width: 25%"></td>
                <td style="width:35%;text-align: right;">
                    <span style="font-weight: bold; font-size: 14px">Address</span><br><span
                        style="font-weight: 700;font-size: 14px"><?php echo $clinic_information->address; ?></span><br><span
                        style=" font-weight: bold;font-size: 14px">Phone:</span><span
                        style="font-weight: 700; font-size: 14px"><?php echo $clinic_information->clinic_phone; ?></span>
                </td>
            </tr>
        </table>
        <hr>
    </htmlpageheader>
    <?php } elseif ($pdf_settings->header == 1) {
		$style = "margin-bottom: " . $pdf_settings->header_height . "px !important;";
	?>
    <htmlpageheader name="firstpageheader">
        <table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;<?= $style ?>">
            <tr>
                <td>&nbsp;</td>
            </tr>
        </table>
        <hr>
    </htmlpageheader>
    <htmlpageheader name="otherheader">
        <table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;<?= $style ?>">
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
				if ($patientInfo->title) {
					$patientName = ucwords($patientInfo->title) . ". ";
				}
				$patientName .= $patientInfo->first_name . " " . $patientInfo->last_name;
				?>
                <span style="font-weight: bold;">Name:</span><?php echo strtoupper($billingInfo->guest_name); ?><br>
                <span style="font-weight: bold;">Age:</span>&nbsp;<?php echo $billingInfo->age != '0' ? $billingInfo->age . " " . $billingInfo->age_unit : ''; ?><?php echo ',' . $billingInfo->gender; ?><br>
                <!-- <b>Patient Name:</b>&nbsp;<?php echo $umr_no; ?><br> -->
                <?php
				$moreInfo = '';
				if ($patientInfo->gender) {
					$moreInfo .= ucwords(substr($patientInfo->gender, 0, 1));
				}
				if ($patientInfo->age) {
					if ($moreInfo) {
						$moreInfo .= ", ";
					}
					$moreInfo .= $patientInfo->age . " " . ucwords(substr($patientInfo->age_unit, 0, 1));
				}
				?>
                <p><?php echo $moreInfo; ?></p>
            </td>
            <td style="width:30%;text-align: center;vertical-align: top;font-size: 12px">
                <?php if ($patientInfo->address_line != '') { ?>
                <span style="font-weight: bold">Address:</span><br>
                <p><?php echo $patientInfo->address_line; ?></p>
                <?php } ?>
            </td>
            <td style="width: 40%;">
                <table cellspacing="0" cellpadding="0" style="width: 100%">
                    <tr>
                        <!-- <td style="text-align: right; padding-top:10px;font-size: 12px">Date : <span style="font-weight: 600; padding-top:10px;font-size: 12px"><?php echo $billing_date; ?></span></td> -->
                        <td style="text-align: right; padding-top:10px;font-size: 12px"><span style="font-weight:bold">Date :</span> 
                            <span style="font-weight: 600; padding-top:10px;font-size: 12px"><?php echo date('d/m/Y', strtotime($billingInfo->billing_date_time)); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right;font-size: 12px"><span style="font-weight:bold">Invoice Number :</span> <span
                                style="font-weight: 600;font-size: 12px"><?php echo $billingInfo->invoice_no_alias; ?></span>
                        </td>
                    </tr>
                </table>
            </td>
            <!-- <td style="width:30%;text-align: right;vertical-align: top;font-size: 12px">
                <div style="<?= ($pdf_settings->doc_details == 0) ? 'display: none' : '' ?>">
                    <span
                        style="font-weight: bold;font-size: 12px"><b><?php echo strtoupper($doctor_name); ?></b></span><br><span><?php echo strtoupper($doctorInfo->qualification . ", " . $department_name); ?>
                    </span><br>
                    <span><b>Reg. No:</b> &nbsp;<?php echo $doctorInfo->registration_code; ?> </span>
                </div>
            </td> -->
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
	foreach ($billingLineItemsInfo as $bill) {
		$discAmount = 0;
		$paid = 0;
		if ($bill->status == 2)
			continue;
		if ($bill->discount_unit == "INR") {
			$discAmount = $bill->discount;
		} elseif ($bill->discount_unit == "%") {
			$discAmount = (($bill->amount * $bill->discount) / 100);
		}
		$paid = $bill->amount - $discAmount;
		$total += $paid;
	}
	?>
    <table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px 10px">
        <tr>
            <td style="width: 60%; font-size: 12px ">
                <span style="line-height: 50px; font-size: 12px; font-weight:bold;">Payments</span><br>
                <span style="line-height: 20px; font-size: 12px">Received with thanks, amount of <span
                        style="font-weight: bold">Rs. <?= number_format($billingInvoiceInfo->invoice_amount, 2) ?>
                        /-</span> INR towards the following:</span>
            </td>
        </tr>
    </table>

    <hr>

    <table class="app-invoice" cellspacing="0" cellpadding="0" style="width: 100%;">
        <tr>
            <th style="font-size: 12px; background: #333; padding:10px; color:#fff; width: 5%; text-align:right">#</th>
            <th style="font-size: 12px; background: #333; padding:10px; color:#fff; width: 50%; text-align:left">
                Treatment & Product</th>
            <th style="font-size: 12px; background: #333; padding:10px; color:#fff; width: 10%; text-align: right;">Cost
            </th>
            <!-- <th style="font-size: 12px; background: #333; padding:10px; color:#fff; width: 15%; text-align: right;">Disc. Amt</th>
			<th style="font-size: 12px; background: #333; padding:10px; color:#fff; width: 15%; text-align: right;">Amt. Paid</th> -->
        </tr>
        <?php
		$i = 1;
		$grand1 = 0;
		$total = 0;
		foreach ($billingLineItemsInfo as $bills) {
			$discAmount = 0;
			$paid = 0;
			if ($billingInfo->discount == 0) {
				$discAmount = $billingInfo->discount;
			} elseif ($billingInfo->discount != 0) {
				// if($billingInfo->discount_unit == "INR")
				// {
				// $discAmount = $billingInfo->discount;
				// }
				// elseif($billingInfo->discount_unit == "%")
				// {
				$discAmount = (($bills->amount * $billingInfo->discount) / 100);
				// }
			}
			$paid = $bills->amount - $discAmount;
			$total += $bills->amount;
			$ptotal += $paid;
		?>
        <tr>
            <td style="font-size: 12px; width: 5%; padding:10px;text-align: right;"><?php echo $i++; ?></td>
            <td style="font-size: 12px; width: 75%; padding:10px;text-align: left;"><label
                    class="font-weight-bold"><?php echo $bills->item_information; ?></label></td>
            <?php
				$data = $this->db->select("*")->from("clinic_investigation_price")->where("investigation_id", $bills->investigation_id)->where("clinic_id", $clinic_id)->get()->row();
				?>
            <td style="font-size: 12px; text-align: right; width: 20%; padding:10px; border-bottom: 1px solid #ccc; border-right:1px solid #ccc;text-align: right;"
                class="text-right"><span><?php echo number_format($bills->amount, 2); ?></span></td>

            <!-- <td style="font-size: 12px; text-align: right; width: 15%; padding:10px; border-bottom: 1px solid #ccc; border-right:1px solid #ccc;text-align: right;" class="text-right"><span><?php echo number_format($bills->amount, 2); ?></span></td>
								<td style="font-size: 12px; text-align: right; width: 15%; padding:10px; border-bottom: 1px solid #ccc; border-right:1px solid #ccc;text-align: right;" class="text-right"><span><?php echo number_format($bills->amount, 2); ?></span></td> -->
        </tr>
        <?php } ?>
        <tr>
            <td colspan="5" style="text-align: right; padding: 10px;font-size: 12px">
                Total Amount : <span style="font-weight: 600;"><?php echo number_format($total, 2); ?></span>
            </td>
        </tr>

        <tr>
            <td colspan="5" style="text-align: right; padding: 10px;font-size: 12px">
                Total Savings :
                <?php echo number_format($total - $ptotal, 2); ?>
            </td>
        </tr>
        <!-- 

        <tr>
            <td colspan="5" style="text-align: right; padding: 10px;font-size: 12px">
                Billing Amount : <?php echo number_format($billingInfo->billing_amount, 2); ?>
            </td>
        </tr> -->


        <?php if ($billingInvoice->payment_type == 'Net') { ?>
        <tr>
            <td colspan="5" style="text-align: right; padding: 10px;font-size: 12px">
                Total Amount Paid : <?php echo number_format($billingInfo->billing_amount, 2); ?>
            </td>
        </tr>
        <?php } ?>

        <?php if (count($billingInvoice_records) == 1) { ?>
        <?php if ($billingInvoice_records[0]->payment_type == 'Advance') { ?>
        <tr>
            <td colspan="5" style="text-align: right; padding: 10px;font-size: 12px">
                Amount Paid : <?php echo number_format($billingInvoice_records[0]->invoice_amount, 2); ?>
            </td>
        </tr>
        <tr>
            <td colspan="5" style="text-align: right; padding: 10px;font-size: 12px">
                Balance Amount :
                <?php 
                if($billingInfo->osa == '0.00'){
                    echo "0.00";
                }
                else{
                    if(($ptotal - $billingInvoice_records[0]->invoice_amount) < 0) { echo "0.00"; } else { echo number_format($ptotal - $billingInvoice_records[0]->invoice_amount, 2); }
                }
                ?>
            </td>
        </tr>
        <?php } ?>
        <?php } ?>

        <?php if (count($billingInvoice_records) == 2) { ?>

        <?php if ($billingInvoice_records[0]->payment_type == 'Advance') { ?>
        <tr>
            <td colspan="5" style="text-align: right; padding: 10px;font-size: 12px">
                Amount Paid : <?php echo number_format($billingInvoice_records[0]->invoice_amount, 2); ?> on
                <?php echo date('d/m/Y', strtotime($billingInvoice_records[0]->invoice_date)); ?>
            </td>
        </tr>
        <tr>
            <td colspan="5" style="text-align: right; padding: 10px;font-size: 12px">
                Balance Amount :
                <?php 
                if($billingInfo->osa == '0.00'){
                    echo "0.00";
                }
                else{
                    if(($ptotal - $billingInvoice_records[0]->invoice_amount) < 0) { echo "0.00"; } else { echo number_format($ptotal - $billingInvoice_records[0]->invoice_amount, 2); }
                }
                ?>
            </td>
        </tr>
        <?php } ?>

        <?php if ($billingInvoice_records[1]->payment_type == 'OSA') { ?>
        <tr>
            <!-- <tr>
			<td colspan="5" style="text-align: right; padding: 10px;font-size: 12px">
				Advance Amount Paid : <?php echo number_format($billingInvoice_records[1]->invoice_amount, 2); ?>
			</td>
		</tr> -->
        <tr>
            <td colspan="5" style="text-align: right; padding: 10px;font-size: 12px">
                Balance Amount Paid : <?php echo number_format($billingInvoice_records[1]->invoice_amount, 2); ?> on
                <?php echo date('d/m/Y', strtotime($billingInvoice_records[1]->invoice_date)); ?>
            </td>
        </tr>
        </tr>

        <?php } ?>
        <?php } ?>
        <tr>
            <td colspan="5" style="padding: 10px; text-align: left; padding: 10px;font-size: 12px">
                Mode of Payment : <span
                    style="font-weight: 600;"><?php echo ucfirst($billingInvoice->payment_mode); ?></span>
            </td>
        </tr>
        <tr>
            <!-- <td colspan="5" style="padding: 10px; text-align: left; padding: 5px 10px;font-size: 12px">
				Valid for <?php echo $review_days; ?> Days
			</td> -->
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