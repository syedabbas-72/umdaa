<style type="text/css">
  .highcharts-credits {
    display: none;
  }
  #overall-chart-container {
    height: 500px; 
    min-width: 310px; 
    max-width: 800px;
    margin: 0 auto;
}
text.highcharts-title
{
  font-size: 16px !important;
  margin-bottom: 10px !important;
  color: #10367a !important;
}
</style>

<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?=$this->session->userdata('clinic_name')?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li class="active">Reports</li>
        </ol>
    </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="row">
      <div class="col-md-12">
        <div class="row">
          <div class="col-md-2 pr-0">
            <label class="col-form-label">Select Doctor</label>
            <select class="form-control" id="doctor">
              <option selected="" disabled="" value="">Select Doctor</option>
              <!-- <option value="all">All Doctors</option> -->
               <?php
               foreach ($doctors as $value) {
                 ?>
                 <option value="<?=$value->doctor_id?>"><?="Dr. ".ucwords(strtolower($value->first_name))." ".ucwords(strtolower($value->last_name))?></option>
                 <?php
               }
               ?>              
            </select>
          </div>
          <div class="col-md-2 pr-0">
            <label class="col-form-label">Select Parameter</label>
            <select class="form-control" id="parameter">
              <option selected="" disabled="">Select Parameter</option>  
              <option value="all">All Parameters</option>
              <option value="finances">Overall Finances</option>
              <option value="appointments">Appointments</option>
              <option value="patients">Patients</option>
              <option value="patientsLocation">Location Wise Patients</option>
              <option value="pharmacyRevenue">Pharmacy Revenue</option>
              <option value="lostPatients">Follow Up Appointments</option>
            </select>
          </div>
          <div class="col-md-3 pr-0">
            <label class="col-form-label">Select Date Range</label>
             <div class="btn btn-primary btn-xs" id="daterange" style="cursor: pointer; padding: 5px 10px; width: 100%;">
                <i class="fa fa-calendar-alt"></i>&nbsp;
                <span></span> <i class="fa fa-caret-down"></i>
                <input class="startDate" type="hidden"> 
                <input class="endDate" type="hidden"> 
            </div>
          </div>
          <div class="col-md-3 pr-0">
            <label class="col-form-label">Trends Interval Period</label>
            <select class="form-control interval_period">
              <option selected="" disabled="">Select Interval Period</option>
              <option value="Weekly">Weekly</option>
              <option value="Monthly">Monthly</option>
              <option value="Quarterly">Quarterly</option>
              <option value="Half-Yearly">Half-Yearly</option>
              <option value="Annually">Annually</option>
            </select>
          </div>
          <div class="col-md-2 mt-1">
            <button class="btn btn-app mt-4" id="getParam"><i class="fa fa-search"></i></button>
          </div>
        </div>
      </div>
    </div>
</div>
</div>

