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
<?php

?>
<body style="font-family: 'Roboto', sans-serif;">
    <?php if($pdf_settings->header != 1){  
        ?>
        <htmlpageheader name="firstpageheader">
            <?php
        $this->load->view("reports/default_pdf_header");
            ?>
        </htmlpageheader>
        <!-- <htmlpageheader name="otherheader">
            <table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
                <tr>
                    <td style="width:40%">
                        <img style="width:50%" alt="" src="<?php echo base_url(); ?>uploads/clinic_logos/<?php echo $appointments->clinic_logo; ?>">
                    </td>
                    <td style="width: 25%"></td>
                    <td style="width:35%;text-align: right;">
                        <span style="font-weight: bold;font-size: 12px">Address</span><br><span style="font-weight: 700;font-size: 12px"><?php echo $appointments->address; ?></span><br><span style=" font-weight: bold;font-size: 12px">Phone:</span><span style="font-weight: 700;font-size: 12px"><?php echo $appointments->clinic_phone; ?></span>
                    </td>
                </tr>
            </table>
            <hr>
        </htmlpageheader> -->
    <?php }
    elseif($pdf_settings->header==1)
    {
        $style = "margin-bottom: ".$pdf_settings->header_height."px !important;";
        ?>
        <htmlpageheader name="firstpageheader">
            <table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;<?=$style?>">
                <tr>
                    <td>&nbsp;</td>
                </tr>
            </table>
            <hr>
        </htmlpageheader>
        <htmlpageheader name="otherheader">
            <table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;<?=$style?>">
                <tr>
                    <td>&nbsp;</td>
                </tr>
            </table>
            <hr>
        </htmlpageheader>
        <?php
    }
        ?>
    <!-- <table cellpadding="0" cellspacing="0" style="margin: 0px;font-size: 12px; padding: 0px; width: 100%;">
        <tr>
            <td style="width:30%">
                <?php 
                $patientName = '';
                if($appointments->title){
                    $patientName = ucwords($appointments->title).". ";
                }
                $patientName .= $appointments->pname." ".$appointments->plname;
                ?>
                <span style="font-weight: bold;"><b><?php echo strtoupper($patientName); ?></b></span><br>
                <b>Patient Id:</b>&nbsp;<?php echo $appointments->umr_no; ?><br>
                <?php 
                $moreInfo = '';
                if($appointments->gender) { 
                    $moreInfo .= ucwords(substr($appointments->gender, 0, 1));
                } 
                if($appointments->age) { 
                    if($moreInfo){
                        $moreInfo .= ", ";
                    }
                    $moreInfo .= $appointments->age." ".ucwords(substr($appointments->age_unit, 0, 1));
                }                           
                ?>
                <p><?php echo $moreInfo; ?></p>
            </td>
            <td style="width:30%;text-align: center;vertical-align: top">
                <?php if($appointments->address_line != ''){ ?>
                    <span style="font-weight: bold">Address:</span><br><p><?php echo $appointments->address_line; ?></p>
                <?php } ?>
            </td>
            <td style="width:30%;text-align: right;vertical-align: top;font-size: 13px;">
                <div style="<?=($pdf_settings->doc_details==0)?'display: none':''?>">
                <span style="font-weight: bold"><b><?php echo "DR. ".strtoupper($appointments->dfname." ".$appointments->dlname); ?></b></span><br><span><?php echo strtoupper($appointments->qualification. ", ". $appointments->department_name); ?> </span><br>
                <span><b>Reg. No:</b> &nbsp;<?php echo $appointments->registration_code; ?> </span> </div>                      
            </td>
        </tr>
    </table>
    <hr> -->

