<div class="row mt-3">
    <div class="col-md-12">
        <table class="table customTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Medicine</th>
                    <th>Frequency</th>
                    <th>Duration</th>
                    <th>Qty</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                if(count($prescriptionLineItemsInfo)>0)
                {
                    foreach($prescriptionLineItemsInfo as $value)
                    {
                        $drugInfo = getDrugInfo($value->drug_id);
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
						$Intake_converted = translate($intake,"en",$plang);
						$remark_converted = translate(strtolower($value->remarks),"en",$plang);

						if($value->day_schedule !=""){
							$split_schedule = explode(",",$value->day_schedule);

							if(in_array("M", $split_schedule)){
								$M = "<span>&#10004;</span>";
								$dayM = "<span>M</span>";

							}else{
								$M = "<span>&#215;</span>";
								$dayM = "<span>M</span>";
							}

							if(in_array("A", $split_schedule)){
								$A = "<span>&#10004;</span>";
								$dayA = "<span>A</span>";

							}else{
								$A = "<span>&#215;</span>";
								$dayA = "<span>A</span>";
							}

							if(in_array("N", $split_schedule)){
								$N = "<span>&#10004;</span>";
								$dayN = "<span>N</span>";

							}else{
								$N = "<span>&#215;</span>";
								$dayN = "<span>N</span>";
							}
						}
                        ?>
                        <tr>
                            <td><?=$i?></td>
                            <td><label class="font-weight-bold text-uppercase"><?=$drugInfo->formulation." ".$drugInfo->trade_name?></label></td>
							<td class="text-center">
                                <?php 
                                if($value->day_schedule==""||$value->day_schedule==NULL){ 
                                    ?>
                                    <span>
                                    <?php if($value->day_dosage =="stat" || $value->day_dosage =="sos" || $value->day_dosage =="HS")
                                    { 
                                        echo $value->day_dosage; 
                                    } 
                                    else 
                                    { 
                                        echo $value->day_dosage." times in a ".$value->dosage_frequency; 
                                    } 
                                    ?>
                                    </span>
                                    <br>
                                    <label class="font-weight-bold"><?php echo "(".$value->drug_dose." ".$value->dosage_unit." each )"; ?></label>
                                <?php } 
                                else { ?>
									<span>
                                    <?php echo $M.' - '.$A.' - '.$N; ?></span><br><span><?php echo $dayM.'&nbsp;&nbsp; '.$dayA.'&nbsp;&nbsp; '.$dayN; ?></span>
                                    <br><span><small><?php echo $value->drug_dose." ".$value->dosage_unit." each"; ?></small></span>
								<?php } ?>

							</td>
							<td><?php if($value->dose_course == 0 ){ echo "--"; } else { echo $value->dose_course." ".$freq."(s)"; } ?><br>
								<span style="font-weight: bold;text-transform: capitalize;font-size: 12px"><?=$intake?></span>
								<?php if($plang != "en"){ ?> <br><?php echo "(".ucfirst($Intake_converted).")"; } ?>
							</td>
                            <td><?php if($value->quantity == 0 ){ echo "--"; } else { echo $value->quantity; } ?></td>
                            <td><label class="font-weight-bold"><?php if($plang != "en"){ echo ucfirst($remark_converted); } else { echo ucwords($value->remarks); } ?></label></td>
                        </tr>
                        <?php
                        $i++;
                    }
                }
                else
                {
                    ?>
                    <tr>
                        <td colspan="6 text-center">
                            <p class="font-weight-bold text-center mb-0">No Prescriptions Found.</p>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <div class="row">
            <div class="col-md-12">
                <h6 class="font-weight-bold font-italic text-justify" style="line-height:normal">
            CAUTION : Take the medicine as prescribed by the doctor. Do not use in larger or smaller amounts than advised/prescribed. Use the medicine only for the prescribed purpose. Report any unusual symptoms to the doctor immediately. Do not stop the use of the medicine without consulting your doctor. Ensure that you complete the course as prescribed by your doctor.
            </h6>
            </div>
        </div>
    </div>
</div>

<!-- Footer Info -->
<div class="row mt-5">
    <div class="col-md-6">
        <label class="font-weight-bold">POWERED BY UMDAA</label>
    </div>
    <div class="col-md-6 text-right">
        <label class="font-weight-bold"><?=date("d-m-Y h:i A", strtotime($prescriptionInfo->created_date_time))?></label>
    </div>
</div>