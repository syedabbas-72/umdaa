<!DOCTYPE html>
<html lang="en">
<head>
	<!-- Meta information -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
</head>
<?php

?>
<body style="font-family: 'Roboto', sans-serif;">
<?php if($pdf_settings->header != 1){ ?>
    <htmlpageheader name="firstpageheader">
        <table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
            <tr>
                <td style="width:40%">
                    <!-- <?php echo base_url(); ?>uploads/clinic_logos/<?php echo $clinic_logo." "; ?>  -->
                    <img style="height:50px;" alt="" src="<?php echo base_url(); ?>uploads/clinic_logos/<?php echo $clinic_information->clinic_logo." "; ?>">
                </td>
                <td style="width: 25%"></td>
                <td style="width:35%;text-align: right;">
                    <span style="font-weight: bold; font-size: 14px">Address</span><br><span style="font-weight: 700;font-size: 14px"><?php echo $clinic_information->address; ?></span><br><span style=" font-weight: bold;font-size: 14px">Phone:</span><span style="font-weight: 700; font-size: 14px"><?php echo $clinic_information->clinic_phone; ?></span>
                </td>
            </tr>
        </table>
        <hr>
    </htmlpageheader>
    <htmlpageheader name="otherheader">
        <table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
            <tr>
                <td style="width:40%">
                    <!-- <?php echo base_url(); ?>uploads/clinic_logos/<?php echo $clinic_logo." "; ?>  -->
                    <img style="width:50%" alt="" src="<?php echo base_url(); ?>uploads/clinic_logos/<?php echo $clinic_information->clinic_logo." "; ?>">
                </td>
                <td style="width: 25%"></td>
                <td style="width:35%;text-align: right;">
                    <span style="font-weight: bold; font-size: 14px">Address</span><br><span style="font-weight: 700;font-size: 14px"><?php echo $clinic_information->address; ?></span><br><span style=" font-weight: bold;font-size: 14px">Phone:</span><span style="font-weight: 700; font-size: 14px"><?php echo $clinic_information->clinic_phone; ?></span>
                </td>
            </tr>
        </table>
        <hr>
    </htmlpageheader>
<?php }elseif($pdf_settings->header == 1){
    $style = "margin-bottom: ".$pdf_settings->header_height."px !important;";
    ?>
    <htmlpageheader name="firstpageheader">
        <table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;<?=$style?>">
            <tr>
                <td>&nbsp;</td>
            </tr>
        </table>
        <hr>
    </htmlpageheader>
    <htmlpageheader name="otherheader">
        <table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;<?=$style?>">
            <tr>
                <td>&nbsp;</td>
            </tr>
        </table>
        <hr>
    </htmlpageheader>
    <?php
} ?>	

