
<!-- Create Template Modal Starts -->
<div class="modal" id="createTemplate">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Create Template</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <form action="<?=base_url('Bulksms/addTemplate')?>" method="post">
          <div class="form-group">
            <label class="col-form-label">Message</label>
            <textarea class="form-control" placeholder="Enter Message" name="message" required="" style="height: 150px"></textarea>
          </div>
          <div class="form-group text-center">
            <button class="btn btn-primary" name="submit">Save Template</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>
<!-- Template Modal Ends -->

<!-- Edit Template Modal Starts -->
<div class="modal" id="editTemplate">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Edit Template</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <form action="<?=base_url('Bulksms/editTemplate')?>" method="post">
          <div class="form-group">
            <label class="col-form-label">Message</label>
            <input type="hidden" name="edit_template_id" class="edit_template_id" >
            <textarea class="form-control edit_message" placeholder="Enter Message" name="edit_message" required="" style="height: 150px"></textarea>
          </div>
          <div class="form-group text-center">
            <button class="btn btn-primary" name="submit">Update</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>
<!-- Template Modal Ends -->

<!-- Bulk Upload Modal Starts -->
<div class="modal" id="BulkModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Bulk Upload</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <form action="<?=base_url('Bulksms/save')?>" method="post" enctype="multipart/form-data">
          <div class="form-group">
            <label class="col-form-label">Choose Excel File</label>
            <input type="file" name="userfile" required="" class="form-control" accept=".xlsx,.xls">
          </div>
          <p style="font-style: italic;">Please <a target="blank" href="<?=base_url('assets/img/bulksms.jpg')?>">Click Here</a> to view the format of excel sheet to be uploaded.</p>
          <div class="form-group text-center">
            <button class="btn btn-primary" name="importfile">Import</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>
<!-- Bulk Upload Modal Ends -->

<!-- Patient Number Modal Starts -->
<div class="modal" id="NumberModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Patients</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <p class="trade_name">Check Required Numbers</p>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <input type="checkbox" name="" class="patient_numbers pn_main" value=""> Select ALL
          </div>
        </div>
        <div class="row">
        <?php
        foreach ($patientsData as $value) {
          $mobileNumbers[] = DataCrypt($value->mobile,'decrypt');
          ?>
          <div class="col-md-3">
              <input type="checkbox" name="" class="patient_numbers pn_sub" value="<?=DataCrypt($value->mobile,'decrypt')?>"> <?=ucwords(strtolower($value->first_name))?>
          </div>
          <?php
        }
        ?>
        <input type="hidden" class="mobilenmbrs" value="<?=implode(",",$mobileNumbers)?>">
        </div>
        <div class="row ">
          <div class="col-md-12 text-center">
            <button class="btn btn-success selectNumbers">Submit</button>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- Patient Number Modal Ends -->

