<?php

/*
Plugin Name: Bloghosting (Feature: PSeCommerce Globaler Artikelfilter)
*/

class ProSites_Module_PSeCommerce_Global {

	static $user_label;
	static $user_description;

	var $pro_sites = false;

	// Module name for registering
	public static function get_name() {
		return __('PSeCommerce Globaler Artikelfilter', 'psts');
	}

	// Module description for registering
	public static function get_description() {
		return __('Wenn diese Option aktiviert ist, werden Gratis Bloghosting-Artikel aus den globalen PSeCommerce-Artikellisten entfernt.', 'psts');
	}

	// This module requires a specific class
	public static function get_class_restriction() {
		return 'PSeCommerce';
	}

	function __construct() {
		if( ! is_admin() && is_main_site( get_current_blog_id() ) ) {
			return;
		}
		add_filter( 'mp_list_global_products_results', array( &$this, 'filter' ) );

		self::$user_label       = __( 'PSeCommerce', 'psts' );
		self::$user_description = __( 'Anzeige in der globalen Artikelliste von PSeCommerce', 'psts' );
	}

	function filter( $results ) {
		global $wpdb;

		if ( ! $this->pro_sites ) {
			$this->pro_sites = $wpdb->get_col( "SELECT blog_ID FROM {$wpdb->base_prefix}pro_sites WHERE expire > '" . time() . "'" );
		}

		foreach ( $results as $key => $row ) {
			if ( ! in_array( $row->blog_id, $this->pro_sites ) ) {
				unset( $results[ $key ] );
			}
		}

		return $results;
	}

	public static function is_included( $level_id ) {
		switch ( $level_id ) {
			default:
				return false;
		}
	}

}
