<?php
/**
 * The admin specific functionality of the plugin.
 *
 * @author  : Ginger Plugins <gingerplugins@gmail.com>
 * @license : GPL2
 * */

defined('ABSPATH') or die('Direct Access is not allowed');

class GP_Admin_Common_Sticky_Chat_Widget
{

    /**
     * The slug of this plugin.
     *
     * @var    string    $slug    The slug of this plugin.
     * @since  1.1.2
     * @access public
     */
    public $slug = "sticky-chat-widget";

    /**
     * The name of this plugin.
     *
     * @var    string    $pluginName    The name of this plugin.
     * @since  1.1.2
     * @access public
     */
    public $pluginName = "Sticky Chat Widget";

    /**
     * The affiliate link of this plugin.
     *
     * @var    string    $affiliateLink    The affiliate link of this plugin.
     * @since  1.1.2
     * @access public
     */
    public $affiliateLink;

    /**
     *  The response of submit a form.
     *
     * @var    array    $response    The response of submit a form.
     * @since  1.1.2
     * @access public
     */
    public $response;

    /**
     *  The email of this plugin.
     *
     * @var    array    $email    The email of this plugin.
     * @since  1.1.2
     * @access protected
     */
    protected $email = "contact@gingerplugins.com";


    /**
     * Initialize the class and set its properties.
     *
     * This constructor function sets up various actions and hooks to handle admin scripts, menu creation, AJAX requests,
     * and other functionalities related to the Ginger Social Chat Widget plugin. It also initializes default values for
     * the AJAX response array and sets the affiliate link.
     *
     * @since 1.1.2
     */
    public function __construct()
    {
        // Enqueue admin scripts.
        add_action('admin_enqueue_scripts', [ $this, 'admin_script' ]);

        // Create menu in admin for Ginger social chat button settings.
        add_action('admin_menu', [$this, 'admin_menu'], 100);

        add_action("admin_head", [$this, "admin_head"]);

        // Deactivate Plugin.
        add_action("wp_ajax_".$this->slug."-plugin_deactivate_form", [ $this, 'plugin_deactivate_form_request']);

        // Contact Form.
        add_action("wp_ajax_contact_ginger_form_scw", [ $this, 'contact_ginger']);

        // Update review box status.
        add_action("wp_ajax_".$this->slug."_update_review_box_status", [ $this, 'update_review_box_status']);
        // Send email for review.
        add_action("wp_ajax_".$this->slug."_send_email", [ $this, 'review_send_mail']);

        // Rating message.
        add_action('in_admin_header', [$this, 'in_admin_header']);

        // Deactivate form.
        add_action('admin_footer', [$this, 'plugin_deactivate_form']);

        // Set the affiliate link for the plugin.
        $this->affiliateLink = "https://www.gingerplugins.com/affiliate-area/?plugin=".$this->slug."&domain=".$_SERVER['HTTP_HOST'];

        // Initialize default AJAX response array.
        $this->response = [
            'status'  => 0,
            'data'    => 0,
            'errors'  => [],
            'message' => "",
        ];

    }//end __construct()


    /**
     * Update review box status to control the display of a review notice.
     *
     * This function handles the AJAX request to update the review box status based on the provided day interval.
     *
     * @since  1.1.2
     * @return null
     */
    public function update_review_box_status()
    {
        $errorMessage = esc_html__("%1\$s is required", 'sticky-chat-widget');
        $post         = filter_input_array(INPUT_POST);
        $errors       = [];

        // Check if nonce is set and valid.
        if (isset($post['nonce']) && !empty($post['nonce'])) {
            if (wp_verify_nonce($post['nonce'], $this->slug."-review-box-status")) {
                $noOfDays = isset($post['day_interval']) ? $post['day_interval'] : 7;

                // If the day interval is set to -1, set review box status to always show.
                if ($noOfDays == -1) {
                    update_option($this->slug."_review_box_status", 1);
                } else {
                    // Calculate the next date to show the review box based on the day interval.
                    $nextDate = gmdate("Y-m-d", strtotime("+".$noOfDays." days"));
                    update_option($this->slug."_review_date", $nextDate);
                }
            }
        }

    }//end update_review_box_status()