<section class="main-content">
  <div class="row">               
    <div class="col-md-12">
        <h4 class="page-title" style="border: none !important;">Bulk SMS</h4>  
        <div class="row">
          <div class="col-md-6">
            <div class="panel panel-primary">
              <div class="panel-heading" style="padding-bottom: 0px">Send SMS</div><hr style="margin-top: 5px;margin-bottom: 0px">
              <div class="panel-body" style="padding-top: 0px;">
                <form method="post" action="<?=base_url('Bulksms/sendbulkSMS')?>">
                  <div class="form-group">
                    <label class="col-form-label">To</label>
                    <div class="row">
                      <div class="col-md-10">
                        <?php
                        if(isset($_GET['q']))
                        {
                          $numbers = $this->db->select("mobile")->from("bulksms_numbers")->where("bks_key='".$_GET['q']."'")->get()->result();
                          foreach ($numbers as $value) {
                            $numbers_array[] = $value->mobile;
                          }
                          $numbers_str = implode(",",$numbers_array);
                          ?>
                          <input type="hidden" name="bks_key" value="<?=$_GET['q']?>">
                          <?php
                        }
                        ?>
                        <input class="form-control" type="text" onkeypress="return SMSnumeric()" value="<?=($numbers_str!='')?$numbers_str:''?>" name="mobile" placeholder="Ex: 9XXXXXX999, 8XXXXXX888">
                      </div>
                      <div class="col-md-1">
                        <a class="pull-right btn btn-xs btn-sm" data-toggle="modal" data-target="#NumberModal" style="margin-top: 3px"  title="Group Contacts"><i class="fa fa-user"></i></a>
                      </div>
                      <div class="col-md-1">
                        <a class="pull-right btn btn-xs btn-sm" data-toggle="modal" data-target="#BulkModal" style="margin-top: 3px" title="Upload File"><i class="fa fa-paperclip"></i></a>
                      </div>
                    </div>
                  </div>
                  <?php
                  $sess_role_id = $this->session->userdata('role_id');
                  $roles = $this->db->select("role_id")->from("roles")->where("role_name='Doctor'")->get()->row();

                  //if doctor
                  if($roles->role_id!=$sess_role_id)
                  {
                    ?>
                    <div class="form-group">
                      <label class="col-form-label">Doctors</label>
                      <select class="form-control" name="doctor" required="">
                        <option selected disabled="">Select Doctor</option>
                        <?php
                        foreach ($doctors as $value) {
                          ?>
                          <option value="<?=$value->doctor_id?>">Dr. <?=$value->first_name." ".$value->last_name?></option>
                          <?php
                        }
                        ?>
                      </select>
                    </div>
                    <?php
                  }
                  else
                  {
                    ?>
                    <input type="hidden" name="doctor" value="<?=$this->session->userdata('user_id')?>">
                    <?php
                  }
                  ?>
                  <div class="form-group">
                    <div style="float: right;right: 0">
                      <span class="sms">SMS Count : 0</span>&nbsp;
                      <span class="chars">Chars Count : 0</span>
                      <input type="hidden" name="credits" class="credits">
                    </div>
                    <label class="col-form-label">Message</label>
                    <textarea class="form-control message" style="height: 150px;" required="" name="message"></textarea>
                  </div>
                  <div class="form-group text-center">
                    <button class="btn btn-primary" name="sendsms">Send SMS</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="panel panel-success">
              <div class="panel-heading" style="padding-bottom: 0px">Templates
                <button class="btn btn-success pull-right btn-xs" data-toggle="modal" data-target="#createTemplate"><i class="fa fa-plus"></i> Add</button>
              </div>
              <hr style="margin-top: 5px;margin-bottom: 0px">
              <div class="panel-body" style="padding-top: 0px;">
                <table class="table table-bordered dt-responsive">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Template</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                        <td>1</td>
                        <td>Download application using link https://play.google.com/store/apps/details?id=com.patient.umdaa&hl=en</td>                        
                        <td>
                          <a class="tmplate" data-id="Download application using link https://play.google.com/store/apps/details?id=com.patient.umdaa&hl=en"><i class="fa fa-eye"></i></a>
                        </td>
                      </tr>
                    <?php
                    $i=2;
                    foreach ($templates as $value) {
                      ?>
                      <tr>
                        <td><?=$i?></td>
                        <td><?=$value->message?></td>                        
                        <td>
                          <a class="tmplate" data-id="<?=$value->message?>" id="<?=$value->template_id?>"><i class="fa fa-eye"></i></a>
                          <a class="edit_template" id="<?=$value->template_id?>" data-id="<?=$value->message?>" data-toggle="modal" data-target="#editTemplate"><i class="fa fa-edit"></i></a>
                          <a class="delete_template" id="<?=$value->template_id?>"><i class="fa fa-trash"></i></a>
                        </td>
                      </tr>
                      <?php
                      $i++;
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>            
          </div>
        </div>
    </div>
  </div>
</section>
<script type="text/javascript">
function SMSnumeric()
{
  var charCode = event.keyCode;
  console.log("Character code: "+charCode);
  if ((charCode >= 48 && charCode <= 57) || charCode == 8 || charCode==44)
    return true;
  else
    return false;
}
</script>
<script type="text/javascript">
  $(document).ready(function(){

    $('.pn_main').on("click",function(){
      //select all
      if($(this).is(":checked"))
      {
        $('.pn_sub').prop("checked",true);
      }
      else
      {
        $('.pn_sub').prop("checked",false);
      }
    });

    $('.pn_sub').on("click",function(){
      if($(this).is(":unchecked"))
      {
        $(".pn_main").prop("checked",false);
      }
    });

    //Select NUmbers
    $('.selectNumbers').on("click",function(){
      if($('.pn_main').prop("checked"))
      {
        $('input[name=mobile]').val($('.mobilenmbrs').val());
        $("#NumberModal").modal("hide");
      }
      else
      {
        var values = new Array();
        $.each($('.pn_sub:checked'),function(){
          values.push($(this).val())
        });
        var mobile = values.join(",");
        $('input[name=mobile]').val(mobile);
        $("#NumberModal").modal("hide");
      }
    });

    $('.table').dataTable();

    $('.tmplate').on("click",function(){
      var message = $(this).attr("data-id");
      $(".message").val(message);
      $(".message").focus();
      var length = message.length;
      var sms = 0;
      $('.chars').html("Chars Count : "+length);
      if(length>0 && length<=120)
      {
        sms = 1;
      }
      else if(length>120)
      {
        sms = Math.ceil(length/120);
      }
      $('.sms').html("SMS Count : "+sms);
      $('.credits').val(sms);
    });

    $('.message').on("input",function(){
      var message = $(this).val();
      var length = message.length;
      var sms = 0;
      $('.chars').html("Chars Count : "+length);
      if(length>0 && length<=120)
      {
        sms = 1;
      }
      else if(length>120)
      {
        sms = Math.ceil(length/120);
      }
      $('.sms').html("SMS Count : "+sms);
      $('.credits').val(sms);
    });

    $('.edit_template').on("click",function(){
      var template_id = $(this).attr("id");
      var message = $(this).attr("data-id");
      $('.edit_template_id').val(template_id);
      $('.edit_message').val(message);
    });

    $(".delete_template").on("click",function(){
      var template_id = $(this).attr("id");
      var confirm = window.confirm("Are you sure you want to delete this template ? ");
      if(confirm==true)
      {
        location = "<?=base_url('Bulksms/deleteTemplate/')?>"+template_id;
      }
    });

  });
</script>



 