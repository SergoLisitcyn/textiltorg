<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Информация. Всё о нашем магазине Вы можете узнать в этом разделе. Конкурсы, условия оплаты, кредит, партнёрам, вопросы, гарантии");
$APPLICATION->SetPageProperty("keywords", "информация о магазине, конкурсы, оплата, кредит, партнёрам, вопрос-ответ, гарантии");
$APPLICATION->SetPageProperty("title", "Информация о нашем магазине | ТекстильТорг");
$APPLICATION->SetTitle("Информация");
?>

<p><b>Здесь Вы можете получить информацию:</b></p>
<p>- <a href="/informacija/konkursy/" style="font-size:12px!important">конкурсы</a>, которые проводит наша компания </p>
<p>- информация для <a href="/informacija/partneram/" style="font-size:12px!important">партнеров</a></p>
<p>- <a href="/informacija/kredit/" style="font-size:12px!important">кредит</a> и условия его предоставления для покупателей</p>
<p>- <a href="/informacija/garantii/" style="font-size:12px!important">условия обмена и возврата</a>, купленного в «ТекстильТорге» товара.</p>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>