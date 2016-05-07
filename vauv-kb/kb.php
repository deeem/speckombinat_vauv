<?php
/*
Plugin Name: VAUV Knowledge Base
Plugin URI: https://github.com/deeem/vauv-kb
Description: База знаний отдела ВАУВ
Version: 1.0
Author: deeem
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

/* ================================================
             Registering custom post type
   ================================================ */
add_action( 'init', 'create_knowledge_base' );

function create_knowledge_base() {
    register_post_type( 'kb', array(
        'labels'        => array(
            'name'               => 'База Знаний',
            'singular_name'      => 'Запись Базы Знаний',
            'add_new'            => 'Добавить запись',
            'add_new_item'       => 'Добавить новую запись в Базу Знаний',
            'edit'               => 'Редактировать',
            'edit_item'          => 'Редактировать запись Базы Знаний',
            'new_item'           => 'Новая запись Базы Знаний',
            'view'               => 'Просмотр',
            'view_item'          => 'Просмотреть запись Базы Знаний',
            'search_items'       => 'Поиск по Базе Знаний',
            'not_found'          => 'Не найдены записи в Базе Знаний',
            'not_found_in_trash' => 'Не найдены записи в Базе Знаний в корзине',
            'parent'             => 'Родительская запись Базы Знаний'
        ),
        'public'        => true,
        'menu_position' => 15,
        'supports'      => array( 'title', 'editor', 'author', 'revisions' ),
        'menu_icon'     => 'dashicons-book-alt',
        'has_archive'   => true,
        'rewrite'       => array(
            'slug' => 'base',
        )
    ) );
}

/* ================================================
                     Taxonomy
   ================================================ */
add_action( 'init', 'create_kb_taxonomies', 0 );

function create_kb_taxonomies() {
    register_taxonomy(
        'kb_categories',
        'kb',
        array(
            'labels'        => array(
                'name'          => 'Категории знаний',
                'add_new_item'  => 'Добавить категорию',
                'new_item_name' => 'Новая категоря'
            ),
            'show_ui'       => true,
            'show_tagcloud' => false,
            'hierarchical'  => true,
            'rewrite'       => array(
                'slug' => 'knowledge'
            )
        ) );
}

/* ================================================
                    Templates
   ================================================ */
add_filter( 'template_include', 'template_include_kb', 1 );

function template_include_kb( $template_path ) {

    if ( get_post_type() == 'kb' ) {
        if ( is_single() ) {
            // если есть готовый шаблон в папке с темой, то используем его,
            // иначе используем шаблон из папки плагина
            if ( $theme_file = locate_template( array( 'single-kb.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/single-kb.php';
            }
        } elseif ( is_archive() ) {
            if ( $theme_file = locate_template( array( 'archive-kb.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/archive-kb.php';
            }
        }
    }

    return $template_path;
}

/* ================================================
                     Metabox
   ================================================ */
add_action( 'admin_init', 'add_kb_meta_box' );

function add_kb_meta_box() {
    add_meta_box( 'kb_meta_box',
        'Дополнительно',
        'display_kb_meta_box',
        'kb', 'normal', 'high' );
}

function display_kb_meta_box( $kb ) {
    $kb_visible = get_post_meta( $kb->ID, 'kb_visible', true );
    ?>
    <table>
        <tr>
            <td>Видна неавторизованным?</td>
            <td><input type="checkbox" name="kb_visible" <?php if ( $kb_visible == 'true' ) {
                    echo 'checked';
                }; ?>/></td>
        </tr>
    </table>
    <?php
}

add_action( 'save_post', 'save_kb_fields', 10, 1 );

function save_kb_fields( $kb_id ) {

    if ( isset( $_POST['post_type'] ) && $_POST['post_type'] == 'kb' ) {
        if ( isset( $_POST['kb_visible'] ) ) {
            update_post_meta( $kb_id, 'kb_visible', 'true' );
        } else {
            update_post_meta( $kb_id, 'kb_visible', 'false' );
        }
    }
}

/* ================================================
                Admin Columns
   ================================================ */
add_filter( 'manage_edit-kb_columns', 'kb_columns' );

function kb_columns( $columns ) {
    $columns['kb_visible'] = 'доступ';

    return $columns;
}

add_action( 'manage_posts_custom_column', 'populate_kb_columns' );

function populate_kb_columns( $column ) {
    if ( $column == 'kb_visible' ) {
        $visible = esc_html( get_post_meta( get_the_ID(), 'kb_visible', true ) );
        if ( $visible == 'true' ) {
            echo '<span class="dashicons dashicons-share-alt"></span>';
        }
    }
}

/* ================================================
             Enqueueing scripts
   ================================================ */
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style( 'vauv-kb-styles', plugin_dir_url( __FILE__ ) . 'vauv-kb.css' );
} );

/* ================================================
               Shortcodes
   ================================================ */

/*
 * Шорткод для обозначения оператора мобильной связи в номере.
 * Пример: [mobile]0992405476[/mobile]. Операторы: МТС, Киевстар, Life:).
 */

function mobile_operators( $atts, $content = null ) {
    $phone = trim( $content );

    $operators = array(
        'mts'      => array(
            '/^[+]?[3]?[8]?[\(]?[0]{1}[5]{1}[0]{1}[\)]?[0-9\s-]{7,}$/', // 050
            '/^[+]?[3]?[8]?[\(]?[0]{1}[6]{1}[6]{1}[\)]?[0-9\s-]{7,}$/', // 066
            '/^[+]?[3]?[8]?[\(]?[0]{1}[9]{1}[5]{1}[\)]?[0-9\s-]{7,}$/', // 095
            '/^[+]?[3]?[8]?[\(]?[0]{1}[9]{1}[9]{1}[\)]?[0-9\s-]{7,}$/'  // 099
        ),
        'kievstar' => array(
            '/^[+]?[3]?[8]?[\(]?[0]{1}[6]{1}[7]{1}[\)]?[0-9\s-]{7,}$/', // 067
            '/^[+]?[3]?[8]?[\(]?[0]{1}[9]{1}[6]{1}[\)]?[0-9\s-]{7,}$/', // 096
            '/^[+]?[3]?[8]?[\(]?[0]{1}[9]{1}[7]{1}[\)]?[0-9\s-]{7,}$/', // 097
            '/^[+]?[3]?[8]?[\(]?[0]{1}[9]{1}[8]{1}[\)]?[0-9\s-]{7,}$/'  // 098
        ),
        'life'     => array(
            '/^[+]?[3]?[8]?[\(]?[0]{1}[6]{1}[3]{1}[\)]?[0-9\s-]{7,}$/', // 063
            '/^[+]?[3]?[8]?[\(]?[0]{1}[9]{1}[3]{1}[\)]?[0-9\s-]{7,}$/'  // 093
        )
    );

    foreach ( $operators as $operator => $patterns ) {
        foreach ( $patterns as $pattern ) {
            if ( preg_match( $pattern, $phone ) ) {
                return '<img src="' . plugins_url( 'vauv-kb/images/'. $operator .'.gif' ) . '" /> ' . $content;
            }
        }
    }
}

add_shortcode( 'mobile', 'mobile_operators' );