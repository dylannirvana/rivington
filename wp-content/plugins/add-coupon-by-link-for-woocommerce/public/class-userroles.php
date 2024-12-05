<?php 

namespace PISOL\ACBLW\FRONT;

class UserRoles{
    static $instance = null;

    public static function get_instance(){
        if(self::$instance == null){
            self::$instance = new self();
        }
        return self::$instance;
    }

    function __construct(){
        add_action( 'woocommerce_coupon_options_usage_restriction', array( $this, 'userRole' ), 10, 2 );
        add_action( 'woocommerce_coupon_options_save', array( $this, 'save_meta' ), 10, 2 );
        add_filter( 'woocommerce_coupon_is_valid', array( $this, 'validate' ), 11, 3 );
        add_filter( 'woocommerce_coupon_is_valid', array( $this, 'validateExcludedRole' ), 12, 3 );

    }

    function userRole( $coupon_id = 0, $coupon = null){

			$user_roles = ( is_object( $coupon ) && method_exists( $coupon, 'get_meta'  ) ) ? $coupon->get_meta( 'pi_acblw_user_roles' ) : get_post_meta( $coupon_id, 'pi_acblw_user_roles', true );

            $exc_user_roles = ( is_object( $coupon ) && method_exists( $coupon, 'get_meta'  ) ) ? $coupon->get_meta( 'pi_acblw_exc_user_roles' ) : get_post_meta( $coupon_id, 'pi_acblw_exc_user_roles', true );

			if ( ! is_array( $user_roles ) || empty( $user_roles ) ) {
				$user_roles = array();
			}

            if ( ! is_array( $exc_user_roles ) || empty( $exc_user_roles ) ) {
				$exc_user_roles = array();
			}

            $all_user_roles =  $this->get_available_user_roles();

            $options = array();
            foreach ($all_user_roles as $role_key => $role) {
                $options[$role_key] = $role['name'];
            }

            woocommerce_wp_select(
                array(
                    'id'                => 'pi_acblw_user_roles',
                    'name'              => 'pi_acblw_user_roles[]',
                    'label'             => __('User role', 'add-coupon-by-link-woocommerce'),
                    'description'       => __('If you want to restrict the coupon to specific user role, then you can add that role here', 'add-coupon-by-link-woocommerce'),
                    'desc_tip'          => true,
                    'value'             => $user_roles,
                    'options'           => $options,
                    'custom_attributes' => array(
                        'multiple' => 'multiple',
                    ),
                    'class'             => 'wc-enhanced-select',
                )
            );

            woocommerce_wp_select(
                array(
                    'id'                => 'pi_acblw_exc_user_roles',
                    'name'              => 'pi_acblw_exc_user_roles[]',
                    'label'             => __('Excluded user role', 'add-coupon-by-link-woocommerce'),
                    'description'       => __('If you want to exclude certain group from using the coupon, then you can add that role here', 'add-coupon-by-link-woocommerce'),
                    'desc_tip'          => true,
                    'value'             => $exc_user_roles,
                    'options'           => $options,
                    'custom_attributes' => array(
                        'multiple' => 'multiple',
                    ),
                    'class'             => 'wc-enhanced-select',
                )
            );

    }

    public function get_available_user_roles() {
        $available_user_roles = array();

        if ( ! function_exists( 'get_editable_roles' ) ) {
            require_once ABSPATH . 'wp-admin/includes/user.php';
        }

        if ( function_exists( 'get_editable_roles' ) ) {
            $available_user_roles = get_editable_roles();
        }

        return $available_user_roles;
    }

