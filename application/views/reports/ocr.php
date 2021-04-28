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

<body style="font-family: 'Roboto', sans-serif;border-bottom: 1px solid #ddd;font-size:10px !important">


<!-- <div class="container-fluid"> -->
<?php
if(count($pdfSettings) > 0){
    if($pdfSettings->header == 0){
        ?>
        <htmlpageheader name="firstpageheader">
    <!-- Header Block -->
    <table class="table" style="margin-bottom:0px;font-size:10px ;border-bottom:1px solid #000;width:100%;">
        <tbody>
            <tr>
                <td>
                    <img src="<?=base_url('uploads/clinic_logos/'.$clinicsInfo->clinic_logo)?>" style="width:30%">
                </td>
                <td style="text-align:right">
                    <!-- <p style="font-weight: bold;font-size:16px"><?=$clinicsInfo->clinic_name?></p>
                    <p><span style="font-weight:bold">Address:</span> <?=$clinicsInfo->address?></p>
                    <p><span style="font-weight:bold">Contact:</span> <?=$clinicsInfo->clinic_phone?></p> -->
                    <span style="font-weight: bold;font-size:16px"><b><?php echo getDoctorName($docInfo->doctor_id); ?></b></span><br>
					<span><?php echo strtoupper($docInfo->qualification. ", ". $docInfo->department_name); ?> </span><br>
					<span><b>Reg. No:</b> &nbsp;<?php echo $docInfo->registration_code; ?> </span>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="table" style="font-size:10px;margin:0px !important;width:100%;padding:0px !important">
        <tbody>
            <tr>
                <td style="width: 66%">
                    <span style="font-weight: bold;">Address: </span><?=$clinicsInfo->address?>
                </td>
                <td style="width: 34%">
                    <span style="font-weight:bold;">Contact:</span><?=$clinicsInfo->clinic_phone?>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="container" style="margin-top:0px;margin-bottom:10px;border-bottom:1px solid #000"></div>
    <!-- header Block Ends -->
</htmlpageheader>

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
    <htmlpageheader name="firstpageheader">
    <!-- Header Block -->
    <table class="table" style="margin-bottom:0px;font-size:10px ;border-bottom:1px solid #000;width:100%;">
        <tbody>
            <tr>
                <td>
                    <img src="<?=base_url('uploads/clinic_logos/'.$clinicsInfo->clinic_logo)?>" style="width:30%">
                </td>
                <td style="text-align:right">
                    <!-- <p style="font-weight: bold;font-size:16px"><?=$clinicsInfo->clinic_name?></p>
                    <p><span style="font-weight:bold">Address:</span> <?=$clinicsInfo->address?></p>
                    <p><span style="font-weight:bold">Contact:</span> <?=$clinicsInfo->clinic_phone?></p> -->
                    <span style="font-weight: bold;font-size:16px"><b><?php echo getDoctorName($docInfo->doctor_id); ?></b></span><br>
					<span><?php echo strtoupper($docInfo->qualification. ", ". $docInfo->department_name); ?> </span><br>
					<span><b>Reg. No:</b> &nbsp;<?php echo $docInfo->registration_code; ?> </span>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="table" style="font-size:10px;margin:0px !important;width:100%;padding:0px !important">
        <tbody>
            <tr>
                <td style="width: 66%">
                    <span style="font-weight: bold;">Address: </span><?=$clinicsInfo->address?>
                </td>
                <td style="width: 34%">
                    <span style="font-weight:bold;">Contact:</span><?=$clinicsInfo->clinic_phone?>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="container" style="margin-top:0px;margin-bottom:10px;border-bottom:1px solid #000"></div>
    <!-- header Block Ends -->
</htmlpageheader>

    <?php
}
?>



<div class="container">
    <div class="row">
        <div class="col-xs-1">&nbsp;</div>
        <div class="col-xs-11">

            <!-- <div style="height:10px;border:1px solid black !important">khgsd</div> -->
            <!-- <p class="line-break" style="border:20px solid #000 !important">&nbsp;</p> -->
            <!-- <hr> -->
            
            <table  cellpadding="0" cellspacing="0" style="font-size:10px;margin:0px;width:100%;">
                <tbody>
                    <tr>
                        <td style="width: 33%;">
                            <p style="font-weight:bold;text-transform:uppercase;margin-bottom:10px"><?=getPatientName($patientInfo->patient_id)?></p>
                            <p><?=$patientInfo->umr_no?></p>
                        </td>
                        <td style="width: 33%">
                            <p><span style="font-weight: bold;margin-bottom:10px">Age & Gender: </span><?=$patientInfo->age." ".$patientInfo->age_unit?>/<?=$patientInfo->gender?></p>
                            <p><span style="font-weight: bold">Billing Date:</span> <?=date('d-m-Y', strtotime($billingInfo->created_date_time))?></p>
                        </td>
                        <td style="width: 34%">
                            <p><span style="font-weight:bold;margin-bottom:20px">Location:</span><?=$patientInfo->location?></p>
                            <p><span style="font-weight:bold">Valid For:</span> 1 Visit(s)</p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- <hr> -->
            <div class="container" style="margin-top:0px;margin-bottom:10px;border-bottom:1px solid #000">&nbsp;</div>
            <div class="container">
                <div class="row">
                    <div class="col-xs-2">
                        <p style="font-weight:bold;font-size:12px">Vitals</p>
                    </div>
                    <div class="col-xs-10 text-right">
                        <p style="font-size:10px"><span style="font-weight:bold">Drug Allergy: </span>☐ No ☐ Yes ...........................................................</p>
                    </div>
                </div>
            </div>

            <?php 
            $res_vital_sign = $this->db->query("SELECT a.vital_sign, a.vital_result, a.patient_id, a.vital_sign_recording_date_time,b.unit FROM `patient_vital_sign` a left join vital_sign b on(a.vital_sign = b.short_form)  WHERE a.appointment_id='".$appointments->appointment_id."' and a.vital_sign_recording_date_time =(select max(a.vital_sign_recording_date_time) FROM `patient_vital_sign` a where a.patient_id = '".$appointments->patient_id."' and a.clinic_id = '".$appointments->clinic_id."') and a.vital_result != '' and a.sign_type = 'generic' ORDER BY a.patient_vital_id ASC")->result();
            $SBP = 0;
            $DBP = 0;
            $BP = '';
            $i = 2;
            $vitalAr = array('PR','SBP','DBP','RR','Temp','SaO2','Height','Weight','BMI');
            $vital_sign_results = array();
            if(count($res_vital_sign) > 0){
                ?>
                <!-- Vitals -->
                    <table cellpadding="0" cellspacing="0" style="margin-bottom:0px;width:100%">
                    <tbody>
                    <tr>
                            <?php 
                                            // echo "<td>Array count: ".count($res_vital_sign)."</td>";

                                foreach ($res_vital_sign as $value) 
                                { 
                                    if(!in_array($value->vital_sign, $vitalAr))
                                        continue;
                                    // echo "<td>".$value->vital_sign.": ".$value->vital_result."</td>";
                                    if($value->vital_sign == "PR") {
                                        $vital_sign_results[0]['vital_sign'] = 'PR';
                                        $vital_sign_results[0]['value'] = $value->vital_result;
                                            $vital_sign_results[0]['unit'] = $value->unit;
                                    }elseif($value->vital_sign == "DBP" || $value->vital_sign == "SBP"){	
                                        if($value->vital_sign == "DBP"){
                                            $DBP = $value->vital_result;
                                        }elseif($value->vital_sign == "SBP"){
                                            $SBP = $value->vital_result;
                                        }

                                        if($SBP != 0 && $DBP != 0){
                                            $BP = $SBP."/".$DBP;
                                            $vital_sign_results[1]['vital_sign'] = 'BP';
                                            $vital_sign_results[1]['value'] = $BP;
                                            $vital_sign_results[1]['unit'] = $value->unit;
                                        }						
                                    }else{
                                        $vital_sign_results[$i]['vital_sign'] = $value->vital_sign;
                                        $vital_sign_results[$i]['value'] = $value->vital_result;
                                        $vital_sign_results[$i]['unit'] = $value->unit;
                                        $i++;
                                    }	

                                } 

                                // echo "<td>Array count: ".count($vital_sign_results[0])."</td>";
                                $x = 0;
                                for($j=0; $j<count($vital_sign_results); $j++){
                                    
                                    if($x == 4){
                                        ?>
                                        <td style="text-align:right;width:20%">
                                            <span style='font-size:10px;'>............... : </span>
                                            <span style="font-size:10px">.........................</span>
                                        </td>
                                        <?php
                                        echo "</tr><tr>"; // Add to new row after 4th column
                                        $x = 0;
                                    }

                                    ?>
                                    <td style='padding:5px 0px;width:20%'>
                                        <span style='font-weight:bold;font-size:10px;'><?=$vital_sign_results[$j]['vital_sign']?>: </span>
                                        <span style="font-size:10px"><?=$vital_sign_results[$j]['value'].$vital_sign_results[$j]['unit']?></span>
                                    </td>
                                    
                                    <?php
                                    if($vital_sign_results[$j]['vital_sign'] == "BMI"){
                                        ?>
                                        <td style="text-align:right;width:20%">
                                            <span style='font-size:10px;'>............... : </span>
                                            <span style="font-size:10px">.........................</span>
                                        </td>
                                        <?php
                                    }
                                    $x++;
                                }

                        ?>
                        </tr>
                    </tbody>
                    </table>

                    <div class="container" style="margin-top:0px;margin-bottom: 10px;border-bottom:1px solid #000">&nbsp;</div>
                <?php
            }
            else{
                ?>
                <table cellpadding="0" cellspacing="0" style="margin-bottom:0px;width:100%">
                <tbody>
                <tr>
                    <td style="text-align:left;width:20% !important">
                        <span style='font-size:10px;font-weight:bold'>PR : </span>
                        <span style="font-size:10px">.........................</span>
                    </td>
                    <td style="text-align:left;width:20% !important">
                        <span style='font-size:10px;font-weight:bold'>BP : </span>
                        <span style="font-size:10px">.........................</span>
                    </td>
                    <td style="text-align:left;width:20% !important">
                        <span style='font-size:10px;font-weight:bold'>RR : </span>
                        <span style="font-size:10px">.........................</span>
                    </td>
                    <td style="text-align:left;width:20% !important">
                        <span style='font-size:10px;font-weight:bold'>Temp : </span>
                        <span style="font-size:10px">.........................</span>
                    </td>
                    <td style="text-align:left;width:20% !important">
                        <span style='font-size:10px;'>............... : </span>
                        <span style="font-size:10px">.........................</span>
                    </td>
                </tr><br>
                <tr>
                    <td style="text-align:left;width:20% !important">
                        <span style='font-size:10px;font-weight:bold'>SaO2 : </span>
                        <span style="font-size:10px">.........................</span>
                    </td>
                    <td style="text-align:left;width:20% !important">
                        <span style='font-size:10px;font-weight:bold'>Height : </span>
                        <span style="font-size:10px">.........................</span>
                    </td>
                    <td style="text-align:left;width:20% !important">
                        <span style='font-size:10px;font-weight:bold'>Weight : </span>
                        <span style="font-size:10px">.........................</span>
                    </td>
                    <td style="text-align:left;width:20% !important">
                        <span style='font-size:10px;font-weight:bold'>BMI : </span>
                        <span style="font-size:10px">.........................</span>
                    </td>
                    <td style="text-align:left;width:20% !important">
                        <span style='font-size:10px;'>............... : </span>
                        <span style="font-size:10px">.........................</span>
                    </td>
                </tr>

                </tbody>
                </table>
                    <div class="container" style="margin-top:0px;margin-bottom: 10px;border-bottom:1px solid #000">&nbsp;</div>
                <?php
            }
            ?>
            <div class="container">
                <div class="row">
                    <div class="col-xs-8" style="border-right:1px solid black">
                        <p style="font-weight:bold;font-size:12px">Clinical Notes</p>
                        <div style="font-size:10px;margin:0px;margin-bottom:15px;width:100%;margin-top:15px">..............................................................................................................................................</div>
                        <div style="font-size:10px;margin:0px;margin-bottom:15px;width:100%">..............................................................................................................................................</div>
                        <div style="font-size:10px;margin:0px;margin-bottom:15px;width:100%">..............................................................................................................................................</div>
                        <div style="font-size:10px;margin:0px;margin-bottom:15px;width:100%">..............................................................................................................................................</div>
                        <div style="font-size:10px;margin:0px;margin-bottom:15px;width:100%">..............................................................................................................................................</div>
                        <!-- <div style="font-size:10px;margin:0px;margin-bottom:15px;width:100%">..............................................................................................................................................</div> -->
                        <!-- <div style="font-size:10px;margin:0px;width:100%">..............................................................................................................................................</div> -->
                    </div>
                    <div class="col-xs-3" style="margin-left:10px ">
                        <p style="font-weight:bold;font-size:12px">Past & Personal History</p>
                        <p style="font-size:10px;margin:0px;margin-bottom:5px">☐ DM</p>
                        <p style="font-size:10px;margin:0px;margin-bottom:5px">☐ HTN</p>
                        <p style="font-size:10px;margin:0px;margin-bottom:5px">☐ CAD</p>
                        <p style="font-size:10px;margin:0px;margin-bottom:5px">☐ CVA</p>
                        <p style="font-size:10px;margin:0px;margin-bottom:5px">☐ Surgery</p>
                        <p style="font-size:10px;margin:0px;margin-bottom:5px">☐ Smoking</p>
                        <p style="font-size:10px;margin:0px;margin-bottom:5px">☐ Alcohol</p>
                        <!-- <div style="font-size:10px;margin:0px;margin-bottom:15px;width:100%;margin-top:22px">......................................................</div> -->
                        <!-- <div style="font-size:10px;margin:0px;margin-bottom:15px;width:100%">......................................................</div> -->
                        <!-- <div style="font-size:10px;margin:0px;width:100%">......................................................</div> -->
                    </div>
                </div>
            </div>
            <div class="container" style="margin-top:0px;margin-bottom:10px;border-bottom:1px solid #000">&nbsp;</div>

            <div class="container">
                <div class="row">
                    <div class="col-xs-4" style="border-right: 1px solid black">
                        <p style="font-weight:bold;font-size:12px">Investigations</p>
                        <div style="font-size:10px;margin:0px;margin-bottom:15px;width:100%;margin-top:20px">...............................................................</div>
                        <div style="font-size:10px;margin:0px;margin-bottom:15px;width:100%">...............................................................</div>
                        <div style="font-size:10px;margin:0px;margin-bottom:15px;width:100%">...............................................................</div>
                        <div style="font-size:10px;margin:0px;margin-bottom:15px;width:100%">...............................................................</div>
                        <div style="font-size:10px;margin:0px;margin-bottom:15px;width:100%">...............................................................</div>
                        <div style="font-size:10px;margin:0px;margin-bottom:15px;width:100%">...............................................................</div>
                        <div style="font-size:10px;margin:0px;margin-bottom:15px;width:100%">...............................................................</div>
                        <div style="font-size:10px;margin:0px;margin-bottom:15px;width:100%">...............................................................</div>
                        <div style="font-size:10px;margin:0px;margin-bottom:15px;width:100%">...............................................................</div>
                        <p style="font-weight:bold;font-size:12px">General Instructions/Procedures</p>
                        <div style="font-size:10px;margin:0px;margin-bottom:15px;width:100%;margin-top:20px">...............................................................</div>
                        <div style="font-size:10px;margin:0px;margin-bottom:15px;width:100%">...............................................................</div>
                        <div style="font-size:10px;margin:0px;margin-bottom:15px;width:100%">...............................................................</div>
                        <div style="font-size:10px;margin:0px;width:100%">...............................................................</div>
                    </div>
                    <div class="col-xs-7 " style="margin-left:30px">
                        <div class="row">
                            <div class="col-xs-6" style="padding-right:0px">
                                <p style="font-weight:bold;font-size:10px">Drug Name</p>
                            </div>
                            <div class="col-xs-1">
                                <p style="font-weight:bold;font-size:10px">M</p>
                            </div>
                            <div class="col-xs-1">
                                <p style="font-weight:bold;font-size:10px">A</p>
                            </div>
                            <div class="col-xs-1">
                                <p style="font-weight:bold;font-size:10px">N</p>
                            </div>
                            <div class="col-xs-1">
                                <p style="font-weight:bold;font-size:10px">BF</p>
                            </div>
                            <div class="col-xs-1">
                                <p style="font-weight:bold;font-size:10px">AF</p>
                            </div>
                            <div class="col-xs-1">
                                <p style="font-weight:bold;font-size:10px">Days</p>
                            </div>
                        </div>

                        
                        <?php
                        for($i = 0;$i < 7;$i++){
                            ?>
                            <div class="row">
                                <div class="col-xs-6">
                                    <p style="font-size:10px">
                                        <span style="font-weight:bold"><?=$i+1?>)</span> ..........................................................
                                    </p>
                                </div>
                                <div class="col-xs-1">
                                    <img src="<?=base_url('uploads/box.png')?>" style="width:60%">
                                    <!-- <p style="font-size:25px">☐</p> -->
                                </div>
                                <div class="col-xs-1">
                                    <img src="<?=base_url('uploads/box.png')?>" style="width:60%">
                                </div>
                                <div class="col-xs-1">
                                    <img src="<?=base_url('uploads/box.png')?>" style="width:60%">
                                </div>
                                <div class="col-xs-1">
                                    <img src="<?=base_url('uploads/box.png')?>" style="width:60%">
                                </div>
                                <div class="col-xs-1">
                                    <img src="<?=base_url('uploads/box.png')?>" style="width:60%">
                                </div>
                                <div class="col-xs-1">
                                    <p style="font-size:10px">.........</p>
                                </div>
                            </div>
                            <div class="row mb-2" style="margin-bottom:10px">
                                <div class="col-xs-12">
                                    <p style="font-size:10px">Remarks ........................................................................................................................</p>
                                </div>
                            </div>
                            <?php
                        }
                        ?>

                    </div>
                </div>

            </div>
            <div class="container" style="margin-top:0px;margin-bottom:10px;border-bottom:1px solid #000">&nbsp;</div>

            <div class="container" style="margin-bottom:0px">
                <div class="row">
                    <div class="col-xs-6">
                        <p style="font-size:10px;margin-bottom:0px;"><span style="font-weight:bold">Follow Up Date : </span><?=($prescriptions[0]->follow_up_date == "") ?'NA':date('d M Y', strtotime($prescriptions[0]->follow_up_date))?></p>
                    </div>
                    <div class="col-xs-6 " style="text-align:right">
                        <br>
                        <p style="font-weight:bold;margin-bottom:0px;font-size:10px"><?=getDoctorName($docInfo->doctor_id)?></p>
                    </div>
                </div>
            </div>
            <div class="container" style="margin-top:0px;margin-bottom:0px;border-bottom:1px solid #000">&nbsp;</div>
            <?php   
		if($pdfSettings->footer==1)
		{
		    $style = "margin-bottom: ".$pdfSettings->footer_height."px !important;";
		}
		?>
            <htmlpagefooter name="footer">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-1">&nbsp;</div>
                        <div class="col-xs-11">
                            
                            <table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;<?=$style?>">
                                <tr>
                                    <td style="width:45%">
                                        <span style="font-weight: bold;font-size:10px">Powered by UMDAA</span>
                                    </td>
                                    <td style="width:55%;text-align: right;">
                                        <span style="font-weight: bold;font-size:10px"><b>Generated On: </b><?php echo date("d M Y h:i A", strtotime($appointments->created_date_time)); ?></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </htmlpagefooter>

        </div>
    </div>
</div>
    
</body>
</html>