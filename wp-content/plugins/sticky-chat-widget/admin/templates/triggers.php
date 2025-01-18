<?php
/**
 * Trigger setting for widget functionality of the plugin.
 *
 * @author  : Ginger Plugins <gingerplugins@gmail.com>
 * @license : GPL2
 * */
defined('ABSPATH') or die('Direct Access is not allowed');
?>

<?php
$defaultTriggerRuleSettings = Ginger_Social_Icons::get_trigger_rule_setting();
$triggerRulesSettings       = get_post_meta($postId, "trigger_rules", true);
$triggerRulesSettings       = shortcode_atts($defaultTriggerRuleSettings, $triggerRulesSettings);
?>

<div id="triggers-settings" class="setting-tab">
    <div class="setting-title"><?php esc_html_e("Triggers", "sticky-chat-widget") ?></div>
    <div class="gp-step-sub-title"><?php esc_html_e("When should it show?", "sticky-chat-widget") ?></div>
    <div class="gp-form-field">
        <div class="gp-form-label">
            <span class="dashboard-switch in-flex">
                <input type="hidden" name="trigger_rules[on_scroll]" value="no">
                <input class="sr-only" id="on_scroll" type="checkbox" name="trigger_rules[on_scroll]" value="yes" <?php checked($triggerRulesSettings['on_scroll'], "yes") ?>>
                <label class="text-right in-flex trigger-label" for="on_scroll"><?php esc_html_e("After visitor scrolled down ", "sticky-chat-widget") ?><input class="tiny-input only-numeric" <?php echo ($triggerRulesSettings['on_scroll'] == "yes") ? "" : "disabled" ?> type="text" name="trigger_rules[page_scroll]" value="<?php echo esc_attr($triggerRulesSettings['page_scroll']) ?>"><?php esc_html_e("  % on the page ", "sticky-chat-widget") ?><span class="ginger-info" data-ginger-tooltip="<?php esc_html_e("Sticky Chat Widget will be displayed after specified scroll on page", 'sticky-chat-widget') ?>"><span class="dashicons dashicons-editor-help"></span></span></label>
            </span>
        </div>
    </div>
    <div class="gp-form-field">
        <div class="gp-form-label">
            <span class="dashboard-switch in-flex">
                <input type="hidden" name="trigger_rules[after_seconds]" value="no">
                <input class="sr-only" id="after_seconds" type="checkbox" name="trigger_rules[after_seconds]" value="yes" <?php checked($triggerRulesSettings['after_seconds'], "yes") ?>>
                <label class="text-right in-flex trigger-label" for="after_seconds"><?php esc_html_e("After visitor has been on the page for at least  ", "sticky-chat-widget") ?><input class="tiny-input only-numeric" <?php echo ($triggerRulesSettings['after_seconds'] == "yes") ? "" : "disabled" ?> type="text" name="trigger_rules[seconds]" value="<?php echo esc_attr($triggerRulesSettings['seconds']) ?>"><?php esc_html_e("  seconds ", "sticky-chat-widget") ?><span class="ginger-info" data-ginger-tooltip="<?php esc_html_e("Sticky Chat Widget will be displayed after user spent specific time(in seconds) on page", 'sticky-chat-widget') ?>"><span class="dashicons dashicons-editor-help"></span></span></label>
            </span>
        </div>
    </div>
    <div class="gp-form-field">
        <div class="gp-form-label">
            <span class="dashboard-switch in-flex">
                <input type="hidden" name="trigger_rules[exit_intent]" value="no">
                <input class="sr-only exit-intent" id="exit_intent" type="checkbox" name="trigger_rules[exit_intent]" value="yes" <?php checked($triggerRulesSettings['exit_intent'], "yes") ?>>
                <label class="text-right in-flex trigger-label" for="exit_intent"><?php esc_html_e("On exit intent", "sticky-chat-widget") ?><span class="ginger-info" data-ginger-tooltip="<?php esc_html_e("Sticky Chat Widget will be displayed when visitor is about to leave the page or ideal for some time", 'sticky-chat-widget') ?>"><span class="dashicons dashicons-editor-help"></span></span></label>
            </span>
        </div>
    </div>
    <div class="exit-intent-setting <?php echo ($triggerRulesSettings['exit_intent'] == "yes") ? "active" : "" ?>">
        <div class="pro-content-to-show">
            <div class="gp-form-label">
                <span class="dashboard-switch in-flex">
                    <input type="hidden" name="trigger_rules[browser]" value="no">
                    <input class="sr-only" id="leaving_browser" type="checkbox" name="trigger_rules[browser]" value="yes" <?php checked($triggerRulesSettings['browser'], "yes") ?>>
                    <label class="text-right in-flex trigger-label" for="leaving_browser"><?php esc_html_e("Trigger when leaving browser window", "sticky-chat-widget") ?></label>
                </span>
            </div>
            <div class="gp-form-label">
                <span class="dashboard-switch in-flex">
                    <input type="hidden" name="trigger_rules[on_inactivity]" value="no">
                    <input class="sr-only" id="on_inactivity" type="checkbox" name="trigger_rules[on_inactivity]" value="yes" <?php checked($triggerRulesSettings['on_inactivity'], "yes") ?>>
                    <label class="text-right in-flex trigger-label" for="on_inactivity"><?php esc_html_e("Trigger on inactivity (1 minutes)", "sticky-chat-widget") ?></label>
                </span>
            </div>
        </div>
    </div>
</div>
