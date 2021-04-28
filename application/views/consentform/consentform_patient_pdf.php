<p style="text-align: center;"><span style="font-size: 22px; line-height: 107%;">INFORMED CONSENT</span></p>
<p style="text-align: center;"><span style="font-size: 16px; line-height: 107%;"><?php echo $Consentform_val->consent_form_title; ?></span></p>
<hr class="linebreak">
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">PATIENTS NAME: <b><?php if($appointment->title =="") { echo $appointment->pf_name." ".$appointment->pm_name." ".$appointment->pl_name; } else { echo $appointment->title.".".$appointment->pf_name." ".$appointment->pm_name." ".$appointment->pl_name; } ?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';font-weight:bold">UMR NO. <b><?php echo $appointment->umr_no; ?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Age :<b><?php echo $appointment->age." ".$appointment->age_unit; ?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Sex: <?php echo $appointment->p_gender; ?></span></p>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">I/we hereby declare and confirm that I/we have been given detailed oral explanation by:</span></p>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';"><b>Dr.&nbsp;<?php echo $appointment->first_name." ".$appointment->last_name; ?></b>&nbsp;&nbsp;&nbsp;&nbsp;in language I/we best understand</span></p>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">My doctors have recommended the following operation or procedure or </span><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">treatment</span></p>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">(BRIEF EXPLANATION) </span></p>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';"><b style="margin-top: 10px"><?php echo $Consentform_val->brief;?></b></span></p>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman'; color: black;">and the following type of anesthesia: &nbsp;&nbsp; <b><?php echo $Consentform_val->anesthesia;?></b>&nbsp; </span></p>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman'; color: black;">The operations or procedures will be performed by the doctor named below (or, in the event the doctor is unable to perform or complete the procedure, a qualified substitute doctor), together with associates and assistants, including anesthesiologists, pathologists, and radiologists from the medical staff of &nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo $appointment->clinic_name;?></b>(name of hospital)&nbsp;&nbsp;&nbsp;&nbsp;</span></p>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman'; color: black;">&nbsp;I/we have been explained the procedure in detail, in particular :</span></p>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman'; color: black;">INTENDED BENEFITS:&nbsp;&nbsp;<b><?php echo $Consentform_val->benefits;?></b></span></p>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman'; color: black;">SERIOUS AND FREQUENTLY OCCURING RISKS,</span></p>
<table>
<tbody>
<tr>
<td style="border: .75pt solid black; vertical-align: top; background: white;" width="608">
<table width="100%">
<tbody>
<tr>
<td>
<p><b><?php echo $Consentform_val->complications;?></b></p>
</td>
</tr>
</tbody>
</table>
&nbsp;</td>
</tr>
</tbody>
</table>

<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman'; color: black;">including any extra procedure,which may be necessary during the procedure.</span></p>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman'; color: black;">I/we have been explained in detail what the procedure is likely to involve,the benefits and risks of any avaliable alternative treatments(including no treatment)</span></p>
<table>
<tbody>
<tr>
<td style="border: .75pt solid black; vertical-align: top; background: white;" width="608">
<table width="100%">
<tbody>
<tr>
<td>
<p>ALTERNATIVE TREATMENT: <b><?php echo $Consentform_val->alternative;?></b></p>
</td>
</tr>
</tbody>
</table>
&nbsp;</td>
</tr>
</tbody>
</table>
<br>
<br><br><br>

<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman'; color: black;">T</span><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">he risk associated with blood transfusion, alternatives to transfusion of blood and blood products should I need any blood transfusion is explained to me in detail.</span></p>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">I /we are also made aware that in addition to these above-mentioned risks, there are other risks also which have been discussed with me/us but are not listed above. I/we understand the purpose and all benefits of the proposed treatment and/or special procedure, that no guarantee has been made to me/us as to the results that may be obtained, and that the concerned doctor has offered to answer any of our questions about the proposed surgery/procedure/treatment.</span></p>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">Due to the following specific medical condition(s): <b><?php echo $Consentform_val->medical_conditions; ?></b>, additional risks and/ or complications of the operation or procedure and anesthesia is explained to me/us. </span></p>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">I/we agree </span></p>
<ul style="margin-top: 0in;">
<li><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">the use of anesthesia and /or sedation/analgesia as required</span></li>
<li><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">the procedure and course of treatment as described above</span></li>
<li><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">to blood transfusion if necessary</span></li>
<li><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">any tissue removed during this procedure could be stored and used for medical research purpose</span></li>
<li><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">any procedure in addition to those described in this form will only be carried out if it is necessary to save my life or to prevent serious harm to my life</span></li>
<li><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">I/we also authorize the hospital and treating physician to photograph, video and or use any other mediums which result in permanent documentation of my image for medical, scientific or educational purpose, provided my identity is not revealed to them.</span></li>
</ul>
<p style="margin-left: .5in;"><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">&nbsp;</span></p>
<p><span style="font-size: 12.0pt;width:400px;float: left;text-align: left; line-height: 107%; font-family: 'Times New Roman';">Patient or legally authorized representative&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="font-size: 12.0pt;width:200px;float: left; text-align:right;line-height: 107%; font-family: 'Times New Roman';"> Patient's relative</span></p>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">&nbsp;</span></p>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">--------------------------------------------------- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp; 1.----------------------------------------------</span></p>

<ol start="2">
<li style="margin-left: 4.1in;"> <span style="font-size: 12.0pt;margin-top: 10px;margin-bottom: 10px line-height: 107%; font-family: 'Times New Roman';">--------------------------------------------- Relationship-----------------------------</span></li>
</ol>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">&nbsp;</span></p>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">Date: -------------------------------------&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Time: -------------------------------------------------</span></p>
<p style="border: none; padding: 0in;"><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">Interpreter responsible for explaining the procedure and special treatment:</span></p>
<p style="border: none; padding: 0in;"><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">&nbsp;</span></p>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">&nbsp;</span></p><br>
<br>
<br>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">Physician certification</span></p>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">I certify that I have discussed the procedure described in the consent form with the patient or patient&rsquo;s legal representative, the risks and benefits of procedure, reasonable adverse effects that may occur, alternate methods of treatment their risks and benefits. I encouraged patients and their relatives to ask questions and that all questions were answered.</span></p>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">Signature of physician: -------------------------------------------------</span></p>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">Name of physician: -------------------------------------------------------</span></p>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">Date: ---------------------------&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Time:------------------------------</span></p>
<p><span style="font-size: 12.0pt; line-height: 107%; font-family: 'Times New Roman';">&nbsp;</span></p>
