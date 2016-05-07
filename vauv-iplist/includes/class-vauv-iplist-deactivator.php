<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://speckombinat.org.ua
 * @since      1.0.0
 *
 * @package    Vauv_Iplist
 * @subpackage Vauv_Iplist/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Vauv_Iplist
 * @subpackage Vauv_Iplist/includes
 * @author     deeem <my@email.com>
 */
class Vauv_Iplist_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		self::unregister_view();
	}

	public static function unregister_view() {

		$page = self::get_page_by_slug( 'iplist' );
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
