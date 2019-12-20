<?
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

CModule::IncludeModule("iblock");
CModule::IncludeModule("highloadblock");

class BitrixMarket
{
    const HLBLOCK_BRAND = 1;
    const HLBLOCK_FEATURES = 2;
    const HLBLOCK_EQUIPMENT = 3;
    const IBLOCK_CATALOG = 8;
    const MAX_PROPS_CONTAINER = 3;

    const SHOP_NAME = "ТЕКСТИЛЬТОРГ";
    const SHOP_COMPANY = "ТЕКСТИЛЬТОРГ - продажа бытового и промышленного оборудования";
    const DEFAULT_CURRENCY = "RUR";
    const DEFAULT_PRICE_DELIVERY = 300;
    const DEFAULT_DAYS_DELIVERY = 1;
    const PRICE_FREE_DELIVERY = 25000;
    const DEFAULT_PRICE_DELIVERY_RB = 6;
    const DEFAULT_DAYS_DELIVERY_RB = 1;

    private $shopUrls = array(
        "DEFAULT" => "https://www.textiletorg.ru",
        "BY" => "http://www.textiletorg.by",
        "BYN" => "http://www.textiletorg.by"
    );

    private $regionCode = "MSK";
    private $handlerCode = "YAMARKET";
    private $arHandler = array();
    private $regionPriceId = 1;
    private $arSections = array();
    private $arSectionsId = array();
    private $arSectionsYmId = array();
    private $arBrands = array();
    private $arFeatures = array();
    private $arEquipments = array();
    private $arElements = array();

    private $arRegionPrices = array(
        "MSK"   => 1,//москва
		"SPB"   => 2,//санкт петербург
		"EKB"   => 4,//Екатеринбург && Свердловская область
		"N_NOV" => 5,//Нижний Новгород && Нижегородская область
		"RND"   => 6,//Ростовская область && Ростов-на-Дону
		"NSB" 	=> 12 //Новосибирская область && Новосибирск
    );

    private $arCurrency = array(
        "MSK" => "RUB",
        "SPB" => "RUB",
        "EKB" => "RUB",
        "N_NOV" => "RUB",
        "RND" => "RUB",
		"NSB" => "RUB"
    );

    private $arPricesDelivery = array(
        "MSK" => 300,
        "SPB" => 300,
        "EKB" => 300,
        "N_NOV" => 300,
        "RND" => 300,
		"NSB" => 300,
    );

    function __construct($regionCode, $handlerCode, $arHandler = array())
    {
        $this->arHandler = $arHandler;

        if (!empty($regionCode)) {
            $this->regionCode = $regionCode;
        }

        if (!empty($regionCode)) {
            $this->handlerCode = $handlerCode;
        }

        $this->regionPriceId = $this->arRegionPrices[$this->regionCode];
        $this->arBrands = BitrixMarket::GetHlBlockElements(self::HLBLOCK_BRAND);
        $this->arFeatures = BitrixMarket::GetHlBlockElements(self::HLBLOCK_FEATURES);
        $this->arEquipments = BitrixMarket::GetHlBlockElements(self::HLBLOCK_EQUIPMENT);
    }

    private function GetHlBlockElements($hlBlockId)
    {
        $arResult = array();

        $hlBlock = HL\HighloadBlockTable::getById($hlBlockId)->fetch();
        $dbEntity = HL\HighloadBlockTable::compileEntity($hlBlock);

        $entityDataClass = $dbEntity->getDataClass();
        $entityTableName = $hlblock['TABLE_NAME'];

        $sTableID = 'tbl_' . $entityTableName;
        $dbResult = $entityDataClass::getList(
            array(
                "select" => array("ID", "UF_NAME", "UF_XML_ID"),
                "filter" => array(),
                "order" => array(
                    "UF_SORT" => "ASC"
                )
            )
        );

        $dbResult = new CDBResult($dbResult, $sTableID);

        while ($arRow = $dbResult->Fetch()) {
            $arResult[$arRow["UF_XML_ID"]] = $arRow;
        }

        return $arResult;
    }

    private function GetSubCategory($arrTargetCategory)
    {
        $arrResultCategory = array();

        foreach ($arrTargetCategory as $item => $value) {
            //выборка дерева подразделов для раздела
            $rsParentSection = CIBlockSection::GetByID($value);
            if ($arParentSection = $rsParentSection->GetNext()) {
                $arFilter = array(
                    'IBLOCK_ID' => self::IBLOCK_CATALOG,
                    '>LEFT_MARGIN' => $arParentSection['LEFT_MARGIN'],
                    '<RIGHT_MARGIN' => $arParentSection['RIGHT_MARGIN'],
                    '>DEPTH_LEVEL' => $arParentSection['DEPTH_LEVEL']
                ); // выберет потомков без учета активности
                $rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'), $arFilter);

                while ($arSect = $rsSect->GetNext()) {
                    // получаем подразделы
                    $arrResultCategory[] = $arSect["ID"];
                }
            }
        }

        return $arrResultCategory;
    }

    public function GetSectionsXml($commercialSpace = "YM")
    {
        $arResult = array();

        $arCommercialSpace = array(
            "YM" => "UF_IS_YM",
            "FISRU" => "UF_IS_FISRU"
        );

        $dbResult = CIBlockSection::GetList(
            array(
                "SECTION" => "ASC"
            ),
            array(
                "IBLOCK_ID" => self::IBLOCK_CATALOG,
                $arCommercialSpace[$commercialSpace] => true,
                ">UF_YM_ID" => 0
            ),
            false,
            array("ID", "IBLOCK_ID", "NAME", "UF_IS_EXPORT_MARKET", "UF_YM_ID", "IBLOCK_SECTION_ID", "DEPTH_LEVEL")
        );

        $arSections = array();
        while ($arRow = $dbResult->GetNext()) {
            $this->arSections[$arRow["ID"]] = $arRow;
            $this->arSectionsId[] = $arRow["ID"];
            $this->arSectionsYmId[$arRow["ID"]] = $arRow["UF_YM_ID"];

            $arSections[] = $arRow;
        }

        foreach ($arSections as $arSection) {
            $arXmlCategory = array(
                "@id" => $arSection["UF_YM_ID"],
            );

            if ($arSection["IBLOCK_SECTION_ID"] && $this->arSectionsYmId[$arSection["IBLOCK_SECTION_ID"]]) {
                $arXmlCategory["@parentId"] = $this->arSectionsYmId[$arSection["IBLOCK_SECTION_ID"]];
            }

            $arXmlCategory["%"] = self::ClearText($arSection["NAME"]);

            $arResult[] = $arXmlCategory;
        }

        $arSort = array_map(
            function ($arValue) {
                return $arValue["@parentId"];
            },
            $arResult
        );
        array_multisort($arSort, SORT_ASC, $arResult);

        return $arResult;
    }

    public function GetSectionsAll()
    {
        $dbResult = CIBlockSection::GetList(
            array(
                "SECTION" => "ASC"
            ),
            array(
                "IBLOCK_ID" => self::IBLOCK_CATALOG
            ),
            false,
            array("ID", "IBLOCK_ID", "NAME", "UF_IS_EXPORT_MARKET", "UF_YM_ID", "IBLOCK_SECTION_ID", "DEPTH_LEVEL")
        );

        while ($arRow = $dbResult->GetNext()) {
            $this->arSections[$arRow["ID"]] = $arRow;
            $this->arSectionsId[] = $arRow["ID"];
            $this->arSectionsYmId[$arRow["ID"]] = $arRow["UF_YM_ID"];
        }
    }

