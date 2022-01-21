<?php
class BookReviewRequest{
    private static $instance ;
    public  $Data = array();
    public $DBC;
    private function __construct(){
        global $wpdb, $arrPhysicalPath, $arrVirtualPath;

        $this->DBC = $wpdb;
        //
        $this->Data['TableName']        =   $wpdb->prefix.'t_book_request_cycle';
        $this->Data['TableNameMaster']  =   $wpdb->prefix.'t_book_catalog';
        $this->Data['TableUsers']        =   $wpdb->prefix.'users';
        $this->Data['Field_Prefix']     =   'brc_';
        $this->Data['F_PrimaryKey']     =   $this->Data['Field_Prefix'].'id';
        $this->Data['F_F_Key']          =   $this->Data['Field_Prefix'].'bcat_id';
        $this->Data['F_PrimaryField']   =   $this->Data['Field_Prefix'].'bcat_author_wpuid';

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

        return $this->DBC->insert($this->Data['TableName'], $POST);
    }

    public function getRequestByParam($POST=false){
        $param = '';
        
        if(isset($POST['status']) && is_array($POST['status']) && count($POST['status']) > 0){
            $param .= ' AND '.$this->Data['Field_Prefix'].'request_status IN ('.implode($POST['status'],',').')';
        }
        else if(isset($POST['status']) && $POST['status'] != ''){
            $param .= ' AND '.$this->Data['Field_Prefix'].'request_status = '.$POST['status'];
        }
        if(isset($POST['reviewer_id']) && is_numeric($POST['reviewer_id']))
            $param .= ' AND '.$this->Data['Field_Prefix'].'reviewer_wpuid = '.$POST['reviewer_id'];

        if(isset($POST['author_id']) && is_numeric($POST['author_id']))
            $param .= ' AND M.'. $this->Data['F_PrimaryField'].' = '.$POST['author_id'];

        $sql = "SELECT M.*,BM.bcat_cover_image,BM.bcat_full_length_book_file, BM.bcat_sample_book_file,BM.bcat_title,BM.bcat_amazon_permalink, UM.user_email, UM.display_name, AU.display_name as author_name, AU.user_email as author_email FROM ".$this->Data['TableName']." AS M 

        LEFT JOIN ".$this->Data['TableNameMaster']." AS BM ON M.".$this->Data['F_F_Key']." = BM.".str_replace($this->Data['Field_Prefix'], '', $this->Data['F_F_Key'])."
        LEFT JOIN ".$this->Data['TableUsers']." AS UM ON M.".$this->Data['Field_Prefix']."reviewer_wpuid = UM.ID
        LEFT JOIN ".$this->Data['TableUsers']." AS AU ON M.".$this->Data['Field_Prefix']."bcat_author_wpuid = AU.ID
        WHERE 1 ".$param;

        //echo '<pre>';print_r($sql);die;
        return $this->DBC->get_results($sql, ARRAY_A);
    }

    public function updateByParam($POST, $PK){
        return $this->DBC->update($this->Data['TableName'], $POST,  array($this->Data['F_PrimaryKey'] => $PK));
    }

