<style>
.select2-container .select2-selection--single
{
  height:auto !important;
  padding: 5px !important;
}
.buttons-csv,.buttons-excel
{
  background: #10365a !important;
  color: #fff !important;
  margin-right: 10px !important;
  border-radius: 4px !important;
}
</style>
<div class="page-bar">
   <div class="page-title-breadcrumb">
      <ol class="breadcrumb page-breadcrumb">
         <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php
            echo base_url("dashboard");
            ?>"><?=$this->session->userdata('clinic_name')?></a>&nbsp;<i class="fa fa-angle-right"></i>
         </li>
         <li><a class="parent-item active" href="<?php
            echo base_url("DocRecords");
            ?>">Excel Reports</a>
         </li>
      </ol>
   </div>
 </div>

 <div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <form class="form-horizontal" method="post" action="<?=base_url('DocRecords/getRecords')?>">
          <div class="row justify-content-center">

            <div class="col-md-4">
              <label class="control-form-label">Doctor</label>
              <select class="form-control" name="doctor" id="doctor" required> 
                <option selected disabled>Select Doctor</option>
                <?php
                foreach($doctors as $value)
                {
                  ?>
                  <option value="<?=$value->doctor_id?>"><?=getDoctorName($value->doctor_id)?></option>
                  <?php
                }
                ?>
              </select>
            </div>

            <div class="col-md-4">
                <label class="control-form-label">Date Range</label>
                <button class="btn btn-app btn-block" type="button">
                    <div id="daterange" style="cursor: pointer; padding: 5px 10px; width: 100%;">
                        <i class="fa fa-calendar-alt"></i>&nbsp;
                        <span></span> <i class="fa fa-caret-down"></i>
                        <input class="startDate" name="startDate" type="hidden"> 
                        <input class="endDate" name="endDate" type="hidden"> 
                    </div>
                </button>
            </div>

            <div class="col-md-4">
                <!-- <p class="m-0">&nbsp;</p> -->
                <label class="control-form-label">&nbsp;</label>
                <button class="btn btn-app btn-block" type="submit" style="padding:10px !important;">Get Records</button>
            </div>

          </div>
        </form>

        <div class="row loadingrow mt-4 d-none">
            <div class="col-12">
                <h4 class="text-center text-danger font-weight-bold text-uppercase">Loading Data Please Wait...
                <i class="fas fa-spinner fa-spin"></i>
                </h4>
            </div>
        </div>

        <div class="row">
          <div class="col-12">
            
            <div class="table-responsive reportsDiv ">
              <table class="customTable" id="ReportsTable">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Name</th>
                      <th>UMR</th>
                      <th>DOB</th>
                      <th>Age</th>
                      <th>Gender</th>
                      <th>Mobile</th>
                      <th>Location</th>
                      <th>Appointment Date</th>
                      <th>Vitals</th>
                      <th>Symptoms</th>
                      <th>Clinical Diagnosis</th>
                      <th>Investigations</th>
                      <th>Prescription</th>
                    </tr>
                  </thead>
                  <tbody id="ReportsBody">
                  <?php 
                  if(isset($appInfo) > 0)
                  {
                    $i = 1;
                    foreach($appInfo as $value)
                    {
                        $patientInfo = getPatientDetails($value->patient_id);
                        // $data[$i]['patientInfo'] = getPatientDetails($value->patient_id);
                        // $vitals = getVitals($value->appointment_id);
                        ?>
                        <tr>
                            <td><?=$i?></td>
                            <td><?=$patientInfo->first_name." ".$patientInfo->last_name?></td>
                            <td><?=$patientInfo->umr_no?></td>
                            <td><?=($patientInfo->dob == "")?'N/A':date(" d M Y", strtotime($patientInfo->dob))?></td>
                            <td><?=($patientInfo->age == "")?'N/A':$patientInfo->age.$patientInfo->age_unit?></td>
                            <td><?=($patientInfo->gender == "")?'N/A':$patientInfo->gender?></td>
                            <td><?=($patientInfo->mobile == "")?'N/A':DataCrypt($patientInfo->mobile, 'decrypt')?></td>
                            <td><?=($patientInfo->location == "")?'N/A':$patientInfo->location?></td>
                            <td><?=date('d-m-Y', strtotime($value->appointment_date))?></td>
                            <td><?=getVitals($value->appointment_id)?></td>
                            <td><?=getSymptoms($value->appointment_id)?></td>
                            <td><?=getClinicalDiagnosis($value->appointment_id)?></td>
                            <td><?=getInvestigations($value->appointment_id)?></td>
                            <td><?=getPrescriptions($value->appointment_id)?></td>
                        </tr>
                        <?php
                        $i++;
                    } 
                  }
                  ?>
                  </tbody>
              </table>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>
 </div>

 <script>
  $(document).ready(function(){
    $('select').select2();
    // $('#ReportsTable').hide();
     var table = $('#ReportsTable').DataTable({
        "paging": false,
        dom: 'Bfrtip',
        buttons: ['csv','excel',]
      });

      // if ( ! table.data().count() ) {
      //     $('#ReportsTable').DataTable().destroy();
      //     $('#ReportsTable').DataTable( {
      //         searching: false
      //     })
      // }
  })
 </script>
 
<script src="<?php echo base_url(); ?>assets/plugins/moment/moment.min.js" ></script>
<link href="<?php echo base_url(); ?>assets/newdaterangepicker24Jul2019/daterangepicker.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url(); ?>assets/newdaterangepicker24Jul2019/daterangepicker.js"></script>
 <script>
  $(function() {

    var start = moment().subtract(0, 'days');
    var end = moment();

    function cb(start, end) {
        $('#daterange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        
        var start_date = start.format('YYYY-MM-DD');
        var end_date = end.format('YYYY-MM-DD');
        
        $('#daterange .startDate').val(start_date);
        $('#daterange .endDate').val(end_date);   
        $('.bill_loader').show();   

        // var doctor = $('#doctor').val();
        // if(doctor!=null)
        // {
          // $('.loadingrow').removeClass('d-none');
          // $.post("<?=base_url('DocRecords/getRecords')?>",
          // {
          //     startDate:start_date,
          //     endDate:end_date,
          //     doctor_id:doctor
          // },
          // function(data){
          //     console.log(data)
          //     // $('#ReportsBody').empty();
          //     $('.reportsDiv').removeClass('d-none');

          //     if ( $.fn.dataTable.isDataTable( '#ReportsTable' ) ) {
          //       console.log('yes')
          //     //   // $('#ReportsTable').DataTable().destroy();
          //       $('#ReportsTable').DataTable({
          //         // "destroy": true,
          //         "paging": false,
          //         dom: 'Bfrtip',
          //         buttons: ['csv','excel',]
          //       });
          //       $('#ReportsBody').html(data);
          //     }
          //     else {
          //       console.log('no')
          //       $('#ReportsTable').DataTable({
          //         "paging": false,
          //         dom: 'Bfrtip',
          //         buttons: ['csv','excel',]
          //       });
          //       $('#ReportsBody').html(data);
          //     }

          //     // $("#ReportsTable").ajax.reload(null, false);
          //     $('.loadingrow').addClass('d-none');
          //     // $('#ReportsTable').DataTable();
              
          // });
        // }
    }

    $('#daterange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);

    cb(start, end);

    });
 </script>