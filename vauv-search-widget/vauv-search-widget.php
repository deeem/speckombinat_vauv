<?php

/*
* Plugin Name:       VAUV Search Widget
* Plugin URI:        https://github.com/deeem/speckombinat_vauv
* Description:       Поиск по телефонному справочнику и списку ip-адресов. Виджет для сайта ВАУВ.
* Version:           1.0.0
* Author:            deeem
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
*/

class VAUV_Search_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		parent::__construct(
			'vauv_search_widget', // Base ID
			'VAUV Search Widget', // Name
			array( 'description' => 'Поиск по справочникам телефонов и ip-адресов', ) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		ob_start();
		require_once plugin_dir_path( __FILE__ ) . '/partial_display.php';
		echo ob_get_clean();
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
	}
}

add_action( 'widgets_init', function () {
	register_widget( 'VAUV_Search_Widget' );
} );

/* Enqueue script file */
wp_enqueue_script( 'vauv-search-widget-script',
	plugins_url( 'vauv-search-widget/vauv-search-widget.js' ),
	array( 'jquery' )
);

/* Adds the WordPress Ajax Library to the frontend */
wp_localize_script( 'vauv-search-widget-script', 'ajax', array( 'url' => admin_url( 'admin-ajax.php' ) ) );

/* Ajax action */
add_action( 'wp_ajax_vauv_search', 'vauv_search' );

function vauv_search() {

	if ( isset( $_POST['param'] ) && $_POST['param'] !== '' ) {
		$param = $_POST['param'];
	}

	global $wpdb;

	$query_phones = 'SELECT * FROM wp_vauv_phones WHERE `phone` LIKE "' . $param . '%" OR `name` LIKE "' . $param . '%"';
	$phones       = $wpdb->get_results( $query_phones );

	$query_iplist = '
SELECT inet_ntoa(`ip`) AS `ip`,
`name`,
`user`,
`phone`
FROM wp_vauv_iplist
WHERE `name` LIKE "' . $param . '%"
OR `user` LIKE "' . $param . '%"
OR `phone` LIKE "' . $param . '%"';
	$iplist       = $wpdb->get_results( $query_iplist );

	echo json_encode( array( 'phones' => $phones, 'iplist' => $iplist ) );

	die();
}
