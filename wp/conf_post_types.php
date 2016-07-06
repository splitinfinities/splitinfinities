<?php

/**
 * Helper class to add Custom Post Types
 * @return null
 */
function init_custom_post_types() {
	$post_types = array(
		'blog' => array(
			'slug'			=> 'blog',
			'title'			=> 'Blog',
			'singular'		=> 'Post',
			'plural'		=> 'Posts',
			'menu_icon'		=> 'dashicons-media-document',
			'supports'		=> array( 'title', 'content', 'editor', 'excerpt', 'page-attributes', 'revisions', 'permalinks' ),
			'position'		=> 11,
			'has_archive'	=> true
		),
	);

	foreach ($post_types as $name => $options) {
		$labels = array(
			'name'               => _x( $options['title'], 'post type general name', 'neighborhood' ),
			'singular_name'      => _x( $options['singular'], 'post type singular name', 'neighborhood' ),
			'menu_name'          => _x( $options['title'], 'admin menu', 'neighborhood' ),
			'name_admin_bar'     => _x( $options['singular'], 'add new on admin bar', 'neighborhood' ),
			'add_new'            => _x( 'Add ' . $options['singular'], $name, 'neighborhood' ),
			'add_new_item'       => __( 'Add New ' . $options['singular'], 'neighborhood' ),
			'new_item'           => __( 'New ' . $options['singular'], 'neighborhood' ),
			'edit_item'          => __( 'Edit ' . $options['singular'], 'neighborhood' ),
			'view_item'          => __( 'View ' . $options['singular'], 'neighborhood' ),
			'all_items'          => __( 'All ' . $options['plural'], 'neighborhood' ),
			'search_items'       => __( 'Search ' . $options['plural'], 'neighborhood' ),
			'parent_item_colon'  => __( 'Parent ' . $options['plural'], 'neighborhood' ),
			'not_found'          => __( 'No ' . strtolower($options['plural']) . ' found.', 'neighborhood' ),
			'not_found_in_trash' => __( 'No ' . strtolower($options['plural']) . ' found in Trash.', 'neighborhood' )
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => $options['slug'], 'with_front' => true ),
			'capability_type'    => 'post',
			'has_archive'        => $options['has_archive'],
			'hierarchical'       => true,
			'menu_position'      => $options['position'],
			'menu_icon'          => $options['menu_icon'],
			'supports'           => $options['supports']
		);

		register_post_type( $name, $args );
	}
}

add_action( 'init', 'init_custom_post_types' );

