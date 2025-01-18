<?php
/**
 * Display notice for review functionality of the plugin.
 *
 * @author  : Ginger Plugins <gingerplugins@gmail.com>
 * @license : GPL2
 * */
defined('ABSPATH') or die('Direct Access is not allowed');
?>
<style>
    .ginger-notice-section {
        padding: 11px 0;
        font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
        position: relative;
    }
    .ginger-plugin-icon {
        width: 60px;
        display: inline-block;
        vertical-align: middle;
    }
    .ginger-plugin-icon img {
        width: 100%;
        height: 100%;
        -webkit-border-radius: 50%;
        -moz-border-radius: 50%;
        border-radius: 50%;
    }
    .ginger-plugin-message {
        display: inline-block;
        width: calc(100% - 80px);
        vertical-align: middle;
        padding: 0 0 0 15px;
    }
    .ginger-plugin-message-top {
        padding: 0 0 5px 0;
    }
    .ginger-plugin-message-bottom {

    }
    .ginger-plugin-message-bottom ul {
        margin: 0;
        padding: 0;
    }
    .ginger-plugin-message-bottom ul li {
        display: inline-block;
        margin: 0 10px 0 0;
        vertical-align: middle;
    }
    .ginger-plugin-message-bottom ul li a {
        text-decoration: none;
        display: inline-block;
        vertical-align: middle;
        line-height: 16px;
    }
    .ginger-plugin-message-bottom ul li span {
        display: inline-block;
        width: 16px;
        height: 16px;
        font-size: 16px;
        margin: 0 3px 0 0;
    }
    .thanks-text p {
        display: inline-block;
        font-size: 0px;
        font-weight: 900;
        color: #000;
        margin: 0;
        padding: 0;
    }
    .ginger-plugin-thanks-box {
        display: none;
    }
    button.close-review-box-btn {
        border: none;
        background: no-repeat;
        padding: 0;
        position: absolute;
        right: -12px;
        top: 0;
        color: #1c5fc6;
        cursor: pointer;
    }
</style>

<div class="notice notice-info ginger-notice" id="<?php echo esc_attr($this->slug) ?>-review-box" data-review="<?php echo esc_attr($this->slug) ?>" >
    <div class="ginger-notice-section">
        <div class="ginger-plugin-icon">
            <img id="<?php echo esc_attr($this->slug) ?>-thanks-img" src="<?php echo esc_url(GSB_PLUGIN_URL."dist/admin/images/review-icon.jpg") ?>" />
        </div>
        <div class="ginger-plugin-message">
            <div class="ginger-plugin-message-box" id="<?php echo esc_attr($this->slug) ?>-review-message-box">
                <div class="ginger-plugin-message-top">
                    <?php esc_html_e("We hope you're enjoying Sticky Chat Widget! Could you please do us a Big favor and give it 5 Star rating on WordPress to help us spread the word and boost our motivation?") ?>
                </div>
                <div class="ginger-plugin-message-bottom">
                    <ul>
                        <li><a class="<?php echo esc_attr($this->slug) ?>-thanks-box-btn" target="_blank" href="https://wordpress.org/support/plugin/sticky-chat-widget/reviews/?filter=5/#new-post"><span class="dashicons dashicons-external"></span><?php esc_html_e("Ok, you deserve it!"); ?></a></li>
                        <li><a class="<?php echo esc_attr($this->slug) ?>-thanks-box-btn" href="javascript:;"><span class="dashicons dashicons-smiley"></span><?php esc_html_e("I already did"); ?></a></li>
                        <li><a class="<?php echo esc_attr($this->slug) ?>-later-box-btn" href="javascript:;"><span class="dashicons dashicons-calendar-alt"></span><?php esc_html_e("Maybe Later"); ?></a></li>
                        <li><a class="<?php echo esc_attr($this->slug) ?>-hide-box-btn" href="javascript:;"><span class="dashicons dashicons-dismiss"></span><?php esc_html_e("Never show again"); ?></a></li>
                    </ul>
                </div>
            </div>
            <div class="ginger-plugin-thanks-box" id="<?php echo esc_attr($this->slug) ?>-review-thanks-box">
                <?php
                $text  = "THANK YOU! YOU ARE AWESOME";
                $texts = str_split($text);
                ?>
                <div class="thanks-text">
                    <?php foreach ($texts as $key => $text) {
                        if ($text == " ") {
                            $text = "&nbsp;";
                        }

                        echo "<p class='thanks-".esc_attr($key)."'>".esc_attr($text)."</p>";
                    } ?>
                </div>
                <button type="button" class="close-review-box-btn" id="<?php echo esc_attr($this->slug) ?>-close-btn"><span class="dashicons dashicons-no-alt"></span></button>
            </div>
        </div>
    </div>
</div>
<style>
    <?php foreach ($texts as $key => $text) {
        $time = (0.05 * $key);
        echo ".thanks-".esc_attr($key)." {animation: showup 5s infinite ".esc_attr($time)."s;}";
    } ?>
    @keyframes showup {
        0% {
            font-size: 0px;
            transform: rotate(0deg);
        }
        5% {
            font-size: 30px;
        }
        10% {
            font-size: 30px;
            transform: rotate(0deg);
        }
        60% {
            font-size: 30px;
            opacity: 1;
        }
        71% {
            opacity: 0;
        }
        100% {
            opacity: 0;
            font-size: 30px;
            transform: rotate(0deg);
        }
    }
</style>
<script>
    (function($) {
        "use strict";
        $(document).ready(function(){
            $(document).on("click",".<?php echo esc_attr($this->slug) ?>-thanks-box-btn, .<?php echo esc_attr($this->slug) ?>-thanks-box-btn", function(){
                $("#<?php echo esc_attr($this->slug) ?>-review-message-box").hide();
                $("#<?php echo esc_attr($this->slug) ?>-review-thanks-box").show();
                $("#<?php echo esc_attr($this->slug) ?>-thanks-img").attr("src", "<?php echo esc_url(GSB_PLUGIN_URL."dist/admin/images/icon-thanks.jpg") ?>");
                save_sticky_chat_widget_box_status(-1);
            });
            $(document).on("click",".<?php echo esc_attr($this->slug) ?>-later-box-btn", function(){
                $("#<?php echo esc_attr($this->slug) ?>-review-box").remove();
                save_sticky_chat_widget_box_status(7);
            });
            $(document).on("click",".<?php echo esc_attr($this->slug) ?>-hide-box-btn", function(){
                $("#<?php echo esc_attr($this->slug) ?>-review-box").remove();
                save_sticky_chat_widget_box_status(-1);
            });
            $(document).on("click","#<?php echo esc_attr($this->slug) ?>-close-btn", function(){
                $("#<?php echo esc_attr($this->slug) ?>-review-box").remove();
            });
        });


        function save_sticky_chat_widget_box_status(noOfDays) {
            $.ajax({
                url: "<?php echo esc_url(admin_url("admin-ajax.php")) ?>",
                data: "action=<?php echo esc_attr($this->slug) ?>_update_review_box_status&day_interval="+noOfDays+"&nonce=<?php echo esc_attr(wp_create_nonce($this->slug."-review-box-status")) ?>",
                type: "post",
                success: function(){

                }
            })
        }
    })(jQuery);
</script>
