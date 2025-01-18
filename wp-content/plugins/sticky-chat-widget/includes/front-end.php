<?php
/**
 * The admin specific functionality of the plugin.
 *
 * @author  : Ginger Plugins <gingerplugins@gmail.com>
 * @license : GPL2
 * */
defined('ABSPATH') or die('Direct Access is not allowed');

class GP_Front_Sticky_Chat_Buttons
{

    /**
     * Check widget is active or not.
     *
     * @var    boolean    $slug    The slug of this plugin.
     * @since  1.1.2
     * @access private
     */
    private $isWidgetActive = null;

    /**
     * The settings of widget.
     *
     * @var    array    $slug    The settings of widget.
     * @since  1.1.2
     * @access private
     */
    private $settings = [];

    /**
     * The name of the plugin.
     *
     * @var    string $pluginName The name of the plugin.
     * @since  1.0.0
     * @access private
     */
    public $pluginName = "Sticky Chat Widget";


    /**
     * Initialize the Ginger Sticky Chat Widget class and set its properties.
     *
     * This constructor function is responsible for initializing the Ginger Sticky Chat Widget class
     * and setting its properties. It hooks into WordPress actions to enqueue CSS and JavaScript files
     * for the front end of the website. Additionally, it registers AJAX actions to handle form data
     * saving both for logged-in users and non-logged-in users.
     *
     * @since 1.1.2
     */
    public function __construct()
    {
        // Enqueue CSS and JavaScript files for the front end of the website.
        add_action('wp_enqueue_scripts', [ $this, 'front_end_script' ]);

        // Register AJAX action to save form data for logged-in users.
        add_action('wp_ajax_scw_save_form_data', [$this, 'save_form_data']);

        // Register AJAX action to save form data for non-logged-in users.
        add_action('wp_ajax_nopriv_scw_save_form_data', [$this, 'save_form_data']);

    }//end __construct()


    /**
     * Enqueue front-end script and CSS file.
     *
     * This function is responsible for enqueuing the necessary front-end script and CSS file for the Ginger Sticky Chat Widget.
     * It checks if the widget is active and, if so, enqueues the corresponding assets. Additionally, it localizes script data
     * with information about buttons, AJAX URL, and form data nonce for dynamic functionality on the front end.
     *
     * @since  1.1.2
     * @return null
     */
    public function front_end_script()
    {
        // Check if the widget buttons are active.
        $this->check_for_buttons();

        // Determine whether to use minified versions based on the development version.
        $minified = ".min";
        if (GSB_DEV_VERSION) {
            $minified = "";
        }

        // Enqueue script and style if the widget is active.
        if ($this->isWidgetActive) {
            // Enqueue JavaScript script.
            wp_enqueue_script('gsb-script', GSB_PLUGIN_URL."dist/front/js/script.js", ['jquery'], GSB_PLUGIN_VERSION, true);

            // Enqueue front-end CSS.
            wp_enqueue_style('gsb-front', GSB_PLUGIN_URL."dist/front/css/front.css", [], GSB_PLUGIN_VERSION);

            // Localize script data for dynamic functionality.
            $data = [
                'buttons'         => $this->settings,
                'ajax_url'        => admin_url("admin-ajax.php"),
                'form_data_nonce' => wp_create_nonce("form_data_nonce"),
            ];
            wp_localize_script('gsb-script', "gsb_settings", $data);
        }

    }//end front_end_script()


