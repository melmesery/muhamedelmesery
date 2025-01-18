<?php
/**
 * Deactivate plugin functionality of the plugin.
 *
 * @author  : Ginger Plugins <gingerplugins@gmail.com>
 * @license : GPL2
 * */
defined('ABSPATH') or die('Direct Access is not allowed');
?>
<?php $pluginSlug = $this->slug ; ?>
<div class="ginger-deactivate-popup" tabindex="-1" id="<?php echo esc_attr($pluginSlug) ?>-popup-form">
    <div class="ginger-popup-form-overlay"></div>
    <div class="ginger-popup-form">
        <form action="<?php echo esc_url(admin_url("admin-ajax.php")) ?>" method="post" id="<?php echo esc_attr($pluginSlug) ?>-deactivate-form">
            <div class="gp-popup-form-header">
                <?php esc_html_e('Quick feedback', "sticky-chat-widget"); ?>
            </div>
            <div class="gp-popup-form-body">
                <div class="gp-popup-message"><?php esc_html_e('If you have a moment, please share why you are deactivating Sticky chat Widget. Your feedback will help us to improve our product', "sticky-chat-widget"); ?></div>
                <div class="gp-popup-form-control">
                    <label for="email-<?php echo esc_attr($pluginSlug) ?>"><?php esc_html_e("Email address", "sticky-chat-widget") ?></label>
                    <input id="email-<?php echo esc_attr($pluginSlug) ?>" type="email" name="deactivate_email" value="<?php echo esc_attr(get_option('admin_email')) ?>" placeholder="<?php esc_html_e("Email address", "sticky-chat-widget") ?>" >
                </div>
                <div class="gp-popup-form-control">
                    <label for="deactivate_comment-<?php echo esc_attr($pluginSlug) ?>"><?php esc_html_e("Your comment", "sticky-chat-widget") ?></label>
                    <textarea rows="5" id="deactivate_comment-<?php echo esc_attr($pluginSlug) ?>" name="deactivate_comment"><?php esc_html_e('I need help with Sticky Chat Widget', "sticky-chat-widget"); ?></textarea>
                </div>
            </div>
            <div class="gp-popup-form-footer">
                <input type="submit" class="button button-secondary gp-deactivate-button <?php echo esc_attr($pluginSlug) ?>-popup-submit" value="<?php esc_html_e("Submit & Deactivate", "sticky-chat-widget") ?>">
                <span class="gp-popup-loader <?php echo esc_attr($pluginSlug) ?>-loader"></span>
                <div class="gp-popup-action-buttons">
                    <input type="button" class="button button-secondary gp-skip-button <?php echo esc_attr($pluginSlug) ?>-skip-feedback" value="<?php esc_html_e("Skip & Deactivate", "sticky-chat-widget") ?>">
                    <a href="javascript:;" class="button button-primary <?php echo esc_attr($pluginSlug) ?>-close-button"><?php esc_html_e('Cancel', "sticky-chat-widget"); ?></a>
                </div>
            </div>
            <input type="hidden" name="deactivate_nonce" value="<?php echo esc_attr(wp_create_nonce($pluginSlug."-deactivate-plugin")) ?>">
            <input type="hidden" name="action" value="<?php echo esc_attr($pluginSlug."-plugin_deactivate_form") ?>">
        </form>
    </div>
</div>