    public function GetElementsXml($isNotProps = false, $noExportMarket = false)
    {
        $arResult = array();

        if ($isNotProps) {
            $arProps = $this->GetProperties(array(
                "BRAND",
                "GUARANTEE_DEFAULT",
                "FEATURES",
                "EQUIPMENT",
                "VENDOR_CODE",
                "PAYMENT_INSTALLMENTS"
            ));

            $this->arElements = $this->GetElements($this->arElements, $arProps, array(), $noExportMarket);
        } else {
            $arContainerProps = $this->GetContainerProperties();

            foreach ($arContainerProps as $key => $arProps) {
                $this->arElements = $this->GetElements($this->arElements, $arProps);
            }
        }

        $arPicturesId = array();
        foreach ($this->arElements as $arItem) {
            $arPicturesId[] = $arItem["DETAIL_PICTURE"];
        }

        // echo "<pre>";
        // var_dump($this->arElements);
        // die;

        $arPictures = $this->GetPictures($arPicturesId);

        foreach ($this->arElements as $arItem) {
            if (!$isNotProps) {
                $arFormatProps = array();

                foreach ($arItem["DISPLAY_PROPERTIES"] as $arProp) {
                    if (is_array($arProp["VALUE"])) {
                        $arProp["VALUE"] = implode(", ", $arProp["VALUE"]);
                    }

                    $arFormatProps[] = array(
                        "@name" => $arProp["NAME"],
                        "%" => $arProp["VALUE"]
                    );
                }

                if (!empty($arItem["PROPERTIES"]["EQUIPMENT"]["VALUE"])) {
                    $arFormatProps[] = array(
                        "@name" => "Комплектация(не поддерживается)",
                        "%" => $this->GetEquipmentsXML($arItem["PROPERTIES"]["EQUIPMENT"]["VALUE"])
                    );
                }

                if (!empty($arItem["PROPERTIES"]["FEATURES"]["VALUE"])) {
                    $arFormatProps[] = array(
                        "@name" => "Отличительные особенности",
                        "%" => $this->GetFeaturesXML($arItem["PROPERTIES"]["FEATURES"]["VALUE"])
                    );
                }
            }

            $arFormatRow = array(
                "@id" => $arItem["ID"]
            );

            $arFormatRow["@available"] = (intval($arItem["CATALOG_QUANTITY"])) ? "true" : "false";

            $priceDelivery = (empty($this->arPricesDelivery[$this->regionCode])) ? self::DEFAULT_PRICE_DELIVERY : $this->arPricesDelivery[$this->regionCode];
            $deliveryCost = (intval($arItem["CATALOG_PRICE_" . $this->regionPriceId]) >= self::PRICE_FREE_DELIVERY) ? "0" : $priceDelivery;

            $countZero = ($arItem["CATALOG_CURRENCY_" . $this->regionPriceId] == "BYN") ? 2 : 0;

            $arFormatRow += array(
                "url" => $this->GetOfferUrl($arItem["DETAIL_PAGE_URL"]),
                "price" => number_format($arItem["CATALOG_PRICE_" . $this->regionPriceId], $countZero, ".", ""),
                "currencyId" => $arItem["CATALOG_CURRENCY_" . $this->regionPriceId],
                "categoryId" => $this->arSectionsYmId[$arItem["IBLOCK_SECTION_ID"]],
                "picture" => $arPictures[$arItem["DETAIL_PICTURE"]],
                "delivery" => "true",
                "delivery-options" => array(
                    "option" => array(
                        "@cost" => $deliveryCost,
                        "@days" => $deliveryDays
                    )
                ),
                "name" => $arItem["NAME"],
            );

            $brandName = (!empty($arItem["PROPERTIES"]["BRAND"]["VALUE"])) ?
                $this->arBrands[$arItem["PROPERTIES"]["BRAND"]["VALUE"]]["UF_NAME"] :
                false;

            if (!empty($brandName)) {
                $arFormatRow["vendor"] = $brandName;
            }

            $arFormatRow += array(
                "description" => self::FormatTextDesc($arItem["PREVIEW_TEXT"]),
                "sales_notes" => self::GetSaleNotes(!empty($arItem["PROPERTIES"]["PAYMENT_INSTALLMENTS"])),
            );
            $arFormatRow["manufacturer_warranty"] = $this->GetOfferGuarantee($arItem["PROPERTIES"]["GUARANTEE_DEFAULT"]["VALUE"]);

            if (!$isNotProps) {
                $arFormatRow["param"] = $arFormatProps;
            }

            $arResult[] = $arFormatRow;
        }

        return $arResult;
    }

    public function GetElementsXmlGoods($isNotProps, $arVendorCodes)
    {
        $arResult = array();

        if ($isNotProps) {
            $arProps = $this->GetProperties(array(
                "BRAND",
                "GUARANTEE_DEFAULT",
                "FEATURES",
                "EQUIPMENT",
                "VENDOR_CODE",
                "PAYMENT_INSTALLMENTS",
                "EAN13"
            ));

            $arFilterExt = array("PROPERTY_VENDOR_CODE" => $arVendorCodes);

            $this->arElements = $this->GetElements($this->arElements, $arProps, $arFilterExt);
        } else {
            $arContainerProps = $this->GetContainerProperties();

            foreach ($arContainerProps as $key => $arProps) {
                $this->arElements = $this->GetElements($this->arElements, $arProps);
            }
        }

        $arPicturesId = array();
        foreach ($this->arElements as $arItem) {
            $arPicturesId[] = $arItem["DETAIL_PICTURE"];
        }

        // echo "<pre>";
        // var_dump($this->arElements);
        // die;

        $arPictures = $this->GetPictures($arPicturesId);

        foreach ($this->arElements as $arItem) {
            if (!$isNotProps) {
                $arFormatProps = array();

                foreach ($arItem["DISPLAY_PROPERTIES"] as $arProp) {
                    if (is_array($arProp["VALUE"])) {
                        $arProp["VALUE"] = implode(", ", $arProp["VALUE"]);
                    }

                    $arFormatProps[] = array(
                        "@name" => $arProp["NAME"],
                        "%" => $arProp["VALUE"]
                    );
                }

                if (!empty($arItem["PROPERTIES"]["EQUIPMENT"]["VALUE"])) {
                    $arFormatProps[] = array(
                        "@name" => "Комплектация(не поддерживается)",
                        "%" => $this->GetEquipmentsXML($arItem["PROPERTIES"]["EQUIPMENT"]["VALUE"])
                    );
                }

                if (!empty($arItem["PROPERTIES"]["FEATURES"]["VALUE"])) {
                    $arFormatProps[] = array(
                        "@name" => "Отличительные особенности",
                        "%" => $this->GetFeaturesXML($arItem["PROPERTIES"]["FEATURES"]["VALUE"])
                    );
                }
            }

            $arFormatRow = array(
                "@id" => $arItem["ID"]
            );

            $arFormatRow["@available"] = (intval($arItem["CATALOG_QUANTITY"])) ? "true" : "false";

            $priceDelivery = (empty($this->arPricesDelivery[$this->regionCode])) ? self::DEFAULT_PRICE_DELIVERY : $this->arPricesDelivery[$this->regionCode];
            $deliveryCost = (intval($arItem["CATALOG_PRICE_" . $this->regionPriceId]) >= self::PRICE_FREE_DELIVERY) ? "0" : $priceDelivery;

            $countZero = ($arItem["CATALOG_CURRENCY_" . $this->regionPriceId] == "BYN") ? 2 : 0;

            $arFormatRow += array(
                "url" => $this->GetOfferUrl($arItem["DETAIL_PAGE_URL"]),
                "price" => number_format($arItem["CATALOG_PRICE_" . $this->regionPriceId], $countZero, ".", ""),
                "currencyId" => $arItem["CATALOG_CURRENCY_" . $this->regionPriceId],
                "categoryId" => $this->arSectionsYmId[$arItem["IBLOCK_SECTION_ID"]],
                "picture" => $arPictures[$arItem["DETAIL_PICTURE"]],
                "delivery" => "true",
                "delivery-options" => array(
                    "option" => array(
                        "@cost" => $deliveryCost,
                        "@days" => $deliveryDays
                    )
                ),
                "name" => $arItem["NAME"],
                //"barcode" => $arItem["PROPERTIES"]["EAN13"]["VALUE"]
            );

            $brandName = (!empty($arItem["PROPERTIES"]["BRAND"]["VALUE"])) ?
                $this->arBrands[$arItem["PROPERTIES"]["BRAND"]["VALUE"]]["UF_NAME"] :
                false;

            if (!empty($brandName)) {
                $arFormatRow["vendor"] = $brandName;
            }

            if (!empty($arItem["PROPERTIES"]["EAN13"]["VALUE"])) {
                $arFormatRow["barcode"] = $arItem["PROPERTIES"]["EAN13"]["VALUE"];
            }

            $arFormatRow += array(
                "description" => self::FormatTextDesc($arItem["PREVIEW_TEXT"]),
                "sales_notes" => self::GetSaleNotes(!empty($arItem["PROPERTIES"]["PAYMENT_INSTALLMENTS"])),
            );
            $arFormatRow["manufacturer_warranty"] = $this->GetOfferGuarantee($arItem["PROPERTIES"]["GUARANTEE_DEFAULT"]["VALUE"]);

            if (!$isNotProps) {
                $arFormatRow["param"] = $arFormatProps;
            }

            $arResult[] = $arFormatRow;
        }

        return $arResult;
    }

    public function GetElementsXmlYasearchBy()
    {
        $arResult = array();

        $arContainerProps = $this->GetContainerProperties();

        foreach ($arContainerProps as $key => $arProps) {
            $this->arElements = $this->GetElements($this->arElements, $arProps, array(
                "CATALOG_CURRENCY_7" => "BYR",
                "PROPERTY_VIEW_SITE_RB_VALUE" => "Да"
            ));
        }

        $arPicturesId = array();
        foreach ($this->arElements as $arItem) {
            $arPicturesId[] = $arItem["DETAIL_PICTURE"];
        }

        // echo "<pre>";
        // var_dump($this->arElements);
        // die;

        $arPictures = $this->GetPictures($arPicturesId);

        foreach ($this->arElements as $arItem) {
            if (!$isNotProps) {
                $arFormatProps = array();

                foreach ($arItem["DISPLAY_PROPERTIES"] as $arProp) {
                    if (is_array($arProp["VALUE"])) {
                        $arProp["VALUE"] = implode(", ", $arProp["VALUE"]);
                    }

                    $arFormatProps[] = array(
                        "@name" => $arProp["NAME"],
                        "%" => $arProp["VALUE"]
                    );
                }

                if (!empty($arItem["PROPERTIES"]["EQUIPMENT"]["VALUE"])) {
                    $arFormatProps[] = array(
                        "@name" => "Комплектация(не поддерживается)",
                        "%" => $this->GetEquipmentsXML($arItem["PROPERTIES"]["EQUIPMENT"]["VALUE"])
                    );
                }

                if (!empty($arItem["PROPERTIES"]["FEATURES"]["VALUE"])) {
                    $arFormatProps[] = array(
                        "@name" => "Отличительные особенности",
                        "%" => $this->GetFeaturesXML($arItem["PROPERTIES"]["FEATURES"]["VALUE"])
                    );
                }
            }

            $arFormatRow = array(
                "@id" => $arItem["ID"]
            );

            $arFormatRow["@available"] = (intval($arItem["CATALOG_QUANTITY"])) ? "true" : "false";

            $priceDelivery = (empty($this->arPricesDelivery[$this->regionCode])) ? self::DEFAULT_PRICE_DELIVERY : $this->arPricesDelivery[$this->regionCode];
            $deliveryCost = (intval($arItem["CATALOG_PRICE_" . $this->regionPriceId]) >= self::PRICE_FREE_DELIVERY) ? "0" : $priceDelivery;

            $arFormatRow += array(
                "url" => $this->GetOfferUrl($arItem["DETAIL_PAGE_URL"]),
                "price" => number_format($arItem["CATALOG_PRICE_" . $this->regionPriceId], 2, ",", ""),
                "currencyId" => $this->GetCurrency(),
                "categoryId" => $this->arSectionsYmId[$arItem["IBLOCK_SECTION_ID"]],
                "picture" => $arPictures[$arItem["DETAIL_PICTURE"]],
                "delivery" => "true",
                "delivery-options" => array(
                    "option" => array(
                        "@cost" => $deliveryCost,
                        "@days" => $deliveryDays
                    )
                ),
                "name" => $arItem["NAME"],
            );

            $brandName = (!empty($arItem["PROPERTIES"]["BRAND"]["VALUE"])) ?
                $this->arBrands[$arItem["PROPERTIES"]["BRAND"]["VALUE"]]["UF_NAME"] :
                false;

            if (!empty($brandName)) {
                $arFormatRow["vendor"] = $brandName;
            }

            $arFormatRow += array(
                "description" => self::FormatTextDesc($arItem["PREVIEW_TEXT"]),
                "sales_notes" => self::GetSaleNotes(!empty($arItem["PROPERTIES"]["PAYMENT_INSTALLMENTS"])),
            );
            $arFormatRow["manufacturer_warranty"] = $this->GetOfferGuarantee($arItem["PROPERTIES"]["GUARANTEE_DEFAULT"]["VALUE"]);

            if (!$isNotProps) {
                $arFormatRow["param"] = $arFormatProps;
            }

            $arResult[] = $arFormatRow;
        }

        return $arResult;
    }

