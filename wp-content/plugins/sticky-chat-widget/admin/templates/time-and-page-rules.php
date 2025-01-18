<?php
/**
 * Time delay and page rule setting functionality of the plugin.
 *
 * @author  : Ginger Plugins <gingerplugins@gmail.com>
 * @license : GPL2
 * */
defined('ABSPATH') or die('Direct Access is not allowed');
?>
<?php
$defaultDisplayRuleSetting = Ginger_Social_Icons::get_display_rule_setting();
$displayRuleSettings       = get_post_meta($postId, "display_rules", true);
$displayRuleSettings       = shortcode_atts($defaultDisplayRuleSetting, $displayRuleSettings);

if (!empty($disabled)) {
    $displayRuleSettings['time_rule'] = "all_time";
}
?>

<div id="targeting-settings" class="setting-tab">
    <div class="setting-title"><?php esc_html_e("Targeting", "sticky-chat-widget") ?></div>
    <div class="gp-step-sub-title"><?php esc_html_e("On which pages should it show?", "sticky-chat-widget") ?></div>
    <div class="inline-radio-buttons">
        <div class="inline-radio-button">
            <input class="sr-only page-rule-type" type="radio" name="display_rules[page_rule]" id="page_rules_all" value="all_pages" <?php checked($displayRuleSettings['page_rule'], "all_pages") ?>>
            <label for="page_rules_all"><?php esc_html_e("On all pages ", "sticky-chat-widget") ?></label>
        </div>
        <div class="inline-radio-button">
            <input class="sr-only page-rule-type" type="radio" name="display_rules[page_rule]" id="page_rules_custom" value="custom_pages" <?php checked($displayRuleSettings['page_rule'], "custom_pages") ?>>
            <label for="page_rules_custom"><?php esc_html_e("On selected pages only ", "sticky-chat-widget") ?></label>
            <?php if (!empty($disabled)) { ?>
                <a class="upgrade-link in-block" href="javascript:;" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($icons['pro']); ?></a>
            <?php } ?>
        </div>
    </div>
    <?php

    $pageRules = get_post_meta($postId, "page_rules", true);
    if (!isset($pageRules['show_on_pages'])) {
        $showChecked  = "yes";
        $isShowActive = "active";
    } else {
        $showChecked  = !empty($pageRules['show_on_pages']) ? $pageRules['show_on_pages'] : "yes";
        $isShowActive = ($pageRules['show_on_pages'] == "yes") ? "active" : "";
    }

    if (!isset($pageRules['hide_on_pages'])) {
        $hideChecked  = "yes";
        $isHideActive = "active";
    } else {
        $hideChecked  = !empty($pageRules['hide_on_pages']) ? $pageRules['hide_on_pages'] : "yes";
        $isHideActive = ($pageRules['hide_on_pages'] == "yes") ? "active" : "";
    }

    ?>
    <div class="custom-page-rules pro-content <?php echo esc_attr($disabled) ?> <?php echo ($displayRuleSettings['page_rule'] == "custom_pages") ? "active" : "" ?>">
        <div class="pro-content-to-show">
            <span class="dashboard-switch in-flex on-off">
                <input type="hidden" name="page_rules[show_on_pages]" value="no">
                <input type="checkbox" id="show_on_pages" name="page_rules[show_on_pages]" value="yes" class="sr-only" <?php checked($showChecked, "yes") ?>>
                <label for="show_on_pages"><?php esc_html_e("Show on pages those matching at least one of the following condition(s) ", "sticky-chat-widget") ?></label>
            </span>
            <div class="page-rules-content custom-page-rule <?php echo esc_attr($isShowActive) ?>" id="show-pages-rule">
                <div class="show-page-rules page-rules">
                    <?php
                    $count = 0;
                    if (isset($pageRules['show_rules']) && is_array($pageRules['show_rules']) && count($pageRules['show_rules']) > 0) {
                        foreach ($pageRules['show_rules'] as $rule) { ?>
                            <div class="page-rule">
                                <div class="rule-label">
                                    <?php esc_html_e("URL of page", "sticky-chat-widget") ?>
                                </div>
                                <div class="rule-selector">
                                    <select class="sumoselect" name="page_rules[show_rules][<?php echo esc_attr($count) ?>][url_rule]">
                                        <?php foreach ($rules as $key => $value) { ?>
                                            <option <?php selected($key, $rule['url_rule']) ?> value="<?php echo esc_attr($key) ?>"><?php echo esc_attr($value) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="rule-input">
                                    <input type="text" data-label="<?php esc_attr_e("page rule", "sticky-chat-widget") ?>" name="page_rules[show_rules][<?php echo esc_attr($count) ?>][value]" class="is-required" value="<?php echo esc_attr($rule['value']) ?>" id="show_rules_<?php echo esc_attr($count) ?>_value">
                                </div>
                                <div class="remove-rule rule-remove" id="show_rule_remove">
                                    <?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['trash']); ?>
                                </div>
                            </div>
                            <?php
                            $count++;
                        }//end foreach
                    }//end if
                    ?>
                </div>
                <div class="page-rules-btn">
                    <a href="javascript:;" role="button" class="add-condition <?php echo (empty($upgrade)) ? "show-rule" : "" ?>"><?php esc_html_e("+ Add another condition", "sticky-chat-widget") ?></a>
                </div>
            </div>
            <span class="dashboard-switch in-flex on-off">
                <input type="hidden" name="page_rules[hide_on_pages]" value="no">
                <input type="checkbox" id="hide_on_pages" name="page_rules[hide_on_pages]" value="yes" class="sr-only" <?php checked($hideChecked, "yes") ?>>
                <label for="hide_on_pages"><?php esc_html_e("Hide on pages those matching at least one of the following condition(s) ", "sticky-chat-widget") ?></label>
            </span>
            <div class="page-rules-content custom-page-rule <?php echo esc_attr($isHideActive) ?>" id="hide-pages-rule">
                <div class="page-rules hide-page-rules">
                    <?php if (isset($pageRules['hide_rules']) && is_array($pageRules['hide_rules']) && count($pageRules['hide_rules']) > 0) { ?>
                        <?php foreach ($pageRules['hide_rules'] as $rule) { ?>
                            <div class="page-rule">
                                <div class="rule-label">
                                    <?php esc_html_e("URL of page", "sticky-chat-widget") ?>
                                </div>
                                <div class="rule-selector">
                                    <select class="sumoselect" name="page_rules[hide_rules][<?php echo esc_attr($count) ?>][url_rule]" id="hide_rules_<?php echo esc_attr($count) ?>_url_rule">
                                        <?php foreach ($rules as $key => $value) { ?>
                                            <option <?php selected($key, $rule['url_rule']) ?> value="<?php echo esc_attr($key) ?>"><?php echo esc_attr($value) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="rule-input">
                                    <input type="text" class="is-required" data-label="<?php esc_attr_e("page rule", "sticky-chat-widget") ?>" name="page_rules[hide_rules][<?php echo esc_attr($count) ?>][value]" value="<?php echo esc_attr($rule['value']) ?>">
                                </div>
                                <div class="remove-rule rule-remove" id="hide_rule_remove">
                                    <?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['trash']); ?>
                                </div>
                            </div>
                            <?php $count++;
                        }//end foreach
                    }//end if
                    ?>
                </div>
                <div class="page-rules-btn">
                    <a href="javascript:;" role="button" class="add-condition <?php echo (empty($upgrade)) ? "hide-rule" : "" ?>"><?php esc_html_e("+ Add another condition", "sticky-chat-widget") ?></a>
                </div>
            </div>
        </div>
        <?php if (!empty($disabled)) { ?>
            <div class="pro-overlay">
                <a class="in-block pro-button" href="<?php echo esc_url(GP_Admin_Sticky_Chat_Buttons::upgrade_url()) ?>" target="_blank"><?php esc_html_e("Upgrade to Pro ", "sticky-chat-widget") ?></a>
            </div>
        <?php } ?>
    </div>
    <div class="gp-step-sub-title mt-36"><?php esc_html_e("On which days should it show?", "sticky-chat-widget") ?></div>
    <div class="inline-radio-buttons">
        <div class="inline-radio-button">
            <input class="sr-only time-rule-type" type="radio" name="display_rules[time_rule]" id="time_rules_all" value="all_time" <?php checked($displayRuleSettings['time_rule'], "all_time") ?>>
            <label for="time_rules_all"><?php esc_html_e("On all days ", "sticky-chat-widget") ?></label>
        </div>
        <div class="inline-radio-button">
            <input class="sr-only time-rule-type" type="radio" name="display_rules[time_rule]" id="time_rules_custom" value="custom_time" <?php checked($displayRuleSettings['time_rule'], "custom_time") ?>>
            <label for="time_rules_custom"><?php esc_html_e("On selected days only ", "sticky-chat-widget") ?></label>
            <?php if (!empty($disabled)) { ?>
                <a class="upgrade-link in-block" href="javascript:;" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($icons['pro']); ?></a>
            <?php } ?>
        </div>
    </div>
    <?php
    $defaultgsbWeekDays = [
        'timezone' => 0,
        'schedule' => [],
    ];
    $gsbWeekdays        = get_post_meta($postId, "weekdays", true);
    $gsbWeekdays        = shortcode_atts($defaultgsbWeekDays, $gsbWeekdays);

    ?>
    <div class="custom-time-rules pro-content <?php echo esc_attr($disabled) ?> <?php echo ($displayRuleSettings['time_rule'] == "custom_time") ? "active" : "" ?>">
        <div class="pro-content-to-show">
            <div class="gp-form-field">
                <div class="gp-form-label">
                    <label><?php esc_html_e("Timezone:", "sticky-chat-widget") ?></label>
                </div>
                <div class="gp-form-input">
                    <select name="weekdays[timezone]" class="sumoselect">
                        <option value="0">UTC</option>
                    </select>
                </div>
            </div>
            <div class="gp-form-field">
                <div class="gp-form-label">
                    <label><?php esc_html_e("Weekdays:", "sticky-chat-widget") ?></label>
                </div>
                <?php $schedule = $gsbWeekdays['schedule']; ?>
                <div class="gp-form-input weekdays">
                    <?php for ($i = 0; $i < 7; $i++) { ?>
                        <div class="timezone-setting weekday-input <?php echo esc_attr(($i == 6) ? "last" : "") ?>" data-index="<?php echo esc_attr($i) ?>">
                            <div class="d-flex">
                                <span class="dashboard-switch in-flex on-off">
                                    <input type="hidden" name="weekdays[schedule][<?php echo esc_attr($i) ?>][status]" value="no">
                                    <input type="checkbox" id="weekday_status_<?php echo esc_attr($i) ?>" name="weekdays[schedule][<?php echo esc_attr($i) ?>][status]" value="yes" checked class="sr-only custom-checkbox">
                                    <label for="weekday_status_<?php echo esc_attr($i) ?>"><?php echo esc_attr(gmdate("l", strtotime("1970-01-".($i + 4)))) ?></label>
                                </span>
                                <div class="display-time">
                                    From <span class="start-time_<?php echo esc_attr($i) ?>">00:00</span> to <span class="end-time_<?php echo esc_attr($i) ?>">23:59</span>
                                </div>
                            </div>
                            <div class="weekday-bottom">
                                <div id="time-range-<?php echo esc_attr($i) ?>"></div>
                                <input type="hidden" name="weekdays[schedule][<?php echo esc_attr($i) ?>][start_time]" id="start_time_<?php echo esc_attr($i) ?>">
                                <input type="hidden" name="weekdays[schedule][<?php echo esc_attr($i) ?>][end_time]" id="end_time_<?php echo esc_attr($i) ?>">
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php if (!empty($disabled)) { ?>
            <div class="pro-overlay">
                <a class="in-block pro-button" href="<?php echo esc_url(GP_Admin_Sticky_Chat_Buttons::upgrade_url()) ?>" target="_blank"><?php esc_html_e("Upgrade to Pro ", "sticky-chat-widget") ?></a>
            </div>
        <?php } ?>
    </div>

    <div class="gp-step-sub-title mt-36"><?php esc_html_e("On which dates should it show?", "sticky-chat-widget") ?></div>
    <div class="inline-radio-buttons">
        <div class="inline-radio-button">
            <input class="sr-only" type="radio" name="display_rules[dates_rule]" id="date_rules_all" value="all_dates" <?php checked($displayRuleSettings['dates_rule'], "all_dates") ?>>
            <label for="date_rules_all"><?php esc_html_e("For all dates ", "sticky-chat-widget") ?></label>
        </div>
        <div class="inline-radio-button">
            <input class="sr-only" type="radio" name="display_rules[dates_rule]" id="date_rules_custom" value="custom_dates" <?php checked($displayRuleSettings['dates_rule'], "custom_dates") ?>>
            <label for="date_rules_custom"><?php esc_html_e("For selected dates ", "sticky-chat-widget") ?></label>
            <?php if (!empty($disabled)) { ?>
                <a class="upgrade-link in-block" href="javascript:;" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($icons['pro']); ?></a>
            <?php } ?>
        </div>
    </div>

    <div class="custom-dates-rule pro-content <?php echo esc_attr($disabled) ?> <?php echo ($displayRuleSettings['dates_rule'] == "custom_dates") ? "active" : "" ?>">
        <div class="pro-content-to-show">
            <div class="gp-form-field in-flex">
                <div class="gp-form-label">
                    <label><?php esc_html_e("Timezone:", "sticky-chat-widget") ?></label>
                </div>
                <div class="gp-form-input medium-input">
                    <select name="" class="sumoselect">
                        <option value="0">UTC</option>
                    </select>
                </div>
            </div>
            <div class="gp-form-field in-flex">
                <div class="gp-form-label">
                    <label><?php esc_html_e("Start date and time:", "sticky-chat-widget") ?></label>
                </div>
                <div class="gp-form-input medium-input">
                    <input type="text" readonly value="">
                </div>
            </div>
            <div class="gp-form-field in-flex">
                <div class="gp-form-label">
                    <label><?php esc_html_e("End date and time:", "sticky-chat-widget") ?></label>
                </div>
                <div class="gp-form-input medium-input">
                    <input type="text" readonly value="">
                </div>
            </div>
        </div>
        <?php if (!empty($disabled)) { ?>
            <div class="pro-overlay">
                <a class="in-block pro-button" href="<?php echo esc_url(GP_Admin_Sticky_Chat_Buttons::upgrade_url()) ?>" target="_blank"><?php esc_html_e("Upgrade to Pro ", "sticky-chat-widget") ?></a>
            </div>
        <?php } ?>
    </div>

    <div class="gp-step-sub-title mt-36"><?php esc_html_e("For which countries should it show?", "sticky-chat-widget") ?></div>
    <div class="inline-radio-buttons">
        <div class="inline-radio-button">
            <input class="sr-only" type="radio" name="display_rules[country_rule]" id="country_rules_all" value="all_country" <?php checked($displayRuleSettings['country_rule'], "all_country") ?>>
            <label for="country_rules_all"><?php esc_html_e("For all countries ", "sticky-chat-widget") ?></label>
        </div>
        <div class="inline-radio-button">
            <input class="sr-only" type="radio" name="display_rules[country_rule]" id="country_rules_custom" value="custom_country" <?php checked($displayRuleSettings['country_rule'], "custom_country") ?>>
            <label for="country_rules_custom"><?php esc_html_e("For selected countries ", "sticky-chat-widget") ?></label>
            <?php if (!empty($disabled)) { ?>
                <a class="upgrade-link in-block" href="javascript:;" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($icons['pro']); ?></a>
            <?php } ?>
        </div>
    </div>
    <div class="custom-country-rule pro-content <?php echo esc_attr($disabled) ?> <?php echo ($displayRuleSettings['country_rule'] == "custom_country") ? "active" : "" ?>">
        <div class="pro-content-to-show">
            <div class="gp-form-field in-flex">
                <div class="gp-form-label">
                    <label for="ginger_sb_font_family"><?php esc_html_e("Select country:", "sticky-chat-widget") ?></label>
                </div>
                <div class="gp-form-input medium-input">
                    <select name="" id="" class="sumoselect">
                        <option value=""><?php esc_html_e("Select countries", "sticky-chat-widget") ?></option>
                    </select>
                </div>
            </div>
        </div>
        <?php if (!empty($disabled)) { ?>
            <div class="pro-overlay">
                <a class="in-block pro-button" href="<?php echo esc_url(GP_Admin_Sticky_Chat_Buttons::upgrade_url()) ?>" target="_blank"><?php esc_html_e("Upgrade to Pro ", "sticky-chat-widget") ?></a>
            </div>
        <?php } ?>
    </div>

    <div class="gp-step-sub-title mt-36"><?php esc_html_e("For which users should it show?", "sticky-chat-widget") ?></div>
    <div class="inline-radio-buttons">
        <div class="inline-radio-button">
            <input class="sr-only" type="radio" name="display_rules[user_rule]" id="user_rules_all" value="all_users" <?php checked($displayRuleSettings['user_rule'], "all_users") ?>>
            <label for="user_rules_all"><?php esc_html_e("For all users ", "sticky-chat-widget") ?></label>
        </div>
        <div class="inline-radio-button">
            <input class="sr-only" type="radio" name="display_rules[user_rule]" id="user_rules_custom" value="custom_user" <?php checked($displayRuleSettings['user_rule'], "custom_user") ?>>
            <label for="user_rules_custom"><?php esc_html_e("For selected users ", "sticky-chat-widget") ?></label>
            <?php if (!empty($disabled)) { ?>
                <a class="upgrade-link in-block" href="javascript:;" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($icons['pro']); ?></a>
            <?php } ?>
        </div>
    </div>
    <div class="custom-user-rule pro-content <?php echo esc_attr($disabled) ?> <?php echo ($displayRuleSettings['user_rule'] == "custom_user") ? "active" : "" ?>">
        <div class="pro-content-to-show">
            <div class="gp-form-field in-flex">
                <div class="gp-form-label">
                    <label for="ginger_sb_font_family"><?php esc_html_e("Select user:", "sticky-chat-widget") ?></label>
                </div>
                <div class="gp-form-input medium-input">
                    <select name="" id="" class="sumoselect">
                        <option value=""><?php esc_html_e("Select user", "sticky-chat-widget") ?></option>
                    </select>
                </div>
            </div>
        </div>
        <?php if (!empty($disabled)) { ?>
            <div class="pro-overlay">
                <a class="in-block pro-button" href="<?php echo esc_url(GP_Admin_Sticky_Chat_Buttons::upgrade_url()) ?>" target="_blank"><?php esc_html_e("Upgrade to Pro ", "sticky-chat-widget") ?></a>
            </div>
        <?php } ?>
    </div>

</div>
