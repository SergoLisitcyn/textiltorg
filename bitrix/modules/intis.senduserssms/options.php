<?
/////////////////////////////
//INTIS LLC. 2013          //
//Tel.: 8 800-333-12-02    //
//www.sms16.ru             //
//Ruslan Semagin           //
/////////////////////////////


require_once( $_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/main/include/prolog_admin_before.php" );


IncludeModuleLangFile( __FILE__ );

$module_id = "intis.senduserssms";

$POST_RIGHT = $APPLICATION->GetGroupRight( "intis.senduserssms" );

if( $POST_RIGHT == "D" )
    $APPLICATION->AuthForm( GetMessage( "ACCESS_DENIED" ) );
?>
<?
CModule::IncludeModule( "iblock" );
CModule::IncludeModule( "intis.senduserssms" );
CModule::IncludeModule( "sale" );
CModule::IncludeModule( "catalog" );

$PROTOCOL = CIntisSendSMS::GetProtocol();
$table = new CIntisSUSTable();
$rules = $table->GetRules( SITE_CHARSET );

if( isset( $_POST[ 'RULE_ID' ] ) ) {
    foreach( $_POST[ 'RULE_ID' ] as $key => $value ) {
        $kv = explode( "_", $key );
        if( $value == "ACTIVE" ) {
            $table->OnOffRule( $kv[ 0 ], "1", $kv[ 1 ] );
        } elseif( $value == "INACTIVE" ) {
            $table->OnOffRule( $kv[ 0 ], "0", $kv[ 1 ] );
        } elseif( $value == "DELETE" ) {
            $table->DeleteRule( $kv[ 0 ] );
        }
    }

    LocalRedirect( $APPLICATION->GetCurPage() . "?mid=" . urlencode( $module_id ) . "&amp;lang=" . urlencode( LANGUAGE_ID ) );
}

if( isset( $_POST[ 'NEW_RULE' ] ) && $_POST[ 'NEW_RULE' ] == "NEW_RULE" ) {
    $table->AddNewRule(
        $_POST[ 'SITE_RULE_ID' ], $_POST[ 'EVENT_ID' ], $_POST[ 'NEW_RULE_TEMPLATE' ], 1,
        $_POST[ 'FIRST' ], $_POST[ 'SECOND_SUMM' ], $_POST[ 'SECOND_PERIOD' ], $_POST[ 'SECOND_PERIOD_VAL' ],
        $_POST[ 'THIRD' ], $_POST[ 'FINAL_STEP_VALUE' ], $_POST[ 'SELECT_DISCOUNT' ] );

    LocalRedirect( $APPLICATION->GetCurPage() . "?mid=" . urlencode( $module_id ) . "&amp;lang=" . urlencode( LANGUAGE_ID ) );
}

if( $_POST[ "AUTH_TOKEN" ] == true ) {
    COption::SetOptionString( $module_id, "PROTOCOL_FIELD", htmlspecialchars( $_POST[ 'PROTOCOL' ] ) );
    COption::SetOptionString( "intis.senduserssms", "TOKEN_PARAM", $_POST[ 'AUTH_TOKEN' ] );
    COption::SetOptionString( "intis.senduserssms", "ORDER_PHONE_ID", $_POST[ 'ORDER_PHONE' ] );
    COption::SetOptionString( "intis.senduserssms", "ADMIN_PHONE_ID", $_POST[ 'ADMIN_PHONE' ] );

    if( $_POST[ 'USER_PHONE' ] ) {
        COption::SetOptionString( "intis.senduserssms", "USER_PHONE", $_POST[ 'USER_PHONE' ] );
    }

    if( $_POST[ 'SELECT_ORIGINATOR' ] ) {
        COption::SetOptionString( "intis.senduserssms", "ORIGINATOR_NAME_ID", $_POST[ 'SELECT_ORIGINATOR' ] );
    }

    LocalRedirect( $APPLICATION->GetCurPage() . "?mid=" . urlencode( $module_id ) . "&amp;lang=" . urlencode( LANGUAGE_ID ) );
}

if( $_POST[ 'ADD_SETTINGS' ] && ( strlen( $_POST[ 'ADD_SETTINGS' ] ) == 1 || strlen( $_POST[ 'ADD_SETTINGS' ] ) == 2 ) ) {
    $table->AddTable( $_POST[ 'ADD_SETTINGS' ] );
    LocalRedirect( $APPLICATION->GetCurPage() . "?mid=" . urlencode( $module_id ) . "&amp;lang=" . urlencode( LANGUAGE_ID ) );
}

if( $_POST[ 'USER_REGISTER_ON_OFF' ] == 'on' ) {
    $table->SetRegisterTemplate( 1, $_POST[ 'USER_REGISTER_TEMPLATE' ] );
    COption::SetOptionString( "intis.senduserssms", "USER_REGISTER_ON_OFF_VALUE", 'on' );
    LocalRedirect( $APPLICATION->GetCurPage() . "?mid=" . urlencode( $module_id ) . "&amp;lang=" . urlencode( LANGUAGE_ID ) );
}

if( $_POST[ 'USER_REGISTER_ON_OFF' ] == 'off' ) {
    $table->SetRegisterTemplate( 0, $_POST[ 'USER_REGISTER_TEMPLATE' ] );
    COption::SetOptionString( "intis.senduserssms", "USER_REGISTER_ON_OFF_VALUE", "off" );
    LocalRedirect( $APPLICATION->GetCurPage() . "?mid=" . urlencode( $module_id ) . "&amp;lang=" . urlencode( LANGUAGE_ID ) );
}

if( $_POST[ 'USER_AUTH_ON_OFF' ] == 'on' ) {
    $table->SetAuthTemplate( 1, $_POST[ 'USER_AUTH_TEMPLATE' ] );
    COption::SetOptionString( "intis.senduserssms", "USER_AUTH_ON_OFF_VALUE", 'on' );
    LocalRedirect( $APPLICATION->GetCurPage() . "?mid=" . urlencode( $module_id ) . "&amp;lang=" . urlencode( LANGUAGE_ID ) );
}

if( $_POST[ 'USER_AUTH_ON_OFF' ] == 'off' ) {
    $table->SetAuthTemplate( 0, $_POST[ 'USER_AUTH_TEMPLATE' ] );
    COption::SetOptionString( "intis.senduserssms", "USER_AUTH_ON_OFF_VALUE", "off" );
    LocalRedirect( $APPLICATION->GetCurPage() . "?mid=" . urlencode( $module_id ) . "&amp;lang=" . urlencode( LANGUAGE_ID ) );
}

$arFilter[ "LID" ] = LANGUAGE_ID;
$getStatusList = CSaleStatus::GetList(
    array( $by => $order ),
    $arFilter,
    false,
    false,
    array( "ID", "SORT", "LID", "NAME", "DESCRIPTION", $by )
);
while( $arStatusResult = $getStatusList->Fetch() ) {
    $arStatusList[ ] = array(
        "ID"          => $arStatusResult[ 'ID' ],
        "NAME"        => $arStatusResult[ 'NAME' ],
        "DESCRIPTION" => $arStatusResult[ 'DESCRIPTION' ]
    );
}

$rsGetSites = CSite::GetList( $by = "id", $order = "ASC", array() );
while( $arGetSite = $rsGetSites->Fetch() ) {
    if( isset( $_POST[ 'NEW_STATUS_' . $arGetSite[ 'ID' ] ] ) ) {
        foreach( $_POST[ 'NEW_STATUS_' . $arGetSite[ 'ID' ] ] as $new_status ) {
            if( !$table->FindStatus( $arGetSite[ 'ID' ], $new_status ) && $new_status == true ) {
                $table->AddStatus( $arGetSite[ 'ID' ], $new_status, $_POST[ 'SMS_SALE_STATUS_MESSAGE_' . $arGetSite[ 'ID' ] . $new_status ], $_POST[ 'SMS_SALE_STATUS_ACTIVE_' . $arGetSite[ 'ID' ] . $new_status ] );
            }
        }
    }

    if( isset( $_POST[ 'SMS_SALE_MESSAGE_' . $arGetSite[ 'ID' ] . 'NEW' ] ) ) {
        $table->UpdateMainEvent( $arGetSite[ 'ID' ], 'NEW', $_POST[ 'SMS_SALE_MESSAGE_' . $arGetSite[ 'ID' ] . 'NEW' ],
                                 $_POST[ 'SMS_SALE_ACTIVE_' . $arGetSite[ 'ID' ] . 'NEW' ], $_POST[ 'SMS_SALE_COPY_' . $arGetSite[ 'ID' ] . 'NEW' ] );
    }

    if( isset( $_POST[ 'SMS_SALE_MESSAGE_' . $arGetSite[ 'ID' ] . 'CANCEL' ] ) ) {
        $table->UpdateMainEvent( $arGetSite[ 'ID' ], 'CANCEL', $_POST[ 'SMS_SALE_MESSAGE_' . $arGetSite[ 'ID' ] . 'CANCEL' ],
                                 $_POST[ 'SMS_SALE_ACTIVE_' . $arGetSite[ 'ID' ] . 'CANCEL' ], $_POST[ 'SMS_SALE_COPY_' . $arGetSite[ 'ID' ] . 'CANCEL' ] );
    }

    if( isset( $_POST[ 'SMS_SALE_MESSAGE_' . $arGetSite[ 'ID' ] . 'PAY' ] ) ) {
        $table->UpdateMainEvent( $arGetSite[ 'ID' ], 'PAY', $_POST[ 'SMS_SALE_MESSAGE_' . $arGetSite[ 'ID' ] . 'PAY' ],
                                 $_POST[ 'SMS_SALE_ACTIVE_' . $arGetSite[ 'ID' ] . 'PAY' ], $_POST[ 'SMS_SALE_COPY_' . $arGetSite[ 'ID' ] . 'PAY' ] );
    }

    if( isset( $_POST[ 'SMS_SALE_MESSAGE_' . $arGetSite[ 'ID' ] . 'DELIVER' ] ) ) {
        $table->UpdateMainEvent( $arGetSite[ 'ID' ], 'DELIVER', $_POST[ 'SMS_SALE_MESSAGE_' . $arGetSite[ 'ID' ] . 'DELIVER' ],
                                 $_POST[ 'SMS_SALE_ACTIVE_' . $arGetSite[ 'ID' ] . 'DELIVER' ], $_POST[ 'SMS_SALE_COPY_' . $arGetSite[ 'ID' ] . 'DELIVER' ] );
    }

    if( isset( $_POST[ 'SMS_SALE_MESSAGE_' . $arGetSite[ 'ID' ] . '_REMIND' ] ) ) {
        $table->UpdateRemind( $arGetSite[ 'ID' ], $_POST[ 'SMS_SALE_MESSAGE_' . $arGetSite[ 'ID' ] . '_REMIND' ],
                              $_POST[ 'SMS_SALE_ACTIVE_' . $arGetSite[ 'ID' ] . '_REMIND' ], $_POST[ 'SMS_SALE_COPY_' . $arGetSite[ 'ID' ] . '_REMIND' ] );
    }

    foreach( $arStatusList as $stat ) {
        if( isset( $_POST[ 'SMS_SALE_STATUS_MESSAGE_' . $arGetSite[ 'ID' ] . $stat[ 'ID' ] ] ) ) {
            $table->UpdateStatusEvent( $arGetSite[ 'ID' ], $stat[ 'ID' ], $_POST[ 'SMS_SALE_STATUS_MESSAGE_' . $arGetSite[ 'ID' ] . $stat[ 'ID' ] ],
                                       $_POST[ 'SMS_SALE_STATUS_ACTIVE_' . $arGetSite[ 'ID' ] . $stat[ 'ID' ] ], $_POST[ 'SMS_SALE_STATUS_COPY_' . $arGetSite[ 'ID' ] . $stat[ 'ID' ] ] );
        }
    }
}

$par = COption::GetOptionString( "intis.senduserssms", "TOKEN_PARAM", "" );
$reg_on = COption::GetOptionString( "intis.senduserssms", "USER_REGISTER_ON_OFF_VALUE", "" );
$auth_on = COption::GetOptionString( "intis.senduserssms", "USER_AUTH_ON_OFF_VALUE", "" );
$reg_tpl = $table->GetRegisterTemplate();

if( COption::GetOptionString( "intis.senduserssms", "ORIGINATOR_NAME_ID", "" ) == false )
    CIntisOriginators::SetDefaultOriginatorName();

if( COption::GetOptionString( "intis.senduserssms", "ORDER_PHONE_ID", "" ) == false )
    CIntisSendSMS::SetDefaultOrderPhoneMessage();

$originator = COption::GetOptionString( "intis.senduserssms", "ORIGINATOR_NAME_ID", "" );
$orderPhoneFieldValue = COption::GetOptionString( "intis.senduserssms", "ORDER_PHONE_ID", "" );
$adminPhoneFieldValue = COption::GetOptionString( "intis.senduserssms", "ADMIN_PHONE_ID", "" );
$userPhoneValue = COption::GetOptionString( "intis.senduserssms", "USER_PHONE", "" );

$rsUser = CUser::GetByID( 1 );
$arUser = $rsUser->Fetch();
$keys = $arUser;
ksort( $keys );
reset( $keys );

$rsDiscounts = CCatalogDiscount::GetList(
    array( "SITE_ID" => "ASC" ),
    array(),
    false,
    false,
    array( "ID", "SITE_ID", "NAME", "VALUE_TYPE", "VALUE" )
);

$p1 = 'PERSONAL';
$p2 = 'WORK';
$p3 = 'UF_';
?>
<?
require( $_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/main/include/prolog_admin_after.php" );
?>
<?
$aTabs = array();
$aTabs[ ] = array( "DIV" => "edit1", "TAB" => GetMessage( "INTIS_SEND_USER_SMS_SALE_OPTION_TAB1" ), "ICON" => "", "TITLE" => "" );

$rsSites = CSite::GetList( $by = "id", $order = "ASC", array() );
while( $arSite = $rsSites->Fetch() ) {
    $aTabs[ ] = array( "DIV" => "edit_" . $arSite[ 'ID' ], "TAB" => GetMessage( "INTIS_SEND_USER_SMS_SALE_OPTION_TAB_SITE" ) . $arSite[ 'ID' ], "ICON" => "", "TITLE" => "" );
}

$aTabs[ ] = array( "DIV" => "edit_register", "TAB" => GetMessage( "INTIS_SEND_USER_SMS_REGISTER_TITLE" ), "ICON" => "", "TITLE" => "" );
$aTabs[ ] = array( "DIV" => "edit_rules", "TAB" => GetMessage( "INTIS_SEND_USER_SMS_SALE_OPTION_RULES" ), "ICON" => "", "TITLE" => "" );

if( COption::GetOptionString( "intis.senduserssms", "TOKEN_PARAM", "" ) == true ) {
    $aTabs[ ] = array( "DIV" => "edit6", "TAB" => GetMessage( "INTIS_SEND_USER_SMS_SALE_OPTION_GET_BALANCE" ), "ICON" => "", "TITLE" => "" );
}
$aTabs[ ] = array( "DIV" => "edit7", "TAB" => GetMessage( "INTIS_SEND_USER_SMS_SALE_OPTION_TAB5" ), "ICON" => "", "TITLE" => "" );

$tabControl = new CAdminTabControl( "tabControl", $aTabs );
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
<form method="POST"
      action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode( $module_id ) ?>&amp;lang=<?= urlencode( LANGUAGE_ID ) ?>">
    <?
    if( $par ) {
        echo CAdminMessage::ShowNote( GetMessage( 'INTIS_SEND_USER_SMS_TOKEN_OK' ) );
    } else {
        echo CAdminMessage::ShowMessage( GetMessage( 'INTIS_SEND_USER_SMS_TOKEN_ERR' ) );
    }
    ?>
    <tr class="heading">
        <td><? echo GetMessage( "INTIS_SEND_USER_SMS_OPTION_AUTH_TOKEN" ) ?></td>
    </tr>
    <tr>
        <td>
            <input type="text" name="AUTH_TOKEN" id="TOKEN_PARAM" value="<?= htmlspecialcharsbx( $par ) ?>"
                   style="width:500px;">
        </td>
    </tr>
    <tr>
        <td><?= GetMessage( "INTIS_SEND_USER_SMS_SALE_OPTION_GET_KEY" ) ?></td>
    </tr>
    <tr>
        <td><?= GetMessage( "INTIS_SEND_USER_SMS_SALE_OPTION_GET_KEY_MAP" ) ?></td>
    </tr>
    <tr>
        <td><?= GetMessage( "INTIS_SEND_USER_SMS_SALE_OPTION_GET_KEY_ALERT" ) ?></td>
    </tr>
    <tr class="heading">
        <td><? echo GetMessage( "INTIS_SEND_USER_SMS_SALE_OPTION_PROTOCOL" ) ?></td>
    </tr>
    <tr>
        <td>
            <select name="PROTOCOL">
                <option value="https://"<? if( $PROTOCOL == "https://" ): ?> selected<? endif; ?>>https://</option>
                <option value="http://"<? if( $PROTOCOL == "http://" ): ?> selected<? endif; ?>>http://</option>
            </select>
            <input type="hidden" id="PROTOCOL_FIELD" value="<?= $PROTOCOL ?>" readonly>
        </td>
    </tr>
    <tr class="heading">
        <td>
            <? echo GetMessage( "INTIS_SEND_USER_SMS_OPTION_ORIGINATOR" ) ?>
        </td>
    </tr>
    <tr>
        <td>
            <?
            CIntisOriginators::GetOriginator( $par );
            ?>
            <input type="hidden" name="ORIGINATOR_NAME" id="ORIGINATOR_NAME_ID"
                   value="<?= htmlspecialcharsbx( $originator ) ?>" style="width:500px;" placeholder=""><br/><br/>
        </td>
    </tr>
    <tr>
        <td style="padding:10px;background-color:#ffd942;"><?= GetMessage( "INTIS_SEND_USER_SMS_SALE_OPTION_ORIGINATOR_NAME_ALERT" ) ?></td>
    </tr>
    <tr>
        <td><?= GetMessage( "INTIS_SEND_USER_SMS_SALE_OPTION_ORIGINATOR_NAME_MAP" ) ?></td>
    </tr>
    <tr class="heading">
        <td><? echo GetMessage( "INTIS_SEND_USER_SMS_OPTION_ORDER_PHONE" ) ?></td>
    </tr>
    <tr>
        <td>
            <input type="text" name="ORDER_PHONE" id="ORDER_PHONE_ID"
                   value="<?= htmlspecialcharsbx( $orderPhoneFieldValue ) ?>">
            <label><?= GetMessage( "INTIS_SEND_USER_SMS_SALE_OPTION_PHONE_CODE_LABEL" ) ?></label>
        </td>
    </tr>
    <tr class="heading">
        <td><? echo GetMessage( "INTIS_SEND_USER_SMS_OPTION_ADMIN_PHONE" ) ?></td>
    </tr>
    <tr>
        <td>
            <input type="text" name="ADMIN_PHONE" id="ADMIN_PHONE_ID"
                   value="<?= htmlspecialcharsbx( $adminPhoneFieldValue ) ?>" style="width: 300px;">
            <label><?= GetMessage( "INTIS_SEND_USER_SMS_SALE_OPTION_ADMIN_PHONE_LABEL" ) ?></label>
        </td>
    </tr>
    <tr class="heading">
        <td><? echo GetMessage( "INTIS_SEND_USER_SMS_OPTION_USER_PHONE" ) ?></td>
    </tr>
    <tr>
        <td>
            <select name="USER_PHONE">
                <?
                foreach( $arUser as $k => $v ) {
                    $pos = strpos( $k, $p1 );
                    $pos2 = strpos( $k, $p2 );
                    $pos3 = strpos( $k, $p3 );

                    if( $k == $userPhoneValue ) {
                        $s = ' selected';
                    } else {
                        $s = '';
                    }

                    if( $pos !== false || $pos2 !== false || $pos3 !== false ) {
                        echo '<option value="' . $k . '"' . $s . '>' . $k . '</option>';
                    }
                }
                ?>
            </select>
            <input type="hidden" id="USER_PHONE" value="<?= $userPhoneValue ?>"/>
        </td>
    </tr>
    <tr>
        <td>
            <input type="submit" name="<?= GetMessage( "MAIN_OPT_APPLY" ) ?>"
                   value="<?= GetMessage( "INTIS_SEND_USER_SMS_SALE_OPTION_SAVE" ) ?>"
                   title="<?= GetMessage( "MAIN_OPT_APPLY_TITLE" ) ?>">
        </td>
    </tr>
</form>

<?
$rsSites = CSite::GetList( $by = "id", $order = "ASC", array() );
while( $arSite = $rsSites->Fetch() ) {
    $tabControl->BeginNextTab();

    if( $table->FindTable( $arSite[ 'ID' ] ) ) {
        $tbl = $table->GetMainEventList( $arSite[ 'ID' ] );
        $remind = $table->GetRemindTemplate( $arSite[ 'ID' ] );
        ?>
        <form method="POST"
              action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode( $module_id ) ?>&amp;lang=<?= urlencode( LANGUAGE_ID ) ?>">
        <?
        foreach( $tbl as $item ) {
            $PREF = $item[ 'ID' ];
            $db_ptype = CSalePersonType::GetList( Array( "SORT" => "ASC" ), Array( "LID" => $arSite[ 'ID' ] ) );
            ?>
            <tr class="heading">
                <td><? echo GetMessage( "INTIS_SEND_MAIN_TITLE_" . $PREF ) ?></td>
            </tr>

            <tr>
                <td>
                    <div>
                        <?
                        echo '<select id="SELECT_' . $arSite[ 'ID' ] . $PREF . '" onchange="insertMainMacros(\'' . $arSite[ 'ID' ] . $PREF . '\', this.value)">';
                        echo '<option>' . GetMessage( 'MACROS_SELECT' ) . '</option>';
                        echo '<optgroup label="' . GetMessage( 'MACROS_FIRST' ) . '">';
                        echo '<option value="#ORID#">' . GetMessage( 'MACROS_ORID' ) . '</option>';
                        echo '<option value="#ORPRICE#">' . GetMessage( 'MACROS_ORPRICE' ) . '</option>';
                        echo '<option value="#ORCURRENCY#">' . GetMessage( 'MACROS_ORCURRENCY' ) . '</option>';
                        echo '<option value="#ORDISCOUNTVALUE#">' . GetMessage( 'MACROS_ORDISCOUNTVALUE' ) . '</option>';
                        echo '<option value="#ORUSERID#">' . GetMessage( 'MACROS_ORUSERID' ) . '</option>';
                        echo '<option value="#ORPAYSYSTEMID#">' . GetMessage( 'MACROS_ORPAYSYSTEMID' ) . '</option>';
                        echo '<option value="#ORDELIVERYID#">' . GetMessage( 'MACROS_ORDELIVERYID' ) . '</option>';
                        echo '<option value="#ORDELIVERYDOCNUM#">' . GetMessage( 'MACROS_ORDELIVERYDOCNUM' ) . '</option>';
                        echo '<option value="#ORDELIVERYDOCDATE#">' . GetMessage( 'MACROS_ORDELIVERYDOCDATE' ) . '</option>';
                        echo '<option value="#ORDPAYNUM#">' . GetMessage( 'MACROS_ORDPAYNUM' ) . '</option>';
                        echo '<option value="#ORDPAYDOCNUM#">' . GetMessage( 'MACROS_ORDPAYDOCNUM' ) . '</option>';
                        echo '<option value="#ORTRACKINGNUMBER#">' . GetMessage( 'MACROS_ORTRACKINGNUMBER' ) . '</option>';
                        echo '<option value="#ORSTATUSID#">' . GetMessage( 'MACROS_ORSTATUSID' ) . '</option>';
                        echo '<option value="#ORDATESTATUS#">' . GetMessage( 'MACROS_ORDATESTATUS' ) . '</option>';
                        echo '<option value="#ORCOSTDELIVERY#">' . GetMessage( 'MACROS_ORCOSTDELIVERY' ) . '</option>';
                        echo '<option value="#ORDATEALLOWDELIVERY#">' . GetMessage( 'MACROS_ORDATEALLOWDELIVERY' ) . '</option>';
                        echo '<option value="#ORQWITEMS#">' . GetMessage( 'MACROS_ORQWITEMS' ) . '</option>';
                        echo '<option value="#ORQWONE#">' . GetMessage( 'MACROS_ORQWONE' ) . '</option>';
                        echo '<option value="#ORQWTWO#">' . GetMessage( 'MACROS_ORQWTWO' ) . '</option>';
                        echo '<option value="#ORQWTREE#">' . GetMessage( 'MACROS_ORQWTREE' ) . '</option>';
                        echo '<option value="#ORQCANCELDESCRIPTION#">' . GetMessage( 'MACROS_ORQCANCELDESCRIPTION' ) . '</option>';
                        echo '</optgroup>';

                        while( $ptype = $db_ptype->Fetch() ) {
                            $db_propsGroup = CSaleOrderPropsGroup::GetList(
                                array( "SORT" => "ASC" ),
                                array( "PERSON_TYPE_ID" => $ptype[ "ID" ] ),
                                false,
                                false,
                                array()
                            );

                            while( $propsGroup = $db_propsGroup->Fetch() ) {
                                echo '<optgroup label="' . $propsGroup[ "NAME" ] . ' (' . $ptype[ "NAME" ] . ')">';

                                $db_props = CSaleOrderProps::GetList(
                                    array( "SORT" => "ASC" ),
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
                                    if( $props[ 'TYPE' ] == "LOCATION" ) {
                                        echo '<option value="#PR' . 'LOCATIONCOUNTRY' . $props[ 'ID' ] . '#">' . $props[ 'NAME' ] . ' (' . GetMessage( 'LOC_COUNTRY' ) . ') (#PR' . 'LOCATIONCOUNTRY' . $props[ 'ID' ] . '#)</option>';
                                        echo '<option value="#PR' . 'LOCATIONREGION' . $props[ 'ID' ] . '#">' . $props[ 'NAME' ] . ' (' . GetMessage( 'LOC_REGION' ) . ') (#PR' . 'LOCATIONREGION' . $props[ 'ID' ] . '#)</option>';
                                        echo '<option value="#PR' . 'LOCATIONCITY' . $props[ 'ID' ] . '#">' . $props[ 'NAME' ] . ' (' . GetMessage( 'LOC_CITY' ) . ') (#PR' . 'LOCATIONCITY' . $props[ 'ID' ] . '#)</option>';
                                    } else {
                                        echo '<option value="#PR' . $props[ 'CODE' ] . $props[ 'ID' ] . '#">' . $props[ 'NAME' ] . ' (#PR' . $props[ 'CODE' ] . $props[ 'ID' ] . '#)</option>';
                                    }
                                }

                                echo '</optgroup>';
                            }
                        }

                        echo '<optgroup label="' . GetMessage( 'USER_FIELDS' ) . '">';
                        foreach( $arUser as $k => $v ) {
                            $pos = strpos( $k, $p1 );
                            $pos2 = strpos( $k, $p2 );
                            $pos3 = strpos( $k, $p3 );

                            if( $pos !== false || $pos2 !== false || $pos3 !== false ) {
                                echo '<option value="#USERPROP' . $k . '#">' . $k . '</option>';
                            }
                        }

                        $store = CCatalogStore::GetList(
                            array( "ID" => "ASC" ),
                            array(),
                            false,
                            false,
                            array( "ID", "TITLE", "PHONE", "SCHEDULE", "ADDRESS", "DESCRIPTION" )
                        );

                        while( $store_item = $store->Fetch() ) {
                            echo '<optgroup label="' . GetMessage( 'STORE_PROPERTY' ) . ' - ' . $store_item[ 'TITLE' ] . '">';
                            echo '<option value="#STORE' . $store_item[ 'ID' ] . 'TITLE#">' . GetMessage( 'STORE_NAME' ) . ' (#STORE' . $store_item[ 'ID' ] . 'TITLE#)</option>';
                            echo '<option value="#STORE' . $store_item[ 'ID' ] . 'PHONE#">' . GetMessage( 'STORE_PHONE' ) . ' (#STORE' . $store_item[ 'ID' ] . 'PHONE#)</option>';
                            echo '<option value="#STORE' . $store_item[ 'ID' ] . 'SCHEDULE#">' . GetMessage( 'STORE_SCHEDULE' ) . ' (#STORE' . $store_item[ 'ID' ] . 'SCHEDULE#)</option>';
                            echo '<option value="#STORE' . $store_item[ 'ID' ] . 'ADDRESS#">' . GetMessage( 'STORE_ADDRESS' ) . ' (#STORE' . $store_item[ 'ID' ] . 'ADDRESS#)</option>';
                            echo '<option value="#STORE' . $store_item[ 'ID' ] . 'DESCRIPTION#">' . GetMessage( 'STORE_DESCRIPTION' ) . ' (#STORE' . $store_item[ 'ID' ] . 'DESCRIPTION#)</option>';
                        }

                        echo '</select>';
                        ?>
                    </div>
                    <textarea cols="40" rows="2" name="SMS_SALE_MESSAGE_<?= $arSite[ 'ID' ] . $PREF ?>"
                              id="SMS_SALE_MESSAGE_<?= $arSite[ 'ID' ] . $PREF ?>" wrap="off"
                              style="width:100%"><?= $item[ 'TEMPLATE' ] ?></textarea>
                </td>
            </tr>
            <tr>
                <td><input id="SMS_SALE_ACTIVE_<?= $arSite[ 'ID' ] . $PREF ?>"
                           name="SMS_SALE_ACTIVE_<?= $arSite[ 'ID' ] . $PREF ?>"
                           type="checkbox"<? if( $item[ 'ACTIVE' ] == 1 ): ?> checked <? endif; ?> /> <?= GetMessage( "SMS_SALE_ACTIVATE_LABEL" ) ?>

                    <input id="SMS_SALE_COPY_<?= $arSite[ 'ID' ] . $PREF ?>"
                           name="SMS_SALE_COPY_<?= $arSite[ 'ID' ] . $PREF ?>"
                           type="checkbox"<? if( $item[ 'COPY' ] == 1 ): ?> checked <? endif; ?> /> <?= GetMessage( "SMS_SALE_ADMIN_COPY" ) ?>
                </td>
            </tr>
        <?
        }
        ?>
        <tr class="heading">
            <td><? echo GetMessage( "SEND_SALE_REMIND_TITLE" ) ?></td>
        </tr>
        <tr>
            <td>
                <div>
                    <?
                    echo '<select id="SELECT_' . $arSite[ 'ID' ] . '_REMIND" onchange="insertMainMacros(\'' . $arSite[ 'ID' ] . '_REMIND\', this.value)">';
                    echo '<option>' . GetMessage( 'MACROS_SELECT' ) . '</option>';
                    echo '<optgroup label="' . GetMessage( 'MACROS_FIRST' ) . '">';
                    echo '<option value="#ORID#">' . GetMessage( 'MACROS_ORID' ) . '</option>';
                    echo '<option value="#ORPRICE#">' . GetMessage( 'MACROS_ORPRICE' ) . '</option>';
                    echo '<option value="#ORCURRENCY#">' . GetMessage( 'MACROS_ORCURRENCY' ) . '</option>';
                    echo '<option value="#ORDISCOUNTVALUE#">' . GetMessage( 'MACROS_ORDISCOUNTVALUE' ) . '</option>';
                    echo '<option value="#ORUSERID#">' . GetMessage( 'MACROS_ORUSERID' ) . '</option>';
                    echo '<option value="#ORPAYSYSTEMID#">' . GetMessage( 'MACROS_ORPAYSYSTEMID' ) . '</option>';
                    echo '<option value="#ORDELIVERYID#">' . GetMessage( 'MACROS_ORDELIVERYID' ) . '</option>';
                    echo '<option value="#ORDELIVERYDOCNUM#">' . GetMessage( 'MACROS_ORDELIVERYDOCNUM' ) . '</option>';
                    echo '<option value="#ORDELIVERYDOCDATE#">' . GetMessage( 'MACROS_ORDELIVERYDOCDATE' ) . '</option>';
                    echo '<option value="#ORDPAYNUM#">' . GetMessage( 'MACROS_ORDPAYNUM' ) . '</option>';
                    echo '<option value="#ORDPAYDOCNUM#">' . GetMessage( 'MACROS_ORDPAYDOCNUM' ) . '</option>';
                    echo '<option value="#ORTRACKINGNUMBER#">' . GetMessage( 'MACROS_ORTRACKINGNUMBER' ) . '</option>';
                    echo '<option value="#ORSTATUSID#">' . GetMessage( 'MACROS_ORSTATUSID' ) . '</option>';
                    echo '<option value="#ORDATESTATUS#">' . GetMessage( 'MACROS_ORDATESTATUS' ) . '</option>';
                    echo '<option value="#ORCOSTDELIVERY#">' . GetMessage( 'MACROS_ORCOSTDELIVERY' ) . '</option>';
                    echo '<option value="#ORDATEALLOWDELIVERY#">' . GetMessage( 'MACROS_ORDATEALLOWDELIVERY' ) . '</option>';
                    echo '<option value="#ORQWITEMS#">' . GetMessage( 'MACROS_ORQWITEMS' ) . '</option>';
                    echo '<option value="#ORQWONE#">' . GetMessage( 'MACROS_ORQWONE' ) . '</option>';
                    echo '<option value="#ORQWTWO#">' . GetMessage( 'MACROS_ORQWTWO' ) . '</option>';
                    echo '<option value="#ORQWTREE#">' . GetMessage( 'MACROS_ORQWTREE' ) . '</option>';
                    echo '<option value="#ORQCANCELDESCRIPTION#">' . GetMessage( 'MACROS_ORQCANCELDESCRIPTION' ) . '</option>';
                    echo '</optgroup>';

                    while( $ptype = $db_ptype->Fetch() ) {
                        $db_propsGroup = CSaleOrderPropsGroup::GetList(
                            array( "SORT" => "ASC" ),
                            array( "PERSON_TYPE_ID" => $ptype[ "ID" ] ),
                            false,
                            false,
                            array()
                        );

                        while( $propsGroup = $db_propsGroup->Fetch() ) {
                            echo '<optgroup label="' . $propsGroup[ "NAME" ] . ' (' . $ptype[ "NAME" ] . ')">';

                            $db_props = CSaleOrderProps::GetList(
                                array( "SORT" => "ASC" ),
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
                                if( $props[ 'TYPE' ] == "LOCATION" ) {
                                    echo '<option value="#PR' . 'LOCATIONCOUNTRY' . $props[ 'ID' ] . '#">' . $props[ 'NAME' ] . ' (' . GetMessage( 'LOC_COUNTRY' ) . ') (#PR' . 'LOCATIONCOUNTRY' . $props[ 'ID' ] . '#)</option>';
                                    echo '<option value="#PR' . 'LOCATIONREGION' . $props[ 'ID' ] . '#">' . $props[ 'NAME' ] . ' (' . GetMessage( 'LOC_REGION' ) . ') (#PR' . 'LOCATIONREGION' . $props[ 'ID' ] . '#)</option>';
                                    echo '<option value="#PR' . 'LOCATIONCITY' . $props[ 'ID' ] . '#">' . $props[ 'NAME' ] . ' (' . GetMessage( 'LOC_CITY' ) . ') (#PR' . 'LOCATIONCITY' . $props[ 'ID' ] . '#)</option>';
                                } else {
                                    echo '<option value="#PR' . $props[ 'CODE' ] . $props[ 'ID' ] . '#">' . $props[ 'NAME' ] . ' (#PR' . $props[ 'CODE' ] . $props[ 'ID' ] . '#)</option>';
                                }
                            }

                            echo '</optgroup>';
                        }
                    }

                    echo '<optgroup label="' . GetMessage( 'USER_FIELDS' ) . '">';
                    foreach( $arUser as $k => $v ) {
                        $pos = strpos( $k, $p1 );
                        $pos2 = strpos( $k, $p2 );
                        $pos3 = strpos( $k, $p3 );

                        if( $pos !== false || $pos2 !== false || $pos3 !== false ) {
                            echo '<option value="#USERPROP' . $k . '#">' . $k . '</option>';
                        }
                    }

                    $store = CCatalogStore::GetList(
                        array( "ID" => "ASC" ),
                        array(),
                        false,
                        false,
                        array( "ID", "TITLE", "PHONE", "SCHEDULE", "ADDRESS", "DESCRIPTION" )
                    );

                    while( $store_item = $store->Fetch() ) {
                        echo '<optgroup label="' . GetMessage( 'STORE_PROPERTY' ) . ' - ' . $store_item[ 'TITLE' ] . '">';
                        echo '<option value="#STORE' . $store_item[ 'ID' ] . 'TITLE#">' . GetMessage( 'STORE_NAME' ) . ' (#STORE' . $store_item[ 'ID' ] . 'TITLE#)</option>';
                        echo '<option value="#STORE' . $store_item[ 'ID' ] . 'PHONE#">' . GetMessage( 'STORE_PHONE' ) . ' (#STORE' . $store_item[ 'ID' ] . 'PHONE#)</option>';
                        echo '<option value="#STORE' . $store_item[ 'ID' ] . 'SCHEDULE#">' . GetMessage( 'STORE_SCHEDULE' ) . ' (#STORE' . $store_item[ 'ID' ] . 'SCHEDULE#)</option>';
                        echo '<option value="#STORE' . $store_item[ 'ID' ] . 'ADDRESS#">' . GetMessage( 'STORE_ADDRESS' ) . ' (#STORE' . $store_item[ 'ID' ] . 'ADDRESS#)</option>';
                        echo '<option value="#STORE' . $store_item[ 'ID' ] . 'DESCRIPTION#">' . GetMessage( 'STORE_DESCRIPTION' ) . ' (#STORE' . $store_item[ 'ID' ] . 'DESCRIPTION#)</option>';
                    }

                    echo '</select>';
                    ?>
                </div>
                <textarea cols="40" rows="2" name="SMS_SALE_MESSAGE_<?= $arSite[ 'ID' ] ?>_REMIND"
                          id="SMS_SALE_MESSAGE_<?= $arSite[ 'ID' ] ?>_REMIND" wrap="off"
                          style="width:100%"><?= $remind[ 'TEXT' ] ?></textarea>
            </td>
        </tr>
        <tr>
            <td><input id="SMS_SALE_ACTIVE_<?= $arSite[ 'ID' ] ?>_REMIND"
                       name="SMS_SALE_ACTIVE_<?= $arSite[ 'ID' ] ?>_REMIND"
                       type="checkbox"<? if( $remind[ 'ACTIVE' ] == 1 ): ?> checked <? endif; ?> /> <?= GetMessage( "SMS_SALE_ACTIVATE_LABEL" ) ?>

                <input id="SMS_SALE_COPY_<?= $arSite[ 'ID' ] ?>_REMIND"
                       name="SMS_SALE_COPY_<?= $arSite[ 'ID' ] ?>_REMIND"
                       type="checkbox"<? if( $remind[ 'COPY' ] == 1 ): ?> checked <? endif; ?> /> <?= GetMessage( "SMS_SALE_ADMIN_COPY" ) ?>
            </td>
        </tr>
        <tr class="heading">
            <td><? echo GetMessage( "SEND_SALE_STATUSES" ) ?></td>
        </tr>
        <?
        foreach( $arStatusList as $status ) {
            $db_ptype = CSalePersonType::GetList( Array( "SORT" => "ASC" ), Array( "LID" => $arSite[ 'ID' ] ) );
            $st = $table->FindStatus( $arSite[ 'ID' ], $status[ 'ID' ] );
            ?>
            <tr class="heading">
                <td><?= $status[ 'NAME' ] . " (" . $status[ 'ID' ] . ")" ?></td>
            </tr>
            <? if( $st ): ?>
                <tr>
                    <td>
                        <div>
                            <?
                            echo '<select id="SELECT_' . $arSite[ 'ID' ] . $st[ 'ID' ] . '" onchange="insertStatusMacros(\'' . $arSite[ 'ID' ] . $st[ 'ID' ] . '\', this.value)">';
                            echo '<option>' . GetMessage( 'MACROS_SELECT' ) . '</option>';
                            echo '<optgroup label="' . GetMessage( 'MACROS_FIRST' ) . '">';
                            echo '<option value="#ORID#">' . GetMessage( 'MACROS_ORID' ) . '</option>';
                            echo '<option value="#ORPRICE#">' . GetMessage( 'MACROS_ORPRICE' ) . '</option>';
                            echo '<option value="#ORCURRENCY#">' . GetMessage( 'MACROS_ORCURRENCY' ) . '</option>';
                            echo '<option value="#ORDISCOUNTVALUE#">' . GetMessage( 'MACROS_ORDISCOUNTVALUE' ) . '</option>';
                            echo '<option value="#ORUSERID#">' . GetMessage( 'MACROS_ORUSERID' ) . '</option>';
                            echo '<option value="#ORPAYSYSTEMID#">' . GetMessage( 'MACROS_ORPAYSYSTEMID' ) . '</option>';
                            echo '<option value="#ORDELIVERYID#">' . GetMessage( 'MACROS_ORDELIVERYID' ) . '</option>';
                            echo '<option value="#ORDELIVERYDOCNUM#">' . GetMessage( 'MACROS_ORDELIVERYDOCNUM' ) . '</option>';
                            echo '<option value="#ORDELIVERYDOCDATE#">' . GetMessage( 'MACROS_ORDELIVERYDOCDATE' ) . '</option>';
                            echo '<option value="#ORDPAYNUM#">' . GetMessage( 'MACROS_ORDPAYNUM' ) . '</option>';
                            echo '<option value="#ORDPAYDOCNUM#">' . GetMessage( 'MACROS_ORDPAYDOCNUM' ) . '</option>';
                            echo '<option value="#ORTRACKINGNUMBER#">' . GetMessage( 'MACROS_ORTRACKINGNUMBER' ) . '</option>';
                            echo '<option value="#ORSTATUSID#">' . GetMessage( 'MACROS_ORSTATUSID' ) . '</option>';
                            echo '<option value="#ORDATESTATUS#">' . GetMessage( 'MACROS_ORDATESTATUS' ) . '</option>';
                            echo '<option value="#ORCOSTDELIVERY#">' . GetMessage( 'MACROS_ORCOSTDELIVERY' ) . '</option>';
                            echo '<option value="#ORDATEALLOWDELIVERY#">' . GetMessage( 'MACROS_ORDATEALLOWDELIVERY' ) . '</option>';
                            echo '<option value="#ORQWITEMS#">' . GetMessage( 'MACROS_ORQWITEMS' ) . '</option>';
                            echo '<option value="#ORQWTWO#">' . GetMessage( 'MACROS_ORQWTWO' ) . '</option>';
                            echo '<option value="#ORQWTREE#">' . GetMessage( 'MACROS_ORQWTREE' ) . '</option>';
                            echo '</optgroup>';

                            while( $ptype = $db_ptype->Fetch() ) {
                                $db_propsGroup = CSaleOrderPropsGroup::GetList(
                                    array( "SORT" => "ASC" ),
                                    array( "PERSON_TYPE_ID" => $ptype[ "ID" ] ),
                                    false,
                                    false,
                                    array()
                                );

                                while( $propsGroup = $db_propsGroup->Fetch() ) {
                                    echo '<optgroup label="' . $propsGroup[ "NAME" ] . ' (' . $ptype[ "NAME" ] . ')">';

                                    $db_props = CSaleOrderProps::GetList(
                                        array( "SORT" => "ASC" ),
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
                                        if( $props[ 'TYPE' ] == "LOCATION" ) {
                                            echo '<option value="#PR' . 'LOCATIONCOUNTRY' . $props[ 'ID' ] . '#">' . $props[ 'NAME' ] . ' (' . GetMessage( 'LOC_COUNTRY' ) . ') (#PR' . 'LOCATIONCOUNTRY' . $props[ 'ID' ] . '#)</option>';
                                            echo '<option value="#PR' . 'LOCATIONREGION' . $props[ 'ID' ] . '#">' . $props[ 'NAME' ] . ' (' . GetMessage( 'LOC_REGION' ) . ') (#PR' . 'LOCATIONREGION' . $props[ 'ID' ] . '#)</option>';
                                            echo '<option value="#PR' . 'LOCATIONCITY' . $props[ 'ID' ] . '#">' . $props[ 'NAME' ] . ' (' . GetMessage( 'LOC_CITY' ) . ') (#PR' . 'LOCATIONCITY' . $props[ 'ID' ] . '#)</option>';
                                        } else {
                                            echo '<option value="#PR' . $props[ 'CODE' ] . $props[ 'ID' ] . '#">' . $props[ 'NAME' ] . ' (#PR' . $props[ 'CODE' ] . $props[ 'ID' ] . '#)</option>';
                                        }
                                    }

                                    echo '</optgroup>';
                                }
                            }

                            echo '<optgroup label="' . GetMessage( 'USER_FIELDS' ) . '">';
                            foreach( $arUser as $k => $v ) {
                                $string = $k;
                                $pos = strpos( $string, $p1 );
                                $pos2 = strpos( $string, $p2 );
                                $pos3 = strpos( $string, $p3 );

                                if( $pos !== false || $pos2 !== false || $pos3 !== false ) {
                                    echo '<option value="#USERPROP' . $k . '#">' . $k . '</option>';
                                }
                            }

                            $store = CCatalogStore::GetList(
                                array( "ID" => "ASC" ),
                                array(),
                                false,
                                false,
                                array( "ID", "TITLE", "PHONE", "SCHEDULE", "ADDRESS", "DESCRIPTION" )
                            );

                            while( $store_item = $store->Fetch() ) {
                                echo '<optgroup label="' . GetMessage( 'STORE_PROPERTY' ) . ' - ' . $store_item[ 'TITLE' ] . '">';
                                echo '<option value="#STORE' . $store_item[ 'ID' ] . 'TITLE#">' . GetMessage( 'STORE_NAME' ) . ' (#STORE' . $store_item[ 'ID' ] . 'TITLE#)</option>';
                                echo '<option value="#STORE' . $store_item[ 'ID' ] . 'PHONE#">' . GetMessage( 'STORE_PHONE' ) . ' (#STORE' . $store_item[ 'ID' ] . 'PHONE#)</option>';
                                echo '<option value="#STORE' . $store_item[ 'ID' ] . 'SCHEDULE#">' . GetMessage( 'STORE_SCHEDULE' ) . ' (#STORE' . $store_item[ 'ID' ] . 'SCHEDULE#)</option>';
                                echo '<option value="#STORE' . $store_item[ 'ID' ] . 'ADDRESS#">' . GetMessage( 'STORE_ADDRESS' ) . ' (#STORE' . $store_item[ 'ID' ] . 'ADDRESS#)</option>';
                                echo '<option value="#STORE' . $store_item[ 'ID' ] . 'DESCRIPTION#">' . GetMessage( 'STORE_DESCRIPTION' ) . ' (#STORE' . $store_item[ 'ID' ] . 'DESCRIPTION#)</option>';
                            }

                            echo '</select>';
                            ?>
                        </div>
                        <textarea cols="40" rows="2" name="SMS_SALE_STATUS_MESSAGE_<?= $arSite[ 'ID' ] . $st[ 'ID' ] ?>"
                                  id="SMS_SALE_STATUS_MESSAGE_<?= $arSite[ 'ID' ] . $st[ 'ID' ] ?>" wrap="off"
                                  style="width:100%"><?= $st[ 'TEMPLATE' ] ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td><input id="SMS_SALE_STATUS_ACTIVE_<?= $arSite[ 'ID' ] . $st[ 'ID' ] ?>"
                               name="SMS_SALE_STATUS_ACTIVE_<?= $arSite[ 'ID' ] . $st[ 'ID' ] ?>"
                               type="checkbox"<? if( $st[ 'ACTIVE' ] == 1 ): ?> checked <? endif; ?> /> <?= GetMessage( "SMS_SALE_STATUS_ACTIVATE_LABEL" ) ?>

                        <input id="SMS_SALE_STATUS_COPY_<?= $arSite[ 'ID' ] . $st[ 'ID' ] ?>"
                               name="SMS_SALE_STATUS_COPY_<?= $arSite[ 'ID' ] . $st[ 'ID' ] ?>"
                               type="checkbox"<? if( $st[ 'COPY' ] == 1 ): ?> checked <? endif; ?> /> <?= GetMessage( "SMS_SALE_ADMIN_COPY" ) ?>
                    </td>
                </tr>
            <? else: ?>
                <tr id="NEW_STATUS_LINK_<?= $arSite[ 'ID' ] ?><?= $status[ 'ID' ] ?>">
                    <td align="center">
                        <span class="add_template"
                              onclick="setNewStatus('<?= $arSite[ 'ID' ] ?>', '<?= $status[ 'ID' ] ?>')"><?= GetMessage( "ADD_TEMPLATE_FOR_STATUS" ) . "(" . $arSite[ 'ID' ] . ")" ?></span>
                    </td>
                </tr>
                <tr id="NEW_STATUS_FORM_<?= $arSite[ 'ID' ] ?><?= $status[ 'ID' ] ?>" class="display_none">
                    <td>
                        <div>
                            <?
                            echo '<select id="SELECT_' . $arSite[ 'ID' ] . $status[ 'ID' ] . '" onchange="insertStatusMacros(\'' . $arSite[ 'ID' ] . $status[ 'ID' ] . '\', this.value)">';
                            echo '<option>' . GetMessage( 'MACROS_SELECT' ) . '</option>';
                            echo '<optgroup label="' . GetMessage( 'MACROS_FIRST' ) . '">';
                            echo '<option value="#ORID#">' . GetMessage( 'MACROS_ORID' ) . '</option>';
                            echo '<option value="#ORPRICE#">' . GetMessage( 'MACROS_ORPRICE' ) . '</option>';
                            echo '<option value="#ORCURRENCY#">' . GetMessage( 'MACROS_ORCURRENCY' ) . '</option>';
                            echo '<option value="#ORDISCOUNTVALUE#">' . GetMessage( 'MACROS_ORDISCOUNTVALUE' ) . '</option>';
                            echo '<option value="#ORUSERID#">' . GetMessage( 'MACROS_ORUSERID' ) . '</option>';
                            echo '<option value="#ORPAYSYSTEMID#">' . GetMessage( 'MACROS_ORPAYSYSTEMID' ) . '</option>';
                            echo '<option value="#ORDELIVERYID#">' . GetMessage( 'MACROS_ORDELIVERYID' ) . '</option>';
                            echo '<option value="#ORDELIVERYDOCNUM#">' . GetMessage( 'MACROS_ORDELIVERYDOCNUM' ) . '</option>';
                            echo '<option value="#ORDELIVERYDOCDATE#">' . GetMessage( 'MACROS_ORDELIVERYDOCDATE' ) . '</option>';
                            echo '<option value="#ORDPAYNUM#">' . GetMessage( 'MACROS_ORDPAYNUM' ) . '</option>';
                            echo '<option value="#ORDPAYDOCNUM#">' . GetMessage( 'MACROS_ORDPAYDOCNUM' ) . '</option>';
                            echo '<option value="#ORTRACKINGNUMBER#">' . GetMessage( 'MACROS_ORTRACKINGNUMBER' ) . '</option>';
                            echo '<option value="#ORSTATUSID#">' . GetMessage( 'MACROS_ORSTATUSID' ) . '</option>';
                            echo '<option value="#ORDATESTATUS#">' . GetMessage( 'MACROS_ORDATESTATUS' ) . '</option>';
                            echo '<option value="#ORCOSTDELIVERY#">' . GetMessage( 'MACROS_ORCOSTDELIVERY' ) . '</option>';
                            echo '<option value="#ORDATEALLOWDELIVERY#">' . GetMessage( 'MACROS_ORDATEALLOWDELIVERY' ) . '</option>';
                            echo '<option value="#ORQWITEMS#">' . GetMessage( 'MACROS_ORQWITEMS' ) . '</option>';
                            echo '<option value="#ORQWONE#">' . GetMessage( 'MACROS_ORQWONE' ) . '</option>';
                            echo '<option value="#ORQWTWO#">' . GetMessage( 'MACROS_ORQWTWO' ) . '</option>';
                            echo '<option value="#ORQWTREE#">' . GetMessage( 'MACROS_ORQWTREE' ) . '</option>';
                            echo '</optgroup>';

                            while( $ptype = $db_ptype->Fetch() ) {
                                $db_propsGroup = CSaleOrderPropsGroup::GetList(
                                    array( "SORT" => "ASC" ),
                                    array( "PERSON_TYPE_ID" => $ptype[ "ID" ] ),
                                    false,
                                    false,
                                    array()
                                );

                                while( $propsGroup = $db_propsGroup->Fetch() ) {
                                    echo '<optgroup label="' . $propsGroup[ "NAME" ] . ' (' . $ptype[ "NAME" ] . ')">';

                                    $db_props = CSaleOrderProps::GetList(
                                        array( "SORT" => "ASC" ),
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
                                        if( $props[ 'TYPE' ] == "LOCATION" ) {
                                            echo '<option value="#PR' . $props[ 'ID' ] . 'LOCATIONCOUNTRY#">' . $props[ 'NAME' ] . ' (' . GetMessage( 'LOC_COUNTRY' ) . ') (#PR' . $props[ 'ID' ] . 'LOCATIONCOUNTRY#)</option>';
                                            echo '<option value="#PR' . $props[ 'ID' ] . 'LOCATIONREGION#">' . $props[ 'NAME' ] . ' (' . GetMessage( 'LOC_REGION' ) . ') (#PR' . $props[ 'ID' ] . 'LOCATIONREGION#)</option>';
                                            echo '<option value="#PR' . $props[ 'ID' ] . 'LOCATIONCITY#">' . $props[ 'NAME' ] . ' (' . GetMessage( 'LOC_CITY' ) . ') (#PR' . $props[ 'ID' ] . 'LOCATIONCITY#)</option>';
                                        } else {
                                            echo '<option value="#PR' . $props[ 'ID' ] . '#">' . $props[ 'NAME' ] . ' (#PR' . $props[ 'ID' ] . '#)</option>';
                                        }
                                    }

                                    echo '</optgroup>';
                                }
                            }

                            echo '<optgroup label="' . GetMessage( 'USER_FIELDS' ) . '">';
                            foreach( $arUser as $k => $v ) {
                                $string = $k;
                                $pos = strpos( $string, $p1 );
                                $pos2 = strpos( $string, $p2 );
                                $pos3 = strpos( $string, $p3 );

                                if( $pos !== false || $pos2 !== false || $pos3 !== false ) {
                                    echo '<option value="#USERPROP' . $k . '#">' . $k . '</option>';
                                }
                            }

                            $store = CCatalogStore::GetList(
                                array( "ID" => "ASC" ),
                                array(),
                                false,
                                false,
                                array( "ID", "TITLE", "PHONE", "SCHEDULE", "ADDRESS", "DESCRIPTION" )
                            );

                            while( $store_item = $store->Fetch() ) {
                                echo '<optgroup label="' . GetMessage( 'STORE_PROPERTY' ) . ' - ' . $store_item[ 'TITLE' ] . '">';
                                echo '<option value="#STORE' . $store_item[ 'ID' ] . 'TITLE#">' . GetMessage( 'STORE_NAME' ) . ' (#STORE' . $store_item[ 'ID' ] . 'TITLE#)</option>';
                                echo '<option value="#STORE' . $store_item[ 'ID' ] . 'PHONE#">' . GetMessage( 'STORE_PHONE' ) . ' (#STORE' . $store_item[ 'ID' ] . 'PHONE#)</option>';
                                echo '<option value="#STORE' . $store_item[ 'ID' ] . 'SCHEDULE#">' . GetMessage( 'STORE_SCHEDULE' ) . ' (#STORE' . $store_item[ 'ID' ] . 'SCHEDULE#)</option>';
                                echo '<option value="#STORE' . $store_item[ 'ID' ] . 'ADDRESS#">' . GetMessage( 'STORE_ADDRESS' ) . ' (#STORE' . $store_item[ 'ID' ] . 'ADDRESS#)</option>';
                                echo '<option value="#STORE' . $store_item[ 'ID' ] . 'DESCRIPTION#">' . GetMessage( 'STORE_DESCRIPTION' ) . ' (#STORE' . $store_item[ 'ID' ] . 'DESCRIPTION#)</option>';
                            }

                            echo '</select>';
                            ?>
                        </div>
                        <textarea cols="40" rows="2"
                                  name="SMS_SALE_STATUS_MESSAGE_<?= $arSite[ 'ID' ] . $status[ 'ID' ] ?>"
                                  id="SMS_SALE_STATUS_MESSAGE_<?= $arSite[ 'ID' ] . $status[ 'ID' ] ?>" wrap="off"
                                  style="width:100%"><?= GetMessage( 'NEW_STATUS_TEMPLATE' ) ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="hidden" id="NEW_STATUS_<?= $arSite[ 'ID' ] . $status[ 'ID' ] ?>"
                               name="NEW_STATUS_<?= $arSite[ 'ID' ] ?>[]" value=""/>
                        <input id="SMS_SALE_STATUS_ACTIVE_<?= $arSite[ 'ID' ] . $status[ 'ID' ] ?>"
                               name="SMS_SALE_STATUS_ACTIVE_<?= $arSite[ 'ID' ] . $status[ 'ID' ] ?>"
                               type="checkbox"<? if( $st[ 'ACTIVE' ] == 1 ): ?> checked <? endif; ?> /> <?= GetMessage( "SMS_SALE_STATUS_ACTIVATE_LABEL" ) ?>
                    </td>
                </tr>
            <?endif; ?>
        <?
        }
        ?>
        <tr>
            <td>
                <input type="submit" name="<?= GetMessage( "MAIN_OPT_APPLY" ) ?>"
                       value="<?= GetMessage( "INTIS_SEND_USER_SMS_SALE_OPTION_SAVE" ) ?>"
                       title="<?= GetMessage( "MAIN_OPT_APPLY_TITLE" ) ?>">
            </td>
        </tr>
        </form>
    <?
    } else {
        ?>
        <form method="POST"
              action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode( $module_id ) ?>&amp;lang=<?= urlencode( LANGUAGE_ID ) ?>">
            <input name="ADD_SETTINGS" type="hidden" value="<?= $arSite[ 'ID' ] ?>"/>
            <input type="submit" name="<?= GetMessage( "MAIN_OPT_APPLY" ) ?>"
                   value="<?= GetMessage( "INTIS_SEND_USER_SMS_SALE_OPTION_ADD" ) ?>"/>
        </form>
    <?
    }
}
?>
<?
$tabControl->BeginNextTab();
if( $userPhoneValue ) {
    echo CAdminMessage::ShowNote( GetMessage( 'INTIS_SEND_USER_SMS_USER_PHONE_OK' ) );
    echo CAdminMessage::ShowMessage( GetMessage( 'INTIS_SEND_USER_SMS_USER_PHONE_ERR2' ) );
} else {
    echo CAdminMessage::ShowMessage( GetMessage( 'INTIS_SEND_USER_SMS_USER_PHONE_ERR' ) );
}

?>
<form method="POST"
      action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode( $module_id ) ?>&amp;lang=<?= urlencode( LANGUAGE_ID ) ?>">
    <tr class="heading">
        <td><?= GetMessage( 'INTIS_SEND_USER_SMS_REGISTER_TITLE_REG' ) ?></td>
    </tr>
    <tr>
        <td>
            <input name="USER_REGISTER_ON_OFF" value="on"
                   type="radio"<? if( $reg_on == 'on' ): ?> checked<? endif; ?> /> <?= GetMessage( "INTIS_SEND_USER_SMS_REGISTER_ON_OFF" ) ?>
            <input name="USER_REGISTER_ON_OFF" value="off"
                   type="radio"<? if( $reg_on == 'off' ): ?> checked<? endif; ?> /> <?= GetMessage( "INTIS_SEND_USER_SMS_REGISTER_ON_OFF2" ) ?>
            <input type="hidden" id="USER_REGISTER_ON_OFF_VALUE" name="USER_REGISTER_ON_OFF_VALUE"
                   value="<?= $reg_on ?>" readonly/>
            <br/>
            <br/>
            <?
            echo '<select id="INSERT_USER_REG_MACROS" onchange="insertRegMacros(\'USER_REGISTER_TEMPLATE\', this.value)">';
            echo '<option>' . GetMessage( 'MACROS_SELECT' ) . '</option>';
            echo '<option value="#REGUSERID#">' . GetMessage( 'MACROS_REGUSERID' ) . '</option>';
            echo '<option value="#REGLOGIN#">' . GetMessage( 'MACROS_REGLOGIN' ) . '</option>';
            echo '<option value="#REGNAME#">' . GetMessage( 'MACROS_REGNAME' ) . '</option>';
            echo '<option value="#REGLASTNAME#">' . GetMessage( 'MACROS_REGLASTNAME' ) . '</option>';
            echo '<option value="#REGPASSWORD#">' . GetMessage( 'MACROS_REGPASSWORD' ) . '</option>';
            echo '<option value="#REGEMAIL#">' . GetMessage( 'MACROS_REGEMAIL' ) . '</option>';
            echo '<option value="#REGUSERIP#">' . GetMessage( 'MACROS_REGUSERIP' ) . '</option>';
            echo '<select>';
            ?>
            <textarea cols="40" rows="3" id="USER_REGISTER_TEMPLATE" name="USER_REGISTER_TEMPLATE" style="width:100%"
                      placeholder="<?= GetMessage( 'RULE_ENTER_TEXT' ) ?>"><?= $reg_tpl[ 'REGISTER' ][ 'TEMPLATE' ] ?></textarea>
        </td>
    </tr>
    <tr>
        <td>
            <input type="submit" name="<?= GetMessage( "MAIN_OPT_APPLY" ) ?>"
                   value="<?= GetMessage( "INTIS_SEND_USER_SMS_SALE_OPTION_SAVE" ) ?>"
                   title="<?= GetMessage( "MAIN_OPT_APPLY_TITLE" ) ?>">
        </td>
    </tr>
</form>
<form method="POST"
      action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode( $module_id ) ?>&amp;lang=<?= urlencode( LANGUAGE_ID ) ?>">
    <tr class="heading">
        <td><?= GetMessage( 'INTIS_SEND_USER_SMS_REGISTER_TITLE_AUTH' ) ?></td>
    </tr>
    <tr>
        <td>
            <input name="USER_AUTH_ON_OFF" value="on"
                   type="radio"<? if( $auth_on == 'on' ): ?> checked<? endif; ?> /> <?= GetMessage( "INTIS_SEND_USER_SMS_AUTH_ON_OFF" ) ?>
            <input name="USER_AUTH_ON_OFF" value="off"
                   type="radio"<? if( $auth_on == 'off' ): ?> checked<? endif; ?> /> <?= GetMessage( "INTIS_SEND_USER_SMS_AUTH_ON_OFF2" ) ?>
            <input type="hidden" id="USER_AUTH_ON_OFF_VALUE" name="USER_AUTH_ON_OFF_VALUE" value="<?= $reg_on ?>"
                   readonly/>
            <br/>
            <br/>
            <?
            echo '<select id="INSERT_USER_AUTH_MACROS" onchange="insertAuthMacros(\'USER_AUTH_TEMPLATE\', this.value)">';
            echo '<option>' . GetMessage( 'MACROS_SELECT' ) . '</option>';
            echo '<option value="#REGUSERID#">' . GetMessage( 'MACROS_REGUSERID' ) . '</option>';
            echo '<option value="#REGLOGIN#">' . GetMessage( 'MACROS_REGLOGIN' ) . '</option>';
            echo '<option value="#REGNAME#">' . GetMessage( 'MACROS_REGNAME' ) . '</option>';
            echo '<option value="#REGLASTNAME#">' . GetMessage( 'MACROS_REGLASTNAME' ) . '</option>';
            echo '<option value="#REGEMAIL#">' . GetMessage( 'MACROS_REGEMAIL' ) . '</option>';
            echo '<option value="#REGUSERIP#">' . GetMessage( 'MACROS_REGUSERIP' ) . '</option>';
            echo '<select>';
            ?>
            <textarea cols="40" rows="3" id="USER_AUTH_TEMPLATE" name="USER_AUTH_TEMPLATE" style="width:100%"
                      placeholder="<?= GetMessage( 'RULE_ENTER_TEXT' ) ?>"><?= $reg_tpl[ 'AUTH' ][ 'TEMPLATE' ] ?></textarea>
        </td>
    </tr>
    <tr>
        <td>
            <input type="submit" name="<?= GetMessage( "MAIN_OPT_APPLY" ) ?>"
                   value="<?= GetMessage( "INTIS_SEND_USER_SMS_SALE_OPTION_SAVE" ) ?>"
                   title="<?= GetMessage( "MAIN_OPT_APPLY_TITLE" ) ?>">
        </td>
    </tr>
</form>

<?
$tabControl->BeginNextTab();
?>
<tr>
    <td>
        <?= GetMessage( 'RULE_TEXT' ) ?>
    </td>
</tr>
<tr class="heading">
    <td><?= GetMessage( 'RULE_TITLE' ) ?></td>
</tr>
<tr>
    <td>
        <form method="POST"
              action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode( $module_id ) ?>&amp;lang=<?= urlencode( LANGUAGE_ID ) ?>">
            <?
            if( $rules ) {
                foreach( $rules as $rule ) {
                    if( $rule[ 'ACTIVE' ] == 1 ) {
                        $rule_status = GetMessage( 'RULE_ACTIVE' );
                    } else {
                        $rule_status = GetMessage( 'RULE_INACTIVE' );
                    }

                    $ev = $rule[ 'EVENT' ];
                    if( substr( $ev, 0, 3 ) == "ST_" ) {
                        $event_id = GetMessage( 'RULE_STATUS' ) . substr( $ev, 3 );
                    } elseif( substr( $ev, 0, 3 ) == "OR_" ) {
                        $ev = substr( $ev, 3 );
                        if( $ev == "NEW" ) {
                            $event_id = GetMessage( 'RULE_NEW_ORDER' );
                        } elseif( $ev == "PAY" ) {
                            $event_id = GetMessage( 'RULE_NEW_PAY' );
                        } elseif( $ev == "CANCEL" ) {
                            $event_id = GetMessage( 'RULE_NEW_CANCEL' );
                        } elseif( $ev == "DELIVER" ) {
                            $event_id = GetMessage( 'RULE_NEW_DELIVER' );
                        }
                    }

                    if( $rule[ 'FIRST_RULE' ] == "first_one" ) {
                        $mess1 = GetMessage( 'RULE_FIRST_ONE' );
                    } elseif( $rule[ 'FIRST_RULE' ] == "first_two" ) {
                        $mess1 = GetMessage( 'RULE_FIRST_TWO' );
                    } elseif( $rule[ 'FIRST_RULE' ] == "first_tree" ) {
                        $mess1 = GetMessage( 'RULE_FIRST_TREE' );
                    }

                    if( $rule[ 'FIRST_RULE_PERIOD' ] ) {
                        if( $rule[ 'FIRST_RULE_PERIOD' ] == "1 day" ) {
                            $mess2 = " 1 " . GetMessage( 'RULE_SELECT_PERIOD1' );
                        } elseif( $rule[ 'FIRST_RULE_PERIOD' ] == "1 week" ) {
                            $mess2 = " 1 " . GetMessage( 'RULE_SELECT_PERIOD2' );
                        } elseif( $rule[ 'FIRST_RULE_PERIOD' ] == "1 month" ) {
                            $mess2 = " 1 " . GetMessage( 'RULE_SELECT_PERIOD3' );
                        } elseif( $rule[ 'FIRST_RULE_PERIOD' ] == "3 month" ) {
                            $mess2 = " " . GetMessage( 'RULE_SELECT_PERIOD4' );
                        } elseif( $rule[ 'FIRST_RULE_PERIOD' ] == "6 month" ) {
                            $mess2 = " " . GetMessage( 'RULE_SELECT_PERIOD5' );
                        } elseif( $rule[ 'FIRST_RULE_PERIOD' ] == "1 year" ) {
                            $mess2 = " 1 " . GetMessage( 'RULE_SELECT_PERIOD6' );
                        }
                    } else {
                        $mess2 = "";
                    }

                    if( $rule[ 'FIRST_RULE_PERIOD_RULE' ] ) {
                        if( $rule[ 'FIRST_RULE_PERIOD_RULE' ] == "less" ) {
                            $mess3 = GetMessage( 'RULE_FIRST_RULE_PERIOD_RULE_LESS' );
                        } elseif( $rule[ 'FIRST_RULE_PERIOD_RULE' ] == "more" ) {
                            $mess3 = GetMessage( 'RULE_FIRST_RULE_PERIOD_RULE_MORE' );
                        }
                    } else {
                        $mess3 = "";
                    }

                    if( $rule[ 'FIRST_RULE_VALUE' ] ) {
                        $mess4 = $rule[ 'FIRST_RULE_VALUE' ];
                    } else {
                        $mess4 = "";
                    }

                    if( $rule[ 'THIRD_RULE' ] ) {
                        if( $rule[ 'THIRD_RULE' ] == "generate_coupon" ) {
                            $mess5 = GetMessage( 'THIRD_RULE1' ) . $rule[ 'THIRD_RULE_DISCOUNT_ID' ];
                        } elseif( $rule[ 'THIRD_RULE' ] == "add_money" ) {
                            $mess5 = GetMessage( 'THIRD_RULE2' ) . $rule[ 'THIRD_RULE_VALUE' ];
                        } elseif( $rule[ 'THIRD_RULE' ] == "add_percent" ) {
                            $mess5 = GetMessage( 'THIRD_RULE3' ) . $rule[ 'THIRD_RULE_VALUE' ] . "%";
                        }
                    } else {
                        $mess5 = "";
                    }
                    ?>
                    <div>
                        <table<? if( $rule[ 'ACTIVE' ] == 1 ): ?> style="background: #c7ffc7;"<? else: ?> style="background: #ffb29d;"<? endif; ?>>
                            <tr>
                                <td>
                                    <input type="radio" name="RULE_ID[<?= $rule[ 'ID' ] ?>_<?= $rule[ 'SITE' ] ?>]"
                                           value="ACTIVE"<? if( $rule[ 'ACTIVE' ] == 1 ): ?> checked <? endif; ?> /> <?= GetMessage( 'RULE_ACTIVE2' ) ?>
                                    <input type="radio" name="RULE_ID[<?= $rule[ 'ID' ] ?>_<?= $rule[ 'SITE' ] ?>]"
                                           value="INACTIVE"<? if( $rule[ 'ACTIVE' ] == 0 ): ?> checked <? endif; ?> /> <?= GetMessage( 'RULE_INACTIVE2' ) ?>
                                    <input type="radio" name="RULE_ID[<?= $rule[ 'ID' ] ?>_<?= $rule[ 'SITE' ] ?>]"
                                           value="DELETE"/> <?= GetMessage( 'RULE_DELETE' ) ?>
                                </td>
                                <td>
                                    <?= GetMessage( 'RULE_STATE' ) ?> <?= $rule_status ?><br/>
                                    <?= GetMessage( 'RULE_SITE' ) ?> <?= $rule[ 'SITE' ] ?><br/>
                                    <?= GetMessage( 'RULE_EVENT' ) ?> <?= $event_id ?><br/>
                                    <?= GetMessage( 'RULE_RULE' ) ?> <?= $mess1 ?><?= $mess2 ?><?= $mess3 ?> <?= $rule[ 'FIRST_RULE_VALUE' ] ?><?= $mess5 ?>
                                    <br/>
                                    <?= GetMessage( 'RULE_SMS' ) ?> <?= $rule[ 'TEMPLATE' ] ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                <?
                }
            }
            ?>
            <div style="margin-top: 15px;">
                <input type="submit" name="<?= GetMessage( "MAIN_OPT_APPLY" ) ?>"
                       value="<?= GetMessage( "UPDATE_RULE" ) ?>"/>
            </div>
        </form>
    </td>
</tr>
<tr class="heading">
    <td><?= GetMessage( 'RULE_NEW_RULE' ) ?></td>
</tr>
<tr id="NEW_RULE_TR">
    <td align="center">
        <span class="add_template" onclick="addNewRule()"><?= GetMessage( "ADD_NEW_RULE" ) ?></span>
    </td>
</tr>
<tr id="ADD_RULE_FORM" class="display_none">
    <td>
        <form method="POST"
              action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode( $module_id ) ?>&amp;lang=<?= urlencode( LANGUAGE_ID ) ?>">
            <div>
                1. <select name="FIRST" id="FIRST_SELECT" onchange="showNewtStep('SECOND', this.value)">
                    <option value=""><?= GetMessage( 'RULE_SELECT_IF' ) ?></option>
                    <option value="first_one"><?= GetMessage( 'RULE_FIRST_ONE' ) ?></option>
                    <option value="first_two"><?= GetMessage( 'RULE_FIRST_TWO' ) ?></option>
                    <option value="first_tree"><?= GetMessage( 'RULE_FIRST_TREE' ) ?></option>
                </select>
            </div>
            <div id="SECOND" class="display_none">
                2. <select name="SECOND_PERIOD" id="SECOND_PERIOD" class="display_none">
                    <option value=""><?= GetMessage( 'RULE_SELECT_PERIOD' ) ?></option>
                    <option value="1 day"><?= GetMessage( 'RULE_SELECT_PERIOD1' ) ?></option>
                    <option value="1 week"><?= GetMessage( 'RULE_SELECT_PERIOD2' ) ?></option>
                    <option value="1 month"><?= GetMessage( 'RULE_SELECT_PERIOD3' ) ?></option>
                    <option value="3 month"><?= GetMessage( 'RULE_SELECT_PERIOD4' ) ?></option>
                    <option value="6 month"><?= GetMessage( 'RULE_SELECT_PERIOD5' ) ?></option>
                    <option value="1 year"><?= GetMessage( 'RULE_SELECT_PERIOD6' ) ?></option>
                </select>
                <select name="SECOND_PERIOD_VAL" id="SECOND_PERIOD_VAL" class="display_none">
                    <option value=""><?= GetMessage( 'RULE_SELECT_IF' ) ?></option>
                    <option value="less"><?= GetMessage( 'RULE_SELECT_PERIOD7' ) ?></option>
                    <option value="more"><?= GetMessage( 'RULE_SELECT_PERIOD8' ) ?></option>
                </select>
                <input type="text" id="SECOND_SUMM" name="SECOND_SUMM"/>
            </div>
            <div id="THIRD" class="display_none">
                3. <select name="THIRD" id="THIRD_SELECT" onchange="showLastStep(this.value)">
                    <option value=""><?= GetMessage( 'RULE_SELECT_IF' ) ?></option>
                    <option value="generate_coupon"><?= GetMessage( 'RULE_SELECT_COUPON' ) ?></option>
                    <option value="add_money"><?= GetMessage( 'RULE_SELECT_ADD_MONEY' ) ?></option>
                    <option value="add_percent"><?= GetMessage( 'RULE_SELECT_ADD_PERCENT' ) ?></option>
                </select>
                <select id="SELECT_DISCOUNT" name="SELECT_DISCOUNT" class="display_none">
                    <option value=""><?= GetMessage( 'RULE_SELECT_DISCOUNT' ) ?></option>
                    <?
                    while( $ar_res = $rsDiscounts->Fetch() ) {
                        echo '<option value="' . $ar_res[ 'ID' ] . '">' . $ar_res[ 'NAME' ] . " (" . $ar_res[ 'SITE_ID' ] . ')</option>';
                    }
                    ?>
                </select>
                <span id="FINAL_STEP_VALUE" class="display_none"><input type="text" id="FINAL_STEP_VALUE2"
                                                                        name="FINAL_STEP_VALUE" value=""/></span>
            </div>
            <div id="FOUR" class="display_none">
                4. <select id="SITE_RULE" name="SITE_RULE_ID" onchange="selectEvent(this.value)">
                    <option value=""><?= GetMessage( 'RULE_SELECT_SITE' ) ?></option>
                    <?
                    $rsSites2 = CSite::GetList( $by = "id", $order = "asc", Array( "NAME" => "" ) );
                    while( $arGetSite2 = $rsSites2->Fetch() ) {
                        echo '<option value="' . $arGetSite2[ 'ID' ] . '">' . GetMessage( 'RULE_SELECT_SITE2' ) . $arGetSite2[ 'ID' ] . " (" . $arGetSite2[ 'NAME' ] . ')</option>';
                    }
                    ?>
                </select>
            </div>
            <div id="FIVE" class="display_none">
                5. <select id="EVENT_ID" name="EVENT_ID" onchange="showSubmit(this.value)">
                    <option value=""><?= GetMessage( 'RULE_SELECT_EVENT' ) ?></option>
                    <option value="OR_NEW"><?= GetMessage( 'RULE_NEW_ORDER' ) ?></option>
                    <option value="OR_CANCEL"><?= GetMessage( 'RULE_NEW_CANCEL' ) ?></option>
                    <option value="OR_PAY"><?= GetMessage( 'RULE_NEW_PAY' ) ?></option>
                    <option value="OR_DELIVER"><?= GetMessage( 'RULE_NEW_DELIVER' ) ?></option>
                    <?
                    foreach( $arStatusList as $stl ) {
                        echo '<option value="ST_' . $stl[ 'ID' ] . '">' . GetMessage( 'RULE_STATUS' ) . $stl[ 'NAME' ] . ' (' . $stl[ 'ID' ] . ')</option>';
                    }
                    ?>
                </select>
            </div>
            <div id="SIX" class="display_none">
                <div>
                    6. <select id="SELECT_RULE" name="SELECT_RULE" onchange="insertRuleMacros('RULE', this.value)">
                        <option value=""><?= GetMessage( 'MACROS_SELECT' ) ?></option>
                        <option value="#ORID#"><?= GetMessage( 'MACROS_ORID' ) ?></option>
                        <option value="#ORUSERID#"><?= GetMessage( 'MACROS_ORUSERID' ) ?></option>
                        <option value="#COUPON#"><?= GetMessage( 'MACROS_COUPON' ) ?></option>
                        <option value="#SUMM#"><?= GetMessage( 'MACROS_SUMM' ) ?></option>
                        <option value="#BUDGET#"><?= GetMessage( 'MACROS_BUDGET' ) ?></option>
                        <option value="#TOTAL#"><?= GetMessage( 'MACROS_BUDGET2' ) ?></option>
                        <option value="#B_CUR#"><?= GetMessage( 'MACROS_B_CUR' ) ?></option>
                    </select>
                </div>
                <textarea cols="40" rows="2" name="NEW_RULE_TEMPLATE" id="NEW_RULE_TEMPLATE" wrap="off"
                          style="width:100%" placeholder="<?= GetMessage( 'RULE_ENTER_TEXT' ) ?>"></textarea>
            </div>
            <input type="hidden" name="NEW_RULE" value="NEW_RULE"/>

            <div style="margin-top: 20px;" id="RULE_SUBMIT" class="display_none">
                <input type="submit" name="<?= GetMessage( "MAIN_OPT_APPLY" ) ?>"
                       value="<?= GetMessage( "ADD_NEW_RULE" ) ?>"/>
            </div>
        </form>
    </td>
</tr>
<?
//6 tab begin
if( COption::GetOptionString( "intis.senduserssms", "TOKEN_PARAM", "" ) == true ) {
    $tabControl->BeginNextTab();
    ?>
    <tr class="heading">
        <td><? echo GetMessage( "INTIS_SEND_USER_SMS_SALE_OPTION_GET_BALANCE" ) ?></td>
    </tr>
    <tr>
        <td>
            <?
            echo GetMessage( "INTIS_SEND_USER_SMS_SALE_OPTION_BALANCE_VALUE" ) . " ";
            echo CBalance::Get();
            ?>
        </td>
    </tr>
<?
}
?>

<?
//7 tab begin
$tabControl->BeginNextTab();
?>
<? require_once( $_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/main/admin/group_rights.php" ); ?>
<?
if( $REQUEST_METHOD == "POST" && strlen( $Update . $Apply . $RestoreDefaults ) > 0 && check_bitrix_sessid() ) {
    if( strlen( $Update ) > 0 && strlen( $_REQUEST[ "back_url_settings" ] ) > 0 )
        LocalRedirect( $_REQUEST[ "back_url_settings" ] );
    else
        LocalRedirect( $APPLICATION->GetCurPage() . "?mid=" . urlencode( $mid ) . "&lang=" . urlencode( LANGUAGE_ID ) . "&back_url_settings=" . urlencode( $_REQUEST[ "back_url_settings" ] ) . "&" . $tabControl->ActiveTabParam() );
}
?>

</td></tr>

<?= bitrix_sessid_post(); ?>
<? $tabControl->End(); ?>
<style>
    .add_template{
        color:#2675d7;
        border-bottom:1px dotted #2675d7;
    }
    .add_template:hover{
        cursor:pointer;
    }
    .display_none{
        display:none;
    }
</style>
<script>
    function insertRuleMacros(id, val) {
        text = document.getElementById("NEW_RULE_TEMPLATE");
        text.focus();
        text.value += val;

        document.getElementById("SELECT_" + id).options["0"].selected = true;

    }

    function showSubmit(val) {
        if (val) {
            document.getElementById('SIX').className = document.getElementById('SIX').className.replace(/(?:^|\s)display_none(?!\S)/, '');
            document.getElementById('RULE_SUBMIT').className = document.getElementById('RULE_SUBMIT').className.replace(/(?:^|\s)display_none(?!\S)/, '');
        } else {
            document.getElementById("RULE_SUBMIT").className = "display_none";
            document.getElementById("SIX").className = "display_none";
            document.getElementById("SELECT_RULE").options["0"].selected = true;
        }
    }

    function selectEvent(val) {
        if (val) {
            document.getElementById('FIVE').className = document.getElementById('FIVE').className.replace(/(?:^|\s)display_none(?!\S)/, '');
        } else {
            document.getElementById("EVENT_ID").options["0"].selected = true;
            document.getElementById("FIVE").className = "display_none";
            document.getElementById("RULE_SUBMIT").className = "display_none";
            document.getElementById("SIX").className = "display_none";
            document.getElementById("SELECT_RULE").options["0"].selected = true;
        }
    }

    function showLastStep(val) {
        if (val == "generate_coupon") {
            text = document.getElementById("FINAL_STEP_VALUE2");
            text.value = "";

            document.getElementById("FINAL_STEP_VALUE").className = "display_none";
            document.getElementById('SELECT_DISCOUNT').className = document.getElementById('SELECT_DISCOUNT').className.replace(/(?:^|\s)display_none(?!\S)/, '');
            document.getElementById('FOUR').className = document.getElementById('FOUR').className.replace(/(?:^|\s)display_none(?!\S)/, '');
        }

        if (val == "add_money" || val == "add_percent") {
            document.getElementById("SELECT_DISCOUNT").options["0"].selected = true;
            document.getElementById("SELECT_DISCOUNT").className = "display_none";
            document.getElementById('FINAL_STEP_VALUE').className = document.getElementById('FINAL_STEP_VALUE').className.replace(/(?:^|\s)display_none(?!\S)/, '');
            document.getElementById('FOUR').className = document.getElementById('FOUR').className.replace(/(?:^|\s)display_none(?!\S)/, '');
        }

        if (!val) {
            text = document.getElementById("FINAL_STEP_VALUE2");
            text.value = "";
            document.getElementById("FINAL_STEP_VALUE").className = "display_none";
            document.getElementById("FOUR").className = "display_none";
            document.getElementById("SITE_RULE").options["0"].selected = true;
            document.getElementById("SELECT_DISCOUNT").options["0"].selected = true;
            document.getElementById("SELECT_DISCOUNT").className = "display_none";
            document.getElementById("FIVE").className = "display_none";
            document.getElementById("RULE_SUBMIT").className = "display_none";
            document.getElementById("SIX").className = "display_none";
            document.getElementById("SELECT_RULE").options["0"].selected = true;
        }
    }

    function showNewtStep(id, val) {
        if (val == "first_tree") {
            document.getElementById('SECOND_PERIOD').className = document.getElementById('SECOND_PERIOD').className.replace(/(?:^|\s)display_none(?!\S)/, '');
            document.getElementById('SECOND_PERIOD_VAL').className = document.getElementById('SECOND_PERIOD_VAL').className.replace(/(?:^|\s)display_none(?!\S)/, '');
        }

        if (val == "first_two" || val == "first_one") {
            document.getElementById("SECOND_PERIOD").options["0"].selected = true;
            document.getElementById("SECOND_PERIOD_VAL").options["0"].selected = true;

            document.getElementById("SECOND_PERIOD").className = "display_none";
            document.getElementById("SECOND_PERIOD_VAL").className = "display_none";
        }

        document.getElementById(id).className = document.getElementById(id).className.replace(/(?:^|\s)display_none(?!\S)/, '');

        if (id == "SECOND") {
            document.getElementById('THIRD').className = document.getElementById('THIRD').className.replace(/(?:^|\s)display_none(?!\S)/, '');
        }

        if (!val) {
            text = document.getElementById("SECOND_SUMM");
            text.value = "";

            text = document.getElementById("FINAL_STEP_VALUE2");
            text.value = "";

            document.getElementById("SECOND").className = "display_none";
            document.getElementById("FINAL_STEP_VALUE").className = "display_none";
            document.getElementById("FOUR").className = "display_none";
            document.getElementById("SITE_RULE").options["0"].selected = true;
            document.getElementById("THIRD_SELECT").options["0"].selected = true;
            document.getElementById("SELECT_DISCOUNT").options["0"].selected = true;
            document.getElementById("SELECT_DISCOUNT").className = "display_none";
            document.getElementById("FIVE").className = "display_none";
            document.getElementById("RULE_SUBMIT").className = "display_none";
            document.getElementById("THIRD").className = "display_none";
            document.getElementById("SIX").className = "display_none";
            document.getElementById("SELECT_RULE").options["0"].selected = true;
        }
    }

    function insertMainMacros(id, val) {
        text = document.getElementById("SMS_SALE_MESSAGE_" + id);
        text.focus();
        text.value += val;

        document.getElementById("SELECT_" + id).options["0"].selected = true;

    }

    function insertRegMacros(id, val) {
        text = document.getElementById("USER_REGISTER_TEMPLATE");
        text.focus();
        text.value += val;

        document.getElementById("INSERT_USER_REG_MACROS").options["0"].selected = true;

    }

    function insertAuthMacros(id, val) {
        text = document.getElementById("USER_AUTH_TEMPLATE");
        text.focus();
        text.value += val;

        document.getElementById("INSERT_USER_AUTH_MACROS").options["0"].selected = true;

    }

    function insertStatusMacros(id, val) {
        text = document.getElementById("SMS_SALE_STATUS_MESSAGE_" + id);
        text.focus();
        text.value += val;

        document.getElementById("SELECT_" + id).options["0"].selected = true;
    }

    function setNewStatus(sid, id) {
        text = document.getElementById("NEW_STATUS_" + sid + id);
        text.focus();
        text.value += id;

        document.getElementById("SMS_SALE_MESSAGE_" + sid + id);
        document.getElementById("NEW_STATUS_LINK_" + sid + id).className = "display_none";
        document.getElementById('NEW_STATUS_FORM_' + sid + id).className = document.getElementById('NEW_STATUS_FORM_' + sid + id).className.replace(/(?:^|\s)display_none(?!\S)/, '');
    }

    function addNewRule() {
        document.getElementById("NEW_RULE_TR").className = "display_none";
        document.getElementById('ADD_RULE_FORM').className = document.getElementById('ADD_RULE_FORM').className.replace(/(?:^|\s)display_none(?!\S)/, '');
    }
</script>

<? require( $_SERVER[ "DOCUMENT_ROOT" ] . "/bitrix/modules/main/include/epilog_admin.php" ); ?>
