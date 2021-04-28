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
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li>Patient&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Prescriptions</li>
        </ol>
    </div>
</div>

<!-- View Prescription Modal -->
<div id="viewPrescriptionModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-fluid" style="max-width: 85% !important">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">View Prescription</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        
        <h4 class="mt-0 mb-1">Patient Details</h4>
        <a href="#" class="btn btn-primary pull-right cartLink"><i class="fa fa-shopping-basket"></i> GO TO ORDER PAGE</a>
        <div class="row">
            <div class="col-md-6">
               <img class="qrcode pull-left" src="<?php echo base_url('uploads/qrcodes/patients/'.$patient_dt->qrcode); ?>" style="margin: 0px !important">
               <label class="patient_name mt-2 font-weight-bold text-primary">Patient Name</span>&emsp;<span class="appbadge p-1 umr_no"></label>
               <p class="address">Address</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table customTable">
                  <thead>
                      <tr>
                        <th>#</th>
                        <th>Medicine</th>
                        <th>Frequency</th>
                        <th>Duration</th>
                        <th>Qty</th>
                        <th>Remarks</th>
                      </tr>
                  </thead>
                  <tbody class="prescriptionTbody">
                    <tr>
                        <td colspan="6">
                           <h5 class="text-center">Getting Prescription <i class="fa fa-spinner fa-spin"></i></h5> 
                        </td>
                    </tr>
                  </tbody>
                </table>
            </div>
        </div>
      </div>
    </div>

  </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body"> 
                <div class="row">
                  <div class="col-12">
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
                    <div class="col-12">
                        <table id="prescriptions" class="table table-bordered  customTable">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Patient Info</th>
                                    <th>Appointment Info</th>
                                    <th>Prescription Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="prescriptionListBody">
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

            $.post("<?=base_url('Pharmacy_prescription/getPrescriptionsList')?>",
            {
                startDate:start_date,
                endDate:end_date
            },
            function(data){
                $('.finLoader').hide();  
                $('.bill_loader').hide();   
                $('#prescriptions').dataTable().fnDestroy();
                $('#prescriptions tbody').html(data);
                $('#prescriptions').DataTable(); 
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

<script type="text/javascript">
    // $(document).ready(function(){
       $(document).on("click", ".viewPrescription",function(){  
            var prescriptionId = $(this).attr("id");
          
            $.post("<?=base_url('pharmacy_prescription/getPrescription')?>",{prescriptionId:prescriptionId},function(data){
                console.log(data);
                var data = data.split("*$");
                var jsonData = JSON.parse(data[0]);
                $('.patient_name').html(jsonData.patient_name);
                $('.address').html(jsonData.address);
                $('.umr_no').html(jsonData.umr_no);
                $('.qrcode').attr("src","<?php echo base_url('uploads/qrcodes/patients/')?>"+jsonData.qrcode);
                $('.cartLink').attr("href",jsonData.cartLink);
                $('.prescriptionTbody').html(data[1]);
            });
        });
    // });
</script>