<?php

class BRShortcode{
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

    public function booksListing($POST=false){
        global $arrPhysicalPath, $arrVirtualPath, $wp;

        include_once $arrPhysicalPath['TemplateBase']. 'BookListingTemplates.php';
        include_once $arrPhysicalPath['DBAccess']. 'BookMaster.php';
        include_once $arrPhysicalPath['DBAccess']. 'BookReviewRequest.php';

        $msg_success = ''; $msg_error = '';

        $objBM = BookMaster::getInstance();
        $objBRR = BookReviewRequest::getInstance();
        # Static HTML design
        //return BookListingTemplates::getDesignListingsHTML();

        # Check user is logged in
        if(is_user_logged_in()){
            global $current_user;
            
            
            /*echo date('Y-m-d H:i:s', $current_user->membership_level->enddate); 
            $ct = date('Y-m-d H:i:s');
            echo "<br/>". $ct;
            echo "<pre> MEMBER : "; print_r($current_user->membership_level->enddate); 
            
            //echo "<br/> CT". $ct;
            $ctime = strtotime($ct);

            echo "<br/> CURRENT : ". $ctime;

            var_dump(1615472053 > 1618342589);
            var_dump($ctime > $current_user->membership_level->enddate);*/
            //echo "<pre>"; print_r($current_user); die;
            
            //$a = apply_filters( 'pmpro_get_membership_level_for_user', $current_user->membership_level, $current_user );
            
            //echo "<pre>"; print_r($a); die;

            # Check user has subscribe membership plan
            if(isset($current_user->membership_level) && is_object($current_user->membership_level) && isset($current_user->membership_level->categories) && is_array($current_user->membership_level->categories)){
                
                # Get current date time timestamp
                $currentTimtStamp = strtotime(date('Y-m-d H:i:s')); 
                
                # Check membership is expired or not [Remain condition]
                if($current_user->membership_level->enddate == '' || ($current_user->membership_level->enddate != '' && $currentTimtStamp > $current_user->membership_level->enddate))
                    return '<div class="text-warning">Your membership has been expired. Please contact to administrative.</div>';

                $args = array(
                    'orderby'       => 'id',
                    'hide_empty'    => 0,
                );

                # When we add membership level, We need to select categories.
                # We able to assign multiple categories in a single membership level
                # So Get all categories to checking assigned level category is exist or not
                $arrAllCategories = get_categories($args);

                $arrCategoryIds = array();
                foreach ($arrAllCategories as $key => $val){
                    $arrCategoryIds[$val->term_id] = $val->name;
                }

                # User membership level has multiple categories
                # Get membership level category ids
                # Membership level category is our actual role such as "Authors" OR "Reviewers"
                # NOTE : This is the one category based logic if want multiple level then check "multiple categories based" commented code
                $userLevelCat = $current_user->membership_level->categories[0];

                # Get category name by id
                # It's return "Authors" OR "Reviewers" etc...
                $catName = get_the_category_by_ID($userLevelCat);

                # Check if assigned category is exist in all categories
                if(array_key_exists($userLevelCat, $arrCategoryIds) == true){
                    # Get logged in user country
                    $userCountry = get_user_meta($current_user->ID, "reg_user_country", true);

                    # Check assigned level
                    if($catName == 'Authors'){
                        //echo "<pre>"; print_r($_POST); die;
                        # Response processing code of add new book
                        if(isset($_POST['AddBK']) && ($_POST['AddBK'] == "ADD BOOK" || $_POST['AddBK'] == "UPDATE BOOK"))
                        {
                            unset($_POST['AddBK']);

                            $POST = $_POST;
                            $POST[$objBM->Data['F_F_Key']] = $current_user->ID;
                            //$POST[$objBM->Data['F_PrimaryField']] = 'IN';
                            
                            /*if(isset($POST[$objBM->Data['Field_Prefix'].'title'])){
                                $str = removeSpecialCharcter($POST[$objBM->Data['Field_Prefix'].'title']);
                                echo $str; die;
                            }*/

                            //echo "<pre>"; print_r($POST); die;
                            if(isset($POST['PK']) && $POST['PK'] != ''){
                                # Insert new book
                                $BKID = $objBM->Update($POST, $POST['PK']);
                                //echo $BKID; die;
                            }else{
                                unset($POST['PK']);
                                if($objBM->checkAsinIsNotExist($POST[$objBM->Data['Field_Prefix'].'asin'])){
                                    # Insert new book
                                    $BKID = $objBM->Insert($POST);
    
                                    $lastid = $objBM->DBC->insert_id;
                                    $POST['PK']  = $lastid;
                                }else{
                                    $current_q_var = $_SERVER['QUERY_STRING'].'&exist=1';
                                    $red_url = add_query_arg( $current_q_var, '', home_url( $wp->request ) );
                                    wp_redirect( $red_url );
                                    exit;
                                }
                            }



                            # Get tier from membership level name
                            # Get from last - position to end of string
                            # TODO: This is not a proper solution so take confirmation of client and change logic as per that
                            $getTier = trim(trim(substr($current_user->membership_level->name, strrpos($current_user->membership_level->name,'-'), strlen($current_user->membership_level->name)), '-'));

                            # Checking limit and update status of current book
                            $objBM->publishBook($POST, $getTier);


                            //$arrArgQury = $_SERVER['QUERY_STRING'];

                            # If successfully added book
                            # Redirect on same page with new query param save = 1
                            if(is_numeric($BKID)){
                                //$arrArgQury .= '&save=1';
                                $red_url = add_query_arg( array('save' => '1'), '', home_url( $wp->request ) );
                                wp_redirect( $red_url );
                                exit;
                            }else{
                                //$arrArgQury .= '&save=2';
                                $current_q_var = $_SERVER['QUERY_STRING'].'&e=1';
                                $red_url = add_query_arg( $current_q_var, '', home_url( $wp->request ) );
                                wp_redirect( $red_url );
                                exit;
                            }
                        }else if(isset($_POST['Import']) && $_POST['Import'] == "Import"){
                            unset($_POST['Import']);
                            
                            # Import csv file data
                            $objBM->importCSVData($current_user->ID);
                                
                        }

                        if(isset($_GET['m']) && $_GET['m'] == 'bk'){
                            if(isset($_GET['act']) && $_GET['act'] == 'bkdel' && isset($_GET['item']) && $_GET['item'] != '' && is_numeric($_GET['item'])){
                                # Delete book
                                $result = $objBM->Delete($_GET['item']);

                                # If successfully deleted book
                                # Redirect on same page with new query param save = 1
                                if($result != false){
                                    $red_url = add_query_arg( array('del' => '1'), '', home_url( $wp->request ) );
                                    wp_redirect( $red_url );
                                    exit;
                                }else{
                                    //$arrArgQury .= '&save=2';
                                    $red_url = add_query_arg( array('del' => '2'), '', home_url( $wp->request ) );
                                    wp_redirect( $red_url );
                                    exit;
                                }
                            }elseif(isset($_GET['act']) && $_GET['act'] == 'visible' && isset($_GET['item']) && $_GET['item'] != '' && is_numeric($_GET['item']) && isset($_GET['st']) && $_GET['st'] != '' && array_key_exists($_GET['st'], BRStaticData::arrYesNo()) == true){
                                $POST = array();
                                //$POST[$objBM->Data['F_VisibleField']] = $_GET['st'];
                                // Set default un visible
                                $POST[$objBM->Data['F_VisibleField']] = IS_UNPUBLISH;

                                $result = $objBM->updateByParam($POST, $_GET['item']);


                                $POST[$objBM->Data['F_VisibleField']] = $_GET['st'];
                                $POST[$objBM->Data['F_F_Key']] = $current_user->ID;
                                $POST['PK']  = $_GET['item'];

                                # Get tier from membership level name
                                # Get from last - position to end of string
                                # TODO: This is not a proper solution so take confirmation of client and change logic as per that
                                $getTier = trim(trim(substr($current_user->membership_level->name, strrpos($current_user->membership_level->name,'-'), strlen($current_user->membership_level->name)), '-'));

                                # Checking limit and update status of current book
                                $objBM->publishBook($POST, $getTier);

                                # If successfully updated book
                                # Redirect on same page with new query param st = 1
                                if($result != false){
                                    $red_url = add_query_arg( array('st' => $_GET['st']), '', home_url( $wp->request ) );
                                    wp_redirect( $red_url );
                                    exit;
                                }else{
                                    //$arrArgQury .= '&save=2';
                                    $red_url = add_query_arg( array('st' => '0'), '', home_url( $wp->request ) );
                                    wp_redirect( $red_url );
                                    exit;
                                }
                            }
                        }
                        if(isset($_GET['m']) && $_GET['m'] == 'br'){
                            if(isset($_GET['status']) && $_GET['status'] != ''){
                                if(isset($_GET['item']) && $_GET['item'] != ''){
                                    $POST = array();
                                    // echo '<pre>';print_r($_GET['status']);die;
                                    # Delete book
                                    if($_GET['status'] == STATUS_APPROVE){
                                        $POST[$objBRR->Data['Field_Prefix'].'request_status'] = $_GET['status'];
                                        $POST[$objBRR->Data['Field_Prefix'].'approved_adt'] = date('Y-m-d H:i:s');
                                    }
                                    elseif($_GET['status'] == STATUS_REJECTED){
                                        $POST[$objBRR->Data['Field_Prefix'].'request_status'] = $_GET['status'];
                                        $POST[$objBRR->Data['Field_Prefix'].'denied_adt'] = date('Y-m-d H:i:s');
                                    }
                                    elseif($_GET['status'] == STATUS_DENY){
                                        $POST[$objBRR->Data['Field_Prefix'].'request_status'] = $_GET['status'];
                                        $POST[$objBRR->Data['Field_Prefix'].'denied_adt'] = date('Y-m-d H:i:s');
                                    }
                                    elseif($_GET['status'] == STATUS_COMPLETED){
                                        $POST[$objBRR->Data['Field_Prefix'].'request_status'] = $_GET['status'];
                                        $POST[$objBRR->Data['Field_Prefix'].'completed_adt'] = date('Y-m-d H:i:s');
                                    }
                                    else{
                                        return false;
                                    }
                                    //echo '<pre>';print_r($POST);die;
                                    $result = $objBRR->updateByParam($POST, $_GET['item']);
                                    //echo '<pre>';print_r($objBRR->DBC);die;
                                    # If successfully deleted book
                                    # Redirect on same page with new query param save = 1
                                    if($result != false){
                                        $red_url = add_query_arg( array('updated' => $_GET['status']), '', home_url( $wp->request ) );
                                        wp_redirect( $red_url );
                                        exit;
                                    }else{
                                        //$arrArgQury .= '&save=2';
                                        $red_url = add_query_arg( array('updated' => '0'), '', home_url( $wp->request ) );
                                        wp_redirect( $red_url );
                                        exit;
                                    }
                                }
                            }

                        }

                        # Add new book
                        # TODO : Try to get query_var using default wordpress functions
                        if(isset($_GET['m']) && $_GET['m'] == 'bk'){
                            $arrSingleItem = array();
                            # Edit record
                            if(isset($_GET['act']) && $_GET['act'] == 'bkedit')
                            {
                                # Get info bi id - Get single book by id
                                $arrSingleItem = $objBM->getInfoByID($_GET['item']);

                                //echo "<pre>"; print_r($arrSingleItem); die;
                            }

                            if(isset($_GET['e']) && $_GET['e'] == 1)
                                $msg_error = '<div class="alert alert-danger">Something went wrong please try again. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>';
                            else if(isset($_GET['exist']) && $_GET['exist'] == 1)
                                $msg_error = '<div class="alert alert-danger">The book already exists in the catalog. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>';


                            return BookListingTemplates::getAutherAddBookHTML($arrSingleItem, $msg_error);
                        }

                        $param['author_id'] = $current_user->ID;

                        if(isset($_GET['save']) && $_GET['save'] == 1)
                            $msg_success = '<div class="alert alert-success">Book has been added successfully. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>';
                        else if(isset($_GET['save']) && $_GET['save'] == 2)
                            $msg_error = '<div class="alert alert-danger">Something went wrong please try again. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>';
                        else if(isset($_GET['del']) && $_GET['del'] == 1)
                            $msg_success = '<div class="alert alert-success">Book has been deleted successfully. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>';
                        else if(isset($_GET['save']) && $_GET['del'] == 2)
                            $msg_error = '<div class="alert alert-danger">Something went wrong please try again. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>';
                        else if(isset($_GET['updated']) && $_GET['updated'] == STATUS_APPROVE)
                            $msg_success = '<div class="alert alert-success">Book request has been approved successfully. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>';
                        else if(isset($_GET['updated']) && $_GET['updated'] == STATUS_DENY)
                            $msg_success = '<div class="alert alert-success">Book request has been rejected successfully. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>';
                        else if(isset($_GET['updated']) && $_GET['updated'] == 2)
                            $msg_error = '<div class="alert alert-danger">Something went wrong please try again. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>';
                        else if(isset($_GET['visible']) && $_GET['visible'] == IS_PUBLISH)
                            $msg_success = '<div class="alert alert-success">Book has been published successfully. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>';
                        else if(isset($_GET['visible']) && $_GET['visible'] == IS_UNPUBLISH)
                            $msg_success = '<div class="alert alert-success">Book has been unpublished successfully. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>';
                        else if(isset($_GET['visible']) && $_GET['visible'] == 0)
                            $msg_error = '<div class="alert alert-danger">Something went wrong please try again. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>';
                        /*else if(isset($_GET['status']) && $_GET['status'] == 2)
                            $msg_success = '<div class="alert alert-success">Book request has been rejected successfully. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>';
                        else if(isset($_GET['status']) && $_GET['status'] == 1)
                            $msg_error = '<div class="alert alert-danger">Something went wrong please try again. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>';
                        */
                        
                        if(isset($_GET['keyword']) && $_GET['keyword'] != '')
                            $param['book_title'] = $_GET['keyword'];
                            
                        if(isset($_GET['adt']) && $_GET['adt'] != '')
                            $param['adt'] = $_GET['adt'];
                        
                        if(isset($_GET['isactive']) && $_GET['isactive'] != '')
                            $param['visible'] = $_GET['isactive'];

                        # Get all listings
                        $arrResult = $objBM->getListingByParam($param);

                        $author_param['author_id'] = $current_user->ID;
                        
                        if(isset($_GET['restatus']) && $_GET['restatus'] != '')
                            $author_param['status'] = $_GET['restatus']; 
                            
                        $arrReqResult1 = $objBRR->getRequestByParam($author_param);
                       //echo '<pre>';print_r($arrReqResult1);die;

                        return BookListingTemplates::getAutherListingsHTML($arrResult, $arrReqResult1, $msg_success, $msg_error);
                    }
                    elseif($catName == 'Reviewers'){
                        //$objBRR = BookReviewRequest::getInstance();
                        //SubmitReview

                        # Response processing code
                        if(isset($_GET['req']) && $_GET['req'] == 1){
                            if(isset($_GET['item']) && $_GET['item'] != '' && strpos($_GET['item'],'-') != false){

                                $POST = array();
                                $arrIDs = explode('-', $_GET['item']);

                                $POST[$objBRR->Data['F_F_Key']]                         =   $arrIDs[0];
                                $POST[$objBRR->Data['F_PrimaryField']]                  =   $arrIDs[1];
                                $POST[$objBRR->Data['Field_Prefix'].'reviewer_wpuid']   =   $current_user->ID;
                                $POST[$objBRR->Data['Field_Prefix'].'request_adt']      =   date('Y-m-d H:i:s');
                                $POST[$objBRR->Data['Field_Prefix'].'request_status']   =   1; //arrStatus[1]
                                $POST[$objBRR->Data['Field_Prefix'].'pending_adt']      =   date('Y-m-d H:i:s');

                                $lreqID = $objBRR->Insert($POST);

                                //echo '<pre>';print_r($objBRR->DBC);die;
                                # If successfully added request
                                # Redirect on same page with new query param save = 1
                                if(is_numeric($lreqID)){
                                    $red_url = add_query_arg( array('req' => '1'), '', home_url( $wp->request ) );
                                    wp_redirect( $red_url );
                                    exit;
                                }else{
                                    //$arrArgQury .= '&save=2';
                                    $red_url = add_query_arg( array('req' => '2'), '', home_url( $wp->request ) );
                                    wp_redirect( $red_url );
                                    exit;
                                }
                            }

                        }
                        // This is the POST based review submit process
                        if(isset($_POST['SubmitReview']) && $_POST['SubmitReview'] == 'submit'){

                            if(isset($_POST['item']) && $_POST['item'] != '' && isset($_POST['review_link'])  && wp_http_validate_url($_POST['review_link']) != false && strpos($_POST['review_link'], "amazon") != false && strpos($_POST['review_link'], "/review") != false){

                                $POST = array();
                                // NOTE : First we are developed logic with update status as received. Now, we are skipped that status step and instead of that direct update status as COMPLETED.
                                /*$POST['brc_request_status'] = STATUS_RECEIVED;
                                $POST['brc_received_adt'] = date('Y-m-d H:i:s');*/
                                $POST[$objBRR->Data['Field_Prefix'].'request_status'] = STATUS_COMPLETED;
                                $POST[$objBRR->Data['Field_Prefix'].'completed_adt'] = date('Y-m-d H:i:s');
                                $POST[$objBRR->Data['Field_Prefix'].'review_url'] = $_POST['review_link'];


                                //echo '<pre>';print_r($POST);die;
                                $result = $objBRR->updateByParam($POST, EncryptDecrypt($_POST['item'], 'd'));
                                //echo '<pre>';print_r($objBRR->DBC);die;
                                # If successfully deleted book
                                # Redirect on same page with new query param save = 1
                                if($result != false){
                                    $red_url = add_query_arg( array('subr' => '1'), '', home_url( $wp->request ) );
                                    wp_redirect( $red_url );
                                    exit;
                                }else{
                                    //$arrArgQury .= '&save=2';
                                    $red_url = add_query_arg( array('subr' => '2'), '', home_url( $wp->request ) );
                                    wp_redirect( $red_url );
                                    exit;
                                }
                            }
                            else{
                                $red_url = add_query_arg( array('subr' => '2'), '', home_url( $wp->request ) );
                                wp_redirect( $red_url );
                                exit;
                            }
                        }
                        // This is for the GET based review submit process
                        if(isset($_GET['m']) && $_GET['m'] == 'br'){
                            if(isset($_GET['item']) && $_GET['item'] != ''){

                                $POST = array();
                                // NOTE : First we are developed logic with update status as received. Now, we are skipped that status step and instead of that direct update status as COMPLETED.
                                /*$POST['brc_request_status'] = STATUS_RECEIVED;
                                $POST['brc_received_adt'] = date('Y-m-d H:i:s');*/
                                $POST[$objBRR->Data['Field_Prefix'].'request_status'] = STATUS_COMPLETED;
                                $POST[$objBRR->Data['Field_Prefix'].'completed_adt'] = date('Y-m-d H:i:s');

                                //echo '<pre>';print_r($POST);die;
                                $result = $objBRR->updateByParam($POST, $_GET['item']);
                                //echo '<pre>';print_r($objBRR->DBC);die;
                                # If successfully deleted book
                                # Redirect on same page with new query param save = 1
                                if($result != false){
                                    $red_url = add_query_arg( array('subr' => '1'), '', home_url( $wp->request ) );
                                    wp_redirect( $red_url );
                                    exit;
                                }else{
                                    //$arrArgQury .= '&save=2';
                                    $red_url = add_query_arg( array('subr' => '2'), '', home_url( $wp->request ) );
                                    wp_redirect( $red_url );
                                    exit;
                                }
                            }
                        }

                        if(isset($_GET['req']) && $_GET['req'] == 1)
                            $msg_success = '<div class="alert alert-success">Request has been sent to author successfully. Waiting on approval. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>';
                        else if(isset($_GET['req']) && $_GET['req'] == 2)
                            $msg_error = '<div class="alert alert-danger">Something went wrong please try again. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>';
                        else if(isset($_GET['subr']) && $_GET['subr'] == 1)
                            $msg_success = '<div class="alert alert-success">Review has been successfully submitted. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>';
                        else if(isset($_GET['subr']) && $_GET['subr'] == 2)
                            $msg_error = '<div class="alert alert-danger">Something went wrong please try again. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>';

                        $param['country'] = $userCountry;
                        $param['visible'] = IS_PUBLISH;
                        
                        if(isset($_GET['keyword']) && $_GET['keyword'] != '')
                            $param['book_title'] = $_GET['keyword'];
                        
                        if(isset($_GET['adt']) && $_GET['adt'] != '')
                            $param['adt'] = $_GET['adt'];
                       
                       
                        

                        //$JOIN = ' LEFT JOIN '.$objBRR->Data['TableName'].' AS BR ON M.'.$objBM->Data['F_PrimaryKey'].' = BR.'.$objBRR->Data['F_F_Key'];

                        # Get all listings
                        $arrResult = $objBM->getListingByParam($param);
                        
                        if(isset($_GET['restatus']) && $_GET['restatus'] != '')
                            $re_param['status'] = $_GET['restatus']; 

                        $re_param['reviewer_id'] = $current_user;

                        # Get all request
                        $arrReqResult = $objBRR->getRequestByParam($re_param);
                       // echo '<pre>';print_r($arrReqResult);die;
                        return BookListingTemplates::getReviewerListingsHTML($arrResult, $arrReqResult, $msg_success, $msg_error);
                    }
                }
                return false;
            }
            return false;


            // multiple categories based
            // Commented code is multiple categories based - One membership levels have multiple categories
            // TODO : If want multiple level based logic then uncommented below logic
            /*
            if(isset($current_user->membership_level) && is_object($current_user->membership_level) && isset($current_user->membership_level->categories) && is_array($current_user->membership_level->categories)){
                $args = array(
                    'orderby'       => 'id',
                    'hide_empty'    => 0,
                );

                # When we add membership level, We need to select categories.
                # We able to assign multiple categories in a single membership level
                # So Get all categories to checking assigned level category is exist or not
                $arrAllCategories = get_categories($args);

                $arrCategoryIds = array();
                foreach ($arrAllCategories as $key => $val){
                    $arrCategoryIds[$val->term_id] = $val->name;
                }

                # User membership level has multiple categories
                # Get membership level category ids
                # Membership level category is our actual role such as "Authors" OR "Reviewers"
                $arrLevelCat = $current_user->membership_level->categories;

                foreach ($arrLevelCat as $ck => $cval){
                    //$arrLevelCategories[$cval] =  get_the_category_by_ID($cval);

                    # Get category name by id
                    # It's return "Authors" OR "Reviewers" etc...
                    $catName = get_the_category_by_ID($cval);

                    # Check if assigned category is exist in all categories
                    if(array_key_exists($cval, $arrCategoryIds) == true){
                        # Check assigned level
                        if($catName == 'Authors'){
                            # Add new book
                            if(isset($_GET['m']) && $_GET['m'] == 'bk' && isset($_GET['act']) && $_GET['act'] == 'bkadd'){
                                return BookListingTemplates::getAutherAddBookHTML();
                            }

                            return BookListingTemplates::getAutherListingsHTML();
                        }
                        elseif($catName == 'Reviewers'){
                            return BookListingTemplates::getReviewerListingsHTML();
                        }
                    }
                }
                return false;
            }
        */
        }
        return BookListingTemplates::getNotLoggedsinUserTemplate();

    }

}