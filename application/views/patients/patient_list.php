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
                      <a class="nav-link active" href="<?=base_url('Patients/getTodayAppointments')?>">Today Appointments</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="<?=base_url('Patients/getCitizens')?>">From Citizens</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="<?=base_url('Patients/getDoctors')?>">From Doctors</a>
                    </li>
                  </ul>
                </div>

                <div class="col-md-12">
                  <div class="row">
                    <div class="col-12">
                      <!-- <h4 class="page-title">TODAY APPOINTMENTS</h4> -->
                      <hr>
                      <div class="row">
                        <div class="col-md-6 mb-2">
                          <form method="post" action="<?=base_url('Patients/AppointmentsSearch')?>">
                            <div class="form-group d-flex">
                              <input class="form-control mr-3" type="text" placeholder="Search with Mobile Number / Name" name="search" required>
                              <button class="btn btn-app" type="submit" name="appointmentsSearch"><i class="fa fa-search"></i></button>
                            </div>
                          </form>
                          <form method="post" action="<?=base_url('Patients/AppointmentsSearch')?>" id="form">
                            <div class="form-group">
                              <input type="checkbox" id="walkin" value="walkin" name="booking_type[]" class="booking_type">&nbsp;
                              <label for="walkin">Walkin</label>&emsp;&emsp;
                              <input type="checkbox" id="phone"  value="phone" name="booking_type[]" class="booking_type">&nbsp;
                              <label for="phone">Phone</label>&emsp;&emsp;
                              <input type="checkbox" id="video_call" value="video call"  name="booking_type[]" class="booking_type">&nbsp;
                              <input type="hidden" name="type" value="filters">
                              <label for="video_call">Video Call</label>
                              <div class="float-right">
                                <button class="btn btn-primary btn-sm text-right" name="appointmentsSearch" type="submit">SUBMIT</button>
                              </div>
                            </div>
                            
                          </form>
                        </div>
                        <div class="col-md-6 mb-2">
                          <div class="row">
                            <div class="col-md-6">
                              <p class="my-3 text-primary">Showing <b><?=$per_page?></b> of <b><?=$count?></b> Entries</p>
                            </div>
                            <div class="col-md-6">
                              <p class="pagination pull-right"><?=$links?></p>
                            </div>
                          </div>
                        </div>
                        <div class="col-12 my-2">
                          <div class="row">
                           <?php
                            if(count($appointments) > 0){
                              $i = 1;
                              foreach($appointments as $value){
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
                                if($value->booking_type == "Walk-in" || $value->booking_type == "walkin" || $value->booking_type == ""){
                                  $booking_type = "<i class='fas fa-walking'></i> Walkin";
                                }
                                else if($value->booking_type == "phone"){
                                  $booking_type = "<i class='fas fa-phone'></i> Phone";
                                }
                                else if($value->booking_type == "Mobile"){
                                  $booking_type = "<i class='fas fa-phone'></i> Mobile";
                                }
                                else if($value->booking_type == "video call"){
                                  $booking_type = "<i class='fas fa-video'></i> Video Call";
                                }
                                ?>
                                <div class="col-md-4">
                                
                                  <a href="<?=base_url('Profile/index/'.$patientInfo->patient_id."/".$value->appointment_id)?>">
                                  <div class="info-box">
                                    <span class="info-box-icon push-bottom">
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
                                        <div class="col-12">
                                          <p class="m-0 p-0"><i class="fa fa-map-marker-alt"></i> <?=ucwords(strtolower($patientInfo->location))?></p>
                                        </div>
                                      </div>

                                      <p class="m-0 p-0"><b><?=$booking_type?></b>
                                      <span class="badge m-0 bg-primary float-right"><?=strtoupper(str_replace('_',' ',$value->status))?></span></p>
                                      
                                      <div class="mt-2">
                                        <p class="p-0 m-0">
                                        <b class="text-uppercase"><i class="fas fa-user-md"></i> <?=getDoctorName($value->doctor_id)?> @ <span><?=$clinicInfo->clinic_name?></span></b>                                          
                                        <br><b><i class="fas fa-calendar-alt"></i> <?=date('d-m-Y h:i A', strtotime($appdate))?></b>
                                        </p>
                                      </div>
                                    </div>
                                    <!-- /.info-box-content -->
                                  </div>
                                  <!-- /.info-box -->
                                  </a>
                                </div>
                                <?php
                                $i++;
                              }
                            }
                            ?>
                          </div>
                        </div>
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

    <script>
    function cleanArray(actual)
    {
        var newArray = new Array();
        for(var i = 0; i<actual.length; i++)
        {
            if (actual[i])
            {
                newArray.push(actual[i]);
            }
        }
        return newArray;
    }

    $(document).ready(function(){
      $('.booking_type').on("click",function(){
        // $('#form').submit();
      })
    })

    // $(document).ready(function(){
    //   $('.booking_type').on("click",function(){
    //     var checkArray = [];
    //     var i = 0;
    //     $('input:checkbox.booking_type').each(function () {
    //        var checkvalue = (this.checked ? $(this).val() : "");
    //        checkArray[i] = checkvalue 
    //        i++
    //     });
    //     var search = cleanArray(checkArray)
    //     console.log(search)
    //     var data = {search:search,type:'filters'};
    //     $.ajax({
    //       type: "POST",
    //       url: "<?=base_url('Patients/AppointmentsSearch/')?>"+data,
    //       dataType: "html",
    //       success:function(data){
    //         $('html').html(data)
    //       },

    //     });
    //     // $.post("<?=base_url('Patients/AppointmentsSearch')?>", {search:search,type:'filters'},function(data){
          
    //     // })
    //   })

      
    // });
    </script>
