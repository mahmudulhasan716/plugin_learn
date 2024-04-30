<?php  

namespace WeLabs\ShopifyProductListing;


$wp_list_table_path = ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

if (file_exists($wp_list_table_path)) {
    require_once $wp_list_table_path;
}

class WpList extends \WP_List_Table{

    function prepare_items(){
        $order_by = isset($_GET['orderby']) ? $_GET['orderby'] : '';
        $order = isset($_GET['order']) ? $_GET['order'] : '';
        $search_form = isset($_POST['s']) ? sanitize_text_field($_POST['s']) : '';

        $this->items = $this->learn_list_table_data($order_by,$order,$search_form);

        $lear_colum = $this->get_columns();
        $learn_hd_colum = $this->get_hidden_columns();
        $this->_column_headers = [$lear_colum,$learn_hd_colum ];


        /**
         * pagination
         */
        
        $posts_per_page = $this->get_items_per_page( 'users_network_per_page' );
        $paged = $this->get_pagenum();
        $usersearch = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';

         $args = array(
			'number'  => $posts_per_page,
			'offset'  => ( $paged - 1 ) * $posts_per_page,
			'search'  => $usersearch,
			'post_type'         => 'product',
            'post_status'       => 'published',
		);

        $my_posts = count(get_posts($args));

        $page_data = array(
                    'total_items' => $my_posts,
                    'per_page'    => $posts_per_page,
        );
        $this->set_pagination_args($page_data);

        /**
         * for filter
         */

         if ( isset($_REQUEST['category']) && !empty($_REQUEST['category']) ) {
            $category = sanitize_text_field($_REQUEST['category']);

              $this->items = $this->learn_filter_table_data($category);
            
           // Add category filter to your query
            // $args['tax_query'] = array(
            //     array(
            //         'taxonomy' => 'product_cat',
            //         'field'    => 'slug',
            //         'terms'    => $category,
            //     ),
            // );
        }
    }

    public function learn_filter_table_data($category=''){
         $data_array = [];

        // $args = [
        //     'post_type'         => 'product',
        //     'post_status'       => 'publish',
        //     'posts_per_page'    => -1,
        //     'category'     => $category,
        // ];


        $args = [
            'post_type' => 'product',
            'tax_query' => [
                [
                    'taxonomy' => 'product_cat',
                    'terms' =>$category,
                ],
            ],
        ];

        $my_posts = get_posts($args);
        
        if( $my_posts) {
            foreach($my_posts as $posts){

                $categories = wp_get_object_terms($posts->ID, 'product_cat');
                    $category_names = array();
                    foreach ($categories as $category) {
                        $category_names[] = $category->name;
                    }
                $category_list = implode(', ', $category_names);

                $tags = wp_get_object_terms($posts->ID, 'product_tag');
                    $tag_names = array();
                    foreach ($tags as $tag) {
                        $tag_names[] = $tag->name;
                    }
                $tag_list = implode(', ', $tag_names);

                $data_array[] =
                 [
                    'cb' => '<input type="checkbox" name="sku[]" value="' . $posts->ID . '" />',
                    'image' => get_the_post_thumbnail($posts->ID, array( 30, 30)),
                    'name' => $posts->post_title,
                    'sku' => $posts->ID,
                    'stock' => 'instack',
                    'price' =>  200,
                    'category' =>  $category_list,
                    'tags' =>  $tag_list,
                    'date' =>  $posts->post_date,
                    'post_status' => $posts->post_status,
                ];    
            }
        }
        return $data_array;

    }

    function learn_list_table_data($order_by = '',$order = '',$search_form= ''){
        $data_array = [];

        $args = [
            'post_type'         => 'product',
            'post_status'       => 'published',
            'posts_per_page'    => -1,
            's'                 => $search_form,
        ];
        $my_posts = get_posts($args);

        if( $my_posts) {
            foreach($my_posts as $posts){

                $categories = wp_get_object_terms($posts->ID, 'product_cat');
                    $category_names = array();
                    foreach ($categories as $category) {
                        $category_names[] = $category->name;
                    }
                $category_list = implode(', ', $category_names);

                $tags = wp_get_object_terms($posts->ID, 'product_tag');
                    $tag_names = array();
                    foreach ($tags as $tag) {
                        $tag_names[] = $tag->name;
                    }
                $tag_list = implode(', ', $tag_names);

                $data_array[] =
                 [
                    'cb' => '<input type="checkbox" name="sku[]" value="' . $posts->ID . '" />',
                    'image' => get_the_post_thumbnail($posts->ID, array( 30, 30)),
                    'name' => $posts->post_title,
                    'sku' => $posts->ID,
                    'stock' => 'instack',
                    'price' =>  200,
                    'category' =>  $category_list,
                    'tags' =>  $tag_list,
                    'date' =>  $posts->post_date,
                    'post_status' => $posts->post_status,
                ];  
            }
        }
        return $data_array;
    }

