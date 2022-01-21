<?php

/*$str = 'Afghanistan,AF|,Albania,AL|,Algeria,DZ|,American Samoa,AS|,Andorra,AD|,Angola,AO|,Anguilla,AI|,Antarctica,AQ|,Antigua and Barbuda,AG|,Argentina,AR|,Armenia,AM|,Aruba,AW|,Australia,AU|,Austria,AT|,Azerbaijan,AZ|,Bahamas,BS|,Bahrain,BH|,Baker Island,UM|,Bangladesh,BD|,Barbados,BB|,Belarus,BY|,Belgium,BE|,Belize,BZ|,Benin,BJ|,Bermuda,BM|,Bhutan,BT|,Bolivia,BO|,Bosnia and Herzegowina,BA|,Botswana,BW|,Bouvet Island,BV|,Brazil,BR|,British Indian Ocean Territory,IO|,Brunei Darussalam,BN|,Bulgaria,BG|,Burkina Faso,BF|,Burundi,BI|,Cambodia,KH|,Cameroon,CM|,Canada,CA|,Cape Verde,CV|,Cayman Islands,KY|,Central African Republic,CF|,Chad,TD|,Chile,CL|,China,CN|,Christmas Island,CX|,Cocos (Keeling) Islands,CC|,Colombia,CO|,Comoros,KM|,Congo,CG|,Cook Islands,CK|,Costa Rica,CR|,Croatia,HR|,Cuba,CU|,Cyprus,CY|,Czech Republic,CZ|,Denmark,DK|,Djibouti,DJ|,Dominica,DM|,Dominican Republic,DO|,East Timor,TP|,Ecuador,EC|,Egypt,EG|,El Salvador,SV|,Equatorial Guinea,GQ|,Eritrea,ER|,Estonia,EE|,Ethiopia,ET|,Falkland Islands array(Islas Malvinas),FK|,Faroe Islands,FO|,Fiji,FJ|,Finland,FI|,France,FR|,France, Metropolitan,FX|,French Guiana,GF|,French Polynesia,PF|,French Southern and Antarctic Lands,TF|,Gabon,GA|,Gambia,GM|,Gaza Strip,GZ|,Georgia,GE|,Germany,DE|,Ghana,GH|,Gibraltar,GI|,Greece,GR|,Greenland,GL|,Grenada,GD|,Guadeloupe,GP|,Guam,GU|,Guatemala,GT|,Guernsey,GG|,Guinea,GN|,Guinea-Bissau,GW|,Guyana,GY|,Haiti,HT|,Heard and McDonald Islands,HM|,Holy See Vatican City,VA|,Honduras,HN|,Hong Kong,HK|,Howland Island,UM|,Hungary,HU|,Iceland,IC|,India,IN|,Indonesia,ID|,Iran,IR|,Iraq,IQ|,Ireland,IE|,Israel,IL|,Italy,IT|,Jamaica,JM|,Jan Mayen,SJ|,Japan,JP|,Jarvis Island,UM|,Jersey,JE|,Johnston Atoll,UM|,Jordan,JO|,Kazakhstan,KZ|,Kenya,KE|,Kingman Reef,UM|,Kiribati,KI|,Korea,KR|,Korea, North,KP|,Kuwait,KW|,Kyrgyzstan,KG|,Laos,LA|,Latvia,LV|,Lebanon,LB|,Lesotho,LS|,Liberia,LR|,Libyan Arab Jamahiriya,LY|,Liechtenstein,LI|,Lithuania,LT|,Luxembourg,LU|,Macau,MO|,Macedonia,MK|,Malawi,MW|,Malaysia,MY|,Maldives,MV|,Mali,ML|,Malta,MT|,Marshall Islands,MH|,Martinique,MQ|,Mauritania,MR|,Mauritius,MU|,Mayotte,YT|,Mexico,MX|,Micronesia,FM|,Midway Islands,UM|,Moldova,MD|,Monaco,MC|,Mongolia,MN|,Montserrat,MS|,Morocco,MA|,Mozambique,MZ|,Myanmar,MM|,Namibia,NA|,Nauru,NR|,Navassa Island,UM|,Nepal,NP|,Netherlands,NL|,Netherlands Antilles,AN|,New Caledonia,NC|,New Zealand,NZ|,Nicaragua,NI|,Niger,NE|,Nigeria,NG|,Niue,NU|,Norfolk Island,NF|,Northern Mariana Islands,MP|,Norway,NO|,Oman,OM|,Pakistan,PK|,Palau,PW|,Palestinian Territory,PS|,Palmyra Atoll,UM|,Panama,PA|,Papua New Guinea,PG|,Paraguay,PY|,Peru,PE|,Philippines,PH|,Pitcairn Island,PN|,Poland,PL|,Portugal,PT|,Puerto Rico,PR|,Qatar,QA|,Reunion,RE|,Romania,RO|,Russia,RU|,Rwanda,RW|,Saint Helena,SH|,Saint Kitts and Nevis,KN|,Saint Lucia,LC|,Saint Pierre and Miquelon,PM|,Saint Vincent and the Grenadines,VC|,Samoa,WS|,San Marino,SM|,Sao Tome and Principe,ST|,Saudi Arabia,SA|,Senegal,SN|,Serbia and Montenegro,CS|,Seychelles,SC|,Sierra Leone,SL|,Singapore,SG|,Slovakia,SK|,Slovenia,SI|,Solomon Islands,SB|,Somalia,SO|,South Africa,ZA|,South Georgia,GS|,Spain,ES|,Sri Lanka,LK|,Sudan,SD|,Suriname,SR|,Svalbard and Jan Mayen Islands,SJ|,Swaziland,SZ|,Sweden,SE|,Switzerland,CH|,Syrian,SY|,Taiwan,TW|,Tajikistan,TJ|,Tanzania,TZ|,Thailand,TH|,Timor-Laste,TL|,Togo,TG|,Tokelau,TK|,Tonga,TO|,Trinidad and Tobago,TT|,Tunisia,TN|,Turkey,TR|,Turkmenistan,TM|,Turks and Caicos Islands,TC|,Tuvalu,TV|,Uganda,UG|,United Arab Emirates,AE|,United Kingdom,GB|,United States,US|,United States Minor Outlying Islands,UM|,Uruguay,UY|,Uzbekistan,UZ|,Vanuatu,VU|,Vatican City State array(Holy See),VA|,Venezuela,VE|,Vietnam,VN|,Virgin Islands array(British),VG|,Virgin Islands array(U.S.),VI|,Wake Island,UM|,Wallis and Futuna Islands,WF|,West Bank,WE|,Western Sahara,EH|,Yemen,YE|,Yugoslavia,YU|,Zaire,ZR|,Zambia,ZM|,Zimbabwe,ZW|,Other,00|,Newziland,NW|,Eglande,EL|';

$arr = explode('|,', $str);
foreach($arr as $k => $v){
    $ar = explode(',', trim($v));
    echo "'".$ar[1]."' => '".$ar[0]."',<br/>";
}
 die;*/