    public function GetElementsForTorgMail()
    {
        $arResult = array();


        $arProps = $this->GetProperties(array(
            "BRAND",
            "CATEGORY_TYPE",
            "MODEL_NAME",
            "MODEL",
        ));

        $this->arElements = $this->GetElements($this->arElements, $arProps, array("!PROPERTY_CATEGORY_TYPE" => false));


        $arPicturesId = array();
        foreach ($this->arElements as $arItem) {
            $arPicturesId[] = $arItem["DETAIL_PICTURE"];
        }

        $arPictures = $this->GetPictures($arPicturesId);

        foreach ($this->arElements as $arItem) {
            $arFormatRow = array(
                "@id" => $arItem["ID"]
            );

            $arFormatRow["@available"] = (intval($arItem["CATALOG_QUANTITY"])) ? "true" : "false";

            $priceDelivery = (empty($this->arPricesDelivery[$this->regionCode])) ? self::DEFAULT_PRICE_DELIVERY : $this->arPricesDelivery[$this->regionCode];
            $deliveryCost = (intval($arItem["CATALOG_PRICE_" . $this->regionPriceId]) >= self::PRICE_FREE_DELIVERY) ? "0" : $priceDelivery;

            // Обязательно использование полей:
            // Производитель
            $brandName = (!empty($arItem["PROPERTIES"]["BRAND"]["VALUE"])) ?
                $this->arBrands[$arItem["PROPERTIES"]["BRAND"]["VALUE"]]["UF_NAME"] :
                false;
            if (empty($brandName)) {
                continue;
            }

            // модель
            if (empty($arItem["PROPERTIES"]["MODEL_NAME"]["VALUE"]) && empty($arItem["PROPERTIES"]["MODEL"]["VALUE"])) {
                continue;
            } else {
                $model = $arItem["PROPERTIES"]["MODEL_NAME"]["VALUE"] . " " . $arItem["PROPERTIES"]["MODEL"]["VALUE"];
            }

            $countZero = ($arItem["CATALOG_CURRENCY_" . $this->regionPriceId] == "BYN") ? 2 : 0;

            $arFormatRow += array(
                "url" => $this->GetOfferUrl($arItem["DETAIL_PAGE_URL"]),
                "price" => number_format($arItem["CATALOG_PRICE_" . $this->regionPriceId], $countZero, ".", ""),
                "currencyId" => $arItem["CATALOG_CURRENCY_" . $this->regionPriceId],
                "categoryId" => $this->arSectionsYmId[$arItem["IBLOCK_SECTION_ID"]],
                "picture" => $arPictures[$arItem["DETAIL_PICTURE"]],
                "typePrefix" => $arItem["PROPERTIES"]["CATEGORY_TYPE"]["VALUE"],
                "vendor" => $brandName,
                "model" => trim($model),
                "description" => self::FormatTextDesc($arItem["PREVIEW_TEXT"]),
                "delivery" => "true",
                "pickup" => "true",
                "local_delivery_cost" => $deliveryCost,
                "name" => $arItem["NAME"],
            );

            // Товарам, которые не размечены, как МРЦ - добавляем тег "oldprice"
            if (intval($arItem["CATALOG_PRICE_9"]) == 0) {
                $arFormatRow["oldprice"] = round($arItem["CATALOG_PRICE_" . $this->regionPriceId] * 1.07, -1);
            }

            $arResult[] = $arFormatRow;
        }

        return $arResult;
    }

    public function GetElementsXmlAllBiz($arrTargetCategory)
    {
        $arResult = array();

        $arrResultCategory = $this->GetSubCategory($arrTargetCategory);

        $arExtFilter = array(
            "SECTION_ID" => $arrResultCategory
        );

        if ($isNotProps) {
            $arProps = $this->GetProperties(array(
                "BRAND",
                "GUARANTEE_DEFAULT",
                "FEATURES",
                "EQUIPMENT",
                "VENDOR_CODE",
                "PAYMENT_INSTALLMENTS"
            ));

            $this->arElements = $this->GetElements($this->arElements, $arProps, $arExtFilter, false, false);
        } else {
            $arContainerProps = $this->GetContainerProperties();

            foreach ($arContainerProps as $key => $arProps) {
                $this->arElements = $this->GetElements($this->arElements, $arProps, $arExtFilter, false, false);
            }
        }

        $arPicturesId = array();
        foreach ($this->arElements as $arItem) {
            $arPicturesId[] = $arItem["DETAIL_PICTURE"];
        }

        $arPictures = $this->GetPictures($arPicturesId);

        foreach ($this->arElements as $arItem) {
            if (!$isNotProps) {
                $arFormatProps = array();

                foreach ($arItem["DISPLAY_PROPERTIES"] as $arProp) {
                    if (is_array($arProp["VALUE"])) {
                        $arProp["VALUE"] = implode(", ", $arProp["VALUE"]);
                    }

                    $arFormatProps[] = array(
                        "@name" => $arProp["NAME"],
                        "%" => $arProp["VALUE"]
                    );
                }

                if (!empty($arItem["PROPERTIES"]["EQUIPMENT"]["VALUE"])) {
                    $arFormatProps[] = array(
                        "@name" => "Комплектация(не поддерживается)",
                        "%" => $this->GetEquipmentsXML($arItem["PROPERTIES"]["EQUIPMENT"]["VALUE"])
                    );
                }

                if (!empty($arItem["PROPERTIES"]["FEATURES"]["VALUE"])) {
                    $arFormatProps[] = array(
                        "@name" => "Отличительные особенности",
                        "%" => $this->GetFeaturesXML($arItem["PROPERTIES"]["FEATURES"]["VALUE"])
                    );
                }
            }

            $arFormatRow = array(
                "@id" => $arItem["ID"]
            );

            $arFormatRow["@available"] = (intval($arItem["CATALOG_QUANTITY"])) ? "true" : "false";

            $priceDelivery = (empty($this->arPricesDelivery[$this->regionCode])) ? self::DEFAULT_PRICE_DELIVERY : $this->arPricesDelivery[$this->regionCode];
            $deliveryCost = (intval($arItem["CATALOG_PRICE_" . $this->regionPriceId]) >= self::PRICE_FREE_DELIVERY) ? "0" : $priceDelivery;

            $countZero = ($arItem["CATALOG_CURRENCY_" . $this->regionPriceId] == "BYN") ? 2 : 0;

            $arFormatRow += array(
                "url" => $this->GetOfferUrl($arItem["DETAIL_PAGE_URL"]),
                "price" => number_format($arItem["CATALOG_PRICE_" . $this->regionPriceId], $countZero, ".", ""),
                "currencyId" => $arItem["CATALOG_CURRENCY_" . $this->regionPriceId],
                "categoryId" => $this->arSectionsYmId[$arItem["IBLOCK_SECTION_ID"]],
                "picture" => $arPictures[$arItem["DETAIL_PICTURE"]],
                "delivery" => "true",
                "delivery-options" => array(
                    "option" => array(
                        "@cost" => $deliveryCost,
                        "@days" => $deliveryDays
                    )
                ),
                "name" => $arItem["NAME"],
            );

            $brandName = (!empty($arItem["PROPERTIES"]["BRAND"]["VALUE"])) ?
                $this->arBrands[$arItem["PROPERTIES"]["BRAND"]["VALUE"]]["UF_NAME"] :
                false;

            if (!empty($brandName)) {
                $arFormatRow["vendor"] = $brandName;
            }

            $arFormatRow += array(
                "description" => self::FormatTextDesc($arItem["PREVIEW_TEXT"]),
                "sales_notes" => self::GetSaleNotes(!empty($arItem["PROPERTIES"]["PAYMENT_INSTALLMENTS"])),
            );
            $arFormatRow["manufacturer_warranty"] = $this->GetOfferGuarantee($arItem["PROPERTIES"]["GUARANTEE_DEFAULT"]["VALUE"]);

            if (!$isNotProps) {
                $arFormatRow["param"] = $arFormatProps;
            }

            $arResult[] = $arFormatRow;
        }

        return $arResult;
    }