<div class="row mt-2">
  <div class="col-md-12">
        <div class="row graphs_area">
          <div class="col-md-6 mb-3">
            <div id="appointments_pie" style="width: 100%;"></div>
          </div>
          <div class="col-md-6 mb-3">
            <div id="appointments_trends" style="width: 100%;"></div>
          </div>
          <div class="col-md-6 mb-3">
            <div id="finances_pie" style="width: 100%;"></div>
          </div>
          <div class="col-md-6 mb-3">
            <div id="finances_trends" style="width: 100%;"></div>
          </div>
          <div class="col-md-6 mb-3">
            <div id="patients_pie" style="width: 100%;"></div>
          </div>
          <div class="col-md-6 mb-3">
            <div id="patients_trends" style="width: 100%;"></div>
          </div>
          <div class="col-md-6 mb-3">
            <div id="pharmacy_pie" style="width: 100%;"></div>
          </div>
          <div class="col-md-6 mb-3">
            <div id="pharmacy_trends" style="width: 100%;"></div>
          </div>
          <div class="col-md-12 mb-3">
            <div id="locations_patients_pie" style="width: 100%;"></div>
          </div>
          <div class="col-md-12 mb-3" id="patientsLost"></div>
          <!-- <div class="col-md-6 mb-3">
            <div id="pharmacy_pie" style="width: 100%;"></div>
          </div> -->
      </div>
      <div class="row col-md-12 graphs_area">
      <div class="card col-md-12 appointmentsCard">
        <table class="table table-bordered customTable" id="appointmentsTable">
          <thead>
            <tr>
              <th colspan="5" class="text-center appointmentsHeader"></th>
            </tr>
            <tr>
              <th>Sno</th>
              <th>Doctor</th>
              <th>New</th>
              <th>Followup</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody class="tbody">
            
          </tbody>
        </table>
      </div>
      <div class="col-md-12 card financesCard">
        <table class="table table-bordered customTable" id="financesTable">
          <thead>
            <tr>
              <th colspan="5" class="text-center financesHeader"></th>
            </tr>
            <tr>
              <th>Sno</th>
              <th>Type</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody class="tbody">
            
          </tbody>
        </table>
      </div>
      <div class="col-md-12 card patientsCard">
        <table class="table table-bordered customTable" id="patientsTable">
          <thead>
            <tr>
              <th colspan="5" class="text-center patientsHeader"></th>
            </tr>
            <tr>
              <th>Sno</th>
              <th>Type</th>
              <th>No of Patients</th>
            </tr>
          </thead>
          <tbody class="tbody">
            
          </tbody>
        </table>
      </div>
      <div class="col-md-12 card patientsLocationCard">
        <table class="table table-bordered customTable" id="patientslocationTable">
          <thead>
            <tr>
              <th colspan="5" class="text-center patientslocationHeader"></th>
            </tr>
            <tr>
              <th>Sno</th>
              <th>Location</th>
              <th>No of Patients</th>
            </tr>
          </thead>
          <tbody class="tbody">
            
          </tbody>
        </table>
      </div>
    </div>
    </div>
  </div>

  <!-- SMS Modal Loading -->
  <div class="modal fade" id="smsModal">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content">
        <div class="modla-body">
          <h4 class="text-center">Sending SMS <i class="fa fa-spinner fa-spin"></i></h4>
        </div>
      </div>
    </div>
  </div>
  <!-- Loading Data -->
  <div class="modal fade" id="dataLoadingModal">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content">
        <div class="modla-body">
          <h4 class="text-center">Loading Data <i class="fa fa-spinner fa-spin"></i></h4>
        </div>
      </div>
    </div>
  </div>
  
<!-- <script src="<?php echo base_url(); ?>assets/plugins/highstock.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/highcharts.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/highcharts-3d.js"></script> -->
<!-- <script src="<?php echo base_url(); ?>assets/js/Chart.js"></script> -->

<script src="<?php echo base_url(); ?>assets/plugins/moment/moment.min.js" ></script>
<!-- <link href="<?php echo base_url(); ?>assets/css/Chart.css" rel="stylesheet"> -->
<!-- Charts Js -->
<script src="<?php echo base_url(); ?>assets/plugins/charts/highcharts.js"></script>
<!-- <script src="<?php echo base_url(); ?>assets/plugins/charts/modules/exporting.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/charts/modules/export-data.js"></script> -->
<!-- Charts Js Ends -->
<link href="<?php echo base_url(); ?>assets/newdaterangepicker24Jul2019/daterangepicker.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url(); ?>assets/newdaterangepicker24Jul2019/daterangepicker.js"></script>
<script type="text/javascript">
  $(document).ready(function(){
      $('.finLoader').hide();  
      $('.labelLoader').hide();  
      $('#appointmentsTable').hide(); 
      $('#patientsLost').hide();
      $('.appointmentsCard').hide();
      $('.financesCard').hide();
      $('.patientsCard').hide();
      $('.patientsLocationCard').hide();
      // $('.table').DataTable();
  });
