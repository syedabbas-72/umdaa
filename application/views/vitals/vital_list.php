<?php $this->view('vitals/left_nav'); ?>
          <div class="col-md-9">
 <div class="row col-md-12" >
  <div class="col-md-4"> <h3>VITALS INFORMATION </h3></div>
  <div class="col-md-8">
  <a href="<?php echo base_url('caseSheet/print_vitals/'.$appointment_id); ?>" class="btn btn-primary pull-right" style="padding:10px;margin-right:10px">Print</a>

  <a style="padding:10px;margin-right:10px"  id = "vital_edit" href = "<?php echo base_url('caseSheet/vital_edit/'.$patient_id.'/'.$appointment_id); ?>" class="btn btn-primary pull-right">Edit</a>
  <a style="padding:10px;margin-left:10px;margin-right:10px"  id = "vital_add" href = "<?php echo base_url('caseSheet/vital_add/'.$patient_id.'/'.$appointment_id); ?>" class="btn btn-primary pull-right">Add</a>
  </div><div class="row col-md-12 text-center" ></div></div>
<?php
      
      for($j=0;$j<count($result);$j++)
      {
          $vital_result = $this->db->query('select * from patient_vital_sign where patient_id = "'.$patient_id.'" and appointment_id = "'.$appointment_id.'" and vital_sign_recording_date_time = "'.$result[$j]->vital_sign_recording_date_time.'"')->result();

           $vital_data .= '<div class = "card"><div class = "card-body">'.date('d-m-Y H:i',strtotime($vital_result[$j]->vital_sign_recording_date_time)).'<div class = "row">';
          for($k = 0;$k<count($vital_result);$k++){
                 if($vital_result[$j]->vital_result != ""){

                $test = $this->db->query('select * from vital_sign where short_form = "'.$vital_result[$k]->vital_sign.'"')->row();
             if($vital_result[$k]->vital_sign == "BP"){
                $status_info1 = $this->db->query('select * from vital_sign where short_form ="SBP"')->row();
              $status_info2 = $this->db->query('select * from vital_sign where short_form ="DBP"')->row();
               $bp_arr = explode("/", $vital_result[$k]->vital_result);
               if($bp_arr[0] >= $status_info1->low_range && $bp_arr[0] <= $status_info1->high_range)
              {
                $sbp_color = "black";
              }
              else
              {
                $sbp_color = "red";
              }
              if($bp_arr[1] >= $status_info2->low_range && $bp_arr[1] <= $status_info2->high_range)
              {
                $dbp_color = "black";
              }
              else
              {
                $dbp_color = "red";
              }
?>
              <div class = "col-md-4" style="padding:10px;"><h5><?php echo $vital_result[$k]->vital_sign; ?></h5><h3><span style="color:<?php echo $sbp_color; ?>"><?php echo $bp_arr[0]; ?></span>/<span style="color:<?php echo $dbp_color; ?>;"><?php echo $bp_arr[1]; ?></span><span style = "font-size:10px;"><?php echo $test->unit; ?></span></h3></div>
             <?php }
              else{
               
                $status_info = $this->db->query("select * from vital_sign where short_form ='".$vital_result[$k]->vital_sign."'")->row();
              print_r($status_info->low_range);exit;
              
                  if($vital_result[$j]->vital_result >= $status_info->low_range  && $vital_result[$j]->vital_result <= $status_info->high_range)
                  {
                 $color = "black";
             }
             else
             {
                $color = "red";
             }
?>
             <div class = "col-md-4" style="padding:10px"><h5><?php echo $vital_result[$k]->vital_sign; ?></h5><h3 style="color:<?php echo $color; ?>"><?php echo $vital_result[$k]->vital_result; ?><span style = "font-size:10px;"><?php echo $test->unit; ?></span></h3></div>
          <?php    }
            
          
        
        

            }
           }
           
?>
           </div>

           
    <?php  } ?>
          </div></div>
     
    </div>