    public function GetElementsXmlIrr($arrTargetCategory)
    {

//        $arrRubricList = array(
//            "Швейная машина" => "/electronics-technics/ironing-sewing-equipment/sewing-machines",
//            "Манекен" => "/electronics-technics/ironing-sewing-equipment/sewing-machines",
//            "Вязальная машина" => "/electronics-technics/ironing-sewing-equipment/knitting-machine",
//            "Ткацкий станок" => "/electronics-technics/ironing-sewing-equipment/knitting-machine",
//            "Кеттельная машина" => "/electronics-technics/ironing-sewing-equipment/knitting-machine",
//            "Швейно-вышивальная машина" => "/electronics-technics/ironing-sewing-equipment/knitting-machine",
//            "Вышивальная машина" => "/electronics-technics/ironing-sewing-equipment/knitting-machine",
//            "Оверлок" => "/electronics-technics/ironing-sewing-equipment/overlock",
//            "Парогенератор" => "/electronics-technics/ironing-sewing-equipment/irons",
//            "Утюг" => "/electronics-technics/ironing-sewing-equipment/irons",
//            "Отпариватель" => "/electronics-technics/ironing-sewing-equipment/irons",
//            "Пресс для брюк" => "/electronics-technics/ironing-sewing-equipment/irons",
//            "Гладильный пресс" => "/electronics-technics/ironing-sewing-equipment/irons",
//            "Гладильный стол" => "/electronics-technics/ironing-sewing-equipment/irons",
//            "Гладильная доска" => "/electronics-technics/ironing-sewing-equipment/irons",
//            "Гладильная система" => "/electronics-technics/ironing-sewing-equipment/irons",
//            "Сушилка" => "/electronics-technics/ironing-sewing-equipment/irons",
//            "Пылесос" => "/electronics-technics/vacuum",
//            "Пароочиститель" => "/electronics-technics/vacuum",
//            "Паровая швабра" => "/electronics-technics/vacuum"
//        );

        $arrRubricList = array(
            "Швейная машина" => "/electronics-technics/ironing-sewing-equipment/sewing-machines",
            "Манекен" => "/equipment/cosmetology",
            "Вязальная машина" => "/electronics-technics/ironing-sewing-equipment/knitting-machine",
            "Ткацкий станок" => "/equipment/cosmetology",
            "Кеттельная машина" => "/electronics-technics/ironing-sewing-equipment/knitting-machine",
            "Швейно-вышивальная машина" => "/equipment/cosmetology",
            "Вышивальная машина" => "/equipment/cosmetology",
            "Оверлок" => "/electronics-technics/ironing-sewing-equipment/overlock",
            "Пресс для брюк" => "/electronics-technics/ironing-sewing-equipment/irons",
            "Парогенератор" => "/equipment/cosmetology",
            "Отпариватель" => "/equipment/cosmetology",
            "Гладильный пресс" => "/equipment/cosmetology",
            "Гладильный стол" => "/furniture-interior/home-stuff/other",
            "Гладильная доска" => "/furniture-interior/home-stuff/other",
            "Гладильная система" => "/electronics-technics/ironing-sewing-equipment/irons",
            "Утюг" => "/electronics-technics/ironing-sewing-equipment/irons",
            "Сушилка" => "/furniture-interior/interior/other",
            "Пылесос" => "/electronics-technics/vacuum",
            "Пароочиститель" => "/equipment/cosmetology/cleaning_equipment",
            "Паровая швабра" => "/equipment/cosmetology/cleaning_equipment"
        );
        $this->arSectionsYmId; // $arrRubricList

        $arrResultCategory = $this->GetSubCategory($arrTargetCategory);

        $arResult = array();

        $arProps = $this->GetProperties(array(
            "BRAND",
            "MODEL",
            "MODEL_NAME",
            "CATEGORY_TYPE",
        ));

        $arExtFilter = array(
            "SECTION_ID" => $arrResultCategory
        );

        $this->arElements = $this->GetElements($this->arElements, $arProps, $arExtFilter, false, false);

        $arPicturesId = array();
        foreach ($this->arElements as $arItem) {
            $arPicturesId[] = $arItem["DETAIL_PICTURE"];
        }

        $arPictures = $this->GetPictures($arPicturesId);

        foreach ($this->arElements as $arItem) {
            if (!$isNotProps) {
                $arFormatProps = array();

                foreach ($arItem["DISPLAY_PROPERTIES"] as $arProp) {
                    if (is_array($arProp["VALUE"])) {
                        $arProp["VALUE"] = implode(", ", $arProp["VALUE"]);
                    }

                    $arFormatProps[] = array(
                        "@name" => $arProp["NAME"],
                        "%" => $arProp["VALUE"]
                    );
                }

                if (!empty($arItem["PROPERTIES"]["EQUIPMENT"]["VALUE"])) {
                    $arFormatProps[] = array(
                        "@name" => "Комплектация(не поддерживается)",
                        "%" => $this->GetEquipmentsXML($arItem["PROPERTIES"]["EQUIPMENT"]["VALUE"])
                    );
                }

                if (!empty($arItem["PROPERTIES"]["FEATURES"]["VALUE"])) {
                    $arFormatProps[] = array(
                        "@name" => "Отличительные особенности",
                        "%" => $this->GetFeaturesXML($arItem["PROPERTIES"]["FEATURES"]["VALUE"])
                    );
                }
            }

            $arFormatRow = array(
                "@source-id" => $arItem["ID"],
                "@validtill" => "2020-10-13T10:42:19",
                "@power-ad" => "1",
                "@category" => $arrRubricList[$arItem["PROPERTIES"]["CATEGORY_TYPE"]["VALUE"]]
            );

            $countZero = ($arItem["CATALOG_CURRENCY_" . $this->regionPriceId] == "BYN") ? 2 : 0;

            $arFormatRow += array(
                "price" => array(
                    "@value" => number_format($arItem["CATALOG_PRICE_" . $this->regionPriceId], $countZero, ".", ""),
                    "@currency" => $arItem["CATALOG_CURRENCY_" . $this->regionPriceId]
                ),
                "title" => $arItem["NAME"],
            );

            $arFormatRow += array(
                "description" => self::FormatTextDesc($arItem["PREVIEW_TEXT"]),
            );

            $arFormatRow += array(
                "fotos" => array(
                    "foto-remote" => array(
                        "@url" => $arPictures[$arItem["DETAIL_PICTURE"]],
                        "@md5" => md5_file($_SERVER['DOCUMENT_ROOT'] . CFile::GetPath($arItem["DETAIL_PICTURE"]))
                    )
                )
            );

            $arFormatProps[] = array(
                "@name" => "region",
                "%region" => "Москва"
            );

            $arFormatProps[] = array(
                "@name" => "address_city",
                "%address_city" => "Москва"
            );

            $arFormatProps[] = array(
                "@name" => "shosse",
                "%shosse" => "Варшавское"
            );

            $arFormatProps[] = array(
                "@name" => "address_house",
                "%address_house" => "33"
            );

            $arFormatProps[] = array(
                "@name" => "phone",
                "%phone" => "+7 (495) 662-97-87"
            );

            $arFormatProps[] = array(
                "@name" => "used-or-new",
                "%used-or-new" => "новый"
            );

            $arFormatProps[] = array(
                "@name" => "make",
                "%make" => $arItem["PROPERTIES"]["BRAND"]["VALUE"]
            );

            $arFormatProps[] = array(
                "@name" => "model",
                "%model" => $arItem["PROPERTIES"]["MODEL_NAME"]["VALUE"] . " " . $arItem["PROPERTIES"]["MODEL"]["VALUE"]
            );

            if (!$isNotProps) {
                $arFormatRow["custom-fields"]["field"] = $arFormatProps;
            }

            $arResult[] = $arFormatRow;
        }

        return $arResult;
    }

