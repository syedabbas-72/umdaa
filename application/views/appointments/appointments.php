<style type="text/css">
	.bg-light-gradient {
    background: #f8f9fa;
    background: -o-linear-gradient(white, #f8f9fa);
    color: #1F2D3D;
}
.page-footer{
    display: none;
}
.booking-time {
    font-weight: 500;
    font-size: 1rem;
}
.booking-time .badge {
    font-size: 90%;
}
.ml-0, .mx-0 {
    margin-left: 0 !important;
}
.mr-0, .mx-0 {
    margin-right: 0 !important;
}
.rounded {
    border-radius: 0.25rem !important;
}
.mb-3, .small-box, .card, .info-box, .callout, .my-3 {
    margin-bottom: 1rem !important;
}
table.dataTable {
    clear: both;
    margin-top: 6px !important;
    margin-bottom: 6px !important;
    max-width: none !important;
    border-collapse: separate !important;
    border-spacing: 0;
}
.img-size-100 {
    width: 100px;
}
button.view-booking-detail {
	box-shadow: none !important;
	overflow: visible !important;
}
.bg-secondary, .bg-secondary a {
    color: #ffffff !important;
}
.btn-outline-primary {
    color: #007bff;
    background-color: transparent;
    background-image: none;
    border-color: #007bff;
}
.btn-outline-dark {
    color: #343a40;
    background-color: transparent;
    background-image: none;
    border-color: #343a40;
}
.btn-outline-danger {
    color: #dc3545;
    background-color: transparent;
    background-image: none;
    border-color: #dc3545;
}
.border-dark {
    border-color: #ffffff !important;
}
.badge{
	background: none !important;
}
.bg-light, .bg-light a {
    color: #1F2D3D !important;
}
.view-booking-detail {
    font-size: 1.8rem;
}
.ticking label
{
    font-size: 16px;
}
</style>
<div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <ol class="breadcrumb page-breadcrumb">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?=$this->session->userdata('clinic_name')?></a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li class="active">Appointments</li>
                            </ol>
                        </div>
                    </div>


         
            <div class="card">
                <div class="card-body">
                            <div class="row">
                              <div class="col-md-12 ">
                                <button class="btn btn-app btn-xs pull-right" style="margin-top: 15px">
                                    <div id="daterange" style="cursor: pointer; padding: 5px 10px; width: 100%;">
                                        <i class="fa fa-calendar-alt"></i>&nbsp;
                                        <span></span> <i class="fa fa-caret-down"></i> <i class="fas fa-spinner fa-spin bill_loader"></i>
                                        <input class="startDate" type="hidden"> 
                                        <input class="endDate" type="hidden"> 
                                    </div>
                                </button>
                              </div>
                            </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered dt-responsive dataTable customTable" id="appointmentList">
                                <thead>
                                  <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 15%">Check In Time</th>
                                    <th style="width: 20%">Patient Name - UMR No.</th>
                                    <th style="width: 20%">Doctor</th>
                                    <th style="width: 15%">Mobile No.</th>
                                    <th style="width: 15%">Status</th>
                                    <th style="width: 10%">Actions</th>
                                  </tr>
                                </thead>
                                <tbody class="appointments_body">
                                </tbody>
                              </table>

                </div>
            </div></div></div>
            <script type="text/javascript">
                $(document).on("click","#res_div",function(){
        $(".reschedule_div").show();
        
    });

    $(document).on("click","#check_btn",function(){
        var sel_date = $("input[name=date]").val();
        //var sel_date = document.getElementById("solo_date").value;
        var doctor_id = $(this).attr("did");
        //$("#solo_date").val('test');
        console.log(sel_date);
        checkslots(doctor_id,sel_date);
        return false;

    });

    function checkslots(d_id,date){

        var current_time = '<?php echo date('H:i'); ?>';
  
        $.ajax({
            type: "POST",
            url: '<?php echo base_url(); ?>calendar_view/check_slot',
            data:{ did:d_id,date:date},
            success: function(result){
                result = $.trim(result);
                //console.log(result);
                if(result == "no"){
                    var slot_html = "<option value=''>NA</option>";
                    $("#slots").html(result);
                }else{
                    var slot_html = result;
                }
                $("#slots").html(slot_html);
            }                    
        });
    }



  $( function() {
 
    $( "#date_of_birth" ).datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat: "dd-mm-yy",
      minDate: 0
   
      
  });
});
            </script>



<script src="<?php echo base_url(); ?>assets/plugins/moment/moment.min.js" ></script>
<link href="<?php echo base_url(); ?>assets/newdaterangepicker24Jul2019/daterangepicker.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url(); ?>assets/newdaterangepicker24Jul2019/daterangepicker.js"></script>
<script type="text/javascript">

    $('.bill_loader').hide();
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

            $.post("<?=base_url('Appointment/getAppointments')?>",
            {
                startDate:start_date,
                endDate:end_date
            },
            function(data){
                console.log(data)
                $('.bill_loader').hide();
                $('#appointmentList').DataTable().destroy();
                $('#appointmentList tbody').html(data);
                $('#appointmentList').DataTable();
            });
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
