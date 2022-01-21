<?php
ob_start();
require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))). DIRECTORY_SEPARATOR. 'wp-load.php');

include_once $arrPhysicalPath['DBAccess']. 'BookReviewRequest.php';

$objBRR = BookReviewRequest::getInstance();

$arrParam = array(
    'status' => array(STATUS_PENDING, STATUS_APPROVE),
);
//echo '<pre>';print_r($arrParam);
//$author_param['author_id'] = 1;
$arrPanddinResult = $objBRR->getRequestByParam($arrParam);
//$status = array('brc_pending_adt' => date('Y-m-d H:i:s'));
//echo '<pre>';print_r($status);
//echo '<pre>';print_r($arrPanddinResult);
//echo '<pre>';print_r(ucwords(get_bloginfo()));die;
$admin_email = get_option( 'admin_email' );
$site_logo = get_custom_logo();

$pendingCnt = 0;
$approveCnt = 0;
$pendingEmailCnt = 0;
$approveEmailCnt = 0;

if(is_array($arrPanddinResult) && count($arrPanddinResult) > 0){
    foreach($arrPanddinResult as $key => $val){

        # If status is PENDING
        if($val[$objBRR->Data['Field_Prefix'].'request_status'] == STATUS_PENDING){

            # Prepare pending date for date diffrence
            $pending_date = $val[$objBRR->Data['Field_Prefix'].'pending_adt'];
            $pending_date = new DateTime($pending_date);
            $today = new DateTime();
            
            # Get pending date and current date diffrence
            $interval = $pending_date->diff($today);
            $day = $interval->format('%a');
            //echo "<pre> pending_date".$day;
            # If pending date is before 3 days from current date then update status as DENY
            if($day >= 3) {
                $POST_UP[$objBRR->Data['Field_Prefix'].'request_status']    =   STATUS_DENY;
                $POST_UP[$objBRR->Data['Field_Prefix'].'denied_adt']        =   date('Y-m-d H:i:s');
                $POST_UP[$objBRR->Data['Field_Prefix'].'denied_reason']     =   'Request Expired';
                
                $result_p = $objBRR->updateByParam($POST_UP, $val[$objBRR->Data['F_PrimaryKey']]);

                if($result_p != false)
                    $pendingCnt += 1;
            }else{
                # Send email to author for PENDING status
                $to = $val['author_email'];
                $subject = 'Alert for pending book request to approve/deny';

                $E_Body = $objBRR->getPendingStatusEmail($val);
                $E_Layout = $objBRR->getEmailContent($E_Body);
                file_put_contents('email-auther.html',print_r($E_Layout, true));
                
                $message = $E_Layout;

                //add_filter( 'wp_mail_content_type', 'set_html_content_type' );

                $headers[] = 'Content-type: text/html; charset=utf-8';
                $headers[] = 'From: Administrative <'.$admin_email.'>';

                $mail = wp_mail($to, $subject, $message, $headers);

                $pendingEmailCnt += 1;
                # Reset content-type to avoid conflicts 
               // remove_filter( 'wp_mail_content_type',  'set_html_content_type' );
            }
        }

        # If status is APPROVE
        if($val[$objBRR->Data['Field_Prefix'].'request_status'] == STATUS_APPROVE){
            # Prepare approve date for date diffrence
            $approve_date = $val[$objBRR->Data['Field_Prefix'].'approved_adt'];
            $approve_date = new DateTime($approve_date);
            $today = new DateTime();
            
            # Get approve date and current date diffrence
            $interval = $approve_date->diff($today);
            $day = $interval->format('%a');
            //echo "<pre> approve_date ".$day;
            # If approve date is before 10 days from current date then update status as DENY
            if($day >= 10) {
                $POST_UP[$objBRR->Data['Field_Prefix'].'request_status']    =   STATUS_DENY;
                $POST_UP[$objBRR->Data['Field_Prefix'].'denied_adt']        =   date('Y-m-d H:i:s');
                $POST_UP[$objBRR->Data['Field_Prefix'].'denied_reason']     =   'Reviewer Abandoned';
                
                $result = $objBRR->updateByParam($POST_UP, $val[$objBRR->Data['F_PrimaryKey']]);
                if($result != false)
                    $approveCnt += 1;
            }
            else{
                 # Send email to reviewer for reviews pending completion
                 $EmailTo = $val['user_email'];
                 $EmailSubject = 'Alert for pending review to complete';
                 $EmailBody = "";

                $ER_Body = $objBRR->getApproveStatusEmail($val);
                $ER_Layout = $objBRR->getEmailContent($ER_Body);

                file_put_contents('email-reviewer.html',print_r($ER_Layout, true));

                $EmailBody = $ER_Layout;
 
                 //add_filter( 'wp_mail_content_type', 'set_html_content_type' );
 
                 $e_headers[] = 'Content-type: text/html; charset=utf-8';
                 $e_headers[] = 'From: Administrative <'.$admin_email.'>';
 
                 $mail = wp_mail($EmailTo, $EmailSubject, $EmailBody, $e_headers);
                 # Reset content-type to avoid conflicts 
                 //remove_filter( 'wp_mail_content_type',  'set_html_content_type' );

                 $approveEmailCnt += 1;
            }
        }
       
    }
}

echo "\n====== SUCCESSFULLY UPDATE STATS OF PENDING TO DENY ======== COUNT : ".$pendingCnt." ======== \n";
echo "\n PENDING Email Sending Finish =============================== COUNT : ".$pendingEmailCnt." =============================== \n";
echo "\n ====== SUCCESSFULLY UPDATE STATS OF APPROVED TO DENY ======== COUNT : ".$approveCnt." ======== \n";
echo "\n APPROVED Email Sending Finish =============================== COUNT : ".$approveEmailCnt." =============================== \n";

$msg = ob_get_contents();
ob_end_clean();
echo $msg;