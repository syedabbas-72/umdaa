   <div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><?php echo $_SESSION['clinic_name']; ?></a></li>
            <li class="breadcrumb-item active">TODAY's APPOINTMENTS</li>
          </ol>
        </div>
        
    </div>
	<section class="main-content">
		<div class="row">
			<?php 
			$a=0;
			foreach($appointment as $app){
				$a_id=$app->appointment_id;

				if($app->photo != NULL)
				{
					$photo='uploads/patients/'.$app->photo;
				}else{
					$photo='assets/img/patient_avatar.png';
				}
				
				if($app->appointment_type=='New')
				{
					$at='N';
				}else{
					$at='F';
				}
				
				if($app->ap_status=='booked'){
					$status='Booked';
					$btn="primary";
				}else if($app->ap_status=='checked_in'){
					$status="Checked In";
					$btn="success";
				}else if($app->ap_status=='in_consultation'){
					$status="In Consultation";
					$btn="success";
				}else if($app->ap_status=='reschedule'){
					$status="Reschedule";
					$btn="warning";
				}else if($app->ap_status=='absent'){
					$status="Absent";
					$btn="danger";
				}else if($app->ap_status=='vital_signs'){
					$status="Vital Sign";
					$btn="success";
				}else if($app->ap_status=='closed'){
					$status="Closed";
					$btn="danger";
				}else if($app->ap_status=='drop'){
					$status="Canceled";
					$btn="danger";
				}
				else if($app->ap_status=='waiting'){
					$status="Waiting";
					$btn="info";
				}
				
				if($app->ap_status !='booked'){
					if($app->check_in_time!=NULL){
						$day = date('M d, Y H:i:s',strtotime($app->check_in_time));
			?>
			
			<script type="text/javascript">
				console.log("<?php echo $day;?>");
			var countDownDate<?php echo $a_id; ?> = new Date("<?php echo $day;?>").getTime();
			var x<?php echo $a_id; ?> = setInterval(function() {
				var now<?php echo $a_id; ?> = new Date().getTime();

				var distance =  now<?php echo $a_id; ?> - countDownDate<?php echo $a_id; ?>;

				var days<?php echo $a_id; ?> = Math.floor(distance / (1000 * 60 * 60 * 24));
				var hours<?php echo $a_id;?> = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
				var minutes<?php echo $a_id; ?> = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
				var seconds<?php echo $a_id; ?> = Math.floor((distance % (1000 * 60)) / 1000);

				document.getElementById("app<?php echo $a_id; ?>").innerHTML = hours<?php echo $a_id; ?> + "<span class='small'>H</span> "
				+ minutes<?php echo $a_id; ?> + "<span class='small'>M</span> " + seconds<?php echo $a_id; ?> + "<span class='small'>S</span>";
					if (distance < 0) {
						clearInterval(x<?php echo $a_id; ?>);
						document.getElementById("app<?php echo $a_id; ?>").innerHTML = "Waiting";
					}
				}, 1000);
			</script>
				<?php 
			}
		}
		$colorcode=$app->color_code;

		$moreInfo = "";
		$i = 0;
		
		if($app->gender) {
			$moreInfo = substr($app->gender,0,1);	
			$i = 1;
		}

		if($app->age){
			if($i){
				$moreInfo .= ', ';
			}
			$moreInfo .= $app->age;	
			if($app->age_unit) {
				$moreInfo .= substr($app->age_unit,0,1);			
			}
		}
		?>

		<div class="col-md-3 col-xs-12 patientDock">
			<div class="card">
				<div class="profilePic text-center">
					<img alt="profile" class="rounded-circle margin-b-10" src="<?php echo base_url($photo);?>" style="border:3px solid <?php echo $colorcode; ?>;">
					<div class="appType <?php echo $at; ?>"><?php echo $at;?></div>
					<?php 
					if($app->priority) {
						if($app->priority != 'none') {
							echo '<p class="priority arrow-up"></p>';
							echo '<p class="P">P</p>';
						}
						?>
					<?php } ?>
				</div>
				<div class="patientInfo">
					<p class="lead text-center"><?php echo ucwords($app->title).". ".strtoupper($app->pfname." ".$app->pmname." ".$app->plname); ?></p>
					<p class="text-muted text-center">
						<?php echo $app->umr_no; ?> 
						<?php if($moreInfo != '') { ?>
							<span class="moreInfo">(<?php echo $moreInfo; ?>)</span>					
						<?php } ?>						
					</p>
				</div>
				<div class="bdrBtm btmGap"></div>
				<div class="docInfo">
					<!-- <span class="baseLine">Appointment with</span> -->
					<p class="lead"><?php echo ucwords($app->salutation).". ".strtoupper($app->dfname." ".$app->dlname); ?></p>
					<!-- <p class="text-muted"><?php echo $app->department_name; ?></p> -->
					<p class="text-muted">On <span class="moreInfo"><?php echo date('d M',strtotime($app->appointment_date)); ?></span>. @ <span class="moreInfo"><?php echo date('h:i A',strtotime($app->appointment_time_slot)); ?></span></p>
				</div>
				<div class="topGap bdrBtm btmGap"></div>
				<div class="ticking">
					<?php
				   	if($app->ap_status != 'booked'){
						if($app->check_in_time!=NULL){
							?>
							<i class="far fa-clock" style="color: <?php echo $colorcode; ?>"></i> <span id="app<?php echo $a_id; ?>" class="lead"></span>
							<?php
						}
					}else{
						?>
							<i class="far fa-clock" style="color: <?php echo $colorcode; ?>"></i> <span class="lead">00<span class='small'>H</span> 00<span class='small'>M</span> 00<span class='small'>S</span> </span>
						<?php
					} 
					?>
				</div>

				<?php
				$CI = &get_instance();

				if(strtolower($status) == 'booked'){
				?>
					<div class="actionBtn checkin">
						<a href="#" class="btn btn-<?php echo $btn; ?> margin-r-5" data-toggle="modal" data-target="#loginModal<?php echo $a_id; ?>" >CHECK IN</a>
						<?php if($app->invoice_pdf!=''&&$app->invoice_pdf!=NULL){?>
						<a href="<?php echo base_url('uploads/billings/').$app->invoice_pdf; ?>" target="_blank"><i style="font-size:20px;position: relative;top: 7px;" class="fas fa-print"></i></a>
						<?php } ?>
					</div>
					<?php 
				}else{
					$appinfo=$CI->db->query("select patient_id,appointment_id  from appointments where appointment_id='".$app->appointment_id."'")->row();
			   		?>
			   		<div class="actionBtn">
						<a href="<?php echo base_url('CaseSheet/patient_info/'.$appinfo->patient_id.'/'.$appinfo->appointment_id)?>" class="btn btn-<?php echo $btn; ?> margin-r-5"><?php echo $status; ?></a>
						<?php if($app->invoice_pdf!=''&&$app->invoice_pdf!=NULL){?>
						<a href="<?php echo base_url('uploads/billings/').$app->invoice_pdf; ?>" target="_blank"><i style="font-size:20px;position: relative;top: 7px;" class="fas fa-print"></i></a>
						<?php } ?>
					</div>
				   	<?php 
			    }
				?>
			</div>
		</div>
		<?php $a++;} ?>
    </div>

    </section>
	<?php foreach($appointment as $app1){
		
		if($app1->ap_status=='booked'){
			$status='Booked';
			$btn="primary";
		}else if($app1->ap_status=='checked_in'){
			$status="Checked In";
			$btn="success";
		}else if($app1->ap_status=='in_consultation'){
			$status="In Consultation";
			$btn="success";
		}else if($app1->ap_status=='reschedule'){
			$status="Reschedule";
			$btn="warning";
		}else if($app1->ap_status=='absent'){
			$status="Absent";
			$btn="danger";
		}else if($app1->ap_status=='vital_signs'){
			$status="Vital Sign";
			$btn="success";
		}else if($app1->ap_status=='closed'){
			$status="Closed";
			$btn="danger";
		}else if($app1->ap_status=='drop'){
			$status="Drop";
			$btn="danger";
		}		
		?>

		<div class="modal fade" id="loginModal<?php echo $app1->appointment_id; ?>">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="fa fa-times"></span></button>
					</div>
					<div class="modal-body">
				  		<div class="col-md-12 col-sm-12 text-center " style="margin-bottom: 20px">
                            <p class="lead margin-b-0" style="color: green;font-size: 16px"><b><?php echo $app1->title." ".$app1->pfname." ".$app1->plname; ?> is having an appointment with <?php echo "DR." .strtoupper($app1->dfname." ".$app1->dlname); ?></b></p>
                            <p class="lead margin-b-0" style="color: green;font-size: 16px"><b>On Date  <?php echo date("d F Y",strtotime($app1->appointment_date))." @ ".date("h:i A",strtotime($app1->appointment_time_slot)); ?></b></p>
                            <p class="lead margin-b-0" style="color: green"></p>
                        </div>
                        <div class="col-md-12 col-sm-12 text-center">
                            <form role="form" method="post" action="<?php echo base_url('appointment/appointment_status');?>">
                                <?php
								if($app1->ap_status=='booked'){
								
								$info=$CI->db->query("select patient_id,payment_status  from appointments where appointment_id='".$app1->appointment_id."'")->row();
								$reg_info = $CI->db->query("select payment_status  from patients where patient_id='".$info->patient_id."'")->row();
								?>

								<?php if($reg_info->payment_status == 0 && $info->payment_status == 0) { ?>
									<a class="btn  btn-info "  href='<?php echo base_url('patients/confirm_payment/'.$app1->appointment_id);?>'>Go to Payment </a>
									<a class="btn  btn-primary " id="res_div">Reschedule</a>
									<a class="btn  btn-danger " href='<?php echo base_url('appointment/appointment_status/drop/'.$app1->appointment_id);?>'>Drop</a>
								<?php } 
								else  if($reg_info->payment_status == 2 || $info->payment_status == 2) { ?>
									<a class="btn  btn-success "  href='<?php echo base_url('appointment/appointment_status/checked_in/'.$app1->appointment_id);?>'>Check in </a>
									<a class="btn  btn-primary " id="res_div">Reschedule</a>									
								<?php }	
								else { ?>
									<a class="btn  btn-success "  href='<?php echo base_url('appointment/appointment_status/checked_in/'.$app1->appointment_id);?>'>Check in </a>
									<a class="btn  btn-primary " id="res_div">Reschedule</a>									
								<?php }	?>
									
								<?php }else if($app1->ap_status=='checked_in'){ ?>
									<!--<button type="button" class="btn  btn-primary ">Login</button>
									<button type="button" class="btn  btn-primary ">Login</button>-->
								<?php }else if($app1->ap_status=='in_consultation'){ ?>
									<!--<button type="button" class="btn  btn-primary ">Login</button>
									<button type="button" class="btn  btn-primary ">Login</button>-->
								<?php }else if($app1->ap_status=='reschedule'){ ?>
									<!--<button type="button" class="btn  btn-primary ">Login</button>
									<button type="button" class="btn  btn-primary ">Login</button>-->
								<?php }else if($app1->ap_status=='absent'){ ?>
									<!--<button type="button" class="btn  btn-primary ">Login</button>
									<button type="button" class="btn  btn-primary ">Login</button>-->
								<?php }else if($app1->ap_status=='vital_signs'){ ?>
									<!--<button type="button" class="btn  btn-primary ">Login</button>
									<button type="button" class="btn  btn-primary ">Login</button>
									<button type="button" class="btn  btn-primary ">Login</button>-->
								<?php }else if($app1->ap_status=='closed'){ ?>
									<!--<button type="button" class="btn  btn-primary ">Login</button>-->
								<?php }else if($app1->ap_status=='drop'){ ?>
									<!--<button type="button" class="btn  btn-primary ">Login</button>-->
								<?php }?>
                            </form>                           
                        </div>

                        <div class="col-md-12 col-sm-12 text-center reschedule_div" style="display:none;padding: 20px">
                            <?php echo form_open(site_url("appointment/reschedule"), array("class" => "form-inline","id"=>"app_form")) ?>
                            <div class="form-group">
                                <label for="input-date" class="sr-only">Date</label>
                                <input class="solo" id="solo_date" type="text" size=15 name="date" placeholder="DD / MM / YYYY" />
                            </div>
                            <input type="hidden" name="appointment_id" value="<?php echo $app1->appointment_id; ?>">
                            <input type="hidden" name="clinic_id" value="<?php echo $app1->clinic_id; ?>">
                            <input type="hidden" name="patient_id" value="<?php echo $app1->patient_id; ?>">
                            <input type="hidden" name="umr_no" value="<?php echo $app1->umr_no; ?>">
                            <input type="hidden" name="doctor_id" value="<?php echo $app1->doctor_id; ?>">
                            <button  did = "<?php echo $app1->doctor_id; ?>" id="check_btn" class="btn btn-warning margin-l-5 mx-sm-3">Check</button>
                            <div class="form-group mx-sm-3">
                                <label for="input-password" class="sr-only"></label>
                                <select class="form-control" id="slots" name="slots"></select>
                            </div>
								
							<div class="col-md-12 col-sm-12 text-center" style="padding: 20px">
								<input class="btn btn-success" type="submit" value="Reschedule" name="submit">
							</div>                                
                        </form>
                    </div>
				</div>
			</div>
		</div>
	</div>
<?php } ?>

