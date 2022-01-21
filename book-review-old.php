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
// Create a new table
    
function wnm_install() {

    global $wpdb, $wnm_db_version;

    $sql = array();

    //sms table
    $sms_table = $wpdb->prefix . "foo20";

    if( $wpdb->get_var("show tables like '". $sms_table . "'") !== $sms_table ) { 

        $sql[] = "CREATE TABLE ". $sms_table . "     (
            bcat_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            bcat_country_code char(8) NOT NULL,
            bcat_asin varchar(255) NOT NULL,
            bcat_author_wpuid int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'FK (Wordpress user table user id)',
            bcat_title varchar(255) NOT NULL,
            bcat_book_format tinyint(2) UNSIGNED NOT NULL DEFAULT '1',
            bcat_isbn_13 varchar(255) NOT NULL,
            bcat_amazon_permalink text NOT NULL,
            bcat_cover_image varchar(255) NOT NULL,
            bcat_sample_book_file varchar(255) NOT NULL,
            bcat_full_length_book_file varchar(255) NOT NULL,
            bcat_is_active tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
            bcat_adt datetime NOT NULL,
            PRIMARY KEY (bcat_id),
            UNIQUE KEY `bcat_asin_2` (`bcat_asin`),
            KEY bcat_country_code (bcat_country_code),
            KEY bcat_asin (bcat_asin),
            KEY bcat_author_wpuid (bcat_author_wpuid),
            KEY bcat_title (bcat_title),
            KEY bcat_book_format (bcat_book_format),
            KEY bcat_isbn_13 (bcat_isbn_13),
            KEY bcat_is_active (bcat_is_active),
            KEY bcat_adt (bcat_adt)
        ) ";

    }

    //sms messages table
    $sms_message_table = $wpdb->prefix . "foo22";
    
    if( $wpdb->get_var("show tables like '". $sms_message_table . "'") !== $sms_message_table ) { 

        $sql[] = "CREATE TABLE ". $sms_message_table . "   (
            brc_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            brc_bcat_id int(10) UNSIGNED NOT NULL COMMENT 'FK (Wordpress user table user id)',
            brc_bcat_author_wpuid int(10) UNSIGNED NOT NULL COMMENT 'FK (Wordpress user table user id)',
            brc_reviewer_wpuid int(10) UNSIGNED NOT NULL,
            brc_request_adt datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            brc_request_status tinyint(2) UNSIGNED NOT NULL DEFAULT '1',
            brc_pending_adt datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            brc_approved_adt datetime DEFAULT NULL,
            brc_received_adt datetime DEFAULT NULL,
            brc_completed_adt datetime DEFAULT NULL,
            brc_denied_adt datetime DEFAULT NULL,
            brc_denied_reason varchar(255) DEFAULT NULL,
            brc_turnaround_time tinyint(2) UNSIGNED NOT NULL DEFAULT '0',
            PRIMARY KEY (brc_id),
            UNIQUE KEY `brc_bcat_id_2` (`brc_bcat_id`,`brc_bcat_author_wpuid`,`brc_reviewer_wpuid`),
            KEY `brc_turnaround_time` (`brc_turnaround_time`),
            KEY `brc_bcat_id` (`brc_bcat_id`),
            KEY `brc_bcat_author_wpuid` (`brc_bcat_author_wpuid`),
            KEY `brc_reviewer_wpuid` (`brc_reviewer_wpuid`),
            KEY `brc_request_status` (`brc_request_status`),
            KEY `brc_pending_adt` (`brc_pending_adt`),
            KEY `brc_approved_adt` (`brc_approved_adt`),
            KEY `brc_received_adt` (`brc_received_adt`),
            KEY `brc_completed_adt` (`brc_completed_adt`),
            KEY `brc_denied_adt` (`brc_denied_adt`),
            KEY `brc_denied_reason` (`brc_denied_reason`),
            KEY `brc_request_adt` (`brc_request_adt`),
            FOREIGN KEY (`brc_bcat_id`) REFERENCES `wp_t_book_request_cycle` (`brc_id`) ON DELETE CASCADE ON UPDATE CASCADE,
            FOREIGN KEY (`brc_bcat_author_wpuid`) REFERENCES `wp_t_book_request_cycle` (`brc_id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ";

    }


    if ( !empty($sql) ) {

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta($sql);
        add_option("wnm_db_version", $wnm_db_version);
        
    }

}

register_activation_hook( __FILE__, 'wnm_install' );

if ( !class_exists( 'BookReview' ) ) {

    Class BookReview{

        public function __construct() {
           // add_action('init', array($this, 'pmpro_hasMembershipLevel'));
            add_action('admin_enqueue_scripts', array($this, 'wp_enqueue_for_admin_manage'));
            add_shortcode('manage_book_review', array( $this, 'manage_book_review_fn'));
            add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_for_front_manage'));
            add_action( 'admin_menu', array($this, 'book_layouts_menu'),100);
        }

        
        public function book_layouts_menu(){
            add_menu_page( __( 'Book Review', BR_TEXTDOMAIN), 'Book Review', 'manage_options','book-review', 'book_review_function','dashicons-book');
            function book_review_function(){
                include_once('includes/book-review-shortcode.php');
            }
        }

       
		/*function installer(){
			include('includes/installer.php');
		}*/
        /**
         * Front Layout
         */
        function manage_book_review_fn() {
            include_once('includes/book-review-front.php');
            
        }

        /**
         *@return style and script in admin site
         */
        public function wp_enqueue_for_admin_manage(){
            wp_enqueue_style( 'admin-custom-style', BR_DIR. '/admin/css/custom_style.css',array());
            wp_enqueue_script( 'admin-custom-js', BR_DIR. '/admin/js/custom_js.js',array());
            wp_enqueue_script( 'admin-scrip-online', 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js');
        }
        /**
         *@return style and script in front site
         */
        function wp_enqueue_for_front_manage() {
            wp_enqueue_style( 'front-custom-style', BR_DIR. '/front/css/custom_style.css',array());
            wp_enqueue_script( 'front-custom-js', BR_DIR. '/front/js/custom_js.js',array());
            wp_enqueue_script( 'front-scrip-online', 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js');
            wp_enqueue_style( 'front-boostrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
            wp_enqueue_style( 'front-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css');
            wp_enqueue_script( 'front-boostrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js');
			wp_enqueue_script( 'front-font-awesome-js', 'https://kit.fontawesome.com/538e211a74.js');
        }
    }
}

/*
 * Created new object of the Book Review.
 */
new BookReview();