<section class="main-content">
    <div class="">
        <div class="">
            <div class="">
                <div class="card-head card-default" style="padding:5px 10px; ">
                    <table cellspacing="0" cellpadding="0" width="100%" class="headerTable">
                        <tr>
                            <td style="width: 25%">
                                <header style="font-weight: bold; font-size: 13px; padding-left: 10%">
                                    MR. <?php echo ucwords(strtoupper($billing_info->guest_name)); ?><br>
                                    <small><?php echo DataCrypt($billing_info->guest_mobile,'decrypt'); ?></small>        
                                </header>
                            </td>
                            <?php if($billing_info->doctor_id != 0){ ?>
                                <td style="width: 25%">
                                    <?php if($billing_info->doctor_id != '' || $billing_info->doctor_id != 0){ ?>
                                    <header>
                                        <?php echo "Dr. ".ucwords($billing_info->doc_first_name." ".$billing_info->doc_last_name); ?><br>
                                        <small><?php echo ucwords($billing_info->department_name); ?></small>
                                    </header>    
                                    <?php } ?>
                                </td>
                                <td style="width:25%">
                                    <?php if($billing_info->appointment_id != '' || $billing_info->appointment_id != 0){ ?>
                                    <header>
                                        Appointment On <?php echo date('d M Y', strtotime($billing_info->appointment_date)); ?><br>
                                        <small><?php echo date('H:i A', strtotime($billing_info->appointment_time_slot)); ?></small>
                                    </header>
                                    <?php } ?>
                                </td>
                            <?php } ?>   
                        </tr>
                    </table>
                </div>
                <hr>
                <div class="card-body"> 

                    <div class="col-md-12" style="font-weight: bold; font-size: 15px; padding: 10px 0px; line-height: 22px; text-align: center;">
                        <?=$test_name?>
                        <hr>
                    </div>

                    <table class="table resultTable noBdr" style="width: 100%" >
                        <?php if($template_type == 'Excel'){ ?>
                        <thead>
                            <tr style="background: #cccccc; color: #ffffff;">
                                <th style="width: 10%; text-align: center; text-transform: uppercase; padding:5px 10px; font-size: 12px;">S.No.</th>
                                <th style="width: 35%; text-transform: uppercase; padding:5px 10px; font-size: 12px;">Investigation</th>
                                <th style="width: 20%; text-align: center; text-transform: uppercase; padding:5px 10px; font-size: 12px;">Result</th>
                                <th style="width: 35%; text-transform: uppercase; padding:5px 10px; font-size: 12px;">Information</th>
                            </tr>
                        </thead>
                        <tbody style="font-family: 'Roboto', sans-serif;">
                            <?php
                            $i = 1;
                            foreach($lab_results as $result) {
                                $low = (float)$result['low_range'];
                                $high = (float)$result['high_range'];
                                $value = (float)$result['value'];
                            ?>
                                <tr>
                                    <td style="text-align: right; font-size: 12px; vertical-align:top;"><?php echo $i++; ?>.</td>
                                    <td style="vertical-align: top">
                                        <span style="font-size:12px; font-weight: bold"><b><?php echo strtoupper($result['investigation']); ?></b></span><br>
                                        <small style="font-style: italic; font-size: 11px; color: #333;"><?=$result['method']?></small>
                                    </td>
                                    <td style="font-size: 12px; vertical-align: top; text-align: center;">
                                        <?php
                                        if($value != "" || $value != null){
                                            if($low != 0 || $high != 0){
                                                if($value < $low || $value > $high){
                                                    echo '<span class="abnormal" style="font-weight:bold">'.$value.'</span>';
                                                }else{
                                                    echo '<span class="normal">'.$value.'</span>';
                                                }
                                            }else{
                                                echo '<span class="normal">'.$value.'</span>';
                                            }
                                            // echo '<span class="units">&nbsp;'.$result['units'].'</span>';
                                        }else{
                                            echo '<span class="normal"> -- </span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="others" style="font-size: 12px; vertical-align: top">
                                        <!-- <?php echo str_replace(';', '<br>', $result['remarks']); ?>     -->
                                        <?php 
                                        // echo $billing_invoice->gender;
                                    if($billing_invoice->gender == 'Male' || $billing_invoice->gender == 'male' ){

                                        $getdata =$this->db->select('*')->from('clinic_investigations')
                                        ->where('investigation_id =',$result['investigation_id'])
                                        ->where('clinic_id =',$clinic_id)
                                        ->get()->row();

                                        $getdataa =$this->db->select('*')->from('clinic_investigation_range')
                                        ->where('clinic_investigation_id =',$getdata->clinic_investigation_id)
                                        ->get()->row();

                                        echo $getdataa->low_range." - ".$getdataa->high_range." ".$getdataa->units."<br>";   
                                        // if($low != 0 || $high != 0){
                                        //     $conditions = $result['condition'];
                                        //     foreach($conditions as $rec){
                                        //         if($rec['condition'] == ''){
                                        //             echo "<span style='font-weight:bold'> </span>".$rec['low_range']." - ".$rec['high_range']." ".$rec['units']."<br>";    
                                        //         }else{
                                        //             echo "<span style='font-weight:bold'>".$rec['condition'].": </span>".$rec['low_range']." - ".$rec['high_range']." ".$rec['units']."<br>";    
                                        //         }
                                        //     }
                                        // }
                                    }else{
                                        // echo $result['clinic_investigation_id'];
                                        // echo $patient_lab_report_line_items[0]->investigation_id;

                                        $data = $this->db->select('*')
                                        ->from('clinic_investigations')
                                        ->where('clinic_id =',$clinic_id)
                                        ->where('investigation_id =', $patient_lab_report_line_items[0]->investigation_id)
                                        ->get()->row();
                                        // echo $data->clinic_investigation_range_id;
                                        // exit();
                                        
                                        $dataa = $this->db->select('*')
                                        ->from('clinic_investigation_range')
                                        ->where('clinic_investigation_id =', $data->clinic_investigation_id)
                                        ->where('investigation_id =',$data->investigation_id)
                                        ->get()->row();
                                        // echo $dataa->remarks;
                                        if($dataa->remarks == '')
                                        {

                                            $getdata =$this->db->select('*')->from('clinic_investigations')
                                            ->where('investigation_id =',$result['investigation_id'])
                                            ->where('clinic_id =',$clinic_id)
                                            ->get()->row();
    
                                            $getdataa =$this->db->select('*')->from('clinic_investigation_range')
                                            ->where('clinic_investigation_id =',$getdata->clinic_investigation_id)
                                            ->get()->row();
    
                                            echo $getdataa->low_range." - ".$getdataa->high_range." ".$getdataa->units."<br>";   
                                            // if($low != 0 || $high != 0){
                                            //     $conditions = $result['condition'];
                                            //     foreach($conditions as $rec){
                                            //         if($rec['condition'] == ''){
                                            //             echo "<span style='font-weight:bold'> </span>".$rec['low_range']." - ".$rec['high_range']." ".$rec['units']."<br>";    
                                            //         }else{
                                            //             echo "<span style='font-weight:bold'>".$rec['condition'].": </span>".$rec['low_range']." - ".$rec['high_range']." ".$rec['units']."<br>";    
                                            //         }
                                            //     }
                                            // }
                                        }
                                        else{
                                            $getdata =$this->db->select('*')->from('clinic_investigations')
                                            ->where('investigation_id =',$result['investigation_id'])
                                            ->where('clinic_id =',$clinic_id)
                                            ->get()->row();
    
                                            $getdataa =$this->db->select('*')->from('clinic_investigation_range')
                                            ->where('clinic_investigation_id =',$getdata->clinic_investigation_id)
                                            ->get()->row();
                                            echo $getdataa->remarks."<br>";  
                                        }	
                                        // if($low != 0 || $high != 0){
                                        //     $conditions = $result['condition'];
                                        //     foreach($conditions as $rec){
                                        //         if($rec['condition'] == ''){
                                        //             echo "<span style='font-weight:bold'>Normal: </span>".$rec['low_range']." - ".$rec['high_range']." ".$rec['units']."<br>";    
                                        //         }else{
                                        //             echo "<span style='font-weight:bold'>".$rec['condition'].": </span>".$rec['low_range']." - ".$rec['high_range']." ".$rec['units']."<br>";    
                                        //         }
                                        //     }
                                        // }
                                    }
                                        ?>    
                                    </td>                                    
                                </tr>
                            <?php } ?>
                                <tr>
                                    <td colspan="4"><hr></td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="vertical-align: top">
                                        <span style="font-weight: bold; font-size: 13px;">Consultant's Remarks:</span>
                                        <p style="padding-top: 10px; font-size: 12px;"><?=$consultant_remark?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3"></td>
                                    <td>
                                        <center>
                                            <img style="height:80px" alt="" src="<?php echo base_url(); ?>uploads/digital_sign/<?php echo $report_info->digital_sign." "; ?>">
                                            <br>
                                            <span style="font-size: 13px; color:#000; text-transform: uppercase;"><?=$report_info->consultant_fname?></span><br>
                                            <span style="font-weight: bold; font-size: 13px; color: #000">CONSULTANT PATHOLOGIST</span>
                                        </center>
                                    </td>
                                </tr>
                        </tbody>
                    <?php }else{ ?>
                        <thead>
                            <tr style="background: #cccccc; color: #ffffff;">
                                <th style="width: 10%; text-align: center; text-transform: uppercase; padding:5px 10px; font-size: 12px;">S.No.</th>
                                <th style="width: 30%; text-transform: uppercase; padding:5px 10px; font-size: 12px;">Investigation</th>
                                <th style="width: 60%; text-transform: uppercase; padding:5px 10px; font-size: 12px;">Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            foreach($lab_results as $result) {
                                $value = $result['remarks'];
                            ?>
                                <tr>
                                    <td style="vertical-align: top; text-align: center; font-size: 13px"><?php echo $i++; ?>.</td>
                                    <td style="vertical-align: top;">
                                        <span style="font-size:12px;font-weight: bold"><b><?php echo strtoupper($result['investigation']); ?></b></span>
                                    </td>
                                    <td style="font-size: 12px; vertical-align: top">
                                        <p><?php echo $value; ?></p>
                                    </td>                                  
                                </tr>
                            <?php } ?>
                                <tr>
                                    <td colspan="3" class="remarks">
                                        <span style="font-weight: bold; text-decoration: underline; font-size: 13px; line-height: 20px;">Lab Consultant's Remarks:</span>
                                        <p><?=$consultant_remark?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="text-align: right;">
                                        <table>
                                            <tr>
                                                <td style="text-align: center">
                                                    <img style="height:50px;" alt="" src="<?php echo base_url(); ?>uploads/digital_sign/<?php echo $report_info->digital_sign." "; ?>"><br>
                                                    <span style="margin-top:10px;font-size: 14px; text-transform: uppercase;"><?=$report_info->consultant_fname?></span><br>
                                                    <span style="margin-top:10px;font-weight: bold;">CONSULTANT PATHOLIGIST</span>    
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                        </tbody>
                    <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
	<htmlpagefooter name="footer">
		<table cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px; width: 100%;">
			<tr>
				<td style="font-size:14px; text-align: left;">
					Powered by umdaa.co 
				</td>
			</tr>
		</table>
	</htmlpagefooter>
</body>	
</html>