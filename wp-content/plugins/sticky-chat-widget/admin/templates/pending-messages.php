<?php
/**
 * Pending messages setting functionality of the plugin.
 *
 * @author  : Ginger Plugins <gingerplugins@gmail.com>
 * @license : GPL2
 * */
defined('ABSPATH') or die('Direct Access is not allowed');
?>
<?php
$defaultPendingMessageSettings = Ginger_Social_Icons::get_customize_widget_setting();
$pendingMessageSettings        = get_post_meta($postId, "widget_settings", true);
$pendingMessageSettings        = shortcode_atts($defaultPendingMessageSettings, $pendingMessageSettings);
?>
<div class="setting-sub-title mt-36"><?php esc_html_e("Pending Messages", "sticky-chat-widget") ?></div>
<div class="gp-form-field">
    <span class="dashboard-switch in-flex on-off">
        <input type="hidden" name="widget_settings[has_pending_message]" value="no">
        <input type="checkbox" id="has_pending_message" name="widget_settings[has_pending_message]" value="yes" class="sr-only has-pending-message" <?php checked($pendingMessageSettings['has_pending_message'], "yes") ?>>
        <label for="has_pending_message"><?php esc_html_e("Show pending message ", "sticky-chat-widget") ?><span aria-hidden="true" class="ginger-info" data-ginger-tooltip="<?php esc_html_e("Increase your click rate by displaying a pending messages (bubble) on widget button", 'sticky-chat-widget') ?>"><span class="dashicons dashicons-editor-help"></span></span></label>
    </span>
</div>
<div class="pending-message-setting <?php echo ($pendingMessageSettings['has_pending_message'] == "yes") ? "active" : "" ?>">
    <div class="gp-form-field">
        <div class="gp-form-label">
            <label for="no_of_messages"><?php esc_html_e("Number of messages:", "sticky-chat-widget") ?></label>
        </div>
        <div class="gp-form-input medium-input">
            <input type="text" name="widget_settings[no_of_messages]" id="no_of_messages" class="only-numeric <?php echo ($pendingMessageSettings['has_pending_message'] == "yes") ? "is-required" : "" ?>" value="<?php echo esc_attr($pendingMessageSettings['no_of_messages']) ?>" data-label="No of messages">
        </div>
    </div>
    <div class="gp-form-field in-flex">
        <div class="gp-form-label">
            <label for="message-bg-color-custom"><?php esc_html_e("Background color:", "sticky-chat-widget") ?></label>
        </div>
        <div class="gp-form-input ginger-color-list">
            <input id="message-bg-color-custom" class="custom-color-picker" type="text" name="widget_settings[message_bg_color]" value="<?php echo esc_attr($pendingMessageSettings['message_bg_color']) ?>" style="background: <?php echo esc_attr($pendingMessageSettings['message_bg_color']) ?>">
        </div>
    </div>
    <div class="gp-form-field in-flex">
        <div class="gp-form-label">
            <label for="message-text-color-custom"><?php esc_html_e("Text color:", "sticky-chat-widget") ?></label>
        </div>
        <div class="gp-form-input ginger-color-list">
            <input id="message-text-color-custom" class="custom-color-picker" type="text" name="widget_settings[message_text_color]" value="<?php echo esc_attr($pendingMessageSettings['message_text_color']) ?>" style="background: <?php echo esc_attr($pendingMessageSettings['message_text_color']) ?>">
        </div>
    </div>
</div>
