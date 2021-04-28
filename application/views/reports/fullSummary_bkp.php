<!-- ShortSummary STarts -->
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

<body style="font-family: 'Roboto', sans-serif;border-bottom: 1px solid #ddd;font-size: 12px !important">
<!-- Header Block -->

<!-- <div class="container-fluid"> -->

<htmlpageheader name="firstpageheader">

    <!-- Clinic Block -->
    <table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
        <tr>
            <td style="width:40%">
                <img style="width:50%" alt="" src="<?php echo base_url(); ?>uploads/clinic_logos/<?php echo $clinicsInfo->clinic_logo; ?>">
            </td>
            <td style="width: 25%"></td>
            <td style="width:35%;text-align: right;">
                <span style="font-weight: bold;font-size: 12px">Address</span><br><span style="font-weight: 700;font-size: 12px"><?php echo $clinicsInfo->address; ?></span><br><span style=" font-weight: bold;font-size: 12px">Phone:</span><span style="font-weight: 700;font-size: 12px"><?php echo $clinicsInfo->clinic_phone; ?></span>
            </td>
        </tr>
    </table>

    <hr>

    <!-- Patient Info -->
    <table class="table" cellpadding="0" cellspacing="0" style="font-size:12px !important;">
        <tbody>
            <tr>
                <td style="padding: 3px 0px !important;width:35%" class="text-left">
                    <p style="font-weight:bold"><?=strtoupper(getPatientName($patientInfo->patient_id))?></p>
                    <p  style="font-size: 12px !important">UMR : <?=$patientInfo->umr_no?></p>
                    <p  style="font-size: 12px !important"><?=$patientInfo->age?><?=$patientInfo->age_unit?> <?=$patientInfo->gender?></p>
                </td>
                
                <td style="padding: 3px 0px !important;vertical-align:top;width:30%" class="text-center">
                    <p style="font-weight:bold">Address</p>
                    <p  style="font-size: 12px !important"><?=DataCrypt($patientInfo->mobile, 'decrypt')?></p>
                    <p  style="font-size: 12px !important"><?=$patientInfo->location?></p>
                </td>
                
                <td style="padding: 3px 0px !important;vertical-align:top;width:35%" class="text-right">
                    <p style="font-weight:bold"><?=strtoupper(getDoctorName($docInfo->doctor_id))?></p>
                    <p  style="font-size: 12px !important"><?=$docInfo->qualification?> - <?=$docInfo->department_name?></p>
                    <p  style="font-size: 12px !important">Reg. No:<?=$docInfo->registration_code?></p>
                </td>
            </tr>
        </tbody>
    </table>

    <hr>
    </htmlpageheader>


    <!-- Drug Allergy -->
    <?php
    if($patientInfo->allergy != '' || $patientInfo->allergy != null){
        $allergy = ucwords($patientInfo->allergy);
    }else{
        $allergy = "No allergy mentioned";
    }
    ?>
    <table cellpadding="0" cellspacing="0" style="font-size:12px !important;width:100%">
        <tr>
            <td class="text-left" style="padding:0px !important">
                <p  style="font-size: 12px !important"><span style="font-weight:bold">Drug Allergy : </span> <?=$allergy?></p>
            </td>
            <td class="text-right" style="padding:0px !important">
                <p  style="font-size: 12px !important"><span style="font-weight:bold;float:right">Appointment Date : </span> <?=date("d-m-Y", strtotime($appointments->appointment_date))." ".date("h:i A", strtotime($appointments->appointment_time_slot))?></p>
            </td>
        </tr>
    </table>

    <hr>


    
                <?php
                $reviewDays = $this->db->query("SELECT review_days FROM clinic_doctor WHERE clinic_id = '".$appointments->clinic_id."' AND doctor_id = '".$appointments->doctor_id."'")->row();

                $check = $this->db->query("select * from appointments a inner join doctors d on(a.doctor_id= d.doctor_id) where a.patient_id='" . $appointments->patient_id . "' and a.doctor_id='" . $appointments->doctor_id . "' and a.parent_appointment_id=0 and a.appointment_date <= '".date('Y-m-d')."' and (a.status !='booked' or a.status !='drop') order by a.appointment_date  desc")->row();

                $get_review_days = $this->db->query("select * from clinic_doctor  where clinic_id='" . $appointments->clinic_id . "' and doctor_id ='".$appointments->doctor_id."'")->row();

                $get_review_times = $get_review_days->review_times;

                $review_check_date = date('Y-m-d', strtotime($check->appointment_date. ' + '.$get_review_days->review_days.' days'));

                $check_review_count = $this->db->query("select * from appointments where  patient_id='" . $appointments->patient_id . "' and doctor_id='" . $doctor_info->doctor_id . "' and appointment_date >'".$check->appointment_date."' and appointment_date <='".$review_check_date."' and status !='booked'")->num_rows();
                echo $check;

                if($check){


                    if($appointments->appointment_date > $review_check_date){
                        $status = "Valid ";
                    }
                    else{

                        if($check_review_count <= $get_review_times){
                            $review_time_diff = $get_review_times - $check_review_count;
                            $status = "You have ".$review_time_diff." Review Visits";
                        }
                        else{
                            $status = "Valid ";
                        }
                    }


                }
                else{
                    $status = "Valid ";
                }
                $review_check_date = date('d M, Y.', strtotime($check->appointment_date. ' + '.$reviewDays->review_days.' days'));
                // echo "Valid Till ".$review_check_date.". ".$status;
                if($review_time_diff > 0){
                    ?>
                    <table cellpadding="0" cellspacing="0" style="font-size:12px !important;width:100%">
                        <tr>
                            <td>	
                                <span style="font-size:12px">Valid Till <?=$review_check_date.". ".$status?></span>
                            </td>
                        </tr>
                    </table>
                    <hr>
                    <?php
                }
                ?>
                
<?php 
$res_vital_sign = $this->db->query("SELECT a.vital_sign, a.vital_result, a.patient_id, a.vital_sign_recording_date_time,b.unit FROM `patient_vital_sign` a left join vital_sign b on(a.vital_sign = b.short_form)  WHERE a.vital_sign_recording_date_time =(select max(a.vital_sign_recording_date_time) FROM `patient_vital_sign` a where a.patient_id = '".$appointments->patient_id."' and a.clinic_id = '".$appointments->clinic_id."') and a.vital_result != '' and a.sign_type = 'generic' ORDER BY a.patient_vital_id ASC")->result();
$SBP = 0;
$DBP = 0;
$BP = '';
$i = 2;
$vital_sign_results = array();
if(count($res_vital_sign) > 0){
    ?>
    <!-- Vitals -->
    <p  style="font-size: 12px !important;"><span style="font-weight:bold;float:right">Vitals</span></p>
        <table cellpadding="0" cellspacing="0" style="width:100%">
        <tr style="padding: 5px">
                <?php 
                                // echo "<td>Array count: ".count($res_vital_sign)."</td>";

                    foreach ($res_vital_sign as $value) 
                    { 
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
                            echo "</tr><tr>"; // Add to new row after 4th column
                            $x = 0;
                        }

                        ?>
                        <td  style='padding:10px;width: 20px;padding-left: 0px !important;padding-right: 0px !important'>
                            <span style='font-weight:bold;font-size: 12px;'><?=$vital_sign_results[$j]['vital_sign']?>: </span><span style="font-size: 
                            12px"><?=$vital_sign_results[$j]['value'].$vital_sign_results[$j]['unit']?></span>
                        </td><?php
                        $x++;
                    }

            ?>
            </tr>
        </table>

        <hr>
    <?php
}
?>
   
<?php 
if(count($symptoms) > 0){
    ?>
     <!-- Symptoms -->
     <p  style="font-size: 12px !important;"><span style="font-weight:bold;float:right">Symptoms</span></p>
    <table cellpadding="0" cellspacing="0" style="font-size:12px !important;width:100%">
        <tr>
            <td class="text-left" style="padding:2px 0px !important">
                <?php 
                foreach($symptoms as $value){
                    $symptom = $symptom.$value->symptom_data." - ".$value->time_span." ".$value->span_type.", ";
                }
                ?>
                <p  style="font-size: 12px !important;"><?=substr(trim($symptom),0,-1)?></p>
            </td>
        </tr>
    </table>

    <hr>
    <?php
}
?>

<?php 
if(count($get_past_history_info) > 0){
    ?>
    <!-- Past History -->
    <p  style="font-size: 12px !important;"><span style="font-weight:bold;float:right">Past History</span></p>
   <table cellpadding="0" cellspacing="0" style="font-size:12px !important;width:100%">
        <?php 
        $i = 1;
        foreach($get_past_history_info as $value){
            if($value->section_text == "")
                continue;
            ?>
            <tr>
                <td class="text-left" style="padding: 5px 0px !important">
                    <p  style="font-size: 12px !important;padding-top:2px !important"><?=$i.". ".$value->section_text?></p>
                </td>
            </tr>
            <?php
            $i++;
        }
        ?>
   </table>

   <hr>
    <?php
}
?>

