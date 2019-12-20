<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Полезное. Здесь вы можете прочитать статьи и тест драйвы по швейной бытовой технике, а также скачать инструкцию по разным моделям швейных машин.");
$APPLICATION->SetPageProperty("keywords", "полезное о текстильторг, инструкция к швейной машинке, бытовая техника инструкции, обзоры швейной техники, швейные машины инструкция");
$APPLICATION->SetPageProperty("title", " Полезное и приятное в наших магазинах | ТекстильТорг");
$APPLICATION->SetTitle("Полезное");
?>
<?
LocalRedirect("/poleznoe/chastye-voprosy/");
?>
<p style="font-size: 16px;">Здесь вы можете прочитать <a href="/poleznoe/stati" style="font-size: 16px;">статьи</a> и <a href="/poleznoe/obzory" style="font-size: 16px;">тест драйвы</a> по швейной бытовой техники, а также скачать <a href="/poleznoe/instrukcii/" style="font-size: 16px;">инструкцию</a> по разным моделям швейных машин.</p>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>