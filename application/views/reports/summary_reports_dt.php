   <div class="row page-header no-background no-shadow margin-b-0">
        <div class="col-lg-6 align-self-center">
          
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">UMDAA CLINICS</a></li>
            <li class="breadcrumb-item active">PATIENTS</li>
          </ol>
        </div>

        <div class="col-lg-6 align-self-center text-right">
          <form method="post" action="<?php echo base_url('SummaryReports/view_summary_report/'.$appointment_id);?>"  autocomplete="off" enctype="multipart/form-data">
    <input type="submit" name="getpdf" value="PDF DOWNLOAD">
   </form>
        </div>
    </div>
<section class="main-content">
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
               
                    <table cellpadding="0" cellspacing="0" style="width:1000px; font-family: segoe ui; color: #333" align="center">
   
   <tr>
      <td style="border-bottom:1px solid #ccc; padding:15px 10px; width: 100%; " colspan="2">
        <table cellspacing="0" cellpadding="0" style="width: 100%">
          <tr>
           
            <td style="width: 25%;text-align: left;"><img src="<?php echo base_url('uploads/clinic_logos/'.$appointments->clinic_logo);?>"/></td>

            <td style="width:75%; text-align: center;padding: 1px 2px 1px 5px;">
              <h2 style="font-size: 30px; padding:0px; margin: 0px;"><?php echo $appointments->clinic_name; ?></h2>
           
            </td>
           
           <!-- <td style="width: 25%;text-align: right;"><img src="<?php //echo base_url('uploads/qrcodes/patients/'.$appointments->qrcode);?>"> </td>
          </tr> -->
        </table>
      </td>
    </tr>

     <tr>
      <td style=" padding:15px 10px 20px 10px;width: 100%;">
        
        <table cellpadding="0" cellspacing="0" style="width:1000px; font-family: segoe ui;  padding: 5px; color: #333" align="center">
          <tr><td>
        <table cellpadding="0" cellspacing="0"  style="margin: 0px; padding: 0px; ">
          <tr>

            <td style="width:25%;text-align: left;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">UMR No</span>
            </td>
            <td style="width:25%;text-align: center;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">:</span>
            </td>
            <td style="width:25%; text-align: left;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">  <?php echo $appointments->umr_no; ?> </span>
            </td>
           
          </tr>
          <tr>

            <td style="width:25%;text-align: left;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">Patient Name</span>
            </td>
            <td style="width:25%;text-align: center;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">:</span>
            </td>
            <td style="width:25%; text-align: left;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">  <?php echo $appointments->pname; ?> </span>
            </td>
           
          </tr>
          <tr>

            <td style="width:25%;text-align: left;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">Address</span>
            </td>
            <td style="width:25%;text-align: center;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">:</span>
            </td>
            <td style="width:25%; text-align: left;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">  <?php echo $appointments->address_line; ?> </span>
            </td>
           
          </tr>
          <tr>

            <td style="width:25%;text-align: left;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">Consultation No</span>
            </td>
            <td style="width:25%;text-align: center;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">:</span>
            </td>
            <td style="width:25%; text-align: left;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">  </span>
            </td>
           
          </tr>
          <tr>

            <td style="width:25%;text-align: left;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">Consultant Doctor</span>
            </td>
            <td style="width:25%;text-align: center;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">:</span>
            </td>
            <td style="width:25%; text-align: left;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">  <?php echo $appointments->salutation.' '. $appointments->dname; ?> </span>
            </td>
           
          </tr>

        </table>
      </td>
       <td style=" padding:15px 10px 20px 10px;width: 50%;vertical-align: top;">
        <table cellpadding="0" cellspacing="0"  style="margin: 0px; padding: 0px;vertical-align: top; ">
          <tr>
            <td style="width:25%;text-align: left;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">Phone No</span>
            </td>
            <td style="width:25%;text-align: center;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">:</span>
            </td>
            <td style="width:25%; text-align: left;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">  <?php echo $appointments->clinic_phone; ?> </span>
            </td>
          </tr>
          <tr>
            <td style="width:25%;text-align: left;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">Age / Gender </span>
            </td>
            <td style="width:25%;text-align: center;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">:</span>
            </td>
            <td style="width:25%; text-align: left;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">  <?php echo $appointments->age .' / '. $appointments->gender; ?> </span>
            </td>
          </tr>
              <tr>
            <td style="width:25%;text-align: left;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">Bill No </span>
            </td>
            <td style="width:25%;text-align: center;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">:</span>
            </td>
            <td style="width:25%; text-align: left;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">   </span>
            </td>
          </tr>
              <tr>
            <td style="width:25%;text-align: left;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">Bill Date </span>
            </td>
            <td style="width:25%;text-align: center;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">:</span>
            </td>
            <td style="width:25%; text-align: left;">
              <span style="color:#000; font-weight: 600; line-height: 25px;">   </span>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  <!-- Table End -->
