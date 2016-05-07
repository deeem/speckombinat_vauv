<?php

/**
 * Fired during plugin deactivation
 *
 * @link       speckombinat.org.ua
 * @since      1.0.0
 *
 * @package    Vauv_Phones
 * @subpackage Vauv_Phones/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Vauv_Phones
 * @subpackage Vauv_Phones/includes
 * @author     deeem <dk81@yandex.ru>
 */
class Vauv_Phones_Deactivator {

	/**
	 * Fired during plugin deactivation.
	 */
	public static function deactivate() {

		self::unregister_view();
	}

	public static function unregister_view() {

		$page = self::get_page_by_slug( 'phones' );
		if ($page->ID) {
			wp_delete_post( $page->ID, true );
		}
	}

	public static function get_page_by_slug( $page_slug, $output = OBJECT, $post_type = 'page' ) {

		global $wpdb;
		$page = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type= %s AND post_status = 'publish'", $page_slug, $post_type ) );
		if ( $page ) {
			return get_post( $page, $output );
		}

		return null;
	}

}
