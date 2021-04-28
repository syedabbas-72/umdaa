
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
	<?php if($pdf_settings->header == 1){  ?>
		<htmlpageheader name="firstpageheader">
			<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
				<tr>
					<td style="width:40%">
						<img style="width:50%" alt="" src="<?php echo base_url(); ?>uploads/clinic_logos/<?php echo $clinicInfo->clinic_logo; ?>">
					</td>
					<td style="width: 25%">
					</td>
					<td style="width:35%;text-align: right;">
						<span style="font-weight: bold;font-size: 14px">Address</span><br><span style="font-weight: 700;font-size: 14px"><?php echo $clinicInfo->address; ?></span><br><span style=" font-weight: bold;font-size: 14px">Phone:</span><span style="font-weight: 700;font-size: 14px"><?php echo $clinicInfo->clinic_phone; ?></span>

					</td>
				</tr>
			</table>
			<hr>
		</htmlpageheader>
		<?php } else { ?>	
			<htmlpageheader name="firstpageheader">
				<div style="height:<?php echo $pdf_settings->header_height; ?>px;border:none"></div>
				<hr>
			</htmlpageheader>
		<?php } ?>		
		<h4 style="text-align: center;font-weight: bold;font-size: 14px">Pharmacy Finances From <?=$fromString?></h4><br>
		<table class="table" border="1">
			<tr>
				<td style="padding:10px;width: 33%;text-align: center"><h5 style="font-weight: bold;font-size:12px;">TOTAL AMOUNT<br>RS. <?=number_format($total+$discTotal, 2)?> /-</h5></td>
				<td style="padding:10px;width: 33%;text-align: center"><h5 style="font-weight: bold;font-size:12px;">DISCOUNT AMOUNT<br>RS. <?=number_format($discTotal,2)?> /-</h5></td>
				<td style="padding:10px;width: 34%;text-align: center"><h5 style="font-weight: bold;font-size:12px;">AMOUNT (AFTER DISCOUNT)<br>RS. <?=number_format($total,2)?> /-</h5></td>
			</tr>
		</table>
		<table style="width: 100%;" cellpadding="0" cellspacing="0" border="1" class="table table-striped dt-responsive customTable" >
            <thead>
                <tr>
	                <th style="font-size: 11px">#</th>
	                <th style="font-size: 11px">Date</th>
	                <th style="font-size: 11px">Invoice No.</th>
	                <th style="font-size: 11px">Name</th>
	                <th style="font-size: 11px">Products</th>
	                <th style="font-size: 11px">Cost</th>
	                <th style="font-size: 11px">Disc. Amt</th>
	                <!-- <th style="width: 10%">Tax</th> -->
	                <th style="font-size: 11px">Amt. Paid</th>
                </tr>
            </thead>
            <tbody>
            	<?php
            	$i = 1;
            	foreach ($billings as $value) { 
            		$billingLineItems = $this->db->query("select bl.*,d.trade_name,d.formulation from drug d,billing_line_items bl where bl.drug_id=d.drug_id and bl.billing_id='".$value->billing_id."'")->result();
				  	if($value->status==2)
				  		continue;
			  		$discAmount = $value->tamount-$value->bamount;
			  		$discTotal = $discTotal+$discAmount;
				  	$total = $total + $value->bamount;
					  ?> 
					  <tr>
					    <td style="font-size: 11px"><?php echo $i;?></td>
					    <td style="font-size: 11px"><?php echo date("d-m-Y",strtotime($value->billing_date_time));?></td>
					    <td style="font-size: 11px"><?php echo $value->invoice_no;?></td>
					    <td style="font-size: 11px">
					    	<?php
					    	if($value->patient_id!="")
					    	{
					    		if($value->title == "")
					    		{
					    			$pname = ucwords(strtolower($value->pname." ".$value->lname));
					    		}
					    		else
					    		{
					    			$pname = ucwords(strtolower($value->title.". ".$value->pname." ".$value->lname));
					    		}
					    		echo '<span style="font-weight:bold">'.$pname.'</span><span class="formulation">'.$value->umr_no.'</span><br>'.DataCrypt($value->pmob,'decrypt');
					    	}
					    	else
					    	{
					    		echo '<span style="font-weight:bold">'.ucwords(strtolower($value->guest_name)).'</span><br>'.$value->guest_mobile;
					    	}
					    	?>
					   	</td> 
					    <!-- <td>&nbsp;</td> -->
					    <td style="font-size: 11px">
				    	<?php
				    	foreach ($billingLineItems as $bli) 
				    	{
				    		echo strtoupper($bli->formulation." ".$bli->trade_name)."<br>";
				    	}
				    	?>
					    </td>
					    <td style="font-size: 11px" class="text-right"><span><?php echo number_format($value->bamount+$discAmount,2); ?></span></td> 
					    <td style="font-size: 11px" class="text-right"><span><?php echo number_format($discAmount,2); ?></span></td> 
					    <!-- <td class="text-right"><span><?php echo number_format($taxValue,2); ?></span></td>  -->
					    <td style="font-size: 11px" class="text-right"><span><?php echo number_format($value->bamount,2); ?></span></td> 
					  </tr>
					<?php
					$i++;
			 }
			?>
            </tbody>
          </table>
		<htmlpagefooter name="footer">
			<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
				<tr>
					<td style="font-size:12px; text-align: left;">
						Powered by umdaa.co 
					</td>
					<td style="font-size:12px; text-align: right;">
						<b>Date: </b><?php echo date("d M Y h:i A"); ?>
					</td>
				</tr>
			</table>
		</htmlpagefooter>

	</body>	
</html>