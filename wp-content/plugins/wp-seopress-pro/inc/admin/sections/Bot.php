<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function seopress_print_section_info_audit() {
    $running = get_option('seopress_pro_site_audit_running') ? get_option('seopress_pro_site_audit_running') : false;
    $post_count = get_option('seopress_pro_site_audit_post_count') ? get_option('seopress_pro_site_audit_post_count') : 0;
    $count = get_option('seopress_pro_site_audit_count_posts') ? get_option('seopress_pro_site_audit_count_posts') : 0;
    $last_audit = get_option('seopress_pro_site_audit_last_scan') ? wp_date( get_option( 'date_format' ) . ' - ' . get_option( 'time_format' ), get_option('seopress_pro_site_audit_last_scan') ) : esc_html('No audit performed to date.', 'wp-seopress-pro');
    ?>
    <div class="sp-section-header">
        <h2>
            <?php esc_html_e('Site Audit', 'wp-seopress-pro'); ?>
        </h2>
    </div>
    <p>
        <?php echo wp_kses_post(__('Find all content analysis in one place to solve technical SEO issues. Everytime you edit a post, page or post type, your site audit will be automatically updated here.', 'wp-seopress-pro')); ?>
    </p>
    <p>
        <?php echo wp_kses_post(sprintf(__('If you have linked your <a href="%s"><strong>Google Search Console account</strong></a>, we also display <strong>clicks and positions</strong> data associated with each URL to help you prioritize your optimizations.', 'wp-seopress-pro'), esc_url(admin_url('admin.php?page=seopress-pro-page#tab=tab_seopress_inspect_url')))); ?>
    </p>
    <p>
        <?php if($running === "1") { ?>
            <button type="button" id="seopress-cancel-site-audit" class="btn btnPrimary">
                <?php esc_html_e('Cancel current audit', 'wp-seopress-pro'); ?>
            </button>
        <?php } else { ?>
            <button type="button" id="seopress-run-site-audit" class="btn btnPrimary">
                <?php esc_html_e('Run audit', 'wp-seopress-pro'); ?>
            </button>
        <?php } ?>
        <span class="spinner" <?php if($running === "1") { echo 'style="visibility:visible;float:none;margin:0 10px 0"'; } ?>></span>
    </p>

    <p class="last-audit">
        <?php printf(wp_kses_post(__('<strong>Last scan:</strong> %s', 'wp-seopress-pro')), esc_html($last_audit)); ?>
    </p>

    <div id="seopress-notice-site-audit-running" class="seopress-notice" style="display:none">
        <p>
            <?php
                if ($post_count < $count) {
                    $current = absint( $post_count );
                } else {
                    $current = absint( $count );
                }
                printf(wp_kses_post(__('<span id="seopress-site-audit-offset">%1$d</span> posts analyzed out of <strong>%2$d</strong>.', 'wp-seopress-pro')), absint($current), absint( $count ));
            ?>
        </p>
        <br>
        <em><?php esc_html_e('You can close this tab, the site audit will continue to run in background.', 'wp-seopress-pro'); ?></em>
    </div>

    <hr>
    <h3>
        <?php esc_html_e('Overview', 'wp-seopress-pro'); ?>
    </h3>

    <div class="seopress-site-health">
        <?php
            $issues = seopress_pro_get_service('SiteAudit')->countTotalIssues();
            if ($issues) { ?>
                <div class="seopress-card-item">
                    <span class="title">
                        <?php esc_html_e('Issues detected', 'wp-seopress-pro'); ?>
                    </span>
                    <span class="value">
                        <?php
                            echo $issues;
                        ?>
                    </span>
                </div>
                <div class="seopress-card-item seopress-error">
                    <span class="title">
                        <?php esc_html_e('High impact issues', 'wp-seopress-pro'); ?>
                    </span>
                    <span class="value">
                        <?php
                            $issues_high = seopress_pro_get_service('SiteAudit')->countTotalIssues('', 'high');

                            echo $issues_high;
                            echo '<small>' . round($issues_high / $issues * 100) . '%</small>';
                        ?>
                    </span>
                </div>
                <div class="seopress-card-item seopress-warning">
                    <span class="title">
                        <?php esc_html_e('Medium impact issues', 'wp-seopress-pro'); ?>
                    </span>
                    <span class="value">
                        <?php
                            $issues_medium = seopress_pro_get_service('SiteAudit')->countTotalIssues('', 'medium');
                            echo $issues_medium;

                            echo '<small>' . round($issues_medium / $issues * 100) . '%</small>';
                        ?>
                    </span>
                </div>
                <div class="seopress-card-item">
                    <span class="title">
                        <?php esc_html_e('Crawled URL', 'wp-seopress-pro'); ?>
                    </span>
                    <span class="value">
                        <?php
                            $crawledURL = seopress_pro_get_service('SiteAudit')->countTotalCrawledURL();

                            echo $crawledURL;
                        ?>
                    </span>
                </div>
            <?php
        } else {
            ?>
                <p>
                    <?php esc_html_e('Run a scan to find issues.', 'wp-seopress-pro'); ?>
                </p>
            <?php
        } ?>
    </div>
<?php
}

function seopress_print_section_info_bot() {
    $docs = function_exists('seopress_get_docs_links') ? seopress_get_docs_links() : ''; ?>
    <div class="sp-section-header">
        <h2>
            <?php esc_html_e('Scan', 'wp-seopress-pro'); ?>
        </h2>
    </div>
    <p><?php esc_html_e('The bot scans links in your content to find errors (404...). We limit this search by default to the last 100 posts/pages/custom post types.', 'wp-seopress-pro'); ?>

    <p>
        <?php esc_html_e('You can increase this value in the settings tab.', 'wp-seopress-pro'); ?>

        <a class="seopress-help" href="<?php echo esc_url($docs['bot']); ?>" target="_blank">
            <?php esc_html_e('Check our guide', 'wp-seopress-pro'); ?>
        </a>
        <span class="seopress-help dashicons dashicons-external"></span>
    </p>

    <a href="<?php echo esc_url( admin_url('edit.php?post_type=seopress_bot') ); ?>" class="btn btnTertiary">
        <?php esc_html_e('View scan results', 'wp-seopress-pro'); ?>
    </a>

<?php
}

function seopress_print_section_info_bot_settings()
{ ?>
    <div class="sp-section-header">
        <h2>
            <?php esc_html_e('Settings', 'wp-seopress-pro'); ?>
        </h2>
    </div>
    <p>
        <?php esc_html_e('Edit your broken links settings.', 'wp-seopress-pro'); ?>
    </p>

<?php
}