</table>
</td>
</tr>
<tr>
  <td style="height: 40px; font-size: 15px;">
    <table cellpadding="0" cellspacing="0" style="width:1000px; font-family: segoe ui; padding: 5px; color: #333" align="center">
    <tr>
      <td style="width:50%;text-align: left;">
        <span style="color:#000; font-weight: 600; height: 40px;">Name : <?php echo $appointments->pname; ?></span><br>
        <span style="color:#000; font-weight: 600;height: 40px;">UMR NO : <?php echo $appointments->umr_no; ?></span>
      </td>
      <td style="width:50%;text-align: center;h">
        <span style="color:#000; font-weight: 600;eight: 40px;">Date : <?php echo date('Y-m-d'); ?></span><br>
        <span style="color:#000; font-weight: 600;eight: 40px;"># Visit  : <?php echo $visit; ?> </span>
      </td>
    </tr>
 
    </table>
  </td>
</tr>
<tr>
  <td>
    <table cellpadding="0" cellspacing="0" style="width:1000px; font-family: segoe ui; padding: 5px; color: #333" align="center">
      <tr>
        <td colspan="2" style="height: 40px;width:1000px;"><span style="color:#000; font-weight: 600">
          <span style="font-weight: bold;">BP</span> : <?php echo $vital_sign['BP(mmHg)*']; ?> mmHG</span> &nbsp;&nbsp;&nbsp;&nbsp;
        <span  style="color:#000; font-weight: 600"><span style="font-weight: bold;">Height</span> : <?php echo $vital_sign['HEIGHT']; ?> cm </span>&nbsp;&nbsp; &nbsp;&nbsp;
        <span  style="color:#000; font-weight: 600"><span style="font-weight: bold;">Weight </span> : <?php echo $vital_sign['WEIGHT']; ?> Kg</span>&nbsp;&nbsp;&nbsp;&nbsp;
        <span  style="color:#000; font-weight: 600"><span style="font-weight: bold;"> BMI </span> : <?php echo $vital_sign['BMI']; ?> Kg/m2 </span>
      </td>
    </tr>
     <tr>
        <td colspan="2" style="height: 40px;width:1000px;"><span style="color:#000; font-weight: 600;font-style: italic;font-size: 16px;"> Diagnosis : </span>
          <?php foreach ($patient_clinical_diagnosis as $key => $value) {
            echo ' <span  style="color:#000; font-weight: 600">'.$value->clinical_diagnosis.' </span>';
          }
         ?>
         
      </td>
    </tr>
    <tr>
  <td colspan="2" style="height: 40px;width: 100%;">
    <span style="color:#000; font-weight: 600;font-size: 16px;"> RX </span>
  </td>
    </tr>
      </table></td></tr>
      <tr>
        <td>
          <table cellpadding="0" cellspacing="0" style="width:1000px; font-family: segoe ui; padding: 5px; color: #333" align="center">
            <tr>

              <td style="width:35%;text-align: left;height: 40px;"><span style="color:#000; font-weight: 600;font-size: 16px; width: 35%;" > Medicine </span></td>
             <td style="width:35%;text-align: left;height: 40px;"><span style="color:#000; font-weight: 600;font-size: 16px; width: 35%;"> Dosage </span></td>
             <td style="width:35%;text-align: left;height: 40px;"><span style="color:#000; font-weight: 600;font-size: 16px; width: 35%;"> Timing - Freq - Duration </span></td>
            </tr>
            <?php 
            $i=1;
            foreach ($patient_prescription as $key => $value) { ?>
            
            <tr style="height: 55px">
             <td style="width:35%;text-align: left;height: 40px;"><span style="color:#000; font-weight: 600;font-size: 16px; width:25%;text-align: left;"><?php echo $i; ?> <?php echo $value->trade_name; ?> </span>

              </td>
              <td style="width:35%;text-align: left; height: 40px;"><span style="color:#000; font-weight: 600;font-size: 16px; width:25%;text-align: left;">
                <?php $M = 0;
                      $N = 0;
                      $A = 0;
                        $dose = 1;
                        $Mday = '';
                    if($value->day_schedule=='M'){
                      $M = $dose;
                      $Mday = $dose.' - '.'Morning'; 
                    }else if($value->day_schedule=='N'){
                      $N = $dose;
                       $Mday = $dose.' - '.'Night';
                    }else if($value->day_schedule=='A'){
                        $A = $dose;
                        $Mday = $dose.' - '.'Afternoon';
                    }

                echo $M.' - '.$A.' - '.$N; ?>
                <?php //echo $value->dose_course; ?> </span></td>
               <td style="width:35%;text-align: left;height: 40px;"><span style="color:#000; font-weight: 600;font-size: 16px; width:25%;text-align: left;"><?php echo $value->dose_course; ?> Day's  </span></td>
            </tr>
             <tr> <td colspan="3" style="width:100%;text-align: left;height: 40px;"><span style="font-style: italic;font-size: 16px;width:25%;text-align: left;">Composition : </span> 
              <span><?php echo $value->composition; ?></span>
             </td></tr>
             <tr>
             <td colspan="3" style="width:100%;text-align: left;height: 40px;"><span style="font-style: italic;font-size: 16px; width:25%;text-align: left;">Timing : </span> 
              <span><?php echo $Mday; ?> </span>
             </td>
           </tr>
           <tr style="border-bottom: 1px solid #ccc;"></tr>
          <?php $i++; } ?>
          </table>
        </td>
      </tr>

