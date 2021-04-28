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
      <ol class="breadcrumb page-breadcrumb">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?=base_url('Dashboard')?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>  
          <li><a class="parent-item" href="<?=base_url('Pharmacy_Billing')?>">Billing</a>&nbsp;<i class="fa fa-angle-right"></i>
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
                <th style="width: 5%">S#</th>
                <th>Drug Desc.</th>
                <th>Batch#</th>
                <th>HSN Code</th>
                <!-- <th class="text-right">MRP / Unit</th> -->
                <th>Qty</th>                
                <th>Price Inc. GST</th>                
                <th>CGST %<!-- br>Amt. --></th>
                <th>SGST %<!-- <br>Amt --></th>
                <th>IGST %<!-- <br>Amt. --></th>
                <th>Disc(%)</th>
                <th>Amount</th>
                <!-- <th class="text-right">Value<br>In-GST</th>                
                <th class="text-right">TaxValue</th> -->                
              </tr>
            </thead>
            <tbody>
              <?php
              $i=1;
              $total=0;
              
              foreach($billing_line_items as $bills) {
                $price = $bills->quantity * $bills->unit_price;
                // echo $price;
                // exit();
                // Accountable price if any discounts applying
                $accountablePrice =  $price - ($price * ($bills->discount / 100));
                $totalPrice = $totalPrice + $price;
                $totalDiscount = number_format($totalDiscount + ($price * ($bills->discount / 100)),2);
               
                // Taxation
                // Value inclding GST = mrp ($accountablePrice)
                // TaxValue = (mrp * 100)/(100 + CGST + SGST)
                $taxValue = number_format(($accountablePrice * 100)/(100 + $bills->cgst + $bills->sgst + $bills->igst),2);

                $cgst = round($taxValue * ($bills->cgst / 100),2);
                $sgst = round($taxValue * ($bills->sgst / 100),2);
                $igst = round($taxValue * ($bills->igst / 100),2);

                $total = $total + $accountablePrice;
                //use this for cgst, igst, sgst
                // // .'%<br><span class="price">'.$cgst.' /-</span>'
                // .'%<br><span class="price">'.$sgst.' /-</span>'
                // .'%<br><span class="price">'.$igst.' /-</span>'
                
              ?>
              <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $bills->item_information; ?></td>
                <td><?php echo $bills->batch_no; ?></td>
                <td><?php echo $bills->hsn_code; ?></td>
                <?php /*<td class="text-right"><?php echo '<span class="price">'.round($bills->unit_price,2).'</span>&nbsp;&nbsp;<span class="tiny">INR</span>'; ?></td>*/?>
                <td class="text-center"><?php echo $bills->quantity; ?></td>
                <td class="text-right"><?php echo '<span class="price">'.$price.' /-</span>'; ?></td>
                <td class="text-center"><?php echo $bills->cgst; ?></td>
                <td class="text-center"><?php echo $bills->sgst; ?></td>
                <td class="text-center"><?php echo $bills->igst; ?></td>
                <td class="text-center"><?php echo round($bills->discount,2); ?></td>
                <td class="text-right"><?php echo '<span class="price">'.number_format($bills->total_amount,2).'/-</span>'; ?></td>
                <?php /*<td class="text-right"><?php echo '<span class="price">'.$taxValue.'</span>'; ?></td>*/?>                
              </tr>
              <?php } ?>
              <tr>
                <td colspan="12" class="text-right">
                  Total Amount : <span style="color: #000; font-weight: 600;"><?php echo number_format($totalPrice,2); ?> /-</span> 
                </td>
              </tr>
              <tr>
                <td colspan="12" class="text-right">
                  Total Savings : <span style="color: #000; font-weight: 600;"><?php echo number_format($totalDiscount,2); ?> /-</span> 
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <label class="col-form-label"><b>Mode Of Payment</b></label>
                </td>
                <td>
                  <label class="col-form-label text-uppercase"><b><?=$billing_master->payment_mode?></b></label>
                </td>
                <td>
                  <label class="col-form-label"><b><?=($billing_master->transaction_id!="")?'Transaction ID':'&nbsp;'?></b></label>
                </td>
                <td>
                  <label class="col-form-label text-uppercase"><b><?=($billing_master->transaction_id!="")?$billing_master->transaction_id:'&nbsp;'?><b></label>
                </td>
                <td colspan="7" style=" text-align: right">
                  Total Amount to be payable : <span style="color: #000; font-weight: 600;"><?php echo number_format($total,2); ?> /-</span> 
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
    
    <?php
    if($billing_master->status == 2)
    {
      ?>
      <label class="bg-danger p-2 rounded-top rounded-bottom">Invoice Dropped</label>
      <?php
    }
    else
    {
      ?>
      <!-- <?php echo $billing_master->billing_id; ?> -->
      <input class="btn btn-success" type="submit" name="submit" value="Print Invoice" />
      <input class="btn btn-danger" type="button" name="cancel" value="Cancel / Drop Invoice" onclick="drop_invoice('<?php echo $billing_master->billing_id; ?>');" />
      <?php
    }
    ?>
    
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
  var confirm = window.confirm("Are You Sure to Drop This Invoice ?");
  if(confirm == true)
  {
    var base_url = '<?php echo base_url(); ?>';
    $.post("<?=base_url('New_order/drop_pharmacy_invoice')?>", {bid:bid}, function(data){
      alert('Invoice Dropped')
      window.location.href = '<?=base_url('Pharmacy_Billing')?>'
    })
    // $.ajax({
    //         url : base_url+"/New_order/drop_pharmacy_invoice",
    //         method : "POST",
    //         data : {bid:bid},
    //         success : function(rdata) { 
    //     alert('Invoice Dropped');
    //     window.location.href = "<?php echo base_url('Pharmacy_Billing'); ?>"; 
    //         }
    //   });
  }
  
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