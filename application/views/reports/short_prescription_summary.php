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
<?php
if(count($pdfSettings) > 0){
    if($pdfSettings->header == 0){
        ?>
        <htmlpageheader name="firstpage">

<!-- Clinic Block -->
<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
    <tr>
        <td style="width:40%">
            <img style="width:50%" alt="" src="<?php echo base_url(); ?>uploads/clinic_logos/<?php echo $clinicsInfo->clinic_logo; ?>">
        </td>
        <td style="width: 25%"></td>
        <td style="width:35%;text-align: right;">
            <div style="<?=($pdfSettings->doc_details == 0)?'display: none':''?>">
                <p style="font-weight:bold"><?=strtoupper(getDoctorName($docInfo->doctor_id))?></p>
                <p style="font-size: 12px !important"><?=$docInfo->qualification?> - <?=$docInfo->department_name?></p>
                <p style="font-size: 12px !important">Reg. No:<?=$docInfo->registration_code?></p>
            </div>
            <span style="font-weight: bold;font-size: 12px">Address</span><br><span style="font-weight: 700;font-size: 12px"><?php echo $clinicsInfo->address; ?></span><br><span style=" font-weight: bold;font-size: 12px">Phone:</span><span style="font-weight: 700;font-size: 12px"><?php echo $clinicsInfo->clinic_phone; ?></span>
        </td>
    </tr>
</table>

<hr>

<!-- Patient Info -->
<table class="table" cellpadding="0" cellspacing="0" style="font-size:12px !important;">
    <tbody>
        <tr>
            <td style="padding: 3px 0px !important;width:50%" class="text-left">
                <p style="font-weight:bold"><?=strtoupper(getPatientName($patientInfo->patient_id))?></p>
                <p  style="font-size: 12px !important">UMR : <?=$patientInfo->umr_no?></p>
                <p  style="font-size: 12px !important"><?=$patientInfo->age?><?=$patientInfo->age_unit?> <?=$patientInfo->gender?></p>
            </td>
            
            <td style="padding: 3px 0px !important;vertical-align:top;width:50%" class="text-right">
                <p style="font-weight:bold">Address</p>
                <p  style="font-size: 12px !important"><?=DataCrypt($patientInfo->mobile, 'decrypt')?></p>
                <p  style="font-size: 12px !important"><?=$patientInfo->location?></p>
            </td>
            
            <!-- <td style="padding: 3px 0px !important;vertical-align:top;width:35%;<?=($pdfSettings->doc_details == 0)?'display: none':''?>" class="text-right">
               
            </td> -->
        </tr>
    </tbody>
</table>

</htmlheader>

<hr>
        <?php
    }
    else{
        ?>
		<div style="padding-top: <?=$pdfSettings->header_height?>px !important">&nbsp;
		</div>
                <!-- <htmlpageheader name="firstpage"> -->

<!-- Patient Info -->
<table class="table" cellpadding="0" cellspacing="0" style="font-size:12px !important;">
    <tbody>
        <tr>
            <td style="padding: 3px 0px !important;width:50%" class="text-left">
                <p style="font-weight:bold"><?=strtoupper(getPatientName($patientInfo->patient_id))?></p>
                <p  style="font-size: 12px !important">UMR : <?=$patientInfo->umr_no?></p>
                <p  style="font-size: 12px !important"><?=$patientInfo->age?><?=$patientInfo->age_unit?> <?=$patientInfo->gender?></p>
            </td>
            
            <td style="padding: 3px 0px !important;vertical-align:top;width:50%" class="text-center">
                <p style="font-weight:bold">Address</p>
                <p  style="font-size: 12px !important"><?=DataCrypt($patientInfo->mobile, 'decrypt')?></p>
                <p  style="font-size: 12px !important"><?=$patientInfo->location?></p>
            </td>
            
            <!-- <td style="padding: 3px 0px !important;vertical-align:top;width:35%" class="text-right">
                <p style="font-weight:bold"><?=strtoupper(getDoctorName($docInfo->doctor_id))?></p>
                <p  style="font-size: 12px !important"><?=$docInfo->qualification?> - <?=$docInfo->department_name?></p>
                <p  style="font-size: 12px !important">Reg. No:<?=$docInfo->registration_code?></p>
            </td> -->
        </tr>
    </tbody>
</table>

<!-- </htmlheader> -->

<hr>
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
            <img style="width:50%" alt="" src="<?php echo base_url(); ?>uploads/clinic_logos/<?php echo $clinicsInfo->clinic_logo; ?>">
        </td>
        <td style="width: 25%"></td>
        <td style="width:35%;text-align: right;">
            <div style="<?=($pdfSettings->doc_details == 0)?'display: none':''?>">
                <p style="font-weight:bold"><?=strtoupper(getDoctorName($docInfo->doctor_id))?></p>
                <p style="font-size: 12px !important"><?=$docInfo->qualification?> - <?=$docInfo->department_name?></p>
                <p style="font-size: 12px !important">Reg. No:<?=$docInfo->registration_code?></p>
            </div>
            <span style="font-weight: bold;font-size: 12px">Address</span><br><span style="font-weight: 700;font-size: 12px"><?php echo $clinicsInfo->address; ?></span><br><span style=" font-weight: bold;font-size: 12px">Phone:</span><span style="font-weight: 700;font-size: 12px"><?php echo $clinicsInfo->clinic_phone; ?></span>
        </td>
    </tr>
</table>

<hr>

<!-- Patient Info -->
<table class="table" cellpadding="0" cellspacing="0" style="font-size:12px !important;">
    <tbody>
        <tr>
            <td style="padding: 3px 0px !important;width:50%" class="text-left">
                <p style="font-weight:bold"><?=strtoupper(getPatientName($patientInfo->patient_id))?></p>
                <p  style="font-size: 12px !important">UMR : <?=$patientInfo->umr_no?></p>
                <p  style="font-size: 12px !important"><?=$patientInfo->age?><?=$patientInfo->age_unit?> <?=$patientInfo->gender?></p>
            </td>
            
            <td style="padding: 3px 0px !important;vertical-align:top;width:50%" class="text-right">
                <p style="font-weight:bold">Address</p>
                <p  style="font-size: 12px !important"><?=DataCrypt($patientInfo->mobile, 'decrypt')?></p>
                <p  style="font-size: 12px !important"><?=$patientInfo->location?></p>
            </td>
            
            <!-- <td style="padding: 3px 0px !important;vertical-align:top;width:35%" class="text-right">
                <p style="font-weight:bold"><?=strtoupper(getDoctorName($docInfo->doctor_id))?></p>
                <p  style="font-size: 12px !important"><?=$docInfo->qualification?> - <?=$docInfo->department_name?></p>
                <p  style="font-size: 12px !important">Reg. No:<?=$docInfo->registration_code?></p>
            </td> -->
        </tr>
    </tbody>
</table>

</htmlheader>

<hr>
    <?php
}
?>

    

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
$res_vital_sign = $this->db->query("SELECT a.vital_sign, a.vital_result, a.patient_id, a.vital_sign_recording_date_time,b.unit FROM `patient_vital_sign` a left join vital_sign b on(a.vital_sign = b.short_form)  WHERE a.vital_sign_recording_date_time =(select max(a.vital_sign_recording_date_time) FROM `patient_vital_sign` a where a.appointment_id='".$appointments->appointment_id."') and a.vital_result != '' and a.sign_type = 'generic' ORDER BY a.patient_vital_id ASC")->result();
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
                                <p><?php echo ucfirst($value->remarks); ?><br><?php if($plang != "en"){ ?> <?php echo ucfirst($remark_converted); } ?></p>
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
if($prescriptionsInfo->general_instructions != ''){
    ?>
    <!-- Clinical Diagnosis -->
    <p  style="font-size: 12px !important"><span style="font-weight:bold;float:right">General Instructions</span></p>
    <table cellpadding="0" cellspacing="0" style="font-size:12px !important;width:100%">
        <tr>
            <td class="text-left" style="padding: 5px 0px !important">
                <p  style="font-size: 12px !important;padding-top:2px !important"><?=$prescriptionsInfo->general_instructions?></p>
            </td>
        </tr>
    </table>

    <hr>
    <?php
}
?>

<?php 
if($prescriptionsInfo->plan != ''){
    ?>
    <!-- Clinical Diagnosis -->
    <p  style="font-size: 12px !important"><span style="font-weight:bold;float:right">Plan</span></p>
    <table cellpadding="0" cellspacing="0" style="font-size:12px !important;width:100%">
        <tr>
            <td class="text-left" style="padding: 5px 0px !important">
                <p  style="font-size: 12px !important;padding-top:2px !important"><?=$prescriptionsInfo->plan?></p>
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
            <p><span style="font-weight:bold">Follow Up Date : </span><?=($prescriptionsInfo->follow_up_date == "" || $prescriptionsInfo->follow_up_date == "0000-00-00") ?'NA':date('d M Y', strtotime($prescriptionsInfo->follow_up_date))?></p>
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
            <p style="font-weight: bold; line-height: 25px;">This is Tele Consultation Appointment</p>
        </td>
    </tr>
    <?php }?>
</table>

<?php   
		if($pdfSettings->footer==1)
		{
		    $style = "margin-bottom: ".$pdfSettings->footer_height."px !important;";
		}
		?>
			<!-- <htmlpagefooter name="footer">
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
			</htmlpagefooter>	 -->
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

<!-- </div> -->

</body>	
</html>

