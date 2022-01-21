<?php
class BookMaster{
    private static $instance ;
    public  $Data = array();
    public $DBC;
    private function __construct(){
        global $wpdb, $arrPhysicalPath, $arrVirtualPath;

        $this->DBC = $wpdb;
        //
        $this->Data['TableName']        =   $wpdb->prefix.'t_book_catalog';

        $this->Data['TableNameBookReq'] =   $wpdb->prefix.'t_book_request_cycle';
        $this->Data['Field_Book_Prefix']=   'brc_';
        /*$this->Data['B_PrimaryKey']   =   $this->Data['Field_Book_Prefix'].'brc_id';
        $this->Data['B_R_Key']          =   $this->Data['Field_Book_Prefix'].'brc_reviewer_wpuid';
        $this->Data['B_R_Adt']          =   $this->Data['Field_Book_Prefix'].'brc_request_adt';
        $this->Data['B_R_Status']       =   $this->Data['Field_Book_Prefix'].'brc_request_status';
        $this->Data['B_P_Adt']          =   $this->Data['Field_Book_Prefix'].'brc_pending_adt';
        $this->Data['B_A_Adt']          =   $this->Data['Field_Book_Prefix'].'brc_approved_adt';
        $this->Data['B_Re_Adt']         =   $this->Data['Field_Book_Prefix'].'brc_received_adt';
        $this->Data['B_C_Adt']          =   $this->Data['Field_Book_Prefix'].'brc_completed_adt';
        $this->Data['B_D_Adt']          =   $this->Data['Field_Book_Prefix'].'brc_denied_adt';
        $this->Data['B_DR_Key']         =   $this->Data['Field_Book_Prefix'].'brc_denied_reason';
        $this->Data['B_TT_Key']         =   $this->Data['Field_Book_Prefix'].'brc_turnaround_time';*/

        $this->Data['Field_Prefix']     =   'bcat_';
        $this->Data['F_PrimaryKey']     =   $this->Data['Field_Prefix'].'id';
        $this->Data['F_F_Key']          =   $this->Data['Field_Prefix'].'author_wpuid';
        $this->Data['F_PrimaryField']   =   $this->Data['Field_Prefix'].'country_code';
        $this->Data['F_VisibleField']   =   $this->Data['Field_Prefix'].'is_active';

        $this->Data['P_Upload']         =	$arrPhysicalPath['UploadBase']. 'books';
        $this->Data['V_Upload']         =   $arrVirtualPath['UploadBase'].'books';
        
        $this->Data['P_Upload_Import']  =	$this->Data['P_Upload']. '/import';
        $this->Data['V_Upload_Import']  =   $this->Data['V_Upload'].'/import';
        
        $this->Data['SampleFile']       =   'import-data.csv';
        
    }
    public static function getInstance(){
        if( !isset(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function Insert($POST){
        if(!is_array($POST) || (is_array($POST) && count($POST) <= 0))
            return false;

/*echo "<pre>"; print_r($_FILES);
echo "<pre>"; print_r($POST); die;*/

        if(is_array($_FILES) && count($_FILES) > 0){
            foreach ($_FILES as $fk => $fv){
                $new_filename = $this->file_upload_c_dir($fv);
                /*if($new_filename == false)
                    return false;*/

                $POST[$fk] = $new_filename;
            }
        }

        $POST[$this->Data['Field_Prefix'].'adt'] = date('Y-m-d H:i:s');
        # Store comma(,) separated  multiple countries
        if(is_array($POST[$this->Data['F_PrimaryField']]) && count($POST[$this->Data['F_PrimaryField']]) > 0)
            $POST[$this->Data['F_PrimaryField']] = implode(',', $POST[$this->Data['F_PrimaryField']]);

        # Set by default no in active field
        # It will update after checking the tier limit
        $POST[$this->Data['F_VisibleField']] = IS_PUBLISH;

        return $this->DBC->insert($this->Data['TableName'], $POST);
    }
    public function Update($POST, $PK){
        if(!isset($PK) || $PK == '' || !is_array($POST) || (is_array($POST) && count($POST) <= 0))
            return false;

        if(is_array($_FILES) && count($_FILES) > 0){
            foreach ($_FILES as $fk => $fv){

                if( $_FILES[$fk]['name'] != ''){
                    $new_filename = $this->file_upload_c_dir($fv);

                    if($new_filename == false){
                        return false;
                    }

                    $POST[$fk] = $new_filename;
                }

                # Delete previous file if uploaded new file
                if(isset($POST['delete_'.$fk]) && $POST['delete_'.$fk] == 1 && isset($POST['prev_'.$fk]) && $POST['prev_'.$fk] != '' && $_FILES[$fk]['name'] != ''){
                    $this->deletePreviusFile($POST['prev_'.$fk]);
                }

                if($_FILES[$fk]['name'] == '' &&  isset($POST['prev_'.$fk]) && $POST['prev_'.$fk] != ''){
                    $POST[$fk] =  $POST['prev_'.$fk];
                }

                unset($POST['prev_'.$fk], $POST['delete_'.$fk]);
            }
        }

        # Unset field
        unset($POST['PK']);

        //$this->publishBook($POST);
        # Set by default no in active field
        # It will update after checking the tier limit
        $POST[$this->Data['F_VisibleField']] = IS_PUBLISH;

        # Store comma(,) separated  multiple countries
        if(is_array($POST[$this->Data['F_PrimaryField']]) && count($POST[$this->Data['F_PrimaryField']]) > 0)
            $POST[$this->Data['F_PrimaryField']] = implode(',', $POST[$this->Data['F_PrimaryField']]);

        return $this->DBC->update($this->Data['TableName'], $POST,  array($this->Data['F_PrimaryKey'] => $PK));
    }
    public function importCSVData($POST, $authorID){
        echo "<pre>"; print_r($POST); 
        echo "<pre>"; print_r($_FILES); 
        die;
        
        //csv_file_upload_c_dir()
    }
    public function updateByParam($POST, $PK){
        return $this->DBC->update($this->Data['TableName'], $POST,  array($this->Data['F_PrimaryKey'] => $PK));

    }
    public function Delete($PK){
        if(!isset($PK) || $PK == '')
            return false;

        return $this->DBC->delete($this->Data['TableName'], array($this->Data['F_PrimaryKey'] => $PK));
    }
    public function getListingByParam($POST=false){
        $param = '';

        if(isset($POST['author_id']) && is_numeric($POST['author_id']))
            $param .= ' AND '.$this->Data['F_F_Key'].' = '.$POST['author_id'];

        if(isset($POST['country']) && $POST['country'] != '')
            $param .= ' AND FIND_IN_SET("'.$POST['country'].'", '.$this->Data['F_PrimaryField'].')';

        if(isset($POST['visible']) && $POST['visible'] != '')
            $param .= ' AND '.$this->Data['F_VisibleField'].' = '.$POST['visible'];
            
        if(isset($POST['book_title']) && $POST['book_title'] != '')
            $param .= ' AND '.$this->Data['Field_Prefix'].'title LIKE "%'.$POST['book_title'].'%"';
            
        if(isset($POST['adt']) && $POST['adt'] != '')
            $param .= ' AND '.$this->Data['Field_Prefix'].'adt LIKE "%'.$POST['adt'].'%"';

        //$param .= ' AND '.$this->Data['F_VisibleField'].' = 1';

        $sql = "SELECT * FROM ".$this->Data['TableName']." WHERE 1 ".$param. ' ORDER BY '.$this->Data['Field_Prefix'].'adt DESC';

        return $this->DBC->get_results($sql, ARRAY_A);
    }

    public function getInfoByID($id){
        # If id is not numeric then return false
        if(!isset($id) || (isset($id) && !is_numeric($id)))
            return false;

        $sql = "SELECT * FROM ".$this->Data['TableName']." WHERE 1 AND ".$this->Data['F_PrimaryKey']." = %d";

        return $this->DBC->get_row($this->DBC->prepare($sql,$id), ARRAY_A);
    }

    public function getVisibleCountByParam($POST=false){

        $param = '';

        if(isset($POST[$this->Data['F_F_Key']]) && is_numeric($POST[$this->Data['F_F_Key']]))
            $param .= ' AND '.$this->Data['F_F_Key'].' = '.$POST[$this->Data['F_F_Key']];

        $sql = "SELECT COUNT(*) as count  FROM ".$this->Data['TableName']." WHERE 1 AND ".$this->Data['F_VisibleField']." = 1 ".$param;

        $count = $this->DBC->get_row($sql, ARRAY_A);
        return $count['count'];
    }
    public function checkAsinIsNotExist($ASIN){
        if($ASIN == '')
            return false;
        
        $sql = "SELECT COUNT(*) as count  FROM ".$this->Data['TableName']." WHERE 1 AND ".$this->Data['Field_Prefix']."asin = ".$ASIN;
        
        $count = $this->DBC->get_row($sql, ARRAY_A);
        if($count['count'] > 0){
            return false;
        }
        
        return true;
    }
    public function planLimitExisted(){
        if(is_user_logged_in()){
            global $current_user;

            # Check user has subscribe membership plan
            if(isset($current_user->membership_level) && is_object($current_user->membership_level) && isset($current_user->membership_level->categories) && is_array($current_user->membership_level->categories)){
                # Get current date time timestamp
                $currentTimtStamp = strtotime(date('Y-m-d H:i:s'));

                # Check membership is expired or not [Remain condition]
                if($current_user->membership_level->enddate == '' || ($current_user->membership_level->enddate != '' && $currentTimtStamp > $current_user->membership_level->enddate))
                    return false;

                # User membership level has multiple categories
                # Get membership level category ids
                # Membership level category is our actual role such as "Authors" OR "Reviewers"
                # NOTE : This is the one category based logic if want multiple level then check "multiple categories based" commented code
                $userLevelCat = $current_user->membership_level->categories[0];

                # Get category name by id
                # It's return "Authors" OR "Reviewers" etc...
                $catName = get_the_category_by_ID($userLevelCat);

                # Check assigned level
                if($catName == 'Authors'){
                    $POST =array();
                    $POST[$this->Data['F_F_Key']] = $current_user->ID;
                    $count = $this->getVisibleCountByParam($POST);
                    $arrTier = BRStaticData::arrTierLimit();

                    # Get tier from membership level name
                    # Get from last - position to end of string
                    # TODO: This is not a proper solution so take confirmation of client and change logic as per that
                    $userTierLevel = trim(trim(substr($current_user->membership_level->name, strrpos($current_user->membership_level->name,'-'), strlen($current_user->membership_level->name)), '-'));

                    if(isset($arrTier[$userTierLevel]) && $count < $arrTier[$userTierLevel] ){
                        return false;
                    }
                    return true;
                }
            }
        }
    }
    public function publishBook($POST, $userTierLevel){
        if(isset($POST[$this->Data['F_F_Key']]) && is_numeric($POST[$this->Data['F_F_Key']]) && isset($POST['PK']) && is_numeric($POST['PK']) == true){
            $count = $this->getVisibleCountByParam($POST);
            $arrTier = BRStaticData::arrTierLimit();

            //echo  "<pre>"; print_r($arrTier[$userTierLevel]); die;
            //var_dump( $count < $arrTier[$userTierLevel]);
            //echo $count; die;
            if(isset($arrTier[$userTierLevel]) && $count < $arrTier[$userTierLevel] ){
                $p_data = array(
                    $this->Data['F_VisibleField'] => isset($POST[$this->Data['F_VisibleField']])?$POST[$this->Data['F_VisibleField']]:2,
                );

                return $this->updateByParam($p_data, $POST['PK']);
            }
        }
        return false;
    }
    public function insertRequest($POST){
        if(!is_array($POST) || (is_array($POST) && count($POST) <= 0))
            return false;

        $POST[$this->Data['Field_Book_Prefix'].'adt'] = date('Y-m-d H:i:s');
        //echo '<pre>';print_r($POST);
        //if(isset($POST['brc_id']) && is_numeric($POST['brc_reviewer_wpuid']))
        return $this->DBC->insert($this->Data['TableNameBookReq'], $POST);
    }



    public function file_upload_c_dir($file) {
        if ( isset($file) ) {
            if ( ! empty( $this->Data['P_Upload'] ) ) {
                if ( ! file_exists( $this->Data['P_Upload'] ) ) {
                    wp_mkdir_p( $this->Data['P_Upload'] );
                }

                # Check valid file type
                if(isset($file['type']) && in_array($file['type'], BRStaticData::arrValidFileFormat())){
                    if($file['name'] != ''){
                        $filename = wp_unique_filename( $this->Data['P_Upload'],$file['name'] );
                        move_uploaded_file($file['tmp_name'], $this->Data['P_Upload'] .'/'. $filename);
                        return $filename;
                    }
                }
                return false;
            }
        }
    }
    public function csv_file_upload_c_dir($file) {
        if ( isset($file) ) {
            if ( ! empty( $this->Data['P_Upload_Import'] ) ) {
                if ( ! file_exists( $this->Data['P_Upload_Import'] ) ) {
                    wp_mkdir_p( $this->Data['P_Upload_Import'] );
                }

                # Check valid file type
                if(isset($file['type']) && in_array($file['type'], BRStaticData::arrValidImportFileFormat())){
                    if($file['name'] != ''){
                        $filename = wp_unique_filename( $this->Data['P_Upload_Import'],$file['name'] );
                        move_uploaded_file($file['tmp_name'], $this->Data['P_Upload_Import'] .'/'. $filename);
                        return $filename;
                    }
                }
                return false;
            }
        }
    }
    public function deletePreviusFile($filename) {
        if ( isset($filename) && $filename != '' ) {
            $actualFile = $this->Data['P_Upload'].'/'.$filename;
            if (file_exists($actualFile)) {
               unlink($actualFile);
            }
        }
    }
}