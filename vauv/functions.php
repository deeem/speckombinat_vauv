<?php

/* =============================================
			   Various actions
   ============================================= */
add_filter( 'show_admin_bar', '__return_false' );
add_action( 'wp_logout', function () {
	wp_redirect( home_url() );
	exit();
} );

/* =============================================
			   Bootstrap Framework
   ============================================= */

add_action( 'wp_enqueue_scripts', 'theme_styles' );

function theme_styles() {
	wp_enqueue_style( 'bootstrap_css', get_template_directory_uri() . '/assets/css/bootstrap.min.css' );
	wp_enqueue_style( 'style_css', get_template_directory_uri() . '/style.css' );
}

add_action( 'wp_enqueue_scripts', 'theme_js' );

function theme_js() {
	wp_register_script( 'html5_shiv', 'https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js', '', '', false );
	wp_register_script( 'respond_shiv', 'https://oss.maxcdn.com/respond/1.4.2/respond.min.js', '', '', false );
	global $wp_scripts;
	$wp_scripts->add_data( 'html5_shiv', 'conditional', 'lt IE 9' );
	$wp_scripts->add_data( 'respond_shiv', 'conditional', 'lt IE 9' );
	wp_enqueue_script( 'bootstrap_js', get_template_directory_uri() . '/assets/js/bootstrap.min.js', array( 'jquery' ), '', true );
}

/* =============================================
			       Thumbnails
   ============================================= */

add_theme_support( 'post-thumbnails' );

/* =============================================
			       Menus
   ============================================= */

add_theme_support( 'menus' );

add_action( 'init', 'register_frontpage_links' );

function register_frontpage_links() {
	register_nav_menu( 'frontpage-links', 'Ссылки на главной' );
}

class frontpage_links_Walker extends Walker_Nav_Menu {

	function start_el( &$output, $item, $depth, $args ) {
		$output .= '<li class="list-group-item">'
		           . '<a href="' . $item->url . '">'
		           . esc_attr( $item->title );
	}

	function end_el( &$output, $item, $depth, $args ) {
		$output .= '</a></li>' . "\n";
	}
}

/* =============================================
			  Roles and Capabilities
   ============================================= */

// extra user roles
add_action( 'load-themes.php', 'add_theme_roles' );

function add_theme_roles() {
	global $pagenow;

	if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) { // Test if theme is activate
		// Theme is activate
		add_role( 'vauv_admin', 'ВАУВ: Администратор', array( 'read' => true ) );
		add_role( 'vauv_programmer', 'ВАУВ: Программист', array( 'read' => true ) );
	} else {
		// Theme is deactivate
		remove_role( 'vauv_admin' );
		remove_role( 'vauv_programmer' );
	}
}

// Extending Dashoard Users page to manage plugins given user capabilities

add_action( 'show_user_profile', 'extra_capabilities_profile' ); // own
add_action( 'edit_user_profile', 'extra_capabilities_profile' ); // everyone else

/**
 * Display extra capabilities management form in user profile
 */
function extra_capabilities_profile( $user ) {

	ob_start();
	if ( current_user_can( 'edit_users' ) ) {
		require_once get_template_directory() . '/dashboardusers-edit.php';
	} else {
		require_once get_template_directory() . '/dashboardusers-display.php';
	}
	echo ob_get_clean();
}

add_action( 'personal_options_update', 'update_extra_capabilities_profile' );
add_action( 'edit_user_profile_update', 'update_extra_capabilities_profile' );

/**
 * Update extra capabilities management form in user profile
 */
function update_extra_capabilities_profile( $user_id ) {

	if ( ! current_user_can( 'edit_user' ) ) {
		return false;
	}

	$user         = new WP_User( $user_id );
	$vauv_plugins = get_option( 'vauv_plugins' );

	foreach ( $vauv_plugins as $plugin ) {
		$capabilities = array_keys( $plugin['capabilities'] );
		foreach ( $capabilities as $cap ) {

			if ( isset( $_POST[ $cap ] ) ) {
				$user->add_cap( $cap );
			} else {
				$user->remove_cap( $cap );
			}
		}
	}
}

/* =============================================
			  Settings Page
   ============================================= */

add_action( 'admin_menu', 'vauv_menu' );
function vauv_menu() {
	add_submenu_page( 'options-general.php', 'ВАУВ', 'ВАУВ: Настройки', 'manage_options', 'vauv-management', 'vauv_management_page' );
}

function vauv_management_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You do not have sufficient permissions to access this page.' );
	}
	echo '<div class="wrap">';
	echo '<p>тут, короче будут настройки </p>';
	echo '</div>';
}

/* =============================================
			  Widget area
   ============================================= */

add_action( 'widgets_init', 'vauv_widgets_init' );

function vauv_widgets_init() {
	register_sidebar( array(
		'name' => 'VAUV Front Page Widgets Area',
		'id'   => 'vauv_fp_widgets',
		//'before_widget' => '<div>',
		//'after_widget'  => '</div>',
		//'before_title'  => '<h2 class="rounded">',
		//'after_title'   => '</h2>',
	) );
}
