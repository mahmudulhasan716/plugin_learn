<?php

namespace WeLabs\ShopifyProductListing;




// use WeDevs\Dokan\Product\Manager as ProductManager;

//  use function media_sideload_image();

require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

// use Shopify\Clients\Rest;
// extends ProductManager
class ProductListing   {

    public function __construct(){
        // add_action( 'init', [$this, 'shopify_product_add'] );
       add_action('admin_menu', [ $this, 'my_custom_menu_page']);
       add_action('admin_init', array($this, 'process_form_submission'));
       add_filter('woocommerce_settings_tabs_array', [$this,'custom_woocommerce_settings_tab' ], 50);
       add_action('woocommerce_settings_tabs_custom_tab', [$this,'custom_woocommerce_settings_content' ]);

       add_action('admin_init', array($this, 'save_custom_woocommerce_settings'));

       add_action('shopify_product_add_cron', [$this, 'shopify_product_add_cron']);
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
         $file_path = plugin_dir_path(__FILE__) . 'inc_file/wp_list_table.php';
            if (file_exists($file_path)) {
                require_once $file_path;
            } else {
                error_log('File does not exist: ' . $file_path);
            }
            
            $post_list_table = new WpList();
            $post_list_table->prepare_items();
            
            echo '<form method="post">';
            $post_list_table->process_bulk_action();
            echo '</form>';
             $post_list_table->display();

            }

     function process_form_submission() {
        if (isset($_GET['action']) && $_GET['action'] === 'shopify_product_add') {
           //  $this->shopify_product_add();
         //    wp_schedule_single_event(time(), 'shopify_product_add_cron');
          //  wp_next_scheduled('shopify_product_add_cron');

        //   $timestamp = strtotime('now');
        //   wp_schedule_single_event($timestamp, 'shopify_product_add_cron');


        ///woocommerce install thakle: 'hock name', array
          as_enqueue_async_action(
            'shopify_product_add_cron',
            array()
            );
        }
    }

    function shopify_product_add_cron() {
      $this-> shopify_product_add();
    }

    public function shopify_product_add(){
       $shopify = \PHPShopify\ShopifySDK::config([
        'ShopUrl' => esc_attr(get_option('shopify_shop_url')) ,
        'AccessToken' =>  esc_attr(get_option('shopify_access_token')) ,
        'ApiVersion' =>  esc_attr(get_option('shopify_api_version')) ,
        ]);

   $products = $shopify->Product->get();

    foreach($products as $product){
    $new_product = array(
    'post_title' =>sanitize_text_field($product['title']),
    'post_content' => sanitize_text_field ($product['body_html']),
    'regular_price' => sanitize_text_field ($product['variants'][0]['price']),
    'stock' => sanitize_text_field($product['variants'][0]['inventory_quantity']),
    'sale_price' => sanitize_text_field($product['variants'][0]['compare_at_price']),
    'thumbnail_url' => sanitize_text_field($product['images'][0]['src']),
    'post_status' => 'publish',
    'post_type' => 'product',
    'categories' => sanitize_text_field( $product['product_type']),
    'tags' => sanitize_text_field( $product['tags']),
    );
    
    // $new_product = array(
    // 'name' =>sanitize_text_field($product['title']),
    // 'regular_price' => $product['variants'][0]['price'],
    // 'featured_image_id' => $this->get_thumbnail_id($image_url),
    // 'post_content' => sanitize_text_field ($product['body_html']),
    // 'status' => 'draft',
    // 'post_type' => 'product',
    // );

    $product = $this->create($new_product);
    }
    }

    public function create($args = []){

       // global $wpdb;
        
        // $wpdb->insert(
        //     $wpdb->posts,
        //     array(
        //         'post_title' => $args['post_title'],
        //         'post_content' =>$args['post_content'],
        //         'post_status' => 'publish',
        //         'post_type' => 'product',
        //     )
        // );

         $product_id = wp_insert_post($args);

        //$product_id = $wpdb->insert_id;

        update_post_meta($product_id, '_regular_price', $args['regular_price']);
        update_post_meta($product_id, '_price', $args['sale_price']);
        update_post_meta($product_id, '_stock', $args['stock']);
        // update_post_meta($product_id, '_sku', $sku);

        if ($args['thumbnail_url']) {
         $thumbnail_id = media_sideload_image($args['thumbnail_url'], $product_id, '', 'id');
         

        if (!is_wp_error($thumbnail_id)) {
            set_post_thumbnail($product_id, $thumbnail_id);
        }

        if (!empty($args['tags'])) {
        $tags = explode(',', $args['tags']);
        wp_set_object_terms($product_id, $tags, 'product_tag', true);
       // wp_set_post_tags($product_id, $tags, true);
        }

        if (!empty($args['categories'])) {
        $categories = explode(',', $args['categories']);
        wp_set_object_terms($product_id, $categories, 'product_cat', true);

        // The  'product_cat' specifies the taxonomy where the terms should be set. 'product_cat' is the default taxonomy used for product categories in WooCommerce. Make sure to replace it with the actual taxonomy name for categories in your WordPress setup if it's different.
}
        

    }
        // $wpdb->insert(
        //     $wpdb->postmeta,
        //     array(
        //         'post_id' => $product_id,
        //          'meta_key' => '_regular_price'
        //         'value' => '',
        //         
        //     )
        // );
        
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