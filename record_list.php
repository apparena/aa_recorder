<?php
require_once('init.php');
?>

<h4><?php __p('Recordings'); ?></h4>
<?php $rows=app_record_list($aa_inst_id); ?>
<?php if(count($rows) == 0): ?>
<div class="alert alert-block span9">
<?php __p('Be the first to record something'); ?>
</div>
<?php else: ?>
<table class="table table-striped">
<?php 
$i = 0;
	foreach($rows as $row){
		if ( $i % 2 == 0)
			echo "<tr>";
		?>
			<td><img src="https://graph.facebook.com/<?php echo $row['fb_user_id']; ?>/picture" alt="<?php echo $row['fb_user_name']; ?>" title="<?php echo $row['fb_user_name']; ?>"></td>
			<td><audio src="<?=$row['sound_url']?>" preload="true"></audio></td>
			<?php 
			if ( $i % 2 == 1)
				echo "</tr>";
		$i++;
	} ?>
</table>
<?php endif; ?>

<script>
/*
jQuery('document').ready(function(){
		audiojs.events.ready(function() {
		   var as = audiojs.createAll();
		});

});
*/
</script>
