<style>
.customTable td{
    padding: 1px 15px !important;
}
</style>
<div class="page-bar">
   <div class="page-title-breadcrumb">
      <ol class="breadcrumb page-breadcrumb">
         <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="#">UMDAA HEALTH CARE</a>&nbsp;<i class="fa fa-angle-right"></i></li>
         <li><a class="parent-item active" href="<?php echo base_url("Features"); ?>">Features</a></li>
      </ol>
   </div>
 </div>

<!-- Feature Creation Modal -->
<div class="modal fade" role="dialog" id="addModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add Feature</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12">
            <form action="<?=base_url('Features/features_add')?>" method="post">
              <div class="form-group">
                <label class="control-label font-weight-bold">Feature Name</label> 
                <input type="text" class="form-control" name="feature_name" required >
              </div>
              <div class="form-group">
                <label class="control-label font-weight-bold">Feature Type</label>
                <div class="radioicon radioicon-black">
                    <input type="radio" name="feature_type" id="radio1" value="Module" required>
                    <label for="radio1"> 
                        <span class="fa-stack" style="margin-top:10px"> <i class="fa fa-circle"></i></span> Module
                    </label>
                </div>
                <div class="radioicon radioicon-black">
                    <input type="radio" name="feature_type" id="radio2" value="Functionality" required>
                    <label for="radio2"> 
                        <span class="fa-stack" style="margin-top:10px"> <i class="fa fa-circle"></i></span> Functionality
                    </label>
                </div>
              </div>
              <div class="form-group" id="module">
                <label class="font-weight-bold">Module</label>
                <select class="form-control" name="module">
                  <option selected disabled>Select Module</option>
                  <?php
                  if(count($modules) > 0){
                    foreach($modules as $val){
                      ?>
                      <option value="<?=$val->module_id?>"><?=$val->module_name?></option>
                      <?php
                    }
                  }
                  ?>
                </select>
              </div>
              <div class="form-group text-center">
                <button class="btn btn-primary" name="feature_add">Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Feature Creation Modal Ends -->

<!-- Feature Edit Modal -->
<div class="modal fade" role="dialog" id="editModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Edit Feature</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12">
            <form action="<?=base_url('Features/feature_edit')?>" method="post">
              <div class="form-group">
                <label class="control-label font-weight-bold">Feature Name</label> 
                <input type="hidden" class="form-control" name="feature_id" id="feature_id">
                <input type="text" class="form-control" name="feature_name" id="feature_name" required >
              </div>
              <div class="form-group">
                <label class="control-label font-weight-bold">Feature Type</label>
                <div class="radioicon radioicon-black">
                    <input type="radio" name="features_type" id="radio3" value="Module">
                    <label for="radio3"> 
                        <span class="fa-stack" style="margin-top:10px"> <i class="fa fa-circle"></i></span> Module
                    </label>
                </div>
                <div class="radioicon radioicon-black">
                    <input type="radio" name="features_type" id="radio4" value="Functionality">
                    <label for="radio4"> 
                        <span class="fa-stack" style="margin-top:10px"> <i class="fa fa-circle"></i></span> Functionality
                    </label>
                </div>
              </div>
              <div class="form-group" id="umodule">
                <label class="font-weight-bold">Module</label>
                <select class="form-control" name="umodule" id="umod">
                  <option selected disabled>Select Module</option>
                  <?php
                  if(count($modules) > 0){
                    foreach($modules as $val){
                      ?>
                      <option value="<?=$val->module_id?>"><?=$val->module_name?></option>
                      <?php
                    }
                  }
                  ?>
                </select>
              </div>
              <div class="form-group text-center">
                <button class="btn btn-primary" name="feature_edit">Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Feature Edit Modal Ends -->

<section class="main-content">
    <div class="row">   
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                        <h4 class="page-title">Features <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#addModal">Add Feature</button></h4>
                            <table class="table customTable">
                                <thead>
                                    <tr>
                                        <th>S.No:</th>
                                        <th>Feature Name</th>   
                                        <th>Module Name</th>
                                        <th>Feature Type</th>     
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  <?php 
                                  $i=1; 
                                  foreach ($features_list as $value) { 
                                    $modInfo = $this->Generic_model->getSingleRecord('modules', array('module_id'=>$value->module_id));
                                  ?> 
                                    <tr>
                                      <td><?php echo $i++;?></td>
                                      <td><span><?php echo $value->feature_name; ?></span></td>  
                                      <td><span><?php echo $modInfo->module_name; ?></span></td>    
                                      <td><?php echo $value->feature_type; ?></td>     
                                      <td>
                                        <a data-toggle="modal" data-target="#editModal" class="edit text-primary" id="<?=$value->feature_id?>" data-i="<?=$modInfo->module_id?>" data-mod="<?=$value->feature_type?>" data-value="<?=$value->feature_name?>"><i class="fa fa-edit"></i></a>
                                        <a href="<?php echo base_url('Features/feature_delete/'.$value->feature_id);?>" onClick="return doconfirm();"><i class="fa fa-trash"></i></a>
                                      </td>
                                    </tr>
                                  <?php 
                                } ?>
                               
                                    
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
  $(document).on("click", ".edit", function(){
        var id = $(this).attr('id')
        var text = $(this).attr('data-value')
        var featType = $(this).attr('data-mod')
        var module_id = $(this).attr('data-i')

        $('#feature_id').val(id)
        $('#feature_name').val(text)
        
        if(featType == "Module"){
          $('#umodule').show()
          $('#umod option[value="'+module_id+'"]').attr('selected', 'selected')
        }
        else{
          $('#umodule').hide()
        }

        $('input[name=features_type][value='+featType+']').attr('checked','checked')
    })
</script>
 <script>
  $(document).ready(function () {
    $('.table').dataTable();
    
    $('#module').hide()
    $('#umodule').hide()

    $('input[name=feature_type]').on("click", function(){
      var value = $(this).val()
      if(value == "Module"){
        $('#module').show()
      }
      else{
        $("#module").hide()
      }
    })

    $('input[name=features_type]').on("click", function(){
      var value = $(this).val()
      if(value == "Module"){
        $('#umodule').show()
      }
      else{
        $("#umodule").hide()
      }
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



 