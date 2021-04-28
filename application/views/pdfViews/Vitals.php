<div class="row mt-3">
    <div class="col-md-12">
        <label class="font-weight-bold">VITALS</label>
    </div>
</div>
<div class="row mt-1">
    <div class="col-md-12">
    <table class="table customTable mb-0">
			<?php 
			$res_vital_sign = $this->db->query("SELECT a.vital_sign, a.vital_result, a.patient_id, a.vital_sign_recording_date_time,b.unit FROM `patient_vital_sign` a left join vital_sign b on(a.vital_sign = b.short_form)  WHERE a.vital_sign_recording_date_time =(select max(a.vital_sign_recording_date_time) FROM `patient_vital_sign` a where a.patient_id = '".$appInfo->patient_id."' and a.clinic_id = '".$appInfo->clinic_id."') and a.vital_result != '' and a.sign_type = 'generic' ORDER BY a.patient_vital_id ASC")->result();
			if(sizeof($res_vital_sign) == 0)
			{
				?>
				<tr >
					<td>&nbsp;</td>
				</tr>
				<?php
			}
			else
			{
				?>
				<tr>
					<?php 
						$SBP = 0;
						$DBP = 0;
						$BP = '';
						$i = 2;
						$vital_sign_results = array();

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
							<td>
								<label class='font-weight-bold'><?=$vital_sign_results[$j]['vital_sign']?> : </label> <?=$vital_sign_results[$j]['value'].$vital_sign_results[$j]['unit']?>
							</td><?php
							$x++;
						}

				?>
			</tr>
			<?php
			}
			?>		
		</table>

		<?php if($patientInfo->allergy) { ?>
		<table class="table customTable mt-0 mb-0">
			<tr>
				<td >
					<span style="font-weight: bold;">Drug Allergy </span> : <?php echo ucfirst($patientInfo->allergy); ?> </span>
				</td>
			</tr>
		</table>
		<?php } ?>
		<table class="table customTable mt-0">
			<tr>
				<td>
					<?php
					$reviewDays = $this->db->query("SELECT review_days FROM clinic_doctor WHERE clinic_id = '".$appInfo->clinic_id."' AND doctor_id = '".$appInfo->doctor_id."'")->row();

					$check = $this->db->query("select * from appointments a inner join doctors d on(a.doctor_id= d.doctor_id) where a.patient_id='" . $appInfo->patient_id . "' and a.doctor_id='" . $appInfo->doctor_id . "' and a.parent_appointment_id=0 and a.appointment_date <= '".date('Y-m-d')."' and (a.status !='booked' or a.status !='drop') order by a.appointment_date  desc")->row();

					$get_review_days = $this->db->query("select * from clinic_doctor  where clinic_id='" . $appIndo->clinic_id . "' and doctor_id ='".$doctor_info->doctor_id."'")->row();

					$get_review_times = $get_review_days->review_times;

					$review_check_date = date('Y-m-d', strtotime($check->appointment_date. ' + '.$get_review_days->review_days.' days'));

					$check_review_count = $this->db->query("select * from appointments where  patient_id='" . $appInfo->patient_id . "' and doctor_id='" . $appInfo->doctor_id . "' and appointment_date >'".$check->appointment_date."' and appointment_date <='".$review_check_date."' and status !='booked'")->num_rows();
					echo $check;

					if($check){


						if($appInfo->appointment_date > $review_check_date){
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
                    ?>
                    <label class="font-weight-bold">
                    <?php
					echo "Valid Till ".$review_check_date.". ".$status;
					?>							</label>
				</td>
				<td class="text-right">
					<span>Referred By : 
						<?php
						if($appointments->referred_by_type=="Doctor")
							echo getreferalDoctorname($patientInfo->referred_by);
						else
							echo $patientInfo->referred_by;
						?>
					</span>
				</td>
			</tr>
		</table>
    </div>
</div>
    

<!-- Footer Info -->
<div class="row mt-5">
    <div class="col-md-6">
        <label class="font-weight-bold">POWERED BY UMDAA</label>
    </div>
    <div class="col-md-6 text-right">
        <label class="font-weight-bold"><?=date("d-m-Y h:i A", strtotime($appInfo->created_date_time))?></label>
    </div>
</div>