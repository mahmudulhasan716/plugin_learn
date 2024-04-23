<?php

function form_shortcode() {

  $output = '
    <form method="post" action="'. esc_url( $_SERVER['REQUEST_URI'] ).'">
        <label for="name">Name:</label><br>
        <input type="text" name="name"><br>
        
        <label for="email">Email:</label><br>
        <input type="email" name="email"><br>
        
        <label for="message">Phone:</label><br>
        <input type="phone" name="phone"><br>

        <label for="zip_code">Zip Code:</label><br>
        <input type="number" name="zip_code"><br>

        <label for="link">Link:</label><br>
        <input type="text" name="link"><br>


        <label >Description:</label><br>
        <textarea name="description"> </textarea> <br>
        
        <input type="submit" name="submit" value="Submit">
    </form>';

    return $output;
}

add_shortcode('custom_form', 'form_shortcode');

function form_submission(){
    if ( isset( $_POST['submit'] ) ) {
        
        global $wpdb;
        $table_name = 'information';
        
        $name= sanitize_text_field( $_POST['name']);
        $email = sanitize_email( $_POST['email']);

        $phone_data = $_POST['phone'];

        if(preg_match( "/[0-9]/", $phone_data)){
            $phone = $phone_data;
        }

        $link = sanitize_url($_POST['link']) ;
        $zip_data = $_POST['zip_code'];

        if ( 10 < strlen( trim( $zip_data ) ) ) {
                //return false;
            } else{
                $zip_code = $zip_data;
            }

        $description = sanitize_textarea_field( $_POST['description']);

        $wpdb->insert($table_name, array(
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'description' => $description,
            'zip_code' => $zip_code,
            'link' => $link,
        ), array('%s', '%s', '%d', '%s', '%d', '%s')
    );
    
    wp_redirect( $_SERVER['REQUEST_URI'] );
        exit;
    }

    if (isset($_POST['update'])) {

        global $wpdb;
        $table_name = 'information';

        $update_id =$_POST['id'];
        
        $data = array(
            'name' => sanitize_text_field( $_POST['name']),
            'email' => sanitize_email( $_POST['email']),
            'phone' => $_POST['phone'],
            'description' => $_POST['description'],
            'zip_code' => $_POST['zip_code'],
            'link' => $_POST['link'],
        );
        $wpdb->update($table_name, $data, array('id' => $update_id));

        wp_redirect( $_SERVER['REQUEST_URI'] );
        exit;
    }
}

add_action( 'init', 'form_submission' );

?>
