<style>
.container {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  min-height: 95vh;
}

.drawing-area {
  box-shadow: 0 0 6px 0 #999;
}

.clear-button {
  margin: 2em;
  font-size: 16px;
}

</style>
<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li>Lab&nbsp;<i class="fa fa-angle-right"></i></li>
            <li><a href="../orders">Orders&nbsp;<i class="fa fa-angle-right"></i></a></li>
            <li class="active">Order Info &amp; Status</li>
        </ol>
    </div>
</div>

<!-- Warning Modal Starts -->
<div class="modal fade" id="warningModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h4>Report Warning</h4>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <h4 class="text-center">Payment is still pending. Are you sure you want to continue to print the Report?</h4>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="#" class="btn btn-default" data-dismiss="modal">Cancel</a>&emsp;
                            <a target="blank" class="btn btn-danger print_url">Print</a>&emsp;
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Warning Modal Ends -->

<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-head card-default">
                    <table cellspacing="0" cellpadding="0" width="100%" class="headerTable">
                        <tr>
                            <td style="width: 25%">
                                <header>
                                    <?php echo ucwords($billing_info->guest_name); ?><br>
                                    <small><?php echo $billing_info->guest_mobile; ?></small>        
                                </header>
                            </td>
                            <?php if($billing_info->doctor_id != 0){ ?>
                                <td style="width: 25%">
                                    <?php if($billing_info->doctor_id != '' || $billing_info->doctor_id != 0){ ?>
                                        <header>
                                            <?php echo "Dr. ".ucwords($billing_info->doc_first_name." ".$billing_info->doc_last_name); ?><br>
                                            <small><?php echo ucwords($billing_info->department_name); ?></small>
                                        </header>    
                                    <?php } ?>
                                </td>
                                <td style="width:25%">
                                    <?php if($billing_info->appointment_id != '' || $billing_info->appointment_id != 0){ ?>
                                        <header>
                                            Appointment On <?php echo date('d M Y', strtotime($billing_info->appointment_date)); ?><br>
                                            <small><?php echo date('H:i A', strtotime($billing_info->appointment_time_slot)); ?></small>
                                        </header>
                                    <?php } ?>
                                </td>
                            <?php } ?>   
                            <td style="width: 25%" class="text-right">
                                <div>
                                    <?php
                                    if($billing_info->payment_status == 0)
                                    {
                                        ?>
                                        <a href="<?=base_url('Lab/pay_osa/'.$billing_info->billing_id)?>" class="btn btn-danger">Pay Pending Amount</a>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <input type="hidden" id="lineItemNo_tb" value="<?=count($billing_line_items)?>">
                                <input type="hidden" id="billing_id_tb" value="<?=$billing_info->billing_id?>">
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="card-body"> 
                    <table class="table customTable">
                        <thead>
                            <tr>
                                <th style="width: 5%;" class="text-center">S.No.</th>
                                <th style="width: 35%">Investigation / Item Code</th>
                                <!-- <th style="width: 15%" class="text-center">Container</th> -->
                                <th style="width: 25%" class="text-center">Report Status</th>
                                <th style="width: 25%" class="text-center">Status</th> 
                                                             
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $sampleCollectedNo = 0;
                            foreach($billing_line_items as $line_item) {
                                if($billing_info->payment_status == 0)
                                {
                                    $class = "data-toggle='modal' data-target='#warningModal' class='warningPrint'";
                                    $href = "#";
                                }
                                else
                                {
                                    $class = "";
                                    // $href = "../view_report/".$line_item['billing_id']."/".$line_item['investigation_id'];
                                    $href = base_url('Lab/view_report/'.$line_item['billing_id']."/".$line_item['investigation_id']);
                                }
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++; ?></td>
                                    <td><span><?php echo strtoupper($line_item['investigation'])." <span class='code'>".$line_item['item_code']."</span>"; ?></td>
                                    <!-- <td class="text-center">-NA-</td> -->
                                    <td class="text-center">
                                        <?php if($line_item['report_status'] == "SC"){ ?>
                                            <small class="pending" id="<?=$line_item['investigation_status_id']?>_small">for Sample Collection</small>
                                        <?php }else if($line_item['report_status'] == "LT"){ ?>
                                            <small class="collected" id="<?=$line_item['investigation_status_id']?>_small">in Laboratory Testing</small>
                                        <?php }else if($line_item['report_status'] == "ST"){ ?>
                                            <small class="pending" id="<?=$line_item['investigation_status_id']?>_small">for Scan/Test</small>
                                        <?php }else if($line_item['report_status'] == "PR"){ ?>
                                            <small class="collected" id="<?=$line_item['investigation_status_id']?>_small">Processing Results</small>
                                        <?php }else if($line_item['report_status'] == "RE"){ ?>
                                            <small class="pending" id="<?=$line_item['investigation_status_id']?>_small">Report Entry</small>
                                        <?php }else if($line_item['report_status'] == "Auth"){ ?>
                                            <small class="pending" id="<?=$line_item['investigation_status_id']?>_small">for Authentication</small>
                                        <?php }else if($line_item['report_status'] == "RDY"){ ?>
                                            <small class="collected" id="<?=$line_item['investigation_status_id']?>_small">Ready for Print</small>            
                                        <?php } ?>    
                                    </td>
                                    <td class="text-center actions">
                                        <?php if($line_item['report_status'] == 'LT' || $line_item['report_status'] == 'PR' ||  $line_item['report_status'] == 'RE') { ?>
                                            <?php if(getPropertyAccess('Report Entry')) {  // Check Property Access ?>
                                                <i class="fas fa-file-medical-alt rupeeSmall" onclick="openpopup('<?=$line_item['clinic_investigation_id']?>','<?=$billing_info->billing_id?>','<?=$line_item['investigation_id']?>','<?=$line_item['investigation_status_id']?>','<?=$line_item['report_entry_status']?>','<?=$line_item['investigation']?>')"></i>
                                            <?php } ?>
                                        <?php }else if($line_item['report_status'] == 'Auth'){ ?>
                                            <?php if(getPropertyAccess('Report Authentication')) {  // Check Property Access ?>
                                                <i class="fas fa-file-signature rupeeSmall" onclick="openpopup('<?=$line_item['clinic_investigation_id']?>','<?=$billing_info->billing_id?>','<?=$line_item['investigation_id']?>','<?=$line_item['investigation_status_id']?>','<?=$line_item['report_entry_status']?>','<?=$line_item['investigation']?>')"></i>
                                            <?php } ?>
                                        <?php }else if($line_item['report_status'] == 'RDY'){ ?>
                                            <?php if(getPropertyAccess('Report Printing')) {  // Check Property Access ?>
                                                <a href="<?=$href?>" id="<?=$billing_info->billing_id."*".$line_item['investigation_id']?>" <?=$class?> ><i class="fas fa-print rupeeSmall <?=($billing_info->payment_status==0)?'text-danger':''?>"></i></a>
                                            <?php } ?>
                                        <?php } ?>

                                        <?php if(getPropertyAccess('Sample Collection') || getPropertyAccess('Radiology')) {  // Check Property Access ?>
                                            <input type="checkbox" <?php if($line_item['report_status'] == "SC" || $line_item['report_status'] == "ST") { echo 'enabled'; }else{ echo 'checked disabled'; } ?> id="<?=$line_item['investigation_status_id']?>_cb" onchange="return reportStatusUpdate('<?=$line_item['investigation_status_id']?>','<?=$line_item['template_type']?>')"><?php echo ($line_item['template_type'] == "Excel") ? '<i class="fas fa-vial sampleSmall"></i>' : '<i class="fas fa-radiation sampleSmall"></i>'; ?>   
                                        <?php } ?>      
                                    </td>                                    
                                </tr>
                            <?php } ?>
                            <input type="hidden" id="sampleCollectedNo_tb" value="<?=$sampleCollectedNo?>">
                            <tr>
                                <td colspan="3"></td>
                                <td class="text-center">
                                    <a href="../orders"><input type="button" class="customBtn" value="Close"></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<div>

<input type="hidden"  class="js-color-picker  color-picker">
<input type="hidden" class="js-line-range" min="1" max="72" value="1">
<!-- <label class="js-range-value">1</label>Px -->

<!-- <canvas class="js-paint  paint-canvas" id="signature" width="400" height="150" style="  border: 1px black solid;
  display: block;
  margin: 1rem;">
</canvas> -->
<!-- <button type="submit" id="clear-button">Clear</button>
<button type="submit" id="clear-button" onclick="signature();">Save</button> -->
    <!-- <i class="fa fa-trash pencil" aria-hidden="true" id="clear-button"></i> </canvas> -->
</div>

<div class="modal fade" id="excelTemplate_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="rgba(0,0,0,0.8)">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="pull-left">
                    <span></span>
                </div>
                <div class="pull-right">
                    <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>    
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-10">
                        <form method="POST" action="<?php echo base_url('Lab/templates_input_save'); ?>" role="form" style="background:#ffffff">
                            <input type="hidden" name="patient_lab_reports[patient_id]" value="<?php echo $billing_info->patient_id; ?>" />
                            <input type="hidden" name="patient_lab_reports[appointment_id]" value="<?php echo $billing_info->appointment_id; ?>" />
                            <input type="hidden" name="patient_lab_reports[doctor_id]" value="<?php echo $billing_info->doctor_id; ?>" />
                            <input type="hidden" name="patient_lab_reports[guest_name]" value="<?php echo $billing_info->guest_name; ?>" />
                            <input type="hidden" name="patient_lab_reports[guest_mobile]" value="<?php echo $billing_info->guest_mobile; ?>" />
                            <input type="hidden" name="patient_lab_reports[billing_id]" value="<?php echo $billing_info->billing_id; ?>" />
                            <input type="hidden" name="patient_lab_reports[template_type]" value="excel" />
                            <table id="template_excel" class="table dt-responsive customTable">
                                <!-- Here it renders the lab investigation parameters -->
                            </table>
                            <!-- <button type="button" class="btn customBtn" onclick="upNdown('up','template_excel');">&ShortUpArrow;</button>
                            <button type="button" class="btn customBtn" onclick="upNdown('down','template_excel');">&ShortDownArrow;</button> -->
                            <div class="modal-footer">
                                <div class="pull-left text-left">
                                    <input type="hidden" name="report_status" value="0">
                                    <div  id="excel_entry_status_div" class="form-group text-left">
                                        <input type="hidden" id="excel_report_entry_status_tb" name="report_entry_status" value="0">
                                        <input id="excel_report_entry_status_cb" type="checkbox" value="1" name="report_entry_status">
                                        <label id="report_entry_status_lbl" for="excel_report_entry_status_cb" class="text-left">All entries are done</label>
                                    </div>
                                    <div id="excel_authentication_div" class="form-group text-left">
                                        <input type="hidden" name="report_authentication" value="0">
                                        <input id="excel_report_authentication_cb" type="checkbox" value="1" name="report_authentication">
                                        <label id="report_authentication_lbl" for="excel_report_authentication_cb" class="text-left">I authenticate the above results</label>
                                    </div>
                                </div>
                                <div class="pull-right">
                                    <input type="submit" class="btn btn-primary" id="block_submit" value="Save" onclick="return confirmSubmit();">
                                </div>              
                            </div>                
                        </form>
                    </div>
                    <div class="col-md-2 mt-5 pt-5">
                        <br><br>
                        <button type="button" class="btn customBtn" onclick="upNdown('up','template_excel');">&ShortUpArrow;</button>
                        <button type="button" class="btn customBtn" onclick="upNdown('down','template_excel');">&ShortDownArrow;</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="generalTemplate_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="rgba(0,0,0,0.8)">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="pull-left">
                    &nbsp;
                </div>
                <div class="pull-right">
                    <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>    
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form method="POST" action="<?php echo base_url('Lab/templates_input_save'); ?>" role="form" class="form" style="background:#ffffff">
                            <input type="hidden" name="patient_lab_reports[patient_id]" value="<?php echo $billing_info->patient_id; ?>" />
                            <input type="hidden" name="patient_lab_reports[appointment_id]" value="<?php echo $billing_info->appointment_id; ?>" />
                            <input type="hidden" name="patient_lab_reports[doctor_id]" value="<?php echo $billing_info->doctor_id; ?>" />
                            <input type="hidden" name="patient_lab_reports[guest_name]" value="<?php echo $billing_info->guest_name; ?>" />
                            <input type="hidden" name="patient_lab_reports[guest_mobile]" value="<?php echo $billing_info->guest_mobile; ?>" />
                            <input type="hidden" name="patient_lab_reports[billing_id]" value="<?php echo $billing_info->billing_id; ?>" />
                            <input type="hidden" name="patient_lab_reports[template_type]" value="general" />
                            <table id="template_general" class="table customTable">
                                <!-- Here it renders the lab investigation parameters -->
                            </table>
                            <button type="button" class="btn customBtn" onclick="upNdown('up','template_general');">&ShortUpArrow;</button>
                            <button type="button" class="btn customBtn" onclick="upNdown('down','template_general');">&ShortDownArrow;</button>
                            <div class="modal-footer">
                                <div class="pull-left text-left">
                                    <input type="hidden" name="report_status" value="0">
                                    <div id="general_entry_status_div" class="form-group text-left">
                                        <input type="hidden" id="general_report_entry_status_tb" name="report_entry_status" value="0">
                                        <input id="general_report_entry_status_cb" type="checkbox" value="1" name="report_entry_status">
                                        <label id="report_entry_status_lbl" for="general_report_entry_status_cb" class="text-left">All entries are done</label>
                                    </div>
                                    <div id="general_authentication_div" class="form-group text-left">
                                        <input type="hidden" name="report_authentication" value="0">
                                        <input id="general_report_authentication_cb" type="checkbox" value="1" name="report_authentication">
                                        <label id="report_authentication_lbl" for="general_report_authentication_cb" class="text-left">I authenticate the above results</label>
                                    </div>
                                </div>
                            
                                <div class="pull-right">
                                    <input type="submit" class="btn btn-primary" id="block_submit" value="Save" onclick="return confirmSubmit();">
                                </div>            
                            </div>
                        </form>                            
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Excel Parameter Modal Creation -->
<div class="modal fade" id="newExcelParameter_modal" tabindex="-1" role="dialog" aria-labelledby="excelParamLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="pull-left">
                    <h4><span id="investigation_span"></span></h4>
                </div>
                <div class="pull-right">
                    <button type="button" class="close pull-right" onclick="return cancelModal('newExcelParameter_modal','excelTemplate_modal')"><span aria-hidden="true">&times;</span></button>    
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form method="POST" action="" role="form" class="form">
                            <div class="row page-title">
                                <div class="col-lg-12">
                                    New Parameter Information
                                    <!-- Hidden textboxes -->
                                    <input type="hidden" id="parent_clinic_investigation_id_tb" value="">
                                    <input type="hidden" id="billing_id_tb" value="">
                                    <input type="hidden" id="investigation_status_id_tb" value="">
                                    <input type="hidden" id="parent_investigation_id_tb" value="">
                                    <input type="hidden" id="report_entry_status_tb" value="">
                                    <input type="hidden" id="Investigation_tb" value="">
                                </div>
                            </div>
                            <div>
                                <table class="table customTable">
                                    <tr>
                                        <td class="form-group" style="width: 40%">
                                            <label>Parameter</label>
                                            <input type="text" class="form-control" id="parameter_tb" value="" placeholder="Specify Parameter Name">    
                                        </td>
                                        <td class="form-group" style="width: 20%">
                                            <label>Low Range</label>
                                            <input type="text" class="form-control" id="low_range_tb" value="" placeholder="Low">
                                        </td>
                                        <td class="form-group" style="width: 20%">
                                            <label>High Range</label>
                                            <input type="text" class="form-control" id="high_range_tb" value="" placeholder="Low">
                                        </td>
                                        <td class="form-group" style="width: 20%">
                                            <label>Unit</label>
                                            <?php if(count($units) > 0) {
                                                echo '<select id="units_sb" class="form-control">';
                                                echo '<option value="">Select Unit</option>';
                                                foreach($units as $rec){
                                                    if(trim($rec['units']) != '' || trim($rec['units']) != NULL){
                                                        echo '<option value="'.$rec['units'].'">'.$rec['units'].'</option>';    
                                                    }
                                                }
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="form-group">
                                            <label>Method</label>
                                            <?php if(count($methods) > 0) {
                                                echo '<select id="method_sb" class="form-control">';
                                                echo '<option value="">Select Method</option>';
                                                foreach($methods as $rec){
                                                    if(trim($rec['method']) != '' || trim($rec['method']) != NULL){
                                                        echo '<option value="'.$rec['method'].'">'.$rec['method'].'</option>';    
                                                    }
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td class="form-group">
                                            <label>Sample Type</label>
                                            <?php if(count($sample_types) > 0) {
                                                echo '<select id="sample_type_sb" class="form-control">';
                                                echo '<option value="">Select Sample Type</option>';
                                                foreach($sample_types as $rec){
                                                    if(trim($rec['sample_type']) != '' || trim($rec['sample_type']) != NULL){
                                                        echo '<option value="'.$rec['sample_type'].'">'.$rec['sample_type'].'</option>';    
                                                    }
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td colspan="2" class="form-group">
                                            <label>Other Information</label>
                                            <input type="text" id="other_information_tb" class="form-control" value="" placeholder="Specify other information, if any...">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-right">
                                            <button type="button" class="btn btn-default" onclick="return cancelModal('newExcelParameter_modal','excelTemplate_modal')">Cancel</button>
                                            <button type="button" class="btn btn-primary" onclick="return createParameter('Excel');">Create Parameter</button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </form>                            
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- New General Parameter Modal Creation -->
<div class="modal fade" id="newGeneralParameter_modal" tabindex="-1" role="dialog" aria-labelledby="generalParamLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="pull-left">
                    <h4><span id="investigation_span"></span></h4>
                </div>
                <div class="pull-right">
                    <button type="button" class="close pull-right" onclick="return cancelModal('newGeneralParameter_modal','generalTemplate_modal')"><span aria-hidden="true">&times;</span></button>    
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form method="POST" action="" role="form" class="form">
                            <div class="row page-title">
                                <div class="col-lg-12">
                                    New Parameter Information test
                                    <!-- Hidden textboxes -->
                                    <input type="hidden" id="parent_clinic_investigation_id_tb" value="">
                                    <input type="hidden" id="billing_id_tb" value="">
                                    <input type="hidden" id="investigation_status_id_tb" value="">
                                    <input type="hidden" id="parent_investigation_id_tb" value="">
                                    <input type="hidden" id="report_entry_status_tb" value="">
                                    <input type="hidden" id="Investigation_tb" value="">
                                </div>
                            </div>
                            <div>
                                <table class="table customTable">
                                    <tr>
                                        <td class="form-group" style="width: 30%">
                                            <label>Parameter</label>
                                            <input type="text" class="form-control" id="general_parameter_tb" value="" placeholder="Specify Parameter Name">    
                                        </td>
                                        <td class="form-group" style="width: 70%">
                                            <label>Remark</label>
                                            <textarea rows="multiple" name="remark" id="remark_ta" class="form-control"></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-right">
                                            <button type="button" class="btn btn-default" onclick="return cancelModal('newGeneralParameter_modal','generalTemplate_modal')">Cancel</button>
                                            <button type="button" class="btn btn-primary" onclick="return createParameter('General');">Create Parameter</button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </form>                            
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        // $('#warningModal').modal();
        $('.warningPrint').on("click",function(){
            var id = $(this).attr('id');
            var str = id.split("*");
            var url = "<?=base_url('Lab/report_pdf/')?>"+str[0]+"/"+str[1];
            $('.print_url').attr("href",url);
            // alert(url);
        });
    });
</script>


<script type="text/javascript">

    function openpopup(clinic_investigation_id, billing_id, investigation_id, investigation_status_id, report_entry_status, investigation)
    {
        console.log(clinic_investigation_id+" * "+billing_id+" * "+investigation_id+" * "+investigation_status_id+" * "+report_entry_status+" * "+investigation)
        var base_url = '<?php echo base_url(); ?>';
        $.ajax({
            url : base_url+"/Lab/get_template_info",
            method : "POST",
            data : {"clinic_investigation_id":clinic_investigation_id, "billing_id":billing_id, "investigation_id":investigation_id, "investigation_status_id":investigation_status_id, "report_entry_status":report_entry_status, "investigation":investigation},
            success : function(result) {
                result = $.trim(result);
                var a = result.split(":|:");
                if(a[0]=="excel")
                {
                    $("#template_excel").empty();
                    $("#template_excel").append(a[1]);
                    $("#excelTemplate_modal").modal();
                    if(report_entry_status == 1){
                        $("#excel_report_entry_status_tb").val(1);
                        $("#excel_entry_status_div").hide();
                        $("#excel_authentication_div").show();
                        $("#excel_consultant_remark_ta").text($("#excel_consultant_remark_tb").val());
                    }else{
                        $("#excel_entry_status_div").show();
                        $("#excel_authentication_div").hide();
                    }
                }
                else if(a[0]=="general")
                {
                    $("#template_general").empty();
                    $("#template_general").append(a[1]);
                    $("#generalTemplate_modal").modal();
                    if(report_entry_status == 1){
                        $("#general_report_entry_status_tb").val(1);
                        $("#general_entry_status_div").hide();
                        $("#general_authentication_div").show();
                        $("#general_consultant_remark_ta").text($("#general_consultant_remark_tb").val());
                    }else{
                        $("#general_entry_status_div").show();
                        $("#general_authentication_div").hide();
                    }
                }
            }
        });
    }

    function reportStatusUpdate(id, template_type){

        if($("#"+id+"_cb").is(":checked")){
            if(template_type == 'Excel'){
                report_status = 'LT';
            }else{
                report_status = 'PR';  
            }
        }else{
            if(template_type == 'Excel'){
                report_status = 'SC';
            }else{
                report_status = 'ST';  
            }   
        }

        console.log(report_status);

        lineItemCount = $("#lineItemNo_tb").val();
        sampleCollectionCount = $("#sampleCollectedNo_tb").val();

        $.ajax({
            url : base_url+"/Lab/reportStatusUpdate",
            method : "POST",
            data : {"investigation_status_id":id, "report_status":report_status},
            success : function(result) {

                console.log(result);

                result = $.trim(result);
                if(result == 'LT'){
                    $("#"+id+"_small").text('in Laboratory Testing');
                    $("#"+id+"_small").attr("class","collected");
                    // sampleCollectionCount = parseInt(sampleCollectionCount) + 1;
                }else if(result == 'SC'){
                    $("#"+id+"_small").text('for Sample Collection');
                    $("#"+id+"_small").attr("class","pending");
                    // sampleCollectionCount = parseInt(sampleCollectionCount) + 1;
                }else if(result == 'ST'){
                    $("#"+id+"_small").text('for Scan/Test');
                    $("#"+id+"_small").attr("class","pending");
                    // sampleCollectionCount = parseInt(sampleCollectionCount) - 1;
                }else if(result == 'PR'){
                    $("#"+id+"_small").text('Processing Result');
                    $("#"+id+"_small").attr("class","collected");
                    // sampleCollectionCount = parseInt(sampleCollectionCount) + 1;
                }                
            }
        });
    }

    // Confirmation before proceeding
    // function confirmSubmit() {
    //     // alert("Aggi Bro");
    //     var res = 0;

    //     if($('#excel_report_entry_status_tb').val() == 0 || $('#general_report_entry_status_tb').val() == 0){
    //         console.log('Entry Form');
    //         if($("#excel_report_entry_status_cb").is(":checked") || $("#general_report_entry_status_cb").is(":checked")){
    //             res = confirm("Put forward the lab results to Consultant Authentication!");    
    //         }else{
    //             res = true;
    //         }                
    //     }else 
    //     if($('#excel_report_entry_status_tb').val() == 1 || $('#general_report_entry_status_tb').val() == 1){
    //         console.log('Auth Form');
    //         if($("#excel_report_authentication_cb").is(":checked") || $("#general_report_authentication_cb").is(":checked")){
    //             res = confirm("Saying OK will authenticate the lab results");
    //         }else{
    //             res = true
    //         } 
    //     }

    //     if(res == true){
    //         return 1;
    //     }else{
    //         return false;
    //     }
    // }

    var index;  // variable to set the selected row index


    function excelRowSelected(tr){
        var currentRowIndex = index = tr.rowIndex;
        $(tr).addClass("selected");
        $(tr).find('.upDown').html("<i class='fas fa-chevron-circle-up pointer' onclick='upNdown(\"up\",\"template_excel\");'></i><i class='fas fa-chevron-circle-down pointer' onclick='upNdown(\"down\",\"template_excel\");'></i>");

        $("#template_excel > tr").each(function(index, tr) {
            if(index > 1){
                if(index != currentRowIndex){
                    $("#template_excel > tr").eq(index).removeClass("selected");
                    $("#template_excel > tr").eq(index).find('.upDown').html('<span><i class="fas fa-chevron-circle-up"></i><i class="fas fa-chevron-circle-down"></i></span>');
                }
            }
        });
    }

    function deleteClinicInvestigation(id)
    {
        if (confirm('Are you sure you want to delete?')) {
            // alert(id);
            $.ajax({
                url : base_url+"/Lab/deleteClinicLabItemId",
                method : "POST",
                data : {id:id},
                success : function(result) {
                    console.log(result);
                    $("#excelTemplate_modal").modal('hide');
                    if(result == 1){               
                    alert('Successfully Deleted')
                    }
                    else
                    {
                        alert('Id Not Found')
                    }
                }
            });
        }
    }

    function deleteClinicInvestigationgeneral(id)
    {
        if (confirm('Are you sure you want to delete?')) {
            // alert(id);
            $.ajax({
                url : base_url+"/Lab/deleteClinicLabItemId",
                method : "POST",
                data : {id:id},
                success : function(result) {
                    console.log(result);
                    $("#generalTemplate_modal").modal('hide');
                    if(result == 1){               
                    alert('Successfully Deleted')
                    }
                    else
                    {
                        alert('Id Not Found')
                    }
                }
            });
        }
    }

    function generalRowSelected(tr){
        var currentRowIndex = index = tr.rowIndex;
        $(tr).addClass("selected");
        $(tr).find('.upDown').html("<i class='fas fa-chevron-circle-up pointer' onclick='upNdown(\"up\",\"template_general\");'></i><i class='fas fa-chevron-circle-down pointer' onclick='upNdown(\"down\",\"template_general\");'></i>");

        $("#template_general > tr").each(function(index, tr) {
            if(index > 1){
                if(index != currentRowIndex){
                    $("#template_general > tr").eq(index).removeClass("selected");
                    $("#template_general > tr").eq(index).find('.upDown').html('<span><i class="fas fa-chevron-circle-up"></i><i class="fas fa-chevron-circle-down"></i></span>');
                }
            }
        });
    }

    function upNdown(direction, template)
    {

        console.log(template);
        var rows;

        if(template == 'template_excel'){
            rows = document.getElementById("template_excel").rows,
            parent = rows[index].parentNode;
        }else {
            rows = document.getElementById("template_general").rows,
            parent = rows[index].parentNode;
        }

        if(direction === "up")
        {
            if(index > 2){
                parent.insertBefore(rows[index],rows[index - 1]);
                // when the row go up the index will be equal to index - 1
                index--;
            }
        }

        if(direction === "down")
        {
            console.log(index);
            if(index < rows.length -1 && index > 1){
                parent.insertBefore(rows[index + 1],rows[index]);
                // when the row go down the index will be equal to index + 1
                index++;
                console.log(index);
            }
        }

        if(template == 'template_excel'){
            excel_rePosition();
        }else{
            general_rePosition();
        }
    }


    function excel_rePosition(){

        var position_update = 0;

        $("#template_excel > tr").each(function(index, tr) {
            console.log('Excel');
            if(index > 1){
                trIndex = parseInt(index)-1;
                $("#template_excel > tr").eq(index).find('.position').val(trIndex);

                var newValue = trIndex;
                var oldValue = $("#template_excel > tr").eq(index).find('.old_position').val();

                if(newValue != oldValue){
                    position_update = 1;
                }

                if(position_update > 0){
                    $("#position_update_tb").val(1);
                }else{
                    $("#position_update_tb").val(0);
                }
            }
        });
    }


    function general_rePosition(){

        var position_update = 0;

        $("#template_general > tr").each(function(index, tr) {
            console.log('General');
            if(index > 1){
                trIndex = parseInt(index)-1;
                $("#template_general > tr").eq(index).find('.position').val(trIndex);

                var newValue = trIndex;
                var oldValue = $("#template_general > tr").eq(index).find('.old_position').val();

                if(newValue != oldValue){
                    position_update = 1;
                }

                if(position_update > 0){
                    $("#position_update_tb").val(1);
                }else{
                    $("#position_update_tb").val(0);
                }
            }
        });
    }


    function newParamModal(clinic_investigation_id, billing_id = '', investigation_id = '', investigation_status_id = '', report_entry_status = '', investigation = ''){
        console.log('Modal opening event raised'+billing_id);
        $("#newExcelParameter_modal").modal('show');
        $("#excelTemplate_modal").modal('hide');
        $("#investigation_span").html(investigation);
        $("#parent_clinic_investigation_id_tb").val(clinic_investigation_id);
        $("#billing_id_tb").val(billing_id);
        $("#parent_investigation_id_tb").val(investigation_id);
        $("#investigation_status_id_tb").val(investigation_status_id);
        $("#report_entry_status_tb").val(report_entry_status);
        $("#investigation_tb").val(investigation);
    }

    function newParamGeneralModal(clinic_investigation_id, billing_id = '', investigation_id = '', investigation_status_id = '', report_entry_status = '', investigation = ''){
        console.log('Modal opening event raised');
        $("#newGeneralParameter_modal").modal('show');
        $("#generalTemplate_modal").modal('hide');
        $("#investigation_span").html(investigation);
        $("#parent_clinic_investigation_id_tb").val(clinic_investigation_id);
        $("#billing_id_tb").val(billing_id);
        $("#parent_investigation_id_tb").val(investigation_id);
        $("#investigation_status_id_tb").val(investigation_status_id);
        $("#report_entry_status_tb").val(report_entry_status);
        $("#investigation_tb").val(investigation);
    }

    function createParameter(template){
        var sdoctor_id = $('#billing_id_tb').val();
        // console.log(sdoctor_id);
        if(template == 'Excel'){
            console.log(template);
            // $("#newExcelParam_modal").modal("hide");    
            $("#newExcelParameter_modal").modal("hide");    
            $.ajax({
                url : base_url+"/Lab/createParameter",
                method : "POST",
                data : {"billing_id":$("#billing_id_tb").val(),"parent_clinic_investigation_id":$("#parent_clinic_investigation_id_tb").val(), "parent_investigation_id":$("#parent_investigation_id_tb").val(), "investigation":$("#parameter_tb").val(), "low_range":$("#low_range_tb").val(), "high_range":$("#high_range_tb").val(), "units":$("#units_sb").val(), "sample_type":$("#sample_type_sb").val(), "method":$("#method_sb").val(), "other_information":$("#other_information_tb").val()},
                success : function(result) {
                    console.log(result);
                    return false;
                    $("#newExcelParameter_modal").modal('hide');
                    location.reload();
                    openpopup($("#clinic_investigation_id_tb").val(),$("#billing_id_tb").val(),$("#investigation_id_tb").val(), $("#investigation_status_id_tb").val(), $("#report_entry_status_tb").val(), $("#investigation_tb").val());
                }
            });
        }

        if(template == 'General'){
            console.log(template);
            $("#newGeneralParameter_modal").modal("hide");  
            // $("#newGeneralParam_modal").modal("hide");    
            $.ajax({
                url : base_url+"/Lab/createParameter",
                method : "POST",
                data : {"billing_id":$("#billing_id_tb").val(),"parent_clinic_investigation_id":$("#parent_clinic_investigation_id_tb").val(), "parent_investigation_id":$("#parent_investigation_id_tb").val(), "investigation":$("#general_parameter_tb").val(), "remarks":$("#remark_ta").val()},
                success : function(result) {
                    console.log(result);
                    return false;
                    // location.reload();
                    $("#generalTemplate_modal close").click();
                    // $("#newGeneralParam_modal close").click();
                    openpopup($("#clinic_investigation_id_tb").val(),$("#billing_id_tb").val(),$("#investigation_id_tb").val(), $("#investigation_status_id_tb").val(), $("#report_entry_status_tb").val(), $("#investigation_tb").val());
                }
            });
        }
    }

    function cancelModal(closeModal, openModal){
        $("#"+closeModal).modal('hide');
        $("#"+openModal).modal();
    }

  

</script>

<script>
const paintCanvas = document.querySelector( '.js-paint' );
const context = paintCanvas.getContext( '2d' );
context.lineCap = 'round';

const colorPicker = document.querySelector( '.js-color-picker');

colorPicker.addEventListener( 'change', event => {
  
} );

const lineWidthRange = document.querySelector( '.js-line-range' );
const lineWidthLabel = document.querySelector( '.js-range-value' );

// lineWidthRange.addEventListener( 'input', event => {
//     const width = event.target.value;
//     lineWidthLabel.innerHTML = width;
//     context.lineWidth = width;
// } );
  context.strokeStyle ='#333';
   context.lineWidth = 5;
   const clearButton = document.getElementById('clear-button');
   clearButton.addEventListener('click', handleClearButtonClick);
   function handleClearButtonClick(event) {
          event.preventDefault();
          
          clearCanvas();
        }
        function clearCanvas() {
            context.clearRect(0, 0, 400, 150);
        }
let x = 0, y = 0;
let isMouseDown = false;

const stopDrawing = () => { isMouseDown = false; }
const startDrawing = event => {
    isMouseDown = true;   
   [x, y] = [event.offsetX, event.offsetY];  
}
const drawLine = event => {
    if ( isMouseDown ) {
        const newX = event.offsetX;
        const newY = event.offsetY;
        context.beginPath();
        context.moveTo( x, y );
        context.lineTo( newX, newY );
        context.stroke();
        //[x, y] = [newX, newY];
        x = newX;
        y = newY;
    }
}

paintCanvas.addEventListener( 'mousedown', startDrawing );
paintCanvas.addEventListener( 'mousemove', drawLine );
paintCanvas.addEventListener( 'mouseup', stopDrawing );
paintCanvas.addEventListener( 'mouseout', stopDrawing );

function signature()
    {
        alert("Saved Successfully");
        context.clearRect(0, 0, 400, 150);
        var canvas1 = document.getElementById("signature");   
        // var img    = canvas1.toDataURL("image/png");     
  if (canvas1.getContext) {
     var ctx = canvas1.getContext("2d");                
     var myImage = canvas1.toDataURL("image/jpeg");    
     console.log(myImage);  
  }
//   var imageElement = document.getElementById("MyPix");  
//   imageElement.src = myImage;   
    }
</script>