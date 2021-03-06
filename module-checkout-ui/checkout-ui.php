<?php namespace h;
/**
 * Change the interface for Checkout page (user)
 */
class Checkout_UI {

  function __construct() {
    // Template
    add_filter('template_include', [$this, 'replace_page_template'], 100);

    // Wrapper
    add_action('woocommerce_before_checkout_form', [$this, 'wrap_side_form'], 8);
    add_action('woocommerce_before_checkout_form', [$this, 'wrap_side_form_close'], 12);

    // Order Review
    add_action('woocommerce_checkout_before_customer_details', [$this, 'before_customer_details'] );
    add_action('woocommerce_checkout_after_customer_details', [$this, 'after_customer_details'] );
    add_action('woocommerce_checkout_after_order_review', [$this, 'after_order_review'] );

    // CSS JS
    add_filter('woocommerce_enqueue_styles', '__return_empty_array');
    add_action('wp_enqueue_scripts', [$this, 'enqueue_assets'], 999999);

    // Email
    add_action('woocommerce_order_status_completed_notification', [$this, 'send_invoice_after_order'] );

    // Account
    add_filter('woocommerce_min_password_strength', [$this, 'reduce_password_requirement'] );
  }


  /**
   * Replace the Page template with the one provided by this plugin
   * @filter template_include
   * 
   * @param string $template - Path to the PHP template file
   * @return string - The new path to PHP template file
   */
  function replace_page_template( $template ) : string {
    $file = 'page-checkout.php';
    if( file_exists( \hoo_locate_template( $file ) ) ) {
  		$template = \hoo_locate_template( $file );
  	}

    return $template;
  }

  /**
   * Open wrapper to Coupon Form and Login
   * @action woocommerce_before_checkout_form
   */
  function wrap_side_form() {
    echo '<div class="checkout-side-form">';
  }

  /**
   * Close wrapper of Coupon Form and Login
   * @action woocommerce_before_checkout_form
   */
  function wrap_side_form_close() {
    echo '</div>';
  }


  /*
    Additional content for Customer Details and wrap Order Review

    @action woocommerce_checkout_before_customer_details
    @action woocommerce_checkout_after_customer_details
    @action woocommerce_checkout_after_order_review
  */
  function before_customer_details() {
    echo '<div class="column-main">';
      if( class_exists('Jetpack') ) { jetpack_breadcrumbs(); }
  }
  function after_customer_details() {
    $this->output_legal_terms();
    echo '</div>';
    echo '<div class="column-aside">';
  }
  function after_order_review() {
    echo '</div>';
  }



  /**
   * Customize JS and CSS in Checkout pages
   * @action wp_enqueue_scripts
   */
  function enqueue_assets( $hook ) {
    // remove all other CSS
    global $wp_styles;
  	foreach( $wp_styles->registered as $handle => $data ) {
      if( in_array($handle, ['admin-bar', 'select2'] ) ) { continue; }
  		wp_dequeue_style( $handle );
  	}

    wp_enqueue_style( 'h-checkout', HOO_DIR . '/assets/css/h-checkout.css' );
  }

  /**
   * Send invoice to customer automatically after order
   * @param $order_id (int) - The new order that's just created
   */
  function send_invoice_after_order( $order_id ) {
    $email = new WC_Email_Customer_Invoice();
    $email->trigger( $order_id );
  }

  /**
   * Set the minimum requirement for password during registration
   * 1: bad, 2: medium, 3: strong
   * @filter woocommerce_min_password_strength
   */
  function reduce_password_requirement() : int {
    return 1;
  }


  /////


  /**
   * Add copyright, legal, and terms condition at the bottom of Customer Detail
   */
  private function output_legal_terms() {
    global $post;
    $tnc_url = get_permalink( wc_get_page_id( 'terms' ) );
    $privacy_url = get_permalink( get_option( 'wp_page_for_privacy_policy', 0 ) );

    ?>
    <div class="h-checkout-legal">
      <span>All rights reserved <?php bloginfo('name'); ?></span>
      <?php if( $tnc_url ) {
        echo "<span><a href='$tnc_url' target='_blank'>" . __('Terms &amp; Conditions') . '</a></span>';
      } ?>
      <?php if( $privacy_url ) {
        echo "<span><a href='$privacy_url' target='_blank'>" .  __('Privacy Policy') . '</a></span>';
      } ?>
    </div>
    <?php
  }
}
