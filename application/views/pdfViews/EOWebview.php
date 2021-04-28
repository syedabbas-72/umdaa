    
    <?php
        $parentDocInfo = doctorDetails($expInfo->parent_doctor_id);
        $referredDocInfo = doctorDetails($expInfo->referred_doctor_id);
        $patientInfo = getPatientDetails($presInfo[0]->patient_id);
    ?>
    <!-- Expert OPinion Header -->
    <div class="row">
        <div class="col-md-12">
            <h2 class="text-center">Expert Opinion</h2>
        </div>
    </div>
    <hr class="my-2">
    <div class="row">
        <div class="col-md-12">
            <label class="font-weight-bold mb-2">Patient Details</label>
            <br>
            <label class="font-weight-bold text-uppercase"><?=($patientInfo->title=="")?'':$patientInfo->title.". "?><?=$patientInfo->first_name." ".$patientInfo->last_name?></label><br>
            <label><?=$patientInfo->umr_no?></label><br>
            <label class="font-weight-bold"><?=DataCrypt($patientInfo->mobile,'decrypt')?></label><br>



        </div>
    </div>
    <hr class="my-2">
    <div class="row">
        <div class="col-md-6">
            <label>To</label><br>
            <label for="" class="font-weight-bold text-uppercase">Dr. <?=$parentDocInfo->first_name." ".$parentDocInfo->last_name?></label>
            <br>
            <p class="p-0 mb-0 text-uppercase">
            <span><?=$parentDocInfo->qualification?><br><?=$parentDocInfo->department_name?></p>
            <label><span class="font-weight-bold">REG NO : <?=$parentDocInfo->registration_code?></span></label>
        </div>
        <div class="col-md-6 text-right">
            <label>From</label><br>
            <label for="" class="font-weight-bold text-uppercase">Dr. <?=$referredDocInfo->first_name." ".$referredDocInfo->last_name?></label>
            <br>
            <p class="p-0 mb-0 text-uppercase text-right">
            <span><?=$referredDocInfo->qualification?><br><?=$referredDocInfo->department_name?></p>
            <label><span class="font-weight-bold">REG NO : <?=$referredDocInfo->registration_code?></span></label>
        </div> 
    </div>
    <hr class="my-2">
    <div class="row">
        <div class="col-md-12">
            <h5 class="p-0 font-weight-bold font-italic">Disclaimer</h5>
            <p class="p-0 font-italic">
            Here I'm writing Final Impression to the requested Patient after Observing his Summary Reports. Please check once with the patient before writing this F.I. I'm not liable for any cause. I'm just suggesting the F.I upto my knowledge. You have to check once while writing.
            </p>
        </div>
    </div>
    <hr class="my-2">
	<?php if(count($cdInfo)>0){ ?>
		<div class="row">
            <div class="col-md-12">
                <label class="font-weight-bold">Clinical Diagnosis</label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
            <?php 
            $cd=0;
            $c=1;
            foreach ($cdInfo as $key => $value) {
				$cd_code = "(".$value->code.")";
				?>
                    <span class="font-weight-bold"><?php echo $c++ . ". " .ucwords($value->disease_name); ?>  <?=($value->code!='')?$cd_code:''?></span><br>
                <?php 
                $cd++;
                }
                ?>
                
            </div>
        </div>
                <?php
        }?>
        
        <hr class="my-2">

        <?php if(count($invInfo)>0){ ?>
            <div class="row">
                <div class="col-md-12">
                    <label class="font-weight-bold">Investigations</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                <?php 
                $i=1;
                foreach ($invInfo as $key => $value) {
                    $inv_id = "(".$value->investigation_id.")";
                    ?>
                        <span class="font-weight-bold"><?php echo $i++ .". ".ucwords($value->investigation_name); ?> <?=($value->investigation_id!=0)?$inv_id:''?></span><br>
                    <?php 
                    $cd++;
                    }
                    ?>
                    
                </div>
            </div>
                    <?php
            }?>
        <hr class="my-2">
		<?php if(count($presInfo)>0) { ?>
			<div class="cls_007"><span class="cls_007" style="font-weight: bold; font-size:14px; line-height: 25px;">PRESCRIPTION(Rx)</span></div>
			<table class="table customTable" >
				<thead>
					<tr>						
						<th style="font-size: 12px;background: #f2f2f2; border-bottom: 1px solid #ccc; width: 10%">S#</th>
						<th style="font-size: 12px;background: #f2f2f2; border-bottom: 1px solid #ccc; width: 40%">Medicine</th>
						<th style="font-size: 12px;background: #f2f2f2; border-bottom: 1px solid #ccc; width: 20%">Frequency</th>
						<th style="font-size: 12px;background: #f2f2f2; border-bottom: 1px solid #ccc; width: 15%">Duration</th>
						<th style="font-size: 12px;background: #f2f2f2; border-bottom: 1px solid #ccc; width: 15%">Qty</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i=1;
					$patient_prescription_drug=$this->db->query("select pd.drug_id, pd.day_schedule, pd.preffered_intake,pd.day_dosage,pd.drug_dose,pd.dosage_unit,pd.dosage_frequency, pd.dose_course, pd.quantity,pd.medicine_name,d.formulation,d.trade_name, d.composition,pd.remarks from patient_prescription_drug pd left join drug d on (pd.drug_id=d.drug_id) where pd.patient_prescription_id='" . $presInfo[0]->patient_prescription_id . "' ")->result();

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
								<span style="font-weight: bold; font-size: 12px;"><?php if($value->trade_name != NULL || $value->trade_name != '') { echo strtoupper($value->formulation." ".$value->trade_name); }else{ echo strtoupper($value->formulation." ".$value->medicine_name); } ?></span><br>
								<p style="font-size: 13px; color:rgb(84,84,84);" class="p-0"><?php echo $value->composition; ?></p>
							</td>
							<td>
								<?php if($value->day_schedule==""||$value->day_schedule==NULL){ ?>
									<span><?php if($value->day_dosage =="stat" || $value->day_dosage =="sos" || $value->day_dosage =="HS"){ echo $value->day_dosage; } else { echo $value->day_dosage." times in a ".$value->dosage_frequency; } ?></span><br><span style="font-size: 13px;color:rgb(84,84,84);"><small><?php echo "(".$value->drug_dose." ".$value->dosage_unit." each )"; ?></small></span>
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
						<?php } ?>
					<?php } ?>
				</tbody>
			</table>
			<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px;vertical-align: top">
				<tr> <td><div  style="font-size:13px;font-weight: bold;font-style: italic;">CAUTION : Take the medicine as prescribed by the doctor. Do not use in larger or smaller amounts than advised/prescribed. Use the medicine only for the prescribed purpose. Report any unusual symptoms to the doctor immediately. Do not stop the use of the medicine without consulting your doctor. Ensure that you complete the course as prescribed by your doctor.</div></td></tr>
			</table>
		<?php } ?>
		<table cellspacing="0" cellpadding="0" style="width: 100%; padding: 15px 10px;vertical-align: top">
			<?php if($patient_prescription->general_instructions != ''){ ?>
				<tr>
					<td colspan="5" style="vertical-align: top; padding:10px 0px"><span style="font-weight: bold; line-height: 25px; font-size:14px;">General Instructions: </span>
					<p style="font-size: 13px; padding:10px 0px;"><?=$patient_prescription->general_instructions?></p>
					</td>
				</tr>
			<?php } ?>
			<?php if($patient_prescription->plan != ''){ ?>
				<tr>
					<td colspan="5" style="vertical-align: top; padding:10px 0px"><span style="font-weight: bold; line-height: 25px;">Plan: </span>
						<p><?=$patient_prescription->plan?></p>
					</td>
				</tr>
			<?php } ?>
		</table>
		<hr class="my-2">
    

<!-- Footer Info -->
<div class="row mt-5">
    <div class="col-md-6">
        <label class="font-weight-bold">POWERED BY UMDAA</label>
    </div>
    <div class="col-md-6 text-right">
        <label class="font-weight-bold"><?=date("d-m-Y h:i A", strtotime($expInfo->created_date_time))?></label>
    </div>
</div>