<?php
/**
 * Plugin Name:       Book Review
 * Plugin URI:        #
 * Description:       When an unknown printer took a galley of type and scrambled it to make a type specimen book.
 * Version:           0.0.1
 * Requires at least: 5.2
 * Author:            Leo Tolstoy
 * Author URI:        #
 * License:           GPL v2 or later
 * License URI:       #
 */

/*
 * Exit if accessed directly
 */
if (!defined('ABSPATH')) {
    exit;
}

define('BR_TEXTDOMAIN', 'book-review');
define('BR_DIR', plugin_dir_url( __FILE__ ));

/*
 *Create a class called "Sample_plugin" if it doesn't already exist
 */

if ( !class_exists( 'Book_review' ) ) {

    Class Book_review{

        public function __construct() {
            //add_action('init', array($this, 'book_review_function'));
            add_action('admin_enqueue_scripts', array($this, 'hide_category_scripts'));
            add_shortcode('manage_book_review', array( $this, 'manage_book_review_fn'));
            //register_activation_hook( __FILE__, 'customplugin_table' );
            add_action('wp_enqueue_scripts', array($this, 'callback_for_setting_up_scripts'));
            add_action( 'admin_menu', array($this, 'book_layouts_menu'),100);
        }

        public function book_layouts_menu(){
            add_menu_page( __( 'Book Review', BR_TEXTDOMAIN), 'Book Review', 'manage_options','book-review', 'book_review_function','dashicons-book');
            function book_review_function(){
                include_once('includes/book-review-shortcode.php');
            }
        }
        /**
         * Front Layout
         */
        function manage_book_review_fn() {
            include_once('includes/book-review-front.php');
        }

        /**
         *@return style and script in admin site
         */
        public function hide_category_scripts(){
            wp_enqueue_style( 'admin-custom-style', BR_DIR. '/admin/css/custom_style.css',array());
            wp_enqueue_script( 'admin-custom-js', BR_DIR. '/admin/js/custom_js.js',array());
            wp_enqueue_script( 'admin-scrip-online', 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js');
			
        }
        /**
         *@return style and script in front site
         */
        function callback_for_setting_up_scripts() {
            wp_enqueue_style( 'front-custom-style', BR_DIR. '/front/css/custom_style.css',array());
            wp_enqueue_script( 'front-custom-js', BR_DIR. '/front/js/custom_js.js',array());
            wp_enqueue_script( 'front-scrip-online', 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js');
            wp_enqueue_style( 'front-boostrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
            wp_enqueue_script( 'front-boostrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js');
            wp_enqueue_script( 'front-boostrap', 'https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js');
			wp_enqueue_script( 'front-font-awesome-js', 'https://kit.fontawesome.com/538e211a74.js');
        }
    }
}

/*
 * Created new object of the Book Review.
 */
new Book_review();