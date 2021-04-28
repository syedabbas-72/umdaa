
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

<body>
	
                    <h4 class="text-center"><strong><?php echo strtoupper($_SESSION['clinic_name']); ?></strong></h4>
                    <p class="text-center" style="font-size:12px"><?php echo $report_heading; ?></p>
                
            
<hr>
	
                    <span style="font-weight: bold;">Summary</span>
               
             <table id="doctorlist" style="margin-top: 15px">
                <tbody>
    <tr>
    	<?php
    	$total_cost = 0;$total_discount=0;$total_after_discount;$invoice_total=0;
    	 foreach ($billing as $value) { 

    	$billing_line_items = $this->db->query("select * from billing_line_items where billing_id=".$value->billing_id)->result();
    	  $total=0;$atotal=0;$dtotal=0;

              foreach($billing_line_items as $bills) {
                $price = round($bills->quantity * $bills->unit_price,2);
                
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
				$atotal = $atotal+$price;
                $dtotal = $dtotal+($price * ($bills->discount / 100));
                
            }
            $total_cost += $atotal;
            $total_discount += $dtotal;
            $total_after_discount += $total;
            $invoice_total += $total;
        }
    ?> 
        <td style="width:15%;font-size:13px" class="text-center">Cost(INR)<br><b><?php echo $total_cost; ?></b></td>
        <td style="width:15%;font-size:13px">Disc(INR)<br><b><?php echo $total_discount; ?></b></td>
        <td style="width:25%;font-size:13px">Income after discount(INR)<br><b><?php echo $total_after_discount; ?></b></td>
        <td style="width:15%;font-size:13px">Tax(INR)<br><b>0.00</b></td>
        <td style="width:20%;font-size:13px">Invoice Amount(INR)<br><b><?php echo $invoice_total; ?></b></td>
    </tr>
</tbody>
</table>
            <hr>

                    <span style="font-weight: bold;">Details</span>
              
            
				 <table id="doctorlist"  class="table table-bordered" style="margin-top: 10px">
                <thead>
    <tr>
        <th style="width:10%" class="text-center">S.No.</th>
        <th style="width:20%">Date</th>
        <th style="width:15%">Inv. No</th>
        <th style="width:20%">Patient</th>
        <th style="width:25%">Treatments & <br>Products</th>
        <th style="width:10%">Cost</th>
        <th style="width:10%">Disc</th>
        <th style="width:10%">Tax</th>
        <th style="width:15%">Inv.<br> Amount</th>
        <th style="width:15%">Amount<br> Paid</th>
                               
    </tr>
</thead>
<tbody>
    <?php 
    $i=1; 
    
    foreach ($billing as $value) { 

    	$drugs = $this->db->query("SELECT GROUP_CONCAT(item_information SEPARATOR ',') as items from billing_line_items where billing_id ='".$value->billing_id."'")->row();
    	$billing_line_items = $this->db->query("select * from billing_line_items where billing_id=".$value->billing_id)->result();
    	  $total=0;$atotal=0;$dtotal=0;

              foreach($billing_line_items as $bills) {
                $price = round($bills->quantity * $bills->unit_price,2);
                
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
				$atotal = $atotal+$price;
                $dtotal = $dtotal+($price * ($bills->discount / 100));
            }
    ?> 
    <tr>
        <td class="text-center"><?php echo $i++;?></td>
        <!--<td><?php //echo date_format($value->billing_date_time,"d-m-Y")?></td>-->
		<td><?php echo date("d M Y",strtotime($value->billing_date_time));?></td>
		<td><?php echo $value->invoice_no;?></td>
      <td><?php echo $value->guest_name; ?><br><?php echo $value->umr_no; ?></td>
      <td><?php echo $drugs->items; ?></td>
      <td><?php echo round($atotal,2); ?></td> 
      <td><?php echo round($dtotal,2); ?></td> 
      <td>0.00</td>
      <td><?php echo round($total,2); ?></td> 
      <td><?php echo round($total,2); ?></td> 



		
    </tr>
  <?php } ?>  
                </tbody>
            </table>
</body>	

</html>