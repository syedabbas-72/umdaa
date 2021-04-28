   <div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">UMDAA CLINICS</a></li>
            <li class="breadcrumb-item active">SUMMARY REPORTS</li>
          </ol>
        </div>
        
    </div>
<section class="main-content">
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
              <form method="post" action="<?php echo base_url('SummaryReports');?>"  autocomplete="off" enctype="multipart/form-data">  
                <div class="row col-md-12">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="clinic_id" class="col-form-label">CLINIC</label>
                          <select id="clinic_id" name="clinic_id" class="form-control" onchange="getClinicPatients(this.value)" required="">
                            <option value="">--select--</option>
                            <?php foreach ($clinics as $value) {?>
                            <option value="<?php echo $value->clinic_id;?>"><?php echo $value->clinic_name;?></option>
                          <?php } ?>
                        </select>
                    </div>
                  </div> 

                  <div class="col-md-4">
                      <div class="form-group" id="clinic_patients">
                        <?php if(isset($patients)){
                           echo'<label for="patient_id" class="col-form-label">PATIENT</label>
                            <select name="patient_id" id="patient_id" class="form-control" required>
                            <option value=""> Seect Patient </option>';
                            foreach ($patients as $key => $value) {
                            echo'<option value="'.$value->patient_id.'">'.$value->first_name.'</option>';
                            }
                           echo '</select>';
                         }?>
                      </div>
                  </div>
                  <!-- <div class="col-md-4">
                     <div class="form-group" style="padding-top:33px;">
                      <input type="submit" value="SUBMIT" name="submit" class="btn btn-success">
                    </div>
                  </div> -->

                </div>
                 <div class="row col-md-12">
                  <div class="col-md-12">
                    <div class="form-group">
                      <div id="appointments_dt">
                        
                      </div>
                    </div>
                  </div>
                 </div>    
              </form>
            </div>
        </div>
    </div>
</div>

</section>
<script type="text/javascript">
  function getClinicPatients(id){
  var url = "<?php echo  base_url('SummaryReports/getClinicPatients'); ?>";
 
$.ajax({
type : 'POST',
url : url,
data: {
clinic_id:id},
beforeSend:function(data){ 
 
 $('#clinic_patients').html('<img src="<?php echo base_url('assets/images/load.gif');?>" />');
},success:function (data) {
  
 $('#clinic_patients').html(data); 
 }
});
}
function getAppointments(patient_id){
var url = "<?php echo  base_url('SummaryReports/getAppointments'); ?>";
 var clinic_id = $("#clinic_id").val();
$.ajax({
type : 'POST',
url : url,
data: {
clinic_id:clinic_id,
patient_id:patient_id},
beforeSend:function(data){ 
 
 $('#appointments_dt').html('<img src="<?php echo base_url('assets/images/load.gif');?>" />');
},success:function (data) {
  
 $('#appointments_dt').html(data); 
 }
});
}
</script>

 