    /**
     * Send mail containing user feedback and rating.
     *
     * This function handles the AJAX request to send an email with user feedback and rating to the specified email address.
     *
     * @since  1.1.2
     * @return null
     */
    public function review_send_mail()
    {
        $post = filter_input_array(INPUT_POST);

        // Check if nonce is set and valid.
        if (isset($post['nonce']) && !empty($post['nonce'])) {
            if (wp_verify_nonce($post['nonce'], $this->slug."-send-mail")) {
                // Prepare the message content with user rating, feedback, and website URL.
                $message = [
                    "Rating"      => $post['rating_star'],
                    "Feedback"    => esc_attr($post['rating_feedback']),
                    "Website URL" => site_url(),
                ];

                // Set the URL for sending the email through the Ginger Plugins API.
                $url          = "https://api.gingerplugins.com/email/send-message.php";
                $args         = [];
                $args['body'] = [
                    'email_id' => 'noreply@gingerplugins.com',
                    'message'  => $message,
                    'subject'  => 'New review for '.$this->pluginName,
                ];

                // Make an HTTP POST request to send the email.
                wp_remote_post($url, $args);
            }//end if
        }//end if

    }//end review_send_mail()


    /**
     * Display the review notice in the admin header.
     *
     * This function is responsible for showing the review notice in the WordPress admin header. It checks the current
     * page, and if it matches specific pages related to the plugin, it removes existing actions for admin notices
     * and then adds the action to display the review notice.
     *
     * @since  1.1.2
     * @return null
     */
    public function in_admin_header()
    {
        // Check if the current page is related to the plugin.
        if (isset($_GET['page']) && ($_GET['page'] == "sticky-chat-widget" || $_GET['page'] == "sticky-chat-widget-upgrade-to-pro" || $_GET['page'] == "sticky-chat-widget-leads" || $_GET['page'] == "sticky-chat-widget-integration" || $_GET['page'] == "sticky-chat-widget-analytics")) {
            // Remove existing actions for admin notices.
            remove_all_actions('admin_notices');
            remove_all_actions('all_admin_notices');
        }

        // Add the action to display the review notice.
        add_action('admin_notices', [$this, 'admin_notices']);

    }//end in_admin_header()


    /**
     * Display the review notice or popup in the WordPress admin.
     *
     * This function checks whether the review notice or popup should be displayed based on certain conditions.
     * It checks if the review notice is hidden, if the review date option is set, and if the current date is after
     * the set review date. If all conditions are met, it includes the appropriate template for display.
     *
     * @since  1.1.2
     * @return null
     */
    public function admin_notices()
    {
        // Check if the review notice is hidden.
        $isHidden = get_option($this->slug."_review_box_status");
        if ($isHidden !== false) {
            return;
        }

        // Check if the review date option is set.
        $reviewDate = get_option($this->slug."_review_date");
        if ($reviewDate === false) {
            // If not set, set the default review date and return.
            $date = gmdate("Y-m-d", strtotime("+7 days"));
            add_option($this->slug."_review_date", $date);
            return;
        }

        // Check if the current date is after the set review date.
        $currentDate = gmdate("Y-m-d");
        if ($currentDate < $reviewDate) {
            return;
        }

        $pages = [
            'sticky-chat-widget',
            'sticky-chat-widget-upgrade-to-pro',
            'sticky-chat-widget-leads',
            'sticky-chat-widget-integration',
            'sticky-chat-widget-analytics',
        ];

        // Check the current page and include the appropriate template.
        if (isset($_GET['page']) && in_array($_GET['page'], $pages)) {
            // Include the review popup template.
            include_once dirname(__FILE__)."/templates/review-popup.php";
        } else {
            // Include the standard admin notice template.
            include_once dirname(__FILE__)."/templates/admin-notice.php";
        }

    }//end admin_notices()


