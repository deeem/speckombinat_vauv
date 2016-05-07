<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       speckombinat.org.ua
 * @since      1.0.0
 *
 * @package    Vauv_Phones
 * @subpackage Vauv_Phones/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Vauv_Phones
 * @subpackage Vauv_Phones/admin
 * @author     deeem <dk81@yandex.ru>
 */
class Vauv_Phones_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $phones_db;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $phones_db ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->phones_db = $phones_db;

	}

	/**
	 * Register the scripts and stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/vauv-phones-admin.css', array(), $this->version, 'all' );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/vauv-phones-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register options management page
	 */
	public function register_management_page() {

		add_submenu_page( 'options-general.php',  'Список телефонов', 'ВАУВ: Phones', 'manage_options', 'vauv-phones-management', array( $this, 'management_page' ) );

	}

	/**
	 * Options management page html markup
	 */
	public function management_page() {

		ob_start();
		require_once plugin_dir_path( __FILE__ ) . '/partials/vauv-phones-admin-settings.php';
		echo ob_get_clean();

	}

	/**
	 * Register our setting in the "vauv-phones-menegement" settings section
	 */
	public function register_settings() {

		register_setting ( 'vauv-phones-settings-group', 'vauv-phones-options', array( $this, 'vauv_phones_sanitize_options') );

	}


	/**
	 * Sanitize "vauv-phones-menegement" settings section
	 *
	 * @param $input array options array to sanitize
	 *
	 * @return mixed array sanitized options array
	 */
	public function vauv_phones_sanitize_options ( $input ) {

		if ( ! isset($_FILES['phones-import']) ) {
			wp_die( 'No xml was uploaded.' );
		}

		if ( ! preg_match( '/xml$/', $_FILES['phones-import']['type']) ) {
			wp_die ( 'File is not xml' );
		}

		if ( ! $_FILES['phones-import']['size'] ) {
			wp_die( 'Null sized file' );
		}

		$result = $this->phones_db->import_phones( $_FILES['phones-import']['tmp_name'] );

		return $input;
	}

}
