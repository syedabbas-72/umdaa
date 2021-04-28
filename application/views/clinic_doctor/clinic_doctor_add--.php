<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/business-hours.css">
<!-- Clock Picker -->
    <link href="<?php echo base_url(); ?>assets/lib/clockpicker/bootstrap-clockpicker.min.css" rel="stylesheet">
  
       
<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">HOME</a></li>
          <li class="breadcrumb-item"><a href="#">CLINIC-DOCTOR</a></li>
          <li class="breadcrumb-item active"><a href="#">ADD</a></li>          
        </ol>
  </div>
</div>

        <section class="main-content">
            <div class="row">             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                         <div class="col-md-12">
                          <?php echo form_open("clinic_doctor/clinic_doctor_add"); ?>
                            <div class="row col-md-12">
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label for="clinic_name" class="col-form-label">CLINIC NAME</label>
                                    <select name="clinic_name" id="clinic_name" class="form-control" Required="">
                                        <option>--select--</option>
                                          <?php foreach($clinic_list as $val){?>
                                            <option value="<?php echo $val->clinic_id; ?>"><?php echo $val->clinic_name; ?></option>
                                          <?php } ?>
                                        </select>
                                </div></div>
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label for="doctor_name" class="col-form-label">DOCTOR NAME</label>
                                        <select name="doctor_name" id="doctor_name"  class="form-control" onchange='show_days()'>
                                          <option>--select--</option>
                                          <?php foreach($doctor_list as $val){?>
                                            <option value="<?php echo $val->doctor_id; ?>"><?php echo $val->first_name; ?></option>
                                          <?php } ?>
                                        </select>
                                    </div>
                                </div>
								<div class="col-md-12">
                                  <div class="form-group">
                                    <label for="doctor_name" class="col-form-label">WEEK DAYS</label>
                                        <div class="checkbox checkbox-success margin-r-5">
													<input id="checkbox1" type="checkbox" name="weekdays[]"  value="1">
													<label for="checkbox1"> Monday </label>
													
												</div>
												 <div class="checkbox checkbox-success margin-r-5">
													<input id="checkbox2" type="checkbox" name="weekdays[]" value="2">
													<label for="checkbox2"> Tuesday </label>
												</div>
												 <div class="checkbox checkbox-success margin-r-5">
													<input id="checkbox3" type="checkbox" name="weekdays[]"  value="3">
													<label for="checkbox3"> Wednesday </label>
												</div>
												 <div class="checkbox checkbox-success margin-r-5">
													<input id="checkbox4" type="checkbox" name="weekdays[]"  value="4">
													<label for="checkbox4"> Thursday </label>
												</div>
												 <div class="checkbox checkbox-success margin-r-5">
													<input id="checkbox5" type="checkbox" name="weekdays[]"  value="5">
													<label for="checkbox5"> Friday </label>
												</div>
												 <div class="checkbox checkbox-success margin-r-5">
													<input id="checkbox6" type="checkbox" name="weekdays[]"  value="6">
													<label for="checkbox6"> Saturday </label>
												</div>
												 <div class="checkbox checkbox-success margin-r-5">
													<input id="checkbox7" type="checkbox" name="weekdays[]"  value="7">
													<label for="checkbox7"> Sunday </label>
												</div>
                                    </div>
                                </div>
                            </div> 


                            
                                 <div class="col-sm-6">
                                        <div class="pull-right">
                                            <input type="submit" value="Save" name="submit" class="btn btn-success btn-rounded box-shadow">
                                        </div>
                                    </div> 

                                     
                                  
                                </form>
                              </div>

                        </div>
                    </div>
                </div>

            </div>
        </section> 



 
<!-- Clock Picker -->
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/lib/clockpicker/bootstrap-clockpicker.min.js"></script>
<script type="text/javascript">

   function show_days(){
    var selected_doctor = $('#doctor_name :selected').val();
    var selected_clinic = $('#clinic_name :selected').val();
    $("#clinic_id").val(selected_clinic);
    $("#doctor_id").val(selected_doctor);
   
 $("#pick_time").show();
 $("#submitBtn").show();
 $('.clockpicker').clockpicker({
      placement: 'bottom',
          align: 'left',
          autoclose: true,
          default: '12:00' 
    });
   }