    public function GetElementsXmlNadavi($arrTargetCategory)
    {
        $arrResultCategory = $this->GetSubCategory($arrTargetCategory);

        $arResult = array();

        $arProps = $this->GetProperties(array(
            "BRAND"
        ));

        $arExtFilter = array(
            "SECTION_ID" => $arrResultCategory
        );

        $this->arElements = $this->GetElements($this->arElements, $arProps, $arExtFilter, false, false);

        $arPicturesId = array();
        foreach ($this->arElements as $arItem) {
            $arPicturesId[] = $arItem["DETAIL_PICTURE"];
        }

        $arPictures = $this->GetPictures($arPicturesId);

        foreach ($this->arElements as $arItem) {
            if (!$isNotProps) {
                $arFormatProps = array();

                foreach ($arItem["DISPLAY_PROPERTIES"] as $arProp) {
                    if (is_array($arProp["VALUE"])) {
                        $arProp["VALUE"] = implode(", ", $arProp["VALUE"]);
                    }

                    $arFormatProps[] = array(
                        "@name" => $arProp["NAME"],
                        "%" => $arProp["VALUE"]
                    );
                }

                if (!empty($arItem["PROPERTIES"]["EQUIPMENT"]["VALUE"])) {
                    $arFormatProps[] = array(
                        "@name" => "Комплектация(не поддерживается)",
                        "%" => $this->GetEquipmentsXML($arItem["PROPERTIES"]["EQUIPMENT"]["VALUE"])
                    );
                }

                if (!empty($arItem["PROPERTIES"]["FEATURES"]["VALUE"])) {
                    $arFormatProps[] = array(
                        "@name" => "Отличительные особенности",
                        "%" => $this->GetFeaturesXML($arItem["PROPERTIES"]["FEATURES"]["VALUE"])
                    );
                }
            }

            $arFormatRow = array(
                "@id" => $arItem["ID"]
            );

            $arFormatRow["@available"] = (intval($arItem["CATALOG_QUANTITY"])) ? "true" : "false";

            $priceDelivery = (empty($this->arPricesDelivery[$this->regionCode])) ? self::DEFAULT_PRICE_DELIVERY : $this->arPricesDelivery[$this->regionCode];
            $deliveryCost = (intval($arItem["CATALOG_PRICE_" . $this->regionPriceId]) >= self::PRICE_FREE_DELIVERY) ? "0" : $priceDelivery;

            $countZero = ($arItem["CATALOG_CURRENCY_" . $this->regionPriceId] == "BYN") ? 2 : 0;

            $arFormatRow += array(
                "url" => $this->GetOfferUrl($arItem["DETAIL_PAGE_URL"]),
                "price" => number_format($arItem["CATALOG_PRICE_" . $this->regionPriceId], $countZero, ".", ""),
                "currencyId" => $arItem["CATALOG_CURRENCY_" . $this->regionPriceId],
                "categoryId" => $this->arSectionsYmId[$arItem["IBLOCK_SECTION_ID"]],
                "picture" => $arPictures[$arItem["DETAIL_PICTURE"]],
                "delivery" => "true",
                "delivery-options" => array(
                    "option" => array(
                        "@cost" => $deliveryCost,
                        "@days" => $deliveryDays
                    )
                ),
                "name" => $arItem["NAME"],
            );

            $brandName = (!empty($arItem["PROPERTIES"]["BRAND"]["VALUE"])) ?
                $this->arBrands[$arItem["PROPERTIES"]["BRAND"]["VALUE"]]["UF_NAME"] :
                false;

            if (!empty($brandName)) {
                $arFormatRow["vendor"] = $brandName;
            }

            $arFormatRow += array(
                "description" => self::FormatTextDesc($arItem["PREVIEW_TEXT"]),
                "sales_notes" => self::GetSaleNotes(!empty($arItem["PROPERTIES"]["PAYMENT_INSTALLMENTS"])),
            );
            $arFormatRow["manufacturer_warranty"] = $this->GetOfferGuarantee($arItem["PROPERTIES"]["GUARANTEE_DEFAULT"]["VALUE"]);

            if (!$isNotProps) {
                $arFormatRow["param"] = $arFormatProps;
            }

            $arResult[] = $arFormatRow;
        }

        return $arResult;
    }

    public function GetElementsXmlGoogleMS($arrTargetCategory)
    {
        $arrResultCategory = $this->GetSubCategory($arrTargetCategory);

        $arResult = array();

        $arProps = $this->GetProperties(array(
            "BRAND"
        ));

        $arExtFilter = array(
            "SECTION_ID" => $arrResultCategory
        );

        $this->arElements = $this->GetElements($this->arElements, $arProps, $arExtFilter, false, false);

        $arPicturesId = array();
        foreach ($this->arElements as $arItem) {
            $arPicturesId[] = $arItem["DETAIL_PICTURE"];
        }

        $arPictures = $this->GetPictures($arPicturesId);

        foreach ($this->arElements as $arItem) {
            $brandName = (!empty($arItem["PROPERTIES"]["BRAND"]["VALUE"])) ?
                $this->arBrands[$arItem["PROPERTIES"]["BRAND"]["VALUE"]]["UF_NAME"] :
                "Manufacturer";

            $priceDelivery = (empty($this->arPricesDelivery[$this->regionCode])) ? self::DEFAULT_PRICE_DELIVERY : $this->arPricesDelivery[$this->regionCode];
            $deliveryCost = (intval($arItem["CATALOG_PRICE_" . $this->regionPriceId]) >= self::PRICE_FREE_DELIVERY) ? "0" : $priceDelivery;

            $countZero = ($arItem["CATALOG_CURRENCY_" . $this->regionPriceId] == "BYN") ? 2 : 0;

            $arFormatRow = array(
                "title" => $arItem["NAME"],
                "link" => $this->GetOfferUrl($arItem["DETAIL_PAGE_URL"]),
                "description" => self::FormatTextDesc($arItem["PREVIEW_TEXT"]),
                "g:availability" => (intval($arItem["CATALOG_QUANTITY"])) ? "in stock" : "out of stock",
                "g:id" => $arItem["ID"],
                "g:condition" => "new",
                "g:price" => number_format($arItem["CATALOG_PRICE_" . $this->regionPriceId], $countZero, ".", ""),
                "g:image_link" => $arPictures[$arItem["DETAIL_PICTURE"]],
                "g:shipping" => array(
                    "g:country" => "RU",
                    "g:service" => "Standard",
                    "g:price" => $deliveryCost . " RUB"
                ),
                "g:brand" => $brandName,
                "g:product_type" => $this->arSections[$arItem["IBLOCK_SECTION_ID"]]["NAME"],
                "g:google_product_category" => "Дом и сад > Бытовые приборы"
            );

            $arResult[] = $arFormatRow;
        }

        return $arResult;
    }

    public function GetElementsXmlGml()
    {
        $arResult = array();

        $arProps = $this->GetProperties(array(
            "BRAND"
        ));

        $this->arElements = $this->GetElements($this->arElements, $arProps);

        $arPicturesId = array();
        foreach ($this->arElements as $arItem) {
            $arPicturesId[] = $arItem["DETAIL_PICTURE"];
        }

        // echo "<pre>";
        // var_dump($this->arElements);
        // die;

        $arPictures = $this->GetPictures($arPicturesId);

        foreach ($this->arElements as $arItem) {
            $brandName = (!empty($arItem["PROPERTIES"]["BRAND"]["VALUE"])) ?
                $this->arBrands[$arItem["PROPERTIES"]["BRAND"]["VALUE"]]["UF_NAME"] :
                "Manufacturer";

            $priceDelivery = (empty($this->arPricesDelivery[$this->regionCode])) ? self::DEFAULT_PRICE_DELIVERY : $this->arPricesDelivery[$this->regionCode];
            $deliveryCost = (intval($arItem["CATALOG_PRICE_" . $this->regionPriceId]) >= self::PRICE_FREE_DELIVERY) ? "0" : $priceDelivery;

            $countZero = ($arItem["CATALOG_CURRENCY_" . $this->regionPriceId] == "BYN") ? 2 : 0;

            $arFormatRow = array(
                "title" => $arItem["NAME"],
                "link" => $this->GetOfferUrl($arItem["DETAIL_PAGE_URL"]),
                "description" => self::FormatTextDesc($arItem["PREVIEW_TEXT"]),
                "g:id" => $arItem["ID"],
                "g:condition" => "new",
                "g:price" => number_format($arItem["CATALOG_PRICE_" . $this->regionPriceId], $countZero, ".", ""),
                "g:availability" => "in stock",
                "g:image_link" => $arPictures[$arItem["DETAIL_PICTURE"]],
                "g:shipping" => array(
                    "g:country" => "RU",
                    "g:service" => "Standard",
                    "g:price" => $deliveryCost . " RUB"
                ),
                "g:brand" => $brandName,
                "g:product_type" => $this->arSections[$arItem["IBLOCK_SECTION_ID"]]["NAME"],
                "g:google_product_category" => "Дом и сад > Бытовые приборы"
            );

            $arResult[] = $arFormatRow;
        }

        return $arResult;
    }

    public function GetElementsXmlCriteo()
    {
        $arResult = array();

        $arProps = $this->GetProperties(array(
            "BRAND"
        ));

        $this->arElements = $this->GetElements($this->arElements, $arProps);

        $arPicturesId = array();
        foreach ($this->arElements as $arItem) {
            $arPicturesId[] = $arItem["DETAIL_PICTURE"];
        }

        $arPictures = $this->GetPictures($arPicturesId);

        foreach ($this->arElements as $arItem) {
            $arFormatRow = array(
                "g:id" => $arItem["ID"],
                "g:title" => $arItem["NAME"],
                "g:description" => self::FormatTextDesc($arItem["PREVIEW_TEXT"]),
                "g:link" => $this->GetOfferUrl($arItem["DETAIL_PAGE_URL"]),
                "g:price" => number_format($arItem["CATALOG_PRICE_" . $this->regionPriceId], $countZero, ".", ""),
                "g:image_link" => $arPictures[$arItem["DETAIL_PICTURE"]],
                "g:google_product_category" => "Дом и сад > Бытовые приборы"
            );

            $arResult[] = $arFormatRow;
        }

        return $arResult;
    }

