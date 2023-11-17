<?php

// Add block patterns
require get_template_directory() . '/inc/block-patterns.php';

add_action( 'wp_enqueue_scripts', 'enqueue_theme_assets' );
function enqueue_theme_assets() {
	wp_enqueue_style( 'guidonciniverdi-main-styles', get_stylesheet_uri() );
    
    if (WP_DEBUG) {
        // If debug mode is enabled, we activate hot reloading (based on browsersync)
        wp_enqueue_script(
            'guidonciniverdi-hot-reloader',
            get_template_directory_uri() . '/scripts/hot_reloader.js'
        );
    }
}

?>
