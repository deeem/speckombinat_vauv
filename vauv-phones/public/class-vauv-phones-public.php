<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       speckombinat.org.ua
 * @since      1.0.0
 *
 * @package    Vauv_Phones
 * @subpackage Vauv_Phones/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Vauv_Phones
 * @subpackage Vauv_Phones/public
 * @author     deeem <dk81@yandex.ru>
 */
class Vauv_Phones_Public
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

    private $phones_db;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     *
     * @param      string $plugin_name The name of the plugin.
     * @param      string $version The version of this plugin.
     * @param $phones_db
     */
    public function __construct($plugin_name, $version, $phones_db)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->phones_db = $phones_db;
    }

    /**
     * Register the scripts and stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        if (is_page('phones')) {
            wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/vauv-phones-public.css', array(), $this->version, 'all');
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/vauv-phones-public.js', array('jquery'), $this->version, false);
            wp_enqueue_style('bootstrap-treeview', plugins_url($this->plugin_name . '/includes/bootstrap-treeview/bootstrap-treeview.min.css'));
            wp_enqueue_script('bootstrap-treeview', plugins_url($this->plugin_name . '/includes/bootstrap-treeview/bootstrap-treeview.min.js'));
            wp_localize_script('vauv-phones', 'ajax',
                array(
                    'url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('phones'))
            );
        }
    }

    /**
     * Display html markup
     *
     * @param $content string html-markup that page already have
     *
     * @return string page html markup containing iplist
     */
    public function display($content)
    {

        if (is_page('phones')) {

            ob_start();
            require_once plugin_dir_path(__FILE__) . '/partials/vauv-phones-public-display.php';
            $output = ob_get_clean();

            $content = $output . $content;
        }

        return $content;
    }

    /**
     * Ajax action method
     */
    public function ajax_phones()
    {
        check_ajax_referer('phones');

        if (isset($_POST['method']) && (!empty($_POST['method']))) {
            $method = $_POST['method'];
        }

        if (isset($_POST['param']) && (!empty($_POST['param']))) {
            $param = $_POST['param'];
        }

        // организации
        if ($method == 'organizations') {
            echo json_encode($this->phones_db->getOrganizations());
        }

        // отделы и абоненты
        if ($method == 'phonebook' && isset($param)) {
            echo json_encode(array(
                'departments' => $this->phones_db->getDepartments($param),
                'subscribers' => $this->phones_db->getSubscribers($param)
            ));
        }

        // абоненты, соответствующие поисковому запросу
        if ($method == 'search' && isset($param)) {
            echo json_encode($this->phones_db->findSubscribers($param));
        }

        die();
    }

}
