<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta information -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
</head>

<body style="font-family: 'Roboto', sans-serif;">
    
<?php
if(count($pdfSettings) > 0){
    if($pdfSettings->header == 0){
        ?>
        <htmlpageheader name="firstpage">

<!-- Clinic Block -->
<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
    <tr>
        <td style="width:40%">
            <img style="width:20%" alt="" src="<?php echo base_url(); ?>uploads/clinic_logos/<?php echo $clinicsInfo->clinic_logo; ?>">
        </td>
        <td style="width: 25%"></td>
        <td style="width:35%;text-align: right;">
            <span style="font-weight: bold;font-size: 12px">Address</span><br><span style="font-weight: 700;font-size: 12px"><?php echo $clinicsInfo->address; ?></span><br><span style=" font-weight: bold;font-size: 12px">Phone:</span><span style="font-weight: 700;font-size: 12px"><?php echo $clinicsInfo->clinic_phone; ?></span>
        </td>
    </tr>
</table>

<hr>

</htmlheader>

        <?php
    }
    else{
        ?>
		<div style="padding-top: <?=$pdfSettings->header_height?>px !important">&nbsp;
		</div>
        <?php
    }
    
}
else{
    ?>
    <htmlpageheader name="firstpage">

<!-- Clinic Block -->
<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
    <tr>
        <td style="width:40%">
            <img style="width:20%" alt="" src="<?php echo base_url(); ?>uploads/clinic_logos/<?php echo $clinicsInfo->clinic_logo; ?>">
        </td>
        <td style="width: 25%"></td>
        <td style="width:35%;text-align: right;">
            <span style="font-weight: bold;font-size: 12px">Address</span><br><span style="font-weight: 700;font-size: 12px"><?php echo $clinicsInfo->address; ?></span><br><span style=" font-weight: bold;font-size: 12px">Phone:</span><span style="font-weight: 700;font-size: 12px"><?php echo $clinicsInfo->clinic_phone; ?></span>
        </td>
    </tr>
</table>
<hr>

</htmlheader>

    <?php
}
?>

    <section class="main-content">
        <div class="card-head card-default" style="padding:0px 10px; ">
            <table cellspacing="0" cellpadding="0" width="100%" class="headerTable">
                <tr>
                    <td style="width: 25%">
                    <header style="font-size: 12px; padding-left: 10%">
                            <p style="font-weight:bold"><?php echo ucwords(strtoupper($billingdetails->guest_name)); ?></p>
                            <!-- <small><?php echo DataCrypt($billingdetails->guest_mobile, 'decrypt'); ?></small> -->
                            <p><?=$billingdetails->age." ".$billingdetails->age_unit?> <?=$billingdetails->gender?></p>
                            <?php
                            if($billingdetails->referred_by != ""){
                                ?>
                                <p><span style="font-weight:bold">Referred By :</span> <?=$billingdetails->referred_by?></p>
                                <?php
                            }
                            ?>
                        </header>
                    </td>
                    <td style="text-align:right">
                        <p style="font-size:12px"><span style="font-weight:bold">Bill Date :</span> <?=date('d-m-Y', strtotime($billingdetails->created_date_time))?></p>   
                        <p style="font-size:12px"><span style="font-weight:bold">Report Date :</span> <?=date('d-m-Y', strtotime($reportsInfo->authenticated_date_time))?></p>
                    </td>
                </tr>
            </table>
        </div>
        <hr>
        <h5 style="padding:10px;padding-top:2px !important;text-decoration:underline;text-transform:uppercase;font-weight:bold;text-align:center"><?=$clinicLabPackgesInfo[0]->department_name?> REport</h5>
        <h5 style="padding:10px;text-decoration:underline;text-transform:uppercase;font-weight:bold;text-align:center"><?=$reportsInfo->mian_heading?></h5>
        <!-- <table class="table" style="width:100%"> -->
            <?php
            foreach($clinicLabPackgesInfo as $value){
                ?>
                
        <div style="width:100%;display:inline">
                <?php
                if($value->investigation_name == ""){
                        $heading = explode(";", $value->heading);
                        if(count($heading) > 1){
                            
                            if(trim($heading[0]) != $last_heading){
                                ?>
                                <div style="display:flex !important;width:100%">
                                    <!-- <div style="width:35%;float:left">&nbsp;</div> -->
                                    <div style="width:50%;float:left">
                                        <h5 style="font-weight:bold;margin:0;margin-top:20px;margin-bottom:20px"><u><?=$heading[0]?></u></h5>
                                    </div>
                                    <div style="width:32%;float:left">
                                        <h5 style="font-weight:bold;margin:0px;margin-bottom:20px;margin-top:20px"><u><?=$heading[1]?></u></h5>									
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        else{
                            if(trim($heading[0]) != ""){
                                if(trim($heading[0]) != $last_heading){
        
                                    ?>
                                    <div style="display:flex !important;width:100%">
                                    <div style="width:35%;float:left">
                                        <h5 style="font-weight:bold;margin:0px;margin-bottom:20px;margin-top:20px"><u><?=$value->heading?></u></h5>
                                    </div>
                                    <?php
                                    if($value->inv_right != ""){
                                        ?>
                                        <div style="width:32%;float:left">
                                        <h5 style="font-weight:bold;margin:0px;margin-bottom:20px;margin-top:20px"><u>Right</u></h5>
                                        </div>
                                        <?php
                                    }
                                    if($value->inv_left != ""){
                                        ?>
                                        <div style="width:32%;float:right">
                                        <h5 style="font-weight:bold;margin:0px;margin-bottom:20px;margin-top:20px"><u>Left</u></h5>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    </div>
                                    <?php
                                }
                            }
                        }

                    ?>
                    <?php
                    if($value->g_e == "1")
                    {
                        ?>
                            <div style="width:100%"><span><?=getLabPatientReports($value->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_package_line_item_value')?></span></div>
                        <?php
                    }
                    elseif($value->g_e == "2")
                    {
                        ?>
                            <!-- <div style="width:30%;float:left">&nbsp;</div> -->
                            <div style="width:100%"><span><?=getLabPatientReports($value->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_package_line_item_value')?></span></div>
                        <?php
                    }
                    elseif($value->g_e == "3")
                    {
                        ?>
                            <div style="width:100%"><span><?=getLabPatientReports($value->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_package_line_item_value')?></span></div>
                        <?php
                    }
                    elseif($value->g_e == "4")
                    {
                        if($value->inv_right != ""){
                            ?>
                            <div style="width:32%;float:left"><span><?=getLabPatientReports($value->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_inv_right')?></span></div>
                            <?php
                        }
                        if($value->inv_left != ""){
                            ?>
                            <div style="width:32%;float:right"><span><?=getLabPatientReports($value->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_inv_left')?></span></div>
                            <?php
                        }
                    }
                    elseif($value->g_e == "43")
                    {
                        if($value->inv_right != ""){
                            ?>
                            <div style="width:50%;float:left"><span><?=getLabPatientReports($value->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_inv_right')?></span></div>
                            <?php
                        }
                        if($value->inv_left != ""){
                            ?>
                            <div style="width:50%;float:right"><span><?=getLabPatientReports($value->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_inv_left')?></span></div>
                            <?php
                        }
                    }
                    ?>
                    <?php
                    
                }
                elseif(strtolower($value->investigation_name) == "impression"){
                    ?>
                    <div style="width:100%">
                        <h5 style="font-weight:bold;margin:0;margin-top:20px;margin-bottom:20px;font-style:italic">IMPRESSION</h5>
                    </div>
                    <div style="width:100%">
                        <span style="font-style:italic;font-weight:bold"><?=getLabPatientReports($value->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_package_line_item_value')?></span>
                    </div>
                    <?php
                }
                else{
                    ?>
                        <?php
                        $heading = explode(";", $value->heading);
                        if(count($heading) > 1){
                            
                            if(trim($heading[0]) != $last_heading){
                                ?>
                                <div style="display:flex !important;width:100%">
                                    <!-- <div style="width:35%;float:left">&nbsp;</div> -->
                                    <div style="width:50%;float:left">
                                        <h5 style="font-weight:bold;margin:0;margin-top:20px;margin-bottom:20px"><u><?=$heading[0]?></u></h5>
                                    </div>
                                    <div style="width:32%;float:left">
                                        <h5 style="font-weight:bold;margin:0px;margin-bottom:20px;margin-top:20px"><u><?=$heading[1]?></u></h5>									
                                    </div>
                                </div>
                                <?php
                            }
                            
                        }
                        else{
                            if(trim($heading[0]) != ""){
                                if(trim($heading[0]) != $last_heading){
        
                                    ?>
                                    <div style="display:flex !important;width:100%">
                                    <div style="width:35%;float:left">
                                        <h5 style="font-weight:bold;margin:0px;margin-bottom:20px;margin-top:20px"><u><?=$value->heading?></u></h5>
                                    </div>
                                    <?php
                                    if($value->inv_right != ""){
                                        ?>
                                        <div style="width:32%;float:left">
                                        <h5 style="font-weight:bold;margin:0px;margin-bottom:20px;margin-top:20px"><u>Right</u></h5>
                                        </div>
                                        <?php
                                    }
                                    if($value->inv_left != ""){
                                        ?>
                                        <div style="width:32%;float:right">
                                        <h5 style="font-weight:bold;margin:0px;margin-bottom:20px;margin-top:20px"><u>Left</u></h5>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    </div>
                                    <?php
                                }
                            }
                        }

                    ?>
                    <div style="width:100%;display:flex">
                    <?php
                    if(count($heading) == 1)
                    {
                        ?>
                        <div style="width:30%;float:left"><span style="font-size:14px"><?=$value->investigation_name?></span></div>
                        <?php
                    }
                    ?>
                    
                    <?php
                    $min_max = "";
                    if($value->normal_range != ""){
                        $min_max = $value->normal_range;
                    }
                        
                    if($value->g_e == "1")
                    {
                        if($min_max == ""){
                            if($value->units != ""){
                                ?>
                                    <div style="width:8%;float:left"><span><?=getLabPatientReports($value->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_package_line_item_value')?></span></div>
                                    <div style="width:20%;float:left"><span><?=$value->units?></span></div>
                                    <div style="width:20%;float:left"><span><?=$min_max?></span></div>
                                <?php
                            }
                            else{
                                ?>
                                    <div style="width:65%;float:left"><span><?=getLabPatientReports($value->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_package_line_item_value')?></span></div>
                                    <!-- <div style="width:20%;float:left"><span><?=$value->units?></span></div>
                                    <div style="width:20%;float:left"><span><?=$min_max?></span></div> -->
                                <?php
                            }
                            
                        }
                        else{
                            ?>
                                <div style="width:8%;float:left"><span><?=getLabPatientReports($value->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_package_line_item_value')?></span></div>
                                <div style="width:20%;float:left"><span><?=$value->units?></span></div>
                                <div style="width:20%;float:left"><span><?=$min_max?></span></div>
                            <?php
                        }
                        
                    }
                    elseif($value->g_e == "2")
                    {
                        ?>
                        <div style="width:65%;float:left"><span><?=getLabPatientReports($value->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_package_line_item_value')?></span></div>
                        <!-- <div style="width:20%;float:left"><span><?=$value->units?></span></div>
                        <div style="width:20%;float:left"><span><?=$min_max?></span></div> -->
                        <?php
                    }
                    elseif($value->g_e == "3")
                    {
                        ?>
                        <div style="width:65%;float:left"><span><?=getLabPatientReports($value->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_package_line_item_value')?></span></div>
                        <!-- <div style="width:20%;float:left"><span><?=$value->units?></span></div>
                        <div style="width:20%;float:left"><span><?=$min_max?></span></div> -->
                        <?php
                    }
                    elseif($value->g_e == "4")
                    {
                        if($value->inv_right != ""){
                            ?>
                            <div style="width:32%;float:left"><span><?=getLabPatientReports($value->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_inv_right')?></span></div>
                            <?php
                        }
                        if($value->inv_left != ""){
                            ?>
                            <div style="width:32%;float:right"><span><?=getLabPatientReports($value->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_inv_left')?></span></div>
                            <?php
                        }
                    }
                    elseif($value->g_e == "43")
                    {
                        if($value->inv_right != ""){
                            ?>
                            <div style="width:50%;float:left"><span><?=getLabPatientReports($value->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_inv_right')?></span></div>
                            <?php
                        }
                        if($value->inv_left != ""){
                            ?>
                            <div style="width:50%;float:right"><span><?=getLabPatientReports($value->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_inv_left')?></span></div>
                            <?php
                        }
                    }
                        ?>
                    <?php
                }
                $last_heading = $heading[0];
                ?>
                <?php
                $val = $this->db->query("select * from clinic_lab_packages cp,clinic_lab_package_line_items cpl where cp.clinic_lab_package_id=cpl.clinic_lab_package_id and cpl.parent='".$value->clinic_lab_package_line_item_id."' order by cpl.positions ASC")->row();
                if(count($val) > 0){
                    $min_max = "";
                    ?>
                    <!-- <div style="display:inline-block"> -->
                    <div style="width:10%;float:left">
                        <h5 style="margin:0px;"><?=$val->investigation_name?></h5>
                    </div>
                    <?php
                    if($val->g_e == "1")
                    {
                        if($min_max == ""){
                            if($val->units != ""){
                                ?>
                                    <div style="width:8%;float:left"><span><?=getLabPatientReports($val->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_package_line_item_value')?></span></div>
                                    <div style="width:20%;float:left"><span><?=$val->units?></span></div>
                                    <div style="width:20%;float:left"><span><?=$min_max?></span></div>
                                <?php
                            }
                            else{
                                ?>
                                    <div style="width:65%;float:left"><span><?=getLabPatientReports($val->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_package_line_item_value')?></span></div>
                                    <!-- <div style="width:20%;float:left"><span><?=$val->units?></span></div>
                                    <div style="width:20%;float:left"><span><?=$min_max?></span></div> -->
                                <?php
                            }
                            
                        }
                        else{
                            ?>
                                <div style="width:8%;float:left"><span><?=getLabPatientReports($val->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_package_line_item_value')?></span></div>
                                <div style="width:20%;float:left"><span><?=$val->units?></span></div>
                                <div style="width:20%;float:left"><span><?=$min_max?></span></div>
                            <?php
                        }
                        
                    }
                    elseif($val->g_e == "2")
                    {
                        ?>
                        <div style="width:65%;float:left"><span><?=getLabPatientReports($val->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_package_line_item_value')?></span></div>
                        <!-- <div style="width:20%;float:left"><span><?=$val->units?></span></div>
                        <div style="width:20%;float:left"><span><?=$min_max?></span></div> -->
                        <?php
                    }
                    elseif($val->g_e == "3")
                    {
                        ?>
                        <div style="width:65%;float:left"><span><?=getLabPatientReports($val->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_package_line_item_value')?></span></div>
                        <!-- <div style="width:20%;float:left"><span><?=$val->units?></span></div>
                        <div style="width:20%;float:left"><span><?=$min_max?></span></div> -->
                        <?php
                    }
                    elseif($val->g_e == "4")
                    {
                        if($val->inv_right != ""){
                            ?>
                            <div style="width:32%;float:left"><span><?=getLabPatientReports($val->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_inv_right')?></span></div>
                            <?php
                        }
                        if($val->inv_left != ""){
                            ?>
                            <div style="width:32%;float:right"><span><?=getLabPatientReports($val->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_inv_left')?></span></div>
                            <?php
                        }
                    }
                    elseif($val->g_e == "43")
                    {
                        if($val->inv_right != ""){
                            ?>
                            <div style="width:50%;float:left"><span><?=getLabPatientReports($val->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_inv_right')?></span></div>
                            <?php
                        }
                        if($val->inv_left != ""){
                            ?>
                            <div style="width:50%;float:right"><span><?=getLabPatientReports($val->clinic_lab_package_line_item_id,$reportsInfo->lab_patient_report_id,'patient_inv_left')?></span></div>
                            <?php
                        }
                    }
                }
                
                    ?>
                <?php
            
            ?>
            </div>
            <?php
            }
            ?>
        <!-- </table> -->
        </div>

        <div style="width:50%;float:left;margin-top:50px">
            <h6 style="font-weight:bold">LAB INCHARGE</h6>
        </div>
        <div style="width:50%;">
            <h6 style="font-weight:bold;text-align:right">CONSULTANT</h6>
        </div>

    </section>

<?php   
if($pdfSettings->footer==1)
{
    $style = "margin-bottom: ".$pdfSettings->footer_height."px !important;";
}
?>
    <htmlpagefooter name="footer">
        <table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;margin-bottom:90px;<?=$style?>">
            <tr>
                <td style="font-size:12px; text-align: left;">
                    Powered by umdaa.co
                </td>
            </tr>
        </table>
    </htmlpagefooter>
</body>

</html>