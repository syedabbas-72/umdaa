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
            <li>Patient&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Prescriptions</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body"> 
                <table class="table-bordered table datatable patientsData customTable">
                    <thead>
                        <tr>
                            <th style="width: 5%">S.No.</th>
                            <th style="width: 35%">Patient Info</th>
                            <th style="width: 35%">Appointment Info</th>
                            <th style="width: 15%">Prescription Date</th>
                            <th style="width: 10%" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i=1;
                        foreach($patient_prescription as $prescription){
                            $total +=$bills->amount;
                            ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td>
                                    <span><?php echo ucwords($prescription->patient_first_name." ".$prescription->patient_last_name); ?></span><br>
                                    <?php echo $prescription->umr_no; ?>
                                </td>
                                <td>
                                    <span><?php echo "Dr. ".ucwords($prescription->doc_first_name." ".$prescription->doc_last_name); ?></span><br>
                                    <?php echo "On ".date("d-m-Y",strtotime($prescription->appointment_date))." @ ".date("H:i a", strtotime($prescription->appointment_time_slot)); ?>
                                </td>
                                <td><?php echo date("d-m-Y",strtotime($prescription->prescription_date)); ?></td>
                                <td class="actions">
                                    <a href="<?php echo base_url('pharmacy_prescription/view_prescription/'.$prescription->patient_prescription_id);?>"><i class="fa fa-eye viewSmall"></i></a>
                                    <a href="<?php echo base_url('New_order/add_order/'.$prescription->patient_prescription_id);?>"><i class="fa fa-shopping-basket cartSmall"></i></a>                                                                        
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>