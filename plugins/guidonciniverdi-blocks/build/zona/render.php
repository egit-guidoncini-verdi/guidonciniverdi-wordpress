<?php
$id = get_the_ID();
$zona = get_post_meta( $id, 'zona', true );
?>
<p <?php echo get_block_wrapper_attributes(); ?>>
	<?php echo esc_html( $zona ); ?>
</p>
