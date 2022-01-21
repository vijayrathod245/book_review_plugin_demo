<?php
/**
 * Plugin Name:       Book Review
 * Plugin URI:        #
 * Description:       When an unknown printer took a galley of type and scrambled it to make a type specimen book.
 * Version:           0.0.1
 * Requires at least: 5.2
 * Author:            ODeveloper Thatsend
 * Author URI:        #
 * License:           GPL v2 or later
 * License URI:       #
 */

/**
 * define shorthand directory separator constant
 */
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

global $arrPhysicalPath, $arrVirtualPath;

$arrPhysicalPath 	= array();
$arrVirtualPath 	= array();

define('BR_TEXTDOMAIN', 'book-review');
define('BR_CSS_JS_V', 1);

define('TIER_ZERO_LIMIT', 1);
define('TIER_ONE_LIMIT', 5);
define('TIER_TWO_LIMIT', 15);
define('TIER_THREE_LIMIT', 99);
define('REVIEWER_REQ_LIMIT', 4);

define('STATUS_PENDING', 1);
define('STATUS_DENY', 2);
define('STATUS_APPROVE', 3);
define('STATUS_RECEIVED', 4);
define('STATUS_COMPLETED', 5);
define('STATUS_REJECTED', 6);

define('IS_PUBLISH', 1);
define('IS_UNPUBLISH', 2);




define('BR_DIR', plugin_dir_url( __FILE__ ));

$arrPhysicalPath['Base']			= dirname(__FILE__) . DS;
$arrVirtualPath['Base']			    = plugins_url('', __FILE__). '/';

$arrPhysicalPath['Install'] 		= $arrPhysicalPath['Base']. 'install' . DS;
$arrPhysicalPath['DBAccess'] 	    = $arrPhysicalPath['Base']. 'db_access' . DS;

$arrPhysicalPath['Libs'] 	        = $arrPhysicalPath['Base']. 'libs' . DS;
$arrVirtualPath['Libs'] 	        = $arrVirtualPath['Base']. 'libs' . '/';

$arrPhysicalPath['UploadBase'] 	    = $arrPhysicalPath['Base']. 'upload' . DS;
$arrVirtualPath['UploadBase'] 	    = $arrVirtualPath['Base']. 'upload' . '/';

include_once $arrPhysicalPath['Base']. 'BRStaticData.php';

/**
* Load install files
*/
include_once $arrPhysicalPath['Install']. 'BRInstaller.php';

global $wpdb;

# Runs when plugin is activated
register_activation_hook(__FILE__,array(BRInstaller::getInstance(), 'install'));

# Define some base path and call initialize depending on user
if( is_admin()){
    # Define required paths
    $arrPhysicalPath['UserBase'] 	= $arrPhysicalPath['Base']. 'admin' . DS;
    $arrVirtualPath['UserBase'] 	= $arrVirtualPath['Base']. 'admin' . '/';

} else {
    # Define required paths
    $arrPhysicalPath['UserBase'] 	= $arrPhysicalPath['Base']. 'front' . DS;
    $arrVirtualPath['UserBase'] 	= $arrVirtualPath['Base']. 'front' . '/';
}

# Define template paths
$arrPhysicalPath['TemplateBase']		= $arrPhysicalPath['UserBase']. 'templates' . DS;
$arrVirtualPath['TemplateBase'] 		= $arrVirtualPath['UserBase']. 'templates' . '/';

$arrVirtualPath['TemplateImages'] 	= $arrVirtualPath['TemplateBase']. 'images' . '/';
$arrVirtualPath['TemplateCss'] 		= $arrVirtualPath['TemplateBase']. 'css' . '/';
$arrVirtualPath['TemplateJs'] 		= $arrVirtualPath['TemplateBase']. 'js' . '/';



/*
 * TODO : Move this code under the child theme function.php file
 * */
function te_add_fields_to_signup(){

    //don't break if Register Helper is not loaded
    if(!function_exists( 'pmprorh_add_registration_field' )) {
        return false;
    }

    $fields = array();

    $fields[] = new PMProRH_Field(
        'reg_user_country',// input name, will also be used as meta key
        'select',	// type of field
        array(
            'options' =>
                BRStaticData::arrCountryData(),
            'label'		=> 'Country',// custom field label
            'size'		=> 30,// input size
            'profile'	=> true,// show in user profile
            'required'	=> true,// make this field required
            'value'     => 'US',
        )
    );

    //add the fields to default forms
    foreach($fields as $field){
        pmprorh_add_registration_field(
            'after_email',	// location on checkout page
            $field	// PMProRH_Field object
        );
    }
}

if( is_admin()) {
    # initialize admin area
    include_once $arrPhysicalPath['UserBase']. 'BRAdmin.php';
    BRAdmin::getInstance()->initialize();
} else {

    function EncryptDecrypt( $string, $action = 'e' ) {
        // you may change these values to your own
        $secret_key = 'ed_secret_key';
        $secret_iv = 'ed_secret_iv';
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash( 'sha256', $secret_key );
        $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
        if( $action == 'e' ) {
            $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
        }
        else if( $action == 'd' ){
            $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
        }
        return $output;
    }
    function removeSpecialCharcter($string) {
       //$string = str_replace('-', ' ', $string); // Replaces all spaces with hyphens.
    
       return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

    // Add custom field in registration form
    add_action( 'init', 'te_add_fields_to_signup' );

    //
    function user_last_seen() {
        if ( is_user_logged_in() ) {
            update_user_meta( get_current_user_id(), 'last_seen', time() );
        } else {
            return;
        }
    }
    add_action( 'wp_footer', 'user_last_seen');

    # initialize front area
    include_once $arrPhysicalPath['UserBase']. 'BRFront.php';
    BRFront::getInstance()->initialize();
}
