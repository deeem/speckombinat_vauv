<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://speckombinat.org.ua
 * @since      1.0.0
 *
 * @package    Vauv_Iplist
 * @subpackage Vauv_Iplist/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Vauv_Iplist
 * @subpackage Vauv_Iplist/admin
 * @author     deeem <my@email.com>
 */
class Vauv_Iplist_Admin {

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

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the scripts and stylesheets for the admin area.
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/vauv-iplist-admin.css', array(), $this->version, 'all' );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/vauv-iplist-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Register options management page
	 */
	public function register_management_page() {
		add_submenu_page( 'options-general.php',  'IP List', 'ВАУВ: IP List', 'manage_options', 'vauv-iplist-management', array( $this, 'management_page' ) );
	}

	/**
	 * Options management page html markup
	 */
	public function management_page() {
		echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
		echo '<h2>My Custom Submenu Page</h2>';
		echo '</div>';
	}

}
