<?php
IncludeModuleLangFile( __FILE__ );

Class CIntisSUSTable
{
    function GetRemindTemplate( $site_id )
    {
        global $DB;

        $query = $DB->Query( 'SELECT * FROM intis_sus_remind_pay_' . $site_id . ' LIMIT 1' );
        $result = $query->Fetch();

        if( $result[ 'template' ] ) {
            return array(
                'TEXT'   => $result[ 'template' ],
                'ACTIVE' => $result[ 'active' ],
                'COPY'   => $result[ 'copy' ]
            );
        } else {
            return false;
        }
    }

    function UpdateRemind( $site_id, $template, $active, $copy )
    {
        global $DB;

        if( $active == 'on' ) {
            $active = 1;
        } else {
            $active = 0;
        }

        if( $copy == 'on' ) {
            $copy = 1;
        } else {
            $copy = 0;
        }

        $query = $DB->Query( 'SELECT `template` FROM intis_sus_remind_pay_' . $site_id . ' LIMIT 1' );
        $result = $query->Fetch();

        if( $result[ 'template' ] ) {
            try {
                $DB->Query( 'UPDATE intis_sus_remind_pay_' . $site_id . ' SET `template`="' . addslashes( $template ) . '", `active`="' . $active . '", `copy`="' . $copy . '"' );
            } catch( \Exception $e ) {

            }
        } else {
            try {
                $DB->Query( 'INSERT INTO intis_sus_remind_pay_' . $site_id . ' (`template`, `active`, `copy`) VALUES ("' . addslashes( $template ) . '", "' . $active . '", "' . $copy . '")' );
            } catch( \Exception $e ) {

            }
        }
    }

    function AddNewRule( $site_id, $event_id, $template, $active, $first_rule, $first_rule_value,
                         $first_rule_period, $first_rule_period_rule, $third_rule, $third_rule_value, $third_rule_discount_id )
    {
        global $DB;

        $site_id = addslashes( $site_id );
        $event_id = addslashes( $event_id );
        $template = addslashes( $template );
        $active = addslashes( $active );
        $first_rule = addslashes( $first_rule );
        $first_rule_value = addslashes( $first_rule_value );
        $first_rule_period = addslashes( $first_rule_period );
        $first_rule_period_rule = addslashes( $first_rule_period_rule );
        $third_rule = addslashes( $third_rule );
        $third_rule_value = addslashes( $third_rule_value );
        $third_rule_discount_id = addslashes( $third_rule_discount_id );

        $rsSites = CSite::GetByID( $site_id );
        $arSite = $rsSites->Fetch();

        switch( $arSite[ 'CHARSET' ] ) {
            case "cp1251":
            case "windows-1251":
                $DB->Query( 'SET NAMES CP1251' );
                break;
            default:
        }

        $DB->Query(
            'INSERT INTO `intis_additional_rules`
            (site_id, event_id, template, active, first_rule, first_rule_value, first_rule_period, first_rule_period_rule, third_rule, third_rule_value, third_rule_discount_id)
            VALUES
            ("' . $site_id . '", "' . $event_id . '", "' . $template . '", "' . $active . '", "' . $first_rule . '", "' . $first_rule_value . '", "' . $first_rule_period . '",
            "' . $first_rule_period_rule . '", "' . $third_rule . '", "' . $third_rule_value . '", "' . $third_rule_discount_id . '")'
        );
    }

    function GetActiveRules( $site_id )
    {
        global $DB;

        $return = false;
        $rsSites = CSite::GetByID( $site_id );
        $arSite = $rsSites->Fetch();

        switch( $arSite[ 'CHARSET' ] ) {
            case "cp1251":
            case "windows-1251":
                $DB->Query( 'SET NAMES CP1251' );
                break;
            default:
        }

        $query = $DB->Query( 'SELECT * FROM intis_additional_rules WHERE site_id="' . $site_id . '" AND active="1"' );
        while( $row = $query->Fetch() ) {
            $return[ ] = array(
                "ID"                     => $row[ 'id' ],
                "SITE"                   => $row[ 'site_id' ],
                "EVENT"                  => $row[ 'event_id' ],
                "TEMPLATE"               => $row[ 'template' ],
                "ACTIVE"                 => $row[ 'active' ],
                "FIRST_RULE"             => $row[ 'first_rule' ],
                "FIRST_RULE_VALUE"       => $row[ 'first_rule_value' ],
                "FIRST_RULE_PERIOD"      => $row[ 'first_rule_period' ],
                "FIRST_RULE_PERIOD_RULE" => $row[ 'first_rule_period_rule' ],
                "THIRD_RULE"             => $row[ 'third_rule' ],
                "THIRD_RULE_VALUE"       => $row[ 'third_rule_value' ],
                "THIRD_RULE_DISCOUNT_ID" => $row[ 'third_rule_discount_id' ],
            );
        }

        return $return;
    }

    function GetRules( $charset )
    {
        global $DB;

        $return = array();

        switch( $charset ) {
            case "cp1251":
            case "windows-1251":
                $DB->Query( 'SET NAMES CP1251' );
                break;
            default:
        }

        $query = $DB->Query( 'SELECT * FROM intis_additional_rules ORDER BY site_id, id ASC' );
        while( $row = $query->Fetch() ) {
            $return[ ] = array(
                "ID"                     => $row[ 'id' ],
                "SITE"                   => $row[ 'site_id' ],
                "EVENT"                  => $row[ 'event_id' ],
                "TEMPLATE"               => $row[ 'template' ],
                "ACTIVE"                 => $row[ 'active' ],
                "FIRST_RULE"             => $row[ 'first_rule' ],
                "FIRST_RULE_VALUE"       => $row[ 'first_rule_value' ],
                "FIRST_RULE_PERIOD"      => $row[ 'first_rule_period' ],
                "FIRST_RULE_PERIOD_RULE" => $row[ 'first_rule_period_rule' ],
                "THIRD_RULE"             => $row[ 'third_rule' ],
                "THIRD_RULE_VALUE"       => $row[ 'third_rule_value' ],
                "THIRD_RULE_DISCOUNT_ID" => $row[ 'third_rule_discount_id' ],
            );
        }

        return $return;
    }

    function OnOffRule( $id, $status, $sid )
    {
        global $DB;

        $rsSites = CSite::GetByID( $sid );
        $arSite = $rsSites->Fetch();

        switch( $arSite[ 'CHARSET' ] ) {
            case "cp1251":
            case "windows-1251":
                $DB->Query( 'SET NAMES CP1251' );
                break;
            default:
        }

        try {
            $DB->Query( 'UPDATE intis_additional_rules SET active="' . $status . '" WHERE id="' . $id . '"' );
        } catch( \Exception $e ) {

        }
    }

    function DeleteRule( $id )
    {
        global $DB;
        try {
            $DB->Query( 'DELETE FROM intis_additional_rules WHERE id="' . $id . '"' );
        } catch( \Exception $e ) {

        }
    }

    public function DeclensionWords( $num, $words )
    {
        $num = $num % 100;

        if( $num > 19 ) {
            $num = $num % 10;
        }

        switch( $num ) {

            case 1: {
                return ( $words[ 0 ] );
            }

            case 2:
            case 3:
            case 4: {
                return ( $words[ 1 ] );
            }

            default: {
            return ( $words[ 2 ] );
            }

        }
    }

    function Send( $phone, $text, $time = false )
    {
        $secretKey = COption::GetOptionString( "intis.senduserssms", "TOKEN_PARAM", "" );

        $originator = COption::GetOptionString( "intis.senduserssms", "ORIGINATOR_NAME_ID", "" );

        if( $time ) {
            $timeSend = ' time_send="' . $time . '"';
        } else {
            $timeSend = false;
        }

        $xml = '<?xml version="1.0" encoding="utf-8" ?>
            <request>
                <message type="sms">
	                <sender>' . $originator . '</sender>
	                <text>' . $text . '</text>
	                <abonent phone="' . $phone . '"' . $timeSend . ' />
                </message>
                <security>
                    <token value="' . $secretKey . '" />
                </security>
            </request>';
        $urltopost = CIntisSendSMS::GetProtocol() . 'xml.sms16.ru/xml/';
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-type: text/xml; charset=utf-8' ) );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_CRLF, true );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $xml );
        curl_setopt( $ch, CURLOPT_URL, $urltopost );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true );
        $result = curl_exec( $ch );
        curl_close( $ch );
    }

    function GetMainTemplateById( $sid, $id )
    {
        global $DB;

        $rsSites = CSite::GetByID( $sid );
        $arSite = $rsSites->Fetch();

        switch( $arSite[ 'CHARSET' ] ) {
            case "cp1251":
            case "windows-1251":
                $DB->Query( 'SET NAMES CP1251' );
                break;
            default:
        }

        $query = $DB->Query( 'SELECT template, active, copy FROM intis_sus_main_template_' . $sid . ' WHERE id="' . $id . '"' );
        $result = $query->Fetch();

        return array(
            "TEXT"   => $result[ 'template' ],
            "ACTIVE" => $result[ 'active' ],
            "COPY"   => $result[ 'copy' ]
        );
    }

    function GetStatusTemplateById( $sid, $id )
    {
        global $DB;

        $rsSites = CSite::GetByID( $sid );
        $arSite = $rsSites->Fetch();

        switch( $arSite[ 'CHARSET' ] ) {
            case "cp1251":
            case "windows-1251":
                $DB->Query( 'SET NAMES CP1251' );
                break;
            default:
        }

        $query = $DB->Query( 'SELECT template, active, copy FROM intis_sus_status_template_' . $sid . ' WHERE id="' . $id . '"' );
        $result = $query->Fetch();

        return array(
            "TEXT"   => $result[ 'template' ],
            "ACTIVE" => $result[ 'active' ],
            "COPY"   => $result[ 'copy' ]
        );
    }

    function GetMainEventList( $prefix )
    {
        global $DB;

        $rsSites = CSite::GetByID( $prefix );
        $arSite = $rsSites->Fetch();

        switch( $arSite[ 'CHARSET' ] ) {
            case "cp1251":
            case "windows-1251":
                $DB->Query( 'SET NAMES CP1251' );
                break;
            default:
        }

        $query = $DB->Query( 'SELECT id, template, active, copy FROM intis_sus_main_template_' . $prefix );

        while( $row = $query->Fetch() ) {
            $arResult[ 'ITEMS' ][ ] = array(
                "ID"       => $row[ 'id' ],
                "TEMPLATE" => $row[ 'template' ],
                "ACTIVE"   => $row[ 'active' ],
                "COPY"     => $row[ 'copy' ]
            );
        }

        return $arResult[ 'ITEMS' ];
    }

    function UpdateMainEvent( $sid, $prefix, $text, $active, $copy )
    {
        global $DB;

        $text = addslashes( $text );

        if( $active ) {
            $act = 1;
        } else {
            $act = 0;
        }

        if( $copy ) {
            $cop = 1;
        } else {
            $cop = 0;
        }

        $rsSites = CSite::GetByID( $sid );
        $arSite = $rsSites->Fetch();

        switch( $arSite[ 'CHARSET' ] ) {
            case "cp1251":
            case "windows-1251":
                $DB->Query( 'SET NAMES CP1251' );
                break;
            default:
        }

        try {
            $DB->Query( 'UPDATE intis_sus_main_template_' . $sid . ' SET template="' . $text . '", active="' . $act . '", copy="' . $cop . '" WHERE id="' . $prefix . '"' );
        } catch( \Exception $e ) {

        }
    }

    function UpdateStatusEvent( $sid, $prefix, $text, $active, $copy )
    {
        global $DB;

        $text = addslashes( $text );

        if( $active ) {
            $act = 1;
        } else {
            $act = 0;
        }

        if( $copy ) {
            $cop = 1;
        } else {
            $cop = 0;
        }

        $rsSites = CSite::GetByID( $sid );
        $arSite = $rsSites->Fetch();

        switch( $arSite[ 'CHARSET' ] ) {
            case "cp1251":
            case "windows-1251":
                $DB->Query( 'SET NAMES CP1251' );
                break;
            default:
        }

        try {
            $DB->Query( 'UPDATE intis_sus_status_template_' . $sid . ' SET template="' . $text . '", active="' . $act . '", copy="' . $cop . '" WHERE id="' . $prefix . '"' );
        } catch( \Exception $e ) {

        }
    }

    function FindStatus( $sid, $id )
    {
        global $DB;

        $rsSites = CSite::GetByID( $sid );
        $arSite = $rsSites->Fetch();

        switch( $arSite[ 'CHARSET' ] ) {
            case "cp1251":
            case "windows-1251":
                $DB->Query( 'SET NAMES CP1251' );
                break;
            default:
        }

        $query = $DB->Query( 'SELECT id, template, active, copy FROM intis_sus_status_template_' . $sid . ' WHERE id="' . $id . '"' );
        $result = $query->Fetch();

        if( intval( $result[ 'id' ] ) ) {
            $arResult[ 'ITEM' ] = array(
                "ID"       => $result[ 'id' ],
                "TEMPLATE" => $result[ 'template' ],
                "ACTIVE"   => $result[ 'active' ],
                "COPY"     => $result[ 'copy' ]
            );

            return $arResult[ 'ITEM' ];
        } else {
            return false;
        }
    }

    function FindTable( $prefix )
    {
        global $DB;

        $rsSites = CSite::GetByID( $prefix );
        $arSite = $rsSites->Fetch();

        switch( $arSite[ 'CHARSET' ] ) {
            case "cp1251":
            case "windows-1251":
                $DB->Query( 'SET NAMES CP1251' );
                break;
            default:
        }

        $query = $DB->Query( "SHOW TABLES LIKE 'intis_sus_status_template_" . $prefix . "'" );
        if( intval( $query->SelectedRowsCount() ) ) {
            return true;
        } else {
            return false;
        }
    }

    function AddStatus( $sid, $id, $text, $active )
    {
        global $DB;

        $text = addslashes( $text );

        if( $active ) {
            $act = 1;
        } else {
            $act = 0;
        }

        $rsSites = CSite::GetByID( $sid );
        $arSite = $rsSites->Fetch();

        switch( $arSite[ 'CHARSET' ] ) {
            case "cp1251":
            case "windows-1251":
                $DB->Query( 'SET NAMES CP1251' );
                break;
            default:
        }

        $DB->Query( 'INSERT INTO intis_sus_status_template_' . $sid . '(id, template, active) VALUES ("' . $id . '", "' . $text . '", "' . $act . '")' );
    }

    function AddTable( $prefix )
    {
        global $DB;

        if( !$this->FindTable( $prefix ) ) {
            $rsSites = CSite::GetByID( $prefix );
            $arSite = $rsSites->Fetch();

            $site_id = $arSite[ 'ID' ];
            $by = "id";
            $order = "desc";

            $rsSites = CSite::GetByID( $sid );
            $arSite = $rsSites->Fetch();

            switch( $arSite[ 'CHARSET' ] ) {
                case "cp1251":
                case "windows-1251":
                    $coll = "cp1251";
                    $DB->Query( 'SET NAMES CP1251' );
                    break;
                default:
                    $coll = "utf8";
            }

            $DB->Query(
                'CREATE TABLE IF NOT EXISTS intis_sus_status_template_' . $site_id . ' (
                  `id` varchar(2) CHARACTER SET ' . $coll . ' NOT NULL,
                  `template` text CHARACTER SET ' . $coll . ' NOT NULL,
                  `active` enum("0","1") NOT NULL DEFAULT "0",
                  UNIQUE KEY `id` (`id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=' . $coll
            );

            $DB->Query(
                'CREATE TABLE IF NOT EXISTS intis_sus_main_template_' . $site_id . ' (
                  `id` varchar(20) CHARACTER SET ' . $coll . ' NOT NULL,
                  `template` text CHARACTER SET ' . $coll . ' NOT NULL,
                  `active` enum("0","1") NOT NULL DEFAULT "1",
                  UNIQUE KEY `id` (`id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=' . $coll
            );

            $DB->Query(
                'INSERT INTO intis_sus_main_template_' . $site_id . '(id, template, active)
                VALUES
                ("NEW", "' . GetMessage( 'INTIS_SEND_USER_SMS_INCLUDE_TEMPLATE_ORDER_ADD_DEFAULT' ) . '", "1"),
                ("CANCEL", "' . GetMessage( 'INTIS_SEND_USER_SMS_INCLUDE_TEMPLATE_CANCEL_DEFAULT' ) . '", "1"),
                ("PAY", "' . GetMessage( 'INTIS_SEND_USER_SMS_INCLUDE_TEMPLATE_PAY_DEFAULT' ) . '", "1"),
                ("DELIVER", "' . GetMessage( 'INTIS_SEND_USER_SMS_INCLUDE_TEMPLATE_DELIVERY_DEFAULT' ) . '", "1")'
            );

            $arFilter[ "LID" ] = $arSite[ 'LANGUAGE_ID' ];
            $getStatusList = CSaleStatus::GetList(
                array( $by => $order ),
                $arFilter,
                false,
                false,
                array( "ID", "SORT", "LID", "NAME", "DESCRIPTION", $by )
            );
            while( $arStatusResult = $getStatusList->Fetch() ) {
                try {
                    $DB->Query( 'INSERT INTO intis_sus_status_template_' . $site_id . '(id, template, active) VALUES ("' . $arStatusResult[ 'ID' ] . '", "' . GetMessage( 'INTIS_SEND_USER_SMS_INCLUDE_TEMPLATE_STATUS_DEFAULT' ) . '", "0")' );
                } catch( \Exception $e ) {

                }
            }
        }
    }

    function CallingEvent( $ID, $arOrder, $prefix, $type, $ev )
    {
        global $DB;
        $class = new CIntisSUSTable();

        $rsSites = CSite::GetByID( $arOrder[ 'LID' ] );
        $arSite = $rsSites->Fetch();

        if( $ev == "remind" ) {
            $template = $class->GetRemindTemplate( $arOrder[ 'LID' ] );
        }

        if( $ev == "main" ) {
            $template = $class->GetMainTemplateById( $arOrder[ 'LID' ], $type );
        }

        if( $ev == "status" ) {
            $template = $class->GetStatusTemplateById( $arOrder[ 'LID' ], $type );
        }

        $dbItemsInOrder = CSaleBasket::GetList( array( "ID" => "ASC" ), array( "ORDER_ID" => $ID ) );

        $q = 0;
        while( $dbItemsInOrder2 = $dbItemsInOrder->Fetch() ) {
            $q++;
        }

        $words1 = array( GetMessage( 'WORD1' ), GetMessage( 'WORD2' ), GetMessage( 'WORD3' ) );
        $words2 = array( GetMessage( 'WORD4' ), GetMessage( 'WORD5' ), GetMessage( 'WORD6' ) );
        $words3 = array( GetMessage( 'WORD7' ), GetMessage( 'WORD8' ), GetMessage( 'WORD9' ) );

        $words1 = $class->DeclensionWords( $q, $words1 );
        $words2 = $class->DeclensionWords( $q, $words2 );
        $words3 = $class->DeclensionWords( $q, $words3 );

        if( $template[ 'ACTIVE' ] == 1 ) {
            $store = CCatalogStore::GetList(
                array( "ID" => "ASC" ),
                array(),
                false,
                false,
                array( "ID", "TITLE", "PHONE", "SCHEDULE", "ADDRESS", "DESCRIPTION" )
            );

            $p1 = 'PERSONAL';
            $p2 = 'WORK';
            $p3 = 'UF_';

            $arPaySys = CSalePaySystem::GetByID( $arOrder[ 'PAY_SYSTEM_ID' ], $arOrder[ 'PERSON_TYPE_ID' ] );
            $arDelivery = CSaleDelivery::GetByID( $arOrder[ 'DELIVERY_ID' ] );
            $arStatus = CSaleStatus::GetByID( $arOrder[ "STATUS_ID" ] );
            $rsUser = CUser::GetByID( $arOrder[ 'USER_ID' ] );
            $arUser = $rsUser->Fetch();

            $db_props2 = CSaleOrderPropsValue::GetOrderProps( $ID );

            $db_ptype = CSalePersonType::GetList( Array( "SORT" => "ASC" ), Array( "LID" => $arSite[ 'ID' ] ) );
            while( $ptype = $db_ptype->Fetch() ) {
                $db_propsGroup = CSaleOrderPropsGroup::GetList(
                    array( "SORT" => "ASC" ),
                    array( "PERSON_TYPE_ID" => $ptype[ "ID" ] ),
                    false,
                    false,
                    array()
                );

                while( $propsGroup = $db_propsGroup->Fetch() ) {
                    $db_props = CSaleOrderProps::GetList(
                        array( "ID" => "ASC" ),
                        array(
                            "PERSON_TYPE_ID" => $ptype[ "ID" ],
                            "PROPS_GROUP_ID" => $propsGroup[ "ID" ],
                            "USER_PROPS"     => "Y"
                        ),
                        false,
                        false,
                        array()
                    );

                    while( $props = $db_props->Fetch() ) {
                        $x = 0;

                        if( $props[ 'PERSON_TYPE_ID' ] != $arOrder[ 'PERSON_TYPE_ID' ] ) {
                            $getTextSmsReplace2[ '#PR' . $props[ 'CODE' ] . $props[ 'ID' ] . '#' ] = "";
                        }

                        while( $arProps = $db_props2->Fetch() ) {
                            if( !$arProps[ 'VALUE' ] ) {
                                $ii[ 'PROP_EXCEPTION' . $arProps[ 'ORDER_PROPS_ID' ] ] = $arProps[ 'ORDER_PROPS_ID' ];
                                $getTextSmsReplace2[ '#PR' . $props[ 'CODE' ] . $arProps[ 'ORDER_PROPS_ID' ] . '#' ] = "";
                            } else {
                                if( $arProps[ 'TYPE' ] == "LOCATION" ) {
                                    $loc = CSaleLocation::GetByID( $arProps[ 'VALUE' ] );
                                    $getTextSmsReplace2[ '#PR' . 'LOCATIONCOUNTRY' . $arProps[ 'ORDER_PROPS_ID' ] . '#' ] = $loc[ 'COUNTRY_NAME' ];
                                    $getTextSmsReplace2[ '#PR' . 'LOCATIONREGION' . $arProps[ 'ORDER_PROPS_ID' ] . '#' ] = $loc[ 'REGION_NAME' ];
                                    $getTextSmsReplace2[ '#PR' . 'LOCATIONCITY' . $arProps[ 'ORDER_PROPS_ID' ] . '#' ] = $loc[ 'CITY_NAME' ];
                                } else {
                                    $ii[ 'PROP_EXCEPTION' . $arProps[ 'ORDER_PROPS_ID' ] ] = $arProps[ 'ORDER_PROPS_ID' ];
                                    $getTextSmsReplace2[ '#PR' . $arProps[ 'CODE' ] . $arProps[ 'ORDER_PROPS_ID' ] . '#' ] = $arProps[ 'VALUE' ];
                                }
                            }
                        }

                    }
                }
            }

            foreach( $arUser as $k => $v ) {
                $pos = strpos( $k, $p1 );
                $pos2 = strpos( $k, $p2 );
                $pos3 = strpos( $k, $p3 );

                if( $pos !== false || $pos2 !== false || $pos3 !== false ) {
                    if( $v ) {
                        $getTextSmsReplace2[ '#USERPROP' . $k . '#' ] = $v;
                    } else {
                        $getTextSmsReplace2[ '#USERPROP' . $k . '#' ] = "";
                    }
                }
            }

            while( $store_item = $store->Fetch() ) {
                $getTextSmsReplace2[ '#STORE' . $store_item[ 'ID' ] . 'TITLE#' ] = $store_item[ 'TITLE' ];
                $getTextSmsReplace2[ '#STORE' . $store_item[ 'ID' ] . 'PHONE#' ] = $store_item[ 'PHONE' ];
                $getTextSmsReplace2[ '#STORE' . $store_item[ 'ID' ] . 'SCHEDULE#' ] = $store_item[ 'SCHEDULE' ];
                $getTextSmsReplace2[ '#STORE' . $store_item[ 'ID' ] . 'ADDRESS#' ] = $store_item[ 'ADDRESS' ];
                $getTextSmsReplace2[ '#STORE' . $store_item[ 'ID' ] . 'DESCRIPTION#' ] = $store_item[ 'DESCRIPTION' ];
            }

            $db_props = CSaleOrderProps::GetList(
                array( "ID" => "ASC" ),
                array(
                    "PERSON_TYPE_ID" => $arOrder[ 'PERSON_TYPE_ID' ],
                    "USER_PROPS"     => "Y",
                    "!ID"            => $ii
                ),
                false,
                false,
                array()
            );
            while( $props = $db_props->Fetch() ) {
                $getTextSmsReplace2[ '#PR' . $props[ 'CODE' ] . $props[ 'ID' ] . '#' ] = "";
            }

            $getTextSmsReplace = array(
                '#ORID#'                 => $arOrder[ 'ACCOUNT_NUMBER' ],
                '#ORPRICE#'              => $arOrder[ 'PRICE' ],
                '#ORCURRENCY#'           => $arOrder[ 'CURRENCY' ],
                '#ORDISCOUNTVALUE#'      => $arOrder[ 'DISCOUNT_VALUE' ],
                '#ORUSERID#'             => $arUser[ 'LOGIN' ],
                '#ORPAYSYSTEMID#'        => $arPaySys[ "NAME" ],
                '#ORDELIVERYID#'         => $arDelivery[ "NAME" ],
                '#ORDELIVERYDOCNUM#'     => $arOrder[ "DELIVERY_DOC_NUM" ],
                '#ORDELIVERYDOCDATE#'    => $arOrder[ "DELIVERY_DOC_DATE" ],
                '#ORDPAYNUM#'            => $arOrder[ "PAY_VOUCHER_NUM" ],
                '#ORDPAYDOCNUM#'         => $arOrder[ "PAY_VOUCHER_DATE" ],
                '#ORTRACKINGNUMBER#'     => $arOrder[ "TRACKING_NUMBER" ],
                '#ORSTATUSID#'           => $arStatus[ "NAME" ],
                '#ORDATESTATUS#'         => $arOrder[ "DATE_STATUS" ],
                '#ORCOSTDELIVERY#'       => $arOrder[ "PRICE_DELIVERY" ],
                '#ORDATEALLOWDELIVERY#'  => $arOrder[ "DATE_ALLOW_DELIVERY" ],
                '#ORQWITEMS#'            => $q,
                '#ORQWONE#'              => $words1,
                '#ORQWTWO#'              => $words2,
                '#ORQWTREE#'             => $words3,
                '#ORQCANCELDESCRIPTION#' => $arOrder[ "REASON_CANCELED" ],
            );

            $merge = array_merge( $getTextSmsReplace, $getTextSmsReplace2 );
            $text_sms = preg_replace( array_keys( $merge ), array_values( $merge ), $template[ 'TEXT' ] );
            $text_sms = str_replace( "#", "", $text_sms );

            switch( $arSite[ 'CHARSET' ] ) {
                case "cp1251":
                case "windows-1251":
                    $text_sms = iconv( "cp1251", "UTF-8", $text_sms );
                    if( !$text_sms )
                        $text_sms = iconv( "windows-1251", "UTF-8", $text_sms );
                    if( !$text_sms )
                        $text_sms = iconv( "cp1251", "UTF8", $text_sms );
                    if( !$text_sms )
                        $text_sms = iconv( "windows-1251", "UTF8", $text_sms );
                    break;
                default:
            }

            $db_props = CSaleOrderPropsValue::GetOrderProps( $ID );

            while( $arProps = $db_props->Fetch() ) {

                if( $arProps[ "CODE" ] == COption::GetOptionString( "intis.senduserssms", "ORDER_PHONE_ID", "" ) ) {
                    $phone = CIntisPhone::Parse( $arProps[ "VALUE" ] );
                    break;
                }

            }

            $class->Send( $phone, $text_sms );

            if( $template[ 'COPY' ] == 1 && COption::GetOptionString( "intis.senduserssms", "ADMIN_PHONE_ID", "" ) ) {
                $adminPhone = CIntisPhone::Parse( COption::GetOptionString( "intis.senduserssms", "ADMIN_PHONE_ID", "" ) );
                $class->Send( $adminPhone, $text_sms );
            }

            $rules = $class->GetActiveRules( $arOrder[ 'LID' ] );
            if( $rules ) {
                foreach( $rules as $rule ) {
                    $event = $rule[ 'EVENT' ];

                    $getAcc = CSaleUserAccount::GetByUserID( $arOrder[ 'USER_ID' ], $arOrder[ 'CURRENCY' ] );

                    switch( $rule[ 'THIRD_RULE' ] ) {
                        case "add_money":
                            $amount_sum = $rule[ 'THIRD_RULE_VALUE' ];
                            break;
                        case "add_percent":
                            $amount_sum = round( ( $arOrder[ 'PRICE' ] * $rule[ 'THIRD_RULE_VALUE' ] ) / 100, 2 );
                            break;
                    }

                    $curBudget = round( $getAcc[ 'CURRENT_BUDGET' ], 2 );
                    $curBudget2 = round( $curBudget + $amount_sum, 2 );

                    if( substr( $event, 0, 3 ) == $prefix && substr( $event, 3 ) == $type ) {
                        $COUPON = CatalogGenerateCoupon();

                        $templateReplace = array(
                            '#ORID#'     => $arOrder[ 'ACCOUNT_NUMBER' ],
                            '#ORUSERID#' => $arUser[ 'LOGIN' ],
                            '#COUPON#'   => $COUPON,
                            '#SUMM#'     => $amount_sum,
                            '#BUDGET#'   => $curBudget,
                            '#TOTAL#'    => $curBudget2,
                            '#B_CUR#'    => $arOrder[ 'CURRENCY' ]
                        );

                        $template = $rule[ 'TEMPLATE' ];

                        $template = preg_replace( array_keys( $templateReplace ), array_values( $templateReplace ), $template );
                        $template = str_replace( "#", "", $template );

                        switch( $arSite[ 'CHARSET' ] ) {
                            case "cp1251":
                            case "windows-1251":
                                $template = iconv( "cp1251", "UTF-8", $template );
                                if( !$template )
                                    $template = iconv( "windows-1251", "UTF-8", $template );
                                if( !$template )
                                    $template = iconv( "cp1251", "UTF8", $template );
                                if( !$template )
                                    $template = iconv( "windows-1251", "UTF8", $template );
                                break;
                            default:
                        }

                        if( $rule[ 'FIRST_RULE' ] == "first_one" && $arOrder[ 'PRICE' ] < $rule[ 'FIRST_RULE_VALUE' ] ) {
                            if( $rule[ 'THIRD_RULE' ] == "generate_coupon" && $rule[ 'THIRD_RULE_DISCOUNT_ID' ] ) {
                                $arCouponFields = array(
                                    "DISCOUNT_ID" => $rule[ 'THIRD_RULE_DISCOUNT_ID' ],
                                    "ACTIVE"      => "Y",
                                    "ONE_TIME"    => "Y",
                                    "COUPON"      => $COUPON,
                                    "DATE_APPLY"  => false
                                );

                                if( CCatalogDiscountCoupon::Add( $arCouponFields ) ) {
                                    $class->Send( $phone, $template );
                                }
                            }

                            if( $rule[ 'THIRD_RULE' ] == "add_money" && ( $arOrder[ 'PRICE' ] < $rule[ 'FIRST_RULE_VALUE' ] ) && $rule[ 'THIRD_RULE_VALUE' ] ) {
                                if( $userAccount = CSaleUserAccount::GetByUserID( $arUser[ 'ID' ], $arOrder[ 'CURRENCY' ] ) ) {
                                    CSaleUserAccount::UpdateAccount(
                                        $arOrder[ 'USER_ID' ],
                                        $amount_sum,
                                        $arOrder[ 'CURRENCY' ],
                                        "Add Bonus",
                                        $arOrder[ 'ID' ]
                                    );

                                    $class->Send( $phone, $template );
                                } else {
                                    $arUserAccountFields = Array( "USER_ID" => $arUser[ 'ID' ], "CURRENCY" => $arOrder[ 'CURRENCY' ], "CURRENT_BUDGET" => 0 );
                                    $accId = CSaleUserAccount::Add( $arUserAccountFields );

                                    CSaleUserAccount::UpdateAccount(
                                        $arOrder[ 'USER_ID' ],
                                        $amount_sum,
                                        $arOrder[ 'CURRENCY' ],
                                        "Add Bonus",
                                        $arOrder[ 'ID' ]
                                    );

                                    $class->Send( $phone, $template );
                                }
                            }

                            if( $rule[ 'THIRD_RULE' ] == "add_percent" && ( $arOrder[ 'PRICE' ] < $rule[ 'FIRST_RULE_VALUE' ] ) && $rule[ 'THIRD_RULE_VALUE' ] ) {
                                if( $userAccount = CSaleUserAccount::GetByUserID( $arUser[ 'ID' ], $arOrder[ 'CURRENCY' ] ) ) {
                                    CSaleUserAccount::UpdateAccount(
                                        $arOrder[ 'USER_ID' ],
                                        $amount_sum,
                                        $arOrder[ 'CURRENCY' ],
                                        "Add Bonus",
                                        $arOrder[ 'ID' ]
                                    );

                                    $class->Send( $phone, $template );
                                } else {
                                    $arUserAccountFields = Array( "USER_ID" => $arUser[ 'ID' ], "CURRENCY" => $arOrder[ 'CURRENCY' ], "CURRENT_BUDGET" => 0 );
                                    $accId = CSaleUserAccount::Add( $arUserAccountFields );

                                    CSaleUserAccount::UpdateAccount(
                                        $arOrder[ 'USER_ID' ],
                                        $amount_sum,
                                        $arOrder[ 'CURRENCY' ],
                                        "Add Bonus",
                                        $arOrder[ 'ID' ]
                                    );

                                    $class->Send( $phone, $template );
                                }
                            }
                        }

                        if( $rule[ 'FIRST_RULE' ] == "first_two" && $arOrder[ 'PRICE' ] >= $rule[ 'FIRST_RULE_VALUE' ] ) {
                            if( $rule[ 'THIRD_RULE' ] == "generate_coupon" && $rule[ 'THIRD_RULE_DISCOUNT_ID' ] ) {
                                $arCouponFields = array(
                                    "DISCOUNT_ID" => $rule[ 'THIRD_RULE_DISCOUNT_ID' ],
                                    "ACTIVE"      => "Y",
                                    "ONE_TIME"    => "Y",
                                    "COUPON"      => $COUPON,
                                    "DATE_APPLY"  => false
                                );

                                CCatalogDiscountCoupon::Add( $arCouponFields );

                                $class->Send( $phone, $template );
                            }

                            if( $rule[ 'THIRD_RULE' ] == "add_money" && ( $arOrder[ 'PRICE' ] >= $rule[ 'FIRST_RULE_VALUE' ] ) && $rule[ 'THIRD_RULE_VALUE' ] ) {
                                if( $userAccount = CSaleUserAccount::GetByUserID( $arUser[ 'ID' ], $arOrder[ 'CURRENCY' ] ) ) {
                                    CSaleUserAccount::UpdateAccount(
                                        $arOrder[ 'USER_ID' ],
                                        $amount_sum,
                                        $arOrder[ 'CURRENCY' ],
                                        "Add Bonus",
                                        $arOrder[ 'ID' ]
                                    );

                                    $class->Send( $phone, $template );
                                } else {
                                    $arUserAccountFields = Array( "USER_ID" => $arUser[ 'ID' ], "CURRENCY" => $arOrder[ 'CURRENCY' ], "CURRENT_BUDGET" => 0 );
                                    $accId = CSaleUserAccount::Add( $arUserAccountFields );

                                    CSaleUserAccount::UpdateAccount(
                                        $arOrder[ 'USER_ID' ],
                                        $amount_sum,
                                        $arOrder[ 'CURRENCY' ],
                                        "Add Bonus",
                                        $arOrder[ 'ID' ]
                                    );

                                    $class->Send( $phone, $template );
                                }
                            }

                            if( $rule[ 'THIRD_RULE' ] == "add_percent" && ( $arOrder[ 'PRICE' ] >= $rule[ 'FIRST_RULE_VALUE' ] ) && $rule[ 'THIRD_RULE_VALUE' ] ) {
                                if( $userAccount = CSaleUserAccount::GetByUserID( $arUser[ 'ID' ], $arOrder[ 'CURRENCY' ] ) ) {
                                    CSaleUserAccount::UpdateAccount(
                                        $arOrder[ 'USER_ID' ],
                                        $amount_sum,
                                        $arOrder[ 'CURRENCY' ],
                                        "Add Bonus",
                                        $arOrder[ 'ID' ]
                                    );

                                    $class->Send( $phone, $template );
                                } else {
                                    $arUserAccountFields = Array( "USER_ID" => $arUser[ 'ID' ], "CURRENCY" => $arOrder[ 'CURRENCY' ], "CURRENT_BUDGET" => 0 );
                                    $accId = CSaleUserAccount::Add( $arUserAccountFields );

                                    CSaleUserAccount::UpdateAccount(
                                        $arOrder[ 'USER_ID' ],
                                        $amount_sum,
                                        $arOrder[ 'CURRENCY' ],
                                        "Add Bonus",
                                        $arOrder[ 'ID' ]
                                    );

                                    $class->Send( $phone, $template );
                                }
                            }
                        }

                        if( $rule[ 'FIRST_RULE' ] == "first_tree" && $rule[ 'FIRST_RULE_PERIOD' ] && $rule[ 'FIRST_RULE_PERIOD_RULE' ] && $rule[ 'FIRST_RULE_VALUE' ] ) {

                            switch( $rule[ 'FIRST_RULE_PERIOD' ] ) {
                                case "1 day":
                                    $date_first = date( 'd.m.Y' ) . " 00:00:00";
                                    $date_second = date( 'd.m.Y H:i:s' );
                                    break;
                                default:
                                    $per = $rule[ 'FIRST_RULE_PERIOD' ];
                                    $str = date( $DB->DateFormatToPHP( CSite::GetDateFormat( "FULL", $arOrder[ 'LID' ] ) ), time() );
                                    $date_first = date( 'd.m.Y H:i:s', strtotime( "$str - $per" ) );
                                    $date_second = date( 'd.m.Y H:i:s' );
                            }

                            $arFilter = Array(
                                "USER_ID"            => $arUser[ 'ID' ],
                                ">=DATE_INSERT_FROM" => $date_first,
                                "<=DATE_INSERT_TO"   => $date_second
                            );

                            $db_sales = CSaleOrder::GetList( array( "DATE_INSERT" => "ASC" ), $arFilter );
                            $sm = 0;
                            while( $ar_sales = $db_sales->Fetch() ) {
                                $sm += $ar_sales[ 'PRICE' ];
                            }

                            switch( $rule[ 'FIRST_RULE_PERIOD_RULE' ] ) {
                                case "more":
                                    if( $sm >= $rule[ 'FIRST_RULE_VALUE' ] ) {
                                        if( $rule[ 'THIRD_RULE' ] == "generate_coupon" && $rule[ 'THIRD_RULE_DISCOUNT_ID' ] ) {
                                            $arCouponFields = array(
                                                "DISCOUNT_ID" => $rule[ 'THIRD_RULE_DISCOUNT_ID' ],
                                                "ACTIVE"      => "Y",
                                                "ONE_TIME"    => "Y",
                                                "COUPON"      => $COUPON,
                                                "DATE_APPLY"  => false
                                            );

                                            CCatalogDiscountCoupon::Add( $arCouponFields );

                                            $class->Send( $phone, $template );
                                        }

                                        if( $rule[ 'THIRD_RULE' ] == "add_money" && ( $arOrder[ 'PRICE' ] >= $rule[ 'FIRST_RULE_VALUE' ] ) && $rule[ 'THIRD_RULE_VALUE' ] ) {
                                            if( $userAccount = CSaleUserAccount::GetByUserID( $arUser[ 'ID' ], $arOrder[ 'CURRENCY' ] ) ) {
                                                CSaleUserAccount::UpdateAccount(
                                                    $arOrder[ 'USER_ID' ],
                                                    $amount_sum,
                                                    $arOrder[ 'CURRENCY' ],
                                                    "Add Bonus",
                                                    $arOrder[ 'ID' ]
                                                );

                                                $class->Send( $phone, $template );
                                            } else {
                                                $arUserAccountFields = Array( "USER_ID" => $arUser[ 'ID' ], "CURRENCY" => $arOrder[ 'CURRENCY' ], "CURRENT_BUDGET" => 0 );
                                                $accId = CSaleUserAccount::Add( $arUserAccountFields );

                                                CSaleUserAccount::UpdateAccount(
                                                    $arOrder[ 'USER_ID' ],
                                                    $amount_sum,
                                                    $arOrder[ 'CURRENCY' ],
                                                    "Add Bonus",
                                                    $arOrder[ 'ID' ]
                                                );

                                                $class->Send( $phone, $template );
                                            }
                                        }

                                        if( $rule[ 'THIRD_RULE' ] == "add_percent" && ( $arOrder[ 'PRICE' ] >= $rule[ 'FIRST_RULE_VALUE' ] ) && $rule[ 'THIRD_RULE_VALUE' ] ) {
                                            if( $userAccount = CSaleUserAccount::GetByUserID( $arUser[ 'ID' ], $arOrder[ 'CURRENCY' ] ) ) {
                                                CSaleUserAccount::UpdateAccount(
                                                    $arOrder[ 'USER_ID' ],
                                                    $amount_sum,
                                                    $arOrder[ 'CURRENCY' ],
                                                    "Add Bonus",
                                                    $arOrder[ 'ID' ]
                                                );

                                                $class->Send( $phone, $template );
                                            } else {
                                                $arUserAccountFields = Array( "USER_ID" => $arUser[ 'ID' ], "CURRENCY" => $arOrder[ 'CURRENCY' ], "CURRENT_BUDGET" => 0 );
                                                $accId = CSaleUserAccount::Add( $arUserAccountFields );

                                                CSaleUserAccount::UpdateAccount(
                                                    $arOrder[ 'USER_ID' ],
                                                    $amount_sum,
                                                    $arOrder[ 'CURRENCY' ],
                                                    "Add Bonus",
                                                    $arOrder[ 'ID' ]
                                                );

                                                $class->Send( $phone, $template );
                                            }
                                        }
                                    }
                                    break;
                                case "less":
                                    if( $sm <= $rule[ 'FIRST_RULE_VALUE' ] ) {
                                        if( $rule[ 'THIRD_RULE' ] == "generate_coupon" && $rule[ 'THIRD_RULE_DISCOUNT_ID' ] ) {
                                            $arCouponFields = array(
                                                "DISCOUNT_ID" => $rule[ 'THIRD_RULE_DISCOUNT_ID' ],
                                                "ACTIVE"      => "Y",
                                                "ONE_TIME"    => "Y",
                                                "COUPON"      => $COUPON,
                                                "DATE_APPLY"  => false
                                            );

                                            CCatalogDiscountCoupon::Add( $arCouponFields );

                                            $class->Send( $phone, $template );
                                        }

                                        if( $rule[ 'THIRD_RULE' ] == "add_money" && ( $arOrder[ 'PRICE' ] < $rule[ 'FIRST_RULE_VALUE' ] ) && $rule[ 'THIRD_RULE_VALUE' ] ) {
                                            if( $userAccount = CSaleUserAccount::GetByUserID( $arUser[ 'ID' ], $arOrder[ 'CURRENCY' ] ) ) {
                                                CSaleUserAccount::UpdateAccount(
                                                    $arOrder[ 'USER_ID' ],
                                                    $amount_sum,
                                                    $arOrder[ 'CURRENCY' ],
                                                    "Add Bonus",
                                                    $arOrder[ 'ID' ]
                                                );

                                                $class->Send( $phone, $template );
                                            } else {
                                                $arUserAccountFields = Array( "USER_ID" => $arUser[ 'ID' ], "CURRENCY" => $arOrder[ 'CURRENCY' ], "CURRENT_BUDGET" => 0 );
                                                $accId = CSaleUserAccount::Add( $arUserAccountFields );

                                                CSaleUserAccount::UpdateAccount(
                                                    $arOrder[ 'USER_ID' ],
                                                    $amount_sum,
                                                    $arOrder[ 'CURRENCY' ],
                                                    "Add Bonus",
                                                    $arOrder[ 'ID' ]
                                                );

                                                $class->Send( $phone, $template );
                                            }
                                        }

                                        if( $rule[ 'THIRD_RULE' ] == "add_percent" && ( $arOrder[ 'PRICE' ] < $rule[ 'FIRST_RULE_VALUE' ] ) && $rule[ 'THIRD_RULE_VALUE' ] ) {
                                            if( $userAccount = CSaleUserAccount::GetByUserID( $arUser[ 'ID' ], $arOrder[ 'CURRENCY' ] ) ) {
                                                CSaleUserAccount::UpdateAccount(
                                                    $arOrder[ 'USER_ID' ],
                                                    $amount_sum,
                                                    $arOrder[ 'CURRENCY' ],
                                                    "Add Bonus",
                                                    $arOrder[ 'ID' ]
                                                );

                                                $class->Send( $phone, $template );
                                            } else {
                                                $arUserAccountFields = Array( "USER_ID" => $arUser[ 'ID' ], "CURRENCY" => $arOrder[ 'CURRENCY' ], "CURRENT_BUDGET" => 0 );
                                                $accId = CSaleUserAccount::Add( $arUserAccountFields );

                                                CSaleUserAccount::UpdateAccount(
                                                    $arOrder[ 'USER_ID' ],
                                                    $amount_sum,
                                                    $arOrder[ 'CURRENCY' ],
                                                    "Add Bonus",
                                                    $arOrder[ 'ID' ]
                                                );

                                                $class->Send( $phone, $template );
                                            }
                                        }
                                    }
                                    break;
                            }
                        }
                    }
                }
            }
        }
    }

    function GetRegisterTemplate()
    {
        global $DB;

        $return = array();
        $rsSites = CSite::GetList( $by = "id", $order = "asc", array() );
        $x = 0;
        while( $arSite = $rsSites->Fetch() ) {
            $x++;
            switch( $arSite[ 'CHARSET' ] ) {
                case "cp1251":
                case "windows-1251":
                    $DB->Query( 'SET NAMES CP1251' );
                    break;
                default:
            }
            if( $x == 1 ) {
                break;
            }
        }

        $query = $DB->Query( 'SELECT id, active, template FROM intis_sus_register ORDER BY id ASC' );

        $x = 0;
        while( $row = $query->Fetch() ) {
            $x++;
            if( $x == 1 ) {
                $return[ 'REGISTER' ] = array(
                    "ACTIVE"   => $row[ 'active' ],
                    "TEMPLATE" => $row[ 'template' ]
                );
            }

            if( $x == 2 ) {
                $return[ 'AUTH' ] = array(
                    "ACTIVE"   => $row[ 'active' ],
                    "TEMPLATE" => $row[ 'template' ]
                );
            }
        }

        return $return;
    }

    function SetRegisterTemplate( $active, $template )
    {
        global $DB;

        if( $active ) {
            $active = 1;
        } else {
            $active = 0;
        }

        if( $template ) {
            $text = $template;
        } else {
            $text = '';
        }

        try {
            $DB->Query( 'UPDATE intis_sus_register SET active="' . $active . '", template="' . addslashes( $text ) . '" WHERE id="1"' );
        } catch( \Exception $e ) {

        }
    }

    function SetAuthTemplate( $active, $template )
    {
        global $DB;

        if( $active ) {
            $active = 1;
        } else {
            $active = 0;
        }

        if( $template ) {
            $text = $template;
        } else {
            $text = '';
        }

        try {
            $DB->Query( 'UPDATE intis_sus_register SET active="' . $active . '", template="' . addslashes( $text ) . '" WHERE id="2"' );
        } catch( \Exception $e ) {

        }
    }
}

