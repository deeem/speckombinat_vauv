<?php
/*
Plugin Name: VAUV Launcher
Plugin URI: https://github.com/deeem/speckombinat_vauv
Description: Панель ярлыков для запуска различных web-приложений на сайте отдела ВАУВ
Version: 1.0
Author: deeem
License: GPLv2
*/

/* ================================================
			Registering custom post type
   ================================================ */
add_action( 'init', 'create_app_launcher' );

function create_app_launcher() {
	register_post_type( 'app', array(
		'labels'        => array(
			'name'               => 'Веб приложения',
			'singular_name'      => 'Веб приложение',
			'add_new'            => 'Добавить приложение',
			'add_new_item'       => 'Добавить новый ярлык веб приложения',
			'edit'               => 'Редактировать',
			'edit_item'          => 'Редактировать ярлык веб приложения',
			'new_item'           => 'Новая ярлык веб приложения',
			'view'               => 'Просмотр',
			'view_item'          => 'Просмотреть ярлык веб приложения',
			'search_items'       => 'Поиск по ярлыкам веб приложений',
			'not_found'          => 'Не найдены ярлыки веб приложений',
			'not_found_in_trash' => 'Не найдены ярлыки веб приложений в корзине',
			'parent'             => 'Родительская запись'
		),
		'public'        => true,
		'menu_position' => 15,
		'supports'      => array( 'title', 'thumbnail' ),
		'menu_icon'     => 'dashicons-products',
		'has_archive'   => true,
		'rewrite'       => array(
			'slug' => 'apps',
		)
	) );
}

/* ================================================
					 Metabox
   ================================================ */
add_action( 'admin_init', 'add_app_meta_box' );

function add_app_meta_box() {
	add_meta_box( 'app_meta_box',
		'Дополнительно',
		'display_app_meta_box',
		'app', 'normal', 'high' );
}

function display_app_meta_box( $app ) {
	$app_description = get_post_meta( $app->ID, 'app_description', true );
	$app_visible     = get_post_meta( $app->ID, 'app_visible', true );
	$app_link        = get_post_meta( $app->ID, 'app_link', true );
	?>
	<table>
		<tr>
			<td style="width:200px;">Описание</td>
			<td>
				<input type="text"
				       name="app_description"
				       value="<?php echo $app_description; ?>"
				       size="50"/>
			</td>
		</tr>
		<tr>
			<td>URL</td>
			<td>
				<input type="text"
				       name="app_link"
				       value="<?php echo $app_link; ?>"/>
			</td>
		</tr>
		<tr>
			<td>Видна неавторизованным?</td>
			<td>
				<input type="checkbox"
				       name="app_visible"
					<?php if ( $app_visible == 'true' ): ?>
						checked
					<?php endif; ?> />
			</td>
		</tr>
	</table>
	<?php
}

add_action( 'save_post', 'save_app_fields', 10, 1 );

function save_app_fields( $app_id ) {
	if ( isset( $_POST['post_type'] ) && $_POST['post_type'] == 'app' ) {
		if ( isset( $_POST['app_visible'] ) ) {
			update_post_meta( $app_id, 'app_visible', 'true' );
		} else {
			update_post_meta( $app_id, 'app_visible', 'false' );
		}
		if ( isset( $_POST['app_link'] ) ) {
			update_post_meta( $app_id, 'app_link', $_POST['app_link'] );
		}
		if ( isset( $_POST['app_description'] ) ) {
			update_post_meta( $app_id, 'app_description', $_POST['app_description'] );
		}
	}
}

/* ================================================
					 Admin Columns
   ================================================ */
add_filter( 'manage_edit-app_columns', 'app_columns' );

function app_columns( $columns ) {
	$columns['app_visible'] = 'доступ';

	return $columns;
}

add_action( 'manage_posts_custom_column', 'populate_app_columns' );

function populate_app_columns( $column ) {
	if ( $column == 'app_visible' ) {
		$visible = esc_html( get_post_meta( get_the_ID(), 'app_visible', true ) );
		if ( $visible == "true" ) {
			echo '<span class="dashicons dashicons-share-alt"></span>';
		}
	}
}
