<?php
function guidoncini_build_picture_pattern( $name, $slug ) {
    return array(
	'title' => __( "Immagine per la specialità $name", 'guidonciniverdi' ),
	'inserter' => true,
	'content' => '<!-- wp:group {"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} --> <div class="wp-block-group">' .
		   '<!-- wp:image {"lightbox":{"enabled":false},"linkDestination":"custom"} -->' .
		   '<figure class="wp-block-image"> <a href="' .
		   esc_url( get_site_url() . '/specialita/' . $slug ) .
		   '"> <img src="' . esc_url( get_template_directory_uri() . '/assets/images/' . $slug . '.png' ) .
		   '" alt="' . esc_attr__( 'Immagine per la specialità ' . $name, 'guidonciniverdi' ) .
		   '"/> </a> </figure><!-- /wp:image --> <!-- wp:heading {"level":3} --> <h3 class="wp-block-heading"><a href="' .
		   esc_url( get_site_url() . '/specialita/' . $slug  ) . '">' . esc_html( $name ) .
		   '</a></h3> <!-- /wp:heading --> </div><!-- /wp:group -->'
    );
}

function guidoncini_register_block_patterns() {
    $specialita_terms = array(
	array( 'name' => 'Alpinismo', 'slug' => 'alpinismo' ),
	array( 'name' => 'Artigianato', 'slug' => 'artigianato' ),
	array( 'name' => 'Campismo', 'slug' => 'campismo' ),
	array( 'name' => 'Civitas', 'slug' => 'civitas' ),
	array( 'name' => 'Esplorazione', 'slug' => 'esplorazione' ),
	array( 'name' => 'Espressione', 'slug' => 'espressione' ),
	array( 'name' => 'Giornalismo', 'slug' => 'giornalismo' ),
	array( 'name' => 'Internazionale', 'slug' => 'internazionale' ),
	array( 'name' => 'Natura', 'slug' => 'natura' ),
	array( 'name' => 'Nautica', 'slug' => 'nautica' ),
	array( 'name' => 'Olimpia', 'slug' => 'olimpia' ),
	array( 'name' => 'Pronto Intervento', 'slug' => 'pronto_intervento' )
    );
    foreach( $specialita_terms as $term ) {
	$pattern_array = guidoncini_build_picture_pattern( $term['name'], $term['slug'] );
	register_block_pattern(
	    'guidonciniverdi/' . $term['slug'] . '-picture',
	    $pattern_array
	);
    }
}

add_action( 'init', 'guidoncini_register_block_patterns', 9 );
?>
