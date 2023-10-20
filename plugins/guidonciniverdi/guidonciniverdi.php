<?php

/*
 * Plugin Name: Guidoncini Verdi
 */

if( ! defined( 'ABSPATH' ) )
    exit( 'Restricted access' );

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
    if( 'edit.php' != $pagenow || !$query->is_admin )
	return $query;
    if( ! current_user_can( 'edit_others_posts' ) ) {
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

// Nascondi i terms di ogni tassonomia alle squadriglie
// Nascondi i terms di ogni tassonomia al ruolo author
function guidonciniverdi_hide_sq_category_display( $terms ) {
    if ( ! current_user_can( 'edit_others_posts' ) ) {
	$terms = array();
    }
    return $terms;
}
add_filter('get_terms', 'guidonciniverdi_hide_sq_category_display');

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

?>
