<?php
/*
  Plugin Name: Fi-Ware Enablers
  Plugin URI: http://catalogue.fiware.org/enablers
  Description: A WordPress plugin that allows users to login or register by authenticating with an existing Google, Facebook, LinkedIn, Github, Reddit or Windows Live account via OAuth 2.0. Easily drops into new or existing sites, integrates with existing users.
  Version: 0.1
  Author: Mubashar Khokhar
  Author URI: http://mubasharkk.social-gizmo.com
  License: GPL2
 */

namespace ComfNet\Fiware;

use ComfNet\Fiware\Enablers;


include_once 'enablers/enabler.php';

// plugin class:
Class Fiware {

    // ==============
    // INITIALIZATION
    // ==============
    // set a version that we can use for performing plugin updates, this should always match the plugin version:
    const PLUGIN_VERSION = "0.1";

    // singleton class pattern:
    protected static $instance = NULL;

    public static function get_instance() {
        NULL === self::$instance and self::$instance = new self;
        return self::$instance;
    }
    
    private $enablers;

    // define the settings used by this plugin; this array will be used for registering settings, applying default values, and deleting them during uninstall:
    private $settings = array(
        'activated' => array(
            'poi' => '',
        ),
    );

    // when the plugin class gets created, fire the initialization:
    function __construct() {
        // hook activation and deactivation for the plugin:
        register_activation_hook(__FILE__, array($this, 'fw_activate'));
        register_deactivation_hook(__FILE__, array($this, 'fw_deactivate'));
        // hook load event to handle any plugin updates:
        add_action('plugins_loaded', array($this, 'fw_update'));
        // hook init event to handle plugin initialization:
        add_action('init', array($this, 'init'));
    }

    // a wrapper for wordpress' get_option(), this basically feeds get_option() the setting's correct default value as specified at the top of this file:
    /*
      function fw_option($name) {
      // TODO: create the option with a default value if it doesn't exist?
      $val = get_option($name, $settings[$name]);
      return $val;
      }
     */

    // do something during plugin activation:
    function fw_activate() {
        
    }

    // do something during plugin deactivation:
    function fw_deactivate() {
        
    }

    // do something during plugin update:
    function fw_update() {
        $plugin_version = self::PLUGIN_VERSION;
        $installed_version = get_option("fw_plugin_version");
        if (!$installed_version || $installed_version <= 0 || $installed_version != $plugin_version) {
            // version mismatch, run the update logic...
            // add any missing options and set a default (usable) value:
            $this->fw_add_missing_settings();
            // set the new version so we don't trigger the update again:
            update_option("fw_plugin_version", $plugin_version);
            // create an admin notice:
            add_action('admin_notices', array($this, 'fw_update_notice'));
        }
    }

    // indicate to the admin that the plugin has been updated:
    function fw_update_notice() {
        $settings_link = "<a href='options-general.php?page=fiware-enablers.php'>Settings Page</a>"; // CASE SeNsItIvE filename!
        ?>
        <div class="updated">
            <p>FiWare Enablers has been updated! Please review the <?php echo $settings_link ?>.</p>
        </div>
        <?php
    }

    // adds any missing settings and their default values:
    function fw_add_missing_settings() {
        foreach ($this->settings as $setting_name => $default_value) {
            // call add_option() which ensures that we only add NEW options that don't exist:
            if (is_array($this->settings[$setting_name])) {
                $default_value = json_encode($default_value);
            }
            $added = add_option($setting_name, $default_value);
        }
    }

    // restores the default plugin settings:
    function fw_restore_default_settings() {
        foreach ($this->settings as $setting_name => $default_value) {
            // call update_option() which ensures that we update the setting's value:
            if (is_array($this->settings[$setting_name])) {
                $default_value = json_encode($default_value);
            }
            update_option($setting_name, $default_value);
        }
        add_action('admin_notices', array($this, 'fw_restore_default_settings_notice'));
    }

    // indicate to the admin that the plugin has been updated:
    function fw_restore_default_settings_notice() {
        $settings_link = "<a href='options-general.php?page=fiware-enablers.php'>Settings Page</a>"; // CASE SeNsItIvE filename!
        ?>
        <div class="updated">
            <p>The default settings have been restored. You may review the <?php echo $settings_link ?>.</p>
        </div>
        <?php
    }

    // initialize the plugin's functionality by hooking into wordpress:
    function init() {
        foreach($this->settings['activated'] as $enabler=>$status){
            include_once 'enablers'.DIRECTORY_SEPARATOR.$enabler.DIRECTORY_SEPARATOR.$enabler.".php";            
            
            $class = __NAMESPACE__.'\Enablers\\'.strtoupper($enabler);
            $this->enablers[$enabler] = new $class();
        }
        
        $this->fw_init_frontend_scripts_styles();
        $this->fw_init_backend_scripts_styles();
                                
        add_shortcode('fiware', array($this,'parse_short_code'));
        
    }
    
    function parse_short_code($attrs){
        $en = $attrs['enabler'];
        $this->enablers[$en]->parse_short_code($attrs);
    }

    // init scripts and styles for use on FRONTEND PAGES:
    function fw_init_frontend_scripts_styles() {
        
        foreach($this->enablers as $enabler => $obj){
            $this->enablers[$enabler]->_init_frontend_scripts_styles();
        }
        
        // here we "localize" php variables, making them available as a js variable in the browser:
//		$fw_cvars = array(
//			// basic info:
//			'ajaxurl' => admin_url('admin-ajax.php'),
//			'template_directory' => get_bloginfo('template_directory'),
//			'stylesheet_directory' => get_bloginfo('stylesheet_directory'),
//			'plugins_url' => plugins_url(),
//			'plugin_dir_url' => plugin_dir_url(__FILE__),
//			'url' => get_bloginfo('url'),
//			'logout_url' => wp_logout_url(),
//			// other:
//			'show_login_messages' => get_option('fw_show_login_messages'),
//			'logout_inactive_users' => get_option('fw_logout_inactive_users'),
//			'logged_in' => is_user_logged_in(),
//		);
//		wp_enqueue_script('wpoa-cvars', plugins_url('/cvars.js', __FILE__));
//		wp_localize_script('wpoa-cvars', 'fw_cvars', $fw_cvars);
//		// we always need jquery:
//		wp_enqueue_script('jquery');
//		// load the core plugin scripts/styles:
//		wp_enqueue_script('wpoa-script', plugin_dir_url( __FILE__ ) . 'wp-oauth.js', array());
//		wp_enqueue_style('wpoa-style', plugin_dir_url( __FILE__ ) . 'wp-oauth.css', array());
    }

    // init scripts and styles for use on BACKEND PAGES:
    function fw_init_backend_scripts_styles() {
        
        foreach($this->enablers as $enabler => $obj){
            $this->enablers[$enabler]->_init_backend_scripts_styles();
        }
        
        // here we "localize" php variables, making them available as a js variable in the browser:
//		$fw_cvars = array(
//			// basic info:
//			'ajaxurl' => admin_url('admin-ajax.php'),
//			'template_directory' => get_bloginfo('template_directory'),
//			'stylesheet_directory' => get_bloginfo('stylesheet_directory'),
//			'plugins_url' => plugins_url(),
//			'plugin_dir_url' => plugin_dir_url(__FILE__),
//			'url' => get_bloginfo('url'),
//			// other:
//			'show_login_messages' => get_option('fw_show_login_messages'),
//			'logout_inactive_users' => get_option('fw_logout_inactive_users'),
//			'logged_in' => is_user_logged_in(),
//		);
//		wp_enqueue_script('wpoa-cvars', plugins_url('/cvars.js', __FILE__));
//		wp_localize_script('wpoa-cvars', 'fw_cvars', $fw_cvars);
//		// we always need jquery:
//		wp_enqueue_script('jquery');
//		// load the core plugin scripts/styles:
//		wp_enqueue_script('wpoa-script', plugin_dir_url( __FILE__ ) . 'wp-oauth.js', array());
//		wp_enqueue_style('wpoa-style', plugin_dir_url( __FILE__ ) . 'wp-oauth.css', array());
//		// load the default wordpress media screen:
//		wp_enqueue_media();
    }

    // init scripts and styles for use on the LOGIN PAGE:
    function fw_init_login_scripts_styles() {
        // here we "localize" php variables, making them available as a js variable in the browser:
//		$fw_cvars = array(
//			// basic info:
//			'ajaxurl' => admin_url('admin-ajax.php'),
//			'template_directory' => get_bloginfo('template_directory'),
//			'stylesheet_directory' => get_bloginfo('stylesheet_directory'),
//			'plugins_url' => plugins_url(),
//			'plugin_dir_url' => plugin_dir_url(__FILE__),
//			'url' => get_bloginfo('url'),
//			// login specific:
//			'hide_login_form' => get_option('fw_hide_wordpress_login_form'),
//			'logo_image' => get_option('fw_logo_image'),
//			'bg_image' => get_option('fw_bg_image'),
//			'login_message' => $_SESSION['WPOA']['RESULT'],
//			'show_login_messages' => get_option('fw_show_login_messages'),
//			'logout_inactive_users' => get_option('fw_logout_inactive_users'),
//			'logged_in' => is_user_logged_in(),
//		);
//		wp_enqueue_script('wpoa-cvars', plugins_url('/cvars.js', __FILE__));
//		wp_localize_script('wpoa-cvars', 'fw_cvars', $fw_cvars);
//		// we always need jquery:
//		wp_enqueue_script('jquery');
//		// load the core plugin scripts/styles:
//		wp_enqueue_script('wpoa-script', plugin_dir_url( __FILE__ ) . 'wp-oauth.js', array());
//		wp_enqueue_style('wpoa-style', plugin_dir_url( __FILE__ ) . 'wp-oauth.css', array());
    }

    // add a settings link to the plugins page:
    function fw_settings_link($links) {
        $settings_link = "<a href='options-general.php?page=fiware-enablers.php'>Settings</a>"; // CASE SeNsItIvE filename!
        array_unshift($links, $settings_link);
        return $links;
    }

}


function debug_arr($arr, $hault = true){
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
    
    if ($hault) exit;
}


// END OF Enabler CLASS
// instantiate the plugin class ONCE and maintain a single instance (singleton):
Fiware::get_instance();

?>
