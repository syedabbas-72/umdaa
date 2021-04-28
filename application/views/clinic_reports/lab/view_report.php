<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li>Lab&nbsp;<i class="fa fa-angle-right"></i></li>
            <li><a href="../../view_order/<?=$billing_info->billing_id?>">Orders&nbsp;<i class="fa fa-angle-right"></i></a></li>
            <li class="active">View Report</li>
        </ol>
    </div>
</div>

<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-head card-default">
                    <table cellspacing="0" cellpadding="0" width="100%" class="headerTable">
                        <tr>
                            <td style="width: 25%">
                                <header>
                                    <?php echo ucwords($billing_info->guest_name); ?><br>
                                    <small><?php echo DataCrypt($billing_info->guest_mobile, 'decrypt'); ?></small>        
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
                            <td style="width: 25%" class="text-right">
                                <input type="hidden" id="lineItemNo_tb" value="<?=count($billing_line_items)?>">
                                <input type="hidden" id="billing_id_tb" value="<?=$billing_info->billing_id?>">
                                <a href="<?=base_url('Lab/report_pdf/'.$billing_info->billing_id.'/'.$investigation_id)?>" class="btn btn-primary " target="_blank"><i class="fa fa-print"></i></a>
                                <!-- <a href="<?=base_url('PdfView/labReport/'.$billing_info->billing_id.'/'.$investigation_id)?>" class="btn btn-primary " target="_blank"><i class="fa fa-print"></i></a> -->
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="card-body"> 

                    <div class="col-md-12 text-center mainTestTitle">
                        <!-- <?=$test_name?> - <?=$lab_results[0]['template_type']?> -->
                        <?=$test_name?>
                    </div>

                    <table class="table resultTable noBdr">
                        <?php if($template_type == 'Excel'){ ?>
                            <thead>
                                <tr>
                                    <th style="width: 5%;" class="text-center">S.No.</th>
                                    <th style="width: 40%">Investigation / Item Code</th>
                                    <th style="width: 20%" class="text-center">Results</th>
                                    <th style="width: 35%">Information<br><small>(Low Range-High Range)</small></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // echo $lab_results[0]['investigation_id'];
                                // exit();
                                $i = 1;
                                foreach($lab_results as $result) {
                                    $low = (float)$result['low_range'];
                                    $high = (float)$result['high_range'];
                                    $value = (float)$result['value'];
                                    $clinic_othersInfo = $this->db->query("select * from clinic_investigations where investigation_id='".$result['investigation_id']."'")->row();
                                    if(count($clinic_othersInfo)>0)
                                    {
                                        $others = $clinic_othersInfo->other_information;
                                    }
                                    else
                                    {
                                        $mastersInvInfo = $this->db->query("select * from investigations where  investigation_id='".$result['investigation_id']."'")->row();
                                        $others = $mastersInvInfo->other_information;
                                    }
                                    // echo "others:".$others;
                                ?>
                                    <tr>
                                        <td class="text-center"><?php echo $i++; ?>.</td>
                                        <td>
                                            <span class="title"><?php echo strtoupper($result['investigation']); ?></span><br>
                                            <small><i><?=$result['method']?></i></small>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            if($value != "" || $value != null){
                                                if($low != 0 || $high != 0){
                                                    if($value < $low || $value > $high){
                                                        echo '<span class="abnormal">'.$value.'</span>';
                                                    }else{
                                                        echo '<span class="normal">'.$value.'</span>';
                                                    }    
                                                }else{
                                                    echo '<span class="normal">'.$value.'</span>';
                                                }
                                                echo '<span class="units">'.$result['units'].'</span>';
                                            }else{
                                                echo '<span class="normal"> -- </span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="others">
                                            <!-- <?php echo $result['investigation_id']?>
                                            <?php echo $clinic_id?> -->
                                            <?php 
                                             $getdata =$this->db->select('*')->from('clinic_investigations')
                                             ->where('investigation_id =',$result['investigation_id'])
                                             ->where('clinic_id =',$clinic_id)
                                             ->get()->row();

                                            //  echo $lab_results[0];
                                            //  exit();

                                            //  $dataa = $this->db->select('*')
                                            //  ->from('clinic_investigation_range')
                                            //  ->where('clinic_investigation_id =', $data->clinic_investigation_id)
                                            //  ->where('investigation_id =',$data->investigation_id)
                                            //  ->get()->row();

                                             $getdataa =$this->db->select('*')->from('clinic_investigation_range')
                                             ->where('clinic_investigation_id =',$getdata->clinic_investigation_id)
                                            //  ->where('investigation_id =',$investigation_id)
                                             ->get()->row();

                                             if($billing_invoice->gender == 'Male' || $billing_invoice->gender == 'male' ){
                                                echo $getdataa->low_range." - ".$getdataa->high_range." ".$getdataa->units."<br>";   
                                            //  if(count($getdata)>0)
                                            //  {
                                            //     $getdataa =$this->db->select('*')->from('clinic_investigation_range')
                                            //     ->where('clinic_investigation_id =',$getdata->clinic_investigation_id)
                                            //     // ->where('clinic_id =',$clinic_id)
                                            //     ->get()->row();
                                            //     echo $getdataa->remarks."<br>";  
                                            //     // echo $getdataa->low_range." - ".$getdataa->high_range." ".$getdataa->units."<br>";  
                                            //  }
                                            }
                                             else
                                             {
                                                $getclinic_investigation_id =$this->db->select('*')->from('clinic_investigations')
                                                ->where('investigation_id =',$lab_results[0]['investigation_id'])
                                                ->where('clinic_id =',$clinic_id)
                                                ->get()->row();

                                                $getda = $this->db->select('*')
                                                ->from('clinic_investigation_range')
                                                ->where('clinic_investigation_id =', $getclinic_investigation_id->clinic_investigation_id)
                                                ->where('investigation_id =',$lab_results[0]['investigation_id'])
                                                ->get()->row();

            //                                     	echo $this->db->last_query();
			// exit();

                                                if($getda->remarks == ''){
                                                    echo $getdataa->low_range." - ".$getdataa->high_range." ".$getdataa->units."<br>";   
                                                }else{
                                                    echo $getdataa->remarks."<br>";  
                                                }
                                                // echo $getdataaa->low_range." - ".$getdataaa->high_range." ".$getdataaa->units."<br>";   
                                             }
                                             ?>
                                            <!-- <?php echo str_replace(';', '<br>', $others); ?> 
                                            <?php 
                                            if($low != 0 || $high != 0){
                                                $conditions = $result['condition'];
                                                foreach($conditions as $rec){
                                                    if($rec['condition'] == ''){
                                                        echo $rec['low_range']." - ".$rec['high_range']." ".$rec['units']."<br>";    
                                                    }else{
                                                        echo "<b>".$rec['condition'].": </b>".$rec['low_range']." - ".$rec['high_range']." ".$rec['units']."<br>";    
                                                    }
                                                }
                                            }
                                            ?>        -->
                                        </td>                                    
                                    </tr>
                                <?php } ?>
                                    <tr>
                                        <td colspan="4" class="remarks">
                                            <span class="subTitle">Lab Consultant's Remarks:</span>
                                            <p><?=$result['consultant_remark']?></p>
                                        </td>
                                    </tr>
                            </tbody>
                        <?php }else{ ?>
                            <thead>
                                <tr>
                                    <th style="width: 5%;" class="text-center">S.No.</th>
                                    <th style="width: 20%">Investigation</th>
                                    <th style="width: 73%">Results</th>
                                    <th style="width: 2%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach($lab_results as $result) {
                                    $value = $result['remarks'];
                                ?>
                                    <tr>
                                        <td class="text-center"><?php echo $i++; ?>.</td>
                                        <td>
                                            <span class="title"><?php echo strtoupper($result['investigation']); ?></span>
                                        </td>
                                        <td>
                                            <p><?php echo $value; ?></p>
                                        </td>    
                                        <td>&nbsp;</td>                              
                                    </tr>
                                <?php } ?>
                                    <tr>
                                        <td colspan="4" class="remarks">
                                            <span class="subTitle">Lab Consultant's Remarks:</span>
                                            <p><?=$result['consultant_remark']?></p>
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