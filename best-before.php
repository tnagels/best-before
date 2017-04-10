<?php
/*
Plugin Name: Best-Before Date
Plugin URI: http://thomasnagels.be/
Description: 'Best Before' dates for WordPress posts
Version: 1.0
Author: Thomas Nagels
Author URI: http://thomasnagels.be
License: GPLv2 or later
*/

// Add the metabox

function best_before_add_box()
{
  add_meta_box(
    'best_before_date',           // Unique ID
    'Best Before Date',  // Box title
    'best_before_box_html',  // Content callback, must be of type callable
    'post'                   // Post type
  );
}
add_action('add_meta_boxes', 'best_before_add_box');

// Metabox html

function best_before_box_html($post)
{
    $value = get_post_meta($post->ID, '_best_before_meta_key', true);
    ?>
    <label for="best_before_field">The date before which this post is best read:</label></br>
    <input type="date" name="best_before_field" id="best_before_field" class="postbox" value="<?php echo($value); ?>"/>
    <?php
}

// Save metadata

function best_before_save_postdata($post_id)
{
    if (array_key_exists('best_before_field', $_POST)) {
        update_post_meta(
            $post_id,
            '_best_before_meta_key',
            $_POST['best_before_field']
        );
    }
}
add_action('save_post', 'best_before_save_postdata');

// Add best before to the date.

function best_before_date($date) {
  $post_id = get_the_ID();
  $bb = get_post_meta($post_id,'_best_before_meta_key', true);
  if(!empty($bb)){
    return $date.' (best before '.$bb.')';
  }
  return $date;
}

add_filter( 'get_the_date', 'best_before_date' );

// Add best before Content

function best_before_content($content) {
  $post_id = get_the_ID();
  $bb = get_post_meta($post_id,'_best_before_meta_key', true);
  if(!empty($bb)){
    if(current_time( 'timestamp' ) > strtotime($bb))
    return '<p><i>This post is outdated since '.$bb.'. It may no longer represent current state of the art. </i></p>'.$content;
  }
  return $content;
}

add_filter( 'the_content', 'best_before_content' );
