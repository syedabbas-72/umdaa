<head>
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>
    <style>
.list-group {
    max-height: 300px;
    margin-bottom: 10px;
    overflow: scroll;
    -webkit-overflow-scrolling: touch;
}

::-webkit-scrollbar {
    /* width: 25px; */
}

#second_processing {

    background-color: #10367a !important;
    color: white !important;
    box-shadow: 5px 10px 18px #888888 !important;
}

.form-control form-control-sm {
    background-color: #10367a !important;
}

.header-btn {
    border-radius: 30px !important;
    padding: 10px !important;
}
    </style>
    <div class="page-bar">
        <div class="row page-header no-background no-shadow margin-b-0">
            <div class="col-lg-6 align-self-center">
                <ol class="breadcrumb page-breadcrumb pull-left">
                    <li><a href="#"><i class="fas fa-home"></i> <span
                                class="text-uppercase"><?= $this->session->userdata('clinic_name') ?></span> <i
                                class="fa fa-angle-right"></i></a></li>
                    <li>SETTINGS</li>
                </ol>
            </div>
            <div class="col-lg-6 align-self-center text-right">
                <button class="btn header-btn btn-app" data-target="#printModal" data-toggle="modal"><i
                        class="fas fa-print"></i> Print Settings</button>
            </div>
        </div>
    </div>
    <div class="modal fade"  id="edit_parameter_popup" role="dialog" style="z-index:99999999999">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="edit_data"></div>
                </div>
            </div>
        </div>
    </div>
    <div id="printModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Print Settings</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="post" action="<?= base_url('LabNew/pdfSettings') ?>">
                        <!-- Paper Settings -->
                        <div class="row text-center docInfoHdr">
                            <div class="col-md-6 text-left">
                                Paper Settings
                            </div>
                        </div>
                        <div class="row col-md-12">
                            <div class="col-md-12">
                                <p class="p-0 m-0" style="padding:0px !important"><label class="font-weight-bold">Paper
                                        Type</label></p>
                                <input type="radio" name="paper_type" checked="true" value="A4"
                                    <?= ($pdfSettings->paper_type == "A4") ? 'checked' : 'checked' ?> id="a4">
                                <label for="a4">A4</label>
                            </div>
                        </div>
                        <!-- Paper Settings -->
                        <!-- Header Settings -->
                        <div class="row text-center docInfoHdr">
                            <div class="col-md-6 text-left">
                                Header Settings
                            </div>
                        </div>
                        <div class="row col-md-12">

                            <div class="col-md-5">
                                <p style="padding:0px !important"><label class="font-weight-bold"
                                        for="reportHeader">Header On Report</label></p>
                                <input type="radio" name="report_header" class="report_header" value="1" id="automated"
                                    <?= ($pdfSettings->header_report == "1") ? 'checked' : '' ?>> <label
                                    for="automated">Automated Header</label>
                                <input type="radio" name="report_header" class="report_header" value="2" id="self"
                                    <?= ($pdfSettings->header_report == "2") ? 'checked' : '' ?>> <label for="self">Self
                                    Header</label>
                            </div>
                            <div class="col-md-3">
                                <p style="padding:0px !important"><label class="font-weight-bold"
                                        for="reportHeader">Header Height <span style="font-weight:normal">(In
                                            cms)</span></label></p>
                                <input type="text" onkeypress="return numeric()"
                                    <?= ($pdfSettings->header_report != "2") ? 'disabled' : '' ?>
                                    class="form-control report_height" name="report_header_height"
                                    value="<?= $pdfSettings->header_report_height ?>">
                            </div>

                        </div>
                        <div class="row col-md-12 mt-2">

                            <div class="col-md-5">
                                <p style="padding:0px !important"><label class="font-weight-bold"
                                        for="reportHeader">Header On Invoice</label></p>
                                <input type="radio" name="Invoice_header" class="invoice_automated" value="1"
                                    id="inv_automated" <?= ($pdfSettings->header_invoice == "1") ? 'checked' : '' ?>>
                                <label for="inv_automated">Automated Header</label>
                                <input type="radio" name="Invoice_header" class="invoice_self" value="2" id="inv_self"
                                    <?= ($pdfSettings->header_invoice == "2") ? 'checked' : '' ?>> <label
                                    for="inv_self">Self Header</label>
                            </div>
                            <div class="col-md-3">
                                <p style="padding:0px !important"><label class="font-weight-bold"
                                        for="reportHeader">Header Height <span style="font-weight:normal">(In
                                            cms)</span></label></p>
                                <input type="text" onkeypress="return numeric()"
                                    <?= ($pdfSettings->header_invoice != "2") ? 'disabled' : '' ?>
                                    class="form-control invoice_height" name="invoice_header_height"
                                    value="<?= $pdfSettings->header_invoice_height ?>">
                            </div>

                        </div>
                        <!-- Header Settings -->
                        <!-- Footer Settings -->
                        <div class="row text-center docInfoHdr">
                            <div class="col-md-6 text-left">
                                Footer Settings
                            </div>
                        </div>
                        <div class="row col-md-12">

                            <div class="col-md-5">
                                <p style="padding:0px !important"><label class="font-weight-bold"
                                        for="reportHeader">Footer On Report</label></p>
                                <input type="radio" name="report_footer" value="1" class="footer_r_automated"
                                    id="footer_automated" <?= ($pdfSettings->footer_report == "1") ? 'checked' : '' ?>>
                                <label for="footer_automated">Automated Footer</label>
                                <input type="radio" name="report_footer" value="2" class="footer_r_self"
                                    id="footer_self" <?= ($pdfSettings->footer_report == "2") ? 'checked' : '' ?>>
                                <label for="footer_self">Self Footer</label>
                            </div>
                            <div class="col-md-3">
                                <p style="padding:0px !important"><label class="font-weight-bold"
                                        for="reportHeader">Footer Height <span style="font-weight:normal">(In
                                            cms)</span></label></p>
                                <input type="text" onkeypress="return numeric()"
                                    <?= ($pdfSettings->footer_report != "2") ? 'disabled' : '' ?>
                                    class="form-control footer_report_height" name="report_footer_height"
                                    value="<?= $pdfSettings->footer_report_height ?>">
                            </div>

                        </div>
                        <div class="row col-md-12 mt-2">

                            <div class="col-md-5">
                                <p style="padding:0px !important"><label class="font-weight-bold"
                                        for="reportHeader">Footer On Invoice</label></p>
                                <input type="radio" name="Invoice_footer" value="1" class="footer_i_automated"
                                    id="inv_foot_automated"
                                    <?= ($pdfSettings->footer_invoice == "1") ? 'checked' : '' ?>> <label
                                    for="inv_foot_automated">Automated Footer</label>
                                <input type="radio" name="Invoice_footer" value="2" class="footer_i_self"
                                    id="inv_foot_self" <?= ($pdfSettings->footer_invoice == "2") ? 'checked' : '' ?>>
                                <label for="inv_foot_self">Self Footer</label>
                            </div>
                            <div class="col-md-3">
                                <p style="padding:0px !important"><label class="font-weight-bold"
                                        for="reportHeader">Footer Height <span style="font-weight:normal">(In
                                            cms)</span></label></p>
                                <input type="text" onkeypress="return numeric()"
                                    <?= ($pdfSettings->footer_invoice != "2") ? 'disabled' : '' ?>
                                    class="form-control footer_invoice_height" name="invoice_footer_height"
                                    value="<?= $pdfSettings->footer_invoice_height ?>">
                            </div>

                        </div>
                        <!-- Footer Settings -->
                        <!-- Submit -->
                        <div class="row col-md-12 text-center mt-3">
                            <div class="col-md-12">
                                <button class="btn btn-success" name="submit" type="submit">Save</button>
                            </div>
                        </div>
                        <!-- submit -->

                    </form>
                </div>
            </div>

        </div>
    </div>


    <div class="card">
        <div class="container-fluid">
            <div>
                <!-- <a href="<?=base_url()?>LabNew/orders_list"
                                class="btn btn-primary float-right font-weight-bold">goto orders</a> -->
                <h4 class="page-title">add investigations to your clinic</h4>
            </div>
            <div class="row">
                <!-- <div class="col-12 col-xl-12 col-md-12 col-lg-12 d-flex"> -->


                <div class="col-12 d-flex">
                    <div class="col-8 pl-0">
                        <!-- <label>Investigation</label> -->
                        <!-- <div class="autocomplete" style="width:300px;"> -->
                        <input id="search_investigations" type="text" name="Search Investigations"
                            placeholder="Search Investigations" class="form-control"
                            onkeypress="Inv_search(this.value)">
                        <!-- </div>   -->
                    </div>
                    <div class="col-4" id="price_input" style="display: none;">
                        <div class="d-flex">


                            <!-- <input type="hidden" id="inv_id"> -->
                            <input class="col-3 form-control" onkeypress="return numeric()" id="price"
                                placeholder="Price">

                            <input type="text" id="somediv" style="display: none;" value="" />
                            <input type="text" id="inv_name" style="display: none;" value="" />
                            <button type="button" class="col-2 btn btn-primary ml-2" value="add"
                                onclick="adding_search_result()" onkeypress="return numeric()" id="invId">add</button>

                        </div>
                    </div>
                </div>
                <!-- </div> -->
            </div> <br><br><br>
            <div class="row">
                <div class="col-6 border">
                    <h5 class="font-weight-bold text-uppercase text-primary text-center">commonly used
                        investigations</h5>
                    <table id="first" class="customTable table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Investigation</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            foreach ($total_records as $tot) { ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php echo $tot->package_name ?></td>
                                <td><input type="number" placeholder="Price"
                                        id="inv_price_<?php echo $tot->package_id ?>" class="form-control">
                                </td>
                                <td><button title="click hear to add" id="common_btn"
                                        value="<?php echo $tot->package_id ?>" onclick="add_investigation(this.value)"
                                        class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                </td>
                            </tr>
                            <?php $i++;
                            } ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-6 border" style="background-color:#c9c9c9; color:white">
                    <h5 class="font-weight-bold text-uppercase text-primary text-center">your Clinic
                        investigations</h5>
                    <table id="second" onclick="" class="customTable table-hover" style="color: black !important;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Investigation</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>

    </div>


    <div class="modal fade " id="inv_setings_popup" style="max-width: 100%;" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div id="settings_modal_popup">
                </div>
            </div>
        </div>
    </div>


    <!-- Par pop up -->


    <div class="modal fade " id="add_paramter_popup" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <h4 class="row page-title">Add New Parameter</h4>

         
                    <form>
                    <div class="row">
                    <div class="col-12">
                    <div class="form-group">
                            <input type="hidden" name="package_id" id="package_id" value=""/>
                                <label>Parameter Name</label>
                                <input type="text" class="form-control" required id="inv_namee"  placeholder="investigation name">
                            </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label for="exampleFormControlTextarea1">Content</label>
                            <textarea class="form-control summernote" id="content" rows="3"></textarea>
                        </div>
                    </div>

                  <!-- <div class="col-12"> -->
                
                  <!-- <div class="form-check">
                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                    <label class="form-check-label" for="flexRadioDefault1">
                    Do You Want Units?
                    </label>
                    </div> -->
                            <div class="col-6">
                            <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="units">
                        <label class="form-check-label" for="flexCheckDefault">
                         Do you want to add Units?
                        </label>
                        <div class="form-group" id="units_range" style="display: none">
                            <input type="hidden" name="package_id" id="package_id" value=""/>
                                <label>Units</label>
                                <textarea id="units_value" autocomplete="off" class="form-control " required  placeholder="Enter Units"><?php echo $value?></textarea>
                            </div>
                    </div>
                            </div>

                            <div class="col-6">
                            <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="range">
                        <label class="form-check-label" for="flexCheckDefault">
                        Do you want to add Range?
                        </label>
                        <div class="form-group" id="add_range" style="display: none">
                            <input type="hidden" name="package_id" id="package_id" value=""/>
                                <label>Range</label>
                                <textarea class="form-control" id="range_value" rows="1" placeholder="Enter Range"></textarea>
                             
                            </div>
                    </div>
                            </div>
                  
                  
                <!-- <div class="form-check">
                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2" checked>
                <label class="form-check-label" for="flexRadioDefault2">
                    Default checked radio
                </label>
                </div> -->
                  <!-- </div> -->
               
                  <!-- <div class="col-12" id="units_range" style="display: none">
                    <div class="form-group">
                            <input type="hidden" name="package_id" id="package_id" value=""/>
                                <label>Units</label>
                                <input type="text" id="units_value" autocomplete="off" class="form-control" required  value="<?php echo $value?>" placeholder="Enter Units">
                            </div>
                    </div> -->

                <!-- <div class="col-12">
                <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="range">
                        <label class="form-check-label" for="flexCheckDefault">
                        Do you want to add Range?
                        </label>
                        <div class="form-group" id="add_range" style="display: none">
                            <input type="hidden" name="package_id" id="package_id" value=""/>
                                <label>Range</label>
                                <textarea class="form-control" id="range_value" rows="3"></textarea>
                             
                            </div>
                    </div>
                </div> -->

                    <!-- <div class="col-12" id="add_range" style="display: none">
                    <div class="form-group">
                            <input type="hidden" name="package_id" id="package_id" value=""/>
                                <label>Range</label>
                                <textarea class="form-control" id="range_value" rows="3"></textarea>
                             
                            </div>
                    </div> -->
                  
                    </form>
                
                  

                    <!-- <div class="row">
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">Investigation Name</label>
                            <input type="text" class="form-control">
                        </div>
                    </div> -->
                </div>
                <!-- this division is used to assigning clinic_lab_package_id to this div p tag -->
                <div>
                    <p id="clinic_lab_package_id">
                    </p>
                </div>

                <div class="modal-footer">
            <button class="btn btn-primary" id="report_submit" value="mater" onclick="add_parameter()" data-dismiss="modal">SUBMIT</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>


            </div>
        </div>
    </div>

    <div class="modal fade " id="add_paramter_popup" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <h4 class="row page-title">Add New Parameter</h4>

         
                    <form>
                    <div class="row">
                    <div class="col-12">
                    <div class="form-group">
                            <input type="hidden" name="package_id" id="package_id" value=""/>
                                <label>Parameter Name</label>
                                <input type="text" class="form-control" required id="inv_namee"  placeholder="investigation name">
                            </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label for="exampleFormControlTextarea1">Content</label>
                            <textarea class="form-control" id="content" rows="3"></textarea>
                        </div>
                    </div>

                  <!-- <div class="col-12"> -->
                
                  <!-- <div class="form-check">
                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
                    <label class="form-check-label" for="flexRadioDefault1">
                    Do You Want Units?
                    </label>
                    </div> -->
                            <div class="col-6">
                            <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="units">
                        <label class="form-check-label" for="flexCheckDefault">
                         Do you want to add Units?
                        </label>
                        <div class="form-group" id="units_range" style="display: none">
                            <input type="hidden" name="package_id" id="package_id" value=""/>
                                <label>Units</label>
                                <textarea id="units_value" autocomplete="off" class="form-control" required  placeholder="Enter Units"><?=$value?></textarea>
                            </div>
                    </div>
                            </div>

                            <div class="col-6">
                            <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="range">
                        <label class="form-check-label" for="flexCheckDefault">
                        Do you want to add Range?
                        </label>
                        <div class="form-group" id="add_range" style="display: none">
                            <input type="hidden" name="package_id" id="package_id" value=""/>
                                <label>Range</label>
                                <textarea class="form-control" id="range_value" rows="1" placeholder="Enter Range"></textarea>
                             
                            </div>
                    </div>
                            </div>
                  
                  
                <!-- <div class="form-check">
                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2" checked>
                <label class="form-check-label" for="flexRadioDefault2">
                    Default checked radio
                </label>
                </div> -->
                  <!-- </div> -->
               
                  <!-- <div class="col-12" id="units_range" style="display: none">
                    <div class="form-group">
                            <input type="hidden" name="package_id" id="package_id" value=""/>
                                <label>Units</label>
                                <input type="text" id="units_value" autocomplete="off" class="form-control" required  value="<?php echo $value?>" placeholder="Enter Units">
                            </div>
                    </div> -->

                <!-- <div class="col-12">
                <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="range">
                        <label class="form-check-label" for="flexCheckDefault">
                        Do you want to add Range?
                        </label>
                        <div class="form-group" id="add_range" style="display: none">
                            <input type="hidden" name="package_id" id="package_id" value=""/>
                                <label>Range</label>
                                <textarea class="form-control" id="range_value" rows="3"></textarea>
                             
                            </div>
                    </div>
                </div> -->

                    <!-- <div class="col-12" id="add_range" style="display: none">
                    <div class="form-group">
                            <input type="hidden" name="package_id" id="package_id" value=""/>
                                <label>Range</label>
                                <textarea class="form-control" id="range_value" rows="3"></textarea>
                             
                            </div>
                    </div> -->
                  
                    </form>
                
                  

                    <!-- <div class="row">
                        <div class="form-group col-md-4">
                            <label class="font-weight-bold">Investigation Name</label>
                            <input type="text" class="form-control">
                        </div>
                    </div> -->
                </div>
                <!-- this division is used to assigning clinic_lab_package_id to this div p tag -->
                <div>
                    <p id="clinic_lab_package_id">
                    </p>
                </div>

                <div class="modal-footer">
            <button class="btn btn-primary" id="report_submit" value="mater" onclick="add_parameter()" data-dismiss="modal">SUBMIT</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>


            </div>
        </div>
    </div>


    <!-- add parameter pop up -->

    <!-- start edit parameter pop up -->

    <div class="modal fade" id="exampleModall" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>


 

    <!-- end edit parameter pop up -->
    <script type="text/javascript">
    $(function () {
      $('.summernote').summernote({
            toolbar: []
      });
        $("#units").click(function () {
            if ($(this).is(":checked")) {
                $("#units_range").show();
            } else {
                $("#units_range").hide();
                $('#units_value').val('');
            }
        });
    });
