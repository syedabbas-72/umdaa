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
     
      <ol class="breadcrumb page-breadcrumb pull-right">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="#"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>  
          <li><a class="parent-item" href="#">Prescriptions List</a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>        
         
      </ol>
  </div>
</div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
      <div class="card-body"> 
          
        
					 <table class="table-bordered table datatable patientsData">
            <thead>
              <tr>
                <th>S#</th>
                <th>Patient Name</th>
                <th>UMR</th>
                <th>Prescription Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $i=1;
        foreach($patient_prescription as $prescription)
        {
          $total +=$bills->amount;
          ?>
           <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo ucwords($prescription->first_name." ".$prescription->last_name); ?></td>
            <td><?php echo $prescription->umr_no; ?></td>
            <td><?php echo date("d-m-Y",strtotime($prescription->pdate)); ?></td>
            <td>
              <a href="<?php echo base_url('New_order/add_order/'.$prescription->patient_prescription_id);?>"><i class="fas fa-shopping-basket"></i></a>
              <a href="<?php echo base_url('pharmacy_prescription/print/'.$prescription->patient_prescription_id);?>"><i class="fas fa-print"></i></a>
              <a href="<?php echo base_url('pharmacy_prescription/view_prescription/'.$prescription->patient_prescription_id);?>"><i class="fas fa-eye"></i></a>
            </td>
            </tr>
            <?php } ?>
            
             
            </tbody>
          </table>
       
		</div>
  </div>
</div>
</div>