    /**
     * List Table Bulk Action
     */
     public function get_bulk_actions(){
      return  array(
            'learn_delete' =>  __('Delete','TextDomain'),
            'learn_edit'   => __('Edit', 'TextDomain'),
        );
     }

     /**
     * List Table Row Action
     */
    public function handle_row_actions( $item, $column_name, $primary ){
        if( $primary !== $column_name ){
            return '';
        }
        $action=  [];
        $action['edit'] = '<a>'. __('Edit', 'TextDomain') . '</a>';
        $action['delete'] = '<a>'. __('Delete', 'TextDomain') . '</a>';
        $action['quick-edit'] = '<a>'. __('Update', 'TextDomain') . '</a>';
        $action['view'] = '<a>'. __('View', 'TextDomain') . '</a>';

        return $this->row_actions($action);
    }

	

    function get_hidden_columns(){
        
        return [];
    }

    function get_columns(){
        $column = [
            'cb' => '<input type="checkbox"  />',
            'image' => __( 'Image', 'textdomain'),
            'name' => __('Name', 'textdomain'),
            'sku' => __('SKU', 'textdomain'),
            'stock' => __('Stock', 'textdomain'),
            'price' => __('Price', 'textdomain'),
            'category' => __('Category', 'textdomain'),
            'tags' => __('Tags', 'textdomain'),
            'date' => __('Date', 'textdomain'),
            'post_status' => __('Post Status', 'textdomain'),
        ];

        return $column;
    }

    function column_default($item, $column_name){
        
        switch ($column_name){
            case 'cb':
            case 'image':
            case 'name':
            case 'sku':
            case 'stock':
            case 'price':
            case 'category':
            case 'tags':
            case 'date':
            case 'post_status':
                return $item[$column_name];
            default:
            return 'No data found';
            
        };
    }

    /**
     * delete with bulk actin
     */

     public function process_bulk_action() {
        
        // Check if the bulk action is triggered
        $action = $this->current_action();
        if ( 'learn_delete' === $action ) {
            // Get the IDs of selected items
            $ids = isset( $_REQUEST['sku'] ) ? $_REQUEST['sku'] : array();

            if ( ! empty( $ids ) ) {
                foreach ( $ids as $id ) {
                    wp_delete_post( $id, true ); // Set the second parameter to true to force delete
                }
            }
        }
    }

    /**
     * filter
     */


     


/**
 * item list a checkbok show korbe
 */
    function column_cb($item){
        $checkbox = '<input type="checkbox" name="sku[]" value="' . $item['sku'] . '"/>';
        return $checkbox;
    }

    public function display_rows() {
		foreach ( $this->items as $userid => $user_object ) {
			echo "\n\t" . $this->single_row( $user_object, '', '', 0 );
		}
}


    public function display() {
        echo '<form method="post" action="' . esc_url( get_admin_url( null, 'admin.php?page=my-custom-menu' ) ) . '">';

        // $this->print_hidden_form_fields();
        $this->search_box( esc_html__( 'Search Post', 'custom-search' ), 'customer-search-input' );

        parent::display();

        echo '</form>
    </div>';
    }

    function extra_tablenav($which){
        if ( $which == "top" ){
            // Output your select dropdown here
            
            $categories =  get_terms();
            echo '<select name="category">';
            echo '<option value="">Filter by Category</option>';
            foreach ( $categories as $category ) {
                $selected = ( isset($_REQUEST['category']) && $_REQUEST['category'] == $category->slug ) ? 'selected="selected"' : '';
                echo '<option value="' . esc_attr($category->term_id) . '" ' . $selected . '>' . esc_html($category->name) . '</option>';
            }
            echo '</select>';
            submit_button( __('Filter'), 'button', false, false, array('id' => 'category-filter') );
        }
    }
}