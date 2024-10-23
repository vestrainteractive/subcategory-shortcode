<?php
/**
 * Plugin Name: Subcategory List with Thumbnails
 * Plugin URI:  https://github.com/vestrainteractive/subcategory-shortcode
 * Description: Displays a list of subcategories of the current category with thumbnails.
 * Version: 1.0
 * Author: Vestra Interactive
 * Author URI: # https://vestrainteractive.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: subcategory-list-thumbs
 * Domain Path: /languages/

 * Requires at least: 5.0
 * Tested up to: 6.1

 */

// Check if Categories Images plugin is active
if ( ! function_exists( 'z_taxonomy_image' ) ) {
  add_action( 'admin_notices', 'subcategory_list_thumbs_notice' );
  return;
}

function subcategory_list_thumbs_notice() {
  ?>
  <div class="notice notice-error">
    <p>
      **Subcategory List with Thumbnails Plugin:** This plugin requires the "Categories Images" plugin to be active. Please install and activate "Categories Images" to use this plugin.
    </p>
  </div>
  <?php
}

// Shortcode function
function subcategory_list_thumbs_shortcode( $atts ) {

  // Get current category object
  $current_category = get_queried_object();

  // Check if we are on a category page
  if ( ! $current_category || ! $current_category->taxonomy == 'category' ) {
    return;
  }

  // Get arguments for subcategories
  $args = array(
    'taxonomy' => 'category',
    'child_of' => $current_category->term_id,
    'hide_empty' => false,
  );

  // Get subcategories
  $subcategories = get_categories( $args );

  // If no subcategories found, return
  if ( empty( $subcategories ) ) {
    return;
  }

  $output = '<div class="widget_sub_categories"><ul class="subcategory-list subcategory_list_thumbs" style="list-style:none !Important;padding-left:0px !Important;">';

  foreach ( $subcategories as $subcategory ) {
    $thumbnail_url = z_taxonomy_image_url( $subcategory->term_id, 'category_thumb' );

    $output .= '<li class="cat-item">';
    
    if ( $thumbnail_url ) {
      $output .= '<a href="' . get_term_link( $subcategory ) . '"><img src="' . $thumbnail_url . '" alt="' . esc_attr( $subcategory->name ) . '" /></a>';
    }

    $output .= '<a href="' . get_term_link( $subcategory ) . '">' . $subcategory->name . '</a>';
    $output .= '</li>';
  }

  $output .= '</ul></div>';

  return $output;
}

// Add shortcode
add_shortcode( 'subcategory_list_thumbs', 'subcategory_list_thumbs_shortcode' );

// Include the GitHub Updater class
add_action('plugins_loaded', function() {
    $file = plugin_dir_path( __FILE__ ) . 'class-github-updater.php';

    if ( file_exists( $file ) ) {
        require_once $file;
        error_log( 'GitHub Updater file included successfully.' );
    } else {
        error_log( 'GitHub Updater file not found at: ' . $file );
    }

    // Ensure the class exists before instantiating
    if ( class_exists( 'GitHub_Updater' ) ) {
        // Initialize the updater
        new GitHub_Updater( 'estimated-read-time', 'https://github.com/vestrainteractive/estimated-read-time', '1.0.0' ); // Replace with actual values
        error_log( 'GitHub Updater class instantiated.' );
    } else {
        error_log( 'GitHub_Updater class not found.' );
    }
});