//    $('input[name="days"]:checked').each(function() {
//    console.log(this.value);
// });

   function get_picker(id){
    if ($('input[name="days"]:checked').length == 0) {
      $('input[name="days"]:not(:checked)').removeAttr('disabled');
    }
    else {
      $('input[name="days"]:not(:checked)').attr('disabled', 'disabled');
    }
 
    var id = $('#checkbox'+id).attr("id");
    $('.clockpicker').clockpicker({
      placement: 'bottom',
          align: 'left',
          autoclose: true,
          default: '12:00'
    });
    $("#pick_time").show();
    $("#submitBtn").show();

    
   }
   function picker_add(day_id,name,id){
     $("#add_"+id).attr("disabled","disabled");
    var next_row_id = parseInt(id)+1;
      $("#"+name+id).after('<div class="operationDayTimeContainer" id="'+name+next_row_id+'"><div class="operationTime" style="height: 85px;border: 1px solid #ddd;padding: 5px;"><div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true"><input type="text" class="form-control from_'+day_id+'[]" name="from_'+day_id+'_'+next_row_id+'[]" value="<?php echo date("h: i"); ?>"></div><div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true"><input type="text" class="form-control to_'+day_id+'_'+next_row_id+'[]" name="to_'+day_id+'_'+next_row_id+'[]" value="<?php echo date("h: i"); ?>"></div><input type="hidden" name="total[]" value="'+day_id+'_'+next_row_id+'"><div class="mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true"  style="padding: 6px"><input type="button" id="add_'+next_row_id+'" class="btn btn-success" onclick= "picker_add(\'' + day_id  +'\',\'' + name  +'\',\'' + next_row_id  +'\')" value="+" style="padding: 2px;width: 100%"/></div><div class="mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true" style="padding: 6px"><input type="button" id="delete_'+next_row_id+'" class="btn btn-danger" onclick= "picker_delete(\'' + name  +'\',\'' + next_row_id  +'\')" value="-" style="padding: 2px;width: 100%" /></div></div></div>');
      $('.clockpicker').clockpicker({
      placement: 'bottom',
          align: 'left',
          autoclose: true
    });
   }
   function picker_delete(name,id){
    $("#"+name+id).remove();
 $("#add_"+id).removeAttr("disabled");
   }

   $(document).on('click','#submit',function(){
 
       for(i=1;i<=7;i++){
        alert($(".from_"+i).val());
       }
    

   });
   

</script>
<!--
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/business-hours.css">
<!-- Clock Picker 
    <link href="<?php echo base_url(); ?>assets/lib/clockpicker/bootstrap-clockpicker.min.css" rel="stylesheet">
  
       
<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">HOME</a></li>
          <li class="breadcrumb-item"><a href="#">CLINIC-DOCTOR</a></li>
          <li class="breadcrumb-item active"><a href="#">ADD</a></li>          
        </ol>
  </div>
