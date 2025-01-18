<?php
/**
 * Upgrade to pro page of the plugin.
 *
 * @author  : Ginger Plugins <gingerplugins@gmail.com>
 * @license : GPL2
 * */
defined('ABSPATH') or die('Direct Access is not allowed');


$faqs = [
    [
        'question' => esc_html__("What is your refund and return policy for Sticky Chat Widget purchases?", "sticky-chat-widget"),
        'answer'   => esc_html__("We are committed to giving you the best plugin experience to help you improve your business. If you find that Sticky Chat Widget is not suitable for your business needs, we offer a hassle-free 100% money-back guarantee within 30 days of purchase", "sticky-chat-widget"),
    ],
    [
        'question' => esc_html__("Will Sticky Chat Widget stop working if I don’t renew my license?", "sticky-chat-widget"),
        'answer'   => esc_html__("Of course NOT!, Sticky Chat Widget plugin and all your settings will continue to work as before; however, you will no longer receive plugin updates including feature additions, improvements, and support", "sticky-chat-widget"),
    ],
    [
        'question' => esc_html__("Do I need separate license key for my staging or development site?", "sticky-chat-widget"),
        'answer'   => esc_html__("No, we do not consider domains like staging.*, dev.*, local.*, localhost in activation domain list", "sticky-chat-widget"),
    ],
    [
        'question' => esc_html__("Can I upgrade my plan in future if needed?", "sticky-chat-widget"),
        'answer'   => esc_html__("Yes, you can upgrade your plan from Basic to Business or Enterprise and Business to Enterprise", "sticky-chat-widget"),
    ],
    [
        'question' => esc_html__("Can I use Sticky chat widget on multiple domains?", "sticky-chat-widget"),
        'answer'   => esc_html__("Yes, you can use free version on any numbers of domains, while for Pro version you need to buy Business or Enterprise plan", "sticky-chat-widget"),
    ],
]
?>

