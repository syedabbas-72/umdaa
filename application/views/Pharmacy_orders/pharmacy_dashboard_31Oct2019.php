<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-right">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="#"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li> 
            <li><a class="parent-item" href="#">PHARMACY</a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>         
            <li class="active">DASHBOARD</li>
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
                                            FINANCES <i class="fas fa-spinner fa-spin finLoader"></i>
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
                                <td class="analyticsRightPane" style="vertical-align: top">
                                    <div id="amt_table">
                                        <table cellspacing="0" cellpadding="0" class="table finances">
                                            <tr>
                                                <td class="noBdr btmBdr">
                                                    <span class="amt expectedRevenue">
                                                        <i class='fas fa-rupee-sign'></i> 
                                                        <?php 
                                                        if($expected_revenue){
                                                            echo $expected_revenue;
                                                        }else{
                                                            echo 0.00;
                                                        } 
                                                        ?>
                                                    </span><br />
                                                    EXPECTED REVENUE (From <span id="expectedPrescriptionCount_span"><?php echo $prescriptions_count; ?></span> prescription(s))
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="noBdr">
                                                    <span class="amt convertedRevenue">
                                                        <i class='fas fa-rupee-sign'></i> 
                                                        <?php 
                                                        if($converted_revenue){
                                                            echo $converted_revenue;
                                                        }else{
                                                            echo 0.00;
                                                        } 
                                                        ?>
                                                    </span><br />
                                                    CONVERTED REVENUE (From <span id="convertedPrescriptionsCount_span"><?php echo $billing_count;?></span> Prescription(s))
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="noBdr">
                                                    <span class="amt outPeople">
                                                        <i class='fas fa-rupee-sign'></i> 
                                                        <?php 
                                                        if($out_people_revenue){
                                                            echo $out_people_revenue;
                                                        }else{
                                                            echo 0.00;
                                                        } 
                                                        ?>
                                                    </span><br />
                                                    OUT PEOPLE (Outside <span id="outPeopleCount_span"><?php echo $out_people_count; ?></span> people)
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="noBdr">
                                                    <span class="amt lostRevenue">
                                                        <i class='fas fa-rupee-sign'></i> 
                                                        <?php 
                                                        if($lost_revenue){
                                                            echo $lost_revenue;
                                                        }else{
                                                            echo 0.00;
                                                        } 
                                                        ?>
                                                    </span><br />
                                                    LOST AMOUNT (From unconverted prescriptions)                                                  
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
  $(document).ready(function(){    
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
            $('.finLoader').show();  

            $.post("<?=base_url('Pharmacy_orders/getFinances')?>",
            {
                d_id:'all',
                startDate:start_date,
                endDate:end_date
            },
            function(data){
                console.log(data);
                var data = data.split("*"); 
                $('.finLoader').hide();  
                $('.expectedRevenue').html("<i class='fas fa-rupee-sign'></i> "+data[0]);
                $('.convertedRevenue').html("<i class='fas fa-rupee-sign'></i> "+data[1]);
                $('.outPeople').html("<i class='fas fa-rupee-sign'></i> "+data[2]);
                $('.lostRevenue').html("<i class='fas fa-rupee-sign'></i> "+data[3]);
                $('#expectedPrescriptionCount_span').html(data[4]);
                $('#convertedPrescriptionsCount_span').html(data[5]);
                $('#outPeopleCount_span').html(data[6]);
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

<!-- end page content -->
<script type="text/javascript">
    function getdetails(c_id,d_id,li_id){

        var startDate = $('.startDate').val();
        var endDate = $('.endDate').val();


        // add 'active' class for active li
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

        $('.finLoader').show();  
        
        $.post("<?=base_url('Pharmacy_orders/getFinances')?>",
        {
            startDate:startDate,
            endDate:endDate,
            c_id:c_id,
            d_id:d_id
        },
        function(data){
          console.log(data);
          var data = data.split("*");
          $('.finLoader').hide();  
          $('.expectedRevenue').html("<i class='fas fa-rupee-sign'></i> "+data[0]);
          $('.convertedRevenue').html("<i class='fas fa-rupee-sign'></i> "+data[1]);
          $('.outPeople').html("<i class='fas fa-rupee-sign'></i> "+data[2]);
          $('.lostRevenue').html("<i class='fas fa-rupee-sign'></i> "+data[3]);
          $('html').scrollTop(0);
      });
    }
</script>