</script>

<script type="text/javascript">
    $(function () {
        $("#range").click(function () {
            if ($(this).is(":checked")) {
                $("#add_range").show();
            } else {
                $("#add_range").hide();
                $('#range_value').val('');
            }
        });
    });
</script>

    <script>
$(document).ready(function() {
    // $('#add_paramter_popup').modal()
    $('input[name="report_header"]').on("click", function() {
        var value = $(this).val()
        console.log(value)
        if (value == 1) {
            $('.report_height').attr("disabled", true)
            $('.report_height').val("0")
        } else if (value == 2) {
            $('.report_height').removeAttr("disabled")
        } else {
            $('.report_height').removeAttr("disabled")
        }
    })

    $('input[name="Invoice_header"]').on("click", function() {
        var value = $(this).val()
        console.log(value)
        if (value == 1) {
            $('.invoice_height').attr("disabled", true)
            $('.invoice_height').val("0")
        } else if (value == 2) {
            $('.invoice_height').removeAttr("disabled")
        } else {
            $('.invoice_height').removeAttr("disabled")
        }
    })

    $('input[name="report_footer"]').on("click", function() {
        var value = $(this).val()
        console.log(value)
        if (value == 1) {
            $('.footer_report_height').attr("disabled", true)
            $('.footer_report_height').val("0")
        } else if (value == 2) {
            $('.footer_report_height').removeAttr("disabled")
        } else {
            $('.footer_report_height').removeAttr("disabled")
        }
    })

    $('input[name="Invoice_footer"]').on("click", function() {
        var value = $(this).val()
        console.log(value)
        if (value == 1) {
            $('.footer_invoice_height').attr("disabled", true)
            $('.footer_invoice_height').val("0")
        } else if (value == 2) {
            $('.footer_invoice_height').removeAttr("disabled")
        } else {
            $('.footer_invoice_height').removeAttr("disabled")
        }
    })
})
    </script>
    <script>
