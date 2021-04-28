
<!DOCTYPE html>
<html lang="en">
<head>
	<!-- Meta information -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="author" content="">

	<link href="<?= base_url() ?>assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?= base_url() ?>assets/css/pdf.css" rel="stylesheet">

	<!-- <script src="<?php echo base_url(); ?>assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script type="text/javascript">
		$(document).ready(function(){
			var type = $('.Submit_type').val();
			if(type == "SP")
			{
				window.open('<?php echo base_url(uri_string()); ?>','_blank');
			}
		});
	</script> -->

</head>
<body class="vitals">
	<!-- <input type="hidden" value="<?=$type?>" class="Submit_type"> -->
	<!-- <p><?=$type?></p> -->
		<htmlpageheader name="firstpageheader">
			<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
				<tr>

					<td style="width:40%">
						<img src="<?php echo base_url(); ?>assets/img/umdaaLogo.jpg">
					</td>
					<td style="width: 25%">
					</td>
					<td style="width:35%;text-align: right;">
						<span style="font-weight: bold;font-size: 12px">Address</span><br><span style="font-weight: 700;font-size: 12px">#8-1-21/146, TOLICHOWKI, HYDERABAD, TELANGANA - 500008</span><br><span style=" font-weight: bold;font-size: 12px">Phone:</span><span style="font-weight: 700;font-size: 12px">+91 - 9100 462 015</span>

					</td>
				</tr>
			</table>
			<hr>
		</htmlpageheader>	
		<?php
        $dateObj   = DateTime::createFromFormat('!m', $invoice_master->month);

		?>
		<p class="text-right" style="font-weight: bold;font-size: 12px">Date : <?=date("d M Y h:i A",strtotime($invoice_master->created_date_time))?><br>Invoice No : <?=$invoice_master->invoice_no?></p>
		<p style="font-weight: bold;font-size: 12px">To,</p>
		<p style="text-transform: uppercase;font-size: 12px"><span style="font-weight: bold;">Dr. <?=$doctorInfo->first_name." ".$doctorInfo->last_name?>,</span><br>
			<?=strtoupper($doctorInfo->qualification)?> - <?=$doctorInfo->department_name?><br><?=ucwords($doctorInfo->address)?></p>
		<p style="font-size: 12px;font-weight: bold;">Total Invoices : <?=$invoice_master->totalInvoices?></p>
		<p style="font-size: 12px;font-weight: bold;color: red">*First 100 Invoices Are Free.</p>	
		<h4 style="text-align: center;font-weight: bold;font-size: 12px;text-transform: uppercase;">INVOICE OF <?=$dateObj->format('F')." - ".$invoice_master->year?></h4>
		<table style="width: 100%;" cellpadding="0" cellspacing="0" class="table table-bordered dt-responsive customTable" >
            <thead>
                <tr>
	                <th style="font-size: 12px">#</th>
					<th style="font-size: 12px">ITEM INFO</th>
					<th style="font-size: 12px">NO. OF INVOICES</th>
					<th style="font-size: 12px">UNIT PRICE</th>
					<th style="font-size: 12px">TOTAL COST</th>
                </tr>
            </thead>
            <tbody>
            	<?php
            	$i = 1;
            	foreach ($invoice_line_items as $value) { 
        			  $btype = strtolower(explode(" ", $value->item_information)[0]);
        			  $subtotal = $value->quantity*$value->per_unit_price;
					  ?> 
					  <tr>
					    <td style="font-size: 12px"><?=$i?></td>
					    <td style="font-size: 12px"><?=$value->item_information?></td>
					    <td style="font-size: 12px"><?=$value->quantity?></td>
					    <td style="font-size: 12px"><?=$value->per_unit_price?></td>
					    <td style="font-size: 12px;text-align: right"><?=number_format($subtotal,2)?></td>
					  </tr>
					<?php
					$i++;
			 }
			?>
			<tr>
				<td colspan="4" style="text-align: right"><span style="font-size:12px;font-weight: bold;text-align: right">GRAND TOTAL</span></td>
				<td style="text-align: right">
					<?php
					if($invoice_master->totalInvoices<=100)
					{
						?>
						<span style="font-size:12px;font-weight: bold;color: green">FREE</span>
						<?php
					 
					}
					else
					{
						$total = $invoice_master->amount-100;
						?>
						<span style="font-size:12px;font-weight: bold;"><?=number_format($total,2)?></span>
						<?php
					}
					?>
					</td>
			</tr>
            </tbody>
          </table>
		<div style="width: 100%;text-align: right">
			<p style="font-weight: bold;font-size: 12px">From</p>
			<p style="font-weight: bold;text-transform: uppercase;font-size: 12px"><span style="font-weight: bold">UMDAA Health Care</span><br><span style="font-weight: normal;">#8-1-21/146, TOLICHOWKI,<br>HYDERABAD, TELANGANA - 500008.<br>+91 - 9100 462 015</span></p>
		</div>
		<htmlpagefooter name="footer">
			<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
				<tr>
					<td style="font-size:12px; text-align: left;">
						Powered by umdaa.co 
					</td>
					<td style="font-size:12px; text-align: right;">
						<b>Date: </b><?php echo date("d M Y h:i A",strtotime($invoice_master->created_date_time)); ?>
					</td>
				</tr>
			</table>
		</htmlpagefooter>

	</body>	
</html>