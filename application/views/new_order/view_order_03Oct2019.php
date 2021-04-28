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
          <li class="active">Order# <?php echo $billing_master->receipt_no.' / '.$billing_master->invoice_no; ?></li>
      </ol>
  </div>
</div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
      <div class="card-body"> 
          <form method="post" action="<?php echo base_url('New_order/print_bill/'.$billing_master->billing_id); ?>" target="_blank">
					 <table class="table-bordered table">
            <thead>
              <tr>
                <th>S#</th>
                <th>Drug Desc.</th>
                <th>Batch#</th>
                <th>HSN Code</th>
                <!-- <th class="text-right">MRP / Unit</th> -->
                <th class="text-center">Qty</th>                
                <th class="text-right">Price Inc. GST</th>                
                <th class="text-center">CGST %<br>Amt.</th>
                <th class="text-center">SGST %<br>Amt</th>
                <th class="text-center">IGST %<br>Amt.</th>
                <th class="text-center">Disc(%)</th>
                <th class="text-right">Payable Amt</th>
                <!-- <th class="text-right">Value<br>In-GST</th>                
                <th class="text-right">TaxValue</th> -->                
              </tr>
            </thead>
            <tbody>
              <?php
              $i=1;
              $total=0;

              foreach($billing_line_items as $bills) {
                $price = round($bills->quantity * $bills->unit_price,2);
                
                // Accountable price if any discounts applying
                $accountablePrice =  round($price - ($price * ($bills->discount / 100)),2);
                $totalPrice = $totalPrice + $price;
                $totalDiscount = round($totalDiscount + ($price * ($bills->discount / 100)),2);
               
                // Taxation
                // Value inclding GST = mrp ($accountablePrice)
                // TaxValue = (mrp * 100)/(100 + CGST + SGST)
                $taxValue = round(($accountablePrice * 100)/(100 + $bills->cgst + $bills->sgst + $bills->igst),2);

                $cgst = round($taxValue * ($bills->cgst / 100),2);
                $sgst = round($taxValue * ($bills->sgst / 100),2);
                $igst = round($taxValue * ($bills->igst / 100),2);

                $total = $total + $accountablePrice;
                
              ?>
              <tr>
                <td class="text-center"><?php echo $i++; ?></td>
                <td><?php echo $bills->item_information; ?></td>
                <td><?php echo $bills->batch_no; ?></td>
                <td><?php echo $bills->hsn_code; ?></td>
                <?php /*<td class="text-right"><?php echo '<span class="price">'.round($bills->unit_price,2).'</span>&nbsp;&nbsp;<span class="tiny">INR</span>'; ?></td>*/?>
                <td class="text-center"><?php echo $bills->quantity; ?></td>
                <td class="text-right"><?php echo '<span class="price">'.$price.' /-</span>'; ?></td>
                <td class="text-center"><?php echo $bills->cgst.'%<br><span class="price">'.$cgst.' /-</span>'; ?></td>
                <td class="text-center"><?php echo $bills->sgst.'%<br><span class="price">'.$sgst.' /-</span>'; ?></td>
                <td class="text-center"a><?php echo $bills->igst.'%<br><span class="price">'.$igst.' /-</span>'; ?></td>
                <td class="text-center"><?php echo round($bills->discount,2); ?></td>
                <td class="text-right"><?php echo '<span class="price">'.$accountablePrice.'/-</span>'; ?></td>
                <?php /*<td class="text-right"><?php echo '<span class="price">'.$taxValue.'</span>'; ?></td>*/?>                
              </tr>
              <?php } ?>
              <tr>
                <td colspan="12" class="text-right">
                  Total Amount : <span style="color: #000; font-weight: 600;"><?php echo round($totalPrice,2); ?> /-</span> 
                </td>
              </tr>
              <tr>
                <td colspan="12" class="text-right">
                  Total Savings : <span style="color: #000; font-weight: 600;"><?php echo round($totalDiscount,2); ?> /-</span> 
                </td>
              </tr>
              <tr>
                <td colspan="12" style=" text-align: right">
                  Total Amount to be payable : <span style="color: #000; font-weight: 600;"><?php echo round($total,2); ?> /-</span> 
                </td>
              </tr>
            </tbody>
          </table>
          <?php /*
          <div id="div_cash" style="display: none;">
            <div class="row col-md-12" >
              <div class="col-md-3">
                <div class="form-group">
                  <label for="dd_or_cash_no" class="col-form-label">DD/Check No<span style="color:red;">*</span></label>
                  <input id="dd_or_cash_no" name="dd_or_cheque_no" type="text" placeholder="" class="form-control" value = ''>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="bank_id" class="col-form-label">BANK NAME<span style="color:red;">*</span></label>
                  <input name="bank_name" class="form-control">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="fee_date" class="col-form-label">DATE<span style="color:red;">*</span></label>
                  <input id="fee_date" name="fee_date" type="text" placeholder="" class="form-control" value = '<?php echo date("Y-m-d"); ?>'>
                </div>
              </div>
            </div>
        </div>
        */ ?>
			<!-- </div> -->
<div class="row">
  <div class="col-md-12 text-center" >
    <input class="btn btn-success" type="submit" name="submit" value="Print Invoice" />
    <input class="btn btn-success" type="button" name="cancel" value="Cancel / Drop Invoice" onclick="drop_invoice('<?php echo $billing_master->billing_id; ?>');" />
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