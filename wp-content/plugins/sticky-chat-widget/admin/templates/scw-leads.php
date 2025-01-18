<?php
/**
 * Leads list functionality of the plugin.
 *
 * @author  : Ginger Plugins <gingerplugins@gmail.com>
 * @license : GPL2
 * */
defined('ABSPATH') or die('Direct Access is not allowed');


global $wpdb;
$numOfRecords = 10;
$tableName    = $wpdb->prefix.'scw_contact_form_leads';
$page         = filter_input(INPUT_GET, 'paged');
$paged        = isset($page)&&!empty($page)&&is_numeric($page)&&$page > 0 ? sanitize_text_field($page) : 1;
$start        = (($paged - 1) * $numOfRecords);
$formIcons    = Ginger_Social_Icons::svg_icons();

$startDate = filter_input(INPUT_GET, "start_date");
if (isset($startDate) && !empty($startDate)) {
    $startDate    = sanitize_text_field($startDate);
    $startDate    = gmdate("Y-m-d", strtotime($startDate));
    $startDateSet = gmdate("Y-m-d H:i:s", strtotime($startDate));
} else {
    $startDate    = "";
    $startDateSet = "";
}

$endDate = filter_input(INPUT_GET, "end_date");
if (isset($endDate) && !empty($endDate)) {
    $endDate    = sanitize_text_field($endDate);
    $endDate    = gmdate("Y-m-d", strtotime($endDate));
    $endDateSet = gmdate("Y-m-d H:i:s", strtotime($endDate."23:59:59"));
} else {
    $endDate    = "";
    $endDateSet = "";
}

$search = filter_input(INPUT_GET, "search_lead");
if (isset($search) && !empty($search)) {
    $search = sanitize_text_field($search);
} else {
    $search = "";
}

$query   = "SELECT * FROM $tableName ";
$prepare = [];
if ($startDateSet != "" && $endDateSet != "") {
    $query    .= "where ( created_on >= '%s' AND created_on <= '%s' )";
    $prepare[] = esc_sql($startDateSet);
    $prepare[] = esc_sql($endDateSet);
} else if ($startDateSet != "") {
    $query    .= "where ( created_on >= '%s' )";
    $prepare[] = esc_sql($startDateSet);
} else if ($endDateSet != "") {
    $query    .= "where ( created_on <= '%s' )";
    $prepare[] = esc_sql($endDateSet);
}

if ($search != "") {
    $searchable = '%'.$search.'%';
    if ($startDateSet != "" || $endDateSet != "") {
        $query .= " AND ";
    } else {
        $query .= " WHERE ";
    }

    $query    .= "(name LIKE %s OR email LIKE %s OR phone LIKE %s OR message LIKE %s)";
    $prepare[] = esc_sql($searchable);
    $prepare[] = esc_sql($searchable);
    $prepare[] = esc_sql($searchable);
    $prepare[] = esc_sql($searchable);
}

$query    .= " LIMIT %d, %d";
$prepare[] = $start;
$prepare[] = $numOfRecords;

if (!empty($prepare)) {
    $query = $wpdb->prepare($query, $prepare);
}

$postData   = $wpdb->get_results($query);
$noRecords  = (empty($postData) && $paged == 1) ? 1 : 0;
$totalCount = $wpdb->get_results("SELECT * FROM $tableName");
?>