Class CIntisPhone
{
    function Parse( $phone )
    {
        $res = preg_replace( '/[^0-9]/', '', $phone );

        if( strlen( $res ) > 10 ) {
            if( strlen( $res ) == 11 && substr( $res, 0, 2 ) == "89" ) {
                $number = "7" . substr( $res, 1 );
            } else {
                $number = $res;
            }

            return $number;
        } elseif( strlen( $res ) == 10 && substr( $res, 0, 1 ) == "9" ) {
            $number = "7" . $res;

            return $number;
        }
    }
}

Class CIntisOriginators
{
    function SetDefaultOriginatorName()
    {
        COption::SetOptionString( "intis.senduserssms", "ORIGINATOR_NAME_ID", "inetsms" );
    }

    function GetOriginator( $secretKey )
    {
        $xml = '<?xml version="1.0" encoding="utf-8" ?>
            <request>
                <security>
                    <token value="' . $secretKey . '" />
                </security>
            </request>';
        $urltopost = CIntisSendSMS::GetProtocol() . 'xml.sms16.ru/xml/originator.php';
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-type: text/xml; charset=utf-8' ) );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_CRLF, true );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $xml );
        curl_setopt( $ch, CURLOPT_URL, $urltopost );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true );
        $result = curl_exec( $ch );

        $parse = $result;
        $p = xml_parser_create();
        xml_parse_into_struct( $p, $parse, $vals, $index );
        xml_parser_free( $p );

        $x = 0;

        foreach( $vals as $key => $avalue ) {
            foreach( $avalue as $value ) {
                if( ( $value == "ORIGINATOR" ) && ( $vals[ $key ][ 'attributes' ][ 'STATE' ] == "completed" ) ) {
                    $x++;
                }
            }
        }

        curl_close( $ch );

        if( $x ) {
            echo "<select name='SELECT_ORIGINATOR'>";
            echo "<option value=''></option>";
            foreach( $vals as $key => $avalue ) {
                foreach( $avalue as $value ) {
                    if( ( $value == "ORIGINATOR" ) && ( $vals[ $key ][ 'attributes' ][ 'STATE' ] == "completed" ) ) {
                        $x++;
                        if( $vals[ $key ][ 'value' ] == COption::GetOptionString( "intis.senduserssms", "ORIGINATOR_NAME_ID", "" ) ) {
                            $selected = " selected";
                        } else {
                            $selected = "";
                        }
                        echo "<option value='" . $vals[ $key ][ 'value' ] . "'" . $selected . ">" . $vals[ $key ][ 'value' ] . "</option>";
                        $originator = true;
                    }
                }
            }
            echo "</select>";
        } else {
            echo GetMessage( 'INTIS_SEND_USER_SMS_NOT_ACTIVE_SENDER' );
        }
    }
}

