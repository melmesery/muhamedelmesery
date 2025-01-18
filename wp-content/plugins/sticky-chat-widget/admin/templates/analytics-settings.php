<?php
/**
 * Google analytics for widget functionality of the plugin.
 *
 * @author  : Ginger Plugins <gingerplugins@gmail.com>
 * @license : GPL2
 * */
defined('ABSPATH') or die('Direct Access is not allowed');
?>

<?php
$inputValue = get_post_meta($postId, "google_analytics", true);
$inputValue = empty($inputValue) ? "no" : $inputValue;
?>

<div class="setting-sub-title mt-36"><?php esc_html_e("Analytics Settings", "sticky-chat-widget") ?></div>
<div class="gp-form-field google-analytics">
    <div class="gp-form-label">
    </div>
    <div class="gp-form-input d-flex">
        <span class="dashboard-switch in-flex on-off">
            <input type="hidden" name="gsb_google_analytics" value="no">
            <input type="checkbox" id="gsb_google_analytics" name="gsb_google_analytics" <?php echo esc_attr($disabled) ?> value="yes" class="sr-only" <?php checked($inputValue, "yes") ?>>
            <label for="gsb_google_analytics"><?php esc_html_e("Google analytics", "sticky-chat-widget") ?></label>
            <?php if (!empty($disabled)) { ?>
                <a class="upgrade-link in-block" href="javascript:;" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($icons['pro']); ?></a>
            <?php } ?>
        </span>
    </div>
</div>

<div class="gp-form-field widget-analytics">
    <div class="gp-form-label">
    </div>
    <div class="gp-form-input d-flex">
        <span class="dashboard-switch in-flex on-off">
            <input type="hidden" name="widget_settings[widget_analytics]" value="no">
            <input type="checkbox" id="gsb_widget_analytics" name="widget_settings[widget_analytics]" <?php echo esc_attr($disabled) ?> value="yes" class="sr-only" <?php checked($widgetSettings['widget_analytics'], "yes") ?>>
            <label for="gsb_widget_analytics"><?php esc_html_e("Widget analytics", "sticky-chat-widget") ?></label>
            <?php if (!empty($disabled)) { ?>
                <a class="upgrade-link in-block" href="javascript:;" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($icons['pro']); ?></a>
            <?php } ?>
        </span>
        <a href="<?php echo esc_url(admin_url("admin.php?page=sticky-chat-widget-analytics")) ?>" target="_blank" class="view-widget-analytics">View widget analytics</a>
    </div>
</div>
