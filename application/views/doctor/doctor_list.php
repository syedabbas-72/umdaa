<div class="page-bar">
    <div class="page-title-breadcrumb pull-left">
        <ol class="breadcrumb page-breadcrumb pull-left">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item"
                    href="<?php echo base_url("dashboard"); ?>"><?php echo $_SESSION['clinic_id'] == 0 ? "Umdaa Health Care" : $_SESSION['clinic_name']; ?></a>&nbsp;<i
                    class="fa fa-angle-right"></i>
            </li>
            <li class="active">Doctors</li>
        </ol>
    </div>
    <div class="pull-right" style="padding: 20px 15px 0px 0px;">
        <?php
        if($this->session->userdata("clinic_id")==0)
        {
            ?>
        <!-- <a href="<?php echo base_url('doctor/doctor_add/'); ?>"><i class="fas fa-plus add"></i></a> -->
        <?php
        }
        ?>
    </div>
</div>

<!-- packages Modal -->
<div id="packagesModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Packages Info</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body packagesInfoBody">
		  <p class="text-center">Getting Data <i class="fas fa-spinner fa-spin"></i></p>
      </div>
    </div>

  </div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="card ">
            <div class="card-body">
                <table id="doctorlist" class="table customTable">
                    <thead>
                        <tr>
                            <th>S#</th>
                            <th>Doctor & Qualification</th>
                            <th>Package</th>
                            <th>Email</th>
                            <th>Mobile Number</th>
                            <th>Clinics</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i=1; 
                        foreach ($doctor_list as $value) { 
                            $clinicDoc = $this->db->query("select * from clinic_doctor where doctor_id='".$value->doctor_id."'")->num_rows();
                            $packageInfo = getDocPackage($value->doctor_id);
                            ?>
                        <tr>
                            <td><?php echo $i++;?></td>
                            <td>
                                <p class="trade_name mb-0 p-0"><?=getDoctorName($value->doctor_id)?></p>
                                <span class="m-0 text-uppercase formulation">
                                <?php echo $value->qualification; ?> - <?php echo $value->department_name; ?></span><br>
                                <span><?php echo $value->registration_code; ?></span>
                            </td>
                            <td><?php 
                            if($packageInfo->package_name != ""){
                                ?>
                                <span class="code"><?=$packageInfo->package_name?></span>
                                <?php
                            }
                            ?></td>
                            <td><?php echo strtolower($value->email); ?></td>
                            <td><?php echo $value->mobile; ?></td>
                            <td><?=$clinicDoc?> Clinics</td>
                            <td style="width: 25%;">
                            <a class="mr-1 packageInfo" data-id="<?=$value->doctor_id?>" data-target="#packagesModal" data-toggle="modal" title="Packages Info"><i class="fas fa-info-circle"></i></a>
                            <a href="<?=base_url('Doctor/Profile/'.$value->doctor_id)?>" class="mr-1" title="Edit Doctor Info">
                                <i class="fa fa-edit"></i>
                            </a>
                                <a data-toggle="modal" data-target="#doctorsModal" title="Clinics Info" class="mr-1" onclick="getDocDetails('<?=$value->doctor_id?>','<?=$docName?>')">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="<?=base_url('Doctor/deleteDoc/'.$value->doctor_id)?>" title="Delete Doctor" class="mr-1" onclick="return confirm('Are you sure to delete Doctor?')"><i class="fa fa-trash-alt"></i></a>
                                <input type="hidden" value="citizen.devumdaa.in/doctor_profile/<?=$value->doctor_id?>" id="myInput_<?=$value->doctor_id?>">
                                <a onclick="copyLink('<?=$value->doctor_id?>')" class="mr-1" title="Copy to clipboard"><i class="fa fa-clipboard" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Doctors Modal -->
<div class="modal fade" id="doctorsModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body docBody">
                <p class="text-center">Loading Data&nbsp;<i class="fa fa-spinner fa-spin"></i></p>
            </div>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {
    $('#doctorlist').dataTable();
});
</script>
<script>
// $(document).on("load", ".delLoading").hide();
$(document).ready(function(){
    $('.packageInfo').on("click", function(){
        var id = $(this).attr('data-id')
        $.post("<?=base_url('Doctor/packagesInfo')?>", {doctor_id: id}, function(data){
            // console.log(JSON.parse(data))
			$('.packagesInfoBody').html(data);
        })
    })
})
</script>
<script>
$(document).on("click", ".submitDel", function() {
    var clinics = [];
    $.each($("input[name='clinics']:checked"), function() {
        clinics.push($(this).val());
    });
    if (clinics.length == 0) {
        alert("Please Select Clinic.");
    } else {
        var confirm = window.confirm("Are you sure to continue?");
        if (confirm == true) {
            var docId = $('.docId').val();
            $('#delLoading').removeClass("hidden");
            $.post("<?=base_url('Doctor/DeleteData')?>", {
                id: docId,
                clinics: clinics.toString()
            }, function(data) {
                console.log(data)
                if(data == 1)
                {
                    alert("Successfully Deleted.");
                    location.reload();
                }
                else
                {
                    alert("Error Occured.");
                    location.reload();
                }
            });
        }
    }
});
</script>
<script>
function getDocDetails(id, docName) {
    $('#doctorsModal .modal-title').html(docName);
    $.post("<?=base_url('Doctor/getDocDetails')?>", {
        id: id
    }, function(data) {
        $('.docBody').html(data);
    });
}

function copyLink(doctor_id)
{
    // var copyText = $("#myInput_"+doctor_id);
    // alert(text);
    var copyText = document.getElementById("myInput_"+doctor_id);
    copyText.select();
    // copyText.setSelectionRange(0, 99999); /* For mobile devices */
    document.execCommand("copy");

  alert("Succesfully Copied");
}
</script>