<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <div class="card-body"> 

                    <div class="col-md-12 text-center mainTestTitle" style="font-weight: bold;font-size: 16px;text-decoration: underline;padding: 10px;line-height: 22px">
                        <?=$test_name?>
                    </div>

                    <table class="table resultTable noBdr" style="width: 100%" >
                        <?php if($template_type == 'Excel'){ ?>
                        <thead>
                            <tr>
                                <th style="width: 10%; text-align: center">S.No.</th>
                                <th style="width: 40%">Investigation</th>
                                <th style="width: 20%">Results</th>
                                <th style="width: 30%">Information</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            foreach($lab_results as $result) {
                                $low = (float)$result['low_range'];
                                $high = (float)$result['high_range'];
                                $value = (float)$result['value'];
                            ?>
                                <tr>
                                    <td style="text-align: center; font-size: 13px"><?php echo $i++; ?>.</td>
                                    <td style="vertical-align: top">
                                        <span style="font-size:12px;font-weight: bold"><b><?php echo strtoupper($result['investigation']); ?></b></span><br>
                                        <small style="font-style: italic;font-size: 10px"><?=$result['method']?></small>
                                    </td>
                                    <td style="font-size: 12px; vertical-align: top">
                                        <?php
                                        if($value != "" || $value != null){
                                            if($value < $low || $value > $high){
                                                echo '<span class="abnormal" style="font-weight:bold">'.$value.'</span>';
                                            }else{
                                                echo '<span class="normal">'.$value.'</span>';
                                            }
                                            echo '<span class="units">&nbsp;'.$result['units'].'</span>';
                                        }else{
                                            echo '<span class="normal"> -- </span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="others" style="font-size: 12px; vertical-align: top">
                                        <?php echo str_replace(';', '<br>', $result['other_information']); ?>        
                                    </td>                                    
                                </tr>
                            <?php } ?>
                                <tr>
                                    <td colspan="4" style="vertical-align: top">
                                        <span style="font-weight: bold; text-decoration: underline; font-size: 13px; line-height: 20px;">Lab Consultant's Remarks:</span>
                                        <p><?=$consultant_remark?></p>
                                    </td>
                                </tr>
                        </tbody>
                    <?php }else{ ?>
                        <thead>
                            <tr>
                                <th style="width: 10%; text-align: center">S.No.</th>
                                <th style="width: 20%">Investigation</th>
                                <th style="width: 65%">Remark</th>
                                <th style="width: 5%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            foreach($lab_results as $result) {
                                $value = $result['remarks'];
                            ?>
                                <tr>
                                    <td style="vertical-align: top; text-align: center; font-size: 13px"><?php echo $i++; ?>.</td>
                                    <td style="vertical-align: top;">
                                        <span style="font-size:12px;font-weight: bold"><b><?php echo strtoupper($result['investigation']); ?></b></span>
                                    </td>
                                    <td style="font-size: 12px; vertical-align: top">
                                        <p><?php echo $value; ?></p>
                                    </td>
                                    <td></td>                                    
                                </tr>
                            <?php } ?>
                                <tr>
                                    <td colspan="4" class="remarks">
                                        <span style="font-weight: bold; text-decoration: underline; font-size: 13px; line-height: 20px;">Lab Consultant's Remarks:</span>
                                        <p><?=$consultant_remark?></p>
                                    </td>
                                </tr>
                        </tbody>
                    <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
        <?php   
        if($pdf_settings->footer==1)
        {
            $style = "margin-bottom: ".$pdf_settings->footer_height."px !important;";
        }
        ?>
            <htmlpagefooter name="footer">
                <table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;<?=$style?>">
                    <tr>
                        <td style="width:45%">
                            <span style="font-weight: bold;font-size: 12px">Powered By umdaa.co</span>
                        </td>
                        <td style="width: 20%">
                            <span style="font-weight: bold;font-size: 12px;text-align: center;">Page {PAGENO} of {nb}</span>
                        </td>
                        <td style="width:35%;text-align: right;">
                            <span style="font-weight: bold;font-size: 12px"><b>Date: </b><?php echo date("d M Y h:i A", strtotime($appointments->created_date_time)); ?></span>
                        </td>
                    </tr>
                </table>
            </htmlpagefooter>       
<?php
if($page_break=="1")
{
    ?>
        <div class="page-break"></div>
        <?php
        $this->load->view("reports/default_pdf_header");
}
?>
</body>	
</html>