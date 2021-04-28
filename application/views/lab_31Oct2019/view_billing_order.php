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
  .modal-dialog {
    max-width: 80% !important;
    margin: 30px auto;
}
</style>
<div class="row page-header no-background no-shadow margin-b-0">
  <div class="col-lg-6 align-self-center">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#"><?php echo $_SESSION['clinic_name']; ?></a></li>
      <li class="breadcrumb-item"><a href="#">Billing</a></li>
      <li class="breadcrumb-item active">Order# <?php echo $billing_master->billing_no; ?></li>
    </ol>
  </div>
</div>
<section class="main-content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
	  <table class="table-bordered table">
            <thead>
              <tr>
                <th>Patient Information</th>
                                               
              </tr>
            </thead>
            <tbody>
			<tr>
			<td><?php
			echo $billing_master->guest_name."<br />".$billing_master->guest_mobile;
			?></td>
			</tr>
			</tbody>
		</table>
        <!-- <div class="card-body"> -->
          <!--<form method="post" action="<?php echo base_url('Lab/print_bill/'.$billing_master->billing_id); ?>" target="_blank">-->
					 <table class="table-bordered table">
            <thead>
              <tr>
                <th>S#</th>
                <th>Investigation</th>
                <th>Item Code</th>
                <th>Short Form</th>
                <th class="text-right">Category</th>
                <th class="text-center">MRP</th>                                
              </tr>
            </thead>
            <tbody>
              <?php
              $i=1;
              $total=0;
			  $j=0;
              foreach($billing_line_items as $bills) {
                $price = round($bills['quantity'] * $bills['unit_price'],2);               
				if($j==0)
					$picount = $bills['picount'];
                $total = $total + $accountablePrice;
                if($bills['pid']==1&&($picount==$bills['picount'])){
					//echo $j."-".$bills['pid']."-"."tests"."<br />";
					$j++;$picount--;
              ?>
			  <tr>
			  <td colspan="6"><?php echo $bills['opname']; ?></td>
			  </tr>
				<?php }else{
			  if($bills['pid']==0)
				  $j=0;
			  //echo $j."-".$bills['pid']."-"."test"."<br />";
			  } ?>
              <tr>
                <td class="text-center"><?php echo $i++; ?></td>
                <td><?php echo $bills['pname']; ?> <button id="view_parameter" class="btn btn-info btn-rounded btn-xs" onclick="openpopup(<?php echo $bills['oid'];?>,<?php echo $billing_master->billing_id; ?>,<?php echo $bills['pid']; ?>)">view</button></td>
                <td><?php echo $bills['item_code']; ?></td>
                <td><?php echo $bills['short_form']; ?></td>
				<td><?php echo $bills['category']; ?></td>
				
                <td class="text-right"><?php echo '<span class="price">'.$bills['amount'].'</span>&nbsp;&nbsp;<span class="tiny">INR</span>'; ?></td>
                
              </tr>
              <?php } ?>
              <!--<tr>
                <td colspan="12" style=" text-align: right">
                  Total Amount to be payable : <span style="color: #000; font-weight: 600;"><?php echo ($invoice->iamt-$invoice->aamount); ?> INR</span> 
                </td>
              </tr>-->
            </tbody>
          </table>
         <!-- <div class="row col-md-12">
            <div class="col-md-6">
              PAYMENT MODE<br>
              <select class="form-control" name="payment_mode" id="payment_type">
                <option value="Cash">Cash</option>
                <option value="Card">Card</option>
                <option value="PayTM">PayTM</option>
                <option value="Google_Pay">Google Pay</option>
              </select>
            </div>
          </div>-->
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
		</div>
  </div>
</div>

<!--<div class="row">
	<div class="col-md-12 text-center" >
		<input class="btn btn-success" type="submit" name="submit" value="Print Invoice" />
	</div>
	</div>
  </form>-->
</section>
<div class="modal fade" id="InvModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    
      <div class="modal-body">
      

    
      <div class="row col-md-12">
        <form method="POST" action="<?php echo base_url('Lab/templates_input_save'); ?>" role="form">
		<input type="hidden" name="pname" value="<?php echo $billing_master->guest_name; ?>" />
		<input type="hidden" name="pmobile" value="<?php echo $billing_master->guest_mobile; ?>" />
		<input type="hidden" name="bid" value="<?php echo $billing_master->billing_id; ?>" />
		<input type="hidden" name="type" value="excel" />
           <table id="orderlist" class="table table-bordered dt-responsive nowrap">
              <thead>
                <tr>
                
                  <th style="width: 300px">Parameter</th>
                  <th style="width: 100px">Value</th>
                  <th style="width: 70px">Low Range</th>
                  <th style="width: 70px">High Range</th>
                  <th style="width: 100px">Units</th>
                  <th style="width: 150px">Method</th> 
				  <th>Other Information</th>
                </tr>
              </thead>
              <tbody id="template_excel">
              </tbody>
            </table>
                       
                      
      </div>

    </div>
    <div class="modal-footer">

        <input type="submit" class="btn btn-primary" id="block_submit" value="Save">
       

       

        </div>
		</form>
      </div>
     </div>
     </div>
	 <div class="modal fade" id="InvModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    
      <div class="modal-body">
      

    
      <div class="row col-md-12">
        <form method="POST" action="<?php echo base_url('Lab/templates_input_save'); ?>" role="form">
		<input type="hidden" name="pname" value="<?php echo $billing_master->guest_name; ?>" />
		<input type="hidden" name="pmobile" value="<?php echo $billing_master->guest_mobile; ?>" />
		<input type="hidden" name="bid" value="<?php echo $billing_master->billing_id; ?>" />
		<input type="hidden" name="type" value="general" />
                           <table id="orderlist" class="table table-bordered dt-responsive nowrap">
              <thead>
                <tr>
                
                  <th style="width: 250px">Parameter</th>
                  
                  <th style="width: 100px">Remarks</th>
                  
                                             
                </tr>
              </thead>
              <tbody id="template_general">
              </tbody>
            </table>
                       
                      
      </div>

    </div>
    <div class="modal-footer">

        <input type="submit" class="btn btn-primary" id="block_submit" value="Save">
        

       

        </div>
		</form>
      </div>
     </div>
     </div>
<script type="text/javascript">
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
  function openpopup(invgid,bid,pid)
  {
	  
	  var base_url = '<?php echo base_url(); ?>';
	  $.ajax({
			  url : base_url+"/Lab/get_template_info",
			  method : "POST",
			  data : {"invg":invgid,"bid":bid,"pid":pid},
			  success : function(drgid) {
				drgid = $.trim(drgid);
				//alert(drgid);
				var a = drgid.split(":");
				if(a[0]=="excel")
				{
					$("#template_excel").empty();
					$("#template_excel").append(a[1]);
					$("#InvModal").modal();
					
				}
				else if(a[0]=="general")
				{
					$("#template_general").empty();
					$("#template_general").append(a[1]);
					$("#InvModal1").modal();
				}
			  }
		});
	  
  }
   /*$(document).on("click","#view_parameter",function(){
    
    return false;
   })*/
</script>
