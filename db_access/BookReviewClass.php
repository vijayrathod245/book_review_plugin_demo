<?php
Class BookReview{

        public function __construct() {
            add_action('init', array($this, 'br_link_function'));
            add_action('admin_enqueue_scripts', array($this, 'hide_category_scripts'));
            add_shortcode('manage_book_review', array( $this, 'manage_book_review_fn'));
            add_action('wp_enqueue_scripts', array($this, 'callback_for_setting_up_scripts'));
            add_action( 'admin_menu', array($this, 'book_layouts_menu'),100);
        }


        public function book_layouts_menu(){
            add_menu_page( __( 'Book Review', BR_TEXTDOMAIN), 'Book Review', 'manage_options','book-review', 'book_review_function','dashicons-book');
            function book_review_function(){
                include_once( BR_DIR. '/includes/book-review-shortcode.php');
            }
        }
        public function br_link_function() {
			//include_once('includes/book-review-shortcode.php');
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
            wp_enqueue_style( 'front-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css');
            wp_enqueue_script( 'front-boostrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js');
			wp_enqueue_script( 'front-font-awesome-js', 'https://kit.fontawesome.com/538e211a74.js');
        }
    }
/*
 * Created new object of the Book Review.
 */
new BookReview();