class BRStaticData
{
    public static function arrBooksFormate(){
        return array(
            '1'    =>  'Kindle',
            '2'    =>  'Paperback',
            '3'    =>  'Hardcover',
        );
    }
    public static function arrYesNo(){
        return array(
            IS_PUBLISH      =>  'Publish',
            IS_UNPUBLISH    =>  'Unpublish',
        );
    }

    public static function valid_pic_format() {
        return '.jpeg,.png,.gif,.jpg,.ico';
    }
    public static function valid_doc_format() {
        return '.pdf';
    }
    public static function valid_doc_import_format() {
        return '.csv';
    }
    public static function arrValidFileFormat() {
        return array(
            'image/jpeg',
            'application/pdf',
        );
    }
    
    public static function arrValidImportFileFormat() {
        return array(
            'csv',
        );
    }

    public static function arrTierLimit(){
        return array(
            'tier0'    =>  TIER_ZERO_LIMIT,
            'tier1'    =>  TIER_ONE_LIMIT,
            'tier2'    =>  TIER_TWO_LIMIT,
            'tier3'    =>  TIER_THREE_LIMIT,
        );
    }

    public static function arrReqStatus(){
        return array(
            STATUS_PENDING      =>  'Pending',
            STATUS_DENY         =>  'Deny',
            STATUS_APPROVE      =>  'Approve',
            //STATUS_RECEIVED     =>  'Recived',
            STATUS_COMPLETED    =>  'Completed',
            STATUS_REJECTED     =>  'Rejected'

        );
    }
    public static function arrReqStatusAuth(){
        return array(
            STATUS_PENDING      =>  'Pending Approval',
            STATUS_DENY         =>  'Expired', // Expired from system (cronjob)
            STATUS_APPROVE      =>  'Approved',
            //STATUS_RECEIVED     =>  'Recived',
            STATUS_COMPLETED    =>  'Completed',
            STATUS_REJECTED     =>  'Rejected' // When author rejected request
        );
    }
    public static function arrReqStatusReviewer(){
        return array(
            STATUS_PENDING      =>  'Pending Approval',
            STATUS_DENY         =>  'Expired',
            STATUS_APPROVE      =>  'Approved',
            //STATUS_RECEIVED     =>  'Recived',
            STATUS_COMPLETED    =>  'Completed',
            STATUS_REJECTED     =>  'Rejected'
        );
    }
    //Pending Approval, Approved, Completed, Rejected, and Expired
    // TODO : Check all country is included in these array and also check duplication
    public static function arrCountryData()
    {
        return array (
            'AU' => 'Australia',
            'BR' => 'Brazil',
            'CA' => 'Canada',
            'FR' => 'France',
            'DE' => 'Germany',
            'IN' => 'India',
            'IT' => 'Italy',
            'JP' => 'Japan',
            'MX' => 'Mexico',
            'ES' => 'Spain',
            'GB' => 'United Kingdom',
            'US' => 'United States',
        );
        /*return array(
            'AF' => 'Afghanistan',
            'AL' => 'Albania',
            'DZ' => 'Algeria',
            'AS' => 'American Samoa',
            'AD' => 'Andorra',
            'AO' => 'Angola',
            'AI' => 'Anguilla',
            'AQ' => 'Antarctica',
            'AG' => 'Antigua and Barbuda',
            'AR' => 'Argentina',
            'AM' => 'Armenia',
            'AW' => 'Aruba',
            'AU' => 'Australia',
            'AT' => 'Austria',
            'AZ' => 'Azerbaijan',
            'BS' => 'Bahamas',
            'BH' => 'Bahrain',
            'UM' => 'Baker Island',
            'BD' => 'Bangladesh',
            'BB' => 'Barbados',
            'BY' => 'Belarus',
            'BE' => 'Belgium',
            'BZ' => 'Belize',
            'BJ' => 'Benin',
            'BM' => 'Bermuda',
            'BT' => 'Bhutan',
            'BO' => 'Bolivia',
            'BA' => 'Bosnia and Herzegowina',
            'BW' => 'Botswana',
            'BV' => 'Bouvet Island',
            'BR' => 'Brazil',
            'IO' => 'British Indian Ocean Territory',
            'BN' => 'Brunei Darussalam',
            'BG' => 'Bulgaria',
            'BF' => 'Burkina Faso',
            'BI' => 'Burundi',
            'KH' => 'Cambodia',
            'CM' => 'Cameroon',
            'CA' => 'Canada',
            'CV' => 'Cape Verde',
            'KY' => 'Cayman Islands',
            'CF' => 'Central African Republic',
            'TD' => 'Chad',
            'CL' => 'Chile',
            'CN' => 'China',
            'CX' => 'Christmas Island',
            'CC' => 'Cocos (Keeling) Islands',
            'CO' => 'Colombia',
            'KM' => 'Comoros',
            'CG' => 'Congo',
            'CK' => 'Cook Islands',
            'CR' => 'Costa Rica',
            'HR' => 'Croatia',
            'CU' => 'Cuba',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DK' => 'Denmark',
            'DJ' => 'Djibouti',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'TP' => 'East Timor',
            'EC' => 'Ecuador',
            'EG' => 'Egypt',
            'SV' => 'El Salvador',
            'GQ' => 'Equatorial Guinea',
            'ER' => 'Eritrea',
            'EE' => 'Estonia',
            'ET' => 'Ethiopia',
            'FK' => 'Falkland Islands array(Islas Malvinas)',
            'FO' => 'Faroe Islands',
            'FJ' => 'Fiji',
            'FI' => 'Finland',
            'FR' => 'France',
            'FX' => 'France, Metropolitan',
            'GF' => 'French Guiana',
            'PF' => 'French Polynesia',
            'TF' => 'French Southern and Antarctic Lands',
            'GA' => 'Gabon',
            'GM' => 'Gambia',
            'GZ' => 'Gaza Strip',
            'GE' => 'Georgia',
            'DE' => 'Germany',
            'GH' => 'Ghana',
            'GI' => 'Gibraltar',
            'GR' => 'Greece',
            'GL' => 'Greenland',
            'GD' => 'Grenada',
            'GP' => 'Guadeloupe',
            'GU' => 'Guam',
            'GT' => 'Guatemala',
            'GG' => 'Guernsey',
            'GN' => 'Guinea',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HT' => 'Haiti',
            'HM' => 'Heard and McDonald Islands',
            'VA' => 'Holy See Vatican City',
            'HN' => 'Honduras',
            'HK' => 'Hong Kong',
            'UM' => 'Howland Island',
            'HU' => 'Hungary',
            'IC' => 'Iceland',
            'IN' => 'India',
            'ID' => 'Indonesia',
            'IR' => 'Iran',
            'IQ' => 'Iraq',
            'IE' => 'Ireland',
            'IL' => 'Israel',
            'IT' => 'Italy',
            'JM' => 'Jamaica',
            'SJ' => 'Jan Mayen',
            'JP' => 'Japan',
            'UM' => 'Jarvis Island',
            'JE' => 'Jersey',
            'UM' => 'Johnston Atoll',
            'JO' => 'Jordan',
            'KZ' => 'Kazakhstan',
            'KE' => 'Kenya',
            'UM' => 'Kingman Reef',
            'KI' => 'Kiribati',
            'KR' => 'Korea',
            'KP' => 'Korea, North',
            'KW' => 'Kuwait',
            'KG' => 'Kyrgyzstan',
            'LA' => 'Laos',
            'LV' => 'Latvia',
            'LB' => 'Lebanon',
            'LS' => 'Lesotho',
            'LR' => 'Liberia',
            'LY' => 'Libyan Arab Jamahiriya',
            'LI' => 'Liechtenstein',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'MO' => 'Macau',
            'MK' => 'Macedonia',
            'MW' => 'Malawi',
            'MY' => 'Malaysia',
            'MV' => 'Maldives',
            'ML' => 'Mali',
            'MT' => 'Malta',
            'MH' => 'Marshall Islands',
            'MQ' => 'Martinique',
            'MR' => 'Mauritania',
            'MU' => 'Mauritius',
            'YT' => 'Mayotte',
            'MX' => 'Mexico',
            'FM' => 'Micronesia',
            'UM' => 'Midway Islands',
            'MD' => 'Moldova',
            'MC' => 'Monaco',
            'MN' => 'Mongolia',
            'MS' => 'Montserrat',
            'MA' => 'Morocco',
            'MZ' => 'Mozambique',
            'MM' => 'Myanmar',
            'NA' => 'Namibia',
            'NR' => 'Nauru',
            'UM' => 'Navassa Island',
            'NP' => 'Nepal',
            'NL' => 'Netherlands',
            'AN' => 'Netherlands Antilles',
            'NC' => 'New Caledonia',
            'NZ' => 'New Zealand',
            'NI' => 'Nicaragua',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'NU' => 'Niue',
            'NF' => 'Norfolk Island',
            'MP' => 'Northern Mariana Islands',
            'NO' => 'Norway',
            'OM' => 'Oman',
            'PK' => 'Pakistan',
            'PW' => 'Palau',
            'PS' => 'Palestinian Territory',
            'UM' => 'Palmyra Atoll',
            'PA' => 'Panama',
            'PG' => 'Papua New Guinea',
            'PY' => 'Paraguay',
            'PE' => 'Peru',
            'PH' => 'Philippines',
            'PN' => 'Pitcairn Island',
            'PL' => 'Poland',
            'PT' => 'Portugal',
            'PR' => 'Puerto Rico',
            'QA' => 'Qatar',
            'RE' => 'Reunion',
            'RO' => 'Romania',
            'RU' => 'Russia',
            'RW' => 'Rwanda',
            'SH' => 'Saint Helena',
            'KN' => 'Saint Kitts and Nevis',
            'LC' => 'Saint Lucia',
            'PM' => 'Saint Pierre and Miquelon',
            'VC' => 'Saint Vincent and the Grenadines',
            'WS' => 'Samoa',
            'SM' => 'San Marino',
            'ST' => 'Sao Tome and Principe',
            'SA' => 'Saudi Arabia',
            'SN' => 'Senegal',
            'CS' => 'Serbia and Montenegro',
            'SC' => 'Seychelles',
            'SL' => 'Sierra Leone',
            'SG' => 'Singapore',
            'SK' => 'Slovakia',
            'SI' => 'Slovenia',
            'SB' => 'Solomon Islands',
            'SO' => 'Somalia',
            'ZA' => 'South Africa',
            'GS' => 'South Georgia',
            'ES' => 'Spain',
            'LK' => 'Sri Lanka',
            'SD' => 'Sudan',
            'SR' => 'Suriname',
            'SJ' => 'Svalbard and Jan Mayen Islands',
            'SZ' => 'Swaziland',
            'SE' => 'Sweden',
            'CH' => 'Switzerland',
            'SY' => 'Syrian',
            'TW' => 'Taiwan',
            'TJ' => 'Tajikistan',
            'TZ' => 'Tanzania',
            'TH' => 'Thailand',
            'TL' => 'Timor-Laste',
            'TG' => 'Togo',
            'TK' => 'Tokelau',
            'TO' => 'Tonga',
            'TT' => 'Trinidad and Tobago',
            'TN' => 'Tunisia',
            'TR' => 'Turkey',
            'TM' => 'Turkmenistan',
            'TC' => 'Turks and Caicos Islands',
            'TV' => 'Tuvalu',
            'UG' => 'Uganda',
            'AE' => 'United Arab Emirates',
            'GB' => 'United Kingdom',
            'US' => 'United States',
            'UM' => 'United States Minor Outlying Islands',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VU' => 'Vanuatu',
            'VA' => 'Vatican City State array(Holy See)',
            'VE' => 'Venezuela',
            'VN' => 'Vietnam',
            'VG' => 'Virgin Islands array(British)',
            'VI' => 'Virgin Islands array(U.S.)',
            'UM' => 'Wake Island',
            'WF' => 'Wallis and Futuna Islands',
            'WE' => 'West Bank',
            'EH' => 'Western Sahara',
            'YE' => 'Yemen',
            'YU' => 'Yugoslavia',
            'ZR' => 'Zaire',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe',
            '00' => 'Other',
        );*/
    }
}