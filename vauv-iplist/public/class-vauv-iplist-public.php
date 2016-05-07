<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://speckombinat.org.ua
 * @since      1.0.0
 *
 * @package    Vauv_Iplist
 * @subpackage Vauv_Iplist/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Vauv_Iplist
 * @subpackage Vauv_Iplist/public
 * @author     deeem <my@email.com>
 */
class Vauv_Iplist_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    private $iplist_db;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     *
     * @param      string $plugin_name The name of the plugin.
     * @param      string $version The version of this plugin.
     * @param $iplist_db
     */
    public function __construct($plugin_name, $version, $iplist_db)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->iplist_db = $iplist_db;
    }

    /**
     * Register the scripts and stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        if (current_user_can('iplist_view') && is_page('iplist')) {

            wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/vauv-iplist-public.css', array(), $this->version, 'all');
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/vauv-iplist-public.js', array('jquery'), $this->version, false);
            wp_localize_script('vauv-iplist', 'ajax_object',
                array(
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('datatable'))
            );
        }

    }

    /**
     * Ajax action method to use with datatables jquery plugin
     */
    public function datatable()
    {
        check_ajax_referer('datatable');

        if(isset($_POST['method']) && $_POST['method'] !==''){
            $method = $_POST['method'];
        }
        if(isset($_POST['param']) && $_POST['param'] !==''){
            $param = $_POST['param'];
        }

        if (current_user_can('iplist_view')) {
            switch ($method) {
                case 'list':
                    // список всех адресов подсети range
                    echo json_encode($this->iplist_db->getList($param));
                    die();
                case 'subnets':
                    // список всех подсетей
                    echo json_encode($this->iplist_db->getSubnets());
                    die();
                case 'search':
                    // поиск
                    echo json_encode($this->iplist_db->search($param));
                    die();
            }
        }

        if (current_user_can('iplist_edit')) {
            switch ($method) {
                case 'save':
                    // обновить запись
                    $this->iplist_db->update(ip2long($param['ip']), $param['data']);
                    die();
                case 'delete':
                    // очистить данные записи ip
                    echo $this->iplist_db->delete(ip2long($param));
                    die();
                case 'add_subnet':
                    echo json_encode($this->iplist_db->addSubnet($param));
                    die();
            }
        }

        die('something wrong');
    }

    /**
     * Displaying the html markup
     *
     * @param $content html markup that page already have
     *
     * @return string page html markup containing iplist
     */
    public function display($content)
    {

        if (current_user_can('iplist_view') && is_page('iplist')) {

            ob_start();
            require_once plugin_dir_path(__FILE__) . '/partials/vauv-iplist-public-display.php';
            $output = ob_get_clean();

            return $output . $content;
        } else {
            return $content;
        }
    }

}