<div class="wrap">
    <h2></h2>
    <div class="ginger-box-layout">
        <div class="ginger-box no-bg">
            <div class="plan-title"><?php esc_html_e("Choose the best plan which is suitable to you", "sticky-chat-widget") ?></div>
            <div class="pricing-switch">
                <div class="pricing-switch-box">
                    <div class="plan-duration"><a href="javascript:;" class="monthly-plan active"><?php esc_html_e("Yearly Plan", "sticky-chat-widget") ?></a></div>
                    <div class="plan-duration"><a href="javascript:;" class="year-plan"><?php esc_html_e("Lifetime Plan", "sticky-chat-widget") ?></a></div>
                </div>
            </div>
            <div class="pricing-tables" id="sticky-price-plan">
                <div class="row">
                    <div class="col-md-4 col-sm-12">
                        <div class="price-table table-1">
                            <div class="table-head">
                                <div class="package-header">
                                    <div class="package-name"><?php esc_html_e("Basic Plan", 'sticky-chat-widget') ?></div>
                                    <div class="package-price"><?php echo esc_attr($prices['1_domain']['1_year']['price']) ?><span><?php esc_html_e("/year", 'sticky-chat-widget') ?></span></div>
                                </div>
                                <div class="package-desc"><?php esc_html_e("Renewals at 25% off", 'sticky-chat-widget') ?></div>
                            </div>
                            <div class="table-body">
                                <ul>
                                    <li><a href="javascript:;" class='tooltip'><span class='dashicons dashicons-yes'></span><?php echo "1 domain" ?> <span class='tooltip-text'><?php echo esc_attr($first)." 1 Domain" ?></span></a></li>
                                    <?php if (!empty($priceFeatures)) {
                                        foreach ($priceFeatures as $key => $feature) {
                                            if (isset($feature['tooltip']) && !empty($feature['tooltip'])) {
                                                echo "<li class='li-".esc_attr($key)."'><a class='tooltip' href='javascript:;'><span class='dashicons dashicons-yes'></span>".esc_attr($feature['text'])."<span class='tooltip-text'>".esc_attr($feature['tooltip'])."</span></a></li>";
                                            } else {
                                                echo "<li><span class='dashicons dashicons-yes'></span>".esc_attr($feature['text'])."</li>";
                                            }
                                        }
                                    } ?>
                                </ul>
                            </div>
                            <div class="plan-price-bottom">
                                <div class="price-switch">
                                    <div class="yearly-plan active text-right" data-price="<?php echo esc_attr($prices['1_domain']['1_year']['price']) ?>" data-url="<?php echo esc_attr($cartUrl).esc_attr($prices['1_domain']['1_year']['id']) ?>" data-plan="<?php esc_html_e("/year", 'sticky-chat-widget') ?>" data-desc="<?php esc_html_e("Renewals at 25% off", 'sticky-chat-widget') ?>">
                                        <label for="basic-plan">
                                            <div class="plan-price"><?php echo esc_attr($prices['1_domain']['1_year']['price']) ?></div>
                                            <div class="plan-desc"><?php esc_html_e("per year", 'sticky-chat-widget') ?></div>
                                        </label>
                                    </div>
                                    <div class="plan-switch">
                                        <input id="basic-plan" type="checkbox" class="sr-only">
                                        <label class="checkbox-switch" for="basic-plan"></label>
                                    </div>
                                    <div class="annualy-plan text-left" data-price="<?php echo esc_attr($prices['1_domain']['lifetime']['price']) ?>" data-url="<?php echo esc_attr($cartUrl).esc_attr($prices['1_domain']['lifetime']['id']) ?>" data-plan="<?php esc_html_e("/lifetime", 'sticky-chat-widget') ?>" data-desc="<?php esc_html_e("Lifetime updates & support", 'sticky-chat-widget') ?>">
                                        <label for="basic-plan">
                                            <div class="plan-price"><?php echo esc_attr($prices['1_domain']['lifetime']['price']) ?></div>
                                            <div class="plan-desc"><?php esc_html_e("lifetime", 'sticky-chat-widget') ?></div>
                                        </label>
                                    </div>
                                </div>
                                <div class="table-footer">
                                    <a class="checkout-url" target="_blank" href="<?php echo esc_attr($cartUrl).esc_attr($prices['1_domain']['1_year']['id']) ?>"><?php esc_html_e("Get started", 'sticky-chat-widget') ?></a>
                                </div>
                                <div class="plan-bottom-position absolute"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="price-table table-2">
                            <div class="table-head">
                                <div class="package-header">
                                    <div class="package-name"><?php esc_html_e("Business Plan", 'sticky-chat-widget') ?></div>
                                    <div class="package-price"><?php echo esc_attr($prices['5_domains']['1_year']['price']) ?><span><?php esc_html_e("/year", 'sticky-chat-widget') ?></span></div>
                                </div>
                                <div class="package-desc"><?php esc_html_e("Renewals at 25% off", 'sticky-chat-widget') ?></div>
                            </div>
                            <div class="table-body">
                                <ul>
                                    <li><a href="javascript:;" class='tooltip'><span class='dashicons dashicons-yes'></span><?php echo "5 domains" ?> <span class='tooltip-text'><?php echo esc_attr($first)." 5 Domains" ?></span></a></li>
                                    <?php if (!empty($priceFeatures)) {
                                        foreach ($priceFeatures as $key => $feature) {
                                            if (isset($feature['tooltip']) && !empty($feature['tooltip'])) {
                                                echo "<li class='li-".esc_attr($key)."'><a class='tooltip' href='javascript:;'><span class='dashicons dashicons-yes'></span>".esc_attr($feature['text'])."<span class='tooltip-text'>".esc_attr($feature['tooltip'])."</span></a></li>";
                                            } else {
                                                echo "<li><span class='dashicons dashicons-yes'></span>".esc_attr($feature['text'])."</li>";
                                            }
                                        }
                                    } ?>
                                </ul>
                            </div>
                            <div class="plan-price-bottom">
                                <div class="price-switch">
                                    <div class="yearly-plan active text-right" data-price="<?php echo esc_attr($prices['5_domains']['1_year']['price']) ?>" data-url="<?php echo esc_attr($cartUrl).esc_attr($prices['5_domains']['1_year']['id']) ?>" data-plan="<?php esc_html_e("/year", 'sticky-chat-widget') ?>" data-desc="<?php esc_html_e("Renewals at 25% off", 'sticky-chat-widget') ?>">
                                        <label for="pro-plan">
                                            <div class="plan-price"><?php echo esc_attr($prices['5_domains']['1_year']['price']) ?></div>
                                            <div class="plan-desc"><?php esc_html_e("per year", 'sticky-chat-widget') ?></div>
                                        </label>
                                    </div>
                                    <div class="plan-switch">
                                        <input id="pro-plan" type="checkbox" class="sr-only">
                                        <label class="checkbox-switch" for="pro-plan"></label>
                                    </div>
                                    <div class="annualy-plan text-left" data-price="<?php echo esc_attr($prices['5_domains']['lifetime']['price']) ?>" data-url="<?php echo esc_attr($cartUrl).esc_attr($prices['5_domains']['lifetime']['id']) ?>" data-plan="<?php esc_html_e("/lifetime", 'sticky-chat-widget') ?>" data-desc="<?php esc_html_e("Lifetime updates & support", 'sticky-chat-widget') ?>">
                                        <label for="pro-plan">
                                            <div class="plan-price"><?php echo esc_attr($prices['5_domains']['lifetime']['price']) ?></div>
                                            <div class="plan-desc"><?php esc_html_e("lifetime", 'sticky-chat-widget') ?></div>
                                        </label>
                                    </div>
                                </div>
                                <div class="table-footer">
                                    <a class="checkout-url" target="_blank" href="<?php echo esc_attr($cartUrl).esc_attr($prices['5_domains']['1_year']['id']) ?>"><?php esc_html_e("Get started", 'sticky-chat-widget') ?></a>
                                </div>
                                <div class="plan-bottom-position absolute"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="price-table table-3">
                            <div class="table-head">
                                <div class="package-header">
                                    <div class="package-name"><?php esc_html_e("Enterprise Plan", 'sticky-chat-widget') ?></div>
                                    <div class="package-price"><?php echo esc_attr($prices['50_domains']['1_year']['price']) ?><span><?php esc_html_e("/year", 'sticky-chat-widget') ?></span></div>
                                </div>
                                <div class="package-desc"><?php esc_html_e("Renewals at 25% off", 'sticky-chat-widget') ?></div>
                            </div>
                            <div class="table-body">
                                <ul>
                                    <li><a href="javascript:;" class='tooltip'><span class='dashicons dashicons-yes'></span><?php echo "50 domains" ?> <span class='tooltip-text'><?php echo esc_attr($first)." 50 Domains" ?></span></a></li>
                                    <?php if (!empty($priceFeatures)) {
                                        foreach ($priceFeatures as $key => $feature) {
                                            if (isset($feature['tooltip']) && !empty($feature['tooltip'])) {
                                                echo "<li class='li-".esc_attr($key)."'><a class='tooltip' href='javascript:;'><span class='dashicons dashicons-yes'></span>".esc_attr($feature['text'])."<span class='tooltip-text'>".esc_attr($feature['tooltip'])."</span></a></li>";
                                            } else {
                                                echo "<li><span class='dashicons dashicons-yes'></span>".esc_attr($feature['text'])."</li>";
                                            }
                                        }
                                    } ?>
                                </ul>
                            </div>
                            <div class="plan-price-bottom">
                                <div class="price-switch">
                                    <div class="yearly-plan active text-right" data-price="<?php echo esc_attr($prices['50_domains']['1_year']['price']) ?>" data-url="<?php echo esc_attr($cartUrl).esc_attr($prices['50_domains']['1_year']['id']) ?>" data-plan="<?php esc_html_e("/year", 'sticky-chat-widget') ?>" data-desc="<?php esc_html_e("Renewals at 25% off", 'sticky-chat-widget') ?>">
                                        <label for="enterprise-plan">
                                            <div class="plan-price"><?php echo esc_attr($prices['50_domains']['1_year']['price']) ?></div>
                                            <div class="plan-desc"><?php esc_html_e("per year", 'sticky-chat-widget') ?></div>
                                        </label>
                                    </div>
                                    <div class="plan-switch">
                                        <input id="enterprise-plan" type="checkbox" class="sr-only">
                                        <label class="checkbox-switch" for="enterprise-plan"></label>
                                    </div>
                                    <div class="annualy-plan text-left" data-price="<?php echo esc_attr($prices['50_domains']['lifetime']['price']) ?>" data-url="<?php echo esc_attr($cartUrl).esc_attr($prices['50_domains']['lifetime']['id']) ?>" data-plan="<?php esc_html_e("/lifetime", 'sticky-chat-widget') ?>" data-desc="<?php esc_html_e("Lifetime updates & support", 'sticky-chat-widget') ?>">
                                        <label for="enterprise-plan">
                                            <div class="plan-price"><?php echo esc_attr($prices['50_domains']['lifetime']['price']) ?></div>
                                            <div class="plan-desc"><?php esc_html_e("lifetime", 'sticky-chat-widget') ?></div>
                                        </label>
                                    </div>
                                </div>
                                <div class="table-footer">
                                    <a class="checkout-url" target="_blank" href="<?php echo esc_attr($cartUrl).esc_attr($prices['50_domains']['1_year']['id']) ?>"><?php esc_html_e("Get started", 'sticky-chat-widget') ?></a>
                                </div>
                                <div class="plan-bottom-position absolute"></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="gp-faq-section">
        <div class="ginger-box no-bg">
            <div class="gp-faq-box">
                <div class="gp-faq-box-left">
                    <div class="support-title">
                        <?php esc_html_e("Support", 'sticky-chat-widget') ?>
                    </div>
                    <div class="faqs-title">
                        <?php esc_html_e("FAQs", 'sticky-chat-widget') ?>
                    </div>
                    <div class="support-desc">
                        <p><?php esc_html_e("Everything you need to know about product and billing. Can't find answer what you're looking for?", 'sticky-chat-widget') ?></p>
                        <p><?php echo sprintf(esc_html__("Please %1\$s", 'sticky-chat-widget'), "<a href='mailto:contact@gingerplugins.com?subject=Need help with Sticky Chat Widget'>".esc_html__("chat to our friendly team", 'sticky-chat-widget')."</a>") ?></p>
                    </div>
                </div>
                <div class="gp-faq-box-right">
                    <div class="faqs-items">
                        <?php foreach ($faqs as $key => $faq) { ?>
                            <div class="faq-item <?php echo esc_attr(($key == 0) ? "active" : "") ?>">
                                <div class="faq-question">
                                    <?php echo esc_attr($faq['question']) ?>
                                </div>
                                <div class="faq-answer">
                                    <?php echo esc_attr($faq['answer']) ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
