<style type="text/css">
    .table td{
        padding-left:15px !important;
        padding-right: 15px !important; 
    }
    .price{
        font-weight: 600;
        font-size: 14px;
    }
    .formulation{
        background: #ebebeb;
        border-radius: 4;
    }
</style>

<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li>Lab&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Investigations Prescribed</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-head card-default">
                <table cellspacing="0" cellpadding="0" width="100%" class="headerTable">
                    <tr>
                        <td style="width: 25%">
                            <header>
                                <?php echo "Patient Name:".$patients_details->first_name." ".$patients_details->last_name; ?>
                                <br>
                                <small><?php echo $patients_details->umr_no; ?></small>        
                            </header>
                        </td>
                        <td style="width: 25%">
                            <header>
                                <?php echo "Dr. ".ucwords($doctor_details->first_name." ".$doctor_details->last_name); ?>
                                <small><?php echo ucwords($dep->department_name); ?></small>
                            </header>    
                        </td>
                        <td style="width:25%">
                            <header>
                                Appointment On <?php echo date('d M Y', strtotime($appointments->appointment_date)); ?><br>
                                <small><?php echo $appointments->appointment_time_slot;?></small>
                            </header>
                        </td>
                        <td style="width: 25%" class="text-right">
                            <header>
                                <a href="<?php echo base_url('Lab/add_order/'.$patient_investigation_id);  ?>"><i class="fas fa-shopping-basket add"></i></a>
                            </header>
                        </td>
                    </tr>
                </table>
            </div>



            <div class="card-body"> 
                <table class="table table-bordered customTable">
                    <thead>
                        <tr>
                            <th style="width: 5%;" class="text-right">S.No.</th>
                            <th style="width:70%" class="text-left">Investigation &amp; Item Code</th>
                            <!-- <th style="width: 15%" class="text-center">Status</th> -->
                            <th style="width: 15%" class="text-center">Price</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php 
                    // echo "<pre>";
                    // echo "hai";
                    // print_r($clinic_id); 
                    // print_r($p);
                    
                    // echo $pri->investigation_id;
                    // echo "hai";
                    // echo "</pre>";
                    ?>

                    <!-- foreach ($tabl aps $key => $value) {   -->
                        <?php 
                        // print_r(($tablee));
                    
                        $i=1;
                        foreach ($table as $value) {
                         
                            ?>
                            <tr>
                                <td class="text-right"><?php echo $i++; ?></td>
                                <td class="text-left">
                                    <span><?php echo ($value->investigation_name); ?></span><br>
                                    <!-- <?php echo $value->price; ?> -->
                                </td>
                                <!-- <td class=""><?=$value->price?></td> -->
                                <!-- <td class="text-center"><?php if(number_format($value->price) == "0.00"){ ?><div class="no">Not Available</div><?php } else { ?><div class="okay">Available</div><?php } ?></td> -->
                                <!-- <td class="text-right"><span style="font-size: 18px;"><?php echo number_format($value->price,2); ?></span></td> -->

                                <!-- <td class="text-left"> <?php echo $value->status; ?></td> -->
           
                                <td class="text-center"><i class="fas fa-rupee-sign"></i> &nbsp; <?php
                                   $tablee=$this->db->select("css.*")->from("clinic_investigation_price css")
                                   ->where("css.investigation_id='".$value->investigation_id."' and css.clinic_id='".$clinic_id."' ")
                                   ->get()->
                                   row();
                                //    print_r($tablee);
                                   if(count($tablee)>0)
                                   {
                                    echo $tablee->price; 
                                   }
                                   else{
                                    echo '0';
                                   }
                                   ?></td>
                            </tr>
                            <?php 
                        } 
                        ?>
                        
                    </tbody>

                </table>

                <?php 
                    if(empty($table)){

                        echo '<P>'.'<center>'.'<h5>'."The investigation is not yet added to this patient".'<h5>'.'</center>'.'<p>';
                    }?>


                <!-- <?php $dp=print_r($table); $json=json_encode($table); ?> -->

                 <!-- print_r($tabl); -->
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function drop_invoice(bid)
    {
        var base_url = '<?php echo base_url(); ?>';
        $.ajax({
            url : base_url+"/New_order/drop_pharmacy_invoice",
            method : "POST",
            data : {"bid":bid},
            success : function(rdata) { 
                alert('Invoice Dropped');
                window.location.href = "<?php echo base_url('Pharmacy_Billing'); ?>"; 
            }
        });
    }
</script>
