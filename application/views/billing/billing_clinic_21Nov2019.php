<?php
// Get CRUD info
$crudInfo = getcrudInfo('Billing');
?>

<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
            <li>Billing&nbsp;<i class="fa fa-angle-right"></i></li>
            <li class="active">Invoices</li>
        </ol>
    </div>
</div>


<section class="main-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card noCardPadding">
                <div class="card-body">          
                    <div class="row pull-right">
                        <button class="btn btn-app btn-xs pull-right" style="margin-top:20px; margin-right:35px;">
                            <div id="daterange" style="cursor: pointer; padding: 5px 10px; width: 100%;">
                                <i class="fa fa-calendar-alt"></i>&nbsp;
                                <span></span> <i class="fa fa-caret-down"></i> <i class="fas fa-spinner fa-spin bill_loader"></i>
                                <input class="startDate" type="hidden"> 
                                <input class="endDate" type="hidden"> 
                            </div>
                        </button>
                    </div>

                    <table id="doctorlist" class="table customTable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 5%">S.No</th>
                                <th style="width: 15%">Invoice no. &amp; Date</th>
                                <th style="width: 19%">Patient Name & UMR No.</th>
                                <th style="width: 18%">Billing Type</th>
                                <th style="width: 8%" class="text-center">Status</th>
                                <th style="width: 15%">Reason</th>
                                <th class="text-right" style="width: 10%">Inv. Amount</th>
                                <th class="text-center" style="width: 10%">Action</th>
                            </tr>
                        </thead>
                        <tbody class="billings_body">
                            <?php 
                            $i=1; 
                            foreach ($billing as $value) { 
                                $discount = $value->discount;
                                $discount_unit = $value->discount_unit;
                                $total_amount = $value->total_amount;
                                $disc = $discount."%";
                                ?> 
                                <tr>
                                    <td class="text-center"><?php echo $i++;?></td>
                                    <td><span><?=$value->invoice_no?></span><br><small><b>D: </b><?=date("d-m-Y",strtotime($value->created_date_time))?></small></td>
                                    <td><span><?php echo ucwords($value->pname." ".$value->lname)."</span><span><small>(".$value->umr_no."]".')</small></span><br><small><b>M: </b>'.DataCrypt($value->mobile, 'decrypt'); ?></small></td> 
                                    <td><?php echo $value->billing_type; ?></td>
                                    <td class="statusInfo" class="text-center">
                                        <?php 
                                        if($value->status == 1){
                                            echo "<div class='closed'>Closed</div>";
                                        }else{
                                            echo "<div class='dropped'>Dropped</div>";
                                        }
                                        ?>
                                    </td>
                                    <td class="statusInfo"><?=$value->reason?></td>
                                    <td class="text-right">
                                        <i class="fas fa-rupee-sign"></i> 
                                        <?php 
                                        if($discount_unit=="INR")
                                        {
                                            echo $total_amount-$discount;
                                        }
                                        else
                                        {
                                         echo $total_amount-(($disc/100)*$total_amount);
                                     }
                                     ?>
                                 </td>          
                                 <td class="text-center actions" disabled="disabled">
                                    
                                        <!-- Read -->                                    
                                        <?php if($crudInfo->p_update == 1){ ?>
                                            <a href="<?php echo base_url('billing/view_order/'.$value->billing_id);?>"><i class="fas fa-search viewSmall"></i></a>&nbsp;
                                        <?php } ?>

                                        <a target="blank" href="<?php echo base_url('patients/print_invoice/'.$value->appointment_id.'/'.$value->billing_id);?>"><i class="fas fa-print"></i></a>

                                        <!-- Delete -->
                                        <?php if($crudInfo->p_delete == 1){ ?>
                                            <!-- <a href="<?php echo base_url('billing/drop_invoice/'.$value->billing_id);?>" onClick="return doconfirm();"><i class="fas fa-trash-alt deleteSmall" title="Drop Invoice"></i></a> -->
                                            <i class="fas fa-trash-alt deleteSmall" title="Drop Invoice"></i>
                                        <?php } ?>

                                </td>  
                            </tr>
                        <?php } ?>  
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal" id="drop_invoice_modal"  role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Drop/Cancel Invoice</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>
            <div class="modal-body" style="padding-top: 0px !important">
                <form action="<?php echo base_url('Billing/drop_invoice'); ?>" method="POST" role="form" class="customForm">
                    <p>Please specify a reason below for dropping this invoice</p>
                    <div class="row col-md-12">
                        <div class="form-group">
                            <label for="reason_ta" class="col-form-label">Reason <span style="color:red;">*</span></label>    
                            <textarea id="reason_ta" class="form-control" name="reason" cols="90" rows="2"></textarea>
                            <input type="hidden" name="billing_id" id="billing_id_tb">
                        </div>
                    </div>
                    <div class="row col-md-12 text-center">
                        <div class="col-md-6 text-right">
                            <div class="form-group">
                                <input type="submit" class="customBtn okayBtn" value="Submit Reason">
                            </div>
                        </div>
                        <div class="col-md-6 text-left">
                            <div class="form-group">
                                <input type="button" class="customBtn cancelBtn" data-dismiss="modal" value="Cancel">
                            </div>
                        </div>
                    </div>
                </form>
            </div> 
        </div>
    </div>
</div>


<!-- JAVE SCRIPT -->
<script src="<?php echo base_url(); ?>assets/plugins/moment/moment.min.js" ></script>
<link href="<?php echo base_url(); ?>assets/newdaterangepicker24Jul2019/daterangepicker.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url(); ?>assets/newdaterangepicker24Jul2019/daterangepicker.js"></script>

<script type="text/javascript">

    $(document).ready(function () {
        $('#doctorlist').dataTable();
    });

    function doconfirm()
    {
        if(confirm("Delete selected messages ?")){
            return true;
        }else{
            return false;  
        } 
    }

    $('.bill_loader').hide();
    $(function() {

        var start = moment().subtract(0, 'days');
        var end = moment();

        function cb(start, end) {
            $('#daterange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

            var start_date = start.format('YYYY-MM-DD');
            var end_date = end.format('YYYY-MM-DD');

            $('#daterange .startDate').val(start_date);
            $('#daterange .endDate').val(end_date);   
            $('.bill_loader').show();   

            $.post("<?=base_url('Billing/getBillings')?>",
            {
                startDate:start_date,
                endDate:end_date
            },
            function(data){
                console.log(data);
                $('.bill_loader').hide();
                $('.billings_body').html(data);
            });
        }

        $('#daterange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end);
    });

    function drop_invoice(billing_id){
        $("#billing_id_tb").val(billing_id);
        $("#drop_invoice_modal").modal();
    }

</script>