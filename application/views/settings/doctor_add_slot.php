
<div class="row page-header">
   <div class="col-lg-6 align-self-center ">       
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">HOME</a></li>
          <li class="breadcrumb-item"><a href="#">DOCTOR TIMINGS</a></li>
          <li class="breadcrumb-item active"><a href="#">ADD SLOT</a></li>          
        </ol>
  </div>
</div>

        <section class="main-content">
            <div class="row">             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                       

                      <div class="row col-md-12" id="pick_time" style="display: block;margin-top: 2rem">
                     <?php echo form_open("settings/clinic_doctor_add_sloat"); ?>
                     <input type="hidden" name="clinic_id" id="clinic_id" value="">
                     <input type="hidden" name="doctor_id" id="doctor_id" value="">
                     <input type="hidden" name="consultation_fee" id="consultation_fee" value="">
					 <input type="hidden" name="consultation_time" id="consultation_time" value="">
					 
					 <input type='hidden' name='clinic_doctor_weekday_id' value='<?php echo $clinic_doctor_weekday_id;?>'>
					 <input type='hidden' name='weekday' value='<?php echo $weekday;?>'>

                                  <div class="clean"></div>
								  
								  <?php if($weekday==1){?>
                   <div class="dayContainer">
                  
                 <div class="checkbox checkbox-inline checkbox-success" style="padding-left: 0">
               <input id="checkbox1" type="checkbox" name="weekdays[]" checked value="1">
                <label for="checkbox1" class="font-weight-bold m-t-10"> Monday </label>
              </div>
                                  
                                  <div class="operationDayTimeContainer" id="Mon1"><div class="row col-md-12 operationTime" style="height: 50px;padding: 5px;"><div class="clockpicker col-md-3  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select class="form-control from_1 time_slot" name="from_1_1[]"></select>
                       
                    </div><div class="clockpicker col-md-3   mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select class="form-control to_1 time_slot" name="to_1_1[]"></select>
                      
                    </div>
                    <input type="hidden" name="total[]" value="1_1">
                    <div class="mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                       <input type="button" id="add_1" class="btn btn-success" onclick= "picker_add('1','Mon','1')" value="+" style="padding: 6px;margin-right:10px;width: 100%"/>
                      
                    </div>
                   
                  </div></div>
                </div>
								  <?php }?>
								    <?php if($weekday==2){?>

                        <div class="dayContainer">
                  
                 <div class="checkbox checkbox-inline checkbox-success" style="padding-left: 0">
               <input id="checkbox1" type="checkbox" name="weekdays[]" checked value="2">
                <label for="checkbox1" class="font-weight-bold m-t-10"> Tuesday </label>
              </div>
                                  
                                  <div class="operationDayTimeContainer" id="Tue1"><div class="row col-md-12 operationTime" style="height: 50px;padding: 5px;"><div class="clockpicker col-md-3  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select class="form-control from_2 time_slot" name="from_2_1[]"></select>
                       
                    </div><div class="clockpicker col-md-3   mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select class="form-control to_2 time_slot" name="to_2_1[]"></select>
                      
                    </div>
                    <input type="hidden" name="total[]" value="2_1">
                    <div class="mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                       <input type="button" id="add_2" class="btn btn-success" onclick= "picker_add('2','Tue','1')" value="+" style="padding: 6px;margin-right:10px;width: 100%"/>
                      
                    </div>
                   
                  </div></div>
                </div>
									<?php }?>
									  <?php if($weekday==3){?>
 <div class="dayContainer">
                  
                 <div class="checkbox checkbox-inline checkbox-success" style="padding-left: 0">
               <input id="checkbox1" type="checkbox" name="weekdays[]" checked value="3">
                <label for="checkbox1" class="font-weight-bold m-t-10"> Wednesday </label>
              </div>
                                  
                                  <div class="operationDayTimeContainer" id="Wed1"><div class="row col-md-12 operationTime" style="height: 50px;padding: 5px;"><div class="clockpicker col-md-3  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select class="form-control from_3 time_slot" name="from_3_1[]"></select>
                       
                    </div><div class="clockpicker col-md-3   mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select class="form-control to_3 time_slot" name="to_3_1[]"></select>
                      
                    </div>
                    <input type="hidden" name="total[]" value="3_1">
                    <div class="mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                       <input type="button" id="add_3" class="btn btn-success" onclick= "picker_add('3','Wed','1')" value="+" style="padding: 6px;margin-right:10px;width: 100%"/>
                      
                    </div>
                   
                  </div></div>
                </div>
				  
									  <?php }?>
									    <?php if($weekday==4){?>

                                <div class="dayContainer">
                  
                 <div class="checkbox checkbox-inline checkbox-success" style="padding-left: 0">
               <input id="checkbox1" type="checkbox" name="weekdays[]" checked value="4">
                <label for="checkbox1" class="font-weight-bold m-t-10"> Thursday </label>
              </div>
                                  
                                  <div class="operationDayTimeContainer" id="Thu1"><div class="row col-md-12 operationTime" style="height: 50px;padding: 5px;"><div class="clockpicker col-md-3  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select class="form-control from_4 time_slot" name="from_4_1[]"></select>
                       
                    </div><div class="clockpicker col-md-3   mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select class="form-control to_4 time_slot" name="to_4_1[]"></select>
                      
                    </div>
                    <input type="hidden" name="total[]" value="4_1">
                    <div class="mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                       <input type="button" id="add_4" class="btn btn-success" onclick= "picker_add('4','Thu','1')" value="+" style="padding: 6px;margin-right:10px;width: 100%"/>
                      
                    </div>
                   
                  </div></div>
                </div>
				  
										<?php }?>
										
										  <?php if($weekday==5){?>

                                <div class="dayContainer">
                  
                 <div class="checkbox checkbox-inline checkbox-success" style="padding-left: 0">
               <input id="checkbox1" type="checkbox" name="weekdays[]" checked value="5">
                <label for="checkbox1" class="font-weight-bold m-t-10"> Friday </label>
              </div>
                                  
                                  <div class="operationDayTimeContainer" id="Fri1"><div class="row col-md-12 operationTime" style="height: 50px;padding: 5px;"><div class="clockpicker col-md-3  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select class="form-control from_5 time_slot" name="from_5_1[]"></select>
                       
                    </div><div class="clockpicker col-md-3   mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select class="form-control to_5 time_slot" name="to_5_1[]"></select>
                      
                    </div>
                    <input type="hidden" name="total[]" value="5_1">
                    <div class="mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                       <input type="button" id="add_5" class="btn btn-success" onclick= "picker_add('5','Fri','1')" value="+" style="padding: 6px;margin-right:10px;width: 100%"/>
                      
                    </div>
                   
                  </div></div>
                </div>
				  
										  <?php }?>
										  
										    <?php if($weekday==6){?>

                             <div class="dayContainer">
                  
                 <div class="checkbox checkbox-inline checkbox-success" style="padding-left: 0">
               <input id="checkbox1" type="checkbox" name="weekdays[]" checked value="6">
                <label for="checkbox1" class="font-weight-bold m-t-10"> Saturday </label>
              </div>
                                  
                                  <div class="operationDayTimeContainer" id="Sat1"><div class="row col-md-12 operationTime" style="height: 50px;padding: 5px;"><div class="clockpicker col-md-3  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select class="form-control from_6 time_slot" name="from_6_1[]"></select>
                       
                    </div><div class="clockpicker col-md-3   mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select class="form-control to_6 time_slot" name="to_6_1[]"></select>
                      
                    </div>
                    <input type="hidden" name="total[]" value="6_1">
                    <div class="mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                       <input type="button" id="add_2" class="btn btn-success" onclick= "picker_add('6','Sat','1')" value="+" style="padding: 6px;margin-right:10px;width: 100%"/>
                      
                    </div>
                   
                  </div></div>
                </div>
				  
											<?php }?>
											
											  <?php if($weekday==7){?>

                                <div class="dayContainer">
                  
                 <div class="checkbox checkbox-inline checkbox-success" style="padding-left: 0">
               <input id="checkbox1" type="checkbox" name="weekdays[]" checked value="7">
                <label for="checkbox1" class="font-weight-bold m-t-10"> Sunday </label>
              </div>
                                  
                                  <div class="operationDayTimeContainer" id="Sun1"><div class="row col-md-12 operationTime" style="height: 50px;padding: 5px;"><div class="clockpicker col-md-3  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select class="form-control from_7 time_slot" name="from_7_1[]"></select>
                       
                    </div><div class="clockpicker col-md-3   mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                      <select class="form-control to_7 time_slot" name="to_7_1[]"></select>
                      
                    </div>
                    <input type="hidden" name="total[]" value="7_1">
                    <div class="mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true">
                       <input type="button" id="add_7" class="btn btn-success" onclick= "picker_add('7','Sun','1')" value="+" style="padding: 6px;margin-right:10px;width: 100%"/>
                      
                    </div>
                   
                  </div></div>
                </div>
                                
											  <?php }?>
                           
                                 
                                 
                                </div>

                                <div class="row col-md-12" id="submitBtn" style="margin-left: 40%;margin-top: 2rem;">
                                  <input type="submit" class="btn btn-success" name="submit" value="Add Slot">
                                </div>
                               
                                <?php form_close(); ?>
                        </div>
                    </div>
                </div>

            </div>
        </section> 

