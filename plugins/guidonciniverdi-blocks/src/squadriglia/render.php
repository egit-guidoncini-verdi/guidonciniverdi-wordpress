<?php
$id = get_the_ID();
$squadriglia = get_post_meta( $id, 'squadriglia', true );
?>
<h1 <?php echo get_block_wrapper_attributes(); ?>>
	<?php echo esc_html( $squadriglia ); ?>
</h1>