function add_parameter() {

    // alert("this is the clinic_lab_package_id  ");
  
    var content= $("#content").val();
    var investigation_name=  $("#inv_namee").val();
    var units_value =  $("#units_value").val();
    var package_id = $("#package_id").val();
    var range_value = $("#range_value").val();


    if(investigation_name == '')
    {
        alert("Enter parameter name");
    }else if(content == '')
    {
        alert("Enter Content");
    }
    else{
    $.ajax({
        url: '<?=base_url()?>LabNew/add_paramters',
        type: 'POST',
        data: {
            package_id:package_id,
            range:range_value,
            investigation_name: investigation_name,
            unit: units_value,
            content:content
        },

        success: function(response) {
            // console.log(response);
            alert("parameter successfully added");
        },

        error: function() {

            alert("Please try again");
        }

    })
    }

}
    </script>
    <script>
function delete_line_items(id) {
    // alert(id);
    $.ajax({
        url: '<?=base_url()?>LabNew/delete_package_line_items',

        type: 'POST',
        data: {
            clinic_lab_package_line_item_id: id,
        },
        success: function(response) {
            // console.log(response)

            alert("successfully deleted")
        },
        error: function() {

            alert("error");
        }

    })

}
    </script>
    <script>
    $(document).on("click",".add_param",function(){
        var title = $(this).attr('data')
        var package_id = $(this).attr('data-value')
        $('#add_paramter_popup .modal-title').html(title)
        $(".modal-body #package_id").val(package_id);
    })
    </script>

  <script>
    $(document).on("click",".edit_param",function(){
        var title = $(this).attr('data-value')
        var clinic_lab_package_line_item_id = $(this).attr('data')

        $('#edit_parameter_popup .modal-title').html(title);
        $(".modal-body #package_id").val(package_id);
    })
    </script>

    <script>

    </script>

    <script>

