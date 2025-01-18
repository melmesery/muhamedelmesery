<?php
/**
 * The mail integration functionality of the plugin.
 *
 * @author  : Ginger Plugins <gingerplugins@gmail.com>
 * @license : GPL2
 * */

if (defined('ABSPATH') === false) {
    exit;
}

$formIcons         = Ginger_Social_Icons::svg_icons();
$activation_url    = "";
$install_url       = "";
$plugin            = 'mailpoet/mailpoet.php';
$installed_plugins = get_plugins();
if (isset($installed_plugins[$plugin]) && !is_plugin_active($plugin)) {
    if (! current_user_can('activate_plugins')) {
        return;
    }

    $activation_url = wp_nonce_url('plugins.php?action=activate&amp;plugin='.$plugin.'&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_'.$plugin);
} else if (! class_exists('\MailPoet\API\API')) {
    $install_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=mailpoet'), 'install-plugin_mailpoet');
}
?>

<style>

</style>
<div class="email-integration-container">
<div class="scw-mail-integration <?php echo ((isset($_GET['is_mailchimp_connect']) && $_GET['is_mailchimp_connect'] == 1) || (isset($_GET['is_mailpoet_connect']) && $_GET['is_mailpoet_connect'] == 1) || (isset($_GET['is_captcha_connect']) && $_GET['is_captcha_connect'] == 1)) ? "" : "active" ?>">
    <div class="scw-integration-title">
        <?php esc_html_e("Integrations", "sticky-chat-widget") ?>
        <a class="upgrade-link in-block" href="<?php echo esc_url(GP_Admin_Sticky_Chat_Buttons::upgrade_url()) ?>" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['pro']); ?></a>
    </div>
    <div class="scw-integration-subtitle"><?php esc_html_e("Connect sticky chat widget contact form to other apps"); ?></div>
    <div class="mail-integrations">
        <div class="scw-mailchimp-integration-box is-pro">
            <div class="scw-mailchimp-integration-img">
                <img src="<?php echo esc_url(GSB_PLUGIN_URL.'dist/admin/images/mailchimp.svg') ?>">
            </div>
            <div class="scw-mailchimp-integration-title">
                <?php esc_html_e("Mailchimp", "sticky-chat-widget") ?>
            </div>
            <div class="scw-mailchimp-integration-subtitle">
                <?php esc_html_e("Add and update contacts in your email lists", "sticky-chat-widget") ?>
            </div>
            <div class="scw-integration-button">
                <a href="<?php echo esc_url(admin_url("admin.php?page=sticky-chat-widget-upgrade-to-pro")); ?>" class="new-upgrade-button" target="blank">
                    <?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['pro']); ?>
                </a>
            </div>
        </div>
        <div class="scw-mailpoet-integration-box is-pro">
            <div class="scw-mailpoet-integration-img">
                <img src="<?php echo esc_url(GSB_PLUGIN_URL.'dist/admin/images/mailpoet.svg') ?>">
            </div>
            <div class="scw-mailpoet-integration-title">
                <?php esc_html_e("MailPoet", "sticky-chat-widget") ?>
            </div>
            <div class="scw-mailpoet-integration-subtitle">
                <?php esc_html_e("Add and update contacts in your email lists", "sticky-chat-widget") ?>
            </div>
            <div class="scw-integration-button">
                <a href="<?php echo esc_url(admin_url("admin.php?page=sticky-chat-widget-upgrade-to-pro")); ?>" class="new-upgrade-button" target="blank">
                    <?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['pro']); ?>
                </a>
            </div>
        </div>
        <div class="scw-gcaptcha-integration-box is-pro">
            <div class="scw-mailpoet-integration-img">
                <img src="<?php echo esc_url(GSB_PLUGIN_URL.'dist/admin/images/google-captcha.svg') ?>">
            </div>
            <div class="scw-mailpoet-integration-title">
                <?php esc_html_e("Google reCAPTCHA", "sticky-chat-widget") ?>
            </div>
            <div class="scw-mailpoet-integration-subtitle">
                <?php esc_html_e("Enhance Security with Google reCAPTCHA", "sticky-chat-widget") ?>
            </div>
            <div class="scw-integration-button">
                <a href="<?php echo esc_url(admin_url("admin.php?page=sticky-chat-widget-upgrade-to-pro")); ?>" class="new-upgrade-button" target="blank">
                    <?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['pro']); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="scw-mailchimp-setting <?php echo (isset($_GET['is_mailchimp_connect']) && $_GET['is_mailchimp_connect'] == 1) ? "active" : "" ?>">
    <div class="scw-mailchimp-header">
        <div class="scw-mailchimp-header-left">
            <div class="scw-mailchimp-header-img">
                <img src="<?php echo esc_url(GSB_PLUGIN_URL.'dist/admin/images/mailchimp.svg') ?>">
            </div>
            <div class="scw-mailchimp-title-section">
                <div class="scw-integration-title">
                    <?php esc_html_e("Mailchimp", "sticky-chat-widget") ?>
                    <a class="upgrade-link in-block" href="<?php echo esc_url(GP_Admin_Sticky_Chat_Buttons::upgrade_url()) ?>" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['pro']); ?></a>
                </div>
                <div class="scw-integration-subtitle"><?php esc_html_e("Add and update contacts in your email lists", "sticky-chat-widget") ?></div>
            </div>
        </div>
        <div class="scw-integration-back-btn">
            <svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M6.27915 0.587952L6.27913 0.587975L6.28326 0.592025C6.39707 0.703506 6.3971 0.895698 6.28599 1.00681L1.64964 5.64316L1.29633 5.99647L1.64939 6.35003L6.27936 10.9864L6.2796 10.9866C6.39769 11.1047 6.39769 11.2933 6.2796 11.4114C6.16151 11.5295 5.97289 11.5295 5.8548 11.4114L0.540571 6.09721L0.540208 6.09684C0.527462 6.08413 0.51735 6.06902 0.510451 6.05238C0.503551 6.03575 0.5 6.01792 0.5 5.99991C0.5 5.98191 0.503551 5.96408 0.510451 5.94745C0.51735 5.93081 0.527462 5.9157 0.540208 5.90298L0.540571 5.90262L5.8548 0.588399L5.85524 0.587952C5.88305 0.560072 5.91609 0.537953 5.95246 0.522861C5.98883 0.507769 6.02782 0.5 6.0672 0.5C6.10658 0.5 6.14557 0.507769 6.18194 0.522861C6.21831 0.537953 6.25134 0.560073 6.27915 0.587952Z" fill="#3E4652" stroke="#3E4652"/> </svg>
            <span><?php esc_html_e("Back to List", "sticky-chat-widget") ?></span>
        </div>
    </div>
    <div class="scw-mailchimp-setting-box">
        <div class="scw-mailchimp-first-section mailchimp-not-connected <?php echo empty($scw_mc_api_key) ? "active" : ""; ?>">
            <p><a href="https://mailchimp.com" target="_blank"><?php esc_html_e("Mailchimp", "sticky-chat-widget") ?></a><?php esc_html_e(
                " is a marketing automation platform designed to help small businesses. Integrate Contact form with Mailchimp to automatically send
                new leads, customers, and subscribers to your mailing lists.",
                "sticky-chat-widget"
            ) ?></p>
            <p><?php esc_html_e("Use this integration to", "sticky-chat-widget") ?></p>
            <ul>
                <li><?php esc_html_e("Add new subscribers to your email lists", "sticky-chat-widget") ?></li>
                <li><?php esc_html_e("Update existing subscriber data", "sticky-chat-widget") ?></li>
                <li><?php esc_html_e("Add contacts", "sticky-chat-widget") ?></li>
            </ul>
            <p><?php esc_html_e("Get more email signups to your announcements, newsletters, and more with Contact form’s Mailchimp integration.", "sticky-chat-widget") ?></p>
            <p><?php esc_html_e(
                "Your Mailchimp contacts will automatically be updated with each form submission, so you can spend less time manually transferring information and
                more time growing your business.",
                "sticky-chat-widget"
            ) ?></p>
            <div class="scw-integration-help-link"><?php esc_html_e("Learn: ", "sticky-chat-widget") ?><a href="https://www.gingerplugins.com/knowledge-base/sticky-chat-widget/how-to-create-your-mailchimp-api-key/" target="_blank"><?php esc_html_e("How to create your Mailchimp API key?", "sticky-chat-widget") ?></a></div>
        </div>
        <div class="scw-mailchimp-second-section">
            <div class="scw-mailchimp-auth-title">
                <?php esc_html_e("Authentication", "sticky-chat-widget") ?>
                <a class="upgrade-link in-block" href="<?php echo esc_url(GP_Admin_Sticky_Chat_Buttons::upgrade_url()) ?>" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['pro']); ?></a>
            </div>
            <div class="scw-mailchimp-auth-subtitle"><?php esc_html_e("Authenticate your MailChimp account in order to create an integration.", "sticky-chat-widget") ?></div>
            <div class="scw-mailchimp-auth-form">
                <div class="scw-mailchimp-auth-label">
                    <?php esc_html_e("Enter Mailchimp API Key", "sticky-chat-widget") ?>
                </div>
                <div class="scw-mailchimp-auth-input">
                    <input type="text" id="scw-mc-api-key" name="scw_mc_api_key" value="" style="width: 100%;"  disabled="disabled" >
                    <span class="mailchimp-error-message"><?php esc_html_e("This field is required", "sticky-chat-widget") ?></span>
                </div>
                <div class="scw-mailchimp-auth-btn">
                    <button type="button" class="btn-mailchimp-integration" disabled >Connect</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="scw-mailpoet-setting <?php echo (isset($_GET['is_mailpoet_connect']) && $_GET['is_mailpoet_connect'] == 1) ? "active" : "" ?>">
    <div class="scw-mailchimp-header">
        <div class="scw-mailchimp-header-left">
            <div class="scw-mailchimp-header-img">
                <img src="<?php echo esc_url(GSB_PLUGIN_URL.'dist/admin/images/mailpoet.svg') ?>">
            </div>
            <div class="scw-mailchimp-title-section">
                <div class="scw-integration-title">
                    <?php esc_html_e("MailPoet", "sticky-chat-widget") ?>
                    <a class="upgrade-link in-block" href="<?php echo esc_url(GP_Admin_Sticky_Chat_Buttons::upgrade_url()) ?>" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['pro']); ?></a>
                </div>
                <div class="scw-integration-subtitle"><?php esc_html_e("Add and update contacts in your email lists", "sticky-chat-widget") ?></div>
            </div>
        </div>
        <div class="scw-integration-back-btn">
            <svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M6.27915 0.587952L6.27913 0.587975L6.28326 0.592025C6.39707 0.703506 6.3971 0.895698 6.28599 1.00681L1.64964 5.64316L1.29633 5.99647L1.64939 6.35003L6.27936 10.9864L6.2796 10.9866C6.39769 11.1047 6.39769 11.2933 6.2796 11.4114C6.16151 11.5295 5.97289 11.5295 5.8548 11.4114L0.540571 6.09721L0.540208 6.09684C0.527462 6.08413 0.51735 6.06902 0.510451 6.05238C0.503551 6.03575 0.5 6.01792 0.5 5.99991C0.5 5.98191 0.503551 5.96408 0.510451 5.94745C0.51735 5.93081 0.527462 5.9157 0.540208 5.90298L0.540571 5.90262L5.8548 0.588399L5.85524 0.587952C5.88305 0.560072 5.91609 0.537953 5.95246 0.522861C5.98883 0.507769 6.02782 0.5 6.0672 0.5C6.10658 0.5 6.14557 0.507769 6.18194 0.522861C6.21831 0.537953 6.25134 0.560073 6.27915 0.587952Z" fill="#3E4652" stroke="#3E4652"/> </svg>
            <span><?php esc_html_e("Back to List", "sticky-chat-widget") ?></span>
        </div>
    </div>
    <div class="scw-mailchimp-setting-box">
        <div class="scw-mailchimp-first-section mailpoet-not-connected <?php echo empty($scw_mailpoet_connect) ? "active" : ""; ?>">
            <p><a href="https://www.mailpoet.com/" target="_blank"><?php esc_html_e("Mailpoet", "sticky-chat-widget") ?></a><?php esc_html_e(
                " is a marketing automation platform designed to help small businesses. Integrate Contact form with Mailpoet to automatically send
                new leads, customers, and subscribers to your mailing lists.",
                "sticky-chat-widget"
            ) ?></p>
            <p><?php esc_html_e("Use this integration to", "sticky-chat-widget") ?></p>
            <ul>
                <li><?php esc_html_e("Add new subscribers to your email lists", "sticky-chat-widget") ?></li>
                <li><?php esc_html_e("Update existing subscriber data", "sticky-chat-widget") ?></li>
                <li><?php esc_html_e("Add contacts", "sticky-chat-widget") ?></li>
            </ul>
            <p><?php esc_html_e("Get more email signups to your announcements, newsletters, and more with Contact form’s Mailpoet integration.", "sticky-chat-widget") ?></p>
            <p><?php esc_html_e(
                "Your Mailpoet contacts will automatically be updated with each form submission, so you can spend less time manually transferring information and
                more time growing your business.",
                "sticky-chat-widget"
            ) ?></p>
            <div class="scw-integration-help-link"><?php esc_html_e("Learn: ", "sticky-chat-widget") ?><a href="https://www.gingerplugins.com/knowledge-base/sticky-chat-widget/how-to-connect-sticky-chat-widget-form-with-mailpoet/" target="_blank"><?php esc_html_e("How to connect with Mailpoet?", "sticky-chat-widget") ?></a></div>
        </div>
        <div class="scw-mailchimp-second-section">
            <div class="scw-mailchimp-auth-title pb-5">
                <?php esc_html_e("Connect your forms to MailPoet", "sticky-chat-widget") ?>
                <a class="upgrade-link in-block" href="<?php echo esc_url(GP_Admin_Sticky_Chat_Buttons::upgrade_url()) ?>" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['pro']); ?></a>
            </div>
            <div class="scw-mailchimp-auth-subtitle mailpoet-installation <?php echo !empty($install_url) ? "active" : ""; ?>"><?php esc_html_e("Install MailPoet plugin to connect your forms", "sticky-chat-widget") ?></div>
            <div class="scw-mailchimp-auth-subtitle mailpoet-activation <?php echo !empty($activation_url) ? "active" : ""; ?>"><?php esc_html_e("Activate MailPoet plugin to connect your forms", "sticky-chat-widget") ?></div>
            <div class="scw-mailchimp-auth-subtitle mailpoet-connect <?php echo (empty($install_url) && empty($activation_url) && empty($scw_mailpoet_connect)) ? "active" : ""; ?>"><?php esc_html_e("Connect sticky chat widget with MailPoet", "sticky-chat-widget") ?></div>
            <div class="scw-mailchimp-auth-form">
                <div class="scw-mailchimp-auth-btn scw-mailpoet-btn">
                    <?php
                    if (!empty($install_url)) {
                        ?>
                            <a href="#"><?php esc_html_e("Install MailPoet", "sticky-chat-widget") ?></a>
                        <?php
                    } else if (!empty($activation_url)) {
                        ?>
                            <a href="#"><?php esc_html_e("Activate MailPoet", "sticky-chat-widget") ?></a>
                        <?php
                    } else {
                        ?>
                            <button type="button" class="btn-mailpoet-connect" disabled>Connect</button>
                        <?php
                    }

                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="scw-gcaptcha-setting <?php echo (isset($_GET['is_captcha_connect']) && $_GET['is_captcha_connect'] == 1) ? "active" : "" ?>">
    <div class="scw-mailchimp-header">
        <div class="scw-mailchimp-header-left">
            <div class="scw-mailchimp-header-img">
                <img src="<?php echo esc_url(GSB_PLUGIN_URL.'dist/admin/images/google-captcha.svg') ?>">
            </div>
            <div class="scw-mailchimp-title-section">
                <div class="scw-integration-title">
                    <?php esc_html_e("Google reCAPTCHA", "sticky-chat-widget") ?>
                    <a class="upgrade-link in-block" href="<?php echo esc_url(GP_Admin_Sticky_Chat_Buttons::upgrade_url()) ?>" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['pro']); ?></a>
                </div>
                <div class="scw-integration-subtitle"><?php esc_html_e("Enhance Security with Google reCAPTCHA", "sticky-chat-widget") ?></div>
            </div>
        </div>
        <div class="scw-integration-back-btn">
            <svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M6.27915 0.587952L6.27913 0.587975L6.28326 0.592025C6.39707 0.703506 6.3971 0.895698 6.28599 1.00681L1.64964 5.64316L1.29633 5.99647L1.64939 6.35003L6.27936 10.9864L6.2796 10.9866C6.39769 11.1047 6.39769 11.2933 6.2796 11.4114C6.16151 11.5295 5.97289 11.5295 5.8548 11.4114L0.540571 6.09721L0.540208 6.09684C0.527462 6.08413 0.51735 6.06902 0.510451 6.05238C0.503551 6.03575 0.5 6.01792 0.5 5.99991C0.5 5.98191 0.503551 5.96408 0.510451 5.94745C0.51735 5.93081 0.527462 5.9157 0.540208 5.90298L0.540571 5.90262L5.8548 0.588399L5.85524 0.587952C5.88305 0.560072 5.91609 0.537953 5.95246 0.522861C5.98883 0.507769 6.02782 0.5 6.0672 0.5C6.10658 0.5 6.14557 0.507769 6.18194 0.522861C6.21831 0.537953 6.25134 0.560073 6.27915 0.587952Z" fill="#3E4652" stroke="#3E4652"/> </svg>
            <span><?php esc_html_e("Back to List", "sticky-chat-widget") ?></span>
        </div>
    </div>
    <div class="scw-mailchimp-setting-box">
        <div class="scw-mailchimp-first-section">
            <p><?php esc_html_e("Protect your form from spam and automated abuse by integrating Google reCAPTCHA. By enabling this feature, you ensure that only legitimate users can submit the form, enhancing the overall security of your website. Google reCAPTCHA helps distinguish between humans and bots, providing an additional layer of defense against malicious activities.", "sticky-chat-widget") ?></p>
            <p><?php esc_html_e("Follow the instructions below to set up and configure Google reCAPTCHA for your form.", "sticky-chat-widget") ?></p>
            <div class="scw-integration-help-link"><?php esc_html_e("Learn: ", "sticky-chat-widget") ?><a href="https://cloud.google.com/recaptcha-enterprise/docs/create-key-website#create-key" target="_blank"><?php esc_html_e("How to get Google reCAPTCHA API key?", "sticky-chat-widget") ?></a></div>
        </div>
        <div class="scw-mailchimp-second-section">
            <div class="scw-mailchimp-auth-form" style="margin-top: 10px;">
                <div class="scw-mailchimp-auth-field">
                    <div class="scw-mailchimp-auth-label">
                        <label for="enable_captcha"><?php esc_html_e("Enable reCAPTCHA", "sticky-chat-widget"); ?></label>
                    </div>
                    <div class="scw-mailchimp-auth-input">
                        <span class="dashboard-switch in-flex">
                            <input type="hidden" class="enable-captcha" name="captcha_settings[enable_captcha]" value="no">
                            <input type="checkbox" disabled id="enable_captcha" name="captcha_settings[enable_captcha]" value="yes" class="sr-only enable-captcha">
                            <label for="enable_captcha">
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
                <div class="scw-mailchimp-auth-field">
                    <div class="scw-mailchimp-auth-label">
                        <?php esc_html_e("reCAPTCHA type", "sticky-chat-widget"); ?>
                    </div>
                    <div class="scw-mailchimp-auth-input">
                        <div class="gp-radio-buttons in-flex">
                            <div class="gp-radio-button">
                                <input id="recaptcha_v3" type="radio" disabled class="sr-only captcha-type" name="captcha_settings[captcha_type]" value="recaptcha_v3" checked>
                                <label for="recaptcha_v3">reCAPTCHA v3</label>
                            </div>
                            <div class="gp-radio-button">
                                <input id="recaptcha_v2" type="radio" disabled class="sr-only captcha-type" name="captcha_settings[captcha_type]" value="recaptcha_v2">
                                <label for="recaptcha_v2">reCAPTCHA v2</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="v2-captcha-setting">
                    <div class="scw-mailchimp-auth-field">
                        <div class="scw-mailchimp-auth-label">
                            <?php esc_html_e("Site Key", "sticky-chat-widget") ?>
                        </div>
                        <div class="scw-mailchimp-auth-input">
                            <input type="text" id="v2_site_key" name="captcha_settings[v2_site_key]" value="" disabled style="width: 100%;">
                        </div>
                    </div>
                    <div class="scw-mailchimp-auth-field">
                        <div class="scw-mailchimp-auth-label">
                            <?php esc_html_e("Secret Key", "sticky-chat-widget") ?>
                        </div>
                        <div class="scw-mailchimp-auth-input">
                            <input type="text" id="v2_secret_key" name="captcha_settings[v2_secret_key]" value="" disabled style="width: 100%;">
                        </div>
                    </div>
                </div>
                <div class="v3-captcha-setting active">
                    <div class="scw-mailchimp-auth-field">
                        <div class="scw-mailchimp-auth-label">
                            <?php esc_html_e("Site Key", "sticky-chat-widget") ?>
                        </div>
                        <div class="scw-mailchimp-auth-input">
                            <input type="text" id="v3_site_key" name="captcha_settings[v3_site_key]" value="" disabled style="width: 100%;">
                        </div>
                    </div>
                    <div class="scw-mailchimp-auth-field">
                        <div class="scw-mailchimp-auth-label">
                            <?php esc_html_e("Secret Key", "sticky-chat-widget") ?>
                        </div>
                        <div class="scw-mailchimp-auth-input">
                            <input type="text" id="v3_secret_key" name="captcha_settings[v3_secret_key]" value="" disabled style="width: 100%;">
                        </div>
                    </div>
                </div>
                <div class="scw-mailchimp-auth-field">
                    <div class="scw-mailchimp-auth-label">

                    </div>
                    <div class="scw-mailchimp-auth-input">
                        <div class="scw-mailchimp-auth-btn">
                            <button type="button" class="btn-gcaptcha-integration">
                                <?php esc_html_e("Save Changes", "sticky-chat-widget") ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <a href="<?php echo esc_url(GP_Admin_Sticky_Chat_Buttons::upgrade_url()) ?>" target="_blank" class="upgrade-link-btn"><?php esc_html_e("Upgrade to Pro", "sticky-chat-widget") ?></a>
