
		<!-- Payment Info Header -->
		<?php
		$b=1;
		$total=0;
		foreach($billingLineItemsInfo as $bills){				
			$price = number_format(($bills->quantity * $bills->unit_price),2);
			
			// Accountable price if any discounts applying
            $accountablePrice =  number_format($price - ($price * ($bills->discount / 100)),2);
           
            // Taxation
            // Value inclding GST = mrp ($accountablePrice)
            // TaxValue = (mrp * 100)/(100 + CGST + SGST)
            $taxValue = number_format(($accountablePrice * 100)/(100 + $bills->cgst + $bills->sgst + $bills->igst),2);

            $cgst = round($taxValue * ($bills->cgst / 100),2);
            $sgst = round($taxValue * ($bills->sgst / 100),2);
            $igst = round($taxValue * ($bills->igst / 100),2);

            $total = $total + $accountablePrice;

		}
		?>
		<div class="row mt-3 border-bottom">
			<div class="col-md-8 col-12 mb-2">
				<label class="font-weight-bold">Payments Info</label>
				<p class="p-0 mb-0">Received With Thanks, Amount Of <label class="font-weight-bold">Rs. <?=number_format($total,2)?> /-</label> Towards The Following:</p>
			</div>
			<div class="col-md-4 col-12 mb-2">
				<p class="p-0 mb-0 text-right"><label class="font-weight-bold">Date : </label> <?=date('d-M-Y', strtotime($billingInfo->created_date_time))?></p>
				<p class="p-0 mb-0 text-right"><label class="font-weight-bold">Invoice No. : </label> <?=$billingInfo->invoice_no?></p>
			</div>
		</div>
		<!-- Product Info -->
		<div class="row mt-3 ">
			<div class="col-md-12">
            <table class="table-bordered table app-invoice" style="border-collapse: collapse;">
            <thead style="background: #f6f6f6;">
              <tr>
                <th>S#</th>
                <th>Drug Desc.</th>
                <th>Batch#</th>
                <th>HSN Code</th>
                <th class="text-right">MRP / Unit</th>
                <th class="text-center">Qty</th>                
                <th class="text-center">Disc(%)</th>            
                <th class="text-right">TaxValue</th>
                <th class="text-center">CGST %<br>Amt.</th>
                <th class="text-center">SGST %<br>Amt</th>
                <th class="text-center">IGST %<br>Amt.</th>
                <th class="text-right">Value<br>In-GST</th>    
              </tr>
            </thead>
            <tbody>

				<?php

				

				$i=1;

				$grand_total=0;
				$total_price=0;
				$total_discount=0;

				foreach($billingLineItemsInfo as $bills)

				{

				
				$price = number_format(($bills->quantity * $bills->unit_price),2);
				
				// Accountable price if any discounts applying
	            $accountablePrice =  number_format($price - ($price * ($bills->discount / 100)),2);
	           	$total_price = number_format(($total_price + $price),2);
           		$total_discount = number_format((float)$total_discount + ($price * ($bills->discount / 100)), 2, '.', '');

	            // Taxation
	            // Value inclding GST = mrp ($accountablePrice)
	            // TaxValue = (mrp * 100)/(100 + CGST + SGST)
	            $taxValue = number_format(($accountablePrice * 100)/(100 + $bills->cgst + $bills->sgst + $bills->igst),2);

	            $cgst = round($taxValue * ($bills->cgst / 100),2);
	            $sgst = round($taxValue * ($bills->sgst / 100),2);
	            $igst = round($taxValue * ($bills->igst / 100),2);

	            $grand_total = $grand_total + $accountablePrice;

				?>
                    <tr>
                        <td  class="text-center"><?php echo $i++; ?></td>
                        <td ><label class="font-weight-bold text-uppercase"><?php echo $bills->item_information; ?></label></td>
                        <td ><?php echo $bills->batch_no; ?></td>
                        <td ><?php echo $bills->hsn_code; ?></td>
                        <td  class="text-center"><?php echo number_format($bills->unit_price,2); ?></td>
                        <td  class="text-center"><?php echo $bills->quantity; ?></td>
                        <td  class="text-right"><span class="price"><?php echo $bills->discount; ?></span></td>
                        <td  class="text-right"><span class="price"><?php echo $taxValue; ?></span></td>
                        <td  class="text-center"><?php echo $bills->cgst; ?><br><span class="price"><?php echo $cgst; ?></span>&nbsp;&nbsp;<span class="tiny">INR</span></td>
                        <td  class="text-center"><?php echo $bills->sgst; ?><br><span class="price"><?php echo $sgst; ?></span>&nbsp;&nbsp;<span class="tiny">INR</span></td>
                        <td  class="text-center" a=""><?php echo $bills->igst; ?><br><span class="price"><?php echo $igst; ?></span>&nbsp;&nbsp;<span class="tiny">INR</span></td>
                        <td  class="text-right"><span class="price"><?php echo $accountablePrice; ?></span></td>
                    </tr>
                <?php } ?>           
                <tr>
                    <td  colspan="12" class=" text-right">
                    <label class="font-weight-bold">Total Cost : <span><?php echo number_format($total_price,2); ?> INR</span> </label>
                    </td>
                </tr>
                <tr>
                    <td  colspan="12" class=" text-right">
                    <label class="font-weight-bold">Total Savings : <span><?php echo number_format($total_discount,2); ?> INR</span> </label>
                    </td>
                </tr>
                <tr>
                    <td  colspan="12" class=" text-right">
                    <label class="font-weight-bold">Total Amount To Be  Payable : <span><?php echo number_format($grand_total,2); ?> INR</span> </label>
                    </td>
                </tr>
                <tr>
                    <td  colspan="12">
                        <label class="font-weight-bold">Mode of Payment : <span><?php echo ucwords($billing_master->payment_mode); ?></span></label>
                    </td>
                </tr>
					<?php
							if($billing_master->transaction_id!="")
							{
								?>
						<tr>
                            <td colspan="12">
                                    <label class="font-weight-bold">Transaction ID : <span><?php echo ucwords($billing_master->transaction_id); ?></span></label>
                            </td>
                        </tr>
<?php
							}
							?>
					
            </tbody>
          </table>
				<?php
				if(isset($patientInfo->referred_by))
				{
					if($patientInfo->referred_by_type=="Doctor")
					{
						$referred_by = getreferalDoctorname($patientInfo->referred_by);
					}
					else
					{
						$referred_by = $patientInfo->referred_by;
					}
					?>
					<label class="font-weight-bold pl-2">Referred By : Dr. <?=$referred_by?></label>
					<?php
				}
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