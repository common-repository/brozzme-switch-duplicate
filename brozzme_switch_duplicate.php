<?php

/*
Plugin Name: Brozzme Switch Duplicate
Plugin URI: https://brozzme.com/
Description: Post type switcher and duplicate tools.
Version: 1.6
Author: Benoti
Author URI: https://brozzme.com

*/
defined( 'ABSPATH' ) || exit;

class brozzme_switch_duplicate
{

    /**
     * brozzme_switch_duplicate constructor.
     */
    public function __construct()
    {

        // Define plugin constants
        $this->basename = plugin_basename(__FILE__);
        $this->directory_path = plugin_dir_path(__FILE__);
        $this->directory_url = plugins_url(dirname($this->basename));

        // group menu ID
        $this->plugin_dev_group = 'Brozzme';
        $this->plugin_dev_group_id = 'brozzme-plugins';

        // plugin info
        $this->plugin_name = 'brozzme-switch-duplicate';
        $this->plugin_slug = 'brozzme-switch-duplicate';
        $this->settings_page_slug = 'brozzme-switch-duplicate-settings';
        $this->tools_page_slug = 'brozzme-switch-duplicate-main';
        $this->plugin_version = '1.0';
        $this->plugin_text_domain = 'brozzme-switch-duplicate';

        $this->_define_constants();

        $this->general_options = get_option('bsd_settings');
        $this->switcher_options = get_option('bsd_switcher_settings');
        $this->duplicate_options = get_option('bsd_duplicate_settings');

        add_action( 'admin_enqueue_scripts', array( $this, '_add_settings_styles') );
        $this->_init();

        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'desactivate' ) );
        register_uninstall_hook(    __DIR__ .'/uninstall.php', 'brozzme_switch_duplicate_uninstall' );

    }

    /**
     *
     */
    public function _define_constants(){

        if(is_admin()){
            defined( 'BFSL_PLUGINS_DEV_GROUPE' ) or define( 'BFSL_PLUGINS_DEV_GROUPE', $this->plugin_dev_group );
            defined( 'BFSL_PLUGINS_DEV_GROUPE_ID' ) or define( 'BFSL_PLUGINS_DEV_GROUPE_ID', $this->plugin_dev_group_id );
            defined( 'BFSL_PLUGINS_URL' ) or define( 'BFSL_PLUGINS_URL', $this->directory_url );
            defined( 'BFSL_PLUGINS_SLUG' ) or define( 'BFSL_PLUGINS_SLUG', $this->plugin_slug );


            defined('B7E_SD')    or define('B7E_SD', $this->plugin_name);
            defined('B7E_SD_SLUG')  or define('B7E_SD_SLUG', $this->settings_page_slug);
            defined('B7E_SD_TOOLS')  or define('B7E_SD_TOOLS', $this->tools_page_slug);
            defined('B7E_SD_DIR')    or define('B7E_SD_DIR', $this->directory_path);
            defined('B7E_SD_DIR_URL')    or define('B7E_SD_DIR_URL', $this->directory_url);
            defined('B7E_SD_VERSION')        or define('B7E_SD_VERSION', $this->plugin_version);
            defined('B7E_SD_TEXT_DOMAIN')    or define('B7E_SD_TEXT_DOMAIN', $this->plugin_text_domain);
        }

    }

    /**
     *
     */
    public function _init(){

        if(is_admin()){
            if (!class_exists('brozzme_plugins_page')){
                include_once ($this->directory_path . 'includes/brozzme_plugins_page.php');
            }

            add_filter('plugin_action_links', array($this, 'brozzme_switch_duplicate_action_links'), 10, 2);

            add_action( 'plugins_loaded', array($this, '_load_textdomain') );

            include_once $this->directory_path . 'includes/brozzme_switch_duplicate_settings.php';
            new brozzme_switch_duplicate_Settings();


            if($this->general_options['bsd_enable'] == 'true'){
                if($this->general_options['bsd_enable_switcher'] == 'true'){
                    include_once $this->directory_path . 'includes/brozzme_switch_duplicate_switcher.php';
                    new brozzme_switch_duplicate_switcher();
                }
                if($this->general_options['bsd_enable_duplicate'] == 'true'){
                    include_once $this->directory_path . 'includes/brozzme_switch_duplicate_duplicator.php';
                    new brozzme_switch_duplicate_duplicator();
                }
            }
        }
    }

    /**
     * @param $hook
     */
    public function _add_settings_styles($hook){
        if($hook == 'toplevel_page_' . $this->plugin_dev_group_id || $hook == 'tools_page_'.$this->plugin_slug){
            wp_enqueue_style( $this->plugin_txt_domain, plugin_dir_url( __FILE__ ) . 'css/brozzme-admin-css.css');
        }
    }

    /**
     * @param $links
     * @param $file
     * @return mixed
     */
    public function brozzme_switch_duplicate_action_links($links, $file) {
        static $this_plugin;

        if (!$this_plugin) {
            $this_plugin = plugin_basename(__FILE__);
        }

        if ($file == $this_plugin) {
            $settings_link = '<a href="' . admin_url() . 'admin.php?page='. B7E_SD .'">'.__('Settings', B7E_SD_TEXT_DOMAIN).'</a>';
            array_unshift($links, $settings_link);
        }

        return $links;
    }

    /**
     *
     */
    public function _load_textdomain() {
        load_plugin_textdomain( $this->plugin_text_domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    /**
     *
     */
    public function activate(){

        if(!get_option('bsd_settings')) {

            $options = array(
                'bsd_enable'       => 'true', // set to 1 to enable plugin
                'bsd_enable_duplicate'  => 'true',
                'bsd_enable_switcher'   => 'true',
                'bsd_suppress_options_on_desactivation' => 'false'
            );

            add_option('bsd_settings', $options);

        }

        if(!get_option('bsd_switcher_settings')) {

            $options = array(
                'bsd_post_type' => array('post', 'page'),
                'bsd_post_type_column' => 'true'
            );

            add_option('bsd_switcher_settings', $options);
        }

        if(!get_option('bsd_duplicate_settings')) {

            $options = array(
                'bsd_copy_taxonomy' => 'true',
                'bsd_copy_custom_fields' => 'true'
            );

            add_option('bsd_duplicate_settings', $options);
        }
    }

    /**
     *
     */
    public function desactivate(){

        if($this->general_options['bsd_suppress_options_on_desactivation'] == 'true'){
            //delete_option('bsd_settings');
            //delete_option('bsd_switcher_settings');
            //delete_option('bsd_duplicate_settings');
        }
    }
}

new brozzme_switch_duplicate();