function inv_settings_edit(inv_id) {

    var fire = 0;
    $('#settings_modal_popup').html('')

    $.ajax({
        url: '<?=base_url()?>LabNew/settings_pop_up',
        type: 'POST',
        data: {
            clinic_package_id: inv_id,
        },

        success: function(response) {

            console.log(response);
            var html = "";

            var popuparray = response.split("*$");

            fire = JSON.parse(popuparray[1]);
            console.log(fire);
            if (popuparray[0] == 0) {
                html =
                    '<div class="modal-body text-center"><h4 class="text-center">Investigation Not Found/Invalid.</h4><button class="btn btn-app text-center" data-dismiss="modal">Close</button></div>';
            } else if (popuparray[0] == 1) {
                var clinic_package_id = fire[0].clinic_lab_package_id;
                $("#clinic_lab_package_id").val(fire[0].clinic_lab_package_id)

                // html += ' <div class="modal-content">';
                html += ' <div class="modal-header">';
                html += ' <h3 class="modal-title text-white">' + fire[0].package_name + '</h3>';
                html += '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                html += ' </div>';
                html += ' <div class="modal-body">';
                html += '<div class="row col-12">';
                html += '<div class="col-4">';
                html += '<label for="male" >Edit Price</label>';
                html +=
                    '<input id="price_upate" type="text" name="price_updat" onkeypress="return numeric()" class="form-control" value="' +
                    fire[0].price + '">';
                html += '</div>';
                // html += '<div class="col-2"><button class="btn btn-primary mt-4" id="report_submit" onclick="price_update_btn(' +
                //     clinic_package_id + ')" data-dismiss="modal" >SUBMIT</button></div>';
                html += '<div class="col-8 pull-right"><button class="btn btn-primary mt-4 mr-2" id="report_submit" onclick="price_update_btn(' + clinic_package_id + ')" data-dismiss="modal" >SUBMIT</button><button class="btn btn-primary mt-4 add_param" data-dismiss="modal" data-target="#add_paramter_popup" data-toggle="modal" data="'+fire[0].package_name+'" data-value="'+clinic_package_id+'">Add New Parameter</button></div>';
                html += '</div>';
                html += '<table class="customTable invTbody">';
                html += '<thead>';


                html += '<tr class="font-weight-bold">';
                // html += '<th>#</th>';
                html += '<th>investigation name</th>';
                html += '<th>Actions</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody class="investigations_tbody">';

                for (var i = 0; i < fire.length; i++) {
                    html += '<tr data-id="' + fire[i].clinic_lab_package_line_item_id + '" data-value="' +
                        clinic_package_id + '" class="invTr">';
                    // html += '<td class="index">' + (parseInt(i)+parseInt(1)) + '</td>';
                    html += '<td>' + fire[i].investigation_name + '</td>';
                    html +='<td><button class="btn btn-primary edit_param" style="background-color: Transparent; background-repeat:no-repeat; border: none;cursor:pointer;" value="'+fire[i].clinic_lab_package_line_item_id+'" data="'+fire[i].clinic_lab_package_line_item_id +'" data-value="'+fire[0].package_name+'" data-toggle="modal" data-dismiss="modal" data-target="#edit_parameter_popup" onclick=edit_parameter_data('+fire[i].clinic_lab_package_line_item_id +')>Edit</button>';
                    html +=
                        '<button class="btn btn-danger" style="margin-left: 20px;background-color: Transparent; background-repeat:no-repeat; border: none;cursor:pointer;" data-dismiss="modal" value=' 
                        +fire[i].clinic_lab_package_line_item_id 
                        +' onclick=delete_line_items(this.value)>Delete</button></td>';
                    // html +='<button style="background-color: Transparent; background-repeat:no-repeat; border: none;cursor:pointer;  color:blue;"  value=' 
                    //     +fire[i].clinic_lab_package_line_item_id 
                    //     +' data="'+
                    //     fire[i].clinic_lab_package_line_item_id +'" data-value="'
                    //     +fire[0].package_name+'" data-toggle="modal" data-target="#edit_parameter_popup" onclick=edit_parameter_data('+
                    //     fire[i].clinic_lab_package_line_item_id +') class="edit_param"><i class="fas fa-edit"></i></button></td>';
                    // html +='<button class="btn btn-primary" style="margin-left: 20px;background-color: Transparent; background-repeat:no-repeat; border: none;cursor:pointer;" value="'+fire[i].clinic_lab_package_line_item_id+'" data="'+fire[i].clinic_lab_package_line_item_id +'" data-value="'+fire[0].package_name+'" data-toggle="modal" data-dismiss="modal" data-target="#edit_parameter_popup" onclick=edit_parameter_data('+fire[i].clinic_lab_package_line_item_id +') class="edit_param">Edit</button></td>';
                    // html +=
                    //     '<td><button style="background-color: Transparent; background-repeat:no-repeat; border: none;cursor:pointer;  color:red;" data-dismiss="modal" value=' 
                    //     +fire[i].clinic_lab_package_line_item_id 
                    //     +' onclick=delete_line_items(this.value)><i class="fas fa-trash"></i></button><button style="background-color: Transparent; background-repeat:no-repeat; border: none;cursor:pointer;  color:blue;" data-dismiss="modal" value=' 
                    //     +fire[i].clinic_lab_package_line_item_id 
                    //     +' onclick=edit_parameter_data(this.value)  data="'
                    //     +fire[i].clinic_lab_package_line_item_id +'" data-value="'
                    //     +fire[0].package_name+'"  class="edit_param" ><i class="fas fa-edit"></i></button></td>';
                    html += '</tr>';
                    // <button class="btn btn-primary mt-4 add_param" data-dismiss="modal" data-target="#add_paramter_popup" data-toggle="modal" data="'+fire[0].package_name+'" data-value="'+clinic_package_id+'">Add Parameter</button>
                    // html +='<td><button style="background-color: Transparent; background-repeat:no-repeat; border: none;cursor:pointer;  color:red;" data-dismiss="modal" value=' +
                    //     fire[i].clinic_lab_package_line_item_id +
                    //     'onclick=delete_line_items(this.value)><i class="fas fa-trash"></i></td>';
                    // html += '</tr>';
                }
                html += '</tbody>';
                html += '</table>';
                html += '<div class="modal-footer">';
                html +=
                    '<button class="btn btn-success" style="display: none;" id="print_btn"><i class="fa fa-print" aria-hidden="true"></i> PRINT REPORT</button>';
                html += '<div class="col-2"><button class="btn btn-primary mt-4" id="report_submit" onclick="price_update_btn(' +
                    clinic_package_id + ')" data-dismiss="modal" >SUBMIT</button></div>';
                html += '</div>';
                // html += '</div>';
                html += '</div>';
            } else if (popuparray[0] == 2) {
                var clinic_package_id = fire.clinic_lab_package_id;
                $("#clinic_lab_package_id").val(fire.clinic_lab_package_id)

                // html += ' <div class="modal-content">';
                html += ' <div class="modal-header">';
                html += ' <h3 class="modal-title text-white">' + fire.package_name + '</h3>';
                html += '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                html += ' </div>';
                html += ' <div class="modal-body">';
                // html += '<div class="row col-12">';
                // html += '<div class="col-10">';
                // html += '<label for="male" >Edit Price</label>';
                // html +=
                //     '<input id="price_upate" type="text" name="price_updat" onkeypress="return numeric()" class="form-control" value="' +
                //     fire.price + '">';
                // html += '</div>';
                // html +=
                //     '<div class="col-2"><button class="btn btn-primary mt-4" id="report_submit" onclick="price_update_btn(' +
                //     clinic_package_id + ')" data-dismiss="modal" >SUBMIT</button></div>';
                // html += '</div>';
                
                html += '<div class="row col-12">';
                html += '<div class="col-4">';
                html += '<label for="male" >Edit Price</label>';
                html +=
                    '<input id="price_upate" type="text" name="price_updat" onkeypress="return numeric()" class="form-control" value="' +
                    fire.price + '">';
                html += '</div>';
                // html += '<div class="col-2"><button class="btn btn-primary mt-4" id="report_submit" onclick="price_update_btn(' +
                //     clinic_package_id + ')" data-dismiss="modal" >SUBMIT</button></div>';
                html += '<div class="col-8 pull-right"><button class="btn btn-primary mr-2 mt-4" id="report_submit" onclick="price_update_btn(' + clinic_package_id + ')" data-dismiss="modal" >SUBMIT</button><button class="btn btn-primary mt-4 add_param" data-dismiss="modal" data-target="#add_paramter_popup" data-toggle="modal" data="'+fire.package_name+'" data-value="'+clinic_package_id+'">Add New Parameter</button></div>';
                html += '</div>';
                html += '<table class="customTable">';
                html += '<thead>';


                html += '<tr class="font-weight-bold">';
                html += '<th>investigation name</th>';
                html += '<th>Actions</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';

                html += '<tr><td colspan="3" class="text-center">No Parameters Found.</td></tr>'

                html += '</tbody>';
                html += '</table>';
                html += '<div class="modal-footer">';
                html +=
                    '<button class="btn btn-success" style="display: none;" id="print_btn"><i class="fa fa-print" aria-hidden="true"></i> PRINT REPORT</button>';
                html += '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
                html += '</div>';
                // html += '</div>';
                html += '</div>'
            } else {
                html =
                    '<div class="modal-body text-center"><h4 class="text-center">Error Occured. Please Refresh the page and try again</h4><button class="btn btn-app text-center" data-dismiss="modal">Close</button></div>';
            }


            $("#settings_modal_popup").html(html);

            $('.invTbody tbody').sortable({
                delay: 150,
                stop: function() {
                    var selectedData = new Array();
                    var clinic_package_id = $('.invTr').attr('data-value');
                    $('.invTr').each(function() {
                        selectedData.push($(this).attr("data-id"));
                    });
                    updateOrder(selectedData, clinic_package_id);
                }
            })

            // $('.invTbody tbody').sortable({
            //     helper: fixHelperModified,
            //     stop: updateIndex
            // }).disableSelection();

        },
        error: function() {

            alert("error");
        }
    });
}

