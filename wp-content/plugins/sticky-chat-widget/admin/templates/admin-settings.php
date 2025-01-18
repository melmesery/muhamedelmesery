<?php
/**
 * The widget setting functionality of the plugin.
 *
 * @author  : Ginger Plugins <gingerplugins@gmail.com>
 * @license : GPL2
 * */

defined('ABSPATH') or die('Direct Access is not allowed');

$getSelectedChannels = get_post_meta($postId, "selected_channels", true);
$widgetStatus        = get_post_meta($postId, "widget_status", true);
$widgetStatus        = isset($widgetStatus) && !empty($widgetStatus) ? $widgetStatus : "yes";
$icons = Ginger_Social_Icons::svg_icons();
?>
<div style="display: none">
    <?php
    $embedded_message = "";
    $settings         = [
        'media_buttons'    => false,
        'wpautop'          => false,
        'drag_drop_upload' => false,
        'textarea_name'    => 'chat_editor_channel',
        'textarea_rows'    => 4,
        'quicktags'        => false,
        'tinymce'          => [
            'toolbar1' => 'bold, italic, underline',
            'toolbar2' => '',
            'toolbar3' => '',
        ],
    ];
    wp_editor($embedded_message, "chat_editor_channel", $settings);
    ?>
</div>

    <form action="<?php echo esc_url(admin_url("admin-ajax.php")) ?>" method="post" id="ginger_sb_form" autocomplete="off">
        <div class="widget-settings">
            <div class="widget-sidebar">
                <ul>
                    <li><a class="active" href="#channel-settings"><?php esc_html_e("Select Channels", "sticky-chat-widget") ?></a></li>
                    <li><a href="#icon-settings"><?php esc_html_e("Customize Widget", "sticky-chat-widget") ?></a></li>
                    <li><a href="#triggers-settings"><?php esc_html_e("Triggers", "sticky-chat-widget") ?></a></li>
                    <li><a href="#targeting-settings"><?php esc_html_e("Targeting", "sticky-chat-widget") ?></a></li>
                </ul>
            </div>
            <div class="widget-setting">
                <?php
                require_once dirname(__FILE__)."/social-channels.php";
                require_once dirname(__FILE__)."/customize-widget-button.php";
                require_once dirname(__FILE__)."/triggers.php";
                require_once dirname(__FILE__)."/time-and-page-rules.php";
                ?>
                <div class="widget-footer text-center">
                    <button type="button" class="gp-action-button back-button back-next-btn"><?php esc_html_e("Back", "sticky-chat-widget") ?></button>
                    <button type="button" class="gp-action-button next-button back-next-btn active"><?php esc_html_e("Next", "sticky-chat-widget"); ?></button>
                    <div class="widget-save-btn">
                        <button class="gp-action-button main-save-btn save-changes submitButton preview-button" type="submit" data-attr="preview-button">
                            <span class="btn-text"><?php esc_html_e('Save Changes', 'sticky-chat-widget') ?></span>
                            <span class="more-save-option"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M6 9L12 15L18 9" stroke="white" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"/> </svg></span>
                        </button>
                        <button type="submit" class="save-view-btn"><?php esc_html_e("Save and View Dashboard", "sticky-chat-widget") ?></button>
                    </div>
                    <span class="scw-loader"><span class="dashicons dashicons-update"></span></span>
                </div>
            </div>
            <input type="hidden" name="gsb_selected_channels" id="gsb_selected_channels" value="<?php echo esc_attr($getSelectedChannels); ?>" />
            <input type="hidden" name="action" value="save_gsb_buttons_setting" />
            <input type="hidden" id="button_setting_nonce" name="nonce" value="<?php echo esc_attr(wp_create_nonce("save_gsb_buttons_setting".esc_attr($postId))) ?>" />
            <input type="hidden" id="button_setting_id" name="setting_id" value="<?php echo esc_attr($postId) ?>" />
            <input type="hidden" id="check_widget_status" name="widget_status" value="<?php echo esc_attr($widgetStatus) ?>">
            <input type="hidden" id="save_btn_type" name="save_btn_type" value="save-btn">

            <div class="widget-preview">
                <?php require_once dirname(__FILE__)."/widget-preview.php"; ?>
            </div>

            <!-- Inline CSS -->
            <div class="inline-style"></div>
        </div>
        <div class="form-confirmation gp-modal" tabindex="-1">
            <div class="gp-modal-bg"></div>
            <div class="gp-modal-container small">
                <div class="gp-modal-content">
                    <div class="gp-modal-data">
                        <div class="gp-modal-header">
                            <?php esc_html_e("Sticky Chat Widget is disabled", 'sticky-chat-widget') ?>
                        </div>
                        <div class="gp-modal-body">
                            <p><?php esc_html_e("Sticky Chat Widget is currently disabled.", 'sticky-chat-widget') ?></p>
                            <p><?php esc_html_e("Would you like to show it on your website?", 'sticky-chat-widget') ?></p>
                        </div>
                        <div class="gp-modal-footer text-center">
                            <button type="button" class="primary-btn save-confirm-btn"><?php esc_html_e("Yes, enable and save it", "sticky-chat-widget"); ?></button>
                            <button type="button" class="secondary-btn hide-gp-modal no-confirm-btn"><?php esc_html_e("No, just save changes", "sticky-chat-widget"); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="sticky-preview-button">
        <button type="button" class="preview-btn"><?php esc_html_e("Preview", "sticky-chat-widget") ?></button>
    </div>
<?php require_once dirname(__FILE__)."/premium-features.php"; ?>
<?php require_once dirname(__FILE__)."/common.php";
