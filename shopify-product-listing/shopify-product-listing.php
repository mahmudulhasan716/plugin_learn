<?php
/**
 * Plugin Name: Shopify Product Listing
 * Plugin URI:  https://welabs.dev
 * Description: Custom plugin by weLabs
 * Version: 0.0.1
 * Author: WeLabs
 * Author URI: https://welabs.dev
 * Text Domain: shopify-product-listing
 * WC requires at least: 5.0.0
 * Domain Path: /languages/
 * License: GPL2
 */
use WeLabs\ShopifyProductListing\ShopifyProductListing;

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'SHOPIFY_PRODUCT_LISTING_FILE' ) ) {
    define( 'SHOPIFY_PRODUCT_LISTING_FILE', __FILE__ );
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Load Shopify_Product_Listing Plugin when all plugins loaded
 *
 * @return \WeLabs\ShopifyProductListing\ShopifyProductListing
 */
function welabs_shopify_product_listing() {
    return ShopifyProductListing::init();
}

// Lets Go....
welabs_shopify_product_listing();
