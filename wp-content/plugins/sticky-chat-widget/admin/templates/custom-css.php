<?php
/**
 * Custom css for widget functionality of the plugin.
 *
 * @author  : Ginger Plugins <gingerplugins@gmail.com>
 * @license : GPL2
 * */
defined('ABSPATH') or die('Direct Access is not allowed');
?>

<?php
$inputValue = get_post_meta($postId, "button_css", true);
$inputValue = isset($inputValue) ? $inputValue : "";
?>

<div class="setting-sub-title mt-36"><?php esc_html_e("Custom CSS", "sticky-chat-widget") ?></div>
<div class="gp-form-field">
    <div class="gp-form-label d-flex">
        <label for="css_editor"><?php esc_html_e("Button CSS:", "sticky-chat-widget") ?></label>
        <?php if (!empty($disabled)) { ?>
            <a class="upgrade-link in-block" href="javascript:;" target="_blank" style="margin-bottom: 5px"><?php Ginger_Social_Icons::load_and_sanitize_svg($icons['pro']); ?></a>
        <?php } ?>
    </div>
    <div class="gp-form-input">
        <textarea <?php echo esc_attr($disabled) ?> name="gsb_button_css" class="custom-css" rows="5"></textarea>
    </div>
</div>
