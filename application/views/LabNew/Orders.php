<style>
.modal-content {
    width: 100% !important;
    position: relative !important;
}
.customTable span.amt{
    font-size: 20px !important;
}
.pac-container {
    z-index: 9999999999 !important;
}

.modal-content {
    display: inline-block !important;
    position: relative !important;
    /* border: 1px solid !important; */
    min-width: 370px !important;
}

button:focus {
    outline: none !important;
}
.icon{
    border-right: 1px dotted #ebebeb;
}
.icons i{
    font-size: 18px !important;
    color: #aaa ;
}
.trashicon{
    color: #ff3636 !important
}
.header-btn{
    border-radius: 30px !important;
    padding: 10px !important;
}
.stat-icon{
    float: left;
    padding: 20px;
}
.stat-icon i{
    font-size: 36px !important;
}
</style>

<head>
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
</head>

<script type="text/javascript" src="js/jquery-ui.min.js"></script>

<script type="text/javascript" src="js/jquery-ui.js"></script>

<div class="page-bar">
    <div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
            <ol class="breadcrumb page-breadcrumb pull-left">
                <li class="text-uppercase"><a href="#"><i class="fa fa-home"></i> <?=$this->session->userdata('clinic_name')?> <i class="fa fa-angle-right"></i></a></li>
                <li>LAB ORDERS</li>
            </ol>
        </div>
        <div class="col-lg-6 align-self-center text-right">
            <!-- <button class="btn btn-app header-btn" data-toggle="modal" data-target="#dashboardModal"><i class="fas fa-file-alt"></i> Reports</button> -->
            <button class="btn btn-app header-btn" data-toggle="modal" data-target="#dashboardModal"><i class="fas fa-tachometer-alt"></i> Dashboard</button>
            <button class="btn btn-success header-btn" onclick="makeBillModalEmpty()" data-toggle="modal" data-target="#exampleModal"><i class="fas fa-plus"></i> New Order</button>
        </div>
    </div>
</div>

<div id="osaModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Outstanding Balance</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-weight-bold">Clear Outstanding Balance</label>
                    <input class="form-control" id="osa" readonly disabled type="text" onkeypress="return numeric()">
                    <input type="hidden" id="osa_billing_id">
                </div>
                <div class="form-group text-center">
                    <button class="btn btn-app" id="clearosa">Submit</button>
                </div>
            </div>
        </div>

    </div>
</div>

<div id="invoiceModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Invoices</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="customTable w-100 m-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice No</th>
                            <th>Invoice Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="invoicesTbody">
                        <tr>
                            <td colspan="4">
                                <p class="text-center text-primary" style="padding:0px !important;margin:0px !important">Getting Records. Please Wait....<i class="fas fa-spinner fa-spin"></i></p>            
                            </td>
                        </tr>
                    </tbody>
                </table>
                
            </div>
        </div>

    </div>
</div>

<div id="dashboardModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <div class="container-fluid">
                <h4 class="page-title">
                    <select class="custom-select w-25 btn btn-app " id="reportsType">
                        <option value="dashboard">Dashboard</option>
                        <option value="referrals">Referrals</option>
                        <option value="investigations">Investigations Wise</option>
                    </select>
                    <!-- Dashboard -->
                    <button id="daterange" class="btn btn-app pull-right">
                        <i class="fa fa-calendar-alt"></i>&nbsp;
                        <span style="color:white !important"></span> <i class="fa fa-caret-down"></i>
                        <input class="startDate" type="hidden"> 
                        <input class="endDate" type="hidden"> 
                    </button>
                </h4>
                <div class="row" id="loaderBlock">
                    <div class="col-12 text-center">
                        <h1 class="p-5 text-secondary">Loading Data <i class="fas fa-circle-notch fa-spin"></i></h1>
                    </div>
                </div>
                <!-- DashboardBLock -->
                <div class="row d-none" id="dashboardBlock">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h1 class="stat-icon"><i class="fas fa-vials"></i></h1>
                                <h1 class="text-right invCount">0</h1>
                                <h4 class="text-right">Investigations</h4>
                            </div>
                        </div>
                    </div> 
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h1 class="stat-icon"><i class="fas fa-rupee-sign"></i></h1>
                                <h1 class="text-right collectedAmount">0.00</h1>
                                <h4 class="text-right">Collected Revenue</h4>
                            </div>
                        </div>
                    </div> 
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h1 class="stat-icon"><i class="fas fa-balance-scale"></i></h1>
                                <h1 class="text-right outstandingBal">0.00</h1>
                                <h4 class="text-right">Outstanding Balance</h4>
                            </div>
                        </div>
                    </div> 
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h1 class="stat-icon"><i class="fas fa-percentage"></i></h1>
                                <h1 class="text-right totalDiscount">0.00</h1>
                                <h4 class="text-right">Discounts</h4>
                            </div>
                        </div>
                    </div> 
                </div>
                <!-- DashboardBlock Ends -->

                <!-- Referrals Block -->

                <div class="row" id="referalsBlock">
                    <div class="col-md-12">
                        <table class="customTable w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Referred By</th>
                                    <th>No Of Investigations</th>
                                    <th>Total Revenue</th>
                                    <th>Collected</th>
                                    <th>Outstanding</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center">No Data Available</td>
                                </tr>
                            </tbody>
                        </table> 
                    </div>
                </div>

                <!-- Referrals Block Ends -->

                <!-- Investigations Wise Block -->

                <div class="row" id="investigationsBlock">
                    <div class="col-md-12">
                        <table class="customTable w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Investigation Name</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="3" class="text-center">No Data Available</td>
                                </tr>
                            </tbody>
                        </table> 
                    </div>
                </div>

                <!-- Investigations Wise Block Ends -->

            </div>
           
            </div>
        </div>

    </div>
</div>



<div class="modal fade " id="report_entry_popup" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body" id="entryDiv">
            </div>
        </div>
    </div>
</div>


<div class="modal fade " id="authenticate_popup_entry" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body" id="authenticateDiv">
            </div>
        </div>
    </div>
</div>

<!-- report entry pop-up -->

