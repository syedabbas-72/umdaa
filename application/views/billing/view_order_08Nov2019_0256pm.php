<style type="text/css">
  .table td{
    padding-left:15px !important;
    padding-right: 15px !important; 
  }
  .price{
    font-weight: 600;
    font-size: 14px;
  }
  .formulation{
    background: #ebebeb;
    border-radius: 4;
  }
</style>
<div class="page-bar">
  <div class="page-title-breadcrumb">
      <!-- <div class=" pull-left">
        <div class="page-title">Form Layouts</div>
      </div> -->
      <ol class="breadcrumb page-breadcrumb pull-right">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="#"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>  
          <li><a class="parent-item" href="#">Billing</a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>        
          <li class="active">Order# <?php echo $billing_master->invoice_no; ?></li>
      </ol>
  </div>
</div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
      <div class="card-body"> 
          
            <?php  if($billing_master->billing_type != "Pharmacy") {  ?>
              <form method="post" action="<?php echo base_url('patients/print_invoice/'.$billing_master->appointment_id.'/'.$billing_master->billing_id); ?>" target="_blank">
					 <table class="table-bordered table">
            <thead>
              <tr>
                <th>S#</th>
                <th>Treatment</th>
             
                <th>Amount</th>
              </tr>
            </thead>
            <tbody>
              <?php
               $total = 0;$i=1;
        foreach($billing_line_items as $bills)
        {
          $total +=$bills->amount;
          ?>
           <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo $bills->item_information; ?></td>
            <td><?php echo $bills->amount; ?></td>
            </tr>
            <?php } ?>
            
              <tr>
                <td colspan="12" style=" text-align: right">
                  Total Amount to be payable : <span style="color: #000; font-weight: 600;"><?php echo round($total,2); ?> INR</span> 
                </td>
              </tr>
            </tbody>
          </table>
        <?php } else { ?>
          <form method="post" action="<?php echo base_url('New_order/print_bill/'.$billing_master->billing_id); ?>" target="_blank">
          <table class="table-bordered table">
            <thead>
              <tr>
                <th>S#</th>
                <th>Drug Desc.</th>
                <th>Batch#</th>
                <th>HSN Code</th>
                <th class="text-right">MRP / Unit</th>
                <th class="text-center">Qty</th>                
                <th class="text-center">Disc(%)</th>
                <th class="text-right">Value<br>In-GST</th>                
                <th class="text-right">TaxValue</th>
                <th class="text-center">CGST %<br>Amt.</th>
                <th class="text-center">SGST %<br>Amt</th>
                <th class="text-center">IGST %<br>Amt.</th>
              </tr>
            </thead>
            <tbody>
             <?php

        

        $i=1;

        $grand_total=0;
        $total_price=0;
        $total_discount=0;

        foreach($billing_line_items as $bills)

        {

        
        $price = round(($bills->quantity * $bills->unit_price),2);
        
        // Accountable price if any discounts applying
              $accountablePrice =  round($price - ($price * ($bills->discount / 100)),2);
              $total_price = round(($total_price + $price),2);
              $total_discount = number_format((float)$total_discount + ($price * ($bills->discount / 100)), 2, '.', '');

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
                <td class="text-center"><?php echo $i++; ?></td>
                <td><?php echo $bills->item_information; ?></td>
                <td><?php echo $bills->batch_no; ?></td>
                <td><?php echo $bills->hsn_code; ?></td>
                <td class="text-right"><?php echo '<span class="price">'.round($bills->unit_price,2).'</span>&nbsp;&nbsp;<span class="tiny">INR</span>'; ?></td>
                <td class="text-center"><?php echo $bills->quantity; ?></td>
                <td class="text-center"><?php echo round($bills->discount,2); ?></td>
                <td class="text-right"><?php echo '<span class="price">'.$accountablePrice.'</span>'; ?></td>
                <td class="text-right"><?php echo '<span class="price">'.$taxValue.'</span>'; ?></td>
                <td class="text-center"><?php echo $bills->cgst.'<br><span class="price">'.$cgst.'</span>&nbsp;&nbsp;<span class="tiny">INR</span>'; ?></td>
                <td class="text-center"><?php echo $bills->sgst.'<br><span class="price">'.$sgst.'</span>&nbsp;&nbsp;<span class="tiny">INR</span>'; ?></td>
                <td class="text-center"a><?php echo $bills->igst.'<br><span class="price">'.$igst.'</span>&nbsp;&nbsp;<span class="tiny">INR</span>'; ?></td>
              </tr>
              <?php } ?>
               <tr>
                <td  colspan="12" style=" text-align: right;font-weight: bold;">
                  Total Cost : <span><?php echo $total_price; ?> INR</span> 
                </td>
              </tr>
                <tr>
                <td  colspan="12" style=" text-align: right;font-weight: bold;">
                  Discount : <span><?php echo $total_discount; ?> INR</span> 
                </td>
              </tr>
                <tr>
                <td  colspan="12" style=" text-align: right;font-weight: bold;">
                  Net Payable : <span><?php echo $grand_total; ?> INR</span> 
                </td>
              </tr>
            </tbody>
          </table>

        <?php }  ?>
         
<div class="row">
  <div class="col-md-12 text-center" >
     <input class="btn btn-success" type="submit" name="submit" value="Print Invoice" />
  </div>
  </div>
  </form>
		</div>
  </div>
</div>
</div>



<script type="text/javascript">
  function drop_invoice(bid)
{
  var base_url = '<?php echo base_url(); ?>';
  $.ajax({
          url : base_url+"/New_order/drop_pharmacy_invoice",
          method : "POST",
          data : {"bid":bid},
          success : function(rdata) { 
      alert('Invoice Dropped');
      window.location.href = "<?php echo base_url('Pharmacy_Billing'); ?>"; 
          }
    });
}
   $(document).ready(function(){
  
    // $("#payment_type").change(function(){
    //   var payment_type = $("#payment_type").val();
    //   if(payment_type == "cash" || payment_type == "" || payment_type == "card"){
    //      $("#div_cash").css("display","none");
    //   }else{
    //      $("#div_cash").css("display","block");
    //   }
    // });
  });
</script>