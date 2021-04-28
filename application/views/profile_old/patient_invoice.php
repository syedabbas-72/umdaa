<style type="text/css">
    .radio label::after{
        top:10px !important;
    }
</style>

<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li>Patient&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Invoice</li>
        </ol>
    </div>
</div>

<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card noCardPadding">
                <div class="pull-left page-title">
                    <?php count($appointmentInfo) > 0 ? $this->load->view('profile/appointment_info_header', $patient_dt->patient_id) : ''; ?>
                </div>
                <div class="row col-md-12"> 
                    <div class="col-md-3" id="view_casesheet">
                        <div class="col-md-12">
                            <?php $this->load->view('profile/patient_info_left_nav'); ?>
                        </div>
                    </div>
                    <div class="col-md-9" id="" class="">
                        <div class="row page-title">
                            <div class="col-md-12">
                                Invoice list
                            </div>
                        </div>
                        <div class="row col-md-12" style="padding-right: 0px">
                            <table id="doctorlist" class="table dt-responsive nowrap customTable">
                                <thead>
                                    <tr>
                                        <th style="width:5%" class="text-center">S#</th>
                                        <th style="width:12%" class="text-center">Date</th>
                                        <th style="width:35%" class="text-left">Type &amp; Description</th>
                                        <th style="width:12%" class="text-right">Total Amt</th>
                                        <th style="width:14%" class="text-center">Discount</th>
                                        <th style="width:12%" class="text-right">Invoice Amt</th>
                                        <th style="width:10%" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $i=1; 
                                    foreach ($billing as $value) { 

                                        if(trim($value->discount_unit) == "%"){
                                            // if % then calculate the discount amount in INR
                                            $discount = $value->discount;
                                            $discounted_figure = round(($value->totalBillAmount * ($value->discount / 100)),2);
                                            $discount_unit = $value->discount_unit;
                                        }else{
                                            // if INR
                                            $discount = number_format($value->discount, 2);
                                            $discount_unit = $value->discount_unit;
                                        }

                                        // Get description
                                        $this->db->select('GROUP_CONCAT(item_information) as itemInfo');
                                        $this->db->from('billing_line_items');
                                        $this->db->where('billing_id =',$value->billing_id);

                                        $description = $this->db->get()->row();
                                        ?> 
                                        <tr>
                                            <td class="text-center"><?php echo $i++;?></td>
                                            <td class="text-center">
                                                <?php echo date("d M. Y",strtotime($value->billing_date_time));?><br>        
                                            </td>
                                            <td class="text-left">
                                                <span class="bold"><?php echo $value->billing_type; ?></span><br>
                                                <?php echo str_replace(',', ', ', $description->itemInfo); ?>
                                            </td>
                                            <td class="text-right"><?php echo number_format($value->totalBillAmount, 2); ?></td>
                                            <td class="text-center"><?php echo ($discount != '' ? $discount.' '.$discount_unit : ''); ?></td>
                                            <td class="text-right"><?php echo number_format(round($value->totalBillAmount,2) - $discounted_figure, 2); ?></td>
                                            <?php if($billing_master->billing_type != "Pharmacy") { ?>
                                                <td class="text-center noBdrRight" style="font-size:20px;">
                                                    <a target="_blank" href="<?php echo base_url('patients/print_invoice/'.$value->appointment_id.'/'.$value->billing_id);?>"><i class="fa fa-print"></i></a>
                                                </td>
                                            <?php } else { ?>
                                                <td class="text-center noBdrRight" style="font-size:20px;">
                                                    <a target="_blank" href="<?php echo base_url('New_order/print_bill/'.$value->billing_id);?>"><i class="fa fa-print"></i></a>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                    <?php } // End of forloop ?>  
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
