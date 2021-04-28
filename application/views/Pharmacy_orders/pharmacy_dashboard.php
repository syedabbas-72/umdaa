<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item"
                    href="<?=base_url('Dashboard')?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i
                    class="fa fa-angle-right"></i>
            </li>
            <li><a class="active">PHARMACY DASHBOARD</a></li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-md-12 analytics">
        <div class="card">
            <table cellpadding="0" cellspacing="0" class="table table-bordered noPadding" style="border: none;">
                <tr>
                    <!-- Doctors & Finances Dashboards -->
                    <td class="noBdr">
                        <table class="table">
                            <!-- Left & Right Header Panes Row -->
                            <tr>
                                <th width="40%">Doctors</th>
                                <th>
                                    <div class="row">
                                        <div class="col-md-6 text-center" style="margin-top: 5px;padding-right: 0px">
                                            FINANCES <i class="fas fa-spinner fa-spin finLoader"></i>
                                        </div>
                                        <div class="col-md-6 pull-right" style="padding-left: 0px !important">
                                            <div id="daterange"
                                                style="cursor: pointer; padding: 5px 10px; width: 100%;">
                                                <i class="fa fa-calendar-alt"></i>&nbsp;
                                                <span></span> <i class="fa fa-caret-down"></i>
                                                <input class="startDate" type="hidden">
                                                <input class="endDate" type="hidden">
                                            </div>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <!-- Precsriptions Pane-->
                                <td class="analyticsLeftPane">
                                    <input type="hidden" value="all" id="dispval" />
                                    <ul class="doctors-list">
                                        <li id="all" class="all-doctors selected"
                                            onclick="getdetails('<?php echo $clinic_id; ?>','all',this.id)">
                                            <span class="doctor-category-list-item">
                                                <i class="fa fa-circle" style="color:#ccc;font-size:13px;"></i>
                                                <span class="doctor-name">&nbsp;ALL DOCTORS</span>
                                                <span id="total_apnts" class="num-appoint"></span>
                                            </span>
                                        </li>

                                        <?php
                                        foreach ($doctors_list as $key => $values) {
                                            $get_doctor = $this->db->query("select * from doctors d inner join department dep on(d.department_id = dep.department_id) where d.doctor_id='".$values->doctor_id."'")->row();
                                            if($get_doctor->first_name=="" && $get_doctor->last_name=="")
                                                continue;
                                            ?>
                                        <li class="all-doctors" id="<?php echo $values->doctor_id; ?>_cnt"
                                            onclick="getdetails('<?php echo $clinic_id; ?>','<?php echo $values->doctor_id; ?>', this.id)">
                                            <span class="doctor-category-list-item">
                                                <i class="fa fa-circle"
                                                    style="color:<?php echo $get_doctor->color_code; ?>; font-size:13px;"></i>
                                                <span
                                                    class="doctor-name">&nbsp;<?php echo "Dr. ".strtoupper($get_doctor->first_name."  ".$get_doctor->last_name); ?></span><br>
                                                <span
                                                    style="margin-left: 25px"><small><?php echo $get_doctor->department_name; ?></small></span>
                                                <span class="num-appoint"></span>
                                            </span>
                                        </li>
                                        <?php  
                                        } ?>
                                    </ul>
                                    <!-- hidden fields -->
                                    <input type="hidden" id="clinic_id" value="<?php echo $clinic_id; ?>">
                                    <input type="hidden" id="doctor_id" value="all">
                                    <input type="hidden" id="doctor_slot" value="">
                                    <input type="hidden" id="doctor_id_list" value="<?php echo $doctor_id; ?>">
                                    <input type="hidden" id="li_holder_tb" value="all">

                                </td>

                                <!-- Finances Pane -->
                                <td class="analyticsRightPane" style="vertical-align: top">
                                    <div id="amt_table">
                                        <table cellspacing="0" cellpadding="0" class="table finances">
                                            <tr>
                                                <td class="noBdr">
                                                    <span class="amt expectedRevenue">
                                                        <i class='fas fa-rupee-sign'></i>
                                                        0.00
                                                    </span><br />
                                                    <p class="text-center">EXPECTED REVENUE</p>
                                                    <small>(From <span
                                                            id="expectedPrescriptionCount_span"><?php echo $prescriptions_count; ?></span>
                                                        prescription(s))</small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="noBdr">
                                                    <span class="amt convertedRevenue">
                                                        <i class='fas fa-rupee-sign'></i>
                                                        0.00
                                                    </span><br />
                                                    <p class="text-center">CONVERTED REVENUE</p>
                                                    <small>(From <span
                                                            id="convertedPrescriptionsCount_span"><?php echo $billing_count;?></span>
                                                        Prescription(s))</small>
                                                    <small class="text-muted">* May Include Previous Converted Prescriptions</small>
                                                </td>
                                            </tr>
                                            <tr class="inRow">
                                                <td class="noBdr">
                                                    <span class="amt indiscounts">
                                                        <i class='fas fa-rupee-sign'></i>
                                                        0.00
                                                    </span><br />
                                                    <p class="text-center">Converted Prescriptions Discounts</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="noBdr">
                                                    <span class="amt lostRevenue">
                                                        <i class='fas fa-rupee-sign'></i>
                                                        0.00
                                                    </span><br />
                                                    <p class="text-center">LOST AMOUNT</p>
                                                    <small>( Includes Discounts )</small>
                                                </td>
                                            </tr>
                                            <tr class="outRow">
                                                <td class="noBdr">
                                                    <span class="amt outPeople">
                                                        <i class='fas fa-rupee-sign'></i>
                                                        0.00
                                                    </span><br />
                                                    <p class="text-center">OUT PEOPLE</p>
                                                    <small>(Outside <span
                                                            id="outPeopleCount_span"><?php echo $out_people_count; ?></span>
                                                        people)</small>
                                                </td>
                                            </tr>
                                            <tr class="outDRow">
                                                <td class="noBdr">
                                                    <span class="amt outdiscounts">
                                                        <i class='fas fa-rupee-sign'></i>
                                                        0.00
                                                    </span><br />
                                                    <p class="text-center">OUT DISCOUNTS</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/plugins/moment/moment.min.js"></script>
