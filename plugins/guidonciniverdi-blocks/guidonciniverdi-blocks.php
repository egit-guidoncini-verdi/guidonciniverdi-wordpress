<?php
/**
 * Plugin Name:       Guidonciniverdi Blocks
 * Description:       Example block scaffolded with Create Block tool.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       guidonciniverdi-blocks
 *
 * @package           create-block
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function guidonciniverdi_blocks_guidonciniverdi_blocks_block_init() {
	register_block_type( __DIR__ . '/build/squadriglia' );
	register_block_type( __DIR__ . '/build/gruppo' );
	register_block_type( __DIR__ . '/build/zona' );
	register_block_type( __DIR__ . '/build/specialita' );
}
add_action( 'init', 'guidonciniverdi_blocks_guidonciniverdi_blocks_block_init' );
