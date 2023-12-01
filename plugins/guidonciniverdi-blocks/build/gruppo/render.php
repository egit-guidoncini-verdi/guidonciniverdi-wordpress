<?php
$id = get_the_ID();
$gruppo = get_post_meta( $id, 'gruppo', true );
?>
<p <?php echo get_block_wrapper_attributes(); ?>>
	<?php echo esc_html( $gruppo ); ?>
</p>
