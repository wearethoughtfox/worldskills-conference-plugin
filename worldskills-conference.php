<?php
/**
 * Plugin Name:       Worldskills Conference
 * Description:       Custom blocks for the WorldSkills Conference.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       worldskills-conference
 *
 * @package Worldskills
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

 function ws_register_block_category( $block_categories, $editor_context ) {
    if ( ! empty( $editor_context->post ) ) {
        array_push( $block_categories, array(
            'slug'  => 'worldskills-conference',
            'title' => __( 'WorldSkills Conference', 'ws-text-domain' ),
            'icon'  => null,  // Optional: You can specify an icon here
        ));
    }
    return $block_categories;
}
add_filter( 'block_categories_all', 'ws_register_block_category', 10, 2 );


 function worldskills_worldskills_conference_block_init() {
	register_block_type( __DIR__ . '/build/get-sessions' );
	register_block_type( __DIR__ . '/build/get-speakers' );
    register_block_type( __DIR__ . '/build/session-time' );
}
add_action( 'init', 'worldskills_worldskills_conference_block_init' );




function create_speaker_post_type() {
    $labels = array(
        'name'                  => _x('Speakers', 'Post type general name', 'textdomain'),
        'singular_name'         => _x('Speaker', 'Post type singular name', 'textdomain'),
        'menu_name'             => _x('Speakers', 'Admin Menu text', 'textdomain'),
        'name_admin_bar'        => _x('Speaker', 'Add New on Toolbar', 'textdomain'),
        'add_new'               => _x('Add New Speaker', 'speaker', 'textdomain'),
        'add_new_item'          => __('Add New Speaker', 'textdomain'),
        'new_item'              => __('New Speaker', 'textdomain'),
        'edit_item'             => __('Edit Speaker', 'textdomain'),
        'view_item'             => __('View Speaker', 'textdomain'),
        'all_items'             => __('All Speakers', 'textdomain'),
        'search_items'          => __('Search Speakers', 'textdomain'),
        'not_found'             => __('No speakers found.', 'textdomain'),
        'not_found_in_trash'    => __('No speakers found in Trash.', 'textdomain'),
        'featured_image'        => __('Speaker Image', 'textdomain'),
        'set_featured_image'    => __('Set speaker image', 'textdomain'),
        'remove_featured_image' => __('Remove speaker image', 'textdomain'),
        'use_featured_image'    => __('Use as speaker image', 'textdomain'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'speakers'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        'show_in_rest'       => true,  // This enables Gutenberg editor for this post type
    );

    register_post_type('speaker', $args);
}

add_action('init', 'create_speaker_post_type');


function create_session_post_type() {
    $labels = array(
        'name'                  => _x('Sessions', 'Post type general name', 'textdomain'),
        'singular_name'         => _x('Session', 'Post type singular name', 'textdomain'),
        'menu_name'             => _x('Sessions', 'Admin Menu text', 'textdomain'),
        'name_admin_bar'        => _x('Session', 'Add New on Toolbar', 'textdomain'),
        'add_new'               => _x('Add New Session', 'session', 'textdomain'),
        'add_new_item'          => __('Add New Session', 'textdomain'),
        'new_item'              => __('New Session', 'textdomain'),
        'edit_item'             => __('Edit Session', 'textdomain'),
        'view_item'             => __('View Session', 'textdomain'),
        'all_items'             => __('All Sessions', 'textdomain'),
        'search_items'          => __('Search Sessions', 'textdomain'),
        'not_found'             => __('No sessions found.', 'textdomain'),
        'not_found_in_trash'    => __('No sessions found in Trash.', 'textdomain'),
        'featured_image'        => __('Session Image', 'textdomain'),
        'set_featured_image'    => __('Set session image', 'textdomain'),
        'remove_featured_image' => __('Remove session image', 'textdomain'),
        'use_featured_image'    => __('Use as session image', 'textdomain'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'sessions'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments','custom-fields'),
        'show_in_rest'       => true,  // This enables Gutenberg editor for this post type
    );

    register_post_type('session', $args);
}

add_action('init', 'create_session_post_type');



function create_conference_taxonomy() {
    $labels = array(
        'name'              => 'Session Name Tags',
        'singular_name'     => 'Session Name Tag',
        'search_items'      => 'Search Session Name Tags',
        'all_items'         => 'All Session Name Tags',
        'edit_item'         => 'Edit Session Name Tags',
        'update_item'       => 'Update Session Name Tags',
        'add_new_item'      => 'Add New Session Name Tags',
        'new_item_name'     => 'New Session Name',
        'menu_name'         => 'Session Name',
    );

    $args = array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'conference'),
		'show_in_rest'      => true, 
    );

    register_taxonomy('conference', array('speaker', 'session'), $args);
}
add_action('init', 'create_conference_taxonomy');

function create_session_type_taxonomy() {
    $labels = array(
        'name'              => 'Session Types',
        'singular_name'     => 'Session Type',
        'search_items'      => 'Search Session Types',
        'all_items'         => 'All Session Types',
        'edit_item'         => 'Edit Session Type',
        'update_item'       => 'Update Session Type',
        'add_new_item'      => 'Add New Session Type',
        'new_item_name'     => 'New Session Type Name',
        'menu_name'         => 'Session Types',
    );

    $args = array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'session-type'),
        'show_in_rest'      => true,
    );

    register_taxonomy('session-type', array('session'), $args);
}
add_action('init', 'create_session_type_taxonomy');
