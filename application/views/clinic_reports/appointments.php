<div class="row page-header no-background no-shadow margin-b-0">
  <div class="col-lg-6 align-self-center">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#"><?php echo $_SESSION['clinic_name']; ?></a></li>
      <li class="breadcrumb-item active">APPOINTMENT REPORTS</li>
    </ol>
  </div>
<?php 
$clinic_id = $this->session->userdata('clinic_id');

if($clinic_id==0) { ?>
  <div class="col-lg-6 align-self-center text-right">
    <a href="<?php echo base_url('Doctor/doctor_add');?>" class="btn btn-primary box-shadow btn-icon btn-rounded"><i class="fa fa-plus"></i> ADD</a>
  </div>
<?php } ?>
</div>

<section class="main-content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
                   
                      <form method="POST" action="<?php echo base_url('reports/get_appointments');?>" enctype="multipart/form-data" role="form">
                        <div class="row col-md-12">
                                        <div class="col-md-3">
                            <div class="form-group">
                                <select name="doctor_name" id="filter-customer" class="form-control"tabindex="-1" aria-hidden="true">
                                    <option value="">Select Doctor: View All</option>
                                    <?php foreach ($doctor_list as  $value) { ?>
                                      <option value="<?php echo $value->doctor_id; ?>"><?php echo $value->first_name." ".$value->last_name; ?></option>
                                    <?php } ?>
                             </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" class="form-control datepicker" name="appointment_date_from" id="appointment_date_from" placeholder="Appointment Date From">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" class="form-control datepicker" name="appointment_date_to" id="appointment_date_to" placeholder="Appointment Date To">
                            </div>
                        </div>
                                
                                                <div class="col-md-3">
                            <div class="form-group">
                                <input type="submit" id="reset-filter" class="btn btn-danger" value="Submit">
                            </div>
                        </div>
                    </div>
                  

              </form>

                  
                </div>
                <hr>
        <div class="card-body">
          <table id="app_list" class="table table-striped dt-responsive nowrap">
            <thead>
              <tr>
  
                <th>Name</th>
                <th>UMR</th>
                <th>Doctor</th>
                <th>Date</th>
                <th>Slot</th>
             
              </tr>
            </thead>
            <tbody>
              <?php $i=1; foreach ($appointments as $value) { ?> 
              <tr>
                <td><?php echo strtoupper($value->pfname." ".$value->plname); ?></td>
                <td><?php echo $value->umr_no; ?></td>
                <td><?php echo strtoupper("DR. ".$value->dfname." ".$value->dlname); ?></td>
                <td><?php echo date("d-m-Y",strtotime($value->appointment_date)); ?></td>
                <td><?php echo date("h:i A",strtotime($value->appointment_time_slot)); ?></td>
              
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
    $('#app_list').dataTable({
       dom: 'Bfrtip',
          buttons: [
          
            'excelHtml5'
         
          ],
   "ordering": false
    });
});

</script>