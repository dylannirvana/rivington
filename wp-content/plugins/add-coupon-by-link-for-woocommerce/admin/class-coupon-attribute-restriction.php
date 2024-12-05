<?php 

namespace PISOL\ACBLW\ADMIN;

class CouponAttributeRestriction{
    static $instance = null;

    public static function get_instance(){
        if(self::$instance == null){
            self::$instance = new self();
        }
        return self::$instance;
    }

    function __construct(){
        add_filter( 'woocommerce_coupon_data_tabs', [$this,'add_coupon_product_attribute_tab']);

        add_action( 'woocommerce_coupon_data_panels', [$this,'coupon_product_attribute_fields'],10 ,2 );

        add_action('admin_enqueue_scripts', [$this, 'enqueue_coupon_admin_styles']);

        add_action('wp_ajax_pisol_get_attribute', [$this, 'search_product_attributes']);

        add_action('woocommerce_coupon_options_save', [$this, 'save_coupon_attributes']);
    }

    function add_coupon_product_attribute_tab($tabs) {
        $tabs['product_attribute'] = array(
            'label'    => __( 'Product Attribute', 'add-coupon-by-link-woocommerce' ),
            'target'   => 'product_attribute_coupon_data',
            'class'    => array(),
            'priority' => 30,
        );
        return $tabs;
    }

    function coupon_product_attribute_fields($coupon_id, $coupon) {
        ?>
        <div id="product_attribute_coupon_data" class="panel woocommerce_options_panel">
            <?php include_once plugin_dir_path( dirname( __FILE__ ) ).'admin/partials/coupon-attribute-fields.php'; ?>
        </div>
        <?php
    }

    function enqueue_coupon_admin_styles($hook){
        if (($hook === 'post.php' || $hook == 'post-new.php') && get_post_type() === 'shop_coupon') {
            // Enqueue the external CSS file
            wp_enqueue_style(
                'coupon-admin-styles', 
                plugin_dir_url( __FILE__ ) . 'css/coupon-page.css', 
                array(), 
                ADD_COUPON_BY_LINK_WOOCOMMERCE_VERSION, 
                'all'
            );

            wp_enqueue_script( 'coupon-admin-js', plugin_dir_url( __FILE__ ) . 'js/coupon-page.js', array('jquery','selectWoo'), ADD_COUPON_BY_LINK_WOOCOMMERCE_VERSION, true );
        }
    }

    function search_product_attributes() {
        //check so user can administrator
        if (!current_user_can('administrator') || !isset($_GET['_nonce']) || wp_verify_nonce('_nonce', 'security')) {
            wp_send_json_error('You are not allowed to search product attributes');
        }
    
        global $wpdb;
    
        $term = sanitize_text_field($_GET['term']);
        $results = array();
    
        // Custom SQL query to search within attribute taxonomies
        $query = $wpdb->prepare("
            SELECT attribute_name, attribute_label 
            FROM {$wpdb->prefix}woocommerce_attribute_taxonomies
            WHERE attribute_label LIKE %s
            LIMIT 50
        ", '%' . $wpdb->esc_like($term) . '%');
    
        $attributes = $wpdb->get_results($query);
    
        foreach ($attributes as $attribute) {
            $attribute_name = wc_attribute_taxonomy_name($attribute->attribute_name);
            $terms = get_terms(array(
                'taxonomy' => $attribute_name,
                'hide_empty' => false
            ));
    
            foreach ($terms as $term) {
                $results[] = array(
                    'id' => $attribute_name . ':' . $term->term_id,
                    'text' => $attribute->attribute_label . ' : ' . $term->name
                );
            }
        }
    
        wp_send_json($results);
    }
    
    function save_coupon_attributes($post_id) {
        // Check if we have posted data for our custom field
        if (isset($_POST['_included_attributes'])) {
            // Sanitize and decode JSON data
            $included_attributes = $_POST['_included_attributes'];
    
            // Update or add custom meta field to the coupon
            update_post_meta($post_id, '_included_attributes', $included_attributes);
        }else{
            update_post_meta($post_id, '_included_attributes', []);
        }

        if (isset($_POST['_excluded_attributes'])) {
            // Sanitize and decode JSON data
            $excluded_attributes = $_POST['_excluded_attributes'];
    
            // Update or add custom meta field to the coupon
            update_post_meta($post_id, '_excluded_attributes', $excluded_attributes);
        }else{
            update_post_meta($post_id, '_excluded_attributes', []);
        }
    }
    
    static function showOptions($field, $coupon_id){
        $attributes = get_post_meta($coupon_id, $field, true);
        if(is_array($attributes)){
            foreach ($attributes as $taxonomy_attribute) {
                $parts = explode(':', $taxonomy_attribute);

                if (count($parts) !== 2) {
                    continue;
                }

                $taxonomy_slug = $parts[0];
                $attribute_id = $parts[1];
                
                $taxonomy = get_taxonomy($taxonomy_slug);
                if ($taxonomy) {
                    // Output the taxonomy label (taxonomy name)
                    $taxonomy_label = $taxonomy->labels->singular_name;
                } else {
                    $taxonomy_label = $taxonomy_slug;
                }

                $term = get_term($attribute_id);
                if ($term) {
                    // Output the term name
                    $term_name = $term->name;
                } else {
                    $term_name = $attribute_id;
                }

                echo '<option value="' . esc_attr($taxonomy_attribute) . '" selected="selected">' . esc_html( $taxonomy_label.' : '.$term_name ) . '</option>';
            }
        }
    }
}

CouponAttributeRestriction::get_instance();