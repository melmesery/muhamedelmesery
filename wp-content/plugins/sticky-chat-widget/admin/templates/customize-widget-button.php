<?php
/**
 * Customize widget setting functionality of the plugin.
 *
 * @author  : Ginger Plugins <gingerplugins@gmail.com>
 * @license : GPL2
 * */
defined('ABSPATH') or die('Direct Access is not allowed');
?>

<?php
$defaultWidgetSettings = Ginger_Social_Icons::get_customize_widget_setting();
$widgetSettings        = get_post_meta($postId, "widget_settings", true);
$widgetSettings        = shortcode_atts($defaultWidgetSettings, $widgetSettings);
$imageUrl = "";
if (!empty($widgetSettings['custom_icon'])) {
    $imageData = wp_get_attachment_image_src($widgetSettings['custom_icon'], "full");
    if (!empty($imageData) && isset($imageData[0])) {
        $imageUrl = $imageData[0];
    } else {
        $customIcon = "";
    }
}
?>

<div id="icon-settings" class="setting-tab">
    <div class="setting-title"><?php esc_html_e("Customize Widget", "sticky-chat-widget") ?></div>
    <div class="gp-form-field">
        <div class="gp-form-label">
            <label><?php esc_html_e("Select view:", "sticky-chat-widget") ?></label>
        </div>
        <div class="gp-form-input">
            <div class="image-radio-buttons in-flex">
                <div class="image-radio-button">
                    <input id="view_icon" type="radio" class="sr-only ginger-menu-view" name="widget_settings[view]" value="icon_view" <?php checked($widgetSettings['view'], "icon_view") ?>>
                    <label for="view_icon" class="radio-image"><img src='<?php echo esc_url(GSB_PLUGIN_URL)."dist/admin/images/icon-view.svg"; ?>'></label>
                    <label for="view_icon" class="radio-image-checked"><img src='<?php echo esc_url(GSB_PLUGIN_URL)."dist/admin/images/icon-view-selected.svg"; ?>'></label>
                    <label for="view_icon" class="image-radio-label"><?php esc_html_e("Icon view", "sticky-chat-widget") ?></label>
                </div>
                <div class="image-radio-button">
                    <input id="view_list" type="radio" class="sr-only ginger-menu-view" name="widget_settings[view]" value="list_view" <?php checked($widgetSettings['view'], "list_view") ?>>
                    <label for="view_list" class="radio-image"><img src='<?php echo esc_url(GSB_PLUGIN_URL)."dist/admin/images/list-view.svg"; ?>'></label>
                    <label for="view_list" class="radio-image-checked"><img src='<?php echo esc_url(GSB_PLUGIN_URL)."dist/admin/images/list-view-selected.svg"; ?>'></label>
                    <label for="view_list" class="image-radio-label"><?php esc_html_e("List view", "sticky-chat-widget") ?></label>
                </div>
                <div class="image-radio-button">
                    <input id="view_grid" type="radio" class="sr-only ginger-menu-view" name="widget_settings[view]" value="grid_view" <?php checked($widgetSettings['view'], "grid_view") ?>>
                    <label for="view_grid" class="radio-image"><img src='<?php echo esc_url(GSB_PLUGIN_URL)."dist/admin/images/grid-view.svg"; ?>'></label>
                    <label for="view_grid" class="radio-image-checked"><img src='<?php echo esc_url(GSB_PLUGIN_URL)."dist/admin/images/grid-view-selected.svg"; ?>'></label>
                    <label for="view_grid" class="image-radio-label"><?php esc_html_e("Grid view", "sticky-chat-widget") ?></label>
                </div>
                <div class="image-radio-button">
                    <input id="corner_circle_icon" type="radio" class="sr-only ginger-menu-view" name="widget_settings[view]" value="corner_circle_view" <?php checked($widgetSettings['view'], "corner_circle_view") ?>>
                    <label for="corner_circle_icon" class="radio-image"><img src='<?php echo esc_url(GSB_PLUGIN_URL)."dist/admin/images/corner-circle.svg"; ?>'></label>
                    <label for="corner_circle_icon" class="radio-image-checked"><img src='<?php echo esc_url(GSB_PLUGIN_URL)."dist/admin/images/corner-circle-selected.svg"; ?>'></label>
                    <label for="corner_circle_icon" class="image-radio-label"><?php esc_html_e("Corner circle", "sticky-chat-widget") ?></label>
                </div>
                <div class="image-radio-button">
                    <input id="menu_icon" type="radio" class="sr-only ginger-menu-view" name="widget_settings[view]" value="menu_view" <?php checked($widgetSettings['view'], "menu_view") ?>>
                    <label for="menu_icon" class="radio-image"><img src='<?php echo esc_url(GSB_PLUGIN_URL)."dist/admin/images/menu-view.svg"; ?>'></label>
                    <label for="menu_icon" class="radio-image-checked"><img src='<?php echo esc_url(GSB_PLUGIN_URL)."dist/admin/images/menu-view-selected.svg"; ?>'></label>
                    <label for="menu_icon" class="image-radio-label"><?php esc_html_e("Menu view", "sticky-chat-widget") ?></label>
                </div>
            </div>
        </div>
    </div>
    <div class="list-view-field <?php echo ($widgetSettings['view'] != "icon_view" && $widgetSettings['view'] != 'corner_circle_view' && $widgetSettings['view'] != 'menu_view') ? "active" : "" ?>">
        <div class="gp-form-field">
            <div class="gp-form-label">
                <label for="list_view_title"><?php esc_html_e("Header text:", 'sticky-chat-widget') ?></label>
            </div>
            <div class="gp-form-input medium-input">
                <input type="text" class="" name="widget_settings[list_view_title]" id="list_view_title" value="<?php echo esc_attr($widgetSettings['list_view_title']) ?>">
            </div>
        </div>
        <div class="gp-form-field">
            <div class="gp-form-label">
                <label for="grid_view_title"><?php esc_html_e("Body text:", 'sticky-chat-widget') ?></label>
            </div>
            <div class="gp-form-input medium-input">
                <?php
                $settings = [
                    'media_buttons'    => false,
                    'wpautop'          => false,
                    'drag_drop_upload' => false,
                    'textarea_name'    => 'widget_settings[list_view_subtitle]',
                    'textarea_rows'    => 4,
                    'quicktags'        => false,
                    'tinymce'          => [
                        'toolbar1'    => 'bold, italic, underline',
                        'toolbar2'    => '',
                        'toolbar3'    => '',
                        'content_css' => GSB_PLUGIN_URL.'dist/admin/css/myEditorCSS.css',
                    ],
                ];
                wp_editor($widgetSettings['list_view_subtitle'], "grid_view_title", $settings);
                ?>
                <span class="scw-badges view-badges"><?php esc_html_e("{page_url}", "sticky-chat-widget") ?></span>
                <span class="scw-badges view-badges"><?php esc_html_e("{page_title}", "sticky-chat-widget") ?></span>
            </div>
        </div>
        <?php if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) { ?>
            <div class="gp-form-field">
                <div class="gp-form-label">
                </div>
                <div class="gp-form-input d-flex">
            <span class="dashboard-switch in-flex on-off">
                <input type="hidden" name="widget_settings[woocommerce_customization]" value="no">
                <input type="checkbox" id="woocommerce_customization" name="widget_settings[woocommerce_customization]" value="yes" class="sr-only" <?php checked($widgetSettings['woocommerce_customization'], "yes") ?>>
                <label for="woocommerce_customization">
                    <svg width="27" height="18" viewBox="0 0 27 18" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M2.51792 17.0012H2.03931C1.43584 17.0012 0.957226 16.5225 0.957226 15.9191C0.957226 15.2948 1.45665 14.7954 2.08093 14.7954H2.51792V13.9214H1.93526C0.873989 13.9214 0 14.7954 0 15.8566C0 17.0428 0.957226 18 2.14335 18H2.51792V17.0012ZM24.1179 17.0012H23.6393C23.0358 17.0012 22.5572 16.5225 22.5572 15.9191C22.5572 15.2948 23.0567 14.7954 23.6809 14.7954H24.1179V13.9214H23.5145C22.4532 13.9214 21.5792 14.7954 21.5792 15.8566C21.5792 17.0428 22.5364 18 23.7226 18H24.0971V17.0012H24.1179ZM4.59885 13.9214C3.66243 13.9214 2.9133 14.837 2.9133 15.9607C2.9133 17.0844 3.66243 18 4.59885 18C5.53526 18 6.2844 17.0844 6.2844 15.9607C6.2844 14.837 5.53526 13.9214 4.59885 13.9214ZM4.59885 17.2301C4.18266 17.2301 3.87052 16.6682 3.87052 15.9815C3.87052 15.2948 4.20347 14.7329 4.59885 14.7329C5.01503 14.7329 5.32717 15.2948 5.32717 15.9815C5.32717 16.6682 4.99422 17.2301 4.59885 17.2301ZM6.2844 18L6.92948 13.9214H8.28208L8.67746 15.9607L9.19769 13.9214H10.4462L11.0497 18H10.0093L9.6763 15.2324L9.11445 18H8.40694L7.637 15.274L7.28324 18H6.2844ZM11.2786 18L11.9237 13.9214H13.2763L13.6509 15.9607L14.1711 13.9214H15.4405L16.0439 18H15.0035L14.6497 15.2324L14.0879 18H13.4012L12.6104 15.274L12.2775 18H11.2786ZM16.2104 18V13.9214H18.333V14.7121H17.2717V15.5861H18.2081V16.3353H17.1468V17.126H18.333V18H16.2104ZM24.5133 18V13.9214H26.6358V14.7121H25.5746V15.5861H26.511V16.3353H25.4497V17.126H26.6358V18H24.5133Z" fill="black"/> <path d="M20.8093 16.1064C21.1422 16.1064 21.4335 15.8358 21.4335 15.4821V15.1075C21.4335 14.4416 20.8925 13.9214 20.2474 13.9214H18.7075V18H19.6647V16.0855L20.7052 18H21.7665L20.8093 16.1064ZM19.9977 15.7526H19.6439V14.7538H20.0393C20.3098 14.7538 20.5179 14.9619 20.5179 15.2324C20.4971 15.5237 20.2682 15.7526 19.9977 15.7526Z" fill="black"/> <path d="M21.6412 10.5711H5.05627C3.72447 10.5711 2.62158 9.48902 2.62158 8.13642V2.43468C2.62158 1.10289 3.70366 0 5.05627 0H21.6412C22.973 0 24.0759 1.08208 24.0759 2.43468V8.13642C24.0759 9.48902 22.973 10.5711 21.6412 10.5711Z" fill="#7F54B3"/> <path d="M4.95326 8.51101C4.95326 8.51101 5.43187 10.4671 6.3891 9.1561C7.34632 7.84511 8.26193 5.785 8.26193 5.785C8.26193 5.785 8.32436 5.26477 8.59488 6.40928C8.8654 7.55379 10.0307 9.07286 10.0307 9.07286C10.0307 9.07286 11.092 10.3838 11.529 9.01043C11.3625 7.17922 12.0076 3.84974 12.6527 2.24742C12.9232 0.832395 11.4249 1.22777 11.3001 1.58153C11.1752 1.93529 10.218 4.01621 10.1556 6.2428C10.1556 6.2428 9.36482 4.12026 9.30239 3.32951C9.23997 2.55956 8.42841 2.35147 7.94979 3.1006C7.47118 3.84974 6.03534 6.74222 6.03534 6.74222L5.11973 2.01852C5.11973 2.01852 4.20413 0.811586 3.66309 2.28904C3.66309 2.28904 4.62031 8.09483 4.95326 8.51101ZM17.2099 3.32951C15.7741 0.957251 13.6723 2.74685 13.6723 2.74685C13.6723 2.74685 12.07 4.59887 12.7984 7.11679C13.9637 9.6139 15.566 8.4902 16.003 8.30292C16.44 8.09483 18.3544 5.93066 17.2099 3.32951ZM16.1278 5.41043C16.1278 6.40928 15.3163 7.32488 14.6296 7.11679C14.255 6.9087 14.0261 6.30523 14.0261 5.3272C14.359 3.70407 15.1082 3.49598 15.4827 3.5376C16.1903 3.91217 16.1486 4.76535 16.1278 5.41043ZM22.5371 3.32951C21.1012 0.957251 18.9995 2.74685 18.9995 2.74685C18.9995 2.74685 17.3972 4.59887 18.1255 7.11679C19.2908 9.6139 20.8932 8.4902 21.3301 8.30292C21.7879 8.09483 23.7024 5.93066 22.5371 3.32951ZM21.455 5.41043C21.455 6.40928 20.6434 7.32488 19.9567 7.11679C19.5822 6.9087 19.3533 6.30523 19.3533 5.3272C19.6862 3.70407 20.4353 3.49598 20.8099 3.5376C21.5174 3.91217 21.4966 4.76535 21.455 5.41043Z" fill="white"/> <path d="M12.9014 10.5711L16.6262 12.7561L15.8563 10.5711L13.7129 9.96765L12.9014 10.5711Z" fill="#7F54B3"/></svg>
                    <?php esc_html_e("WooCommerce Customization", "sticky-chat-widget") ?>
                </label>
            </span>
                </div>
            </div>
            <div class="woocommerce-customization-setting <?php echo ($widgetSettings['woocommerce_customization'] == "yes") ? "active" : ""; ?>">
                <div class="gp-form-field">
                    <div class="gp-form-label">
                        <label for="woo_list_view_title"><?php esc_html_e("Header text:", 'sticky-chat-widget') ?></label>
                    </div>
                    <div class="gp-form-input medium-input">
                        <input type="text" class="" name="widget_settings[woo_list_view_title]" id="woo_list_view_title" value="<?php echo esc_attr($widgetSettings['woo_list_view_title']) ?>">
                    </div>
                </div>
                <div class="gp-form-field">
                    <div class="gp-form-label">
                        <label for="woo_list_view_subtitle"><?php esc_html_e("Body text:", 'sticky-chat-widget') ?></label>
                    </div>
                    <div class="gp-form-input medium-input">
                        <?php
                        $settings = [
                            'media_buttons'    => false,
                            'wpautop'          => false,
                            'drag_drop_upload' => false,
                            'textarea_name'    => 'widget_settings[woo_list_view_subtitle]',
                            'textarea_rows'    => 4,
                            'quicktags'        => false,
                            'tinymce'          => [
                                'toolbar1'    => 'bold, italic, underline',
                                'toolbar2'    => '',
                                'toolbar3'    => '',
                                'content_css' => GSB_PLUGIN_URL.'dist/admin/css/myEditorCSS.css',
                            ],
                        ];
                        wp_editor($widgetSettings['woo_list_view_subtitle'], "woo_list_view_subtitle", $settings);
                        ?>
                    </div>
                    <span class="scw-badges add-tags-badge">
                    <?php esc_html_e("Add Tags", "sticky-chat-widget") ?>
                    <svg width="14" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg"> <path fill-rule="evenodd" clip-rule="evenodd" d="M7.69458 7.69458C7.49896 7.89014 7.23368 8 6.95707 8C6.68047 8 6.41518 7.89014 6.21956 7.69458L0.318462 1.79348C0.21883 1.69725 0.139361 1.58214 0.0846901 1.45487C0.0300196 1.32761 0.00124299 1.19072 3.93856e-05 1.05222C-0.00116422 0.913706 0.0252295 0.776345 0.0776801 0.648146C0.130131 0.519946 0.207588 0.403476 0.305532 0.305532C0.403476 0.207587 0.519946 0.13013 0.648145 0.0776796C0.776344 0.0252291 0.913706 -0.00116422 1.05221 3.93863e-05C1.19072 0.00124299 1.32761 0.0300196 1.45487 0.0846901C1.58214 0.139361 1.69725 0.21883 1.79348 0.318461L6.95707 5.48206L12.1207 0.318461C12.3174 0.128443 12.5809 0.0232993 12.8544 0.0256761C13.1279 0.0280528 13.3896 0.13776 13.583 0.331168C13.7764 0.524577 13.8861 0.786212 13.8885 1.05972C13.8908 1.33323 13.7857 1.59674 13.5957 1.79348L7.69458 7.69458Z" fill="#7A8790"/> </svg>
                </span>
                    <div class="woocommerce-badges">
                        <span class="scw-badges woo-view-badges"><?php esc_html_e("{page_url}", "sticky-chat-widget") ?></span>
                        <span class="scw-badges woo-view-badges"><?php esc_html_e("{page_title}", "sticky-chat-widget") ?></span>
                        <span class="scw-badges woo-view-badges"><?php esc_html_e("{product-name}", "sticky-chat-widget") ?></span>
                        <span class="scw-badges woo-view-badges"><?php esc_html_e("{product-sku}", "sticky-chat-widget") ?></span>
                        <span class="scw-badges woo-view-badges"><?php esc_html_e("{product-price}", "sticky-chat-widget") ?></span>
                    </div>
                </div>
            </div>
        <?php }//end if
        ?>
        <div class="flex-input mt-20">
            <div class="gp-form-field">
                <div class="gp-form-label">
                    <label for="list-bg-color-custom"><?php esc_html_e("Header background color:", "sticky-chat-widget") ?></label>
                </div>
                <div class="gp-form-input ginger-color-list">
                    <input id="list-bg-color-custom" class="custom-color-picker" type="text" name="widget_settings[list_title_bg]" value="<?php echo esc_attr($widgetSettings['list_title_bg']) ?>" style="background: <?php echo esc_attr($widgetSettings['list_title_bg']) ?>">
                </div>
            </div>
            <div class="gp-form-field">
                <div class="gp-form-label">
                    <label for="list-title-color-custom"><?php esc_html_e("Header text color:", "sticky-chat-widget") ?></label>
                </div>
                <div class="gp-form-input ginger-color-list">
                    <input id="list-title-color-custom" class="custom-color-picker" type="text" name="widget_settings[list_title_color]" value="<?php echo esc_attr($widgetSettings['list_title_color']) ?>" style="background: <?php echo esc_attr($widgetSettings['list_title_color']) ?>">
                </div>
            </div>
        </div>
    </div>
    <div class="gp-form-field mt-20">
        <div class="gp-form-label">
            <label><?php esc_html_e("Icon:", "sticky-chat-widget") ?></label>
        </div>
        <div class="gp-form-input">
            <div class="ginger-close-icons">
                <ul>
                    <?php foreach ($closeIcons as $key => $value) { ?>
                        <li class="<?php echo esc_attr($upgrade) ?>">
                            <input class="sr-only" type="radio" name="widget_settings[chat_icon]" <?php echo (!empty($disabled)) ? esc_attr($value['disabled']) : ""; ?> value="<?php echo esc_attr($key) ?>" <?php checked($widgetSettings['chat_icon'], $key) ?> id="chat_icon_<?php echo esc_attr($key) ?>">
                            <?php if ($value['disabled'] == "disabled" && !empty($disabled)) { ?>
                                <label data-ginger-tooltip="<?php esc_html_e("Upgrade to Pro", 'sticky-chat-widget') ?>" id="label-chat_icon_<?php echo esc_attr($key) ?><?php echo esc_attr($disabled) ?>" for="chat_icon_<?php echo esc_attr($key) ?>">
                                    <a class="" href="<?php echo esc_url(GP_Admin_Sticky_Chat_Buttons::upgrade_url()) ?>" target="_blank">
                                    <span class="svg-icon">
                                        <?php Ginger_Social_Icons::load_and_sanitize_svg($value['icon']); ?>
                                    </span>
                                    </a>
                                </label>
                            <?php } else { ?>
                                <label id="label-chat_icon_<?php echo esc_attr($key) ?><?php echo esc_attr($disabled) ?>" for="chat_icon_<?php echo esc_attr($key) ?>">
                                <span class="svg-icon">
                                    <?php Ginger_Social_Icons::load_and_sanitize_svg($value['icon']); ?>
                                </span>
                                </label>
                            <?php } ?>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="gp-form-field mt-20">
        <div class="gp-form-label">
            <label><?php esc_html_e("Position:", "sticky-chat-widget") ?></label>
        </div>
        <div class="gp-form-input medium-input">
            <div class="gp-radio-buttons in-flex">
                <div class="gp-radio-button">
                    <input id="position_left" type="radio" class="sr-only" name="widget_settings[position]" value="left" <?php checked($widgetSettings['position'], "left") ?>>
                    <label for="position_left"><?php esc_html_e("Left", "sticky-chat-widget") ?></label>
                </div>
                <div class="gp-radio-button">
                    <input id="position_right" type="radio" class="sr-only" name="widget_settings[position]" value="right" <?php checked($widgetSettings['position'], "right") ?>>
                    <label for="position_right"><?php esc_html_e("Right", "sticky-chat-widget") ?></label>
                </div>
                <div class="gp-radio-button <?php echo esc_attr($disabled) ?> <?php echo esc_attr($upgrade) ?>">
                    <input id="position_custom" type="radio" class="sr-only" <?php echo esc_attr($disabled) ?> name="widget_settings[position]" value="custom" <?php checked($widgetSettings['position'], "custom") ?>>
                    <label for="position_custom">
                        <?php if (!empty($disabled)) { ?>
                            <a href="javascript:;" class="upgrade-link-btn" target="_blank"><?php esc_html_e("Custom (Pro)", "sticky-chat-widget") ?></a>
                        <?php } else { ?>
                            <?php esc_html_e("Custom", "sticky-chat-widget") ?>
                        <?php } ?>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="gp-form-field mt-20 position-in-mobile-toggle">
        <div class="gp-form-label">
        </div>
        <div class="gp-form-input d-flex">
            <span class="dashboard-switch in-flex on-off">
                <input type="hidden" name="position_in_mobile" value="no">
                <input type="checkbox" id="position_in_mobile" name="widget_settings[position_in_mobile]" <?php echo esc_attr($disabled) ?> value="yes" class="sr-only" <?php checked($widgetSettings['position_in_mobile'], "yes") ?>>
                <label for="position_in_mobile"><?php esc_html_e("Position in mobile", "sticky-chat-widget") ?></label>
                <?php if (!empty($disabled)) { ?>
                    <a class="upgrade-link in-block" href="javascript:;" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($icons['pro']); ?></a>
                <?php } ?>
            </span>
        </div>
    </div>
    <div class="gp-form-field mt-20">
        <div class="gp-form-label">
        </div>
        <div class="gp-form-input d-flex">
            <span class="dashboard-switch in-flex on-off">
                <input type="hidden" name="widget_settings[show_greeting_message]" value="no">
                <input type="checkbox" id="show_greeting_message" name="widget_settings[show_greeting_message]" value="yes" class="sr-only" <?php checked($widgetSettings['show_greeting_message'], "yes") ?>>
                <label for="show_greeting_message"><?php esc_html_e("Show greeting message", "sticky-chat-widget") ?></label>
            </span>
        </div>
    </div>
    <div class="greeting-message-setting <?php echo ($widgetSettings['show_greeting_message'] == "yes") ? "" : "hidden" ?>">
        <div class="gp-form-field">
            <div class="gp-form-label">
                <label for="ginger_sb_greeting_text"><?php esc_html_e("Greeting text", "sticky-chat-widget") ?></label>
            </div>
            <div class="gp-form-input medium-input">
                <textarea name="widget_settings[greeting_text]" id="ginger_sb_greeting_text"><?php echo esc_attr($widgetSettings['greeting_text']) ?></textarea>
            </div>
        </div>
        <div class="gp-form-field">
            <div class="gp-form-label">
                <span class="dashboard-switch in-flex greeting-timer-box">
                    <?php esc_html_e("Show Greeting message after ", "sticky-chat-widget") ?>
                    <input class="tiny-input only-numeric" type="text" name="widget_settings[greeting_after]" value="<?php echo esc_attr($widgetSettings['greeting_after']) ?>">
                    <?php esc_html_e("  seconds", "sticky-chat-widget") ?>
                </span>
            </div>
        </div>
        <div class="flex-input mt-20">
            <div class="gp-form-field">
                <div class="gp-form-label">
                    <label for="greeting-bg-color"><?php esc_html_e("Greeting background color:", "sticky-chat-widget") ?></label>
                </div>
                <div class="gp-form-input ginger-color-list">
                    <input id="greeting-bg-color" class="custom-color-picker" type="text" name="widget_settings[greeting_bg_color]" value="<?php echo esc_attr($widgetSettings['greeting_bg_color']) ?>" style="background: <?php echo esc_attr($widgetSettings['greeting_bg_color']) ?>">
                </div>
            </div>
            <div class="gp-form-field">
                <div class="gp-form-label">
                    <label for="greeting-text-color"><?php esc_html_e("Greeting text color:", "sticky-chat-widget") ?></label>
                </div>
                <div class="gp-form-input ginger-color-list">
                    <input id="greeting-text-color" class="custom-color-picker" type="text" name="widget_settings[greeting_text_color]" value="<?php echo esc_attr($widgetSettings['greeting_text_color']) ?>" style="background: <?php echo esc_attr($widgetSettings['greeting_text_color']) ?>">
                </div>
            </div>
        </div>
    </div>
    <div class="gp-form-field mt-20 menu-view icon-view-field <?php echo ($widgetSettings['view'] == "icon_view") ? "active activate" : "" ?>">
        <div class="gp-form-label">
            <label><?php esc_html_e("Icons view: ", "sticky-chat-widget") ?><span class="ginger-info" data-ginger-tooltip="<?php esc_html_e("You can display chat buttons menu by vertical or horizontal", 'sticky-chat-widget') ?>"><span class="dashicons dashicons-editor-help"></span></span></label>
        </div>
        <div class="gp-form-input medium-input">
            <div class="gp-radio-buttons in-flex">
                <div class="gp-radio-button">
                    <input id="menu_view_vertical" type="radio" class="sr-only" name="widget_settings[menu_view]" value="vertical" <?php checked($widgetSettings['menu_view'], "vertical") ?>>
                    <label for="menu_view_vertical"><?php esc_html_e("Vertical", "sticky-chat-widget") ?></label>
                </div>
                <div class="gp-radio-button">
                    <input id="menu_view_horizontal" type="radio" class="sr-only" name="widget_settings[menu_view]" value="horizontal" <?php checked($widgetSettings['menu_view'], "horizontal") ?>>
                    <label for="menu_view_horizontal"><?php esc_html_e("Horizontal", "sticky-chat-widget") ?></label>
                </div>
            </div>
        </div>
    </div>
    <div class="gp-form-field mt-20">
        <div class="gp-form-label">
            <label for="call_to_action"><?php esc_html_e("Call to action:", "sticky-chat-widget") ?></label>
        </div>
        <div class="gp-form-input medium-input">
            <textarea name="widget_settings[call_to_action]" id="ginger_sb_call_to_action"><?php echo esc_attr($widgetSettings['call_to_action']) ?></textarea>
        </div>
    </div>
    <div class="gp-form-field mt-20">
        <div class="gp-form-label">
            <label><?php esc_html_e("Show CTA text: ", "sticky-chat-widget") ?><span class="ginger-info" data-ginger-tooltip="<?php esc_html_e("Choose how the CTA button text would appear. \n All time: always show CTA button text \nUntil first click: hides the CTA button text after the first click.", 'sticky-chat-widget') ?>"><span class="dashicons dashicons-editor-help"></span></span></label>
        </div>
        <div class="gp-form-input medium-input">
            <div class="gp-radio-buttons in-flex">
                <div class="gp-radio-button">
                    <input id="cta_all_time" type="radio" class="sr-only" name="widget_settings[show_cta]" value="all_time" <?php checked($widgetSettings['show_cta'], "all_time") ?>>
                    <label for="cta_all_time"><?php esc_html_e("All time", "sticky-chat-widget") ?></label>
                </div>
                <div class="gp-radio-button">
                    <input id="cta_on_click" type="radio" class="sr-only" name="widget_settings[show_cta]" value="first_click" <?php checked($widgetSettings['show_cta'], "first_click") ?>>
                    <label for="cta_on_click"><?php esc_html_e("Until first click", "sticky-chat-widget") ?></label>
                </div>
            </div>
        </div>
    </div>
    <div class="flex-input mt-20">
        <div class="gp-form-field">
            <div class="gp-form-label">
                <label for="icon-bg-color-custom"><?php esc_html_e("Background color:", "sticky-chat-widget") ?></label>
            </div>
            <div class="gp-form-input ginger-color-list">
                <input id="icon-bg-color-custom" class="custom-color-picker" type="text" name="widget_settings[bg_color]" value="<?php echo esc_attr($widgetSettings['bg_color']) ?>" style="background: <?php echo esc_attr($widgetSettings['bg_color']) ?>">
            </div>
        </div>
        <div class="gp-form-field">
            <div class="gp-form-label">
                <label for="icon-bg-color-interaction-custom"><?php esc_html_e("Background hover color:", "sticky-chat-widget") ?></label>
            </div>
            <div class="gp-form-input ginger-color-list">
                <input id="icon-bg-color-interaction-custom" class="custom-color-picker" type="text" name="widget_settings[interaction_bg_color]" value="<?php echo esc_attr($widgetSettings['interaction_bg_color']) ?>" style="background: <?php echo esc_attr($widgetSettings['interaction_bg_color']) ?>">
            </div>
        </div>
    </div>
    <div class="flex-input mt-20">
        <div class="gp-form-field">
            <div class="gp-form-label">
                <label for="icon-text-color-custom"><?php esc_html_e("Icon color:", "sticky-chat-widget") ?></label>
            </div>
            <div class="gp-form-input ginger-color-list">
                <input id="icon-text-color-custom" class="custom-color-picker" type="text" name="widget_settings[text_color]" value="<?php echo esc_attr($widgetSettings['text_color']) ?>" style="background: <?php echo esc_attr($widgetSettings['text_color']) ?>">
            </div>
        </div>
        <div class="gp-form-field">
            <div class="gp-form-label">
                <label for="icon-text-color-interaction-custom"><?php esc_html_e("Icon hover color:", "sticky-chat-widget") ?></label>
            </div>
            <div class="gp-form-input ginger-color-list">
                <input id="icon-text-color-interaction-custom" class="custom-color-picker" type="text" name="widget_settings[interaction_text_color]" value="<?php echo esc_attr($widgetSettings['interaction_text_color']) ?>" style="background: <?php echo esc_attr($widgetSettings['interaction_text_color']) ?>">
            </div>
        </div>
    </div>
    <div class="gp-form-field mt-20 menu-animation icon-view-field <?php echo ($widgetSettings['view'] == "icon_view") ? "active activate" : "" ?>">
        <div class="gp-form-label">
            <label for="ginger_menu_animation"><?php esc_html_e("Menu animation: ", "sticky-chat-widget") ?><span class="ginger-info" data-ginger-tooltip="<?php esc_html_e("Display social chat button menu using different animations. for preview click on widget button in preview section", 'sticky-chat-widget') ?>"><span class="dashicons dashicons-editor-help"></span></span></label>
        </div>
        <div class="gp-form-input medium-input">
            <select name="widget_settings[menu_animation]" id="ginger_menu_animation" class="sumoselect">
                <?php foreach ($menuAnimations as $key => $value) { ?>
                    <option <?php selected($value['class_name'], $widgetSettings['menu_animation']) ?> value="<?php echo esc_attr($value['class_name']) ?>"><?php echo esc_attr($value['title']) ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="gp-form-field">
        <div class="gp-form-label">
            <label><?php esc_html_e("Attention effect: ", "sticky-chat-widget") ?><span class="ginger-info" data-ginger-tooltip="<?php esc_html_e("Use different kind of attention effects for widget button", 'sticky-chat-widget') ?>"><span class="dashicons dashicons-editor-help"></span></span></label>
        </div>
        <div class="gp-form-input medium-input">
            <select name="widget_settings[animation]" id="ginger_sb_animation" class="sumoselect">
                <?php foreach ($animations as $key => $value) { ?>
                    <option <?php selected($value['class_name'], $widgetSettings['animation']) ?> value="<?php echo esc_attr($value['class_name']) ?>"><?php echo esc_attr($value['title']) ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="gp-form-field mt-20">
        <div class="gp-form-label">
            <label for="widget_icon_size"><?php esc_html_e("Icon size:", "sticky-chat-widget") ?></label>
        </div>
        <div class="gp-form-input add-prefix-text medium-input" data-prefix="PX">
            <input data-label="<?php esc_html_e('Icon size', 'sticky-chat-widget') ?>" type="text" name="widget_settings[icon_size]" id="widget_icon_size" class="only-numeric is-required" value="<?php echo esc_attr($widgetSettings['icon_size']) ?>">
        </div>
    </div>
    <div class="gp-form-field">
        <div class="gp-form-label">
            <label for="border_radius"><?php esc_html_e("Border radius:", "sticky-chat-widget") ?></label>
        </div>
        <div class="gp-form-input add-prefix-text medium-input" data-prefix="PX">
            <input type="text" name="widget_settings[border_radius]" id="border_radius" class="only-numeric" value="<?php echo esc_attr($widgetSettings['border_radius']) ?>">
        </div>
    </div>
    <div class="gp-form-field">
        <div class="gp-form-label">
            <label><?php esc_html_e("Default state:", "sticky-chat-widget") ?></label>
        </div>
        <div class="gp-form-input medium-input">
            <div class="gp-radio-buttons in-flex">
                <div class="gp-radio-button">
                    <input id="default_state_default" type="radio" class="sr-only default-state-option" name="widget_settings[default_state]" value="click" <?php checked($widgetSettings['default_state'], "click") ?>>
                    <label for="default_state_default"><?php esc_html_e("Click to open ", "sticky-chat-widget") ?></label>
                </div>
                <div class="gp-radio-button">
                    <input id="default_state_click" type="radio" class="sr-only default-state-option" name="widget_settings[default_state]" value="open" <?php checked($widgetSettings['default_state'], "open") ?>>
                    <label for="default_state_click"><?php esc_html_e("Open by default ", "sticky-chat-widget") ?><span class="ginger-info" data-ginger-tooltip="<?php esc_html_e("Chat buttons menu will be opened by default when website visitor will visit your website", 'sticky-chat-widget') ?>"><span class="dashicons dashicons-editor-help"></span></span></label>
                </div>
            </div>
        </div>
    </div>
    <div class="default-state <?php echo (esc_attr($widgetSettings['default_state'] == "open" && $widgetSettings['view'] != "corner_circle_view")) ? "active" : "" ?> icon-view-field <?php echo ($widgetSettings['view'] == "icon_view" || $widgetSettings['view'] == "menu_view") ? "activate" : "" ?>">
        <span class="dashboard-switch in-flex on-off">
            <input type="hidden" name="widget_settings[show_close_button]" value="no">
            <input type="checkbox" id="show_close_button" name="widget_settings[show_close_button]" value="yes" class="sr-only" <?php checked($widgetSettings['show_close_button'], "yes") ?>>
            <label for="show_close_button"><?php esc_html_e("Show close button ", "sticky-chat-widget") ?></label><span class="ginger-info" data-ginger-tooltip="<?php esc_html_e("You can show/hide Widget close button when Default state is opened by default", 'sticky-chat-widget') ?>"><span class="dashicons dashicons-editor-help"></span></span>
        </span>
        <span class="dashboard-switch in-flex on-off <?php echo (esc_attr($widgetSettings['show_close_button'] == "yes")) ? "active" : "" ?> hide-menu-close-click">
            <input type="hidden" name="widget_settings[hide_menu_after_close_click]" value="no">
            <input type="checkbox" id="hide_menu_after_close_click" name="widget_settings[hide_menu_after_close_click]" value="yes" class="sr-only" <?php checked($widgetSettings['hide_menu_after_close_click'], "yes") ?>>
            <label for="hide_menu_after_close_click"><?php esc_html_e("Hide menu on close button click ", "sticky-chat-widget") ?></label><span class="ginger-info" data-ginger-tooltip="<?php esc_html_e("Menu will be not opened by default once website visitor will click on widget close button", 'sticky-chat-widget') ?>"><span class="dashicons dashicons-editor-help"></span></span>
        </span>
    </div>
    <div class="gp-form-field mt-20">
        <div class="gp-form-label">
            <label for="ginger_sb_font_family"><?php esc_html_e("Font family:", "sticky-chat-widget") ?></label>
        </div>
        <div class="gp-form-input medium-input">
            <select name="widget_settings[font_family]" id="ginger_sb_font_family" class="sumoselect-font-family">
                <option value=""><?php esc_html_e("Default", "sticky-chat-widget") ?></option>
                <optgroup label="System Font Family">
                    <?php foreach ($systemFonts as $key => $value) { ?>
                        <option <?php selected($widgetSettings['font_family'], $key) ?> value="<?php echo esc_attr($key) ?>"><?php echo esc_attr($key) ?></option>
                    <?php } ?>
                </optgroup>
                <optgroup label="Google Font Family">
                    <?php foreach ($googleFonts as $key => $value) { ?>
                        <option <?php selected($widgetSettings['font_family'], $key) ?> value="<?php echo esc_attr($key) ?>"><?php echo esc_attr($key) ?></option>
                    <?php } ?>
                </optgroup>
            </select>
        </div>
    </div>


    <?php
    require_once dirname(__FILE__)."/analytics-settings.php";
    require_once dirname(__FILE__)."/pending-messages.php";
    require_once dirname(__FILE__)."/tooltip-settings.php";
    require_once dirname(__FILE__)."/custom-css.php";
    ?>

</div>