    public function getEmailContent($Email_Body,$emailHeader=true, $emailFooter=true){

        if(!isset($Email_Body) || (isset($Email_Body) && $Email_Body == ''))
            return false;
        
        $Email_Header = ''; $Email_Footer = '';

        if($emailHeader == true)
            $Email_Header = $this->getEmailHeader();
        
        if($emailFooter == true)    
            $Email_Footer = $this->getEmailFooter();

        $html = '<!DOCTYPE HTML>
                <meta http-equiv="content-type" content="text/html" />
                <meta name="author" content="" />
                <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;"/>
                
                    <style type="text/css">
                    .stdEmail tr td {
                        font-family:Arial, Helvetica, sans-serif;
                        font-size:9pt;
                        color:#333
                    }
                    .border, .border td, .border th	{ border:1px solid #efefef; padding:0.5em }
                    body{width: 100%; height: 100%; margin:0; padding:0; -webkit-font-smoothing: antialiased;}
                    html{width: 100%; }
                    </style>
                
                    <style type="text/css"> @media only screen and (max-width: 479px){
                            body{width:auto!important;  }
                            table[class=full] {width: 100%!important; clear: both; }
                            table[class=mobile] {width: 100%!important; clear: both; }
                            table[class=fullCenter] {width: 100%!important; text-align: center!important; clear: both; }
                            td[class=fullCenter] {width: 100%!important; text-align: center!important; clear: both; }
                            .td-responsive{width:100%;display: block}
                        }
                    </style>
                    <style type="text/css"> @media only screen and (max-width: 640px){
                            body{width:auto!important;}
                            table[class=full] {width: 100%!important; clear: both; }
                            table[class=mobile] {width: 100%!important; clear: both; }
                            td[class=mobile] {width: 100%!important; clear: both; }
                            table[class=fullCenter] {width:100%!important; text-align: center!important; clear: both; }
                            td[class=fullCenter] {width: 100%!important; text-align: center!important; clear: both; }
                        } </style>
                    <style type="text/css">@media screen and (max-width: 1024px) and (min-width: 768px) {
                            body{width:auto!important; }
                            table[class=full] {width: 100%!important; clear: both; }
                            td[class=mobile] {width: 100%!important; clear: both; }
                            table[class=fullCenter] {width: 100%!important; text-align: center!important; clear: both; }
                            td[class=fullCenter] {width: 100%!important; text-align: center!important; clear: both; }
                        } </style>
                    <style type="text/css">@media screen and (max-width: 1366px) and (min-width: 1024px) {
                            body{width:auto!important;  }
                            table[class=full] {width: 100%!important; clear: both; }
                            td[class=mobile] {width: 100%!important; clear: both; }
                            table[class=fullCenter] {width: 100%!important; text-align: center!important; clear: both; }
                            td[class=fullCenter] {width: 100%!important; text-align: center!important; clear: both; }
                        } </style>
                    <style type="text/css">@media screen and (max-width: 1024px) and (min-width: 1024px) {
                            body{width:auto!important;  }
                            table[class=full] {width: 100%!important; clear: both; }
                            td[class=mobile] {width: 100%!important;  clear: both; }
                            table[class=fullCenter] {width: 100%!important; text-align: center!important; clear: both; }
                            td[class=fullCenter] {width: 100%!important; text-align: center!important; clear: both; }
                        } </style>
                
                '.$Email_Header.'
                <table class="full" cellpadding="0" cellspacing="0" width="100%" height="100%" align="center" border="0">
                    <tr>
                        <td>
                            '.$Email_Body.'
                            <br />&nbsp;
                        </td>
                    </tr>
                </table>
                '.$Email_Footer;

       return $html;
        
    }

    public function getPendingStatusEmail($arrRequest){

        if(!is_array($arrRequest) || (is_array($arrRequest) && count($arrRequest) < 0))
            return false;

        $date = date_create($arrRequest[$this->Data['Field_Prefix'].'pending_adt']);
        $r_date = date_format($date, "d M Y H:i A");

        $html = '<table width="50%" border="0" cellspacing="5" cellpadding="0" align="center">
                    <tr>
                        <td colspan="2" style="color:#000; text-align:left; font-size:1em; font-weight:700">Dear '.$arrRequest['author_name'].', </td>
                    </tr>
                    <tr>
                        <td colspan="2">Alert for pending book request to approve/deny</td>
                    </tr>
                    <tr>
                        <td width="30%">&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    
                    <tr>
                        <td align="right" valign="top" style="padding:5px; background-color:#eaeaea"><strong>Book Title :</strong></td>
                        <td>'.$arrRequest['bcat_title'].'</td>
                    </tr>
                    <tr>
                        <td align="right" valign="top" style="padding:5px; background-color:#eaeaea"><strong>Reviewer Name :</strong></td>
                        <td>'.$arrRequest['display_name'].'</td>
                    </tr>
                    <tr>
                        <td align="right" valign="top" style="padding:5px; background-color:#eaeaea"><strong>Status :</strong></td>
                        <td>'.BRStaticData::arrReqStatus()[$arrRequest[$this->Data['Field_Prefix'].'request_status']].'</td>
                    </tr>
                    <tr>
                        <td align="right" valign="top" style="padding:5px; background-color:#eaeaea"><strong>Pending Date :</strong></td>
                        <td>'.$r_date.'</td>
                    </tr>
                </table>';

        return $html;
    }

    public function getApproveStatusEmail($arrRequest){

        if(!is_array($arrRequest) || (is_array($arrRequest) && count($arrRequest) < 0))
            return false;
        
        $date = date_create($arrRequest[$this->Data['Field_Prefix'].'approved_adt']);

        $a_date = date_format($date, "d M Y H:i A");

        $html = '<table width="50%" border="0" cellspacing="5" cellpadding="0" align="center">
                    <tr>
                        <td colspan="2" style="color:#000; text-align:left; font-size:1em; font-weight:700">Dear '.$arrRequest['display_name'].',</td>
                    </tr>
                    <tr>
                        <td colspan="2">Alert for pending review to complete</td>
                    </tr>
                    <tr>
                        <td width="30%">&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    
                    <tr>
                        <td align="right" valign="top" style="padding:5px; background-color:#eaeaea"><strong>Book Title :</strong></td>
                        <td>'.$arrRequest['bcat_title'].'</td>
                    </tr>
                    <tr>
                        <td align="right" valign="top" style="padding:5px; background-color:#eaeaea"><strong>Status :</strong></td>
                        <td>'.BRStaticData::arrReqStatus()[$arrRequest[$this->Data['Field_Prefix'].'request_status']].'</td>
                    </tr>
                    <tr>
                        <td align="right" valign="top" style="padding:5px; background-color:#eaeaea"><strong>Approved Date :</strong></td>
                        <td>'.$a_date.'</td>
                    </tr>
                </table>';

        return $html;
    }

    public function getEmailHeader(){
        $site_title = get_bloginfo();
        $site_logo = get_custom_logo();

        /*if($site_logo != '')
            $logo = '<img src="'.home_url('/').'/wp-content/plugins/book-review/upload/04-Horizontal-on-Darkv2-e1561711404473.png" style="height: 45px;"/>';
        else*/
            $logo = '<h1>'.ucwords($site_title).'</h1>';

        $html = '
        <table align="center" width="100%" class="full">
            <tr style="">
                <td class="logo250" style="text-align: center; line-height: 1px;width: 100%;padding-top: 15px;margin-top: 15px;">
                    '.$logo.'
                </td>
            </tr>
        </table>';
        return $html;
    }

    public function getEmailFooter(){
        $site_title = get_bloginfo();
        $html = '
        <table class="mobile" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #DBDBDB">
            <tbody>
            <tr>
                <td>
                    <table class="mobile" style="width: 50%; padding: 10px;" cellspacing="0" cellpadding="0" border="0" align="center">
                        <tbody>
                            <tr>
                                <td style="padding: 15px;">
                                    <font face="Arial" size="2" color="#a3a3a3">
                                        To know more about us, please go here: <a style="color:#c69b2d;" href="'.home_url('/').'">'.ucwords($site_title).'</a>
                                    </font>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>';
        return $html;
    }
    
}