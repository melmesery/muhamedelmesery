<?php
/**
 * Display preview of widget functionality of the plugin.
 *
 * @author  : Ginger Plugins <gingerplugins@gmail.com>
 * @license : GPL2
 * */
defined('ABSPATH') or die('Direct Access is not allowed');
?>

<div class="setting-title"><?php esc_html_e("Preview", "sticky-chat-widget") ?></div>
<div id="ginger-sticky-element" class="ginger-sticky-element"></div>
<div class="ginger-sticky-content">
    <div class="ginger-sticky-box">
        <div class="preview-layout inner-form">
            <div id="desktop" class="desktop-layout preview-desktop-layout">
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
                <input class="sr-only preview-desktop-btn" checked type="radio" name="device_switch" id="device_desktop" value="desktop">
                <label for="device_desktop"><span class="dashicons dashicons-desktop"></span> Desktop</label>
            </div>
            <div class="device-option mobile">
                <input class="sr-only preview-mobile-btn" id="device_mobile" type="radio" name="device_switch" value="mobile">
                <label for="device_mobile"><i class="fa fa-mobile-alt"></i> Mobile</label>
            </div>
        </div>
    </div>
</div>
