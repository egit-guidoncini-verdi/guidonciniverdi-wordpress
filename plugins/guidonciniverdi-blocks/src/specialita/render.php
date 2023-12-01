<?php
$id = get_the_ID();
$specialita = get_post_meta( $id, 'specialita', true );
?>
<p <?php echo get_block_wrapper_attributes(); ?>>
	<?php echo esc_html( $specialita ); ?>
</p>
