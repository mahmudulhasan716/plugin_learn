<?php

namespace WeLabs\ShopifyProductListing;

use WeDevs\Dokan\Product\Manager as ProductManager;

// use function \media_sideload_image;

// require_once(ABSPATH . 'wp-admin/includes/file.php');
// require_once(ABSPATH . 'wp-admin/includes/media.php');
// require_once(ABSPATH . 'wp-admin/includes/image.php');

// use Shopify\Clients\Rest;

class ProductListing extends ProductManager   {

    public function __construct(){
        // add_action( 'init', [$this, 'shopify_product_add'] );
       add_action('admin_menu', [ $this, 'my_custom_menu_page']);
       add_action('admin_init', array($this, 'process_form_submission'));
       add_filter('woocommerce_settings_tabs_array', [$this,'custom_woocommerce_settings_tab' ], 50);
       add_action('woocommerce_settings_tabs_custom_tab', [$this,'custom_woocommerce_settings_content' ]);

       add_action('admin_init', array($this, 'save_custom_woocommerce_settings'));
       
    }


function custom_woocommerce_settings_tab($settings_tabs) {
    $settings_tabs['custom_tab'] = 'Shopify';
    return $settings_tabs;
}

 function custom_woocommerce_settings_content() {
    // Add your custom settings content here
    echo '<h2>Shopify Access </h2>';
    ?>
<label for="shopify_shop_url"> Shop Url </label>
<input type="text" id="shopify_shop_url" name="shopify_shop_url"
    value="<?php echo esc_attr(get_option('shopify_shop_url')); ?>" /> </br> </br>

<label for="shopify_access_token"> Access Token</label>
<input type="text" id="shopify_access_token" name="shopify_access_token"
    value="<?php echo esc_attr(get_option('shopify_access_token')); ?>" /> </br> </br>

<label for="shopify_api_version"> Api Version</label>
<input type="text" id="shopify_api_version" name="shopify_api_version"
    value="<?php echo esc_attr(get_option('shopify_api_version')); ?>" /> </br> </br>

<?php

 }

 public function save_custom_woocommerce_settings() {
        // Save custom settings to the database
    if ( isset( $_POST['shopify_shop_url'] ) ) {
        update_option( 'shopify_shop_url', sanitize_text_field( $_POST['shopify_shop_url'] ) );
    }
         if ( isset( $_POST['shopify_access_token'] ) ) {
        update_option( 'shopify_access_token', sanitize_text_field( $_POST['shopify_access_token'] ) );
    }

    if ( isset( $_POST['shopify_api_version'] ) ) {
        update_option( 'shopify_api_version', sanitize_text_field( $_POST['shopify_api_version'] ) );
    }
    }



function my_custom_menu_page() {
    add_menu_page(
        'Wc Data Listing',    // Page title
        'Shopify to Wc',    // Menu title
        'manage_options', // Capability required to access the menu page
        'my-custom-menu', // Menu slug
        array($this, 'my_custom_menu_page_content'), // Callback function to display the menu page content
        'dashicons-admin-generic', // Icon URL or Dashicons class
    );
}

    function my_custom_menu_page_content() {
       ?>
<div class="wrap">
    <h2>My Custom Menu Page</h2>
    <p>This is where you can add your custom content.</p>
    <a href="?page=my-custom-menu&action=shopify_product_add" class="button">Store</a>
</div>
<?php
}

     function process_form_submission() {
        if (isset($_GET['action']) && $_GET['action'] === 'shopify_product_add') {
            $this->shopify_product_add();
        }

    }

    public function shopify_product_add(){
        
       $shopify = \PHPShopify\ShopifySDK::config([
        'ShopUrl' => "'" . esc_attr(get_option('shopify_shop_url')) . "'",
    'AccessToken' => "'" . esc_attr(get_option('shopify_access_token')) . "'",
    'ApiVersion' => "'" . esc_attr(get_option('shopify_api_version')) . "'",
    
]);

$products = $shopify->Product->get();

foreach($products as $product){

$image_url = $product['images'][0]['src'];

// $new_product = array(
// 'post_title' =>sanitize_text_field($product['title']),
// 'post_content' => sanitize_text_field ($product['body_html']),
// 'post_status' => 'publish',
// 'post_type' => 'product',
// );
$new_product = array(
'name' =>sanitize_text_field($product['title']),
'regular_price' => $product['variants'][0]['price'],
'featured_image_id' => $this->get_thumbnail_id($image_url),
'status' => 'draft',
// 'post_type' => 'product',
);


$product = $this->create($new_product);

// $woocommerce_product_id = wp_insert_post($new_product);

// $product_thumbnail_ids = array();

// foreach ($product['images'] as $image) {

// $image_url = $image['src'];

// $image_id = media_sideload_image($image_url, $woocommerce_product_id, $product['title']);


// if (!is_wp_error($image_id) && !empty($image_id)) {
// $product_thumbnail_ids[] = $image_id;
// }
// }


// if ($product_thumbnail_ids) {
// set_post_thumbnail($woocommerce_product_id, $product_thumbnail_ids[0]);
// }
// update_post_meta($woocommerce_product_id, '_product_image_gallery', implode(',', $product_thumbnail_ids));


// update_post_meta($woocommerce_product_id, '_regular_price', $price);
// update_post_meta($woocommerce_product_id, '_price', $price);

}

}


public function get_thumbnail_id($image) {


$attachment = new Attachment();

try {
$id = $attachment->get_attachment_id_from_url( $image, 0 );
} catch ( \Throwable $th ) {
return 0;
}

return $id;
}





}

?>