<script type="text/javascript">

          var timeSelect = '';
        
            
            for(var i=0; i < 12; i++) {
              var hour = i;
              var dis_h = i;
              if(hour <= 9) hour = '0' + hour;
              if(dis_h == 0) dis_h = 12;
              if(dis_h <= 9) dis_h = '0' + dis_h
              var time1 = hour + ':00';
              var time2 = hour + ':30';
              timeSelect +='<option value="' + time1 + '">' + dis_h  + ':00 AM</option>';
              timeSelect +='<option value="' + time2 + '">' + dis_h  + ':30 AM</option>';
            }
            timeSelect +='<option value="12:00">12:00 PM</option>';
            timeSelect +='<option value="12:30">12:30 PM</option>';
            for(var i=1; i < 12; i++) {
              var hour = i +12;
              var dis_h = i;
              if(hour <= 9) hour = '0' + hour;
              if(dis_h == 1) dis_h = 1;
              if(dis_h <= 9) dis_h = '0' + dis_h
              var time1 = hour + ':00';
              var time2 = hour + ':30';
              timeSelect +='<option value="' + time1 + '">' + dis_h  + ':00 PM</option>';
              timeSelect +='<option value="' + time2 + '">' + dis_h  + ':30 PM</option>';
            }
          
          $(".time_slot").html(timeSelect);
  function picker_add(day_id,name,id){
     $("#add_"+id).attr("disabled","disabled");
    var next_row_id = parseInt(id)+1;
      $("#"+name+id).after('<div class="operationDayTimeContainer" id="'+name+next_row_id+'"><div class="operationTime row col-md-12" style="height: 50px;padding: 5px;"><div class="clockpicker col-md-3  mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true"><select class="form-control time_slot from_'+day_id+'[]" name="from_'+day_id+'_'+next_row_id+'[]"></select></div><div class="clockpicker col-md-3 mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true"><select class="form-control time_slot to_'+day_id+'_'+next_row_id+'[]" name="to_'+day_id+'_'+next_row_id+'[]"></select></div><input type="hidden" name="total[]" value="'+day_id+'_'+next_row_id+'"><div class="mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true"><input type="button" id="add_'+next_row_id+'" class="btn btn-success" onclick= "picker_add(\'' + day_id  +'\',\'' + name  +'\',\'' + next_row_id  +'\')" value="+" style="padding: 6px;margin-right:10px;width: 100%"/></div><div class="mini-time operationTimeFrom" data-placement="left" data-align="top" data-autoclose="true"><input type="button" id="delete_'+next_row_id+'" class="btn btn-danger" onclick= "picker_delete(\'' + name  +'\',\'' + next_row_id  +'\')" value="-" style="padding: 6px;margin-left:10px;width: 100%" /></div></div></div>');
      $(".time_slot").html(timeSelect);
    
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
