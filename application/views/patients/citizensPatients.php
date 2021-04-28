<style>
/* .bg-card
{
  background: rgb(2,0,36);
  background: linear-gradient(45deg, #10365a 28%, rgba(255,255,255,1) 28%);
  cursor: pointer;
}
.patientCard{
  cursor: pointer;
} */
</style>
<div class="page-bar">
  <div class="page-title-breadcrumb">
     
      <ol class="breadcrumb page-breadcrumb">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?=base_url('Dashboard')?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>  
          <li><a class="parent-item" href="#">Patients List</a></li>        
      </ol>
  </div>
</div>

        <section class="main-content">
          
          <div class="card">
            <div class="card-body">
              <h4 class="page-title">Patients List</h4>
              <!-- Patients View -->
              <div class="row">
                <div class="col-md-12">
                  <ul class="nav nav-pills">
                    <li class="nav-item">
                      <a class="nav-link" href="<?=base_url('Patients/getTodayAppointments')?>">Today Appointments</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link active" href="<?=base_url('Patients/getCitizens')?>">From Citizens</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link " href="<?=base_url('Patients/getDoctors')?>">From Doctors</a>
                    </li>
                  </ul>
                </div>
                

                <div class="col-md-12">
                <hr>
                <div class="row">
                <?php  
                if(count($patients) > 0)
                {
                  ?>
                  <div class="col-6">
                    <form method="post" action="<?=base_url('Patients/PatientsSearch')?>">
                      <div class="form-group d-flex">
                        <input class="form-control mr-3" type="text" placeholder="Search with Mobile Number / Name" name="search" required>
                        <button class="btn btn-app" type="submit" name="patientsearch"><i class="fa fa-search"></i></button>
                      </div>
                    </form>
                  </div>
                  <div class="col-6">
                    <div class="row">
                      <div class="col-md-6">
                        <p class="my-3 text-primary">Showing <b><?=$per_page?></b> of <b><?=$count?></b> Entries</p>
                      </div>
                      <div class="col-md-6">
                        <p class="pagination pull-right"><?=$links?></p>
                      </div>
                    </div>
                  </div>
                  <div class="col-12 my-3">
                    <div class="row">
                    <?php 
                    foreach($patients as $value)
                    {
                      $patientInfo = getPatientDetails($value->patient_id);
                      $docInfo = doctorDetails($value->doctor_id);
                      $clinicInfo = clinicDetails($value->clinic_id);

                      $appdate = $value->appointment_date.' '.$value->appointment_time_slot;
                      
                      $title = $patientInfo->title;
                      if($title == "")
                      {
                        $fullname = $patientInfo->first_name." ".$patientInfo->last_name;
                      }
                      else
                      {
                        $fullname = $title.". ".$patientInfo->first_name." ".$patientInfo->last_name;
                      }
                      if($patientInfo->mobile == "")
                      {
                        $mobile = $patientInfo->alternate_mobile;
                        $primary = 0;                      
                      }
                      else
                      {
                        $mobile = $patientInfo->mobile;
                        $primary = 1;
                      }
                      ?>
                      <div class="col-md-4">
                        <a href="<?=base_url('Profile/index/'.$patientInfo->patient_id)?>">
                          <div class="info-box">
                            <span class="info-box-icon push-bottom m-0">
                              <img src="<?php echo ($patientInfo->photo=='') ? base_url('assets/img/profileIcon.jpg') :base_url('uploads/patients/'.$patientInfo->photo) ?>" class="img-fluid" style="width:60px;height:60px;position:relative;border-radius:70px;">
                            </span>

                            <div class="info-box-content">
                              <span class="info-box-text font-weight-bold"><?=$fullname?></span>

                              <div class="row">
                                <div class="col-6">
                                  <p class="m-0 p-0"><span class="bg-primary formulation m-0 text-white"><?=$patientInfo->umr_no?></span></p>
                                </div>
                                <div class="col-6">
                                  <p class="m-0 p-0"><i class="fas fa-phone"></i> <?=DataCrypt($mobile, 'decrypt')?></p>
                                </div>
                              </div>
                              
                              <span class="progress-description">
                                <i class="fa fa-map-marker-alt"></i> <?=ucwords(strtolower($patientInfo->location))?>
                              </span>
                              
                            </div>
                            <!-- /.info-box-content -->
                          </div>
                          <!-- /.info-box -->
                        </a>
                      </div>
                      <?php
                    }
                    ?>
                    </div>
                  </div>
                  <?php
                }
                else
                {
                  ?>
                  <div class="col-12 text-center">
                    <h4>Patients Not Found.</h4>
                  </div>
                  <?php
                }
                ?>
              </div>
                  <div class="col-12">
                    <div class="row">
                      <div class="col-md-6">
                        <p class="my-3 text-primary">Showing <b><?=$per_page?></b> of <b><?=$count?></b> Entries</p>
                      </div>
                      <div class="col-md-6">
                        <p class="pagination pull-right"><?=$links?></p>
                      </div>
                    </div>
                  </div>
                </div>

              </div>

              

            </div>
          </div>                   


        </section>  

<!-- <script>
        $('.edit-row').click(function () {
            var id = $(this).data('row-id');
            var url = 'http://appointo.froid.works/account/booking-times/:id/edit';
            url = url.replace(':id', id);

            $('#modelHeading').html('Edit Booking Times');
            $.ajaxModal('#application-modal', url);
        });

   $.ajaxModal = function (selector, url, onLoad) {

        $(selector).removeData('bs.modal').modal({
            show: true
        });
        $(selector + ' .modal-content').removeData('bs.modal').load(url);

        // Trigger to do stuff with form loaded in modal
        $(document).trigger("ajaxPageLoad");

        // Call onload method if it was passed in function call
        if (typeof onLoad != "undefined") {
            onLoad();
        }

        // Reset modal when it hides
        $(selector).on('hidden.bs.modal', function () {
            $(this).find('.modal-body').html('Loading...');
            $(this).find('.modal-footer').html('<button type="button" data-dismiss="modal" class="btn dark btn-outline">Cancel</button>');
            $(this).data('bs.modal', null);
        });
    };
    </script> -->

    <script type="text/javascript">

      $(document).ready(function(){
        $('.dataTable').dataTable({
          "paging":false,
          "searching":true
        });
      });
    </script>
