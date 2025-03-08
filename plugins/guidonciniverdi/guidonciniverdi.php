<?php

/*
 * Plugin Name: Guidoncini Verdi
 */

if ( ! defined( 'ABSPATH' ) )
    exit( 'Restricted access' );

/*
 * Custom post type navigazione
 */

function guidoncini_navigazione_post_type() {
    $labels = array(
	'name'          => __('Navigazione', 'textdomain')
    );
    $args = array(
	'labels'      => $labels,
	'public'      => true,
	'publicly_queryable' => true,
	'show_ui'            => true,
	'show_in_menu'       => true,
	'show_in_rest'       => true,
	'query_var'          => true,
	'has_archive'        => true,
	'capability_type'    => array( 'post', 'posts' ),
	'map_meta_cap'       => true,
	'show_in_admin_bar'  => true,
	'taxonomies'         => ['category'],
	'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields' ),
    );
    register_post_type('navigazione', $args);
}

add_action('init', 'guidoncini_navigazione_post_type');

/*
 * Restrizione degli utenti
 */

// Solo le squadriglie devono poter accedere alla media library
// Permetti solo a editor e admin (blocca author e contributor)
// di vedere contenuti non caricati da loro
function guidoncini_show_current_sq_attachments( $query ) {
    global $user_ID;
    if ( ! current_user_can( 'edit_others_posts' ) ) {
        $query['author'] = $user_ID;
    }
    return $query;
}
add_filter( 'ajax_query_attachments_args', 'guidoncini_show_current_sq_attachments' );
function guidoncini_show_current_sq_attachments_list( $query ) {
    global $user_ID;
    if ( function_exists( 'get_current_screen')  ) { 
	$screen = get_current_screen(); 
    } else {
	return $query;
    }
    if ( in_array( $screen->id, array( 'upload' ) ) ) {
        if ( ! current_user_can( 'edit_others_posts' ) ) {
            $query['author'] = $user_ID;
        }
    }
    return $query;
}
add_filter( 'request', 'guidoncini_show_current_sq_attachments_list' );

// Impedisci alle squadriglie di vedere articoli non creati da loro
// Permetti solo a editor e admin (blocca author e contributor)
// di vedere articoli non scritti da loro
function guidoncini_show_current_sq_posts( $query ) {
    global $pagenow, $user_ID; 
    /* if ( ('edit.php' != $pagenow && 'upload.php' != $pagenow) || !$query->is_admin )
       return $query; */
    if ( ! current_user_can( 'edit_others_posts' ) ) {
	global $user_ID;
	$query->set( 'author', $user_ID );
    }
    return $query;
}
add_filter( 'pre_get_posts', 'guidoncini_show_current_sq_posts' );

// Impedisci alle squadriglie di eliminare post
// Impedisci al ruolo author di eliminare post
function guidoncini_disable_sq_post_deletion() {
    $role = get_role( 'author' );
    $role->remove_cap( 'delete_posts' );
    $role->remove_cap( 'delete_published_posts' );
}
register_activation_hook( __FILE__, 'guidoncini_disable_sq_post_deletion' );
function guidoncini_enable_sq_post_deletion() {
    $role = get_role( 'author' );
    $role->add_cap( 'delete_posts' );
    $role->add_cap( 'delete_published_posts' );
}
register_deactivation_hook( __FILE__, 'guidoncini_enable_sq_post_deletion' );