<!-- Modal -->
<div class="col-md-12 modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 51%;">
        <div class="modal-content">
            <!-- <div class="modal-header">
                
            </div> -->
            <div class="modal-body ui-front">
                <div class="row mb-2">
                    <div class="col-12">
                        <button type="button" class="bg-danger close pull-right rounded-circle text-white" style="height:30px;width:30px;padding:0px !important" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>    
                        <h3 class="text-center">Generate New Order</h3>
                    </div>
                </div>
                
                <div class="row">
                
                    <div class="col-4 mb-2">
                        <label class="font-weight-bold">Mobile Number</label>
                        <input type="text" class="form-control" maxlength="10" onkeypress="return numeric()" oninput="show_inv()" id="mobile">
                    </div>
                    <div class="col-4 mb-2">
                        <label class="font-weight-bold">Customer Name</label>
                        <input type="text" class="form-control" onkeypress="return alpha()" oninput="show_inv()" id="name">
                    </div>
                    <div class="col-4 mb-2">
                        <label class="font-weight-bold">Age</label>
                        <div class="d-flex">
                            <input class="form-control" id="age" onkeypress="return numeric()" maxlength="3">
                            <select class="form-control" id="age_unit">
                                <option selected value="Years">Years</option>
                                <option value="Months">Months</option>
                                <option yyvalue="Days">Days</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-4 mb-2">
                        <label class="font-weight-bold">Gender</label>
                        <select class="form-control" id="gender">
                            <option value="" disabled selected >Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Transgender">Transgender</option>
                        </select>
                    </div>
                    <div class="col-4 mb-2">
                        <label class="font-weight-bold">Email</label>
                        <input type="email" class="form-control" id="email">
                    </div>
                    <div class="col-4 mb-2">
                        <label class="font-weight-bold">Referred By</label>
                        <input type="text" class="form-control" id="referred_by">
                    </div>
                    
                    <!-- this is table -->
                    <div id="investigation_part" style="display: none;" class="col-12">

                        <div class="form-group">
                            <label for="pname" class="font-weight-bold">Add Investigation<span class="imp">*</span></label>
                            <input class="form-control" type="text" autocomplete="off" placeholder="Search by investigation name" id="search_investigations" onkeypress="search_result(this.value)" />
                        </div>
                        <div style="display:none;" id="notice_text">
                            <h4 class="text-warning">this investigaton is not add in your clinic </h4>
                        </div>
                        <div>
                            <table class="customTable mt-0 mb-0" id="added_investigation_table">
                                <thead>
                                    <tr>
                                        <!-- <th>#</th> -->
                                        <th colspan="3">Investigation / Package</th>
                                        <th>Amount</th>
                                        <!-- <th>Price</th> -->
                                    </tr>
                                </thead>
                                <tbody class="addInv_tbody">
                                </tbody>
                            <!-- </table>

                            <table class="mt-0 customTable"> -->
                            <tfoot>
                            <tr>
                                        <!-- <div style="display: none;" id="show_price"> -->
                                            <td colspan="3" class="font-weight-bold text-primary">Total Amount</td>
                                            <td class="font-weight-bold text-primary">
                                                <input class="border-0 font-weight-bold text-primary text-center" type="text" id="total_amount" disabled>
                                                
                                            </td>
                                        <!-- </div> -->
                                    </tr>
                                <tr>
                                    <td style="width:20%">
                                        <label class="font-weight-bold">
                                            Discount in %
                                        </label>
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" oninput="return discount(this.value)"
                                            onkeypress="return numeric()" max="100" name="discount" id="discount">
                                    </td>
                                <!-- </tr>
                                <tr> -->
                                    <td style="width:35%">
                                        <input id="advancePayment" type="checkbox" name="advancePayment" value="1">
                                        <label for="advancePayment" class="font-weight-bold">
                                            Advance <small>(minimum amount 65%)</small>
                                        </label>
                                    </td>
                                    <td>
                                        <input class="form-control" name="advance"
                                            oninput="return checkAdvance(this.value)" id="advance" disabled>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:20%">
                                        <label class="font-weight-bold">Oustanding</label>
                                    </td>
                                    <td class="text-center">
                                        <label class="font-weight-bold text-center" id="remAmount"></label>
                                    </td>
                                <!-- </tr>
                                <tr> -->
                                    <td style="width:35%">
                                        <label class="font-weight-bold text-right">To Pay Now</label>
                                    </td>
                                    <td class="text-center">
                                        <label class="font-weight-bold text-center" id="toPay"></label>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>





                        </div>
                    </div>
                    <!-- table ended -->
                    <div class="modal-footer text-right w-100">
                        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                        <button type="button" class="btn btn-primary" onclick="save()" data-dismiss="modal">Save
                            changes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <h4 class="page-title">Orders 
                    <button class="btn btn-app btn-xs pull-right" style="margin-top: -5px">
                        <div id="orders_daterange" style="cursor: pointer; padding: 5px 10px; width: 100%;">
                            <i class="fa fa-calendar-alt"></i>&nbsp;
                            <span></span> <i class="fa fa-caret-down"></i> 
                            <i class="fas fa-spinner fa-spin orders_bill_loader"></i>
                            <input class="orders_startDate" type="hidden"> 
                            <input class="orders_endDate" type="hidden"> 
                        </div>
                    </button>
                </h4>
                    <table class=" customTable table-hover table-bordered" id="orders_table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Patient Info</th>
                                <th>Investigations</th>
                                <th style="width:15%">Status</th>
                                <th style="width:20%">amount</th>
                                <th>action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
            </div>
        </div>
    </div>
</section>


<script src="<?php echo base_url(); ?>assets/plugins/moment/moment.min.js" ></script>
<link href="<?php echo base_url(); ?>assets/newdaterangepicker24Jul2019/daterangepicker.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url(); ?>assets/newdaterangepicker24Jul2019/daterangepicker.js"></script>
<script type="text/javascript">
  $(document).ready(function(){    
      $('.finLoader').hide();   
      $('.orders_bill_loader').hide();  
      $('.summernote').summernote({
            toolbar: []
      });
  });
</script>