Class CBalance
{
    function Get()
    {
        CModule::IncludeModule( 'intis.senduserssms' );

        $secretKey = COption::GetOptionString( "intis.senduserssms", "TOKEN_PARAM", "" );
        $xml = '<?xml version="1.0" encoding="utf-8" ?>
            <request>
                <security>
                    <token value="' . $secretKey . '" />
                </security>
            </request>';
        $urltopost = CIntisSendSMS::GetProtocol() . 'xml.sms16.ru/xml/balance.php';
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-type: text/xml; charset=utf-8' ) );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_CRLF, true );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $xml );
        curl_setopt( $ch, CURLOPT_URL, $urltopost );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true );
        $result = curl_exec( $ch );

        $parse = $result;
        $p = xml_parser_create();
        xml_parse_into_struct( $p, $parse, $vals, $index );
        xml_parser_free( $p );

        echo $vals[ '1' ][ 'value' ];
        if( $vals[ '1' ][ 'attributes' ][ 'CURRENCY' ] == "RUR" ) {
            echo GetMessage( "INTIS_SEND_USER_SMS_INCLUDE_RUR" );
        } else {
            echo $vals[ '1' ][ 'attributes' ][ 'CURRENCY' ];
        }

        curl_close( $ch );
    }
}

Class CIntisSendSMS
{
    function GetProtocol()
    {
        $getResult = htmlspecialchars( COption::GetOptionString( "intis.senduserssms", "PROTOCOL_FIELD", "" ) );

        return $getResult;
    }

    function SetTemplateMessageDefaultOrder()
    {
        COption::SetOptionString( "intis.senduserssms", "SMS_SALE_ORDER_ADD_ID", GetMessage( "INTIS_SEND_USER_SMS_INCLUDE_TEMPLATE_ORDER_ADD_DEFAULT" ) );
    }

    function SetTemplateMessageDefaultStatus()
    {
        COption::SetOptionString( "intis.senduserssms", "SMS_SALE_ORDER_STATUS_ID", GetMessage( "INTIS_SEND_USER_SMS_INCLUDE_TEMPLATE_STATUS_DEFAULT" ) );
    }

    function SetTemplateMessageDefaultCancel()
    {
        COption::SetOptionString( "intis.senduserssms", "SMS_SALE_ORDER_CANCEL_ID", GetMessage( "INTIS_SEND_USER_SMS_INCLUDE_TEMPLATE_CANCEL_DEFAULT" ) );
    }

    function SetTemplateMessageDefaultPay()
    {
        COption::SetOptionString( "intis.senduserssms", "SMS_SALE_ORDER_PAY_ID", GetMessage( "INTIS_SEND_USER_SMS_INCLUDE_TEMPLATE_PAY_DEFAULT" ) );
    }

    function SetTemplateMessageDefaultDelivery()
    {
        COption::SetOptionString( "intis.senduserssms", "SMS_SALE_ORDER_DELIVERY_ID", GetMessage( "INTIS_SEND_USER_SMS_INCLUDE_TEMPLATE_DELIVERY_DEFAULT" ) );
    }

    function SetDefaultOrderPhoneMessage()
    {
        COption::SetOptionString( "intis.senduserssms", "ORDER_PHONE_ID", "PHONE" );
    }

    function OnSaleRemindOrderPayHandler( $ID, $eventName, $arOrder )
    {
        CModule::IncludeModule( 'catalog' );
        CModule::IncludeModule( 'intis.senduserssms' );

        $class = new CIntisSUSTable();

        $class->CallingEvent( $ID, $arOrder, false, false, "remind" );
    }

    function OnSaleComponentOrderOneStepCompleteHandler( $ID, $arOrder )
    {
        CModule::IncludeModule( 'catalog' );
        CModule::IncludeModule( 'intis.senduserssms' );

        $class = new CIntisSUSTable();

        $class->CallingEvent( $ID, $arOrder, "OR_", "NEW", "main" );
    }

    function OnSaleComponentOrderCompleteHandler( $ID, $arOrder )
    {
        CModule::IncludeModule( 'catalog' );
        CModule::IncludeModule( 'intis.senduserssms' );

        $class = new CIntisSUSTable();

        $class->CallingEvent( $ID, $arOrder, "OR_", "NEW", "main" );
    }

    function OnSaleCancelOrderHandler( $ID, $val )
    {
        if( $val == 'Y' ) {
            CModule::IncludeModule( 'catalog' );
            CModule::IncludeModule( 'intis.senduserssms' );

            $arOrder = CSaleOrder::GetByID( $ID );

            $class = new CIntisSUSTable();

            $class->CallingEvent( $ID, $arOrder, "OR_", "CANCEL", "main" );
        } else {
            return false;
        }
    }

    function OnSalePayOrderHandler( $ID, $val )
    {
        if( $val == 'Y' ) {
            CModule::IncludeModule( 'catalog' );
            CModule::IncludeModule( 'intis.senduserssms' );

            $arOrder = CSaleOrder::GetByID( $ID );

            $class = new CIntisSUSTable();

            $class->CallingEvent( $ID, $arOrder, "OR_", "PAY", "main" );
        }
    }

    function OnSaleDeliveryOrderHandler( $ID, $val )
    {
        if( $val == 'Y' ) {
            CModule::IncludeModule( 'catalog' );
            CModule::IncludeModule( 'intis.senduserssms' );

            $arOrder = CSaleOrder::GetByID( $ID );

            $class = new CIntisSUSTable();

            $class->CallingEvent( $ID, $arOrder, "OR_", "DELIVER", "main" );
        }
    }

    function OnSaleStatusOrderHandler( $ID, $val )
    {
        CModule::IncludeModule( 'catalog' );
        CModule::IncludeModule( 'intis.senduserssms' );

        $arOrder = CSaleOrder::GetByID( $ID );

        $class = new CIntisSUSTable();

        $class->CallingEvent( $ID, $arOrder, "ST_", $val, "status" );
    }

    function RegisterHandler( &$arFields )
    {
        global $DB;

        if( $arFields[ "USER_ID" ] > 0 ) {
            $rsUser = CUser::GetByID( $arFields[ 'USER_ID' ] );
            $arUser = $rsUser->Fetch();

            $query = $DB->Query( 'SELECT * FROM intis_sus_register WHERE id="1"' );

            $result = $query->Fetch();
            $active = $result[ 'active' ];
            $template = $result[ 'template' ];
            $phone_field = COption::GetOptionString( "intis.senduserssms", "USER_PHONE", "" );
            $phone = CIntisPhone::Parse( $arUser[ $phone_field ] );

            if( $active && $template ) {
                $class = new CIntisSUSTable();

                $getTextSmsReplace = array(
                    '#REGUSERID#'   => $arFields[ 'USER_ID' ],
                    '#REGLOGIN#'    => $arFields[ 'LOGIN' ],
                    '#REGNAME#'     => $arFields[ 'NAME' ],
                    '#REGLASTNAME#' => $arFields[ 'LAST_NAME' ],
                    '#REGPASSWORD#' => $arFields[ 'PASSWORD' ],
                    '#REGEMAIL#'    => $arFields[ 'EMAIL' ],
                    '#REGUSERIP#'   => $arFields[ 'USER_IP' ]
                );

                $text_sms = preg_replace( array_keys( $getTextSmsReplace ), array_values( $getTextSmsReplace ), $template );
                $text_sms = str_replace( "#", "", $text_sms );

                $class->Send( $phone, $text_sms );
            }
        }
    }

    function AuthHandler( $arUser )
    {
        global $DB;

        $query = $DB->Query( 'SELECT * FROM intis_sus_register WHERE id="2"' );

        $result = $query->Fetch();
        $active = $result[ 'active' ];
        $template = $result[ 'template' ];
        $phone_field = COption::GetOptionString( "intis.senduserssms", "USER_PHONE", "" );
        $phone = CIntisPhone::Parse( $arUser[ 'user_fields' ][ $phone_field ] );

        if( $active && $template ) {
            $class = new CIntisSUSTable();

            $getTextSmsReplace = array(
                '#REGUSERID#'   => $arUser[ 'user_fields' ][ 'ID' ],
                '#REGLOGIN#'    => $arUser[ 'user_fields' ][ 'LOGIN' ],
                '#REGNAME#'     => $arUser[ 'user_fields' ][ 'NAME' ],
                '#REGLASTNAME#' => $arUser[ 'user_fields' ][ 'LAST_NAME' ],
                '#REGEMAIL#'    => $arUser[ 'user_fields' ][ 'EMAIL' ],
                '#REGUSERIP#'   => $_SERVER[ 'REMOTE_ADDR' ]
            );

            $text_sms = preg_replace( array_keys( $getTextSmsReplace ), array_values( $getTextSmsReplace ), $template );
            $text_sms = str_replace( "#", "", $text_sms );

            $class->Send( $phone, $text_sms );
        }
    }
}

?>