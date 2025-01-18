<?php
/**
 * Tooltip setting for widget functionality of the plugin.
 *
 * @author  : Ginger Plugins <gingerplugins@gmail.com>
 * @license : GPL2
 * */
defined('ABSPATH') or die('Direct Access is not allowed');

$defaultTooltipSettings = Ginger_Social_Icons::get_tooltip_setting();
$tooltipSettings        = get_post_meta($postId, "tooltip_settings", true);
$tooltipSettings        = shortcode_atts($defaultTooltipSettings, $tooltipSettings);
?>

<div class="setting-sub-title mt-36"><?php esc_html_e("Customize on Hover Text", "sticky-chat-widget") ?></div>
<div class="tooltip-setting-box">
    <div class="tooltip-setting">
        <div class="gp-form-field">
            <div class="gp-form-label">
                <label for="tooltip_font_size"><?php esc_html_e("Font size:", "sticky-chat-widget") ?></label>
            </div>
            <div class="gp-form-input add-prefix-text medium-input" data-prefix="PX">
                <input type="text" class="only-numeric" name="tooltip_settings[font_size]" id="tooltip_font_size" value="<?php echo esc_attr($tooltipSettings['font_size']) ?>">
            </div>
        </div>
        <div class="gp-form-field">
            <div class="gp-form-label">
                <label for="tooltip_height"><?php esc_html_e("Tooltip height:", "sticky-chat-widget") ?></label>
            </div>
            <div class="gp-form-input add-prefix-text medium-input" data-prefix="PX">
                <input type="text" class="only-numeric" name="tooltip_settings[tooltip_height]" id="tooltip_height" value="<?php echo esc_attr($tooltipSettings['tooltip_height']) ?>">
            </div>
        </div>
        <div class="gp-form-field">
            <div class="gp-form-label">
                <label for="tooltip_border_radius"><?php esc_html_e("Border radius:", "sticky-chat-widget") ?></label>
            </div>
            <div class="gp-form-input add-prefix-text medium-input" data-prefix="PX">
                <input type="text" class="only-numeric" name="tooltip_settings[border_radius]" id="tooltip_border_radius" value="<?php echo esc_attr($tooltipSettings['border_radius']) ?>">
            </div>
        </div>
    </div>
    <div class="gp-form-field in-flex">
        <div class="gp-form-label">
            <label for="tooltip-bg-color-custom"><?php esc_html_e("Background color:", "sticky-chat-widget") ?></label>
        </div>
        <div class="gp-form-input ginger-color-list">
            <input id="tooltip-bg-color-custom" class="custom-color-picker" type="text" name="tooltip_settings[bg_color]" value="<?php echo esc_attr($tooltipSettings['bg_color']) ?>" style="background: <?php echo esc_attr($tooltipSettings['bg_color']) ?>">
        </div>
    </div>
    <div class="gp-form-field in-flex">
        <div class="gp-form-label">
            <label for="tooltip-text-color-custom"><?php esc_html_e("Text color:", "sticky-chat-widget") ?></label>
        </div>
        <div class="gp-form-input ginger-color-list">
            <input id="tooltip-text-color-custom" class="custom-color-picker" type="text" name="tooltip_settings[text_color]" value="<?php echo esc_attr($tooltipSettings['text_color']) ?>" style="background: <?php echo esc_attr($tooltipSettings['text_color']) ?>">
        </div>
    </div>
</div>
