<?php
/*
Plugin Name: Pronamic FAQ
Plugin URI: http://www.happywp.com/plugins/pronamic-faq/
Description: This plugin adds an FAQ functionality to WordPress.

Version: 1.0.1
Requires at least: 3.0

Author: Pronamic
Author URI: http://www.pronamic.eu/

Text Domain: pronamic_faq
Domain Path: /languages/

License: GPL

GitHub URI: https://github.com/pronamic/wp-pronamic-faq
*/

/**
 * Flush data
 */
function pronamic_faq_rewrite_flush() {
    pronamic_faq_init();

    flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'pronamic_faq_rewrite_flush' );

/**
 * Register post type and taxonomy
 */
function pronamic_faq_init() {
	$rel_path = dirname( plugin_basename( __FILE__ ) ) . '/languages/';

	load_plugin_textdomain( 'pronamic_faq', false, $rel_path );

	register_post_type(
		'pronamic_faq',
		[
			'labels'             => [
				'name'               => _x( 'FAQ', 'post type general name', 'pronamic_faq' ), 
				'singular_name'      => _x( 'FAQ', 'post type singular name', 'pronamic_faq' ), 
				'add_new'            => _x( 'Add New', 'pronamic_faq', 'pronamic_faq' ),
				'add_new_item'       => __( 'Add New FAQ', 'pronamic_faq' ),
				'edit_item'          => __( 'Edit FAQ', 'pronamic_faq' ),
				'new_item'           => __( 'New FAQ', 'pronamic_faq' ),
				'view_item'          => __( 'View FAQ', 'pronamic_faq' ),
				'search_items'       => __( 'Search FAQs', 'pronamic_faq' ),
				'not_found'          => __( 'No FAQs found', 'pronamic_faq' ),
				'not_found_in_trash' => __( 'No FAQs found in Trash', 'pronamic_faq' ), 
				'parent_item_colon'  => __( 'Parent FAQ:', 'pronamic_faq' ),
				'menu_name'          => __( 'FAQ', 'pronamic_faq' ),
			],
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'show_in_rest'       => true,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'rewrite'            => [
				'slug'       => 'faq', 
				'with_front' => false
			],
			'menu_icon'          => 'dashicons-format-chat',
			'supports'           => [
				'title',
				'editor',
				'author',
				'thumbnail',
				'custom-fields',
			],
		]
	);

	register_taxonomy(
		'pronamic_faq_category',
		'pronamic_faq', 
		[ 
			'hierarchical' => true , 
			'labels'       => [
				'name'              => _x( 'FAQ Category', 'category general name', 'pronamic_faq' ),
				'singular_name'     => _x( 'FAQ Category', 'category singular name', 'pronamic_faq' ),
				'search_items'      => __( 'Search FAQ Categories', 'pronamic_faq' ),
				'all_items'         => __( 'All FAQ Categories', 'pronamic_faq' ),
				'parent_item'       => __( 'Parent FAQ Category', 'pronamic_faq' ),
				'parent_item_colon' => __( 'Parent FAQ Category:', 'pronamic_faq' ),
				'edit_item'         => __( 'Edit FAQ Category', 'pronamic_faq' ),
				'update_item'       => __( 'Update FAQ Category', 'pronamic_faq' ),
				'add_new_item'      => __( 'Add New FAQ Category', 'pronamic_faq' ),
				'new_item_name'     => __( 'New FAQ Category Name', 'pronamic_faq' ),
				'menu_name'         => __( 'Categories', 'pronamic_faq' )
			],
			'show_ui'      => true,
			'query_var'    => true,
			'show_in_rest' => true,
		]
	);
}

add_action( 'init', 'pronamic_faq_init' );

/**
 * Set columns
 * 
 * @param $columns
 */
function pronamic_faq_set_columns( $columns ) {
	$new_columns = [];

	if ( isset( $columns['cb'] ) ) {
		$new_columns['cb'] = $columns['cb'];
	}

	if ( isset( $columns['title'] ) ) {
		$new_columns['title'] = __( 'Question', 'pronamic_faq' );
	}

	if ( isset( $columns['author'] ) ) {
		$new_columns['author'] = $columns['author'];
	}

	$new_columns['pronamic_faq_categories'] = __( 'Categories', 'pronamic_faq' );

	if ( isset( $columns['comments'] ) ) {
		$new_columns['comments'] = $columns['comments'];
	}

	if ( isset( $columns['date'] ) ) {
		$new_columns['date'] = $columns['date'];
	}
	
	return $new_columns;
}

add_filter( 'manage_edit-pronamic_faq_columns' , 'pronamic_faq_set_columns' );

/**
 * Custom columns
 * 
 * @param $column
 * @param $post_id
 */
function pronamic_faq_custom_columns( $column, $post_id ) {
	switch ( $column ) {
		case 'pronamic_faq_categories':
			$terms = get_the_term_list( $post_id, 'pronamic_faq_category', '', ', ', '' );

			if ( is_string( $terms ) ) {
				echo $terms;
			} else {
				echo __( 'No Categorie', 'pronamic_faq' );
			}

			break;
	}
}

add_action( 'manage_posts_custom_column', 'pronamic_faq_custom_columns', 10, 2 );