function updateOrder(data, clinic_package_id) {
    console.log(data)
    console.log(clinic_package_id)
    $.post("<?= base_url('LabNew/updatePositions') ?>", {
        positions: data
    }, function(res) {
        console.log(res)
    })
}
    </script>
    <script>

function add_parameterr(a)
{
    
    $("#exampleModall").modal('show');
}
    </script>
    <script>
function price_update_btn(id) {

    var price = $("#price_upate").val();
    $.ajax({
        url: '<?=base_url()?>LabNew/price_update',
        type: 'POST',
        data: {
            clinic_lab_package_id: id,
            price: price
        },
        success: function(response) {
            console.log(response)
            if (response == "updated") {
                alert("successfully updated")
            }
            var table2 = $('#second').DataTable();
            table2.ajax.reload();
        },
        error: function() {
            alert("error");
        }
    });

}

function checkEditData()
{
    $("#exampleModal").modal('show');
    // alert(a);

    
}

    function edit_parameter_data(a)
    {
    // alert(a);
    // $("#exampleModal").modal('show');
    $.ajax({
        url: '<?=base_url()?>LabNew/get_paramters_info',
        type: 'POST',
        data: {
            clinic_lab_package_line_item_id: a
        },
        success: function(response) {

            clinic_lab_template_data = JSON.parse(response);
            console.log(clinic_lab_template_data);

            edit_data.innerHTML ='';
            // edit_data.innerHTML = '<div class="row"></div>';
            edit_data.innerHTML += '<div class="row col-12">';
            edit_data.innerHTML += '<div class="col-6">';
            edit_data.innerHTML += '<label for="male" >Investigation Name</label>';
            edit_data.innerHTML +=
                    '<input id="edit_inv_name" type="text" name="edit_inv_name"  class="form-control" value="' +
                    clinic_lab_template_data.investigation_name + '">';
                    edit_data.innerHTML += '</div>';
                    if(clinic_lab_template_data.normal_range != '')
                   {
                    edit_data.innerHTML += '<div class="col-6">';
                    edit_data.innerHTML += '<label for="male" >Normal Range</label>';
                    edit_data.innerHTML += '<textarea id="edit_normal_range" type="text" name="normal_range"  class="form-control summernote">' +
                    clinic_lab_template_data.normal_range + '</textarea></div>';
                   }
                    if(clinic_lab_template_data.units != '')
                   {
                    edit_data.innerHTML += '<div class="col-6">';
                    edit_data.innerHTML += '<label for="male" >units</label>';
                    edit_data.innerHTML += '<textarea id="edit_units" type="text" name="units"  class="form-control summernote">' +
                    clinic_lab_template_data.units + '</textarea>';
                    edit_data.innerHTML += '</div>';
                   }
                   if(clinic_lab_template_data.dropdowns != '')
                   {
                    edit_data.innerHTML += '<div class="col-6">';
                    edit_data.innerHTML += '<label for="male" >DropDowns</label>';
                    edit_data.innerHTML += '<input id="edit_dropdowns" type="text" name="dropdowns"  class="form-control" value="' +
                    clinic_lab_template_data.dropdowns + '">';
                    edit_data.innerHTML += '</div>';
                   }
                   if(clinic_lab_template_data.content != '')
                   {
                    edit_data.innerHTML += '<div class="col-6">';
                    edit_data.innerHTML += '<label >Content</label>';
                    edit_data.innerHTML += '<textarea id="edit_content" name="content"  class="form-control summernote">' +
                    clinic_lab_template_data.content + '</textarea>';
                    edit_data.innerHTML += '</div>';
                   }
                  
                edit_data.innerHTML += '<div class="modal-footer">';
               
                    edit_data.innerHTML += '<button class="btn btn-primary mt-4 pull-right" onclick="submitData('+a+')" data-dismiss="modal" >SUBMIT</button>';
                    edit_data.innerHTML += '</div>';
                // html += '</div>';
            // $("#myInput").val(clinic_lab_template_data.investigation_name);
            
      $('.summernote').summernote({
            toolbar: []
      });
            
        },
        error: function() {
            alert("error");
        }
    });
    // $.ajax({
    //         alert("1");
    //         url: '<?=base_url()?>LabNew/get_paramters_info',
    //         type: 'POST',
    //         data: {
    //             clinic_lab_package_line_item_id:a,
    //             // range:$("#inv_normal_range").val(),
    //             // investigation_name: $("#inv_namee").val(),
    //             // unit: $("#inv_units").val(),
    //         },

    //         success: function(response) {
    //             alert(response)
    //             // alert("parameter successfully added");
    //         },

    //         error: function() {
    //             alert("Please try again");
    //         }
    //      })
    }
    </script>

    <script>
    function submitData(a)
    {
        // alert(a);
        edit_inv_name= $('#edit_inv_name').val();
        edit_normal_range=$('#edit_normal_range').val();
        edit_dropdowns=$('#edit_dropdowns').val();
        edit_content=$('#edit_content').val();
        edit_units=$('#edit_units').val();

        // alert(edit_inv_name);
        // alert(edit_normal_range);
        // alert(edit_dropdowns);
        // alert(edit_content);
        // alert(edit_units);

        $.ajax({
        url: '<?=base_url()?>LabNew/edit_investigation_data',
        type: 'POST',
        data: {
            clinic_lab_package_line_item_id:a,
            edit_inv_name: edit_inv_name,
            normal_range:edit_normal_range,
            dropdowns:edit_dropdowns,
            content:edit_content,
            units:edit_units
        },
        success: function(response) {
            console.log(response);
            alert('you have successfully edited');
            // var table = $('#second').DataTable();
            // table.ajax.reload();
        },
        error: function() {
            alert("error");
        }
    });
    }
    </script>

    <script>
