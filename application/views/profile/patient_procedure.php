<style type="text/css">
  .radio label::after{
    top:10px !important;
  }
</style>
<div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?php echo $_SESSION['clinic_name']; ?></div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                              
                                <li class="active">Profile</li>
                            </ol>
                        </div>
                    </div>
<section class="main-content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">

      <?php if($app_info->appointment_id!="") { $this->load->view('profile/appointment_info_header'); } ?>
        <div class="row col-md-12" style="margin-top: 20px"> 
          <div class="col-md-3" id="view_casesheet">
		  <div class="col-md-12">
		  
			<div class="form-group ulgroup" >
				<?php $this->load->view('profile/patient_info_left_nav'); ?>
			</div>
		 </div>
		  
		  </div>
          <div class="col-md-9" id="" class="">
		  <div class="card">
        <div class="row col-md-12" style="padding: 10px" >
          <div class="col-md-12"> 
     <form method="post" id="patient_form" action="<?php echo base_url('Patients/save_collectPayment/'.$patient_id.'/'.$appoinment_id); ?>" autocomplete="off" enctype="multipart/form-data" class="form customForm" name='Registration' >  
                   <div class="col-md-8">
                
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-10">
                                <div class="form-group">
                                    <label for="">Add procedure</label>
                                    <select id="procedure_select" name="procedure_select" class="form-control select2">
                                        <option value="">--Select Procedure--</option>
                                        <?php

                                        foreach ($procedures as  $value) { ?>
                                            <option data-val="<?php echo $value->procedure_cost; ?>" value="<?php echo $value->clinic_procedure_id; ?>"><?php echo $value->procedure_name; ?></option>
                                       <?php }
                                        ?>
                                    </select>
                                </div>
                            </div>
  

                            <div class="col-md-12 mt-2 mb-2 p-2" id="pos-customer-details"></div>

                        </div>

                        <div class="row">
                            <table class="table table-condensed" id="cart-table">
                                <tbody><tr>
                                    <th width="80%">Procedure</th>
                                    <th width="20%">Price</th>
                                   
                                </tr>

                           </tbody></table>
                        </div>
          
                   
                        <div class="row pos-calculations">
                            <div class="col-md-6 border-bottom">
                                Sub Total                            </div>
                            <div class="col-md-6 border-bottom" id="cart-sub-total">0</div>
                            <div class="col-md-6 border-bottom">
                                <h6>Discount <select onchange="calculateTotal();" id="discount_type" name="discount_type" style="padding-left: 10px;margin-left: 10px">
                                                <option>INR</option>
                                                <option>%</option>
                                              </select></h6>
                            </div>
                            <div class="col-md-6 border-bottom">
                                <input type="number" id="cart-discount" name="cart_discount_0" class="form-control" step=".01" min="0" value="0" style="width: 70px">
                            </div>

                            <div class="col-md-6">
                                <h4>Total</h4>
                            </div>
                            <div class="col-md-6">
                                <h4 id="cart-total">0</h4>
                                <input type="hidden" id="cart-total-input" value="35.40">
                            </div>
                        </div>
                        <hr>
              <div class="row col-md-12">
                        <div class="col-md-6">
                          PAYMENT MODE<br>
                          <select class="form-control" name="payment_mode" id="payment_type">
                            <option value="cash">Cash</option>
                            <option value="cheque">Card</option>
                            <option value="online">Online</option>
                            <option value="paytm">Paytm</option>
                            <option value="googlepay">Google Pay</option>
                          </select>
                        </div>
                      </div>
                        <div class="col-md-4 offset-md-6">
                    <input type="submit" class="btn btn-primary" value="Save & Create Invoice" />
                </div>
                    </div>

               
            </div>
      
        
        </form>
        </div>
      </div>
    </div>
		  </div>
        </div>
      </div>
    </div>
  </div>

</section>

   <script type="text/javascript">
      $(function(){
    // turn the element to select2 select style
      $('#procedure_select').select2({
  theme: "bootstrap",
    allowClear: true,
    placeholder: "select procedure"
});

    $('#procedure_select').on('change', function() {
        var procedureName = $("#procedure_select option:selected").text();
        var procedureId = $("#procedure_select option:selected").val();
         var procedureAmount = $("#procedure_select option:selected").attr("data-val");
         if(procedureId!=""){
         var isAdded = checkExists(procedureId);
         if(isAdded == 0){
         var cartRow =  '<tr>\n' +
                    '                                <td><input type="hidden" class="cart-service-'+procedureId+'" name="cart_services[]" value="'+procedureName+'">'+procedureName+'</td>\n' +
                    '                                <td><input type="hidden" name="cart_prices[]" class="cart-price-'+procedureId+'" value="'+procedureAmount+'">'+procedureAmount+'</td>\n' +
                    '   <td>\n' +
                    '                                    <a href="javascript:;" class="btn btn-danger btn-sm btn-circle delete-cart-row" data-toggle="tooltip"\n' +
                    '                                      data-original-title="Delete"><i class="fa fa-times"\n' +
                    '                                                                                                   aria-hidden="true"></i></a>\n' +
                    '                                </td>\n' +
                    '                            </tr>';

                $("#cart-table").append(cartRow);
                 $('#procedure_select').val('').trigger("change");
                // $("#procedure_select").select2("destroy");

                // $("#procedure_select").select2({
                //     placeholder: "Select procedure"
                // });

                calculateTotal();
            }
        }
    })
  });
  $('#cart-discount').keyup(function () {
            calculateTotal();
        });
  $('#cart-table').on('click', '.delete-cart-row', function () {
            $(this).closest('tr').remove();
            calculateTotal();
        });
function calculateTotal() {
            var cartTotal = 0;
            var cartSubTotal = 0;
            var cartDiscount = $('#cart-discount').val();
            var discount = 0;
            if(cartDiscount == ""){
                cartDiscount = 0;
            }
            $("input[name='cart_prices[]']").each(function( index ) {
                var Price = $(this).val();
                cartSubTotal = (cartSubTotal + (parseFloat(Price)));
            });

            $("#cart-sub-total").html(cartSubTotal.toFixed(2));

             if($("#discount_type option:selected").val() == 'INR'){
      var totalPrice = cartSubTotal - parseInt(cartDiscount);
     }else{
       var totalPrice = cartSubTotal - (cartSubTotal * parseInt(cartDiscount)/100);
     }
            $("#cart-total").html(totalPrice.toFixed(2));
           
          
        }
        function checkExists(pId) {
         
            var isAdded = $(".cart-service-"+pId).length;
            if(isAdded > 0){
                alert("procedure already added to list");
                 $('#procedure_select').val('').trigger("change");
                return 1;
            }
            return 0;
        }
</script>