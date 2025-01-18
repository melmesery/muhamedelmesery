<?php
/**
 * Display notice for review popup on Sticky Chat Widget page.
 *
 * @author  : Ginger Plugins <gingerplugins@gmail.com>
 * @license : GPL2
 * */
defined('ABSPATH') or die('Direct Access is not allowed');
$formIcons = Ginger_Social_Icons::svg_icons();
?>
<style>
    .review-modal {
        display: none;
        font-family: Lato, sans-serif;
        font-size: 16px;
        line-height: 1.2;
        box-sizing: border-box;
    }
    .review-modal.active {
        display: block;
    }
    .review-modal * {
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
    }
    .review-modal .review-modal-bg {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        z-index: 100001;
        background-color: rgba(0, 0, 0, 0.25);
    }
    .review-modal .review-modal-container {
        width: 660px;
        max-width: 90%;
        top: 50%;
        left: 0;
        right: 0;
        position: fixed;
        transform: translate(0px, -50%);
        background-color: #ffffff;
        border-radius: 4px;
        margin: 0 auto;
        min-height: 100px;
        z-index: 100009;
        max-height: 84vh;
        overflow: auto;
        box-shadow: 0 16px 16px -5px rgba(0, 0, 0, 0.22), 0 0 0.8px rgba(0, 0, 0, 0.1);
    }
    .review-modal .review-modal-container .review-modal-content {
        position: relative;
        text-align: center;
        background: url("<?php echo esc_url(GSB_PLUGIN_URL)."dist/admin/images/social-icons.png" ?>") fixed top center no-repeat;
        background-size: contain;
    }
    .review-modal .review-modal-container .review-modal-content .review-modal-close-btn {
        position: absolute;
        right: 10px;
        top: 10px;
        width: 24px;
        height: 24px;
        background: transparent;
        border: none;
        cursor: pointer;
    }
    .review-modal .review-modal-container .review-modal-content .review-modal-close-btn .svg-icon {
        display: block;
        width: 24px;
        height: 24px;
    }
    .review-modal .review-modal-container .review-modal-content .review-modal-close-btn .svg-icon svg {
        width: 100%;
        height: 100%;
    }
    .review-modal .review-modal-container .review-modal-body {
        padding: 15px 100px;
    }
    .review-modal .review-modal-container.small {
        width: 540px;
    }
    .review-title {
        margin: 40px auto;
        font-size: 20px;
        font-weight: bold;
    }
    .review-close-box {
        background-color: white;
        border-radius: 4px;
        width: 150px;
        height: auto;
        position: absolute;
        right: 0;
        top: 32px;
        -webkit-box-shadow: 0px 0px 47px -10px rgba(0,0,0,0.37);
        -moz-box-shadow: 0px 0px 47px -10px rgba(0,0,0,0.37);
        box-shadow: 0px 0px 47px -10px rgba(0,0,0,0.37);
        display: none;
    }
    .review-close-box.active {
        display: block;
    }
    .review-close-box:after {
        content: '';
        border-style: solid;
        border-width: 0 7px 7px;
        border-color: transparent transparent #fff;
        transition-duration: 0s;
        transform-origin: top;
        text-decoration: none;
        position: absolute;
        top: -7px;
        right: 5px;
    }
    .review-close-box ul {
        display: inline-block;
        width: 100%;
    }
    .review-close-box ul li {
        padding: 10px 0;
    }
    .review-close-box ul li:not(:first-child) {
        border-top: 1px solid #d5d5d5;
    }
    .review-rating-star {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        margin: 40px 0 10px 0;
    }
    .stars:not(:last-child) {
        margin-right: 5px;
    }
    .stars svg {
        cursor: pointer;
    }
    .stars.hover svg path, .stars.selected svg path {
        fill: orange;
    }
    .no-of-stars {
        margin-bottom: 40px;
    }
    #rating-review-popup .no-of-stars {
        margin-bottom: 10px;
    }
    .gp-form-label {
        text-align: left;
    }
    .submit-review {
        margin: 10px 0;
        background-color: #4F46E5;
        border: 1px solid #4F46E5;
        padding: 5px 40px;
        color: #fff;
        border-radius: 4px;
        cursor: pointer;
        letter-spacing: 0.6px;
    }
    #rating-review-popup .review-title {
        margin: 20px 0 40px 0;
    }
    .review-close-box ul li a:hover, .review-close-box ul li a:active, .review-close-box ul li a {
        color: #000000;
    }
    .review-desc {
        line-height: 1.4;
    }
    .gp-form-field .gp-form-label {
        display: block;
    }
    .gp-form-label {
        text-align: left;
    }
    .gp-form-field .gp-form-label label {
        display: inline-block;
        padding: 0 0 8px 0;
        font-size: 16px;
        cursor: pointer;
    }
    .gp-form-field .gp-form-input {
        position: relative;
    }
    .gp-form-field .gp-form-input textarea {
        border: solid 1px #d7d7d7;
        padding: 5px 10px;
        border-radius: 4px;
        width: 100%;
        font-size: 14px;
        color: #1c2733;
        height: 84px;
        max-width: 100%;
        outline: none;
        box-shadow: none;
    }
    @media only screen and (max-width: 500px) {
        .review-modal .review-modal-container .review-modal-body {
            padding: 15px 20px;
        }
    }
