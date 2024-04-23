<?php
/**
 * Plugin Name: User Meta
 */

 function data_filed_extand($user){
    ?>
<table>
    <th> Location</th>
    <td>
        <input type="text" name="meta_location" class="form_control"
            value="<?php echo get_user_meta($user->ID, 'meta_location', true  )  ?>" />
    </td>
</table>

<?php
 }
 add_action( 'show_user_profile', 'data_filed_extand' );
 add_action( 'edit_user_profile', 'data_filed_extand' );

function save_data_field_data($user_id) {
        update_user_meta($user_id, 'meta_location', sanitize_text_field($_POST['meta_location']));
}

// Save additional fields data when user updates profile
add_action('personal_options_update', 'save_data_field_data');
add_action('edit_user_profile_update', 'save_data_field_data');



add_filter( 'woocommerce_customer_meta_fields', 'billing_custom_filed' );
function billing_custom_filed( $fields ) {
	
	$fields[ 'billing' ][ 'fields' ][ 'billing_new_filed' ] = array(
		'label' => 'Another Address',
	);

	return $fields;
	
}


// Hook into WooCommerce admin menu
add_action( 'admin_menu', 'add_custom_menu_to_woocommerce' );

function add_custom_menu_to_woocommerce() {
    // Add a new submenu under the "WooCommerce" menu
    add_submenu_page(
        'woocommerce',
        'profile',
        'Profile',
        'manage_woocommerce',
        'my-custom-menu',
        'my_custom_menu_profile'
    );
}

function my_custom_menu_profile() {
    
    $user_id = get_current_user_id();
    
    $billing_first_name = get_user_meta($user_id, 'billing_first_name', true);
    $billing_last_name = get_user_meta($user_id, 'billing_last_name', true);
    $billing_company = get_user_meta($user_id, 'billing_company', true);
    $billing_address_1 = get_user_meta($user_id, 'billing_address_1', true);
    $billing_address_2 = get_user_meta($user_id, 'billing_address_2', true);

    $shipping_first_name = get_user_meta($user_id, 'shipping_first_name', true);
    $shipping_last_name = get_user_meta($user_id, 'shipping_last_name', true);
    $shipping_company = get_user_meta($user_id, 'shipping_company', true);
    $shipping_address_1 = get_user_meta($user_id, 'shipping_address_1', true);
    $shipping_address_2 = get_user_meta($user_id, 'shipping_address_2', true);


    if (isset($_POST['update_profile'])) {
        update_user_meta($user_id, 'billing_first_name', sanitize_text_field($_POST['billing_first_name']));
        update_user_meta($user_id, 'billing_last_name', sanitize_text_field($_POST['billing_last_name']));
        update_user_meta($user_id, 'billing_company', sanitize_text_field($_POST['billing_company']));
        update_user_meta($user_id, 'billing_address_1', sanitize_text_field($_POST['billing_address_1']));
        update_user_meta($user_id, 'billing_address_2', sanitize_text_field($_POST['billing_address_2']));


        update_user_meta($user_id, 'shipping_first_name', sanitize_text_field($_POST['shipping_first_name']));
        update_user_meta($user_id, 'shipping_last_name', sanitize_text_field($_POST['shipping_last_name']));
        update_user_meta($user_id, 'shipping_company', sanitize_text_field($_POST['shipping_company']));
        update_user_meta($user_id, 'shipping_address_1', sanitize_text_field($_POST['shipping_address_1']));
        update_user_meta($user_id, 'shipping_address_2', sanitize_text_field($_POST['shipping_address_2']));

        
        echo '<p>Profile updated successfully!</p>';
    }

    ?>
<div>
    <h2> Customize Data </h2>

    <form method="post">
        <table>
            <h3> Customer billing address</h3>

            <tr>
                <th><label>First Name</label></th>
                <td><input type="text" name="billing_first_name" class="form_control"
                        value="<?php echo $billing_first_name ?>" /></td>
            </tr>
            <tr>
                <th><label>Last Name</label></th>
                <td><input type="text" name="billing_last_name" class="form_control"
                        value="<?php echo $billing_last_name ?>" />
                </td>
            </tr>
            <tr>
                <th><label>Company</label></th>
                <td><input type="text" name="billing_company" class="form_control"
                        value="<?php echo $billing_company ?>" /></td>
            </tr>

            <tr>
                <th><label>Address line 1</label></th>
                <td><input type="text" name="billing_address_1" class="form_control"
                        value="<?php echo $billing_address_1 ?>" /></td>
            </tr>
            <tr>
                <th><label>Address line 2</label></th>
                <td><input type="text" name="billing_address_2" class="form_control"
                        value="<?php echo $billing_address_2 ?>" /></td>
            </tr>

        </table>

        <table>
            <h3> Customer shipping address </h3>

            <tr>
                <th><label>First Name</label></th>
                <td><input type="text" name="shipping_first_name" class="form_control"
                        value="<?php echo $shipping_first_name ?>" /></td>
            </tr>
            <tr>
                <th><label>Last Name</label></th>
                <td><input type="text" name="shipping_last_name" class="form_control"
                        value="<?php echo $shipping_last_name ?>" />
                </td>
            </tr>
            <tr>
                <th><label>Company</label></th>
                <td><input type="text" name="shipping_company" class="form_control"
                        value="<?php echo $shipping_company ?>" /></td>
            </tr>

            <tr>
                <th><label>Address line 1</label></th>
                <td><input type="text" name="shipping_address_1" class="form_control"
                        value="<?php echo $shipping_address_1 ?>" /></td>
            </tr>
            <tr>
                <th><label>Address line 2</label></th>
                <td><input type="text" name="shipping_address_2" class="form_control"
                        value="<?php echo $shipping_address_2 ?>" /></td>
            </tr>

        </table>



        <input type="submit" name="update_profile" value="Update  Info" />
    </form>


</div>

<?php 


}


 ?>