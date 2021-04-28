
		<!-- Payment Info Header -->
		<?php
		$total = 0;
		foreach($billingLineItemsInfo as $bill)
		{
			$discAmount = 0;
			$paid = 0;
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
		<div class="row mt-3 border-bottom">
			<div class="col-md-8 col-12 mb-2">
				<label class="font-weight-bold">Payments Info</label>
				<p class="p-0 mb-0">Received With Thanks, Amount Of <label class="font-weight-bold">Rs. <?=number_format($billingInvoiceInfo->invoice_amount,2)?> /-</label> Towards The Following:</p>
			</div>
			<div class="col-md-4 col-12 mb-2">
				<p class="p-0 mb-0 text-right"><label class="font-weight-bold">Date : </label> <?=date('d-M-Y', strtotime($billingInfo->created_date_time))?></p>
				<p class="p-0 mb-0 text-right"><label class="font-weight-bold">Invoice No. : </label> <?=$billingInfo->invoice_no_alias?></p>
			</div>
		</div>
		<!-- Product Info -->
		<div class="row mt-3 ">
			<div class="col-md-12">
				<table class="table customTable">
					<thead>
						<th>#</th>
						<th>Treatment & Product</th>
						<th>Disc. Amt</th>
						<th>Cost</th>
					</thead>
					<tbody>
					<?php
					$i=1;
					$grand1=0;
					$total = 0;
					foreach($billingLineItemsInfo as $bills) {
						$discAmount = 0;
						$paid = 0;
						if($billingInfo->discount==0)
						{
							$discAmount = $billingInfo->discount;
						}
						elseif($billingInfo->discount!=0)
						{
							// if($billingInfo->discount_unit == "INR")
							// {
							// $discAmount = $billingInfo->discount;
							// }
							// elseif($billingInfo->discount_unit == "%")
							// {
							$discAmount = (($bills->amount*$billingInfo->discount)/100);
							// }
						}
						$paid = $bills->amount-$discAmount;
						$total += $bills->amount;
						$ptotal += $paid;
							?>
							<tr>
								<td><?php echo $i++; ?></td>
								<td><label class="font-weight-bold"><?php echo $bills->item_information; ?></label></td>
								<td class="text-right"><span><?php echo number_format($discAmount,2); ?></span></td>
								<td class="text-right"><span><?php echo number_format($bills->amount,2); ?></span></td>
							</tr>
						<?php } ?>
						<tr>
							<td colspan="5" class="text-right">
								<label class="font-weight-bold">Amount Paid : <?php echo number_format($billingInvoiceInfo->invoice_amount,2); ?></label>
							</td>
						</tr>
						<tr>
							<td colspan="5" class="text-right">
								<label class="font-weight-bold">Total Savings : 
								<?php 
								if($billingInvoiceInfo->payment_type == "Advance" || $billingInvoiceInfo->payment_type == "Net")
								{
									echo $savngs = number_format($total-$ptotal,2);
								}
								else
								{
									echo $savngs = "0.00";
								}
								?></label>
							</td>
						</tr>
						<?php
						if($billingInvoiceInfo->payment_type == "Advance")
						{
							?>
							<tr>
								<td colspan="5" class="text-right">
									<label class="font-weight-bold">Amount Due : <?php echo number_format($ptotal-$billingInvoiceInfo->invoice_amount,2); ?></label>
								</td>
							</tr>
							<?php
						}
						?>
						<tr>
							<td colspan="5">
								<label class="font-weight-bold">Mode of Payment : <?php echo ucfirst($billingInvoiceInfo->payment_mode); ?></label>
							</td>
						</tr>
						<?php
						if(!empty($billingInvoiceInfo->transaction_id))
						{
							?>
							<tr>
								<td colspan="5">
									<label class="font-weight-bold">Transaction ID : <?=$billingInvoiceInfo->transaction_id?></label>
								</td>
							</tr>
							<?php
						}
						if(isset($clinicDocInfo->review_days))
						{
							?>
							<tr>
								<td colspan="5">
									<label class="font-weight-bold">Valid for <?php echo $clinicDocInfo->review_days; ?> Days</label>
								</td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
				<?php
				// if(isset($patientInfo->referred_by))
				// {
				// 	if($patientInfo->referred_by_type=="Doctor")
				// 	{
				// 		$referred_by = getreferalDoctorname($patientInfo->referred_by);
				// 	}
				// 	else
				// 	{
				// 		$referred_by = $patientInfo->referred_by;
				// 	}
				if($billingInvoiceInfo->referral_doctor_id !='0')
				{
					$id =$billingInvoiceInfo->referral_doctor_id;
					$get = $this->db->select("*")->from('referral_doctors')->where("rfd_id",$id)->get()->row();
					$referred_by = $get->doctor_name;
					?>
					<label class="font-weight-bold pl-2">Referred By : Dr. <?php echo $referred_by?></label>
					<?php
				}
					?>
					
					<?php
				// }
				?>
			</div>
		</div>
		
<!-- Footer Info -->
<div class="row mt-5">
    <div class="col-md-6">
        <label class="font-weight-bold">POWERED BY UMDAA</label>
    </div>
    <div class="col-md-6 text-right">
        <label class="font-weight-bold"><?=date("d-m-Y h:i A", strtotime($billingInfo->created_date_time))?></label>
    </div>
</div>

