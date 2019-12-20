<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "сайт швейных машин, швейные машинки, продажа швейных машин");
$APPLICATION->SetPageProperty("description", "ТекстильТорг – это магазин с огромным выбором швейной техники и влажно-теплового оборудования. Мы приятно удивим Вас низкими ценами и высоким качеством нашего сервиса.");
$APPLICATION->SetTitle("ТекстильТорг - интернет-магазин и официальный сайт. Продажа швейных машин оптом и в розницу.");
@define('ERROR_404','Y');
CHTTP::SetStatus('404 Not Found');
?>
<?$APPLICATION->IncludeComponent(
    "custom:search404.prototype",
    "",
    array(
        "PATH_LOG" => "/upload/4/"
    ),
    false
);?>
<div class="pnf">
    <div class="pnf-left"><img src="/img/404.jpg" alt="Ошибка 404"></div>
    <div class="pnf-right">
        <div class="pnf-a">УПС...</div>
        <div class="pnf-b">404</div>
        <div class="pnf-c">Похоже, мы не можем найти нужную вам страницу</div>
        <div class="pnf-d"><a href="/">Вернуться на главную</a></div>
    </div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>