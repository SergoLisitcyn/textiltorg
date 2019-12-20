<?
define("BASEPATH", dirname(__FILE__));
require_once BASEPATH."/moysklad.php";

// test
// $rf = new Moysklad(
//     "admin@ttprom",
//     "a4e0814179"
// );

$rf = new Moysklad(
    'robot@textiletorg',
    'x82sVc_5pP'
);

$rf->Sync(
    "s1",
    array(
        "Регион" => array(
            "ORG" => "ООО \"ТекстильТорг Регион\"",
            "STORE" => "Основной склад",
        ),
        "Москва" => array(
            "ORG" => "ООО \"ТекстильТорг МСК\"",
            "STORE" => "Основной склад",
        ),
        "Санкт-Петербург" => array(
            "ORG" => "ООО \"ТекстильТорг СПБ\"",
            "STORE" => "Лиговский СПБ",
        ),
        "Екатеринбург" => array(
            "ORG" => "ООО \"ТекстильТорг ЕКБ\"",
            "STORE" => "Магазин Екатеринбург",
        ),
        "Нижний Новгород" => array(
            "ORG" => "ООО \"ТекстильТорг НН\"",
            "STORE" => "Магазин НН",
        ),
        "Ростов-на-Дону" => array(
            "ORG" => "ООО \"ТекстильТорг РНД\"",
            "STORE" => "Магазин Ростов"
        ),
    )
);

//---

$by = new Moysklad(
    "robot@bymagazine",
    "dJ5y8ff6e6dl"
);

$by->Sync(
    "by",
    array(
        "Регион" => array(
            "ORG" => "ТекстильТорг МНСК",
            "STORE" => "Основной склад",
        ),
        "Минск" => array(
            "ORG" => "ТекстильТорг МНСК",
            "STORE" => "Основной склад",
        )
    )
);