</script>
<script type="text/javascript">
  $(document).ready(function(){
    $('#getParam').on("click",function(){
      // $('#getParam').html('Loading <i class="fa fa-spinner fa-spin"></i>');
      var param = $('#parameter').val();
      var doctor_id = $("#doctor").val();
      var ChartModel = $('#parameter :selected').text();
      var startDate = $('.startDate').val();
      var endDate = $('.endDate').val();
      var docStr = "["+$('#doctor option:selected').html()+"]";
      var interval = $('.interval_period').val();
      // alert(param);
      var names = '';
      var colors = '';
      var values = '';

      if(doctor_id==null || doctor_id=='')
      {
        alert("Please Select Doctor");
        $('#getParam').html('<i class="fa fa-search"></i>');
      }
      else if(param==null || param=='')
      {
        alert("Please Select Parameter");
        $('#getParam').html('<i class="fa fa-search"></i>');
      }
      else
      {
        // $('#dataLoadingModal').modal('show');
        $('.labelLoader').show();
        $('.graphs_area div div').empty();
        if(param == "appointments")
        {
          getAppointments();
          appointmentsTrends();
       
          $('#dataLoadingModal').modal('hide');  
          $('.appointmentsCard').show();
          $('.financesCard').hide();
          $('.patientsCard').hide();
          $('.patientsLocationCard').hide();
          $('#patientsLost').hide();
        }
        else if(param == "finances")
        {
          getFinances();
          financesTrends();
       
          $('#dataLoadingModal').modal('hide');  
          $('.appointmentsCard').hide();
          $('.financesCard').show();
          $('.patientsCard').hide();
          $('.patientsLost').hide();
          $('.patientsLocationCard').hide();
        }
        else if(param == "patients")
        {
          getPatients();
          patientsTrends();
       
          $('#dataLoadingModal').modal('hide');  
          $('.appointmentsCard').hide();
          $('.financesCard').hide();
          $('.patientsCard').show();
          $('.patientsLocationCard').hide();
          $('#patientsLost').hide();
        }
        else if(param == "patientsLocation")
        {
          getPatientsLocation();
       
          $('#dataLoadingModal').modal('hide');  
          $('.appointmentsCard').hide();
          $('.financesCard').hide();
          $('.patientsCard').hide();
          $('.patientsLocationCard').show();
          $('#patientsLost').hide();
        }
        else if(param == "pharmacyRevenue")
        {
          getPharmacyFinances();
          PharmacyTrends();
        }
        else if(param == "lostPatients")
        {
          getLostAppointments();
          
          $('#dataLoadingModal').modal('hide');  
          $('.appointmentsCard').hide();
          $('.financesCard').hide();
          $('.patientsCard').hide();
          $('.patientsLocationCard').hide();
          $('#patientsLost').show();
        }
        else if(param == "all")
        {
          $("#dataLoadingModal").modal("hide");
          getAppointments();
          appointmentsTrends();
          getLostAppointments();
          getPharmacyFinances();
          PharmacyTrends();
          getFinances();
          financesTrends();
          getPatients();
          patientsTrends();
          getPatientsLocation();
          $('.appointmentsCard').show();
          $('#patientsLost').show();
          $('.financesCard').show();
          $('.patientsCard').show();
          $('.patientsLocationCard').show();
        }
        
      }
      

    });
  });
</script>

