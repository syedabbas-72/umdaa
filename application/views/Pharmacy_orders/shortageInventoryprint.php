<?php
	$clinic_id = $this->session->userdata('clinic_id');
?>
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
		<h4 style="text-align: center;font-weight: bold;font-size: 14px">SHORTAGE LIST</h4>
		<table class="table " border="1" style="width: 100%;font-size: 12px;" >
			<thead>
				<tr>
					<th style="width: 5%;font-size: 12px;">S#</th>
					<th style="font-size: 12px;">Drug Name</th>	
					<th style="font-size: 12px;">Batch No</th>
					<th style="font-size: 12px;">Quantity</th>
					<th style="font-size: 12px;">Status</th>
					<th style="font-size: 12px;">Vendor</th>															
				</tr>
			</thead>
			<tbody>
			<?php $ssno=1;
			if(count($shortage)>0)
			{
				foreach($shortage as $result){ 
					$indentInfo = $this->db->query("select * from pharmacy_indent_line_items pl,pharmacy_indent p where p.pharmacy_indent_id=pl.pharmacy_indent_id and p.clinic_id='".$clinic_id."' and pl.drug_id='".$result['drug_id']."' and pl.status='1'")->row();
					$vendor_list = $this->db->query("select * from vendor_master where vendor_id='".$result['vendor_id']."'")->row();
					$status = '';
					if(sizeof($indentInfo) != 0)
					{
						$status = "Indent Raised";
					}
					$c_date = date("Y-m-d");
					?>
				<tr>
					<td style="font-size: 12px;"><?php echo $ssno; ?></td>
					<td style="font-weight: bold;font-size: 12px;"><?php echo $result['drug_name']; ?></td>
					<td style="font-size: 12px;"><?php echo strtoupper($result['batch_no']); ?></td>
					<td style="font-size: 12px;"><?=($c_date>=$result['edate'])?'0':$result['quantity']?></td>
					<td style="font-size: 12px;"><?=@$status?></td>
					<td style="font-size: 12px;"><?=$vendor_list->vendor_storeName.", ".$vendor_list->vendor_location?></td>
				</tr>
				<?php $ssno++;}
			}
			else
			{
				?>
				<tr>
					<td colspan="6" style="text-align:center;"><label style="font-weight:bold;text-transform:uppercase">No Shortage Drugs</label></td>
				</tr>
				<?php
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
						<b>Date: </b><?php echo date("d M Y h:i A", strtotime($appointments->created_date_time)); ?>
					</td>
				</tr>
			</table>
		</htmlpagefooter>

	</body>	
</html>