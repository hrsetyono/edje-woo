<?php namespace h;

class Frontend_Product {
  function __construct() {
    add_filter( 'woocommerce_get_breadcrumb', '__return_false' );
    add_filter( 'loop_shop_per_page', array($this, 'change_products_per_page') );

    $this->_rearrange_single_product();
  }

  /*
    Change Products-per-page
    @filter loop_shop_per_page

    @param int $num - Current number of products per page
  */
  function change_products_per_page( $num ) {
    return get_option( 'posts_per_page' );
  }

  /////

  /*
    Rearrange the elements in single product page
  */
  private function _rearrange_single_product() {
    // Move UPSELL and RELATED products to bottom
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

    // Move DESCRIPTION to center panel
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
    add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs' );

    // move the PRICE and VARIATION to right-sidebar
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
    add_action( 'woocommerce_after_single_product_summary', 'woocommerce_template_single_price' );
    add_action( 'woocommerce_after_single_product_summary', 'woocommerce_template_single_add_to_cart' );

    // move TITLE, SHARING, and RATING to top
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
    add_action( 'woocommerce_before_single_product', 'woocommerce_template_single_title' );
    add_action( 'woocommerce_before_single_product', 'woocommerce_template_single_sharing' );
    add_action( 'woocommerce_before_single_product', 'woocommerce_template_single_rating' );
  }
}