    public function GetElementsXmlMixmarket()
    {
        $arResult = array();

        $arProps = $this->GetProperties(array(
            "BRAND",
            "PAYMENT_INSTALLMENTS",
            "COUNTRY_PRODUCER",
            "MODEL"
        ));

        $this->arElements = $this->GetElements($this->arElements, $arProps);

        $arPicturesId = array();
        foreach ($this->arElements as $arItem) {
            $arPicturesId[] = $arItem["DETAIL_PICTURE"];
        }

        // echo "<pre>";
        // var_dump($this->arElements);
        // die;

        $arPictures = $this->GetPictures($arPicturesId);

        foreach ($this->arElements as $arItem) {
            $arFormatRow = array(
                "@id" => $arItem["ID"],
                "@available" => (intval($arItem["CATALOG_QUANTITY"])) ? "true" : "false",
                "@type" => "vendor.model"
            );

            $priceDelivery = (empty($this->arPricesDelivery[$this->regionCode])) ? self::DEFAULT_PRICE_DELIVERY : $this->arPricesDelivery[$this->regionCode];
            $deliveryCost = (intval($arItem["CATALOG_PRICE_" . $this->regionPriceId]) >= self::PRICE_FREE_DELIVERY) ? "0" : $priceDelivery;

            $countZero = ($arItem["CATALOG_CURRENCY_" . $this->regionPriceId] == "BYN") ? 2 : 0;

            $arFormatRow += array(
                "url" => $this->GetOfferUrl($arItem["DETAIL_PAGE_URL"]),
                "price" => number_format($arItem["CATALOG_PRICE_" . $this->regionPriceId], $countZero, ".", ""),
                "currencyId" => $arItem["CATALOG_CURRENCY_" . $this->regionPriceId],
                "categoryId" => $this->arSectionsYmId[$arItem["IBLOCK_SECTION_ID"]],
                "picture" => $arPictures[$arItem["DETAIL_PICTURE"]],
                "delivery" => "true",
                "local_delivery_cost" => $deliveryCost
            );

            if ($arItem["PROPERTIES"]["MODEL"]["VALUE"]) {
                $arFormatRow["model"] = $arItem["PROPERTIES"]["MODEL"]["VALUE"];
            } else {
                $arFormatRow["name"] = $arItem["NAME"];
            }


            $brandName = (!empty($arItem["PROPERTIES"]["BRAND"]["VALUE"])) ?
                $this->arBrands[$arItem["PROPERTIES"]["BRAND"]["VALUE"]]["UF_NAME"] :
                false;

            if (!empty($brandName)) {
                $arFormatRow["vendor"] = $brandName;
            }

            $arFormatRow += array(
                "description" => self::FormatTextDesc($arItem["PREVIEW_TEXT"]),
                "sales_notes" => self::GetSaleNotes(!empty($arItem["PROPERTIES"]["PAYMENT_INSTALLMENTS"])),
            );
            $arFormatRow["manufacturer_warranty"] = $this->GetOfferGuarantee($arItem["PROPERTIES"]["GUARANTEE_DEFAULT"]["VALUE"]);

            if ($arItem["PROPERTIES"]["COUNTRY_PRODUCER"]["VALUE"]) {
                $arFormatRow["country_of_origin"] = $arItem["PROPERTIES"]["COUNTRY_PRODUCER"]["VALUE"];
            }

            $arResult[] = $arFormatRow;
        }

        return $arResult;
    }

    public function GetElementsXmlWikimart()
    {
        $arResult = array();

        $arProps = $this->GetProperties(array(
            "PAYMENT_INSTALLMENTS", "EXPORT_IN_NEW_WIKIMART"
        ));

        $this->arElements = $this->GetElements($this->arElements, $arProps);

        $arPicturesId = array();
        foreach ($this->arElements as $arItem) {
            $arPicturesId[] = $arItem["DETAIL_PICTURE"];
        }

        // echo "<pre>";
        // var_dump($this->arElements);
        // die;

        $arPictures = $this->GetPictures($arPicturesId);

        foreach ($this->arElements as $arItem) {
            $arFormatRow = array(
                "@id" => $arItem["ID"],
                "@available" => (intval($arItem["CATALOG_QUANTITY"])) ? "true" : "false"
            );

            $priceDelivery = (empty($this->arPricesDelivery[$this->regionCode])) ? self::DEFAULT_PRICE_DELIVERY : $this->arPricesDelivery[$this->regionCode];
            $deliveryCost = (intval($arItem["CATALOG_PRICE_" . $this->regionPriceId]) >= self::PRICE_FREE_DELIVERY) ? "0" : $priceDelivery;

            $countZero = ($arItem["CATALOG_CURRENCY_" . $this->regionPriceId] == "BYN") ? 2 : 0;

            $arFormatRow += array(
                "url" => $this->GetOfferUrl($arItem["DETAIL_PAGE_URL"]),
                "price" => number_format($arItem["CATALOG_PRICE_" . $this->regionPriceId], $countZero, ".", ""),
                "currencyId" => $arItem["CATALOG_CURRENCY_" . $this->regionPriceId],
                "categoryId" => $this->arSectionsYmId[$arItem["IBLOCK_SECTION_ID"]],
                "picture" => $arPictures[$arItem["DETAIL_PICTURE"]],
                "local_delivery_cost" => $deliveryCost,
                "name" => $arItem["NAME"],
                "length" => $arItem["CATALOG_LENGTH"] / 1000,
                "width" => $arItem["CATALOG_WIDTH"] / 1000,
                "height" => $arItem["CATALOG_HEIGHT"] / 1000,
                "weight" => $arItem["CATALOG_WEIGHT"] / 1000,
                "stock" => $arItem["CATALOG_QUANTITY"]
            );

            $arFormatRow += array(
                "description" => self::FormatTextDesc($arItem["PREVIEW_TEXT"]),
                "sales_notes" => self::GetSaleNotes(!empty($arItem["PROPERTIES"]["PAYMENT_INSTALLMENTS"])),
            );
            $arFormatRow["manufacturer_warranty"] = $this->GetOfferGuarantee($arItem["PROPERTIES"]["GUARANTEE_DEFAULT"]["VALUE"]);

            $flagLabel = (empty($arItem["CATALOG_LENGTH"]) || empty($arItem["CATALOG_WIDTH"]) || empty($arItem["CATALOG_HEIGHT"]) || empty($arItem["CATALOG_WEIGHT"])) ? false : true;

            if (isset($arItem["PROPERTIES"]["EXPORT_IN_NEW_WIKIMART"]) && $flagLabel) {
                $arFormatRow["w:labels"] = array("w:label" => "VMI2MP");
            }

            $arResult[] = $arFormatRow;
        }

        return $arResult;
    }

    public function GetElementsXmlWikimartNew()
    {
        $arResult = array();

        $arProps = $this->GetProperties(array("BRAND", "VENDOR_CODE", "PRODUCER", "COUNTRY_PRODUCER"));
        $this->arElements = $this->GetElements($this->arElements, $arProps, array("!PROPERTY_EXPORT_IN_NEW_WIKIMART" => false), NULL, false);

        $arPicturesId = array();
        foreach ($this->arElements as $arItem) {
            $arPicturesId[] = $arItem["DETAIL_PICTURE"];
        }

        $arPictures = $this->GetPictures($arPicturesId);

        foreach ($this->arElements as $arItem) {
            $arFormatRow = array(
                "@id" => $arItem["ID"]
            );

            $countZero = ($arItem["CATALOG_CURRENCY_" . $this->regionPriceId] == "BYN") ? 2 : 0;

            $arFormatRow += array(
                "w:labels" => array(
                    "w:label" => array("CyberMonday2016", "BlackFriday2014", "CheapShit")
                ),
                "suppler" => "ТесктильТорг",
                "name" => $arItem["NAME"],
                "stock" => $arItem["CATALOG_QUANTITY"],
                "price" => number_format($arItem["CATALOG_PRICE_" . $this->regionPriceId], $countZero, ".", ""),
                "brand" => $this->arBrands[$arItem["PROPERTIES"]["BRAND"]["VALUE"]]["UF_NAME"],
                "barcode" => "wikimart",
                "url" => $arPictures[$arItem["DETAIL_PICTURE"]],
                "category" => $this->arSections[$arItem["IBLOCK_SECTION_ID"]]["NAME"],
                "vendorCode" => $arItem["PROPERTIES"]["VENDOR_CODE"]["VALUE"],
                "vendor" => $arItem["PROPERTIES"]["PRODUCER"]["VALUE"],
                "country" => $arItem["PROPERTIES"]["COUNTRY_PRODUCER"]["VALUE"],
                "length" => ($arItem["CATALOG_LENGTH"] / 1000),
                "width" => ($arItem["CATALOG_WIDTH"] / 1000),
                "height" => ($arItem["CATALOG_HEIGHT"] / 1000),
                "weight" => ($arItem["CATALOG_WEIGHT"] / 1000),
                "vat" => 0,
            );


            $arResult[] = $arFormatRow;
        }

        return $arResult;
    }

