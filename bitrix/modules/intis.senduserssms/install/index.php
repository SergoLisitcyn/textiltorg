<?
/////////////////////////////
//INTIS LLC. 2013          //
//Tel.: 8 800-333-12-02    //
//www.sms16.ru             //
//Ruslan Semagin           //
/////////////////////////////

global $MESS;
$PathInstall = str_replace( "\\", "/", __FILE__ );
$PathInstall = substr( $PathInstall, 0, strlen( $PathInstall ) - strlen( "/index.php" ) );
IncludeModuleLangFile( $PathInstall . "/install.php" );

Class intis_senduserssms extends CModule
{
    var $MODULE_ID = "intis.senduserssms";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_GROUP_RIGHTS = "Y";

    function intis_senduserssms()
    {
        $arModuleVersion = array();

        $path = str_replace( "\\", "/", __FILE__ );
        $path = substr( $path, 0, strlen( $path ) - strlen( "/index.php" ) );
        include( $path . "/version.php" );

        if( is_array( $arModuleVersion ) && array_key_exists( "VERSION", $arModuleVersion ) ) {
            $this->MODULE_VERSION = $arModuleVersion[ "VERSION" ];
            $this->MODULE_VERSION_DATE = $arModuleVersion[ "VERSION_DATE" ];
        }

        $this->MODULE_NAME = GetMessage( "INTIS_SEND_USER_SMS_MODULE_NAME" );
        $this->MODULE_DESCRIPTION = GetMessage( "INTIS_SEND_USER_SMS_MODULE_DESCRIPTION" );
        $this->PARTNER_NAME = 'Intis LLC';
        $this->PARTNER_URI = "http://www.sms16.ru";
    }

    function CreateIntisTables()
    {
        global $DB;

        CModule::IncludeModule( 'sale' );

        $rsSites = CSite::GetList( $by = "id", $order = "asc", array() );
        while( $arSite = $rsSites->Fetch() ) {
            $site_id = $arSite[ 'ID' ];

            switch( $arSite[ 'CHARSET' ] ) {
                case "UTF-8":
                    $coll = "utf8";
                    break;
                case "cp1251":
                case "windows-1251":
                    $coll = "cp1251";
                    break;
            }

            $DB->Query(
                'CREATE TABLE IF NOT EXISTS intis_additional_rules (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `site_id` varchar(2) CHARACTER SET ' . $coll . ' NOT NULL,
                  `event_id` varchar(15) CHARACTER SET ' . $coll . ' NOT NULL,
                  `template` text CHARACTER SET ' . $coll . ' NOT NULL,
                  `active` enum("0","1") CHARACTER SET ' . $coll . ' NOT NULL DEFAULT "0",
                  `first_rule` varchar(10) CHARACTER SET ' . $coll . ' NOT NULL,
                  `first_rule_value` bigint(18) NOT NULL,
                  `first_rule_period` varchar(7) CHARACTER SET ' . $coll . ' NOT NULL,
                  `first_rule_period_rule` varchar(4) CHARACTER SET ' . $coll . ' NOT NULL,
                  `third_rule` varchar(15) CHARACTER SET ' . $coll . ' NOT NULL,
                  `third_rule_value` bigint(18) NOT NULL,
                  `third_rule_discount_id` int(10) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=' . $coll
            );

            $DB->Query(
                'CREATE TABLE IF NOT EXISTS intis_sus_status_template_' . $site_id . ' (
                  `id` varchar(2) CHARACTER SET ' . $coll . ' NOT NULL,
                  `template` text CHARACTER SET ' . $coll . ' NOT NULL,
                  `active` enum("0","1") NOT NULL DEFAULT "1",
                  `copy` enum("0","1") NOT NULL DEFAULT "0",
                  UNIQUE KEY `id` (`id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=' . $coll
            );

            $DB->Query(
                'CREATE TABLE IF NOT EXISTS intis_sus_main_template_' . $site_id . ' (
                  `id` varchar(20) CHARACTER SET ' . $coll . ' NOT NULL,
                  `template` text CHARACTER SET ' . $coll . ' NOT NULL,
                  `active` enum("0","1") NOT NULL DEFAULT "1",
                  `copy` enum("0","1") NOT NULL DEFAULT "0",
                  UNIQUE KEY `id` (`id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=' . $coll
            );

            $DB->Query(
                "CREATE TABLE IF NOT EXISTS intis_sus_remind_pay_" . $site_id . " (
                  `template` text NOT NULL,
                  `active` enum('0','1') NOT NULL DEFAULT '0',
                  `copy` enum('0','1') NOT NULL DEFAULT '0'
                ) ENGINE=MyISAM DEFAULT CHARSET=" . $coll
            );

            $DB->Query(
                "CREATE TABLE IF NOT EXISTS intis_sus_register (
                  `id` int(11) NOT NULL,
                  `active` enum('0','1') NOT NULL DEFAULT '0',
                  `template` text NOT NULL
                ) ENGINE=MyISAM DEFAULT CHARSET=" . $coll
            );

            try {
                $DB->Query( "INSERT INTO `intis_sus_register` (`id`, `active`, `template`) VALUES (1, '0', ''), (2, '0', '')" );
            } catch( \Exception $e ) {

            }

            try {
                $DB->Query(
                    'INSERT INTO intis_sus_main_template_' . $site_id . '(`id`, `template`, `active`)
                    VALUES
                    ("NEW", "' . GetMessage( 'INTIS_SEND_USER_SMS_MAIN_NEW' ) . '", "1"),
                    ("CANCEL", "' . GetMessage( 'INTIS_SEND_USER_SMS_MAIN_CANCEL' ) . '", "1"),
                    ("PAY", "' . GetMessage( 'INTIS_SEND_USER_SMS_MAIN_PAY' ) . '", "1"),
                    ("DELIVER", "' . GetMessage( 'INTIS_SEND_USER_SMS_MAIN_DELIVER' ) . '", "1")'
                );
            } catch( \Exception $e ) {

            }

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
                    $DB->Query( 'INSERT INTO intis_sus_status_template_' . $site_id . '(`id`, `template`, `active`) VALUES ("' . $arStatusResult[ 'ID' ] . '", "' . GetMessage( 'INTIS_SEND_USER_SMS_STATUS_TEXT' ) . '", "0")' );
                } catch( \Exception $e ) {

                }
            }
        }
    }

    function InstallDB()
    {
        RegisterModule( "intis.senduserssms" );

        return true;
    }

    function UnInstallDB()
    {
        COption::RemoveOption( "intis.senduserssms" );
        UnRegisterModule( "intis.senduserssms" );

        return true;
    }

    function InstallFiles()
    {
        return true;
    }

    function UnInstallFiles()
    {
        return true;
    }

    function InstallEvents()
    {
        return true;
    }

    function UnInstallEvents()
    {
        return true;
    }

    function DoInstall()
    {
        if( IsModuleInstalled( "sale" ) ) {
            global $DB, $APPLICATION;
            $this->CreateIntisTables();
            $this->InstallDB();
            $this->InstallFiles();
            RegisterModuleDependences( "sale", "OnSaleComponentOrderOneStepComplete", "intis.senduserssms", "CIntisSendSMS", "OnSaleComponentOrderOneStepCompleteHandler" );
            RegisterModuleDependences( "sale", "OnSaleComponentOrderComplete", "intis.senduserssms", "CIntisSendSMS", "OnSaleComponentOrderCompleteHandler" );
            RegisterModuleDependences( "sale", "OnSaleStatusOrder", "intis.senduserssms", "CIntisSendSMS", "OnSaleStatusOrderHandler" );
            RegisterModuleDependences( "sale", "OnSaleCancelOrder", "intis.senduserssms", "CIntisSendSMS", "OnSaleCancelOrderHandler" );
            RegisterModuleDependences( "sale", "OnSalePayOrder", "intis.senduserssms", "CIntisSendSMS", "OnSalePayOrderHandler" );
            RegisterModuleDependences( "sale", "OnSaleDeliveryOrder", "intis.senduserssms", "CIntisSendSMS", "OnSaleDeliveryOrderHandler" );
            RegisterModuleDependences( "main", "OnAfterUserAuthorize", "intis.senduserssms", "CIntisSendSMS", "AuthHandler" );
            RegisterModuleDependences( "main", "OnAfterUserRegister", "intis.senduserssms", "CIntisSendSMS", "RegisterHandler" );
            RegisterModuleDependences( "main", "OnAfterUserSimpleRegister", "intis.senduserssms", "CIntisSendSMS", "RegisterHandler" );
            $APPLICATION->IncludeAdminFile( GetMessage( "INTIS_SEND_USER_SMS_INSTALL_TITLE" ), $_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/intis.senduserssms/install/step.php" );
        } else {
            global $APPLICATION;
            $APPLICATION->IncludeAdminFile( GetMessage( "INTIS_SEND_USER_SMS_INSTALL_TITLE" ), $_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/intis.senduserssms/install/error.php" );
        }
    }

    function DoUninstall()
    {
        global $APPLICATION, $DB;
        $this->UnInstallFiles();
        $this->UnInstallDB();
        UnRegisterModuleDependences( "sale", "OnSaleComponentOrderOneStepComplete", "intis.senduserssms", "CIntisSendSMS", "OnSaleComponentOrderOneStepCompleteHandler" );
        UnRegisterModuleDependences( "sale", "OnSaleComponentOrderComplete", "intis.senduserssms", "CIntisSendSMS", "OnSaleComponentOrderCompleteHandler" );
        UnRegisterModuleDependences( "sale", "OnSaleStatusOrder", "intis.senduserssms", "CIntisSendSMS", "OnSaleStatusOrderHandler" );
        UnRegisterModuleDependences( "sale", "OnSaleCancelOrder", "intis.senduserssms", "CIntisSendSMS", "OnSaleCancelOrderHandler" );
        UnRegisterModuleDependences( "sale", "OnSalePayOrder", "intis.senduserssms", "CIntisSendSMS", "OnSalePayOrderHandler" );
        UnRegisterModuleDependences( "sale", "OnSaleDeliveryOrder", "intis.senduserssms", "CIntisSendSMS", "OnSaleDeliveryOrderHandler" );
        UnRegisterModuleDependences( "main", "OnAfterUserAuthorize", "intis.senduserssms", "CIntisSendSMS", "AuthHandler" );
        UnRegisterModuleDependences( "main", "OnAfterUserRegister", "intis.senduserssms", "CIntisSendSMS", "RegisterHandler" );
        UnRegisterModuleDependences( "main", "OnAfterUserSimpleRegister", "intis.senduserssms", "CIntisSendSMS", "RegisterHandler" );
        UnRegisterModuleDependences( "main", "OnAfterUserAuthorize", "intis.senduserssms", "CIntisSendSMS", "AuthHandler" );
        UnRegisterModuleDependences( "main", "OnAfterUserRegister", "intis.senduserssms", "CIntisSendSMS", "RegisterHandler" );
        UnRegisterModuleDependences( "main", "OnAfterUserSimpleRegister", "intis.senduserssms", "CIntisSendSMS", "RegisterHandler" );
        UnRegisterModuleDependences( "sale", "OnOrderRemindSendEmail", "intis.senduserssms", "CIntisSendSMS", "OnSaleRemindOrderPayHandler" );
        $APPLICATION->IncludeAdminFile( GetMessage( "INTIS_SEND_USER_SMS_UNINSTALL_TITLE" ), $_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/intis.senduserssms/install/unstep.php" );
    }
}

?>