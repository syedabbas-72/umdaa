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
<?php echo $result; ?>
</section>