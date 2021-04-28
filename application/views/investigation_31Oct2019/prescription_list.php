   <div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">UMDAA CLINICS</a></li>
            <li class="breadcrumb-item active">PRESCRIPTION LIST</li>
          </ol>
        </div>
    
    </div>
<section class="main-content">
<div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="investigation_list" class="table table-striped dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>CLINIC</th>
                                        <th>DOCTOR</th>
                                        <th>PATIENT</th>
                                        <th>APPOINTMENT DATE</th>
                                        <th>ACTION</th>

                                    </tr>
                                </thead>
                                <tbody>
                                   <?php $i=1; foreach ($prescription_list as $value) { ?> 
                                    <tr>
                                      <?php 
                                      $dinfo = $this->Generic_model->getSingleRecord('doctors',array('doctor_id'=>$value->doctor_id),$order='');
                                      $pinfo = $this->Generic_model->getSingleRecord('patients',array('patient_id'=>$value->patient_id),$order='');
                                      $cinfo = $this->Generic_model->getSingleRecord('clinics',array('clinic_id'=>$value->clinic_id),$order='');
                                      $appointment_info = $this->db->query("SELECT * FROM `appointments` where clinic_id='".$cinfo->clinic_id."' and doctor_id='".$dinfo->doctor_id."' and patient_id='".$pinfo->patient_id."' order by appointment_id asc ")->row();


                                      ?>
                                        <td><?php echo $i++;?></td>

                                        <td><?php echo $cinfo->clinic_name; ?></td>
                                        <td><?php echo $dinfo->first_name ." ". $dinfo->last_name; ?></td>
                                        <td><?php echo $pinfo->first_name ." ". $pinfo->last_name; ?></td>
                                        <td><?php echo $appointment_info->appointment_date; ?></td>
                                        <td><a href="<?php echo base_url('prescription/prescription_view/'.$value->patient_investigation_id);?>"><i class="fa fa-eye"></i></a>
                                        
                                          <a href="<?php echo base_url('prescription/prescription_delete/'.$value->patient_investigation_id);?>" onClick="return doconfirm();"><i class="fa fa-gavel"></i></a></td>
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
      $('#investigation_list').dataTable();
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



 