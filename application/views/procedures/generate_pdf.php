<!DOCTYPE html>

<html lang="en">

<body>

<div style="padding-left:0pt;margin-top:20px;">
	<p><b>PATIENTS NAME:</b></p><br/>
	 <table cellpadding="0" cellspacing="0" style="width:1000px; font-size:60px; color: #333" align="center">
	 	<tr>
	 		<td style="border-bottom:0px solid #ccc; padding:15px 10px; width: 100%; font-size:60px;"><b>Name:</b></td>
	 		<td style="border-bottom:0px solid #ccc; padding:15px 10px; width: 100%;font-size:60px; "><b>age:</b></td>
	 		<td style="border-bottom:0px solid #ccc; padding:15px 10px; width: 100%;font-size:60px; "><b>sex:</b></td>
	 	</tr>
	 	<tr>
	 		<td style="border-bottom:0px solid #ccc; padding:15px 10px; width: 100%; font-size:60px;"><b>w/o:</b></td>
	 		<td style="border-bottom:0px solid #ccc; padding:15px 10px; width: 100%; font-size:60px;" colspan="2"><b>UMR no:</b></td>	 		
	 	</tr>
	 	<tr>
	 		<td style="border-bottom:0px solid #ccc; padding:15px 10px; width: 100%;font-size:60px; " colspan="2"><b>Date:</b></td>
	 		<td style="border-bottom:0px solid #ccc; padding:15px 10px; width: 100%;font-size:60px; "><b>emergency/elective:</b></td>
	 	</tr>
	 </table>
	 <br/>
	 <div style="width:90%;border-bottom:1px dashed black;"></div>
	<table cellpadding="0" cellspacing="0" style="width:1000px; font-size:60px; color: #333" align="center">
	 	<tr>
	 		<td style="border-bottom:0px solid #ccc; padding:15px 10px; width: 100%; font-size:40px;"><b>Surgeon:</b></td>
	 		<td style="border-bottom:0px solid #ccc; padding:15px 10px; width: 100%;font-size:40px; "><b>anesthetist:</b></td>	 		
	 	</tr>
	 	<tr>
	 		<td style="border-bottom:0px solid #ccc; padding:15px 10px; width: 100%; font-size:40px;"><b>Assisting surgeon:</b></td>
	 		<td style="border-bottom:0px solid #ccc; padding:15px 10px; width: 100%;font-size:40px; "><b>type of anesthesia: SÁ/EA/GA</b></td>	 		
	 	</tr>
	 	<tr>
	 		<td style="border-bottom:0px solid #ccc; padding:15px 10px; width: 100%; font-size:40px;" colspan="2"><b>Assisting nurse</b></td>
	 			 		
	 	</tr>
	 </table>
	 <div style="width:90%;border-bottom:1px dashed black;"></div>
	 <p><b>PATIENTS NAME:</b></p><br/>
	 <div style="width:90%;border:0px solid black;height:40px;"></div>
	 <p><b>POSTOPERATIVE DIAGNOSIS:</b></p><br/>
	 <div style="width:90%;border:0px solid black;height:30px;"></div>
	 <p><b>INDICATION:</b></p><br/>
	 <p><b>POSITION: </b>dorsal supine</p><br/>
	 <div style="width:90%;border-bottom:1px dashed black;"></div>

</div>

<h2><?php echo $medical_procedure; ?></h2>

<!-- <div style="background-color:#ddccff; padding:0pt; border: 1px solid #555555;"> -->
	<div style=" padding:0pt; border: 0px solid #555555;width:90%">

<ol class="lista" style="text-align: justify;list-style-type: upper-roman;">

<li><?php echo $procedure_description; ?></li>



</ol>

</div>





</body>

</html>