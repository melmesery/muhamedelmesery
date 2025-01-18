<?php
/**
    Plugin Name: Sticky Chat Widget
    Description: Connect with your valuable website visitors through Sticky Chat Widget that consist of current trendy chat options
    Version:     1.3.4
    Author: Ginger Plugins
    Author URI: https://www.gingerplugins.com/downloads/sticky-chat-widget/
    Text Domain: sticky-chat-widget
    Domain Path: /languages
    License: GPL3
 *
 * @author  : Ginger Plugins <gingerplugins@gmail.com>
 * @license : GPL3
 */

defined('ABSPATH') or die('Direct Access is not allowed');


if (!defined('GSB_PLUGIN_URL')) {
    define("GSB_PLUGIN_URL", plugin_dir_url(__FILE__));
}

if (!defined('GSB_PLUGIN_VERSION')) {
    define("GSB_PLUGIN_VERSION", "1.3.4");
}

if (!defined('GSB_PLUGIN_BASE')) {
    define("GSB_PLUGIN_BASE", plugin_basename(__FILE__));
}

if (!defined('GSB_DEV_VERSION')) {
    define("GSB_DEV_VERSION", false);
}

// Include social icon class.
require_once dirname(__FILE__)."/includes/social-icons.php";

require_once dirname(__FILE__)."/includes/front-end.php";


if (!function_exists('wp_doing_ajax')) {


    /**
     * Check if WordPress is currently running an AJAX request.
     *
     * @return bool True if WordPress is currently running an AJAX request, False otherwise.
     * @since  Unknown
     */
    function wp_doing_ajax()
    {
        return apply_filters('wp_doing_ajax', defined('DOING_AJAX') && DOING_AJAX);

    }//end wp_doing_ajax()


}


// Redirect on setting page on activation.
if (!function_exists("scw_redirect_on_activate")) {
    add_action('activated_plugin', 'scw_redirect_on_activate');


    /**
     * Redirect on activate.
     *
     * @param string $plugin The basename of the plugin file.
     *
     * @return void
     * @since  1.0.0
     */
    function scw_redirect_on_activate($plugin)
    {
        if ($plugin == plugin_basename(__FILE__)) {
            if (!defined("DOING_AJAX") && $plugin == plugin_basename(__FILE__)) {
                delete_option("scw_redirect");
                add_option("scw_redirect", 1);
            }
        }

    }//end scw_redirect_on_activate()


}//end if


// Include backend files for settings.
if (is_admin()) {
    include_once dirname(__FILE__)."/admin/admin.php";
    include_once dirname(__FILE__)."/admin/admin-common.php";
}

if (!function_exists("create_contact_form_table")) {
    add_action('init', 'create_contact_form_table');


    /**
     * Create table while install.
     *
     * @since  1.1.2
     * @return null.
     */
    function create_contact_form_table()
    {
        global $wpdb;
        $tableName = $wpdb->prefix.'scw_contact_form_leads';
        $sql       = "CREATE TABLE IF NOT EXISTS {$tableName}
        (
            id mediumint(12) NOT NULL AUTO_INCREMENT,
            name varchar(100),
            email  varchar(128),
            phone  varchar(100),
            message LONGTEXT,
            page_url varchar(200),
            widget_id mediumint(12),
            ip_address char(200),
            is_from_mobile tinyint(10),
            created_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        );";
        $wpdb->query($sql);

        $results = $wpdb->get_results("SHOW FULL COLUMNS FROM $tableName");
        if (!empty($results)) {
            foreach ($results as $row) {
                if ($row->Collation != 'utf8mb4_unicode_ci') {
                    $convert_sql = "ALTER TABLE $tableName CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
                    $wpdb->query($convert_sql);
                }
            }
        }

    }//end create_contact_form_table()


}//end if
