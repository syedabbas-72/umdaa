<div class="row">
    <div class="col-md-12">
    <div class="col-md-12 text-center mainTestTitle">
                        <?=$test_name?> - <?=$lab_results[0]['template_type']?>
                    </div>

                    <table class="table resultTable noBdr">
                        <?php if($template_type == 'Excel'){ ?>
                            <thead>
                                <tr>
                                    <th style="width: 5%;" class="text-center">S.No.</th>
                                    <th style="width: 40%">Investigation / Item Code</th>
                                    <th style="width: 20%" class="text-center">Results</th>
                                    <th style="width: 35%">Information</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
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
                                        $mastersInvInfo = $this->db->query("select * from investigations where investigation_id='".$result['investigation_id']."'")->row();
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



                                             if(count($getdata)>0)
                                             {
                                                $getdataa =$this->db->select('*')->from('clinic_investigation_range')
                                                ->where('clinic_investigation_id =',$getdata->clinic_investigation_id)
                                                // ->where('clinic_id =',$clinic_id)
                                                ->get()->row();
                                                echo $getdataa->low_range." - ".$getdataa->high_range." ".$getdataa->units."<br>";  
                                             }
                                             else
                                             {
                                                $getdataaa =$this->db->select('*')->from('investigation_range')
                                                ->where('investigation_id =',$result['investigation_id'])
                                                // ->where('clinic_id =',$clinic_id)
                                                ->get()->row();
                                                echo $getdataaa->low_range." - ".$getdataaa->high_range." ".$getdataaa->units."<br>";   
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
<!-- Footer Info -->
<div class="row">
    <div class="col-md-6">
        <label class="font-weight-bold">POWERED BY UMDAA</label>
    </div>
    <div class="col-md-6 text-right">
    <img style="width:50%" alt="" src="<?php echo  base_url()."uploads/digital_sign/".$digitalSignature->digital_sign;?>">
  <br>
    <b>Lab Consultant</b>
        <!-- <label class="font-weight-bold"><?=date("d-m-Y h:i A", strtotime($billingInfo->created_date_time))?></label> -->
    </div>
</div>