<?php

/**
 * Fired during plugin activation
 *
 * @link       http://speckombinat.org.ua
 * @since      1.0.0
 *
 * @package    Vauv_Iplist
 * @subpackage Vauv_Iplist/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Vauv_Iplist
 * @subpackage Vauv_Iplist/includes
 * @author     deeem <my@email.com>
 */
class Vauv_Iplist_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		self::create_table();
		self::add_capabilities();
		self::register_view();
	}

	/**
	 * Create or update iplist table structure in database
	 */
	public static function create_table() {

		global $wpdb;
		global $charset_collate;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql_create_table = "CREATE TABLE {$wpdb->prefix}vauv_iplist (
  ip int(10) unsigned NOT NULL DEFAULT '0',
  name varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  user varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  phone varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (ip)
) $charset_collate; ";
		dbDelta( $sql_create_table );
	}

	/**
	 * Add extra capabilities and store them into 'wp_options' table
	 */
	public static function add_capabilities() {

		// store capabilities info in options
		$name         = 'список ip-адресов';
		$capabilities = array( 'iplist_view' => 'просмотр списка', 'iplist_edit' => 'редактирование списка' );

		if ( get_option( 'vauv_plugins' ) !== false ) {
			$vauv_plugins           = get_option( 'vauv_plugins' );
			$vauv_plugins['iplist'] = array(
				'name'         => $name,
				'capabilities' => $capabilities
			);
			update_option( 'vauv_plugins', $vauv_plugins );
		} else {
			$vauv_plugins           = array();
			$vauv_plugins['iplist'] = array(
				'name'         => $name,
				'capabilities' => $capabilities
			);
			update_option( 'vauv_plugins', $vauv_plugins );
		}
	}

	/**
	 * Create post with giving slug to use as 'view' for displaying html-markup
	 */
	public static function register_view() {

		// check slug and create post with this slug
		$slug = 'iplist';
		if ( ! self::the_slug_exists( $slug ) ) {

			// create post with this slug
			$post_id = wp_insert_post(
				array(
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
					'post_author'    => 1,
					'post_name'      => $slug,
					'post_title'     => $slug,
					'post_status'    => 'publish',
					'post_type'      => 'page'
				)
			);

			if ( $post_id == - 1 || $post_id == - 2 ) {
				wp_die( "The post whit slug " . $slug . " wasn't created or the page already exists" );
			}

		} else {
			wp_die( "Slug " . $slug . " уже занят. Переименуйте пост, имеющий данный slug или удалите и очистите корзину" );
		}
	}

	public static function the_slug_exists( $post_name ) {
		global $wpdb;
		if ( $wpdb->get_row( "SELECT post_name FROM wp_posts WHERE post_name = '" . $post_name . "'", 'ARRAY_A' ) ) {
			return true;
		} else {
			return false;
		}
	}

}
