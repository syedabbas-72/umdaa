

<div class="page-bar">
    <div class="page-title-breadcrumb">
        <ol class="breadcrumb page-breadcrumb">
            <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?php echo base_url("dashboard"); ?>"><?=$this->session->userdata('clinic_name')?></a></li>
            <li class="active">Citizens Tutorial Videos</li>
        </ol>
    </div>
    <div class="pull-right">
          <a href="<?php echo base_url('CitizenTutorials/add');?>" class="btn btn-app mt-3"><i class="fa fa-plus"></i> Add Video</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <table class="customTable dataTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tutorial Title</th>
                            <th>Description</th>
                            <th>Video Thumbnail</th>
                            <th>Video Link</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        foreach($tutorials as $value)
                        {
                            ?>
                        <tr>
                            <td><?=$i?></td>
                            <td><?=$value->tutorial_name?></td>
                            <td><?=$value->tutorial_description?></td>
                            <td>
                                <a href="<?=base_url('uploads/thumbnails/'.$value->video_thumbnail)?>" target="blank">
                                    <img src="<?=base_url('uploads/thumbnails/'.$value->video_thumbnail)?>" class="img-100">
                                </a>
                            </td>
                            <td><a href="https://www.youtube.com/watch?v=<?=$value->tutorial_link?>" target="blank" class="btn btn-app">Show Video</a></td>
                            <td>
                                <a href="<?=base_url('CitizenTutorials/edit/'.$value->umdaa_tutorial_id)?>" class="mr-2"><i class="fa fa-edit"></i></a>
                                <a href="<?=base_url('CitizenTutorials/Delete/'.$value->umdaa_tutorial_id)?>" onclick="return confirm('Are you sure to delete ?')"><i class="fa fa-trash"></i></a>
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
<script>
    $(document).ready(function(){
        $('.dataTable').DataTable();
    });
</script>