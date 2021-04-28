<div class="page-bar">
    <div class="page-title-breadcrumb">

        <ol class="breadcrumb page-breadcrumb">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?= base_url('Dashboard') ?>"><?php echo $_SESSION['clinic_name']; ?></a>&nbsp;<i class="fa fa-angle-right"></i>
            </li>
            <li><a class="parent-item" href="#">Patients List</a></li>
        </ol>
    </div>
</div>

<section class="main-content">
    <form method="post" action="<?= base_url('AdminPatients/search') ?>">
        <div class="form-group d-flex">
            <input class="form-control mr-3" type="text" placeholder="Search with Mobile Number / Name" name="search" required>
            <button class="btn btn-app" type="submit"><i class="fa fa-search"></i></button>
        </div>
    </form>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered dt-responsive dataTable no-footer customTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Patient Name - UMR NO.</th>
                                <th>Gender & Age</th>
                                <th>Last Visited Date</th>
                                <th>Location</th>
                                <th style="width: auto !important">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $clinic_id = $this->session->userdata('clinic_id');
                            foreach ($patients as $value) {
                                if ($value->age_unit == "Years")
                                    $age_unit = "Y";
                                elseif ($value->age_unit == "Months")
                                    $age_unit = "M";
                                elseif ($value->age_unit == "Weeks")
                                    $age_unit = "W";
                                elseif ($value->age_unit == "Days")
                                    $age_unit = "D";

                                $appInfo = $this->db->query("select * from appointments where clinic_id='" . $clinic_id . "' and patient_id='" . $value->patient_id . "' order by appointment_id desc")->row();
                            ?>
                                <tr>
                                    <td><?= $i ?></td>
                                    <td><?= ($value->title != "") ? $value->title . ". " : '' ?><?= ucwords(strtolower($value->first_name)) ?><br><span class="code m-0"><?= $value->umr_no ?></span></td>
                                    <td><?= $value->gender ?><?= ($value->age == "") ? '' : "," . $value->age . " " . $age_unit . " Old" ?></td>
                                    <td><span><?= getDoctorName($appInfo->doctor_id) ?></span><br><?= $appInfo->appointment_date ?> @ <?= $appInfo->appointment_time_slot ?></td>
                                    <td><?= ucwords(strtolower($value->location)) ?></td>
                                    <td>
                                        <a href="<?= base_url('profile/index/' . $value->patient_id) ?>"><i class="fa fa-eye"></i></a>
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


</section>

<script>
    $('.edit-row').click(function() {
        var id = $(this).data('row-id');
        var url = 'http://appointo.froid.works/account/booking-times/:id/edit';
        url = url.replace(':id', id);

        $('#modelHeading').html('Edit Booking Times');
        $.ajaxModal('#application-modal', url);
    });

    $.ajaxModal = function(selector, url, onLoad) {

        $(selector).removeData('bs.modal').modal({
            show: true
        });
        $(selector + ' .modal-content').removeData('bs.modal').load(url);

        // Trigger to do stuff with form loaded in modal
        $(document).trigger("ajaxPageLoad");

        // Call onload method if it was passed in function call
        if (typeof onLoad != "undefined") {
            onLoad();
        }

        // Reset modal when it hides
        $(selector).on('hidden.bs.modal', function() {
            $(this).find('.modal-body').html('Loading...');
            $(this).find('.modal-footer').html('<button type="button" data-dismiss="modal" class="btn dark btn-outline">Cancel</button>');
            $(this).data('bs.modal', null);
        });
    };
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('.dataTable').dataTable();
    });
</script>