<link href="<?php echo base_url(); ?>assets/newdaterangepicker24Jul2019/daterangepicker.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url(); ?>assets/newdaterangepicker24Jul2019/daterangepicker.js">
</script>
<script type="text/javascript">
$(document).ready(function() {
    $('.finLoader').hide();
});
</script>
<script type="text/javascript">
$(function() {

    var start = moment().subtract(0, 'days');
    var end = moment();

    function cb(start, end) {
        $('#daterange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));

        var start_date = start.format('YYYY-MM-DD');
        var end_date = end.format('YYYY-MM-DD');

        $('#daterange .startDate').val(start_date);
        $('#daterange .endDate').val(end_date);
        var did = $('.doctors-list li.selected').attr("id");
        if (did == "all") {
            var d_id = "all";
        } else {
            var d_id = did.split("_")[0];
        }
        $('.finLoader').show();

        $.post("<?=base_url('Pharmacy_orders/getFinances')?>", {
                d_id: d_id,
                startDate: start_date,
                endDate: end_date
            },
            function(data) {
                console.log(data)
                var JSONData = JSON.parse(data);
                // console.log(JSONData);
                $('.finLoader').hide();
                $('.expectedRevenue').html("<i class='fas fa-rupee-sign'></i> " + JSONData
                .expected_revenue);
                $('.convertedRevenue').html("<i class='fas fa-rupee-sign'></i> " + JSONData
                    .converted_revenue);
                $('.outPeople').html("<i class='fas fa-rupee-sign'></i> " + JSONData.out_people_revenue);
                $('.lostRevenue').html("<i class='fas fa-rupee-sign'></i> " + JSONData.lost_revenue);
                $('.indiscounts').html("<i class='fas fa-rupee-sign'></i> " + JSONData.indiscounts);
                $('.outdiscounts').html("<i class='fas fa-rupee-sign'></i> " + JSONData.outdiscounts);
                $('#expectedPrescriptionCount_span').html(JSONData.expected_prescriptions_count);
                $('#convertedPrescriptionsCount_span').html(JSONData.converted_prescriptions_count);
                $('#outPeopleCount_span').html(JSONData.out_people_count);
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
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
                .endOf('month')
            ]
        }
    }, cb);

    cb(start, end);

});
</script>

<!-- end page content -->
<script type="text/javascript">
function getdetails(c_id, d_id, li_id) {

    var startDate = $('.startDate').val();
    var endDate = $('.endDate').val();


    // add 'active' class for active li
    $("#" + li_id).addClass("selected");

    var get_selected_id = $("#" + li_id).attr("id");

    // refresh classes for list view
    $("#" + $("#li_holder_tb").val()).removeClass("selected");
    $("#li_holder_tb").val(li_id);

    if (get_selected_id != "all") {
        $("#reg_td").hide();
    } else {
        $("#reg_td").show();
    }

    if (d_id == "all") {
        $('.outRow').show();
        $('.outDRow').show();
    } else {
        $('.outRow').hide();
        $('.outDRow').hide();
    }

    $('.finLoader').show();

    $.post("<?=base_url('Pharmacy_orders/getFinances')?>", {
            startDate: startDate,
            endDate: endDate,
            c_id: c_id,
            d_id: d_id
        },
        function(data) {
            var JSONData = JSON.parse(data);
            console.log(JSONData);
            $('.finLoader').hide();
            $('.finLoader').hide();
            $('.expectedRevenue').html("<i class='fas fa-rupee-sign'></i> " + JSONData.expected_revenue);
            $('.convertedRevenue').html("<i class='fas fa-rupee-sign'></i> " + JSONData.converted_revenue);
            $('.outPeople').html("<i class='fas fa-rupee-sign'></i> " + JSONData.out_people_revenue);
            $('.lostRevenue').html("<i class='fas fa-rupee-sign'></i> " + JSONData.lost_revenue);
            $('.indiscounts').html("<i class='fas fa-rupee-sign'></i> " + JSONData.indiscounts);
            $('.outdiscounts').html("<i class='fas fa-rupee-sign'></i> " + JSONData.outdiscounts);
            $('#expectedPrescriptionCount_span').html(JSONData.expected_prescriptions_count);
            $('#convertedPrescriptionsCount_span').html(JSONData.converted_prescriptions_count);
            $('#outPeopleCount_span').html(JSONData.out_people_count);
            // $('.expectedRevenue').html("<i class='fas fa-rupee-sign'></i> "+JSONData.expected_revenue);
            // $('.convertedRevenue').html("<i class='fas fa-rupee-sign'></i> "+JSONData.converted_revenue);
            // $('.outPeople').html("<i class='fas fa-rupee-sign'></i> "+JSONData.out_people_revenue);
            // $('.lostRevenue').html("<i class='fas fa-rupee-sign'></i> "+JSONData.lost_revenue);
            // $('.discountRevenue').html("<i class='fas fa-rupee-sign'></i> "+parseFloat(JSONData.discount_revenue).toFixed(2));
            // $('#expectedPrescriptionCount_span').html(JSONData.expected_prescriptions_count);
            // $('#convertedPrescriptionsCount_span').html(JSONData.converted_prescriptions_count);
            // $('#outPeopleCount_span').html(JSONData.out_people_count);
        });
}
</script>