function adding_search_result() {

    var price = $('#price').val();
    var inv_id = $('#somediv').val();

    if (inv_id > 0) {
        add_search_investigation(inv_id, price);
    } else {
        alert('Select Investigation')
    }
    $('#price').val('');
    $('#somediv').val('');
    $('#search_investigations').val('');
}

function add_search_investigation(inv_id, price) {


    // alert('price is ' + price + 'and' + inv_id)
    var price = $('#price').val();
    if (price != 0) {
        $.ajax({
            url: '<?=base_url()?>LabNew/add_investigaton',
            type: 'POST',
            data: {
                inv_id: inv_id,
                price: price
            },
            success: function(response) {
                console.log(response + "test")
                if (response == 1) {
                    // console.log('undi ra babu')
                    alert("this investigation is already added")
                } else {
                    console.log('ledu')
                    var table2 = $('#second').DataTable();
                    table2.ajax.reload();
                    alert("successfully added");
                }


            },
            error: function() {
                alert("error");
            }
        });
    } else {
        alert('please add proper price')
    }
}
    </script>

    <script>
function add_investigation(val) {
    // alert(val);
    var price = $('#inv_price_' + val).val();

    // console.log(price)

    if (price != 0) {
        $.ajax({
            url: '<?=base_url()?>LabNew/add_investigaton',
            type: 'POST',
            data: {
                inv_id: val,
                price: price
            },
            success: function(response) {

                if (response == 1) {
                    // console.log('undi ra babu')
                    alert("this investigation is already added")
                } else {

                    console.log('ledu')
                    var table2 = $('#second').DataTable();
                    table2.ajax.reload();
                    alert("successfully added");
                }


            },
            error: function() {
                alert("error");
            }
        });
    } else {
        alert('please add proper price')
    }
    $('#inv_price_' + val).val('');

}
    </script>
    <!-- delete clinic_investigations -->
    <script>
