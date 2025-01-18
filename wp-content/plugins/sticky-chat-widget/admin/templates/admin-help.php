<?php
/**
 * Help functionality of the plugin.
 *
 * @author  : gingerplugins <gingerplugins@gmail.com>
 * @license : GPL2
 * */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="sticky-help-form">
    <div class="sticky-form-title">
        <?php esc_html_e('Need help?', 'sticky-chat-widget') ?>
        <a class="hide-help-form" href="javascript:;">
            <span class="dashicons dashicons-no"></span>
        </a>
    </div>
    <div class="ginger-help-form">
        <div class="ajax-response"></div>
        <form action="" method="post" id="help_form_new" autocomplete="off">
            <?php
            $userId    = get_current_user_id();
            $userData  = get_user_by("id",  $userId);
            $userEmail = isset($userData->data->user_email) ? $userData->data->user_email : "";
            $name      = isset($userData->data->user_nicename) ? $userData->data->user_nicename : "";
            ?>
            <div class="gp-form-field">
                <div class="gp-form-label">
                    <label for="name"><?php esc_html_e("Name", 'sticky-chat-widget') ?></label>
                </div>
                <div class="gp-form-input">
                    <input data-label="<?php esc_html_e("Name", 'sticky-chat-widget') ?>" type="text" name="name" id="name" class="ginger-form-input is-required" autocomplete="off" value="<?php echo esc_attr($name) ?>" />
                </div>
            </div>
            <div class="gp-form-field">
                <div class="gp-form-label">
                    <label for="email"><?php esc_html_e("Email", 'sticky-chat-widget') ?></label>
                </div>
                <div class="gp-form-input">
                    <input data-label="<?php esc_html_e("Email", 'sticky-chat-widget') ?>" type="text" name="email" id="email" class="ginger-form-input is-required is-email" autocomplete="off" value="<?php echo esc_attr($userEmail) ?>" />
                </div>
            </div>
            <div class="gp-form-field">
                <div class="gp-form-label">
                    <label for="message"><?php esc_html_e("Message", 'sticky-chat-widget') ?></label>
                </div>
                <div class="gp-form-input">
                    <textarea data-label="<?php esc_html_e("Message", 'sticky-chat-widget') ?>" name="message" id="message" class="ginger-form-input is-required" ></textarea>
                </div>
            </div>
            <div class="gp-form-field">
                <button type="submit" class="gp-action-button" ><?php esc_html_e("Send message", 'sticky-chat-widget') ?></button>
                <svg class="ginger-ajax-loader" id="ajax-loader" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="32px" height="32px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                    <circle cx="50" cy="50" r="32" stroke-width="8" stroke="#0a0a0a" stroke-dasharray="50.26548245743669 50.26548245743669" fill="none" stroke-linecap="round" transform="rotate(273.5 50 50)">
                        <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" keyTimes="0;1" values="0 50 50;360 50 50"></animateTransform>
                    </circle>
                </svg>
            </div>
            <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce($this->slug."ajax-contact-form")) ?>" />
            <input type="hidden" name="action" value="<?php echo esc_attr("contact_ginger_form_scw") ?>" />
        </form>
    </div>
</div>
<div class="sticky-help-button">
    <button type="button"><?php esc_html_e("Need help?", 'sticky-chat-widget') ?></button>
</div>
