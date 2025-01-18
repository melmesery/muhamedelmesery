<?php
/**
 * Widget list functionality of the plugin.
 *
 * @author  : Ginger Plugins <gingerplugins@gmail.com>
 * @license : GPL2
 * */
defined('ABSPATH') or die('Direct Access is not allowed');
?>
<?php if (isset($posts) && !empty($posts)) { ?>
    <div class="gp-box mt-40">
        <div class="dashboard-header">
            <div class="dashboard-header-left">
                <div class="gp-page-title">
                    <?php esc_html_e("Dashboard", "sticky-chat-widget") ?>
                </div>
            </div>
            <div class="dashboard-header-right">
                <a href="javascript:;"
                   class="gp-action-button pro-premium-features"><?php esc_html_e("Create Widget", "sticky-chat-widget") ?></a>
            </div>
        </div>
        <div class="dashboard-table responsive-table">
            <table>
                <thead>
                <tr>
                    <th class="status-col"><?php esc_html_e("Status", "sticky-chat-widget") ?></th>
                    <th class="created-col"><?php esc_html_e("Title", "sticky-chat-widget") ?></th>
                    <th class="channel-col"><?php esc_html_e("Channels", "sticky-chat-widget") ?></th>
                    <th class="analytics-col"><?php esc_html_e("Analytics ", "sticky-chat-widget") ?></th>
                    <th class="date-col"><?php esc_html_e("Created On", "sticky-chat-widget") ?></th>
                    <th class="action-col"><?php esc_html_e("Action", "sticky-chat-widget") ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($posts as $post) {
                    $widgetStatus = get_post_meta($post->ID, "widget_status", true);
                    ?>
                    <tr data-nonce="<?php echo esc_attr(wp_create_nonce("gsb_buttons_action_".esc_attr($post->ID))) ?>"
                        data-id="<?php echo esc_attr($post->ID) ?>"
                        class="gsb-buttons-col-<?php echo esc_attr($post->ID) ?>">
                        <td class="status-col">
                            <span class="dashboard-switch in-flex on-off">
                                <input type="checkbox" id="gp_sticky_btn_widget_status_<?php echo esc_attr($post->ID) ?>"
                                       value="yes"
                                       class="sr-only sticky-chat-widget-status" <?php checked($widgetStatus, 'yes') ?>>
                                <label for="gp_sticky_btn_widget_status_<?php echo esc_attr($post->ID) ?>"></label>
                            </span>
                        </td>
                        <td class="created-col"><?php echo esc_attr($post->post_title) ?></td>
                        <td class="channel-col">
                            <?php
                            $icons    = Ginger_Social_Icons::icon_list();
                            $channels = get_post_meta($post->ID, "channel_settings", true);
                            if (isset($channels) && !empty($channels)) {
                                ?>
                                <div class="display-icon widget-<?php echo esc_attr($post->ID) ?>">
                                    <?php
                                    $icon          = "";
                                    $count         = 0;
                                    $channelTitles = [];
                                    foreach ($channels as $key => $channel) {
                                        $setting = $icons[$key];
                                        $defaultChannelSetting = [
                                            'value'            => '',
                                            'title'            => $setting['title'],
                                            'for_desktop'      => 'yes',
                                            'for_mobile'       => 'yes',
                                            'image_id'         => '',
                                            'bg_color'         => $setting['color'],
                                            'text_color'       => '#ffffff',
                                            'bg_hover_color'   => $setting['color'],
                                            'text_hover_color' => '#ffffff',
                                            'custom_id'        => '',
                                            'custom_class'     => '',
                                            'whatsapp_message' => '',
                                            'email_subject'    => '',
                                        ];
                                        $channelSetting        = shortcode_atts($defaultChannelSetting, $channel);

                                        if ($key == "twitter" && ($channelSetting['bg_color'] == "#65BBF2" || $channelSetting['bg_color'] == '#65bbf2')) {
                                            $channelSetting['bg_color'] = "#000000";
                                        }

                                        $imageUrl   = "";
                                        $imageClass = "";
                                        $imageId    = $channelSetting['image_id'];
                                        if (!empty($imageId)) {
                                            $imageData = wp_get_attachment_image_src($imageId, "full");
                                            if (!empty($imageData) && isset($imageData[0])) {
                                                $imageUrl   = $imageData[0];
                                                $imageClass = "has-image";
                                            }
                                        }

                                        $count++;
                                        if (count($channels) > 5) {
                                            if ($count <= 4) { ?>
                                                <span class="channel-icons <?php echo ($key == "instagram" && $channels['instagram']['bg_color'] != "#df0079") ? "" : "channel-slug-".esc_attr($key) ?>"
                                                      data-ginger-tooltip="<?php echo esc_attr($channelSetting['title']) ?>"
                                                      style="background-color: <?php echo esc_attr($channelSetting['bg_color']) ?>;">
                                                    <?php if (!empty($imageUrl)) {?>
                                                        <img src="<?php echo esc_attr($imageUrl) ?>">
                                                    <?php } else { ?>
                                                        <?php Ginger_Social_Icons::load_and_sanitize_svg($icons[$key]['icon']); ?>
                                                    <?php } ?>
                                                </span>
                                            <?php }
                                        } else if (count($channels) == 5 || count($channels) < 5) { ?>
                                            <span class="channel-icons <?php echo ($key == "instagram" && $channels['instagram']['bg_color'] != "#df0079") ? "" : "channel-slug-".esc_attr($key) ?>"
                                                  data-ginger-tooltip="<?php echo esc_attr($channelSetting['title']) ?>"
                                                  style="background-color: <?php echo esc_attr($channelSetting['bg_color']) ?>;">
                                                    <?php if (!empty($imageUrl)) {?>
                                                        <img src="<?php echo esc_attr($imageUrl) ?>">
                                                    <?php } else { ?>
                                                        <?php Ginger_Social_Icons::load_and_sanitize_svg($icons[$key]['icon']); ?>
                                                    <?php } ?>
                                            </span>
                                        <?php }//end if

                                        $channelTitles[] = $channels[$key]['title'];
                                    }//end foreach

                                    $sliceArray = array_slice($channelTitles, 4);
                                    $channelst  = implode(", ", $sliceArray);
                                    if (count($channels) > 4 && count($channels) != 5) {
                                        echo '<span class="channel-icons channel-count" data-ginger-tooltip="'.esc_attr($channelst).'">+'.(count($channels) - 4).'</span>';
                                    }
                                    ?>
                                </div>
                            <?php }//end if
                            ?>
                        </td>
                        <td class="analytics-col"><a href="<?php echo esc_url(admin_url("admin.php?page=sticky-chat-widget-analytics")) ?>" class="analytics-icon"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['analytics']); ?></a></td>
                        <td class="date-col"><?php echo esc_attr(gmdate("d M, Y", strtotime(esc_attr($post->post_date)))) ?></td>
                        <td class="action-col">
                            <span class="action-box">
                                <a class="edit-record"
                                   href="<?php echo esc_url(admin_url('admin.php?page=sticky-chat-widget&task=edit-widget&edit='.esc_attr($post->ID).'&nonce='.esc_attr(wp_create_nonce('edit_widget_'.esc_attr($post->ID))))) ?>"><?php esc_html_e("Edit", "sticky-chat-widget") ?></a>
                                <a class="dropdown-button" href="javascript:;">
                                    <?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['action']); ?>
                                </a>
                            </span>
                            <div class="button-actions">
                                <ul>
                                    <?php if (!empty($upgrade)) { ?>
                                        <li><a href="#"
                                               class="clone-option pro-premium-features"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['clone']); ?><?php esc_html_e("Clone (Pro)", "sticky-chat-widget") ?></a>
                                        </li>
                                    <?php } else { ?>
                                        <li><a href="#" class="clone-option clone-widget"
                                               data-name="<?php echo esc_attr($post->post_title) ?>"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['clone']); ?><?php esc_html_e("Clone", "sticky-chat-widget") ?></a>
                                        </li>
                                    <?php } ?>
                                    <li><a href="#" class="rename-option rename-widget"
                                           data-title="<?php echo esc_attr($post->post_title) ?>"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['edit']); ?><?php esc_html_e("Rename", "sticky-chat-widget") ?></a>
                                    </li>
                                    <li class="delete-btn"><a href="#"
                                                              class="delete-option remove-widget"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['trash']); ?><?php esc_html_e("Remove", "sticky-chat-widget") ?></a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php
                    $icons    = Ginger_Social_Icons::icon_list();
                    $channels = get_post_meta($post->ID, "channel_settings", true);
                    if (isset($channels) && !empty($channels)) {
                        foreach ($channels as $key => $value) {?>
                            <style>
                                .widget-<?php echo esc_attr($post->ID) ?> .channel-slug-<?php echo esc_attr($key) ?> svg { fill: <?php echo esc_attr($value['text_color']) ?> !important;}
                                <?php if ($key == "slack" && $channels['slack']['text_color'] != "#ffffff") { ?>
                                .widget-<?php echo esc_attr($post->ID) ?> .channel-slug-<?php echo esc_attr($key) ?> svg path { fill: <?php echo esc_attr($value['text_color']) ?> !important;}
                                <?php } ?>
                            </style>
                            <?php
                        }
                    } ?>
                <?php }//end foreach
                ?>
                </tbody>
            </table>
        </div>
    </div>
