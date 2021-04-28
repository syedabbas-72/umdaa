
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

<body>
  <?php if($pdf_settings->header != 1){  
    ?>
    <htmlpageheader name="firstpageheader">
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
    </htmlpageheader>
    <htmlpageheader name="otherheader">
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
    </htmlpageheader>
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
  <table cellpadding="0" cellspacing="0" style="margin: 0px;font-size: 12px; padding: 0px; width: 100%;">
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
      <td style="width:30%;text-align: right;vertical-align: top;font-size: 13px">
        <span style="font-weight: bold"><b><?php echo "DR. ".strtoupper($appointments->dfname." ".$appointments->dlname); ?></b></span><br><span><?php echo strtoupper($appointments->qualification. ", ". $appointments->department_name); ?> </span><br>
        <span><b>Reg. No:</b> &nbsp;<?php echo $appointments->registration_code; ?> </span>             
      </td>
    </tr>
    <tr><td colspan="3"><hr></td></tr>
    <tr>
      <td colspan="3">
        <b style="font-weight: bold; line-height: 25px;text-transform: uppercase; padding: 10px;">Drug Allergy: </b>
        <?php 
        if($appointments->allergy != '' || $appointments->allergy != null){
          echo ucwords($appointments->allergy);
        }else{
          echo "No allergy mentioned";
        }
        ?>
      </td>
    </tr>
  </table>
<hr>


			<div class="cls_007"><span class="cls_007"  style="font-size:12px;font-weight: bold">PRESCRIPTION(Rx)</span></div>
				<table class="table" cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px">
					<thead>
					<tr>
						<th  style="font-size: 12px;background: #f6f6f6;">#</th>
						<th  style="font-size: 12px;background: #f6f6f6;">Medicine</th>
						<th  style="font-size: 12px;background: #f6f6f6;">Frequency</th>
						<th  style="font-size: 12px;background: #f6f6f6;">Duration</th>
						<th  style="font-size: 12px;background: #f6f6f6;">Qty</th>
					</tr>
				</thead>
				<tbody>

	
				
				<?php 
            $i=1;

			
            foreach ($patient_prescription_drug as $value) { 
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
            if($value->day_schedule != "")
              $freq = "Day";

            if($value->preffered_intake == "AF"){
              $intake = "after food";
            }
            if($value->preffered_intake == "BF"){
              $intake = "before food";
            }
            if($appointments->preferred_language !="NULL" || $appointments->preferred_language !=""){
              if($appointments->preferred_language == "Telugu"){
                $plang = "te";
              }
              else if($appointments->preferred_language == "Hindi"){
                $plang = "hi";
              }
              else if($appointments->preferred_language == "Gujarati"){
                $plang = "gu";
              }
              else if($appointments->preferred_language == "Kannada"){
                $plang = "kn";
              }
              else if($appointments->preferred_language == "Malayalam"){
                $plang = "ml";
              }
              else if($appointments->preferred_language == "Marathi"){
                $plang = "mr";
              }
              else if($appointments->preferred_language == "Panjabi"){
                $plang = "pa";
              }
              else if($appointments->preferred_language == "Sindhi"){
                $plang = "sd";
              }
              else if($appointments->preferred_language == "Tamil"){
                $plang = "ta";
              }
              else if($appointments->preferred_language == "Urdu"){
                $plang = "ur";
              }
              else if($appointments->preferred_language == "Bengali"){
                $plang = "bn";
              }
              else{
                $plang = "en";
              }
            }
            else{
              $plang = "en";
            }
            $Intake_converted = translate($intake,"en",$plang);
            $remark_converted = translate(strtolower($value->remarks),"en",$plang);

            if($value->day_schedule !=""){
              $split_schedule = explode(",",$value->day_schedule);

              if(in_array("M", $split_schedule)){
                $M = "<span style='font-size:20px'>&#10004;</span>";
                $dayM = "<span>M</span>";

              }else{
                $M = "<span style='font-size:20px'>&#215;</span>";
                $dayM = "<span>M</span>";
              }

              if(in_array("A", $split_schedule)){
                $A = "<span style='font-size:20px'>&#10004;</span>";
                $dayA = "<span>A</span>";

              }else{
                $A = "<span style='font-size:20px'>&#215;</span>";
                $dayA = "<span>A</span>";
              }

              if(in_array("N", $split_schedule)){
                $N = "<span style='font-size:20px'>&#10004;</span>";
                $dayN = "<span>N</span>";

              }else{
                $N = "<span style='font-size:20px'>&#215;</span>";
                $dayN = "<span>N</span>";
              }
            }
            ?>
            <tr>
              <td><?php echo $i++; ?></td>
              <td>
                <span style="font-weight: bold;font-size: 12px;"><?php if($value->trade_name != NULL || $value->trade_name != '') { echo strtoupper($value->formulation." ".$value->trade_name); }else{ echo strtoupper($value->formulation." ".$value->medicine_name); } ?></span><br>
                <p style="font-size: 12px; color:rgb(84,84,84);"><?php echo $value->composition; ?></p>
              </td>
              <td>
                <?php if($value->day_schedule==""||$value->day_schedule==NULL){ ?>
                  <span><?php if($value->day_dosage =="stat" || $value->day_dosage =="sos" || $value->day_dosage =="HS"){ echo $value->day_dosage; } else { echo $value->day_dosage." times in a ".$value->dosage_frequency; } ?></span><br><span style="font-size: 12px;color:rgb(84,84,84);"><small><?php echo "(".$value->drug_dose." ".$value->dosage_unit." each )"; ?></small></span>
                <?php } else { ?>
                  <span><?php echo $M.'   -   '.$A.'   -   '.$N; ?></span><br><span><?php echo $dayM.'&nbsp;&nbsp;&nbsp;&nbsp; '.$dayA.'&nbsp;&nbsp;&nbsp;&nbsp; '.$dayN; ?></span><br><span><small><?php echo $value->drug_dose." ".$value->dosage_unit." each"; ?></small></span>
                <?php } ?>

              </td>
              <td><?php if($value->dose_course == 0 ){ echo "--"; } else { echo $value->dose_course." ".$freq."(s)"; } ?><br>
                <span style="font-weight: bold;text-transform: capitalize;font-size: 12px"><?=$intake?></span>
                <?php if($plang != "en"){ ?> <br><?php echo "(".ucfirst($Intake_converted).")"; } ?>
              </td>
              <td><?php if($value->quantity == 0 ){ echo "--"; } else { echo $value->quantity; } ?></td>
            </tr>
            <?php if($value->remarks !="") { ?>
              <tr>
                <td colspan="1" style="padding-top: 0"></td>
                <td colspan="4" style="padding-top: 0"><span style="font-weight:bold">Remarks: </span> <?php echo ucfirst($value->remarks); ?><?php if($plang != "en"){ ?> <br><?php echo "(".ucfirst($remark_converted).")"; } ?></td>
              </tr>
            <?php }?>
					
					<?php } ?>
					
				
				
	</tbody>

	</table>




	<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px;vertical-align: top">
	<tr> <td><div  style="font-size:12px;font-weight: bold;font-style: italic;">CAUTION : Take the medicine as prescribed by the doctor. Do not use in larger or smaller amounts than advised/prescribed. Use the medicine only for the prescribed purpose. Report any unusual symptoms to the doctor immediately. Do not stop the use of the medicine without consulting your doctor. Ensure that you complete the course as prescribed by your doctor.</div></td></tr></table>
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
   

</body>	
</html>