</style>
<div class="review-modal active" id="rating-popup">
    <div class="review-modal-bg"></div>
    <div class="review-modal-container">
        <div class="review-modal-content">
            <div class="review-modal-data">
                <button class="review-modal-close-btn">
                    <span class="svg-icon">
                        <?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['close']); ?>
                    </span>
                    <div class="review-close-box">
                        <ul>
                            <li class="<?php echo esc_attr($this->slug) ?>-later-box-btn"><?php esc_html_e("Ask me later", "sticky-chat-widget") ?></li>
                            <li class="<?php echo esc_attr($this->slug) ?>-hide-box-btn"><?php esc_html_e("Never show again", "sticky-chat-widget") ?></li>
                        </ul>
                    </div>
                </button>
                <div class="review-modal-body">
                    <div class="review-title"><?php esc_html_e("Enjoying the Sticky Chat Widget?", "sticky-chat-widget") ?></div>
                    <div class="review-desc"><?php esc_html_e("Can you please show us some love and rate Sticky Chat Widget? It'll really help us a lot 🙏", "sticky-chat-widget") ?></div>
                    <div class="review-rating-star">
                        <div class="star-1 stars" data-star="1">
                            <svg width="44" height="42" viewBox="0 0 44 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19.6396 1.46102L14.2692 12.3206L2.25349 14.0677C0.0987305 14.3793 -0.764819 17.0286 0.797795 18.546L9.49086 26.9942L7.43479 38.9283C7.0647 41.0854 9.34283 42.7012 11.2509 41.6924L22 36.0575L32.7491 41.6924C34.6572 42.693 36.9353 41.0854 36.5652 38.9283L34.5091 26.9942L43.2022 18.546C44.7648 17.0286 43.9013 14.3793 41.7465 14.0677L29.7308 12.3206L24.3604 1.46102C23.3981 -0.474683 20.6101 -0.499289 19.6396 1.46102Z" fill="#B3B3B3"/></svg>
                        </div>
                        <div class="star-2 stars" data-star="2">
                            <svg width="44" height="42" viewBox="0 0 44 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19.6396 1.46102L14.2692 12.3206L2.25349 14.0677C0.0987305 14.3793 -0.764819 17.0286 0.797795 18.546L9.49086 26.9942L7.43479 38.9283C7.0647 41.0854 9.34283 42.7012 11.2509 41.6924L22 36.0575L32.7491 41.6924C34.6572 42.693 36.9353 41.0854 36.5652 38.9283L34.5091 26.9942L43.2022 18.546C44.7648 17.0286 43.9013 14.3793 41.7465 14.0677L29.7308 12.3206L24.3604 1.46102C23.3981 -0.474683 20.6101 -0.499289 19.6396 1.46102Z" fill="#B3B3B3"/></svg>
                        </div>
                        <div class="star-2 stars" data-star="3">
                            <svg width="44" height="42" viewBox="0 0 44 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19.6396 1.46102L14.2692 12.3206L2.25349 14.0677C0.0987305 14.3793 -0.764819 17.0286 0.797795 18.546L9.49086 26.9942L7.43479 38.9283C7.0647 41.0854 9.34283 42.7012 11.2509 41.6924L22 36.0575L32.7491 41.6924C34.6572 42.693 36.9353 41.0854 36.5652 38.9283L34.5091 26.9942L43.2022 18.546C44.7648 17.0286 43.9013 14.3793 41.7465 14.0677L29.7308 12.3206L24.3604 1.46102C23.3981 -0.474683 20.6101 -0.499289 19.6396 1.46102Z" fill="#B3B3B3"/></svg>
                        </div>
                        <div class="star-4 stars" data-star="4">
                            <svg width="44" height="42" viewBox="0 0 44 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19.6396 1.46102L14.2692 12.3206L2.25349 14.0677C0.0987305 14.3793 -0.764819 17.0286 0.797795 18.546L9.49086 26.9942L7.43479 38.9283C7.0647 41.0854 9.34283 42.7012 11.2509 41.6924L22 36.0575L32.7491 41.6924C34.6572 42.693 36.9353 41.0854 36.5652 38.9283L34.5091 26.9942L43.2022 18.546C44.7648 17.0286 43.9013 14.3793 41.7465 14.0677L29.7308 12.3206L24.3604 1.46102C23.3981 -0.474683 20.6101 -0.499289 19.6396 1.46102Z" fill="#B3B3B3"/></svg>
                        </div>
                        <div class="star-5 stars <?php echo esc_attr($this->slug) ?>-thanks-box-btn" data-star="5">
                            <a href="https://wordpress.org/support/plugin/sticky-chat-widget/reviews/?filter=5/#new-post" target="_blank">
                                <svg width="44" height="42" viewBox="0 0 44 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19.6396 1.46102L14.2692 12.3206L2.25349 14.0677C0.0987305 14.3793 -0.764819 17.0286 0.797795 18.546L9.49086 26.9942L7.43479 38.9283C7.0647 41.0854 9.34283 42.7012 11.2509 41.6924L22 36.0575L32.7491 41.6924C34.6572 42.693 36.9353 41.0854 36.5652 38.9283L34.5091 26.9942L43.2022 18.546C44.7648 17.0286 43.9013 14.3793 41.7465 14.0677L29.7308 12.3206L24.3604 1.46102C23.3981 -0.474683 20.6101 -0.499289 19.6396 1.46102Z" fill="#B3B3B3"/></svg>
                            </a>
                        </div>
                    </div>
                    <div class="no-of-stars"><span>0</span> / 5</div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="review-modal" id="rating-review-popup">
    <div class="review-modal-bg"></div>
    <div class="review-modal-container">
        <div class="review-modal-content">
            <div class="review-modal-data">
                <button class="review-modal-close-btn">
                    <span class="svg-icon">
                        <?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['close']); ?>
                    </span>
                </button>
                <div class="review-modal-body">
                    <div class="review-title"><?php esc_html_e("Share your experience", "sticky-chat-widget") ?></div>
                    <div class="review-rating-star">
                        <div class="star-1 stars" data-star="1">
                            <svg width="44" height="42" viewBox="0 0 44 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19.6396 1.46102L14.2692 12.3206L2.25349 14.0677C0.0987305 14.3793 -0.764819 17.0286 0.797795 18.546L9.49086 26.9942L7.43479 38.9283C7.0647 41.0854 9.34283 42.7012 11.2509 41.6924L22 36.0575L32.7491 41.6924C34.6572 42.693 36.9353 41.0854 36.5652 38.9283L34.5091 26.9942L43.2022 18.546C44.7648 17.0286 43.9013 14.3793 41.7465 14.0677L29.7308 12.3206L24.3604 1.46102C23.3981 -0.474683 20.6101 -0.499289 19.6396 1.46102Z" fill="#B3B3B3"/></svg>
                        </div>
                        <div class="star-2 stars" data-star="2">
                            <svg width="44" height="42" viewBox="0 0 44 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19.6396 1.46102L14.2692 12.3206L2.25349 14.0677C0.0987305 14.3793 -0.764819 17.0286 0.797795 18.546L9.49086 26.9942L7.43479 38.9283C7.0647 41.0854 9.34283 42.7012 11.2509 41.6924L22 36.0575L32.7491 41.6924C34.6572 42.693 36.9353 41.0854 36.5652 38.9283L34.5091 26.9942L43.2022 18.546C44.7648 17.0286 43.9013 14.3793 41.7465 14.0677L29.7308 12.3206L24.3604 1.46102C23.3981 -0.474683 20.6101 -0.499289 19.6396 1.46102Z" fill="#B3B3B3"/></svg>
                        </div>
                        <div class="star-3 stars" data-star="3">
                            <svg width="44" height="42" viewBox="0 0 44 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19.6396 1.46102L14.2692 12.3206L2.25349 14.0677C0.0987305 14.3793 -0.764819 17.0286 0.797795 18.546L9.49086 26.9942L7.43479 38.9283C7.0647 41.0854 9.34283 42.7012 11.2509 41.6924L22 36.0575L32.7491 41.6924C34.6572 42.693 36.9353 41.0854 36.5652 38.9283L34.5091 26.9942L43.2022 18.546C44.7648 17.0286 43.9013 14.3793 41.7465 14.0677L29.7308 12.3206L24.3604 1.46102C23.3981 -0.474683 20.6101 -0.499289 19.6396 1.46102Z" fill="#B3B3B3"/></svg>
                        </div>
                        <div class="star-4 stars" data-star="4">
                            <svg width="44" height="42" viewBox="0 0 44 42" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19.6396 1.46102L14.2692 12.3206L2.25349 14.0677C0.0987305 14.3793 -0.764819 17.0286 0.797795 18.546L9.49086 26.9942L7.43479 38.9283C7.0647 41.0854 9.34283 42.7012 11.2509 41.6924L22 36.0575L32.7491 41.6924C34.6572 42.693 36.9353 41.0854 36.5652 38.9283L34.5091 26.9942L43.2022 18.546C44.7648 17.0286 43.9013 14.3793 41.7465 14.0677L29.7308 12.3206L24.3604 1.46102C23.3981 -0.474683 20.6101 -0.499289 19.6396 1.46102Z" fill="#B3B3B3"/></svg>
                        </div>
                    </div>
                    <div class="no-of-stars"><span>0</span> / 4</div>
                    <form method="post" action="" id="review_form">
                        <div class="gp-form-field">
                            <div class="gp-form-label">
                                <label for="rating_review"><?php esc_html_e("Review (optional)", "sticky-chat-widget"); ?></label>
                            </div>
                            <div class="gp-form-input">
                                <textarea rows="3" name="rating_feedback" id="rating_review"></textarea>
                            </div>
                        </div>
                        <input type="hidden" name="rating_star" id="rating_star" value="">
                        <button type="submit" class="submit-review"><?php esc_html_e("Submit", "sticky-chat-widget") ?></button>
                        <input type="hidden" name="action" value="<?php echo esc_attr($this->slug) ?>_send_email">
                        <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce($this->slug."-send-mail")) ?>">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function($) {
        "use strict";
        $(document).ready(function (){
            $(document).on("click", "#rating-popup .review-modal-close-btn", function (){
               $(".review-close-box").toggleClass("active");
            });
        });

        $(document).on("mouseover", '.review-rating-star .stars', function () {
            var onStar = parseInt($(this).data('star'), 10); // The star currently mouse on

            // Now highlight all the stars that's not after the current hovered star
            $(this).parent().children('.stars').each(function (e) {
                if (e < onStar) {
                    $(this).addClass('hover');
                }
                else {
                    $(this).removeClass('hover');
                }
            });

            var val = parseInt($('.review-rating-star .stars.hover').last().data('star'), 10);
            $(".no-of-stars span").text(val);
        });

        $(document).on("mouseout", '.review-rating-star .stars', function () {
            $(this).parent().children('.stars').each(function (e) {
                $(this).removeClass('hover');
            });
            var val = parseInt($('.review-rating-star .stars.selected').last().data('star'), 10);
            if($(".review-rating-star .stars.hover").length == 0 && $(".review-rating-star .stars.selected").length == 0) {
                $(".no-of-stars span").text(0);
            } else {
                $(".no-of-stars span").text(val);
            }
        });

        $(document).on("click", '.review-rating-star .stars', function () {
            var i;
            var onStar = parseInt($(this).data('star'), 10); // The star currently selected
            var stars = $(this).parent().children('.stars');
            var sub_stars = $("#rating-review-popup .review-rating-star").children('.stars');

            for (i = 0; i < stars.length; i++) {
                $(stars[i]).removeClass('selected');
            }

            for (i = 0; i < onStar; i++) {
                $(stars[i]).addClass('selected');
            }

            var val = parseInt($('.review-rating-star .stars.selected').last().data('star'), 10);
            $(".no-of-stars span").text(val);
            // $("#rating-review-popup .no-of-stars span").text(val);
            if(onStar <= 4) {
                $("#rating-review-popup").addClass("active");
                $("#rating-popup").removeClass("active");
                for (i = 0; i < onStar; i++) {
                    $(sub_stars[i]).addClass('selected');
                }
                $("#rating_star").val(onStar);
                $(".no-of-stars span").text(onStar);
            }
        });

        $(document).on("click", "#rating-review-popup .review-modal-close-btn", function (){
            $("#rating-review-popup").removeClass("active");
            save_sticky_chat_widget_box_status(-1);
        });

        $(document).on("click",".<?php echo esc_attr($this->slug) ?>-thanks-box-btn", function(){
            save_sticky_chat_widget_box_status(-1);
            $("#rating-popup").removeClass("active");
        });

        $(document).on("click",".<?php echo esc_attr($this->slug) ?>-later-box-btn", function(){
            $("#rating-popup").removeClass("active");
            save_sticky_chat_widget_box_status(14);
        });

        $(document).on("click",".<?php echo esc_attr($this->slug) ?>-hide-box-btn", function(){
            $("#rating-popup").removeClass("active");
            save_sticky_chat_widget_box_status(-1);
        });

        $(document).on("click", ".review-modal-bg", function (){
            $("#rating-review-popup").removeClass("active");
            $("#rating-popup").removeClass("active");
            save_sticky_chat_widget_box_status(7);
        });

        $(document).on("submit", "#review_form", function (){
            $(".submit-review").prop("disabled", true);
            $.ajax({
                url: "<?php echo esc_url(admin_url("admin-ajax.php")) ?>",
                data: $("#review_form").serialize(),
                type: "post",
                success: function(){
                    $(".submit-review").prop("disabled", false);
                    save_sticky_chat_widget_box_status(-1);
                    $("#rating-review-popup").removeClass("active");
                    $("#rating-popup").removeClass("active");
                },
                error: function (){
                    $(".submit-review").prop("disabled", false);
                    $("#rating-review-popup").removeClass("active");
                    $("#rating-popup").removeClass("active");
                }
            });
            return false;
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