<?php } else { ?>
    <style>
        body, #wpcontent, #wpfooter {
            background-color: #F7F7FF;
        }

        #wpfooter {
            display: none;
        }

        #wpcontent, #wpbody-content {
            padding: 0 !important;
        }

        .gp-no-records * {
            box-sizing: border-box;
        }
        .gp-no-records {
            width: 100%;
            position: relative;
            min-height: 640px;
            background-color: #F7F7FF;
            height: calc(100vh - 32px);
        }

        .gp-no-records-box {
            width: 100%;
            margin: 0 auto;
            position: absolute;
            left: 0;
            right: 0;
            height: auto;
            transform: translate(0, -50%);
            top: 50%;
            padding: 10px;
        }

        .gp-no-records-top {
            width: 300px;
            margin: 20px auto;
            max-width: 100%;
        }

        .gp-no-records-top img {
            width: 100%;
        }

        .no-records-title {
            font-size: 24px;
            text-align: center;
            position: relative;
            padding: 0 0 10px 0;
            margin: 0 0 40px 0;
            font-family: 'Fjalla One', sans-serif;
            line-height: 32px;
        }

        .no-records-title:after {
            content: "";
            width: 120px;
            height: 2px;
            background: #4F46E5;
            position: absolute;
            left: 0;
            right: 0;
            top: 100%;
            margin: 0 auto;
        }

        .no-records-features {
            max-width: 900px;
            margin: 0 auto;
            padding: 30px;
            background-color: #EEF0FF;
            border-radius: 18px;
        }

        .no-records-features ul {
            margin: 0;
            padding: 0;
        }

        .no-records-features ul li {
            display: block;
            padding: 0 0 10px 20px;
            font-size: 20px;
            line-height: 30px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            font-style: normal;
            font-weight: 400;
            position: relative;
        }

        .no-records-features ul li i {
            color: #4F46E5;
            font-size: 8px;
        }
        .no-records-features ul li:before {
            content: "";
            position: absolute;
            top: 13px;
            left: 0;
            height: 6px;
            width: 6px;
            background: #3c434a;
            border-radius: 50%;
        }

        .gp-no-records-bottom {
            text-align: center;
            padding: 40px 0 30px;
        }
        .gp-no-records-bottom .gp-action-button i {
            margin-left: 15px;
        }

        .text-color {
            color: #4F46E5;
        }
        @media screen and (max-width: 782px) {
            .no-records-features {
                padding: 25px;
            }
        }
        @media screen and (max-width: 768px) {
            .gp-no-records-box {
                position: relative;
                top: 0;
                transform: none;
            }
            .gp-no-records {
                height: auto;
            }
        }
    </style>
    <div class="gp-no-records">
        <div class="gp-no-records-box">
            <div class="gp-no-records-top">
                <img src="<?php echo esc_url(GSB_PLUGIN_URL)."dist/admin/images/empty-scrn.png"?>">
            </div>
            <div class="gp-no-records-middle">
                <div class="no-records-features">
                    <ul>
                        <li><?php esc_html_e(" Add and display ", "sticky-chat-widget") ?>
                            <span class="text-color"><?php esc_html_e("multi social", "sticky-chat-widget") ?></span><?php esc_html_e(" channels on your website so that your visitors can ", "sticky-chat-widget") ?>
                            <span class="text-color"><?php esc_html_e("quickly contact", "sticky-chat-widget") ?></span><?php esc_html_e(" you and share their ", "sticky-chat-widget") ?>
                            <span class="text-color"><?php esc_html_e("requirements.", "sticky-chat-widget") ?></span>
                        </li>
                        <li><span
                                    class="text-color"><?php esc_html_e(" Customize", "sticky-chat-widget") ?></span><?php esc_html_e(" your ", "sticky-chat-widget") ?>
                            <span class="text-color"><?php esc_html_e("widget", "sticky-chat-widget") ?></span><?php esc_html_e(" channels style according to your ", "sticky-chat-widget") ?>
                            <span class="text-color"><?php esc_html_e("requirement.", "sticky-chat-widget") ?></span>
                        </li>
                        <li>
                            <?php esc_html_e(" Display  your channels on ", "sticky-chat-widget") ?>
                            <span class="text-color"><?php esc_html_e("mobile, desktop", "sticky-chat-widget") ?></span><?php esc_html_e(" or ", "sticky-chat-widget") ?>
                            <span class="text-color"><?php esc_html_e("both.", "sticky-chat-widget") ?></span></li>
                    </ul>
                </div>
            </div>
            <div class="gp-no-records-bottom">
                <a href="javascript:;"
                   class="gp-action-button add-new-widget"><?php esc_html_e(" Create Your First Widget", "sticky-chat-widget"); ?> <i class="fas fa-plus"></i>
                </a>
            </div>
        </div>
    </div>
<?php }//end if ?>
<?php require_once dirname(__FILE__)."/common.php";
require_once dirname(__FILE__)."/premium-features.php";
require_once dirname(__FILE__)."/pro-features.php";