<script type="text/javascript">
  //Patients Trends
  function patientsTrends(){
  var param = $('#parameter').val();
  var doctor_id = $("#doctor").val();
  var ChartModel = $('#parameter :selected').text();
  var startDate = $('.startDate').val();
  var endDate = $('.endDate').val();
  var docStr = "["+$('#doctor option:selected').html()+"]";
  var interval = $('.interval_period').val();
    $.post("<?=base_url('Reports/getPatientsTrends')?>",{doctor_id:doctor_id,param:param,interval_period:interval,startDate:startDate,endDate:endDate},function(data){
      console.log(data)
      var data = data.split("*NV$");
      $('#myChart').empty();
      var ChartName = "<b>Patients From <span class='text-danger'>"+new Date(startDate).toDateString("MM dd YY")+"</span> To <span class='text-danger'>"+new Date(endDate).toDateString("MM dd YY")+"</b>";
      var xvalues = $.trim(data[0]);
      var json = $.trim(data[1]);
      $('#getParam').html('<i class="fa fa-search"></i>');
      var selector = "patients_trends";
      var colors = ['#008976','#F4511E','#6D4C41','#fdd835'];
      drawColumn(ChartName,json,ChartModel,xvalues,selector,colors);
      $('.labelLoader').hide();
    });
    return 1;
  }
  
  // Appointments Trends
  function getLostAppointments(){
    var param = $('#parameter').val();
    var doctor_id = $("#doctor").val();
    var ChartModel = $('#parameter :selected').text();
    var startDate = $('.startDate').val();
    var endDate = $('.endDate').val();
    var interval = $('.interval_period').val();
    $.post("<?=base_url('Reports/getLostAppointments')?>",{doctor_id:doctor_id,param:param,interval_period:interval,startDate:startDate,endDate:endDate},function(data){
      console.log(data);
      $('#getParam').html('<i class="fa fa-search"></i>');
      $('#patientsLost').html(data);
    });
    return 1;
  }

  //Get Finances Trends
  function financesTrends(){
    var param = $('#parameter').val();
    var doctor_id = $("#doctor").val();
    var ChartModel = $('#parameter :selected').text();
    var startDate = $('.startDate').val();
    var endDate = $('.endDate').val();
    var docStr = "["+$('#doctor option:selected').html()+"]";
    var interval = $('.interval_period').val();
    $.post("<?=base_url('Reports/getFinancesTrends')?>",{doctor_id:doctor_id,param:param,interval_period:interval,startDate:startDate,endDate:endDate},function(data){
      console.log(data)
      var data = data.split("*NV$");
      $('#myChart').empty();
      var ChartName = "<b>Finances From <span class='text-danger'>"+new Date(startDate).toDateString("MM dd YY")+"</span> To <span class='text-danger'>"+new Date(endDate).toDateString("MM dd YY")+"</b>";
      var xvalues = $.trim(data[0]);
      var json = $.trim(data[1]);
      // console.log($.parseJSON(data[1]))
      $('#getParam').html('<i class="fa fa-search"></i>');
      var selector = "finances_trends";
      var colors = ['#6d4c41','#757575','#039BE5','#f50057','#a01e20','#3949AB'];
      drawColumn(ChartName,json,ChartModel,xvalues,selector,colors);
      $('.labelLoader').hide();
    });
    return 1;
  }

  // Appointments Trends
  function appointmentsTrends(){
  var param = $('#parameter').val();
  var doctor_id = $("#doctor").val();
  var ChartModel = $('#parameter :selected').text();
  var startDate = $('.startDate').val();
  var endDate = $('.endDate').val();
  var docStr = "["+$('#doctor option:selected').html()+"]";
  var interval = $('.interval_period').val();
    $.post("<?=base_url('Reports/getAppointmentsTrends')?>",{doctor_id:doctor_id,param:param,interval_period:interval,startDate:startDate,endDate:endDate},function(data){
      console.log(data);
      var data = data.split("*NV$");
      $('#myChart').empty();
      var ChartName = "<b>Appointments From <span class='text-danger'>"+new Date(startDate).toDateString("MM dd YY")+"</span> To <span class='text-danger'>"+new Date(endDate).toDateString("MM dd YY")+"</b>";
      var xvalues = $.trim(data[0]);
      var json = $.trim(data[1]);
      $('#getParam').html('<i class="fa fa-search"></i>');
      var selector = "appointments_trends";
      var colors = ['#5c35b1','#1e88e5','#a01e20'];
      drawColumn(ChartName,json,ChartModel,xvalues,selector,colors);
      $('.labelLoader').hide();
    });
    return 1;
  }


  // Get Appointments
  function getAppointments(){
  var param = $('#parameter').val();
  var doctor_id = $("#doctor").val();
  var ChartModel = $('#parameter :selected').text();
  var startDate = $('.startDate').val();
  var endDate = $('.endDate').val();
  var docStr = "["+$('#doctor option:selected').html()+"]";
  var interval = $('.interval_period').val();
    $.post("<?=base_url('Reports/getAppointments')?>",{doctor_id:doctor_id,param:param,startDate:startDate,endDate:endDate},function(data){
      var data = data.split("*NV$");
      $('#myChart').empty();
      $('.appointmentsHeader').html("Appointments Booked From <span class='text-danger'>"+new Date(startDate).toDateString("MMM dd YYYY")+"</span> To <span class='text-danger'>"+new Date(endDate).toDateString("MMM dd YYYY")+"</span> ");
      $('#appointmentsTable .tbody').html(data[0]);
      var ChartName = "<b>Appointments From <span class='text-danger'>"+new Date(startDate).toDateString("MM dd YY")+"</span> To <span class='text-danger'>"+new Date(endDate).toDateString("MM dd YY")+"</b>";
      var json = $.trim(data[1]);
      $('#getParam').html('<i class="fa fa-search"></i>');
      var selector = "appointments_pie";
      var colors = ['#5c35b1','#1e88e5'];
      drawPie(ChartName,json,ChartModel,selector,colors);
      $('.labelLoader').hide();
      $('#appointmentsTable').show();
    });
    return 1;
  }
  
  // Pharmacy Finances Trends
  function PharmacyTrends(){
  var param = $('#parameter').val();
  var doctor_id = $("#doctor").val();
  var ChartModel = $('#parameter :selected').text();
  var startDate = $('.startDate').val();
  var endDate = $('.endDate').val();
  var docStr = "["+$('#doctor option:selected').html()+"]";
  var interval = $('.interval_period').val();
    $.post("<?=base_url('Reports/getPharmacyTrends')?>",{doctor_id:doctor_id,param:param,interval_period:interval,startDate:startDate,endDate:endDate},function(data){
      console.log(data);
      var data = data.split("*NV$");
      $('#myChart').empty();
      var ChartName = "<b>Pharmacy Finances From <span class='text-danger'>"+new Date(startDate).toDateString("MM dd YY")+"</span> To <span class='text-danger'>"+new Date(endDate).toDateString("MM dd YY")+"</b>";
      var xvalues = $.trim(data[0]);
      var json = $.trim(data[1]);
      $('#getParam').html('<i class="fa fa-search"></i>');
      var selector = "pharmacy_trends";
      var colors = ['#3949AB','#8E24AA','#e53935','#1E88E5','#00897B','#FB8C00'];
      drawColumn(ChartName,json,ChartModel,xvalues,selector,colors);
      $('.labelLoader').hide();
    });
    return 1;
  }
  

  // Get Pharmacy Finances
  function getPharmacyFinances(){
  var param = $('#parameter').val();
  var doctor_id = $("#doctor").val();
  var ChartModel = $('#parameter :selected').text();
  var startDate = $('.startDate').val();
  var endDate = $('.endDate').val();
  var docStr = "["+$('#doctor option:selected').html()+"]";
  var interval = $('.interval_period').val();
    $.post("<?=base_url('Reports/getPharmacyFinances')?>",{doctor_id:doctor_id,param:param,startDate:startDate,endDate:endDate},function(data){
      console.log(data)
      // var data = data.split("*NV$");
      $('#myChart').empty();
      $('.appointmentsHeader').html("Appointments Booked From <span class='text-danger'>"+new Date(startDate).toDateString("MMM dd YYYY")+"</span> To <span class='text-danger'>"+new Date(endDate).toDateString("MMM dd YYYY")+"</span> ");
      var ChartName = "<b>Pharmacy Finances From <span class='text-danger'>"+new Date(startDate).toDateString("MM dd YY")+"</span> To <span class='text-danger'>"+new Date(endDate).toDateString("MM dd YY")+"</b>";
      var json = $.trim(data);
      $('#getParam').html('<i class="fa fa-search"></i>');
      var selector = "pharmacy_pie";
      var colors = ['#3949AB','#8E24AA','#e53935','#1E88E5','#00897B','#FB8C00'];
      drawPie(ChartName,json,ChartModel,selector,colors);
      $('.labelLoader').hide();
    });
    return 1;
  }
  
  // Get Patients
  function getPatients(){
  var param = $('#parameter').val();
  var doctor_id = $("#doctor").val();
  var ChartModel = $('#parameter :selected').text();
  var startDate = $('.startDate').val();
  var endDate = $('.endDate').val();
  var docStr = "["+$('#doctor option:selected').html()+"]";
  var interval = $('.interval_period').val();
    $.post("<?=base_url('Reports/getPatients')?>",{doctor_id:doctor_id,param:param,interval_period:interval,startDate:startDate,endDate:endDate},function(data){
      console.log(data)
      var data = data.split("*NV$");
      $('#myChart').empty();
      $('.patientsHeader').html("Patients From <span class='text-danger'>"+new Date(startDate).toDateString("MMM dd YYYY")+"</span> To <span class='text-danger'>"+new Date(endDate).toDateString("MMM dd YYYY")+"</span> ");
      $('#patientsTable .tbody').html(data[0]);
      var ChartName = "<b>Patients From <span class='text-danger'>"+new Date(startDate).toDateString("MM dd YY")+"</span> To <span class='text-danger'>"+new Date(endDate).toDateString("MM dd YY")+"</b>";
      var json = data[2];
      var Xvalues = data[1];
      $('#getParam').html('<i class="fa fa-search"></i>');
      var selector = "patients_pie";
      var colors = ['#008976','#F4511E','#6D4C41','#fdd835'];
      drawPie(ChartName,json,ChartModel,selector,colors);
      $('.labelLoader').hide();
    });
    return 1;
  }
  //Get Finances
  function getFinances(){
  var param = $('#parameter').val();
  var doctor_id = $("#doctor").val();
  var ChartModel = $('#parameter :selected').text();
  var startDate = $('.startDate').val();
  var endDate = $('.endDate').val();
  var docStr = "["+$('#doctor option:selected').html()+"]";
  var interval = $('.interval_period').val();
    $.post("<?=base_url('Reports/getFinances')?>",{doctor_id:doctor_id,param:param,interval_period:interval,startDate:startDate,endDate:endDate},function(data){
      console.log(data)
      var data = data.split("*NV$");
      $('.financesHeader').html("Finances From <span class='text-danger'>"+new Date(startDate).toDateString("MMM dd YYYY")+"</span> To <span class='text-danger'>"+new Date(endDate).toDateString("MMM dd YYYY")+"</span> ");
      $('#financesTable .tbody').html(data[0]);
      $('#myChart').empty();
      var ChartName = "<b>Finances From <span class='text-danger'>"+new Date(startDate).toDateString("MM dd YY")+"</span> To <span class='text-danger'>"+new Date(endDate).toDateString("MM dd YY")+"</b>";
      var json = data[1];
      var Xvalues = data[2];
      $('#getParam').html('<i class="fa fa-search"></i>');
      var selector = "finances_pie";
      var colors = ['#6d4c41','#757575','#039BE5','#f50057','#a01e20','#3949AB'];
      drawPie(ChartName,json,ChartModel,selector,colors);
      // drawTrends(ChartName,Xvalues,json)
      $('.labelLoader').hide();              
    });
    return 1;
  }
  // Get Patients Location Wise
  function getPatientsLocation(){
  var param = $('#parameter').val();
  var doctor_id = $("#doctor").val();
  var ChartModel = $('#parameter :selected').text();
  var startDate = $('.startDate').val();
  var endDate = $('.endDate').val();
  var docStr = "["+$('#doctor option:selected').html()+"]";
  var interval = $('.interval_period').val();
    $.post("<?=base_url('Reports/getLocationPatients')?>",{doctor_id:doctor_id,param:param,interval_period:interval,startDate:startDate,endDate:endDate},function(data){
      console.log(data)
      var data = data.split("*NV$");
      $('#myChart').empty();
      $('.patientslocationHeader').html("Location Wise Patients From <span class='text-danger'>"+new Date(startDate).toDateString("MMM dd YYYY")+"</span> To <span class='text-danger'>"+new Date(endDate).toDateString("MMM dd YYYY")+"</span> ");
      $('#patientslocationTable .tbody').html(data[0]);
      var ChartName = "<b>Location Wise From <span class='text-danger'>"+new Date(startDate).toDateString("MM dd YY")+"</span> To <span class='text-danger'>"+new Date(endDate).toDateString("MM dd YY")+"</b>";
      var json = data[2];
      var Xvalues = data[1];
      $('#getParam').html('<i class="fa fa-search"></i>');
      var selector = "locations_patients_pie";
      var colors = ['#008976','#F4511E','#6D4C41','#fdd835','#039BE5','#8E24AA'];
      drawPie(ChartName,json,ChartModel,selector,colors);
      $('.labelLoader').hide();
    });
    return 1;
  }