<script type="text/javascript">

	$(document).ready(function () {
		$('#doctorlist').dataTable();
	});

  	function doconfirm(){
        if(confirm("Delete selected messages ?")){
            return true;
        }else{
            return false;  
        } 
   	}

	$(document).on("click","#res_div",function(){
		$(".reschedule_div").show();
		
	});

	$(document).on("click","#check_btn",function(){
		var sel_date = $("#solo_date").val();
		//var sel_date = document.getElementById("solo_date").value;
		var doctor_id = $(this).attr("did");
		//$("#solo_date").val('test');
		console.log(sel_date);
		checkslots(doctor_id,sel_date);
		return false;

	});

	function checkslots(d_id,date){

		var current_time = '<?php echo date('H:i'); ?>';
  
   		$.ajax({
		    type: "POST",
		    url: '<?php echo base_url(); ?>calendar_view/check_slot',
		    data:{ did:d_id,date:date},
		    success: function(result){
				result = $.trim(result);
				//console.log(result);
				if(result == "no"){
					var slot_html = "<option value=''>NA</option>";
					$("#slots").html(result);
				}else{
					var slot_html = result;
				}
				$("#slots").html(slot_html);
			}                    
     	});
	}

	var soloInput = $('input.solo');

	soloInput.on('keyup', function(){
		var v = $(this).val();
		if (v.match(/^\d{2}$/) !== null) {
			$(this).val(v + '/');
		} else if (v.match(/^\d{2}\/\d{2}$/) !== null) {
			$(this).val(v + '/');
		}  
	});

</script>