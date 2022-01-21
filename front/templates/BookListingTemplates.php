<?php
include_once $arrPhysicalPath['DBAccess']. 'BookMaster.php';
//include_once $arrPhysicalPath['DBAccess']. 'BookReviewRequest.php';
class BookListingTemplates{
    public static function getAutherListingsHTML($arrData, $arrReqList,$msg_success='',$msg_error=''){
        global $wp, $arrPhysicalPath,$current_user;

        $addBookLink = home_url( add_query_arg( array('m' => 'bk', 'act' => 'bkadd'), $wp->request ) );
        $upgradePlanLink = home_url( '/membership-account/');

        $objBM = BookMaster::getInstance();
        include_once $arrPhysicalPath['DBAccess']. 'BookReviewRequest.php';
        $objBRR = BookReviewRequest::getInstance();


        $authorHTML = '';
        $arrReqBookIds = array();
        $autherReq = '';
        $autherReqList = '';
        //echo '<pre>';print_r($arrAuthorResult);die;
        if(is_array($arrReqList) && count($arrReqList) > 0){
            foreach ($arrReqList as $ak => $arrAuthorResult){
                //echo "<pre>"; print_r($arrAuthorResult); die;

                # Get reviewer data by id
                $rev_data = get_userdata($arrAuthorResult[$objBRR->Data['Field_Prefix'].'reviewer_wpuid']);

                # Get member since
                $member_since = isset($rev_data->user_registered)?human_time_diff(strtotime($rev_data->user_registered)):'-';

                # Last seen
                $last_seen = get_the_author_meta('last_seen', $arrAuthorResult[$objBRR->Data['Field_Prefix'].'reviewer_wpuid']);
                $last_seen_on = ($last_seen)?human_time_diff($last_seen):'-';

                $aproveLink = home_url( add_query_arg( array('m' => 'br', 'status' => STATUS_APPROVE, 'item' => $arrAuthorResult[$objBRR->Data['F_PrimaryKey']] ),  $wp->request ) );
                //$rejectLink = home_url( add_query_arg( array('m' => 'br', 'status' => '2', 'item' => $arrAuthorResult[$objBRR->Data['F_PrimaryKey']] ), $wp->request ) );
                $rejectLink = home_url( add_query_arg( array('m' => 'br', 'status' => STATUS_REJECTED, 'item' => $arrAuthorResult[$objBRR->Data['F_PrimaryKey']] ), $wp->request ) );
                $complete = home_url( add_query_arg( array('m' => 'br', 'status' => STATUS_COMPLETED, 'item' => $arrAuthorResult[$objBRR->Data['F_PrimaryKey']] ), $wp->request ) );
                
                $completeDate = '-'; 
                if($arrAuthorResult['brc_completed_adt'] != ''){
                    $c_date = date_create($arrAuthorResult[$objBRR->Data['Field_Prefix'].'completed_adt']);
                    $completeDate = date_format($c_date, "d M Y H:i A");
                }
                //echo '<pre>';print_r($rejectLink);die;
                $actionDIV  = '';
                if($arrAuthorResult['brc_request_status'] == STATUS_PENDING){
                    /*$actionDIV  = '<div class="dropdown te-dropdown">
                                    <button class="btn btn-dark dropdown-toggle te-dropdown border-0" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Action
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="'.$aproveLink.'">Approve</a>
                                        <a class="dropdown-item" href="'.$rejectLink.'">Reject</a>
                                    </div>
                                </div>';*/
                    $actionDIV  = '<div class="row">
                                        <div class="col-12 col-md-6 form-group"><a class="btn btn-success btn-block" href="'.$aproveLink.'" data-toggle="tooltip" data-placement="top" title="Member Since: '.$member_since.', Last Seen On: '.$last_seen_on.'">Approve</a></div>
                                        <div class="col-12 col-md-6 form-group"><a class="btn btn-danger btn-block" href="'.$rejectLink.'"  data-toggle="tooltip" data-placement="top" title="Member Since: '.$member_since.', Last Seen On: '.$last_seen_on.'">Reject</a></div>
                                    </div>';
                }
                elseif($arrAuthorResult['brc_request_status'] == STATUS_APPROVE){
                    $actionDIV  = 'Waiting for review submission';
                }
                
                elseif($arrAuthorResult['brc_request_status'] == STATUS_RECEIVED){
                    $actionDIV  = '<a href="'.$complete.'" class="btn btn-dark">Complete</a>';
                }

                elseif($arrAuthorResult['brc_request_status'] == STATUS_COMPLETED){
                    //echo '<pre>';print_r($ritem);die;
                    $completed_date = $arrAuthorResult['brc_completed_adt'];
                    $completed_date = new DateTime($completed_date);
                    
                    $start_date = $arrAuthorResult['brc_request_adt'];
                    $start_date = new DateTime($start_date);
                
                    $interval = $start_date->diff($completed_date);
                    $day = $interval->format('%r%a');
                    //$actionDIV  = '<div class="text-success">Finished </div><div class="text-info"> Completed ('.$day.' days)</div>';
                    $actionDIV  = '<div class="text-info"> Completed ('.$day.' days)</div>';
                }
                // For status colomn
                if($arrAuthorResult[$objBRR->Data['Field_Prefix'].'request_status'] == STATUS_COMPLETED){
                    $reqStatus = '<a href="'.$arrAuthorResult[$objBRR->Data['Field_Prefix'].'review_url'].'" class="text-decoration-none " target="_blank">'.BRStaticData::arrReqStatusAuth()[$arrAuthorResult[$objBRR->Data['Field_Prefix'].'request_status']].' <i class="fas fa-external-link-alt"></i></a>';
                }
                else{
                    $reqStatus = BRStaticData::arrReqStatusAuth()[$arrAuthorResult[$objBRR->Data['Field_Prefix'].'request_status']];
                }

                $oldauthorHTML = '<tr>
                                    <td class="text-center"><div class="row"><div class="col-12 mb-2 text-center"><img class="img-thumbnail" src="'.$objBM->Data['V_Upload'].'/'.$arrAuthorResult['bcat_cover_image'].'" width="100px" height="100px"></div></div>'.$actionDIV.'</td>
                                    <td class="text-center">'.$arrAuthorResult['bcat_title'].'</td>
                                    <td class="text-center">'.$member_since.'</td>
                                    <td class="text-center">'.$last_seen_on.'</td>
                                    <td class="text-center">'.$completeDate.'</td>
                                    <td class="text-center">'
                                        .$actionDIV.
                                    '</td>
                                </tr>';
                $authorHTML .= '<tr>
                                    <td class="text-center"><div class="row"><div class="col-12 mb-2 text-center"><img class="img-thumbnail" src="'.$objBM->Data['V_Upload'].'/'.$arrAuthorResult['bcat_cover_image'].'" ></div></div>'.$actionDIV.'</td>
                                    <td class="text-center">'.$arrAuthorResult['bcat_title'].'</td>
                                    <td class="text-center">'.$reqStatus.'</td>
                                    <td class="text-center">'.$completeDate.'</td>
                                </tr>';
            }
            
            $autherReq = '  <div class="row">
                                <div class="col-xl-12 table-responsive px-0">
                                    <table class="table te-table table-hover">
                                        <thead>
                                        <tr>
                                            <th scope="col" width="25%" class="text-center">Image</th>
                                            <th scope="col" width="25%"  class="text-center">Book Title</th>
                                            <th scope="col" width="25%"  class="text-center">Review Status</th>
                                            <th scope="col" width="25%"  class="text-center">Review Completion Date</th>
                                        </tr>
                                       </thead>
                                        <tbody>
                                        '. $authorHTML .'
                                        </tbody>
                                    </table>
                                </div>
                            </div>';
           
        }else if(isset($_GET['restatus'])){
            $autherReq = '<div class="row">
                             <div class="col-12">
                                <h3>Review Progress</h3>
                                <div class="alert alert-secondary">No request found. Please change your search criteria and try again.</div>     
                            </div>
                        </div>';
        }
        else{
            $autherReq = '<div class="row">
                             <div class="col-12">
                                <h3>Review Progress</h3>
                                <div class="alert alert-secondary">There is no book request found for you.</div>     
                            </div>
                        </div>';
        }
        
        $arrRequestAuthStatus = BRStaticData::arrReqStatusAuth();
        
        $htmlAuthRequestStatus = '';
        if(is_array($arrRequestAuthStatus) && count($arrRequestAuthStatus) > 0){
            foreach ($arrRequestAuthStatus as $brsk => $brsv){
                $selected_st = '';

                if(isset($_GET['restatus']) && $_GET['restatus'] == strval($brsk))
                    $selected_st = 'selected="selected"';

                $htmlAuthRequestStatus .= '<option value="'.$brsk.'" '.$selected_st.' >'.$brsv.'</option>';
            }
        }
        
        $autherReqList = '<section class="py-5">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <h1 class="test te-front-side">Review Progress</h1>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <form class="form-inline-" method="get">
                                            <div class="row justify-content-md-end">
                                                <div class="col-12 col-md-4">
                                                    <div class="input-group form-group">
                                                        <select name="restatus"  class="custom-select te-form-select">
                                                            <option selected>--- Status ---</option>
                                                            '.$htmlAuthRequestStatus.'
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-2">
                                                    <button type="submit" class="btn btn-dark">Search &nbsp;<i class="fas fa-search" aria-hidden="true"></i></button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                '.$autherReq.'
                            </div>
                        </section>';
                                    
        
        
        $itemHTML = '';
        if(is_array($arrData) && count($arrData) > 0){
            foreach ($arrData as $dk => $item){

                //echo '<pre>';print_r($item);die;
                $editBookLink = home_url( add_query_arg( array('m' => 'bk', 'act' => 'bkedit', 'item' => $item[$objBM->Data['F_PrimaryKey']]), $wp->request ) );
                $deleteBookLink = home_url( add_query_arg( array('m' => 'bk', 'act' => 'bkdel', 'item' => $item[$objBM->Data['F_PrimaryKey']]), $wp->request ) );
                $viewBookLink = home_url( add_query_arg( array('m' => 'bk', 'act' => 'bkview', 'item' => $item[$objBM->Data['F_PrimaryKey']]), $wp->request ) );

                $visibleHtml = '';
                $statusBadge = '';
                if($item[$objBM->Data['F_VisibleField']] == IS_PUBLISH){
                    $visibleBookLink = home_url( add_query_arg( array('m' => 'bk', 'act' => 'visible', 'item' => $item[$objBM->Data['F_PrimaryKey']], 'st' => IS_UNPUBLISH), $wp->request ) );
                    $visibleHtml = '<a class="btn btn-danger  btn-block text-uppercase" href="'.$visibleBookLink.'" role="button">UNPUBLISH</a>';
                    $statusBadge = '<span class="badge text-uppercase badge-success bk-status">PUBLISHED</span>';
                }
                else{
                    $visibleBookLink = home_url( add_query_arg( array('m' => 'bk', 'act' => 'visible', 'item' => $item[$objBM->Data['F_PrimaryKey']], 'st' => IS_PUBLISH), $wp->request ) );
                    $visibleHtml = '<a class="btn btn-success  btn-block text-uppercase" href="'.$visibleBookLink.'" role="button">PUBLISH</a>';
                    $statusBadge = '<span class="badge text-uppercase badge-danger bk-status">UNPUBLISHED</span>';
                }


                //$Link = "confirmDelete('".$deleteBookLink."')";
                $itemHTML .= '<div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 te-product-box padding-info">
                                <div class="card border-0 te-shadow">
                                    <a href="'.$editBookLink.'" class="text-decoration-none">
                                        <img src="'.$objBM->Data['V_Upload'].'/'.$item['bcat_cover_image'].'" class="card-img-top" alt="'.$item['bcat_title'].'">
                                        '.$statusBadge.'
                                    </a>
                                    <div class="card-body px-0 pt-2 pb-1 bg-white position-relative">
                                        <h4 class="card-title te-card-title mb-0 te-font-size-16 font-weight-bold mb-1">
                                            <a href="'.$item['bcat_amazon_permalink'].'" class="text-decoration-none" target="_blank" >'.$item['bcat_title'].'</a>
                                        </h4>
                                        <div class="row">
                                            <div class="col-6">
                                                <a class="btn btn-dark  btn-block text-uppercase" href="'.$editBookLink.'" role="button">Edit</a>
                                            </div>
                                            <div class="col-6">
                                                <a class="btn btn-danger  btn-block text-uppercase del" href="javascript::void(0);" role="button" data-url="'.$deleteBookLink.'">Delete</a>
                                            </div>
                                            <div class="col-12 mt-1">
                                                '.$visibleHtml.'
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>';
            }
        }elseif(isset($_GET['keyword'])){
            $itemHTML = '   
                             <div class="col-12">
                                <div class="text text-warning text-center h3 my-5">No data found. Please change your search criteria and try again.</div>     
                            </div> 
                        ';
        }
        else{
            $itemHTML = '
                            <div class="container">
                                <div class="row">
                                     <div class="col-12">
                                     <h3>My Books</h3>
                                        <div class="alert alert-secondary">There is no book found added by you.</div>     
                                    </div>  
                                </div>
                            </div>';
        }
        $upgradePlanHtml = '';
        if($objBM->planLimitExisted() == true){
            // Get all the page settings.
            $pmpro_page_ids = pmpro_get_pmpro_pages();

            if(isset($pmpro_page_ids['account']))
                $upgradePlanLink = get_page_link($pmpro_page_ids['account']);

            $upgradePlanHtml = '<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 my-md-4">
                                <div class="alert alert-warning">You have reached the plan limit so please upgrade the plan to increase publishing limit of book. <a class="text-decoration-none" role="button" href="'.$upgradePlanLink.'">Upgrade Now</a></div>
                            </div>';
        }
        
        $arrActiveOpt = BRStaticData::arrYesNo();
        
        $htmlOptionIsActive = '';
        if(is_array($arrActiveOpt) && count($arrActiveOpt) > 0){
            foreach ($arrActiveOpt as $bak => $bav){
                $selected_a = '';

                if(isset($_GET['isactive']) && $_GET['isactive'] == strval($bak))
                    $selected_a = 'selected="selected"';
                /*else if(!isset($_GET['isactive']) && strval($bak) == IS_UNPUBLISH)
                    $selected_a = 'selected="selected"';*/

                $htmlOptionActive .= '<option value="'.$bak.'" '.$selected_a.' >'.$bav.'</option>';
            }
        }

        $html = '<section class="">
                    <div class="container-fluid main-info">
                        <div class="row">
                            '.$upgradePlanHtml.'
                            <div class="col-12">
                                <form class="form-inline-" method="get">
                                    <div class="row justify-content-md-end">
                                        <div class="col-12 col-md-4">
                                            <div class="input-group form-group">
                                                <input type="text" class="form-control" name="keyword" id="book_title" value="'.(isset($_GET['keyword'])?$_GET['keyword']:'').'" placeholder="Search books">
                                                <div class="input-group-prepend">
                                                    <button type="submit" class="btn btn-dark"><i class="fas fa-search" aria-hidden="true"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <div class="input-group form-group">
                                                <select name="isactive"  class="custom-select te-form-select">
                                                    <option selected>--- Filter Catalog By ---</option>
                                                    '.$htmlOptionActive.'
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <div class="input-group form-group">
                                                <input type="date" class="form-control" name="adt" id="adt" value="'.(isset($_GET['adt'])?$_GET['adt']:'').'" placeholder="Added date">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-2">
                                            <button type="submit" class="btn btn-dark">Search &nbsp;<i class="fas fa-search" aria-hidden="true"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 my-md-4">
                                <a class="btn btn-dark py-3 px-5" role="button" href="'.$addBookLink.'">ADD BOOK</a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                '.$msg_success.'
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <h3>My Books</h3>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                '.$msg_error.'
                            </div>
                        </div>
                        <div class="row">
                            '.$itemHTML.'
                        </div>
                    </div>
                </section>';
        
        $importActionLink = home_url( add_query_arg( array('m' => 'bk', 'act' => 'bkimport'), $wp->request ) );
                
        $importHtml = '<hr/>
                        <section class="">
                            <div class="container-fluid main-info">
                                <div class="row justify-content-between align-items-center">
                                    <div class="col-12 col-md-6 form-group">
                                      <a class="btn btn-success" data-toggle="collapse" href="#importCollaps" role="button" aria-expanded="false" aria-controls="importCollaps">
                                            Import CSV
                                      </a>
                                    </div>
                                    <div class="col-12 col-md-6 text-right form-group">
                                        <a class="text-decoration-none" href="'.$objBM->Data['V_Upload_Import'].'/'.$objBM->Data['SampleFile'].'" role="button" download>Download</a> blank CSV file to import data.
                                    </div>
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <div class="collapse" id="importCollaps">
                                          <div class="card card-body">
                                                <form class="needs-validation" method="post" enctype="multipart/form-data">
                                                    <div class="row justify-content-center">
                                                        <div class="col-12 col-md-auto">
                                                            <div class="custom-file te-custom-file">
                                                                <input type="file" name="import_file" class="custom-file-input h-100" id="import_file" accept=".csv" required>
                                                                <div class="invalid-feedback">Please choose valid csv.</div>
                                                                <label class="custom-file-label h-100" for="cover_image">Choose CSV file</label>
                                                                <small id="import_file" class="form-text text-muted">Valid File Format : '.BRStaticData::valid_doc_import_format().'</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-auto">
                                                            <button type="submit" class="btn btn-dark" name="Import" value="Import">Import &nbsp;<i class="fas fa-file-import"></i></button>
                                                        </div>
                                                    </div>
                                                </form>
                                          </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </section>';

        $html .= $autherReqList.$importHtml;
        return $html;
    }
    public static function getReviewerListingsHTML($arrData, $arrReqResult=false, $msg_success='', $msg_error=''){
        global $wp, $arrPhysicalPath;
        include_once $arrPhysicalPath['DBAccess']. 'BookMaster.php';
        $objBM = BookMaster::getInstance();

        include_once $arrPhysicalPath['DBAccess']. 'BookReviewRequest.php';
        $objBRR = BookReviewRequest::getInstance();
        
        $reqHTML = '';
        $reviwerReq = '';
        $arrReqBookIds = array();
        $arrLimitCount = array();
        if(is_array($arrReqResult) && count($arrReqResult) > 0){
            foreach ($arrReqResult as $rk => $ritem){
                //$submitreviw = home_url( add_query_arg( array('m' => 'br', 'act' => 'subr', 'item' => $ritem[$objBRR->Data['F_PrimaryKey']]), $wp->request ) );
                $submitreviw = home_url( $wp->request );
               
                //echo '<pre>';print_r($ritem);die;
                # Set key as book id and value as author id
                //$arrReqBookIds[$ritem[$objBRR->Data['F_F_Key']]] = $ritem[$objBRR->Data['F_PrimaryField']];
                $arrReqBookIds[$ritem[$objBRR->Data['F_F_Key']]] = $ritem;
                
                if($ritem['brc_request_status'] != STATUS_APPROVE && $ritem['brc_request_status'] != STATUS_COMPLETED)
                    $arrLimitCount[$ritem[$objBRR->Data['F_F_Key']]] = $ritem[$objBRR->Data['F_F_Key']];

                $downloadDIV = ' - ';
                $actionDiv = ' - ';
                if($ritem['brc_request_status'] == STATUS_APPROVE){
                    $expiry_date = $ritem['brc_approved_adt'];
                    $expiry_date = new DateTime($expiry_date);
                    $today = new DateTime();
                    
                    $interval = $expiry_date->diff($today);
                    $day = $interval->format('%r%a');
                    
                    if($day < 10) {
                        $downloadDIV  = '   
                                            <form action="'.$submitreviw.'" class="needs-validation" method="post">
                                                <div class="form-group">
                                                    <input name="review_link" type="text" class="form-control valid-url" placeholder="https://amazon.com" required>
                                                    <div class="invalid-feedback ">Please enter valid url.</div>
                                                    <small id="InputReviewPermalinkHelp" class="form-text text-muted">Eg: http://xyz.com, http://www.xyz.com, https://xyz.com etc.. </small>
                                                </div>
                                                <div class="text-center form-group">
                                                    <button type="submit" name="SubmitReview" class="btn btn-dark" value="submit">Submit Review</a>
                                                    <input type="hidden" name="item" value="'.EncryptDecrypt($ritem[$objBRR->Data['F_PrimaryKey']], 'e').'">
                                                </div>    
                                           </form>
                                            <div class="text-center">
                                                <a href="'.$objBM->Data['V_Upload'].'/'.$ritem['bcat_sample_book_file'].'" class="" download>Download Sample</a>
                                            </div>  
                                        ';
                    }
                    else{
                        $downloadDIV = '<div class="text-center text-danger">Abandoned</div>';
                    }
                }
                elseif($ritem['brc_request_status'] == STATUS_RECEIVED){
                    $downloadDIV  = '<div class="text-warning text-center">Review Submitted</div>';
                }
                elseif($ritem['brc_request_status'] == STATUS_COMPLETED){
                    $downloadDIV  = '<div class="text-center"><a href="'.$objBM->Data['V_Upload'].'/'.$ritem['bcat_full_length_book_file'].'" download>Download Full Book</a></div>';
                }
               /* else{
                    $downloadDIV =  BRStaticData::arrReqStatus($ritem['brc_request_status']);
                }*/

                $reqHTML .= '<tr>
                                <td class="text-center"><a href="'.$ritem['bcat_amazon_permalink'].'" class="text-decoration-none te-link-to-amazon" target="_blank"><img class="w-50- img-thumbnail" src="'.$objBM->Data['V_Upload'].'/'.$ritem['bcat_cover_image'].'"></a></td>
                                <td class="text-center">'.$ritem['bcat_title'].'</td>
                                <td class="text-center">'.BRStaticData::arrReqStatusReviewer()[$ritem[$objBRR->Data['Field_Prefix'].'request_status']].'</td>
                                <td class="text-center">'.$downloadDIV.'</td>
                            </tr>';
            }
            
            
            
            $reviwerReq = ' 
        
                                <div class="row">
                                    <div class="col-xl-12 table-responsive px-0">
                                        <table class="table te-table table-hover">
                                            <thead>
                                                <tr>
                                                    <th scope="col" width="25%" class="text-center">Image</th>
                                                    <th scope="col" width="25%" class="text-center">Book Title</th>
                                                    <th scope="col" width="25%" class="text-center">Review Status</th>
                                                    <th scope="col" width="25%" class="text-center">Book Download</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            
                                                
                                            '.$reqHTML.'
        
                                            </tbody>
                                        </table>
                                    </div>
                                </div>';
        }else if(isset($_GET['restatus'])){
            $reviwerReq = '
                                <div class="row">
                                     <div class="col-12">
                                        <h3>Review Progress</h3>
                                        <div class="alert alert-secondary">No request found. Please change your search criteria and try again.</div>     
                                    </div>
                                </div>
                            ';
        }else{
            $reviwerReq = '
                                    <div class="row">
                                        <div class="col-12">
                                            <h3>Review Progress</h3>
                                            <div class="alert alert-secondary">You haven\'t requested any book.</div>     
                                        </div>
                                    </div>';
        }
        
        $arrRequestStatus = BRStaticData::arrReqStatusReviewer();
        $htmlRequestStatus = '';
        if(is_array($arrRequestStatus) && count($arrRequestStatus) > 0){
            foreach ($arrRequestStatus as $brrk => $brrv){
                $selected_st = '';

                if(isset($_GET['restatus']) && $_GET['restatus'] == strval($brrk))
                    $selected_st = 'selected="selected"';
                else if(!isset($_GET['restatus']) && strval($brrk) == STATUS_PENDING)
                    $selected_st = 'selected="selected"';

                $htmlRequestStatus .= '<option value="'.$brrk.'" '.$selected_st.' >'.$brrv.'</option>';
            }
        }
        
        
        $reviwerReq = '<section class="py-5">
                                <div class="container-fluid">
                                <div class="row">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <h1 class="test te-front-side">Review Progress</h1>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <form class="form-inline-" method="get">
                                            <div class="row justify-content-md-end">
                                                <div class="col-12 col-md-4">
                                                    <div class="input-group form-group">
                                                        <select name="restatus"  class="custom-select te-form-select">
                                                            <option selected>--- Status ---</option>
                                                            '.$htmlRequestStatus.'
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-2">
                                                    <button type="submit" class="btn btn-dark">Search <i class="fas fa-search" aria-hidden="true"></i></button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                '.$reviwerReq.'
                            </div>
                        </section>';
        

        $itemHTML = '';
        if(is_array($arrData) && count($arrData) > 0){
            foreach ($arrData as $dk => $item){
                $requestReview = home_url( add_query_arg( array('m' => 'bk', 'req' => '1', 'item' => $item[$objBM->Data['F_PrimaryKey']] .'-'. $item[$objBM->Data['F_F_Key']]), $wp->request ) );

                $objBM = BookMaster::getInstance();
                $requestReviewBtn = '';
                if(!isset($arrReqBookIds[$item[$objBM->Data['F_PrimaryKey']]])){
                    # Disbale button
                    if(count($arrLimitCount) >= REVIEWER_REQ_LIMIT)
                        $requestReviewBtn = '<a class="btn btn-dark text-white text-decoration-none w-100 text-uppercase te-request-review border-0 disabled" href="javascript::void(0);" role="button" aria-disabled="true">Request Review</a>';
                    else
                        $requestReviewBtn = '<a class="btn btn-dark text-white text-decoration-none w-100 text-uppercase te-request-review border-0" href="'.$requestReview.'" role="button">Request Review</a>';
                }
                elseif(isset($arrReqBookIds[$item[$objBM->Data['F_PrimaryKey']]])){
                    if($arrReqBookIds[$item[$objBM->Data['F_PrimaryKey']]]['brc_request_status']== STATUS_COMPLETED || $arrReqBookIds[$item[$objBM->Data['F_PrimaryKey']]]['brc_request_status']== STATUS_DENY )
                        $requestReviewBtn = '';
                    elseif($arrReqBookIds[$item[$objBM->Data['F_PrimaryKey']]]['brc_request_status']== STATUS_PENDING)
                        $requestReviewBtn = '<div class="alert alert-warning">'.BRStaticData::arrReqStatusReviewer()[STATUS_PENDING].'</div>';
                }

                $itemHTML .= '<div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 te-product-box padding-info">
                                <div class="card border-0 te-shadow">
                                    <a href="'.$item['bcat_amazon_permalink'].'" target="_blank">
                                        <img src="'.$objBM->Data['V_Upload'].'/'.$item['bcat_cover_image'].'" class="card-img-top" alt="'.$item['bcat_title'].'">
                                    </a>
                                    <div class="card-body px-0 pt-2 pb-1 bg-white position-relative">
                                        <h4 class="card-title te-card-title mb-0 te-font-size-16 font-weight-bold mb-1">
                                            <a href="'.$item['bcat_amazon_permalink'].'" class="text-decoration-none" target="_blank">'.$item['bcat_title'].'</a>
                                        </h4>
                                        '.$requestReviewBtn.'
                                    </div>
                                </div>
                            </div>';
            }
        }elseif(isset($_GET['keyword'])){
            $itemHTML = '   
                                     <div class="col-12">
                                        <div class="text text-warning text-center h3 my-5">No data found. Please change your search criteria and try again.</div>     
                                    </div> 
                                ';
        }
        else{
            $itemHTML = '   
                                     <div class="col-12">
                                        <div class="alert alert-secondary">There is no book found in your region.</div>     
                                    </div> 
                                ';
        }

        $html = '<section class="">
                    <div class="container-fluid main-info">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                '.$msg_success.'
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <h3>Search Books</h3>
                            </div>
                            <div class="col-12">
                                <form class="form-inline-" method="get">
                                    <div class="row -justify-content-md-end">
                                        <div class="col-12 col-md-6">
                                            <div class="input-group form-group">
                                                <input type="text" class="form-control" name="keyword" id="book_title" value="'.(isset($_GET['keyword'])?$_GET['keyword']:'').'" placeholder="Search books">
                                                <div class="input-group-prepend">
                                                    <button type="submit" class="btn btn-dark"><i class="fas fa-search" aria-hidden="true"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <div class="input-group form-group">
                                                <input type="date" class="form-control" name="adt" id="adt" value="'.(isset($_GET['adt'])?$_GET['adt']:'').'" placeholder="Added date">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <button type="submit" class="btn btn-dark">Search &nbsp;<i class="fas fa-search" aria-hidden="true"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                '.$msg_error.'
                            </div>
                        </div>
                        <div class="row">
                            '.$itemHTML.'
                        </div>
                    </div>
                </section>';

        $html .= $reviwerReq;



        return $html;
    }
    /*public static function getAutherListingsHTML($arrData, $arrReqResult1=false, $msg_success='', $msg_error=''){
        global $wp, $arrPhysicalPath;
        include_once $arrPhysicalPath['DBAccess']. 'BookMaster.php';
        $objBM = BookMaster::getInstance();

        include_once $arrPhysicalPath['DBAccess']. 'BookReviewRequest.php';
        $objBRR = BookReviewRequest::getInstance();

        $authorHTML = '';
        $arrReqBookIds = array();
        if(is_array($arrReqResult1) && count($arrAuthorResult) > 0){
            foreach ($arrAuthorResult as $ak => $authoritem){
                //echo '<pre>';print_r($ritem);die;
                # Set key as book id and value as author id
                $arrReqBookIds[$authoritem[$objBRR->Data['F_F_Key']]] = $authoritem[$objBRR->Data['F_PrimaryField']];

                $authorHTML = '<section class="py-5">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <h1 class="test te-front-side">Reviewer Dashboard</h1>
                                    </div>
                                </div>
        
                                <div class="row">
                                    <div class="col-xl-12 table-responsive px-0">
                                        <table class="table te-table table-hover">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Image</th>
                                                    <th scope="col">Book Title</th>
                                                    <th scope="col">Link to Amazon</th>
                                                    <th scope="col">Author Name</th>
                                                    <th scope="col">Review Request</th>
                                     
                                                </tr>
                                            </thead>
                                            <tbody>
                                            
                                                <tr>
                                                    <td><img class="w-75" src="#"></td>
                                                    <td>Test</td>
                                                    <td><a href="#" class="text-decoration-none te-link-to-amazon" target="_blank">Link to Amazon</a></td>
                                                    <td></td>
                                                    <td>Test</td>
                                                </tr>
                                              
        
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>';
                //return $reqHTML;
            }
        }else{
            $authorHTML = '  <div class="container">
                             <div class="col-12">
                                <div class="alert alert-secondary">No books found.</div>     
                            </div>  
                        </div>';
        }

    }

*/
    public static function getDesignListingsHTML(){
        $html = '<section>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <h1 class="test te-front-side">Best seller books</h1>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 p-3 te-product-box">
                                <div class="card border-0">
                                    <div class="position-relative">
                                        <img src="/wp-content/plugins/book-review/front/templates/image/one-arranged-murder.jpg" class="card-img-top" alt="...">
                                        <div class="te-quick-view bg-primary text-uppercase text-center position-absolute w-100">
                                            <a href="#" class="text-white text-decoration-none d-block p-3"> QUICK VIEW </a>
                                        </div>
                                    </div>
                                    <div class="card-body px-0 pt-2 pb-1 bg-white position-relative">
                                        <span class="te-category-list mb-0">
                                            <a href="#" class="text-decoration-none text-black-50">English,</a>
                                            <a href="#" class="text-decoration-none text-black-50">Chetan Bhagat,</a>
                                            <a href="#" class="text-decoration-none text-black-50">Best Seller</a>
                                        </span>
                                        <h4 class="card-title te-card-title mb-0 te-font-size-16 font-weight-bold"><a href="#" class="text-decoration-none">One Arranged Murder</a></h4>
                                        <div class="te-star te-width-max-content" data-toggle="tooltip" data-placement="top" title="4.67">
                                            <i class="fas fa-star text-black"></i>
                                            <i class="fas fa-star text-black"></i>
                                            <i class="fas fa-star text-black"></i>
                                            <i class="fas fa-star text-black"></i>
                                            <i class="fas fa-star te-star-light"></i>
                                        </div>
                                        <div>
                                            <span class="font-weight-bolder text-dark pr-1 te-main-price">$120</span>
                                            <span class="text-black-50 pr-1 te-font-size-16"><del>$225</del></span>
                                            <span class="text-primary">(46% OFF)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 p-3 te-product-box">
                                <div class="card border-0">
                                    <img src="/wp-content/plugins/book-review/front/templates/image/one-arranged-murder.jpg" class="card-img-top" alt="...">
                                    <div class="card-body px-0 pt-2 pb-1 bg-white position-relative">
                                        <h4 class="card-title te-card-title mb-0 te-font-size-16 font-weight-bold">
                                            <a href="#" class="text-decoration-none">One Arranged Murder</a>
                                        </h4>
                                        <div class="te-category-list-1 mb-0 py-2">
                                            <a href="#" class="text-decoration-none text-black-50">Link to Amazon</a>
                                        </div>
                                        <a class="btn btn-dark text-white text-decoration-none w-100 text-uppercase te-request-review border-0" href="#" role="button">Request Review</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 p-3 te-product-box">
                                <div class="card border-0">
                                    <img src="/wp-content/plugins/book-review/front/templates/image/3-mistakes-of-my-life.jpg" class="card-img-top" alt="...">
                                    <div class="card-body px-0 pt-2 pb-1 bg-white">
                                        <h4 class="card-title te-card-title mb-0 te-font-size-16 font-weight-bold">
                                            <a href="#" class="text-decoration-none">The 3 Mistakes of My Life</a>
                                        </h4>
                                        <div class="te-category-list-1 mb-0 py-2">
                                            <a href="#" class="text-decoration-none text-black-50">Link to Amazon</a>
                                        </div>
                                        <a class="btn text-white text-decoration-none text-uppercase te-request-review-2 border-0 position-absolute te-width-max-content" href="#" role="button">Request Review</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 p-3 te-product-box">
                                <div class="card border-0">
                                    <img src="/wp-content/plugins/book-review/front/templates/image/2-states.jpg" class="card-img-top" alt="...">
                                    <div class="card-body px-0 pt-2 pb-1 bg-white">
                                        <h4 class="card-title te-card-title mb-0 te-font-size-16 font-weight-bold">
                                            <a href="#" class="text-decoration-none">2 States - Story of Marriage</a>
                                        </h4>
                                        <div class="te-category-list-1 mb-0 py-2">
                                            <a href="#" class="text-decoration-none text-black-50">Link to Amazon</a>
                                        </div>
                                        <a class="btn text-white text-decoration-none text-uppercase te-request-review-3 border-0 position-absolute te-width-max-content" href="#" role="button">Request Review</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 p-3 te-product-box">
                                <div class="card border-0">
                                    <div class="position-relative">
                                        <img src="/wp-content/plugins/book-review/front/templates/image/3-mistakes-of-my-life.jpg" class="card-img-top" alt="...">
                                        <div class="te-quick-view bg-primary text-uppercase text-center position-absolute w-100">
                                            <a href="#" class="text-white text-decoration-none d-block p-3"> QUICK VIEW </a>
                                        </div>
                                    </div>
                                    <div class="card-body px-0 pt-2 pb-1 bg-white position-relative">
                                        <span class="te-category-list mb-0">
                                            <a href="#" class="text-decoration-none text-black-50">English,</a>
                                            <a href="#" class="text-decoration-none text-black-50">Chetan Bhagat,</a>
                                            <a href="#" class="text-decoration-none text-black-50">Best Seller</a>
                                        </span>
                                        <h4 class="card-title te-card-title mb-0 te-font-size-16 font-weight-bold"><a href="#" class="text-decoration-none">The 3 Mistakes of My Life</a></h4>
                                        <div class="te-star te-width-max-content" data-toggle="tooltip" data-placement="top" title="5.00">
                                            <i class="fas fa-star text-black"></i>
                                            <i class="fas fa-star text-black"></i>
                                            <i class="fas fa-star text-black"></i>
                                            <i class="fas fa-star text-black"></i>
                                            <i class="fas fa-star text-black"></i>
                                        </div>
                                        <div>
                                            <span class="font-weight-bolder text-dark pr-1 te-main-price">$150</span>
                                            <span class="text-black-50 pr-1 te-font-size-16"><del>$215</del></span>
                                            <span class="text-primary">(40% OFF)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 p-3 te-product-box">
                                <div class="card border-0">
                                    <div class="position-relative">
                                        <img src="/wp-content/plugins/book-review/front/templates/image/2-states.jpg" class="card-img-top" alt="...">
                                        <div class="te-quick-view bg-primary text-uppercase text-center position-absolute w-100">
                                            <a href="#" class="text-white text-decoration-none d-block p-3"> QUICK VIEW </a>
                                        </div>
                                    </div>
                                    <div class="card-body px-0 pt-2 pb-1 bg-white position-relative">
                                        <span class="te-category-list mb-0">
                                            <a href="#" class="text-decoration-none text-black-50">English,</a>
                                            <a href="#" class="text-decoration-none text-black-50">Chetan Bhagat,</a>
                                            <a href="#" class="text-decoration-none text-black-50">Best Seller</a>
                                        </span>
                                        <h4 class="card-title te-card-title mb-0 te-font-size-16 font-weight-bold"><a href="#" class="text-decoration-none">2 States - Story of Marriage</a></h4>
                                        <div class="te-star te-width-max-content" data-toggle="tooltip" data-placement="top" title="4.67">
                                            <i class="fas fa-star text-black"></i>
                                            <i class="fas fa-star text-black"></i>
                                            <i class="fas fa-star text-black"></i>
                                            <i class="fas fa-star text-black"></i>
                                            <i class="fas fa-star te-star-light"></i>
                                        </div>
                                        <div>
                                            <span class="font-weight-bolder text-dark pr-1 te-main-price">$120</span>
                                            <span class="text-black-50 pr-1 te-font-size-16"><del>$225</del></span>
                                            <span class="text-primary">(46% OFF)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="py-4">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <h1 class="test te-front-side">Add Book Details</h1>
                            </div>
                        </div>
                        <form>
                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                                    <div class="form-group">
                                        <label class="text-dark-50 font-weight-bold te-label" for="InputAsin">Asin</label>
                                        <input type="text" class="form-control p-3" id="InputAsin" placeholder="Enter Asin">
                                     </div>
                                </div>

                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                                    <div class="form-group">
                                        <label class="text-dark-50 font-weight-bold te-label" for="InputBookTitle">Book title</label>
                                        <input type="text" class="form-control p-3" id="InputBookTitle" placeholder="Enter Book title">
                                     </div>
                                </div>

                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                                    <label class="text-dark-50 font-weight-bold te-label" for="InputBookTitle">Book format</label>
                                    <select class="custom-select te-form-select">
                                        <option selected>Book format</option>
                                        <option value="Format 1">Format 1</option>
                                        <option value="Format 1">Format 2</option>
                                        <option value="Format 1">Format 3</option>
                                    </select>
                                </div>

                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                                    <div class="form-group">
                                        <label class="text-dark-50 font-weight-bold te-label" for="InputBookIsbn13">Isbn 13</label>
                                        <input type="text" class="form-control p-3" id="InputBookIsbn13" placeholder="Enter Isbn 13">
                                     </div>
                                </div>

                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                                    <div class="form-group">
                                        <label class="text-dark-50 font-weight-bold te-label" for="InputAmazonPermalink">Amazon permalink</label>
                                        <input type="text" class="form-control p-3" id="InputAmazonPermalink" placeholder="Enter Amazon permalink">
                                     </div>
                                </div>

                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                                    <label class="text-dark-50 font-weight-bold te-label" for="InputBookTitle">Is_Active</label>
                                    <select class="custom-select te-form-select">
                                        <option selected>Is_Active (yes/no)</option>
                                        <option value="Format 1">Yes</option>
                                        <option value="Format 1">No</option>
                                    </select>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 mb-1 mb-md-3">
                                    <label for="exampleFormControlFile1" class="text-dark-50 font-weight-bold">Cover image</label>
                                    <div class="custom-file te-custom-file">
                                        <input type="file" class="custom-file-input h-100" id="cover_image">
                                        <label class="custom-file-label h-100" for="customFile">Choose file</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 mb-1 mb-md-3">
                                    <label for="exampleFormControlFile1" class="text-dark-50 font-weight-bold">Sample book file</label>
                                    <div class="custom-file te-custom-file">
                                        <input type="file" class="custom-file-input h-100" id="sample_book_file">
                                        <label class="custom-file-label h-100" for="sample_book_file">Choose file</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 mb-1 mb-md-3">
                                    <label for="exampleFormControlFile1" class="text-dark-50 font-weight-bold">Full length book file</label>
                                    <div class="custom-file te-custom-file">
                                        <input type="file" class="custom-file-input h-100" id="full_length_book_file">
                                        <label class="custom-file-label h-100" for="full_length_book_file">Choose file</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 my-md-4">
                                    <a class="btn btn-dark py-3 px-5" role="button" type="submit" href="#">ADD AUTHOR</a>
                                </div>
                            </div>

                        </form>
                    </div>
                </section>

                <section>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 p-3 te-product-box">
                                <div class="card border-0">
                                    <img src="/wp-content/plugins/book-review/front/templates/image/one-arranged-murder.jpg" class="card-img-top" alt="...">
                                    <div class="card-body px-0 pt-2 pb-1 bg-white position-relative">
                                        <h4 class="card-title te-card-title mb-0 te-font-size-16 font-weight-bold">
                                            <a href="#" class="text-decoration-none">One Arranged Murder</a>
                                        </h4>
                                        <div class="te-category-list-1 mb-0 py-2">
                                            <a href="#" class="text-decoration-none text-black-50">Link to Amazon</a>
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4 pr-1">
                                                <a class="btn btn-dark text-white text-decoration-none w-100 text-uppercase te-request-review border-0" href="#" role="button">Edit</a>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4 px-1">
                                                <a class="btn btn-dark text-white text-decoration-none w-100 text-uppercase te-request-review border-0" href="#" role="button">Delete</a>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4 pl-1">
                                                <a class="btn btn-dark text-white text-decoration-none w-100 text-uppercase te-request-review border-0" href="#" role="button">View</a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 p-3 te-product-box">
                                <div class="card border-0">
                                    <img src="/wp-content/plugins/book-review/front/templates/image/one-arranged-murder.jpg" class="card-img-top" alt="...">
                                    <div class="card-body px-0 pt-2 pb-1 bg-white position-relative">
                                        <h4 class="card-title te-card-title mb-0 te-font-size-16 font-weight-bold">
                                            <a href="#" class="text-decoration-none">One Arranged Murder</a>
                                        </h4>
                                        <div class="te-category-list-1 mb-0 py-2">
                                            <a href="#" class="text-decoration-none text-black-50">Link to Amazon</a>
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4 pr-1">
                                                <a class="btn btn-dark text-white text-decoration-none w-100 text-uppercase te-request-review border-0" href="#" role="button">Edit</a>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4 px-1">
                                                <a class="btn btn-dark text-white text-decoration-none w-100 text-uppercase te-request-review border-0" href="#" role="button">Delete</a>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4 pl-1">
                                                <a class="btn btn-dark text-white text-decoration-none w-100 text-uppercase te-request-review border-0" href="#" role="button">View</a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 p-3 te-product-box">
                                <div class="card border-0">
                                    <img src="/wp-content/plugins/book-review/front/templates/image/one-arranged-murder.jpg" class="card-img-top" alt="...">
                                    <div class="card-body px-0 pt-2 pb-1 bg-white position-relative">
                                        <h4 class="card-title te-card-title mb-0 te-font-size-16 font-weight-bold">
                                            <a href="#" class="text-decoration-none">One Arranged Murder</a>
                                        </h4>
                                        <div class="te-category-list-1 mb-0 py-2">
                                            <a href="#" class="text-decoration-none text-black-50">Link to Amazon</a>
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4 pr-1">
                                                <a class="btn btn-dark text-white text-decoration-none w-100 text-uppercase te-request-review border-0" href="#" role="button">Edit</a>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4 px-1">
                                                <a class="btn btn-dark text-white text-decoration-none w-100 text-uppercase te-request-review border-0" href="#" role="button">Delete</a>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4 pl-1">
                                                <a class="btn btn-dark text-white text-decoration-none w-100 text-uppercase te-request-review border-0" href="#" role="button">View</a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>


                <section class="py-5">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <h1 class="test te-front-side">Review progress</h1>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-12 table-responsive px-0">
                                <table class="table te-table table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">Image</th>
                                            <th scope="col">Book Title</th>
                                            <th scope="col">Reviewer Name</th>
                                            <th scope="col">Reviewer Email</th>
                                            <th scope="col">Member Since</th>
                                            <th scope="col">Last Seen On</th>
                                            <th scope="col">Reviews Completed</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><img class="w-75" src="/wp-content/plugins/book-review/front/templates/image/2-states.jpg"></td>
                                            <td>2 States - Story of My Marriage</td>
                                            <td>John doe</td>
                                            <td>johndoe@gmail.com</td>
                                            <td>5 Months</td>
                                            <td>1 hour ago</td>
                                            <td>Lorem Ipsum</td>
                                            <td>
                                                <div class="dropdown te-dropdown">
                                                    <button class="btn btn-dark dropdown-toggle te-dropdown border-0" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Action
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <a class="dropdown-item" href="#">Approve</a>
                                                        <a class="dropdown-item" href="#">Reject</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><img class="w-75" src="/wp-content/plugins/book-review/front/templates/image/3-mistakes-of-my-life.jpg"></td>
                                            <td>The 3 Mistakes of My Life</td>
                                            <td>John doe</td>
                                            <td>johndoe@gmail.com</td>
                                            <td>2 Years</td>
                                            <td>15 minutes ago</td>
                                            <td>Lorem Ipsum</td>
                                            <td>
                                                <div class="dropdown te-dropdown">
                                                    <button class="btn btn-dark dropdown-toggle te-dropdown border-0" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Action
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <a class="dropdown-item" href="#">Approve</a>
                                                        <a class="dropdown-item" href="#">Reject</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><img class="w-75" src="/wp-content/plugins/book-review/front/templates/image/what-young-india-wants.jpeg"></td>
                                            <td>What Young India Wants</td>
                                            <td>John doe</td>
                                            <td>johndoe@gmail.com</td>
                                            <td>2 Months</td>
                                            <td>9 hour ago</td>
                                            <td>Lorem Ipsum</td>
                                            <td>
                                                <div class="dropdown te-dropdown">
                                                    <button class="btn btn-dark dropdown-toggle te-dropdown border-0" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Action
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <a class="dropdown-item" href="#">Approve</a>
                                                        <a class="dropdown-item" href="#">Reject</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><img class="w-75" src="/wp-content/plugins/book-review/front/templates/image/one-arranged-murder.jpg"></td>
                                            <td>One Arranged Murder</td>
                                            <td>John doe</td>
                                            <td>johndoe@gmail.com</td>
                                            <td>6 Months</td>
                                            <td>2 hour ago</td>
                                            <td>Lorem Ipsum</td>
                                            <td>
                                                <div class="dropdown te-dropdown">
                                                    <button class="btn btn-dark dropdown-toggle te-dropdown border-0" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Action
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <a class="dropdown-item" href="#">Approve</a>
                                                        <a class="dropdown-item" href="#">Reject</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="py-5">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <h1 class="test te-front-side">Reviewer Dashboard</h1>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-12 table-responsive px-0">
                                <table class="table te-table table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">Image</th>
                                            <th scope="col">Book Title</th>
                                            <th scope="col">Link to Amazon</th>
                                            <th scope="col">Author Name</th>
                                            <th scope="col">Review Request</th>
                                            <th scope="col">Book Price</th>
                                            <th scope="col">Book Ratings</th>
                                            <th scope="col">Book Language</th>
                                            <th scope="col">Print Length</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><img class="w-75" src="/wp-content/plugins/book-review/front/templates/image/2-states.jpg"></td>
                                            <td>2 States - Story of My Marriage</td>
                                            <td><a href="#" class="text-decoration-none te-link-to-amazon">Link to Amazon</a></td>
                                            <td>Chetan Bhagat</td>
                                            <td>Pending</td>
                                            <td>$18.95</td>
                                            <td>4.2 star</td>
                                            <td>English</td>
                                            <td>220 Pages</td>
                                        </tr>
                                        <tr>
                                            <td><img class="w-75" src="/wp-content/plugins/book-review/front/templates/image/3-mistakes-of-my-life.jpg"></td>
                                            <td>The 3 Mistakes of My Life</td>
                                            <td><a href="#" class="text-decoration-none te-link-to-amazon">Link to Amazon</a></td>
                                            <td>Chetan Bhagat</td>
                                            <td>Approved</td>
                                            <td>$12.95</td>
                                            <td>4.6 star</td>
                                            <td>English</td>
                                            <td>312 Pages</td>
                                        </tr>
                                        <tr>
                                            <td><img class="w-75" src="/wp-content/plugins/book-review/front/templates/image/what-young-india-wants.jpeg"></td>
                                            <td>What Young India Wants</td>
                                            <td><a href="#" class="text-decoration-none te-link-to-amazon">Link to Amazon</a></td>
                                            <td>Chetan Bhagat</td>
                                            <td>Rejected</td>
                                            <td>$12.95</td>
                                            <td>4.6 star</td>
                                            <td>English</td>
                                            <td>312 Pages</td>
                                        </tr>
                                        <tr>
                                            <td><img class="w-75" src="/wp-content/plugins/book-review/front/templates/image/one-arranged-murder.jpg"></td>
                                            <td>One Arranged Murder</td>
                                            <td><a href="#" class="text-decoration-none te-link-to-amazon">Link to Amazon</a></td>
                                            <td>Chetan Bhagat</td>
                                            <td>Approved</td>
                                            <td>$12.95</td>
                                            <td>4.6 star</td>
                                            <td>English</td>
                                            <td>312 Pages</td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>';
        return $html;
}
    public static function getAutherAddBookHTML($item, $msg_error= false){
        global $wp;

        # Get current url with query strig
        //$currentUrl = add_query_arg( $_SERVER['QUERY_STRING'], '', home_url( $wp->request ) );
        $cancelURL = home_url( $wp->request );

        $objBM = BookMaster::getInstance();

        $arrBooksFormate = BRStaticData::arrBooksFormate();
        $arrActive = BRStaticData::arrYesNo();
        $arrCountry = BRStaticData::arrCountryData();


        $htmlOptionFormate = '';
        if(is_array($arrBooksFormate) && count($arrBooksFormate) > 0){
            foreach ($arrBooksFormate as $bfk => $bfv){
                $selected = '';
                if(isset($item['bcat_book_format']) && $item['bcat_book_format'] == strval($bfk))
                    $selected = 'selected';

                $htmlOptionFormate .= '<option value="'.$bfk.'" '.$selected.'>'.$bfv.'</option>';
            }
        }

        $htmlOptionActive = '';
        if(is_array($arrActive) && count($arrActive) > 0){
            foreach ($arrActive as $bak => $bav){
                $selected_a = '';

                if(isset($item[$objBM->Data['F_VisibleField']]) && $item[$objBM->Data['F_VisibleField']] == strval($bak))
                    $selected_a = 'selected="selected"';
                else if(!isset($item[$objBM->Data['F_VisibleField']]) && strval($bak) == IS_UNPUBLISH)
                    $selected_a = 'selected="selected"';

                $htmlOptionActive .= '<option value="'.$bak.'" '.$selected_a.' >'.$bav.'</option>';
            }
        }

        $htmlOptionCountry = '';
        if(is_array($arrCountry) && count($arrCountry) > 0){
            foreach ($arrCountry as $cfk => $cfv){
                $selected_c = '';
                if(isset($item[$objBM->Data['F_PrimaryField']]) /*&& $item[$objBM->Data['F_PrimaryField']] == $cfk*/)
                {
                    $arrMCountry = explode(',', $item[$objBM->Data['F_PrimaryField']]);

                    if(in_array($cfk, $arrMCountry))
                        $selected_c = 'selected';
                }


                $htmlOptionCountry .= '<option value="'.$cfk.'" '.$selected_c.'>'.$cfv.'</option>';
            }
        }

        $htmlCoverImage = '';
        if(isset($item[$objBM->Data['Field_Prefix'].'cover_image']) && $item[$objBM->Data['Field_Prefix'].'cover_image'] != ''){
            $htmlCoverImage = '
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                    <label for="exampleFormControlFile1" class="text-dark-50 font-weight-bold text-uppercase">Cover image</label>
                    <div class="custom-file te-custom-file">
                        <input type="file" name="'.$objBM->Data['Field_Prefix'].'cover_image" class="custom-file-input h-100 mb-2" id="cover_image" accept="image/*" >
                        <div class="invalid-feedback">Please choose cover image.</div>
                        <input type="hidden" name="prev_'.$objBM->Data['Field_Prefix'].'cover_image" class="" value="'.$item[$objBM->Data['Field_Prefix'].'cover_image'].'">
                        <label class="custom-file-label h-100" for="cover_image">Choose file</label>
                        <img class="img-thumbnail" width="100" height="100" src="'.$objBM->Data['V_Upload'].'/'.$item[$objBM->Data['Field_Prefix'].'cover_image'].'">
                        <div class="form-check">
                          <input name="delete_'.$objBM->Data['Field_Prefix'].'cover_image" class="form-check-input" type="checkbox" value="1" >
                          <label class="form-check-label" for="gridCheck">
                            Delete Cover Image.
                          </label>
                        </div>
                        <small id="cover_imageHelp" class="form-text text-muted">Valid File Format : '.BRStaticData::valid_pic_format().'</small>
                    </div>
                </div>';
        }
        else{
            $htmlCoverImage = '
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                    <label for="exampleFormControlFile1" class="text-dark-50 font-weight-bold text-uppercase">Cover Image</label>
                    <div class="custom-file te-custom-file">
                        <input type="file" name="'.$objBM->Data['Field_Prefix'].'cover_image" class="custom-file-input h-100" id="cover_image" accept="image/*" required>
                        <div class="invalid-feedback">Please choose cover image.</div>
                        <label class="custom-file-label h-100" for="cover_image">Choose file</label>
                        <small id="cover_imageHelp" class="form-text text-muted">Valid File Format : '.BRStaticData::valid_pic_format().'</small>
                    </div>
                </div>';
        }


        $htmlSampleImage = '';
        if(isset($item[$objBM->Data['Field_Prefix'].'sample_book_file']) && $item[$objBM->Data['Field_Prefix'].'sample_book_file'] != ''){
            $htmlSampleImage = '
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                    <label for="exampleFormControlFile1" class="text-dark-50 font-weight-bold text-uppercase">Sample Book File</label>
                    <div class="custom-file te-custom-file">
                        <input type="file" name="'.$objBM->Data['Field_Prefix'].'sample_book_file" class="custom-file-input h-100" id="bcat_sample_book_file" accept="application/pdf" >
                        <div class="invalid-feedback">Please choose cover image.</div>
                        <input type="hidden" name="prev_'.$objBM->Data['Field_Prefix'].'sample_book_file" class="" value="'.$item[$objBM->Data['Field_Prefix'].'sample_book_file'].'">
                        <label class="custom-file-label h-100" for="sample_book_file">Choose file</label>
                        <a href="'.$objBM->Data['V_Upload'].'/'.$item[$objBM->Data['Field_Prefix'].'sample_book_file'].'" target="_blank">View Sample File</a>
                        <div class="form-check">
                          <input name="delete_'.$objBM->Data['Field_Prefix'].'sample_book_file" class="form-check-input" type="checkbox" value="1">
                          <label class="form-check-label" for="gridCheck">
                            Delete Sample File.
                          </label>
                        </div>
                        <small id="sample_book_fileHelp" class="form-text text-muted">Valid File Format : '.BRStaticData::valid_doc_format().'</small>
                    </div>
                </div>';
        }
        else{
            $htmlSampleImage = '
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                    <label for="exampleFormControlFile1" class="text-dark-50 font-weight-bold text-uppercase">Sample Book File</label>
                    <div class="custom-file te-custom-file">
                        <input type="file" name="'.$objBM->Data['Field_Prefix'].'sample_book_file" class="custom-file-input h-100" id="sample_book_image" accept="application/pdf" required>
                        <div class="invalid-feedback">Please choose cover image.</div>
                        <label class="custom-file-label h-100" for="cover_image">Choose file</label>
                        <small id="sample_book_fileHelp" class="form-text text-muted">Valid File Format : '.BRStaticData::valid_doc_format().'</small>
                    </div>
                </div>';
        }

        $htmlFullImage = '';
        if(isset($item[$objBM->Data['Field_Prefix'].'full_length_book_file']) && $item[$objBM->Data['Field_Prefix'].'full_length_book_file'] != ''){
            $htmlFullImage = '
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                    <label for="exampleFormControlFile1" class="text-dark-50 font-weight-bold text-uppercase">Full Length Book File</label>
                    <div class="custom-file te-custom-file">
                        <input type="file" name="'.$objBM->Data['Field_Prefix'].'full_length_book_file" class="custom-file-input h-100" id="full_length_book_file" accept="application/pdf">
                        <div class="invalid-feedback">Please choose cover image.</div>
                        <input type="hidden" name="prev_'.$objBM->Data['Field_Prefix'].'full_length_book_file" class="" value="'.$item[$objBM->Data['Field_Prefix'].'full_length_book_file'].'">
                        <label class="custom-file-label h-100" for="full_length_book_file">Choose file</label>
                        <a href="'.$objBM->Data['V_Upload'].'/'.$item[$objBM->Data['Field_Prefix'].'full_length_book_file'].'" target="_blank">View Full Book File</a>
                        <div class="form-check">
                          <input name="delete_'.$objBM->Data['Field_Prefix'].'full_length_book_file" class="form-check-input" type="checkbox" value="1">
                          <label class="form-check-label" for="gridCheck">
                            Delete Full Length File.
                          </label>
                        </div>
                        <small id="full_length_book_fileHelp" class="form-text text-muted">Valid File Format : '.BRStaticData::valid_doc_format().'</small>
                    </div>
                </div>';
        }
        else{
            $htmlFullImage = '
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                    <label for="exampleFormControlFile1" class="text-dark-50 font-weight-bold text-uppercase">Full Length Book File</label>
                    <div class="custom-file te-custom-file">
                        <input type="file" name="'.$objBM->Data['Field_Prefix'].'full_length_book_file" class="custom-file-input h-100" id="full_length_book_file" accept="application/pdf" required>
                        <div class="invalid-feedback">Please choose cover image.</div>
                        <label class="custom-file-label h-100" for="full_length_book_file">Choose file</label>
                        <small id="full_length_book_fileHelp" class="form-text text-muted">Valid File Format : '.BRStaticData::valid_doc_format().'</small>
                    </div>
                </div>';
        }
        $editActClass = 'text-uppercase';
        /*if(isset($item[$objBM->Data['F_PrimaryKey']])){
            $editActClass = 'text-uppercase';
        }*/
        $error = ($msg_error != false)?$msg_error:'';

        $html = '<section class="py-4">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <h1 class="test te-front-side">'.((is_array($item) && count($item) > 0)?'Edit Book Details':'Add Book Details').'</h1>
                            </div>
                        </div>
                        <form class="needs-validation" name="myForm" method="post" id="bkadd-form" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-12">
                                    '.$error.'
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                    <label class="text-dark-50 font-weight-bold te-label  text-uppercase " for="InputAsin">ASIN (or ISBN)</label>
                                    <input type="text" name="'.$objBM->Data['Field_Prefix'].'asin" class="form-control " id="InputAsin" placeholder="Enter Asin" value="'.(isset($item[$objBM->Data['Field_Prefix'].'asin'])?$item[$objBM->Data['Field_Prefix'].'asin']:'').'" required>
                                    <div class="invalid-feedback">Please enter valid asin</div>
                                </div>
                
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                    <label class="text-dark-50 font-weight-bold te-label  text-uppercase " for="InputBookTitle">Book Title</label>
                                    <input type="text" name="'.$objBM->Data['Field_Prefix'].'title" class="form-control" id="InputBookTitle" placeholder="Enter Book title" value="'.(isset($item[$objBM->Data['Field_Prefix'].'title'])?$item[$objBM->Data['Field_Prefix'].'title']:'').'" required>
                                    <div class="invalid-feedback">Please enter valid book title.</div>
                                </div>
                
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                    <label class="text-dark-50 font-weight-bold te-label text-uppercase " for="InputBookTitle">Book Format</label>
                                    <select name="'.$objBM->Data['Field_Prefix'].'book_format" class="custom-select- te-form-select form-control " required>
                                        <option>--- Select Book Format ---</option>
                                        '.$htmlOptionFormate.'
                                    </select>
                                </div>
                
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                    <label class="text-dark-50 font-weight-bold te-label text-uppercase " for="InputBookIsbn13">Isbn 13</label>
                                    <input type="text" name="'.$objBM->Data['Field_Prefix'].'isbn_13" class="form-control isbn13" id="InputBookIsbn13" placeholder="Enter Isbn 13" value="'.(isset($item[$objBM->Data['Field_Prefix'].'isbn_13'])?$item[$objBM->Data['Field_Prefix'].'isbn_13']:'').'">
                                </div>
                
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                    <label class="text-dark-50 font-weight-bold te-label text-uppercase " for="InputAmazonPermalink">Amazon Permalink</label>
                                    <input type="text"  name="'.$objBM->Data['Field_Prefix'].'amazon_permalink" class="form-control valid-url" id="InputAmazonPermalink" placeholder="Enter Amazon permalink" value="'.(isset($item[$objBM->Data['Field_Prefix'].'amazon_permalink'])?$item[$objBM->Data['Field_Prefix'].'amazon_permalink']:'').'" required>
                                    <div class="invalid-feedback ">Please enter valid url.</div>
                                    <small id="InputAmazonPermalinkHelp" class="form-text text-muted">Eg: http://xyz.com, http://www.xyz.com, https://xyz.com etc.. </small>
                                </div>
                                
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                    <label class="text-dark-50 font-weight-bold te-label text-uppercase" for="InputBookTitle">Marketplace</label>
                                    <select name="'.$objBM->Data['Field_Prefix'].'country_code[]" class="custom-select- te-form-select- selectpicker- mul-selectize " multiple="multiple" multiple- -data-live-search="true"  required>
                                        '.$htmlOptionCountry.'
                                    </select>
                                </div>';
                
                                /*<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 form-group">
                                    <label class="text-dark-50 font-weight-bold te-label '.$editActClass.'" for="InputBookTitle">Is Active?</label>
                                    <select name="'.$objBM->Data['Field_Prefix'].'is_active"  class="custom-select te-form-select">
                                        '.$htmlOptionActive.'
                                    </select>
                                </div>*/
                                
                            $html .= '</div>
                            <div class="row">'.$htmlCoverImage.'</div>
                            <div class="row">'.$htmlSampleImage.'</div>
                            <div class="row">'.$htmlFullImage.'</div>
                            <div class="row">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 form-group">
                                    <input class="btn btn-dark btn-xl btn-block-info" type="submit" name="AddBK" value="'.((is_array($item) && count($item) > 0)?'UPDATE BOOK':'ADD BOOK').'" />
                                    <a href="'.$cancelURL.'" id="cancel" name="cancel" class="btn btn-danger btn-xl btn-block-info">CANCEL</a>
                                    <input class="btn btn-dark btn-xl" type="hidden" name="PK" value="'.(isset($item[$objBM->Data['F_PrimaryKey']])?$item[$objBM->Data['F_PrimaryKey']]:'').'" />
                                </div>
                            </div>
                        </form>
                    </div>
                </section>';

        return $html;
    }
    public static function getNotLoggedsinUserTemplate(){
        $html = '<section class="py-4">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="alert alert-warning" role="alert">
                                    <h3 class="alert-heading mb-0">Logged in to gain access</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>';

        return $html;
    }


}








