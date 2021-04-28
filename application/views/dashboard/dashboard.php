<style type="text/css">
  .state-overview .symbol, .state-overview .value{
    border-top-left-radius: 20px;
    border-bottom-left-radius: 20px;
}

/* dashboard styling */
.analytics{
  height:100% !important;
}

.noBdr{border:none !important;}
.btmBdr{border-bottom:1px dotted #cccccc !important;}
.noPadding{padding:0px !important;}

.analytics th{
  padding: 10px;
  text-align: center;
  font-size: 14px;
  font-weight: 600;
  text-transform: uppercase;
  background: #003d5a !important;
  color: #ffffff !important;
  border:1px solid #ccc;
}
.analytics .card{
  padding: 20px !important;
  margin: 0px !important;
}
.analytics .empty{
  text-align: center;
  vertical-align: middle;
  font-size: 14px;
  color: #ccc;
  height: 100% !important;
  width: 100% !important;
}
.doctor-name {
  font-size: 13px;
}
.analytics .empty i{
  font-size: 60px;
  color: #ccc;
}
.analyticsLeftPane{
  vertical-align: top !important;
}
.analyticsLeftPane ul{
  padding:0px;
  margin:0px;
}
.analyticsLeftPane ul li{
  list-style: none;
  border-bottom: 1px dotted #ccc;
  padding:10px 15px;
  text-align: left;
  cursor: pointer;
}
.analyticsLeftPane ul li.selected{
  background: #f5f5f5;
  font-weight:600;
}

.finances td{
  text-align: center;
  font-weight: 500;
  font-size: 14px;
  color: #666666;
  text-transform: uppercase;
  line-height: 20px;
  height: 100px;
}
.finances .amt{
  font-size: 25px;
  font-weight: 600;
  color: #003d5a;
  line-height: 30px;
}
</style>

<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?=$this->session->userdata('clinic_name')?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li class="active">Dashboard</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-12 analytics">
        <div class="card">
            <table cellpadding="0" cellspacing="0" class="table noPadding">
                <tr>
                    <!-- Doctors & Finances Dashboards -->
                    <td width="100%" class="noBdr">
                        <table class="table table-bordered">
                            <!-- Left & Right Header Panes Row -->
                            <tr>
                                <th width="40%">DOCTORS</th>
                                <th>
                                    <div class="row">
                                        <div class="col-md-5 text-center" style="margin-top: 5px;padding-right: 0px">
                                            FINANCES <i class="fas fa-spinner fa-spin finLoader"></i>
                                        </div>
                                        <div class="col-md-7 pull-right" style="padding-left: 0px !important">
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
                                <!-- Doctors Pane -->
                                <td class="analyticsLeftPane">
                                    <input type="hidden" value="all" id="dispval" />           
                                    <ul class="doctors-list" id="docList">
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
                                            <li class="all-doctors" id="<?php echo $values->doctor_id; ?>_cnt" onclick="getdetails('<?php echo $values->clinic_id; ?>','<?php echo $values->doctor_id; ?>', this.id)">
                                                <span  class="doctor-category-list-item">
                                                    <i class="fa fa-circle" style="color:<?php echo $get_doctor->color_code; ?>;font-size:13px;"></i>
                                                    <span class="doctor-name">&nbsp;<?php echo "Dr. ".strtoupper($get_doctor->first_name."  ".$get_doctor->last_name); ?></span><br>
                                                    <span style="margin-left: 25px"><small><?php echo $get_doctor->department_name; ?></small></span>
                                                    <span class="num-appoint"></span>
                                                </span>
                                            </li>
                                            <?php  
                                        } ?>
                                    </ul>
                                </td>

                                <!-- Finances Pane -->
                                <td class="analyticsRightPane" style="vertical-align: top">
                                    <div id="amt_table">
                                        <table cellspacing="0" cellpadding="0" class="table finances">
                                            <tr>
                                                <td class="noBdr btmBdr">
                                                	<span class="amt consultationsAmount"></span><br />CONSULTATIONS (<label class="conCount font-weight-bold "></label>)
                                                </td>
                                            </tr>
                                            <tr>
                                                <td id="reg_td" class="noBdr btmBdr">
                                                	<span class="amt registrationsData"></span><br />REGISTRATIONS(<label class="regCount font-weight-bold "></label>)
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="noBdr btmBdr">
                                                	<span class="amt proceduresData"></span><br />PROCEDURES(<label class="proCount font-weight-bold "></label>)
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="noBdr">
                                                	<span class="amt investigationData"></span><br />INVESTIGATION(<label class="invCount font-weight-bold "></label>)
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="noBdr">
                                                	<span class="amt totalDiscount"></span><br />DISCOUNTS
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- hidden fields -->
                                    <input type="hidden" id="clinic_id" value="<?php echo $clinic_id; ?>">
                                    <input type="hidden" id="doctor_id" value="all">
                                    <input type="hidden" id="doctor_slot" value="">
                                    <input type="hidden" id="doctor_id_list" value="<?php echo $doctor_id; ?>">
                                    <input type="hidden" id="li_holder_tb" value="all">
                                </td>
                            </tr>
                        </table>
                    </td>

                    <!-- Pharmacy & Lab Analytics -->
                    <td class="noBdr"></td>
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
            var d_id = $('#docList .selected').attr("id").split("_")[0];
            if(d_id == "" || d_id == "undefined")
            {
                d_id = "all";
            }
            else
            {
                d_id = d_id;
            }
            $.post("<?=base_url('Dashboard/getFinances')?>",
            {
                d_id:d_id,
                startDate:start_date,
                endDate:end_date
            },
            function(data){
                // console.log(data);
                var data = JSON.parse(data); 
                $('.finLoader').hide();  
                $('.consultationsAmount').html("<i class='fas fa-rupee-sign'></i> "+data.consultationAmount);
                $('.registrationsData').html("<i class='fas fa-rupee-sign'></i> "+data.registrationAmount);
                $('.proceduresData').html("<i class='fas fa-rupee-sign'></i> "+data.proAmount);
                $('.investigationData').html("<i class='fas fa-rupee-sign'></i> "+data.invAmount);
                $('.totalDiscount').html("<i class='fas fa-rupee-sign'></i> "+data.discountAmount);
                $('.conCount').html(data.conCount);
                $('.proCount').html(data.proCount);
                $('.invCount').html(data.invCount);
                $('.regCount').html(data.regCount);
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
        $("body").scrollTop(0);
        // refresh classes for list view
        $("#"+$("#li_holder_tb").val()).removeClass("selected");
        $("#li_holder_tb").val(li_id);
        if(get_selected_id != "all"){
            $("#reg_td").hide();
        }
        else{
            $("#reg_td").show();
        }
        
        $.post("<?=base_url('Dashboard/getFinances')?>",
            {
                startDate:startDate,
                endDate:endDate,
                c_id:c_id,
                d_id:d_id
            },
            function(data){
                // console.log(data);
                var data = JSON.parse(data); 
                $('.consultationsAmount').html("<i class='fas fa-rupee-sign'></i> "+data.consultationAmount);
                $('.registrationsData').html("<i class='fas fa-rupee-sign'></i> "+data.registrationAmount);
                $('.proceduresData').html("<i class='fas fa-rupee-sign'></i> "+data.proAmount);
                $('.investigationData').html("<i class='fas fa-rupee-sign'></i> "+data.invAmount);
                $('.totalDiscount').html("<i class='fas fa-rupee-sign'></i> "+data.discountAmount);
                $('.conCount').html(data.conCount);
                $('.proCount').html(data.proCount);
                $('.invCount').html(data.invCount);
                $('.regCount').html(data.regCount);
            });
    }
</script>