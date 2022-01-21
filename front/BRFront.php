<?php
include_once 'BRShortCode.php';

class BRFront {

    private static $instance ;

    private function __construct(){

    }

    public static function getInstance(){
        if( !isset(self::$instance)){
            self::$instance = new BRFront();
        }
        return self::$instance;
    }

    public function initialize() {

        global $arrVirtualPath, $arrPhysicalPath;

        # Incluse theme required common css
        wp_enqueue_script('jquery');
        wp_enqueue_style( 'front-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css');
        //wp_enqueue_style( 'bootstrap-select-css', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css');
        wp_enqueue_script( 'front-font-awesome-js', 'https://kit.fontawesome.com/538e211a74.js');

        //wp_enqueue_script( 'bootstrap-select-js', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js');

        # Selectize
        wp_enqueue_style( 'selectize-bt-css', 'https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/css/selectize.bootstrap4.min.css');
        wp_enqueue_style( 'selectize-css', 'https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/css/selectize.min.css');
        wp_enqueue_script( 'selectize-microplugin-js', 'https://cdnjs.cloudflare.com/ajax/libs/microplugin/0.0.3/microplugin.min.js', array('jquery'), BR_CSS_JS_V, true);
        wp_enqueue_script( 'selectize-js', 'https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/js/selectize.min.js', array('jquery'), BR_CSS_JS_V, true);
        wp_enqueue_script( 'selectize-standalone-js', 'https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/js/standalone/selectize.min.js', array('jquery'), BR_CSS_JS_V, true);

        //wp_enqueue_style( 'validate', 'https://jqueryvalidation.org/files/demo/site-demos.css');

        wp_enqueue_script( 'validate1-js', 'https://code.jquery.com/jquery-1.11.1.min.js');
        wp_enqueue_script( 'validate-js', 'https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js');
        wp_enqueue_script( 'validate1-js', 'https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js');

        wp_enqueue_script( 'jquery-maskedinput-js', $arrVirtualPath['Libs'].'/jQuery/maskedinput/jquery.maskedinput.js', array('jquery'), BR_CSS_JS_V, true);

       

        # Include Common CSS
        wp_enqueue_style('br-theme-style', $arrVirtualPath['TemplateCss']. 'br-common.css',array(),BR_CSS_JS_V);

        # Include Common Js
        wp_enqueue_script('br-common', 		        $arrVirtualPath['TemplateJs']. 'br-common.js', array('jquery'), BR_CSS_JS_V, true);

        # Register Shortcodes
        add_shortcode('manage_book_review', array(BRShortcode::getInstance(), 'booksListing') );


        /*wp_enqueue_script( 'front-scrip-online', 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js');
        wp_enqueue_style( 'front-boostrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
        wp_enqueue_style( 'front-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css');
        wp_enqueue_script( 'front-boostrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js');
        wp_enqueue_script( 'front-font-awesome-js', 'https://kit.fontawesome.com/538e211a74.js');*/
    }
}