    public function GetElementsForAvito($noExportMarket = false)
    {
        $arResult = array();

        // Получаем записи
        $arProps = $this->GetProperties(array());
        $arExtFilter = array(
            "PROPERTY_NO_EXPORT_AVITO" => false
        );
        $this->arElements = $this->GetElements($this->arElements, $arProps, $arExtFilter, $noExportMarket);
		
        // Получаем изображения
        $arPicturesId = array();
        foreach ($this->arElements as $arItem) {
            $arPicturesId[] = $arItem["DETAIL_PICTURE"];
        }
        $arPictures = $this->GetPictures($arPicturesId);

        // Получаем все разделы
        $arFilter = Array("IBLOCK_ID" => $this->IBLOCK_CATALOG);
        $db_list = CIBlockSection::GetList(Array($by => $order), $arFilter, false, array("ID", "NAME", "IBLOCK_SECTION_ID", "DEPTH_LEVEL"));
        $arSections = array();
        while ($ar_result = $db_list->GetNext()) {
            $arSections[$ar_result['ID']] = $ar_result;
        }

        // Получаем счетчик
        $writeAd = 4;
        $avitoCounter = COption::GetOptionInt("main", "xml_avito_counter_" . $this->regionPriceId, 1);
        $maxAd = $avitoCounter * $writeAd;

        $i = 1;
        foreach ($this->arElements as $arItem) {
            if (empty($arItem["PREVIEW_TEXT"])) continue;

            if ($i > $maxAd) {
                COption::SetOptionInt("main", "xml_avito_counter_" . $this->regionPriceId, $avitoCounter + 1);
                break;
            }
            $i++;

            $arFormatRow = array();

            $countZero = ($arItem["CATALOG_CURRENCY_" . $this->regionPriceId] == "BYN") ? 2 : 0;

            $description = strip_tags($arItem["PREVIEW_TEXT"], '<p><br><strong><em><ul><ol><li>');
            $description = self::TruncateText($description, 2500);
            $description .= "<br><br>Внимание! На каждый товар приобретенный в нашем магазине Вы получаете все необходимые документы, включая чек и официальный гарантийный талон. Есть доставка по РФ. Весь товар новый! В магазине действуют дополнительные скидки и акции, посетите сайт магазина Текстильторг, что бы узнать больше.";
			
            $arFormatRow += array(
                "Id" => $arItem["ID"],
                "Category" => "Бытовая техника",
                "Title" => $arItem["NAME"],
                "Description|CDATA" => $description,
                "Price" => (int)$arItem["CATALOG_PRICE_" . $this->regionPriceId],
            );

			/*
			
				"MSK"   => 1,//москва
				"SPB"   => 2,//санкт петербург
				"EKB"   => 4,//Екатеринбург && Свердловская область
				"N_NOV" => 5,//Нижний Новгород && Нижегородская область
				"RND"   => 6,//Ростовская область && Ростов-на-Дону
				"BY"    => 11,
				"BYN"   => 11,
				"NSB" 	=> 12 //Новосибирская область && Новосибирск
			
			*/
			
            // Получим регион
            switch ($this->regionPriceId) {
                // Москва
                case "1":
                    $arFormatRow += array(
                        "Region" => "Москва",
                    );
                    break;
                // Санкт-Петербург
                case "2":
                    $arFormatRow += array(
                        "Region" => "Санкт-Петербург",
                    );
                    break;
                // Екатеринбург
                case "4":
                    $arFormatRow += array(
                        "Region" => "Свердловская область",
                        "City" => "Екатеринбург",
                    );
                    break;
                // Нижний новгород
                case "5":
                    $arFormatRow += array(
                        "Region" => "Нижегородская область",
                        "City" => "Нижний Новгород",
                    );
                    break;
                // Ростов на дону
                case "6":
                    $arFormatRow += array(
                        "Region" => "Ростовская область",
                        "City" => "Ростов-на-Дону",
                    );
                    break;
               //Новосибирская область && Новосибирск
                case "12":
                    $arFormatRow += array(
                        "Region" => "Новосибирская область",
                        "City" => "Новосибирск",
                    );
                    break;
            }

            // Получим раздел 2 уровня
            if ($arSections[$arItem["IBLOCK_SECTION_ID"]]["DEPTH_LEVEL"] >= 2) {

                $sectionId = $arItem["IBLOCK_SECTION_ID"];
                while (true) {
                    if ($arSections[$sectionId]["DEPTH_LEVEL"] == 2) {
                        $SectionName = $arSections[$sectionId]["NAME"];
                        break;
                    } else {
                        $sectionId = $arSections[$sectionId]["IBLOCK_SECTION_ID"];
                    }
                }
				
				if(in_array($SectionName, array("Манекены","Принтеры по текстилю"))){
					$arFormatRow["Category"] = "Оборудование для бизнеса";
				}
				
                // Соотношение разделов на сайте с разделами на авито
                if (in_array($SectionName, array("Швейные машины", "Вышивальные машины", "Швейно-вышивальные машины", "Вязальные машины", "Ткацкие станки", "Оверлоки"))) {
                    $arFormatRow += array("GoodsType" => "Швейные машины");
                } elseif (in_array($SectionName, array("Пылесосы"))) {
                    $arFormatRow += array("GoodsType" => "Пылесосы");
                } elseif (in_array($SectionName, array("Утюги", "Парогенераторы"))) {
                    $arFormatRow += array("GoodsType" => "Утюги");
                } else {
                    $arFormatRow += array("GoodsType" => "Другое");
                }
                
            } else {
                $arFormatRow += array("GoodsType" => "Другое");
            }

            // Получим изображение
           
            if (isset($arPictures[$arItem["DETAIL_PICTURE"]])) {
                $arItem["IMG_LIST"][] = $arPictures[$arItem["DETAIL_PICTURE"]];
                //$arFormatRow += array("Images" => array("Image" => array("@url" => $arPictures[$arItem["DETAIL_PICTURE"]])));
            }
             $i = 0;
			foreach($arItem["IMG_LIST"] as $key => $value){
				++$i;
				$arFormatRow["Images"]["Image"][] = array("@url" => $value);
				if($i == 10){
					break;
				}
			}
			

            $arResult[] = $arFormatRow;
        }

        return $arResult;
    }

    private function GetEquipmentsXML($arEquipments = array())
    {
        $arResult = array();

        foreach ($arEquipments as $equipmentCode) {
            $valueEquipment = $this->arEquipments[$equipmentCode]["UF_NAME"];
            if ($valueEquipment) {
                $arResult[] = $valueEquipment;
            }
        }

        return implode(", ", $arResult);
    }

    private function GetFeaturesXML($arFeatures = array())
    {
        $arResult = array();

        foreach ($arFeatures as $featureCode) {
            $valueFaeture = $this->arFeatures[$featureCode]["UF_NAME"];
            if ($valueFaeture) {
                $arResult[] = $valueFaeture;
            }
        }

        return implode(", ", $arResult);
    }

    private function GetOfferGuarantee($years = array(), $printYaers = false)
    {
        if (intval($years)) {
            if ($printYaers) {
                return intval($years);
            } else {
                return "P" . intval($years) . "Y";
            }
        }

        return "true";
    }

    private function GetOfferUrl($detailPageUrl)
    {
        $detailPageUrl = Helper::RemoveOneLavelUrl($detailPageUrl);
        $detailPageUrl = $this->GetFullShopUrl() . $detailPageUrl;


        if (!empty($this->arHandler["URL_POSTFIX"])) {
            $detailPageUrl .= $this->arHandler["URL_POSTFIX"];
        }

        if ($this->arHandler["IS_CTX"] !== false) {
            $detailPageUrl .= (preg_match("/\?/", $detailPageUrl)) ? "&" : "?";
            $detailPageUrl .= "ctx=" . strtolower($this->regionCode);
        }

        return $detailPageUrl;
    }

    private function GetElements($arResult = array(), $arProps = array(), $arFilterExt = array(), $noExportMarket = false, $isFilterSection = true)
    {
        if (empty($this->arSectionsId) && $isFilterSection)
            return array();

        $arFilter = array(
            "IBLOCK_ID" => self::IBLOCK_CATALOG,
            "ACTIVE" => "Y",
            ">CATALOG_PRICE_" . $this->regionPriceId => 0
        );

        if (!empty($arFilterExt)) {
            $arFilter = array_merge($arFilter, $arFilterExt);
        }

        if ($isFilterSection) {
            $arFilter["SECTION_ID"] = $this->arSectionsId;
            $arFilter["INCLUDE_SUBSECTIONS"] = "Y";
        }

        if ($noExportMarket !== NULL) {
            $arFilter["PROPERTY_NO_EXPORT_MARKET"] = $noExportMarket;
        }

        $arSelect = array("ID", "IBLOCK_ID", "IBLOCK_TYPE_ID", "NAME", "CODE", "XML_ID", "IBLOCK_SECTION_ID", "PREVIEW_TEXT",
           "DETAIL_PICTURE", "DETAIL_PAGE_URL", "CATALOG_GROUP_9", "CATALOG_GROUP_" . $this->regionPriceId);

        foreach ($arProps as $arProp) {
            $arSelect[] = "PROPERTY_" . $arProp["CODE"];
        }
		
        $dbResult = CIBlockElement::GetList(
            array("SORT" => "ASC"),
            $arFilter,
            false,
            false,
            $arSelect
        );
        
        while ($arRow = $dbResult->GetNext()) {
        	
            if (empty($this->arSectionsYmId[$arRow["IBLOCK_SECTION_ID"]]) && $isFilterSection) {
                continue;
            }

            $elementId = $arRow["ID"];
			
			
			$Query = CIBlockElement::GetProperty($arRow["IBLOCK_ID"], $arRow["ID"], array(), Array("CODE"=>"PHOTOS"));
            
            $ListImg = array();
            
            while($Answer = $Query->Fetch()){$ListImg[] = $Answer["VALUE"];}
            $arResult[$elementId]["IMG_LIST"] = $this->GetPictures($ListImg);
          		
            $arResult[$elementId] = (!empty($arResult[$elementId])) ? $arResult[$elementId] : array();

            $arResult[$elementId] = array_merge($arResult[$elementId], $arRow);

            foreach ($arProps as $arProp) {
                $propCode = $arProp["CODE"];
                $propArrayKey = (preg_match("/^CHAR_/", $propCode)) ? "DISPLAY_PROPERTIES" : "PROPERTIES";

                if (!empty($arRow["PROPERTY_" . $propCode . "_VALUE"])) {

                    if ($arProp["PROPERTY_TYPE"] != "L" && $arProp["MULTIPLE"] != "Y") {
                        $arProp["VALUE"] = $arRow["PROPERTY_" . $propCode . "_VALUE"];
                    } else {
                        $arProp["VALUE"] = (!empty($arResult[$elementId][$propArrayKey][$propCode]["VALUE"])) ?
                            $arResult[$elementId][$propArrayKey][$propCode]["VALUE"] :
                            array();

                        $arProp["VALUE"][] = $arRow["PROPERTY_" . $propCode . "_VALUE"];

                        $arProp["VALUE"] = array_unique($arProp["VALUE"]);
                    }

                    $arResult[$elementId][$propArrayKey][$propCode] = $arProp;
                }

                unset($arResult[$elementId]["PROPERTY_" . $propCode . "_VALUE"]);
                unset($arResult[$elementId]["PROPERTY_" . $propCode . "_ENUM_ID"]);
                unset($arResult[$elementId]["PROPERTY_" . $propCode . "_VALUE_ID"]);
                unset($arResult[$elementId]["~PROPERTY_" . $propCode . "_VALUE"]);
                unset($arResult[$elementId]["~PROPERTY_" . $propCode . "_ENUM_ID"]);
                unset($arResult[$elementId]["~PROPERTY_" . $propCode . "_VALUE_ID"]);
            }
        }

        foreach ($arResult as $id => $arItem) {
            if (!empty($arItem["XML_ID"]) && $arItem["XML_ID"] != $arItem["ID"]) {
                $arItem["ID"] = $arItem["XML_ID"];
                $arResult[$id] = $arItem;
            }
        }

        return $arResult;
    }

