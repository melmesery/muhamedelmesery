<?php
/**
 * The admin specific functionality of the plugin.
 *
 * @author  : Ginger Plugins <gingerplugins@gmail.com>
 * @license : GPL2
 * */

defined('ABSPATH') or die('Direct Access is not allowed');

class GP_Admin_Sticky_Chat_Buttons
{

    /**
     * The slug of this plugin.
     *
     * @var    string    $slug    The slug of this plugin.
     * @since  1.1.2
     * @access public
     */
    public $slug = "sticky-chat-widget";


    /**
     * Initialize the class and set its properties.
     *
     * @since 1.1.2
     */
    public function __construct()
    {
        // Enqueue CSS and JavaScript files for Ginger Sticky Chat Widget in the admin.
        add_action('admin_enqueue_scripts', [ $this, 'admin_script' ]);

        // Register a custom post type.
        add_action('init', [$this, 'register_post_type']);

        // Initialize plugin settings.
        add_action('admin_init', [$this, 'migrate_data']);

        // Save Ginger Sticky Chat Widget settings.
        add_action('wp_ajax_save_gsb_buttons_setting', [$this, 'save_gsb_buttons_setting']);

        // Change the status of the widget.
        add_action("wp_ajax_gsb_buttons_change_status", [$this, "gsb_buttons_change_status"]);

        // Remove a widget.
        add_action("wp_ajax_gsb_buttons_remove_widget", [$this, "gsb_buttons_remove_widget"]);

        // Create menu in the admin for Ginger Sticky Chat Widget settings.
        add_action('admin_menu', [$this, 'admin_menu']);

        // Get button data.
        add_action("wp_ajax_get_gb_settings", [$this, "get_gb_settings"]);

        // Add settings and upgrade link to plugin base.
        add_filter('plugin_action_links_'.GSB_PLUGIN_BASE, [$this, 'setting_and_upgrade_link']);

        // Load plugin language files.
        add_action('plugins_loaded', [ $this, 'plugin_language' ]);

        // Handle sign-up page AJAX request.
        add_action('wp_ajax_scw_save_sign_up_info', [$this, "scw_save_sign_up_info"]);

        // Clear cache for the Ginger Sticky Chat Widget plugin.
        add_action('clear_cache_for_scw_plugin', [$this, "clear_cache_for_scw_plugin"]);

        // Handle data migration after plugin update.
        add_action("upgrader_process_complete", [ $this, "migrate_data" ]);

        // Handle data migration during plugin loading.
        add_action("plugins_loaded", [ $this, "migrate_data" ]);

        // Handle AJAX request to create a new widget.
        add_action("wp_ajax_gsb_buttons_create_widget", [$this, "gsb_buttons_create_widget"]);

        // Handle AJAX request to rename a widget.
        add_action("wp_ajax_gsb_buttons_rename_widget", [$this, "gsb_buttons_rename_widget"]);

        // Handle AJAX request to download leads data as CSV.
        add_action('wp_ajax_scw_leads_download_csv', [$this, 'download_csv']);

        // Handle AJAX request to remove leads data.
        add_action('wp_ajax_gsb_buttons_remove_leads', [$this, 'remove_leads']);

        // Handle AJAX request to remove all leads data.
        add_action('wp_ajax_gsb_buttons_remove_all_leads', [$this, 'remove_all_leads']);

        // Handle AJAX request to remove a single lead.
        add_action('wp_ajax_gsb_buttons_remove_single_lead', [$this, 'remove_single_lead']);

        add_action("admin_init", [$this, "scw_activate_redirection"]);

    }//end __construct()


    /**
     * Clears the cache for the SCW plugin by invoking various cache clearing methods for different cache plugins.
     *
     * @return null
     * @since  1.1.2
     */
    public function clear_cache_for_scw_plugin()
    {
        try {
            // W3 Total Cache.
            global $wp_fastest_cache;
            if (function_exists('w3tc_flush_all')) {
                w3tc_flush_all();
            }

            // WP Super Cache.
            if (function_exists('wp_cache_clean_cache')) {
                global $file_prefix, $supercachedir;
                if (empty($supercachedir) && function_exists('get_supercache_dir')) {
                    $supercachedir = get_supercache_dir();
                }

                wp_cache_clean_cache($file_prefix);
            }

            // WP Fastest Cache Plugin.
            if (method_exists('WpFastestCache', 'deleteCache') && !empty($wp_fastest_cache)) {
                $wp_fastest_cache->deleteCache();
            }

            // WP Rocket Plugin.
            if (function_exists('rocket_clean_domain')) {
                rocket_clean_domain();
                // Preload cache.
                if (function_exists('run_rocket_sitemap_preload')) {
                    run_rocket_sitemap_preload();
                }
            }

            // Autoptimize Cache Plugin.
            if (class_exists("autoptimizeCache") && method_exists("autoptimizeCache", "clearall")) {
                autoptimizeCache::clearall();
            }

            // LiteSpeed Plugin.
            if (class_exists("LiteSpeed_Cache_API") && method_exists("autoptimizeCache", "purge_all")) {
                LiteSpeed_Cache_API::purge_all();
            }

            // Breeze Plugin.
            if (class_exists("Breeze_PurgeCache") && method_exists("Breeze_PurgeCache", "breeze_cache_flush")) {
                Breeze_PurgeCache::breeze_cache_flush();
            }

            // Hummingbird.
            if (class_exists('\Hummingbird\Core\Utils')) {
                $modules = \Hummingbird\Core\Utils::get_active_cache_modules();
                foreach ($modules as $module => $name) {
                    $mod = \Hummingbird\Core\Utils::get_module($module);
                    if ($mod->is_active()) {
                        if ($module === 'minify') {
                            $mod->clear_files();
                        } else {
                            $mod->clear_cache();
                        }
                    }
                }
            }

            // WP Total Cache.
            if (function_exists('wp_cache_clean_cache')) {
                global $file_prefix;
                wp_cache_clean_cache($file_prefix, true);
            }

            // Site Optimizer.
            if (class_exists("Supercacher")) {
                if (method_exists("Supercacher", "delete_assets")) {
                    Supercacher::delete_assets();
                }

                if (method_exists("Supercacher", "purge_cache")) {
                    Supercacher::purge_cache();
                }

                if (method_exists("Supercacher", "flush_memcache")) {
                    Supercacher::flush_memcache();
                }

                if (method_exists("Supercacher", "purge_everything")) {
                    Supercacher::purge_everything();
                }
            }

            // WP asset clean up plugin.
            if (class_exists("OptimizeCommon") && method_exists("OptimizeCommon", "clearCache")) {
                OptimizeCommon::clearCache();
            }

            // WP Rocket.
            if (function_exists('rocket_clean_domain')) {
                rocket_clean_domain();
            }

            // WP Rocket: Clear minified CSS and JavaScript files.
            if (function_exists('rocket_clean_minify')) {
                rocket_clean_minify();
            }
        } catch (Exception $e) {
            return 1;
        }//end try

    }//end clear_cache_for_scw_plugin()


    /**
     * Check for redirection and perform redirection if necessary.
     *
     * @return void
     */
    public function scw_activate_redirection()
    {
        if (!defined("DOING_AJAX")) {
            $scw_status = get_option("scw_redirect");
            if ($scw_status) {
                delete_option("scw_redirect");
                wp_redirect(admin_url("admin.php?page=sticky-chat-widget"));
                exit;
            }
        }

    }//end scw_activate_redirection()


    /**
     * Define the language for the Ginger Sticky Chat Widget plugin.
     *
     * @since  1.1.2
     * @return null
     */
    public function plugin_language()
    {
        // Load the translation files for the 'sticky-chat-widget' domain from the 'languages' directory.
        load_plugin_textdomain("sticky-chat-widget", false, dirname(plugin_basename(__FILE__)).'/languages/');

    }//end plugin_language()


    /**
     * Add settings and upgrade link to the plugin action links.
     *
     * @since  1.1.2
     * @param  array $links The existing links associated with the plugin.
     * @return array The modified array of links, including settings and upgrade links.
     */
    public function setting_and_upgrade_link($links)
    {
        // Create a link to the plugin settings page.
        $settings = '<a href="'.admin_url("admin.php?page=sticky-chat-widget").'" ><b>'.esc_attr('Settings', 'sticky-chat-widget').'</b></a>';

        // Insert the settings link at the beginning of the links array.
        array_unshift($links, $settings);

        // Create a link to the "Go Pro" page with specific styling.
        $links['upgrade_link'] = '<a href="'.admin_url("admin.php?page=sticky-chat-widget-upgrade-to-pro").'" style="display: inline-block; color: #e91e63; font-weight: bold;" >'.esc_attr('Go Pro', 'sticky-chat-widget').'</a>';

        // Return the modified array of links.
        return $links;

    }//end setting_and_upgrade_link()


    /**
     * Get the selected channels for the Ginger Sticky Chat Widget.
     *
     * @since  1.1.2
     * @return array The list of selected channels for the widget.
     */
    public static function get_selected_channels()
    {
        // Retrieve the selected channels from the plugin options.
        $channels = get_option("gsb_selected_channels");

        // If no channels are set, return a default array with WhatsApp and Facebook Messenger.
        if ($channels === false) {
            return [
                "whatsapp",
                "facebook_messenger",
            ];
        }

        // Initialize an array to store the selected channels.
        $channelArray = [];

        // If channels are set, process and sanitize the values.
        if (!empty($channels)) {
            // Remove leading and trailing whitespaces and commas.
            $channels = trim($channels);
            $channels = trim($channels, ",");

            // Explode the comma-separated string into an array.
            $channelArray = explode(",", $channels);
        }

        // Return the array of selected channels.
        return $channelArray;

    }//end get_selected_channels()


    /**
     * Get channel settings for a specific button and post ID.
     *
     * @since  1.1.2
     * @return void
     */
    public function get_gb_settings()
    {
        // Initialize status to 0 (default).
        $status = 0;

        // Retrieve button and post ID from the AJAX request.
        $button = filter_input(INPUT_POST, 'button', FILTER_SANITIZE_STRING);
        $postId = filter_input(INPUT_POST, 'postId', FILTER_SANITIZE_STRING);

        // Get the channel settings for the specified button and post ID.
        $message = self::get_channel_settings($button, $postId);

        // If channel settings are retrieved successfully, set status to 1 (success).
        if (!empty($message)) {
            $status = 1;
        }

        // Create a response array with status and message.
        $response = [
            'status'  => $status,
            'message' => $message,
        ];

        // Encode the response in JSON format and exit.
        echo wp_json_encode($response);
        wp_die();

    }//end get_gb_settings()