function delete_clinic_inv(value) {

    alert('Are you sure you want to remove this Investigation');
    $.ajax({
        url: '<?=base_url()?>LabNew/delete_clinic_inv',
        type: 'POST',
        data: {
            cli_inv_id: value
        },
        success: function(response) {
            // alert('success fully deleted');
            var table = $('#second').DataTable();
            table.ajax.reload();
        },
        error: function() {
            alert("error");
        }
    });
}
    </script>
    <!-- delete clinic_investigations -->
    <script>
// first database
$(document).ready(function() {
    $('#first').DataTable({
        "processing": true,
        "ordering": false,
        "info": false,
        "sPaginationType": "full_numbers",
        "lengthChange": false
    });
});

// second table

$(document).ready(function() {
    $('#second').DataTable({
        "sPaginationType": "full_numbers",
        "info": false,
        "lengthChange": false,
        // Processing indicator
        "processing": "Loading. Please wait...",
        'language': {
            "processing": "Loading please wait...",
            searchPlaceholder: "Search clinic investigations",
            "search": "_INPUT_",
        },   
        // DataTables server-side processing mode
        "serverSide": true,
        // Initial no order.
        "order": [],
        // Load data from an Ajax source
        "ajax": {
            "url": "<?=base_url()?>LabNew/Ajax",
            "type": "POST"
        },
        //Set column definition initialisation properties
        "columnDefs": [{
            "targets": [0],
            "orderable": false
        }]
    });

});
    </script>
    <script>
