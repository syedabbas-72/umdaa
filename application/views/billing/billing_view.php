   <div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">UMDAA CLINICS</a></li>
            <li class="breadcrumb-item active">BILLING</li>
          </ol>
        </div>
        
    </div>



<section class="main-content">
<div class="row">
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
<div class="col-sm-12">
  <div class="form-group" style=" float:  right;margin-right: 0px;">
  <a href="javascript:void(0)" onClick="window.history.go(-1)"> <button type="button" class="btn btn-danger">
          Back</button></a>
  </div>
</div>
<div class="row col-md-12">
  <div class="row col-md-12">
  Name : <?php echo $pname; ?></h2>
</div>
  
<div class="row col-md-12">
  <h2><?php echo $clinic; ?></h2>
</div>

<div style="float:right">
  <address><?php echo $clinic_address; ?></address>
</div>
</div>
<table id="doctorlist" class="table table-striped dt-responsive nowrap">
  <thead>
    <tr>
        <th>Payment Type</th>
        <th>BillingInfo</th>
        <th>Date</th>
        <th>Amount</th>
  </tr>
</thead>
<tbody>
  
   <?php $i=1; foreach ($billing as $value) { ?> 
    <tr>
      <td><?php echo $value->billing_type; ?></td>
        <td>
          <?php echo'<div>
          <div>'.$value->bank_name.'</div>
          <div>'.$value->cheque_no.'</div>          
          <div>Deposit Date : '.$value->deposit_date.'</div>
          </div>'; ?>
        </td>
        <td><?php echo $value->billing_date_time; ?></td>
        <td><?php echo $value->amount; ?></td>
        
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