// Impedisci alle squadriglie di creare post
// - Mappa la capability create_posts a create_posts nel post type 'post'
// - Impedisci al ruolo di author di creare post
function guidoncini_add_create_posts_cap( $args ) {
    $args['show_in_rest'] = true;
    $args['map_meta_cap'] = true;
    $args['capabilities']['create_posts'] = 'create_posts';
    return $args;
}
add_filter( 'register_post_post_type_args', 'guidoncini_add_create_posts_cap' );
function guidoncini_disable_sq_post_creation() {
    $role = get_role( 'author' );
    $role->remove_cap( 'create_posts' );
}
register_activation_hook( __FILE__, 'guidoncini_disable_sq_post_creation' );
function guidoncini_enable_sq_post_creation() {
    $role = get_role( 'author' );
    $role->add_cap( 'create_posts' );
}
register_deactivation_hook( __FILE__, 'guidoncini_enable_sq_post_creation' );
function guidoncini_enable_admin_post_creation() {
    $role = get_role( 'administrator' );
    $role->add_cap( 'create_posts' );
}
register_activation_hook( __FILE__, 'guidoncini_enable_admin_post_creation' );
function guidoncini_disable_admin_post_creation() {
    $role = get_role( 'administrator' );
    $role->remove_cap( 'create_posts' );
}
register_deactivation_hook( __FILE__, 'guidoncini_disable_admin_post_creation' );

// Impedisci alle sq di accedere alla dashboard
// Impedisci al ruolo author di accedere alla dashboard
function guidoncini_disable_sq_profile_page() {
    $role = get_role( 'author' );
    $role->remove_cap( 'read' );
}
register_activation_hook( __FILE__, 'guidoncini_disable_sq_profile_page' );
function guidoncini_enable_sq_profile_page() {
    $role = get_role( 'author' );
    $role->add_cap( 'read' );
}
register_deactivation_hook( __FILE__, 'guidoncini_enable_sq_profile_page' );
// Redireziona dopo il login, se non si ha accesso a wp-admin
function guidoncini_redirect_sq_on_login( $redirect_to, $requested_redirect_to, $user ) {
    if ( $user && is_object( $user ) && is_a( $user, 'WP_User' ) &&
	 ! user_can($user, 'edit_others_posts') ) {
	// Get the most recent post by the logged-in author
	$argsq = array(
		'author' => $user->ID,
		'posts_per_page' => 1,
	);
	$recent_posts = get_posts($argsq);
	if (!empty($recent_posts)) {
		$redirect_to = get_permalink($recent_posts[0]);
	}
	else {
            $redirect_to = esc_url( get_author_posts_url( $user->ID ) );
	}
    }
    return $redirect_to;
}
add_filter( 'login_redirect', 'guidoncini_redirect_sq_on_login', 10, 3 );

/*
 * Tassonomie e categorie
 */

