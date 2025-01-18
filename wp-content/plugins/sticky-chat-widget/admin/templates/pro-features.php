<?php
/**
 * Pro features popup box of the plugin.
 *
 * @author  : Ginger Plugins <gingerplugins@gmail.com>
 * @license : GPL2
 * */
defined('ABSPATH') or die('Direct Access is not allowed');
?>
<div class="ginger-popup-box premium-features" id="pro-features">
    <div class="ginger-popup-box-bg"></div>
    <div class="ginger-popup-content">
        <div class="ginger-popup-header">
            <i class="fas fa-gem"></i> <?php esc_html_e("Creating multiple widgets is a Pro feature", "sticky-chat-widget") ?>
        </div>
        <div class="ginger-popup-body">
            <div class="pro-feature-box">
                <div class="pro-feature-title"><?php esc_html_e("Create multiple widgets for your website, where you can use it?", "sticky-chat-widget") ?></div>
                <ul>
                    <li><i class="fas fa-check"></i> <?php esc_html_e("Create separate widget for desktop and mobile", "sticky-chat-widget") ?></li>
                    <li><i class="fas fa-check"></i> <?php esc_html_e("Create different size's widget for desktop and mobile", "sticky-chat-widget") ?></li>
                    <li><i class="fas fa-check"></i> <?php esc_html_e("Create different widgets for different languages", "sticky-chat-widget") ?></li>
                    <li><i class="fas fa-check"></i> <?php esc_html_e("Show different widgets based on your availability", "sticky-chat-widget") ?></li>
                    <li><i class="fas fa-check"></i> <?php esc_html_e("Show different widgets on different pages", "sticky-chat-widget") ?></li>
                    <li><i class="fas fa-check"></i> <?php esc_html_e("Hide or Show widgets on selected pages", "sticky-chat-widget") ?></li>
                    <li><i class="fas fa-check"></i> <?php esc_html_e("Show widget based on visitors country", "sticky-chat-widget") ?></li>
                    <li><i class="fas fa-check"></i> <?php esc_html_e("Show widget by scheduling dates and time", "sticky-chat-widget") ?></li>
                </ul>
            </div>
        </div>
        <div class="ginger-popup-footer">
            <a class="btn secondary-btn close-ginger-popup" href="#"><?php esc_html_e("Cancel", "sticky-chat-widget") ?></a>
            <a target="blank" class="btn primary-btn" href="<?php echo esc_url(GP_Admin_Sticky_Chat_Buttons::upgrade_url()) ?>" type="button"><?php esc_html_e("Go Pro", "sticky-chat-widget") ?></a>
        </div>
    </div>
</div>