</div>

        <section class="main-content">
            <div class="row">             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                         <div class="col-md-12">
                          
                            <div class="row col-md-12">
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label for="clinic_name" class="col-form-label">CLINIC NAME</label>
                                    <select name="clinic_name" id="clinic_name" class="form-control" Required="">
                                        <option>--select--</option>
                                          <?php foreach($clinic_list as $val){?>
                                            <option value="<?php echo $val->clinic_id; ?>"><?php echo $val->clinic_name; ?></option>
                                          <?php } ?>
                                        </select>
                                </div></div>
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label for="doctor_name" class="col-form-label">DOCTOR NAME</label>
                                        <select name="doctor_name" id="doctor_name"  class="form-control" onchange='show_days()'>
                                          <option>--select--</option>
                                          <?php foreach($doctor_list as $val){?>
                                            <option value="<?php echo $val->doctor_id; ?>"><?php echo $val->first_name; ?></option>
                                          <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div> 


                            
                                <!-- <div class="col-sm-6">
                                        <div class="pull-right">
                                            <input type="submit" value="Save" name="submit" class="btn btn-success btn-rounded box-shadow">
                                        </div>
                                    </div> -->

                                    <!-- <div class="col-sm-6">
                                        <div class="pull-left">
                                            <button type="submit" class="btn  btn-gray btn-rounded btn-border btn-sm">
                                                <i class=""></i> Cancel
                                            </button>
                                        </div>
                                    </div> 
                                  
                                </form>
                              </div>

                                <div class="row col-md-12" id="pick_time" style="display: none;margin-top: 2rem">
                     <?php echo form_open("clinic_doctor/clinic_doctor_add"); ?>
                     <input type="hidden" name="clinic_id" id="clinic_id" value="">
                     <input type="hidden" name="doctor_id" id="doctor_id" value="">
                                  <div class="clean"></div>
								  
                                  <div class="dayContainer">
								  <input id="checkbox1" type="checkbox" name="weekdays[]"  value="1">
								  <div data-original-title="" class="colorBox WorkingDayState">
								  
								  <input type="checkbox" class="invisible operationState"><div class="day_name">Mon</div></div>
                                  
                                  <div class="operationDayTimeContainer" id="Mon1"><div class="operationTime" style="height: 85px;border: 1px solid #ddd;padding: 5px;"><div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <input type="text" class="form-control from_1" name="from_1_1[]" value="<?php echo date("h: i"); ?>">
                       
                    </div><div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <input type="text" class="form-control to_1" name="to_1_1[]" value="<?php echo date("h: i"); ?>">
                      
                    </div>
                    <input type="hidden" name="total[]" value="1_1">
                    <div class="mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true"  style="padding: 6px">
                       <input type="button" id="add_1" class="btn btn-success" onclick= "picker_add('1','Mon','1')" value="+" style="padding: 2px;width: 100%"/>
                      
                    </div>
                   
                  </div></div>
                </div>

                                  <div class="dayContainer"><div data-original-title="" class="colorBox WorkingDayState"><input type="checkbox" class="invisible operationState"><div class="day_name">Tue</div></div><div class="operationDayTimeContainer" id="Tue1"><div class="operationTime" style="height: 85px;border: 1px solid #ddd;padding: 5px;"><div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <input type="text" class="form-control from_2" name="from_2_1[]" value="<?php echo date("h: i"); ?>">
                      
                    </div><div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <input type="text" class="form-control to_2" name="to_2_1[]" value="<?php echo date("h: i"); ?>">
                     
                      
                    </div>
                     <input type="hidden" name="total[]" value="2_1">
                    <div class="mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true"  style="padding: 6px">
                       <input type="button" id="add_1" class="btn btn-success" onclick= "picker_add('2','Tue','1')" value="+" style="padding: 2px;width: 100%"/>
                      
                    </div>
                    
                  </div></div></div>

                                  <div class="dayContainer"><div data-original-title="" class="colorBox WorkingDayState"><input type="checkbox" class="invisible operationState"><div class="day_name">Wed</div></div><div class="operationDayTimeContainer" id="Wed1"><div class="operationTime" style="height: 85px;border: 1px solid #ddd;padding: 5px;"><div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <input type="text" class="form-control from_3" name="from_3_1[]" value="<?php echo date("h: i"); ?>">
                      
                    </div><div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <input type="text" class="form-control to_3" name="to_3_1[]" value="<?php echo date("h: i"); ?>">
                      
                      
                    </div>
                    <input type="hidden" name="total[]" value="3_1">
                    <div class="mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true"  style="padding: 6px">
                       <input type="button" id="add_1" class="btn btn-success" onclick= "picker_add('3','Wed','1')" value="+" style="padding: 2px;width: 100%"/>
                      
                    </div>
                    
                  </div></div></div>

                                  <div class="dayContainer"><div data-original-title="" class="colorBox WorkingDayState"><input type="checkbox" class="invisible operationState"><div class="day_name">Thur</div></div><div class="operationDayTimeContainer" id="Thu1"><div class="operationTime" style="height: 85px;border: 1px solid #ddd;padding: 5px;"><div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <input type="text" class="form-control from_4" name="from_4_1[]" value="<?php echo date("h: i"); ?>">
                      
                      
                    </div><div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <input type="text" class="form-control to_4" name="to_4_1[]" value="<?php echo date("h: i"); ?>">
                      
                    </div>
                    <input type="hidden" name="total[]" value="4_1">
                    <div class="mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true"  style="padding: 6px">
                       <input type="button" id="add_1" class="btn btn-success" onclick= "picker_add('4','Thu','1')" value="+" style="padding: 2px;width: 100%"/>
                      
                      
                    </div>
                    
                  </div></div></div>

                                  <div class="dayContainer"><div data-original-title="" class="colorBox WorkingDayState"><input type="checkbox" class="invisible operationState"><div class="day_name">Fri</div></div><div class="operationDayTimeContainer" id="Fri1"><div class="operationTime" style="height: 85px;border: 1px solid #ddd;padding: 5px;"><div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <input type="text" class="form-control from_5" name="from_5_1[]" value="<?php echo date("h: i"); ?>">
                      
                    </div><div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <input type="text" class="form-control to_5" name="to_5_1[]" value="<?php echo date("h: i"); ?>">
                     
                      
                    </div>
                     <input type="hidden" name="total[]" value="5_1">
                    <div class="mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true"  style="padding: 6px">
                       <input type="button" id="add_1" class="btn btn-success" onclick= "picker_add('5','Fri','1')" value="+" style="padding: 2px;width: 100%"/>
                      
                    </div>
                   
                  </div></div></div>

                                  <div class="dayContainer"><div data-original-title="" class="colorBox RestDayState"><input type="checkbox" class="invisible operationState"><div class="day_name">Sat</div></div><div class="operationDayTimeContainer" id="Sat1"><div class="operationTime" style="height: 85px;border: 1px solid #ddd;padding: 5px;"><div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <input type="text" class="form-control from_6" name="from_6_1[]" value="<?php echo date("h: i"); ?>">
                     
                      
                    </div><div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <input type="text" class="form-control to_6" name="to_6_1[]" value="<?php echo date("h: i"); ?>">
                      
                    </div>
                     <input type="hidden" name="total[]" value="6_1">
                    <div class="mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true"  style="padding: 6px">
                       <input type="button" id="add_1" class="btn btn-success" onclick= "picker_add('6','Sat','1')" value="+" style="padding: 2px;width: 100%"/>
                      
                    </div>
                   
                  </div></div></div>

                                  <div class="dayContainer"><div data-original-title="" class="colorBox RestDayState"><input type="checkbox" class="invisible operationState"><div class="day_name">Sun</div></div><div class="operationDayTimeContainer" id="Sun1"><div class="operationTime" style="height: 85px;border: 1px solid #ddd;padding: 5px;"><div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <input type="text" class="form-control from_7" name="from_7_1[]" value="<?php echo date("h: i"); ?>">
                      
                    </div><div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <input type="text" class="form-control to_7" name="to_7_1[]" value="<?php echo date("h: i"); ?>">
                      
                      
                    </div>
                    <input type="hidden" name="total[]" value="7_1">
                    <div class="mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true"  style="padding: 6px">
                       <input type="button" id="add_1" class="btn btn-success" onclick= "picker_add('7','Sun','1')" value="+" style="padding: 2px;width: 100%"/>
                      
                    </div>
                    
                  </div></div></div>
                                
                 
                           
                                 
                                 
                                </div>

                                <div class="row col-md-12" id="submitBtn" style="margin-left: 40%;margin-top: 2rem;display: none">
                                  <input type="submit" class="btn btn-success" name="submit" value="Create Schedule">
                                </div>
                               
                                <?php form_close(); ?>
                        </div>
                    </div>
                </div>

            </div>
        </section> 

-->

 
<!-- Clock Picker -->
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/lib/clockpicker/bootstrap-clockpicker.min.js"></script>
<script type="text/javascript">

   function show_days(){
    var selected_doctor = $('#doctor_name :selected').val();
    var selected_clinic = $('#clinic_name :selected').val();
    $("#clinic_id").val(selected_clinic);
    $("#doctor_id").val(selected_doctor);
   
 $("#pick_time").show();
 $("#submitBtn").show();
 $('.clockpicker').clockpicker({
      placement: 'bottom',
          align: 'left',
          autoclose: true,
          default: '12:00' 
    });
   }
//    $('input[name="days"]:checked').each(function() {
//    console.log(this.value);
// });

   function get_picker(id){
    if ($('input[name="days"]:checked').length == 0) {
      $('input[name="days"]:not(:checked)').removeAttr('disabled');
    }
    else {
      $('input[name="days"]:not(:checked)').attr('disabled', 'disabled');
    }
 
    var id = $('#checkbox'+id).attr("id");
    $('.clockpicker').clockpicker({
      placement: 'bottom',
          align: 'left',
          autoclose: true,
          default: '12:00'
    });
    $("#pick_time").show();
    $("#submitBtn").show();

    
   }
   function picker_add(day_id,name,id){
     $("#add_"+id).attr("disabled","disabled");
    var next_row_id = parseInt(id)+1;
      $("#"+name+id).after('<div class="operationDayTimeContainer" id="'+name+next_row_id+'"><div class="operationTime" style="height: 85px;border: 1px solid #ddd;padding: 5px;"><div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true"><input type="text" class="form-control from_'+day_id+'[]" name="from_'+day_id+'_'+next_row_id+'[]" value="<?php echo date("h: i"); ?>"></div><div class="clockpicker  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true"><input type="text" class="form-control to_'+day_id+'_'+next_row_id+'[]" name="to_'+day_id+'_'+next_row_id+'[]" value="<?php echo date("h: i"); ?>"></div><input type="hidden" name="total[]" value="'+day_id+'_'+next_row_id+'"><div class="mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true"  style="padding: 6px"><input type="button" id="add_'+next_row_id+'" class="btn btn-success" onclick= "picker_add(\'' + day_id  +'\',\'' + name  +'\',\'' + next_row_id  +'\')" value="+" style="padding: 2px;width: 100%"/></div><div class="mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true" style="padding: 6px"><input type="button" id="delete_'+next_row_id+'" class="btn btn-danger" onclick= "picker_delete(\'' + name  +'\',\'' + next_row_id  +'\')" value="-" style="padding: 2px;width: 100%" /></div></div></div>');
      $('.clockpicker').clockpicker({
      placement: 'bottom',
          align: 'left',
          autoclose: true
    });
   }
   function picker_delete(name,id){
    $("#"+name+id).remove();
 $("#add_"+id).removeAttr("disabled");
   }

   $(document).on('click','#submit',function(){
 
       for(i=1;i<=7;i++){
        alert($(".from_"+i).val());
       }
    

   });
   

</script>
