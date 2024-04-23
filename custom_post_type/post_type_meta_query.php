<?php 
/**
 *  Plugin Name: Post Type With Meta Query
 */




 function meta_query_register_post_type(){

     register_post_type('mb_person_info', array(
    'labels' => array(
        'name' => __('persons Info', 'wplearn'),
        'singlur_name' => __('person Info', 'wplearn'),
        'add_new_item'          => __( 'Add New person', 'wplearn' ),
    ),
    'public' => true,
    'has_archive' => true ,
    'rewrite' => array('slug' => 'mb_person_info'),
 ));

 }

 add_action('init', 'meta_query_register_post_type');




 function mb_person_email($post){
    ?>

<label> Email</label>
<input type="email" name="mb_person_email"
    value="<?php echo esc_attr(get_post_meta($post->ID, 'mb_person_email_unique', true)); ?>" />
<br /> <br>
<label> Height </label>
<input type="number" name="mb_person_height"
    value="<?php echo esc_attr(get_post_meta($post->ID, 'mb_person_height_unique', true)); ?>" />
<br> <br>
<label> Weight </label>
<input type="number" name="mb_person_weight"
    value="<?php echo esc_attr(get_post_meta($post->ID, 'mb_person_weight_unique', true)); ?>" />

<?php
 }


 function meta_box_person_information_add(){
    
    add_meta_box(
        'mb_box_id',
        'Person Email',
        'mb_person_email',
        'mb_person_info'
    );
 }

 add_action('add_meta_boxes', 'meta_box_person_information_add');

function mb_data_store($post_id){
    
     update_post_meta(
        $post_id,
        'mb_person_email_unique',
        $_POST['mb_person_email']
        
     );

      update_post_meta(
        $post_id,
        'mb_person_height_unique',
        $_POST['mb_person_height']
        
     );

      update_post_meta(
        $post_id,
        'mb_person_weight_unique',
        $_POST['mb_person_weight']
        
     );
}

  add_action('save_post', 'mb_data_store');


 function get_personal_info(){

    $person_info = new WP_Query( array(
        'post_type' => 'mb_person_info',
        'post_per_page' => 8,
        'meta_query' => array(
            array(
                'key' => 'mb_person_height_unique',
                'type' => 'NUMERIC',
                'value' => array(50, 80),
                'compare' => 'BETWEEN'
            ),
        ),
    ));
    ob_start();

    while($person_info->have_posts()){
        $person_info->the_post();
        ?>
<div>
    <h3> <?php  esc_html(the_title()); ?> </h3>
    <p>Email: <?php echo esc_html(get_post_meta(get_the_ID(), 'mb_person_email_unique', true)); ?></p>
    <p> Height: <?php echo esc_html(get_post_meta(get_the_ID(), 'mb_person_height_unique', true)); ?></p>
    <p>Weight: <?php echo esc_html(get_post_meta(get_the_ID(), 'mb_person_weight_unique', true)); ?></p>
</div>

<?php
    }
    return ob_get_clean();
 }

 add_shortcode( 'metapost', 'get_personal_info' );