<?php
$id = get_the_ID();
$squadriglia = get_post_meta( $id, 'squadriglia', true );
?>
<p <?php echo get_block_wrapper_attributes(); ?>>
	<?php echo esc_html( $squadriglia ); ?>
</p>
