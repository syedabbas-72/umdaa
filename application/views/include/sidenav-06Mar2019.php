<style>
</style>
<div class="main-sidebar-nav dark-navigation">
    <div class="nano">
        <div class="nano-content sidebar-nav">
			<ul class="metisMenu nav flex-column" id="menu">
				<li class="nav-heading"><span>MAIN</span></li>
               
<?php
$CI =& get_instance();
$profile_id = $CI->session->userdata('profile_id');
$entities = $CI->db->query("select * from user_entities a inner join profile_permissions b on a.user_entity_id=b.user_entity_id where profile_id=".$profile_id." and parent_id=0 ORDER BY FIELD(category, 'Main', 'Masters', 'Administrations'),position")->result();
//echo $CI->db->last_query();
$msc=0;$amc=0;
foreach($entities as $result)
{
	$sub_chk = $CI->db->query("select * from user_entities a inner join profile_permissions b on a.user_entity_id=b.user_entity_id where parent_id=".$result->user_entity_id." and p_read=1 and profile_id=".$profile_id)->result_array();	
	$cnt = count($sub_chk);
?>
<?php if($result->category=='Masters'&&($msc==0)){ $msc++; ?>

	<li class="nav-heading"><span><u class="under_line">MASTERS</u></span></li>	
<?php }?>
<?php if($result->category=='Administrations'&&($amc==0)){ $amc++; ?>
	<li class="nav-heading"><span><u class="under_line">ADMINISTRATION</u></span></li>	
<?php  }?>
<?php if(accessprofile($result->method_name,P_READ)){ ?>
<li class="nav-item">
	<a class="nav-link" href="<?php echo base_url($result->entity_url); ?>" aria-expanded="false"><i class="fas fa-clinic-medical"></i> &nbsp; <span class="toggle-none"><?php echo $result->user_entity_name; ?><?php if($cnt>0){?><span class="fa arrow"></span></span><?php } ?></a>
	<?php if($cnt>0){?>
		<ul class="nav-second-level nav flex-column " aria-expanded="false">
		<?php foreach($sub_chk as $sresult){ ?>
			<li class="nav-item"><a class="nav-link" href="<?php echo base_url($sresult['entity_url']); ?>"><?php echo $sresult['user_entity_name']; ?></a></li>
		<?php } ?>
		</ul>
	<?php } ?>
</li>
<?php } ?>
  
<?php
}
?>
<li class="nav-item">
	<a class="nav-link"  href="<?php echo base_url('Authentication/logout'); ?>" aria-expanded="false"><img src="<?php echo base_url('assets/img/Profiles.png'); ?>"> &nbsp; <span class="toggle-none">LOGOUT</span></a>
</li>
</ul>
</div>
</div>
</div>