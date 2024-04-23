<?php 


 //register_post_type();

 if(! function_exists('custom_post_testimonial') ){

    function custom_post_testimonial(){
        register_post_type('learn_testimonial', array(
            'labels'=> array(
                'name' => __('Testimonials', 'learntextdomain'),
                'singular_name' => __('Testimonial', 'learntextdomain'),
            ),
            'public' =>true,
            'has_archive' => true,
            'taxonomies' => array('designation')
        ));
    }
 }

 add_action('init', 'custom_post_testimonial');


 function testimonial_designation(){

    register_taxonomy('designation', ['learn_testimonial'], array(
    'hieraarchical' => true,
    'labels' => array(
        'name' => __('Designations', 'learntextdomain'),
        'singular_name' => __('designation ', 'learntextdomain'),
    )
 ));

 }

 add_action('init','testimonial_designation' );

 //ADD META BOX

 function email_filed_func($post){
        ?>

<label for="email"> Email</label>
<input type="email" name="meta_box_email" id="email"
    value='<?php echo get_post_meta($post->ID,'meta_box_email_uq', true);   ?>' /> <br /> <br />

<label for="job-title"> job Title</label>
<input type="text" name="meta_box_job-title" id="job-title"
    value='<?php echo get_post_meta($post->ID,'meta_box_job-title_uq', true);   ?>' />

<?php
 }


 function email_meta_box(){
    
    add_meta_box(
        'meta_box_id',
        'Email Address',
        'email_filed_func',
        'learn_testimonial'
    );
 }

 add_action('add_meta_boxes', 'email_meta_box');

 function save_email_meta_box($post_id){

         update_post_meta(
        $post_id,
        'meta_box_email_uq',
        $_POST['meta_box_email']
    );

     update_post_meta(
        $post_id,
        'meta_box_job-title_uq',
        $_POST['meta_box_job-title']
    );


   
 }

 add_action('save_post', 'save_email_meta_box' );



 function get_testimonial_info(){
    $testimonial_info =  new WP_Query( array(
    'post_type' => 'learn_testimonial',
    'posts_per_page' => 8,
    
 ));
    ob_start();

    while($testimonial_info->have_posts()){
        $testimonial_info->the_post();
        ?>

<div>
    <h2> Title: <?php esc_html( the_title() ) ; ?> </h2>
    <span> Email: <?php echo esc_html( get_post_meta(get_the_ID(),'meta_box_email_uq', true) ) ; ?> </span> <br>
    <span> Job Title: <?php echo esc_html( get_post_meta(get_the_ID(),'meta_box_job-title_uq', true) ) ; ?> </span>
    <p> Content: <?php esc_html(the_content() ); ?> </p>

    <br>
    <br>
    <br>
    <br>
    <br>

</div>

<?php

    }

    return ob_get_clean();

 }


 add_shortcode( 'learn_testimonial', 'get_testimonial_info' );
