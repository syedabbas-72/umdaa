
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
		<h4 style="text-align: center;font-weight: bold;font-size: 14px">INVENTORY LIST</h4>
		<table id="inventoryList" class="table table-bordered dt-responsive customTable" style="font-size: 12px" border="1">
			<thead class="thdClr">
				<tr>
					<th style="font-size: 12px">S#</th>
					<th style="font-size: 12px">Trade Name (Formulation)<br>Composition</th>
					<th style="font-size: 12px">Batch#<br> EXP.</th>
					<th style="font-size: 12px">Ava. Qty</th>
					<th style="font-size: 12px">MRP - PACK SIZE</th>
					<th style="font-size: 12px">HSN CODE</th>	
					<th style="font-size: 12px">IGST</th>
					<th style="font-size: 12px">CGST</th>
					<th style="font-size: 12px">SGST</th>
					<th style="font-size: 12px">Max. Disc</th>
					<th style="font-size: 12px">RL</th>
				</tr>
			</thead>
			<tbody>
				   <?php $i=1; 
				 if(count($parinfo)>0)
				 {
					foreach ($parinfo as $value) { ?> 
						<tr style="font-size: 12px">
							<td><?php echo $i;?></td>
							<td><?php echo '<span style="font-weight:bold">'.$value['trade_name'].'</span> <span class="formulation">'.strtoupper($value['formulation']).'</span><br>'.$value['composition'];?></td>
							<td><?php echo '<span style="font-weight:600">'.strtoupper($value['batch_no']).'</span><br>'.date("M. 'y",strtotime($value['expiry_date']));?></td>
							<td><?php echo $value['oqty']; ?></td>
							<td><?php echo '<span style="font-weight:600">'.round($value['mrp'],2).'</span> - '.$value['pack_size']; ?></td>
							<td><?php echo $value['hsn_code']; ?></td>
							<td><?php echo $value['igst']; ?></td>
							<td><?php echo $value['cgst']; ?></td>
							<td><?php echo $value['sgst']; ?></td>
							<td><?php echo $value['disc']; ?></td> 
							<td><?php echo $value['reorder_level']; ?></td>
						</tr>
						  <?php $i++;}
				 }  
				 else{
					?>
					<tr>
						<td style="text-align:center;" colspan="11"><label style="font-weight:bold;text-transform:uppercase">Empty Inventory</label></td>
					</tr>
					<?php
				 }
				 
				  ?>
			</tbody>
		</table>
		<htmlpagefooter name="footer">
			<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
				<tr>
					<td style="font-size:14px; text-align: left;">
						Powered by umdaa.co 
					</td>
					<td style="font-size:14px; text-align: right;">
						<b>Date: </b><?php echo date("d M Y h:i A", strtotime($appointments->created_date_time)); ?>
					</td>
				</tr>
			</table>
		</htmlpagefooter>

	</body>	
</html>