<?php 
if(count($gpe_info) > 0){
    ?>
    <!-- Past History -->
    <p  style="font-size: 12px !important;"><span style="font-weight:bold;float:right">General Physical Examination</span></p>
   <table cellpadding="0" cellspacing="0" style="font-size:12px !important;width:100%">
        <?php 
        $i = 1;
        foreach($gpe_info as $value){
            if($value->section_text == "")
                continue;
            ?>
            <tr>
                <td class="text-left" style="padding: 5px 0px !important">
                    <p  style="font-size: 12px !important;padding-top:2px !important"><?=$i.". ".$value->section_text?></p>
                </td>
            </tr>
            <?php
            $i++;
        }
        ?>
   </table>

   <hr>
    <?php
}
?>

<?php 
if(count($se_info) > 0){
    ?>
    <!-- Past History -->
    <p  style="font-size: 12px !important;"><span style="font-weight:bold;float:right">Systemic Examination</span></p>
   <table cellpadding="0" cellspacing="0" style="font-size:12px !important;width:100%">
        <?php 
        $i = 1;
        foreach($se_info as $value){
            if($value->section_text == "")
                continue;
            ?>
            <tr>
                <td class="text-left" style="padding: 5px 0px !important">
                    <p  style="font-size: 12px !important;padding-top:2px !important"><?=$i.". ".$value->section_text?></p>
                </td>
            </tr>
            <?php
            $i++;
        }
        ?>
   </table>

   <hr>
    <?php
}
?>

<?php 
if(count($notes) > 0){
    ?>
    <!-- Notes -->
    <p  style="font-size: 12px !important"><span style="font-weight:bold;float:right">Notes</span></p>
    <table cellpadding="0" cellspacing="0" style="font-size:12px !important;width:100%">
        <?php 
        $i = 1;
        foreach($notes as $value){
            ?>
            <tr>
                <td class="text-left" style="padding: 5px 0px !important">
                    <p  style="font-size: 12px !important;padding-top:2px !important"><?=$i.". ".$value->note_details?></p>
                </td>
            </tr>
            <?php
            $i++;
        }
        ?>
    </table>

    <hr>
    <?php
}
?>

<?php 
if(count($clinicalDiagnosis) > 0){
    ?>
    <!-- Clinical Diagnosis -->
    <p  style="font-size: 12px !important"><span style="font-weight:bold;float:right">Clinical Diagnosis</span></p>
    <table cellpadding="0" cellspacing="0" style="font-size:12px !important;width:100%">
        <?php 
        $i = 1;
        foreach($clinicalDiagnosis as $value){
            $cdInfo = masterCDInfo($value->clinical_diagnosis_id);
            ?>
            <tr>
                <td class="text-left" style="padding: 5px 0px !important">
                    <p  style="font-size: 12px !important;padding-top:2px !important"><?=$i.". ".$value->disease_name." ( ".$cdInfo->code." )"?></p>
                </td>
            </tr>
            <?php
            $i++;
        }
        ?>
    </table>

    <hr>
    <?php
}
?>

<?php 
if(count($investigations) > 0){
    ?>
    <!-- Investigations -->
    <p  style="font-size: 12px !important"><span style="font-weight:bold;float:right">Investigations</span></p>
    <table cellpadding="0" cellspacing="0" style="font-size:12px !important;width:100%">
        <?php 
        $i = 1;
        foreach($investigations as $value){
            $invInfo = masterInvInfo($value->investigation_id);
            ?>
            <tr>
                <td class="text-left" style="padding: 5px 0px !important">
                    <p  style="font-size: 12px !important;padding-top:2px !important"><?=$i.". ".$value->investigation_name?></p>
                </td>
            </tr>
            <?php
            $i++;
        }
        ?>
    </table>

    <hr>
    <?php
}
?>


