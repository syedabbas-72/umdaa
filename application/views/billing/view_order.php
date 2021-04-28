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
            <li><a class="parent-item" href="<?=base_url('Billing')?>">F.O.Billing</a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>        
            <li class="active">Order# <?php echo $billing_master->invoice_no; ?></li>
        </ol>
    </div>
</div>

<!-- Modal -->
<div id="PayModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Bill Payments</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body paymentBody">
                <form action="<?=base_url('Billing/clearDues')?>" method="post">
                    <div class="form-group">
                        <input type="hidden" name="billing_id" class="billID">
                        <label class="control-label font-weight-bold">Outstanding Balance</label>
                        <input type="text" class="osa form-control" name="osb" readonly="">
                    </div>
                    <div class="form-group text-center">
                        <button class="btn btn-app">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Ends -->

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body"> 
              <?php  if($billing_master->billing_type != "Pharmacy") {  ?>
                <form method="post" action="<?php echo base_url('patients/print_invoice/'.$billing_master->appointment_id.'/'.$billing_master->billing_id); ?>" target="_blank">
                  <table class="table-bordered table customTable">
                    <thead>
                      <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 45%">Item Description</th>
                        <th style="width: 15%">Discount Amount</th>
                        <th style="width: 15%">Amount</th>       
                        <th style="width: 15%">Total Amount</th>                 
                    </tr>
                </thead>
                <tbody>
                    <?php

                    $ptotal = 0;$total=0;$i=1;$j = 0;
                    foreach($billing_line_items as $bills)
                    {
                        $paid = 0;
                        if($bills->discount==0)
                        {
                            $discAmount = $bills->discount;
                        }
                        elseif($bills->discount!=0)
                        {
                            if($bills->discount_unit == "INR")
                            {
                                $discAmount = $bills->discount;
                            }
                            elseif($bills->discount_unit == "%")
                            {
                                $discAmount = (($bills->amount*$bills->discount)/100);
                            }
                        }
                        $paid = $bills->amount-$discAmount;
                        $total += $bills->amount;
                        $discAmnt += $discAmount;  
                        $ptotal += $paid;
                        ?>
                        <tr>
                            <td class="text-right"><?php echo $i++; ?>. </td>
                            <td><?php echo $bills->item_information; ?></td>
                            <td class="text-right font-weight-bold"><?php echo number_format($discAmount,2); ?>/-</td>
                            <td class="text-right font-weight-bold"><?php echo number_format($paid,2); ?>/-</td>
                            <td class="text-right font-weight-bold"><?php echo number_format($bills->amount,2); ?>/-</td>
                            <!-- <td class="text-right font-weight-bold"><?php echo number_format($billing_invoice[$j]->invoice_amount, 2); ?>/-</td>
                            <td class="font-weight-bold"><?php echo strtoupper($billing_invoice[$j]->payment_type); ?></td>
                            <td class="text-center font-weight-bold">
                                <?php 
                                if($billing_master->osa != 0)
                                {
                                ?>
                                <a href="#" data-toggle="modal" data-target="#PayModal" data-id="<?=$data_id?>" class="pay">
                                <i class="fas fa-rupee-sign"></i>
                                </a>&emsp;  
                                <?php
                                }
                                ?>

                                <a href="#">
                                <i class="fas fa-print"></i>
                                </a>
                            </td> -->
                        </tr>
                        <?php 
                        $j++; 
                    }

                    // $billInfo = $this->db->query("select sum(invoice_amount) as paidSum from billing_invoice where billing_id='".$billing_master->billing_id."'")->row();
                    $total_paid = ((float)$billing_master->total_amount) - ((float)$billing_master->osa);
                    ?>
                        <tr>
                            <td colspan="4" class=" text-right">
                                <label class="font-weight-bold control-label">Total</label>
                            </td>
                            <td class=" text-right">
                                <label class="font-weight-bold control-label"><?=number_format($total,2)?>/-</label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class=" text-right">
                                <label class="font-weight-bold control-label">To Pay</label>
                            </td>
                            <td class=" text-right">
                                <!-- <label class="font-weight-bold control-label"><?=number_format($billInfo->paidSum,2)?>/-</label> -->
                                <label class="font-weight-bold control-label"><?=number_format($total-$discAmnt,2)?>/-</label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class=" text-right">
                                <label class="font-weight-bold control-label">Outstanding Balance</label>
                            </td>
                            <td class=" text-right">
                                <!-- <label class="font-weight-bold control-label"><?=number_format($ptotal-$billInfo->paidSum,2)?>/-</label> -->
                                <label class="font-weight-bold control-label"><?=number_format($billing_master->osa,2)?>/-</label>
                            </td>
                        </tr>
                        <!-- <tr>
                            <td colspan="5" style=" text-align: right">
                            Total Amount : <span style="color: #000; font-weight: 600;"><?php echo number_format($total,2); ?>/-</span> 
                            </td>
                            </tr>
                            <tr>
                            <td colspan="5" style=" text-align: right">
                            Total Savings : <span style="color: #000; font-weight: 600;"><?php echo number_format($total-$ptotal,2); ?>/-</span> 
                            </td>
                            </tr>
                            <tr>
                            <td colspan="5" style=" text-align: right">
                            Total Amount to be payable : <span style="color: #000; font-weight: 600;"><?php echo number_format($ptotal,2); ?>/-</span> 
                            </td>
                        </tr> -->
                    </tbody>
                </table>
                <!-- <table class="table table-bordered customTable">
                    <tr>
                    <td style="width: 25%">
                    <label class="col-form-label text-uppercase text-right"><b>Mode Of Payment</b></label>
                    </td>
                    <td style="width: 25%">
                    <label class="col-form-label text-uppercase"><b><?=$billing_master->payment_mode?></b></label>
                    </td>
                    <td style="width: 25%">
                    <label class="col-form-label text-uppercase text-right"><b><?=($billing_master->transaction_id!="")?'Transaction ID':'&nbsp;'?></b></label>
                    </td>
                    <td style="width: 25%">
                    <label class="col-form-label text-uppercase"><b><?=($billing_master->transaction_id!="")?$billing_master->transaction_id:'&nbsp;'?><b></label>
                    </td>
                    </tr>
                </table> -->
                <?php
                // if(count($billing_invoice)>0)
                // {
                ?>
                <table class="customTable">
                    <thead>
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 15%">Invoice No</th>
                            <th style="width: 15%">Payment Type</th>
                            <th style="width: 15%">Payment Mode</th>
                            <th style="width: 15%">Transaction ID</th>
                            <th style="width: 15%" class="text-right">Invoice Amount</th>
                            <th style="width: 15%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <?php
                    $invoice_amount = ((float)$billing_master->total_amount) - ((float)$billing_master->osa);
                    // $i = 1;
                    // foreach ($billing_invoice as $value) 
                    // {
                        ?>
                        <!-- <tr>
                            <td class="text-right"><?=$i?>. </td>
                            <td><?=$value->invoice_no?></td>
                            <td><?=$value->payment_type?></td>
                            <td><?=ucwords($value->payment_mode)?></td>
                            <td><?=$value->transaction_id?></td>
                            <td class="text-right">Rs.<?=$value->invoice_amount?>/-</td>
                            <td class="text-center"><a href="<?=base_url('Billing/printBill/'.$value->billing_invoice_id)?>" target="blank"><i class="fa fa-print"></i></a></td>
                        </tr> -->
                        <tr>
                            <!-- <td class="text-right"><?=$i?>. </td> -->
                            <td class="text-right">1. </td>
                            <td><?=$billing_master->invoice_no?></td>
                            <td><?=$billing_master->modification_remark?></td>
                            <td><?=ucwords($billing_master->payment_mode)?></td>
                            <td><?=$billing_master->transaction_id?></td>
                            <td class="text-right">Rs.<?=$total?>/-</td>
                            <td class="text-center"><a href="<?=base_url('Billing/printProcedureBill/'.$billing_master->billing_id)?>" target="blank"><i class="fa fa-print"></i></a></td>
                        </tr>
                        <?php
                    //     $i++;
                    // }
                    ?>
                    </table>
                    <?php
                // }
                ?>

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
            <!-- <input class="btn btn-success" type="submit" name="submit" value="Print Invoice" /> -->
            <?php
            if($billing_master->status != 2 || $billing_master->status != 3)
            {
                ?>
                <input class="btn btn-danger" type="button" name="cancel" value="Cancel / Drop Invoice" onclick="drop_invoice('<?php echo $billing_master->billing_id; ?>');" />
                <button type="button" class="btn btn-app" onclick="refund_invoice('<?php echo $billing_master->billing_id; ?>');" id="refund" data-values="<?=$billing_master->billing_id?>*<?=$total_price?>">Refund Money</button> 
                <?php
            }
            else
            {
                ?>
                <p><?=($billing_master->status==2)?'Refunded':($billing_master->status==3)?'Dropped':''?></p>
                <?php
            }
            if($billing_master->osa != 0)
            {
                $data_id = $billing_master->billing_id."*$".$billing_master->osa;
                ?>
                <button type="button" class="btn btn-danger"  data-toggle="modal" data-target="#PayModal" data-id="<?=$data_id?>" id="osa_pay">Clear Dues</button> 
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


            <!-- Refund Modal -->
            
            <!-- Refund Modal Ends -->


<script type="text/javascript">
/*function drop_invoice(bid)
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
}*/


function drop_invoice(bid)
{
  var confirm = window.confirm("Are You Sure to Drop This Invoice ?");
  if(confirm == true)
  {
    var base_url = '<?php echo base_url(); ?>';
    $.ajax({
      url : '<?=base_url('Billing/drop_invoice')?>',
      method : "POST",
      data : {"bid":bid},
      success : function(rdata) { 
        alert('Invoice Dropped');
        window.location.href = "<?php echo base_url('Billing'); ?>"; 
    }
});
}

}

function refund_invoice(bid)
{
  var confirm = window.confirm("Are You Sure to Give Refund ?");
  if(confirm == true)
  {
    var base_url = '<?php echo base_url(); ?>';
    $.ajax({
      url : '<?=base_url('Billing/refund_invoice')?>',
      method : "POST",
      data : {"bid":bid},
      success : function(rdata) { 
        alert('Invoice Refunded');
        window.location.href = "<?php echo base_url('Billing'); ?>"; 
    }
});
}

}
$(document).ready(function(){
  $('#osa_pay').on("click",function(){
    var data = $(this).attr("data-id").split("*$");
    $('.billID').val(data[0]);
    $('.osa').val(data[1]);
});
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