<?php
/**
 * Premium features popup box of the plugin.
 *
 * @author  : Ginger Plugins <gingerplugins@gmail.com>
 * @license : GPL2
 * */
defined('ABSPATH') or die('Direct Access is not allowed');
?>
<div class="ginger-popup-box premium-features" id="premium-features">
    <div class="ginger-popup-box-bg"></div>
    <div class="ginger-popup-content">
        <div class="ginger-popup-header">
            <i class="fas fa-gem"></i> <?php esc_html_e("Premium Features", "sticky-chat-widget") ?>
        </div>
        <div class="ginger-popup-body">
            <div class="pro-feature-box">
                <div class="pro-feature-title"><?php esc_html_e("What you get as a Pro user:", "sticky-chat-widget") ?></div>
                <ul>
                    <li><i class="fas fa-check"></i> <?php esc_html_e("Create multiple widgets for different pages and languages", "sticky-chat-widget") ?></li>
                    <li><i class="fas fa-check"></i> <?php esc_html_e("Upload custom images for Widget button and Social channel buttons", "sticky-chat-widget") ?></li>
                    <li><i class="fas fa-check"></i> <?php esc_html_e("Choose custom color for widget button and tooltip", "sticky-chat-widget") ?></li>
                    <li><i class="fas fa-check"></i> <?php esc_html_e("Show Sticky Chat Widget using the advanced time schedules", "sticky-chat-widget") ?></li>
                    <li><i class="fas fa-check"></i> <?php esc_html_e("Show Sticky Chat Widget on specific pages using advanced page rules", "sticky-chat-widget") ?></li>
                    <li><i class="fas fa-check"></i> <?php esc_html_e("Track events on google analytics", "sticky-chat-widget") ?></li>
                    <li><i class="fas fa-check"></i> <?php esc_html_e("Set custom IDs and CSS class names", "sticky-chat-widget") ?></li>
                    <li><i class="fas fa-check"></i> <?php esc_html_e("Add your own CSS to Sticky Chat Widget", "sticky-chat-widget") ?></li>
                    <li><i class="fas fa-check"></i> <?php esc_html_e("Show widget based on visitors country", "sticky-chat-widget") ?></li>
                    <li><i class="fas fa-check"></i> <?php esc_html_e("Show widget by scheduling dates and time", "sticky-chat-widget") ?></li>
                    <li><i class="fas fa-check"></i> <?php esc_html_e("Add custom fields like text, phone, textarea, select, date etc in contact form", "sticky-chat-widget") ?></li>
                    <li><i class="fas fa-check"></i> <?php esc_html_e("Integrate contact form leads with marketing platform like Mailchimp, Mailpoet", "sticky-chat-widget") ?></li>

                </ul>
            </div>
        </div>
        <div class="ginger-popup-footer">
            <a class="btn secondary-btn close-ginger-popup" href="#"><?php esc_html_e("Cancel", "sticky-chat-widget") ?></a>
            <a target="_blank" class="btn primary-btn" href="<?php echo esc_url(GP_Admin_Sticky_Chat_Buttons::upgrade_url()) ?>" type="button"><?php esc_html_e("Go Pro", "sticky-chat-widget") ?></a>
        </div>
    </div>
</div>