</script>

<script type="text/javascript">
  function drawPie(ChartName,json,ChartModel,selector,colors){
     // Build the chart
     $('#'+selector).empty();
      console.log(JSON.parse(json));
      Highcharts.chart(selector, {
          chart: {
              plotBackgroundColor: null,
              plotBorderWidth: null,
              plotShadow: false,
              type: 'pie'
          },
          colors:colors,
          title: {
              text: ChartName
          },
          tooltip: {
               pointFormat: '{point.name}[{point.y}]: <b>{point.y}</b>'
          },
          plotOptions: {
              pie: {
                  allowPointSelect: true,
                  cursor: 'pointer',
                  dataLabels: {
                      enabled: true
                  },
                  showInLegend: true
              }
          },
          series: [{
              name: ChartModel,
              colorByPoint: true,
              data: $.parseJSON(json)
          }]
      });

  }
  function drawColumn(ChartName,json,ChartModel,Xvalues,selector,colors){
    console.log($.parseJSON(json))
    console.log($.parseJSON(Xvalues))
    Highcharts.chart(selector, {
      title: {
          text: ChartName
      },
      colors:colors,
      xAxis: {
          categories: $.parseJSON(Xvalues)
      },plotOptions: {
        series: {
            minPointLength: 4
        }
    },
      series: $.parseJSON(json)
  });
  }

 
</script>
    <script type="text/javascript">

    </script>
<script type="text/javascript">
    $(function() {

        var start = moment().subtract(6, 'days');
        var end = moment();

        function cb(start, end) {
            $('#daterange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
            
            var start_date = start.format('YYYY-MM-DD');
            var end_date = end.format('YYYY-MM-DD');
            
            $('#daterange .startDate').val(start_date);
            $('#daterange .endDate').val(end_date);    
            $('.finLoader').show();  
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

<script>
  $(document).on("click",".sendsms",function(){
    var id = $(this).attr("id");
    // $(this).html("Sending SMS..");
    
    $('#smsModal').modal();    
    $(this).attr("disabled");
    $.post("<?=base_url('Reports/sendsms')?>",{id:id},function(data){
      console.log(data)
      if(data == 1)
      {
        $(this).html("Send SMS");
        alert("SMS Sent.");
      }
      else
      {
        $(this).html("Send SMS");
        alert("SMS Not Sent");
      }
      
      $('#smsModal').modal('hide');    

    });
  });
</script>