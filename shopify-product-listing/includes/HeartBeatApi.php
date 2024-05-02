<?php 

 namespace WeLabs\ShopifyProductListing;

class HeartBeatApi {

    public function __construct(){
        // Register activation hook
       

        add_action('admin_menu', [ $this, 'message_menu_page']);
        add_action('init', [$this,'enqueue_heat_beat_api_scripts']);
        add_action( 'wp_ajax_send_message', [ $this,'heartbeat_date_save' ] );

        add_filter('heartbeat_received', [$this,'hapi_heartbeat_received'], 10, 2); // For logged in users
        add_filter('heartbeat_nopriv_received', [$this,'hapi_heartbeat_received'], 10, 2);

        //  add_action('wp_ajax_get_data_from_other_user', [$this, 'hapi_heartbeat_received']);
        //  add_action('wp_ajax_nopriv_get_data_from_other_user', [$this, 'hapi_heartbeat_received']); 
         add_action('wp_ajax_get_data_from_other_user', [$this, 'get_data_from_other_user']);
         add_action('wp_ajax_nopriv_get_data_from_other_user', [$this, 'get_data_from_other_user']); 
        
    }

    

    public function message_menu_page(){
        add_submenu_page(
            'my-custom-menu',
            'message api',    // Page title
            'Mssage',    // Menu title
            'manage_options', // Capability required to access the menu page
            'my-message-menu', // Menu slug
            array($this, 'my_custom_message_page_content'), // Callback function to display the menu page content 
        );
    }

    public function enqueue_heat_beat_api_scripts(){
        wp_enqueue_script(
        'heat-beat-api', // Unique handle for your script
        plugin_dir_url(__FILE__) . 'inc_file/heart_beat_api.js',
        ['jquery', 'heartbeat'],
        time(),
        true // Whether to enqueue the script in the footer (true) or in the header (false)
        );
    }

    public function my_custom_message_page_content(){
        ?>
<div style="margin-top: 20px">
    <div id="wp-admin-chat">
        <h2>Heartbeat API</h2>
        <input type="hidden" id="receiver_id" value="1">
        <input type="hidden" id="send_message_nonce" value="<?php echo wp_create_nonce( 'send_message_nonce' ); ?>">
        <input type="text" id="my-input" placeholder="Type your message..." value="" />
        <button id="send_message_button">Send</button>
    </div>
    <div>
        <h3> Receive Message </h3>
        <p id="received-message"> </p>

    </div>
</div>
<?php
    }
    
    // public function hapi_heartbeat_received($response, $data){
    //     if ($data['message']) {
    //         $response['message'] = $data['message'];
    //         $response['status'] = 'success';
    //        // $this->save_message_to_database($response);
    //     }
    //     return $response;
    // }

    public function heartbeat_date_save() {
        $message = $_REQUEST['message'];
        $receiver_id = $_REQUEST['receiver_id'];
        $this->save_message_to_database($message, $receiver_id);
        // error_log("My message->".json_encode($message));
    }

    private function save_message_to_database($message, $receiver_id){
        global $wpdb;

        // Assuming you have a table named 'heartbeat_messages'
        $table_name = $wpdb->prefix .'message';
        $sender_id = get_current_user_id();
        

        // Insert the message into the database
        $wpdb->insert(
            $table_name,
            array(
                'messages' => $message,
                'sender_id' => $sender_id,
                'receiver_id' => $receiver_id,
                'created_at' => current_time('mysql'),
            )
        );
    }

    //receive message 

    public function hapi_heartbeat_received($response, $data){

        // if ($data['message']) {
        //      $response['message'] = $data['message'];
        //      $response['status'] = 'success';
        //     }
        // return $response;


         global $wpdb;
        $table_name = $wpdb->prefix . 'message';

        if ($data['message']) {
            $new_data = $wpdb->get_results(
            $wpdb->prepare(
            "SELECT messages FROM $table_name WHERE messages = %s",$data['message']
        ),
            ARRAY_A
        );
         
       //$messages = array_column($new_data, 'messages');

        // Set the response message to the first message in the $messages array
        if ($new_data) {
            // $response['message'] = $new_data[0];
            // $response['status'] = 'success';
            // error_log('got it->'.$response['message']);

            $message = $new_data[0]['messages'];
            $response['message'] = $message;
            $response['status'] = 'success';
           // error_log('got it->'.$response['message']);
        }
         
        }
        return $response;
    }

    public function get_data_from_other_user() {
    //     $response = array(
    //     'message' => 'ok', // Set the message to "ok"
    //     'status' => 'success', // Set the status to "success"
    // );

    // Send the JSON response
    return $response;

    }


}