    /**
     * Get channel setting HTML design.
     *
     * @since  1.1.2
     * @param  string  $button The slug of channel.
     * @param  integer $postId The ID of widget.
     * @return string The html design of channel in json format.
     */
    public static function get_channel_settings($button, $postId)
    {
        $message     = "";
        $socialIcons = Ginger_Social_Icons::icon_list();
        $formIcons   = Ginger_Social_Icons::svg_icons();

        $disabled = "disabled";

        foreach ($socialIcons as $key => $icon) {
            if ($key == $button) {
                ob_start();
                $defaultChannelSettings = Ginger_Social_Icons::get_channel_setting($icon);

                $allChannelSetting = get_post_meta($postId, "channel_settings", true);
                $channelSetting    = isset($allChannelSetting[$button])&&is_array($allChannelSetting[$button]) ? $allChannelSetting[$button] : [];
                $channelSetting    = shortcode_atts($defaultChannelSettings, $channelSetting);

                if ($key == "twitter" && ($channelSetting['bg_color'] == "#65BBF2" || $channelSetting['bg_color'] == '#65bbf2')) {
                    $channelSetting['bg_color'] = "#000000";
                }

                if ($key == "twitter" && ($channelSetting['bg_hover_color'] == "#65BBF2" || $channelSetting['bg_hover_color'] == '#65bbf2')) {
                    $channelSetting['bg_hover_color'] = "#000000";
                }

                $defaultContactFormSetting = Ginger_Social_Icons::get_contact_form_setting($icon);
                $contact_form_setting      = get_post_meta($postId, "contact_form_settings", true);
                $contact_form_setting      = isset($contact_form_setting)&&is_array($contact_form_setting) ? $contact_form_setting : [];
                $contact_form_setting      = shortcode_atts($defaultContactFormSetting, $contact_form_setting);

                $imageUrl   = "";
                $imageClass = "";
                $imageId    = $channelSetting['image_id'];
                if (!empty($imageId)) {
                    $imageData = wp_get_attachment_image_src($imageId, "full");
                    if (!empty($imageData) && isset($imageData[0])) {
                        $imageUrl   = $imageData[0];
                        $imageClass = "has-image";
                    }
                } else if (!empty($channelSetting['icon_class'])) {
                    $imageClass = "has-icon";
                }
                ?>
                <li class="gsb-settings <?php echo ($key == "contact_form") ? "contact-form-li" : "" ?>" id="social-buttons-<?php echo esc_attr($icon['label']) ?>-settings" data-button="<?php echo esc_attr($icon['label']) ?>">
                    <div class="gsb-settings-top">
                        <div class="gsb-free-settings">
                            <div class="gsb-input-icon">
                                <span class="ginger-button-icon <?php echo esc_attr($imageClass) ?> ssb-btn-bg-<?php echo esc_attr($icon['label']) ?>"
                                      data-ginger-tooltip="<?php echo esc_attr($icon['title']) ?>">
                                    <?php if (!empty($imageUrl)) { ?>
                                        <img src="<?php echo esc_url($imageUrl) ?>"
                                             alt="<?php echo esc_attr($icon['title']) ?>"/>
                                    <?php } else if (!empty($channelSetting['icon_class'])) { ?>
                                        <span class="channel-bs-icon">
                                            <i class="<?php echo esc_attr($channelSetting['icon_class']); ?>"></i>
                                        </span>
                                    <?php } ?>
                                    <?php Ginger_Social_Icons::load_and_sanitize_svg($icon['icon']) ?>
                                    <a href="javascript:;" class="remove-channel-img"><span class="dashicons dashicons-no-alt"></span></a>
                                </span>
                            </div>
                            <?php if ($key != "contact_form") { ?>
                            <div class="gsb-input-value">
                                <div class="gp-form-field channel-input">
                                    <div class="gp-form-label">
                                        <label for="ginger_sb_<?php echo esc_attr($icon['label']) ?>_value"><?php echo esc_attr($icon['value']) ?>
                                            <?php if ($icon['label'] == "whatsapp") { ?>
                                                <a data-ginger-tooltip="Fill in your Phone number without any spaces and symbols"
                                                   href="https://faq.whatsapp.com/en/android/26000030/" target="_blank"><span
                                                            class="dashicons dashicons-editor-help"></span></a>
                                            <?php } else if ($icon['label'] == "facebook_messenger") { ?>
                                                <a data-ginger-tooltip="How to find Facebook page ID?"
                                                   href="https://www.facebook.com/help/1503421039731588"
                                                   target="_blank"><span class="dashicons dashicons-editor-help"></span></a>
                                            <?php } else if ($icon['label'] == "line") { ?>
                                                <a data-ginger-tooltip="How to find your link?"
                                                   href="https://developers.line.biz/en/docs/messaging-api/sharing-bot/#add-an-add-friend-button-or-link-to-your-app-or-website"
                                                   target="_blank"><span class="dashicons dashicons-editor-help"></span></a>
                                            <?php } else if ($icon['label'] == "signal") { ?>
                                                <a data-ginger-tooltip="How to get Group Id?"
                                                   href="https://support.signal.org/hc/en-us/articles/360051086971-Group-Link-or-QR-code"
                                                   target="_blank"><span class="dashicons dashicons-editor-help"></span></a>
                                            <?php } ?>
                                        </label>
                                    </div>
                                    <div class="gp-form-input">
                                        <input placeholder="<?php echo esc_attr($icon['example']) ?>"
                                               data-label="<?php echo esc_attr($icon['value']) ?>"
                                               class="setting-input <?php echo esc_attr($icon['class_name']) ?>"
                                               type="text" id="ginger_sb_<?php echo esc_attr($icon['label']) ?>_value"
                                               name="channel_settings[<?php echo esc_attr($button) ?>][value]"
                                               value="<?php echo esc_attr($channelSetting['value']); ?>">
                                    </div>
                                </div>
                            </div>
                                <?php
                            }//end if
                            ?>
                            <div class="gsb-title">
                                <div class="gp-form-field channel-input">
                                    <div class="gp-form-label">
                                        <label for="ginger_sb_<?php echo esc_attr($icon['label']) ?>_title"><?php esc_html_e("On hover text", "sticky-chat-widget") ?></label>
                                    </div>
                                    <div class="gp-form-input">
                                        <input data-label="<?php esc_html_e('Title', 'sticky-chat-widget') ?>" data-channel="<?php echo esc_attr($icon['label']) ?>"
                                               class="setting-input channel-title is-required"
                                               type="text" id="ginger_sb_<?php echo esc_attr($icon['label']) ?>_title"
                                               name="channel_settings[<?php echo esc_attr($button) ?>][title]"
                                               value="<?php echo esc_attr($channelSetting['title']) ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="gsb-more-settings">
                                <a class="remove-channel-setting" href="javascript:;" data-ginger-tooltip="Remove">
                                    <?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['close']) ?>
                                </a>
                            </div>
                        </div>
                        <?php if ($key != "contact_form") { ?>
                            <div class="display-flex">
                                <?php if ($key == "whatsapp") { ?>
                                    <div class="load-more-setting">
                                        <a class="whatsapp-channel-widget-settings" href="javascript:;" data-tab="whatsapp_widget_setting">
                                            <?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['setting']); ?>
                                            <span class="setting-label"><?php esc_html_e("WhatsApp Widget", "sticky-chat-widget"); ?></span>
                                        </a>
                                    </div>
                                    <div class="load-more-setting">
                                        <a class="whatsapp-channel-widget-settings" href="javascript:;" data-tab="whatsapp_general_setting">
                                            <?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['setting']); ?>
                                            <span class="setting-label"><?php esc_html_e("Settings", "sticky-chat-widget"); ?></span>
                                        </a>
                                    </div>
                                <?php } else { ?>
                                    <div class="load-more-setting">
                                        <a class="load-channel-settings" href="javascript:;">
                                            <?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['setting']); ?>
                                            <span class="setting-label"><?php esc_html_e("Settings", "sticky-chat-widget"); ?></span>
                                        </a>
                                    </div>
                                <?php }//end if
                                ?>
                            </div>
                        <?php }//end if
                        ?>
                        <?php if ($key == "contact_form") { ?>
                            <div class="display-flex">
                                <div class="load-more-setting contact-load-more-setting">
                                    <a class="contact-form-more-setting" href="javascript:;" data-tab="form_fields">
                                        <?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['setting']); ?>
                                        <span class="setting-label"><?php esc_html_e("Form Fields", "sticky-chat-widget"); ?></span>
                                    </a>
                                </div>
                                <div class="load-more-setting contact-load-more-setting">
                                    <a class="contact-form-more-setting" href="javascript:;" data-tab="form_settings">
                                        <?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['setting']); ?>
                                        <span class="setting-label"><?php esc_html_e("Form Setting", "sticky-chat-widget"); ?></span>
                                    </a>
                                </div>
                                <div class="load-more-setting contact-load-more-setting">
                                    <a class="contact-form-more-setting" href="javascript:;" data-tab="icon_setting">
                                        <?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['setting']); ?>
                                        <span class="setting-label"><?php esc_html_e("Icon Setting", "sticky-chat-widget"); ?></span>
                                    </a>
                                </div>
                            </div>
                        <?php }//end if
                        ?>
                        <?php if ($key == "whatsapp") { ?>
                            <div class="whatsapp-form-setting">
                                <div class="contact-form-setting-tabs">
                                    <div class="contact-form-setting-tab tab-section whatsapp_widget_setting active" data-id="whatsapp_widget_setting"><?php esc_html_e("Whatsapp Widget", "sticky-chat-widget") ?></div>
                                    <div class="contact-form-setting-tab tab-section whatsapp_general_setting" data-id="whatsapp_general_setting"><?php esc_html_e("Icon Setting", "sticky-chat-widget") ?></div>
                                </div>
                                <div class="contact-form-setting-body">
                                    <div class="tab-setting-section" id="whatsapp_widget_setting">
                                        <div class="whatsapp-popup-settings">
                                            <div class="show-wt-popup-box">
                                                <span class="dashboard-switch in-flex on-off">
                                                    <input type="hidden" name="channel_settings[<?php echo esc_attr($key); ?>][show_whatsapp_popup]" value="no">
                                                    <input type="checkbox" id="show_whatsapp_popup" name="channel_settings[<?php echo esc_attr($key) ?>][show_whatsapp_popup]" value="yes" class="sr-only show-whatsapp-popup" <?php checked($channelSetting['show_whatsapp_popup'], 'yes') ?>>
                                                    <label for="show_whatsapp_popup"><?php esc_html_e("Show WhatsApp chat widget 🗨️", "sticky-chat-widget") ?></label>
                                                </span>
                                            </div>
                                            <div class="<?php echo ($channelSetting['show_whatsapp_popup'] == "yes") ? "" : "add-blur-bg" ?> whatsapp-widget-setting">
                                                <div class="input-settings gp-form-field in-flex bb-none pb-none">
                                                    <div class="gp-form-label pb-9">
                                                        <label><?php esc_html_e("WhatsApp icon", "sticky-chat-widget") ?></label>
                                                    </div>
                                                    <div class="d-in-flex">
                                                        <div class="gp-form-input flex-120">
                                                            <a href="javascript:;" class="image-upload-btn img-profile-upload-btn m-0"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['upload']) ?><span><?php esc_html_e(" Upload", "sticky-chat-widget") ?></span></a>
                                                        </div>
                                                        <div class="custom-profile-img">
                                                            <?php if (!empty($channelSetting['custom_whatsapp_profile'])) { ?>
                                                                <img src="<?php echo esc_url($channelSetting['custom_whatsapp_profile']) ?>" alt="Profile image">
                                                            <?php } ?>
                                                        </div>
                                                        <div class="remove-whatsapp-profile <?php echo (!empty($channelSetting['custom_whatsapp_profile'])) ? "active" : "" ?>"><?php esc_html_e("Remove", "sticky-chat-widget") ?></div>
                                                        <input type="hidden" name="channel_settings[<?php echo esc_attr($key); ?>][custom_whatsapp_profile]" id="custom_whatsapp_profile" value="<?php echo esc_attr($channelSetting['custom_whatsapp_profile']) ?>">
                                                    </div>
                                                </div>
                                                <div class="gp-form-field in-flex">
                                                    <div class="gp-form-label"><label for="whatsapp_popup_title"><?php esc_html_e('Header title', 'sticky-chat-widget') ?></label></div>
                                                    <div class="gp-form-input">
                                                        <input id="whatsapp_popup_title" type="text" name="channel_settings[<?php echo esc_attr($key) ?>][whatsapp_popup_title]" value="<?php echo esc_attr($channelSetting['whatsapp_popup_title']) ?>">
                                                    </div>
                                                </div>
                                                <div class="gp-form-field in-flex">
                                                    <div class="gp-form-label"><label for="whatsapp_popup_sub_title"><?php esc_html_e('Sub header title', 'sticky-chat-widget') ?></label></div>
                                                    <div class="gp-form-input">
                                                        <input id="whatsapp_popup_sub_title" type="text" name="channel_settings[<?php echo esc_attr($key) ?>][whatsapp_popup_sub_title]" value="<?php echo esc_attr($channelSetting['whatsapp_popup_sub_title']) ?>">
                                                    </div>
                                                </div>
                                                <div class="gp-form-field in-flex">
                                                    <div class="gp-form-label"><label><?php esc_html_e("Welcome message", "sticky-chat-widget") ?></label></div>
                                                    <div class="gp-form-input">
                                                        <?php
                                                        $settings = [
                                                            'media_buttons'    => false,
                                                            'wpautop'          => false,
                                                            'drag_drop_upload' => false,
                                                            'textarea_name'    => 'channel_settings['.$key.'][whatsapp_popup_text]',
                                                            'textarea_rows'    => 4,
                                                            'quicktags'        => false,
                                                            'tinymce'          => [
                                                                'toolbar1'    => 'bold, italic, underline',
                                                                'toolbar2'    => '',
                                                                'toolbar3'    => '',
                                                                'content_css' => GSB_PLUGIN_URL.'dist/admin/css/myEditorCSS.css',
                                                            ],
                                                        ];
                                                        wp_editor($channelSetting['whatsapp_popup_text'], "whatsapp_popup_text", $settings);
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="input-settings gp-form-field in-flex bb-none pb-none">
                                                    <div class="gp-form-label">
                                                        <label><?php esc_html_e("Profile image", "sticky-chat-widget") ?></label>
                                                    </div>
                                                    <div class="d-in-flex">
                                                        <div class="gp-form-input flex-120">
                                                            <a href="javascript:;" class="image-upload-btn whatsapp-user-profile-img m-0"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['upload']) ?><span><?php esc_html_e(" Upload", "sticky-chat-widget") ?></span></a>
                                                        </div>
                                                        <div class="custom-user-profile-img">
                                                            <?php if (!empty($channelSetting['whatsapp_user_profile_img'])) { ?>
                                                                <img src="<?php echo esc_url($channelSetting['whatsapp_user_profile_img']) ?>" alt="Profile image">
                                                            <?php } ?>
                                                        </div>
                                                        <div class="remove-whatsapp-user-profile <?php echo (!empty($channelSetting['whatsapp_user_profile_img'])) ? "active" : "" ?>"><?php esc_html_e("Remove", "sticky-chat-widget") ?></div>
                                                        <input type="hidden" name="channel_settings[<?php echo esc_attr($key); ?>][whatsapp_user_profile_img]" id="whatsapp_user_profile_img" value="<?php echo esc_attr($channelSetting['whatsapp_user_profile_img']) ?>">
                                                    </div>
                                                </div>
                                                <div class="gp-form-field in-flex">
                                                    <div class="gp-form-label"><label for="wp_name_to_display"><?php esc_html_e('Name to display', 'sticky-chat-widget') ?></label></div>
                                                    <div class="gp-form-input">
                                                        <input id="wp_name_to_display" type="text" name="channel_settings[<?php echo esc_attr($key) ?>][whatsapp_name_to_display]" value="<?php echo esc_attr($channelSetting['whatsapp_name_to_display']) ?>">
                                                    </div>
                                                </div>
                                                <div class="blur-overlay">
                                                    <div class="disabled-whatsapp-widget">
                                                        <?php esc_html_e("Whatsapp chat widget is disabled", "sticky-chat-widget"); ?>
                                                        <div><a href="javascript:;" class="enable-whatsapp-widget"><?php esc_html_e("Click here", "sticky-chat-widget") ?></a>
                                                            <?php esc_html_e("to enable WhatsApp chat widget", "sticky-chat-widget"); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-setting-section" id="whatsapp_general_setting">
                                        <div class="input-settings device-img-option">
                                            <div class="input-setting for-mobile-desktop device-option-responsive">
                                                <span class="dashboard-switch in-flex on-off">
                                                    <input type="hidden" name="channel_settings[<?php echo esc_attr($button) ?>][for_desktop]" value="no">
                                                    <input type="checkbox" id="ginger_sb_<?php echo esc_attr($icon['label']) ?>_for_desktop"
                                                           name="channel_settings[<?php echo esc_attr($button) ?>][for_desktop]" value="yes"
                                                           class="sr-only btn-for-desktop" <?php checked($channelSetting['for_desktop'], "yes") ?>>
                                                    <label for="ginger_sb_<?php echo esc_attr($icon['label']) ?>_for_desktop"><?php esc_html_e("Desktop", "sticky-chat-widget") ?></label>
                                                </span>
                                                <span class="dashboard-switch in-flex on-off">
                                                    <input type="hidden" name="channel_settings[<?php echo esc_attr($button) ?>][for_mobile]" value="no">
                                                    <input type="checkbox" id="ginger_sb_<?php echo esc_attr($icon['label']) ?>_for_mobile"
                                                           name="channel_settings[<?php echo esc_attr($button) ?>][for_mobile]" value="yes"
                                                           class="sr-only btn-for-mobile" <?php checked($channelSetting['for_mobile'], "yes") ?>>
                                                    <label for="ginger_sb_<?php echo esc_attr($icon['label']) ?>_for_mobile"><?php esc_html_e("Mobile", "sticky-chat-widget") ?></label>
                                                </span>
                                            </div>
                                            <div class="input-setting gp-form-field in-flex custom-img content-center">
                                                <div class="dashboard-switch channel-custom-img">
                                                    <?php esc_html_e("Custom image", "sticky-chat-widget") ?>
                                                </div>
                                                <div class="dashboard-switch upgrade-upload-btn">
                                                    <a
                                                        <?php if (!empty($disabled)) { ?>
                                                            data-ginger-tooltip="<?php esc_html_e("Upgrade to Pro", 'sticky-chat-widget') ?>" target="_blank" href="javascript:;"
                                                        <?php } else { ?>
                                                            href="javascript:;"
                                                        <?php } ?>
                                                            class="image-upload-btn img-upgrade-btn <?php echo esc_attr($disabled) ?>"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['upload']) ?><span><?php esc_html_e(" Upload", "sticky-chat-widget") ?></span></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="gp-form-field in-flex">
                                            <div class="gp-form-label"><label for=""><?php esc_html_e("Background color", "sticky-chat-widget") ?></label>
                                            </div>
                                            <div class="gp-form-input color-section">
                                                <div class="color-choice">
                                                    <div class="channel-color-picker-section">
                                                        <div class="flex-center">
                                                            <div class="margin-right pr-5"><?php esc_html_e("Default", "sticky-chat-widget") ?></div>
                                                            <div class="pr-5">
                                                                <input type="text"
                                                                       name="channel_settings[<?php echo esc_attr($button) ?>][bg_color]"
                                                                       class="color-picker channel-bg-color"
                                                                       style="background: <?php echo esc_attr($channelSetting['bg_color']) ?>"
                                                                       value="<?php echo esc_attr($channelSetting['bg_color']) ?>">
                                                            </div>
                                                        </div>
                                                        <div class="flex-center">
                                                            <div class="pr-5"><?php esc_html_e("On hover", "sticky-chat-widget") ?></div>
                                                            <div class="pr-5">
                                                                <input type="text"
                                                                       name="channel_settings[<?php echo esc_attr($button) ?>][bg_hover_color]"
                                                                       class="color-picker channel-bg-hover-color"
                                                                       style="background: <?php echo esc_attr($channelSetting['bg_hover_color']) ?>"
                                                                       value="<?php echo esc_attr($channelSetting['bg_hover_color']) ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="gp-form-field in-flex">
                                            <div class="gp-form-label"><label for=""><?php esc_html_e("Icon color", "sticky-chat-widget") ?></label></div>
                                            <div class="gp-form-input color-section">
                                                <div class="color-choice">
                                                    <div class="channel-color-picker-section">
                                                        <div class="flex-center">
                                                            <div class="margin-right pr-5"><?php esc_html_e("Default", "sticky-chat-widget") ?></div>
                                                            <div class="pr-5">
                                                                <input type="text"
                                                                       name="channel_settings[<?php echo esc_attr($button) ?>][text_color]"
                                                                       class="color-picker channel-text-color"
                                                                       style="background: <?php echo esc_attr($channelSetting['text_color']) ?>"
                                                                       value="<?php echo esc_attr($channelSetting['text_color']) ?>">
                                                            </div>
                                                        </div>
                                                        <div class="flex-center">
                                                            <div class="pr-5"><?php esc_html_e("On hover", "sticky-chat-widget") ?></div>
                                                            <div class="pr-5">
                                                                <input type="text"
                                                                       name="channel_settings[<?php echo esc_attr($button) ?>][text_hover_color]"
                                                                       class="color-picker channel-text-hover-color"
                                                                       style="background: <?php echo esc_attr($channelSetting['text_hover_color']) ?>"
                                                                       value="<?php echo esc_attr($channelSetting['text_hover_color']) ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php $whatsappMessage = isset($channelSetting['whatsapp_message']) ? $channelSetting['whatsapp_message'] : ""; ?>
                                        <div class="gp-form-field in-flex">
                                            <div class="gp-form-label"><label for="whatsapp_message"><?php esc_html_e("Pre defined message", "sticky-chat-widget") ?></label></div>
                                            <div class="gp-form-input">
                                                <input type="text" id="whatsapp_message" value="<?php echo esc_attr($whatsappMessage) ?>" class="medium-input"
                                                       name="channel_settings[<?php echo esc_attr($button) ?>][whatsapp_message]">
                                                <span class="scw-badges wp-badges"><?php esc_html_e("{page_url}", "sticky-chat-widget") ?></span>
                                                <span class="scw-badges wp-badges"><?php esc_html_e("{page_title}", "sticky-chat-widget") ?></span>
                                            </div>
                                        </div>
                                        <div class="gp-form-field in-flex mobile-link-settings <?php echo ($channelSetting['show_whatsapp_popup'] == "yes") ? "" : "active" ?>">
                                            <span class="dashboard-switch in-flex on-off">
                                                <input type="hidden" name="channel_settings[<?php echo esc_attr($button) ?>][is_mobile_link]" value="no">
                                                <input type="checkbox" id="ginger_sb_<?php echo esc_attr($icon['label']) ?>_mobile_link"
                                                       name="channel_settings[<?php echo esc_attr($button) ?>][is_mobile_link]" value="yes"
                                                       class="sr-only" <?php checked($channelSetting['is_mobile_link'], "yes") ?>>
                                                <label for="ginger_sb_<?php echo esc_attr($icon['label']) ?>_mobile_link">
                                                    <?php esc_html_e("Use ", "sticky-chat-widget") ?><span class="link-color"><?php esc_html_e("whatsapp://send ", "sticky-chat-widget") ?></span><?php esc_html_e("as a link in mobile", "sticky-chat-widget") ?>
                                                    <span aria-hidden="true" class="ginger-info" data-ginger-tooltip="<?php esc_html_e("when this option is enabled, it will open WhatsApp app in mobile if installed , if WhatsApp app is not installed this option will not work.", 'sticky-chat-widget') ?>"><span class="dashicons dashicons-editor-help"></span></span>
                                                </label>
                                            </span>
                                        </div>
                                        <div class="gp-form-field in-flex">
                                            <div class="gp-form-label"><label for="custom_id_<?php echo esc_attr($icon['label']) ?>"><?php esc_html_e("Custom ID", "sticky-chat-widget") ?></label></div>
                                            <div class="gp-form-input">
                                                <input <?php echo esc_attr($disabled) ?> id="custom_id_<?php echo esc_attr($icon['label']) ?>" type="text"
                                                                                         name="channel_settings[<?php echo esc_attr($button) ?>][custom_id]" value="<?php echo esc_attr($channelSetting['custom_id']) ?>">
                                                <?php if (!empty($disabled)) { ?>
                                                    <a class="upgrade-link" href="javascript:;" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['pro']); ?></a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="gp-form-field in-flex">
                                            <div class="gp-form-label"><label for="custom_class_<?php echo esc_attr($button) ?>"><?php esc_html_e("Custom class", "sticky-chat-widget") ?></label>
                                            </div>
                                            <div class="gp-form-input">
                                                <input <?php echo esc_attr($disabled) ?> id="custom_class_<?php echo esc_attr($icon['label']) ?>" type="text"
                                                                                         name="channel_settings[<?php echo esc_attr($button) ?>][custom_class]" value="<?php echo esc_attr($channelSetting['custom_class']) ?>">
                                                <?php if (!empty($disabled)) { ?>
                                                    <a class="upgrade-link" href="javascript:;" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['pro']); ?></a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }//end if
                        ?>
                        <?php if ($key == "contact_form") {
                            ?>
                            <div class="contact-form-setting">
                                <div class="contact-form-setting-tabs">
                                    <div class="contact-form-setting-tab tab-section form_fields active" data-id="form_fields"><?php esc_html_e("Form Fields", "sticky-chat-widget") ?></div>
                                    <div class="contact-form-setting-tab tab-section form_settings" data-id="form_settings"><?php esc_html_e("Form Setting", "sticky-chat-widget") ?></div>
                                    <div class="contact-form-setting-tab tab-section icon_setting" data-id="icon_setting"><?php esc_html_e("Icon Setting", "sticky-chat-widget") ?></div>
                                </div>
                                <div class="contact-form-setting-body">
                                    <div class="tab-setting-section" id="icon_setting">
                                        <div class="input-settings device-img-option">
                                            <div class="input-setting for-mobile-desktop device-option-responsive">
                                                <span class="dashboard-switch in-flex on-off">
                                                    <input type="hidden" name="channel_settings[<?php echo esc_attr($button) ?>][for_desktop]" value="no">
                                                    <input type="checkbox" id="ginger_sb_<?php echo esc_attr($icon['label']) ?>_for_desktop"
                                                           name="channel_settings[<?php echo esc_attr($button) ?>][for_desktop]" value="yes"
                                                           class="sr-only btn-for-desktop" <?php checked($channelSetting['for_desktop'], "yes") ?>>
                                                    <label for="ginger_sb_<?php echo esc_attr($icon['label']) ?>_for_desktop"><?php esc_html_e("Desktop", "sticky-chat-widget") ?></label>
                                                </span>
                                                <span class="dashboard-switch in-flex on-off">
                                                    <input type="hidden" name="channel_settings[<?php echo esc_attr($button) ?>][for_mobile]" value="no">
                                                    <input type="checkbox" id="ginger_sb_<?php echo esc_attr($icon['label']) ?>_for_mobile"
                                                           name="channel_settings[<?php echo esc_attr($button) ?>][for_mobile]" value="yes"
                                                           class="sr-only btn-for-mobile" <?php checked($channelSetting['for_mobile'], "yes") ?>>
                                                    <label for="ginger_sb_<?php echo esc_attr($icon['label']) ?>_for_mobile"><?php esc_html_e("Mobile", "sticky-chat-widget") ?></label>
                                                </span>
                                            </div>
                                            <div class="input-setting gp-form-field in-flex custom-img content-center">
                                                <div class="dashboard-switch channel-custom-img">
                                                    <?php esc_html_e("Custom image", "sticky-chat-widget") ?>
                                                </div>
                                                <div class="dashboard-switch upgrade-upload-btn">
                                                    <a
                                                        <?php if (!empty($disabled)) { ?>
                                                            data-ginger-tooltip="<?php esc_html_e("Upgrade to Pro", 'sticky-chat-widget') ?>" target="_blank" href="javascript:;"
                                                        <?php } else { ?>
                                                            href="javascript:;"
                                                        <?php } ?>
                                                            class="image-upload-btn img-upgrade-btn <?php echo esc_attr($disabled) ?>"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['upload']); ?><span><?php esc_html_e(" Upload", "sticky-chat-widget") ?></span></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="gp-form-field in-flex">
                                            <div class="gp-form-label"><label for=""><?php esc_html_e("Background color", "sticky-chat-widget") ?></label>
                                            </div>
                                            <div class="gp-form-input color-section">
                                                <div class="color-choice">
                                                    <div class="channel-color-picker-section">
                                                        <div class="flex-center">
                                                            <div class="margin-right pr-5"><?php esc_html_e("Default", "sticky-chat-widget") ?></div>
                                                            <div class="pr-5">
                                                                <input type="text"
                                                                       name="channel_settings[<?php echo esc_attr($button) ?>][bg_color]"
                                                                       class="color-picker channel-bg-color"
                                                                       style="background: <?php echo esc_attr($channelSetting['bg_color']) ?>"
                                                                       value="<?php echo esc_attr($channelSetting['bg_color']) ?>">
                                                            </div>
                                                        </div>
                                                        <div class="flex-center">
                                                            <div class="pr-5"><?php esc_html_e("On hover", "sticky-chat-widget") ?></div>
                                                            <div class="pr-5">
                                                                <input type="text"
                                                                       name="channel_settings[<?php echo esc_attr($button) ?>][bg_hover_color]"
                                                                       class="color-picker channel-bg-hover-color"
                                                                       style="background: <?php echo esc_attr($channelSetting['bg_hover_color']) ?>"
                                                                       value="<?php echo esc_attr($channelSetting['bg_hover_color']) ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="gp-form-field in-flex">
                                            <div class="gp-form-label"><label for=""><?php esc_html_e("Icon color", "sticky-chat-widget") ?></label></div>
                                            <div class="gp-form-input color-section">
                                                <div class="color-choice">
                                                    <div class="channel-color-picker-section">
                                                        <div class="flex-center">
                                                            <div class="margin-right pr-5"><?php esc_html_e("Default", "sticky-chat-widget") ?></div>
                                                            <div class="pr-5">
                                                                <input type="text"
                                                                       name="channel_settings[<?php echo esc_attr($button) ?>][text_color]"
                                                                       class="color-picker channel-text-color"
                                                                       style="background: <?php echo esc_attr($channelSetting['text_color']) ?>"
                                                                       value="<?php echo esc_attr($channelSetting['text_color']) ?>">
                                                            </div>
                                                        </div>
                                                        <div class="flex-center">
                                                            <div class="pr-5"><?php esc_html_e("On hover", "sticky-chat-widget") ?></div>
                                                            <div class="pr-5">
                                                                <input type="text"
                                                                       name="channel_settings[<?php echo esc_attr($button) ?>][text_hover_color]"
                                                                       class="color-picker channel-text-hover-color"
                                                                       style="background: <?php echo esc_attr($channelSetting['text_hover_color']) ?>"
                                                                       value="<?php echo esc_attr($channelSetting['text_hover_color']) ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="gp-form-field in-flex">
                                            <div class="gp-form-label"><label for="custom_id_<?php echo esc_attr($icon['label']) ?>"><?php esc_html_e("Custom ID", "sticky-chat-widget") ?></label></div>
                                            <div class="gp-form-input">
                                                <input <?php echo esc_attr($disabled) ?> id="custom_id_<?php echo esc_attr($icon['label']) ?>" type="text"
                                                                                         name="channel_settings[<?php echo esc_attr($button) ?>][custom_id]" value="<?php echo esc_attr($channelSetting['custom_id']) ?>">
                                                <?php if (!empty($disabled)) { ?>
                                                    <a class="upgrade-link"
                                                       href="javascript:;"
                                                       target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['pro']); ?></a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="gp-form-field in-flex">
                                            <div class="gp-form-label"><label for="custom_class_<?php echo esc_attr($button) ?>"><?php esc_html_e("Custom class", "sticky-chat-widget") ?></label>
                                            </div>
                                            <div class="gp-form-input">
                                                <input <?php echo esc_attr($disabled) ?> id="custom_class_<?php echo esc_attr($icon['label']) ?>" type="text"
                                                                                         name="channel_settings[<?php echo esc_attr($button) ?>][custom_class]" value="<?php echo esc_attr($channelSetting['custom_class']) ?>">
                                                <?php if (!empty($disabled)) { ?>
                                                    <a class="upgrade-link"
                                                       href="javascript:;"
                                                       target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['pro']); ?></a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="form_fields" class="tab-setting-section active">
                                        <div class="toggle-fields contact-form-toggle-fields">
                                            <?php
                                            $fields = $contact_form_setting['fields'];
                                            if(!isset($fields['consent_checkbox'])) {
                                                $fields['consent_checkbox'] = [
                                                    'label'            => esc_html__('Consent Checkbox', 'sticky-chat-widget'),
                                                    'placeholder_text' => esc_html__('I accept terms & conditions', 'sticky-chat-widget'),
                                                    'is_visible'       => 1,
                                                    'is_required'      => 1,
                                                    'required_msg'     => esc_html__('This field is required', 'sticky-chat-widget')
                                                ];
                                            }
                                            foreach ($fields as $key1 => $field) { ?>
                                                <div class="toggle-field" data-type="<?php echo esc_attr($key1); ?>">
                                                    <div class="toggle-field-title <?php echo ($field['is_visible'] == 1) ? "toggle-field-clickable" : "" ?>">
                                                        <span class="dashboard-switch in-flex on-off visible_check_toggle">
                                                            <input type="hidden" name="contact_form_settings[fields][<?php echo esc_attr($key1) ?>][is_visible]" value="0">
                                                            <input type="checkbox" id="contact_form_<?php echo esc_attr($key1) ?>_visible" name="contact_form_settings[fields][<?php echo esc_attr($key1) ?>][is_visible]" value="1" class="sr-only visible_check" <?php checked($field['is_visible'], "1") ?>>
                                                            <label for="contact_form_<?php echo esc_attr($key1) ?>_visible"></label>
                                                        </span>
                                                        <div class="toggle-field-label"><?php echo esc_attr($field['label']) ?></div>
                                                        <div class="toggle-field-setting <?php echo ($field['is_visible'] == 1) ? "active" : "" ?>"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['edit']); ?></div>
                                                    </div>
                                                    <div class="toggle-field-content">
                                                        <div class="gp-form-field channel-input in-flex">
                                                            <div class="gp-form-label">
                                                                <label for="contact_form_<?php echo esc_attr($key1) ?>_title"><?php esc_html_e("Field label", "sticky-chat-widget") ?></label>
                                                            </div>
                                                            <div class="gp-form-input">
                                                                <input class="contact-form-input" type="text" id="contact_form_<?php echo esc_attr($key1) ?>_title" name="contact_form_settings[fields][<?php echo esc_attr($key1) ?>][label]" value="<?php echo esc_attr($field['label']) ?>" placeholder="<?php esc_html_e("Enter label", "sticky-chat-widget") ?>">
                                                            </div>
                                                        </div>
                                                        <div class="gp-form-field channel-input in-flex">
                                                            <div class="gp-form-label">
                                                                <label for="contact_form_<?php echo esc_attr($key1) ?>_placeholder"><?php esc_html_e("Field message text", "sticky-chat-widget") ?></label>
                                                            </div>
                                                            <div class="gp-form-input">
                                                                <?php
                                                                if($key1 == "consent_checkbox") {
                                                                    $consentSettings = [
                                                                        'media_buttons' => false,
                                                                        'wpautop' => false,
                                                                        'drag_drop_upload' => false,
                                                                        'textarea_name' => 'contact_form_settings[fields][' . $key1 . '][placeholder_text]',
                                                                        'textarea_rows' => 4,
                                                                        'quicktags' => false,
                                                                        'tinymce' => [
                                                                            'toolbar1' => 'bold, italic, underline, link, forecolor, backcolor',
                                                                            'toolbar2' => '',
                                                                            'toolbar3' => '',
                                                                            'content_css' => GSB_PLUGIN_URL . 'dist/admin/css/myEditorCSS.css',
                                                                        ],
                                                                    ];
                                                                    wp_editor($field['placeholder_text'], "consent_message_text", $consentSettings);
                                                                } else { ?>
                                                                    <input class="contact_form_custom_value" type="text" id="contact_form_<?php echo esc_attr($key1) ?>_placeholder" name="contact_form_settings[fields][<?php echo esc_attr($key1) ?>][placeholder_text]" value="<?php echo esc_attr($field['placeholder_text']) ?>" placeholder="<?php esc_html_e("Enter placeholder text", "sticky-chat-widget") ?>">
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        <div class="gp-form-field channel-input in-flex">
                                                            <div class="gp-form-label">
                                                                <label for="contact_form_<?php echo esc_attr($key1) ?>_required"><?php esc_html_e("Is required?", "sticky-chat-widget") ?></label>
                                                            </div>
                                                            <div class="gp-form-input">
                                                                <span class="dashboard-switch in-flex on-off">
                                                                    <input type="hidden" name="contact_form_settings[fields][<?php echo esc_attr($key1) ?>][is_required]" value="0">
                                                                    <input type="checkbox" id="contact_form_<?php echo esc_attr($key1) ?>_required" name="contact_form_settings[fields][<?php echo esc_attr($key1) ?>][is_required]" value="1" class="sr-only required_check" <?php checked($field['is_required'], "1") ?>>
                                                                    <label for="contact_form_<?php echo esc_attr($key1) ?>_required"></label>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="gp-form-field channel-input in-flex required-field-message <?php echo ($field['is_required'] == "1") ? "active" : "" ?>">
                                                            <div class="gp-form-label">
                                                                <label for="contact_form_<?php echo esc_attr($key1) ?>_require_msg"><?php esc_html_e("Required error message", "sticky-chat-widget") ?><span style="color: #ff0000"> *</span></label>
                                                            </div>
                                                            <div class="gp-form-input">
                                                                <input data-label="<?php esc_html_e('Required error message', 'sticky-chat-widget') ?>" class="<?php echo ($field['is_required'] == "1") ? "is-required" : "" ?> toggle-field-required" type="text" id="contact_form_<?php echo esc_attr($key1) ?>_require_msg" name="contact_form_settings[fields][<?php echo esc_attr($key1) ?>][required_msg]" value="<?php echo esc_attr($field['required_msg']) ?>">
                                                            </div>
                                                        </div>
                                                        <?php if ($key1 == 'email') { ?>
                                                            <div class="gp-form-field channel-input in-flex disabled-field">
                                                                <div class="gp-form-label">
                                                                    <label for="contact_form_<?php echo esc_attr($key1) ?>_email_suggestion"><?php esc_html_e("Show email suggestion", "sticky-chat-widget") ?></label>
                                                                </div>
                                                                <div class="gp-form-input">
                                                                    <span class="dashboard-switch in-flex on-off">
                                                                        <input type="checkbox" id="contact_form_<?php echo esc_attr($key1) ?>_email_suggestion" name="" value="1" class="sr-only" disabled>
                                                                        <label for="contact_form_<?php echo esc_attr($key1) ?>_email_suggestion"></label>
                                                                    </span>
                                                                    <a class="upgrade-link in-block" href="javascript:;" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['pro']); ?></a>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                        <?php if ($key1 == 'phone') { ?>
                                                            <div class="gp-form-field channel-input in-flex disabled-field">
                                                                <div class="gp-form-label">
                                                                    <label for="contact_form_<?php echo esc_attr($key1) ?>_country_dropdown"><?php esc_html_e("Show country dropdown", "sticky-chat-widget") ?></label>
                                                                </div>
                                                                <div class="gp-form-input">
                                                                    <span class="dashboard-switch in-flex on-off">
                                                                        <input type="checkbox" id="contact_form_<?php echo esc_attr($key1) ?>_country_dropdown" name="" value="1" class="sr-only" disabled>
                                                                        <label for="contact_form_<?php echo esc_attr($key1) ?>_country_dropdown"></label>
                                                                    </span>
                                                                    <a class="upgrade-link in-block" href="javascript:;" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['pro']); ?></a>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <?php
                                            }//end foreach
                                            ?>
                                        </div>
                                        <div class="add-new-custom-field contact-form-field-option">
                                            <a href="javascript:;" class="add-contact-custom-field add-custom-field-btn">
                                                <span class="dashicons dashicons-plus"></span>
                                                <?php esc_html_e("Add custom field", "sticky-chat-widget") ?>
                                            </a>
                                            <?php if (!empty($disabled)) { ?>
                                                <a class="upgrade-link in-block" href="javascript:;" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['pro']); ?></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="tab-setting-section" id="form_settings">
                                        <div class="">
                                            <div class="contact-form-color-option-inner">
                                                <div class="gp-form-field in-flex">
                                                    <div class="gp-form-label"><label for="form_text_<?php echo esc_attr($icon['label']) ?>"><?php esc_html_e("Form title", "sticky-chat-widget") ?><span style="color: #ff0000"> *</span></label></div>
                                                    <div class="gp-form-input">
                                                        <input id="form_text_<?php echo esc_attr($icon['label']) ?>" class="contact-btn-text is-required" type="text" name="contact_form_settings[form_title]" value="<?php echo esc_attr($contact_form_setting['form_title']) ?>" data-label="<?php esc_html_e("Form title", "sticky-chat-widget") ?>">
                                                    </div>
                                                </div>
                                                <div class="gp-form-field in-flex">
                                                    <div class="gp-form-label"><label for="success_msg_<?php echo esc_attr($icon['label']) ?>"><?php esc_html_e("Success message", "sticky-chat-widget") ?><span style="color: #ff0000"> *</span></label></div>
                                                    <div class="gp-form-input">
                                                        <input id="success_msg_<?php echo esc_attr($icon['label']) ?>" class="contact-btn-text is-required" type="text" name="contact_form_settings[success_msg]" value="<?php echo esc_attr($contact_form_setting['success_msg']) ?>" data-label="<?php esc_html_e("Success message", "sticky-chat-widget") ?>">
                                                    </div>
                                                </div>
                                                <div class="gp-form-field in-flex">
                                                    <div class="gp-form-input">
                                                <span class="dashboard-switch in-flex on-off">
                                                    <input type="hidden" name="contact_form_settings[is_redirect]" value="0">
                                                    <input type="checkbox" id="is_redirect_<?php echo esc_attr($icon['label']) ?>" name="contact_form_settings[is_redirect]" value="1" class="sr-only redirect_check" <?php checked($contact_form_setting['is_redirect'], "1") ?>>
                                                    <label for="is_redirect_<?php echo esc_attr($icon['label']) ?>"><?php esc_html_e("Redirect visitor after submit", "sticky-chat-widget") ?></label>
                                                </span>
                                                    </div>
                                                </div>
                                                <div class="gp-form-field in-flex redirect-field-contact <?php echo ($contact_form_setting['is_redirect'] == 1) ? "active" : "" ?>">
                                                    <div class="gp-form-label"><label for="redirect_url_<?php echo esc_attr($icon['label']) ?>"><?php esc_html_e("Redirect URL", "sticky-chat-widget") ?><span style="color: #ff0000"> *</span></label></div>
                                                    <div class="gp-form-input">
                                                        <input id="redirect_url_<?php echo esc_attr($icon['label']) ?>" class="contact-btn-text <?php echo ($contact_form_setting['is_redirect'] == 1) ? "is-required" : "" ?>" type="text" name="contact_form_settings[redirect_url]" value="<?php echo esc_attr($contact_form_setting['redirect_url']) ?>" data-label="<?php esc_html_e("Redirect Url", "sticky-chat-widget") ?>">
                                                    </div>
                                                </div>
                                                <div class="gp-form-field in-flex mt-5 redirect-field-contact <?php echo ($contact_form_setting['is_redirect'] == 1) ? "active" : "" ?>">
                                                    <div class="gp-form-label"></div>
                                                    <div class="gp-form-input">
                                                        <input type="hidden" name="contact_form_settings[is_redirect_new_tab]" value="0">
                                                        <span class="checkbox-custom">
                                                        <input id="redirect_tab_<?php echo esc_attr($icon['label']) ?>" class="contact-btn-text sr-only" type="checkbox" name="contact_form_settings[is_redirect_new_tab]" value="1" <?php checked($contact_form_setting['is_redirect_new_tab'], "1") ?>>
                                                        <label for="redirect_tab_<?php echo esc_attr($icon['label']) ?>"><?php esc_html_e("Open in new tab", "sticky-chat-widget") ?></label>
                                                    </span>
                                                    </div>
                                                </div>
                                                <div class="gp-form-field in-flex">
                                                    <div class="gp-form-input">
                                                        <span class="dashboard-switch in-flex on-off">
                                                            <input type="hidden" name="contact_form_settings[is_close_aftr_submit]" value="0">
                                                            <input type="checkbox" id="close_after_Submit_<?php echo esc_attr($icon['label']) ?>" name="contact_form_settings[is_close_aftr_submit]" value="1" class="sr-only close_after_submit_check" <?php checked($contact_form_setting['is_close_aftr_submit'], "1") ?>>
                                                            <label for="close_after_Submit_<?php echo esc_attr($icon['label']) ?>"><?php esc_html_e("Close form after submit", "sticky-chat-widget") ?></label>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="gp-form-field in-flex close-after-field-contact <?php echo ($contact_form_setting['is_close_aftr_submit'] == 1) ? "active" : "" ?>">
                                                    <label for="close_after_sec_<?php echo esc_attr($icon['label']) ?>">
                                                        <?php esc_html_e("Close after ", "sticky-chat-widget") ?>
                                                        <input id="close_after_sec_<?php echo esc_attr($icon['label']) ?>" class="contact-btn-text tiny-input only-numeric" type="text" name="contact_form_settings[close_after_sec]" value="<?php echo esc_attr($contact_form_setting['close_after_sec']) ?>">
                                                        <?php esc_html_e(" seconds", "sticky-chat-widget") ?>
                                                    </label>
                                                </div>
                                                <div class="gp-form-field in-flex activate-general-setting">
                                                    <div class="gp-form-input">
                                                    <span class="dashboard-switch in-flex on-off">
                                                        <input type="hidden" name="contact_form_settings[is_send_leads]" value="0">
                                                        <input type="checkbox" id="send_leads_<?php echo esc_attr($icon['label']) ?>" disabled name="contact_form_settings[is_send_leads]" value="1" class="sr-only send_leads_to_mail" <?php checked($contact_form_setting['is_send_leads'], "1") ?>>
                                                        <label for="send_leads_<?php echo esc_attr($icon['label']) ?>"><?php esc_html_e("Send leads to mail", "sticky-chat-widget") ?></label>
                                                        <?php if (!empty($disabled)) { ?>
                                                            <a class="upgrade-link in-block" href="javascript:;" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['pro']); ?></a>
                                                        <?php } ?>
                                                    </span>
                                                    </div>
                                                </div>
                                                <div class="gp-form-field in-flex activate-general-setting">
                                                    <div class="gp-form-input">
                                                        <span class="dashboard-switch in-flex on-off">
                                                            <input type="hidden" name="contact_form_settings[auto_responder]" value="0">
                                                            <input type="checkbox" id="auto_responder_<?php echo esc_attr($icon['label']) ?>" disabled name="contact_form_settings[auto_responder]" value="1" class="sr-only auto_responder" <?php checked($contact_form_setting['auto_responder'], 1) ?>>
                                                            <label for="auto_responder_<?php echo esc_attr($icon['label']) ?>"><?php esc_html_e("Auto responder", "sticky-chat-widget") ?></label>
                                                            <?php if (!empty($disabled)) { ?>
                                                                <a class="upgrade-link in-block" href="javascript:;" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['pro']); ?></a>
                                                            <?php } ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="gp-form-field in-flex activate-general-setting">
                                                    <div class="gp-form-input">
                                                        <span class="dashboard-switch in-flex on-off">
                                                            <input type="hidden" name="contact_form_settings[google_captcha]" value="0">
                                                            <input type="checkbox" id="google_captcha_<?php echo esc_attr($icon['label']) ?>" disabled name="contact_form_settings[google_captcha]" value="1" class="sr-only" <?php checked($contact_form_setting['google_captcha'], 1) ?>>
                                                            <label for="google_captcha_<?php echo esc_attr($icon['label']) ?>"><?php esc_html_e("Enable reCAPTCHA", "sticky-chat-widget") ?></label>
                                                            <?php if (!empty($disabled)) { ?>
                                                                <a class="upgrade-link in-block" href="javascript:;" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['pro']); ?></a>
                                                            <?php } ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="gp-form-field in-flex activate-general-setting">
                                                    <div class="gp-form-input">
                                                        <span class="dashboard-switch in-flex on-off">
                                                            <input type="hidden" name="contact_form_settings[sends_leads_to_mailchimp]" value="0">
                                                            <input type="checkbox" id="sends_leads_to_mailchimp_<?php echo esc_attr($icon['label']) ?>" disabled name="contact_form_settings[sends_leads_to_mailchimp]" value="1" class="sr-only" <?php checked($contact_form_setting['sends_leads_to_mailchimp'], 1) ?>>
                                                            <label for="sends_leads_to_mailchimp_<?php echo esc_attr($icon['label']) ?>"><?php esc_html_e("Sends leads to mailchimp", "sticky-chat-widget") ?></label>
                                                            <?php if (!empty($disabled)) { ?>
                                                                <a class="upgrade-link in-block" href="javascript:;" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['pro']); ?></a>
                                                            <?php } ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="gp-form-field in-flex activate-general-setting">
                                                    <div class="gp-form-input">
                                                        <span class="dashboard-switch in-flex on-off">
                                                            <input type="hidden" name="contact_form_settings[sends_leads_to_mailpoet]" value="0">
                                                            <input type="checkbox" id="sends_leads_to_mailpoet_<?php echo esc_attr($icon['label']) ?>" disabled name="contact_form_settings[sends_leads_to_mailpoet]" value="1" class="sr-only" <?php checked($contact_form_setting['sends_leads_to_mailpoet'], 1) ?>>
                                                            <label for="sends_leads_to_mailpoet_<?php echo esc_attr($icon['label']) ?>"><?php esc_html_e("Sends leads to mailpoet", "sticky-chat-widget") ?></label>
                                                            <?php if (!empty($disabled)) { ?>
                                                                <a class="upgrade-link in-block" href="javascript:;" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['pro']); ?></a>
                                                            <?php } ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="contact-form-color-option-inner">
                                                <label class="button-setting-label"><?php esc_html_e("Button Settings", "sticky-chat-widget") ?></label>
                                                <div class="gp-form-field in-flex">
                                                    <div class="gp-form-label"><label for="button_text_<?php echo esc_attr($icon['label']) ?>"><?php esc_html_e("Button text", "sticky-chat-widget") ?><span style="color: #ff0000"> *</span></label></div>
                                                    <div class="gp-form-input">
                                                        <input id="button_text_<?php echo esc_attr($icon['label']) ?>" class="contact-btn-text is-required" type="text" name="contact_form_settings[btn_text]" value="<?php echo esc_attr($contact_form_setting['btn_text']) ?>" data-label="<?php esc_html_e("Button text", "sticky-chat-widget") ?>">
                                                    </div>
                                                </div>
                                                <div class="gp-form-field in-flex">
                                                    <div class="gp-form-label"><label for=""><?php esc_html_e("Text color", "sticky-chat-widget") ?></label></div>
                                                    <div class="gp-form-input color-section">
                                                        <div class="color-choice">
                                                            <div class="channel-color-picker-section">
                                                                <div class="flex-center">
                                                                    <div class="margin-right pr-5"><?php esc_html_e("Default", "sticky-chat-widget") ?></div>
                                                                    <div class="pr-5">
                                                                        <input type="text"
                                                                               name="contact_form_settings[btn_color]"
                                                                               class="color-picker channel-btn-text-color"
                                                                               style="background: <?php echo esc_attr($contact_form_setting['btn_color']) ?>"
                                                                               value="<?php echo esc_attr($contact_form_setting['btn_color']) ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="flex-center">
                                                                    <div class="pr-5"><?php esc_html_e("On hover", "sticky-chat-widget") ?></div>
                                                                    <div class="pr-5">
                                                                        <input type="text"
                                                                               name="contact_form_settings[btn_hover_color]"
                                                                               class="color-picker channel-btn-text-hover-color"
                                                                               style="background: <?php echo esc_attr($contact_form_setting['btn_hover_color']) ?>"
                                                                               value="<?php echo esc_attr($contact_form_setting['btn_hover_color']) ?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="gp-form-field in-flex">
                                                    <div class="gp-form-label"><label for=""><?php esc_html_e("Background color", "sticky-chat-widget") ?></label></div>
                                                    <div class="gp-form-input color-section">
                                                        <div class="color-choice">
                                                            <div class="channel-color-picker-section">
                                                                <div class="flex-center">
                                                                    <div class="margin-right pr-5"><?php esc_html_e("Default", "sticky-chat-widget") ?></div>
                                                                    <div class="pr-5">
                                                                        <input type="text"
                                                                               name="contact_form_settings[btn_bg_color]"
                                                                               class="color-picker channel-btn-bg-color"
                                                                               style="background: <?php echo esc_attr($contact_form_setting['btn_bg_color']) ?>"
                                                                               value="<?php echo esc_attr($contact_form_setting['btn_bg_color']) ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="flex-center">
                                                                    <div class="pr-5"><?php esc_html_e("On hover", "sticky-chat-widget") ?></div>
                                                                    <div class="pr-5">
                                                                        <input type="text"
                                                                               name="contact_form_settings[btn_bg_hover_color]"
                                                                               class="color-picker channel-btn-bg-hover-color"
                                                                               style="background: <?php echo esc_attr($contact_form_setting['btn_bg_hover_color']) ?>"
                                                                               value="<?php echo esc_attr($contact_form_setting['btn_bg_hover_color']) ?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }//end if
                        ?>
                        <?php if ($key != "contact_form" && $key != "whatsapp") { ?>
                        <div class="pro-settings">
                            <div class="pro-content-to-show">
                                <div class="input-settings device-img-option">
                                    <div class="input-setting for-mobile-desktop device-option-responsive">
                                        <span class="dashboard-switch in-flex on-off">
                                            <input type="hidden" name="channel_settings[<?php echo esc_attr($button) ?>][for_desktop]" value="no">
                                            <input type="checkbox" id="ginger_sb_<?php echo esc_attr($icon['label']) ?>_for_desktop"
                                                   name="channel_settings[<?php echo esc_attr($button) ?>][for_desktop]" value="yes"
                                                   class="sr-only btn-for-desktop" <?php checked($channelSetting['for_desktop'], "yes") ?>>
                                            <label for="ginger_sb_<?php echo esc_attr($icon['label']) ?>_for_desktop"><?php esc_html_e("Desktop", "sticky-chat-widget") ?></label>
                                        </span>
                                        <span class="dashboard-switch in-flex on-off">
                                            <input type="hidden" name="channel_settings[<?php echo esc_attr($button) ?>][for_mobile]" value="no">
                                            <input type="checkbox" id="ginger_sb_<?php echo esc_attr($icon['label']) ?>_for_mobile"
                                                   name="channel_settings[<?php echo esc_attr($button) ?>][for_mobile]" value="yes"
                                                   class="sr-only btn-for-mobile" <?php checked($channelSetting['for_mobile'], "yes") ?>>
                                            <label for="ginger_sb_<?php echo esc_attr($icon['label']) ?>_for_mobile"><?php esc_html_e("Mobile", "sticky-chat-widget") ?></label>
                                        </span>
                                    </div>
                                    <div class="input-setting gp-form-field in-flex custom-img content-center">
                                        <div class="channel-custom-img dashboard-switch">
                                            <?php esc_html_e("Custom image", "sticky-chat-widget") ?>
                                        </div>
                                        <div class="upgrade-upload-btn dashboard-switch">
                                            <a
                                                <?php if (!empty($disabled) && $key != "link" && $key != "custom-link") { ?>
                                                    data-ginger-tooltip="<?php esc_html_e("Upgrade to Pro", 'sticky-chat-widget') ?>" target="_blank" href="javascript:;"
                                                <?php } else { ?>
                                                    href="javascript:;"
                                                <?php } ?>
                                                    class="image-upload-btn img-upgrade-btn <?php echo ( $key != "link" && $key != "custom-link") ? esc_attr($disabled) : "" ?>"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['upload']); ?><span><?php esc_html_e(" Upload", "sticky-chat-widget") ?></span></a>
                                            <?php if ($key == "link" || $key == "custom-link") { ?>
                                                <div class="upload-image-selection-container">
                                                    <div class="upload-image-selection">
                                                        <ul>
                                                            <li class="image-upload-gallery"><a href="javascript:;"><?php esc_html_e("Media library", "sticky-chat-widget") ?></a></li>
                                                            <li class="image-select-icon <?php echo esc_attr($disabled) ?> " id="<?php echo esc_attr($icon['label']) ?>-icon-picker"><a <?php if (!empty($disabled)) { ?>
                                                                    data-ginger-tooltip="<?php esc_html_e("Upgrade to Pro", 'sticky-chat-widget') ?>" target="_blank" href="<?php echo esc_url(self::upgrade_url()) ?>"
                                                           <?php } else { ?>
                                                                    href="javascript:;"
                                                           <?php } ?>><?php esc_html_e("Icon", "sticky-chat-widget") ?></a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="upload-image-selection-overlay"></div>
                                                <input type="hidden" name="channel_settings[<?php echo esc_attr($button) ?>][image_id]"
                                                       id="image_for_<?php echo esc_attr($icon['label']) ?>"
                                                       value="<?php echo esc_attr($imageId) ?>">
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="gp-form-field in-flex">
                                    <div class="gp-form-label"><label for=""><?php esc_html_e("Background color", "sticky-chat-widget") ?></label>
                                    </div>
                                    <div class="gp-form-input color-section">
                                        <div class="color-choice">
                                            <div class="channel-color-picker-section">
                                                <div class="flex-center">
                                                    <div class="margin-right pr-5"><?php esc_html_e("Default", "sticky-chat-widget") ?></div>
                                                    <div class="pr-5">
                                                        <input type="text"
                                                               name="channel_settings[<?php echo esc_attr($button) ?>][bg_color]"
                                                               class="color-picker channel-bg-color"
                                                               style="background: <?php echo esc_attr($channelSetting['bg_color']) ?>"
                                                               value="<?php echo esc_attr($channelSetting['bg_color']) ?>">
                                                    </div>
                                                </div>
                                                <div class="flex-center">
                                                    <div class="pr-5"><?php esc_html_e("On hover", "sticky-chat-widget") ?></div>
                                                    <div class="pr-5">
                                                        <input type="text"
                                                               name="channel_settings[<?php echo esc_attr($button) ?>][bg_hover_color]"
                                                               class="color-picker channel-bg-hover-color"
                                                               style="background: <?php echo esc_attr($channelSetting['bg_hover_color']) ?>"
                                                               value="<?php echo esc_attr($channelSetting['bg_hover_color']) ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="gp-form-field in-flex">
                                    <div class="gp-form-label"><label for=""><?php esc_html_e("Icon color", "sticky-chat-widget") ?></label></div>
                                    <div class="gp-form-input color-section">
                                        <div class="color-choice">
                                            <div class="channel-color-picker-section">
                                                <div class="flex-center">
                                                    <div class="margin-right pr-5"><?php esc_html_e("Default", "sticky-chat-widget") ?></div>
                                                    <div class="pr-5">
                                                        <input type="text"
                                                               name="channel_settings[<?php echo esc_attr($button) ?>][text_color]"
                                                               class="color-picker channel-text-color"
                                                               style="background: <?php echo esc_attr($channelSetting['text_color']) ?>"
                                                               value="<?php echo esc_attr($channelSetting['text_color']) ?>">
                                                    </div>
                                                </div>
                                                <div class="flex-center">
                                                    <div class="pr-5"><?php esc_html_e("On hover", "sticky-chat-widget") ?></div>
                                                    <div class="pr-5">
                                                        <input type="text"
                                                               name="channel_settings[<?php echo esc_attr($button) ?>][text_hover_color]"
                                                               class="color-picker channel-text-hover-color"
                                                               style="background: <?php echo esc_attr($channelSetting['text_hover_color']) ?>"
                                                               value="<?php echo esc_attr($channelSetting['text_hover_color']) ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if($icon['label'] == "instagram") { ?>
                                    <div class="gp-form-field in-flex instagram-link-settings">
                                        <span class="dashboard-switch in-flex on-off">
                                            <input type="hidden" name="channel_settings[<?php echo esc_attr($button) ?>][is_ig_link]" value="no">
                                            <input type="checkbox" id="ginger_sb_<?php echo esc_attr($icon['label']) ?>_ig_link"
                                                   name="channel_settings[<?php echo esc_attr($button) ?>][is_ig_link]" value="yes"
                                                   class="sr-only" <?php checked($channelSetting['is_ig_link'], "yes") ?>>
                                            <label for="ginger_sb_<?php echo esc_attr($icon['label']) ?>_ig_link">
                                                <?php esc_html_e("Use ", "sticky-chat-widget") ?><span class="link-color"><?php esc_html_e("https://ig.me/m ", "sticky-chat-widget") ?></span><?php esc_html_e("as a link", "sticky-chat-widget") ?>
                                            </label>
                                        </span>
                                    </div>
                                <?php } ?>
                                <?php if ($icon['label'] == "mail") {
                                    $emailSubject = isset($channelSetting['email_subject']) ? $channelSetting['email_subject'] : ""; ?>
                                    <div class="gp-form-field in-flex">
                                        <div class="gp-form-label"><label for="email_subject"><?php esc_html_e("Email subject", "sticky-chat-widget") ?></label></div>
                                        <div class="gp-form-input">
                                            <input type="text" id="email_subject" value="<?php echo esc_attr($emailSubject) ?>"
                                                   name="channel_settings[<?php echo esc_attr($button) ?>][email_subject]">
                                            <span class="scw-badges mail-badges"><?php esc_html_e("{page_url}", "sticky-chat-widget") ?></span>
                                            <span class="scw-badges mail-badges"><?php esc_html_e("{page_title}", "sticky-chat-widget") ?></span>
                                        </div>
                                    </div>
                                <?php } else if ($icon['label'] == "sms") {
                                    $smsMessage = isset($channelSetting['sms_message']) ? $channelSetting['sms_message'] : ''; ?>
                                    <div class="gp-form-field in-flex">
                                        <div class="gp-form-label"><label for="sms_message"><?php esc_html_e("Pre defined message", "sticky-chat-widget") ?></label></div>
                                        <div class="gp-form-input">
                                            <input type="text" id="sms_message" value="<?php echo esc_attr($smsMessage) ?>" class="medium-input"
                                                   name="channel_settings[<?php echo esc_attr($button) ?>][sms_message]">
                                            <span class="scw-badges sms-badges"><?php esc_html_e("{page_url}", "sticky-chat-widget") ?></span>
                                            <span class="scw-badges sms-badges"><?php esc_html_e("{page_title}", "sticky-chat-widget") ?></span>
                                        </div>
                                    </div>
                                <?php }//end if
                                ?>
                                <?php if($icon['label'] == "link" || $icon['label'] == "custom-link") { ?>
                                    <div class="gp-form-field in-flex instagram-link-settings">
                                        <div class="kl-checkbox">
                                            <input type="hidden" name="channel_settings[<?php echo esc_attr($button) ?>][open_in_new_tab]" value="no">
                                            <input type="checkbox" id="ginger_sb_<?php echo esc_attr($icon['label']) ?>_open_in_new_tab"
                                                   name="channel_settings[<?php echo esc_attr($button) ?>][open_in_new_tab]" value="yes"
                                                   class="sr-only" <?php checked($channelSetting['open_in_new_tab'], "yes") ?>>
                                            <label for="ginger_sb_<?php echo esc_attr($icon['label']) ?>_open_in_new_tab">
                                                <?php esc_html_e("Open in a new tab", "sticky-chat-widget") ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ($icon['label'] == "wechat") { ?>
                                    <div class="gp-form-field in-flex mb-20">
                                        <div class="gp-form-label">
                                            <label for=""><?php esc_html_e("Upload QR code", "sticky-chat-widget"); ?></label>
                                        </div>
                                        <div class="gp-form-input wechat-qr-code-img-box">
                                            <a href="javascript:;" class="qr-img-upload-btn">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19 13V14.2C19 15.8802 19 16.7202 18.673 17.362C18.3854 17.9265 17.9265 18.3854 17.362 18.673C16.7202 19 15.8802 19 14.2 19H5.8C4.11984 19 3.27976 19 2.63803 18.673C2.07354 18.3854 1.6146 17.9265 1.32698 17.362C1 16.7202 1 15.8802 1 14.2V13M15 6L10 1M10 1L5 6M10 1V13" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                                <span> Upload</span>
                                            </a>
                                            <input type="hidden" name="channel_settings[<?php echo esc_attr($button) ?>][wechat_qr_img]" id="wechat_qr_code_input" value="<?php echo esc_attr($channelSetting['wechat_qr_img']) ?>">
                                            <div class="wechat-qr-code-img">
                                                <img src="<?php echo esc_url($channelSetting['wechat_qr_img']) ?>" alt="Wechat QR code">
                                            </div>
                                            <div class="wechat-qr-img-remove"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['trash']); ?></div>
                                        </div>
                                    </div>
                                    <div class="wechat-qr-setting-box <?php echo (!empty($channelSetting['wechat_qr_img'])) ? "active" : ""; ?>">
                                        <div class="gp-form-field in-flex">
                                            <div class="gp-form-label"><label for="wechat_qr_popup_heading"><?php esc_html_e("Heading", "sticky-chat-widget") ?></label></div>
                                            <div class="gp-form-input">
                                                <input type="text" id="wechat_qr_popup_heading" value="<?php echo esc_attr($channelSetting['wechat_qr_popup_heading']) ?>" class="medium-input"
                                                       name="channel_settings[<?php echo esc_attr($button) ?>][wechat_qr_popup_heading]">
                                            </div>
                                        </div>
                                        <div class="gp-form-field in-flex">
                                            <div class="gp-form-label">
                                                <label for="wechat_qr_popup_bg_color"><?php esc_html_e("Header background", "sticky-chat-widget") ?></label>
                                            </div>
                                            <div class="gp-form-input ginger-color-list">
                                                <input id="wechat_qr_popup_bg_color" class="color-picker" type="text" name="channel_settings[<?php echo esc_attr($button) ?>][wechat_qr_bg_color]" value="<?php echo esc_attr($channelSetting['wechat_qr_bg_color']) ?>" style="background: <?php echo esc_attr($channelSetting['wechat_qr_bg_color']) ?>">
                                            </div>
                                        </div>
                                        <div class="gp-form-field in-flex">
                                            <div class="gp-form-label"><label for="wechat_qr_heading"><?php esc_html_e("QR code heading", "sticky-chat-widget") ?></label></div>
                                            <div class="gp-form-input">
                                                <input type="text" id="wechat_qr_heading" value="<?php echo esc_attr($channelSetting['wechat_qr_heading']) ?>" class="medium-input"
                                                       name="channel_settings[<?php echo esc_attr($button) ?>][wechat_qr_heading]">
                                            </div>
                                        </div>
                                    </div>
                                <?php }//end if ?>
                                <div class="gp-form-field in-flex">
                                    <div class="gp-form-label"><label for="custom_id_<?php echo esc_attr($icon['label']) ?>"><?php esc_html_e("Custom ID", "sticky-chat-widget") ?></label></div>
                                    <div class="gp-form-input">
                                        <input <?php echo esc_attr($disabled) ?> id="custom_id_<?php echo esc_attr($icon['label']) ?>" type="text"
                                         name="channel_settings[<?php echo esc_attr($button) ?>][custom_id]" value="<?php echo esc_attr($channelSetting['custom_id']) ?>">
                                        <?php if (!empty($disabled)) { ?>
                                            <a class="upgrade-link"
                                               href="javascript:;"
                                               target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['pro']); ?></a>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="gp-form-field in-flex">
                                    <div class="gp-form-label"><label for="custom_class_<?php echo esc_attr($button) ?>"><?php esc_html_e("Custom class", "sticky-chat-widget") ?></label>
                                    </div>
                                    <div class="gp-form-input">
                                        <input <?php echo esc_attr($disabled) ?> id="custom_class_<?php echo esc_attr($icon['label']) ?>" type="text"
                                         name="channel_settings[<?php echo esc_attr($button) ?>][custom_class]" value="<?php echo esc_attr($channelSetting['custom_class']) ?>">
                                        <?php if (!empty($disabled)) { ?>
                                            <a class="upgrade-link"
                                               href="javascript:;"
                                               target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['pro']); ?></a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php }//end if
                        ?>
                    </div>
                </li>
                <?php
                $message = ob_get_clean();
            }//end if
        }//end foreach

        return $message;

    }//end get_channel_settings()


    /**
     * Register a custom post type for Ginger Sticky Chat Widget buttons.
     *
     * @since  1.1.2
     * @return void
     */
    public function register_post_type()
    {
        // Register the custom post type 'gsb_buttons'.
        register_post_type(
            'gsb_buttons',
            // Custom Post Type (CPT) Options.
            [
                'labels'       => [
                    'name'          => __('Sticky Widgets', 'sticky-chat-widget'),
                    'singular_name' => __('Sticky Widget', 'sticky-chat-widget'),
                ],
                'public'       => false,
                // Do not make the custom post type public.
                'has_archive'  => false,
                // Do not create an archive page for this post type.
                'rewrite'      => ['slug' => 'gsb_buttons'],
                // Set the slug for the post type.
                'show_in_rest' => false,
                // Disable REST API support for this post type.
            ]
        );

    }//end register_post_type()


    /**
     * Migrate option values to post meta values for Ginger Sticky Chat Widget.
     *
     * @since  1.1.2
     * @return void
     */
    public function migrate_data()
    {
        // Check if migration has already been performed.
        $flag = get_option("is_scw_database_migrated");

        // If migration has not been performed, proceed with migration.
        if ($flag === false) {
            // Set a flag indicating that the migration has been performed.
            add_option("is_scw_database_migrated", 1);

            // Retrieve the widget status option.
            $widgetStatus = get_option("gsb_widget_active");

            // If widget status option is not set, return.
            if ($widgetStatus === false) {
                return;
            }

            // Retrieve the selected channels option.
            $selectedChannels = get_option("gsb_selected_channels");

            // If selected channels option is not empty, process and migrate the data.
            if (!empty($selectedChannels)) {
                // Sanitize and process the selected channels.
                $selectedChannels = trim($selectedChannels);
                $selectedChannels = trim($selectedChannels, ",");
                $selectedChannels = explode(",", $selectedChannels);

                // Initialize an array to store channel settings.
                $channels = [];

                // Retrieve settings for each selected channel and store in the $channels array.
                foreach ($selectedChannels as $channel) {
                    $channels[$channel] = get_option("ginger_sb_".$channel);
                }

                // Retrieve various widget settings from options.
                $widgetSettings       = get_option("gsb_widget_settings");
                $triggerRuleSettings  = get_option("gsb_trigger_rules");
                $pageRuleSettings     = get_option("gsb_page_rules");
                $displayRuleSettings  = get_option("gsb_time_rules");
                $tooltipSettings      = get_option("gsb_tooltip_settings");
                $gglAnalyticsSettings = get_option("gsb_google_analytics");
                $buttonCss            = get_option("gsb_button_css");

                // Create a new post for storing migrated settings.
                $postID = 0;
                if (empty($postID)) {
                    $postData = [
                        'post_title'  => 'Settings',
                        'post_status' => 'publish',
                        'post_type'   => 'gsb_buttons',
                    ];
                    $postID   = wp_insert_post($postData);
                }

                // If post is created, add post meta with migrated settings.
                if (!empty($postID)) {
                    add_post_meta($postID, "channel_settings", $channels);
                    add_post_meta($postID, "selected_channels", get_option("gsb_selected_channels"));
                    add_post_meta($postID, "widget_settings", $widgetSettings);
                    add_post_meta($postID, "trigger_rules", $triggerRuleSettings);
                    add_post_meta($postID, "page_rules", $pageRuleSettings);
                    add_post_meta($postID, "display_rules", $displayRuleSettings);
                    add_post_meta($postID, "tooltip_settings", $tooltipSettings);
                    add_post_meta($postID, "google_analytics", $gglAnalyticsSettings);
                    add_post_meta($postID, "widget_status", $widgetStatus);
                    add_post_meta($postID, "button_css", $buttonCss);
                }

                // Clear cookies related to the widget.
                setcookie("scw-button", -1, (time() - 3600), "/");
                setcookie("scw-status", -1, (time() - 3600), "/");

                // Delete old options that are now migrated to post meta.
                delete_option("gsb_selected_channels");

                $socialIcons = Ginger_Social_Icons::icon_list();
                foreach ($socialIcons as $icon) {
                    delete_option("ginger_sb_".$icon['label']);
                }

                delete_option("gsb_widget_settings");
                delete_option("gsb_trigger_rules");
                delete_option("gsb_page_rules");
                delete_option("gsb_time_rules");
                delete_option("gsb_tooltip_settings");
                delete_option("gsb_updated_date");
                delete_option("gsb_google_analytics");
                delete_option("gsb_widget_active");
                delete_option("gsb_button_css");

                // Trigger an action to clear cache for the Ginger Sticky Chat Widget plugin.
                do_action("clear_cache_for_scw_plugin");
            }//end if
        }//end if

    }//end migrate_data()


    /**
     * Enqueue scripts and styles for the admin page.
     *
     * @param string $hook The current admin page hook.
     */
    public function admin_script($hook)
    {

        // Set minified version based on the development version flag.
        $minified = ".min";
        if (GSB_DEV_VERSION) {
            $minified = "";
        }

        // Enqueue scripts and styles based on the admin page.
        if ($hook == 'toplevel_page_sticky-chat-widget') {
            // Enqueue main admin script.
            wp_enqueue_script('gsb-admin-script', GSB_PLUGIN_URL."dist/admin/js/script.js", ['jquery', 'wp-color-picker', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable', 'jquery-ui-slider'], GSB_PLUGIN_VERSION, true);

            // Enqueue Ajax submit script.
            wp_enqueue_script('gsb-admin-ajax-script', GSB_PLUGIN_URL."dist/admin/js/jquery.ajaxsubmit.js", [], GSB_PLUGIN_VERSION, true);

            // Enqueue SumoSelect script.
            wp_enqueue_script('gsb-sumo-select-script', GSB_PLUGIN_URL."dist/admin/js/jquery.sumoselect.min.js", [], GSB_PLUGIN_VERSION, true);

            // Enqueue International Telephone Input script.
            wp_enqueue_script("gsb-country-min", GSB_PLUGIN_URL.'dist/admin/js/intlTelInput-jquery.min.js', ['jquery'], GSB_PLUGIN_VERSION, true);

            // Enqueue WordPress color picker styles.
            wp_enqueue_style('wp-color-picker');

            // Enqueue Font Awesome styles.
            wp_enqueue_style('ssb-font-awesome', GSB_PLUGIN_URL."dist/admin/css/fontwesome.all.min.css", [], GSB_PLUGIN_VERSION);

            // Enqueue SumoSelect styles.
            wp_enqueue_style('gsb-sumo-select', GSB_PLUGIN_URL."dist/admin/css/sumoselect.css", [], GSB_PLUGIN_VERSION);

            // Enqueue main admin styles.
            wp_enqueue_style('gsb-admin-style', GSB_PLUGIN_URL."dist/admin/css/style.css", [], GSB_PLUGIN_VERSION);

            // Enqueue custom admin styles.
            wp_enqueue_style('gsb-admin-custom-style', GSB_PLUGIN_URL."dist/admin/css/custom.css", [], GSB_PLUGIN_VERSION);

            // Enqueue International Telephone Input styles.
            wp_enqueue_style('gsb-admin-country', GSB_PLUGIN_URL."dist/admin/css/intlTelInput.css", [], GSB_PLUGIN_VERSION);

            // Enqueue the right-to-left (RTL) stylesheet for the GSB plugin's admin section.
            wp_enqueue_style('gsb-rtl-style', GSB_PLUGIN_URL."dist/admin/css/style-rtl.css", [], GSB_PLUGIN_VERSION);

            // Enqueue WordPress media scripts.
            wp_enqueue_media();

            // Enqueue additional scripts for Picmo.
            wp_enqueue_script("gsb-picmo-umd", GSB_PLUGIN_URL.'dist/admin/js/picmo-umd.min.js', ['jquery'], GSB_PLUGIN_VERSION, true);
            wp_enqueue_script("gsb-picmo-latest-umd", GSB_PLUGIN_URL.'dist/admin/js/picmo-latest-umd.min.js', ['jquery'], GSB_PLUGIN_VERSION, true);

            // Check and enqueue signup styles if needed.
            $flag = get_option($this->slug."-subscribe-hide");
            if ($flag == false) {
                wp_enqueue_style($this->slug.'-signup-style', GSB_PLUGIN_URL."dist/admin/css/sign-up.css", [], GSB_PLUGIN_VERSION);
            }

            // Dynamically generate CSS for social icons based on settings.
            $socialIcons = Ginger_Social_Icons::icon_list();
            $css         = "";
            if (!empty($socialIcons)) {
                foreach ($socialIcons as $icon) {
                    if ($icon['label'] != "instagram") {
                        $css .= ".social-icon.active .ssb-btn-".esc_attr($icon['label']).", .social-icon:hover .ssb-btn-".esc_attr($icon['label'])." {background-color: ".esc_attr($icon['color'])."; border-color: ".esc_attr($icon['color'])."; color: #ffffff;}";
                        $css .= ".social-icon.active .ssb-btn-".esc_attr($icon['label'])." svg, .social-icon:hover .ssb-btn-".esc_attr($icon['label'])." svg {color: #ffffff; fill: #ffffff;}";
                        $css .= ".ssb-btn-bg-".esc_attr($icon['label'])." {background-color: ".esc_attr($icon['color']).";}";
                    }
                }
            }

            // Add dynamically generated CSS to main admin style.
            wp_add_inline_style('gsb-admin-style', $css);

            $show_popup = "";
            if (isset($_GET['get_popup']) && $_GET['get_popup'] == 1) {
                $show_popup = 1;
            }

            $settingExists = 0;
            if ($this->isSettingExists()) {
                $settingExists = 1;
            }

            // Localize script with necessary data.
            wp_localize_script(
                'gsb-admin-script',
                'BUTTON_SETTINGS',
                [
                    'ajax_url'          => admin_url('admin-ajax.php'),
                    'required_message'  => esc_html__("%s is required", "sticky-chat-widget"),
                    'font_size_message' => esc_html__("Font size must be smaller than icon size", "sticky-chat-widget"),
                    'nonce'             => wp_create_nonce("gsb_buttons_create_widget"),
                    'go_pro_url'        => self::upgrade_url(),
                    'content_css'       => GSB_PLUGIN_URL.'dist/admin/css/myEditorCSS.css',
                    'show_popup'        => $show_popup,
                    'isSettingExists'   => $settingExists,
                ]
            );
        } else if ($hook == "sticky-chat-widget_page_sticky-chat-widget-analytics") {
            wp_enqueue_style('gsb-analytics-style', GSB_PLUGIN_URL."dist/admin/css/widget-analytics.css", [], GSB_PLUGIN_VERSION);
        } else if ($hook == "sticky-chat-widget_page_sticky-chat-widget-leads") {
            // Enqueue scripts and styles for leads page.
            wp_enqueue_script('gsb-admin-ajax-script', GSB_PLUGIN_URL."dist/admin/js/jquery.ajaxsubmit.js", [], GSB_PLUGIN_VERSION, true);
            wp_enqueue_script('gsb-date-time-picker', GSB_PLUGIN_URL."dist/admin/js/jquery.datetimepicker.min.js", [], GSB_PLUGIN_VERSION, true);
            wp_enqueue_style('gsb-date-time-picker-css', GSB_PLUGIN_URL."dist/admin/css/jquery.datetimepicker.min.css", [], GSB_PLUGIN_VERSION);
            wp_enqueue_style('gsb-admin-style', GSB_PLUGIN_URL."dist/admin/css/style.css", [], GSB_PLUGIN_VERSION);
            wp_enqueue_style('gsb-admin-custom-style', GSB_PLUGIN_URL."dist/admin/css/custom.css", [], GSB_PLUGIN_VERSION);
            wp_enqueue_style('gsb-admin-custom-style-leads', GSB_PLUGIN_URL."dist/admin/css/leads-css.css", [], GSB_PLUGIN_VERSION);
            wp_enqueue_script('gsb-admin-script', GSB_PLUGIN_URL."dist/admin/js/leads-js.js", ['jquery'], GSB_PLUGIN_VERSION, true);

            // Check and enqueue signup styles if needed.
            $flag = get_option($this->slug."-subscribe-hide");
            if ($flag == false) {
                wp_enqueue_style($this->slug.'-signup-style', GSB_PLUGIN_URL."dist/admin/css/sign-up.css", [], GSB_PLUGIN_VERSION);
            }

            // Enqueue the right-to-left (RTL) stylesheet for the GSB plugin's admin section.
            wp_enqueue_style('gsb-rtl-style', GSB_PLUGIN_URL."dist/admin/css/style-rtl.css", [], GSB_PLUGIN_VERSION);

            // Localize script with leads data.
            wp_localize_script(
                'gsb-admin-script',
                'LEADS_DATA',
                [
                    'AJAX_URL'   => admin_url("admin-ajax.php"),
                    'leads_page' => admin_url("admin.php?page=sticky-chat-widget-leads"),
                ]
            );
        } else if ($hook == "sticky-chat-widget_page_sticky-chat-widget-integration") {
            // Check and enqueue signup styles if needed.
            $flag = get_option($this->slug."-subscribe-hide");
            if ($flag == false) {
                wp_enqueue_style($this->slug.'-signup-style', GSB_PLUGIN_URL."dist/admin/css/sign-up.css", [], GSB_PLUGIN_VERSION);
            }

            // Enqueue the right-to-left (RTL) stylesheet for the GSB plugin's admin section.
            wp_enqueue_style('gsb-rtl-style', GSB_PLUGIN_URL."dist/admin/css/style-rtl.css", [], GSB_PLUGIN_VERSION);
        } else {
            // If the page is not recognized, return.
            return;
        }//end if

    }//end admin_script()


    /**
     * Get the upgrade URL for the Pro version.
     *
     * @since  1.1.2
     * @return string The URL of the upgrade page.
     */
    public static function upgrade_url()
    {
        // Construct and return the URL for upgrading to the Pro version.
        return admin_url("admin.php?page=sticky-chat-widget-upgrade-to-pro");

    }//end upgrade_url()


    /**
     * Add menu and submenu pages to the admin dashboard for the Sticky Chat Widget plugin.
     *
     * @since  1.1.2
     * @return null
     */
    public function admin_menu()
    {
        // Add the main menu page.
        add_menu_page(
            __('Sticky Chat Widget', 'sticky-chat-widget'),
            // Page title.
            __('Sticky Chat Widget', 'sticky-chat-widget'),
            // Menu title.
            'manage_options',
            // Capability required.
            'sticky-chat-widget',
            // Menu slug.
            [
                $this,
                'admin_setting_page',
            ],
            // Callback function for the main menu page.
            esc_url(GSB_PLUGIN_URL."dist/admin/images/scw-icon.png")
            // Icon URL.
        );

        // Add Dashboard submenu page.
        add_submenu_page(
            'sticky-chat-widget',
            // Parent menu slug.
            esc_attr__('Dashboard', 'sticky-chat-widget'),
            // Page title.
            esc_attr__('Dashboard', 'sticky-chat-widget'),
            // Menu title.
            'manage_options',
            // Capability required.
            'sticky-chat-widget',
            // Menu slug (same as the parent for the main dashboard).
            [
                $this,
                'admin_setting_page',
            ]
            // Callback function for the Dashboard submenu page.
        );

        // Add Analytics submenu page.
        add_submenu_page(
            'sticky-chat-widget',
            // Parent menu slug.
            esc_attr__('Analytics', 'sticky-chat-widget'),
            // Page title.
            esc_attr__('Analytics', 'sticky-chat-widget'),
            // Menu title.
            'manage_options',
            // Capability required.
            'sticky-chat-widget-analytics',
            // Menu slug (same as the parent for the main dashboard).
            [
                $this,
                'admin_analytics_page',
            ]
            // Callback function for the Analytics submenu page.
        );

        // Add Form Leads submenu page.
        add_submenu_page(
            'sticky-chat-widget',
            // Parent menu slug.
            esc_attr__('Form Leads', 'sticky-chat-widget'),
            // Page title.
            esc_attr__('Form Leads', 'sticky-chat-widget'),
            // Menu title.
            'manage_options',
            // Capability required.
            'sticky-chat-widget-leads',
            // Menu slug.
            [
                $this,
                'admin_leads_page',
            ]
            // Callback function for the Form Leads submenu page.
        );

        // Add Integrations submenu page.
        add_submenu_page(
            'sticky-chat-widget',
            // Parent menu slug.
            esc_attr__('Integrations', 'sticky-chat-widget'),
            // Page title.
            esc_attr__('Integrations', 'sticky-chat-widget'),
            // Menu title.
            'manage_options',
            // Capability required.
            'sticky-chat-widget-integration',
            // Menu slug.
            [
                $this,
                'admin_integration_page',
            ]
            // Callback function for the Integrations submenu page.
        );

    }//end admin_menu()


    /**
     * Render the analytics page for the admin section.
     *
     * This method includes the widget-analytics.php template file to display the analytics page.
     */
    public function admin_analytics_page()
    {
        include_once dirname(__FILE__)."/templates/widget-analytics.php";

    }//end admin_analytics_page()


    /**
     * Include the Leads table page in the admin dashboard for the Sticky Chat Widget plugin.
     *
     * @since  1.1.2
     * @return null
     */
    public function admin_leads_page()
    {
        $flag = get_option($this->slug."-subscribe-hide");

        // Check if subscription is hidden.
        if ($flag == "yes") {
            // Include the Leads table page template.
            include_once dirname(__FILE__)."/templates/scw-leads.php";
        } else {
            // Include subscribe template if subscription is not hidden.
            include_once dirname(__FILE__)."/templates/subscribe.php";
        }

    }//end admin_leads_page()


    /**
     * Include the Mailchimp and Mailpoet integration page in the admin dashboard for the Sticky Chat Widget plugin.
     *
     * @since  1.1.2
     * @return null
     */
    public function admin_integration_page()
    {
        $flag = get_option($this->slug."-subscribe-hide");

        // Check if subscription is hidden.
        if ($flag == "yes") {
            // Include the Mailchimp and Mailpoet integration page template.
            include_once dirname(__FILE__)."/templates/admin-mail-integration.php";
        } else {
            // Include subscribe template if subscription is not hidden.
            include_once dirname(__FILE__)."/templates/subscribe.php";
        }

    }//end admin_integration_page()


    /**
     * Include the appropriate page (setting, help, lists, or help) based on the admin URL for the Sticky Chat Widget plugin.
     *
     * @since  1.1.2
     * @return null
     */
    public function admin_setting_page()
    {
        // Get social icon list.
        $socialIcons = Ginger_Social_Icons::icon_list();

        // Get social color list.
        $socialColors = Ginger_Social_Icons::color_list();

        // Get chat icon list.
        $closeIcons = Ginger_Social_Icons::get_chat_icons();

        // Upload icon.
        $uploadIcon = Ginger_Social_Icons::get_upload_icon();

        // Animation list.
        $animations = Ginger_Social_Icons::animation_styles();

        // Menu animation list.
        $menuAnimations = Ginger_Social_Icons::menu_animations();

        // Font list.
        $systemFonts = Ginger_Social_Icons::get_system_fonts_list();
        $googleFonts = Ginger_Social_Icons::get_google_fonts_list();

        // SVG icon.
        $formIcons = Ginger_Social_Icons::svg_icons();

        // Disabled section.
        $disabled = "disabled";

        // Upgrade Status.
        $upgrade = "has-upgrade-link";

        $postId          = 0;
        $postTitle       = "";
        $isSettingExists = 0;

        $task  = filter_input(INPUT_GET, 'task');
        $edit  = filter_input(INPUT_GET, 'edit');
        $nonce = filter_input(INPUT_GET, 'nonce');

        $flag = get_option($this->slug."-subscribe-hide");

        // Check if subscription is hidden.
        if ($flag == "yes") {
            if (isset($task) && $task == "edit-widget" && isset($edit) && isset($nonce)) {
                $postId = isset($edit) ? sanitize_text_field($edit) : 0;
                $nonce  = isset($nonce) ? sanitize_text_field($nonce) : "";

                // Verify nonce for security.
                if (wp_verify_nonce($nonce, "edit_widget_".$postId)) {
                    $postData = get_post($postId);
                    if (!empty($postData) && isset($postData->post_type) && $postData->post_type == "gsb_buttons") {
                        $postTitle = $postData->post_title;
                    }

                    // Include settings and help templates for editing widget.
                    include_once dirname(__FILE__)."/templates/admin-settings.php";
                    include_once dirname(__FILE__)."/templates/admin-help.php";
                }
            } else {
                // Fetch posts to check if settings exist.
                $posts = get_posts(
                    [
                        "post_type"   => "gsb_buttons",
                        'post_status' => 'publish',
                        'numberposts' => 1,
                    ]
                );

                // Check if settings exist.
                if ($this->isSettingExists()) {
                    $isSettingExists = 1;
                }

                // Include lists template.
                include_once dirname(__FILE__)."/templates/admin-lists.php";
            }//end if
        } else {
            // Include subscribe template if subscription is not hidden.
            include_once dirname(__FILE__)."/templates/subscribe.php";
        }//end if

    }//end admin_setting_page()


    /**
     * Check if settings for the Sticky Chat Widget exist and return the ID of the widget if found.
     *
     * @since  1.1.2
     * @return int|false The ID of the widget if it exists, or false if not found.
     */
    public function isSettingExists()
    {
        // Retrieve posts of type 'gsb_buttons'.
        $posts = get_posts(
            [
                "post_type"    => "gsb_buttons",
                "num_of_posts" => 1,
            ]
        );

        $postID = false;

        // Check if any posts are found.
        if (!empty($posts)) {
            // Set the postID to the ID of the first post (assuming only one post is expected).
            $postID = isset($posts[0]->ID) ? $posts[0]->ID : false;
        }

        return $postID;

    }//end isSettingExists()


    /**
     * Save widget settings into the database.
     *
     * @since  1.1.2
     * @return null
     */
    public function save_gsb_buttons_setting()
    {
        // Retrieve and sanitize the nonce value.
        $nonce = filter_input(INPUT_POST, "nonce");
        if (isset($nonce)) {
            $nonce = sanitize_text_field($nonce);
        }

        // Retrieve and sanitize the setting ID.
        $postId = filter_input(INPUT_POST, 'setting_id');
        if (isset($postId)) {
            $postId = sanitize_text_field($postId);
        }

        // Retrieve the save button type.
        $saveType = filter_input(INPUT_POST, 'save_btn_type');

        // Retrieve and sanitize the widget status.
        $widgetStatus = filter_input(INPUT_POST, 'widget_status', FILTER_SANITIZE_STRING);

        // Initialize the response array.
        $response = [
            'status'  => 0,
            'message' => esc_html__("Invalid Request, Please try again", "sticky-chat-widget"),
            'data'    => ["URL" => ''],
        ];

        // Verify the nonce.
        if (!empty($nonce) && wp_verify_nonce($nonce, "save_gsb_buttons_setting".$postId)) {
            // Retrieve and sanitize various settings.
            $channelsSetting     = filter_input(INPUT_POST, 'channel_settings', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            $selectedChannel     = filter_input(INPUT_POST, 'gsb_selected_channels');
            $widgetsSetting      = filter_input(INPUT_POST, 'widget_settings', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            $triggerRulesSetting = filter_input(INPUT_POST, "trigger_rules", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            $pageRulesSetting    = filter_input(INPUT_POST, "page_rules", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            $displayRulesSetting = filter_input(INPUT_POST, "time_rules", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            $tooltipSetting      = filter_input(INPUT_POST, "tooltip_settings", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            $contactFormSetting  = filter_input(INPUT_POST, "contact_form_settings", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            $gglAnalyticsSetting = filter_input(INPUT_POST, "gsb_google_analytics");
            $buttonCssSetting    = filter_input(INPUT_POST, "gsb_button_css");

            // Cast channel settings and widget settings to arrays if they are not set.
            $channelsSetting     = isset($channelsSetting) ? (array) $channelsSetting : [];
            $selectedChannel     = isset($selectedChannel) ? sanitize_text_field($selectedChannel) : "";
            $widgetsSetting      = isset($widgetsSetting) ? (array) $widgetsSetting : [];
            $triggerRulesSetting = isset($triggerRulesSetting) ? (array) $triggerRulesSetting : [];
            $pageRulesSetting    = isset($pageRulesSetting) ? (array) $pageRulesSetting : [];
            $displayRulesSetting = isset($displayRulesSetting) ? (array) $displayRulesSetting : [];
            $tooltipSetting      = isset($tooltipSetting) ? (array) $tooltipSetting : [];
            $gglAnalyticsSetting = isset($gglAnalyticsSetting) ? sanitize_text_field($gglAnalyticsSetting) : "no";
            $widgetsStatus       = isset($widgetStatus) ? sanitize_text_field($widgetStatus) : "no";
            $buttonCssSetting    = isset($buttonCssSetting) ? sanitize_text_field($buttonCssSetting) : "";
            $contactFormSetting  = isset($contactFormSetting) ? (array) $contactFormSetting : "";

            // Prepare post data for updating.
            $postData = [
                'ID'          => $postId,
                'post_status' => 'publish',
                'post_type'   => 'gsb_buttons',
            ];

            // Update the post.
            wp_update_post($postData);

            if (!empty($postId)) {
                // Check if the contact form has at least one visible field.
                $isContactForm = true;
                if (!empty($contactFormSetting)) {
                    $activeCount = 0;
                    foreach ($contactFormSetting['fields'] as $field) {
                        $activeCount = ($field['is_visible'] == 1) ? ($activeCount + 1) : $activeCount;
                    }

                    $isContactForm = ($activeCount == 0) ? false : true;
                }

                if ($isContactForm) {
                    // Update post meta with the new settings.
                    update_post_meta($postId, "channel_settings", $channelsSetting);
                    update_post_meta($postId, "selected_channels", $selectedChannel);
                    update_post_meta($postId, "widget_settings", $widgetsSetting);
                    update_post_meta($postId, "trigger_rules", $triggerRulesSetting);
                    update_post_meta($postId, "page_rules", $pageRulesSetting);
                    update_post_meta($postId, "display_rules", $displayRulesSetting);
                    update_post_meta($postId, "tooltip_settings", $tooltipSetting);
                    update_post_meta($postId, "google_analytics", $gglAnalyticsSetting);
                    update_post_meta($postId, "widget_status", $widgetsStatus);
                    update_post_meta($postId, "button_css", $buttonCssSetting);
                    update_post_meta($postId, "contact_form_settings", $contactFormSetting);

                    // Set response status and message.
                    $response['status']  = 1;
                    $response['message'] = esc_html__("Widget is updated successfully", "sticky-chat-widget");

                    // Set the URL in the response data based on the save button type.
                    if ($saveType == "save-view-btn") {
                        $response['data']['URL'] = admin_url("admin.php?page=sticky-chat-widget");
                    } else {
                        $response['data']['URL'] = "";
                    }
                } else {
                    // If no visible fields in the contact form, set error in the response.
                    $response['status']  = 0;
                    $response['message'] = esc_html__("Please select at least one field for the contact form", "sticky-chat-widget");
                }//end if
            }//end if

            // Clear cookies related to the widget.
            setcookie("scw-button", -1, (time() - 3600), "/");
            setcookie("scw-status", -1, (time() - 3600), "/");
            setcookie("gsb-button-view-".$postId, -1, time(), "/");
            setcookie("gsb-button-click-".$postId, -1, time(), "/");
            setcookie("gsb-greeting-".$postId, -1, time(), "/");

            // Trigger action to clear the cache for the plugin.
            do_action("clear_cache_for_scw_plugin");
        }//end if

        // Encode the response as JSON and exit.
        echo wp_json_encode($response);
        exit;

    }//end save_gsb_buttons_setting()


    /**
     * Change widget status and update the value in the database.
     *
     * @since  1.1.2
     * @return null
     */
    public function gsb_buttons_change_status()
    {
        // Retrieve and sanitize the nonce value.
        $nonce = filter_input(INPUT_POST, 'nonce');
        if (isset($nonce)) {
            $nonce = sanitize_text_field($_POST['nonce']);
        }

        // Retrieve and sanitize the setting ID.
        $postId = filter_input(INPUT_POST, 'setting_id');
        if (isset($postId)) {
            $postId = sanitize_text_field($postId);
        }

        // Retrieve and sanitize the new status value.
        $status = filter_input(INPUT_POST, 'status');
        if (isset($status)) {
            $status = sanitize_text_field($status);
        }

        // Initialize the response array.
        $response = [
            'status'  => 0,
            'message' => '',
            'data'    => [],
        ];

        // Verify the nonce.
        if (!empty($nonce) && wp_verify_nonce($nonce, "gsb_buttons_action_".$postId)) {
            // Update the post meta with the new widget status.
            update_post_meta($postId, "widget_status", $status);
            // Set the response status to success.
            $response['status'] = 1;
        }

        // Encode the response as JSON and exit.
        echo wp_json_encode($response);
        exit;

    }//end gsb_buttons_change_status()


    /**
     * Remove the widget from the database.
     *
     * @since  1.1.2
     * @return null
     */
    public function gsb_buttons_remove_widget()
    {
        // Retrieve and sanitize the nonce value.
        $nonce = filter_input(INPUT_POST, 'nonce');
        if (isset($nonce)) {
            $nonce = sanitize_text_field($nonce);
        }

        // Retrieve and sanitize the widget ID.
        $postId = filter_input(INPUT_POST, 'widget_id');
        if (isset($postId)) {
            $postId = sanitize_text_field($postId);
        }

        // Initialize the response array with a default success message.
        $response = [
            'status'  => 0,
            'message' => esc_html__("Widget is removed successfully", "sticky-chat-widget"),
            'data'    => [],
        ];

        // Verify the nonce.
        if (!empty($nonce) && wp_verify_nonce($nonce, "gsb_buttons_action_".$postId)) {
            // Sanitize and escape the widget ID.
            $postId = esc_sql($postId);
            // Delete the post with the specified ID.
            wp_delete_post($postId);
            // Set the response status to success.
            $response['status'] = 1;
        }

        // Encode the response as JSON and exit.
        echo wp_json_encode($response);
        exit;

    }//end gsb_buttons_remove_widget()


    /**
     * Save subscribe data.
     *
     * @since  1.1.2
     * @return null
     */
    public function scw_save_sign_up_info()
    {
        // Retrieve and sanitize the 'skip' parameter.
        $skip = filter_input(INPUT_POST, "skip");
        if (isset($skip)) {
            $skip = sanitize_text_field($skip);
        }

        // Retrieve and sanitize the 'email_id' parameter.
        $emailId = filter_input(INPUT_POST, "email_id");
        if (isset($emailId)) {
            $emailId = sanitize_text_field($emailId);
        }

        // Retrieve and sanitize the 'is_signup' parameter.
        $isSignUp = filter_input(INPUT_POST, "is_signup");
        if (isset($emailId)) {
            $isSignUp = sanitize_text_field($isSignUp);
        }

        // Retrieve and sanitize the nonce value.
        $nonce = filter_input(INPUT_POST, "nonce");
        if (isset($nonce)) {
            $nonce = sanitize_text_field($nonce);
        }

        // Initialize the response array with a default invalid request message.
        $response = [
            'status'  => 0,
            'message' => esc_html__("Invalid Request, Please try again", "gp-sticky-buttons"),
            'data'    => ["URL" => admin_url("admin.php?page=sticky-chat-widget")],
        ];

        // Verify the nonce.
        if (!empty($nonce) && wp_verify_nonce($nonce, "scw_save_sign_up_info_nonce")) {
            // Check if 'skip' is not empty.
            if (!empty($skip)) {
                // Add an option to hide subscribe if 'skip' is not empty.
                add_option($this->slug."-subscribe-hide", "yes");
                // Set the response status to success.
                $response['status'] = 1;
            } else {
                // Construct the API URL.
                $url  = "https://api.gingerplugins.com/email/signup.php";
                $args = [];
                // Set the request body parameters.
                $args['body'] = [
                    'email_id'  => $emailId,
                    'wp_plugin' => "scw",
                ];
                // Append parameters to the URL.
                $url .= "?email_id=".$emailId."&wp_plugin=scw";
                // Make a remote POST request.
                wp_remote_post($url, $args);
                // Add an option to hide subscribe.
                add_option($this->slug."-subscribe-hide", "yes");
            }//end if
        }//end if

        // Encode the response as JSON and exit.
        echo wp_json_encode($response);
        exit;

    }//end scw_save_sign_up_info()


    /**
     * Create a widget for sticky chat.
     *
     * This function handles the creation of a widget for the sticky chat. It receives data via a POST request,
     * including the widget title and a nonce for verification. It verifies the nonce, inserts a new post of type
     * 'gsb_buttons' with the provided title, and returns a JSON response indicating the status of the operation.
     *
     * @return void
     * @since  1.0.0
     */
    public function gsb_buttons_create_widget()
    {
        // Initialize the response array with default values.
        $response = [
            'status'  => 0,
            'message' => esc_html__('Invalid request, Please try again', "sticky-chat-widget"),
            'data'    => ['URL' => ''],
        ];

        // Retrieve and sanitize the 'widget_title' parameter.
        $widgetTitle = filter_input(INPUT_POST, 'widget_title', FILTER_SANITIZE_STRING);

        // Retrieve and sanitize the nonce value.
        $nonce = filter_input(INPUT_POST, 'nonce', FILTER_SANITIZE_STRING);

        // Verify the nonce.
        if (!empty($nonce) && wp_verify_nonce($nonce, "gsb_buttons_create_widget")) {
            // Prepare arguments for inserting a new post.
            $args = [
                'post_title'  => $widgetTitle,
                'post_type'   => 'gsb_buttons',
                'post_status' => 'publish',
            ];

            // Insert a new post.
            $postId = wp_insert_post($args);

            // Update the response array with success details.
            $response['status']      = 1;
            $response['message']     = "Widget is created successfully";
            $response['data']['URL'] = admin_url('admin.php?page=sticky-chat-widget&task=edit-widget&edit='.$postId.'&nonce='.wp_create_nonce('edit_widget_'.$postId));
        }

        // Encode the response as JSON and exit.
        echo wp_json_encode($response);
        exit;

    }//end gsb_buttons_create_widget()


    /**
     * Rename the widget title and return response in JSON format.
     *
     * This function handles the renaming of a widget title for the sticky chat. It receives data via a POST request,
     * including the new widget title, nonce for verification, and the widget ID. It verifies the nonce, updates the
     * post title for the specified widget ID, and returns a JSON response indicating the status of the operation.
     *
     * @return void
     * @since  1.0.0
     */
    public function gsb_buttons_rename_widget()
    {
        // Initialize the response array with default values.
        $response = [
            'status'  => 0,
            'message' => esc_html__('Invalid request, Please try again', "sticky-chat-widget"),
            'data'    => ['URL' => ''],
        ];

        // Retrieve and sanitize the 'widget_title' parameter.
        $widgetTitle = filter_input(INPUT_POST, 'widget_title', FILTER_SANITIZE_STRING);

        // Retrieve and sanitize the nonce value.
        $nonce = filter_input(INPUT_POST, 'nonce', FILTER_SANITIZE_STRING);

        // Retrieve and sanitize the 'widget_id' parameter.
        $postId = filter_input(INPUT_POST, 'widget_id', FILTER_SANITIZE_STRING);

        // Verify the nonce and check if 'widget_id' is not empty.
        if (!empty($nonce) && wp_verify_nonce($nonce, "gsb_buttons_action_".$postId)) {
            if (!empty($postId)) {
                // Prepare arguments for updating the post title.
                $arg = [
                    'ID'          => $postId,
                    'post_title'  => $widgetTitle,
                    'post_type'   => 'gsb_buttons',
                    'post_status' => 'publish',
                ];

                // Update the post title.
                wp_update_post($arg);

                // Update the response array with success details.
                $response['status']  = 1;
                $response['message'] = "Widget title is updated successfully";
            }
        }

        // Encode the response as JSON and exit.
        echo wp_json_encode($response);
        exit;

    }//end gsb_buttons_rename_widget()


    /**
     * Download CSV file.
     *
     * This function handles the download of a CSV file containing contact form leads data. It verifies the nonce
     * received via POST request, retrieves the necessary data from the database based on specified criteria such as
     * start and end dates and search term, and generates a CSV file for download. The generated file includes a header
     * row and rows of contact form data. The CSV file is then sent to the browser for download.
     *
     * @since 1.0.0
     */
    function download_csv()
    {
        // Retrieve and sanitize the nonce value.
        $nonce = filter_input(INPUT_POST, 'nonce', FILTER_SANITIZE_STRING);

        // Check if the nonce is valid.
        if (!empty($nonce) && wp_verify_nonce($nonce, "export_data_nonce")) {
            global $wpdb;

            // Initialize variables for filename and data rows.
            $filename  = 'export_form'.time().'.csv';
            $data_rows = [];

            // Set the table name for contact form leads.
            $tableName = $wpdb->prefix.'scw_contact_form_leads';

            // Retrieve start date, end date, and search term from the POST request.
            $startDate = filter_input(INPUT_POST, "start_date");
            $endDate   = filter_input(INPUT_POST, "end_date");
            $search    = filter_input(INPUT_POST, "search");

            // Sanitize and format dates.
            $startDateSet = (!empty($startDate)) ? gmdate("Y-m-d H:i:s", strtotime(sanitize_text_field($startDate))) : "";
            $endDateSet   = (!empty($endDate)) ? gmdate("Y-m-d H:i:s", strtotime(sanitize_text_field($endDate." 23:59:59"))) : "";

            // Sanitize search term.
            $search = (!empty($search)) ? sanitize_text_field($search) : "";

            // Build the SQL query based on specified criteria.
            $query   = "SELECT * FROM $tableName ";
            $prepare = [];

            if ($startDateSet != "" && $endDateSet != "") {
                $query    .= "WHERE ( created_on >= '%s' AND created_on <= '%s' )";
                $prepare[] = esc_sql($startDateSet);
                $prepare[] = esc_sql($endDateSet);
            } else if ($startDateSet != "") {
                $query    .= "WHERE ( created_on >= '%s' )";
                $prepare[] = esc_sql($startDateSet);
            } else if ($endDateSet != "") {
                $query    .= "WHERE ( created_on <= '%s' )";
                $prepare[] = esc_sql($endDateSet);
            }

            if ($search != "") {
                $searchable = '%'.$search.'%';
                if ($startDateSet != "" || $endDateSet != "") {
                    $query .= " AND ";
                } else {
                    $query .= " WHERE ";
                }

                $query    .= "(name LIKE %s OR email LIKE %s OR phone LIKE %s OR message LIKE %s)";
                $prepare[] = esc_sql($searchable);
                $prepare[] = esc_sql($searchable);
                $prepare[] = esc_sql($searchable);
                $prepare[] = esc_sql($searchable);
            }

            if (!empty($prepare)) {
                $query = $wpdb->prepare($query, $prepare);
            }

            // Retrieve results from the database.
            $results = $wpdb->get_results($query);

            // Define the header row for the CSV file.
            $header_row = [
                'Name',
                'Email',
                'Phone',
                'Message',
                'Page Url',
                'Created On',
            ];

            // Format data rows for the CSV file.
            foreach ($results as $result) {
                $row         = [
                    'name'       => $result->name,
                    'email'      => $result->email,
                    'phone'      => $result->phone,
                    'message'    => $result->message,
                    'page_url'   => $result->page_url,
                    'created_on' => $result->created_on,
                ];
                $data_rows[] = $row;
            }

            // Clean the output buffer and set up the CSV file download.
            ob_end_clean();
            $fh = @fopen('php://output', 'w');
            header("Content-Disposition: attachment; filename={$filename}");

            // Write header row and data rows to the CSV file.
            fputcsv($fh, $header_row);
            foreach ($data_rows as $keys => $value) {
                fputcsv($fh, $value);
            }

            // Exit to ensure no further output is sent.
            exit();
        }//end if

    }//end download_csv()


    /**
     * Remove leads.
     *
     * This function handles the removal of leads from the database based on the provided lead IDs. It verifies the
     * nonce received via POST request, processes the lead IDs, and executes a database query to remove the corresponding
     * leads. The response, including the status and a message, is then sent in JSON format.
     *
     * @since 1.0.0
     */
    function remove_leads()
    {
        global $wpdb;

        // Initialize response data.
        $response = [
            'status'  => 0,
            'message' => esc_html__('Invalid request, Please try again', "sticky-chat-widget"),
            'data'    => ['URL' => ''],
        ];

        // Retrieve and sanitize the nonce value.
        $nonce = filter_input(INPUT_POST, 'nonce', FILTER_SANITIZE_STRING);

        // Check if the nonce is valid.
        if (!empty($nonce) && wp_verify_nonce($nonce, "remove_leads_nonce")) {
            // Retrieve lead IDs from the POST request.
            $ids = filter_input(INPUT_POST, 'ids', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

            // Set the table name for contact form leads.
            $tableName = $wpdb->prefix.'scw_contact_form_leads';

            // Loop through each lead ID and delete corresponding records.
            foreach ($ids as $id) {
                $query = $wpdb->delete(
                    $tableName,
                    ['id' => esc_sql($id)],
                    ['%d']
                );
            }

            // Check if leads were removed successfully.
            if ($query) {
                $response['status']  = 1;
                $response['message'] = "Leads removed successfully";
            }
        }//end if

        // Send the response data in JSON format.
        echo wp_json_encode($response);
        exit;

    }//end remove_leads()


    /**
     * Remove all leads.
     *
     * This function handles the removal of all leads from the database. It verifies the nonce received via POST request,
     * truncates the table storing contact form leads, and sends a JSON response indicating the status of the operation.
     *
     * @since 1.0.0
     */
    function remove_all_leads()
    {
        global $wpdb;

        // Initialize response data.
        $response = [
            'status'  => 0,
            'message' => esc_html__('Invalid request, Please try again', "sticky-chat-widget"),
            'data'    => ['URL' => ''],
        ];

        // Retrieve and sanitize the nonce value.
        $nonce = filter_input(INPUT_POST, 'nonce', FILTER_SANITIZE_STRING);

        // Check if the nonce is valid.
        if (!empty($nonce) && wp_verify_nonce($nonce, "remove_all_leads_nonce")) {
            // Set the table name for contact form leads.
            $tableName = $wpdb->prefix.'scw_contact_form_leads';

            // Execute a query to truncate the leads table.
            $query = $wpdb->query('TRUNCATE TABLE '.$tableName);

            // Check if all leads were removed successfully.
            if ($query) {
                $response['status']  = 1;
                $response['message'] = "All leads removed successfully";
            }
        }

        // Send the response data in JSON format.
        echo wp_json_encode($response);
        exit;

    }//end remove_all_leads()


    /**
     * Remove single lead.
     *
     * This function handles the removal of a single lead from the database based on the provided lead ID. It verifies
     * the nonce received via POST request, deletes the specified lead from the leads table, and sends a JSON response
     * indicating the status of the removal operation.
     *
     * @since 1.0.0
     */
    function remove_single_lead()
    {
        global $wpdb;

        // Initialize response data.
        $response = [
            'status'  => 0,
            'message' => esc_html__('Invalid request, Please try again', "sticky-chat-widget"),
            'data'    => ['URL' => ''],
        ];

        // Retrieve and sanitize the nonce value and lead ID.
        $nonce = filter_input(INPUT_POST, 'nonce', FILTER_SANITIZE_STRING);
        $id    = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);

        // Check if the nonce is valid.
        if (!empty($nonce) && wp_verify_nonce($nonce, "remove_single_lead_nonce".$id)) {
            // Set the table name for contact form leads.
            $tableName = $wpdb->prefix.'scw_contact_form_leads';

            // Execute a query to delete the specified lead from the leads table.
            $query = $wpdb->delete(
                $tableName,
                ['id' => esc_sql($id)],
                ['%d']
            );

            // Check if the lead was removed successfully.
            if ($query) {
                $response['status']  = 1;
                $response['message'] = "Lead removed successfully";
            }
        }

        // Send the response data in JSON format.
        echo wp_json_encode($response);
        exit;

    }//end remove_single_lead()


}//end class


if (class_exists("GP_Admin_Sticky_Chat_Buttons")) {
    $GP_admin_sticky_chat_buttons = new GP_Admin_Sticky_Chat_Buttons();
}