</div>

<script type="text/javascript">
    ( function( $ ) {
        "use strict";
        $(document).ready(function(){

            $(document).on("click","a[href='#']",function(e) {
                e.preventDefault();
            })

            $(document).on("click", ".scw-mailchimp-integration-box.is-pro", function (){
                $(".scw-mail-integration").removeClass("active");
                $(".scw-mailchimp-setting").addClass("active");
                $(".upgrade-link-btn").addClass("active");
            });

            $(document).on("click", ".scw-mailpoet-integration-box.is-pro", function (){
                $(".scw-mail-integration").removeClass("active");
                $(".scw-mailpoet-setting").addClass("active");
                $(".upgrade-link-btn").addClass("active");
            });

            $(document).on("click", ".scw-gcaptcha-integration-box.is-pro", function (){
                $(".scw-mail-integration").removeClass("active");
                $(".scw-gcaptcha-setting").addClass("active");
                $(".upgrade-link-btn").addClass("active");
            });

            $(document).on("click", ".scw-integration-back-btn", function (){
                $(".scw-mail-integration").addClass("active");
                $(".upgrade-link-btn").removeClass("active");
                $(".scw-mailchimp-setting").removeClass("active");
                $(".scw-mailpoet-setting").removeClass("active");
                $(".scw-gcaptcha-setting").removeClass("active");
            });

            $(document).on("click",".scw-integration-button",function(e) {
                e.stopPropagation();
            })
        });
    })( jQuery );
</script>
