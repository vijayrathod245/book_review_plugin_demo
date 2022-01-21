<?php
class BRAdmin {

    private static $instance ;

    private function __construct(){
        //
    }

    public static function getInstance(){
        if( !isset(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function initialize() {
        global $arrPhysicalPath, $arrVirtualPath;

        # Add menu
        add_action('admin_menu', array($this, 'createAdminMenu'));

        function no_admin_access() {
            $redirect =  home_url( '/' );
            global $current_user;
            $user_roles = $current_user->roles;
            $user_role = array_shift($user_roles);

            if($user_role === 'subscriber'){
                exit( wp_redirect( $redirect ) );
            }
        }
        add_action( 'admin_init', 'no_admin_access', 100 );
    }

    public function createAdminMenu(){

        global $arrOREVirtualPath, $arrOREConfig;

        $baseAdminUrl= admin_url('admin.php?page='.BR_TEXTDOMAIN);

        $main_menu_slug =  add_menu_page('Book Review', 'Book Review', 'manage_options',BR_TEXTDOMAIN, array($this, 'finalHandler'),'dashicons-book');

    }

    public function finalHandler() {

        global $arrPhysicalPath;

        include_once $arrPhysicalPath['TemplateBase']. 'admin-book-review.php';
    }
}
?>