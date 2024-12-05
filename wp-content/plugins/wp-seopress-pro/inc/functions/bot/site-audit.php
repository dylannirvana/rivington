<?php
namespace SEOPressPRO;

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

/**
 * Site Audit Class.
 *
 * @since 7.8.0
 */
class SiteAudit {
    private $dataIdsCache = null;
    private $postMetaCache = [];
    private $countItemsCache = [];
    private $editLinkCache = [];
    private $permalinkCache = [];
    private $postCache = [];

    public function __construct() {
        //add_action('init', [$this, 'init'], 0);
    }

    public function getDataIds() {
        if ($this->dataIdsCache === null) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'seopress_content_analysis';
            $column_name = 'post_id';
            $this->dataIdsCache = $wpdb->get_col("SELECT $column_name FROM $table_name");
        }
        return $this->dataIdsCache ? $this->dataIdsCache : [];
    }

    public function getDataAnalysis($post, $type) {
        return seopress_get_service('GetContentAnalysis')->getAnalyzes($post, $type);
    }

    public function renderAnalysis($type, $details) {
        ob_start();
        ?>
            <details class="wrap-site-audit-analysis">
                <?php $this->renderSummaryAndDescription($details, $type); ?>
                <?php $this->renderAnalysisResults($type); ?>
            </details>
        <?php
        echo ob_get_clean();
    }

    private function renderSummaryAndDescription($details, $type) {
        if (isset($details['title'])) {
            $numItems = $this->countItems($type);
            ?>
                <summary>
                    <?php echo $details['title']; ?> 
                    <span class="site-audit-count-issues" data-issues="<?php echo $numItems['issues'] ?? 0; ?>" data-good="<?php echo $numItems['good'] ?? 0; ?>">
                        <?php if ($numItems['issues'] > 0) {
                            echo '(' . $numItems['issues'] . ' issues)';
                        } else {
                            ?><span class="dashicons dashicons-yes"></span><?php
                        } ?>
                    </span>
                </summary>
            <?php
        }
    }

    private function countItems($type) {
        if (!isset($this->countItemsCache[$type])) {
            $ids = $this->getDataIds();
            $counters = ['issues' => 0, 'good' => 0];

            foreach ($ids as $id) {
                $post = $this->getPost($id);
                $data = $this->getDataAnalysis($post, $type);
                if (isset($data[$type]['impact']) && $data[$type]['impact'] !== 'good') {
                    $counters['issues']++;
                } else {
                    $counters['good']++;
                }
            }

            $this->countItemsCache[$type] = $counters;
        }

        return $this->countItemsCache[$type];
    }

    private function renderAnalysisResults($type) {
        $ids = $this->getDataIds();
        if (!empty($ids)) {
            $numItems = 0; // Initialize item count
            ob_start();
            ?>
            <div class="site-audit-analysis-desc gr-analysis">
                <table class="seopress-site-audit-table display">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Impact','wp-seopress-pro'); ?></th>
                            <th><?php esc_html_e('Post title','wp-seopress-pro'); ?></th>
                            <th><?php esc_html_e('Target keywords','wp-seopress-pro'); ?></th>
                            <?php if (seopress_get_service('ToggleOption')->getToggleInspectUrl() === '1') {
                                $google_api_key = $this->getGoogleApiKey();
                                if (!empty($google_api_key)) { ?>
                                    <th><?php esc_html_e('Position','wp-seopress-pro'); ?></th>
                                    <th><?php esc_html_e('Clicks','wp-seopress-pro'); ?></th>
                                <?php }
                            } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $renderedItems = $this->renderAllAnalysisItems($ids, $type);
                            $numItems = array_sum(array_column($renderedItems, 'count'));
                            echo implode('', array_column($renderedItems, 'html'));
                            $this->renderAllGoodMessage($numItems);
                        ?>
                    </tbody>
                </table>
            </div>
            <?php
            echo ob_get_clean();
        }
    }

    private function renderAllAnalysisItems($ids, $type) {
        $items = [];
        foreach ($ids as $id) {
            $items[] = $this->renderAnalysisItem($id, $type);
        }
        return $items;
    }

    private function renderAnalysisItem($id, $type) {
        if (!isset($this->editLinkCache[$id])) {
            $this->editLinkCache[$id] = get_edit_post_link($id);
        }

        if (!isset($this->permalinkCache[$id])) {
            $this->permalinkCache[$id] = get_permalink($id);
        }

        $post = $this->getPost($id);
        $post_title = $post->post_title ?? '';
        $post_permalink = $this->permalinkCache[$id];
        $edit_link = $this->editLinkCache[$id];
        $target_keywords = $this->getPostMeta($id, '_seopress_analysis_target_kw', '<span class="site-audit-target-keyword-missing">' . __('Missing!', 'wp-seopress-pro') . '</span>');

        $data = $this->getDataAnalysis($post, $type);

        if (isset($data[$type])) {
            $impact = $data[$type]['impact'] ?? 0;

            if ($impact !== 'good') {
                switch ($impact) {
                    case 'low':
                        $priority = 1;
                        break;
                    case 'medium':
                        $priority = 2;
                        break;
                    case 'high':
                        $priority = 3;
                        break;
                }
                ob_start();
                ?>
                <tr class="gr-analysis-title">
                    <td class="site-audit-item-impact" aria-hidden="true" data-sort="<?php echo esc_attr($priority);?>">
                        <span class="impact <?php echo esc_attr($impact); ?>" aria-hidden="true"></span>
                    </td>
                    <td class="site-audit-item-link" data-sort="<?php echo esc_html($post_title); ?>">
                        <a href="<?php echo esc_url($edit_link ?: ''); ?>" class="site-audit-edit-url" target="_blank">
                            <?php echo esc_html($post_title); ?>
                            <span class="dashicons dashicons-edit"></span>
                        </a>
                        <span>
                            <?php echo esc_html($post_permalink ?: ''); ?>
                            <a href="<?php echo esc_url($post_permalink ?: ''); ?>" target="_blank">
                                <span class="dashicons dashicons-external"></span>
                            </a>
                        </span>
                    </td>
                    <td class="site-audit-target-keyword" data-sort="<?php echo sanitize_text_field($target_keywords); ?>">
                        <?php
                            echo wp_kses_post($target_keywords);
                        ?>
                    </td>
                    <?php if (seopress_get_service('ToggleOption')->getToggleInspectUrl() === '1') {
                        $google_api_key = $this->getGoogleApiKey();
                        if (!empty($google_api_key)) { ?>
                            <td class="site-audit-gsc-rankings">
                                <?php
                                    $position = $this->getPostMeta($id, '_seopress_search_console_analysis_position', 0);
                                    echo esc_attr(round($position), 0);
                                ?>
                            </td>
                            <td class="site-audit-gsc-clicks">
                                <?php
                                    $clicks = $this->getPostMeta($id, '_seopress_search_console_analysis_clicks', 0);
                                    echo esc_attr(round($clicks, 2));
                                ?>
                            </td>
                        <?php }
                    } ?>
                </tr>
                <?php
                $html = ob_get_clean();
                return ['count' => 1, 'html' => $html];
            }
        }
        return ['count' => 0, 'html' => ''];
    }

    private function renderAllGoodMessage($numItems) {
        if ($numItems === 0) { ?>
            <p><?php _e('All good!', 'wp-seopress-pro'); ?></p>
            <?php
        }
    }

    private function getPost($id) {
        if (!isset($this->postCache[$id])) {
            $this->postCache[$id] = get_post($id);
        }
        return $this->postCache[$id];
    }

    private function getPostMeta($post_id, $meta_key, $default = '') {
        if (!isset($this->postMetaCache[$post_id])) {
            $this->postMetaCache[$post_id] = get_post_meta($post_id);
        }

        // Check if the meta key exists and if its value is not empty
        if (isset($this->postMetaCache[$post_id][$meta_key]) && !empty($this->postMetaCache[$post_id][$meta_key][0])) {
            return $this->postMetaCache[$post_id][$meta_key][0];
        }

        return $default;
    }

    private function getGoogleApiKey() {
        static $google_api_key = null;
        if ($google_api_key === null) {
            $options = get_option('seopress_instant_indexing_option_name');
            $google_api_key = $options['seopress_instant_indexing_google_api_key'] ?? '';
        }
        return $google_api_key;
    }
}

$seopress_pro_site_audit = new SiteAudit();
