<?php

function form_datalist() {
    global $wpdb;
    $table_name = 'information';
    $output = '';
    
    $datalist = $wpdb->get_results("SELECT * FROM $table_name", OBJECT);

     if (isset($_GET['delete_id'])) {
        $delete_id = $_GET['delete_id'];
        $wpdb->delete($table_name, array('id' => $delete_id));
    }

      if (isset($_POST['update'])) {

        $update_id =$_POST['id'];

        
        
        $data = array(
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone']
        );
        $wpdb->update($table_name, $data, array('id' => $update_id));
    }

    $output .= '
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Number</th>
                <th>zip_code</th>
                <th>Link</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>';

    foreach ($datalist as $data) {
        $output .= '
        <tr>
            <td>' . esc_html($data->name) . '</td>
            <td>' . esc_attr($data->email)  . '</td>
            <td>' . esc_attr($data->phone)   . '</td>
            <td>' . esc_attr($data->zip_code). '</td>
            <td>' . esc_attr($data->link)  . '</td>
            <td>' . esc_attr( $data->description) . '</td>
            <td>
                <a href="?edit_id=' . $data->id . '">Edit</a>
                <a href="?delete_id=' . $data->id . '">Delete</a>
            </td>
        </tr>';
    }

    $output .= '
        </tbody>
    </table>';

    if (isset($_GET['edit_id'])) {
        $edit_id =$_GET['edit_id'];

        $record = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $edit_id", OBJECT);

        $output .= '
        <h2>Edit Record</h2>
        <form method="post" action="'.esc_url($_SERVER['REQUEST_URI']).'">
            <input type="hidden" name="id" value="' . $edit_id . '">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="' . $record->name. '" required>
            <br>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="' .$record->email. '" required>
            <br>
            <label for="email">Number:</label>
            <input type="phone" name="phone" id="email" value="' .$record->phone. '" required>
            <br>
            <input type="submit" name="update" value="Update">
        </form>';
    }


    return $output;
}

add_shortcode('data_list', 'form_datalist');
