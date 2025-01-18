<?php
/**
 * Display subscribe box of the plugin.
 *
 * @author  : Ginger Plugins <gingerplugins@gmail.com>
 * @license : GPL2
 * */
defined('ABSPATH') or die('Direct Access is not allowed');
?>
<div class="sign-up-container">
    <div class="sign-up-box">
        <div class="sign-up-data">
            <div class="left-section">
                <img src="<?php echo esc_url(GSB_PLUGIN_URL."dist/admin/images/left-section.png") ?>" class="left-section-img">
            </div>
            <form id="gp_sticky_sign_up" autocomplete="off">
                <div class="right-section">
                    <div class="sign-up-header">
                        <span style="color: #5067F3;">Stay</span>
                        <?php esc_html_e("Updated with All the Latest Implementations and Tips by Subscribing Here. 💼", "sticky-chat-widget") ?>
                    </div>
                    <div class="sign-up-desc">
                        <?php esc_html_e("Subscribe now to get quick updates regarding our new features, the latest developments, exciting offers and discounts.", "sticky-chat-widget") ?>
                    </div>
                    <div class="sign-up-email-box">
                        <input type="email" required="" name="email_id" class="input-email" placeholder="example@domain.com" value="<?php echo esc_attr(get_option('admin_email')); ?>">
                        <button type="submit" class="sign-up-btn"><svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M1 13L7 7L1 1" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/> </svg></button>
                    </div>
                    <div class="sign-up-content">
                        <?php esc_html_e("If you don't want updates in future from us, you can easily unsubscribe with a single click.", "sticky-chat-widget") ?>
                    </div>
                    <div class="skip-link">
                        <a href="#" id="skip_now"><?php esc_html_e("Skip for now", "sticky-chat-widget") ?></a>
                    </div>
                </div>
                <input type="hidden" name="action" value="scw_save_sign_up_info" />
            </form>
        </div>
    </div>
</div>
<script>
    (function($) {
        $(document).on("submit", "#gp_sticky_sign_up", function(e){
            e.preventDefault();
            $(".sign-up-data").addClass("form-loading");
            $.ajax({
                url: "<?php echo esc_url(admin_url('admin-ajax.php')) ?>",
                data: {
                    email_id: $("input[name='email_id']").val(),
                    is_signup : 1,
                    nonce: "<?php echo esc_attr(wp_create_nonce("scw_save_sign_up_info_nonce")) ?>",
                    action: "scw_save_sign_up_info"
                },
                type: 'post',
                success: function(responseText) {
                    $(".sign-up-data").removeClass("form-loading");
                    window.location = '<?php echo esc_url(admin_url())."admin.php?page=sticky-chat-widget&get_popup=1" ?>'
                }
            });
        });

        $(document).on("click", "#skip_now", function(){
            $(".sign-up-data").addClass("form-loading");
            $.ajax({
                url: "<?php echo esc_url(admin_url('admin-ajax.php')) ?>",
                data: {
                    skip: "skip",
                    is_signup : 1,
                    nonce: "<?php echo esc_attr(wp_create_nonce("scw_save_sign_up_info_nonce")) ?>",
                    action: "scw_save_sign_up_info"
                },
                type: 'post',
                success: function(responseText) {
                    $(".sign-up-data").removeClass("form-loading");
                    window.location = '<?php echo esc_url(admin_url())."admin.php?page=sticky-chat-widget&get_popup=1" ?>'
                }
            });
        });
    })(jQuery);
</script>