    /**
     * Save form data to the database.
     *
     * This function handles the submission of form data, validates it, and saves it to the database if the request is valid.
     * It checks the nonce for security, validates form field values, and inserts the data into the database table
     * 'scw_contact_form_leads'. If successful, it returns a status of 1 and a success message, otherwise, it returns a
     * status of 0 and an error message. Additionally, it can include a callback URL in the response data if provided.
     *
     * @since 1.0.1
     */
    public function save_form_data()
    {
        global $wpdb;

        // Default response values.
        $response = [
            'status'  => 0,
            'message' => esc_html__("Invalid Request, Please try again", "sticky-chat-widget"),
            'data'    => ["URL" => ""],
        ];

        // Retrieve and sanitize nonce and callback URL from the POST request.
        $nonce = filter_input(INPUT_POST, "nonce");
        if (isset($nonce)) {
            $nonce = sanitize_text_field($nonce);
        }

        $call_back_url = filter_input(INPUT_POST, "call_back_url");
        if (isset($call_back_url)) {
            $call_back_url = sanitize_text_field($call_back_url);
        }

        // Verify the nonce for security.
        if (!empty($nonce) && wp_verify_nonce($nonce, "form_data_nonce")) {
            // Retrieve form data from the POST request.
            $formData = filter_input(INPUT_POST, 'scw_form_fields', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            $consentCheckbox = sanitize_text_field(filter_input(INPUT_POST, 'scw_consent_checkbox'));

            // Retrieve the client's IP address.
            $ip = $this->get_client_ip();

            // Retrieve form settings for validation.
            $formSettings = get_post_meta($formData['widget_id'], "contact_form_settings");

            // Initialize validation variables.
            $isValid = 1;
            $response['has_error']       = [];
            $response['has_error_valid'] = [];

            // Validate form field values based on form settings.
            foreach ($formSettings as $formSetting) {
                foreach ($formSetting['fields'] as $key => $field) {
                    if($key != "consent_checkbox") {
                        if ($field['is_visible'] == 1 && $field['is_required'] == 1 && $formData[$key] == "") {
                            $isValid = 0;
                            $response['has_error'][] = $key;
                        } else if ($field['is_visible'] == 1 && $formData[$key] != "") {
                            if ($key == "email") {
                                if (!filter_var($formData[$key], FILTER_VALIDATE_EMAIL)) {
                                    $isValid = 0;
                                    $response['has_error_valid'][] = $key;
                                }
                            } else if ($key == "phone") {
                                if (!preg_match('/^[0-9]*$/', $formData[$key])) {
                                    $isValid = 0;
                                    $response['has_error_valid'][] = $key;
                                }
                            }
                        }
                    } else {
                        if(!isset($consentCheckbox)) {
                            $isValid = 0;
                            $response['has_error_valid'][] = $key;
                        }
                    }
                }
            }

            // If the data is valid, insert it into the database.
            if ($isValid == 1) {
                $formData['ip_address'] = $ip;
                $tableName = $wpdb->prefix.'scw_contact_form_leads';
                $result    = $wpdb->insert($tableName, $formData);

                // Update the response based on the database insertion result.
                if ($result) {
                    $isSentmail = get_option('scb-sent-leads-mail', false);
                    $rowCount   = $wpdb->get_var("SELECT COUNT(*) FROM `$tableName`");
                    if (!$isSentmail && $rowCount == 1) {
                        add_option('scb-sent-leads-mail', 1);
                        $email    = get_option('admin_email');
                        $headers  = "MIME-Version: 1.0\r\n";
                        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                        $headers .= 'From:'.$this->pluginName.PHP_EOL;
                        $headers .= 'X-Mailer: PHP/'.phpversion();

                        $subject = "First lead from for ".$this->pluginName.": ".site_url();

                        $emailMessage = "You have received first lead from ".$this->pluginName."";

                        // Sending an Email.
                        wp_mail($email, $subject, $emailMessage, $headers);
                    }

                    $response['status']  = 1;
                    $response['message'] = $formSettings[0]['success_msg'];
                    if (isset($call_back_url) && !empty($call_back_url)) {
                        $response['data']['URL'] = $call_back_url;
                    }
                }//end if
            } else {
                // If the data is not valid, update the response with an error message.
                $response['status']  = 0;
                $response['message'] = esc_html__("Invalid Request, Please try again", "sticky-chat-widget");
            }//end if
        }//end if

        // Return the JSON-encoded response and exit the script.
        echo wp_json_encode($response);
        exit;

    }//end save_form_data()


    /**
     * Helper function to retrieve the client's IP address.
     *
     * @return string The client's IP address.
     */
    private function get_client_ip()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            return getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR')) {
            return getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('HTTP_X_FORWARDED')) {
            return getenv('HTTP_X_FORWARDED');
        } else if (getenv('HTTP_FORWARDED_FOR')) {
            return getenv('HTTP_FORWARDED_FOR');
        } else if (getenv('HTTP_FORWARDED')) {
            return getenv('HTTP_FORWARDED');
        } else if (getenv('REMOTE_ADDR')) {
            return getenv('REMOTE_ADDR');
        } else {
            return 'UNKNOWN';
        }

    }//end get_client_ip()


    /**
     * Add all settings of the widget.
     *
     * This function retrieves active widget settings, including channels, triggers, widget settings, custom CSS, ID, token,
     * and client status. It checks for active buttons in the 'gsb_buttons' custom post type with the 'widget_status' meta
     * value set to 'yes'. For each active button, it retrieves associated channels, triggers, widget settings, and custom CSS.
     * The collected settings are stored in an array and the 'isWidgetActive' flag is updated accordingly. The function returns
     * whether the widget is active or not.
     *
     * @since  1.1.2
     * @return boolean The widget is active or not.
     */
    public function check_for_buttons()
    {
        global $wpdb;
        $prefix = $wpdb->prefix;

        // Retrieve active buttons from the database.
        $posts = $wpdb->get_results($wpdb->prepare("SELECT %1\$sposts.ID FROM %2\$sposts INNER JOIN %3\$spostmeta ON ( %4\$sposts.ID = %5\$spostmeta.post_id ) WHERE 1=1 AND ( ( %6\$spostmeta.meta_key = 'widget_status' AND %7\$spostmeta.meta_value = 'yes' ) ) AND %8\$sposts.post_type = 'gsb_buttons' AND ((%9\$sposts.post_status = 'publish')) GROUP BY %10\$sposts.ID", [$prefix, $prefix, $prefix, $prefix, $prefix, $prefix, $prefix, $prefix, $prefix, $prefix]));

        $settings = [];

        // Process each active button.
        if (!empty($posts) && count($posts) > 0) {
            foreach ($posts as $post) {
                $channels = $this->check_for_channels($post->ID);

                // Check for associated channels.
                if (!empty($channels)) {
                    $widgetSettings  = $this->get_setting($post->ID);
                    $triggerSettings = $this->get_trigger($post->ID);
                    $customCss       = $this->get_custom_css($post->ID);

                    // Prepare and store the button settings.
                    $setting    = [
                        'channels'   => $channels,
                        'triggers'   => $triggerSettings,
                        'settings'   => $widgetSettings,
                        'custom_css' => $customCss,
                        'id'         => $post->ID,
                        'token'      => wp_create_nonce("gsb_button_settings_".$post->ID),
                        'client'     => 1,
                    ];
                    $settings[] = $setting;
                }
            }//end foreach
        }//end if

        // Update class properties based on the collected settings.
        $this->settings       = $settings;
        $this->isWidgetActive = !empty($this->settings);

        return $this->isWidgetActive;

    }//end check_for_buttons()


    /**
     * Retrieve the customized settings of the widget.
     *
     * This function retrieves and customizes the settings of the widget specified by the given post ID. It uses
     * `Ginger_Social_Icons::get_customize_widget_setting()` to get the default widget settings and then fetches the saved
     * widget settings from the database. It applies default values where necessary and processes dynamic content, such as
     * replacing placeholders like {page_url}, {page_title}, {product-name}, {product-sku}, and {product-price}. The
     * function also retrieves and customizes tooltip settings, including border radius, font size, and tooltip height.
     *
     * @since  1.1.2
     * @param  integer $postId The ID of the widget.
     * @return array The customized settings of the widget.
     */
    public function get_setting($postId)
    {
        // Define allowed HTML tags for wp_kses.
        $allowedTags = [
            'a'       => [
                'href'   => [],
                'title'  => [],
                'target' => [],
            ],
            'abbr'    => ['title' => []],
            'acronym' => ['title' => []],
            'code'    => [],
            'pre'     => [],
            'em'      => [],
            'strong'  => [],
            'ul'      => [],
            'ol'      => [],
            'li'      => [],
            'span'    => ['style' => []],
            'p'       => [],
            'br'      => [],
            'img'     => [
                'src' => [],
                'alt' => [],
            ],
        ];

        // Get default widget settings.
        $defaultWidgetSettings = Ginger_Social_Icons::get_customize_widget_setting();

        // Get saved widget settings from the database.
        $widgetSettings = get_post_meta($postId, "widget_settings", true);
        $widgetSettings = shortcode_atts($defaultWidgetSettings, $widgetSettings);

        // Get current page URL and title.
        $pageURL = (empty($_SERVER['HTTPS']) ? 'http' : 'https')."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        global $wp_query;
        $pageTitle = isset($wp_query->post->post_title) ? $wp_query->post->post_title : "";

        // Get chat icons and set the icon URL.
        $icons = Ginger_Social_Icons::get_chat_icons();
        $widgetSettings['icon_url'] = isset($icons[$widgetSettings['chat_icon']]) ? "<div class='chat-button-icon'>".$icons[$widgetSettings['chat_icon']]['icon']."</div>" : "<div class='chat-button-icon'>".$icons['chat-line']['icon']."</div>";

        // Customize widget settings.
        $widgetSettings['list_view_title'] = esc_attr($widgetSettings['list_view_title']);
        $list_view_subtitle = wp_kses($widgetSettings['list_view_subtitle'], $allowedTags);
        $widgetSettings['list_view_subtitle'] = str_replace(['{page_url}', '{page_title}'], [$pageURL, $pageTitle], $list_view_subtitle);
        $widgetSettings['call_to_action']     = esc_attr($widgetSettings['call_to_action']);

        // Check if WooCommerce customization is enabled and the current page is a product page.
        if (function_exists('is_product') && isset($widgetSettings['woocommerce_customization']) && $widgetSettings['woocommerce_customization'] == "yes" && is_product()) {
            $product = wc_get_product(get_the_ID());
            $widgetSettings['list_view_title'] = esc_attr($widgetSettings['woo_list_view_title']);
            $list_view_subtitle = wp_kses($widgetSettings['woo_list_view_subtitle'], $allowedTags);
            $widgetSettings['list_view_subtitle'] = str_replace(['{page_url}', '{page_title}', '{product-name}', '{product-sku}', '{product-price}'], [$pageURL, $pageTitle, $product->get_name(), $product->get_sku(), get_woocommerce_currency_symbol().$product->get_price()], $list_view_subtitle);
        }

        // Get default tooltip settings.
        $defaultTooltipSettings = Ginger_Social_Icons::get_tooltip_setting();

        // Get saved tooltip settings from the database.
        $tooltipSettings = get_post_meta($postId, "tooltip_settings", true);
        $tooltipSettings = shortcode_atts($defaultTooltipSettings, $tooltipSettings);

        // Customize tooltip settings.
        $widgetSettings['tooltip_settings'] = [
            'border_radius'  => isset($tooltipSettings['border_radius']) && !empty($tooltipSettings['border_radius']) ? $tooltipSettings['border_radius'] : '5',
            'font_size'      => isset($tooltipSettings['font_size']) && !empty($tooltipSettings['font_size']) ? $tooltipSettings['font_size'] : '16',
            'tooltip_height' => isset($tooltipSettings['tooltip_height']) && !empty($tooltipSettings['tooltip_height']) ? $tooltipSettings['tooltip_height'] : '20',
            'bg_color'       => isset($tooltipSettings['bg_color']) && !empty($tooltipSettings['bg_color']) ? $tooltipSettings['bg_color'] : '#ffffff',
            'text_color'     => isset($tooltipSettings['text_color']) && !empty($tooltipSettings['text_color']) ? $tooltipSettings['text_color'] : '#000000',
        ];

        // Set border radius.
        $borderRadius = isset($widgetSettings['border_radius']) && !empty($widgetSettings['border_radius']) ? $widgetSettings['border_radius'] : '28';
        $widgetSettings['border_radius'] = $borderRadius;

        return $widgetSettings;

    }//end get_setting()


    /**
     * Retrieve the trigger settings of the widget.
     *
     * This method fetches and customizes the trigger settings of the widget specified by the given post ID. It uses default
     * trigger settings from the `Ginger_Social_Icons::get_trigger_rule_setting()` method and then retrieves the saved
     * trigger settings from the database. The function applies default values where necessary and ensures that certain
     * conditions are met for specific trigger options. For example, it checks if the 'seconds' value is numeric and greater
     * than or equal to 0, and if not, it sets 'after_seconds' to 'no'. It performs similar checks for 'page_scroll',
     * 'browser', 'on_inactivity', and 'exit_intent' options.
     *
     * @since  1.1.2
     * @param  integer $postId The ID of the widget.
     * @return array The customized trigger settings of the widget.
     */
    public function get_trigger($postId)
    {
        // Get saved trigger settings from the database.
        $triggerSettings = get_post_meta($postId, "trigger_rules", true);

        // Get default trigger settings.
        $defaultTriggerSettings = Ginger_Social_Icons::get_trigger_rule_setting();

        // Merge default and saved trigger settings.
        $triggerSettings = shortcode_atts($defaultTriggerSettings, $triggerSettings);

        // Check and customize trigger settings.
        if (empty($triggerSettings['seconds']) || !is_numeric($triggerSettings['seconds']) || $triggerSettings['seconds'] < 0) {
            $triggerSettings['after_seconds'] = 'no';
        }

        if (empty($triggerSettings['page_scroll']) || !is_numeric($triggerSettings['page_scroll']) || $triggerSettings['page_scroll'] < 0) {
            $triggerSettings['on_scroll'] = 'no';
        }

        if ($triggerSettings['browser'] == "no" && $triggerSettings['on_inactivity'] == "no") {
            $triggerSettings['exit_intent'] = "no";
        }

        return $triggerSettings;

    }//end get_trigger()


    /**
     * Retrieve the custom CSS of the widget.
     *
     * This method fetches and returns the custom CSS of the widget specified by the given post ID. It retrieves the
     * custom CSS from the database using the 'button_css' meta key. If custom CSS is set for the widget, it is returned;
     * otherwise, an empty string is returned.
     *
     * @since  1.1.2
     * @param  integer $postId The ID of the widget.
     * @return string The custom CSS of the widget.
     */
    public function get_custom_css($postId)
    {
        // Get custom CSS from the database.
        $customCss = get_post_meta($postId, "button_css", true);

        // Initialize CSS string.
        $css = "";

        // Append custom CSS to the string if available.
        if (isset($customCss) && !empty($customCss)) {
            $css .= $customCss;
        }

        return $css;

    }//end get_custom_css()


    /**
     * Retrieve the selected channel settings of the widget.
     *
     * This method fetches and returns the settings for all selected channels of the widget specified by the given post ID.
     * It checks the status of the widget using the 'widget_status' meta key. If the widget is active ('yes'), it proceeds to
     * retrieve and process the channel settings, applying necessary transformations and validations. The final settings are
     * returned as an array.
     *
     * @since  1.1.2
     * @param  integer $postId The ID of the widget.
     * @return array The selected channel settings of the widget.
     */
    public function check_for_channels($postId)
    {

        $allowedTags = [
            'a'       => [
                'href'   => [],
                'title'  => [],
                'target' => [],
            ],
            'abbr'    => [ 'title' => [] ],
            'acronym' => [ 'title' => [] ],
            'code'    => [],
            'pre'     => [],
            'em'      => [],
            'strong'  => [],
            'ul'      => [],
            'ol'      => [],
            'li'      => [],
            'span'    => [
                'style' => [],
            ],
            'p'       => [],
            'br'      => [],
            'img'     => [
                'src' => [],
                'alt' => [],
            ],
        ];

        $isActive = get_post_meta($postId, "widget_status", true);

        $device    = "";
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
            $device = "mobile";
        } else {
            $device = "desktop";
        }

        $pageURL = (empty($_SERVER['HTTPS']) ? 'http' : 'https')."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        global $wp_query;
        $pageTitle = "";
        if (isset($wp_query->post->post_title)) {
            $pageTitle = $wp_query->post->post_title;
        }

        if ($isActive == "yes") {
            $this->isWidgetActive = true;

            $channelsSetting = get_post_meta($postId, "channel_settings", true);

            $settings = [];
            if (!empty($channelsSetting)) {
                $socialIcons = Ginger_Social_Icons::icon_list();
                foreach ($channelsSetting as $key => $channel) {
                    if ($key == "contact_form") {
                        $channel['value'] = "123";
                    }

                    if (isset($socialIcons[$key]) && ($channel['for_desktop'] || $channel['for_mobile']) && !empty($channel['value'])) {
                        $channels = $socialIcons[$key];

                        $defaultChannelSettings    = Ginger_Social_Icons::get_channel_setting($channels);
                        $defaultContactFormSetting = Ginger_Social_Icons::get_contact_form_setting($channels);
                        $channelsSetting           = shortcode_atts($defaultChannelSettings, $channel);
                        $contactFormSetting        = get_post_meta($postId, "contact_form_settings", true);
                        $contactFormSetting        = isset($contactFormSetting)&&is_array($contactFormSetting) ? $contactFormSetting : [];
                        $contactFormSetting        = shortcode_atts($defaultContactFormSetting, $contactFormSetting);
                        $visibleCount = 0;
                        foreach ($contactFormSetting['fields'] as $field) {
                            if ($field['is_visible'] == 1) {
                                $visibleCount++;
                            }
                        }

                        $imageUrl = "";
                        $value    = trim($channelsSetting['value']);
                        if (!empty($channelsSetting['image_id'])) {
                            $imageData = wp_get_attachment_image_src($channelsSetting['image_id'], "full");
                            if (!empty($imageData) && isset($imageData[0])) {
                                $imageUrl = $imageData[0];
                            }
                        }

                        if ($key == "contact_form") {
                            $value = "";
                        }

                        if (!empty($value)  || ($key == "contact_form" && $visibleCount > 0)) {
                            $whatsapp_message = "";
                            $href   = "javascript:;";
                            $target = "";
                            if ($key == "whatsapp") {
                                $value  = trim($value, "+");
                                $value  = str_replace([" ", "-", "_"], ["", "", ""], $value);
                                $href   = esc_url("https://web.whatsapp.com/send?phone=".$value);
                                $target = "_blank";
                                $whatsapp_message = isset($channelsSetting['whatsapp_message']) && !empty($channelsSetting['whatsapp_message']) ? $channelsSetting['whatsapp_message'] : "";
                                $whatsapp_message = str_replace(['{page_url}', '{page_title}'], [$pageURL, $pageTitle], $whatsapp_message);
                                if (!empty($whatsapp_message)) {
                                    $href = $href."&text=".esc_attr(trim($whatsapp_message));
                                    // $value = $value."?text=".esc_attr(trim($whatsapp_message));
                                }
                            } else if ($key == "facebook_messenger") {
                                $href   = esc_url("https://m.me/".$value);
                                $target = "_blank";
                            } else if ($key == "viber") {
                                $value = trim($value, "+");
                                if ($device == "mobile") {
                                    $href = $value;
                                } else {
                                    $href = "+".$value;
                                }
                                $target = "";
                                $href   = "viber://chat?number=".$href;
                            } else if ($key == "line") {
                                $href   = esc_url($value);
                                $target = "_blank";
                            } else if ($key == "phone") {
                                $value  = str_replace([" ", "-", "_"], ["", "", ""], $value);
                                $href   = "tel:".$value;
                                $target = "";
                            } else if ($key == "mail") {
                                $href   = "mailto:".trim($value);
                                $others = isset($channelsSetting['email_subject']) && !empty($channelsSetting['email_subject']) ? $channelsSetting['email_subject'] : "";
                                $others = str_replace(['{page_url}', '{page_title}'], [$pageURL, $pageTitle], $others);
                                if (!empty($others)) {
                                    $href = $href."?subject=".esc_attr($others);
                                }
                            } else if ($key == "telegram") {
                                $href   = esc_url("https://telegram.me/".$value);
                                $target = "_blank";
                            } else if ($key == "vkontakte") {
                                $href   = esc_url("https://vk.me/".$value);
                                $target = "_blank";
                            } else if ($key == "sms") {
                                $value       = str_replace([" ", "-", "_"], ["", "", ""], $value);
                                $href        = "sms:".$value;
                                $sms_message = isset($channelsSetting['sms_message']) && !empty($channelsSetting['sms_message']) ? $channelsSetting['sms_message'] : "";
                                $sms_message = str_replace(['{page_url}', '{page_title}'], [$pageURL, $pageTitle], $sms_message);
                                if (!empty($sms_message)) {
                                    $href = $href.";?&body=".esc_attr(trim($sms_message));
                                }
                            } else if ($key == "wechat") {
                                $channelsSetting['title'] = $channelsSetting['title'].": ".$channelsSetting['value'];
                            } else if ($key == "skype") {
                                $href = "skype:".$value."?chat";
                            } else if ($key == "snapchat") {
                                $href   = esc_url("https://www.snapchat.com/add/".$value);
                                $target = "_blank";
                            } else if ($key == "linkedin") {
                                $href   = esc_url("https://www.linkedin.com/".$value);
                                $target = "_blank";
                            } else if ($key == "twitter") {
                                $href   = esc_url("https://twitter.com/".$value);
                                $target = "_blank";
                            } else if ($key == "instagram") {
                                if($channelsSetting['is_ig_link'] == "yes") {
                                    $href = esc_url("https://ig.me/m/" . $value);
                                } else {
                                    $href = esc_url("https://www.instagram.com/".$value);
                                }
                                $target = "_blank";
                            } else if ($key == "waze") {
                                $href   = esc_url($value);
                                $target = "_blank";
                            } else if ($key == "link") {
                                $href   = esc_url($value);
                                if($channelsSetting['open_in_new_tab'] == "yes") {
                                    $target = "_blank";
                                }
                            } else if ($key == "slack") {
                                $href   = esc_url($value);
                                $target = "_blank";
                            } else if ($key == "google-map") {
                                $href   = esc_url($value);
                                $target = "_blank";
                            } else if ($key == "custom-link") {
                                $href   = esc_url($value);
                                if($channelsSetting['open_in_new_tab'] == "yes") {
                                    $target = "_blank";
                                }
                            } else if ($key == "signal") {
                                $value  = trim($value, "https://signal.group/");
                                $value  = trim($value, "http://signal.group/");
                                $value  = "https://signal.group/".$value;
                                $href   = esc_url($value);
                                $target = "_blank";
                            } else if ($key == "tiktok") {
                                $value  = trim($value, "https://tiktok.com/");
                                $value  = trim($value, "http://tiktok.com/");
                                $value  = trim($value, "@");
                                $value  = "https://tiktok.com/@".$value;
                                $href   = esc_url($value);
                                $target = "_blank";
                            } else if ($key == "discord") {
                                $href   = esc_url($value);
                                $target = "_blank";
                            } else if ($key == "microsoft_teams") {
                                $href   = esc_url($value);
                                $target = "_blank";
                            } else if ($key == "zalo") {
                                $href   = esc_url($value);
                                $target = "_blank";
                            }//end if

                            $channelSetting = [
                                'title'            => esc_attr($channelsSetting['title']),
                                'bg_color'         => $channelsSetting['bg_color'],
                                'bg_hover_color'   => $channelsSetting['bg_hover_color'],
                                'text_color'       => $channelsSetting['text_color'],
                                'text_hover_color' => $channelsSetting['text_hover_color'],
                                'icon'             => $channelsSetting['icon'],
                                'href'             => $href,
                                'for_desktop'      => $channelsSetting['for_desktop'],
                                'for_mobile'       => $channelsSetting['for_mobile'],
                                'channel'          => $key,
                                'target'           => $target,
                                'image_url'        => $imageUrl,
                                'value'            => esc_attr($value),
                                'custom_id'        => $channelsSetting['custom_id'],
                                'custom_class'     => $channelsSetting['custom_class'],
                                'whatsapp_message' => $whatsapp_message,
                            ];

                            $wechatPopupSetting = [
                                'wechat_qr_popup_heading' => esc_attr($channelsSetting['wechat_qr_popup_heading']),
                                'wechat_qr_bg_color'      => $channelsSetting['wechat_qr_bg_color'],
                                'wechat_qr_heading'       => esc_attr($channelsSetting['wechat_qr_heading']),
                                'wechat_qr_img'           => esc_url($channelsSetting['wechat_qr_img']),
                            ];
                            if ($key == "wechat") {
                                $channelSetting['wechat_popup_setting'] = $wechatPopupSetting;
                            }

                            $whatsappPopupSetting = [
                                'show_whatsapp_popup'      => $channelsSetting['show_whatsapp_popup'],
                                'custom_whatsapp_profile'  => $channelsSetting['custom_whatsapp_profile'],
                                'whatsapp_popup_title'     => esc_attr($channelsSetting['whatsapp_popup_title']),
                                'whatsapp_popup_sub_title' => esc_attr($channelsSetting['whatsapp_popup_sub_title']),
                                'whatsapp_popup_text'      => wp_kses($channelsSetting['whatsapp_popup_text'], $allowedTags),
                                'user_profile_image'       => $channelsSetting['whatsapp_user_profile_img'],
                                'user_name_to_display'     => esc_attr($channelsSetting['whatsapp_name_to_display']),
                            ];

                            if ($key == "whatsapp") {
                                $channelSetting['whatsapp_popup_setting'] = $whatsappPopupSetting;
                                $channelSetting['is_mobile_link'] = $channelsSetting['is_mobile_link'];
                            }

                            $contactFormSetting = [
                                'btn_bg_hover_color'   => esc_attr($contactFormSetting['btn_bg_hover_color']),
                                'btn_bg_color'         => esc_attr($contactFormSetting['btn_bg_color']),
                                'btn_color'            => esc_attr($contactFormSetting['btn_color']),
                                'btn_hover_color'      => esc_attr($contactFormSetting['btn_hover_color']),
                                'success_msg'          => esc_attr($contactFormSetting['success_msg']),
                                'btn_text'             => esc_attr($contactFormSetting['btn_text']),
                                'form_title'           => esc_attr($contactFormSetting['form_title']),
                                'is_redirect'          => esc_attr($contactFormSetting['is_redirect']),
                                'redirect_url'         => esc_url($contactFormSetting['redirect_url']),
                                'is_redirect_new_tab'  => esc_attr($contactFormSetting['is_redirect_new_tab']),
                                'is_close_aftr_submit' => esc_attr($contactFormSetting['is_close_aftr_submit']),
                                'close_after_sec'      => esc_attr($contactFormSetting['close_after_sec']),
                                'is_send_leads'        => esc_attr($contactFormSetting['is_send_leads']),
                                'auto_responder'       => esc_attr($contactFormSetting['auto_responder']),
                                'fields'               => [
                                    'name'    => [
                                        'label'            => esc_attr($contactFormSetting['fields']['name']['label']),
                                        'placeholder_text' => esc_attr($contactFormSetting['fields']['name']['placeholder_text']),
                                        'is_visible'       => esc_attr($contactFormSetting['fields']['name']['is_visible']),
                                        'is_required'      => esc_attr($contactFormSetting['fields']['name']['is_required']),
                                        'required_msg'     => esc_attr($contactFormSetting['fields']['name']['required_msg']),
                                    ],
                                    'email'   => [
                                        'label'            => esc_attr($contactFormSetting['fields']['email']['label']),
                                        'placeholder_text' => esc_attr($contactFormSetting['fields']['email']['placeholder_text']),
                                        'is_visible'       => esc_attr($contactFormSetting['fields']['email']['is_visible']),
                                        'is_required'      => esc_attr($contactFormSetting['fields']['email']['is_required']),
                                        'required_msg'     => esc_attr($contactFormSetting['fields']['email']['required_msg']),
                                    ],
                                    'phone'   => [
                                        'label'            => esc_attr($contactFormSetting['fields']['phone']['label']),
                                        'placeholder_text' => esc_attr($contactFormSetting['fields']['phone']['placeholder_text']),
                                        'is_visible'       => esc_attr($contactFormSetting['fields']['phone']['is_visible']),
                                        'is_required'      => esc_attr($contactFormSetting['fields']['phone']['is_required']),
                                        'required_msg'     => esc_attr($contactFormSetting['fields']['phone']['required_msg']),
                                    ],
                                    'message' => [
                                        'label'            => esc_attr($contactFormSetting['fields']['message']['label']),
                                        'placeholder_text' => esc_attr($contactFormSetting['fields']['message']['placeholder_text']),
                                        'is_visible'       => esc_attr($contactFormSetting['fields']['message']['is_visible']),
                                        'is_required'      => esc_attr($contactFormSetting['fields']['message']['is_required']),
                                        'required_msg'     => esc_attr($contactFormSetting['fields']['message']['required_msg']),
                                    ],
                                    'consent_checkbox' => [
                                        'label'            => esc_attr($contactFormSetting['fields']['consent_checkbox']['label']),
                                        'placeholder_text' => $contactFormSetting['fields']['consent_checkbox']['placeholder_text'],
                                        'is_visible'       => esc_attr($contactFormSetting['fields']['consent_checkbox']['is_visible']),
                                        'is_required'      => esc_attr($contactFormSetting['fields']['consent_checkbox']['is_required']),
                                        'required_msg'     => esc_attr($contactFormSetting['fields']['consent_checkbox']['required_msg']),
                                    ],
                                ],
                            ];

                            if ($key == "contact_form") {
                                $channelSetting['contact_form_setting'] = $contactFormSetting;
                            }

                            if($key == "instagram") {
                                $channelSetting['is_ig_link'] = $channelsSetting['is_ig_link'];
                            }

                            $settings[] = $channelSetting;
                        }//end if
                    }//end if
                }//end foreach
            }//end if

            return $settings;
        }//end if

    }//end check_for_channels()


}//end class


if (class_exists("GP_Front_Sticky_Chat_Buttons")) {
    $gpSocial = new GP_Front_Sticky_Chat_Buttons();
}
