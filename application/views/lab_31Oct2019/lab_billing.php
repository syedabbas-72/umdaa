   <div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><?php echo $_SESSION['clinic_name']; ?></a></li>
            <li class="breadcrumb-item active">LAB BILLING</li>
          </ol>
        </div>
        <!--<div class="col-lg-6 align-self-center text-right">
	     <a href="<?= base_url('New_order/add_order'); ?>" class="btn btn-primary btn-rounded box-shadow btn-icon"><i class="fa fa-plus"></i> ADD NEW ORDER</a>
		 
	</div>-->
    </div>



<section class="main-content">
<div class="row">
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <table id="doctorlist" class="table table-striped dt-responsive nowrap table-responsive">
                <thead>
    <tr>
        <th style="width:10%" class="text-center">S.No:</th>
        <th style="width:15%">Date</th>
		<th style="width:15%">Invoice</th>
		<th style="width:15%">Order ID</th>
        <th style="width:45%">Investigation Information</th>
		<th style="width:45%">Payment Type</th>
		<th style="width:45%">Total Amount</th>
        <th style="width:20%" class="text-right">Payment</th>
		<th style="width:20%" class="text-right">Bal. Amount</th>
        
                               
    </tr>
</thead>
<tbody>
    <?php 
    $i=1; 
    foreach ($billing_info as $value) { 
    ?> 
    <tr>
      <td class="text-center"><?php echo $i++;?></td>        
  		<td><?php echo $value['inv_date'];?></td>
  		<td><?php echo $value['invoice'];?></td>
  		<td><?php echo $value['order'];?></td>
  		<td><?php echo $value['investigations']; ?></td> 
  		<td class="text-right"><?php echo $value['ptype']; ?></td> 
      <td class="text-right"><?php echo $value['inv_amt']; ?></td> 
  		<td class="text-right"><?php echo $value['p_amt']; ?></td> 
  	  <td class="text-right"><?php echo $value['inv_amt']-$value['p_amt']; ?></td> 
		
        <!--<td class="text-center">
		<?php //if($value['out_amt']>0){?>
		<a href="<?php echo base_url('Lab/make_lab_payment/'.$value['billing_id']);?>"><input type="button" value="Make Payment" /></a>
		<?php //} ?>
		<a href="<?php echo base_url('Lab/view_order/'.$value['billing_id']);?>"><i class="fa fa-eye"></i></a>
         <a data-target = "tool-tip"  href="<?php echo base_url('uploads/billing/'.$value->invoice_pdf);?>"><i class="fa fa-download"></i></a></td>-->
		
    </tr>
  <?php } ?>  
                </tbody>
            </table>

        </div>
    </div>
</div>
</div>

    </section>

 <script>
  $(document).ready(function () {
      $('#doctorlist').dataTable();
  });
  </script>
  <script>
  function doconfirm()
    {
        if(confirm("Delete selected messages ?")){
            return true;
        }else{
            return false;  
        } 
   }
  </script>