function guidoncini_list_specialita() {
    return array(
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
}

// Aggiungi le specialità di squadriglia alla tassonomia specialità
function guidoncini_add_specialita_to_taxonomy() {
    // Elenco delle specialità di squadriglia e dei relativi slug
    $guidoncini_elenco_specialita = guidoncini_list_specialita();
    foreach ( $guidoncini_elenco_specialita as $specialita ) {
	if ( ! term_exists( $specialita['slug'], 'specialita' ) ) {
	    $args = array( 'slug' => $specialita['slug'] );
	    wp_insert_term( $specialita['name'], 'specialita', $args);
	}
    }
}

// Crea la tassonomia specialità e popolala
function guidoncini_register_taxonomy_specialita () {
    $labels = array(
	'name' => __( 'Specialità' ),
	'singular_name' => __( 'Specialità' ),
	'search_items' => __( 'Cerca Specialità' ),
	'all_items' => __( 'Tutte le Specialità' ),
	'edit_item' => __( 'Modifica Specialità' ),
	'update_item' => __( 'Aggiorna Specialità' ),
	'add_new_item' => __( 'Aggiungi Specialità' ),
	'new_item_name' => __( 'Nuovo nome Specialità' ),
	'menu_name' => __( 'Specialità' )
    );
    $args = array(
	'hierarchical' => false,
	'labels' => $labels,
	'show_ui' => true,
	'show_admin_column' => true,
	'query_var' => true,
	'rewrite' => true,
	'public' => true,
	'show_in_rest' => true,
	'capabilities' => array(
	    'manage_terms' => 'manage_categories',
	    'edit_terms' => 'manage_categories',
	    'delete_terms' => 'manage_categories',
	    'assign_terms' => 'manage_categories'
	)
    );
    register_taxonomy( 'specialita', [ 'post', 'navigazione' ], $args);
    guidoncini_add_specialita_to_taxonomy();
}
add_action( 'init', 'guidoncini_register_taxonomy_specialita' );

// Aggiungi presentazione, imprese, missione alle categorie
function guidoncini_add_category_terms() {
    $categories = array(
	array( 'name' => 'Presentazione', 'slug' => 'presentazione' ),
	array( 'name' => 'Prima impresa', 'slug' => 'prima_impresa' ),
	array( 'name' => 'Seconda impresa', 'slug' => 'seconda_impresa' ),
	array( 'name' => 'Missione', 'slug' => 'missione' ),
	array( 'name' => 'Navigazione', 'slug' => 'navigazione' ),
	array( 'name' => 'Pagina unica', 'slug' => 'pagina_unica' )
    );
    foreach ( $categories as $category ) {
	if ( ! term_exists( $category['slug'], 'category' ) ) {
	    $args = array( 'slug' => $category['slug'] );
	    wp_insert_term( $category['name'], 'category', $args);
	}
    }    
}
add_action( 'init', 'guidoncini_add_category_terms' );

// Separa assign_terms da edit_posts
function guidoncini_category_args ( $args, $taxonomy ) {
    if ( 'category' != $taxonomy ) {
	return $args;
    }
    $args['capabilities']['assign_terms'] = 'manage_categories';
    return $args;
}
add_filter( 'register_taxonomy_args', 'guidoncini_category_args', 10, 2);

/*
 * Attributi addizionali degli utenti
 */ 

// Registra i meta e rendili disponibili per l'accesso alla REST API
function guidoncini_register_meta () {
    $fields = array(
	'squadriglia' => 'Nome della squadriglia.',
	'specialita' => 'Specialità per la quale la sq sta lavorando.',
	'gruppo' => 'Gruppo della sq.',
	'zona' => 'Zona della sq.',
	'regione' => 'Regione della sq.',
	'anno' => 'Anno dell\'edizione dei Guidoncini Verdi.'
    );
    foreach ( $fields as $key => $val ) {
	$args = array(
	    'type' => 'string',
	    'description' => $val,
	    'single' => true,
	    'default' => '',
	    'show_in_rest' => true
	);
	register_meta( 'user', $key, $args );
	register_meta( 'post', $key, $args );
	register_meta( 'navigazione', $key, $args);
    }
    $args = array(
	'type' => 'boolean',
	'description' => 'La sq sta rinnovando la specialità.',
	'single' => true,
	'default' => false,
	'show_in_rest' => true
    );
    register_meta( 'user', 'rinnovo', $args );
    register_meta( 'post', 'rinnovo', $args );
    register_meta( 'navigazione', 'rinnovo', $args );
}
add_action( 'rest_api_init', 'guidoncini_register_meta' );

// Mostra in user-edit.php i campi addizionali
function guidoncini_show_extra_profile_fields ( $user ) {
    $fields = array( 'squadriglia' => '', 'specialita' => '', 'gruppo' => '', 'zona' => '', 'regione' => '', 'anno' => '');
    foreach ( $fields as $key => $value ) {
	$fields[$key] = get_the_author_meta( $key, $user->ID );
    }
    $rinnovo = get_the_author_meta( 'rinnovo', $user->ID );
?>
<h3>Informazioni addizionali</h3>
<table class="form-table">
    <?php
    foreach ( $fields as $key => $value ) {
	$escaped_key = esc_html($key);
	$escaped_value = esc_html($value);
	$label = ucfirst($escaped_key);
	echo "<tr><th><label for='$escaped_key'>$label</label></th>";
	echo "<td><input type='text' name='$escaped_key' id='$escaped_key' value='$escaped_value' class='regular-text'/></td></tr>";
    }
    echo "<tr><th><label for='rinnovo'>Rinnovo</label></th>";
    echo "<td><input type='checkbox' name='rinnovo' id='rinnovo' value='rinnovo'";
    echo $rinnovo ? " checked='checked'" : "";
    echo "/></td></tr>";
    ?>
</table>
<?php
}
add_action( 'show_user_profile', 'guidoncini_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'guidoncini_show_extra_profile_fields' );

// Restituisci errore nel caso in cui si cerchi di inserire un valore vuoto
/* function guidoncini_user_profile_update_errors( $errors, $update, $user ) {
 *     $show_error = false;
 *     $fields = array( 'specialita', 'gruppo', 'zona' );
 *     foreach ( $fields as $field ) {
 * 	if ( empty( $_POST[$field] ) ) {
 * 	    $show_error = true;
 * 	}
 *     }
 *     if ( $show_error ) {
 * 	$errors->add( 'additional_field_error', __( '<strong>ERRORE</strong>: Uno dei campi non è valido.', 'crf' ) );
 *     }
 * }
 * add_action( 'user_profile_update_errors', 'guidoncini_user_profile_update_errors', 10, 3 ); */

// Salva nel db le informazioni addizionali
function guidoncini_update_profile_fields( $user_id ) {
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
	return false;
    }
    $fields = array( 'squadriglia', 'specialita', 'gruppo', 'zona', 'regione', 'anno' );
    foreach ( $fields as $field ) {
	if ( ! empty( $_POST[$field] ) ) {
	    update_user_meta( $user_id, $field, $_POST[$field] );
	}
    }
    update_user_meta( $user_id, 'rinnovo', ! empty( $_POST['rinnovo'] ) );
}
add_action( 'personal_options_update', 'guidoncini_update_profile_fields' );
add_action( 'edit_user_profile_update', 'guidoncini_update_profile_fields' );

/*
 * Customizzazione di blocchi
 */

// Restituisci i soli post degli autori del post corrente quando tra
// le keyword nel query loop block c'è ":guidoncini-filter-sq"
function guidoncini_query_block_filter_sq( $query ) {
    $nav_ID = get_the_ID();
    if ( isset($query['s']) && $query['s'] == ':guidoncini-filter-sq' ) {
 	$query['s'] = '';
 	$query['meta_query'] = array (
	    array (
		'key' => 'squadriglia',
		'value' => get_post_meta( $nav_ID, 'squadriglia' ),
	    ),
	    array (
		'key' => 'gruppo',
		'value' => get_post_meta( $nav_ID, 'gruppo' ),
	    ),
	    array (
		'key' => 'anno',
		'value' => get_post_meta( $nav_ID, 'anno' ),
	    ),
	);
    }
    return $query;
}
add_filter( 'query_loop_block_query_vars', 'guidoncini_query_block_filter_sq' );

// Restituisici i soli post presentazione della specialità quando
// tra le keyword nel query loop block c'è ":guidoncini-filter-specialita"
function guidoncini_query_block_filter_specialita( $query ) {
    if ( $query['s'] == ':guidoncini-filter-specialita' ) {
	$queried_object = get_queried_object();
 	$query['s'] = '';
 	$query['post_type'] = ["post", "navigazione"];
	$query['tax_query'] = array(
		'relation' => 'AND',
	    array(
			'taxonomy' => 'specialita',
			'field' => 'slug',
			'terms' => $queried_object->slug
		),
		array(
			'taxonomy' => 'category',
			'field' => 'slug',
			'terms' => ['navigazione', 'pagina_unica']
	    )
	);
    }
    return $query;
}

add_filter( 'query_loop_block_query_vars', 'guidoncini_query_block_filter_specialita' );

?>
