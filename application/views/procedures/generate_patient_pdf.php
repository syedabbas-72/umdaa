<!DOCTYPE html>

<html lang="en">

<body>

<div style="padding-left:0pt;margin-top:20px;">
	<p><b>PATIENTS NAME:</b></p><br/>
	 <table cellpadding="0" cellspacing="0" style="width:1000px; font-size:60px; color: #333" align="center">
	 	<tr>
	 		<td style="border-bottom:0px solid #ccc; padding:15px 10px; width: 100%; font-size:60px;"><b>Name:</b> <?php echo $patient_list->first_name ;?></td>
	 		<td style="border-bottom:0px solid #ccc; padding:15px 10px; width: 100%;font-size:60px; "><b>age:</b> <?php echo $patient_list->age ;?></td>
	 		<td style="border-bottom:0px solid #ccc; padding:15px 10px; width: 100%;font-size:60px; "><b>sex:</b> <?php echo $patient_list->gender ;?></td>
	 	</tr>
	 	<tr>
	 		<td style="border-bottom:0px solid #ccc; padding:15px 10px; width: 100%; font-size:60px;" ><b>UMR no:</b> <?php echo $patient_list->umr_no ;?></td>	 	
	 		<td style="border-bottom:0px solid #ccc; padding:15px 10px; width: 100%;font-size:60px; "><b>Date:</b> <?php echo date("d-m-Y") ;?></td>
	 		<td style="border-bottom:0px solid #ccc; padding:15px 10px; width: 100%;font-size:60px; "><b>emergency/elective:</b> <?php echo $patient_list->first_name ;?></td>
	 	</tr>
	 </table>
	 <br/>
	 <div style="width:90%;border-bottom:1px dashed black;"></div>
	<table cellpadding="0" cellspacing="0" style="width:1000px; font-size:60px; color: #333" align="center">
	 	<tr>
	 		<td style="border-bottom:0px solid #ccc; padding:15px 10px; width: 100%; font-size:40px;"><b>Surgeon:</b> <?php echo $patient_procedure->surgeon ;?></td>
	 		<td style="border-bottom:0px solid #ccc; padding:15px 10px; width: 100%;font-size:40px; "><b>anesthetist:</b><?php echo $patient_procedure->anesthetist ;?></td>	 		
	 	</tr>
	 	<tr>
	 		<td style="border-bottom:0px solid #ccc; padding:15px 10px; width: 100%; font-size:40px;"><b>Assisting surgeon:</b><?php echo $patient_procedure->assisting_surgeon ;?></td>
	 		<td style="border-bottom:0px solid #ccc; padding:15px 10px; width: 100%;font-size:40px; "><b>type of anesthesia: </b><?php echo $patient_procedure->type_of_anesthesia ;?></td>	 		
	 	</tr>
	 	<tr>
	 		<td style="border-bottom:0px solid #ccc; padding:15px 10px; width: 100%; font-size:40px;" colspan="2"><b>Assisting nurse</b><?php echo $patient_procedure->assisting_nurse ;?></td>
	 			 		
	 	</tr>
	 </table>
	 <div style="width:90%;border-bottom:1px dashed black;"></div>
	 <p><b>PREOPERATIVE DIAGNOSIS:</b></p><br/>
	 <div style="width:90%;border:0px solid black;height:40px;">
	 	<?php echo $patient_procedure->preoperative_diagnosis ;?>
	 </div>
	 <p><b>POSTOPERATIVE DIAGNOSIS:</b></p><br/>
	 <div style="width:90%;border:0px solid black;height:30px;">
	 	<?php echo $patient_procedure->postoperative_diagnosis ;?>
	 </div>
	 <p><b>INDICATION:</b></p><br/>
	 <?php echo $patient_procedure->indication ;?>
	 <p><b>POSITION: </b><?php echo $patient_procedure->position ;?></p><br/>
	 
	 <div style="width:90%;border-bottom:1px dashed black;"></div>

</div>

<!-- <h2><?php echo $medical_procedure; ?></h2> -->

<!-- <div style="background-color:#ddccff; padding:0pt; border: 1px solid #555555;"> -->
	<div style=" padding:0pt; border: 0px solid #555555;width:90%">

<ol class="lista" style="text-align: justify;list-style-type: upper-roman;">

<li><?php echo $patient_procedure->medical_procedure; ?></li>



</ol>

</div>





</body>

</html>