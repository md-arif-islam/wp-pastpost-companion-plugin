<?php

function pastpost_custom_post_slider() {
	/**
	 * Post Type: Slider.
	 */

	$labels = array(
		'name'               => esc_html__( 'Slider', 'pastpost-companion' ),
		'singular_name'      => esc_html__( 'Slider item', 'pastpost-companion' ),
		'add_new'            => esc_html__( 'Add New', 'pastpost-companion' ),
		'add_new_item'       => esc_html__( 'Add New Slider item', 'pastpost-companion' ),
		'edit_item'          => esc_html__( 'Edit Slider item', 'pastpost-companion' ),
		'new_item'           => esc_html__( 'New Slider item', 'pastpost-companion' ),
		'view_item'          => esc_html__( 'View Slider item', 'pastpost-companion' ),
		'search_items'       => esc_html__( 'Search Slider items', 'pastpost-companion' ),
		'not_found'          => esc_html__( 'No slider items found', 'pastpost-companion' ),
		'not_found_in_trash' => esc_html__( 'No slider items found in Trash', 'pastpost-companion' ),
		'parent_item_colon'  => '',
	);

	$args = array(
		'labels'             => $labels,
		'menu_icon'          => 'dashicons-format-gallery',
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'query_var'          => true,
		'capability_type'    => 'post',
		'hierarchical'       => false,
		'menu_position'      => 29,
		'show_in_rest'       => true,
		'rewrite'            => array( "slug" => "slider", "with_front" => true ),
		'supports'           => array( 'editor', 'page-attributes', 'thumbnail', 'title' ),
	);

	register_post_type( 'slider', $args );

	register_taxonomy( 'slider-types', 'slider', array(
		'label'        => esc_html__( 'Slider Categories', 'pastpost-companion' ),
		'hierarchical' => true,
		'query_var'    => true,
		'show_in_rest' => true,
		'rewrite'      => array(
			'slug' => "slider-types",
		),
	) );
}

add_action( 'init', 'pastpost_custom_post_slider' );


function pastpost_slider_add_columns( $columns ) {
	$newcolumns = array(
		'cb'               => '<input type="checkbox" />',
		'slider_thumbnail' => esc_html__( 'Thumbnail', 'pastpost-companion' ),
		'title'            => esc_html__( 'Title', 'pastpost-companion' ),
		'slider_types'     => esc_html__( 'Categories', 'pastpost-companion' ),
		'slider_order'     => esc_html__( 'Order', 'pastpost-companion' ),
	);
	$columns    = array_merge( $newcolumns, $columns );

	return $columns;
}

// applied to the list of columns to print on the manage posts screen for a custom post type
add_filter( 'manage_edit-slider_columns', "pastpost_slider_add_columns" );


function pastpost_slider_custom_column( $column ) {
	global $post;

	switch ( $column ) {
		case 'slider_thumbnail':
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( '50x50' );
			}
			break;
		case 'slider_types':
			echo get_the_term_list( $post->ID, 'slider-types', '', ', ', '' );
			break;
		case 'slider_order':
			echo esc_attr( $post->menu_order );
			break;
	}
}


// allows to add or remove (unset) custom columns to the list post/page/custom post type pages
add_action( 'manage_posts_custom_column', "pastpost_slider_custom_column" );