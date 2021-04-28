<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="#"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li> 
            <li><a class="parent-item" href="#">Lab</a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>         
            <li class="active">Dashboard</li><?php echo $expectedRevenue;?>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-12 analytics">
        <div class="card">
            <table cellpadding="0" cellspacing="0" class="table table-bordered noPadding" style="border: none;">
                <tr>
                    <!-- Doctors & Finances Dashboards -->
                    <td width="50%" class="noBdr">
                        <table class="table">
                            <!-- Left & Right Header Panes Row -->
                            <tr>
                                <th width="40%">Doctors</th>
                                <th>
                                    <div class="row">
                                        <div class="col-md-6 text-center" style="margin-top: 5px;padding-right: 0px">
                                            FINANCES <span id="loader" style="display: none"><i class="fas fa-spinner fa-spin finLoader"></i></span>
                                        </div>
                                        <div class="col-md-6 pull-right" style="padding-left: 0px !important">
                                            <div id="daterange" style="cursor: pointer; padding: 5px 10px; width: 100%;">
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
                                        <li id="all" class="all-doctors selected" onclick="getdetails('<?php echo $clinic_id; ?>','all',this.id)">
                                            <span  class="doctor-category-list-item">
                                                <i class="fa fa-circle" style="color:#ccc;font-size:13px;"></i>
                                                <span class="doctor-name">&nbsp;ALL DOCTORS</span>
                                                <span id="total_apnts" class="num-appoint"></span>
                                            </span>
                                        </li>

                                        <?php
                                        foreach ($doctors_list as $key => $values) {
                                            $get_doctor = $this->db->query("select * from doctors d inner join department dep on(d.department_id = dep.department_id) where d.doctor_id='".$values->doctor_id."'")->row();
                                            ?>
                                            <li class="all-doctors" id="<?php echo $values->doctor_id; ?>_cnt" onclick="getdetails('<?php echo $clinic_id; ?>','<?php echo $values->doctor_id; ?>', this.id)">
                                                <span  class="doctor-category-list-item">
                                                    <i class="fa fa-circle" style="color:<?php echo $get_doctor->color_code; ?>; font-size:13px;"></i>
                                                    <span class="doctor-name">&nbsp;<?php echo "Dr. ".strtoupper($get_doctor->first_name."  ".$get_doctor->last_name); ?></span><br>
                                                    <span style="margin-left: 25px"><small><?php echo $get_doctor->department_name; ?></small></span>
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
                                <td class="analyticsRightPane">
                                    <div id="amt_table">
                                        <table cellspacing="0" cellpadding="0" class="table finances">
                                            <tr>
                                                <td class="noBdr btmBdr">
                                                    <!-- <span class="amt expectedRevenue"> -->
                                                    <span class="amt">
                                                        <i class='fas fa-rupee-sign'></i> 
                                                        <?php 
                                                        // echo $expectedRevenue;
                                                        if($expectedRevenue != ''){
                                                            //  echo $expectedRevenue;
                                                             echo number_format($expectedRevenue, 2);
                                                            // echo "1122";
                                                        }else{
                                                            echo 0.00;
                                                        } 
                                                        ?>
                                                    </span><br />
                                                    <p class="text-center">EXPECTED REVENUE</p>
                                                    <small>(From prescription(s))</small>
                                                    <!-- <small>(From <span id="expectedPrescriptionCount_span"><?php echo $prescriptions_count; ?></span> prescription(s))</small> -->
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="noBdr btmBdr">
                                                    <!-- <span class="amt convertedRevenue"> -->
                                                    <span class="amt">
                                                        <i class='fas fa-rupee-sign'></i> 
                                                        <?php 
                                                        if($converted_revenue != ''){
                                                            echo $converted_revenue;
                                                        }else{
                                                            echo 0.00;
                                                        } 
                                                        ?>
                                                    </span><br />
                                                    <p class="text-center">CONVERTED REVENUE</p>
                                                    <small>(From Prescription(s))</small>
                                                    <!-- <small>(From <span id="convertedPrescriptionsCount_span"><?php echo $billing_count;?></span> Prescription(s))</small> -->
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="noBdr btmBdr">
                                                    <!-- <span class="amt lostRevenue"> -->
                                                    <span class="amt">
                                                        <i class='fas fa-rupee-sign'></i> 
                                                        <?php 
                                                        if($lost_revenue){
                                                            echo $lost_revenue;
                                                        }else{
                                                            echo 0.00;
                                                        } 
                                                        ?>
                                                    </span>
                                                    <p class="text-center">OUT STANDING AMOUNT</p>
                                                    <small>(From unconverted prescriptions)</small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="noBdr btmBdr">
                                                    <!-- <span class="amt outPeople"> -->
                                                    <span class="amt">
                                                        <i class='fas fa-rupee-sign'></i> 
                                                        <?php 
                                                        if($out_people_revenue){
                                                            echo $out_people_revenue;
                                                        }else{
                                                            echo 0.00;
                                                        } 
                                                        ?>
                                                    </span><br />
                                                    <p class="text-center">OUT PEOPLE</p>
                                                    <small>(From outside customer(s))</small>
                                                    <!-- <small>(From <span id="outPeopleCount_span"><?php echo $out_people_count; ?></span> outside customer(s))</small> -->
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="noBdr">
                                                    <!-- <span class="amt outPeople"> -->
                                                    <span class="amt">
                                                        <i class='fas fa-rupee-sign'></i> 
                                                        <?php 
                                                        if($out_people_osa){
                                                            echo $out_people_osa;
                                                        }else{
                                                            echo 0.00;
                                                        } 
                                                        ?>
                                                    </span><br />
                                                    <p class="text-center">OUTSTANDING AMOUNT</p>
                                                    <small>(From outside customer(s))</small>
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

<script src="<?php echo base_url(); ?>assets/plugins/moment/moment.min.js" ></script>
<link href="<?php echo base_url(); ?>assets/newdaterangepicker24Jul2019/daterangepicker.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url(); ?>assets/newdaterangepicker24Jul2019/daterangepicker.js"></script>
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
            $('#loader').show();  

            $.post("<?=base_url('Lab/getFinances')?>",
            {
                d_id:'all',
                startDate:start_date,
                endDate:end_date
            },
            function(data){
                console.log(data);
                // var data = data.split("*"); 

                var JSONData = JSON.parse(data);
                console.log(JSONData)
                $('#loader').hide();  
                $('.expectedRevenue').html("<i class='fas fa-rupee-sign'></i> " + JSONData.expected_revenue);
                $('.convertedRevenue').html("<i class='fas fa-rupee-sign'></i> " + JSONData.converted_revenue);
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
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end);

    });


    function getdetails(c_id,d_id,li_id){

        console.log('Get Details....');

        var startDate = $('.startDate').val();
        var endDate = $('.endDate').val();

        console.log("Start date: "+startDate);
        console.log("End date: "+endDate);

        $("#"+li_id).addClass("selected");

        var get_selected_id = $("#"+li_id).attr("id");

        // refresh classes for list view
        $("#"+$("#li_holder_tb").val()).removeClass("selected");
        $("#li_holder_tb").val(li_id);

        if(get_selected_id != "all"){
            $("#reg_td").hide();
        }else{
            $("#reg_td").show();
        }

        $('#loader').show();  

        $.post("<?=base_url('Lab/getFinances')?>",
        {
            startDate:startDate,
            endDate:endDate,
            c_id:c_id,
            d_id:d_id
        },
        function(data){
            console.log(data);
            // var data = data.split("*");
            $('#loader').hide();  

            var JSONData = JSON.parse(data);
            console.log(JSONData)
            $('#loader').hide();  
            $('.expectedRevenue').html("<i class='fas fa-rupee-sign'></i> " + JSONData.expected_revenue);
            $('.convertedRevenue').html("<i class='fas fa-rupee-sign'></i> " + JSONData.converted_revenue);
            $('.outPeople').html("<i class='fas fa-rupee-sign'></i> " + JSONData.out_people_revenue);
            $('.lostRevenue').html("<i class='fas fa-rupee-sign'></i> " + JSONData.lost_revenue);
            $('.indiscounts').html("<i class='fas fa-rupee-sign'></i> " + JSONData.indiscounts);
            $('.outdiscounts').html("<i class='fas fa-rupee-sign'></i> " + JSONData.outdiscounts);
            $('#expectedPrescriptionCount_span').html(JSONData.expected_prescriptions_count);
            $('#convertedPrescriptionsCount_span').html(JSONData.converted_prescriptions_count);
            $('#outPeopleCount_span').html(JSONData.out_people_count);
        });
    }

</script>