    /**
     * Send mail when deactivating the plugin.
     *
     * This function handles the request to send an email when deactivating the plugin.
     * It validates the provided email address and comment, and if everything is valid,
     * it sends the email containing the deactivation message.
     *
     * @since  1.1.2
     * @return null
     */
    public function plugin_deactivate_form_request()
    {
        // Initialize error messages and retrieve POST data.
        $errorMessage = esc_html__("%1\$s is required", 'sticky-chat-widget');
        $post         = filter_input_array(INPUT_POST);
        $errors       = [];

        // Set a default email address.
        $emailId = "no-reply@domain.com";

        // Validate and retrieve the provided email address.
        if (!isset($post['deactivate_email']) || empty($post['deactivate_email'])) {
            $errors[] = [
                'key'     => 'deactivate_email',
                'message' => sprintf($errorMessage, esc_html__("Email", 'sticky-chat-widget')),
            ];
        } else if (!filter_var($post['deactivate_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = [
                'key'     => 'deactivate_email',
                'message' => esc_html__('Email address is not valid', 'sticky-chat-widget'),
            ];
        } else {
            $emailId = $post['deactivate_email'];
        }

        // Validate and retrieve the provided deactivation comment.
        if (!isset($post['deactivate_comment']) || empty($post['deactivate_comment'])) {
            $errors[] = [
                'key'     => 'deactivate_comment',
                'message' => sprintf($errorMessage, esc_html__("Message", 'sticky-chat-widget')),
            ];
        }

        // Process the request if there are no errors.
        if (empty($errors)) {
            // Validate the nonce.
            if (!isset($post['deactivate_nonce']) || empty($post['deactivate_nonce']) || !wp_verify_nonce($post['deactivate_nonce'], $this->slug."-deactivate-plugin")) {
                $this->response['message'] = esc_html__("Your request is not valid", 'sticky-chat-widget');
            } else {
                global $current_user;
                $name = "Unknown Person";

                // Get the user's name if available.
                if (isset($current_user->first_name) && isset($current_user->first_name)) {
                    $name = trim($current_user->first_name." ".$current_user->last_name);
                }

                // Prepare the message to be sent.
                $message = [
                    "Email"   => esc_attr($emailId),
                    "Message" => $post['deactivate_comment'],
                ];

                // Set up the request to send the email.
                $url          = "https://api.gingerplugins.com/email/send-message.php";
                $args         = [];
                $args['body'] = [
                    'email_id' => $emailId,
                    'message'  => $message,
                    'name'     => $name,
                    'subject'  => $this->pluginName." was removed from ".site_url(),
                ];
                wp_remote_post($url, $args);

                // Set the response status and message.
                $this->response['status']  = 1;
                $this->response['message'] = esc_html__("Your message has been sent successfully", 'sticky-chat-widget');
            }//end if
        }//end if

        // Send the JSON-encoded response and exit.
        echo wp_json_encode($this->response);
        exit;

    }//end plugin_deactivate_form_request()


    /**
     * Display a deactivation form when the user deactivates the plugin.
     *
     * This function is responsible for rendering and displaying a deactivation form
     * when the user attempts to deactivate the plugin. It checks if the current page
     * is the plugins.php page, and if so, includes the template for the deactivation form.
     *
     * @since  1.1.2
     * @return null
     */
    public function plugin_deactivate_form()
    {
        global $pagenow;

        // Check if the current page is plugins.php.
        if ($pagenow !== 'plugins.php') {
            return;
        }

        // Include the template for the deactivation form.
        include_once dirname(__FILE__)."/templates/plugin-deactivate.php";

    }//end plugin_deactivate_form()


    /**
     * Send mail to Ginger Plugins when submitting a help form.
     *
     * This function handles the submission of a help form, validating the form fields
     * such as name, email, and message. If the form is valid, it sends the message to
     * Ginger Plugins using the provided API endpoint. It also includes information about
     * the plugin version, domain, WordPress version, and PHP version.
     *
     * @since  1.1.2
     * @return null
     */
    public function contact_ginger()
    {
        $errorMessage = esc_html__("%1\$s is required", 'sticky-chat-widget');
        $post         = filter_input_array(INPUT_POST);
        $errors       = [];

        // Validate the 'name' field.
        if (!isset($post['name']) || empty($post['name'])) {
            $errors[] = [
                'key'     => 'name',
                'message' => sprintf($errorMessage, esc_html__("Name", 'sticky-chat-widget')),
            ];
        }

        // Validate the 'email' field.
        if (!isset($post['email']) || empty($post['email'])) {
            $errors[] = [
                'key'     => 'email',
                'message' => sprintf($errorMessage, esc_html__("Email", 'sticky-chat-widget')),
            ];
        } else if (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = [
                'key'     => 'email',
                'message' => esc_html__('Email address is not valid', 'sticky-chat-widget'),
            ];
        }

        // Validate the 'message' field.
        if (!isset($post['message']) || empty($post['message'])) {
            $errors[] = [
                'key'     => 'message',
                'message' => sprintf($errorMessage, esc_html__("Message", 'sticky-chat-widget')),
            ];
        }

        // Process the form submission.
        if (empty($errors)) {
            if (!isset($post['nonce']) || empty($post['nonce'])) {
                $this->response['message'] = esc_html__("Your request is not valid", 'sticky-chat-widget');
            } else if (!wp_verify_nonce($post['nonce'], $this->slug."ajax-contact-form")) {
                $this->response['message'] = esc_html__("Your request is not valid", 'sticky-chat-widget');
            } else {
                // Prepare the message data.
                $message = [
                    "Name"              => esc_attr($post['name']),
                    "Email"             => esc_attr($post['email']),
                    "Message"           => esc_attr($post['message']),
                    "Plugin"            => esc_attr($this->pluginName),
                    "Plugin Version"    => esc_attr(GSB_PLUGIN_VERSION),
                    "Domain"            => esc_url(site_url()),
                    "WordPress Version" => esc_attr(get_bloginfo('version')),
                    "PHP Version"       => esc_attr(PHP_VERSION),
                ];

                // Send the message to Ginger Plugins using the API endpoint.
                $url          = "https://api.gingerplugins.com/email/send-message.php";
                $args         = [];
                $args['body'] = [
                    'email_id' => $post['email'],
                    'name'     => $post['name'],
                    'message'  => $message,
                ];
                wp_remote_post($url, $args);

                ob_start(); ?>
                <table cellspacing="0" border="0">
                    <tr>
                        <th align="left">Name</th>
                        <td><?php echo esc_attr($post['name']) ?></td>
                    </tr>
                    <tr>
                        <th align="left">Email</th>
                        <td><?php echo esc_attr($post['email']) ?></td>
                    </tr>
                    <tr>
                        <th align="left">Message</th>
                        <td><?php echo esc_attr($post['message']) ?></td>
                    </tr>
                    <tr>
                        <th align="left">Plugin</th>
                        <td><?php echo esc_attr($this->pluginName) ?></td>
                    </tr>
                    <tr>
                        <th align="left">Plugin Version</th>
                        <td><?php echo esc_attr(GSB_PLUGIN_VERSION) ?></td>
                    </tr>
                    <tr>
                        <th align="left">Domain</th>
                        <td><?php echo esc_url(site_url()) ?></td>
                    </tr>
                    <tr>
                        <th align="left">WordPress Version</th>
                        <td><?php echo esc_attr(get_bloginfo('version')) ?></td>
                    </tr>
                    <tr>
                        <th align="left">PHP Version</th>
                        <td><?php echo esc_attr(PHP_VERSION) ?></td>
                    </tr>
                </table>
                <?php
                $emailMessage = ob_get_clean();

                // Send a mail.
                $headers  = "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                $headers .= 'From:'.$post['name'].' <'.$post['email'].'>'.PHP_EOL;
                $headers .= 'Reply-To:'.$post['name'].'<'.$post['email'].'>'.PHP_EOL;
                $headers .= 'X-Mailer: PHP/'.phpversion();

                $subject = "Message for ".$this->pluginName.": ".site_url();

                // Sending an Email.
                wp_mail($this->email, $subject, $emailMessage, $headers);

                // Set success status and message.
                $this->response['status']  = 1;
                $this->response['message'] = esc_html__("Your message has been sent successfully", 'sticky-chat-widget');
            }//end if
        } else {
            // Set error details if there are validation errors.
            $this->response['errors'] = $errors;
        }//end if

        // Send the response in JSON format.
            echo wp_json_encode($this->response);
        exit;

    }//end contact_ginger()


    /**
     * Enqueue admin style and script files.
     *
     * This function is responsible for enqueueing the necessary JavaScript and CSS files
     * on the WordPress admin pages. It conditionally loads specific files based on the
     * current admin page, ensuring proper functionality and styling.
     *
     * @since  1.1.2
     * @param  string $hook The name of the page in the admin URL.
     * @return null
     */
    public function admin_script($hook)
    {
        // Enqueue scripts and styles for the 'plugins.php' page.
        if ($hook == "plugins.php") {
            wp_enqueue_script($this->slug.'-admin-deactivate-plugin', GSB_PLUGIN_URL."dist/admin/js/deactivate-plugin.js", ['jquery'], GSB_PLUGIN_VERSION, true);
            wp_enqueue_style($this->slug.'-admin-deactivate-plugin', GSB_PLUGIN_URL."dist/admin/css/deactivate-plugin.css", [], GSB_PLUGIN_VERSION);

            // Localize script with settings.
            wp_localize_script(
                $this->slug.'-admin-deactivate-plugin',
                'SCW_SETTINGS',
                [
                    'ajax_url'         => admin_url('admin-ajax.php'),
                    'required_message' => esc_html__("Your comment is required", "sticky-chat-widget"),
                ]
            );
            return;
        }

        if ($hook == "sticky-chat-widget_page_sticky-chat-widget-integration") {
            wp_enqueue_style($this->slug.'-integration-style', GSB_PLUGIN_URL."dist/admin/css/integration.css", [], GSB_PLUGIN_VERSION);
        }

        // Enqueue scripts and styles for specific admin pages.
        $pages = [
            'sticky-chat-widget_page_sticky-chat-widget-upgrade-to-pro',
            'toplevel_page_sticky-chat-widget',
        ];
        if (!in_array($hook, $pages)) {
            return;
        }

        // Determine whether to use minified versions of the files based on development mode.
        $minified = ".min";
        if (GSB_DEV_VERSION) {
            $minified = "";
        }

        // Enqueue common admin script.
        wp_enqueue_script($this->slug.'-admin-script', GSB_PLUGIN_URL."dist/admin/js/common-script.js", ['jquery'], GSB_PLUGIN_VERSION, true);

        // Enqueue common admin style.
        wp_enqueue_style($this->slug.'-admin-style', GSB_PLUGIN_URL."dist/admin/css/admin-style.css", [], GSB_PLUGIN_VERSION);

        // Enqueue the right-to-left (RTL) stylesheet for the GSB plugin's admin section.
        wp_enqueue_style('gsb-rtl-style', GSB_PLUGIN_URL."dist/admin/css/style-rtl.css", [], GSB_PLUGIN_VERSION);

        // Check and enqueue signup styles if needed.
        $flag = get_option($this->slug."-subscribe-hide");
        if ($flag == false) {
            wp_enqueue_style($this->slug.'-signup-style', GSB_PLUGIN_URL."dist/admin/css/sign-up.css", [], GSB_PLUGIN_VERSION);
        }

        // Localize script with common settings.
        wp_localize_script(
            $this->slug.'-admin-script',
            'GP_COMMON_SETTINGS',
            [
                'ajax_url'         => admin_url('admin-ajax.php'),
                'required_message' => esc_html__("%s is required", "sticky-chat-widget"),
            ]
        );

    }//end admin_script()


    /**
     * Add menu and submenu for the plugin in the admin dashboard.
     *
     * This function is responsible for adding a submenu page for the plugin in the WordPress
     * admin dashboard. It specifically adds a "Go Pro" submenu page with the capability
     * to manage options. The submenu page callback is set to the 'admin_upgrade_to_pro' method.
     *
     * @since  1.1.2
     * @return null
     */
    public function admin_menu()
    {
        $formIcons = Ginger_Social_Icons::svg_icons();
        add_submenu_page(
            $this->slug,
            __('Go Pro', 'sticky-chat-widget').$formIcons['pro'],
            __('Go Pro', 'sticky-chat-widget').$formIcons['pro'],
            'manage_options',
            $this->slug."-upgrade-to-pro",
            [
                $this,
                'admin_upgrade_to_pro',
            ]
        );

    }//end admin_menu()


    /**
     * Add custom CSS styles to the WordPress admin head.
     *
     * This function is responsible for injecting custom CSS styles into the head
     * section of the WordPress admin pages. These styles are specific to the
     * Sticky Chat Widget plugin and affect the appearance of certain admin menu items.
     *
     * @return null
     * @since  1.1.2
     */
    function admin_head()
    {
        ?>
        <style>
            #adminmenu .toplevel_page_sticky-chat-widget > ul > li:last-child {
                padding: 5px 10px;
            }
            #adminmenu .toplevel_page_sticky-chat-widget > ul > li:last-child a {
                display: flex;
                background-color: #6d65f5;
                border-radius: 6px;
                font-size: 12px;
                gap: 4px;
                padding: 4px 8px;
                color: #ffffff;
                align-items: center;
                transition: all 0.2s linear;
                font-weight: normal;
                justify-content: center;
                outline: none;
                box-shadow: none;
            }
            #adminmenu .toplevel_page_sticky-chat-widget > ul > li:last-child a svg {
                width: 15px;
                height: 15px;
                display: block;
                vertical-align: middle;
            }
            #adminmenu .toplevel_page_sticky-chat-widget > ul > li:last-child a svg path {
                fill: #fff;
                stroke: #fff;
            }
            #adminmenu .toplevel_page_sticky-chat-widget > ul > li:last-child a:hover {
                color: #ffffff;
                background-color: #3D36B7;
                font-weight: normal;
                box-shadow: none;
            }
        </style>
        <?php

    }//end admin_head()


    /**
     * Display the "Upgrade to Pro" page in the admin dashboard.
     *
     * This function is responsible for rendering the "Upgrade to Pro" page in the WordPress
     * admin dashboard. It includes information about Pro features, pricing, and FAQs. Users are
     * provided with details about various features such as multiple widgets, customization options,
     * integration with Mailchimp & Mailpoet, and more. Additionally, users can find answers to common
     * questions in the FAQs section. The function utilizes arrays to store feature details, pricing
     * information, and FAQs. Users have the option to navigate to the checkout page for purchasing
     * the Pro version.
     *
     * @since  1.1.2
     * @return null
     */
    public function admin_upgrade_to_pro()
    {
        $flag = get_option($this->slug."-subscribe-hide");

        // Check if subscription is hidden.
        if ($flag == "yes") {
            $cartUrl = "https://www.gingerplugins.com/checkout/?edd_action=add_to_cart&download_id=4104&edd_options[price_id]=";

            // Pro feature list.
            $priceFeatures = [
                [
                    'tooltip' => esc_html__("Show different widgets for different pages based on page targeting rules. You can also show different channels on desktop and mobile", 'sticky-chat-widget'),
                    'text'    => esc_html__("Multiple widgets", 'sticky-chat-widget'),
                ],
                [
                    'tooltip' => esc_html__("Upload custom icon, choose custom color for your widget button", 'sticky-chat-widget'),
                    'text'    => esc_html__("Widget customization", 'sticky-chat-widget'),
                ],
                [
                    'tooltip' => esc_html__("Select from 26 channels also upload custom icon", 'sticky-chat-widget'),
                    'text'    => esc_html__("Channel customization", 'sticky-chat-widget'),
                ],
                [
                    'tooltip' => esc_html__("Add custom fields like text, textarea, number, date, website, dropdown, file upload to contact form", 'sticky-chat-widget'),
                    'text'    => esc_html__("Custom fields for contact forms", 'sticky-chat-widget'),
                ],
                [
                    'tooltip' => esc_html__("Get leads directly to your Mailchimp or Mailpoet lists when customers submit their emails through contact form", 'sticky-chat-widget'),
                    'text'    => esc_html__("Integrate contact form data with Mailchimp & Mailpoet", 'sticky-chat-widget'),
                ],
                [
                    'tooltip' => esc_html__("Get the your contact form leads to your email address", 'sticky-chat-widget'),
                    'text'    => esc_html__("Get contact form leads to email", 'sticky-chat-widget'),
                ],
                [
                    'tooltip' => esc_html__("Select pages where you like to show or hide Sticky Chat Widget", 'sticky-chat-widget'),
                    'text'    => esc_html__("Page targeting", 'sticky-chat-widget'),
                ],
                [
                    'tooltip' => esc_html__("Insert custom IDs and class name to each chat channel", 'sticky-chat-widget'),
                    'text'    => esc_html__("Custom IDs and class", 'sticky-chat-widget'),
                ],
                [
                    'tooltip' => esc_html__("Select week day and time when you like to show Sticky Chat Widget", 'sticky-chat-widget'),
                    'text'    => esc_html__("Days and time selection", 'sticky-chat-widget'),
                ],
                [
                    'tooltip' => esc_html__("Show Sticky Chat Widget for selected countries", 'sticky-chat-widget'),
                    'text'    => esc_html__("Country targeting", 'sticky-chat-widget'),
                ],
                [
                    'tooltip' => esc_html__("Show Sticky Chat Widget between selected dates and time", 'sticky-chat-widget'),
                    'text'    => esc_html__("Date and time selection", 'sticky-chat-widget'),
                ],
                [
                    'tooltip' => esc_html__("Display Sticky Chat Widget on custom location", 'sticky-chat-widget'),
                    'text'    => esc_html__("Custom location", 'sticky-chat-widget'),
                ],
                [
                    'tooltip' => esc_html__("Track each channel's click on google analytics", 'sticky-chat-widget'),
                    'text'    => esc_html__("Google analytics", 'sticky-chat-widget'),
                ],
            ];

            $faqs = [
                [
                    "question" => esc_html__("Can I cancel my account at any time?", 'sticky-chat-widget'),
                    "answer"   => esc_html__("Yes, if you ever decide that Sticky Chat Widget isn't the best plugin for you, simply cancel your account from your Account panel. You'll still be able to use the plugin without updates or support.", 'sticky-chat-widget'),
                ],
                [
                    "question" => esc_html__("Can I change my plan later on?", 'sticky-chat-widget'),
                    "answer"   => esc_html__("Absolutely! You can upgrade or downgrade your plan at any time.", 'sticky-chat-widget'),
                ],
                [
                    "question" => esc_html__("What payment methods are accepted?", 'sticky-chat-widget'),
                    "answer"   => esc_html__("We accept all major credit cards including Visa, Mastercard, American Express, as well as PayPal payments.", 'sticky-chat-widget'),
                ],
                [
                    "question" => esc_html__("Do I get updates for the premium plugin?", 'sticky-chat-widget'),
                    "answer"   => esc_html__("Yes! Automatic updates to our premium plugin are available free of charge as long as you stay our paying customer. If you cancel your subscription, you'll still be able to use our plugin without updates or support.", 'sticky-chat-widget'),
                ],
                [
                    "question" => esc_html__("Do you offer support if I need help?", 'sticky-chat-widget'),
                    "answer"   => esc_html__("Yes! Top-notch customer support for our paid as well as free customers. We'll do our very best to resolve any issues you encounter while using sticky chat widget.", 'sticky-chat-widget'),
                ],
            ];

            // Pro Price List.
            $prices = [
                "1_domain"   => [
                    '1_year'   => [
                        'price' => "$25",
                        'id'    => 9,
                    ],

                    'lifetime' => [
                        'price' => "$79",
                        'id'    => 10,
                    ],
                ],
                "5_domains"  => [
                    '1_year'   => [
                        'price' => "$69",
                        'id'    => 11,
                    ],

                    'lifetime' => [
                        'price' => "$179",
                        'id'    => 12,
                    ],
                ],
                "50_domains" => [
                    '1_year'   => [
                        'price' => "$99",
                        'id'    => 13,
                    ],

                    'lifetime' => [
                        'price' => "$279",
                        'id'    => 14,
                    ],
                ],
            ];

            $first = "Use Sticky Chat Widget on ";

            include_once dirname(__FILE__)."/templates/upgrade-to-pro.php";
        } else {
            // Include subscribe template if subscription is not hidden.
            include_once dirname(__FILE__)."/templates/subscribe.php";
        }//end if

    }//end admin_upgrade_to_pro()


}//end class


$GP_admin_common_sticky_chat_widget = new GP_Admin_Common_Sticky_Chat_Widget();