$(document).ready(function() {
    $('#price').keydown(function(e) {
        if (e.keyCode == 13) {
            alert('this is inv_id ' + $('#inv_id').val() + " this is price " + $(this).val());
            var price = $(this).val()
            var inv_id = $('#inv_id').val()
            $(this).val('')
            $('#inv_id').val('')
            $('#search_investigations').focus()
            $.post('<?= base_url('Lab/') ?>', {
                price: price,
                inv_id: inv_id
            }, function(data) {
                console.log(data)
            })
        }
    })
})
    </script>

    <script>
function model(id, inv_name) {


    // $('#search_investigations').val(inv_name)
    $('#inv_id').val(id)
    $('#price').focus()

    $('#invLabel').html('Enter Price for ' + inv_name)

}

function Inv_search(search) {
    // console.log(search);
    // console.log($('#search_investigations').val());
    $('#search_investigations').autocomplete({
        minLength: 2,
        source: function(request, response) {
            $.ajax({
                url: '<?= base_url('LabNew/Investigation_search') ?>',
                type: 'POST',
                data: {
                    search: search
                },
                success: function(result) {
                    response($.parseJSON(result))
                }
            })
        },


        select: function(event, ui) {
            // $('#search_investigations').val(ui.item.investigation);
            var inv_id = ui.item.package_id
            var inv_name = ui.item.package_name
            // $('#search_investigations').val(inv_name);
            var price = $('#price').val();
            // alert("price" + price);
            // add_search_investigation(inv_id,price)
            // console.log(inv_name)
            // alert('mater'+inv_name);
            // $('#search_investigations').val(inv_name);
            // // $('#search-user-id').val(username);
            // $('#search_investigations').focus();
            // $("#search_investigations").val(inv_id);
            $("#inv_name").val(inv_name);
            $("#somediv").val(inv_id);
            event.preventDefault();
            $("#search_investigations").val(inv_name);
            // $(this).autocomplete("#search_investigations", inv_name);
            // $('#search_investigations').val(inv_name)
            model(inv_id, inv_name);
            // add_search_investigation(inv_id, price);
            $("#price_input").show();
        },

        // focus: function(event, ui) {
        //     console.log('Focus='+ui.item.investigation);
        // // event.preventDefault();
        // $('#search_investigations').val(ui.item.investigation);
        // }


        create: function() {
            $(this).data('ui-autocomplete')._renderItem = function(ul, item) {
                console.log(item)

                return $('<li>')
                    .append(
                        '<a value="' + item.package_id +
                        '" id="searching"><div class="inline-block srchRes w-100"><div class="row"><div class="col-md-12"><p class="m-0 p-0 font-weight-light trade_name">' +
                        item.package_name + '</div></a>')
                    .appendTo(ul);
            };
        }
    });
}
    </script>



    <script>

    </script>