   <div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">INVENTORY</a></li>
            <li class="breadcrumb-item active">INVENTORY LIST</li>
          </ol>
        </div>
        <div class="col-lg-6 align-self-center text-right">
                 <a href="<?= base_url('Pharmacy_orders/pharmacy_add'); ?>" class="btn btn-primary btn-rounded box-shadow btn-icon"><i class="fa fa-plus"></i> ADD</a> 
        </div>
    </div>



<section class="main-content">
<div class="row">
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <table id="doctorlist" class="table table-striped dt-responsive nowrap">
                <thead>
    <tr>
        <th>S.No:</th>
        <th>Drug Name</th>
        <th>Batch no</th>
       <!-- <th>Doctor</th>-->
        <th>Quantity</th>
		<th>MRP</th>
		<th>Pack Size</th>
        <!--<th>Payment</th>-->
        
        <th>Expire Date</th>
                               
    </tr>
</thead>
<tbody>
   <?php $i=1; foreach ($pinfo as $value) { ?> 
    <tr>
        <td><?php echo $i;?></td>
        <td><?php echo $value->trade_name	;?></td>
        <td><?php echo $value->batch_no;?></td>
        <td><?php echo $value->oqty; ?></td>
		<td><?php echo $value->mrp; ?></td>
		<td><?php echo $value->pack_size; ?></td>
		<td><?php echo $value->expiry_date; ?></td>
    </tr>
  <?php $i++;} ?>
               
                    
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