    public function save_meta( $post_id = 0, $coupon = null ) {

        if ( empty( $post_id ) ) return;

        $user_roles = isset($_POST['pi_acblw_user_roles']) && is_array($_POST['pi_acblw_user_roles']) ? array_map('sanitize_text_field', $_POST['pi_acblw_user_roles']) : array();

        update_post_meta($post_id, 'pi_acblw_user_roles', $user_roles);

        $exc_user_roles = isset($_POST['pi_acblw_exc_user_roles']) && is_array($_POST['pi_acblw_exc_user_roles']) ? array_map('sanitize_text_field', $_POST['pi_acblw_exc_user_roles']) : array();

        update_post_meta($post_id, 'pi_acblw_exc_user_roles', $exc_user_roles);
    }

    public function validate( $valid = false, $coupon = object, $discounts = null ) {
        // If coupon is invalid already, no need for further checks.
        if ( false === $valid ) {
            return $valid;
        }

        $coupon_id = method_exists($coupon, 'get_id') ? $coupon->get_id() : $coupon->id;

        $selected_user_roles = ( is_object( $coupon ) && method_exists( $coupon, 'get_meta'  ) ) ? $coupon->get_meta( 'pi_acblw_user_roles' ) : get_post_meta( $coupon_id, 'pi_acblw_user_roles', true );

        if(empty($selected_user_roles) || !is_array($selected_user_roles)){
            return $valid;
        }

        if (!is_user_logged_in()) {
            $valid = false;
            $coupon_code = ( is_object( $coupon ) && method_exists(  $coupon, 'get_code' )  ) ? $coupon->get_code() : '';
            WC()->cart->remove_coupon($coupon_code);
            wc_add_notice(__('You must be logged in in order to use this coupon code', 'add-coupon-by-link-woocommerce'), 'error');
            return $valid;
        }

        // Get selected payment method during checkout
        $user = wp_get_current_user();
        $user_roles = $user->roles;

        foreach ($user_roles as $user_role) {
            if (in_array($user_role, $selected_user_roles)) {
                return $valid;
            }
        }

        $valid = false;
        $coupon_code = ( is_object( $coupon ) && method_exists(  $coupon, 'get_code' )  ) ? $coupon->get_code() : '';
        WC()->cart->remove_coupon($coupon_code);
        wc_add_notice(__('This coupon is not valid for your user role.', 'add-coupon-by-link-woocommerce'), 'error');

        return $valid;
    }

    public function validateExcludedRole( $valid = false, $coupon = object, $discounts = null ) {
        // If coupon is invalid already, no need for further checks.
        if ( false === $valid ) {
            return $valid;
        }

        $coupon_id = method_exists($coupon, 'get_id') ? $coupon->get_id() : $coupon->id;

        $selected_exc_user_roles = ( is_object( $coupon ) && method_exists( $coupon, 'get_meta'  ) ) ? $coupon->get_meta( 'pi_acblw_exc_user_roles' ) : get_post_meta( $coupon_id, 'pi_acblw_exc_user_roles', true );

        if(empty($selected_exc_user_roles) || !is_array($selected_exc_user_roles)){
            return $valid;
        }

        if (!is_user_logged_in()) {
            $valid = false;
            $coupon_code = ( is_object( $coupon ) && method_exists(  $coupon, 'get_code' )  ) ? $coupon->get_code() : '';
            WC()->cart->remove_coupon($coupon_code);
            wc_add_notice(__('You must be logged in in order to use this coupon code', 'add-coupon-by-link-woocommerce'), 'error');
            return $valid;
        }

        // Get selected payment method during checkout
        $user = wp_get_current_user();
        $user_roles = $user->roles;

        foreach ($user_roles as $user_role) {
            if (in_array($user_role, $selected_exc_user_roles)) {
                $valid = false;
                $coupon_code = ( is_object( $coupon ) && method_exists(  $coupon, 'get_code' )  ) ? $coupon->get_code() : '';
                WC()->cart->remove_coupon($coupon_code);
                wc_add_notice(__('This coupon is not valid for your user role.', 'add-coupon-by-link-woocommerce'), 'error');
            }
        }

        return $valid;
    }
}

UserRoles::get_instance();