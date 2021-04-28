<style type="text/css">
  
</style>


<div class="row">
<?php extract($form);?>
<section class="main-content">
 <div class = "row">
<?php


function getDependencyFields($parent_id,$option_id)
{
  $CI=&get_instance();
  $html = "";
   $dependencyRes = $CI->db->query('select * from field where parent_field_id ="'.$parent_id.'" and parent_option_id = '.$option_id)->result();

  if(count($dependencyRes)>0){
      // $html .= "<div style='padding:10px; border:1px solid #ccc; padding:10px; margin:10px 0px; clear:both' id='".$parent_id."_div' class='dependencyDiv ".$option_id."_div' data-target='".$parent_option_id."'>";
      // //print_r($dependencyRes);
           getField($dependencyRes);

        //$html .= $CI->getField($dependencyRes[$j]);
        

      // print_r($html);
      // $html .= "</div>";

      //return $html;
    }

}

// get Field
  function getField($field_rec) {
      
    $CI=&get_instance();
    //print_r($field_rec);
    for($k=0;$k<count($field_rec);$k++){
    $options= $CI->db->query('select * from field_option where field_id = "'.$field_rec[$k]->parent_field_id.'"')->result();
      //print_r($options);

    if($field_rec[$k]->field_type == "text")
    {
     echo "<div class = '' style = 'display:none;'>"; 
     echo "<label>";
     echo $field_rec[$k]->field_name;
     echo "</label>";
     echo "<input type = 'text' class = 'form-control'>";
     if(count($options)>0)
     {
     for($i=0;$i<count($options);$i++)
     {
       echo "<div class = ''>";
       echo "<label>";
       echo $options[$i]->option_name;
       echo "</label>";
       echo "<input type = 'radio' value = ''>";
       echo "</div>";

     }
     echo "</div>";
   }
     
   }
   //elseif($field_rec->field_type == "radio")
   //{
     // for($i=0;$i<count($options);$i++)
      //{
        // echo "<label>";
         //echo $$options[$i]->option_name;
        //echo "</label>";
         //echo "<input type = 'radio' value = "">";
         // if($field_rec->dependency == 1)
         // {
         //   getDependencyFields($field_rec->parent_field_id,$field_rec->parent_option_id);
         // }
      //}
   //}
   //elseif($field_rec->field_type == "checkbox")
   // {
   //    for($i=0;$i<count($options);$i++)
   //    {
   //       echo "<label>";
   //       echo $options->option_name;
   //       echo "</label>";
   //       echo "<input type = 'radio' value = "">";
   //    }
   // }
   
    }
  }





?>

 	  <?php for($i=0;$i<1;$i++){?>
 		<h4><?php echo $form['formName'];?></h4><br/>
 		<?php for($j=0;$j<count($form['sections']);$j++){?>
            <section style = "width:80%; margin-left: 50px;">
            	<div class = "card">

                  <?php for($l=0;$l<count($form['sections'][$j]['labels']);$l++) { ?>

            		<div class = "card-body">
            			<?php if($form['sections'][$j]['labels'][$l]['widgetType'] == "radio"){?>
                           <label><?php echo $form['sections'][$j]['labels'][$l]['labelText'];?></label><br/>
                           <?php for($k=0;$k<count($form['sections'][$j]['labels'][$l]['options']);$k++)  { ?>
                                 
                                   <label><?php echo $form['sections'][$j]['labels'][$l]['options'][$k]['optionText'];?></label>

                                   <input type = "radio" name = ""/>
                            

                                   <?php if(count($form['sections'][$j]['labels'][$l]['options'][$k]['dependency'])> 0) { ?>
                                       
                                       <?php getDependencyFields($form['sections'][$j]['labels'][$l]['id'],$form['sections'][$j]['labels'][$l]['options'][$k]['id']);?>

                                       
               
                               <?php }else{ ?>


                                <?php } ?>
                              
                           <?php } ?>
                       <?php }elseif($form['sections'][$j]['labels'][$l]['widgetType'] == "checkbox") { ?>
                       	   <label><?php echo $form['sections'][$j]['labels'][$l]['labelText'];?></label><br/>
            				<?php for($m=0;$m<count($form['sections'][$j]['labels'][$l]['options']);$m++)  { ?>
                                   <label><?php echo $form['sections'][$j]['labels'][$l]['options'][$m]['optionText'];?></label>
                                   <input type = "radio" value = "" name = ""/>



                           <?php } ?>
            			<?php }elseif($form['sections'][$j]['labels'][$l]['widgetType'] == "text"){
                         
            			 ?>
            				<label><?php echo $form['sections'][$j]['labels'][$l]['labelText'];?></label><br/>
            				
                                          <input type = "text" value = "" name = "" class = "form-control"/>
                           <?php } ?>


                     

            		</div>
            	<?php } ?>
            	<div class = "row">
            		<div class = "col-md-6">
            			<input type="button" name="" class = "btn btn-success" value = "submit"/>
            		</div>
            	</div>
            	</div>

            </section>
 		<?php } ?>

 	<?php }?>	
 </div>


</section>			
</div>
<script type="text/javascript">
  
</script>