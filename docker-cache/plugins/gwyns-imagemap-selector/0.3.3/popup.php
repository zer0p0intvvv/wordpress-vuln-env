<?php
/*
Template Name: Ajax Handler
*/
?>
<?php
	include_once("../../../wp-config.php");

	$post = get_post($_GET['id']);
	$myCSSclass = $_GET['class'];
?>
<?php if ($post) : ?>
	<?php setup_postdata($post); ?>
	<div class="<?php echo $myCSSclass; ?> popup post-id-<?php echo $_GET['id']; ?>">
		<div class="<?php echo $myCSSclass; ?> popup-thumbnail">
		<?php
			if (isset($_GET['thumbnail'])) :
				echo get_the_post_thumbnail($_GET['id'], 'thumbnail', array('class' => 'alignleft popup-thumbnail'));
			endif;
		?>
		</div>
		<div class="<?php echo $myCSSclass; ?> popup-content">
			<h2 class="<?php echo $myCSSclass; ?> popup-title"><?php echo get_the_title(); ?></h2>
		<?php
			if (!isset($_GET['excerpt']))
				echo get_the_content();
			else
				echo '<p>' . get_the_excerpt() . '</p>'; 
		?>
		</div>
	</div>
<?php endif; ?>