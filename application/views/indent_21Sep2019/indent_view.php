<div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><?php echo $_SESSION['clinic_name']; ?></a></li>
            <li class="breadcrumb-item active">INDENT</li>
          </ol>
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
        <th>Quantity</th>
       
                               
    </tr>
</thead>
<tbody>
   <?php $i=1; foreach ($indent_info as $value) { ?> 
    <tr>
        <td><?php echo $i;?></td>
        <td><?php echo $value->trade_name;?></td>
        <td><?php echo $value->quantity;?></td>
        
    </tr>
  <?php $i++;} ?>
               
                    
                </tbody>
            </table>

        </div>
    </div>
</div>
</div>
</section>