
		<!-- Header Code Starts -->
		<div class="row mt-3 border-bottom">
			<div class="col-md-6 col-12">
				<img src="<?=base_url('uploads/clinic_logos/'.$clinicInfo->clinic_logo)?>" class="w-100 d-none d-md-block d-lg-none"  alt="">
				<img src="<?=base_url('uploads/clinic_logos/'.$clinicInfo->clinic_logo)?>" class="w-50 d-none d-md-none d-lg-block"  alt="">
				<img src="<?=base_url('uploads/clinic_logos/'.$clinicInfo->clinic_logo)?>" class="w-75 d-none d-sm-block d-md-none"  alt="">
				<img src="<?=base_url('uploads/clinic_logos/'.$clinicInfo->clinic_logo)?>" class="w-100 d-block  d-sm-none"  alt="">
			</div>
			<div class="col-md-6 col-12 text-right">
				<label class="font-weight-bold">ADDRESS</label>
				<p class="text-right p-0">
					<?=$clinicInfo->address?><br>
					<?=$clinicInfo->location." - ".$clinicInfo->pincode?><br>
					<span class="font-weight-bold">Phone :</span> <?=$clinicInfo->clinic_phone?> 	
				</p>
			</div>
		</div>
		<!-- Header Code Ends -->

		<!-- Info Header Starts -->
		<div class="row mt-3 border-bottom">
			<!-- Patient Details -->
			<div class="col-md-6 mb-2">
				<?php
				if($patientInfo->patient_id == 0)
				{
					?>
					<label for="" class="font-weight-bold text-uppercase">Name : <?=$billingInfo->guest_name?></label>
					<br>
					<label for="" class="font-weight-bold text-uppercase">Age : <?=$billingInvoiceInfo->age?>Y</label>
					<br>
					<label for="" class="font-weight-bold text-uppercase">Mobile : <?=DataCrypt($billingInfo->guest_mobile,'decrypt')?></label>
					<?php
				}
				else
				{
					?>
					<label for="" class="font-weight-bold text-uppercase"><?=$patientInfo->title.". ".$patientInfo->first_name." ".$patientInfo->last_name?></label>
					<br>
					<p class="p-0 mb-0"><span class="font-weight-bold">Gender :</span><?=$patientInfo->gender?>, 
						<span class="font-weight-bold">Age :</span> <?=$patientInfo->age?></p>
					<p class="p-0 mb-0"><span class="font-weight-bold">Address : </span><?=$patientInfo->location?></p>
					<label class="font-weight-bold">UMR NO : <?=$patientInfo->umr_no?></label>
					<?php
				}
				?>
				
			</div>
			<!-- Doctor Details -->
			<?php
			if(isset($docInfo))
			{
				?>
				<div class="col-md-6 text-right mb-2">
					<label for="" class="font-weight-bold text-uppercase">Dr. <?=$docInfo->first_name." ".$docInfo->last_name?></label>
					<br>
					<p class="p-0 mb-0 text-right text-uppercase">
					<span><?=$docInfo->qualification?><br><?=$docInfo->department_name?></p>
					<label><span class="font-weight-bold">REG NO : <?=$docInfo->registration_code?></span></label>
				</div>
				<?php
			}
			?>
			
		</div>
		<!-- Info Header Ends -->