<?php 
if(count($prescriptions) > 0){
    ?>
    <!-- Prescriptions -->
<p  style="font-size: 12px !important"><span style="font-weight:bold;float:right">Prescriptions (Rx)</span></p>
<table class="table" cellspacing="0" cellpadding="0" style="width: 100%;">
    <thead>
        <tr>
            <th style="font-size: 12px;background: #f2f2f2; border-bottom: 1px solid #ccc; width: 5%">S#</th>
            <th style="font-size: 12px;background: #f2f2f2; border-bottom: 1px solid #ccc; width: 30%">Medicine</th>
            <th style="font-size: 12px;background: #f2f2f2; border-bottom: 1px solid #ccc; width: 35%">Frequency</th>
            <!-- <th style="font-size: 12px;background: #f2f2f2; border-bottom: 1px solid #ccc; width: 15%">Duration</th> -->
            <th style="font-size: 12px;background: #f2f2f2; border-bottom: 1px solid #ccc; width: 20%">Days</th>
            <th style="font-size: 12px;background: #f2f2f2; border-bottom: 1px solid #ccc; width: 10%">QTY</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $i=1;

        foreach ($prescriptions as $key => $value) { 
            $drug = masterDrugInfo($value->drug_id);
            $M = 0;
            $dayM = "M";
            $dayA = "A";
            $dayN = "N";
            $N = 0;
            $A = 0;
            $dose = 1;
            $Mday = '';

            if($value->dosage_frequency != "")
                $freq = $value->dosage_frequency;
            elseif($value->day_schedule != "")
                $freq = "Day";

            if($value->preffered_intake == "AF"){
                $intake = "after food";
            }
            if($value->preffered_intake == "BF"){
                $intake = "before food";
            }
            if($patientInfo->preferred_language !="NULL" || $patientInfo->preferred_language !=""){
                if($patientInfo->preferred_language == "Telugu"){
                    $plang = "te";
                }
                else if($patientInfo->preferred_language == "Hindi"){
                    $plang = "hi";
                }
                else if($patientInfo->preferred_language == "Gujarati"){
                    $plang = "gu";
                }
                else if($patientInfo->preferred_language == "Kannada"){
                    $plang = "kn";
                }
                else if($patientInfo->preferred_language == "Malayalam"){
                    $plang = "ml";
                }
                else if($patientInfo->preferred_language == "Marathi"){
                    $plang = "mr";
                }
                else if($patientInfo->preferred_language == "Panjabi"){
                    $plang = "pa";
                }
                else if($patientInfo->preferred_language == "Sindhi"){
                    $plang = "sd";
                }
                else if($patientInfo->preferred_language == "Tamil"){
                    $plang = "ta";
                }
                else if($patientInfo->preferred_language == "Urdu"){
                    $plang = "ur";
                }
                else if($patientInfo->preferred_language == "Bengali"){
                    $plang = "bn";
                }
                else{
                    $plang = "en";
                }
            }
            else{
                $plang = "en";
            }
            // echo $plang;
            if($intake != ""){
                @$Intake_converted = "(".translate($intake,$plang).")";
            }
            if($value->remarks != ""){
                @$remark_converted = "(".translate(strtolower($value->remarks),$plang).")";
            }
            
            

        

            if($value->day_schedule !=""){
                $split_schedule = explode(",",$value->day_schedule);

                if(in_array("M", $split_schedule)){
                    $M = $value->drug_dose;
                    $dayM = "<span>MORNING</span>";

                }else{
                    $M = 0;
                    $dayM = "<span>MORNING</span>";
                }

                if(in_array("A", $split_schedule)){
                    $A = $value->drug_dose;
                    $dayA = "<span>AFTERNOON</span>";

                }else{
                    $A = 0;
                    $dayA = "<span>AFTERNOON</span>";
                }

                if(in_array("N", $split_schedule)){
                    $N = $value->drug_dose;
                    $dayN = "<span>NIGHT</span>";

                }else{
                    $N = 0;
                    $dayN = "<span>NIGHT</span>";
                }
            }
            ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td>
                    <p style="font-size: 12px;"><span style="text-transform:uppercase"><?=$drug->formulation?></span> <span style="font-weight:bold"><?php echo $drug->composition; ?></span>
                    ( <span style="font-weight: bold; font-size: 12px;"0><?php if($value->trade_name != NULL || $value->trade_name != '') { echo strtoupper($value->trade_name); }else{ echo strtoupper($value->medicine_name); } ?></span> )</p>
                                      
                </td>
                <td style="padding-left:0px !important;text-align:center">
                    <?php if($value->day_schedule==""||$value->day_schedule==NULL){ 
                        ?>
                        <span style="font-size:12px">
                        <?php 
                        if($value->day_dosage =="stat" || $value->day_dosage =="sos" || $value->day_dosage =="HS"){ 
                            echo $value->day_dosage; 
                        } 
                        else {
                            echo $value->day_dosage." times in a ".$value->dosage_frequency; 
                        } 
                        ?>
                        </span>
                        <br>
                        <!-- <span style="font-size: 12px;color:rgb(84,84,84);"><small><?php echo "(".$value->drug_dose." ".$value->dosage_unit." each )"; ?></small></span> -->
                                    <p style="font-size:12px;">( <?php echo $value->drug_dose." ".$value->dosage_unit." each"; ?> )</p>
                    <?php } else { ?>
                        <table style="width:100%;text-align:center;font-size:12px">
                            <tr>
                                <td style="padding:0px;font-weight:bold"><b><?=$M?></b></td>
                                <td style="padding:0px;font-weight:bold"><b><?=$A?></b></td>
                                <td style="padding:0px;font-weight:bold"><b><?=$N?></b></td>
                            </tr>
                            <tr>
                                <td style="padding:0px">Morning</td>
                                <td style="padding:0px">Noon</td>
                                <td style="padding:0px">Night</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-left" style="padding:5px;text-align:center">
                                    <p style="font-size:12px;">( <?php echo $value->drug_dose." ".$value->dosage_unit." each"; ?> )</p>
                                </td>
                            </tr>
                        </table>
                        
                    <?php } ?>

                </td>
                <td>
                    <table style="width:100%;font-size:12px" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="padding:5px 0px">
                                <p><?php if($value->dose_course == 0 ){ echo "--"; } else { echo $value->dose_course." ".$freq."(s)"; } ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 5px 0px">
                                <p><?php echo ucfirst($intake); ?></p>
                                <p><?php if($plang != "en"){ ?> <?php echo ucfirst($Intake_converted); } ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:5px 0px">
                                <p><?php if($plang != "en"){ ?> <?php echo ucfirst($remark_converted); } ?></p>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="padding:10px 0px;">
                    <span style="font-size:12px">
                    <?php if($value->quantity == 0){
                        echo "--";
                    }
                    else{
                        echo $value->quantity." ".$drug->formulation;
                        echo ($value->quantity > 1)?'(s)':'';
                    } ?> 
                    </span> 
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px;vertical-align: top">
    <tr> <td style="text-align:justify !important"><div  style="font-size: 12px;font-weight: bold;font-style: italic;">CAUTION : Take the medicine as prescribed by the doctor. Do not use in larger or smaller amounts than advised/prescribed. Use the medicine only for the prescribed purpose. Report any unusual symptoms to the doctor immediately. Do not stop the use of the medicine without consulting your doctor. Ensure that you complete the course as prescribed by your doctor.</div></td></tr>
</table>

<hr>
    <?php 
}
?>

<?php 
if($prescriptions[0]->general_instructions != ''){
    ?>
    <!-- Clinical Diagnosis -->
    <p  style="font-size: 12px !important"><span style="font-weight:bold;float:right">General Instructions</span></p>
    <table cellpadding="0" cellspacing="0" style="font-size:12px !important;width:100%">
        <tr>
            <td class="text-left" style="padding: 5px 0px !important">
                <p  style="font-size: 12px !important;padding-top:2px !important"><?=$prescriptions[0]->general_instructions?></p>
            </td>
        </tr>
    </table>

    <hr>
    <?php
}
?>

<?php 
if($prescriptions[0]->plan != ''){
    ?>
    <!-- Clinical Diagnosis -->
    <p  style="font-size: 12px !important"><span style="font-weight:bold;float:right">Plan</span></p>
    <table cellpadding="0" cellspacing="0" style="font-size:12px !important;width:100%">
        <tr>
            <td class="text-left" style="padding: 5px 0px !important">
                <p  style="font-size: 12px !important;padding-top:2px !important"><?=$prescriptions[0]->plan?></p>
            </td>
        </tr>
    </table>

    <hr>
    <?php
}
?>

<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px;vertical-align: top;font-size:12px">
    <tr> 
        <td>
            <p><span style="font-weight:bold">Follow Up Date : </span><?=($prescriptionsInfo->follow_up_date == "") ?'NA':date('d M Y', strtotime($prescriptionsInfo->follow_up_date))?></p>
        </td>
    </tr>
</table>

<hr>
<?php
$cdImgs = explode(",", $clinicalDiagnosis[0]->images);
if(count($cdImgs) > 0)
{
    for($j=0;$j<count($cdImgs);$j++)
    {
        if($cdImgs[$j] != "")
        {
            ?>
            <img src="<?=base_url("uploads/clinical_diagnosis/").$cdImgs[$j]?>" style="width: 45%">
            <?php
        }
        
    }
}


?>

<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;font-size:12px">
    <tr>
        <td style="width:47%;text-align: left;vertical-align: bottom;">
            <span style="font-weight: bold; line-height: 25px;"><img src="<?php echo base_url('uploads/qrcodes/patients/'.$patientInfo->qrcode);?>"></span>
        </td>
        <td style="width:53%;text-align: right;vertical-align: middle;">
        <?php
        $digiSignInfo = $this->db->query("select * from digital_signatures where user_id='".$docInfo->doctor_id."'")->row();
        ?>
        <p class="font-weight-bold text-right"><img src="<?=base_url('uploads/docDigitalSign/'.$digiSignInfo->digital_signature)?>" class="mr-3" style="width:200px !important"></p>	
        <p class="font-weight-bold text-right" style="font-weight:bold"><?=getDoctorName($docInfo->doctor_id)?></p>
        <p class="font-weight-bold text-right">Reg No:<?php echo $docInfo->registration_code?></p>
        </td>
    </tr>
    <?php if($appointmentdetails->slot_type=='video call'){?>
    <tr>
        <td>
            <p style="font-weight: bold; line-height: 25px;">This Is Tele Consultation Appointment</p>
        </td>
    </tr>
    <?php }?>
</table>


<!-- </div> -->


<!-- SHort SUmmary Ends -->
    
<!-- Full Summary Starts     -->

<!-- Previous Documents Starts -->
<?php 
if(count($previous_documents)>0){ 
    ?>
    <div class="page-break"></div>
    <div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-transform: uppercase;">Medical Report</span></div>
    <?php 
    $i=1;
    foreach ($previous_documents as $key => $value) { 
        $images = trim($value->images, ",");
        $picture_explode = explode(",", $images);
        for ($k = 0; $k < count($picture_explode); $k++) { 
            ?>
            <img  src="<?php echo base_url('uploads/previous_documents/' . trim($picture_explode[$k])); ?>" style="width: 50%;" /><br><br>
            <?php  
        } 
    } 
}
?>
<!-- Previous Documents Ends -->

<!-- Consent Fomrs starts -->
<?php
		if(count($patient_consent_form)>0)
		{
            ?>
            <div class="page-break"></div>
            <?php
			// $this->load->view("reports/default_pdf_header");
			$i=0;
			foreach ($patient_consent_form as $value) {

				if($i>0)
				{

					?>
					<div class="page-break"></div>
					<?php
				}

				unset($before);
				unset($after);
				unset($during);

				$patient_consent_checklist = $this->db->query("select * from patient_checklist pc,checklist_master cm where pc.checklist_id=cm.checklist_id and  pc.appointment_id='".$appointments->appointment_id."' and pc.checked='1' and pc.patient_consent_form_id='".$value->patient_consent_form_id."' order by cm.checklist_id ASC")->result();

				?>
				<div style="font-weight: bold;font-size: 12px;text-transform: uppercase;">CONSENT FORM - <?=$value->consent_form_title?></div><br>
				<table style="width: 100%">
					<tr>
						<td style="font-size: 12px;"><span style="font-size: 12px;font-weight:bold;">Checked By : </span><?=$value->checked_by?></td>
						<td style="font-size: 12px;"><span style="font-size: 12px;font-weight:bold;">Procedure Done By : </span><?=$value->done_by?></td>
					</tr>
					<tr>
						<td style="font-size: 12px;"><span style="font-size: 12px;font-weight:bold;">Assisted By : </span><?=$value->assisted_by?></td>
						<td style="font-size: 12px;"><span style="font-size: 12px;font-weight:bold;">UHID NO. : </span><?=$value->umr_no?></td>
					</tr>
					<tr>
						<td style="font-size: 12px;"><span style="font-size: 12px;font-weight:bold;">Nurse : </span><?=$value->nurse?></td>
						<td style="font-size: 12px;"><span style="font-size: 12px;font-weight:bold;">Anesthetist : </span><?=$value->anesthetist?></td>
					</tr>
				</table><br>
				<!-- <span style="font-weight: bold; text-transform: uppercase;">Instructions Taken</span><br> -->
				<?php
				foreach ($patient_consent_checklist as $value) 
				{
					if($value->category=="before")
						$before[] = $value;
					elseif($value->category=="during")
						$during[] = $value;
					elseif($value->category=="after")
						$after[] = $value;
				}

				if(count($before)>0)
				{
					?>
					<span style="font-weight: bold;font-size: 12px;">Before Procedure Check</span><br>
					<?php
					foreach ($before as $value) {
						?>
						<span style="font-size: 12px;padding:5px;padding-top:12px;<?=($value->type=='Title')?'font-weight:bold':''?>"><?=($value->type!="Title")?'&#8227;':''?> <?=$value->description?></span><br>
						<?php
						if($value->remark!="")
						{
							?>
							<span style="font-size: 12px;padding:10px;margin-left: 25px;">&emsp;&emsp; &bull; <?=$value->remark?></span><br><br>
							<?php
						}
						?>
						<?php
					}
				}

				if(count($during)>0)
				{
					?>
					<span style="font-weight: bold;font-size: 12px;line-height: 25px">During Procedure Check</span><br>
					<?php
					foreach ($during as $value) {
						?>
						<span style="font-size: 12px;padding:5px;padding-top:12px;<?=($value->type=='Title')?'font-weight:bold':''?>"><?=($value->type!="Title")?'&#8227;':''?> <?=$value->description?></span><br>
						<?php
						if($value->remark!="")
						{
							?>
							<span style="font-size: 12px;padding:10px;margin-left: 25px;">&emsp;&emsp; &bull; <?=$value->remark?></span><br>
							<?php
						}
						?>
						<?php
					}
				}

				if(count($after)>0)
				{
					?>
					<span style="font-weight: bold;font-size: 12px;line-height: 25px">After Procedure Check</span><br>
					<?php
					foreach ($after as $value) {
						?>
						<span style="font-size: 12px;padding:5px;padding-top:12px;<?=($value->type=='Title')?'font-weight:bold':''?>"><?=($value->type!="Title")?'&#8227;':''?> <?=$value->description?></span><br>
						<?php
						if($value->remark!="")
						{
							?>
							<span style="font-size: 12px;padding:10px;margin-left: 25px;">&emsp;&emsp; &bull; <?=$value->remark?></span><br>
							<?php
						}
						?>
						<?php
					}
				}

				?>

				
				<?php
				$i++;
			}
			?>

			<?php
		}
		?>
<!-- Consent Form Ends -->

<!-- Presenting Symptoms Starts -->

<?php if(count($presenting_symptoms)>0){ 
			?>
            <div class="page-break"></div>
            <div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-transform: uppercase;">Symptoms</span></div><br>
            <table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px">
                
            <?php $cd=0;$c=1;
            foreach ($presenting_symptoms as $key => $value) {
                $hopi_info = $this->db->query("select * from form where form_type='".HOPI."' and form_id='".$value->form_id."'")->row();
                ?>           
                <tr>
                    <td>
                        <span style="font-size: 12px"><?php echo $c++ . ". " .ucwords($value->symptom_data)." (".$value->time_span." ".$value->span_type.")"; ?></span>
                    </td>
                </tr>
                <?php $cd++;} ?>
            </table>
<?php } ?>
<!-- Presenting Symptoms Ends -->

<!-- HOPI Starts -->

<?php if(count($get_hopi_info)>0) { 
	?>
    <div class="page-break"></div>
<div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-tranform: uppercase;">HOPI</span></div><br>
		
<?php
$n=0;
foreach ($get_hopi_info as $get_hinfo) 
{
    $s=0;
    
    if($n > 0)
    {
        ?>
        <div class="page-break"></div>
        <?php 
    }
    ?>
    
    <?php
	$hopi_data = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$get_hinfo->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
    $get_hopi_form = $this->db->select("form_name")->from("form")->where("form_id='".$get_hinfo->form_id."'")->get()->row();
	
	foreach($hopi_data as $pl){
	$section_text = $this->db->query("select section_text from patient_form_line_items where patient_form_id='".$get_hinfo->patient_form_id."' and section_id='".$pl->section_id."' and (section_text >'' or section_text IS NULL or section_text <'')")->row();
	
	if($section_text->section_text == '')
	{
		$s_text='';
	}else{
		$s_text=$section_text->section_text;
	}
		
	
	$section = $this->Generic_model->getSingleRecord('section',array('section_id'=>$pl->section_id),'');
	$parent_section_id = $section->parent_section_id;
	if($parent_section_id==0){
	?>
	
		<span style="font-size: 12px;padding: 5px"><span style="font-weight: bold"><?php echo $section->title; ?>: </span><?php echo $s_text; ?></span><br>
		<?php
		$section_image = $this->db->query("select scribbling_image from patient_form_scribbling_images where patient_form_id='".$get_hinfo->patient_form_id."'")->result();
		$img = '';$s_image = '';
		
		foreach($section_image as $sec_image)
		{
			if($sec_image->scribbling_image == '' || $sec_image->scribbling_image == NULL)
			{
				$img='';
			}else{
				$s_image = $sec_image->scribbling_image;
				$s_image = ltrim($s_image, './');
				$head = array_change_key_case(get_headers(base_url($s_image), TRUE));
				$filesize = $head['content-length'];
				if($filesize>0)
					echo $img = "<img style='width:45%' src='".base_url($s_image)."' />";
			}
		}
		?>
				
		<table cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px; padding: 5px">
		<?php
		$labels_data = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$get_hinfo->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
		$ld =0;
		foreach($labels_data as $ld)
		{
			if($ld->field_value=="")
                continue;
                
                
		?>
			<tr>
				<td style="font-size: 12px;">
                        <?php
                        if($last != $ld->field_value)
                        {
                            ?>
                            <p style="line-height:20px !important;font-size: 12px;font-weight: bold">
                            <?php
                            echo $ld->field_value;
                            ?>
                            </p>
                            <?php
                        }
                         ?>  
                         <p style="line-height:20px !important;font-size: 12px;">&emsp;&#8227; <?php echo $ld->option_value; ?> </p>
                    
				</td>
			</tr>
        <?php 
            
            $last = $ld->field_value;
            $ld++; 
        } ?>
		</table>
		
	<?php }else{
		$section_tile = $this->Generic_model->getSingleRecord('section',array('section_id'=>$parent_section_id),'');
		?>
		<span style="font-weight: bold;font-size: 12px;"><?php echo $section_tile->title; ?></span>				
		<span style="font-weight: bold;font-size: 12px;"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?></span>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px">
			<?php
				$labels_data1 = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$get_hinfo->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
				$ld1=0;
				foreach($labels_data1 as $ld1)
				{
						if($ld1->field_value=="")
							continue;
				?>
                <tr>
                    <td style="font-size: 12px;">
                            <?php
                            if($last != $ld->field_value)
                            {
                                ?>
                                <p style="line-height:20px !important;font-size: 12px;font-weight: bold">
                                <?php
                                echo $ld->field_value;
                                ?>
                                </p>
                                <?php
                            }
                             ?>  
                             <p style="line-height:20px !important;font-size: 12px;">&emsp;&#8227; <?php echo $ld->option_value; ?> </p>
                        
                    </td>
                </tr>
            <?php 
                
                $last = $ld->field_value;
                $ld++; } ?>
		</table>
	
	<?php } $s++;$n++; } ?>
<?php 
}

}
?>

<!-- HOPI Ends -->
<!-- Past History STarts -->
<?php if(count($get_past_history)>0) { 
	?>
    <div class="page-break"></div>
<div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-tranform: uppercase;">Past History</span></div><br>
		
<?php
$n=0;
foreach ($get_past_history as $get_hinfo) 
{
    $s=0;
    
    if($n > 0)
    {
        ?>
        <div class="page-break"></div>
        <?php 
    }
    ?>
    
    <?php
	$hopi_data = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$get_hinfo->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
    $get_hopi_form = $this->db->select("form_name")->from("form")->where("form_id='".$get_hinfo->form_id."'")->get()->row();
	
	foreach($hopi_data as $pl){
	$section_text = $this->db->query("select section_text from patient_form_line_items where patient_form_id='".$get_hinfo->patient_form_id."' and section_id='".$pl->section_id."' and (section_text >'' or section_text IS NULL or section_text <'')")->row();
	
	if($section_text->section_text == '')
	{
		$s_text='';
	}else{
		$s_text=$section_text->section_text;
	}
		
	
	$section = $this->Generic_model->getSingleRecord('section',array('section_id'=>$pl->section_id),'');
	$parent_section_id = $section->parent_section_id;
	if($parent_section_id==0){
	?>
	
		<span style="font-size: 12px;padding: 5px"><span style="font-weight: bold"><?php echo $section->title; ?>: </span><?php echo $s_text; ?></span><br>
		<?php
		$section_image = $this->db->query("select scribbling_image from patient_form_scribbling_images where patient_form_id='".$get_hinfo->patient_form_id."'")->result();
		$img = '';$s_image = '';
		
		foreach($section_image as $sec_image)
		{
			if($sec_image->scribbling_image == '' || $sec_image->scribbling_image == NULL)
			{
				$img='';
			}else{
				$s_image = $sec_image->scribbling_image;
				$s_image = ltrim($s_image, './');
				$head = array_change_key_case(get_headers(base_url($s_image), TRUE));
				$filesize = $head['content-length'];
				if($filesize>0)
					echo $img = "<img style='width:45%' src='".base_url($s_image)."' />";
			}
		}
		?>
				
		<table cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px; padding: 5px">
		<?php
		$labels_data = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$get_hinfo->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
		$ld =0;
		foreach($labels_data as $ld)
		{
			if($ld->field_value=="")
                continue;
                
                
		?>
			<tr>
				<td style="font-size: 12px;">
                        <?php
                        if($last != $ld->field_value)
                        {
                            ?>
                            <p style="line-height:20px !important;font-size: 12px;font-weight: bold">
                            <?php
                            echo $ld->field_value;
                            ?>
                            </p>
                            <?php
                        }
                         ?>  
                         <p style="line-height:20px !important;font-size: 12px;">&emsp;&#8227; <?php echo $ld->option_value; ?> </p>
                    
				</td>
			</tr>
        <?php 
            
            $last = $ld->field_value;
            $ld++; 
        } ?>
		</table>
		
	<?php }else{
		$section_tile = $this->Generic_model->getSingleRecord('section',array('section_id'=>$parent_section_id),'');
		?>
		<span style="font-weight: bold;font-size: 12px;"><?php echo $section_tile->title; ?></span>				
		<span style="font-weight: bold;font-size: 12px;"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?></span>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px">
			<?php
				$labels_data1 = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$get_hinfo->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
				$ld1=0;
				foreach($labels_data1 as $ld1)
				{
						if($ld1->field_value=="")
							continue;
				?>
                <tr>
                    <td style="font-size: 12px;">
                            <?php
                            if($last != $ld->field_value)
                            {
                                ?>
                                <p style="line-height:20px !important;font-size: 12px;font-weight: bold">
                                <?php
                                echo $ld->field_value;
                                ?>
                                </p>
                                <?php
                            }
                             ?>  
                             <p style="line-height:20px !important;font-size: 12px;">&emsp;&#8227; <?php echo $ld->option_value; ?> </p>
                        
                    </td>
                </tr>
            <?php 
                
                $last = $ld->field_value;
                $ld++; } ?>
		</table>
	
	<?php } $s++;$n++; } ?>
<?php 
}

}
?>
<!-- Past History Ends -->

<!-- Personal History STarts -->
<?php if(count($get_personal_history)>0) { 
	?>
    <div class="page-break"></div>
<div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-tranform: uppercase;">Personal History</span></div><br>
		
<?php
$n=0;
foreach ($get_personal_history as $get_hinfo) 
{
    $s=0;
    
    if($n > 0)
    {
        ?>
        <div class="page-break"></div>
        <?php 
    }
    ?>
    
    <?php
	$hopi_data = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$get_hinfo->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
    $get_hopi_form = $this->db->select("form_name")->from("form")->where("form_id='".$get_hinfo->form_id."'")->get()->row();
	
	foreach($hopi_data as $pl){
	$section_text = $this->db->query("select section_text from patient_form_line_items where patient_form_id='".$get_hinfo->patient_form_id."' and section_id='".$pl->section_id."' and (section_text >'' or section_text IS NULL or section_text <'')")->row();
	
	if($section_text->section_text == '')
	{
		$s_text='';
	}else{
		$s_text=$section_text->section_text;
	}
		
	
	$section = $this->Generic_model->getSingleRecord('section',array('section_id'=>$pl->section_id),'');
	$parent_section_id = $section->parent_section_id;
	if($parent_section_id==0){
	?>
	
		<span style="font-size: 12px;padding: 5px"><span style="font-weight: bold"><?php echo $section->title; ?>: </span><?php echo $s_text; ?></span><br>
		<?php
		$section_image = $this->db->query("select scribbling_image from patient_form_scribbling_images where patient_form_id='".$get_hinfo->patient_form_id."'")->result();
		$img = '';$s_image = '';
		
		foreach($section_image as $sec_image)
		{
			if($sec_image->scribbling_image == '' || $sec_image->scribbling_image == NULL)
			{
				$img='';
			}else{
				$s_image = $sec_image->scribbling_image;
				$s_image = ltrim($s_image, './');
				$head = array_change_key_case(get_headers(base_url($s_image), TRUE));
				$filesize = $head['content-length'];
				if($filesize>0)
					echo $img = "<img style='width:45%' src='".base_url($s_image)."' />";
			}
		}
		?>
				
		<table cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px; padding: 5px">
		<?php
		$labels_data = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$get_hinfo->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
		$ld =0;
		foreach($labels_data as $ld)
		{
			if($ld->field_value=="")
                continue;
                
                
		?>
			<tr>
				<td style="font-size: 12px;">
                        <?php
                        if($last != $ld->field_value)
                        {
                            ?>
                            <p style="line-height:20px !important;font-size: 12px;font-weight: bold">
                            <?php
                            echo $ld->field_value;
                            ?>
                            </p>
                            <?php
                        }
                         ?>  
                         <p style="line-height:20px !important;font-size: 12px;">&emsp;&#8227; <?php echo $ld->option_value; ?> </p>
                    
				</td>
			</tr>
        <?php 
            
            $last = $ld->field_value;
            $ld++; 
        } ?>
		</table>
		
	<?php }else{
		$section_tile = $this->Generic_model->getSingleRecord('section',array('section_id'=>$parent_section_id),'');
		?>
		<span style="font-weight: bold;font-size: 12px;"><?php echo $section_tile->title; ?></span>				
		<span style="font-weight: bold;font-size: 12px;"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?></span>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px">
			<?php
				$labels_data1 = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$get_hinfo->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
				$ld1=0;
				foreach($labels_data1 as $ld1)
				{
						if($ld1->field_value=="")
							continue;
				?>
                <tr>
                    <td style="font-size: 12px;">
                            <?php
                            if($last != $ld->field_value)
                            {
                                ?>
                                <p style="line-height:20px !important;font-size: 12px;font-weight: bold">
                                <?php
                                echo $ld->field_value;
                                ?>
                                </p>
                                <?php
                            }
                             ?>  
                             <p style="line-height:20px !important;font-size: 12px;">&emsp;&#8227; <?php echo $ld->option_value; ?> </p>
                        
                    </td>
                </tr>
            <?php 
                
                $last = $ld->field_value;
                $ld++; } ?>
		</table>
	
	<?php } $s++;$n++; } ?>
<?php 
}

}
?>
<!-- Personal History Ends -->

<!-- Social History STarts -->
<?php if(count($get_social_history)>0) { 
	?>
    <div class="page-break"></div>
<div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-tranform: uppercase;">Social History</span></div><br>
		
<?php
$n=0;
foreach ($get_social_history as $get_hinfo) 
{
    $s=0;
    
    if($n > 0)
    {
        ?>
        <div class="page-break"></div>
        <?php 
    }
    ?>
    
    <?php
	$hopi_data = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$get_hinfo->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
    $get_hopi_form = $this->db->select("form_name")->from("form")->where("form_id='".$get_hinfo->form_id."'")->get()->row();
	
	foreach($hopi_data as $pl){
	$section_text = $this->db->query("select section_text from patient_form_line_items where patient_form_id='".$get_hinfo->patient_form_id."' and section_id='".$pl->section_id."' and (section_text >'' or section_text IS NULL or section_text <'')")->row();
	
	if($section_text->section_text == '')
	{
		$s_text='';
	}else{
		$s_text=$section_text->section_text;
	}
		
	
	$section = $this->Generic_model->getSingleRecord('section',array('section_id'=>$pl->section_id),'');
	$parent_section_id = $section->parent_section_id;
	if($parent_section_id==0){
	?>
	
		<span style="font-size: 12px;padding: 5px"><span style="font-weight: bold"><?php echo $section->title; ?>: </span><?php echo $s_text; ?></span><br>
		<?php
		$section_image = $this->db->query("select scribbling_image from patient_form_scribbling_images where patient_form_id='".$get_hinfo->patient_form_id."'")->result();
		$img = '';$s_image = '';
		
		foreach($section_image as $sec_image)
		{
			if($sec_image->scribbling_image == '' || $sec_image->scribbling_image == NULL)
			{
				$img='';
			}else{
				$s_image = $sec_image->scribbling_image;
				$s_image = ltrim($s_image, './');
				$head = array_change_key_case(get_headers(base_url($s_image), TRUE));
				$filesize = $head['content-length'];
				if($filesize>0)
					echo $img = "<img style='width:45%' src='".base_url($s_image)."' />";
			}
		}
		?>
				
		<table cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px; padding: 5px">
		<?php
		$labels_data = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$get_hinfo->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
		$ld =0;
		foreach($labels_data as $ld)
		{
			if($ld->field_value=="")
                continue;
                
                
		?>
			<tr>
				<td style="font-size: 12px;">
                        <?php
                        if($last != $ld->field_value)
                        {
                            ?>
                            <p style="line-height:20px !important;font-size: 12px;font-weight: bold">
                            <?php
                            echo $ld->field_value;
                            ?>
                            </p>
                            <?php
                        }
                         ?>  
                         <p style="line-height:20px !important;font-size: 12px;">&emsp;&#8227; <?php echo $ld->option_value; ?> </p>
                    
				</td>
			</tr>
        <?php 
            
            $last = $ld->field_value;
            $ld++; 
        } ?>
		</table>
		
	<?php }else{
		$section_tile = $this->Generic_model->getSingleRecord('section',array('section_id'=>$parent_section_id),'');
		?>
		<span style="font-weight: bold;font-size: 12px;"><?php echo $section_tile->title; ?></span>				
		<span style="font-weight: bold;font-size: 12px;"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?></span>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px">
			<?php
				$labels_data1 = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$get_hinfo->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
				$ld1=0;
				foreach($labels_data1 as $ld1)
				{
						if($ld1->field_value=="")
							continue;
				?>
                <tr>
                    <td style="font-size: 12px;">
                            <?php
                            if($last != $ld->field_value)
                            {
                                ?>
                                <p style="line-height:20px !important;font-size: 12px;font-weight: bold">
                                <?php
                                echo $ld->field_value;
                                ?>
                                </p>
                                <?php
                            }
                             ?>  
                             <p style="line-height:20px !important;font-size: 12px;">&emsp;&#8227; <?php echo $ld->option_value; ?> </p>
                        
                    </td>
                </tr>
            <?php 
                
                $last = $ld->field_value;
                $ld++; } ?>
		</table>
	
	<?php } $s++;$n++; } ?>
<?php 
}

}
?>
<!-- Social History Ends -->

<!-- Family History STarts -->
<?php if(count($get_family_history)>0) { 
	?>
    <div class="page-break"></div>
<div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-tranform: uppercase;">Family History</span></div><br>
		
<?php
$n=0;
foreach ($get_family_history as $get_hinfo) 
{
    $s=0;
    
    if($n > 0)
    {
        ?>
        <div class="page-break"></div>
        <?php 
    }
    ?>
    
    <?php
	$hopi_data = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$get_hinfo->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
    $get_hopi_form = $this->db->select("form_name")->from("form")->where("form_id='".$get_hinfo->form_id."'")->get()->row();
	
	foreach($hopi_data as $pl){
	$section_text = $this->db->query("select section_text from patient_form_line_items where patient_form_id='".$get_hinfo->patient_form_id."' and section_id='".$pl->section_id."' and (section_text >'' or section_text IS NULL or section_text <'')")->row();
	
	if($section_text->section_text == '')
	{
		$s_text='';
	}else{
		$s_text=$section_text->section_text;
	}
		
	
	$section = $this->Generic_model->getSingleRecord('section',array('section_id'=>$pl->section_id),'');
	$parent_section_id = $section->parent_section_id;
	if($parent_section_id==0){
	?>
	
		<span style="font-size: 12px;padding: 5px"><span style="font-weight: bold"><?php echo $section->title; ?>: </span><?php echo $s_text; ?></span><br>
		<?php
		$section_image = $this->db->query("select scribbling_image from patient_form_scribbling_images where patient_form_id='".$get_hinfo->patient_form_id."'")->result();
		$img = '';$s_image = '';
		
		foreach($section_image as $sec_image)
		{
			if($sec_image->scribbling_image == '' || $sec_image->scribbling_image == NULL)
			{
				$img='';
			}else{
				$s_image = $sec_image->scribbling_image;
				$s_image = ltrim($s_image, './');
				$head = array_change_key_case(get_headers(base_url($s_image), TRUE));
				$filesize = $head['content-length'];
				if($filesize>0)
					echo $img = "<img style='width:45%' src='".base_url($s_image)."' />";
			}
		}
		?>
				
		<table cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px; padding: 5px">
		<?php
		$labels_data = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$get_hinfo->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
		$ld =0;
		foreach($labels_data as $ld)
		{
			if($ld->field_value=="")
                continue;
                
                
		?>
			<tr>
				<td style="font-size: 12px;">
                        <?php
                        if($last != $ld->field_value)
                        {
                            ?>
                            <p style="line-height:20px !important;font-size: 12px;font-weight: bold">
                            <?php
                            echo $ld->field_value;
                            ?>
                            </p>
                            <?php
                        }
                         ?>  
                         <p style="line-height:20px !important;font-size: 12px;">&emsp;&#8227; <?php echo $ld->option_value; ?> </p>
                    
				</td>
			</tr>
        <?php 
            
            $last = $ld->field_value;
            $ld++; 
        } ?>
		</table>
		
	<?php }else{
		$section_tile = $this->Generic_model->getSingleRecord('section',array('section_id'=>$parent_section_id),'');
		?>
		<span style="font-weight: bold;font-size: 12px;"><?php echo $section_tile->title; ?></span>				
		<span style="font-weight: bold;font-size: 12px;"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?></span>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px">
			<?php
				$labels_data1 = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$get_hinfo->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
				$ld1=0;
				foreach($labels_data1 as $ld1)
				{
						if($ld1->field_value=="")
							continue;
				?>
                <tr>
                    <td style="font-size: 12px;">
                            <?php
                            if($last != $ld->field_value)
                            {
                                ?>
                                <p style="line-height:20px !important;font-size: 12px;font-weight: bold">
                                <?php
                                echo $ld->field_value;
                                ?>
                                </p>
                                <?php
                            }
                             ?>  
                             <p style="line-height:20px !important;font-size: 12px;">&emsp;&#8227; <?php echo $ld->option_value; ?> </p>
                        
                    </td>
                </tr>
            <?php 
                
                $last = $ld->field_value;
                $ld++; } ?>
		</table>
	
	<?php } $s++;$n++; } ?>
<?php 
}

}
?>
<!-- Family History Ends -->

<!-- Treatment History STarts -->
<?php if(count($get_treatment_history)>0) { 
	?>
    <div class="page-break"></div>
<div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-tranform: uppercase;">Treatment History</span></div><br>
		
<?php
$n=0;
foreach ($get_treatment_history as $get_hinfo) 
{
    $s=0;
    
    if($n > 0)
    {
        ?>
        <div class="page-break"></div>
        <?php 
    }
    ?>
    
    <?php
	$hopi_data = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$get_hinfo->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
    $get_hopi_form = $this->db->select("form_name")->from("form")->where("form_id='".$get_hinfo->form_id."'")->get()->row();
	
	foreach($hopi_data as $pl){
	$section_text = $this->db->query("select section_text from patient_form_line_items where patient_form_id='".$get_hinfo->patient_form_id."' and section_id='".$pl->section_id."' and (section_text >'' or section_text IS NULL or section_text <'')")->row();
	
	if($section_text->section_text == '')
	{
		$s_text='';
	}else{
		$s_text=$section_text->section_text;
	}
		
	
	$section = $this->Generic_model->getSingleRecord('section',array('section_id'=>$pl->section_id),'');
	$parent_section_id = $section->parent_section_id;
	if($parent_section_id==0){
	?>
	
		<span style="font-size: 12px;padding: 5px"><span style="font-weight: bold"><?php echo $section->title; ?>: </span><?php echo $s_text; ?></span><br>
		<?php
		$section_image = $this->db->query("select scribbling_image from patient_form_scribbling_images where patient_form_id='".$get_hinfo->patient_form_id."'")->result();
		$img = '';$s_image = '';
		
		foreach($section_image as $sec_image)
		{
			if($sec_image->scribbling_image == '' || $sec_image->scribbling_image == NULL)
			{
				$img='';
			}else{
				$s_image = $sec_image->scribbling_image;
				$s_image = ltrim($s_image, './');
				$head = array_change_key_case(get_headers(base_url($s_image), TRUE));
				$filesize = $head['content-length'];
				if($filesize>0)
					echo $img = "<img style='width:45%' src='".base_url($s_image)."' />";
			}
		}
		?>
				
		<table cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px; padding: 5px">
		<?php
		$labels_data = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$get_hinfo->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
		$ld =0;
		foreach($labels_data as $ld)
		{
			if($ld->field_value=="")
                continue;
                
                
		?>
			<tr>
				<td style="font-size: 12px;">
                        <?php
                        if($last != $ld->field_value)
                        {
                            ?>
                            <p style="line-height:20px !important;font-size: 12px;font-weight: bold">
                            <?php
                            echo $ld->field_value;
                            ?>
                            </p>
                            <?php
                        }
                         ?>  
                         <p style="line-height:20px !important;font-size: 12px;">&emsp;&#8227; <?php echo $ld->option_value; ?> </p>
                    
				</td>
			</tr>
        <?php 
            
            $last = $ld->field_value;
            $ld++; 
        } ?>
		</table>
		
	<?php }else{
		$section_tile = $this->Generic_model->getSingleRecord('section',array('section_id'=>$parent_section_id),'');
		?>
		<span style="font-weight: bold;font-size: 12px;"><?php echo $section_tile->title; ?></span>				
		<span style="font-weight: bold;font-size: 12px;"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?></span>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px">
			<?php
				$labels_data1 = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$get_hinfo->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
				$ld1=0;
				foreach($labels_data1 as $ld1)
				{
						if($ld1->field_value=="")
							continue;
				?>
                <tr>
                    <td style="font-size: 12px;">
                            <?php
                            if($last != $ld->field_value)
                            {
                                ?>
                                <p style="line-height:20px !important;font-size: 12px;font-weight: bold">
                                <?php
                                echo $ld->field_value;
                                ?>
                                </p>
                                <?php
                            }
                             ?>  
                             <p style="line-height:20px !important;font-size: 12px;">&emsp;&#8227; <?php echo $ld->option_value; ?> </p>
                        
                    </td>
                </tr>
            <?php 
                
                $last = $ld->field_value;
                $ld++; } ?>
		</table>
	
	<?php } $s++;$n++; } ?>
<?php 
}

}
?>
<!-- Treatment History Ends -->

<!-- GPE STarts -->
<?php if(count($getgpe_info)>0) { 
	?>
    <div class="page-break"></div>
<div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-tranform: uppercase;">General Physical Examination</span></div><br>
		
<?php
$n=0;
foreach ($getgpe_info as $get_hinfo) 
{
    $s=0;
    
    if($n > 0)
    {
        ?>
        <div class="page-break"></div>
        <?php 
    }
    ?>
    
		<?php
		$section_image = $this->db->query("select scribbling_image from patient_form_scribbling_images where patient_form_id='".$get_hinfo->patient_form_id."'")->result();
		$img = '';$s_image = '';
		
		foreach($section_image as $sec_image)
		{
			if($sec_image->scribbling_image == '' || $sec_image->scribbling_image == NULL)
			{
				$img='';
			}else{
				$s_image = $sec_image->scribbling_image;
				$s_image = ltrim($s_image, './');
				$head = array_change_key_case(get_headers(base_url($s_image), TRUE));
				$filesize = $head['content-length'];
				if($filesize>0)
					echo $img = "<img style='width:45%' src='".base_url($s_image)."' />";
			}
		}
		?>
    <?php
	$hopi_data = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$get_hinfo->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
    $get_hopi_form = $this->db->select("form_name")->from("form")->where("form_id='".$get_hinfo->form_id."'")->get()->row();
	
	foreach($hopi_data as $pl){
	$section_text = $this->db->query("select section_text from patient_form_line_items where patient_form_id='".$get_hinfo->patient_form_id."' and section_id='".$pl->section_id."' and (section_text >'' or section_text IS NULL or section_text <'')")->row();
	
	if($section_text->section_text == '')
	{
		$s_text='';
	}else{
		$s_text=$section_text->section_text;
	}
		
	
	$section = $this->Generic_model->getSingleRecord('section',array('section_id'=>$pl->section_id),'');
	$parent_section_id = $section->parent_section_id;
	if($parent_section_id==0){
	?>
	
		<p style="font-size: 12px;padding: 5px"><span style="font-weight: bold"><?php echo $section->title; ?>: </span><?php echo $s_text; ?></p>
		<table cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px; padding: 5px">
		<?php
		$labels_data = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$get_hinfo->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
		$ld =0;
		foreach($labels_data as $ld)
		{
			if($ld->field_value=="")
                continue;
                
                
		?>
			<tr>
				<td style="font-size: 12px;">
                        <?php
                        if($last != $ld->field_value)
                        {
                            ?>
                            <p style="line-height:20px !important;font-size: 12px;font-weight: bold">
                            <?php
                            echo $ld->field_value;
                            ?>
                            </p>
                            <?php
                        }
                         ?>  
                         <p style="line-height:20px !important;font-size: 12px;">&emsp;&#8227; <?php echo $ld->option_value; ?> </p>
                    
				</td>
			</tr>
        <?php 
            
            $last = $ld->field_value;
            $ld++; 
        } ?>
		</table>
		
	<?php }else{
		$section_tile = $this->Generic_model->getSingleRecord('section',array('section_id'=>$parent_section_id),'');
		?>
		<span style="font-weight: bold;font-size: 12px;"><?php echo $section_tile->title; ?></span>				
		<span style="font-weight: bold;font-size: 12px;"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?></span>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px">
			<?php
				$labels_data1 = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$get_hinfo->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
				$ld1=0;
				foreach($labels_data1 as $ld1)
				{
						if($ld1->field_value=="")
							continue;
				?>
                <tr>
                    <td style="font-size: 12px;">
                            <?php
                            if($last != $ld->field_value)
                            {
                                ?>
                                <p style="line-height:20px !important;font-size: 12px;font-weight: bold">
                                <?php
                                echo $ld->field_value;
                                ?>
                                </p>
                                <?php
                            }
                             ?>  
                             <p style="line-height:20px !important;font-size: 12px;">&emsp;&#8227; <?php echo $ld->option_value; ?> </p>
                        
                    </td>
                </tr>
            <?php 
                
                $last = $ld->field_value;
                $ld++; } ?>
		</table>
	
	<?php } $s++;$n++; } ?>
<?php 
}

}
?>
<!-- GPE Ends -->

<!-- SE STarts -->
<?php if(count($getse_info)>0) { 
	?>
    <div class="page-break"></div>
<div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-tranform: uppercase;">Systemic Examination</span></div><br>
		
<?php
$n=0;
foreach ($getse_info as $get_hinfo) 
{
    $s=0;
    
    if($n > 0)
    {
        ?>
        <div class="page-break"></div>
        <?php 
    }
    ?>
    <?php
    $section_image = $this->db->query("select scribbling_image from patient_form_scribbling_images where patient_form_id='".$get_hinfo->patient_form_id."'")->result();
    $img = '';$s_image = '';
    
    foreach($section_image as $sec_image)
    {
        if($sec_image->scribbling_image == '' || $sec_image->scribbling_image == NULL)
        {
            $img='';
        }else{
            $s_image = $sec_image->scribbling_image;
            $s_image = ltrim($s_image, './');
            $head = array_change_key_case(get_headers(base_url($s_image), TRUE));
            $filesize = $head['content-length'];
            if($filesize>0)
                echo $img = "<img style='width:45%' src='".base_url($s_image)."' />";
        }
    }
    ?>
    
    <?php
	$hopi_data = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$get_hinfo->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
    $get_hopi_form = $this->db->select("form_name")->from("form")->where("form_id='".$get_hinfo->form_id."'")->get()->row();
	
	foreach($hopi_data as $pl){
	$section_text = $this->db->query("select section_text from patient_form_line_items where patient_form_id='".$get_hinfo->patient_form_id."' and section_id='".$pl->section_id."' and (section_text >'' or section_text IS NULL or section_text <'')")->row();
	
	if($section_text->section_text == '')
	{
		$s_text='';
	}else{
		$s_text=$section_text->section_text;
	}
		
	
	$section = $this->Generic_model->getSingleRecord('section',array('section_id'=>$pl->section_id),'');
	$parent_section_id = $section->parent_section_id;
	if($parent_section_id==0){
	?>
	
		<p style="font-size: 12px;padding: 5px"><span style="font-weight: bold"><?php echo $section->title; ?>: </span><?php echo $s_text; ?></p>
				
		<table cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px; padding: 5px">
		<?php
		$labels_data = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$get_hinfo->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
		$ld =0;
		foreach($labels_data as $ld)
		{
			if($ld->field_value=="")
                continue;
                
                
		?>
			<tr>
				<td style="font-size: 12px;">
                        <?php
                        if($last != $ld->field_value)
                        {
                            ?>
                            <p style="line-height:20px !important;font-size: 12px;font-weight: bold">
                            <?php
                            echo $ld->field_value;
                            ?>
                            </p>
                            <?php
                        }
                         ?>  
                         <p style="line-height:20px !important;font-size: 12px;">&emsp;&#8227; <?php echo $ld->option_value; ?> </p>
                    
				</td>
			</tr>
        <?php 
            
            $last = $ld->field_value;
            $ld++; 
        } ?>
		</table>
		
	<?php }else{
		$section_tile = $this->Generic_model->getSingleRecord('section',array('section_id'=>$parent_section_id),'');
		?>
		<span style="font-weight: bold;font-size: 12px;"><?php echo $section_tile->title; ?></span>				
		<span style="font-weight: bold;font-size: 12px;"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?></span>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px">
			<?php
				$labels_data1 = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$get_hinfo->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
				$ld1=0;
				foreach($labels_data1 as $ld1)
				{
						if($ld1->field_value=="")
							continue;
				?>
                <tr>
                    <td style="font-size: 12px;">
                            <?php
                            if($last != $ld->field_value)
                            {
                                ?>
                                <p style="line-height:20px !important;font-size: 12px;font-weight: bold">
                                <?php
                                echo $ld->field_value;
                                ?>
                                </p>
                                <?php
                            }
                             ?>  
                             <p style="line-height:20px !important;font-size: 12px;">&emsp;&#8227; <?php echo $ld->option_value; ?> </p>
                        
                    </td>
                </tr>
            <?php 
                
                $last = $ld->field_value;
                $ld++; } ?>
		</table>
	
	<?php } $s++;$n++; } ?>
<?php 
}

}
?>
<!-- SE Ends -->

<!-- OS STarts -->
<?php if(count($getos_info)>0) { 
	?>
    <div class="page-break"></div>
<div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold;text-tranform: uppercase;">Other Systems</span></div><br>
		
<?php
$n=0;
foreach ($getos_info as $get_hinfo) 
{
    $s=0;
    
    if($n > 0)
    {
        ?>
        <div class="page-break"></div>
        <?php 
    }
    ?>
    <?php
    $section_image = $this->db->query("select scribbling_image from patient_form_scribbling_images where patient_form_id='".$get_hinfo->patient_form_id."'")->result();
    $img = '';$s_image = '';
    
    foreach($section_image as $sec_image)
    {
        if($sec_image->scribbling_image == '' || $sec_image->scribbling_image == NULL)
        {
            $img='';
        }else{
            $s_image = $sec_image->scribbling_image;
            $s_image = ltrim($s_image, './');
            $head = array_change_key_case(get_headers(base_url($s_image), TRUE));
            $filesize = $head['content-length'];
            if($filesize>0)
                echo $img = "<img style='width:45%' src='".base_url($s_image)."' />";
        }
    }
    ?>
    
    <?php
	$hopi_data = $this->db->select("DISTINCT(section_id) as section_id")->from("patient_form_line_items pfl")->join("patient_form pf","pfl.patient_form_id = pf.patient_form_id")->where("pf.patient_form_id='".$get_hinfo->patient_form_id."'")->order_by("pfl.section_id","asc")->get()->result();
    $get_hopi_form = $this->db->select("form_name")->from("form")->where("form_id='".$get_hinfo->form_id."'")->get()->row();
	
	foreach($hopi_data as $pl){
	$section_text = $this->db->query("select section_text from patient_form_line_items where patient_form_id='".$get_hinfo->patient_form_id."' and section_id='".$pl->section_id."' and (section_text >'' or section_text IS NULL or section_text <'')")->row();
	
	if($section_text->section_text == '')
	{
		$s_text='';
	}else{
		$s_text=$section_text->section_text;
	}
		
	
	$section = $this->Generic_model->getSingleRecord('section',array('section_id'=>$pl->section_id),'');
	$parent_section_id = $section->parent_section_id;
	if($parent_section_id==0){
	?>
	
		<p style="font-size: 12px;padding: 5px"><span style="font-weight: bold"><?php echo $section->title; ?>: </span><?php echo $s_text; ?></p>
				
		<table cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px; padding: 5px">
		<?php
		$labels_data = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$get_hinfo->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
		$ld =0;
		foreach($labels_data as $ld)
		{
			if($ld->field_value=="")
                continue;
                
                
		?>
			<tr>
				<td style="font-size: 12px;">
                        <?php
                        if($last != $ld->field_value)
                        {
                            ?>
                            <p style="line-height:20px !important;font-size: 12px;font-weight: bold">
                            <?php
                            echo $ld->field_value;
                            ?>
                            </p>
                            <?php
                        }
                         ?>  
                         <p style="line-height:20px !important;font-size: 12px;">&emsp;&#8227; <?php echo $ld->option_value; ?> </p>
                    
				</td>
			</tr>
        <?php 
            
            $last = $ld->field_value;
            $ld++; 
        } ?>
		</table>
		
	<?php }else{
		$section_tile = $this->Generic_model->getSingleRecord('section',array('section_id'=>$parent_section_id),'');
		?>
		<span style="font-weight: bold;font-size: 12px;"><?php echo $section_tile->title; ?></span>				
		<span style="font-weight: bold;font-size: 12px;"><b><?php echo $section->title; ?>: </b><?php echo $s_text; ?></span>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 5px">
			<?php
				$labels_data1 = $this->Generic_model->getAllRecords('patient_form_line_items',array('patient_form_id'=>$get_hinfo->patient_form_id,'section_id'=>$pl->section_id),array('field'=>'patient_form_line_items_id','type'=>'asc'));
				$ld1=0;
				foreach($labels_data1 as $ld1)
				{
						if($ld1->field_value=="")
							continue;
				?>
                <tr>
                    <td style="font-size: 12px;">
                            <?php
                            if($last != $ld->field_value)
                            {
                                ?>
                                <p style="line-height:20px !important;font-size: 12px;font-weight: bold">
                                <?php
                                echo $ld->field_value;
                                ?>
                                </p>
                                <?php
                            }
                             ?>  
                             <p style="line-height:20px !important;font-size: 12px;">&emsp;&#8227; <?php echo $ld->option_value; ?> </p>
                        
                    </td>
                </tr>
            <?php 
                
                $last = $ld->field_value;
                $ld++; } ?>
		</table>
	
	<?php } $s++;$n++; } ?>
<?php 
}

}
?>
<!-- OS Ends -->

<!-- Procedures Starts -->

<?php
	if(count($patient_procedures)>0){
		// $this->load->view("reports/default_pdf_header");
				?><div class="page-break"></div>
                <?php
		$i=0;
		foreach ($patient_procedures as $value) {
			if($i>0)
			{
				?>
				<div class="page-break"></div>
				<?php
				$this->load->view("reports/default_pdf_header");
			}
			?>

			<div style="font-weight: bold;text-align: center;text-transform: uppercase"><?=$value->procedure_title?> - PROCEDURE</div><br>
			<table cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px;">
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Performed By : </span>
						<span style="font-size: 12px"><?=$value->surgeon?></span></td>
					<td><span style="font-weight: bold;font-size: 12px">Anaesthetist : </span>
						<span style="font-size: 12px"><?=$value->anesthetist?></span></td>
				</tr>
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Assisted Doctor : </span>
						<span style="font-size: 12px"><?=$value->assisting_surgeon?></span></td>
					<td><span style="font-weight: bold;font-size: 12px">Assisted Nurse : </span>
						<span style="font-size: 12px"><?=$value->assisting_nurse?></span></td>
				</tr>
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Type Of Anaesthesia : </span>
						<span style="font-size: 12px"><?=$value->type_of_anesthesia?></span></td>
				</tr>
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Preoperative Diagnosis : </span>
						<span style="font-size: 12px"><?=$value->preoperative_diagnosis?></span></td>
				</tr>
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Postoperative Diagnosis : </span>
						<span style="font-size: 12px"><?=$value->postoperative_diagnosis?></span></td>
				</tr>
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Indication : </span>
						<span style="font-size: 12px"><?=$value->indication?></span></td>
				</tr>
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Position : </span>
						<span style="font-size: 12px"><?=$value->position?></span></td>
				</tr>
			</table>
			<div style="font-size: 12px">
				<?=$value->medical_procedure?>
			</div>
			<?php
			$i++;
		}
		?>

	<!-- <div class="page-break"></div> -->
		<?php
	}
?>
<!-- Procedures Ends -->

<!-- web_patient_forms -->
<?php
	if(count($web_consent_forms)>0){
		// $this->load->view("reports/default_pdf_header");
		$i=0;
		foreach ($web_consent_forms as $value) {
			if($i>0)
			{
				
				?>
				<div class="page-break"></div>
				<?php
				// $this->load->view("reports/default_pdf_header");
			}
			?>
<div style="font-weight: bold;text-transform: uppercase"> Consent Form</div><br>
			<!-- <div style="font-weight: bold;text-align: center;text-transform: uppercase"><?=$value->procedure_title?> - PROCEDURE</div><br> -->
			<!-- <table cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px;">
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Performed By : </span>
						<span style="font-size: 12px"><?=$value->surgeon?></span></td>
					<td><span style="font-weight: bold;font-size: 12px">Anaesthetist : </span>
						<span style="font-size: 12px"><?=$value->anesthetist?></span></td>
				</tr>
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Assisted Doctor : </span>
						<span style="font-size: 12px"><?=$value->assisting_surgeon?></span></td>
					<td><span style="font-weight: bold;font-size: 12px">Assisted Nurse : </span>
						<span style="font-size: 12px"><?=$value->assisting_nurse?></span></td>
				</tr>
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Type Of Anaesthesia : </span>
						<span style="font-size: 12px"><?=$value->type_of_anesthesia?></span></td>
				</tr>
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Preoperative Diagnosis : </span>
						<span style="font-size: 12px"><?=$value->preoperative_diagnosis?></span></td>
				</tr>
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Postoperative Diagnosis : </span>
						<span style="font-size: 12px"><?=$value->postoperative_diagnosis?></span></td>
				</tr>
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Indication : </span>
						<span style="font-size: 12px"><?=$value->indication?></span></td>
				</tr>
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Position : </span>
						<span style="font-size: 12px"><?=$value->position?></span></td>
				</tr>
			</table> -->
			<div style="font-size: 12px">
				<?=$value->patient_consent_form_description?>
			</div>
			<?php
			$i++;
		}
		?>

	<!-- <div class="page-break"></div> -->
		<?php
	}
?>
<!-- web_patient_forms_end -->

<!-- more_forms -->
<?php
	if(count($more_forms)>0){
		// $this->load->view("reports/default_pdf_header");
		$i=0;
		foreach ($more_forms as $value) {
			if($i>0)
			{
				?>
				<div class="page-break"></div>
				<?php
				// $this->load->view("reports/default_pdf_header");
			}
			?>

			<!-- <div style="font-weight: bold;text-align: center;text-transform: uppercase"><?=$value->procedure_title?> - PROCEDURE</div><br> -->
			<!-- <table cellspacing="0" cellpadding="0" style="width: 100%;font-size: 12px;">
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Performed By : </span>
						<span style="font-size: 12px"><?=$value->surgeon?></span></td>
					<td><span style="font-weight: bold;font-size: 12px">Anaesthetist : </span>
						<span style="font-size: 12px"><?=$value->anesthetist?></span></td>
				</tr>
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Assisted Doctor : </span>
						<span style="font-size: 12px"><?=$value->assisting_surgeon?></span></td>
					<td><span style="font-weight: bold;font-size: 12px">Assisted Nurse : </span>
						<span style="font-size: 12px"><?=$value->assisting_nurse?></span></td>
				</tr>
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Type Of Anaesthesia : </span>
						<span style="font-size: 12px"><?=$value->type_of_anesthesia?></span></td>
				</tr>
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Preoperative Diagnosis : </span>
						<span style="font-size: 12px"><?=$value->preoperative_diagnosis?></span></td>
				</tr>
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Postoperative Diagnosis : </span>
						<span style="font-size: 12px"><?=$value->postoperative_diagnosis?></span></td>
				</tr>
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Indication : </span>
						<span style="font-size: 12px"><?=$value->indication?></span></td>
				</tr>
				<tr>
					<td><span style="font-weight: bold;font-size: 12px">Position : </span>
						<span style="font-size: 12px"><?=$value->position?></span></td>
				</tr>
			</table> -->
			<div style="font-weight: bold;text-transform: uppercase"> <?=$value->name?></div><br>
			<div style="font-size: 12px">
				<?=$value->description?>
			</div>
			<?php
			$i++;
		}
		?>

	<!-- <div class="page-break"></div> -->
		<?php
	}
?>
<!-- more_forms_end -->


<!-- Full Summary Ends    -->


<htmlpagefooter name="footer">
    <table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;<?=$style?>">
        <tr>
            <td style="width:45%">
                <span style="font-weight: bold;font-size: 12px">Powered By umdaa.co</span>
            </td>
            <td style="width: 20%">
                <span style="font-weight: bold;font-size: 12px;text-align: center;">Page {PAGENO} of {nb}</span>
                <!-- <span style="font-weight: bold;font-size: 12px;text-align: center;"><?=('{PAGENO}' < '{nb}')?'P.T.O':''?></span> -->
            </td>
            <td style="width:35%;text-align: right;">
                <span style="font-weight: bold;font-size: 12px"><b>Date: </b><?php echo date("d M Y h:i A", strtotime($appointments->created_date_time)); ?></span>
            </td>
        </tr>
    </table>
</htmlpagefooter>