<?php
/**
 * Popup box functionality of the plugin.
 *
 * @author  : Ginger Plugins <gingerplugins@gmail.com>
 * @license : GPL2
 * */
defined('ABSPATH') or die('Direct Access is not allowed');
?>
<div class="gp-modal" id="create-widget">
    <div class="gp-modal-bg"></div>
    <div class="gp-modal-container small">
        <div class="gp-modal-content">
            <div class="gp-modal-data">
                <button class="gp-modal-close-btn">
                    <span class="svg-icon">
                        <?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['close']); ?>
                    </span>
                </button>
                <div class="gp-modal-header">
                    <?php esc_html_e("Create Widget", "sticky-chat-widget"); ?>
                </div>
                <div class="gp-modal-body">
                    <div class="gp-form-field">
                        <div class="gp-form-label">
                            <label for="widget_title"><?php esc_html_e("Widget title :", "sticky-chat-widget"); ?></label>
                        </div>
                        <div class="gp-form-input">
                            <input type="text" placeholder="<?php esc_html_e("Enter widget title", "sticky-chat-widget"); ?>" class="is-required" id="widget_title" autocomplete="off" name="" data-label="Widget title">
                        </div>
                    </div>
                </div>
                <div class="gp-modal-footer text-center">
                    <button class="primary-btn" id="create_widget"><?php esc_html_e("Create Widget", "sticky-chat-widget"); ?></button>
                    <button class="secondary-btn hide-gp-modal"><?php esc_html_e("Cancel", "sticky-chat-widget"); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="gp-modal" id="rename-widget">
    <div class="gp-modal-bg"></div>
    <div class="gp-modal-container small">
        <div class="gp-modal-content">
            <div class="gp-modal-data">
                <button class="gp-modal-close-btn">
                    <span class="svg-icon">
                        <?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['close']); ?>
                    </span>
                </button>
                <div class="gp-modal-header">
                    <?php esc_html_e("Rename Widget", "sticky-chat-widget"); ?>
                </div>
                <div class="gp-modal-body">
                    <div class="gp-form-field">
                        <div class="gp-form-label">
                            <label for="rename_widget_title"><?php esc_html_e("Widget title :", "sticky-chat-widget"); ?></label>
                        </div>
                        <div class="gp-form-input">
                            <input type="text" class="is-required" id="rename_widget_title" placeholder="<?php esc_html_e("Enter widget title", "sticky-chat-widget"); ?>" autocomplete="off" name="" data-label="Widget title">
                        </div>
                    </div>
                </div>
                <div class="gp-modal-footer text-center">
                    <button class="primary-btn" id="rename_widget"><?php esc_html_e("Rename Widget", "sticky-chat-widget"); ?></button>
                    <button class="secondary-btn hide-gp-modal"><?php esc_html_e("Cancel", "sticky-chat-widget"); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="gp-modal" id="copy-widget">
    <div class="gp-modal-bg"></div>
    <div class="gp-modal-container small">
        <div class="gp-modal-content">
            <div class="gp-modal-data">
                <button class="gp-modal-close-btn">
                    <span class="svg-icon">
                        <?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['close']); ?>
                    </span>
                </button>
                <div class="gp-modal-header">
                    <?php esc_html_e("Clone Widget", "sticky-chat-widget"); ?>
                </div>
                <div class="gp-modal-body">
                    <div class="gp-form-field">
                        <div class="gp-form-label">
                            <label for="clone_name"><?php esc_html_e("Widget title :", "sticky-chat-widget"); ?></label>
                        </div>
                        <div class="gp-form-input">
                            <input type="text" class="is-required" data-label="Title" id="clone_name" autocomplete="off" name="">
                        </div>
                    </div>
                </div>
                <div class="gp-modal-footer text-center">
                    <button class="primary-btn" id="copy_widget"><?php esc_html_e("Clone Widget", "sticky-chat-widget"); ?></button>
                    <button class="secondary-btn hide-gp-modal"><?php esc_html_e("Cancel", "sticky-chat-widget"); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="gp-modal" id="delete-widget">
    <div class="gp-modal-bg"></div>
    <div class="gp-modal-container small">
        <div class="gp-modal-content">
            <div class="gp-modal-data">
                <button class="gp-modal-close-btn">
                    <span class="svg-icon">
                        <?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['close']); ?>
                    </span>
                </button>
                <div class="gp-modal-header">
                    <?php esc_html_e("Remove Widget", "sticky-chat-widget"); ?>
                </div>
                <div class="gp-modal-body">
                    <?php esc_html_e("Are you sure, you want to remove this widget?", "sticky-chat-widget") ?>
                </div>
                <div class="gp-modal-footer text-center">
                    <button class="danger-btn" id="delete_widget"><?php esc_html_e("Remove", "sticky-chat-widget"); ?></button>
                    <button class="secondary-btn hide-gp-modal"><?php esc_html_e("Cancel", "sticky-chat-widget"); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="gp-modal" id="preview_widget">
    <div class="gp-modal-bg"></div>
    <div class="gp-modal-container small">
        <div class="gp-modal-content">
            <div class="gp-modal-data">
                <button class="gp-modal-close-btn">
                    <span class="svg-icon">
                        <?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['close']); ?>
                    </span>
                </button>
                <div class="gp-modal-header">
                    <?php esc_html_e("Preview", "sticky-chat-widget"); ?>
                </div>
                <div class="gp-modal-body">
                    <div class="preview-layout inner-form">
                        <div id="preview_desktop" class="desktop-layout preview-desktop-layout">
                            <div class="outer">
                                <div class="inner">
                                    <div class="ginger-sticky-buttons has-shadow">
                                        <div class="sticky-button-list">
                                            <div class="button-list">

                                            </div>
                                            <div class="main-button channel-btn active-tooltip">
                                                <div class="main-action-button">
                                                    <div class="gsb-main-action-button">
                                                        <a href="javascript:;" class="cta-button"></a>
                                                    </div>
                                                    <div class="close-gsb-action-button">
                                                        <a class="active-tooltip close-gsb-button" href="javascript:;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path d="M18.83 16l8.6-8.6a2 2 0 0 0-2.83-2.83l-8.6 8.6L7.4 4.6a2 2 0 0 0-2.82 2.82l8.58 8.6-8.58 8.6a2 2 0 1 0 2.83 2.83l8.58-8.6 8.6 8.6a2 2 0 0 0 2.83-2.83z"/></svg>
                                                        </a>
                                                    </div>

                                                </div>
                                                <div class="single-btn"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="desktop-bottom">

                            </div>
                        </div>
                    </div>
                    <div class="device-switch">
                        <div class="device-option desktop">
                            <input class="sr-only preview-button preview-desktop-btn" checked type="radio" name="preview_device_switch" id="preview_device_desktop" value="desktop">
                            <label for="preview_device_desktop"><span class="dashicons dashicons-desktop"></span> Desktop</label>
                        </div>
                        <div class="device-option mobile">
                            <input class="sr-only preview-button preview-mobile-btn" id="preview_device_mobile" type="radio" name="preview_device_switch" value="mobile">
                            <label for="preview_device_mobile"><i class="fa fa-mobile-alt"></i> Mobile</label>
                        </div>
                    </div>
                </div>
                <div class="gp-modal-footer text-center">
                    <button class="secondary-btn hide-gp-modal"><?php esc_html_e("Cancel", "sticky-chat-widget"); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