<tr>
  <td>
    <table cellpadding="0" cellspacing="0" style="width:1000px; font-family: segoe ui; padding: 5px; color: #333" align="center">
      <tr>
        <td style="width:100%;text-align: left;height: 40px;">
        <span style="font-style: italic;font-size: 16px;font-weight: bold;"></span>
        <span style="">Advice : </span>

      </td>
    </tr>
      <tr>
        <td style="width:100%;text-align: left; height: 40px;">
        <span style="font-style: italic;font-size: 16px; font-weight: bold;">Test Prescribed : </span>
        <span style="">  </span>
        
      </td>
    </tr>
   
    </table>
  </td>
</tr>

  <tr>
        <td>
           <table cellpadding="0" cellspacing="0" style="width:1000px;width: 100%; font-family: segoe ui; padding: 5px; color: #333" align="center">
            <tr>
              <td style="width: 70%;text-align: left;"><span style="color:#000; font-weight: 600;font-size: 16px;"><img src="<?php echo base_url('uploads/qrcodes/patients/'.$appointments->qrcode);?>"> </span></td>
              <td style="width: 30%;text-align: center;"><span style="color:#000; font-weight: bold; line-height: 25px;">  <?php echo $appointments->salutation.' . '. $appointments->dname; ?> </span></td>
            </tr>
          </table>
        
      </td>
      <td></td>
    </tr>
     <tr style="border-bottom: 1px solid #ccc; height: 10px;"> </tr>
</table>

            </div>
        </div>
    </div>
</div>

</section>

 <script>
  $(document).ready(function () {
      $('#userlist').dataTable();
  });
  </script>
  <script>
  function doconfirm()
    {
        if(confirm("Delete selected messages ?")){
            return true;
        }else{
            return false;  
        } 
   }
  </script>



 