<?php


if (function_exists('get_field')) {

	include 'acf/brand.php';
	include 'acf/images.php';
	include 'acf/users.php';

	if (get_field('enable_page_builder', 'options')) {
		include 'acf/page-content.php';
	}

	include 'acf/seo.php';
	include 'acf/sdo.php';
	include 'acf/apis.php';
	include 'acf/page-seo_and_sdo.php';


	if (function_exists('acf_add_options_page')) {
		/**
		 * Register settings page
		 */
		function plugin_admin_add_page() {


			acf_add_options_page(array(
				'page_title' 	=> 'Brand',
				'menu_title'	=> get_bloginfo('name'),
				'menu_slug' 	=> 'theme-general-settings',
				'redirect'		=> false,
				'position'		=> -1
				)
			);

			acf_add_options_sub_page(array(
				'page_title' 	=> 'SEO (Search Engine Optimization)',
				'menu_title'	=> 'SEO',
				'parent_slug'	=> 'theme-general-settings',
				)
			);

			acf_add_options_sub_page(array(
				'page_title' 	=> 'SDO (Social Discovery Optimization)',
				'menu_title'	=> 'SDO',
				'parent_slug'	=> 'theme-general-settings',
				)
			);

			acf_add_options_sub_page(array(
				'page_title' 	=> 'Third Party API\'S',
				'menu_title'	=> 'API\'s',
				'parent_slug'	=> 'theme-general-settings',
				)
			);

		}

		add_action('admin_menu', 'plugin_admin_add_page');
	}
}
