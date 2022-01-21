<?php

if (!class_exists('BRInstaller')) {
    /**
     * Singleton implementation of BRInstaller
     */
    class BRInstaller
    {

        private static $instance;

        private function __construct()
        {
        }

        public static function getInstance()
        {
            if (!isset(self::$instance)) {
                self::$instance = new BRInstaller();
            }
            return self::$instance;
        }

        /**
         * Function installs the Book Review plugin
         * and initializes rewrite rules.
         */
        public function install()
        {

            global $arrPhysicalPath, $wpdb;

            # Create required tables
            $DBFile = $arrPhysicalPath['Install'] . "db_create.sql";

            $strFileContent = file_get_contents($DBFile);

            $arrQuery = explode(";", $strFileContent);

            $wpdb->show_errors();

            $tablePrefix = "wp_";

            foreach ($arrQuery as $key => $query) {
                if (!empty($query)) {
                    ## NOTE : REplace database prefix, with current prefix set
                    $query = str_replace($tablePrefix, $wpdb->prefix, $query);

                    $wpdb->query($query);
                }
            }
        }

        /**
         * Function removes Book Review plugin related information.
         */
        public function remove()
        {

            global $arrPhysicalPath, $wpdb;

        }
    }
}