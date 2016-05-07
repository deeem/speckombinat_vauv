<?php

/**
 * Fired during plugin activation
 *
 * @link       speckombinat.org.ua
 * @since      1.0.0
 *
 * @package    Vauv_Phones
 * @subpackage Vauv_Phones/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Vauv_Phones
 * @subpackage Vauv_Phones/includes
 * @author     deeem <dk81@yandex.ru>
 */
class Vauv_Phones_Activator
{

    /**
     * Fired during plugin activation
     */
    public static function activate()
    {
        self::create_table();
        self::add_capabilities();
        self::register_view();
    }

    /**
     * Create or update phones table structure in database
     */
    public static function create_table()
    {
        global $wpdb;
        global $charset_collate;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql_create_table = "
		CREATE TABLE {$wpdb->prefix}vauv_phones (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  department int(10) unsigned DEFAULT NULL COMMENT 'организация/отдел/группа абонента',
  parents text COMMENT 'цепочка организация/отдел/группа абонента',
  name varchar(255) DEFAULT NULL COMMENT 'имя абонента',
  position varchar(255) DEFAULT NULL COMMENT 'должность абонента',
  phone varchar(15) DEFAULT NULL COMMENT 'номер телефона',
  PRIMARY KEY (id)
){$charset_collate};";
        dbDelta($sql_create_table);
    }

    /**
     * Add extra capabilities and store them into 'wp_options' table
     */
    public static function add_capabilities()
    {
        $name = 'телефонный справочник';
        $capabilities = array('phones_update' => 'обновление справочника');

        if (get_option('vauv_plugins') !== false) {
            $vauv_plugins = get_option('vauv_plugins');
            $vauv_plugins['phones'] = array(
                'name' => $name,
                'capabilities' => $capabilities
            );
            update_option('vauv_plugins', $vauv_plugins);
        } else {
            $vauv_plugins = array();
            $vauv_plugins['phones'] = array(
                'name' => $name,
                'capabilities' => $capabilities
            );
            update_option('vauv_plugins', $vauv_plugins);
        }
    }

    /**
     * Create post with giving slug to use as 'view' for displaying html-markup
     */
    public static function register_view()
    {
        // check slug and create post with this slug
        $slug = 'phones';
        if (!self::the_slug_exists($slug)) {

            // create post with this slug
            $post_id = wp_insert_post(
                array(
                    'comment_status' => 'closed',
                    'ping_status' => 'closed',
                    'post_author' => 1,
                    'post_name' => $slug,
                    'post_title' => $slug,
                    'post_status' => 'publish',
                    'post_type' => 'page'
                )
            );

            if ($post_id == -1 || $post_id == -2) {
                wp_die("The post whit slug " . $slug . " wasn't created or the page already exists");
            }

        } else {
            wp_die("Slug {$slug} уже занят. Переименуйте пост, имеющий данный slug или удалите и очистите корзину");
        }
    }

    public static function the_slug_exists($post_name)
    {
        global $wpdb;
        if ($wpdb->get_row("SELECT post_name FROM wp_posts WHERE post_name = '" . $post_name . "'", 'ARRAY_A')) {
            return true;
        } else {
            return false;
        }
    }

}