    private function GetContainerProperties()
    {
        $arResult = array();

        $arSystemProps = array(
            "BRAND",
            "GUARANTEE_DEFAULT",
            "FEATURES",
            "EQUIPMENT",
            "VENDOR_CODE",
            "PAYMENT_INSTALLMENTS"
        );

        $dbResult = CIBlockProperty::GetList(
            array(),
            array(
                "IBLOCK_ID" => self::IBLOCK_CATALOG,
                "ACTIVE" => "Y",
            )
        );

        $i = 0;
        $arPropsContainer = array();

        while ($arProp = $dbResult->Fetch()) {
            if (preg_match("/^CHAR_/", $arProp["CODE"]) ||
                in_array($arProp["CODE"], $arSystemProps)
            ) {
                if ($i < self::MAX_PROPS_CONTAINER) {
                    $i++;
                    $arPropsContainer[] = $arProp;
                } else {
                    $i = 0;
                    $arResult[] = $arPropsContainer;
                    $arPropsContainer = array();
                }
            }
        }

        return $arResult;
    }

    private function GetProperties($arParamPops = array())
    {
        $arResult = array();

        $dbResult = CIBlockProperty::GetList(
            array(),
            array(
                "IBLOCK_ID" => self::IBLOCK_CATALOG,
                "ACTIVE" => "Y",
            )
        );

        while ($arProp = $dbResult->Fetch()) {
            if (in_array($arProp["CODE"], $arParamPops)) {
                $arResult[] = $arProp;
            }
        }

        return $arResult;
    }

    private function GetPictures($arId = array())
    {
        $arResult = array();

        $dbResult = CFile::GetList(
            array(),
            Array("@ID" => implode(",", $arId))
        );

        while ($arRow = $dbResult->GetNext())
            $arResult[$arRow["ID"]] = $this->GetFullShopUrl() . "/upload/" . $arRow["SUBDIR"] . "/" . $arRow["FILE_NAME"];

        return $arResult;
    }

    static function FormatTextDesc($text)
    {
        $text = self::ClearText($text);
        $text = self::TruncateText($text, 250);

        return $text;
    }

    static function ClearText($text)
    {
        $text = str_replace("\n", "", $text);
        $text = str_replace("\n\r", "", $text);
        $text = preg_replace("/\t+/", "", $text);
        $text = strip_tags($text);
        return $text;
    }

    static function TruncateText($string, $length = 80, $etc = "...", $charset = "UTF-8", $breakWords = false, $middle = false)
    {
        if ($length == 0)
            return "";

        if (strlen($string) > $length) {
            $length -= min($length, strlen($etc));
            if (!$breakWords && !$middle)
                $string = preg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $length + 1, $charset));

            if (!$middle)
                return rtrim(mb_substr($string, 0, $length, $charset), ".,?\s") . $etc;
            else
                return rtrim(mb_substr($string, 0, $length / 2, $charset), ".,?\s") . $etc . rtrim(mb_substr($string, -$length / 2, $charset), ".,?\s");
        } else
            return rtrim($string, ".,?\s");
    }

    public function GetFullShopUrl($isHttp = true)
    {
        $shopUrl = ($this->shopUrls[$this->regionCode]) ?
            $this->shopUrls[$this->regionCode] :
            $this->shopUrls["DEFAULT"];

        if (!$isHttp) {
            $shopUrl = preg_replace("/^http\:\/\//", "", $shopUrl);
            $shopUrl = preg_replace("/^https\:\/\//", "", $shopUrl);
        }

        return $shopUrl;
    }

    public function GetCurrency()
    {
        return $this->arCurrency[$this->regionCode];
    }

    public function GetSaleNotes($isPayInst = false)
    {
        $arSaleNotes = array(
            "DEFAULT" => "Обучение и Демонстрация Бесплатно!",
            "PAY_INST" => "Покажем и научим - Бесплатно! Рассрочка 0%"
        );

        return ($isPayInst) ? $arSaleNotes["PAY_INST"] : $arSaleNotes["DEFAULT"];
    }

    private function GetNameLevelSection($level, $id)
    {
        $result = "";
        while (true) {
            if ($this->arSections[$id]["DEPTH_LEVEL"] == $level || !isset($this->arSections[$id]["IBLOCK_SECTION_ID"])) {
                $result = $this->arSections[$id]["NAME"];
                break;
            } else {
                $id = $this->arSections[$id]["IBLOCK_SECTION_ID"];
            }

        }
        return $result;
    }

    public function GetElementsXmlForOnlinerby()
    {
        $array_carts = array(
            "Швейные машины" => "Швейные машины",
            "Швейно-вышивальные" => "Швейные машины",
            "Вязальные машины" => "Швейные машины",
            "Вышивальные машины" => "Швейные машины",
            "Кеттельные машины" => "Швейные машины",
            "Оверлоки" => "Оверлоки и распошивальные машины",
            "Отпариватели" => "Пароочистители и отпариватели",
            "Пароочистители" => "Пароочистители и отпариватели",
            "Сушилки для белья" => "Сушилки для белья",
            "Утюги" => "Утюги",
            "Парогенераторы" => "Электронные парогенераторы",
            "Швейно-вышивальные машины" => "Швейные машины"
        );

        $arResult = array();

        $arProps = $this->GetProperties(array("BRAND", "GUARANTEE_DEFAULT", "MODEL_FOR_ONLINERBY", "PRODUCER", "IMPORTER", "SERVICE_CENTERS"));
        $arExtFilter = array(
            "!PROPERTY_PRODUCER" => false,
            "!PROPERTY_IMPORTER" => false,
            "!PROPERTY_SERVICE_CENTERS" => false,
            "!PROPERTY_MODEL_FOR_ONLINERBY" => false
        );

        $this->arElements = $this->GetElements($this->arElements, $arProps, $arExtFilter);

        foreach ($this->arElements as $arItem) {
            $countZero = ($arItem["CATALOG_CURRENCY_" . $this->regionPriceId] == "BYN") ? 2 : 0;
            $price = number_format($arItem["CATALOG_PRICE_" . $this->regionPriceId], $countZero, ".", "");
            $deliveryPrice = ($price >= 50 * 1) ? 0 : 50 * 1;

            $category = $this->GetNameLevelSection(2, $arItem["IBLOCK_SECTION_ID"]);
            $arFormatRow = array(
                "category" => (isset($array_carts[$category])) ? $array_carts[$category] : "Категория не найдена",
                "vendor" => $arItem["PROPERTIES"]["BRAND"]["VALUE"],
                "model" => $arItem["PROPERTIES"]["MODEL_FOR_ONLINERBY"]["VALUE"],
                "price" => $price,
                "currency" => $arItem["CATALOG_CURRENCY_" . $this->regionPriceId],
                "comment" => self::FormatTextDesc($arItem["PREVIEW_TEXT"]),
                "producer" => $arItem["PROPERTIES"]["PRODUCER"]["VALUE"],
                "importer" => $arItem["PROPERTIES"]["IMPORTER"]["VALUE"],
                "serviceCenters" => $arItem["PROPERTIES"]["SERVICE_CENTERS"]["VALUE"],
                "warranty" => intval($this->GetOfferGuarantee($arItem["PROPERTIES"]["GUARANTEE_DEFAULT"]["VALUE"], true)) ? intval($this->GetOfferGuarantee($arItem["PROPERTIES"]["GUARANTEE_DEFAULT"]["VALUE"], true)) * 12 : 0,
                "deliveryTownTime" => 2,
                "deliveryTownPrice" => $deliveryPrice,
                "deliveryCountryTime" => 2,
                "deliveryCountryPrice" => $deliveryPrice,
                "productLifeTime" => 120,
                "isCashless" => "нет",
                "isCredit" => "нет"
            );
            $arResult["item"][] = $arFormatRow;
        }
        return $arResult;
    }

    public function GetDeliveryPriceRB($arElementsXml = array(), $currency = "BYN")
    {
        $defaultFreeDeliveryPrice = 50;
        $defaultDeliveryPrice = self::DEFAULT_PRICE_DELIVERY_RB;

        if ($currency = "BYR") {
            $defaultDeliveryPrice = self::DEFAULT_PRICE_DELIVERY_RB * 1;
            $defaultFreeDeliveryPrice = 50 * 1;
        }

        foreach ($arElementsXml as $n => $arElementXml) {
            $deliveryPrice = ($arElementXml["price"] >= 50) ? 0 : $defaultDeliveryPrice;

            $arElementXml["delivery-options"] = array(
                "option" => array(
                    "@cost" => $deliveryPrice,
                    "@days" => self::DEFAULT_DAYS_DELIVERY_RB
                )
            );

            $arElementsXml[$n] = $arElementXml;
        }

        return $arElementsXml;
    }
}
