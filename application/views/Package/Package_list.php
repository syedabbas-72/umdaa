
<div class="page-bar">
   <div class="page-title-breadcrumb">
      <ol class="breadcrumb page-breadcrumb">
         <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="#">UMDAA HEALTH CARE</a>&nbsp;<i class="fa fa-angle-right"></i></li>
         <li><a class="parent-item active" href="<?php echo base_url("Package"); ?>">Packages</a></li>
      </ol>
   </div>
 </div>

 <!-- Package Features Mapping -->
 <div class="modal fade" role="dialog" id="addFeat">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add Features</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
      <div class="container featuresBody">
        <p class="text-center">Getting Data <i class="fas fa-spinner fa-spin"></i></p>
      </div>
    </div>
  </div>
</div>
</div>
 <!-- Package Features Mapping Ends Modal -->

<!-- Pacakge Creation Modal -->
<div class="modal fade" role="dialog" id="addModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add Pacakge</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12">
            <form action="<?=base_url('Package/package_add')?>" method="post">
              <div class="form-group">
                <label class="control-label font-weight-bold">Package Name</label> 
                <input type="text" class="form-control" name="package_name" required >
              </div>
              <div class="form-group">
                <input id="free" type="checkbox" name="free" id="free">
                <label for="free" class="control-label font-weight-bold font-italic">Tick here to make package for free</label>
              </div>
              <div class="payRow">
                <div class="d-flex">
                  <div class="form-group w-100">
                    <label class="control-label font-weight-bold">MRP</label> 
                    <input type="text" class="form-control" name="mrp" id="mrp" onkeypress="return numeric()" >
                  </div>
                  <div class="form-group w-100">
                    <label class="control-label font-weight-bold">Sale Price</label> 
                    <input type="text" class="form-control" name="sale_price" id="sale_price" oninput="return checksp()" onkeypress="return numeric()">
                  </div>
                </div>
                <div class="d-flex">
                  <div class="form-group w-100">
                    <label class="control-label font-weight-bold">Coupon Code</label> 
                    <input type="text" class="form-control" name="coupon">
                  </div>
                  <div class="form-group w-100">
                    <label class="font-weight-bold">Coupon Discount</label>
                    <div class="input-group mb-3">
                      <input type="text" class="form-control" id="coupon_discount" name="coupon_discount" max="100" onkeypress="return numeric()">
                      <div class="input-group-append">
                        <span class="input-group-text" id="basic-addon2">% Off</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-group text-center">
                <button class="btn btn-primary" name="package_add">Submit</button>
              </div>
              
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Package Creation Modal Ends -->

<section class="main-content">
<div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                        <h4 class="page-title">Packages <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#addModal">Add Package</button></h4>
                            <table class="table customTable">
                                <thead>
                                    <tr>
                                        <th>S.No:</th>
                                        <th>Package Name</th>     
                                        <th>MRP</th>                                                                         
                                        <th>Sale Price</th>
                                        <th>Coupon</th>
                                        <th>Coupon Description</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  <?php 
                                  $i=1; 
                                  foreach ($packages_list as $value) { 
                                    $disc = "";
                                    if($value->coupon_discount == "" || $value->coupon_discount == 0){
                                      $disc = "";
                                    }
                                    else{
                                      $disc = $value->coupon_discount." % Off";
                                    }
                                    
                                  ?> 
                                    <tr>
                                      <td><?php echo $i++;?></td>
                                      <td><span><?php echo $value->package_name; ?></span></td>     
                                      <td>Rs. <?php echo $value->mrp; ?> /-</td>     
                                      <td>Rs. <?php echo $value->sale_price; ?> /-</td>     
                                      <td><?=($value->coupon == "")?'':'<span class="code">'.$value->coupon.'</span>'?></td>     
                                      <td><?=$disc?></td>                                      
                                      <td>
                                        <a class="text-primary openpackageFeat" id="<?=$value->package_id?>" data="<?=$featInfo->feat?>" data-toggle="modal" data-target="#addFeat"><i class="fa fa-eye"></i></a>
                                        <!-- <a href="<?php echo base_url('Package/package_update/'.$value->package_id);?>"><i class="fa fa-edit"></i></a> -->
                                        <a href="<?php echo base_url('Package/package_delete/'.$value->package_id);?>" onClick="return doconfirm();"><i class="fa fa-trash"></i></a>
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

<script>
function checksp(){
  var sp = $('#sale_price').val();
  var mrp = $('#mrp').val()
  if(parseInt(sp) > parseInt(mrp)){
    alert('You cannot exceed more than MRP')
    $('#sale_price').val(mrp)
  }
}
</script>
 <script>
  $(document).ready(function () {
    // $('#addFeat').modal()
    $('.table').dataTable();

    $("#free").on("click", function(){
      if($(this).prop('checked') == true){
        $(".payRow").hide()
      }
      else{
        $(".payRow").show()
      }
    });

    $("#coupon_discount").on("input", function(){
      var value = $(this).val();
      if(parseInt(value) >= 100){
        $(this).val('100')
        alert('You cannot exceed more than 100%')
      }
    })

    $('.openpackageFeat').on("click", function(){
      var id = $(this).attr('id');
      $('#f_package_id').val(id)
      $.post("<?=base_url('Package/features_list')?>", {package_id: id}, function(data){
        $('.featuresBody').html(data)
      })
      //console.log(data)
    })

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



 