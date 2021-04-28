
<style type="text/css">
	
  hr{
    margin: 10px 0;
  }
</style>
</head>
<body>
<div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?php echo $_SESSION['clinic_name']; ?></div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                            	<li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li><a  href="<?php echo base_url("calendar_view"); ?>">Appointments</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                              
                                <li class="active">Patient Payment</li>
                            </ol>
                        </div>
</div>
	
	<!-- hidden input text storing selected procedure -->
	<input type="hidden" name="procedure_id" value="<?php echo $this->session->userdata('selected_procedures'); ?>">

        <div class="row">        
            <div class="col-md-12">
                <div class="card">
                	<div class="card-header font-weight-bold">
INVOICE SUMMARY
                   				
                	</div>
                    <div class="card-body">						
					<div class="row">
            <div class="row col-md-8">
 
          <div class="col-md-5">
          <h3>Consultation Billing</h3>
        </div>
        <div class="col-md-3">
          <h3><a target="_blank" href="<?php echo base_url('patients/print_invoice/'.$reg_billing[0]->appointment_id.'/'.$reg_billing[0]->billing_id); ?>" class="btn btn-info">Print Invoice</a></h3>
        </div>
       
      </div>
      </div>
      <div class="row">
        <div class="well col-md-6 invoice-body">
          <table class="table table-bordered">
          <thead>
            <tr>
              <th>Description</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody>
            <?php
           
            $total = 0;
        foreach($reg_billing as $bills)
        {
          $total +=$bills->amount;
          ?>
           <tr>
            <td><?php echo $bills->item_information; ?></td>
            <td><?php echo $bills->amount; ?></td>
            </tr>
            <?php } ?>
            <tr>
            
              <td><strong>Total</strong></td>
              <td><strong><?php echo $total; ?></strong></td>
            </tr>
        
          </tbody>
        </table>
        </div>
        </div>
        <?php if($procedure == 1){ ?>
          <div class="row col-md-8">
 
          <div class="col-md-5">
          <h3>Procedure Billing</h3>
        </div>
        <div class="col-md-3">
          <h3><a target="_blank" href="<?php echo base_url('patients/print_invoice/'.$procedure_billing[0]->appointment_id.'/'.$procedure_billing[0]->billing_id); ?>" class="btn btn-info">Print Invoice</a></h3>
        </div>
       
      </div>
      <div class="row">
        <div class="well col-md-6 invoice-body">
          <table class="table table-bordered">
          <thead>
            <tr>
              <th>Description</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody>
               <?php
            $total=0;
        foreach($procedure_billing as $bills)
        {
          $total += $bills->amount;
          ?>
          <tr>
            <td><?php echo $bills->item_information; ?></td>
            <td><?php echo $bills->amount; ?></td>
            </tr>
            <?php } ?>
           
              <tr>
              <td><strong>Total</strong></td>
              <td><strong><?php echo $total; ?></strong></td>
            </tr>
          </tbody>
        </table>
        </div>
      </div>
					<?php } ?>
				</div>
			</div>
			</div>
		</div>


