<?php 

namespace PISOL\ACBLW\ADMIN;

class Reporting{

    static $instance = null;

    public static function get_instance(){
        if(self::$instance == null){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct(){
        add_filter( 'manage_shop_coupon_posts_columns', array( $this, 'define_columns' ), 11 );
        add_action( 'manage_shop_coupon_posts_custom_column', array( $this, 'render_columns' ), 10, 2 );

        add_action('restrict_manage_posts', array( $this, 'add_coupon_email_search'));

        add_filter('pre_get_posts', array( $this, 'filter_coupons_by_email_search'));
    }

    public function define_columns( $columns = array() ) {

        if ( ! is_array( $columns ) || empty( $columns ) ) {
            $columns = array();
        }

        $columns['view_related_orders'] = __( 'Used in orders', 'add-coupon-by-link-woocommerce' );

        return $columns;
    }

    public function render_columns( $column = '', $post_id = 0 ) {

        if ( ! empty( $column ) ) {
            switch ( $column ) {
                case 'view_related_orders':
                    $this->view_related_orders( $post_id );
                    break;
            }
        }
    }

    public function view_related_orders( $coupon_id = 0 ) {

        $coupon = new \WC_Coupon( $coupon_id );

        if ( ! is_object( $coupon ) || ! is_callable( array( $coupon, 'get_id' ) ) ) {
            return;
        }

        $usage_count = is_callable( array( $coupon, 'get_usage_count' ) )  ? $coupon->get_usage_count() : (isset($coupon->usage_count) ? $coupon->usage_count : 0);
        
        $coupon_code = is_callable( array( $coupon, 'get_code' ) )  ? $coupon->get_code() : (isset($coupon->code) ? $coupon->code : '');

        if ( empty( $coupon_code ) || empty($usage_count) || $usage_count <= 0) {
            return;
        }

        $coupon_usage_url = add_query_arg(
            array(
                's'           => $coupon_code,
                'post_status' => 'all',
                'post_type'   => 'shop_order',
            ),
            admin_url( 'edit.php' )
        );
        $column_content   = sprintf( '<a href="%s" target="_blank" title="%s"><span class="dashicons dashicons-external"></span></a>', esc_url( $coupon_usage_url ), esc_attr__('Click to view list of orders', 'add-coupon-by-link-woocommerce') );
        
        echo wp_kses_post( $column_content );

    }

    function add_coupon_email_search() {
        global $typenow, $wp_query;
    
        if ($typenow == 'shop_coupon') {
            ?>
            <input type="text" name="coupon_email_search" id="coupon_email_search" value="<?php echo isset($_GET['coupon_email_search']) ? esc_attr($_GET['coupon_email_search']) : ''; ?>" placeholder="<?php esc_attr_e('Search by Email ID', 'add-coupon-by-link-woocommerce'); ?>" />
            <?php
        }
    }

    function filter_coupons_by_email_search($query) {
        global $pagenow, $typenow;
    
        if ($pagenow == 'edit.php' && $typenow == 'shop_coupon' && isset($_GET['coupon_email_search']) && $_GET['coupon_email_search'] != '') {
            $email_search = sanitize_text_field($_GET['coupon_email_search']);
            $meta_query = array(
                array(
                    'key'     => 'customer_email',
                    'value'   => $email_search,
                    'compare' => 'LIKE',
                ),
            );
            $query->set('meta_query', $meta_query);
        }
    }
}

Reporting::get_instance();