<script type="text/javascript">
    $(function() {

        var start = moment().subtract(0, 'days');
        var end = moment();



        function cb(start, end) {
            $('#dashboardBlock').addClass('d-none');
            $('#referalsBlock').addClass('d-none');
            $('#investigationsBlock').addClass('d-none');
            $('#loaderBlock').removeClass('d-none');
            $('#daterange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
            
            var report_type = $('#reportsType').val()
            var start_date = start.format('YYYY-MM-DD');
            var end_date = end.format('YYYY-MM-DD');
            
            $('#daterange .startDate').val(start_date);
            $('#daterange .endDate').val(end_date);    
            $('.finLoader').show();  

            console.log(report_type)

            $.post("<?=base_url('LabNew/getFinances')?>",
            {
                startDate:start_date,
                endDate:end_date,
                report_type: report_type
            },
            function(data){
                // console.log(data);
                if(report_type == "dashboard"){
                    var data = JSON.parse(data); 
                    $('.finLoader').hide();  
                    $('.collectedAmount').html(data.collectedAmount);
                    $('.outstandingBal').html(data.outstandingBal);
                    $('.invCount').html(data.invCount);
                    $('.totalDiscount').html(data.totalDiscount);
                    $('#dashboardBlock').removeClass('d-none');
                    $('#referalsBlock').addClass('d-none');
                    $('#investigationsBlock').addClass('d-none');
                    $('#loaderBlock').addClass('d-none');
                }
                else if(report_type == "referrals"){
                    $('#referalsBlock tbody').html(data);
                    $('#dashboardBlock').addClass('d-none');
                    $('#referalsBlock').removeClass('d-none');
                    $('#investigationsBlock').addClass('d-none');
                    $('#loaderBlock').addClass('d-none');
                }
                else if(report_type == "investigations"){
                    $('#investigationsBlock tbody').html(data);
                    $('#dashboardBlock').addClass('d-none');
                    $('#referalsBlock').addClass('d-none');
                    $('#investigationsBlock').removeClass('d-none');
                    $('#loaderBlock').addClass('d-none');
                }
                
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

    $(function() {

        var start = moment().subtract(0, 'days');
        var end = moment();

        function ocb(start, end) {
            $('#orders_daterange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
            
            var start_date = start.format('YYYY-MM-DD');
            var end_date = end.format('YYYY-MM-DD');
            
            $('#orders_daterange .orders_startDate').val(start_date);
            $('#orders_daterange .orders_endDate').val(end_date);    
            $('.orders_bill_loader').show();  
            
            // var order_tab = $('#orders_table').DataTable();
            // order_tab.ajax.reload(); 

            reloadOrders()
            $('.orders_bill_loader').hide();  

        }

        $('#orders_daterange').daterangepicker({
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
        }, ocb);

        ocb(start, end);

        });
</script>

<script>

// Report Entry
$(document).on("click",".save_report",function(){
    var billing_line_item_id = $(this).attr('data-value')
    var left = []
    var right = []
    var patient_values = []

    $('.patient_report_left').each(function(k, v){
        var clpli_id = $(this).attr('data-value')
        var val = $(this).val()
        left.push({
            clinic_lab_package_line_item_id: clpli_id,
            patient_inv_left: val
        })
    })

    $('.patient_report_right').each(function(k, v){
        var clpli_id1 = $(this).attr('data-value')
        var val1 = $(this).val()
        right.push({
            clinic_lab_package_line_item_id: clpli_id1,
            patient_inv_right: val1
        })
    })

    $('.patient_report_line_item_value').each(function(k, v){
        var clpli_id2 = $(this).attr('data-value')
        var val2 = $(this).val()
        patient_values.push({
            clinic_lab_package_line_item_id: clpli_id2,
            patient_package_line_item_value: val2
        })
    })
    
    $.post("<?=base_url('LabNew/save_patient_lab_reports')?>", {
        billing_line_item_id: billing_line_item_id,
        right: right,
        left: left,
        patient_values: patient_values
    },function(response){
        console.log(response)
        alert("Report Entry Done");
        $('#report_entry_popup').modal('hide')
        var order_tab = $('#orders_table').DataTable();
        order_tab.ajax.reload();
    })
})

// Authenticate
// $(document).on("click",".authenticate",function(){
//     var billing_line_item_id = $(this).attr('data-value')
//     var left = []
//     var right = []
//     var patient_values = []

//     $('.patient_report_left').each(function(k, v){
//         var clpli_id = $(this).attr('data-value')
//         var val = $(this).val()
//         left.push({
//             clinic_lab_package_line_item_id: clpli_id,
//             patient_inv_left: val
//         })
//     })

//     $('.patient_report_right').each(function(k, v){
//         var clpli_id1 = $(this).attr('data-value')
//         var val1 = $(this).val()
//         right.push({
//             clinic_lab_package_line_item_id: clpli_id1,
//             patient_inv_right: val1
//         })
//     })

//     $('.patient_report_line_item_value').each(function(k, v){
//         var clpli_id2 = $(this).attr('data-value')
//         var val2 = $(this).val()
//         patient_values.push({
//             clinic_lab_package_line_item_id: clpli_id2,
//             patient_package_line_item_value: val2
//         })
//     })
    
//     $.post("<?=base_url('LabNew/save_patient_lab_reports')?>", {
//         billing_line_item_id: billing_line_item_id,
//         right: right,
//         left: left,
//         patient_values: patient_values
//     },function(response){
//         console.log(response)
//         alert("Report Entry Done");
//         $('#report_entry_popup').modal('hide')
//         var order_tab = $('#orders_table').DataTable();
//         order_tab.ajax.reload();
//     })
// })


$(document).on("click", ".clearosa", function() {
    var data = $(this).attr('data-value')
    data = data.split("*$")
    $('#osa').val(data[1])
    $('#osa_billing_id').val(data[0])
})

$(document).on('click', '#clearosa', function() {
    var osa = $('#osa').val()
    var osa_billing_id = $('#osa_billing_id').val()
    $.post("<?= base_url('LabNew/ClearOsa') ?>", {
        osa: osa,
        osa_billing_id: osa_billing_id
    }, function(data) {
        console.log(data)
        // reload();
        $('#osaModal').modal('hide')
        var order_tab = $('#orders_table').DataTable();
        order_tab.ajax.reload();
    })
})

$(document).ready(function() {

    // $('#osaModal').modal()



    $('#advancePayment').on("click", function(e) {
        if ($(this).prop('checked') == true) {

            var inv = []
            var n = 0

            $('.addInv_tbody tr').each(function(key, value) {
                console.log($(this).attr('data-value'))
                var dataVal = $(this).attr('data-value')
                dataVal = dataVal.split("*$")
                inv[n] = {
                    name: dataVal[0],
                    id: dataVal[1],
                    price: dataVal[2]
                }
                n++;
            })

            if (inv.length == 0) {
                alert("Please Add Investigations")
                $(this).prop('checked', false)
            } else {
                var total_amount = $('#total_amount').val()
                var minAdvance = ((parseFloat(total_amount) * 65) / 100).toFixed(2)
                $('#advance').removeAttr('disabled')
                billing()
                // $('#advance').val(minAdvance)
                // $('#remAmount').html(parseFloat(total_amount) - parseFloat(minAdvance))
            }

        } else {
            $('#advance').val('')
            billing()
            $('#advance').attr('disabled', true)
        }
    })
})
</script>

<script>
// function collectSample(){
//     var data = $(this).val()
//     console.log(data)
// }
</script>

<script>
var added = []

function makeBillModalEmpty() {
    var added = []
    $('#mobile').val('');
    $('#name').val('');
    $('#age').val('');
    $('#gender').val('');
    $('#advance').val('')
    $('#discount').val('')
    $('#advancePayment').prop('checked', false)
    $('#remAmount').html('')
    $('#toPay').html('')
    $('#total_amount').val('')
    $('#advance').attr('disabled')
    $('.addInv').remove()
}

function billing() {
    var total_amount = $('#total_amount').val();
    var discount = $('#discount').val()
    if ($('#advancePayment').prop('checked') == true) {
        var adv = $('#advance').val()
        if (parseFloat(discount) > 0 && discount != "") {
            var discountedAmt = (parseFloat(total_amount) * parseFloat(discount)) / 100
            var afterDiscAmt = parseFloat(total_amount) - parseFloat(discountedAmt)
            if (adv != "" && adv == 0) {
                var adAmt = (parseFloat(afterDiscAmt) * 65) / 100
                $('#advance').val(adAmt.toFixed(2))
            } else {
                var adAmt = $('#advance').val()
            }
            $('#remAmount').html((parseFloat(afterDiscAmt) - parseFloat(adAmt)).toFixed(2))
            $('#toPay').html($('#advance').val())
        } else {
            if (adv != "" && adv == 0) {
                var adAmt = (parseFloat(total_amount) * 65) / 100
                $('#advance').val(adAmt.toFixed(2))
            } else {
                var adAmt = $('#advance').val()
            }
            console.log(adAmt)
            console.log(parseFloat(total_amount) - parseFloat(adAmt))
            $('#remAmount').html((parseFloat(total_amount) - parseFloat(adAmt)).toFixed(2))
            $('#toPay').html($('#advance').val())
        }
    } else {
        $('#advance').val(0)
        if (parseFloat(discount) > 0 && discount != "") {
            var discountedAmt = (parseFloat(total_amount) * parseFloat(discount)) / 100
            var afterDiscAmt = parseFloat(total_amount) - parseFloat(discountedAmt)
            $('#remAmount').html(0)
            $('#toPay').html(afterDiscAmt)
        } else {
            $('#remAmount').html(0)
            $('#toPay').html(total_amount)
        }
    }
}

function discount(discount) {
    if (discount <= 100) {

    } else {
        alert('Cannot Exceed More than 100')
        $('#discount').val('100')
    }
    billing()

}

function checkAdvance(amount) {
    var total_amount = $('#total_amount').val()
    var discount = $('#discount').val()
    // console.log(minAdvance)
    // console.log(amount)
    if (amount != "") {
        if (parseFloat(discount) > 0 && discount != "") {
            var discountedAmt = (parseFloat(total_amount) * parseFloat(discount)) / 100
            var afterDiscAmt = parseFloat(total_amount) - parseFloat(discountedAmt)
            var minAdvance = ((parseFloat(afterDiscAmt) * 65) / 100).toFixed(2)
        } else {
            var minAdvance = ((parseFloat(total_amount) * 65) / 100).toFixed(2)

        }
        if ($('#advancePayment').prop('checked') == true) {
            if (amount > 0) {
                // $('#advance').val(amoun)
            } else {
                $('#advance').val(minAdvance)
            }
        } else {

        }

        if (parseFloat(amount) < parseFloat(minAdvance)) {
            $('#advance').css({
                'border-color': 'red'
            })
        } else if (parseFloat(amount) >= parseFloat(minAdvance)) {
            $('#advance').css({
                'border-color': 'green'
            })
        }
        if (parseFloat(amount) > parseFloat(total_amount)) {
            $('#advance').val(minAdvance)
        }
    } else {
        $('#advance').css({
            'border-color': 'red'
        })
        // $('#advance').removeClass('bg-success text-white')
        // $('#advance').addClass('bg-danger text-white')
    }
    billing()

}
var data = [];

var patient_rpts = [];


function show_inv() {

    var mobile = $('#mobile').val();
    var name = $('#name').val();
    console.log(mobile.length)

    if (mobile.length > 8 && name.length != '') {

        $("#investigation_part").show(1000);

    } else {

        $("#investigation_part").hide(1000);
    }
}
</script>

<script>
function search_result(search) {

    $('#search_investigations').autocomplete({
        // minLength: 3,
        source: function(request, response) {
            $.ajax({
                url: '<?= base_url('LabNew/clinic_Investigation_search') ?>',
                type: 'POST',
                data: {
                    search: search
                },
                success: function(result) {

                    response($.parseJSON(result))

                    // if(result==)
                    // console.log("this is empty");

                }
            })
        },


        select: function(event, ui) {

            // console.log(ui);

            var inv_id = ui.item.package_id
            var inv_name = ui.item.package_name
            var inv_price = ui.item.package_id

            var price = $('#price').val();
            $("#inv_name").val(inv_name);
            $("#somediv").val(inv_id);
            // event.preventDefault();
            // $("#search_investigations").val(inv_name);
            // model(inv_id, inv_name);
            $("#price_input").show();
            var obj = ui.item
            console.log(obj);
            investigation_set(obj)
            // console.log(data);

        },

        create: function() {

            $(this).data('ui-autocomplete')._renderItem = function(ul, item) {

                return $('<li>')
                    .append(
                        '<a value="' + item.package_id +
                        '" id="searching" ><div class="inline-block srchRes w-100"><div class="row"><div class="col-md-12"><p class="m-0 p-0 font-weight-light trade_name" style="padding:0px !important">' +
                        item.package_name + '</div></a>')
                    .appendTo(ul);

            };

        }


    })
}
</script>


<script>
var prices = []
var i = 0

function investigation_set(obj) {

    $('.addInv_tbody tr').each(function(key, value) {
        added[i] = $(this).attr('data-id')
        i++
    })
    // console.log(obj)
    // console.log(added)
    // console.log(obj.clinic_lab_package_id)

    if ($.inArray(obj.clinic_lab_package_id, added) == -1) {
        var appData = obj.package_name + "*$" + obj.package_id + "*$" + obj.price


        var html = '<tr class="addInv" data-id="' + obj.clinic_lab_package_id + '" data-value="' + appData +
            '" id="clp_id_' + obj
            .clinic_lab_package_id + '"><td colspan="3"><p style="padding: 0px !important">' + obj.package_name +
            '</span></td><td class="addInv_price" data-value="' + obj
            .price + '">' + obj.price +
            ' <a class="delAddInv pull-right" data-id="' + obj.clinic_lab_package_id +
            '"  id="addInvr_' + obj
            .clinic_lab_package_id +
            '"><i class="fa fa-trash"></i></a></td></tr>';

        $("#added_investigation_table tbody").append(html);
        var rate = 0
        $('.addInv_tbody tr td.addInv_price').each(function(key, value) {
            rate += parseFloat($(this).attr('data-value'))
        })
        $('#total_amount').val(rate)
        $('#discount').val('')
        // var ad = $('#advance').val()

        var minAdvance = ((parseFloat(rate) * 65) / 100).toFixed(2)
        if ($('#advancePayment').prop('checked') == true) {
            var ad = $('#advance').val()
            $('#advance').val(minAdvance)
        } else {
            $('#advance').val('')
        }
        checkAdvance(minAdvance)
        billing()

    } else {
        alert("Already Exists!!");
    }

}
</script>
<script>
$(document).on("click", ".delAddInv", function() {
    // alert("hi naveen")
    var id = $(this).attr('data-id')
    $('#clp_id_' + id).remove()
    const index = added.indexOf(id);
    if (index > -1) {
        added.splice(index, 1);
    }
    var rate = 0
    $('.addInv_tbody tr td.addInv_price').each(function(key, value) {
        rate += parseFloat($(this).attr('data-value'))
    })
    $('#total_amount').val(rate)
    billing()
})
</script>

<script>
function remove_inv(id, this_price, tot) {

    console.log(id)

    var act_total = $("#total_amount").val();

    var delete_tot = act_total - this_price;

    $("#total_amount").val(delete_tot);

    alert(delete_tot);



    $("#" + id).remove();

    console.log(data);

    for (var i = 0; i < data.length; i++) {

        const found = data.find(element => data[i].id == id);

        if (data[i] == found) {

            data.splice(i, 1);

        }
    }

}
</script>
<script>
function reloadOrders(){
    $('#orders_table').DataTable().destroy()
    $('#orders_table').DataTable({
        "paging": true,
        "searching":true,
        "processing": true,
        "serverSide": true,
        // "bLengthChange": false,
        // "bSort": false,
        // "order": [],
        "ajax": {
            url: "<?=base_url()?>/LabNew/orders",
            type: 'POST',
            data: {
                start_date: $('#orders_daterange .orders_startDate').val(),
                end_date: $('#orders_daterange .orders_endDate').val()
            }
        },
        // "columnDefs": [{
        //     "targets": [0],
        //     "orderable": false
        // }],

        success: function(data) {
            console.log(data)
            alert("hi")
        },

    });
}

</script>

<script>
$(document).ready(function() {
    // $('#dashboardModal').modal()
    // var start_date = $('#orders_daterange .orders_startDate').val();
    // var end_date = $('#orders_daterange .orders_endDate').val();   
    // $('#orders_table').DataTable().destroy()
    // $('#orders_table').DataTable({
    //     "paging": true,
    //     "searching":true,
    //     "processing": true,
    //     "serverSide": true,
    //     // "bLengthChange": false,
    //     // "bSort": false,
    //     // "order": [],
    //     "ajax": {
    //         url: "<?=base_url()?>/LabNew/orders",
    //         type: 'POST',
    //         data: {
    //             start_date: $('#orders_daterange .orders_startDate').val(),
    //             end_date: $('#orders_daterange .orders_endDate').val()
    //         }
    //     },
    //     // "columnDefs": [{
    //     //     "targets": [0],
    //     //     "orderable": false
    //     // }],

    //     success: function(data) {
    //         console.log(data)
    //         alert("hi")
    //     },

    // });
});
</script>

<script>
function reload() {

    // var trCount = $("#added_investigation_table").find('tr').length;

    // alert(trCount);
    var results = [];
    $('#added_investigation_table  tbody tr').each(function(k, v) {
        results.push(v);

    });

    alert(results);
    // var table = $('#added_investigation_table tbody tr').text();

    // alert(results);

}
</script>
<script>
function delete_patient_investigaton(id) {
    // alert(id);
    // exit();
    $.ajax({
        url: '<?=base_url()?>/LabNew/delete_patient_investigatons',
        type: 'POST',
        data: {
            patient_inv_id: id,
        },
        success: function(response) {

            reload();
        },
        error: function() {
            alert("error");
        }
    });

}
</script>
<script>
function add_patient_investigation(inv_id, inv_name) {
    var patient_number = $('#mobile').val();
    var patient_name = $('#name').val();

    $.ajax({
        url: '<?=base_url()?>/LabNew/add_patient_investigation',
        type: 'POST',
        data: {
            inv_id: inv_id,
            inv_name: inv_name,
            patinet_name: patient_name,
            patient_number: patient_number
        },
        success: function(response) {

            reload();
        },
        error: function() {
            alert("error");
        }
    });
}
</script>

<script>
function save() {
    var inv = []
    var n = 0

    $('.addInv_tbody tr').each(function(key, value) {
        console.log($(this).attr('data-value'))
        var dataVal = $(this).attr('data-value')
        dataVal = dataVal.split("*$")

        inv[n] = {
            name: dataVal[0],
            id: dataVal[1],
            price: dataVal[2]
        }
        // inv[n]['id'] = dataVal[1]
        // inv[n]['price'] = dataVal[2]

        n++;
    })

    console.log(inv)
    var flag;
    var total_amount = $('#total_amount').val();
    var discount = $('#discount').val()
    if ($('#advancePayment').prop('checked') == true) {
        var advance = $('#advance').val()
        if (discount > 0 && discount != "") {
            var discountedAmt = (parseInt(total_amount) - ((parseInt(total_amount) * parseInt(discount)) / 100))
                .toFixed(2)
            var final = ((discountedAmt * 65) / 100).toFixed(2)
            if (advance >= final) {
                flag = 1;
            } else {
                flag = 0;
            }
        } else {
            var final = ((total_amount * 65) / 100)
            if (advance >= final) {
                flag = 1;
            } else {
                flag = 0;
            }
        }
    } else {
        flag = 1;
    }

    if (flag == 1) {
            var patient_number = $('#mobile').val();
            var patient_name = $('#name').val();
            var patient_age = $('#age').val();
            var patient_gender = $('#gender').val();
            var age_unit = $('#age_unit').val()
            var email = $('#email').val()
            var referred_by = $('#referred_by').val()
        if (inv.length != 0 && patient_number != "" && patient_name != "") {

            var advance = $('#advance').val()
            var discount = $('#discount').val()
            var advanceCheck = $('#advancePayment').val()
            var toPay = $('#toPay').html()
            var osa = $('#remAmount').html()
            // alert(patient_number+" "+patient_name+" "+patient_age+" "+patient_gender)
            // console.log(data)

            console.log(advanceCheck)
            $.ajax({
                url: '<?=base_url()?>/LabNew/add_patient_investigation',
                type: 'POST',
                data: {
                    patient_number: patient_number,
                    patient_name: patient_name,
                    patient_age: patient_age,
                    patient_gender: patient_gender,
                    age_unit: age_unit,
                    email: email,
                    referred_by: referred_by,
                    inv_data: inv,
                    advance: advance,
                    discount: discount,
                    advance_check: advanceCheck,
                    toPay: toPay,
                    osa: osa
                },
                success: function(response) {
                    // console.log(advanceCheck)
                    // console.log(response)
                    $('.addInv_tbody').empty()
                    added = []

                    var order_tab = $('#orders_table').DataTable();
                    order_tab.ajax.reload();
                    // console.log("<?=base_url()?>/LabNew/LabInvoice/" + $.trim(response))

                    window.open("<?=base_url()?>/LabNew/LabInvoice/" + $.trim(response),"_blank");
                    // window.reload()
                },

                error: function() {

                    alert("error");
                }
            });

        } else {
            alert('Please Enter Valid Info')
        }
    } else {
        alert('Amount is not Sufficient')
    }




    // var type = typeof(data);
    // console.log(data.length)
    // // console.log('this is array');


}
</script>


<script>

$(document).on("click",".getInvoiceList",function(){
    var data = $(this).attr("data-value")
    $.post("<?=base_url('LabNew/getInvoices')?>", {billing_id : data}, function(data){
        $('.invoicesTbody').html(data)
    })
})

$(document).on("click",".delOrder",function(){
    var id = $(this).attr('data-value')
    $.ajax({
        url: '<?=base_url()?>/LabNew/delete_order',
        type: 'POST',
        data: {
            order_id: id,
        },
        success: function(response) {

            var order_tab = $('#orders_table').DataTable();
            order_tab.ajax.reload();
        },
        error: function() {
            alert("error");
        }
    });
})
$(document).on("click",".deleteLineItems",function(){
    var billing_line_item_id = $(this).attr('data-value')
    $.ajax({
        url: '<?=base_url()?>/LabNew/delete_billing_line_items',
        type: 'POST',

        data: {
            billing_line_item_id: billing_line_item_id,
        },
        success: function(response) {
            if (response == 1) {
                alert("Please Delete Total Recond");
                del_order(response)
            } else {
                var order_tab = $('#orders_table').DataTable();
                order_tab.ajax.reload();
            }

        },
        error: function() {

            alert("error");
        }

    });
})

$(document).on("click",".collectSample",function(){
    var data = $(this).attr('data-value')
    var a = confirm("Sample Taken?");
    if(a == true){
        $.post("<?=base_url('LabNew/Sample')?>", {billing_line_item_id : data}, function(response){
            console.log($.trim(response))
            if($.trim(response) == "1"){
                var order_tab = $('#orders_table').DataTable();
                order_tab.ajax.reload();
            }
            else{
                alert("Error Occured. Please Try Again Later")
            }
        })
    }
    
})


$(document).on("click",".authenticate",function(){
    var report_details
    
    var lab_patient_report_id = $(this).attr('data-value')
    // alert("this is the package_idsss " + lab_patient_report_id);
    var fire = 0;
    var popuparray = '';

    console.log(report_details);
    // this ajax is used to send the entry details into db
    $.ajax({
        url: '<?=base_url()?>/LabNew/authenticate_popup_entry',
        type: 'POST',
        data: {
            lab_patient_report_id:lab_patient_report_id
        },
        success: function(response) {

            $("#authenticateDiv").html(response);
            $('.summernote').summernote({
                toolbar: []
            });
        },
        error: function() {
            alert("error");
        }
    });
    
})

$(document).on("click",".report_entry",function(){
    var report_details;

    var value = $(this).attr('data-value')
    var test = value.split(',');
    var package_id = test[0];
    var billing_line_item_id = test[1];
    // alert("this is the package_idsss " + test);
    console.log(test)
    var fire = 0;
    var popuparray = '';

    console.log(report_details);
    // this ajax is used to send the entry details into db
    // popup_report_entry
    $.ajax({
        url: '<?=base_url()?>/LabNew/report_entry_popup',
        type: 'POST',
        data: {
            package_id: package_id,
            billing_line_item_id: billing_line_item_id
        },
        success: function(response) {
            console.log(response)
            $('#entryDiv').html(response)
            $('.summernote').summernote({
                toolbar: []
            });
        },

        error: function() {
            alert("error");
        }
    });
})
</script>

<script>
$(document).on("click",".printReport",function(){
    var billing_line_item_id = $(this).attr('data-value')
    $.post("<?=base_url('LabNew/printReport')?>", {billing_line_item_id:billing_line_item_id}, function(data){

    })
})
$(document).on("click", ".checkPrintReport", function(){
    var link = $(this).attr('data-value')
    var billing_line_item_id = $(this).attr('data')
    $.post("<?=base_url('LabNew/printReportStatus')?>", {billing_line_item_id:billing_line_item_id}, function(data){
        data = $.trim(data)
        console.log(data)
        if(data == 1){
            window.open(link,'_blank')
        }
        else{
            alert("You are not allowed to print report. Clear Outstanding Balance and try again")
        }
    })
})
</script>

<script>
function report(billing_line_item_id) {
    // console.log("this is the report billing line item id  " + billing_line_item_id)
    var prnt_btn_show;
    var id;
    var patient_report_array = [];
    var i = 0;
    $('.inv').each(function(key, value) {
        id = $(this).attr('id')
        var split_val = id.split("_")
        patient_report_array.push({

            "clinic_lab_package_line_item_id": split_val[1],
            "patient_package_line_item_value": $('#' + id).val()
        })
        i++
    })

    console.log(patient_report_array);

    $.ajax({
        url: '<?=base_url()?>/LabNew/save_patient_lab_reports',
        type: 'POST',

        data: {
            patient_report_array: patient_report_array,
            billing_line_item_id: billing_line_item_id
        },
        success: function(response) {
            // prnt_btn_show = response;
            alert('Report Entry Done')
            response = $.trim(response)
            $('#report_entry_popup').modal('hide')
            var order_tab = $('#orders_table').DataTable();
            order_tab.ajax.reload();
        },
        error: function() {
            alert("error");
        }
    });
    var rep = $('#report_submit').val();
    console.log("this is the print btn validation" + prnt_btn_show);
    var z = $("#zd").val();
}

$(document).on("click",".authenticate_report",function(){

    var lab_patient_report_id = $(this).attr('data-value')
    if($('#auth_'+lab_patient_report_id).prop('checked') == true){
        var left = []
        var right = []
        var patient_values = []

        $('.patient_report_left').each(function(k, v){
            var clpli_id = $(this).attr('data-value')
            var val = $(this).val()
            left.push({
                clinic_lab_package_line_item_id: clpli_id,
                patient_inv_left: val
            })
        })

        $('.patient_report_right').each(function(k, v){
            var clpli_id1 = $(this).attr('data-value')
            var val1 = $(this).val()
            right.push({
                clinic_lab_package_line_item_id: clpli_id1,
                patient_inv_right: val1
            })
        })

        $('.patient_report_line_item_value').each(function(k, v){
            var clpli_id2 = $(this).attr('data-value')
            var val2 = $(this).val()
            patient_values.push({
                clinic_lab_package_line_item_id: clpli_id2,
                patient_package_line_item_value: val2
            })
        })
        
        $.post("<?=base_url('LabNew/authenticate_patient_reports')?>", {
            lab_patient_report_id: lab_patient_report_id,
            right: right,
            left: left,
            patient_values: patient_values
        },function(response){
            console.log(response)
            alert("Authentication Done.Report Ready.");
            $('#authenticate_popup_entry').modal('hide')
            var order_tab = $('#orders_table').DataTable();
            order_tab.ajax.reload();
        })
    }
    else{
        alert('Please Check "I authenticate all the above results"')
    }
    
})



function authenticate(billing_line_item_id) {
    // console.log("this is the report billing line item id  " + billing_line_item_id)
    if($('#auth_'+billing_line_item_id).prop('checked') == true){
        var id;
        var patient_report_array = [];
        var i = 0;
        $('.inv').each(function(key, value) {
            id = $(this).attr('id')
            var split_val = id.split("_")
            patient_report_array.push({

                "clinic_lab_package_line_item_id": split_val[1],
                "patient_package_line_item_value": $('#' + id).val()
            })
            i++
        })

        console.log(patient_report_array);

        $.ajax({
            url: '<?=base_url()?>/LabNew/authenticate_patient_lab_reports',
            type: 'POST',

            data: {
                patient_report_array: patient_report_array,
                billing_line_item_id: billing_line_item_id
            },
            success: function(response) {
                // prnt_btn_show = response;
                alert('Report Authenticated')
                response = $.trim(response)
                $('#authenticate_reports').modal('hide')
                var order_tab = $('#orders_table').DataTable();
                order_tab.ajax.reload();
            },
            error: function() {
                alert("error");
            }
        });
        var rep = $('#report_submit').val();
        var z = $("#zd").val();
    }
    else{
        alert("Please check 'I authenticate all the above results'");
    }
    
}


// function popup(value) {
//     alert(value);
//     $.ajax({
//         url: '<?=base_url()?>/LabNew/popup_report_entry',
//         type: 'POST',
//         data: {
//             package_id: value,
//         },
//         success: function(response) {
//             alert(response);

//         },
//         error: function() {
//             alert("error");
//         }
//     });
// }
// var popuparray = '';


function report_entry_popup(value) {
    // var html;
    var report_details;

    var test = value.split(',');
    var package_id = test[0];
    var billing_line_item_id = test[1];
    // alert("this is the package_idsss " + test);
    var fire = 0;
    var popuparray = '';

    console.log(report_details);
    // this ajax is used to send the entry details into db
    $.ajax({
        url: '<?=base_url()?>/LabNew/popup_report_entry',
        type: 'POST',
        data: {
            package_id: package_id,
            billing_line_item_id: billing_line_item_id
        },
        success: function(response) {
            popuparray = response;
            console.log(response);
            
            fire = JSON.parse(popuparray);
            console.table(fire);
            var html = "";
            // html =
            // '<div class="modal fade " id="report_entry_popup" style="min-width: 600px;" role="dialog">';
            // html += '<div class="modal-dialog">';
            html += ' <div class="modal-content">';
            html += ' <div class="modal-header">';
            html += '<h3 class="modal-title text-white">' + fire[0].mian_heading + '</h3>';
            html += '<button type="button" class="close" data-dismiss="modal">&times;</button> ';
            html += ' </div>';
            html += ' <div class="modal-body">';

            html += '<table class="customTable">';
            html += '<thead>';
            html += '<tr class="modal-title font-weight-bold">';
            html += '<th style="font-size:13px;">Investigation</th>';
            html += '<th style="font-size:13px;">Enter Value</th>';
            html += '<th style="font-size:13px;">min-max-units</th>';
            // html += '<td>third</td>';
            // html += '<td>Details</td>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';

            var disabled;

            if (fire[0].billing_line_item_id == null) {

                disabled = "";
            } else {
                disabled = "disabled";
            }

            for (var i = 0; i < fire.length; i++) {

                //hading purpose
                if (fire[i].heading !== null) {

                    var fruit =
                        '<p  class="font-weight-bold" style="font-size:13px;text-align: left;">' +
                        fire[i].heading + '</p>';
                }
                //hading purpose


                var dropdown = fire[i].dropdowns;
                var dropdownarry = dropdown.split(';');
                // console.log(dropdownarry);
                var report_value;

                if (fire[i].patient_package_line_item_value == null) {
                    report_value = "";
                } else {
                    report_value = fire[i].patient_package_line_item_value;
                }

                html += '<tr class="text-primary">';

                html += '<td scope="row"><span>' + fire[i].investigation_name +'</span></td>';
                html += '<td>';

                if (fire[i].content == "" && fire[i].dropdowns == "") {

                    html +=
                        '<input class="form-control inv" type="text" value="' + report_value +
                        '" id="investigation_' + fire[i]
                        .clinic_lab_package_line_item_id +
                        '"' + disabled + '>';
                }

                else if (fire[i].dropdowns != "") {

                    html += '<select ' + disabled +
                        ' class="custom-select inv" name="cars" id="investigation_' +
                        fire[i]
                        .clinic_lab_package_line_item_id + '">';

                    // html += '<option value="default" autofocus>select options</option>';
                    for (var j = 0; j < dropdownarry.length; j++) {

                        if (fire[i].patient_package_line_item_value == null) {
                            dropdownarry[j] = dropdownarry[j];
                        } else {
                            dropdownarry[j] = fire[i].patient_package_line_item_value
                        }

                        html += '<option value="' + dropdownarry[j] + '" id="' + fire[i]
                            .clinic_lab_package_line_item_id + '">' + dropdownarry[j] + '</option>';
                    }

                    html += '</select>';
                }

                else if (fire[i].max_value == "" && fire[i].dropdowns == "") {

                    var display_content;

                    var content = fire[i].content;

                    var db_content = fire[i].patient_package_line_item_value;

                    if (content === db_content) {

                        display_content = content

                    } else if (db_content == null) {

                        display_content = content
                    } else {
                        display_content = db_content
                    }


                    html +=
                        '<textarea  ' + disabled +
                        ' class="form-control inv" placeholder="" rows="5" cols="60" id="investigation_' +
                        fire[i]
                        .clinic_lab_package_line_item_id + '">' +
                        display_content + '</textarea>';
                }
            else {

                html +=
                    '<input class="form-control inv" type="text" value="' + report_value +
                    '" id="investigation_' + fire[i]
                    .clinic_lab_package_line_item_id +
                    '"' + disabled + '>';
                }


                html += '</td>';

                // this is the value printing variables
                var dash = " -- ";
                var slash = "     ";
                // 
                if (fire[i].min_value == "" && fire[i].max_value == "") {
                    dash = "";
                    slash = "";
                }

                html +=
                    '<td>' + fire[i].min_value + dash + fire[i].max_value + slash + fire[i]
                    .min_max_units +
                    '</td>';
                // html += '<th scope="row"></th>';
                html += '</tr>';
            }

            // for (var i = 0; i < fire.length; i++) {

            //     patient_rpts.push({
            //         id: $("'#" + fire[i].clinic_lab_package_line_item_id + "'").val(),
            //         name: $('#fire[' + i + '].investigation_name').val(),
            //         price: "testing mater"
            //     });

            // }
            var submit_btn, print_btn;
            if (disabled == "disabled") {

                submit_btn = "none";
                print_btn = "block";
            } else {
                block = "block";
                print_btn = "none";
            }
            html += '</tbody>';
            html += '</table>';
            html += '<div class="modal-footer">';
            html +=
                '<button class="btn btn-primary" style="display:' + submit_btn +
                ';" id="report_submit" onclick="report(' +
                billing_line_item_id + ')">SUBMIT</button>';

            html +=
                '<button data-value="<?=base_url()?>/LabNew/patient_report_pdf/' + billing_line_item_id +
                '/' +
                package_id + '" data="'+billing_line_item_id+'" class="btn btn-success checkPrintReport" style="display: ' +
                print_btn +
                ';" id="print_btn"><i class="fa fa-print" aria-hidden="true" ></i>PRINT REPORT</button>';
            html +=
                '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
            html += '</div>';
            // console.log("this is the patient_rpts array  " + patient_rpts)

            $("#InfoDiv").html(html);
        },

        error: function() {
            alert("error");
        }
    });
}
</script>

<script>
function delete_billing_line_items(billing_line_item_id) {

    $.ajax({
        url: '<?=base_url()?>/LabNew/delete_billing_line_items',
        type: 'POST',

        data: {
            billing_line_item_id: billing_line_item_id,
        },
        success: function(response) {
            if (response == 1) {
                alert("Please Delete Total Recond");
                del_order(response)
            } else {
                var order_tab = $('#orders_table').DataTable();
                order_tab.ajax.reload();
            }

        },
        error: function() {

            alert("error");
        }

    });

}
</script>
</script>