<div class="gp-box mt-40">
    <div class="dashboard-header">
        <div class="dashboard-header-left">
            <div class="gp-page-title">
                <?php esc_html_e("Leads", "sticky-chat-widget") ?>
            </div>
        </div>
        <div class="dashboard-header-right">

        </div>
    </div>


        <input type="hidden" name="page" value="sticky-chat-widget-leads">

    <?php if ($totalCount) { ?>
    <div class="filter-inputs">
        <div class="date-filter">
            <div class="gp-form-field ">
                <div class="gp-form-label">
                    <label for=""><?php esc_html_e("Start date", "sticky-chat-widget") ?></label>
                </div>
                <div class="gp-form-input calender-input">
                    <input type="text" name="start_date" id="filter-start-date" value="<?php echo esc_attr($startDate) ?>">
                </div>
            </div>
            <div class="gp-form-field">
                <div class="gp-form-label">
                    <label for=""><?php esc_html_e("End date", "sticky-chat-widget") ?></label>
                </div>
                <div class="gp-form-input calender-input">
                    <input type="text" name="end_date" id="filter-end-date" value="<?php echo esc_attr($endDate) ?>">
                </div>
            </div>
        </div>
        <div class="gp-form-field">
            <div class="gp-form-label">
                <label for=""><?php esc_html_e("Search", "sticky-chat-widget") ?></label>
            </div>
            <div class="gp-form-input">
                <input type="text" placeholder="<?php esc_html_e("Search...", "sticky-chat-widget") ?>" name="search_lead" id="search-filter" value="<?php echo esc_attr($search) ?>">
            </div>
        </div>
        <button type="submit" class="submit-filter gp-action-button"><?php esc_html_e("Apply", "sticky-chat-widget") ?></button>
    </div>
    <?php }//end if
    ?>
    <input type="hidden" id="remove_leads_nonce" value="<?php echo esc_attr(wp_create_nonce("remove_leads_nonce")) ?>">
    <input type="hidden" id="remove_all_leads_nonce" value="<?php echo esc_attr(wp_create_nonce("remove_all_leads_nonce")) ?>">
    <div class="dashboard-table leads-record responsive-table">
        <div id="ajax-table">
            <div id="ajax-table-data">
                <?php if (isset($postData) && !empty($postData)) {
                    ?>
                <table>
                    <thead>
                    <tr>
                        <th class="col-status"></th>
                        <th class="col-date"><?php esc_html_e("Date", "sticky-chat-widget") ?></th>
                        <th class="col-name"><?php esc_html_e("Name", "sticky-chat-widget") ?></th>
                        <th class="col-email"><?php esc_html_e("Email", "sticky-chat-widget") ?></th>
                        <th class="col-phone"><?php esc_html_e("Phone number", "sticky-chat-widget") ?></th>
                        <th class="col-message"><?php esc_html_e("Message", "sticky-chat-widget") ?></th>
                        <th class="col-link"><?php esc_html_e("Page Url", "sticky-chat-widget") ?></th>
                        <th class="action-col"><?php esc_html_e("Delete", "sticky-chat-widget") ?></th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($postData as $post) {
                            ?>
                        <tr>
                            <td class="col-status">
                                <span class="checkbox-custom">
                                    <input id="leads_selected_<?php echo esc_attr($post->id) ?>" type="checkbox" name="leads_id" class="sr-only leads_selected" value="<?php echo esc_attr($post->id) ?>">
                                    <label for="leads_selected_<?php echo esc_attr($post->id) ?>"></label>
                                </span>
                            </td>
                            <?php $created_on = strtotime($post->created_on); ?>
                            <td class="col-date"><?php echo nl2br(esc_attr(gmdate(get_option('date_format')."\n".get_option('time_format'), esc_attr($created_on))))  ?></td>
                            <td class="col-name"><?php echo esc_attr($post->name) ?></td>
                            <td class="col-email"><?php echo esc_attr($post->email) ?></td>
                            <td class="col-phone"><?php echo esc_attr($post->phone) ?></td>
                            <td class="col-message"><?php echo nl2br(esc_attr($post->message)) ?></td>
                            <td class="col-link"><a class="" href="<?php echo esc_attr($post->page_url) ?>" target="_blank"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['to_link']); ?></a></td>
                            <td class="action-col"><span class="delete-single-lead trash-record" data-id="<?php echo esc_attr($post->id) ?>" data-nonce="<?php echo esc_attr(wp_create_nonce("remove_single_lead_nonce".esc_attr($post->id))) ?>"><?php Ginger_Social_Icons::load_and_sanitize_svg($formIcons['trash']); ?></span></td>
                        </tr>
                            <?php
                        } ?>

                    </tbody>
                </table>

                <input type="hidden" id="filter_start" value="<?php echo esc_attr($startDate) ?>">
                <input type="hidden" id="filter_end" value="<?php echo esc_attr($endDate) ?>">

                <div class="ajax-pagination">
                    <?php
                    $tableName = $wpdb->prefix.'scw_contact_form_leads';
                    $query     = "SELECT count(*) FROM $tableName ";
                    $prepare   = [];
                    if ($startDateSet != "" && $endDateSet != "") {
                        $query    .= "where ( created_on >= '%s' AND created_on <= '%s' )";
                        $prepare[] = esc_sql($startDateSet);
                        $prepare[] = esc_sql($endDateSet);
                    } else if ($startDateSet != "") {
                        $query    .= "where ( created_on >= '%s' )";
                        $prepare[] = esc_sql($startDateSet);
                    } else if ($endDateSet != "") {
                        $query    .= "where ( created_on <= '%s' )";
                        $prepare[] = esc_sql($endDateSet);
                    }

                    if ($search != "") {
                        $searchable = '%'.$search.'%';
                        if ($startDateSet != "" || $endDateSet != "") {
                            $query .= " AND ";
                        } else {
                            $query .= " WHERE ";
                        }

                        $query    .= "(name LIKE %s OR email LIKE %s OR phone LIKE %s OR message LIKE %s)";
                        $prepare[] = esc_sql($searchable);
                        $prepare[] = esc_sql($searchable);
                        $prepare[] = esc_sql($searchable);
                        $prepare[] = esc_sql($searchable);
                    }


                    if (!empty($prepare)) {
                        $query = $wpdb->prepare($query, $prepare);
                    }

                    $totalTestimonials = $wpdb->get_var($query);


                    $totalPages = ceil($totalTestimonials / $numOfRecords);

                    if ($totalPages > 1) {
                        $pages       = filter_input(INPUT_GET, 'paged');
                        $currentPage = isset($pages) ? sanitize_text_field($pages) : 1;

                        echo '<div class="gp-navigation">';

                        echo paginate_links(
                            [
                                'base'      => get_pagenum_link(1).'%_%',
                                'format'    => '&paged=%#%',
                                'current'   => $currentPage,
                                'total'     => $totalPages,
                                'prev_text' => '<i class="fas fa-angle-left"></i> '.esc_html__("Prev"),
                                'next_text' => esc_html__("Next ").'<i class="fas fa-angle-right"></i>',
                                'type'      => 'list',
                            ]
                        );

                        echo '</div>';
                    }
                    ?>
                </div>
                    <div class="action-btn-row">
                        <a href="javascript:;" class="gp-action-button leads-export" data-nonce="<?php echo esc_attr(wp_create_nonce("export_data_nonce")) ?>"><?php esc_html_e("Download CSV", "sticky-chat-widget") ?></a>
                        <a class="gp-action-button danger delete-leads disabled"><?php esc_html_e("Remove", "sticky-chat-widget") ?></a>
                        <a class="gp-action-button danger delete-all-leads"><?php esc_html_e("Remove all records", "sticky-chat-widget") ?></a>
                        <div class="clear"></div>
                    </div>
                <?php } else {
                    ?>
                    <div class="no-records">
                    <svg xmlns="http://www.w3.org/2000/svg" class="A" id="A" viewBox="0 0 500 500"><style><![CDATA[svg#A.A #B{animation:1s 1 forwards cubic-bezier(.36,-0.01,.5,1.38) lightSpeedRight;animation-delay:0s}svg#A.A #C{animation:1s 1 forwards cubic-bezier(.36,-0.01,.5,1.38) lightSpeedLeft;animation-delay:0s}svg#A.A #D{animation:1s 1 forwards cubic-bezier(.36,-0.01,.5,1.38) zoomIn;animation-delay:0s}svg#A.A #E{animation:1s 1 forwards cubic-bezier(.36,-0.01,.5,1.38) slideUp;animation-delay:0s}@keyframes lightSpeedRight{from{transform:translate3d(50%, 0, 0) skewX(-20deg);opacity:0}60%{transform:skewX(10deg);opacity:1}80%{transform:skewX(-2deg)}to{opacity:1;transform:translate3d(0, 0, 0)}} @keyframes lightSpeedLeft{from{transform:translate3d(-50%, 0, 0) skewX(20deg);opacity:0}60%{transform:skewX(-10deg);opacity:1}80%{transform:skewX(2deg)}to{opacity:1;transform:translate3d(0, 0, 0)}} @keyframes zoomIn{0%{opacity:0;transform:scale(0.5)}100%{opacity:1;transform:scale(1)}} @keyframes slideUp{0%{opacity:0;transform:translateY(30px)}100%{opacity:1;transform:inherit}}.C{fill:#a388e9}.D{fill:#ebebeb}.E{fill:#263238}.F{fill:#e6e6e6}.G{fill:#fafafa}.H{opacity:.6}.I{fill:#f0f0f0}.J{fill:#ffb573}.K{fill:#fff}.L{fill:#f5f5f5}]]></style><g id="B" class="B" transform-origin="250px 228.23px"><path class="D" d="M0 382.4h500v.25H0z" transform-origin="250px 382.525px"/><path class="D" d="M416.78 398.49h33.12v.25h-33.12z" transform-origin="433.34px 398.615px"/><path class="D" d="M322.53 401.21h8.69v.25h-8.69z" transform-origin="326.875px 401.335px"/><path class="D" d="M396.59 389.21h19.19v.25h-19.19z" transform-origin="406.185px 389.335px"/><path class="D" d="M52.46 390.89h43.19v.25H52.46z" transform-origin="74.055px 391.015px"/><path class="D" d="M104.56 390.89h6.33v.25h-6.33z" transform-origin="107.725px 391.015px"/><path class="D" d="M131.47 395.11h93.68v.25h-93.68z" transform-origin="178.31px 395.235px"/><path d="M237,337.8H43.91a5.71,5.71,0,0,1-5.7-5.71V60.66A5.71,5.71,0,0,1,43.91,55H237a5.71,5.71,0,0,1,5.71,5.71V332.09A5.71,5.71,0,0,1,237,337.8ZM43.91,55.2a5.46,5.46,0,0,0-5.45,5.46V332.09a5.46,5.46,0,0,0,5.45,5.46H237a5.47,5.47,0,0,0,5.46-5.46V60.66A5.47,5.47,0,0,0,237,55.2Z" class="D" transform-origin="140.46px 196.4px"/><path d="M453.31 337.8h-193.1a5.72 5.72 0 0 1-5.71-5.71V60.66a5.72 5.72 0 0 1 5.71-5.66h193.1a5.71 5.71 0 0 1 5.69 5.66v271.43a5.71 5.71 0 0 1-5.69 5.71zM260.21 55.2a5.47 5.47 0 0 0-5.46 5.46v271.43a5.47 5.47 0 0 0 5.46 5.46h193.1a5.47 5.47 0 0 0 5.46-5.46V60.66a5.47 5.47 0 0 0-5.46-5.46z" class="D" transform-origin="356.75px 196.4px"/><g class="F"><path d="M289.69 83.83h137.78v90.23H289.69z" transform-origin="358.58px 128.945px" transform="rotate(180)"/></g><g class="I"><path d="M285.49 83.83h140.02v90.23H285.49z" transform-origin="355.5px 128.945px" transform="rotate(180)"/></g><g class="F"><path d="M289.69 174.06h137.78v17.71H289.69z" transform-origin="358.58px 182.915px" transform="rotate(180)"/></g><g class="I"><path d="M278.48 174.06H418.5v17.71H278.48z" transform-origin="348.49px 182.915px" transform="rotate(180)"/></g><g class="G"><path d="M316.27 64.82h78.46v128.25h-78.46z" transform-origin="355.5px 128.945px" transform="rotate(90)"/></g><path class="K" d="M390.7 168.17l-16.38-78.45h-25.56l16.38 78.45h25.56z" transform-origin="369.73px 128.945px"/><path d="M416.9 162.32a.42.42 0 0 0 .42-.43V93.55a.42.42 0 0 0-.42-.42.41.41 0 0 0-.42.42v68.34a.42.42 0 0 0 .42.43z" class="I" transform-origin="416.9px 127.725px"/><path class="K" d="M359.65 168.17l-16.38-78.45h-9.97l16.39 78.45h9.96z" transform-origin="346.475px 128.945px"/><g class="F"><path d="M252.52 128.57h78.46v.75h-78.46z" transform-origin="291.75px 128.945px" transform="rotate(90)"/></g><g class="D"><path class="H" d="M284.1 98.59h137.78l.54-6.59H284.64l-.54 6.59z" transform-origin="353.26px 95.295px"/></g><g class="D"><path class="H" d="M284.1 109.39h137.78l.54-6.58H284.64l-.54 6.58z" transform-origin="353.26px 106.1px"/></g><g class="D"><path class="H" d="M284.1 120.19h137.78l.54-6.58H284.64l-.54 6.58z" transform-origin="353.26px 116.9px"/></g><g class="D"><path class="H" d="M284.1 131h137.78l.54-6.59H284.64l-.54 6.59z" transform-origin="353.26px 127.705px"/></g><g class="D"><path class="H" d="M284.1 141.8h137.78l.54-6.59H284.64l-.54 6.59z" transform-origin="353.26px 138.505px"/></g><g class="D"><path class="H" d="M284.1 152.6h137.78l.54-6.59H284.64l-.54 6.59z" transform-origin="353.26px 149.305px"/></g><path class="F" d="M378.8 316.78h28.89v5.7H378.8z" transform-origin="393.245px 319.63px"/><g class="F"><path d="M324.31 251.33h5.33V382.4h-5.33z" transform-origin="326.975px 316.865px" transform="rotate(180)"/></g><g class="L"><path d="M305.84 316.78h72.96v5.7h-72.96z" transform-origin="342.32px 319.63px" transform="rotate(180)"/></g><path class="F" d="M378.8 347.95h28.89v5.7H378.8z" transform-origin="393.245px 350.8px"/><g class="L"><path d="M305.84 347.95h72.96v5.7h-72.96z" transform-origin="342.32px 350.8px" transform="rotate(180)"/></g><path class="F" d="M378.8 254.45h28.89v5.7H378.8z" transform-origin="393.245px 257.3px"/><g class="L"><path d="M305.84 254.45h72.96v5.7h-72.96z" transform-origin="342.32px 257.3px" transform="rotate(180)"/></g><path class="F" d="M378.8 285.61h28.89v5.7H378.8z" transform-origin="393.245px 288.46px"/><g class="L"><path d="M305.84 285.61h72.96v5.7h-72.96z" transform-origin="342.32px 288.46px" transform="rotate(180)"/></g><g class="F"><path d="M397.27 251.33h5.33V382.4h-5.33z" transform-origin="399.935px 316.865px" transform="rotate(180)"/></g><g class="L"><path d="M373.47 251.33h5.33V382.4h-5.33z" transform-origin="376.135px 316.865px" transform="rotate(180)"/></g><g class="L"><path d="M305.84 251.33h5.33V382.4h-5.33z" transform-origin="308.505px 316.865px" transform="rotate(180)"/></g><g class="F"><path d="M65.37 276.51h54.58V382.4H65.37z" transform-origin="92.66px 329.455px" transform="rotate(180)"/></g><path class="G" d="M79.95 382.4H65.37v-14.62h29.86L79.95 382.4z" transform-origin="80.3px 375.09px"/><g class="F"><path d="M214.18 276.51h54.58V382.4h-54.58z" transform-origin="241.47px 329.455px" transform="rotate(180)"/></g><g class="G"><path d="M65.37 276.51H226.9v100.86H65.37z" transform-origin="146.135px 326.94px" transform="rotate(180)"/></g><path class="G" d="M212.33 382.4h14.57v-14.62h-29.85l15.28 14.62z" transform-origin="211.975px 375.09px"/><g class="I"><path d="M76.68 314.09H215.6v25.24H76.68z" transform-origin="146.14px 326.71px" transform="rotate(180)"/></g><g class="I"><path d="M76.68 344.73H215.6v25.24H76.68z" transform-origin="146.14px 357.35px" transform="rotate(180)"/></g><g class="G"><path d="M103.08 311.9h86.11a4.58 4.58 0 0 1 4.58 4.58v.31h0-95.26 0v-.31a4.58 4.58 0 0 1 4.57-4.58z" transform-origin="146.14px 314.345px" transform="rotate(180)"/></g><g class="I"><path d="M76.68 283.46H215.6v25.24H76.68z" transform-origin="146.14px 296.08px" transform="rotate(180)"/></g><g class="G"><path d="M103.08 281.27h86.11a4.58 4.58 0 0 1 4.58 4.58v.31h0-95.26 0v-.31a4.58 4.58 0 0 1 4.57-4.58z" transform-origin="146.14px 283.715px" transform="rotate(180)"/></g><g class="G"><path d="M103.08 342.54h86.11a4.58 4.58 0 0 1 4.58 4.58v.31h0-95.26 0v-.31a4.58 4.58 0 0 1 4.57-4.58z" transform-origin="146.14px 344.985px" transform="rotate(180)"/></g><g class="F"><path d="M74.07 83.83h137.78v90.23H74.07z" transform-origin="142.96px 128.945px" transform="rotate(180)"/></g><g class="I"><path d="M69.86 83.83h140.02v90.23H69.86z" transform-origin="139.87px 128.945px" transform="rotate(180)"/></g><g class="F"><path d="M74.07 174.06h137.78v17.71H74.07z" transform-origin="142.96px 182.915px" transform="rotate(180)"/></g><g class="I"><path d="M62.86 174.06h140.02v17.71H62.86z" transform-origin="132.87px 182.915px" transform="rotate(180)"/></g><g class="G"><path d="M100.65 64.82h78.46v128.25h-78.46z" transform-origin="139.88px 128.945px" transform="rotate(90)"/></g><path class="K" d="M175.08 168.17L158.7 89.72h-25.56l16.38 78.45h25.56z" transform-origin="154.11px 128.945px"/><path d="M201.27 162.32a.42.42 0 0 0 .42-.43V93.55a.41.41 0 0 0-.42-.42.42.42 0 0 0-.42.42v68.34a.42.42 0 0 0 .42.43z" class="I" transform-origin="201.27px 127.725px"/><path class="K" d="M144.03 168.17l-16.38-78.45h-9.97l16.38 78.45h9.97z" transform-origin="130.855px 128.945px"/><g class="F"><path d="M36.9 128.57h78.46v.75H36.9z" transform-origin="76.13px 128.945px" transform="rotate(90)"/></g><g class="D"><path class="H" d="M68.47 98.59h137.78l.54-6.59H69.01l-.54 6.59z" transform-origin="137.63px 95.295px"/></g><g class="D"><path class="H" d="M68.47 102.31h137.78l.54-6.59H69.01l-.54 6.59z" transform-origin="137.63px 99.015px"/></g><g class="D"><path class="H" d="M68.47 106.02h137.78l.54-6.58H69.01l-.54 6.58z" transform-origin="137.63px 102.73px"/></g><g class="D"><path class="H" d="M68.47 109.74h137.78l.54-6.59H69.01l-.54 6.59z" transform-origin="137.63px 106.445px"/></g><g class="D"><path class="H" d="M68.47 113.45h137.78l.54-6.58H69.01l-.54 6.58z" transform-origin="137.63px 110.16px"/></g><g class="D"><path class="H" d="M68.47 117.17h137.78l.54-6.59H69.01l-.54 6.59z" transform-origin="137.63px 113.875px"/></g><g class="L"><path d="M96.01 220.18h4.76v53.09h-4.76z" transform-origin="98.39px 246.725px" transform="rotate(180)"/></g><g class="G"><path d="M96.63 220.14h1.35v53.09h-1.35z" transform-origin="97.305px 246.685px" transform="rotate(180)"/></g><g class="G"><path d="M98.54 220.14h.53v53.09h-.53z" transform-origin="98.805px 246.685px" transform="rotate(180)"/></g><g class="I"><path d="M80.76 272.06H116h0 0a4.44 4.44 0 0 1-4.44 4.44H85.21a4.44 4.44 0 0 1-4.44-4.44h0-.01z" transform-origin="98.38px 274.28px" transform="rotate(180)"/></g><path d="M89.84 253.14h0a1.38 1.38 0 0 0 1.37-1.37V217a1.37 1.37 0 0 0-1.37-1.37h0a1.37 1.37 0 0 0-1.38 1.37v34.81a1.38 1.38 0 0 0 1.38 1.33z" class="I" transform-origin="89.835px 234.385px"/><path d="M77.07 232.8h42.65l-4.91-29.8H81.98l-4.91 29.8z" fill="#e0e0e0" transform-origin="98.395px 217.9px"/></g><g id="C" class="B L" transform-origin="250px 416.24px"><ellipse cx="250" cy="416.24" rx="193.89" ry="11.32" transform-origin="250px 416.24px"/></g><g id="D" class="B" transform-origin="224.924px 233.29px"><g class="C"><path d="M103.78 202.37h1v18.49h-1z" transform-origin="104.28px 211.615px" transform="rotate(355.88)"/></g><g class="C"><path d="M105.38 230.35h1v6.96h-1z" transform-origin="105.88px 233.83px" transform="rotate(355.88)"/></g><path d="M337.71 315.16H123.55a9.65 9.65 0 0 1-9.44-8.81l-10.52-146.13a8.1 8.1 0 0 1 8.17-8.8h214.16a9.65 9.65 0 0 1 9.44 8.8l10.52 146.13a8.11 8.11 0 0 1-8.17 8.81z" class="C" transform-origin="224.735px 233.29px"/><path d="M338.53 315.16H124.37a9.65 9.65 0 0 1-9.44-8.81l-10.52-146.13a8.1 8.1 0 0 1 8.17-8.8h214.16a9.65 9.65 0 0 1 9.44 8.8l10.52 146.13a8.1 8.1 0 0 1-8.17 8.81z" class="C" transform-origin="225.555px 233.29px"/><g class="K"><path d="M338.53 315.16H124.37a9.65 9.65 0 0 1-9.44-8.81l-10.52-146.13a8.1 8.1 0 0 1 8.17-8.8h214.16a9.65 9.65 0 0 1 9.44 8.8l10.52 146.13a8.1 8.1 0 0 1-8.17 8.81z" opacity=".5" transform-origin="225.555px 233.29px"/></g><path d="M327.06 155.82H112.9h-.72c-5.47.45-4.35 8.78 1.17 8.78h214.52c5.53 0 5.45-8.33-.09-8.78z" class="C" transform-origin="220.288px 160.21px"/><path d="M118.48 160.22a1.85 1.85 0 0 1-1.88 2 2.2 2.2 0 0 1-2.16-2 1.85 1.85 0 0 1 1.87-2 2.2 2.2 0 0 1 2.17 2z" class="G" transform-origin="116.46px 160.22px"/><path d="M125.35 160.22a1.85 1.85 0 0 1-1.88 2 2.2 2.2 0 0 1-2.16-2 1.85 1.85 0 0 1 1.87-2 2.2 2.2 0 0 1 2.17 2z" class="G" transform-origin="123.33px 160.22px"/><path d="M132.21 160.22a1.85 1.85 0 0 1-1.87 2 2.19 2.19 0 0 1-2.16-2 1.84 1.84 0 0 1 1.87-2 2.2 2.2 0 0 1 2.16 2z" class="G" transform-origin="130.195px 160.22px"/><path d="M332.85 300.58H128a3.49 3.49 0 0 1-3.42-3.2l-8.65-120.17a2.92 2.92 0 0 1 3-3.19h204.9a3.48 3.48 0 0 1 3.42 3.19l8.66 120.17a2.94 2.94 0 0 1-3.06 3.2z" class="K" transform-origin="225.92px 237.301px"/><path class="K" d="M246.53 254.8l-3.34-46.32-10.1-6.08h-27.81l3.78 52.4h37.47z" transform-origin="225.905px 228.6px"/><path d="M246.53 255.28h-37.47a.48.48 0 0 1-.49-.45l-3.77-52.4a.47.47 0 0 1 .13-.36.48.48 0 0 1 .35-.16h27.81a.55.55 0 0 1 .25.07l10.1 6.08a.48.48 0 0 1 .24.38l3.32 46.32a.46.46 0 0 1-.13.37.47.47 0 0 1-.34.15zm-37-1H246l-3.28-45.55-9.72-5.85h-27.2z" class="C" transform-origin="225.901px 228.595px"/><path class="D" d="M243.19 208.48l-10.1-6.08 3.71 8.06 6.39-1.98z" transform-origin="238.14px 206.43px"/><path d="M236.8,210.94a.49.49,0,0,1-.44-.28l-3.71-8.06a.5.5,0,0,1,.11-.57.5.5,0,0,1,.58-.05l10.1,6.08a.5.5,0,0,1,.24.48.49.49,0,0,1-.34.4l-6.4,2Zm-2.61-7.32,2.87,6.25,5-1.53Z" class="C" transform-origin="238.144px 206.425px"/><path d="M221,226a2,2,0,0,1-2,2.11,2.31,2.31,0,0,1-2.26-2.11,1.94,1.94,0,0,1,2-2.12A2.32,2.32,0,0,1,221,226Z" class="C" transform-origin="218.867px 225.994px"/><path d="M234.7,226a1.94,1.94,0,0,1-2,2.11,2.32,2.32,0,0,1-2.27-2.11,2,2,0,0,1,2-2.12A2.31,2.31,0,0,1,234.7,226Z" class="C" transform-origin="232.567px 225.996px"/><path d="M238.58,239.74a.49.49,0,0,1-.48-.45c-.25-3.41-5.59-6.18-11.9-6.18-4.19,0-7.95,1.25-9.81,3.25a3.74,3.74,0,0,0-1.14,2.86.49.49,0,0,1-1,.07,4.76,4.76,0,0,1,1.4-3.59c2-2.19,6.07-3.56,10.52-3.56,6.93,0,12.58,3.11,12.86,7.08a.48.48,0,0,1-.45.51Z" class="C" transform-origin="226.638px 235.948px"/><path d="M213.17 221.72a.51.51 0 0 1-.33-.13.49.49 0 0 1 0-.69l2.13-2.29a.49.49 0 0 1 .68 0 .48.48 0 0 1 0 .68l-2.13 2.29a.48.48 0 0 1-.35.14z" class="C" transform-origin="214.245px 220.096px"/><path d="M237.58 221.72a.55.55 0 0 1-.33-.13l-2.45-2.29a.48.48 0 0 1 0-.69.49.49 0 0 1 .68 0l2.45 2.29a.48.48 0 0 1 0 .68.52.52 0 0 1-.35.14z" class="C" transform-origin="236.362px 220.096px"/><path d="M202.26,265.15h2.26l3.26,4.34-.31-4.34h2.28l.56,7.84H208l-3.24-4.31.31,4.31h-2.28Z" class="C" transform-origin="206.285px 269.07px"/><path d="M211.33 269.07a3.8 3.8 0 0 1 .86-3 3.76 3.76 0 0 1 2.9-1.07 4.38 4.38 0 0 1 3.09 1.05 4.32 4.32 0 0 1 1.27 2.94 4.77 4.77 0 0 1-.3 2.25 3 3 0 0 1-1.24 1.37 4.16 4.16 0 0 1-2.14.49 5.25 5.25 0 0 1-2.23-.42 3.62 3.62 0 0 1-1.5-1.34 4.66 4.66 0 0 1-.71-2.27zm2.43 0a2.73 2.73 0 0 0 .56 1.7 1.59 1.59 0 0 0 1.24.52 1.38 1.38 0 0 0 1.17-.51A2.75 2.75 0 0 0 217 269a2.57 2.57 0 0 0-.56-1.62 1.64 1.64 0 0 0-1.25-.51 1.38 1.38 0 0 0-1.14.52 2.53 2.53 0 0 0-.29 1.7z" class="C" transform-origin="215.384px 269.047px"/><path d="M224.15,265.15h3.6a4.51,4.51,0,0,1,1.74.29,3.06,3.06,0,0,1,1.14.83,3.76,3.76,0,0,1,.71,1.25,6.3,6.3,0,0,1,.3,1.52,5.08,5.08,0,0,1-.15,1.95,2.8,2.8,0,0,1-.71,1.16,2.3,2.3,0,0,1-1,.62,5.6,5.6,0,0,1-1.43.22h-3.59Zm2.55,1.78.31,4.28h.59a2.45,2.45,0,0,0,1.07-.17,1,1,0,0,0,.46-.59,3.54,3.54,0,0,0,.08-1.36,2.72,2.72,0,0,0-.53-1.7,1.81,1.81,0,0,0-1.38-.46Z" class="C" transform-origin="227.912px 269.069px"/><path d="M237.45,271.69H234.7l-.29,1.3h-2.47l2.38-7.84H237l3.51,7.84h-2.54Zm-.63-1.69-1.06-2.82L235.1,270Z" class="C" transform-origin="236.225px 269.07px"/><path d="M239.37,265.15h7.36l.14,1.94H244.4l.42,5.9H242.4l-.42-5.9h-2.47Z" class="C" transform-origin="243.12px 269.07px"/><path d="M252.37,271.69h-2.75l-.29,1.3h-2.47l2.38-7.84h2.64l3.5,7.84h-2.53Zm-.63-1.69-1.07-2.82L250,270Z" class="C" transform-origin="251.12px 269.07px"/></g><g id="E" class="B" transform-origin="336.555px 264.616px"><path d="M353.69 173.08l2.94-1.57 3-1.72 5.8-3.72a76.94 76.94 0 0 0 10.52-8.66c.41-.38.77-.8 1.16-1.21l.57-.61.28-.3.14-.16h0c-.14.27 0 .19 0-.07a5.28 5.28 0 0 0 .15-1.09 30.6 30.6 0 0 0-.73-6.32c-.89-4.48-2.24-9.1-3.57-13.62l3.88-1.7a81.31 81.31 0 0 1 6.11 13.6 31.67 31.67 0 0 1 1.72 7.89 12 12 0 0 1-.08 2.51 7.71 7.71 0 0 1-1.22 3.29l-.17.23-.13.16-.15.19-.31.38-.62.75c-.41.49-.81 1-1.25 1.47a71.85 71.85 0 0 1-11.38 10.2 75.49 75.49 0 0 1-6.29 4.19c-1.07.65-2.16 1.27-3.27 1.88s-2.19 1.17-3.47 1.77z" class="J" transform-origin="369.691px 156.585px"/><path d="M344.79 408.18a10.27 10.27 0 0 0 2.22-.3.22.22 0 0 0 .15-.16.21.21 0 0 0-.09-.2c-.29-.19-2.83-1.83-3.81-1.39a.68.68 0 0 0-.39.56 1.13 1.13 0 0 0 .33 1.05 2.35 2.35 0 0 0 1.59.44zm1.65-.58c-1.45.29-2.55.24-3-.15a.77.77 0 0 1-.2-.71.3.3 0 0 1 .17-.25c.53-.23 2 .51 3.03 1.11z" class="C" transform-origin="345.004px 407.122px"/><path d="M347 407.88h.1a.21.21 0 0 0 .1-.17c0-.11 0-2.52-.92-3.32a1 1 0 0 0-.84-.27.69.69 0 0 0-.67.55c-.19 1 1.33 2.75 2.13 3.21a.18.18 0 0 0 .1 0zm-1.43-3.39a.66.66 0 0 1 .44.17 4.53 4.53 0 0 1 .78 2.64c-.8-.64-1.75-2-1.63-2.57 0-.09.07-.21.32-.24z" class="C" transform-origin="345.977px 405.998px"/><path d="M346.22,148.07c-1,5-3,15,.45,18.35,0,0-1.36,5-10.59,5-10.16,0-4.85-5-4.85-5,5.54-1.32,5.39-5.43,4.43-9.3Z" class="J" transform-origin="338.257px 159.745px"/><path d="M329.28 168.42c-1.59.22-.23-3.91.41-4.34 1.5-1 20.86-2.39 20.73 0-.08 1-.56 3-1.4 3.66s-5.82-1.4-19.74.68z" class="E" transform-origin="339.479px 165.594px"/><path d="M332.44 167c-1.27.43-1.15-3.73-.72-4.23 1-1.16 16.67-5.18 17.13-2.91.18 1 .27 3-.27 3.71s-5.14-.46-16.14 3.43z" class="E" transform-origin="340.206px 163.104px"/><path d="M326.61 139.11a.4.4 0 0 1-.33-.15 3.18 3.18 0 0 0-2.59-1.23.39.39 0 0 1-.44-.35.4.4 0 0 1 .35-.43 3.91 3.91 0 0 1 3.29 1.51.4.4 0 0 1-.05.56.5.5 0 0 1-.23.09z" class="E" transform-origin="325.115px 138.028px"/><path d="M324.67,144a17.91,17.91,0,0,1-2,4.53,2.9,2.9,0,0,0,2.44.21Z" fill="#ff5652" transform-origin="323.89px 146.458px"/><path d="M325.16 142.79c.07.67-.24 1.25-.68 1.29s-.85-.46-.91-1.14.24-1.25.67-1.29.85.46.92 1.14z" class="E" transform-origin="324.366px 142.865px"/><path d="M324.44 141.67l-1.66-.31s.95 1.18 1.66.31z" class="E" transform-origin="323.61px 141.675px"/><path class="J" d="M356.7 407.69h-8.38l.66-19.4h8.38l-.66 19.4z" transform-origin="352.84px 397.99px"/><path d="M347.66 406.72h9.41a.66.66 0 0 1 .67.57l1.07 7.44a1.34 1.34 0 0 1-1.34 1.49l-9-.25-9.78.27c-3.51 0-3.71-3.48-2.24-3.79 6.56-1.42 7.6-3.36 9.81-5.22a2.21 2.21 0 0 1 1.4-.51z" class="E" transform-origin="347.203px 411.48px"/><g><g opacity=".2" transform-origin="353px 393.29px"><path d="M357.36 388.29h-8.38l-.34 10h8.38l.34-10z" transform-origin="353px 393.29px"/></g></g><path d="M323.37 178a162.48 162.48 0 0 1-15.05-6.75 91.24 91.24 0 0 1-14.4-8.77 34.31 34.31 0 0 1-3.42-3.07l-.83-.93a11.32 11.32 0 0 1-.88-1.13 7.9 7.9 0 0 1-1.34-3.61 8.22 8.22 0 0 1 .49-3.62 10.64 10.64 0 0 1 1.41-2.6 18.13 18.13 0 0 1 3.39-3.52 42 42 0 0 1 7.3-4.67l3.78-1.8 3.91-1.54 1.77 3.85c-4.36 2.75-8.88 5.78-12.2 9.16a13.86 13.86 0 0 0-1.95 2.49c-.48.79-.47 1.38-.39 1.33s0 0 .14.1l.4.43c.15.18.36.36.55.55a25.62 25.62 0 0 0 2.69 2.16 62.92 62.92 0 0 0 6.44 4c2.25 1.26 4.57 2.45 6.93 3.59a291.06 291.06 0 0 0 14.39 6.41z" class="J" transform-origin="306.959px 156.995px"/><path d="M312 137.15l1.38-3-6.32-2.15s-2 5.91.37 8.63h0a6.05 6.05 0 0 0 4.57-3.48z" class="J" transform-origin="309.826px 136.315px"/><path class="J" d="M313.35 127.66l-5.03-1.46-1.26 5.76 6.29 2.19v-6.49z" transform-origin="310.205px 130.175px"/><path d="M378.35,134.44l.73-7L372.6,129s-.1,6.58,3.33,7.62Z" class="J" transform-origin="375.84px 132.03px"/><path class="J" d="M375.96 122.51l-4.23 1.97.87 4.47 6.48-1.53-3.12-4.91z" transform-origin="375.405px 125.73px"/><path d="M347.54 138.25c.31 8.31.61 11.82-3.14 16.47-5.65 7-15.92 5.55-18.7-2.5-2.5-7.24-2.58-19.61 5.18-23.69a11.34 11.34 0 0 1 16.66 9.72z" class="J" transform-origin="335.951px 143.207px"/><path d="M343.92 154.71c8.16-3.53 13.52-11 11.26-22.58-2.17-11.11-9.67-12.21-12.77-9.91s-10.83-1.13-15.47 2.77c-8 6.77-.44 14 3.52 18.37 2.36 4.86 5.46 14.8 13.46 11.35z" class="E" transform-origin="339.556px 138.327px"/><path d="M340.53 130.14a8.29 8.29 0 1 0 1.83-11.92 8.53 8.53 0 0 0-1.83 11.92z" class="C" transform-origin="347.118px 125.043px"/><path d="M342.65 123c-1.17-6.7 5.16-11.86 13.64-9.37s4 10 2.25 16 2.83 11.72 4.73 7.15-1.3-6-1.3-6 9 2.33.64 12.48-15.93-1.06-13.88-7.67c1.66-5.31-4.97-6.26-6.08-12.59z" class="E" transform-origin="354.339px 130.164px"/><path d="M334.18,125.53c-3.87-2-10.42-3.65-14.15,2.63-1.76,3-1.08,7-1.08,7l11.39.75Z" class="E" transform-origin="326.5px 129.819px"/><path d="M317.61 133.18h0a.26.26 0 0 1-.24-.27c0-.17.29-4.19 2.72-6.71 5.87-6.1 12.75-1 14.71.65a.25.25 0 0 1-.32.38c-1.89-1.62-8.47-6.47-14-.69-2.3 2.39-2.58 6.36-2.58 6.4a.25.25 0 0 1-.29.24z" class="E" transform-origin="326.119px 128.233px"/><path d="M331.85 143a6.89 6.89 0 0 1-1.65 4.28c-1.38 1.62-3 .82-3.33-1-.33-1.61 0-4.4 1.74-5.37s3.26.27 3.24 2.09z" class="J" transform-origin="329.303px 144.363px"/><path d="M330.05,219.12s.54,58.15,5.58,90.55c4.08,26.17,11.61,86.69,11.61,86.69h11.43s1.11-58.43-1-84.31c-5.23-65.5,8.28-77.75-2.62-92.93Z" class="E" transform-origin="344.743px 307.74px"/><g class="K"><path d="M330.05,219.12s.54,58.15,5.58,90.55c4.08,26.17,11.61,86.69,11.61,86.69h11.43s1.11-58.43-1-84.31c-5.23-65.5,8.28-77.75-2.62-92.93Z" opacity=".1" transform-origin="344.743px 307.74px"/></g><g><path d="M336.06 245.5c4 17.55.81 45.19-1.38 57.36-2.32-18.48-3.49-42.43-4.07-60 2.07-3.34 4.03-3.56 5.45 2.64z" opacity=".3" transform-origin="334.289px 271.702px"/></g><path class="C" d="M345.68 396.58h14.55l.76-5.1-15.39-.52.08 5.62z" transform-origin="353.295px 393.77px"/><path d="M349.83 170c1.37-2.72 8.73-4.43 12.75-4.42l3 13.36s-8 11.89-11.34 10.63c-3.93-1.45-7.34-13.7-4.41-19.57z" class="C" transform-origin="357.154px 177.622px"/><g><path d="M349.83 170c1.37-2.72 8.73-4.43 12.75-4.42l3 13.36s-8 11.89-11.34 10.63c-3.93-1.45-7.34-13.7-4.41-19.57z" opacity=".4" transform-origin="357.154px 177.622px"/></g><path d="M341.35 393.31a.19.19 0 0 0 0-.19.2.2 0 0 0-.21-.11c-.42.07-4.12.65-4.62 1.71a.65.65 0 0 0 0 .63 1.1 1.1 0 0 0 .86.57c1.21.12 3-1.56 3.9-2.58zm-4.44 1.54c.34-.56 2.26-1.08 3.72-1.35-1.32 1.34-2.48 2.09-3.16 2a.71.71 0 0 1-.56-.38.25.25 0 0 1 0-.25z" class="C" transform-origin="338.907px 394.467px"/><path d="M341.35 393.31a.05.05 0 0 0 0 0 .19.19 0 0 0 0-.19c-.06-.07-1.54-1.78-2.91-1.87a1.44 1.44 0 0 0-1.09.37c-.41.37-.37.69-.27.89.43.86 3.09 1.16 4.16.93a.18.18 0 0 0 .11-.13zm-3.91-1.26a.79.79 0 0 1 .16-.18 1 1 0 0 1 .79-.27 4.6 4.6 0 0 1 2.39 1.47c-1.19.1-3.11-.26-3.37-.78a.24.24 0 0 1 .03-.24z" class="C" transform-origin="339.194px 392.38px"/><path class="J" d="M350.08 389.76l-7.66 3.41-3.41-7.11-4.46-9.3-.53-1.08 7.66-3.41.59 1.22 4.32 9.01 3.49 7.26z" transform-origin="342.05px 382.72px"/><g><path d="M346.59 382.5l-7.58 3.56-4.46-9.3 7.72-3.27 4.32 9.01z" opacity=".2" transform-origin="340.57px 379.775px"/></g><path d="M321.07,219.12S296.6,280,303.23,308.6c6,25.89,32.5,74.92,32.5,74.92L346,378.38s-16.39-59.19-18.16-72.05c-3.46-25,18.77-60.33,18.77-87.21Z" class="E" transform-origin="324.35px 301.32px"/><g class="K"><path d="M321.07,219.12S296.6,280,303.23,308.6c6,25.89,32.5,74.92,32.5,74.92L346,378.38s-16.39-59.19-18.16-72.05c-3.46-25,18.77-60.33,18.77-87.21Z" opacity=".1" transform-origin="324.35px 301.32px"/></g><path d="M341.06 392.1l7.6-5.53a.66.66 0 0 1 .88.06l5.25 5.39a1.35 1.35 0 0 1-.21 2l-7.42 5.09c-2.06 1.5-6.16 4.81-9 6.88s-5-.63-4-1.74c4.48-5 5.42-8.1 6.12-10.91a2.17 2.17 0 0 1 .78-1.24z" class="E" transform-origin="344.525px 396.59px"/><path class="C" d="M335.07 385.69l13.23-6.05-1.69-5.39-14.17 6.46 2.63 4.98z" transform-origin="340.37px 379.97px"/><path d="M327.56 169.36c-1.09-2.84-10.65-6-15.45-7L310 178.08s7.84 11.26 11.27 10.33c4.06-1.1 8.65-12.96 6.29-19.05z" class="C" transform-origin="319.102px 175.412px"/><g><path d="M327.56 169.36c-1.09-2.84-10.65-6-15.45-7L310 178.08s7.84 11.26 11.27 10.33c4.06-1.1 8.65-12.96 6.29-19.05z" opacity=".4" transform-origin="319.102px 175.412px"/></g><path d="M317.07 168.57s-4 1.4 4 50.55h34c-.57-13.84-.59-22.38 6-50.8a100.28 100.28 0 0 0-14.45-1.9 107.4 107.4 0 0 0-15.44 0c-6.59.58-14.11 2.15-14.11 2.15z" class="C" transform-origin="338.57px 192.631px"/><g><path d="M317.07 168.57s-4 1.4 4 50.55h34c-.57-13.84-.59-22.38 6-50.8a100.28 100.28 0 0 0-14.45-1.9 107.4 107.4 0 0 0-15.44 0c-6.59.58-14.11 2.15-14.11 2.15z" opacity=".4" transform-origin="338.57px 192.631px"/></g><path d="M355.6 217.13l1.53 3c.12.24-.16.48-.55.48H320.9c-.31 0-.56-.15-.58-.35l-.31-3c0-.21.25-.39.58-.39H355a.61.61 0 0 1 .6.26z" class="C" transform-origin="338.584px 218.736px"/><g class="K"><path d="M355.6 217.13l1.53 3c.12.24-.16.48-.55.48H320.9c-.31 0-.56-.15-.58-.35l-.31-3c0-.21.25-.39.58-.39H355a.61.61 0 0 1 .6.26z" opacity=".3" transform-origin="338.584px 218.736px"/></g><path d="M351 221h.92c.19 0 .33-.1.31-.21l-.43-4c0-.12-.17-.21-.35-.21h-.93c-.18 0-.32.09-.31.21l.43 4c-.01.09.15.21.36.21z" class="E" transform-origin="351.221px 218.79px"/><path d="M328.61 221h.92c.18 0 .32-.1.31-.21l-.43-4c0-.12-.17-.21-.36-.21h-.92c-.19 0-.32.09-.31.21l.43 4c.01.09.17.21.36.21z" class="E" transform-origin="328.83px 218.79px"/></g></svg>
                    <?php esc_html_e("No records found", "sticky-chat-widget") ?>
                    </div>
                        <?php
                }//end if
                ?>
            </div>
        </div>
    </div>

</div>

<div class="gp-modal" id="delete-leads">
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
                    <?php esc_html_e("Remove Leads", "sticky-chat-widget"); ?>
                </div>
                <div class="gp-modal-body">
                    <?php esc_html_e("Are you sure, you want to remove selected record(s)?", "sticky-chat-widget") ?>
                </div>
                <div class="gp-modal-footer text-center">
                    <button class="danger-btn" id="delete_leads"><?php esc_html_e("Remove", "sticky-chat-widget"); ?></button>
                    <button class="secondary-btn hide-gp-modal"><?php esc_html_e("Cancel", "sticky-chat-widget"); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="gp-modal" id="delete-all-leads">
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
                    <?php esc_html_e("Remove All Records", "sticky-chat-widget"); ?>
                </div>
                <div class="gp-modal-body">
                    <?php esc_html_e("Are you sure, you want to remove all record(s)?", "sticky-chat-widget") ?>
                </div>
                <div class="gp-modal-footer text-center">
                    <button class="danger-btn" id="delete_all_leads"><?php esc_html_e("Remove", "sticky-chat-widget"); ?></button>
                    <button class="secondary-btn hide-gp-modal"><?php esc_html_e("Cancel", "sticky-chat-widget"); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="gp-modal" id="delete-single-lead">
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
                    <?php esc_html_e("Remove Lead", "sticky-chat-widget"); ?>
                </div>
                <div class="gp-modal-body">
                    <?php esc_html_e("Are you sure, you want to remove this record?", "sticky-chat-widget") ?>
                </div>
                <div class="gp-modal-footer text-center">
                    <button class="danger-btn" id="delete_single_lead"><?php esc_html_e("Remove", "sticky-chat-widget"); ?></button>
                    <button class="secondary-btn hide-gp-modal"><?php esc_html_e("Cancel", "sticky-chat-widget"); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
