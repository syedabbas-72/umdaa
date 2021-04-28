<!DOCTYPE html>
<html lang="en">
<head>
   
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>UMDAA</title>
</head>
<body>
  <table cellpadding="0" cellspacing="0" style="width:1000px; font-family: segoe ui; color: #333" align="center">
   
   <tr>
      <td style="border-bottom:1px solid #ccc; padding:15px 10px; width: 100%; " colspan="2">
        <table cellspacing="0" cellpadding="0" style="width: 100%">
          <tr>
           
            

            <td style="width:75%; text-align: center;padding: 1px 2px 1px 5px;">
              <h3 style="font-size: 30px; padding:0px; margin: 0px;">INFORMED CONSENT</h3>
            </td>
          </tr> 
        </table>
      </td>
    </tr>
     <tr style="border-bottom: 1px solid #ccc; height: 10px;"> </tr>
</table>
	<div style="padding-left:0pt;margin-top:20px;">
	<p>PATIENTS NAME:&nbsp;<b><?php echo $appointment->title.".".$appointment->pf_name." ".$appointment->pm_name." ".$appointment->pl_name; ?></b></p>
	<br/>
	<p>UMR NO:&nbsp; <b><?php echo $appointment->umr_no; ?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Age:&nbsp; <b><?php echo $appointment->age." ".$appointment->age_unit."s"; ?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sex:&nbsp; <b><?php echo $appointment->p_gender; ?></b></p>
	<br/>
	<p>I/we hereby declare and confirm that I/we have been given detailed oral explanation by:</p>
	<br/>
	<p><b>Dr.&nbsp;<?php echo $appointment->saluation." ".$appointment->first_name." ".$appointment->last_name; ?></b>  in language I/we best understand</p>
	<br/>
	<p>My doctors have recommended the following operation or procedure or treatment</p>
	<br/>
	<p>
	(BRIEF EXPLANATION)<br/>
	<b><?php echo $Consentform_val->brief;?></b>
	</p>
	<br/>
	<p>
	and the following type of anesthesia: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b><?php echo $Consentform_val->anesthesia;?></b>
	</p>
	<br/>
	<p>
	The operations or procedures will be performed by the doctor named below (or, in the event the doctor is unable to perform or complete <br/> the procedure, a qualified substitute doctor), together with associates and assistants, including anesthesiologists, pathologists, and radiologists <br/><br/> from the medical staff of <b><?php echo $appointment->clinic_name;?></b> (name of hospital)
	</p>
	<br/>
	<p>
	I/we have been explained the procedure in detail, in particular :
	</p>
	<br/>
	<p>
	INTENDED BENEFITS:<br/>
	<b><?php echo $Consentform_val->benefits;?></b>
	</p>
	<br/>
	<p>SERIOUS AND FREQUENTLY OCCURING RISKS,<br/>
	<b><?php echo $Consentform_val->complications;?></b>
	<br/>
	including any extra procedure,which may be necessary during the procedure.
	<br/>
	I/we have been explained in detail what the procedure is likely to involve,the benefits and risks of any avaliable alternative<br/> treatments(including no treatment)
	</p>
	<br/>
	<p>
	ALTERNATIVE TREATMENT:
	<b><?php echo $Consentform_val->alternative;?></b>
	</p>
	<br/>
	<p>The risk associated with blood transfusion, alternatives to transfusion of blood and blood products should <br/>I need any blood transfusion is explained to me in detail.
	<br/><br/>
	I /we are also made aware that in addition to these above-mentioned risks, there are other risks also which have been discussed<br/> with me/us but are not listed above. I/we understand the purpose and all benefits of the proposed treatment and/or special procedure, <br/>that no guarantee has been made to me/us as to the results that may be obtained, and that the concerned doctor has offered to answer<br/> any of our questions about the proposed surgery/procedure/treatment.
	</p>
	<br/>
	<p>
	Due to the following specific medical condition(s): &nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo $Consentform_val->consent_form_title; ?></b>, <br>
	additional risks and/ or complications of the operation or procedure and anesthesia is explained to me/us. 
	</p>
	<br/>
	<p>I/we agree
	<br/>
	
	<ul style="padding-left:25pt;">
	<li>the use of anesthesia and /or sedation/analgesia as required</li>
	<li>the procedure and course of treatment as described above</li>
	<li>to blood transfusion if necessary</li>
	<li>any tissue removed during this procedure could be stored and used for medical research purpose</li>
	<li>any procedure in addition to those described in this form will only be carried out if it is necessary to save my life or to <br/>prevent serious harm to my life</li>
	<li>I/we also authorize the hospital and treating physician to photograph, video and or use any other mediums which result in <br/>permanent documentation of my image for medical, scientific or educational purpose, provided my identity is not revealed to them.</li>
	</ul>
	</p>
	<br/>
	<p>
	<div style="float:left">
		Patient or legally authorized representative
		<br/>
		<br/>
		---------------------------------------------------------------
	</div>
	<div style="float:right;padding-right:0px;">
		Patients’ relative                                                       
		<br/>
		<br/>
		1. --------------------------------------------------------------
		<br/>
		2. --------------------------------------------------------------
		
		<br/>
		Relationship ---------------------------------------------------------
		</div>
	</p>
	<p style="padding-top:50pt;">
			Date:&nbsp; -------------------------------------------------------&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Time:&nbsp; -------------------------------------------------------
		</p>
	<p style="padding-top:50pt;">
	Interpreter responsible for explaining the procedure and special treatment:
	<br/><br/>
	____________________________________________________________________________________________________<br/><br/>____________________________________________________________________________________________________
	</p>
	<br/>
	<p>
	<b>Physician certification</b>
	<br/>
	I certify that I have discussed the procedure described in the consent form with the patient or patient’s legal representative,<br/> the risks and benefits of procedure, reasonable adverse effects that may occur, alternate methods of treatment their risks and benefits.<br/> I encouraged patients and their relatives to ask questions and that all questions were answered.
	<br/><br/><br/>
	Signature of physician: -------------------------------------------------
	<br/>
	<br/>
	Name of physician: -------------------------------------------------------
	<br/>
	<br/>
	Date: ---------------------------&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Time: ------------------------------

	</p>
	</div>
            
			

</body> 
</html>