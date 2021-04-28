<style type="text/css">
	.bg-light-gradient {
    background: #f8f9fa;
    background: -o-linear-gradient(white, #f8f9fa);
    color: #1F2D3D;
}
.page-footer{
    display: none;
}
.booking-time {
    font-weight: 500;
    font-size: 1rem;
}
.booking-time .badge {
    font-size: 90%;
}
.ml-0, .mx-0 {
    margin-left: 0 !important;
}
.mr-0, .mx-0 {
    margin-right: 0 !important;
}
.rounded {
    border-radius: 0.25rem !important;
}
.mb-3, .small-box, .card, .info-box, .callout, .my-3 {
    margin-bottom: 1rem !important;
}
table.dataTable {
    clear: both;
    margin-top: 6px !important;
    margin-bottom: 6px !important;
    max-width: none !important;
    border-collapse: separate !important;
    border-spacing: 0;
}
.img-size-100 {
    width: 100px;
}
button.view-booking-detail {
	box-shadow: none !important;
	overflow: visible !important;
}
.bg-secondary, .bg-secondary a {
    color: #ffffff !important;
}
.btn-outline-primary {
    color: #007bff;
    background-color: transparent;
    background-image: none;
    border-color: #007bff;
}
.btn-outline-dark {
    color: #343a40;
    background-color: transparent;
    background-image: none;
    border-color: #343a40;
}
.btn-outline-danger {
    color: #dc3545;
    background-color: transparent;
    background-image: none;
    border-color: #dc3545;
}
.border-dark {
    border-color: #ffffff !important;
}
.badge{
	background: none !important;
}
.bg-light, .bg-light a {
    color: #1F2D3D !important;
}
.view-booking-detail {
    font-size: 1.8rem;
}
</style>
<div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?php echo $_SESSION['clinic_name']; ?></div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                              
                                <li class="active">Appointments</li>
                            </ol>
                        </div>
                    </div>


         
            <div class="card">
                <div class="card-body">
<div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">

            

                                    <div id="myTable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer"><div class="row"><div class="col-sm-12"><table id="myTable" class="table table-borderless w-100 dataTable no-footer dtr-inline" role="grid" aria-describedby="myTable_info" style="width: 0px;"><thead class="hide">
                                        <tr role="row"><th class="sorting_disabled" rowspan="1" colspan="1" style="width: 0px;">#</th></tr>
                                        </thead>
                                        
                                    <tbody><tr role="row" class="odd">
                                        <div class="row col-md-12 mb-3 mr-0 ml-0  rounded" style="padding: 0">

                                        <?php 
            $a=0;
            $i=1;
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
                    $btn="warning";
                }else if($app->ap_status=='reschedule'){
                    $status="Reschedule";
                    $btn="warning";
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
                
            var countDownDate<?php echo $a_id; ?> = new Date("<?php echo $day;?>").getTime();
            var x<?php echo $a_id; ?> = setInterval(function() {
                var now<?php echo $a_id; ?> = new Date().getTime();

                var distance =  now<?php echo $a_id; ?> - countDownDate<?php echo $a_id; ?>;

                var days<?php echo $a_id; ?> = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours<?php echo $a_id;?> = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes<?php echo $a_id; ?> = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds<?php echo $a_id; ?> = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById("app<?php echo $a_id; ?>").innerHTML = '<span  style="color: #F84982;">'+hours<?php echo $a_id; ?> + '</span><span class="small font-weight-bold">H</span><span  style="color: #F84982;"> '+ minutes<?php echo $a_id; ?> + '</span><span class="small font-weight-bold">M</span> <span  style="color: #F84982;">' + seconds<?php echo $a_id; ?> + '</span><span class="small font-weight-bold">S</span>';
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
 
   <div class="row col-md-6" style="margin-right:10px;margin-bottom: 10px">
    <div class="col-md-2 bg-<?php echo $btn; ?> text-center booking-time booking-div rounded-left d-flex align-items-center justify-content-center">
        <div>
            <div style="font-weight: 600;font-size: 25px;background: white;width:40px;height:40px;margin:0 auto;border-radius: 50%;color: #000000ab;"><?php echo $at; ?></div>
            <h5 style="font-weight: 600"><?php echo date("d F",strtotime($app->appointment_date)); ?></h5>
            <span class="badge border  border-dark  font-weight-bold"><?php echo date("h:i A",strtotime($app->appointment_time_slot)); ?></span><br>
            <!-- <small class="text-uppercase"><?php echo $at; ?></small> -->
        </div>
    </div>
    <div class="col-md-9 bg-light-gradient booking-div p-2 text-uppercase">
        <div class="row mb-2">
            <div class="col-md-6 text-uppercase">
        <h4 class="font-weight-bold" style="margin-bottom: 0"><?php echo strtoupper($app->pfname." ".$app->plname); ?></h4>
    </div>
    <div class="col-md-6">
        <div class="ticking font-weight-bold" style="font-size: 25px">
                    <?php
                    if($app->ap_status != 'booked'){
                        if($app->check_in_time!=NULL){
                            ?>
                            <i class="far fa-clock" style="color: #000"></i> <span id="app<?php echo $a_id; ?>" class="font-weight-bold"></span>
                            <?php
                        }
                    }else{
                        ?>
                            <i class="far fa-clock" style="color: #000;"></i> <span  style="color: #F84982;">00</span><span class="small font-weight-bold">H</span><span  style="color: #F84982;">00</span><span class="small font-weight-bold">M</span> <span  style="color: #F84982;">00</span><span class="small font-weight-bold">S</span>
                        <?php
                    } 
                    ?>
                </div>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-5 text-uppercase">
               
                                                            <?php echo strtoupper($app->umr_no); ?> 
                                                </div>
            <div class="col-md-4">
                <i class="fa fa-phone"></i>  <?php echo $app->mobile; ?> 
            </div>
            <div class="col-md-3">
                <span class="badge bg-light small border status
                                  border-<?php echo $btn; ?>                                                         badge-pill"><?php echo str_replace("_"," ",$app->ap_status); ?></span>
            </div>
        </div>

                    <span class="text-primary font-weight-bold" style="font-size: 95%"><?php echo "DR. ".strtoupper($app->dfname." ".$app->dlname); ?></span>
                   
            </div>
    <div class="col-md-1 text-right border-left bg-light rounded-right d-flex align-items-center justify-content-center">
        <a href="<?php echo base_url(); ?>/profile/index/<?php echo $app->patient_id; ?>" class="btn bg-transparent text-primary p-3 btn-social-icon rounded-right view-booking-detail"><i class="fa fa-chevron-right" style="margin-left: -5px;margin-right: 0"></i></a>
    </div></div>

<?php $a++;} ?>
</div>
</div></div></div>

                              

                    
       <?php 
$CI = &get_instance();
       foreach($appointment as $app1){
        
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
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="fa fa-times"></span></button>
                    </div>
                    <div class="modal-body">
                         <input type="hidden" name="appointment_id" value="<?php echo $app1->appointment_id; ?>">
                            <input type="hidden" name="clinic_id" value="<?php echo $app1->clinic_id; ?>">
                            <input type="hidden" name="patient_id" value="<?php echo $app1->patient_id; ?>">
                            <input type="hidden" name="umr_no" value="<?php echo $app1->umr_no; ?>">
                            <input type="hidden" name="doctor_id" value="<?php echo $app1->doctor_id; ?>">
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
                                  <!--   <a class="btn  btn-primary " id="res_div">Reschedule</a> -->
                                    <a class="btn  btn-danger " href='<?php echo base_url('appointment/appointment_status/drop/'.$app1->appointment_id);?>'>Drop</a>
                                <?php } 
                                else  if($reg_info->payment_status == 2 || $info->payment_status == 2) { ?>
                                    <a class="btn  btn-success "  href='<?php echo base_url('appointment/appointment_status/checked_in/'.$app1->appointment_id);?>'>Check in </a>
                                                                
                                <?php } 
                                else { ?>
                                    <a class="btn  btn-success "  href='<?php echo base_url('appointment/appointment_status/checked_in/'.$app1->appointment_id);?>'>Check in </a>
                                    <!-- <a class="btn  btn-primary " id="res_div">Reschedule</a> -->                                    
                                <?php } ?>
                                    
                               
                                <?php }else if($app1->ap_status=='closed'){ ?>
                                   <div class="col-md-12 text-center mt-2 mb-2"><a href="<?php echo base_url('caseSheet/patient_info/'.$app1->patient_id.'/'.$app1->appointment_id); ?>" style="margin: 0 2px;" class="btn btn-sm btn-outline-primary"> GO TO VTALS</a></div>
                                <?php }else if($app1->ap_status=='drop'){ ?>
                                    <!--<button type="button" class="btn  btn-primary ">Login</button>-->
                                <?php } else {?>
                                    <div class="col-md-12 text-center mt-2 mb-2"><a href="<?php echo base_url('caseSheet/patient_info/'.$app1->patient_id.'/'.$app1->appointment_id); ?>" style="margin: 0 2px;" class="btn btn-sm btn-outline-primary"> GO TO VTALS</a></div>

                                <?php } ?>
                            </form>                           
                        </div>

                        <div class="row col-md-12 col-sm-12 text-center reschedule_div" style="display:none;padding: 20px">
                            <?php echo form_open(site_url("appointment/reschedule"), array("class" => "form-inline","id"=>"app_form")) ?>
                            <div class="col-md-3">
                            <div class="form-group">
                                <label for="input-date" class="sr-only">Date</label>
                                <input id="date_of_birth" type="text"  name="date" placeholder="DD / MM / YYYY" />
                            </div>
                        </div>
                 
                            <div class="col-md-3">
                            <button  did = "<?php echo $app1->doctor_id; ?>" id="check_btn" class="btn btn-warning margin-l-5 mx-sm-3">Check</button>
                        </div>
                            <div class="col-md-3">
                            <div class="form-group mx-sm-3">
                                <label for="input-password" class="sr-only"></label>
                                <select class="form-control" id="slots" name="slots"></select>
                            </div>
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
    </div>
<?php } ?>
     
                        </div>

                </div>
            </div></div></div>
            <script type="text/javascript">
                $(document).on("click","#res_div",function(){
        $(".reschedule_div").show();
        
    });

    $(document).on("click","#check_btn",function(){
        var sel_date = $("input[name=date]").val();
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



  $( function() {
 
    $( "#date_of_birth" ).datepicker({
      changeMonth: true,
      changeYear: true,
      dateFormat: "dd-mm-yy",
      minDate: 0
   
      
  });
});
            </script>